<?php

namespace App\Http\Controllers\SuperAdmin;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\File;
use App\Models\Company;
use App\Models\Package;
use App\Models\CompanyPackage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\EmailSetting;
use App\Services\ElasticService;
use App\Models\PaymentSetting;

class SuperAdminDashboardController extends SuperAdminBaseController
{
    
    protected $elasticService;
    public function __construct(ElasticService $elasticService)
    {
        parent::__construct();
        $this->pageIcon = 'icon-speedometer';
        $this->pageTitle = 'menu.dashboard';
        $this->elasticService = $elasticService;
    }
    public function index()
    {
        $this->versionUpdate();

        // $mapping = $this->elasticService->getClient()->indices()->getMapping([
        //     'index' => ELASTIC_CANDIDATES_BENCH
        //      ]);
        //      dd($mapping);

        /** Start Register Companies Count  */

            $params = [
                'index' => ELASTIC_COMPANIES_INDEX,
                'body'  => [
                    'size' => 0, // no documents, only stats
                    'aggs' => [
                        'company_type_count' => [
                            'terms' => [
                                'field' => 'company_type', // must be keyword/int
                                'size'  => 3
                            ]
                        ],
                        'other_count' => [
                            'missing' => [
                                'field' => 'company_type'
                            ]
                        ]
                    ]
                ]
            ];
            $response = $this->elasticService->getClient()->search($params);
            $buckets = $response['aggregations']['company_type_count']['buckets'] ?? [];
            $map = array_column($buckets, 'doc_count', 'key');
            $company_type_count = (object)[
                'total'    => $response['hits']['total']['value'] ?? 0,
                'staffing' => $map[1] ?? 0,
                'service'  => $map[2] ?? 0,
                'product'  => $map[3] ?? 0,
                'other'    => $response['aggregations']['other_count']['doc_count'] ?? 0,
            ];
            $this->total_company_count = $company_type_count->total;
            $this->staffing_company_count = $company_type_count->staffing;
            $this->service_company_count = $company_type_count->service;
            $this->product_company_count = $company_type_count->product;
            $this->other_company_count = $company_type_count->other;

            $this->companies_labels = ["Staffing","Service","Product","Other"];
            $this->companiesValues = [$company_type_count->staffing,$company_type_count->service,$company_type_count->product,$company_type_count->other];
            $this->companies_total = $company_type_count->total;


        /** End Register Companies Count */   
        
        /** Start Active Companies Count */

            $params = [
                'index' => ELASTIC_COMPANIES_INDEX,
                'body'  => [
                    'size' => 0,
                    'aggs' => [
                        'activity' => [
                            'filters' => [
                                'filters' => [
                                    'active' => [
                                        'exists' => ['field' => 'last_login']
                                    ],
                                    'dac' => [
                                        'range' => [
                                            'last_login' => [
                                                'gte' => 'now-1d/d'
                                            ]
                                        ]
                                    ],
                                    'wac' => [
                                        'range' => [
                                            'last_login' => [
                                                'gte' => 'now-7d/d'
                                            ]
                                        ]
                                    ],
                                    'mac' => [
                                        'range' => [
                                            'last_login' => [
                                                'gte' => 'now-30d/d'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];
            $response = $this->elasticService->getClient()->search($params);
            $activity = $response['aggregations']['activity']['buckets'] ?? [];
            $result = (object)[
                'total'  => $response['hits']['total']['value'] ?? 0,
                'active' => $activity['active']['doc_count'] ?? 0,
                'dac'    => $activity['dac']['doc_count'] ?? 0,
                'wac'    => $activity['wac']['doc_count'] ?? 0,
                'mac'    => $activity['mac']['doc_count'] ?? 0,
            ];
            $activecompanylabels = ["Daily Active","Weekly Active","Monthly Active"];
            $activecompanyValues = [$result->dac,$result->wac,$result->mac];
            $this->activecompanylabels = $activecompanylabels ; 
            $this->activecompanyValues = $activecompanyValues;
            $this->activecompanies = $result->active;

        /** End Active Companies Count */   
        
        /** Start Bench Candidates Count */

            $params_bench = [
                'index' => ELASTIC_CANDIDATES_BENCH,
                'body'  => [
                    'size' => 0, // no documents, only stats
                    'aggs' => [
                        'status_count' => [
                            'terms' => [
                                'field' => 'status', // must be keyword/int
                                'size'  => 5
                            ]
                        ]
                    ]
                ]
            ];
            $responseBench = $this->elasticService->getClient()->search($params_bench);
            $bucketsBench = $responseBench['aggregations']['status_count']['buckets'] ?? [];
            $mapBench = array_column($bucketsBench, 'doc_count', 'key');
            $Bench_status_count = (object)[
                'total'    => $responseBench['hits']['total']['value'] ?? 0,
                'pool' => $mapBench[1] ?? 0,
                'bench'  => $mapBench[2] ?? 0,
                'placed'  => $mapBench[3] ?? 0,
                'hold'    => $mapBench[4] ?? 0,
                'exited'    => $mapBench[5] ?? 0,
            ];
            $this->bench_labels = ["Pool","Bench","Placed","Hold","Exited"];
            $this->benchcandidateValues = [$Bench_status_count->pool,$Bench_status_count->bench,$Bench_status_count->placed,$Bench_status_count->hold,$Bench_status_count->exited];
            $this->bench_total = $Bench_status_count->total;
            // dd($Bench_status_count);

        /** End Bench Candidate Count */  
        
        /** Start job posted*/

            $params = [
                'index' => ELASTIC_JOBS,
                'body'  => [
                    'size' => 0,
                    'aggs' => [
                        'job_posts' => [
                            'filters' => [
                                'filters' => [
                                    'daily' => [
                                        'range' => [
                                            'created_at' => [
                                                'gte' => 'now-1d/d',
                                                'time_zone' => '+05:30'
                                            ]
                                        ]
                                    ],
                                    'weekly' => [
                                        'range' => [
                                            'created_at' => [
                                                'gte' => 'now-7d/d',
                                                'time_zone' => '+05:30'
                                            ]
                                        ]
                                    ],
                                    'monthly' => [
                                        'range' => [
                                            'created_at' => [
                                                'gte' => 'now-30d/d',
                                                'time_zone' => '+05:30'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            $response = $this->elasticService->getClient()->search($params);
            $buckets = $response['aggregations']['job_posts']['buckets'] ?? [];

            $job_stats = (object)[
                'total'   => $response['hits']['total']['value'] ?? 0,
                'daily'   => $buckets['daily']['doc_count'] ?? 0,
                'weekly'  => $buckets['weekly']['doc_count'] ?? 0,
                'monthly' => $buckets['monthly']['doc_count'] ?? 0,
            ];
            $this->jobPostLabels = ["Daily","Weekly","Monthly"];
            $this->jobPostValues = [$job_stats->daily,$job_stats->weekly,$job_stats->monthly];
            $this->jobPostTotal = $job_stats->total;

        /** End job posted*/   
        
        /** Start job status */
            $params = [
                'index' => ELASTIC_JOBS,
                'body'  => [
                    'size' => 0,
                    'aggs' => [
                        'job_status' => [
                            'filters' => [
                                'filters' => [
                                    'active' => [
                                        'term' => ['status' => 'active']
                                    ],
                                    'hold' => [
                                        'term' => ['status' => 'hold']
                                    ],
                                    'inactive' => [
                                        'term' => ['status' => 'inactive']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            $response = $this->elasticService->getClient()->search($params);
            $buckets = $response['aggregations']['job_status']['buckets'] ?? [];

            $job_status_count = (object)[
                'total'    => $response['hits']['total']['value'] ?? 0,
                'active'   => $buckets['active']['doc_count'] ?? 0,
                'hold'     => $buckets['hold']['doc_count'] ?? 0,
                'inactive' => $buckets['inactive']['doc_count'] ?? 0,
            ];
            $this->jobsStatusLabels = ["Hiring","Hold","Closed"];
            $this->jobsStatusValues = [$job_status_count->active,$job_status_count->hold,$job_status_count->inactive];
            $this->jobsStatusTotal = $job_status_count->total;
            
        /** End job status*/ 
        
        /** Start check subscription by company oprational */

        $params = [
            'index' => ELASTIC_COMPANIES_INDEX,
            'body'  => [
                'size' => 0,
                'aggs' => [
                    'coins_nested' => [
                        'nested' => [
                            'path' => 'company_coins'
                        ],
                        'aggs' => [

                            // ✅ Filter only operational records
                            'operational_only' => [
                                'filter' => [
                                    'term' => [
                                        'company_coins.event_type' => 'operational'
                                    ]
                                ],
                                'aggs' => [
                                    'plan_types' => [
                                        'filters' => [
                                            'filters' => [

                                                // ✅ FREE
                                                'free' => [
                                                    'bool' => [
                                                        'should' => [
                                                            [
                                                                'match' => [
                                                                    'company_coins.package_name' => 'Free'
                                                                ]
                                                            ],
                                                            [
                                                                'bool' => [
                                                                    'must_not' => [
                                                                        [
                                                                            'exists' => [
                                                                                'field' => 'company_coins.package_name'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ]
                                                        ],
                                                        'minimum_should_match' => 1
                                                    ]
                                                ],

                                                // ✅ PAID
                                                'paid' => [
                                                    'bool' => [
                                                        'must' => [
                                                            [
                                                                'exists' => [
                                                                    'field' => 'company_coins.package_name'
                                                                ]
                                                            ]
                                                        ],
                                                        'must_not' => [
                                                            [
                                                                'match' => [
                                                                    'company_coins.package_name' => 'Free'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ],

                                            ]
                                        ],
                                        'aggs' => [
                                            // ✅ Unique companies
                                            'to_company' => [
                                                'reverse_nested' => new \stdClass()
                                            ]
                                        ]
                                    ]
                                ]
                            ]

                        ]
                    ]
                ]
            ]
        ];

        $response = $this->elasticService->getClient()->search($params);

        $buckets = $response['aggregations']['coins_nested']['operational_only']['plan_types']['buckets'] ?? [];

        $company_plan_stats = (object)[
            'free' => $buckets['free']['to_company']['doc_count'] ?? 0,
            'paid' => $buckets['paid']['to_company']['doc_count'] ?? 0,
        ];
        $this->oprationalPlanLabels = ["Free","Paid"];
        $this->oprationalPlanValues = [$company_plan_stats->free,$company_plan_stats->paid];
        $this->oprationalPlanTotal = $company_plan_stats->free+$company_plan_stats->paid;

        /** End check subscription by company oprational */ 

        /** Start check subscription by company sales*/

        $params = [
            'index' => ELASTIC_COMPANIES_INDEX,
            'body'  => [
                'size' => 0,
                'aggs' => [
                    'coins_nested' => [
                        'nested' => [
                            'path' => 'company_coins'
                        ],
                        'aggs' => [

                            // ✅ Filter only sales records
                            'operational_only' => [
                                'filter' => [
                                    'term' => [
                                        'company_coins.event_type' => 'sales'
                                    ]
                                ],
                                'aggs' => [
                                    'plan_types' => [
                                        'filters' => [
                                            'filters' => [

                                                // ✅ FREE
                                                'free' => [
                                                    'bool' => [
                                                        'should' => [
                                                            [
                                                                'match' => [
                                                                    'company_coins.package_name' => 'Free'
                                                                ]
                                                            ],
                                                            [
                                                                'bool' => [
                                                                    'must_not' => [
                                                                        [
                                                                            'exists' => [
                                                                                'field' => 'company_coins.package_name'
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ]
                                                        ],
                                                        'minimum_should_match' => 1
                                                    ]
                                                ],

                                                // ✅ PAID
                                                'paid' => [
                                                    'bool' => [
                                                        'must' => [
                                                            [
                                                                'exists' => [
                                                                    'field' => 'company_coins.package_name'
                                                                ]
                                                            ]
                                                        ],
                                                        'must_not' => [
                                                            [
                                                                'match' => [
                                                                    'company_coins.package_name' => 'Free'
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ],

                                            ]
                                        ],
                                        'aggs' => [
                                            // ✅ Unique companies
                                            'to_company' => [
                                                'reverse_nested' => new \stdClass()
                                            ]
                                        ]
                                    ]
                                ]
                            ]

                        ]
                    ]
                ]
            ]
        ];

        $response = $this->elasticService->getClient()->search($params);

        $buckets = $response['aggregations']['coins_nested']['operational_only']['plan_types']['buckets'] ?? [];

        $company_plan_stats = (object)[
            'free' => $buckets['free']['to_company']['doc_count'] ?? 0,
            'paid' => $buckets['paid']['to_company']['doc_count'] ?? 0,
        ];
        $this->salesPlanLabels = ["Free","Paid"];
        $this->salesPlanValues = [$company_plan_stats->free,$company_plan_stats->paid];
        $this->salesPlanTotal = $company_plan_stats->free+$company_plan_stats->paid;

        /** End check subscription by company sales */ 
        
        /** Start Active Recruiters*/
    
		$params = [
            'index' => ELASTIC_USERS_LIST,
            'body'  => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'term' => [
                                    'root_user' => 0
                                ]
                            ],
                            [
                                'term' => [
                                    'status' => 'active' // ✅ only active users
                                ]
                            ]
                        ]
                    ]
                ],
                'aggs' => [
                    'activity' => [
                        'filters' => [
                            'filters' => [

                                // ✅ DAR
                                'dar' => [
                                    'range' => [
                                        'last_login' => [
                                            'gte' => 'now-1d/d',
                                            'time_zone' => '+05:30'
                                        ]
                                    ]
                                ],

                                // ✅ WAR
                                'war' => [
                                    'range' => [
                                        'last_login' => [
                                            'gte' => 'now-7d/d',
                                            'time_zone' => '+05:30'
                                        ]
                                    ]
                                ],

                                // ✅ MAR
                                'mar' => [
                                    'range' => [
                                        'last_login' => [
                                            'gte' => 'now-30d/d',
                                            'time_zone' => '+05:30'
                                        ]
                                    ]
                                ],

                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->elasticService->getClient()->search($params);

        $buckets = $response['aggregations']['activity']['buckets'] ?? [];

        $recruiter_stats = (object)[
            'total_active_recruiters' => $response['hits']['total']['value'] ?? 0,
            'dar' => $buckets['dar']['doc_count'] ?? 0,
            'war' => $buckets['war']['doc_count'] ?? 0,
            'mar' => $buckets['mar']['doc_count'] ?? 0,
        ];

        $this->recruiterStatsLabels = ["Daily Active","Weekly Active","Monthly Active"];
        $this->recruiterStatsValues = [$recruiter_stats->dar,$recruiter_stats->war,$recruiter_stats->mar];
        $this->recruiterStatsTotal = $recruiter_stats->total_active_recruiters;

        /** End  Active Recruiters*/    
        
        /** Start Upload Candidates (Last 30 days)*/
        
        $params = [
            'index' => ELASTIC_CANDIDATES,
            'body'  => [
                'size' => 0,
                'query' => [
                    'range' => [
                        'created_at' => [
                            'gte' => 'now-30d/d',
                            'lte' => 'now/d',
                            'time_zone' => '+05:30'
                        ]
                    ]
                ],
                'aggs' => [
                    'daily_candidates' => [
                        'date_histogram' => [
                            'field' => 'created_at',
                            'calendar_interval' => 'day',
                            'format' => 'yyyy-MM-dd',
                            'min_doc_count' => 0,
                            'extended_bounds' => [
                                'min' => 'now-30d/d',
                                'max' => 'now/d'
                            ],
                            'time_zone' => '+05:30'
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->elasticService->getClient()->search($params);

        $buckets = $response['aggregations']['daily_candidates']['buckets'] ?? [];

        // ✅ Final formatted report
        $daily_report = array_map(function ($item) {
            return [
                'date'  => Carbon::parse($item['key_as_string'])->format('d-M-Y'),
                'count' => $item['doc_count']
            ];
        }, $buckets);

        $total = $response['hits']['total']['value'] ?? 0;
        // ✅ Optional: chart-ready format
        $chart = (object)[
            'labels' => array_column($daily_report, 'date'),
            'data'   => array_column($daily_report, 'count'),
        ];
        $this->candidateUploadLabels = $chart->labels;
        $this->candidateUploadValues = $chart->data;
        $this->candidateUploadTotal = $total;

        /** End Upload Candidates (Last 30 days)*/ 
        
        /** Start all Upload Candidates */

            $params_all_candidates = [
                'index' => ELASTIC_CANDIDATES,
                'body'  => [
                    'size' => 0, // no documents, only stats
                    'aggs' => [
                        'status_count' => [
                            'terms' => [
                                'field' => 'job_master_status_id', // must be keyword/int
                                'size'  => 10
                            ]
                        ]
                    ]
                ]
            ];
            $responseAllCandidates = $this->elasticService->getClient()->search($params_all_candidates);
            $bucketsAllCandidates = $responseAllCandidates['aggregations']['status_count']['buckets'] ?? [];
            $mapAllCandidates = array_column($bucketsAllCandidates, 'doc_count', 'key');
            $totalFromBuckets = array_sum(array_column($bucketsAllCandidates, 'doc_count'));
            $allCandidates_status_count = (object)[
                'total'    =>$totalFromBuckets,
                'inReview' => $mapAllCandidates[8] ?? 0,
                'sourcing'  => $mapAllCandidates[1] ?? 0,
                'screening'  => $mapAllCandidates[2] ?? 0,
                'evaluation'  => $mapAllCandidates[3] ?? 0,
                'negotiation'  => $mapAllCandidates[4] ?? 0,
                'offered'  => $mapAllCandidates[5] ?? 0,
                'joined'  => $mapAllCandidates[6] ?? 0,
                'rejected' => $mapAllCandidates[7] ?? 0,
            ];
            // dd($allCandidates_status_count);
            $this->allcandidateslabels = ["InReview","Sourcing","Screening","Evaluation","Negotiation","Offered","Joined","Rejected"];
            $this->allcandidatesValues = [$allCandidates_status_count->inReview,$allCandidates_status_count->sourcing,$allCandidates_status_count->screening,$allCandidates_status_count->evaluation,$allCandidates_status_count->negotiation,$allCandidates_status_count->offered,$allCandidates_status_count->joined,$allCandidates_status_count->rejected];
            $this->allcandidates_total = $allCandidates_status_count->total;
            // dd($this->allcandidateslabels,$this->allcandidatesValues,$this->allcandidates_total);


        /** End all Upload Candidates */ 
        
        /** Start Interview record count */

         $params_all_candidates = [
                'index' => ELASTIC_INERTVIEW_TRACKER,
                'body'  => [
                    'size' => 0, // no documents, only stats
                    'aggs' => [
                        'status_count' => [
                            'terms' => [
                                'field' => 'available_status', // must be keyword/int
                                'size'  => 10
                            ]
                        ]
                    ]
                ]
            ];
            $responseinterviewData = $this->elasticService->getClient()->search($params_all_candidates);
            $bucketsinterviewData = $responseinterviewData['aggregations']['status_count']['buckets'] ?? [];
            $mapinterviewData = array_column($bucketsinterviewData, 'doc_count', 'key');
            $totalFromBuckets = array_sum(array_column($bucketsinterviewData, 'doc_count'));

            $interview_status_count = (object)[
                'total'    =>$totalFromBuckets,
                'available' => $mapinterviewData[1] ?? 0,
                'notAvailable'  => $mapinterviewData[2] ?? 0,
                'rescheduled'  => $mapinterviewData[3] ?? 0,
                'cancelInterview'  => $mapinterviewData[4] ?? 0,
                'otherReason'  => $mapinterviewData[5] ?? 0,
            ];
            // dd($allCandidates_status_count);
            $this->interviewDatalabels = ["Available","NotAvailable","Rescheduled","Cancel Interview","Other Reason"];
            $this->interviewDataValues = [$interview_status_count->available,$interview_status_count->notAvailable,$interview_status_count->rescheduled,$interview_status_count->cancelInterview,$interview_status_count->otherReason];
            $this->interviewData_total = $interview_status_count->total;



        /** End Interview record count */ 
        
        /** Start whatsapp and email count */

         $params_all_email = [
                'index' => ELASTIC_EMAIL_COUNT,
                'body'  => [
                    'size' => 0, // no documents, only stats
                    'aggs' => [
                        'status_count' => [
                            'terms' => [
                                'field' => 'emailType', // must be keyword/int
                                'size'  => 10
                            ]
                        ]
                    ]
                ]
            ];
            $responseemailData = $this->elasticService->getClient()->search($params_all_email);
            $bucketsemailData = $responseemailData['aggregations']['status_count']['buckets'] ?? [];
            $mapemailData = array_column($bucketsemailData, 'doc_count', 'key');
            $totalFromBuckets = array_sum(array_column($bucketsemailData, 'doc_count'));

            $emailSummary = (object)[
                'total'    =>$totalFromBuckets,
                'mass_mail' => $mapemailData[1] ?? 0,
                'candidate_mail'  => $mapemailData[2] ?? 0,
                'interview_reminder'  => $mapemailData[3] ?? 0,
                'notification'  => $mapemailData[4] ?? 0,
                'accounts_offer'  => $mapemailData[5] ?? 0,
            ];

            $this->emailSummaryDatalabels = ["Mass Mail","Candidate mail & CV Fwd","Interview Remainder","Notification","Accounts & Offer"];
            $this->emailSummaryDataValues = [$emailSummary->mass_mail,$emailSummary->candidate_mail,$emailSummary->interview_reminder,$emailSummary->notification,$emailSummary->accounts_offer];
            $this->emailSummaryData_total = $emailSummary->total;
            

            $params_all_whatsapp = [
                    'index' => ELASTIC_WHATSAPP_COUNT,
                    'body'  => [
                        'size' => 0, // no documents, only stats
                        'aggs' => [
                            'status_count' => [
                                'terms' => [
                                    'field' => 'messageType', // must be keyword/int
                                    'size'  => 10
                                ]
                            ]
                        ]
                    ]
                ];
                $responsewhatsappData = $this->elasticService->getClient()->search($params_all_whatsapp);
                $bucketswhatsappData = $responsewhatsappData['aggregations']['status_count']['buckets'] ?? [];
                $mapwhatsappData = array_column($bucketswhatsappData, 'doc_count', 'key');
                $totalFromBuckets = array_sum(array_column($bucketswhatsappData, 'doc_count'));

                $whatsappSummary = (object)[
                    'total'    =>$totalFromBuckets,
                    'custom_message' => $mapwhatsappData[1] ?? 0,
                    'reminder_message'  => $mapwhatsappData[2] ?? 0,
                ];

            $this->whatsappSummaryDatalabels = ["Custom message","Reminder message"];
            $this->whatsappSummaryDataValues = [$whatsappSummary->custom_message,$whatsappSummary->reminder_message];
            $this->whatsappSummary_total = $whatsappSummary->total;


        /** End whatsapp and email count */     

        return view('super-admin.dashboard.index', $this->data);
    }
    private function versionUpdate()
    {
        try {
            $client = new Client();
            $res = $client->request('GET', config('froiden_envato.updater_file_path'), ['verify' => false]);
            $lastVersion = $res->getBody();
            $lastVersion = json_decode($lastVersion, true);

            if ($lastVersion['version'] > File::get('version.txt')) {
                $this->lastVersion = $lastVersion['version'];
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

    }

}
