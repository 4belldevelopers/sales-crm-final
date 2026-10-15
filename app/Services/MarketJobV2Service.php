<?php

namespace App\Services;

use App\Helper\GeneralMethod;
use Illuminate\Http\Request;

/**
 * MarketJobV2Service
 *
 * Intent: Service layer for Find Jobs V2 architecture. Responsible for constructing
 * high-performance Elasticsearch queries, handling single-pass bulk fetching of relation data,
 * and preparing denormalized job items for listing display without runtime N+1 database queries.
 *
 * Updated (2026-08-18): Added getCommercialTermsBulk() single-pass Redis fetch for the new
 * 5-column card design's inline Commercial Terms column. Zero DB queries on warm Redis cache.
 *
 * Tenancy Note: Tenant isolation is enforced via Elasticsearch `must_not` term filter on `comId`
 * ($companyId), ensuring recruiters never view or apply to their own company's job postings.
 */
class MarketJobV2Service
{
    protected ElasticService $elasticService;

    /**
     * Constructor for MarketJobV2Service
     */
    public function __construct(ElasticService $elasticService)
    {
        $this->elasticService = $elasticService;
    }

    /**
     * Fetch paginated Market Jobs list using V2 single-pass architecture.
     *
     * Intent: Builds ES search query for `ats_jobs`, executes single-pass bulk recommendation relation
     * fetch, and attaches pre-loaded metadata onto job hits.
     *
     * @return array Contains 'data', 'total', 'page', 'size'
     */
    public function fetchMarketJobs(Request $request, int $companyId): array
    {
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) ($request->input('per_page') ?? $request->input('size') ?? $request->input('limit') ?? 10);
        $from = ($page - 1) * $size;

        // Parse Skills input (cus_search can be Tagify JSON string or plain text/comma-separated)
        $searchTerms = $request->input('cus_search');
        $requiredSkills = [];

        if (! empty($searchTerms)) {
            $customSearch = json_decode($searchTerms, true);
            if (is_array($customSearch)) {
                $checkedSkills = array_filter($customSearch, fn ($s) => ($s['checked'] ?? false) == true);
                $uncheckedSkills = array_filter($customSearch, fn ($s) => ($s['checked'] ?? false) == false);

                if (! empty($checkedSkills)) {
                    $requiredSkills = array_column($checkedSkills, 'value');
                } else {
                    $requiredSkills = array_column($uncheckedSkills, 'value');
                }
            } elseif (is_string($searchTerms)) {
                $requiredSkills = array_filter(array_map('trim', explode(',', $searchTerms)));
            }
        }

        // Parse Preferred Location input (kt_tagify_location can be Tagify JSON string or plain text/comma-separated)
        $locationTerms = $request->input('kt_tagify_location');
        $requiredLocations = [];

        if (! empty($locationTerms)) {
            $customLoc = json_decode($locationTerms, true);
            if (is_array($customLoc)) {
                $requiredLocations = array_filter(array_column($customLoc, 'value'));
            } elseif (is_string($locationTerms)) {
                $requiredLocations = array_filter(array_map('trim', explode(',', $locationTerms)));
            }
        }

        // Parse salary parameters and mode conversions
        $salMode = (int) ($request->input('sal_mode') ?? 0);
        $minSalaryCurrency = (int) ($request->input('min_salary_currency') ?? 0);

        // Only apply experience filter when user explicitly set a value (not empty/null/zero)
        $minExpRaw = $request->input('min_exp');
        $maxExpRaw = $request->input('max_exp');
        $minExp = ($minExpRaw !== null && $minExpRaw !== '' && (int) $minExpRaw > 0) ? (int) $minExpRaw : null;
        $maxExp = ($maxExpRaw !== null && $maxExpRaw !== '' && (int) $maxExpRaw > 0) ? (int) $maxExpRaw : null;

        $minSalaryRaw = $request->input('min_sal');
        $maxSalaryRaw = $request->input('max_sal');

        $currencyList = GeneralMethod::currency();
        $currencyCode = $currencyList[$minSalaryCurrency] ?? 'INR';

        $convertedMinSal = $minSalaryRaw !== null ? (int) $minSalaryRaw : null;
        if ($minSalaryCurrency > 0 && $convertedMinSal !== null) {
            $convertedMinSal = GeneralMethod::convertToINR($convertedMinSal, $currencyCode);
        }

        $convertedMaxSal = $maxSalaryRaw !== null ? (int) $maxSalaryRaw : null;
        if ($minSalaryCurrency > 0 && $convertedMaxSal !== null) {
            $convertedMaxSal = GeneralMethod::convertToINR($convertedMaxSal, $currencyCode);
        }

        // Null-safe: salaryDividedByMode may return null when no salary is set
        $minCtcDivided = $convertedMinSal !== null ? GeneralMethod::salaryDividedByMode($salMode, $convertedMinSal) : null;
        $maxCtcDivided = $convertedMaxSal !== null ? GeneralMethod::salaryDividedByMode($salMode, $convertedMaxSal) : null;

        $minSalary = null;
        $maxSalary = null;

        if ($salMode === 2) {
            $minSalary = is_object($minCtcDivided) ? ($minCtcDivided->hourly ?? null) : null;
            $maxSalary = is_object($maxCtcDivided) ? ($maxCtcDivided->hourly ?? null) : null;
        } elseif ($salMode === 1) {
            $minSalary = is_object($minCtcDivided) ? ($minCtcDivided->monthly ?? null) : null;
            $maxSalary = is_object($maxCtcDivided) ? ($maxCtcDivided->monthly ?? null) : null;
        } else {
            $minSalary = is_object($minCtcDivided) ? ($minCtcDivided->yearly ?? null) : null;
            $maxSalary = is_object($maxCtcDivided) ? ($maxCtcDivided->yearly ?? null) : null;
        }

        // Build Elasticsearch Query Structure
        $must = [];
        $filter = [];
        $mustNot = [];
        $sort = [];

        $sortOrder = $request->input('custom_sort') ?? $request->input('custom_hiringtype') ?? 1;
        switch ((int) $sortOrder) {
            case 1:
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 2:
                $sort[] = ['id' => ['order' => 'asc']];
                break;
            case 3:
                $sort[] = ['max_salary' => ['order' => 'asc', 'missing' => '_last']];
                break;
            case 4:
                $sort[] = ['max_salary' => ['order' => 'desc', 'missing' => '_last']];
                break;
            case 5:
                $sort[] = ['client_name' => ['order' => 'asc']];
                break;
            case 6:
                $sort[] = ['client_name' => ['order' => 'desc']];
                break;
            case 7:
                $sort[] = ['max_exp' => ['order' => 'desc']];
                break;
            case 8:
                $sort[] = ['max_exp' => ['order' => 'asc']];
                break;
            default:
                $sort[] = ['id' => ['order' => 'desc']];
        }

        // Status Filter — only active jobs in marketplace
        $filter[] = ['term' => ['status' => 'active']];

        // Tab Filtering (All Agencies Jobs / Fulltime / Contract / Freelance / Part-Time / Agency Requests)
        $customTab = $request->input('custom_tab');
        if ($customTab === 'fulltime') {
            $filter[] = ['term' => ['employment_type' => 0]];
        } elseif ($customTab === 'contract') {
            $filter[] = ['term' => ['employment_type' => 1]];
        } elseif ($customTab === 'freelance') {
            $filter[] = ['term' => ['employment_type' => 2]];
        } elseif ($customTab === 'parttime') {
            $filter[] = ['term' => ['employment_type' => 3]];
        } elseif ($customTab === 'agency_request') {
            $filter[] = ['term' => ['public_market_status' => 2]];
        }

        // Drawer Hiring Type filter (0 = Fulltime, 1 = Contract)
        $hiringTypeDrawer = $request->input('app_int_outsource');
        if ($hiringTypeDrawer !== null && $hiringTypeDrawer !== '' && $hiringTypeDrawer !== 'All') {
            $filter[] = ['term' => ['employment_type' => (int) $hiringTypeDrawer]];
        }

        // Free-text Search (Job Title & Skills)
        $textSearch = trim($request->input('custom_search') ?? $request->input('search') ?? $request->input('inputJobSearch') ?? '');
        if (! empty($textSearch)) {
            $must[] = [
                'bool' => [
                    'should' => [
                        ['match_phrase_prefix' => ['job_title' => $textSearch]],
                        ['match' => ['job_title' => $textSearch]],
                        ['nested' => [
                            'path' => 'job_metronics_skills',
                            'query' => [
                                'match' => ['job_metronics_skills.skill_name' => $textSearch],
                            ],
                        ]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        // Tenancy Condition: Exclude current company's own jobs (status filter is in $filter above)
        $mustNot[] = ['term' => ['comId' => $companyId]];

        // Required Skills Filter (Intent: matches nested job_metronics_skills as well as job title/description/requirement for high recall)
        if (! empty($requiredSkills)) {
            $skillQueries = [];
            foreach ($requiredSkills as $skill) {
                $skillClean = trim($skill);
                if (empty($skillClean)) {
                    continue;
                }

                $skillQueries[] = [
                    'bool' => [
                        'should' => [
                            ['nested' => [
                                'path' => 'job_metronics_skills',
                                'query' => [
                                    'match' => ['job_metronics_skills.skill_name' => $skillClean],
                                ],
                            ]],
                            ['match_phrase' => ['title' => $skillClean]],
                            ['match_phrase' => ['job_description' => $skillClean]],
                            ['match_phrase' => ['job_requirement' => $skillClean]],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ];
            }

            if (! empty($skillQueries)) {
                foreach ($skillQueries as $sQuery) {
                    $must[] = $sQuery;
                }
            }
        }

        // Preferred Location Filter (Intent: matches nested client_city_jobs.city_name/country_name, client_location.address, or text match on description/title)
        if (! empty($requiredLocations)) {
            $locationQueries = [];
            foreach ($requiredLocations as $loc) {
                $rawLoc = trim($loc);
                if (empty($rawLoc)) {
                    continue;
                }

                // Extract primary city search term (e.g. "Noida" from "Noida | Uttar Pradesh | India" or "Noida, India")
                $cityTerm = $rawLoc;
                if (str_contains($rawLoc, '|')) {
                    $parts = array_filter(array_map('trim', explode('|', $rawLoc)));
                    $cityTerm = reset($parts) ?: $rawLoc;
                } elseif (str_contains($rawLoc, ',')) {
                    $parts = array_filter(array_map('trim', explode(',', $rawLoc)));
                    $cityTerm = reset($parts) ?: $rawLoc;
                }
                $cityClean = strtolower(trim($cityTerm));

                if (empty($cityClean)) {
                    continue;
                }

                $shouldClauses = [
                    // 1. Nested match on client_city_jobs (exact city or country match)
                    [
                        'nested' => [
                            'path' => 'client_city_jobs',
                            'query' => [
                                'bool' => [
                                    'should' => [
                                        ['term' => ['client_city_jobs.city_name' => $cityClean]],
                                        ['match_phrase' => ['client_city_jobs.city_name' => $cityClean]],
                                        ['term' => ['client_city_jobs.country_name' => $cityClean]],
                                        ['match_phrase' => ['client_city_jobs.country_name' => $cityClean]],
                                    ],
                                    'minimum_should_match' => 1,
                                ],
                            ],
                        ],
                    ],
                    // 2. Direct address match on client_location address
                    ['match_phrase' => ['client_location.address' => $cityClean]],
                    // 3. Fallback text matches on title or job description
                    ['match_phrase' => ['job_description' => $cityClean]],
                    ['match_phrase' => ['title' => $cityClean]],
                ];

                if ($cityClean === 'remote') {
                    $shouldClauses[] = ['term' => ['work_type' => 'yes']];
                }

                $locationQueries[] = [
                    'bool' => [
                        'should' => $shouldClauses,
                        'minimum_should_match' => 1,
                    ],
                ];
            }

            if (! empty($locationQueries)) {
                foreach ($locationQueries as $lQuery) {
                    $must[] = $lQuery;
                }
            }
        }

        // Experience Filters — only applied when user explicitly sets a value > 0
        if ($minExp !== null) {
            $filter[] = ['range' => ['max_exp' => ['gte' => $minExp]]];
        }
        if ($maxExp !== null) {
            $filter[] = ['range' => ['max_exp' => ['lte' => $maxExp]]];
        }

        // Salary Range Filters
        $salaryField = match ($salMode) {
            1 => 'max_monthly',
            2 => 'max_hourly',
            default => 'max_yearly',
        };

        // Salary Range Filters — strictly bounded to positive values (Rule 1: omit if no positive salary boundary)
        if ($minSalary !== null && (float) $minSalary > 0) {
            $filter[] = ['range' => [$salaryField => ['gte' => (float) $minSalary]]];
        }
        if ($maxSalary !== null && (float) $maxSalary > 0) {
            $filter[] = ['range' => [$salaryField => ['lte' => (float) $maxSalary]]];
        }

        // Execute Primary Elasticsearch Query
        $params = [
            'index' => ELASTIC_JOBS,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => $filter,
                        'must_not' => $mustNot,
                    ],
                ],
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_JOBS)) {
            return ['data' => [], 'total' => 0, 'page' => $page, 'size' => $size];
        }

        $response = $this->elasticService->getClient()->search($params);
        $total = $response['hits']['total']['value'] ?? 0;
        $rawHits = array_map(fn ($hit) => $hit['_source'], $response['hits']['hits'] ?? []);

        if (empty($rawHits)) {
            return ['data' => [], 'total' => 0, 'page' => $page, 'size' => $size];
        }

        // SINGLE-PASS BULK FETCH: Bulk load Recommendation Relations for all jobs on current page
        $jobIds = array_column($rawHits, 'id');
        $relationsMap = GeneralMethod::getrRcommendationRelationDataBulk($companyId, $jobIds);

        // SINGLE-PASS BULK FETCH: Bulk load Bench Candidate Match Counts for all jobs on current page (0 DB queries)
        $benchCountsMap = self::getBenchCandidateCountsBulk($companyId, $rawHits);

        // SINGLE-PASS BULK FETCH: Bulk load Commercial Terms for all unique client companies on
        // current page. Reads from Redis (AgencyCommercialTermsService, 1hr TTL) — zero DB queries
        // on warm cache. Used by the new 5-column card's inline Commercial Terms column.
        $uniqueComIds = array_values(array_unique(array_filter(array_column($rawHits, 'comId'))));
        $commercialTermsMap = self::getCommercialTermsBulk($uniqueComIds);

        // BULK RESOLVE: Only lookup category/department names from Redis/DB if not already in the ES doc
        $missingCategoryIds = [];
        foreach ($rawHits as $hit) {
            if (empty($hit['category_name']) && ! empty($hit['category_id'])) {
                $missingCategoryIds[] = (int) $hit['category_id'];
            }
        }
        $categoryNamesMap = ! empty($missingCategoryIds) ? self::getCategoryNamesBulk(array_unique($missingCategoryIds)) : [];

        // Pre-attach Bulk Data onto Job Hit Objects
        $processedData = array_map(function ($hit) use ($relationsMap, $benchCountsMap, $commercialTermsMap, $categoryNamesMap) {
            $jobId = $hit['id'] ?? null;
            $hitComId = (int) ($hit['comId'] ?? 0);
            $catId = (int) ($hit['category_id'] ?? 0);
            $relation = $relationsMap[$jobId] ?? (object) [];

            $hit['v2_relation'] = $relation;
            $hit['v2_recommendation_id'] = $relation->id ?? null;
            $hit['v2_final_status'] = $relation->final_status ?? null;
            $hit['v2_from_company_id'] = $relation->from_company_id ?? null;
            $hit['v2_to_company_id'] = $relation->to_company_id ?? null;
            $hit['v2_bench_match_count'] = $benchCountsMap[$jobId] ?? 0;
            $hit['v2_commercial_terms'] = $commercialTermsMap[$hitComId] ?? null;
            $hit['category_name'] = ! empty($hit['category_name']) ? $hit['category_name'] : ($categoryNamesMap[$catId] ?? '');
            $hit['v2_category_name'] = $hit['category_name'];

            return $hit;
        }, $rawHits);

        return [
            'data' => $processedData,
            'total' => $total,
            'page' => $page,
            'size' => $size,
        ];
    }

    /**
     * Generate versioned Redis cache key for tenant market job searches.
     */
    public function getCacheKey(Request $request, int $companyId): string
    {
        $versionKey = 'allmarketjobs_version_company_'.$companyId;
        $globalVersionKey = 'allmarketjobs_global_version';
        $companyVersion = cache()->get($versionKey, 1);
        $globalVersion = cache()->get($globalVersionKey, 1);

        $payload = [
            'page' => (int) $request->input('page', 1),
            'size' => (int) ($request->input('per_page') ?? $request->input('size') ?? $request->input('limit') ?? 10),
            'q' => $request->input('q'),
            'cus_search' => $request->input('cus_search'),
            'custom_tab' => $request->input('custom_tab'),
            'custom_hiringtype' => $request->input('custom_hiringtype'),
            'app_int_outsource' => $request->input('app_int_outsource'),
            'min_exp' => $request->input('min_exp'),
            'max_exp' => $request->input('max_exp'),
            'kt_tagify_location' => $request->input('kt_tagify_location'),
            'min_sal' => $request->input('min_sal'),
            'max_sal' => $request->input('max_sal'),
            'min_salary_currency' => $request->input('min_salary_currency'),
            'sal_mode' => $request->input('sal_mode'),
            'company_id' => $companyId,
        ];

        return 'ats:v2:allmarketjobs:'.$companyId.':'.$companyVersion.':'.$globalVersion.':'.md5(json_encode($payload));
    }

    /**
     * Invalidate Market Jobs cache for a specific tenant company.
     */
    public static function invalidateCacheForCompany(int $companyId): void
    {
        $versionKey = 'allmarketjobs_version_company_'.$companyId;
        \App\Helper\GeneralMethod::cacheClean($versionKey);
    }

    /**
     * Invalidate global Market Jobs cache version.
     */
    public static function invalidateGlobalCache(): void
    {
        $globalVersionKey = 'allmarketjobs_global_version';
        \App\Helper\GeneralMethod::cacheClean($globalVersionKey);
    }

    /**
     * Bulk fetch bench candidate match counts for an array of job hit documents.
     *
     * Intent: Executes a single Elasticsearch `msearch` query to compute bench candidate skill
     * match counts for all jobs on the current page in 1 round trip with ZERO MySQL queries.
     * Tenancy Note: Filtered by tenant $companyId, `condidate_type` = 'bench_condidate', status = 2.
     *
     * @param  int  $companyId  Tenant company ID
     * @param  array  $rawHits  Raw job hits from ats_jobs search
     * @return array Map of [job_id => match_count]
     */
    public static function getBenchCandidateCountsBulk(int $companyId, array $rawHits): array
    {
        if (empty($rawHits)) {
            return [];
        }

        $elasticService = app(ElasticService::class);
        $map = [];

        if (! defined('ELASTIC_CANDIDATES_BENCH') || ! $elasticService->indexExists(ELASTIC_CANDIDATES_BENCH)) {
            return [];
        }

        $msearchBody = [];
        $jobIdOrder = [];

        foreach ($rawHits as $hit) {
            $jobId = $hit['id'] ?? null;
            if (! $jobId) {
                continue;
            }

            $skillNames = [];
            $rawSkills = $hit['job_metronics_skills'] ?? [];
            if (is_array($rawSkills)) {
                foreach ($rawSkills as $s) {
                    $name = is_array($s) ? ($s['skill_name'] ?? '') : ($s->skill_name ?? '');
                    if (! empty($name)) {
                        $skillNames[] = strtolower(trim($name));
                    }
                }
            }

            if (empty($skillNames) && ! empty($hit['default_skill'])) {
                $parts = explode(',', $hit['default_skill']);
                foreach ($parts as $p) {
                    $pClean = strtolower(trim($p));
                    if (! empty($pClean)) {
                        $skillNames[] = $pClean;
                    }
                }
            }

            $skillShould = [];
            foreach (array_unique($skillNames) as $skill) {
                $skillShould[] = [
                    'term' => [
                        'job_application_skills.skills.keyword' => [
                            'value' => $skill,
                            'boost' => 10,
                        ],
                    ],
                ];
            }

            $mustQuery = [
                ['term' => ['company_id' => (int) $companyId]],
                ['term' => ['condidate_type' => 'bench_condidate']],
                ['term' => ['status' => 2]],
            ];

            $boolQuery = [
                'must' => $mustQuery,
            ];

            if (! empty($skillShould)) {
                $boolQuery['should'] = $skillShould;
                $boolQuery['minimum_should_match'] = 1;
            }

            $msearchBody[] = ['index' => ELASTIC_CANDIDATES_BENCH, 'request_cache' => true];
            $msearchBody[] = [
                'size' => 0,
                'track_total_hits' => true,
                'query' => ['bool' => $boolQuery],
            ];

            $jobIdOrder[] = $jobId;
        }

        if (empty($msearchBody)) {
            return [];
        }

        try {
            $response = $elasticService->getClient()->msearch(['body' => $msearchBody]);
            $responses = $response['responses'] ?? [];

            foreach ($responses as $index => $res) {
                $jobId = $jobIdOrder[$index] ?? null;
                if ($jobId !== null) {
                    $totalHits = $res['hits']['total']['value'] ?? 0;
                    $map[$jobId] = (int) $totalHits;
                }
            }
        } catch (\Throwable $e) {
            \Log::error('getBenchCandidateCountsBulk failed: '.$e->getMessage());
        }

        return $map;
    }

    /**
     * getCommercialTermsBulk
     *
     * Intent: Bulk-fetch commercial terms for a set of client company IDs using the
     * AgencyCommercialTermsService which reads from Redis cache (1hr TTL). Returns a
     * simplified summary object per company containing only the 5 fields needed by
     * the card UI inline Commercial Terms column. Zero DB queries on cache hit.
     *
     * Tenancy Note: Terms are scoped to each individual client company (comId), not the
     * viewing agency. The viewing agency sees the posting company's fee structure.
     *
     * @param  int[]  $comIds  Array of unique client company IDs from the current page hits
     * @return array Map of [comId => simplified_terms_array]
     */
    public static function getCommercialTermsBulk(array $comIds): array
    {
        if (empty($comIds)) {
            return [];
        }

        /** @var \App\Services\AgencyCommercialTermsService $termsService */
        $termsService = app(\App\Services\AgencyCommercialTermsService::class);
        $map = [];

        foreach ($comIds as $comId) {
            $comId = (int) $comId;
            if ($comId <= 0) {
                continue;
            }
            try {
                $terms = $termsService->getForCompany($comId);
                $map[$comId] = [
                    'fulltime_fee_model' => $terms->fulltime_fee_model ?? 'percentage',
                    'hiring_fee_pct' => $terms->fulltime_success_fee_percentage ?? 8.33,
                    'fixed_fee_amount' => $terms->fulltime_fixed_fee_amount ?? null,
                    'fixed_fee_unit' => $terms->fulltime_fixed_fee_unit ?? 'Per Hire',
                    'replacement_days' => $terms->fulltime_replacement_period_days ?? 90,
                    'payment_days' => $terms->fulltime_payment_terms_days ?? 30,
                    'contract_payment_days' => $terms->contract_payment_terms_days ?? 30,
                    'contract_agency_rate_type' => $terms->contract_agency_rate_type ?? 'Fixed Rate',
                    'billing_model' => ucfirst($terms->contract_billing_model ?? 'monthly'),
                    'billing_rate' => $terms->contract_rate_amount ?? null,
                    'contract_experience_pricing' => $terms->contract_experience_pricing ?? [],
                    'fulltime_experience_pricing' => $terms->fulltime_experience_pricing ?? [],
                ];
            } catch (\Throwable $e) {
                \Log::warning("getCommercialTermsBulk: failed for comId={$comId}: ".$e->getMessage());
                // Graceful fallback — card will show "View Full Terms" link only
                $map[$comId] = null;
            }
        }

        return $map;
    }

    /**
     * Bulk fetch category/department names for an array of category IDs.
     * Cached in Redis (24hr TTL) to ensure zero DB queries on warm cache.
     *
     * @param  int[]  $categoryIds  Array of unique category IDs from current page hits
     * @return array Map of [category_id => category_name]
     */
    public static function getCategoryNamesBulk(array $categoryIds): array
    {
        if (empty($categoryIds)) {
            return [];
        }

        $result = [];
        $missingIds = [];

        foreach ($categoryIds as $catId) {
            $catId = (int) $catId;
            if ($catId <= 0) {
                continue;
            }
            $cacheKey = "ats:category_name:{$catId}";
            $cached = cache()->get($cacheKey);
            if ($cached !== null) {
                $result[$catId] = $cached;
            } else {
                $missingIds[] = $catId;
            }
        }

        if (! empty($missingIds)) {
            try {
                $dbCategories = \Illuminate\Support\Facades\DB::table('job_categories')
                    ->whereIn('id', $missingIds)
                    ->pluck('name', 'id')
                    ->toArray();

                foreach ($missingIds as $catId) {
                    $name = $dbCategories[$catId] ?? '';
                    $result[$catId] = $name;
                    cache()->put("ats:category_name:{$catId}", $name, now()->addHours(24));
                }
            } catch (\Throwable $e) {
                \Log::warning('getCategoryNamesBulk: DB fetch failed: '.$e->getMessage());
            }
        }

        return $result;
    }
}
