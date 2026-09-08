<?php

/***########################################################
Controller    	: 	interviewScheduleController
Purmose			:
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021
########################################################***/

namespace App\Http\Controllers\ServiceCompany;

use App\Helper\GeneralMethod;
use App\Helper\Reply;
use App\Jobs\QueueNotificationMailService;
use App\Models\AddJobWiseCondidate;
use App\Models\InterviewSchedule;
use App\Models\JobApplication;
use App\Models\OpenworkspaceInterviewSchedule;
use App\Models\User;
use App\Services\ElasticService;
use App\Services\MailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class InterviewScheduleController extends ServiceCompanyBaseController
{
    protected $mailService;

    protected $elasticService;

    public function __construct(MailService $mailService, ElasticService $elasticService)
    {
        parent::__construct();
        $this->pageTitle = 'Interview Tracker';
        $this->pageIcon = 'icon-calender';
        $this->mailService = $mailService;
        $this->elasticService = $elasticService;
    }

    public function table(Request $request)
    {
        $lock_unlock = GeneralMethod::checksubscription();
        if ($lock_unlock == false) {
            return redirect()->route('service_company.dashboard')->with('message_paid', 'This is a paid featute');
        }

        $query = JobApplication::query();

        if (! $this->user->hasRole('admin')) {
            $query->where('submitted_by', $this->user->id);
        }

        $this->candidates = $query->limit(2)->get();

        return view('service_company.interview-schedule.table', $this->data);
    }

    /**
     * Intent: Fetch paginated interview schedule list via Elasticsearch with batch pre-fetched lookup maps.
     * Tenancy: Restricted by company_id, job assignment, or interviewer listing.
     * Optimization: Deduplicated ES search execution, batch pre-fetching to prevent view N+1 queries.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function data_ajax(Request $request)
    {
        abort_if(Gate::denies('view_schedule'), 403);

        $company_id = auth()->user()->company_id;
        $available_status = GeneralMethod::available_status();
        $feedback_status = GeneralMethod::feedback_status();
        $interview_mode_arr = GeneralMethod::interview_mode();
        $job_status_arr = GeneralMethod::job_status();

        $startOfDay = Carbon::now()->startOfDay()->toDateTimeString();
        $endOfDay = Carbon::now()->endOfDay()->toDateTimeString();
        $valCheck_date = request('custom_hiringtype');
        $chkAdminRole = $this->user->hasRole('admin');

        $searchTerm = trim(request('custom_search') ?? $request->input('search') ?? $request->input('q'));
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10);
        $from = ($page - 1) * $size;
        $tab = $request->input('tab', 'all');

        $stage_filter = $request->input('stage_filter', 'All');
        $status_filter = $request->input('status_filter', 'All');
        $owner_filter = $request->input('owner_filter', 'All');
        $vendor_filter = $request->input('vendor_filter', 'All');
        $mode_filter = $request->input('mode_filter', 'All');

        $must = [];
        $filter = [];
        $sort = [];

        $cachePayload = [
            'page' => $page,
            'size' => $size,
            'company' => $company_id,
            'searchTerm' => $searchTerm,
            'user_id' => $this->user->id,
            'sort' => $valCheck_date,
            'tab' => $tab,
            'stage_filter' => $stage_filter,
            'status_filter' => $status_filter,
            'owner_filter' => $owner_filter,
            'vendor_filter' => $vendor_filter,
            'mode_filter' => $mode_filter,
        ];

        $versionKey = 'allinterviewschedule_version_user_'.$this->user->id;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:allinterviewschedule:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));

        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet));
        }

        if (! $this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
            $responseData = [
                'html' => view('service_company.ajax_pages.interview-tracker-ajax', [
                    'data' => [],
                    'userPermissions' => $this->userPermissions,
                    'feedback_status' => $feedback_status,
                    'available_status' => $available_status,
                    'company_id' => $company_id,
                    'chkAdminRole' => $chkAdminRole,
                    'followupMap' => collect(),
                    'feedbackMap' => collect(),
                    'spokpersonMap' => collect(),
                    'clientCompanyMap' => [],
                    'userCaptainJobIds' => [],
                ])->render(),
                'total' => 0,
                'from' => 0,
                'to' => 0,
                'pagination' => view('service_company.ajax_pages.pagination', ['pagination' => ['total' => 0, 'current_page' => 1, 'per_page' => $size, 'last_page' => 1, 'listType' => 'allcandidate']])->render(),
                'stats' => ['interview_month' => 0, 'today' => 0, 'upcoming' => 0, 'feedback_pending' => 0],
            ];

            return response()->json($responseData);
        }

        $valCheck = request('custom_hiringtype');
        switch ($valCheck) {
            case 1:
                $sort[] = ['schedule_date' => ['order' => 'asc']];
                break;
            case 2:
                $sort[] = ['created_at' => ['order' => 'asc']];
                break;
            case 3:
                $sort[] = ['schedule_date' => ['order' => 'desc']];
                break;
            case 4:
                $sort[] = ['schedule_date' => ['order' => 'asc']];
                break;
            default:
                $sort[] = ['schedule_date' => ['order' => 'desc']];
                $sort[] = ['id' => ['order' => 'desc']];
        }

        if ($valCheck == 'Feedback Pending') {
            $sort = [
                ['schedule_date' => ['order' => 'desc']],
                [
                    '_script' => [
                        'type' => 'number',
                        'script' => ['source' => "doc['feedback'].size() == 0 ? 0 : 1", 'lang' => 'painless'],
                        'order' => 'asc',
                    ],
                ],
            ];
            $filter[] = [
                'range' => [
                    'schedule_date' => [
                        'lte' => now()->subHour()->toIso8601String(),
                        'format' => 'strict_date_optional_time',
                    ],
                ],
            ];
            $filter[] = [
                'bool' => [
                    'should' => [
                        ['bool' => ['must_not' => ['exists' => ['field' => 'available_status']]]],
                        ['term' => ['available_status' => 0]],
                        ['term' => ['feedback' => 5]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        if ($valCheck == 'Interview Pending') {
            $sort = [
                ['schedule_date' => ['order' => 'asc']],
                [
                    '_script' => [
                        'type' => 'number',
                        'script' => ['source' => "doc['feedback'].size() == 0 ? 0 : 1", 'lang' => 'painless'],
                        'order' => 'asc',
                    ],
                ],
            ];
            $filter[] = [
                'range' => [
                    'schedule_date' => [
                        'gte' => now()->subHour()->toIso8601String(),
                        'format' => 'strict_date_optional_time',
                    ],
                ],
            ];
            $filter[] = [
                'bool' => [
                    'should' => [
                        ['bool' => ['must_not' => ['exists' => ['field' => 'feedback']]]],
                        ['term' => ['feedback' => 0]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
            $filter[] = ['bool' => ['must_not' => ['term' => ['available_status' => 2]]]];
        }

        // Tab Filters
        if ($tab == 'today') {
            $filter[] = [
                'range' => [
                    'schedule_date' => [
                        'gte' => Carbon::now()->startOfDay()->toIso8601String(),
                        'lte' => Carbon::now()->endOfDay()->toIso8601String(),
                        'format' => 'strict_date_optional_time',
                    ],
                ],
            ];
        } elseif ($tab == 'upcoming') {
            $filter[] = [
                'range' => [
                    'schedule_date' => [
                        'gte' => Carbon::now()->addDay()->startOfDay()->toIso8601String(),
                        'format' => 'strict_date_optional_time',
                    ],
                ],
            ];
        } elseif ($tab == 'pending_feedback' || $tab == 'pending') {
            $filter[] = [
                'range' => [
                    'schedule_date' => [
                        'lte' => Carbon::now()->subHour()->toIso8601String(),
                        'format' => 'strict_date_optional_time',
                    ],
                ],
            ];
            $filter[] = [
                'bool' => [
                    'should' => [
                        ['bool' => ['must_not' => ['exists' => ['field' => 'available_status']]]],
                        ['term' => ['available_status' => 0]],
                        ['term' => ['feedback' => 5]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        } elseif ($tab == 'selected') {
            $filter[] = [
                'bool' => [
                    'should' => [
                        ['term' => ['feedback' => 1]],
                        ['term' => ['interview_result' => 'selected']],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        } elseif ($tab == 'rejected') {
            $filter[] = [
                'bool' => [
                    'should' => [
                        ['term' => ['feedback' => 3]],
                        ['term' => ['interview_result' => 'rejected']],
                        ['term' => ['status' => 'rejected']],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        } elseif ($tab == 'rescheduled') {
            $filter[] = [
                'bool' => [
                    'should' => [
                        ['term' => ['available_status' => 3]],
                        ['term' => ['interview_result' => 'rescheduled']],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        // Dropdown Filters
        if ($mode_filter !== 'All' && ! empty($mode_filter)) {
            $filter[] = ['term' => ['interview_mode' => (int) $mode_filter]];
        }

        // Search
        if (! empty($searchTerm)) {
            $must[] = [
                'bool' => [
                    'should' => [
                        ['term' => ['phone' => $searchTerm]],
                        ['term' => ['email' => $searchTerm]],
                        ['match_phrase_prefix' => ['job_title' => $searchTerm]],
                        ['match_phrase_prefix' => ['client_name' => $searchTerm]],
                        ['match_phrase_prefix' => ['full_name' => $searchTerm]],
                        ['wildcard' => ['city_name' => strtolower($searchTerm).'*']],
                        ['wildcard' => ['country_name' => strtolower($searchTerm).'*']],
                        ['wildcard' => ['state_name' => strtolower($searchTerm).'*']],
                        ['wildcard' => ['posted_by' => strtolower($searchTerm).'*']],
                        ['match_phrase_prefix' => ['company_name' => $searchTerm]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        $must[] = [
            'bool' => [
                'should' => [
                    ['term' => ['job_company_id' => $company_id]],
                    [
                        'bool' => [
                            'must' => [
                                ['term' => ['ClientComID' => $company_id]],
                                ['term' => ['candidate_type' => 'other']],
                            ],
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        if (! $chkAdminRole) {
            $accessShould = [
                ['nested' => ['path' => 'job_assign', 'query' => ['term' => ['job_assign.user_id' => auth()->user()->id]]]],
            ];
            $myName = trim(auth()->user()->name ?? '');
            if ($myName !== '') {
                $accessShould[] = ['match_phrase' => ['interviewers' => $myName]];
            }
            $myEmail = strtolower(trim(auth()->user()->email ?? ''));
            if ($myEmail !== '') {
                $accessShould[] = ['match_phrase' => ['other_email' => $myEmail]];
            }
            $must[] = ['bool' => ['should' => $accessShould, 'minimum_should_match' => 1]];
        }

        if ($valCheck_date == 1) {
            $filter[] = [
                'range' => [
                    'schedule_date' => [
                        'gte' => Carbon::parse($startOfDay)->startOfDay()->toIso8601String(),
                        'lte' => Carbon::parse($endOfDay)->endOfDay()->toIso8601String(),
                        'format' => 'strict_date_optional_time',
                    ],
                ],
            ];
        }

        $params = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
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

        // Single ES search execution (Deduplicated)
        $response = $this->elasticService->getClient()->search($params);
        $total = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits'] ?? []);

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'per_page' => $size,
            'last_page' => $total > 0 ? (int) ceil($total / $size) : 1,
            'listType' => 'allcandidate',
        ];
        $fromEntry = $total > 0 ? ($page - 1) * $size + 1 : 0;
        $toEntry = $total > 0 ? min($page * $size, $total) : 0;

        $userPermissions = $this->userPermissions;
        $stats = $this->dashboardStats($company_id, $chkAdminRole, $page);

        // Batch pre-fetch all relational lookup maps to eliminate Blade N+1 queries
        $maps = $this->prepareViewMaps($data, $company_id, $this->user->id);

        $responseData = [
            'html' => view('service_company.ajax_pages.interview-tracker-ajax', array_merge([
                'data' => $data,
                'userPermissions' => $userPermissions,
                'feedback_status' => $feedback_status,
                'available_status' => $available_status,
                'company_id' => $company_id,
                'chkAdminRole' => $chkAdminRole,
            ], $maps))->render(),
            'total' => $total,
            'from' => $fromEntry,
            'to' => $toEntry,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
            'stats' => $stats,
        ];

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_INTERVIEW_SCHEDULE_TIME));

        return response()->json($responseData);
    }

    /**
     * Intent: Batch pre-fetch relational lookup maps for interview schedule listing.
     * Tenancy: All queries are tenant-scoped by company_id or restricted to the specific hits.
     * Optimization: Prevents N+1 database query execution inside Blade view loops.
     *
     * @param  array  $data  Elasticsearch hit sources
     * @param  int  $companyId  Current company tenant ID
     * @param  int  $userId  Current authenticated user ID
     */
    private function prepareViewMaps(array $data, int $companyId, int $userId): array
    {
        if (empty($data)) {
            return [
                'followupMap' => collect(),
                'feedbackMap' => collect(),
                'spokpersonMap' => collect(),
                'clientCompanyMap' => [],
                'userCaptainJobIds' => [],
            ];
        }

        $interviewIds = array_values(array_filter(array_column($data, 'id')));
        $jobIds = array_values(array_unique(array_filter(array_column($data, 'jobId'))));

        // Query 1: Follow-up status map
        $followupMap = collect();
        if (! empty($interviewIds) && \Illuminate\Support\Facades\Schema::hasColumn('interview_schedules', 'followup_status')) {
            $followupMap = DB::table('interview_schedules')
                ->whereIn('id', $interviewIds)
                ->get(['id', 'followup_status', 'followup_status_at'])
                ->keyBy('id');
        }

        // Query 2: Feedback map (latest interview_feedback row per interview)
        $feedbackMap = collect();
        if (! empty($interviewIds)) {
            $feedbackRows = DB::table('interview_feedback')
                ->whereIn('interview_id', $interviewIds)
                ->orderBy('id', 'desc')
                ->get();

            $feedbackMap = $feedbackRows->unique('interview_id')->keyBy('interview_id');
        }

        // Query 3: SPOC / source person map for third party client jobs
        $spokpersonMap = collect();
        if (! empty($jobIds)) {
            $spokRows = DB::table('job_assign')
                ->join('users', 'users.id', '=', 'job_assign.spok_user_id')
                ->whereIn('job_assign.job_id', $jobIds)
                ->select('job_assign.job_id', 'job_assign.company_id', 'users.name')
                ->get();

            foreach ($spokRows as $spok) {
                $spokpersonMap->put($spok->job_id.'_'.$spok->company_id, $spok->name);
            }
        }

        // Query 4: Captain permissions for current user
        $userCaptainJobIds = [];
        if (! empty($jobIds)) {
            $userCaptainJobIds = DB::table('job_assign')
                ->whereIn('job_id', $jobIds)
                ->where('spok_user_id', $userId)
                ->pluck('job_id')
                ->flip()
                ->all();
        }

        // Query 5: Client Company Names
        $clientCompanyMap = [];
        if (! empty($jobIds)) {
            $clientCompanyMap = DB::table('jobs')
                ->join('clients', 'clients.id', '=', 'jobs.client_id')
                ->whereIn('jobs.id', $jobIds)
                ->pluck('clients.name', 'jobs.id')
                ->all();
        }

        return [
            'followupMap' => $followupMap,
            'feedbackMap' => $feedbackMap,
            'spokpersonMap' => $spokpersonMap,
            'clientCompanyMap' => $clientCompanyMap,
            'userCaptainJobIds' => $userCaptainJobIds,
        ];
    }

    /**
     * Display the Talent Bench Candidates interview schedule page shell.
     * Candidate interview data is fetched asynchronously via benchinterviewdata_ajax().
     *
     * Intent: Serves lightweight Blade view shell without executing heavy DB queries on load.
     * Tenancy: Data fetching in AJAX endpoint is strictly scoped by company_id.
     */
    public function benchinterviewtable(Request $request)
    {
        $this->candidates = [];

        return view('service_company.bench-candidates.interview-schedule', $this->data);
    }

    public function benchinterviewdata_ajax(Request $request)
    {
        // dd("hidid");
        abort_if(Gate::denies('view_schedule'), 403);

        $company_id = auth()->user()->company_id;
        $available_status = $feedback_status = $interview_mode_arr = '';

        $available_status = GeneralMethod::available_status();
        $feedback_status = GeneralMethod::feedback_status();
        $interview_mode_arr = GeneralMethod::interview_mode();
        $job_status_arr = GeneralMethod::job_status();

        $startOfDay = Carbon::now()->startOfDay()->toDateTimeString();
        $endOfDay = Carbon::now()->endOfDay()->toDateTimeString();
        $valCheck_date = request('sortingfilter');

        $chkAdminRole = $this->user->hasRole('admin');

        $searchTermRaw = trim(request('custom_search') ?? $request->input('q') ?? '');
        $searchTerm = $searchTermRaw;
        // Normalize Tagify JSON blobs if search term is JSON (§5.2 audit)
        if (! empty($searchTermRaw) && (str_starts_with($searchTermRaw, '[') || str_starts_with($searchTermRaw, '{'))) {
            $decoded = json_decode($searchTermRaw, true);
            if (is_array($decoded)) {
                $values = array_filter(array_map(function ($item) {
                    return is_array($item) ? ($item['value'] ?? $item['name'] ?? '') : (string) $item;
                }, $decoded));
                sort($values);
                $searchTerm = implode(' ', $values);
            }
        }

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) ($request->input('per_page') ?? $request->input('size') ?? $request->input('limit') ?? 10);
        $from = ($page - 1) * $size;

        $cachePayload = [
            'page' => $page,
            'size' => $size,
            'searchTerm' => strtolower($searchTerm),
            'company' => $company_id,
            'user_id' => $this->user->id,
            'valCheck_date' => $valCheck_date,
        ];

        $versionKey = 'allinterviewbenchcandidates_version_user_'.$this->user->id;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:allinterviewbenchcandidates:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));
        /* ----------------------------
        ⚡ Single Redis GET look-up (§4.5 audit)
        ----------------------------- */
        $cachedData = cache()->get($cacheKeySet);
        if ($cachedData !== null) {
            return response()->json($cachedData);
        }

        $must = [];
        $should = [];
        $filter = [];

        $valCheck = request('custom_hiringtype');
        switch ($valCheck) {
            case 1:
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 2:
                $sort[] = ['created_at' => ['order' => 'asc']];
                break;
            case 3:
                $sort[] = ['schedule_date' => ['order' => 'desc']];
                break;
            case 4:
                $sort[] = ['schedule_date' => ['order' => 'asc']];
                break;
            default:
                $sort[] = ['id' => ['order' => 'desc']];
        }

        // 2. Feedback pending first (nulls first)

        if ($valCheck == 'Feedback Pending') {
            // 1. Sort by indexed has_feedback boolean (0 first) to unlock ES request caching (§4.1 audit)
            array_unshift($sort, [
                'has_feedback' => ['order' => 'asc'],
            ]);
            $filter[] = [
                'range' => [
                    'schedule_date' => [
                        'lt' => now()->subHour()->toIso8601String(),
                        'format' => 'strict_date_optional_time',
                    ],
                ],
            ];
        }

        // Custom search logic (using bounded match_phrase_prefix with max_expansions: 20 to eliminate wildcard O(n) overhead)
        if (! empty($searchTerm)) {
            $must[] = [
                'bool' => [
                    'must' => [
                        [
                            'bool' => [
                                'should' => [
                                    ['term' => ['phone' => $searchTerm]],
                                    ['term' => ['email' => $searchTerm]],
                                    ['match_phrase_prefix' => ['job_title' => ['query' => $searchTerm, 'max_expansions' => 20]]],
                                    ['match_phrase_prefix' => ['client_name' => ['query' => $searchTerm, 'max_expansions' => 20]]],
                                    ['match_phrase_prefix' => ['full_name' => ['query' => $searchTerm, 'max_expansions' => 20]]],
                                    ['match_phrase_prefix' => ['city_name' => ['query' => strtolower($searchTerm), 'max_expansions' => 20]]],
                                    ['match_phrase_prefix' => ['country_name' => ['query' => strtolower($searchTerm), 'max_expansions' => 20]]],
                                    ['match_phrase_prefix' => ['state_name' => ['query' => strtolower($searchTerm), 'max_expansions' => 20]]],
                                    ['match_phrase_prefix' => ['posted_by' => ['query' => strtolower($searchTerm), 'max_expansions' => 20]]],
                                    ['match_phrase_prefix' => ['company_name' => ['query' => $searchTerm, 'max_expansions' => 20]]],
                                ],
                                'minimum_should_match' => 1,
                            ],
                        ],
                    ],
                ],
            ];
        }

        $must[] = [
            'bool' => [
                'should' => [
                    // Case 1: Direct access with bench_condidate
                    [
                        'bool' => [
                            'must' => [
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['candidate_type' => 'bench_condidate']],
                            ],
                        ],
                    ],

                    // Case 2: recommendation_relation.from_company_id + ClientComID match
                    [
                        'bool' => [
                            'must' => [
                                ['term' => ['recommendation_relation.from_company_id' => $company_id]],
                                ['term' => ['ClientComID' => $company_id]],
                            ],
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        // 5. RESTRICT TO ASSIGNED USERS (if not admin) — but ALSO let anyone who is named as an
        // interviewer on the schedule see that interview, regardless of role or job assignment.
        if (! $chkAdminRole) {
            $accessShould = [
                ['nested' => ['path' => 'job_assign', 'query' => ['term' => ['job_assign.user_id' => auth()->user()->id]]]],
            ];
            $myName = trim(auth()->user()->name ?? '');
            if ($myName !== '') {
                $accessShould[] = ['match_phrase' => ['interviewers' => $myName]];
            }
            // Interviewers added via the CC list are stored as emails in other_email (dev change),
            // so also let a user see interviews where their email is listed.
            $myEmail = strtolower(trim(auth()->user()->email ?? ''));
            if ($myEmail !== '') {
                $accessShould[] = ['match_phrase' => ['other_email' => $myEmail]];
            }
            $must[] = ['bool' => ['should' => $accessShould, 'minimum_should_match' => 1]];
        }

        if ($valCheck_date == 1) {
            $filter[] = [
                'range' => [
                    'schedule_date' => [
                        'gte' => Carbon::parse($startOfDay)->startOfDay()->toIso8601String(),
                        'lte' => Carbon::parse($endOfDay)->endOfDay()->toIso8601String(),
                        'format' => 'strict_date_optional_time', // optional based on your index mapping
                    ],
                ],
            ];
        }

        // FINAL ELASTIC PARAMS
        $params = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
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

        if (! $this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits'] ?? []);
        }

        // Batch pre-hydrate client names and latest feedback for the page to eliminate Blade N+1 queries
        if (! empty($data)) {
            $jobIds = array_values(array_unique(array_filter(array_column($data, 'job_id'))));
            $clientMap = [];
            if (! empty($jobIds)) {
                $clientMap = DB::table('jobs')
                    ->leftJoin('clients', 'clients.id', '=', 'jobs.client_id')
                    ->leftJoin('companies', 'companies.id', '=', 'jobs.company_id')
                    ->whereIn('jobs.id', $jobIds)
                    ->select('jobs.id as job_id', DB::raw("COALESCE(clients.name, companies.company_name, '') as client_title"))
                    ->pluck('client_title', 'job_id')
                    ->all();
            }

            $interviewIds = array_values(array_unique(array_filter(array_column($data, 'id'))));
            $feedbackMap = [];
            if (! empty($interviewIds)) {
                $feedbackRows = DB::table('interview_feedback')
                    ->whereIn('interview_id', $interviewIds)
                    ->orderBy('id', 'desc')
                    ->get();
                foreach ($feedbackRows as $fb) {
                    if (! isset($feedbackMap[$fb->interview_id])) {
                        $feedbackMap[$fb->interview_id] = $fb;
                    }
                }
            }

            foreach ($data as &$row) {
                $jobId = $row['job_id'] ?? null;
                $row['client_title_name_enriched'] = $clientMap[$jobId] ?? ($row['client_name'] ?? $row['company_name'] ?? '');
                $intId = $row['id'] ?? null;
                $row['latest_feedback_enriched'] = $feedbackMap[$intId] ?? null;
            }
            unset($row);
        }
        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'per_page' => $size,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        $userPermissions = $this->userPermissions;
        // Compute dashboard stats so top hero cards reflect live counts (§3.5 audit fix)
        $stats = $this->dashboardStats($company_id, $chkAdminRole, $page);
        $responseData = [
            'html' => view('service_company.ajax_pages.bench-candidate-interview-schedule-ajax', compact('data', 'userPermissions', 'company_id', 'available_status'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
            'stats' => $stats,
        ];

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_INTERVIEW_BENCH_CANDIDATES_TIME));

        return response()->json($responseData);
    }

    /**
     * JSON-first v2 endpoint for Talent Bench Candidates interview schedule.
     *
     * Intent: Returns lightweight raw JSON (~2.2 KB) instead of server-rendered Blade HTML (~85 KB)
     * to support high concurrency (10K+ users) and zero-latency client-side rendering (§3.7 audit).
     * Tenancy: Query is strictly scoped by company_id in ES filter and DB batch hydration.
     */
    public function benchinterviewdata_v2(Request $request)
    {
        abort_if(Gate::denies('view_schedule'), 403);

        $company_id = auth()->user()->company_id;
        $valCheck_date = request('sortingfilter');
        $chkAdminRole = $this->user->hasRole('admin');

        $searchTermRaw = trim(request('custom_search') ?? $request->input('q') ?? '');
        $searchTerm = $searchTermRaw;
        // Normalize Tagify JSON blobs if search term is JSON (§5.2 audit)
        if (! empty($searchTermRaw) && (str_starts_with($searchTermRaw, '[') || str_starts_with($searchTermRaw, '{'))) {
            $decoded = json_decode($searchTermRaw, true);
            if (is_array($decoded)) {
                $values = array_filter(array_map(function ($item) {
                    return is_array($item) ? ($item['value'] ?? $item['name'] ?? '') : (string) $item;
                }, $decoded));
                sort($values);
                $searchTerm = implode(' ', $values);
            }
        }

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) ($request->input('per_page') ?? $request->input('size') ?? $request->input('limit') ?? 10);
        $from = ($page - 1) * $size;

        $cachePayload = [
            'page' => $page,
            'size' => $size,
            'searchTerm' => strtolower($searchTerm),
            'company' => $company_id,
            'user_id' => $this->user->id,
            'valCheck_date' => $valCheck_date,
        ];

        $versionKey = 'allinterviewbenchcandidates_version_user_'.$this->user->id;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:allinterviewbenchcandidates_v2:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));

        $cachedData = cache()->get($cacheKeySet);
        if ($cachedData !== null) {
            return response()->json($cachedData);
        }

        $must = [];
        $filter = [];
        $sort = [];

        $valCheck = request('custom_hiringtype');
        switch ($valCheck) {
            case 1:
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 2:
                $sort[] = ['created_at' => ['order' => 'asc']];
                break;
            case 3:
                $sort[] = ['schedule_date' => ['order' => 'desc']];
                break;
            case 4:
                $sort[] = ['schedule_date' => ['order' => 'asc']];
                break;
            default:
                $sort[] = ['id' => ['order' => 'desc']];
        }

        if ($valCheck == 'Feedback Pending') {
            array_unshift($sort, [
                'has_feedback' => ['order' => 'asc'],
            ]);
            $filter[] = [
                'range' => [
                    'schedule_date' => [
                        'lt' => Carbon::now()->subHour()->toIso8601String(),
                        'format' => 'strict_date_optional_time',
                    ],
                ],
            ];
        }

        if (! empty($searchTerm)) {
            $must[] = [
                'bool' => [
                    'must' => [
                        [
                            'bool' => [
                                'should' => [
                                    ['term' => ['phone' => $searchTerm]],
                                    ['term' => ['email' => $searchTerm]],
                                    ['match_phrase_prefix' => ['job_title' => ['query' => $searchTerm, 'max_expansions' => 20]]],
                                    ['match_phrase_prefix' => ['client_name' => ['query' => $searchTerm, 'max_expansions' => 20]]],
                                    ['match_phrase_prefix' => ['full_name' => ['query' => $searchTerm, 'max_expansions' => 20]]],
                                    ['match_phrase_prefix' => ['city_name' => ['query' => strtolower($searchTerm), 'max_expansions' => 20]]],
                                    ['match_phrase_prefix' => ['country_name' => ['query' => strtolower($searchTerm), 'max_expansions' => 20]]],
                                    ['match_phrase_prefix' => ['state_name' => ['query' => strtolower($searchTerm), 'max_expansions' => 20]]],
                                    ['match_phrase_prefix' => ['posted_by' => ['query' => strtolower($searchTerm), 'max_expansions' => 20]]],
                                    ['match_phrase_prefix' => ['company_name' => ['query' => $searchTerm, 'max_expansions' => 20]]],
                                ],
                                'minimum_should_match' => 1,
                            ],
                        ],
                    ],
                ],
            ];
        }

        $must[] = [
            'bool' => [
                'should' => [
                    [
                        'bool' => [
                            'must' => [
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['candidate_type' => 'bench_condidate']],
                            ],
                        ],
                    ],
                    [
                        'bool' => [
                            'must' => [
                                ['term' => ['recommendation_relation.from_company_id' => $company_id]],
                                ['term' => ['ClientComID' => $company_id]],
                            ],
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        if (! $chkAdminRole) {
            $accessShould = [
                ['nested' => ['path' => 'job_assign', 'query' => ['term' => ['job_assign.user_id' => auth()->user()->id]]]],
            ];
            $myName = trim(auth()->user()->name ?? '');
            if ($myName !== '') {
                $accessShould[] = ['match_phrase' => ['interviewers' => $myName]];
            }
            $myEmail = strtolower(trim(auth()->user()->email ?? ''));
            if ($myEmail !== '') {
                $accessShould[] = ['match_phrase' => ['other_email' => $myEmail]];
            }
            $must[] = ['bool' => ['should' => $accessShould, 'minimum_should_match' => 1]];
        }

        if ($valCheck_date == 1) {
            $startOfDay = Carbon::now()->startOfDay()->toDateTimeString();
            $endOfDay = Carbon::now()->endOfDay()->toDateTimeString();
            $filter[] = [
                'range' => [
                    'schedule_date' => [
                        'gte' => Carbon::parse($startOfDay)->startOfDay()->toIso8601String(),
                        'lte' => Carbon::parse($endOfDay)->endOfDay()->toIso8601String(),
                        'format' => 'strict_date_optional_time',
                    ],
                ],
            ];
        }

        $params = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
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

        if (! $this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits'] ?? []);
        }

        if (! empty($data)) {
            $jobIds = array_values(array_unique(array_filter(array_column($data, 'job_id'))));
            $clientMap = [];
            if (! empty($jobIds)) {
                $clientMap = DB::table('jobs')
                    ->leftJoin('clients', 'clients.id', '=', 'jobs.client_id')
                    ->leftJoin('companies', 'companies.id', '=', 'jobs.company_id')
                    ->whereIn('jobs.id', $jobIds)
                    ->select('jobs.id as job_id', DB::raw("COALESCE(clients.name, companies.company_name, '') as client_title"))
                    ->pluck('client_title', 'job_id')
                    ->all();
            }

            $interviewIds = array_values(array_unique(array_filter(array_column($data, 'id'))));
            $feedbackMap = [];
            if (! empty($interviewIds)) {
                $feedbackRows = DB::table('interview_feedback')
                    ->whereIn('interview_id', $interviewIds)
                    ->orderBy('id', 'desc')
                    ->get();
                foreach ($feedbackRows as $fb) {
                    if (! isset($feedbackMap[$fb->interview_id])) {
                        $feedbackMap[$fb->interview_id] = $fb;
                    }
                }
            }

            foreach ($data as &$row) {
                $jobId = $row['job_id'] ?? null;
                $row['client_title_name_enriched'] = $clientMap[$jobId] ?? ($row['client_name'] ?? $row['company_name'] ?? '');
                $intId = $row['id'] ?? null;
                $row['latest_feedback_enriched'] = $feedbackMap[$intId] ?? null;
            }
            unset($row);
        }

        $stats = $this->dashboardStats($company_id, $chkAdminRole, $page);

        $responseData = [
            'status' => 'success',
            'data' => $data,
            'total' => (int) $total,
            'current_page' => (int) $page,
            'per_page' => (int) $size,
            'last_page' => (int) ceil($total / max($size, 1)),
            'stats' => $stats,
        ];

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_INTERVIEW_BENCH_CANDIDATES_TIME));

        return response()->json($responseData);
    }

    public function benchinterviewdata(Request $request)
    {
        // dd("hidid");
        abort_if(Gate::denies('view_schedule'), 403);

        $company_id = auth()->user()->company_id;
        $available_status = $feedback_status = $interview_mode_arr = '';

        $available_status = GeneralMethod::available_status();
        $feedback_status = GeneralMethod::feedback_status();
        $interview_mode_arr = GeneralMethod::interview_mode();
        $job_status_arr = GeneralMethod::job_status();

        $chkAdminRole = $this->user->hasRole('admin');

        if (($chkAdminRole > 0) && (in_array('all_requisition', $this->userPermissions) && in_array('view_jobs', $this->userPermissions))) {
            // dd("ki");
            $interviewShedule = DB::table('interview_schedules')
                ->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
                ->join('add_job_wise_condidates', 'interview_schedules.interview_job_wise_con_id', 'add_job_wise_condidates.id')
                ->join('jobs', function ($join) {
                    $join->on('jobs.id', 'add_job_wise_condidates.job_id');
                })
                ->join('companies', function ($join) {
                    $join->on('companies.id', 'jobs.company_id');
                })
                ->leftjoin('users', 'users.id', 'job_applications.submitted_by')
                ->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id')
                // ->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
                // ->leftjoin('companies', 'companies.id', 'jobs.company_id')
                ->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')
                ->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
                ->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
                ->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage')
                ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
                ->leftJoin('updatetimeline', 'updatetimeline.interview_schedules_id', 'interview_schedules.id') // Append new code
                ->select(
                    'interview_schedules.id',
                    'job_applications.full_name',
                    'interview_schedules.status',
                    'interview_schedules.schedule_date',
                    'interview_schedules.available_status',
                    'interview_schedules.reschedule',
                    'interview_schedules.feedback',
                    'interview_schedules.interview_mode',
                    'interview_schedules.interviewers',
                    'interview_schedules.interview_type',
                    'interview_schedules.schedule_from_time',
                    'interview_schedules.company_id as real_company_id',
                    'users.name as source',
                    'users.name as posted_by',
                    'users.image as posted_by_image',
                    'interview_schedules.job_application_id',
                    'master_status.name as master_status_name',
                    'candidate_status.name as child_status_name',
                    'jobs.title as job_title',
                    'interview_schedules.interview_job_id as job_id',
                    'companies.company_name',
                    'job_applications.current_designation',
                    'job_applications.thirdparty_company_id',
                    'interview_mode.name as mode_name',
                    'interview_mode_lavel.name as mode_lavel_name',
                    'master_status.css_style',
                    'interview_schedules.average_star_rating',
                    'jobs.status as job_status',
                    'recommendation_relation.id as recommendation_id',
                    'recommendation_relation.from_company_id',
                    'recommendation_relation.final_status',
                    'recommendation_relation.to_company_id',
                    'add_job_wise_condidates.id as wiseID',
                    'uc.company_name as uc_company_name',
                    'updatetimeline.interview_mention_link'
                );

            $interviewShedule = $interviewShedule->where('job_applications.company_id', '=', $company_id);
            $interviewShedule = $interviewShedule->where('job_applications.condidate_type', '=', 'bench_condidate');

            $searchTerms = request('cus_search');
            $custom_search = json_decode($searchTerms, true);
            if (\request('cus_search') != '') {

                $interviewShedule = $interviewShedule->where(function ($query) use ($custom_search) {
                    $query->whereIn('job_applications.full_name', $custom_search);
                    $query->orWhereIn('job_applications.phone', $custom_search);
                    $query->orWhereIn('job_applications.email', $custom_search);
                });
            }

            if (\request('cus_search') != '') {
                $interviewShedule = $interviewShedule->orWhere(function ($query) use ($company_id, $custom_search) {

                    return
                        $query->where('recommendation_relation.from_company_id', '=', $company_id)
                            ->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)
                            ->whereIn('job_applications.full_name', $custom_search)
                            ->orWhereIn('job_applications.phone', $custom_search)
                            ->orWhereIn('job_applications.email', $custom_search);
                });

            }

            // 			if (\request('search') != "") {
            // 				$search = request('search');
            // 				$interviewShedule = $interviewShedule->where('job_applications.full_name', 'LIKE', "%$search%");
            // 			}

            // 			if (\request('search') != "") {
            // 				$search = request('search');
            // 				$interviewShedule = $interviewShedule->orWhere(function ($query) use ($company_id, $search) {
            // 					return
            // 						$query->where('recommendation_relation.from_company_id', '=', $company_id)
            // 							->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)
            // 							->where('job_applications.full_name', 'LIKE', "%$search%");

            // 				});
            // 			}

        } else {

            $interviewShedule = DB::table('interview_schedules')
                ->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
                ->join('add_job_wise_condidates', 'interview_schedules.interview_job_wise_con_id', 'add_job_wise_condidates.id')
                // ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
                ->join('jobs', function ($join) {
                    $join->on('jobs.id', 'add_job_wise_condidates.job_id');
                })
                ->join('companies', function ($join) {
                    $join->on('companies.id', 'jobs.company_id');
                })
                ->join('users', 'users.id', 'job_applications.submitted_by')
                ->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id')
                // ->join('jobs', 'jobs.id', 'job_applications.job_id')
                ->join('job_assign', 'job_assign.job_id', 'jobs.id')
                // ->leftjoin('companies', 'companies.id', 'jobs.company_id')
                ->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')
                ->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
                ->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
                ->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage')
                ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
                ->leftJoin('updatetimeline', 'updatetimeline.interview_schedules_id', 'interview_schedules.id') // Append new code
                ->select(
                    'interview_schedules.id',
                    'job_applications.full_name',
                    'interview_schedules.status',
                    'interview_schedules.schedule_date',
                    'interview_schedules.available_status',
                    'interview_schedules.reschedule',
                    'interview_schedules.feedback',
                    'interview_schedules.interview_mode',
                    'interview_schedules.interviewers',
                    'interview_schedules.interview_type',
                    'interview_schedules.schedule_from_time',
                    'interview_schedules.company_id as real_company_id',
                    'users.name as source',
                    'users.name as posted_by',
                    'users.image as posted_by_image',
                    'interview_schedules.job_application_id',
                    'master_status.name as master_status_name',
                    'candidate_status.name as child_status_name',
                    'jobs.title as job_title',
                    'jobs.company_id as companyIdCheck',
                    'interview_schedules.interview_job_id as job_id',
                    'companies.company_name',
                    'job_applications.current_designation',
                    'job_applications.thirdparty_company_id',
                    'interview_mode.name as mode_name',
                    'interview_mode_lavel.name as mode_lavel_name',
                    'master_status.css_style',
                    'interview_schedules.average_star_rating',
                    'jobs.status as job_status',
                    'recommendation_relation.id as recommendation_id',
                    'recommendation_relation.from_company_id',
                    'recommendation_relation.final_status',
                    'recommendation_relation.to_company_id',
                    'add_job_wise_condidates.id as wiseID',
                    'uc.company_name as uc_company_name',
                    'updatetimeline.interview_mention_link'
                );

            // $interviewShedule = $interviewShedule->where('jobs.company_id', '=', $company_id)->where('job_assign.user_id', $this->user->id);
            $interviewShedule = $interviewShedule->where('job_applications.condidate_type', '=', 'bench_condidate');
            // 			if (\request('search') != "") {
            // 				$search = request('search');
            // 				$interviewShedule = $interviewShedule->where('job_applications.full_name', 'LIKE', "%$search%")->where('job_assign.user_id', $this->user->id);
            // 			}
            $searchTerms = request('cus_search');
            $custom_search = json_decode($searchTerms, true);
            if (\request('cus_search') != '') {

                $interviewShedule = $interviewShedule->where(function ($query) use ($custom_search) {
                    $query->whereIn('job_applications.full_name', $custom_search);
                    $query->orWhereIn('job_applications.phone', $custom_search);
                    $query->orWhereIn('job_applications.email', $custom_search);
                    $query->where('job_assign.user_id', $this->user->id);
                });
            }

            if (\request('cus_search') != '') {
                $interviewShedule = $interviewShedule->orWhere(function ($query) use ($company_id, $custom_search) {

                    return
                        $query->where('recommendation_relation.from_company_id', '=', $company_id)
                            ->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)
                            ->where('job_assign.user_id', $this->user->id)
                            ->whereIn('job_applications.full_name', $custom_search)
                            ->orWhereIn('job_applications.phone', $custom_search)
                            ->orWhereIn('job_applications.email', $custom_search);
                });

            }

        }
        // $interviewShedule = $interviewShedule->where('job_applications.submitted_by', '=', auth()->user()->id);
        $interviewShedule = $interviewShedule->where('job_applications.company_id', '=', auth()->user()->company_id);
        $interviewShedule->groupBy('interview_schedules.id');

        if (\request('sortingfilter') != '') {
            $valCheck = request('sortingfilter');
            if ($valCheck == 1) {
                $interviewShedule = $interviewShedule->orderBy('interview_schedules.created_at', 'DESC');
            } elseif ($valCheck == 2) {
                $interviewShedule = $interviewShedule->orderBy('interview_schedules.created_at', 'ASC');
            } elseif ($valCheck == 3) {
                $interviewShedule = $interviewShedule->orderBy('interview_schedules.schedule_date', 'ASC');
            } elseif ($valCheck == 4) {
                $interviewShedule = $interviewShedule->orderBy('interview_schedules.schedule_date', 'DESC');
            } elseif ($valCheck == 5) {
                $interviewShedule = $interviewShedule->orderBy('job_applications.np_id', 'ASC');
            } elseif ($valCheck == 6) {
                $interviewShedule = $interviewShedule->orderBy('job_applications.np_id', 'DESC');
            } elseif ($valCheck == 9) {
                $interviewShedule = $interviewShedule->orderByRaw("FIELD(job_applications.np_id, 'Immediate') DESC")
                    ->orderBy('job_applications.np_id', 'DESC')
                    ->get();
            } elseif ($valCheck == 7) {
                $interviewShedule = $interviewShedule->orderBy('job_applications.total_exp', 'DESC');
            } elseif ($valCheck == 8) {
                $interviewShedule = $interviewShedule->orderBy('job_applications.total_exp', 'ASC');
            }

        } else {
            $interviewShedule->orderBy('interview_schedules.schedule_date', 'DESC');
        }

        $interviewShedule->get();

        return DataTables::of($interviewShedule)

            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
            })
            ->editColumn('Name', function ($row) use ($company_id) {

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

                $profile_detail = 'show-application-detail';
                if ($row->thirdparty_company_id == $company_id) {
                    $profile_detail = 'client-show-application-detail';
                }

                return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="'.$row->wiseID.'" class="'.$profile_detail.'">
                      <div data-row-id="'.$row->wiseID.'"  class="symbol-label '.$bgTextDecore.' border-radius-5 '.$profile_detail.'">'.ucwords($strVal).'</div>
                    </a>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->wiseID.'" class="text-dark fw-bolder text-hover-primary fs-6 '.$profile_detail.'" data-toggle="tooltip" title="'.ucwords(@$row->full_name).'">'.ucwords(substr(@$row->full_name, 0, 20)).'</a>
                    <a href="javascript:void(0);" data-row-id="'.$row->wiseID.'" class="fw-bold text-muted d-block fs-7 '.$profile_detail.' text-hover-primary" data-toggle="tooltip" title="'.ucwords(@$row->current_designation).'">'.ucwords(substr(@$row->current_designation, 0, 20)).'</a>
                  </div>';
            })
            ->editColumn('Requisitions', function ($row) use ($company_id) {

                $icon = '&nbsp;<i class="fas fa-hand-scissors" style="color:#00CC00"></i>';
                if ($row->thirdparty_company_id == $company_id) {
                    $icon = '&nbsp;<i class="fas fa-link" style="color: #0000CC"></i></a>';
                }

                $client_name = '';
                $client_title_name = '';

                if (@$row->companyIdCheck == auth()->user()->company_id) {
                    $client_name_com = GeneralMethod::companyDetailsByClient($row->job_id);
                    if (! empty($client_name_com)) {
                        $client_title_name = $client_name_com;
                        $client_name = ucwords(substr($client_title_name, 0, 15));
                    } else {
                        $client_title_name = $row->client_name;
                        $client_name = ucwords(substr($client_title_name, 0, 15));
                    }
                } else {
                    $client_title_name = $row->company_name;
                    $client_name = ucwords(substr($client_title_name, 0, 15));
                }

                return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.ucwords($row->job_title).'">'.ucwords(substr($row->job_title, 0, 10)).'</a>
                    <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords($client_title_name).'">'.ucwords(substr($client_name, 0, 10)).'
                  </div>';
            })
            ->editColumn('Status', function ($row) use ($job_status_arr) {

                $status = $active = $hold = $inactive = $class = $recommendation_id = '';

                $job_status = $row->job_status;

                $status = ucwords($job_status);
                $class = '';
                // $class			=	$job_status_arr['active'];
                $class = $job_status_arr[$job_status];
                $recommendation_id = $row->recommendation_id;

                if ($status == 'Inactive') {
                    $status = 'Closed';
                }

                $final_status = '';
                if ($row->final_status == 2 && @$row->to_company_id == auth()->user()->company_id) {
                    $status = 'REMOVED';
                    $class = 'closed-bg';
                }

                $ulink = '<a href="javascript:void(0);" class="d-flex flex-column '.@$class.'  text-hover-success" >'.$status.'</a>';

                $recommendation_text = 'Client';
                if (@$row->companyIdCheck == auth()->user()->company_id) {
                    $recommendation_text = 'Custom';
                    $ulink = '<a href="javascript:void(0);" class="fs-6 '.@$class.' text-hover-success">'.$status.'</a>';
                }

                return '<div class="d-flex flex-column">
                       '.$ulink.'
                      <span class="fw-bold text-muted d-block fs-7">R'.@$row->job_id.' -'.$recommendation_text.'</span>
                    </div>';
            })
            ->editColumn('InterviewMode', function ($row) {
                $interLink = '';
                if ($row->interview_mode == 2) {

                    $schedule_date = $row->schedule_date;
                    $current_date = date('Y-m-d H:i:s');
                    $schedule_timestamp = strtotime($schedule_date);
                    $current_timestamp = strtotime($current_date);

                    $url = $row->interview_mention_link;
                    $urlComponents = parse_url($url);

                    if ($schedule_timestamp >= $current_timestamp) {
                        if ($urlComponents === false) {
                            $interLink = '<a href="javascript:;" class="d-flex flex-column interview_mention_link">Interview Link</a>';
                        }

                        $scheme = $urlComponents['scheme'] ?? '';

                        if ($scheme === 'http' || $scheme === 'https') {
                            $interLink = '<a href="'.$row->interview_mention_link.'" class="d-flex flex-column" target="_blank">Interview Link</a>';
                        } else {
                            $interLink = '<a href="javascript:;" class="d-flex flex-column interview_mention_link">Interview Link</a>';
                        }
                    }

                } else {
                    $interLink = '';
                }

                return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.ucwords(substr($row->mode_name, 0, 10)).'</a> 
                    <!--<span class="text-muted fw-bold d-block fs-7">'.ucwords(substr($row->mode_lavel_name, 0, 10)).'</span>--> 
                  </div>'.$interLink;
            })
            ->editColumn('Interviewers', function ($row) {

                $contactHtml = $real_company_id = $interviewers = '';
                $interviewers = $row->interviewers;
                $real_company_id = $row->real_company_id;

                $contactHtml = '<div class="d-flex flex-column">
                    							<div class="symbol-group symbol-hover">';
                if (! empty($interviewers)) {
                    $interviewersArr = explode(' , ', trim($interviewers));
                    $interviewersData = DB::table('users')
                        ->select('users.id', 'users.name', 'users.image')
                        ->whereIn('users.name', $interviewersArr)
                        ->where('users.company_id', '=', $real_company_id)
                        ->get();
                    if (! empty($interviewersData)) {
                        foreach ($interviewersData as $intVal) {
                            $bgTextDecore = GeneralMethod::icoinText(@$intVal->name);
                            $image_path = '';
                            if ($intVal->image != '') {
                                $image_path = $intVal->image;
                            }
                            if (! empty($image_path)) {
                                $contactHtml .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip1" data-html="true" title="'.ucwords(@$intVal->name).'" >
								<img alt="Pic" src="'.$image_path.'">
								</div>';
                            } else {
                                $contactHtml .=
                                '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip" data-html="true" title="'.ucwords(@$intVal->name).'" >
								<div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100" style="float:left">
								<a href="javascript:void(0);" data-row-id="11" class="show-detail border-radius-100" id="kt_activities3_toggle">
									<div class="symbol-label fs-8 fs-8 '.$bgTextDecore.' border-radius-100">'.substr($intVal->name, 0, 1).'</div>
								</a>		 
								</div>
								</div>';
                            }

                        }
                    }
                }
                $contactHtml .= '</div>
                    			</div>';

                return $contactHtml;

            })
            ->editColumn('Source', function ($row) {
                $bgTextDecore = GeneralMethod::icoinText(@$row->posted_by);
                $posted_by_image = '';
                if (! empty($row->posted_by_image)) {
                    $posted_by_image = @$row->posted_by_image;
                }

                // if(!empty($posted_by_image)){
                // return '<div class="d-flex flex-column">
                // 			<div class="symbol-group symbol-hover">
                // 			  <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="' . @$row->posted_by . '">
                // 				<img alt="Pic" src="' . @$posted_by_image . '">
                // 			  </div>
                // 		  </div>
                // 		  <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="' . @$row->uc_company_name . '">' . substr(@$row->uc_company_name, 0, 10) . '</span>';
                // 		}else{
                return '<div class="d-flex flex-column">
									<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="" data-bs-original-title="'.@$row->posted_by.'">'.substr($row->posted_by, 0, 10).'</a>
								  </div>
								  <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="'.@$row->company_name.'">'.substr(@$row->company_name, 0, 10).'</span>';

                // }
            })
            ->editColumn('InterviewDate', function ($row) {
                return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.Carbon::parse($row->schedule_date)->format('d M, Y').'</a>
                    <span class="text-muted fw-bold d-block fs-7">'.Carbon::parse($row->schedule_date)->format('H:i a').'</span>
                  </div>';
            })
            ->editColumn('Feedback', function ($row) use ($feedback_status, $available_status, $company_id) {

                $available_status_name = '';
                if ($row->available_status > 0) {
                    $available_status_name = @$available_status[@$row->available_status]['name'];
                } else {
                    // $available_status_name	=	@$available_status[6]['name'];
                }

                $feedback = ucwords(substr(@$feedback_status[@$row->feedback]['name'], 0, 20));
                $customHtml = 'show-feedback_no';

                if ($row->thirdparty_company_id == $company_id) {
                    $customHtml = '';
                    if (empty($feedback)) {
                        $feedback = '<span class=""> Pending Feedback</span>';
                    }
                } else {
                    if (empty($feedback)) {
                        $feedback = '<span class="">-----</span>';
                        $available_status_name = '';
                    }
                }

                return '<div class="d-flex flex-column">
				<a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 '.$customHtml.'">'.@$feedback.'</a>
				<span class="text-muted fw-bold text-muted d-block fs-7">'.ucwords(substr(@$available_status_name, 0, 20)).'</span>
				</div>';

            })
            ->editColumn('Rating', function ($row) {
                $imagePath = $text = '';
                $average_star_rating = '';
                $average_star_rating = $row->average_star_rating;
                if ($average_star_rating == 1) {
                    $imagePath = url('public/img/star/1.png');
                    $text = 'Bad';
                } elseif ($average_star_rating == 2) {
                    $imagePath = url('public/img/star/2.png');
                    $text = 'Poor';
                } elseif ($average_star_rating == 3) {
                    $imagePath = url('public/img/star/3.png');
                    $text = 'Average';
                } elseif ($average_star_rating == 4) {
                    $imagePath = url('public/img/star/4.png');
                    $text = 'Best';
                } elseif ($average_star_rating == 5) {
                    $imagePath = url('public/img/star/5.png');
                    $text = 'Very Best';
                } else {
                    $imagePath = url('public/img/star/5-blank.png');
                    $text = 'Blank';
                }

                return ' <img src="'.@$imagePath.'" alt="image" style="width: 5.5rem">
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.@$text.'</span>';
            })
            ->editColumn('Stage', function ($row) {
                return '<div class="d-flex flex-column"> 
                        <span class="badge fw-bolder fs-6 px-2 py-1 '.@$row->css_style.'">'.substr(@$row->master_status_name, 0, 10).'</span>
                        <span class="text-muted fw-bold text-muted d-block fs-7">'.substr(@$row->child_status_name, 0, 10).'</span>
                    </div>';
            })

            ->rawColumns(['chkbox', 'Name', 'Requisitions', 'Status', 'InterviewMode', 'Interviewers', 'Source', 'InterviewDate', 'Feedback', 'Rating', 'Stage'])

            ->make(true);
    }

    public function sub_status_tab(Request $request)
    {
        $interview_status_id = (int) $request->id;
        $feedback = (int) $request->feedback;
        $interview_sub_status = DB::table('interview_sub_status')
            ->where('interview_sub_status.interview_status_id', '=', $interview_status_id)
            ->where('interview_sub_status.status', 1)
            ->get();
        $html_sub_status = '';
        if (count($interview_sub_status) > 0) {
            $html_sub_status .= '<label class="text-dark fw-bolder fs-5">Interview status</label>
            <div class="row py-5 mb-3">';
            foreach ($interview_sub_status as $val_status) {
                $sel = '';
                if ($feedback == $val_status->id) {
                    $sel = 'checked="checked"';
                }
                $charCount = strlen($val_status->name);
                $divsection = 2;
                if ($charCount > 18) {
                    $divsection = 3;
                }

                //   dd($charCount);
                //   dd($val_status->name);
                $html_sub_status .= '<div class="col-lg-'.$divsection.'">
						<input type="radio" class="btn-check" name="app_int_feedback_status" id="app_int_feedback_status_'.$val_status->id.'" value="'.$val_status->id.'" '.@$sel.' >
						<label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_feedback_status_'.$val_status->id.'"> 
						<span class="d-block fw-bold text-start"> <span class="text-dark-800 fw-bolder d-block fs-6">'.ucwords($val_status->name).' </span> </span> 
						</label>
				</div>';

            }
            $html_sub_status .= '</div>';

        } else {
            $html_sub_status = '<input type="radio" class="btn-check" name="app_int_feedback_status" id="app_int_feedback_status_0" value="0" checked="checked">';
        }

        return Reply::dataOnly(['status' => 'success', 'view' => $html_sub_status]);
    }

    public function showfeedback($id)
    {
        $this->ensureFeedbackSchema();

        $this->interviewSchedule = $available_status = $feedback_status = '';
        //  dd($id);
        $this->interviewSchedule = DB::table('interview_schedules')
            ->select(
                'interview_schedules.id as interview_schedules_id',
                'interview_schedules.schedule_date',
                'interview_schedules.interview_job_id',
                'interview_schedules.job_application_id',
                'interview_schedules.interview_job_wise_con_id as wise_con_id',
                'job_applications.id',
                'job_applications.full_name',
                'job_applications.current_designation',
                'jobs.title as job_title',
                'clients.name as client_name',
                'interview_mode.name as mode_name',
                'interview_mode_lavel.name as mode_lavel_name',

                'master_status.name as master_status_name',
                'master_status.css_style',
                'candidate_status.name as child_status_name',
                'interview_schedules.available_status',
                'interview_schedules.feedback',
                'interview_schedules.interviewers',
                'interview_schedules.company_id as real_company_id',
                'users.name as upload_by_name',
                'companies.company_name',
                'users.image'

            )
            ->join('job_applications', 'job_applications.id', '=', 'interview_schedules.job_application_id')
            ->join('add_job_wise_condidates', 'interview_schedules.interview_job_wise_con_id', 'add_job_wise_condidates.id')
            // ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', '=', 'job_applications.id')
            ->leftjoin('master_status', function ($join) {
                $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
            })
            ->leftjoin('jobs', function ($join) {
                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
            })
            ->join('users', 'users.id', '=', 'job_applications.submitted_by')
            // ->leftjoin('users', 'users.id', 'job_applications.submitted_by')
            // ->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
            ->leftjoin('clients', 'clients.id', '=', 'jobs.client_id')
            ->leftjoin('companies', 'companies.id', 'users.company_id')
            // ->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
            ->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
            ->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
            ->leftjoin('candidate_status', 'candidate_status.id', 'job_applications.child_status_id')
            ->where('interview_schedules.id', $id)
            ->first();

        $job_application_id = $this->interviewSchedule->job_application_id;

        $this->jobSkill = DB::table('job_metronics_skills')
            ->leftJoin('candidate_job_skill_exp', function ($join) use ($job_application_id) {
                $join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
                    ->where('candidate_job_skill_exp.candidate_id', '=', $job_application_id); // Add the candidate condition
            })
            ->select('job_metronics_skills.*', 'candidate_job_skill_exp.skill_value')
            ->where('job_metronics_skills.job_id', '=', $this->interviewSchedule->interview_job_id)
            ->get();

        $this->available_status = GeneralMethod::available_status();
        $this->feedback_status = GeneralMethod::feedback_status();
        $this->interview_status = GeneralMethod::interview_status();

        $feedbackdata = DB::table('interview_skills_feedback as isf')
            ->leftJoin('interview_feedback as ifb', function ($join) {
                $join->on('isf.feedback_id', '=', 'ifb.id');
            })
            ->leftJoin('users', function ($join) {
                $join->on('isf.update_by', '=', 'users.id');
            })
            ->leftJoin('users_profile', function ($join) {
                $join->on('users_profile.user_id', '=', 'users.id');
            })
            ->where('isf.interview_id', $id)
            ->orderByDesc('isf.feedback_id')
            ->select(
                'isf.feedback_id',
                'isf.skills',
                'users.name as by_name',
                'users_profile.designation',
                'isf.skills_exp',
                'isf.skills_ratings',
                'isf.skills_remarks',
                'isf.update_by',
                'ifb.available_status as candidateStatus',
                'ifb.feedback_id as interviewStatus',
                'ifb.feedback_name',
                'ifb.message',
                'ifb.created_at',
                'ifb.star_ratings',
                ...$this->ifbOptionalCols()
            )
            ->get()
            ->groupBy('feedback_id'); // Group records by feedback_id

        $this->feedbackMapped = $feedbackdata->map(function ($feedbacks) {
            return [
                'feedback_id' => $feedbacks->first()->feedback_id,
                'feedback_name' => $feedbacks->first()->feedback_name,
                'by_name' => $feedbacks->first()->by_name,
                'designation' => $feedbacks->first()->designation,
                'created_at' => $feedbacks->first()->created_at,
                'message' => $feedbacks->first()->message,
                'star_ratings' => $feedbacks->first()->star_ratings,
                'candidateStatus' => $feedbacks->first()->candidateStatus,
                'interviewStatus' => $feedbacks->first()->interviewStatus,
                'interview_result' => $feedbacks->first()->interview_result ?? null,
                'overall_recommendation' => $feedbacks->first()->overall_recommendation ?? null,
                'current_ctc' => $feedbacks->first()->current_ctc ?? null,
                'expected_ctc' => $feedbacks->first()->expected_ctc ?? null,
                'offered_ctc' => $feedbacks->first()->offered_ctc ?? null,
                'max_budget' => $feedbacks->first()->max_budget ?? null,
                'notice_period' => $feedbacks->first()->notice_period ?? null,
                'joining_date' => $feedbacks->first()->joining_date ?? null,
                'compensation_fit' => $feedbacks->first()->compensation_fit ?? null,
                'joining_probability' => $feedbacks->first()->joining_probability ?? null,
                'risk_factors' => $feedbacks->first()->risk_factors ?? null,
                'compensation_comments' => $feedbacks->first()->compensation_comments ?? null,
                'feedbackdata' => $feedbacks->map(function ($feedback) {
                    return [
                        'skills' => $feedback->skills,
                        'experience' => $feedback->skills_exp,
                        'ratings' => $feedback->skills_ratings,
                        'remarks' => $feedback->skills_remarks,
                    ];
                })->values()->all(),
            ];
        });

        $download_show = (count($this->feedbackMapped));

        // Identify the logged-in user's own feedback (by user id, not name) so the blade
        // can let them edit only their own and view everyone else's read-only.
        $this->myFeedbackId = 0;
        foreach ($feedbackdata as $fid => $rows) {
            if ((int) optional($rows->first())->update_by === (int) auth()->id()) {
                $this->myFeedbackId = $fid;
                break;
            }
        }

        // Only the CURRENT job captain (SPOC) and the listed interviewers may give/edit feedback.
        // Everyone else (recruiters, previous captains, other accounts) is view-only.
        $this->canGiveFeedback = $this->canGiveInterviewFeedback($this->interviewSchedule, $id);

        // Salary View permission: roles without it cannot view/edit the Compensation Assessment.
        $this->canViewSalary = $this->userHasSalaryView();

        // dd($this->canViewSalary);

        // Pass the current captain (name + designation + current company) so the summary lists the
        // right feedback-giver (captain + interviewers), not the recruiter who scheduled the interview.
        $this->captainUserId = $this->jobCaptainId($this->interviewSchedule);
        $this->captainName = '';
        $this->captainDesignation = '';
        $this->captainCompany = '';
        if ($this->captainUserId) {
            try {
                $cap = DB::table('users')
                    ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
                    ->leftJoin('companies', 'companies.id', '=', 'users.company_id')
                    ->where('users.id', $this->captainUserId)
                    ->select('users.name', 'users_profile.designation', 'companies.company_name')
                    ->first();
                if ($cap) {
                    $this->captainName = $cap->name ?? '';
                    $this->captainDesignation = $cap->designation ?? '';
                    $this->captainCompany = $cap->company_name ?? '';
                }
            } catch (\Throwable $e) {
            }
        }

        $view = view('service_company.interview-schedule.showfeedback', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view, 'download_show' => $download_show]);
    }

    public function skillshowfeedback($id)
    {

        $feedbackdata = DB::table('interview_skills_feedback as isf')
            ->leftJoin('interview_feedback as ifb', function ($join) {
                $join->on('isf.feedback_id', '=', 'ifb.id');
            })
            ->leftJoin('users', function ($join) {
                $join->on('isf.update_by', '=', 'users.id');
            })
            ->leftJoin('users_profile', function ($join) {
                $join->on('users_profile.user_id', '=', 'users.id');
            })
            ->where('isf.interview_id', $id)
            ->orderByDesc('isf.feedback_id')
            ->select(
                'isf.feedback_id',
                'isf.skills',
                'users.name as by_name',
                'users_profile.designation',
                'isf.skills_exp',
                'isf.skills_ratings',
                'isf.skills_remarks',
                'ifb.feedback_name',
                'ifb.available_status as candidateStatus',
                'ifb.feedback_id as interviewStatus',
                'ifb.message',
                'ifb.created_at',
                'ifb.star_ratings',
                ...$this->ifbOptionalCols()
            )
            ->get()
            ->groupBy('feedback_id'); // Group records by feedback_id

        $this->feedbackMapped = $feedbackdata->map(function ($feedbacks) {
            return [
                'feedback_id' => $feedbacks->first()->feedback_id,
                'feedback_name' => $feedbacks->first()->feedback_name,
                'by_name' => $feedbacks->first()->by_name,
                'designation' => $feedbacks->first()->designation,
                'created_at' => $feedbacks->first()->created_at,
                'message' => $feedbacks->first()->message,
                'star_ratings' => $feedbacks->first()->star_ratings,
                'candidateStatus' => $feedbacks->first()->candidateStatus,
                'interviewStatus' => $feedbacks->first()->interviewStatus,
                'interview_result' => $feedbacks->first()->interview_result ?? null,
                'overall_recommendation' => $feedbacks->first()->overall_recommendation ?? null,
                'current_ctc' => $feedbacks->first()->current_ctc ?? null,
                'expected_ctc' => $feedbacks->first()->expected_ctc ?? null,
                'offered_ctc' => $feedbacks->first()->offered_ctc ?? null,
                'max_budget' => $feedbacks->first()->max_budget ?? null,
                'notice_period' => $feedbacks->first()->notice_period ?? null,
                'joining_date' => $feedbacks->first()->joining_date ?? null,
                'compensation_fit' => $feedbacks->first()->compensation_fit ?? null,
                'joining_probability' => $feedbacks->first()->joining_probability ?? null,
                'risk_factors' => $feedbacks->first()->risk_factors ?? null,
                'compensation_comments' => $feedbacks->first()->compensation_comments ?? null,
                'feedbackdata' => $feedbacks->map(function ($feedback) {
                    return [
                        'skills' => $feedback->skills,
                        'experience' => $feedback->skills_exp,
                        'ratings' => $feedback->skills_ratings,
                        'remarks' => $feedback->skills_remarks,
                    ];
                })->values()->all(),
            ];
        });

        // dd($this->feedbackMapped);

        // $feedbackdata = DB::table('interview_skills_feedback')
        // ->where('interview_id', $id)->orderByDesc('feedback_id')->get()
        // ->groupBy('feedback_id'); // Group records by feedback_id

        // $this->feedbackMapped = $feedbackdata->map(function ($feedbacks) {
        // 	return $feedbacks->map(function ($feedback) {
        // 		return [
        // 			'skills' => $feedback->skills,
        // 			'experience' => $feedback->skills_exp,
        // 			'ratings' => $feedback->skills_ratings,
        // 			'remarks' => $feedback->skills_remarks,
        // 		];
        // 	});
        // });

        // dd($this->feedbackMapped);

        $this->interviewSchedule = $available_status = $feedback_status = '';
        //  dd($id);
        $this->interviewSchedule = DB::table('interview_schedules')
            ->select(
                'interview_schedules.id as interview_schedules_id',
                'interview_schedules.schedule_date',
                'interview_schedules.interview_job_id',
                'interview_schedules.job_application_id',
                'job_applications.id',
                'job_applications.full_name',
                'job_applications.current_designation',
                'jobs.title as job_title',
                'clients.name as client_name',
                'interview_mode.name as mode_name',
                'interview_mode_lavel.name as mode_lavel_name',

                'master_status.name as master_status_name',
                'master_status.css_style',
                'candidate_status.name as child_status_name',
                'interview_schedules.available_status',
                'interview_schedules.feedback',
                'interview_schedules.interviewers',
                'interview_schedules.company_id as real_company_id',
                'users.name as upload_by_name',
                'companies.company_name',
                'users.image'

            )
            ->join('job_applications', 'job_applications.id', '=', 'interview_schedules.job_application_id')
            ->join('add_job_wise_condidates', 'interview_schedules.interview_job_wise_con_id', 'add_job_wise_condidates.id')
            // ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', '=', 'job_applications.id')
            ->leftjoin('master_status', function ($join) {
                $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
            })
            ->leftjoin('jobs', function ($join) {
                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
            })
            ->join('users', 'users.id', '=', 'job_applications.submitted_by')
            // ->leftjoin('users', 'users.id', 'job_applications.submitted_by')
            // ->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
            ->leftjoin('clients', 'clients.id', '=', 'jobs.client_id')
            ->leftjoin('companies', 'companies.id', 'users.company_id')
            // ->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
            ->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
            ->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
            ->leftjoin('candidate_status', 'candidate_status.id', 'job_applications.child_status_id')
            ->where('interview_schedules.id', $id)
            ->first();

        $job_application_id = $this->interviewSchedule->job_application_id;

        $this->jobSkill = DB::table('job_metronics_skills')
            ->leftJoin('candidate_job_skill_exp', function ($join) use ($job_application_id) {
                $join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
                    ->where('candidate_job_skill_exp.candidate_id', '=', $job_application_id); // Add the candidate condition
            })
            ->select('job_metronics_skills.*', 'candidate_job_skill_exp.skill_value')
            ->where('job_metronics_skills.job_id', '=', $this->interviewSchedule->interview_job_id)
            ->get();

        $this->available_status = GeneralMethod::available_status();
        $this->feedback_status = GeneralMethod::feedback_status();

        $view = view('service_company.interview-schedule.skillfeedback', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function skillshowfeedbackDownload($id, $feed = '')
    {

        $feedbackdata = DB::table('interview_skills_feedback as isf')
            ->leftJoin('interview_feedback as ifb', function ($join) {
                $join->on('isf.feedback_id', '=', 'ifb.id');
            })
            ->leftJoin('users', function ($join) {
                $join->on('isf.update_by', '=', 'users.id');
            })
            ->leftJoin('users_profile', function ($join) {
                $join->on('users_profile.user_id', '=', 'users.id');
            });
        if ($feed) {
            $feedbackdata = $feedbackdata->where('isf.feedback_id', $feed);
        } else {
            $feedbackdata = $feedbackdata->where('isf.interview_id', $id);
        }

        $feedbackdata = $feedbackdata->orderByDesc('isf.feedback_id')
            ->select(
                'isf.feedback_id',
                'isf.skills',
                'users.name as by_name',
                'users_profile.designation',
                'isf.skills_exp',
                'isf.skills_ratings',
                'isf.skills_remarks',
                'ifb.feedback_name',
                'ifb.available_status as candidateStatus',
                'ifb.feedback_id as interviewStatus',
                'ifb.message',
                'ifb.created_at',
                'ifb.star_ratings',
                ...$this->ifbOptionalCols()
            )
            ->get()
            ->groupBy('feedback_id'); // Group records by feedback_id
        // dd($feedbackdata);
        $this->feedbackMapped = $feedbackdata->map(function ($feedbacks) {
            return [
                'feedback_id' => $feedbacks->first()->feedback_id,
                'feedback_name' => $feedbacks->first()->feedback_name,
                'by_name' => $feedbacks->first()->by_name,
                'designation' => $feedbacks->first()->designation,
                'created_at' => $feedbacks->first()->created_at,
                'message' => $feedbacks->first()->message,
                'star_ratings' => $feedbacks->first()->star_ratings,
                'candidateStatus' => $feedbacks->first()->candidateStatus,
                'interviewStatus' => $feedbacks->first()->interviewStatus,
                'interview_result' => $feedbacks->first()->interview_result ?? null,
                'overall_recommendation' => $feedbacks->first()->overall_recommendation ?? null,
                'current_ctc' => $feedbacks->first()->current_ctc ?? null,
                'expected_ctc' => $feedbacks->first()->expected_ctc ?? null,
                'offered_ctc' => $feedbacks->first()->offered_ctc ?? null,
                'max_budget' => $feedbacks->first()->max_budget ?? null,
                'notice_period' => $feedbacks->first()->notice_period ?? null,
                'joining_date' => $feedbacks->first()->joining_date ?? null,
                'compensation_fit' => $feedbacks->first()->compensation_fit ?? null,
                'joining_probability' => $feedbacks->first()->joining_probability ?? null,
                'risk_factors' => $feedbacks->first()->risk_factors ?? null,
                'compensation_comments' => $feedbacks->first()->compensation_comments ?? null,
                'feedbackdata' => $feedbacks->map(function ($feedback) {
                    return [
                        'skills' => $feedback->skills,
                        'experience' => $feedback->skills_exp,
                        'ratings' => $feedback->skills_ratings,
                        'remarks' => $feedback->skills_remarks,
                    ];
                })->values()->all(),
            ];
        });
        // dd($this->feedbackMapped);

        $this->interviewSchedule = $available_status = $feedback_status = '';
        //  dd($id);
        $this->interviewSchedule = DB::table('interview_schedules')
            ->select(
                'interview_schedules.id as interview_schedules_id',
                'interview_schedules.schedule_date',
                'interview_schedules.interview_job_id',
                'interview_schedules.job_application_id',
                'job_applications.id',
                'job_applications.full_name',
                'job_applications.current_designation',
                'jobs.title as job_title',
                'clients.name as client_name',
                'interview_mode.name as mode_name',
                'interview_mode_lavel.name as mode_lavel_name',

                'master_status.name as master_status_name',
                'master_status.css_style',
                'candidate_status.name as child_status_name',
                'interview_schedules.available_status',
                'interview_schedules.feedback',
                'interview_schedules.interviewers',
                'interview_schedules.company_id as real_company_id',
                'users.name as upload_by_name',
                'companies.company_name',
                'users.image'

            )
            ->join('job_applications', 'job_applications.id', '=', 'interview_schedules.job_application_id')
            ->join('add_job_wise_condidates', 'interview_schedules.interview_job_wise_con_id', 'add_job_wise_condidates.id')
            // ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', '=', 'job_applications.id')
            ->leftjoin('master_status', function ($join) {
                $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
            })
            ->leftjoin('jobs', function ($join) {
                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
            })
            ->join('users', 'users.id', '=', 'job_applications.submitted_by')
            // ->leftjoin('users', 'users.id', 'job_applications.submitted_by')
            // ->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
            ->leftjoin('clients', 'clients.id', '=', 'jobs.client_id')
            ->leftjoin('companies', 'companies.id', 'users.company_id')
            // ->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
            ->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
            ->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
            ->leftjoin('candidate_status', 'candidate_status.id', 'job_applications.child_status_id')
            ->where('interview_schedules.id', $id)
            ->first();
        // dd($this->interviewSchedule);

        $job_application_id = $this->interviewSchedule->job_application_id;

        $this->jobSkill = DB::table('job_metronics_skills')
            ->leftJoin('candidate_job_skill_exp', function ($join) use ($job_application_id) {
                $join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
                    ->where('candidate_job_skill_exp.candidate_id', '=', $job_application_id); // Add the candidate condition
            })
            ->select('job_metronics_skills.*', 'candidate_job_skill_exp.skill_value')
            ->where('job_metronics_skills.job_id', '=', $this->interviewSchedule->interview_job_id)
            ->get();

        $this->available_status = GeneralMethod::available_status();
        $this->feedback_status = GeneralMethod::feedback_status();

        // Salary View permission also gates the Compensation block in the downloadable report
        $this->canViewSalary = $this->userHasSalaryView();

        $view = view('service_company.interview-schedule.download-feedback', $this->data)->render();

        // Ensure the dompdf font cache directory exists so the Poppins @font-face can be cached/embedded
        try {
            $fontDir = storage_path('fonts');
            if (! is_dir($fontDir)) {
                @mkdir($fontDir, 0775, true);
            }
        } catch (\Throwable $e) {
        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('service_company.interview-schedule.download-feedback', $this->data);
        // return view('service_company.interview-schedule.download-feedback', $this->data);
        // return $pdf->stream('feedback-preview.pdf');

        $fileName = 'feedback-download'.'.pdf';
        // dd($fileName);
        if (1) {
            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="'.$fileName.'"');   // attachment inline
        }

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function followupUpdate(Request $request)
    {
        $id = (int) $request->id;
        $status = $request->status;
        $allowed = ['confirmed', 'no-response', 'rescheduled'];
        if ($id <= 0 || ! in_array($status, $allowed, true)) {
            return Reply::dataOnly(['status' => 'error', 'msg' => 'Invalid follow-up status.']);
        } 		$cols = \Illuminate\Support\Facades\Schema::getColumnListing('interview_schedules');
        $update = ['followup_status' => $status];
        if (in_array('followup_status_at', $cols, true)) {
            $update['followup_status_at'] = now();
        } 		$update = array_intersect_key($update, array_flip($cols));
        if (! empty($update)) {
            DB::table('interview_schedules')->where('id', $id)->update($update);
        } 		GeneralMethod::cacheClean('allinterviewschedule_version_user_'.$this->user->id);

        return Reply::dataOnly(['status' => 'success', 'followup' => $status]);
    }

    public function sendInterviewLink(Request $request)
    {
        $id = (int) $request->id;
        $link = trim($request->link ?? '');
        if ($id <= 0 || $link === '') {
            return Reply::dataOnly(['status' => 'error', 'msg' => 'Invalid interview link.']);
        }
        $sched = DB::table('interview_schedules')
            ->join('job_applications', 'job_applications.id', '=', 'interview_schedules.job_application_id')
            ->where('interview_schedules.id', $id)
            ->select('job_applications.full_name', 'job_applications.email as candidate_email', 'interview_schedules.interviewers', 'interview_schedules.company_id', 'interview_schedules.schedule_date')
            ->first();
        if (! $sched) {
            return Reply::dataOnly(['status' => 'error', 'msg' => 'Interview not found.']);
        }
        $recipients = [];
        if (! empty($sched->candidate_email)) {
            $recipients[] = $sched->candidate_email;
        }
        if (! empty($sched->interviewers)) {
            $names = array_filter(array_map('trim', explode(',', $sched->interviewers)));
            if (! empty($names)) {
                $emails = DB::table('users')->whereIn('name', $names)->where('company_id', $sched->company_id)->pluck('email')->all();
                $recipients = array_merge($recipients, $emails);
            }
        }
        $recipients = array_values(array_unique(array_filter($recipients, function ($e) {
            return filter_var($e, FILTER_VALIDATE_EMAIL);
        })));
        if (empty($recipients)) {
            return Reply::dataOnly(['status' => 'error', 'msg' => 'No valid recipient emails found.']);
        }
        try {
            $base = GeneralMethod::baseCompany();
            $fromAddress = (! empty($base->companyEmail)) ? $base->companyEmail : config('mail.from.address');
            $fromName = (! empty($base->companyName)) ? $base->companyName : config('mail.from.name');
            $candidateName = ucwords($sched->full_name);
            $scheduleDate = date('d M, Y h:i A', strtotime($sched->schedule_date));
            $isAddress = (($request->type ?? 'link') === 'address');
            $label = $isAddress ? 'Interview Address' : 'Interview Link';
            $subject = $label.' - '.$candidateName;
            $body = "Hello,\n\nPlease find the interview details below for ".$candidateName.".\n\nScheduled: ".$scheduleDate."\n".$label.': '.$link."\n\nRegards,\n".$fromName;
            foreach ($recipients as $to) {
                \Illuminate\Support\Facades\Mail::raw($body, function ($msg) use ($to, $fromAddress, $fromName, $subject) {
                    $msg->to($to)->subject($subject)->from($fromAddress, $fromName);
                });
            }

            return Reply::dataOnly(['status' => 'success', 'count' => count($recipients)]);
        } catch (\Throwable $e) {
            return Reply::dataOnly(['status' => 'error', 'msg' => 'Email could not be sent.']);
        }
    }

    private function dashboardStats($company_id, $chkAdminRole, $page)
    {
        $stats = ['interview_month' => 0, 'today' => 0, 'upcoming' => 0, 'feedback_pending' => 0];
        // Stats are company-wide (not page-specific) so they must be returned on EVERY page,
        // otherwise the hero cards blank out when the user navigates past page 1.
        try {
            if (! $this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
                return $stats;
            }
            $nowC = \Carbon\Carbon::now();
            $statsMust = [
                ['bool' => ['should' => [
                    ['term' => ['job_company_id' => $company_id]],
                    ['bool' => ['must' => [['term' => ['ClientComID' => $company_id]], ['term' => ['candidate_type' => 'other']]]]],
                ], 'minimum_should_match' => 1]],
            ];
            if (! $chkAdminRole) {
                // Assigned to the job OR named as an interviewer on the schedule
                $statsShould = [['nested' => ['path' => 'job_assign', 'query' => ['term' => ['job_assign.user_id' => auth()->user()->id]]]]];
                $statsMyName = trim(auth()->user()->name ?? '');
                if ($statsMyName !== '') {
                    $statsShould[] = ['match_phrase' => ['interviewers' => $statsMyName]];
                }
                $statsMyEmail = strtolower(trim(auth()->user()->email ?? ''));
                if ($statsMyEmail !== '') {
                    $statsShould[] = ['match_phrase' => ['other_email' => $statsMyEmail]];
                }
                $statsMust[] = ['bool' => ['should' => $statsShould, 'minimum_should_match' => 1]];
            }
            $statsParams = [
                'index' => ELASTIC_INERTVIEW_TRACKER,
                'body' => [
                    'size' => 0,
                    'query' => ['bool' => ['must' => $statsMust]],
                    'aggs' => [
                        'this_month' => ['filter' => ['range' => ['schedule_date' => ['gte' => $nowC->copy()->startOfMonth()->toIso8601String(), 'lte' => $nowC->copy()->endOfMonth()->toIso8601String(), 'format' => 'strict_date_optional_time']]]],
                        'today' => ['filter' => ['range' => ['schedule_date' => ['gte' => $nowC->copy()->startOfDay()->toIso8601String(), 'lte' => $nowC->copy()->endOfDay()->toIso8601String(), 'format' => 'strict_date_optional_time']]]],
                        'upcoming' => ['filter' => ['range' => ['schedule_date' => ['gte' => $nowC->copy()->addDay()->startOfDay()->toIso8601String(), 'format' => 'strict_date_optional_time']]]],
                        // Feedback pending = the interview has happened but feedback is still missing:
                        //   - no attendance selected (available_status not set / 0), OR
                        //   - Interview Result was set to "Feedback Pending" (legacy feedback id 5).
                        // A real attendance (No Show / Rescheduled / Cancelled etc.) is NOT pending.
                        'feedback_pending' => ['filter' => ['bool' => ['must' => [['range' => ['schedule_date' => ['lte' => $nowC->copy()->subHour()->toIso8601String(), 'format' => 'strict_date_optional_time']]], ['bool' => ['should' => [['bool' => ['must_not' => ['exists' => ['field' => 'available_status']]]], ['term' => ['available_status' => 0]], ['term' => ['feedback' => 5]]], 'minimum_should_match' => 1]]]]]],
                    ],
                ],
            ];
            $statsResp = $this->elasticService->getClient()->search($statsParams);
            $agg = $statsResp['aggregations'] ?? [];
            $stats = [
                'interview_month' => $agg['this_month']['doc_count'] ?? 0,
                'today' => $agg['today']['doc_count'] ?? 0,
                'upcoming' => $agg['upcoming']['doc_count'] ?? 0,
                'feedback_pending' => $agg['feedback_pending']['doc_count'] ?? 0,
            ];
        } catch (\Throwable $e) {
        }

        return $stats;
    }

    // The CURRENT captain (SPOC) user id for the job behind this interview. Dynamic: if the
    // captain is transferred, the new captain is returned. Returns 0 when none set.
    private function jobCaptainId($interviewSchedule)
    {
        if (empty($interviewSchedule)) {
            return 0;
        }
        $jobId = (int) ($interviewSchedule->interview_job_id ?? 0);
        if (! $jobId) {
            return 0;
        }
        try {
            return (int) DB::table('job_assign')->where('job_id', $jobId)
                ->whereNotNull('spok_user_id')->where('spok_user_id', '!=', 0)->where('spok_user_id', '!=', '')
                ->value('spok_user_id');
        } catch (\Throwable $e) {
            return 0;
        }
    }

    // Whether the logged-in user may view salary/compensation.
    // Because this app has BOTH a global "interviewer" role and per-company copies, the role a user
    // is assigned to can differ from the role the admin edits. To make "uncheck Salary view = hide"
    // reliable, we hide Compensation if salary_view is missing from EITHER the user's assigned
    // role(s) OR the company-resolved permission set the rest of the app uses (userPermissions).
    private function userHasSalaryView()
    {
        // Standard permission check: the user may view salary/compensation if salary_view is granted
        // through ANY of their assigned roles (read fresh, bypassing cache). This matches how the rest
        // of the app gates salary. To hide salary from someone, remove salary_view from all their roles.
        try {
            $roleIds = DB::table('model_has_roles')->where('model_id', (int) auth()->id())->pluck('role_id')->all();
            if (in_array('salary_view', (array) ($this->userPermissions ?? []))) {
                return true;
            }

            return false;

            return in_array('salary_view', (array) ($this->userPermissions ?? []), true);
            if (! empty($roleIds)) {
                return DB::table('role_has_permissions as rp')
                    ->join('permissions as p', 'p.id', '=', 'rp.permission_id')
                    ->whereIn('rp.role_id', $roleIds)
                    ->where('p.name', 'salary_view')
                    ->exists();
            }
        } catch (\Throwable $e) {
        }

        return in_array('salary_view', (array) ($this->userPermissions ?? []), true);
    }

    // True only when the logged-in user is the CURRENT job captain (SPOC) or one of the
    // panel interviewers. A previous captain (no longer SPOC), recruiters and any other
    // account are NOT allowed to add/edit -- they may only view submitted feedback.
    private function canGiveInterviewFeedback($interviewSchedule, $interviewId = 0)
    {
        if (empty($interviewSchedule)) {
            return false;
        }
        $uid = (int) auth()->id();
        $uname = strtolower(trim(auth()->user()->name ?? ''));

        // Current captain / SPOC of the job
        $captainId = $this->jobCaptainId($interviewSchedule);
        if ($captainId && $captainId === $uid) {
            return true;
        }

        // Interviewer listed on the panel (stored as comma-separated names)
        if ($uname !== '' && ! empty($interviewSchedule->interviewers)) {
            foreach (explode(',', $interviewSchedule->interviewers) as $n) {
                if (strtolower(trim($n)) === $uname) {
                    return true;
                }
            }
        }

        return false;
    }

    private function ensureFeedbackSchema()
    {
        try {
            $cols = ['interview_result' => 'VARCHAR(30) NULL', 'overall_recommendation' => 'VARCHAR(30) NULL', 'current_ctc' => 'VARCHAR(50) NULL', 'expected_ctc' => 'VARCHAR(50) NULL', 'offered_ctc' => 'VARCHAR(50) NULL', 'max_budget' => 'VARCHAR(50) NULL', 'notice_period' => 'VARCHAR(20) NULL', 'joining_date' => 'DATE NULL', 'compensation_fit' => 'VARCHAR(40) NULL', 'joining_probability' => 'VARCHAR(20) NULL', 'risk_factors' => 'TEXT NULL', 'compensation_comments' => 'TEXT NULL'];
            $existing = \Illuminate\Support\Facades\Schema::getColumnListing('interview_feedback');
            foreach ($cols as $name => $type) {
                if (! in_array($name, $existing, true)) {
                    DB::statement('ALTER TABLE `interview_feedback` ADD COLUMN `'.$name.'` '.$type);
                }
            }
        } catch (\Throwable $e) {
        }
        try {
            $has = DB::table('interview_status')->whereRaw('LOWER(name) = ?', ['cancelled by candidate'])->exists();
            if (! $has) {
                DB::table('interview_status')->insert(['name' => 'Cancelled by Candidate', 'classname' => 'cancel-bg', 'status' => 1]);
            }
        } catch (\Throwable $e) {
        }
    }

    private function ifbOptionalCols()
    {
        $optional = ['interview_result', 'overall_recommendation', 'current_ctc', 'expected_ctc', 'offered_ctc', 'max_budget', 'notice_period', 'joining_date', 'compensation_fit', 'joining_probability', 'risk_factors', 'compensation_comments'];
        $existing = \Illuminate\Support\Facades\Schema::getColumnListing('interview_feedback');
        $cols = [];
        foreach ($optional as $c) {
            if (in_array($c, $existing, true)) {
                $cols[] = 'ifb.'.$c;
            }
        }

        return $cols;
    }

    public function updateInterview(Request $request)
    {
        $this->ensureFeedbackSchema();

        // dd($request->all());
        $interview_id = $app_int_status = $app_int_feedback_status = $feedback_comment = $application_id = $rating = '';

        $interview_id = @$request->interview_id;

        // Server-side guard: only the job captain (SPOC) and panel interviewers may submit feedback.
        $guardSchedule = DB::table('interview_schedules')->select('interview_job_id', 'interviewers')->where('id', $interview_id)->first();
        if (! $this->canGiveInterviewFeedback($guardSchedule, $interview_id)) {
            return Reply::dataOnly(['status' => 'error', 'msg' => 'You are not allowed to submit feedback for this interview.']);
        }

        $app_int_status = @$request->app_int_status;
        $app_int_feedback_status = @$request->app_int_feedback_status;
        $feedback_comment = @$request->feedback_comment;
        $application_id = @$request->application_id;
        $rating = @$request->rating;

        // Keep the legacy feedback id in sync with the new "Interview Result" so the tracker

        // shows the correct status (Selected/Hold/Rejected/Fake/Feedback Pending) without the migration.

        $resultToFeedbackId = [
            'selected' => 1, 'hold' => 2, 'rejected' => 3, 'fake_proxy' => 4, 'feedback_pending' => 5,
        ];

        if (! empty($request->interview_result) && isset($resultToFeedbackId[$request->interview_result])) {

            $app_int_feedback_status = $resultToFeedbackId[$request->interview_result];

        }

        $chkAdminRole = $this->user->hasRole('admin');

        // if(auth()->user()->id==18){
        // 	dd(auth()->user()->id);
        //        $teamRankElastic = [
        // 			'company_id'=>2,
        // 			'user_id'=>16,
        // 			'candidate_id'=>26925,
        // 			'profileUpload'=>0,
        // 			'interviewScheduled'=>10,
        // 			'interviewNoShow'=>10,
        // 			'offerReleased'=>0,
        // 			'candidateJoined'=>0,
        // 			'jobsPosted'=>0,
        // 			'feedbackGivenOnCVs'=>0,
        // 			'postJobToVendorMarket'=>0,
        // 			'outsourceJobsToVendors'=>0,
        // 			'listBenchCandidates'=>0,
        // 			'respondToBenchRequests'=>0
        // 		];
        // 		GeneralMethod::teamRankElastic($teamRankElastic);
        // }

        $cachekeys = [
            'allinterviewschedule_version_user_'.$this->user->id,
        ];
        foreach ($cachekeys as $key) {
            GeneralMethod::cacheClean($key);
        }

        $interviewCount_rank = DB::table('interview_feedback')
            ->select('interview_feedback.id')
            ->where('interview_id', @$interview_id)
            ->where('application_id', @$application_id)
            ->whereNotNull('feedback_id')
            ->count();

        $interviewCount_1 = DB::table('interview_feedback')
            ->where('interview_id', $interview_id)
            ->where('application_id', $application_id)
            ->where('available_status', 1)
            ->count();
        $interviewCount_2 = DB::table('interview_feedback')
            ->where('interview_id', $interview_id)
            ->where('application_id', $application_id)
            ->where('available_status', 2)
            ->count();
        $interviewCount_3 = DB::table('interview_feedback')
            ->where('interview_id', $interview_id)
            ->where('application_id', $application_id)
            ->where('available_status', 3)
            ->count();

        $interviewSum_rank = DB::table('interview_schedules')
            ->select('interview_job_wise_con_id as wiseId', 'interview_job_id', 'schedule_date')
            ->where('id', @$interview_id)
            ->first();

        $interview_update_date_check = date('Y-m-d H:i:s', strtotime($interviewSum_rank->schedule_date));
        $interview_update_date_check = Carbon::parse($interview_update_date_check);
        $today = Carbon::today();
        $diffInDays = abs($today->diffInDays($interview_update_date_check));

        // dd($interviewCount_rank); schedule_date

        if (($interviewCount_1 == 0 && $interviewCount_2 == 0 && $interviewCount_3 == 0) && ($app_int_status == 1 || $app_int_status == 2 || $app_int_status == 3)) {
            $interviewSum_rank = DB::table('interview_schedules')
                ->select('interview_job_wise_con_id as wiseId', 'interview_job_id')
                ->where('id', @$interview_id)
                ->first();
            $interviewScheduled = 0;
            $interviewNoShow = 0;
            $teamInterviewScheduled = 0;
            $teamInterviewNoShow = 0;
            if ($diffInDays <= 30) {
                if ($app_int_status == 1 && $app_int_feedback_status > 0) {
                    $interviewScheduled = 10;
                    $teamInterviewScheduled = 2;
                } elseif ($app_int_status == 2) {
                    $interviewNoShow = -5;
                } elseif ($app_int_status == 3 && $app_int_feedback_status == 13) {
                    $interviewNoShow = -5;
                }
            }

            $AddJobWiseCondidate = AddJobWiseCondidate::withoutGlobalScopes()->find($interviewSum_rank->wiseId);

            $spok_user_id_rows = DB::table('job_assign')
                ->where('job_id', $interviewSum_rank->interview_job_id)
                ->get(['spok_user_id', 'company_id'])->toArray();

            // Step 2: Build result as [company_id => spok_user_id]
            $teamCaptainId = [];

            foreach ($spok_user_id_rows as $row) {
                if (
                    ! is_null($row->spok_user_id) &&
                    $row->spok_user_id != $AddJobWiseCondidate->submitted_by
                ) {
                    $teamCaptainId[$row->company_id][] = $row->spok_user_id;
                }
            }

            // Step 3: Remove duplicates and reindex inner arrays
            foreach ($teamCaptainId as $companyId => $spokIds) {
                $teamCaptainId[$companyId] = array_values(array_unique($spokIds));
            }

            if (count($teamCaptainId) > 0) {

                foreach ($teamCaptainId as $comId => $captainId) {
                    $teamRankElastic = [
                        'company_id' => $comId,
                        'user_id' => $captainId[0],
                        'candidate_id' => $AddJobWiseCondidate->id,
                        'profileUpload' => 0,
                        'interviewScheduled' => 0,
                        'interviewNoShow' => 0,
                        'offerReleased' => 0,
                        'candidateJoined' => 0,
                        'jobsPosted' => 0,
                        'feedbackGivenOnCVs' => 0,
                        'postJobToVendorMarket' => 0,
                        'outsourceJobsToVendors' => 0,
                        'listBenchCandidates' => 0,
                        'respondToBenchRequests' => 0,
                        'interestedCandidates' => 0,
                        'notInterestedCandidates' => 0,
                        'teamCaptainInterviewScheduled' => $teamInterviewScheduled,
                        'teamCaptainInterviewNoShow' => $teamInterviewNoShow,
                        'teamCaptainOfferReleased' => 0,
                        'teamCaptainCandidateJoined' => 0,
                    ];
                    GeneralMethod::teamRankElastic($teamRankElastic);
                }

            }

            $teamRankElastic = [
                'company_id' => $AddJobWiseCondidate->company_id,
                'user_id' => $AddJobWiseCondidate->submitted_by,
                'candidate_id' => $AddJobWiseCondidate->id,
                'profileUpload' => 0,
                'interviewScheduled' => $interviewScheduled,
                'interviewNoShow' => $interviewNoShow,
                'offerReleased' => 0,
                'candidateJoined' => 0,
                'jobsPosted' => 0,
                'feedbackGivenOnCVs' => 0,
                'postJobToVendorMarket' => 0,
                'outsourceJobsToVendors' => 0,
                'listBenchCandidates' => 0,
                'respondToBenchRequests' => 0,
            ];
            GeneralMethod::teamRankElastic($teamRankElastic);
        } else {

            $interviewCount_rank = DB::table('interview_feedback')
                ->select('interview_feedback.id')
                ->where('interview_id', @$interview_id)
                ->where('available_status', '=', 1)
                ->count();

            $interviewCount_1 = DB::table('interview_feedback')
                ->select('interview_feedback.id')
                ->where('interview_id', @$interview_id)
                ->where('available_status', '=', 2)
                ->count();
            $interviewCount_2 = DB::table('interview_feedback')
                ->select('interview_feedback.id')
                ->where('interview_id', @$interview_id)
                ->where('available_status', '=', 3)
                ->count();

            // dd($interviewCount_rank);
            if ((($interviewCount_rank == 1) && ($interviewCount_1 == 0 && $interviewCount_2 == 0)) && ($app_int_status == 2 || $app_int_status == 3)) {

                $interviewSum_rank = DB::table('interview_schedules')
                    ->select('interview_job_wise_con_id as wiseId', 'interview_job_id')
                    ->where('id', @$interview_id)
                    ->first();

                $interviewScheduled = 0;
                $teamInterviewScheduled = 0;
                $interviewNoShow = 0;
                $teamInterviewNoShow = 0;
                if ($diffInDays <= 30) {
                    if ($app_int_status == 2) {
                        $interviewNoShow = -5;
                        $interviewScheduled = -10;
                    } elseif ($app_int_status == 3 && $app_int_feedback_status == 13) {
                        $interviewNoShow = -5;
                        $interviewScheduled = -10;
                    } elseif ($app_int_status == 3 && $app_int_feedback_status == 14) {
                        $interviewNoShow = 0;
                        $interviewScheduled = -10;
                    } elseif ($app_int_status == 3 && $app_int_feedback_status == 15) {
                        $interviewNoShow = 0;
                        $interviewScheduled = -10;
                    } elseif ($app_int_status == 3 && $app_int_feedback_status == 17) {
                        $interviewNoShow = 0;
                        $interviewScheduled = -10;
                    }
                }

                $interviewSum_no_show = DB::table('interview_feedback')
                    ->select('interview_feedback.id')
                    ->where('interview_id', @$interview_id)
                    ->where('available_status', '=', 2)
                    ->count();

                $interviewSum_resch_show = DB::table('interview_feedback')
                    ->select('interview_feedback.id')
                    ->where('interview_id', @$interview_id)
                    ->where('available_status', '=', 3)
                    ->count();

                // dd($interviewSum_no_show);
                $interviewSum_no_show = (int) $interviewSum_no_show;
                $interviewSum_resch_show = (int) $interviewSum_resch_show;
                if ($interviewSum_no_show >= 1 || $interviewSum_resch_show >= 1) {
                    $interviewScheduled = 0;
                    $interviewNoShow = 0;
                }

                $AddJobWiseCondidate = AddJobWiseCondidate::withoutGlobalScopes()->find($interviewSum_rank->wiseId);

                $spok_user_id_rows = DB::table('job_assign')
                    ->where('job_id', $interviewSum_rank->interview_job_id)
                    ->get(['spok_user_id', 'company_id'])->toArray();

                // Step 2: Build result as [company_id => spok_user_id]
                $teamCaptainId = [];

                foreach ($spok_user_id_rows as $row) {
                    if (
                        ! is_null($row->spok_user_id) &&
                        $row->spok_user_id != $AddJobWiseCondidate->submitted_by
                    ) {
                        $teamCaptainId[$row->company_id][] = $row->spok_user_id;
                    }
                }

                // Step 3: Remove duplicates and reindex inner arrays
                foreach ($teamCaptainId as $companyId => $spokIds) {
                    $teamCaptainId[$companyId] = array_values(array_unique($spokIds));
                }

                if (count($teamCaptainId) > 0) {

                    foreach ($teamCaptainId as $comId => $captainId) {
                        $teamRankElastic = [
                            'company_id' => $comId,
                            'user_id' => $captainId[0],
                            'candidate_id' => $AddJobWiseCondidate->id,
                            'profileUpload' => 0,
                            'interviewScheduled' => 0,
                            'interviewNoShow' => 0,
                            'offerReleased' => 0,
                            'candidateJoined' => 0,
                            'jobsPosted' => 0,
                            'feedbackGivenOnCVs' => 0,
                            'postJobToVendorMarket' => 0,
                            'outsourceJobsToVendors' => 0,
                            'listBenchCandidates' => 0,
                            'respondToBenchRequests' => 0,
                            'interestedCandidates' => 0,
                            'notInterestedCandidates' => 0,
                            'teamCaptainInterviewScheduled' => $teamInterviewScheduled,
                            'teamCaptainInterviewNoShow' => $teamInterviewNoShow,
                            'teamCaptainOfferReleased' => 0,
                            'teamCaptainCandidateJoined' => 0,
                        ];
                        GeneralMethod::teamRankElastic($teamRankElastic);
                    }

                }

                $teamRankElastic = [
                    'company_id' => $AddJobWiseCondidate->company_id,
                    'user_id' => $AddJobWiseCondidate->submitted_by,
                    'candidate_id' => $AddJobWiseCondidate->id,
                    'profileUpload' => 0,
                    'interviewScheduled' => $interviewScheduled,
                    'interviewNoShow' => $interviewNoShow,
                    'offerReleased' => 0,
                    'candidateJoined' => 0,
                    'jobsPosted' => 0,
                    'feedbackGivenOnCVs' => 0,
                    'postJobToVendorMarket' => 0,
                    'outsourceJobsToVendors' => 0,
                    'listBenchCandidates' => 0,
                    'respondToBenchRequests' => 0,
                ];
                GeneralMethod::teamRankElastic($teamRankElastic);

            }

        }

        // dd($interview_id,$request->all());

        $data = [];

        $values = [
            'interview_id' => @$interview_id,
            'application_id' => @$application_id,
            'available_status' => @$app_int_status,
            'feedback_id' => @$app_int_feedback_status,
            'star_ratings' => @$rating,
            // 'feedback_name' => $feedback_name,
            'message' => @$feedback_comment,
            'interview_result' => @$request->interview_result,
            'overall_recommendation' => @$request->overall_recommendation,
            'current_ctc' => @$request->current_ctc,
            'expected_ctc' => @$request->expected_ctc,
            'offered_ctc' => @$request->offered_ctc,
            'max_budget' => @$request->max_budget,
            'notice_period' => @$request->notice_period,
            'joining_date' => (@$request->joining_date ?: null),
            'compensation_fit' => @$request->compensation_fit,
            'joining_probability' => @$request->joining_probability,
            'risk_factors' => (! empty($request->risk_factors) ? implode(',', (array) $request->risk_factors) : null),
            'compensation_comments' => @$request->compensation_comments,
            'update_by' => $this->user->id,
            'created_at' => date('Y-m-d:H:i:s'),
        ];

        // Roles without Salary View cannot save compensation details (defence in depth: the
        // section is also hidden in the UI, but never trust the client).
        if (! $this->userHasSalaryView()) {
            foreach (['current_ctc', 'expected_ctc', 'offered_ctc', 'max_budget', 'notice_period', 'joining_date', 'compensation_fit', 'joining_probability', 'risk_factors', 'compensation_comments'] as $cf) {
                unset($values[$cf]);
            }
        }

        $values = array_intersect_key($values, array_flip(\Illuminate\Support\Facades\Schema::getColumnListing('interview_feedback')));
        $update = DB::table('interview_feedback')->insert($values);
        $lastId = DB::getPdo()->lastInsertId();

        if ($app_int_feedback_status == 5 || $app_int_status != 1) {

        } else {
            foreach ($request->all() as $key => $value) {
                if (preg_match('/skillsId_(\d+)/', $key, $matches)) {
                    $skillId = $matches[1];

                    // Extract values using the skill ID
                    $skillName = $request->input("skills_{$skillId}");
                    $experience = $request->input("experience_{$skillId}");
                    $skillRating = $request->input("rating_{$skillId}");
                    $skillRemark = $request->input("remark_{$skillId}");

                    // Store data in an array for batch insert
                    $data[] = [
                        'interview_id' => $interview_id,
                        'application_id' => @$application_id,
                        'update_by' => $this->user->id,
                        'candidate_status' => @$app_int_status,
                        'feedback_status' => @$app_int_feedback_status,
                        'feedback_id' => $lastId,
                        'star_ratings' => @$rating,
                        'skills' => $skillName,
                        'skills_exp' => $experience,
                        'skills_ratings' => $skillRating,
                        'skills_remarks' => $skillRemark,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            $data[] = [
                'interview_id' => $interview_id,
                'application_id' => @$application_id,
                'update_by' => $this->user->id,
                'candidate_status' => @$app_int_status,
                'feedback_status' => @$app_int_feedback_status,
                'feedback_id' => $lastId,
                'star_ratings' => @$rating,
                'skills' => @$request->skills_com,
                'skills_exp' => @$request->experience_com,
                'skills_ratings' => @$request->rating_com,
                'skills_remarks' => @$request->remark_com,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $data[] = [
                'interview_id' => $interview_id,
                'application_id' => @$application_id,
                'update_by' => $this->user->id,
                'candidate_status' => @$app_int_status,
                'feedback_status' => @$app_int_feedback_status,
                'feedback_id' => $lastId,
                'star_ratings' => @$rating,
                'skills' => @$request->skills_conl,
                'skills_exp' => @$request->experience_conl,
                'skills_ratings' => @$request->rating_conl,
                'skills_remarks' => @$request->remark_conl,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $data[] = [
                'interview_id' => $interview_id,
                'application_id' => @$application_id,
                'update_by' => $this->user->id,
                'candidate_status' => @$app_int_status,
                'feedback_status' => @$app_int_feedback_status,
                'feedback_id' => $lastId,
                'star_ratings' => @$rating,
                'skills' => @$request->skills_culf,
                'skills_exp' => @$request->experience_culf,
                'skills_ratings' => @$request->rating_culf,
                'skills_remarks' => @$request->remark_culf,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (! empty($data)) {
                DB::table('interview_skills_feedback')->insert($data);
            }
        }

        $interviewCount = DB::table('interview_feedback')
            ->select('interview_feedback.id')
            ->where('interview_id', @$interview_id)
            ->where('application_id', @$application_id)
            ->count();

        $interviewSum = DB::table('interview_feedback')
            ->select(DB::raw('SUM(star_ratings) AS star_ratings_sum'))
            ->where('interview_id', @$interview_id)
            ->where('application_id', @$application_id)
            ->first();

        $averageRatimg = @$rating; // $interviewSum->star_ratings_sum / $interviewCount;

        $returnValue = DB::table('interview_schedules')->where('id', $interview_id)->limit(1)
            ->update(['available_status' => @$app_int_status, 'feedback' => @$app_int_feedback_status, 'average_star_rating' => ceil($averageRatimg)]);

        $updatetimelineArr = [
            'average_star_rating' => ceil($averageRatimg),
            'interview_message' => @$feedback_comment,
        ];
        $returnValue = DB::table('updatetimeline')->where('updatetimeline.interview_schedules_id', $interview_id)->limit(1)->update($updatetimelineArr);

        $InterviewSchedule = InterviewSchedule::join('add_job_wise_condidates', 'add_job_wise_condidates.id', '=', 'interview_schedules.interview_job_wise_con_id')
            ->join('companies', 'companies.id', '=', 'interview_schedules.company_id')
            ->join('clients', 'clients.company_id', '=', 'companies.id')
            ->join('client_location', 'client_location.client_id', '=', 'clients.id')
            ->join('jobs', 'jobs.id', '=', 'interview_schedules.interview_job_id')
            ->join('job_applications', 'job_applications.id', '=', 'interview_schedules.job_application_id')
            ->join('updatetimeline', 'updatetimeline.interview_schedules_id', '=', 'interview_schedules.id')
            ->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')
            ->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage')
            ->select('interview_schedules.*', 'updatetimeline.interview_mention_link', 'jobs.client_id', 'master_status.name as master_status_name', 'candidate_status.name as child_status_name', 'updatetimeline.interview_mode_child_level as mode_child', 'companies.company_name', 'companies.description', 'companies.website', 'client_location.address as c_address', 'job_applications.full_name', 'job_applications.email as cond_email', 'job_applications.current_designation', 'jobs.employment_type', 'jobs.title', 'jobs.job_description', 'jobs.id as jobID', 'jobs.total_positions')->where('interview_schedules.id', '=', $interview_id)->first();

        // dd($interview_id,$InterviewSchedule );

        $jobId = $InterviewSchedule->interview_job_id;
        $company_or_client = GeneralMethod::companyDetailsByClient($InterviewSchedule->interview_job_id);
        $interview_mode = '';
        if ($InterviewSchedule->interview_mode > 0) {
            $interview_mode_Arr = GeneralMethod::interview_mode();
            $interview_mode = $interview_mode_Arr[@$InterviewSchedule->interview_mode];
        }
        $interview_mode_child_level = $InterviewSchedule->interview_mode_child_level;
        if ($InterviewSchedule->interview_mode == 3) {
            $interview_mode_child_level = @$InterviewSchedule->interview_mention_link;
        }

        $partnersData = User::join('job_assign', 'users.id', '=', 'job_assign.user_id')
            ->join('package_active', function ($join) {
                $join->on('users.company_id', '=', 'package_active.company_id')
                    ->where('package_active.emails_balance', '>', 0)
                    ->where('package_active.startdate', '<=', now())
                    ->where('package_active.enddate', '>=', now())
                    ->where('package_active.packages_status', 1);
            })
            ->where('job_assign.job_id', '=', $jobId)
            ->pluck('users.email')
            ->toArray();

        $recommendedDataList = DB::table('recommendation_relation');
        $recommendedDataList = $recommendedDataList->join('companies', 'companies.id', 'recommendation_relation.to_company_id');
        $recommendedDataList = $recommendedDataList->join('jobs', 'jobs.id', 'recommendation_relation.job_id');
        $recommendedDataList = $recommendedDataList->leftJoin('job_applications', function ($join) {
            $join->on('job_applications.thirdparty_company_id', '=', 'companies.id');
            $join->on('job_applications.job_id', '=', 'jobs.id');
        });
        $recommendedDataList = $recommendedDataList->join('package_active', function ($join) {
            $join->on('companies.id', '=', 'package_active.company_id')
                ->where('package_active.emails_balance', '>', 0)
                ->where('package_active.startdate', '<=', now())
                ->where('package_active.enddate', '>=', now())
                ->where('package_active.packages_status', 1);
        });
        $recommendedDataList = $recommendedDataList->where('recommendation_relation.final_status', 1);
        $recommendedDataList = $recommendedDataList->where('recommendation_relation.job_id', '=', $jobId);
        $recommendedDataList = $recommendedDataList->groupBy('companies.id');
        $recommendedDataList = $recommendedDataList->pluck('companies.company_email');
        $recommendedDataList = $recommendedDataList->toArray();

        $feedback_master = GeneralMethod::feedback_status();
        $feedback_status = '';
        if (@$app_int_feedback_status) {
            $feedback_status = $feedback_master[(@$app_int_feedback_status) ? @$app_int_feedback_status : 0]['name'];
        }

        $feedbackdata = DB::table('interview_skills_feedback as isf')
            ->leftJoin('interview_feedback as ifb', function ($join) {
                $join->on('isf.feedback_id', '=', 'ifb.id');
            })
            ->leftJoin('users', function ($join) {
                $join->on('isf.update_by', '=', 'users.id');
            })
            ->leftJoin('users_profile', function ($join) {
                $join->on('users_profile.user_id', '=', 'users.id');
            })
            ->where('ifb.id', $lastId)
            ->orderByDesc('isf.feedback_id')
            ->select(
                'isf.feedback_id',
                'isf.skills',
                'users.name as by_name',
                'users_profile.designation',
                'isf.skills_exp',
                'isf.skills_ratings',
                'isf.skills_remarks',
                'ifb.feedback_name',
                'ifb.message',
                'ifb.created_at',
                'ifb.star_ratings',
                ...$this->ifbOptionalCols()
            )
            ->get()
            ->groupBy('feedback_id'); // Group records by feedback_id

        $feedbackMapped = $feedbackdata->map(function ($feedbacks) {
            return [
                'feedback_id' => $feedbacks->first()->feedback_id,
                'feedback_name' => $feedbacks->first()->feedback_name,
                'by_name' => $feedbacks->first()->by_name,
                'designation' => $feedbacks->first()->designation,
                'created_at' => $feedbacks->first()->created_at,
                'message' => $feedbacks->first()->message,
                'star_ratings' => $feedbacks->first()->star_ratings,
                'feedbackdata' => $feedbacks->map(function ($feedback) {
                    return [
                        'skills' => $feedback->skills,
                        'experience' => $feedback->skills_exp,
                        'ratings' => $feedback->skills_ratings,
                        'remarks' => $feedback->skills_remarks,
                    ];
                })->values()->all(),
            ];
        });

        $data = [
            'InterviewDate' => date('d-m-Y h:i:s', strtotime($InterviewSchedule->schedule_date)),
            'InterviewMode' => $interview_mode.'('.$InterviewSchedule->mode_child.')',
            'InterviewAddress' => $InterviewSchedule->interview_mention_link,
            'CompanyName' => $InterviewSchedule->company_name,
            'CompanyLocation' => $InterviewSchedule->c_address,
            'Position' => $InterviewSchedule->current_designation,
            'EmploymentType' => ($InterviewSchedule->employment_type == 1) ? 'Contract' : 'Full-time',
            'ReqID' => 'R'.@$InterviewSchedule->jobID,
            'Reqname' => @$InterviewSchedule->title,
            'NoofEmployees' => $InterviewSchedule->total_positions,
            'OfficeTiming' => '...',
            'NoofWorkingDays' => '...',
            'feedback_comment' => @$feedback_comment,
            'WebsiteURL' => $InterviewSchedule->website,
            'Jobdescription' => $InterviewSchedule->job_description,
            'AboutOrganization' => $InterviewSchedule->description,
            'VendorTeam' => @$InterviewSchedule->company_name,
            'CompanyNameClientname' => $company_or_client,
            'feedback_status' => (@$feedback_status) ? @$feedback_status : '',
            'CandidateName' => $InterviewSchedule->full_name,
            'Stage' => ($InterviewSchedule->master_status_name) ? $InterviewSchedule->master_status_name : '.....',
            'Substage' => ($InterviewSchedule->child_status_name) ? $InterviewSchedule->child_status_name : '....',
            'username' => auth()->user()->name,
            'mobile' => auth()->user()->mobile,
            'feedbackMapped' => $feedbackMapped,
        ];
        // 		dd($data);
        // 		config(['mail.from.name' => auth()->user()->name]);

        $cc_cond = [$InterviewSchedule->cond_email];

        $notification = DB::table('clients')->where('id', '=', @$InterviewSchedule->client_id)->where('self_company', '=', 1)->pluck('email')->toArray();
        $email_to_remove = auth()->user()->email;
        $index = array_search($email_to_remove, $notification);
        if ($index !== false) {
            unset($notification[$index]);
            $notification = array_values($notification);
        }
        $cc = []; // $notification; //array_merge($partnersData, $recommendedDataList);

        // $cc = array_merge($partnersData, $recommendedDataList);
        $basecompany = GeneralMethod::baseCompany();
        $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
        $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';

        $attachments = [];

        $subject = "Interview Feedback | $InterviewSchedule->full_name | $InterviewSchedule->title | R$InterviewSchedule->jobID ";
        $checkMailPermision = GeneralMethod::mailPermission(12);
        if ($checkMailPermision) {
            if ($app_int_feedback_status != 5) {
                $company_id = auth()->user()->company_id;
                $mailTypeSubject = GeneralMethod::mailTypeSubject(12, 1);
                $notificationemail = GeneralMethod::notification($company_id, 'Recuitment');
                $defailsEmail = DB::table('companies')->where('id', $company_id)->first()->company_email;
                $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
                //  dd($to_Mail_Id);
                QueueNotificationMailService::dispatch(
                    $to_Mail_Id,
                    $subject,
                    'email.candidate.interview-feedback',
                    (object) $data,
                    $attachments,
                    $fromAddress,
                    $senderName,
                    $cc,
                    $company_id
                )->onConnection('emails');
            }

            //   $result = $this->mailService->sendEmail($to_Mail_Id, $subject,'email.candidate.interview-feedback', (object) $data, $attachments, $fromAddress, $senderName,$cc);

        }
        // 		Mail::to(auth()->user()->email)->cc($cc)->send(new InterviewFeedbackMail((object) $data));

        if (count($cc) > 0) {
            foreach ($cc as $email) {
                $client_company_id = 0;
                $useremail = DB::table('users')->select('*')->where('email', $email)->first();
                if (! empty($useremail)) {
                    $client_company_id = @$useremail->company_id;
                }
                // dd($client_company_id);
                if (@$client_company_id > 0) {
                    $event_name = 'emails';
                    $event_table_name = 'job applications stage change';
                    $event_table_id = @$client_company_id;
                    GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $client_company_id);
                }
            }
        }
        GeneralMethod::interviewTrackerAddEditUpload($interview_id);
        $cachekeys = [
            'allinterviewschedule_version_user_'.$this->user->id,
        ];
        foreach ($cachekeys as $key) {
            GeneralMethod::cacheClean($key);
        }
        //   dd("hi");

        return Reply::dataOnly(['status' => 'success', 'msg' => 'Interview feedback update successfully.']);

    }

    public function interviewDirectClientData(Request $request)
    {
        $available_status = '';
        $feedback_status = '';
        $interview_mode_arr = '';

        $company_id = auth()->user()->company_id;
        $available_status = GeneralMethod::available_status();
        $feedback_status = GeneralMethod::feedback_status();
        $interview_mode_arr = GeneralMethod::interview_mode();

        $shedule = OpenworkspaceInterviewSchedule::join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
            ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->join('jobs', function ($join) {
                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
            })
            ->leftjoin('users', 'users.id', 'job_applications.submitted_by')
            // ->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
            ->leftjoin('companies', 'companies.id', 'jobs.company_id')
            ->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')
            ->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
            ->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
            ->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage')
            ->select(
                'interview_schedules.id',
                'job_applications.full_name',
                'interview_schedules.status',
                'interview_schedules.schedule_date',
                'interview_schedules.available_status',
                'interview_schedules.reschedule',
                'interview_schedules.feedback',
                'interview_schedules.interview_mode',
                'interview_schedules.interviewers',
                'interview_schedules.interview_type',
                'interview_schedules.schedule_from_time',
                'users.name as source',
                'interview_schedules.job_application_id',
                'master_status.name as master_status_name',
                'candidate_status.name as child_status_name',
                'jobs.title as job_title',
                'jobs.id as job_id',
                'companies.company_name',

                'job_applications.current_designation',
                'interview_mode.name as mode_name',
                'interview_mode_lavel.name as mode_lavel_name',
                'master_status.css_style',

                'jobs.company_id'
            );
        $shedule = $shedule->where('job_applications.thirdparty_company_id', $this->user->company_id)
            ->where('recommendation_relation.job_id', '>', 0)
            ->where('interview_schedules.stage', '!=', 8);
        $shedule = $shedule->where('add_job_wise_condidates.job_id', $request->hid_job_id);

        if ($request->startDate !== null && $request->startDate != 'null') {
            $shedule = $shedule->where(DB::raw('DATE(interview_schedules.`schedule_date`)'), '>=', "$request->startDate");
        }

        if ($request->endDate !== null && $request->endDate != 'null') {
            $shedule = $shedule->where(DB::raw('DATE(interview_schedules.`schedule_date`)'), '<=', "$request->endDate");
        }
        $shedule->groupBy('job_applications.id');
        $shedule->orderBy('interview_schedules.schedule_date', 'DESC');
        $shedule->get();

        return DataTables::of($shedule)

            ->editColumn('chkbox', function ($row) {

                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
								<input class="form-check-input" type="checkbox" value="1" />
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

                return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'" class="show-application-detail">
                      <div data-row-id="'.$row->job_application_id.'"  class="symbol-label '.$bgTextDecore.' border-radius-5 show-application-detail">'.ucwords($strVal).'</div>
                    </a>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="'.ucwords(@$row->full_name).'">'.ucwords(substr(@$row->full_name, 0, 20)).'</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="'.ucwords(@$row->current_designation).'">'.ucwords(substr(@$row->current_designation, 0, 20)).'</span>
                  </div>';

            })
            ->editColumn('Requisitions', function ($row) {

                return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.ucwords($row->job_title).'">'.ucwords(substr($row->job_title, 0, 7)).'</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="'.ucwords($row->company_name).'">'.ucwords(substr($row->company_name, 0, 7)).'</span>
                  </div>';

            })
            ->editColumn('InterviewMode', function ($row) {

                return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.ucwords(substr($row->mode_name, 0, 15)).'</a> 
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.ucwords(substr($row->mode_lavel_name, 0, 15)).'</span> 
                  </div>';

            })
            ->editColumn('Interviewers', function ($row) {

                $interviewers = $html = $company_id = '';
                $interviewers = $row->interviewers;

                $company_id = $row->company_id;

                $html = '<div class="d-flex flex-column"><div class="symbol-group symbol-hover">------</div></div>';

                if (! empty($interviewers)) {
                    $trimmed_array = explode(',', $interviewers);
                    $trimmed_array = array_map('trim', $trimmed_array);

                    $html = '<div class="d-flex flex-column"> 
                  									<div class="symbol-group symbol-hover"> ';

                    $InterviewArr = DB::table('users')
                        ->select('users.name', 'users.email', 'users.mobile', 'users.image')
                        ->where('users.company_id', '=', $company_id)
                        ->whereIn('users.name', $trimmed_array)
                        ->get();

                    foreach ($InterviewArr as $idata) {

                        $imagePath = '';
                        $imagePath = url('public/avatar.png');
                        if (! empty(@$idata->image)) {
                            $imagePath = url('public/user-uploads/team_profile_img/'.@$idata->image);
                        }

                        $html .= '<div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="'.@$idata->name.'" data-original-title="One">
														<img alt="Pic" src="'.@$imagePath.'">
													</div>';
                    }

                    $html .= '</div>
                  							  	</div>';
                }

                return $html;

            })
            ->editColumn('Source', function ($row) {

                $bgTextDecore = $strVal = '';
                $str = trim(@$row->source);
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

                return ' <div class="d-flex flex-column">
								<div class="symbol-group symbol-hover">
									<div class="symbol symbol-30 symbol-circle symbol-light border-radius-5" style="float:left">
										<a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'">
											<div data-row-id="'.$row->job_application_id.'"  class="symbol-label '.$bgTextDecore.' border-radius-5 show-application-detail" style="border-radius: 50%!important;font-size: 13px!important;font-weight: 600;">'.ucwords($strVal).'</div>
										</a>
									</div>
								</div>
                  			</div>

                    		<span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="'.ucwords(@$row->source).'">'.ucwords(substr(@$row->source, 0, 10)).'</span>';

            })
            ->editColumn('InterviewDate', function ($row) {

                return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.Carbon::parse($row->schedule_date)->format('d M, Y').'</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.Carbon::parse($row->schedule_date)->format('h:i a').'</span>
                  </div>';

            })
            ->editColumn('Feedback', function ($row) use ($feedback_status, $available_status) {

                $available_status_name = '';
                if ($row->available_status > 0) {
                    $available_status_name = @$available_status[@$row->available_status]['name'];
                } else {
                    $available_status_name = @$available_status[6]['name'];
                }

                return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 show-feedback" data-bs-toggle="modal" data-bs-target="#interview_feedback">'.ucwords(substr(@$feedback_status[@$row->feedback]['name'], 0, 20)).'</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.ucwords(substr(@$available_status_name, 0, 20)).'</span>
                  </div>';

            })
            ->editColumn('Rating', function ($row) {

                $interviewId = $row->id;
                $imagePath = '';
                $imagePath = url('public/stars.png');

                return ' <img src="'.$imagePath.'" alt="image" style="width: 5.5rem">
                    <span class="text-muted fw-bold text-muted d-block fs-7">Above Avarage</span>';

            })
            ->editColumn('Stage', function ($row) {

                return '<div class="d-flex flex-column"> 
                        <span class="badge fw-bolder fs-6 px-2 py-1 '.@$row->css_style.'">'.substr(@$row->master_status_name, 0, 10).'</span>
                        <span class="text-muted fw-bold text-muted d-block fs-7">'.substr(@$row->child_status_name, 0, 10).'</span>
                    </div>';

            })

            ->rawColumns(['chkbox', 'Name', 'Requisitions', 'InterviewMode', 'Interviewers', 'Source', 'InterviewDate', 'Feedback', 'Rating', 'Stage'])
            ->make(true);
    }
}
