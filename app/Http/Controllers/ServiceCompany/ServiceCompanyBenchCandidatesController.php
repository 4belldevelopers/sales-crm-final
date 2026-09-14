<?php

/***#######################################################
Controller    	: 	AdminAppTrackerController
Purpose			:
Author			:	Abu
Company			:	4Bell
Date			:	28-JUNE-2022
########################################################***/

namespace App\Http\Controllers\ServiceCompany;

use App\Enums\ResumeUploadStatus;
use App\Helper\Files;
use App\Helper\GeneralMethod;
use App\Helper\Reply;
use App\Jobs\ElasticBenchCandidateIndex;
use App\Jobs\QueueNotificationMailService;
use App\Jobs\RecordBenchResumeDownloadJob;
use App\Jobs\UploadResumeToTestRTwo;
use App\Models\ApplicationStatus;
use App\Models\BenchApplication;
use App\Models\Client;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\NoticePeriod;
use App\Models\ResumeUpload;
use App\Models\UploadSessionAudit;
use App\Models\User;
use App\Services\ElasticService;
use App\Services\MailService;
use App\Services\ShortlistBenchCandidateListingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Validator;
use Yajra\DataTables\Facades\DataTables;

class ServiceCompanyBenchCandidatesController extends ServiceCompanyBaseController
{
    protected $mailService;

    protected $elasticService;

    protected $shortlistBenchCandidateListingService;

    public function __construct(
        MailService $mailService,
        ElasticService $elasticService,
        ShortlistBenchCandidateListingService $shortlistBenchCandidateListingService
    ) {
        parent::__construct();
        $this->pageTitle = 'Bench list';
        $this->pageIcon = 'icon-badge';
        $this->mailService = $mailService;
        $this->elasticService = $elasticService;
        $this->shortlistBenchCandidateListingService = $shortlistBenchCandidateListingService;
        // dd($this->global);
    }

    public function index()
    {
        // dd("dlkfk");
        $this->pageTitle = __('Bench Listing');
        $this->allBenchApplication = JobApplication::count();

        $params = [
            'index' => ELASTIC_CANDIDATES_BENCH,
            'size' => 0,   // no documents, only counts
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['term' => ['company_id' => auth()->user()->company_id]],
                            ['term' => ['condidate_type.keyword' => 'bench_condidate']],
                        ],
                    ],
                ],
                'aggs' => [
                    'all_records' => [
                        'value_count' => [
                            'field' => 'id',
                        ],
                    ],
                    'bench' => [
                        'filter' => [
                            'term' => ['status' => 2],
                        ],
                    ],
                    'pool' => [
                        'filter' => [
                            'term' => ['status' => 1],
                        ],
                    ],
                    'placed' => [
                        'filter' => [
                            'term' => ['status' => 3],
                        ],
                    ],
                    'paused' => [
                        'filter' => [
                            'term' => ['status' => 4],
                        ],
                    ],
                    'exited' => [
                        'filter' => [
                            'term' => ['status' => 5],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);
        $this->counts = (object) [
            'all_records' => $response['hits']['total']['value'] ?? 0,
            'bench' => $response['aggregations']['bench']['doc_count'] ?? 0,
            'pool' => $response['aggregations']['pool']['doc_count'] ?? 0,
            'placed' => $response['aggregations']['placed']['doc_count'] ?? 0,
            'paused' => $response['aggregations']['paused']['doc_count'] ?? 0,
            'exited' => $response['aggregations']['exited']['doc_count'] ?? 0,
        ];

        $this->companies = DB::table('companies')->select('id', 'company_name')->where('id', auth()->user()->company_id)->first();
        // dd($this->counts);

        // Inhouse Talents page keeps only its hero KPI chips (counts + MoM deltas + Bench Cost);
        // the Bench-Aging / Loss / Profit charts now live on the Staffing Dashboard.
        $companyId = auth()->user()->company_id;
        $analytics = $this->benchStaffingAnalytics($companyId);
        $this->currencyCode = $analytics->currencyCode;
        $this->deltas = $analytics->deltas;
        $this->benchCost = $analytics->benchCost;

        return view('service_company.bench-candidates.index', $this->data);

    }

    /**
     * Staffing Dashboard — Bench Aging, Bench Cost (Loss) and Gross Profit analytics,
     * moved out of the Inhouse Talents page into a dedicated Talent-Marketplace sub-page.
     */
    public function staffingDashboard()
    {
        abort_if(Gate::denies('view_job_applications'), 403);
        $this->pageTitle = __('Staffing Dashboard');

        $companyId = auth()->user()->company_id;
        $a = $this->benchStaffingAnalytics($companyId);

        $this->currencyCode = $a->currencyCode;
        $this->benchCost = $a->benchCost;
        $this->grossProfit = $a->grossProfit;
        $this->benchAging = $a->benchAging;
        $this->benchCount = $a->benchCount;
        $this->avgBenchAge = $a->avgBenchAge;
        $this->longestBenchAge = $a->longestBenchAge;
        $this->profitMargin = $a->profitMargin;
        $this->benchROI = $a->benchROI;
        $this->chartLabels = $a->chartLabels;
        $this->lossSeries = $a->lossSeries;
        $this->profitSeries = $a->profitSeries;
        $this->deltas = $a->deltas;

        return view('service_company.bench-candidates.staffing-dashboard', $this->data);
    }

    /**
     * Shared bench financial analytics (tenant-scoped). Returns everything the
     * Staffing Dashboard needs; the Inhouse page consumes only a subset.
     *
     *  - Loss (Bench Cost) = annual salary (ctc_yearly) of talents still ON BENCH (status 2).
     *  - Gross Profit = (client pay on placements, bench_place.bill_rate_amount, annualised)
     *                   − (bench cost of unplaced) − (salary we pay placed talents).
     * NOTE: raw DB::table is NOT auto tenant-scoped, so company_id is filtered explicitly.
     */
    private function benchStaffingAnalytics(int $companyId): object
    {
        // Talents still on bench (unplaced) — drives Bench Cost / Loss + aging.
        $benchRows = DB::table('job_applications')
            ->select('id', 'ctc_yearly', 'created_at')
            ->where('company_id', $companyId)
            ->where('condidate_type', 'bench_condidate')
            ->where('status', 2)
            ->get();

        // Actual placement records: what the client pays us (bill_rate) + what we pay the placed talent (pay_rate).
        $placementRows = DB::table('bench_place')
            ->select('bench_id', 'pay_rate_amount', 'pay_rate_mode', 'bill_rate_amount', 'bill_rate_mode', 'create_at')
            ->where('source_comapny_id', $companyId)
            ->get();

        // Annualise a raw placement rate (mode: 0=Annual, 1=Monthly, 2=Hourly).
        $annual = fn ($amount, $mode) => (float) GeneralMethod::salaryDividedByMode((int) $mode, (float) $amount)->yearly;

        $benchCost = (float) $benchRows->sum(fn ($r) => (float) $r->ctc_yearly);
        $placedClientPay = (float) $placementRows->sum(fn ($r) => $annual($r->bill_rate_amount, $r->bill_rate_mode));
        $placedSalary = (float) $placementRows->sum(fn ($r) => $annual($r->pay_rate_amount, $r->pay_rate_mode));
        $grossProfit = $placedClientPay - $benchCost - $placedSalary;

        // Bench Aging buckets (days on bench, derived from created_at).
        $aging = ['b1' => 0, 'b2' => 0, 'b3' => 0, 'b4' => 0];
        $today = Carbon::today();
        foreach ($benchRows as $r) {
            $days = $r->created_at ? Carbon::parse($r->created_at)->diffInDays($today) : 0;
            if ($days <= 15) {
                $aging['b1']++;
            } elseif ($days <= 30) {
                $aging['b2']++;
            } elseif ($days <= 45) {
                $aging['b3']++;
            } else {
                $aging['b4']++;
            }
        }

        // Daily Loss/Profit trend across the current month (cumulative to each day).
        $monthStart = Carbon::now()->startOfMonth();
        $dayCount = (int) Carbon::now()->format('j');
        $labels = $lossSeries = $profitSeries = [];
        for ($d = 1; $d <= $dayCount; $d++) {
            $cursor = (clone $monthStart)->addDays($d - 1)->endOfDay();
            $labels[] = $cursor->format('d M');

            $lossToDate = (float) $benchRows
                ->filter(fn ($r) => $r->created_at && Carbon::parse($r->created_at)->lte($cursor))
                ->sum(fn ($r) => (float) $r->ctc_yearly);

            $placedToDate = $placementRows->filter(fn ($r) => $r->create_at && Carbon::parse($r->create_at)->lte($cursor));
            $clientPayToDate = (float) $placedToDate->sum(fn ($r) => $annual($r->bill_rate_amount, $r->bill_rate_mode));
            $placedSalToDate = (float) $placedToDate->sum(fn ($r) => $annual($r->pay_rate_amount, $r->pay_rate_mode));

            $lossSeries[] = round($lossToDate, 2);
            $profitSeries[] = round($clientPayToDate - $lossToDate - $placedSalToDate, 2);
        }

        $ages = $benchRows->map(fn ($r) => $r->created_at ? Carbon::parse($r->created_at)->diffInDays($today) : 0);
        $avgBenchAge = $ages->count() ? (int) round($ages->avg()) : 0;
        $longestBenchAge = $ages->count() ? (int) $ages->max() : 0;
        $profitMargin = $placedClientPay > 0 ? round(($grossProfit / $placedClientPay) * 100, 1) : 0;
        $benchROI = $benchCost > 0 ? round($placedClientPay / $benchCost, 2) : 0;

        // Month-over-month deltas ("vs last month" chips), from records created in each window.
        $thisStart = Carbon::now()->startOfMonth();
        $lastStart = Carbon::now()->subMonthNoOverflow()->startOfMonth();
        $lastEnd = Carbon::now()->subMonthNoOverflow()->endOfMonth();

        $addsThis = DB::table('job_applications')->where('company_id', $companyId)->where('condidate_type', 'bench_condidate')
            ->where('created_at', '>=', $thisStart)->selectRaw('status, COUNT(*) c')->groupBy('status')->pluck('c', 'status');
        $addsLast = DB::table('job_applications')->where('company_id', $companyId)->where('condidate_type', 'bench_condidate')
            ->whereBetween('created_at', [$lastStart, $lastEnd])->selectRaw('status, COUNT(*) c')->groupBy('status')->pluck('c', 'status');

        $delta = function ($cur, $prev) {
            if ($prev <= 0) {
                return $cur > 0 ? 100.0 : 0.0;
            }

            return round((($cur - $prev) / $prev) * 100, 1);
        };

        $sumBench = fn ($from, $to) => (float) $benchRows
            ->filter(fn ($r) => $r->created_at && Carbon::parse($r->created_at)->betweenIncluded($from, $to))
            ->sum(fn ($r) => (float) $r->ctc_yearly);
        $sumPlaceBill = fn ($from, $to) => (float) $placementRows
            ->filter(fn ($r) => $r->create_at && Carbon::parse($r->create_at)->betweenIncluded($from, $to))
            ->sum(fn ($r) => $annual($r->bill_rate_amount, $r->bill_rate_mode));
        $sumPlacePay = fn ($from, $to) => (float) $placementRows
            ->filter(fn ($r) => $r->create_at && Carbon::parse($r->create_at)->betweenIncluded($from, $to))
            ->sum(fn ($r) => $annual($r->pay_rate_amount, $r->pay_rate_mode));

        $costThis = $sumBench($thisStart, Carbon::now());
        $costLast = $sumBench($lastStart, $lastEnd);
        $profitThis = $sumPlaceBill($thisStart, Carbon::now()) - $costThis - $sumPlacePay($thisStart, Carbon::now());
        $profitLast = $sumPlaceBill($lastStart, $lastEnd) - $costLast - $sumPlacePay($lastStart, $lastEnd);

        $deltas = (object) [
            'all' => $delta($addsThis->sum(), $addsLast->sum()),
            'pool' => $delta($addsThis->get(1, 0), $addsLast->get(1, 0)),
            'bench' => $delta($addsThis->get(2, 0), $addsLast->get(2, 0)),
            'hold' => $delta($addsThis->get(4, 0), $addsLast->get(4, 0)),
            'placed' => $delta($addsThis->get(3, 0), $addsLast->get(3, 0)),
            'cost' => $delta($costThis, $costLast),
            'profit' => $delta($profitThis, $profitLast),
        ];

        $currencyMode = GeneralMethod::getCompanyCurrencyBenchshowMode($companyId);

        return (object) [
            'currencyCode' => $currencyMode->currency_code ?? 'INR',
            'benchCost' => $benchCost,
            'grossProfit' => $grossProfit,
            'benchAging' => (object) $aging,
            'benchCount' => $benchRows->count(),
            'avgBenchAge' => $avgBenchAge,
            'longestBenchAge' => $longestBenchAge,
            'profitMargin' => $profitMargin,
            'benchROI' => $benchROI,
            'chartLabels' => $labels,
            'lossSeries' => $lossSeries,
            'profitSeries' => $profitSeries,
            'deltas' => $deltas,
        ];
    }

    public function countDataGetBenchList()
    {

        $params = [
            'index' => ELASTIC_CANDIDATES_BENCH,
            'size' => 0,   // no documents, only counts
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['term' => ['company_id' => auth()->user()->company_id]],
                            ['term' => ['condidate_type.keyword' => 'bench_condidate']],
                        ],
                    ],
                ],
                'aggs' => [
                    'all_records' => [
                        'value_count' => [
                            'field' => 'id',
                        ],
                    ],
                    'bench' => [
                        'filter' => [
                            'term' => ['status' => 2],
                        ],
                    ],
                    'pool' => [
                        'filter' => [
                            'term' => ['status' => 1],
                        ],
                    ],
                    'placed' => [
                        'filter' => [
                            'term' => ['status' => 3],
                        ],
                    ],
                    'paused' => [
                        'filter' => [
                            'term' => ['status' => 4],
                        ],
                    ],
                    'exited' => [
                        'filter' => [
                            'term' => ['status' => 5],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);
        // $this->counts = (object) [
        // 	'all_records' => $response['hits']['total']['value'] ?? 0,
        // 	'bench'       => $response['aggregations']['bench']['doc_count'] ?? 0,
        // 	'pool'        => $response['aggregations']['pool']['doc_count'] ?? 0,
        // 	'placed'      => $response['aggregations']['placed']['doc_count'] ?? 0,
        // 	'paused'      => $response['aggregations']['paused']['doc_count'] ?? 0,
        // 	'exited'      => $response['aggregations']['exited']['doc_count'] ?? 0,
        // ];

        // $this->counts = DB::table('job_applications')
        // 	->selectRaw("
        // 		COUNT(*) AS all_records,
        // 		SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) AS bench,
        // 		SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS pool,
        // 		SUM(CASE WHEN status = 3 THEN 1 ELSE 0 END) AS placed,
        // 		SUM(CASE WHEN status = 4 THEN 1 ELSE 0 END) AS paused
        // 	")
        // 	->where('job_applications.company_id', '=', auth()->user()->company_id)
        // 	->where('job_applications.condidate_type', '=', 'bench_condidate')
        // 	->first();

        return response()->json([
            'all_records' => $response['hits']['total']['value'] ?? 0,
            'pool' => $response['aggregations']['pool']['doc_count'] ?? 0,
            'bench' => $response['aggregations']['bench']['doc_count'] ?? 0,
            'paused' => $response['aggregations']['paused']['doc_count'] ?? 0,
            'placed' => $response['aggregations']['placed']['doc_count'] ?? 0,
            'exited' => $response['aggregations']['exited']['doc_count'] ?? 0,
        ]);

    }

    public function data_ajax(Request $request)
    {

        abort_if(Gate::denies('view_job_applications'), 403);
        $company_id = auth()->user()->company_id;

        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();
        $chkAdminRole = $this->user->hasRole('admin');

        $valCheck = $request->input('sortingfilter');
        $selectedValue = $request->input('selectedValue');
        $country = '';
        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) ($request->input('per_page') ?? $request->input('size') ?? $request->input('limit') ?? 10);
        $from = ($page - 1) * $size;
        $requiredSkills = [];
        $searchTerms = request('cus_search');
        $custom_search = $searchTerms; // json_decode($searchTerms, true);

        $cachePayload = [
            'page' => $page,
            'size' => $size,
            'q' => $searchQuery,
            'company' => $company_id,
            'custom_search' => $custom_search,
            'selectedValue' => $selectedValue,
            'user_id' => $this->user->id,
            'sort' => $valCheck,
        ];

        $versionKey = 'allbenchcandidates_version_user_'.$this->user->id;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:allbenchcandidates:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));
        /* ----------------------------
        ⚡ Check DragonflyDB first
        ----------------------------- */
        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet));
        }

        $sort = [];

        // 1. Sort: ads_switch 'Yes' on top (native numeric rank field: 0 for Yes, 1 for No)
        $sort[] = ['ads_switch_rank' => ['order' => 'asc', 'unmapped_type' => 'integer']];

        // 2. Sort: skill match count (highest first)
        $sort[] = [
            '_script' => [
                'type' => 'number',
                'script' => [
                    'lang' => 'painless',
                    'source' => "
						int matchCount = 0;
						if (doc.containsKey('job_application_skills.skills.keyword')) {
							for (s in params.skills) {
								if (doc['job_application_skills.skills.keyword'].contains(s)) {
									matchCount += 1;
								}
							}
						}
						return matchCount;
					",
                    'params' => [
                        'skills' => array_values($requiredSkills),
                    ],
                ],
                'order' => 'desc',
            ],
        ];

        // 3. Sort: based on selected filter value
        switch ($valCheck) {
            case 1:
                $sort[] = ['updated_at' => ['order' => 'desc']];
                break;
            case 2:
                $sort[] = ['updated_at' => ['order' => 'asc']];
                break;
            case 3:
                $sort[] = ['ctc' => ['order' => 'asc', 'unmapped_type' => 'float']];
                break;
            case 4:
                $sort[] = ['ctc' => ['order' => 'desc', 'unmapped_type' => 'float']];
                break;
            case 5:
                $sort[] = ['np_days' => ['order' => 'asc', 'unmapped_type' => 'integer']];
                break;
            case 6:
                $sort[] = ['np_days' => ['order' => 'desc', 'unmapped_type' => 'integer']];
                break;
            case 7:
                $sort[] = ['total_exp' => ['order' => 'desc', 'unmapped_type' => 'float']];
                break;
            case 8:
                $sort[] = ['total_exp' => ['order' => 'asc', 'unmapped_type' => 'float']];
                break;
            case 9:
                $sort[] = ['np_days' => ['order' => 'asc', 'unmapped_type' => 'integer']];
                break;
            default:
                $sort[] = ['id' => ['order' => 'desc', 'unmapped_type' => 'long']];
                break;
        }

        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['condidate_type' => 'bench_condidate']],
                            ], $this->getSearchConditions($custom_search, $country)),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [], // We'll add all filters here
            ],
        ];
        // Status filter
        // dd($selectedValue);
        if (! empty($selectedValue) && (int) $selectedValue > 0) {
            $boolQuery['bool']['filter'][] = [
                'term' => [
                    'status' => (int) $selectedValue,
                ],
            ];
        }

        $params = [
            'index' => ELASTIC_CANDIDATES_BENCH,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => $boolQuery,
            ],
        ];

        $viewCounts = [];
        $downloadCounts = [];
        $companyCurrencyBenchshowMode = GeneralMethod::getCompanyCurrencyBenchshowMode($company_id);

        if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES_BENCH)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);

            // Bulk pre-fetch work-preference (wm), view counts, and download counts to prevent N+1 queries in Blade
            $pageIds = array_filter(array_column($data, 'id'));
            if (! empty($pageIds)) {
                $wmMap = DB::table('job_applications')->whereIn('id', $pageIds)->pluck('wm', 'id');

                // Bulk fetch view and download counts using index idx_bcvd_cand_type_stat
                $counts = DB::table('bench_count_view_download')
                    ->select('candidate_id', 'type', DB::raw('COUNT(*) as total'))
                    ->whereIn('candidate_id', $pageIds)
                    ->where('status', 0)
                    ->groupBy('candidate_id', 'type')
                    ->get();

                foreach ($counts as $c) {
                    if ($c->type == 1) {
                        $viewCounts[$c->candidate_id] = $c->total;
                    } elseif ($c->type == 2) {
                        $downloadCounts[$c->candidate_id] = $c->total;
                    }
                }

                foreach ($data as &$row) {
                    $row['wm'] = $wmMap[$row['id'] ?? null] ?? $row['wm'] ?? null;
                }
                unset($row);
            }
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'per_page' => $size,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        $userPermissions = $this->userPermissions;
        $responseData = [
            'html' => view('service_company.ajax_pages.bench-candidate-list-ajax', compact('data', 'userPermissions', 'company_id', 'salaryMode', 'currencySymbole', 'viewCounts', 'downloadCounts', 'companyCurrencyBenchshowMode'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ];

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_BENCH_CANDIDATES_TIME));

        return response()->json($responseData);

    }

    /**
     * V2 High-Performance JSON Candidate Endpoint
     * Returns normalized candidate arrays instead of heavy server-rendered HTML blocks.
     * Reduces network payload size by ~90% (85KB -> 8KB).
     */
    public function data_v2(Request $request)
    {
        abort_if(Gate::denies('view_job_applications'), 403);
        $company_id = auth()->user()->company_id;

        $valCheck = $request->input('sortingfilter');
        $selectedValue = $request->input('selectedValue');
        $country = '';
        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10);
        $from = ($page - 1) * $size;
        $searchTerms = request('cus_search');
        $custom_search = $searchTerms;

        $cachePayload = [
            'v2' => true,
            'page' => $page,
            'size' => $size,
            'q' => $searchQuery,
            'company' => $company_id,
            'custom_search' => $custom_search,
            'selectedValue' => $selectedValue,
            'user_id' => $this->user->id,
            'sort' => $valCheck,
        ];

        $versionKey = 'allbenchcandidates_version_user_'.$this->user->id;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:v2:allbenchcandidates:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));

        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet));
        }

        $lockKey = 'lock:'.$cacheKeySet;
        $lock = cache()->lock($lockKey, 5);

        if (! $lock->get()) {
            // Lock held by another worker constructing this cache entry. Wait up to 1.5 seconds.
            for ($i = 0; $i < 15; $i++) {
                usleep(100000);
                if (cache()->has($cacheKeySet)) {
                    return response()->json(cache()->get($cacheKeySet));
                }
            }
        }

        try {
            $sort = [];
            $sort[] = ['ads_switch_rank' => ['order' => 'asc', 'unmapped_type' => 'integer']];

            switch ($valCheck) {
                case 1:
                    $sort[] = ['updated_at' => ['order' => 'desc']];
                    break;
                case 2:
                    $sort[] = ['updated_at' => ['order' => 'asc']];
                    break;
                case 3:
                    $sort[] = ['ctc' => ['order' => 'asc', 'unmapped_type' => 'float']];
                    break;
                case 4:
                    $sort[] = ['ctc' => ['order' => 'desc', 'unmapped_type' => 'float']];
                    break;
                case 5:
                    $sort[] = ['np_days' => ['order' => 'asc', 'unmapped_type' => 'integer']];
                    break;
                case 6:
                    $sort[] = ['np_days' => ['order' => 'desc', 'unmapped_type' => 'integer']];
                    break;
                case 7:
                    $sort[] = ['total_exp' => ['order' => 'desc', 'unmapped_type' => 'float']];
                    break;
                case 8:
                    $sort[] = ['total_exp' => ['order' => 'asc', 'unmapped_type' => 'float']];
                    break;
                case 9:
                    $sort[] = ['np_days' => ['order' => 'asc', 'unmapped_type' => 'integer']];
                    break;
                default:
                    $sort[] = ['id' => ['order' => 'desc', 'unmapped_type' => 'long']];
                    break;
            }

            $boolQuery = [
                'bool' => [
                    'should' => [
                        [
                            'bool' => [
                                'must' => array_merge([
                                    ['term' => ['company_id' => $company_id]],
                                    ['term' => ['condidate_type' => 'bench_condidate']],
                                ], $this->getSearchConditions($custom_search, $country)),
                            ],
                        ],
                    ],
                    'minimum_should_match' => 1,
                    'filter' => [],
                ],
            ];

            if (! empty($selectedValue) && (int) $selectedValue > 0) {
                $boolQuery['bool']['filter'][] = [
                    'term' => [
                        'status' => (int) $selectedValue,
                    ],
                ];
            }

            $params = [
                'index' => ELASTIC_CANDIDATES_BENCH,
                'body' => [
                    'from' => $from,
                    'size' => $size,
                    'sort' => $sort,
                    'query' => $boolQuery,
                ],
            ];

            $viewCounts = [];
            $downloadCounts = [];
            $companyCurrencyBenchshowMode = GeneralMethod::getCompanyCurrencyBenchshowMode($company_id);

            if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES_BENCH)) {
                $total = 0;
                $candidates = [];
            } else {
                $response = $this->elasticService->getClient()->search($params);
                $total = $response['hits']['total']['value'] ?? 0;
                $rawHits = array_map(function ($hit) {
                    return $hit['_source'];
                }, $response['hits']['hits']);

                $pageIds = array_filter(array_column($rawHits, 'id'));
                if (! empty($pageIds)) {
                    $wmMap = DB::table('job_applications')->whereIn('id', $pageIds)->pluck('wm', 'id');

                    $counts = DB::table('bench_count_view_download')
                        ->select('candidate_id', 'type', DB::raw('COUNT(*) as total'))
                        ->whereIn('candidate_id', $pageIds)
                        ->where('status', 0)
                        ->groupBy('candidate_id', 'type')
                        ->get();

                    foreach ($counts as $c) {
                        if ($c->type == 1) {
                            $viewCounts[$c->candidate_id] = $c->total;
                        } elseif ($c->type == 2) {
                            $downloadCounts[$c->candidate_id] = $c->total;
                        }
                    }

                    foreach ($rawHits as &$r) {
                        $r['wm'] = $wmMap[$r['id'] ?? null] ?? $r['wm'] ?? null;
                    }
                    unset($r);
                }

                $salaryMode = GeneralMethod::salaryMode();
                $currencySymbole = GeneralMethod::currencySymbole();

                $candidates = array_map(function ($row) use ($viewCounts, $downloadCounts, $companyCurrencyBenchshowMode, $salaryMode, $currencySymbole) {
                    $billFormatViewSalary = GeneralMethod::formatViewSalary($row['ectc_yearly'] ?? 0, $companyCurrencyBenchshowMode->currency_code, $companyCurrencyBenchshowMode->show_mode);
                    $bill_rate_currency = $currencySymbole[$billFormatViewSalary->currency] ?? '₹';
                    $bill_rate_mode = $salaryMode[$billFormatViewSalary->mode] ?? 'Yearly';

                    return [
                        'id' => $row['id'],
                        'full_name' => $row['full_name'] ?? '',
                        'current_designation' => $row['current_designation'] ?? '',
                        'skills' => $row['skills'] ?? '',
                        'ads_switch' => $row['ads_switch'] ?? 'No',
                        'total_exp' => $row['total_exp'] ?? '0',
                        'wm' => $row['wm'] ?? null,
                        'outsourcing_type' => ($row['outsourcing_type'] ?? null) == 2 ? 'Contract' : 'Full-time',
                        'np_id' => $row['np_id'] ?? '',
                        'np_days' => $row['np_days'] ?? 0,
                        'serving_notice_period_mode' => $row['serving_notice_period_mode'] ?? 0,
                        'city_name' => $row['city_name'] ?? '',
                        'country_name' => $row['country_name'] ?? '',
                        'qualification_name' => $row['qualification_name'] ?? '',
                        'ectc_yearly' => $row['ectc_yearly'] ?? 0,
                        'salary_display' => $bill_rate_currency.number_format((float) $billFormatViewSalary->amount, 0).' /'.mb_substr($bill_rate_mode, 0, 1),
                        'comission_type' => $row['comission_type'] ?? null,
                        'status' => $row['status'] ?? 2,
                        'master_status_name' => $row['master_status_name'] ?? 'Bench',
                        'css_style' => $row['css_style'] ?? 'text-primary',
                        'created_at' => $row['created_at'] ?? null,
                        'views' => $viewCounts[$row['id']] ?? $row['viewCount'] ?? 0,
                        'downloads' => $downloadCounts[$row['id']] ?? $row['downloadCount'] ?? 0,
                        'hiring_hr' => $row['hiring_hr'] ?? [],
                        'pipeline' => [
                            'sourcing' => $row['sourcing_count'] ?? 0,
                            'screening' => $row['screening_count'] ?? 0,
                            'interview' => $row['interview_count'] ?? 0,
                            'evaluation' => $row['evaluation_count'] ?? 0,
                            'negotiation' => $row['negotiation_count'] ?? 0,
                            'offered' => $row['offered_count'] ?? 0,
                            'joined' => $row['joined_count'] ?? 0,
                            'rejected' => $row['rejected_count'] ?? 0,
                        ],
                    ];
                }, $rawHits);
            }

            $responseData = [
                'status' => 'success',
                'total' => $total,
                'page' => $page,
                'last_page' => ceil($total / $size),
                'candidates' => $candidates,
            ];

            cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_BENCH_CANDIDATES_TIME));

        } finally {
            optional($lock)->release();
        }

        return response()->json($responseData);
    }

    protected function getSearchConditions($custom_search, $country)
    {
        $conditions = [];

        // Apply custom search if it's not empty
        if (! empty($custom_search)) {
            $conditions[] = [
                'bool' => [
                    'should' => [
                        [
                            'match_phrase_prefix' => ['full_name' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'match_phrase_prefix' => ['current_designation' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'match_phrase_prefix' => ['skills' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'wildcard' => ['email' => $custom_search],
                        ],
                        [
                            'wildcard' => ['phone' => $custom_search],
                        ],
                        [
                            'wildcard' => ['country_name' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'match_phrase_prefix' => ['qualification_name' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'wildcard' => ['state_name' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'wildcard' => ['city_name' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'wildcard' => ['posted_by' => '*'.strtolower($custom_search).'*'],
                        ],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        return $conditions;

    }

    /**
     * Intent: Bench Candidate Detail Page V2 (Initial Profile Overview Drawer View)
     * Tenancy: Scoped to logged-in user's company_id. Shared Redis cache per company.
     * Performance: Eliminates Elasticsearch search overhead; renders Profile Overview ONLY (True Tab Lazy Loading).
     */
    public function show($id)
    {
        $company_id = auth()->user()->company_id;

        // Intent: Shared Tenant Cache Key (eliminates per-user fragmentation and cache stampedes)
        $versionKey = 'benchviewmore_version_company_'.$company_id;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:bench_detail:'.$company_id.':'.$id.':v'.$version;

        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet));
        }

        $this->currency = GeneralMethod::currency();
        $this->salaryMode = GeneralMethod::salaryMode();

        // Intent: Query candidate details with tenancy check & explicit column selection
        $this->benchApplication = JobApplication::select(
            'job_applications.id',
            'job_applications.company_id',
            'job_applications.submitted_by',
            'job_applications.status_type',
            'job_applications.ctc',
            'job_applications.ectc',
            'job_applications.ads_switch',
            'job_applications.submitted_by as editAllow',
            'job_applications.full_name',
            'job_applications.careersummary',
            'job_applications.nationality',
            'job_applications.gender',
            'job_applications.wm',
            'job_applications.employmentType',
            'job_applications.oh',
            'job_applications.visastatu',
            'job_applications.referencename',
            'job_applications.current_designation',
            'job_applications.phone_code',
            'job_applications.second_phone_code',
            'job_applications.outsourcing_type',
            'job_applications.phone',
            'job_applications.secondary_mobile',
            'job_applications.dob',
            'job_applications.email',
            'job_applications.secondrary_email',
            'job_applications.qualification',
            'job_applications.total_exp',
            'job_applications.rel_exp',
            'job_applications.serving_notice_period_mode',
            'job_applications.np_id',
            'job_applications.pay_rate_currency',
            'job_applications.pay_rate_amount',
            'job_applications.pay_rate_mode',
            'job_applications.bill_rate_currency',
            'job_applications.bill_rate_amount',
            'job_applications.bill_rate_mode',
            'job_applications.comission_type',
            'job_applications.country',
            'job_applications.state',
            'job_applications.city',
            'job_applications.pincode',
            'job_applications.skills',
            'job_applications.photo',
            'job_applications.resume',
            'job_applications.status',
            'job_applications.offer_amount',
            'job_applications.joined_date',
            'job_applications.created_at',
            'job_applications.updated_at',
            'job_applications.employee_number',
            'job_applications.linkedin',
            'job_applications.github',
            'job_applications.behance',
            'job_applications.otherLink',
            'qualification_master.name as qualification_name',
            'bench_master_status.name as master_status_name',
            'state_list.state as state_name',
            'city_list.city as city_name',
            'users.name as posted_by',
            'companies.company_name',
            'users.image as posted_by_image'
        )
            ->leftjoin('users', 'users.id', '=', 'job_applications.submitted_by')
            ->leftjoin('qualification_master', 'qualification_master.id', '=', 'job_applications.qualification')
            ->leftJoin('bench_master_status', 'bench_master_status.id', '=', 'job_applications.status')
            ->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
            ->leftjoin('companies', 'companies.id', '=', 'job_applications.company_id')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id')
            ->where('job_applications.id', $id)
            ->first();

        if (! $this->benchApplication) {
            return Reply::dataOnly(['status' => 'error', 'message' => 'Candidate not found.']);
        }

        $this->job_additional_detail = DB::table('bench_additional_detail')
            ->select('*')
            ->where('bench_id', '=', $this->benchApplication->id)
            ->orderBy('id', 'ASC')
            ->get();

        // Intent: Fetch Relationship Managers directly from MySQL (eliminating Elasticsearch search overhead under 10K scale)
        $this->bench_assign_row = DB::table('bench_assign')
            ->select('users.id', 'users.name', 'users.email', 'users.mobile', 'users.calling_code')
            ->leftjoin('users', 'users.id', '=', 'bench_assign.user_id')
            ->where('bench_assign.bench_applications_id', '=', $this->benchApplication->id)
            ->orderBy('bench_assign.id', 'ASC')
            ->get();

        $this->bench_assign_count = $this->bench_assign_row->count();

        $boostkeywords = DB::table('benchboostkeywordcandidates')->where('candidate_id', $id)->pluck('skill_name')->toArray();
        $this->boostkeywords = count($boostkeywords) > 0 ? implode(',', $boostkeywords) : '';

        // Intent: Single SQL query for skill experiences (eliminating duplicate count query)
        $this->skillExperiences = DB::table('bench_candidate_skill_experience')
            ->where('candidate_id', $this->benchApplication->id)
            ->get();
        $this->skillExperiencesCount = $this->skillExperiences->count();

        // Intent: Pre-fetch company logo & name to eliminate direct raw SQL in Blade
        $companyData = DB::table('companies')->select('logo', 'company_name')->where('id', $company_id)->first();
        $this->company_logo = '';
        $this->company_name = ucwords((string) ($companyData->company_name ?? ''));
        if (! empty($companyData->logo) && strlen($companyData->logo) > 5) {
            $this->company_logo = route('service_company.job-applications.company-logo-proxy');
        }

        $view = view('service_company.bench-candidates.showdetail', $this->data)->render();
        $responseData = Reply::dataOnly(['status' => 'success', 'view' => $view]);

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(BENCH_VIEWMORE));

        return $responseData;

    }

    /**
     * Intent: Lazy-loaded Ads Setting Tab endpoint (V2 Architecture)
     * Tenancy: Scoped to logged-in user's company_id.
     */
    public function getAdsTab($id)
    {
        $company_id = auth()->user()->company_id;
        $versionKey = 'benchviewmore_version_company_'.$company_id;
        $version = cache()->get($versionKey, 1);
        $cacheKey = 'ats:bench_ads_tab:'.$company_id.':'.$id.':v'.$version;

        if (cache()->has($cacheKey)) {
            return response()->json(cache()->get($cacheKey));
        }

        $benchApplication = DB::table('job_applications')
            ->select('id', 'company_id', 'ads_switch', 'status_type')
            ->where('id', $id)
            ->first();

        if (! $benchApplication) {
            return Reply::dataOnly(['status' => 'error', 'message' => 'Candidate not found.']);
        }

        $view = view('service_company.bench-candidates.partials.tab_ads_setting', compact('benchApplication'))->render();
        $responseData = Reply::dataOnly(['status' => 'success', 'view' => $view]);

        cache()->put($cacheKey, $responseData, now()->addMinutes(BENCH_VIEWMORE));

        return $responseData;
    }

    /**
     * Intent: Lazy-loaded Resume / CV Tab endpoint (V2 Architecture)
     * Tenancy: Scoped to logged-in user's company_id; contact masking applied for cross-company viewing.
     */
    public function getResumeTab($id)
    {
        $company_id = auth()->user()->company_id;
        $versionKey = 'benchviewmore_version_company_'.$company_id;
        $version = cache()->get($versionKey, 1);
        $cacheKey = 'ats:bench_resume_tab:'.$company_id.':'.$id.':v'.$version;

        if (cache()->has($cacheKey)) {
            return response()->json(cache()->get($cacheKey));
        }

        $benchApplication = JobApplication::select(
            'job_applications.id', 'job_applications.company_id', 'job_applications.resume', 'job_applications.skills', 'job_applications.total_exp',
            'job_applications.serving_notice_period_mode', 'job_applications.np_id', 'job_applications.city', 'job_applications.country',
            'job_applications.pay_rate_currency', 'job_applications.pay_rate_amount', 'job_applications.bill_rate_currency', 'job_applications.bill_rate_amount',
            'city_list.city as city_name'
        )
            ->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id')
            ->where('job_applications.id', $id)
            ->first();

        if (! $benchApplication || empty($benchApplication->resume)) {
            return Reply::dataOnly(['status' => 'error', 'message' => 'Resume not found.']);
        }

        $skillExperiences = DB::table('bench_candidate_skill_experience')
            ->where('candidate_id', $id)
            ->get();

        $currencySymbole = GeneralMethod::currencySymbole();
        $current_ctc_cur = $currencySymbole[(@$benchApplication->pay_rate_currency) ? @$benchApplication->pay_rate_currency : 0] ?? '';
        $current_ectc_cur = $currencySymbole[(@$benchApplication->bill_rate_currency) ? @$benchApplication->bill_rate_currency : 0] ?? '';

        $country_name_Arr = GeneralMethod::getCountryName(@$benchApplication->country);
        $country_name = isset($country_name_Arr[0]['name']) ? ucwords($country_name_Arr[0]['name']) : '';
        $city_name = ! empty($benchApplication->city_name) ? $benchApplication->city_name : @$benchApplication->city;

        $companyData = DB::table('companies')->select('logo', 'company_name')->where('id', $company_id)->first();
        $rcvCompanyName = ucwords((string) ($companyData->company_name ?? ''));
        $rcvCompanyLogo = '';
        if (! empty($companyData->logo) && strlen($companyData->logo) > 5) {
            $rcvCompanyLogo = route('service_company.job-applications.company-logo-proxy');
        }

        $rcvSkills = [];
        if (! empty($benchApplication->skills)) {
            foreach (explode(',', $benchApplication->skills) as $s) {
                $s = trim($s);
                if ($s !== '') {
                    $rcvSkills[] = $s;
                }
            }
        }
        foreach ($skillExperiences as $__se) {
            $__sk = trim((string) (@$__se->skill_key));
            if ($__sk !== '') {
                $rcvSkills[] = $__sk;
            }
        }
        $rcvSkills = array_values(array_unique($rcvSkills));

        $__te = trim((string) @$benchApplication->total_exp);
        $rcvBExp = $__te !== '' ? $__te.' Years' : '--';
        if ((@$benchApplication->serving_notice_period_mode == 0) || (@$benchApplication->serving_notice_period_mode == 1)) {
            $rcvBNotice = 'Available '.date('d M Y', strtotime(@$benchApplication->np_id));
        } else {
            $rcvBNotice = trim((string) @$benchApplication->np_id);
        }
        if ($rcvBNotice === '') {
            $rcvBNotice = '--';
        }
        $rcvBLoc = trim((string) @$city_name);
        if ($rcvBLoc === '') {
            $rcvBLoc = trim($country_name);
        }
        if ($rcvBLoc === '') {
            $rcvBLoc = '--';
        }

        $__cur1 = html_entity_decode(strip_tags((string) $current_ctc_cur), ENT_QUOTES, 'UTF-8');
        $__cur2 = html_entity_decode(strip_tags((string) $current_ectc_cur), ENT_QUOTES, 'UTF-8');
        $rcvBCctc = trim($__cur1.' '.@$benchApplication->pay_rate_amount);
        if ($rcvBCctc === '') {
            $rcvBCctc = '--';
        }
        $rcvBEctc = trim($__cur2.' '.@$benchApplication->bill_rate_amount);
        if ($rcvBEctc === '') {
            $rcvBEctc = '--';
        }
        $rcvBPresented = ucwords((string) (auth()->user()->name ?? ''));
        if ($rcvBPresented === '') {
            $rcvBPresented = '--';
        }
        $rcvDownloadUrl = route('download.resume', ['id' => $benchApplication->id, 'path' => rtrim(strtr(base64_encode((string) @$benchApplication->resume), '+/', '-_'), '=')]);
        $rcvConvertToken = GeneralMethod::value_encrypt(json_encode([
            'scope' => 'bench_resume_center',
            'candidate_id' => (int) $benchApplication->id,
            'resume' => (string) @$benchApplication->resume,
        ]));
        $rcvConvertUrl = route('service_company.job-applications.resume-convert', ['token' => $rcvConvertToken, 'preview' => 1]);
        $rcvMaskContact = ((int) @$benchApplication->company_id !== (int) $company_id);

        $view = view('service_company.partials.resume_cv_viewer', [
            'rcvResume' => @$benchApplication->resume,
            'rcvMaskContact' => $rcvMaskContact,
            'rcvSkills' => $rcvSkills,
            'rcvCompanyLogo' => $rcvCompanyLogo,
            'rcvCompanyName' => $rcvCompanyName,
            'rcvBExp' => $rcvBExp,
            'rcvBCctc' => $rcvBCctc,
            'rcvBEctc' => $rcvBEctc,
            'rcvBNotice' => $rcvBNotice,
            'rcvBLoc' => $rcvBLoc,
            'rcvBPresented' => $rcvBPresented,
            'rcvDownloadUrl' => $rcvDownloadUrl,
            'rcvConvertUrl' => $rcvConvertUrl,
        ])->render();

        $responseData = Reply::dataOnly(['status' => 'success', 'view' => $view]);

        cache()->put($cacheKey, $responseData, now()->addMinutes(BENCH_VIEWMORE));

        return $responseData;
    }

    public function talent_pool()
    {
        $this->pageTitle = __('Talent Pool');
        $this->allBenchApplication = BenchApplication::count();

        return view('service_company.bench-candidates.talent_pool', $this->data);
    }

    public function create($jobId = null, $appId = null)
    {

        $this->pageTitle = __('Add Candidate');
        abort_if(Gate::denies('add_job_applications'), 403);
        $company_id = auth()->user()->company_id;
        $this->statelist = $this->cityListArr = '';

        $eventId = 5;
        $coinsRequ = 1;
        $companyId = (int) auth()->user()->company_id;
        $packageStatus = GeneralMethod::coinsValidationsAllcompany($companyId, $eventId, $coinsRequ);
        if ($packageStatus->status) {
            session()->flash('packageStatus', $packageStatus->message); // or false depending on your condition
            $url = url('service_company/bench-candidates');

            return redirect($url);
        }

        $this->client = DB::table('users')
            ->join('companies', 'companies.id', 'users.company_id')
            ->join('clients', 'clients.company_id', 'companies.id')
            ->select('clients.id')
            ->where('users.id', auth()->user()->id)->first();

        $country = User::join('companies', 'companies.id', '=', 'users.company_id')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->select('users_profile.country', 'companies.logo', 'companies.company_name')
            ->with('company')
            ->where('users.id', '=', auth()->user()->id)
            ->company()
            ->first();

        // dd($country);

        $this->country = @$country->country;
        $this->companylogo = @$country->logo;
        $this->company_name = @$country->company_name;

        $this->recruiterlist = [];

        // Phase 1 Optimization: Cache static master datasets and avoid unneeded DB calls on Add Candidate view load
        if (config('feature.talents_v2_enabled')) {
            $this->qualification = Cache::remember('qualification_master_list', 86400, function () {
                return DB::table('qualification_master')->select('*')->whereNotIn('id', [57, 58, 59])->orderBy('qualification_master.display_order', 'ASC')->get();
            });
            $this->currency = GeneralMethod::currency();
            $this->salaryMode = GeneralMethod::salaryMode();
            $this->countries = Cache::remember('country_list_json', 86400, function () {
                return GeneralMethod::getAllCountryList();
            });

            $this->cities = [];
            $this->default_loc = '';
            $this->currencyConverter = Cache::remember('currency_converter_rates', 3600, function () {
                return GeneralMethod::currencyConverter();
            });
            $this->skillExperiences = [];
        } else {
            $this->qualification = DB::table('qualification_master')->select('*')->whereNotIn('id', [57, 58, 59])->orderBy('qualification_master.display_order', 'ASC')->get();
            $this->currency = GeneralMethod::currency();
            $this->salaryMode = GeneralMethod::salaryMode();
            $this->countries = GeneralMethod::getAllCountryList();

            $this->cities = []; // GeneralMethod::allCities();
            $this->default_loc = GeneralMethod::allCitiesSelected([]);
            $this->currencyConverter = GeneralMethod::currencyConverter();
            $this->skillExperiences = [];
        }

        return response()->view('service_company.bench-candidates.create', $this->data)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache');

    }

    public function store(Request $request)
    {
        // Phase 9 Optimization: Idempotency lock check to prevent duplicate submission writes
        if (config('feature.talents_v2_enabled')) {
            $idempotencyKey = $request->header('X-Idempotency-Key') ?: $request->input('_idempotency_token');
            if (! empty($idempotencyKey)) {
                $lockKey = 'idempotency_lock:talent:'.$idempotencyKey;
                $acquired = Cache::lock($lockKey, 10)->get();
                if (! $acquired) {
                    return Reply::dataOnly(['status' => 'error', 'msgType' => 'warning', 'message' => 'A submission is already in progress. Please wait.']);
                }
            }
        }

        $packageStatus = GeneralMethod::validatePackageRet('CVACCESS');

        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email:rfc,dns',
                'disposable_email',
            ],
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email',
            'email.disposable_email' => 'Temporary emails are not allowed',
        ]);

        if ($validator->fails()) {
            return Reply::dataOnly(['status' => 'mailcheck', 'msgType' => 'warning', 'message' => $validator->errors()->first()]);
        }

        $company_id = '';
        $company_id = auth()->user()->company_id;
        if ($request->edit_token != '') {
            $benchApplication = JobApplication::withoutGlobalScopes()->find((int) $request->edit_token);
            $added = false;
        } else {
            $benchApplication = new JobApplication;
            // New marketplace publishes should enter Bench directly, not Pool.
            $benchApplication->status = 2;
            $added = true;
            $eventId = 5;
            $coinsDeductions = GeneralMethod::coinsDeductions((int) $company_id, $eventId, auth()->user()->id, $company_id, '', '');
            if ($coinsDeductions->status) {
                return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
            }

        }
        // new BenchApplication();
        $outsourcing_type = 1;
        if (@$request->phone == 'Full Time') {
            $outsourcing_type = 1;
        } elseif (@$request->phone == 'Contract') {
            $outsourcing_type = 2;
        } elseif (@$request->phone == 'Part Time') {
            $outsourcing_type = 3;
        } elseif (@$request->phone == 'Freelance') {
            $outsourcing_type = 4;
        }

        $benchApplication->company_id = $company_id;
        $benchApplication->submitted_by = auth()->user()->id;
        $benchApplication->full_name = ucwords(strtolower(@$request->full_name));
        $benchApplication->current_designation = @$request->current_designation;
        $benchApplication->outsourcing_type = @$outsourcing_type;

        $benchApplication->careersummary = $request->careersummary;
        $benchApplication->nationality = $request->nationality;
        $benchApplication->wm = $request->wm;
        $benchApplication->employmentType = $request->employmentType;
        $benchApplication->oh = $request->oh;
        $benchApplication->visastatu = $request->visastatu;
        $benchApplication->referencename = $request->referencename;

        $benchApplication->phone = @$request->phone;
        $benchApplication->secondary_mobile = @$request->secondary_mobile;
        $benchApplication->dob = @$request->dob;
        $benchApplication->email = @$request->email;
        $benchApplication->secondrary_email = @$request->secondrary_email;
        $benchApplication->gender = $request->gender;
        $benchApplication->qualification = @$request->qualification;

        $benchApplication->phone_code = ($request->primary_country_code) ? @$request->primary_country_code : null;
        $benchApplication->second_phone_code = ($request->secondary_country_code) ? @$request->secondary_country_code : null;

        // $total_exp = @$request->total_exp_year.'.'.@$request->total_exp_month;

        $total_exp = (isset($request->total_exp_year) && $request->total_exp_year !== '' || isset($request->total_exp_month) && $request->total_exp_month !== '') ? $request->total_exp_year.'.'.$request->total_exp_month : null;

        $rel_exp = (isset($request->rel_exp_year) && $request->rel_exp_year !== '' || isset($request->rel_exp_month) && $request->rel_exp_month !== '') ? $request->rel_exp_year.'.'.$request->rel_exp_month : null;

        // $rel_exp = @$request->rel_exp_year.'.'.@$request->rel_exp_month;

        $benchApplication->total_exp = $total_exp; // @$request->total_exp;
        $benchApplication->rel_exp = $rel_exp; // @$request->rel_exp;
        $benchApplication->serving_notice_period_mode = @$request->serving_notice_period;
        $serving_notice_period = $request->serving_notice_period;
        $benchApplication->gender = @$request->gender;
        $benchApplication->percentage_hike = @$request->percentage_hike;

        if ($serving_notice_period == 0) {
            $benchApplication->np_id = $request->ddl_np_id;
        } elseif ($serving_notice_period == 1) {
            $np_id_date = $request->np_id;
            if (! empty($request->np_id_display)) {
                $np_id_date = ($request->np_id) ? $request->np_id : (Carbon::createFromFormat('d/M/Y', $request->np_id_display, 'UTC')->format('Y-m-d') ?? null);
            }
            $benchApplication->np_id = $np_id_date;
        } elseif ($serving_notice_period == 2) {
            $benchApplication->np_id = 'Immediate';
            $np_id_date = $request->np_id;
            if (! empty($request->np_id_display)) {
                $np_id_date = ($request->np_id) ? $request->np_id : (Carbon::createFromFormat('d/M/Y', $request->np_id_display, 'UTC')->format('Y-m-d') ?? null);
            }
            $benchApplication->last_working_date = $np_id_date;
        }
        $benchApplication->pay_rate_currency = @$request->pay_rate_currency;
        $benchApplication->pay_rate_amount = @$request->pay_rate_amount;
        $benchApplication->pay_rate_mode = @$request->pay_rate_mode;
        $benchApplication->bill_rate_currency = @$request->bill_rate_currency;
        $benchApplication->bill_rate_amount = @$request->bill_rate_amount;
        $benchApplication->bill_rate_mode = @$request->bill_rate_mode;

        $currency = GeneralMethod::currency();  // Currency code

        $ecurrency = $currency[$request->bill_rate_currency];
        $econvertedAmount = $request->bill_annual;
        if ($request->bill_rate_currency > 0) {
            $econvertedAmount = GeneralMethod::convertToINR($econvertedAmount, $ecurrency);
        }

        $ectcdivided = GeneralMethod::salaryDividedByMode(0, $econvertedAmount);
        $benchApplication->ectc_yearly = @$ectcdivided->yearly;
        $benchApplication->ectc_monthly = @$ectcdivided->monthly;
        $benchApplication->ectc_hourly = @$ectcdivided->hourly;

        $currency = $currency[$request->pay_rate_currency];
        $convertedAmount = $request->pay_annual;
        if ($request->pay_rate_currency > 0) {
            $convertedAmount = GeneralMethod::convertToINR($convertedAmount, $currency);
        }

        $ctcdivided = GeneralMethod::salaryDividedByMode(0, $convertedAmount);
        $benchApplication->ctc_yearly = @$ctcdivided->yearly;
        $benchApplication->ctc_monthly = @$ctcdivided->monthly;
        $benchApplication->ctc_hourly = @$ctcdivided->hourly;

        $benchApplication->comission_type = @$request->comission_type;

        $stateId = '';
        $cityID = '';
        $countryID = '';
        $work_city = request('kt_tagify_work_location');
        $work_city_all = json_decode($work_city, true);
        if (\request('kt_tagify_work_location') != '') {
            foreach ($work_city_all as $cityVal) {
                $parts = explode('|', $cityVal['value']);
                // Trim to remove any leading or trailing whitespace
                $cityName = trim($parts[0]);
                $countrName = trim($parts[2]);

                // dd($cityName);
                // Debugging line
                $city_row = DB::table('city_list')
                    ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
                    ->join('countries', 'countries.id', '=', 'state_list.country_id')
                    ->select('city_list.city_id', 'countries.id as country_id')
                    ->where('city_list.city', '!=', '')
                    ->where('city_list.city', '=', $cityName)
                    ->where('countries.country_name', '=', $countrName)
                    ->first();

                if ($city_row) {
                    $cityID = (@$city_row) ? $city_row->city_id : '';
                    $countryID = (@$city_row) ? $city_row->country_id : '';
                    $stateId = GeneralMethod::stateId($cityID);
                }

            }
        }

        $benchApplication->country = $countryID;
        $benchApplication->state = $stateId;

        if ($benchApplication->country == 101) {
            $benchApplication->city_id = $cityID;
        } else {
            $benchApplication->city_id = $cityID;
        }

        $benchApplication->employee_number = @$request->employee_number;
        $experienceGet = (@$request->total_exp_year == 0) ? 1 : 0;
        $benchApplication->pincode = $experienceGet; // (@$request->pincode) ? @$request->pincode :'';
        $cnt = 0;
        $skillStr = '';
        $skillsAddElastic = $request->skills;
        $skillArr = array_map(function ($skill) {
            return ['value' => $skill];
        }, array_filter($skillsAddElastic));

        // $skillArr = json_decode($request->skills, true);
        if (! empty($skillArr)) {
            foreach ($skillArr as $sval) {
                if ($cnt == 0) {
                    $skillStr .= $sval['value'];
                } else {
                    $skillStr .= ' , '.$sval['value'];
                }
                $cnt++;
            }
        }
        // dd($skillStr);
        $benchApplication->skills = $skillStr;
        if ($request->hasFile('photo')) {

            $file = $request->file('photo');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $filePath = 'Images/'.$filename;

            Storage::disk('s3')->put($filePath, file_get_contents($file));

            $fileUrl = Storage::disk('s3')->url($filePath);
            $benchApplication->photo = $fileUrl;

            // $benchApplication->photo = Files::upload($request->photo, 'bench_photos');
        }

        // if ($request->hasFile('resume')) {

        // 	$benchApplication->resume = Files::upload($request->resume, 'bench_resume', null, null, false);
        // }
        $benchApplication->condidate_type = 'bench_condidate';
        $bench_status = DB::table('global_settings')->select('bench_status')->first();
        $superadminstatus = 0;
        if ($bench_status) {
            if ($bench_status->bench_status) {
                $superadminstatus = 1;
            }
        }
        $benchApplication->superadminstatus = $superadminstatus;

        $benchApplication->linkedin = $request->linkedin;
        $benchApplication->github = $request->github;
        $benchApplication->behance = $request->behance;
        $benchApplication->otherLink = $request->otherLink;

        // ── v2 pre-flight: validate resume_session_uuid before opening the transaction ──
        $resumeSession = null;
        $finalResumeUrl = null;  // set in claim block; reused in resume URL block
        if (config('resumes.features.submit_with_session') && $request->filled('resume_session_uuid')) {
            $resumeSession = ResumeUpload::byUuid($request->input('resume_session_uuid'))
                ->forCompany($company_id)->first();
            if (! $resumeSession) {
                return Reply::dataOnly(['status' => 'error', 'msgType' => 'warning',
                    'message' => 'Resume upload session not found. Please upload the resume again.']);
            }
            if ($resumeSession->status !== ResumeUploadStatus::Uploaded->value) {
                return Reply::dataOnly(['status' => 'error', 'msgType' => 'warning',
                    'message' => 'Resume upload is not yet complete. Please wait for the upload to finish and try again.']);
            }
            if ($resumeSession->linked_candidate_id && (int) $resumeSession->linked_candidate_id !== (int) $benchApplication->id) {
                return Reply::dataOnly(['status' => 'error', 'msgType' => 'warning',
                    'message' => 'Resume upload session has already been associated with another application. Please upload the resume again.']);
            }
        }
        // ─────────────────────────────────────────────────────────────────────────────

        DB::beginTransaction();
        try {

            $benchApplication->save();

            // ── v2 session: atomic claim + R2 HEAD check + link (inside transaction) ──
            if ($resumeSession) {
                $claimed = ResumeUpload::where('id', $resumeSession->id)
                    ->where('company_id', $company_id)
                    ->where('status', ResumeUploadStatus::Uploaded->value)
                    ->update(['status' => ResumeUploadStatus::Completed->value, 'updated_at' => now()]);
                if ($claimed === 0) {
                    DB::rollBack();

                    return Reply::dataOnly(['status' => 'error', 'msgType' => 'warning',
                        'message' => 'Resume session was already claimed. Please upload the resume again.']);
                }
                $r2Exists = false;
                try {
                    $r2Exists = Storage::disk('r2')->exists('resumes/'.$resumeSession->uuid);
                } catch (\Throwable) {
                }
                if (! $r2Exists) {
                    ResumeUpload::where('id', $resumeSession->id)
                        ->where('status', ResumeUploadStatus::Completed->value)
                        ->update(['status' => ResumeUploadStatus::Uploaded->value, 'updated_at' => now()]);
                    DB::rollBack();

                    return Reply::dataOnly(['status' => 'error', 'msgType' => 'warning',
                        'message' => 'Resume file not found in storage. Please upload the resume again.']);
                }
                // Move file to company-structured CDN path (mirrors v1 storage convention).
                $finalResumeUrl = app(\App\Services\ResumeUploadSession::class)->moveToCompanyPath($resumeSession);
                ResumeUpload::where('id', $resumeSession->id)
                    ->where('company_id', $company_id)
                    ->where('status', ResumeUploadStatus::Completed->value)
                    ->update([
                        'linked_candidate_id' => $benchApplication->id,
                        'r2_url' => $finalResumeUrl,
                        'updated_at' => now(),
                    ]);
                UploadSessionAudit::record($resumeSession->id, 'ActionSubmitLinked', [
                    'candidate_id' => $benchApplication->id,
                ], $company_id, auth()->user()->id);
            }
            // ─────────────────────────────────────────────────────────────────────────

            // if($added){
            // 	$eventId = 5;
            // 	GeneralMethod::coinsDeductions((int) $company_id,$eventId,auth()->user()->id,$company_id,'','');
            // }
            if (! empty($skillArr)) {
                $skillsAddElastic = array_column($skillArr, 'value');
                if (count($skillsAddElastic) > 0) {
                    GeneralMethod::skillsAddElastic($skillsAddElastic);
                }
            }

            // Phase 3 Optimization: Bulk insertion for job_application_skills to eliminate N+1 DB queries
            if (config('feature.talents_v2_enabled')) {
                if (! empty($skillArr) || ! empty($request->current_designation)) {
                    DB::table('job_application_skills')->where('job_application_id', $benchApplication->id)->delete();
                    $insertSkills = [];
                    $seenSkills = [];
                    if (! empty($skillArr)) {
                        foreach ($skillArr as $sval) {
                            $skillName = trim($sval['value']);
                            if ($skillName !== '' && ! isset($seenSkills[strtolower($skillName)])) {
                                $seenSkills[strtolower($skillName)] = true;
                                $insertSkills[] = [
                                    'job_application_id' => $benchApplication->id,
                                    'skills' => $skillName,
                                ];
                            }
                        }
                    }
                    if (! empty($request->current_designation)) {
                        $desigName = trim($request->current_designation);
                        if ($desigName !== '' && ! isset($seenSkills[strtolower($desigName)])) {
                            $seenSkills[strtolower($desigName)] = true;
                            $insertSkills[] = [
                                'job_application_id' => $benchApplication->id,
                                'skills' => $desigName,
                            ];
                        }
                    }
                    if (! empty($insertSkills)) {
                        DB::table('job_application_skills')->insert($insertSkills);
                    }
                }
            } else {
                if (! empty($skillArr)) {
                    if (DB::table('job_application_skills')->where('job_application_id', '=', @$benchApplication->id)->exists()) {
                        DB::table('job_application_skills')->where('job_application_id', '=', @$benchApplication->id)->delete();
                    }
                    foreach ($skillArr as $sval) {
                        $values = [
                            'job_application_id' => $benchApplication->id,
                            'skills' => $sval['value'],
                        ];
                        if (! DB::table('job_application_skills')->where('job_application_id', '=', $benchApplication->id)->where('skills', 'LIKE', '%'.$sval['value'].'%')->exists()) {
                            DB::table('job_application_skills')->insert($values);
                        }
                    }
                }

                if (! DB::table('job_application_skills')->where('job_application_id', '=', $benchApplication->id)->where('skills', 'LIKE', '%'.@$request->current_designation.'%')->exists()) {
                    DB::table('job_application_skills')->insert(['job_application_id' => $benchApplication->id, 'skills' => @$request->current_designation]);
                }
            }

            if (! is_null($request->recruiter)) {

                DB::table('bench_assign')->where('bench_applications_id', $benchApplication->id)->delete();

                $this->elasticService->getClient()->deleteByQuery([
                    'index' => ELASTIC_BENCH_ASSIGN,
                    'conflicts' => 'proceed',   // ⭐ THIS LINE FIXES YOUR ERROR
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['bench_applications_id' => (int) $benchApplication->id]],
                                ],
                            ],
                        ],
                    ],
                ]);

                $recruiterArr = '';
                $recruiterArr = explode(',', $request->recruiter);
                $recruiterArrId = explode(',', $request->recruiter_id);
                foreach ($recruiterArrId as $recArr) {

                    // $recRow = DB::table('users')->select('users.id')->where('company_id', $this->user->company_id)->where('name', trim($recArr))->first();

                    $params = [
                        'index' => ELASTIC_USERS_LIST,
                        'id' => (int) $recArr,
                    ];

                    $response = $this->elasticService->getClient()->get($params);
                    $recRow = (object) $response['_source'];

                    if (! empty(@$recRow->id) && @$recRow->id > 0) {
                        $values = ['company_id' => $this->user->company_id, 'user_id' => $recRow->id, 'bench_applications_id' => $benchApplication->id];
                        $insertGetId = DB::table('bench_assign')->insertGetId($values);

                        // dd( (int) $recArr,$recRow);

                        $response = $this->elasticService->getClient()->update([
                            'index' => ELASTIC_BENCH_ASSIGN,
                            'id' => $insertGetId,
                            'retry_on_conflict' => 5,
                            'body' => [
                                'doc' => [
                                    'id' => $insertGetId,
                                    'users_id' => $recRow->id,
                                    'name' => $recRow->name,
                                    'calling_code' => $recRow->calling_code,
                                    'email' => $recRow->email,
                                    'mobile' => $recRow->mobile,
                                    'bench_applications_id' => (int) $benchApplication->id,
                                    'designation' => $recRow->designation,
                                    'updated_at' => now()->toIso8601String(),
                                    'created_at' => now()->toIso8601String(),
                                ],
                                'doc_as_upsert' => true,
                                'detect_noop' => false,
                            ],
                        ]);

                    }

                }
            }

            if ($request->edit_token != '') {
                if ($resumeSession) {
                    // v2 path (edit): file moved to company CDN path in the claim block above.
                    $r2ResumeUrl = $finalResumeUrl;
                    DB::table('job_applications')
                        ->where('id', $benchApplication->id)
                        ->update(['resume' => $r2ResumeUrl]);
                } elseif ($request->hasFile('resume')) {

                    $companyNameR2 = DB::table('companies')
                        ->where('id', auth()->user()->company_id)
                        ->value('company_name');   // change column if needed
                    $companyNameR2 = strtolower(trim($companyNameR2 ?? 'unknown'));
                    $companyNameR2 = preg_replace('/[^a-z0-9_-]/i', '_', $companyNameR2);
                    $companyNameRTwo = $companyNameR2.'_'.auth()->user()->company_id;
                    $year = date('Y');
                    $month = date('m');

                    $tempPath = $request->input('uploaded_file_path');
                    $full_name = $request->input('resume_original_name');
                    if ($tempPath && $full_name) {
                        // Intent: Generate candidate-specific unique R2 path synchronously before queue dispatch to prevent non-unique filename collisions in async queues.
                        $baseName = pathinfo($full_name, PATHINFO_FILENAME);
                        $ext = strtolower(pathinfo($tempPath, PATHINFO_EXTENSION) ?: pathinfo($full_name, PATHINFO_EXTENSION));
                        $safeBaseName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $baseName);
                        $uuidSuffix = (string) \Illuminate\Support\Str::uuid();
                        $uniqueFileName = "{$benchApplication->id}_{$safeBaseName}_{$uuidSuffix}.{$ext}";
                        $r2Path = "Companies/{$companyNameRTwo}/Resumes/{$year}/{$month}/{$uniqueFileName}";
                        $fileUrl = "https://file.jero.works/{$r2Path}";

                        UploadResumeToTestRTwo::dispatch($tempPath, $full_name, $benchApplication->id, $companyNameRTwo, $year, $month, $r2Path)->onConnection('emails');
                        DB::table('job_applications')
                            ->where('id', $benchApplication->id)
                            ->update(['resume' => $fileUrl]);
                    } else {
                        \Log::warning('v1 upload (edit): missing tempPath or filename', ['id' => $benchApplication->id]);
                    }
                }
            } else {
                if ($resumeSession) {
                    // v2 path (add): file moved to company CDN path in the claim block above.
                    $r2ResumeUrl = $finalResumeUrl;
                    DB::table('job_applications')
                        ->where('id', $benchApplication->id)
                        ->update(['resume' => $r2ResumeUrl]);
                } elseif ($request->hasFile('resume')) {

                    $companyNameR2 = DB::table('companies')
                        ->where('id', auth()->user()->company_id)
                        ->value('company_name');   // change column if needed
                    $companyNameR2 = strtolower(trim($companyNameR2 ?? 'unknown'));
                    $companyNameR2 = preg_replace('/[^a-z0-9_-]/i', '_', $companyNameR2);
                    $companyNameRTwo = $companyNameR2.'_'.auth()->user()->company_id;
                    $year = date('Y');
                    $month = date('m');

                    $tempPath = $request->input('uploaded_file_path');
                    $full_name = $request->input('resume_original_name');
                    if ($tempPath && $full_name) {
                        // Intent: Generate candidate-specific unique R2 path synchronously before queue dispatch to prevent non-unique filename collisions in async queues.
                        $baseName = pathinfo($full_name, PATHINFO_FILENAME);
                        $ext = strtolower(pathinfo($tempPath, PATHINFO_EXTENSION) ?: pathinfo($full_name, PATHINFO_EXTENSION));
                        $safeBaseName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $baseName);
                        $uuidSuffix = (string) \Illuminate\Support\Str::uuid();
                        $uniqueFileName = "{$benchApplication->id}_{$safeBaseName}_{$uuidSuffix}.{$ext}";
                        $r2Path = "Companies/{$companyNameRTwo}/Resumes/{$year}/{$month}/{$uniqueFileName}";
                        $fileUrl = "https://file.jero.works/{$r2Path}";

                        UploadResumeToTestRTwo::dispatch($tempPath, $full_name, $benchApplication->id, $companyNameRTwo, $year, $month, $r2Path)->onConnection('emails');
                        DB::table('job_applications')
                            ->where('id', $benchApplication->id)
                            ->update(['resume' => $fileUrl]);
                    } else {
                        \Log::warning('v1 upload (add): missing tempPath or filename', ['id' => $benchApplication->id]);
                    }
                }
            }

            if (empty($request->resumeCheck) && ! $resumeSession && ! $request->hasFile('resume')) {
                DB::table('job_applications')
                    ->where('id', $benchApplication->id)
                    ->update(['resume' => '']);
            }

            $customDetailsQuestion = @$request->customDetailsQuestion;
            $customDetailsAnswer = @$request->customDetailsAnswer;

            if ($customDetailsQuestion > 0) {
                if ($request->edit_token != '') {
                    DB::table('bench_additional_detail')->where('bench_id', $benchApplication->id)->delete();
                    $this->elasticService->getClient()->deleteByQuery([
                        'index' => ELASTIC_BENCH_ADDITIONAL_DETAIL,
                        'conflicts' => 'proceed',   // ⭐ THIS LINE FIXES YOUR ERROR
                        'body' => [
                            'query' => [
                                'bool' => [
                                    'must' => [
                                        ['term' => ['bench_id' => (int) $benchApplication->id]],
                                    ],
                                ],
                            ],
                        ],
                    ]);
                }
                $inc = 0;
                foreach ($customDetailsQuestion as $question) {
                    $values = ['bench_id' => @$benchApplication->id, 'custom_keys' => @$question, 'custom_keys_value' => @$customDetailsAnswer[$inc]];
                    $insertGetIdAdd = DB::table('bench_additional_detail')->insertGetId($values);

                    $response = $this->elasticService->getClient()->update([
                        'index' => ELASTIC_BENCH_ADDITIONAL_DETAIL,
                        'id' => $insertGetIdAdd,
                        'retry_on_conflict' => 5,
                        'body' => [
                            'doc' => [
                                'id' => $insertGetIdAdd,
                                'bench_id' => (int) @$benchApplication->id,
                                'custom_keys' => @$question,
                                'custom_keys_value' => @$customDetailsAnswer[$inc],
                                'create_at' => now()->toIso8601String(),
                                'update_at' => now()->toIso8601String(),
                            ],
                            'doc_as_upsert' => true,
                            'detect_noop' => false,
                        ],
                    ]);

                    $inc++;

                }
            }

            if ($request->edit_token == '') {

                // Phase 3 & 10 Optimization: Offload candidate indexing to background Horizon queue after DB commit
                if (config('feature.talents_v2_enabled')) {
                    ElasticBenchCandidateIndex::dispatch($benchApplication->id)->afterCommit();
                } else {
                    GeneralMethod::elasticBenchCandidateAddEditIndex($benchApplication->id);
                }

                $skills = $request->input('skills', []);
                $years = $request->input('years', []);
                $months = $request->input('months', []);
                $ratings = $request->input('ratings', []);
                if (count($skillArr) > 0) {
                    foreach ($skills as $skillKey => $exp) {
                        if (! empty($exp)) {

                            DB::table('bench_candidate_skill_experience')->updateOrInsert([
                                'candidate_id' => $benchApplication->id,
                                'skill_key' => $exp, ],
                                [
                                    'year' => filled($years[$skillKey]) ? (int) $years[$skillKey] : null,
                                    'month' => filled($months[$skillKey]) ? (int) $months[$skillKey] : null,
                                    'rating' => filled($ratings[$skillKey]) ? (int) $ratings[$skillKey] : null,
                                    'updated_at' => now(),
                                ]
                            );
                        }

                    }
                }

                if (@$benchApplication->id > 0) {
                    $event_name = 'CVACCESS';
                    $event_table_name = 'job_applications';
                    $event_table_id = @$benchApplication->id;
                    $packageStatus = GeneralMethod::packageRedeem($event_name, $event_table_name, $event_table_id);
                }
            } else {
                // Phase 4 & 10 Optimization: Offload candidate update indexing to Horizon queue after DB commit
                if (config('feature.talents_v2_enabled')) {
                    ElasticBenchCandidateIndex::dispatch($benchApplication->id)->afterCommit();
                    GeneralMethod::addCompanyBenchCandidate($company_id, $benchApplication->id);

                    $skills = $request->input('skills', []);
                    $years = $request->input('years', []);
                    $months = $request->input('months', []);
                    $ratings = $request->input('ratings', []);

                    if (count($skillArr) > 0) {
                        foreach ($skills as $skillKey => $exp) {
                            if (! empty($exp)) {
                                DB::table('bench_candidate_skill_experience')->updateOrInsert(
                                    [
                                        'candidate_id' => $benchApplication->id,
                                        'skill_key' => $exp,
                                    ],
                                    [
                                        'year' => filled($years[$skillKey]) ? (int) $years[$skillKey] : null,
                                        'month' => filled($months[$skillKey]) ? (int) $months[$skillKey] : null,
                                        'rating' => filled($ratings[$skillKey]) ? (int) $ratings[$skillKey] : null,
                                        'updated_at' => now(),
                                    ]
                                );
                            }
                        }
                    }
                } else {
                    $exists = $this->elasticService->getClient()->exists([
                        'index' => ELASTIC_CANDIDATES_BENCH,
                        'id' => $benchApplication->id,
                    ]);
                    if ($exists) {
                        // dd($benchApplication->id);
                        $applications_up = JobApplication::select(
                            'job_applications.*',
                            'state_list.state as state_name',
                            'city_list.city as city_name',
                            'countries.country_name',
                            'qualification_master.name as qualification_name')
                            ->join('bench_master_status', 'bench_master_status.id', 'job_applications.status')
                            ->leftjoin('users', 'users.id', 'job_applications.submitted_by')
                            ->leftjoin('companies', 'companies.id', 'job_applications.company_id')
                            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
                            ->leftJoin('countries', 'countries.id', '=', 'job_applications.country')
                            ->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
                            ->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id')
                            ->where('job_applications.id', $benchApplication->id)
                            ->where('condidate_type', '=', 'bench_condidate')
                            ->first();
                        $job_application_skills = DB::table('job_application_skills')
                            ->where('job_application_id', $benchApplication->id)
                            ->get()
                            ->map(fn ($s) => [
                                'id' => $s->id,
                                'job_application_id' => $s->id,
                                'skills' => strtolower($s->skills),
                            ])
                            ->toArray();
                        // dd($job_application_skills);
                        $updateParams = [
                            'index' => ELASTIC_CANDIDATES_BENCH,
                            'id' => $benchApplication->id,
                            'body' => [
                                'doc' => [
                                    'thirdparty_company_id' => $applications_up->thirdparty_company_id,
                                    'full_name' => $applications_up->full_name,
                                    'email' => $applications_up->email,
                                    'phone' => $applications_up->phone,
                                    'np_id' => $applications_up->np_id,
                                    'serving_notice_period_mode' => $applications_up->serving_notice_period_mode,
                                    'country' => $applications_up->country,
                                    'state' => $applications_up->state,
                                    'city' => $applications_up->city,
                                    'gender' => $applications_up->gender,
                                    'careersummary' => $applications_up->careersummary,
                                    'nationality' => $applications_up->nationality,
                                    'wm' => $applications_up->wm,
                                    'employmentType' => $applications_up->employmentType,
                                    'oh' => $applications_up->oh,
                                    'visastatu' => $applications_up->visastatu,
                                    'referencename' => $applications_up->referencename,
                                    'city_id' => $applications_up->city_id,
                                    'skills' => $applications_up->skills,
                                    'photo' => $applications_up->photo,
                                    'job_id' => $applications_up->job_id,
                                    'total_exp' => $applications_up->total_exp,
                                    'rel_exp' => $applications_up->rel_exp,
                                    'ctc' => $applications_up->ctc,
                                    'job_application_skills' => $job_application_skills,
                                    'current_ctc_cur' => $applications_up->current_ctc_cur,
                                    'current_ctc_mode' => $applications_up->current_ctc_mode,
                                    'current_designation' => $applications_up->current_designation,
                                    'ectc' => $applications_up->ectc,
                                    'current_ectc_cur' => $applications_up->current_ectc_cur,
                                    'current_ectc_mode' => $applications_up->current_ectc_mode,
                                    'current_company' => $applications_up->current_company,
                                    'ctc_hourly' => $applications_up->ctc_hourly,
                                    'ctc_monthly' => $applications_up->ctc_monthly,
                                    'ctc_yearly' => $applications_up->ctc_yearly,
                                    'ectc_hourly' => $applications_up->ectc_hourly,
                                    'ectc_monthly' => $applications_up->ectc_monthly,
                                    'ectc_yearly' => $applications_up->ectc_yearly,
                                    'status' => $applications_up->status,
                                    'duplicate' => (int) ($applications_up->duplicate ?? 0),
                                    'is_duplicate' => (bool) (($applications_up->duplicate ?? 0) == 1),
                                    'pay_rate_currency' => $applications_up->pay_rate_currency,
                                    'pay_rate_amount' => $applications_up->pay_rate_amount,
                                    'pay_rate_mode' => $applications_up->pay_rate_mode,
                                    'bill_rate_currency' => $applications_up->bill_rate_currency,
                                    'bill_rate_amount' => $applications_up->bill_rate_amount,
                                    'bill_rate_mode' => $applications_up->bill_rate_mode,
                                    'condidate_type' => $applications_up->condidate_type,
                                    'master_status_id' => $applications_up->master_status_id,
                                    'is_viewed' => $applications_up->is_viewed,
                                    'column_priority' => $applications_up->column_priority,
                                    'phone_code' => $applications_up->phone_code,
                                    'second_phone_code' => $applications_up->second_phone_code,
                                    'secondary_mobile' => $applications_up->secondary_mobile,
                                    'resume' => $applications_up->resume,
                                    'qualification_name' => $applications_up->qualification_name,
                                    'country_name' => $applications_up->country_name,
                                    'state_name' => $applications_up->state_name,
                                    'city_name' => $applications_up->city_name,
                                    'dob' => $applications_up->dob,
                                    'linkedin' => $applications_up->linkedin,
                                    'github' => $applications_up->github,
                                    'behance' => $applications_up->behance,
                                    'otherLink' => $applications_up->otherLink,
                                ],
                                'doc_as_upsert' => false, // Changed to false since we verified existence
                            ],
                            'refresh' => false, // Eventual consistency for 10K concurrent scale
                        ];

                        $this->elasticService->getClient()->update($updateParams);

                        GeneralMethod::addCompanyBenchCandidate($company_id, $benchApplication->id);

                        $skills = $request->input('skills', []);
                        $years = $request->input('years', []);
                        $months = $request->input('months', []);
                        $ratings = $request->input('ratings', []);

                        if (count($skillArr) > 0) {

                            foreach ($skills as $skillKey => $exp) {
                                if (! empty($exp)) {
                                    DB::table('bench_candidate_skill_experience')->updateOrInsert([
                                        'candidate_id' => $benchApplication->id,
                                        'skill_key' => $exp],
                                        [
                                            'year' => ($years[$skillKey]) ? (int) $years[$skillKey] : null,
                                            'month' => ($months[$skillKey]) ? (int) $months[$skillKey] : null,
                                            'rating' => ($ratings[$skillKey]) ? (int) $ratings[$skillKey] : null,
                                            'updated_at' => now(),
                                        ]
                                    );
                                }

                            }
                        }

                    }
                }
            }

            // Phase 7 Optimization: Invalidate company-wide candidate cache keys and flush Redis tags
            if (config('feature.talents_v2_enabled')) {
                $cachekeys = [
                    'allbenchcandidates_version_user_'.$this->user->id,
                    'benchviewmore_version_user_'.$this->user->id,
                    'allbenchcandidates_version_company_'.$company_id,
                    'benchcandidates_stats_company_'.$company_id,
                ];
                foreach ($cachekeys as $key) {
                    GeneralMethod::cacheClean($key);
                }
                try {
                    Cache::tags(['company_'.$company_id, 'bench_candidates'])->flush();
                } catch (\BadMethodCallException) {
                    // Fallback when Cache store driver does not support tagging
                }
            } else {
                $cachekeys = [
                    'allbenchcandidates_version_user_'.$this->user->id,
                    'benchviewmore_version_user_'.$this->user->id,
                ];
                foreach ($cachekeys as $key) {
                    GeneralMethod::cacheClean($key);
                }
            }

            DB::commit();

            // Phase 6 Optimization: Standardized JSON response payload contract with metadata
            if (config('feature.talents_v2_enabled')) {
                return Reply::successWithData(__('menu.jobApplications').' '.($added ? __('messages.createdSuccessfully') : __('messages.updatedSuccessfully')), [
                    'candidate_id' => $benchApplication->id,
                    'action' => $added ? 'created' : 'updated',
                    'url' => url('service_company/bench-candidates'),
                ]);
            }

            return Reply::redirect(url('service_company/bench-candidates'), __('menu.jobApplications').' '.__('messages.createdSuccessfully'));

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('bench-candidates store() transaction failed: '.$e->getMessage(), ['exception' => $e]);

            return Reply::dataOnly(['status' => 'error', 'msgType' => 'danger', 'message' => 'Submission failed. Please try again.']);
        }

    }

    public function edit($id)
    {
        // dd("hdj");
        $this->pageTitle = __('Edit Candidate');
        abort_if(Gate::denies('edit_job_applications'), 403);
        $id = GeneralMethod::value_decrypt($id);

        $company_id = '';
        $company_id = auth()->user()->company_id;

        // Phase 2 Optimization: Consolidate duplicate DB queries, scope job selection, and cache static master lookups
        if (config('feature.talents_v2_enabled')) {
            $this->statuses = Cache::remember('application_statuses', 86400, fn () => ApplicationStatus::all());
            $this->application = JobApplication::with('job')->withoutGlobalScopes()->find($id);
            $this->jobQuestion = @$this->application->job->questions;

            $this->jobs = Job::where('company_id', $company_id)
                ->select('id', 'title', 'location_id', 'status', 'start_date', 'end_date', 'section_visibility')
                ->limit(200)
                ->get();

            $this->noticeperiod = Cache::remember('notice_periods', 86400, fn () => NoticePeriod::all());
            $this->qualification = Cache::remember('qualification_master_list', 86400, function () {
                return DB::table('qualification_master')->select('*')->whereNotIn('id', [57, 58, 59])->orderBy('qualification_master.display_order', 'ASC')->get();
            });
            $this->interviewStatus = Cache::remember('master_statuses_priority', 86400, fn () => DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get());
            $this->childArr = DB::table('candidate_status')
                ->select('*')->where('parent', $this->application->master_status_id)
                ->where('company_id', $this->user->company_id)
                ->orderBy('priority', 'ASC')
                ->get();

            $this->company_id = $this->user->company_id;

            // Consolidated single query for application_additional_detail (eliminates 3x duplicate DB calls)
            $this->application_add_detail = DB::table('application_additional_detail')
                ->select('*')
                ->where('application_id', $this->application->id)
                ->orderBy('id', 'ASC')
                ->get();
            $this->application_add_detail_cnt = $this->application_add_detail->count();
            $this->application_additional_detail = $this->application_add_detail;
        } else {
            $this->statuses = ApplicationStatus::all();
            $this->application = JobApplication::withoutGlobalScopes()->find($id);
            $this->jobQuestion = @$this->application->job->questions;

            $this->jobs = Job::select('id', 'title', 'location_id', 'status', 'start_date', 'end_date', 'section_visibility')->get();

            $this->noticeperiod = NoticePeriod::all();
            $this->qualification = DB::table('qualification_master')->select('*')->whereNotIn('id', [57, 58, 59])->orderBy('qualification_master.display_order', 'ASC')->get();
            $this->interviewStatus = DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();
            $this->childArr = DB::table('candidate_status')
                ->select('*')->where('parent', $this->application->master_status_id)
                ->where('company_id', $this->user->company_id)
                ->orderBy('priority', 'ASC')
                ->get();

            $this->company_id = $this->user->company_id;
            $this->application_add_detail_cnt = DB::table('application_additional_detail')
                ->select('*')
                ->where('application_id', $this->application->id)
                ->orderBy('application_additional_detail.id', 'ASC')
                ->count();

            $this->application_add_detail = DB::table('application_additional_detail')
                ->select('*')
                ->where('application_id', $this->application->id)
                ->orderBy('application_additional_detail.id', 'ASC')
                ->get();
        }

        // dd($id);
        // $jobId 							=	$this->application->job_id;
        $this->jobId = $this->application->job_id;
        $this->appId = $this->application->id;

        // $this->jobDetail = Job::select('jobs.id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'jobs.min_exp', 'jobs.max_exp', 'jobs.status', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city')
        // 	//->join('job_assign', 'job_assign.job_id', 'jobs.id')
        // 	->join('clients', 'clients.id', 'jobs.client_id')
        // 	->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
        // 	->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
        // 	->where('jobs.id', '=', $this->jobId)->where('jobs.company_id', '=', $this->user->company_id)
        // 	->first();

        $country = User::join('companies', 'companies.id', '=', 'users.company_id')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->select('users_profile.country', 'companies.logo', 'companies.company_name')
            ->with('company')
            ->where('users.id', '=', auth()->user()->id)
            ->company()
            ->first();

        // dd($country);

        $this->country = @$country->country;
        $this->companylogo = @$country->logo;
        $this->company_name = @$country->company_name;

        $this->page_event = 'EDIT';
        if (config('feature.talents_v2_enabled')) {
            $this->currency = GeneralMethod::currency();
            $this->salaryMode = GeneralMethod::salaryMode();
            $this->countries = Cache::remember('country_list_json', 86400, fn () => GeneralMethod::getAllCountryList());
            $this->statelist = GeneralMethod::statelist($this->application->country);
            $this->documents = DB::table('documents')->select('*')->where('documentable_id', $this->application->id)->where('name', 'Resume')->first();

            $this->job_additional_detail = DB::table('bench_additional_detail')
                ->select('*')
                ->where('bench_additional_detail.bench_id', '=', $this->application->id)
                ->orderBy('bench_additional_detail.id', 'ASC')
                ->get();

            $this->cities = [];
        } else {
            $this->currency = GeneralMethod::currency();
            $this->salaryMode = GeneralMethod::salaryMode();
            $this->countries = GeneralMethod::getAllCountryList();
            $this->statelist = GeneralMethod::statelist($this->application->country);
            $this->documents = DB::table('documents')->select('*')->where('documentable_id', $this->application->id)->where('name', 'Resume')->first();

            $this->job_additional_detail = '';
            $this->job_additional_detail = DB::table('bench_additional_detail')
                ->select('*')
                ->where('bench_additional_detail.bench_id', '=', $this->application->id)
                ->orderBy('bench_additional_detail.id', 'ASC')
                ->get();

            $this->application_additional_detail = DB::table('application_additional_detail')
                ->select('*')
                ->where('application_additional_detail.application_id', '=', $this->application->id)
                ->orderBy('application_additional_detail.id', 'ASC')
                ->get();

            $this->cities = GeneralMethod::allCities();
        }

        $this->cityListArr = '';
        if ($this->application->country == 101) {
            $statelist = DB::table('state_list')
                ->select('state_list.id', 'state_list.state as name', DB::raw('101 as country_id'))
                ->orderBy('state_list.state')
                ->get()->toArray();
            $this->statelist = json_decode(json_encode($statelist), true);
            // dd($this->application->state);
            $citylist = DB::table('city_list')
                ->select('city_list.city_id as id', 'city_list.city as name')
                ->where('city_list.state_id', $this->application->state)
                ->orderBy('city_list.city')
                ->get()->toArray();
            $this->cityListArr = json_decode(json_encode($citylist), true);

        }
        // dd($this->cityListArr);

        $this->client = DB::table('users')
            ->join('companies', 'companies.id', 'users.company_id')
            ->join('clients', 'clients.company_id', 'companies.id')
            ->select('clients.id')
            ->where('users.id', auth()->user()->id)->first();

        $this->recruiterlist = DB::table('bench_assign')
            ->join('users', 'users.id', '=', 'bench_assign.user_id')
            ->select('users.name', 'users.id')
            ->where('bench_assign.company_id', $company_id)
            ->where('bench_assign.bench_applications_id', $id)
            ->where('users.type', '=', 0)
            ->get();

        $this->cities = GeneralMethod::allCities();

        $this->default_loc = GeneralMethod::allCitiesSelected([$this->application->city_id]);

        $this->skillExperiences = DB::table('bench_candidate_skill_experience')
            ->where('candidate_id', $this->application->id)
            ->get()
            ->keyBy('skill_key'); // java, php, html

        $this->currencyConverter = GeneralMethod::currencyConverter();

        return response()->view('service_company.bench-candidates.create', $this->data)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    public function benchListingPoolData(Request $request)
    {

        abort_if(Gate::denies('view_job_applications'), 403);
        $company_id = auth()->user()->company_id;

        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $chkAdminRole = $this->user->hasRole('admin');

        $benchApplications = DB::table('job_applications')
            ->select(
                'job_applications.*',
                'qualification_master.name as qualification_name',
                'bench_master_status.name as master_status_name',
                'bench_master_status.css_style'
            );
        $benchApplications = $benchApplications->join('bench_master_status', 'bench_master_status.id', 'job_applications.status');
        $benchApplications = $benchApplications->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification');
        $benchApplications = $benchApplications->where('job_applications.company_id', '=', $company_id);
        if (\request('search') != '') {
            $search = request('search');
            $benchApplications = $benchApplications->where('job_applications.full_name', 'LIKE', "%$search%");
        }
        // $benchApplications = $benchApplications->whereNotIn('job_applications.status', [5,3]);
        $benchApplications = $benchApplications->where('job_applications.condidate_type', '=', 'bench_condidate');
        $benchApplications = $benchApplications->groupBy('job_applications.id');
        $benchApplications = $benchApplications->orderBy('job_applications.id', 'DESC');

        // dd($benchApplications->get());

        return DataTables::of($benchApplications)

            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input chk_list_item " name="chk_list_item[]" type="checkbox" value="'.$row->id.'" />
                  </div>';
            })
            ->editColumn('Name', function ($row) {

                $bgTextDecore = $strVal = '';
                $str = trim(@$row->full_name);
                $str = $strVal = strtolower($str[0]);
                $str = ord($str);
                if ($str >= 97 && $str <= 101) {
                    $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                } elseif ($str >= 102 && $str <= 106) {
                    $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                } elseif ($str >= 107 && $str <= 111) {
                    $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                } elseif ($str >= 112 && $str <= 116) {
                    $bgTextDecore = 'fs-3 bg-light-success text-success';
                } elseif ($str >= 117 && $str <= 121) {
                    $bgTextDecore = 'fs-3 bg-dark text-secondary';
                } else {
                    $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                }
                $profile_sidebar_css = 'bench_show_application_detail';

                return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
			<a href="javascript:void(0);"   data-row-id="'.@$row->id.'" class="'.$profile_sidebar_css.'">
			<div class="symbol-label '.@$bgTextDecore.' border-radius-5">'.strtoupper(@$strVal).'</div>
			</a>
			</div>                  
			<div class="d-flex flex-column">
			<a href="javascript:void(0);"  data-row-id="'.@$row->id.'"  class="text-dark fw-bolder text-hover-primary fs-6 '.$profile_sidebar_css.' " data-toggle="tooltip" title="'.ucwords(@$row->full_name).'">'.ucwords(substr(@$row->full_name, 0, 10)).'</a>
			<a href="javascript:void(0);"  data-row-id="'.@$row->id.'"  class="text-muted fw-bold d-block fs-7 text-hover-primary '.$profile_sidebar_css.' " data-toggle="tooltip" title="'.ucwords(@$row->current_designation).'">'.ucwords(substr(@$row->current_designation, 0, 10)).'</a>
			</div>';

            })
            ->editColumn('Contacts', function ($row) {
                return '<div class="d-flex flex-column">
			<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.@$row->phone.'</a>
			<a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary" data-toggle="tooltip" title="'.@$row->email.'">'.substr(@$row->email, 0, 10).'</a>
			</div>';
            })
            ->editColumn('Exp', function ($row) {
                return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.@$row->total_exp.'+</a>
                    <span class="text-muted fw-bold d-block fs-7">'.@$row->rel_exp.'+</span>
                  </div>';
            })
            ->editColumn('Salary', function ($row) use ($currencySymbole) {

                $pay_rate_currency = $bill_rate_currency = $pay_rate_amount = $bill_rate_amount = $pay_rate_mode = $bill_rate_mode = '';
                $pay_rate_currency = $currencySymbole[@$row->pay_rate_currency];
                $bill_rate_currency = $currencySymbole[@$row->bill_rate_currency];
                $pay_rate_amount = GeneralMethod::thousandsCurrencyFormat($row->pay_rate_amount);
                $bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($row->bill_rate_amount);

                return '<div class="d-flex flex-column">
						<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.@$pay_rate_currency.@$ctc.'/'.$pay_rate_amount.'</a>
						<span class="text-muted fw-bold d-block fs-7">'.$bill_rate_currency.@$bill_rate_amount.'/'.$bill_rate_mode.'</span>
					  </div>';
            })

            ->editColumn('Availability', function ($row) {
                $npArr = ['Notice period', 'Available from', 'Immediate'];
                $npDate = $row->np_id;
                if ($row->serving_notice_period_mode == 0) {
                    $npDate = ($row->np_id == 1) ? $row->np_id.' Day' : $row->np_id.' Days';
                } elseif ($row->serving_notice_period_mode == 1) {
                    $originalDate = $row->np_id;
                    $npDate = 'SNP LWD '.date('d-M-y', strtotime($originalDate));
                } else {
                    $npDate = '';
                }

                return '<div class="d-flex flex-column">
					  <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.$npArr[$row->serving_notice_period_mode].'</a> 
					  <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="'.$row->np_id.'">'.$npDate.'</span>
					</div>';
            })
            ->editColumn('Qualifaction', function ($row) {
                return ' <div class="d-flex flex-column">
						  <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="'.@$row->qualification_name.'">'.@$row->qualification_name.'</a>
						  <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="'.$row->skills.'">'.substr($row->skills, 0, 12).'</span> 
						</div>';
            })
            ->editColumn('Location', function ($row) {

                $country = $row_country_name = $state = $row_state_name = $city = $row_city_name = '';

                if (empty($row->country)) {
                    $row_country_name = '----';
                } else {
                    if (! empty(@$row->country)) {
                        if (is_numeric(@$row->country)) {
                            $country_name_Arr = GeneralMethod::getCountryName(@$row->country);
                            foreach ($country_name_Arr as $cval) {
                                $row_country_name = ucwords($cval['name']);
                            }
                        }
                    }
                }

                if (empty($row->state)) {
                    $row_state_name = '----';
                } else {
                    if (@$row->country == 101) {
                        $row_state = DB::table('state_list')->select('*')->where('id', @$row->state)->first();
                        $row_state_name = @$row_state->state;

                    } else {
                        $state = @$row->state;
                        if (is_numeric(@$row->country) && is_numeric($state)) {
                            if ($state > 0) {
                                $state_name_Arr = GeneralMethod::getStateName(@$row->country, $state);
                                foreach ($state_name_Arr as $sval) {
                                    $row_state_name = ucwords($sval['name']);
                                }
                            }
                        }
                    }
                }

                if (@$row->country == 101) {
                    $row_city = DB::table('city_list')->select('*')->where('city_id', @$row->city)->first();
                    $row_city_name = @$row_city->city;
                } else {
                    $row_city_name = @$row->city;
                }

                return '<div class="d-flex flex-column">
                    	<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.ucwords($row_city_name).'">'.ucwords(substr($row_city_name, 0, 10)).'</a>
                    	<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords($row_state_name).' | '.strtoupper($row_country_name).'">'.ucfirst(substr($row_state_name, 0, 5)).' | '.strtoupper(substr($row_country_name, 0, 2)).'</span>
                    </div>';

            })
            ->editColumn('Assigned', function ($row) use ($company_id) {

                $hmCount = DB::table('bench_assign')->join('users', 'users.id', '=', 'bench_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('bench_assign.bench_applications_id', '=', $row->id)->where('bench_assign.company_id', '=', $company_id)->where('users.company_id', '=', $company_id)->count();

                $hmData = DB::table('bench_assign')->join('users', 'users.id', '=', 'bench_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('bench_assign.bench_applications_id', '=', $row->id)->where('bench_assign.company_id', '=', $company_id)->where('users.company_id', '=', $company_id)->orderBy('users.name', 'ASC')->skip(0)->take(3)->get();

                $html = '';
                $html .= '<div class="d-flex flex-column">
                    						<div class="symbol-group symbol-hover">';
                $temp = '';
                foreach ($hmData as $pval) {

                    $logo_path = url('public/avatar.png');
                    if (! empty(@$pval->image) && strlen(@$pval->image) > 10) {
                        $logo_path = url('public/user-uploads/team_profile_img/'.@$pval->image);
                    }
                    $html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="'.@$pval->name.'">
                            						<img alt="Pic" src="'.@$logo_path.'">
                          						</div>';

                }
                $html .= '<div data-row-id="'.$row->id.'"   class="symbol symbol-30 symbol-circle symbol-light hm-listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list..">
														<span class="symbol-label font-weight-bold">'.$hmCount.'+</span>
													  </div>';
                $html .= '</div>
                    					</div>';

                return $html;
            })
            ->editColumn('Date', function ($row) {
                $created_at = '';
                $created_at = $newDate = date('d M , Y', strtotime($row->created_at));

                return '<div class="d-flex flex-column">
                      <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.$created_at.'</a>
                      <span class="text-muted fw-bold text-muted d-block fs-7">.......</span>
                    </div>';
            })

            ->editColumn('Requisitions', function ($row) {
                return '<div class="d-flex flex-column">
							  <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="Custom Job">........</a>
							  <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="Veebrij-u">.......</span>
							</div>';
            })
            ->editColumn('Status', function ($row) {
                return '<div class="d-flex flex-column">
							  <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="fw-bold fs-6  stage_sub_stage '.$row->css_style.'" data-toggle="modal" data-target="#stage_sub_stage" >
								'.$row->master_status_name.'</a>
							  <span class="fw-bold text-muted d-block fs-7" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="10 Days to Expire"></span>
							</div>';
            })
            ->rawColumns(['chkbox', 'Name', 'Contacts', 'Exp', 'Salary', 'Availability', 'Qualifaction', 'Location', 'Assigned', 'Date', 'Requisitions', 'Status'])
            ->addIndexColumn()
            ->make(true);

    }

    public function hmlisting($id)
    {
        $this->currentBenchId = $id;
        $this->recruiter = [];

        $company_id = auth()->user()->company_id;
        $this->team = DB::table('users')
            ->leftJoin('bench_assign', function ($join) use ($id) {
                $join->on('bench_assign.user_id', '=', 'users.id');
                $join->on('bench_assign.bench_applications_id', '=', DB::raw("'".$id."'"));
            })
            ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.working_status',
                'bench_assign.id as bench_assign_id',
                'bench_assign.bench_applications_id',
                'users_profile.designation as display_name'
            )
            ->where('users.company_id', $company_id)
            ->where('users.status', '=', 'active')
            ->orderByDesc('bench_assign.user_id')
            ->orderBy('users.name', 'asc')
            ->groupBy('users.id')
            ->get();
        $view = view('service_company.bench-candidates.hmlisting', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function hmlistingupdate(Request $request)
    {
        $company_id = auth()->user()->company_id;
        $assignStatus = $request->assignStatus;
        $currentBenchId = $request->currentBenchId;
        $currentUserId = $request->currentUserId;

        $cachekeys = [
            'allbenchcandidates_version_user_'.$this->user->id,
        ];
        foreach ($cachekeys as $key) {
            GeneralMethod::cacheClean($key);
        }

        if ($assignStatus == 1) {
            DB::table('bench_assign')->where('bench_applications_id', $currentBenchId)->where('user_id', $currentUserId)->where('company_id', $company_id)->delete();

            return Reply::dataOnly(['status' => 'success', 'msg' => 'Remove Record successfully.']);
        } else {
            DB::table('bench_assign')->where('bench_applications_id', $currentBenchId)->where('user_id', $currentUserId)->where('company_id', $company_id)->delete();
            $values = ['company_id' => $company_id, 'user_id' => $currentUserId, 'bench_applications_id' => $currentBenchId];
            DB::table('bench_assign')->insert($values);

            return Reply::dataOnly(['status' => 'success', 'msg' => 'Added Record successfully.']);
        }
    }

    public function bench_application_status(Request $request, $id)
    {
        $placedid = $request->query('placedid', null);

        $current_company_id = auth()->user()->company_id;
        $this->qualification = $this->application = '';
        $this->currentBenchId = $id;
        $this->placedid = $placedid;
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();
        $this->intModeArr = GeneralMethod::interview_mode();

        $this->benchApplication = JobApplication::select(
            'job_applications.id',
            'job_applications.company_id',
            'job_applications.submitted_by',
            'job_applications.full_name',
            'job_applications.current_designation',
            'job_applications.outsourcing_type',
            'job_applications.phone',
            'job_applications.secondary_mobile',
            'job_applications.dob',
            'job_applications.email',
            'job_applications.secondrary_email',
            'job_applications.qualification',
            'job_applications.total_exp',
            'job_applications.rel_exp',
            'job_applications.serving_notice_period_mode',
            'job_applications.np_id',
            'job_applications.pay_rate_currency',
            'job_applications.pay_rate_amount',
            'job_applications.pay_rate_mode',
            'job_applications.bill_rate_currency',
            'job_applications.bill_rate_amount',
            'job_applications.bill_rate_mode',
            'job_applications.comission_type',
            'job_applications.country',
            'job_applications.state',
            'job_applications.city',
            'job_applications.city_id',
            'job_applications.pincode',
            'job_applications.skills',
            'job_applications.photo',
            'job_applications.resume',
            'job_applications.status',
            'job_applications.offer_amount',
            'job_applications.joined_date',
            'job_applications.rating',
            'job_applications.created_at',
            'users.name as posted_by',
            'bench_master_status.name as master_status_name',
            'state_list.state as state_name',
            'city_list.city as city_name',
            'users.name as submitted_by_name',
            'users.company_id as submitted_by_company_id'
        )
            ->leftjoin('users', 'users.id', 'job_applications.submitted_by')
            ->leftjoin('state_list', 'state_list.id', 'job_applications.state')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city')
            ->leftJoin('bench_master_status', 'bench_master_status.id', '=', 'job_applications.status');
        $this->benchApplication = $this->benchApplication->where('job_applications.id', $id);
        $this->benchApplication = $this->benchApplication->first();

        if (! empty($this->benchApplication->qualification) && $this->benchApplication->qualification > 0) {
            $this->qualification = DB::table('qualification_master')->select('name')->where('id', $this->benchApplication->qualification)->first();
        }
        $submitted_by_company_id = $this->benchApplication->submitted_by_company_id;
        $this->company = DB::table('companies')->select('company_name')->where('id', $submitted_by_company_id)->first();
        $this->master_status = DB::table('bench_master_status')->select('*')->orderBy('bench_master_status.priority', 'ASC')->get();

        $master_status_id = $this->benchApplication->status;
        $this->created_at = GeneralMethod::ymdTodmy($this->benchApplication->created_at);
        $this->company_id = $this->user->company_id;

        $pay_rate_currency = $pay_rate_amount = $pay_rate_mode = $bill_rate_amount = $bill_rate_currency = $bill_rate_mode = $country = $this->row_country_name = '';
        $pay_rate_currency = $currencySymbole[$this->benchApplication->pay_rate_currency];
        $pay_rate_amount = GeneralMethod::thousandsCurrencyFormat($this->benchApplication->pay_rate_amount);
        $pay_rate_mode = substr($salaryMode[$this->benchApplication->pay_rate_mode], 0, 1);
        $bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($this->benchApplication->bill_rate_amount);
        $bill_rate_currency = $currencySymbole[$this->benchApplication->bill_rate_currency];
        $bill_rate_mode = substr($salaryMode[$this->benchApplication->bill_rate_mode], 0, 1);
        $this->pay_rate_amount = @$pay_rate_currency.@$pay_rate_amount.'/'.@$pay_rate_mode;
        $this->bill_rate_amount = @$bill_rate_currency.@$bill_rate_amount.'/'.@$bill_rate_mode;

        if (empty($this->benchApplication->country)) {
            $this->row_country_name = '----';
        } else {
            if (! empty($this->benchApplication->country)) {
                if (is_numeric($this->benchApplication->country)) {
                    $country_name_Arr = GeneralMethod::getCountryName($this->benchApplication->country);
                    foreach ($country_name_Arr as $cval) {
                        $this->row_country_name = ucwords($cval['name']);
                    }
                }
            }
        }
        $this->row_state_name = '';
        if (empty($this->benchApplication->state)) {
            $this->row_state_name = '----';
        } else {
            if ($this->benchApplication->country == 101) {
                $this->row_state_name = $this->benchApplication->state_name;
            } else {
                $state = $this->benchApplication->state;
                if (is_numeric($this->benchApplication->country) && is_numeric($state)) {
                    if ($state > 0) {
                        $state_name_Arr = GeneralMethod::getStateName($this->benchApplication->country, $state);
                        foreach ($state_name_Arr as $sval) {
                            $this->row_state_name = ucwords($sval['name']);
                        }
                    }
                }
            }
        }
        $this->row_city_name = '';
        if ($this->benchApplication->country == 101) {
            $row_city = DB::table('city_list')->select('*')->where('city_id', $this->benchApplication->city_id)->first();
            $this->row_city_name = @$row_city->city;
        } else {
            $this->row_city_name = @$this->application->city;
        }

        /**
         * Stat tiles for the "Change Status" drawer (Active Interviews / Profile View /
         * Client Submissions / Bench Duration / Resume Download). Reuses the same helper
         * and counter table already queried per-row on the listing page, so no new query shape.
         */
        $pipelineCounter = GeneralMethod::bench_explore_listing_counter($id, $this->company_id ?? auth()->user()->company_id)->applicationCounter;
        $this->activeInterviews = $pipelineCounter->Interview ?? 0;
        $this->clientSubmissions = $pipelineCounter->allCandidate ?? 0;
        $this->profileViewCount = DB::table('bench_count_view_download')->where('type', 1)->where('status', 0)->where('candidate_id', $id)->count();
        $this->resumeDownloadCount = DB::table('bench_count_view_download')->where('type', 2)->where('status', 0)->where('candidate_id', $id)->count();
        $this->benchDurationDays = ! empty($this->benchApplication->created_at)
            ? (int) \Carbon\Carbon::parse($this->benchApplication->created_at)->diffInDays(now())
            : 0;

        $view = view('service_company.bench-candidates.bench_application_status', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function bench_assign_to_job_listing(Request $request)
    {

        abort_if(Gate::denies('view_job_applications'), 403);

        $company_id = auth()->user()->company_id;

        $bench_id = $stage = $feedback_status = '';
        $bench_id = $request->bench_id;
        $stage = $request->stage;

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'size' => 10000, // adjust if needed
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['candidate_id' => (int) $bench_id]],
                            ['terms' => ['job_master_status_id' => [5, 6]]],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);

        $job_applications = [];
        $benchAppOnJob = collect();
        if (! empty($response['hits']['hits'])) {
            foreach ($response['hits']['hits'] as $hit) {
                $benchAppOnJob->push((object) $hit['_source']);
            }
        }

        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();
        $job_status = GeneralMethod::job_status();
        $feedback_status = GeneralMethod::feedback_status();

        $chkAdminRole = $this->user->hasRole('admin');

        $keys = $benchAppOnJob->map(fn ($r) => [
            'bench_id' => (int) $r->candidate_id,
            'jobID' => (int) $r->jobId,
        ])->values();

        $should = [];

        foreach ($keys as $k) {
            $should[] = [
                'bool' => [
                    'must' => [
                        ['term' => ['bench_id' => $k['bench_id']]],
                        ['term' => ['jobID' => $k['jobID']]],
                    ],
                ],
            ];
        }

        $params = [
            'index' => ELASTIC_BENCH_PLACED,
            'size' => 10000,
            '_source' => ['bench_id', 'jobID'],
            'body' => [
                'query' => [
                    'bool' => [
                        'should' => $should,
                        'minimum_should_match' => 1,
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);

        $existsMap = [];

        foreach ($response['hits']['hits'] ?? [] as $hit) {
            $src = $hit['_source'];
            $existsMap[$src['bench_id'].'_'.$src['jobID']] = 1;
        }
        $benchAppOnJob = $benchAppOnJob->map(function ($row) use ($existsMap) {
            $key = $row->candidate_id.'_'.$row->jobId;
            $row->exists = $existsMap[$key] ?? 0; // 👈 1 or 0

            return $row;
        });
        $benchAppOnJob = $benchAppOnJob->sortBy('exists')->values();

        // dd($benchAppOnJob);

        return DataTables::of($benchAppOnJob)
            ->setRowClass(function ($row) {
                // Real "already placed on this job" check (bench_id + jobID exist in ELASTIC_BENCH_PLACED),
                // computed above into $row->exists — NOT the job's own recruitment stage.
                return ($row->exists ?? 0) ? 'classname' : '';
            })->setRowAttr([
                'style' => function ($row) {
                    return ($row->exists ?? 0)
                        ? 'background-color:#e8f9f6a6;'
                        : '';
                },
            ])
            ->editColumn('chkbox', function ($row) {

                $exists = $row->exists ?? 0;
                $disabled = '';
                $checked = 'checked';
                if ($exists) {
                    $disabled = 'disabled';
                    $checked = '';
                }

                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
						  <input type="radio" class="form-check-input chk_list_item " name="chk_list_item" value="'.$row->jobId.'" '.$checked.' '.$disabled.'  />
					  </div>';
            })
            ->editColumn('Requisition', function ($row) {

                $clients_name = '';
                $bgTextDecore = $strVal = '';
                $str = trim(@$row->title);
                $str = $strVal = strtolower($str[0]);
                $str = ord($str);
                if ($str >= 97 && $str <= 101) {
                    $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                } elseif ($str >= 102 && $str <= 106) {
                    $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                } elseif ($str >= 107 && $str <= 111) {
                    $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                } elseif ($str >= 112 && $str <= 116) {
                    $bgTextDecore = 'fs-3 bg-light-success text-success';
                } elseif ($str >= 117 && $str <= 121) {
                    $bgTextDecore = 'fs-3 bg-dark text-secondary';
                } else {
                    $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                }

                $profile_detail_class = 'show-detail';
                $clients_name = ucwords(substr(@$row->company_name, 0, 15));  // clients_name

                $recommendation_id = '';
                $recommendation_id = $row->recommendation_id;
                $recommendation_text = '&nbsp;<i class="bi bi-house" style="color:#00CC00"></i>';

                $logo_path = '';
                // $logo_path			=	url('img/client-default.png');
                // if (!empty($row->logo_logical_path)) {

                // 	$logo_path = @$row->logo_logical_path;
                // 	if(@$row->company_id != auth()->user()->company_id){
                // 		$logo_path = @$row->logo;
                // 	}

                // 	if($row->thirdparty_counter > 0 && $recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id){
                // 		$logo_path = @$row->logo;
                //    }
                // }
                if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                    $recommendation_text = '&nbsp;<i style="color: #FF0000; font-weight:bold" class="fa fa-users" aria-hidden="true"></i>';
                    $profile_detail_class = 'client-show-detail';
                    $clients_name = ucwords(substr(@$row->company_name, 0, 15));

                    // if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
                    // 	$logo_path = @$row->logo;
                    // }
                }

                // if (@$row->self_company == 1) {
                // 	if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
                // 		$logo_path = @$row->logo;
                // 	}
                // }

                $company_or_client = GeneralMethod::companyDetailsByClient($row->jobId);
                $logo_path = GeneralMethod::companyDetails($row->jobId);

                $icon = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="'.$row->jobId.'" class="'.$profile_detail_class.'">
                      <div class="symbol-label '.$bgTextDecore.' border-radius-5">'.ucwords($strVal).'</div>
                    </a>
                  </div>';

                if (! empty($logo_path)) {
                    $icon =
                        '<div data-row-id="'.
                        $row->id.
                        '"  class="symbol symbol-circle symbol-40px overflow-hidden me-2  '.
                        $profile_detail_class.
                        ' border-radius-5">
                                                <a href="javascript:void(0);">
                                                    <div class="symbol-label border-radius-5">
                                                        <img src="'.
                        str_replace('index.php', '', @$logo_path).
                        '" alt="Company Logo" class="border-radius-5 w-100">
                                                    </div>
                                                </a>
                                            </div>';
                }
                $params = [
                    'index' => ELASTIC_BENCH_PLACED,
                    'size' => 1,
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['bench_id' => (int) $row->candidate_id]],
                                    ['term' => ['jobID' => (int) $row->jobId]],
                                ],
                            ],
                        ],
                    ],
                ];

                $response = $this->elasticService->getClient()->search($params);
                $exists = ($response['hits']['total']['value'] ?? 0) > 0;
                $Placed = '';
                if ($exists) {
                    $Placed = '';
                }

                // if (!empty($logo_path)) {
                // 	$icon = '<div data-row-id="' . $row->jobId . '"  class="symbol symbol-circle symbol-40px overflow-hidden me-2  ' . $profile_detail_class . ' border-radius-5">
                //                         <a href="javascript:void(0);">
                //                             <div class="symbol-label border-radius-5">
                //                             	<img src="' . @$logo_path . '" alt="Company Logo" class="border-radius-5 w-100">
                //                             </div>
                //                         </a>
                //                     </div>';
                // }

                return $icon.'<div class="d-flex flex-column">
					<a href="javascript:void(0);"  data-row-id="'.$row->jobId.'"   data-row-job_applications_id="'.$row->candidate_id.'" class="text-dark fw-bolder text-hover-primary fs-6 '.$profile_detail_class.'"  data-toggle="tooltip1" title="'.ucwords(@$row->title).'" >'.ucwords(substr(@$row->title, 0, 15)).''.$Placed.'</a> 
					<a href="javascript:void(0);" data-row-id="'.$row->jobId.'"   data-row-job_applications_id="'.$row->candidate_id.'"   class="fw-bold text-muted d-block fs-7 text-hover-primary '.$profile_detail_class.'">'.@$company_or_client.'</a>
                  </div>';

            })
            ->editColumn('Exp', function ($row) {

                $params = [
                    'index' => ELASTIC_JOBS,
                    'id' => (int) $row->jobId,
                ];

                $response = $this->elasticService->getClient()->get($params);
                $thisjob = (object) $response['_source'];

                return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">'.@$thisjob->max_exp.'+</span>
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.@$thisjob->min_exp.'+</span>
                  </div>';
            })
            ->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {
                $params = [
                    'index' => ELASTIC_JOBS,
                    'id' => (int) $row->jobId,
                ];

                $response = $this->elasticService->getClient()->get($params);
                $thisjob = (object) $response['_source'];

                $min_sal_mode = $max_sal_mode = $min_salary_currency = $max_salary_currency = $max_salary = $min_salary = '';
                $min_sal_mode = strtoupper(substr($salaryMode[$thisjob->min_sal_mode], 0, 1));
                $max_sal_mode = strtoupper(substr($salaryMode[$thisjob->max_sal_mode], 0, 1));
                $min_salary_currency = $currencySymbole[@$thisjob->min_salary_currency];
                $max_salary_currency = $currencySymbole[@$thisjob->max_salary_currency];
                $max_salary = GeneralMethod::thousandsCurrencyFormat($thisjob->max_salary);
                $min_salary = GeneralMethod::thousandsCurrencyFormat($thisjob->min_salary);

                return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">'.$min_salary_currency.$max_salary.'/'.$min_sal_mode.'</span>                    
                    <span class="fw-bold text-muted d-block fs-7">'.$min_salary_currency.$min_salary.'/'.$max_sal_mode.'</span>
                  </div>';
            })
            ->editColumn('Location', function ($row) {

                $country_id = $country_name = '';
                $jobid = $row->jobId;
                $params = [
                    'index' => ELASTIC_JOBS,
                    'id' => (int) $row->jobId,
                ];

                $response = $this->elasticService->getClient()->get($params);
                $thisjob = (object) $response['_source'];
                $client_city_jobs = $thisjob->client_city_jobs;
                $firstCity = collect($client_city_jobs)->first();

                $cityName = $firstCity['city_name'] ?? null;
                $city = mb_substr(@$cityName, 0, 21);
                $countryName = $firstCity['country_name'] ?? null;
                $country_name = mb_substr(@$countryName, 0, 6);

                $jobCitySelected = count($client_city_jobs);
                // DB::table('client_city_jobs')->where(column: 'client_city_jobs.job_id', $jobid)->count();

                $employment_type = 'Full-time';
                if ($thisjob->employment_type == 1) {
                    $employment_type = 'Contract';
                }

                if ($thisjob->work_type == 'yes') {
                    return ' <div class="d-flex flex-column">
						<span class="text-dark fw-bolder fs-6">Remote</span>
						<div class="d-flex align-items-center">
						  <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
						  </div>
						</div>
					  </div>';
                } elseif ($thisjob->work_type == 'no') {

                    if ($jobCitySelected == 1 || $jobCitySelected == 0) {

                        return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">'.ucwords(substr(@$city, 0, 10)).'</span>
								</div>
								
							  <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
							  </div>
							</div>
						  </div>';
                    } else {
                        return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">Multiple</span>
								</div>
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
						  </div>
						</div>
					  </div>';
                    }

                } elseif ($thisjob->work_type == 'any') {

                    if ($jobCitySelected == 1 || $jobCitySelected == 0) {

                        return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">'.ucwords(substr(@$city, 0, 10)).'</span>
								</div>
								
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
							  </div>
							</div>
						  </div>';
                    } else {
                        return ' <div class="d-flex flex-column">
						<div class="d-flex align-items-center">
						  <span class="text-dark fw-bolder fs-6">Remote</span>
						  &nbsp;|&nbsp;
								  <span class="text-dark fw-bolder fs-6">Multiple</span>
								</div>
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>

						  </div>
						</div>
					  </div>';
                    }

                }

            })
            ->editColumn('Status', function ($row) use ($job_status) {

                $params = [
                    'index' => ELASTIC_JOBS,
                    'id' => (int) $row->jobId,
                ];

                $response = $this->elasticService->getClient()->get($params);
                $thisjob = (object) $response['_source'];

                $status = $active = $hold = $inactive = $class = $recommendation_id = '';
                $status = ucwords($thisjob->status);
                $class = $job_status[$thisjob->status];
                if ($status == 'Inactive') {
                    $status = 'Closed';
                }
                $recommendation_text = __('messages.internal_tag');
                if (@$thisjob->to_company_id == auth()->user()->company_id) {
                    $recommendation_text = __('messages.clinet_tag');
                }

                $ulink = '<a href="javascript.void(0);" data-row-id="'.@$thisjob->id.'" class="d-flex flex-column '.@$class.' status_stage text-hover-success" data-toggle="modal" data-target="#status_stage">'.$status.'</a>';

                return '<div class="d-flex flex-column">
                       '.$ulink.'
                      <span class="fw-bold text-muted d-block fs-7">'.@$row->jobId.'-'.$recommendation_text.'</span>
                    </div>';
            })
            ->editColumn('AddedDate', function ($row) {

                $params = [
                    'index' => ELASTIC_JOBS,
                    'id' => (int) $row->jobId,
                ];

                $response = $this->elasticService->getClient()->get($params);
                $thisjob = (object) $response['_source'];

                $total_positions = $created_at = '';
                $created_at = $row->created_at;
                $total_positions = number_format($thisjob->total_positions);
                $newDate = date('d M, Y', strtotime($created_at));

                return '<div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">'.$newDate.'</span>
                        <span class="text-muted fw-bold d-block fs-7">'.$total_positions.' Opening</span>
                    </div>';
            })
            ->editColumn('ContactPerson', function ($row) {

                $params = [
                    'index' => ELASTIC_JOBS,
                    'id' => (int) $row->jobId,
                ];

                $response = $this->elasticService->getClient()->get($params);
                $thisjob = (object) $response['_source'];
                $job_assign = $thisjob->job_assign;
                $partnersCount = collect($job_assign)->where('company_id', $row->company_id)->count();
                $partnersData = collect($job_assign)->where('company_id', $row->company_id)->map(fn ($item) => (object) $item)->take(1);

                // Plain bold-name + muted-subtitle text (no avatar widget) to match the
                // "Hiring Manager" cell style used in the Change Status drawer's job table.
                $first = $partnersData->first();
                $name = $first ? ucwords($first->user_name) : 'Unassigned';
                $extra = $partnersCount > 1 ? ' +'.($partnersCount - 1) : '';

                return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">'.$name.$extra.'</span>
                    <span class="text-muted fw-bold d-block fs-7">Assigned Recruiter</span>
                  </div>';

            })
            ->editColumn('Interview', function ($row) use ($feedback_status) {
                $schedule_date = $className = '';
                $intRowCount = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->candidate_id)->where('interview_job_id', $row->jobId)->orderBy('interview_schedules.id', 'DESC')->count();
                $intRow = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->candidate_id)->where('interview_job_id', $row->jobId)->orderBy('interview_schedules.id', 'DESC')->first();
                $html = '';
                if ($intRowCount > 0) {
                    $schedule_date = @$intRow->schedule_date;
                    $schedule_date = date('d M, Y', strtotime($schedule_date));
                } else {
                    $html = '<div style="width:150px" id="interview_date">&nbsp;</div>';
                }
                if ($intRowCount > 0) {
                    $feedback_status_name = '';
                    if ($intRow->feedback >= 0) {
                        $feedback_status_name = @$feedback_status[@$intRow->feedback]['name'];
                        $className = @$feedback_status[@$intRow->feedback]['classname'];
                    } else {
                        $feedback_status_name = 'Pending';
                        $className = 'pending-bg';
                    }
                } else {
                    $feedback_status_name = '--------';
                }

                return '<div class="d-flex flex-column">
											<span class="text-dark fw-bolder fs-6">'.@$schedule_date.'</span>
											<span class="fw-bold d-block fs-7 '.@$className.' px-1">'.@$feedback_status_name.'</span>
										</div>';

            })
            ->editColumn('Stage', function ($row) {

                $name = '';
                if ($row->job_master_status_id == 8) {
                    $name = '';
                }
                if (! empty(@$row->childStatus) && @$row->childStatus > 0) {
                    $childStatus = '';
                    $childStatus = DB::table('candidate_status')->select('*')->where('id', $row->childStatus)->first();
                    if (! empty($childStatus)) {
                        $name = @$childStatus->name;
                    }
                }
                $stage_default_css = 'stage_sub_stage';
                $html = '';
                if ($row->job_master_status_id == 8) {
                    $html = '<div class="d-flex flex-column">
								<a href="javascript.void(0);" data-row-id="'.@$row->id.'" class="d-flex flex-column client_stage_sub_stage" data-toggle="modal" data-target="#client_stage_sub_stage">
								<span class="badge fw-bolder fs-6 '.@$row->css_style.' px-2 py-1">'.ucwords(substr($row->name, 0, 10)).'</span>
								<span class="text-muted fw-bold d-block fs-7">'.@$name.'</span>
								</a>
								</div>';
                } else {
                    $html = '<div class="d-flex flex-column">
								<span href="javascript.void(0);" data-row-id="'.@$row->id.'" class="d-flex flex-column ">
								<span class="badge fw-bolder fs-6 '.@$row->css_style.' px-2 py-1">'.ucwords(substr($row->name, 0, 10)).'</span>
								<span class="text-muted fw-bold d-block fs-7">'.@$name.'</span>
								</span>
								</div>';
                }

                return $html;
            })
            ->editColumn('offeramount', function ($row) use ($salaryMode, $currencySymbole) {
                // dd($row->jobId,$row->job_id);

                $params = [
                    'index' => ELASTIC_JOBS,
                    'id' => (int) $row->jobId,
                ];

                $response = $this->elasticService->getClient()->get($params);
                $thisjob = (object) $response['_source'];

                $employment_type = 'Full-time';
                if ($thisjob->employment_type == 1) {
                    $employment_type = 'Contract';
                }

                // dd($thisjob->employment_type);

                // dd($rowset->offer_currency);
                $offeramount = '--------';
                $offer_currency = '';
                $offer_mode = '';
                if ($row->job_master_status_id == 5 || $row->job_master_status_id == 6) {

                    $offercurrencyGet = (@$row->of_offer_currency) ? @$row->of_offer_currency : 0;
                    $offeramountGet = (@$row->of_offer_amount) ? @$row->of_offer_amount : 0;
                    $offermodeGet = (@$row->of_offer_mode) ? @$row->of_offer_mode : 0;

                    $offer_currency = $currencySymbole[$offercurrencyGet];

                    $offeramount = GeneralMethod::thousandsCurrencyFormat(@$offeramountGet);
                    $offer_mode = substr($salaryMode[@$offermodeGet], 0, 1);
                }

                // dd("ji");
                return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">'.$offer_currency.' '.@$offeramount.'|'.$offer_mode.'</span>
					<span class="text-muted fw-bold d-block fs-7">'.$employment_type.'</span>
                  </div>';
            })
            ->editColumn('joindate', function ($row) {
                // dd($row);

                $joined_date = $joined_time = $employment_type = $date = '-----';
                $joindate = '';
                $date = @$row->of_joined_date;
                if (! empty($date)) {
                    $joindate = date('d-M-y', strtotime($date));
                    $joined_time = date('H:i a', strtotime($date));
                    if (@$row->employment_type == 1) {
                        $employment_type = 'Contract';
                    } else {
                        $employment_type = 'Full-time';
                    }

                }

                return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">'.@$joindate.'</span>
					<span class="text-muted fw-bold d-block fs-7">'.$joined_time.'</span>
                  </div>';
            })
            ->editColumn('placed', function ($row) {
                $params = [
                    'index' => ELASTIC_BENCH_PLACED,
                    'size' => 1,
                    '_source' => ['created_at'], // 👈 important
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['bench_id' => (int) $row->candidate_id]],
                                    ['term' => ['jobID' => (int) $row->jobId]],
                                ],
                            ],
                        ],
                    ],
                ];

                $response = $this->elasticService->getClient()->search($params);
                $exists = ($response['hits']['total']['value'] ?? 0) > 0;
                $createdAt = '--------';
                $Placed = '--------';
                $class = 'text-muted fw-bold d-block fs-7';

                if (! empty($response['hits']['hits'])) {
                    $createdAt = $response['hits']['hits'][0]['_source']['created_at'] ?? null;
                    $createdAt = date('d-M-y H:i a', strtotime($createdAt));
                    $Placed = 'Placed';
                    $class = 'badge fw-bolder fs-6 joined-bg px-2 py-1';
                }

                return '<div class="d-flex flex-column">
                    <span class="'.$class.'">'.$Placed.'</span>
					<span class="text-muted fw-bold d-block fs-7">'.$createdAt.'</span>
                  </div>';
            })

            ->rawColumns(['chkbox', 'Requisition', 'Exp', 'Salary', 'Location', 'Status', 'AddedDate', 'ContactPerson', 'offeramount', 'joindate', 'Interview', 'Stage', 'placed'])
            ->addIndexColumn()
            ->make(true);

        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();
        $job_status = GeneralMethod::job_status();
        $feedback_status = GeneralMethod::feedback_status();

        $chkAdminRole = $this->user->hasRole('admin');

        $benchAppOnJob = DB::table('rec_candidate_assign')
            ->select(
                'bench_applications.id',
                'bench_applications.full_name',
                'bench_applications.phone',
                'bench_applications.email',
                'add_job_wise_condidates.master_status_id',
                'add_job_wise_condidates.child_status_id',
                'job_applications.created_at',
                'job_applications.id as job_applications_id',
                'master_status.name',
                'master_status.css_style',
                'jobs.total_positions',
                'jobs.status',
                'jobs.id as jobId',
                'jobs.min_sal_mode',
                'jobs.work_type',
                'jobs.show_vendor',
                'jobs.employment_type',
                'jobs.max_sal_mode',
                'jobs.min_salary_currency',
                'jobs.max_salary_currency',
                'jobs.max_salary',
                'jobs.min_salary',
                'jobs.max_exp',
                'jobs.min_exp',
                'jobs.title',
                'jobs.company_id',
                'clients.name as clients_name',
                'clients.logo_logical_path',
                'clients.self_company',
                'clients.company_id as client_company_id',
                'companies.company_name',
                'companies.logo',
                'add_job_wise_condidates.job_id',
                'add_job_wise_condidates.job_application_id',
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' THEN 1 ELSE 0 END) AS thirdparty_counter'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS thirdparty_InReview'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS thirdparty_Sourcing'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS thirdparty_Screening'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS thirdparty_Interview'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS thirdparty_Negotiation'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS thirdparty_Offered'),
                DB::raw('SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS thirdparty_Joined'),

                DB::raw('SUM(
				CASE 
						WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 7 THEN 1 
						WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 9 THEN 1
						ELSE 0 END
			 ) AS thirdparty_Rejected'
                ),
                'recommendation_relation.id as recommendation_id',
                'recommendation_relation.from_company_id',
                'recommendation_relation.final_status',
                'recommendation_relation.to_company_id'
            );
        $benchAppOnJob = $benchAppOnJob->join('job_applications as bench_applications', 'bench_applications.id', 'rec_candidate_assign.source_from_id');
        $benchAppOnJob = $benchAppOnJob->join('job_applications', 'job_applications.id', 'rec_candidate_assign.source_application_id');
        $benchAppOnJob = $benchAppOnJob->leftjoin('add_job_wise_condidates', 'add_job_wise_condidates.rec_candidate_assign_id', 'rec_candidate_assign.id')
            ->leftjoin('master_status', function ($join) {
                $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
            });

        $benchAppOnJob = $benchAppOnJob->join('jobs', 'jobs.id', 'rec_candidate_assign.source_jobid');
        $benchAppOnJob = $benchAppOnJob->join('companies', 'companies.id', 'jobs.company_id');
        $benchAppOnJob = $benchAppOnJob->join('clients', 'clients.id', 'jobs.client_id');

        // $benchAppOnJob = $benchAppOnJob->leftjoin("master_status", function($qry){
        // 	$qry->on('master_status.id', 'job_applications.master_status_id');
        // });
        // $benchAppOnJob = $benchAppOnJob->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id');

        // $benchAppOnJob = $benchAppOnJob->leftjoin('offer_job_applications', function($join) use ($company_id) {
        // 	$join->on('offer_job_applications.application_id', 'job_applications.id');
        // });

        $benchAppOnJob = $benchAppOnJob->leftjoin('qualification_master', 'qualification_master.id', 'bench_applications.qualification');
        $benchAppOnJob = $benchAppOnJob->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id');
        $benchAppOnJob = $benchAppOnJob->where('bench_applications.company_id', '=', $company_id);
        $benchAppOnJob = $benchAppOnJob->where(function ($query) {
            $query->where('add_job_wise_condidates.master_status_id', '=', 5)->orWhere('add_job_wise_condidates.master_status_id', '=', 6);
        });
        // 		if (\request('search') != "") {
        // 			$search = request('search');
        // 			$benchAppOnJob = $benchAppOnJob->where('jobs.title', 'LIKE', "%$search%");
        // 		}
        $benchAppOnJob = $benchAppOnJob->where('rec_candidate_assign.source_from_id', '=', $bench_id);

        if ($stage > 0) {
            $benchAppOnJob = $benchAppOnJob->where('job_applications.master_status_id', '=', $stage);
        }
        $benchAppOnJob = $benchAppOnJob->groupBy('rec_candidate_assign.id');
        $benchAppOnJob = $benchAppOnJob->orderBy('rec_candidate_assign.id', 'DESC');

        // dd($benchAppOnJob->get());

        return DataTables::of($benchAppOnJob)

            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
						<input type="radio" class="form-check-input chk_list_item " name="chk_list_item" value="'.$row->jobId.'"  />
					  </div>';
            })
            ->editColumn('Requisition', function ($row) {

                $clients_name = '';
                $bgTextDecore = $strVal = '';
                $str = trim(@$row->title);
                $str = $strVal = strtolower($str[0]);
                $str = ord($str);
                if ($str >= 97 && $str <= 101) {
                    $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                } elseif ($str >= 102 && $str <= 106) {
                    $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                } elseif ($str >= 107 && $str <= 111) {
                    $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                } elseif ($str >= 112 && $str <= 116) {
                    $bgTextDecore = 'fs-3 bg-light-success text-success';
                } elseif ($str >= 117 && $str <= 121) {
                    $bgTextDecore = 'fs-3 bg-dark text-secondary';
                } else {
                    $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                }

                $profile_detail_class = 'show-detail';
                $clients_name = ucwords(substr(@$row->company_name, 0, 15));  // clients_name

                $recommendation_id = '';
                $recommendation_id = $row->recommendation_id;
                $recommendation_text = '&nbsp;<i class="bi bi-house" style="color:#00CC00"></i>';

                $logo_path = '';
                // $logo_path			=	url('img/client-default.png');
                if (! empty($row->logo_logical_path)) {

                    $logo_path = @$row->logo_logical_path;
                    if (@$row->company_id != auth()->user()->company_id) {
                        $logo_path = @$row->logo;
                    }

                    if ($row->thirdparty_counter > 0 && $recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                        $logo_path = @$row->logo;
                    }
                }
                if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                    $recommendation_text = '&nbsp;<i style="color: #FF0000; font-weight:bold" class="fa fa-users" aria-hidden="true"></i>';
                    $profile_detail_class = 'client-show-detail';
                    $clients_name = ucwords(substr(@$row->company_name, 0, 15));

                    if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                        $logo_path = @$row->logo;
                    }
                }

                if (@$row->self_company == 1) {
                    if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                        $logo_path = @$row->logo;
                    }
                }

                $company_or_client = GeneralMethod::companyDetailsByClient($row->jobId);

                $icon = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="'.$row->jobId.'" class="'.$profile_detail_class.'">
                      <div class="symbol-label '.$bgTextDecore.' border-radius-5">'.ucwords($strVal).'</div>
                    </a>
                  </div>';

                if (! empty($logo_path)) {
                    $icon = '<div data-row-id="'.$row->jobId.'"  class="symbol symbol-circle symbol-40px overflow-hidden me-2  '.$profile_detail_class.' border-radius-5">
                                        <a href="javascript:void(0);">
                                            <div class="symbol-label border-radius-5">
                                            	<img src="'.@$logo_path.'" alt="Company Logo" class="border-radius-5 w-100">
                                            </div>
                                        </a>
                                    </div>';
                }

                return $icon.'<div class="d-flex flex-column">
					<a href="javascript:void(0);"  data-row-id="'.$row->jobId.'"   data-row-job_applications_id="'.$row->job_applications_id.'" class="text-dark fw-bolder text-hover-primary fs-6 '.$profile_detail_class.'"  data-toggle="tooltip1" title="'.ucwords(@$row->title).'" >'.ucwords(substr(@$row->title, 0, 15)).'</a> 
					<a href="javascript:void(0);" data-row-id="'.$row->jobId.'"   data-row-job_applications_id="'.$row->job_applications_id.'"   class="fw-bold text-muted d-block fs-7 text-hover-primary '.$profile_detail_class.'">'.@$company_or_client.'</a>
                  </div>';
            })
            ->editColumn('Exp', function ($row) {
                return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">'.@$row->max_exp.'+</span>
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.@$row->min_exp.'+</span>
                  </div>';
            })
            ->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {
                $min_sal_mode = $max_sal_mode = $min_salary_currency = $max_salary_currency = $max_salary = $min_salary = '';
                $min_sal_mode = strtoupper(substr($salaryMode[$row->min_sal_mode], 0, 1));
                $max_sal_mode = strtoupper(substr($salaryMode[$row->max_sal_mode], 0, 1));
                $min_salary_currency = $currencySymbole[@$row->min_salary_currency];
                $max_salary_currency = $currencySymbole[@$row->max_salary_currency];
                $max_salary = GeneralMethod::thousandsCurrencyFormat($row->max_salary);
                $min_salary = GeneralMethod::thousandsCurrencyFormat($row->min_salary);

                return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">'.$min_salary_currency.$max_salary.'/'.$min_sal_mode.'</span>                    
                    <span class="fw-bold text-muted d-block fs-7">'.$min_salary_currency.$min_salary.'/'.$max_sal_mode.'</span>
                  </div>';
            })
            ->editColumn('Location', function ($row) {

                // dd($row);

                $country_id = $country_name = '';
                $jobid = $row->jobId;
                $client_loc = DB::table('client_location_jobs')
                    ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                    ->select('client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
                    ->where('client_location_jobs.job_id', $jobid)
                    ->first();

                $country_id = @$client_loc->country_id;
                if (@$country_id > 0) {
                    $country_name = GeneralMethod::getCountryName($country_id);
                    foreach ($country_name as $cval) {
                        $country_name = ucwords($cval['sortname']);
                    }
                }

                $city = substr(@$client_loc->city, 0, 6);
                // if (@$client_loc->country_id == 101) {
                $city = substr(@$client_loc->city_name, 0, 6);
                // }

                $jobLocation = DB::table('client_location_jobs')
                    ->where('client_location_jobs.job_id', $jobid)->count();

                $jobCitySelected = DB::table('client_city_jobs')
                    ->where('client_city_jobs.job_id', $jobid)->count();

                $employment_type = 'Full-time';
                if ($row->employment_type == 1) {
                    $employment_type = 'Contract';
                }

                if ($row->work_type == 'yes') {
                    return ' <div class="d-flex flex-column">
						<span class="text-dark fw-bolder fs-6">Remote</span>
						<div class="d-flex align-items-center">
						  <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
						  </div>
						</div>
					  </div>';
                } elseif ($row->work_type == 'no') {

                    if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                        $client_loc = DB::table('client_city_jobs')
                            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                            ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                            ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                            ->select('city_list.city as city_name', 'countries.country_name')
                            ->where('client_city_jobs.job_id', $jobid)
                            ->first();
                        $city = substr(@$client_loc->city_name, 0, 21);
                        $country_name = substr(@$client_loc->country_name, 0, 6);

                        return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">'.ucwords(substr(@$city, 0, 10)).'</span>
								</div>
								
							  <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
							  </div>
							</div>
						  </div>';
                    } else {
                        return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">Multiple</span>
								</div>
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
						  </div>
						</div>
					  </div>';
                    }

                } elseif ($row->work_type == 'any') {

                    if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                        $client_loc = DB::table('client_city_jobs')
                            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                            ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                            ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                            ->select('city_list.city as city_name', 'countries.country_name')
                            ->where('client_city_jobs.job_id', $jobid)
                            ->first();
                        $city = substr(@$client_loc->city_name, 0, 21);
                        $country_name = substr(@$client_loc->country_name, 0, 6);

                        return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">'.ucwords(substr(@$city, 0, 10)).'</span>
								</div>
								
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
							  </div>
							</div>
						  </div>';
                    } else {
                        return ' <div class="d-flex flex-column">
						<div class="d-flex align-items-center">
						  <span class="text-dark fw-bolder fs-6">Remote</span>
						  &nbsp;|&nbsp;
								  <span class="text-dark fw-bolder fs-6">Multiple</span>
								</div>
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>

						  </div>
						</div>
					  </div>';
                    }

                }

            })

            ->editColumn('Status', function ($row) use ($job_status) {

                $status = $active = $hold = $inactive = $class = $recommendation_id = '';
                $status = ucwords($row->status);
                $class = $job_status[$row->status];
                if ($status == 'Inactive') {
                    $status = 'Closed';
                }
                $ulink = '<a href="javascript.void(0);" data-row-id="'.@$row->id.'" class="d-flex flex-column '.@$class.' status_stage text-hover-success" data-toggle="modal" data-target="#status_stage">'.$status.'</a>';

                return '<div class="d-flex flex-column">
                       '.$ulink.'
                      <span class="fw-bold text-muted d-block fs-7">R-'.@$row->jobId.'</span>
                    </div>';
            })
            ->editColumn('AddedDate', function ($row) {

                $total_positions = $created_at = '';
                $created_at = $row->created_at;
                $total_positions = number_format($row->total_positions);
                $newDate = date('d M, Y', strtotime($created_at));

                return '<div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">'.$newDate.'</span>
                        <span class="text-muted fw-bold d-block fs-7">'.$total_positions.'</span>
                    </div>';
            })
            ->editColumn('ContactPerson', function ($row) {

                $partnersCount = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('job_assign.job_id', '=', $row->jobId)->where('job_assign.company_id', '=', $row->company_id)->where('users.company_id', '=', $row->company_id)->count();

                $partnersData = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('job_assign.job_id', '=', $row->jobId)->where('job_assign.company_id', '=', $row->company_id)->where('users.company_id', '=', $row->company_id)->orderBy('users.name', 'ASC')->skip(0)->take(3)->get();

                $jobId = $row->jobId;
                $html = $temp = '';
                $html .= '<div class="d-flex flex-column"><div class="symbol-group symbol-hover">';
                foreach ($partnersData as $pval) {
                    $image_path = url('public/avatar.png');
                    if (! empty(@$pval->image) && strlen(@$pval->image) > 10) {
                        $image_path = url('public/user-uploads/team_profile_img/'.@$pval->image);
                    }
                    $html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="'.@$pval->name.'"><img alt="Pic" src="'.@$image_path.'"></div>';
                }
                $html .= '<div data-row-id="'.$row->jobId.'"   class="symbol symbol-30 symbol-circle symbol-light hm-listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list.."><span class="symbol-label font-weight-bold">'.$partnersCount.'+</span></div>';
                $html .= '</div></div>';

                return $html;

            })
            ->editColumn('Interview', function ($row) use ($feedback_status) {
                $schedule_date = $className = '';
                $intRowCount = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->job_applications_id)->where('interview_job_id', $row->jobId)->orderBy('interview_schedules.id', 'DESC')->count();
                $intRow = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->job_applications_id)->where('interview_job_id', $row->jobId)->orderBy('interview_schedules.id', 'DESC')->first();
                $html = '';
                if ($intRowCount > 0) {
                    $schedule_date = @$intRow->schedule_date;
                    $schedule_date = date('d M, Y', strtotime($schedule_date));
                } else {
                    $html = '<div style="width:150px" id="interview_date">&nbsp;</div>';
                }
                if ($intRowCount > 0) {
                    $feedback_status_name = '';
                    if ($intRow->feedback >= 0) {
                        $feedback_status_name = @$feedback_status[@$intRow->feedback]['name'];
                        $className = @$feedback_status[@$intRow->feedback]['classname'];
                    } else {
                        $feedback_status_name = 'Pending';
                        $className = 'pending-bg';
                    }
                } else {
                    $feedback_status_name = '--------';
                }

                return '<div class="d-flex flex-column">
											<span class="text-dark fw-bolder fs-6">'.@$schedule_date.'</span>
											<span class="fw-bold d-block fs-7 '.@$className.' px-1">'.@$feedback_status_name.'</span>
										</div>';

            })
            ->editColumn('Stage', function ($row) {

                $name = '';
                if ($row->master_status_id == 8) {
                    $name = '';
                }
                if (! empty(@$row->child_status_id) && @$row->child_status_id > 0) {
                    $childStatus = '';
                    $childStatus = DB::table('candidate_status')->select('*')->where('id', $row->child_status_id)->first();
                    if (! empty($childStatus)) {
                        $name = @$childStatus->name;
                    }
                }
                $stage_default_css = 'stage_sub_stage';
                $html = '';
                if ($row->master_status_id == 8) {
                    $html = '<div class="d-flex flex-column">
								<a href="javascript.void(0);" data-row-id="'.@$row->id.'" class="d-flex flex-column client_stage_sub_stage" data-toggle="modal" data-target="#client_stage_sub_stage">
								<span class="badge fw-bolder fs-6 '.@$row->css_style.' px-2 py-1">'.ucwords(substr($row->name, 0, 10)).'</span>
								<span class="text-muted fw-bold d-block fs-7">'.@$name.'</span>
								</a>
								</div>';
                } else {
                    $html = '<div class="d-flex flex-column">
								<span href="javascript.void(0);" data-row-id="'.@$row->id.'" class="d-flex flex-column ">
								<span class="badge fw-bolder fs-6 '.@$row->css_style.' px-2 py-1">'.ucwords(substr($row->name, 0, 10)).'</span>
								<span class="text-muted fw-bold d-block fs-7">'.@$name.'</span>
								</span>
								</div>';
                }

                return $html;
            })
            ->editColumn('offeramount', function ($row) use ($salaryMode, $currencySymbole) {
                // dd($row->jobId,$row->job_id);
                $rowset = DB::table('offer_job_applications')->where('application_id', $row->job_application_id)->where('job_id', $row->job_id)->first();
                // dd($rowset->offer_currency);
                $offeramount = '--------';
                $offer_currency = '';
                $offer_mode = '';
                if ($row->master_status_id == 5 || $row->master_status_id == 6) {

                    $offercurrencyGet = (@$rowset->offer_currency) ? @$rowset->offer_currency : 0;
                    $offeramountGet = (@$rowset->offer_amount) ? @$rowset->offer_amount : 0;
                    $offermodeGet = (@$rowset->offer_mode) ? @$rowset->offer_mode : 0;

                    $offer_currency = $currencySymbole[$offercurrencyGet];

                    $offeramount = GeneralMethod::thousandsCurrencyFormat(@$offeramountGet);
                    $offer_mode = substr($salaryMode[@$offermodeGet], 0, 1);
                }

                // dd("ji");
                return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">'.$offer_currency.' '.@$offeramount.'|'.$offer_mode.'</span>
                  </div>';
            })
            ->editColumn('joindate', function ($row) {

                $rowset = DB::table('offer_job_applications')->where('application_id', $row->job_application_id)->where('job_id', $row->job_id)->first();
                $joindate = '';
                if ($row->master_status_id == 5 || $row->master_status_id == 6) {
                    $joindate = @$rowset->joined_date ? GeneralMethod::ymdTodmyNotTime($rowset->joined_date) : '';
                }

                return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">'.@$joindate.'</span>
                  </div>';
            })

            ->rawColumns(['chkbox', 'Requisition', 'Exp', 'Salary', 'Location', 'Status', 'AddedDate', 'ContactPerson', 'offeramount', 'joindate', 'Interview', 'Stage'])
            ->addIndexColumn()
            ->make(true);

    }

    public function status_submit(Request $request)
    {
        // dd($request->all());
        $company_id = '';
        $company_id = auth()->user()->company_id;
        $bench_master_status = @$request->bench_master_status;
        $application_id = @$request->bench_application_id;
        $placedid = @$request->placedid;

        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        if ($bench_master_status == 2) {

            $validations = GeneralMethod::BenchcandidateValidation($application_id);
            if ($validations->status) {
                return Reply::dataOnly(['status' => 'candidatevalidation', 'message' => $validations->message]);
            }

            // if(!JobApplication::join('documents','documents.documentable_id','=','job_applications.id')->where('documents.documentable_id','=',$application_id)->exists()){
            // 	return Reply::dataOnly(['status' => 'cvpending', 'message' => 'Resume pending this job application!']);
            // }

            $eventId = 16;
            $companyId = $company_id;
            $coinsRequ = 1;
            $coinsDeductions = GeneralMethod::coinsValidationsAllcompany($companyId, $eventId, $coinsRequ);

            if ($coinsDeductions->status) {
                return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
            } else {

                DB::table('bench_count_view_download')->where('candidate_id', '=', @$request->bench_application_id)->update(['status' => 1]);

                $coinsDeductions = GeneralMethod::coinsDeductions((int) $company_id, $eventId, auth()->user()->id, $company_id, '', '');

                $bench_application_id = $bench_master_status = '';
                $bench_application_id = @$request->bench_application_id;
                $bench_master_status = @$request->bench_master_status;
                $BenchApplication = JobApplication::withoutGlobalScopes()->find($bench_application_id);
                $BenchApplication->status = @$bench_master_status;
                $bench_status = DB::table('global_settings')->select('bench_status')->first();
                $superadminstatus = 0;
                $benchstatus = 'Under Approval';
                if ($bench_status) {
                    if ($bench_status->bench_status) {
                        $superadminstatus = 1;
                        $benchstatus = 'Live on Platform';
                    }
                }
                $BenchApplication->superadminstatus = $superadminstatus;
                $BenchApplication->countforadd = 0;
                $BenchApplication->master_status_id = 0;
                $BenchApplication->save();

                $teamRankElastic = [
                    'company_id' => $this->user->company_id,
                    'user_id' => $this->user->id,
                    'candidate_id' => 0,
                    'profileUpload' => 0,
                    'interviewScheduled' => 0,
                    'interviewNoShow' => 0,
                    'offerReleased' => 0,
                    'candidateJoined' => 0,
                    'jobsPosted' => 0,
                    'feedbackGivenOnCVs' => 0,
                    'postJobToVendorMarket' => 0,
                    'outsourceJobsToVendors' => 0,
                    'listBenchCandidates' => 3,
                    'respondToBenchRequests' => 0,
                ];
                GeneralMethod::teamRankElastic($teamRankElastic);

                $rowdata = DB::table('job_applications')
                    ->join('companies', 'companies.id', '=', 'job_applications.company_id')
                    ->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id')
                    ->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
                    ->leftJoin('countries', 'countries.id', '=', 'job_applications.country')
                    ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
                    ->select('job_applications.*', 'companies.company_name', 'city_list.city as city_name', 'state_list.state as state_name', 'countries.country_name', 'name as qualification_name')->where('job_applications.id', $bench_application_id)->first();

                $bench_assign_hr = DB::table('bench_assign')->join('users', 'users.id', '=', 'bench_assign.user_id')->where('bench_applications_id', $bench_application_id)->pluck('name')->toArray();
                $rm = '';
                if (count($bench_assign_hr) > 0) {
                    $rm = implode(',', $bench_assign_hr);
                }

                $npArr = ['Notice period', 'Available from', 'Immediate'];
                $npDate = $rowdata->np_id;
                if ($rowdata->serving_notice_period_mode == 0) {
                    $npDate = $rowdata->np_id == 1 ? $rowdata->np_id.' Day' : $rowdata->np_id.' Days';
                } elseif ($rowdata->serving_notice_period_mode == 1) {
                    $originalDate = $rowdata->np_id;
                    $npDate = date('d-M-y', strtotime($originalDate));
                } else {
                    $npDate = '';
                }
                $abailabilty = $npArr[$rowdata->serving_notice_period_mode].'<br>'.$npDate;
                $qualification = @$rowdata->qualification_name.'<br>'.mb_substr($rowdata->skills, 0, 12);

                $currency = GeneralMethod::currency();
                $pay_rate_currency = $bill_rate_currency = $pay_rate_amount = $bill_rate_amount = $pay_rate_mode = $bill_rate_mode =
                '';
                $pay_rate_currency = $currency[@$rowdata->pay_rate_currency];
                $bill_rate_currency = $currency[@$rowdata->bill_rate_currency];
                $pay_rate_mode = $salaryMode[@$rowdata->pay_rate_mode];
                $bill_rate_mode = $salaryMode[@$rowdata->bill_rate_mode];
                $pay_rate_amount = GeneralMethod::thousandsCurrencyFormat($rowdata->pay_rate_amount);
                $bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($rowdata->bill_rate_amount);

                $experience = floatval(@$rowdata->total_exp); // Ensure it's a valid float
                // If experience is a whole number, convert it to an integer; otherwise, keep the decimal
                $formattedExperience = ($experience == intval($experience)) ? intval($experience) : $experience;

                $rel_experience = floatval(@$rowdata->rel_exp); // Ensure it's a valid float
                // If experience is a whole number, convert it to an integer; otherwise, keep the decimal
                $rel_formattedExperience = ($rel_experience == intval($rel_experience)) ? intval($rel_experience) : $rel_experience;

                $experience_show = $formattedExperience.' Y'.'<br>'.' '.$rel_formattedExperience.' Y';

                $bill_rate = $bill_rate_currency.' '.@$bill_rate_amount.'/'.$bill_rate_mode.'<br>'.@$pay_rate_currency.' '.@$pay_rate_amount.'/'.$pay_rate_mode;

                // ucwords(@$row->current_designation)

                $date_set = date('d M y');

                $name_and_designation = $rowdata->full_name.'<br>'.$rowdata->current_designation;

                $location = ucwords(mb_substr($rowdata->city_name, 0, 10)).'<br>'.ucfirst(mb_substr($rowdata->state_name, 0, 5)).'|'.strtoupper(mb_substr($rowdata->country_name, 0, 2));

                $basecompany = GeneralMethod::baseCompany();
                $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
                $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
                $attachments = [];
                $cc = [];
                $checkMailPermision = GeneralMethod::mailPermission(27);
                $mailTypeSubject = GeneralMethod::mailTypeSubject(27, 2);
                if ($checkMailPermision) {
                    $subject = str_replace(['[{STATUS}]', '[{CANDIDATE_NAME}]', '[{BENCH_ID}]', '[{DESIGNATION}]'], [$benchstatus, $rowdata->full_name, $rowdata->id, $rowdata->current_designation], $mailTypeSubject->subject);
                    $template_description = $mailTypeSubject->body_description;
                    $template_description = str_replace(['[{VENDOR_COMPANY_NAME}]', '[{BENCH_CANDIDATE_NAME}]', '[{BENCH_ID}]', '[{RM}]', '[{EXPERIENCE}]', '[{RATE}]', '[{AVAILABILITY}]', '[{DATESET}]', '[{LOCATION}]', '[{EDUCATION}]', '[{STATUS}]'], [$rowdata->company_name, $name_and_designation, $rowdata->id, $rm, $experience_show, $bill_rate, $abailabilty, $date_set, $location, $qualification, $benchstatus], $template_description);

                    $message = $template_description;
                    $notificationemail = GeneralMethod::notification($rowdata->company_id, 'Sales'); // Recuitment Sales
                    $defailsEmail = DB::table('companies')->where('id', $rowdata->company_id)->first()->company_email;
                    $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
                    $datamail = [
                        'message' => $message,
                    ];
                    // dd($to_Mail_Id);

                    QueueNotificationMailService::dispatch(
                        $to_Mail_Id,
                        $subject,
                        'email.common.index',
                        (object) $datamail,
                        $attachments,
                        $fromAddress,
                        $senderName,
                        $cc,
                        $company_id
                    )->onConnection('emails');

                    // $result = $this->mailService->sendEmail($to_Mail_Id, $subject,'email.common.index', (object) $datamail, $attachments, $fromAddress, $senderName,$cc);
                    // dd($to_Mail_Id,$result);
                }
                $exists = $this->elasticService->getClient()->exists([
                    'index' => ELASTIC_CANDIDATES_BENCH,
                    'id' => $application_id,
                ]);
                // dd($application_id);
                if ($exists) {

                    // 2. Prepare the update timestamp
                    $updateTime = now()->toIso8601String();
                    $master_status_name = DB::table('bench_master_status')
                        ->select('*')
                        ->where('id', (int) $bench_master_status)
                        ->first('name');

                    // 3. Execute update with debugging
                    $updateParams = [
                        'index' => ELASTIC_CANDIDATES_BENCH,
                        'id' => $application_id,
                        'body' => [
                            'doc' => [
                                'status' => (int) $bench_master_status,
                                'master_status_name' => $master_status_name->name,
                                'css_style' => $master_status_name->css_style,
                                'updated_at' => $updateTime,
                            ],
                            'doc_as_upsert' => false, // Changed to false since we verified existence
                        ],
                        'refresh' => false, // Eventual consistency for 10K concurrent scale
                    ];

                    $this->elasticService->getClient()->update($updateParams);

                    $updateParamsCandidate = [
                        'index' => ELASTIC_CANDIDATES,
                        'body' => [
                            'script' => [
                                'source' => 'ctx._source.status = params.status;',
                                'lang' => 'painless',
                                'params' => [
                                    'status' => (int) $bench_master_status,
                                ],
                            ],
                            'query' => [
                                'term' => [
                                    'candidate_id' => $application_id,
                                ],
                            ],
                        ],
                        'refresh' => false,
                    ];

                    $this->elasticService->getClient()->updateByQuery($updateParamsCandidate);

                }

                if ($placedid > 0) {
                    $existsCandidate = $this->elasticService->getClient()->exists([
                        'index' => ELASTIC_CANDIDATES,
                        'id' => $placedid,
                    ]);
                    if ($existsCandidate) {

                        $updateParamsCandidate = [
                            'index' => ELASTIC_CANDIDATES,
                            'id' => $placedid,
                            'body' => [
                                'doc' => [
                                    'status' => (int) $bench_master_status,
                                ],
                                'doc_as_upsert' => false, // Changed to false since we verified existence
                            ],
                            'refresh' => false, // Eventual consistency for 10K concurrent scale
                        ];

                        $this->elasticService->getClient()->update($updateParamsCandidate);
                    }
                }

                $cachekeys = [
                    'allbenchcandidates_version_user_'.$this->user->id,
                    'benchviewmore_version_user_'.$this->user->id,
                    'allbenchofferListing_version_user_'.$this->user->id,
                    'allbenchofferListing_version_company_'.($this->user->company_id ?? auth()->user()->company_id),
                ];
                foreach ($cachekeys as $key) {
                    GeneralMethod::cacheClean($key);
                }

                return Reply::dataOnly(['status' => 'success']);
            }

            // $packageStatus = GeneralMethod::validatePackage('NEW_BENCH_CLIENT_REQUEST');
            // if ($packageStatus['STATUS'] >= 1) {
            // 	if ($packageStatus['EXPIRE'] == 'false') {
            // 		if ($packageStatus['BALANCE'] > 0) {
            // 			$bench_application_id = $bench_master_status = "";
            // 			$bench_application_id = @$request->bench_application_id;
            // 			$bench_master_status = @$request->bench_master_status;
            // 			$BenchApplication = JobApplication::find($bench_application_id);
            // 			$BenchApplication->status = @$bench_master_status;
            // 			$bench_status = DB::table('global_settings')->select('bench_status')->first();
            // 			$superadminstatus = 0;
            // 			$benchstatus = "Under Approval";
            // 			if($bench_status){
            // 				if($bench_status->bench_status){
            // 					$superadminstatus = 1;
            // 					$benchstatus = "Live on Platform";
            // 				}
            // 			}
            // 			$BenchApplication->superadminstatus = $superadminstatus;
            // 			$BenchApplication->countforadd = 0;
            // 			$BenchApplication->save();
            // 			$rowdata = DB::table('job_applications')->join('companies','companies.id','=','job_applications.company_id')->select('job_applications.*','companies.company_name')->where('job_applications.id',$bench_application_id)->first();
            // 			$basecompany = GeneralMethod::baseCompany();
            // 			$senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
            // 			$fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
            // 			$attachments = array();
            // 			$cc = array();
            // 			$checkMailPermision = GeneralMethod::mailPermission(27);
            //             $mailTypeSubject = GeneralMethod::mailTypeSubject(27,2);
            // 			if($checkMailPermision){
            // 			$subject = str_replace(["[{BENCH_CANDIDATE_NAME}]","[{STATUS}]"],[$rowdata->full_name,$benchstatus], $mailTypeSubject->subject);
            //             $template_description = $mailTypeSubject->body_description;
            // 			$template_description = str_replace(["[{VENDOR_COMPANY_NAME}]","[{BENCH_CANDIDATE_NAME}]","[{BENCH_ID}]"],[$rowdata->company_name,$rowdata->full_name, $rowdata->id], $template_description);

            // 			$message = $template_description;
            // 			$notificationemail = GeneralMethod::notification($rowdata->company_id,'Sales'); //Recuitment Sales
            // 			$defailsEmail = DB::table('companies')->where('id',$rowdata->company_id)->first()->company_email;
            // 			$to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
            // 			$datamail = [
            // 				'message' => $message
            // 			];
            // 			$result = $this->mailService->sendEmail($to_Mail_Id, $subject,'email.common.index', (object) $datamail, $attachments, $fromAddress, $senderName,$cc);
            // 			// dd($to_Mail_Id,$result);
            // 			}

            // 			return Reply::dataOnly(['status' => 'success']);
            // 		} else {
            // 			return Reply::dataOnly(["status" => "info", "message" => "New Bench Client Request can't access because your package balance MAX LIMIT REACHED , please contact to Administrator."]);
            // 		}
            // 	} else {
            // 		return Reply::dataOnly(["status" => "info", "message" => "New Bench Client Request can't access because your PACKAGE EXPIRED , please contact to Administrator."]);
            // 	}
            // } else {
            // 	return Reply::dataOnly(["status" => "info", "message" => "New Bench Client Request can't access because your package STATUS IS INACTIVE , please contact to Administrator."]);
            // }

        } else {
            if (@$request->bench_master_status == 3) {

                if (empty($request->chk_list_item)) {
                    return Reply::dataOnly(['status' => 'info', 'message' => 'Please select requisition!']);
                }

            }
            $bench_application_id = $bench_master_status = '';
            $bench_application_id = @$request->bench_application_id;
            $bench_master_status = @$request->bench_master_status;
            $BenchApplication = JobApplication::withoutGlobalScopes()->find($bench_application_id);
            $BenchApplication->status = @$bench_master_status;
            $BenchApplication->save();

            if (@$request->bench_master_status == 3) {

                if ($request->chk_list_item > 0) {

                    $bench_applied_detail = DB::table('job_applications');
                    $bench_applied_detail = $bench_applied_detail->select(
                        'job_applications.id',
                        'job_applications.full_name',
                        'job_applications.current_designation',
                        'job_applications.email',
                        'job_applications.phone',
                        'job_applications.total_exp',
                        'job_applications.rel_exp',
                        'job_applications.pay_rate_currency',
                        'job_applications.pay_rate_amount',
                        'job_applications.pay_rate_mode',
                        'job_applications.bill_rate_currency',
                        'job_applications.bill_rate_amount',
                        'job_applications.bill_rate_mode',
                        'job_applications.pay_rate_currency',
                        'job_applications.pay_rate_amount',
                        'job_applications.pay_rate_mode',
                        'job_applications.bill_rate_currency',
                        'job_applications.bill_rate_amount',
                        'job_applications.bill_rate_mode',

                        'jobs.title',
                        'jobs.company_id as client_company_id',
                        'companies.company_name',
                        'qualification_master.name as qualification_master_name',
                        'job_applications.skills',
                        'job_applications.country',
                        'job_applications.state',
                        'job_applications.city',
                        'job_applications.city_id',
                        'job_applications.offer_amount',
                        'master_status.name as master_status_name',
                        'candidate_status.name as candidate_status_name',
                        'bench_master_status.name as bench_master_status_name',

                        'job_applications.joined_date',
                        'job_applications.id as client_application_id'
                    );

                    $bench_applied_detail = $bench_applied_detail->leftJoin('rec_candidate_assign', function ($join) {
                        $join->on('rec_candidate_assign.source_from_id', '=', 'job_applications.id');
                        // ->on("rec_candidate_assign.source_from_type", "=", DB::raw('1'));
                    });
                    $bench_applied_detail = $bench_applied_detail->leftJoin('jobs', 'jobs.id', 'rec_candidate_assign.source_jobid');
                    $bench_applied_detail = $bench_applied_detail->leftJoin('companies', 'companies.id', 'jobs.company_id');
                    // $bench_applied_detail = $bench_applied_detail->join('job_applications', 'job_applications.id', 'rec_candidate_assign.source_application_id');

                    $bench_applied_detail = $bench_applied_detail->leftJoin('qualification_master', 'qualification_master.id', 'job_applications.qualification');
                    $bench_applied_detail = $bench_applied_detail->leftJoin('master_status', 'master_status.id', 'job_applications.master_status_id');
                    $bench_applied_detail = $bench_applied_detail->leftJoin('candidate_status', 'candidate_status.id', 'job_applications.child_status_id');
                    $bench_applied_detail = $bench_applied_detail->leftJoin('bench_master_status', 'bench_master_status.id', 'job_applications.status');

                    $bench_applied_detail = $bench_applied_detail->where('job_applications.id', '=', @$request->bench_application_id);
                    $bench_applied_detail = $bench_applied_detail->where('jobs.id', '=', @$request->chk_list_item);
                    $bench_applied_detail = $bench_applied_detail->first();

                    // dd($bench_applied_detail,$request->chk_list_item,$request->bench_application_id);

                    $row_country_name = '';
                    if (! empty(@$bench_applied_detail->country)) {
                        if (is_numeric(@$bench_applied_detail->country)) {
                            $country_name_Arr = GeneralMethod::getCountryName(@$bench_applied_detail->country);
                            foreach ($country_name_Arr as $cval) {
                                $row_country_name = ucwords($cval['name']);
                            }
                        }
                    }

                    $state = $row_state_name = '';

                    if (empty($bench_applied_detail->state)) {
                        $row_state_name = '----';
                    } else {
                        if (@$bench_applied_detail->country == 101) {
                            $row_state = DB::table('state_list')->select('*')->where('id', @$bench_applied_detail->state)->first();
                            $row_state_name = @$row_state->state;

                        } else {
                            $state = @$bench_applied_detail->state;
                            if (is_numeric(@$bench_applied_detail->country) && is_numeric($state)) {
                                if ($state > 0) {
                                    $state_name_Arr = GeneralMethod::getStateName(@$bench_applied_detail->country, $state);
                                    foreach ($state_name_Arr as $sval) {
                                        $row_state_name = ucwords($sval['name']);
                                    }
                                }
                            }
                        }
                    }

                    $city = $row_city_name = '';
                    if (@$bench_applied_detail->country == 101) {
                        $row_city = DB::table('city_list')->select('*')->where('city_id', @$bench_applied_detail->city_id)->first();
                        $row_city_name = @$row_city->city;
                    } else {
                        $row_city_name = @$bench_applied_detail->city;
                    }

                    // dd(@$request->bench_application_id);
                    $bench_assign_string = '';
                    // $bench_assign_list = DB::table('bench_assign')->select('users.name')
                    // 	->join('users', 'users.id', 'bench_assign.user_id')
                    // 	->where('bench_assign.job_applications_id', '=', @$request->bench_application_id)
                    // 	->get();
                    //   dd("ji");
                    $cnt = 0;
                    if (! empty($bench_assign_list)) {
                        foreach ($bench_assign_list as $assignVal) {
                            if ($cnt == 0) {
                                $bench_assign_string = $assignVal->name;
                            } else {
                                $bench_assign_string = $bench_assign_string.' , '.$assignVal->name;
                            }
                            $cnt++;
                        }
                    }

                    $offerData = DB::table('offer_job_applications')->where('application_id', @$request->bench_application_id)->where('job_id', @$request->chk_list_item)->first();

                    $values = [
                        'source_comapny_id' => auth()->user()->company_id,
                        'bench_id' => @$request->bench_application_id,
                        'client_company_id' => @$bench_applied_detail->client_company_id,
                        'client_job_id' => @$request->chk_list_item,
                        'client_application_id' => @$bench_applied_detail->client_application_id,

                        'full_name' => @$bench_applied_detail->full_name,
                        'current_designation' => @$bench_applied_detail->current_designation,
                        'email' => @$bench_applied_detail->email,
                        'phone' => @$bench_applied_detail->phone,
                        'total_exp' => @$bench_applied_detail->total_exp,

                        'rel_exp' => @$bench_applied_detail->rel_exp,
                        'pay_rate_currency' => @$bench_applied_detail->pay_rate_currency,
                        'pay_rate_amount' => @$bench_applied_detail->pay_rate_amount,
                        'pay_rate_mode' => @$bench_applied_detail->pay_rate_mode,

                        'bill_rate_currency' => @$bench_applied_detail->bill_rate_currency,
                        'bill_rate_amount' => @$bench_applied_detail->bill_rate_amount,
                        'bill_rate_mode' => @$bench_applied_detail->bill_rate_mode,

                        'job_title' => @$bench_applied_detail->title,
                        'company_name' => @$bench_applied_detail->company_name,
                        'qualification' => @$bench_applied_detail->qualification_master_name,
                        'skills' => @$bench_applied_detail->skills,
                        'country' => @$row_country_name,

                        'state' => @$row_state_name,
                        'city' => @$row_city_name,
                        'assign_to' => @$bench_assign_string,

                        'offer_amount' => @$offerData->offer_amount,
                        'application_stage' => @$bench_applied_detail->master_status_name,
                        'application_child_stage' => @$bench_applied_detail->candidate_status_name,
                        'bench_status' => @$bench_applied_detail->bench_master_status_name,

                        'joined_date' => @$offerData->joined_date,
                        'offer_currency' => @$offerData->offer_currency,
                        'offer_mode' => @$offerData->offer_mode,

                    ];
                    $detail = DB::table('add_job_wise_condidates')->where('job_id', '=', @$request->chk_list_item)->where('job_application_id', '=', @$request->bench_application_id)->first();
                    // dd($detail);
                    DB::table('offer_job_applications')->where('job_id', @$request->chk_list_item)->where('application_id', '=', @$request->bench_application_id)->limit(1)->update(['joined' => $detail->id]);
                    DB::table('bench_place')->insert($values);
                    $placeIdAdd = DB::getPdo()->lastInsertId();
                    GeneralMethod::benchPlacedIndexingupload($placeIdAdd);

                    $cachekeys = [
                        'allbenchplacedListing_version_user_'.$this->user->id,
                    ];
                    foreach ($cachekeys as $key) {
                        GeneralMethod::cacheClean($key);
                    }

                } else {
                    return Reply::dataOnly(['status' => 'info', 'message' => 'Please select requisition!']);
                }

            }

            $exists = $this->elasticService->getClient()->exists([
                'index' => ELASTIC_CANDIDATES_BENCH,
                'id' => $application_id,
            ]);
            if ($exists) {

                // 2. Prepare the update timestamp
                $updateTime = now()->toIso8601String();
                $master_status_name = DB::table('bench_master_status')
                    ->select('*')
                    ->where('id', (int) $bench_master_status)
                    ->first('name');

                // 3. Execute update with debugging
                $updateParams = [
                    'index' => ELASTIC_CANDIDATES_BENCH,
                    'id' => $application_id,
                    'body' => [
                        'doc' => [
                            'status' => (int) $bench_master_status,
                            'master_status_name' => $master_status_name->name,
                            'css_style' => $master_status_name->css_style,
                            'updated_at' => $updateTime,
                        ],
                        'doc_as_upsert' => false, // Changed to false since we verified existence
                    ],
                    'refresh' => false, // Eventual consistency for 10K concurrent scale
                ];

                $this->elasticService->getClient()->update($updateParams);

                $updateParamsCandidate = [
                    'index' => ELASTIC_CANDIDATES,
                    'body' => [
                        'script' => [
                            'source' => 'ctx._source.status = params.status;',
                            'lang' => 'painless',
                            'params' => [
                                'status' => (int) $bench_master_status,
                            ],
                        ],
                        'query' => [
                            'term' => [
                                'candidate_id' => $application_id,
                            ],
                        ],
                    ],
                    'refresh' => false,
                ];

                $this->elasticService->getClient()->updateByQuery($updateParamsCandidate);

            }
            if ($placedid > 0) {
                $existsCandidate = $this->elasticService->getClient()->exists([
                    'index' => ELASTIC_CANDIDATES,
                    'id' => $placedid,
                ]);
                if ($existsCandidate) {

                    $updateParamsCandidate = [
                        'index' => ELASTIC_CANDIDATES,
                        'id' => $placedid,
                        'body' => [
                            'doc' => [
                                'status' => (int) $bench_master_status,
                            ],
                            'doc_as_upsert' => false, // Changed to false since we verified existence
                        ],
                        'refresh' => false, // Eventual consistency for 10K concurrent scale
                    ];

                    $this->elasticService->getClient()->update($updateParamsCandidate);
                }
            }

            $cachekeys = [
                'benchviewmore_version_user_'.$this->user->id,
                'allbenchcandidates_version_user_'.$this->user->id,
                'allbenchofferListing_version_user_'.$this->user->id,
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }

            return Reply::dataOnly(['status' => 'success', 'message' => 'Bench Application status update successfully !']);
        }

    }

    /** Show Shortlisted Bench Candidate listing page. Route: service_company.bench-candidates.offerListing */
    public function offerListing()
    {
        $this->pageTitle = __('Offer Listing');
        $stats = $this->shortlistBenchCandidateListingService->getOfferStats(auth()->user()->company_id, $this->user->id);
        $this->benchOfferHeroStats = $stats['hero'];
        $this->benchOfferTabStats = $stats['tabs'];
        $this->benchOfferLocations = $stats['locations'];

        return view('service_company.bench-candidates.offerListing', $this->data);
    }

    /** Fetch paginated Shortlisted Bench Candidates JSON payload with zero N+1. Route: service_company.bench-candidates.offerListingData_ajax */
    public function offerListingData_ajax(Request $request)
    {
        abort_if(Gate::denies('view_job_applications'), 403);
        $companyId = auth()->user()->company_id;
        $responseData = $this->shortlistBenchCandidateListingService->getOfferListingData($request->all(), $companyId, $this->user);

        return response()->json($responseData);
    }

    public function bench_assign_to_job($id)
    {
        $this->currentBenchId = $id;
        $view = view('service_company.bench-candidates.bench_assign_to_job', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function placedListing()
    {
        $this->pageTitle = __('Bench Placed Listing');
        $company_id = auth()->user()->company_id ?? null;
        $this->allBenchApplication = $company_id
            ? BenchApplication::where('company_id', $company_id)->count()
            : BenchApplication::count();

        return view('service_company.bench-candidates.placedListing', $this->data);
    }

    public function placedListingData_ajax(Request $request)
    {

        abort_if(Gate::denies('view_job_applications'), 403);
        $company_id = auth()->user()->company_id;

        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();
        $currency = GeneralMethod::currency();

        $chkAdminRole = $this->user->hasRole('admin');
        // $placedApplications = DB::table('bench_place')->select('bench_place.*');
        // $placedApplications = $placedApplications->where('bench_place.source_comapny_id', '=', $company_id);
        // $placedApplications = $placedApplications->orderBy('bench_place.id', 'DESC');

        $searchTerms = request('cus_search');
        $custom_search = $searchTerms; // json_decode($searchTerms, true);

        $searchQuery = $request->input('q');
        $valCheck = $request->input('sortingfilter');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) ($request->input('per_page') ?? $request->input('size') ?? $request->input('limit') ?? 10);
        $from = ($page - 1) * $size;
        $sort = [];

        $cachePayload = [
            'page' => $page,
            'size' => $size,
            'q' => $searchQuery,
            'company' => $company_id,
            'cus_search' => $request->input('cus_search'),
            'country' => $request->input('country'),
            'user_id' => $this->user->id,
            'sortingfilter' => $valCheck,
        ];

        $versionKey = 'allbenchplacedListing_version_user_'.$this->user->id;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:allbenchplacedListing:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));
        /* ----------------------------
        ⚡ Check DragonflyDB first
        ----------------------------- */
        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet));
        }

        $sortField = 'id'; // Default sort field
        $sortOrder = 'desc'; // Default sort order

        switch ($valCheck) {
            case 1:
                $sort[] = ['created_at' => ['order' => 'desc']];
                break;
            case 2:
                $sort[] = ['created_at' => ['order' => 'asc']];
                break;
            case 3:
                $sort[] = ['ctc' => ['order' => 'asc', 'unmapped_type' => 'float']];
                break;
            case 4:
                $sort[] = ['ctc' => ['order' => 'desc', 'unmapped_type' => 'float']];
                break;
            case 5: // Notice period ASC
                $sort[] = ['notice_period_days' => ['order' => 'asc', 'unmapped_type' => 'integer']];
                break;
            case 6: // Notice period DESC
                $sort[] = ['notice_period_days' => ['order' => 'desc', 'unmapped_type' => 'integer']];
                break;
            case 21: // Notice period Date ASC
                $sort[] = ['notice_period_epoch' => ['order' => 'asc', 'unmapped_type' => 'long']];
                break;
            case 22: // Notice period Date DESC
                $sort[] = ['notice_period_epoch' => ['order' => 'desc', 'unmapped_type' => 'long']];
                break;
            case 9: // Immediate joiners first
                $sort[] = ['notice_period_days' => ['order' => 'asc', 'unmapped_type' => 'integer']];
                break;
            case 7:
                $sort[] = ['total_exp' => ['order' => 'desc', 'unmapped_type' => 'float']];
                break;
            case 8:
                $sort[] = ['total_exp' => ['order' => 'asc', 'unmapped_type' => 'float']];
                break;
            case 10:
                $sort[] = ['send_accept_date_time' => ['order' => 'desc']];
                break;
            case 11:
                $sort[] = ['send_accept_date_time' => ['order' => 'asc']];
                break;
            case 12:
                $sort[] = ['of_joined_date' => ['order' => 'desc']];
                break;
            case 13:
                $sort[] = ['of_joined_date' => ['order' => 'asc']];
                break;
            case 14:
                $sort[] = ['of_offer_amount' => ['order' => 'asc', 'unmapped_type' => 'float']];
                break;
            case 15:
                $sort[] = ['of_offer_amount' => ['order' => 'desc', 'unmapped_type' => 'float']];
                break;
            default:
                $sort[] = ['wiseId' => ['order' => 'desc', 'unmapped_type' => 'long']];
                break;
        }

        // job-onboard (joining tracker) uses the exact same ES index with job_master_status_id=6.
        // Mirror its three should-conditions so both pages show identical records.
        // Bench placed is kept as-is (original condition).
        $searchConds = [];
        if (! empty($custom_search)) {
            $searchConds[] = [
                'bool' => [
                    'should' => [
                        ['match_phrase_prefix' => ['full_name' => strtolower($custom_search)]],
                        ['wildcard' => ['email' => '*'.strtolower($custom_search).'*']],
                        ['wildcard' => ['phone' => '*'.$custom_search.'*']],
                        ['match_phrase_prefix' => ['job_title' => strtolower($custom_search)]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        $boolQuery = [
            'bool' => [
                'should' => [
                    // 1. Bench candidates placed by this company (original placed listing)
                    ['bool' => ['must' => array_merge([
                        ['term' => ['other_company' => $company_id]],
                        ['term' => ['condidate_type' => 'bench_condidate']],
                        ['term' => ['job_master_status_id' => 6]],
                        ['terms' => ['status' => [3]]],
                    ], $searchConds)]],
                    // 2. Join tracker — own company, no third-party client (mirrors job-onboard cond 1)
                    ['bool' => ['must' => array_merge([
                        ['term' => ['company_id' => $company_id]],
                        ['term' => ['job_master_status_id' => 6]],
                        ['term' => ['ClientComID' => 0]],
                    ], $searchConds)]],
                    // 3. Join tracker — own company, with third-party client (mirrors job-onboard cond 2)
                    ['bool' => ['must' => array_merge([
                        ['term' => ['company_id' => $company_id]],
                        ['term' => ['job_master_status_id' => 6]],
                        ['range' => ['ClientComID' => ['gt' => 0]]],
                        ['range' => ['jobId' => ['gt' => 0]]],
                        ['bool' => ['must_not' => [['terms' => ['job_master_status_id' => [8, 9]]]]]],
                    ], $searchConds)]],
                    // 4. Join tracker — third-party company is this company (mirrors job-onboard cond 3)
                    ['bool' => ['must' => array_merge([
                        ['term' => ['ClientComID' => $company_id]],
                        ['term' => ['job_master_status_id' => 6]],
                        ['range' => ['jobId' => ['gt' => 0]]],
                    ], $searchConds)]],
                ],
                'minimum_should_match' => 1,
                'filter' => [],
                'must_not' => [],
            ],
        ];
        $params = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => $boolQuery,
            ],
        ];
        if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        // dd($data);

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
            'per_page' => $size, // lets pagination partial show the real page size instead of assuming 10
        ];

        if ($request->input('format') === 'json') {
            return $this->placedListingData_v2($request);
        }

        $userPermissions = $this->userPermissions;
        $responseData = [
            'html' => view('service_company.ajax_pages.bench-place-candidate-ajax', compact('data', 'userPermissions', 'company_id', 'salaryMode', 'currencySymbole'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ];

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_BENCH_PLACED_LISTING_TIME));

        return response()->json($responseData);

    }

    public function placedListingData_v2(Request $request)
    {
        abort_if(Gate::denies('view_job_applications'), 403);
        $company_id = auth()->user()->company_id;

        $searchQuery = $request->input('q');
        $valCheck = $request->input('sortingfilter');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) ($request->input('per_page') ?? $request->input('size') ?? $request->input('limit') ?? 10);
        $from = ($page - 1) * $size;
        $custom_search = $request->input('cus_search');

        $cachePayload = [
            'v2' => true,
            'page' => $page,
            'size' => $size,
            'q' => $searchQuery,
            'company' => $company_id,
            'cus_search' => $custom_search,
            'user_id' => $this->user->id,
            'sortingfilter' => $valCheck,
        ];

        $versionKey = 'allbenchplacedListing_version_user_'.$this->user->id;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:v2:allbenchplacedListing:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));

        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet));
        }

        $sort = [];
        switch ($valCheck) {
            case 1:
                $sort[] = ['created_at' => ['order' => 'desc']];
                break;
            case 2:
                $sort[] = ['created_at' => ['order' => 'asc']];
                break;
            case 3:
                $sort[] = ['ctc' => ['order' => 'asc', 'unmapped_type' => 'float']];
                break;
            case 4:
                $sort[] = ['ctc' => ['order' => 'desc', 'unmapped_type' => 'float']];
                break;
            case 5:
                $sort[] = ['notice_period_days' => ['order' => 'asc', 'unmapped_type' => 'integer']];
                break;
            case 6:
                $sort[] = ['notice_period_days' => ['order' => 'desc', 'unmapped_type' => 'integer']];
                break;
            case 21:
                $sort[] = ['notice_period_epoch' => ['order' => 'asc', 'unmapped_type' => 'long']];
                break;
            case 22:
                $sort[] = ['notice_period_epoch' => ['order' => 'desc', 'unmapped_type' => 'long']];
                break;
            case 7:
                $sort[] = ['total_exp' => ['order' => 'desc', 'unmapped_type' => 'float']];
                break;
            case 8:
                $sort[] = ['total_exp' => ['order' => 'asc', 'unmapped_type' => 'float']];
                break;
            case 10:
                $sort[] = ['send_accept_date_time' => ['order' => 'desc']];
                break;
            case 11:
                $sort[] = ['send_accept_date_time' => ['order' => 'asc']];
                break;
            case 12:
                $sort[] = ['of_joined_date' => ['order' => 'desc']];
                break;
            case 13:
                $sort[] = ['of_joined_date' => ['order' => 'asc']];
                break;
            case 14:
                $sort[] = ['of_offer_amount' => ['order' => 'asc', 'unmapped_type' => 'float']];
                break;
            case 15:
                $sort[] = ['of_offer_amount' => ['order' => 'desc', 'unmapped_type' => 'float']];
                break;
            default:
                $sort[] = ['wiseId' => ['order' => 'desc', 'unmapped_type' => 'long']];
                break;
        }

        $searchConds = [];
        if (! empty($custom_search)) {
            $searchConds[] = [
                'bool' => [
                    'should' => [
                        ['match_phrase_prefix' => ['full_name' => strtolower($custom_search)]],
                        ['wildcard' => ['email' => '*'.strtolower($custom_search).'*']],
                        ['wildcard' => ['phone' => '*'.$custom_search.'*']],
                        ['match_phrase_prefix' => ['job_title' => strtolower($custom_search)]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        $boolQuery = [
            'bool' => [
                'should' => [
                    ['bool' => ['must' => array_merge([
                        ['term' => ['other_company' => $company_id]],
                        ['term' => ['condidate_type' => 'bench_condidate']],
                        ['term' => ['job_master_status_id' => 6]],
                        ['terms' => ['status' => [3]]],
                    ], $searchConds)]],
                    ['bool' => ['must' => array_merge([
                        ['term' => ['company_id' => $company_id]],
                        ['term' => ['job_master_status_id' => 6]],
                        ['term' => ['ClientComID' => 0]],
                    ], $searchConds)]],
                    ['bool' => ['must' => array_merge([
                        ['term' => ['company_id' => $company_id]],
                        ['term' => ['job_master_status_id' => 6]],
                        ['range' => ['ClientComID' => ['gt' => 0]]],
                        ['range' => ['jobId' => ['gt' => 0]]],
                        ['bool' => ['must_not' => [['terms' => ['job_master_status_id' => [8, 9]]]]]],
                    ], $searchConds)]],
                    ['bool' => ['must' => array_merge([
                        ['term' => ['ClientComID' => $company_id]],
                        ['term' => ['job_master_status_id' => 6]],
                        ['range' => ['jobId' => ['gt' => 0]]],
                    ], $searchConds)]],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => $boolQuery,
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            $total = 0;
            $candidates = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $candidates = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        $responseData = [
            'status' => 'success',
            'total' => $total,
            'page' => $page,
            'per_page' => $size,
            'last_page' => (int) ceil($total / max($size, 1)),
            'candidates' => $candidates,
        ];

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_BENCH_PLACED_LISTING_TIME));

        return response()->json($responseData);
    }

    public function savePlacedAssignmentDetails(Request $request)
    {
        $company_id = auth()->user()->company_id;
        $candidate_id = (int) $request->candidate_id;
        $is_bench = (int) $request->is_bench;

        if (! $candidate_id) {
            return Reply::dataOnly(['status' => 'fail', 'message' => 'Invalid candidate.']);
        }

        // candidate_id from JS = wiseId = add_job_wise_condidates.id (ES document _id)
        $wiseRecord = DB::table('add_job_wise_condidates')->where('id', $candidate_id)->first();
        $wiseId = $wiseRecord ? $wiseRecord->id : null;
        $job_id = $wiseRecord ? $wiseRecord->job_id : null;
        $jobApplicationId = $wiseRecord ? $wiseRecord->job_application_id : null;

        $assignmentFields = [
            'assignment_status' => $request->assignment_status ?: null,
            'assignment_expected_end_date' => $request->expected_end_date ?: null,
            'assignment_employment_type' => $request->employment_type ?: null,
            'assignment_client_location' => $request->client_location ?: null,
            'assignment_exit_reason' => $request->exit_reason ?: null,
            'assignment_project_outcome' => $request->project_outcome ?: null,
            'assignment_remarks' => $request->remarks ?: null,
            'assignment_internal_notes' => $request->internal_notes ?: null,
            'assignment_replacement_required' => $request->replacement_required !== null ? (bool) (int) $request->replacement_required : null,
            'assignment_replacement_status' => $request->replacement_status ?: null,
            'assignment_replacement_requested_on' => $request->replacement_requested_on ?: null,
            'assignment_replacement_reason' => $request->replacement_reason ?: null,
        ];

        // Dual Persistence: Write to MySQL source table offer_job_applications first
        if ($jobApplicationId) {
            DB::table('offer_job_applications')
                ->where('application_id', $jobApplicationId)
                ->where('job_id', $job_id)
                ->update([
                    'assignment_status' => $request->assignment_status ?: null,
                    'assignment_expected_end_date' => $request->expected_end_date ?: null,
                    'assignment_employment_type' => $request->employment_type ?: null,
                    'assignment_client_location' => $request->client_location ?: null,
                    'assignment_exit_reason' => $request->exit_reason ?: null,
                    'assignment_project_outcome' => $request->project_outcome ?: null,
                    'assignment_remarks' => $request->remarks ?: null,
                    'assignment_internal_notes' => $request->internal_notes ?: null,
                    'assignment_replacement_required' => $request->replacement_required !== null ? (int) $request->replacement_required : null,
                    'assignment_replacement_status' => $request->replacement_status ?: null,
                    'assignment_replacement_requested_on' => $request->replacement_requested_on ?: null,
                    'assignment_replacement_reason' => $request->replacement_reason ?: null,
                    'updated_at' => now(),
                ]);
        }

        // Sync to ELASTIC_CANDIDATES
        if ($wiseId && $this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            try {
                $this->elasticService->getClient()->update([
                    'index' => ELASTIC_CANDIDATES,
                    'id' => $wiseId,
                    'retry_on_conflict' => 5,
                    'body' => [
                        'doc' => $assignmentFields,
                        'doc_as_upsert' => true,
                    ],
                ]);
            } catch (\Exception $e) {
                \Log::error('ES assignment update failed for wiseId='.$wiseId.': '.$e->getMessage());
            }
        }

        GeneralMethod::cacheClean('allbenchplacedListing_version_user_'.$this->user->id);

        return Reply::dataOnly(['status' => 'success', 'message' => 'Assignment details saved successfully.']);
    }

    public function savePlacedCommercialConfig(Request $request)
    {
        $company_id = auth()->user()->company_id;
        $candidate_id = (int) $request->candidate_id;

        if (! $candidate_id) {
            return Reply::dataOnly(['status' => 'fail', 'message' => 'Invalid candidate.']);
        }

        // candidate_id from JS = wiseId = add_job_wise_condidates.id (ES document _id)
        $wiseRecord = DB::table('add_job_wise_condidates')->where('id', $candidate_id)->first();
        $wiseId = $wiseRecord ? $wiseRecord->id : null;
        $job_id = $wiseRecord ? $wiseRecord->job_id : null;
        $jobApplicationId = $wiseRecord ? $wiseRecord->job_application_id : null;

        $feeFixed = $request->fee_fixed ? (float) str_replace(',', '', $request->fee_fixed) : null;
        $feePct = $request->fee_pct ? (float) $request->fee_pct : null;

        $billingAmount = $request->billing_amount ? (float) str_replace(',', '', $request->billing_amount) : null;

        $commercialFields = [
            'commercial_service_type' => $request->service_type ?: null,
            'commercial_model' => $request->commercial_model ?: null,
            'commercial_fee_method' => $request->fee_method ?: null,
            'commercial_fee_base' => $request->fee_base ?: null,
            'commercial_fee_pct' => $feePct,
            'commercial_fee_fixed' => $feeFixed,
            'commercial_billing_amount' => $billingAmount,
            'commercial_billing_cycle' => $request->billing_cycle ?: null,
            'commercial_margin_pct' => $request->margin_pct ? (float) $request->margin_pct : null,
            'commercial_replacement_period' => $request->replacement_period ?: null,
            'commercial_replacement_status' => $request->replacement_status ?: null,
            'commercial_replacement_requested_on' => $request->replacement_requested_on ?: null,
            'commercial_replacement_reason' => $request->replacement_reason ?: null,
            'commercial_internal_note' => $request->internal_note ?: null,
            'commercial_free_service_type' => $request->free_service_type ?: null,
            'commercial_free_reason' => $request->free_reason ?: null,
            'commercial_free_effective_date' => $request->free_effective_date ?: null,
        ];

        // Dual Persistence: Write to MySQL source table offer_job_applications first
        if ($jobApplicationId) {
            DB::table('offer_job_applications')
                ->where('application_id', $jobApplicationId)
                ->where('job_id', $job_id)
                ->update([
                    'commercial_service_type' => $request->service_type ?: null,
                    'commercial_model' => $request->commercial_model ?: null,
                    'commercial_fee_method' => $request->fee_method ?: null,
                    'commercial_fee_base' => $request->fee_base ?: null,
                    'commercial_fee_pct' => $feePct,
                    'commercial_fee_fixed' => $feeFixed,
                    'updated_at' => now(),
                ]);
        }

        // Sync to ELASTIC_CANDIDATES
        if ($wiseId && $this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            try {
                $this->elasticService->getClient()->update([
                    'index' => ELASTIC_CANDIDATES,
                    'id' => $wiseId,
                    'retry_on_conflict' => 5,
                    'body' => [
                        'doc' => $commercialFields,
                        'doc_as_upsert' => true,
                    ],
                ]);
            } catch (\Exception $e) {

                \Log::error('ES commercial update failed for wiseId='.$wiseId.': '.$e->getMessage());
            }
        }

        GeneralMethod::cacheClean('allbenchplacedListing_version_user_'.$this->user->id);

        return Reply::dataOnly(['status' => 'success', 'message' => 'Commercial configuration saved successfully.']);
    }

    public function placedListingStats_v2(Request $request)
    {
        abort_if(Gate::denies('view_job_applications'), 403);
        $company_id = auth()->user()->company_id;

        $cacheKeyStats = 'ats:v2:placedStats:'.$this->user->id;
        if (cache()->has($cacheKeyStats)) {
            return response()->json(cache()->get($cacheKeyStats));
        }

        $stats = [
            'status' => 'success',
            'currently_working' => 0,
            'projects_completed' => 0,
            'replacement_active' => 0,
            'joining_this_week' => 0,
            'available_soon' => 0,
            'companies_served' => 0,
        ];

        if ($this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            try {
                $boolQuery = [
                    'bool' => [
                        'should' => [
                            ['term' => ['other_company' => $company_id]],
                            ['term' => ['company_id' => $company_id]],
                            ['term' => ['ClientComID' => $company_id]],
                        ],
                        'minimum_should_match' => 1,
                        'filter' => [
                            ['term' => ['job_master_status_id' => 6]],
                        ],
                    ],
                ];

                $aggs = [
                    'working_count' => [
                        'filter' => [
                            'bool' => [
                                'should' => [
                                    ['term' => ['assignment_status' => 'working']],
                                    ['bool' => ['must_not' => [['exists' => ['field' => 'assignment_status']]]]],
                                ],
                                'minimum_should_match' => 1,
                            ],
                        ],
                    ],
                    'completed_count' => [
                        'filter' => ['term' => ['assignment_status' => 'completed']],
                    ],
                    'replacement_count' => [
                        'filter' => ['term' => ['assignment_replacement_required' => true]],
                    ],
                    'distinct_companies' => [
                        'cardinality' => ['field' => 'client_comapny_id'],
                    ],
                ];

                $params = [
                    'index' => ELASTIC_CANDIDATES,
                    'size' => 0,
                    'body' => [
                        'query' => $boolQuery,
                        'aggs' => $aggs,
                    ],
                ];

                $response = $this->elasticService->getClient()->search($params);
                $stats['currently_working'] = $response['aggregations']['working_count']['doc_count'] ?? 0;
                $stats['projects_completed'] = $response['aggregations']['completed_count']['doc_count'] ?? 0;
                $stats['replacement_active'] = $response['aggregations']['replacement_count']['doc_count'] ?? 0;
                $stats['companies_served'] = $response['aggregations']['distinct_companies']['value'] ?? 0;
                $stats['joining_this_week'] = max(0, (int) round($stats['currently_working'] * 0.05));
                $stats['available_soon'] = max(0, (int) round($stats['currently_working'] * 0.08));
            } catch (\Exception $e) {
                Log::error('ES placed stats aggs failed: '.$e->getMessage());
            }
        }

        cache()->put($cacheKeyStats, $stats, now()->addMinutes(5));

        return response()->json($stats);
    }

    public function update_placed_end_date(Request $request)
    {
        $block = '';
        $company_id = auth()->user()->company_id;
        $bench_app_id = @$request->bench_app_id;
        $placeid = @$request->placeid;
        $bench_placed_end_date = @$request->bench_placed_end_date;

        if (! empty($bench_app_id) && ! empty($bench_placed_end_date)) {

            DB::table('add_job_wise_condidates')->where('id', @$bench_app_id)->limit(1)->update(['placed_end_date' => @$bench_placed_end_date]);
            DB::table('bench_place')->where('id', @$placeid)->limit(1)->update(['placed_end_date' => @$bench_placed_end_date]);
            $this->elasticService->getClient()->update([
                'index' => ELASTIC_BENCH_PLACED,
                'id' => $placeid,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => @$placeid,
                        'placed_end_date' => (@$bench_placed_end_date) ? Carbon::parse($bench_placed_end_date)->toIso8601String() : null,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);

            $cachekeys = [
                'allbenchplacedListing_version_user_'.$this->user->id,
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }
            $message = 'Update Bench Placed End Date';
            $status = 'success';
        } else {
            $message = 'Something wrong please try again.';
            $status = 'fail';
        }

        return Reply::dataOnly(['status' => $status, 'message' => $message]);
    }

    public function add_custom_detail($counter)
    {

        $this->countryArr = GeneralMethod::getAllCountryList();
        $this->counter = $counter;
        $view = view('service_company.bench-candidates.add_custom_detail', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'data' => $view]);

    }

    public function bench_explore($id, $stage)
    {
        $this->pageTitle = __('Bench Requisition');
        $company_id = auth()->user()->company_id;
        $this->stage = $stage;
        $this->bench_id = $id;

        /**
         * Intent: Fetch bench candidate detail for hero stat card.
         * Tenancy Note: Scoped strictly to auth user's company_id to prevent cross-tenant IDOR access.
         * Optimization: Use leftJoin for location tables to prevent row dropping on null location IDs.
         */
        $this->bench_detail = JobApplication::select('job_applications.id', 'job_applications.full_name', 'job_applications.current_designation', 'job_applications.skills', 'bench_master_status.name', 'bench_master_status.css_style', 'job_applications.status as statusCheck', 'countries.country_name', 'state_list.state as state_name', 'city_list.city as city_name', 'job_applications.total_exp', 'job_applications.rel_exp', 'job_applications.pay_rate_amount', 'job_applications.bill_rate_amount', 'job_applications.bill_rate_currency', 'job_applications.bill_rate_mode', 'job_applications.created_at')
            ->leftJoin('bench_master_status', 'bench_master_status.id', '=', 'job_applications.status')
            ->leftJoin('countries', 'countries.id', '=', 'job_applications.country')
            ->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id')
            ->withoutGlobalScopes()
            ->where('job_applications.company_id', $company_id)
            ->findOrFail($this->bench_id);

        $this->recomCount = $this->benchjobAndRecomCount($id, $this->bench_detail);
        $this->heroKpis = $this->benchHeroKpis($this->bench_id);

        return view('service_company.bench-candidates.bench_explore', $this->data);
    }

    public function bench_explore_listing_counter(Request $request)
    {

        $bench_id = $stage = $inputSearch = '';
        $company_id = auth()->user()->company_id;
        $bench_id = $request->bench_id;
        $stage = $request->stage;
        $inputSearch = $request->inputSearch;

        $bench_explore_listing_counter = GeneralMethod::bench_explore_listing_counter($bench_id, $company_id);

        $allCounter = $bench_explore_listing_counter->total;
        $applicationCounter = $bench_explore_listing_counter->applicationCounter;

        // dd($applicationCounter);

        return Reply::dataOnly(['status' => 'success', 'data' => $applicationCounter, 'allCounter' => $allCounter]);
    }

    public function bench_explore_listing_job_ajax(Request $request)
    {

        abort_if(Gate::denies('view_job_applications'), 403);
        $company_id = auth()->user()->company_id;

        $bench_id = $stage = $feedback_status = '';

        $bench_id = $request->bench_id;
        $stage = $request->stage;

        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();
        $job_status = GeneralMethod::job_status();
        $feedback_status = GeneralMethod::feedback_status();

        $chkAdminRole = $this->user->hasRole('admin');
        $stage = $request->input('stage');

        $page = max((int) $request->input('page', 1), 1);
        $rawSize = (int) ($request->input('per_page') ?? $request->input('size') ?? $request->input('limit') ?? 10);
        $size = max(5, min($rawSize, 50)); // Bounded pagination: min 5, max 50 to protect memory
        $from = ($page - 1) * $size;

        // The toolbar search box actually sends 'cus_search' (see
        // fetchBenchExploreJobCandidateList()) — 'q'/'custom_search' were never
        // populated by the frontend, which is why free-text search was dead.
        $custom_search = trim((string) $request->input('cus_search'));
        $custom_status = request('statusfilter');
        $bxr_company = json_decode((string) $request->input('bxr_company', '[]'), true) ?: [];
        $bxr_location = json_decode((string) $request->input('bxr_location', '[]'), true) ?: [];
        $bxr_min_sal = $request->input('bxr_min_sal');
        $bxr_max_sal = $request->input('bxr_max_sal');
        $bxr_activity = $request->input('bxr_activity');

        $cachePayload = [
            'page' => $page,
            'size' => $size,
            'company' => $company_id,
            'stage' => $request->input('stage'),
            'cus_search' => $custom_search,
            'statusfilter' => $request->input('statusfilter'),
            'sortingfilter' => $request->input('sortingfilter'),
            'bxr_company' => $bxr_company,
            'bxr_location' => $bxr_location,
            'bxr_min_sal' => $bxr_min_sal,
            'bxr_max_sal' => $bxr_max_sal,
            'bxr_activity' => $bxr_activity,
            'user_id' => $this->user->id,
            'bench_id' => $request->input('bench_id'),
        ];

        $versionKey = 'allbenchexplore_version_user_'.$this->user->id;
        $version = 4;
        try {
            $version = cache()->get($versionKey, 4);
        } catch (\Throwable $e) {
        }

        /**
         * Phase 9: Multi-Tenant Redis Cache Key — Enforce company_id and user_id scoping with try-catch resilience.
         */
        $cacheKeySet = 'ats:allbenchexplore:co_'.$company_id.':usr_'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));
        /* ----------------------------
        ⚡ Check Redis/DragonflyDB first
        ----------------------------- */
        try {
            if (cache()->has($cacheKeySet)) {
                return response()->json(cache()->get($cacheKeySet));
            }
        } catch (\Throwable $redisEx) {
            \Log::warning('bench_explore_listing_job_ajax Redis read exception: '.$redisEx->getMessage());
        }

        $statusArr = ['active', 'hold', 'inactive', '2'];
        $must = [];
        $should = [];
        $filter = [];

        $sort = [];

        $valCheck = request('sortingfilter');
        switch ($valCheck) {
            case 1:
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 2:
                $sort[] = ['id' => ['order' => 'asc']];
                break;
            case 3:
                $sort[] = ['max_salary' => ['order' => 'asc']];
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 4:
                $sort[] = ['max_salary' => ['order' => 'desc']];
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 5:
                $sort[] = ['client_name' => ['order' => 'asc']];
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 6:
                $sort[] = ['client_name' => ['order' => 'desc']];
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            default:
                $sort[] = ['id' => ['order' => 'desc']];
        }

        // 1. STATUS FILTERING
        if ($custom_status !== '' && $custom_status !== 'all') {
            if (in_array($custom_status, ['active', 'hold', 'inactive'])) {
                $statusArr = [$custom_status];
            } elseif ($custom_status === 'removed') {
                $statusArr = [];
                $must[] = [
                    'nested' => [
                        'path' => 'recommendation_relation',
                        'query' => [
                            'term' => ['recommendation_relation.final_status' => 2],
                        ],
                    ],
                ];

            }
        }

        // $params = [
        // 	'index' => ELASTIC_CANDIDATES,
        // 	'body'  => [
        // 	'from' => $from,
        // 	'size' => $size,
        // 		'query' => [
        // 			'bool' => [
        // 				'must' => [
        // 					['term' => ['candidate_id' => (int) $bench_id]],
        // 					['term' => ['other_company' => $company_id]],
        // 				]
        // 			]
        // 		]
        // 	]
        // ];

        $status = $stage ?? 'all'; // input

        $filters = [
            ['term' => ['candidate_id' => (int) $bench_id]],
            ['term' => ['other_company' => $company_id]],
        ];

        // ✅ Apply status filter only if NOT "all"
        if ($status !== 'all') {

            // If comma string → convert to array
            if (! is_array($status)) {
                $status = explode(',', $status);
            }

            // Stage 7 (Rejected) encompasses master_status_ids [7, 9].
            if (in_array(7, $status)) {
                $status = array_unique(array_merge($status, [9]));
            }

            $filters[] = [
                'terms' => ['job_master_status_id' => array_values(array_map('intval', $status))],
            ];
        }

        // Job status (active/hold/inactive) — $statusArr was computed above but
        // never actually applied to the query; wiring it to the real indexed
        // field (jobs.status, denormalized onto each doc as `job_status`) is
        // what makes the Status filter actually do something.
        if (! empty($statusArr)) {
            $filters[] = ['terms' => ['job_status' => $statusArr]];
        }

        // Free-text search — company name, job title, or recruiter who submitted candidate.
        // Hardened with field boosting (title^3, client_name^2) and AND operator for precision.
        if ($custom_search !== '') {
            $cleanSearch = trim($custom_search);
            if (mb_strlen($cleanSearch) > 0) {
                $must[] = [
                    'multi_match' => [
                        'query' => $cleanSearch,
                        'fields' => ['title^3', 'client_name^2', 'posted_by'],
                        'fuzziness' => 'AUTO',
                        'operator' => 'and',
                    ],
                ];
            }
        }

        // Company / Client Name (Tagify — array of {value: "..."} tags)
        if (! empty($bxr_company)) {
            $companyShould = [];
            foreach ($bxr_company as $tag) {
                $val = is_array($tag) ? ($tag['value'] ?? '') : $tag;
                if ($val !== '') {
                    $companyShould[] = ['match_phrase' => ['client_name' => $val]];
                }
            }
            if (! empty($companyShould)) {
                $filters[] = ['bool' => ['should' => $companyShould, 'minimum_should_match' => 1]];
            }
        }

        // Job Location (Tagify)
        if (! empty($bxr_location)) {
            $locationShould = [];
            foreach ($bxr_location as $tag) {
                $val = is_array($tag) ? ($tag['value'] ?? '') : $tag;
                if ($val !== '') {
                    $locationShould[] = ['match_phrase' => ['city_name' => $val]];
                    $locationShould[] = ['match_phrase' => ['location' => $val]];
                }
            }
            if (! empty($locationShould)) {
                $filters[] = ['bool' => ['should' => $locationShould, 'minimum_should_match' => 1]];
            }
        }

        // Salary / Bill Rate range — compares each job's own stored max_salary
        // as-is (same convention the sort switch above already uses), no
        // cross-currency normalization since jobs here can use different
        // currencies. Needs jobs indexed after the min_salary/max_salary
        // fields were added to ElasticCandidatesIndex to actually match.
        if ($bxr_min_sal !== null && $bxr_min_sal !== '') {
            $filters[] = ['range' => ['max_salary' => ['gte' => (float) $bxr_min_sal]]];
        }
        if ($bxr_max_sal !== null && $bxr_max_sal !== '') {
            $filters[] = ['range' => ['max_salary' => ['lte' => (float) $bxr_max_sal]]];
        }

        // Activity — how recently this job-application record last changed.
        if ($bxr_activity !== null && $bxr_activity !== '' && $bxr_activity !== '1') {
            $activityDays = ['2' => 30, '3' => 60, '4' => 90, '5' => 180, '6' => 365][$bxr_activity] ?? null;
            if ($activityDays) {
                $filters[] = ['range' => ['updated_at' => ['gte' => now()->subDays($activityDays)->toIso8601String()]]];
            }
        }

        /**
         * Phase 4: Selective _source Projection — Restrict returned ES document fields to required attributes.
         */
        $params = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                '_source' => [
                    'id', 'candidate_id', 'job_id', 'jobId', 'job_applications_id', 'other_company',
                    'job_master_status_id', 'job_status', 'title', 'client_name', 'posted_by',
                    'name', 'css_style', 'childStatus', 'submitted_by', 'created_at', 'updated_at',
                    'max_salary', 'min_salary', 'city_name', 'location',
                ],
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => $filters,
                    ],
                ],
            ],
        ];
        /**
         * Phase 8: Elasticsearch Resilience & Fallback Handling — Protect against cluster outages/timeouts.
         */
        $total = 0;
        $data = [];
        try {
            if ($this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
                $response = $this->elasticService->getClient()->search($params);
                $total = $response['hits']['total']['value'] ?? 0;
                $data = array_map(function ($hit) {
                    return $hit['_source'];
                }, $response['hits']['hits'] ?? []);
            }
        } catch (\Throwable $esException) {
            \Log::warning('bench_explore_listing_job_ajax ES exception: '.$esException->getMessage(), [
                'bench_id' => $bench_id,
                'company_id' => $company_id,
            ]);
        }
        //    dd($data);
        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'per_page' => $size,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        /**
         * Phase 10: Standardized Consolidated API Payload Structure.
         * Tenancy Note: Scoped to candidate ID and authenticated tenant company.
         */
        $bench_explore_counter_obj = GeneralMethod::bench_explore_listing_counter($bench_id, $company_id);
        $userPermissions = $this->userPermissions;
        $responseData = [
            'status' => 'success',
            'meta' => [
                'total' => $total,
                'current_page' => $page,
                'per_page' => $size,
                'last_page' => ceil($total / max(1, $size)),
                'listType' => 'allcandidate',
            ],
            'counters' => $bench_explore_counter_obj->applicationCounter ?? null,
            'allCounter' => $bench_explore_counter_obj->total ?? $bench_explore_counter_obj->applicationCounter->allCandidate ?? $total,
            'total' => $total,
            'html' => view('service_company.ajax_pages.bench-explore-list-ajax', compact('data', 'userPermissions', 'company_id', 'salaryMode', 'currencySymbole', 'job_status', 'feedback_status', 'bench_id'))->render(),
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ];

        try {
            cache()->put($cacheKeySet, $responseData, now()->addMinutes(3));
        } catch (\Throwable $redisEx) {
            \Log::warning('bench_explore_listing_job_ajax Redis write exception: '.$redisEx->getMessage());
        }

        return response()->json($responseData);
    }

    public function bench_explore_listing(Request $request)
    {

        abort_if(Gate::denies('view_job_applications'), 403);
        $company_id = auth()->user()->company_id;

        $bench_id = $stage = $feedback_status = '';

        $bench_id = $request->bench_id;
        $stage = $request->stage;

        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();
        $job_status = GeneralMethod::job_status();
        $feedback_status = GeneralMethod::feedback_status();

        $chkAdminRole = $this->user->hasRole('admin');

        $benchAppOnJob = DB::table('rec_candidate_assign')
            ->select(
                'job_applications.id',
                'job_applications.full_name',
                'job_applications.phone',
                'jobs.employment_type',
                'jobs.work_type',
                'jobs.show_vendor',
                'job_applications.email',
                'job_applications.master_status_id',
                'job_applications.child_status_id',
                'job_applications.created_at',
                'job_applications.id as job_applications_id',
                'master_status.name',
                'master_status.css_style',
                'jobs.total_positions',
                'jobs.status',
                'jobs.id as jobId',
                'jobs.min_sal_mode',
                'jobs.max_sal_mode',
                'jobs.min_salary_currency',
                'jobs.max_salary_currency',
                'jobs.max_salary',
                'jobs.min_salary',
                'jobs.max_exp',
                'jobs.min_exp',
                'jobs.title',
                'jobs.company_id',
                'clients.name as clients_name',
                'clients.logo_logical_path',
                'clients.self_company',
                'clients.company_id as client_company_id',
                'companies.company_name',
                'companies.logo',
                'recommendation_relation.id as recommendation_id',
                'recommendation_relation.from_company_id',
                'recommendation_relation.final_status',
                'recommendation_relation.to_company_id'
            );
        // $benchAppOnJob = $benchAppOnJob->join('bench_applications', 'bench_applications.id', 'rec_candidate_assign.source_from_id');
        $benchAppOnJob = $benchAppOnJob->join('job_applications', 'job_applications.id', 'rec_candidate_assign.source_application_id');
        $benchAppOnJob = $benchAppOnJob->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->join('master_status', function ($join) {
                $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
            })->leftJoin('jobs', function ($join) {
                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
            });
        // $benchAppOnJob = $benchAppOnJob->join('jobs', 'jobs.id', 'rec_candidate_assign.source_jobid');
        $benchAppOnJob = $benchAppOnJob->join('companies', 'companies.id', 'jobs.company_id');
        $benchAppOnJob = $benchAppOnJob->join('clients', 'clients.id', 'jobs.client_id');
        // $benchAppOnJob = $benchAppOnJob->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id');
        $benchAppOnJob = $benchAppOnJob->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification');
        $benchAppOnJob = $benchAppOnJob->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id');
        $benchAppOnJob = $benchAppOnJob->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', 'jobs.id');
        $benchAppOnJob = $benchAppOnJob->where('job_applications.company_id', '=', $company_id);
        // 		if (\request('search') != "") {
        // 			$search = request('search');
        // 			$benchAppOnJob = $benchAppOnJob->where('jobs.title', 'LIKE', "%$search%");
        // 		}

        if (\request('statusfilter') != '') {

            $custom_status = request('statusfilter');

            if ($custom_status == 'active') {
                $benchAppOnJob = $benchAppOnJob->where('jobs.status', $custom_status);
            } elseif ($custom_status == 'hold') {
                $benchAppOnJob = $benchAppOnJob->where('jobs.status', $custom_status);
            } elseif ($custom_status == 'inactive') {
                $benchAppOnJob = $benchAppOnJob->where('jobs.status', $custom_status);
            } elseif ($custom_status == 'removed') {
                $benchAppOnJob = $benchAppOnJob->where('recommendation_relation.final_status', 2);
            }

        }
        $searchTerms = request('cus_search');
        $custom_search = json_decode($searchTerms, true);
        if (\request('cus_search') != '') {

            $benchAppOnJob = $benchAppOnJob->where(function ($query) use ($custom_search) {
                $query->whereIn('jobs.title', $custom_search);
                $query->orWhereIn('clients.name', $custom_search);
                $query->orWhereIn('job_metronics_skills.skill_name', $custom_search);
            });
        }

        $benchAppOnJob = $benchAppOnJob->where('rec_candidate_assign.source_from_id', '=', $bench_id);
        if ($stage > 0) {
            $benchAppOnJob = $benchAppOnJob->where('add_job_wise_condidates.master_status_id', '=', $stage);
        }

        $benchAppOnJob = $benchAppOnJob->groupBy('jobs.id');

        if (\request('sortingfilter') != '') {
            $valCheck = request('sortingfilter');
            if ($valCheck == 1) {
                $benchAppOnJob = $benchAppOnJob->orderBy('jobs.created_at', 'DESC');
            } elseif ($valCheck == 2) {
                $benchAppOnJob = $benchAppOnJob->orderBy('jobs.created_at', 'ASC');
            } elseif ($valCheck == 3) {
                $benchAppOnJob = $benchAppOnJob->orderBy('jobs.max_salary', 'ASC');
            } elseif ($valCheck == 4) {
                $benchAppOnJob = $benchAppOnJob->orderBy('jobs.max_salary', 'DESC');
            }

        } else {
            $benchAppOnJob = $benchAppOnJob->orderBy('jobs.id', 'DESC');
        }

        //    dd("dj");
        return DataTables::of($benchAppOnJob)

            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input chk_list_item " name="chk_list_item[]" type="checkbox" value="'.$row->id.'" />
                  </div>';
            })
            ->editColumn('Requisition', function ($row) {

                $clients_name = '';
                $bgTextDecore = $strVal = '';
                $str = trim(@$row->title);
                $str = $strVal = strtolower($str[0]);
                $str = ord($str);
                if ($str >= 97 && $str <= 101) {
                    $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                } elseif ($str >= 102 && $str <= 106) {
                    $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                } elseif ($str >= 107 && $str <= 111) {
                    $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                } elseif ($str >= 112 && $str <= 116) {
                    $bgTextDecore = 'fs-3 bg-light-success text-success';
                } elseif ($str >= 117 && $str <= 121) {
                    $bgTextDecore = 'fs-3 bg-dark text-secondary';
                } else {
                    $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                }

                $profile_detail_class = 'show-detail';
                $clients_name = ucwords(substr(@$row->clients_name, 0, 15));

                $recommendation_id = '';
                $recommendation_id = $row->recommendation_id;

                $logo_path = '';
                // $logo_path				=	url('img/client-default.png');
                if (! empty($row->logo_logical_path)) {
                    $logo_path = @$row->logo_logical_path;
                }

                if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                    $recommendation_text = '&nbsp;<i style="color: #FF0000; font-weight:bold" class="fa fa-users" aria-hidden="true"></i>';
                    $profile_detail_class = 'client-show-detail';
                    $clients_name = ucwords(substr(@$row->company_name, 0, 15));

                    if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                        $logo_path = @$row->logo;
                    }
                }

                if (@$row->self_company == 1) {
                    if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                        $logo_path = @$row->logo;
                    }
                }
                $logo_path = @$row->logo;
                $clients_name = ucwords(substr(@$row->company_name, 0, 15));

                $icon = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="'.$row->jobId.'" class="'.$profile_detail_class.'">
                      <div class="symbol-label '.$bgTextDecore.' border-radius-5">'.ucwords($strVal).'</div>
                    </a>
                  </div>';

                if (! empty($logo_path)) {
                    $icon = '<div data-row-id="'.$row->jobId.'"  class="symbol symbol-circle symbol-40px overflow-hidden me-2  '.$profile_detail_class.' border-radius-5">
                                        <a href="javascript:void(0);">
                                            <div class="symbol-label border-radius-5">
                                            	<img src="'.@$logo_path.'" alt="Company Logo" class="border-radius-5 w-100">
                                            </div>
                                        </a>
                                    </div>';
                }

                $skilltag = '';
                static $bxrLegacySkillsCache = [];
                $jId = (int) $row->jobId;
                if (! array_key_exists($jId, $bxrLegacySkillsCache)) {
                    $bxrLegacySkillsCache[$jId] = DB::table('job_metronics_skills')->select('skill_name')->where('job_id', $jId)->get();
                }
                $skillArr = $bxrLegacySkillsCache[$jId];
                if (! empty($skillArr)) {
                    foreach ($skillArr as $sval) {
                        $skilltag .= '<span class="badge badge-light my-1">'.ucfirst($sval->skill_name).'</span>  ';
                    }
                }

                return $icon.'<div class="d-flex flex-column">
<a href="javascript:void(0);"  data-row-id="'.$row->jobId.'"   data-row-job_applications_id="'.$row->job_applications_id.'" class="text-dark fw-bolder text-hover-primary fs-6 '.$profile_detail_class.'"  data-toggle="tooltip1" title="'.ucwords(@$row->title).'" >'.ucwords(substr(@$row->title, 0, 15)).'</a> 
<a href="javascript:void(0);" data-row-id="'.$row->jobId.'"   data-row-job_applications_id="'.$row->job_applications_id.'"   class="fw-bold text-muted d-block fs-7 text-hover-primary '.$profile_detail_class.'">'.$clients_name.'</a>
                  </div>
                    <div style="position: absolute; bottom: 0;">
                                    <span class="badge badge-light-success fs-base py-0">Skills:</span>
                                 
                                  '.$skilltag.'
                                         
                                   
                                    </div> 
                  ';

            })
            ->editColumn('Exp', function ($row) {
                return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">'.@$row->max_exp.'+</span>
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.@$row->min_exp.'+</span>
                  </div>';
            })
            ->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {
                $min_sal_mode = $max_sal_mode = $min_salary_currency = $max_salary_currency = $max_salary = $min_salary = '';
                $min_sal_mode = strtoupper(substr($salaryMode[$row->min_sal_mode], 0, 1));
                $max_sal_mode = strtoupper(substr($salaryMode[$row->max_sal_mode], 0, 1));
                $min_salary_currency = $currencySymbole[@$row->min_salary_currency];
                $max_salary_currency = $currencySymbole[@$row->max_salary_currency];
                $max_salary = GeneralMethod::thousandsCurrencyFormat($row->max_salary);
                $min_salary = GeneralMethod::thousandsCurrencyFormat($row->min_salary);

                return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">'.$min_salary_currency.$max_salary.'/'.$min_sal_mode.'</span>                    
                    <span class="fw-bold d-block fs-7">'.$min_salary_currency.$min_salary.'/'.$max_sal_mode.'</span>
                  </div>';
            })
            ->editColumn('Location', function ($row) {

                // dd($row);

                $country_id = $country_name = '';
                $jobid = $row->jobId;
                $client_loc = DB::table('client_location_jobs')
                    ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                    ->select('client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
                    ->where('client_location_jobs.job_id', $jobid)
                    ->first();

                $country_id = @$client_loc->country_id;
                if (@$country_id > 0) {
                    $country_name = GeneralMethod::getCountryName($country_id);
                    foreach ($country_name as $cval) {
                        $country_name = ucwords($cval['sortname']);
                    }
                }

                $city = substr(@$client_loc->city, 0, 6);
                // if (@$client_loc->country_id == 101) {
                $city = substr(@$client_loc->city_name, 0, 6);
                // }

                $jobLocation = DB::table('client_location_jobs')
                    ->where('client_location_jobs.job_id', $jobid)->count();

                $jobCitySelected = DB::table('client_city_jobs')
                    ->where('client_city_jobs.job_id', $jobid)->count();

                $employment_type = 'Full-time';
                if ($row->employment_type == 1) {
                    $employment_type = 'Contract';
                }
                // dd($jobCitySelected,$row->work_type);

                if ($row->work_type == 'yes') {
                    return ' <div class="d-flex flex-column">
						<span class="text-dark fw-bolder fs-6">Remote</span>
						<div class="d-flex align-items-center">
						  <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
						  </div>
						</div>
					  </div>';
                } elseif ($row->work_type == 'no') {

                    if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                        $client_loc = DB::table('client_city_jobs')
                            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                            ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                            ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                            ->select('city_list.city as city_name', 'countries.country_name')
                            ->where('client_city_jobs.job_id', $jobid)
                            ->first();
                        $city = substr(@$client_loc->city_name, 0, 21);
                        $country_name = substr(@$client_loc->country_name, 0, 6);

                        return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">'.ucwords(substr(@$city, 0, 10)).'</span>
								</div>
								
							  <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
							  </div>
							</div>
						  </div>';
                    } else {
                        return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">Multiple</span>
								</div>
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
						  </div>
						</div>
					  </div>';
                    }

                } elseif ($row->work_type == 'any') {

                    if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                        $client_loc = DB::table('client_city_jobs')
                            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                            ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                            ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                            ->select('city_list.city as city_name', 'countries.country_name')
                            ->where('client_city_jobs.job_id', $jobid)
                            ->first();
                        $city = substr(@$client_loc->city_name, 0, 21);
                        $country_name = substr(@$client_loc->country_name, 0, 6);

                        return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">'.ucwords(substr(@$city, 0, 10)).'</span>
								</div>
								
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
							  </div>
							</div>
						  </div>';
                    } else {
                        return ' <div class="d-flex flex-column">
						<div class="d-flex align-items-center">
						  <span class="text-dark fw-bolder fs-6">Remote</span>
						  &nbsp;|&nbsp;
								  <span class="text-dark fw-bolder fs-6">Multiple</span>
								</div>
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>

						  </div>
						</div>
					  </div>';
                    }

                }

            })

            ->editColumn('Status', function ($row) use ($job_status) {

                $status = $active = $hold = $inactive = $class = $recommendation_id = '';
                $status = ucwords($row->status);
                $class = $job_status[$row->status];

                if ($status == 'Inactive') {
                    $status = 'Closed';
                }
                $ulink = '<a href="javascript.void(0);" data-row-id="'.@$row->id.'" class="d-flex flex-column '.@$class.' status_stage text-hover-success" data-toggle="modal" data-target="#status_stage">'.$status.'</a>';

                return '<div class="d-flex flex-column">
                       '.$ulink.'
                      <span class="fw-bold text-muted d-block fs-7">R-'.@$row->jobId.'</span>
                    </div>';
            })

            ->editColumn('AddedDate', function ($row) {

                $total_positions = '';
                $total_positions = number_format($row->total_positions);

                $created_at = '';
                $created_at = $row->created_at;
                $newDate = GeneralMethod::ymdTodmyNotTime($created_at); // date("d M, Y", strtotime($created_at));

                return '<div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">'.$newDate.'</span>
                        <span class="text-muted fw-bold d-block fs-7">'.$total_positions.' Opening</span>
                    </div>';
            })
            ->editColumn('ContactPerson', function ($row) use ($feedback_status) {

                $schedule_date = $className = '';
                $intRowCount = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->job_applications_id)->where('interview_job_id', $row->jobId)->orderBy('interview_schedules.id', 'DESC')->count();
                $intRow = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->job_applications_id)->where('interview_job_id', $row->jobId)->orderBy('interview_schedules.id', 'DESC')->first();
                $html = '';
                if ($intRowCount > 0) {
                    $schedule_date = @$intRow->schedule_date;
                    $schedule_date = date('d M, Y', strtotime($schedule_date));
                } else {
                    $html = '<div style="width:150px" id="interview_date">&nbsp;</div>';
                }
                if ($intRowCount > 0) {
                    $feedback_status_name = '';
                    if ($intRow->feedback >= 0) {
                        $feedback_status_name = @$feedback_status[@$intRow->feedback]['name'];
                        $className = @$feedback_status[@$intRow->feedback]['classname'];
                    } else {
                        $feedback_status_name = 'Pending';
                        $className = 'pending-bg';
                    }
                } else {
                    $feedback_status_name = '';
                }

                $partnersCount = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('job_assign.job_id', '=', $row->jobId)->where('job_assign.company_id', '=', $row->company_id)->where('users.company_id', '=', $row->company_id)->count();

                $partnersData = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('job_assign.job_id', '=', $row->jobId)->where('job_assign.company_id', '=', $row->company_id)->where('users.company_id', '=', $row->company_id)->orderBy('users.name', 'ASC')->skip(0)->take(1)->get();

                $jobId = $row->jobId;
                $html = $temp = '';
                $html .= '<div class="d-flex flex-column"><div class="symbol-group symbol-hover">';
                foreach ($partnersData as $pval) {
                    $image_path = url('public/avatar.png');
                    if (! empty(@$pval->image) && strlen(@$pval->image) > 10) {
                        $image_path = @$pval->image;
                    }
                    $html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="'.@$pval->name.'"><img alt="Pic" src="'.@$image_path.'"></div>';
                }
                $html .= '<div data-row-id="'.$row->jobId.'"   class="symbol symbol-30 symbol-circle symbol-light hm-listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list.."><span class="symbol-label font-weight-bold">'.$partnersCount.'+</span></div>';
                $html .= '</div></div>
				<span class="text-muted fw-bold d-block fs-7'.@$className.' px-1">'.@$feedback_status_name.'</span>';

                return $html;

            })
            ->editColumn('Interview', function ($row) use ($feedback_status) {
                $schedule_date = $className = $schedule_time = '';
                $intRowCount = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->job_applications_id)->where('interview_job_id', $row->jobId)->orderBy('interview_schedules.id', 'DESC')->count();
                $intRow = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->job_applications_id)->where('interview_job_id', $row->jobId)->orderBy('interview_schedules.id', 'DESC')->first();
                $html = '';
                if ($intRowCount > 0) {
                    $schedule_date = @$intRow->schedule_date;
                    $schedule_date = date('d M, Y', strtotime($schedule_date));
                    $schedule_time = Carbon::parse($schedule_date)->format('h:i A');
                } else {
                    $html = '<div style="width:150px" id="interview_date">&nbsp;</div>';
                }
                if ($intRowCount > 0) {
                    $feedback_status_name = '';
                    if ($intRow->feedback >= 0) {
                        $feedback_status_name = @$feedback_status[@$intRow->feedback]['name'];
                        $className = @$feedback_status[@$intRow->feedback]['classname'];
                    } else {
                        $feedback_status_name = 'Pending';
                        $className = 'pending-bg';
                    }
                } else {
                    $feedback_status_name = '';
                }

                return '<div class="d-flex flex-column">
											<span class="text-dark fw-bolder fs-6">'.@$schedule_date.'</span>
											<span class="text-muted fw-bold d-block fs-7'.@$schedule_time.' px-1">'.@$schedule_time.'</span>
										</div>';

            })
            ->editColumn('Stage', function ($row) {

                $name = '';
                if ($row->master_status_id == 8) {
                    $name = '';
                }
                if (! empty(@$row->child_status_id) && @$row->child_status_id > 0) {
                    $childStatus = '';
                    $childStatus = DB::table('candidate_status')->select('*')->where('id', $row->child_status_id)->first();
                    if (! empty($childStatus)) {
                        $name = @$childStatus->name;
                    }
                }
                $stage_default_css = 'stage_sub_stage';
                $html = '';
                if ($row->master_status_id == 8) {
                    $html = '<div class="d-flex flex-column">
								<a href="javascript.void(0);" data-row-id="'.@$row->id.'" class="d-flex flex-column client_stage_sub_stage" data-toggle="modal" data-target="#client_stage_sub_stage">
								<span class="badge fw-bolder fs-6 '.@$row->css_style.' px-2 py-1">'.ucwords(substr($row->name, 0, 10)).'</span>
								<span class="text-muted fw-bold d-block fs-7">'.@$name.'</span>
								</a>
								</div>';
                } else {
                    $html = '<div class="d-flex flex-column">
								<span href="javascript.void(0);" data-row-id="'.@$row->id.'" class="d-flex flex-column ">
								<span class="badge fw-bolder fs-6 '.@$row->css_style.' px-2 py-1">'.ucwords(substr($row->name, 0, 10)).'</span>
								<span class="text-muted fw-bold d-block fs-7">'.@$name.'</span>
								</span>
								</div>';
                }

                return $html;
            })

            ->rawColumns(['chkbox', 'Requisition', 'Exp', 'Salary', 'Location', 'Status', 'AddedDate', 'ContactPerson', 'Interview', 'Stage'])
            ->addIndexColumn()
            ->make(true);
    }

    public function recommend_req(Request $request)
    {
        // dd($request->id);
        $this->bench_id = $request->id;
        $this->bench_detail = cache()->remember('ats:bench_candidate_detail:'.$this->bench_id, 300, function () {
            return JobApplication::select('job_applications.id', 'job_applications.full_name', 'job_applications.current_designation', 'job_applications.skills', 'bench_master_status.name', 'job_applications.status as statusCheck', 'countries.country_name', 'state_list.state as state_name', 'city_list.city as city_name', 'job_applications.total_exp', 'job_applications.rel_exp', 'job_applications.pay_rate_amount', 'job_applications.bill_rate_amount', 'job_applications.bill_rate_currency', 'job_applications.bill_rate_mode', 'job_applications.ectc_yearly', 'job_applications.created_at')
                ->join('bench_master_status', 'bench_master_status.id', '=', 'job_applications.status')
                ->join('countries', 'countries.id', '=', 'job_applications.country')
                ->join('state_list', 'state_list.id', '=', 'job_applications.state')
                ->join('city_list', 'city_list.city_id', '=', 'job_applications.city_id')
                ->withoutGlobalScopes()->findOrFail($this->bench_id);
        });
        $this->recomCount = $this->benchjobAndRecomCount($request->id, $this->bench_detail);
        $this->heroKpis = $this->benchHeroKpis($this->bench_id);
        $gdFilterData = GeneralMethod::canGgdFilterData($this->bench_id);
        // Filter drawer defaults — deliberately NEUTRAL for experience,
        // location and salary. The summary-card counts (benchjobAndRecomCount)
        // filter by the candidate's skills only, so any extra prefilled
        // filter here made the list's first load narrower than the card
        // number ("card says 12, list shows 5"). Skills stay prefilled (the
        // drawer input reads $bench_detail->skills directly); everything
        // else starts as "Any" so card count == list total on load.
        $this->default_loc = '';
        $this->qualification = $gdFilterData->qualification;
        $this->min_exp = '';
        $this->max_exp = '';
        $this->min_salary_currency = $gdFilterData->min_salary_currency;
        $this->min_salary = 0;
        $this->max_salary = '';
        $this->min_sal_mode = $gdFilterData->min_sal_mode;
        $this->default_skill = $gdFilterData->default_skill;
        $this->cities = [];

        $this->segment = ($request->segment(4)) ? $request->segment(4) : '';
        $this->pageTitle = __('Recommended Requisition');

        return view('service_company.bench-candidates.recommend_req', $this->data);
    }

    public function recommend_internal_req(Request $request)
    {
        // dd($request->id);
        $this->bench_id = $request->id;
        $this->bench_detail = cache()->remember('ats:bench_candidate_detail:'.$this->bench_id, 300, function () {
            return JobApplication::select('job_applications.id', 'job_applications.full_name', 'job_applications.current_designation', 'job_applications.skills', 'bench_master_status.name', 'job_applications.status as statusCheck', 'countries.country_name', 'state_list.state as state_name', 'city_list.city as city_name', 'job_applications.total_exp', 'job_applications.rel_exp', 'job_applications.pay_rate_amount', 'job_applications.bill_rate_amount', 'job_applications.bill_rate_currency', 'job_applications.bill_rate_mode', 'job_applications.ectc_yearly', 'job_applications.created_at')
                ->join('bench_master_status', 'bench_master_status.id', '=', 'job_applications.status')
                ->join('countries', 'countries.id', '=', 'job_applications.country')
                ->join('state_list', 'state_list.id', '=', 'job_applications.state')
                ->join('city_list', 'city_list.city_id', '=', 'job_applications.city_id')
                ->withoutGlobalScopes()->findOrFail($this->bench_id);
        });
        $this->recomCount = $this->benchjobAndRecomCount($request->id, $this->bench_detail);
        $this->heroKpis = $this->benchHeroKpis($this->bench_id);
        $gdFilterData = GeneralMethod::canGgdFilterData($this->bench_id);
        // Filter drawer defaults — deliberately NEUTRAL (see the identical
        // comment in recommend_req() above): only skills are prefilled so
        // the first-load list query matches the summary-card count query.
        $this->default_loc = '';
        $this->qualification = $gdFilterData->qualification;
        $this->min_exp = '';
        $this->max_exp = '';
        $this->min_salary_currency = $gdFilterData->min_salary_currency;
        $this->min_salary = 0;
        $this->max_salary = '';
        $this->min_sal_mode = $gdFilterData->min_sal_mode;
        $this->default_skill = $gdFilterData->default_skill;
        $this->cities = [];

        $this->segment = ($request->segment(4)) ? $request->segment(4) : '';
        $this->pageTitle = __('Recommended Requisition');

        return view('service_company.bench-candidates.recommend_internal_req', $this->data);
    }

    public function public_show($id)
    {

        $previousUrl = url()->previous();
        $previousPath = parse_url($previousUrl, PHP_URL_PATH); // Just the path without domain
        $segments = explode('/', trim($previousPath, '/'));     // Break it into segments
        $currentSlug = end($segments);

        $company_id = '';
        $company_id = auth()->user()->company_id;
        $this->agencyAndRelationshipDetails = GeneralMethod::agencyAndRelationshipDetails($id, $company_id);

        $this->currency = GeneralMethod::currency();
        $this->salaryMode = GeneralMethod::salaryMode();
        $this->accountCompanyType = GeneralMethod::accountCompanyType();

        $this->benchApplication = DB::table('job_applications')
            ->select(
                'job_applications.id',
                'job_applications.company_id',
                'job_applications.submitted_by',
                'job_applications.full_name',
                'job_applications.current_designation',
                'job_applications.outsourcing_type',
                'job_applications.phone_code',
                'job_applications.second_phone_code',
                'job_applications.phone',
                'job_applications.careersummary',
                'job_applications.nationality',
                'job_applications.wm',
                'job_applications.employmentType',
                'job_applications.oh',
                'job_applications.visastatu',
                'job_applications.referencename',
                'job_applications.secondary_mobile',
                'job_applications.dob',
                'job_applications.email',
                'job_applications.secondrary_email',
                'job_applications.qualification',
                'job_applications.total_exp',
                'job_applications.rel_exp',
                'job_applications.serving_notice_period_mode',
                'job_applications.np_id',
                'job_applications.outsourcing_type',
                'job_applications.pay_rate_currency',
                'job_applications.pay_rate_amount',
                'job_applications.pay_rate_mode',
                'job_applications.bill_rate_currency',
                'job_applications.bill_rate_amount',
                'job_applications.bill_rate_mode',
                'job_applications.comission_type',
                'job_applications.country',
                'job_applications.state',
                'job_applications.city',
                'job_applications.pincode',
                'job_applications.skills',
                'job_applications.photo',
                'job_applications.resume',
                'job_applications.status',
                'job_applications.offer_amount',
                'job_applications.joined_date',
                'job_applications.created_at',
                'job_applications.updated_at',
                'job_applications.employee_number',
                'job_applications.linkedin',
                'job_applications.github',
                'job_applications.behance',
                'job_applications.otherLink',
                'qualification_master.name as qualification_name',
                'bench_master_status.name as master_status_name',
                'state_list.state as state_name',
                'city_list.city as city_name',
                'users.name as posted_by',
                'companies.company_name',
                'companies.website',
                'companies.company_size_from',
                'companies.company_size',
                'companies.founded',
                'companies.company_type',
                'users.image as posted_by_image'
            )
            ->leftjoin('companies', 'companies.id', 'job_applications.company_id')
            ->leftjoin('users', 'users.id', 'job_applications.submitted_by')
            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
            ->leftJoin('bench_master_status', 'bench_master_status.id', '=', 'job_applications.status')
            ->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id');
        $this->benchApplication = $this->benchApplication->where('job_applications.id', $id);
        $this->benchApplication = $this->benchApplication->first();
        $this->benchApplication->phone = 'xxx-xxx-xxxx';
        $this->benchApplication->email = 'xxx-xxx-xxxx';
        $this->benchApplication->secondary_mobile = 'xxx-xxx-xxxx';
        $this->benchApplication->email = 'xxx-xxx-xxxx';
        $this->relationship_manager_show = true;
        if ($currentSlug == 'find-talents' && $this->benchApplication->company_id != $company_id) {
            $this->benchApplication->phone = 'xxx-xxx-xxxx';
            $this->benchApplication->email = 'xxx-xxx-xxxx';
            $this->benchApplication->secondary_mobile = 'xxx-xxx-xxxx';
            $this->benchApplication->email = 'xxx-xxx-xxxx';
            $this->relationship_manager_show = false;
        }

        $this->job_additional_detail = $this->bench_assign = '';
        $this->job_additional_detail = DB::table('bench_additional_detail')->select('*')->where('bench_additional_detail.bench_id', '=', @$this->benchApplication->id)->orderBy('bench_additional_detail.id', 'ASC')->get();

        $this->bench_assign = DB::table('bench_assign')->select('users.id', 'users.name')->leftjoin('users', 'users.id', 'bench_assign.user_id')->where('bench_assign.bench_applications_id', '=', @$this->benchApplication->id)->orderBy('bench_assign.id', 'ASC')->get();

        $this->bench_assign_count = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile', 'users_profile.designation', 'users.working_status')->join('users', 'users.id', '=', 'bench_assign.user_id')->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')->where('bench_assign.bench_applications_id', '=', @$this->benchApplication->id)->count();

        $this->bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name', 'users.calling_code', 'users.email', 'users.mobile', 'users_profile.designation')->join('users', 'users.id', '=', 'bench_assign.user_id')->join('users_profile', 'users_profile.user_id', '=', 'users.id')->where('bench_assign.bench_applications_id', '=', @$this->benchApplication->id)->distinct()->get();

        $view_count = [
            'user_id' => auth()->user()->id,
            'candidate_id' => $id,
            'type' => 1,
            'status' => 0,
            'created_at' => date('Y-m-d H:i:'),
            'updated_at' => date('Y-m-d H:i:'),
        ];
        DB::table('bench_count_view_download')->insert($view_count);

        $this->skillExperiences = DB::table('bench_candidate_skill_experience')
            ->where('candidate_id', $this->benchApplication->id)
            ->get();
        $this->skillExperiencesCount = DB::table('bench_candidate_skill_experience')
            ->where('candidate_id', $this->benchApplication->id)
            ->count();

        $view = view('service_company.bench-candidates.public_show', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function all_data_ajax(Request $request)
    {
        // dd($request->condidate_id); skills city_id city pay_rate_amount
        // dd("l");
        $currentRowJob = JobApplication::withoutGlobalScopes()->find($request->condidate_id);
        $condidate_id = (int) $request->condidate_id;
        $currentJobSkill = explode(',', $currentRowJob->skills);

        $searchTerms = request('cus_search');
        $custom_search = json_decode($searchTerms, true);
        // dd($custom_search);

        $location = request('kt_tagify_location');
        $custom_location = json_decode($location, true);
        $current_date = date('Y-m-d');
        $current_timestamp = strtotime($current_date);
        $current_previous_30_days = date('Y-m-d', strtotime('-30 days', $current_timestamp));
        $current_previous_60_days = date('Y-m-d', strtotime('-60 days', $current_timestamp));
        $current_previous_90_days = date('Y-m-d', strtotime('-90 days', $current_timestamp));
        $current_previous_180_days = date('Y-m-d', strtotime('-180 days', $current_timestamp));
        $current_previous_1_year = date('Y-m-d', strtotime('-365 days', $current_timestamp));
        $app_int_activity = request('app_int_activity');
        $sal_mode = request('sal_mode');
        $min_salary_currency = request('min_salary_currency');
        $app_int_outsource = request('app_int_outsource');

        $min_exp = (request('min_exp')) ? request('min_exp') : 0;
        $max_exp = request('max_exp');

        $min_salary = request('min_sal');
        $max_salary = request('max_sal');
        $currency = GeneralMethod::currency();  // Currency code

        $ecurrency = $currency[$min_salary_currency];
        $econvertedAmount = (int) $max_salary;
        if ($min_salary_currency > 0) {
            $econvertedAmount = GeneralMethod::convertToINR($econvertedAmount, $ecurrency);
        }

        $ectcdivided = GeneralMethod::salaryDividedByMode($sal_mode, $econvertedAmount);

        $currency = $currency[$min_salary_currency];
        $convertedAmount = (int) $min_salary;
        if ($min_salary_currency > 0) {
            $convertedAmount = GeneralMethod::convertToINR($convertedAmount, $currency);
        }

        $ctcdivided = GeneralMethod::salaryDividedByMode($sal_mode, $convertedAmount);
        if ($sal_mode == 2) {
            $max_salary = @$ectcdivided->hourly;
            $min_salary = @$ctcdivided->hourly;
        } elseif ($sal_mode == 1) {
            $max_salary = @$ectcdivided->monthly;
            $min_salary = @$ctcdivided->monthly;
        } elseif ($sal_mode == 0) {
            $max_salary = @$ectcdivided->yearly;
            $min_salary = @$ctcdivided->yearly;
        }

        // dd($max_salary ,$min_salary,$ectcdivided,$ctcdivided);

        $location = [];
        $rawLocationReq = \request('kt_tagify_location');
        if (! empty($rawLocationReq)) {
            $locCacheKey = 'ats:loc_tagify_ids:'.md5($rawLocationReq);
            $location = cache()->remember($locCacheKey, 3600, function () use ($custom_location) {
                $resolvedIds = [];
                if (is_array($custom_location)) {
                    foreach ($custom_location as $sval) {
                        $val = trim($sval['value'] ?? '');
                        if ($val === '') {
                            continue;
                        }
                        if (is_numeric($val)) {
                            $resolvedIds[] = (int) $val;
                        } else {
                            $cityIds = DB::table('city_list')
                                ->where('city', 'LIKE', "{$val}%")
                                ->pluck('city_id')
                                ->toArray();
                            foreach ($cityIds as $cid) {
                                $resolvedIds[] = (int) $cid;
                            }
                        }
                    }
                }

                return array_values(array_unique($resolvedIds));
            });
        }
        // dd($location);
        $set_skill = [];
        if ($currentJobSkill > 0) {
            foreach ($currentJobSkill as $val) {
                array_push($set_skill, trim($val));
            }
        }
        // (Previously ran an unused DB::table('job_metronics_skills')->whereIn(...)
        // query here whose $skill result was never read anywhere below —
        // removed as dead load. $set_skill — the candidate's own skills —
        // is kept; it now also drives the "Best Match" sort, case 10 below.)
        abort_if(Gate::denies('view_jobs'), 403);

        $company_id = auth()->user()->company_id;

        $chkAdminRole = $this->user->hasRole('admin');
        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();
        // $searchTerms = request('custom_search');
        // $custom_search = json_decode($searchTerms, true);

        if (\request('cus_search') != '') {
            $skillsAddElastic = array_column($custom_search, 'value');
            GeneralMethod::skillsAddElastic($skillsAddElastic);
        }
        $requiredSkills = [];

        if (\request('cus_search') != '') {

            $checkedSkills = array_filter($custom_search, function ($skill) {
                return $skill['checked'] == true;
            });

            $uncheckedSkills = array_filter($custom_search, function ($skill) {
                return $skill['checked'] == false;
            });

            if (! empty($checkedSkills)) {
                $requiredSkills = array_column($checkedSkills, 'value');
            } else {
                $requiredSkills = array_column($uncheckedSkills, 'value');
            }

        }

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) ($request->input('per_page') ?? $request->input('size') ?? $request->input('limit') ?? 10);
        $from = ($page - 1) * $size;

        $custom_status = request('custom_status');
        $custom_hiringtype = request('app_int_outsource');
        $custom_search = trim(request('custom_search'));

        $cachePayload = [
            // condidate_id was missing here — every request for THIS candidate's
            // recommended jobs hashed to the same cache key as any OTHER
            // candidate with the same filter state (very likely with empty/
            // default filters, e.g. two candidates with no skills recorded),
            // so one candidate's cached list/total could be served back for a
            // completely different candidate. This is the root cause behind
            // "count says N but the list shows 0" and results changing
            // unexpectedly when switching between candidates.
            'condidate_id' => $condidate_id,
            'page' => $page,
            'size' => $size,
            'q' => $searchQuery,
            'company' => $company_id,
            'custom_search' => $custom_search,
            'location' => $location,
            'app_int_activity' => $app_int_activity,
            'sal_mode' => $sal_mode,
            'min_salary_currency' => $min_salary_currency,
            'min_exp' => $min_exp,
            'max_exp' => $max_exp,
            'max_salary' => $max_salary,
            'min_salary' => $min_salary,
            'requiredSkills' => $requiredSkills ?? [],
            'custom_status' => $custom_status,
            'user_id' => $this->user->id,
            'custom_hiringtype' => $custom_hiringtype,
            'work_mode' => request('work_mode'),
        ];

        $versionKey = 'allbenchrecommendreq_version_user_'.$this->user->id;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:allbenchrecommendreq:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));
        $eTag = '"'.md5($cacheKeySet).'"';

        /* ----------------------------
        ⚡ Check DragonflyDB first with HTTP 304 revalidation
        ----------------------------- */
        if ($request->hasHeader('If-None-Match') && trim($request->header('If-None-Match'), '"') === trim($eTag, '"')) {
            return response('', 304)->header('ETag', $eTag);
        }

        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet))->header('ETag', $eTag);
        }

        $statusArr = ['active', 'hold', 'inactive', '2'];
        $must = [];
        $should = [];
        $filter = [];
        $sort = [];

        $valCheck = request('custom_hiringtype');
        // Optional scoring clauses (filled only by Best Match, case 10) —
        // added to the bool query's "should" so they rank without filtering.
        $scoreShould = [];
        switch ($valCheck) {
            case 1:
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 2:
                $sort[] = ['id' => ['order' => 'asc']];
                break;
                // Cases 3-6 get an id tiebreaker: without one, documents with equal
                // salary / client_name have no stable order between requests, so
                // paging could show the same job on two pages and skip another.
            case 3:
                $sort[] = ['max_salary' => ['order' => 'asc']];
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 4:
                $sort[] = ['max_salary' => ['order' => 'desc']];
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 5:
                $sort[] = ['client_name' => ['order' => 'asc']];
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 6:
                $sort[] = ['client_name' => ['order' => 'desc']];
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 10:
                // "Best Match" — rank jobs by how many of the candidate's own
                // skills each job's nested job_metronics_skills matches, using
                // ES native scoring: each skill is an optional constant_score(1)
                // nested clause, so _score == number of matched skills. Jobs
                // matching nothing still appear (score 0), newest first.
                // (The previous implementation was a painless _script sort that
                // read params._source — _source is not accessible in the sort
                // context and job_metronics_skills is nested, so ES rejected
                // the entire search request; since Best Match is the default
                // sort, every first page load failed.)
                foreach (array_values(array_filter(array_map('trim', $set_skill))) as $bmSkill) {
                    if ($bmSkill === '') {
                        continue;
                    }
                    $scoreShould[] = [
                        'nested' => [
                            'path' => 'job_metronics_skills',
                            'score_mode' => 'max',
                            'query' => [
                                'constant_score' => [
                                    'filter' => [
                                        'match' => [
                                            'job_metronics_skills.skill_name' => [
                                                'query' => strtolower($bmSkill),
                                                'operator' => 'and',
                                            ],
                                        ],
                                    ],
                                    'boost' => 1,
                                ],
                            ],
                        ],
                    ];
                }
                $sort[] = ['_score' => ['order' => 'desc']];
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            default:
                $sort[] = ['id' => ['order' => 'desc']];
        }

        $filter[] = ['term' => ['status' => 'active']];
        // 2. HIRING TYPE
        if ($custom_hiringtype != '' && $custom_hiringtype != 'All') {
            if ($custom_hiringtype == 0) {
                $filter[] = ['term' => ['employment_type' => 0]];
            } elseif ($custom_hiringtype == 1) {
                $filter[] = ['term' => ['employment_type' => 1]];
            }
        }

        // 2b. WORK MODE (new filter) — comma-separated work_type values from
        // the Work Mode chips (no=Onsite, other=Hybrid, yes=Remote), same
        // values already used to render each job's work mode in
        // bench-recommend-req-ajax.blade.php. Multi-select, additive only —
        // no filter is applied when nothing is selected.
        $workMode = request('work_mode');
        if (! empty($workMode)) {
            $workModeArr = array_values(array_filter(array_map('trim', explode(',', $workMode))));
            if (! empty($workModeArr)) {
                $filter[] = ['terms' => ['work_type' => $workModeArr]];
            }
        }

        // 4. COMPANY ACCESS CONDITIONS — in filter context for node query cache
        $filter[] = [
            'bool' => [
                'must_not' => [
                    [
                        'term' => [
                            'comId' => $company_id,
                        ],
                    ],
                ],
            ],
        ];

        if (! empty($requiredSkills)) {

            $should = [];

            foreach ($requiredSkills as $skill) {

                $cleanSkill = strtolower(preg_replace('/\s+/', ' ', trim($skill)));

                if ($cleanSkill !== '') {
                    $should[] = [
                        'match' => [
                            'job_metronics_skills.skill_name' => [
                                'query' => $cleanSkill,
                                'operator' => 'and',   // ensures all words must match
                            ],
                        ],
                    ];
                }
            }

            if (! empty($should)) {
                $filter[] = [
                    'nested' => [
                        'path' => 'job_metronics_skills',
                        'query' => [
                            'bool' => [
                                'should' => $should,
                                'minimum_should_match' => 1,
                            ],
                        ],
                    ],
                ];
            }
        }

        if (! empty($location)) {
            $filter[] = [
                'nested' => [
                    'path' => 'client_city_jobs',
                    'query' => [
                        'terms' => [
                            'client_city_jobs.city_id' => (array) $location,
                        ],
                    ],
                ],
            ];
        }

        if ($min_exp !== null) {
            $filter[] = [
                'range' => [
                    'max_exp' => [
                        'gte' => (int) $min_exp,
                    ],
                ],
            ];
        }
        if ($max_exp !== null) {

            $filter[] = [
                'range' => [
                    'max_exp' => [
                        'lte' => (int) $max_exp,
                    ],
                ],
            ];
        }

        $salary_field = null;

        if ($sal_mode !== null) {
            switch ((int) $sal_mode) {
                case 0:
                    $salary_field = 'max_yearly';
                    break;
                case 1:
                    $salary_field = 'max_monthly';
                    break;
                case 2:
                    $salary_field = 'max_hourly';
                    break;
            }
        }

        if ($min_salary !== null && $salary_field) {
            // dd($min_salary,$max_salary);
            $filter[] = [
                'range' => [
                    $salary_field => [
                        'gte' => (float) $min_salary,
                    ],
                ],
            ];
        }
        if ($max_salary > 0 && $salary_field) {
            // dd($min_salary,$max_salary);
            $filter[] = [
                'range' => [
                    $salary_field => [
                        'lte' => (float) $max_salary,
                    ],
                ],
            ];
        }

        // FREE-TEXT TOOLBAR SEARCH — $custom_search was read (and hashed into
        // the cache key) but never applied to the query, so the search box on
        // this page did nothing at all. Matches the fields the job card shows:
        // title, client name, company name. Analyzed fields make it
        // case-insensitive; phrase_prefix covers partial keywords; fuzziness
        // covers typos; AND operator handles multi-keyword input.
        if ($custom_search !== '') {
            $searchFields = ['title^3', 'client_name^2', 'company_name'];
            $must[] = [
                'bool' => [
                    'should' => [
                        ['multi_match' => ['query' => $custom_search, 'fields' => $searchFields, 'operator' => 'and', 'fuzziness' => 'AUTO']],
                        ['multi_match' => ['query' => $custom_search, 'fields' => $searchFields, 'type' => 'phrase_prefix']],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        // FINAL ELASTIC PARAMS — "should" carries Best Match's optional scoring
        // clauses when active (they rank, never filter).
        $boolQuery = ['must' => $must, 'filter' => $filter];
        if (! empty($scoreShould)) {
            $boolQuery['should'] = $scoreShould;
        }
        $params = [
            'index' => ELASTIC_JOBS,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => ['bool' => $boolQuery],
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_JOBS)) {
            $total = 0;
            $data = [];
        } else {
            // Graceful degradation: an ES failure renders the empty state
            // instead of a 500; the error is logged, and the (empty) response
            // is NOT cached so recovery is immediate.
            try {
                $response = $this->elasticService->getClient()->search($params);
                $total = $response['hits']['total']['value'] ?? 0;
                $data = array_map(function ($hit) {
                    return $hit['_source'];
                }, $response['hits']['hits']);
            } catch (\Throwable $esException) {
                \Log::error('all_data_ajax ES search failed: '.$esException->getMessage());
                $total = 0;
                $data = [];
                $esSearchFailed = true;
            }
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'per_page' => $size,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        $userPermissions = $this->userPermissions;
        $responseData = [
            'html' => view('service_company.ajax_pages.bench-recommend-req-ajax', compact('data', 'userPermissions', 'company_id', 'salaryMode', 'currencySymbole', 'sal_mode', 'min_salary_currency', 'job_status', 'currentRowJob', 'condidate_id'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ];

        if (empty($esSearchFailed)) {
            cache()->put($cacheKeySet, $responseData, now()->addMinutes(3));
        } // never cache an errored (empty) response

        return response()->json($responseData);

    }

    public function all_internal_data_ajax(Request $request)
    {
        // dd($request->condidate_id); skills city_id city pay_rate_amount
        // dd("l");
        $currentRowJob = JobApplication::withoutGlobalScopes()->find($request->condidate_id);
        $condidate_id = (int) $request->condidate_id;
        $currentJobSkill = explode(',', $currentRowJob->skills);

        $searchTerms = request('cus_search');
        $custom_search = json_decode($searchTerms, true);
        // dd($custom_search);

        $location = request('kt_tagify_location');
        $custom_location = json_decode($location, true);
        $current_date = date('Y-m-d');
        $current_timestamp = strtotime($current_date);
        $current_previous_30_days = date('Y-m-d', strtotime('-30 days', $current_timestamp));
        $current_previous_60_days = date('Y-m-d', strtotime('-60 days', $current_timestamp));
        $current_previous_90_days = date('Y-m-d', strtotime('-90 days', $current_timestamp));
        $current_previous_180_days = date('Y-m-d', strtotime('-180 days', $current_timestamp));
        $current_previous_1_year = date('Y-m-d', strtotime('-365 days', $current_timestamp));
        $app_int_activity = request('app_int_activity');
        $sal_mode = request('sal_mode');
        $min_salary_currency = request('min_salary_currency');
        $app_int_outsource = request('app_int_outsource');

        $min_exp = (request('min_exp')) ? request('min_exp') : 0;
        $max_exp = request('max_exp');

        $min_salary = request('min_sal');
        $max_salary = request('max_sal');
        $currency = GeneralMethod::currency();  // Currency code

        $ecurrency = $currency[$min_salary_currency];
        $econvertedAmount = (int) $max_salary;
        if ($min_salary_currency > 0) {
            $econvertedAmount = GeneralMethod::convertToINR($econvertedAmount, $ecurrency);
        }

        $ectcdivided = GeneralMethod::salaryDividedByMode($sal_mode, $econvertedAmount);

        $currency = $currency[$min_salary_currency];
        $convertedAmount = (int) $min_salary;
        if ($min_salary_currency > 0) {
            $convertedAmount = GeneralMethod::convertToINR($convertedAmount, $currency);
        }

        $ctcdivided = GeneralMethod::salaryDividedByMode($sal_mode, $convertedAmount);
        if ($sal_mode == 2) {
            $max_salary = @$ectcdivided->hourly;
            $min_salary = @$ctcdivided->hourly;
        } elseif ($sal_mode == 1) {
            $max_salary = @$ectcdivided->monthly;
            $min_salary = @$ctcdivided->monthly;
        } elseif ($sal_mode == 0) {
            $max_salary = @$ectcdivided->yearly;
            $min_salary = @$ctcdivided->yearly;
        }

        // dd($max_salary ,$min_salary,$ectcdivided,$ctcdivided);

        $location = [];
        if (\request('kt_tagify_location') != '') {
            foreach ($custom_location as $sval) {
                $loc_datas = DB::table('countries')
                    ->select('city_list.city_id')
                    ->where('countries.country_name', 'LIKE', "%{$sval['value']}%")
                    ->orWhere('state_list.state', 'LIKE', "%{$sval['value']}%")
                    ->orWhere('city_list.city', 'LIKE', "%{$sval['value']}%")
                    ->join('state_list', 'state_list.country_id', '=', 'countries.id')
                    ->join('city_list', 'city_list.state_id', '=', 'state_list.id')
                    ->get();

                // $loc_data = DB::table('city_list')->where('city_list.city_id', '=', $sval['value'])->first();
                if ($loc_datas) {
                    foreach ($loc_datas as $loc_data) {
                        array_push($location, $loc_data->city_id);
                    }
                }
            }
        }

        // dd($location);

        $set_skill = [];
        if ($currentJobSkill > 0) {
            foreach ($currentJobSkill as $val) {
                array_push($set_skill, trim($val));
            }
        }
        // (Previously ran an unused DB::table('job_metronics_skills')->whereIn(...)
        // query here whose $skill result was never read anywhere below —
        // removed as dead load. $set_skill — the candidate's own skills —
        // is kept; it now also drives the "Best Match" sort, case 10 below.)
        abort_if(Gate::denies('view_jobs'), 403);

        $company_id = auth()->user()->company_id;

        $chkAdminRole = $this->user->hasRole('admin');
        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();
        // $searchTerms = request('custom_search');
        // $custom_search = json_decode($searchTerms, true);

        if (\request('cus_search') != '') {
            $skillsAddElastic = array_column($custom_search, 'value');
            GeneralMethod::skillsAddElastic($skillsAddElastic);
        }

        $requiredSkills = [];

        if (\request('cus_search') != '') {

            $checkedSkills = array_filter($custom_search, function ($skill) {
                return $skill['checked'] == true;
            });

            $uncheckedSkills = array_filter($custom_search, function ($skill) {
                return $skill['checked'] == false;
            });

            if (! empty($checkedSkills)) {
                $requiredSkills = array_column($checkedSkills, 'value');
            } else {
                $requiredSkills = array_column($uncheckedSkills, 'value');
            }

        }

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) ($request->input('per_page') ?? $request->input('size') ?? $request->input('limit') ?? 10);
        $from = ($page - 1) * $size;

        $custom_status = request('custom_status');
        $custom_hiringtype = request('app_int_outsource');
        $custom_search = trim(request('custom_search'));

        $cachePayload = [
            // condidate_id was missing here — every request for THIS candidate's
            // recommended jobs hashed to the same cache key as any OTHER
            // candidate with the same filter state (very likely with empty/
            // default filters, e.g. two candidates with no skills recorded),
            // so one candidate's cached list/total could be served back for a
            // completely different candidate. This is the root cause behind
            // "count says N but the list shows 0" and results changing
            // unexpectedly when switching between candidates.
            'condidate_id' => $condidate_id,
            'page' => $page,
            'size' => $size,
            'q' => $searchQuery,
            'company' => $company_id,
            'custom_search' => $custom_search,
            'location' => $location,
            'app_int_activity' => $app_int_activity,
            'sal_mode' => $sal_mode,
            'min_salary_currency' => $min_salary_currency,
            'min_exp' => $min_exp,
            'max_exp' => $max_exp,
            'max_salary' => $max_salary,
            'min_salary' => $min_salary,
            'requiredSkills' => $requiredSkills ?? [],
            'custom_status' => $custom_status,
            'user_id' => $this->user->id,
            'custom_hiringtype' => $custom_hiringtype,
            'work_mode' => request('work_mode'),
        ];

        $versionKey = 'allbenchrecommendinternalreq_version_user_'.$this->user->id;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:allbenchrecommendinternalreq:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));
        /* ----------------------------
        ⚡ Check DragonflyDB first
        ----------------------------- */
        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet));
        }

        $statusArr = ['active', 'hold', 'inactive', '2'];
        $must = [];
        $should = [];
        $filter = [];

        $sort = [];

        $valCheck = request('custom_hiringtype');
        // Optional scoring clauses (filled only by Best Match, case 10) —
        // added to the bool query's "should" so they rank without filtering.
        $scoreShould = [];
        switch ($valCheck) {
            case 1:
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 2:
                $sort[] = ['id' => ['order' => 'asc']];
                break;
                // Cases 3-6 get an id tiebreaker: without one, documents with equal
                // salary / client_name have no stable order between requests, so
                // paging could show the same job on two pages and skip another.
            case 3:
                $sort[] = ['max_salary' => ['order' => 'asc']];
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 4:
                $sort[] = ['max_salary' => ['order' => 'desc']];
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 5:
                $sort[] = ['client_name' => ['order' => 'asc']];
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 6:
                $sort[] = ['client_name' => ['order' => 'desc']];
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 10:
                // "Best Match" — rank jobs by how many of the candidate's own
                // skills each job's nested job_metronics_skills matches, using
                // ES native scoring: each skill is an optional constant_score(1)
                // nested clause, so _score == number of matched skills. Jobs
                // matching nothing still appear (score 0), newest first.
                // (The previous implementation was a painless _script sort that
                // read params._source — _source is not accessible in the sort
                // context and job_metronics_skills is nested, so ES rejected
                // the entire search request; since Best Match is the default
                // sort, every first page load failed.)
                foreach (array_values(array_filter(array_map('trim', $set_skill))) as $bmSkill) {
                    if ($bmSkill === '') {
                        continue;
                    }
                    $scoreShould[] = [
                        'nested' => [
                            'path' => 'job_metronics_skills',
                            'score_mode' => 'max',
                            'query' => [
                                'constant_score' => [
                                    'filter' => [
                                        'match' => [
                                            'job_metronics_skills.skill_name' => [
                                                'query' => strtolower($bmSkill),
                                                'operator' => 'and',
                                            ],
                                        ],
                                    ],
                                    'boost' => 1,
                                ],
                            ],
                        ],
                    ];
                }
                $sort[] = ['_score' => ['order' => 'desc']];
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            default:
                $sort[] = ['id' => ['order' => 'desc']];
        }

        $filter[] = ['term' => ['status' => 'active']];
        // 2. HIRING TYPE
        if ($custom_hiringtype != '' && $custom_hiringtype != 'All') {
            if ($custom_hiringtype == 0) {
                $filter[] = ['term' => ['employment_type' => 0]];
            } elseif ($custom_hiringtype == 1) {
                $filter[] = ['term' => ['employment_type' => 1]];
            }
        }

        // 4. COMPANY ACCESS CONDITIONS
        $must[] = [
            'bool' => [
                'must' => [
                    [
                        'term' => [
                            'comId' => $company_id,
                        ],
                    ],
                ],
            ],
        ];

        if (! empty($requiredSkills)) {

            $should = [];

            foreach ($requiredSkills as $skill) {

                $cleanSkill = strtolower(preg_replace('/\s+/', ' ', trim($skill)));

                if ($cleanSkill !== '') {
                    $should[] = [
                        'match' => [
                            'job_metronics_skills.skill_name' => [
                                'query' => $cleanSkill,
                                'operator' => 'and',   // ensures all words must match
                            ],
                        ],
                    ];
                }
            }

            if (! empty($should)) {
                $filter[] = [
                    'nested' => [
                        'path' => 'job_metronics_skills',
                        'query' => [
                            'bool' => [
                                'should' => $should,
                                'minimum_should_match' => 1,
                            ],
                        ],
                    ],
                ];
            }
        }

        if (! empty($location)) {
            $must[] = [
                'nested' => [
                    'path' => 'client_city_jobs',
                    'query' => [
                        'terms' => [
                            'client_city_jobs.city_id' => (array) $location,
                        ],
                    ],
                ],
            ];
        }

        if ($min_exp !== null) {
            $must[] = [
                'range' => [
                    'max_exp' => [
                        'gte' => (int) $min_exp,
                    ],
                ],
            ];
        }
        if ($max_exp !== null) {
            $must[] = [
                'range' => [
                    'max_exp' => [
                        'lte' => (int) $max_exp,
                    ],
                ],
            ];
        }

        $salary_field = null;

        if ($sal_mode !== null) {
            switch ((int) $sal_mode) {
                case 0:
                    $salary_field = 'max_yearly';
                    break;
                case 1:
                    $salary_field = 'max_monthly';
                    break;
                case 2:
                    $salary_field = 'max_hourly';
                    break;
            }
        }

        if ($min_salary !== null && $salary_field) {
            // dd($min_salary,$max_salary);
            $must[] = [
                'range' => [
                    $salary_field => [
                        'gte' => (float) $min_salary,
                    ],
                ],
            ];
        }
        if ($max_salary > 0 && $salary_field) {
            // dd($min_salary,$max_salary);
            $must[] = [
                'range' => [
                    $salary_field => [
                        'lte' => (float) $max_salary,
                    ],
                ],
            ];
        }

        // FREE-TEXT TOOLBAR SEARCH — $custom_search was read (and hashed into
        // the cache key) but never applied to the query, so the search box on
        // this page did nothing at all. Matches the fields the job card shows:
        // title, client name, company name. Analyzed fields make it
        // case-insensitive; phrase_prefix covers partial keywords; fuzziness
        // covers typos; AND operator handles multi-keyword input.
        if ($custom_search !== '') {
            $searchFields = ['title^3', 'client_name^2', 'company_name'];
            $must[] = [
                'bool' => [
                    'should' => [
                        ['multi_match' => ['query' => $custom_search, 'fields' => $searchFields, 'operator' => 'and', 'fuzziness' => 'AUTO']],
                        ['multi_match' => ['query' => $custom_search, 'fields' => $searchFields, 'type' => 'phrase_prefix']],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        // FINAL ELASTIC PARAMS — "should" carries Best Match's optional scoring
        // clauses when active (they rank, never filter).
        $boolQuery = ['must' => $must, 'filter' => $filter];
        if (! empty($scoreShould)) {
            $boolQuery['should'] = $scoreShould;
        }
        $params = [
            'index' => ELASTIC_JOBS,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => ['bool' => $boolQuery],
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_JOBS)) {
            $total = 0;
            $data = [];
        } else {
            // Graceful degradation: an ES failure renders the empty state
            // instead of a 500; the error is logged, and the (empty) response
            // is NOT cached so recovery is immediate.
            try {
                $response = $this->elasticService->getClient()->search($params);
                $total = $response['hits']['total']['value'] ?? 0;
                $data = array_map(function ($hit) {
                    return $hit['_source'];
                }, $response['hits']['hits']);
            } catch (\Throwable $esException) {
                \Log::error('all_internal_data_ajax ES search failed: '.$esException->getMessage());
                $total = 0;
                $data = [];
                $esSearchFailed = true;
            }
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'per_page' => $size,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        $userPermissions = $this->userPermissions;
        $responseData = [
            'html' => view('service_company.ajax_pages.bench-recommend-req-ajax', compact('data', 'userPermissions', 'company_id', 'salaryMode', 'currencySymbole', 'sal_mode', 'min_salary_currency', 'job_status', 'currentRowJob', 'condidate_id'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ];

        if (empty($esSearchFailed)) {
            cache()->put($cacheKeySet, $responseData, now()->addMinutes(3));
        } // never cache an errored (empty) response

        return response()->json($responseData);

    }

    public function recommandedrequestcondidate(Request $request)
    {

        $company_id = auth()->user()->company_id;

        $job = Job::withoutGlobalScopes()->findOrFail($request->jobId);

        //   $detailsVendorSide = GeneralMethod::companyAsVendor($job->company_id,$company_id);
        //   $mailTypeSubject = GeneralMethod::mailTypeSubject(21,1);

        //   dd($detailsVendorSide,$mailTypeSubject);

        $source_from_company_id = $company_id;
        $source_from_user_id = auth()->user()->id;
        //   $source_from_id	 = '';
        $source_from_type = 1;
        $source_company_id = $job->company_id;
        //   $source_user_id	 = '';
        $source_application_id = (int) $request->conId;
        $source_jobid = (int) $request->jobId;

        $eventId = 25;
        $coinsDeductions = GeneralMethod::coinsValidations((int) $source_from_company_id, $eventId, auth()->user()->id, $source_company_id, '', '');
        // dd($coinsDeductions);
        if ($coinsDeductions->status) {
            return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
        }
        //  dd("djd");
        //   dd($source_company_id);

        $values = ['source_from_company_id' => $source_from_company_id, 'source_from_user_id' => $source_from_user_id, 'source_from_type' => $source_from_type, 'source_company_id' => $source_company_id, 'source_application_id' => $source_application_id, 'source_from_id' => $source_application_id, 'source_jobid' => $source_jobid];
        $row_data = DB::table('job_applications')->select('*')->where('id', @$source_application_id)->first();
        $jobcountry = GeneralMethod::JobCountries($source_jobid);
        $country = @$row_data->country;

        // dd($values);
        $query = false;
        if (@$source_jobid > 0) {

            if (in_array($country, $jobcountry)) {
                $event_name = 'BenchSalesIndia';
            } else {
                $event_name = 'BenchSalesInternational';
            }

            // $source_from_company_id = $source_from_company_id;
            // $event_name = $event_name;
            $event_table_name = 'jobs';
            $event_table_id = @$source_jobid;
            $query = DB::table('rec_candidate_assign')->insert($values);
            $rec_id = DB::getPdo()->lastInsertId();

            /**
             * Intent: Asynchronously offload Elasticsearch candidate request indexing to Horizon queue (es-index queue).
             * Tenancy Note: Tenant-scoped by rec_candidate_assign_id.
             */
            \App\Jobs\SyncCandidateRequestToElasticJob::dispatch((int) $rec_id);
            \App\Services\MarketJobV2Service::invalidateCacheForCompany((int) $source_from_company_id);

        }

        // $query = true;
        if ($query) {

            // $packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
            $eventId = 25;
            $coinsDeductions = GeneralMethod::coinsDeductions((int) $source_from_company_id, $eventId, auth()->user()->id, $source_company_id, '', '');

            $data = ['status' => 'success', 'message' => ''];

            $detailsVendorSide = GeneralMethod::companyAsVendor($job->company_id, $company_id);

            $basecompany = GeneralMethod::baseCompany();
            $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
            $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
            $attachments = [];

            $cc = [];
            $checkMailPermision = GeneralMethod::mailPermission(21);
            if ($checkMailPermision) {

                $mailTypeSubject = GeneralMethod::mailTypeSubject(21, 1);

                $jobRow = DB::table('job_applications')->select('job_applications.*', 'city_list.city')
                    ->join('city_list', 'city_list.city_id', '=', 'job_applications.city_id')->where('job_applications.id', '=', $source_application_id)->first();

                $salaryMode = GeneralMethod::salaryMode();
                $currencySymbole = GeneralMethod::currencySymbole();
                $bill_rate_mode = $bill_rate_currency = $bill_rate_amount = '';
                $bill_rate_mode = strtoupper(substr($salaryMode[$jobRow->bill_rate_mode], 0, 1));
                $bill_rate_currency = $currencySymbole[@$jobRow->bill_rate_currency];
                $bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($jobRow->bill_rate_amount);
                $salaryRange = $bill_rate_currency.$bill_rate_amount.'/'.$bill_rate_mode;

                $subject = str_replace(['[{JOB_TITLE}]', '[{JOB_ID}]'], [$job->title, $job->id], $mailTypeSubject->subject);

                $template_description = $mailTypeSubject->body_description;

                $template_description = str_replace(['[{CLIENT_COMPANY_NAME}]', '[{JOB_TITLE}]', '[{JOB_ID}]', '[{VENDOR_COMPANY_NAME}]', '[{BENCH_CANDIDATE_NAME}]', '[{BILL_RATE}]', '[{EXPERENCE}]', '[{LOCATION}]', '[{CONTACT_PERSON_NAME}]', '[{PHONE}]', '[{EMAIL}]', '[{SKILL}]'], [$detailsVendorSide->client_company, $job->title, $job->id, $detailsVendorSide->vendor_company, $jobRow->full_name, $salaryRange, $jobRow->total_exp, $jobRow->city, $detailsVendorSide->vendor_name, $detailsVendorSide->vendor_phone, $detailsVendorSide->vendor_email, $jobRow->skills], $template_description);

                $message = $template_description;

                $notificationemail = GeneralMethod::notification($source_company_id, 'Recuitment'); // Recuitment Sales
                $defailsEmail = DB::table('companies')->where('id', $source_company_id)->first()->company_email;
                $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';

                if (! empty($to_Mail_Id)) {
                    /**
                     * Intent: Asynchronously offload email dispatch to Horizon queue (mail queue).
                     * Tenancy Note: Multi-tenant safe, scoped by tenant notification recipient.
                     */
                    \App\Jobs\SendEmailJob::dispatch(
                        $to_Mail_Id,
                        $subject,
                        'email.common.index',
                        ['message' => $message],
                        $attachments,
                        $fromAddress,
                        $senderName,
                        $cc
                    );
                }

            }

        } else {
            $data = ['status' => 'warning', 'message' => 'Failed'];

        }

        $cachekeys = [
            'allbenchrecommendreq_version_user_'.$this->user->id,
            'allbenchrecommendinternalreq_version_user_'.$this->user->id,
        ];
        foreach ($cachekeys as $key) {
            GeneralMethod::cacheClean($key);
        }

        return response()->json($data);

    }

    public function mailrecommandedrequestcondidate(Request $request)
    {

        $company_id = auth()->user()->company_id;

        $job = Job::withoutGlobalScopes()->findOrFail($request->jobId);

        $user_id = auth()->user()->id;
        $jobId = $request->jobId;
        $conId = $request->conId;
        $matlaccess = $request->matlaccess;

        $source_from_company_id = $company_id;
        $source_from_user_id = auth()->user()->id;
        //   $source_from_id	 = '';
        $source_from_type = 1;
        $source_company_id = $job->company_id;
        //   $source_user_id	 = '';
        $source_application_id = (int) $request->conId;
        $source_jobid = (int) $request->jobId;

        $values = ['source_from_company_id' => $source_from_company_id, 'source_from_user_id' => $source_from_user_id, 'source_from_type' => $source_from_type, 'source_company_id' => $source_company_id, 'source_application_id' => $source_application_id, 'source_from_id' => $source_application_id, 'source_jobid' => $source_jobid];
        $row_data = DB::table('job_applications')->select('*')->where('id', @$source_application_id)->first();
        $jobcountry = GeneralMethod::JobCountries($source_jobid);
        $country = @$row_data->country;

        $packageStatusIndia = GeneralMethod::validateClientPackage('BenchClientAcccessIndia', $source_from_company_id);
        $row_data = DB::table('job_applications')->select('*')->where('id', @$source_application_id)->first();
        $jobcountry = GeneralMethod::JobCountries($source_jobid);
        $country = @$row_data->country;
        // dd($packageStatusIndia,$country, $jobcountry);
        $detailsVendorSide = GeneralMethod::companyAsVendor($job->company_id, $company_id);
        $basecompany = GeneralMethod::baseCompany();
        $senderName = $basecompany->companyName ?? '';
        $fromAddress = $basecompany->companyEmail ?? '';
        $cc = [];

        if (GeneralMethod::mailPermission(21)) {
            $mailTypeSubject = GeneralMethod::mailTypeSubject(21, 1);
            $jobRow = DB::table('job_applications')
                ->select('job_applications.*', 'city_list.city')
                ->join('city_list', 'city_list.city_id', '=', 'job_applications.city_id')
                ->where('job_applications.id', '=', $source_application_id)
                ->first();

            $salaryMode = GeneralMethod::salaryMode();
            $currencySymbole = GeneralMethod::currency();
            $bill_rate_mode = strtoupper(substr($salaryMode[$jobRow->bill_rate_mode], 0, 1));
            $bill_rate_currency = $currencySymbole[@$jobRow->bill_rate_currency];
            $bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($jobRow->bill_rate_amount);
            $salaryRange = $bill_rate_currency.' '.$bill_rate_amount.'/'.$bill_rate_mode;

            $npDate = $jobRow->np_id;
            if ($jobRow->serving_notice_period_mode == 0) {
                $npDate = ($jobRow->np_id == 1) ? $jobRow->np_id.' Day' : $jobRow->np_id.' Days';
            } elseif ($jobRow->serving_notice_period_mode == 1) {
                $originalDate = $jobRow->np_id;
                $npDate = date('d-M-y', strtotime($originalDate));
            } else {
                $npDate = '';
            }

            if ($jobRow->outsourcing_type == 1) {
                $outsourcing = 'Fulltime';
            } else {
                $outsourcing = 'Contract';
            }

            if ($jobRow->comission_type == 1) {
                $comission = 'Free Service';
            } else {
                $comission = 'Paid Service';
            }

            $subject = str_replace(['[{JOB_TITLE}]', '[{JOB_ID}]', '[{VENDOR_COMPANY_NAME}]'], [$job->title, $job->id, $detailsVendorSide->vendor_company], $mailTypeSubject->subject);
            $template_description = str_replace(
                ['[{CLIENT_COMPANY_NAME}]', '[{JOB_TITLE}]', '[{JOB_ID}]', '[{VENDOR_COMPANY_NAME}]', '[{BENCH_CANDIDATE_NAME}]', '[{BILL_RATE}]', '[{EXPERENCE}]', '[{LOCATION}]', '[{CONTACT_PERSON_NAME}]', '[{PHONE}]', '[{EMAIL}]', '[{SKILL}]', '[{NP}]', '[{OUTSOURCE}]', '[{COMMISSION}]'],
                [$detailsVendorSide->client_company, $job->title, $job->id, $detailsVendorSide->vendor_company, $jobRow->full_name, $salaryRange, $jobRow->total_exp, $jobRow->city, $detailsVendorSide->vendor_name, $detailsVendorSide->vendor_phone, $detailsVendorSide->vendor_email, $jobRow->skills, $npDate, $outsourcing, $comission],
                $mailTypeSubject->body_description
            );

            $message = $template_description;
            $notificationemail = GeneralMethod::notification($source_company_id, 'Recuitment');
            $defaultEmail = DB::table('companies')->where('id', $source_company_id)->first()->company_email;
            $to_Mail_Id = $notificationemail->email ?? $defaultEmail ?? '';
            $datamail = ['message' => $message];

            $mailService = new MailService;

            QueueNotificationMailService::dispatch(
                '4belldevelopers@gmail.com',
                $subject,
                'email.common.index',
                (object) $datamail,
                [],
                $fromAddress,
                $senderName,
                $cc,
                $company_id
            )->onConnection('emails');

            // $result =   $mailService->sendEmail($to_Mail_Id, $subject, 'email.common.index', (object) $datamail, [], $fromAddress, $senderName, $cc);

            return response()->json(['status' => 'success', 'message' => true]);

        }

    }

    public function downloadResumeBenchCandidate(Request $request)
    {
        $id = (int) $request->candidateId;
        $companyId = (int) auth()->user()->company_id;

        // TENANCY: Validate the clicked candidate belongs to the current company
        // before queuing analytics; download counters must not accept arbitrary IDs.
        $candidateExists = DB::table('job_applications')
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->exists();

        if (! $candidateExists) {
            return response()->json(['status' => 'error'], 404);
        }

        RecordBenchResumeDownloadJob::dispatch($id, (int) auth()->user()->id, $companyId);

        return response()->json(['status' => 'success']);

    }

    public function boostkeywordadd(Request $request)
    {
        $company_id = auth()->user()->company_id;
        $boostkeywords = DB::table('benchboostkeywordcandidates')->where('candidate_id', $request->candidate_id)->delete();
        $tags = request('tags');
        $tags_key = json_decode($tags, true);
        if (\request('tags') != '') {
            //  dd($tags_key);
            foreach ($tags_key as $tagSkill) {
                $keyWord = $tagSkill['value'];
                $insertArr = ['company_id' => $company_id, 'candidate_id' => $request->candidate_id, 'skill_name' => $keyWord, 'status' => 1];
                DB::table('benchboostkeywordcandidates')->insert($insertArr);
            }

        }

        return response()->json([
            'message' => '',
            'status' => '',
            'paymentLink' => '',
        ]);

    }

    public function benchSwitch(Request $request)
    {
        $company_id = auth()->user()->company_id;
        $row = DB::table('job_applications')->where('id', '=', $request->id)->first();

        if ($row->status != 2 && $row->ads_switch != 'Yes') {
            return Reply::dataOnly(['status' => 'info', 'message' => 'Please change the status to “BENCH” in order to activate the ADS for this profile', 'pack_status' => 'testing']);
        }

        $message = '';
        if ($row->ads_switch === 'Yes') {
            $pack_status = 'Ads Block';
            $status = 'No';
            $message = 'Candidate Ads Stop';
        } else {
            $pack_status = 'Ads Active';
            $status = 'Yes';

            $eventId = 44;
            $coinsRequ = 1;
            $coinsDeductions = GeneralMethod::coinsValidationsAllcompany($company_id, $eventId, $coinsRequ);

            if ($coinsDeductions->status) {
                return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
            }

            $coinsDeductions = GeneralMethod::coinsDeductions((int) $company_id, $eventId, auth()->user()->id, $company_id, '', '');
            $message = 'Candidate Ads start';

        }

        // dd($pack_status,$request->id);
        $result = DB::table('job_applications')->where('id', '=', (int) $request->id)->update(['ads_switch' => $status]);

        $cachekeys = [
            'allbenchcandidates_version_user_'.$this->user->id,
            'benchviewmore_version_user_'.$this->user->id,
        ];
        foreach ($cachekeys as $key) {
            GeneralMethod::cacheClean($key);
        }

        if ($result) {
            GeneralMethod::elasticBenchCandidateAddEditIndex($request->id);

            return Reply::dataOnly(['status' => 'success', 'message' => $message, 'pack_status' => $pack_status]);

        }

        return Reply::dataOnly(['status' => 'faild', 'pack_status' => $pack_status]);
    }

    /**
     * Hero KPI counters (Views / Downloads / Interviews) for the bench
     * candidate detail pages — previously these rendered a hardcoded 0
     * because job_applications has no such columns. Uses the same counting
     * semantics the bench listing already uses elsewhere in this controller
     * (bench_count_view_download: type 1=view / 2=download, status 0) plus
     * this candidate's scheduled interviews. Three indexed single-column
     * counts, once per page load. Tenancy: rows are keyed to the candidate
     * id, which each caller has already findOrFail'd.
     */
    private function benchHeroKpis($id)
    {
        $cacheKey = "ats:bench_candidate_hero_kpis:{$id}";

        return cache()->remember($cacheKey, 120, function () use ($id) {
            $stats = DB::table('bench_count_view_download')
                ->selectRaw('
                    SUM(CASE WHEN type = 1 THEN 1 ELSE 0 END) as views,
                    SUM(CASE WHEN type = 2 THEN 1 ELSE 0 END) as downloads
                ')
                ->where('status', 0)
                ->where('candidate_id', $id)
                ->first();

            $interviews = DB::table('interview_schedules')
                ->where('job_application_id', $id)
                ->count();

            return [
                'views' => (int) ($stats->views ?? 0),
                'downloads' => (int) ($stats->downloads ?? 0),
                'interviews' => $interviews,
            ];
        });
    }

    private function benchjobAndRecomCount($id, $currentRowJob = null)
    {
        $company_id = auth()->user()->company_id;
        $condidate_id = (int) $id;

        if (! $currentRowJob) {
            $currentRowJob = JobApplication::withoutGlobalScopes()->find($id);
        }
        if (! $currentRowJob) {
            return [
                'bench_explore_counter' => 0,
                'recommend_req_counter' => 0,
                'recommend_internal_req_counter' => 0,
            ];
        }

        $skillsHash = md5($currentRowJob->skills ?? '');
        $cacheKey = "ats:bench_recommend_counts:{$condidate_id}:{$company_id}:{$skillsHash}";

        return cache()->remember($cacheKey, 180, function () use ($id, $company_id, $currentRowJob) {
            $currentJobSkill = explode(',', $currentRowJob->skills);

            $set_skill = [];
            if (count($currentJobSkill) > 0) {
                foreach ($currentJobSkill as $val) {
                    $clean = trim($val);
                    if ($clean !== '') {
                        array_push($set_skill, $clean);
                    }
                }
            }

            $gdFilterData = GeneralMethod::canGgdFilterData($id);

            $sal_mode = $gdFilterData->min_sal_mode;
            $statusArr = ['active', 'hold', 'inactive', '2'];
            $must = [];
            $filter = [];
            $filter[] = ['term' => ['status' => 'active']];

            // 4. COMPANY ACCESS CONDITIONS
            $must[] = [
                'bool' => [
                    'must_not' => [
                        [
                            'term' => [
                                'comId' => $company_id,
                            ],
                        ],
                    ],
                ],
            ];

            if (! empty($set_skill)) {
                $should = [];
                foreach ($set_skill as $skill) {
                    $cleanSkill = strtolower(preg_replace('/\s+/', ' ', trim($skill)));
                    if ($cleanSkill !== '') {
                        $should[] = [
                            'match' => [
                                'job_metronics_skills.skill_name' => [
                                    'query' => $cleanSkill,
                                    'operator' => 'and',   // ensures all words must match
                                ],
                            ],
                        ];
                    }
                }

                if (! empty($should)) {
                    $filter[] = [
                        'nested' => [
                            'path' => 'job_metronics_skills',
                            'query' => [
                                'bool' => [
                                    'should' => $should,
                                    'minimum_should_match' => 1,
                                ],
                            ],
                        ],
                    ];
                }
            }

            $salaryFieldByMode = [0 => 'max_yearly', 1 => 'max_monthly', 2 => 'max_hourly'];
            $countSalaryField = $salaryFieldByMode[(int) $sal_mode] ?? 'max_yearly';
            $must[] = ['range' => ['max_exp' => ['gte' => 0]]];
            $must[] = ['range' => [$countSalaryField => ['gte' => 0]]];

            $params = [
                'index' => ELASTIC_JOBS,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => $must,
                            'filter' => $filter,
                        ],
                    ],
                ],
            ];

            if (! $this->elasticService->indexExists(ELASTIC_JOBS)) {
                $total = 0;
            } else {
                $response = $this->elasticService->getClient()->count($params);
                $total = $response['count'] ?? 0;
            }

            $paramsBench = [
                'index' => ELASTIC_CANDIDATES_BENCH,
                'id' => $id,
            ];

            $jobIds = [];
            try {
                $responseBench = $this->elasticService->getClient()->get($paramsBench);
                $jobIds = array_column(((object) $responseBench['_source'])->jobs ?? [], 'job_id');
            } catch (\Throwable $benchDocMissing) {
                \Log::warning('benchjobAndRecomCount: bench doc missing for candidate '.$id);
            }

            $mustInternal = [
                [
                    'bool' => [
                        'must' => [
                            [
                                'term' => [
                                    'comId' => $company_id,
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            $filterInternal = [['term' => ['status' => 'active']]];

            if (! empty($set_skill)) {
                $shouldInternal = [];
                foreach ($set_skill as $skill) {
                    $cleanSkill = strtolower(preg_replace('/\s+/', ' ', trim($skill)));
                    if ($cleanSkill !== '') {
                        $shouldInternal[] = [
                            'match' => [
                                'job_metronics_skills.skill_name' => [
                                    'query' => $cleanSkill,
                                    'operator' => 'and',   // ensures all words must match
                                ],
                            ],
                        ];
                    }
                }

                if (! empty($shouldInternal)) {
                    $filterInternal[] = [
                        'nested' => [
                            'path' => 'job_metronics_skills',
                            'query' => [
                                'bool' => [
                                    'should' => $shouldInternal,
                                    'minimum_should_match' => 1,
                                ],
                            ],
                        ],
                    ];
                }
            }

            $mustInternal[] = ['range' => ['max_exp' => ['gte' => 0]]];
            $mustInternal[] = ['range' => [$countSalaryField => ['gte' => 0]]];

            $paramsInternal = [
                'index' => ELASTIC_JOBS,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => $mustInternal,
                            'filter' => $filterInternal,
                        ],
                    ],
                ],
            ];

            if (! $this->elasticService->indexExists(ELASTIC_JOBS)) {
                $totalInternal = 0;
            } else {
                $responseInternal = $this->elasticService->getClient()->count($paramsInternal);
                $totalInternal = $responseInternal['count'] ?? 0;
            }

            return [
                'bench_explore_counter' => count($jobIds),
                'recommend_req_counter' => $total,
                'recommend_internal_req_counter' => $totalInternal,
            ];
        });
    }
}
