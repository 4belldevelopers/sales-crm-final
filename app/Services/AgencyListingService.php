<?php

namespace App\Services;

/**
 * Class AgencyListingService
 *
 * INTENT: Provides high-throughput, batch-loaded agency listing data and summary stat aggregations for Agency Listing V2.
 * Eliminates per-row N+1 database queries and nested ES HTTP calls by pre-hydrating job statistics, candidate pipeline counts,
 * locations, industries, and relation statuses in bulk.
 */

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AgencyListingService
{
    /**
     * @var ElasticService
     */
    protected $elasticService;

    /**
     * AgencyListingService constructor.
     */
    public function __construct(ElasticService $elasticService)
    {
        $this->elasticService = $elasticService;
    }

    /**
     * Fetch top summary counter cards for the logged-in company with Redis caching.
     *
     * @param  int  $companyId  Current authenticated user's company ID
     * @return array Summary counter metrics
     */
    public function getSummaryCounters(int $companyId): array
    {
        $cacheKey = "ats:spartner_summary_cnt:comp_{$companyId}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($companyId) {
            $counters = [];

            // 1. All Connected Agencies
            $clientAll = DB::table('companies')
                ->select('companies.id')
                ->leftJoin('staffing_company_relation', 'staffing_company_relation.to_company_id', '=', 'companies.id')
                ->leftJoin('clients', 'clients.company_id', '=', 'staffing_company_relation.to_company_id')
                ->where('staffing_company_relation.final_status', 1)
                ->where('staffing_company_relation.from_company_id', $companyId)
                ->groupBy('staffing_company_relation.to_company_id')
                ->get();
            $counters['spartnerAll'] = count($clientAll);
            $counters['clientAll'] = 0;

            // 2. Sent Requests
            $counters['sentRequest'] = DB::table('staffing_company_invitation')
                ->where('from_company_id', $companyId)
                ->where('to_invitation_status', '!=', 1)
                ->count();

            // 3. Direct Clients via Elasticsearch
            $clientDirect = 0;
            if ($this->elasticService->indexExists(ELASTIC_CLIENT_AND_AGENCY_RELATION_INDEX)) {
                try {
                    $resDirect = $this->elasticService->getClient()->count([
                        'index' => ELASTIC_CLIENT_AND_AGENCY_RELATION_INDEX,
                        'body' => [
                            'query' => [
                                'bool' => [
                                    'must' => [
                                        ['term' => ['to_company_id' => $companyId]],
                                        ['term' => ['final_status' => 1]],
                                    ],
                                    'must_not' => [
                                        ['term' => ['from_company_id' => $companyId]],
                                    ],
                                ],
                            ],
                        ],
                    ]);
                    $clientDirect = $resDirect['count'] ?? 0;
                } catch (\Exception $e) {
                    $clientDirect = DB::table('staffing_company_relation')
                        ->where('to_company_id', $companyId)
                        ->where('from_company_id', '!=', $companyId)
                        ->where('final_status', 1)
                        ->count();
                }
            } else {
                $clientDirect = DB::table('staffing_company_relation')
                    ->where('to_company_id', $companyId)
                    ->where('from_company_id', '!=', $companyId)
                    ->where('final_status', 1)
                    ->count();
            }
            $counters['clientDirect'] = $clientDirect;

            // 4. Custom Clients via Elasticsearch
            $clientCustom = 0;
            if ($this->elasticService->indexExists(ELASTIC_CLIENT_LIST)) {
                try {
                    $resCustom = $this->elasticService->getClient()->count([
                        'index' => ELASTIC_CLIENT_LIST,
                        'body' => [
                            'query' => [
                                'bool' => [
                                    'must' => [
                                        ['term' => ['company_id' => $companyId]],
                                    ],
                                    'must_not' => [
                                        ['term' => ['self_company' => 1]],
                                    ],
                                ],
                            ],
                        ],
                    ]);
                    $clientCustom = $resCustom['count'] ?? 0;
                } catch (\Exception $e) {
                    $clientCustom = DB::table('clients')
                        ->where('self_company', '!=', 1)
                        ->where('company_id', $companyId)
                        ->count();
                }
            } else {
                $clientCustom = DB::table('clients')
                    ->where('self_company', '!=', 1)
                    ->where('company_id', $companyId)
                    ->count();
            }
            $counters['clientCustom'] = $clientCustom;

            // 5. Agencies added this month
            $counters['spartnerThisMonth'] = DB::table('staffing_company_relation')
                ->where('from_company_id', $companyId)
                ->where('final_status', 1)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count();

            // 6. Newly Onboarded (last 7 days)
            $counters['newlyOnboarded'] = DB::table('staffing_company_relation')
                ->where('from_company_id', $companyId)
                ->where('final_status', 1)
                ->where('created_at', '>=', now()->subDays(7))
                ->count();

            // Connected agency IDs
            $connectedAgencyIds = DB::table('staffing_company_relation')
                ->where('from_company_id', $companyId)
                ->where('final_status', 1)
                ->pluck('to_company_id')
                ->toArray();

            if (empty($connectedAgencyIds)) {
                $counters['workingOnJobs'] = 0;
                $counters['workingOnJobsThisMonth'] = 0;
            } else {
                // Working on Jobs (ES cardinality aggregation)
                try {
                    $esJobs = $this->elasticService->getClient()->search([
                        'index' => ELASTIC_JOBS,
                        'body' => [
                            'size' => 0,
                            'query' => [
                                'bool' => [
                                    'must' => [
                                        ['term' => ['status' => 'active']],
                                        [
                                            'nested' => [
                                                'path' => 'recommendation_relation',
                                                'query' => [
                                                    'bool' => [
                                                        'must' => [
                                                            ['term' => ['recommendation_relation.from_company_id' => $companyId]],
                                                            ['term' => ['recommendation_relation.final_status' => 1]],
                                                            ['terms' => ['recommendation_relation.to_company_id' => $connectedAgencyIds]],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'aggs' => [
                                'agencies' => [
                                    'nested' => ['path' => 'recommendation_relation'],
                                    'aggs' => [
                                        'filtered' => [
                                            'filter' => [
                                                'bool' => [
                                                    'must' => [
                                                        ['term' => ['recommendation_relation.from_company_id' => $companyId]],
                                                        ['term' => ['recommendation_relation.final_status' => 1]],
                                                        ['terms' => ['recommendation_relation.to_company_id' => $connectedAgencyIds]],
                                                    ],
                                                ],
                                            ],
                                            'aggs' => [
                                                'distinct_agencies' => [
                                                    'cardinality' => ['field' => 'recommendation_relation.to_company_id'],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ]);
                    $counters['workingOnJobs'] = $esJobs['aggregations']['agencies']['filtered']['distinct_agencies']['value'] ?? 0;
                } catch (\Exception $e) {
                    $counters['workingOnJobs'] = 0;
                }

                // Working on Jobs This Month
                try {
                    $startOfMonth = now()->startOfMonth()->toIso8601String();
                    $endOfMonth = now()->endOfMonth()->toIso8601String();
                    $esJobsMonth = $this->elasticService->getClient()->search([
                        'index' => ELASTIC_JOBS,
                        'body' => [
                            'size' => 0,
                            'query' => [
                                'bool' => [
                                    'must' => [
                                        [
                                            'nested' => [
                                                'path' => 'recommendation_relation',
                                                'query' => [
                                                    'bool' => [
                                                        'must' => [
                                                            ['term' => ['recommendation_relation.from_company_id' => $companyId]],
                                                            ['term' => ['recommendation_relation.final_status' => 1]],
                                                            ['terms' => ['recommendation_relation.to_company_id' => $connectedAgencyIds]],
                                                            ['range' => ['recommendation_relation.created_at' => ['gte' => $startOfMonth, 'lte' => $endOfMonth]]],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'aggs' => [
                                'agencies' => [
                                    'nested' => ['path' => 'recommendation_relation'],
                                    'aggs' => [
                                        'filtered' => [
                                            'filter' => [
                                                'bool' => [
                                                    'must' => [
                                                        ['term' => ['recommendation_relation.from_company_id' => $companyId]],
                                                        ['term' => ['recommendation_relation.final_status' => 1]],
                                                        ['terms' => ['recommendation_relation.to_company_id' => $connectedAgencyIds]],
                                                        ['range' => ['recommendation_relation.created_at' => ['gte' => $startOfMonth, 'lte' => $endOfMonth]]],
                                                    ],
                                                ],
                                            ],
                                            'aggs' => [
                                                'distinct_agencies' => [
                                                    'cardinality' => ['field' => 'recommendation_relation.to_company_id'],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ]);
                    $counters['workingOnJobsThisMonth'] = $esJobsMonth['aggregations']['agencies']['filtered']['distinct_agencies']['value'] ?? 0;
                } catch (\Exception $e) {
                    $counters['workingOnJobsThisMonth'] = 0;
                }
            }

            // 7. Actively Submitting in last 30 days
            $counters['activelySubmitting'] = DB::table('job_applications')
                ->join('staffing_company_relation', function ($join) use ($companyId) {
                    $join->on('staffing_company_relation.to_company_id', '=', 'job_applications.thirdparty_company_id')
                        ->where('staffing_company_relation.from_company_id', $companyId)
                        ->where('staffing_company_relation.final_status', 1);
                })
                ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
                ->where('jobs.company_id', $companyId)
                ->where('job_applications.created_at', '>=', now()->subDays(30))
                ->distinct('job_applications.thirdparty_company_id')
                ->count('job_applications.thirdparty_company_id');

            // 8. Actively Submitting Today
            $counters['activelySubmittingToday'] = DB::table('job_applications')
                ->join('staffing_company_relation', function ($join) use ($companyId) {
                    $join->on('staffing_company_relation.to_company_id', '=', 'job_applications.thirdparty_company_id')
                        ->where('staffing_company_relation.from_company_id', $companyId)
                        ->where('staffing_company_relation.final_status', 1);
                })
                ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
                ->where('jobs.company_id', $companyId)
                ->whereDate('job_applications.created_at', now()->toDateString())
                ->distinct('job_applications.thirdparty_company_id')
                ->count('job_applications.thirdparty_company_id');

            return $counters;
        });
    }

    /**
     * Bulk fetch relation data for a list of agency company IDs.
     * INTENT + TENANCY: Filters by from_company_id to isolate logged-in tenant company.
     *
     * @param  array  $agencyCompanyIds  List of target agency company IDs
     * @param  int  $fromCompanyId  Current authenticated company ID
     * @return array Keyed by to_company_id
     */
    public function getBatchRelationData(array $agencyCompanyIds, int $fromCompanyId): array
    {
        if (empty($agencyCompanyIds)) {
            return [];
        }

        $relations = DB::table('staffing_company_relation')
            ->where('from_company_id', $fromCompanyId)
            ->whereIn('to_company_id', $agencyCompanyIds)
            ->get();

        $result = [];
        foreach ($relations as $rel) {
            $result[$rel->to_company_id] = $rel;
        }

        return $result;
    }

    /**
     * Bulk fetch industry names for a list of industry IDs.
     *
     * @param  array  $industryIds  List of industry IDs
     * @return array Keyed by ind_id
     */
    public function getBatchIndustries(array $industryIds): array
    {
        $filtered = array_filter(array_unique($industryIds));
        if (empty($filtered)) {
            return [];
        }

        return DB::table('td_industries')
            ->whereIn('ind_id', $filtered)
            ->get()
            ->keyBy('ind_id')
            ->toArray();
    }

    /**
     * Bulk fetch client location details for a list of client IDs.
     *
     * @param  array  $clientIds  List of client IDs
     * @return array Keyed by client_id
     */
    public function getBatchLocations(array $clientIds): array
    {
        $filtered = array_filter(array_unique($clientIds));
        if (empty($filtered)) {
            return [];
        }

        $locations = DB::table('client_location')
            ->select(
                'client_location.id',
                'client_location.client_id',
                'client_location.city',
                'client_location.address',
                'client_location.postcode',
                'client_location.country_id',
                'client_location.state',
                'city_list.city as city_name',
                'state_list.state as state_name'
            )
            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
            ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
            ->whereIn('client_location.client_id', $filtered)
            ->get();

        $result = [];
        foreach ($locations as $loc) {
            $result[$loc->client_id] = $loc;
        }

        return $result;
    }

    /**
     * Bulk fetch job counts and candidate pipeline stats for all agencies on the current page in 2 bulk SQL queries.
     * INTENT: Eliminates per-row N+1 DB/ES calls inside Blade views.
     *
     * @param  array  $agencyCompanyIds  List of agency company IDs on current page
     * @param  int  $fromCompanyId  Logged-in tenant company ID
     * @return array Keyed by agency company_id containing job and candidate counts
     */
    public function getBatchAgencyStats(array $agencyCompanyIds, int $fromCompanyId): array
    {
        if (empty($agencyCompanyIds)) {
            return [];
        }

        // Initialize default zero metrics for every agency ID
        $stats = [];
        foreach ($agencyCompanyIds as $id) {
            $stats[$id] = (object) [
                'job_active' => 0,
                'job_inactive' => 0,
                'job_hold' => 0,
                'all_candidates' => 0,
                'sourced' => 0,
                'evaluated' => 0,
                'all_interview' => 0,
                'offers' => 0,
                'Joined' => 0,
            ];
        }

        // 1. Bulk Query for Job Counts (Active, Inactive/Closed, Hold)
        $jobCounts = DB::table('recommendation_relation')
            ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
            ->select(
                'recommendation_relation.to_company_id',
                DB::raw("COUNT(CASE WHEN jobs.status = 'active' THEN 1 END) AS job_active"),
                DB::raw("COUNT(CASE WHEN jobs.status = 'inactive' THEN 1 END) AS job_inactive"),
                DB::raw("COUNT(CASE WHEN jobs.status = 'hold' THEN 1 END) AS job_hold")
            )
            ->where('recommendation_relation.from_company_id', $fromCompanyId)
            ->whereIn('recommendation_relation.to_company_id', $agencyCompanyIds)
            ->where('recommendation_relation.final_status', 1)
            ->groupBy('recommendation_relation.to_company_id')
            ->get();

        foreach ($jobCounts as $jc) {
            if (isset($stats[$jc->to_company_id])) {
                $stats[$jc->to_company_id]->job_active = (int) $jc->job_active;
                $stats[$jc->to_company_id]->job_inactive = (int) $jc->job_inactive;
                $stats[$jc->to_company_id]->job_hold = (int) $jc->job_hold;
            }
        }

        // 2. Bulk Query for Candidate Pipeline Stats
        $candidateCounts = DB::table('add_job_wise_condidates')
            ->join('recommendation_relation', function ($join) use ($fromCompanyId) {
                $join->on('recommendation_relation.job_id', '=', 'add_job_wise_condidates.job_id')
                    ->on('recommendation_relation.to_company_id', '=', 'add_job_wise_condidates.thirdparty_company_id')
                    ->where('recommendation_relation.from_company_id', '=', $fromCompanyId)
                    ->where('recommendation_relation.final_status', '=', 1);
            })
            ->select(
                'add_job_wise_condidates.thirdparty_company_id',
                DB::raw('COUNT(1) AS all_candidates'),
                DB::raw('COUNT(CASE WHEN add_job_wise_condidates.master_status_id = 1 THEN 1 END) AS sourced'),
                DB::raw('COUNT(CASE WHEN add_job_wise_condidates.master_status_id = 3 THEN 1 END) AS evaluated'),
                DB::raw('COUNT(CASE WHEN add_job_wise_condidates.master_status_id = 7 THEN 1 END) AS all_interview'),
                DB::raw('COUNT(CASE WHEN add_job_wise_condidates.master_status_id = 10 THEN 1 END) AS offers'),
                DB::raw('COUNT(CASE WHEN add_job_wise_condidates.master_status_id = 6 THEN 1 END) AS joined')
            )
            ->whereIn('add_job_wise_condidates.thirdparty_company_id', $agencyCompanyIds)
            ->whereNotIn('add_job_wise_condidates.master_status_id', [8, 9])
            ->groupBy('add_job_wise_condidates.thirdparty_company_id')
            ->get();

        foreach ($candidateCounts as $cc) {
            if (isset($stats[$cc->thirdparty_company_id])) {
                $stats[$cc->thirdparty_company_id]->all_candidates = (int) $cc->all_candidates;
                $stats[$cc->thirdparty_company_id]->sourced = (int) $cc->sourced;
                $stats[$cc->thirdparty_company_id]->evaluated = (int) $cc->evaluated;
                $stats[$cc->thirdparty_company_id]->all_interview = (int) $cc->all_interview;
                $stats[$cc->thirdparty_company_id]->offers = (int) $cc->offers;
                $stats[$cc->thirdparty_company_id]->Joined = (int) $cc->joined;
            }
        }

        return $stats;
    }

    /**
     * Invalidate cached summary counters for a tenant company.
     * INTENT: Ensures cache consistency whenever staffing relations, invitations, or job assignments change.
     */
    public function clearCompanyStatsCache(int $companyId): void
    {
        Cache::forget("ats:spartner_summary_cnt:comp_{$companyId}");
    }

    /**
     * Invalidate user-scoped agency listing AJAX cache version.
     */
    public function clearUserConsultancyCache(int $userId): void
    {
        $versionKey = 'allconsultancy_version_user_'.$userId;
        Cache::increment($versionKey);
    }
}
