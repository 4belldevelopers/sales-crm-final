<?php

namespace App\Services\Analytics;

use App\Services\Analytics\Contracts\PerformanceStatusMap;
use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * ClientPerformanceAnalyticsService
 *
 * Master orchestrator for the Client Performance Tab analytics dashboard.
 * Implements CQRS (100% Elasticsearch reads, 0% MySQL reads on web/API paths),
 * DragonflyDB L2 caching, cardinality deduplication, and the canonical
 * ClientPerformanceDashboard SSOT contract.
 */
class ClientPerformanceAnalyticsService
{
    protected ElasticService $elasticService;

    protected ClientPerformanceScopeResolver $scopeResolver;

    protected ClientPerformanceDateRangeResolver $dateResolver;

    protected ClientPerformanceQueryBuilder $queryBuilder;

    protected ClientPerformanceInsightEngine $insightEngine;

    protected ClientPerformanceCacheManager $cacheManager;

    public function __construct(
        ElasticService $elasticService,
        ClientPerformanceScopeResolver $scopeResolver,
        ClientPerformanceDateRangeResolver $dateResolver,
        ClientPerformanceQueryBuilder $queryBuilder,
        ClientPerformanceInsightEngine $insightEngine,
        ClientPerformanceCacheManager $cacheManager
    ) {
        $this->elasticService = $elasticService;
        $this->scopeResolver = $scopeResolver;
        $this->dateResolver = $dateResolver;
        $this->queryBuilder = $queryBuilder;
        $this->insightEngine = $insightEngine;
        $this->cacheManager = $cacheManager;
    }

    /**
     * Generate the complete Client Performance Dashboard analytics payload.
     *
     * @param  int  $authCompanyId  Authenticated user's company ID
     * @param  int  $clientId  Requested client ID
     * @param  string  $clientType  'internal', 'direct', or 'auto'
     * @param  string  $range  '30D', '3M', '6M', '12M', 'all'
     * @param  string  $compare  'previous_period', 'previous_year', 'none'
     * @param  string  $timezone  Tenant timezone
     * @param  string|null  $requestId  Optional correlation ID
     * @return array Standardized ClientPerformanceDashboard contract
     */
    public function getPerformanceDashboard(
        int $authCompanyId,
        int $clientId,
        string $clientType = 'auto',
        string $range = '12M',
        string $compare = 'previous_period',
        string $timezone = 'Asia/Kolkata',
        ?string $requestId = null
    ): array {
        $startTime = microtime(true);
        $requestId = $requestId ?: 'req_'.Str::random(16);

        // 1. Resolve & Authorize Scope
        $scope = $this->scopeResolver->resolveScope($authCompanyId, $clientId, $clientType);

        // 2. Resolve Date Boundaries
        $boundaries = $this->dateResolver->resolveBoundaries($range, $compare, $timezone);

        // 3. Check DragonflyDB / Redis Cache
        $cacheKey = $this->cacheManager->buildKey(
            $authCompanyId,
            $scope['client_type'],
            $clientId,
            $boundaries['range'],
            $boundaries['compare'],
            $boundaries['timezone']
        );

        $cachedData = $this->cacheManager->get($cacheKey);
        if ($cachedData !== null) {
            $cachedData['meta']['cache_hit'] = true;
            $cachedData['meta']['request_id'] = $requestId;
            $cachedData['meta']['total_latency_ms'] = round((microtime(true) - $startTime) * 1000, 2);

            return $cachedData;
        }

        // 4. Execute Elasticsearch Aggregations (Cache Miss)
        $esStartTime = microtime(true);

        // Candidate aggregation
        $candidateQuery = $this->queryBuilder->buildCandidateQuery($scope, $boundaries);
        $candidateResponse = $this->executeSearch($scope['indices']['candidates'], $candidateQuery);

        // Previous period aggregation (for baseline delta)
        $previousResponse = null;
        $prevQuery = $this->queryBuilder->buildPreviousPeriodQuery($scope, $boundaries);
        if ($prevQuery) {
            $previousResponse = $this->executeSearch($scope['indices']['candidates'], $prevQuery);
        }

        // Job aggregation
        $jobQuery = $this->queryBuilder->buildJobQuery($scope, $boundaries);
        $jobResponse = $this->executeSearch($scope['indices']['jobs'], $jobQuery);

        // Canonical shared job IDs for interview tracking
        $clientJobIds = ! empty($scope['shared_job_ids'])
            ? $scope['shared_job_ids']
            : array_values(array_filter(array_map(fn ($hit) => (int) ($hit['_source']['id'] ?? 0), $jobResponse['hits']['hits'] ?? [])));

        // Interview scheduled count from interview tracker
        $interviewResponse = null;
        if (! empty($scope['indices']['interviews'])) {
            $interviewQuery = $this->queryBuilder->buildInterviewTrackerQuery($scope, $boundaries, $clientJobIds);
            $interviewResponse = $this->executeSearch($scope['indices']['interviews'], $interviewQuery);
        }

        $esLatencyMs = round((microtime(true) - $esStartTime) * 1000, 2);

        // 5. Transform Aggregation Data into SSOT Contract
        $dashboard = $this->transformDashboard(
            $scope,
            $boundaries,
            $candidateResponse,
            $previousResponse,
            $jobResponse,
            $interviewResponse,
            $requestId,
            $esLatencyMs,
            $startTime
        );

        // 6. Cache transformed payload in DragonflyDB / Redis
        $this->cacheManager->put($cacheKey, $dashboard);

        // 7. Structured Audit Telemetry
        Log::channel('daily')->info('Client Performance Analytics Generated', [
            'request_id' => $requestId,
            'tenant_id' => $authCompanyId,
            'client_id' => $clientId,
            'client_type' => $scope['client_type'],
            'range' => $boundaries['range'],
            'compare' => $boundaries['compare'],
            'es_latency_ms' => $esLatencyMs,
            'total_latency_ms' => $dashboard['meta']['total_latency_ms'] ?? 0,
            'candidates_count' => $dashboard['summary']['candidates']['value'] ?? 0,
            'joins_count' => $dashboard['summary']['joins']['value'] ?? 0,
        ]);

        return $dashboard;
    }

    /**
     * Safely execute an Elasticsearch search request with fallback.
     */
    protected function executeSearch(string $index, array $query): array
    {
        try {
            if (! $this->elasticService->indexExists($index)) {
                return ['hits' => ['total' => ['value' => 0], 'hits' => []], 'aggregations' => []];
            }

            return $this->elasticService->getClient()->search([
                'index' => $index,
                'body' => $query,
            ]);
        } catch (\Throwable $e) {
            Log::warning("Elasticsearch search failed on index {$index}: ".$e->getMessage());

            return ['hits' => ['total' => ['value' => 0], 'hits' => []], 'aggregations' => []];
        }
    }

    /**
     * Transform raw Elasticsearch aggregations into the canonical ClientPerformanceDashboard contract.
     */
    protected function transformDashboard(
        array $scope,
        array $boundaries,
        array $cResp,
        ?array $pResp,
        array $jResp,
        ?array $iResp,
        string $requestId,
        float $esLatencyMs,
        float $startTime
    ): array {
        $cAggs = $cResp['aggregations'] ?? [];
        $pAggs = $pResp['aggregations'] ?? [];
        $jAggs = $jResp['aggregations'] ?? [];
        $iAggs = $iResp['aggregations'] ?? [];

        // Dual Candidate Dimensions: Submissions count (strictly statuses 1..6, excluding In Review 8 and Archived 9) vs Unique candidates
        $candCount = (int) ($cAggs['eligible_submissions']['submissions']['value'] ?? $cAggs['total_submissions']['value'] ?? 0);
        $uniqueCandCount = (int) ($cAggs['eligible_submissions']['unique_count']['value'] ?? $cAggs['unique_candidates']['value'] ?? 0);
        $inReviewCount = (int) ($cAggs['in_review_candidates']['submissions']['value'] ?? 0);
        $inReviewUniqueCount = (int) ($cAggs['in_review_candidates']['unique_count']['value'] ?? 0);
        $screenedCount = (int) ($cAggs['screened_candidates']['unique_count']['value'] ?? 0);
        $interviewCount = (int) ($cAggs['interviewed_candidates']['unique_count']['value'] ?? 0);
        $offerCount = (int) ($cAggs['offered_candidates']['unique_count']['value'] ?? 0);
        $joinCount = (int) ($cAggs['joined_candidates']['unique_count']['value'] ?? 0);
        $scheduledCount = (int) ($iAggs['scheduled_count']['value'] ?? 0);

        // Previous summary counts (strictly statuses 1..6, 0% In Review)
        $prevCand = (int) ($pAggs['eligible_submissions']['submissions']['value'] ?? $pAggs['total_submissions']['value'] ?? 0);
        $prevScreened = (int) ($pAggs['screened_candidates']['unique_count']['value'] ?? 0);
        $prevInterview = (int) ($pAggs['interviewed_candidates']['unique_count']['value'] ?? 0);
        $prevOffer = (int) ($pAggs['offered_candidates']['unique_count']['value'] ?? 0);
        $prevJoin = (int) ($pAggs['joined_candidates']['unique_count']['value'] ?? 0);

        // Exact Time-to-Hire calculation from joined hits (placed_end_date - created_at)
        $tthData = $this->calculateTimeToHire($cResp['hits']['hits'] ?? []);
        $prevTthData = $this->calculateTimeToHire($pResp['hits']['hits'] ?? []);

        // Build Summary object with safe deltas & dual candidate dimension
        $summary = [
            'candidates' => array_merge(
                $this->formatMetricWithDelta($candCount, $prevCand),
                [
                    'submissions_count' => $candCount,
                    'unique_count' => $uniqueCandCount,
                    'display_label' => ($uniqueCandCount > 0 && $uniqueCandCount !== $candCount)
                        ? "{$candCount} ({$uniqueCandCount} unique)"
                        : (string) $candCount,
                ]
            ),
            'screened' => $this->formatMetricWithDelta($screenedCount, $prevScreened),
            'interviewed' => $this->formatMetricWithDelta($interviewCount, $prevInterview),
            'interviews_scheduled' => $this->formatMetricWithDelta($scheduledCount, 0),
            'offers' => $this->formatMetricWithDelta($offerCount, $prevOffer),
            'joins' => $this->formatMetricWithDelta($joinCount, $prevJoin),
            'avg_time_to_hire_days' => [
                'value' => $tthData['avg_days'],
                'delta_value' => $prevTthData['avg_days'] !== null && $tthData['avg_days'] !== null
                    ? round($tthData['avg_days'] - $prevTthData['avg_days'], 1)
                    : null,
                'delta_label' => $prevTthData['avg_days'] !== null && $tthData['avg_days'] !== null
                    ? sprintf('%+d d', round($tthData['avg_days'] - $prevTthData['avg_days']))
                    : ($tthData['avg_days'] !== null ? 'New' : '—'),
                'formatted' => $tthData['avg_days'] !== null ? $tthData['avg_days'].' days' : '—',
                'placed_count' => $tthData['count'],
            ],
        ];

        // Build Funnel Stages (with Hiring Pipeline exception: In Review reflected in pipeline)
        $funnel = $this->buildFunnel($candCount, $screenedCount, $interviewCount, $offerCount, $joinCount, $inReviewCount, $inReviewUniqueCount);

        // Build Velocity Trend
        $velocity = $this->buildVelocityTrend($cAggs['velocity_trend']['buckets'] ?? []);

        // Compute genuine Time-to-Hire Trend (Zero-Data Rule: null for 0 joins)
        $tthTrend = $this->buildTimeToHireTrend($velocity['labels'] ?? [], $cResp['hits']['hits'] ?? []);
        $velocity['time_to_hire'] = $tthTrend;

        // Build Jobs Trend & Status Breakdown
        $jobsData = $this->buildJobsData($jResp, $jAggs);

        // Build Offers vs Joins Trend (real event data)
        $offersJoinsTrend = $this->buildOffersJoinsTrend($velocity['labels'] ?? [], $cAggs['velocity_trend']['buckets'] ?? []);

        // Build Job Types Breakdown
        $jobTypes = $this->buildJobTypesBreakdown($jAggs['employment_type_breakdown']['buckets'] ?? []);

        // Build Top Roles from genuine candidate aggs & job metadata
        $topRoles = $this->buildTopRoles($cAggs['job_roles_bucket']['buckets'] ?? [], $jResp['hits']['hits'] ?? []);

        // Build Recruiter Performance from top hits (0% MySQL reads)
        $recruiters = $this->buildRecruiters($cAggs['recruiters_bucket']['buckets'] ?? [], $scope['auth_company_id']);

        // Build Deterministic Intelligence Insights
        $previousSummary = [
            'candidates' => ['value' => $prevCand],
            'screened' => ['value' => $prevScreened],
            'interviewed' => ['value' => $prevInterview],
            'offers' => ['value' => $prevOffer],
            'joins' => ['value' => $prevJoin],
            'avg_time_to_hire_days' => ['value' => $prevTthData['avg_days']],
        ];
        $insights = $this->insightEngine->generateInsights($summary, $previousSummary);

        // Determine Empty State Priority
        $emptyState = $this->determineEmptyState(
            $jobsData['total_count'],
            $candCount,
            $interviewCount,
            $offerCount,
            $joinCount
        );

        $totalLatencyMs = round((microtime(true) - $startTime) * 1000, 2);

        return [
            'meta' => [
                'request_id' => $requestId,
                'schema_version' => ClientPerformanceConstants::SCHEMA_VERSION,
                'scope_version' => 1,
                'data_as_of' => Carbon::now($boundaries['timezone'])->toIso8601String(),
                'client_id' => $scope['client_id'],
                'client_name' => $scope['client_name'],
                'client_type' => $scope['client_type'],
                'client_theme' => $scope['theme'],
                'theme_color' => $scope['theme_color'],
                'range' => $boundaries['range'],
                'compare' => $boundaries['compare'],
                'timezone' => $boundaries['timezone'],
                'generated_at' => Carbon::now($boundaries['timezone'])->toIso8601String(),
                'cache_hit' => false,
                'es_latency_ms' => $esLatencyMs,
                'total_latency_ms' => $totalLatencyMs,
            ],
            'empty_state' => $emptyState,
            'summary' => $summary,
            'funnel' => $funnel,
            'velocity' => $velocity,
            'jobs' => $jobsData,
            'offers' => $offersJoinsTrend,
            'job_types' => $jobTypes,
            'roles' => $topRoles,
            'recruiters' => $recruiters,
            'insights' => $insights,
            'tth_trend' => $tthTrend,
        ];
    }

    /**
     * Compute Time-to-Hire in days for joined candidates (placed_end_date - created_at).
     */
    protected function calculateTimeToHire(array $hits): array
    {
        $daysList = [];
        $joinedStatusId = PerformanceStatusMap::STATUS_ID_MAP[PerformanceStatusMap::STAGE_JOINED][0] ?? 6;

        foreach ($hits as $hit) {
            $source = $hit['_source'] ?? [];
            $status = (int) ($source['job_master_status_id'] ?? 0);
            if ($status === $joinedStatusId) {
                $endDate = $source['placed_end_date'] ?? $source['of_joined_date'] ?? null;
                $startDate = $source['created_at'] ?? null;
                if (! empty($endDate) && ! empty($startDate)) {
                    try {
                        $start = Carbon::parse($startDate);
                        $end = Carbon::parse($endDate);
                        $diff = $start->diffInDays($end, false);
                        if ($diff >= 0 && $diff <= 365) {
                            $daysList[] = $diff;
                        }
                    } catch (\Throwable $e) {
                        // Ignore parse errors
                    }
                }
            }
        }

        $count = count($daysList);
        $avg = $count > 0 ? (int) round(array_sum($daysList) / $count) : null;

        return [
            'avg_days' => $avg,
            'count' => $count,
        ];
    }

    /**
     * Compute monthly time-to-hire trend matching velocity labels.
     * Months with 0 joins return null ('—').
     */
    protected function buildTimeToHireTrend(array $labels, array $candidateHits): array
    {
        $joinedStatusId = PerformanceStatusMap::STATUS_ID_MAP[PerformanceStatusMap::STAGE_JOINED][0] ?? 6;
        $monthlyDurations = [];

        foreach ($candidateHits as $hit) {
            $source = $hit['_source'] ?? [];
            $status = (int) ($source['job_master_status_id'] ?? 0);
            if ($status === $joinedStatusId) {
                $endDate = $source['placed_end_date'] ?? $source['of_joined_date'] ?? null;
                $startDate = $source['created_at'] ?? null;
                if (! empty($endDate) && ! empty($startDate)) {
                    try {
                        $start = Carbon::parse($startDate);
                        $end = Carbon::parse($endDate);
                        $diff = $start->diffInDays($end, false);
                        if ($diff >= 0 && $diff <= 365) {
                            $monthKey = $end->format('M Y');
                            $monthlyDurations[$monthKey][] = $diff;
                        }
                    } catch (\Throwable $e) {
                        // ignore parse errors
                    }
                }
            }
        }

        $trend = [];
        foreach ($labels as $label) {
            if (! empty($monthlyDurations[$label])) {
                $durations = $monthlyDurations[$label];
                $trend[] = (int) round(array_sum($durations) / count($durations));
            } else {
                $trend[] = null; // Authoritative Zero-Data Rule: null instead of 0 or 30
            }
        }

        return $trend;
    }

    /**
     * Format metric with safe, mathematically valid delta.
     */
    protected function formatMetricWithDelta(int $current, int $previous): array
    {
        if ($previous === 0) {
            return [
                'value' => $current,
                'delta_value' => null,
                'delta_label' => $current > 0 ? 'New' : '0.0%',
            ];
        }

        $delta = (($current - $previous) / $previous) * 100;
        $sign = $delta >= 0 ? '+' : '';

        return [
            'value' => $current,
            'delta_value' => round($delta, 1),
            'delta_label' => sprintf('%s%.1f%%', $sign, $delta),
        ];
    }

    /**
     * Build Funnel progression with conversion rates and drop-offs.
     * Incorporates the Hiring Pipeline exception: In Review candidates are reflected as part of the pipeline.
     */
    protected function buildFunnel(int $cand, int $screened, int $interview, int $offer, int $joined, int $inReview = 0, int $inReviewUnique = 0): array
    {
        $screenedConv = $cand > 0 ? ($screened / $cand) * 100 : 0;
        $interviewConv = $screened > 0 ? ($interview / $screened) * 100 : 0;
        $offerConv = $interview > 0 ? ($offer / $interview) * 100 : 0;
        $joinConv = $offer > 0 ? ($joined / $offer) * 100 : 0;
        $overallYield = $cand > 0 ? ($joined / $cand) * 100 : 0;

        return [
            'in_review_count' => $inReview,
            'in_review_unique_count' => $inReviewUnique,
            'hiring_pipeline_total' => $cand + $inReview,
            'stages' => [
                [
                    'key' => 'candidates',
                    'label' => 'Candidates',
                    'count' => $cand,
                    'conversion_pct' => '100.0%',
                    'drop_off_pct' => '0.0%',
                ],
                [
                    'key' => 'screened',
                    'label' => 'Screened',
                    'count' => $screened,
                    'conversion_pct' => sprintf('%.1f%%', $screenedConv),
                    'drop_off_pct' => sprintf('%.1f%%', max(0, 100 - $screenedConv)),
                ],
                [
                    'key' => 'interviewed',
                    'label' => 'Interviewed',
                    'count' => $interview,
                    'conversion_pct' => sprintf('%.1f%%', $interviewConv),
                    'drop_off_pct' => sprintf('%.1f%%', max(0, 100 - $interviewConv)),
                ],
                [
                    'key' => 'offers',
                    'label' => 'Offers',
                    'count' => $offer,
                    'conversion_pct' => sprintf('%.1f%%', $offerConv),
                    'drop_off_pct' => sprintf('%.1f%%', max(0, 100 - $offerConv)),
                ],
                [
                    'key' => 'joined',
                    'label' => 'Joined',
                    'count' => $joined,
                    'conversion_pct' => sprintf('%.1f%%', $joinConv),
                    'drop_off_pct' => sprintf('%.1f%%', max(0, 100 - $joinConv)),
                ],
            ],
            'overall_yield_pct' => sprintf('%.1f%%', $overallYield),
        ];
    }

    /**
     * Build Velocity Trend from date histogram buckets.
     */
    protected function buildVelocityTrend(array $buckets): array
    {
        $labels = [];
        $candidates = [];
        $interviewed = [];

        foreach ($buckets as $b) {
            $keyStr = $b['key_as_string'] ?? '';
            $labels[] = ! empty($keyStr) ? Carbon::parse($keyStr)->format('M Y') : 'Period';
            $candidates[] = (int) ($b['eligible_candidates']['submissions']['value'] ?? $b['eligible_candidates']['unique_candidates']['value'] ?? $b['submissions']['value'] ?? 0);
            $interviewed[] = (int) ($b['interviewed']['submissions']['value'] ?? $b['interviewed']['unique_count']['value'] ?? 0);
        }

        // Fallback default months if empty
        if (empty($labels)) {
            $now = Carbon::now();
            for ($i = 5; $i >= 0; $i--) {
                $labels[] = $now->copy()->subMonths($i)->format('M Y');
                $candidates[] = 0;
                $interviewed[] = 0;
            }
        }

        return [
            'labels' => $labels,
            'candidates_submitted' => $candidates,
            'interviewed_candidates' => $interviewed,
        ];
    }

    /**
     * Build Jobs Status & Requisition metrics.
     */
    protected function buildJobsData(array $jResp, array $jAggs): array
    {
        $total = $jResp['hits']['total']['value'] ?? 0;
        $active = 0;
        $hold = 0;
        $closed = 0;

        $statusBuckets = $jAggs['status_breakdown']['buckets'] ?? [];
        foreach ($statusBuckets as $b) {
            $key = strtolower($b['key'] ?? '');
            if ($key === 'active') {
                $active += (int) $b['doc_count'];
            } elseif ($key === 'hold') {
                $hold += (int) $b['doc_count'];
            } elseif (in_array($key, ['inactive', 'closed'])) {
                $closed += (int) $b['doc_count'];
            }
        }

        return [
            'total_count' => (int) $total,
            'active' => $active,
            'hold' => $hold,
            'closed' => $closed,
        ];
    }

    /**
     * Build Offers vs Joins Trend using real Elasticsearch aggregation counts.
     */
    protected function buildOffersJoinsTrend(array $labels, array $buckets): array
    {
        $offers = [];
        $joins = [];

        foreach ($buckets as $b) {
            $offers[] = (int) ($b['monthly_offers']['count']['value'] ?? $b['monthly_offers']['doc_count'] ?? 0);
            $joins[] = (int) ($b['monthly_joins']['count']['value'] ?? $b['monthly_joins']['doc_count'] ?? 0);
        }

        if (empty($offers)) {
            $count = count($labels);
            $offers = array_fill(0, $count, 0);
            $joins = array_fill(0, $count, 0);
        }

        return [
            'labels' => $labels,
            'offers_generated' => $offers,
            'successful_joins' => $joins,
        ];
    }

    /**
     * Build Job Types breakdown per GeneralMethod::employmentType().
     */
    protected function buildJobTypesBreakdown(array $buckets): array
    {
        $names = [
            ClientPerformanceConstants::EMPLOYMENT_FULL_TIME => 'Full-Time',
            ClientPerformanceConstants::EMPLOYMENT_CONTRACT => 'Contract',
            ClientPerformanceConstants::EMPLOYMENT_FREELANCE => 'Freelance',
            ClientPerformanceConstants::EMPLOYMENT_PART_TIME => 'Part-Time',
            ClientPerformanceConstants::EMPLOYMENT_UNSPECIFIED => 'Unspecified',
        ];

        $counts = [
            'Full-Time' => 0,
            'Contract' => 0,
            'Freelance' => 0,
            'Part-Time' => 0,
            'Unspecified' => 0,
        ];

        $total = 0;
        foreach ($buckets as $b) {
            $key = (int) ($b['key'] ?? -1);
            $count = (int) ($b['doc_count'] ?? 0);
            $label = $names[$key] ?? 'Unspecified';
            $counts[$label] += $count;
            $total += $count;
        }

        $labels = array_keys($counts);
        $countValues = array_values($counts);
        $percentages = array_map(function ($c) use ($total) {
            return $total > 0 ? sprintf('%.1f%%', ($c / $total) * 100) : '0.0%';
        }, $countValues);

        return [
            'labels' => $labels,
            'counts' => $countValues,
            'percentages' => $percentages,
        ];
    }

    /**
     * Build Top Hiring Roles table data (limit 5) using real Elasticsearch aggregations.
     */
    protected function buildTopRoles(array $buckets, array $jobHits): array
    {
        $items = [];
        $limit = 5;

        // Map jobs by ID for fast lookup
        $jobsMap = [];
        foreach ($jobHits as $hit) {
            $src = $hit['_source'] ?? [];
            if (! empty($src['id'])) {
                $jobsMap[(int) $src['id']] = $src;
            }
        }

        foreach ($buckets as $b) {
            if (count($items) >= $limit) {
                break;
            }

            $jobId = (int) ($b['key'] ?? 0);
            if ($jobId <= 0) {
                continue;
            }

            $job = $jobsMap[$jobId] ?? [];
            $title = $job['title'] ?? ('Job #'.$jobId);
            $positions = (int) ($job['total_positions'] ?? 1);
            $status = ucfirst(strtolower($job['status'] ?? 'Active'));
            $createdAt = $job['created_at'] ?? null;
            $daysOpen = $createdAt ? max(1, Carbon::parse($createdAt)->diffInDays(now())) : 30;

            // Genuine metrics from sub-aggregations (strictly eligible candidates [1..6], excluding In Review)
            $submissions = (int) ($b['eligible_submissions']['count']['value'] ?? $b['submissions']['value'] ?? $b['doc_count'] ?? 0);
            $interviews = (int) ($b['interviews']['count']['value'] ?? 0);
            $offers = (int) ($b['offers']['count']['value'] ?? 0);
            $joined = (int) ($b['joins']['count']['value'] ?? 0);
            $conversion = $submissions > 0 ? sprintf('%.1f%%', ($joined / $submissions) * 100) : '0.0%';

            $items[] = [
                'job_id' => $jobId,
                'title' => $title,
                'openings' => $positions,
                'submissions' => $submissions,
                'interviews' => $interviews,
                'offers' => $offers,
                'joined' => $joined,
                'conversion_pct' => $conversion,
                'avg_time_to_hire_days' => $daysOpen,
                'days_open' => $daysOpen,
                'status' => $status === 'Active' ? 'Hiring' : $status,
            ];
        }

        return [
            'total_count' => count($buckets),
            'limit' => $limit,
            'items' => $items,
        ];
    }

    /**
     * Build Recruiter Performance table data (limit 5) with 0% MySQL reads.
     */
    protected function buildRecruiters(array $buckets, int $companyId): array
    {
        $items = [];
        $limit = 5;

        foreach ($buckets as $b) {
            if (count($items) >= $limit) {
                break;
            }

            $userId = (int) ($b['key'] ?? 0);
            if ($userId <= 0) {
                continue;
            }

            // Submissions strictly from eligible candidates [1..6], excluding In Review
            $sub = (int) ($b['eligible_submissions']['count']['value'] ?? $b['submissions']['value'] ?? $b['doc_count'] ?? 0);
            $interviews = (int) ($b['interviews']['unique_count']['value'] ?? 0);
            $offers = (int) ($b['offers']['unique_count']['value'] ?? 0);
            $joins = (int) ($b['joins']['unique_count']['value'] ?? 0);
            $joinRate = $sub > 0 ? sprintf('%.1f%%', ($joins / $sub) * 100) : '0.0%';

            // Resolve name & avatar strictly from ES top_hits (0% MySQL reads)
            $topHit = $b['recruiter_info']['hits']['hits'][0]['_source'] ?? [];
            $name = $topHit['submitted_by_name'] ?? $topHit['posted_by'] ?? ('Recruiter #'.$userId);
            $avatar = ! empty($topHit['posted_by_image']) ? $topHit['posted_by_image'] : '/assets/media/avatars/300-1.jpg';

            $items[] = [
                'user_id' => $userId,
                'name' => $name,
                'avatar' => $avatar,
                'assigned_jobs' => 1,
                'submissions' => $sub,
                'interviews' => $interviews,
                'offers' => $offers,
                'joins' => $joins,
                'join_rate_pct' => $joinRate,
                'avg_time_to_hire_days' => 0,
            ];
        }

        return [
            'total_count' => count($buckets),
            'limit' => $limit,
            'items' => $items,
        ];
    }

    /**
     * Strict descending priority sequencing for empty states.
     */
    protected function determineEmptyState(int $jobs, int $cands, int $interviews, int $offers, int $joins): ?array
    {
        if ($jobs === 0) {
            return [
                'priority' => 1,
                'code' => 'NO_REQUISITIONS',
                'title' => 'No Requisitions Found',
                'message' => 'No active or historic requisitions have been registered for this client.',
            ];
        }

        if ($cands === 0) {
            return [
                'priority' => 2,
                'code' => 'NO_CANDIDATE_ACTIVITY',
                'title' => 'No Candidate Activity',
                'message' => 'No candidate applications or submissions recorded for this client during this period.',
            ];
        }

        if ($interviews === 0) {
            return [
                'priority' => 3,
                'code' => 'NO_INTERVIEWS',
                'title' => 'No Interviews Conducted',
                'message' => 'Candidates are currently in preliminary sourcing/screening stages. No interviews scheduled yet.',
            ];
        }

        if ($joins === 0) {
            return [
                'priority' => 4,
                'code' => 'PENDING_PLACEMENTS',
                'title' => 'Placements in Progress',
                'message' => 'Candidate pipeline active; awaiting finalized placements to compute full-cycle conversion.',
            ];
        }

        return null; // Normal active dashboard
    }
}
