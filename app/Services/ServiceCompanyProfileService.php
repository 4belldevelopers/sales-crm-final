<?php

/**
 * ServiceCompanyProfileService — High-Performance Profile Data & Aggregation Service.
 *
 * INTENT:
 * Provides cached data retrieval for recruiter profile metrics, leaderboards,
 * performance graphs, and pipeline status counts.
 * Replaces synchronous 20-month Elasticsearch loops and live in-memory sorting with
 * multi-tier Redis caching and single-pass aggregations.
 *
 * TENANCY & SCOPING:
 * All queries are strictly scoped by user_id and/or tenant company_id.
 */

namespace App\Http\Controllers\ServiceCompany;

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ServiceCompanyProfileService
{
    public function __construct(
        private readonly ElasticService $elasticService
    ) {}

    /**
     * Get recruiter profile user details (cached 10 mins).
     *
     * Intent: Fetch single recruiter profile joined with company and location info.
     * Tenancy: Scoped strictly by user ID and tenant company scope.
     */
    public function getUserDetails(int $userId): ?User
    {
        return Cache::remember("profile:user:{$userId}", 600, function () use ($userId) {
            return User::join('companies', 'companies.id', '=', 'users.company_id')
                ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'users_profile.city_id')
                ->leftJoin('state_list', 'state_list.id', '=', 'users_profile.state')
                ->select(
                    'users.id', 'users.company_id', 'users.name', 'users.calling_code', 'users.email', 'users.mobile', 'users.created_at', 'users.image', 'users.status',
                    'companies.company_name', 'companies.website', 'companies.zoom_status', 'companies.google_status', 'companies.microsoft_status', 'companies.company_size',
                    'users_profile.country', 'users_profile.state', 'users_profile.city', 'users_profile.designation', 'users_profile.skills',
                    'city_list.city as city_name',
                    'state_list.state as state_name'
                )
                ->with('company')
                ->where('users.id', '=', $userId)
                ->company()
                ->first();
        });
    }

    /**
     * Get list of countries (cached forever/24h).
     */
    public function getCountriesList()
    {
        return Cache::remember('ref:countries', 86400, function () {
            return DB::table('countries')->get();
        });
    }

    /**
     * Get KPI counters & pipeline status counts for recruiter profile.
     *
     * Cached 30 mins in Redis per recruiter.
     */
    public function getKpiMetrics(int $userId, int $companyId): array
    {
        return Cache::remember("profile:kpis:u{$userId}", 1800, function () use ($userId, $companyId) {
            $startOfMonth = Carbon::now()->startOfMonth()->subMonths(3);
            $endOfMonth = Carbon::now()->endOfMonth();

            // Candidate pipeline status count query
            $boolQuery = [
                'bool' => [
                    'should' => [
                        [
                            'bool' => [
                                'must' => [
                                    ['term' => ['submitted_by' => $userId]],
                                    ['term' => ['ClientComID' => 0]],
                                ],
                            ],
                        ],
                        [
                            'bool' => [
                                'must' => [
                                    ['term' => ['submitted_by' => $userId]],
                                    ['range' => ['ClientComID' => ['gt' => 0]]],
                                    ['range' => ['jobId' => ['gt' => 0]]],
                                    [
                                        'bool' => [
                                            'must_not' => [
                                                ['terms' => ['job_master_status_id' => [8, 9]]],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'bool' => [
                                'must' => [
                                    ['term' => ['submitted_by' => $userId]],
                                    ['range' => ['jobId' => ['gt' => 0]]],
                                ],
                            ],
                        ],
                    ],
                    'minimum_should_match' => 1,
                    'filter' => [
                        [
                            'bool' => [
                                'should' => [
                                    [
                                        'range' => [
                                            'updated_at' => [
                                                'gte' => $startOfMonth->toIso8601String(),
                                                'lte' => $endOfMonth->toIso8601String(),
                                            ],
                                        ],
                                    ],
                                ],
                                'minimum_should_match' => 1,
                            ],
                        ],
                    ],
                ],
            ];

            $params = [
                'index' => ELASTIC_CANDIDATES,
                'request_cache' => true,
                'body' => [
                    'track_total_hits' => true,
                    'size' => 0,
                    'query' => $boolQuery,
                    'aggs' => [
                        'status_counts' => [
                            'terms' => [
                                'field' => 'job_master_status_id',
                                'size' => 10,
                            ],
                        ],
                    ],
                ],
            ];

            $bucketData = [];
            try {
                $response = $this->elasticService->getClient()->search($params);
                $bucketData = $response['aggregations']['status_counts']['buckets'] ?? [];
            } catch (\Throwable $e) {
            }

            $result = collect($bucketData)->mapWithKeys(function ($item) {
                return [$item['key'] => $item['doc_count']];
            });

            $joined_count = $result->get('6', 0);
            $sourcing_count = $result->get('1', 0);
            $screening_count = $result->get('2', 0);
            $evaluation_count = $result->get('3', 0);
            $negotiation_count = $result->get('4', 0);
            $offer_count = $result->get('5', 0);
            $Rejected_count = $result->get('7', 0);
            $inreview_count = $result->get('8', 0);
            $total_candidates = $inreview_count + $sourcing_count + $screening_count + $evaluation_count + $negotiation_count + $offer_count + $joined_count + $Rejected_count;

            // Jobs status count graph
            $must = [
                [
                    'bool' => [
                        'should' => [
                            ['term' => ['comId' => $companyId]],
                            [
                                'nested' => [
                                    'path' => 'recommendation_relation',
                                    'query' => [
                                        'term' => ['recommendation_relation.to_company_id' => $companyId],
                                    ],
                                ],
                            ],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ],
                [
                    'nested' => [
                        'path' => 'job_assign',
                        'query' => [
                            'term' => ['job_assign.user_id' => $userId],
                        ],
                    ],
                ],
            ];

            $jobsCounts = collect();
            try {
                $jobResponse = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_JOBS,
                    'request_cache' => true,
                    'body' => [
                        'track_total_hits' => true,
                        'size' => 0,
                        'query' => ['bool' => ['must' => $must]],
                        'aggs' => [
                            'status_counts' => [
                                'terms' => ['field' => 'status', 'size' => 10],
                            ],
                        ],
                    ],
                ]);
                $jobBuckets = $jobResponse['aggregations']['status_counts']['buckets'] ?? [];
                $jobsCounts = collect($jobBuckets)->mapWithKeys(fn ($item) => [$item['key'] => $item['doc_count']]);
            } catch (\Throwable $e) {
            }

            // Header overall totals
            $scpCandidatesTotal = 0;
            $scpJoiningsTotal = 0;
            $scpInterviewsTotal = 0;
            $scpOffersTotal = 0;

            try {
                $cR = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_CANDIDATES,
                    'body' => ['track_total_hits' => true, 'size' => 0, 'query' => ['bool' => ['must' => [['term' => ['submitted_by' => $userId]]]]]],
                ]);
                $scpCandidatesTotal = $cR['hits']['total']['value'] ?? 0;
            } catch (\Throwable $e) {
                $scpCandidatesTotal = $total_candidates;
            }

            try {
                $jR = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_CANDIDATES,
                    'body' => ['track_total_hits' => true, 'size' => 0, 'query' => ['bool' => ['must' => [['term' => ['submitted_by' => $userId]], ['term' => ['job_master_status_id' => 6]]]]]],
                ]);
                $scpJoiningsTotal = $jR['hits']['total']['value'] ?? 0;
            } catch (\Throwable $e) {
                $scpJoiningsTotal = $joined_count;
            }

            try {
                $iR = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_INERTVIEW_TRACKER,
                    'body' => ['track_total_hits' => true, 'size' => 0, 'query' => ['bool' => ['filter' => [['term' => ['submitted_by' => $userId]]]]]],
                ]);
                $scpInterviewsTotal = $iR['hits']['total']['value'] ?? 0;
            } catch (\Throwable $e) {
            }

            try {
                $oR = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_CANDIDATES,
                    'body' => ['track_total_hits' => true, 'size' => 0, 'query' => ['bool' => ['must' => [['term' => ['submitted_by' => $userId]], ['exists' => ['field' => 'of_created_date']]]]]],
                ]);
                $scpOffersTotal = $oR['hits']['total']['value'] ?? 0;
            } catch (\Throwable $e) {
                $scpOffersTotal = $offer_count;
            }

            return [
                'joined_count' => $joined_count,
                'sourcing_count' => $sourcing_count,
                'screening_count' => $screening_count,
                'evaluation_count' => $evaluation_count,
                'negotiation_count' => $negotiation_count,
                'offer_count' => $offer_count,
                'Rejected_count' => $Rejected_count,
                'inreview_count' => $inreview_count,
                'total_candidates' => $total_candidates,
                'counts' => $jobsCounts,
                'scpCandidatesTotal' => $scpCandidatesTotal,
                'scpJoiningsTotal' => $scpJoiningsTotal,
                'scpInterviewsTotal' => $scpInterviewsTotal,
                'scpOffersTotal' => $scpOffersTotal,
            ];
        });
    }

    /**
     * Get monthly rank trends for the user (optimizes away 20-month loop).
     *
     * Cached 30 mins in Redis per recruiter.
     */
    public function getMonthlyRanks(int $userId, int $companyId): array
    {
        return Cache::remember("profile:ranks:u{$userId}", 1800, function () use ($userId, $companyId) {
            $months = collect();
            $reportMonth = Carbon::now()->startOfMonth()->subMonth();
            $currentMonth = $reportMonth->copy()->format('Y-m');
            $startYear = 2025;
            $currentYear = Carbon::now()->year;

            for ($year = $startYear; $year <= $currentYear; $year++) {
                for ($i = 1; $i <= 12; $i++) {
                    $month = Carbon::create($year, $i, 1)->format('Y-m');
                    if ($month > $currentMonth) {
                        break 2;
                    }
                    $months->push($month);
                }
            }
            $months = $months->sortDesc()->values();

            // Fetch current & previous month ranks instead of doing 20 iterations
            $internalRankedUsers = [];
            $globalRankedUsers = [];

            // Execute rank query only for active months (or latest 6 months to prevent ES explosion)
            $recentMonths = $months->take(6);
            foreach ($recentMonths as $month) {
                $reportMonth = Carbon::parse($month.'-01');
                $start = $reportMonth->copy()->startOfMonth()->toIso8601String();
                $end = $reportMonth->copy()->endOfMonth()->toIso8601String();

                $internalRankedUsers[$month] = $this->getRankDataForUser($companyId, $userId, $start, $end, true);
                $globalRankedUsers[$month] = $this->getRankDataForUser(null, $userId, $start, $end, false);
            }

            return [
                'months' => $months,
                'internalRanks' => $internalRankedUsers,
                'globalRanks' => $globalRankedUsers,
            ];
        });
    }

    /**
     * Calculate rank for a single user without returning full 10,000 array maps.
     */
    private function getRankDataForUser(?int $companyId, int $userId, string $startDate, string $endDate, bool $isInternal): array
    {
        try {
            $rankQuery = [
                'index' => ELASTIC_TEAM_RANK,
                'body' => [
                    'size' => 0,
                    'query' => [
                        'bool' => [
                            'filter' => array_values(array_filter([
                                $isInternal ? ['term' => ['doc.company_id' => $companyId]] : null,
                                ['range' => ['doc.created_at' => ['gte' => $startDate, 'lte' => $endDate]]],
                            ])),
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
                                'sourcing_sum' => ['sum' => ['field' => 'doc.sourcing']],
                                'screening_sum' => ['sum' => ['field' => 'doc.screening']],
                                'evaluation_sum' => ['sum' => ['field' => 'doc.evaluation']],
                                'negotiation_sum' => ['sum' => ['field' => 'doc.negotiation']],
                            ],
                        ],
                    ],
                ],
            ];

            $response = $this->elasticService->getClient()->search($rankQuery);
            $buckets = $response['aggregations']['user_scores']['buckets'] ?? [];

            $userScore = 0;
            $allScores = [];

            foreach ($buckets as $bucket) {
                $uid = $bucket['key'];
                $score = ($bucket['profileUpload_sum']['value'] ?? 0)
                    + ($bucket['interviewScheduled_sum']['value'] ?? 0)
                    + ($bucket['interviewNoShow_sum']['value'] ?? 0)
                    + ($bucket['offerReleased_sum']['value'] ?? 0)
                    + ($bucket['candidateJoined_sum']['value'] ?? 0)
                    + ($bucket['sourcing_sum']['value'] ?? 0)
                    + ($bucket['screening_sum']['value'] ?? 0)
                    + ($bucket['evaluation_sum']['value'] ?? 0)
                    + ($bucket['negotiation_sum']['value'] ?? 0);

                $allScores[$uid] = $score;
                if ($uid == $userId) {
                    $userScore = $score;
                }
            }

            arsort($allScores);
            $rank = 1;
            foreach ($allScores as $uid => $score) {
                if ($uid == $userId) {
                    break;
                }
                $rank++;
            }

            return ['score' => $userScore, 'rank' => empty($allScores) ? 'N/A' : $rank];
        } catch (\Throwable $e) {
            return ['score' => 0, 'rank' => 'N/A'];
        }
    }

    /**
     * Get top-10 leaderboard data (cached 15 mins).
     */
    public function getLeaderboard(int $companyId, int $userId): array
    {
        return Cache::remember("profile:leaderboard:c{$companyId}:u{$userId}", 1800, function () use ($companyId, $userId) {
            $lm = Carbon::now()->startOfMonth();
            $lbStart = $lm->copy()->startOfMonth()->toIso8601String();
            $lbEnd = $lm->copy()->endOfMonth()->toIso8601String();

            try {
                $userResponse = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_USERS_LIST,
                    'body' => [
                        'query' => ['bool' => ['filter' => [['term' => ['company_id' => $companyId]]]]],
                        '_source' => ['id', 'name', 'email', 'designation', 'status', 'image'],
                        'size' => 1000,
                    ],
                ]);
                $users = collect($userResponse['hits']['hits'])->pluck('_source');

                $rankResponse = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_TEAM_RANK,
                    'body' => [
                        'query' => ['bool' => ['filter' => [
                            ['term' => ['doc.company_id' => $companyId]],
                            ['range' => ['doc.created_at' => ['gte' => $lbStart, 'lte' => $lbEnd]]],
                        ]]],
                        'aggs' => ['user_scores' => [
                            'terms' => ['field' => 'doc.user_id', 'size' => 1000],
                            'aggs' => [
                                'profileUpload_sum' => ['sum' => ['field' => 'doc.profileUpload']],
                                'interviewScheduled_sum' => ['sum' => ['field' => 'doc.interviewScheduled']],
                                'offerReleased_sum' => ['sum' => ['field' => 'doc.offerReleased']],
                                'candidateJoined_sum' => ['sum' => ['field' => 'doc.candidateJoined']],
                            ],
                        ]],
                        'size' => 0,
                    ],
                ]);
                $buckets = $rankResponse['aggregations']['user_scores']['buckets'] ?? [];

                $scoreMap = collect($buckets)->mapWithKeys(function ($item) {
                    $score = ($item['profileUpload_sum']['value'] ?? 0)
                        + ($item['interviewScheduled_sum']['value'] ?? 0)
                        + ($item['offerReleased_sum']['value'] ?? 0)
                        + ($item['candidateJoined_sum']['value'] ?? 0);

                    return [$item['key'] => $score];
                });

                $ranked = $users->map(function ($user) use ($scoreMap) {
                    $id = $user['id'];

                    return [
                        'id' => $id,
                        'name' => $user['name'] ?? 'User',
                        'designation' => $user['designation'] ?? '',
                        'image' => $user['image'] ?? '',
                        'status' => strtolower($user['status'] ?? 'inactive'),
                        'score' => (int) round($scoreMap[$id] ?? 0),
                    ];
                })->sort(function ($a, $b) {
                    if ($a['status'] !== $b['status']) {
                        return ($a['status'] === 'active') ? -1 : 1;
                    }
                    if ($a['score'] !== $b['score']) {
                        return $b['score'] <=> $a['score'];
                    }

                    return $a['id'] <=> $b['id'];
                })->values()->map(function ($user, $index) {
                    $user['rank'] = $index + 1;

                    return $user;
                });

                return [
                    'top' => $ranked->take(10)->values(),
                    'me' => $ranked->firstWhere('id', $userId),
                    'total' => $ranked->count(),
                ];
            } catch (\Throwable $e) {
                return ['top' => collect(), 'me' => null, 'total' => 0];
            }
        });
    }

    /**
     * Get candidate & offer performance graph metrics (cached 15 mins).
     */
    public function getGraphData(int $userId, int $companyId): array
    {
        return Cache::remember("profile:graphs_v5:u{$userId}", 1800, function () use ($userId, $companyId) {
            $endDate = Carbon::today()->endOfDay();
            $startDate = Carbon::today()->subDays(6)->startOfDay();

            // Candidate 7-day upload histogram
            $labels = [];
            $candidateValues = [];

            try {
                $responseCandidates = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_CANDIDATES,
                    'request_cache' => true,
                    'body' => [
                        'track_total_hits' => true,
                        'size' => 0,
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['company_id' => $companyId]],
                                    ['term' => ['submitted_by' => $userId]],
                                ],
                                'filter' => [
                                    ['range' => ['created_at' => ['gte' => $startDate->toDateString(), 'lte' => $endDate->toDateString()]]],
                                ],
                            ],
                        ],
                        'aggs' => [
                            'uploads_over_time' => [
                                'date_histogram' => [
                                    'field' => 'created_at',
                                    'calendar_interval' => 'day',
                                    'format' => 'yyyy-MM-dd',
                                    'min_doc_count' => 0,
                                    'extended_bounds' => ['min' => $startDate->toDateString(), 'max' => $endDate->toDateString()],
                                ],
                            ],
                        ],
                    ],
                ]);
                $candidateBuckets = $responseCandidates['aggregations']['uploads_over_time']['buckets'] ?? [];

                for ($i = 0; $i < 7; $i++) {
                    $date = $startDate->copy()->addDays($i);
                    $labels[] = $date->format('M d');
                    $candidateBucket = collect($candidateBuckets)->firstWhere('key_as_string', $date->format('Y-m-d'));
                    $candidateValues[] = (int) ($candidateBucket['doc_count'] ?? 0);
                }
            } catch (\Throwable $e) {
            }

            if (array_sum($candidateValues) === 0) {
                try {
                    $dbCand = DB::table('job_applications')
                        ->where('company_id', $companyId)
                        ->where('submitted_by', $userId)
                        ->whereBetween('created_at', [$startDate->toDateTimeString(), $endDate->toDateTimeString()])
                        ->selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as day_str, COUNT(*) as cnt")
                        ->groupBy('day_str')
                        ->pluck('cnt', 'day_str');

                    if ($dbCand->isNotEmpty()) {
                        $labels = [];
                        $candidateValues = [];
                        for ($i = 0; $i < 7; $i++) {
                            $date = $startDate->copy()->addDays($i);
                            $labels[] = $date->format('M d');
                            $dKey = $date->format('Y-m-d');
                            $candidateValues[] = (int) ($dbCand[$dKey] ?? 0);
                        }
                    }
                } catch (\Throwable $e) {
                }
            }

            // Last 6 months offer histogram
            $labelsCommoffer = [];
            $candidateValuesCommoffer = [];
            $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(5);
            $endOfMonthcomman = Carbon::now()->endOfMonth();

            try {
                $responseOffer = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_CANDIDATES,
                    'request_cache' => true,
                    'body' => [
                        'track_total_hits' => true,
                        'size' => 0,
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['company_id' => $companyId]],
                                    ['term' => ['submitted_by' => $userId]],
                                    ['exists' => ['field' => 'of_created_date']],
                                ],
                                'filter' => [
                                    ['range' => ['of_created_date' => ['gte' => $startOfMonthcomman->toDateString(), 'lte' => $endOfMonthcomman->toDateString()]]],
                                ],
                            ],
                        ],
                        'aggs' => [
                            'of_offer_count' => [
                                'date_histogram' => [
                                    'field' => 'of_created_date',
                                    'calendar_interval' => 'month',
                                    'format' => 'yyyy-MM',
                                    'min_doc_count' => 0,
                                    'extended_bounds' => ['min' => $startOfMonthcomman->format('Y-m'), 'max' => $endOfMonthcomman->format('Y-m')],
                                ],
                            ],
                        ],
                    ],
                ]);
                $candidateBucketsoffer = $responseOffer['aggregations']['of_offer_count']['buckets'] ?? [];

                $startDateComm = Carbon::now();
                for ($i = 5; $i >= 0; $i--) {
                    $stDate = $startDateComm->copy()->startOfMonth()->subMonths($i);
                    $labelsCommoffer[] = $stDate->format('M Y');
                    $candidateBucketoffer = collect($candidateBucketsoffer)->firstWhere('key_as_string', $stDate->format('Y-m'));
                    $candidateValuesCommoffer[] = (int) ($candidateBucketoffer['doc_count'] ?? 0);
                }
            } catch (\Throwable $e) {
            }

            if (array_sum($candidateValuesCommoffer) === 0) {
                try {
                    $dbOffers = DB::table('job_applications')
                        ->join('offer_job_applications', 'offer_job_applications.job_application_id', '=', 'job_applications.id')
                        ->where('job_applications.company_id', $companyId)
                        ->where('job_applications.submitted_by', $userId)
                        ->whereBetween('offer_job_applications.created_at', [$startOfMonthcomman->toDateTimeString(), $endOfMonthcomman->toDateTimeString()])
                        ->selectRaw("DATE_FORMAT(offer_job_applications.created_at, '%Y-%m') as month_str, COUNT(*) as cnt")
                        ->groupBy('month_str')
                        ->pluck('cnt', 'month_str');

                    if ($dbOffers->isNotEmpty()) {
                        $labelsCommoffer = [];
                        $candidateValuesCommoffer = [];
                        $startDateComm = Carbon::now();
                        for ($i = 5; $i >= 0; $i--) {
                            $stDate = $startDateComm->copy()->startOfMonth()->subMonths($i);
                            $labelsCommoffer[] = $stDate->format('M Y');
                            $mKey = $stDate->format('Y-m');
                            $candidateValuesCommoffer[] = (int) ($dbOffers[$mKey] ?? 0);
                        }
                    }
                } catch (\Throwable $e) {
                }
            }

            // Intent: Aggregate last 6 months joined candidate metrics for recruiter dashboard graph
            // Tenancy: Filtered by company_id and recruiter user_id (submitted_by)
            $labelsCommjoin = [];
            $candidateValuesCommjoin = [];
            try {
                $responseJoinedGraph = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_CANDIDATES,
                    'request_cache' => true,
                    'body' => [
                        'track_total_hits' => true,
                        'size' => 0,
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['company_id' => $companyId]],
                                    ['term' => ['submitted_by' => $userId]],
                                    ['term' => ['job_master_status_id' => 6]],
                                ],
                            ],
                        ],
                        'aggs' => [
                            'of_joined_count' => [
                                'date_histogram' => [
                                    'field' => 'of_joined_date',
                                    'calendar_interval' => 'month',
                                    'format' => 'yyyy-MM',
                                    'min_doc_count' => 0,
                                    'extended_bounds' => [
                                        'min' => $startOfMonthcomman->format('Y-m'),
                                        'max' => $endOfMonthcomman->format('Y-m'),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]);
                $candidateBucketsjoin = $responseJoinedGraph['aggregations']['of_joined_count']['buckets'] ?? [];
                $startDateCommJoin = Carbon::now();
                for ($i = 5; $i >= 0; $i--) {
                    $stDate = $startDateCommJoin->copy()->startOfMonth()->subMonths($i);
                    $labelsCommjoin[] = $stDate->format('M Y');
                    $candidateBucketjoin = collect($candidateBucketsjoin)->firstWhere('key_as_string', $stDate->format('Y-m'));
                    $candidateValuesCommjoin[] = (int) ($candidateBucketjoin['doc_count'] ?? 0);
                }
            } catch (\Throwable $e) {
            }

            if (array_sum($candidateValuesCommjoin) === 0) {
                try {
                    $dbJoinings = DB::table('job_applications')
                        ->leftJoin('offer_job_applications', 'offer_job_applications.job_application_id', '=', 'job_applications.id')
                        ->where('job_applications.company_id', $companyId)
                        ->where('job_applications.submitted_by', $userId)
                        ->where('job_applications.job_master_status_id', 6)
                        ->whereBetween(
                            DB::raw('COALESCE(offer_job_applications.joined_date, job_applications.joining_date, job_applications.updated_at)'),
                            [$startOfMonthcomman->toDateTimeString(), $endOfMonthcomman->toDateTimeString()]
                        )
                        ->selectRaw("DATE_FORMAT(COALESCE(offer_job_applications.joined_date, job_applications.joining_date, job_applications.updated_at), '%Y-%m') as month_str, COUNT(*) as cnt")
                        ->groupBy('month_str')
                        ->pluck('cnt', 'month_str');

                    $labelsCommjoin = [];
                    $candidateValuesCommjoin = [];
                    $startDateCommJoin = Carbon::now();
                    for ($i = 5; $i >= 0; $i--) {
                        $stDate = $startDateCommJoin->copy()->startOfMonth()->subMonths($i);
                        $labelsCommjoin[] = $stDate->format('M Y');
                        $mKey = $stDate->format('Y-m');
                        $candidateValuesCommjoin[] = (int) ($dbJoinings[$mKey] ?? 0);
                    }
                } catch (\Throwable $e) {
                }
            }

            // Intent: Aggregate last 6 months interview trend metrics for recruiter dashboard graph
            // Tenancy: Filtered by company_id and recruiter user_id (submitted_by / user_id)
            $interviewlabelsMonth = [];
            $interviewValuesMonth = [];
            $start6Months = Carbon::now()->startOfMonth()->subMonths(5);
            $end6Months = Carbon::now()->endOfMonth();

            try {
                $intvIndex = defined('ELASTIC_INERTVIEW_TRACKER') ? ELASTIC_INERTVIEW_TRACKER : 'local_interview_tracker_elastic';
                $hasIntvIndex = $this->elasticService->indexExists($intvIndex);
                $hasIntvData = false;

                if ($hasIntvIndex) {
                    $responseIntv = $this->elasticService->getClient()->search([
                        'index' => $intvIndex,
                        'request_cache' => true,
                        'body' => [
                            'track_total_hits' => true,
                            'size' => 0,
                            'query' => [
                                'bool' => [
                                    'should' => [
                                        ['term' => ['submitted_by' => $userId]],
                                        ['term' => ['user_id' => $userId]],
                                        ['term' => ['posted_by' => $userId]],
                                        ['term' => ['interview_created_by' => $userId]],
                                    ],
                                    'minimum_should_match' => 1,
                                    'filter' => [
                                        ['term' => ['available_status' => 1]],
                                        ['range' => ['schedule_date' => ['gte' => $start6Months->toDateString(), 'lte' => $end6Months->toDateString()]]],
                                    ],
                                ],
                            ],
                            'aggs' => [
                                'monthly_intv' => [
                                    'date_histogram' => [
                                        'field' => 'schedule_date',
                                        'calendar_interval' => 'month',
                                        'format' => 'yyyy-MM-dd',
                                        'min_doc_count' => 0,
                                        'extended_bounds' => ['min' => $start6Months->toDateString(), 'max' => $end6Months->toDateString()],
                                    ],
                                ],
                            ],
                        ],
                    ]);

                    $totalHits = $responseIntv['hits']['total']['value'] ?? 0;
                    if ($totalHits > 0) {
                        $hasIntvData = true;
                        $intvBuckets = $responseIntv['aggregations']['monthly_intv']['buckets'] ?? [];
                        $startDateCommIntv = Carbon::now();
                        for ($i = 5; $i >= 0; $i--) {
                            $stDate = $startDateCommIntv->copy()->startOfMonth()->subMonths($i);
                            $interviewlabelsMonth[] = $stDate->format('M Y');
                            $prefix = $stDate->format('Y-m');
                            $sumCount = 0;
                            foreach ($intvBuckets as $bucket) {
                                $keyStr = $bucket['key_as_string'] ?? '';
                                if (strpos($keyStr, $prefix) === 0) {
                                    $sumCount += (int) ($bucket['doc_count'] ?? 0);
                                }
                            }
                            $interviewValuesMonth[] = $sumCount;
                        }
                    }
                }

                if (! $hasIntvData) {
                    // Fallback to ELASTIC_CANDIDATES index query (attended interviews only)
                    $responseIntv = $this->elasticService->getClient()->search([
                        'index' => ELASTIC_CANDIDATES,
                        'request_cache' => true,
                        'body' => [
                            'track_total_hits' => true,
                            'size' => 0,
                            'query' => [
                                'bool' => [
                                    'must' => [
                                        ['term' => ['company_id' => $companyId]],
                                        ['term' => ['submitted_by' => $userId]],
                                        ['exists' => ['field' => 'interview_schedule_id']],
                                        ['term' => ['available_status' => 1]],
                                    ],
                                    'filter' => [
                                        ['range' => ['created_at' => ['gte' => $start6Months->toIso8601String(), 'lte' => $end6Months->toIso8601String()]]],
                                    ],
                                ],
                            ],
                            'aggs' => [
                                'monthly_intv' => [
                                    'date_histogram' => [
                                        'field' => 'created_at',
                                        'calendar_interval' => 'month',
                                        'format' => 'yyyy-MM',
                                        'min_doc_count' => 0,
                                        'extended_bounds' => ['min' => $start6Months->format('Y-m'), 'max' => $end6Months->format('Y-m')],
                                    ],
                                ],
                            ],
                        ],
                    ]);
                    $intvBuckets = $responseIntv['aggregations']['monthly_intv']['buckets'] ?? [];
                    $totalCandHits = $responseIntv['hits']['total']['value'] ?? 0;

                    if ($totalCandHits > 0) {
                        $hasIntvData = true;
                        $startDateCommIntv = Carbon::now();
                        for ($i = 5; $i >= 0; $i--) {
                            $stDate = $startDateCommIntv->copy()->startOfMonth()->subMonths($i);
                            $interviewlabelsMonth[] = $stDate->format('M Y');
                            $intvBucket = collect($intvBuckets)->firstWhere('key_as_string', $stDate->format('Y-m'));
                            $interviewValuesMonth[] = (int) ($intvBucket['doc_count'] ?? 0);
                        }
                    }
                }

                if (! $hasIntvData) {
                    // Tier 3: Direct MySQL DB query fallback on interview_schedules table (attended interviews only: available_status = 1)
                    $dbIntv = DB::table('interview_schedules')
                        ->leftJoin('job_applications', 'job_applications.id', '=', 'interview_schedules.job_application_id')
                        ->where('interview_schedules.available_status', 1)
                        ->where(function ($q) use ($companyId) {
                            $q->where('interview_schedules.company_id', $companyId)
                                ->orWhere('job_applications.company_id', $companyId);
                        })
                        ->where(function ($q) use ($userId) {
                            $q->where('job_applications.submitted_by', $userId)
                                ->orWhere('interview_schedules.interview_created_by', $userId);
                        })
                        ->whereBetween('interview_schedules.schedule_date', [$start6Months->toDateTimeString(), $end6Months->toDateTimeString()])
                        ->selectRaw("DATE_FORMAT(interview_schedules.schedule_date, '%Y-%m') as month_str, COUNT(*) as cnt")
                        ->groupBy('month_str')
                        ->pluck('cnt', 'month_str');

                    $interviewlabelsMonth = [];
                    $interviewValuesMonth = [];
                    $startDateCommIntv = Carbon::now();
                    for ($i = 5; $i >= 0; $i--) {
                        $stDate = $startDateCommIntv->copy()->startOfMonth()->subMonths($i);
                        $interviewlabelsMonth[] = $stDate->format('M Y');
                        $mKey = $stDate->format('Y-m');
                        $interviewValuesMonth[] = (int) ($dbIntv[$mKey] ?? 0);
                    }
                }
            } catch (\Throwable $e) {
                $interviewlabelsMonth = [];
                $interviewValuesMonth = [];
                for ($i = 5; $i >= 0; $i--) {
                    $stDate = Carbon::now()->startOfMonth()->subMonths($i);
                    $interviewlabelsMonth[] = $stDate->format('M Y');
                    $interviewValuesMonth[] = 0;
                }
            }

            return [
                'labels' => $labels,
                'candidateValues' => $candidateValues,
                'candidateValuesTotal' => array_sum($candidateValues),
                'labelsCommoffer' => $labelsCommoffer,
                'candidateValuesCommoffer' => $candidateValuesCommoffer,
                'candidateValuesTotaloffer' => array_sum($candidateValuesCommoffer),
                'labelsCommjoin' => $labelsCommjoin,
                'candidateValuesCommjoin' => $candidateValuesCommjoin,
                'candidateValuesTotaljoin' => array_sum($candidateValuesCommjoin),
                'interviewlabelsMonth' => $interviewlabelsMonth,
                'interviewValuesMonth' => $interviewValuesMonth,
                'interviewValuesTotalMonth' => array_sum($interviewValuesMonth),
            ];
        });
    }

    /**
     * Get joined candidate list & upcoming interview schedules (cached 5 mins).
     */
    public function getListItemsData(int $userId, int $companyId): array
    {
        return Cache::remember("profile:list_items:u{$userId}", 1800, function () use ($userId) {
            $data_list = [];
            $total_candidates_join = 0;
            $upcomingData = [];
            $labelsCommjoin = [];
            $candidateValuesCommjoin = [];
            $startDateComm = Carbon::now();

            for ($i = 5; $i >= 0; $i--) {
                $stDate = $startDateComm->copy()->startOfMonth()->subMonths($i);
                $labelsCommjoin[] = $stDate->format('M Y');
                $candidateValuesCommjoin[] = 0;
            }
            $candidateValuesTotaljoin = 0;

            // Joined candidate list & monthly histogram
            try {
                $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(6);
                $endOfMonthcomman = Carbon::now()->endOfMonth();

                $responseJoined = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_CANDIDATES,
                    'request_cache' => true,
                    'body' => [
                        'track_total_hits' => true,
                        'size' => 6,
                        'sort' => [['of_joined_date' => ['order' => 'desc']]],
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['submitted_by' => $userId]],
                                    ['term' => ['job_master_status_id' => 6]],
                                ],
                            ],
                        ],
                        'aggs' => [
                            'of_joined_count' => [
                                'date_histogram' => [
                                    'field' => 'of_joined_date',
                                    'calendar_interval' => 'month',
                                    'format' => 'yyyy-MM',
                                    'min_doc_count' => 0,
                                    'extended_bounds' => [
                                        'min' => $startOfMonthcomman->format('Y-m'),
                                        'max' => $endOfMonthcomman->format('Y-m'),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]);
                $total_candidates_join = $responseJoined['hits']['total']['value'] ?? 0;
                $data_list = array_map(fn ($hit) => $hit['_source'], $responseJoined['hits']['hits'] ?? []);

                $candidateBucketsjoin = $responseJoined['aggregations']['of_joined_count']['buckets'] ?? [];
                $candidateValuesCommjoin = [];
                $labelsCommjoin = [];
                for ($i = 5; $i >= 0; $i--) {
                    $stDate = $startDateComm->copy()->startOfMonth()->subMonths($i);
                    $labelsCommjoin[] = $stDate->format('M Y');
                    $candidateBucketjoin = collect($candidateBucketsjoin)->firstWhere('key_as_string', $stDate->format('Y-m'));
                    $candidateValuesCommjoin[] = $candidateBucketjoin['doc_count'] ?? 0;
                }
                $candidateValuesTotaljoin = array_sum($candidateValuesCommjoin);
            } catch (\Throwable $e) {
            }

            // Upcoming interview schedule list
            try {
                $responseInterview = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_INERTVIEW_TRACKER,
                    'body' => [
                        'size' => 5,
                        'query' => [
                            'bool' => [
                                'filter' => [
                                    ['term' => ['submitted_by' => $userId]],
                                    ['range' => ['schedule_date' => ['gte' => now()->toIso8601String(), 'format' => 'strict_date_optional_time']]],
                                ],
                            ],
                        ],
                        'sort' => [['schedule_date' => ['order' => 'asc']]],
                    ],
                ]);
                $upcomingData = array_map(fn ($hit) => $hit['_source'], $responseInterview['hits']['hits'] ?? []);
            } catch (\Throwable $e) {
            }

            return [
                'data_list' => $data_list,
                'total_candidates_join' => $total_candidates_join,
                'upcomingData' => $upcomingData,
                'labelsCommjoin' => $labelsCommjoin,
                'candidateValuesCommjoin' => $candidateValuesCommjoin,
                'candidateValuesTotaljoin' => $candidateValuesTotaljoin,
            ];
        });
    }

    /**
     * Get active subscription plan names (Operational & Sales) for company (cached 10 mins).
     *
     * INTENT:
     * Retrieve active Operational and Sales subscription plan names for company profile card.
     *
     * TENANCY & SCOPING:
     * Filtered strictly by tenant company_id.
     */
    public function getCompanySubscriptionPlans(int $companyId): array
    {
        return Cache::remember("profile:subscription_plans:c{$companyId}", 600, function () use ($companyId) {
            $formatPlanTitle = function (?string $title): string {
                if (empty($title)) {
                    return 'Free Plan';
                }
                $lower = strtolower($title);
                if (str_contains($lower, 'plan') || str_contains($lower, 'package')) {
                    return ucwords($title);
                }

                return ucwords($title).' Plan';
            };

            $operPlan = DB::table('company_coins')
                ->leftJoin('coin_orders', 'coin_orders.id', '=', 'company_coins.coin_order_id')
                ->leftJoin('coin_packages', 'coin_packages.id', '=', 'coin_orders.coin_package_id')
                ->leftJoin('coin_package_types', 'coin_package_types.id', '=', 'coin_packages.coin_package_type_id')
                ->where('company_coins.company_id', $companyId)
                ->where('company_coins.event_type', 'operational')
                ->select('coin_package_types.title')
                ->first();

            $salesPlan = DB::table('company_coins')
                ->leftJoin('coin_orders', 'coin_orders.id', '=', 'company_coins.coin_order_id')
                ->leftJoin('coin_packages', 'coin_packages.id', '=', 'coin_orders.coin_package_id')
                ->leftJoin('coin_package_types', 'coin_package_types.id', '=', 'coin_packages.coin_package_type_id')
                ->where('company_coins.company_id', $companyId)
                ->where('company_coins.event_type', 'sales')
                ->select('coin_package_types.title')
                ->first();

            return [
                'operational_package_name' => $formatPlanTitle($operPlan?->title),
                'sales_package_name' => $formatPlanTitle($salesPlan?->title),
            ];
        });
    }

    /**
     * Flush cached profile data for a recruiter.
     */
    public function invalidateCache(int $userId, int $companyId): void
    {
        Cache::forget("profile:user:{$userId}");
        Cache::forget("profile:kpis:u{$userId}");
        Cache::forget("profile:ranks:u{$userId}");
        Cache::forget("profile:leaderboard:c{$companyId}:u{$userId}");
        Cache::forget("profile:graphs:u{$userId}");
        Cache::forget("profile:list_items:u{$userId}");
        Cache::forget("profile:subscription_plans:c{$companyId}");
    }
}
