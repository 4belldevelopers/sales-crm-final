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
        // Strict Backend Tenancy Guard: Super Admin must NOT receive or see agency-specific direct jobs
        if ($query->clientType === 'direct' && auth()->check()) {
            $user = auth()->user();
            $isSuperAdmin = (bool) ($user->is_superadmin ?? false)
                || in_array((int) ($user->company_id ?? 0), \App\Helper\GeneralMethod::superAdminCompanyIds(), true)
                || (method_exists($user, 'hasRole') && $user->hasRole('super_admin'))
                || (int) ($user->company_id ?? 0) === 1;

            if ($isSuperAdmin) {
                return new JobsTabViewModel(
                    totalCount: 0,
                    filteredCount: 0,
                    hiringCount: 0,
                    holdCount: 0,
                    closedCount: 0,
                    inactiveCount: 0,
                    jobs: [],
                    pipelineStageMap: [],
                    logoMap: [],
                    pagination: ['total' => 0, 'page' => 1, 'last_page' => 1, 'per_page' => $query->perPage, 'from' => 0, 'to' => 0],
                    activeFilters: ['status' => $query->status, 'search' => $query->search],
                    projectionVersion: 1,
                    queryHash: '',
                    generatedAt: now()->toIso8601String(),
                    tabs: [],
                    clientName: null,
                    clientType: 'direct'
                );
            }
        }

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
        } elseif ($query->clientType === 'agency') {
            // Agency Shared Jobs Boundary (Operating Company -> Agency)
            $agencyCompanyId = $partnerPart > 0 ? $partnerPart : (int) $query->clientId;
            $nestedMust = [
                ['term' => ['recommendation_relation.from_company_id' => (int) $query->serviceCompanyId]],
                ['terms' => ['recommendation_relation.final_status' => [1, 2]]],
            ];
            if ($agencyCompanyId > 0) {
                $nestedMust[] = ['term' => ['recommendation_relation.to_company_id' => $agencyCompanyId]];
            }

            $scopeMust = [
                [
                    'nested' => [
                        'path' => 'recommendation_relation',
                        'query' => [
                            'bool' => [
                                'must' => $nestedMust,
                            ],
                        ],
                    ],
                ],
            ];
        } else {
            // Direct Client Complete 4-Level Defense-in-Depth Boundary
            $fromCompId = $partnerPart > 0 ? $partnerPart : (int) $query->clientId;
            $nestedMust = [
                ['term' => ['recommendation_relation.to_company_id' => (int) $query->serviceCompanyId]],
                ['terms' => ['recommendation_relation.final_status' => [1, 2]]],
            ];
            if ($fromCompId > 0) {
                $nestedMust[] = ['term' => ['recommendation_relation.from_company_id' => $fromCompId]];
            }

            $scopeMust = [
                [
                    'nested' => [
                        'path' => 'recommendation_relation',
                        'query' => [
                            'bool' => [
                                'must' => $nestedMust,
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
                    'size' => 10,
                ],
            ],
        ];

        // 3.3 Status & Search Filtering
        $must = $scopeMust;

        if (! empty($query->status) && $query->status !== 'all') {
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

        if (! empty($query->search)) {
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
                    'query' => $query->search,
                    'fields' => $searchFields,
                    'type' => 'best_fields',
                    'operator' => $operator,
                    'minimum_should_match' => $minMatch,
                ],
            ];
        }

        // 3.4 Deterministic Tie-Breaker Sorting
        $sortOrder = strtolower($query->direction) === 'asc' ? 'asc' : 'desc';
        $sortField = $query->sort === 'created_at' ? 'created_at' : 'id';
        $sort = [
            [$sortField => ['order' => $sortOrder]],
            ['id' => ['order' => 'desc']],
        ];

        // 3.5 Bounded _source fields
        $sourceFields = [
            'id', 'title', 'status', 'min_exp', 'max_exp', 'min_salary', 'max_salary',
            'min_salary_currency', 'max_salary_currency', 'min_sal_mode', 'max_sal_mode',
            'created_at', 'hiring_manager', 'skills', 'total_positions', 'client_id',
            'comId', 'location_id', 'category_id', 'client_name', 'client_company',
            'company_name', 'self_company', 'work_type', 'employment_type', 'client_city_jobs',
            'client_contact_info_jobs', 'job_assign', 'recommendation_relation', 'matching_agency_count',
            'department_name', 'category_name', 'job_metronics_skills', 'logo', 'logo_logical_path', 'state_version', 'projection_version',
        ];

        $from = ($query->page - 1) * $query->perPage;

        $jobsParams = [
            'index' => $indexJobs,
            'request_cache' => true,
            'body' => [
                '_source' => $sourceFields,
                'from' => $from,
                'size' => $query->perPage,
                'sort' => $sort,
                'query' => [
                    'bool' => [
                        'must' => $must,
                    ],
                ],
                'aggs' => $aggs,
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
        $jobs = array_map(fn ($h) => (object) $h['_source'], $hits);

        // Page clamping: if jobs closed/evicted and page > last_page, adjust page
        $lastPage = (int) max(1, ceil($filteredCount / $query->perPage));
        $currentPage = min($query->page, $lastPage);

        // 6. Secondary Query: Candidate Pipeline Stages (Batched O(1))
        $jobIds = array_filter(array_map(fn ($j) => (int) ($j->id ?? 0), $jobs));
        $pipelineStageMap = [];
        $interviewMap = [];
        $logoMap = [];

        if (! empty($jobIds)) {
            $candFilter = [
                [
                    'bool' => [
                        'should' => [
                            ['terms' => ['jobId' => array_values($jobIds)]],
                            [
                                'bool' => [
                                    'must' => [['terms' => ['job_id' => array_values($jobIds)]]],
                                    'must_not' => [['exists' => ['field' => 'jobId']]],
                                ],
                            ],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ],
            ];

            if ($query->clientType === 'internal') {
                $candFilter[] = ['term' => ['company_id' => (int) $query->serviceCompanyId]];
                if ($query->clientId > 0) {
                    $candFilter[] = ['term' => ['client_id' => (int) $query->clientId]];
                }
            } elseif ($query->clientType === 'agency' || $query->clientType === 'direct') {
                $party1 = (int) $query->serviceCompanyId;
                $party2 = (int) ($partnerPart > 0 ? $partnerPart : $query->clientId);

                $partyScope = fn (int $cid) => [
                    'bool' => [
                        'should' => [
                            ['term' => ['company_id' => $cid]],
                            ['term' => ['from_company_id' => $cid]],
                            ['term' => ['to_company_id' => $cid]],
                            ['term' => ['ClientComID' => $cid]],
                            ['term' => ['thirdparty_company_id' => $cid]],
                            ['term' => ['agency_id' => $cid]],
                            ['term' => ['agency_company_id' => $cid]],
                            ['term' => ['job_company_id' => $cid]],
                            ['term' => ['client_comapny_id' => $cid]],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ];

                $candFilter[] = $partyScope($party1);
                $candFilter[] = $partyScope($party2);
            }

            // Rule: Internal client retains INW (status 8) in pipeline counts.
            // Direct client and Agency strictly exclude status 8 (In-Review) and status 9.
            if ($query->clientType === 'internal') {
                // Internal client: only exclude status 9 (Archived/Deleted) — INW (8) is a valid stage
                $mustNot = [
                    ['terms' => ['job_master_status_id' => [9, '9']]],
                    ['terms' => ['master_status_id' => [9, '9']]],
                ];
            } else {
                // Direct client & Agency: strictly exclude status 8 (In-Review) and 9
                $mustNot = [
                    ['terms' => ['job_master_status_id' => [8, 9, '8', '9']]],
                    ['terms' => ['master_status_id' => [8, 9, '8', '9']]],
                ];
            }

            $candParams = [
                'index' => $indexCandidates,
                'size' => 0,
                'body' => [
                    'query' => [
                        'bool' => [
                            'filter' => $candFilter,
                            'must_not' => $mustNot,
                        ],
                    ],
                    'aggs' => [
                        'by_job' => [
                            'terms' => ['field' => 'jobId', 'size' => max(10, count($jobIds))],
                            'aggs' => [
                                'by_job_stage' => [
                                    'terms' => ['field' => 'job_master_status_id', 'size' => 10],
                                ],
                                'by_master_stage' => [
                                    'terms' => ['field' => 'master_status_id', 'size' => 10],
                                ],
                                'with_interview' => [
                                    'filter' => [
                                        'bool' => [
                                            'should' => [
                                                ['exists' => ['field' => 'schedule_date']],
                                                ['exists' => ['field' => 'interviewDateOne']],
                                                ['terms' => ['job_master_status_id' => [3, 4]]],
                                                ['terms' => ['master_status_id' => [3, 4]]],
                                            ],
                                            'minimum_should_match' => 1,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'by_job_legacy' => [
                            'terms' => ['field' => 'job_id', 'size' => max(10, count($jobIds))],
                            'aggs' => [
                                'by_job_stage' => [
                                    'terms' => ['field' => 'job_master_status_id', 'size' => 10],
                                ],
                                'by_master_stage' => [
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
                $legacyBuckets = $candResponse['aggregations']['by_job_legacy']['buckets'] ?? [];

                $processedJobIds = [];
                // For internal client, status 8 (INW) is a valid pipeline stage and must be included.
                // For direct client and agency, status 8 (INW) must always be excluded.
                $isInternalPipeline = ($query->clientType === 'internal');

                foreach ($jobBuckets as $jb) {
                    $jId = (int) $jb['key'];
                    if ($jId <= 0) {
                        continue;
                    }
                    $processedJobIds[$jId] = true;
                    $interviewMap[$jId] = (int) ($jb['with_interview']['doc_count'] ?? 0);

                    $jobStageBuckets = $jb['by_job_stage']['buckets'] ?? [];
                    $masterStageBuckets = $jb['by_master_stage']['buckets'] ?? [];

                    $targetBuckets = ! empty($jobStageBuckets) ? $jobStageBuckets : $masterStageBuckets;
                    foreach ($targetBuckets as $sb) {
                        $sId = (int) $sb['key'];
                        // Always exclude status 9. For direct/agency, also exclude status 8 (INW).
                        if ($sId <= 0 || $sId === 9 || (! $isInternalPipeline && $sId === 8)) {
                            continue;
                        }
                        $pipelineStageMap[$jId][$sId] = ($pipelineStageMap[$jId][$sId] ?? 0) + (int) $sb['doc_count'];
                    }
                }

                // If any job was not in by_job, check by_job_legacy
                foreach ($legacyBuckets as $lb) {
                    $jId = (int) $lb['key'];
                    if ($jId <= 0 || ! empty($processedJobIds[$jId])) {
                        continue;
                    }
                    $processedJobIds[$jId] = true;

                    $jobStageBuckets = $lb['by_job_stage']['buckets'] ?? [];
                    $masterStageBuckets = $lb['by_master_stage']['buckets'] ?? [];

                    $targetBuckets = ! empty($jobStageBuckets) ? $jobStageBuckets : $masterStageBuckets;
                    foreach ($targetBuckets as $sb) {
                        $sId = (int) $sb['key'];
                        if ($sId <= 0 || $sId === 9 || (! $isInternalPipeline && $sId === 8)) {
                            continue;
                        }
                        $pipelineStageMap[$jId][$sId] = ($pipelineStageMap[$jId][$sId] ?? 0) + (int) $sb['doc_count'];
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning('ClientJobsReadService: pipeline aggregations degraded', [
                    'error' => $e->getMessage(),
                ]);
            }

            // Circuit-breaker DB fallback (Rules 1 & 2): if any job on page has 0 candidate counts,
            // populate from MySQL for agency and direct client types (not internal — ES handles INW for internal).
            if ($query->clientType === 'agency') {
                $agencyCompanyId = $partnerPart > 0 ? $partnerPart : (int) $query->clientId;
                foreach ($jobIds as $jId) {
                    if (empty($pipelineStageMap[$jId])) {
                        try {
                            $dbStages = \Illuminate\Support\Facades\DB::table('add_job_wise_condidates')
                                ->where('job_id', $jId)
                                ->where('company_id', (int) $query->serviceCompanyId)
                                ->where(function ($q) use ($agencyCompanyId) {
                                    $q->where('thirdparty_company_id', $agencyCompanyId)
                                      ->orWhere('ClientComID', $agencyCompanyId);
                                })
                                ->whereNotIn('master_status_id', [8, 9])
                                ->select('master_status_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as cnt'))
                                ->groupBy('master_status_id')
                                ->get();

                            foreach ($dbStages as $row) {
                                $sId = (int) $row->master_status_id;
                                if ($sId > 0 && $sId !== 8 && $sId !== 9) {
                                    $pipelineStageMap[$jId][$sId] = (int) $row->cnt;
                                }
                            }
                        } catch (\Throwable $e) {
                            // Defensive
                        }
                    }
                }
            } elseif ($query->clientType === 'direct') {
                // Direct client DB fallback: query job_applications scoped to service company (excludes status 8 & 9)
                $directClientId = $partnerPart > 0 ? $partnerPart : (int) $query->clientId;
                foreach ($jobIds as $jId) {
                    if (empty($pipelineStageMap[$jId])) {
                        try {
                            $dbStages = \Illuminate\Support\Facades\DB::table('job_applications')
                                ->where('job_id', $jId)
                                ->where('company_id', (int) $query->serviceCompanyId)
                                ->whereNotIn('master_status_id', [8, 9])
                                ->select('master_status_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as cnt'))
                                ->groupBy('master_status_id')
                                ->get();

                            foreach ($dbStages as $row) {
                                $sId = (int) $row->master_status_id;
                                if ($sId > 0 && $sId !== 8 && $sId !== 9) {
                                    $pipelineStageMap[$jId][$sId] = (int) $row->cnt;
                                }
                            }
                        } catch (\Throwable $e) {
                            // Defensive
                        }
                    }
                }
            }
            // Internal client: no DB fallback needed here — INW (status 8) counts come from ES pipeline above

            // Pre-calculate visual metadata (logos/initials)
            foreach ($jobs as $job) {
                $jId = (int) $job->id;
                $title = (string) ($job->title ?? '');
                $initial = strtoupper(mb_substr(trim($title), 0, 1));
                $logoMap[$jId] = [
                    'initial' => $initial ?: 'J',
                    'color' => '#ffffff',
                ];
            }
        }

        // 7. Pagination Contract
        $pagination = [
            'total' => $filteredCount,
            'current_page' => $currentPage,
            'last_page' => $lastPage,
            'per_page' => $query->perPage,
            'from' => $filteredCount > 0 ? $from + 1 : 0,
            'to' => min($filteredCount, $from + count($jobs)),
            'listType' => 'client_jobs',
        ];

        // 8. Resolve Authoritative Client Display Name (Strictly 0% MySQL Reads)
        $clientDisplayName = $query->clientName;
        if (empty($clientDisplayName) && $query->clientId > 0) {
            $clientNameKey = "ats:client_display_name:{$query->clientType}:{$query->clientId}";
            $clientDisplayName = Redis::get($clientNameKey);
            if (empty($clientDisplayName)) {
                try {
                    if ($query->clientType === 'internal') {
                        $indexClient = defined('ELASTIC_CLIENT_LIST') ? ELASTIC_CLIENT_LIST : config('constants.ELASTIC_CLIENT_LIST', 'ats_client_list_elastic');
                        $clientDoc = $this->elasticService->getClient()->get([
                            'index' => $indexClient,
                            'id' => (string) $query->clientId,
                        ]);
                        $clientDisplayName = $clientDoc['_source']['name'] ?? ($clientDoc['_source']['client_name'] ?? '');
                    } else {
                        $indexClient = defined('ELASTIC_CLIENT_LIST') ? ELASTIC_CLIENT_LIST : 'ats_client_list_elastic';
                        if ($this->elasticService->getClient()->exists(['index' => $indexClient, 'id' => (string) $query->clientId])) {
                            $compDoc = $this->elasticService->getClient()->get([
                                'index' => $indexClient,
                                'id' => (string) $query->clientId,
                            ]);
                            $clientDisplayName = $compDoc['_source']['company_name'] ?? ($compDoc['_source']['name'] ?? '');
                        } else {
                            $indexComp = defined('ELASTIC_COMPANIES_INDEX') ? ELASTIC_COMPANIES_INDEX : config('constants.ELASTIC_COMPANIES_INDEX', 'ats_companies_elastic');
                            if ($this->elasticService->getClient()->exists(['index' => $indexComp, 'id' => (string) $query->clientId])) {
                                $compDoc = $this->elasticService->getClient()->get([
                                    'index' => $indexComp,
                                    'id' => (string) $query->clientId,
                                ]);
                                $clientDisplayName = $compDoc['_source']['company_name'] ?? ($compDoc['_source']['name'] ?? '');
                            }
                        }
                    }
                    if (! empty($clientDisplayName)) {
                        Redis::setex($clientNameKey, 3600, (string) $clientDisplayName);
                    }
                } catch (\Throwable $e) {
                    // Graceful fallback with zero MySQL reads
                }
            }
        }

        // Attach resolved client name and enforce strict internal client masking
        if ($query->clientType === 'direct') {
            // Requirement 2: Direct Client internal clients (e.g. Mahindra) MUST NEVER be shown to an Agency viewer!
            // Strictly override client_name and company_name with Direct Client identity.
            foreach ($jobs as $job) {
                if (! empty($clientDisplayName)) {
                    $job->client_name = $clientDisplayName;
                    $job->company_name = $clientDisplayName;
                }
            }
        } else {
            foreach ($jobs as $job) {
                if (empty($job->client_name) && ! empty($clientDisplayName)) {
                    $job->client_name = $clientDisplayName;
                }
            }
        }

        // 9. Assemble Fully Dynamic Status Tabs
        $dynamicTabs = [
            [
                'key' => 'all',
                'label' => 'All Jobs',
                'count' => $totalCount,
                'badge_cls' => 'count-primary',
            ],
            [
                'key' => 'hiring',
                'label' => 'Hiring',
                'count' => $hiringCount,
                'badge_cls' => 'count-success',
            ],
            [
                'key' => 'hold',
                'label' => 'On Hold',
                'count' => $holdCount,
                'badge_cls' => 'count-warning',
            ],
            [
                'key' => 'closed',
                'label' => 'Closed',
                'count' => $closedCount,
                'badge_cls' => 'count-danger',
            ],
            [
                'key' => 'inactive',
                'label' => 'Inactive',
                'count' => $inactiveCount,
                'badge_cls' => 'count-neutral',
            ],
        ];

        // 10. Construct View Model
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
            tabs: $dynamicTabs,
            projectionVersion: $currentVersion,
            queryHash: md5($cacheKey),
            generatedAt: now()->toIso8601String(),
            clientName: $clientDisplayName,
            clientType: $query->clientType,
            interviewMap: $interviewMap
        );

        // 9. Store in Redis Query Cache
        $cacheTtl = (int) config('ats.cache.query_ttl_seconds', 300);
        Redis::setex($cacheKey, $cacheTtl, serialize($viewModel));

        return $viewModel;
    }

    /**
     * Resolves initial brand color matching Modern Card + Sub-Card enterprise design standard (#ffffff).
     */
    protected function getInitialColor(string $initial): string
    {
        return '#ffffff';
    }
}
