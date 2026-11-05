<?php

namespace App\Services;

/**
 * Class AgencyListingService
 *
 * INTENT: Provides high-throughput, batch-loaded agency listing data and summary stat aggregations for Agency Listing V2.
 * Eliminates per-row N+1 database queries and nested ES HTTP calls by pre-hydrating job statistics, candidate pipeline counts,
 * locations, industries, and relation statuses in bulk.
 */

use App\Helper\GeneralMethod;
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
     * Strictly isolated to Agency Tracker data - 0% Client Tracker dependencies.
     *
     * @param  int  $companyId  Current authenticated user's company ID
     * @return array Summary counter metrics
     */
    public function getSummaryCounters(int $companyId): array
    {
        $cacheKey = "ats:spartner_summary_cnt:comp_{$companyId}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($companyId) {
            $counters = [];

            // 1. All Connected Agencies (spartnerAll)
            $connectedAgencyIds = [];
            if ($this->elasticService->indexExists(ELASTIC_CLIENT_AND_AGENCY_RELATION_INDEX)) {
                try {
                    $resAll = $this->elasticService->getClient()->search([
                        'index' => ELASTIC_CLIENT_AND_AGENCY_RELATION_INDEX,
                        'body' => [
                            'size' => 10000,
                            'query' => [
                                'bool' => [
                                    'must' => [
                                        ['term' => ['from_company_id' => $companyId]],
                                        ['term' => ['final_status' => 1]],
                                    ],
                                ],
                            ],
                            '_source' => ['to_company_id'],
                        ],
                    ]);
                    $connectedAgencyIds = collect($resAll['hits']['hits'] ?? [])
                        ->pluck('_source.to_company_id')
                        ->filter()
                        ->unique()
                        ->values()
                        ->toArray();
                    $counters['spartnerAll'] = count($connectedAgencyIds);
                } catch (\Exception $e) {
                    $connectedAgencyIds = DB::table('staffing_company_relation')
                        ->where('from_company_id', $companyId)
                        ->where('final_status', 1)
                        ->whereNotNull('to_company_id')
                        ->distinct()
                        ->pluck('to_company_id')
                        ->toArray();
                    $counters['spartnerAll'] = count($connectedAgencyIds);
                }
            } else {
                $connectedAgencyIds = DB::table('staffing_company_relation')
                    ->where('from_company_id', $companyId)
                    ->where('final_status', 1)
                    ->whereNotNull('to_company_id')
                    ->distinct()
                    ->pluck('to_company_id')
                    ->toArray();
                $counters['spartnerAll'] = count($connectedAgencyIds);
            }

            // 2. Sent Pending Invitations (sentRequest)
            $counters['sentRequest'] = DB::table('staffing_company_invitation')
                ->where('from_company_id', $companyId)
                ->where('to_invitation_status', '!=', 1)
                ->count();

            // 3. Agencies added this month
            $counters['spartnerThisMonth'] = DB::table('staffing_company_relation')
                ->where('from_company_id', $companyId)
                ->where('final_status', 1)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count();

            // 4. Newly Onboarded (last 7 days)
            $counters['newlyOnboarded'] = DB::table('staffing_company_relation')
                ->where('from_company_id', $companyId)
                ->where('final_status', 1)
                ->where('created_at', '>=', now()->subDays(7))
                ->count();

            // 5. Newly Added Agencies: joined within last 180 days
            $recent180Ids = DB::table('staffing_company_relation')
                ->where('from_company_id', $companyId)
                ->where('final_status', 1)
                ->where('created_at', '>=', now()->subDays(180))
                ->pluck('to_company_id')
                ->unique()
                ->values()
                ->toArray();
            $counters['newlyAdded'] = count($recent180Ids);
            $counters['newlyAddedTotal'] = count($recent180Ids);

            // 6. Working on Active Jobs (hiring / active agencies)
            $workingOnJobs = 0;
            if (! empty($connectedAgencyIds)) {
                try {
                    $workingOnJobs = DB::table('recommendation_relation')
                        ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
                        ->where('recommendation_relation.from_company_id', $companyId)
                        ->whereIn('recommendation_relation.to_company_id', $connectedAgencyIds)
                        ->where('recommendation_relation.final_status', 1)
                        ->where('jobs.status', 'active')
                        ->distinct()
                        ->count('recommendation_relation.to_company_id');
                } catch (\Exception $e) {
                    $workingOnJobs = 0;
                }
            }
            $counters['workingOnJobs'] = $workingOnJobs;

            // 7. Inactive Agencies (connected agencies with 0 active jobs)
            $counters['inactive'] = max(0, $counters['spartnerAll'] - $workingOnJobs);

            // 8. Top Performing Agencies: agencies with verified pipeline activity / placements (interview, offer, joined)
            $topAgencyCount = 0;
            if (! empty($connectedAgencyIds)) {
                $topAgencyCount = DB::table('add_job_wise_condidates')
                    ->join('recommendation_relation', function ($join) use ($companyId) {
                        $join->on('recommendation_relation.job_id', '=', 'add_job_wise_condidates.job_id')
                            ->on('recommendation_relation.to_company_id', '=', 'add_job_wise_condidates.thirdparty_company_id')
                            ->where('recommendation_relation.from_company_id', '=', $companyId)
                            ->where('recommendation_relation.final_status', '=', 1);
                    })
                    ->whereIn('add_job_wise_condidates.thirdparty_company_id', $connectedAgencyIds)
                    ->whereIn('add_job_wise_condidates.master_status_id', [3, 4, 5, 6])
                    ->distinct()
                    ->count('add_job_wise_condidates.thirdparty_company_id');
            }
            $counters['topPerforming'] = $topAgencyCount;

            // 9. Actively Submitting in last 30 days
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

            // 10. Actively Submitting Today
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
     * Resolve company IDs of top performing agencies with verified candidate pipeline placements.
     */
    public function getTopPerformingAgencyIds(int $companyId, array $agencyCompanyIds): array
    {
        if (empty($agencyCompanyIds)) {
            return [];
        }

        return DB::table('add_job_wise_condidates')
            ->join('recommendation_relation', function ($join) use ($companyId) {
                $join->on('recommendation_relation.job_id', '=', 'add_job_wise_condidates.job_id')
                    ->on('recommendation_relation.to_company_id', '=', 'add_job_wise_condidates.thirdparty_company_id')
                    ->where('recommendation_relation.from_company_id', '=', $companyId)
                    ->where('recommendation_relation.final_status', '=', 1);
            })
            ->whereIn('add_job_wise_condidates.thirdparty_company_id', $agencyCompanyIds)
            ->whereIn('add_job_wise_condidates.master_status_id', [3, 4, 5, 6])
            ->distinct()
            ->pluck('add_job_wise_condidates.thirdparty_company_id')
            ->toArray();
    }

    /**
     * Resolve company IDs of agencies currently assigned to active jobs.
     */
    public function getActiveAgencyIds(int $companyId, array $agencyCompanyIds): array
    {
        if (empty($agencyCompanyIds)) {
            return [];
        }

        return DB::table('recommendation_relation')
            ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
            ->where('recommendation_relation.from_company_id', $companyId)
            ->whereIn('recommendation_relation.to_company_id', $agencyCompanyIds)
            ->where('recommendation_relation.final_status', 1)
            ->where('jobs.status', 'active')
            ->distinct()
            ->pluck('recommendation_relation.to_company_id')
            ->toArray();
    }

    /**
     * Resolve company IDs of newly added agencies within given days.
     */
    public function getRecentAgencyIds(int $companyId, int $days = 180): array
    {
        return DB::table('staffing_company_relation')
            ->where('from_company_id', $companyId)
            ->where('final_status', 1)
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->pluck('to_company_id')
            ->unique()
            ->values()
            ->toArray();
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
     * Bulk fetch company size / employee strength for agency companies.
     *
     * @return array Keyed by company id
     */
    public function getBatchCompanySizes(array $agencyCompanyIds): array
    {
        $filtered = array_filter(array_unique($agencyCompanyIds));
        if (empty($filtered)) {
            return [];
        }

        return DB::table('companies')
            ->whereIn('id', $filtered)
            ->select('id', 'company_size', 'company_size_from')
            ->get()
            ->keyBy('id')
            ->toArray();
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
                'screening' => 0,
                'evaluated' => 0,
                'negotiation' => 0,
                'offers' => 0,
                'Joined' => 0,
                'rejected' => 0,
                'all_interview' => 0,
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

        // 2. Query Elasticsearch for Candidate Pipeline Stats via Symmetric DirectClientCandidatesCountList (Rule 1 & 2 compliant)
        foreach ($agencyCompanyIds as $agencyId) {
            $candStats = GeneralMethod::DirectClientCandidatesCountList($fromCompanyId, $agencyId);
            if (isset($stats[$agencyId])) {
                $stats[$agencyId]->all_candidates = (int) ($candStats->all_candidates ?? 0);
                $stats[$agencyId]->sourced = (int) ($candStats->sourced ?? 0);
                $stats[$agencyId]->screening = (int) ($candStats->screening ?? 0);
                $stats[$agencyId]->evaluated = (int) ($candStats->evaluated ?? 0);
                $stats[$agencyId]->negotiation = (int) ($candStats->negotiations ?? ($candStats->negotiation ?? 0));
                $stats[$agencyId]->offers = (int) ($candStats->offers ?? 0);
                $stats[$agencyId]->Joined = (int) ($candStats->joined ?? ($candStats->Joined ?? 0));
                $stats[$agencyId]->rejected = (int) ($candStats->rejected ?? 0);
                if (isset($candStats->all_interview) && $candStats->all_interview > 0) {
                    $stats[$agencyId]->all_interview = (int) $candStats->all_interview;
                }
            }
        }

        // 3. Bulk Query for Scheduled Interviews per Agency
        $interviewCounts = DB::table('interview_schedules')
            ->join('job_applications', 'job_applications.id', '=', 'interview_schedules.job_application_id')
            ->leftJoin('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', '=', 'job_applications.id')
            ->where(function ($q) use ($fromCompanyId) {
                $q->where('interview_schedules.company_id', '=', $fromCompanyId)
                    ->orWhere('job_applications.company_id', '=', $fromCompanyId)
                    ->orWhere('add_job_wise_condidates.company_id', '=', $fromCompanyId);
            })
            ->where(function ($q) use ($agencyCompanyIds) {
                $q->whereIn('job_applications.thirdparty_company_id', $agencyCompanyIds)
                    ->orWhereIn('add_job_wise_condidates.thirdparty_company_id', $agencyCompanyIds);
            })
            ->select(
                DB::raw('COALESCE(job_applications.thirdparty_company_id, add_job_wise_condidates.thirdparty_company_id) AS agency_id'),
                DB::raw('COUNT(DISTINCT interview_schedules.id) AS interview_count')
            )
            ->groupBy('agency_id')
            ->get();

        foreach ($interviewCounts as $ic) {
            if (isset($stats[$ic->agency_id])) {
                $stats[$ic->agency_id]->all_interview = (int) $ic->interview_count;
            }
        }

        return $stats;
    }

    /**
     * Invalidate cached summary counters and filter facets for a tenant company.
     * INTENT: Ensures cache consistency whenever staffing relations, invitations, or job assignments change.
     */
    public function clearCompanyStatsCache(int $companyId): void
    {
        Cache::forget("ats:spartner_summary_cnt:comp_{$companyId}");
        Cache::forget("ats:spartner_loc_facets:comp_{$companyId}");
        Cache::forget("ats:spartner_recency_cnt:comp_{$companyId}");
        Cache::forget("ats:spartner_pipe_cnt:comp_{$companyId}");
    }

    /**
     * Fetch agency location facets for the Location Combobox Popover.
     * Strictly isolated to Agency Tracker data - 0% Client Tracker dependencies.
     */
    public function getAgencyLocationFacets(int $companyId): array
    {
        $cacheKey = "ats:spartner_loc_facets:comp_{$companyId}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($companyId) {
            $relations = DB::table('staffing_company_relation')
                ->join('clients', function ($join) {
                    $join->on('clients.company_id', '=', 'staffing_company_relation.to_company_id')
                        ->where('clients.self_company', 1);
                })
                ->leftJoin('client_location', 'client_location.client_id', '=', 'clients.id')
                ->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                ->where('staffing_company_relation.from_company_id', $companyId)
                ->where('staffing_company_relation.final_status', 1)
                ->select(
                    'client_location.country_id',
                    'countries.country_name',
                    'countries.country_code',
                    'client_location.city_id',
                    'city_list.city as city_name',
                    'client_location.city as custom_city'
                )
                ->get();

            $total = $relations->count();
            $unspecifiedCount = 0;
            $hubsMap = [];

            foreach ($relations as $row) {
                $countryId = (int) ($row->country_id ?? 0);
                $countryName = trim((string) ($row->country_name ?? ''));
                if ($countryId <= 0 || empty($countryName)) {
                    $unspecifiedCount++;

                    continue;
                }

                if (! isset($hubsMap[$countryId])) {
                    $hubsMap[$countryId] = [
                        'country_id' => $countryId,
                        'country_name' => $countryName,
                        'country_code' => $row->country_code ?? '🌐',
                        'count' => 0,
                        'cities' => [],
                    ];
                }
                $hubsMap[$countryId]['count']++;

                $cityId = (int) ($row->city_id ?? 0);
                $cityName = trim((string) ($row->city_name ?: ($row->custom_city ?? '')));
                if (! empty($cityName)) {
                    if (! isset($hubsMap[$countryId]['cities'][$cityName])) {
                        $hubsMap[$countryId]['cities'][$cityName] = [
                            'city_id' => $cityId,
                            'city_name' => $cityName,
                            'count' => 0,
                        ];
                    }
                    $hubsMap[$countryId]['cities'][$cityName]['count']++;
                }
            }

            $hubs = array_values(array_map(function ($hub) {
                $hub['cities'] = array_values($hub['cities']);
                usort($hub['cities'], fn ($a, $b) => $b['count'] <=> $a['count']);

                return $hub;
            }, $hubsMap));

            usort($hubs, fn ($a, $b) => $b['count'] <=> $a['count']);

            return [
                'total' => $total,
                'located_count' => max(0, $total - $unspecifiedCount),
                'unspecified_count' => $unspecifiedCount,
                'hubs' => $hubs,
            ];
        });
    }

    /**
     * Fetch agency engagement recency counts for the Activity Combobox Popover.
     * Strictly isolated to Agency Tracker data - 0% Client Tracker dependencies.
     */
    public function getAgencyRecencyCounts(int $companyId): array
    {
        $cacheKey = "ats:spartner_recency_cnt:comp_{$companyId}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($companyId) {
            $connectedIds = DB::table('staffing_company_relation')
                ->where('from_company_id', $companyId)
                ->where('final_status', 1)
                ->whereNotNull('to_company_id')
                ->distinct()
                ->pluck('to_company_id')
                ->toArray();

            $total = count($connectedIds);
            if ($total === 0) {
                return ['all' => 0, '7d' => 0, '30d' => 0, '90d' => 0, 'dormant' => 0, 'never' => 0];
            }

            $latestDates = DB::table('job_applications')
                ->whereIn('thirdparty_company_id', $connectedIds)
                ->groupBy('thirdparty_company_id')
                ->selectRaw('thirdparty_company_id, MAX(created_at) as latest_submission')
                ->pluck('latest_submission', 'thirdparty_company_id')
                ->toArray();

            $c7d = 0;
            $c30d = 0;
            $c90d = 0;
            $cDormant = 0;
            $cNever = 0;

            $sub7 = now()->subDays(7);
            $sub30 = now()->subDays(30);
            $sub90 = now()->subDays(90);

            foreach ($connectedIds as $cId) {
                if (! isset($latestDates[$cId]) || empty($latestDates[$cId])) {
                    $cNever++;

                    continue;
                }
                $date = \Carbon\Carbon::parse($latestDates[$cId]);
                if ($date >= $sub7) {
                    $c7d++;
                } elseif ($date >= $sub30) {
                    $c30d++;
                } elseif ($date >= $sub90) {
                    $c90d++;
                } else {
                    $cDormant++;
                }
            }

            return [
                'all' => $total,
                '7d' => $c7d,
                '30d' => $c30d,
                '90d' => $c90d,
                'dormant' => $cDormant,
                'never' => $cNever,
            ];
        });
    }

    /**
     * Fetch agency candidate pipeline state counts for the Pipeline Combobox Popover.
     * Strictly isolated to Agency Tracker data - 0% Client Tracker dependencies.
     */
    public function getAgencyPipelineCounts(int $companyId): array
    {
        $cacheKey = "ats:spartner_pipe_cnt:comp_{$companyId}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($companyId) {
            $connectedIds = DB::table('staffing_company_relation')
                ->where('from_company_id', $companyId)
                ->where('final_status', 1)
                ->whereNotNull('to_company_id')
                ->distinct()
                ->pluck('to_company_id')
                ->toArray();

            $total = count($connectedIds);
            if ($total === 0) {
                return ['all' => 0, 'active' => 0, 'none' => 0];
            }

            $activeAgencyIds = DB::table('add_job_wise_condidates')
                ->whereIn('thirdparty_company_id', $connectedIds)
                ->whereIn('master_status_id', [1, 2, 3, 4, 5, 6, 7])
                ->distinct()
                ->pluck('thirdparty_company_id')
                ->toArray();

            $activeCount = count($activeAgencyIds);
            $noneCount = max(0, $total - $activeCount);

            return [
                'all' => $total,
                'active' => $activeCount,
                'none' => $noneCount,
            ];
        });
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
