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

        $searchTerms = $request->input('cus_search');
        $customSearch = json_decode($searchTerms, true);
        $requiredSkills = [];

        if (! empty($searchTerms) && is_array($customSearch)) {
            $checkedSkills = array_filter($customSearch, fn ($s) => ($s['checked'] ?? false) == true);
            $uncheckedSkills = array_filter($customSearch, fn ($s) => ($s['checked'] ?? false) == false);

            if (! empty($checkedSkills)) {
                $requiredSkills = array_column($checkedSkills, 'value');
            } else {
                $requiredSkills = array_column($uncheckedSkills, 'value');
            }
        }

        // Parse salary parameters and mode conversions
        $salMode = (int) ($request->input('sal_mode') ?? 0);
        $minSalaryCurrency = (int) ($request->input('min_salary_currency') ?? 0);
        $minExp = $request->input('min_exp') !== null ? (int) $request->input('min_exp') : 0;
        $maxExp = $request->input('max_exp') !== null ? (int) $request->input('max_exp') : null;
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

        $minCtcDivided = $convertedMinSal !== null ? GeneralMethod::salaryDividedByMode($salMode, $convertedMinSal) : null;
        $maxCtcDivided = $convertedMaxSal !== null ? GeneralMethod::salaryDividedByMode($salMode, $convertedMaxSal) : null;

        $minSalary = null;
        $maxSalary = null;

        if ($salMode === 2) {
            $minSalary = $minCtcDivided->hourly ?? null;
            $maxSalary = $maxCtcDivided->hourly ?? null;
        } elseif ($salMode === 1) {
            $minSalary = $minCtcDivided->monthly ?? null;
            $maxSalary = $maxCtcDivided->monthly ?? null;
        } else {
            $minSalary = $minCtcDivided->yearly ?? null;
            $maxSalary = $maxCtcDivided->yearly ?? null;
        }

        // Build Elasticsearch Query Structure
        $must = [];
        $filter = [];
        $mustNot = [];
        $sort = [];

        $valCheck = $request->input('custom_hiringtype');
        switch ($valCheck) {
            case 1:
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 2:
                $sort[] = ['id' => ['order' => 'asc']];
                break;
            case 3:
                $sort[] = ['max_salary' => ['order' => 'asc']];
                break;
            case 4:
                $sort[] = ['max_salary' => ['order' => 'desc']];
                break;
            case 5:
                $sort[] = ['client_name' => ['order' => 'asc']];
                break;
            case 6:
                $sort[] = ['client_name' => ['order' => 'desc']];
                break;
            default:
                $sort[] = ['id' => ['order' => 'desc']];
        }

        // Status Filter
        $filter[] = ['terms' => ['status' => ['active', 'hold', 'inactive', '2']]];

        if ($valCheck !== null && $valCheck !== '' && $valCheck !== 'All') {
            if ((int) $valCheck === 2) {
                $filter[] = ['term' => ['public_market_status' => 2]];
            } elseif ((int) $valCheck === 1) {
                $mustNot[] = ['term' => ['public_market_status' => 2]];
            }
        }

        // Employment Type / Hiring Type
        $customHiringType = $request->input('app_int_outsource');
        if ($customHiringType !== null && $customHiringType !== '' && $customHiringType !== 'All') {
            $filter[] = ['term' => ['employment_type' => (int) $customHiringType]];
        }

        // Tenancy Condition: Exclude current company's own jobs
        $must[] = [
            'bool' => [
                'must' => [['term' => ['status' => 'active']]],
                'must_not' => [['term' => ['comId' => $companyId]]],
            ],
        ];

        // Required Skills Filter (Nested ES Query)
        if (! empty($requiredSkills)) {
            $must[] = [
                'nested' => [
                    'path' => 'job_metronics_skills',
                    'query' => [
                        'bool' => [
                            'should' => array_map(fn ($skill) => [
                                'match' => ['job_metronics_skills.skill_name' => $skill],
                            ], $requiredSkills),
                            'minimum_should_match' => 1,
                        ],
                    ],
                ],
            ];
        }

        // Experience Filters
        if ($minExp !== null) {
            $must[] = ['range' => ['max_exp' => ['gte' => $minExp]]];
        }
        if ($maxExp !== null && $maxExp > 0) {
            $must[] = ['range' => ['max_exp' => ['lte' => $maxExp]]];
        }

        // Salary Range Filters
        $salaryField = match ($salMode) {
            1 => 'max_monthly',
            2 => 'max_hourly',
            default => 'max_yearly',
        };

        if ($minSalary !== null) {
            $must[] = ['range' => [$salaryField => ['gte' => (float) $minSalary]]];
        }
        if ($maxSalary !== null && $maxSalary > 0) {
            $must[] = ['range' => [$salaryField => ['lte' => (float) $maxSalary]]];
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

        // Pre-attach Bulk Data onto Job Hit Objects
        $processedData = array_map(function ($hit) use ($relationsMap, $benchCountsMap, $commercialTermsMap) {
            $jobId = $hit['id'] ?? null;
            $hitComId = (int) ($hit['comId'] ?? 0);
            $relation = $relationsMap[$jobId] ?? (object) [];

            $hit['v2_relation'] = $relation;
            $hit['v2_recommendation_id'] = $relation->id ?? null;
            $hit['v2_final_status'] = $relation->final_status ?? null;
            $hit['v2_from_company_id'] = $relation->from_company_id ?? null;
            $hit['v2_to_company_id'] = $relation->to_company_id ?? null;
            $hit['v2_bench_match_count'] = $benchCountsMap[$jobId] ?? 0;
            $hit['v2_commercial_terms'] = $commercialTermsMap[$hitComId] ?? null;

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
        $currentVersion = (int) cache()->get($versionKey, 1);
        cache()->forever($versionKey, $currentVersion + 1);
    }

    /**
     * Invalidate global Market Jobs cache version.
     */
    public static function invalidateGlobalCache(): void
    {
        $globalVersionKey = 'allmarketjobs_global_version';
        $currentVersion = (int) cache()->get($globalVersionKey, 1);
        cache()->forever($globalVersionKey, $currentVersion + 1);
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
                // Return only the 5 fields the card UI needs — keep payload minimal
                $map[$comId] = [
                    'hiring_fee_pct' => $terms->fulltime_success_fee_percentage ?? 8.33,
                    'replacement_days' => $terms->fulltime_replacement_period_days ?? 90,
                    'payment_days' => $terms->fulltime_payment_terms_days ?? 30,
                    'billing_model' => ucfirst($terms->contract_billing_model ?? 'monthly'),
                    'billing_rate' => $terms->contract_rate_amount ?? null,
                ];
            } catch (\Throwable $e) {
                \Log::warning("getCommercialTermsBulk: failed for comId={$comId}: ".$e->getMessage());
                // Graceful fallback — card will show "View Full Terms" link only
                $map[$comId] = null;
            }
        }

        return $map;
    }
}
