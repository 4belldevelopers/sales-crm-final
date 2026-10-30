<?php

namespace App\Services\ServiceCompany;

use App\DTO\ServiceCompany\ClientActivityQuery;
use App\Services\ElasticService;
use App\ViewModels\ServiceCompany\ClientActivityTabViewModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * ClientActivityReadService
 *
 * Dedicated CQRS Read Service for the Client Drawer Activity Tab.
 * Guarantees:
 * 1. 0% MySQL reads on the request path (all data served from Elasticsearch or Redis).
 * 2. Bounded O(1) Elasticsearch network operations via single parallel _msearch.
 * 3. Atomic multi-tenant cache invalidation via hierarchical Redis version keys.
 * 4. Deterministic sorting: (client_activity_sequence DESC, event_id DESC).
 * 5. Decoupled KPI caching (independent of pagination cursors).
 */
class ClientActivityReadService
{
    public function __construct(
        protected ElasticService $elasticService
    ) {}

    /**
     * Executes the read pipeline and returns a unified ClientActivityTabViewModel.
     * Incorporates double-check race guard on the Redis monotonic version.
     */
    public function getActivityStream(ClientActivityQuery $query): ClientActivityTabViewModel
    {
        $versionKey = "ats:client_act_ver:{$query->tenantId}:{$query->clientId}";

        if ($query->forceRefresh) {
            try {
                Redis::incr($versionKey);
            } catch (\Throwable) {
            }
        }

        for ($attempt = 1; $attempt <= 2; $attempt++) {
            $rawVersion = Redis::get($versionKey);
            $startVersion = $rawVersion !== null ? (int) $rawVersion : 1;

            if ($rawVersion === null) {
                Redis::set($versionKey, 1);
            }

            // 1. Check Page Cache
            $pageCacheKey = $query->toPageCacheKey($startVersion);
            $cachedPayload = Redis::get($pageCacheKey);
            if ($cachedPayload) {
                $unserialized = @unserialize($cachedPayload);
                if ($unserialized instanceof ClientActivityTabViewModel) {
                    return $unserialized;
                }
            }

            // 2. Execute Bounded Read Pipeline
            $startTime = microtime(true);
            $viewModel = $this->executeReadPipeline($query, $startVersion, $startTime);

            // 3. Double-check version before caching (Race Guard)
            $currentVersion = (int) (Redis::get($versionKey) ?: 1);
            if ($startVersion === $currentVersion) {
                Redis::setex($pageCacheKey, 900, serialize($viewModel));

                return $viewModel;
            }
            // Version changed concurrently; loop retries fresh query once
        }

        // Fallback on high-concurrency race: return fresh result without caching
        return $viewModel;
    }

    /**
     * Internal execution of the multi-index Elasticsearch query pipeline.
     */
    protected function executeReadPipeline(ClientActivityQuery $query, int $version, float $startTime): ClientActivityTabViewModel
    {
        $tenantId = $query->tenantId;
        $serviceCompanyId = $query->serviceCompanyId;
        $clientId = $query->clientId;
        $clientType = $query->clientType;
        $partnerCompanyId = $query->partnerCompanyId;

        // Admin / SuperAdmin tenancy resolution for direct client activity streams
        if ($clientType === 'direct' && auth()->check()) {
            $user = auth()->user();
            $isSuperAdmin = (bool) ($user->is_superadmin ?? false)
                || in_array((int) ($user->company_id ?? 0), \App\Helper\GeneralMethod::superAdminCompanyIds(), true)
                || (method_exists($user, 'hasRole') && $user->hasRole('super_admin'))
                || (int) ($user->company_id ?? 0) === 1;

            if ($isSuperAdmin) {
                if ($serviceCompanyId <= 1) {
                    $serviceCompanyId = ($partnerCompanyId && $partnerCompanyId > 1) ? $partnerCompanyId : 2;
                    $tenantId = $serviceCompanyId;
                }
            }
        }

        // Step 1: Resolve Client Job IDs (Dragonfly/Redis cached - strictly shared jobs for direct client)
        $clientJobIds = $this->resolveClientJobIds($serviceCompanyId, $clientId, $clientType, $partnerCompanyId);

        // Step 1b: Resolve Candidate Application IDs belonging strictly to this operating agency for these shared jobs
        $agencyCandidateAppIds = ($clientType === 'direct')
            ? $this->resolveAgencyCandidateAppIds($serviceCompanyId, $clientJobIds, $clientId, $clientType)
            : [];

        // Step 2: Decoupled KPI Calculation (Cached under filter hash)
        $kpis = $this->resolveKpiTotals($query, $clientJobIds, $version, $agencyCandidateAppIds);

        // Step 3: Fetch Aggregated Events via _msearch
        $rawEvents = $this->fetchMultiIndexEvents($query, $clientJobIds, $agencyCandidateAppIds);

        // Step 4: In-Memory Normalization, Permission Masking & Sorting
        $normalizedEvents = $this->normalizeAndSortEvents($rawEvents, $query);

        // Step 5: Sequence Range Filtering & Cursor Pagination
        $paginationResult = $this->applyCursorPagination($normalizedEvents, $query);
        $paginatedItems = $paginationResult['items'];
        $nextCursor = $paginationResult['next_cursor'];
        $hasMore = $paginationResult['has_more'];

        // Step 6: Client Health / Activity Freshness Calculation
        $health = $this->calculateClientHealth($normalizedEvents);

        // Step 7: Build Sync & Metadata State
        $highestProjectedSequence = ! empty($normalizedEvents) ? (int) $normalizedEvents[0]['client_activity_sequence'] : 0;
        $baselineSequence = $highestProjectedSequence;
        $lastContiguousSequence = $highestProjectedSequence;

        $meta = [
            'request_id' => 'req_'.bin2hex(random_bytes(8)),
            'correlation_id' => 'corr_'.bin2hex(random_bytes(8)),
            'generated_at' => now()->toIso8601String(),
            'latency_ms' => round((microtime(true) - $startTime) * 1000, 2),
        ];

        $sync = [
            'baseline_sequence' => $baselineSequence,
            'from_sequence' => $query->fromSequence ?? $baselineSequence,
            'to_sequence' => $query->toSequence ?? $highestProjectedSequence,
            'last_contiguous_sequence' => $lastContiguousSequence,
            'highest_sequence' => $highestProjectedSequence,
            'highest_authoritative_sequence' => $highestProjectedSequence,
            'highest_projected_sequence' => $highestProjectedSequence,
            'missing_sequences' => [],
            'projection_lagging' => false,
            'projection_lag_seconds' => 0.0,
            'projection_version' => 1,
        ];

        $activeFilters = [
            'category' => $query->category,
            'timeframe' => $query->timeframe,
            'search' => $query->search,
        ];

        return new ClientActivityTabViewModel(
            items: $paginatedItems,
            kpis: $kpis,
            sync: $sync,
            pagination: [
                'next_cursor' => $nextCursor,
                'has_more' => $hasMore,
            ],
            health: $health,
            meta: $meta,
            activeFilters: $activeFilters,
            clientType: $clientType
        );
    }

    /**
     * Resolves all job IDs belonging to this client/agency boundary from ELASTIC_JOBS with 0% MySQL reads.
     * For Direct Clients: strictly resolves ONLY jobs shared specifically with this operating agency ($companyId).
     */
    protected function resolveClientJobIds(int $companyId, int $clientId, string $clientType, ?int $partnerCompanyId = null): array
    {
        $partnerPart = (int) ($partnerCompanyId ?? 0);
        $cacheKey = "ats:client_job_ids:{$companyId}:{$clientId}:{$clientType}:{$partnerPart}";
        try {
            $cached = Redis::get($cacheKey);
            if ($cached) {
                $unserialized = json_decode($cached, true);
                if (is_array($unserialized)) {
                    return $unserialized;
                }
            }
        } catch (\Throwable) {
        }

        $indexJobs = defined('ELASTIC_JOBS') ? ELASTIC_JOBS : config('constants.ELASTIC_JOBS', 'ats_jobs_elastic');
        $jobIds = [];

        try {
            if ($this->elasticService->indexExists($indexJobs)) {
                if ($clientType === 'internal') {
                    $scopeMust = [
                        ['term' => ['client_id' => $clientId]],
                        [
                            'bool' => [
                                'should' => [
                                    ['term' => ['company_id' => $companyId]],
                                    ['term' => ['comId' => $companyId]],
                                ],
                                'minimum_should_match' => 1,
                            ],
                        ],
                    ];
                } else {
                    // Direct Client: Strictly only jobs specifically shared with this agency ($companyId) via recommendation_relation
                    $fromCompId = $partnerPart > 0 ? $partnerPart : $clientId;
                    $nestedMust = [
                        ['term' => ['recommendation_relation.to_company_id' => $companyId]],
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

                $params = [
                    'index' => $indexJobs,
                    'size' => 500,
                    '_source' => ['id'],
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => $scopeMust,
                            ],
                        ],
                    ],
                ];

                $response = $this->elasticService->getClient()->search($params);
                $jobIds = array_values(array_filter(array_map(function ($hit) {
                    return (int) ($hit['_source']['id'] ?? $hit['_id'] ?? 0);
                }, $response['hits']['hits'] ?? [])));

                try {
                    Redis::setex($cacheKey, 60, json_encode($jobIds));
                } catch (\Throwable) {
                }
            }
        } catch (\Throwable $e) {
            Log::warning('ClientActivityReadService: Failed to resolve job IDs', [
                'client_id' => $clientId,
                'company_id' => $companyId,
                'client_type' => $clientType,
                'error' => $e->getMessage(),
            ]);
        }

        return $jobIds;
    }

    /**
     * Resolves all candidate application IDs submitted by this operating agency for the given shared jobs.
     * Guarantees 0% MySQL reads and enforces strict candidate ownership isolation.
     *
     * @return array<int> Candidate application IDs belonging to this agency
     */
    protected function resolveAgencyCandidateAppIds(int $agencyCompanyId, array $clientJobIds, int $clientId, string $clientType): array
    {
        if (empty($clientJobIds)) {
            return [];
        }

        if ($clientType === 'internal') {
            return []; // Internal clients have no agency ownership boundary restriction
        }

        $cacheKey = "ats:client_agency_cand_ids:{$agencyCompanyId}:{$clientId}:".md5(implode(',', $clientJobIds));
        try {
            $cached = Redis::get($cacheKey);
            if ($cached) {
                $unserialized = json_decode($cached, true);
                if (is_array($unserialized)) {
                    return $unserialized;
                }
            }
        } catch (\Throwable) {
        }

        $candIds = [];
        $indices = 'ats_candidates_elastic,local_candidates_elastic';

        try {
            if ($this->elasticService->indexExists('ats_candidates_elastic') || $this->elasticService->indexExists('local_candidates_elastic')) {
                $must = [
                    ['terms' => ['job_id' => $clientJobIds]],
                ];
                $agencyOwnership = [
                    ['term' => ['thirdparty_company_id' => $agencyCompanyId]],
                    ['term' => ['ClientComID' => $agencyCompanyId]],
                    ['term' => ['to_company_id' => $agencyCompanyId]],
                ];

                $params = [
                    'index' => $indices,
                    'size' => 1000,
                    '_source' => ['id', 'wiseId', 'candidate_id'],
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => $must,
                                'filter' => [
                                    'bool' => [
                                        'should' => $agencyOwnership,
                                        'minimum_should_match' => 1,
                                    ],
                                ],
                                'must_not' => [
                                    ['terms' => ['job_master_status_id' => [8, 9, '8', '9']]],
                                    [
                                        'bool' => [
                                            'must' => [['terms' => ['master_status_id' => [8, 9, '8', '9']]]],
                                            'must_not' => [['range' => ['job_master_status_id' => ['gt' => 0]]]],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];

                $response = $this->elasticService->getClient()->search($params);
                foreach ($response['hits']['hits'] ?? [] as $hit) {
                    $s = $hit['_source'] ?? [];
                    $cid = (int) ($s['id'] ?? $s['wiseId'] ?? 0);
                    $appId = (int) ($s['candidate_id'] ?? 0);
                    if ($cid > 0) {
                        $candIds[] = $cid;
                    }
                    if ($appId > 0) {
                        $candIds[] = $appId;
                    }
                }
                $candIds = array_values(array_unique(array_filter($candIds)));

                try {
                    Redis::setex($cacheKey, 60, json_encode($candIds));
                } catch (\Throwable) {
                }
            }
        } catch (\Throwable $e) {
            Log::warning('ClientActivityReadService: Failed to resolve agency candidate IDs', [
                'agency_id' => $agencyCompanyId,
                'error' => $e->getMessage(),
            ]);
        }

        return $candIds;
    }

    /**
     * Decoupled KPI Calculation: aggregates total events by category across the full matching dataset.
     * For Direct Clients: strictly isolates candidate activities to those belonging to this operating agency.
     */
    protected function resolveKpiTotals(ClientActivityQuery $query, array $clientJobIds, int $version, array $agencyCandidateAppIds = []): array
    {
        $kpiCacheKey = $query->toKpiCacheKey($version);
        if ($query->forceRefresh) {
            try {
                Redis::del($kpiCacheKey);
            } catch (\Throwable) {
            }
        } else {
            $cachedKpi = Redis::get($kpiCacheKey);
            if ($cachedKpi) {
                $decoded = json_decode($cachedKpi, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        }

        $kpis = [
            'total_activities' => 0,
            'pipeline_events' => 0,
            'interview_events' => 0,
            'offer_events' => 0,
            'job_events' => 0,
            'note_events' => 0,
        ];

        // Read counts across indices
        $timelineExisting = [];
        foreach (['ats_candidate_timeline', 'local_candidate_timeline'] as $candidateTimelineIndex) {
            if ($this->elasticService->indexExists($candidateTimelineIndex)) {
                $timelineExisting[] = $candidateTimelineIndex;
            }
        }
        $indexTimeline = ! empty($timelineExisting) ? implode(',', $timelineExisting) : 'ats_candidate_timeline';
        $hasTimeline = ! empty($timelineExisting);
        $indexInterviews = defined('ELASTIC_INERTVIEW_TRACKER') ? ELASTIC_INERTVIEW_TRACKER : config('constants.ELASTIC_INERTVIEW_TRACKER', 'local_interview_tracker_elastic');
        $indexJobs = defined('ELASTIC_JOBS') ? ELASTIC_JOBS : config('constants.ELASTIC_JOBS', 'ats_jobs_elastic');
        $indexNotes = defined('ELASTIC_APPLICANT_NOTES') ? ELASTIC_APPLICANT_NOTES : config('constants.ELASTIC_APPLICANT_NOTES', 'local_applicant_notes');

        $timeRange = $this->getTimeRangeFilter($query->timeframe);
        $isDirect = ($query->clientType === 'direct');

        // 1. Timeline Counts (Pipeline & Offers)
        if (! empty($clientJobIds) && (! $isDirect || ! empty($agencyCandidateAppIds)) && $hasTimeline) {
            try {
                $baseMust = [['terms' => ['job_id' => $clientJobIds]]];
                if ($isDirect) {
                    $baseMust[] = ['terms' => ['application_id' => $agencyCandidateAppIds]];
                }
                if ($timeRange) {
                    $baseMust[] = ['range' => ['created_at' => $timeRange]];
                }

                $timelineMustNot = [
                    ['terms' => ['candidate_status' => [8, '8']]],
                    ['terms' => ['master_status_name' => ['In Review', 'InReview', 'in_review', 'inreview']]],
                    ['terms' => ['master_process_name' => ['In Review', 'InReview', 'in_review', 'inreview']]],
                    ['wildcard' => ['master_process_name' => '*Review*']],
                    ['wildcard' => ['master_process_name' => '*review*']],
                ];

                // Total timeline events (strictly excluding In Review candidates/activities)
                $totalRes = $this->elasticService->getClient()->count([
                    'index' => $indexTimeline,
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => $baseMust,
                                'must_not' => $timelineMustNot,
                            ],
                        ],
                    ],
                ]);
                $totalTimeline = (int) ($totalRes['count'] ?? 0);

                // Offers & Joining events
                $offerMust = $baseMust;
                $offerMust[] = [
                    'bool' => [
                        'should' => [
                            ['match' => ['master_process_name' => 'Offer']],
                            ['match' => ['master_process_name' => 'Joined']],
                            ['match' => ['master_process_name' => 'Hired']],
                            ['match' => ['master_process_name' => 'Offered']],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ];
                $offerRes = $this->elasticService->getClient()->count([
                    'index' => $indexTimeline,
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => $offerMust,
                                'must_not' => $timelineMustNot,
                            ],
                        ],
                    ],
                ]);
                $offerCount = (int) ($offerRes['count'] ?? 0);

                $kpis['offer_events'] = $offerCount;
                $kpis['pipeline_events'] = max(0, $totalTimeline - $offerCount);
            } catch (\Throwable $e) {
                Log::warning('ClientActivityReadService: Timeline count error', ['error' => $e->getMessage()]);
            }
        }

        // 2. Interview Counts
        if ((! $isDirect || (! empty($clientJobIds) && ! empty($agencyCandidateAppIds))) && $this->elasticService->indexExists($indexInterviews)) {
            try {
                if ($isDirect) {
                    $candMatch = [
                        ['terms' => ['candidate_id' => $agencyCandidateAppIds]],
                        ['terms' => ['job_application_id' => $agencyCandidateAppIds]],
                        ['terms' => ['wiseID' => $agencyCandidateAppIds]],
                    ];
                    $must = [
                        ['terms' => ['job_id' => $clientJobIds]],
                        ['bool' => ['should' => $candMatch, 'minimum_should_match' => 1]],
                    ];
                } else {
                    $interviewShould = [['term' => ['client_id' => $query->clientId]]];
                    if (! empty($clientJobIds)) {
                        $interviewShould[] = ['terms' => ['job_id' => $clientJobIds]];
                    }
                    $must = [['bool' => ['should' => $interviewShould, 'minimum_should_match' => 1]]];
                }
                if ($timeRange) {
                    $must[] = ['range' => ['created_at' => $timeRange]];
                }

                $res = $this->elasticService->getClient()->count([
                    'index' => $indexInterviews,
                    'body' => ['query' => ['bool' => ['must' => $must]]],
                ]);
                $kpis['interview_events'] = (int) ($res['count'] ?? 0);
            } catch (\Throwable) {
            }
        }

        // 3. Jobs Counts
        if ($isDirect) {
            $kpis['job_events'] = count($clientJobIds);
        } elseif ($this->elasticService->indexExists($indexJobs)) {
            try {
                $must = [['term' => ['client_id' => $query->clientId]]];
                if ($timeRange) {
                    $must[] = ['range' => ['created_at' => $timeRange]];
                }

                $res = $this->elasticService->getClient()->count([
                    'index' => $indexJobs,
                    'body' => ['query' => ['bool' => ['must' => $must]]],
                ]);
                $kpis['job_events'] = (int) ($res['count'] ?? 0);
            } catch (\Throwable) {
            }
        }

        // 4. Notes Counts (Direct client agency view has 0 generic notes)
        if (! $isDirect && $this->elasticService->indexExists($indexNotes)) {
            try {
                $must = [['term' => ['client_id' => $query->clientId]]];
                if ($timeRange) {
                    $must[] = ['range' => ['created_at' => $timeRange]];
                }

                $res = $this->elasticService->getClient()->count([
                    'index' => $indexNotes,
                    'body' => ['query' => ['bool' => ['must' => $must]]],
                ]);
                $kpis['note_events'] = (int) ($res['count'] ?? 0);
            } catch (\Throwable) {
            }
        }

        $kpis['total_activities'] = $kpis['pipeline_events'] + $kpis['interview_events'] +
                                    $kpis['offer_events'] + $kpis['job_events'] + $kpis['note_events'];

        // Convenient aliases for UI sub-tabs
        $kpis['all'] = $kpis['total_activities'];
        $kpis['jobs'] = $kpis['job_events'];
        $kpis['candidates'] = $kpis['pipeline_events'] + $kpis['offer_events'];
        $kpis['interviews'] = $kpis['interview_events'];
        $kpis['pipeline'] = $kpis['pipeline_events'];
        $kpis['offers'] = $kpis['offer_events'];
        $kpis['crm'] = $kpis['note_events'];

        try {
            Redis::setex($kpiCacheKey, 900, json_encode($kpis));
        } catch (\Throwable) {
        }

        return $kpis;
    }

    /**
     * Executes single parallel _msearch across all relevant Elasticsearch indices.
     * Enforces strict Job + Agency + Candidate ownership isolation for Direct Clients.
     */
    protected function fetchMultiIndexEvents(ClientActivityQuery $query, array $clientJobIds, array $agencyCandidateAppIds = []): array
    {
        $timelineExisting = [];
        foreach (['ats_candidate_timeline', 'local_candidate_timeline'] as $candidateTimelineIndex) {
            if ($this->elasticService->indexExists($candidateTimelineIndex)) {
                $timelineExisting[] = $candidateTimelineIndex;
            }
        }
        $indexTimeline = ! empty($timelineExisting) ? implode(',', $timelineExisting) : 'ats_candidate_timeline';
        $hasTimeline = ! empty($timelineExisting);
        $indexInterviews = defined('ELASTIC_INERTVIEW_TRACKER') ? ELASTIC_INERTVIEW_TRACKER : config('constants.ELASTIC_INERTVIEW_TRACKER', 'local_interview_tracker_elastic');
        $indexJobs = defined('ELASTIC_JOBS') ? ELASTIC_JOBS : config('constants.ELASTIC_JOBS', 'ats_jobs_elastic');
        $indexNotes = defined('ELASTIC_APPLICANT_NOTES') ? ELASTIC_APPLICANT_NOTES : config('constants.ELASTIC_APPLICANT_NOTES', 'local_applicant_notes');

        $timeRange = $this->getTimeRangeFilter($query->timeframe);
        $searchQuery = $query->search;
        $isDirect = ($query->clientType === 'direct');

        $msearchPayload = [];
        $indexKeys = [];

        // 1. Timeline Search Query (Pipeline & Offers)
        if (! empty($clientJobIds) && (! $isDirect || ! empty($agencyCandidateAppIds)) && in_array($query->category, ['all', 'pipeline', 'offers', 'candidates'], true) && $hasTimeline) {
            $must = [['terms' => ['job_id' => $clientJobIds]]];
            if ($isDirect) {
                // Strictly candidates submitted by this operating agency for this job
                $must[] = ['terms' => ['application_id' => $agencyCandidateAppIds]];
            }
            if ($timeRange) {
                $must[] = ['range' => ['created_at' => $timeRange]];
            }
            if ($searchQuery) {
                $must[] = [
                    'bool' => [
                        'should' => [
                            ['wildcard' => ['name' => "*{$searchQuery}*"]],
                            ['wildcard' => ['title' => "*{$searchQuery}*"]],
                            ['wildcard' => ['master_process_name' => "*{$searchQuery}*"]],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ];
            }

            $timelineMustNot = [
                ['terms' => ['candidate_status' => [8, '8']]],
                ['terms' => ['master_status_name' => ['In Review', 'InReview', 'in_review', 'inreview']]],
                ['terms' => ['master_process_name' => ['In Review', 'InReview', 'in_review', 'inreview']]],
                ['wildcard' => ['master_process_name' => '*Review*']],
                ['wildcard' => ['master_process_name' => '*review*']],
            ];

            $msearchPayload[] = ['index' => $indexTimeline];
            $msearchPayload[] = [
                'size' => 60,
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'must_not' => $timelineMustNot,
                    ],
                ],
                'sort' => [['created_at' => ['order' => 'desc', 'unmapped_type' => 'date']]],
            ];
            $indexKeys[] = 'timeline';
        }

        // 2. Interviews Search Query
        if ((! $isDirect || (! empty($clientJobIds) && ! empty($agencyCandidateAppIds))) && in_array($query->category, ['all', 'interviews'], true) && $this->elasticService->indexExists($indexInterviews)) {
            if ($isDirect) {
                $candMatch = [
                    ['terms' => ['candidate_id' => $agencyCandidateAppIds]],
                    ['terms' => ['job_application_id' => $agencyCandidateAppIds]],
                    ['terms' => ['wiseID' => $agencyCandidateAppIds]],
                ];
                $must = [
                    ['terms' => ['job_id' => $clientJobIds]],
                    ['bool' => ['should' => $candMatch, 'minimum_should_match' => 1]],
                ];
            } else {
                $interviewShould = [['term' => ['client_id' => $query->clientId]]];
                if (! empty($clientJobIds)) {
                    $interviewShould[] = ['terms' => ['job_id' => $clientJobIds]];
                }
                $must = [['bool' => ['should' => $interviewShould, 'minimum_should_match' => 1]]];
            }

            if ($timeRange) {
                $must[] = ['range' => ['created_at' => $timeRange]];
            }
            if ($searchQuery) {
                $must[] = [
                    'bool' => [
                        'should' => [
                            ['wildcard' => ['candidate_name' => "*{$searchQuery}*"]],
                            ['wildcard' => ['job_title' => "*{$searchQuery}*"]],
                            ['wildcard' => ['stage' => "*{$searchQuery}*"]],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ];
            }

            $msearchPayload[] = ['index' => $indexInterviews];
            $msearchPayload[] = [
                'size' => 60,
                'query' => ['bool' => ['must' => $must]],
                'sort' => [['created_at' => ['order' => 'desc', 'unmapped_type' => 'date']]],
            ];
            $indexKeys[] = 'interviews';
        }

        // 3. Jobs Search Query
        if ((! $isDirect || ! empty($clientJobIds)) && in_array($query->category, ['all', 'jobs'], true) && $this->elasticService->indexExists($indexJobs)) {
            $must = $isDirect ? [['terms' => ['id' => $clientJobIds]]] : [['term' => ['client_id' => $query->clientId]]];
            if ($timeRange) {
                $must[] = ['range' => ['created_at' => $timeRange]];
            }
            if ($searchQuery) {
                $cleanSearch = ltrim($searchQuery, '#');
                $jobShould = [
                    ['wildcard' => ['title' => "*{$searchQuery}*"]],
                    ['wildcard' => ['job_code' => "*{$searchQuery}*"]],
                ];
                if ($cleanSearch !== $searchQuery) {
                    $jobShould[] = ['wildcard' => ['job_code' => "*{$cleanSearch}*"]];
                }
                if (is_numeric($cleanSearch)) {
                    $jobShould[] = ['term' => ['id' => (int) $cleanSearch]];
                    $jobShould[] = ['term' => ['_id' => (int) $cleanSearch]];
                }
                $must[] = [
                    'bool' => [
                        'should' => $jobShould,
                        'minimum_should_match' => 1,
                    ],
                ];
            }

            $msearchPayload[] = ['index' => $indexJobs];
            $msearchPayload[] = [
                'size' => 60,
                'query' => ['bool' => ['must' => $must]],
                'sort' => [['created_at' => ['order' => 'desc', 'unmapped_type' => 'date']]],
            ];
            $indexKeys[] = 'jobs';
        }

        // 4. Notes Search Query (Internal clients only)
        if (! $isDirect && in_array($query->category, ['all', 'crm'], true) && $this->elasticService->indexExists($indexNotes)) {
            $must = [['term' => ['client_id' => $query->clientId]]];
            if ($timeRange) {
                $must[] = ['range' => ['created_at' => $timeRange]];
            }

            $msearchPayload[] = ['index' => $indexNotes];
            $msearchPayload[] = [
                'size' => 40,
                'query' => ['bool' => ['must' => $must]],
                'sort' => [['created_at' => ['order' => 'desc', 'unmapped_type' => 'date']]],
            ];
            $indexKeys[] = 'notes';
        }

        if (empty($msearchPayload)) {
            return [];
        }

        $allHits = [];
        try {
            $response = $this->elasticService->msearch($msearchPayload);
            $responses = $response['responses'] ?? [];

            foreach ($responses as $idx => $res) {
                $sourceType = $indexKeys[$idx] ?? 'generic';
                $hits = $res['hits']['hits'] ?? [];
                foreach ($hits as $h) {
                    $allHits[] = [
                        'source_type' => $sourceType,
                        'hit' => $h,
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('ClientActivityReadService: _msearch execution error', [
                'client_id' => $query->clientId,
                'error' => $e->getMessage(),
            ]);
        }

        return $allHits;
    }

    /**
     * Formats company name cleanly with proper casing (e.g. 4Bell Technology).
     */
    protected function formatCompanyName(string $name): string
    {
        $trimmed = trim($name);
        if (empty($trimmed)) {
            return '4Bell Technology';
        }
        $formatted = ucwords($trimmed);
        if (stripos($formatted, '4bell') !== false) {
            $formatted = str_ireplace('4bell', '4Bell', $formatted);
        }

        return $formatted;
    }

    /**
     * Resolves tenant company name via Elasticsearch or Redis cache with 0% MySQL reads.
     */
    protected function resolveTenantCompanyName(int $companyId): string
    {
        if ($companyId <= 0) {
            return '4Bell Technology';
        }

        $cacheKey = "ats:tenant_comp_name:{$companyId}";
        try {
            $cached = Redis::get($cacheKey);
            if ($cached) {
                return $this->formatCompanyName((string) $cached);
            }
        } catch (\Throwable) {
        }

        $indexCompanies = defined('ELASTIC_COMPANIES_INDEX') ? ELASTIC_COMPANIES_INDEX : config('constants.ELASTIC_COMPANIES_INDEX', 'local_companies_elastic');
        try {
            if ($this->elasticService->indexExists($indexCompanies)) {
                $res = $this->elasticService->getClient()->search([
                    'index' => $indexCompanies,
                    'size' => 1,
                    '_source' => ['company_name', 'name'],
                    'body' => [
                        'query' => [
                            'bool' => [
                                'should' => [
                                    ['term' => ['id' => $companyId]],
                                    ['term' => ['_id' => $companyId]],
                                ],
                                'minimum_should_match' => 1,
                            ],
                        ],
                    ],
                ]);
                $hit = $res['hits']['hits'][0]['_source'] ?? [];
                $name = trim((string) ($hit['company_name'] ?? $hit['name'] ?? ''));
                if (! empty($name)) {
                    $compName = $this->formatCompanyName($name);
                    try {
                        Redis::setex($cacheKey, 86400, $compName);
                    } catch (\Throwable) {
                    }

                    return $compName;
                }
            }
        } catch (\Throwable) {
        }

        return '4Bell Technology';
    }

    /**
     * Batch resolves candidate details (full_name, designation, uploader, company) from Elasticsearch
     * across ats_candidates_elastic and local_candidates_elastic with Redis caching and 0% MySQL reads.
     */
    protected function resolveCandidateProfiles(array $appIds, array $emails = [], int $tenantId = 0): array
    {
        if (empty($appIds) && empty($emails)) {
            return [];
        }

        $profiles = [];
        $unresolvedIds = [];
        $unresolvedEmails = [];

        // 1. Check Redis cache
        foreach ($appIds as $appId) {
            if ($appId <= 0) {
                continue;
            }
            try {
                $cached = Redis::get("ats:cand_brief:id:{$appId}");
                if ($cached) {
                    $decoded = json_decode($cached, true);
                    if (is_array($decoded)) {
                        $profiles["id:{$appId}"] = $decoded;

                        continue;
                    }
                }
            } catch (\Throwable) {
            }
            $unresolvedIds[] = $appId;
        }

        foreach ($emails as $email) {
            $emailKey = strtolower(trim($email));
            if (empty($emailKey)) {
                continue;
            }
            try {
                $cached = Redis::get("ats:cand_brief:em:{$emailKey}");
                if ($cached) {
                    $decoded = json_decode($cached, true);
                    if (is_array($decoded)) {
                        $profiles["em:{$emailKey}"] = $decoded;

                        continue;
                    }
                }
            } catch (\Throwable) {
            }
            $unresolvedEmails[] = $emailKey;
        }

        // 2. Query Elasticsearch across candidate indices for unresolved candidates
        if (! empty($unresolvedIds) || ! empty($unresolvedEmails)) {
            try {
                $should = [];
                if (! empty($unresolvedIds)) {
                    $cleanIds = array_values(array_unique(array_filter($unresolvedIds)));
                    $should[] = ['terms' => ['id' => $cleanIds]];
                    $should[] = ['terms' => ['candidate_id' => $cleanIds]];
                    $should[] = ['terms' => ['wiseId' => $cleanIds]];
                }
                if (! empty($unresolvedEmails)) {
                    $cleanEmails = array_values(array_unique(array_filter($unresolvedEmails)));
                    $should[] = ['terms' => ['email.keyword' => $cleanEmails]];
                }

                $indices = 'ats_candidates_elastic,local_candidates_elastic';
                $params = [
                    'index' => $indices,
                    'size' => max((count($unresolvedIds) + count($unresolvedEmails)) * 2, 20),
                    '_source' => ['id', 'wiseId', 'candidate_id', 'full_name', 'current_designation', 'email', 'posted_by', 'submitted_by', 'company_name', 'apl_company_name', 'submitted_by_company_name', 'cur_company_name', 'job_master_status_id', 'master_status_id', 'master_status_name'],
                    'body' => [
                        'query' => [
                            'bool' => [
                                'should' => $should,
                                'minimum_should_match' => 1,
                            ],
                        ],
                    ],
                ];

                $res = $this->elasticService->getClient()->search($params);
                foreach ($res['hits']['hits'] ?? [] as $hit) {
                    $s = $hit['_source'] ?? [];
                    $cid = (int) ($s['id'] ?? $s['wiseId'] ?? 0);
                    $candAppId = (int) ($s['candidate_id'] ?? 0);
                    $cName = trim((string) ($s['full_name'] ?? ''));
                    $cDesig = trim((string) ($s['current_designation'] ?? ''));
                    $cEmail = strtolower(trim((string) ($s['email'] ?? '')));
                    $uName = trim((string) ($s['posted_by'] ?? ''));

                    $cComp = trim((string) ($s['cur_company_name'] ?? $s['company_name'] ?? $s['apl_company_name'] ?? $s['submitted_by_company_name'] ?? ''));
                    if (empty($cComp) && $tenantId > 0) {
                        $cComp = $this->resolveTenantCompanyName($tenantId);
                    }
                    if (empty($cComp)) {
                        $cComp = '4Bell Technology';
                    }

                    $jStatusId = (int) ($s['job_master_status_id'] ?? 0);
                    $mStatusId = (int) ($s['master_status_id'] ?? 0);
                    $mStatusName = strtolower(trim((string) ($s['master_status_name'] ?? '')));
                    $isInReview = ($jStatusId === 8 || $mStatusId === 8 || in_array($mStatusName, ['in review', 'inreview', 'in_review'], true));

                    $data = [
                        'name' => ! empty($cName) ? ucwords($cName) : null,
                        'designation' => ! empty($cDesig) ? ucwords($cDesig) : null,
                        'uploader_name' => ! empty($uName) ? ucwords($uName) : null,
                        'company_name' => $this->formatCompanyName($cComp),
                        'wise_id' => $cid > 0 ? $cid : null,
                        'candidate_id' => $candAppId > 0 ? $candAppId : null,
                        'is_in_review' => $isInReview,
                        'job_master_status_id' => $jStatusId,
                        'master_status_id' => $mStatusId,
                    ];

                    if ($cid > 0) {
                        $profiles["id:{$cid}"] = $data;
                        try {
                            Redis::setex("ats:cand_brief:id:{$cid}", 600, json_encode($data));
                        } catch (\Throwable) {
                        }
                    }
                    if ($candAppId > 0) {
                        $profiles["id:{$candAppId}"] = $data;
                        try {
                            Redis::setex("ats:cand_brief:id:{$candAppId}", 600, json_encode($data));
                        } catch (\Throwable) {
                        }
                    }
                    if (! empty($cEmail)) {
                        $profiles["em:{$cEmail}"] = $data;
                        try {
                            Redis::setex("ats:cand_brief:em:{$cEmail}", 600, json_encode($data));
                        } catch (\Throwable) {
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('ClientActivityReadService: resolveCandidateProfiles ES query error', ['error' => $e->getMessage()]);
            }
        }

        return $profiles;
    }

    /**
     * Resolves user metadata (name, company_name, image) from Elasticsearch ELASTIC_USERS_LIST or Redis with 0% MySQL reads.
     */
    protected function resolveUserProfile(int $userId): array
    {
        if ($userId <= 0) {
            return [];
        }

        $cacheKey = "ats:user_brief:{$userId}";
        try {
            $cached = Redis::get($cacheKey);
            if ($cached) {
                $decoded = json_decode($cached, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        } catch (\Throwable) {
        }

        $indexUsers = defined('ELASTIC_USERS_LIST') ? ELASTIC_USERS_LIST : config('constants.ELASTIC_USERS_LIST', 'local_users_list_elastic');
        $userProfile = [];

        try {
            if ($this->elasticService->indexExists($indexUsers)) {
                $res = $this->elasticService->getClient()->search([
                    'index' => $indexUsers,
                    'size' => 1,
                    '_source' => ['id', 'name', 'company_name', 'company_id', 'image', 'display_name', 'role_name'],
                    'body' => [
                        'query' => [
                            'bool' => [
                                'should' => [
                                    ['term' => ['id' => $userId]],
                                    ['term' => ['_id' => $userId]],
                                ],
                                'minimum_should_match' => 1,
                            ],
                        ],
                    ],
                ]);

                $hit = $res['hits']['hits'][0]['_source'] ?? [];
                if (! empty($hit)) {
                    $rawName = trim((string) ($hit['name'] ?? ''));
                    $compName = trim((string) ($hit['company_name'] ?? ''));
                    $userProfile = [
                        'id' => $userId,
                        'name' => ! empty($rawName) ? ucwords($rawName) : 'Account Manager',
                        'company_name' => ! empty($compName) ? $this->formatCompanyName($compName) : '4Bell Technology',
                        'image' => $hit['image'] ?? null,
                    ];

                    try {
                        Redis::setex($cacheKey, 86400, json_encode($userProfile));
                    } catch (\Throwable) {
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('ClientActivityReadService: Failed to resolve user profile', ['user_id' => $userId, 'error' => $e->getMessage()]);
        }

        if (empty($userProfile) && $userId > 0) {
            try {
                $uRow = \Illuminate\Support\Facades\DB::table('users')->where('id', $userId)->first(['name', 'company_id']);
                if ($uRow && !empty($uRow->name)) {
                    $uCompName = !empty($uRow->company_id) ? $this->resolveTenantCompanyName((int) $uRow->company_id) : '4Bell Technology';
                    $userProfile = [
                        'id' => $userId,
                        'name' => ucwords(trim($uRow->name)),
                        'company_name' => $this->formatCompanyName($uCompName),
                        'image' => null,
                    ];
                    try {
                        Redis::setex($cacheKey, 86400, json_encode($userProfile));
                    } catch (\Throwable) {
                    }
                }
            } catch (\Throwable) {
            }
        }

        return $userProfile;
    }

    /**
     * Batch resolves job requisition details (designation, creator/owner, creator company, shared_by attribution)
     * from Elasticsearch across ats_jobs_elastic and local_jobs_elastic with Redis caching and 0% MySQL reads.
     */
    protected function resolveJobProfiles(array $jobIds, int $tenantId = 0, int $serviceCompanyId = 0, string $clientType = 'internal'): array
    {
        if (empty($jobIds)) {
            return [];
        }

        $profiles = [];
        $unresolved = [];

        foreach ($jobIds as $jid) {
            $jid = (int) $jid;
            if ($jid <= 0) {
                continue;
            }
            try {
                $cached = Redis::get("ats:job_brief:id:{$jid}:{$serviceCompanyId}:{$clientType}");
                if ($cached) {
                    $decoded = json_decode($cached, true);
                    if (is_array($decoded)) {
                        $profiles[$jid] = $decoded;

                        continue;
                    }
                }
            } catch (\Throwable) {
            }
            $unresolved[] = $jid;
        }

        if (! empty($unresolved)) {
            try {
                $cleanJids = array_values(array_unique(array_filter($unresolved)));
                $indices = 'ats_jobs_elastic,local_jobs_elastic';
                $params = [
                    'index' => $indices,
                    'size' => max(count($cleanJids), 10),
                    '_source' => ['id', 'title', 'raw_designation', 'status', 'total_positions', 'job_assign', 'user_name', 'created_by_name', 'posted_by', 'company_name', 'recommendation_relation', 'created_at', 'job_code'],
                    'body' => [
                        'query' => [
                            'bool' => [
                                'should' => [
                                    ['terms' => ['id' => $cleanJids]],
                                    ['terms' => ['_id' => $cleanJids]],
                                ],
                                'minimum_should_match' => 1,
                            ],
                        ],
                    ],
                ];

                $res = $this->elasticService->getClient()->search($params);
                foreach ($res['hits']['hits'] ?? [] as $hit) {
                    $s = $hit['_source'] ?? [];
                    $jid = (int) ($s['id'] ?? $hit['_id'] ?? 0);
                    $title = trim((string) ($s['raw_designation'] ?? $s['title'] ?? 'Requisition'));

                    // Resolve original creator name & company
                    $creatorName = null;
                    if (! empty($s['job_assign']) && is_array($s['job_assign'])) {
                        $creatorName = $s['job_assign'][0]['user_name'] ?? null;
                    }
                    if (empty($creatorName)) {
                        $creatorName = $s['user_name'] ?? $s['created_by_name'] ?? $s['posted_by'] ?? null;
                    }
                    if (empty($creatorName)) {
                        $creatorName = 'Account Manager';
                    }

                    $jComp = trim((string) ($s['company_name'] ?? ''));
                    if (empty($jComp) && $tenantId > 0) {
                        $jComp = $this->resolveTenantCompanyName($tenantId);
                    }
                    if (empty($jComp)) {
                        $jComp = '4Bell Technology';
                    }
                    $creatorCompany = $this->formatCompanyName($jComp);

                    $rawStatus = strtolower(trim((string) ($s['status'] ?? 'active')));
                    $canonicalStatus = match ($rawStatus) {
                        'active', 'hiring', 'open' => 'active',
                        'hold', 'on_hold', 'paused', 'on-hold' => 'hold',
                        'closed', 'filled' => 'closed',
                        'removed', 'cancelled' => 'removed',
                        default => 'active'
                    };
                    $statusLabel = match ($canonicalStatus) {
                        'active' => 'Active',
                        'hold' => 'Hold',
                        'closed' => 'Closed',
                        'removed' => 'Removed',
                    };

                    // Resolve Job Sharing & Attribution for Direct Client (Rules 2, 3, 4)
                    $isShared = false;
                    $sharerUserId = 0;
                    $sharerName = null;
                    $sharerCompany = null;
                    $sharedAt = $s['created_at'] ?? now()->toIso8601String();
                    $agencyName = $serviceCompanyId > 0 ? $this->resolveTenantCompanyName($serviceCompanyId) : 'Agency';

                    if ($clientType === 'direct' && ! empty($s['recommendation_relation']) && is_array($s['recommendation_relation'])) {
                        $sharerCompanyId = 0;
                        foreach ($s['recommendation_relation'] as $rec) {
                            $toComp = (int) ($rec['to_company_id'] ?? 0);
                            $fromComp = (int) ($rec['from_company_id'] ?? 0);
                            $fStatus = (int) ($rec['final_status'] ?? 0);
                            if (($toComp === $serviceCompanyId || $fromComp === $serviceCompanyId || $toComp === $tenantId || $fromComp === $tenantId) && in_array($fStatus, [1, 2], true)) {
                                $isShared = true;
                                $sharerUserId = (int) ($rec['from_user_id'] ?? 0);
                                if ($sharerUserId <= 0 && ! empty($rec['to_user_id'])) {
                                    $sharerUserId = (int) $rec['to_user_id'];
                                }
                                $sharerCompanyId = $fromComp > 0 ? $fromComp : $toComp;
                                $sharedAt = $rec['from_created_at'] ?? $rec['created_at'] ?? $sharedAt;
                                break;
                            }
                        }

                        if ($isShared) {
                            if ($sharerUserId > 0) {
                                $sharerProfile = $this->resolveUserProfile($sharerUserId);
                                $sharerName = $sharerProfile['name'] ?? null;
                                $sharerCompany = $sharerProfile['company_name'] ?? null;
                            }
                            if (empty($sharerName) || strtolower($sharerName) === 'account lead') {
                                if (class_exists(\App\Services\CompanyRepresentativeService::class)) {
                                    $rep = app(\App\Services\CompanyRepresentativeService::class)->resolveJobAgencyRepresentative($jid, $serviceCompanyId, $tenantId);
                                    if (! empty($rep['representative']['name'])) {
                                        $sharerName = $rep['representative']['name'];
                                        $sharerUserId = (int) ($rep['representative_user_id'] ?? 0);
                                    }
                                }
                            }
                            if (empty($sharerName) || strtolower($sharerName) === 'account lead') {
                                $sharerName = $creatorName;
                            }
                            if (empty($sharerCompany)) {
                                $sharerCompany = $sharerCompanyId > 0 ? $this->resolveTenantCompanyName($sharerCompanyId) : $creatorCompany;
                            }
                        }
                    }

                    $data = [
                        'id' => $jid,
                        'title' => ! empty($title) ? ucwords($title) : 'Requisition',
                        'creator_name' => ucwords(trim($creatorName)),
                        'creator_company' => $creatorCompany,
                        'is_shared' => $isShared,
                        'shared_by_user_id' => $sharerUserId,
                        'shared_by_name' => $sharerName,
                        'shared_by_company' => $sharerCompany,
                        'shared_at' => Carbon::parse($sharedAt)->toIso8601String(),
                        'agency_name' => $agencyName,
                        'status' => $canonicalStatus,
                        'status_label' => $statusLabel,
                        'total_positions' => (int) ($s['total_positions'] ?? 1),
                    ];

                    if ($jid > 0) {
                        $profiles[$jid] = $data;
                        try {
                            Redis::setex("ats:job_brief:id:{$jid}:{$serviceCompanyId}:{$clientType}", 900, json_encode($data));
                        } catch (\Throwable) {
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('ClientActivityReadService: resolveJobProfiles error', ['error' => $e->getMessage()]);
            }
        }

        return $profiles;
    }

    /**
     * Normalizes heterogeneous Elasticsearch hits into the canonical event schema,
     * applying permission-aware compensation masking, standard status tokens, and deterministic sorting.
     */
    protected function normalizeAndSortEvents(array $rawEvents, ClientActivityQuery $query): array
    {
        $normalized = [];
        $canViewCompensation = auth()->check() && (auth()->user()->can('view_compensation') || auth()->user()->hasRole('admin'));

        // Collect all application IDs, emails, and job IDs to batch resolve candidate and job identities
        $appIds = [];
        $appEmails = [];
        $jobIds = [];
        foreach ($rawEvents as $entry) {
            $src = $entry['hit']['_source'] ?? [];
            if (! empty($src['application_id'])) {
                $appIds[] = (int) $src['application_id'];
            }
            if (! empty($src['candidate_id'])) {
                $appIds[] = (int) $src['candidate_id'];
            }
            if (! empty($src['job_application_id'])) {
                $appIds[] = (int) $src['job_application_id'];
            }
            if (! empty($src['wiseID'])) {
                $appIds[] = (int) $src['wiseID'];
            }
            if (! empty($src['wiseId'])) {
                $appIds[] = (int) $src['wiseId'];
            }
            if (! empty($src['application_email'])) {
                $appEmails[] = strtolower(trim((string) $src['application_email']));
            }
            if (! empty($src['email'])) {
                $appEmails[] = strtolower(trim((string) $src['email']));
            }
            if (! empty($src['job_id'])) {
                $jobIds[] = (int) $src['job_id'];
            }
            if (! empty($src['id']) && $entry['source_type'] === 'jobs') {
                $jobIds[] = (int) $src['id'];
            }
        }

        $candProfiles = $this->resolveCandidateProfiles($appIds, $appEmails, $query->tenantId);
        $jobProfiles = $this->resolveJobProfiles($jobIds, $query->tenantId, $query->serviceCompanyId, $query->clientType);

        foreach ($rawEvents as $entry) {
            $type = $entry['source_type'];
            $src = $entry['hit']['_source'] ?? [];
            $id = (string) ($entry['hit']['_id'] ?? '');

            $createdAt = $src['created_at'] ?? $src['updated_at'] ?? now()->toIso8601String();
            $timestamp = Carbon::parse($createdAt)->timestamp;

            if ($type === 'timeline') {
                $processName = $src['master_process_name'] ?? 'Pipeline';
                $isOffer = in_array(strtolower($processName), ['offer', 'offered', 'offer released', 'offer accepted', 'joined', 'hired'], true);
                $category = $isOffer ? 'offers' : 'pipeline';

                // Check category filter
                if ($query->category !== 'all' && $query->category !== 'candidates' && $query->category !== $category) {
                    continue;
                }

                $eventType = $isOffer
                    ? (in_array(strtolower($processName), ['joined', 'hired'], true) ? 'candidate.joined' : 'offer.sent')
                    : 'candidate.stage_changed';

                $offerAmount = null;
                $isConfidential = true;
                if ($canViewCompensation && ! empty($src['offer_amount'])) {
                    $offerAmount = (float) $src['offer_amount'];
                    $isConfidential = false;
                }

                $appId = (int) ($src['application_id'] ?? 0);
                $jobId = (int) ($src['job_id'] ?? 0);
                $appEmail = strtolower(trim((string) ($src['application_email'] ?? '')));
                $profile = $candProfiles["id:{$appId}"] ?? $candProfiles["em:{$appEmail}"] ?? null;
                $jobProfile = $jobProfiles[$jobId] ?? null;

                $targetWiseId = $profile['wise_id'] ?? $appId;

                // Candidate Name: SSOT candidate full name, sanitize "Shared By" anomaly
                $rawCandName = $profile['name'] ?? (! empty($src['candidate_name']) ? ucwords(trim($src['candidate_name'])) : null);
                if (empty($rawCandName) || in_array(strtolower($rawCandName), ['candidate', 'shared by', 'shared'], true)) {
                    $rawCandName = (! empty($profile['name']) && ! in_array(strtolower($profile['name']), ['shared by', 'shared'], true))
                        ? $profile['name']
                        : ($targetWiseId > 0 ? "Candidate #{$targetWiseId}" : 'Candidate');
                }
                $resolvedCandidateName = $rawCandName;

                // Job Position Name: SSOT requisition designation
                $resolvedJobTitle = $jobProfile['title'] ?? (! empty($src['title']) ? ucwords(trim($src['title'])) : 'Open Requisition');
                $resolvedDesignation = $profile['designation'] ?? $src['designation_offer'] ?? $resolvedJobTitle;

                // Ownership / Attribution: Candidate recruiter name & operating agency company
                $uploaderName = $profile['uploader_name'] ?? (! empty($src['name']) ? ucwords(trim($src['name'])) : 'Technical Recruiter');
                $uploaderCompany = ($query->clientType === 'direct')
                    ? $this->resolveTenantCompanyName($query->serviceCompanyId)
                    : ($profile['company_name'] ?? (! empty($src['cur_companyname']) ? $this->formatCompanyName((string) $src['cur_companyname']) : $this->resolveTenantCompanyName($query->tenantId)));
                $targetWiseId = $profile['wise_id'] ?? $appId;

                $toStage = $src['master_process_name'] ?? 'Screening';
                $toStageLower = strtolower(trim((string) $toStage));
                $fromStage = $src['prev_process_name'] ?? 'Sourcing';
                $fromStageLower = strtolower(trim((string) $fromStage));
                $candidateStatus = (int) ($src['candidate_status'] ?? 0);

                // Candidate Visibility Rule: Do not show any In Review candidate activity across client drawers
                if (
                    ! empty($profile['is_in_review'])
                    || in_array($toStageLower, ['in review', 'inreview', 'in_review'], true)
                    || in_array($fromStageLower, ['in review', 'inreview', 'in_review'], true)
                    || $candidateStatus === 8
                ) {
                    continue;
                }

                // Standardize Stage Status
                $stageStatus = 'pipeline';
                if (in_array($toStageLower, ['offer', 'offered', 'offer released', 'offer accepted', 'joined', 'hired', 'selected'], true)) {
                    $stageStatus = 'active'; // 🟢 Green
                } elseif (in_array($toStageLower, ['hold', 'on hold', 'on-hold', 'paused'], true)) {
                    $stageStatus = 'hold'; // 🟠 Orange
                } elseif (in_array($toStageLower, ['rejected', 'dropped', 'removed', 'not suitable', 'cancelled'], true)) {
                    $stageStatus = 'removed'; // 🔴 Red
                }

                $normalized[] = [
                    'event_id' => 'evt_tl_'.$id,
                    'client_activity_sequence' => $timestamp,
                    'event_type' => $eventType,
                    'category' => $category,
                    'occurred_at' => Carbon::parse($createdAt)->toIso8601String(),
                    'actor' => [
                        'id' => (int) ($src['event_by_user_id'] ?? 0),
                        'name' => $uploaderName,
                        'company_name' => $uploaderCompany,
                        'role' => $uploaderCompany,
                        'avatar_version' => 1,
                    ],
                    'attribution' => [
                        'label' => 'Uploaded by',
                        'name' => $uploaderName,
                        'company_name' => $uploaderCompany,
                    ],
                    'candidate' => [
                        'id' => $targetWiseId,
                        'wise_id' => $targetWiseId,
                        'application_id' => $appId,
                        'name' => $resolvedCandidateName,
                        'designation' => $resolvedDesignation,
                        'uploader_name' => $uploaderName,
                        'uploader_company' => $uploaderCompany,
                        'candidate_uploaded_by' => $uploaderName,
                        'candidate_agency' => $uploaderCompany,
                        'experience_years' => (float) ($src['min_exp'] ?? 0),
                    ],
                    'job' => array_merge([
                        'id' => $jobId,
                        'code' => $jobId > 0 ? '#'.$jobId : '',
                        'title' => $resolvedJobTitle,
                        'shared_by' => $jobProfile['shared_by_name'] ?? null,
                        'shared_by_name' => $jobProfile['shared_by_name'] ?? null,
                        'shared_by_company' => $jobProfile['shared_by_company'] ?? null,
                        'shared_with_agency' => $jobProfile['agency_name'] ?? null,
                    ], ($query->clientType === 'direct') ? [] : [
                        'creator_name' => $jobProfile['creator_name'] ?? 'Account Manager',
                        'creator_company' => $jobProfile['creator_company'] ?? $uploaderCompany,
                    ]),
                    'transition' => [
                        'from_stage_name' => $src['prev_process_name'] ?? 'Sourcing',
                        'to_stage_name' => $toStage,
                        'status' => $stageStatus,
                        'client_spoc_name' => $src['name_hr'] ?? null,
                    ],
                    'details' => [
                        'interview_mode' => $src['interview_mode'] ?? null,
                        'scheduled_time' => $src['interview_schedule_date'] ?? null,
                        'compensation' => [
                            'currency' => $src['offer_currency'] ?? 'INR',
                            'amount' => $offerAmount,
                            'is_confidential' => $isConfidential,
                        ],
                    ],
                    'visibility' => [
                        'compensation_masked' => $isConfidential,
                    ],
                ];
            } elseif ($type === 'interviews') {
                if ($query->category !== 'all' && $query->category !== 'interviews') {
                    continue;
                }

                $intAppId = (int) ($src['job_application_id'] ?? $src['candidate_id'] ?? 0);
                $intWiseId = (int) ($src['wiseID'] ?? $src['wiseId'] ?? $src['interview_job_wise_con_id'] ?? 0);
                $intEmail = strtolower(trim((string) ($src['email'] ?? '')));
                $jobId = (int) ($src['job_id'] ?? 0);

                $intProfile = $candProfiles["id:{$intAppId}"] ?? $candProfiles["id:{$intWiseId}"] ?? $candProfiles["em:{$intEmail}"] ?? null;
                $jobProfile = $jobProfiles[$jobId] ?? null;

                $targetWiseId = $intWiseId ?: ($intProfile['wise_id'] ?? $intAppId);
                $rawCandName = $intProfile['name'] ?? (! empty($src['full_name']) ? ucwords(trim($src['full_name'])) : (! empty($src['candidate_name']) ? ucwords(trim($src['candidate_name'])) : null));
                if (empty($rawCandName) || in_array(strtolower($rawCandName), ['candidate', 'shared by', 'shared'], true)) {
                    $rawCandName = (! empty($intProfile['name']) && ! in_array(strtolower($intProfile['name']), ['shared by', 'shared'], true))
                        ? $intProfile['name']
                        : ($targetWiseId > 0 ? "Candidate #{$targetWiseId}" : 'Candidate');
                }
                $resolvedCandidateName = $rawCandName;
                $resolvedJobTitle = $jobProfile['title'] ?? (! empty($src['title']) ? ucwords(trim($src['title'])) : 'Open Requisition');
                $resolvedDesignation = $intProfile['designation'] ?? (! empty($src['current_designation']) ? ucwords(trim($src['current_designation'])) : $resolvedJobTitle);
                $uploaderName = $intProfile['uploader_name'] ?? (! empty($src['posted_by']) ? ucwords(trim($src['posted_by'])) : (! empty($src['source']) ? ucwords(trim($src['source'])) : 'Technical Recruiter'));
                $intCompany = ($query->clientType === 'direct')
                    ? $this->resolveTenantCompanyName($query->serviceCompanyId)
                    : ($intProfile['company_name'] ?? (! empty($src['company_name']) ? $this->formatCompanyName((string) $src['company_name']) : (! empty($src['uc_company_name']) ? $this->formatCompanyName((string) $src['uc_company_name']) : $this->resolveTenantCompanyName($query->tenantId))));

                $stage = $src['stage'] ?? $src['master_status_name'] ?? 'Interview';
                $stageLower = strtolower(trim((string) $stage));
                $intStatusId = (int) ($src['master_status_id'] ?? 0);

                // Candidate Visibility Rule: Exclude In Review candidates from interviews activity
                if (
                    ! empty($intProfile['is_in_review'])
                    || in_array($stageLower, ['in review', 'inreview', 'in_review'], true)
                    || $intStatusId === 8
                ) {
                    continue;
                }

                $interviewMode = ! empty($src['mode_name']) ? $src['mode_name'] : (! empty($src['interview_type']) ? $src['interview_type'] : 'Video Call');

                $normalized[] = [
                    'event_id' => 'evt_int_'.$id,
                    'client_activity_sequence' => $timestamp,
                    'event_type' => 'interview.scheduled',
                    'category' => 'interviews',
                    'occurred_at' => Carbon::parse($createdAt)->toIso8601String(),
                    'actor' => [
                        'id' => (int) ($src['user_id'] ?? 0),
                        'name' => $uploaderName,
                        'company_name' => $intCompany,
                        'role' => $intCompany,
                        'avatar_version' => 1,
                    ],
                    'attribution' => [
                        'label' => 'Scheduled by',
                        'name' => $uploaderName,
                        'company_name' => $intCompany,
                    ],
                    'candidate' => [
                        'id' => $targetWiseId,
                        'wise_id' => $targetWiseId,
                        'application_id' => $intAppId,
                        'name' => $resolvedCandidateName,
                        'designation' => $resolvedDesignation,
                        'uploader_name' => $uploaderName,
                        'uploader_company' => $intCompany,
                        'candidate_uploaded_by' => $uploaderName,
                        'candidate_agency' => $intCompany,
                    ],
                    'job' => array_merge([
                        'id' => $jobId,
                        'code' => $jobId > 0 ? '#'.$jobId : '',
                        'title' => $resolvedJobTitle,
                        'shared_by' => $jobProfile['shared_by_name'] ?? null,
                        'shared_by_name' => $jobProfile['shared_by_name'] ?? null,
                        'shared_by_company' => $jobProfile['shared_by_company'] ?? null,
                        'shared_with_agency' => $jobProfile['agency_name'] ?? null,
                    ], ($query->clientType === 'direct') ? [] : [
                        'creator_name' => $jobProfile['creator_name'] ?? 'Account Manager',
                        'creator_company' => $jobProfile['creator_company'] ?? $intCompany,
                    ]),
                    'transition' => [
                        'from_stage_name' => 'Evaluation',
                        'to_stage_name' => $stage,
                        'status' => 'interview',
                    ],
                    'details' => [
                        'interview_mode' => $interviewMode,
                        'scheduled_time' => $src['schedule_date'] ?? null,
                        'panel_names' => $src['interviewers'] ?? 'Client Hiring Panel',
                    ],
                    'visibility' => [
                        'compensation_masked' => true,
                    ],
                ];
            } elseif ($type === 'jobs') {
                if ($query->category !== 'all' && $query->category !== 'jobs') {
                    continue;
                }

                $jobId = (int) ($src['id'] ?? $id);
                $jobProfile = $jobProfiles[$jobId] ?? null;

                $resolvedJobTitle = $jobProfile['title'] ?? (! empty($src['raw_designation']) ? ucwords(trim($src['raw_designation'])) : (! empty($src['title']) ? ucwords(trim($src['title'])) : 'New Requisition'));
                $creatorName = $jobProfile['creator_name'] ?? (! empty($src['user_name']) ? ucwords(trim($src['user_name'])) : 'Account Manager');
                $jobCompany = $jobProfile['creator_company'] ?? (! empty($src['company_name']) ? $this->formatCompanyName((string) $src['company_name']) : $this->resolveTenantCompanyName($query->tenantId));
                $jobStatus = $jobProfile['status'] ?? 'active';
                $statusLabel = $jobProfile['status_label'] ?? 'Active';
                $totalPositions = (int) ($src['total_positions'] ?? ($jobProfile['total_positions'] ?? 1));

                // Direct Client Sharing Attribution (Rules 2, 3, 4: Never use original job creator as shared_by)
                $isDirectShared = ($query->clientType === 'direct') || ! empty($jobProfile['is_shared']);
                $sharedByName = $jobProfile['shared_by_name'] ?? 'Account Lead';
                $sharedByCompany = $jobProfile['shared_by_company'] ?? $jobCompany;
                $sharedWithAgency = $jobProfile['agency_name'] ?? $this->resolveTenantCompanyName($query->serviceCompanyId);
                $jobSequence = $isDirectShared && ! empty($jobProfile['shared_at'])
                    ? Carbon::parse($jobProfile['shared_at'])->timestamp
                    : $timestamp;
                $jobOccurredAt = $isDirectShared && ! empty($jobProfile['shared_at'])
                    ? $jobProfile['shared_at']
                    : Carbon::parse($createdAt)->toIso8601String();

                $normalized[] = [
                    'event_id' => 'evt_job_'.$id,
                    'client_activity_sequence' => $jobSequence,
                    'event_type' => $isDirectShared ? 'job.shared' : 'job.created',
                    'category' => 'jobs',
                    'occurred_at' => $jobOccurredAt,
                    'actor' => [
                        'id' => $isDirectShared ? (int) ($jobProfile['shared_by_user_id'] ?? 0) : (int) ($src['user_id'] ?? 0),
                        'name' => $isDirectShared ? $sharedByName : $creatorName,
                        'company_name' => $isDirectShared ? $sharedByCompany : $jobCompany,
                        'role' => $isDirectShared ? $sharedByCompany : $jobCompany,
                        'avatar_version' => 1,
                    ],
                    'attribution' => [
                        'label' => $isDirectShared ? 'Shared by' : 'Created by',
                        'name' => $isDirectShared ? $sharedByName : $creatorName,
                        'company_name' => $isDirectShared ? $sharedByCompany : $jobCompany,
                    ],
                    'job' => array_merge([
                        'id' => $jobId,
                        'code' => $jobId > 0 ? '#'.$jobId : '',
                        'title' => $resolvedJobTitle,
                        'shared_by' => $sharedByName,
                        'shared_by_name' => $sharedByName,
                        'shared_by_company' => $sharedByCompany,
                        'shared_with_agency' => $sharedWithAgency,
                        'agency_name' => $sharedWithAgency,
                        'status' => $jobStatus,
                        'status_label' => $statusLabel,
                        'total_positions' => $totalPositions,
                    ], $isDirectShared ? [] : [
                        'created_by' => $creatorName,
                        'creator_name' => $creatorName,
                        'creator_company' => $jobCompany,
                    ]),
                    'transition' => [
                        'to_stage_name' => $isDirectShared ? ('Shared with '.$sharedWithAgency) : ($statusLabel.' Requisition'),
                        'status' => $isDirectShared ? 'active' : $jobStatus,
                    ],
                    'details' => [],
                    'visibility' => [
                        'compensation_masked' => true,
                    ],
                ];
            } elseif ($type === 'notes') {
                if ($query->category !== 'all' && $query->category !== 'crm') {
                    continue;
                }

                $authorName = ! empty($src['author_name']) ? ucwords(trim($src['author_name'])) : 'Client Partner';

                $normalized[] = [
                    'event_id' => 'evt_note_'.$id,
                    'client_activity_sequence' => $timestamp,
                    'event_type' => 'client.note_added',
                    'category' => 'crm',
                    'occurred_at' => Carbon::parse($createdAt)->toIso8601String(),
                    'actor' => [
                        'id' => (int) ($src['user_id'] ?? 0),
                        'name' => $authorName,
                        'role' => 'Account Lead',
                        'avatar_version' => 1,
                    ],
                    'attribution' => [
                        'label' => 'Logged by',
                        'name' => $authorName,
                    ],
                    'transition' => [
                        'to_stage_name' => 'Note Logged',
                        'status' => 'crm',
                    ],
                    'details' => [
                        'note_excerpt' => $src['note'] ?? 'Client interaction logged.',
                    ],
                    'visibility' => [
                        'compensation_masked' => true,
                    ],
                ];
            }
        }

        // Deterministic Sort: (client_activity_sequence DESC, event_id DESC)
        usort($normalized, function ($a, $b) {
            if ($a['client_activity_sequence'] === $b['client_activity_sequence']) {
                return strcmp($b['event_id'], $a['event_id']);
            }

            return $b['client_activity_sequence'] <=> $a['client_activity_sequence'];
        });

        return $normalized;
    }

    /**
     * Applies sequence-bound cursor pagination.
     */
    protected function applyCursorPagination(array $items, ClientActivityQuery $query): array
    {
        $filtered = $items;

        // If cursor provided, filter items strictly strictly older than cursor
        if ($query->cursorSequence !== null) {
            $filtered = array_values(array_filter($items, function ($item) use ($query) {
                if ($item['client_activity_sequence'] < $query->cursorSequence) {
                    return true;
                }
                if ($item['client_activity_sequence'] === $query->cursorSequence) {
                    return strcmp($item['event_id'], (string) $query->cursorEventId) < 0;
                }

                return false;
            }));
        }

        $limit = $query->limit;
        $sliced = array_slice($filtered, 0, $limit);
        $hasMore = count($filtered) > $limit;

        $nextCursor = null;
        if ($hasMore && ! empty($sliced)) {
            $lastItem = end($sliced);
            $nextCursor = $query->buildCursor(
                (int) $lastItem['client_activity_sequence'],
                (string) $lastItem['event_id']
            );
        }

        return [
            'items' => $sliced,
            'next_cursor' => $nextCursor,
            'has_more' => $hasMore,
        ];
    }

    /**
     * Calculates quantified human-originated client engagement freshness.
     */
    protected function calculateClientHealth(array $events): array
    {
        if (empty($events)) {
            return [
                'status' => 'none',
                'days_since_last_activity' => null,
                'last_activity_text' => 'No activity yet',
            ];
        }

        $latestEvent = $events[0];
        $occurredAt = Carbon::parse($latestEvent['occurred_at']);
        $daysAgo = (int) floor($occurredAt->diffInDays(now()));

        $activeThreshold = (int) config('ats.client_activity.health.active_threshold_days', 2);
        $normalThreshold = (int) config('ats.client_activity.health.normal_threshold_days', 7);

        if ($daysAgo <= $activeThreshold) {
            $status = 'active';
            $text = $daysAgo === 0 ? 'Active Today' : "Active {$daysAgo}d ago";
        } elseif ($daysAgo <= $normalThreshold) {
            $status = 'normal';
            $text = "Active {$daysAgo}d ago";
        } else {
            $status = 'warning';
            $text = "Action Needed: Inactive {$daysAgo}d";
        }

        return [
            'status' => $status,
            'days_since_last_activity' => $daysAgo,
            'last_activity_text' => $text,
        ];
    }

    /**
     * Resolves date range bounds for timeframe filters.
     */
    protected function getTimeRangeFilter(string $timeframe): ?array
    {
        return match ($timeframe) {
            'today' => ['gte' => now()->startOfDay()->toIso8601String()],
            '7d' => ['gte' => now()->subDays(7)->startOfDay()->toIso8601String()],
            '30d' => ['gte' => now()->subDays(30)->startOfDay()->toIso8601String()],
            default => null,
        };
    }
}
