<?php

namespace App\Repositories;

use App\Helper\GeneralMethod;
use App\Models\Client;
use App\Models\Job;
use App\Services\AgencyCommercialTermsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Repository: AccountRepository
 *
 * Intent: Encapsulates database queries for company-specific account settings and profile data.
 * Applies tenant scoping by company_id and utilizes Redis caching to avoid redundant DB hits.
 *
 * Author: Antigravity AI
 * Date: August 12, 2026
 */
class AccountRepository
{
    private const CACHE_TTL_COMPANY_SETTINGS = 3600; // 1 hour TTL for company account settings

    /**
     * Fetch tenant account profile and settings data.
     *
     * Tenancy: Strictly filtered by $companyId.
     */
    public function getCompanyAccountSettings(int $companyId): array
    {
        $cacheKey = "account_settings:company:{$companyId}";

        return Cache::remember($cacheKey, self::CACHE_TTL_COMPANY_SETTINGS, function () use ($companyId) {
            // 1. Boost Keywords
            $boostkeywords = DB::table('boostkeywords')
                ->join('countries', 'countries.id', '=', 'boostkeywords.country_id')
                ->where('boostkeywords.company_id', $companyId)
                ->select('boostkeywords.*', 'countries.country_name')
                ->orderByDesc('boostkeywords.id')
                ->get();

            // 2. Company & Self Client
            $account = DB::table('companies')->select('*')->where('id', $companyId)->first();
            $client = Client::where('clients.company_id', '=', $companyId)
                ->where('clients.self_company', '=', 1)
                ->first();

            // 3. Overview Calculations
            $yearAgo = '';
            if ($account && $account->founded > 0) {
                $yearAgo = GeneralMethod::timeAgo($account->founded.'-01-01');
            }
            $joined = '';
            if ($account && $account->created_at > 0) {
                $joined = GeneralMethod::timeAgo($account->created_at);
            }
            $industriesName = '';
            if ($account && $account->industries > 0) {
                $industries = DB::table('td_industries')
                    ->select('td_industries.ind_id', 'td_industries.ind_name')
                    ->where('td_industries.ind_id', '=', $account->industries)
                    ->orderBy('td_industries.ind_name', 'ASC')
                    ->first();
                if ($industries) {
                    $industriesName = $industries->ind_name;
                }
            }

            // 4. Tags, Active Jobs, Team
            $tags = DB::table('company_tags')->select('company_tags.id', 'company_tags.tag_name')->where('company_id', $companyId)->get();
            $activeJobs = Job::where('status', 'active')->where('company_id', '=', $companyId)->count();
            $team = DB::table('users')->select('id', 'name', 'image')->where('type', 0)->where('company_id', $companyId)->where('status', 'active')->take(5)->get();
            $teamCount = DB::table('users')->where('type', 0)->where('company_id', $companyId)->where('status', 'active')->count();

            // 5. Client Locations & Contacts
            $clientId = @$client->id;
            $clientLocationCount = 0;
            $clientLocation = null;
            $allLocations = [];
            if ($clientId) {
                $clientLocations = DB::table('client_location')
                    ->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                    ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
                    ->select('client_location.*', 'countries.country_name', 'city_list.city as city_name_resolved', 'state_list.state as state_name')
                    ->where('client_location.client_id', $clientId)
                    ->orderBy('client_location.id', 'ASC')
                    ->get();
                $clientLocationCount = $clientLocations->count();
                $clientLocation = $clientLocations->first();
                $allLocations = $clientLocations->all();
            }

            $companyCityName = '';
            $companyCountryName = '';
            if ($clientLocationCount > 0 && $clientLocation) {
                if ($clientLocation->country_id == 101) {
                    $cityObj = DB::table('city_list')->select('city_list.city')->where('city_list.city_id', $clientLocation->city_id)->first();
                    $companyCityName = $cityObj ? ucwords($cityObj->city) : '';
                } else {
                    $companyCityName = ucwords($clientLocation->city ?? '');
                }

                if ($clientLocation->country_id > 0) {
                    $countryName = GeneralMethod::getCountryName($clientLocation->country_id);
                    foreach ($countryName as $cval) {
                        $companyCountryName = ucwords($cval['name']);
                    }
                }
            }

            $clientContacts = ($clientId > 0)
                ? DB::table('client_contact_info')->where('client_id', $clientId)->orderBy('id', 'ASC')->get()
                : collect([]);

            // 5b. Authoritative Company KPI Metrics (CQRS via Elasticsearch with DB fallback)
            $kpiTotalJobs = 0;
            $kpiActiveJobs = $activeJobs;
            $kpiCandidatesSubmitted = 0;
            $kpiInterviews = 0;
            $kpiOffers = 0;
            $kpiJoined = 0;

            try {
                $elasticService = app(\App\Services\ElasticService::class);
                if ($elasticService->indexExists(ELASTIC_JOBS)) {
                    $jobAggs = $elasticService->getClient()->search([
                        'index' => ELASTIC_JOBS,
                        'request_cache' => true,
                        'body' => [
                            'size' => 0,
                            'query' => [
                                'term' => ['comId' => (int) $companyId],
                            ],
                            'aggs' => [
                                'total_jobs' => ['value_count' => ['field' => 'id']],
                                'active_jobs' => ['filter' => ['term' => ['status' => 'active']]],
                            ],
                        ],
                    ]);
                    $kpiTotalJobs = (int) ($jobAggs['aggregations']['total_jobs']['value'] ?? 0);
                    $kpiActiveJobs = (int) ($jobAggs['aggregations']['active_jobs']['doc_count'] ?? $activeJobs);
                } else {
                    $kpiTotalJobs = DB::table('jobs')->where('company_id', $companyId)->count();
                }

                // Authoritative Candidates, Offers & Joinings from ELASTIC_CANDIDATES
                if ($elasticService->indexExists(ELASTIC_CANDIDATES)) {
                    $candParams = [
                        'index' => ELASTIC_CANDIDATES,
                        'request_cache' => true,
                        'body' => [
                            'size' => 0,
                            'query' => [
                                'bool' => [
                                    'should' => [
                                        ['term' => ['company_id' => (int) $companyId]],
                                        ['term' => ['ClientComID' => (int) $companyId]],
                                        ['term' => ['client_comapny_id' => (int) $companyId]],
                                        ['term' => ['agency_company_id' => (int) $companyId]],
                                        ['term' => ['from_company_id' => (int) $companyId]],
                                        ['term' => ['to_company_id' => (int) $companyId]],
                                    ],
                                    'minimum_should_match' => 1,
                                ],
                            ],
                            'aggs' => [
                                'offers' => [
                                    'filter' => [
                                        'bool' => [
                                            'should' => [
                                                ['term' => ['job_master_status_id' => 5]],
                                                ['term' => ['master_status_id' => 5]],
                                                ['term' => ['of_offer_release' => 1]],
                                            ],
                                            'minimum_should_match' => 1,
                                        ],
                                    ],
                                ],
                                'joined' => [
                                    'filter' => [
                                        'bool' => [
                                            'should' => [
                                                ['term' => ['job_master_status_id' => 6]],
                                                ['term' => ['master_status_id' => 6]],
                                                ['term' => ['of_joined' => 1]],
                                            ],
                                            'minimum_should_match' => 1,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ];
                    $candRes = $elasticService->getClient()->search($candParams);
                    $kpiCandidatesSubmitted = (int) ($candRes['hits']['total']['value'] ?? 0);
                    $kpiOffers = (int) ($candRes['aggregations']['offers']['doc_count'] ?? 0);
                    $kpiJoined = (int) ($candRes['aggregations']['joined']['doc_count'] ?? 0);
                }

                // Authoritative Interviews from ELASTIC_INERTVIEW_TRACKER
                if ($elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
                    $intParams = [
                        'index' => ELASTIC_INERTVIEW_TRACKER,
                        'request_cache' => true,
                        'body' => [
                            'size' => 0,
                            'query' => [
                                'bool' => [
                                    'should' => [
                                        ['term' => ['company_id' => (int) $companyId]],
                                        ['term' => ['job_company_id' => (int) $companyId]],
                                        ['term' => ['ClientComID' => (int) $companyId]],
                                    ],
                                    'minimum_should_match' => 1,
                                ],
                            ],
                        ],
                    ];
                    $intRes = $elasticService->getClient()->search($intParams);
                    $kpiInterviews = (int) ($intRes['hits']['total']['value'] ?? 0);
                }
            } catch (\Throwable $e) {
                // Non-blocking fallback to MySQL
            }

            // Database Reconciliation Safety Net: Ensure real DB counts are never obscured
            $dbCandCount = DB::table('job_applications')->where('company_id', $companyId)->count();
            $dbIntCount = DB::table('interview_schedules')->where('company_id', $companyId)->count();
            $dbOfferCount = DB::table('offer_job_applications')->where('company_id', $companyId)->count();
            $dbJoinCount = DB::table('offer_job_applications')->where('company_id', $companyId)->where('joined', 1)->count();

            if ($kpiCandidatesSubmitted < $dbCandCount) {
                $kpiCandidatesSubmitted = $dbCandCount;
            }
            if ($kpiInterviews < $dbIntCount) {
                $kpiInterviews = $dbIntCount;
            }
            if ($kpiOffers < $dbOfferCount) {
                $kpiOffers = $dbOfferCount;
            }
            if ($kpiJoined < $dbJoinCount) {
                $kpiJoined = $dbJoinCount;
            }

            // 5c. Recent Company Activities (latest 5 active/updated jobs for company)
            $recentActivities = DB::table('jobs')
                ->where('company_id', $companyId)
                ->orderByDesc('id')
                ->limit(5)
                ->get()
                ->map(function ($job) {
                    $isActive = ($job->status === 'active');

                    return (object) [
                        'title' => $job->title,
                        'subtitle' => ($job->total_positions ?? 1).' '.(($job->total_positions ?? 1) == 1 ? 'Opening' : 'Openings'),
                        'badge_text' => $isActive ? 'Active Requisition' : ucwords($job->status ?? 'Draft'),
                        'badge_class' => $isActive ? 'cd-tl-badge-green' : 'cd-tl-badge-amber',
                        'icon' => 'fas fa-briefcase',
                        'dot_class' => $isActive ? 'cd-tl-dot-green' : 'cd-tl-dot-purple',
                        'time_ago' => ! empty($job->created_at) ? date('M d, Y', strtotime($job->created_at)) : 'Recently',
                        'link' => url('service_company/jobs/'.$job->id),
                    ];
                });

            // 6. Candidate Status Pipeline
            $candidateStatusesGrouped = DB::table('candidate_status')
                ->select('*')
                ->where('candidate_status.company_id', '=', $companyId)
                ->whereIn('candidate_status.parent', [1, 2, 3, 4, 5, 6, 7])
                ->orderBy('candidate_status.parent', 'ASC')
                ->orderBy('candidate_status.priority', 'ASC')
                ->get()
                ->groupBy('parent');

            $interviewStageSourcing = isset($candidateStatusesGrouped[1]) ? $candidateStatusesGrouped[1]->toArray() : [];
            $interviewStageScreening = isset($candidateStatusesGrouped[2]) ? $candidateStatusesGrouped[2]->toArray() : [];
            $interviewStageEvaluation = isset($candidateStatusesGrouped[3]) ? $candidateStatusesGrouped[3]->toArray() : [];
            $interviewStageNegotiation = isset($candidateStatusesGrouped[4]) ? $candidateStatusesGrouped[4]->toArray() : [];
            $interviewStageOffered = isset($candidateStatusesGrouped[5]) ? $candidateStatusesGrouped[5]->toArray() : [];
            $interviewStageJoined = isset($candidateStatusesGrouped[6]) ? $candidateStatusesGrouped[6]->toArray() : [];
            $interviewStageReject = isset($candidateStatusesGrouped[7]) ? $candidateStatusesGrouped[7]->toArray() : [];

            // 7. Departments & Hiring Locations
            $departmentArr = DB::table('job_categories')->select('*')->where('company_id', $companyId)->orderBy('job_categories.name', 'ASC')->get();
            $countriesforhirring = DB::table('job_locations')->where('company_id', $companyId)->pluck('country_id')->toArray();

            // 8. Commercials
            $commCountries = DB::table('commericial_countries')
                ->select('commtype', DB::raw('COUNT(*) as total'))
                ->where('company_id', $companyId)
                ->whereIn('commtype', [1, 2])
                ->groupBy('commtype')
                ->pluck('total', 'commtype');

            $fullTimeCount = $commCountries->get(2, 0);
            $contractCount = $commCountries->get(1, 0);

            $commCountryNames = DB::table('commericial_countries')
                ->join('countries', 'countries.id', '=', 'commericial_countries.country_id')
                ->where('commericial_countries.company_id', $companyId)
                ->whereIn('commericial_countries.commtype', [1, 2])
                ->select('commericial_countries.commtype', 'countries.country_name', 'countries.country_code')
                ->get()
                ->groupBy('commtype');

            $fullTimeCountries = $commCountryNames->get(2, collect())->pluck('country_name')->toArray();
            $contractCountries = $commCountryNames->get(1, collect())->pluck('country_name')->toArray();

            // Auto-select Contract Hiring Services countries based on company registrations if not configured yet
            if (empty($contractCountries)) {
                $registeredCountryIds = \App\Services\CommercialReadModelService::getCompanyRegisteredCountryIds($companyId);
                if (! empty($registeredCountryIds)) {
                    $countryMap = \App\Services\CommercialReadModelService::getCountryIdToNameMap();
                    $contractCountries = array_values(array_filter(array_map(fn ($cid) => $countryMap[$cid] ?? null, $registeredCountryIds)));
                    $contractCount = count($contractCountries);
                }
            }

            if (empty($fullTimeCountries)) {
                $registeredCountryIds = \App\Services\CommercialReadModelService::getCompanyRegisteredCountryIds($companyId);
                if (! empty($registeredCountryIds)) {
                    $countryMap = \App\Services\CommercialReadModelService::getCountryIdToNameMap();
                    $fullTimeCountries = array_values(array_filter(array_map(fn ($cid) => $countryMap[$cid] ?? null, $registeredCountryIds)));
                    $fullTimeCount = count($fullTimeCountries);
                }
            }

            $commericials = DB::table('commericials')
                ->where('company_id', $companyId)
                ->whereIn('commtype', [1, 2])
                ->get()
                ->keyBy('commtype');

            $fullTimeStatus = $commericials->get(2);
            $contractStatus = $commericials->get(1);

            // 9. Offer Points
            $allCompanyOfferPoints = DB::table('offer_point_companies')
                ->select('offer_point_companies.*')
                ->where('offer_point_companies.company_id', '=', $companyId)
                ->orderByDesc('offer_point_companies.sorting')
                ->get();

            $offerPoints = $allCompanyOfferPoints->where('section_type', 0)->where('section_divided', 0)->values();
            $offerPointOther = $allCompanyOfferPoints->where('section_type', 1)->where('section_divided', 0)->values();
            $offerPointHourly = $allCompanyOfferPoints->where('section_type', 0)->where('section_divided', 1)->values();
            $offerPointOtherHourly = $allCompanyOfferPoints->where('section_type', 1)->where('section_divided', 1)->values();

            if ($offerPoints->isEmpty()) {
                $offerPoints = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=', 0)->where('offer_points.section_type', '=', 0)->orderByDesc('sorting')->get();
            }
            if ($offerPointOther->isEmpty()) {
                $offerPointOther = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=', 0)->where('offer_points.section_type', '=', 1)->orderByDesc('sorting')->get();
            }
            if ($offerPointHourly->isEmpty()) {
                $offerPointHourly = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=', 1)->where('offer_points.section_type', '=', 0)->orderByDesc('sorting')->get();
            }
            if ($offerPointOtherHourly->isEmpty()) {
                $offerPointOtherHourly = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=', 1)->where('offer_points.section_type', '=', 1)->orderByDesc('sorting')->get();
            }

            $typesection = $allCompanyOfferPoints->sortByDesc('id')->first();
            $sectionOther = '';
            $sectionHourly = 'd-none';
            $textShow = '';

            if ($typesection) {
                if ($typesection->section_divided == 1) {
                    $sectionOther = 'd-none';
                    $sectionHourly = '';
                } else {
                    $sectionOther = '';
                    $sectionHourly = 'd-none';
                }
                if ($typesection->show_default == 2 || $typesection->show_default == 4) {
                    $textShow = 'Hourly';
                } elseif ($typesection->show_default == 1) {
                    $textShow = 'Monthly';
                } elseif ($typesection->show_default == 3) {
                    $textShow = 'Annual';
                } elseif ($typesection->show_default == 0) {
                    $textShow = 'Monthly';
                }
            }

            // 10. Global Settings
            $globalSettings = DB::table('global_settings')->orderBy('id', 'asc')->first();

            // 11. Coins
            $companyCoins = DB::table('company_coins')
                ->where('company_id', $companyId)
                ->whereIn('event_type', ['operational', 'sales'])
                ->get()
                ->keyBy('event_type');

            $operationCoins = $companyCoins->get('operational');
            $salesCoins = $companyCoins->get('sales');

            // 12. Agencies & Admins
            $agenciesEmpaneled = DB::table('users')->where('company_id', $companyId)->where('type', 1)->count();
            $agenciesActive = DB::table('users')->where('company_id', $companyId)->where('type', 1)->where('status', 'active')->count();

            $administrators = DB::table('users')
                ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
                ->where('users.company_id', $companyId)
                ->where('users.type', 0)
                ->where('users.status', 'active')
                ->select('users.id', 'users.name', 'users.email', 'users.mobile', 'users.image', 'users.root_user', 'users_profile.designation')
                ->orderByDesc('users.root_user')
                ->orderBy('users.id')
                ->limit(4)
                ->get();

            $agencyCommercialTerms = $this->getAgencyCommercialTerms($companyId);

            return [
                'boostkeywords' => $boostkeywords,
                'account' => $account,
                'client' => $client,
                'yearAgo' => $yearAgo,
                'joined' => $joined,
                'industries_name' => $industriesName,
                'tags' => $tags,
                'activeJobs' => $activeJobs,
                'team' => $team,
                'team_count' => $teamCount,
                'client_location' => $clientLocation,
                'client_location_count' => $clientLocationCount,
                'allLocations' => $allLocations,
                'clientContacts' => $clientContacts,
                'kpiTotalJobs' => $kpiTotalJobs,
                'kpiActiveJobs' => $kpiActiveJobs,
                'kpiCandidatesSubmitted' => $kpiCandidatesSubmitted,
                'kpiInterviews' => $kpiInterviews,
                'kpiOffers' => $kpiOffers,
                'kpiJoined' => $kpiJoined,
                'recentActivities' => $recentActivities,
                'company_city_name' => $companyCityName,
                'company_country_name' => $companyCountryName,
                'interviewStage_sourcing' => $interviewStageSourcing,
                'interviewStage_screening' => $interviewStageScreening,
                'interviewStage_evaluation' => $interviewStageEvaluation,
                'interviewStage_negotiation' => $interviewStageNegotiation,
                'interviewStage_offered' => $interviewStageOffered,
                'interviewStage_joined' => $interviewStageJoined,
                'interviewStage_reject' => $interviewStageReject,
                'departmentArr' => $departmentArr,
                'countriesforhirring' => $countriesforhirring,
                'fullTimeCount' => $fullTimeCount,
                'contractCount' => $contractCount,
                'fullTimeStatus' => $fullTimeStatus,
                'contractStatus' => $contractStatus,
                'fullTimeCountries' => $fullTimeCountries,
                'contractCountries' => $contractCountries,
                'offer_points' => $offerPoints,
                'offer_point_other' => $offerPointOther,
                'offer_point_hourly' => $offerPointHourly,
                'offer_point_other_hourly' => $offerPointOtherHourly,
                'section_other' => $sectionOther,
                'section_hourly' => $sectionHourly,
                'textShow' => $textShow,
                'global_settings' => $globalSettings,
                'operationCoins' => $operationCoins,
                'salesCoins' => $salesCoins,
                'agenciesEmpaneled' => $agenciesEmpaneled,
                'agenciesActive' => $agenciesActive,
                'administrators' => $administrators,
                'agencyCommercialTerms' => $agencyCommercialTerms,
            ];
        });
    }

    /**
     * Build the company-scoped agency commercial terms payload.
     *
     * Intent: Keep agency-facing commercial terms separate from client Services/Commercials
     * while returning a stable cached object for high-read marketplace pages.
     * Tenancy: Reads only the row keyed by the authenticated service company's company_id.
     */
    public function getAgencyCommercialTerms(int $companyId): object
    {
        return app(AgencyCommercialTermsService::class)->getForCompany($companyId);
    }

    /**
     * Flush tenant account settings cache key.
     */
    public function flushCompanyCache(int $companyId): void
    {
        Cache::forget("account_settings:company:{$companyId}");
    }

    /**
     * Fetch tenant career page settings data wrapped in Redis cache.
     *
     * Tenancy: Strictly filtered by $companyId.
     *
     * Author: Antigravity AI
     * Date: August 17, 2026
     */
    public function getCompanyCareerSettings(int $companyId): array
    {
        $cacheKey = "company_career_settings:{$companyId}";

        return Cache::remember($cacheKey, 86400, function () use ($companyId) {
            $company = DB::table('companies')->where('id', $companyId)->first();
            $global = DB::table('global_settings')->first();
            $careerSettings = DB::table('company_career_settings')->where('company_id', $companyId)->first();

            return [
                'company' => $company,
                'global' => $global,
                'careerSettings' => $careerSettings,
            ];
        });
    }

    /**
     * Flush tenant career settings cache key.
     */
    public function flushCompanyCareerCache(int $companyId): void
    {
        Cache::forget("company_career_settings:{$companyId}");
        Cache::forget("tenant_career_meta:{$companyId}");
    }
}
