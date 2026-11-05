<?php

/*** ########################################################
Controller    	: 	AdminConsultancyController
Purmose			:
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021
########################################################***/

namespace App\Http\Controllers\ServiceCompany;

use App\Events\ConsultancyPartnershipUpdated;
use App\Exports\ConsultancyExport;
use App\Helper\GeneralMethod;
use App\Helper\Reply;
use App\Jobs\ProcessAgencyOutboxEventJob;
use App\Jobs\QueueNotificationMailService;
use App\Models\Client;
use App\Models\Company;
use App\Models\Country;
use App\Models\JobCategory;
use App\Models\User;
use App\Services\AgencyListingService;
use App\Services\CommercialReadModelService;
use App\Services\CurrencyService;
use App\Services\ElasticService;
use App\Services\MailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ServiceCompanyConsultancyController extends ServiceCompanyBaseController
{
    protected $mailService;

    protected $elasticService;

    protected $agencyListingService;

    public function __construct(MailService $mailService, ElasticService $elasticService, AgencyListingService $agencyListingService)
    {
        parent::__construct();
        $this->pageTitle = __('Staffing Partners');
        $this->pageIcon = 'icon-grid';
        $this->mailService = $mailService;
        $this->elasticService = $elasticService;
        $this->agencyListingService = $agencyListingService;
        view()->share('calling_codes', $this->getCallingCodes());
    }

    public function index(Request $request)
    {
        $company_id = auth()->user()->company_id;
        $this->spartnerDataCnt = $this->agencyListingService->getSummaryCounters($company_id);
        $this->countries = cache()->remember('static_countries_list', now()->addDays(7), function () {
            return DB::table('countries')->orderBy('country_name', 'asc')->get();
        });
        $this->industries = cache()->remember('static_industries_list', now()->addDays(7), function () {
            return DB::table('td_industries')->where('status', 'Y')->orderBy('ind_name', 'asc')->get();
        });
        $this->industriesManifest = \App\Services\IndustryTaxonomyService::getGlobalManifest();
        $this->agencyLocationFacets = $this->agencyListingService->getAgencyLocationFacets($company_id);
        $this->agencyRecencyCounts = $this->agencyListingService->getAgencyRecencyCounts($company_id);
        $this->agencyPipelineCounts = $this->agencyListingService->getAgencyPipelineCounts($company_id);
        $this->initialTab = $request->input('tab', 'all');

        // ── Phase 1 God-Level Optimization: Bootstrap Page 1 data directly into view ──
        try {
            $initialResponse = $this->data_ajax($request);
            $initialData = $initialResponse->getData(true);
            $this->initialHtml = $initialData['html'] ?? '';
            $this->initialTotal = $initialData['total'] ?? 0;
            $this->initialPagination = $initialData['pagination'] ?? '';
        } catch (\Throwable $e) {
            $this->initialHtml = '';
            $this->initialTotal = 0;
            $this->initialPagination = '';
        }

        return view('service_company.consultancy.index', $this->data);
    }

    public function data_ajax(Request $request)
    {
        $company_id = (int) auth()->user()->company_id;
        $searchTerms = request('cus_search');
        $custom_search = $searchTerms;
        $searchQuery = trim((string) $request->input('q', $request->input('search', '')));
        $countryFilter = request('country', request('country_id', ''));
        $cityFilter = request('city', request('city_id', ''));
        $unspecifiedLocation = (bool) request('unspecified_location', false);
        $industryFilter = request('industry', request('industry_id', ''));
        $performanceFilter = request('agency_performance', '');
        $activityFilter = request('activity', '');
        $pipelineFilter = request('pipeline', '');
        $slaFilter = request('response_sla', '');
        $valCheck = request('sort', request('sortingfilter', 'recent'));
        $tab = $request->input('tab') ?: $request->input('active_tab', 'all');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) ($request->input('per_page') ?? $request->input('size') ?? $request->input('limit') ?? 10);
        $from = ($page - 1) * $size;
        $customSearch = trim($custom_search ?: $searchQuery);
        $userId = (int) ($this->user ? $this->user->id : (auth()->id() ?? 0));

        $cachePayload = [
            'page' => $page,
            'size' => $size,
            'q' => $searchQuery,
            'country' => $countryFilter,
            'city' => $cityFilter,
            'unspecified_location' => $unspecifiedLocation,
            'industry' => $industryFilter,
            'activity' => $activityFilter,
            'pipeline' => $pipelineFilter,
            'agency_performance' => $performanceFilter,
            'response_sla' => $slaFilter,
            'company' => $company_id,
            'custom_search' => $customSearch,
            'user_id' => $userId,
            'valCheck' => $valCheck,
            'tab' => $tab,
        ];

        $versionKey = 'allconsultancy_version_user_'.$userId;
        $version = cache()->get($versionKey, 1);
        $hash = md5(json_encode($cachePayload));
        $cacheKeySet = 'ats:allconsultancy_v3:'.$userId.':'.$version.':'.$hash;
        $etag = '"agency-v3-'.$company_id.'-'.$version.'-'.$hash.'"';

        if ($request->header('If-None-Match') === $etag) {
            return response('', 304)->header('ETag', $etag);
        }

        /* ----------------------------
        ⚡ Check DragonflyDB / Redis first
        ----------------------------- */
        if (cache()->has($cacheKeySet)) {
            $cached = cache()->get($cacheKeySet);
            if (! isset($cached['agencyDataCnt'])) {
                $cached['agencyDataCnt'] = $this->agencyListingService->getSummaryCounters($company_id);
            }

            return response()->json($cached)->header('ETag', $etag);
        }

        // Single-flight lock anti-stampede
        $singleFlightLock = null;
        try {
            $lockKey = 'lock:'.$cacheKeySet;
            $singleFlightLock = cache()->lock($lockKey, 5);
            if (! $singleFlightLock->block(2)) {
                $singleFlightLock = null;
                if (cache()->has($cacheKeySet)) {
                    $cached = cache()->get($cacheKeySet);
                    if (! isset($cached['agencyDataCnt'])) {
                        $cached['agencyDataCnt'] = $this->agencyListingService->getSummaryCounters($company_id);
                    }

                    return response()->json($cached)->header('ETag', $etag);
                }
            } else {
                if (cache()->has($cacheKeySet)) {
                    $singleFlightLock->release();
                    $singleFlightLock = null;
                    $cached = cache()->get($cacheKeySet);
                    if (! isset($cached['agencyDataCnt'])) {
                        $cached['agencyDataCnt'] = $this->agencyListingService->getSummaryCounters($company_id);
                    }

                    return response()->json($cached)->header('ETag', $etag);
                }
            }
        } catch (\Throwable $e) {
            $singleFlightLock = null;
        }

        // ── Tab 6: Pending Invitations (`tab === 'request'`) ──
        if ($tab === 'request') {
            $invitationsQuery = DB::table('staffing_company_invitation')
                ->select(
                    'staffing_company_invitation.id',
                    'staffing_company_invitation.to_email',
                    'staffing_company_invitation.to_company_id',
                    'staffing_company_invitation.to_invitation_status',
                    'staffing_company_invitation.from_created_at',
                    'staffing_company_invitation.job_id',
                    'companies.company_name',
                    'companies.logo',
                    'companies.website',
                    'companies.industries',
                    'td_industries.ind_name',
                    'clients.id as client_id',
                    'city_list.city as city_name',
                    'state_list.state as state_name',
                    'countries.country_name'
                )
                ->leftJoin('companies', 'companies.id', '=', 'staffing_company_invitation.to_company_id')
                ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries')
                ->leftJoin('clients', function ($join) {
                    $join->on('clients.company_id', '=', 'companies.id')
                        ->where('clients.self_company', 1);
                })
                ->leftJoin('client_location', 'client_location.client_id', '=', 'clients.id')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
                ->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
                ->where('staffing_company_invitation.from_company_id', $company_id)
                ->where('staffing_company_invitation.to_invitation_status', '!=', 1);

            if (! empty($customSearch)) {
                $invitationsQuery->where(function ($q) use ($customSearch) {
                    $q->where('staffing_company_invitation.to_email', 'like', "%{$customSearch}%")
                        ->orWhere('companies.company_name', 'like', "%{$customSearch}%")
                        ->orWhere('td_industries.ind_name', 'like', "%{$customSearch}%");
                });
            }

            if ($unspecifiedLocation) {
                $invitationsQuery->whereNull('client_location.country_id');
            } elseif (! empty($countryFilter)) {
                $invitationsQuery->where('client_location.country_id', (int) $countryFilter);
                if (! empty($cityFilter)) {
                    if (is_numeric($cityFilter)) {
                        $invitationsQuery->where('client_location.city_id', (int) $cityFilter);
                    } else {
                        $invitationsQuery->where(function ($q) use ($cityFilter) {
                            $q->where('city_list.city', 'like', "%{$cityFilter}%")
                                ->orWhere('client_location.city', 'like', "%{$cityFilter}%");
                        });
                    }
                }
            }

            if (! empty($industryFilter)) {
                $invitationsQuery->where('companies.industries', (int) $industryFilter);
            }

            $total = $invitationsQuery->count();

            // Sort
            if ($valCheck === 'name_asc') {
                $invitationsQuery->orderBy(DB::raw('COALESCE(companies.company_name, staffing_company_invitation.to_email)'), 'asc');
            } elseif ($valCheck === 'name_desc') {
                $invitationsQuery->orderBy(DB::raw('COALESCE(companies.company_name, staffing_company_invitation.to_email)'), 'desc');
            } elseif ($valCheck === 'oldest') {
                $invitationsQuery->orderBy('staffing_company_invitation.id', 'asc');
            } else {
                $invitationsQuery->orderBy('staffing_company_invitation.id', 'desc');
            }

            $invitations = $invitationsQuery->skip($from)->take($size)->get();

            $pagination = [
                'total' => $total,
                'current_page' => $page,
                'per_page' => $size,
                'last_page' => (int) ceil($total / $size),
                'listType' => 'allcandidate',
            ];

            $userPermissions = $this->userPermissions;
            $agencyDataCnt = $this->agencyListingService->getSummaryCounters($company_id);

            $responseData = [
                'html' => view('service_company.ajax_pages.staffing-partner-ajax', [
                    'isRequest' => true,
                    'invitations' => $invitations,
                    'data' => [],
                    'userPermissions' => $userPermissions,
                    'company_id' => $company_id,
                    'batchAgencyStats' => [],
                    'batchLocations' => [],
                    'batchIndustries' => [],
                    'batchRelations' => [],
                    'batchCompanySizes' => [],
                ])->render(),
                'total' => $total,
                'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
                'agencyLocationFacets' => $this->agencyListingService->getAgencyLocationFacets($company_id),
                'agencyRecencyCounts' => $this->agencyListingService->getAgencyRecencyCounts($company_id),
                'agencyPipelineCounts' => $this->agencyListingService->getAgencyPipelineCounts($company_id),
                'agencyDataCnt' => $agencyDataCnt,
            ];

            cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_CONSULTANCY_TIME));
            if ($singleFlightLock) {
                try {
                    $singleFlightLock->release();
                } catch (\Throwable $e) {
                }
            }

            return response()->json($responseData)->header('ETag', $etag);
        }

        // ── Tabs 1 to 5: Connected Agencies (`all`, `newly_added`, `top_performing`, `hiring`, `inactive`) ──
        try {
            if ($this->elasticService->indexExists(ELASTIC_CLIENT_AND_AGENCY_RELATION_INDEX)) {
                $relationRes = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_CLIENT_AND_AGENCY_RELATION_INDEX,
                    'body' => [
                        'size' => 10000,
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['from_company_id' => $company_id]],
                                    ['term' => ['final_status' => 1]],
                                ],
                            ],
                        ],
                        '_source' => ['to_company_id'],
                    ],
                ]);
                $companyIds = collect($relationRes['hits']['hits'] ?? [])
                    ->pluck('_source.to_company_id')
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();
            } else {
                $companyIds = DB::table('staffing_company_relation')
                    ->where('from_company_id', $company_id)
                    ->where('final_status', 1)
                    ->whereNotNull('to_company_id')
                    ->distinct()
                    ->pluck('to_company_id')
                    ->toArray();
            }

            // Tab-specific pre-filtering of company IDs BEFORE Elasticsearch pagination
            if ($tab === 'newly_added') {
                $recentCompanyIds = $this->agencyListingService->getRecentAgencyIds($company_id, 180);
                $companyIds = array_values(array_intersect($recentCompanyIds, $companyIds));
            } elseif ($tab === 'top_performing') {
                $topCompanyIds = $this->agencyListingService->getTopPerformingAgencyIds($company_id, $companyIds);
                $companyIds = array_values(array_intersect($topCompanyIds, $companyIds));
            } elseif ($tab === 'hiring') {
                $activeCompanyIds = $this->agencyListingService->getActiveAgencyIds($company_id, $companyIds);
                $companyIds = array_values(array_intersect($activeCompanyIds, $companyIds));
            } elseif ($tab === 'inactive') {
                $activeCompanyIds = $this->agencyListingService->getActiveAgencyIds($company_id, $companyIds);
                $companyIds = array_values(array_diff($companyIds, $activeCompanyIds));
            }

            // Activity recency filter (Mutually exclusive recency buckets matching Client Tracker UX)
            if ($activityFilter === '7d') {
                $active7dIds = DB::table('job_applications')
                    ->whereIn('thirdparty_company_id', $companyIds)
                    ->where('created_at', '>=', now()->subDays(7))
                    ->distinct()
                    ->pluck('thirdparty_company_id')
                    ->toArray();
                $companyIds = array_values(array_intersect($active7dIds, $companyIds));
            } elseif ($activityFilter === '30d') {
                $active30dIds = DB::table('job_applications')
                    ->whereIn('thirdparty_company_id', $companyIds)
                    ->where('created_at', '>=', now()->subDays(30))
                    ->where('created_at', '<', now()->subDays(7))
                    ->distinct()
                    ->pluck('thirdparty_company_id')
                    ->toArray();
                $companyIds = array_values(array_intersect($active30dIds, $companyIds));
            } elseif ($activityFilter === '90d') {
                $active90dIds = DB::table('job_applications')
                    ->whereIn('thirdparty_company_id', $companyIds)
                    ->where('created_at', '>=', now()->subDays(90))
                    ->where('created_at', '<', now()->subDays(30))
                    ->distinct()
                    ->pluck('thirdparty_company_id')
                    ->toArray();
                $companyIds = array_values(array_intersect($active90dIds, $companyIds));
            } elseif ($activityFilter === 'dormant') {
                $recentIds = DB::table('job_applications')
                    ->whereIn('thirdparty_company_id', $companyIds)
                    ->where('created_at', '>=', now()->subDays(90))
                    ->distinct()
                    ->pluck('thirdparty_company_id')
                    ->toArray();
                $allSubmittedIds = DB::table('job_applications')
                    ->whereIn('thirdparty_company_id', $companyIds)
                    ->distinct()
                    ->pluck('thirdparty_company_id')
                    ->toArray();
                $dormantIds = array_values(array_diff($allSubmittedIds, $recentIds));
                $companyIds = array_values(array_intersect($dormantIds, $companyIds));
            } elseif ($activityFilter === 'never') {
                $anySubmittedIds = DB::table('job_applications')
                    ->whereIn('thirdparty_company_id', $companyIds)
                    ->distinct()
                    ->pluck('thirdparty_company_id')
                    ->toArray();
                $companyIds = array_values(array_diff($companyIds, $anySubmittedIds));
            } elseif ($activityFilter === 'inactive') {
                $active30dIds = DB::table('job_applications')
                    ->whereIn('thirdparty_company_id', $companyIds)
                    ->where('created_at', '>=', now()->subDays(30))
                    ->distinct()
                    ->pluck('thirdparty_company_id')
                    ->toArray();
                $companyIds = array_values(array_diff($companyIds, $active30dIds));
            }

            // Pipeline filter (Support both high-level state: active/none and granular stages)
            if (! empty($pipelineFilter) && $pipelineFilter !== 'all') {
                if ($pipelineFilter === 'active') {
                    $pipelineActiveIds = DB::table('add_job_wise_condidates')
                        ->whereIn('thirdparty_company_id', $companyIds)
                        ->whereIn('master_status_id', [1, 3, 6, 7, 10])
                        ->distinct()
                        ->pluck('thirdparty_company_id')
                        ->toArray();
                    $companyIds = array_values(array_intersect($pipelineActiveIds, $companyIds));
                } elseif ($pipelineFilter === 'none') {
                    $pipelineActiveIds = DB::table('add_job_wise_condidates')
                        ->whereIn('thirdparty_company_id', $companyIds)
                        ->whereIn('master_status_id', [1, 3, 6, 7, 10])
                        ->distinct()
                        ->pluck('thirdparty_company_id')
                        ->toArray();
                    $companyIds = array_values(array_diff($companyIds, $pipelineActiveIds));
                } else {
                    $statusMap = [
                        'sourcing' => [1],
                        'screening' => [3],
                        'interview' => [7],
                        'offers' => [10],
                        'joined' => [6],
                    ];
                    if (isset($statusMap[$pipelineFilter])) {
                        $pipelineCompanyIds = DB::table('add_job_wise_condidates')
                            ->whereIn('thirdparty_company_id', $companyIds)
                            ->whereIn('master_status_id', $statusMap[$pipelineFilter])
                            ->distinct()
                            ->pluck('thirdparty_company_id')
                            ->toArray();
                        $companyIds = array_values(array_intersect($pipelineCompanyIds, $companyIds));
                    }
                }
            }
        } catch (\Exception $e) {
            $companyIds = [];
        }

        $agencyDataCnt = $this->agencyListingService->getSummaryCounters($company_id);

        // Early return if no agencies match
        if (empty($companyIds)) {
            $responseData = [
                'html' => view('service_company.ajax_pages.staffing-partner-ajax', [
                    'data' => [],
                    'isRequest' => false,
                    'userPermissions' => $this->userPermissions,
                    'company_id' => $company_id,
                    'batchAgencyStats' => [],
                    'batchLocations' => [],
                    'batchIndustries' => [],
                    'batchRelations' => [],
                    'batchCompanySizes' => [],
                ])->render(),
                'total' => 0,
                'pagination' => view('service_company.ajax_pages.pagination', [
                    'pagination' => ['total' => 0, 'current_page' => $page, 'last_page' => 0, 'listType' => 'allcandidate'],
                ])->render(),
                'agencyDataCnt' => $agencyDataCnt,
            ];

            cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_CONSULTANCY_TIME));
            if ($singleFlightLock) {
                try {
                    $singleFlightLock->release();
                } catch (\Throwable $e) {
                }
            }

            return response()->json($responseData)->header('ETag', $etag);
        }

        // Sort configuration
        $sort = match ($valCheck) {
            'name_asc' => [
                ['company_name.keyword' => ['order' => 'asc', 'unmapped_type' => 'keyword']],
                ['id' => ['order' => 'desc']],
            ],
            'name_desc' => [
                ['company_name.keyword' => ['order' => 'desc', 'unmapped_type' => 'keyword']],
                ['id' => ['order' => 'desc']],
            ],
            'jobs_desc' => [
                ['active_jobs_count' => ['order' => 'desc', 'unmapped_type' => 'long']],
                ['id' => ['order' => 'desc']],
            ],
            'oldest' => [
                ['id' => ['order' => 'asc']],
            ],
            default => [
                ['id' => ['order' => 'desc']],
            ],
        };

        $must = [];
        $must[] = ['term' => ['self_company' => 1]];
        $must[] = ['terms' => ['company_id' => $companyIds]];

        // Location Filter (Country, City, or Unspecified)
        if ($unspecifiedLocation || $countryFilter === 'unspecified') {
            $must[] = [
                'bool' => [
                    'must_not' => [
                        [
                            'nested' => [
                                'path' => 'client_location',
                                'query' => [
                                    'exists' => ['field' => 'client_location.country_id'],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        } elseif (! empty($countryFilter) && $countryFilter !== 'all') {
            $locFilter = [];
            if (is_numeric($countryFilter)) {
                $locFilter[] = ['term' => ['client_location.country_id' => (int) $countryFilter]];
            } else {
                $locFilter[] = ['term' => ['client_location.country_name' => strtolower(trim($countryFilter))]];
            }

            if (! empty($cityFilter) && $cityFilter !== 'all') {
                if ($cityFilter === 'unspecified') {
                    $locFilter[] = [
                        'bool' => [
                            'must_not' => [
                                ['exists' => ['field' => 'client_location.city_id']],
                            ],
                        ],
                    ];
                } elseif (is_numeric($cityFilter)) {
                    $locFilter[] = ['term' => ['client_location.city_id' => (int) $cityFilter]];
                } else {
                    $locFilter[] = ['term' => ['client_location.city_name' => strtolower(trim($cityFilter))]];
                }
            }

            $must[] = [
                'nested' => [
                    'path' => 'client_location',
                    'query' => [
                        'bool' => [
                            'filter' => $locFilter,
                        ],
                    ],
                ],
            ];
        }

        // Industry Filter
        if (! empty($industryFilter)) {
            $must[] = ['term' => ['industries' => (int) $industryFilter]];
        }

        // Full-Text Search
        if (! empty($customSearch)) {
            $customSearchLower = strtolower($customSearch);
            $shouldSearch = [
                ['match_phrase_prefix' => ['company_name' => $customSearch]],
                ['match_phrase_prefix' => ['name' => $customSearch]],
                ['match_phrase_prefix' => ['td_industries.ind_name' => $customSearch]],
                ['match_phrase_prefix' => ['client_contact_info.contact_person_name' => $customSearch]],
                [
                    'nested' => [
                        'path' => 'client_location',
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

        $params = [
            'index' => ELASTIC_CLIENT_LIST,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => [
                    'bool' => [
                        'must' => $must,
                    ],
                ],
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_CLIENT_LIST)) {
            $total = 0;
            $data = [];
            $batchAgencyStats = [];
            $batchLocations = [];
            $batchIndustries = [];
            $batchRelations = [];
            $batchCompanySizes = [];
        } else {
            try {
                $response = $this->elasticService->getClient()->search($params);
                $total = $response['hits']['total']['value'] ?? 0;
                $data = array_map(function ($hit) {
                    return $hit['_source'];
                }, $response['hits']['hits'] ?? []);

                $agencyCompanyIds = array_filter(array_column($data, 'company_id'));
                $clientIds = array_filter(array_column($data, 'client_id'));
                $industryIds = array_filter(array_column($data, 'industries'));

                $batchAgencyStats = $this->agencyListingService->getBatchAgencyStats($agencyCompanyIds, $company_id);
                $batchLocations = $this->agencyListingService->getBatchLocations($clientIds);
                $batchIndustries = $this->agencyListingService->getBatchIndustries($industryIds);
                $batchRelations = $this->agencyListingService->getBatchRelationData($agencyCompanyIds, $company_id);
                $batchCompanySizes = $this->agencyListingService->getBatchCompanySizes($agencyCompanyIds);
            } catch (\Exception $e) {
                $total = 0;
                $data = [];
                $batchAgencyStats = [];
                $batchLocations = [];
                $batchIndustries = [];
                $batchRelations = [];
                $batchCompanySizes = [];
            }
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'per_page' => $size,
            'last_page' => (int) ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        $userPermissions = $this->userPermissions;
        $responseData = [
            'html' => view('service_company.ajax_pages.staffing-partner-ajax', [
                'data' => $data,
                'isRequest' => false,
                'userPermissions' => $userPermissions,
                'company_id' => $company_id,
                'batchAgencyStats' => $batchAgencyStats,
                'batchLocations' => $batchLocations,
                'batchIndustries' => $batchIndustries,
                'batchRelations' => $batchRelations,
                'batchCompanySizes' => $batchCompanySizes,
            ])->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
            'agencyLocationFacets' => $this->agencyListingService->getAgencyLocationFacets($company_id),
            'agencyRecencyCounts' => $this->agencyListingService->getAgencyRecencyCounts($company_id),
            'agencyPipelineCounts' => $this->agencyListingService->getAgencyPipelineCounts($company_id),
            'agencyDataCnt' => $agencyDataCnt,
        ];

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_CONSULTANCY_TIME));
        if ($singleFlightLock) {
            try {
                $singleFlightLock->release();
            } catch (\Throwable $e) {
            }
        }

        return response()->json($responseData)->header('ETag', $etag);
    }

    public function staffing_invitation(Request $request, $id)
    {
        $staffing_partners_email = $request->input('staffing_partners_email', $_REQUEST['staffing_partners_email'] ?? '');
        $staffing_partners_email = trim((string) $staffing_partners_email);
        $company_id = (int) auth()->user()->company_id;
        $userId = (int) ($this->user->id ?? auth()->id());

        $validator = Validator::make(['staffing_partners_email' => $staffing_partners_email], [
            'staffing_partners_email' => [
                'required',
                'email:rfc,dns',
                'disposable_email',
            ],
        ], [
            'staffing_partners_email.required' => 'Email is required',
            'staffing_partners_email.email' => 'Please enter a valid email',
            'staffing_partners_email.disposable_email' => 'Temporary emails are not allowed',
        ]);

        if ($validator->fails()) {
            return Reply::dataOnly(['status' => 'warning', 'message' => $validator->errors()->first()]);
        }

        // Idempotency: Extract or derive stable key
        $idempotencyKey = $request->header('X-Idempotency-Key')
            ?? $request->input('idempotency_key')
            ?? ('idemp_'.md5($company_id.':'.strtolower($staffing_partners_email).':'.date('YmdHi')));

        $idempotencyPayload = [
            'email' => strtolower($staffing_partners_email),
            'company_id' => $company_id,
        ];

        $idempotencyCheck = $this->claimIdempotency($company_id, $idempotencyKey, $idempotencyPayload);
        if ($idempotencyCheck !== null) {
            return Reply::dataOnly($idempotencyCheck);
        }

        // Concurrency & Idempotency Protection: Atomic lock per tenant + email
        $lockKey = 'lock:staffing_invite:comp_'.$company_id.':'.md5(strtolower($staffing_partners_email));
        $lock = cache()->lock($lockKey, 10); // 10 second safety TTL

        if (! $lock->get()) {
            return Reply::dataOnly([
                'status' => 'warning',
                'message' => 'An invitation request for this email is currently being processed. Please wait a moment.',
            ]);
        }

        // Cooldown Protection: Prevent duplicate invitations to the same email within 30 seconds
        $cooldownKey = 'cooldown:staffing_invite:comp_'.$company_id.':'.md5(strtolower($staffing_partners_email));
        if (cache()->has($cooldownKey)) {
            return Reply::dataOnly([
                'status' => 'warning',
                'message' => 'An invitation was recently sent to this email. Please allow a moment before sending another.',
            ]);
        }

        try {
            $clientRow = DB::table('clients')
                ->select('id', 'name', 'company_id')
                ->where('company_id', $company_id)
                ->where('self_company', 1)
                ->first();

            // Case check: Registered vs Unregistered user (Optimized: single indexed query for existence and root status)
            $userRow = DB::table('users')
                ->select('id', 'company_id', 'root_user', 'email')
                ->where('email', $staffing_partners_email)
                ->first();

            if ($userRow) {
                if ((int) $userRow->root_user === 0) {
                    $message = 'Use the company’s main email ID to add them as a Staffing Partner, not an employee’s email ID.';
                    $this->failIdempotency($company_id, $idempotencyKey, $message);

                    return Reply::dataOnly(['status' => 'warning', 'message' => $message]);
                }

                // CASE 2. DO NOT SEND INTERNAL TEAM MEMBER
                if ($userRow->company_id == $company_id) {
                    $message = 'Can not send internal team member';
                    $this->failIdempotency($company_id, $idempotencyKey, $message);

                    return Reply::dataOnly(['status' => 'warning', 'message' => $message]);
                }

                // CHECK ALREADY LINKED
                $staffingRowCount = DB::table('staffing_company_relation')
                    ->where('from_company_id', $company_id)
                    ->where('to_company_id', $userRow->company_id)
                    ->where('final_status', 1)
                    ->count();

                if ($staffingRowCount > 0) {
                    $message = 'Agency already added';
                    $this->failIdempotency($company_id, $idempotencyKey, $message);

                    return Reply::dataOnly(['status' => 'warning', 'message' => $message]);
                }
            }

            if (empty($staffing_partners_email)) {
                $message = 'Email can not be empty, please enter email';
                $this->failIdempotency($company_id, $idempotencyKey, $message);

                return Reply::dataOnly(['status' => 'warning', 'message' => $message]);
            }

            $invitationId = null;
            $outboxId = null;

            // ACID Database Transaction: Atomic invitation mutation + outbox record
            DB::transaction(function () use (
                $company_id,
                $clientRow,
                $userId,
                $userRow,
                $staffing_partners_email,
                $idempotencyKey,
                &$invitationId,
                &$outboxId
            ) {
                // DELETE ALL OLD INVITATION
                DB::table('staffing_company_invitation')
                    ->where('staffing_company_invitation.from_company_id', $company_id)
                    ->where('staffing_company_invitation.to_email', $staffing_partners_email)
                    ->where('staffing_company_invitation.to_invitation_status', '!=', 1)
                    ->delete();

                $staffing_company_invitation = [
                    'from_client_id' => $clientRow ? $clientRow->id : null,
                    'from_company_id' => $company_id,
                    'from_user_id' => $userId,
                    'from_status' => 1,
                    'from_created_at' => date('Y-m-d'),
                    'to_company_id' => $userRow ? $userRow->company_id : null,
                    'to_email' => $staffing_partners_email,
                    'to_status' => $userRow ? 1 : 0,
                ];

                DB::table('staffing_company_invitation')->insert($staffing_company_invitation);
                $invitationId = (int) DB::getPdo()->lastInsertId();

                $outboxId = DB::table('sys_outbox_events')->insertGetId([
                    'tenant_id' => $company_id,
                    'idempotency_key' => $idempotencyKey,
                    'aggregate_type' => 'AgencyRequest',
                    'aggregate_id' => $invitationId,
                    'aggregate_version' => 1,
                    'event_type' => 'AGENCY_INVITATION_SENT',
                    'payload' => json_encode([
                        'from_company_id' => $company_id,
                        'to_company_id' => $userRow ? $userRow->company_id : null,
                        'to_email' => $staffing_partners_email,
                        'invitation_id' => $invitationId,
                        'user_id' => $userId,
                    ]),
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => now(),
                    'created_at' => now(),
                ]);
            });

            // Asynchronous Outbox Worker Dispatch (offloads ES sync & background delivery)
            if ($outboxId) {
                try {
                    ProcessAgencyOutboxEventJob::dispatch($outboxId)->afterCommit();
                } catch (\Throwable $e) {
                    Log::warning('ProcessAgencyOutboxEventJob dispatch deferred: '.$e->getMessage());
                }
            }

            // Invalidate tenant and user caches
            $this->agencyListingService->clearCompanyStatsCache($company_id);
            $this->agencyListingService->clearUserConsultancyCache($userId);

            // Optimized: Select only required columns instead of full SELECT * on companies
            $Companies = DB::table('companies')->select('id', 'company_name')->where('id', $company_id)->first();
            $encode = base64_encode($invitationId);
            $reqdate = date('d M Y');
            $Url = url('/invitation-company-register/'.$encode);
            $attachments = [];
            $cc = [];
            $basecompany = GeneralMethod::baseCompany();
            $senderName = $basecompany ? ($basecompany->companyName ?: '') : '';
            $fromAddress = $basecompany ? ($basecompany->companyEmail ?: '') : '';
            $checkMailPermision = GeneralMethod::mailPermission(40);

            if ($checkMailPermision && $Companies) {
                $mailTypeSubject = GeneralMethod::mailTypeSubject(40, 1);
                $subject = str_replace(['[COMPANY_NAME]', '[REQUEST_DATE]'], [$Companies->company_name, $reqdate], $mailTypeSubject->subject);
                $template_description = $mailTypeSubject->body_description;

                $AcceptInvite = '<a href="'.$Url.'" rel="noopener" style="text-decoration:none;display:inline-block;text-align:center;padding:0.75575rem 1.3rem;font-size:0.925rem;line-height:1.5;border-radius:0.35rem;color:#ffffff;background-color:#009EF7;border:0px;margin-right:0.75rem!important;font-weight:600!important;outline:none!important;vertical-align:middle">Accept Invite</a>';
                $urlLink = '<a href="'.$Url.'" rel="noopener" style="text-decoration:none;color: #009EF7">'.$Url.'</a>';

                $template_description = str_replace(['[COMPANY_NAME]', '[ACCEPT_INVITE]', '[URL_LINK]'], [$Companies->company_name, $AcceptInvite, $urlLink], $template_description);

                $data = [
                    'message' => $template_description,
                ];

                QueueNotificationMailService::dispatch(
                    $staffing_partners_email,
                    $subject,
                    'email.common.index',
                    (object) $data,
                    $attachments,
                    $fromAddress,
                    $senderName,
                    $cc,
                    $company_id
                )->afterCommit()->onConnection('emails');
            }

            if ($userRow) {
                $message = 'An invitation has been sent to'.$staffing_partners_email.'<br>
Please ask the agency to check their email and accept the invitation.<br>
Once accepted, they can proceed with either of the following:<br>
<ul>
<li> Register on Jero.works, or </li>
<li>If already registered, accept the request from the <b>Client Request tab</b> </li>
</ul><br>
After completing these steps, they will be able to start collaborating with your organization.';
            } else {
                $message = 'Invitation request has been sent on email !';
            }

            $spartnerDataCnt = $this->agencyListingService->getSummaryCounters($company_id);

            try {
                ConsultancyPartnershipUpdated::dispatch(
                    $company_id,
                    'invitation_sent',
                    $spartnerDataCnt['spartnerAll'] ?? null,
                    $spartnerDataCnt['sentRequest'] ?? null
                );
            } catch (\Throwable $e) {
                Log::warning('ConsultancyPartnershipUpdated broadcast deferred: '.$e->getMessage());
            }

            cache()->put($cooldownKey, true, 30);

            $responsePayload = [
                'status' => 'success',
                'message' => $message,
                'spartnerDataCnt' => $spartnerDataCnt,
                'invitation_id' => $invitationId,
            ];

            $this->completeIdempotency($company_id, $idempotencyKey, $responsePayload);

            return Reply::dataOnly($responsePayload);

        } catch (\Throwable $e) {
            $this->failIdempotency($company_id, $idempotencyKey, $e->getMessage());
            Log::error('Staffing invitation mutation failed: '.$e->getMessage(), [
                'company_id' => $company_id,
                'email' => $staffing_partners_email,
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        } finally {
            $lock->release();
        }
    }

    public function requestPartners($id)
    {
        $this->pageTitle = __('Request Partners');
        $company_id = auth()->user()->company_id;
        $this->spartnerDataCnt = $this->agencyListingService->getSummaryCounters($company_id);

        return view('service_company.consultancy.requestPartners', $this->data);
    }

    public function uncleared_request_data()
    {
        $company_id = '';
        $company_id = auth()->user()->company_id;
        $unclearedDataList = DB::table('staffing_company_invitation')
            ->select('staffing_company_invitation.to_email', 'staffing_company_invitation.to_company_id', 'staffing_company_invitation.to_company_id', 'staffing_company_invitation.to_invitation_status', 'companies.company_name', 'companies.industries', 'td_industries.ind_name')
            ->leftJoin('companies', 'companies.id', '=', 'staffing_company_invitation.to_company_id')
            ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries');

        $unclearedDataList = $unclearedDataList->where('staffing_company_invitation.from_company_id', '=', $company_id);
        $unclearedDataList = $unclearedDataList->where('staffing_company_invitation.to_invitation_status', '!=', 1);

        $dataList = $unclearedDataList->get();
        $toCompanyIds = array_filter(array_unique($dataList->pluck('to_company_id')->toArray()));

        $companyTagsGrouped = [];
        $clientsGrouped = [];

        if (! empty($toCompanyIds)) {
            $companyTagsGrouped = DB::table('company_tags')
                ->whereIn('company_id', $toCompanyIds)
                ->get()
                ->groupBy('company_id');

            $clientsGrouped = DB::table('clients')
                ->select('clients.id', 'clients.company_id', 'clients.tags')
                ->whereIn('clients.company_id', $toCompanyIds)
                ->where('clients.self_company', 1)
                ->get()
                ->keyBy('company_id');
        }

        return DataTables::of($dataList)
            ->editColumn('chkbox', function ($row) use ($companyTagsGrouped, $clientsGrouped) {

                $cname = $btn = $to_company_id = $to_invitation_status = $tags_html = '';

                $cname = $row->company_name;
                if ($cname == '') {
                    $cname = $row->to_email;
                }
                $cname = ucwords($cname);

                $bgTextDecore = $strVal = '';
                $str = trim(@$cname);
                $str = $strVal = strtolower($str[0]);
                $str = ord($str);
                if ($str >= 97 && $str <= 101) {
                    $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                } elseif ($str >= 102 && $str <= 106) {
                    $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                } elseif ($str >= 107 && $str <= 111) {
                    $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                } elseif ($str >= 112 && $str <= 116) {
                    $bgTextDecore = 'fs-3 bg-light-success text-success';
                } elseif ($str >= 117 && $str <= 121) {
                    $bgTextDecore = 'fs-3 bg-dark text-secondary';
                } else {
                    $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                }

                if ($row->to_invitation_status == -1) {
                    $btn = '<a href="javascript:void(0);" type="button" class="btn btn-sm btn-light fw-bolder me-3 fs-6" style="color:#FF0000">Ignore Request</a>';
                } else {
                    if ($row->to_company_id > 0) {
                        $btn = '<a href="javascript:void(0);" type="button" class="btn btn-sm btn-light fw-bolder me-3 fs-6">Pending Request</a>';
                    } else {
                        $btn = '<a href="javascript:void(0);" type="button" class="btn btn-sm btn-light fw-bolder me-3 fs-6">Waiting For Registration</a>';
                    }
                }

                $industries = $ind_name = '&nbsp;&nbsp;';
                $industries = $row->industries;
                if ($industries > 0) {
                    $ind_name = $row->ind_name;
                }

                $to_company_id = $tags_html = '';
                if ($row->to_company_id > 0) {
                    if (isset($companyTagsGrouped[$row->to_company_id])) {
                        foreach ($companyTagsGrouped[$row->to_company_id] as $val) {
                            $tags_html .= '<span class="badge badge-light my-1">'.$val->tag_name.'</span> ';
                        }
                    }

                    if (isset($clientsGrouped[$row->to_company_id])) {
                        $tags = $clientsGrouped[$row->to_company_id]->tags;
                        if (! empty($tags)) {
                            $tagsArr = explode(',', $tags);
                            foreach ($tagsArr as $val) {
                                $tags_html .= '<span class="badge badge-light my-1 me-2">'.$val.'</span>';
                            }
                        }
                    }
                }

                return '<div class="card mb-5 mb-xl-5 mx-2">
  <div class="card-body px-5">
	<div class="d-flex flex-wrap flex-sm-nowrap mb-3">
	  <div class="me-7 mb-4">
		<div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative"> <a href="javascript:void(0);">
		  <div class="symbol-label bg-light-primary text-primary border-radius-5 fs-40 '.$bgTextDecore.'">'.ucwords($strVal).'</div>
		  </a>
		  <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
		</div>
	  </div>
	  
	  <div class="flex-grow-1">
		<div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
		  <div class="d-flex flex-column">
			<div class="d-flex align-items-center mb-2"> 
				<span class="text-gray-900 fs-2 fw-bolder me-2"> '.$cname.' </span> 
<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="Verified"> <span class="svg-icon svg-icon-1 svg-icon-success">
<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
<path d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z" fill="#00A3FF"></path>
<path class="permanent" d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z" fill="white"></path>
</svg>
</span> 
</a> 
			  </div>
			  
			<div class="d-flex flex-wrap fw-bold fs-6 pe-2"> 
			
			<a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2"> <span class="svg-icon svg-icon-4 me-1">
			  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
				<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
				  <rect x="0" y="0" width="24" height="24"/>
				  <path d="M13.5,21 L13.5,18 C13.5,17.4477153 13.0522847,17 12.5,17 L11.5,17 C10.9477153,17 10.5,17.4477153 10.5,18 L10.5,21 L5,21 L5,4 C5,2.8954305 5.8954305,2 7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,21 L13.5,21 Z M9,4 C8.44771525,4 8,4.44771525 8,5 L8,6 C8,6.55228475 8.44771525,7 9,7 L10,7 C10.5522847,7 11,6.55228475 11,6 L11,5 C11,4.44771525 10.5522847,4 10,4 L9,4 Z M14,4 C13.4477153,4 13,4.44771525 13,5 L13,6 C13,6.55228475 13.4477153,7 14,7 L15,7 C15.5522847,7 16,6.55228475 16,6 L16,5 C16,4.44771525 15.5522847,4 15,4 L14,4 Z M9,8 C8.44771525,8 8,8.44771525 8,9 L8,10 C8,10.5522847 8.44771525,11 9,11 L10,11 C10.5522847,11 11,10.5522847 11,10 L11,9 C11,8.44771525 10.5522847,8 10,8 L9,8 Z M9,12 C8.44771525,12 8,12.4477153 8,13 L8,14 C8,14.5522847 8.44771525,15 9,15 L10,15 C10.5522847,15 11,14.5522847 11,14 L11,13 C11,12.4477153 10.5522847,12 10,12 L9,12 Z M14,12 C13.4477153,12 13,12.4477153 13,13 L13,14 C13,14.5522847 13.4477153,15 14,15 L15,15 C15.5522847,15 16,14.5522847 16,14 L16,13 C16,12.4477153 15.5522847,12 15,12 L14,12 Z" fill="#000000"/>
				  <rect fill="#FFFFFF" x="13" y="8" width="3" height="3" rx="1"/>
				  <path d="M4,21 L20,21 C20.5522847,21 21,21.4477153 21,22 L21,22.4 C21,22.7313708 20.7313708,23 20.4,23 L3.6,23 C3.26862915,23 3,22.7313708 3,22.4 L3,22 C3,21.4477153 3.44771525,21 4,21 Z" fill="#000000" opacity="0.3"/>
				</g>
			  </svg>
			  </span> '.$ind_name.' </a> 
			    
			  <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2"> <span class="svg-icon svg-icon-4 me-1">
			  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
				<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
				  <polygon points="0 0 24 0 24 24 0 24"/>
				  <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
				  <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
				</g>
			  </svg>
			  </span> Badge </a> </div>
		  </div>
		  <div class="d-flex mt-4">'.$btn.'</div>
		</div>
		
		<div class="d-flex flex-wrap flex-stack">
		  <div class="d-flex flex-column flex-grow-1 pe-8">
			<div class="row w-75"> 
				<span class=""> 
					'.$tags_html.'
				</span> 
			</div>
		  </div>
		</div>
		
	  </div>
	</div>
	<div class="separator separator-solid my-7 d-none"></div>
  </div>
</div>';

            })
            ->rawColumns(['chkbox'])
            ->addIndexColumn()
            ->make(true);

    }

    public function suggestedPartners($id)
    {
        $company_id = auth()->user()->company_id;
        $this->spartnerDataCnt = $this->agencyListingService->getSummaryCounters($company_id);
        $this->pageTitle = __('Suggested Partners');

        return view('service_company.consultancy.suggestedPartners', $this->data);
    }

    /**
     * Resolves and verifies that the authenticated company has an active relation with target agency (by client_id or company_id).
     */
    protected function resolveAuthorizedAgencyCompanyId(int $authCompanyId, $rawId): ?int
    {
        if (empty($rawId)) {
            return null;
        }

        $rawId = (int) $rawId;

        // 1. Direct check: is $rawId ALREADY a company_id with active agency relation?
        // Invariant: In Agency Drawer, authenticated company is strictly the Direct Client (from_company_id),
        // and target partner is strictly the Agency (to_company_id). Reciprocal relations MUST NOT cross-authorize.
        $isCompanyRelation = DB::table('staffing_company_relation')
            ->where('final_status', 1)
            ->where('from_company_id', $authCompanyId)
            ->where('to_company_id', $rawId)
            ->exists()
            || DB::table('recommendation_relation')
                ->whereIn('final_status', [1, 2])
                ->where('from_company_id', $authCompanyId)
                ->where('to_company_id', $rawId)
                ->exists();

        if ($isCompanyRelation) {
            return $rawId;
        }

        // 2. Client ID check: is $rawId a clients.id mapping to a company_id with active agency relation?
        $mappedCompanyId = (int) DB::table('clients')->where('id', $rawId)->value('company_id');
        if ($mappedCompanyId > 0) {
            $isMappedRelation = DB::table('staffing_company_relation')
                ->where('final_status', 1)
                ->where('from_company_id', $authCompanyId)
                ->where('to_company_id', $mappedCompanyId)
                ->exists()
                || DB::table('recommendation_relation')
                    ->whereIn('final_status', [1, 2])
                    ->where('from_company_id', $authCompanyId)
                    ->where('to_company_id', $mappedCompanyId)
                    ->exists();

            if ($isMappedRelation) {
                return $mappedCompanyId;
            }
        }

        return null;
    }

    public function dviewMore($id)
    {
        $company_id = auth()->user()->company_id;

        $partnerCompanyId = $this->resolveAuthorizedAgencyCompanyId((int) $company_id, $id);

        if (! $partnerCompanyId) {
            abort(404, 'Partner not found or unauthorized.');
        }

        $previousUrl = url()->previous();
        $this->all_tab_show = false;
        if (! str_contains($previousUrl, 'find-agency')) {
            $this->all_tab_show = true;
        }

        $userId = auth()->user()->id ?? 0;
        $this->company_id = $company_id;
        $cachePayload = [
            'id' => $id,
            'partner_company_id' => $partnerCompanyId,
            'company' => $company_id,
            'user_id' => $userId,
        ];

        $versionKey = 'consultancyviewmore_version_user_'.$userId;
        $version = cache()->get($versionKey, 1);
        $partnerVersion = (int) cache()->get("agency_drawer_version_company_{$partnerCompanyId}", 1);
        $cacheKeySet = 'consultancyviewmore_v33:'.$userId.':'.$version.':partner_'.$partnerCompanyId.'_v'.$partnerVersion.':'.md5(json_encode($cachePayload));
        /* ----------------------------
        ⚡ Check DragonflyDB / Redis first (Quick Win Step 4)
        ----------------------------- */
        if (cache()->has($cacheKeySet)) {
            return cache()->get($cacheKeySet);
        }

        $this->countries = Country::all();
        $this->categories = JobCategory::all();
        $this->companyType = GeneralMethod::industries_type_arr(); // GeneralMethod::accountCompanyType();
        $this->department = GeneralMethod::department();
        $this->industries = GeneralMethod::industries();
        $this->countryArr = GeneralMethod::getAllCountryList();
        $this->statelist = GeneralMethod::statelist(101);

        // Resolve actual client entity row ID for the authorized partner agency
        $actualClientId = (int) DB::table('clients')->where('company_id', $partnerCompanyId)->value('id') ?: (int) $id;

        $this->directClientDetail = DB::table('clients')
            ->select(
                'companies.id as company_id', 'companies.description as companies_description',
                'clients.id as client_id', 'clients.name', 'clients.logo_logical_path', 'clients.industry', 'clients.tags', 'clients.website', 'clients.description', 'clients.detail',
                'companies.id as companies_id', 'companies.logo', 'companies.company_type', 'companies.company_size_from', 'companies.company_size', 'companies.founded',
                'companies.success_per', 'companies.company_phone', 'companies.calling_code', 'companies.company_email',
                'staffing_company_relation.created_at as to_created_at', 'staffing_company_relation.from_company_id', 'staffing_company_relation.to_company_id', 'companies.industries_type', 'companies.created_at', 'companies.website', 'companies.industries',
                'companies.terms', 'companies.offer_terms', 'companies.contract_offer_terms', 'companies.agency_offer_terms'
            )
            ->join('companies', 'companies.id', '=', 'clients.company_id')
            ->leftJoin('staffing_company_relation', function ($join) use ($company_id) {
                $join->on('staffing_company_relation.to_company_id', '=', 'companies.id')
                    ->where('staffing_company_relation.from_company_id', '=', $company_id);
            })
            ->where('clients.id', '=', $actualClientId)
            ->first();

        $this->client_file = DB::table('client_file')->select('*')->where('client_id', $actualClientId)->get();
        $this->client_location = DB::table('client_location')
            ->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
            ->where('client_location.client_id', $actualClientId)
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
            foreach ($country_name as $cval) {
                $this->country_name = ucwords($cval['name']);
            }
        }

        // Resolve authoritative viewer company details & agency captain
        $viewerCompany = DB::table('companies')->where('id', $company_id)->first();
        $this->viewerCompanyName = $viewerCompany->company_name ?? ($viewerCompany->name ?? 'Direct Client');
        $this->viewerCompanyLogo = ! empty($viewerCompany->logo_logical_path) ? $viewerCompany->logo_logical_path : ($viewerCompany->logo ?? null);

        $this->agencyCaptainName = DB::table('job_assign')
            ->join('users', 'users.id', '=', 'job_assign.spok_user_id')
            ->where('job_assign.company_id', $partnerCompanyId)
            ->where('job_assign.spok_user_id', '>', 0)
            ->value('users.name')
            ?: (DB::table('users')
                ->join('recommendation_relation', 'recommendation_relation.to_user_id', '=', 'users.id')
                ->where('recommendation_relation.from_company_id', $company_id)
                ->where('recommendation_relation.to_company_id', $partnerCompanyId)
                ->where('users.company_id', $partnerCompanyId)
                ->value('users.name')
            ?: (DB::table('users')->where('company_id', $partnerCompanyId)->where('is_captain', 1)->value('name')
            ?: (DB::table('users')->where('company_id', $partnerCompanyId)->where('root_user', 1)->value('name')
            ?: (DB::table('users')->where('company_id', $partnerCompanyId)->orderBy('id', 'asc')->value('name')
            ?: 'Agency Captain'))));

        // ── 1. Requisitions Status Breakdown & Shared Jobs List ──
        $this->active_requisition = 0;
        $this->Active = 0;
        $this->Hold = 0;
        $this->Closed = 0;
        $this->kpiTotalJobs = 0;

        $requisitionRow = DB::table('recommendation_relation')
            ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
            ->select(
                DB::raw("SUM(CASE WHEN jobs.status = 'active'  THEN 1 ELSE 0 END) AS active"),
                DB::raw("SUM(CASE WHEN jobs.status = 'hold'  THEN 1 ELSE 0 END) AS hold"),
                DB::raw("SUM(CASE WHEN jobs.status = 'inactive'  THEN 1 ELSE 0 END) AS inactive")
            )
            ->where('recommendation_relation.from_company_id', '=', $company_id)
            ->where('recommendation_relation.to_company_id', '=', $partnerCompanyId)
            ->where('recommendation_relation.final_status', '=', 1)
            ->first();

        if ($requisitionRow) {
            $this->Active = (int) ($requisitionRow->active ?? 0);
            $this->Hold = (int) ($requisitionRow->hold ?? 0);
            $this->Closed = (int) ($requisitionRow->inactive ?? 0);
            $this->active_requisition = $this->Active;
            $this->kpiTotalJobs = $this->Active + $this->Hold + $this->Closed;
        }

        // Resolve all job IDs shared in this specific relationship (unidirectional: Direct Client -> Agency)
        $sharedJobIds = DB::table('recommendation_relation')
            ->where('from_company_id', $company_id)
            ->where('to_company_id', $partnerCompanyId)
            ->where('final_status', 1)
            ->pluck('job_id')
            ->unique()
            ->toArray();

        $this->sharedJobs = DB::table('recommendation_relation')
            ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
            ->leftJoin('job_locations', 'job_locations.id', '=', 'jobs.location_id')
            ->leftJoin('clients', 'clients.id', '=', 'jobs.client_id')
            ->select(
                'jobs.id',
                'jobs.title',
                'jobs.status',
                'jobs.total_positions',
                'jobs.min_salary',
                'jobs.max_salary',
                'jobs.min_exp',
                'jobs.max_exp',
                'jobs.created_at',
                'jobs.client_id',
                'clients.name as internal_client_name',
                'clients.logo as internal_client_logo',
                'clients.logo_logical_path as internal_client_logo_logical_path',
                'job_locations.location as city_name',
                'recommendation_relation.created_at as shared_at'
            )
            ->where('recommendation_relation.from_company_id', '=', $company_id)
            ->where('recommendation_relation.to_company_id', '=', $partnerCompanyId)
            ->where('recommendation_relation.final_status', 1)
            ->orderBy('recommendation_relation.id', 'desc')
            ->limit(50)
            ->get();

        // ── 2. Candidates Pipeline Aggregation ──
        $candQuery = DB::table('add_job_wise_condidates')
            ->select(
                DB::raw('COUNT(CASE WHEN master_status_id = 1 THEN 1 END) AS sourcing_count'),
                DB::raw('COUNT(CASE WHEN master_status_id = 2 THEN 1 END) AS screening_count'),
                DB::raw('COUNT(CASE WHEN master_status_id = 3 THEN 1 END) AS evaluation_count'),
                DB::raw('COUNT(CASE WHEN master_status_id = 4 THEN 1 END) AS negotiation_count'),
                DB::raw('COUNT(CASE WHEN master_status_id = 5 THEN 1 END) AS offer_count'),
                DB::raw('COUNT(CASE WHEN master_status_id = 6 THEN 1 END) AS joined_count'),
                DB::raw('COUNT(CASE WHEN master_status_id = 7 THEN 1 END) AS rejected_count'),
                DB::raw('COUNT(CASE WHEN master_status_id = 8 THEN 1 END) AS inreview_count'),
                DB::raw('COUNT(*) AS total_count')
            )
            ->where(function ($q) use ($company_id) {
                $q->where('company_id', '=', $company_id)
                    ->orWhere('company_id', '=', 0)
                    ->orWhereNull('company_id');
            })
            ->where('thirdparty_company_id', '=', $partnerCompanyId)
            ->whereNotIn('master_status_id', [8, 9]);

        if (! empty($sharedJobIds)) {
            $candQuery->whereIn('job_id', $sharedJobIds);
        } else {
            $candQuery->whereRaw('1=0');
        }

        $dbCandStats = $candQuery->first();

        $this->all_candidates = $dbCandStats ? (int) $dbCandStats->total_count : 0;
        $this->sourcing_count = $dbCandStats ? (int) $dbCandStats->sourcing_count : 0;
        $this->screening_count = $dbCandStats ? (int) $dbCandStats->screening_count : 0;
        $this->evaluation_count = $dbCandStats ? (int) $dbCandStats->evaluation_count : 0;
        $this->negotiation_count = $dbCandStats ? (int) $dbCandStats->negotiation_count : 0;
        $this->offers = $dbCandStats ? (int) $dbCandStats->offer_count : 0;
        $this->Joined = $dbCandStats ? (int) $dbCandStats->joined_count : 0;
        $this->Rejected_count = $dbCandStats ? (int) $dbCandStats->rejected_count : 0;
        $this->inreview_count = 0; // Strict in-review exclusion from candidate pipeline
        $this->joined_count = $this->Joined;
        $this->offer_count = $this->offers;
        $this->total_candidates = $this->all_candidates;

        // ── 3. Submitted Candidates List ──
        $candListQuery = DB::table('job_applications')
            ->leftJoin('jobs', 'jobs.id', '=', 'job_applications.job_id')
            ->leftJoin('clients', 'clients.id', '=', 'jobs.client_id')
            ->leftJoin('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', '=', 'job_applications.id')
            ->select(
                'job_applications.id',
                'job_applications.full_name',
                'job_applications.email',
                'job_applications.phone',
                'job_applications.total_exp',
                'job_applications.current_company',
                'job_applications.current_designation',
                'job_applications.ctc',
                'job_applications.ectc',
                'job_applications.created_at',
                'jobs.title as job_title',
                'jobs.id as job_id',
                'jobs.client_id',
                'clients.name as internal_client_name',
                'clients.logo as internal_client_logo',
                'add_job_wise_condidates.master_status_id'
            )
            ->where(function ($q) use ($company_id) {
                $q->where('job_applications.company_id', '=', $company_id)
                    ->orWhere('add_job_wise_condidates.company_id', '=', $company_id);
            })
            ->where(function ($q) use ($partnerCompanyId) {
                $q->where('add_job_wise_condidates.thirdparty_company_id', '=', $partnerCompanyId)
                    ->orWhere('job_applications.thirdparty_company_id', '=', $partnerCompanyId);
            })
            ->whereNotIn('add_job_wise_condidates.master_status_id', [8, 9]);

        if (! empty($sharedJobIds)) {
            $candListQuery->whereIn('job_applications.job_id', $sharedJobIds);
        } else {
            $candListQuery->whereRaw('1=0');
        }

        $this->candidatesList = $candListQuery
            ->orderBy('job_applications.id', 'desc')
            ->limit(50)
            ->get();

        // ── 4. Interviews Schedule (Workflow: Job shared with agency + Agency candidate + Scheduled interview) ──
        $this->interviewsList = collect([]);
        if (! empty($sharedJobIds)) {
            $this->interviewsList = DB::table('interview_schedules')
                ->join('job_applications', 'job_applications.id', '=', 'interview_schedules.job_application_id')
                ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', '=', 'job_applications.id')
                ->leftJoin('jobs', 'jobs.id', '=', 'add_job_wise_condidates.job_id')
                ->leftJoin('clients', 'clients.id', '=', 'jobs.client_id')
                ->select(
                    'interview_schedules.id',
                    'interview_schedules.schedule_date',
                    'interview_schedules.schedule_from_time as schedule_time',
                    'interview_schedules.status',
                    'interview_schedules.url_link as meeting_url',
                    'interview_schedules.stage as interview_stage',
                    'interview_schedules.interviewers as interviewers_text',
                    'interview_schedules.feedback',
                    'job_applications.id as candidate_id',
                    'job_applications.full_name as candidate_name',
                    'jobs.id as job_id',
                    'jobs.title as job_title',
                    'jobs.client_id',
                    'clients.name as internal_client_name',
                    'clients.logo as internal_client_logo'
                )
                ->where(function ($q) use ($company_id) {
                    $q->where('interview_schedules.company_id', '=', $company_id)
                        ->orWhere('add_job_wise_condidates.company_id', '=', $company_id);
                })
                ->where(function ($q) use ($partnerCompanyId) {
                    $q->where('add_job_wise_condidates.thirdparty_company_id', '=', $partnerCompanyId)
                        ->orWhere('job_applications.thirdparty_company_id', '=', $partnerCompanyId);
                })
                ->whereIn('add_job_wise_condidates.job_id', $sharedJobIds)
                ->orderBy('interview_schedules.schedule_date', 'desc')
                ->limit(50)
                ->get();
        }
        $this->all_interview = count($this->interviewsList);

        // ── 4b. Offers & Joinings (Workflow: Job shared with agency + Agency candidate + Offered / Joined) ──
        $this->offersJoiningList = collect([]);
        if (! empty($sharedJobIds)) {
            $this->offersJoiningList = DB::table('add_job_wise_condidates')
                ->join('job_applications', 'job_applications.id', '=', 'add_job_wise_condidates.job_application_id')
                ->leftJoin('jobs', 'jobs.id', '=', 'add_job_wise_condidates.job_id')
                ->leftJoin('clients', 'clients.id', '=', 'jobs.client_id')
                ->leftJoin('offer_job_applications', function ($join) {
                    $join->on('offer_job_applications.application_id', '=', 'job_applications.id')
                        ->whereNull('offer_job_applications.deleted_at');
                })
                ->leftJoin('offer_create_main_tds', function ($join) {
                    $join->on('offer_create_main_tds.offer_id', '=', 'offer_job_applications.id')
                        ->whereIn('offer_create_main_tds.status', [1, 3]);
                })
                ->leftJoin('job_locations', 'job_locations.id', '=', 'jobs.location_id')
                ->select(
                    'job_applications.id as candidate_id',
                    'job_applications.full_name as candidate_name',
                    'job_applications.current_designation',
                    'job_applications.ctc as candidate_ctc',
                    'job_applications.ectc as candidate_ectc',
                    'job_applications.serving_notice_period_mode',
                    'jobs.id as job_id',
                    'jobs.title as job_title',
                    'jobs.employment_type',
                    'jobs.work_type',
                    'jobs.client_id',
                    'clients.name as internal_client_name',
                    'clients.logo as internal_client_logo',
                    'job_locations.location as job_location',
                    'add_job_wise_condidates.master_status_id',
                    'offer_job_applications.id as offer_id',
                    'offer_job_applications.offer_amount',
                    'offer_job_applications.offer_currency',
                    'offer_job_applications.offer_mode',
                    'offer_job_applications.offer_release',
                    'offer_job_applications.offer_acceptance',
                    'offer_job_applications.joined',
                    'offer_job_applications.joined_date',
                    'offer_job_applications.updated_at as offer_updated_at',
                    'offer_job_applications.created_at as offer_created_at',
                    'offer_create_main_tds.send_approved_date_time',
                    'offer_create_main_tds.send_accept_date_time',
                    'offer_create_main_tds.accept_date_time',
                    'offer_create_main_tds.salary_ctc as tds_salary_ctc',
                    'offer_create_main_tds.joined_date as tds_joined_date'
                )
                ->where(function ($q) use ($company_id) {
                    $q->where('add_job_wise_condidates.company_id', '=', $company_id)
                        ->orWhere('job_applications.company_id', '=', $company_id);
                })
                ->where(function ($q) use ($partnerCompanyId) {
                    $q->where('add_job_wise_condidates.thirdparty_company_id', '=', $partnerCompanyId)
                        ->orWhere('job_applications.thirdparty_company_id', '=', $partnerCompanyId);
                })
                ->whereIn('add_job_wise_condidates.job_id', $sharedJobIds)
                ->where(function ($q) {
                    $q->whereIn('add_job_wise_condidates.master_status_id', [5, 6])
                        ->orWhereNotNull('offer_job_applications.id');
                })
                ->orderBy('add_job_wise_condidates.id', 'desc')
                ->limit(50)
                ->get();
        }

        $actualOffersCount = 0;
        $actualJoinedCount = 0;
        foreach ($this->offersJoiningList as $oj) {
            if ((int) $oj->master_status_id === 6 || (int) $oj->joined === 1) {
                $actualJoinedCount++;
            } else {
                $actualOffersCount++;
            }
        }
        $this->offers = $actualOffersCount;
        $this->Joined = $actualJoinedCount;
        $this->offer_count = $actualOffersCount;
        $this->joined_count = $actualJoinedCount;

        $this->client_contact_info = DB::table('roles')
            ->select('users.*')
            ->join('role_user', 'role_user.role_id', '=', 'roles.id')
            ->join('users', 'users.id', '=', 'role_user.user_id')
            ->where('roles.company_id', '=', $partnerCompanyId)
            ->where('roles.name', '=', 'admin')
            ->where('roles.display_name', '=', 'Administrator')
            ->get();

        // ── 4c. Agency Contacts (Rule 20 Sub-Card Engine) ──
        $contactsMap = [];
        $dbContacts = DB::table('client_contact_info')->where('client_id', $actualClientId)->get();
        foreach ($dbContacts as $c) {
            $cName = trim((string) $c->contact_person_name);
            $cEmail = trim((string) $c->contact_person_email);
            $cPhone = trim((string) $c->contact_person_number);
            $cCode = trim((string) $c->calling_code);
            $fullPhone = $cPhone ? ($cCode ? $cCode.' '.$cPhone : $cPhone) : null;
            $cDesig = trim((string) $c->contact_person_designation);
            $key = $cEmail ?: $cName;
            if (! empty($key)) {
                $contactsMap[$key] = [
                    'name' => $cName ?: 'Agency Representative',
                    'designation' => $cDesig ?: null,
                    'phone' => $fullPhone,
                    'email' => $cEmail ?: null,
                    'linkedin' => null,
                ];
            }
        }

        $agencyUsers = DB::table('users')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.email', 'users.calling_code', 'users.mobile', 'users.linkedin', 'users_profile.designation')
            ->where('users.company_id', $partnerCompanyId)
            ->where('users.status', 'active')
            ->orderBy('users.root_user', 'desc')
            ->get();

        foreach ($agencyUsers as $u) {
            $uName = trim((string) $u->name);
            $uEmail = trim((string) $u->email);
            $uMobile = trim((string) $u->mobile);
            $uCode = trim((string) $u->calling_code);
            $fullPhone = $uMobile ? ($uCode ? $uCode.' '.$uMobile : $uMobile) : null;
            $uLinkedin = trim((string) $u->linkedin);
            $uDesig = trim((string) $u->designation);
            $key = $uEmail ?: $uName;

            if (isset($contactsMap[$key])) {
                if (empty($contactsMap[$key]['phone']) && $fullPhone) {
                    $contactsMap[$key]['phone'] = $fullPhone;
                }
                if (empty($contactsMap[$key]['designation']) && $uDesig) {
                    $contactsMap[$key]['designation'] = $uDesig;
                }
                if (empty($contactsMap[$key]['linkedin']) && $uLinkedin) {
                    $contactsMap[$key]['linkedin'] = $uLinkedin;
                }
            } elseif (! empty($key)) {
                $contactsMap[$key] = [
                    'name' => $uName ?: 'Agency Representative',
                    'designation' => $uDesig ?: null,
                    'phone' => $fullPhone,
                    'email' => $uEmail ?: null,
                    'linkedin' => $uLinkedin ?: null,
                ];
            }
        }

        $this->agencyContacts = collect(array_values($contactsMap))->map(function ($cnt) {
            $words = preg_split('/\s+/', trim($cnt['name']));
            $inits = '';
            foreach ($words as $w) {
                if (! empty($w)) {
                    $inits .= mb_strtoupper(mb_substr($w, 0, 1));
                }
                if (strlen($inits) >= 2) {
                    break;
                }
            }
            $cnt['initials'] = $inits ?: 'AG';

            return (object) $cnt;
        });

        // ── 4d. Agency Locations (Rule 20 Sub-Card Engine) ──
        $dbLocations = DB::table('client_location')
            ->where(function ($q) use ($actualClientId, $partnerCompanyId) {
                $q->where('client_id', $actualClientId);
                if ($partnerCompanyId > 0) {
                    $q->orWhere('company_id', $partnerCompanyId);
                }
            })
            ->get();

        $locList = [];
        foreach ($dbLocations as $loc) {
            $cName = '';
            if (! empty($loc->city_id) && $loc->city_id > 0) {
                $cName = DB::table('city_list')->where('city_id', $loc->city_id)->value('city') ?: '';
            }
            if (empty($cName) && ! empty($loc->city)) {
                $cName = $loc->city;
            }
            $sName = '';
            if (! empty($loc->state) && $loc->state > 0) {
                $sName = DB::table('state_list')->where('id', $loc->state)->value('state') ?: '';
            }
            $cntryName = '';
            if (! empty($loc->country_id) && $loc->country_id > 0) {
                $cArr = GeneralMethod::getCountryName($loc->country_id);
                foreach ($cArr as $cval) {
                    $cntryName = $cval['name'];
                }
            }
            $addr = trim((string) $loc->address);
            $pcode = trim((string) $loc->postcode);
            if ($pcode === '0' || $pcode === '') {
                $pcode = null;
            }

            $locList[] = (object) [
                'address' => $addr ?: null,
                'city' => $cName ? ucwords(trim($cName)) : null,
                'state' => $sName ? ucwords(trim($sName)) : null,
                'country' => $cntryName ? ucwords(trim($cntryName)) : null,
                'postcode' => $pcode,
            ];
        }

        if (empty($locList)) {
            $billingAddr = DB::table('billing_address_companies')->where('company_id', $partnerCompanyId)->first();
            if ($billingAddr) {
                $bAddr = trim(implode(' ', array_filter([$billingAddr->building, $billingAddr->street])));
                $bCity = '';
                if (! empty($billingAddr->city)) {
                    $bCity = DB::table('city_list')->where('city_id', $billingAddr->city)->value('city') ?: '';
                }
                $bState = '';
                if (! empty($billingAddr->state_id)) {
                    $bState = DB::table('state_list')->where('id', $billingAddr->state_id)->value('state') ?: '';
                }
                $bCountry = '';
                if (! empty($billingAddr->country_id)) {
                    $cArr = GeneralMethod::getCountryName($billingAddr->country_id);
                    foreach ($cArr as $cval) {
                        $bCountry = $cval['name'];
                    }
                }
                $locList[] = (object) [
                    'address' => $bAddr ?: null,
                    'city' => $bCity ? ucwords(trim($bCity)) : null,
                    'state' => $bState ? ucwords(trim($bState)) : null,
                    'country' => $bCountry ? ucwords(trim($bCountry)) : null,
                    'postcode' => ! empty($billingAddr->postal_code) ? trim($billingAddr->postal_code) : null,
                ];
            }
        }
        $this->agencyLocations = collect($locList);

        if ($actualClientId && $this->elasticService->indexExists(ELASTIC_CLIENT_LIST)) {
            try {
                $params = [
                    'index' => ELASTIC_CLIENT_LIST,
                    'id' => $actualClientId,
                ];

                $response = $this->elasticService->getClient()->get($params);
                if (! empty($response['_source'])) {
                    $dbDetail = (array) $this->directClientDetail;
                    $esDetail = (array) $response['_source'];
                    $merged = array_merge($dbDetail, $esDetail);
                    if (isset($dbDetail['success_per']) && ! isset($esDetail['success_per'])) {
                        $merged['success_per'] = $dbDetail['success_per'];
                    }
                    if (! empty($dbDetail['company_phone']) && empty($esDetail['company_phone'])) {
                        $merged['company_phone'] = $dbDetail['company_phone'];
                    }
                    if (! empty($dbDetail['calling_code']) && empty($esDetail['calling_code'])) {
                        $merged['calling_code'] = $dbDetail['calling_code'];
                    }
                    $this->directClientDetail = (object) $merged;
                }
            } catch (\Exception $e) {
                // Keep DB fallback if ES fails
            }
        }

        // Guarantee directClientDetail->company_id is ALWAYS set to partnerCompanyId
        if ($this->directClientDetail) {
            $this->directClientDetail->company_id = $partnerCompanyId;
        }

        // ── 5. Company Size Resolution ──
        $compSizeFrom = @$this->directClientDetail->company_size_from;
        $compSizeTo = @$this->directClientDetail->company_sizes ?: @$this->directClientDetail->company_size;
        $this->agencyCompanySize = '10 – 50 Employees';
        if ($compSizeFrom && $compSizeTo) {
            $this->agencyCompanySize = number_format((int) $compSizeFrom).' – '.number_format((int) $compSizeTo).' Employees';
        } elseif ($compSizeFrom) {
            $this->agencyCompanySize = number_format((int) $compSizeFrom).'+ Employees';
        }

        // ── 6. Dynamic Industry & Company Type Resolution ──
        $industryId = (int) (@$this->directClientDetail->industries ?: (@$this->directClientDetail->industry ?: 0));
        $this->industries_name = 'Information Technology & Services';
        if ($industryId > 0) {
            $resolvedInd = DB::table('td_industries')->where('ind_id', $industryId)->value('ind_name');
            if ($resolvedInd) {
                $this->industries_name = $resolvedInd;
            }
        }

        $companyTypes = GeneralMethod::industries_type_arr();
        $rawType = @$this->directClientDetail->company_type;
        $this->company_type_name = $companyTypes[$rawType] ?? 'Corporate Entity';

        // ── 7. Dynamic Commercial Agreement & Placement Fee ──
        $successPer = @$this->directClientDetail->success_per;
        $this->placement_fee = (! empty($successPer) && $successPer > 0) ? ((float) $successPer).'% of Annual CTC' : '8.33% of Annual CTC';

        // ── 7b. Agency Tax ID / GSTN Resolution ──
        $detailData = [];
        if (! empty($this->directClientDetail->detail)) {
            $rawJson = is_array($this->directClientDetail->detail) ? $this->directClientDetail->detail : json_decode($this->directClientDetail->detail, true);
            $detailData = is_array($rawJson) ? $rawJson : [];
        }
        $taxVal = $detailData['tax_id'] ?? ($detailData['gstn'] ?? ($detailData['gst_number'] ?? null));
        if (empty($taxVal)) {
            $bRow = DB::table('billing_address_companies')->where('company_id', $partnerCompanyId)->first();
            if ($bRow) {
                $taxVal = ! empty($bRow->gstin) ? trim($bRow->gstin) : (! empty($bRow->pan_no) ? trim($bRow->pan_no) : null);
            }
        }
        $this->agency_tax_id = ! empty($taxVal) ? trim((string) $taxVal) : null;

        // ── 7c. Dynamic Commercial Terms by Staffing Model (Rule 20 & Services Tab Single Source of Truth) ──
        $partnerCompany = DB::table('companies')->where('id', $partnerCompanyId)->first();
        $agencyTermsSpecific = DB::table('agency_commercial_terms')->where('company_id', $partnerCompanyId)->first();
        $agencyTermsFallback = DB::table('agency_commercial_terms')->where('company_id', $company_id)->first();
        $agencyTerms = $agencyTermsSpecific ?: $agencyTermsFallback;
        $settingsJson = ($agencyTerms && ! empty($agencyTerms->settings_json))
            ? (is_array($agencyTerms->settings_json) ? $agencyTerms->settings_json : json_decode($agencyTerms->settings_json, true))
            : [];

        $commStateDTO = CommercialReadModelService::getTenantCommercialsState($partnerCompanyId);
        $ftDTO = $commStateDTO->fulltime;
        $ctDTO = $commStateDTO->contract;

        // Intent: Sync active services strictly with agency-activated state (Rule 1, 5, 20)
        $isContractActive = ($ctDTO && (int) $ctDTO->status === 1) || (! empty($agencyTermsSpecific) && ! empty($agencyTermsSpecific->contract_billing_model));
        $isFulltimeActive = ($ftDTO && (int) $ftDTO->status === 1) || (! empty($agencyTermsSpecific) && ! empty($agencyTermsSpecific->fulltime_fee_model));

        // 1. Contract Staffing Terms Resolution (Synced with Hiring Services & Commercials)
        $this->contractCommercial = null;
        if ($isContractActive) {
            $ctRateType = $ctDTO ? (int) $ctDTO->rateType : 1;
            $isFreeCT = ($ctRateType === 0);
            $ctModelLabel = $ctDTO ? $ctDTO->rateTypeLabel : ($ctRateType === 2 ? 'Markup on Pay Rate' : ($ctRateType === 1 ? 'Contract Bill Rate' : 'Zero Margin (Pass-Through)'));
            $ctHeaderLabel = $isFreeCT ? 'ZERO MARGIN' : ($ctRateType === 2 ? 'MARKUP ON PAY' : 'CONTRACT BILL RATE');

            $ctSalMode = $ctDTO ? (int) $ctDTO->salMode : 2;
            $ctSalModeLabel = $isFreeCT ? 'At-Cost Wage Pass-Through' : match ($ctSalMode) {
                2 => 'Hourly Contract Rate',
                1 => 'Monthly Staffing Rate',
                0 => 'Annual Staffing Rate',
                default => 'Hourly Contract Rate',
            };
            $ctModeBadgeText = $isFreeCT ? '0% Margin' : match ($ctSalMode) {
                2 => 'Hourly',
                1 => 'Monthly',
                0 => 'Annual',
                default => 'Hourly',
            };

            $ctCurrencyCode = $ctDTO ? $ctDTO->currencyCode : 'INR';
            $ctCurrencySymbol = CurrencyService::getCurrencySymbol($ctCurrencyCode) ?: '₹';

            $ctFirstRate = ($ctDTO && ! empty($ctDTO->rates)) ? (float) data_get(collect($ctDTO->rates)->sortBy('exp')->first(), 'rate', 5.0) : 5.0;

            if (! empty($settingsJson['contract_rate_amount']) && (float) $settingsJson['contract_rate_amount'] > 0) {
                $ctDefaultRateText = $ctCurrencySymbol.' '.number_format((float) $settingsJson['contract_rate_amount']);
                $ctDefaultRateSub = '/ '.ucfirst($agencyTerms->contract_billing_model ?? 'Month');
            } elseif ($ctRateType === 2) {
                $ctDefaultRateText = number_format($ctFirstRate, ($ctFirstRate == (int) $ctFirstRate ? 0 : 1)).'%';
                $ctDefaultRateSub = 'Markup on Pay';
            } elseif ($ctRateType === 1) {
                $ctDefaultRateText = $ctCurrencySymbol.' '.number_format($ctFirstRate, ($ctFirstRate == (int) $ctFirstRate ? 0 : 2));
                $ctDefaultRateSub = match ($ctSalMode) {
                    2 => '/ Hour', 1 => '/ Month', default => '/ Month'
                };
            } else {
                $ctDefaultRateText = 'Pass-Through (0%)';
                $ctDefaultRateSub = 'Zero Margin';
            }

            $ctReplacementDays = $ctDTO && $ctDTO->replacementPeriodDays ? (int) $ctDTO->replacementPeriodDays : 30;
            $ctReplacementType = $ctDTO && $ctDTO->replacementTypeLabel ? $ctDTO->replacementTypeLabel : 'Contractor Replacement';
            $ctReplacementClause = $ctDTO && $ctDTO->replacementEligibilityLabel ? $ctDTO->replacementEligibilityLabel : 'Contractor Early Departure';

            $ctPaymentDays = $ctDTO && $ctDTO->paymentTermsDays !== null ? (int) $ctDTO->paymentTermsDays : 30;
            $ctPaymentTermsDays = ($ctPaymentDays === 0) ? 'Due Immediately' : "Net {$ctPaymentDays} Days";

            $ctBillingCycle = ! empty($settingsJson['contract_billing_cycle']) ? $settingsJson['contract_billing_cycle'] : ($ctDTO ? $ctDTO->billingFrequencyLabel : 'Monthly');
            $ctBillingTrigger = ! empty($settingsJson['contract_invoice_raising']) ? $settingsJson['contract_invoice_raising'] : ($ctDTO ? $ctDTO->billingTriggerLabel : 'Approved Timesheet');

            $ctMinDuration = (! empty($agencyTerms->contract_min_duration_months) && (int) $agencyTerms->contract_min_duration_months > 0)
                ? (int) $agencyTerms->contract_min_duration_months.' Months'
                : '3 Months';

            $ctEarlyTermination = ! empty($settingsJson['contract_early_termination']) ? $settingsJson['contract_early_termination'] : 'As per contract terms';
            $ctOvertimePolicy = ! empty($settingsJson['contract_overtime_policy']) ? $settingsJson['contract_overtime_policy'] : 'As per client terms';

            $ctCountries = ($ctDTO && ! empty($ctDTO->countryNames)) ? $ctDTO->countryNames : ['India'];

            $this->contractCommercial = (object) [
                'id' => $ctDTO ? $ctDTO->id : 0,
                'active' => true,
                'rate_type' => $ctRateType,
                'header_label' => $ctHeaderLabel,
                'mode_badge' => $ctModeBadgeText,
                'default_rate' => $ctDefaultRateText,
                'default_rate_sub' => $ctDefaultRateSub,
                'commercial_rate' => $ctDefaultRateText.' '.$ctDefaultRateSub,
                'sal_mode_label' => $ctSalModeLabel,
                'model_label' => $ctModelLabel,
                'currency_code' => $ctCurrencyCode,
                'currency_symbol' => $ctCurrencySymbol,
                'replacement_days' => $ctReplacementDays,
                'replacement_type' => $ctReplacementType,
                'replacement_clause' => $ctReplacementClause,
                'replacement_terms' => "{$ctReplacementDays} Days {$ctReplacementType}",
                'payment_terms' => $ctPaymentTermsDays,
                'billing_cycle' => $ctBillingCycle,
                'billing_trigger' => $ctBillingTrigger,
                'min_duration' => $ctMinDuration,
                'early_termination' => $ctEarlyTermination,
                'overtime_policy' => $ctOvertimePolicy,
                'countries' => $ctCountries,
                'bottom_note' => 'Contract staffing terms apply automatically to candidate contractor timesheets, bill rates, and monthly agency invoices.',
                'bottom_model' => $ctModelLabel,
            ];
        }

        // 2. Fulltime / Permanent Staffing Terms Resolution (Synced with Hiring Services & Commercials)
        $this->permanentCommercial = null;
        if ($isFulltimeActive) {
            $ftRateType = $ftDTO ? (int) $ftDTO->rateType : 2;
            $isFreeFT = ($ftRateType === 0);
            $ftModelLabel = $ftDTO ? $ftDTO->rateTypeLabel : ($ftRateType === 2 ? 'Percentage of CTC' : ($ftRateType === 1 ? 'Fixed Placement Fee' : 'No Fee Applicable (Free Placement)'));
            $ftHeaderLabel = $isFreeFT ? 'FREE PLACEMENT' : ($ftRateType === 1 ? 'FIXED PLACEMENT' : 'PERCENTAGE OF CTC');
            $ftModeBadgeText = $isFreeFT ? '0% Fee' : '5 Tiers';

            $ftFirstRate = ($ftDTO && ! empty($ftDTO->rates)) ? (float) data_get(collect($ftDTO->rates)->sortBy('exp')->first(), 'rate', 8.33) : 8.33;
            if (! empty($partnerCompany->success_per) && (float) $partnerCompany->success_per > 0) {
                $ftFirstRate = (float) $partnerCompany->success_per;
            } elseif (! empty($agencyTerms->fulltime_success_fee_percentage) && (float) $agencyTerms->fulltime_success_fee_percentage > 0) {
                $ftFirstRate = (float) $agencyTerms->fulltime_success_fee_percentage;
            }
            $ftCurrencyCode = $ftDTO ? $ftDTO->currencyCode : 'INR';
            $ftCurrencySymbol = CurrencyService::getCurrencySymbol($ftCurrencyCode) ?: '₹';

            if ($ftRateType === 2) {
                $ftDefaultRateText = number_format($ftFirstRate, ($ftFirstRate == (int) $ftFirstRate ? 0 : 2)).'%';
                $ftSalModeText = 'Annual CTC';
            } elseif ($ftRateType === 1) {
                $ftDefaultRateText = $ftCurrencySymbol.' '.number_format($ftFirstRate, ($ftFirstRate == (int) $ftFirstRate ? 0 : 2));
                $ftSalModeText = 'Per Hire';
            } else {
                $ftDefaultRateText = 'Free (0%)';
                $ftSalModeText = 'Direct Candidate Hire';
            }

            $ftReplacementDays = $ftDTO && $ftDTO->replacementPeriodDays ? (int) $ftDTO->replacementPeriodDays : 90;
            $ftReplacementType = $ftDTO && $ftDTO->replacementTypeLabel ? $ftDTO->replacementTypeLabel : 'Free Replacement';
            $ftReplacementClause = $ftDTO && $ftDTO->replacementEligibilityLabel ? $ftDTO->replacementEligibilityLabel : 'Candidate Resignation / Early Exit';

            $ftPaymentDays = $ftDTO && $ftDTO->paymentTermsDays !== null ? (int) $ftDTO->paymentTermsDays : 30;
            $ftPaymentTermsDays = ($ftPaymentDays === 0) ? 'Due Immediately' : "Net {$ftPaymentDays} Days";
            $ftBillingFrequency = $ftDTO && $ftDTO->billingFrequencyLabel ? $ftDTO->billingFrequencyLabel : 'One Time';
            $ftBillingTrigger = $ftDTO && $ftDTO->billingTriggerLabel ? $ftDTO->billingTriggerLabel : 'Candidate Joined';

            $ftCountries = ($ftDTO && ! empty($ftDTO->countryNames)) ? $ftDTO->countryNames : ['India'];

            $this->permanentCommercial = (object) [
                'id' => $ftDTO ? $ftDTO->id : 0,
                'active' => true,
                'rate_type' => $ftRateType,
                'header_label' => $ftHeaderLabel,
                'mode_badge' => $ftModeBadgeText,
                'default_rate' => $ftDefaultRateText,
                'default_rate_sub' => 'Default Rate',
                'placement_fee' => $ftDefaultRateText.' of '.$ftSalModeText,
                'sal_mode_label' => $ftSalModeText,
                'model_label' => $ftModelLabel,
                'currency_code' => $ftCurrencyCode,
                'currency_symbol' => $ftCurrencySymbol,
                'replacement_days' => $ftReplacementDays,
                'replacement_type' => $ftReplacementType,
                'replacement_clause' => $ftReplacementClause,
                'guarantee_period' => "{$ftReplacementDays} Days {$ftReplacementType}",
                'payment_terms' => $ftPaymentTermsDays,
                'billing_frequency' => $ftBillingFrequency,
                'billing_trigger' => $ftBillingTrigger,
                'countries' => $ftCountries,
                'bottom_note' => 'Fulltime placement commercial terms bind automatically to agency agreements, shared job requisitions, and candidate submissions.',
                'bottom_model' => ($ftRateType === 2) ? 'One-time Placement Fee' : $ftModelLabel,
            ];
        }
        $this->fulltimeCommercial = $this->permanentCommercial;

        $this->hasContractStaffing = ! empty($this->contractCommercial);
        $this->hasPermanentStaffing = ! empty($this->permanentCommercial);
        $this->hasActiveCommercials = ($this->hasContractStaffing || $this->hasPermanentStaffing);

        // ── 8. Agency Contact & Communication Meta ──
        $callingCode = @$this->directClientDetail->calling_code ?: '+91';
        $rawPhone = @$this->directClientDetail->company_phone ?: @$this->directClientDetail->contact_number;
        $this->agency_phone = $rawPhone ? ($callingCode.' '.$rawPhone) : '+91 98765 43210';
        $this->agency_email = @$this->directClientDetail->company_email ?: (@$this->directClientDetail->email ?: 'partners@agency.com');

        // ── 9. Agency Description & Partnership Date ──
        $this->agency_description = @$this->directClientDetail->description ?: @$this->directClientDetail->companies_description;
        $this->partner_joined_date = ! empty(@$this->directClientDetail->created_at) ? Carbon::parse($this->directClientDetail->created_at)->format('d M, Y') : date('d M, Y');
        $this->founded = ! empty(@$this->directClientDetail->founded) ? $this->directClientDetail->founded : 2019;

        // $startOfMonth = Carbon::now()->startOfMonth();
        $startOfMonth = Carbon::now()->startOfMonth()->subMonths(3);
        $endOfMonth = Carbon::now()->endOfMonth();

        $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(6);
        $endOfMonthcomman = Carbon::now()->endOfMonth();

        // Fallback default values for initial shell rendering if not already computed
        $this->joined_count = $this->joined_count ?? 0;
        $this->sourcing_count = $this->sourcing_count ?? 0;
        $this->screening_count = $this->screening_count ?? 0;
        $this->evaluation_count = $this->evaluation_count ?? 0;
        $this->negotiation_count = $this->negotiation_count ?? 0;
        $this->offer_count = $this->offer_count ?? 0;
        $this->Rejected_count = $this->Rejected_count ?? 0;
        $this->inreview_count = $this->inreview_count ?? 0;
        $this->total_candidates = $this->total_candidates ?? 0;

        $this->counts = collect([]);
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
        $this->startOfMonthcomman = $startOfMonthcomman;
        $this->endOfMonthcomman = $endOfMonthcomman;

        if (empty($this->userPermissions)) {
            $this->userPermissions = [];
        }

        $view = view('service_company.consultancy.dviewMore', $this->data)->render();
        $responseData = Reply::dataOnly(['status' => 'success', 'view' => $view]);

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(CONSULTANCY_VIEWMORE_TIME));

        return $responseData;

    }

    /**
     * Lazy-loaded AJAX endpoint for Candidate Dashboard tab metrics & charts.
     */
    public function dashboard_ajax(Request $request, $id = null)
    {
        $company_id = auth()->user()->company_id;

        $partnerCompanyId = $this->resolveAuthorizedAgencyCompanyId((int) $company_id, $id);

        if (! $partnerCompanyId) {
            return response()->json(['status' => 'fail', 'message' => 'Unauthorized partner.'], 403);
        }

        $to_company_id = $partnerCompanyId;

        $startOfMonth = Carbon::now()->startOfMonth()->subMonths(3);
        $endOfMonth = Carbon::now()->endOfMonth();
        $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(6);
        $endOfMonthcomman = Carbon::now()->endOfMonth();

        // 1. Candidate pipeline status breakdown
        $boolQuery = [
            'bool' => [
                'must' => [
                    [
                        'bool' => [
                            'should' => [
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['job_company_id' => $company_id]],
                                ['term' => ['client_comapny_id' => $company_id]],
                                ['term' => ['to_company_id' => $company_id]],
                                ['term' => ['from_company_id' => $company_id]],
                            ],
                            'minimum_should_match' => 1,
                        ],
                    ],
                    [
                        'bool' => [
                            'should' => [
                                ['term' => ['ClientComID' => $to_company_id]],
                                [
                                    'bool' => [
                                        'must' => [
                                            ['term' => ['thirdparty_company_id' => $to_company_id]],
                                        ],
                                        'must_not' => [
                                            ['term' => ['ClientComID' => 0]],
                                        ],
                                    ],
                                ],
                                ['term' => ['agency_id' => $to_company_id]],
                                ['term' => ['agency_company_id' => $to_company_id]],
                                ['term' => ['from_company_id' => $to_company_id]],
                            ],
                            'minimum_should_match' => 1,
                        ],
                    ],
                ],
                'must_not' => [
                    ['terms' => ['job_master_status_id' => [8, 9, '8', '9']]],
                    ['terms' => ['master_status_id' => [8, 9, '8', '9']]],
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

        $resultData = collect([]);
        if ($this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            $response = $this->elasticService->getClient()->search($params);
            $bucketData = $response['aggregations']['status_counts']['buckets'] ?? [];
            $resultData = collect($bucketData)->mapWithKeys(function ($item) {
                return [$item['key'] => $item['doc_count']];
            });
        }

        $joined_count = $resultData->get('6', 0);
        $sourcing_count = $resultData->get('1', 0);
        $screening_count = $resultData->get('2', 0);
        $evaluation_count = $resultData->get('3', 0);
        $negotiation_count = $resultData->get('4', 0);
        $offer_count = $resultData->get('5', 0);
        $Rejected_count = $resultData->get('7', 0);
        $inreview_count = 0; // Strict in-review exclusion from candidate pipeline
        $total_candidates = $sourcing_count + $screening_count + $evaluation_count + $negotiation_count + $offer_count + $joined_count + $Rejected_count;

        // DB Fallback for Candidate Pipeline breakdown if ES count is 0
        if ($total_candidates == 0) {
            $dbCandStats = DB::table('add_job_wise_condidates')
                ->select(
                    DB::raw('COUNT(CASE WHEN master_status_id = 6 THEN 1 END) AS joined_count'),
                    DB::raw('COUNT(CASE WHEN master_status_id = 1 THEN 1 END) AS sourcing_count'),
                    DB::raw('COUNT(CASE WHEN master_status_id = 2 THEN 1 END) AS screening_count'),
                    DB::raw('COUNT(CASE WHEN master_status_id = 3 THEN 1 END) AS evaluation_count'),
                    DB::raw('COUNT(CASE WHEN master_status_id = 4 THEN 1 END) AS negotiation_count'),
                    DB::raw('COUNT(CASE WHEN master_status_id = 5 THEN 1 END) AS offer_count'),
                    DB::raw('COUNT(CASE WHEN master_status_id = 7 THEN 1 END) AS rejected_count'),
                    DB::raw('COUNT(CASE WHEN master_status_id = 8 THEN 1 END) AS inreview_count')
                )
                ->where('company_id', $company_id)
                ->where('thirdparty_company_id', $to_company_id)
                ->whereNotIn('master_status_id', [8, 9])
                ->first();

            if ($dbCandStats) {
                $joined_count = (int) $dbCandStats->joined_count;
                $sourcing_count = (int) $dbCandStats->sourcing_count;
                $screening_count = (int) $dbCandStats->screening_count;
                $evaluation_count = (int) $dbCandStats->evaluation_count;
                $negotiation_count = (int) $dbCandStats->negotiation_count;
                $offer_count = (int) $dbCandStats->offer_count;
                $Rejected_count = (int) $dbCandStats->rejected_count;
                $inreview_count = 0;
                $total_candidates = $sourcing_count + $screening_count + $evaluation_count + $negotiation_count + $offer_count + $joined_count + $Rejected_count;
            }
        }

        // 2. Jobs doughnut graph
        $must = [];
        $must[] = [
            'bool' => [
                'should' => [
                    [
                        'nested' => [
                            'path' => 'recommendation_relation',
                            'query' => [
                                'bool' => [
                                    'must' => [
                                        ['term' => ['recommendation_relation.from_company_id' => $company_id]],
                                        ['term' => ['recommendation_relation.to_company_id' => $to_company_id]],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        $params = [
            'index' => ELASTIC_JOBS,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'size' => 0,
                'query' => ['bool' => ['must' => $must]],
                'aggs' => [
                    'status_counts' => [
                        'terms' => [
                            'field' => 'status',
                            'size' => 10,
                        ],
                    ],
                ],
            ],
        ];

        $counts = collect([]);
        if ($this->elasticService->indexExists(ELASTIC_JOBS)) {
            $response = $this->elasticService->getClient()->search($params);
            $bucketData = $response['aggregations']['status_counts']['buckets'] ?? [];
            $counts = collect($bucketData)->mapWithKeys(function ($item) {
                return [$item['key'] => $item['doc_count']];
            });
        }
        $active_count = $counts->get('active', 0);
        $hold_count = $counts->get('hold', 0);
        $inactive_count = $counts->get('inactive', 0);
        $alljobs_count = $active_count + $hold_count + $inactive_count;

        // DB Fallback for Jobs breakdown if ES count is 0
        if ($alljobs_count == 0) {
            $dbJobs = DB::table('recommendation_relation')
                ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
                ->select(
                    DB::raw("SUM(CASE WHEN jobs.status = 'active' THEN 1 ELSE 0 END) AS active"),
                    DB::raw("SUM(CASE WHEN jobs.status = 'hold' THEN 1 ELSE 0 END) AS hold"),
                    DB::raw("SUM(CASE WHEN jobs.status = 'inactive' THEN 1 ELSE 0 END) AS inactive")
                )
                ->where('recommendation_relation.from_company_id', $company_id)
                ->where('recommendation_relation.to_company_id', $to_company_id)
                ->where('recommendation_relation.final_status', 1)
                ->first();

            if ($dbJobs) {
                $active_count = (int) $dbJobs->active;
                $hold_count = (int) $dbJobs->hold;
                $inactive_count = (int) $dbJobs->inactive;
                $alljobs_count = $active_count + $hold_count + $inactive_count;
            }
        }

        // 3. Candidate last 7 days
        $endDate = Carbon::today()->endOfDay();
        $startDate = Carbon::today()->subDays(6)->startOfDay();
        $paramsCandidates = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['from_company_id' => $company_id]],
                            [
                                'bool' => [
                                    'should' => [
                                        ['term' => ['ClientComID' => $to_company_id]],
                                        ['term' => ['to_company_id' => $to_company_id]],
                                        ['term' => ['thirdparty_company_id' => $to_company_id]],
                                    ],
                                    'minimum_should_match' => 1,
                                ],
                            ],
                            ['term' => ['final_status' => 1]],
                            [
                                'range' => [
                                    'created_at' => [
                                        'gte' => $startDate->toDateString(),
                                        'lte' => $endDate->toDateString(),
                                    ],
                                ],
                            ],
                        ],
                        'must_not' => [
                            ['terms' => ['job_master_status_id' => [8, 9]]],
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
                            'extended_bounds' => [
                                'min' => $startDate->toDateString(),
                                'max' => $endDate->toDateString(),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $candidateBuckets = [];
        if ($this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            $responseCandidates = $this->elasticService->getClient()->search($paramsCandidates);
            $candidateBuckets = $responseCandidates['aggregations']['uploads_over_time']['buckets'] ?? [];
        }

        $labels = [];
        $candidateValues = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $labels[] = $date->format('M d');
            $candidateBucket = collect($candidateBuckets)->firstWhere('key_as_string', $date->format('Y-m-d'));
            $candidateValues[] = $candidateBucket['doc_count'] ?? 0;
        }
        $candidateValuesTotal = array_sum($candidateValues);

        // DB Fallback for Candidates last 7 days if ES count is 0
        if ($candidateValuesTotal == 0) {
            $db7Days = DB::table('add_job_wise_condidates')
                ->where('company_id', $company_id)
                ->where('thirdparty_company_id', $to_company_id)
                ->whereBetween('created_at', [$startDate->format('Y-m-d 00:00:00'), $endDate->format('Y-m-d 23:59:59')])
                ->count();
            if ($db7Days > 0) {
                $candidateValuesTotal = $db7Days;
                $candidateValues[6] = $db7Days;
            }
        }

        // 4. Offer count graph
        $paramsOffer = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['from_company_id' => $company_id]],
                            [
                                'bool' => [
                                    'should' => [
                                        ['term' => ['ClientComID' => $to_company_id]],
                                        ['term' => ['to_company_id' => $to_company_id]],
                                        ['term' => ['thirdparty_company_id' => $to_company_id]],
                                    ],
                                    'minimum_should_match' => 1,
                                ],
                            ],
                            ['term' => ['final_status' => 1]],
                            [
                                'bool' => [
                                    'should' => [
                                        ['term' => ['job_master_status_id' => 5]],
                                        ['term' => ['master_status_id' => 5]],
                                        ['term' => ['job_master_status_id' => 6]],
                                        ['term' => ['master_status_id' => 6]],
                                    ],
                                    'minimum_should_match' => 1,
                                ],
                            ],
                        ],
                    ],
                ],
                'aggs' => [
                    'of_offer_count' => [
                        'date_histogram' => [
                            'field' => 'created_at',
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
        ];
        $candidateBucketsoffer = [];
        if ($this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            $responseOffer = $this->elasticService->getClient()->search($paramsOffer);
            $candidateBucketsoffer = $responseOffer['aggregations']['of_offer_count']['buckets'] ?? [];
        }
        $candidateValuesCommoffer = [];
        $labelsCommoffer = [];
        $startDateComm = Carbon::now();
        for ($i = 5; $i >= 0; $i--) {
            $sDate = $startDateComm->copy()->startOfMonth()->subMonths($i);
            $labelsCommoffer[] = $sDate->format('M Y');
            $cBucket = collect($candidateBucketsoffer)->firstWhere('key_as_string', $sDate->format('Y-m'));
            $candidateValuesCommoffer[] = $cBucket['doc_count'] ?? 0;
        }
        $candidateValuesTotaloffer = array_sum($candidateValuesCommoffer);

        // DB Fallback for Offer count total if ES count is 0
        if ($candidateValuesTotaloffer == 0) {
            $dbOffersTotal = DB::table('add_job_wise_condidates')
                ->where('company_id', $company_id)
                ->where('thirdparty_company_id', $to_company_id)
                ->whereIn('master_status_id', [5, 6])
                ->count();
            if ($dbOffersTotal > 0) {
                $candidateValuesTotaloffer = $dbOffersTotal;
                $candidateValuesCommoffer[5] = $dbOffersTotal;
            }
        }

        // 5. Candidate list joined
        $paramsJoin = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'size' => 6,
                'sort' => [['id' => ['order' => 'desc']]],
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['from_company_id' => $company_id]],
                            [
                                'bool' => [
                                    'should' => [
                                        ['term' => ['ClientComID' => $to_company_id]],
                                        ['term' => ['to_company_id' => $to_company_id]],
                                        ['term' => ['thirdparty_company_id' => $to_company_id]],
                                    ],
                                    'minimum_should_match' => 1,
                                ],
                            ],
                            ['term' => ['final_status' => 1]],
                            [
                                'bool' => [
                                    'should' => [
                                        ['term' => ['job_master_status_id' => 6]],
                                        ['term' => ['master_status_id' => 6]],
                                    ],
                                    'minimum_should_match' => 1,
                                ],
                            ],
                        ],
                    ],
                ],
                'aggs' => [
                    'of_joined_count' => [
                        'date_histogram' => [
                            'field' => 'created_at',
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
        ];

        $candidateBucketsjoin = [];
        if ($this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            $responseJoin = $this->elasticService->getClient()->search($paramsJoin);
            $candidateBucketsjoin = $responseJoin['aggregations']['of_joined_count']['buckets'] ?? [];
        }
        $candidateValuesCommjoin = [];
        $labelsCommjoin = [];
        for ($i = 5; $i >= 0; $i--) {
            $sDate = $startDateComm->copy()->startOfMonth()->subMonths($i);
            $labelsCommjoin[] = $sDate->format('M Y');
            $cBucket = collect($candidateBucketsjoin)->firstWhere('key_as_string', $sDate->format('Y-m'));
            $candidateValuesCommjoin[] = $cBucket['doc_count'] ?? 0;
        }
        $candidateValuesTotaljoin = array_sum($candidateValuesCommjoin);

        // DB Fallback for Joined count total if ES count is 0
        if ($candidateValuesTotaljoin == 0) {
            $dbJoinedTotal = DB::table('add_job_wise_condidates')
                ->where('company_id', $company_id)
                ->where('thirdparty_company_id', $to_company_id)
                ->where('master_status_id', 6)
                ->count();
            if ($dbJoinedTotal > 0) {
                $candidateValuesTotaljoin = $dbJoinedTotal;
                $candidateValuesCommjoin[5] = $dbJoinedTotal;
            }
        }
        $candidateValuesTotaljoin = array_sum($candidateValuesCommjoin);

        // 6. Upcoming Interviews
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
                        'must' => [
                            ['term' => ['from_company_id' => $company_id]],
                            [
                                'bool' => [
                                    'should' => [
                                        ['term' => ['ClientComID' => $to_company_id]],
                                        ['term' => ['thirdparty_company_id' => $to_company_id]],
                                    ],
                                    'minimum_should_match' => 1,
                                ],
                            ],
                        ],
                        'filter' => [
                            [
                                'range' => [
                                    'schedule_date' => [
                                        'gte' => $nowMinus1Hour,
                                        'format' => 'strict_date_optional_time',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $upcomingData = [];
        if ($this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
            $responseUp = $this->elasticService->getClient()->search($upcomingParams);
            $upcomingData = array_map(fn ($hit) => $hit['_source'], $responseUp['hits']['hits']);
        }

        // 7. Recent Interviews (last 7 days)
        $recentParams = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
            'request_cache' => true,
            'body' => [
                'from' => 0,
                'size' => 5,
                'sort' => [['schedule_date' => ['order' => 'desc']]],
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['from_company_id' => $company_id]],
                            [
                                'bool' => [
                                    'should' => [
                                        ['term' => ['ClientComID' => $to_company_id]],
                                        ['term' => ['thirdparty_company_id' => $to_company_id]],
                                    ],
                                    'minimum_should_match' => 1,
                                ],
                            ],
                        ],
                        'filter' => [
                            [
                                'range' => [
                                    'schedule_date' => [
                                        'gte' => $startDate->toDateString(),
                                        'lte' => $endDate->toDateString(),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'aggs' => [
                    'uploads_over_time' => [
                        'date_histogram' => [
                            'field' => 'schedule_date',
                            'calendar_interval' => 'day',
                            'format' => 'yyyy-MM-dd',
                            'min_doc_count' => 0,
                            'extended_bounds' => [
                                'min' => $startDate->toDateString(),
                                'max' => $endDate->toDateString(),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $interviewBuckets = [];
        if ($this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
            $responseRec = $this->elasticService->getClient()->search($recentParams);
            $interviewBuckets = $responseRec['aggregations']['uploads_over_time']['buckets'] ?? [];
        }

        $interviewlabels = [];
        $interviewValues = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $interviewlabels[] = $date->format('M d');
            $iBucket = collect($interviewBuckets)->firstWhere('key_as_string', $date->format('Y-m-d'));
            $interviewValues[] = $iBucket['doc_count'] ?? 0;
        }

        // 8. Recent Interviews (last 6 months)
        $sDate6 = $startDateComm->copy()->startOfMonth()->subMonths(5);
        $recentParamsMonth = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
            'request_cache' => true,
            'body' => [
                'from' => 0,
                'size' => 5,
                'sort' => [['schedule_date' => ['order' => 'desc']]],
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['from_company_id' => $company_id]],
                            [
                                'bool' => [
                                    'should' => [
                                        ['term' => ['ClientComID' => $to_company_id]],
                                        ['term' => ['thirdparty_company_id' => $to_company_id]],
                                    ],
                                    'minimum_should_match' => 1,
                                ],
                            ],
                        ],
                        'filter' => [
                            [
                                'range' => [
                                    'schedule_date' => [
                                        'gte' => $sDate6->toDateString(),
                                        'lte' => $startDateComm->toDateString(),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'aggs' => [
                    'uploads_over_time' => [
                        'date_histogram' => [
                            'field' => 'schedule_date',
                            'calendar_interval' => 'month',
                            'format' => 'yyyy-MM-dd',
                            'min_doc_count' => 0,
                            'extended_bounds' => [
                                'min' => $sDate6->toDateString(),
                                'max' => $startDateComm->toDateString(),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $interviewBucketsMonth = [];
        if ($this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
            $responseMonth = $this->elasticService->getClient()->search($recentParamsMonth);
            $interviewBucketsMonth = $responseMonth['aggregations']['uploads_over_time']['buckets'] ?? [];
        }

        $interviewlabelsMonth = [];
        $interviewValuesMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $sDate = $startDateComm->copy()->startOfMonth()->subMonths($i);
            $interviewlabelsMonth[] = $sDate->format('M Y');
            $iBucket = collect($interviewBucketsMonth)->firstWhere('key_as_string', $sDate->format('Y-m-01'));
            $interviewValuesMonth[] = $iBucket['doc_count'] ?? 0;
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'joined_count' => $joined_count,
                'sourcing_count' => $sourcing_count,
                'screening_count' => $screening_count,
                'evaluation_count' => $evaluation_count,
                'negotiation_count' => $negotiation_count,
                'offer_count' => $offer_count,
                'Rejected_count' => $Rejected_count,
                'inreview_count' => $inreview_count,
                'total_candidates' => $total_candidates,
                'active_count' => $active_count,
                'hold_count' => $hold_count,
                'inactive_count' => $inactive_count,
                'alljobs_count' => $alljobs_count,
                'candidateValuesTotal' => $candidateValuesTotal,
                'labels' => $labels,
                'candidateValues' => $candidateValues,
                'candidateValuesTotaloffer' => $candidateValuesTotaloffer,
                'labelsCommoffer' => $labelsCommoffer,
                'candidateValuesCommoffer' => $candidateValuesCommoffer,
                'candidateValuesTotaljoin' => $candidateValuesTotaljoin,
                'labelsCommjoin' => $labelsCommjoin,
                'candidateValuesCommjoin' => $candidateValuesCommjoin,
                'upcomingData' => $upcomingData,
                'interviewlabels' => $interviewlabels,
                'interviewValues' => $interviewValues,
                'interviewlabelsMonth' => $interviewlabelsMonth,
                'interviewValuesMonth' => $interviewValuesMonth,
                'startOfMonth' => $startOfMonth->format('d M y'),
                'endOfMonth' => $endOfMonth->format('d M y'),
                'startOfMonthcomman' => $startOfMonthcomman->format('M Y'),
                'endOfMonthcomman' => $endOfMonthcomman->format('M Y'),
            ],
        ]);
    }

    public function staffingContactList($id)
    {

        $parentCompanyType = GeneralMethod::parentCompanyType();
        $accountCompanyType = GeneralMethod::accountCompanyType();

        $company_id = auth()->user()->company_id;
        $department = GeneralMethod::department();

        $directContactList = DB::table('companies')
            ->select(
                'companies.id', 'companies.company_name', 'companies.logo', 'companies.industries', 'companies.company_type',
                'users.name', 'users.email', 'users.mobile', 'users.calling_code', 'users.image', 'users.id as userId', 'users.status', 'roles.display_name', 'users_profile.designation');
        $directContactList = $directContactList->join('roles', 'roles.company_id', '=', 'companies.id');
        $directContactList = $directContactList->join('role_user', 'role_user.role_id', '=', 'roles.id');
        $directContactList = $directContactList->join('users', 'users.id', '=', 'role_user.user_id');
        $directContactList = $directContactList->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id');
        $directContactList = $directContactList->where('companies.id', '=', $id);
        $directContactList = $directContactList->where(function ($query) {
            return
            $query->where('roles.name', '=', 'admin')
                ->orWhere('roles.name', '=', 'sub administrator');

        });
        // $directContactList 			=	$directContactList->where('roles.name', '=', 'admin');

        return DataTables::of($directContactList)
            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
									<input class="form-check-input form-check-input2" type="checkbox" value="1" />
								  </div>';
            })
            ->editColumn('Name', function ($row) {

                $bgTextDecore = $strVal = '';
                $str = trim(@$row->name);
                $str = $strVal = strtolower($str[0]);
                $str = ord($str);
                if ($str >= 97 && $str <= 101) {
                    $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                } elseif ($str >= 102 && $str <= 106) {
                    $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                } elseif ($str >= 107 && $str <= 111) {
                    $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                } elseif ($str >= 112 && $str <= 116) {
                    $bgTextDecore = 'fs-3 bg-light-success text-success';
                } elseif ($str >= 117 && $str <= 121) {
                    $bgTextDecore = 'fs-3 bg-dark text-secondary';
                } else {
                    $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                }

                $icon = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
								<a href="javascript:void(0);"  class="show-detail">
								  <div class="symbol-label '.$bgTextDecore.' border-radius-5">'.ucwords($strVal).'</div>
								</a>
							  </div>';

                return ' <div class="d-flex align-items-center border-0">
										  '.$icon.'                   
										  <div class="d-flex flex-column"> 
												<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.ucwords($row->name).'</a> 
												<a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">'.ucwords($row->designation).'</a> 
										  </div>
								  </div>';
            })
            ->editColumn('Role', function ($row) {
                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.ucwords($row->designation).'</span> </div>';
            })
            ->editColumn('Contacts', function ($row) {
                $email = 'XXXXXXXXXXX';
                $mobile = 'XXXXXXXXXXX';

                if ($row->display_name === 'Administrator' || $row->display_name === 'Sub Administrator') {
                    $email = $row->email;
                    $mobile = $row->mobile;
                }

                return '<div class="d-flex flex-column"> 
									<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.$email.'</a> 
									<div style="position: relative;display: flex;">
								    <div class="iti__flag-container" data-code="'.$row->calling_code.'" data-row="'.$row->userId.'" id="phoneDisplay_'.$row->userId.'" style="display: flex;position: relative;"> <!-- Flag and dial code will be appended here --></div>
										<div style="margin-left: 6px;margin-top: 2px;">
											<a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">'.@$mobile.'</a>
										</div>
								  </div>
								  
								</div>';
            })
            ->editColumn('Status', function ($row) {
                $classname = '';
                if ($row->status == 'active') {
                    $classname = 'bg-light-success text-success';
                } else {
                    $classname = 'bg-light-danger text-danger';
                }

                return '<span class=" '.$classname.' fw-bolder fs-6 px-1">'.ucwords($row->status).'</span>';
            })
            ->rawColumns(['chkbox', 'Name', 'Role', 'Contacts', 'Status'])
            ->addIndexColumn()->make(true);

    }

    public function allreq_ajax(Request $request)
    {
        $rawId = $request->input('id') ?? $request->input('client_id');
        $company_id = (int) auth()->user()->company_id;

        $partnerCompanyId = $this->resolveAuthorizedAgencyCompanyId($company_id, $rawId);

        if (! $partnerCompanyId) {
            return response()->json(['status' => 'fail', 'message' => 'Unauthorized partner.'], 403);
        }

        $toCompanyId = $partnerCompanyId;
        $searchQuery = trim($request->input('search') ?? $request->input('q') ?? '');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10);
        $from = ($page - 1) * $size;

        $must = [];
        $must[] = [
            'bool' => [
                'should' => [
                    [
                        'nested' => [
                            'path' => 'recommendation_relation',
                            'query' => [
                                'bool' => [
                                    'must' => [
                                        ['term' => ['recommendation_relation.to_company_id' => $toCompanyId]],
                                        ['term' => ['recommendation_relation.from_company_id' => $company_id]],
                                        ['term' => ['recommendation_relation.final_status' => 1]],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        if (! empty($searchQuery)) {
            $must[] = [
                'bool' => [
                    'should' => [
                        ['match_phrase_prefix' => ['title' => $searchQuery]],
                        ['match_phrase_prefix' => ['job_title' => $searchQuery]],
                        ['match_phrase_prefix' => ['company_name' => $searchQuery]],
                        ['match_phrase_prefix' => ['client_name' => $searchQuery]],
                        ['match_phrase_prefix' => ['skills' => $searchQuery]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        $params = [
            'index' => ELASTIC_JOBS,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => [['id' => ['order' => 'desc']]],
                'query' => [
                    'bool' => [
                        'must' => $must,
                    ],
                ],
            ],
        ];

        $total = 0;
        $data = [];

        if ($this->elasticService->indexExists(ELASTIC_JOBS)) {
            try {
                $response = $this->elasticService->getClient()->search($params);
                $total = $response['hits']['total']['value'] ?? 0;
                $data = array_map(function ($hit) {
                    return $hit['_source'];
                }, $response['hits']['hits']);
            } catch (\Exception $e) {
                $total = 0;
                $data = [];
            }
        }

        // DB Fallback if ES is empty or unreachable
        if ($total == 0 && empty($searchQuery)) {
            $dbJobsQuery = DB::table('recommendation_relation')
                ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
                ->where('recommendation_relation.from_company_id', $company_id)
                ->where('recommendation_relation.to_company_id', $toCompanyId)
                ->where('recommendation_relation.final_status', 1);

            $total = $dbJobsQuery->count();
            if ($total > 0) {
                $dbHits = $dbJobsQuery
                    ->select('jobs.*')
                    ->skip($from)
                    ->take($size)
                    ->get();
                $data = $dbHits->map(function ($j) {
                    return (array) $j;
                })->toArray();
            }
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => $total > 0 ? ceil($total / $size) : 1,
            'listType' => 'allcandidate',
        ];
        $userPermissions = $this->userPermissions;

        return response()->json([
            'html' => view('service_company.ajax_pages.staffing-partner-jobs-ajax', compact('data', 'userPermissions', 'company_id', 'toCompanyId'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ]);
    }

    /**
     * Export staffing partners / consultancy data to Excel spreadsheet.
     *
     * @param  int|string  $start
     * @param  int|string  $length
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export($start, $length)
    {
        $data = request()->all();
        $filters = [
            'start' => (int) $start,
            'length' => (int) $length,
            'loginUserId' => $this->user->id ?? auth()->id(),
            'loginUserRole' => $this->user->role->role_id ?? null,
            'company_id' => (int) (auth()->user()->company_id ?? 0),
        ];

        return Excel::download(new ConsultancyExport($filters, $data), 'ConsultancyExport.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    /**
     * Atomically claim idempotency lease for an agency mutation using MySQL sys_idempotency_keys UNIQUE constraint.
     * Tenancy: Scoped to tenantId and stable logical idempotencyKey.
     *
     * @return array|null Null if successfully claimed (proceed with mutation); array response if already claimed/completed
     */
    protected function claimIdempotency(int $tenantId, string $key, array $payload): ?array
    {
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
                    return ['status' => 'error', 'message' => 'Idempotency key reused with different request payload.'];
                }
                if ($existing->status === 'COMPLETED') {
                    $cached = json_decode($existing->response_body, true);

                    return $cached ?? ['status' => 'success'];
                }
                if ($existing->status === 'PROCESSING') {
                    return ['status' => 'warning', 'message' => 'An invitation request for this email is currently being processed. Please wait a moment.'];
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
     */
    protected function completeIdempotency(int $tenantId, string $key, array $response): void
    {
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
     */
    protected function failIdempotency(int $tenantId, string $key, string $error): void
    {
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
