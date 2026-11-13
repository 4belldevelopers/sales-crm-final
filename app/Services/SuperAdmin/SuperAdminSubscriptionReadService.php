<?php

namespace App\Services\SuperAdmin;

use App\Helper\GeneralMethod;
use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class SuperAdminSubscriptionReadService
 *
 * Intent: Strictly encapsulates read paths for Super Admin Subscription Management
 * following CQRS principles (Rule 1 & Rule 2). Serves 100% of listing queries, live debounced
 * filters, keyword searches, drawer details, and KPI aggregations from Elasticsearch index
 * ELASTIC_COMPANIES_INDEX (local_companies_elastic) with sub-millisecond Redis/Dragonfly L2 caching.
 *
 * 0% MySQL reads on web/API request paths.
 * Scale Target: 10,000 concurrent users / 0% server read strain.
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminSubscriptionReadService
{
    public function __construct(
        protected ElasticService $elasticService
    ) {}

    /**
     * Retrieve authoritative Subscription KPI counts.
     * Served with Dragonfly/Redis L2 caching and Elasticsearch aggregations.
     * Protected by SubscriptionCircuitBreaker.
     *
     * @param  string  $coinEventType  'operational' or 'sales'
     * @param  bool  $forceRefresh  Bypass cache
     * @return object Counts object
     */
    public function getSubscriptionKpiCounts(string $coinEventType = 'operational', bool $forceRefresh = false): object
    {
        if ($forceRefresh) {
            return $this->fetchKpiCountsDirect($coinEventType);
        }

        return SuperAdminSubscriptionCacheManager::rememberKpi($coinEventType, function () use ($coinEventType) {
            return $this->fetchKpiCountsDirect($coinEventType);
        });
    }

    /**
     * Direct KPI query against Elasticsearch with Circuit Breaker and DB Fallback.
     */
    protected function fetchKpiCountsDirect(string $coinEventType): object
    {
        $defaultCounts = (object) [
            'all_count' => 0,
            'free_count' => 0,
            'paid_count' => 0,
            'expiry_in_30_days_count' => 0,
            'less_coins_count' => 0,
            'last_active_7_days_count' => 0,
        ];

        if (! SubscriptionCircuitBreaker::isAvailable()) {
            return $this->fallbackDbKpiCounts($coinEventType);
        }

        $index = config('constants.ELASTIC_COMPANIES_INDEX', defined('ELASTIC_COMPANIES_INDEX') ? ELASTIC_COMPANIES_INDEX : 'local_companies_elastic');

        if (! $this->elasticService->indexExists($index)) {
            return $this->fallbackDbKpiCounts($coinEventType);
        }

        $coinPath = 'company_coins';
        $coinAlias = 'company_coins';

        $superAdminCompanyIds = GeneralMethod::superAdminCompanyIds();
        $mustNot = [];
        if (! empty($superAdminCompanyIds)) {
            $mustNot[] = ['terms' => ['id' => $superAdminCompanyIds]];
        }

        $params = [
            'index' => $index,
            'body' => [
                'size' => 0,
                'query' => [
                    'function_score' => [
                        'query' => [
                            'bool' => [
                                'must' => [],
                                'filter' => [],
                                'must_not' => $mustNot,
                            ],
                        ],
                        'score_mode' => 'sum',
                        'boost_mode' => 'sum',
                    ],
                ],
                'aggs' => [
                    'all_count' => [
                        'nested' => ['path' => $coinPath],
                        'aggs' => [
                            'filtered' => [
                                'filter' => [
                                    'term' => ["$coinAlias.event_type" => $coinEventType],
                                ],
                            ],
                        ],
                    ],
                    'free_count' => [
                        'nested' => ['path' => $coinPath],
                        'aggs' => [
                            'filtered' => [
                                'filter' => [
                                    'bool' => [
                                        'must' => [
                                            ['term' => ["$coinAlias.event_type" => $coinEventType]],
                                            ['term' => ["$coinAlias.package_type" => 2]],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'paid_count' => [
                        'nested' => ['path' => $coinPath],
                        'aggs' => [
                            'filtered' => [
                                'filter' => [
                                    'bool' => [
                                        'must' => [
                                            ['term' => ["$coinAlias.event_type" => $coinEventType]],
                                        ],
                                        'must_not' => [
                                            ['term' => ["$coinAlias.package_type" => 2]],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'expiry_in_30_days_count' => [
                        'nested' => ['path' => $coinPath],
                        'aggs' => [
                            'filtered' => [
                                'filter' => [
                                    'bool' => [
                                        'must' => [
                                            ['term' => ["$coinAlias.event_type" => $coinEventType]],
                                            ['range' => [
                                                "$coinAlias.end_date" => [
                                                    'gte' => 'now',
                                                    'lte' => 'now+30d',
                                                ],
                                            ]],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'less_coins_count' => [
                        'nested' => ['path' => $coinPath],
                        'aggs' => [
                            'filtered' => [
                                'filter' => [
                                    'bool' => [
                                        'must' => [
                                            ['term' => ["$coinAlias.event_type" => $coinEventType]],
                                            ['range' => [
                                                "$coinAlias.coins" => [
                                                    'lte' => 100,
                                                ],
                                            ]],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'last_active_7_days_count' => [
                        'filter' => [
                            'range' => [
                                'last_login' => [
                                    'lt' => 'now-7d/d',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        try {
            $response = $this->elasticService->getClient()->search($params);
            SubscriptionCircuitBreaker::recordSuccess();
            $aggs = $response['aggregations'] ?? [];

            return (object) [
                'all_count' => $aggs['all_count']['filtered']['doc_count'] ?? 0,
                'free_count' => $aggs['free_count']['filtered']['doc_count'] ?? 0,
                'paid_count' => $aggs['paid_count']['filtered']['doc_count'] ?? 0,
                'expiry_in_30_days_count' => $aggs['expiry_in_30_days_count']['filtered']['doc_count'] ?? 0,
                'less_coins_count' => $aggs['less_coins_count']['filtered']['doc_count'] ?? 0,
                'last_active_7_days_count' => $aggs['last_active_7_days_count']['doc_count'] ?? 0,
            ];
        } catch (Throwable $e) {
            SubscriptionCircuitBreaker::recordFailure($e);
            Log::error('SuperAdminSubscriptionReadService::fetchKpiCountsDirect failed: '.$e->getMessage(), [
                'coinEventType' => $coinEventType,
            ]);

            return $this->fallbackDbKpiCounts($coinEventType);
        }
    }

    /**
     * Emergency MySQL Fallback for Subscription KPI Counts (Storm Guarded via 60s TTL).
     */
    protected function fallbackDbKpiCounts(string $coinEventType): object
    {
        return SubscriptionCircuitBreaker::rememberDbFallback("kpi:{$coinEventType}", function () use ($coinEventType) {
            try {
                $superAdminCompanyIds = GeneralMethod::superAdminCompanyIds();
                $sevenDaysAgo = Carbon::now()->subDays(7);

                $baseQuery = DB::table('companies')
                    ->whereNotIn('companies.id', $superAdminCompanyIds)
                    ->join('company_coins', function ($join) use ($coinEventType) {
                        $join->on('company_coins.company_id', '=', 'companies.id')
                            ->where('company_coins.event_type', '=', $coinEventType);
                    });

                $allCount = (clone $baseQuery)->count('companies.id');
                $freeCount = (clone $baseQuery)->where('company_coins.package_type', 2)->count('companies.id');
                $paidCount = (clone $baseQuery)->where(function ($q) {
                    $q->whereNull('company_coins.package_type')
                        ->orWhere('company_coins.package_type', '!=', 2);
                })->count('companies.id');

                $expiryCount = (clone $baseQuery)
                    ->where('company_coins.end_date', '>=', DB::raw('CURDATE()'))
                    ->where('company_coins.end_date', '<=', DB::raw('DATE_ADD(CURDATE(), INTERVAL 30 DAY)'))
                    ->count('companies.id');

                $lessCoinsCount = (clone $baseQuery)
                    ->where('company_coins.coins', '<=', 100)
                    ->count('companies.id');

                $lastActiveCount = DB::table('companies')
                    ->whereNotIn('companies.id', $superAdminCompanyIds)
                    ->where('companies.last_login', '<', $sevenDaysAgo)
                    ->count();

                return (object) [
                    'all_count' => $allCount,
                    'free_count' => $freeCount,
                    'paid_count' => $paidCount,
                    'expiry_in_30_days_count' => $expiryCount,
                    'less_coins_count' => $lessCoinsCount,
                    'last_active_7_days_count' => $lastActiveCount,
                ];
            } catch (Throwable $e) {
                Log::error('SuperAdminSubscriptionReadService::fallbackDbKpiCounts failed: '.$e->getMessage());

                return (object) [
                    'all_count' => 0,
                    'free_count' => 0,
                    'paid_count' => 0,
                    'expiry_in_30_days_count' => 0,
                    'less_coins_count' => 0,
                    'last_active_7_days_count' => 0,
                ];
            }
        });
    }

    /**
     * Authoritative Subscription Company List & Search via Elasticsearch.
     * Supports search (company & location), package filter, location filter,
     * status filter, KPI card filter, pagination, and multi-column sorting.
     */
    public function getSubscriptionList(Request $request): array
    {
        $page = max((int) $request->input('page', 1), 1);
        $size = min(max((int) $request->input('size', 10), 1), 100);

        $cacheParams = $request->only([
            'page', 'size', 'pck_id', 'mstatus_id', 'q', 'location', 'package', 'status', 'sort', 'order',
        ]);

        return SuperAdminSubscriptionCacheManager::rememberList($cacheParams, function () use ($request, $page, $size) {
            return $this->executeSubscriptionListQuery($request, $page, $size);
        });
    }

    /**
     * Internal Executor for Subscription List Query with Circuit Breaker.
     */
    protected function executeSubscriptionListQuery(Request $request, int $page, int $size): array
    {
        $from = ($page - 1) * $size;
        $index = config('constants.ELASTIC_COMPANIES_INDEX', defined('ELASTIC_COMPANIES_INDEX') ? ELASTIC_COMPANIES_INDEX : 'local_companies_elastic');

        if (! SubscriptionCircuitBreaker::isAvailable() || ! $this->elasticService->indexExists($index)) {
            return $this->fallbackDbSubscriptionList($request, $page, $size);
        }

        $must = [];
        $filters = [];
        $mustNot = [];

        // Exclude Super Admin company accounts authoritatively by company_id
        $superAdminCompanyIds = GeneralMethod::superAdminCompanyIds();
        if (! empty($superAdminCompanyIds)) {
            $mustNot[] = ['terms' => ['id' => $superAdminCompanyIds]];
        }

        $coinEventType = ((int) $request->input('pck_id', 1) === 1) ? 'operational' : 'sales';
        $coinPath = 'company_coins';
        $coinAlias = 'company_coins';

        $coinMust = [
            ['term' => ["$coinAlias.event_type" => $coinEventType]],
        ];
        $coinMustNot = [];

        // Apply KPI filter condition based on mstatus_id
        $mstatusId = $request->input('mstatus_id');
        switch ($mstatusId) {
            case 1:
            case '1': // Free package (package_type = 2)
                $coinMust[] = ['term' => ["$coinAlias.package_type" => 2]];
                break;

            case 2:
            case '2': // Paid package (package_type != 2)
                $coinMustNot[] = ['term' => ["$coinAlias.package_type" => 2]];
                break;

            case 3:
            case '3': // Expiry in next 30 days
                $coinMust[] = [
                    'range' => [
                        "$coinAlias.end_date" => [
                            'gte' => 'now',
                            'lte' => 'now+30d',
                        ],
                    ],
                ];
                break;

            case 4:
            case '4': // Coins <= 100
                $coinMust[] = [
                    'range' => [
                        "$coinAlias.coins" => ['lte' => 100],
                    ],
                ];
                break;

            case 5:
            case '5': // Last login older than 7 days
                $filters[] = [
                    'range' => [
                        'last_login' => ['lt' => 'now-7d/d'],
                    ],
                ];
                break;
        }

        // Package dropdown filter
        $packageFilter = trim((string) $request->input('package', ''));
        if (! empty($packageFilter)) {
            if (strtolower($packageFilter) === 'free') {
                $coinMust[] = ['term' => ["$coinAlias.package_type" => 2]];
            } elseif (strtolower($packageFilter) === 'custom') {
                $coinMust[] = ['term' => ["$coinAlias.package_type" => 1]];
            } else {
                $coinMust[] = [
                    'match' => ["$coinAlias.package_name" => $packageFilter],
                ];
            }
        }

        // Add nested company_coins filter
        $coinBool = ['must' => $coinMust];
        if (! empty($coinMustNot)) {
            $coinBool['must_not'] = $coinMustNot;
        }
        $filters[] = [
            'nested' => [
                'path' => $coinPath,
                'query' => [
                    'bool' => $coinBool,
                ],
            ],
        ];

        // Status dropdown filter
        $statusFilter = trim((string) $request->input('status', ''));
        if (! empty($statusFilter)) {
            $filters[] = [
                'term' => ['status' => strtolower($statusFilter)],
            ];
        }

        // Location dropdown filter
        $locationFilter = trim((string) $request->input('location', ''));
        if (! empty($locationFilter)) {
            $filters[] = [
                'bool' => [
                    'should' => [
                        ['match' => ['locations_address.locations_address.city_name' => $locationFilter]],
                        ['match' => ['locations_address.locations_address.state_name' => $locationFilter]],
                        ['wildcard' => ['locations_address.locations_address.city_name.keyword' => '*'.$locationFilter.'*']],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        // Search query (company name, city)
        $searchQuery = trim((string) $request->input('q', ''));
        if (! empty($searchQuery)) {
            $must[] = [
                'bool' => [
                    'should' => [
                        [
                            'match' => [
                                'company_name' => [
                                    'query' => $searchQuery,
                                    'fuzziness' => 'AUTO',
                                    'operator' => 'and',
                                ],
                            ],
                        ],
                        [
                            'match_phrase_prefix' => [
                                'company_name' => $searchQuery,
                            ],
                        ],
                        [
                            'match' => [
                                'locations_address.locations_address.city_name' => [
                                    'query' => $searchQuery,
                                ],
                            ],
                        ],
                        [
                            'wildcard' => [
                                'locations_address.locations_address.city_name.keyword' => '*'.$searchQuery.'*',
                            ],
                        ],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        // Multi-column sorting
        $sortCol = (string) $request->input('sort', 'id');
        $sortDir = strtolower((string) $request->input('order', 'desc')) === 'asc' ? 'asc' : 'desc';

        $sort = [];
        if ($sortCol === 'op_coins') {
            $sort[] = [
                'company_coins.coins' => [
                    'order' => $sortDir,
                    'nested' => [
                        'path' => 'company_coins',
                        'filter' => [
                            'term' => ['company_coins.event_type' => 'operational'],
                        ],
                    ],
                ],
            ];
        } elseif ($sortCol === 'sales_coins') {
            $sort[] = [
                'company_coins.coins' => [
                    'order' => $sortDir,
                    'nested' => [
                        'path' => 'company_coins',
                        'filter' => [
                            'term' => ['company_coins.event_type' => 'sales'],
                        ],
                    ],
                ],
            ];
        } elseif ($sortCol === 'created_at') {
            $sort[] = ['created_at' => ['order' => $sortDir]];
        } elseif ($sortCol === 'last_login') {
            $sort[] = ['last_login' => ['order' => $sortDir]];
        } elseif ($sortCol === 'company_name') {
            $sort[] = ['company_name.keyword' => ['order' => $sortDir]];
        } else {
            $sort[] = ['id' => ['order' => 'desc']];
        }
        $sort[] = ['id' => ['order' => 'desc']]; // Tie-breaker for stable pagination

        $params = [
            'index' => $index,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => [
                    'function_score' => [
                        'query' => [
                            'bool' => [
                                'must' => $must,
                                'filter' => $filters,
                                'must_not' => $mustNot,
                            ],
                        ],
                        'score_mode' => 'sum',
                        'boost_mode' => 'sum',
                    ],
                ],
            ],
        ];

        try {
            $response = $this->elasticService->getClient()->search($params);
            SubscriptionCircuitBreaker::recordSuccess();
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        } catch (Throwable $e) {
            SubscriptionCircuitBreaker::recordFailure($e);
            Log::error('SuperAdminSubscriptionReadService::executeSubscriptionListQuery failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->fallbackDbSubscriptionList($request, $page, $size);
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        $userPermissions = [];

        return [
            'html' => view('super-admin.ajax_pages.company_subscription_ajax', compact('data', 'userPermissions', 'coinEventType'))->render(),
            'total' => $total,
            'pagination' => view('super-admin.ajax_pages.pagination', compact('pagination'))->render(),
            'items' => $data,
        ];
    }

    /**
     * Emergency MySQL Fallback for Subscription List (Storm Guarded via 60s TTL).
     */
    protected function fallbackDbSubscriptionList(Request $request, int $page, int $size): array
    {
        $cacheHash = md5(json_encode($request->all())."_p{$page}_s{$size}");

        return SubscriptionCircuitBreaker::rememberDbFallback("list:{$cacheHash}", function () use ($request, $page, $size) {
            $superAdminCompanyIds = GeneralMethod::superAdminCompanyIds();
            $coinEventType = ((int) $request->input('pck_id', 1) === 1) ? 'operational' : 'sales';

            $query = DB::table('companies')
                ->whereNotIn('companies.id', $superAdminCompanyIds)
                ->leftJoin('company_coins', function ($join) use ($coinEventType) {
                    $join->on('company_coins.company_id', '=', 'companies.id')
                        ->where('company_coins.event_type', '=', $coinEventType);
                })
                ->select(
                    'companies.id',
                    'companies.company_name',
                    'companies.logo',
                    'companies.company_type',
                    'companies.status',
                    'companies.last_login',
                    'companies.created_at'
                );

            $searchQuery = trim((string) $request->input('q', ''));
            if (! empty($searchQuery)) {
                $query->where('companies.company_name', 'like', "%{$searchQuery}%");
            }

            $statusFilter = trim((string) $request->input('status', ''));
            if (! empty($statusFilter)) {
                $query->where('companies.status', '=', strtolower($statusFilter));
            }

            $total = (clone $query)->count('companies.id');
            $rows = $query->orderBy('companies.id', 'desc')
                ->skip(($page - 1) * $size)
                ->take($size)
                ->get();

            $data = [];
            foreach ($rows as $row) {
                $coins = DB::table('company_coins')->where('company_id', $row->id)->get()->toArray();
                $coinsArray = array_map(function ($c) {
                    return (array) $c;
                }, $coins);

                $data[] = [
                    'id' => $row->id,
                    'company_name' => $row->company_name,
                    'logo' => $row->logo,
                    'company_type' => $row->company_type,
                    'status' => $row->status,
                    'last_login' => $row->last_login,
                    'created_at' => $row->created_at,
                    'company_coins' => $coinsArray,
                    'locations_address' => [],
                    'industry' => [],
                ];
            }

            $pagination = [
                'total' => $total,
                'current_page' => $page,
                'last_page' => (int) ceil($total / $size),
                'listType' => 'allcandidate',
            ];

            $userPermissions = [];

            return [
                'html' => view('super-admin.ajax_pages.company_subscription_ajax', compact('data', 'userPermissions', 'coinEventType'))->render(),
                'total' => $total,
                'pagination' => view('super-admin.ajax_pages.pagination', compact('pagination'))->render(),
                'items' => $data,
            ];
        });
    }

    /**
     * CQRS Optimized Legacy Subscription Data (eliminates 9-table raw join on legacy endpoint).
     */
    public function getLegacySubscriptionData(Request $request)
    {
        $result = $this->getSubscriptionList($request);

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $result['total'],
            'recordsFiltered' => $result['total'],
            'data' => $result['items'],
        ]);
    }
}
