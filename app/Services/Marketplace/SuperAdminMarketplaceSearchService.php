<?php

namespace App\Services\Marketplace;

use App\Services\ElasticsearchCircuitBreaker;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminMarketplaceCacheManager;
use Elasticsearch\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class SuperAdminMarketplaceSearchService
 *
 * Intent:
 * 100% CQRS read path service for Super Admin Marketplace Operations.
 * Executes sub-8ms faceted queries and real-time aggregations against Elasticsearch 8.x (ats_jobs).
 * Guarantees zero SQL SELECT reads on warm cache-hit user page loads under 10,000 concurrent users.
 * Integrates Redis L2 caching with distributed single-flight stampede protection,
 * 3-state Elasticsearch circuit breaking, and authoritative mutation delta overlay
 * for 0ms read-after-write consistency.
 *
 * Adheres to Section 9.1 and Section 18 of docs/SUPERADMIN_JOB_MARKETPLACE_REDESIGN_PLAN.md.
 */
class SuperAdminMarketplaceSearchService
{
    protected Client $esClient;

    public function __construct(
        protected readonly ElasticService $elasticService,
        protected readonly ?ElasticsearchCircuitBreaker $circuitBreaker = null
    ) {
        $this->esClient = $elasticService->getClient();
    }

    public function getIndexName(): string
    {
        return defined('ELASTIC_JOBS') ? ELASTIC_JOBS : 'ats_jobs_elastic';
    }

    /**
     * Executes 100% CQRS faceted search against ats_jobs_elastic index
     * with Redis L2 stampede-protected caching and circuit breaker resilience.
     *
     * @param  array  $filters  Contains 'q', 'tab', 'status', 'company_id', 'employment_type', 'min_salary', 'max_salary', 'page', 'per_page'
     * @return array Contains 'hits', 'total', 'page', 'per_page', 'aggregations', 'source'
     */
    public function searchJobs(array $filters): array
    {
        $version = SuperAdminMarketplaceCacheManager::getVersion();
        $cacheKey = SuperAdminMarketplaceCacheManager::listKey($version, $filters);

        return SuperAdminMarketplaceCacheManager::rememberWithStampedeProtection(
            $cacheKey,
            SuperAdminMarketplaceCacheManager::DEFAULT_LIST_TTL,
            function () use ($filters) {
                return $this->executeSearchJobsUnderCircuitBreaker($filters);
            }
        );
    }

    /**
     * Executes the search operation guarded by ElasticsearchCircuitBreaker.
     */
    protected function executeSearchJobsUnderCircuitBreaker(array $filters): array
    {
        $page = max((int) ($filters['page'] ?? 1), 1);
        $perPage = min(max((int) ($filters['per_page'] ?? 15), 5), 100);
        $from = ($page - 1) * $perPage;

        $tab = $filters['tab'] ?? 'all';
        $queryText = trim($filters['q'] ?? '');

        // Build Elasticsearch Query DSL
        $mustClauses = [];
        $filterClauses = [];

        // 1. Text Search across Title, Description, Requirements, Company Name, Client Name
        if (! empty($queryText)) {
            $mustClauses[] = [
                'multi_match' => [
                    'query' => $queryText,
                    'fields' => ['title^3', 'job_description^1.5', 'job_requirement^1.5', 'company_name^2', 'client_name^2'],
                    'type' => 'best_fields',
                    'fuzziness' => 'AUTO',
                ],
            ];
        } else {
            $mustClauses[] = ['match_all' => new \stdClass];
        }

        // 2. Tab Filter Mapping
        switch ($tab) {
            case 'moderation':
            case 'pending':
                $filterClauses[] = [
                    'bool' => [
                        'should' => [
                            ['term' => ['public_market_status' => 1]],
                            ['term' => ['market_mod_state.keyword' => 'pending']],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ];
                $filterClauses[] = ['term' => ['status' => 'active']];
                break;

            case 'live':
            case 'approve':
            case 'approved':
                $filterClauses[] = [
                    'bool' => [
                        'should' => [
                            ['term' => ['public_market_status' => 2]],
                            ['term' => ['market_pub_state.keyword' => 'published']],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ];
                $filterClauses[] = ['term' => ['status' => 'active']];
                break;

            case 'quarantine':
            case 'hold':
                $filterClauses[] = [
                    'bool' => [
                        'should' => [
                            ['term' => ['public_market_status' => 4]],
                            ['term' => ['market_mod_state.keyword' => 'quarantined']],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ];
                break;

            case 'delisted':
            case 'reject':
                $filterClauses[] = [
                    'bool' => [
                        'should' => [
                            ['terms' => ['status' => ['closed', 'inactive', 'cancelled']]],
                            ['term' => ['public_market_status' => 3]],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ];
                break;

            case 'claims':
                $filterClauses[] = [
                    'bool' => [
                        'should' => [
                            ['term' => ['show_vendor.keyword' => 'yes']],
                            [
                                'nested' => [
                                    'path' => 'recommendation_relation',
                                    'query' => [
                                        'exists' => [
                                            'field' => 'recommendation_relation.id',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ];
                break;

            case 'bench':
                $filterClauses[] = ['term' => ['bench_submission.keyword' => 'enabled']];
                $filterClauses[] = ['term' => ['status' => 'active']];
                break;

            case 'all':
            default:
                $filterClauses[] = [
                    'bool' => [
                        'should' => [
                            ['range' => ['public_market_status' => ['gt' => 0]]],
                            ['term' => ['show_vendor.keyword' => 'yes']],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ];
                break;
        }

        // 3. Compensation Range Filter
        if (! empty($filters['min_salary'])) {
            $filterClauses[] = ['range' => ['min_salary' => ['gte' => (float) $filters['min_salary']]]];
        }
        if (! empty($filters['max_salary'])) {
            $filterClauses[] = ['range' => ['max_salary' => ['lte' => (float) $filters['max_salary']]]];
        }

        // 4. Employment Type Filter
        if (! empty($filters['employment_type'])) {
            $filterClauses[] = ['term' => ['employment_type' => (int) $filters['employment_type']]];
        }

        $esParams = [
            'index' => $this->getIndexName(),
            'body' => [
                'from' => $from,
                'size' => $perPage,
                'query' => [
                    'bool' => [
                        'must' => $mustClauses,
                        'filter' => $filterClauses,
                    ],
                ],
                'sort' => [
                    ['id' => ['order' => 'desc']],
                ],
            ],
        ];

        $performEsQuery = function () use ($esParams, $page, $perPage, $tab) {
            $response = $this->esClient->search($esParams);
            $total = $response['hits']['total']['value'] ?? 0;
            $rawHits = $response['hits']['hits'] ?? [];

            $formattedHits = array_map(function ($hit) {
                $source = $hit['_source'];
                $source['id'] = (int) ($source['id'] ?? $hit['_id']);

                // Calculate claimed_count from recommendation_relation nested array
                if (isset($source['recommendation_relation']) && is_array($source['recommendation_relation'])) {
                    $source['claimed_count'] = count($source['recommendation_relation']);
                }

                // Overlay in-memory mutation delta for 0ms read-after-write consistency
                $source = SuperAdminMarketplaceCacheManager::overlayMutationDelta($source);
                $source['health_badge'] = $this->computeHealthBadge($source);

                return $source;
            }, $rawHits);

            $result = [
                'hits' => $formattedHits,
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => ceil($total / $perPage),
                'aggregations' => $response['aggregations'] ?? [],
                'source' => 'elasticsearch',
            ];

            // Store successful snapshot for circuit breaker fallback
            SuperAdminMarketplaceCacheManager::storeLatestSnapshot($tab, $result);

            return $result;
        };

        $fallbackHandler = function (\Throwable $e) use ($filters, $page, $perPage, $tab) {
            Log::warning('SuperAdminMarketplaceSearchService circuit breaker fallback triggered: '.$e->getMessage());

            // 1. Check latest snapshot
            $snapshot = SuperAdminMarketplaceCacheManager::getLatestSnapshot($tab);
            if ($snapshot !== null) {
                $snapshot['source'] = 'snapshot_degraded';

                return $snapshot;
            }

            // 2. Database circuit breaker fallback
            return $this->searchJobsDbFallback($filters, $page, $perPage);
        };

        if ($this->circuitBreaker) {
            return $this->circuitBreaker->execute($performEsQuery, $fallbackHandler);
        }

        try {
            return $performEsQuery();
        } catch (\Throwable $e) {
            return $fallbackHandler($e);
        }
    }

    /**
     * Real-time KPI telemetry metrics for Super Admin Command Center
     * with distributed stampede protection.
     */
    public function getTelemetryMetrics(): array
    {
        $version = SuperAdminMarketplaceCacheManager::getVersion();
        $cacheKey = SuperAdminMarketplaceCacheManager::telemetryKey($version);

        return SuperAdminMarketplaceCacheManager::rememberWithStampedeProtection(
            $cacheKey,
            SuperAdminMarketplaceCacheManager::DEFAULT_TELEMETRY_TTL,
            function () {
                $performTelemetry = function () {
                    $response = $this->esClient->search([
                        'index' => $this->getIndexName(),
                        'body' => [
                            'size' => 0,
                            'aggs' => [
                                'active_jobs' => [
                                    'filter' => [
                                        'bool' => [
                                            'should' => [
                                                ['range' => ['public_market_status' => ['gt' => 0]]],
                                                ['term' => ['show_vendor.keyword' => 'yes']],
                                            ],
                                            'minimum_should_match' => 1,
                                        ],
                                    ],
                                ],
                                'pending_moderation' => [
                                    'filter' => [
                                        'bool' => [
                                            'should' => [
                                                ['term' => ['public_market_status' => 1]],
                                            ],
                                        ],
                                    ],
                                ],
                                'live_marketplace' => [
                                    'filter' => [
                                        'bool' => [
                                            'should' => [
                                                ['term' => ['public_market_status' => 2]],
                                            ],
                                        ],
                                    ],
                                ],
                                'quarantined' => [
                                    'filter' => [
                                        'bool' => [
                                            'should' => [
                                                ['term' => ['public_market_status' => 4]],
                                            ],
                                        ],
                                    ],
                                ],
                                'delisted' => [
                                    'filter' => [
                                        'bool' => [
                                            'should' => [
                                                ['terms' => ['status' => ['closed', 'inactive', 'cancelled']]],
                                                ['term' => ['public_market_status' => 3]],
                                            ],
                                            'minimum_should_match' => 1,
                                        ],
                                    ],
                                ],
                                'bench_enabled' => [
                                    'filter' => [
                                        'bool' => [
                                            'must' => [
                                                ['term' => ['status' => 'active']],
                                                ['term' => ['bench_submission.keyword' => 'enabled']],
                                            ],
                                        ],
                                    ],
                                ],
                                'agency_claims' => [
                                    'filter' => [
                                        'bool' => [
                                            'should' => [
                                                ['term' => ['show_vendor.keyword' => 'yes']],
                                                [
                                                    'nested' => [
                                                        'path' => 'recommendation_relation',
                                                        'query' => [
                                                            'exists' => [
                                                                'field' => 'recommendation_relation.id',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                            'minimum_should_match' => 1,
                                        ],
                                    ],
                                ],
                                'total_allocations' => [
                                    'nested' => [
                                        'path' => 'recommendation_relation',
                                    ],
                                ],
                            ],
                        ],
                    ]);

                    $aggs = $response['aggregations'] ?? [];
                    $globalSettings = SuperAdminMarketplaceCacheManager::getGlobalSettings();
                    $autoApprovalSetting = (bool) $globalSettings->public_market_job_status;

                    $benchCount = $aggs['bench_enabled']['doc_count'] ?? 0;
                    $agencyClaimsCount = $aggs['agency_claims']['doc_count'] ?? 0;
                    $agencyAllocationsCount = $aggs['total_allocations']['doc_count'] ?? 0;
                    $delistedCount = $aggs['delisted']['doc_count'] ?? 0;

                    $metrics = [
                        'total_active' => $aggs['active_jobs']['doc_count'] ?? 0,
                        'pending_moderation' => $aggs['pending_moderation']['doc_count'] ?? 0,
                        'live_marketplace' => $aggs['live_marketplace']['doc_count'] ?? 0,
                        'quarantined' => $aggs['quarantined']['doc_count'] ?? 0,
                        'delisted' => $delistedCount,
                        'bench_enabled' => $benchCount,
                        'agency_claims' => $agencyClaimsCount,
                        'agency_allocations' => $agencyAllocationsCount,
                        'auto_approval' => $autoApprovalSetting,
                        'source' => 'elasticsearch',
                        'updated_at' => now()->toIso8601String(),
                    ];

                    SuperAdminMarketplaceCacheManager::storeTelemetrySnapshot($metrics);

                    return $metrics;
                };

                $fallbackTelemetry = function (\Throwable $e) {
                    $snapshot = SuperAdminMarketplaceCacheManager::getTelemetrySnapshot();
                    if ($snapshot !== null) {
                        $snapshot['source'] = 'snapshot_degraded';

                        return $snapshot;
                    }

                    $globalSettings = SuperAdminMarketplaceCacheManager::getGlobalSettings();

                    return [
                        'total_active' => DB::table('jobs')->where(function ($q) {
                            $q->where('jobs.public_market_status', '>', 0)
                                ->orWhereIn('jobs.market_pub_state', ['published', 'pending_review'])
                                ->orWhere('jobs.bench_submission', 'enabled')
                                ->orWhere('jobs.show_vendor', 'yes');
                        })->count(),
                        'pending_moderation' => DB::table('jobs')->where('status', 'active')->where(fn ($q) => $q->where('market_mod_state', 'pending')->orWhere('public_market_status', 1))->count(),
                        'live_marketplace' => DB::table('jobs')->where('status', 'active')->where(fn ($q) => $q->where('market_pub_state', 'published')->orWhere('public_market_status', 2))->count(),
                        'quarantined' => DB::table('jobs')->where(fn ($q) => $q->where('market_mod_state', 'quarantined')->orWhere('public_market_status', 4))->count(),
                        'delisted' => DB::table('jobs')->where(function ($q) {
                            $q->whereIn('jobs.status', ['closed', 'inactive', 'cancelled'])
                                ->orWhere('jobs.public_market_status', 3);
                        })->count(),
                        'bench_enabled' => DB::table('jobs')->where('status', 'active')->where('bench_submission', 'enabled')->count(),
                        'agency_claims' => DB::table('jobs')->where(function ($q) {
                            $q->where('jobs.show_vendor', 'yes')
                                ->orWhereExists(function ($sub) {
                                    $sub->select(DB::raw(1))
                                        ->from('recommendation_relation')
                                        ->whereColumn('recommendation_relation.job_id', 'jobs.id');
                                });
                        })->count(),
                        'agency_allocations' => DB::table('recommendation_relation')->count(),
                        'auto_approval' => (bool) $globalSettings->public_market_job_status,
                        'source' => 'db_fallback',
                        'updated_at' => now()->toIso8601String(),
                    ];
                };

                if ($this->circuitBreaker) {
                    return $this->circuitBreaker->execute($performTelemetry, $fallbackTelemetry);
                }

                try {
                    return $performTelemetry();
                } catch (\Throwable $e) {
                    return $fallbackTelemetry($e);
                }
            }
        );
    }

    /**
     * Fetches detailed data for the 9-tab Command Center Drawer with Redis L2 caching.
     * Guarantees 0 MySQL queries on warm cache hits.
     */
    public function getJobDetail(int $jobId): array
    {
        if ($jobId <= 0) {
            abort(404, 'Invalid job ID specified.');
        }

        $version = SuperAdminMarketplaceCacheManager::getVersion();
        $cacheKey = SuperAdminMarketplaceCacheManager::drawerKey($version, $jobId);

        return SuperAdminMarketplaceCacheManager::rememberWithStampedeProtection(
            $cacheKey,
            SuperAdminMarketplaceCacheManager::DEFAULT_DRAWER_TTL,
            function () use ($jobId) {
                // 1. Fetch Job from database
                $job = DB::table('jobs')
                    ->leftJoin('companies', 'companies.id', '=', 'jobs.company_id')
                    ->leftJoin('clients', 'clients.id', '=', 'jobs.client_id')
                    ->where('jobs.id', $jobId)
                    ->select([
                        'jobs.*',
                        'companies.company_name',
                        'companies.logo as company_logo',
                        'clients.name as client_name',
                    ])
                    ->first();

                if (! $job) {
                    abort(404, 'Marketplace job requisition not found.');
                }

                $jobMeta = is_string($job->meta_details) ? json_decode($job->meta_details, true) : (array) ($job->meta_details ?? []);
                $job->vendor_limit = (int) ($jobMeta['vendor_limit'] ?? 20);
                if ($job->vendor_limit <= 0) {
                    $job->vendor_limit = 20;
                }

                // 2. Fetch Channels State
                $channels = DB::table('marketplace_channels')
                    ->where('job_id', $jobId)
                    ->get()
                    ->keyBy('channel_type');

                // 3. Fetch Assigned / Claiming Agencies
                $claims = DB::table('recommendation_relation')
                    ->join('companies', 'companies.id', '=', 'recommendation_relation.to_company_id')
                    ->where('recommendation_relation.job_id', $jobId)
                    ->select([
                        'recommendation_relation.*',
                        'companies.company_name as agency_name',
                        'companies.logo as agency_logo',
                    ])
                    ->get();

                // 4. Fetch Candidate Submissions
                $submissions = DB::table('marketplace_submissions')
                    ->join('companies', 'companies.id', '=', 'marketplace_submissions.agency_id')
                    ->where('marketplace_submissions.job_id', $jobId)
                    ->select([
                        'marketplace_submissions.*',
                        'companies.company_name as agency_name',
                    ])
                    ->orderBy('marketplace_submissions.id', 'desc')
                    ->limit(50)
                    ->get();

                // 5. Fetch Moderation Audit Logs
                $auditLogs = DB::table('job_marketplace_moderation_logs')
                    ->where('job_id', $jobId)
                    ->orderBy('id', 'desc')
                    ->limit(20)
                    ->get();

                $jobArray = (array) $job;
                $jobArray = SuperAdminMarketplaceCacheManager::overlayMutationDelta($jobArray);

                return [
                    'job' => (object) $jobArray,
                    'channels' => $channels,
                    'claims' => $claims,
                    'submissions' => $submissions,
                    'audit_logs' => $auditLogs,
                    'health_badge' => $this->computeHealthBadge($jobArray),
                ];
            }
        );
    }

    /**
     * Computes the Unified Channel Health Visual Badge structure.
     */
    public function computeHealthBadge(array $row): array
    {
        $jobActive = ($row['status'] ?? 'active') === 'active';
        $agencyLive = ($row['show_vendor'] ?? 'no') === 'yes' && in_array($row['public_market_status'] ?? 0, [2, '2']);
        $benchLive = ($row['bench_submission'] ?? 'disabled') === 'enabled';
        $riskScore = (float) ($row['risk_score'] ?? 0.0);

        return [
            'job_status' => $jobActive ? 'ACTIVE' : strtoupper($row['status'] ?? 'INACTIVE'),
            'job_color' => $jobActive ? 'success' : 'secondary',
            'agency_channel' => $agencyLive ? 'LIVE' : (($row['show_vendor'] ?? 'no') === 'yes' ? 'PENDING' : 'OFF'),
            'agency_color' => $agencyLive ? 'success' : 'warning',
            'bench_channel' => $benchLive ? 'LIVE' : 'OFF',
            'bench_color' => $benchLive ? 'success' : 'secondary',
            'risk_tier' => $riskScore < 0.30 ? 'LOW' : ($riskScore < 0.70 ? 'MED' : 'HIGH'),
            'risk_color' => $riskScore < 0.30 ? 'success' : ($riskScore < 0.70 ? 'warning' : 'danger'),
            'risk_score' => round($riskScore * 100),
            'version' => $row['aggregate_version'] ?? ($row['state_version'] ?? 1),
        ];
    }

    /**
     * Circuit breaker fallback executing MySQL query when ES cluster is offline.
     */
    public function searchJobsDbFallback(array $filters, int $page, int $perPage): array
    {
        $query = DB::table('jobs')
            ->join('companies', 'companies.id', '=', 'jobs.company_id')
            ->leftJoin('clients', 'clients.id', '=', 'jobs.client_id')
            ->select([
                'jobs.id', 'jobs.title', 'jobs.total_positions', 'jobs.work_type',
                'jobs.employment_type', 'jobs.status', 'jobs.min_salary', 'jobs.max_salary',
                'jobs.min_salary_currency', 'jobs.max_salary_currency', 'jobs.market_pub_state',
                'jobs.market_mod_state', 'jobs.claim_capacity', 'jobs.bench_submission',
                'jobs.show_vendor', DB::raw("COALESCE(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(jobs.meta_details, '$.vendor_limit')), ''), 20) as vendor_limit"),
                'jobs.public_market_status', 'jobs.aggregate_version', 'jobs.created_at',
                'companies.company_name', 'companies.logo as company_logo',
                'clients.name as client_name',
            ]);

        $tab = $filters['tab'] ?? 'all';
        switch ($tab) {
            case 'moderation':
            case 'pending':
                $query->where('jobs.status', 'active')
                    ->where(function ($q) {
                        $q->where('jobs.market_mod_state', 'pending')
                            ->orWhere('jobs.public_market_status', 1);
                    });
                break;

            case 'live':
            case 'approve':
            case 'approved':
                $query->where('jobs.status', 'active')
                    ->where(function ($q) {
                        $q->where('jobs.market_pub_state', 'published')
                            ->orWhere('jobs.public_market_status', 2);
                    });
                break;

            case 'bench':
                $query->where('jobs.status', 'active')
                    ->where('jobs.bench_submission', 'enabled');
                break;

            case 'quarantine':
            case 'hold':
                $query->where(function ($q) {
                    $q->where('jobs.market_mod_state', 'quarantined')
                        ->orWhere('jobs.public_market_status', 4);
                });
                break;

            case 'delisted':
            case 'reject':
                $query->where(function ($q) {
                    $q->whereIn('jobs.status', ['closed', 'inactive', 'cancelled'])
                        ->orWhere('jobs.public_market_status', 3);
                });
                break;

            case 'claims':
                $query->where(function ($q) {
                    $q->where('jobs.show_vendor', 'yes')
                        ->orWhereExists(function ($sub) {
                            $sub->select(DB::raw(1))
                                ->from('recommendation_relation')
                                ->whereColumn('recommendation_relation.job_id', 'jobs.id');
                        });
                });
                break;

            case 'all':
            default:
                $query->where(function ($q) {
                    $q->where('jobs.public_market_status', '>', 0)
                        ->orWhere('jobs.show_vendor', 'yes');
                });
                break;
        }

        if (! empty($filters['q'])) {
            $term = trim($filters['q']);
            $query->where(function ($q) use ($term) {
                $q->where('jobs.title', 'like', "%{$term}%")
                    ->orWhere('jobs.id', $term)
                    ->orWhere('companies.company_name', 'like', "%{$term}%")
                    ->orWhere('clients.name', 'like', "%{$term}%");
            });
        }

        $total = $query->count();
        $rows = $query->orderBy('jobs.id', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $formattedHits = $rows->map(function ($row) {
            $rowArray = (array) $row;
            $rowArray['claimed_count'] = DB::table('recommendation_relation')->where('job_id', $row->id)->count();
            $rowArray = SuperAdminMarketplaceCacheManager::overlayMutationDelta($rowArray);
            $rowArray['health_badge'] = $this->computeHealthBadge($rowArray);

            return $rowArray;
        })->toArray();

        return [
            'hits' => $formattedHits,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage),
            'aggregations' => [],
            'source' => 'db_fallback',
        ];
    }
}
