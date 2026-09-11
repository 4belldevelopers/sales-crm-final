<?php

/*** ########################################################
Controller    	: 	adminDashboardController
Purmose			:
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021
########################################################***/

namespace App\Http\Controllers\ServiceCompany;

use App\Helper\GeneralMethod;
use App\Models\InterviewSchedule;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\MeetingComment;
use App\Models\MeetingSchedule;
use App\Services\AutoCloseJobsService;
use App\Services\AutoRenewFreePackage;
use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceCompanyDashboardController extends ServiceCompanyBaseController
{
    protected $elasticService;

    public function __construct(ElasticService $elasticService, AutoCloseJobsService $autoCloseJobsService, AutoRenewFreePackage $autoRenewFreePackage)
    {

        parent::__construct();

        $this->pageIcon = 'icon-speedometer';
        $this->pageTitle = 'menu.dashboard';
        $this->elasticService = $elasticService;
        $this->autoCloseJobsService = $autoCloseJobsService;
        $this->autoRenewFreePackage = $autoRenewFreePackage;

    }

    public function index()
    {

    // $params = [
    //             'index' => ELASTIC_LOCATIONS,
    //             'id' => '132032',
    //         ];

    //         $response = $this->elasticService->getClient()->get($params);
    //         $dddddd = (object) $response['_source'];

    //         dd($dddddd);


        $chkAdminRole = $this->user->hasRole('admin');
        // $mapping = $this->elasticService->getClient()->indices()->getMapping([
        //     'index' => ELASTIC_CANDIDATES
        //      ]);
        // dd($mapping);
        // $add_job_wise_condidates =  DB::table('add_job_wise_condidates')
        // ->where('job_id','=',254)
        // ->select('add_job_wise_condidates.id','add_job_wise_condidates.job_id')
        // ->get();
        // foreach ($add_job_wise_condidates as $add_job_wise) {
        // 	GeneralMethod::JobApplicationsAddEditUpload($add_job_wise->id);
        // }
        // dd("djfj");

        $pathToCredentialsFile = base_path('config\client_secret_1_1049949345978-ud7ovbld3m3p54hm0n9fkt825uuuqkqm.apps.googleusercontent.com.json');
        // dd($pathToCredentialsFile);

        $this->company_id = auth()->user()->company_id;

        $getTopFive = request()->query('getTopFive');
        // V2 Phase 1: job expiry & package renewal now run via AutoCloseExpiredJobsJob (hourly Horizon schedule).

        // GeneralMethod::jobAutoExpiredAfterValidDate($this->company_id);

        $cachePayload = [
            'company' => $this->company_id,
            'user' => $this->user->id,
        ];

        $versionKey = 'candidateDetails_version_user_'.$this->user->id;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:candidateDetails:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));
        /* ----------------------------
        ⚡ Check DragonflyDB first
        ----------------------------- */
        $cachedData = cache()->get($cacheKeySet);
        if (cache()->has($cacheKeySet)) {
            if ((int) $getTopFive != 908070605012344321 && $getTopFive == '') {
                return response($cachedData)->header('Content-Type', 'text/html');
            }

        }

        $exists_company = $this->elasticService->getClient()->exists([
            'index' => ELASTIC_COMPANIES_INDEX,
            'id' => $this->company_id,
        ]);

        // V2 Phase 1: team coin deductions moved to ProcessOperationalCoinDeductionsJob (scheduled every 5 min).

        // V2 Phase 1: job coin deductions moved to ProcessOperationalCoinDeductionsJob (scheduled every 5 min).
        // V2 Phase 1: offer 72-hour timeout moved to ProcessOfferTimeoutJob (scheduled every 5 min).

        if ($exists_company) {

            $company_coins = DB::table('company_coins')
                ->where('company_id', $this->company_id)
                ->get()
                ->map(function ($c) {
                    $coin_orders = DB::table('coin_orders')
                        ->join('coin_package_types', 'coin_package_types.id', '=', 'coin_orders.coin_package_id')
                        ->join('coin_packages', 'coin_packages.coin_package_type_id', '=', 'coin_package_types.id')
                        ->where('coin_orders.id', $c->coin_order_id)
                        ->where('coin_orders.status', 1)
                        ->select('coin_package_types.title', 'coin_packages.custome_pck')
                        ->first();
                    $package_name = null;
                    $package_type = null;
                    // dd($coin_orders);
                    if ($coin_orders) {
                        $package_name = @$coin_orders->title;
                        $package_type = @$coin_orders->custome_pck;
                    }

                    return [
                        'id' => (int) $c->id,
                        'company_id' => (int) $c->company_id,
                        'coins' => (float) $c->coins,
                        'actual_coins' => (float) $c->actual_coins,
                        'balance_coin' => (float) $c->balance_coin,
                        'package_name' => $package_name,
                        'package_type' => (int) $package_type,
                        'amount' => (float) $c->amount,
                        'discount' => (float) $c->discount,
                        'currency' => ($c->currency),
                        'coin_order_id' => (int) $c->coin_order_id,
                        'status' => ($c->status),
                        'event_type' => ($c->event_type),
                        'free_pack_status' => ($c->free_pack_status),
                        'start_date' => ($c->start_date) ? Carbon::parse($c->start_date)->toIso8601String() : null,
                        'end_date' => ($c->end_date) ? Carbon::parse($c->end_date)->toIso8601String() : null,
                        'auto_job_coins' => ($c->auto_job_coins) ? Carbon::parse($c->auto_job_coins)->toIso8601String() : null,
                        'auto_team_coins' => ($c->auto_team_coins) ? Carbon::parse($c->auto_team_coins)->toIso8601String() : null,
                        'free_update_date' => ($c->free_update_date) ? Carbon::parse($c->free_update_date)->toIso8601String() : null,
                        'created_at' => ($c->created_at) ? Carbon::parse($c->created_at)->toIso8601String() : null,
                        'updated_at' => ($c->updated_at) ? Carbon::parse($c->updated_at)->toIso8601String() : null,
                    ];
                })
                ->toArray();

            $updateParams = [
                'index' => ELASTIC_COMPANIES_INDEX,
                'id' => $this->company_id,
                'body' => [
                    'doc' => [
                        'company_coins' => $company_coins,
                    ],
                    'doc_as_upsert' => false, // Changed to false since we verified existence
                ],
                'refresh' => false, // Eventual consistency for 10K concurrent scale
            ];
            // $this->elasticService->getClient()->update($updateParams);
        }

        $this->job_status = GeneralMethod::job_status();
        $this->salaryMode = GeneralMethod::salaryMode();
        $this->currencySymbole = GeneralMethod::currencySymbole();

        $company_id = $this->user->company_id;
        $rolesCustome = $this->user->getAllPermissions();
        $this->admin = $rolesCustome[0]->name;

        // $company_id				= 	$this->user->roles[0]->company_id;
        // $this->admin			=	$this->user->roles[0]->name;

        // dd($company_id,$this->admin);

        $company_id = '';
        $company_id = auth()->user()->company_id;

        $lastSevenDate = date('Y-m-d', strtotime('-7 days'));
        $todayDate = date('Y-m-d H:i:s');
        $nextSevenDate = date('Y-m-d', strtotime('+7 days'));

        $currentDate = Carbon::now(company()->timezone)->format('Y-m-d');

        // Range query on schedule_date uses the idx_comp_sched_status index (V2 Phase 2).
        $this->totalTodayInterview = InterviewSchedule::whereBetween('schedule_date', [
            Carbon::now(company()->timezone)->startOfDay(),
            Carbon::now(company()->timezone)->endOfDay(),
        ])
            ->count();
        $this->todoItemsView = $this->generateTodoView();

        $companyidGraph = $this->company_id;

        $company_id = auth()->user()->company_id;
        $limit = '';
        $userId = $this->user->id;

        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();
        $startOfMonth = Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        // For monthly report

        // $rankstartOfMonthForReport = Carbon::now()->subMonth()->startOfMonth()->toDateTimeString();
        // $rankendOfMonthForReport = Carbon::now()->subMonth()->endOfMonth()->toDateTimeString();

        $reportMonth = Carbon::now()->startOfMonth()->subMonth();
        $rankstartOfMonthForReport = $reportMonth->copy()->startOfMonth()->toDateTimeString();      // 2025-06-01
        $rankendOfMonthForReport = $reportMonth->copy()->endOfMonth()->toDateTimeString();

        // The blade views label every score card / scoreboard with $rankstartOfMonth.
        // It was never defined, so the month showed as 1970. Expose the report month here.
        $this->rankstartOfMonth = $rankstartOfMonthForReport;

        // dd($rankstartOfMonthForReport,$rankendOfMonthForReport);

        $startOfMonthForReport = Carbon::now()->startOfMonth()->toDateTimeString();
        $endOfMonthForReport = Carbon::now()->endOfMonth()->toDateTimeString();

        // For weekly report
        $startOfWeek = Carbon::now()->startOfWeek()->toDateTimeString();
        $endOfWeek = Carbon::now()->endOfWeek()->toDateTimeString();

        // For daily report
        $startOfDayForReport = Carbon::now()->startOfDay()->toDateTimeString();
        $endOfDayForReport = Carbon::now()->subDay()->endOfDay()->toDateTimeString();
        $startOfPreviousDay = Carbon::now()->subDay()->startOfDay()->toDateTimeString();

        $endOfDayForReport = Carbon::now()->endOfDay()->toDateTimeString();
        $startOfPreviousDay = Carbon::now()->startOfDay()->toDateTimeString();

        // dd($startOfMonth,$endOfMonth);

        /** Start Jobs Count Graph */
        $must = [];
        $should = [];
        $filter = [];
        $sort = [];

        // $filter[] = [
        // 		'bool' => [
        // 			'should' => [
        // 				[
        // 					'range' => [
        // 						'created_at' => [
        // 							'gte' => $startOfMonth,
        // 							'lte' => $endOfMonth,
        // 						],
        // 					],
        // 				],
        // 				[
        // 					'nested' => [
        // 						'path' => 'job_assign',
        // 						'query' => [
        // 							'range' => [
        // 								'job_assign.created_at' => [
        // 									'gte' => $startOfMonth,
        // 									'lte' => $endOfMonth,
        // 								],
        // 							],
        // 						],
        // 					],
        // 				],
        // 			],
        // 			'minimum_should_match' => 1,
        // 		],
        // ];

        $must[] = [
            'bool' => [
                'should' => [
                    [
                        'term' => [
                            'comId' => $company_id,
                        ],
                    ],

                    [
                        'nested' => [
                            'path' => 'recommendation_relation',
                            'query' => [
                                'bool' => [
                                    'must' => [
                                        [
                                            'term' => [
                                                'recommendation_relation.to_company_id' => $company_id,
                                            ],
                                        ],
                                        [
                                            'terms' => [
                                                'recommendation_relation.final_status' => [1, 2],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        // $must[] = [
        // 	'bool' => [
        // 		'should' => [
        // 			['term' => ['comId' => $company_id]],
        // 			[
        // 				'nested' => [
        // 					'path' => 'recommendation_relation',
        // 					'query' => [
        // 						'term' => ['recommendation_relation.to_company_id' => $company_id],
        // 					],
        // 				],
        // 			],
        // 		],
        // 		'minimum_should_match' => 1,
        // 	],
        // ];

        if (! (($chkAdminRole > 0))) {
            $must[] = [
                'nested' => [
                    'path' => 'job_assign',
                    'query' => [
                        'term' => ['job_assign.user_id' => auth()->user()->id],
                    ],
                ],
            ];
        }

        $params = [
            'index' => ELASTIC_JOBS,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => $filter,
                    ],
                ],
                'aggs' => [
                    'status_counts' => [
                        'terms' => [
                            'field' => 'status', // Use `.keyword` for exact match
                            'size' => 10, // Increase if you have more unique statuses
                        ],
                    ],
                ],
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);
        $bucketData = $response['aggregations']['status_counts']['buckets'] ?? [];
        $this->counts = collect($bucketData)->mapWithKeys(function ($item) {
            return [$item['key'] => $item['doc_count']];
        });

        /** End Jobs Count Graph */

        /** Start Candidate last 7 days */
        $endDate = Carbon::today()->endOfDay();
        $startDate = Carbon::today()->subDays(6)->startOfDay();
        // -------------------------------------------
        // CANDIDATE COUNTS - LAST 7 DAYS
        // -------------------------------------------
        $paramsCandidates = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'bool' => [
                                    'should' => [
                                        // First condition
                                        [
                                            'bool' => [
                                                'must' => [
                                                    ['term' => ['company_id' => (int) $company_id]],
                                                    ['term' => ['ClientComID' => 0]],
                                                ],
                                            ],
                                        ],
                                        // Second condition
                                        [
                                            'bool' => [
                                                'must' => [
                                                    ['term' => ['company_id' => (int) $company_id]],
                                                    ['range' => ['ClientComID' => ['gt' => 0]]],
                                                ],
                                                'must_not' => [
                                                    ['terms' => ['job_master_status_id' => [8, 9]]],
                                                ],
                                            ],
                                        ],
                                        // Third condition
                                        [
                                            'bool' => [
                                                'must' => [
                                                    ['term' => ['ClientComID' => (int) $company_id]],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'minimum_should_match' => 1,
                                ],
                            ],
                        ],
                        'filter' => [
                            [
                                'range' => [
                                    'created_at' => [
                                        'gte' => $startDate->toDateString(),
                                        'lte' => $endDate->toDateString(),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],

                'aggs' => [
                    'uploads_over_time' => [
                        'date_histogram' => [
                            'field' => 'created_at',
                            'calendar_interval' => 'day',
                            'format' => 'yyyy-MM-dd',
                            'min_doc_count' => 0,
                            'extended_bounds' => [
                                'min' => $startDate->toDateString(),
                                'max' => $endDate->toDateString(),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $responseCandidates = $this->elasticService->getClient()->search($paramsCandidates);
        $candidateBuckets = $responseCandidates['aggregations']['uploads_over_time']['buckets'] ?? [];

        // -------------------------------------------
        // BUILD LABELS & VALUES
        // -------------------------------------------
        $labels = [];
        $candidateValues = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $formatted = $date->format('M d'); // e.g., 'Jul 16'
            $labels[] = $formatted;
            // Find matching doc_count from candidates
            $candidateBucket = collect($candidateBuckets)->firstWhere('key_as_string', $date->format('Y-m-d'));

            $candidateValues[] = $candidateBucket['doc_count'] ?? 0;
        }
        $this->labels = $labels;
        $this->candidateValues = $candidateValues;
        $this->candidateValuesTotal = array_sum($candidateValues);

        /*
         * Daily Performance — last-7-days Offers & Joinings series for the KPI cards.
         * $offerDailyValues / $joinDailyValues were never built, so the Offers and Joinings
         * cards always showed 0 regardless of real data. Source = offer_job_applications
         * (same authoritative source as the Monthly Performers joinings fix): a Joining is
         * master_status_id 6 dated by joined_date; an Offer is master_status_id 5/6 dated by
         * created_at. The last element of each array is "today". Reuses the 7-day window
         * ($startDate..$endDate) already set above. Tenancy: raw DB::table is not auto-scoped,
         * so company_id is filtered explicitly (Rule #3).
         */
        $joinDailyMap = DB::table('offer_job_applications')
            ->select(DB::raw('DATE(joined_date) as d'), DB::raw('COUNT(*) as cnt'))
            ->where('company_id', $this->company_id)
            ->whereNull('deleted_at')
            ->where('master_status_id', 6)
            ->whereBetween('joined_date', [$startDate->toDateTimeString(), $endDate->toDateTimeString()])
            ->groupBy(DB::raw('DATE(joined_date)'))
            ->pluck('cnt', 'd')->toArray();

        $offerDailyMap = DB::table('offer_job_applications')
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as cnt'))
            ->where('company_id', $this->company_id)
            ->whereNull('deleted_at')
            ->whereIn('master_status_id', [5, 6])
            ->whereBetween('created_at', [$startDate->toDateTimeString(), $endDate->toDateTimeString()])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('cnt', 'd')->toArray();

        $offerDailyValues = [];
        $joinDailyValues = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $startDate->copy()->addDays($i)->format('Y-m-d');
            $offerDailyValues[] = (int) ($offerDailyMap[$day] ?? 0);
            $joinDailyValues[] = (int) ($joinDailyMap[$day] ?? 0);
        }
        $this->offerDailyValues = $offerDailyValues;
        $this->joinDailyValues = $joinDailyValues;

        /** End Candidate last 7 days */
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        /** Start Candidate list joined*/
        $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(6);
        $endOfMonthcomman = Carbon::now()->endOfMonth();

        $must = [];
        $should = [];
        $filter = [];
        $sort = [];

        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['job_master_status_id' => 6]],
                                ['term' => ['ClientComID' => 0]],
                            ]),
                        ],
                    ],
                    // Second condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['job_master_status_id' => 6]],
                                ['range' => ['ClientComID' => ['gt' => 0]]],
                                ['range' => ['job_id' => ['gt' => 0]]],
                            ]),
                        ],
                    ],
                    // Third condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['ClientComID' => $company_id]],
                                ['term' => ['job_master_status_id' => 6]],
                                ['range' => ['job_id' => ['gt' => 0]]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [
                    [
                        'bool' => [
                            'should' => [
                                [
                                    'range' => [
                                        'of_joined_date' => [
                                            'gte' => $startOfMonthcomman->toDateString(),
                                            'lte' => $endOfMonthcomman->toDateString(),
                                        ],
                                    ],
                                ],
                            ],
                            'minimum_should_match' => 1,
                        ],
                    ],
                ],
            ],
        ];
        $sort[] = ['of_joined_date' => ['order' => 'desc']];
        $params = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'size' => 6,
                'sort' => $sort,
                'query' => $boolQuery,
                'aggs' => [
                    'of_joined_count' => [
                        'date_histogram' => [
                            'field' => 'of_joined_date',
                            'calendar_interval' => 'month',
                            'format' => 'yyyy-MM',
                            'min_doc_count' => 0,
                            'extended_bounds' => [
                                'min' => $startOfMonthcomman->format('Y-m'),
                                'max' => $endOfMonthcomman->format('Y-m'),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);
        $this->total_candidates_join = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);
        $this->data_list = $data;
        $candidateBucketsjoin = $response['aggregations']['of_joined_count']['buckets'] ?? [];

        $candidateValuesCommjoin = [];
        $labelsCommjoin = [];
        $startDateComm = Carbon::now();
        for ($i = 5; $i >= 0; $i--) {
            $startDate = $startDateComm->copy()->startOfMonth()->subMonths($i);
            $endDate = $startDate->copy()->endOfMonth();
            // dd($startDate,$endDate);
            $formattedComm = $startDate->format('M Y'); // e.g., 'Jul 16'
            $labelsCommjoin[] = $formattedComm;
            $candidateBucketjoin = collect($candidateBucketsjoin)->firstWhere('key_as_string', $startDate->format('Y-m'));
            $candidateValuesCommjoin[] = $candidateBucketjoin['doc_count'] ?? 0;

        }
        $this->labelsCommjoin = $labelsCommjoin;
        $this->candidateValuesCommjoin = $candidateValuesCommjoin;
        //  dd($this->candidateValuesCommjoin,$this->labelsCommjoin);
        $this->candidateValuesTotaljoin = array_sum($candidateValuesCommjoin);

        /*
         * Candidates Added Trend wiring (intent): the dashboard "Candidates Added Trend"
         * chart reads $addedMonthLabels / $addedMonthValues (last 6 months) which were
         * never built (blank chart). This runs the SAME company-scoped candidate query
         * used by the 7-day "candidates added" widget, but as a monthly date_histogram
         * on created_at across the last 6 months. Tenancy: scoped by company_id /
         * ClientComID in the should-clauses below.
         */
        $addedStartMonth = Carbon::now()->startOfMonth()->subMonths(5);
        $addedEndMonth = Carbon::now()->endOfMonth();
        $paramsAdded = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'bool' => [
                                    'should' => [
                                        // candidate added directly by this company (no client routing)
                                        ['bool' => ['must' => [
                                            ['term' => ['company_id' => (int) $company_id]],
                                            ['term' => ['ClientComID' => 0]],
                                        ]]],
                                        // candidate added by this company against a client requirement
                                        ['bool' => [
                                            'must' => [
                                                ['term' => ['company_id' => (int) $company_id]],
                                                ['range' => ['ClientComID' => ['gt' => 0]]],
                                            ],
                                            'must_not' => [
                                                ['terms' => ['job_master_status_id' => [8, 9]]],
                                            ],
                                        ]],
                                        // candidate routed to this company as the client
                                        ['bool' => ['must' => [
                                            ['term' => ['ClientComID' => (int) $company_id]],
                                        ]]],
                                    ],
                                    'minimum_should_match' => 1,
                                ],
                            ],
                        ],
                        'filter' => [
                            ['range' => ['created_at' => [
                                'gte' => $addedStartMonth->toDateString(),
                                'lte' => $addedEndMonth->toDateString(),
                            ]]],
                        ],
                    ],
                ],
                'aggs' => [
                    'added_over_time' => [
                        'date_histogram' => [
                            'field' => 'created_at',
                            'calendar_interval' => 'month',
                            'format' => 'yyyy-MM',
                            'min_doc_count' => 0,
                            'extended_bounds' => [
                                'min' => $addedStartMonth->format('Y-m'),
                                'max' => $addedEndMonth->format('Y-m'),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $responseAdded = $this->elasticService->getClient()->search($paramsAdded);
        $addedBuckets = $responseAdded['aggregations']['added_over_time']['buckets'] ?? [];
        $addedMonthLabels = [];
        $addedMonthValues = [];
        for ($i = 5; $i >= 0; $i--) {
            $addedMonthPoint = Carbon::now()->startOfMonth()->subMonths($i);
            $addedMonthLabels[] = $addedMonthPoint->format('M Y');
            $addedMonthBucket = collect($addedBuckets)->firstWhere('key_as_string', $addedMonthPoint->format('Y-m'));
            $addedMonthValues[] = $addedMonthBucket['doc_count'] ?? 0;
        }
        $this->addedMonthLabels = $addedMonthLabels;
        $this->addedMonthValues = $addedMonthValues;

        /** End Candidate list*/

        /** Start Candidate Count Graph */
        $must = [];
        $should = [];
        $filter = [];
        $sort = [];

        $filter[] = [
            'bool' => [
                'should' => [
                    [
                        'range' => [
                            'created_at' => [
                                'gte' => $startOfMonth,
                                'lte' => $endOfMonth,
                            ],
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => (int) $company_id]],
                                ['term' => ['ClientComID' => 0]],
                            ]),
                        ],
                    ],
                    // Second condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => (int) $company_id]],
                                ['range' => ['ClientComID' => ['gt' => 0]]],
                            ], [
                                ['bool' => [
                                    'must_not' => [
                                        ['terms' => ['job_master_status_id' => [8, 9]]],
                                    ],
                                ]],
                            ]),
                        ],
                    ],
                    // Third condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['ClientComID' => (int) $company_id]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [
                    [
                        'bool' => [
                            'should' => [
                                [
                                    'range' => [
                                        'updated_at' => [
                                            'gte' => $startOfMonth,
                                            'lte' => $endOfMonth,
                                        ],
                                    ],
                                ],
                            ],
                            'minimum_should_match' => 1,
                        ],
                    ],
                ],
            ],
        ];

        // dd($startOfMonth,$endOfMonth);

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'size' => 0,
                'query' => $boolQuery,
                'aggs' => [
                    'status_counts' => [
                        'terms' => [
                            'field' => 'job_master_status_id', // Use `.keyword` for exact match
                            'size' => 10, // Increase if you have more unique statuses
                        ],
                    ],
                ],
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);
        $bucketData = $response['aggregations']['status_counts']['buckets'] ?? [];
        $this->result = collect($bucketData)->mapWithKeys(function ($item) {
            return [$item['key'] => $item['doc_count']];
        });

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'size' => 0,
                'query' => $boolQuery,
                'aggs' => [
                    'status_counts' => [
                        'terms' => [
                            'field' => 'job_master_status_id',
                            'size' => 10,
                        ],
                    ],
                    // ✅ Count records where of_created_date is in range
                    'of_created_date_count' => [
                        'filter' => [
                            'range' => [
                                'of_created_date' => [
                                    'gte' => $startOfMonth,
                                    'lte' => $endOfMonth,
                                ],
                            ],
                        ],
                    ],
                    // ✅ Count records where of_joined_date is in range
                    'of_joined_date_count' => [
                        'filter' => [
                            'range' => [
                                'of_joined_date' => [
                                    'gte' => $startOfMonth,
                                    'lte' => $endOfMonth,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);

        // existing status buckets
        $bucketData = $response['aggregations']['status_counts']['buckets'] ?? [];
        $this->resultGet = collect($bucketData)->mapWithKeys(function ($item) {
            return [$item['key'] => $item['doc_count']];
        });

        // ✅ existing status counts
        $this->joined_count = $this->resultGet->get('6', 0);
        $this->offer_count = $this->resultGet->get('5', 0);

        // ✅ new separate counts
        $this->of_created_count = $this->offer_count; // $response['aggregations']['of_created_date_count']['doc_count'] ?? 0;
        $this->of_joined_count = $this->joined_count; // $response['aggregations']['of_joined_date_count']['doc_count'] ?? 0;

        $this->joined_count = $this->of_joined_count;
        $this->sourcing_count = $this->result->get('1', 0);
        $this->screening_count = $this->result->get('2', 0);
        $this->evaluation_count = $this->result->get('3', 0);
        $this->negotiation_count = $this->result->get('4', 0);
        $this->offer_count = $this->of_created_count;
        $this->Rejected_count = $this->result->get('7', 0);
        $this->inreview_count = $this->result->get('8', 0);

        // dd($this->offer_count);

        // $this->total_candidates = collect($this->result)->sum();
        $this->total_candidates = $this->inreview_count + $this->sourcing_count + $this->screening_count + $this->evaluation_count + $this->negotiation_count + $this->offer_count + $this->joined_count;

        /*
         * Candidates Funnel (Last 6 Months) — TRUE 6-month cohort.
         * The $this->result counts above are filtered by updated_at within the CURRENT
         * month, so they do NOT match this card's "Last 6 Months" label. Here we run a
         * status_counts aggregation over candidates CREATED in the last 6 months (the same
         * company scope as the Added Trend) and break that cohort down by current status.
         * $candidatesAdded6mTotal is the headline total of candidates added in those 6 months
         * (shown above the funnel). Tenancy: company_id / ClientComID in the should-clauses.
         */
        $funnelScope = [
            'bool' => [
                'must' => [[
                    'bool' => [
                        'should' => [
                            ['bool' => ['must' => [
                                ['term' => ['company_id' => (int) $company_id]],
                                ['term' => ['ClientComID' => 0]],
                            ]]],
                            ['bool' => [
                                'must' => [
                                    ['term' => ['company_id' => (int) $company_id]],
                                    ['range' => ['ClientComID' => ['gt' => 0]]],
                                ],
                                'must_not' => [['terms' => ['job_master_status_id' => [8, 9]]]],
                            ]],
                            ['bool' => ['must' => [
                                ['term' => ['ClientComID' => (int) $company_id]],
                            ]]],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ]],
                'filter' => [
                    ['range' => ['created_at' => [
                        'gte' => $addedStartMonth->toDateString(),
                        'lte' => $addedEndMonth->toDateString(),
                    ]]],
                ],
            ],
        ];
        $funnelResponse = $this->elasticService->getClient()->search([
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'size' => 0,
                'query' => $funnelScope,
                'aggs' => [
                    'status_counts' => ['terms' => ['field' => 'job_master_status_id', 'size' => 20]],
                ],
            ],
        ]);
        $funnel6mResult = collect($funnelResponse['aggregations']['status_counts']['buckets'] ?? [])
            ->mapWithKeys(fn ($b) => [(string) $b['key'] => $b['doc_count']]);
        $this->funnel6m = [
            'inreview' => (int) $funnel6mResult->get('8', 0),
            'sourcing' => (int) $funnel6mResult->get('1', 0),
            'screening' => (int) $funnel6mResult->get('2', 0),
            'evaluation' => (int) $funnel6mResult->get('3', 0),
            // 'offer' & 'joined' below are placeholders — they are overridden further down with the
            // actual offers/joinings that OCCURRED in the last 6 months (by offer/joined date), to match
            // the Offers/Joinings Trend charts. Current-status counts under-report these events.
            'offer' => (int) $funnel6mResult->get('5', 0),
            'joined' => (int) $funnel6mResult->get('6', 0),
        ];
        // Total candidates added in the last 6 months (matches the Added Trend chart total).
        $this->candidatesAdded6mTotal = (int) array_sum($addedMonthValues);

        $counts = collect([
            'Joined' => $this->result->get('6', 0),
            'Sourcing' => $this->result->get('1', 0),
            'Screening' => $this->result->get('2', 0),
            'Evaluation' => $this->result->get('3', 0),
            'Negotiation' => $this->result->get('4', 0),
            'Offer' => $this->result->get('5', 0),
            'Rejected' => $this->result->get('7', 0),
            'In Review' => $this->result->get('8', 0),
        ]);
        $sorted = $counts->sortDesc();

        $this->funnel_chart = $sorted->map(function ($value, $label) {
            return [
                'name' => $label,
                'value' => $value,
            ];
        })->values(); // values() resets keys for proper JSON structure

        // dd(json_encode($this->funnel_chart));

        /** End interview Count Graph */
        if ($chkAdminRole) {
            //  dd($this->funnel_chart,$this->result);
        }

        /** End Candidate count Graph */

        /** Start interview Count Graph */
        $must = [];
        $should = [];
        $filter = [];
        $sort = [];

        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['ClientComID' => 0]],
                            ]),
                        ],
                    ],
                    // Second condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['range' => ['ClientComID' => ['gt' => 0]]],
                                ['range' => ['job_id' => ['gt' => 0]]],
                            ], [
                                ['bool' => [
                                    'must_not' => [
                                        ['terms' => ['job_master_status_id' => [8, 9]]],
                                    ],
                                ]],
                            ]),
                        ],
                    ],
                    // Third condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['ClientComID' => $company_id]],
                                ['range' => ['job_id' => ['gt' => 0]]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [
                    [
                        'bool' => [
                            'should' => [
                                [
                                    'range' => [
                                        'schedule_date' => [
                                            'gte' => $startOfMonth,
                                            'lte' => $endOfMonth,
                                        ],
                                    ],
                                ],
                            ],
                            'minimum_should_match' => 1,
                        ],
                    ],
                ], // We'll add all filters here
            ],
        ];

        if (! ($chkAdminRole > 0)) {
            $boolQuery['bool']['filter'][] = [
                'term' => ['submitted_by' => $this->user->id],
            ];
        }

        $params = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'size' => 0,
                'query' => $boolQuery,
                'aggs' => [
                    'status_counts' => [
                        'terms' => [
                            'field' => 'master_status_id', // Use `.keyword` for exact match
                            'size' => 10, // Increase if you have more unique statuses
                        ],
                    ],
                ],
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);
        $bucketData = $response['aggregations']['status_counts']['buckets'] ?? [];
        $this->counts_interview = collect($bucketData)->mapWithKeys(function ($item) {
            return [$item['key'] => $item['doc_count']];
        });

        $this->allinterviewCount = collect($this->counts_interview)->sum();
        // dd($totalCount);

        /** Start Department Count Graph */
        $job_categories = DB::table('job_categories')
            ->where('company_id', $company_id)
            ->pluck('name', 'id')
            ->toArray();
        $mustFilters = [
            ['term' => ['comId' => $company_id]],
            ['term' => ['status' => 'active']],
        ];

        // Include shared jobs as well (OR condition in SQL)
        $shouldFilters = [];

        $params = [
            'index' => ELASTIC_JOBS, // use your actual index
            'size' => 0, // we want only aggregation
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => $mustFilters,
                        'should' => $shouldFilters,
                        'minimum_should_match' => 0,
                    ],
                ],
                'aggs' => [
                    'job_categories' => [
                        'terms' => [
                            'field' => 'category_id',
                            'size' => 100,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);
        $bucketData = $response['aggregations']['job_categories']['buckets'] ?? [];

        $counts = collect($bucketData)->mapWithKeys(function ($item) {
            return [(int) $item['key'] => $item['doc_count']];
        })->toArray();
        if (empty($counts) && array_sum($counts) <= 0) {
            $this->department_true = true;
        } else {
            $this->department_true = false;
        }
        $colorPalette = [
            '#4CAF50', '#F44336', '#2196F3', '#FF9800', '#9C27B0',
            '#E91E63', '#FFC107', '#00BCD4', '#795548', '#607D8B',
            '#009688', '#CDDC39', '#FFEB3B', '#8BC34A', '#03A9F4',
            '#E91E63', '#FFC107', '#00BCD4', '#795548', '#607D8B',
            '#4CAF50', '#F44336', '#2196F3', '#FF9800', '#9C27B0',
        ];

        $departmentArray = [['Department', 'Jobs', ['role' => 'style']]];

        $index = 0;
        foreach ($job_categories as $id => $name) {
            $count = $counts[$id] ?? 0;
            $color = $colorPalette[$index % count($colorPalette)];
            $departmentArray[] = [$name, $count, $color];
            $index++;
        }

        $jsonStringdepartmentwisejob = json_encode($departmentArray);
        $this->graphobsdepartmentwise = json_decode($jsonStringdepartmentwisejob, true);

        // dd($this->graphobsdepartmentwise);

        /** End Department Count Graph */

        /** Start Offer Count Graph */
        $must = [];
        $should = [];
        $filter = [];
        $sort = [];

        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['ClientComID' => 0]],
                            ]),
                        ],
                    ],
                    // Second condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['range' => ['ClientComID' => ['gt' => 0]]],
                                ['range' => ['job_id' => ['gt' => 0]]],
                            ], [
                                ['bool' => [
                                    'must_not' => [
                                        ['terms' => ['job_master_status_id' => [8, 9]]],
                                    ],
                                ]],
                            ]),
                        ],
                    ],
                    // Third condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['ClientComID' => $company_id]],
                                ['range' => ['job_id' => ['gt' => 0]]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [
                    [
                        'bool' => [
                            'should' => [
                                [
                                    'range' => [
                                        'of_created_date' => [
                                            'gte' => $startOfMonthcomman->toDateString(),
                                            'lte' => $endOfMonthcomman->toDateString(),
                                        ],
                                    ],
                                ],
                            ],
                            'minimum_should_match' => 1,
                        ],
                    ],
                ],
            ],
        ];

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'size' => 0,
                'query' => $boolQuery,
                'aggs' => [
                    'of_offer_count' => [
                        'date_histogram' => [
                            'field' => 'of_created_date',
                            'calendar_interval' => 'month',
                            'format' => 'yyyy-MM',
                            'min_doc_count' => 0,
                            'extended_bounds' => [
                                'min' => $startOfMonthcomman->format('Y-m'),
                                'max' => $endOfMonthcomman->format('Y-m'),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);
        $candidateBucketsoffer = $response['aggregations']['of_offer_count']['buckets'] ?? [];

        $candidateValuesCommoffer = [];
        $labelsCommoffer = [];
        $startDateComm = Carbon::now();
        for ($i = 5; $i >= 0; $i--) {
            $startDate = $startDateComm->copy()->startOfMonth()->subMonths($i);
            $endDate = $startDate->copy()->endOfMonth();
            // dd($startDate,$endDate);
            $formattedComm = $startDate->format('M Y'); // e.g., 'Jul 16'
            $labelsCommoffer[] = $formattedComm;
            $candidateBucketoffer = collect($candidateBucketsoffer)->firstWhere('key_as_string', $startDate->format('Y-m'));
            $candidateValuesCommoffer[] = $candidateBucketoffer['doc_count'] ?? 0;

        }
        $this->labelsCommoffer = $labelsCommoffer;
        $this->candidateValuesCommoffer = $candidateValuesCommoffer;
        //  dd($this->candidateValuesCommoffer,$this->labelsCommoffer);
        $this->candidateValuesTotaloffer = array_sum($candidateValuesCommoffer);

        /*
         * Funnel Offers & Joined fix: show offers RELEASED and candidates JOINED within the last
         * 6 months (by offer date / joined date) — the same totals as the Offers/Joinings Trend
         * charts and the Offer Acceptance section — instead of "created-in-6-months candidates
         * currently in that status", which under-reported them. Both 6-month series exist by now.
         */
        $f6 = $this->funnel6m;
        $f6['offer'] = (int) array_sum($candidateValuesCommoffer);
        $f6['joined'] = (int) array_sum($candidateValuesCommjoin);
        $this->funnel6m = $f6;
        //  dd($labelsCommoffer,$candidateValuesCommoffer);

        /** End Offer count Graph */

        /** Start Interview Section */
        $page = 1;
        $size = 5; // Default 20 results per page
        $from = ($page - 1) * $size;
        $must = [];
        $should = [];
        $filter = [];
        $nowMinus1Hour = now()->subHour()->toIso8601String();
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

        $upcomingParams = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => [
                    ['schedule_date' => ['order' => 'asc']],
                ],
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => [
                            [
                                'range' => [
                                    'schedule_date' => [
                                        'gte' => $nowMinus1Hour,
                                        'format' => 'strict_date_optional_time',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $upcomingData = [];
        $upcomingTotal = 0;

        if ($this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
            $response = $this->elasticService->getClient()->search($upcomingParams);
            $upcomingTotal = $response['hits']['total']['value'] ?? 0;
            $upcomingData = array_map(fn ($hit) => $hit['_source'], $response['hits']['hits']);
        }
        $this->upcomingData = $upcomingData;

        // ////////////////////////////////// last 7 days
        $endDate = Carbon::today()->endOfDay();
        $startDate = Carbon::today()->subDays(6)->startOfDay();
        $recentParams = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => [
                    ['schedule_date' => ['order' => 'desc']],
                ],
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => [
                            [
                                'range' => [
                                    'schedule_date' => [
                                        'gte' => $startDate->toDateString(),
                                        'lte' => $endDate->toDateString(),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'aggs' => [
                    'uploads_over_time' => [
                        'date_histogram' => [
                            'field' => 'schedule_date',
                            'calendar_interval' => 'day',
                            'format' => 'yyyy-MM-dd',
                            'min_doc_count' => 0,
                            'extended_bounds' => [
                                'min' => $startDate->toDateString(),
                                'max' => $endDate->toDateString(),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $recentData = [];
        $recentTotal = 0;

        if ($this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
            $response = $this->elasticService->getClient()->search($recentParams);
            $interviewBuckets = $response['aggregations']['uploads_over_time']['buckets'] ?? [];
            $recentTotal = $response['hits']['total']['value'] ?? 0;
            $recentData = array_map(fn ($hit) => $hit['_source'], $response['hits']['hits']);
        }
        $this->recentData = $recentData;

        $interviewlabels = [];
        $interviewValues = [];

        for ($i = 0; $i < 7; $i++) {
            // dd($date->format('Y-m-d'));
            $date = $startDate->copy()->addDays($i);
            $formatted = $date->format('M d'); // e.g., 'Jul 16'
            $interviewlabels[] = $formatted;
            // Find matching doc_count from candidates
            $interviewBucket = collect($interviewBuckets)->firstWhere('key_as_string', $date->format('Y-m-d'));

            $interviewValues[] = $interviewBucket['doc_count'] ?? 0;
        }
        $this->interviewlabels = $interviewlabels;
        $this->interviewValues = $interviewValues;
        $this->interviewValuesTotal = array_sum($interviewValues);

        // ////////////////////////////////// last 6 months
        $startDateComm = Carbon::now();
        $startDate = $startDateComm->copy()->startOfMonth()->subMonths(5);
        $endDate = $startDateComm;
        $recentParamsMonth = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => [
                    ['schedule_date' => ['order' => 'desc']],
                ],
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => [
                            [
                                'range' => [
                                    'schedule_date' => [
                                        'gte' => $startDate->toDateString(),
                                        'lte' => $endDate->toDateString(),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'aggs' => [
                    'uploads_over_time' => [
                        'date_histogram' => [
                            'field' => 'schedule_date',
                            'calendar_interval' => 'month',
                            'format' => 'yyyy-MM-dd',
                            'min_doc_count' => 0,
                            'extended_bounds' => [
                                'min' => $startDate->toDateString(),
                                'max' => $endDate->toDateString(),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $recentDataMonth = [];
        $recentTotalMonth = 0;

        if ($this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
            $responseMonth = $this->elasticService->getClient()->search($recentParamsMonth);
            $interviewBucketsMonth = $responseMonth['aggregations']['uploads_over_time']['buckets'] ?? [];
            $recentTotalMonth = $responseMonth['hits']['total']['value'] ?? 0;
            $recentDataMonth = array_map(fn ($hit) => $hit['_source'], $responseMonth['hits']['hits']);
        }
        $this->recentDataMonth = $recentDataMonth;

        // dd($this->recentDataMonth);

        $interviewlabelsMonth = [];
        $interviewValuesMonth = [];

        for ($i = 5; $i >= 0; $i--) {
            $startDate = $startDateComm->copy()->startOfMonth()->subMonths($i);
            // dd($startDate->format('Y-m'),$this->recentDataMonth);
            $formatted = $startDate->format('M Y'); // e.g., 'Jul 16'
            $interviewlabelsMonth[] = $formatted;
            $interviewBucket = collect($interviewBucketsMonth)->firstWhere('key_as_string', $startDate->format('Y-m-01'));
            // collect($interviewBucketsMonth)->firstWhere('key_as_string', $startDate->format('Y-m'));

            $interviewValuesMonth[] = $interviewBucket['doc_count'] ?? 0;
        }
        $this->interviewlabelsMonth = $interviewlabelsMonth;
        $this->interviewValuesMonth = $interviewValuesMonth;
        $this->interviewValuesTotalMonth = array_sum($interviewValuesMonth);

        // dd($recentData,$this->labels,$this->interviewValues,$this->interviewValuesTotal);

        /** End Interview Section */

        /** Start Subscription Section */
        $this->coin_transection_logs = DB::table('coin_transection_logs')
            ->join('users', 'users.id', '=', 'coin_transection_logs.event_by')
            ->leftJoin('companies', 'companies.id', '=', 'coin_transection_logs.company_id')
            ->select('coin_transection_logs.*', 'users.name', 'companies.company_name')
            ->orderByDesc('coin_transection_logs.id')
            ->where('coin_transection_logs.event_type', '=', 'operational')
            ->where('coin_transection_logs.coins', '>', 0)
            ->where(function ($query) use ($company_id) {
                $query->where('coin_transection_logs.company_id', $company_id);
            })->take(5)->cursor();

        $actual_count = DB::table('coin_transection_logs')
            ->select('coin_transection_logs.coins')
            ->orderByDesc('coin_transection_logs.id')
            ->where('coin_transection_logs.event_type', '=', 'operational')
            ->where('coin_transection_logs.transection_type', '=', 'credit')
            ->where(function ($query) use ($company_id) {
                $query->where('coin_transection_logs.company_id', $company_id);
            })->first();

        $remain_coins = DB::table('company_coins')
            ->select('company_coins.coins')
            ->orderByDesc('company_coins.id')
            ->where('company_coins.event_type', '=', 'operational')
            ->where(function ($query) use ($company_id) {
                $query->where('company_coins.company_id', $company_id);
            })->first();
        $this->actual_count = $actual_count->coins ?? 0;
        $this->remain_coins = $remain_coins->coins ?? 0;

        $this->coin_transection_sales_logs = DB::table('coin_transection_logs')
            ->join('users', 'users.id', '=', 'coin_transection_logs.event_by')
            ->leftJoin('companies', 'companies.id', '=', 'coin_transection_logs.company_id')
            ->select('coin_transection_logs.*', 'users.name', 'companies.company_name')
            ->orderByDesc('coin_transection_logs.id')
            ->where('coin_transection_logs.event_type', '=', 'sales')
            ->where('coin_transection_logs.coins', '>', 0)
            ->where(function ($query) use ($company_id) {
                $query->where('coin_transection_logs.company_id', $company_id);
            })->take(5)->cursor();

        $actual_count_sales = DB::table('coin_transection_logs')
            ->select('coin_transection_logs.coins')
            ->orderByDesc('coin_transection_logs.id')
            ->where('coin_transection_logs.event_type', '=', 'sales')
            ->where('coin_transection_logs.transection_type', '=', 'credit')
            ->where(function ($query) use ($company_id) {
                $query->where('coin_transection_logs.company_id', $company_id);
            })->first();

        $remain_coins_sales = DB::table('company_coins')
            ->select('company_coins.coins')
            ->orderByDesc('company_coins.id')
            ->where('company_coins.event_type', '=', 'sales')
            ->where(function ($query) use ($company_id) {
                $query->where('company_coins.company_id', $company_id);
            })->first();
        $this->actual_count_sales = $actual_count_sales->coins ?? 0;
        $this->remain_coins_sales = $remain_coins_sales->coins ?? 0;

        // dd($actual_count,$remain_coins);

        // dd($this->coin_transection_logs);

        $company_id = auth()->user()->company_id;
        $loggedInUserId = auth()->user()->id;

        // Step 1: Get all users with sourcing count within date range
        $startOfPreviousDay = Carbon::parse($startOfPreviousDay)->toIso8601String();
        $endOfDayForReport = Carbon::parse($endOfDayForReport)->toIso8601String();
        $startOfMonthForReport = Carbon::parse($startOfMonthForReport)->toIso8601String();
        $endOfMonthForReport = Carbon::parse($endOfMonthForReport)->toIso8601String();

        $rankstartOfMonthForReport = Carbon::parse($rankstartOfMonthForReport)->toIso8601String();
        $rankendOfMonthForReport = Carbon::parse($rankendOfMonthForReport)->toIso8601String();

        // Start daily 1: Get top users by candidate count (excluding current user)
        $userSearchPayload = [
            'index' => ELASTIC_USERS_LIST,
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['term' => ['company_id' => $company_id]],
                            ['term' => ['status' => 'active']],
                        ],
                    ],
                ],
                '_source' => ['id', 'name', 'email', 'designation'],
                'size' => 10000,
                'sort' => [
                    ['id' => ['order' => 'asc']],
                    ['status' => ['order' => 'asc']],
                ],
            ],
        ];

        $userResponse = $this->elasticService->getClient()->search($userSearchPayload);
        $users = collect($userResponse['hits']['hits'])->pluck('_source');

        // Step 2: Get candidate counts by user_id from ELASTIC_CANDIDATES for this month
        $candidateAggPayload = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['range' => [
                                'created_at' => [
                                    'gte' => $startOfPreviousDay,
                                    'lte' => $endOfDayForReport,
                                ],
                            ]],
                            ['term' => ['company_id' => $company_id]],
                        ],
                    ],
                ],
                'aggs' => [
                    'user_uploads' => [
                        'terms' => [
                            'field' => 'submitted_by',
                            'size' => 10000,
                            'order' => ['_count' => 'desc'],
                        ],
                    ],
                ],
            ],
        ];

        $candidateResponse = $this->elasticService->getClient()->search($candidateAggPayload);
        $aggBuckets = $candidateResponse['aggregations']['user_uploads']['buckets'];

        // Step 3: Map counts to users and sort
        $uploadsMap = collect($aggBuckets)->mapWithKeys(function ($item) {
            return [$item['key'] => $item['doc_count']];
        });

        $rankedUsers = $users->map(function ($user) use ($uploadsMap) {
            // Intent: Null-coalesce user id to prevent undefined array key exceptions when Elasticsearch hits omit 'id'
            $userId = $user['id'] ?? null;
            $user['upload_count'] = $uploadsMap[$userId] ?? 0;

            return $user;
        })->sortByDesc('upload_count')->values();

        // Add rank (starting from 1)
        $rankedUsers = $rankedUsers->values()->map(function ($user, $index) {
            $user['rank'] = $index + 1;

            return $user;
        });

        $topUsers = $rankedUsers->take(4);
        // Step 5: Find the current logged-in user in the ranked list

        $teamDailyLabels = [];
        $tealDailyValues = [];

        if (count($topUsers) > 0) {
            foreach ($topUsers as $key => $val_ob) {
                $val = (object) $val_ob;
                $teamDailyLabels[] = ucwords($val->name);
                $tealDailyValues[] = $val->upload_count ?? 0;
            }
        }
        $this->teamDailyLabels = $teamDailyLabels;
        $this->tealDailyValues = $tealDailyValues;
        $this->graph_daily_report = $topUsers;

        // dd($this->graph_daily_report);

        // End daily 1: Get top users by candidate count (excluding current user)

        // Start Monthly: Get all users with sourcing count within date range

        $userSearchPayload_month = [
            'index' => ELASTIC_USERS_LIST,
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['term' => ['company_id' => $company_id]],
                            ['term' => ['status' => 'active']],
                        ],
                    ],
                ],
                '_source' => ['id', 'name', 'email', 'designation'],
                'size' => 10000,
                'sort' => [
                    ['id' => ['order' => 'asc']],
                    ['status' => ['order' => 'asc']],
                ],
            ],
        ];

        $userResponse_month = $this->elasticService->getClient()->search($userSearchPayload_month);
        $users = collect($userResponse_month['hits']['hits'])->pluck('_source');

        // Step 2: Get candidate counts by user_id from ELASTIC_CANDIDATES for this month

        // Step 1: Build the reusable $boolQuery
        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['ClientComID' => 0]],
                            ]),
                        ],
                    ],
                    // Second condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['range' => ['ClientComID' => ['gt' => 0]]],
                                ['range' => ['job_id' => ['gt' => 0]]],
                            ], [
                                ['bool' => [
                                    'must_not' => [
                                        ['terms' => ['job_master_status_id' => [8, 9]]],
                                    ],
                                ]],
                            ]),
                        ],
                    ],
                    // Third condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['ClientComID' => $company_id]],
                                ['range' => ['job_id' => ['gt' => 0]]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [
                    ['range' => [
                        'of_joined_date' => [
                            'gte' => $startOfMonthForReport,
                            'lte' => $endOfMonthForReport,
                        ],
                    ]],
                    ['term' => ['job_master_status_id' => 6]],
                ],
            ],
        ];
        // dd($startOfMonthForReport,$endOfMonthForReport);
        // Step 2: Final candidate aggregation payload with query and aggregation
        $candidateAggPayload_month = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'size' => 0,
                'query' => $boolQuery, // use merged query directly
                'aggs' => [
                    'user_uploads' => [
                        'terms' => [
                            'field' => 'submitted_by',
                            'size' => 10000,
                            'order' => ['_count' => 'desc'],
                        ],
                    ],
                ],
            ],
        ];

        $candidateResponse_month = $this->elasticService->getClient()->search($candidateAggPayload_month);
        $aggBuckets_month = $candidateResponse_month['aggregations']['user_uploads']['buckets'];

        // Step 3: Map counts to users and sort
        $uploadsMap_month = collect($aggBuckets_month)->mapWithKeys(function ($item) {
            return [$item['key'] => $item['doc_count']];
        });

        $rankedUsers_month = $users->map(function ($user_month) use ($uploadsMap_month) {
            // Intent: Null-coalesce user id to prevent undefined array key exceptions when Elasticsearch hits omit 'id'
            $userMonthId = $user_month['id'] ?? null;
            $user_month['upload_count'] = $uploadsMap_month[$userMonthId] ?? 0;

            return $user_month;
        })->sortByDesc('upload_count')->values();

        // Add rank (starting from 1)
        $rankedUsers_month = $rankedUsers_month->values()->map(function ($user_month, $index_month) {
            $user_month['rank'] = $index_month + 1;

            return $user_month;
        });

        $topUsers_month = $rankedUsers_month->take(4);

        // dd($currentUser_month);

        $teamMonthlyLabels = [];
        $tealMonthlyValues = [];

        if (count($topUsers_month) > 0) {
            foreach ($topUsers_month as $key => $val_ob) {
                $val = (object) $val_ob;
                $teamMonthlyLabels[] = ucwords($val->name);
                $tealMonthlyValues[] = $val->upload_count ?? 0;
            }
        }
        $this->teamMonthlyLabels = $teamMonthlyLabels;
        $this->tealMonthlyValues = $tealMonthlyValues;
        $this->graph_monthly_report = $topUsers;

        // End Monthly: Get all users with sourcing count within date range

        // Start Your Internal Rank: Get all users with count within date range

        // Step 1: Fetch all users
        $userSearchPayload_rank = [
            'index' => ELASTIC_USERS_LIST,
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['term' => ['company_id' => $company_id]],
                            // ['term' => ['status' => 'active']],
                        ],
                    ],
                ],
                '_source' => ['id', 'name', 'email', 'designation', 'status'],
                'size' => 10000,
                'sort' => [
                    ['id' => ['order' => 'asc']],
                    ['status' => ['order' => 'asc']],
                ],
            ],
        ];

        $userResponse_rank = $this->elasticService->getClient()->search($userSearchPayload_rank);
        $usersteam = collect($userResponse_rank['hits']['hits'])->pluck('_source');

        $rankQuery = [
            'index' => ELASTIC_TEAM_RANK,
            'body' => [
                'from' => 0,
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['term' => ['doc.company_id' => $company_id]],
                            ['range' => [
                                'doc.created_at' => [
                                    'gte' => $rankstartOfMonthForReport,
                                    'lte' => $rankendOfMonthForReport,
                                ],
                            ]],
                        ],
                    ],
                ],
                'aggs' => [
                    'user_scores' => [
                        'terms' => [
                            'field' => 'doc.user_id',
                            'size' => 10000,
                        ],
                        'aggs' => [
                            'profileUpload_sum' => ['sum' => ['field' => 'doc.profileUpload']],
                            'interviewScheduled_sum' => ['sum' => ['field' => 'doc.interviewScheduled']],
                            'interviewNoShow_sum' => ['sum' => ['field' => 'doc.interviewNoShow']],
                            'offerReleased_sum' => ['sum' => ['field' => 'doc.offerReleased']],
                            'candidateJoined_sum' => ['sum' => ['field' => 'doc.candidateJoined']],
                            'interestedCandidates_sum' => ['sum' => ['field' => 'doc.interestedCandidates']],
                            'notInterestedCandidates_sum' => ['sum' => ['field' => 'doc.notInterestedCandidates']],
                            'teamCaptainInterviewScheduled_sum' => ['sum' => ['field' => 'doc.teamCaptainInterviewScheduled']],
                            'teamCaptainInterviewNoShow_sum' => ['sum' => ['field' => 'doc.teamCaptainInterviewNoShow']],
                            'teamCaptainOfferReleased_sum' => ['sum' => ['field' => 'doc.teamCaptainOfferReleased']],
                            'teamCaptainCandidateJoined_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoined']],
                            'sourcing_sum' => ['sum' => ['field' => 'doc.sourcing']],
                            'screening_sum' => ['sum' => ['field' => 'doc.screening']],
                            'evaluation_sum' => ['sum' => ['field' => 'doc.evaluation']],
                            'negotiation_sum' => ['sum' => ['field' => 'doc.negotiation']],
                            'backout_sum' => ['sum' => ['field' => 'doc.backout']],
                            'backoutTeam_sum' => ['sum' => ['field' => 'doc.backoutTeam']],
                            'candidateJoinedContract_sum' => ['sum' => ['field' => 'doc.candidateJoinedContract']],
                            'teamCaptainCandidateJoinedContract_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoinedContract']],
                            'backoutContract_sum' => ['sum' => ['field' => 'doc.backoutContract']],
                            'backoutTeamContract_sum' => ['sum' => ['field' => 'doc.backoutTeamContract']],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($rankQuery);
        $buckets = $response['aggregations']['user_scores']['buckets'];

        $scoreMap = collect($buckets)->mapWithKeys(function ($item) {
            $score =
                ($item['profileUpload_sum']['value'] ?? 0) +
                ($item['interviewScheduled_sum']['value'] ?? 0) +
                ($item['interviewNoShow_sum']['value'] ?? 0) +
                ($item['offerReleased_sum']['value'] ?? 0) +
                ($item['candidateJoined_sum']['value'] ?? 0) +
                ($item['teamCaptainInterviewScheduled_sum']['value'] ?? 0) +
                ($item['teamCaptainOfferReleased_sum']['value'] ?? 0) +
                ($item['teamCaptainCandidateJoined_sum']['value'] ?? 0) +
                ($item['sourcing_sum']['value'] ?? 0) +
                ($item['screening_sum']['value'] ?? 0) +
                ($item['evaluation_sum']['value'] ?? 0) +
                ($item['negotiation_sum']['value'] ?? 0) +
                ($item['backout_sum']['value'] ?? 0) +
                ($item['backoutTeam_sum']['value'] ?? 0) + ($item['candidateJoinedContract_sum']['value'] ?? 0) + ($item['teamCaptainCandidateJoinedContract_sum']['value'] ?? 0) + ($item['backoutContract_sum']['value'] ?? 0) + ($item['backoutTeamContract_sum']['value'] ?? 0);

            return [$item['key'] => [
                'user_id' => $item['key'],
                'profileUpload' => ($item['profileUpload_sum']['value'] ?? 0),
                'interviewScheduled' => ($item['interviewScheduled_sum']['value'] ?? 0),
                'interviewNoShow' => ($item['interviewNoShow_sum']['value'] ?? 0),
                'offerReleased' => ($item['offerReleased_sum']['value'] ?? 0),
                'candidateJoined' => ($item['candidateJoined_sum']['value'] ?? 0),
                'teamCaptainInterviewScheduled' => ($item['teamCaptainInterviewScheduled_sum']['value'] ?? 0),
                'teamCaptainOfferReleased' => ($item['teamCaptainOfferReleased_sum']['value'] ?? 0),
                'teamCaptainCandidateJoined' => ($item['teamCaptainCandidateJoined_sum']['value'] ?? 0),
                'sourcing' => ($item['sourcing_sum']['value'] ?? 0),
                'screening' => ($item['screening_sum']['value'] ?? 0),
                'evaluation' => ($item['evaluation_sum']['value'] ?? 0),
                'negotiation' => ($item['negotiation_sum']['value'] ?? 0),
                'backout' => ($item['backout_sum']['value'] ?? 0),
                'backoutTeam' => ($item['backoutTeam_sum']['value'] ?? 0),
                'candidateJoinedContract' => ($item['candidateJoinedContract_sum']['value'] ?? 0),
                'teamCaptainCandidateJoinedContract' => ($item['teamCaptainCandidateJoinedContract_sum']['value'] ?? 0),
                'backoutContract' => ($item['backoutContract_sum']['value'] ?? 0),
                'backoutTeamContract' => ($item['backoutTeamContract_sum']['value'] ?? 0),
                'score' => $score,
            ]];
        });

        $rankedUsers = $usersteam->map(function ($userteam) use ($scoreMap) {
            // Intent: Null-coalesce user id to prevent undefined array key exceptions when Elasticsearch hits omit 'id'
            $user_id = $userteam['id'] ?? null;
            $userteam['profileUpload'] = $scoreMap[$user_id]['profileUpload'] ?? 0;
            $userteam['interviewScheduled'] = $scoreMap[$user_id]['interviewScheduled'] ?? 0;
            $userteam['interviewNoShow'] = $scoreMap[$user_id]['interviewNoShow'] ?? 0;
            $userteam['offerReleased'] = $scoreMap[$user_id]['offerReleased'] ?? 0;
            $userteam['candidateJoined'] = $scoreMap[$user_id]['candidateJoined'] ?? 0;
            $userteam['interestedCandidates'] = $scoreMap[$user_id]['interestedCandidates'] ?? 0;
            $userteam['notInterestedCandidates'] = $scoreMap[$user_id]['notInterestedCandidates'] ?? 0;
            $userteam['teamCaptainInterviewScheduled'] = $scoreMap[$user_id]['teamCaptainInterviewScheduled'] ?? 0;
            $userteam['teamCaptainInterviewNoShow'] = $scoreMap[$user_id]['teamCaptainInterviewNoShow'] ?? 0;
            $userteam['teamCaptainOfferReleased'] = $scoreMap[$user_id]['teamCaptainOfferReleased'] ?? 0;
            $userteam['teamCaptainCandidateJoined'] = $scoreMap[$user_id]['teamCaptainCandidateJoined'] ?? 0;
            $userteam['sourcing'] = $scoreMap[$user_id]['sourcing'] ?? 0;
            $userteam['screening'] = $scoreMap[$user_id]['screening'] ?? 0;
            $userteam['evaluation'] = $scoreMap[$user_id]['evaluation'] ?? 0;
            $userteam['negotiation'] = $scoreMap[$user_id]['negotiation'] ?? 0;
            $userteam['backout'] = $scoreMap[$user_id]['backout'] ?? 0;
            $userteam['backoutTeam'] = $scoreMap[$user_id]['backoutTeam'] ?? 0;
            $userteam['candidateJoinedContract'] = $scoreMap[$user_id]['candidateJoinedContract'] ?? 0;
            $userteam['teamCaptainCandidateJoinedContract'] = $scoreMap[$user_id]['teamCaptainCandidateJoinedContract'] ?? 0;
            $userteam['backoutContract'] = $scoreMap[$user_id]['backoutContract'] ?? 0;
            $userteam['backoutTeamContract'] = $scoreMap[$user_id]['backoutTeamContract'] ?? 0;
            $userteam['score'] = $scoreMap[$user_id]['score'] ?? 0;

            return $userteam;
        })->sort(function ($a, $b) {

            // 1️⃣ Active users first
            $aStatus = strtolower($a['status'] ?? 'inactive');
            $bStatus = strtolower($b['status'] ?? 'inactive');

            if ($aStatus !== $bStatus) {
                return ($aStatus === 'active') ? -1 : 1;
            }

            // 2️⃣ Higher score first
            if (($a['score'] ?? 0) !== ($b['score'] ?? 0)) {
                return ($b['score'] ?? 0) <=> ($a['score'] ?? 0);
            }

            // 3️⃣ User ID: ASC
            if (($a['id'] ?? 0) !== ($b['id'] ?? 0)) {
                return ($a['id'] ?? 0) <=> ($b['id'] ?? 0);
            }

            // 3️⃣ Latest created
            // return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        })
            ->values();

        $rankedUsers = $rankedUsers->map(function ($user, $index) {
            $user['rank'] = $index + 1;

            return $user;
        });
        $topUsers = $rankedUsers->take(4);
        $currentUser = $rankedUsers->firstWhere('id', $loggedInUserId);
        if ($currentUser && ! $topUsers->contains('id', $loggedInUserId)) {
            $topUsers->push($currentUser);
        }

        $this->topUsers_rank = $topUsers;
        $this->ownUser = $currentUser;

        // Total pool for the internal Score Board (used for "out of N" + percentile).
        $this->recruiterMeta = [
            'count' => $rankedUsers->count(),
            'active' => $rankedUsers->filter(fn ($u) => strtolower($u['status'] ?? 'inactive') === 'active')->count(),
        ];

        /*
         * Actual monthly joinings & offers per recruiter for the JOIN / O→J% columns of
         * "Monthly Performers". These must reflect REAL recruitment outcomes, not gamification
         * points — the points pipeline only credits an 'Onboarded' joining (and deducts for
         * non-onboarded ones), so it under-reports real joinings (e.g. shows 1 where 2 joined).
         * Source of truth = offer_job_applications.submitted_by (the recruiter who owns the
         * candidate). Window = the report month (previous completed month, matching the rest of
         * this section). Tenancy: raw DB::table is NOT auto-scoped, so company_id is filtered
         * explicitly (Rule #3). Join = reached "Joined" (master_status_id 6) with joined_date in
         * the month; Offer = reached offer/joined stage (master_status_id 5 or 6) in the month.
         */
        $repMonthStart = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d H:i:s');
        $repMonthEnd = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d H:i:s');

        $this->recruiterActualJoins = DB::table('offer_job_applications')
            ->select('submitted_by', DB::raw('COUNT(*) as cnt'))
            ->where('company_id', $this->company_id)
            ->whereNull('deleted_at')
            ->where('master_status_id', 6)
            ->whereBetween('joined_date', [$repMonthStart, $repMonthEnd])
            ->groupBy('submitted_by')
            ->pluck('cnt', 'submitted_by')
            ->toArray();

        $this->recruiterActualOffers = DB::table('offer_job_applications')
            ->select('submitted_by', DB::raw('COUNT(*) as cnt'))
            ->where('company_id', $this->company_id)
            ->whereNull('deleted_at')
            ->whereIn('master_status_id', [5, 6])
            ->whereBetween('created_at', [$repMonthStart, $repMonthEnd])
            ->groupBy('submitted_by')
            ->pluck('cnt', 'submitted_by')
            ->toArray();

        // Top performing captains — reuse the already-built $rankedUsers (no extra query).
        // Captain-only score (not recruiter score) = sum of their team-captain contributions.
        // Only users who are acting captains (captain activity >= 1) are included.
        $this->topUsersCaptain = $rankedUsers
            ->map(function ($u) {
                $u = (array) $u;
                $u['captainScore'] = (int) ($u['teamCaptainInterviewScheduled'] ?? 0)
                    + (int) ($u['teamCaptainOfferReleased'] ?? 0)
                    + (int) ($u['teamCaptainCandidateJoined'] ?? 0);

                return $u;
            })
            ->filter(fn ($u) => ($u['captainScore'] ?? 0) > 0)
            ->sortByDesc('captainScore')
            ->values()
            ->take(4);

        // End Your Internal Rank: Get all users with sourcing count within date range

        // Start Your Current Rank: Get all users with count within date range

        // Step 1: Fetch all users
        $userSearchPayload_rank_current = [
            'index' => ELASTIC_USERS_LIST,
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['term' => ['company_id' => $company_id]],
                            // ['term' => ['status' => 'active']],
                        ],
                    ],
                ],
                '_source' => ['id', 'name', 'email', 'designation', 'status'],
                'size' => 10000,
                'sort' => [
                    ['id' => ['order' => 'asc']],
                    ['status' => ['order' => 'asc']],
                ],
            ],
        ];

        $userResponse_rank_current = $this->elasticService->getClient()->search($userSearchPayload_rank_current);
        $usersteam_current = collect($userResponse_rank_current['hits']['hits'])->pluck('_source');

        // dd($rankstartOfMonthForReport,$rankendOfMonthForReport,$startOfMonthForReport,$endOfMonthForReport);

        $rankQuery_current = [
            'index' => ELASTIC_TEAM_RANK,
            'body' => [
                'from' => 0,
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['term' => ['doc.company_id' => $company_id]],
                            ['range' => [
                                'doc.created_at' => [
                                    'gte' => $startOfMonthForReport,
                                    'lte' => $endOfMonthForReport,
                                ],
                            ]],
                        ],
                    ],
                ],
                'aggs' => [
                    'user_scores' => [
                        'terms' => [
                            'field' => 'doc.user_id',
                            'size' => 10000,
                        ],
                        'aggs' => [
                            'profileUpload_sum' => ['sum' => ['field' => 'doc.profileUpload']],
                            'interviewScheduled_sum' => ['sum' => ['field' => 'doc.interviewScheduled']],
                            'interviewNoShow_sum' => ['sum' => ['field' => 'doc.interviewNoShow']],
                            'offerReleased_sum' => ['sum' => ['field' => 'doc.offerReleased']],
                            'candidateJoined_sum' => ['sum' => ['field' => 'doc.candidateJoined']],
                            'interestedCandidates_sum' => ['sum' => ['field' => 'doc.interestedCandidates']],
                            'notInterestedCandidates_sum' => ['sum' => ['field' => 'doc.notInterestedCandidates']],
                            'teamCaptainInterviewScheduled_sum' => ['sum' => ['field' => 'doc.teamCaptainInterviewScheduled']],
                            'teamCaptainInterviewNoShow_sum' => ['sum' => ['field' => 'doc.teamCaptainInterviewNoShow']],
                            'teamCaptainOfferReleased_sum' => ['sum' => ['field' => 'doc.teamCaptainOfferReleased']],
                            'teamCaptainCandidateJoined_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoined']],
                            'sourcing_sum' => ['sum' => ['field' => 'doc.sourcing']],
                            'screening_sum' => ['sum' => ['field' => 'doc.screening']],
                            'evaluation_sum' => ['sum' => ['field' => 'doc.evaluation']],
                            'negotiation_sum' => ['sum' => ['field' => 'doc.negotiation']],
                            'backout_sum' => ['sum' => ['field' => 'doc.backout']],
                            'backoutTeam_sum' => ['sum' => ['field' => 'doc.backoutTeam']],
                            'candidateJoinedContract_sum' => ['sum' => ['field' => 'doc.candidateJoinedContract']],
                            'teamCaptainCandidateJoinedContract_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoinedContract']],
                            'backoutContract_sum' => ['sum' => ['field' => 'doc.backoutContract']],
                            'backoutTeamContract_sum' => ['sum' => ['field' => 'doc.backoutTeamContract']],
                        ],
                    ],
                ],
            ],
        ];

        $response_current = $this->elasticService->getClient()->search($rankQuery_current);
        $buckets_current = $response_current['aggregations']['user_scores']['buckets'];

        $scoreMap_current = collect($buckets_current)->mapWithKeys(function ($item) {
            $score =
                ($item['profileUpload_sum']['value'] ?? 0) +
                ($item['interviewScheduled_sum']['value'] ?? 0) +
                ($item['interviewNoShow_sum']['value'] ?? 0) +
                ($item['offerReleased_sum']['value'] ?? 0) +
                ($item['candidateJoined_sum']['value'] ?? 0) +
                ($item['teamCaptainInterviewScheduled_sum']['value'] ?? 0) +
                ($item['teamCaptainOfferReleased_sum']['value'] ?? 0) +
                ($item['teamCaptainCandidateJoined_sum']['value'] ?? 0) +
                ($item['sourcing_sum']['value'] ?? 0) +
                ($item['screening_sum']['value'] ?? 0) +
                ($item['evaluation_sum']['value'] ?? 0) +
                ($item['negotiation_sum']['value'] ?? 0) +
                ($item['backout_sum']['value'] ?? 0) +
                ($item['backoutTeam_sum']['value'] ?? 0) + ($item['candidateJoinedContract_sum']['value'] ?? 0) + ($item['teamCaptainCandidateJoinedContract_sum']['value'] ?? 0) + ($item['backoutContract_sum']['value'] ?? 0) + ($item['backoutTeamContract_sum']['value'] ?? 0);

            return [$item['key'] => [
                'user_id' => $item['key'],
                'profileUpload' => ($item['profileUpload_sum']['value'] ?? 0),
                'interviewScheduled' => ($item['interviewScheduled_sum']['value'] ?? 0),
                'interviewNoShow' => ($item['interviewNoShow_sum']['value'] ?? 0),
                'offerReleased' => ($item['offerReleased_sum']['value'] ?? 0),
                'candidateJoined' => ($item['candidateJoined_sum']['value'] ?? 0),
                'teamCaptainInterviewScheduled' => ($item['teamCaptainInterviewScheduled_sum']['value'] ?? 0),
                'teamCaptainOfferReleased' => ($item['teamCaptainOfferReleased_sum']['value'] ?? 0),
                'teamCaptainCandidateJoined' => ($item['teamCaptainCandidateJoined_sum']['value'] ?? 0),
                'sourcing' => ($item['sourcing_sum']['value'] ?? 0),
                'screening' => ($item['screening_sum']['value'] ?? 0),
                'evaluation' => ($item['evaluation_sum']['value'] ?? 0),
                'negotiation' => ($item['negotiation_sum']['value'] ?? 0),
                'backout' => ($item['backout_sum']['value'] ?? 0),
                'backoutTeam' => ($item['backoutTeam_sum']['value'] ?? 0),
                'candidateJoinedContract' => ($item['candidateJoinedContract_sum']['value'] ?? 0),
                'teamCaptainCandidateJoinedContract' => ($item['teamCaptainCandidateJoinedContract_sum']['value'] ?? 0),
                'backoutContract' => ($item['backoutContract_sum']['value'] ?? 0),
                'backoutTeamContract' => ($item['backoutTeamContract_sum']['value'] ?? 0),
                'score' => $score,
            ]];
        });

        $rankedUsers_current = $usersteam_current->map(function ($userteam_current) use ($scoreMap_current) {
            // Intent: Null-coalesce user id to prevent undefined array key exceptions when Elasticsearch hits omit 'id'
            $user_id = $userteam_current['id'] ?? null;
            $userteam_current['profileUpload'] = $scoreMap_current[$user_id]['profileUpload'] ?? 0;
            $userteam_current['interviewScheduled'] = $scoreMap_current[$user_id]['interviewScheduled'] ?? 0;
            $userteam_current['interviewNoShow'] = $scoreMap_current[$user_id]['interviewNoShow'] ?? 0;
            $userteam_current['offerReleased'] = $scoreMap_current[$user_id]['offerReleased'] ?? 0;
            $userteam_current['candidateJoined'] = $scoreMap_current[$user_id]['candidateJoined'] ?? 0;
            $userteam_current['interestedCandidates'] = $scoreMap_current[$user_id]['interestedCandidates'] ?? 0;
            $userteam_current['notInterestedCandidates'] = $scoreMap_current[$user_id]['notInterestedCandidates'] ?? 0;
            $userteam_current['teamCaptainInterviewScheduled'] = $scoreMap_current[$user_id]['teamCaptainInterviewScheduled'] ?? 0;
            $userteam_current['teamCaptainInterviewNoShow'] = $scoreMap_current[$user_id]['teamCaptainInterviewNoShow'] ?? 0;
            $userteam_current['teamCaptainOfferReleased'] = $scoreMap_current[$user_id]['teamCaptainOfferReleased'] ?? 0;
            $userteam_current['teamCaptainCandidateJoined'] = $scoreMap_current[$user_id]['teamCaptainCandidateJoined'] ?? 0;
            $userteam_current['sourcing'] = $scoreMap_current[$user_id]['sourcing'] ?? 0;
            $userteam_current['screening'] = $scoreMap_current[$user_id]['screening'] ?? 0;
            $userteam_current['evaluation'] = $scoreMap_current[$user_id]['evaluation'] ?? 0;
            $userteam_current['negotiation'] = $scoreMap_current[$user_id]['negotiation'] ?? 0;
            $userteam_current['backout'] = $scoreMap_current[$user_id]['backout'] ?? 0;
            $userteam_current['backoutTeam'] = $scoreMap_current[$user_id]['backoutTeam'] ?? 0;
            $userteam_current['score'] = $scoreMap_current[$user_id]['score'] ?? 0;

            return $userteam_current;
        })->sort(function ($a, $b) {

            // 1️⃣ Active users first
            $aStatus = strtolower($a['status'] ?? 'inactive');
            $bStatus = strtolower($b['status'] ?? 'inactive');

            if ($aStatus !== $bStatus) {
                return ($aStatus === 'active') ? -1 : 1;
            }

            // 2️⃣ Higher score first
            if (($a['score'] ?? 0) !== ($b['score'] ?? 0)) {
                return ($b['score'] ?? 0) <=> ($a['score'] ?? 0);
            }

            // 3️⃣ User ID: ASC
            if (($a['id'] ?? 0) !== ($b['id'] ?? 0)) {
                return ($a['id'] ?? 0) <=> ($b['id'] ?? 0);
            }

            // 3️⃣ Latest created
            // return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        })
            ->values();

        $rankedUsers_current = $rankedUsers_current->map(function ($user, $index) {
            $user['rank'] = $index + 1;

            return $user;
        });
        $topUsers_current = $rankedUsers_current->take(4);
        $currentUser_current = $rankedUsers_current->firstWhere('id', $loggedInUserId);
        if ($currentUser_current && ! $topUsers_current->contains('id', $loggedInUserId)) {
            $topUsers_current->push($currentUser_current);
        }

        $this->topUsers_rank_current = $topUsers_current;
        $this->ownUser_current = $currentUser_current;

        // End Your Current Rank: Get all users with sourcing count within date range

        // Start Your Daily Rank: Get all users with sourcing count within date range

        // dd($startOfPreviousDay,$endOfDayForReport);

        // Step 1: Fetch all users
        $userSearchPayload_rank_daily_internal_rank = [
            'index' => ELASTIC_USERS_LIST,
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['term' => ['company_id' => $company_id]],
                            // ['term' => ['status' => 'active']],
                        ],
                    ],
                ],
                '_source' => ['id', 'name', 'email', 'designation', 'status'],
                'size' => 10000,
                'sort' => [
                    ['id' => ['order' => 'asc']],
                    ['status' => ['order' => 'asc']],
                ],
            ],
        ];

        $userResponse_rank_daily_internal_rank = $this->elasticService->getClient()->search($userSearchPayload_rank_daily_internal_rank);
        $usersteam_daily_internal_rank = collect($userResponse_rank_daily_internal_rank['hits']['hits'])->pluck('_source');

        $rankQuery_daily_internal_rank = [
            'index' => ELASTIC_TEAM_RANK,
            'body' => [
                'from' => 0,
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['term' => ['doc.company_id' => $company_id]],
                            ['range' => [
                                'doc.created_at' => [
                                    'gte' => $startOfPreviousDay,
                                    'lte' => $endOfDayForReport,
                                ],
                            ]],
                        ],
                    ],
                ],
                'aggs' => [
                    'user_scores' => [
                        'terms' => [
                            'field' => 'doc.user_id',
                            'size' => 10000,
                        ],
                        'aggs' => [
                            'profileUpload_sum' => ['sum' => ['field' => 'doc.profileUpload']],
                            'interviewScheduled_sum' => ['sum' => ['field' => 'doc.interviewScheduled']],
                            'interviewNoShow_sum' => ['sum' => ['field' => 'doc.interviewNoShow']],
                            'offerReleased_sum' => ['sum' => ['field' => 'doc.offerReleased']],
                            'candidateJoined_sum' => ['sum' => ['field' => 'doc.candidateJoined']],
                            'interestedCandidates_sum' => ['sum' => ['field' => 'doc.interestedCandidates']],
                            'notInterestedCandidates_sum' => ['sum' => ['field' => 'doc.notInterestedCandidates']],
                            'teamCaptainInterviewScheduled_sum' => ['sum' => ['field' => 'doc.teamCaptainInterviewScheduled']],
                            'teamCaptainInterviewNoShow_sum' => ['sum' => ['field' => 'doc.teamCaptainInterviewNoShow']],
                            'teamCaptainOfferReleased_sum' => ['sum' => ['field' => 'doc.teamCaptainOfferReleased']],
                            'teamCaptainCandidateJoined_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoined']],
                            'sourcing_sum' => ['sum' => ['field' => 'doc.sourcing']],
                            'screening_sum' => ['sum' => ['field' => 'doc.screening']],
                            'evaluation_sum' => ['sum' => ['field' => 'doc.evaluation']],
                            'negotiation_sum' => ['sum' => ['field' => 'doc.negotiation']],
                            'backout_sum' => ['sum' => ['field' => 'doc.backout']],
                            'backoutTeam_sum' => ['sum' => ['field' => 'doc.backoutTeam']],
                            'candidateJoinedContract_sum' => ['sum' => ['field' => 'doc.candidateJoinedContract']],
                            'teamCaptainCandidateJoinedContract_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoinedContract']],
                            'backoutContract_sum' => ['sum' => ['field' => 'doc.backoutContract']],
                            'backoutTeamContract_sum' => ['sum' => ['field' => 'doc.backoutTeamContract']],
                        ],
                    ],
                ],
            ],
        ];

        $response_daily_internal_rank = $this->elasticService->getClient()->search($rankQuery_daily_internal_rank);
        $buckets_daily_internal_rank = $response_daily_internal_rank['aggregations']['user_scores']['buckets'];

        // if(auth()->user()->id==1232){
        // dd($buckets_daily_internal_rank);
        // }

        $scoreMap_daily_internal_rank = collect($buckets_daily_internal_rank)->mapWithKeys(function ($item) {
            $score =
                ($item['profileUpload_sum']['value'] ?? 0) +
                ($item['interviewScheduled_sum']['value'] ?? 0) +
                ($item['interviewNoShow_sum']['value'] ?? 0) +
                ($item['offerReleased_sum']['value'] ?? 0) +
                ($item['candidateJoined_sum']['value'] ?? 0) +
                ($item['teamCaptainInterviewScheduled_sum']['value'] ?? 0) +
                ($item['teamCaptainOfferReleased_sum']['value'] ?? 0) +
                ($item['teamCaptainCandidateJoined_sum']['value'] ?? 0) +
                ($item['sourcing_sum']['value'] ?? 0) +
                ($item['screening_sum']['value'] ?? 0) +
                ($item['evaluation_sum']['value'] ?? 0) +
                ($item['negotiation_sum']['value'] ?? 0) +
                ($item['backout_sum']['value'] ?? 0) +
                ($item['backoutTeam_sum']['value'] ?? 0) + ($item['candidateJoinedContract_sum']['value'] ?? 0) + ($item['teamCaptainCandidateJoinedContract_sum']['value'] ?? 0) + ($item['backoutContract_sum']['value'] ?? 0) + ($item['backoutTeamContract_sum']['value'] ?? 0);

            return [$item['key'] => [
                'user_id' => $item['key'],
                'profileUpload' => ($item['profileUpload_sum']['value'] ?? 0),
                'interviewScheduled' => ($item['interviewScheduled_sum']['value'] ?? 0),
                'interviewNoShow' => ($item['interviewNoShow_sum']['value'] ?? 0),
                'offerReleased' => ($item['offerReleased_sum']['value'] ?? 0),
                'candidateJoined' => ($item['candidateJoined_sum']['value'] ?? 0),
                'teamCaptainInterviewScheduled' => ($item['teamCaptainInterviewScheduled_sum']['value'] ?? 0),
                'teamCaptainOfferReleased' => ($item['teamCaptainOfferReleased_sum']['value'] ?? 0),
                'teamCaptainCandidateJoined' => ($item['teamCaptainCandidateJoined_sum']['value'] ?? 0),
                'sourcing' => ($item['sourcing_sum']['value'] ?? 0),
                'screening' => ($item['screening_sum']['value'] ?? 0),
                'evaluation' => ($item['evaluation_sum']['value'] ?? 0),
                'negotiation' => ($item['negotiation_sum']['value'] ?? 0),
                'backout' => ($item['backout_sum']['value'] ?? 0),
                'backoutTeam' => ($item['backoutTeam_sum']['value'] ?? 0),
                'candidateJoinedContract' => ($item['candidateJoinedContract_sum']['value'] ?? 0),
                'teamCaptainCandidateJoinedContract' => ($item['teamCaptainCandidateJoinedContract_sum']['value'] ?? 0),
                'backoutContract' => ($item['backoutContract_sum']['value'] ?? 0),
                'backoutTeamContract' => ($item['backoutTeamContract_sum']['value'] ?? 0),
                'score' => $score,
            ]];
        });

        $rankedUsers_daily_internal_rank = $usersteam_daily_internal_rank->map(function ($userteam_daily_internal_rank) use ($scoreMap_daily_internal_rank) {
            // Intent: Null-coalesce user id to prevent undefined array key exceptions when Elasticsearch hits omit 'id'
            $user_id = $userteam_daily_internal_rank['id'] ?? null;
            $userteam_daily_internal_rank['profileUpload'] = $scoreMap_daily_internal_rank[$user_id]['profileUpload'] ?? 0;
            $userteam_daily_internal_rank['interviewScheduled'] = $scoreMap_daily_internal_rank[$user_id]['interviewScheduled'] ?? 0;
            $userteam_daily_internal_rank['interviewNoShow'] = $scoreMap_daily_internal_rank[$user_id]['interviewNoShow'] ?? 0;
            $userteam_daily_internal_rank['offerReleased'] = $scoreMap_daily_internal_rank[$user_id]['offerReleased'] ?? 0;
            $userteam_daily_internal_rank['candidateJoined'] = $scoreMap_daily_internal_rank[$user_id]['candidateJoined'] ?? 0;
            $userteam_daily_internal_rank['interestedCandidates'] = $scoreMap_daily_internal_rank[$user_id]['interestedCandidates'] ?? 0;
            $userteam_daily_internal_rank['notInterestedCandidates'] = $scoreMap_daily_internal_rank[$user_id]['notInterestedCandidates'] ?? 0;
            $userteam_daily_internal_rank['teamCaptainInterviewScheduled'] = $scoreMap_daily_internal_rank[$user_id]['teamCaptainInterviewScheduled'] ?? 0;
            $userteam_daily_internal_rank['teamCaptainInterviewNoShow'] = $scoreMap_daily_internal_rank[$user_id]['teamCaptainInterviewNoShow'] ?? 0;
            $userteam_daily_internal_rank['teamCaptainOfferReleased'] = $scoreMap_daily_internal_rank[$user_id]['teamCaptainOfferReleased'] ?? 0;
            $userteam_daily_internal_rank['teamCaptainCandidateJoined'] = $scoreMap_daily_internal_rank[$user_id]['teamCaptainCandidateJoined'] ?? 0;
            $userteam_daily_internal_rank['sourcing'] = $scoreMap_daily_internal_rank[$user_id]['sourcing'] ?? 0;
            $userteam_daily_internal_rank['screening'] = $scoreMap_daily_internal_rank[$user_id]['screening'] ?? 0;
            $userteam_daily_internal_rank['evaluation'] = $scoreMap_daily_internal_rank[$user_id]['evaluation'] ?? 0;
            $userteam_daily_internal_rank['negotiation'] = $scoreMap_daily_internal_rank[$user_id]['negotiation'] ?? 0;
            $userteam_daily_internal_rank['backout'] = $scoreMap_daily_internal_rank[$user_id]['backout'] ?? 0;
            $userteam_daily_internal_rank['backoutTeam'] = $scoreMap_daily_internal_rank[$user_id]['backoutTeam'] ?? 0;
            $userteam_daily_internal_rank['score'] = $scoreMap_daily_internal_rank[$user_id]['score'] ?? 0;

            return $userteam_daily_internal_rank;
        })->sort(function ($a, $b) {

            // 1️⃣ Active users first
            $aStatus = strtolower($a['status'] ?? 'inactive');
            $bStatus = strtolower($b['status'] ?? 'inactive');

            if ($aStatus !== $bStatus) {
                return ($aStatus === 'active') ? -1 : 1;
            }

            // 2️⃣ Higher score first
            if (($a['score'] ?? 0) !== ($b['score'] ?? 0)) {
                return ($b['score'] ?? 0) <=> ($a['score'] ?? 0);
            }

            // 3️⃣ User ID: ASC
            if (($a['id'] ?? 0) !== ($b['id'] ?? 0)) {
                return ($a['id'] ?? 0) <=> ($b['id'] ?? 0);
            }

            // 3️⃣ Latest created
            // return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        })
            ->values();

        $rankedUsers_daily_internal_rank = $rankedUsers_daily_internal_rank->map(function ($user, $index) {
            $user['rank'] = $index + 1;

            return $user;
        });
        $topUsers_daily_internal_rank = $rankedUsers_daily_internal_rank->take(4);
        $currentUser_daily_internal_rank = $rankedUsers_daily_internal_rank->firstWhere('id', $loggedInUserId);
        if ($currentUser_daily_internal_rank && ! $topUsers_daily_internal_rank->contains('id', $loggedInUserId)) {
            $topUsers_daily_internal_rank->push($currentUser_daily_internal_rank);
        }

        $this->topUsers_rank_daily_internal_rank = $topUsers_daily_internal_rank;
        $this->ownUser_daily_internal_rank = $currentUser_daily_internal_rank;

        // Top captains (daily) — reuse the already-built daily ranked list (no extra query)
        $this->topUsersCaptainDaily = $rankedUsers_daily_internal_rank
            ->map(function ($u) {
                $u = (array) $u;
                $u['captainScore'] = (int) ($u['teamCaptainInterviewScheduled'] ?? 0)
                    + (int) ($u['teamCaptainOfferReleased'] ?? 0)
                    + (int) ($u['teamCaptainCandidateJoined'] ?? 0);

                return $u;
            })
            ->filter(fn ($u) => ($u['captainScore'] ?? 0) > 0)
            ->sortByDesc('teamCaptainCandidateJoined')
            ->values()
            ->take(4);

        $getTopFive = request()->query('getTopFive');

        if ((int) $getTopFive == 808070605012344321 && auth()->user()->id == 3) {
            dd($rankedUsers_daily_internal_rank, $rankedUsers_current, $rankedUsers);
        }
        if ((int) $getTopFive == 808070605012344321 && auth()->user()->id == 18) {
            dd($rankedUsers_daily_internal_rank, $rankedUsers_current, $rankedUsers);
        }

        // Global Rank

        $userSearchPayload_rank_daily_global_rank = [
            'index' => ELASTIC_USERS_LIST,
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [],
                    ],
                ],
                '_source' => ['id', 'name', 'email', 'designation', 'status'],
                'size' => 10000,
                'sort' => [
                    ['id' => ['order' => 'asc']],
                    ['status' => ['order' => 'asc']],
                ],
            ],
        ];

        $userResponse_rank_daily_global_rank = $this->elasticService->getClient()->search($userSearchPayload_rank_daily_global_rank);
        $usersteam_daily_global_rank = collect($userResponse_rank_daily_global_rank['hits']['hits'])->pluck('_source');

        $rankQuery_daily_global_rank = [
            'index' => ELASTIC_TEAM_RANK,
            'body' => [
                'from' => 0,
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['range' => [
                                'doc.created_at' => [
                                    'gte' => $startOfPreviousDay,
                                    'lte' => $endOfDayForReport,
                                ],
                            ]],
                        ],
                    ],
                ],
                'aggs' => [
                    'user_scores' => [
                        'terms' => [
                            'field' => 'doc.user_id',
                            'size' => 10000,
                        ],
                        'aggs' => [
                            'profileUpload_sum' => ['sum' => ['field' => 'doc.profileUpload']],
                            'interviewScheduled_sum' => ['sum' => ['field' => 'doc.interviewScheduled']],
                            'interviewNoShow_sum' => ['sum' => ['field' => 'doc.interviewNoShow']],
                            'offerReleased_sum' => ['sum' => ['field' => 'doc.offerReleased']],
                            'candidateJoined_sum' => ['sum' => ['field' => 'doc.candidateJoined']],
                            'interestedCandidates_sum' => ['sum' => ['field' => 'doc.interestedCandidates']],
                            'notInterestedCandidates_sum' => ['sum' => ['field' => 'doc.notInterestedCandidates']],
                            'teamCaptainInterviewScheduled_sum' => ['sum' => ['field' => 'doc.teamCaptainInterviewScheduled']],
                            'teamCaptainInterviewNoShow_sum' => ['sum' => ['field' => 'doc.teamCaptainInterviewNoShow']],
                            'teamCaptainOfferReleased_sum' => ['sum' => ['field' => 'doc.teamCaptainOfferReleased']],
                            'teamCaptainCandidateJoined_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoined']],
                            'sourcing_sum' => ['sum' => ['field' => 'doc.sourcing']],
                            'screening_sum' => ['sum' => ['field' => 'doc.screening']],
                            'evaluation_sum' => ['sum' => ['field' => 'doc.evaluation']],
                            'negotiation_sum' => ['sum' => ['field' => 'doc.negotiation']],
                            'backout_sum' => ['sum' => ['field' => 'doc.backout']],
                            'backoutTeam_sum' => ['sum' => ['field' => 'doc.backoutTeam']],
                            'candidateJoinedContract_sum' => ['sum' => ['field' => 'doc.candidateJoinedContract']],
                            'teamCaptainCandidateJoinedContract_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoinedContract']],
                            'backoutContract_sum' => ['sum' => ['field' => 'doc.backoutContract']],
                            'backoutTeamContract_sum' => ['sum' => ['field' => 'doc.backoutTeamContract']],
                        ],
                    ],
                ],
            ],
        ];

        $response_daily_global_rank = $this->elasticService->getClient()->search($rankQuery_daily_global_rank);
        $buckets_daily_global_rank = $response_daily_global_rank['aggregations']['user_scores']['buckets'];

        $scoreMap_daily_global_rank = collect($buckets_daily_global_rank)->mapWithKeys(function ($item) {
            $score =
                ($item['profileUpload_sum']['value'] ?? 0) +
                ($item['interviewScheduled_sum']['value'] ?? 0) +
                ($item['interviewNoShow_sum']['value'] ?? 0) +
                ($item['offerReleased_sum']['value'] ?? 0) +
                ($item['candidateJoined_sum']['value'] ?? 0) +
                ($item['teamCaptainInterviewScheduled_sum']['value'] ?? 0) +
                ($item['teamCaptainOfferReleased_sum']['value'] ?? 0) +
                ($item['teamCaptainCandidateJoined_sum']['value'] ?? 0) +
                ($item['sourcing_sum']['value'] ?? 0) +
                ($item['screening_sum']['value'] ?? 0) +
                ($item['evaluation_sum']['value'] ?? 0) +
                ($item['negotiation_sum']['value'] ?? 0) +
                ($item['backout_sum']['value'] ?? 0) +
                ($item['backoutTeam_sum']['value'] ?? 0) + ($item['candidateJoinedContract_sum']['value'] ?? 0) + ($item['teamCaptainCandidateJoinedContract_sum']['value'] ?? 0) + ($item['backoutContract_sum']['value'] ?? 0) + ($item['backoutTeamContract_sum']['value'] ?? 0);

            return [$item['key'] => [
                'user_id' => $item['key'],
                'profileUpload' => ($item['profileUpload_sum']['value'] ?? 0),
                'interviewScheduled' => ($item['interviewScheduled_sum']['value'] ?? 0),
                'interviewNoShow' => ($item['interviewNoShow_sum']['value'] ?? 0),
                'offerReleased' => ($item['offerReleased_sum']['value'] ?? 0),
                'candidateJoined' => ($item['candidateJoined_sum']['value'] ?? 0),
                'teamCaptainInterviewScheduled' => ($item['teamCaptainInterviewScheduled_sum']['value'] ?? 0),
                'teamCaptainOfferReleased' => ($item['teamCaptainOfferReleased_sum']['value'] ?? 0),
                'teamCaptainCandidateJoined' => ($item['teamCaptainCandidateJoined_sum']['value'] ?? 0),
                'sourcing' => ($item['sourcing_sum']['value'] ?? 0),
                'screening' => ($item['screening_sum']['value'] ?? 0),
                'evaluation' => ($item['evaluation_sum']['value'] ?? 0),
                'negotiation' => ($item['negotiation_sum']['value'] ?? 0),
                'backout' => ($item['backout_sum']['value'] ?? 0),
                'backoutTeam' => ($item['backoutTeam_sum']['value'] ?? 0),
                'candidateJoinedContract' => ($item['candidateJoinedContract_sum']['value'] ?? 0),
                'teamCaptainCandidateJoinedContract' => ($item['teamCaptainCandidateJoinedContract_sum']['value'] ?? 0),
                'backoutContract' => ($item['backoutContract_sum']['value'] ?? 0),
                'backoutTeamContract' => ($item['backoutTeamContract_sum']['value'] ?? 0),
                'score' => $score,
            ]];
        });

        $rankedUsers_daily_global_rank = $usersteam_daily_global_rank->map(function ($userteam_daily_global_rank) use ($scoreMap_daily_global_rank) {
            // Intent: Null-coalesce user id to prevent undefined array key exceptions when Elasticsearch hits omit 'id'
            $user_id = $userteam_daily_global_rank['id'] ?? null;
            $userteam_daily_global_rank['profileUpload'] = $scoreMap_daily_global_rank[$user_id]['profileUpload'] ?? 0;
            $userteam_daily_global_rank['interviewScheduled'] = $scoreMap_daily_global_rank[$user_id]['interviewScheduled'] ?? 0;
            $userteam_daily_global_rank['interviewNoShow'] = $scoreMap_daily_global_rank[$user_id]['interviewNoShow'] ?? 0;
            $userteam_daily_global_rank['offerReleased'] = $scoreMap_daily_global_rank[$user_id]['offerReleased'] ?? 0;
            $userteam_daily_global_rank['candidateJoined'] = $scoreMap_daily_global_rank[$user_id]['candidateJoined'] ?? 0;
            $userteam_daily_global_rank['interestedCandidates'] = $scoreMap_daily_global_rank[$user_id]['interestedCandidates'] ?? 0;
            $userteam_daily_global_rank['notInterestedCandidates'] = $scoreMap_daily_global_rank[$user_id]['notInterestedCandidates'] ?? 0;
            $userteam_daily_global_rank['teamCaptainInterviewScheduled'] = $scoreMap_daily_global_rank[$user_id]['teamCaptainInterviewScheduled'] ?? 0;
            $userteam_daily_global_rank['teamCaptainInterviewNoShow'] = $scoreMap_daily_global_rank[$user_id]['teamCaptainInterviewNoShow'] ?? 0;
            $userteam_daily_global_rank['teamCaptainOfferReleased'] = $scoreMap_daily_global_rank[$user_id]['teamCaptainOfferReleased'] ?? 0;
            $userteam_daily_global_rank['teamCaptainCandidateJoined'] = $scoreMap_daily_global_rank[$user_id]['teamCaptainCandidateJoined'] ?? 0;
            $userteam_daily_global_rank['sourcing'] = $scoreMap_daily_global_rank[$user_id]['sourcing'] ?? 0;
            $userteam_daily_global_rank['screening'] = $scoreMap_daily_global_rank[$user_id]['screening'] ?? 0;
            $userteam_daily_global_rank['evaluation'] = $scoreMap_daily_global_rank[$user_id]['evaluation'] ?? 0;
            $userteam_daily_global_rank['negotiation'] = $scoreMap_daily_global_rank[$user_id]['negotiation'] ?? 0;
            $userteam_daily_global_rank['backout'] = $scoreMap_daily_global_rank[$user_id]['backout'] ?? 0;
            $userteam_daily_global_rank['backoutTeam'] = $scoreMap_daily_global_rank[$user_id]['backoutTeam'] ?? 0;
            $userteam_daily_global_rank['score'] = $scoreMap_daily_global_rank[$user_id]['score'] ?? 0;

            return $userteam_daily_global_rank;
        })->sort(function ($a, $b) {

            // 1️⃣ Active users first
            $aStatus = strtolower($a['status'] ?? 'inactive');
            $bStatus = strtolower($b['status'] ?? 'inactive');

            if ($aStatus !== $bStatus) {
                return ($aStatus === 'active') ? -1 : 1;
            }

            // 2️⃣ Higher score first
            if (($a['score'] ?? 0) !== ($b['score'] ?? 0)) {
                return ($b['score'] ?? 0) <=> ($a['score'] ?? 0);
            }

            // 3️⃣ User ID: ASC
            if (($a['id'] ?? 0) !== ($b['id'] ?? 0)) {
                return ($a['id'] ?? 0) <=> ($b['id'] ?? 0);
            }

            // 3️⃣ Latest created
            // return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        })
            ->values();

        $rankedUsers_daily_global_rank = $rankedUsers_daily_global_rank->map(function ($user, $index) {
            $user['rank'] = $index + 1;

            return $user;
        });
        $topUsers_daily_global_rank = $rankedUsers_daily_global_rank->take(4);
        $currentUser_daily_global_rank = $rankedUsers_daily_global_rank->firstWhere('id', $loggedInUserId);
        if ($currentUser_daily_global_rank && ! $topUsers_daily_global_rank->contains('id', $loggedInUserId)) {
            $topUsers_daily_global_rank->push($currentUser_daily_global_rank);
        }

        $this->topUsers_rank_daily_global_rank = $topUsers_daily_global_rank;
        $this->ownUser_daily_global_rank = $currentUser_daily_global_rank;

        // End Your Daily Rank: Get all users with sourcing count within date range

        // Start Your Global Rank: Get all users with count within date range

        // Step 1: Fetch all users
        $userSearchPayload_rank_global = [
            'index' => ELASTIC_USERS_LIST,
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [],
                    ],
                ],
                '_source' => ['id', 'name', 'email', 'designation', 'status'],
                'size' => 10000,
                'sort' => [
                    ['id' => ['order' => 'asc']],
                    ['status' => ['order' => 'asc']],
                ],
            ],
        ];

        $userResponse_rank_global = $this->elasticService->getClient()->search($userSearchPayload_rank_global);
        $usersteam_global = collect($userResponse_rank_global['hits']['hits'])->pluck('_source');

        $rankQuery_global = [
            'index' => ELASTIC_TEAM_RANK,
            'body' => [
                'from' => 0,
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['range' => [
                                'doc.created_at' => [
                                    'gte' => $rankstartOfMonthForReport,
                                    'lte' => $rankendOfMonthForReport,
                                ],
                            ]],
                        ],
                    ],
                ],
                'aggs' => [
                    'user_scores' => [
                        'terms' => [
                            'field' => 'doc.user_id',
                            'size' => 10000,
                        ],
                        'aggs' => [
                            'profileUpload_sum' => ['sum' => ['field' => 'doc.profileUpload']],
                            'interviewScheduled_sum' => ['sum' => ['field' => 'doc.interviewScheduled']],
                            'interviewNoShow_sum' => ['sum' => ['field' => 'doc.interviewNoShow']],
                            'offerReleased_sum' => ['sum' => ['field' => 'doc.offerReleased']],
                            'candidateJoined_sum' => ['sum' => ['field' => 'doc.candidateJoined']],
                            'interestedCandidates_sum' => ['sum' => ['field' => 'doc.interestedCandidates']],
                            'notInterestedCandidates_sum' => ['sum' => ['field' => 'doc.notInterestedCandidates']],
                            'teamCaptainInterviewScheduled_sum' => ['sum' => ['field' => 'doc.teamCaptainInterviewScheduled']],
                            'teamCaptainInterviewNoShow_sum' => ['sum' => ['field' => 'doc.teamCaptainInterviewNoShow']],
                            'teamCaptainOfferReleased_sum' => ['sum' => ['field' => 'doc.teamCaptainOfferReleased']],
                            'teamCaptainCandidateJoined_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoined']],
                            'sourcing_sum' => ['sum' => ['field' => 'doc.sourcing']],
                            'screening_sum' => ['sum' => ['field' => 'doc.screening']],
                            'evaluation_sum' => ['sum' => ['field' => 'doc.evaluation']],
                            'negotiation_sum' => ['sum' => ['field' => 'doc.negotiation']],
                            'backout_sum' => ['sum' => ['field' => 'doc.backout']],
                            'backoutTeam_sum' => ['sum' => ['field' => 'doc.backoutTeam']],
                            'candidateJoinedContract_sum' => ['sum' => ['field' => 'doc.candidateJoinedContract']],
                            'teamCaptainCandidateJoinedContract_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoinedContract']],
                            'backoutContract_sum' => ['sum' => ['field' => 'doc.backoutContract']],
                            'backoutTeamContract_sum' => ['sum' => ['field' => 'doc.backoutTeamContract']],
                        ],
                    ],
                ],
            ],
        ];

        $response_global = $this->elasticService->getClient()->search($rankQuery_global);
        $buckets_global = $response_global['aggregations']['user_scores']['buckets'];

        $scoreMap_global = collect($buckets_global)->mapWithKeys(function ($item) {
            $score =
                ($item['profileUpload_sum']['value'] ?? 0) +
                ($item['interviewScheduled_sum']['value'] ?? 0) +
                ($item['interviewNoShow_sum']['value'] ?? 0) +
                ($item['offerReleased_sum']['value'] ?? 0) +
                ($item['candidateJoined_sum']['value'] ?? 0) +
                ($item['teamCaptainInterviewScheduled_sum']['value'] ?? 0) +
                ($item['teamCaptainOfferReleased_sum']['value'] ?? 0) +
                ($item['teamCaptainCandidateJoined_sum']['value'] ?? 0) +
                ($item['sourcing_sum']['value'] ?? 0) +
                ($item['screening_sum']['value'] ?? 0) +
                ($item['evaluation_sum']['value'] ?? 0) +
                ($item['negotiation_sum']['value'] ?? 0) +
                ($item['backout_sum']['value'] ?? 0) +
                ($item['backoutTeam_sum']['value'] ?? 0) + ($item['candidateJoinedContract_sum']['value'] ?? 0) + ($item['teamCaptainCandidateJoinedContract_sum']['value'] ?? 0) + ($item['backoutContract_sum']['value'] ?? 0) + ($item['backoutTeamContract_sum']['value'] ?? 0);

            return [$item['key'] => [
                'user_id' => $item['key'],
                'profileUpload' => ($item['profileUpload_sum']['value'] ?? 0),
                'interviewScheduled' => ($item['interviewScheduled_sum']['value'] ?? 0),
                'interviewNoShow' => ($item['interviewNoShow_sum']['value'] ?? 0),
                'offerReleased' => ($item['offerReleased_sum']['value'] ?? 0),
                'candidateJoined' => ($item['candidateJoined_sum']['value'] ?? 0),
                'teamCaptainInterviewScheduled' => ($item['teamCaptainInterviewScheduled_sum']['value'] ?? 0),
                'teamCaptainOfferReleased' => ($item['teamCaptainOfferReleased_sum']['value'] ?? 0),
                'teamCaptainCandidateJoined' => ($item['teamCaptainCandidateJoined_sum']['value'] ?? 0),
                'sourcing' => ($item['sourcing_sum']['value'] ?? 0),
                'screening' => ($item['screening_sum']['value'] ?? 0),
                'evaluation' => ($item['evaluation_sum']['value'] ?? 0),
                'negotiation' => ($item['negotiation_sum']['value'] ?? 0),
                'backout' => ($item['backout_sum']['value'] ?? 0),
                'backoutTeam' => ($item['backoutTeam_sum']['value'] ?? 0),
                'candidateJoinedContract' => ($item['candidateJoinedContract_sum']['value'] ?? 0),
                'teamCaptainCandidateJoinedContract' => ($item['teamCaptainCandidateJoinedContract_sum']['value'] ?? 0),
                'backoutContract' => ($item['backoutContract_sum']['value'] ?? 0),
                'backoutTeamContract' => ($item['backoutTeamContract_sum']['value'] ?? 0),
                'score' => $score,
            ]];
        });

        $rankedUsers_global = $usersteam_global->map(function ($userteam_global) use ($scoreMap_global) {
            // Intent: Null-coalesce user id to prevent undefined array key exceptions when Elasticsearch hits omit 'id'
            $user_id = $userteam_global['id'] ?? null;
            $userteam_global['profileUpload'] = $scoreMap_global[$user_id]['profileUpload'] ?? 0;
            $userteam_global['interviewScheduled'] = $scoreMap_global[$user_id]['interviewScheduled'] ?? 0;
            $userteam_global['interviewNoShow'] = $scoreMap_global[$user_id]['interviewNoShow'] ?? 0;
            $userteam_global['offerReleased'] = $scoreMap_global[$user_id]['offerReleased'] ?? 0;
            $userteam_global['candidateJoined'] = $scoreMap_global[$user_id]['candidateJoined'] ?? 0;
            $userteam_global['interestedCandidates'] = $scoreMap_global[$user_id]['interestedCandidates'] ?? 0;
            $userteam_global['notInterestedCandidates'] = $scoreMap_global[$user_id]['notInterestedCandidates'] ?? 0;
            $userteam_global['teamCaptainInterviewScheduled'] = $scoreMap_global[$user_id]['teamCaptainInterviewScheduled'] ?? 0;
            $userteam_global['teamCaptainInterviewNoShow'] = $scoreMap_global[$user_id]['teamCaptainInterviewNoShow'] ?? 0;
            $userteam_global['teamCaptainOfferReleased'] = $scoreMap_global[$user_id]['teamCaptainOfferReleased'] ?? 0;
            $userteam_global['teamCaptainCandidateJoined'] = $scoreMap_global[$user_id]['teamCaptainCandidateJoined'] ?? 0;
            $userteam_global['sourcing'] = $scoreMap_global[$user_id]['sourcing'] ?? 0;
            $userteam_global['screening'] = $scoreMap_global[$user_id]['screening'] ?? 0;
            $userteam_global['evaluation'] = $scoreMap_global[$user_id]['evaluation'] ?? 0;
            $userteam_global['negotiation'] = $scoreMap_global[$user_id]['negotiation'] ?? 0;
            $userteam_global['backout'] = $scoreMap_global[$user_id]['backout'] ?? 0;
            $userteam_global['backoutTeam'] = $scoreMap_global[$user_id]['backoutTeam'] ?? 0;
            $userteam_global['score'] = $scoreMap_global[$user_id]['score'] ?? 0;

            return $userteam_global;
        })->sort(function ($a, $b) {

            // 1️⃣ Active users first
            $aStatus = strtolower($a['status'] ?? 'inactive');
            $bStatus = strtolower($b['status'] ?? 'inactive');

            if ($aStatus !== $bStatus) {
                return ($aStatus === 'active') ? -1 : 1;
            }

            // 2️⃣ Higher score first
            if (($a['score'] ?? 0) !== ($b['score'] ?? 0)) {
                return ($b['score'] ?? 0) <=> ($a['score'] ?? 0);
            }

            // 3️⃣ User ID: ASC
            if (($a['id'] ?? 0) !== ($b['id'] ?? 0)) {
                return ($a['id'] ?? 0) <=> ($b['id'] ?? 0);
            }

            // 3️⃣ Latest created
            // return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        })
            ->values();

        $rankedUsers_global = $rankedUsers_global->map(function ($user, $index) {
            $user['rank'] = $index + 1;

            return $user;
        });
        $topUsers_global = $rankedUsers_global->take(4);
        $currentUser_global = $rankedUsers_global->firstWhere('id', $loggedInUserId);
        if ($currentUser_global && ! $topUsers_global->contains('id', $loggedInUserId)) {
            $topUsers_global->push($currentUser_global);
        }

        $this->topUsers_rank_global = $topUsers_global;
        $this->ownUser_global = $currentUser_global;

        // Total pool for the global Score Board (used for "out of N" + percentile).
        $this->recruiterMetaGlobal = [
            'count' => $rankedUsers_global->count(),
            'active' => $rankedUsers_global->filter(fn ($u) => strtolower($u['status'] ?? 'inactive') === 'active')->count(),
        ];

        // End Your Global Rank: Get all users with sourcing count within date range

        // Start Your Company Global Rank: Get all users with count within date range

        // Step 1: Fetch all users

        $loggedInCompanyId = auth()->user()->company_id;
        //  dd($loggedInCompanyId);
        $userSearchPayload_rank_global_company = [
            'index' => ELASTIC_COMPANIES_INDEX,
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [],
                    ],
                ],
                '_source' => ['id', 'company_name', 'status'],
                'size' => 10000,
                'sort' => [
                    ['id' => ['order' => 'asc']],
                    ['status' => ['order' => 'asc']],
                ],
            ],
        ];

        $userResponse_rank_global_company = $this->elasticService->getClient()->search($userSearchPayload_rank_global_company);
        $usersteam_global_company = collect($userResponse_rank_global_company['hits']['hits'])->pluck('_source');
        // dd($usersteam_global_company);
        // dd($rankstartOfMonthForReport,$rankendOfMonthForReport); $startOfMonthForReport, $endOfMonthForReport,
        $rankQuery_global_company = [
            'index' => ELASTIC_TEAM_RANK,
            'body' => [
                'from' => 0,
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['range' => [
                                'doc.created_at' => [
                                    'gte' => $rankstartOfMonthForReport,
                                    'lte' => $rankendOfMonthForReport,
                                ],
                            ]],
                        ],
                    ],
                ],
                'aggs' => [
                    'user_scores' => [
                        'terms' => [
                            'field' => 'doc.company_id',
                            'size' => 10000,
                        ],
                        'aggs' => [
                            'profileUpload_sum' => ['sum' => ['field' => 'doc.profileUpload']],
                            'interviewScheduled_sum' => ['sum' => ['field' => 'doc.interviewScheduled']],
                            'interviewNoShow_sum' => ['sum' => ['field' => 'doc.interviewNoShow']],
                            'offerReleased_sum' => ['sum' => ['field' => 'doc.offerReleased']],
                            'candidateJoined_sum' => ['sum' => ['field' => 'doc.candidateJoined']],
                            'interestedCandidates_sum' => ['sum' => ['field' => 'doc.interestedCandidates']],
                            'notInterestedCandidates_sum' => ['sum' => ['field' => 'doc.notInterestedCandidates']],
                            // 'teamCaptainInterviewScheduled_sum' => ['sum' => ['field' => 'doc.teamCaptainInterviewScheduled']],
                            // 'teamCaptainInterviewNoShow_sum' => ['sum' => ['field' => 'doc.teamCaptainInterviewNoShow']],
                            // 'teamCaptainOfferReleased_sum' => ['sum' => ['field' => 'doc.teamCaptainOfferReleased']],
                            // 'teamCaptainCandidateJoined_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoined']],
                            'jobsPosted_sum' => ['sum' => ['field' => 'doc.jobsPosted']],
                            'feedbackGivenOnCVs_sum' => ['sum' => ['field' => 'doc.feedbackGivenOnCVs']],
                            'postJobToVendorMarket_sum' => ['sum' => ['field' => 'doc.postJobToVendorMarket']],
                            'outsourceJobsToVendors_sum' => ['sum' => ['field' => 'doc.outsourceJobsToVendors']],
                            'listBenchCandidates_sum' => ['sum' => ['field' => 'doc.listBenchCandidates']],
                            'respondToBenchRequests_sum' => ['sum' => ['field' => 'doc.respondToBenchRequests']],
                            'sourcing_sum' => ['sum' => ['field' => 'doc.sourcing']],
                            'screening_sum' => ['sum' => ['field' => 'doc.screening']],
                            'evaluation_sum' => ['sum' => ['field' => 'doc.evaluation']],
                            'negotiation_sum' => ['sum' => ['field' => 'doc.negotiation']],
                            'backout_sum' => ['sum' => ['field' => 'doc.backout']],
                            'backoutTeam_sum' => ['sum' => ['field' => 'doc.backoutTeam']],
                            'candidateJoinedContract_sum' => ['sum' => ['field' => 'doc.candidateJoinedContract']],
                            'teamCaptainCandidateJoinedContract_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoinedContract']],
                            'backoutContract_sum' => ['sum' => ['field' => 'doc.backoutContract']],
                            'backoutTeamContract_sum' => ['sum' => ['field' => 'doc.backoutTeamContract']],
                        ],
                    ],
                ],
            ],
        ];

        $response_global_company = $this->elasticService->getClient()->search($rankQuery_global_company);
        $buckets_global_company = $response_global_company['aggregations']['user_scores']['buckets'];

        $scoreMap_global_company = collect($buckets_global_company)->mapWithKeys(function ($item) {
            $score =
                ($item['profileUpload_sum']['value'] ?? 0) +
                ($item['interviewScheduled_sum']['value'] ?? 0) +
                ($item['interviewNoShow_sum']['value'] ?? 0) +
                ($item['offerReleased_sum']['value'] ?? 0) +
                ($item['candidateJoined_sum']['value'] ?? 0) +
                // ($item['teamCaptainInterviewScheduled_sum']['value'] ?? 0) +
                // ($item['teamCaptainOfferReleased_sum']['value'] ?? 0) +
                // ($item['teamCaptainCandidateJoined_sum']['value'] ?? 0) +
                ($item['jobsPosted_sum']['value'] ?? 0) +
                ($item['feedbackGivenOnCVs_sum']['value'] ?? 0) +
                ($item['postJobToVendorMarket_sum']['value'] ?? 0) +
                ($item['outsourceJobsToVendors_sum']['value'] ?? 0) +
                ($item['listBenchCandidates_sum']['value'] ?? 0) +
                ($item['respondToBenchRequests_sum']['value'] ?? 0) +
                ($item['sourcing_sum']['value'] ?? 0) +
                ($item['screening_sum']['value'] ?? 0) +
                ($item['evaluation_sum']['value'] ?? 0) +
                ($item['negotiation_sum']['value'] ?? 0) +
                ($item['backout_sum']['value'] ?? 0) +
                ($item['backoutTeam_sum']['value'] ?? 0) + ($item['candidateJoinedContract_sum']['value'] ?? 0) + ($item['teamCaptainCandidateJoinedContract_sum']['value'] ?? 0) + ($item['backoutContract_sum']['value'] ?? 0) + ($item['backoutTeamContract_sum']['value'] ?? 0);

            return [$item['key'] => [
                'company_id' => $item['key'],
                'profileUpload' => ($item['profileUpload_sum']['value'] ?? 0),
                'interviewScheduled' => ($item['interviewScheduled_sum']['value'] ?? 0),
                'interviewNoShow' => ($item['interviewNoShow_sum']['value'] ?? 0),
                'offerReleased' => ($item['offerReleased_sum']['value'] ?? 0),
                'candidateJoined' => ($item['candidateJoined_sum']['value'] ?? 0),
                // 'teamCaptainInterviewScheduled' => ($item['teamCaptainInterviewScheduled_sum']['value'] ?? 0),
                // 'teamCaptainOfferReleased' => ($item['teamCaptainOfferReleased_sum']['value'] ?? 0),
                // 'teamCaptainCandidateJoined' => ($item['teamCaptainCandidateJoined_sum']['value'] ?? 0),
                'jobsPosted' => ($item['jobsPosted_sum']['value'] ?? 0),
                'feedbackGivenOnCVs' => ($item['feedbackGivenOnCVs_sum']['value'] ?? 0),
                'postJobToVendorMarket' => ($item['postJobToVendorMarket_sum']['value'] ?? 0),
                'outsourceJobsToVendors' => ($item['outsourceJobsToVendors_sum']['value'] ?? 0),
                'listBenchCandidates' => ($item['listBenchCandidates_sum']['value'] ?? 0),
                'respondToBenchRequests' => ($item['respondToBenchRequests_sum']['value'] ?? 0),
                'sourcing' => ($item['sourcing_sum']['value'] ?? 0),
                'screening' => ($item['screening_sum']['value'] ?? 0),
                'evaluation' => ($item['evaluation_sum']['value'] ?? 0),
                'negotiation' => ($item['negotiation_sum']['value'] ?? 0),
                'backout' => ($item['backout_sum']['value'] ?? 0),
                'backoutTeam' => ($item['backoutTeam_sum']['value'] ?? 0),
                'candidateJoinedContract' => ($item['candidateJoinedContract_sum']['value'] ?? 0),
                'teamCaptainCandidateJoinedContract' => ($item['teamCaptainCandidateJoinedContract_sum']['value'] ?? 0),
                'backoutContract' => ($item['backoutContract_sum']['value'] ?? 0),
                'backoutTeamContract' => ($item['backoutTeamContract_sum']['value'] ?? 0),
                'score' => $score,
            ]];
        });

        $rankedUsers_global_company = $usersteam_global_company->map(function ($userteam_global_company) use ($scoreMap_global_company) {
            // Intent: Null-coalesce company id to prevent undefined array key exceptions when Elasticsearch hits omit 'id'
            $company_id = $userteam_global_company['id'] ?? null;
            $userteam_global_company['profileUpload'] = $scoreMap_global_company[$company_id]['profileUpload'] ?? 0;
            $userteam_global_company['interviewScheduled'] = $scoreMap_global_company[$company_id]['interviewScheduled'] ?? 0;
            $userteam_global_company['interviewNoShow'] = $scoreMap_global_company[$company_id]['interviewNoShow'] ?? 0;
            $userteam_global_company['offerReleased'] = $scoreMap_global_company[$company_id]['offerReleased'] ?? 0;
            $userteam_global_company['candidateJoined'] = $scoreMap_global_company[$company_id]['candidateJoined'] ?? 0;
            $userteam_global_company['interestedCandidates'] = $scoreMap_global_company[$company_id]['interestedCandidates'] ?? 0;
            $userteam_global_company['notInterestedCandidates'] = $scoreMap_global_company[$company_id]['notInterestedCandidates'] ?? 0;
            // $userteam_global_company['teamCaptainInterviewScheduled'] = $scoreMap_global_company[$company_id]['teamCaptainInterviewScheduled'] ?? 0;
            // $userteam_global_company['teamCaptainInterviewNoShow'] = $scoreMap_global_company[$company_id]['teamCaptainInterviewNoShow'] ?? 0;
            // $userteam_global_company['teamCaptainOfferReleased'] = $scoreMap_global_company[$company_id]['teamCaptainOfferReleased'] ?? 0;
            // $userteam_global_company['teamCaptainCandidateJoined'] = $scoreMap_global_company[$company_id]['teamCaptainCandidateJoined'] ?? 0;
            $userteam_global_company['jobsPosted'] = $scoreMap_global_company[$company_id]['jobsPosted'] ?? 0;
            $userteam_global_company['feedbackGivenOnCVs'] = $scoreMap_global_company[$company_id]['feedbackGivenOnCVs'] ?? 0;
            $userteam_global_company['postJobToVendorMarket'] = $scoreMap_global_company[$company_id]['postJobToVendorMarket'] ?? 0;
            $userteam_global_company['outsourceJobsToVendors'] = $scoreMap_global_company[$company_id]['outsourceJobsToVendors'] ?? 0;
            $userteam_global_company['candidateJoined'] = $scoreMap_global_company[$company_id]['candidateJoined'] ?? 0;
            $userteam_global_company['listBenchCandidates'] = $scoreMap_global_company[$company_id]['listBenchCandidates'] ?? 0;
            $userteam_global_company['respondToBenchRequests'] = $scoreMap_global_company[$company_id]['respondToBenchRequests'] ?? 0;
            $userteam_global_company['sourcing'] = $scoreMap_global_company[$company_id]['sourcing'] ?? 0;
            $userteam_global_company['screening'] = $scoreMap_global_company[$company_id]['screening'] ?? 0;
            $userteam_global_company['evaluation'] = $scoreMap_global_company[$company_id]['evaluation'] ?? 0;
            $userteam_global_company['negotiation'] = $scoreMap_global_company[$company_id]['negotiation'] ?? 0;
            $userteam_global_company['backout'] = $scoreMap_global_company[$company_id]['backout'] ?? 0;
            $userteam_global_company['backoutTeam'] = $scoreMap_global_company[$company_id]['backoutTeam'] ?? 0;
            $userteam_global_company['score'] = $scoreMap_global_company[$company_id]['score'] ?? 0;

            return $userteam_global_company;
        })->sort(function ($a, $b) {

            // 1️⃣ Active users first
            $aStatus = strtolower($a['status'] ?? 'inactive');
            $bStatus = strtolower($b['status'] ?? 'inactive');

            if ($aStatus !== $bStatus) {
                return ($aStatus === 'active') ? -1 : 1;
            }

            // 2️⃣ Higher score first
            if (($a['score'] ?? 0) !== ($b['score'] ?? 0)) {
                return ($b['score'] ?? 0) <=> ($a['score'] ?? 0);
            }

            // 3️⃣ User ID: ASC
            if (($a['id'] ?? 0) !== ($b['id'] ?? 0)) {
                return ($a['id'] ?? 0) <=> ($b['id'] ?? 0);
            }

            // 3️⃣ Latest created
            // return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        })
            ->values();

        $rankedUsers_global_company = $rankedUsers_global_company->map(function ($user, $index) {
            $user['rank'] = $index + 1;

            return $user;
        });
        $topUsers_global_company = $rankedUsers_global_company->take(4);
        $currentUser_global_company = $rankedUsers_global_company->firstWhere('id', $loggedInCompanyId);
        if ($currentUser_global_company && ! $topUsers_global_company->contains('id', $loggedInCompanyId)) {
            $topUsers_global_company->push($currentUser_global_company);
        }

        $this->topUsers_rank_global_company = $topUsers_global_company;
        $this->ownUser_global_company = $currentUser_global_company;
        // For the Company Score Board modal: total ranked companies (percentile) + a fuller top-7 leaderboard.
        $this->companyRankTotal = $rankedUsers_global_company->count();
        $this->companyLeaders = $rankedUsers_global_company->take(7)->values();

        // dd($this->ownUser_global_company);

        // End Your Company Global Rank: Get all users with sourcing count within date range

        // Start daily 1: Get top users by candidate count (excluding current user)

        $staffingPartnerSearchPayload_daily = [
            'index' => ELASTIC_CLIENT_LIST,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'nested' => [
                                    'path' => 'staffing_company',
                                    'query' => [
                                        'bool' => [
                                            'must' => [
                                                ['term' => ['staffing_company.from_company_id' => $company_id]],
                                                ['term' => ['staffing_company.final_status' => 1]],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                '_source' => ['id', 'company_id', 'company_name', 'client_location', 'email'],
                'size' => 10000,
            ],
        ];

        $staffingPartnerResponse_daily = $this->elasticService->getClient()->search($staffingPartnerSearchPayload_daily);
        $staffingPartners_daily = collect($staffingPartnerResponse_daily['hits']['hits'])->pluck('_source');

        // Step 2: Get candidate counts by user_id from ELASTIC_CANDIDATES for this month

        // Step 1: Build the reusable $boolQuery
        $boolQuerystaffing_daily = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['range' => ['ClientComID' => ['gt' => 0]]],
                                ['range' => ['job_id' => ['gt' => 0]]],
                            ], [
                                ['bool' => [
                                    'must_not' => [
                                        ['terms' => ['job_master_status_id' => [8, 9]]],
                                    ],
                                ]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [
                    ['range' => [
                        'created_at' => [
                            'gte' => $startOfPreviousDay,
                            'lte' => $endOfDayForReport,
                        ],
                    ]],
                ],
            ],
        ];
        // dd($$startOfPreviousDay,$endOfDayForReport);
        // Step 2: Final candidate aggregation payload with query and aggregation
        $staffingAggPayload_daily = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'size' => 0,
                'query' => $boolQuerystaffing_daily, // use merged query directly
                'aggs' => [
                    'user_uploads' => [
                        'terms' => [
                            'field' => 'ClientComID',
                            'size' => 10000,
                            'order' => ['_count' => 'desc'],
                        ],
                    ],
                ],
            ],
        ];

        $staffingResponse_daily = $this->elasticService->getClient()->search($staffingAggPayload_daily);
        $staffing_aggBuckets_daily = $staffingResponse_daily['aggregations']['user_uploads']['buckets'];
        // Step 3: Map counts to users and sort

        $staffing_uploadsMap_daily = collect($staffing_aggBuckets_daily)->mapWithKeys(function ($item) {
            return [$item['key'] => $item['doc_count']];
        });

        // dd($staffing_uploadsMap_daily,$staffinguser_daily['upload_count']);

        $rankedstaffing_daily = $staffingPartners_daily->map(function ($staffinguser_daily) use ($staffing_uploadsMap_daily) {
            $staffinguser_daily['upload_count'] = $staffing_uploadsMap_daily[$staffinguser_daily['company_id']] ?? 0;

            return $staffinguser_daily;
        })->sortByDesc('upload_count')->values();
        // dd($rankedstaffing_daily);

        // Add rank (starting from 1)
        $rankedstaffing_daily = $rankedstaffing_daily->values()->map(function ($staffinguser_daily, $staffingindex_daily) {
            $staffinguser_daily['rank'] = $staffingindex_daily + 1;

            return $staffinguser_daily;
        });
        $topStaffing_daily = $rankedstaffing_daily->take(4);

        $staffingDailyLabels = [];
        $staffingDailyValues = [];

        if (count($topStaffing_daily) > 0) {
            foreach ($topStaffing_daily as $key => $val_ob) {
                $val = (object) $val_ob;
                $staffingDailyLabels[] = ucwords($val->company_name);
                $staffingDailyValues[] = $val->upload_count ?? 0;
            }
        }
        $this->staffingDailyLabels = $staffingDailyLabels;
        $this->staffingDailyValues = $staffingDailyValues;

        $this->graph_staffing_partner_daily_report = $topStaffing_daily;

        // dd($startOfPreviousDay,$endOfDayForReport);

        // End daily 1: Get top users by candidate count (excluding current user)

        // Start Monthly: Get all users with sourcing count within date range

        $staffingPartnerSearchPayload_monthly = [
            'index' => ELASTIC_CLIENT_LIST,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'nested' => [
                                    'path' => 'staffing_company',
                                    'query' => [
                                        'bool' => [
                                            'must' => [
                                                ['term' => ['staffing_company.from_company_id' => $company_id]],
                                                ['term' => ['staffing_company.final_status' => 1]],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                '_source' => ['id', 'company_id', 'company_name', 'client_location', 'email'],
                'size' => 10000,
            ],
        ];

        $staffingPartnerResponse_monthly = $this->elasticService->getClient()->search($staffingPartnerSearchPayload_monthly);
        $staffingPartners_monthly = collect($staffingPartnerResponse_monthly['hits']['hits'])->pluck('_source');

        // Step 2: Get candidate counts by user_id from ELASTIC_CANDIDATES for this month

        // Step 1: Build the reusable $boolQuery
        $boolQuerystaffing_monthly = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['job_master_status_id' => 6]],
                                ['range' => ['ClientComID' => ['gt' => 0]]],
                                ['range' => ['job_id' => ['gt' => 0]]],
                            ], [
                                ['bool' => [
                                    'must_not' => [
                                        ['term' => ['master_status_id' => 8]],
                                    ],
                                ]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [
                    ['range' => [
                        'of_joined_date' => [
                            'gte' => Carbon::now()->subMonth()->startOfMonth()->toDateString(),
                            'lte' => Carbon::now()->subMonth()->endOfMonth()->toDateString(),
                        ],
                    ]],
                    ['term' => ['job_master_status_id' => 6]],
                ],
            ],
        ];
        // dd($startOfMonthForReport,$endOfMonthForReport);
        // Step 2: Final candidate aggregation payload with query and aggregation
        $staffingAggPayload_monthly = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'size' => 0,
                'query' => $boolQuerystaffing_monthly, // use merged query directly
                'aggs' => [
                    'user_uploads' => [
                        'terms' => [
                            'field' => 'ClientComID',
                            'size' => 10000,
                            'order' => ['_count' => 'desc'],
                        ],
                    ],
                ],
            ],
        ];

        $staffingResponse_monthly = $this->elasticService->getClient()->search($staffingAggPayload_monthly);
        $staffing_aggBuckets_monthly = $staffingResponse_monthly['aggregations']['user_uploads']['buckets'];
        // Step 3: Map counts to users and sort
        // dd($staffing_aggBuckets_monthly);

        $staffing_uploadsMap_monthly = collect($staffing_aggBuckets_monthly)->mapWithKeys(function ($item) {
            return [$item['key'] => $item['doc_count']];
        });

        // dd($staffing_uploadsMap_monthly,$staffinguser_monthly['upload_count']);

        $rankedstaffing_monthly = $staffingPartners_monthly->map(function ($staffinguser_monthly) use ($staffing_uploadsMap_monthly) {
            $staffinguser_monthly['upload_count'] = $staffing_uploadsMap_monthly[$staffinguser_monthly['company_id']] ?? 0;

            return $staffinguser_monthly;
        })->sortByDesc('upload_count')->values();
        // dd($rankedstaffing_monthly);

        // Add rank (starting from 1)
        $rankedstaffing_monthly = $rankedstaffing_monthly->values()->map(function ($staffinguser_monthly, $staffingindex_monthly) {
            $staffinguser_monthly['rank'] = $staffingindex_monthly + 1;

            return $staffinguser_monthly;
        });
        $topStaffing_monthly = $rankedstaffing_monthly->take(4);

        $staffingMonthlyLabels = [];
        $staffingMonthlyValues = [];

        if (count($topStaffing_monthly) > 0) {
            foreach ($topStaffing_monthly as $key => $val_ob) {
                $val = (object) $val_ob;
                $staffingMonthlyLabels[] = ucwords($val->company_name);
                $staffingMonthlyValues[] = $val->upload_count ?? 0;
            }
        }
        $this->staffingMonthlyLabels = $staffingMonthlyLabels;
        $this->staffingMonthlyValues = $staffingMonthlyValues;

        $this->graph_staffing_partner_monthly_report = $topStaffing_monthly;

        // End Monthly: Get all users with sourcing count within date range

        //  Start Job Request for Service Company & staffing

        $page = 1;
        $size = 5; // Default 20 results per page
        $from = ($page - 1) * $size;
        $filters = [];
        $final_list = [];
        $params = [
            'index' => ELASTIC_JOB_REQUEST,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => ['final_status' => ['order' => 'asc', 'unmapped_type' => 'long']],
                'query' => [
                    'bool' => [
                        'should' => [
                            // First condition
                            [
                                'bool' => [
                                    'must' => array_merge([
                                        ['term' => ['to_company_id' => $company_id]],
                                        ['term' => ['final_status' => 0]],
                                    ]),
                                ],
                            ],
                        ],
                        'minimum_should_match' => 1,
                        'filter' => $filters, // We'll add all filters here
                    ],
                ],
            ],
        ];

        // dd($params);

        if (! $this->elasticService->indexExists(ELASTIC_JOB_REQUEST)) {
            $this->total_pending = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $this->total_pending = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
            $final_list = $data;
            if ($this->total_pending < $size) {
                $final_list = [];
                $newSize = $size - $this->total_pending;

                $params_accept = [
                    'index' => ELASTIC_JOB_REQUEST,
                    'request_cache' => true,
                    'body' => [
                        'from' => $from,
                        'size' => $newSize,
                        'sort' => ['final_status' => ['order' => 'asc', 'unmapped_type' => 'long']],
                        'query' => [
                            'bool' => [
                                'should' => [
                                    // First condition
                                    [
                                        'bool' => [
                                            'must' => array_merge([
                                                ['term' => ['to_company_id' => $company_id]],
                                                ['term' => ['final_status' => 1]],
                                            ]),
                                        ],
                                    ],
                                ],
                                'minimum_should_match' => 1,
                                'filter' => $filters, // We'll add all filters here
                            ],
                        ],
                    ],
                ];

                if (! $this->elasticService->indexExists(ELASTIC_JOB_REQUEST)) {
                    $total_accept = 0;
                    $data_accept = [];
                } else {
                    $response_accept = $this->elasticService->getClient()->search($params_accept);
                    $total_accept = $response_accept['hits']['total']['value'] ?? 0;
                    $data_accept = array_map(function ($hit) {
                        return $hit['_source'];
                    }, $response_accept['hits']['hits']);
                }

                $final_list = array_merge($data, $data_accept);

            }

        }

        $this->final_list = $final_list;

        // End Job Request for Service Company & staffing

        //  Start Bench Talent Request

        $page = 1;
        $size = 5; // Default 20 results per page
        $from = ($page - 1) * $size;
        $filters = [];
        $candidate_final_list = [];
        $params_candidate_req = [
            'index' => ELASTIC_CANDIDATES_REQUEST,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => ['id' => ['order' => 'desc', 'unmapped_type' => 'long']],
                'query' => [
                    'bool' => [
                        'should' => [
                            // First condition
                            [
                                'bool' => [
                                    'must' => array_merge([
                                        ['term' => ['source_company_id' => $company_id]],
                                        ['term' => ['ac_status' => 0]],
                                    ]),
                                ],
                            ],
                        ],
                        'minimum_should_match' => 1,
                        'filter' => $filters, // We'll add all filters here
                    ],
                ],
            ],
        ];

        // dd($params);

        if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES_REQUEST)) {
            $this->total_candidate_req = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params_candidate_req);
            $this->total_candidate_req = $response['hits']['total']['value'] ?? 0;
            $data_candidate_req = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
            $candidate_final_list = $data_candidate_req;
            if ($this->total_candidate_req < $size) {
                $candidate_final_list = [];
                $newSize = $size - $this->total_candidate_req;

                $params_accept_candidate_req = [
                    'index' => ELASTIC_CANDIDATES_REQUEST,
                    'request_cache' => true,
                    'body' => [
                        'from' => $from,
                        'size' => $newSize,
                        'sort' => ['id' => ['order' => 'desc', 'unmapped_type' => 'long']],
                        'query' => [
                            'bool' => [
                                'should' => [
                                    // First condition
                                    [
                                        'bool' => [
                                            'must' => array_merge([
                                                ['term' => ['source_company_id' => $company_id]],
                                                ['term' => ['ac_status' => 1]],
                                            ]),
                                        ],
                                    ],
                                ],
                                'minimum_should_match' => 1,
                                'filter' => $filters, // We'll add all filters here
                            ],
                        ],
                    ],
                ];

                if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES_REQUEST)) {
                    $total_accept_candidate_req = 0;
                    $data_accept_candidate_req = [];
                } else {
                    $response_accept_candidate_req = $this->elasticService->getClient()->search($params_accept_candidate_req);
                    $total_accept_candidate_req = $response_accept_candidate_req['hits']['total']['value'] ?? 0;
                    $data_accept_candidate_req = array_map(function ($hit) {
                        return $hit['_source'];
                    }, $response_accept_candidate_req['hits']['hits']);
                }

                $candidate_final_list = array_merge($data_candidate_req, $data_accept_candidate_req);

            }

        }

        $this->candidate_final_list = $candidate_final_list;

        // dd($this->candidate_final_list);

        // End Bench Talent Request

        /*
         * Daily Leaderboards wiring (intent): the dashboard "Daily Leaderboards" block
         * (Teams / Captains / Agencies) reads $teamDailyReport, $captainDailyReport,
         * $agencyDailyReport (+ matching *Meta). The daily ranks themselves are already
         * computed above ($rankedUsers_daily_internal_rank, $this->topUsersCaptainDaily,
         * $rankedstaffing_daily); this block only reshapes them into the
         * {name, value, rank, is_me} rows the view expects — no ranking/business logic
         * is changed. Tenancy: every source list is already company-scoped (built from
         * this company's ES users / staffing partners), so no extra filter is needed.
         */
        $dashLoggedInUserId = (int) auth()->user()->id;

        // Teams Daily Report — top 4 team members by daily score (+ the logged-in user if outside the top 4).
        $teamDailySource = isset($rankedUsers_daily_internal_rank) ? collect($rankedUsers_daily_internal_rank) : collect();
        $teamDailyTop = $teamDailySource->take(4);
        $teamDailyMe = $teamDailySource->firstWhere('id', $dashLoggedInUserId);
        if ($teamDailyMe && ! $teamDailyTop->contains('id', $dashLoggedInUserId)) {
            $teamDailyTop = $teamDailyTop->push($teamDailyMe);
        }
        $this->teamDailyReport = $teamDailyTop->values()->map(function ($u) use ($dashLoggedInUserId) {
            $u = (array) $u;

            return [
                'name' => ucwords(strtolower($u['name'] ?? '')),
                'value' => (int) ($u['score'] ?? 0),
                'rank' => (int) ($u['rank'] ?? 0),
                'is_me' => (int) ($u['id'] ?? 0) === $dashLoggedInUserId,
            ];
        })->values();
        $this->teamDailyMeta = [
            'count' => $teamDailySource->count(),
            'total' => (int) $teamDailySource->sum(fn ($u) => (int) (((array) $u)['score'] ?? 0)),
        ];

        // Captains Daily Report — top 5 captains by today's captain score, plus the logged-in user's
        // own position if they're a captain outside the top 5. A "captain" = a user assigned as the
        // spoke (spok_user_id) on at least one of this company's ACTIVE jobs, so captains are listed
        // even with zero captain points today. Tenancy: raw DB::table, scoped by jobs.company_id (Rule #3).
        $captainUserIds = DB::table('job_assign')
            ->join('jobs', 'jobs.id', '=', 'job_assign.job_id')
            ->where('jobs.company_id', $this->company_id)
            ->where('jobs.status', 'active')
            ->whereNotNull('job_assign.spok_user_id')
            ->distinct()
            ->pluck('job_assign.spok_user_id')
            ->map(fn ($v) => (int) $v)
            ->all();

        $captainAllRanked = collect($rankedUsers_daily_internal_rank ?? [])
            ->filter(fn ($u) => in_array((int) (((array) $u)['id'] ?? 0), $captainUserIds, true))
            ->map(function ($u) {
                $u = (array) $u;
                $u['captainScore'] = (int) ($u['teamCaptainInterviewScheduled'] ?? 0)
                    + (int) ($u['teamCaptainOfferReleased'] ?? 0)
                    + (int) ($u['teamCaptainCandidateJoined'] ?? 0);

                return $u;
            })
            ->sortByDesc('captainScore')
            ->values()
            ->map(function ($u, $i) {
                $u['captainRank'] = $i + 1;

                return $u;
            });

        $captainTop = $captainAllRanked->take(5);
        $captainMe = $captainAllRanked->firstWhere('id', $dashLoggedInUserId);
        if ($captainMe && ! $captainTop->contains('id', $dashLoggedInUserId)) {
            $captainTop = $captainTop->push($captainMe);   // append "YOU" row with the real rank
        }
        $this->captainDailyReport = $captainTop->values()->map(function ($u) use ($dashLoggedInUserId) {
            $u = (array) $u;

            return [
                'name' => ucwords(strtolower($u['name'] ?? '')),
                'value' => (int) ($u['captainScore'] ?? 0),
                'rank' => (int) ($u['captainRank'] ?? 0),
                'is_me' => (int) ($u['id'] ?? 0) === $dashLoggedInUserId,
            ];
        })->values();
        $this->captainDailyMeta = [
            'count' => $captainAllRanked->count(),
            'total' => (int) $captainAllRanked->sum(fn ($u) => (int) (((array) $u)['captainScore'] ?? 0)),
        ];

        // Agency Daily Report — top staffing partners by today's profile uploads.
        $agencyDailySource = isset($rankedstaffing_daily) ? collect($rankedstaffing_daily) : collect();
        $this->agencyDailyReport = $agencyDailySource->take(4)->map(function ($a) {
            $a = (array) $a;

            return [
                'name' => ucwords(strtolower($a['company_name'] ?? '')),
                'value' => (int) ($a['upload_count'] ?? 0),
                'rank' => (int) ($a['rank'] ?? 0),
                'is_me' => false,
            ];
        })->values();
        $this->agencyDailyMeta = [
            'count' => $agencyDailySource->count(),
            'total' => (int) $agencyDailySource->sum(fn ($a) => (int) (((array) $a)['upload_count'] ?? 0)),
        ];

        $responseData = view('service_company.dashboard.index', $this->data)->render();

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_DASHBOARD_CACHES_TIME));

        return $responseData;

    }

    public function home()
    {

        // echo 	$this->user->last_login; exit;

        // echo '<pre>'; print_r(session()->all());  exit;

        $company_id = $this->user->company_id;
        $rolesCustome = $this->user->getAllPermissions();
        $this->admin = $rolesCustome[0]->name;

        $lastSevenDate = date('Y-m-d', strtotime('-7 days'));
        $todayDate = date('Y-m-d H:i:s');
        $nextSevenDate = date('Y-m-d', strtotime('+7 days'));
        // exit;
        // $start_date 		=	date("Y-m-d", strtotime($start_date) );
        // $end_date 			=	date("Y-m-d", strtotime($end_date) );
        // $application_data 	=	$application_data->whereBetween('job_applications.created_at', [$start_date, $end_date] );

        // echo $this->admin; exit;

        if ($this->admin == 'admin') {

            // Top Active Tiles
            $this->totalOpenings = Job::whereBetween('jobs.created_at', [$lastSevenDate, $todayDate])->where('jobs.status', '=', 'active')->count();
            // Top Total Applicant
            $allApplications = JobApplication::join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
                ->whereBetween('job_applications.created_at', [$lastSevenDate, $todayDate])
                ->get();
            $this->totalApplications = count($allApplications);

            // Offered 5
            $this->totalOffered = JobApplication::join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
                ->where('job_applications.master_status_id', '=', 5)
                ->whereBetween('job_applications.updated_at', [$lastSevenDate, $todayDate])
                ->count();
            // Joined 6
            $this->totalJoined = JobApplication::join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
                ->where('job_applications.master_status_id', '=', 6)
                ->whereBetween('job_applications.updated_at', [$lastSevenDate, $todayDate])
                ->count();
            // All Requisitions
            $this->jobListing = Job::select('clients.name as clients_name', 'jobs.title', 'jobs.total_positions', DB::raw('count(job_applications.id) as allCandidate'));
            $this->jobListing = $this->jobListing->join('clients', 'clients.id', 'jobs.client_id');
            $this->jobListing = $this->jobListing->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id');
            $this->jobListing = $this->jobListing->where('status', '=', 'active');
            $this->jobListing = $this->jobListing->whereBetween('jobs.created_at', [$lastSevenDate, $todayDate]);
            $this->jobListing = $this->jobListing->take(4);
            $this->jobListing = $this->jobListing->groupBy('jobs.id');
            $this->jobListing = $this->jobListing->orderBy('jobs.id', 'DESC');
            $this->jobListing = $this->jobListing->get();

            // New Joining listing
            $this->newJoining_count = JobApplication::select('job_applications.id', 'job_applications.full_name', 'jobs.title', 'clients.name as clients_name')
                ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
                ->join('clients', 'clients.id', 'jobs.client_id')
                ->where('job_applications.master_status_id', '=', 6)
                ->take(4)
                ->count();

            $this->newJoining = JobApplication::select('job_applications.id', 'job_applications.full_name', 'jobs.title', 'clients.name as clients_name')
                ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
                ->join('clients', 'clients.id', 'jobs.client_id')
                ->where('job_applications.master_status_id', '=', 6)
                ->take(4)
                ->get();

            // Interview Schedule
            $this->intSchedules_count = InterviewSchedule::select('interview_schedules.schedule_date', 'job_applications.full_name', 'jobs.title', 'clients.name as clients_name')
                                                    // ->with(['employee','employee.user'])
                ->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
                ->join('jobs', 'jobs.id', 'job_applications.job_id')
                ->join('clients', 'clients.id', 'jobs.client_id')
                ->whereBetween('interview_schedules.schedule_date', [$todayDate, $nextSevenDate])
                ->take(4)
                ->orderBy('interview_schedules.id', 'DESC')
                ->count();

            $this->intSchedules = InterviewSchedule::select('interview_schedules.schedule_date', 'job_applications.full_name', 'jobs.title', 'clients.name as clients_name')
                                                   // ->with(['employee','employee.user'])
                ->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
                ->join('jobs', 'jobs.id', 'job_applications.job_id')
                ->join('clients', 'clients.id', 'jobs.client_id')
                ->whereBetween('interview_schedules.schedule_date', [$todayDate, $nextSevenDate])
                ->take(4)
                ->orderBy('interview_schedules.id', 'DESC')
                ->get();

            // Follow up candidate listing OFFered
            $this->followupApp_count = JobApplication::select('job_applications.id', 'job_applications.created_at', 'job_applications.full_name', 'jobs.title', 'clients.name as clients_name')
                ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
                ->join('clients', 'clients.id', 'jobs.client_id')
                ->where('job_applications.master_status_id', '=', 5)
                                                     // ->whereBetween('job_applications.updated_at', [$todayDate, $nextSevenDate])
                ->take(4)
                ->orderBy('job_applications.id', 'DESC')
                ->count();

            $this->followupApp = JobApplication::select('job_applications.id', 'job_applications.created_at', 'job_applications.full_name', 'jobs.title', 'clients.name as clients_name')
                ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
                ->join('clients', 'clients.id', 'jobs.client_id')
                ->where('job_applications.master_status_id', '=', 5)
                                                     // ->whereBetween('job_applications.updated_at', [$todayDate, $nextSevenDate])
                ->take(4)
                ->orderBy('job_applications.id', 'DESC')
                ->get();

            $this->offer = JobApplication::select('job_applications.id', 'job_applications.full_name', 'jobs.title', 'job_applications.current_designation', 'job_applications.ctc', 'job_applications.ectc', 'master_status.name',
                'clients.name as client_name',
                'job_applications.proposed_ctc', 'job_applications.any_offer', 'job_applications.document', 'job_applications.sub_for_approval', 'job_applications.offer_approved', 'job_applications.offer_release', 'job_applications.offer_acceptance', 'job_applications.joined', 'job_applications.joined_date', 'job_applications.job_id'

            )
                ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
                ->join('clients', 'clients.id', 'jobs.client_id')
                ->join('master_status', 'master_status.id', 'job_applications.master_status_id')
                ->where('job_applications.master_status_id', '=', 6)
                                                 // ->whereBetween('job_applications.updated_at', [$todayDate, $nextSevenDate])
                ->take(4)
                ->orderBy('job_applications.joined_date', 'DESC')
                ->get();

            // echo '<pre>'; print_r($this->offer);

        } else {

            // WORKING JOBS
            $this->totalOpenings = Job::join('job_assign', 'job_assign.job_id', 'jobs.id')->where('jobs.id', '>', '0')
                ->whereBetween('jobs.created_at', [$lastSevenDate, $todayDate])
                ->where('job_assign.user_id', '=', $this->user->id)
                ->where('jobs.status', '=', 'active')
                ->count();

            // ALL ASSIGN ASSIGNMENT
            $allApplications = JobApplication::join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
                ->where('job_applications.submitted_by', '=', $this->user->id)
                ->whereBetween('job_applications.created_at', [$lastSevenDate, $todayDate])
                ->get();
            $this->totalApplications = count($allApplications);

            // OFFERED 5
            $this->totalOffered = JobApplication::join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
                ->where('job_applications.master_status_id', '=', 5)
                ->where('job_applications.submitted_by', '=', $this->user->id)
                ->whereBetween('job_applications.updated_at', [$lastSevenDate, $todayDate])
                ->count();

            // Joined 6
            $this->totalJoined = JobApplication::join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')->whereBetween('job_applications.updated_at', [$lastSevenDate, $todayDate])
                ->where('job_applications.submitted_by', '=', $this->user->id)
                ->where('job_applications.master_status_id', '=', 6)
                ->count();

            // Interview Schedule
            $this->intSchedules_count = InterviewSchedule::select('interview_schedules.schedule_date', 'job_applications.full_name', 'jobs.title', 'clients.name as clients_name')
                                                        // ->with(['employee','employee.user'])
                ->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
                ->join('jobs', 'jobs.id', 'job_applications.job_id')
                ->join('clients', 'clients.id', 'jobs.client_id')
                ->whereBetween('interview_schedules.schedule_date', [$todayDate, $nextSevenDate])
                ->where('job_applications.submitted_by', '=', $this->user->id)
                ->take(4)
                ->orderBy('interview_schedules.id', 'DESC')
                ->count();

            $this->intSchedules = InterviewSchedule::select('interview_schedules.schedule_date', 'job_applications.full_name', 'jobs.title', 'clients.name as clients_name')
                                                        // ->with(['employee','employee.user'])
                ->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
                ->join('jobs', 'jobs.id', 'job_applications.job_id')
                ->join('clients', 'clients.id', 'jobs.client_id')
                ->whereBetween('interview_schedules.schedule_date', [$todayDate, $nextSevenDate])
                ->where('job_applications.submitted_by', '=', $this->user->id)
                ->take(4)
                ->orderBy('interview_schedules.id', 'DESC')
                ->get();

            // All Requisitions

            $this->jobListing = Job::select('clients.name as clients_name', 'jobs.title', 'jobs.total_positions',
                DB::raw('count(job_applications.id) as allCandidate')
            );
            $this->jobListing = $this->jobListing->join('clients', 'clients.id', 'jobs.client_id');
            $this->jobListing = $this->jobListing->join('job_assign', 'job_assign.job_id', 'jobs.id');
            $this->jobListing = $this->jobListing->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id');

            $this->jobListing = $this->jobListing->where('status', '=', 'active');
            $this->jobListing = $this->jobListing->where('job_assign.user_id', '=', $this->user->id);
            $this->jobListing = $this->jobListing->take(4);
            $this->jobListing = $this->jobListing->groupBy('jobs.id');
            $this->jobListing = $this->jobListing->orderBy('jobs.id', 'DESC');
            $this->jobListing = $this->jobListing->get();

            // New Joining listing
            $this->newJoining_count = JobApplication::select('job_applications.id', 'job_applications.full_name', 'jobs.title', 'clients.name as clients_name')
                ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
                ->join('clients', 'clients.id', 'jobs.client_id')
                ->where('job_applications.master_status_id', '=', 6)
                ->where('job_applications.submitted_by', '=', $this->user->id)
                ->take(4)
                ->count();

            $this->newJoining = JobApplication::select('job_applications.id', 'job_applications.full_name', 'jobs.title', 'clients.name as clients_name')
                ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
                ->join('clients', 'clients.id', 'jobs.client_id')
                ->where('job_applications.master_status_id', '=', 6)
                ->where('job_applications.submitted_by', '=', $this->user->id)
                ->take(4)
                ->get();

            $this->followupApp_count = JobApplication::select('job_applications.id', 'job_applications.created_at', 'job_applications.full_name', 'jobs.title', 'clients.name as clients_name')
                ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
                ->join('clients', 'clients.id', 'jobs.client_id')
                ->where('job_applications.master_status_id', '=', 5)
                ->where('job_applications.submitted_by', '=', $this->user->id)
                ->take(4)
                ->orderBy('job_applications.id', 'DESC')
                ->count();

            $this->followupApp = JobApplication::select('job_applications.id', 'job_applications.created_at', 'job_applications.full_name', 'jobs.title', 'clients.name as clients_name')
                ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
                ->join('clients', 'clients.id', 'jobs.client_id')
                ->where('job_applications.master_status_id', '=', 5)
                ->where('job_applications.submitted_by', '=', $this->user->id)
                ->take(4)
                ->orderBy('job_applications.id', 'DESC')
                ->get();

            $this->offer = JobApplication::select('job_applications.id', 'job_applications.full_name', 'jobs.title', 'job_applications.current_designation', 'job_applications.ctc', 'job_applications.ectc', 'master_status.name',
                'clients.name as client_name',
                'job_applications.proposed_ctc', 'job_applications.any_offer', 'job_applications.document', 'job_applications.sub_for_approval', 'job_applications.offer_approved', 'job_applications.offer_release', 'job_applications.offer_acceptance', 'job_applications.joined', 'job_applications.joined_date', 'job_applications.job_id'

            )
                ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
                ->join('clients', 'clients.id', 'jobs.client_id')
                ->join('master_status', 'master_status.id', 'job_applications.master_status_id')
                ->where('job_applications.master_status_id', '=', 6)
                ->where('job_applications.submitted_by', '=', $this->user->id)
                                                         // ->whereBetween('job_applications.updated_at', [$todayDate, $nextSevenDate])
                ->take(4)
                ->orderBy('job_applications.joined_date', 'DESC')
                ->get();

        }

        $this->totalHired = $allApplications->filter(function ($value, $key) {
            return $value->slug == 'hired';
        })->count();

        $this->totalRejected = $allApplications->filter(function ($value, $key) {
            return $value->slug == 'rejected';
        })->count();

        $this->newApplications = $allApplications->filter(function ($value, $key) {
            return $value->slug == 'applied';
        })->count();

        $this->shortlisted = $allApplications->filter(function ($value, $key) {
            return $value->slug == 'phone screen' || $value->status == 'interview';
        })->count();

        $currentDate = Carbon::now(company()->timezone)->format('Y-m-d');

        // Range query on schedule_date uses the idx_comp_sched_status index (V2 Phase 2).
        $this->totalTodayInterview = InterviewSchedule::whereBetween('schedule_date', [
            Carbon::now(company()->timezone)->startOfDay(),
            Carbon::now(company()->timezone)->endOfDay(),
        ])
            ->count();
        $this->todoItemsView = $this->generateTodoView();

        return view('service_company.dashboard.home', $this->data);
    }

    public function meetingScheduleList(Request $request)
    {

        $company_id = auth()->user()->company_id;
        $this->totalMeetingSchedules = MeetingSchedule::where('to_company_id', $company_id)->count();
        $this->MeetingSchedule = MeetingSchedule::with('comments', 'toCompany', 'fromCompany', 'users_main')->orderByDesc('id')->where('meeting_schedules.to_company_id', $company_id)->paginate(50);

        return view('service_company.dashboard.meeting-list', $this->data);

    }

    public function meetingComment(Request $request, $id)
    {
        $html = '';
        $interview_feedback = DB::table('meeting_comments')
            ->select('meeting_comments.*', 'users.name', 'users.image')
            ->join('users', 'users.id', '=', 'meeting_comments.comment_by')
            ->where('meeting_comments.meeting_schedule_id', @$id)
            ->orderByDesc('meeting_comments.id')
            ->get();
        if (! empty($interview_feedback)) {
            foreach ($interview_feedback as $intfeed) {

                $newDate = date('d-m-Y', strtotime($intfeed->comment_date));
                $newTime = date('H:i A', strtotime($intfeed->comment_date));

                $imagePath = '';
                $imagePath = url('public/user-uploads/team_profile_img/'.@$intfeed->image);

                $feedback_star_imagePath = $feedback_star_text = '';

                $feedback_star_imagePath = url('public/img/star/5-blank.png');
                $feedback_star_text = 'Blank';

                $html .= '<div class="overflow-auto pb-5">
					  <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
					  <div class="mb-1"> 
					  <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
					  <img src="'.str_replace('index.php', '', @$feedback_star_imagePath).'" alt="'.@$feedback_star_text.'"  />&nbsp;Meeting Feedback:
					  </a>
					  <p> '.$intfeed->comments.' </p>
					  <div class="d-flex align-items-center mt-1 fs-6">
					  <div class="text-muted me-2 fs-7">Added at '.@$newTime.' | '.@$newDate.' by</div>
					  <div class="symbol symbol-circle symbol-25px" data-toggle="tooltip" data-boundary="window" data-placement="top" title="'.@$intfeed->name.'"> 
					  <img src="'.str_replace('index.php', '', @$imagePath).'" alt="'.@$intfeed->name.'"> 
					  </div>   
					  </div>
					  </div>
					  </div>
					  </div>';
            }
        }

        return response()->json(['html' => $html]);

    }

    public function meetingcommentnew(Request $request)
    {
        $MeetingComment = new MeetingComment;
        $MeetingComment->meeting_schedule_id = $request->meet_id;
        $MeetingComment->comment_by = auth()->user()->id;
        $MeetingComment->comments = $request->comments;

        $MeetingComment->save();

        return response()->json(['status' => true]);
    }
}
