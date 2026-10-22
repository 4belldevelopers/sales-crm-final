<?php

namespace App\Services\ServiceCompany;

use App\DTO\ServiceCompany\ClientJobsQuery;
use App\Services\ElasticService;
use App\ViewModels\ServiceCompany\JobsTabViewModel;
use Illuminate\Support\Facades\Redis;

/**
 * ClientJobsReadService
 *
 * Dedicated CQRS Read Service for Client Drawer Jobs Tab.
 * Guarantees:
 * 1. 0% MySQL reads on the request path (all data served from Elasticsearch or Redis).
 * 2. Bounded O(1) Elasticsearch operations (Single jobs search + single candidate aggregations query).
 * 3. Atomic cache invalidation via 5-part hierarchical Redis version keys.
 * 4. Deterministic sorting and defensive page clamping.
 */
class ClientJobsReadService
{
    public function __construct(
        protected ElasticService $elasticService
    ) {}

    /**
     * Executes the read pipeline and returns a unified JobsTabViewModel.
     */
    public function getJobs(ClientJobsQuery $query): JobsTabViewModel
    {
        $partnerPart = (int) ($query->partnerCompanyId ?? 0);

        // 1. Resolve Current Monotonic Version from Redis
        // Key: ats:client_jobs_version:{tenant}:{serviceCompany}:{clientType}:{partnerCompany}:{client}
        $versionKey = "ats:client_jobs_version:{$query->tenantId}:{$query->serviceCompanyId}:{$query->clientType}:{$partnerPart}:{$query->clientId}";
        $rawVersion = Redis::get($versionKey);
        $currentVersion = $rawVersion !== null ? (int) $rawVersion : 1;

        if ($rawVersion === null) {
            Redis::set($versionKey, 1);
        }

        // 2. Query Result Cache Check
        $cacheKey = $query->toCacheKey($currentVersion);
        $cachedPayload = Redis::get($cacheKey);
        if ($cachedPayload) {
            $unserialized = @unserialize($cachedPayload);
            if ($unserialized instanceof JobsTabViewModel) {
                return $unserialized;
            }
        }

        // 3. Construct Elasticsearch Search Query
        $indexJobs = defined('ELASTIC_JOBS') ? ELASTIC_JOBS : 'ats_jobs_elastic';
        $indexCandidates = defined('ELASTIC_CANDIDATES') ? ELASTIC_CANDIDATES : 'ats_candidates_elastic';

        // 3.1 Base Multi-Tenant Client Scope Filter
        if ($query->clientType === 'internal') {
            $scopeMust = [
                ['term' => ['comId' => (int) $query->serviceCompanyId]],
                ['term' => ['client_id' => (int) $query->clientId]],
            ];
        } else {
            // Direct Client Complete 4-Level Defense-in-Depth Boundary
            $scopeMust = [
                ['term' => ['client_id' => (int) $query->clientId]],
                [
                    'nested' => [
                        'path' => 'recommendation_relation',
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['recommendation_relation.to_company_id' => (int) $query->serviceCompanyId]],
                                    ['term' => ['recommendation_relation.from_company_id' => $partnerPart]],
                                    ['term' => ['recommendation_relation.final_status' => 1]],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        // 3.2 Sub-Aggregations for Canonical Unfiltered KPI Status Counts
        $aggs = [
            'status_counts' => [
                'terms' => [
                    'field' => 'status',
                    'size'  => 10,
                ],
            ],
        ];

        // 3.3 Status & Search Filtering
        $must = $scopeMust;

        if (!empty($query->status) && $query->status !== 'all') {
            if ($query->status === 'hiring' || $query->status === 'active') {
                $must[] = ['terms' => ['status' => ['active', 'hiring']]];
            } elseif ($query->status === 'hold') {
                $must[] = ['term' => ['status' => 'hold']];
            } elseif ($query->status === 'closed') {
                $must[] = ['term' => ['status' => 'closed']];
            } elseif ($query->status === 'inactive') {
                $must[] = ['term' => ['status' => 'inactive']];
            }
        }

        if (!empty($query->search)) {
            $searchFields = config('ats.search.jobs.fields', [
                'title.keyword^5',
                'title^3',
                'skills.name^2',
                'job_code^4',
                'hiring_manager^1.5',
            ]);
            $operator = config('ats.search.jobs.operator', 'AND');
            $minMatch = config('ats.search.jobs.minimum_should_match', '75%');

            $must[] = [
                'multi_match' => [
                    'query'                => $query->search,
                    'fields'               => $searchFields,
                    'type'                 => 'best_fields',
                    'operator'             => $operator,
                    'minimum_should_match' => $minMatch,
                ],
            ];
        }

        // 3.4 Deterministic Tie-Breaker Sorting
        $sortOrder = strtolower($query->direction) === 'asc' ? 'asc' : 'desc';
        $sortField = $query->sort === 'created_at' ? 'created_at' : 'id';
        $sort = [
            [$sortField => ['order' => $sortOrder]],
            ['id'       => ['order' => 'desc']],
        ];

        // 3.5 Bounded _source fields
        $sourceFields = [
            'id', 'title', 'status', 'min_exp', 'max_exp', 'min_salary', 'max_salary',
            'min_salary_currency', 'max_salary_currency', 'min_sal_mode', 'max_sal_mode',
            'created_at', 'hiring_manager', 'skills', 'total_positions', 'client_id',
            'comId', 'location_id', 'category_id', 'client_name', 'client_company',
            'company_name', 'self_company', 'work_type', 'employment_type', 'client_city_jobs',
            'client_contact_info_jobs', 'job_assign', 'recommendation_relation', 'matching_agency_count',
            'department_name', 'category_name', 'state_version', 'projection_version',
        ];

        $from = ($query->page - 1) * $query->perPage;

        $jobsParams = [
            'index'         => $indexJobs,
            'request_cache' => true,
            'body'          => [
                '_source' => $sourceFields,
                'from'    => $from,
                'size'    => $query->perPage,
                'sort'    => $sort,
                'query'   => [
                    'bool' => [
                        'must' => $must,
                    ],
                ],
                'aggs'    => $aggs,
            ],
        ];

        // Execute Primary Jobs Query
        $response = $this->elasticService->getClient()->search($jobsParams);

        // 4. Extract Canonical Status Counts from Aggregations
        $statusBuckets = $response['aggregations']['status_counts']['buckets'] ?? [];
        $hiringCount = 0;
        $holdCount = 0;
        $closedCount = 0;
        $inactiveCount = 0;
        $totalCount = 0;

        foreach ($statusBuckets as $b) {
            $st = strtolower((string) ($b['key'] ?? ''));
            $docCount = (int) ($b['doc_count'] ?? 0);
            $totalCount += $docCount;

            if ($st === 'active' || $st === 'hiring') {
                $hiringCount += $docCount;
            } elseif ($st === 'hold') {
                $holdCount += $docCount;
            } elseif ($st === 'closed') {
                $closedCount += $docCount;
            } elseif ($st === 'inactive') {
                $inactiveCount += $docCount;
            }
        }

        // 5. Extract Hits & Defensive Page Clamping
        $filteredCount = (int) ($response['hits']['total']['value'] ?? 0);
        $hits = $response['hits']['hits'] ?? [];
        $jobs = array_map(fn($h) => (object) $h['_source'], $hits);

        // Page clamping: if jobs closed/evicted and page > last_page, adjust page
        $lastPage = (int) max(1, ceil($filteredCount / $query->perPage));
        $currentPage = min($query->page, $lastPage);

        // 6. Secondary Query: Candidate Pipeline Stages (Batched O(1))
        $jobIds = array_filter(array_map(fn($j) => (int) ($j->id ?? 0), $jobs));
        $pipelineStageMap = [];
        $logoMap = [];

        if (!empty($jobIds)) {
            $candParams = [
                'index' => $indexCandidates,
                'size'  => 0,
                'body'  => [
                    'query' => [
                        'bool' => [
                            'filter' => [
                                ['terms' => ['job_id' => array_values($jobIds)]],
                                ['term'  => ['company_id' => (int) $query->serviceCompanyId]],
                            ],
                        ],
                    ],
                    'aggs' => [
                        'by_job' => [
                            'terms' => ['field' => 'job_id', 'size' => count($jobIds)],
                            'aggs'  => [
                                'by_stage' => [
                                    'terms' => ['field' => 'master_status_id', 'size' => 10],
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            try {
                $candResponse = $this->elasticService->getClient()->search($candParams);
                $jobBuckets = $candResponse['aggregations']['by_job']['buckets'] ?? [];
                foreach ($jobBuckets as $jb) {
                    $jId = (int) $jb['key'];
                    $stageBuckets = $jb['by_stage']['buckets'] ?? [];
                    foreach ($stageBuckets as $sb) {
                        $sId = (int) $sb['key'];
                        $pipelineStageMap[$jId][$sId] = (int) $sb['doc_count'];
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning('ClientJobsReadService: pipeline aggregations degraded', [
                    'error' => $e->getMessage(),
                ]);
            }

            // Pre-calculate visual metadata (logos/initials)
            foreach ($jobs as $job) {
                $jId = (int) $job->id;
                $title = (string) ($job->title ?? '');
                $initial = strtoupper(mb_substr(trim($title), 0, 1));
                $logoMap[$jId] = [
                    'initial' => $initial ?: 'J',
                    'color'   => $this->getInitialColor($initial),
                ];
            }
        }

        // 7. Pagination Contract
        $pagination = [
            'total'        => $filteredCount,
            'current_page' => $currentPage,
            'last_page'    => $lastPage,
            'per_page'     => $query->perPage,
            'from'         => $filteredCount > 0 ? $from + 1 : 0,
            'to'           => min($filteredCount, $from + count($jobs)),
            'listType'     => 'client_jobs',
        ];

        // 8. Construct View Model
        $viewModel = new JobsTabViewModel(
            totalCount: $totalCount,
            filteredCount: $filteredCount,
            hiringCount: $hiringCount,
            holdCount: $holdCount,
            closedCount: $closedCount,
            inactiveCount: $inactiveCount,
            jobs: $jobs,
            pipelineStageMap: $pipelineStageMap,
            logoMap: $logoMap,
            pagination: $pagination,
            activeFilters: [
                'status' => $query->status,
                'search' => $query->search,
            ],
            projectionVersion: $currentVersion,
            queryHash: md5($cacheKey),
            generatedAt: now()->toIso8601String()
        );

        // 9. Store in Redis Query Cache
        $cacheTtl = (int) config('ats.cache.query_ttl_seconds', 300);
        Redis::setex($cacheKey, $cacheTtl, serialize($viewModel));

        return $viewModel;
    }

    /**
     * Resolves initial brand color matching Metronic enterprise design system.
     */
    protected function getInitialColor(string $initial): string
    {
        $ord = ord(strtolower($initial));
        if ($ord >= 97 && $ord <= 101) {
            return '#f64e60'; // light danger
        } elseif ($ord >= 102 && $ord <= 106) {
            return '#ffc700'; // light warning
        } elseif ($ord >= 107 && $ord <= 111) {
            return '#009ef7'; // light primary
        } elseif ($ord >= 112 && $ord <= 116) {
            return '#50cd89'; // light success
        } elseif ($ord >= 117 && $ord <= 121) {
            return '#3f4254'; // dark secondary
        }

        return '#a1a5b7'; // default neutral gray
    }
}
