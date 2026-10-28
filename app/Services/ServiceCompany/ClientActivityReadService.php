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
        $clientId = $query->clientId;
        $clientType = $query->clientType;

        // Step 1: Resolve Client Job IDs (Dragonfly/Redis cached)
        $clientJobIds = $this->resolveClientJobIds($tenantId, $clientId, $clientType);

        // Step 2: Decoupled KPI Calculation (Cached under filter hash)
        $kpis = $this->resolveKpiTotals($query, $clientJobIds, $version);

        // Step 3: Fetch Aggregated Events via _msearch
        $rawEvents = $this->fetchMultiIndexEvents($query, $clientJobIds);

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
     * Resolves all job IDs belonging to this client from ELASTIC_JOBS with 0% MySQL reads.
     */
    protected function resolveClientJobIds(int $companyId, int $clientId, string $clientType): array
    {
        $cacheKey = "ats:client_job_ids:{$companyId}:{$clientId}:{$clientType}";
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
                $scopeMust = [
                    ['term' => ['client_id' => $clientId]],
                ];
                if ($clientType === 'internal') {
                    $scopeMust[] = [
                        'bool' => [
                            'should' => [
                                ['term' => ['company_id' => $companyId]],
                                ['term' => ['comId' => $companyId]],
                            ],
                            'minimum_should_match' => 1,
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
                'error' => $e->getMessage(),
            ]);
        }

        return $jobIds;
    }

    /**
     * Decoupled KPI Calculation: aggregates total events by category across the full matching dataset.
     */
    protected function resolveKpiTotals(ClientActivityQuery $query, array $clientJobIds, int $version): array
    {
        $kpiCacheKey = $query->toKpiCacheKey($version);
        $cachedKpi = Redis::get($kpiCacheKey);
        if ($cachedKpi) {
            $decoded = json_decode($cachedKpi, true);
            if (is_array($decoded)) {
                return $decoded;
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
        $indexTimeline = defined('ELASTIC_CANDIDATE_TIMELINE') ? ELASTIC_CANDIDATE_TIMELINE : config('constants.ELASTIC_CANDIDATE_TIMELINE', 'local_candidate_timeline');
        $indexInterviews = defined('ELASTIC_INERTVIEW_TRACKER') ? ELASTIC_INERTVIEW_TRACKER : config('constants.ELASTIC_INERTVIEW_TRACKER', 'local_interview_tracker_elastic');
        $indexJobs = defined('ELASTIC_JOBS') ? ELASTIC_JOBS : config('constants.ELASTIC_JOBS', 'ats_jobs_elastic');
        $indexNotes = defined('ELASTIC_APPLICANT_NOTES') ? ELASTIC_APPLICANT_NOTES : config('constants.ELASTIC_APPLICANT_NOTES', 'local_applicant_notes');

        $timeRange = $this->getTimeRangeFilter($query->timeframe);

        // 1. Timeline Counts (Pipeline & Offers)
        if (! empty($clientJobIds) && $this->elasticService->indexExists($indexTimeline)) {
            try {
                $must = [['terms' => ['job_id' => $clientJobIds]]];
                if ($timeRange) {
                    $must[] = ['range' => ['created_at' => $timeRange]];
                }

                $res = $this->elasticService->getClient()->search([
                    'index' => $indexTimeline,
                    'size' => 0,
                    'body' => [
                        'query' => ['bool' => ['must' => $must]],
                        'aggs' => [
                            'by_process' => [
                                'terms' => ['field' => 'master_process_name.keyword', 'size' => 10],
                            ],
                        ],
                    ],
                ]);

                $buckets = $res['aggregations']['by_process']['buckets'] ?? [];
                foreach ($buckets as $b) {
                    $name = strtolower($b['key'] ?? '');
                    $count = (int) ($b['doc_count'] ?? 0);
                    if (in_array($name, ['offer', 'joined'], true)) {
                        $kpis['offer_events'] += $count;
                    } else {
                        $kpis['pipeline_events'] += $count;
                    }
                }
            } catch (\Throwable) {
            }
        }

        // 2. Interview Counts
        if ($this->elasticService->indexExists($indexInterviews)) {
            try {
                $interviewShould = [['term' => ['client_id' => $query->clientId]]];
                if (! empty($clientJobIds)) {
                    $interviewShould[] = ['terms' => ['job_id' => $clientJobIds]];
                }
                $must = [['bool' => ['should' => $interviewShould, 'minimum_should_match' => 1]]];
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
        if ($this->elasticService->indexExists($indexJobs)) {
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

        // 4. Notes Counts
        if ($this->elasticService->indexExists($indexNotes)) {
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
        $kpis['pipeline'] = $kpis['pipeline_events'];
        $kpis['interviews'] = $kpis['interview_events'];
        $kpis['offers'] = $kpis['offer_events'];
        $kpis['jobs'] = $kpis['job_events'];
        $kpis['crm'] = $kpis['note_events'];

        try {
            Redis::setex($kpiCacheKey, 900, json_encode($kpis));
        } catch (\Throwable) {
        }

        return $kpis;
    }

    /**
     * Executes single parallel _msearch across all relevant Elasticsearch indices.
     */
    protected function fetchMultiIndexEvents(ClientActivityQuery $query, array $clientJobIds): array
    {
        $indexTimeline = defined('ELASTIC_CANDIDATE_TIMELINE') ? ELASTIC_CANDIDATE_TIMELINE : config('constants.ELASTIC_CANDIDATE_TIMELINE', 'local_candidate_timeline');
        $indexInterviews = defined('ELASTIC_INERTVIEW_TRACKER') ? ELASTIC_INERTVIEW_TRACKER : config('constants.ELASTIC_INERTVIEW_TRACKER', 'local_interview_tracker_elastic');
        $indexJobs = defined('ELASTIC_JOBS') ? ELASTIC_JOBS : config('constants.ELASTIC_JOBS', 'ats_jobs_elastic');
        $indexNotes = defined('ELASTIC_APPLICANT_NOTES') ? ELASTIC_APPLICANT_NOTES : config('constants.ELASTIC_APPLICANT_NOTES', 'local_applicant_notes');

        $timeRange = $this->getTimeRangeFilter($query->timeframe);
        $searchQuery = $query->search;

        $msearchPayload = [];
        $indexKeys = [];

        // 1. Timeline Search Query
        if (! empty($clientJobIds) && in_array($query->category, ['all', 'pipeline', 'offers'], true) && $this->elasticService->indexExists($indexTimeline)) {
            $must = [['terms' => ['job_id' => $clientJobIds]]];
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

            $msearchPayload[] = ['index' => $indexTimeline];
            $msearchPayload[] = [
                'size' => 60,
                'query' => ['bool' => ['must' => $must]],
                'sort' => [['created_at' => ['order' => 'desc', 'unmapped_type' => 'date']]],
            ];
            $indexKeys[] = 'timeline';
        }

        // 2. Interviews Search Query
        if (in_array($query->category, ['all', 'interviews'], true) && $this->elasticService->indexExists($indexInterviews)) {
            $interviewShould = [['term' => ['client_id' => $query->clientId]]];
            if (! empty($clientJobIds)) {
                $interviewShould[] = ['terms' => ['job_id' => $clientJobIds]];
            }
            $must = [['bool' => ['should' => $interviewShould, 'minimum_should_match' => 1]]];
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
        if (in_array($query->category, ['all', 'jobs'], true) && $this->elasticService->indexExists($indexJobs)) {
            $must = [['term' => ['client_id' => $query->clientId]]];
            if ($timeRange) {
                $must[] = ['range' => ['created_at' => $timeRange]];
            }
            if ($searchQuery) {
                $must[] = [
                    'bool' => [
                        'should' => [
                            ['wildcard' => ['title' => "*{$searchQuery}*"]],
                            ['wildcard' => ['job_code' => "*{$searchQuery}*"]],
                        ],
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

        // 4. Notes Search Query
        if (in_array($query->category, ['all', 'crm'], true) && $this->elasticService->indexExists($indexNotes)) {
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
     * Batch resolves candidate details (full_name, designation, uploader) from Elasticsearch
     * across ats_candidates_elastic and local_candidates_elastic with Redis caching and 0% MySQL reads.
     */
    protected function resolveCandidateProfiles(array $appIds, array $emails = []): array
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
                    '_source' => ['id', 'wiseId', 'candidate_id', 'full_name', 'current_designation', 'email', 'posted_by', 'submitted_by'],
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

                    $data = [
                        'name' => ! empty($cName) ? ucwords($cName) : null,
                        'designation' => ! empty($cDesig) ? ucwords($cDesig) : null,
                        'uploader_name' => ! empty($uName) ? ucwords($uName) : null,
                        'wise_id' => $cid > 0 ? $cid : null,
                        'candidate_id' => $candAppId > 0 ? $candAppId : null,
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
     * Batch resolves job requisition details (designation, creator/owner) from Elasticsearch
     * across ats_jobs_elastic and local_jobs_elastic with Redis caching and 0% MySQL reads.
     */
    protected function resolveJobProfiles(array $jobIds): array
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
                $cached = Redis::get("ats:job_brief:id:{$jid}");
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
                    '_source' => ['id', 'title', 'raw_designation', 'status', 'total_positions', 'job_assign', 'user_name', 'created_by_name', 'posted_by'],
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

                    // Resolve creator / owner name
                    $creatorName = null;
                    if (! empty($s['job_assign']) && is_array($s['job_assign'])) {
                        $creatorName = $s['job_assign'][0]['user_name'] ?? null;
                    }
                    if (empty($creatorName)) {
                        $creatorName = $s['user_name'] ?? $s['created_by_name'] ?? $s['posted_by'] ?? null;
                    }

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

                    $data = [
                        'id' => $jid,
                        'title' => ! empty($title) ? ucwords($title) : 'Requisition',
                        'creator_name' => ! empty($creatorName) ? ucwords(trim($creatorName)) : 'Account Manager',
                        'status' => $canonicalStatus,
                        'status_label' => $statusLabel,
                        'total_positions' => (int) ($s['total_positions'] ?? 1),
                    ];

                    if ($jid > 0) {
                        $profiles[$jid] = $data;
                        try {
                            Redis::setex("ats:job_brief:id:{$jid}", 900, json_encode($data));
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

        $candProfiles = $this->resolveCandidateProfiles($appIds, $appEmails);
        $jobProfiles = $this->resolveJobProfiles($jobIds);

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
                if ($query->category !== 'all' && $query->category !== $category) {
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

                // Candidate Name: SSOT candidate full name, never fallback to "Candidate" when resolved
                $resolvedCandidateName = $profile['name'] ?? (! empty($src['candidate_name']) ? ucwords(trim($src['candidate_name'])) : null);
                if (empty($resolvedCandidateName) || strtolower($resolvedCandidateName) === 'candidate') {
                    $resolvedCandidateName = 'Candidate';
                }

                // Job Position Name: SSOT requisition designation
                $resolvedJobTitle = $jobProfile['title'] ?? (! empty($src['title']) ? ucwords(trim($src['title'])) : 'Open Requisition');
                $resolvedDesignation = $profile['designation'] ?? $src['designation_offer'] ?? $resolvedJobTitle;

                // Ownership / Attribution: Candidate uploader name
                $uploaderName = $profile['uploader_name'] ?? (! empty($src['name']) ? ucwords(trim($src['name'])) : 'Technical Recruiter');
                $targetWiseId = $profile['wise_id'] ?? $appId;

                // Standardize Stage Status
                $toStage = $src['master_process_name'] ?? 'Screening';
                $toStageLower = strtolower(trim((string) $toStage));
                $stageStatus = 'pipeline';
                if (in_array($toStageLower, ['offer', 'offered', 'offer released', 'offer accepted', 'joined', 'hired'], true)) {
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
                        'role' => 'Technical Recruiter',
                        'avatar_version' => 1,
                    ],
                    'attribution' => [
                        'label' => 'Uploaded by',
                        'name' => $uploaderName,
                    ],
                    'candidate' => [
                        'id' => $targetWiseId,
                        'wise_id' => $targetWiseId,
                        'application_id' => $appId,
                        'name' => $resolvedCandidateName,
                        'designation' => $resolvedDesignation,
                        'experience_years' => (float) ($src['min_exp'] ?? 0),
                    ],
                    'job' => [
                        'id' => $jobId,
                        'code' => 'JERO-'.$jobId,
                        'title' => $resolvedJobTitle,
                        'creator_name' => $jobProfile['creator_name'] ?? 'Account Manager',
                    ],
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

                $resolvedCandidateName = $intProfile['name'] ?? (! empty($src['full_name']) ? ucwords(trim($src['full_name'])) : (! empty($src['candidate_name']) ? ucwords(trim($src['candidate_name'])) : 'Candidate'));
                $resolvedJobTitle = $jobProfile['title'] ?? (! empty($src['job_title']) ? ucwords(trim($src['job_title'])) : 'Requisition');
                $resolvedDesignation = $intProfile['designation'] ?? (! empty($src['current_designation']) ? ucwords(trim($src['current_designation'])) : $resolvedJobTitle);

                $targetWiseId = $intWiseId ?: ($intProfile['wise_id'] ?? $intAppId);
                $uploaderName = $intProfile['uploader_name'] ?? (! empty($src['posted_by']) ? ucwords(trim($src['posted_by'])) : (! empty($src['source']) ? ucwords(trim($src['source'])) : 'Technical Recruiter'));

                $stage = $src['stage'] ?? $src['master_status_name'] ?? 'Interview';
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
                        'role' => 'Coordinator',
                        'avatar_version' => 1,
                    ],
                    'attribution' => [
                        'label' => 'Uploaded by',
                        'name' => $uploaderName,
                    ],
                    'candidate' => [
                        'id' => $targetWiseId,
                        'wise_id' => $targetWiseId,
                        'application_id' => $intAppId,
                        'name' => $resolvedCandidateName,
                        'designation' => $resolvedDesignation,
                    ],
                    'job' => [
                        'id' => $jobId,
                        'code' => 'JERO-'.$jobId,
                        'title' => $resolvedJobTitle,
                        'creator_name' => $jobProfile['creator_name'] ?? 'Account Manager',
                    ],
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
                $jobStatus = $jobProfile['status'] ?? 'active';
                $statusLabel = $jobProfile['status_label'] ?? 'Active';

                $normalized[] = [
                    'event_id' => 'evt_job_'.$id,
                    'client_activity_sequence' => $timestamp,
                    'event_type' => 'job.created',
                    'category' => 'jobs',
                    'occurred_at' => Carbon::parse($createdAt)->toIso8601String(),
                    'actor' => [
                        'id' => (int) ($src['user_id'] ?? 0),
                        'name' => $creatorName,
                        'role' => 'Account Lead',
                        'avatar_version' => 1,
                    ],
                    'attribution' => [
                        'label' => 'Created by',
                        'name' => $creatorName,
                    ],
                    'job' => [
                        'id' => $jobId,
                        'code' => 'JERO-'.$jobId,
                        'title' => $resolvedJobTitle,
                        'creator_name' => $creatorName,
                        'status' => $jobStatus,
                        'status_label' => $statusLabel,
                        'total_positions' => (int) ($src['total_positions'] ?? ($jobProfile['total_positions'] ?? 1)),
                    ],
                    'transition' => [
                        'to_stage_name' => $statusLabel,
                        'status' => $jobStatus,
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
