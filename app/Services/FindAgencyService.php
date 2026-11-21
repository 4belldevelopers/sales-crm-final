<?php

namespace App\Services;

use App\Domain\AgencyPartnership\Services\AgencyRecipientNormalizer;
use App\Domain\AgencyPartnership\Services\InvitationTokenService;
use App\Helper\GeneralMethod;
use App\Helper\Reply;
use App\Jobs\ProcessAgencyOutboxEventJob;
use App\Models\Company;
use App\Notifications\QueueNotificationMailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class FindAgencyService
 *
 * Intent:
 * High-performance enterprise discovery and invitation engine for Partner Agencies.
 * Implements:
 * 1. Strict CQRS with zero avoidable MySQL reads on search/filter paths.
 * 2. N+1 query elimination via pre-flight batch enrichment (ES & MySQL).
 * 3. L3 Multi-Tenant Redis Cache with versioned invalidation.
 * 4. Transactional Outbox pattern logging to sys_outbox_events.
 * 5. Atomic idempotency protection via sys_idempotency_keys.
 * 6. Decoupled asynchronous Elasticsearch indexing and email notification dispatch.
 *
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class FindAgencyService
{
    public function __construct(
        protected ElasticService $elasticService
    ) {}

    /**
     * Invalidate find-agency cache for a specific tenant company.
     * Tenancy: Scoped to company_id.
     */
    public static function invalidateTenantCache(int $companyId): void
    {
        $versionKey = "ats:v2:findagency:version:{$companyId}";
        $current = (int) Cache::get($versionKey, 1);
        Cache::put($versionKey, $current + 1, 86400 * 30);
        GeneralMethod::cacheClean('allfindagencyversion_global');
    }

    /**
     * Retrieve paginated agency discovery list with batch enrichment and L3 cache.
     * Tenancy: Scoped to current authenticated user's company_id.
     */
    public function getAgencyData(Request $request, $user): JsonResponse|Response
    {
        $company_id = (int) $user->company_id;
        $tab = $request->input('tab', 'all');

        $searchTerms = $request->input('cus_search');
        $custom_search = json_decode($searchTerms, true);

        $searchTermsLocation = $request->input('kt_tagify_location') ?? $request->input('country') ?? $request->input('location') ?? '';
        $custom_search_location = json_decode($searchTermsLocation, true);

        $parent_employment_type = $request->input('app_int_outsource');
        if ($tab === 'contract') {
            $parent_employment_type = '1';
        } elseif ($tab === 'fulltime') {
            $parent_employment_type = '2';
        }
        $commtype = ! empty($parent_employment_type) ? (int) $parent_employment_type : 2;

        $currentJobSkill = ['skill'];
        if (is_array($custom_search) && ! empty($searchTerms)) {
            $currentJobSkill = array_column($custom_search, 'value');
        }

        $skillsList = array_filter($currentJobSkill, fn ($s) => $s !== 'skill');
        $skillsList = array_values(array_map('strtolower', $skillsList));

        // Resolve target job requirement context (100% CQRS via Elasticsearch / Session)
        $jobId = (int) ($request->input('job_id') ?? session()->get('job_id') ?? 0);
        $jobContext = null;
        if ($jobId > 0) {
            $jobContext = Cache::remember("ref:job_context_es:{$jobId}", 1800, function () use ($jobId) {
                try {
                    $indexJobs = defined('ELASTIC_JOBS') ? ELASTIC_JOBS : 'ats_jobs_elastic';
                    $res = $this->elasticService->getClient()->get([
                        'index' => $indexJobs,
                        'id' => (string) $jobId,
                    ]);

                    return $res['_source'] ?? null;
                } catch (\Throwable $e) {
                    return null;
                }
            });
        }

        // If no explicit skills were searched, inherit required skills from active job context
        if (empty($skillsList) && ! empty($jobContext['job_metronics_skills'])) {
            $jobSkills = array_column($jobContext['job_metronics_skills'], 'skill_name');
            $skillsList = array_values(array_unique(array_filter(array_map('strtolower', $jobSkills))));
        }

        $searchQuery = trim($request->input('q', ''));
        $experience = $request->input('experience');
        $industry = $request->input('industry');
        $globalSearch = trim($request->input('search', ''));
        $sortingfilter = $request->input('sortingfilter') ?? $request->input('sort') ?? 'best';

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) ($request->input('per_page') ?? $request->input('size') ?? $request->input('limit') ?? 10);
        $from = ($page - 1) * $size;

        // 1. Resolve country filter with cached lookup
        $country_id_job = null;
        $locTarget = '';
        if (is_array($custom_search_location) && ! empty($searchTermsLocation)) {
            $currentJobLocation = array_column($custom_search_location, 'value');
            $locTarget = $currentJobLocation[0] ?? '';
        } elseif (! empty($searchTermsLocation)) {
            $locTarget = is_string($searchTermsLocation) ? trim($searchTermsLocation) : '';
        }

        if (! empty($locTarget) && strtolower($locTarget) !== 'all locations' && strtolower($locTarget) !== 'all' && strtolower($locTarget) !== 'location') {
            $country_id_job = Cache::remember('ref:country_id_name:'.md5($locTarget), 3600, function () use ($locTarget) {
                $cId = DB::table('countries')->where('country_name', '=', $locTarget)->value('id');

                return $cId ? (int) $cId : 101;
            });
        }

        // Resolve Job Context Geography & Hiring Model Parameters
        $jobCountryId = $country_id_job ?? ($jobContext['client_location'][0]['country_id'] ?? ($jobContext['client_city_jobs'][0]['country_id'] ?? 101));
        $jobHiringType = $commtype ?? ((int) ($jobContext['employment_type'] ?? 2) ?: 2);
        $jobIndustryId = ! empty($industry) ? $industry : ($jobContext['category_id'] ?? null);

        // 2. Check L3 Multi-Tenant Redis Cache first
        $versionKey = "ats:v2:findagency:version:{$company_id}";
        $version = Cache::get($versionKey, 1);

        $cachePayload = [
            'tab' => $tab,
            'page' => $page,
            'size' => $size,
            'job_id' => $jobId,
            'q' => $searchQuery,
            'search' => $globalSearch,
            'company' => $company_id,
            'kt_tagify_location' => $searchTermsLocation,
            'app_int_outsource' => $parent_employment_type,
            'experience' => $experience,
            'industry' => $industry,
            'skillsList' => $skillsList,
            'country_id_job' => $country_id_job,
            'sortingfilter' => $sortingfilter,
        ];

        $cacheKey = "ats:v2:findagency:{$company_id}:{$version}:".GeneralMethod::canonicalHash($cachePayload);
        $forceRefresh = $request->boolean('force_refresh', false);

        $etag = '"v2:fa:'.GeneralMethod::canonicalHash($cacheKey).'"';
        $ifNoneMatch = $request->header('If-None-Match');
        $hasRepresentation = $request->header('X-Has-Representation') == '1';

        $matchesEtag = GeneralMethod::matchesIfNoneMatch($ifNoneMatch, $etag);

        // Fast conditional revalidation when client holds authoritative representation
        if (! $forceRefresh && $matchesEtag && $hasRepresentation) {
            return response(null, 304)
                ->header('ETag', $etag)
                ->header('Cache-Control', 'private, no-cache');
        }

        if (! $forceRefresh && Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey))
                ->header('ETag', $etag)
                ->header('Cache-Control', 'private, no-cache');
        }

        // 3. Step 1: Find companies with matching skills in Elasticsearch
        $companySkillMatchCount = [];
        $companyMatchedSkills = [];

        if (! empty($skillsList)) {
            $skillQueries = [];
            foreach ($skillsList as $skill) {
                $trimmedSkill = trim($skill);
                if (! empty($trimmedSkill)) {
                    $lowerSkill = strtolower($trimmedSkill);
                    $skillQueries[] = [
                        'bool' => [
                            'should' => [
                                ['match_phrase' => ['skill_name' => $lowerSkill]],
                                ['prefix' => ['skill_name' => $lowerSkill]],
                                ['wildcard' => ['skill_name' => '*'.$lowerSkill.'*']],
                            ],
                            'minimum_should_match' => 1,
                        ],
                    ];
                }
            }

            if (! empty($skillQueries)) {
                $superAdminCompanyIds = GeneralMethod::superAdminCompanyIds();
                $skillSearchParams = [
                    'index' => ELASTIC_COMPANY_SKILLS,
                    'body' => [
                        'size' => 10000,
                        '_source' => ['company_id', 'skill_name'],
                        'query' => [
                            'bool' => [
                                'must' => [
                                    [
                                        'bool' => [
                                            'should' => $skillQueries,
                                            'minimum_should_match' => 1,
                                        ],
                                    ],
                                ],
                                'must_not' => array_merge(
                                    [['term' => ['company_id' => $company_id]]],
                                    array_map(fn ($saId) => ['term' => ['company_id' => $saId]], $superAdminCompanyIds)
                                ),
                            ],
                        ],
                    ],
                ];

                try {
                    $skillResponse = $this->elasticService->getClient()->search($skillSearchParams);
                    $hits = $skillResponse['hits']['hits'] ?? [];
                    $skillArrCheck = [];

                    foreach ($hits as $hit) {
                        $source = $hit['_source'];
                        $matchedCompanyId = $source['company_id'];
                        $skillName = strtolower(trim($source['skill_name']));

                        $checkKey = "{$matchedCompanyId}_{$skillName}";
                        if (! in_array($checkKey, $skillArrCheck, true)) {
                            $skillArrCheck[] = $checkKey;

                            if (! isset($companySkillMatchCount[$matchedCompanyId])) {
                                $companySkillMatchCount[$matchedCompanyId] = 0;
                            }
                            $companySkillMatchCount[$matchedCompanyId]++;
                            $companyMatchedSkills[$matchedCompanyId][] = $skillName;
                        }
                    }

                    foreach ($companyMatchedSkills as $cId => $sList) {
                        $companyMatchedSkills[$cId] = array_values(array_unique($sList));
                    }
                } catch (\Throwable $e) {
                    Log::warning('FindAgencyService: Skill search warning: '.$e->getMessage());
                }
            }
        }

        // 4. Step 2: Build dynamic 5-Tier Recommendation Function Score Boosts (Smart Ranking Engine)
        $functions = [];
        $highSkillCompanyIds = [];
        foreach ($companySkillMatchCount as $matchedCompanyId => $matchedSkillCount) {
            // Tier 2: Strong Skill & Technology Match (+1000 per skill, bonus +1500 for 2+ skills)
            $skillWeight = $matchedSkillCount * 1000;
            if ($matchedSkillCount >= 2) {
                $skillWeight += 1500;
                $highSkillCompanyIds[] = (int) $matchedCompanyId;
            }

            $functions[] = [
                'filter' => [
                    'term' => [
                        'id' => $matchedCompanyId,
                    ],
                ],
                'weight' => $skillWeight,
            ];
        }

        // Tier 1: Exact Job Match Boost (+5000 points)
        // High skill match + target location + target commercial model
        if (! empty($highSkillCompanyIds) && ! empty($jobCountryId)) {
            $functions[] = [
                'filter' => [
                    'bool' => [
                        'must' => [
                            ['terms' => ['id' => $highSkillCompanyIds]],
                            ['term' => ['commericial_countries.country_id' => (int) $jobCountryId]],
                            ['term' => ['commericials.commtype' => (int) $jobHiringType]],
                        ],
                    ],
                ],
                'weight' => 5000,
            ];
        }

        // Tier 3: Industry Match (+500 points)
        if (! empty($jobIndustryId)) {
            $functions[] = [
                'filter' => [
                    'terms' => [
                        'industry.ind_id' => (array) $jobIndustryId,
                    ],
                ],
                'weight' => 500,
            ];
        }

        // Tier 4: Location-Based Match (+300 points)
        if (! empty($jobCountryId)) {
            $functions[] = [
                'filter' => [
                    'term' => [
                        'commericial_countries.country_id' => (int) $jobCountryId,
                    ],
                ],
                'weight' => 300,
            ];
        }

        // Tier 5: Similar Hiring Category Agencies (+200 points)
        if (! empty($jobHiringType)) {
            $functions[] = [
                'filter' => [
                    'term' => [
                        'commericials.commtype' => (int) $jobHiringType,
                    ],
                ],
                'weight' => 200,
            ];
        }

        // Bench Strength & Talent Availability (+150 points)
        $functions[] = [
            'filter' => [
                'range' => [
                    'bench_candidates.id' => ['gt' => 0],
                ],
            ],
            'weight' => 150,
        ];

        // 5. Step 3: Filter criteria for experience and industry
        $experienceFilter = [];
        $industryFilter = [];
        $currentYear = (int) date('Y');

        if (! empty($experience)) {
            if ($experience === '0–2 years' || $experience === '0-2 years') {
                $experienceFilter[] = ['range' => ['founded' => ['gte' => $currentYear - 2]]];
            } elseif ($experience === '3–5 years' || $experience === '3-5 years') {
                $experienceFilter[] = ['range' => ['founded' => ['gte' => $currentYear - 5, 'lte' => $currentYear - 3]]];
            } elseif ($experience === '5–10 years' || $experience === '5-10 years') {
                $experienceFilter[] = ['range' => ['founded' => ['gte' => $currentYear - 10, 'lte' => $currentYear - 5]]];
            } elseif ($experience === '10+ years') {
                $experienceFilter[] = ['range' => ['founded' => ['lte' => $currentYear - 10]]];
            }
        }

        if (! empty($industry)) {
            $industryFilter[] = [
                'terms' => [
                    'industry.ind_id' => (array) $industry,
                ],
            ];
        }

        $domesticCountryId = (int) \App\Helper\GeneralMethod::defaultCountryId($company_id);

        $countryFilter = [];
        if (! empty($country_id_job)) {
            $countryFilter[] = ['term' => ['commericial_countries.country_id' => $country_id_job]];
        } elseif ($tab === 'domestic' || $tab === 'india') {
            $countryFilter[] = ['term' => ['commericial_countries.country_id' => $domesticCountryId]];
        } elseif ($tab === 'global') {
            $countryFilter[] = [
                'bool' => [
                    'must_not' => [
                        ['term' => ['commericial_countries.country_id' => $domesticCountryId]],
                    ],
                ],
            ];
        }

        $benchFilter = [];
        if ($tab === 'bench') {
            $benchFilter[] = [
                'bool' => [
                    'should' => [
                        ['term' => ['commericials.commtype' => 1]],
                        ['range' => ['bench_candidates.id' => ['gt' => 0]]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        $commercialFilter = [];
        if (! empty($parent_employment_type) && ($tab === 'contract' || $tab === 'fulltime' || $parent_employment_type == '1' || $parent_employment_type == '2')) {
            $commercialFilter[] = [
                'term' => [
                    'commericials.commtype' => (int) $parent_employment_type,
                ],
            ];
            $commercialFilter[] = [
                'term' => [
                    'commericials.status' => 1,
                ],
            ];
        }

        $textSearchFilter = [];
        $searchVal = ! empty($globalSearch) ? $globalSearch : $searchQuery;
        if (! empty($searchVal)) {
            $searchValLower = strtolower($searchVal);
            $textSearchFilter[] = [
                'bool' => [
                    'should' => [
                        ['match_phrase_prefix' => ['company_name' => $searchVal]],
                        ['wildcard' => ['company_name' => '*'.$searchValLower.'*']],
                        ['match' => ['description' => $searchVal]],
                        ['match' => ['industry.industry_name' => $searchVal]],
                        ['match' => ['job_metronics_skills.skill_name' => $searchVal]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        $sortArray = [];
        if ($sortingfilter === 'recent') {
            $sortArray = [
                ['last_login' => ['order' => 'desc', 'missing' => '_last']],
                ['_score' => ['order' => 'desc']],
            ];
        } elseif ($sortingfilter === 'oldest') {
            $sortArray = [
                ['created_at' => ['order' => 'asc', 'missing' => '_last']],
            ];
        } elseif ($sortingfilter === 'name_asc') {
            $sortArray = [
                ['company_name.keyword' => ['order' => 'asc']],
                ['company_name' => ['order' => 'asc']],
            ];
        } elseif ($sortingfilter === 'name_desc') {
            $sortArray = [
                ['company_name.keyword' => ['order' => 'desc']],
                ['company_name' => ['order' => 'desc']],
            ];
        } else {
            $sortArray = [
                ['_score' => ['order' => 'desc']],
            ];
        }

        $superAdminCompanyIds = GeneralMethod::superAdminCompanyIds();

        $companySearchParams = [
            'index' => ELASTIC_COMPANIES_INDEX,
            'body' => [
                'from' => $from,
                'size' => $size,
                'track_total_hits' => true,
                'query' => [
                    'function_score' => [
                        'query' => [
                            'bool' => [
                                'must' => array_merge(
                                    [
                                        ['term' => ['status' => 'active']],
                                        ['terms' => ['company_type' => [1, 2]]],
                                        [
                                            'nested' => [
                                                'path' => 'company_coins',
                                                'query' => [
                                                    'bool' => [
                                                        'must' => [
                                                            ['term' => ['company_coins.event_type' => 'sales']],
                                                            ['range' => ['company_coins.coins' => ['gt' => 0]]],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    $countryFilter,
                                    $benchFilter,
                                    $commercialFilter,
                                    $experienceFilter,
                                    $industryFilter,
                                    $textSearchFilter
                                ),
                                'must_not' => array_merge(
                                    [['term' => ['id' => $company_id]]],
                                    array_map(fn ($saId) => ['term' => ['id' => $saId]], $superAdminCompanyIds)
                                ),
                            ],
                        ],
                        'functions' => $functions,
                        'score_mode' => 'sum',
                        'boost_mode' => 'sum',
                    ],
                ],
                'sort' => $sortArray,
                'aggs' => [
                    'total' => ['value_count' => ['field' => 'id']],
                    'contract' => [
                        'filter' => ['term' => ['commericials.commtype' => 1]],
                    ],
                    'fulltime' => [
                        'filter' => ['term' => ['commericials.commtype' => 2]],
                    ],
                    'domestic' => [
                        'filter' => ['term' => ['commericial_countries.country_id' => $domesticCountryId]],
                    ],
                    'global' => [
                        'filter' => [
                            'bool' => [
                                'must_not' => [
                                    ['term' => ['commericial_countries.country_id' => $domesticCountryId]],
                                ],
                            ],
                        ],
                    ],
                    'bench' => [
                        'filter' => [
                            'bool' => [
                                'should' => [
                                    ['term' => ['commericials.commtype' => 1]],
                                    ['range' => ['bench_candidates.id' => ['gt' => 0]]],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // 6. Execute Elasticsearch search
        $companyResponse = $this->elasticService->getClient()->search($companySearchParams);
        $total = $companyResponse['hits']['total']['value'] ?? 0;
        $hits = $companyResponse['hits']['hits'] ?? [];
        $isFallback = false;
        $fallbackNotice = '';

        // Fallback Recommendation Engine (Never Leave Listing Empty per Rule 4)
        if ($total === 0 && (! empty($searchVal) || ! empty($industryFilter) || ! empty($experienceFilter) || ! empty($countryFilter))) {
            $fallbackSearchParams = $companySearchParams;
            $fallbackSearchParams['body']['query']['function_score']['query']['bool']['must'] = [
                ['term' => ['status' => 'active']],
                ['terms' => ['company_type' => [1, 2]]],
                [
                    'nested' => [
                        'path' => 'company_coins',
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['company_coins.event_type' => 'sales']],
                                    ['range' => ['company_coins.coins' => ['gt' => 0]]],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
            if (! empty($countryFilter)) {
                $fallbackSearchParams['body']['query']['function_score']['query']['bool']['should'] = $countryFilter;
            }

            try {
                $fallbackResponse = $this->elasticService->getClient()->search($fallbackSearchParams);
                $fallbackTotal = $fallbackResponse['hits']['total']['value'] ?? 0;
                if ($fallbackTotal > 0) {
                    $companyResponse = $fallbackResponse;
                    $total = $fallbackTotal;
                    $hits = $fallbackResponse['hits']['hits'] ?? [];
                    $isFallback = true;
                    $fallbackNotice = 'No exact matches found for your filter. Showing recommended partner agencies based on location, industry, and hiring specialization.';
                }
            } catch (\Throwable $e) {
                Log::warning('FindAgencyService: Fallback search warning: '.$e->getMessage());
            }
        }

        // 7. Extract agency IDs for Batch Pre-enrichment (N+1 Elimination)
        $agencyIds = [];
        foreach ($hits as $hit) {
            if (isset($hit['_source']['id'])) {
                $agencyIds[] = (int) $hit['_source']['id'];
            }
        }
        $agencyIds = array_values(array_unique(array_filter($agencyIds)));

        // Pre-flight batch data retrieval with Dual Commercials (Full-Time + Contract)
        $batchClientsMap = ! empty($agencyIds) ? GeneralMethod::batchGetClientIdCompany($agencyIds) : [];
        $batchRelationsMap = ! empty($agencyIds) ? GeneralMethod::batchGetAgencyRelationData($company_id, $agencyIds) : [];
        $batchMatchFoundMap = ! empty($agencyIds) ? GeneralMethod::batchGetMatchFoundMap($company_id, $agencyIds) : [];
        $batchJobsCountMap = ! empty($agencyIds) ? GeneralMethod::batchAgencyJobsCount($agencyIds) : [];
        $batchCandidatesCountMap = ! empty($agencyIds) ? GeneralMethod::batchAgencyJobsCandidatesCount($agencyIds) : [];
        $batchCommercialsContractMap = ! empty($agencyIds) ? GeneralMethod::batchGetCommercialsData($agencyIds, 1) : [];
        $batchCommercialsFulltimeMap = ! empty($agencyIds) ? GeneralMethod::batchGetCommercialsData($agencyIds, 2) : [];

        // Batch superadmin verification status and terms from companies table
        $batchAccountVerify = [];
        $batchCompanyTerms = [];
        if (! empty($agencyIds)) {
            $compRows = DB::table('companies')
                ->whereIn('id', $agencyIds)
                ->select('id', 'accountVerify', 'terms', 'agency_offer_terms', 'company_size_from', 'company_size')
                ->get();
            foreach ($compRows as $cr) {
                $batchAccountVerify[$cr->id] = (int) $cr->accountVerify;
                $batchCompanyTerms[$cr->id] = $cr;
            }
        }

        // Batch serving countries from commericial_countries
        $batchServingCountries = [];
        if (! empty($agencyIds)) {
            $scRows = DB::table('commericial_countries')
                ->join('countries', 'countries.id', '=', 'commericial_countries.country_id')
                ->whereIn('commericial_countries.company_id', $agencyIds)
                ->select('commericial_countries.company_id', 'countries.country_name')
                ->distinct()
                ->get();
            foreach ($scRows as $scr) {
                $batchServingCountries[$scr->company_id][] = $scr->country_name;
            }
        }

        // Batch recruiter user counts
        $batchUserCounts = [];
        if (! empty($agencyIds)) {
            $batchUserCounts = DB::table('users')
                ->whereIn('company_id', $agencyIds)
                ->groupBy('company_id')
                ->pluck(DB::raw('count(*) as cnt'), 'company_id')
                ->toArray();
        }

        // 8. Assemble and enrich agency data items
        $data = [];
        foreach ($hits as $hit) {
            $source = $hit['_source'];
            $matchedCompanyId = (int) $source['id'];

            $matchedSkills = $companyMatchedSkills[$matchedCompanyId] ?? [];
            $commonSkills = array_values(array_intersect(
                array_map('strtolower', $skillsList),
                array_map('strtolower', $matchedSkills)
            ));

            $source['matched_skills'] = $commonSkills;
            $source['search_skills'] = $skillsList;
            $source['match_score'] = $hit['_score'];
            $source['matched_skill_count'] = count($commonSkills);

            // Recommendation Tier Classification (User Specification Priority Hierarchy)
            $matchedSkillCount = count($commonSkills);
            $hasLocationMatch = ! empty($jobCountryId) && in_array((int) $jobCountryId, array_column($source['commericial_countries'] ?? [], 'country_id'));
            $hasCommMatch = in_array((int) $jobHiringType, array_column($source['commericials'] ?? [], 'commtype'));
            $benchCount = count($source['bench_candidates'] ?? []);

            $recTier = 'marketplace';
            $isDirectMatch = false;
            $recLabel = '';
            $recReason = '';
            $matchPct = 0;

            if ($matchedSkillCount >= 2 && $hasLocationMatch && $hasCommMatch) {
                // Tier 1: Exact Job Match Agencies
                $recTier = 'exact';
                $isDirectMatch = true;
                $recLabel = 'Exact Job Match';
                $recReason = 'Matches skills ('.implode(', ', array_slice($commonSkills, 0, 3)).'), location & '.($jobHiringType == 1 ? 'Contract' : 'Full-Time').' hiring';
                $matchPct = 95;
            } elseif ($matchedSkillCount >= 2) {
                // Tier 2: Strong Skill & Technology Match
                $recTier = 'strong_skill';
                $isDirectMatch = true;
                $recLabel = 'Strong Skill Match';
                $recReason = 'Strong technology match in '.implode(', ', array_slice($commonSkills, 0, 3));
                $matchPct = min(90, max(75, round(($matchedSkillCount / max(1, count($skillsList))) * 100)));
            } elseif ($matchedSkillCount === 1) {
                // Tier 2: Skill Match
                $recTier = 'skill';
                $isDirectMatch = true;
                $recLabel = 'Skill Match';
                $recReason = 'Skill overlap in '.implode(', ', array_slice($commonSkills, 0, 2));
                $matchPct = min(74, max(50, round(($matchedSkillCount / max(1, count($skillsList))) * 100)));
            } else {
                $recTier = 'marketplace';
                $isDirectMatch = false;
                $recLabel = '';
                $recReason = '';
                $matchPct = 0;
            }

            $source['recommendation_tier'] = $recTier;
            $source['is_direct_match'] = $isDirectMatch;
            $source['recommendation_label'] = $recLabel;
            $source['recommendation_reason'] = $recReason;
            $source['match_pct'] = $matchPct;

            // Attach enriched batch metrics to avoid Blade template N+1 queries
            $clientObj = $batchClientsMap[$matchedCompanyId] ?? null;
            $source['_client_info'] = $clientObj;
            $source['_relation_info'] = $batchRelationsMap[$matchedCompanyId] ?? null;
            $source['_match_found'] = $batchMatchFoundMap[$matchedCompanyId] ?? 0;
            $source['_user_size_count'] = $batchUserCounts[$matchedCompanyId] ?? 0;
            $source['_agency_jobs_count'] = $batchJobsCountMap[$matchedCompanyId] ?? (object) ['all_jobs' => 0];
            $source['_agency_candidates_count'] = $batchCandidatesCountMap[$matchedCompanyId] ?? (object) ['all_candidates' => 0, 'all_candidate_bench' => 0];

            $commContract = $batchCommercialsContractMap[$matchedCompanyId] ?? null;
            $commFulltime = $batchCommercialsFulltimeMap[$matchedCompanyId] ?? null;

            $source['_commercials_contract'] = $commContract;
            $source['_commercials_fulltime'] = $commFulltime;
            $source['_minimum_rate_contract'] = $commContract ? $commContract->minimum_rate : null;
            $source['_minimum_rate_contract_type'] = $commContract ? $commContract->minimum_rate_type : null;
            $source['_minimum_rate_fulltime'] = $commFulltime ? $commFulltime->minimum_rate : null;
            $source['_minimum_rate_fulltime_type'] = $commFulltime ? $commFulltime->minimum_rate_type : null;

            $source['_account_verify'] = $batchAccountVerify[$matchedCompanyId] ?? 0;
            $source['_serving_countries'] = array_values(array_unique($batchServingCountries[$matchedCompanyId] ?? []));
            $source['_company_terms'] = $batchCompanyTerms[$matchedCompanyId] ?? null;
            $source['_company_size_from'] = $batchCompanyTerms[$matchedCompanyId]->company_size_from ?? null;
            $source['_company_size'] = $batchCompanyTerms[$matchedCompanyId]->company_size ?? null;

            $data[] = $source;
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'per_page' => $size,
            'last_page' => (int) ceil($total / max(1, $size)),
            'listType' => 'allcandidate',
        ];

        $userPermissions = $user->can_manage ?? [];
        $app_int_outsource = $parent_employment_type;
        $hiringtype = $app_int_outsource;
        $jobid = '';

        $domesticDocCount = (int) ($companyResponse['aggregations']['domestic']['doc_count'] ?? ($companyResponse['aggregations']['indian']['doc_count'] ?? 0));

        $domesticCountryIso = strtolower(trim(GeneralMethod::countryCodeFun($company_id)));
        $domesticCountryAdjective = 'Domestic';
        if ($domesticCountryId === 101 || $domesticCountryIso === 'in') {
            $domesticCountryAdjective = 'Indian';
        } elseif ($domesticCountryId === 233 || $domesticCountryIso === 'us') {
            $domesticCountryAdjective = 'US';
        } else {
            $cRow = DB::table('countries')->where('id', $domesticCountryId)->first();
            if ($cRow) {
                $domesticCountryAdjective = ! empty($cRow->country_code) ? strtoupper($cRow->country_code) : $cRow->country_name;
            }
        }
        $domesticSubtext = $domesticDocCount . ' ' . $domesticCountryAdjective . ($domesticDocCount === 1 ? ' agency' : ' agencies');

        $kpiCounts = [
            'total_agencies' => $companyResponse['aggregations']['total']['value'] ?? $total,
            'contract_staffing' => $companyResponse['aggregations']['contract']['doc_count'] ?? 0,
            'fulltime_hiring' => $companyResponse['aggregations']['fulltime']['doc_count'] ?? 0,
            'domestic_agencies' => $domesticDocCount,
            'indian_agencies' => $domesticDocCount,
            'domestic_subtext' => $domesticSubtext,
            'domestic_country_adjective' => $domesticCountryAdjective,
            'global_agencies' => $companyResponse['aggregations']['global']['doc_count'] ?? 0,
            'bench_agencies' => $companyResponse['aggregations']['bench']['doc_count'] ?? 0,
        ];

        $responseData = [
            'html' => view('service_company.ajax_pages.find_agency_ajax', compact(
                'data', 'userPermissions', 'app_int_outsource', 'jobid', 'currentJobSkill',
                'hiringtype', 'parent_employment_type', 'company_id', 'isFallback', 'fallbackNotice'
            ))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
            'version' => $version,
            'kpi_counts' => $kpiCounts,
            'is_fallback' => $isFallback,
            'fallback_notice' => $fallbackNotice,
        ];

        // Store in Redis cache for 30 minutes
        Cache::put($cacheKey, $responseData, now()->addMinutes(30));

        return response()->json($responseData)
            ->header('ETag', $etag)
            ->header('Cache-Control', 'private, no-cache');
    }

    /**
     * Authoritative mutation workflow for agency invitation requests.
     * Guaranteed ACID transactions, atomic idempotency, and transactional outbox publication.
     *
     * @param  int|string  $id  Target agency company ID
     * @param  object  $user  Authenticated user instance
     */
    public function handleAgencyRequest(Request $request, $id, $user): JsonResponse
    {
        $from_company_id = (int) $user->company_id;
        $from_user_id = (int) $user->id;
        $to_company_id = (int) $id;
        $event_id = (int) ($request->input('event_id') ?? $request->get('event_id', 1));

        // 1. Resolve or generate Idempotency Key
        $idempotencyKey = $request->header('X-Idempotency-Key')
            ?? $request->input('idempotency_key')
            ?? "agency_req_{$from_company_id}_{$to_company_id}_{$event_id}";

        $requestFingerprint = hash('sha256', json_encode([
            'from_company_id' => $from_company_id,
            'to_company_id' => $to_company_id,
            'event_id' => $event_id,
            'jobid' => $request->input('current_jobid', ''),
        ]));

        // 2. Atomic Idempotency Claim via sys_idempotency_keys
        $claimed = DB::table('sys_idempotency_keys')->insertOrIgnore([
            'tenant_id' => $from_company_id,
            'idempotency_key' => $idempotencyKey,
            'request_hash' => $requestFingerprint,
            'status' => 'PROCESSING',
            'expires_at' => now()->addHours(24),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (! $claimed) {
            $existing = DB::table('sys_idempotency_keys')
                ->where('tenant_id', $from_company_id)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                if ($existing->status === 'COMPLETED') {
                    $body = json_decode($existing->response_body, true);

                    return response()->json($body ?? Reply::dataOnly(['status' => 'success']));
                }
                if ($existing->status === 'PROCESSING') {
                    return response()->json(['status' => 'waiting', 'message' => 'Request is currently being processed.'], 425);
                }
            }
        }

        try {
            $outboxId = null;
            $mutationResponse = null;

            // 3. Execute authoritative business mutation inside ACID transaction
            DB::transaction(function () use (
                $from_company_id, $from_user_id, $to_company_id, $event_id,
                $request, $idempotencyKey, &$outboxId, &$mutationResponse
            ) {
                if ($event_id === -1) {
                    // Cancel Invitation workflow (Update to Cancelled -2, preserving historical audit record)
                    DB::table('staffing_company_invitation')
                        ->where('from_company_id', $from_company_id)
                        ->where('to_company_id', $to_company_id)
                        ->where('to_status', 0)
                        ->where('to_invitation_status', 0)
                        ->update([
                            'to_invitation_status' => -2,
                            'updated_at' => now(),
                        ]);

                    // Insert Outbox Event for durable async processing
                    $outboxId = DB::table('sys_outbox_events')->insertGetId([
                        'tenant_id' => $from_company_id,
                        'idempotency_key' => $idempotencyKey,
                        'aggregate_type' => 'AgencyRequest',
                        'aggregate_id' => $to_company_id,
                        'aggregate_version' => 1,
                        'event_type' => 'AGENCY_REQUEST_CANCELLED',
                        'payload' => json_encode([
                            'from_company_id' => $from_company_id,
                            'to_company_id' => $to_company_id,
                            'invitation_id' => 0,
                            'action' => 'cancelled',
                        ]),
                        'status' => 'PENDING',
                        'attempts' => 0,
                        'available_at' => now(),
                        'created_at' => now(),
                    ]);

                    $mutationResponse = ['status' => 'success', 'clientDataCnt' => ''];
                } else {
                    // Send Invitation workflow
                    if ((int) $request->input('boostercom') === 1) {
                        $boostIdArr = explode(',', (string) $request->input('boostids'));
                        $boostId = $boostIdArr[0] ?? null;
                        if ($boostId) {
                            DB::table('boostkeywords')->where('id', $boostId)->increment('clickCount');
                        }
                    }

                    $clientRow = DB::table('clients')->select('id')->where('company_id', $from_company_id)->first();
                    $company_row = DB::table('companies')->select('company_email', 'id')->where('id', $to_company_id)->first();

                    $existingInv = DB::table('staffing_company_invitation')
                        ->where('from_company_id', $from_company_id)
                        ->where('to_company_id', $to_company_id)
                        ->whereIn('to_invitation_status', [0, 1])
                        ->orderBy('id', 'desc')
                        ->first();

                    $needsNewInvitation = true;
                    $invitationId = 0;

                    if ($existingInv) {
                        if ((int) $existingInv->to_invitation_status === 1) {
                            $needsNewInvitation = false;
                            $invitationId = $existingInv->id;
                        } elseif ((int) $existingInv->to_invitation_status === 0) {
                            $isPast = false;
                            if (! empty($existingInv->expires_at)) {
                                $isPast = \Carbon\Carbon::parse($existingInv->expires_at)->isPast();
                            } elseif (! empty($existingInv->created_at)) {
                                $isPast = \Carbon\Carbon::parse($existingInv->created_at)->addDays(7)->isPast();
                            }

                            if ($isPast) {
                                // JIT Lazy Expiry (Rule 22): Lazily transition expired invitation to -2, releasing unique slot constraint
                                DB::table('staffing_company_invitation')
                                    ->where('id', $existingInv->id)
                                    ->update([
                                        'to_invitation_status' => -2,
                                        'updated_at' => now(),
                                    ]);
                                $needsNewInvitation = true;
                            } else {
                                $needsNewInvitation = false;
                                $invitationId = $existingInv->id;
                            }
                        }
                    }

                    if ($needsNewInvitation) {
                        $targetEmail = $company_row ? (string) $company_row->company_email : '';
                        $normalizedRecipient = AgencyRecipientNormalizer::normalize($targetEmail);
                        $tokenService = app(InvitationTokenService::class);
                        $tokenData = $tokenService->generate();

                        $insertArr = [
                            'from_company_id' => $from_company_id,
                            'from_client_id' => $clientRow ? $clientRow->id : null,
                            'from_user_id' => $from_user_id,
                            'from_status' => 1,
                            'from_created_at' => now(),
                            'to_company_id' => $to_company_id,
                            'to_email' => $targetEmail,
                            'to_status' => 0,
                            'to_invitation_status' => 0,
                            'token_hash' => $tokenData['token_hash'],
                            'expires_at' => now()->addDays(7),
                            'delivery_status' => 'PENDING',
                            'normalized_recipient' => $normalizedRecipient,
                        ];

                        try {
                            $invitationId = (int) DB::table('staffing_company_invitation')->insertGetId($insertArr);
                        } catch (\Illuminate\Database\QueryException $qe) {
                            if ($qe->getCode() === '23000' && str_contains($qe->getMessage(), '1062')) {
                                $invitationId = (int) DB::table('staffing_company_invitation')
                                    ->where('from_company_id', $from_company_id)
                                    ->where('to_company_id', $to_company_id)
                                    ->where('to_invitation_status', 0)
                                    ->value('id');
                            } else {
                                throw $qe;
                            }
                        }

                        // Queue email notification with scanner-safe link
                        if (! empty($targetEmail)) {
                            try {
                                $employerCompany = Company::withoutGlobalScopes()->find($from_company_id);
                                $reqdate = date('d M Y');
                                $url = $tokenData['url'];
                                $checkMailPermision = GeneralMethod::mailPermission(40);

                                if ($checkMailPermision && $employerCompany) {
                                    $mailTypeSubject = GeneralMethod::mailTypeSubject(40, 1);
                                    $subject = str_replace(
                                        ['[COMPANY_NAME]', '[REQUEST_DATE]'],
                                        [$employerCompany->company_name, $reqdate],
                                        $mailTypeSubject->subject ?? "Invitation to Become a Staffing Partner | {$employerCompany->company_name} | {$reqdate}"
                                    );
                                    $template_description = $mailTypeSubject->body_description ?? '';
                                    $acceptInvite = '<a href="'.$url.'" rel="noopener" style="text-decoration:none;display:inline-block;text-align:center;padding:0.75575rem 1.3rem;font-size:0.925rem;line-height:1.5;border-radius:0.35rem;color:#ffffff;background-color:#009EF7;font-weight:600;">Accept Invite</a>';
                                    $urlLink = '<a href="'.$url.'" rel="noopener" style="text-decoration:none;color: #009EF7">'.$url.'</a>';
                                    $template_description = str_replace(
                                        ['[COMPANY_NAME]', '[ACCEPT_INVITE]', '[URL_LINK]'],
                                        [$employerCompany->company_name, $acceptInvite, $urlLink],
                                        $template_description
                                    );

                                    $basecompany = GeneralMethod::baseCompany();
                                    $senderName = $basecompany->companyName ?? '';
                                    $fromAddress = $basecompany->companyEmail ?? '';

                                    QueueNotificationMailService::dispatch(
                                        $targetEmail,
                                        $subject,
                                        'email.common.index',
                                        (object) ['message' => $template_description],
                                        [],
                                        $fromAddress,
                                        $senderName,
                                        [],
                                        $company_row->id
                                    )->onConnection('emails');
                                }
                            } catch (\Throwable $e) {
                                Log::warning('FindAgencyService: Email dispatch error: '.$e->getMessage());
                            }
                        }
                    } else {
                        $existingInv = DB::table('staffing_company_invitation')
                            ->where('from_company_id', $from_company_id)
                            ->where('to_company_id', $to_company_id)
                            ->where('to_invitation_status', 0)
                            ->first();
                        $invitationId = $existingInv ? (int) $existingInv->id : 0;
                    }

                    // Insert Outbox Event for durable sync
                    $outboxId = DB::table('sys_outbox_events')->insertGetId([
                        'tenant_id' => $from_company_id,
                        'idempotency_key' => $idempotencyKey,
                        'aggregate_type' => 'AgencyRequest',
                        'aggregate_id' => $invitationId ?: $to_company_id,
                        'aggregate_version' => 1,
                        'event_type' => 'AGENCY_REQUEST_SENT',
                        'payload' => json_encode([
                            'from_company_id' => $from_company_id,
                            'to_company_id' => $to_company_id,
                            'invitation_id' => $invitationId,
                            'action' => 'sent',
                        ]),
                        'status' => 'PENDING',
                        'attempts' => 0,
                        'available_at' => now(),
                        'created_at' => now(),
                    ]);

                    $mutationResponse = ['status' => 'success'];
                }

                // Invalidate Redis caches
                self::invalidateTenantCache($from_company_id);
                self::invalidateTenantCache($to_company_id);
            });

            // 4. Dispatch decoupled Outbox worker job after transaction commit
            if ($outboxId) {
                try {
                    ProcessAgencyOutboxEventJob::dispatch($outboxId)->afterCommit();
                } catch (\Throwable $e) {
                    Log::warning('FindAgencyService: Outbox job dispatch deferred: '.$e->getMessage());
                }
            }

            // 5. Update Idempotency Record status to COMPLETED
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', $from_company_id)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'COMPLETED',
                    'response_status' => 200,
                    'response_body' => json_encode($mutationResponse),
                    'completed_at' => now(),
                    'updated_at' => now(),
                ]);

            return response()->json($mutationResponse);

        } catch (\Throwable $e) {
            Log::error('FindAgencyService: Mutation failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            DB::table('sys_idempotency_keys')
                ->where('tenant_id', $from_company_id)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'FAILED_RETRYABLE',
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'updated_at' => now(),
                ]);

            return response()->json(['status' => 'error', 'message' => 'Failed to process agency request.'], 500);
        }
    }
}
