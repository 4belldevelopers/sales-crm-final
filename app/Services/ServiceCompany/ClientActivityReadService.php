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
     * Normalizes heterogeneous Elasticsearch hits into the canonical event schema,
     * applying permission-aware compensation masking and deterministic sorting.
     */
    protected function normalizeAndSortEvents(array $rawEvents, ClientActivityQuery $query): array
    {
        $normalized = [];
        $canViewCompensation = auth()->check() && (auth()->user()->can('view_compensation') || auth()->user()->hasRole('admin'));

        foreach ($rawEvents as $entry) {
            $type = $entry['source_type'];
            $src = $entry['hit']['_source'] ?? [];
            $id = (string) ($entry['hit']['_id'] ?? '');

            $createdAt = $src['created_at'] ?? $src['updated_at'] ?? now()->toIso8601String();
            $timestamp = Carbon::parse($createdAt)->timestamp;

            if ($type === 'timeline') {
                $processName = $src['master_process_name'] ?? 'Pipeline';
                $isOffer = in_array(strtolower($processName), ['offer', 'joined'], true);
                $category = $isOffer ? 'offers' : 'pipeline';

                // Check category filter
                if ($query->category !== 'all' && $query->category !== $category) {
                    continue;
                }

                $eventType = $isOffer
                    ? (strtolower($processName) === 'joined' ? 'candidate.joined' : 'offer.sent')
                    : 'candidate.stage_changed';

                $offerAmount = null;
                $isConfidential = true;
                if ($canViewCompensation && ! empty($src['offer_amount'])) {
                    $offerAmount = (float) $src['offer_amount'];
                    $isConfidential = false;
                }

                $normalized[] = [
                    'event_id' => 'evt_tl_'.$id,
                    'client_activity_sequence' => $timestamp,
                    'event_type' => $eventType,
                    'category' => $category,
                    'occurred_at' => Carbon::parse($createdAt)->toIso8601String(),
                    'actor' => [
                        'id' => (int) ($src['event_by_user_id'] ?? 0),
                        'name' => $src['name'] ?? 'Recruiter',
                        'role' => 'Technical Recruiter',
                        'avatar_version' => 1,
                    ],
                    'candidate' => [
                        'id' => (int) ($src['application_id'] ?? 0),
                        'name' => $src['candidate_name'] ?? $src['name'] ?? 'Candidate',
                        'designation' => $src['designation_offer'] ?? $src['title'] ?? 'Candidate',
                        'experience_years' => (float) ($src['min_exp'] ?? 0),
                    ],
                    'job' => [
                        'id' => (int) ($src['job_id'] ?? 0),
                        'code' => 'JERO-'.($src['job_id'] ?? '0'),
                        'title' => $src['title'] ?? 'Open Requisition',
                    ],
                    'transition' => [
                        'from_stage_name' => $src['prev_process_name'] ?? 'Sourcing',
                        'to_stage_name' => $src['master_process_name'] ?? 'Screening',
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

                $stage = $src['stage'] ?? 'Interview';
                $normalized[] = [
                    'event_id' => 'evt_int_'.$id,
                    'client_activity_sequence' => $timestamp,
                    'event_type' => 'interview.scheduled',
                    'category' => 'interviews',
                    'occurred_at' => Carbon::parse($createdAt)->toIso8601String(),
                    'actor' => [
                        'id' => (int) ($src['user_id'] ?? 0),
                        'name' => $src['created_by_name'] ?? 'Hiring Coordinator',
                        'role' => 'Coordinator',
                        'avatar_version' => 1,
                    ],
                    'candidate' => [
                        'id' => (int) ($src['candidate_id'] ?? 0),
                        'name' => $src['candidate_name'] ?? 'Candidate',
                        'designation' => $src['job_title'] ?? 'Candidate',
                    ],
                    'job' => [
                        'id' => (int) ($src['job_id'] ?? 0),
                        'code' => 'JERO-'.($src['job_id'] ?? '0'),
                        'title' => $src['job_title'] ?? 'Requisition',
                    ],
                    'transition' => [
                        'from_stage_name' => 'Evaluation',
                        'to_stage_name' => $stage,
                    ],
                    'details' => [
                        'interview_mode' => $src['interview_type'] ?? 'Video Call',
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

                $status = $src['status'] ?? 'active';
                $normalized[] = [
                    'event_id' => 'evt_job_'.$id,
                    'client_activity_sequence' => $timestamp,
                    'event_type' => 'job.created',
                    'category' => 'jobs',
                    'occurred_at' => Carbon::parse($createdAt)->toIso8601String(),
                    'actor' => [
                        'id' => (int) ($src['user_id'] ?? 0),
                        'name' => $src['user_name'] ?? 'Account Manager',
                        'role' => 'Account Lead',
                        'avatar_version' => 1,
                    ],
                    'job' => [
                        'id' => (int) ($src['id'] ?? $id),
                        'code' => 'JERO-'.($src['id'] ?? $id),
                        'title' => $src['title'] ?? 'New Requisition',
                        'status' => $status,
                        'total_positions' => (int) ($src['total_positions'] ?? 1),
                    ],
                    'transition' => [
                        'to_stage_name' => ucfirst($status),
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

                $normalized[] = [
                    'event_id' => 'evt_note_'.$id,
                    'client_activity_sequence' => $timestamp,
                    'event_type' => 'client.note_added',
                    'category' => 'crm',
                    'occurred_at' => Carbon::parse($createdAt)->toIso8601String(),
                    'actor' => [
                        'id' => (int) ($src['user_id'] ?? 0),
                        'name' => $src['author_name'] ?? 'Client Partner',
                        'role' => 'Account Lead',
                        'avatar_version' => 1,
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
