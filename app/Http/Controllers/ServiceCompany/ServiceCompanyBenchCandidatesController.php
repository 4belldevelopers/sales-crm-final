<?php

/***#######################################################
Controller    	: 	AdminAppTrackerController
Purpose			: 
Author			:	Abu
Company			:	4Bell
Date			:	28-JUNE-2022	
########################################################***/

namespace App\Http\Controllers\ServiceCompany;

use App\Models\ApplicationStatus;
use App\Helper\Files;
use App\Helper\Reply;
use App\Helper\GeneralMethod;
use App\Models\Job;
use App\Models\BenchApplication;
use App\Models\JobApplication;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use App\Jobs\UploadResumeToTestRTwo;
use Illuminate\Http\Request;
use App\Jobs\SendEmailJob;
use App\Jobs\QueueMailService;
use App\Jobs\QueueMassMailService;
use App\Jobs\QueueNotificationMailService;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use App\Jobs\UploadResumeToS3;
use Validator;
use App\Jobs\UploadResumeToTestS3;
use Yajra\DataTables\Facades\DataTables;
use App\Models\JobSkill;
use App\Jobs\ElasticBenchCandidateIndex;
use App\Models\NoticePeriod;
use App\Services\MailService;
use App\Jobs\ProcessCandidateRequest;
use App\Services\ElasticService;


class ServiceCompanyBenchCandidatesController extends ServiceCompanyBaseController
{
	protected $mailService;
	protected $elasticService;
	public function __construct(MailService $mailService,ElasticService $elasticService)
	{
		parent::__construct();
		$this->pageTitle = 'Bench list';
		$this->pageIcon = 'icon-badge';
		$this->mailService = $mailService;
		$this->elasticService = $elasticService;
		// dd($this->global);
	}
	public function index()
	{
		// dd("dlkfk");
		$this->pageTitle = __('Bench Listing');
		$this->allBenchApplication = JobApplication::count();


		$params = [
			'index' => ELASTIC_CANDIDATES_BENCH,
			'size'  => 0,   // no documents, only counts
			'body'  => [
				'query' => [
					'bool' => [
						'filter' => [
							['term' => ['company_id' => auth()->user()->company_id]],
							['term' => ['condidate_type.keyword' => 'bench_condidate']]
						]
					]
				],
				'aggs' => [
					'all_records' => [
						'value_count' => [
							'field' => 'id'
						]
					],
					'bench' => [
						'filter' => [
							'term' => ['status' => 2]
						]
					],
					'pool' => [
						'filter' => [
							'term' => ['status' => 1]
						]
					],
					'placed' => [
						'filter' => [
							'term' => ['status' => 3]
						]
					],
					'paused' => [
						'filter' => [
							'term' => ['status' => 4]
						]
					],
					'exited' => [
						'filter' => [
							'term' => ['status' => 5]
						]
					]
				]
			]
		];

		$response = $this->elasticService->getClient()->search($params);
		$this->counts = (object) [
			'all_records' => $response['hits']['total']['value'] ?? 0,
			'bench'       => $response['aggregations']['bench']['doc_count'] ?? 0,
			'pool'        => $response['aggregations']['pool']['doc_count'] ?? 0,
			'placed'      => $response['aggregations']['placed']['doc_count'] ?? 0,
			'paused'      => $response['aggregations']['paused']['doc_count'] ?? 0,
			'exited'      => $response['aggregations']['exited']['doc_count'] ?? 0,
		];


			$this->companies = DB::table('companies')->select('id','company_name')->where('id', auth()->user()->company_id)->first();
            // dd($this->counts);

		// Inhouse Talents page keeps only its hero KPI chips (counts + MoM deltas + Bench Cost);
		// the Bench-Aging / Loss / Profit charts now live on the Staffing Dashboard.
		$companyId = auth()->user()->company_id;
		$analytics = $this->benchStaffingAnalytics($companyId);
		$this->currencyCode = $analytics->currencyCode;
		$this->deltas       = $analytics->deltas;
		$this->benchCost    = $analytics->benchCost;

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

		$this->currencyCode    = $a->currencyCode;
		$this->benchCost       = $a->benchCost;
		$this->grossProfit     = $a->grossProfit;
		$this->benchAging      = $a->benchAging;
		$this->benchCount      = $a->benchCount;
		$this->avgBenchAge     = $a->avgBenchAge;
		$this->longestBenchAge = $a->longestBenchAge;
		$this->profitMargin    = $a->profitMargin;
		$this->benchROI        = $a->benchROI;
		$this->chartLabels     = $a->chartLabels;
		$this->lossSeries      = $a->lossSeries;
		$this->profitSeries    = $a->profitSeries;
		$this->deltas          = $a->deltas;

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

		$benchCost       = (float) $benchRows->sum(fn ($r) => (float) $r->ctc_yearly);
		$placedClientPay = (float) $placementRows->sum(fn ($r) => $annual($r->bill_rate_amount, $r->bill_rate_mode));
		$placedSalary    = (float) $placementRows->sum(fn ($r) => $annual($r->pay_rate_amount, $r->pay_rate_mode));
		$grossProfit     = $placedClientPay - $benchCost - $placedSalary;

		// Bench Aging buckets (days on bench, derived from created_at).
		$aging = ['b1' => 0, 'b2' => 0, 'b3' => 0, 'b4' => 0];
		$today = Carbon::today();
		foreach ($benchRows as $r) {
			$days = $r->created_at ? Carbon::parse($r->created_at)->diffInDays($today) : 0;
			if ($days <= 15)      { $aging['b1']++; }
			elseif ($days <= 30)  { $aging['b2']++; }
			elseif ($days <= 45)  { $aging['b3']++; }
			else                  { $aging['b4']++; }
		}

		// Daily Loss/Profit trend across the current month (cumulative to each day).
		$monthStart = Carbon::now()->startOfMonth();
		$dayCount   = (int) Carbon::now()->format('j');
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

			$lossSeries[]   = round($lossToDate, 2);
			$profitSeries[] = round($clientPayToDate - $lossToDate - $placedSalToDate, 2);
		}

		$ages = $benchRows->map(fn ($r) => $r->created_at ? Carbon::parse($r->created_at)->diffInDays($today) : 0);
		$avgBenchAge     = $ages->count() ? (int) round($ages->avg()) : 0;
		$longestBenchAge = $ages->count() ? (int) $ages->max() : 0;
		$profitMargin    = $placedClientPay > 0 ? round(($grossProfit / $placedClientPay) * 100, 1) : 0;
		$benchROI        = $benchCost > 0 ? round($placedClientPay / $benchCost, 2) : 0;

		// Month-over-month deltas ("vs last month" chips), from records created in each window.
		$thisStart = Carbon::now()->startOfMonth();
		$lastStart = Carbon::now()->subMonthNoOverflow()->startOfMonth();
		$lastEnd   = Carbon::now()->subMonthNoOverflow()->endOfMonth();

		$addsThis = DB::table('job_applications')->where('company_id', $companyId)->where('condidate_type', 'bench_condidate')
			->where('created_at', '>=', $thisStart)->selectRaw('status, COUNT(*) c')->groupBy('status')->pluck('c', 'status');
		$addsLast = DB::table('job_applications')->where('company_id', $companyId)->where('condidate_type', 'bench_condidate')
			->whereBetween('created_at', [$lastStart, $lastEnd])->selectRaw('status, COUNT(*) c')->groupBy('status')->pluck('c', 'status');

		$delta = function ($cur, $prev) {
			if ($prev <= 0) { return $cur > 0 ? 100.0 : 0.0; }
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
			'all'    => $delta($addsThis->sum(), $addsLast->sum()),
			'pool'   => $delta($addsThis->get(1, 0), $addsLast->get(1, 0)),
			'bench'  => $delta($addsThis->get(2, 0), $addsLast->get(2, 0)),
			'hold'   => $delta($addsThis->get(4, 0), $addsLast->get(4, 0)),
			'placed' => $delta($addsThis->get(3, 0), $addsLast->get(3, 0)),
			'cost'   => $delta($costThis, $costLast),
			'profit' => $delta($profitThis, $profitLast),
		];

		$currencyMode = GeneralMethod::getCompanyCurrencyBenchshowMode($companyId);

		return (object) [
			'currencyCode'    => $currencyMode->currency_code ?? 'INR',
			'benchCost'       => $benchCost,
			'grossProfit'     => $grossProfit,
			'benchAging'      => (object) $aging,
			'benchCount'      => $benchRows->count(),
			'avgBenchAge'     => $avgBenchAge,
			'longestBenchAge' => $longestBenchAge,
			'profitMargin'    => $profitMargin,
			'benchROI'        => $benchROI,
			'chartLabels'     => $labels,
			'lossSeries'      => $lossSeries,
			'profitSeries'    => $profitSeries,
			'deltas'          => $deltas,
		];
	}
	public function countDataGetBenchList(){


	$params = [
			'index' => ELASTIC_CANDIDATES_BENCH,
			'size'  => 0,   // no documents, only counts
			'body'  => [
				'query' => [
					'bool' => [
						'filter' => [
							['term' => ['company_id' => auth()->user()->company_id]],
							['term' => ['condidate_type.keyword' => 'bench_condidate']]
						]
					]
				],
				'aggs' => [
					'all_records' => [
						'value_count' => [
							'field' => 'id'
						]
					],
					'bench' => [
						'filter' => [
							'term' => ['status' => 2]
						]
					],
					'pool' => [
						'filter' => [
							'term' => ['status' => 1]
						]
					],
					'placed' => [
						'filter' => [
							'term' => ['status' => 3]
						]
					],
					'paused' => [
						'filter' => [
							'term' => ['status' => 4]
						]
					],
					'exited' => [
						'filter' => [
							'term' => ['status' => 5]
						]
					]
				]
			]
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
				'paused' =>$response['aggregations']['paused']['doc_count'] ?? 0,
				'placed' =>$response['aggregations']['placed']['doc_count'] ?? 0,
				'exited' =>$response['aggregations']['exited']['doc_count'] ?? 0,
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
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;
		$requiredSkills = [];
		$searchTerms = request('cus_search');
		$custom_search = $searchTerms; //json_decode($searchTerms, true); 


		$cachePayload = [
			'page' => $page,
			'size' => $size,
			'q' => $searchQuery,
			'company' => $company_id,
			'custom_search' => $custom_search,
			'selectedValue' => $selectedValue,
			'user_id' => $this->user->id,
			'sort' => $valCheck
		];

		$versionKey = 'allbenchcandidates_version_user_'.$this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:allbenchcandidates:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
		/* ----------------------------
		⚡ Check DragonflyDB first
		----------------------------- */
		if (cache()->has($cacheKeySet)) {
			return response()->json(cache()->get($cacheKeySet));
		}

		$sort = [];

		// 1. Sort: ads_switch 'Yes' on top
		$sort[] = [
			'_script' => [
				'type' => 'number',
				'script' => [
					'lang' => 'painless',
					'source' => "
						if (!doc.containsKey('ads_switch') || doc['ads_switch'].size() == 0) {
							return 1;
						}
						return doc['ads_switch'].value.toLowerCase() == 'yes' ? 0 : 1;
					"
				],
				'order' => 'asc'
			]
		];
		
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
						'skills' => array_values($requiredSkills)
					]
				],
				'order' => 'desc'
			]
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
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return 999999;
								}
								try {
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {}
								return 999999;
							"
						],
						'order' => 'asc'
					]
				];
				break;
			case 6:
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return -1;
								}
								try {
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {}
								return -1;
							"
						],
						'order' => 'desc'
					]
				];
				break;
			case 7:
				$sort[] = ['total_exp' => ['order' => 'desc', 'unmapped_type' => 'float']];
				break;
			case 8:
				$sort[] = ['total_exp' => ['order' => 'asc', 'unmapped_type' => 'float']];
				break;
			case 9:
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "params['_source']['immediate_join'] == 'Immediate' ? 0 : 1"
						],
						'order' => 'asc'
					]
				];
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
								['term' => ['condidate_type' => 'bench_condidate']]
							], $this->getSearchConditions($custom_search, $country))
						]
					]
				],
				'minimum_should_match' => 1,
				'filter' => [], // We'll add all filters here
			]
		];
		// Status filter
		// dd($selectedValue);
		if (!empty($selectedValue) && (int)$selectedValue > 0) {
			$boolQuery['bool']['filter'][] = [
				'term' => [
					'status' => (int) $selectedValue
				]
			];
		}

		$params = [
			'index' => ELASTIC_CANDIDATES_BENCH,
			'request_cache' => true,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => $sort,
				'query' => $boolQuery
			]
		];

		if (!$this->elasticService->indexExists(ELASTIC_CANDIDATES_BENCH)) {
			$total = 0;
			$data = [];
		}else{
			$response = $this->elasticService->getClient()->search($params);
			$total = $response['hits']['total']['value'] ?? 0;
			$data = array_map(function ($hit) {
				return $hit['_source'];
			}, $response['hits']['hits']);

			// Enrich rows with work-preference (wm), which is not part of the bench ES index.
			// Why: the Location column shows Remote/Hybrid/Onsite; keep it live without a reindex.
			$pageIds = array_filter(array_column($data, 'id'));
			if (!empty($pageIds)) {
				$wmMap = DB::table('job_applications')->whereIn('id', $pageIds)->pluck('wm', 'id');
				foreach ($data as &$row) {
					$row['wm'] = $wmMap[$row['id'] ?? null] ?? null;
				}
				unset($row);
			}
		}

		$pagination = [
			'total' => $total,
			'current_page' => $page,
			'last_page' =>ceil($total / $size),
			'listType' => 'allcandidate',
		];

		$userPermissions = $this->userPermissions;
		$responseData =  [
			'html' => view('service_company.ajax_pages.bench-candidate-list-ajax', compact('data','userPermissions','company_id','salaryMode','currencySymbole'))->render(),
			'total' => $total,
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		];

		cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_BENCH_CANDIDATES_TIME));


	    return response()->json($responseData);

	}
	protected function getSearchConditions($custom_search, $country) {
		$conditions = [];

		// Apply custom search if it's not empty
		if (!empty($custom_search)) {
				$conditions[] = [
					'bool' => [
						'should' => [
							[
								'match_phrase_prefix' => ['full_name' => '*' . strtolower($custom_search) . '*']
							],
							[
								'match_phrase_prefix' => ['current_designation' => '*' . strtolower($custom_search) . '*']
							],
							[
								'match_phrase_prefix' => ['skills' => '*' . strtolower($custom_search) . '*']
							],
							[
								'wildcard' => [ 'email' => $custom_search ],
							],
							[
								'wildcard' => [ 'phone' => $custom_search ],
							],
							[
								'wildcard' => [ 'country_name' => '*' . strtolower($custom_search) . '*'],
							],
							[
								'match_phrase_prefix' => [ 'qualification_name' => '*' . strtolower($custom_search) . '*'],
							],
							[
								'wildcard' => [ 'state_name' => '*' . strtolower($custom_search) . '*'],
							],
							[
								'wildcard' => [ 'city_name' => '*' . strtolower($custom_search) . '*'],
							],
							[
								'wildcard' => [ 'posted_by' => '*' . strtolower($custom_search) . '*'],
							],
						],
						'minimum_should_match' => 1
					]
				];
		}
	
		return $conditions;

	}
	public function show($id)
	{
		// dd("jfif");
		$company_id = $this->job_additional_detail = $this->bench_assign = "";
		$company_id = auth()->user()->company_id;

		$cachePayload = [
			'id' => $id,
			'user_id' => $this->user->id,
			'company' => $company_id
		];
		
		$versionKey = 'benchviewmore_version_user_'.$this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:benchviewmore:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
		/* ----------------------------
		⚡ Check DragonflyDB first
		----------------------------- */
		if (cache()->has($cacheKeySet)) {
			return response()->json(cache()->get($cacheKeySet));
		}




		$this->currency = GeneralMethod::currency();
		$this->salaryMode = GeneralMethod::salaryMode();
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
			'job_applications.phone_code',
			'job_applications.second_phone_code',
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
			->leftjoin('users', 'users.id', 'job_applications.submitted_by')
			->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
			->leftJoin('bench_master_status', 'bench_master_status.id', '=', 'job_applications.status')
			->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
			->leftjoin('companies', 'companies.id', 'users.company_id')
			->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id');
		$this->benchApplication = $this->benchApplication->where('job_applications.id', $id);
		$this->benchApplication = $this->benchApplication->first();
		$this->job_additional_detail = DB::table('bench_additional_detail')->select('*')->where('bench_additional_detail.bench_id', '=', @$this->benchApplication->id)->orderBy('bench_additional_detail.id', 'ASC')->get();
		$this->bench_assign = DB::table('bench_assign')->select('users.id', 'users.name')->leftjoin('users', 'users.id', 'bench_assign.user_id')->where('bench_assign.bench_applications_id', '=', @$this->benchApplication->id)->orderBy('bench_assign.id', 'ASC')->get();


		$params = [
			'index' => ELASTIC_BENCH_ASSIGN,
			'body'  => [
				'query' => [
					'bool' => [
						'must' => [
							['term' => ['bench_applications_id'   => (int) $id]]
						]
					]
				],
				'sort' => [
					['id' => ['order' => 'desc']]
				],
				'size' => 10000 // optional: get more records
			]
		];

		$response = $this->elasticService->getClient()->search($params);
		// return users
		$this->bench_assign_count = $response['hits']['total']['value'] ?? 0;
		$this->bench_assign_row = collect($response['hits']['hits'] ?? [])->map(fn ($hit) => (object) $hit['_source']);


		$this->boostkeywords = '';
		
            $boostkeywords = DB::table('benchboostkeywordcandidates')->where('candidate_id', $id)->pluck('skill_name')->toArray();
             if(count($boostkeywords)>0){
                $this->boostkeywords = implode(',',$boostkeywords);
             }

			$this->skillExperiences = DB::table('bench_candidate_skill_experience')
			->where('candidate_id', $this->benchApplication->id)
			->get();
			$this->skillExperiencesCount = DB::table('bench_candidate_skill_experience')
			->where('candidate_id', $this->benchApplication->id)
			->count();

			 

		$view = view('service_company.bench-candidates.showdetail', $this->data)->render();
		$responseData =  Reply::dataOnly(['status' => 'success', 'view' => $view]);

		cache()->put($cacheKeySet, $responseData, now()->addMinutes(BENCH_VIEWMORE));


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

		$eventId =5;
		$coinsRequ = 1;
		$companyId = (int) auth()->user()->company_id;
		$packageStatus = GeneralMethod::coinsValidationsAllcompany($companyId,$eventId,$coinsRequ);
		if($packageStatus->status){
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



		$this->recruiterlist = array();



		$this->qualification = DB::table('qualification_master')->select('*')->whereNotIn('id',[57,58,59])->orderBy('qualification_master.display_order', 'ASC')->get();
		$this->currency = GeneralMethod::currency();
		$this->salaryMode = GeneralMethod::salaryMode();
		$this->countries = GeneralMethod::getAllCountryList();

		$this->cities = [];//GeneralMethod::allCities();
        $this->default_loc = GeneralMethod::allCitiesSelected(array());
		$this->currencyConverter = GeneralMethod::currencyConverter();
        $this->skillExperiences =[];

		return view('service_company.bench-candidates.create', $this->data);

	}
	public function store(Request $request)
	{
		
		$packageStatus = GeneralMethod::validatePackageRet('CVACCESS');

        

		 $validator = Validator::make($request->all(), [
			'email' => [
				'required',
				'email:rfc,dns',
				'disposable_email'
			]
		], [
			'email.required' => 'Email is required',
			'email.email' => 'Please enter a valid email',
			'email.disposable_email' => 'Temporary emails are not allowed'
		]);

		if ($validator->fails()) {
			return Reply::dataOnly(['status' => 'mailcheck', 'msgType'=> 'warning', 'message' => $validator->errors()->first()]);	
		}
	
		

		$company_id = '';
		$company_id = auth()->user()->company_id;
		if($request->edit_token !=''){
			$benchApplication = JobApplication::find((int) $request->edit_token);
			$added= false;
		}else{
			$benchApplication = new JobApplication();
			$benchApplication->status = 1;
			$added= true;
			$eventId = 5;
			$coinsDeductions =  GeneralMethod::coinsDeductions((int) $company_id,$eventId,auth()->user()->id,$company_id,'','');
			if($coinsDeductions->status){
				return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message,'company_coins'=> $coinsDeductions->company_coins]);
			}

		}
		 //new BenchApplication();
		 $outsourcing_type = 1;
		 if(@$request->phone=='Full Time'){
		 $outsourcing_type = 1;
		 }elseif(@$request->phone=='Contract'){
		 $outsourcing_type = 2;
		 }elseif(@$request->phone=='Part Time'){
		 $outsourcing_type = 3;
		 }elseif(@$request->phone=='Freelance'){
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

		$total_exp = (isset($request->total_exp_year) && $request->total_exp_year !== '' || isset($request->total_exp_month) && $request->total_exp_month !== '')  ? $request->total_exp_year . '.' . $request->total_exp_month : null;

		$rel_exp = (isset($request->rel_exp_year) && $request->rel_exp_year !== '' || isset($request->rel_exp_month) && $request->rel_exp_month !== '')  ? $request->rel_exp_year . '.' . $request->rel_exp_month : null;

		// $rel_exp = @$request->rel_exp_year.'.'.@$request->rel_exp_month;

		$benchApplication->total_exp = $total_exp;//@$request->total_exp;
		$benchApplication->rel_exp = $rel_exp; //@$request->rel_exp;
		$benchApplication->serving_notice_period_mode = @$request->serving_notice_period;
		$serving_notice_period = $request->serving_notice_period;
		$benchApplication->gender = @$request->gender;
		$benchApplication->percentage_hike = @$request->percentage_hike;
		
		if($serving_notice_period == 0){ 
			$benchApplication->np_id = $request->ddl_np_id;
		}elseif($serving_notice_period == 1) {
			$np_id_date = $request->np_id;
			if(!empty($request->np_id_display)){
				$np_id_date = ($request->np_id) ? $request->np_id : (Carbon::createFromFormat('d/M/Y', $request->np_id_display, 'UTC')->format('Y-m-d') ?? null);
			}
			$benchApplication->np_id = $np_id_date;
		} else if ($serving_notice_period == 2) {
			$benchApplication->np_id = 'Immediate';
			$np_id_date = $request->np_id;
			if(!empty($request->np_id_display)){
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

		$ecurrency  = $currency[$request->bill_rate_currency];
		$econvertedAmount = $request->bill_annual;
		if($request->bill_rate_currency > 0){
			$econvertedAmount = GeneralMethod::convertToINR($econvertedAmount, $ecurrency);
		}

		$ectcdivided = GeneralMethod::salaryDividedByMode(0, $econvertedAmount);
		$benchApplication->ectc_yearly = @$ectcdivided->yearly;
		$benchApplication->ectc_monthly = @$ectcdivided->monthly;
		$benchApplication->ectc_hourly = @$ectcdivided->hourly;

		$currency  = $currency[$request->pay_rate_currency];
		$convertedAmount = $request->pay_annual;
		if($request->pay_rate_currency > 0){
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
		if (\request('kt_tagify_work_location') != "") {
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
				 ->where('city_list.city','!=','')
				 ->where('city_list.city', '=', $cityName)
				 ->where('countries.country_name', '=', $countrName)
				 ->first();

				if($city_row){
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
		$benchApplication->pincode = $experienceGet; //(@$request->pincode) ? @$request->pincode :'';
		$cnt = 0;
		$skillStr = '';
		$skillsAddElastic = $request->skills;
		$skillArr = array_map(function ($skill) {
			return ['value' => $skill];
		}, array_filter($skillsAddElastic));

		// $skillArr = json_decode($request->skills, true);
		if (!empty($skillArr)) {
			foreach ($skillArr as $sval) {
				if ($cnt == 0) {
					$skillStr .= $sval['value'];
				} else {
					$skillStr .= ' , ' . $sval['value'];
				}
				$cnt++;
			}
		}
		// dd($skillStr);
		$benchApplication->skills = $skillStr;
		if ($request->hasFile('photo')) {
		    
		    $file = $request->file('photo');
				$filename = time() . '.' . $file->getClientOriginalExtension();
				$filePath = 'Images/' . $filename;

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
						if($bench_status){
							if($bench_status->bench_status){
								$superadminstatus = 1;
							}
						}
	    $benchApplication->superadminstatus = $superadminstatus;

		$benchApplication->linkedin = $request->linkedin;
		$benchApplication->github = $request->github;
		$benchApplication->behance = $request->behance;
		$benchApplication->otherLink = $request->otherLink;

		$benchApplication->save();
		// if($added){
		// 	$eventId = 5;
		// 	GeneralMethod::coinsDeductions((int) $company_id,$eventId,auth()->user()->id,$company_id,'','');
		// }
		if (!empty($skillArr)) {
			$skillsAddElastic = array_column($skillArr, 'value');
			if(count( $skillsAddElastic)>0){
				GeneralMethod::skillsAddElastic($skillsAddElastic);
			}
		}

		if (!empty($skillArr)) {
			if(DB::table('job_application_skills')->where('job_application_id','=',@$benchApplication->id)->exists()){
					DB::table('job_application_skills')->where('job_application_id','=',@$benchApplication->id)->delete();
			}
			foreach ($skillArr as $sval) {
				$values = array(
					'job_application_id' => $benchApplication->id,
					'skills' => $sval['value']
				);
				if(!DB::table('job_application_skills')->where('job_application_id','=',$benchApplication->id)->where('skills','LIKE', '%'.$sval['value'].'%')->exists()){
					DB::table('job_application_skills')->insert($values);
				}
			}
		}

		if(!DB::table('job_application_skills')->where('job_application_id','=',$benchApplication->id)->where('skills','LIKE', '%'. @$request->current_designation.'%')->exists()){
			DB::table('job_application_skills')->insert(['job_application_id' => $benchApplication->id,'skills' =>  @$request->current_designation]);
			}

		if (!is_null($request->recruiter)) {

			 DB::table('bench_assign')->where('bench_applications_id', $benchApplication->id)->delete();



			$this->elasticService->getClient()->deleteByQuery([
							'index' => ELASTIC_BENCH_ASSIGN,
                            'conflicts' => 'proceed',   // ⭐ THIS LINE FIXES YOUR ERROR
							'body'  => [
								'query' => [
									'bool' => [
										'must' => [
											['term' => ['bench_applications_id' =>(int) $benchApplication->id]],
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
					'id'    => (int) $recArr,
				];
				
				$response = $this->elasticService->getClient()->get($params);
				$recRow = (object) $response['_source'];

				if (!empty(@$recRow->id) && @$recRow->id > 0) {
					$values = array('company_id' => $this->user->company_id, 'user_id' => $recRow->id, 'bench_applications_id' => $benchApplication->id);
					$insertGetId = DB::table('bench_assign')->insertGetId($values);

					// dd( (int) $recArr,$recRow);

					$response = $this->elasticService->getClient()->update([
                        'index' => ELASTIC_BENCH_ASSIGN,
                        'id'    => $insertGetId,
						'retry_on_conflict' => 5,
                        'body'  => [
                            'doc' => [ 
								'id' =>  $insertGetId,
								'users_id' => $recRow->id,
								'name' => $recRow->name,
								'calling_code' => $recRow->calling_code,
								'email' => $recRow->email,
								'mobile' => $recRow->mobile,
                                'bench_applications_id' => (int)$benchApplication->id,
								'designation' => $recRow->designation,
                                'updated_at' => now()->toIso8601String(),
                                'created_at' => now()->toIso8601String()
							],
							'doc_as_upsert' => true,
							'detect_noop' => false
						],
                    ]);

				}

			}
		}
		
		if($request->edit_token !=''){
			if ($request->hasFile('resume')) {

					$companyNameR2 = DB::table('companies')
					->where('id',auth()->user()->company_id)
					->value('company_name');   // change column if needed
					$companyNameR2 = strtolower(trim($companyNameR2 ?? 'unknown'));
					$companyNameR2 = preg_replace('/[^a-z0-9_-]/i','_', $companyNameR2);
					$companyNameRTwo = $companyNameR2 . '_' . auth()->user()->company_id;
					$year  = date('Y');
					$month = date('m');

			        $tempPath = session('resume_temp_path');
					$full_name = session('resume_original_name');
					
					UploadResumeToTestRTwo::dispatch($tempPath, $full_name,$benchApplication->id,$companyNameRTwo,$year,$month)->onConnection('emails');
					$fileUrl = "https://file.jero.works/Companies/{$companyNameRTwo}/Resumes/{$year}/{$month}/{$full_name}";
					DB::table('job_applications')
							->where('id', $benchApplication->id)
							->update(['resume' =>$fileUrl]);
							session()->forget([
								'resume_temp_path',
								'resume_original_name'
							]);
;
			}
		}else{
			if ($request->hasFile('resume')) {

				    $companyNameR2 = DB::table('companies')
					->where('id',auth()->user()->company_id)
					->value('company_name');   // change column if needed
					$companyNameR2 = strtolower(trim($companyNameR2 ?? 'unknown'));
					$companyNameR2 = preg_replace('/[^a-z0-9_-]/i','_', $companyNameR2);
					$companyNameRTwo = $companyNameR2 . '_' . auth()->user()->company_id;
					$year  = date('Y');
					$month = date('m');

					$tempPath = session('resume_temp_path');
					$full_name = session('resume_original_name');
					
					UploadResumeToTestRTwo::dispatch($tempPath, $full_name,$benchApplication->id,$companyNameRTwo,$year,$month)->onConnection('emails');
					$fileUrl = "https://file.jero.works/Companies/{$companyNameRTwo}/Resumes/{$year}/{$month}/{$full_name}";
					DB::table('job_applications')
							->where('id', $benchApplication->id)
							->update(['resume' =>$fileUrl]);
							session()->forget([
								'resume_temp_path',
								'resume_original_name'
							]);
			}
		}

		if(empty($request->resumeCheck)){
					DB::table('job_applications')
				->where('id', $benchApplication->id)
				->update(['resume' =>'']);
		}
	
		$customDetailsQuestion = @$request->customDetailsQuestion;
		$customDetailsAnswer = @$request->customDetailsAnswer;


		if ($customDetailsQuestion > 0) {
			if($request->edit_token !=''){
				DB::table('bench_additional_detail')->where('bench_id', $benchApplication->id)->delete();
				$this->elasticService->getClient()->deleteByQuery([
						'index' => ELASTIC_BENCH_ADDITIONAL_DETAIL,
                        'conflicts' => 'proceed',   // ⭐ THIS LINE FIXES YOUR ERROR
						'body'  => [
							'query' => [
								'bool' => [
									'must' => [
										['term' => ['bench_id' => (int) $benchApplication->id]]
									],
								],
							],
						],
				]);
			}
			$inc = 0;
			foreach ($customDetailsQuestion as $question) {
				$values = array('bench_id' => @$benchApplication->id, 'custom_keys' => @$question, 'custom_keys_value' => @$customDetailsAnswer[$inc]);
				$insertGetIdAdd = DB::table('bench_additional_detail')->insertGetId($values);

				$response = $this->elasticService->getClient()->update([
                        'index' => ELASTIC_BENCH_ADDITIONAL_DETAIL,
                        'id'    => $insertGetIdAdd,
						'retry_on_conflict' => 5,
                        'body'  => [
                            'doc' => [ 
								'id' =>  $insertGetIdAdd,
								'bench_id' =>(int) @$benchApplication->id,
								'custom_keys' => @$question,
								'custom_keys_value' => @$customDetailsAnswer[$inc],
                                'create_at' =>now()->toIso8601String(),
                                'update_at' => now()->toIso8601String()
							],
							'doc_as_upsert' => true,
							'detect_noop' => false
						],
                    ]);

				$inc++;

			}
		}
		
		if($request->edit_token ==''){

			// ElasticBenchCandidateIndex::dispatch($benchApplication->id)->onConnection('emails');
			GeneralMethod::elasticBenchCandidateAddEditIndex($benchApplication->id);

			$skills = $request->input('skills', []);
			$years = $request->input('years', []);
			$months = $request->input('months', []);
			$ratings = $request->input('ratings', []);
            if(count($skillArr)>0){
			foreach ($skills as $skillKey => $exp) {
				 if(!empty($exp)){

					DB::table('bench_candidate_skill_experience')->updateOrInsert([
								'candidate_id' => $benchApplication->id,
								'skill_key'    => $exp,],
								[
								'year'  => filled($years[$skillKey]) ? (int) $years[$skillKey] : null,
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
		}else{
			$exists = $this->elasticService->getClient()->exists([
				'index' => ELASTIC_CANDIDATES_BENCH,
				'id' => $benchApplication->id
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
					->where('condidate_type','=','bench_condidate')
					->first();
					$job_application_skills = DB::table('job_application_skills')
                    ->where('job_application_id', $benchApplication->id)
                    ->get()
                    ->map(fn($s) => [
                        'id' => $s->id,
                        'job_application_id' => $s->id,
                        'skills' => strtolower($s->skills)
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
								'duplicate' => $applications_up->duplicate,
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
								'otherLink' => $applications_up->otherLink
							],
							'doc_as_upsert' => false // Changed to false since we verified existence
						],
						'refresh' => true // Force immediate refresh
					];
		
					$this->elasticService->getClient()->update($updateParams);
					
				   GeneralMethod::addCompanyBenchCandidate($company_id,$benchApplication->id);

				    $skills = $request->input('skills', []);
					$years = $request->input('years', []);
					$months = $request->input('months', []);
					$ratings = $request->input('ratings', []);

					if(count($skillArr)> 0){

					foreach ($skills as $skillKey => $exp) {
						if(!empty($exp)){
							DB::table('bench_candidate_skill_experience')->updateOrInsert([
										'candidate_id' => $benchApplication->id,
										'skill_key'    => $exp],
										[
										'year'  => ($years[$skillKey]) ? (int) $years[$skillKey] : null,
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


		$cachekeys = [
				'allbenchcandidates_version_user_'.$this->user->id,
				'benchviewmore_version_user_'.$this->user->id
			];
			foreach ($cachekeys as $key) {
				GeneralMethod::cacheClean($key);
			}
	
		return Reply::redirect(url('service_company/bench-candidates'), __('menu.jobApplications') . ' ' . __('messages.createdSuccessfully'));

	}
	public function edit($id)
	{
		// dd("hdj");
		$this->pageTitle = __('Edit Candidate');
		abort_if(Gate::denies('edit_job_applications'), 403);
		$id = GeneralMethod::value_decrypt($id);

		$company_id = '';
		$company_id = auth()->user()->company_id;

		$this->statuses = ApplicationStatus::all();
		$this->application = JobApplication::find($id);
		// dd($this->application);
		$this->jobQuestion = @$this->application->job->questions;
		
		$this->jobs = Job::select('id', 'title', 'location_id', 'status', 'start_date', 'end_date', 'section_visibility')->get();
		
		$this->noticeperiod = NoticePeriod::all();
		$this->qualification = DB::table('qualification_master')->select('*')->whereNotIn('id',[57,58,59])->orderBy('qualification_master.display_order', 'ASC')->get();
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

		// dd($id);
		//$jobId 							=	$this->application->job_id; 	
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
		$this->currency = GeneralMethod::currency();
		$this->salaryMode = GeneralMethod::salaryMode();
		$this->countries = GeneralMethod::getAllCountryList();
		$this->statelist = GeneralMethod::statelist($this->application->country);
		$this->documents = DB::table('documents')->select('*')->where('documentable_id', $this->application->id)->where('name', 'Resume')->first();

		$this->job_additional_detail = "";
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

		$this->cityListArr = '';
		if ($this->application->country == 101) {
			$statelist = DB::table('state_list')
				->select('state_list.id', 'state_list.state as name', DB::raw("101 as country_id"))
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
			->select('users.name','users.id')
			->where('bench_assign.company_id', $company_id)
			->where('bench_assign.bench_applications_id', $id)
			->where('users.type', '=', 0)
			->get();

			$this->cities = GeneralMethod::allCities();

			$this->default_loc = GeneralMethod::allCitiesSelected(array($this->application->city_id));

			$this->skillExperiences = DB::table('bench_candidate_skill_experience')
			->where('candidate_id', $this->application->id)
			->get()
			->keyBy('skill_key'); // java, php, html

		    $this->currencyConverter = GeneralMethod::currencyConverter();
			


		return view('service_company.bench-candidates.create', $this->data);
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
		if (\request('search') != "") {
			$search = request('search');
			$benchApplications = $benchApplications->where('job_applications.full_name', 'LIKE', "%$search%");
		}
		// $benchApplications = $benchApplications->whereNotIn('job_applications.status', [5,3]);
		$benchApplications = $benchApplications->where('job_applications.condidate_type', '=', "bench_condidate");
		$benchApplications = $benchApplications->groupBy('job_applications.id');
		$benchApplications = $benchApplications->orderBy('job_applications.id', 'DESC');

		// dd($benchApplications->get());

		return DataTables::of($benchApplications)

			->editColumn('chkbox', function ($row) {
				return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input chk_list_item " name="chk_list_item[]" type="checkbox" value="' . $row->id . '" />
                  </div>';
			})
			->editColumn('Name', function ($row) {

				$bgTextDecore = $strVal = '';
				$str = trim(@$row->full_name);
				$str = $strVal = strtolower($str[0]);
				$str = ord($str);
				if ($str >= 97 && $str <= 101) {
					$bgTextDecore = 'fs-3 bg-light-danger text-danger';
				} else if ($str >= 102 && $str <= 106) {
					$bgTextDecore = 'fs-3 bg-light-warning text-warning';
				} else if ($str >= 107 && $str <= 111) {
					$bgTextDecore = 'fs-3 bg-light-primary text-primary';
				} else if ($str >= 112 && $str <= 116) {
					$bgTextDecore = 'fs-3 bg-light-success text-success';
				} else if ($str >= 117 && $str <= 121) {
					$bgTextDecore = 'fs-3 bg-dark text-secondary';
				} else {
					$bgTextDecore = 'fs-3 bg-light-secondary text-dark';
				}
				$profile_sidebar_css = 'bench_show_application_detail';

				return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
			<a href="javascript:void(0);"   data-row-id="' . @$row->id . '" class="' . $profile_sidebar_css . '">
			<div class="symbol-label ' . @$bgTextDecore . ' border-radius-5">' . strtoupper(@$strVal) . '</div>
			</a>
			</div>                  
			<div class="d-flex flex-column">
			<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-dark fw-bolder text-hover-primary fs-6 ' . $profile_sidebar_css . ' " data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">' . ucwords(substr(@$row->full_name, 0, 10)) . '</a>
			<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-muted fw-bold d-block fs-7 text-hover-primary ' . $profile_sidebar_css . ' " data-toggle="tooltip" title="' . ucwords(@$row->current_designation) . '">' . ucwords(substr(@$row->current_designation, 0, 10)) . '</a>
			</div>';

						})
						->editColumn('Contacts', function ($row) {
							return '<div class="d-flex flex-column">
			<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . @$row->phone . '</a>
			<a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary" data-toggle="tooltip" title="' . @$row->email . '">' . substr(@$row->email, 0, 10) . '</a>
			</div>';
			})
			->editColumn('Exp', function ($row) {
				return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$row->total_exp . '+</a>
                    <span class="text-muted fw-bold d-block fs-7">' . @$row->rel_exp . '+</span>
                  </div>';
			})
			->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {

				$pay_rate_currency = $bill_rate_currency = $pay_rate_amount = $bill_rate_amount = $pay_rate_mode = $bill_rate_mode = "";
				$pay_rate_currency = $currencySymbole[@$row->pay_rate_currency];
				$bill_rate_currency = $currencySymbole[@$row->bill_rate_currency];
				$pay_rate_amount = GeneralMethod::thousandsCurrencyFormat($row->pay_rate_amount);
				$bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($row->bill_rate_amount);

				return '<div class="d-flex flex-column">
						<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$pay_rate_currency . @$ctc . '/' . $pay_rate_amount . '</a>
						<span class="text-muted fw-bold d-block fs-7">' . $bill_rate_currency . @$bill_rate_amount . '/' . $bill_rate_mode . '</span>
					  </div>';
			})

			->editColumn('Availability', function ($row) {
				$npArr = array('Notice period', 'Available from', 'Immediate');
				$npDate = $row->np_id;
				if ($row->serving_notice_period_mode == 0){
					$npDate = ($row->np_id==1) ? $row->np_id.' Day' : $row->np_id.' Days';
				}elseif($row->serving_notice_period_mode == 1) {
					$originalDate = $row->np_id;
					$npDate = 'SNP LWD ' . date("d-M-y", strtotime($originalDate));
				}else{
					$npDate = '';
				}
				return '<div class="d-flex flex-column">
					  <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . $npArr[$row->serving_notice_period_mode] . '</a> 
					  <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' . $row->np_id . '">' . $npDate . '</span>
					</div>';
			})
			->editColumn('Qualifaction', function ($row) {
				return ' <div class="d-flex flex-column">
						  <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="' . @$row->qualification_name . '">' . @$row->qualification_name . '</a>
						  <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' . $row->skills . '">' . substr($row->skills, 0, 12) . '</span> 
						</div>';
			})
			->editColumn('Location', function ($row) {

				$country = $row_country_name = $state = $row_state_name = $city = $row_city_name = '';

				if (empty($row->country)) {
					$row_country_name = '----';
				} else {
					if (!empty(@$row->country)) {
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
                    	<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' . ucwords($row_city_name) . '">' . ucwords(substr($row_city_name, 0, 10)) . '</a>
                    	<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' . ucwords($row_state_name) . ' | ' . strtoupper($row_country_name) . '">' . ucfirst(substr($row_state_name, 0, 5)) . ' | ' . strtoupper(substr($row_country_name, 0, 2)) . '</span>
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
					if (!empty(@$pval->image) && strlen(@$pval->image) > 10) {
						$logo_path = url('public/user-uploads/team_profile_img/' . @$pval->image);
					}
					$html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' . @$pval->name . '">
                            						<img alt="Pic" src="' . @$logo_path . '">
                          						</div>';

				}
				$html .= '<div data-row-id="' . $row->id . '"   class="symbol symbol-30 symbol-circle symbol-light hm-listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list..">
														<span class="symbol-label font-weight-bold">' . $hmCount . '+</span>
													  </div>';
				$html .= '</div>
                    					</div>';
				return $html;
			})
			->editColumn('Date', function ($row) {
				$created_at = '';
				$created_at = $newDate = date("d M , Y", strtotime($row->created_at));
				return '<div class="d-flex flex-column">
                      <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . $created_at . '</a>
                      <span class="text-muted fw-bold text-muted d-block fs-7">.......</span>
                    </div>';
			})

			->editColumn('Requisitions', function ($row) use ($company_id) {
				return '<div class="d-flex flex-column">
							  <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="Custom Job">........</a>
							  <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="Veebrij-u">.......</span>
							</div>';
			})
			->editColumn('Status', function ($row) use ($company_id) {
				return '<div class="d-flex flex-column">
							  <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="fw-bold fs-6  stage_sub_stage ' . $row->css_style . '" data-toggle="modal" data-target="#stage_sub_stage" >
								' . $row->master_status_name . '</a>
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
		$this->recruiter = array();

		$company_id = auth()->user()->company_id;
		$this->team = DB::table('users')
			->leftJoin('bench_assign', function ($join) use ($id) {
				$join->on('bench_assign.user_id', '=', 'users.id');
				$join->on('bench_assign.bench_applications_id', '=', DB::raw("'" . $id . "'"));
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
			return Reply::dataOnly(['status' => 'success', 'msg' => "Remove Record successfully."]);
		} else {
			DB::table('bench_assign')->where('bench_applications_id', $currentBenchId)->where('user_id', $currentUserId)->where('company_id', $company_id)->delete();
			$values = array('company_id' => $company_id, 'user_id' => $currentUserId, 'bench_applications_id' => $currentBenchId);
			DB::table('bench_assign')->insert($values);
			return Reply::dataOnly(['status' => 'success', 'msg' => "Added Record successfully."]);
		}
	}
	public function bench_application_status(Request $request,$id)
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

		if (!empty($this->benchApplication->qualification) && $this->benchApplication->qualification > 0) {
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
		$this->pay_rate_amount = @$pay_rate_currency . @$pay_rate_amount . '/' . @$pay_rate_mode;
		$this->bill_rate_amount = @$bill_rate_currency . @$bill_rate_amount . '/' . @$bill_rate_mode;

		if (empty($this->benchApplication->country)) {
			$this->row_country_name = '----';
		} else {
			if (!empty($this->benchApplication->country)) {
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
		$pipelineCounter = GeneralMethod::bench_explore_listing_counter($id)->applicationCounter;
		$this->activeInterviews = $pipelineCounter->Interview ?? 0;
		$this->clientSubmissions = $pipelineCounter->allCandidate ?? 0;
		$this->profileViewCount = DB::table('bench_count_view_download')->where('type', 1)->where('status', 0)->where('candidate_id', $id)->count();
		$this->resumeDownloadCount = DB::table('bench_count_view_download')->where('type', 2)->where('status', 0)->where('candidate_id', $id)->count();
		$this->benchDurationDays = !empty($this->benchApplication->created_at)
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
			'size'  => 10000, // adjust if needed
			'body'  => [
				'query' => [
					'bool' => [
						'must' => [
							['term' => ['candidate_id' => (int) $bench_id]],
							['terms' => ['job_master_status_id' => [5,6]]],
						]
					]
				]
			]
		];

		$response = $this->elasticService->getClient()->search($params);

		$job_applications = [];
		$benchAppOnJob = collect();
		if (!empty($response['hits']['hits'])) {
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
			'jobID'    => (int) $r->jobId,
		])->values();

		$should = [];

		foreach ($keys as $k) {
			$should[] = [
				'bool' => [
					'must' => [
						['term' => ['bench_id' => $k['bench_id']]],
						['term' => ['jobID' => $k['jobID']]],
					]
				]
			];
		}

		$params = [
			'index' => ELASTIC_BENCH_PLACED,
			'size'  => 10000,
			'_source' => ['bench_id', 'jobID'],
			'body' => [
				'query' => [
					'bool' => [
						'should' => $should,
						'minimum_should_match' => 1
					]
				]
			]
		];

		$response = $this->elasticService->getClient()->search($params);

		$existsMap = [];

		foreach ($response['hits']['hits'] ?? [] as $hit) {
			$src = $hit['_source'];
			$existsMap[$src['bench_id'] . '_' . $src['jobID']] = 1;
		}
		$benchAppOnJob = $benchAppOnJob->map(function ($row) use ($existsMap) {
			$key = $row->candidate_id . '_' . $row->jobId;
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
				}
			])
			->editColumn('chkbox', function ($row) {

			   $exists = $row->exists ?? 0;
                $disabled = '';
				$checked = 'checked';
				if($exists){
					$disabled = 'disabled';
					$checked = '';
				}

				
				return '<div class="form-check form-check-sm form-check-custom form-check-solid">
						  <input type="radio" class="form-check-input chk_list_item " name="chk_list_item" value="' . $row->jobId . '" '.$checked.' '.$disabled.'  />
					  </div>';
			})
			->editColumn('Requisition', function ($row) {

				$clients_name = "";
				$bgTextDecore = $strVal = '';
				$str = trim(@$row->title);
				$str = $strVal = strtolower($str[0]);
				$str = ord($str);
				if ($str >= 97 && $str <= 101) {
					$bgTextDecore = 'fs-3 bg-light-danger text-danger';
				} else if ($str >= 102 && $str <= 106) {
					$bgTextDecore = 'fs-3 bg-light-warning text-warning';
				} else if ($str >= 107 && $str <= 111) {
					$bgTextDecore = 'fs-3 bg-light-primary text-primary';
				} else if ($str >= 112 && $str <= 116) {
					$bgTextDecore = 'fs-3 bg-light-success text-success';
				} else if ($str >= 117 && $str <= 121) {
					$bgTextDecore = 'fs-3 bg-dark text-secondary';
				} else {
					$bgTextDecore = 'fs-3 bg-light-secondary text-dark';
				}

				$profile_detail_class = 'show-detail';
				$clients_name = ucwords(substr(@$row->company_name, 0, 15));  //clients_name

				$recommendation_id = '';
				$recommendation_id = $row->recommendation_id;
				$recommendation_text = '&nbsp;<i class="bi bi-house" style="color:#00CC00"></i>';

				$logo_path = '';
				//$logo_path			=	url('img/client-default.png'); 	
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
                    <a href="javascript:void(0);" data-row-id="' . $row->jobId . '" class="' . $profile_detail_class . '">
                      <div class="symbol-label ' . $bgTextDecore . ' border-radius-5">' . ucwords($strVal) . '</div>
                    </a>
                  </div>';

				  if (!empty($logo_path)) {
                        $icon =
                            '<div data-row-id="' .
                            $row->id .
                            '"  class="symbol symbol-circle symbol-40px overflow-hidden me-2  ' .
                            $profile_detail_class .
                            ' border-radius-5">
                                                <a href="javascript:void(0);">
                                                    <div class="symbol-label border-radius-5">
                                                        <img src="' .
                            str_replace('index.php', '', @$logo_path) .
                            '" alt="Company Logo" class="border-radius-5 w-100">
                                                    </div>
                                                </a>
                                            </div>';
                    }
					$params = [
					'index' => ELASTIC_BENCH_PLACED,
					'size'  => 1,
					'body'  => [
						'query' => [
							'bool' => [
								'must' => [
									['term' => ['bench_id' => (int) $row->candidate_id]],
									['term' => ['jobID' => (int) $row->jobId]],
								]
							]
						]
					]
				];
				
				$response = $this->elasticService->getClient()->search($params);
				$exists = ($response['hits']['total']['value'] ?? 0) > 0;
                $Placed = '';
				if($exists){
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

				return $icon . '<div class="d-flex flex-column">
					<a href="javascript:void(0);"  data-row-id="' . $row->jobId . '"   data-row-job_applications_id="' . $row->candidate_id . '" class="text-dark fw-bolder text-hover-primary fs-6 ' . $profile_detail_class . '"  data-toggle="tooltip1" title="' . ucwords(@$row->title) . '" >' . ucwords(substr(@$row->title, 0, 15)) . ''.$Placed.'</a> 
					<a href="javascript:void(0);" data-row-id="' . $row->jobId . '"   data-row-job_applications_id="' . $row->candidate_id . '"   class="fw-bold text-muted d-block fs-7 text-hover-primary ' . $profile_detail_class . '">' . @$company_or_client . '</a>
                  </div>';


			})
			->editColumn('Exp', function ($row) {

				$params = [
						'index' => ELASTIC_JOBS,
						'id'    =>(int) $row->jobId,
				];
				
				$response = $this->elasticService->getClient()->get($params);
				$thisjob = (object) $response['_source'];


				return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">' . @$thisjob->max_exp . '+</span>
                    <span class="text-muted fw-bold text-muted d-block fs-7">' . @$thisjob->min_exp . '+</span>
                  </div>';
			})
			->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {
				$params = [
						'index' => ELASTIC_JOBS,
						'id'    =>(int) $row->jobId,
				];
				
				$response = $this->elasticService->getClient()->get($params);
				$thisjob = (object) $response['_source'];

				$min_sal_mode = $max_sal_mode = $min_salary_currency = $max_salary_currency = $max_salary = $min_salary = "";
				$min_sal_mode = strtoupper(substr($salaryMode[$thisjob->min_sal_mode], 0, 1));
				$max_sal_mode = strtoupper(substr($salaryMode[$thisjob->max_sal_mode], 0, 1));
				$min_salary_currency = $currencySymbole[@$thisjob->min_salary_currency];
				$max_salary_currency = $currencySymbole[@$thisjob->max_salary_currency];
				$max_salary = GeneralMethod::thousandsCurrencyFormat($thisjob->max_salary);
				$min_salary = GeneralMethod::thousandsCurrencyFormat($thisjob->min_salary);
				return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">' . $min_salary_currency . $max_salary . '/' . $min_sal_mode . '</span>                    
                    <span class="fw-bold text-muted d-block fs-7">' . $min_salary_currency . $min_salary . '/' . $max_sal_mode . '</span>
                  </div>';
			})
			->editColumn('Location', function ($row) {

			    $country_id = $country_name = '';
				$jobid = $row->jobId;
				$params = [
						'index' => ELASTIC_JOBS,
						'id'    =>(int) $row->jobId,
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
				 //DB::table('client_city_jobs')->where(column: 'client_city_jobs.job_id', $jobid)->count();

					$employment_type = 'Full-time';
					if($thisjob->employment_type==1){
						 $employment_type = 'Contract';
					}

				if ($thisjob->work_type == 'yes') {
					return ' <div class="d-flex flex-column">
						<span class="text-dark fw-bolder fs-6">Remote</span>
						<div class="d-flex align-items-center">
						  <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' . ucwords(@$employment_type) . '" >' . ucwords(substr(@$employment_type, 0, 10)) . '</span>
						  </div>
						</div>
					  </div>';
				} elseif ($thisjob->work_type == 'no') {

					if ($jobCitySelected == 1 || $jobCitySelected == 0) {

						return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">' . ucwords(substr(@$city, 0, 10)) . '</span>
								</div>
								
							  <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' . ucwords(@$employment_type) . '" >' . ucwords(substr(@$employment_type, 0, 10)) . '</span>
							  </div>
							</div>
						  </div>';
					} else {
						return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">Multiple</span>
								</div>
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' . ucwords(@$employment_type) . '" >' . ucwords(substr(@$employment_type, 0, 10)) . '</span>
						  </div>
						</div>
					  </div>';
					}

				} elseif ($thisjob->work_type == 'any') {

					if ($jobCitySelected == 1 || $jobCitySelected == 0) {
							
						return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">' . ucwords(substr(@$city, 0, 10)) . '</span>
								</div>
								
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' . ucwords(@$employment_type) . '" >' . ucwords(substr(@$employment_type, 0, 10)) . '</span>
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
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' . ucwords(@$employment_type) . '" >' . ucwords(substr(@$employment_type, 0, 10)) . '</span>

						  </div>
						</div>
					  </div>';
					}

				}


			})
			->editColumn('Status', function ($row) use ($job_status) {

			    $params = [
						'index' => ELASTIC_JOBS,
						'id'    =>(int) $row->jobId,
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

				$ulink = '<a href="javascript.void(0);" data-row-id="' . @$thisjob->id . '" class="d-flex flex-column ' . @$class . ' status_stage text-hover-success" data-toggle="modal" data-target="#status_stage">' . $status . '</a>';
				return '<div class="d-flex flex-column">
                       ' . $ulink . '
                      <span class="fw-bold text-muted d-block fs-7">' . @$row->jobId .'-'. $recommendation_text.'</span>
                    </div>';
			})
			->editColumn('AddedDate', function ($row) {

			    $params = [
						'index' => ELASTIC_JOBS,
						'id'    =>(int) $row->jobId,
				];
				
				$response = $this->elasticService->getClient()->get($params);
				$thisjob = (object) $response['_source'];

				$total_positions = $created_at = '';
				$created_at = $row->created_at;
				$total_positions = number_format($thisjob->total_positions);
				$newDate = date("d M, Y", strtotime($created_at));
				return '<div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">' . $newDate . '</span>
                        <span class="text-muted fw-bold d-block fs-7">' . $total_positions . ' Opening</span>
                    </div>';
			})
			->editColumn('ContactPerson', function ($row) {

				$params = [
							'index' => ELASTIC_JOBS,
							'id'    =>(int) $row->jobId,
					];

				$response = $this->elasticService->getClient()->get($params);
				$thisjob = (object) $response['_source'];
				$job_assign = $thisjob->job_assign;
				$partnersCount = collect($job_assign)->where('company_id',$row->company_id)->count();
				$partnersData = collect($job_assign)->where('company_id',$row->company_id)->map(fn ($item) => (object) $item)->take(1);

				// Plain bold-name + muted-subtitle text (no avatar widget) to match the
				// "Hiring Manager" cell style used in the Change Status drawer's job table.
				$first = $partnersData->first();
				$name = $first ? ucwords($first->user_name) : 'Unassigned';
				$extra = $partnersCount > 1 ? ' +' . ($partnersCount - 1) : '';

				return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">' . $name . $extra . '</span>
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
					$schedule_date = date("d M, Y", strtotime($schedule_date));
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
											<span class="text-dark fw-bolder fs-6">' . @$schedule_date . '</span>
											<span class="fw-bold d-block fs-7 ' . @$className . ' px-1">' . @$feedback_status_name . '</span>
										</div>';

			})
			->editColumn('Stage', function ($row) {

				$name = '';
				if ($row->job_master_status_id == 8) {
					$name = '';
				}
				if (!empty(@$row->childStatus) && @$row->childStatus > 0) {
					$childStatus = '';
					$childStatus = DB::table('candidate_status')->select('*')->where('id', $row->childStatus)->first();
					if (!empty($childStatus)) {
						$name = @$childStatus->name;
					}
				}
				$stage_default_css = 'stage_sub_stage';
				$html = '';
				if ($row->job_master_status_id == 8) {
					$html = '<div class="d-flex flex-column">
								<a href="javascript.void(0);" data-row-id="' . @$row->id . '" class="d-flex flex-column client_stage_sub_stage" data-toggle="modal" data-target="#client_stage_sub_stage">
								<span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1">' . ucwords(substr($row->name, 0, 10)) . '</span>
								<span class="text-muted fw-bold d-block fs-7">' . @$name . '</span>
								</a>
								</div>';
				} else {
					$html = '<div class="d-flex flex-column">
								<span href="javascript.void(0);" data-row-id="' . @$row->id . '" class="d-flex flex-column ">
								<span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1">' . ucwords(substr($row->name, 0, 10)) . '</span>
								<span class="text-muted fw-bold d-block fs-7">' . @$name . '</span>
								</span>
								</div>';
				}
				return $html;
			})
			->editColumn('offeramount', function ($row) use ($salaryMode, $currencySymbole) {
			// dd($row->jobId,$row->job_id);

				$params = [
						'index' => ELASTIC_JOBS,
						'id'    =>(int) $row->jobId,
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
				if($row->job_master_status_id==5 || $row->job_master_status_id==6){

				        $offercurrencyGet =   (@$row->of_offer_currency) ?  @$row->of_offer_currency : 0;
					    $offeramountGet =   (@$row->of_offer_amount) ?  @$row->of_offer_amount : 0;
					    $offermodeGet =   (@$row->of_offer_mode) ?  @$row->of_offer_mode : 0;

					    $offer_currency = $currencySymbole[$offercurrencyGet ];
					
					$offeramount = GeneralMethod::thousandsCurrencyFormat(@$offeramountGet);
					$offer_mode		= substr($salaryMode[@$offermodeGet], 0, 1);
				}
				// dd("ji");
				return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">' .$offer_currency.' '. @$offeramount .'|'.$offer_mode. '</span>
					<span class="text-muted fw-bold d-block fs-7">'.$employment_type.'</span>
                  </div>';
			})
			->editColumn('joindate', function ($row) {
				// dd($row);

				$joined_date = $joined_time = $employment_type  = $date = '-----';
				$joindate = '';
					$date = @$row->of_joined_date;
					if (!empty ($date)) {
						$joindate = date("d-M-y", strtotime($date));
						$joined_time = date("H:i a", strtotime($date));
						if(@$row->employment_type==1){
							$employment_type = 'Contract';
						}else{
							$employment_type = 'Full-time';
						}
					
				}
			
				return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">' . @$joindate . '</span>
					<span class="text-muted fw-bold d-block fs-7">'.$joined_time.'</span>
                  </div>';
			})
			->editColumn('placed', function ($row) {
				$params = [
					'index' => ELASTIC_BENCH_PLACED,
					'size'  => 1,
					'_source' => ['created_at'], // 👈 important
					'body'  => [
						'query' => [
							'bool' => [
								'must' => [
									['term' => ['bench_id' => (int) $row->candidate_id]],
									['term' => ['jobID' => (int) $row->jobId]],
								]
							]
						]
					]
				];

				$response = $this->elasticService->getClient()->search($params);
				$exists = ($response['hits']['total']['value'] ?? 0) > 0;
				$createdAt = '--------';
				$Placed = '--------';
				$class = 'text-muted fw-bold d-block fs-7';

				if (!empty($response['hits']['hits'])) {
					$createdAt = $response['hits']['hits'][0]['_source']['created_at'] ?? null;
					$createdAt = date("d-M-y H:i a", strtotime($createdAt));
					$Placed = 'Placed';
					$class = "badge fw-bolder fs-6 joined-bg px-2 py-1";
				}

				
				return '<div class="d-flex flex-column">
                    <span class="'.$class.'">'.$Placed.'</span>
					<span class="text-muted fw-bold d-block fs-7">'.$createdAt.'</span>
                  </div>';
			})

			->rawColumns(['chkbox', 'Requisition', 'Exp', 'Salary', 'Location', 'Status', 'AddedDate', 'ContactPerson', 'offeramount','joindate','Interview', 'Stage','placed'])
			->addIndexColumn()
			->make(true);


		

		dd($benchAppOnJob);



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
				DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = " . $company_id . " THEN 1 ELSE 0 END) AS thirdparty_counter"),
					DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = " . $company_id . " AND job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS thirdparty_InReview"),
					DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = " . $company_id . " AND job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS thirdparty_Sourcing"),
					DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = " . $company_id . " AND job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS thirdparty_Screening"),
					DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = " . $company_id . " AND job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS thirdparty_Interview"),
					DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = " . $company_id . " AND job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS thirdparty_Negotiation"),
					DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = " . $company_id . " AND job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS thirdparty_Offered"),
					DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = " . $company_id . " AND job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS thirdparty_Joined"),

					DB::raw("SUM(
				CASE 
						WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = " . $company_id . " AND job_applications.master_status_id = 7 THEN 1 
						WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = " . $company_id . " AND job_applications.master_status_id = 9 THEN 1
						ELSE 0 END
			 ) AS thirdparty_Rejected"
					),
				'recommendation_relation.id as recommendation_id',
				'recommendation_relation.from_company_id',
				'recommendation_relation.final_status',
				'recommendation_relation.to_company_id'
			);
		$benchAppOnJob = $benchAppOnJob->join('job_applications as bench_applications', 'bench_applications.id', 'rec_candidate_assign.source_from_id');
		$benchAppOnJob = $benchAppOnJob->join('job_applications', 'job_applications.id', 'rec_candidate_assign.source_application_id');
		$benchAppOnJob = $benchAppOnJob->leftjoin('add_job_wise_condidates', 'add_job_wise_condidates.rec_candidate_assign_id', 'rec_candidate_assign.id')
			->leftjoin('master_status', function($join){
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
						<input type="radio" class="form-check-input chk_list_item " name="chk_list_item" value="' . $row->jobId . '"  />
					  </div>';
			})
			->editColumn('Requisition', function ($row) {

				$clients_name = "";
				$bgTextDecore = $strVal = '';
				$str = trim(@$row->title);
				$str = $strVal = strtolower($str[0]);
				$str = ord($str);
				if ($str >= 97 && $str <= 101) {
					$bgTextDecore = 'fs-3 bg-light-danger text-danger';
				} else if ($str >= 102 && $str <= 106) {
					$bgTextDecore = 'fs-3 bg-light-warning text-warning';
				} else if ($str >= 107 && $str <= 111) {
					$bgTextDecore = 'fs-3 bg-light-primary text-primary';
				} else if ($str >= 112 && $str <= 116) {
					$bgTextDecore = 'fs-3 bg-light-success text-success';
				} else if ($str >= 117 && $str <= 121) {
					$bgTextDecore = 'fs-3 bg-dark text-secondary';
				} else {
					$bgTextDecore = 'fs-3 bg-light-secondary text-dark';
				}

				$profile_detail_class = 'show-detail';
				$clients_name = ucwords(substr(@$row->company_name, 0, 15));  //clients_name

				$recommendation_id = '';
				$recommendation_id = $row->recommendation_id;
				$recommendation_text = '&nbsp;<i class="bi bi-house" style="color:#00CC00"></i>';

				$logo_path = '';
				//$logo_path			=	url('img/client-default.png'); 	
				if (!empty($row->logo_logical_path)) {

					$logo_path = @$row->logo_logical_path;
					if(@$row->company_id != auth()->user()->company_id){
						$logo_path = @$row->logo;
					}

					if($row->thirdparty_counter > 0 && $recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id){
						$logo_path = @$row->logo;
			       }
				}
				if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
					$recommendation_text = '&nbsp;<i style="color: #FF0000; font-weight:bold" class="fa fa-users" aria-hidden="true"></i>';
					$profile_detail_class = 'client-show-detail';
					$clients_name = ucwords(substr(@$row->company_name, 0, 15));

					if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
						$logo_path = @$row->logo;
					}
				}


				if (@$row->self_company == 1) {
					if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
						$logo_path = @$row->logo;
					}
				}

				$company_or_client = GeneralMethod::companyDetailsByClient($row->jobId);
				

				$icon = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="' . $row->jobId . '" class="' . $profile_detail_class . '">
                      <div class="symbol-label ' . $bgTextDecore . ' border-radius-5">' . ucwords($strVal) . '</div>
                    </a>
                  </div>';

				if (!empty($logo_path)) {
					$icon = '<div data-row-id="' . $row->jobId . '"  class="symbol symbol-circle symbol-40px overflow-hidden me-2  ' . $profile_detail_class . ' border-radius-5">
                                        <a href="javascript:void(0);">
                                            <div class="symbol-label border-radius-5">
                                            	<img src="' . @$logo_path . '" alt="Company Logo" class="border-radius-5 w-100">
                                            </div>
                                        </a>
                                    </div>';
				}

				return $icon . '<div class="d-flex flex-column">
					<a href="javascript:void(0);"  data-row-id="' . $row->jobId . '"   data-row-job_applications_id="' . $row->job_applications_id . '" class="text-dark fw-bolder text-hover-primary fs-6 ' . $profile_detail_class . '"  data-toggle="tooltip1" title="' . ucwords(@$row->title) . '" >' . ucwords(substr(@$row->title, 0, 15)) . '</a> 
					<a href="javascript:void(0);" data-row-id="' . $row->jobId . '"   data-row-job_applications_id="' . $row->job_applications_id . '"   class="fw-bold text-muted d-block fs-7 text-hover-primary ' . $profile_detail_class . '">' . @$company_or_client . '</a>
                  </div>';
			})
			->editColumn('Exp', function ($row) {
				return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">' . @$row->max_exp . '+</span>
                    <span class="text-muted fw-bold text-muted d-block fs-7">' . @$row->min_exp . '+</span>
                  </div>';
			})
			->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {
				$min_sal_mode = $max_sal_mode = $min_salary_currency = $max_salary_currency = $max_salary = $min_salary = "";
				$min_sal_mode = strtoupper(substr($salaryMode[$row->min_sal_mode], 0, 1));
				$max_sal_mode = strtoupper(substr($salaryMode[$row->max_sal_mode], 0, 1));
				$min_salary_currency = $currencySymbole[@$row->min_salary_currency];
				$max_salary_currency = $currencySymbole[@$row->max_salary_currency];
				$max_salary = GeneralMethod::thousandsCurrencyFormat($row->max_salary);
				$min_salary = GeneralMethod::thousandsCurrencyFormat($row->min_salary);
				return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">' . $min_salary_currency . $max_salary . '/' . $min_sal_mode . '</span>                    
                    <span class="fw-bold text-muted d-block fs-7">' . $min_salary_currency . $min_salary . '/' . $max_sal_mode . '</span>
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
					if($row->employment_type==1){
						 $employment_type = 'Contract';
					}

				if ($row->work_type == 'yes') {
					return ' <div class="d-flex flex-column">
						<span class="text-dark fw-bolder fs-6">Remote</span>
						<div class="d-flex align-items-center">
						  <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' . ucwords(@$employment_type) . '" >' . ucwords(substr(@$employment_type, 0, 10)) . '</span>
						  </div>
						</div>
					  </div>';
				} elseif ($row->work_type == 'no') {

					if ($jobCitySelected == 1 || $jobCitySelected == 0) {
						$client_loc = DB::table('client_city_jobs')
							->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
							->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
							->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
							->select('city_list.city as city_name','countries.country_name')
							->where('client_city_jobs.job_id', $jobid)
							->first();
							$city = substr(@$client_loc->city_name, 0, 21);
							$country_name = substr(@$client_loc->country_name, 0, 6);

						return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">' . ucwords(substr(@$city, 0, 10)) . '</span>
								</div>
								
							  <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' . ucwords(@$employment_type) . '" >' . ucwords(substr(@$employment_type, 0, 10)) . '</span>
							  </div>
							</div>
						  </div>';
					} else {
						return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">Multiple</span>
								</div>
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' . ucwords(@$employment_type) . '" >' . ucwords(substr(@$employment_type, 0, 10)) . '</span>
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
							->select('city_list.city as city_name','countries.country_name')
							->where('client_city_jobs.job_id', $jobid)
							->first();
							$city = substr(@$client_loc->city_name, 0, 21);
							$country_name = substr(@$client_loc->country_name, 0, 6);
							
						return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">' . ucwords(substr(@$city, 0, 10)) . '</span>
								</div>
								
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' . ucwords(@$employment_type) . '" >' . ucwords(substr(@$employment_type, 0, 10)) . '</span>
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
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' . ucwords(@$employment_type) . '" >' . ucwords(substr(@$employment_type, 0, 10)) . '</span>

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
				$ulink = '<a href="javascript.void(0);" data-row-id="' . @$row->id . '" class="d-flex flex-column ' . @$class . ' status_stage text-hover-success" data-toggle="modal" data-target="#status_stage">' . $status . '</a>';
				return '<div class="d-flex flex-column">
                       ' . $ulink . '
                      <span class="fw-bold text-muted d-block fs-7">R-' . @$row->jobId . '</span>
                    </div>';
			})
			->editColumn('AddedDate', function ($row) {

				$total_positions = $created_at = '';
				$created_at = $row->created_at;
				$total_positions = number_format($row->total_positions);
				$newDate = date("d M, Y", strtotime($created_at));
				return '<div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">' . $newDate . '</span>
                        <span class="text-muted fw-bold d-block fs-7">' . $total_positions . '</span>
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
					if (!empty(@$pval->image) && strlen(@$pval->image) > 10)
						$image_path = url('public/user-uploads/team_profile_img/' . @$pval->image);
					$html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' . @$pval->name . '"><img alt="Pic" src="' . @$image_path . '"></div>';
				}
				$html .= '<div data-row-id="' . $row->jobId . '"   class="symbol symbol-30 symbol-circle symbol-light hm-listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list.."><span class="symbol-label font-weight-bold">' . $partnersCount . '+</span></div>';
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
					$schedule_date = date("d M, Y", strtotime($schedule_date));
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
											<span class="text-dark fw-bolder fs-6">' . @$schedule_date . '</span>
											<span class="fw-bold d-block fs-7 ' . @$className . ' px-1">' . @$feedback_status_name . '</span>
										</div>';

			})
			->editColumn('Stage', function ($row) {

				$name = '';
				if ($row->master_status_id == 8) {
					$name = '';
				}
				if (!empty(@$row->child_status_id) && @$row->child_status_id > 0) {
					$childStatus = '';
					$childStatus = DB::table('candidate_status')->select('*')->where('id', $row->child_status_id)->first();
					if (!empty($childStatus)) {
						$name = @$childStatus->name;
					}
				}
				$stage_default_css = 'stage_sub_stage';
				$html = '';
				if ($row->master_status_id == 8) {
					$html = '<div class="d-flex flex-column">
								<a href="javascript.void(0);" data-row-id="' . @$row->id . '" class="d-flex flex-column client_stage_sub_stage" data-toggle="modal" data-target="#client_stage_sub_stage">
								<span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1">' . ucwords(substr($row->name, 0, 10)) . '</span>
								<span class="text-muted fw-bold d-block fs-7">' . @$name . '</span>
								</a>
								</div>';
				} else {
					$html = '<div class="d-flex flex-column">
								<span href="javascript.void(0);" data-row-id="' . @$row->id . '" class="d-flex flex-column ">
								<span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1">' . ucwords(substr($row->name, 0, 10)) . '</span>
								<span class="text-muted fw-bold d-block fs-7">' . @$name . '</span>
								</span>
								</div>';
				}
				return $html;
			})
			->editColumn('offeramount', function ($row) use ($salaryMode, $currencySymbole) {
			// dd($row->jobId,$row->job_id);
				$rowset = DB::table('offer_job_applications')->where('application_id',$row->job_application_id)->where('job_id',$row->job_id )->first();
				// dd($rowset->offer_currency);
				$offeramount = '--------';
				$offer_currency = '';
				$offer_mode = '';
				if($row->master_status_id==5 || $row->master_status_id==6){

				        $offercurrencyGet =   (@$rowset->offer_currency) ?  @$rowset->offer_currency : 0;
					    $offeramountGet =   (@$rowset->offer_amount) ?  @$rowset->offer_amount : 0;
					    $offermodeGet =   (@$rowset->offer_mode) ?  @$rowset->offer_mode : 0;

					    $offer_currency = $currencySymbole[$offercurrencyGet ];
					
					$offeramount = GeneralMethod::thousandsCurrencyFormat(@$offeramountGet);
					$offer_mode		= substr($salaryMode[@$offermodeGet], 0, 1);
				}
				// dd("ji");
				return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">' .$offer_currency.' '. @$offeramount .'|'.$offer_mode. '</span>
                  </div>';
			})
			->editColumn('joindate', function ($row) {
			
				$rowset = DB::table('offer_job_applications')->where('application_id',$row->job_application_id)->where('job_id',$row->job_id)->first();
				$joindate = '';
				if($row->master_status_id==5 || $row->master_status_id==6){
					$joindate = @$rowset->joined_date ? GeneralMethod::ymdTodmyNotTime($rowset->joined_date) : '';
				}
				return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">' . @$joindate . '</span>
                  </div>';
			})

			->rawColumns(['chkbox', 'Requisition', 'Exp', 'Salary', 'Location', 'Status', 'AddedDate', 'ContactPerson', 'offeramount','joindate','Interview', 'Stage'])
			->addIndexColumn()
			->make(true);

	}
	public function status_submit(Request $request)
	{
        // dd($request->all());
		$company_id = "";
		$company_id = auth()->user()->company_id;
		$bench_master_status = @$request->bench_master_status;
		$application_id = @$request->bench_application_id;
		$placedid = @$request->placedid;

		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();

		if ($bench_master_status == 2) {

				$validations = GeneralMethod::BenchcandidateValidation($application_id);
				if($validations->status){
					return Reply::dataOnly(['status'=> 'candidatevalidation', 'message' => $validations->message]);
				}
	
				// if(!JobApplication::join('documents','documents.documentable_id','=','job_applications.id')->where('documents.documentable_id','=',$application_id)->exists()){
				// 	return Reply::dataOnly(['status' => 'cvpending', 'message' => 'Resume pending this job application!']); 
				// }
	
			   $eventId = 16;
			   $companyId =  $company_id;
			   $coinsRequ = 1;;
			   $coinsDeductions = GeneralMethod::coinsValidationsAllcompany($companyId,$eventId,$coinsRequ);
				
			if($coinsDeductions->status){
				return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message,'company_coins'=> $coinsDeductions->company_coins]);
			}else{

				DB::table('bench_count_view_download')->where('candidate_id','=', @$request->bench_application_id)->update(['status'=>1]);

				$coinsDeductions =  GeneralMethod::coinsDeductions((int) $company_id, $eventId,auth()->user()->id,$company_id,'','');
				
				$bench_application_id = $bench_master_status = "";
						$bench_application_id = @$request->bench_application_id;
						$bench_master_status = @$request->bench_master_status;
						$BenchApplication = JobApplication::find($bench_application_id);
						$BenchApplication->status = @$bench_master_status;
						$bench_status = DB::table('global_settings')->select('bench_status')->first();
						$superadminstatus = 0;
						$benchstatus = "Under Approval";
						if($bench_status){
							if($bench_status->bench_status){
								$superadminstatus = 1;
								$benchstatus = "Live on Platform";
							}
						}
						$BenchApplication->superadminstatus = $superadminstatus;
						$BenchApplication->countforadd = 0;
						$BenchApplication->master_status_id = 0;
						$BenchApplication->save();


						$teamRankElastic = [
						'company_id'=>$this->user->company_id,
						'user_id'=>$this->user->id,
						'candidate_id'=>0,
						'profileUpload'=>0,
						'interviewScheduled'=>0,
						'interviewNoShow'=>0,
						'offerReleased'=>0,
						'candidateJoined'=>0,
						'jobsPosted'=>0,
						'feedbackGivenOnCVs'=>0,
						'postJobToVendorMarket'=>0,
						'outsourceJobsToVendors'=>0,
						'listBenchCandidates'=>3,
						'respondToBenchRequests'=>0
					];
					GeneralMethod::teamRankElastic($teamRankElastic);

						
							
							


						$rowdata = DB::table('job_applications')
						->join('companies','companies.id','=','job_applications.company_id')
						->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id')
						->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
						->leftJoin('countries', 'countries.id', '=', 'job_applications.country')
						->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
						->select('job_applications.*','companies.company_name','city_list.city as city_name','state_list.state as state_name','countries.country_name','name as qualification_name')->where('job_applications.id',$bench_application_id)->first();

						$bench_assign_hr = DB::table('bench_assign')->join('users','users.id','=','bench_assign.user_id')->where('bench_applications_id', $bench_application_id)->pluck('name')->toArray();
						$rm = '';
						if(count($bench_assign_hr) > 0 ){
							$rm = implode(",",$bench_assign_hr);
						}

						$npArr = ['Notice period', 'Available from', 'Immediate'];
						$npDate = $rowdata->np_id;
						if ($rowdata->serving_notice_period_mode == 0) {
							$npDate = $rowdata->np_id == 1 ? $rowdata->np_id . ' Day' : $rowdata->np_id . ' Days';
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

						$experience_show = $formattedExperience. ' Y'.'<br>'.' '. $rel_formattedExperience. ' Y';

						$bill_rate = $bill_rate_currency .' ' . @$bill_rate_amount .'/' .$bill_rate_mode .'<br>'. @$pay_rate_currency.' '. @$pay_rate_amount .'/' .$pay_rate_mode;


						// ucwords(@$row->current_designation)

						$date_set = date('d M y');

						$name_and_designation= $rowdata->full_name.'<br>'.$rowdata->current_designation;

						$location = ucwords(mb_substr($rowdata->city_name, 0, 10)).'<br>'.ucfirst(mb_substr($rowdata->state_name, 0, 5)).'|'.strtoupper(mb_substr($rowdata->country_name, 0, 2));



						$basecompany = GeneralMethod::baseCompany();
						$senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
						$fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
						$attachments = array();
						$cc = array();
						$checkMailPermision = GeneralMethod::mailPermission(27);
                        $mailTypeSubject = GeneralMethod::mailTypeSubject(27,2);
						if($checkMailPermision){ 
						$subject = str_replace(["[{STATUS}]","[{CANDIDATE_NAME}]","[{BENCH_ID}]","[{DESIGNATION}]"],[$benchstatus,$rowdata->full_name,$rowdata->id,$rowdata->current_designation], $mailTypeSubject->subject);
                        $template_description = $mailTypeSubject->body_description;
						$template_description = str_replace(["[{VENDOR_COMPANY_NAME}]","[{BENCH_CANDIDATE_NAME}]","[{BENCH_ID}]","[{RM}]","[{EXPERIENCE}]","[{RATE}]","[{AVAILABILITY}]","[{DATESET}]","[{LOCATION}]","[{EDUCATION}]","[{STATUS}]"],[$rowdata->company_name,$name_and_designation, $rowdata->id,$rm,$experience_show,$bill_rate,$abailabilty,$date_set,$location,$qualification,$benchstatus], $template_description);

						$message = $template_description;
						$notificationemail = GeneralMethod::notification($rowdata->company_id,'Sales'); //Recuitment Sales
						$defailsEmail = DB::table('companies')->where('id',$rowdata->company_id)->first()->company_email;
						$to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
						$datamail = [
							'message' => $message
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
							'id' => $application_id
						]);
						// dd($application_id);
						if ($exists) {
						   
							// 2. Prepare the update timestamp
							$updateTime = now()->toIso8601String();
							$master_status_name = DB::table('bench_master_status')
							->select('*')
							->where('id', (int)$bench_master_status)
							->first('name');
				
						   // 3. Execute update with debugging
							$updateParams = [
								'index' => ELASTIC_CANDIDATES_BENCH,
								'id' => $application_id,
								'body' => [
									'doc' => [
										'status' => (int)$bench_master_status,
										'master_status_name' => $master_status_name->name,
										'css_style' => $master_status_name->css_style,
										'updated_at' => $updateTime
									],
									'doc_as_upsert' => false // Changed to false since we verified existence
								],
								'refresh' => true // Force immediate refresh
							];
				
							$this->elasticService->getClient()->update($updateParams);

							$updateParamsCandidate = [
								'index' => ELASTIC_CANDIDATES,
								'body'  => [
									'script' => [
										'source' => "ctx._source.status = params.status;",
										'lang'   => 'painless',
										'params' => [
											'status' => (int) $bench_master_status,
										]
									],
									'query' => [
										'term' => [
											'candidate_id' => $application_id
										]
									]
								],
								'refresh' => true
							];
		
							$this->elasticService->getClient()->updateByQuery($updateParamsCandidate);

							
						}
                      
                      if($placedid>0){
						$existsCandidate = $this->elasticService->getClient()->exists([
							'index' => ELASTIC_CANDIDATES,
							'id' => $placedid
						]);
						if ($existsCandidate) {
				          
							$updateParamsCandidate = [
								'index' => ELASTIC_CANDIDATES,
								'id' => $placedid,
								'body' => [
									'doc' => [
										'status' => (int)$bench_master_status,
									],
									'doc_as_upsert' => false // Changed to false since we verified existence
								],
								'refresh' => true // Force immediate refresh
							];
				
							$this->elasticService->getClient()->update($updateParamsCandidate);
						}
					    }

						$cachekeys = [
							'allbenchcandidates_version_user_'.$this->user->id,
							'benchviewmore_version_user_'.$this->user->id,
							'allbenchofferListing_version_user_'.$this->user->id
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
			$bench_application_id = $bench_master_status = "";
			$bench_application_id = @$request->bench_application_id;
			$bench_master_status = @$request->bench_master_status;
			$BenchApplication = JobApplication::find($bench_application_id);
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

					$bench_applied_detail = $bench_applied_detail->leftJoin("rec_candidate_assign", function ($join) {
						$join->on("rec_candidate_assign.source_from_id", "=", "job_applications.id");
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
					if (!empty(@$bench_applied_detail->country)) {
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
					if (!empty($bench_assign_list)) {
						foreach ($bench_assign_list as $assignVal) {
							if ($cnt == 0) {
								$bench_assign_string = $assignVal->name;
							} else {
								$bench_assign_string = $bench_assign_string . ' , ' . $assignVal->name;
							}
							$cnt++;
						}
					}
					
					$offerData =DB::table('offer_job_applications')->where('application_id',@$request->bench_application_id)->where('job_id',@$request->chk_list_item)->first();
					
					$values = array(
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

					);
					$detail = DB::table('add_job_wise_condidates')->where('job_id', '=', @$request->chk_list_item)->where('job_application_id', '=', @$request->bench_application_id)->first();
					// dd($detail);
					DB::table('offer_job_applications')->where('job_id', @$request->chk_list_item)->where('application_id', '=', @$request->bench_application_id)->limit(1)->update(['joined'=>$detail->id]);
					DB::table('bench_place')->insert($values);
					$placeIdAdd = DB::getPdo()->lastInsertId();
					GeneralMethod::benchPlacedIndexingupload($placeIdAdd);
					
					$cachekeys = [
							'allbenchplacedListing_version_user_'.$this->user->id,
						];
						foreach ($cachekeys as $key) {
							GeneralMethod::cacheClean($key);
						}

				}else{
					return Reply::dataOnly(['status' => 'info', 'message' => 'Please select requisition!']);
				}

			}

			$exists = $this->elasticService->getClient()->exists([
				'index' => ELASTIC_CANDIDATES_BENCH,
				'id' => $application_id
			]);
			if ($exists) {
			
				// 2. Prepare the update timestamp
				$updateTime = now()->toIso8601String();
				$master_status_name = DB::table('bench_master_status')
				->select('*')
				->where('id', (int)$bench_master_status)
				->first('name');
	
			   // 3. Execute update with debugging
				$updateParams = [
					'index' => ELASTIC_CANDIDATES_BENCH,
					'id' => $application_id,
					'body' => [
						'doc' => [
							'status' => (int)$bench_master_status,
							'master_status_name' => $master_status_name->name,
							'css_style' => $master_status_name->css_style,
							'updated_at' => $updateTime
						],
						'doc_as_upsert' => false // Changed to false since we verified existence
					],
					'refresh' => true // Force immediate refresh
				];
	
				$this->elasticService->getClient()->update($updateParams);


				$updateParamsCandidate = [
					'index' => ELASTIC_CANDIDATES,
					'body'  => [
						'script' => [
							'source' => "ctx._source.status = params.status;",
							'lang'   => 'painless',
							'params' => [
								'status' => (int) $bench_master_status,
							]
						],
						'query' => [
							'term' => [
								'candidate_id' => $application_id
							]
						]
					],
					'refresh' => true
				];

	
				$this->elasticService->getClient()->updateByQuery($updateParamsCandidate);

			}
			if($placedid>0){
				$existsCandidate = $this->elasticService->getClient()->exists([
					'index' => ELASTIC_CANDIDATES,
					'id' => $placedid
				]);
				if ($existsCandidate) {
				  
					$updateParamsCandidate = [
						'index' => ELASTIC_CANDIDATES,
						'id' => $placedid,
						'body' => [
							'doc' => [
								'status' => (int)$bench_master_status,
							],
							'doc_as_upsert' => false // Changed to false since we verified existence
						],
						'refresh' => true // Force immediate refresh
					];
		
					$this->elasticService->getClient()->update($updateParamsCandidate);
				}
			}

			$cachekeys = [
				'benchviewmore_version_user_'.$this->user->id,
				'allbenchcandidates_version_user_'.$this->user->id,
				'allbenchofferListing_version_user_'.$this->user->id
			];
			foreach ($cachekeys as $key) {
				GeneralMethod::cacheClean($key);
			}

			return Reply::dataOnly(['status' => 'success', 'message' => 'Bench Application status update successfully !']);
		}

	}
	public function offerListing()
	{
		$this->pageTitle = __('Offer Listing');
		$this->allBenchApplication = BenchApplication::count();
		$stats = $this->benchOfferStats();
		$this->benchOfferHeroStats = $stats['hero'];
		$this->benchOfferTabStats = $stats['tabs'];
		$this->benchOfferLocations = $stats['locations'];
		return view('service_company.bench-candidates.offerListing', $this->data);
	}

	/**
	 * Hero KPI cards, status-tab counts (All/Pending/Offer Released/Accepted/Joining Soon/
	 * Expired) and the "All Locations" filter options for the Shortlisted Bench Candidates
	 * listing — computed with a single Elasticsearch `filters` + `terms` aggregation call
	 * (candidates index) and cached per-company, so the page load stays one ES round trip.
	 */
	private function benchOfferStats(): array
	{
		$default = [
			'hero' => [
				'total_shortlisted' => 0,
				'joining_this_week' => 0,
				'offer_released' => 0,
				'offer_accepted' => 0,
				'joined' => 0,
			],
			'tabs' => [
				'all' => 0,
				'pending' => 0,
				'released' => 0,
				'accepted' => 0,
				'joining_soon' => 0,
				'expired' => 0,
			],
			'locations' => [],
		];

		$company_id = auth()->user()->company_id;

		if (!$this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
			return $default;
		}

		$versionKey = 'allbenchofferListing_version_user_' . $this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:benchofferStats:' . $company_id . ':' . $version;

		if (cache()->has($cacheKeySet)) {
			return cache()->get($cacheKeySet);
		}

		$activeMust = ['terms' => ['job_master_status_id' => [5, 6]]];
		$activeMustNot = ['term' => ['status' => 3]];
		$noOfferShould = [
			'bool' => [
				'should' => [
					['bool' => ['must_not' => [['exists' => ['field' => 'of_offer_amount']]]]],
					['bool' => ['must_not' => [['exists' => ['field' => 'of_joined_date']]]]],
				],
				'minimum_should_match' => 1,
			],
		];

		$params = [
			'index' => ELASTIC_CANDIDATES,
			'request_cache' => true,
			'body' => [
				'size' => 0,
				'query' => [
					'bool' => [
						'must' => [
							['term' => ['other_company' => $company_id]],
							['term' => ['condidate_type' => 'bench_condidate']],
						],
					],
				],
				'aggs' => [
					'hero_stats' => [
						'filters' => [
							'filters' => [
								'total_shortlisted' => [
									'bool' => ['must' => [$activeMust], 'must_not' => [$activeMustNot]],
								],
								'joining_this_week' => [
									'bool' => [
										'must' => [
											$activeMust,
											['range' => ['of_joined_date' => ['gte' => 'now/d', 'lte' => 'now+7d/d']]],
										],
										'must_not' => [$activeMustNot],
									],
								],
								'offer_released' => [
									'bool' => [
										'must' => [$activeMust, ['term' => ['of_offer_acceptance' => 1]]],
										'must_not' => [$activeMustNot],
									],
								],
								'offer_accepted' => [
									'bool' => [
										'must' => [$activeMust, ['term' => ['of_offer_acceptance' => 2]]],
										'must_not' => [$activeMustNot],
									],
								],
								'joined' => [
									'bool' => [
										'must' => [
											['term' => ['job_master_status_id' => 6]],
											['term' => ['status' => 3]],
										],
									],
								],
								'pending' => [
									'bool' => ['must' => [$activeMust, $noOfferShould], 'must_not' => [$activeMustNot]],
								],
								'expired' => [
									'bool' => [
										'must' => [$activeMust, ['term' => ['of_offer_acceptance' => 4]]],
										'must_not' => [$activeMustNot],
									],
								],
							],
						],
					],
					'active_locations' => [
						'filter' => ['bool' => ['must' => [$activeMust], 'must_not' => [$activeMustNot]]],
						'aggs' => [
							'by_city' => [
								'terms' => ['field' => 'city_name', 'size' => 30, 'order' => ['_count' => 'desc']],
							],
						],
					],
				],
			],
		];

		try {
			$response = $this->elasticService->getClient()->search($params);
		} catch (\Exception $e) {
			return $default;
		}
		$buckets = $response['aggregations']['hero_stats']['buckets'] ?? [];
		$cityBuckets = $response['aggregations']['active_locations']['by_city']['buckets'] ?? [];

		$stats = [
			'hero' => [
				'total_shortlisted' => $buckets['total_shortlisted']['doc_count'] ?? 0,
				'joining_this_week' => $buckets['joining_this_week']['doc_count'] ?? 0,
				'offer_released' => $buckets['offer_released']['doc_count'] ?? 0,
				'offer_accepted' => $buckets['offer_accepted']['doc_count'] ?? 0,
				'joined' => $buckets['joined']['doc_count'] ?? 0,
			],
			'tabs' => [
				'all' => $buckets['total_shortlisted']['doc_count'] ?? 0,
				'pending' => $buckets['pending']['doc_count'] ?? 0,
				'released' => $buckets['offer_released']['doc_count'] ?? 0,
				'accepted' => $buckets['offer_accepted']['doc_count'] ?? 0,
				'joining_soon' => $buckets['joining_this_week']['doc_count'] ?? 0,
				'expired' => $buckets['expired']['doc_count'] ?? 0,
			],
			'locations' => array_map(fn ($b) => $b['key'], $cityBuckets),
		];

		cache()->put($cacheKeySet, $stats, now()->addMinutes(ALL_BENCH_OFFER_LISTING_TIME));

		return $stats;
	}
	public function offerListingData_ajax(Request $request)
	{
		
		abort_if(Gate::denies('view_job_applications'), 403);
		$company_id = auth()->user()->company_id;
		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();
	

		$chkAdminRole = $this->user->hasRole('admin');


		      $searchQuery = $request->input('q');
			  $valCheck = $request->input('sortingfilter');
			  $page = max((int) $request->input('page', 1), 1);
			  $size = (int) $request->input('size', 10); // Default 20 results per page
			  $from = ($page - 1) * $size;
			  $sort = [];

			  $searchTerm = trim((string) ($request->input('search') ?: $request->input('cus_search')));
			  $tab = (string) $request->input('tab', 'all');
			  $location = trim((string) $request->input('country'));

			  $cachePayload = [
					'page' => $page,
					'size' => $size,
					'q' => $searchQuery,
					'company' => $company_id,
					'cus_search' => $request->input('cus_search'),
					'search' => $searchTerm,
					'tab' => $tab,
					'location' => $location,
					'country' => $request->input('country'),
					'user_id' => $this->user->id,
					'sortingfilter' => $valCheck
				];

				
				$versionKey = 'allbenchofferListing_version_user_'.$this->user->id;
				$version = cache()->get($versionKey, 1);
				$cacheKeySet = 'ats:allbenchofferListing:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
				/* ----------------------------
				⚡ Check DragonflyDB first
				----------------------------- */
				if (cache()->has($cacheKeySet)) {
					return response()->json(cache()->get($cacheKeySet));
				}

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
					case 5: // Notice period ASC (ignore Immediate)
						$sort[] = [
							'_script' => [
								'type' => 'number',
								'script' => [
									'lang' => 'painless',
									'source' => "
										if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
											return 999999; // Put missing/Immediate at the end
										}
										try {
											// Extract first number from comma-separated values
											String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
											if (parts.length > 0) {
												return Double.parseDouble(parts[0].trim());
											}
										} catch (Exception e) {
											// If parsing fails
										}
										return 999999; // Invalid values at the end
									"
								],
								'order' => 'asc'
							]
						];
						break;
					case 6: // Notice period DESC (ignore Immediate)
						$sort[] = [
							'_script' => [
								'type' => 'number',
								'script' => [
									'lang' => 'painless',
									'source' => "
										if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
											return -1; // Put missing/Immediate at the end
										}
										try {
											// Extract first number from comma-separated values
											String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
											if (parts.length > 0) {
												return Double.parseDouble(parts[0].trim());
											}
										} catch (Exception e) {
											// If parsing fails
										}
										return -1; // Invalid values at the end
									"
								],
								'order' => 'desc'
							]
						];
						break;
					case 21: // Notice period Date ASC
					$sort[] = [
						'_script' => [
							'type' => 'number',
							'script' => [
								'lang' => 'painless',
								'source' => "
									if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
										return 999999999; // null/empty -> last
									}

									def raw = doc['np_id.keyword'].value;
									if (raw == null || raw.trim().length() == 0) { return 999999999; }
									def v = raw.trim();

									// Immediate should come first
									if (v.equals('Immediate')) { return -999999998; }

									// Handle multi-value
									int comma = v.indexOf(',');
									if (comma > -1) { v = v.substring(0, comma).trim(); }

									// Remove time
									int t = v.indexOf('T');
									if (t > -1) { v = v.substring(0, t); }
									v = v.replace('/', '-');

									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
										return d.toEpochDay();
									} catch (Exception e1) {
										try {
											def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
											return d.toEpochDay();
										} catch (Exception e2) {
											try {
												def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
												return d.toEpochDay();
											} catch (Exception e3) {
												return 999999999; // not a date -> last
											}
										}
									}
								"
							],
							'order' => 'asc'
						]
					];
					break;

					case 22: // Notice period Date DESC
					$sort[] = [
						'_script' => [
							'type' => 'number',
							'script' => [
								'lang' => 'painless',
								'source' => "
									if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
										return -999999999; // null/empty -> last
									}

									def raw = doc['np_id.keyword'].value;
									if (raw == null || raw.trim().length() == 0) { return -999999999; }
									def v = raw.trim();

									// Immediate should come after valid dates (but before null/non-date)
									if (v.equals('Immediate')) { return -888888888; }

									// Handle multi-value
									int comma = v.indexOf(',');
									if (comma > -1) { v = v.substring(0, comma).trim(); }

									// Remove time
									int t = v.indexOf('T');
									if (t > -1) { v = v.substring(0, t); }
									v = v.replace('/', '-');

									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
										return d.toEpochDay();
									} catch (Exception e1) {
										try {
											def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
											return d.toEpochDay();
										} catch (Exception e2) {
											try {
												def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
												return d.toEpochDay();
											} catch (Exception e3) {
												return -999999999; // not a date -> last
											}
										}
									}
								"
							],
							'order' => 'asc'
						]
					];
					break;
					case 9: // Immediate joiners first (original functionality)
						$sort[] = [
							'_script' => [
								'type' => 'number',
								'script' => [
									'lang' => 'painless',
									'source' => "params['_source']['immediate_join'] == 'Immediate' ? 0 : 1"
								],
								'order' => 'asc'
							]
						];
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


				$boolQuery = [
					'bool' => [
						'must' => [
							// First condition
							[
								'bool' => [
									'must' => array_merge([
										['term' => ['other_company' =>$company_id]],
										['term' => ['condidate_type' =>'bench_condidate']],
										['terms' => ['job_master_status_id' =>[5,6]]],
									],
									)
								]
							]
						],
						'filter' => [],
						'must_not' => [
							['term' => ['status' => 3]]
						]
					]
				];

				// Search box: "candidate name, job or offer ID"
				if ($searchTerm !== '') {
					$searchShould = [
						['match_phrase_prefix' => ['full_name' => $searchTerm]],
						['match_phrase_prefix' => ['title' => $searchTerm]],
					];
					if (is_numeric($searchTerm)) {
						$searchShould[] = ['term' => ['offer_id' => (int) $searchTerm]];
					}
					$boolQuery['bool']['must'][] = ['bool' => ['should' => $searchShould, 'minimum_should_match' => 1]];
				}

				// Location filter ("All Locations" dropdown)
				if ($location !== '' && strtolower($location) !== 'all') {
					$boolQuery['bool']['must'][] = ['term' => ['city_name' => $location]];
				}

				// Status tabs: All / Pending / Offer Released / Accepted / Joining Soon / Expired
				switch ($tab) {
					case 'pending':
						$boolQuery['bool']['must'][] = [
							'bool' => [
								'should' => [
									['bool' => ['must_not' => [['exists' => ['field' => 'of_offer_amount']]]]],
									['bool' => ['must_not' => [['exists' => ['field' => 'of_joined_date']]]]],
								],
								'minimum_should_match' => 1,
							],
						];
						break;
					case 'released':
						$boolQuery['bool']['must'][] = ['term' => ['of_offer_acceptance' => 1]];
						break;
					case 'accepted':
						$boolQuery['bool']['must'][] = ['term' => ['of_offer_acceptance' => 2]];
						break;
					case 'joining_soon':
						$boolQuery['bool']['must'][] = ['range' => ['of_joined_date' => ['gte' => 'now/d', 'lte' => 'now+7d/d']]];
						break;
					case 'expired':
						$boolQuery['bool']['must'][] = ['term' => ['of_offer_acceptance' => 4]];
						break;
					default:
						break; // 'all'
				}

				$params = [
					'index' => ELASTIC_CANDIDATES,
					'request_cache' => true,
					'body' => [
						'from' => $from,
						'size' => $size,
						'sort' => $sort,
						'query' => $boolQuery
					]
				];
				if (!$this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
					$total = 0;
					$data = [];
				}else{
					$response = $this->elasticService->getClient()->search($params);
					$total = $response['hits']['total']['value'] ?? 0;
					$data = array_map(function ($hit) {
						return $hit['_source'];
					}, $response['hits']['hits']);
				}
		
				$pagination = [
					'total' => $total,
					'current_page' => $page,
					'last_page' =>ceil($total / $size),
					'listType' => 'allcandidate',
				];

				$userPermissions = $this->userPermissions;
				$responseData =  [
					'html' => view('service_company.ajax_pages.bench-candidate-offer-ajax', compact('data','userPermissions','company_id','salaryMode','currencySymbole'))->render(),
					'total' => $total,
					'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
				];

				
			cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_BENCH_OFFER_LISTING_TIME));

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
		$this->allBenchApplication = BenchApplication::count();
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
		$custom_search =  $searchTerms; //json_decode($searchTerms, true);

		$searchQuery = $request->input('q');
		$valCheck = $request->input('sortingfilter');
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
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
			'sortingfilter' => $valCheck
		];

		$versionKey = 'allbenchplacedListing_version_user_'.$this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:allbenchplacedListing:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
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
			case 5: // Notice period ASC (ignore Immediate)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return 999999; // Put missing/Immediate at the end
								}
								try {
									// Extract first number from comma-separated values
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {
									// If parsing fails
								}
								return 999999; // Invalid values at the end
							"
						],
						'order' => 'asc'
					]
				];
				break;
			case 6: // Notice period DESC (ignore Immediate)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return -1; // Put missing/Immediate at the end
								}
								try {
									// Extract first number from comma-separated values
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {
									// If parsing fails
								}
								return -1; // Invalid values at the end
							"
						],
						'order' => 'desc'
					]
				];
				break;
			case 21: // Notice period Date ASC
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return 999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return 999999999; }
							def v = raw.trim();

							// Immediate should come first
							if (v.equals('Immediate')) { return -999999998; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return 999999999; // not a date -> last
									}
								}
							}
						"
					],
					'order' => 'asc'
				]
			];
			break;

			case 22: // Notice period Date DESC
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return -999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return -999999999; }
							def v = raw.trim();

							// Immediate should come after valid dates (but before null/non-date)
							if (v.equals('Immediate')) { return -888888888; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return -999999999; // not a date -> last
									}
								}
							}
						"
					],
					'order' => 'asc'
				]
			];
			break;
			case 9: // Immediate joiners first (original functionality)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "params['_source']['immediate_join'] == 'Immediate' ? 0 : 1"
						],
						'order' => 'asc'
					]
				];
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
		if (!empty($custom_search)) {
			$searchConds[] = [
				'bool' => [
					'should' => [
						['match_phrase_prefix' => ['full_name' => strtolower($custom_search)]],
						['wildcard' => ['email'    => '*' . strtolower($custom_search) . '*']],
						['wildcard' => ['phone'    => '*' . $custom_search . '*']],
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
						['term'  => ['other_company'       => $company_id]],
						['term'  => ['condidate_type'       => 'bench_condidate']],
						['term'  => ['job_master_status_id' => 6]],
						['terms' => ['status'               => [3]]],
					], $searchConds)]],
					// 2. Join tracker — own company, no third-party client (mirrors job-onboard cond 1)
					['bool' => ['must' => array_merge([
						['term' => ['company_id'           => $company_id]],
						['term' => ['job_master_status_id' => 6]],
						['term' => ['ClientComID'          => 0]],
					], $searchConds)]],
					// 3. Join tracker — own company, with third-party client (mirrors job-onboard cond 2)
					['bool' => ['must' => array_merge([
						['term'  => ['company_id'           => $company_id]],
						['term'  => ['job_master_status_id' => 6]],
						['range' => ['ClientComID'          => ['gt' => 0]]],
						['range' => ['jobId'                => ['gt' => 0]]],
						['bool'  => ['must_not' => [['terms' => ['job_master_status_id' => [8, 9]]]]]],
					], $searchConds)]],
					// 4. Join tracker — third-party company is this company (mirrors job-onboard cond 3)
					['bool' => ['must' => array_merge([
						['term'  => ['ClientComID'          => $company_id]],
						['term'  => ['job_master_status_id' => 6]],
						['range' => ['jobId'                => ['gt' => 0]]],
					], $searchConds)]],
				],
				'minimum_should_match' => 1,
				'filter'   => [],
				'must_not' => [],
			],
		];
		$params = [
			'index' => ELASTIC_CANDIDATES,
			'request_cache' => true,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => $sort,
				'query' => $boolQuery
			]
		];
		if (!$this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
			$total = 0;
			$data = [];
		}else{
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
			'last_page' =>ceil($total / $size),
			'listType' => 'allcandidate',
			'per_page' => $size, // lets pagination partial show the real page size instead of assuming 10
		];

		$userPermissions = $this->userPermissions;
		$responseData =  [
			'html' => view('service_company.ajax_pages.bench-place-candidate-ajax', compact('data','userPermissions','company_id','salaryMode','currencySymbole'))->render(),
			'total' => $total,
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		];

		cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_BENCH_PLACED_LISTING_TIME));

		return response()->json($responseData);



	}
	public function savePlacedAssignmentDetails(Request $request)
	{
		$company_id = auth()->user()->company_id;
		$candidate_id = (int)$request->candidate_id;
		$is_bench = (int)$request->is_bench;

		if (!$candidate_id) {
			return Reply::dataOnly(['status' => 'fail', 'message' => 'Invalid candidate.']);
		}

		// candidate_id from JS = wiseId = add_job_wise_condidates.id (ES document _id)
		$wiseRecord       = DB::table('add_job_wise_condidates')->where('id', $candidate_id)->first();
		$wiseId           = $wiseRecord ? $wiseRecord->id : null;
		$job_id           = $wiseRecord ? $wiseRecord->job_id : null;
		$jobApplicationId = $wiseRecord ? $wiseRecord->job_application_id : null;

		$assignmentFields = [
			'assignment_status'                   => $request->assignment_status ?: null,
			'assignment_expected_end_date'        => $request->expected_end_date ?: null,
			'assignment_employment_type'          => $request->employment_type ?: null,
			'assignment_client_location'          => $request->client_location ?: null,
			'assignment_exit_reason'              => $request->exit_reason ?: null,
			'assignment_project_outcome'          => $request->project_outcome ?: null,
			'assignment_remarks'                  => $request->remarks ?: null,
			'assignment_internal_notes'           => $request->internal_notes ?: null,
			'assignment_replacement_required'     => $request->replacement_required !== null ? (bool)(int)$request->replacement_required : null,
			'assignment_replacement_status'       => $request->replacement_status ?: null,
			'assignment_replacement_requested_on' => $request->replacement_requested_on ?: null,
			'assignment_replacement_reason'       => $request->replacement_reason ?: null,
		];

		

		// Sync to ELASTIC_CANDIDATES
		if ($wiseId && $this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
			try {
				$sdfsdf = $this->elasticService->getClient()->update([
					'index'             => ELASTIC_CANDIDATES,
					'id'                => $wiseId,
					'retry_on_conflict' => 5,
					'body'              => [
						'doc'           => $assignmentFields,
						'doc_as_upsert' => true,
					],
				]);
			} catch (\Exception $e) {
				\Log::error('ES assignment update failed for wiseId=' . $wiseId . ': ' . $e->getMessage());
			}
		}

		GeneralMethod::cacheClean('allbenchplacedListing_version_user_' . $this->user->id);

		return Reply::dataOnly(['status' => 'success', 'message' => 'Assignment details saved successfully.']);
	}

	public function savePlacedCommercialConfig(Request $request)
	{
		$company_id = auth()->user()->company_id;
		$candidate_id = (int)$request->candidate_id;

		if (!$candidate_id) {
			return Reply::dataOnly(['status' => 'fail', 'message' => 'Invalid candidate.']);
		}

		// candidate_id from JS = wiseId = add_job_wise_condidates.id (ES document _id)
		$wiseRecord       = DB::table('add_job_wise_condidates')->where('id', $candidate_id)->first();
		$wiseId           = $wiseRecord ? $wiseRecord->id : null;
		$job_id           = $wiseRecord ? $wiseRecord->job_id : null;
		$jobApplicationId = $wiseRecord ? $wiseRecord->job_application_id : null;

		$feeFixed = $request->fee_fixed ? (float)str_replace(',', '', $request->fee_fixed) : null;
		$feePct   = $request->fee_pct   ? (float)$request->fee_pct : null;

		$billingAmount = $request->billing_amount ? (float)str_replace(',', '', $request->billing_amount) : null;

		$commercialFields = [
			'commercial_service_type'             => $request->service_type ?: null,
			'commercial_model'                    => $request->commercial_model ?: null,
			'commercial_fee_method'               => $request->fee_method ?: null,
			'commercial_fee_base'                 => $request->fee_base ?: null,
			'commercial_fee_pct'                  => $feePct,
			'commercial_fee_fixed'                => $feeFixed,
			'commercial_billing_amount'           => $billingAmount,
			'commercial_billing_cycle'            => $request->billing_cycle ?: null,
			'commercial_margin_pct'               => $request->margin_pct ? (float)$request->margin_pct : null,
			'commercial_replacement_period'       => $request->replacement_period ?: null,
			'commercial_replacement_status'       => $request->replacement_status ?: null,
			'commercial_replacement_requested_on' => $request->replacement_requested_on ?: null,
			'commercial_replacement_reason'       => $request->replacement_reason ?: null,
			'commercial_internal_note'            => $request->internal_note ?: null,
			'commercial_free_service_type'        => $request->free_service_type ?: null,
			'commercial_free_reason'              => $request->free_reason ?: null,
			'commercial_free_effective_date'      => $request->free_effective_date ?: null,
		];

		
		// Sync to ELASTIC_CANDIDATES
		if ($wiseId && $this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
			try {
				$this->elasticService->getClient()->update([
					'index'             => ELASTIC_CANDIDATES,
					'id'                => $wiseId,
					'retry_on_conflict' => 5,
					'body'              => [
						'doc'           => $commercialFields,
						'doc_as_upsert' => true,
					],
				]);
			} catch (\Exception $e) {
				
				\Log::error('ES commercial update failed for wiseId=' . $wiseId . ': ' . $e->getMessage());
			}
		}

		GeneralMethod::cacheClean('allbenchplacedListing_version_user_' . $this->user->id);

		return Reply::dataOnly(['status' => 'success', 'message' => 'Commercial configuration saved successfully.']);
	}

	public function update_placed_end_date(Request $request)
	{
		$block = '';
		$company_id = auth()->user()->company_id;
		$bench_app_id = @$request->bench_app_id;
		$placeid = @$request->placeid;
		$bench_placed_end_date = @$request->bench_placed_end_date;

		if (!empty($bench_app_id) && !empty($bench_placed_end_date)) {

			DB::table('add_job_wise_condidates')->where('id', @$bench_app_id)->limit(1)->update(array('placed_end_date' => @$bench_placed_end_date));
			DB::table('bench_place')->where('id', @$placeid)->limit(1)->update(array('placed_end_date' => @$bench_placed_end_date));
			 $this->elasticService->getClient()->update([
				'index' => ELASTIC_BENCH_PLACED,
				'id'    => $placeid,
				'retry_on_conflict' => 5,
				'body'  => [
					'doc' => [ 
						'id' => @$placeid,
						'placed_end_date' => (@$bench_placed_end_date) ? Carbon::parse($bench_placed_end_date)->toIso8601String() : null
					],
					'doc_as_upsert' => true,
					'detect_noop' => false
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
		
				$this->countryArr 		=	GeneralMethod::getAllCountryList();		   
				$this->counter			=	$counter;
				$view 					=	view('service_company.bench-candidates.add_custom_detail', $this->data)->render();
				return Reply::dataOnly(['status' => 'success', 'data' => $view]);
		
	}
	public function bench_explore($id, $stage)
	{
		$this->pageTitle = __('Bench Requisition');
		$company_id = auth()->user()->company_id;
		$this->stage = $stage;
		$this->bench_id = $id;
		$this->recomCount = $this->benchjobAndRecomCount($id);
		$this->bench_detail = JobApplication::select('job_applications.id', 'job_applications.full_name', 'job_applications.current_designation', 'job_applications.skills', 'bench_master_status.name','bench_master_status.css_style','job_applications.status as statusCheck','countries.country_name','state_list.state as state_name','city_list.city as city_name','job_applications.total_exp','job_applications.rel_exp','job_applications.pay_rate_amount','job_applications.bill_rate_amount','job_applications.bill_rate_currency','job_applications.bill_rate_mode')
			->join('bench_master_status', 'bench_master_status.id', '=', 'job_applications.status')
			->join('countries', 'countries.id', '=', 'job_applications.country')
			->join('state_list', 'state_list.id', '=', 'job_applications.state')
			->join('city_list', 'city_list.city_id', '=', 'job_applications.city_id')
			->findOrFail($this->bench_id);
		return view('service_company.bench-candidates.bench_explore', $this->data);
	}
	public function bench_explore_listing_counter(Request $request)
	{
            
		$bench_id = $stage = $inputSearch = '';
		$company_id = auth()->user()->company_id;
		$bench_id = $request->bench_id;
		$stage = $request->stage;
		$inputSearch = $request->inputSearch;

		$bench_explore_listing_counter = GeneralMethod::bench_explore_listing_counter($bench_id);

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
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;

		// The toolbar search box actually sends 'cus_search' (see
		// fetchBenchExploreJobCandidateList()) — 'q'/'custom_search' were never
		// populated by the frontend, which is why free-text search was dead.
		$custom_search = trim((string) $request->input('cus_search'));
		$custom_status = request('statusfilter');
		$bxr_company  = json_decode((string) $request->input('bxr_company', '[]'), true) ?: [];
		$bxr_location = json_decode((string) $request->input('bxr_location', '[]'), true) ?: [];
		$bxr_min_sal  = $request->input('bxr_min_sal');
		$bxr_max_sal  = $request->input('bxr_max_sal');
		$bxr_activity = $request->input('bxr_activity');

		$cachePayload = [
			'page' => $page,
			'size' => $size,
			'company' => $company_id,
			'stage' => $request->input('stage'),
			'cus_search' => $custom_search,
			'statusfilter' =>$request->input('statusfilter'),
			'sortingfilter' => $request->input('sortingfilter'),
			'bxr_company' => $bxr_company,
			'bxr_location' => $bxr_location,
			'bxr_min_sal' => $bxr_min_sal,
			'bxr_max_sal' => $bxr_max_sal,
			'bxr_activity' => $bxr_activity,
			'user_id' => $this->user->id,
			'bench_id' =>$request->input('bench_id')
		];

		$versionKey = 'allbenchexplore_version_user_'.$this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:allbenchexplore:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
		/* ----------------------------
		⚡ Check DragonflyDB first
		----------------------------- */
		if (cache()->has($cacheKeySet)) {
			// return response()->json(cache()->get($cacheKeySet));
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
				break;
			case 4:
				$sort[] = ['max_salary' => ['order' => 'desc']];
				break;
			case 5:
				$sort[] = ['client_name' => ['order' => 'asc']];
				break;
			case 6:
				$sort[] = ['client_name' => ['order' => 'desc']];
				break;
			default:
				$sort[] = ['id' => ['order' => 'desc']];
		}



		// 1. STATUS FILTERING
		if ($custom_status !== "" && $custom_status !== 'all') {
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
				if (!is_array($status)) {
					$status = explode(',', $status);
				}

				// Optional: handle special logic (example: 7 => [7,9])
				if (in_array(7, $status)) {
					$status = array_unique(array_merge($status, [9]));
				}

				$filters[] = [
					'terms' => ['job_master_status_id' => array_map('intval', $status)]
				];
			}

			// Job status (active/hold/inactive) — $statusArr was computed above but
			// never actually applied to the query; wiring it to the real indexed
			// field (jobs.status, denormalized onto each doc as `job_status`) is
			// what makes the Status filter actually do something.
			if (!empty($statusArr)) {
				$filters[] = ['terms' => ['job_status' => $statusArr]];
			}

			// Free-text search — company name, job title, or the recruiter who
			// submitted the candidate (matches the toolbar's placeholder text).
			if ($custom_search !== '') {
				$must[] = [
					'multi_match' => [
						'query'     => $custom_search,
						'fields'    => ['client_name', 'title', 'posted_by'],
						'fuzziness' => 'AUTO',
					],
				];
			}

			// Company / Client Name (Tagify — array of {value: "..."} tags)
			if (!empty($bxr_company)) {
				$companyShould = [];
				foreach ($bxr_company as $tag) {
					$val = is_array($tag) ? ($tag['value'] ?? '') : $tag;
					if ($val !== '') {
						$companyShould[] = ['match_phrase' => ['client_name' => $val]];
					}
				}
				if (!empty($companyShould)) {
					$filters[] = ['bool' => ['should' => $companyShould, 'minimum_should_match' => 1]];
				}
			}

			// Job Location (Tagify)
			if (!empty($bxr_location)) {
				$locationShould = [];
				foreach ($bxr_location as $tag) {
					$val = is_array($tag) ? ($tag['value'] ?? '') : $tag;
					if ($val !== '') {
						$locationShould[] = ['match_phrase' => ['city_name' => $val]];
						$locationShould[] = ['match_phrase' => ['location' => $val]];
					}
				}
				if (!empty($locationShould)) {
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

			$params = [
				'index' => ELASTIC_CANDIDATES,
				'body'  => [
					'from' => $from,
					'size' => $size,
					'sort' => $sort,
					'query' => [
						'bool' => [
							'must'   => $must,
							'filter' => $filters
						]
					]
				]
			];
			if (!$this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
			$total = 0;
			$data = [];
			}else{
				$response = $this->elasticService->getClient()->search($params);
				$total = $response['hits']['total']['value'] ?? 0;
				$data = array_map(function ($hit) {
					return $hit['_source'];
				}, $response['hits']['hits']);
			}
			//    dd($data);
			$pagination = [
				'total' => $total,
				'current_page' => $page,
				'last_page' =>ceil($total / $size),
				'listType' => 'allcandidate',
			];

			$userPermissions = $this->userPermissions;
			$responseData =  [
				'html' => view('service_company.ajax_pages.bench-explore-list-ajax', compact('data','userPermissions','company_id','salaryMode','currencySymbole','job_status','feedback_status','bench_id'))->render(),
				'total' => $total,
				'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
			];

			// cache()->put($cacheKeySet, $responseData, now()->addMinutes(3));

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
			->join('master_status', function($join){
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
			})->leftJoin('jobs', function($join){
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
        
        	if (\request('statusfilter') != "") {
		 
		    $custom_status = request('statusfilter');
		       
		    if($custom_status=='active'){
		        $benchAppOnJob = $benchAppOnJob->where('jobs.status',$custom_status);
		    }elseif($custom_status=='hold'){
		        $benchAppOnJob = $benchAppOnJob->where('jobs.status',$custom_status);
		    }elseif($custom_status=='inactive'){
		        $benchAppOnJob = $benchAppOnJob->where('jobs.status',$custom_status);
		    }elseif($custom_status=='removed'){
		        $benchAppOnJob = $benchAppOnJob->where('recommendation_relation.final_status', 2);
		    }
			    
		}
		$searchTerms = request('cus_search');
     	$custom_search = json_decode($searchTerms, true);
		if (\request('cus_search') != "") {
			    
			    $benchAppOnJob = $benchAppOnJob->where(function($query) use ($custom_search,$company_id){
			        $query->whereIn('jobs.title', $custom_search);
			        $query->orWhereIn('clients.name',$custom_search);
			        $query->orWhereIn('job_metronics_skills.skill_name',$custom_search);
			    });
			}
		
		$benchAppOnJob = $benchAppOnJob->where('rec_candidate_assign.source_from_id', '=', $bench_id);
		if ($stage > 0) {
			$benchAppOnJob = $benchAppOnJob->where('add_job_wise_condidates.master_status_id', '=', $stage);
		}
		
		$benchAppOnJob = $benchAppOnJob->groupBy('jobs.id');
		
		if (\request('sortingfilter') != "") {
		    $valCheck = request('sortingfilter');
		    if($valCheck==1){
		        $benchAppOnJob = $benchAppOnJob->orderBy('jobs.created_at', 'DESC');
		    }elseif($valCheck==2){
		        $benchAppOnJob = $benchAppOnJob->orderBy('jobs.created_at', 'ASC');
		    }elseif($valCheck==3){
		        $benchAppOnJob = $benchAppOnJob->orderBy('jobs.max_salary', 'ASC');
		    }elseif($valCheck==4){
		        $benchAppOnJob = $benchAppOnJob->orderBy('jobs.max_salary', 'DESC');
		    }
			   
		}else{
		    	$benchAppOnJob = $benchAppOnJob->orderBy('jobs.id', 'DESC');
		}
		
         //    dd("dj");
		return DataTables::of($benchAppOnJob)

			->editColumn('chkbox', function ($row) {
				return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input chk_list_item " name="chk_list_item[]" type="checkbox" value="' . $row->id . '" />
                  </div>';
			})
			->editColumn('Requisition', function ($row) {

				$clients_name = "";
				$bgTextDecore = $strVal = '';
				$str = trim(@$row->title);
				$str = $strVal = strtolower($str[0]);
				$str = ord($str);
				if ($str >= 97 && $str <= 101) {
					$bgTextDecore = 'fs-3 bg-light-danger text-danger';
				} else if ($str >= 102 && $str <= 106) {
					$bgTextDecore = 'fs-3 bg-light-warning text-warning';
				} else if ($str >= 107 && $str <= 111) {
					$bgTextDecore = 'fs-3 bg-light-primary text-primary';
				} else if ($str >= 112 && $str <= 116) {
					$bgTextDecore = 'fs-3 bg-light-success text-success';
				} else if ($str >= 117 && $str <= 121) {
					$bgTextDecore = 'fs-3 bg-dark text-secondary';
				} else {
					$bgTextDecore = 'fs-3 bg-light-secondary text-dark';
				}

				$profile_detail_class = 'show-detail';
				$clients_name = ucwords(substr(@$row->clients_name, 0, 15));

				$recommendation_id = '';
				$recommendation_id = $row->recommendation_id;

				$logo_path = '';
				//$logo_path				=	url('img/client-default.png'); 	
				if (!empty($row->logo_logical_path)) {
					$logo_path = @$row->logo_logical_path;
				}

				if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
					$recommendation_text = '&nbsp;<i style="color: #FF0000; font-weight:bold" class="fa fa-users" aria-hidden="true"></i>';
					$profile_detail_class = 'client-show-detail';
					$clients_name = ucwords(substr(@$row->company_name, 0, 15));

					if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
						$logo_path = @$row->logo;
					}
				}


				if (@$row->self_company == 1) {
					if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
						$logo_path = @$row->logo;
					}
				}
				$logo_path = @$row->logo;
				$clients_name = ucwords(substr(@$row->company_name, 0, 15));

				$icon = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="' . $row->jobId . '" class="' . $profile_detail_class . '">
                      <div class="symbol-label ' . $bgTextDecore . ' border-radius-5">' . ucwords($strVal) . '</div>
                    </a>
                  </div>';

				if (!empty($logo_path)) {
					$icon = '<div data-row-id="' . $row->jobId . '"  class="symbol symbol-circle symbol-40px overflow-hidden me-2  ' . $profile_detail_class . ' border-radius-5">
                                        <a href="javascript:void(0);">
                                            <div class="symbol-label border-radius-5">
                                            	<img src="' . @$logo_path . '" alt="Company Logo" class="border-radius-5 w-100">
                                            </div>
                                        </a>
                                    </div>';
				}
				
				 $skilltag = '';
                                    $skillArr = DB::table('job_metronics_skills')->select('*')->where('job_id', $row->jobId)->get();
                                    if (!empty($skillArr)) {
                                            foreach ($skillArr as $sval) {
                                               $skilltag .= '<span class="badge badge-light my-1">' . ucfirst($sval->skill_name) . '</span>  ';
                                            }
                                        }
                                        

				return $icon . '<div class="d-flex flex-column">
<a href="javascript:void(0);"  data-row-id="' . $row->jobId . '"   data-row-job_applications_id="' . $row->job_applications_id . '" class="text-dark fw-bolder text-hover-primary fs-6 ' . $profile_detail_class . '"  data-toggle="tooltip1" title="' . ucwords(@$row->title) . '" >' . ucwords(substr(@$row->title, 0, 15)) . '</a> 
<a href="javascript:void(0);" data-row-id="' . $row->jobId . '"   data-row-job_applications_id="' . $row->job_applications_id . '"   class="fw-bold text-muted d-block fs-7 text-hover-primary ' . $profile_detail_class . '">' . $clients_name . '</a>
                  </div>
                    <div style="position: absolute; bottom: 0;">
                                    <span class="badge badge-light-success fs-base py-0">Skills:</span>
                                 
                                  '.$skilltag.'
                                         
                                   
                                    </div> 
                  ';

			})
			->editColumn('Exp', function ($row) {
				return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">' . @$row->max_exp . '+</span>
                    <span class="text-muted fw-bold text-muted d-block fs-7">' . @$row->min_exp . '+</span>
                  </div>';
			})
			->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {
				$min_sal_mode = $max_sal_mode = $min_salary_currency = $max_salary_currency = $max_salary = $min_salary = "";
				$min_sal_mode = strtoupper(substr($salaryMode[$row->min_sal_mode], 0, 1));
				$max_sal_mode = strtoupper(substr($salaryMode[$row->max_sal_mode], 0, 1));
				$min_salary_currency = $currencySymbole[@$row->min_salary_currency];
				$max_salary_currency = $currencySymbole[@$row->max_salary_currency];
				$max_salary = GeneralMethod::thousandsCurrencyFormat($row->max_salary);
				$min_salary = GeneralMethod::thousandsCurrencyFormat($row->min_salary);
				return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">' . $min_salary_currency . $max_salary . '/' . $min_sal_mode . '</span>                    
                    <span class="fw-bold d-block fs-7">' . $min_salary_currency . $min_salary . '/' . $max_sal_mode . '</span>
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
					if($row->employment_type==1){
						 $employment_type = 'Contract';
					}
					// dd($jobCitySelected,$row->work_type);

				if ($row->work_type == 'yes') {
					return ' <div class="d-flex flex-column">
						<span class="text-dark fw-bolder fs-6">Remote</span>
						<div class="d-flex align-items-center">
						  <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' . ucwords(@$employment_type) . '" >' . ucwords(substr(@$employment_type, 0, 10)) . '</span>
						  </div>
						</div>
					  </div>';
				} elseif ($row->work_type == 'no') {

					if ($jobCitySelected == 1 || $jobCitySelected == 0) {
						$client_loc = DB::table('client_city_jobs')
							->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
							->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
							->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
							->select('city_list.city as city_name','countries.country_name')
							->where('client_city_jobs.job_id', $jobid)
							->first();
							$city = substr(@$client_loc->city_name, 0, 21);
							$country_name = substr(@$client_loc->country_name, 0, 6);

						return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">' . ucwords(substr(@$city, 0, 10)) . '</span>
								</div>
								
							  <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' . ucwords(@$employment_type) . '" >' . ucwords(substr(@$employment_type, 0, 10)) . '</span>
							  </div>
							</div>
						  </div>';
					} else {
						return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">Multiple</span>
								</div>
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' . ucwords(@$employment_type) . '" >' . ucwords(substr(@$employment_type, 0, 10)) . '</span>
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
							->select('city_list.city as city_name','countries.country_name')
							->where('client_city_jobs.job_id', $jobid)
							->first();
							$city = substr(@$client_loc->city_name, 0, 21);
							$country_name = substr(@$client_loc->country_name, 0, 6);
							
						return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">' . ucwords(substr(@$city, 0, 10)) . '</span>
								</div>
								
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' . ucwords(@$employment_type) . '" >' . ucwords(substr(@$employment_type, 0, 10)) . '</span>
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
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="' . ucwords(@$employment_type) . '" >' . ucwords(substr(@$employment_type, 0, 10)) . '</span>

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
				$ulink = '<a href="javascript.void(0);" data-row-id="' . @$row->id . '" class="d-flex flex-column ' . @$class . ' status_stage text-hover-success" data-toggle="modal" data-target="#status_stage">' . $status . '</a>';
				return '<div class="d-flex flex-column">
                       ' . $ulink . '
                      <span class="fw-bold text-muted d-block fs-7">R-' . @$row->jobId . '</span>
                    </div>';
			})
			
			->editColumn('AddedDate', function ($row) {

				$total_positions = '';
				$total_positions = number_format($row->total_positions);

				$created_at = '';
				$created_at = $row->created_at;
				$newDate = GeneralMethod::ymdTodmyNotTime($created_at); //date("d M, Y", strtotime($created_at));
				return '<div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">' . $newDate . '</span>
                        <span class="text-muted fw-bold d-block fs-7">' . $total_positions . ' Opening</span>
                    </div>';
			})
			->editColumn('ContactPerson', function ($row) use ($feedback_status) {

				$schedule_date = $className = '';
				$intRowCount = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->job_applications_id)->where('interview_job_id', $row->jobId)->orderBy('interview_schedules.id', 'DESC')->count();
				$intRow = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->job_applications_id)->where('interview_job_id', $row->jobId)->orderBy('interview_schedules.id', 'DESC')->first();
				$html = '';
				if ($intRowCount > 0) {
					$schedule_date = @$intRow->schedule_date;
					$schedule_date = date("d M, Y", strtotime($schedule_date));
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
					if (!empty(@$pval->image) && strlen(@$pval->image) > 10)
						$image_path = @$pval->image;
					$html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' . @$pval->name . '"><img alt="Pic" src="' . @$image_path . '"></div>';
				}
				$html .= '<div data-row-id="' . $row->jobId . '"   class="symbol symbol-30 symbol-circle symbol-light hm-listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list.."><span class="symbol-label font-weight-bold">' . $partnersCount . '+</span></div>';
				$html .= '</div></div>
				<span class="text-muted fw-bold d-block fs-7' . @$className . ' px-1">' . @$feedback_status_name . '</span>';
				return $html;

			})
			->editColumn('Interview', function ($row) use ($feedback_status) {
				$schedule_date = $className = $schedule_time =  '';
				$intRowCount = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->job_applications_id)->where('interview_job_id', $row->jobId)->orderBy('interview_schedules.id', 'DESC')->count();
				$intRow = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->job_applications_id)->where('interview_job_id', $row->jobId)->orderBy('interview_schedules.id', 'DESC')->first();
				$html = '';
				if ($intRowCount > 0) {
					$schedule_date = @$intRow->schedule_date;
					$schedule_date = date("d M, Y", strtotime($schedule_date));
					$schedule_time = Carbon::parse($schedule_date)->format('h:i A') ; 
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
											<span class="text-dark fw-bolder fs-6">' . @$schedule_date . '</span>
											<span class="text-muted fw-bold d-block fs-7' . @$schedule_time . ' px-1">' . @$schedule_time . '</span>
										</div>';

			})
			->editColumn('Stage', function ($row) {

				$name = '';
				if ($row->master_status_id == 8) {
					$name = '';
				}
				if (!empty(@$row->child_status_id) && @$row->child_status_id > 0) {
					$childStatus = '';
					$childStatus = DB::table('candidate_status')->select('*')->where('id', $row->child_status_id)->first();
					if (!empty($childStatus)) {
						$name = @$childStatus->name;
					}
				}
				$stage_default_css = 'stage_sub_stage';
				$html = '';
				if ($row->master_status_id == 8) {
					$html = '<div class="d-flex flex-column">
								<a href="javascript.void(0);" data-row-id="' . @$row->id . '" class="d-flex flex-column client_stage_sub_stage" data-toggle="modal" data-target="#client_stage_sub_stage">
								<span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1">' . ucwords(substr($row->name, 0, 10)) . '</span>
								<span class="text-muted fw-bold d-block fs-7">' . @$name . '</span>
								</a>
								</div>';
				} else {
					$html = '<div class="d-flex flex-column">
								<span href="javascript.void(0);" data-row-id="' . @$row->id . '" class="d-flex flex-column ">
								<span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1">' . ucwords(substr($row->name, 0, 10)) . '</span>
								<span class="text-muted fw-bold d-block fs-7">' . @$name . '</span>
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
		$this->bench_id =$request->id;
		$this->bench_detail = JobApplication::select('job_applications.id', 'job_applications.full_name', 'job_applications.current_designation', 'job_applications.skills', 'bench_master_status.name','job_applications.status as statusCheck','countries.country_name','state_list.state as state_name','city_list.city as city_name','job_applications.total_exp','job_applications.rel_exp','job_applications.pay_rate_amount','job_applications.bill_rate_amount','job_applications.bill_rate_currency','job_applications.bill_rate_mode','job_applications.ectc_yearly')
			->join('bench_master_status', 'bench_master_status.id', '=', 'job_applications.status')
			->join('countries', 'countries.id', '=', 'job_applications.country')
			->join('state_list', 'state_list.id', '=', 'job_applications.state')
			->join('city_list', 'city_list.city_id', '=', 'job_applications.city_id')
			->findOrFail($this->bench_id);
        $this->recomCount = $this->benchjobAndRecomCount($request->id);
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
		$cities = DB::table('city_list')->pluck('city')->toArray();
        $this->cities =  $cities;


		$this->segment = ($request->segment(4)) ? $request->segment(4) : '';
		$this->pageTitle = __('Recommended Requisition');
		return view('service_company.bench-candidates.recommend_req', $this->data);
	}
	public function recommend_internal_req(Request $request)
	{
		// dd($request->id);
		$this->bench_id =$request->id;
		$this->bench_detail = JobApplication::select('job_applications.id', 'job_applications.full_name', 'job_applications.current_designation', 'job_applications.skills', 'bench_master_status.name','job_applications.status as statusCheck','countries.country_name','state_list.state as state_name','city_list.city as city_name','job_applications.total_exp','job_applications.rel_exp','job_applications.pay_rate_amount','job_applications.bill_rate_amount','job_applications.bill_rate_currency','job_applications.bill_rate_mode','job_applications.ectc_yearly')
			->join('bench_master_status', 'bench_master_status.id', '=', 'job_applications.status')
			->join('countries', 'countries.id', '=', 'job_applications.country')
			->join('state_list', 'state_list.id', '=', 'job_applications.state')
			->join('city_list', 'city_list.city_id', '=', 'job_applications.city_id')
			->findOrFail($this->bench_id);
        $this->recomCount = $this->benchjobAndRecomCount($request->id);
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
		$cities = DB::table('city_list')->pluck('city')->toArray();
        $this->cities =  $cities;


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
		$this->agencyAndRelationshipDetails = GeneralMethod::agencyAndRelationshipDetails($id,$company_id);

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
		if($currentSlug=='find-talents' && $this->benchApplication->company_id !=$company_id){
			$this->benchApplication->phone = 'xxx-xxx-xxxx';
			$this->benchApplication->email = 'xxx-xxx-xxxx';
			$this->benchApplication->secondary_mobile = 'xxx-xxx-xxxx';
			$this->benchApplication->email = 'xxx-xxx-xxxx';
			$this->relationship_manager_show = false;
		}
		

		$this->job_additional_detail = $this->bench_assign = "";
		$this->job_additional_detail = DB::table('bench_additional_detail')->select('*')->where('bench_additional_detail.bench_id', '=', @$this->benchApplication->id)->orderBy('bench_additional_detail.id', 'ASC')->get();

		$this->bench_assign = DB::table('bench_assign')->select('users.id', 'users.name')->leftjoin('users', 'users.id', 'bench_assign.user_id')->where('bench_assign.bench_applications_id', '=', @$this->benchApplication->id)->orderBy('bench_assign.id', 'ASC')->get();

		$this->bench_assign_count = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile','users_profile.designation','users.working_status')->join('users', 'users.id', '=', 'bench_assign.user_id')->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')->where('bench_assign.bench_applications_id', '=', @$this->benchApplication->id)->count();

		$this->bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name','users.calling_code', 'users.email', 'users.mobile','users_profile.designation')->join('users', 'users.id', '=', 'bench_assign.user_id')->join('users_profile', 'users_profile.user_id', '=', 'users.id')->where('bench_assign.bench_applications_id', '=', @$this->benchApplication->id)->distinct()->get();


		$view_count = array(
			'user_id' => auth()->user()->id,
			'candidate_id' => $id,
			'type' => 1,
			'status' => 0,
			'created_at' => date('Y-m-d H:i:'),
			'updated_at' => date('Y-m-d H:i:')
		);
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
		$currentRowJob = JobApplication::find($request->condidate_id);
		$condidate_id  = (int) $request->condidate_id;
		$currentJobSkill = explode(",", $currentRowJob->skills);

		$searchTerms = request('cus_search');
    	$custom_search = json_decode($searchTerms, true);
		// dd($custom_search);

		$location = request('kt_tagify_location');
    	$custom_location = json_decode($location, true);
		$current_date = date('Y-m-d');
		$current_timestamp = strtotime($current_date);
		$current_previous_30_days = date('Y-m-d',strtotime('-30 days', $current_timestamp));
		$current_previous_60_days = date('Y-m-d',strtotime('-60 days', $current_timestamp));
		$current_previous_90_days = date('Y-m-d',strtotime('-90 days', $current_timestamp));
		$current_previous_180_days = date('Y-m-d',strtotime('-180 days', $current_timestamp));
		$current_previous_1_year = date('Y-m-d',strtotime('-365 days', $current_timestamp));
		$app_int_activity = request('app_int_activity');
		$sal_mode = request('sal_mode');
		$min_salary_currency = request('min_salary_currency');
		$app_int_outsource = request('app_int_outsource');

		$min_exp = (request('min_exp')) ? request('min_exp') : 0;
		$max_exp = request('max_exp');

		$min_salary = request('min_sal');
	    $max_salary = request('max_sal');
		$currency = GeneralMethod::currency();  // Currency code
		
		$ecurrency  = $currency[$min_salary_currency];
		$econvertedAmount = (int) $max_salary;
		if($min_salary_currency > 0){
			$econvertedAmount = GeneralMethod::convertToINR($econvertedAmount, $ecurrency);
		}

		$ectcdivided = GeneralMethod::salaryDividedByMode($sal_mode, $econvertedAmount);
		
		$currency  = $currency[$min_salary_currency];
		$convertedAmount = (int) $min_salary;
		if($min_salary_currency > 0){
			$convertedAmount = GeneralMethod::convertToINR($convertedAmount, $currency);
		}

		$ctcdivided = GeneralMethod::salaryDividedByMode($sal_mode, $convertedAmount);
		if($sal_mode==2){
			$max_salary = @$ectcdivided->hourly;
			$min_salary = @$ctcdivided->hourly;
		}elseif($sal_mode==1){
			$max_salary = @$ectcdivided->monthly;
			$min_salary = @$ctcdivided->monthly;
		}elseif($sal_mode==0){
			$max_salary = @$ectcdivided->yearly;
			$min_salary = @$ctcdivided->yearly;
		}
				
		// dd($max_salary ,$min_salary,$ectcdivided,$ctcdivided);
		
		$location= array();
		if (\request('kt_tagify_location') != "") {
			foreach ($custom_location as $sval) {
				$loc_datas = DB::table('countries')
				->select('city_list.city_id')
				->where('countries.country_name', 'LIKE', "%{$sval['value']}%")
				->orWhere('state_list.state', 'LIKE', "%{$sval['value']}%")
				->orWhere('city_list.city', 'LIKE', "%{$sval['value']}%")
				->join('state_list','state_list.country_id','=','countries.id')
				->join('city_list','city_list.state_id','=','state_list.id')
				->get();

			    // $loc_data = DB::table('city_list')->where('city_list.city_id', '=', $sval['value'])->first();
				if($loc_datas){
					foreach($loc_datas as $loc_data)
					array_push($location,$loc_data->city_id);
				}
			}
		}
		// dd($location);
		$set_skill = array();
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

		if (\request('cus_search') != "") {
			$skillsAddElastic = array_column($custom_search, 'value');
			GeneralMethod::skillsAddElastic($skillsAddElastic);
		}
		$requiredSkills = [];
		
		if (\request('cus_search') != "") {

			$checkedSkills = array_filter($custom_search, function($skill) {
				return $skill['checked'] == true;
			});
		
			$uncheckedSkills = array_filter($custom_search, function($skill) {
				return $skill['checked'] == false;
			});

			if (!empty($checkedSkills)) {
				   $requiredSkills = array_column($checkedSkills, 'value');
				} else {
					$requiredSkills = array_column($uncheckedSkills, 'value');
				}
				
			}

		$searchQuery = $request->input('q');
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
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
		$cacheKeySet = 'ats:allbenchrecommendreq:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
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
		switch ($valCheck) {
			case 1:
				$sort[] = ['id' => ['order' => 'desc']];
				break;
			case 2:
				$sort[] = ['id' => ['order' => 'asc']];
				break;
			case 3:
				$sort[] = ['max_salary' => ['order' => 'asc']];
				break;
			case 4:
				$sort[] = ['max_salary' => ['order' => 'desc']];
				break;
			case 5:
				$sort[] = ['client_name' => ['order' => 'asc']];
				break;
			case 6:
				$sort[] = ['client_name' => ['order' => 'desc']];
				break;
			case 10:
				// "Best Match" — rank jobs by how many of the candidate's own
				// skills ($set_skill, already computed above) appear in each
				// job's nested job_metronics_skills. Purely a ranking signal,
				// not a filter — jobs with zero matches still appear (0%),
				// they just sort last. Mirrors the candidate-vs-job scoring
				// AppTracker's similar-candidates page already does, inverted
				// to score jobs against a candidate instead.
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								int matchCount = 0;
								if (params._source.containsKey('job_metronics_skills')) {
									for (skillDoc in params._source.job_metronics_skills) {
										if (!skillDoc.containsKey('skill_name') || skillDoc.skill_name == null) { continue; }
										String jobSkill = skillDoc.skill_name.toLowerCase();
										for (candSkill in params.skills) {
											if (jobSkill.contains(candSkill) || candSkill.contains(jobSkill)) { matchCount += 1; break; }
										}
									}
								}
								return matchCount;
							",
							'params' => ['skills' => array_values(array_filter(array_map('strtolower', $set_skill)))],
						],
						'order' => 'desc',
					],
				];
				$sort[] = ['id' => ['order' => 'desc']];
				break;
			default:
				$sort[] = ['id' => ['order' => 'desc']];
		}

		$filter[] = ['term' => ['status' => 'active']];
		// 2. HIRING TYPE
		if ($custom_hiringtype != "" && $custom_hiringtype != "All") {
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
		if (!empty($workMode)) {
			$workModeArr = array_values(array_filter(array_map('trim', explode(',', $workMode))));
			if (!empty($workModeArr)) {
				$filter[] = ['terms' => ['work_type' => $workModeArr]];
			}
		}

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

		if (!empty($requiredSkills)) {
	
			$should = [];
		
			foreach ($requiredSkills as $skill) {

				$cleanSkill = strtolower(preg_replace('/\s+/', ' ', trim($skill)));
		
				if ($cleanSkill !== '') {
					$should[] = [
						'match' => [
							'job_metronics_skills.skill_name' => [
								'query' => $cleanSkill,
								'operator' => 'and'   // ensures all words must match
							]
						]
					];
				}
			}

			if (!empty($should)) {
				$filter[] = [
					'nested' => [
						'path' => 'job_metronics_skills',
						'query' => [
							'bool' => [
								'should' => $should,
								'minimum_should_match' => 1
							]
						]
					]
				];
			}
		}

		if (!empty($location)) {
			$must[] = [
				'nested' => [
					'path' => 'client_city_jobs',
					'query' => [
						'terms' => [
							'client_city_jobs.city_id' => (array) $location
						]
					]
				]
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
						'gte' => (float) $min_salary
					],
				],
			];
		}
		if($max_salary >0 && $salary_field) {
			// dd($min_salary,$max_salary);
			$must[] = [
				'range' => [
					$salary_field => [
						'lte' => (float) $max_salary
					],
				],
			];
		}

		// FINAL ELASTIC PARAMS
		$params = [
			'index' => ELASTIC_JOBS,
			'request_cache' => true,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => $sort,
				'query' => [
					'bool' => [
						'must' => $must,
						'filter' => $filter,
					],
				],
			],
		];

		// dd($location);

		if (!$this->elasticService->indexExists(ELASTIC_JOBS)) {
			$total = 0;
			$data = [];
		}else{
			$response = $this->elasticService->getClient()->search($params);
			$total = $response['hits']['total']['value'] ?? 0;
			$data = array_map(function ($hit) {
				return $hit['_source'];
			}, $response['hits']['hits']);
		}

		$pagination = [
			'total' => $total,
			'current_page' => $page,
			'last_page' =>ceil($total / $size),
			'listType' => 'allcandidate',
		];

		$userPermissions = $this->userPermissions;
		$responseData =  [
			'html' => view('service_company.ajax_pages.bench-recommend-req-ajax', compact('data','userPermissions','company_id','salaryMode','currencySymbole','sal_mode','min_salary_currency','job_status','currentRowJob','condidate_id'))->render(),
			'total' => $total,
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		];

		cache()->put($cacheKeySet, $responseData, now()->addMinutes(3));

	     return response()->json($responseData);


	}
	public function all_internal_data_ajax(Request $request)
	{
		// dd($request->condidate_id); skills city_id city pay_rate_amount
		// dd("l");
		$currentRowJob = JobApplication::find($request->condidate_id);
		$condidate_id  = (int) $request->condidate_id;
		$currentJobSkill = explode(",", $currentRowJob->skills);

		$searchTerms = request('cus_search');
    	$custom_search = json_decode($searchTerms, true);
		// dd($custom_search);

		$location = request('kt_tagify_location');
    	$custom_location = json_decode($location, true);
		$current_date = date('Y-m-d');
		$current_timestamp = strtotime($current_date);
		$current_previous_30_days = date('Y-m-d',strtotime('-30 days', $current_timestamp));
		$current_previous_60_days = date('Y-m-d',strtotime('-60 days', $current_timestamp));
		$current_previous_90_days = date('Y-m-d',strtotime('-90 days', $current_timestamp));
		$current_previous_180_days = date('Y-m-d',strtotime('-180 days', $current_timestamp));
		$current_previous_1_year = date('Y-m-d',strtotime('-365 days', $current_timestamp));
		$app_int_activity = request('app_int_activity');
		$sal_mode = request('sal_mode');
		$min_salary_currency = request('min_salary_currency');
		$app_int_outsource = request('app_int_outsource');

		$min_exp = (request('min_exp')) ? request('min_exp') : 0;
		$max_exp = request('max_exp');

		$min_salary = request('min_sal');
	    $max_salary = request('max_sal');
		$currency = GeneralMethod::currency();  // Currency code
		
		$ecurrency  = $currency[$min_salary_currency];
		$econvertedAmount = (int) $max_salary;
		if($min_salary_currency > 0){
			$econvertedAmount = GeneralMethod::convertToINR($econvertedAmount, $ecurrency);
		}

		$ectcdivided = GeneralMethod::salaryDividedByMode($sal_mode, $econvertedAmount);
		
	

		$currency  = $currency[$min_salary_currency];
		$convertedAmount = (int) $min_salary;
		if($min_salary_currency > 0){
			$convertedAmount = GeneralMethod::convertToINR($convertedAmount, $currency);
		}

		$ctcdivided = GeneralMethod::salaryDividedByMode($sal_mode, $convertedAmount);
		if($sal_mode==2){
			$max_salary = @$ectcdivided->hourly;
			$min_salary = @$ctcdivided->hourly;
		}elseif($sal_mode==1){
			$max_salary = @$ectcdivided->monthly;
			$min_salary = @$ctcdivided->monthly;
		}elseif($sal_mode==0){
			$max_salary = @$ectcdivided->yearly;
			$min_salary = @$ctcdivided->yearly;
		}
				
		// dd($max_salary ,$min_salary,$ectcdivided,$ctcdivided);
		
		$location= array();
		if (\request('kt_tagify_location') != "") {
			foreach ($custom_location as $sval) {
				$loc_datas = DB::table('countries')
				->select('city_list.city_id')
				->where('countries.country_name', 'LIKE', "%{$sval['value']}%")
				->orWhere('state_list.state', 'LIKE', "%{$sval['value']}%")
				->orWhere('city_list.city', 'LIKE', "%{$sval['value']}%")
				->join('state_list','state_list.country_id','=','countries.id')
				->join('city_list','city_list.state_id','=','state_list.id')
				->get();

			    // $loc_data = DB::table('city_list')->where('city_list.city_id', '=', $sval['value'])->first();
				if($loc_datas){
					foreach($loc_datas as $loc_data)
					array_push($location,$loc_data->city_id);
				}
			}
		}


		// dd($location);


		$set_skill = array();
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

		if (\request('cus_search') != "") {
			$skillsAddElastic = array_column($custom_search, 'value');
			GeneralMethod::skillsAddElastic($skillsAddElastic);
		}

		$requiredSkills = [];
		
		if (\request('cus_search') != "") {

			$checkedSkills = array_filter($custom_search, function($skill) {
				return $skill['checked'] == true;
			});
		
			$uncheckedSkills = array_filter($custom_search, function($skill) {
				return $skill['checked'] == false;
			});

			if (!empty($checkedSkills)) {
				   $requiredSkills = array_column($checkedSkills, 'value');
				} else {
					$requiredSkills = array_column($uncheckedSkills, 'value');
				}
				
			}


			$searchQuery = $request->input('q');
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
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
		$cacheKeySet = 'ats:allbenchrecommendinternalreq:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
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
		switch ($valCheck) {
			case 1:
				$sort[] = ['id' => ['order' => 'desc']];
				break;
			case 2:
				$sort[] = ['id' => ['order' => 'asc']];
				break;
			case 3:
				$sort[] = ['max_salary' => ['order' => 'asc']];
				break;
			case 4:
				$sort[] = ['max_salary' => ['order' => 'desc']];
				break;
			case 5:
				$sort[] = ['client_name' => ['order' => 'asc']];
				break;
			case 6:
				$sort[] = ['client_name' => ['order' => 'desc']];
				break;
			case 10:
				// "Best Match" — rank jobs by how many of the candidate's own
				// skills ($set_skill, already computed above) appear in each
				// job's nested job_metronics_skills. Purely a ranking signal,
				// not a filter — jobs with zero matches still appear (0%),
				// they just sort last. Mirrors the candidate-vs-job scoring
				// AppTracker's similar-candidates page already does, inverted
				// to score jobs against a candidate instead.
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								int matchCount = 0;
								if (params._source.containsKey('job_metronics_skills')) {
									for (skillDoc in params._source.job_metronics_skills) {
										if (!skillDoc.containsKey('skill_name') || skillDoc.skill_name == null) { continue; }
										String jobSkill = skillDoc.skill_name.toLowerCase();
										for (candSkill in params.skills) {
											if (jobSkill.contains(candSkill) || candSkill.contains(jobSkill)) { matchCount += 1; break; }
										}
									}
								}
								return matchCount;
							",
							'params' => ['skills' => array_values(array_filter(array_map('strtolower', $set_skill)))],
						],
						'order' => 'desc',
					],
				];
				$sort[] = ['id' => ['order' => 'desc']];
				break;
			default:
				$sort[] = ['id' => ['order' => 'desc']];
		}

		$filter[] = ['term' => ['status' => 'active']];
			// 2. HIRING TYPE
			if ($custom_hiringtype != "" && $custom_hiringtype != "All") {
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

			if (!empty($requiredSkills)) {
        
                $should = [];
            
                foreach ($requiredSkills as $skill) {
    
                    $cleanSkill = strtolower(preg_replace('/\s+/', ' ', trim($skill)));
            
                    if ($cleanSkill !== '') {
                        $should[] = [
                            'match' => [
                                'job_metronics_skills.skill_name' => [
                                    'query' => $cleanSkill,
                                    'operator' => 'and'   // ensures all words must match
                                ]
                            ]
                        ];
                    }
                }
    
                if (!empty($should)) {
                    $filter[] = [
                        'nested' => [
                            'path' => 'job_metronics_skills',
                            'query' => [
                                'bool' => [
                                    'should' => $should,
                                    'minimum_should_match' => 1
                                ]
                            ]
                        ]
                    ];
                }
            }

			if (!empty($location)) {
			    $must[] = [
                    'nested' => [
                        'path' => 'client_city_jobs',
                        'query' => [
                            'terms' => [
                                'client_city_jobs.city_id' => (array) $location
                            ]
                        ]
                    ]
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
							'gte' => (float) $min_salary
						],
					],
				];
			}
			if ($max_salary >0 && $salary_field) {
				// dd($min_salary,$max_salary);
				$must[] = [
					'range' => [
						$salary_field => [
							'lte' => (float) $max_salary
						],
					],
				];
			}
			

				// FINAL ELASTIC PARAMS
			$params = [
				'index' => ELASTIC_JOBS,
				'request_cache' => true,
				'body' => [
					'from' => $from,
					'size' => $size,
					'sort' => $sort,
					'query' => [
						'bool' => [
							'must' => $must,
							'filter' => $filter,
						],
					],
				],
			];

		// dd($location);

		if (!$this->elasticService->indexExists(ELASTIC_JOBS)) {
			$total = 0;
			$data = [];
		}else{
			$response = $this->elasticService->getClient()->search($params);
			$total = $response['hits']['total']['value'] ?? 0;
			$data = array_map(function ($hit) {
				return $hit['_source'];
			}, $response['hits']['hits']);
		}

		$pagination = [
			'total' => $total,
			'current_page' => $page,
			'last_page' =>ceil($total / $size),
			'listType' => 'allcandidate',
		];

		$userPermissions = $this->userPermissions;
		$responseData =  [
			'html' => view('service_company.ajax_pages.bench-recommend-req-ajax', compact('data','userPermissions','company_id','salaryMode','currencySymbole','sal_mode','min_salary_currency','job_status','currentRowJob','condidate_id'))->render(),
			'total' => $total,
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		];


		cache()->put($cacheKeySet, $responseData, now()->addMinutes(3));

	     return response()->json($responseData);

	}
	public function recommandedrequestcondidate(Request $request){
          
		$company_id = auth()->user()->company_id;

              $job = Job::findOrFail($request->jobId);

			//   $detailsVendorSide = GeneralMethod::companyAsVendor($job->company_id,$company_id);
			//   $mailTypeSubject = GeneralMethod::mailTypeSubject(21,1);

			//   dd($detailsVendorSide,$mailTypeSubject);


          $source_from_company_id = $company_id;
          $source_from_user_id	= auth()->user()->id;
          //   $source_from_id	 = '';
          $source_from_type  = 1;
          $source_company_id	 = $job->company_id;
          //   $source_user_id	 = '';
          $source_application_id	= (int) $request->conId;
          $source_jobid	 = (int) $request->jobId;


		        $eventId = 25;
				$coinsDeductions =  GeneralMethod::coinsValidations((int) $source_from_company_id,$eventId,auth()->user()->id,$source_company_id,'','');
				// dd($coinsDeductions);
				if($coinsDeductions->status){
					return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message,'company_coins'=> $coinsDeductions->company_coins]);
				}
            //  dd("djd");
		//   dd($source_company_id);

		    $values = array('source_from_company_id' => $source_from_company_id, 'source_from_user_id' =>$source_from_user_id, 'source_from_type' => $source_from_type,'source_company_id'=>$source_company_id,'source_application_id'=>$source_application_id,'source_from_id' => $source_application_id,'source_jobid'=>$source_jobid);
			$row_data = DB::table('job_applications')->select('*')->where('id', @$source_application_id)->first();
			$jobcountry = GeneralMethod::JobCountries($source_jobid);
			$country = @$row_data->country;

			// dd($values);
			$query = false;
			if (@$source_jobid > 0) {

				if (in_array($country, $jobcountry)) {
					$event_name = 'BenchSalesIndia';
				}else{
					$event_name = 'BenchSalesInternational';
				}

				// $source_from_company_id = $source_from_company_id;
				// $event_name = $event_name;
				$event_table_name = 'jobs';
				$event_table_id = @$source_jobid;
				$query =  DB::table('rec_candidate_assign')->insert($values);
				$rec_id = DB::getPdo()->lastInsertId();
				GeneralMethod::candidateRequestIndexElastic($rec_id);

		}
			
			
			// $query = true;
			if($query){
				
				// $packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
				    $eventId = 25;
					$coinsDeductions = GeneralMethod::coinsDeductions((int) $source_from_company_id,$eventId,auth()->user()->id, $source_company_id,'','');

				$data = array('status' => "success","message" => "");

				   $detailsVendorSide = GeneralMethod::companyAsVendor($job->company_id,$company_id);
				

				    $basecompany = GeneralMethod::baseCompany();
					$senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
                    $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
					$attachments = array();

					$cc = [];
					$checkMailPermision = GeneralMethod::mailPermission(21);
					if($checkMailPermision){  
						
					$mailTypeSubject = GeneralMethod::mailTypeSubject(21,1);

					$jobRow = DB::table('job_applications')->select('job_applications.*','city_list.city')
					->join('city_list','city_list.city_id','=','job_applications.city_id')->where('job_applications.id', '=', $source_application_id)->first();

					        $salaryMode = GeneralMethod::salaryMode();
							$currencySymbole = GeneralMethod::currencySymbole();
							$bill_rate_mode = $bill_rate_currency = $bill_rate_amount =  "";
							$bill_rate_mode = strtoupper(substr($salaryMode[$jobRow->bill_rate_mode], 0, 1));
							$bill_rate_currency	 = $currencySymbole[@$jobRow->bill_rate_currency];
							$bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($jobRow->bill_rate_amount);
							$salaryRange =  $bill_rate_currency .$bill_rate_amount . '/' . $bill_rate_mode ;  



					$subject = str_replace(["[{JOB_TITLE}]","[{JOB_ID}]"],[$job->title,$job->id], $mailTypeSubject->subject); 

					$template_description = $mailTypeSubject->body_description;

					$template_description = str_replace(["[{CLIENT_COMPANY_NAME}]","[{JOB_TITLE}]","[{JOB_ID}]","[{VENDOR_COMPANY_NAME}]","[{BENCH_CANDIDATE_NAME}]","[{BILL_RATE}]","[{EXPERENCE}]","[{LOCATION}]","[{CONTACT_PERSON_NAME}]","[{PHONE}]","[{EMAIL}]","[{SKILL}]"],[$detailsVendorSide->client_company,$job->title, $job->id, $detailsVendorSide->vendor_company, $jobRow->full_name, $salaryRange ,$jobRow->total_exp,$jobRow->city,$detailsVendorSide->vendor_name,$detailsVendorSide->vendor_phone,$detailsVendorSide->vendor_email,$jobRow->skills], $template_description);

					$message = $template_description;

					$notificationemail = GeneralMethod::notification($source_company_id,'Recuitment'); //Recuitment Sales
					$defailsEmail = DB::table('companies')->where('id',$source_company_id)->first()->company_email;
					$to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
					//    dd($to_Mail_Id);
					$datamail = [
						'message' => $message
					];

					}
				
			}else{
				$data = array('status' => "warning", "message" => "Failed");
				
			 }
			 
			$cachekeys = [
				'allbenchrecommendreq_version_user_'.$this->user->id,
				'allbenchrecommendinternalreq_version_user_'.$this->user->id
			];
			foreach ($cachekeys as $key) {
				GeneralMethod::cacheClean($key);
			}

			 return response()->json($data);


	}
	public function mailrecommandedrequestcondidate(Request $request){

		$company_id = auth()->user()->company_id;

              $job = Job::findOrFail($request->jobId);

			$user_id = auth()->user()->id;
			$jobId = $request->jobId;
			$conId = $request->conId;
			$matlaccess = $request->matlaccess;


          $source_from_company_id = $company_id;
          $source_from_user_id	= auth()->user()->id;
          //   $source_from_id	 = '';
          $source_from_type  = 1;
          $source_company_id	 = $job->company_id;
          //   $source_user_id	 = '';
          $source_application_id	= (int) $request->conId;
          $source_jobid	 = (int) $request->jobId;

		    $values = array('source_from_company_id' => $source_from_company_id, 'source_from_user_id' =>$source_from_user_id, 'source_from_type' => $source_from_type,'source_company_id'=>$source_company_id,'source_application_id'=>$source_application_id,'source_from_id' => $source_application_id,'source_jobid'=>$source_jobid);
			$row_data = DB::table('job_applications')->select('*')->where('id', @$source_application_id)->first();
			$jobcountry = GeneralMethod::JobCountries($source_jobid);
			$country = @$row_data->country;

			$packageStatusIndia = GeneralMethod::validateClientPackage('BenchClientAcccessIndia',$source_from_company_id);
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
                $salaryRange = $bill_rate_currency .' '. $bill_rate_amount . '/' . $bill_rate_mode;

				$npDate = $jobRow->np_id;
				if ($jobRow->serving_notice_period_mode == 0){
					$npDate = ($jobRow->np_id==1) ? $jobRow->np_id.' Day' : $jobRow->np_id.' Days';
				}elseif($jobRow->serving_notice_period_mode == 1) {
					$originalDate = $jobRow->np_id;
					$npDate = date("d-M-y", strtotime($originalDate));
				}else{
					$npDate='';
				}

				if ($jobRow->outsourcing_type == 1){
                     $outsourcing = 'Fulltime';
				}else{
					$outsourcing = 'Contract';
				}

				if ($jobRow->comission_type == 1){
					$comission = 'Free Service';
				}else{
					$comission = 'Paid Service';
				}

                $subject = str_replace(["[{JOB_TITLE}]", "[{JOB_ID}]","[{VENDOR_COMPANY_NAME}]"], [$job->title, $job->id,$detailsVendorSide->vendor_company], $mailTypeSubject->subject); 
                $template_description = str_replace(
                    ["[{CLIENT_COMPANY_NAME}]", "[{JOB_TITLE}]", "[{JOB_ID}]", "[{VENDOR_COMPANY_NAME}]", "[{BENCH_CANDIDATE_NAME}]", "[{BILL_RATE}]", "[{EXPERENCE}]", "[{LOCATION}]", "[{CONTACT_PERSON_NAME}]", "[{PHONE}]", "[{EMAIL}]", "[{SKILL}]","[{NP}]","[{OUTSOURCE}]","[{COMMISSION}]"],
                    [$detailsVendorSide->client_company, $job->title, $job->id, $detailsVendorSide->vendor_company, $jobRow->full_name, $salaryRange, $jobRow->total_exp, $jobRow->city, $detailsVendorSide->vendor_name, $detailsVendorSide->vendor_phone, $detailsVendorSide->vendor_email, $jobRow->skills,$npDate,$outsourcing,$comission], 
                    $mailTypeSubject->body_description
                );

                $message = $template_description;
                $notificationemail = GeneralMethod::notification($source_company_id, 'Recuitment');
                $defaultEmail = DB::table('companies')->where('id', $source_company_id)->first()->company_email;
                $to_Mail_Id = $notificationemail->email ?? $defaultEmail ?? '';
                $datamail = ['message' => $message];
				

                $mailService = new MailService();

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
	public function downloadResumeBenchCandidate(Request $request){
		$id = $request->candidateId;
		$view_count = array(
			'user_id' => auth()->user()->id,
			'candidate_id' => $id,
			'type' => 2,
			'status' => 0,
			'created_at' => date('Y-m-d H:i:'),
			'updated_at' => date('Y-m-d H:i:')
		);
			DB::table('bench_count_view_download')->insert($view_count);

	}
	public function boostkeywordadd(Request $request) 
    {
		$company_id = auth()->user()->company_id;
        $boostkeywords = DB::table('benchboostkeywordcandidates')->where('candidate_id',$request->candidate_id)->delete();
        $tags = request('tags');
    	$tags_key = json_decode($tags, true);
		if (\request('tags') != "") {
			//  dd($tags_key);
			foreach ($tags_key as $tagSkill) {
                $keyWord = $tagSkill['value'];
                $insertArr = array('company_id' =>$company_id,'candidate_id'=>$request->candidate_id, 'skill_name' => $keyWord, 'status' =>1);
                DB::table('benchboostkeywordcandidates')->insert($insertArr);
            }

        }

        return response()->json([
            'message' => '',
            'status' => '',
            'paymentLink'=> ''
        ]);  


    }
	public function benchSwitch(Request $request){
		$company_id = auth()->user()->company_id;
        $row = DB::table('job_applications')->where('id','=',$request->id)->first();

        if($row->status!=2 && $row->ads_switch !="Yes"){
            return Reply::dataOnly(['status' => 'info','message' =>'Please change the status to “BENCH” in order to activate the ADS for this profile','pack_status'=>'testing']);
		}
		 
		$message ='';
        if($row->ads_switch==="Yes"){
            $pack_status = "Ads Block";
			$status = "No";
			$message = 'Candidate Ads Stop';
        }else{
            $pack_status = "Ads Active";
			$status = "Yes";

			$eventId = 44;
			$coinsRequ = 1;
			    $coinsDeductions = GeneralMethod::coinsValidationsAllcompany($company_id,$eventId,$coinsRequ);
				 
			if($coinsDeductions->status){
				return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message,'company_coins'=> $coinsDeductions->company_coins]);
			}

			$coinsDeductions = GeneralMethod::coinsDeductions((int) $company_id, $eventId, auth()->user()->id, $company_id, '', '');
			$message = 'Candidate Ads start';

        }
          
        // dd($pack_status,$request->id);
        $result = DB::table('job_applications')->where('id','=',(int)$request->id)->update(['ads_switch'=>$status]); 

		
		$cachekeys = [
				'allbenchcandidates_version_user_'.$this->user->id,
				'benchviewmore_version_user_'.$this->user->id,
			];
			foreach ($cachekeys as $key) {
				GeneralMethod::cacheClean($key);
			}
       
        if($result){
			GeneralMethod::elasticBenchCandidateAddEditIndex($request->id);
           return Reply::dataOnly(['status' => 'success','message' =>$message,'pack_status'=>$pack_status]);
           
        } 
        return Reply::dataOnly(['status' => 'faild','pack_status'=>$pack_status]);
    }
	private function benchjobAndRecomCount($id)
    {
        //   dd($id);
        $company_id = auth()->user()->company_id;

		$currentRowJob = JobApplication::find($id);
		$condidate_id  = (int) $id;
		$currentJobSkill = explode(",", $currentRowJob->skills);

		$set_skill = array();
		if ($currentJobSkill > 0) {
			foreach ($currentJobSkill as $val) {
				array_push($set_skill, trim($val));
			}
		}
		$skill = DB::table('job_metronics_skills')->whereIn('skill_name', $set_skill)->pluck('job_id')->toArray();

		$gdFilterData = GeneralMethod::canGgdFilterData($id);
		
		$default_loc = $gdFilterData->default_loc;
		$qualification = $gdFilterData->qualification;
		$min_exp = ($gdFilterData->min_exp >=1) ? $gdFilterData->min_exp-1 : $gdFilterData->min_exp;
		$max_exp = $gdFilterData->max_exp+1;
		$min_salary_currency = $gdFilterData->min_salary_currency;
		$min_salary ='';// $gdFilterData->min_salary;
		$max_salary = $gdFilterData->max_salary;
		$sal_mode = $gdFilterData->min_sal_mode;
		$default_skill = $gdFilterData->default_skill;
		// $max_exp = floor($max_exp / 10) * 10;
		//  dd($this->max_salary,($this->max_exp));
		$cities = DB::table('city_list')->pluck('city')->toArray();
        $location = [$gdFilterData->city_id];
		// dd($location);
		$statusArr = ['active', 'hold', 'inactive', '2'];
		$must = [];
		$sort = [];
		$should = [];
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



		if (!empty($set_skill)) {
        
                $should = [];
            
                foreach ($set_skill as $skill) {
    
                    $cleanSkill = strtolower(preg_replace('/\s+/', ' ', trim($skill)));
            
                    if ($cleanSkill !== '') {
                        $should[] = [
                            'match' => [
                                'job_metronics_skills.skill_name' => [
                                    'query' => $cleanSkill,
                                    'operator' => 'and'   // ensures all words must match
                                ]
                            ]
                        ];
                    }
                }
    
                if (!empty($should)) {
                    $filter[] = [
                        'nested' => [
                            'path' => 'job_metronics_skills',
                            'query' => [
                                'bool' => [
                                    'should' => $should,
                                    'minimum_should_match' => 1
                                ]
                            ]
                        ]
                    ];
                }
        }
			
		

		$page = 1;
		$size = 10000; // Default 20 results per page
		$from = ($page - 1) * $size;

		// Mirror the two range clauses all_data_ajax()/all_internal_data_ajax()
		// ALWAYS apply, even in the filter drawer's neutral default state
		// (min_exp is forced to 0 server-side there → max_exp >= 0; the drawer
		// always posts a salary mode with min salary 0 → {mode field} >= 0).
		// Without these, this card count can include job documents the list
		// query can never return (docs missing max_exp / salary fields), which
		// showed up as "card says 12, list shows 10". Single source of truth:
		// this count must be the list's default query, exactly.
		$salaryFieldByMode = [0 => 'max_yearly', 1 => 'max_monthly', 2 => 'max_hourly'];
		$countSalaryField  = $salaryFieldByMode[(int) $sal_mode] ?? 'max_yearly';
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

		// dd($location);

		if (!$this->elasticService->indexExists(ELASTIC_JOBS)) {
			$total = 0;
			$data = [];
		}else{

			$response = $this->elasticService->getClient()->count($params);
			$total = $response['count'] ?? 0;
		}


		$params = [
        	'index' => ELASTIC_CANDIDATES_BENCH,
        	'id'    => $id,
        ];

        $response = $this->elasticService->getClient()->get($params);
		$data = (object) $response['_source'];
		$yourArray = $data->jobs;
		$jobIds = array_column($yourArray, 'job_id');

		// dd(count($jobIds));

		// 4. COMPANY ACCESS CONDITIONS

		$must = [];
		$should = [];
		$filter = [];
		$sort = [];
		$filter[] = ['term' => ['status' => 'active']];

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

		

		if (!empty($set_skill)) {
        
                $should = [];
            
                foreach ($set_skill as $skill) {
    
                    $cleanSkill = strtolower(preg_replace('/\s+/', ' ', trim($skill)));
            
                    if ($cleanSkill !== '') {
                        $should[] = [
                            'match' => [
                                'job_metronics_skills.skill_name' => [
                                    'query' => $cleanSkill,
                                    'operator' => 'and'   // ensures all words must match
                                ]
                            ]
                        ];
                    }
                }
    
                if (!empty($should)) {
                    $filter[] = [
                        'nested' => [
                            'path' => 'job_metronics_skills',
                            'query' => [
                                'bool' => [
                                    'should' => $should,
                                    'minimum_should_match' => 1
                                ]
                            ]
                        ]
                    ];
                }
        }

		$page = 1;
		$size = 10000; // Default 20 results per page
		$from = ($page - 1) * $size;

		// Same default-query mirror as the client-jobs count above —
		// $countSalaryField was computed there from the candidate's salary mode.
		$must[] = ['range' => ['max_exp' => ['gte' => 0]]];
		$must[] = ['range' => [$countSalaryField => ['gte' => 0]]];

		$paramsInternal = [
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

		// dd($location);

		if (!$this->elasticService->indexExists(ELASTIC_JOBS)) {
			$totalInternal = 0;
			$data = [];
		}else{
			$response = $this->elasticService->getClient()->count($paramsInternal);
			$totalInternal = $response['count'] ?? 0;
		}

		return $result=[
				'bench_explore_counter'=>count($jobIds),
				'recommend_req_counter'=>$total,
				'recommend_internal_req_counter'=>$totalInternal,
		];
		
    }

}
