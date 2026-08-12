<?php

// ShortlistBenchCandidateListingService — handles Elasticsearch query construction,
// hero stats aggregation, and single-pass zero-N+1 batch data hydration for the
// Shortlisted Bench Candidate listing page (service_company/bench-candidates/offerListing).
// Source of truth: Elasticsearch `candidates` index + MySQL batch lookups for master statuses,
// job/client details, and duplicate candidate flags.

namespace App\Services;

use App\Helper\GeneralMethod;

class ShortlistBenchCandidateListingService
{
    protected ElasticService $elasticService;

    /**
     * ShortlistBenchCandidateListingService constructor.
     *
     * @param  ElasticService  $elasticService  Inject Elasticsearch client service wrapper
     */
    public function __construct(ElasticService $elasticService)
    {
        $this->elasticService = $elasticService;
    }

    /**
     * Hero KPI cards, status-tab counts (All/Pending/Offer Released/Accepted/Joining Soon/Expired),
     * and the "All Locations" filter options for the Shortlisted Bench Candidates listing.
     * Computed with a single Elasticsearch `filters` + `terms` aggregation call (candidates index)
     * and cached per-company, so the page load stays one ES round trip.
     *
     * @param  int  $companyId  Service company tenant ID (other_company)
     * @param  int  $userId  Authenticated user ID for version keying
     * @return array Pre-formatted hero counters, tab counts, and active location string array
     */
    public function getOfferStats(int $companyId, int $userId): array
    {
        $default = [
            'hero' => [
                'total_shortlisted' => 0,
                'joining_this_week' => 0,
                'offer_released' => 0,
                'offer_accepted' => 0,
                'joined' => 0,
            ],
            'tabs' => [
                'all' => 0,
                'pending' => 0,
                'released' => 0,
                'accepted' => 0,
                'joining_soon' => 0,
                'expired' => 0,
            ],
            'locations' => [],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            return $default;
        }

        $versionKey = 'allbenchofferListing_version_company_'.$companyId;
        $version = cache()->get($versionKey, cache()->get('allbenchofferListing_version_user_'.$userId, 1));
        $cacheKeySet = 'ats:benchofferStats:'.$companyId.':'.$version;

        if (cache()->has($cacheKeySet)) {
            return cache()->get($cacheKeySet);
        }

        $activeMust = ['terms' => ['job_master_status_id' => [5, 6]]];
        $activeMustNot = ['term' => ['status' => 3]];
        $noOfferShould = [
            'bool' => [
                'should' => [
                    ['bool' => ['must_not' => [['exists' => ['field' => 'of_offer_amount']]]]],
                    ['bool' => ['must_not' => [['exists' => ['field' => 'of_joined_date']]]]],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['other_company' => $companyId]],
                            ['term' => ['condidate_type' => 'bench_condidate']],
                        ],
                    ],
                ],
                'aggs' => [
                    'hero_stats' => [
                        'filters' => [
                            'filters' => [
                                'total_shortlisted' => [
                                    'bool' => ['must' => [$activeMust], 'must_not' => [$activeMustNot]],
                                ],
                                'joining_this_week' => [
                                    'bool' => [
                                        'must' => [
                                            $activeMust,
                                            ['range' => ['of_joined_date' => ['gte' => 'now/d', 'lte' => 'now+7d/d']]],
                                        ],
                                        'must_not' => [$activeMustNot],
                                    ],
                                ],
                                'offer_released' => [
                                    'bool' => [
                                        'must' => [$activeMust, ['term' => ['of_offer_acceptance' => 1]]],
                                        'must_not' => [$activeMustNot],
                                    ],
                                ],
                                'offer_accepted' => [
                                    'bool' => [
                                        'must' => [$activeMust, ['term' => ['of_offer_acceptance' => 2]]],
                                        'must_not' => [$activeMustNot],
                                    ],
                                ],
                                'joined' => [
                                    'bool' => [
                                        'must' => [
                                            ['term' => ['job_master_status_id' => 6]],
                                            ['term' => ['status' => 3]],
                                        ],
                                    ],
                                ],
                                'pending' => [
                                    'bool' => ['must' => [$activeMust, $noOfferShould], 'must_not' => [$activeMustNot]],
                                ],
                                'expired' => [
                                    'bool' => [
                                        'must' => [$activeMust, ['term' => ['of_offer_acceptance' => 4]]],
                                        'must_not' => [$activeMustNot],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'active_locations' => [
                        'filter' => ['bool' => ['must' => [$activeMust], 'must_not' => [$activeMustNot]]],
                        'aggs' => [
                            'by_city' => [
                                'terms' => ['field' => 'city_name', 'size' => 30, 'order' => ['_count' => 'desc']],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        try {
            $response = $this->elasticService->getClient()->search($params);
        } catch (\Exception $e) {
            return $default;
        }

        $buckets = $response['aggregations']['hero_stats']['buckets'] ?? [];
        $cityBuckets = $response['aggregations']['active_locations']['by_city']['buckets'] ?? [];

        $stats = [
            'hero' => [
                'total_shortlisted' => $buckets['total_shortlisted']['doc_count'] ?? 0,
                'joining_this_week' => $buckets['joining_this_week']['doc_count'] ?? 0,
                'offer_released' => $buckets['offer_released']['doc_count'] ?? 0,
                'offer_accepted' => $buckets['offer_accepted']['doc_count'] ?? 0,
                'joined' => $buckets['joined']['doc_count'] ?? 0,
            ],
            'tabs' => [
                'all' => $buckets['total_shortlisted']['doc_count'] ?? 0,
                'pending' => $buckets['pending']['doc_count'] ?? 0,
                'released' => $buckets['offer_released']['doc_count'] ?? 0,
                'accepted' => $buckets['offer_accepted']['doc_count'] ?? 0,
                'joining_soon' => $buckets['joining_this_week']['doc_count'] ?? 0,
                'expired' => $buckets['expired']['doc_count'] ?? 0,
            ],
            'locations' => array_map(fn ($b) => $b['key'], $cityBuckets),
        ];

        cache()->put($cacheKeySet, $stats, now()->addMinutes(ALL_BENCH_OFFER_LISTING_TIME));

        return $stats;
    }

    /**
     * Fetch paginated shortlisted bench candidate listings from Elasticsearch with single-pass batch data hydration.
     * Eliminates in-loop MySQL and Elasticsearch N+1 queries by pre-fetching status names, client/job details,
     * and duplicate application flags prior to view rendering.
     *
     * @param  array  $requestData  Input request params (page, size, search, tab, country, sortingfilter)
     * @param  int  $companyId  Tenant company ID
     * @param  object  $user  Authenticated user model
     * @return array Pre-rendered HTML fragment, total hit count, and pagination HTML
     */
    public function getOfferListingData(array $requestData, int $companyId, $user): array
    {
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $searchQuery = $requestData['q'] ?? null;
        $valCheck = $requestData['sortingfilter'] ?? null;
        $page = max((int) ($requestData['page'] ?? 1), 1);
        $size = (int) ($requestData['size'] ?? 10);
        $from = ($page - 1) * $size;
        $sort = [];

        $searchTerm = trim((string) (($requestData['search'] ?? null) ?: ($requestData['cus_search'] ?? null)));
        $tab = (string) ($requestData['tab'] ?? 'all');
        $location = trim((string) ($requestData['country'] ?? null));

        $cachePayload = [
            'page' => $page,
            'size' => $size,
            'q' => $searchQuery,
            'company' => $companyId,
            'cus_search' => $requestData['cus_search'] ?? null,
            'search' => $searchTerm,
            'tab' => $tab,
            'location' => $location,
            'country' => $requestData['country'] ?? null,
            'sortingfilter' => $valCheck,
        ];

        $versionKey = 'allbenchofferListing_version_company_'.$companyId;
        $version = cache()->get($versionKey, cache()->get('allbenchofferListing_version_user_'.$user->id, 1));
        $cacheKeySet = 'ats:allbenchofferListing:'.$companyId.':'.$version.':'.md5(json_encode($cachePayload));

        if (cache()->has($cacheKeySet)) {
            return cache()->get($cacheKeySet);
        }

        switch ($valCheck) {
            case 1:
                $sort[] = ['created_at' => ['order' => 'desc']];
                break;
            case 2:
                $sort[] = ['created_at' => ['order' => 'asc']];
                break;
            case 3:
                $sort[] = ['ctc' => ['order' => 'asc', 'unmapped_type' => 'float']];
                break;
            case 4:
                $sort[] = ['ctc' => ['order' => 'desc', 'unmapped_type' => 'float']];
                break;
            case 5: // Notice period ASC (indexed numeric field)
                $sort[] = ['np_days' => ['order' => 'asc', 'unmapped_type' => 'integer', 'missing' => 999999]];
                break;
            case 6: // Notice period DESC (indexed numeric field)
                $sort[] = ['np_days' => ['order' => 'desc', 'unmapped_type' => 'integer', 'missing' => -1]];
                break;
            case 21: // Notice period Date ASC (indexed date field)
                $sort[] = ['np_date' => ['order' => 'asc', 'unmapped_type' => 'date', 'missing' => '_last']];
                break;
            case 22: // Notice period Date DESC (indexed date field)
                $sort[] = ['np_date' => ['order' => 'desc', 'unmapped_type' => 'date', 'missing' => '_last']];
                break;
            case 9:
                $sort[] = [
                    '_script' => [
                        'type' => 'number',
                        'script' => [
                            'lang' => 'painless',
                            'source' => "params['_source']['immediate_join'] == 'Immediate' ? 0 : 1",
                        ],
                        'order' => 'asc',
                    ],
                ];
                break;
            case 7:
                $sort[] = ['total_exp' => ['order' => 'desc', 'unmapped_type' => 'float']];
                break;
            case 8:
                $sort[] = ['total_exp' => ['order' => 'asc', 'unmapped_type' => 'float']];
                break;
            case 10:
                $sort[] = ['send_accept_date_time' => ['order' => 'desc']];
                break;
            case 11:
                $sort[] = ['send_accept_date_time' => ['order' => 'asc']];
                break;
            case 12:
                $sort[] = ['of_joined_date' => ['order' => 'desc']];
                break;
            case 13:
                $sort[] = ['of_joined_date' => ['order' => 'asc']];
                break;
            case 14:
                $sort[] = ['of_offer_amount' => ['order' => 'asc', 'unmapped_type' => 'float']];
                break;
            case 15:
                $sort[] = ['of_offer_amount' => ['order' => 'desc', 'unmapped_type' => 'float']];
                break;
            default:
                $sort[] = ['wiseId' => ['order' => 'desc', 'unmapped_type' => 'long']];
                break;
        }

        $boolQuery = [
            'bool' => [
                'must' => [
                    [
                        'bool' => [
                            'must' => [
                                ['term' => ['other_company' => $companyId]],
                                ['term' => ['condidate_type' => 'bench_condidate']],
                                ['terms' => ['job_master_status_id' => [5, 6]]],
                            ],
                        ],
                    ],
                ],
                'filter' => [],
                'must_not' => [
                    ['term' => ['status' => 3]],
                ],
            ],
        ];

        if ($searchTerm !== '') {
            $searchShould = [
                ['match_phrase_prefix' => ['full_name' => $searchTerm]],
                ['match_phrase_prefix' => ['title' => $searchTerm]],
            ];
            if (is_numeric($searchTerm)) {
                $searchShould[] = ['term' => ['offer_id' => (int) $searchTerm]];
            }
            $boolQuery['bool']['must'][] = ['bool' => ['should' => $searchShould, 'minimum_should_match' => 1]];
        }

        if ($location !== '' && strtolower($location) !== 'all') {
            $boolQuery['bool']['must'][] = ['term' => ['city_name' => $location]];
        }

        switch ($tab) {
            case 'pending':
                $boolQuery['bool']['must'][] = [
                    'bool' => [
                        'should' => [
                            ['bool' => ['must_not' => [['exists' => ['field' => 'of_offer_amount']]]]],
                            ['bool' => ['must_not' => [['exists' => ['field' => 'of_joined_date']]]]],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ];
                break;
            case 'released':
                $boolQuery['bool']['must'][] = ['term' => ['of_offer_acceptance' => 1]];
                break;
            case 'accepted':
                $boolQuery['bool']['must'][] = ['term' => ['of_offer_acceptance' => 2]];
                break;
            case 'joining_soon':
                $boolQuery['bool']['must'][] = ['range' => ['of_joined_date' => ['gte' => 'now/d', 'lte' => 'now+7d/d']]];
                break;
            case 'expired':
                $boolQuery['bool']['must'][] = ['term' => ['of_offer_acceptance' => 4]];
                break;
            default:
                break;
        }

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => $boolQuery,
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            $total = 0;
            $data = [];
        } else {
            try {
                $response = $this->elasticService->getClient()->search($params);
                $total = $response['hits']['total']['value'] ?? 0;
                $rawHits = array_map(fn ($hit) => $hit['_source'], $response['hits']['hits']);

                // ZERO N+1 BATCH HYDRATION
                $data = $this->batchHydrateCandidates($rawHits, $companyId, $user);
            } catch (\Exception $e) {
                $total = 0;
                $data = [];
            }
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        $userPermissions = $user->userPermissions ?? [];
        $htmlRaw = view('service_company.ajax_pages.bench-candidate-offer-ajax', compact('data', 'userPermissions', 'companyId', 'salaryMode', 'currencySymbole'))->render();
        $htmlClean = preg_replace('/>\s+</', '><', trim($htmlRaw));

        $paginationRaw = view('service_company.ajax_pages.pagination', compact('pagination'))->render();
        $paginationClean = preg_replace('/>\s+</', '><', trim($paginationRaw));

        $responseData = [
            'html' => $htmlClean,
            'total' => $total,
            'pagination' => $paginationClean,
        ];

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_BENCH_OFFER_LISTING_TIME));

        return $responseData;
    }

    /**
     * Batch hydrate raw candidate hits to eliminate in-loop MySQL and Elasticsearch queries.
     * Pre-fetches master status names, job & client details, and duplicate application counts in single queries.
     *
     * @param  array  $hits  Raw candidate document array from Elasticsearch hits
     * @param  int  $companyId  Service company tenant ID
     * @param  object  $user  Authenticated user instance
     * @return array Pre-hydrated candidate records
     */
    public function batchHydrateCandidates(array $hits, int $companyId, $user): array
    {
        if (empty($hits)) {
            return [];
        }

        // 1. Pre-fetch bench_master_status table (small reference table, cached in-memory)
        $masterStatuses = cache()->remember('ats:bench_master_status_map', now()->addHours(24), function () {
            return \DB::table('bench_master_status')->get()->keyBy('id')->toArray();
        });

        // 2. Collect unique job IDs across all 10 candidate hits
        $jobIds = array_filter(array_unique(array_map(fn ($h) => $h['jobId'] ?? $h['job_id'] ?? null, $hits)));

        $jobDetailsMap = [];
        $clientNamesMap = [];

        if (! empty($jobIds)) {
            // Batch pre-fetch job details and client names in 1 query
            $jobsQuery = \DB::table('jobs')
                ->select(
                    'jobs.id as jobId',
                    'jobs.status',
                    'jobs.company_id as job_company_id',
                    'jobs.client_id',
                    'clients.name as client_name',
                    'companies.company_name'
                )
                ->leftJoin('clients', 'clients.id', '=', 'jobs.client_id')
                ->leftJoin('companies', 'companies.id', '=', 'jobs.company_id')
                ->whereIn('jobs.id', $jobIds)
                ->get();

            foreach ($jobsQuery as $jobRow) {
                $jobDetailsMap[$jobRow->jobId] = (object) [
                    'status' => $jobRow->status ?? 'Active',
                ];

                // Client name logic matching GeneralMethod::companyDetailsByClient
                if (($jobRow->job_company_id ?? null) != $companyId) {
                    $clientName = ! empty($jobRow->company_name) ? ucwords(mb_substr($jobRow->company_name, 0, 30)) : '';
                } else {
                    $clientName = $jobRow->client_name ?? '';
                }
                $clientNamesMap[$jobRow->jobId] = $clientName;
            }
        }

        // 3. Batch check duplicate candidates if duplicate checking is configured for tenant
        $duplicateFlagsMap = [];
        $company = $user->company ?? null;
        $appDuplicate = $company->app_duplicate ?? 0;
        $appDuplicateDays = (int) ($company->app_duplicate_days ?? 0);

        if ($appDuplicate == 1 || $appDuplicate == 2) {
            $emails = array_filter(array_unique(array_map(fn ($h) => $h['email'] ?? null, $hits)));
            $phones = array_filter(array_unique(array_map(fn ($h) => $h['phone'] ?? null, $hits)));

            if (! empty($emails) || ! empty($phones)) {
                // Single batch query for application counts across candidate emails and phones
                $dupCounts = \DB::table('job_applications')
                    ->select('email', 'phone', 'job_id', \DB::raw('COUNT(*) as total'))
                    ->where('company_id', $companyId)
                    ->where(function ($q) use ($emails, $phones) {
                        if (! empty($emails)) {
                            $q->whereIn('email', $emails);
                        }
                        if (! empty($phones)) {
                            $q->orWhereIn('phone', $phones);
                        }
                    })
                    ->where('created_at', '>', \DB::raw('DATE_SUB(CURDATE(), INTERVAL '.$appDuplicateDays.' DAY)'))
                    ->groupBy('email', 'phone', 'job_id')
                    ->get();

                foreach ($hits as $hit) {
                    if (($hit['duplicate'] ?? 0) == 1) {
                        $candEmail = $hit['email'] ?? '';
                        $candPhone = $hit['phone'] ?? '';
                        $candJobId = $hit['job_id'] ?? null;

                        $matchCount = 0;
                        foreach ($dupCounts as $dup) {
                            if ($appDuplicate == 1 && $dup->job_id != $candJobId) {
                                continue;
                            }
                            if (($candEmail !== '' && $dup->email === $candEmail) || ($candPhone !== '' && $dup->phone === $candPhone)) {
                                $matchCount += $dup->total;
                            }
                        }
                        $candKey = ($hit['candidate_id'] ?? $hit['wiseId'] ?? rand());
                        $duplicateFlagsMap[$candKey] = $matchCount > 1 ? 1 : 0;
                    }
                }
            }
        }

        // 4. Batch check confidential amount visibility permission for tenant user
        $confidentialAllowMap = [];
        $isAdmin = method_exists($user, 'hasRole') ? $user->hasRole('admin') : false;
        if ($isAdmin) {
            foreach ($jobIds as $jId) {
                $confidentialAllowMap[$jId] = true;
            }
        } elseif (! empty($jobIds)) {
            $assignedJobs = \DB::table('job_assign')
                ->whereIn('job_id', $jobIds)
                ->where('user_id', $user->id)
                ->pluck('job_id')
                ->toArray();
            foreach ($jobIds as $jId) {
                $confidentialAllowMap[$jId] = in_array($jId, $assignedJobs);
            }
        }

        // Attach pre-hydrated properties to each hit
        return array_map(function ($hit) use ($masterStatuses, $jobDetailsMap, $clientNamesMap, $duplicateFlagsMap, $confidentialAllowMap, $isAdmin) {
            $statusId = $hit['status'] ?? null;
            $jobId = $hit['jobId'] ?? $hit['job_id'] ?? null;
            $candKey = $hit['candidate_id'] ?? $hit['wiseId'] ?? null;

            $hit['_prehydrated_master_status_name'] = $masterStatuses[$statusId]->name ?? '';
            $hit['_prehydrated_status_subline'] = GeneralMethod::bench_status_subline($statusId)->name ?? '';
            $hit['_prehydrated_confidential_allow'] = $confidentialAllowMap[$jobId] ?? $isAdmin;

            if (isset($jobDetailsMap[$jobId])) {
                $hit['_prehydrated_job_status'] = $jobDetailsMap[$jobId];
            }

            if (isset($clientNamesMap[$jobId]) && $clientNamesMap[$jobId] !== '') {
                $hit['_prehydrated_client_name'] = $clientNamesMap[$jobId];
            }

            if (isset($duplicateFlagsMap[$candKey])) {
                $hit['_prehydrated_duplicate_flag'] = $duplicateFlagsMap[$candKey];
            }

            return $hit;
        }, $hits);
    }
}
