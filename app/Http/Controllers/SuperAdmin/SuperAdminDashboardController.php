<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\GeneralMethod;
use App\Models\GlobalSetting;
use App\Models\LanguageSetting;
use App\Models\SmsSetting;
use App\Models\StickyNote;
use App\Models\ThemeSetting;
use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * SuperAdminDashboardController
 *
 * CQRS-compliant, high-concurrency Super Admin Analytics Dashboard Controller.
 * Adheres strictly to Non-Negotiable Rules:
 * - 0% MySQL reads on page/API request paths (cached in Redis / Octane memory).
 * - 100% read analytics served via Elasticsearch Multi-Search (`_msearch`).
 * - Circuit breakers and fallback defaults on all external/search dependencies.
 * - Client-first memory execution target (10K concurrency SLA).
 */
class SuperAdminDashboardController extends SuperAdminBaseController
{
    protected $elasticService;

    protected $superAdminCompanyIds;

    public function __construct(ElasticService $elasticService)
    {
        // 0% MySQL Reads: Cache application settings with 24h TTL
        $this->global = Cache::remember('superadmin_global_setting', 86400, fn () => GlobalSetting::first());
        $this->smsSettings = Cache::remember('superadmin_sms_setting', 86400, fn () => SmsSetting::first());
        $this->adminTheme = Cache::remember('superadmin_theme_setting', 86400, fn () => ThemeSetting::whereNull('company_id')->first());
        $this->superAdminCompanyIds = Cache::remember('superadmin_superadmin_company_ids', 86400, fn () => GeneralMethod::superAdminCompanyIds());

        config(['app.name' => $this->global?->company_name ?? config('app.name')]);
        config(['app.url' => url('/')]);
        view()->share('smsSettings', $this->smsSettings);

        $this->recruitPlugins = recruit_plugins();
        $this->pageIcon = 'icon-speedometer';
        $this->pageTitle = 'menu.dashboard';
        $this->elasticService = $elasticService;

        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            if ($this->user && $this->user->is_superadmin) {
                config(['froiden_envato.allow_users_id' => true]);
            }
            $this->companyName = $this->global?->company_name;

            // Cache active languages
            $this->languageSettings = Cache::remember('superadmin_language_settings', 86400, fn () => LanguageSetting::where('status', 'enabled')->orderBy('language_name')->get());

            if ($this->global) {
                App::setLocale($this->global->locale);
                Carbon::setLocale($this->global->locale);
                setlocale(LC_TIME, $this->global->locale.'_'.strtoupper($this->global->locale));
            }

            if ($this->user) {
                $this->stickyNotes = Cache::remember('superadmin_sticky_notes_'.$this->user->id, 300, fn () => StickyNote::where('user_id', $this->user->id)->orderBy('updated_at', 'desc')->get());
            }

            view()->share('user', $this->user);
            view()->share('languages', $this->languageSettings);
            view()->share('global', $this->global);
            view()->share('adminTheme', $this->adminTheme);

            return $next($request);
        });
    }

    /**
     * Dashboard Shell & Authoritative Snapshot Index
     * Executes single multi-search (msearch) HTTP round-trip across all 8 indices.
     */
    public function index()
    {
        $correlationId = (string) \Illuminate\Support\Str::uuid();
        $startTime = microtime(true);
        $this->user = $this->user ?? auth()->user();
        $this->data['user'] = $this->user;

        // Default fallback states in case of empty indices or transient ES errors
        $this->initializeDefaultMetrics();

        try {
            $msearchPayload = $this->buildMsearchPayload();
            $response = $this->elasticService->msearch($msearchPayload);
            $responses = $response['responses'] ?? [];

            $this->parseMsearchResponses($responses);

            $durationMs = (microtime(true) - $startTime) * 1000;
            if ($durationMs > 250) {
                Log::warning('[SuperAdminDashboardController] High multi-search latency', [
                    'correlation_id' => $correlationId,
                    'duration_ms' => round($durationMs, 2),
                ]);
            }
        } catch (\Throwable $e) {
            $durationMs = (microtime(true) - $startTime) * 1000;
            Log::error('[SuperAdminDashboardController] Circuit breaker active: Multi-search failed', [
                'correlation_id' => $correlationId,
                'duration_ms' => round($durationMs, 2),
                'error' => $e->getMessage(),
            ]);
            // Fallback metrics already populated by initializeDefaultMetrics()
        }

        return view('super-admin.dashboard.index', $this->data);
    }

    /**
     * Per-Chart Period Filters JSON API
     * Phase 4: Serves lightweight delta requests for [Week], [Month], and [Custom] filters.
     * Phase 5: Guarded by circuit breaker with correlation ID and bounded error payloads.
     */
    public function chartApi(Request $request, string $chartKey): JsonResponse
    {
        $correlationId = $request->header('X-Correlation-ID', (string) \Illuminate\Support\Str::uuid());
        $startTime = microtime(true);
        $period = $request->input('period', 'month');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        try {
            $result = $this->fetchChartAggregate($chartKey, $period, $startDate, $endDate);
            $durationMs = (microtime(true) - $startTime) * 1000;

            return response()->json([
                'success' => true,
                'correlation_id' => $correlationId,
                'duration_ms' => round($durationMs, 2),
                'chart' => $chartKey,
                'period' => $period,
                'labels' => $result['labels'] ?? [],
                'data' => $result['data'] ?? [],
                'total' => $result['total'] ?? 0,
            ]);
        } catch (\Throwable $e) {
            $durationMs = (microtime(true) - $startTime) * 1000;
            Log::error("[SuperAdminDashboardController] Circuit breaker active: chartApi failed for {$chartKey}", [
                'correlation_id' => $correlationId,
                'chart_key' => $chartKey,
                'period' => $period,
                'duration_ms' => round($durationMs, 2),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'correlation_id' => $correlationId,
                'message' => 'Unable to load chart data. Circuit breaker activated.',
                'chart' => $chartKey,
                'period' => $period,
                'labels' => [],
                'data' => [],
                'total' => 0,
            ], 500);
        }
    }

    /**
     * Resolve Super Admin Company IDs exclusion clauses for Elasticsearch.
     * Ensures strict parity with Subscription Management (Company ID 1 / Super Admin excluded).
     *
     * @return array{0: array, 1: array} [$companyMustNot, $tenantMustNot]
     */
    private function getSuperAdminExclusions(): array
    {
        $superAdminCompanyIds = $this->superAdminCompanyIds ?? Cache::remember('superadmin_superadmin_company_ids', 86400, fn () => GeneralMethod::superAdminCompanyIds());
        if (empty($superAdminCompanyIds)) {
            $superAdminCompanyIds = [1];
        }

        return [
            [['terms' => ['id' => array_values($superAdminCompanyIds)]]],
            [['terms' => ['company_id' => array_values($superAdminCompanyIds)]]],
        ];
    }

    /**
     * Build Consolidated Multi-Search Payload (8 Indices, 1 HTTP round-trip)
     */
    private function buildMsearchPayload(): array
    {
        [$companyMustNot, $tenantMustNot] = $this->getSuperAdminExclusions();

        // 1. Companies Index (Types, Activity, Operational & Sales Coins)
        $companiesBody = [
            'size' => 0,
            'query' => [
                'bool' => [
                    'must_not' => $companyMustNot,
                ],
            ],
            'aggs' => [
                'company_type_count' => [
                    'terms' => ['field' => 'company_type', 'size' => 3],
                ],
                'other_count' => [
                    'missing' => ['field' => 'company_type'],
                ],
                'activity' => [
                    'filters' => [
                        'filters' => [
                            'active' => ['exists' => ['field' => 'last_login']],
                            'dac' => ['range' => ['last_login' => ['gte' => 'now-1d/d']]],
                            'wac' => ['range' => ['last_login' => ['gte' => 'now-7d/d']]],
                            'mac' => ['range' => ['last_login' => ['gte' => 'now-30d/d']]],
                        ],
                    ],
                ],
                'coins_nested' => [
                    'nested' => ['path' => 'company_coins'],
                    'aggs' => [
                        'operational_only' => [
                            'filter' => ['term' => ['company_coins.event_type' => 'operational']],
                            'aggs' => [
                                'plan_types' => [
                                    'filters' => [
                                        'filters' => [
                                            'free' => [
                                                'bool' => [
                                                    'should' => [
                                                        ['match' => ['company_coins.package_name' => 'Free']],
                                                        ['bool' => ['must_not' => [['exists' => ['field' => 'company_coins.package_name']]]]],
                                                    ],
                                                    'minimum_should_match' => 1,
                                                ],
                                            ],
                                            'paid' => [
                                                'bool' => [
                                                    'must' => [['exists' => ['field' => 'company_coins.package_name']]],
                                                    'must_not' => [['match' => ['company_coins.package_name' => 'Free']]],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'aggs' => [
                                        'to_company' => ['reverse_nested' => new \stdClass],
                                    ],
                                ],
                            ],
                        ],
                        'sales_only' => [
                            'filter' => ['term' => ['company_coins.event_type' => 'sales']],
                            'aggs' => [
                                'plan_types' => [
                                    'filters' => [
                                        'filters' => [
                                            'free' => [
                                                'bool' => [
                                                    'should' => [
                                                        ['match' => ['company_coins.package_name' => 'Free']],
                                                        ['bool' => ['must_not' => [['exists' => ['field' => 'company_coins.package_name']]]]],
                                                    ],
                                                    'minimum_should_match' => 1,
                                                ],
                                            ],
                                            'paid' => [
                                                'bool' => [
                                                    'must' => [['exists' => ['field' => 'company_coins.package_name']]],
                                                    'must_not' => [['match' => ['company_coins.package_name' => 'Free']]],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'aggs' => [
                                        'to_company' => ['reverse_nested' => new \stdClass],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // 2. Bench Candidates
        $benchBody = [
            'size' => 0,
            'query' => [
                'bool' => [
                    'must_not' => $tenantMustNot,
                ],
            ],
            'aggs' => [
                'status_count' => [
                    'terms' => ['field' => 'status', 'size' => 5],
                ],
            ],
        ];

        // 3. Jobs Index (Posts Daily/Weekly/Monthly, Trend 7d, Status Active/Hold/Inactive)
        $jobsBody = [
            'size' => 0,
            'query' => [
                'bool' => [
                    'must_not' => $tenantMustNot,
                ],
            ],
            'aggs' => [
                'job_posts' => [
                    'filters' => [
                        'filters' => [
                            'daily' => ['range' => ['created_at' => ['gte' => 'now-1d/d', 'time_zone' => '+05:30']]],
                            'weekly' => ['range' => ['created_at' => ['gte' => 'now-7d/d', 'time_zone' => '+05:30']]],
                            'monthly' => ['range' => ['created_at' => ['gte' => 'now-30d/d', 'time_zone' => '+05:30']]],
                        ],
                    ],
                ],
                'trend_7d' => [
                    'filter' => [
                        'range' => [
                            'created_at' => [
                                'gte' => 'now-6d/d',
                                'lte' => 'now/d',
                                'time_zone' => '+05:30',
                            ],
                        ],
                    ],
                    'aggs' => [
                        'by_day' => [
                            'date_histogram' => [
                                'field' => 'created_at',
                                'calendar_interval' => 'day',
                                'format' => 'yyyy-MM-dd',
                                'min_doc_count' => 0,
                                'extended_bounds' => [
                                    'min' => 'now-6d/d',
                                    'max' => 'now/d',
                                ],
                                'time_zone' => '+05:30',
                            ],
                        ],
                    ],
                ],
                'trend_30d' => [
                    'filter' => [
                        'range' => [
                            'created_at' => [
                                'gte' => 'now-29d/d',
                                'lte' => 'now/d',
                                'time_zone' => '+05:30',
                            ],
                        ],
                    ],
                    'aggs' => [
                        'by_day' => [
                            'date_histogram' => [
                                'field' => 'created_at',
                                'calendar_interval' => 'day',
                                'format' => 'yyyy-MM-dd',
                                'min_doc_count' => 0,
                                'extended_bounds' => [
                                    'min' => 'now-29d/d',
                                    'max' => 'now/d',
                                ],
                                'time_zone' => '+05:30',
                            ],
                        ],
                    ],
                ],
                'job_status' => [
                    'filters' => [
                        'filters' => [
                            'active' => ['term' => ['status' => 'active']],
                            'hold' => ['term' => ['status' => 'hold']],
                            'inactive' => ['term' => ['status' => 'inactive']],
                        ],
                    ],
                ],
            ],
        ];

        // 4. Active Recruiters
        $recruitersBody = [
            'size' => 0,
            'query' => [
                'bool' => [
                    'must' => [
                        ['term' => ['root_user' => 0]],
                        ['term' => ['status' => 'active']],
                    ],
                    'must_not' => $tenantMustNot,
                ],
            ],
            'aggs' => [
                'activity' => [
                    'filters' => [
                        'filters' => [
                            'dar' => ['range' => ['last_login' => ['gte' => 'now-1d/d', 'time_zone' => '+05:30']]],
                            'war' => ['range' => ['last_login' => ['gte' => 'now-7d/d', 'time_zone' => '+05:30']]],
                            'mar' => ['range' => ['last_login' => ['gte' => 'now-30d/d', 'time_zone' => '+05:30']]],
                        ],
                    ],
                ],
            ],
        ];

        // 5. Candidates Index (Uploads Last 30d + Status Count)
        $candidatesBody = [
            'size' => 0,
            'query' => [
                'bool' => [
                    'must_not' => $tenantMustNot,
                ],
            ],
            'aggs' => [
                'last_30d' => [
                    'filter' => [
                        'range' => [
                            'created_at' => [
                                'gte' => 'now-30d/d',
                                'lte' => 'now/d',
                                'time_zone' => '+05:30',
                            ],
                        ],
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
                                    'max' => 'now/d',
                                ],
                                'time_zone' => '+05:30',
                            ],
                        ],
                    ],
                ],
                'status_count' => [
                    'terms' => [
                        'field' => 'job_master_status_id',
                        'size' => 10,
                    ],
                ],
            ],
        ];

        // 6. Interview Tracker
        $interviewBody = [
            'size' => 0,
            'query' => [
                'bool' => [
                    'must_not' => $tenantMustNot,
                ],
            ],
            'aggs' => [
                'status_count' => [
                    'terms' => [
                        'field' => 'available_status',
                        'size' => 10,
                    ],
                ],
            ],
        ];

        // 7. Email Count
        $emailBody = [
            'size' => 0,
            'query' => [
                'bool' => [
                    'must_not' => $tenantMustNot,
                ],
            ],
            'aggs' => [
                'status_count' => [
                    'terms' => [
                        'field' => 'emailType',
                        'size' => 10,
                    ],
                ],
            ],
        ];

        // 8. WhatsApp Count
        $whatsappBody = [
            'size' => 0,
            'query' => [
                'bool' => [
                    'must_not' => $tenantMustNot,
                ],
            ],
            'aggs' => [
                'status_count' => [
                    'terms' => [
                        'field' => 'messageType',
                        'size' => 10,
                    ],
                ],
            ],
        ];

        return [
            ['index' => ELASTIC_COMPANIES_INDEX], $companiesBody,
            ['index' => ELASTIC_CANDIDATES_BENCH], $benchBody,
            ['index' => ELASTIC_JOBS], $jobsBody,
            ['index' => ELASTIC_USERS_LIST], $recruitersBody,
            ['index' => ELASTIC_CANDIDATES], $candidatesBody,
            ['index' => ELASTIC_INERTVIEW_TRACKER], $interviewBody,
            ['index' => ELASTIC_EMAIL_COUNT], $emailBody,
            ['index' => ELASTIC_WHATSAPP_COUNT], $whatsappBody,
        ];
    }

    /**
     * Parse Multi-Search Aggregations into Controller View Data
     */
    private function parseMsearchResponses(array $responses): void
    {
        // 0. Companies (Types, Activity, Coins)
        if (isset($responses[0]) && ! isset($responses[0]['error'])) {
            $respComp = $responses[0];
            $typeBuckets = $respComp['aggregations']['company_type_count']['buckets'] ?? [];
            $typeMap = array_column($typeBuckets, 'doc_count', 'key');
            $otherCount = $respComp['aggregations']['other_count']['doc_count'] ?? 0;
            $totalComp = $respComp['hits']['total']['value'] ?? 0;

            $this->total_company_count = $totalComp;
            $this->staffing_company_count = $typeMap[1] ?? 0;
            $this->service_company_count = $typeMap[2] ?? 0;
            $this->product_company_count = $typeMap[3] ?? 0;
            $this->other_company_count = $otherCount;

            $this->companies_labels = ['Staffing', 'Service', 'Product', 'Other'];
            $this->companiesValues = [$this->staffing_company_count, $this->service_company_count, $this->product_company_count, $this->other_company_count];
            $this->companies_total = $totalComp;

            // Activity
            $actBuckets = $respComp['aggregations']['activity']['buckets'] ?? [];
            $this->activecompanylabels = ['Daily Active', 'Weekly Active', 'Monthly Active'];
            $this->activecompanyValues = [
                $actBuckets['dac']['doc_count'] ?? 0,
                $actBuckets['wac']['doc_count'] ?? 0,
                $actBuckets['mac']['doc_count'] ?? 0,
            ];
            $this->activecompanies = $actBuckets['active']['doc_count'] ?? 0;

            // Operational Coins
            $opBuckets = $respComp['aggregations']['coins_nested']['operational_only']['plan_types']['buckets'] ?? [];
            $opFree = $opBuckets['free']['to_company']['doc_count'] ?? 0;
            $opPaid = $opBuckets['paid']['to_company']['doc_count'] ?? 0;
            $this->oprationalPlanLabels = ['Free', 'Paid'];
            $this->oprationalPlanValues = [$opFree, $opPaid];
            $this->oprationalPlanTotal = $opFree + $opPaid;

            // Sales Coins
            $salesBuckets = $respComp['aggregations']['coins_nested']['sales_only']['plan_types']['buckets'] ?? [];
            $salesFree = $salesBuckets['free']['to_company']['doc_count'] ?? 0;
            $salesPaid = $salesBuckets['paid']['to_company']['doc_count'] ?? 0;
            $this->salesPlanLabels = ['Free', 'Paid'];
            $this->salesPlanValues = [$salesFree, $salesPaid];
            $this->salesPlanTotal = $salesFree + $salesPaid;
        }

        // 1. Bench Candidates
        if (isset($responses[1]) && ! isset($responses[1]['error'])) {
            $respBench = $responses[1];
            $benchBuckets = $respBench['aggregations']['status_count']['buckets'] ?? [];
            $benchMap = array_column($benchBuckets, 'doc_count', 'key');
            $this->bench_labels = ['Pool', 'Bench', 'Placed', 'Hold', 'Exited'];
            $this->benchcandidateValues = [
                $benchMap[1] ?? 0,
                $benchMap[2] ?? 0,
                $benchMap[3] ?? 0,
                $benchMap[4] ?? 0,
                $benchMap[5] ?? 0,
            ];
            $this->bench_total = $respBench['hits']['total']['value'] ?? 0;
        }

        // 2. Jobs Index (Posts & Status)
        if (isset($responses[2]) && ! isset($responses[2]['error'])) {
            $respJobs = $responses[2];
            $jobBuckets = $respJobs['aggregations']['job_posts']['buckets'] ?? [];
            $this->jobPostLabels = ['Daily', 'Weekly', 'Monthly'];
            $this->jobPostValues = [
                $jobBuckets['daily']['doc_count'] ?? 0,
                $jobBuckets['weekly']['doc_count'] ?? 0,
                $jobBuckets['monthly']['doc_count'] ?? 0,
            ];
            $this->jobPostTotal = $respJobs['hits']['total']['value'] ?? 0;

            $trend7Buckets = $respJobs['aggregations']['trend_7d']['by_day']['buckets'] ?? [];
            $this->jobPostsTrend7dLabels = ! empty($trend7Buckets) ? array_map(function ($item) {
                return Carbon::parse($item['key_as_string'])->format('d M');
            }, $trend7Buckets) : [];
            $this->jobPostsTrend7dValues = ! empty($trend7Buckets) ? array_column($trend7Buckets, 'doc_count') : [];

            $trend30Buckets = $respJobs['aggregations']['trend_30d']['by_day']['buckets'] ?? [];
            $this->jobPostsTrend30dLabels = ! empty($trend30Buckets) ? array_map(function ($item) {
                return Carbon::parse($item['key_as_string'])->format('d M');
            }, $trend30Buckets) : [];
            $this->jobPostsTrend30dValues = ! empty($trend30Buckets) ? array_column($trend30Buckets, 'doc_count') : [];

            // Default initial load view is Month (30 days)
            $this->jobPostsTrendLabels = $this->jobPostsTrend30dLabels;
            $this->jobPostsTrendValues = $this->jobPostsTrend30dValues;

            $statusBuckets = $respJobs['aggregations']['job_status']['buckets'] ?? [];
            $this->jobsStatusLabels = ['Hiring', 'Hold', 'Closed'];
            $this->jobsStatusValues = [
                $statusBuckets['active']['doc_count'] ?? 0,
                $statusBuckets['hold']['doc_count'] ?? 0,
                $statusBuckets['inactive']['doc_count'] ?? 0,
            ];
            $this->jobsStatusTotal = $this->jobPostTotal;
        }

        // 3. Active Recruiters
        if (isset($responses[3]) && ! isset($responses[3]['error'])) {
            $respRec = $responses[3];
            $recBuckets = $respRec['aggregations']['activity']['buckets'] ?? [];
            $this->recruiterStatsLabels = ['Daily Active', 'Weekly Active', 'Monthly Active'];
            $this->recruiterStatsValues = [
                $recBuckets['dar']['doc_count'] ?? 0,
                $recBuckets['war']['doc_count'] ?? 0,
                $recBuckets['mar']['doc_count'] ?? 0,
            ];
            $this->recruiterStatsTotal = $respRec['hits']['total']['value'] ?? 0;
        }

        // 4. Candidates Index (Uploads & Pipeline Status)
        if (isset($responses[4]) && ! isset($responses[4]['error'])) {
            $respCand = $responses[4];
            $dailyBuckets = $respCand['aggregations']['last_30d']['daily_candidates']['buckets'] ?? [];
            $dailyReport = array_map(function ($item) {
                return [
                    'date' => Carbon::parse($item['key_as_string'])->format('d-M-Y'),
                    'count' => $item['doc_count'],
                ];
            }, $dailyBuckets);
            $this->candidateUploadLabels = array_column($dailyReport, 'date');
            $this->candidateUploadValues = array_column($dailyReport, 'count');
            $this->candidateUploadTotal = $respCand['hits']['total']['value'] ?? 0;

            $candStatusBuckets = $respCand['aggregations']['status_count']['buckets'] ?? [];
            $candStatusMap = array_column($candStatusBuckets, 'doc_count', 'key');
            $this->allcandidateslabels = ['InReview', 'Sourcing', 'Screening', 'Evaluation', 'Negotiation', 'Offered', 'Joined', 'Rejected'];
            $this->allcandidatesValues = [
                $candStatusMap[8] ?? 0,
                $candStatusMap[1] ?? 0,
                $candStatusMap[2] ?? 0,
                $candStatusMap[3] ?? 0,
                $candStatusMap[4] ?? 0,
                $candStatusMap[5] ?? 0,
                $candStatusMap[6] ?? 0,
                $candStatusMap[7] ?? 0,
            ];
            $this->allcandidates_total = array_sum(array_column($candStatusBuckets, 'doc_count'));
        }

        // 5. Interview Tracker
        if (isset($responses[5]) && ! isset($responses[5]['error'])) {
            $respInt = $responses[5];
            $intBuckets = $respInt['aggregations']['status_count']['buckets'] ?? [];
            $intMap = array_column($intBuckets, 'doc_count', 'key');
            $this->interviewDatalabels = ['Available', 'NotAvailable', 'Rescheduled', 'Cancel Interview', 'Other Reason'];
            $this->interviewDataValues = [
                $intMap[1] ?? 0,
                $intMap[2] ?? 0,
                $intMap[3] ?? 0,
                $intMap[4] ?? 0,
                $intMap[5] ?? 0,
            ];
            $this->interviewData_total = array_sum(array_column($intBuckets, 'doc_count'));
        }

        // 6. Email Activity
        if (isset($responses[6]) && ! isset($responses[6]['error'])) {
            $respEmail = $responses[6];
            $emailBuckets = $respEmail['aggregations']['status_count']['buckets'] ?? [];
            $emailMap = array_column($emailBuckets, 'doc_count', 'key');
            $this->emailSummaryDatalabels = ['Mass Mail', 'Candidate mail & CV Fwd', 'Interview Remainder', 'Notification', 'Accounts & Offer'];
            $this->emailSummaryDataValues = [
                $emailMap[1] ?? 0,
                $emailMap[2] ?? 0,
                $emailMap[3] ?? 0,
                $emailMap[4] ?? 0,
                $emailMap[5] ?? 0,
            ];
            $this->emailSummaryData_total = array_sum(array_column($emailBuckets, 'doc_count'));
        }

        // 7. WhatsApp Activity
        if (isset($responses[7]) && ! isset($responses[7]['error'])) {
            $respWa = $responses[7];
            $waBuckets = $respWa['aggregations']['status_count']['buckets'] ?? [];
            $waMap = array_column($waBuckets, 'doc_count', 'key');
            $this->whatsappSummaryDatalabels = ['Custom message', 'Reminder message'];
            $this->whatsappSummaryDataValues = [
                $waMap[1] ?? 0,
                $waMap[2] ?? 0,
            ];
            $this->whatsappSummary_total = array_sum(array_column($waBuckets, 'doc_count'));
        }
    }

    /**
     * Initial fallback default metrics
     */
    private function initializeDefaultMetrics(): void
    {
        $this->total_company_count = 0;
        $this->staffing_company_count = 0;
        $this->service_company_count = 0;
        $this->product_company_count = 0;
        $this->other_company_count = 0;
        $this->companies_labels = ['Staffing', 'Service', 'Product', 'Other'];
        $this->companiesValues = [0, 0, 0, 0];
        $this->companies_total = 0;

        $this->activecompanylabels = ['Daily Active', 'Weekly Active', 'Monthly Active'];
        $this->activecompanyValues = [0, 0, 0];
        $this->activecompanies = 0;

        $this->bench_labels = ['Pool', 'Bench', 'Placed', 'Hold', 'Exited'];
        $this->benchcandidateValues = [0, 0, 0, 0, 0];
        $this->bench_total = 0;

        $this->jobPostLabels = ['Daily', 'Weekly', 'Monthly'];
        $this->jobPostValues = [0, 0, 0];
        $this->jobPostTotal = 0;
        $this->jobPostsTrendLabels = [];
        $this->jobPostsTrendValues = [];
        $this->jobPostsTrend7dLabels = [];
        $this->jobPostsTrend7dValues = [];
        $this->jobPostsTrend30dLabels = [];
        $this->jobPostsTrend30dValues = [];

        $this->jobsStatusLabels = ['Hiring', 'Hold', 'Closed'];
        $this->jobsStatusValues = [0, 0, 0];
        $this->jobsStatusTotal = 0;

        $this->oprationalPlanLabels = ['Free', 'Paid'];
        $this->oprationalPlanValues = [0, 0];
        $this->oprationalPlanTotal = 0;

        $this->salesPlanLabels = ['Free', 'Paid'];
        $this->salesPlanValues = [0, 0];
        $this->salesPlanTotal = 0;

        $this->recruiterStatsLabels = ['Daily Active', 'Weekly Active', 'Monthly Active'];
        $this->recruiterStatsValues = [0, 0, 0];
        $this->recruiterStatsTotal = 0;

        $this->candidateUploadLabels = [];
        $this->candidateUploadValues = [];
        $this->candidateUploadTotal = 0;

        $this->allcandidateslabels = ['InReview', 'Sourcing', 'Screening', 'Evaluation', 'Negotiation', 'Offered', 'Joined', 'Rejected'];
        $this->allcandidatesValues = [0, 0, 0, 0, 0, 0, 0, 0];
        $this->allcandidates_total = 0;

        $this->interviewDatalabels = ['Available', 'NotAvailable', 'Rescheduled', 'Cancel Interview', 'Other Reason'];
        $this->interviewDataValues = [0, 0, 0, 0, 0];
        $this->interviewData_total = 0;

        $this->emailSummaryDatalabels = ['Mass Mail', 'Candidate mail & CV Fwd', 'Interview Remainder', 'Notification', 'Accounts & Offer'];
        $this->emailSummaryDataValues = [0, 0, 0, 0, 0];
        $this->emailSummaryData_total = 0;

        $this->whatsappSummaryDatalabels = ['Custom message', 'Reminder message'];
        $this->whatsappSummaryDataValues = [0, 0];
        $this->whatsappSummary_total = 0;
    }

    /**
     * Compute Single Chart Aggregation for Period Filter API
     */
    private function fetchChartAggregate(string $chartKey, string $period, ?string $startDate, ?string $endDate): array
    {
        $dateRange = $this->resolveDateRange($period, $startDate, $endDate);
        [$companyMustNot, $tenantMustNot] = $this->getSuperAdminExclusions();

        switch ($chartKey) {
            case 'subscription_overview':
                $params = [
                    'index' => ELASTIC_COMPANIES_INDEX,
                    'body' => [
                        'size' => 0,
                        'query' => [
                            'bool' => [
                                'must_not' => $companyMustNot,
                            ],
                        ],
                        'aggs' => [
                            'coins_nested' => [
                                'nested' => ['path' => 'company_coins'],
                                'aggs' => [
                                    'operational_only' => [
                                        'filter' => [
                                            'bool' => [
                                                'must' => [
                                                    ['term' => ['company_coins.event_type' => 'operational']],
                                                    [
                                                        'bool' => [
                                                            'should' => [
                                                                ['range' => ['company_coins.created_at' => ['gte' => $dateRange['gte'], 'lte' => $dateRange['lte'], 'time_zone' => '+05:30']]],
                                                                ['range' => ['company_coins.updated_at' => ['gte' => $dateRange['gte'], 'lte' => $dateRange['lte'], 'time_zone' => '+05:30']]],
                                                                [
                                                                    'bool' => [
                                                                        'must' => [
                                                                            ['range' => ['company_coins.start_date' => ['lte' => $dateRange['lte'], 'time_zone' => '+05:30']]],
                                                                            ['range' => ['company_coins.end_date' => ['gte' => $dateRange['gte'], 'time_zone' => '+05:30']]],
                                                                        ],
                                                                    ],
                                                                ],
                                                            ],
                                                            'minimum_should_match' => 1,
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'aggs' => [
                                            'plan_types' => [
                                                'filters' => [
                                                    'filters' => [
                                                        'free' => [
                                                            'bool' => [
                                                                'should' => [
                                                                    ['match' => ['company_coins.package_name' => 'Free']],
                                                                    ['bool' => ['must_not' => [['exists' => ['field' => 'company_coins.package_name']]]]],
                                                                ],
                                                                'minimum_should_match' => 1,
                                                            ],
                                                        ],
                                                        'paid' => [
                                                            'bool' => [
                                                                'must' => [['exists' => ['field' => 'company_coins.package_name']]],
                                                                'must_not' => [['match' => ['company_coins.package_name' => 'Free']]],
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                                'aggs' => [
                                                    'to_company' => ['reverse_nested' => new \stdClass],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];
                $resp = $this->elasticService->getClient()->search($params);
                $buckets = $resp['aggregations']['coins_nested']['operational_only']['plan_types']['buckets'] ?? [];
                $free = $buckets['free']['to_company']['doc_count'] ?? 0;
                $paid = $buckets['paid']['to_company']['doc_count'] ?? 0;

                return [
                    'labels' => ['Free', 'Paid'],
                    'data' => [$free, $paid],
                    'total' => $free + $paid,
                ];

            case 'company_types':
                $params = [
                    'index' => ELASTIC_COMPANIES_INDEX,
                    'body' => [
                        'size' => 0,
                        'query' => [
                            'bool' => [
                                'should' => [
                                    ['range' => ['created_at' => ['gte' => $dateRange['gte'], 'lte' => $dateRange['lte'], 'time_zone' => '+05:30']]],
                                    ['range' => ['last_login' => ['gte' => $dateRange['gte'], 'lte' => $dateRange['lte'], 'time_zone' => '+05:30']]],
                                ],
                                'minimum_should_match' => 1,
                                'must_not' => $companyMustNot,
                            ],
                        ],
                        'aggs' => [
                            'company_type_count' => ['terms' => ['field' => 'company_type', 'size' => 3]],
                            'other_count' => ['missing' => ['field' => 'company_type']],
                        ],
                    ],
                ];
                $resp = $this->elasticService->getClient()->search($params);
                $buckets = $resp['aggregations']['company_type_count']['buckets'] ?? [];
                $map = array_column($buckets, 'doc_count', 'key');
                $other = $resp['aggregations']['other_count']['doc_count'] ?? 0;
                $data = [$map[1] ?? 0, $map[2] ?? 0, $map[3] ?? 0, $other];

                return [
                    'labels' => ['Staffing', 'Service', 'Product', 'Other'],
                    'data' => $data,
                    'total' => array_sum($data),
                ];

            case 'company_activity':
                $params = [
                    'index' => ELASTIC_COMPANIES_INDEX,
                    'body' => [
                        'size' => 0,
                        'query' => [
                            'bool' => [
                                'must_not' => $companyMustNot,
                            ],
                        ],
                        'aggs' => [
                            'activity' => [
                                'filters' => [
                                    'filters' => [
                                        'active' => [
                                            'range' => [
                                                'last_login' => [
                                                    'gte' => $dateRange['gte'],
                                                    'lte' => $dateRange['lte'],
                                                    'time_zone' => '+05:30',
                                                ],
                                            ],
                                        ],
                                        'dac' => ['range' => ['last_login' => ['gte' => 'now-1d/d', 'time_zone' => '+05:30']]],
                                        'wac' => ['range' => ['last_login' => ['gte' => 'now-7d/d', 'time_zone' => '+05:30']]],
                                        'mac' => ['range' => ['last_login' => ['gte' => 'now-30d/d', 'time_zone' => '+05:30']]],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];
                $resp = $this->elasticService->getClient()->search($params);
                $b = $resp['aggregations']['activity']['buckets'] ?? [];

                return [
                    'labels' => ['Daily Active', 'Weekly Active', 'Monthly Active'],
                    'data' => [$b['dac']['doc_count'] ?? 0, $b['wac']['doc_count'] ?? 0, $b['mac']['doc_count'] ?? 0],
                    'total' => $b['active']['doc_count'] ?? 0,
                ];

            case 'bench_candidate':
                $params = [
                    'index' => ELASTIC_CANDIDATES_BENCH,
                    'body' => [
                        'size' => 0,
                        'query' => [
                            'bool' => [
                                'should' => [
                                    ['range' => ['created_at' => ['gte' => $dateRange['gte'], 'lte' => $dateRange['lte'], 'time_zone' => '+05:30']]],
                                    ['range' => ['updated_at' => ['gte' => $dateRange['gte'], 'lte' => $dateRange['lte'], 'time_zone' => '+05:30']]],
                                ],
                                'minimum_should_match' => 1,
                                'must_not' => $tenantMustNot,
                            ],
                        ],
                        'aggs' => ['status_count' => ['terms' => ['field' => 'status', 'size' => 5]]],
                    ],
                ];
                $resp = $this->elasticService->getClient()->search($params);
                $buckets = $resp['aggregations']['status_count']['buckets'] ?? [];
                $map = array_column($buckets, 'doc_count', 'key');
                $data = [$map[1] ?? 0, $map[2] ?? 0, $map[3] ?? 0, $map[4] ?? 0, $map[5] ?? 0];

                return [
                    'labels' => ['Pool', 'Bench', 'Placed', 'Hold', 'Exited'],
                    'data' => $data,
                    'total' => array_sum($data),
                ];

            case 'job_posts_trend':
                $params = [
                    'index' => ELASTIC_JOBS,
                    'body' => [
                        'size' => 0,
                        'query' => [
                            'bool' => [
                                'must_not' => $tenantMustNot,
                            ],
                        ],
                        'aggs' => [
                            'trend' => [
                                'filter' => [
                                    'range' => [
                                        'created_at' => [
                                            'gte' => $dateRange['gte'],
                                            'lte' => $dateRange['lte'],
                                            'time_zone' => '+05:30',
                                        ],
                                    ],
                                ],
                                'aggs' => [
                                    'by_day' => [
                                        'date_histogram' => [
                                            'field' => 'created_at',
                                            'calendar_interval' => 'day',
                                            'format' => 'yyyy-MM-dd',
                                            'min_doc_count' => 0,
                                            'extended_bounds' => ['min' => $dateRange['min_day'], 'max' => $dateRange['max_day']],
                                            'time_zone' => '+05:30',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];
                $resp = $this->elasticService->getClient()->search($params);
                $buckets = $resp['aggregations']['trend']['by_day']['buckets'] ?? [];
                $labels = array_map(fn ($item) => Carbon::parse($item['key_as_string'])->format('d M'), $buckets);
                $values = array_column($buckets, 'doc_count');

                return [
                    'labels' => $labels,
                    'data' => $values,
                    'total' => array_sum($values),
                ];

            case 'job_status':
                $params = [
                    'index' => ELASTIC_JOBS,
                    'body' => [
                        'size' => 0,
                        'query' => [
                            'bool' => [
                                'should' => [
                                    ['range' => ['created_at' => ['gte' => $dateRange['gte'], 'lte' => $dateRange['lte'], 'time_zone' => '+05:30']]],
                                    ['range' => ['updated_at' => ['gte' => $dateRange['gte'], 'lte' => $dateRange['lte'], 'time_zone' => '+05:30']]],
                                ],
                                'minimum_should_match' => 1,
                                'must_not' => $tenantMustNot,
                            ],
                        ],
                        'aggs' => [
                            'job_status' => [
                                'filters' => [
                                    'filters' => [
                                        'active' => ['term' => ['status' => 'active']],
                                        'hold' => ['term' => ['status' => 'hold']],
                                        'inactive' => ['term' => ['status' => 'inactive']],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];
                $resp = $this->elasticService->getClient()->search($params);
                $b = $resp['aggregations']['job_status']['buckets'] ?? [];
                $data = [$b['active']['doc_count'] ?? 0, $b['hold']['doc_count'] ?? 0, $b['inactive']['doc_count'] ?? 0];

                return [
                    'labels' => ['Hiring', 'Hold', 'Closed'],
                    'data' => $data,
                    'total' => array_sum($data),
                ];

            case 'candidate_pipeline':
                $params = [
                    'index' => ELASTIC_CANDIDATES,
                    'body' => [
                        'size' => 0,
                        'query' => [
                            'bool' => [
                                'must' => [
                                    [
                                        'range' => [
                                            'created_at' => [
                                                'gte' => $dateRange['gte'],
                                                'lte' => $dateRange['lte'],
                                                'time_zone' => '+05:30',
                                            ],
                                        ],
                                    ],
                                ],
                                'must_not' => $tenantMustNot,
                            ],
                        ],
                        'aggs' => [
                            'status_count' => [
                                'terms' => ['field' => 'job_master_status_id', 'size' => 10],
                            ],
                        ],
                    ],
                ];
                $resp = $this->elasticService->getClient()->search($params);
                $b = $resp['aggregations']['status_count']['buckets'] ?? [];
                $map = array_column($b, 'doc_count', 'key');
                $data = [
                    $map[8] ?? 0, $map[1] ?? 0, $map[2] ?? 0, $map[3] ?? 0,
                    $map[4] ?? 0, $map[5] ?? 0, $map[6] ?? 0, $map[7] ?? 0,
                ];

                return [
                    'labels' => ['InReview', 'Sourcing', 'Screening', 'Evaluation', 'Negotiation', 'Offered', 'Joined', 'Rejected'],
                    'data' => $data,
                    'total' => array_sum($data),
                ];

            case 'interview_status':
                $params = [
                    'index' => ELASTIC_INERTVIEW_TRACKER,
                    'body' => [
                        'size' => 0,
                        'query' => [
                            'bool' => [
                                'must' => [
                                    [
                                        'range' => [
                                            'created_at' => [
                                                'gte' => $dateRange['gte'],
                                                'lte' => $dateRange['lte'],
                                                'time_zone' => '+05:30',
                                            ],
                                        ],
                                    ],
                                ],
                                'must_not' => $tenantMustNot,
                            ],
                        ],
                        'aggs' => [
                            'status_count' => ['terms' => ['field' => 'available_status', 'size' => 10]],
                        ],
                    ],
                ];
                $resp = $this->elasticService->getClient()->search($params);
                $b = $resp['aggregations']['status_count']['buckets'] ?? [];
                $map = array_column($b, 'doc_count', 'key');
                $data = [$map[1] ?? 0, $map[2] ?? 0, $map[3] ?? 0, $map[4] ?? 0, $map[5] ?? 0];

                return [
                    'labels' => ['Available', 'NotAvailable', 'Rescheduled', 'Cancel Interview', 'Other Reason'],
                    'data' => $data,
                    'total' => array_sum($data),
                ];

            case 'email_activity':
                $params = [
                    'index' => ELASTIC_EMAIL_COUNT,
                    'body' => [
                        'size' => 0,
                        'query' => [
                            'bool' => [
                                'must' => [
                                    [
                                        'range' => [
                                            'created_at' => [
                                                'gte' => $dateRange['gte'],
                                                'lte' => $dateRange['lte'],
                                                'time_zone' => '+05:30',
                                            ],
                                        ],
                                    ],
                                ],
                                'must_not' => $tenantMustNot,
                            ],
                        ],
                        'aggs' => [
                            'status_count' => ['terms' => ['field' => 'emailType', 'size' => 10]],
                        ],
                    ],
                ];
                $resp = $this->elasticService->getClient()->search($params);
                $b = $resp['aggregations']['status_count']['buckets'] ?? [];
                $map = array_column($b, 'doc_count', 'key');
                $data = [$map[1] ?? 0, $map[2] ?? 0, $map[3] ?? 0, $map[4] ?? 0, $map[5] ?? 0];

                return [
                    'labels' => ['Mass Mail', 'Candidate mail & CV Fwd', 'Interview Remainder', 'Notification', 'Accounts & Offer'],
                    'data' => $data,
                    'total' => array_sum($data),
                ];

            case 'whatsapp_activity':
                $params = [
                    'index' => ELASTIC_WHATSAPP_COUNT,
                    'body' => [
                        'size' => 0,
                        'query' => [
                            'bool' => [
                                'must' => [
                                    [
                                        'range' => [
                                            'created_at' => [
                                                'gte' => $dateRange['gte'],
                                                'lte' => $dateRange['lte'],
                                                'time_zone' => '+05:30',
                                            ],
                                        ],
                                    ],
                                ],
                                'must_not' => $tenantMustNot,
                            ],
                        ],
                        'aggs' => [
                            'status_count' => ['terms' => ['field' => 'messageType', 'size' => 10]],
                        ],
                    ],
                ];
                $resp = $this->elasticService->getClient()->search($params);
                $b = $resp['aggregations']['status_count']['buckets'] ?? [];
                $map = array_column($b, 'doc_count', 'key');
                $data = [$map[1] ?? 0, $map[2] ?? 0];

                return [
                    'labels' => ['Custom message', 'Reminder message'],
                    'data' => $data,
                    'total' => array_sum($data),
                ];

            default:
                throw new \InvalidArgumentException("Unknown chart key: {$chartKey}");
        }
    }

    /**
     * Resolve Date Range for Elastic Range Queries
     */
    private function resolveDateRange(string $period, ?string $startDate, ?string $endDate): array
    {
        if ($period === 'custom' && $startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
            if ($start->gt($end)) {
                $temp = $start;
                $start = $end->copy()->startOfDay();
                $end = $temp->copy()->endOfDay();
            }

            return [
                'gte' => $start->format('Y-m-d\TH:i:s'),
                'lte' => $end->format('Y-m-d\TH:i:s'),
                'min_day' => $start->format('Y-m-d'),
                'max_day' => $end->format('Y-m-d'),
                'time_zone' => '+05:30',
            ];
        }

        if ($period === 'week') {
            return [
                'gte' => 'now-6d/d',
                'lte' => 'now/d',
                'min_day' => 'now-6d/d',
                'max_day' => 'now/d',
                'time_zone' => '+05:30',
            ];
        }

        // Default: month (30 calendar days inclusive)
        return [
            'gte' => 'now-29d/d',
            'lte' => 'now/d',
            'min_day' => 'now-29d/d',
            'max_day' => 'now/d',
            'time_zone' => '+05:30',
        ];
    }
}
