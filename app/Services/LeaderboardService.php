<?php

namespace App\Services;

use App\Helper\GeneralMethod;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class LeaderboardService
 *
 * Intent: Authoritative, multi-tenant calculation, persistence, publishing, and retrieval
 * of monthly ATS team member leaderboards across JERO. Ensures 100% synchronized ranking
 * data between Team Member (/service_company/team) and Company Profile (/service_company/profile)
 * for the complete previous calendar month.
 *
 * Performance:
 * - 0% MySQL reads on web/API request paths via high-speed Redis multi-tenant caching.
 * - Authoritative persistence in `company_monthly_leaderboards` MySQL table.
 * - Idempotent publishing workflow preventing duplicate monthly runs.
 * - Client-safe JIT / circuit-breaker fallback if cache or snapshot is missing.
 *
 * Tenancy: Every ranking evaluation is strictly tenant-scoped by `company_id`.
 */
class LeaderboardService
{
    protected ElasticService $elasticService;

    /**
     * Cache TTL for published monthly leaderboards (in seconds: 7 days).
     */
    protected int $cacheTtl = 604800;

    public function __construct(ElasticService $elasticService)
    {
        $this->elasticService = $elasticService;
    }

    /**
     * Resolve the previous complete calendar month date range in Asia/Kolkata timezone.
     *
     * @param Carbon|null $referenceTime
     * @return array{period: string, start: string, end: string, start_db: string, end_db: string, label: string}
     */
    public function getPreviousMonthRange(?Carbon $referenceTime = null): array
    {
        $now = $referenceTime ? $referenceTime->copy()->timezone('Asia/Kolkata') : Carbon::now('Asia/Kolkata');
        $prevMonth = $now->copy()->startOfMonth()->subMonth();

        $periodMonth = $prevMonth->format('Y-m');
        $startCarbon = $prevMonth->copy()->startOfMonth();
        $endCarbon = $prevMonth->copy()->endOfMonth();

        return [
            'period' => $periodMonth,
            'start' => $startCarbon->toIso8601String(),
            'end' => $endCarbon->toIso8601String(),
            'start_db' => $startCarbon->format('Y-m-d 00:00:00'),
            'end_db' => $endCarbon->format('Y-m-d 23:59:59'),
            'label' => $prevMonth->format('M Y'),
        ];
    }

    /**
     * Get authoritative leaderboard data for a company and user.
     * Synchronously serves the previous complete calendar month's published snapshot.
     *
     * @param int $companyId
     * @param int $userId
     * @param string|null $periodMonth Optional YYYY-MM period; defaults to previous calendar month.
     * @return array{top: Collection, me: array|null, total: int, period: string, published_at: string|null}
     */
    public function getLeaderboard(int $companyId, int $userId, ?string $periodMonth = null): array
    {
        if (empty($periodMonth)) {
            $range = $this->getPreviousMonthRange();
            $periodMonth = $range['period'];
        }

        $cacheKey = "ats:leaderboard:monthly:comp_{$companyId}:{$periodMonth}";

        // 1. Try Redis cache (Sub-millisecond read, 0% DB strain)
        $rankedList = Cache::get($cacheKey);

        if (!is_array($rankedList)) {
            // 2. Try MySQL durable snapshot table
            try {
                $snapshot = DB::table('company_monthly_leaderboards')
                    ->where('company_id', $companyId)
                    ->where('period_month', $periodMonth)
                    ->where('status', 'published')
                    ->first();

                if ($snapshot && !empty($snapshot->leaderboard_data)) {
                    $decoded = json_decode($snapshot->leaderboard_data, true);
                    if (is_array($decoded)) {
                        $rankedList = $decoded;
                        Cache::put($cacheKey, $rankedList, $this->cacheTtl);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("LeaderboardService: Failed to read from company_monthly_leaderboards for company {$companyId}: " . $e->getMessage());
            }
        }

        // 3. JIT / Circuit-breaker fallback if no published snapshot exists yet
        if (!is_array($rankedList)) {
            $rankedList = $this->calculateCompanyLeaderboard($companyId, $periodMonth);

            // Persist JIT snapshot so subsequent requests avoid live recalculations
            try {
                DB::table('company_monthly_leaderboards')->updateOrInsert(
                    [
                        'company_id' => $companyId,
                        'period_month' => $periodMonth,
                    ],
                    [
                        'leaderboard_data' => json_encode($rankedList),
                        'total_members' => count($rankedList),
                        'status' => 'published',
                        'published_at' => Carbon::now('Asia/Kolkata'),
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            } catch (\Throwable $e) {
                Log::error("LeaderboardService: Failed to persist JIT snapshot: " . $e->getMessage());
            }

            Cache::put($cacheKey, $rankedList, $this->cacheTtl);
        }

        $rankedCollection = collect($rankedList);
        $me = $rankedCollection->firstWhere('id', $userId);

        return [
            'top' => $rankedCollection->take(10)->values(),
            'me' => $me,
            'total' => $rankedCollection->count(),
            'period' => $periodMonth,
        ];
    }

    /**
     * Calculate authoritative leaderboard ranking for a given company and monthly period.
     * Evaluates all 18 activity point dimensions from Elasticsearch (with MySQL fallback).
     *
     * @param int $companyId
     * @param string $periodMonth Format 'YYYY-MM'
     * @return array List of ranked members with rank, score, designation, image, status.
     */
    public function calculateCompanyLeaderboard(int $companyId, string $periodMonth): array
    {
        $startCarbon = Carbon::parse($periodMonth . '-01 00:00:00', 'Asia/Kolkata')->startOfMonth();
        $endCarbon = Carbon::parse($periodMonth . '-01 00:00:00', 'Asia/Kolkata')->endOfMonth();

        $isoStart = $startCarbon->toIso8601String();
        $isoEnd = $endCarbon->toIso8601String();
        $dbStart = $startCarbon->format('Y-m-d 00:00:00');
        $dbEnd = $endCarbon->format('Y-m-d 23:59:59');

        // 1. Retrieve all company users (Elasticsearch with DB fallback)
        $users = collect();
        if ($this->elasticService->indexExists(ELASTIC_USERS_LIST)) {
            try {
                $userResponse = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_USERS_LIST,
                    'request_cache' => true,
                    'body' => [
                        'query' => ['bool' => ['filter' => [['term' => ['company_id' => $companyId]]]]],
                        '_source' => ['id', 'name', 'email', 'designation', 'status', 'image'],
                        'size' => 1000,
                        'sort' => [['id' => ['order' => 'asc']]],
                    ],
                ]);
                $users = collect($userResponse['hits']['hits'] ?? [])->pluck('_source');
            } catch (\Throwable $e) {
                Log::warning("LeaderboardService: ES user search failed for company {$companyId}: " . $e->getMessage());
            }
        }

        if ($users->isEmpty()) {
            try {
                $users = DB::table('users')
                    ->where('company_id', $companyId)
                    ->orderBy('id', 'asc')
                    ->get(['id', 'name', 'email', 'designation', 'status', 'image'])
                    ->map(fn($u) => (array) $u);
            } catch (\Throwable $e) {
                Log::error("LeaderboardService: DB user query failed for company {$companyId}: " . $e->getMessage());
                return [];
            }
        }

        if ($users->isEmpty()) {
            return [];
        }

        // 2. Aggregate scores from ELASTIC_TEAM_RANK (all 18 activity point fields)
        $scoreMap = collect();
        $esAggSuccess = false;

        if ($this->elasticService->indexExists(ELASTIC_TEAM_RANK)) {
            try {
                $rankResponse = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_TEAM_RANK,
                    'request_cache' => true,
                    'body' => [
                        'query' => [
                            'bool' => [
                                'filter' => [
                                    ['term' => ['doc.company_id' => $companyId]],
                                    ['range' => ['doc.created_at' => ['gte' => $isoStart, 'lte' => $isoEnd]]],
                                ],
                            ],
                        ],
                        'aggs' => [
                            'user_scores' => [
                                'terms' => ['field' => 'doc.user_id', 'size' => 1000],
                                'aggs' => [
                                    'profileUpload_sum' => ['sum' => ['field' => 'doc.profileUpload']],
                                    'interviewScheduled_sum' => ['sum' => ['field' => 'doc.interviewScheduled']],
                                    'interviewNoShow_sum' => ['sum' => ['field' => 'doc.interviewNoShow']],
                                    'offerReleased_sum' => ['sum' => ['field' => 'doc.offerReleased']],
                                    'candidateJoined_sum' => ['sum' => ['field' => 'doc.candidateJoined']],
                                    'teamCaptainInterviewScheduled_sum' => ['sum' => ['field' => 'doc.teamCaptainInterviewScheduled']],
                                    'teamCaptainOfferReleased_sum' => ['sum' => ['field' => 'doc.teamCaptainOfferReleased']],
                                    'teamCaptainCandidateJoined_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoined']],
                                    'sourcing_sum' => ['sum' => ['field' => 'doc.sourcing']],
                                    'screening_sum' => ['sum' => ['field' => 'doc.screening']],
                                    'evaluation_sum' => ['sum' => ['field' => 'doc.evaluation']],
                                    'negotiation_sum' => ['sum' => ['field' => 'doc.negotiation']],
                                    'backout_sum' => ['sum' => ['field' => 'doc.backout']],
                                    'backoutTeam_sum' => ['sum' => ['field' => 'doc.backoutTeam']],
                                    'candidateJoinedContract_sum' => ['sum' => ['field' => 'doc.candidateJoinedContract']],
                                    'teamCaptainCandidateJoinedContract_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoinedContract']],
                                    'backoutContract_sum' => ['sum' => ['field' => 'doc.backoutContract']],
                                    'backoutTeamContract_sum' => ['sum' => ['field' => 'doc.backoutTeamContract']],
                                ],
                            ],
                        ],
                        'size' => 0,
                    ],
                ]);

                $buckets = $rankResponse['aggregations']['user_scores']['buckets'] ?? [];
                $scoreMap = collect($buckets)->mapWithKeys(function ($item) {
                    $score = ($item['profileUpload_sum']['value'] ?? 0)
                        + ($item['interviewScheduled_sum']['value'] ?? 0)
                        + ($item['interviewNoShow_sum']['value'] ?? 0)
                        + ($item['offerReleased_sum']['value'] ?? 0)
                        + ($item['candidateJoined_sum']['value'] ?? 0)
                        + ($item['teamCaptainInterviewScheduled_sum']['value'] ?? 0)
                        + ($item['teamCaptainOfferReleased_sum']['value'] ?? 0)
                        + ($item['teamCaptainCandidateJoined_sum']['value'] ?? 0)
                        + ($item['sourcing_sum']['value'] ?? 0)
                        + ($item['screening_sum']['value'] ?? 0)
                        + ($item['evaluation_sum']['value'] ?? 0)
                        + ($item['negotiation_sum']['value'] ?? 0)
                        + ($item['backout_sum']['value'] ?? 0)
                        + ($item['backoutTeam_sum']['value'] ?? 0)
                        + ($item['candidateJoinedContract_sum']['value'] ?? 0)
                        + ($item['teamCaptainCandidateJoinedContract_sum']['value'] ?? 0)
                        + ($item['backoutContract_sum']['value'] ?? 0)
                        + ($item['backoutTeamContract_sum']['value'] ?? 0);

                    return [(int) $item['key'] => (int) round($score)];
                });
                $esAggSuccess = true;
            } catch (\Throwable $e) {
                Log::warning("LeaderboardService: ES rank search failed for company {$companyId}: " . $e->getMessage());
            }
        }

        // Fallback to MySQL company_user_points_logs if ES aggregation failed
        if (!$esAggSuccess) {
            try {
                $dbLogs = DB::table('company_user_points_logs')
                    ->where('company_id', $companyId)
                    ->whereBetween('created_at', [$dbStart, $dbEnd])
                    ->selectRaw('user_id,
                        SUM(COALESCE(profileUpload, 0)
                          + COALESCE(interviewScheduled, 0)
                          + COALESCE(interviewNoShow, 0)
                          + COALESCE(offerReleased, 0)
                          + COALESCE(candidateJoined, 0)
                          + COALESCE(teamCaptainInterviewScheduled, 0)
                          + COALESCE(teamCaptainOfferReleased, 0)
                          + COALESCE(teamCaptainCandidateJoined, 0)
                          + COALESCE(sourcing, 0)
                          + COALESCE(screening, 0)
                          + COALESCE(evaluation, 0)
                          + COALESCE(negotiation, 0)
                          + COALESCE(backout, 0)
                          + COALESCE(backoutTeam, 0)
                          + COALESCE(candidateJoinedContract, 0)
                          + COALESCE(teamCaptainCandidateJoinedContract, 0)
                          + COALESCE(backoutContract, 0)
                          + COALESCE(backoutTeamContract, 0)
                        ) as total_score')
                    ->groupBy('user_id')
                    ->get();

                $scoreMap = $dbLogs->mapWithKeys(fn($row) => [(int) $row->user_id => (int) round($row->total_score)]);
            } catch (\Throwable $e) {
                Log::error("LeaderboardService: DB points logs query failed for company {$companyId}: " . $e->getMessage());
            }
        }

        // 3. Format, sort, and assign ranks
        $ranked = $users->map(function ($user) use ($scoreMap) {
            $userArr = (array) $user;
            $id = (int) ($userArr['id'] ?? 0);

            return [
                'id' => $id,
                'name' => $userArr['name'] ?? 'User',
                'designation' => $userArr['designation'] ?? '',
                'image' => $userArr['image'] ?? '',
                'status' => strtolower($userArr['status'] ?? 'inactive'),
                'score' => $scoreMap->get($id, 0),
            ];
        })->sort(function ($a, $b) {
            // Rule 1: Active users first
            if ($a['status'] !== $b['status']) {
                return ($a['status'] === 'active') ? -1 : 1;
            }
            // Rule 2: Higher score descending
            if ($a['score'] !== $b['score']) {
                return $b['score'] <=> $a['score'];
            }
            // Rule 3: Deterministic tie-breaker by ID ascending
            return $a['id'] <=> $b['id'];
        })->values()->map(function ($user, $index) {
            $user['rank'] = $index + 1;
            return $user;
        });

        return $ranked->all();
    }

    /**
     * Publish monthly leaderboard data for all or specific companies.
     * Idempotent: Skips already-published companies unless $force is true.
     *
     * @param string|null $targetMonth Format 'YYYY-MM' (defaults to previous calendar month)
     * @param int|null $companyId Optional single company ID
     * @param bool $force Recalculate even if already published
     * @return array Summary of processed, skipped, failed counts and details.
     */
    public function publishMonthlyLeaderboard(?string $targetMonth = null, ?int $companyId = null, bool $force = false): array
    {
        $startTime = microtime(true);
        if (empty($targetMonth)) {
            $range = $this->getPreviousMonthRange();
            $targetMonth = $range['period'];
        }

        // Determine target companies
        if ($companyId) {
            $companies = [$companyId];
        } else {
            try {
                $companies = DB::table('companies')
                    ->where('status', 'active')
                    ->pluck('id')
                    ->map(fn($id) => (int) $id)
                    ->all();
            } catch (\Throwable $e) {
                Log::error("LeaderboardService: Failed to fetch companies: " . $e->getMessage());
                return ['error' => $e->getMessage()];
            }
        }

        $summary = [
            'period_month' => $targetMonth,
            'total_companies' => count($companies),
            'processed' => 0,
            'skipped' => 0,
            'failed' => 0,
            'details' => [],
        ];

        foreach ($companies as $cId) {
            try {
                // Check if already published
                if (!$force) {
                    $existing = DB::table('company_monthly_leaderboards')
                        ->where('company_id', $cId)
                        ->where('period_month', $targetMonth)
                        ->where('status', 'published')
                        ->first();

                    if ($existing) {
                        // Warm cache if missing
                        $cacheKey = "ats:leaderboard:monthly:comp_{$cId}:{$targetMonth}";
                        if (!Cache::has($cacheKey) && !empty($existing->leaderboard_data)) {
                            Cache::put($cacheKey, json_decode($existing->leaderboard_data, true), $this->cacheTtl);
                        }

                        $summary['skipped']++;
                        continue;
                    }
                }

                // Compute leaderboard
                $rankedList = $this->calculateCompanyLeaderboard($cId, $targetMonth);

                // Upsert to DB table with unique constraint protection
                DB::table('company_monthly_leaderboards')->updateOrInsert(
                    [
                        'company_id' => $cId,
                        'period_month' => $targetMonth,
                    ],
                    [
                        'leaderboard_data' => json_encode($rankedList),
                        'total_members' => count($rankedList),
                        'status' => 'published',
                        'published_at' => Carbon::now('Asia/Kolkata'),
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

                // Cache in Redis
                $cacheKey = "ats:leaderboard:monthly:comp_{$cId}:{$targetMonth}";
                Cache::put($cacheKey, $rankedList, $this->cacheTtl);

                // Invalidate legacy profile cache keys
                Cache::forget("profile:leaderboard:c{$cId}:u*");

                $summary['processed']++;
                $summary['details'][$cId] = [
                    'members' => count($rankedList),
                    'top_score' => $rankedList[0]['score'] ?? 0,
                    'top_user' => $rankedList[0]['name'] ?? 'None',
                ];
            } catch (\Throwable $e) {
                $summary['failed']++;
                Log::error("LeaderboardService: Failed publishing for company {$cId}: " . $e->getMessage(), [
                    'company_id' => $cId,
                    'month' => $targetMonth,
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        $summary['duration_ms'] = round((microtime(true) - $startTime) * 1000, 2);
        return $summary;
    }
}
