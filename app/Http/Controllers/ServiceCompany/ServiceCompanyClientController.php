<?php

/***########################################################
Controller    	: 	AdminClientController
Purmose			:
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021
########################################################***/

namespace App\Http\Controllers\ServiceCompany;

use App\Helper\GeneralMethod;
use App\Helper\Reply;
use App\Jobs\ProcessClientOutboxEventJob;
use App\Jobs\QueueNotificationMailService;
use App\Models\Client;
use App\Models\Company;
use App\Models\Country;
use App\Models\JobCategory;
use App\Services\ElasticService;
use App\Services\GoogleSheetsServiceGet;
use App\Services\IndustryTaxonomyService;
use App\Services\MailService;
use App\Services\MassMailService;
use App\Services\NotificationMailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class ServiceCompanyClientController extends ServiceCompanyBaseController
{
    protected $mailService;

    protected $massMailService;

    protected $notificationMailService;

    protected $elasticService;

    public function __construct(MailService $mailService, GoogleSheetsServiceGet $GoogleSheetsServiceGet, MassMailService $massMailService, NotificationMailService $notificationMailService, ElasticService $elasticService)
    {
        parent::__construct();
        $this->pageTitle = __('Clients');
        $this->pageIcon = 'icon-grid';
        $this->mailService = $mailService;
        $this->GoogleSheetsServiceGet = $GoogleSheetsServiceGet;
        $this->massMailService = $massMailService;
        $this->notificationMailService = $notificationMailService;
        $this->elasticService = $elasticService;
    }

    public function index()
    {

        $company_id = auth()->user()->company_id;
        $this->clientDataCnt = $this->commonclient();
        // Countries feed the Location filter dropdown on the Clients list (Cached 24h).
        $this->countries = cache()->remember('global_countries_list', 86400, function () {
            return DB::table('countries')->get();
        });

        $requestedTab = request('tab', 'all');
        if ($requestedTab === 'requests') {
            $requestedTab = 'request';
        }
        $this->initialTab = request('tab', 'all');
        $this->initialOpenClient = (int) request('open_client', 0);

        // ── Phase 1 God-Level Optimization: Bootstrap Page 1 data directly into view ──
        // Eliminates duplicate initial HTTP roundtrip (GET /clients -> GET /clients_ajax)
        try {
            $initialRequest = new Request([
                'tab' => $requestedTab,
                'page' => 1,
                'per_page' => 10,
                'size' => 10,
            ]);
            $initialResponse = $this->all_clients_data_ajax($initialRequest);
            $initialData = $initialResponse->getData(true);
            $this->initialHtml = $initialData['html'] ?? '';
            $this->initialTotal = $initialData['total'] ?? 0;
            $this->initialPagination = $initialData['pagination_html'] ?? ($initialData['pagination'] ?? '');
            $this->initialRows = $initialData['rows'] ?? [];
            $this->initialDataVersion = $initialData['data_version'] ?? 1;
        } catch (\Throwable $e) {
            $this->initialHtml = '';
            $this->initialTotal = 0;
            $this->initialPagination = '';
            $this->initialRows = [];
            $this->initialDataVersion = 1;
        }

        return view('service_company.client.index', $this->data);
    }

    /**
     * Backward-compatible 302 proxy for legacy Direct Clients route.
     * Preserves deep-links and $id for auto-opening client drawer.
     */
    public function direct_client($id = 0)
    {
        $params = ['tab' => 'direct'];
        if ((int) $id > 0) {
            $params['open_client'] = (int) $id;
        }

        return redirect()->route('service_company.clients.index', $params, 302);
    }

    public function all_clients_data_ajax(Request $request)
    {

        abort_if(Gate::denies('view_clients'), 403);

        $company_id = auth()->user()->company_id;
        $department = GeneralMethod::industries();
        $accountCompanyType = GeneralMethod::accountCompanyType();
        $searchTerms = request('cus_search');
        $custom_search = $searchTerms; // json_decode($searchTerms, true);

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('per_page', $request->input('size', $request->input('limit', 10)));
        if ($size <= 0) {
            $size = 10;
        }
        $from = ($page - 1) * $size;
        $sort = [];
        $customSearch = trim($custom_search);
        $shouldSearch = [];
        $sort = [];
        $valCheck = request('sortingfilter');

        $tabFilter = $request->input('tab', $request->input('type', 'all'));
        if ($tabFilter === 'requests') {
            $tabFilter = 'request';
        }

        // If tab is request, delegate to client_request_data method
        if ($tabFilter === 'request') {
            return $this->client_request_data($request);
        }

        $cachePayload = [
            'page' => $page,
            'size' => $size,
            'q' => $searchQuery,
            'country' => request('country'),
            'company' => $company_id,
            'cus_search' => $custom_search,
            'user_id' => $this->user->id,
            'valCheck' => $valCheck,
            'tab' => $tabFilter,
        ];

        $hiringFilter = (int) $request->input('hiring', 0);
        if ($tabFilter === 'hiring') {
            $hiringFilter = 1;
        }
        $cachePayload['hiring'] = $hiringFilter;

        $tenantVersion = (int) cache()->get('tenant_'.$company_id.'_client_version', 1);
        $cachePayload['version'] = $tenantVersion;
        $hash = md5(json_encode($cachePayload));
        $cacheKeySet = 'ats:v2:clients:'.$company_id.':'.$tenantVersion.':'.$hash;
        $etag = '"clients-'.$company_id.'-'.$tenantVersion.'-'.$hash.'"';

        /* ----------------------------------------------------
        ⚡ Phase 6: ETag & HTTP 304 Not Modified Optimization
        ----------------------------------------------------- */
        if ($request->header('If-None-Match') === $etag) {
            return response('', 304)->header('ETag', $etag);
        }

        /* ----------------------------
        ⚡ Check DragonflyDB first
        ----------------------------- */
        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet))->header('ETag', $etag);
        }

        /* ----------------------------------------------------
        ⚡ Phase 6: Single-Flight Request Coalescing (Anti-Stampede)
        ----------------------------------------------------- */
        $singleFlightLock = null;
        try {
            $lockKey = 'lock:'.$cacheKeySet;
            $singleFlightLock = cache()->lock($lockKey, 5);
            if (! $singleFlightLock->block(2)) {
                if (cache()->has($cacheKeySet)) {
                    return response()->json(cache()->get($cacheKeySet))->header('ETag', $etag);
                }
            } else {
                if (cache()->has($cacheKeySet)) {
                    $singleFlightLock->release();

                    return response()->json(cache()->get($cacheKeySet))->header('ETag', $etag);
                }
            }
        } catch (\Throwable $e) {
            // Safe fallback if lock is unsupported
        }

        $relationIds = $this->elasticService->getClient()->search([
            'index' => ELASTIC_CLIENT_AND_AGENCY_RELATION_INDEX,
            'body' => [
                'size' => 10000,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['to_company_id' => $company_id]],
                            ['term' => ['final_status' => 1]],
                        ],
                        'must_not' => [
                            ['term' => ['from_company_id' => $company_id]],
                        ],
                    ],
                ],
                '_source' => ['from_company_id'],
            ],
        ]);

        // Extract company IDs
        $companyIds = collect($relationIds['hits']['hits'])
            ->pluck('_source.from_company_id')
            ->unique()
            ->values()
            ->toArray();

        if (! empty($valCheck)) {
            if ($valCheck == 1) {
                $sort[] = ['created_at' => ['order' => 'desc']];
            } elseif ($valCheck == 2) {
                $sort[] = ['created_at' => ['order' => 'asc']];
            } else {
                $sort[] = ['id' => ['order' => 'desc']];
            }
        } else {
            $sort[] = ['id' => ['order' => 'desc']];
        }

        if ($tabFilter === 'internal' || $tabFilter === 'custom') {
            $must[] = [
                'bool' => [
                    'must' => [
                        ['term' => ['company_id' => $company_id]],
                        ['term' => ['self_company' => 0]],
                    ],
                ],
            ];
        } elseif ($tabFilter === 'direct') {
            $must[] = [
                'bool' => [
                    'must' => [
                        ['term' => ['self_company' => 1]],
                        ['terms' => ['company_id' => $companyIds]],
                    ],
                    'must_not' => [
                        ['term' => ['company_id' => $company_id]],
                    ],
                ],
            ];
        } else {
            $must[] = [
                'bool' => [
                    'should' => [
                        // Case 1: self_company = 0 and from_company_id match
                        [
                            'bool' => [
                                'must' => [
                                    ['term' => ['company_id' => $company_id]],
                                    ['term' => ['self_company' => 0]],
                                ],
                            ],
                        ],
                        // Case 2: self_company = 1 and nested relation matches
                        [
                            'bool' => [
                                'must' => [
                                    ['term' => ['self_company' => 1]],
                                    [
                                        'terms' => [
                                            'company_id' => $companyIds, // 🔥 from relation index
                                        ],
                                    ],
                                ],
                                'must_not' => [
                                    ['term' => ['company_id' => $company_id]],
                                ],
                            ],
                        ],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        if (! empty($customSearch)) {
            $customSearchLower = strtolower($customSearch); // wildcard is case-sensitive
            //   dd($customSearchLower);
            $shouldSearch = [
                ['match_phrase_prefix' => ['name' => $customSearch]],
                ['match_phrase_prefix' => ['td_industries.ind_name' => $customSearch]],
                ['match_phrase_prefix' => ['client_contact_info.contact_person_name' => $customSearch]],

                // Apply wildcard for nested fields
                [
                    'nested' => [
                        'path' => 'client_location', // Specify the nested path
                        'query' => [
                            'bool' => [
                                'should' => [
                                    ['wildcard' => ['client_location.city_name' => $customSearchLower.'*']],
                                    ['wildcard' => ['client_location.state_name' => $customSearchLower.'*']],
                                    ['wildcard' => ['client_location.country_name' => $customSearchLower.'*']],
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            $must[] = [
                'bool' => [
                    'should' => $shouldSearch,
                    'minimum_should_match' => 1,
                ],
            ];
        }
        // ── Hiring filter: sort internal & direct clients by active job count DESC (excluding main company) ──
        if ($hiringFilter) {
            // INTENT: Get active internal client job counts (excluding main company self_company = 1). Tenancy: company_id bound.
            $internalHiring = DB::table('jobs')
                ->join('clients', 'clients.id', '=', 'jobs.client_id')
                ->where('jobs.company_id', $company_id)
                ->where('jobs.status', 'active')
                ->where('clients.company_id', $company_id)
                ->where('clients.self_company', '!=', 1)
                ->select('jobs.client_id as id', DB::raw("'internal' as type"), DB::raw('COUNT(jobs.id) as active_count'))
                ->groupBy('jobs.client_id')
                ->get();

            // INTENT: Get active direct client job counts shared via recommendation_relation. Tenancy: to_company_id bound.
            $directHiring = DB::table('recommendation_relation')
                ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
                ->where('recommendation_relation.to_company_id', $company_id)
                ->where('recommendation_relation.from_company_id', '!=', $company_id)
                ->where('recommendation_relation.final_status', 1)
                ->where('jobs.status', 'active')
                ->select('recommendation_relation.from_company_id as id', DB::raw("'direct' as type"), DB::raw('COUNT(jobs.id) as active_count'))
                ->groupBy('recommendation_relation.from_company_id')
                ->get();

            $allHiringItems = $internalHiring->concat($directHiring)
                ->sortByDesc('active_count')
                ->values();

            if ($allHiringItems->isEmpty()) {
                $total = 0;
                $data = [];
            } else {
                $total = $allHiringItems->count();
                $pageItems = $allHiringItems->slice($from, $size)->values();

                $pageInternalClientIds = $pageItems->where('type', 'internal')->pluck('id')->toArray();
                $pageDirectCompanyIds = $pageItems->where('type', 'direct')->pluck('id')->toArray();

                $shouldClause = [];
                if (! empty($pageInternalClientIds)) {
                    $shouldClause[] = [
                        'bool' => [
                            'must' => [
                                ['term' => ['company_id' => (int) $company_id]],
                                ['term' => ['self_company' => 0]],
                                ['terms' => ['client_id' => array_map('intval', $pageInternalClientIds)]],
                            ],
                        ],
                    ];
                }
                if (! empty($pageDirectCompanyIds)) {
                    $shouldClause[] = [
                        'bool' => [
                            'must' => [
                                ['term' => ['self_company' => 1]],
                                ['terms' => ['company_id' => array_map('intval', $pageDirectCompanyIds)]],
                            ],
                            'must_not' => [
                                ['term' => ['company_id' => (int) $company_id]],
                            ],
                        ],
                    ];
                }

                $hiringMust = $must;
                if (! empty($shouldClause)) {
                    $hiringMust[] = [
                        'bool' => [
                            'should' => $shouldClause,
                            'minimum_should_match' => 1,
                        ],
                    ];
                }

                $hiringParams = [
                    'index' => ELASTIC_CLIENT_LIST,
                    'body' => [
                        'size' => $size,
                        'query' => ['bool' => ['must' => $hiringMust]],
                    ],
                ];

                $hiringResp = $this->elasticService->indexExists(ELASTIC_CLIENT_LIST)
                    ? $this->elasticService->getClient()->search($hiringParams)
                    : ['hits' => ['hits' => []]];

                $indexedHits = [];
                foreach ($hiringResp['hits']['hits'] as $hit) {
                    $src = $hit['_source'];
                    $isSelf = (int) ($src['self_company'] ?? 0);
                    $cCompId = (int) ($src['company_id'] ?? 0);
                    $cClientId = (int) ($src['client_id'] ?? 0);

                    if ($isSelf === 0 && $cCompId === (int) $company_id) {
                        $indexedHits['internal_'.$cClientId] = $src;
                    } elseif ($isSelf === 1 && $cCompId !== (int) $company_id) {
                        $indexedHits['direct_'.$cCompId] = $src;
                    }
                }

                $data = [];
                foreach ($pageItems as $pItem) {
                    $key = $pItem->type.'_'.$pItem->id;
                    if (isset($indexedHits[$key])) {
                        $data[] = $indexedHits[$key];
                    }
                }
            }
        } else {
            $params = [
                'index' => ELASTIC_CLIENT_LIST,
                'request_cache' => true,
                'body' => [
                    'from' => $from,
                    'size' => $size,
                    'sort' => $sort,
                    'query' => ['bool' => ['must' => $must]],
                ],
            ];
            if (! $this->elasticService->indexExists(ELASTIC_CLIENT_LIST)) {
                $total = 0;
                $data = [];
            } else {
                $response = $this->elasticService->getClient()->search($params);
                $total = $response['hits']['total']['value'] ?? 0;
                $data = array_map(fn ($hit) => $hit['_source'], $response['hits']['hits']);
            }
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => (int) ceil($total / max($size, 1)),
            'per_page' => $size,
            'listType' => 'allcandidate',
        ];

        // ── V2 Optimization: Batch pre-fetch relations, job counts, candidate funnels ──
        $relationMap = [];
        $jobsCountsMap = [];
        $candidateCountsMap = [];

        if (! empty($data)) {
            $pageCompanyIds = array_filter(array_unique(array_map(fn ($item) => $item['company_id'] ?? null, $data)));
            if (! empty($pageCompanyIds)) {
                $relations = DB::table('staffing_company_relation')
                    ->where('to_company_id', $company_id)
                    ->whereIn('from_company_id', $pageCompanyIds)
                    ->get();
                foreach ($relations as $rel) {
                    $relationMap[$rel->from_company_id] = $rel;
                }
            }

            foreach ($data as $rItem) {
                $rowObj = (object) $rItem;
                $cId = $rowObj->company_id ?? 0;
                $clId = $rowObj->client_id ?? 0;
                $clientsCompId = $rowObj->clients_company_id ?? 0;

                if ($company_id == $clientsCompId) {
                    $jobsCountsMap['internal_'.$clId] = GeneralMethod::InternalClientJobsCountAllStatus($company_id, $clId);
                    $candidateCountsMap['internal_'.$clId] = GeneralMethod::InternalClientCandidatesCountList($clId);
                } else {
                    $rel = $relationMap[$cId] ?? null;
                    $fromComp = $rel->from_company_id ?? null;
                    $toComp = $rel->to_company_id ?? null;
                    if ($fromComp && $toComp) {
                        $jobsCountsMap['direct_'.$fromComp.'_'.$toComp] = GeneralMethod::DirectClientJobsCountAllStatus($fromComp, $toComp);
                        $candidateCountsMap['direct_'.$fromComp.'_'.$toComp] = GeneralMethod::DirectClientCandidatesCountList($fromComp, $toComp);
                    }
                }
            }
        }

        $userPermissions = $this->userPermissions;

        // ── Phase 4 God-Level Optimization: Client DTO Assembly (zero HTML transfer) ──
        $rowsDto = [];
        foreach ($data as $rItem) {
            $rowObj = (object) $rItem;
            $cId = $rowObj->company_id ?? 0;
            $clId = $rowObj->client_id ?? ($rowObj->id ?? 0);
            $clientsCompId = $rowObj->clients_company_id ?? 0;

            if (isset($relationMap) && isset($relationMap[$cId])) {
                $relationget = $relationMap[$cId];
            } else {
                $relationget = GeneralMethod::getRelationData($company_id, $cId);
            }

            $from_company_id = $relationget->from_company_id ?? null;
            $recommendation_id = $relationget->id ?? null;
            $to_company_id = $relationget->to_company_id ?? null;
            $from_created_at = $relationget->from_created_at ?? null;

            $isDirect = ($company_id != $cId && ($recommendation_id ?? 0) > 0);
            $client_type_css = $isDirect ? 'show-detail-direct' : 'show-detail';
            $client_Or_CompanyName = ($company_id != $cId) ? ($rowObj->company_name ?? $rowObj->name ?? '') : ($rowObj->name ?? '');

            $departmentArr = ($rowObj->ind_name ?? null) ?: ($rowObj->main_ind_name ?? '');
            $rowTargetId = $isDirect ? ($rowObj->client_id ?: ($rowObj->from_company_id ?: ($rowObj->company_id ?: ($rowObj->id ?? 0)))) : ($rowObj->client_id ?: ($rowObj->id ?? 0));

            $country_name = $stateName = $city = '';
            if (! empty($rowObj->client_location) && is_array($rowObj->client_location) && count($rowObj->client_location) > 0) {
                $country_name = $rowObj->client_location[0]['country_name'] ?? '';
                $stateName = $rowObj->client_location[0]['state_name'] ?? '';
                $city = $rowObj->client_location[0]['city_name'] ?? '';
            }

            $companySizeText = '';
            $fromSize = $rowObj->company_size_from ?? null;
            $toSize = $rowObj->company_sizes ?? $rowObj->company_size ?? null;
            if (! empty($fromSize) && ! empty($toSize)) {
                $companySizeText = number_format((int) $fromSize).' - '.number_format((int) $toSize).' employees';
            } elseif (! empty($toSize)) {
                $companySizeText = is_numeric($toSize) ? number_format((int) $toSize).' employees' : $toSize.' employees';
            }

            if ($company_id == $clientsCompId) {
                $jKey = 'internal_'.$clId;
                $jobsCount = $jobsCountsMap[$jKey] ?? GeneralMethod::InternalClientJobsCountAllStatus($company_id, $clId);
                $pKey = 'internal_'.$clId;
                $perf = $candidateCountsMap[$pKey] ?? GeneralMethod::InternalClientCandidatesCountList($clId);
            } else {
                $jKey = 'direct_'.($from_company_id ?? 0).'_'.($to_company_id ?? 0);
                $jobsCount = $jobsCountsMap[$jKey] ?? GeneralMethod::DirectClientJobsCountAllStatus($from_company_id, $to_company_id);
                $pKey = 'direct_'.($from_company_id ?? 0).'_'.($to_company_id ?? 0);
                $perf = $candidateCountsMap[$pKey] ?? GeneralMethod::DirectClientCandidatesCountList($from_company_id, $to_company_id);
            }

            $job_active = (int) ($jobsCount->job_active ?? 0);
            $job_closed = (int) ($jobsCount->job_inactive ?? 0);
            $job_hold = (int) ($jobsCount->job_hold ?? 0);

            $sourced = (int) ($perf->sourced ?? 0);
            $evaluated = (int) ($perf->evaluated ?? 0);
            $interviews = (int) ($perf->all_interview ?? 0);
            $offers = (int) ($perf->offers ?? 0);
            $joined = (int) ($perf->Joined ?? 0);

            if (($recommendation_id ?? 0) > 0 && ($to_company_id ?? 0) == auth()->user()->company_id) {
                $createdTimestamp = $from_created_at ?: ($rowObj->created_at ?? null);
            } else {
                $createdTimestamp = $rowObj->created_at ?? null;
            }
            $newDate = $createdTimestamp ? date('d M, Y', strtotime($createdTimestamp)) : '-';
            $daysAgo = $createdTimestamp ? GeneralMethod::timeAgo($createdTimestamp) : '';

            $client_type = ($company_id != $cId) ? __('messages.direct_clinet_tag') : __('messages.internal_client_tag');

            $locMain = '';
            if ($city && $stateName) {
                $locMain = ucwords($city).', '.ucwords($stateName);
            } elseif ($city) {
                $locMain = ucwords($city);
            } elseif ($stateName) {
                $locMain = ucwords($stateName);
            }

            $rowsDto[] = [
                'id' => (int) $rowTargetId,
                'client_id' => (int) $clId,
                'name' => ucwords($client_Or_CompanyName),
                'industry' => (string) $departmentArr,
                'company_size' => (string) $companySizeText,
                'location_main' => (string) $locMain,
                'country' => ucwords((string) $country_name),
                'is_direct' => $isDirect,
                'client_type_css' => $client_type_css,
                'client_type' => (string) $client_type,
                'jobs' => [
                    'active' => $job_active,
                    'closed' => $job_closed,
                    'hold' => $job_hold,
                ],
                'pipeline' => [
                    'sourced' => $sourced,
                    'evaluated' => $evaluated,
                    'interviews' => $interviews,
                    'offers' => $offers,
                    'joined' => $joined,
                ],
                'connected_on' => (string) $newDate,
                'days_ago' => (string) $daysAgo,
            ];
        }

        $minVersion = (int) $request->input('min_version', 0);
        $projectionStatus = ($minVersion > $tenantVersion) ? 'syncing' : 'synced';

        $responseData = [
            'html' => view('service_company.ajax_pages.all-client-ajax', compact('data', 'userPermissions', 'company_id', 'relationMap', 'jobsCountsMap', 'candidateCountsMap'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.client-pagination', compact('pagination'))->render(),
            'rows' => $rowsDto,
            'data_version' => $tenantVersion,
            'projection_version' => $tenantVersion,
            'projection_status' => $projectionStatus,
            'etag' => $etag,
            'page' => $page,
            'per_page' => $size,
        ];

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_CLIENTS_TIME));

        if ($singleFlightLock) {
            try {
                $singleFlightLock->release();
            } catch (\Throwable $e) {
            }
        }

        return response()->json($responseData)->header('ETag', $etag);

    }

    public function client_direct_data_ajax(Request $request)
    {
        abort_if(Gate::denies('view_clients'), 403);

        $request->merge(['tab' => 'direct']);

        return $this->all_clients_data_ajax($request);
    }

    /**
     * Backward-compatible 302 proxy for legacy Custom/Internal Clients route.
     * Preserves deep-links and $id for auto-opening client drawer.
     */
    public function custom_client($id = 0)
    {
        $params = ['tab' => 'internal'];
        if ((int) $id > 0) {
            $params['open_client'] = (int) $id;
        }

        return redirect()->route('service_company.clients.index', $params, 302);
    }

    public function custom_client_data_ajax(Request $request)
    {
        abort_if(Gate::denies('view_clients'), 403);

        $request->merge(['tab' => 'internal']);

        return $this->all_clients_data_ajax($request);
    }

    /**
     * Backward-compatible 302 proxy for legacy Client Requests route.
     * Preserves deep-links and $id for auto-opening client drawer.
     */
    public function client_request($id = 0)
    {
        $params = ['tab' => 'requests'];
        if ((int) $id > 0) {
            $params['open_client'] = (int) $id;
        }

        return redirect()->route('service_company.clients.index', $params, 302);
    }

    /**
     * Client Request Data endpoint for V2 listing.
     * Fetches tenant-scoped client requests from Elasticsearch with DragonflyDB/Redis caching
     * and full-text search capability.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function client_request_data(Request $request)
    {
        abort_if(Gate::denies('view_clients'), 403);

        $company_id = auth()->user()->company_id;
        $searchQuery = trim((string) $request->input('q', $request->input('search', '')));
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10);
        $from = ($page - 1) * $size;

        $cachePayload = [
            'page' => $page,
            'size' => $size,
            'q' => $searchQuery,
            'user_id' => $this->user->id,
            'company' => $company_id,
        ];

        $tenantVersion = (int) cache()->get('tenant_'.$company_id.'_client_version', 1);
        $cachePayload['version'] = $tenantVersion;
        $hash = md5(json_encode($cachePayload));
        $cacheKeySet = 'ats:v2:clientrequest:'.$company_id.':'.$tenantVersion.':'.$hash;
        $etag = '"clientreq-'.$company_id.'-'.$tenantVersion.'-'.$hash.'"';

        /* ----------------------------------------------------
        ⚡ Phase 6: ETag & HTTP 304 Not Modified Optimization
        ----------------------------------------------------- */
        if ($request->header('If-None-Match') === $etag) {
            return response('', 304)->header('ETag', $etag);
        }

        /* ----------------------------
        ⚡ DragonflyDB / Redis Cache Hit
        ----------------------------- */
        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet))->header('ETag', $etag);
        }

        /* ----------------------------------------------------
        ⚡ Phase 6: Single-Flight Request Coalescing (Anti-Stampede)
        ----------------------------------------------------- */
        $singleFlightLock = null;
        try {
            $lockKey = 'lock:'.$cacheKeySet;
            $singleFlightLock = cache()->lock($lockKey, 5);
            if (! $singleFlightLock->block(2)) {
                if (cache()->has($cacheKeySet)) {
                    return response()->json(cache()->get($cacheKeySet))->header('ETag', $etag);
                }
            } else {
                if (cache()->has($cacheKeySet)) {
                    $singleFlightLock->release();

                    return response()->json(cache()->get($cacheKeySet))->header('ETag', $etag);
                }
            }
        } catch (\Throwable $e) {
            // Graceful fallback
        }

        // Tenancy & Status Filter Context (utilizes ES bitset cache)
        $boolQuery = [
            'bool' => [
                'filter' => [
                    ['term' => ['to_company_id' => (int) $company_id]],
                ],
                'must_not' => [
                    ['terms' => ['to_invitation_status' => [-1, 1]]],
                ],
            ],
        ];

        // Full-text search across company name, industry, and location fields
        if (! empty($searchQuery)) {
            $boolQuery['bool']['must'][] = [
                'multi_match' => [
                    'query' => $searchQuery,
                    'fields' => ['company_name^3', 'ind_name^2', 'city_name', 'country_name'],
                    'type' => 'phrase_prefix',
                ],
            ];
        }

        // Multi-level deterministic sorting
        $sort = [
            ['to_invitation_status' => ['order' => 'asc', 'unmapped_type' => 'long']],
            ['created_at' => ['order' => 'desc', 'unmapped_type' => 'date']],
            ['id' => ['order' => 'desc', 'unmapped_type' => 'long']],
        ];

        $params = [
            'index' => ELASTIC_CLIENT_REQUEST,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => $boolQuery,
                '_source' => [
                    'id', 'company_name', 'logo', 'ind_name', 'city_name', 'country_name',
                    'created_at', 'to_invitation_status', 'staffing_company_invitation_id',
                    'from_company_id', 'client_id', 'tags',
                ],
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_CLIENT_REQUEST)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => (int) ceil($total / max($size, 1)),
            'listType' => 'allcandidate',
        ];

        $rowsDto = [];
        foreach ($data as $rowArr) {
            $rowObj = (object) $rowArr;
            $created_at = ! empty($rowObj->from_created_at) ? $rowObj->from_created_at : ($rowObj->created_at ?? '');
            $newDate = ! empty($created_at) ? date('d M, Y', strtotime($created_at)) : '-';
            $daysAgo = ! empty($created_at) ? GeneralMethod::timeAgo($created_at) : '';

            $rowsDto[] = [
                'id' => (int) ($rowObj->id ?? 0),
                'client_id' => (int) ($rowObj->client_id ?? 0),
                'company_name' => ucwords($rowObj->company_name ?? ''),
                'ind_name' => (string) ($rowObj->ind_name ?? ''),
                'city_name' => (string) ($rowObj->city_name ?? ''),
                'country_name' => (string) ($rowObj->country_name ?? ''),
                'staffing_company_invitation_id' => (int) ($rowObj->staffing_company_invitation_id ?? 0),
                'from_company_id' => (int) ($rowObj->from_company_id ?? 0),
                'to_invitation_status' => (int) ($rowObj->to_invitation_status ?? 0),
                'connected_on' => (string) $newDate,
                'days_ago' => (string) $daysAgo,
            ];
        }

        $minVersion = (int) $request->input('min_version', 0);
        $projectionStatus = ($minVersion > $tenantVersion) ? 'syncing' : 'synced';

        $userPermissions = $this->userPermissions;
        $responseData = [
            'html' => view('service_company.ajax_pages.client_request_ajax', compact('data', 'userPermissions'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.client-pagination', compact('pagination'))->render(),
            'rows' => $rowsDto,
            'data_version' => $tenantVersion,
            'projection_version' => $tenantVersion,
            'projection_status' => $projectionStatus,
            'etag' => $etag,
            'page' => $page,
            'per_page' => $size,
        ];

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_CLIENT_REQUEST_TIME));

        if ($singleFlightLock) {
            try {
                $singleFlightLock->release();
            } catch (\Throwable $e) {
            }
        }

        return response()->json($responseData)->header('ETag', $etag);
    }

    /**
     * Compute client counter metrics for header tabs using Elasticsearch indices with DB fallbacks.
     * INTENT: Efficiently fetch tenant-scoped client counts (Direct, Custom/Internal, Requests, Total) via Elasticsearch.
     * TENANCY: Enforces company_id and to_company_id scoping.
     *
     * @return array
     */
    public function commonclient()
    {
        $company_id = auth()->user()->company_id;
        $clientCounter = [];

        // Direct Client Count via Elasticsearch (tenant-scoped by to_company_id and self_company = 1)
        $clientDirect = 0;
        $directCompanyIds = [];
        if ($this->elasticService->indexExists(ELASTIC_CLIENT_AND_AGENCY_RELATION_INDEX)) {
            try {
                $relResponse = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_CLIENT_AND_AGENCY_RELATION_INDEX,
                    'body' => [
                        'size' => 10000,
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['to_company_id' => (int) $company_id]],
                                    ['term' => ['final_status' => 1]],
                                ],
                                'must_not' => [
                                    ['term' => ['from_company_id' => (int) $company_id]],
                                ],
                            ],
                        ],
                        '_source' => ['from_company_id'],
                    ],
                ]);
                $directCompanyIds = collect($relResponse['hits']['hits'])
                    ->pluck('_source.from_company_id')
                    ->unique()
                    ->values()
                    ->toArray();
            } catch (\Exception $e) {
                $directCompanyIds = DB::table('staffing_company_relation')
                    ->where('staffing_company_relation.to_company_id', '=', $company_id)
                    ->where('staffing_company_relation.from_company_id', '!=', $company_id)
                    ->where('staffing_company_relation.final_status', '=', 1)
                    ->distinct()
                    ->pluck('from_company_id')
                    ->toArray();
            }
        } else {
            $directCompanyIds = DB::table('staffing_company_relation')
                ->where('staffing_company_relation.to_company_id', '=', $company_id)
                ->where('staffing_company_relation.from_company_id', '!=', $company_id)
                ->where('staffing_company_relation.final_status', '=', 1)
                ->distinct()
                ->pluck('from_company_id')
                ->toArray();
        }

        if (! empty($directCompanyIds) && $this->elasticService->indexExists(ELASTIC_CLIENT_LIST)) {
            try {
                $response = $this->elasticService->getClient()->count([
                    'index' => ELASTIC_CLIENT_LIST,
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['self_company' => 1]],
                                    ['terms' => ['company_id' => array_map('intval', $directCompanyIds)]],
                                ],
                                'must_not' => [
                                    ['term' => ['company_id' => (int) $company_id]],
                                ],
                            ],
                        ],
                    ],
                ]);
                $clientDirect = $response['count'] ?? 0;
            } catch (\Exception $e) {
                $clientDirect = Client::where('self_company', 1)
                    ->whereIn('company_id', $directCompanyIds)
                    ->where('company_id', '!=', $company_id)
                    ->count();
            }
        } elseif (! empty($directCompanyIds)) {
            $clientDirect = Client::where('self_company', 1)
                ->whereIn('company_id', $directCompanyIds)
                ->where('company_id', '!=', $company_id)
                ->count();
        } else {
            $clientDirect = 0;
        }
        $clientCounter['clientDirect'] = $clientDirect;

        // Custom / Internal Client Count via Elasticsearch (tenant-scoped by company_id)
        $clientCustom = 0;
        if ($this->elasticService->indexExists(ELASTIC_CLIENT_LIST)) {
            try {
                $response = $this->elasticService->getClient()->count([
                    'index' => ELASTIC_CLIENT_LIST,
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['company_id' => (int) $company_id]],
                                ],
                                'must_not' => [
                                    ['term' => ['self_company' => 1]],
                                ],
                            ],
                        ],
                    ],
                ]);
                $clientCustom = $response['count'] ?? 0;
            } catch (\Exception $e) {
                $clientCustom = Client::where('clients.self_company', '!=', 1)
                    ->where('clients.company_id', '=', $company_id)
                    ->count();
            }
        } else {
            $clientCustom = Client::where('clients.self_company', '!=', 1)
                ->where('clients.company_id', '=', $company_id)
                ->count();
        }
        $clientCounter['clientCustom'] = $clientCustom;

        // Client Requests Count via Elasticsearch (tenant-scoped by to_company_id)
        $boolQuery = [
            'bool' => [
                'filter' => [
                    ['term' => ['to_company_id' => (int) $company_id]],
                ],
                'must_not' => [
                    ['terms' => ['to_invitation_status' => [-1, 1]]],
                ],
            ],
        ];

        $params = [
            'index' => ELASTIC_CLIENT_REQUEST,
            'body' => [
                'query' => $boolQuery,
            ],
        ];

        $count = 0;
        if ($this->elasticService->indexExists(ELASTIC_CLIENT_REQUEST)) {
            try {
                $response = $this->elasticService->getClient()->count($params);
                $count = $response['count'] ?? 0;
            } catch (\Exception $e) {
                $count = 0;
            }
        }

        $clientCounter['clientRequest'] = $count;
        $clientCounter['clientAll'] = $clientCustom + $clientDirect;

        // INTENT: Clients Actively Hiring Count (unique internal clients + direct clients with active jobs, excluding main company self_company = 1). Tenancy: scoped by company_id / to_company_id.
        $internalHiringCount = DB::table('jobs')
            ->join('clients', 'clients.id', '=', 'jobs.client_id')
            ->where('jobs.company_id', $company_id)
            ->where('jobs.status', 'active')
            ->where('clients.company_id', $company_id)
            ->where('clients.self_company', '!=', 1)
            ->distinct()
            ->count('clients.id');

        $directHiringCount = DB::table('recommendation_relation')
            ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
            ->where('recommendation_relation.to_company_id', $company_id)
            ->where('recommendation_relation.from_company_id', '!=', $company_id)
            ->where('recommendation_relation.final_status', 1)
            ->where('jobs.status', 'active')
            ->distinct()
            ->count('recommendation_relation.from_company_id');

        $clientCounter['clientHiring'] = $internalHiringCount + $directHiringCount;

        // INTENT: Total Active Jobs Count across internal and direct clients. Tenancy: scoped by company_id / to_company_id.
        $internalActiveJobs = DB::table('jobs')
            ->join('clients', 'clients.id', '=', 'jobs.client_id')
            ->where('jobs.company_id', $company_id)
            ->where('jobs.status', 'active')
            ->where('clients.company_id', $company_id)
            ->where('clients.self_company', '!=', 1)
            ->count('jobs.id');

        $directActiveJobs = DB::table('recommendation_relation')
            ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
            ->where('recommendation_relation.to_company_id', $company_id)
            ->where('recommendation_relation.from_company_id', '!=', $company_id)
            ->where('recommendation_relation.final_status', 1)
            ->where('jobs.status', 'active')
            ->count('jobs.id');

        $clientCounter['activeJobs'] = $internalActiveJobs + $directActiveJobs;

        return $clientCounter;
    }

    public function viewMore($id)
    {
        $startTime = microtime(true);
        abort_if(Gate::denies('view_clients'), 403);

        $userId = auth()->id() ?? ($this->user->id ?? 0);
        $cachePayload = [
            'client_id' => $id,
            'user_id' => $userId,
        ];

        $versionKey = 'clientsviewmore_version_user_'.$userId;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:clientsviewmore:'.$userId.':'.$version.':'.md5(json_encode($cachePayload));
        /* ----------------------------
        ⚡ Check DragonflyDB first
        ----------------------------- */
        if (cache()->has($cacheKeySet)) {
            \Log::info('CLIENT_DETAILS_APM: viewMore cache hit', [
                'client_id' => $id,
                'user_id' => $userId,
                'latency_ms' => round((microtime(true) - $startTime) * 1000, 2),
            ]);

            return response()->json(cache()->get($cacheKeySet));
        }

        // ── Phase 3 God-Level Optimization: Cache static lookups in Redis (0 MySQL reads) ──
        $this->countries = cache()->remember('global_countries_list', 86400, function () {
            return DB::table('countries')->get();
        });
        $this->categories = cache()->remember('global_job_categories_all', 86400, function () {
            return DB::table('job_categories')->get();
        });
        $this->companyType = GeneralMethod::companyType();
        $this->department = cache()->remember('global_td_industries_all', 86400, function () {
            return DB::table('td_industries')
                ->select('td_industries.ind_id', 'td_industries.ind_name')
                ->orderBy('td_industries.ind_name', 'ASC')
                ->get();
        });
        $this->countryArr = cache()->remember('global_country_arr_list', 86400, function () {
            return GeneralMethod::getAllCountryList();
        });
        $this->statelist = cache()->remember('global_state_list_101', 86400, function () {
            return GeneralMethod::statelist(101);
        });

        // $this->client = Client::find($id);
        if ($id) {
            $params = [
                'index' => ELASTIC_CLIENT_LIST,
                'id' => $id,
            ];

            $response = $this->elasticService->getClient()->get($params);
            $this->client = (object) $response['_source'];
        }

        // dd($this->client);
        $this->client_file = DB::table('client_file')->select('*')->where('client_id', $id)->get();
        // $this->countries 		= Country::all();

        $this->client_location = DB::table('client_location')
            // ->join('countries', 'countries.id', '=', 'client_location.country_id')
            ->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
            ->where('client_location.client_id', $id)
            ->orderBy('client_location.id', 'DESC')
            ->first();

        $this->city_name = '';
        if (@$this->client_location->country_id == 101) {
            $this->city_obj = DB::table('city_list')->select('city_list.city')->where('city_list.city_id', $this->client_location->city_id)->first();
            $this->city_name = ucwords(@$this->city_obj->city);
        } else {
            $city = @$this->client_location->city;
            $this->city_name = ucwords(@$city);
        }

        $this->country_name = '';
        if (@$this->client_location->country_id > 0) {
            $country_name = GeneralMethod::getCountryName($this->client_location->country_id);
            if (count($country_name) == 0) {
                $this->country_name = '';
            } else {
                foreach ($country_name as $cval) {
                    $this->country_name = ucwords($cval['name']);
                }
            }
        }

        $this->client_contact_info = DB::table('client_contact_info')->select('*')->where('client_id', $id)->get();
        $this->active_requisition = DB::table('jobs')->select('jobs.id')->where('jobs.client_id', $id)->where('jobs.status', 'active')->count();

        $startOfMonth = Carbon::now()->startOfMonth()->subMonths(3);
        $endOfMonth = Carbon::now()->endOfMonth();
        $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(6);
        $endOfMonthcomman = Carbon::now()->endOfMonth();

        // Initialize default empty chart state for lazy loading via dashboardAnalytics endpoint
        $this->result = collect();
        $this->joined_count = 0;
        $this->sourcing_count = 0;
        $this->screening_count = 0;
        $this->evaluation_count = 0;
        $this->negotiation_count = 0;
        $this->offer_count = 0;
        $this->Rejected_count = 0;
        $this->inreview_count = 0;
        $this->total_candidates = 0;
        $this->counts = collect();
        $this->labels = [];
        $this->candidateValues = [];
        $this->candidateValuesTotal = 0;
        $this->labelsCommoffer = [];
        $this->candidateValuesCommoffer = [];
        $this->candidateValuesTotaloffer = 0;
        $this->labelsCommjoin = [];
        $this->candidateValuesCommjoin = [];
        $this->candidateValuesTotaljoin = 0;
        $this->upcomingData = [];
        $this->recentData = [];
        $this->interviewlabels = [];
        $this->interviewValues = [];
        $this->interviewValuesTotal = 0;
        $this->recentDataMonth = [];
        $this->interviewlabelsMonth = [];
        $this->interviewValuesMonth = [];
        $this->interviewValuesTotalMonth = 0;

        $this->startOfMonth = $startOfMonth;
        $this->endOfMonth = $endOfMonth;

        /** Render instant lightweight Client Details shell view (< 60ms) */
        $view = view('service_company.client.show', $this->data)->render();
        $responseData = Reply::dataOnly(['status' => 'success', 'view' => $view]);

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(CLIENTS_VIEWMORE_TIME));

        \Log::info('CLIENT_DETAILS_APM: viewMore shell load completed', [
            'client_id' => $id,
            'user_id' => $userId,
            'latency_ms' => round((microtime(true) - $startTime) * 1000, 2),
        ]);

        return response()->json($responseData);

    }

    /**
     * Lazy-loaded Client Dashboard analytics API endpoint.
     * Route: service_company.clients.dashboard-analytics
     *
     * @param  int|string  $id  Client ID
     * @return \Illuminate\Http\JsonResponse
     *
     * @sideeffect Executes ES aggregation searches over CANDIDATES, JOBS, and INTERVIEW_TRACKER indices.
     *             Results cached in Redis for 30 minutes to prevent thundering herd.
     */
    public function dashboardAnalytics($id)
    {
        $startTime = microtime(true);
        abort_if(Gate::denies('view_clients'), 403);
        $company_id = auth()->user()->company_id;

        $cacheKey = "ats:client_dashboard_v7:{$company_id}:{$id}";
        if (cache()->has($cacheKey)) {
            \Log::info('CLIENT_DETAILS_APM: dashboardAnalytics cache hit', [
                'client_id' => $id,
                'company_id' => $company_id,
                'latency_ms' => round((microtime(true) - $startTime) * 1000, 2),
            ]);

            return response()->json(cache()->get($cacheKey));
        }

        $startOfMonth = Carbon::now()->startOfMonth()->subMonths(3);
        $endOfMonth = Carbon::now()->endOfMonth();
        $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(6);
        $endOfMonthcomman = Carbon::now()->endOfMonth();

        $isInternalClient = DB::table('clients')->where('id', $id)->where('company_id', $company_id)->where(function ($q) {
            $q->whereNull('self_company')->orWhere('self_company', 0);
        })->exists();

        if ($isInternalClient) {
            $candMust = [
                [
                    'bool' => [
                        'should' => [
                            ['term' => ['client_id' => (int) $id]],
                            ['term' => ['client_id' => (string) $id]],
                            ['term' => ['from_company_id' => (int) $id]],
                            ['term' => ['from_company_id' => (string) $id]],
                            ['term' => ['client_company_id' => (int) $id]],
                            ['term' => ['client_company_id' => (string) $id]],
                            ['term' => ['client_comapny_id' => (int) $id]],
                            ['term' => ['client_comapny_id' => (string) $id]],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ],
            ];
            $candMustNot = [
                ['terms' => ['job_master_status_id' => [9, '9']]],
            ];
        } else {
            // Direct Client: STRICTLY shared data only (from_company_id = $id, to_company_id = $company_id, ClientComID = $company_id, final_status = 1)
            $candMust = [
                [
                    'bool' => [
                        'should' => [
                            ['term' => ['from_company_id' => (int) $id]],
                            ['term' => ['from_company_id' => (string) $id]],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ],
                [
                    'bool' => [
                        'should' => [
                            ['term' => ['to_company_id' => (int) $company_id]],
                            ['term' => ['to_company_id' => (string) $company_id]],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ],
                [
                    'bool' => [
                        'should' => [
                            ['term' => ['ClientComID' => (int) $company_id]],
                            ['term' => ['ClientComID' => (string) $company_id]],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ],
                [
                    'bool' => [
                        'should' => [
                            ['term' => ['final_status' => 1]],
                            ['term' => ['final_status' => '1']],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ],
            ];
            $candMustNot = [
                ['terms' => ['job_master_status_id' => [9, '9']]],
            ];
        }

        // 1. Candidate status count graph (ES_CANDIDATES)
        $boolQuery = [
            'bool' => [
                'must' => $candMust,
                'must_not' => $candMustNot,
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
                    'status_counts' => ['terms' => ['field' => 'job_master_status_id', 'size' => 10]],
                    'offer_candidates' => [
                        'filter' => [
                            'bool' => [
                                'should' => [
                                    ['term' => ['job_master_status_id' => 5]],
                                    ['term' => ['job_master_status_id' => '5']],
                                    ['exists' => ['field' => 'of_created_date']],
                                ],
                                'minimum_should_match' => 1,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $result = collect();
        if ($this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            $response = $this->elasticService->getClient()->search($params);
            $bucketData = $response['aggregations']['status_counts']['buckets'] ?? [];
            $result = collect($bucketData)->mapWithKeys(fn ($item) => [(string) $item['key'] => $item['doc_count']]);
        }

        $joined_count = $result->get('6', 0) ?? $result->get(6, 0);
        $sourcing_count = $result->get('1', 0) ?? $result->get(1, 0);
        $screening_count = $result->get('2', 0) ?? $result->get(2, 0);
        $evaluation_count = $result->get('3', 0) ?? $result->get(3, 0);
        $negotiation_count = $result->get('4', 0) ?? $result->get(4, 0);
        $offer_count = $result->get('5', 0) ?? $result->get(5, 0);
        $Rejected_count = $result->get('7', 0) ?? $result->get(7, 0);
        $inreview_count = $result->get('8', 0) ?? $result->get(8, 0);
        $total_candidates = $response['hits']['total']['value'] ?? ($inreview_count + $sourcing_count + $screening_count + $evaluation_count + $negotiation_count + $offer_count + $joined_count + $Rejected_count);

        // 2. Jobs status counts graph (ES_JOBS)
        if ($isInternalClient) {
            $paramsJobs = [
                'index' => ELASTIC_JOBS,
                'request_cache' => true,
                'body' => [
                    'track_total_hits' => true,
                    'size' => 0,
                    'query' => [
                        'bool' => [
                            'must' => [
                                [
                                    'bool' => [
                                        'should' => [
                                            ['term' => ['comId' => (int) $company_id]],
                                            ['term' => ['comId' => (string) $company_id]],
                                            ['term' => ['company_id' => (int) $company_id]],
                                            ['term' => ['company_id' => (string) $company_id]],
                                        ],
                                        'minimum_should_match' => 1,
                                    ],
                                ],
                                [
                                    'bool' => [
                                        'should' => [
                                            ['term' => ['client_id' => (int) $id]],
                                            ['term' => ['client_id' => (string) $id]],
                                        ],
                                        'minimum_should_match' => 1,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'aggs' => ['status_counts' => ['terms' => ['field' => 'status', 'size' => 10]]],
                ],
            ];
        } else {
            $paramsJobs = [
                'index' => ELASTIC_JOBS,
                'request_cache' => true,
                'body' => [
                    'track_total_hits' => true,
                    'size' => 0,
                    'query' => [
                        'nested' => [
                            'path' => 'recommendation_relation',
                            'query' => [
                                'bool' => [
                                    'must' => [
                                        ['term' => ['recommendation_relation.to_company_id' => (int) $company_id]],
                                        ['term' => ['recommendation_relation.from_company_id' => (int) $id]],
                                        ['term' => ['recommendation_relation.final_status' => (int) 1]],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'aggs' => ['status_counts' => ['terms' => ['field' => 'status', 'size' => 10]]],
                ],
            ];
        }
        $counts = collect();
        if ($this->elasticService->indexExists(ELASTIC_JOBS)) {
            $responseJobs = $this->elasticService->getClient()->search($paramsJobs);
            $bucketJobs = $responseJobs['aggregations']['status_counts']['buckets'] ?? [];
            $counts = collect($bucketJobs)->mapWithKeys(fn ($item) => [$item['key'] => $item['doc_count']]);
        }

        // 3. Candidate last 7 days histogram
        $endDate7Days = Carbon::today()->endOfDay();
        $startDate7Days = Carbon::today()->subDays(6)->startOfDay();
        $paramsCandidates7Days = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => $candMust,
                        'must_not' => $candMustNot,
                        'filter' => [['range' => ['created_at' => ['gte' => $startDate7Days->format('Y-m-d 00:00:00'), 'lte' => $endDate7Days->format('Y-m-d 23:59:59'), 'format' => 'yyyy-MM-dd HH:mm:ss||strict_date_optional_time||yyyy-MM-dd']]]],
                    ],
                ],
                'aggs' => [
                    'uploads_over_time' => [
                        'date_histogram' => [
                            'field' => 'created_at',
                            'calendar_interval' => 'day',
                            'format' => 'yyyy-MM-dd',
                            'min_doc_count' => 0,
                        ],
                    ],
                ],
            ],
        ];
        $labels7Days = [];
        $candidateValues7Days = [];
        if ($this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            $resp7 = $this->elasticService->getClient()->search($paramsCandidates7Days);
            $buckets7 = $resp7['aggregations']['uploads_over_time']['buckets'] ?? [];
            for ($i = 0; $i < 7; $i++) {
                $d = $startDate7Days->copy()->addDays($i);
                $labels7Days[] = $d->format('M d');
                $b = collect($buckets7)->firstWhere('key_as_string', $d->format('Y-m-d'));
                $candidateValues7Days[] = $b['doc_count'] ?? 0;
            }
        }

        // 4. Offer count graph (6 months)
        $labelsOffer = [];
        $candidateValuesOffer = [];
        $startDateComm = Carbon::now();
        for ($i = 5; $i >= 0; $i--) {
            $startDate = $startDateComm->copy()->startOfMonth()->subMonths($i);
            $labelsOffer[] = $startDate->format('M Y');
            $candidateValuesOffer[$startDate->format('M Y')] = 0;
        }

        if ($this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            $startDate6M = $startDateComm->copy()->startOfMonth()->subMonths(5)->startOfDay();
            $endDate6M = $startDateComm->copy()->endOfMonth();
            $paramsOffers = [
                'index' => ELASTIC_CANDIDATES,
                'request_cache' => true,
                'body' => [
                    'track_total_hits' => true,
                    'size' => 0,
                    'query' => [
                        'bool' => [
                            'must' => $candMust,
                            'must_not' => $candMustNot,
                            'should' => [
                                ['term' => ['job_master_status_id' => 5]],
                                ['term' => ['job_master_status_id' => '5']],
                                ['exists' => ['field' => 'of_created_date']],
                            ],
                            'minimum_should_match' => 1,
                            'filter' => [['range' => ['created_at' => ['gte' => $startDate6M->format('Y-m-d 00:00:00'), 'lte' => $endDate6M->format('Y-m-d 23:59:59'), 'format' => 'yyyy-MM-dd HH:mm:ss||strict_date_optional_time||yyyy-MM-dd']]]],
                        ],
                    ],
                    'aggs' => [
                        'offers_over_time' => [
                            'date_histogram' => [
                                'field' => 'created_at',
                                'calendar_interval' => 'month',
                                'format' => 'yyyy-MM',
                                'min_doc_count' => 0,
                            ],
                        ],
                    ],
                ],
            ];
            $respOffers = $this->elasticService->getClient()->search($paramsOffers);
            $bucketsOffers = $respOffers['aggregations']['offers_over_time']['buckets'] ?? [];
            foreach ($bucketsOffers as $b) {
                $monthLabel = Carbon::parse($b['key_as_string'])->format('M Y');
                if (isset($candidateValuesOffer[$monthLabel])) {
                    $candidateValuesOffer[$monthLabel] = $b['doc_count'] ?? 0;
                }
            }
        }
        $candidateValuesOfferList = array_values($candidateValuesOffer);

        // 5. Candidate joining list graph (6 months)
        $labelsJoin = [];
        $candidateValuesJoin = [];
        for ($i = 5; $i >= 0; $i--) {
            $startDate = $startDateComm->copy()->startOfMonth()->subMonths($i);
            $labelsJoin[] = $startDate->format('M Y');
            $candidateValuesJoin[$startDate->format('M Y')] = 0;
        }

        if ($this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            $startDate6M = $startDateComm->copy()->startOfMonth()->subMonths(5)->startOfDay();
            $endDate6M = $startDateComm->copy()->endOfMonth();
            $paramsJoins = [
                'index' => ELASTIC_CANDIDATES,
                'request_cache' => true,
                'body' => [
                    'track_total_hits' => true,
                    'size' => 0,
                    'query' => [
                        'bool' => [
                            'must' => array_merge($candMust, [
                                ['terms' => ['job_master_status_id' => [6, '6']]],
                            ]),
                            'must_not' => $candMustNot,
                            'filter' => [['range' => ['created_at' => ['gte' => $startDate6M->format('Y-m-d 00:00:00'), 'lte' => $endDate6M->format('Y-m-d 23:59:59'), 'format' => 'yyyy-MM-dd HH:mm:ss||strict_date_optional_time||yyyy-MM-dd']]]],
                        ],
                    ],
                    'aggs' => [
                        'joins_over_time' => [
                            'date_histogram' => [
                                'field' => 'created_at',
                                'calendar_interval' => 'month',
                                'format' => 'yyyy-MM',
                                'min_doc_count' => 0,
                            ],
                        ],
                    ],
                ],
            ];
            $respJoins = $this->elasticService->getClient()->search($paramsJoins);
            $bucketsJoins = $respJoins['aggregations']['joins_over_time']['buckets'] ?? [];
            foreach ($bucketsJoins as $b) {
                $monthLabel = Carbon::parse($b['key_as_string'])->format('M Y');
                if (isset($candidateValuesJoin[$monthLabel])) {
                    $candidateValuesJoin[$monthLabel] = $b['doc_count'] ?? 0;
                }
            }
        }
        $candidateValuesJoinList = array_values($candidateValuesJoin);

        // 6. Upcoming Interviews & Recent Interviews (shared interviews only)
        $upcomingData = [];
        $interviewlabels = [];
        $interviewValues = [];
        $interviewlabelsMonth = [];
        $interviewValuesMonth = [];

        if ($this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
            if ($isInternalClient) {
                $interviewMust = [
                    [
                        'bool' => [
                            'should' => [
                                ['term' => ['client_id' => (int) $id]],
                                ['term' => ['from_company_id' => (int) $id]],
                                ['term' => ['client_company_id' => (int) $id]],
                                ['term' => ['client_comapny_id' => (int) $id]],
                            ],
                            'minimum_should_match' => 1,
                        ],
                    ],
                ];
            } else {
                $interviewMust = [
                    [
                        'bool' => [
                            'should' => [
                                ['term' => ['from_company_id' => (int) $id]],
                                ['term' => ['from_company_id' => (string) $id]],
                                ['term' => ['client_id' => (int) $id]],
                                ['term' => ['client_id' => (string) $id]],
                            ],
                            'minimum_should_match' => 1,
                        ],
                    ],
                    [
                        'bool' => [
                            'should' => [
                                ['term' => ['to_company_id' => (int) $company_id]],
                                ['term' => ['to_company_id' => (string) $company_id]],
                            ],
                            'minimum_should_match' => 1,
                        ],
                    ],
                    [
                        'bool' => [
                            'should' => [
                                ['term' => ['ClientComID' => (int) $company_id]],
                                ['term' => ['ClientComID' => (string) $company_id]],
                            ],
                            'minimum_should_match' => 1,
                        ],
                    ],
                ];
            }

            $nowMinus1Hour = now()->subHour()->toIso8601String();
            $upcomingParams = [
                'index' => ELASTIC_INERTVIEW_TRACKER,
                'request_cache' => true,
                'body' => [
                    'from' => 0,
                    'size' => 5,
                    'sort' => [['schedule_date' => ['order' => 'asc']]],
                    'query' => [
                        'bool' => [
                            'must' => $interviewMust,
                            'filter' => [['range' => ['schedule_date' => ['gte' => $nowMinus1Hour, 'format' => 'strict_date_optional_time']]]],
                        ],
                    ],
                ],
            ];
            $respUp = $this->elasticService->getClient()->search($upcomingParams);
            $upcomingData = array_map(fn ($hit) => $hit['_source'], $respUp['hits']['hits'] ?? []);

            $paramsInterview7 = [
                'index' => ELASTIC_INERTVIEW_TRACKER,
                'request_cache' => true,
                'body' => [
                    'track_total_hits' => true,
                    'size' => 0,
                    'query' => [
                        'bool' => [
                            'must' => $interviewMust,
                            'filter' => [['range' => ['schedule_date' => ['gte' => $startDate7Days->toIso8601String()]]]],
                        ],
                    ],
                    'aggs' => [
                        'interviews_over_time' => [
                            'date_histogram' => [
                                'field' => 'schedule_date',
                                'calendar_interval' => 'day',
                                'format' => 'yyyy-MM-dd',
                                'min_doc_count' => 0,
                            ],
                        ],
                    ],
                ],
            ];
            $respInt7 = $this->elasticService->getClient()->search($paramsInterview7);
            $bucketsInt7 = $respInt7['aggregations']['interviews_over_time']['buckets'] ?? [];

            $interviewMap = [];
            for ($i = 0; $i < 7; $i++) {
                $d = $startDate7Days->copy()->addDays($i);
                $interviewlabels[] = $d->format('M d');
                $interviewMap[$d->format('Y-m-d')] = 0;
            }
            foreach ($bucketsInt7 as $b) {
                $dStr = substr($b['key_as_string'] ?? '', 0, 10);
                if (isset($interviewMap[$dStr])) {
                    $interviewMap[$dStr] = $b['doc_count'] ?? 0;
                }
            }
            $interviewValues = array_values($interviewMap);

            $paramsInterview6M = [
                'index' => ELASTIC_INERTVIEW_TRACKER,
                'request_cache' => true,
                'body' => [
                    'track_total_hits' => true,
                    'size' => 0,
                    'query' => [
                        'bool' => [
                            'must' => $interviewMust,
                            'filter' => [['range' => ['schedule_date' => ['gte' => $startDate6M->toIso8601String()]]]],
                        ],
                    ],
                    'aggs' => [
                        'interviews_over_time' => [
                            'date_histogram' => [
                                'field' => 'schedule_date',
                                'calendar_interval' => 'month',
                                'format' => 'yyyy-MM',
                                'min_doc_count' => 0,
                            ],
                        ],
                    ],
                ],
            ];
            $respInt6M = $this->elasticService->getClient()->search($paramsInterview6M);
            $bucketsInt6M = $respInt6M['aggregations']['interviews_over_time']['buckets'] ?? [];

            $interviewMonthMap = [];
            for ($i = 5; $i >= 0; $i--) {
                $d = $startDateComm->copy()->startOfMonth()->subMonths($i);
                $interviewlabelsMonth[] = $d->format('M Y');
                $interviewMonthMap[$d->format('M Y')] = 0;
            }
            foreach ($bucketsInt6M as $b) {
                $mStr = Carbon::parse($b['key_as_string'])->format('M Y');
                if (isset($interviewMonthMap[$mStr])) {
                    $interviewMonthMap[$mStr] = $b['doc_count'] ?? 0;
                }
            }
            $interviewValuesMonth = array_values($interviewMonthMap);
        }

        $responseData = Reply::dataOnly([
            'status' => 'success',
            'data' => [
                'total_candidates' => $total_candidates,
                'counts' => $counts->toArray(),
                'inreview_count' => $inreview_count,
                'sourcing_count' => $sourcing_count,
                'screening_count' => $screening_count,
                'evaluation_count' => $evaluation_count,
                'negotiation_count' => $negotiation_count,
                'offer_count' => $offer_count,
                'joined_count' => $joined_count,
                'Rejected_count' => $Rejected_count,
                'candidateValuesTotal' => array_sum($candidateValues7Days),
                'labels' => $labels7Days,
                'candidateValues' => $candidateValues7Days,
                'labelsCommoffer' => $labelsOffer,
                'candidateValuesCommoffer' => $candidateValuesOfferList,
                'candidateValuesTotaloffer' => array_sum($candidateValuesOfferList),
                'labelsCommjoin' => $labelsJoin,
                'candidateValuesCommjoin' => $candidateValuesJoinList,
                'candidateValuesTotaljoin' => array_sum($candidateValuesJoinList),
                'upcomingData' => $upcomingData,
                'interviewlabels' => $interviewlabels,
                'interviewValues' => $interviewValues,
                'interviewlabelsMonth' => $interviewlabelsMonth,
                'interviewValuesMonth' => $interviewValuesMonth,
            ],
        ]);

        cache()->put($cacheKey, $responseData, now()->addMinutes(30));

        \Log::info('CLIENT_DETAILS_APM: dashboardAnalytics completed', [
            'client_id' => $id,
            'company_id' => $company_id,
            'latency_ms' => round((microtime(true) - $startTime) * 1000, 2),
        ]);

        return response()->json($responseData);
    }

    public function contactdata(Request $request)
    {
        $startTime = microtime(true);
        abort_if(Gate::denies('view_clients'), 403);

        $clientId = (int) $request->input('clientId');
        $companyId = (int) auth()->user()->company_id;

        $schedule = DB::table('client_contact_info')
            ->select('id', 'contact_person_name', 'calling_code', 'contact_person_email', 'contact_person_number', 'contact_person_designation')
            ->where('client_id', $clientId);

        // Avatar colour palette
        $avatarColors = ['#c084fc', '#60a5fa', '#fbbf24', '#34d399', '#f472b6', '#38bdf8', '#fb923c'];

        $response = DataTables::of($schedule)
            ->editColumn('chkbox', function ($row) {
                return '<input type="checkbox" class="table-checkbox" />';
            })
            ->editColumn('Contact', function ($row) use ($avatarColors) {
                $name = trim((string) @$row->contact_person_name);
                $initials = strtoupper(implode('', array_map(fn ($w) => mb_substr($w, 0, 1),
                    array_slice(array_filter(explode(' ', $name)), 0, 2))));
                $color = $avatarColors[(ord(strtolower(substr($name, 0, 1) ?: 'a'))) % count($avatarColors)];

                return '<div class="contact-cell">
							<div class="contact-avatar-table" style="background:'.$color.'">'.$initials.'</div>
							<div class="contact-info-table">
								<div class="contact-name-table">'.ucwords($name).'</div>
							</div>
						</div>';
            })
            ->editColumn('Designation', function ($row) {
                return ucwords((string) @$row->contact_person_designation);
            })
            ->editColumn('Email', function ($row) {
                return '<span class="email-cell">'.@$row->contact_person_email.'</span>';
            })
            ->editColumn('Phone', function ($row) {
                return GeneralMethod::formatContactPhoneHtml($row->calling_code, $row->contact_person_number);
            })
            ->editColumn('Status', function ($row) {
                return '<span class="status-badge-table active"><i class="fas fa-circle"></i>Active</span>';
            })
            ->editColumn('Actions', function ($row) {
                $call = GeneralMethod::formatContactPhoneForTel($row->calling_code, $row->contact_person_number);

                return '<div class="action-btns">
							<a href="tel:'.$call.'" class="action-btn-table" title="Call"><i class="fas fa-phone"></i></a>
							<a href="mailto:'.$row->contact_person_email.'" class="action-btn-table" title="Email"><i class="fas fa-envelope"></i></a>
						</div>';
            })
            ->rawColumns(['chkbox', 'Contact', 'Email', 'Phone', 'Status', 'Actions'])
            ->addIndexColumn()->make(true);

        \Log::info('CLIENT_DETAILS_APM: contactdata DataTables completed', [
            'client_id' => $clientId,
            'company_id' => $companyId,
            'latency_ms' => round((microtime(true) - $startTime) * 1000, 2),
        ]);

        return $response;

    }

    public function dviewMore($id)
    {
        abort_if(Gate::denies('view_clients'), 403);
        $company_id = auth()->user()->company_id;

        $userId = auth()->id() ?? ($this->user->id ?? 0);
        $cachePayload = [
            'client_id' => $id,
            'user_id' => $userId,
        ];

        $versionKey = 'clientsdviewmore_version_user_'.$userId;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:clientsdviewmore_v4:'.$userId.':'.$version.':'.md5(json_encode($cachePayload));
        /* ----------------------------
        ⚡ Check DragonflyDB first
        ----------------------------- */
        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet));
        }

        // ── Phase 3 God-Level Optimization: Cache static lookups in Redis (0 MySQL reads) ──
        $this->countries = cache()->remember('global_countries_list', 86400, function () {
            return DB::table('countries')->get();
        });
        $this->categories = cache()->remember('global_job_categories_all', 86400, function () {
            return DB::table('job_categories')->get();
        });
        $this->companyType = GeneralMethod::industries_type_arr(); // GeneralMethod::accountCompanyType();
        $this->department = cache()->remember('global_departments_all', 86400, function () {
            return GeneralMethod::department();
        });
        $this->industries = cache()->remember('global_td_industries_all', 86400, function () {
            return DB::table('td_industries')
                ->select('td_industries.ind_id', 'td_industries.ind_name')
                ->orderBy('td_industries.ind_name', 'ASC')
                ->get();
        });
        $this->countryArr = cache()->remember('global_country_arr_list', 86400, function () {
            return GeneralMethod::getAllCountryList();
        });
        $this->statelist = cache()->remember('global_state_list_101', 86400, function () {
            return GeneralMethod::statelist(101);
        });

        $this->directClientDetail = null;

        // ⚡ Tier 1a: Primary Read from Elasticsearch by exact _id or client_id (1-to-1 match for clicked row)
        if ($this->elasticService->indexExists(ELASTIC_CLIENT_LIST)) {
            try {
                $searchRes = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_CLIENT_LIST,
                    'body' => [
                        'size' => 1,
                        'query' => [
                            'bool' => [
                                'should' => [
                                    ['term' => ['_id' => (string) $id]],
                                    ['term' => ['client_id' => (int) $id]],
                                ],
                                'minimum_should_match' => 1,
                            ],
                        ],
                    ],
                ]);

                // ⚡ Tier 1b: If not found by _id/client_id, search ES by company_id (preferring self_company=1)
                if (empty($searchRes['hits']['hits'])) {
                    $searchRes = $this->elasticService->getClient()->search([
                        'index' => ELASTIC_CLIENT_LIST,
                        'body' => [
                            'size' => 1,
                            'query' => [
                                'bool' => [
                                    'must' => [
                                        ['term' => ['company_id' => (int) $id]],
                                    ],
                                ],
                            ],
                            'sort' => [
                                ['self_company' => ['order' => 'desc']],
                            ],
                        ],
                    ]);
                }

                if (! empty($searchRes['hits']['hits'][0]['_source'])) {
                    $source = $searchRes['hits']['hits'][0]['_source'];
                    $cName = ! empty($source['name']) ? $source['name'] : ($source['company_name'] ?? '');
                    $cWeb = ! empty($source['website']) ? $source['website'] : ($source['company_website'] ?? '');
                    $cDesc = ! empty($source['description']) ? $source['description'] : ($source['companies_description'] ?? '');
                    $cLogo = ! empty($source['logo_logical_path']) ? $source['logo_logical_path'] : ($source['logo'] ?? '');
                    $cCompId = $source['company_id'] ?? $source['from_company_id'] ?? $id;
                    $cClientId = $source['client_id'] ?? $source['id'] ?? $id;

                    $this->directClientDetail = (object) [
                        'client_id' => $cClientId,
                        'name' => $cName,
                        'company_name' => $cName,
                        'website' => $cWeb,
                        'company_website' => $cWeb,
                        'logo_logical_path' => $cLogo,
                        'logo' => $cLogo,
                        'industry' => $source['ind_name'] ?? $source['industry'] ?? '',
                        'industries' => $source['industries'] ?? '',
                        'tags' => is_array($source['tags'] ?? null) ? implode(',', $source['tags']) : ($source['tags'] ?? ''),
                        'description' => $cDesc,
                        'companies_description' => $cDesc,
                        'companies_id' => $cCompId,
                        'company_type' => $source['company_type'] ?? '',
                        'industries_type' => $source['industries_type'] ?? '',
                        'company_size' => $source['company_size'] ?? $source['company_sizes'] ?? '',
                        'company_sizes' => $source['company_sizes'] ?? $source['company_size'] ?? '',
                        'founded' => $source['founded'] ?? '',
                        'from_company_id' => $cCompId,
                        'company_id' => $cCompId,
                        'to_created_at' => $source['created_at'] ?? date('Y-m-d H:i:s'),
                    ];
                }
            } catch (\Exception $e) {
                \Log::warning('dviewMore ES primary read warning: '.$e->getMessage(), ['id' => $id]);
            }
        }

        // 🏢 Tier 2a: MySQL DB Companies Table (if direct client id represents company_id)
        if (! $this->directClientDetail) {
            $comp = DB::table('companies')->where('id', $id)->first();
            if ($comp) {
                $rel = DB::table('staffing_company_relation')
                    ->where('from_company_id', $comp->id)
                    ->where('to_company_id', $company_id)
                    ->first();

                $cName = ! empty($comp->company_name) ? $comp->company_name : 'Direct Client';
                $this->directClientDetail = (object) [
                    'client_id' => $id,
                    'name' => $cName,
                    'company_name' => $cName,
                    'website' => $comp->website ?? '',
                    'company_website' => $comp->website ?? '',
                    'logo_logical_path' => $comp->logo ?? '',
                    'logo' => $comp->logo ?? '',
                    'industry' => $comp->industries ?? '',
                    'industries' => $comp->industries ?? '',
                    'tags' => '',
                    'description' => $comp->description ?? '',
                    'companies_description' => $comp->description ?? '',
                    'companies_id' => $comp->id,
                    'company_type' => $comp->company_type ?? '',
                    'industries_type' => $comp->industries_type ?? '',
                    'company_size' => $comp->company_size ?? '',
                    'company_sizes' => $comp->company_size ?? '',
                    'founded' => $comp->founded ?? '',
                    'from_company_id' => $comp->id,
                    'company_id' => $comp->id,
                    'to_created_at' => $rel->created_at ?? date('Y-m-d H:i:s'),
                ];
            }
        }

        // 🏢 Tier 2b: MySQL DB Clients Table Fallback
        if (! $this->directClientDetail) {
            $clientRec = DB::table('clients')
                ->select(
                    'clients.id as client_id',
                    'clients.company_id as clients_company_id',
                    'clients.self_company',
                    'clients.name as client_name',
                    'clients.website as client_website',
                    'clients.logo_logical_path',
                    'clients.logo as client_logo',
                    'clients.industry',
                    'clients.tags',
                    'clients.description as client_description',
                    'clients.company_sizes',
                    'clients.company_type as client_company_type',
                    'clients.founded as client_founded',
                    'companies.id as companies_id',
                    'companies.website as company_website',
                    'companies.description as companies_description',
                    'companies.logo as company_logo',
                    'companies.company_type',
                    'companies.industries',
                    'companies.industries_type',
                    'companies.company_name',
                    'companies.company_size',
                    'companies.founded',
                    'staffing_company_relation.from_company_id',
                    'staffing_company_relation.created_at as to_created_at'
                )
                ->leftJoin('companies', 'companies.id', '=', 'clients.company_id')
                ->leftJoin('staffing_company_relation', function ($join) use ($company_id) {
                    $join->on('staffing_company_relation.from_company_id', '=', 'companies.id')
                        ->where('staffing_company_relation.to_company_id', '=', $company_id);
                })
                ->where('clients.id', '=', $id)
                ->first();

            if ($this->directClientDetail) {
                $this->directClientDetail->name = ! empty($this->directClientDetail->client_name) ? $this->directClientDetail->client_name : $this->directClientDetail->company_name;
                $this->directClientDetail->company_name = $this->directClientDetail->name;
                $this->directClientDetail->website = ! empty($this->directClientDetail->client_website) ? $this->directClientDetail->client_website : $this->directClientDetail->company_website;
                $this->directClientDetail->description = ! empty($this->directClientDetail->client_description) ? $this->directClientDetail->client_description : $this->directClientDetail->companies_description;
                $this->directClientDetail->companies_description = $this->directClientDetail->description;
                $this->directClientDetail->logo = ! empty($this->directClientDetail->logo_logical_path) ? $this->directClientDetail->logo_logical_path : (! empty($this->directClientDetail->client_logo) ? $this->directClientDetail->client_logo : $this->directClientDetail->company_logo);
                $this->directClientDetail->founded = ! empty($this->directClientDetail->client_founded) ? $this->directClientDetail->client_founded : $this->directClientDetail->founded;
                $this->directClientDetail->company_sizes = ! empty($this->directClientDetail->company_sizes) ? $this->directClientDetail->company_sizes : $this->directClientDetail->company_size;
                $this->directClientDetail->from_company_id = $this->directClientDetail->from_company_id ?? $this->directClientDetail->companies_id ?? $id;
                $this->directClientDetail->company_id = $this->directClientDetail->from_company_id;
            }
        }

        // 🛡️ Tier 3: Final Safe Fallback Object (Guarantees zero null pointer exceptions)
        if (! $this->directClientDetail) {
            $this->directClientDetail = (object) [
                'client_id' => $id,
                'name' => 'Client #'.$id,
                'company_name' => 'Client #'.$id,
                'website' => '',
                'company_website' => '',
                'logo_logical_path' => '',
                'logo' => '',
                'industry' => '',
                'industries' => '',
                'tags' => '',
                'description' => '',
                'companies_description' => '',
                'companies_id' => $id,
                'company_type' => '',
                'industries_type' => '',
                'company_size' => '',
                'company_sizes' => '',
                'founded' => '',
                'from_company_id' => $id,
                'company_id' => $id,
                'to_created_at' => date('Y-m-d H:i:s'),
            ];
        }

        $from_company_id = $this->directClientDetail->from_company_id ?? $this->directClientDetail->companies_id ?? $id;
        $this->directClientDetail->company_id = $this->directClientDetail->company_id ?? $from_company_id;

        $this->client_file = DB::table('client_file')->select('*')->where('client_id', $id)->get();

        $this->client_location = DB::table('client_location')
            ->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
            ->where('client_location.client_id', $id)
            ->orderBy('client_location.id', 'DESC')
            ->first();

        $this->city_name = '';
        if (! empty($this->client_location->country_id) && ($this->client_location->country_id == 101)) {
            $this->city_obj = DB::table('city_list')->select('city_list.city')->where('city_list.city_id', $this->client_location->city_id)->first();
            $this->city_name = ucwords($this->city_obj->city ?? '');
        } else {
            $city = @$this->client_location->city;
            $this->city_name = ucwords($city ?? '');
        }

        $this->country_name = '';
        if (@$this->client_location->country_id > 0) {
            $country_name = GeneralMethod::getCountryName($this->client_location->country_id);
            if (! empty($country_name)) {
                foreach ($country_name as $cval) {
                    $this->country_name = ucwords($cval['name'] ?? '');
                }
            }
        }

        $this->client_contact_info = DB::table('client_contact_info')->select('*')->where('client_id', $id)->take(1)->get();
        $this->client_contact_count = DB::table('client_contact_info')->select('*')->where('client_id', $id)->count();
        $this->active_requisition = DB::table('jobs')->select('jobs.id')->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')->where('recommendation_relation.to_company_id', $company_id)->where('jobs.status', 'active')->count();

        $startOfMonth = Carbon::now()->startOfMonth()->subMonths(3);
        $endOfMonth = Carbon::now()->endOfMonth();
        $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(6);
        $endOfMonthcomman = Carbon::now()->endOfMonth();

        // Initialize default empty chart state for lazy loading via dashboardAnalytics endpoint
        $this->result = collect();
        $this->joined_count = 0;
        $this->sourcing_count = 0;
        $this->screening_count = 0;
        $this->evaluation_count = 0;
        $this->negotiation_count = 0;
        $this->offer_count = 0;
        $this->Rejected_count = 0;
        $this->inreview_count = 0;
        $this->total_candidates = 0;
        $this->counts = collect();
        $this->labels = [];
        $this->candidateValues = [];
        $this->candidateValuesTotal = 0;
        $this->labelsCommoffer = [];
        $this->candidateValuesCommoffer = [];
        $this->candidateValuesTotaloffer = 0;
        $this->labelsCommjoin = [];
        $this->candidateValuesCommjoin = [];
        $this->candidateValuesTotaljoin = 0;
        $this->upcomingData = [];
        $this->recentData = [];
        $this->interviewlabels = [];
        $this->interviewValues = [];
        $this->interviewValuesTotal = 0;
        $this->recentDataMonth = [];
        $this->interviewlabelsMonth = [];
        $this->interviewValuesMonth = [];
        $this->interviewValuesTotalMonth = 0;

        $this->startOfMonth = $startOfMonth;
        $this->endOfMonth = $endOfMonth;

        /** Render instant lightweight Direct Client Details shell view (< 60ms) */
        $view = view('service_company.client.dviewMore', $this->data)->render();
        $responseData = Reply::dataOnly(['status' => 'success', 'view' => $view]);

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(CLIENTS_DVIEWMORE_TIME));

        return response()->json($responseData);

    }

    public function dcontactList()
    {
        $startTime = microtime(true);
        abort_if(Gate::denies('view_clients'), 403);

        $company_id = (int) auth()->user()->company_id;
        $direct_company = (int) request('company_id');

        $directContactList = DB::table('companies')
            ->select(
                'companies.id',
                'companies.company_name',
                'companies.logo',
                'companies.industries',
                'companies.company_type',
                'users.name',
                'users.id as userId',
                'users.email',
                'users.mobile',
                'users.image',
                'users.status',
                'users.calling_code',
                'recommendation_relation.from_user_id',
                'users_profile.designation as display_name'
            );
        $directContactList = $directContactList->join('roles', 'roles.company_id', '=', 'companies.id');
        $directContactList = $directContactList->join('role_user', 'role_user.role_id', '=', 'roles.id');
        $directContactList = $directContactList->join('users', 'users.id', '=', 'role_user.user_id');
        $directContactList = $directContactList->join('users_profile', 'users_profile.user_id', '=', 'users.id');
        $directContactList = $directContactList->join('job_assign', 'job_assign.spok_user_id', '=', 'users.id');
        $directContactList = $directContactList->join('recommendation_relation', 'recommendation_relation.from_user_id', '=', 'users.id');

        $direct_company = request('company_id');
        $directContactList = $directContactList->where('companies.id', '=', $direct_company);
        $directContactList = $directContactList->where('recommendation_relation.final_status', '=', 1);
        $directContactList = $directContactList->groupBy('users.id');
        // $directContactList = $directContactList->where('roles.display_name', '=', 'Administrator');

        // Avatar colour palette (matches the Agencies-style contacts table).
        $avatarColors = ['#c084fc', '#60a5fa', '#fbbf24', '#34d399', '#f472b6', '#38bdf8', '#fb923c'];

        $response = DataTables::of($directContactList)
            ->editColumn('chkbox', function ($row) {
                return '<input type="checkbox" class="table-checkbox" />';
            })
            ->editColumn('Contact', function ($row) use ($avatarColors) {
                $name = trim((string) @$row->name);
                $initials = strtoupper(implode('', array_map(fn ($w) => mb_substr($w, 0, 1),
                    array_slice(array_filter(explode(' ', $name)), 0, 2))));
                $color = $avatarColors[(ord(strtolower(substr($name, 0, 1) ?: 'a'))) % count($avatarColors)];
                // "Primary" marks the linked point-of-contact (the spoc tied to the relation).
                $primary = ! empty($row->from_user_id)
                    ? '<div class="contact-primary-badge"><i class="fas fa-check"></i>Primary</div>' : '';

                return '<div class="contact-cell">
							<div class="contact-avatar-table" style="background:'.$color.'">'.$initials.'</div>
							<div class="contact-info-table">
								<div class="contact-name-table">'.ucwords($name).'</div>'.$primary.'
							</div>
						</div>';
            })
            ->editColumn('Designation', function ($row) {
                return ucwords((string) @$row->display_name);
            })
            ->editColumn('Email', function ($row) {
                return '<span class="email-cell">'.@$row->email.'</span>';
            })
            ->editColumn('Phone', function ($row) {
                return GeneralMethod::formatContactPhoneHtml($row->calling_code, $row->mobile);
            })
            ->editColumn('Status', function ($row) {
                $cls = ($row->status == 'active') ? 'active' : 'inactive';

                return '<span class="status-badge-table '.$cls.'"><i class="fas fa-circle"></i>'.ucwords($row->status).'</span>';
            })
            ->editColumn('Actions', function ($row) {
                $call = GeneralMethod::formatContactPhoneForTel($row->calling_code, $row->mobile);

                return '<div class="action-btns">
							<a href="tel:'.$call.'" class="action-btn-table" title="Call"><i class="fas fa-phone"></i></a>
							<a href="mailto:'.$row->email.'" class="action-btn-table" title="Email"><i class="fas fa-envelope"></i></a>
						</div>';
            })
            ->rawColumns(['chkbox', 'Contact', 'Email', 'Phone', 'Status', 'Actions'])
            ->addIndexColumn()->make(true);

        \Log::info('CLIENT_DETAILS_APM: dcontactList DataTables completed', [
            'company_id' => $company_id,
            'direct_company' => $direct_company,
            'latency_ms' => round((microtime(true) - $startTime) * 1000, 2),
        ]);

        return $response;

    }

    public function edit($id)
    {
        abort_if(Gate::denies('edit_clients'), 403);
        $this->pageTitle = __('Edit Clients');
        $this->page_event = 'EDIT';
        $id = GeneralMethod::value_decrypt($id);

        if ($id) {
            /**
             * V2 Primary Read Source from MySQL Relational Database.
             * Intent: Ensure 100% data consistency by reading client model from MySQL source of truth.
             * Tenancy Note: Scoped by company_id to enforce multi-tenant isolation.
             */
            $this->client = Client::where('id', $id)
                ->where('company_id', auth()->user()->company_id)
                ->first();

            abort_if(! $this->client, 404);
        }

        $this->client_file = DB::table('client_file')
            ->select('*')
            ->where('client_id', $id)
            ->where('company_id', auth()->user()->company_id)
            ->get();

        $this->client_location = DB::table('client_location')
            ->join('countries', 'countries.id', '=', 'client_location.country_id')
            ->select(
                'client_location.city',
                'client_location.city_id',
                'client_location.address',
                'client_location.postcode',
                'countries.country_name',
                'client_location.country_id',
                'client_location.state'
            )
            ->where('client_location.client_id', $id)
            ->where('client_location.company_id', auth()->user()->company_id)
            ->get();

        $this->client_contact_info = DB::table('client_contact_info')
            ->select('*')
            ->where('client_id', $id)
            ->get();
        $this->client_contact_arr = $this->client_contact_info;

        /**
         * Cache global static reference datasets for Edit Client view.
         * Tenancy Note: Reference data is global static lookups.
         */
        $this->countries = cache()->remember('global_country_all', 86400, function () {
            return Country::all();
        });
        $this->categories = cache()->remember('global_job_categories_all', 86400, function () {
            return JobCategory::all();
        });
        $this->companyType = GeneralMethod::companyType();
        $this->companyTypeTaxonomy = GeneralMethod::companyTypeTaxonomy();
        $this->department = cache()->remember('global_td_industries_all', 86400, function () {
            return DB::table('td_industries')
                ->select('td_industries.ind_id', 'td_industries.ind_name')
                ->orderBy('td_industries.ind_name', 'ASC')
                ->get();
        });
        $this->countryArr = cache()->remember('global_country_arr_list', 86400, function () {
            return GeneralMethod::getAllCountryList();
        });

        $statelist = cache()->remember('global_state_list_101', 86400, function () {
            return DB::table('state_list')
                ->select('state_list.id', 'state_list.state as name', DB::raw('101 as country_id'))
                ->where('state_list.country_id', 101)
                ->orderBy('state_list.state')
                ->get()->toArray();
        });

        $this->statelist = json_decode(json_encode($statelist), true);
        $this->cities = GeneralMethod::allCities();
        $this->default_loc = GeneralMethod::allCitiesSelected([]);

        $companyId = (int) auth()->user()->company_id;
        $this->industriesManifest = IndustryTaxonomyService::getGlobalManifest();

        return response()
            ->view('service_company.client.create', $this->data)
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
    }

    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('edit_clients'), 403);

        if ($validationError = $this->validateClientSubmission($request)) {
            return $validationError;
        }

        $company_id = $request->user()->company_id;
        $idempotencyKey = $request->header('X-Idempotency-Key') ?? $request->input('idempotency_key');

        $canonicalHash = hash('sha256', json_encode([
            'client_id' => $id,
            'company_id' => $company_id,
            'name' => trim($request->name ?? ''),
            'website' => trim($request->website ?? ''),
            'industry' => $request->industry,
            'custom_industry' => trim($request->custom_industry ?? ''),
            'company_sizes' => $request->company_sizes,
            'company_type' => $request->company_type,
            'founded' => $request->founded,
            'description' => trim(strip_tags($request->description ?? '')),
            'tags' => $request->tags,
            'address' => $request->address,
            'cp_name' => $request->cp_name,
            'cp_email' => $request->cp_email,
        ]));

        if (! empty($idempotencyKey)) {
            $existing = DB::table('sys_idempotency_keys')
                ->where('tenant_id', $company_id)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                // If the exact same request was completed within the last 5 seconds (rapid double-click), return cached redirect
                if ($existing->status === 'COMPLETED' && $existing->request_hash === $canonicalHash && $existing->completed_at && Carbon::parse($existing->completed_at)->diffInSeconds(now()) < 5) {
                    $cachedBody = json_decode($existing->response_body, true);

                    return Reply::redirect($cachedBody['url'] ?? route('service_company.clients.index'), $cachedBody['message'] ?? (__('menu.clients').' '.__('messages.updatedSuccessfully')));
                }

                // If currently processing within the last 15 seconds, reject concurrent race
                if ($existing->status === 'PROCESSING' && $existing->created_at && Carbon::parse($existing->created_at)->diffInSeconds(now()) < 15) {
                    return response()->json(['status' => 'processing', 'message' => 'Update is currently being processed. Please wait.'], 425);
                }

                // Otherwise, update existing key record to PROCESSING with new canonical hash (allow legitimate modifications)
                DB::table('sys_idempotency_keys')
                    ->where('id', $existing->id)
                    ->update([
                        'request_hash' => $canonicalHash,
                        'status' => 'PROCESSING',
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('sys_idempotency_keys')->insertOrIgnore([
                    'tenant_id' => $company_id,
                    'idempotency_key' => $idempotencyKey,
                    'request_hash' => $canonicalHash,
                    'status' => 'PROCESSING',
                    'expires_at' => now()->addHours(24),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        /**
         * Tenancy Scoped Client Fetch
         * Intent: Enforce strict multi-tenant boundary on client updates.
         * Tenancy Note: Scoped by company_id.
         */
        $client = Client::where('id', $id)
            ->where('company_id', $company_id)
            ->firstOrFail();

        // FIRST TAB-1
        if (! empty($request->name)) {
            $client->name = $request->name;
        }
        if (! empty($request->website)) {
            $client->website = $request->website;
        }
        $customIndustry = null;
        if (! empty($request->industry)) {
            if (is_numeric($request->industry)) {
                $client->industry = (int) $request->industry;
            } else {
                $client->industry = 0;
                $customIndustry = trim($request->industry);
            }
        }
        if (! empty($request->custom_industry)) {
            $customIndustry = trim($request->custom_industry);
            $client->industry = 0;
        }
        if (! empty($request->company_sizes)) {
            [$from_size, $to_size] = explode('-', $request->company_sizes);
            $client->company_size_from = (int) $from_size;
            $client->company_sizes = ((int) $to_size > 0) ? (int) $to_size : null;
        }
        if (isset($request->company_type) && $request->company_type !== '') {
            $client->company_type = $request->company_type;
        }
        if (! empty($request->founded)) {
            $client->founded = $request->founded;
        }

        // SECOND TAB-2
        if (! empty($request->description)) {
            $client->description = $request->description;
        }
        if (! empty($request->tags)) {
            $tags = $request->tags;
            $tags = json_decode($tags, true);
            $tags = array_column($tags, 'value');
            $tags = implode(',', $tags);
            $client->tags = $tags;
        }

        $existingDetail = [];
        if (! empty($client->detail)) {
            $decodedExisting = json_decode($client->detail, true);
            if (is_array($decodedExisting)) {
                $existingDetail = $decodedExisting;
            }
        }

        $detailData = array_merge($existingDetail, [
            'custom_industry' => $customIndustry ?? ($existingDetail['custom_industry'] ?? null),
            'career_page_url' => $request->career_page_url ?? null,
            'linkedin_url' => $request->linkedin_url ?? null,
            'twitter_url' => $request->twitter_url ?? null,
            'github_url' => $request->github_url ?? null,
            'ap_email' => $request->ap_email ?? null,
            'tax_id' => $request->tax_id ?? null,
            'youtube_url' => $request->youtube_url ?? null,
            'facebook_url' => $request->facebook_url ?? null,
            'instagram_url' => $request->instagram_url ?? null,
            'custom_link' => $request->custom_link ?? null,
        ]);
        $filteredDetail = array_filter($detailData, fn ($val) => $val !== null && $val !== '');
        if (! empty($filteredDetail)) {
            $client->detail = json_encode($filteredDetail);
        }

        // Handle logo upload outside database transaction
        $timeStamp = time();
        if ($request->hasFile('logo')) {
            $directoryPath = GeneralMethod::document_manage_directory($company_id);
            $file = $request->file('logo');
            $ext = strtolower($file->getClientOriginalExtension());
            $allowed = ['png', 'jpeg', 'jpg'];
            if (in_array($ext, $allowed)) {
                $filename = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $altfilename = GeneralMethod::getValidUploadFileName($filename).'-'.$timeStamp.'.'.$ext;
                $target_file = $directoryPath.'/'.$altfilename;

                $file->move($directoryPath, $altfilename);

                $companyNameR2 = DB::table('companies')
                    ->where('id', $company_id)
                    ->value('company_name');
                $companyNameR2 = strtolower(trim($companyNameR2 ?? 'unknown'));
                $companyNameR2 = preg_replace('/[^a-z0-9_-]/i', '_', $companyNameR2);
                $companyNameRTwo = $companyNameR2.'_'.$company_id;
                $r2Filename = uniqid('JERO_logo').'.'.$ext;
                $fileUrl = "https://file.jero.works/Companies/{$companyNameRTwo}/Images/{$r2Filename}";

                $client->logo = $altfilename;
                $client->logo_physical_path = $target_file;
                $client->logo_logical_path = $fileUrl;
            }
        }

        $lock = Cache::lock('client:update:'.$id, 10);
        if (! $lock->get()) {
            return Reply::dataOnly(['status' => 'error', 'msgType' => 'warning', 'message' => 'Another user is currently updating this client. Please try again in a moment.']);
        }

        $client_id = $id;

        // Pre-fetch existing locations in a single tenant-scoped query outside transaction
        $existingLocations = DB::table('client_location')
            ->where('client_id', $client_id)
            ->where('company_id', $company_id)
            ->get();
        $existingLocMap = [];
        foreach ($existingLocations as $loc) {
            $existingLocMap[$loc->city_id.'_'.trim($loc->address)] = $loc;
        }

        $totalAddresses = is_array($request->address) ? count($request->address) : 0;
        $maxAddressIndex = max($totalAddresses - 1, (int) (@$request->addressId ?? 0));
        $locationsToUpdate = [];
        $locationsToInsert = [];
        $addIdGet = [];

        for ($i = 0; $i <= $maxAddressIndex; $i++) {
            $address = @$request->address[$i];
            $postcode = '0';
            $work_city = @$request->kt_tagify_work_location[$i];
            $work_city_all = json_decode($work_city, true);

            if (! empty($work_city) && is_array($work_city_all)) {
                foreach ($work_city_all as $cityVal) {
                    $valStr = trim($cityVal['value'] ?? '');
                    if (empty($valStr)) {
                        continue;
                    }
                    $parts = (strpos($valStr, '|') !== false) ? explode('|', $valStr) : explode(',', $valStr);
                    $cityName = trim($parts[0] ?? '');
                    $countrName = isset($parts[2]) ? trim($parts[2]) : (isset($parts[1]) ? trim($parts[1]) : '');

                    $cityCacheKey = 'city_geo_lookup_'.md5($cityName.'_'.$countrName);
                    $city_row = cache()->remember($cityCacheKey, 86400, function () use ($cityName, $countrName) {
                        $query = DB::table('city_list')
                            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
                            ->join('countries', 'countries.id', '=', 'state_list.country_id')
                            ->select('city_list.city_id', 'countries.id as country_id', 'city_list.state_id')
                            ->where('city_list.city', '!=', '')
                            ->where('city_list.city', '=', $cityName);

                        if (! empty($countrName)) {
                            $query->where('countries.country_name', '=', $countrName);
                        }
                        $row = $query->first();
                        if (! $row) {
                            $row = DB::table('city_list')
                                ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
                                ->join('countries', 'countries.id', '=', 'state_list.country_id')
                                ->select('city_list.city_id', 'countries.id as country_id', 'city_list.state_id')
                                ->where('city_list.city', '!=', '')
                                ->where('city_list.city', '=', $cityName)
                                ->first();
                        }

                        return $row;
                    });

                    if ($city_row) {
                        $cityID = $city_row->city_id;
                        $countryID = $city_row->country_id;
                        $stateId = $city_row->state_id ?? GeneralMethod::stateId($cityID);

                        $values = [
                            'company_id' => $company_id,
                            'client_id' => $client_id,
                            'country_id' => $countryID,
                            'city' => '',
                            'city_id' => $cityID,
                            'address' => $address,
                            'postcode' => $postcode,
                            'state' => $stateId,
                        ];

                        $locKey = $cityID.'_'.trim($address);
                        if (isset($existingLocMap[$locKey])) {
                            $existingLocId = $existingLocMap[$locKey]->id;
                            $locationsToUpdate[$existingLocId] = $values;
                            $addIdGet[] = $existingLocId;
                        } else {
                            $locationsToInsert[] = $values;
                        }
                    }
                }
            }
        }

        // Pre-fetch existing contacts in a single query outside transaction
        $existingContacts = DB::table('client_contact_info')
            ->where('client_id', $client_id)
            ->get()
            ->keyBy(function ($item) {
                return strtolower(trim($item->contact_person_email));
            });

        $contactsToUpdate = [];
        $contactsToInsert = [];
        $processedContactIds = [];
        $seenEmailsInSubmission = [];
        $totalCpNames = is_array($request->cp_name) ? count($request->cp_name) : 0;
        $contactPersonId = max($totalCpNames - 1, (int) (@$request->contactPersonId ?? 0));

        for ($j = 0; $j <= $contactPersonId; $j++) {
            $cp_name = @$request->cp_name[$j];
            $cp_email = @$request->cp_email[$j];
            $cp_number = @$request->cp_number[$j];
            $cp_desigination = @$request->cp_desigination[$j];
            $calling_code = @$request->calling_code[$j];

            if (! empty($cp_name) && ! empty($cp_email) && ! empty($cp_desigination)) {
                $normEmail = strtolower(trim($cp_email));
                if (isset($seenEmailsInSubmission[$normEmail])) {
                    continue; // Defense-in-depth: skip duplicate contact in same submission
                }
                $seenEmailsInSubmission[$normEmail] = true;

                $values = [
                    'client_id' => $client_id,
                    'contact_person_name' => $cp_name,
                    'contact_person_email' => $cp_email,
                    'contact_person_number' => $cp_number,
                    'calling_code' => $calling_code,
                    'contact_person_designation' => $cp_desigination,
                ];

                if (isset($existingContacts[$normEmail])) {
                    $existingId = $existingContacts[$normEmail]->id;
                    $contactsToUpdate[$existingId] = $values;
                    $processedContactIds[] = $existingId;
                } else {
                    $contactsToInsert[] = $values;
                }
            }
        }

        if (! empty($contactsToInsert) || ! empty($contactsToUpdate)) {
            $primaryContact = ! empty($contactsToInsert) ? reset($contactsToInsert) : reset($contactsToUpdate);
            $client->contact_person_name = substr($primaryContact['contact_person_name'] ?? $client->contact_person_name, 0, 55);
            $client->email = substr($primaryContact['contact_person_email'] ?? $client->email, 0, 55);
            $client->contact_number = substr($primaryContact['contact_person_number'] ?? $client->contact_number, 0, 55);
        } else {
            $client->contact_person_name = $client->contact_person_name ?? '';
            $client->email = $client->email ?? '';
            $client->contact_number = $client->contact_number ?? '';
        }
        $client->commission = $client->commission ?? '0';
        $client->payout = $client->payout ?? '0';

        try {
            DB::beginTransaction();

            $client->save();

            // Execute location updates and inserts
            foreach ($locationsToUpdate as $locId => $locValues) {
                DB::table('client_location')->where('id', $locId)->update($locValues);
            }
            foreach ($locationsToInsert as $newLoc) {
                $newLocId = DB::table('client_location')->insertGetId($newLoc);
                $addIdGet[] = $newLocId;
            }

            DB::table('client_location')
                ->whereNotIn('id', $addIdGet)
                ->where('client_id', $client_id)
                ->delete();

            // Execute contact updates and inserts
            foreach ($contactsToUpdate as $contactId => $cntValues) {
                DB::table('client_contact_info')->where('id', $contactId)->update($cntValues);
            }
            foreach ($contactsToInsert as $newCnt) {
                $newContactId = DB::table('client_contact_info')->insertGetId($newCnt);
                $processedContactIds[] = $newContactId;
            }

            DB::table('client_contact_info')
                ->where('client_id', $client_id)
                ->whereNotIn('id', $processedContactIds)
                ->delete();

            // Transactional Outbox Pattern: Atomic event persistence
            $existingOutbox = ! empty($idempotencyKey)
                ? DB::table('sys_outbox_events')
                    ->where('tenant_id', $company_id)
                    ->where('idempotency_key', $idempotencyKey)
                    ->first()
                : null;

            if ($existingOutbox) {
                $outboxId = $existingOutbox->id;
                DB::table('sys_outbox_events')
                    ->where('id', $outboxId)
                    ->update([
                        'aggregate_id' => $client_id,
                        'aggregate_version' => 2,
                        'event_type' => 'ClientUpdated',
                        'payload' => json_encode([
                            'tenant_id' => $company_id,
                            'client_id' => $client_id,
                            'client_name' => $client->name,
                            'action' => 'ClientUpdated',
                            'timestamp' => now()->toIso8601String(),
                        ]),
                        'status' => 'PENDING',
                        'attempts' => 0,
                        'available_at' => now(),
                        'updated_at' => now(),
                    ]);
            } else {
                $outboxId = DB::table('sys_outbox_events')->insertGetId([
                    'tenant_id' => $company_id,
                    'idempotency_key' => $idempotencyKey,
                    'aggregate_type' => 'Client',
                    'aggregate_id' => $client_id,
                    'aggregate_version' => 2,
                    'event_type' => 'ClientUpdated',
                    'payload' => json_encode([
                        'tenant_id' => $company_id,
                        'client_id' => $client_id,
                        'client_name' => $client->name,
                        'action' => 'ClientUpdated',
                        'timestamp' => now()->toIso8601String(),
                    ]),
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => now(),
                    'created_at' => now(),
                ]);
            }

            // Mark idempotency key as COMPLETED
            if (! empty($idempotencyKey)) {
                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', $company_id)
                    ->where('idempotency_key', $idempotencyKey)
                    ->update([
                        'status' => 'COMPLETED',
                        'response_status' => 200,
                        'response_body' => json_encode([
                            'status' => 'success',
                            'url' => route('service_company.clients.index'),
                            'message' => __('menu.clients').' '.__('messages.updatedSuccessfully'),
                        ]),
                        'completed_at' => now(),
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();
            \Log::info("Client update MySQL write committed successfully for client ID {$client_id}", [
                'client_id' => $client_id,
                'company_id' => $company_id,
                'operation' => 'update',
            ]);

            // Synchronous Elasticsearch Indexing post-commit for authoritative real-time consistency
            $esSyncSuccess = false;
            try {
                $esResult = GeneralMethod::clientStaffingPartner($client_id);
                $esSyncSuccess = true;
                \Log::info("Client ES sync success for client {$client_id}", [
                    'client_id' => $client_id,
                    'index' => ELASTIC_CLIENT_LIST,
                    'operation' => 'update',
                    'result' => $esResult['result'] ?? 'ok',
                ]);

                // Mark outbox event as COMPLETED since synchronous indexing succeeded
                DB::table('sys_outbox_events')
                    ->where('id', $outboxId)
                    ->update([
                        'status' => 'COMPLETED',
                        'processed_at' => now(),
                        'attempts' => 1,
                        'last_error' => null,
                    ]);
            } catch (\Throwable $esEx) {
                \Log::error("Client ES sync failed for client {$client_id}: ".$esEx->getMessage(), [
                    'client_id' => $client_id,
                    'index' => ELASTIC_CLIENT_LIST,
                    'operation' => 'update',
                    'exception' => $esEx,
                ]);

                // Mark outbox event as PENDING with retry info and dispatch job for durable recovery
                DB::table('sys_outbox_events')
                    ->where('id', $outboxId)
                    ->update([
                        'status' => 'PENDING',
                        'last_error' => substr($esEx->getMessage(), 0, 1000),
                        'available_at' => now()->addSeconds(10),
                    ]);

                try {
                    ProcessClientOutboxEventJob::dispatch($outboxId);
                } catch (\Throwable $jobEx) {
                    \Log::warning('ProcessClientOutboxEventJob dispatch warning: '.$jobEx->getMessage());
                }
            }

            $userId = auth()->id() ?? ($this->user->id ?? $request->user()?->id ?? 0);
            $cachekeys = [
                'allclients_version_user_'.$userId,
                'allcustomclient_version_user_'.$userId,
                'clientsviewmore_version_user_'.$userId,
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }
            GeneralMethod::cacheClean('tenant_'.$company_id.'_client_version');

            $redirectMessage = $esSyncSuccess
                ? (__('menu.clients').' '.__('messages.updatedSuccessfully'))
                : (__('menu.clients').' '.__('messages.updatedSuccessfully').' (Search index is syncing in background.)');

            return Reply::redirect(route('service_company.clients.index'), $redirectMessage);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('clients update() transaction failed: '.$e->getMessage(), ['exception' => $e]);

            if (! empty($idempotencyKey)) {
                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', $company_id)
                    ->where('idempotency_key', $idempotencyKey)
                    ->update([
                        'status' => 'FAILED_RETRYABLE',
                        'last_error' => substr($e->getMessage(), 0, 1000),
                        'updated_at' => now(),
                    ]);
            }

            return Reply::dataOnly(['status' => 'error', 'msgType' => 'danger', 'message' => 'Update failed. Please try again.']);
        } finally {
            optional($lock)->release();
        }
    }

    /**
     * Return tenant-scoped versioned industries bundle with ETag HTTP 304 caching.
     *
     * Intent: Client-first industry autocomplete and classification (10K concurrent users, 0% server read strain).
     * Tenancy Note: Tenant cache keys tenant:{$companyId}:dataset:industries_bundle:v{$version}.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function industriesBundle(?Request $request = null)
    {
        $request = $request ?: request();
        abort_if(! auth()->check(), 401);

        $bundle = IndustryTaxonomyService::getGlobalManifest();
        $contentHash = $bundle['content_hash'] ?? 'default';
        $etag = 'W/"ind_'.$contentHash.'"';
        $clientEtag = $request->header('If-None-Match');

        if ($clientEtag && trim($clientEtag) === trim($etag)) {
            return response(null, 304)
                ->header('ETag', $etag)
                ->header('Cache-Control', 'public, max-age=86400, must-revalidate');
        }

        return response()->json($bundle)
            ->header('ETag', $etag)
            ->header('Cache-Control', 'public, max-age=86400, must-revalidate');
    }

    public function create()
    {
        abort_if(Gate::denies('add_clients'), 403);

        $this->pageTitle = __('Add Clients');
        $this->page_event = 'ADD';

        /**
         * Cache global static reference data to eliminate repeated DB queries on Add Client page loads.
         * Tenancy Note: Reference data is global (non-tenant-scoped).
         */
        $this->countries = cache()->remember('global_country_all', 86400, function () {
            return Country::all();
        });
        $this->categories = cache()->remember('global_job_categories_all', 86400, function () {
            return JobCategory::all();
        });
        $this->companyType = GeneralMethod::companyType();
        $this->companyTypeTaxonomy = GeneralMethod::companyTypeTaxonomy();
        $this->department = cache()->remember('global_td_industries_all', 86400, function () {
            return DB::table('td_industries')
                ->select('td_industries.ind_id', 'td_industries.ind_name')
                ->orderBy('td_industries.ind_name', 'ASC')
                ->get();
        });
        $this->countryArr = cache()->remember('global_country_arr_list', 86400, function () {
            return GeneralMethod::getAllCountryList();
        });

        $statelist = cache()->remember('global_state_list_101', 86400, function () {
            return DB::table('state_list')
                ->select('state_list.id', 'state_list.state as name', DB::raw('101 as country_id'))
                ->orderBy('state_list.state')
                ->get()->toArray();
        });
        $this->statelist = json_decode(json_encode($statelist), true);
        $this->cities = GeneralMethod::allCities();

        $this->default_loc = GeneralMethod::allCitiesSelected([]);

        $companyId = (int) auth()->user()->company_id;
        $this->industriesManifest = IndustryTaxonomyService::getGlobalManifest();

        return response()
            ->view('service_company.client.create', $this->data)
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
    }

    public function citylist($state_id)
    {
        /**
         * Global Reference Cache: City List by State ID
         * Intent: Eliminate repeated DB queries on state change; reference data is static and non-tenant-scoped.
         */
        $custom = cache()->remember("global_citylist_html_state_{$state_id}", 86400, function () use ($state_id) {
            $Rawcitylist = DB::table('city_list')
                ->select('city_list.city_id as id', 'city_list.city as name')
                ->where('city_list.state_id', $state_id)
                ->orderBy('city_list.city')
                ->get()->toArray();
            $citylist = json_decode(json_encode($Rawcitylist), true);

            $html = '<option>Select City</option>';
            foreach ($citylist as $sval) {
                $html .= '<option value="'.$sval['id'].'">'.$sval['name'].'</option>';
            }

            return $html;
        });

        return Reply::dataOnly(['status' => 'success', 'data' => $custom]);
    }

    /**
     * Validate Client Add/Edit Submission parameters across 4 wizard sections.
     *
     * Intent: Enforce strict validation rules for Client Information, Client Description,
     * Address Details, and minimum 1 Contact Person added.
     */
    private function validateClientSubmission(Request $request)
    {
        // 1. Client Information
        if (empty(trim($request->name ?? ''))) {
            return Reply::error('Client Name is required.');
        }
        $hasIndustry = (! empty($request->industry) && (string) $request->industry !== '0') || ! empty(trim($request->custom_industry ?? ''));
        if (! $hasIndustry) {
            return Reply::error('Industry is required.');
        }
        if (empty($request->company_sizes)) {
            return Reply::error('Company Size is required.');
        }
        if (! isset($request->company_type) || $request->company_type === '') {
            return Reply::error('Company Type is required.');
        }
        if (empty($request->founded)) {
            return Reply::error('Founded Year is required.');
        }

        // Logo size and extension check (max 2MB per enterprise specification)
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            if ($file->getSize() > 2 * 1024 * 1024) {
                return Reply::error('Company logo size cannot exceed 2MB.');
            }
            $ext = strtolower($file->getClientOriginalExtension());
            if (! in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'svg'])) {
                return Reply::error('Logo must be a valid image file (PNG, JPG, WEBP, SVG).');
            }
        }

        // 2. Client Description & Service Offerings
        if (empty(trim(strip_tags($request->description ?? '')))) {
            return Reply::error('Company Description is required.');
        }
        if (empty($request->tags)) {
            return Reply::error('Service Offerings are required.');
        }

        // 3. Address Details (Minimum 1 location with City & Full Address)
        $hasValidAddress = false;
        $totalAddresses = is_array($request->address) ? count($request->address) : 0;
        $maxAddressIndex = max($totalAddresses - 1, (int) (@$request->addressId ?? 0));
        for ($i = 0; $i <= $maxAddressIndex; $i++) {
            $address = trim(@$request->address[$i] ?? '');
            $work_city = @$request->kt_tagify_work_location[$i];
            if (! empty($address) && ! empty($work_city)) {
                $hasValidAddress = true;
                break;
            }
        }
        if (! $hasValidAddress) {
            return Reply::error('At least 1 Address Detail with City and Full Address is required.');
        }

        // 4. Contact Person (Minimum 1 Contact Person added)
        $hasValidContact = false;
        $totalCpNames = is_array($request->cp_name) ? count($request->cp_name) : 0;
        $contactPersonId = max($totalCpNames - 1, (int) (@$request->contactPersonId ?? 0));
        for ($j = 0; $j <= $contactPersonId; $j++) {
            $cp_name = trim(@$request->cp_name[$j] ?? '');
            $cp_email = trim(@$request->cp_email[$j] ?? '');
            $cp_desigination = trim(@$request->cp_desigination[$j] ?? '');
            if (! empty($cp_name) && ! empty($cp_email) && ! empty($cp_desigination)) {
                $hasValidContact = true;
                break;
            }
        }
        if (! $hasValidContact) {
            return Reply::error('At least 1 Contact Person with Name, Designation, and Email is required.');
        }

        return null;
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('add_clients'), 403);

        if ($validationError = $this->validateClientSubmission($request)) {
            return $validationError;
        }

        $company_id = $request->user()->company_id;
        $idempotencyKey = $request->header('X-Idempotency-Key') ?? $request->input('idempotency_key');

        $canonicalHash = hash('sha256', json_encode([
            'company_id' => $company_id,
            'name' => trim($request->name ?? ''),
            'website' => trim($request->website ?? ''),
            'industry' => $request->industry,
            'custom_industry' => trim($request->custom_industry ?? ''),
            'company_sizes' => $request->company_sizes,
            'company_type' => $request->company_type,
            'founded' => $request->founded,
            'description' => trim(strip_tags($request->description ?? '')),
            'tags' => $request->tags,
            'address' => $request->address,
            'cp_name' => $request->cp_name,
            'cp_email' => $request->cp_email,
        ]));

        if (! empty($idempotencyKey)) {
            $existing = DB::table('sys_idempotency_keys')
                ->where('tenant_id', $company_id)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                // If the exact same request was completed within the last 5 seconds (rapid double-click), return cached redirect
                if ($existing->status === 'COMPLETED' && $existing->request_hash === $canonicalHash && $existing->completed_at && Carbon::parse($existing->completed_at)->diffInSeconds(now()) < 5) {
                    $cachedBody = json_decode($existing->response_body, true);

                    return Reply::redirect($cachedBody['url'] ?? route('service_company.clients.custom_client').'/0', $cachedBody['message'] ?? (__('menu.clients').' '.__('messages.createdSuccessfully')));
                }

                // If currently processing within the last 15 seconds, reject concurrent race
                if ($existing->status === 'PROCESSING' && $existing->created_at && Carbon::parse($existing->created_at)->diffInSeconds(now()) < 15) {
                    return response()->json(['status' => 'processing', 'message' => 'Client creation is currently being processed. Please wait.'], 425);
                }

                // Otherwise, update existing key record to PROCESSING with new canonical hash (allow legitimate retries)
                DB::table('sys_idempotency_keys')
                    ->where('id', $existing->id)
                    ->update([
                        'request_hash' => $canonicalHash,
                        'status' => 'PROCESSING',
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('sys_idempotency_keys')->insertOrIgnore([
                    'tenant_id' => $company_id,
                    'idempotency_key' => $idempotencyKey,
                    'request_hash' => $canonicalHash,
                    'status' => 'PROCESSING',
                    'expires_at' => now()->addHours(24),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $timeStamp = time();
        $client = new Client;
        $client->company_id = $company_id;

        // FIRST TAB-1
        if (! empty($request->name)) {
            $client->name = $request->name;
        }
        if (! empty($request->website)) {
            $client->website = $request->website;
        }
        $customIndustry = null;
        if (! empty($request->industry)) {
            if (is_numeric($request->industry)) {
                $client->industry = (int) $request->industry;
            } else {
                $client->industry = 0;
                $customIndustry = trim($request->industry);
            }
        }
        if (! empty($request->custom_industry)) {
            $customIndustry = trim($request->custom_industry);
            $client->industry = 0;
        }
        if (! empty($request->company_sizes)) {
            [$from_size, $to_size] = explode('-', $request->company_sizes);
            $client->company_size_from = (int) $from_size;
            $client->company_sizes = ((int) $to_size > 0) ? (int) $to_size : null;
        }
        if (isset($request->company_type) && $request->company_type !== '') {
            $client->company_type = $request->company_type;
        }
        if (! empty($request->founded)) {
            $client->founded = $request->founded;
        }

        // SECOND TAB-2
        if (! empty($request->description)) {
            $client->description = $request->description;
        }
        if (! empty($request->tags)) {
            $tags = $request->tags;
            $tags = json_decode($tags, true);
            $tags = array_column($tags, 'value');
            $tags = implode(',', $tags);
            $client->tags = $tags;
        }

        $detailData = [
            'custom_industry' => $customIndustry ?? null,
            'career_page_url' => $request->career_page_url ?? null,
            'linkedin_url' => $request->linkedin_url ?? null,
            'twitter_url' => $request->twitter_url ?? null,
            'github_url' => $request->github_url ?? null,
            'ap_email' => $request->ap_email ?? null,
            'tax_id' => $request->tax_id ?? null,
            'youtube_url' => $request->youtube_url ?? null,
            'facebook_url' => $request->facebook_url ?? null,
            'instagram_url' => $request->instagram_url ?? null,
            'custom_link' => $request->custom_link ?? null,
        ];
        $filteredDetail = array_filter($detailData, fn ($val) => $val !== null && $val !== '');
        if (! empty($filteredDetail)) {
            $client->detail = json_encode($filteredDetail);
        }

        // Handle logo upload outside database transaction
        $altfilename = null;
        $target_file = null;
        $fileUrl = null;
        if ($request->hasFile('logo')) {
            $directoryPath = GeneralMethod::document_manage_directory($company_id);
            $file = $request->file('logo');
            $ext = strtolower($file->getClientOriginalExtension());
            $allowed = ['png', 'jpeg', 'jpg'];
            if (in_array($ext, $allowed)) {
                $origFilename = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $altfilename = GeneralMethod::getValidUploadFileName($origFilename).'-'.$timeStamp.'.'.$ext;
                $target_file = $directoryPath.'/'.$altfilename;

                $file->move($directoryPath, $altfilename);

                $companyNameR2 = DB::table('companies')
                    ->where('id', $company_id)
                    ->value('company_name');
                $companyNameR2 = strtolower(trim($companyNameR2 ?? 'unknown'));
                $companyNameR2 = preg_replace('/[^a-z0-9_-]/i', '_', $companyNameR2);
                $companyNameRTwo = $companyNameR2.'_'.$company_id;
                $r2Filename = uniqid('JERO_logo').'.'.$ext;
                $fileUrl = "https://file.jero.works/Companies/{$companyNameRTwo}/Images/{$r2Filename}";

                $client->logo = $altfilename;
                $client->logo_physical_path = $target_file;
                $client->logo_logical_path = $fileUrl;
            }
        }

        // Prepare locations outside database transaction
        $Location = [];
        $locationsToInsert = [];
        $totalAddresses = is_array($request->address) ? count($request->address) : 0;
        $maxAddressIndex = max($totalAddresses - 1, (int) (@$request->addressId ?? 0));

        for ($i = 0; $i <= $maxAddressIndex; $i++) {
            $address = @$request->address[$i];
            $postcode = '0';
            $work_city = @$request->kt_tagify_work_location[$i];
            $work_city_all = json_decode($work_city, true);

            if (! empty($work_city) && is_array($work_city_all)) {
                foreach ($work_city_all as $cityVal) {
                    $valStr = trim($cityVal['value'] ?? '');
                    if (empty($valStr)) {
                        continue;
                    }
                    $parts = (strpos($valStr, '|') !== false) ? explode('|', $valStr) : explode(',', $valStr);
                    $cityName = trim($parts[0] ?? '');
                    $countrName = isset($parts[2]) ? trim($parts[2]) : (isset($parts[1]) ? trim($parts[1]) : '');

                    $cityCacheKey = 'city_geo_lookup_'.md5($cityName.'_'.$countrName);
                    $city_row = cache()->remember($cityCacheKey, 86400, function () use ($cityName, $countrName) {
                        $query = DB::table('city_list')
                            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
                            ->join('countries', 'countries.id', '=', 'state_list.country_id')
                            ->select('city_list.city_id', 'countries.id as country_id', 'city_list.state_id')
                            ->where('city_list.city', '!=', '')
                            ->where('city_list.city', '=', $cityName);

                        if (! empty($countrName)) {
                            $query->where('countries.country_name', '=', $countrName);
                        }
                        $row = $query->first();
                        if (! $row) {
                            $row = DB::table('city_list')
                                ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
                                ->join('countries', 'countries.id', '=', 'state_list.country_id')
                                ->select('city_list.city_id', 'countries.id as country_id', 'city_list.state_id')
                                ->where('city_list.city', '!=', '')
                                ->where('city_list.city', '=', $cityName)
                                ->first();
                        }

                        return $row;
                    });

                    if ($city_row) {
                        $cityID = $city_row->city_id;
                        $countryID = $city_row->country_id;
                        $stateId = $city_row->state_id ?? GeneralMethod::stateId($cityID);

                        $values = [
                            'company_id' => $company_id,
                            'country_id' => $countryID,
                            'city' => '',
                            'city_id' => $cityID,
                            'address' => $address,
                            'postcode' => $postcode,
                            'state' => $stateId,
                        ];

                        $locationsToInsert[] = $values;
                        $Location[] = (object) $values;
                    }
                }
            }
        }

        // Prepare contacts outside database transaction
        $contacDetails = [];
        $contactsToInsert = [];
        $seenEmailsInSubmission = [];
        $totalCpNames = is_array($request->cp_name) ? count($request->cp_name) : 0;
        $contactPersonId = max($totalCpNames - 1, (int) (@$request->contactPersonId ?? 0));
        for ($j = 0; $j <= $contactPersonId; $j++) {
            $cp_name = @$request->cp_name[$j];
            $cp_email = @$request->cp_email[$j];
            $cp_number = @$request->cp_number[$j];
            $cp_desigination = @$request->cp_desigination[$j];
            $calling_code = @$request->calling_code[$j];

            if (! empty($cp_email)) {
                $normEmail = strtolower(trim($cp_email));
                if (isset($seenEmailsInSubmission[$normEmail])) {
                    continue; // Defense-in-depth: skip duplicate contact in same submission
                }
                $seenEmailsInSubmission[$normEmail] = true;

                $values = [
                    'contact_person_name' => $cp_name,
                    'contact_person_email' => $cp_email,
                    'contact_person_number' => $cp_number,
                    'calling_code' => $calling_code,
                    'contact_person_designation' => $cp_desigination,
                ];
                $contactsToInsert[] = $values;
                $contacDetails[] = (object) $values;
            }
        }

        if (! empty($contactsToInsert)) {
            $primaryContact = $contactsToInsert[0];
            $client->contact_person_name = substr($primaryContact['contact_person_name'] ?? '', 0, 55);
            $client->email = substr($primaryContact['contact_person_email'] ?? '', 0, 55);
            $client->contact_number = substr($primaryContact['contact_person_number'] ?? '', 0, 55);
        } else {
            $client->contact_person_name = $client->contact_person_name ?? '';
            $client->email = $client->email ?? '';
            $client->contact_number = $client->contact_number ?? '';
        }
        $client->commission = $client->commission ?? '0';
        $client->payout = $client->payout ?? '0';

        DB::beginTransaction();
        try {
            $client->save();
            $client_id = $client->id;

            if (! empty($locationsToInsert)) {
                foreach ($locationsToInsert as &$loc) {
                    $loc['client_id'] = $client_id;
                }
                unset($loc);
                DB::table('client_location')->insert($locationsToInsert);
            }

            if (! empty($contactsToInsert)) {
                foreach ($contactsToInsert as &$cnt) {
                    $cnt['client_id'] = $client_id;
                }
                unset($cnt);
                DB::table('client_contact_info')->insert($contactsToInsert);
            }

            // Transactional Outbox Pattern: Atomic event persistence
            $existingOutbox = ! empty($idempotencyKey)
                ? DB::table('sys_outbox_events')
                    ->where('tenant_id', $company_id)
                    ->where('idempotency_key', $idempotencyKey)
                    ->first()
                : null;

            if ($existingOutbox) {
                $outboxId = $existingOutbox->id;
                DB::table('sys_outbox_events')
                    ->where('id', $outboxId)
                    ->update([
                        'aggregate_id' => $client_id,
                        'aggregate_version' => 1,
                        'event_type' => 'ClientCreated',
                        'payload' => json_encode([
                            'tenant_id' => $company_id,
                            'client_id' => $client_id,
                            'client_name' => $client->name,
                            'action' => 'ClientCreated',
                            'timestamp' => now()->toIso8601String(),
                        ]),
                        'status' => 'PENDING',
                        'attempts' => 0,
                        'available_at' => now(),
                        'updated_at' => now(),
                    ]);
            } else {
                $outboxId = DB::table('sys_outbox_events')->insertGetId([
                    'tenant_id' => $company_id,
                    'idempotency_key' => $idempotencyKey,
                    'aggregate_type' => 'Client',
                    'aggregate_id' => $client_id,
                    'aggregate_version' => 1,
                    'event_type' => 'ClientCreated',
                    'payload' => json_encode([
                        'tenant_id' => $company_id,
                        'client_id' => $client_id,
                        'client_name' => $client->name,
                        'action' => 'ClientCreated',
                        'timestamp' => now()->toIso8601String(),
                    ]),
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => now(),
                    'created_at' => now(),
                ]);
            }

            // Update idempotency key to COMPLETED
            if (! empty($idempotencyKey)) {
                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', $company_id)
                    ->where('idempotency_key', $idempotencyKey)
                    ->update([
                        'status' => 'COMPLETED',
                        'response_status' => 200,
                        'response_body' => json_encode([
                            'status' => 'success',
                            'url' => route('service_company.clients.custom_client').'/0',
                            'message' => __('menu.clients').' '.__('messages.createdSuccessfully'),
                        ]),
                        'completed_at' => now(),
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();
            \Log::info("Client create MySQL write committed successfully for client ID {$client_id}", [
                'client_id' => $client_id,
                'company_id' => $company_id,
                'operation' => 'create',
            ]);

            // Synchronous Elasticsearch Indexing post-commit for authoritative real-time consistency
            $esSyncSuccess = false;
            try {
                $esResult = GeneralMethod::clientStaffingPartner($client_id);
                $esSyncSuccess = true;
                \Log::info("Client ES sync success for client {$client_id}", [
                    'client_id' => $client_id,
                    'index' => ELASTIC_CLIENT_LIST,
                    'operation' => 'create',
                    'result' => $esResult['result'] ?? 'ok',
                ]);

                // Mark outbox event as COMPLETED since synchronous indexing succeeded
                DB::table('sys_outbox_events')
                    ->where('id', $outboxId)
                    ->update([
                        'status' => 'COMPLETED',
                        'processed_at' => now(),
                        'attempts' => 1,
                        'last_error' => null,
                    ]);
            } catch (\Throwable $esEx) {
                \Log::error("Client ES sync failed for client {$client_id}: ".$esEx->getMessage(), [
                    'client_id' => $client_id,
                    'index' => ELASTIC_CLIENT_LIST,
                    'operation' => 'create',
                    'exception' => $esEx,
                ]);

                // Mark outbox event as PENDING with retry info and dispatch job for durable recovery
                DB::table('sys_outbox_events')
                    ->where('id', $outboxId)
                    ->update([
                        'status' => 'PENDING',
                        'last_error' => substr($esEx->getMessage(), 0, 1000),
                        'available_at' => now()->addSeconds(10),
                    ]);

                try {
                    ProcessClientOutboxEventJob::dispatch($outboxId);
                } catch (\Throwable $jobEx) {
                    \Log::warning('ProcessClientOutboxEventJob dispatch warning: '.$jobEx->getMessage());
                }
            }

            // Notification Email post-commit
            try {
                $comp = cache()->remember('comp_profile_'.$company_id, 3600, function () use ($company_id) {
                    return Company::withoutGlobalScopes()->find($company_id);
                });
                $added_in_com_user = cache()->remember('comp_admin_user_'.$company_id, 3600, function () use ($company_id) {
                    return Company::where('companies.id', $company_id)->join('users', 'users.company_id', '=', 'companies.id')->first();
                });
                $mailData = [
                    'Status' => 'Active | Hold | Closed',
                    'ClientID' => $client_id,
                    'CompanyName' => $comp->company_name ?? '',
                    'ClientName' => @$request->name,
                    'addedBy' => @$request->user()->name,
                    'added_in_con_user' => @$added_in_com_user->name,
                    'Location' => $Location,
                    'contacDetails' => $contacDetails,
                ];
                $basecompany = GeneralMethod::baseCompany();
                $senderName = $basecompany->companyName ?? '';
                $fromAddress = $basecompany->companyEmail ?? '';

                QueueNotificationMailService::dispatch(
                    auth()->user()->email,
                    'New Client Added',
                    'email.client.add-client',
                    (object) $mailData,
                    [],
                    $fromAddress,
                    $senderName,
                    [],
                    $company_id
                )->onConnection('emails')->afterCommit();
            } catch (\Throwable $e) {
                \Log::warning('Client creation email notification warning: '.$e->getMessage());
            }

            $userId = auth()->id() ?? ($this->user->id ?? $request->user()?->id ?? 0);
            $cachekeys = [
                'allclients_version_user_'.$userId,
                'allcustomclient_version_user_'.$userId,
                'clientsviewmore_version_user_'.$userId,
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }
            GeneralMethod::cacheClean('tenant_'.$company_id.'_client_version');

            $redirectMessage = $esSyncSuccess
                ? (__('menu.clients').' '.__('messages.createdSuccessfully'))
                : (__('menu.clients').' '.__('messages.createdSuccessfully').' (Search index is syncing in background.)');

            return Reply::redirect(route('service_company.clients.custom_client').'/0', $redirectMessage);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('clients store() transaction failed: '.$e->getMessage(), ['exception' => $e]);

            if (! empty($idempotencyKey)) {
                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', $company_id)
                    ->where('idempotency_key', $idempotencyKey)
                    ->update([
                        'status' => 'FAILED_RETRYABLE',
                        'last_error' => substr($e->getMessage(), 0, 1000),
                        'updated_at' => now(),
                    ]);
            }

            return Reply::dataOnly(['status' => 'error', 'msgType' => 'danger', 'message' => 'Submission failed. Please try again.']);
        }
    }

    public function addresslist($cid)
    {
        $cid = (int) $cid;
        $cacheKey = "client_addresslist_partial_{$cid}";
        $cachedView = cache()->remember($cacheKey, 86400, function () use ($cid) {
            $this->countryArr = GeneralMethod::getAllCountryList();
            $this->cid = $cid;
            $this->cities = GeneralMethod::allCities();
            $this->default_loc = GeneralMethod::allCitiesSelected([]);

            return view('service_company.client.addresslist', $this->data)->render();
        });

        return Reply::dataOnly(['status' => 'success', 'data' => $cachedView]);
    }

    public function request_update(Request $request)
    {
        $company_id = auth()->user()->company_id;
        $user_id = $this->user->id;

        $event_id = $request->input('event_id', $_REQUEST['event_id'] ?? null);
        $from_company = $request->input('from_company', $_REQUEST['from_company'] ?? null);
        $invitation_id = $request->input('invitation_id', $_REQUEST['invitation_id'] ?? null);

        if (! $invitation_id || ! $event_id) {
            return Reply::dataOnly(['status' => 'error', 'message' => 'Invalid parameters']);
        }

        // ── Phase 4 God-Level Concurrency: Redis Atomic Distributed Lock ──
        // Prevents double-clicks, concurrent tabs, or simultaneous requests from executing twice
        $lockKey = "lock:client_req_update:{$company_id}:{$invitation_id}";
        $lock = Cache::lock($lockKey, 8);

        if (! $lock->get()) {
            return Reply::dataOnly([
                'status' => 'success',
                'clientDataCnt' => $this->commonclient(),
                'message' => 'Request is already being processed.',
            ]);
        }

        try {
            // Tenancy & Current State verification (Idempotency Check)
            $invitationRow = DB::table('staffing_company_invitation')
                ->where('id', $invitation_id)
                ->where('to_company_id', $company_id)
                ->first();

            if (! $invitationRow) {
                return Reply::dataOnly(['status' => 'error', 'message' => 'Invitation not found or access denied.']);
            }

            // If already resolved to -1 or 1, return idempotent success immediately
            if (in_array((int) $invitationRow->to_invitation_status, [-1, 1])) {
                return Reply::dataOnly([
                    'status' => 'success',
                    'clientDataCnt' => $this->commonclient(),
                    'message' => ((int) $invitationRow->to_invitation_status === 1) ? 'Request has already been Accepted' : 'Request has already been Ignored',
                ]);
            }

            if ($event_id == -1) {
                // CLIENT INVIDATION IGNORE
                $updateArr = ['to_invitation_status' => -1];
                DB::table('staffing_company_invitation')->where('id', $invitation_id)->limit(1)->update($updateArr);
                $exists = $this->elasticService->getClient()->exists([
                    'index' => ELASTIC_CLIENT_REQUEST,
                    'id' => $invitation_id,
                ]);

                if ($exists) {

                    // 2. Prepare the update timestamp
                    $updateTime = now()->toIso8601String();

                    // 3. Execute update with debugging
                    $updateParams = [
                        'index' => ELASTIC_CLIENT_REQUEST,
                        'id' => $invitation_id,
                        'body' => [
                            'doc' => [
                                'to_invitation_status' => -1,
                                'updated_at' => $updateTime,
                            ],
                            'doc_as_upsert' => false, // Changed to false since we verified existence
                        ],
                        'refresh' => false, // Eventual consistency for 10K concurrent scale
                    ];
                    $this->elasticService->getClient()->update($updateParams);

                }

                // CLIENT RELATION IGNORE
                $updateArr = ['final_status' => -1];
                DB::table('staffing_company_relation')
                    ->where('staffing_company_relation.from_company_id', $from_company)
                    ->where('staffing_company_relation.to_company_id', $company_id)
                    ->update($updateArr);
                // CLIENT JOB IGNORE
                $updateArr = ['final_status' => -1];
                DB::table('recommendation_relation')
                    ->where('recommendation_relation.from_company_id', $from_company)
                    ->where('recommendation_relation.to_company_id', $company_id)
                    ->update($updateArr);

                $clientDataCnt = $this->commonclient();

                $cachekeys = [
                    'allclients_version_user_'.$this->user->id,
                    'allclientrequest_version_user_'.$this->user->id,
                    'alldirectclient_version_user_'.$this->user->id,
                    'allconsultancy_version_user_'.$this->user->id,
                ];
                foreach ($cachekeys as $key) {
                    GeneralMethod::cacheClean($key);
                }
                GeneralMethod::cacheClean('tenant_'.$company_id.'_client_version');
                $newVersion = (int) cache()->get('tenant_'.$company_id.'_client_version', 1);

                return Reply::dataOnly([
                    'status' => 'success',
                    'mutation_version' => $newVersion,
                    'projection_status' => 'syncing',
                    'clientDataCnt' => $clientDataCnt,
                    'message' => 'Request has been Ignored',
                ]);
            } elseif ($event_id == 1) {

                $updateArr = ['to_invitation_status' => $event_id];
                DB::table('staffing_company_invitation')->where('id', $invitation_id)->limit(1)->update($updateArr);

                $exists = $this->elasticService->getClient()->exists([
                    'index' => ELASTIC_CLIENT_REQUEST,
                    'id' => $invitation_id,
                ]);

                if ($exists) {

                    // 2. Prepare the update timestamp
                    $updateTime = now()->toIso8601String();

                    // 3. Execute update with debugging
                    $updateParams = [
                        'index' => ELASTIC_CLIENT_REQUEST,
                        'id' => $invitation_id,
                        'body' => [
                            'doc' => [
                                'to_invitation_status' => 1,
                                'updated_at' => $updateTime,
                            ],
                            'doc_as_upsert' => false, // Changed to false since we verified existence
                        ],
                        'refresh' => false, // Eventual consistency for 10K concurrent scale
                    ];
                    $this->elasticService->getClient()->update($updateParams);

                    $cachekeys = [
                        'allclientrequest_version_user_'.$this->user->id,
                        'allclients_version_user_'.$this->user->id,
                        'alldirectclient_version_user_'.$this->user->id,
                    ];
                    foreach ($cachekeys as $key) {
                        GeneralMethod::cacheClean($key);
                    }
                    GeneralMethod::cacheClean('tenant_'.$company_id.'_client_version');

                }

                $invitationRow = DB::table('staffing_company_invitation')->select('*')->where('id', $invitation_id)->first();

                $clientRow = DB::table('clients')->select('*')->where('company_id', $company_id)->where('self_company', 1)->first();

                $from_company_id = $invitationRow->from_company_id;
                $from_client_id = $invitationRow->from_client_id;
                $from_user_id = $invitationRow->from_user_id;
                $from_status = $invitationRow->from_status;
                $from_created_at = $invitationRow->from_created_at;

                $to_company_id = $invitationRow->to_company_id;
                $to_email = $invitationRow->to_email;
                $to_status = $invitationRow->to_status;
                $job_id = $invitationRow->job_id;
                $to_invitation_status = $invitationRow->to_invitation_status;

                $values = [
                    'from_company_id' => $from_company_id,
                    'from_client_id' => $from_client_id,
                    'from_user_id' => $from_user_id,
                    'from_status' => 1,
                    'from_created_at' => date('Y-m-d'),

                    'to_company_id' => $company_id,
                    'to_client_id' => @$clientRow->id,
                    'to_user_id' => @$user_id,
                    'to_status' => 1,
                    'to_created_at' => date('Y-m-d'),

                    'final_status' => 1,
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                ];

                // ── Phase 4 Idempotency Protection: Ensure relationship is not duplicated ──
                $existingRel = DB::table('staffing_company_relation')
                    ->where('from_company_id', $from_company_id)
                    ->where('to_company_id', $company_id)
                    ->first();

                if (! $existingRel) {
                    DB::table('staffing_company_relation')->insert($values);
                    $relationId = DB::getPdo()->lastInsertId();
                    GeneralMethod::appendClientNestedData($relationId);
                } else {
                    DB::table('staffing_company_relation')
                        ->where('id', $existingRel->id)
                        ->update(['final_status' => 1, 'updated_at' => date('Y-m-d')]);
                }

                $job = Job::withoutGlobalScopes()->find($invitationRow->job_id);
                if ($job) {

                    $detailsVendorSide = GeneralMethod::companyAsVendor($from_company_id, $to_company_id);
                    $basecompany = GeneralMethod::baseCompany();
                    $senderName = (auth()->user()->name) ? auth()->user()->name : '';
                    $fromAddress = (auth()->user()->email) ? auth()->user()->email : '';
                    $attachments = [];

                    $cc = [];
                    $checkMailPermision = GeneralMethod::mailPermission(20);
                    if ($checkMailPermision) {

                        $mailTypeSubject = GeneralMethod::mailTypeSubject(20, 1);

                        $subject = str_replace(['[{VENDOR_COMPANY_NAME}]', '[{JOB_TITLE}]', '[{JOB_ID}]'], [$detailsVendorSide->vendor_company, @$job->title, @$job->id], $mailTypeSubject->subject);

                        $template_description = $mailTypeSubject->body_description;

                        $template_description = str_replace(['[{CLIENT_COMPANY_NAME}]', '[{VENDOR_COMPANY_NAME}]', '[{VENDOR_NAME}]', '[{VENDOR_PHONE}]', '[{VENDOR_EMAIL}]', '[{TERMS}]'], [$detailsVendorSide->client_company, $detailsVendorSide->vendor_company, $detailsVendorSide->vendor_name, $detailsVendorSide->vendor_email, $detailsVendorSide->vendor_phone, ''], $template_description);

                        $message = $template_description;

                        $notificationemail = GeneralMethod::notification($from_company_id, 'Recuitment'); // Recuitment Sales
                        $defailsEmail = DB::table('companies')->where('id', $from_company_id)->first()->company_email;
                        $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
                        //    dd($to_Mail_Id);
                        $data = [
                            'message' => $message,
                        ];

                        QueueNotificationMailService::dispatch(
                            $to_Mail_Id,
                            $subject,
                            'email.common.index',
                            (object) $data,
                            $attachments,
                            $fromAddress,
                            $senderName,
                            $cc,
                            $from_company_id
                        )->onConnection('emails');

                        // $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject, 'email.common.index', (object) $data, $attachments, $fromAddress, $senderName, $cc);
                    }
                }

                $clientDataCnt = $this->commonclient();
                // dd("kd");

                $cachekeys = [
                    'allclients_version_user_'.$this->user->id,
                    'allclientrequest_version_user_'.$this->user->id,
                    'alldirectclient_version_user_'.$this->user->id,
                    'allconsultancy_version_user_'.$this->user->id,
                ];
                foreach ($cachekeys as $key) {
                    GeneralMethod::cacheClean($key);
                }
                GeneralMethod::cacheClean('tenant_'.$company_id.'_client_version');
                $newVersion = (int) cache()->get('tenant_'.$company_id.'_client_version', 1);

                return Reply::dataOnly([
                    'status' => 'success',
                    'mutation_version' => $newVersion,
                    'projection_status' => 'syncing',
                    'clientDataCnt' => $clientDataCnt,
                    'message' => 'Request has been Accepted',
                ]);
            }

        } finally {
            $lock->release();
        }

    }

    /**
     * Fetch detailed client request information for the side drawer view.
     * Optimized to eliminate heavy unused lookup queries (countries, categories, state lists).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestviewmore($id)
    {
        abort_if(Gate::denies('view_clients'), 403);
        $company_id = auth()->user()->company_id;

        $this->companyType = GeneralMethod::accountCompanyType();
        $this->department = GeneralMethod::industries();

        $this->directClientDetail = DB::table('clients')
            ->select(
                'clients.id as client_id',
                'clients.name',
                'clients.logo_logical_path',
                'clients.industry',
                'clients.tags',
                'clients.description',
                'clients.website',
                'companies.logo',
                'companies.company_type',
                'companies.company_size',
                'companies.founded',
                'staffing_company_relation.to_created_at'
            )
            ->join('companies', 'companies.id', '=', 'clients.company_id')
            ->leftJoin('staffing_company_relation', 'staffing_company_relation.from_company_id', '=', 'companies.id')
            ->where(function ($q) use ($id) {
                $q->where('clients.id', '=', $id)
                    ->orWhere('clients.company_id', '=', $id)
                    ->orWhere('companies.id', '=', $id);
            })
            ->first();

        if (! $this->directClientDetail) {
            $comp = DB::table('companies')->where('id', $id)->first();
            $this->directClientDetail = (object) [
                'client_id' => $id,
                'name' => $comp->company_name ?? ('Client #'.$id),
                'company_name' => $comp->company_name ?? ('Client #'.$id),
                'logo_logical_path' => $comp->logo ?? '',
                'logo' => $comp->logo ?? '',
                'industry' => $comp->industries ?? '',
                'tags' => '',
                'description' => $comp->description ?? '',
                'website' => $comp->website ?? '',
                'company_type' => $comp->company_type ?? '',
                'company_size' => $comp->company_size ?? '',
                'founded' => $comp->founded ?? '',
                'to_created_at' => date('Y-m-d H:i:s'),
            ];
        }
        $this->client_file = DB::table('client_file')->select('*')->where('client_id', $id)->get();

        $this->client_location = DB::table('client_location')
            ->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
            ->where('client_location.client_id', $id)
            ->orderBy('client_location.id', 'DESC')
            ->first();

        $this->city_name = '';

        if (! empty($this->client_location->country_id) && ($this->client_location->country_id == 101)) {
            $this->city_obj = DB::table('city_list')->select('city_list.city')->where('city_list.city_id', $this->client_location->city_id)->first();
            $this->city_name = ucwords($this->city_obj->city);
        } else {
            $city = @$this->client_location->city;
            $this->city_name = ucwords($city);
        }

        $this->country_name = '';
        if (@$this->client_location->country_id > 0) {
            $country_name = GeneralMethod::getCountryName($this->client_location->country_id);
            if (count($country_name) == 0) {
                $this->country_name = '';
            } else {
                foreach ($country_name as $cval) {
                    $this->country_name = ucwords($cval['name']);
                }
            }
        }

        $this->client_contact_info = DB::table('client_contact_info')->select('*')->where('client_id', $id)->get();
        $this->active_requisition = DB::table('jobs')->select('jobs.id')->where('jobs.client_id', $id)->where('jobs.status', 'active')->count();

        $view = view('service_company.client.requestviewmore', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    /**
     * Atomically claim an idempotency key using MySQL INSERT ... ON DUPLICATE KEY or insertOrIgnore.
     * Guaranteed safe across concurrent web workers.
     *
     * @param  int|string  $tenantId
     * @return array|null Null if successfully claimed (proceed with mutation); array response if already claimed/completed
     */
    protected function claimIdempotency($tenantId, string $key, array $payload): ?array
    {
        $tenantId = (int) $tenantId;
        $hash = hash('sha256', json_encode($payload));
        $claimed = DB::table('sys_idempotency_keys')->insertOrIgnore([
            'tenant_id' => $tenantId,
            'idempotency_key' => $key,
            'request_hash' => $hash,
            'status' => 'PROCESSING',
            'expires_at' => now()->addHours(24),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (! $claimed) {
            $existing = DB::table('sys_idempotency_keys')
                ->where('tenant_id', $tenantId)
                ->where('idempotency_key', $key)
                ->first();

            if ($existing) {
                if ($existing->request_hash !== $hash) {
                    return ['status' => 'error', 'message' => 'Idempotency key reused with different request payload (mismatch).'];
                }
                if ($existing->status === 'COMPLETED') {
                    $cached = json_decode($existing->response_body, true);
                    if ($cached) {
                        $cached['idempotent_replay'] = true;
                    }

                    return $cached ?? ['status' => 'success', 'idempotent_replay' => true];
                }
                if ($existing->status === 'PROCESSING') {
                    return ['status' => 'warning', 'message' => 'A request is currently being processed with this idempotency key. Please wait a moment.'];
                }
                if ($existing->status === 'FAILED_RETRYABLE') {
                    DB::table('sys_idempotency_keys')
                        ->where('id', $existing->id)
                        ->update([
                            'status' => 'PROCESSING',
                            'request_hash' => $hash,
                            'last_error' => null,
                            'updated_at' => now(),
                        ]);

                    return null;
                }
            }
        }

        return null;
    }

    /**
     * Mark idempotency key as COMPLETED with persisted response payload.
     *
     * @param  int|string  $tenantId
     */
    protected function completeIdempotency($tenantId, string $key, array $response): void
    {
        $tenantId = (int) $tenantId;
        DB::table('sys_idempotency_keys')
            ->where('tenant_id', $tenantId)
            ->where('idempotency_key', $key)
            ->update([
                'status' => 'COMPLETED',
                'response_status' => 200,
                'response_body' => json_encode($response),
                'completed_at' => now(),
                'updated_at' => now(),
            ]);
    }

    /**
     * Mark idempotency key as FAILED_RETRYABLE on error so client can safely retry.
     *
     * @param  int|string  $tenantId
     */
    protected function failIdempotency($tenantId, string $key, string $error): void
    {
        $tenantId = (int) $tenantId;
        DB::table('sys_idempotency_keys')
            ->where('tenant_id', $tenantId)
            ->where('idempotency_key', $key)
            ->update([
                'status' => 'FAILED_RETRYABLE',
                'last_error' => substr($error, 0, 1000),
                'updated_at' => now(),
            ]);
    }
}
