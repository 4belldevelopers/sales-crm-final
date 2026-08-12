<?php

namespace App\Repositories;

use App\Helper\GeneralMethod;
use App\Models\Client;
use App\Models\Job;
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
            $client = Client::select('clients.id', 'clients.tags')
                ->where('clients.company_id', '=', $companyId)
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

            // 5. Client Location
            $clientId = @$client->id;
            $clientLocationCount = 0;
            $clientLocation = null;
            if ($clientId) {
                $clientLocations = DB::table('client_location')
                    ->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
                    ->where('client_location.client_id', $clientId)
                    ->orderBy('client_location.id', 'ASC')
                    ->get();
                $clientLocationCount = $clientLocations->count();
                $clientLocation = $clientLocations->first();
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
            ];
        });
    }

    /**
     * Flush tenant account settings cache key.
     */
    public function flushCompanyCache(int $companyId): void
    {
        Cache::forget("account_settings:company:{$companyId}");
    }
}
