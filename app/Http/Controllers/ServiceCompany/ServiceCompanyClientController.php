<?php

/***######################################################## 
Controller    	: 	AdminClientController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/

namespace App\Http\Controllers\ServiceCompany;

use App\Helper\Reply;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Helper\GeneralMethod;
use Illuminate\Support\Facades\Gate;
use App\Models\Client;
use Illuminate\Support\Facades\Storage;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use App\Models\Country;
use App\Jobs\QueueNotificationMailService;
use Carbon\Carbon;
use App\Models\JobCategory;
use App\Services\MailService;
use App\Services\MassMailService;
use App\Services\NotificationMailService;
use App\Services\GoogleSheetsServiceGet;
use App\Services\ElasticService;


class ServiceCompanyClientController extends ServiceCompanyBaseController
{
	protected $mailService;
	protected $massMailService;
	protected $notificationMailService;
	protected $elasticService;
	public function __construct(MailService $mailService, GoogleSheetsServiceGet $GoogleSheetsServiceGet, MassMailService $massMailService, NotificationMailService $notificationMailService,ElasticService $elasticService)
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
		// Countries feed the Location filter dropdown on the Clients list (Agencies-style design).
		$this->countries = DB::table('countries')->get();
		return view('service_company.client.index', $this->data);
	}
	public function direct_client($id)
	{
		$this->pageTitle = __('Direct Client');
		$this->clientDataCnt = $this->commonclient();
		// Countries feed the Location filter dropdown (Agencies-style design).
		$this->countries = DB::table('countries')->get();
		return view('service_company.client.direct_client', $this->data);
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
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;
		$sort = [];
		$customSearch = trim($custom_search);
		$shouldSearch = [];
		$sort = [];
		$valCheck = request('sortingfilter');

		$cachePayload = [
			'page' => $page,
			'size' => $size,
			'q' => $searchQuery,
			'country' => request('country'),
			'company' => $company_id,
			'cus_search' => $custom_search,
			'user_id' => $this->user->id,
			'valCheck' => $valCheck
		];

		$hiringFilter = (int) $request->input('hiring', 0);
		$cachePayload['hiring'] = $hiringFilter;

		$versionKey = 'allclients_version_user_'.$this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:allclients:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
		/* ----------------------------
		⚡ Check DragonflyDB first
		----------------------------- */
		if (cache()->has($cacheKeySet)) {
			return response()->json(cache()->get($cacheKeySet));
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
						]
					]
				],
				'_source' => ['from_company_id']
			]
		]);

		// Extract company IDs
		$companyIds = collect($relationIds['hits']['hits'])
			->pluck('_source.from_company_id')
			->unique()
			->values()
			->toArray();



		if (!empty($valCheck)) {
			if ($valCheck == 1) {
				$sort[] = ['created_at' => ['order' => 'desc']];
			} elseif ($valCheck == 2) {
				$sort[] = ['created_at' => ['order' => 'asc']];
			}else{
			    $sort[] = ['id' => ['order' => 'desc']];
			}
		} else {
			$sort[] = ['id' => ['order' => 'desc']];
		}
		$must[] = [
			'bool' => [
				'should' => [
					// Case 1: self_company = 0 and from_company_id match
					[
						'bool' => [
							'must' => [
								['term' => ['company_id' => $company_id]],
								['term' => ['self_company' => 0]],
							]
						]
					],
					// Case 2: self_company = 1 and nested relation matches
					[
						'bool' => [
							'must' => [
								['term' => ['self_company' => 1]],
								[
									'terms' => [
										'company_id' => $companyIds // 🔥 from relation index
									]
								]
							]
						]
					]
				],
				'minimum_should_match' => 1,
			]
		];

		if (!empty($customSearch)) {
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
									['wildcard' => ['client_location.city_name' => $customSearchLower . '*']],
									['wildcard' => ['client_location.state_name' => $customSearchLower . '*']],
									['wildcard' => ['client_location.country_name' => $customSearchLower . '*']],
								]
							]
						]
					]
				]
			];
		
			$must[] = [
				'bool' => [
					'should' => $shouldSearch,
					'minimum_should_match' => 1,
				]
			];
		}
		// ── Hiring filter: sort clients by active job count DESC via MySQL ──
		if ($hiringFilter) {
			// Get client_ids ordered by active job count (only clients with ≥1 active job)
			$orderedClientIds = DB::table('jobs')
				->where('company_id', $company_id)
				->where('status', 'active')
				->whereNotNull('client_id')
				->select('client_id', DB::raw('COUNT(*) as active_count'))
				->groupBy('client_id')
				->orderByDesc('active_count')
				->pluck('client_id')
				->toArray();

			if (empty($orderedClientIds)) {
				$total = 0;
				$data  = [];
			} else {
				// Paginate the ordered IDs in PHP
				$total        = count($orderedClientIds);
				$pageIds      = array_slice($orderedClientIds, $from, $size);

				// Fetch those clients from ES (no sort — we reorder in PHP)
				$hiringMust   = $must;
				$hiringMust[] = ['terms' => ['client_id' => $pageIds]];

				$hiringParams = [
					'index' => ELASTIC_CLIENT_LIST,
					'body'  => [
						'size'  => $size,
						'query' => ['bool' => ['must' => $hiringMust]],
					],
				];

				$hiringResp = $this->elasticService->indexExists(ELASTIC_CLIENT_LIST)
					? $this->elasticService->getClient()->search($hiringParams)
					: ['hits' => ['hits' => []]];

				// Re-order ES hits to match MySQL active-count order
				$hitsByClientId = [];
				foreach ($hiringResp['hits']['hits'] as $hit) {
					$hitsByClientId[$hit['_source']['client_id'] ?? ''] = $hit['_source'];
				}
				$data = [];
				foreach ($pageIds as $cid) {
					if (isset($hitsByClientId[$cid])) {
						$data[] = $hitsByClientId[$cid];
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
				]
			];
			if (!$this->elasticService->indexExists(ELASTIC_CLIENT_LIST)) {
				$total = 0;
				$data  = [];
			} else {
				$response = $this->elasticService->getClient()->search($params);
				$total    = $response['hits']['total']['value'] ?? 0;
				$data     = array_map(fn($hit) => $hit['_source'], $response['hits']['hits']);
			}
		}

		$pagination = [
			'total'        => $total,
			'current_page' => $page,
			'last_page'    => ceil($total / $size),
			'listType'     => 'allcandidate',
		];

		$userPermissions = $this->userPermissions;
		$responseData = [
			'html'       => view('service_company.ajax_pages.all-client-ajax', compact('data','userPermissions','company_id'))->render(),
			'total'      => $total,
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		];

		cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_CLIENTS_TIME));

		return response()->json($responseData);



	}
	public function client_direct_data_ajax(Request $request)
	{

	    
		abort_if(Gate::denies('view_clients'), 403);

		$parentCompanyType = GeneralMethod::parentCompanyType();
		$accountCompanyType = GeneralMethod::accountCompanyType();

		$company_id = auth()->user()->company_id;
		$department = GeneralMethod::industries();
		;
		$searchTerms = request('cus_search');
		$custom_search = $searchTerms; //json_decode($searchTerms, true);

		$searchQuery = $request->input('q');
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;
		$sort = [];
		$customSearch = trim($custom_search);
		$shouldSearch = [];
		$sort = [];
		$valCheck = request('sortingfilter');

		$cachePayload = [
			'page' => $page,
			'size' => $size,
			'q' => $searchQuery,
			'country' => request('country'),
			'company' => $company_id,
			'cus_search' => $custom_search,
			'user_id' => $this->user->id,
			'valCheck' => $valCheck
		];

		$hiringFilter = (int) $request->input('hiring', 0);
		$cachePayload['hiring'] = $hiringFilter;

		$versionKey = 'alldirectclient_version_user_'.$this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:alldirectclient:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
		/* ----------------------------
		⚡ Check DragonflyDB first
		----------------------------- */
		if (cache()->has($cacheKeySet)) {
			return response()->json(cache()->get($cacheKeySet));
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
						]
					]
				],
				'_source' => ['from_company_id']
			]
		]);

		// Extract company IDs
		$companyIds = collect($relationIds['hits']['hits'])
			->pluck('_source.from_company_id')
			->unique()
			->values()
			->toArray();

		if (!empty($valCheck)) {
			if ($valCheck == 1) {
				$sort[] = ['created_at' => ['order' => 'desc']];
			} elseif ($valCheck == 2) {
				$sort[] = ['created_at' => ['order' => 'asc']];
			}else{
			    $sort[] = ['id' => ['order' => 'desc']];
			}
		} else {
			$sort[] = ['id' => ['order' => 'desc']];
		}
		$must[] = [
			'bool' => [
				'should' => [
					// Case 2: self_company = 1 and nested relation matches
					[
						'bool' => [
							'must' => [
								['term' => ['self_company' => 1]],
								// Case 2: self_company = 1 and nested relation matches
								[
									'bool' => [
										'must' => [
											['term' => ['self_company' => 1]],
											[
												'terms' => [
													'company_id' => $companyIds // 🔥 from relation index
												]
											]
										]
									]
								]
							]
						]
					]
				],
				'minimum_should_match' => 1,
			]
		];

		if (!empty($customSearch)) {
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
									['wildcard' => ['client_location.city_name' => $customSearchLower . '*']],
									['wildcard' => ['client_location.state_name' => $customSearchLower . '*']],
									['wildcard' => ['client_location.country_name' => $customSearchLower . '*']],
								]
							]
						]
					]
				]
			];
		
			$must[] = [
				'bool' => [
					'should' => $shouldSearch,
					'minimum_should_match' => 1,
				]
			];
		}
		// ── Hiring filter: sort direct clients by active job count DESC ──
		if ($hiringFilter) {
			// Direct clients: jobs shared via recommendation_relation where to_company_id = $company_id
			$orderedCompanyIds = DB::table('recommendation_relation')
				->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
				->where('recommendation_relation.to_company_id', $company_id)
				->where('recommendation_relation.final_status', 1)
				->where('jobs.status', 'active')
				->select('recommendation_relation.from_company_id', DB::raw('COUNT(jobs.id) as active_count'))
				->groupBy('recommendation_relation.from_company_id')
				->orderByDesc('active_count')
				->pluck('from_company_id')
				->toArray();

			if (empty($orderedCompanyIds)) {
				$total = 0;
				$data  = [];
			} else {
				$total   = count($orderedCompanyIds);
				$pageIds = array_slice($orderedCompanyIds, $from, $size);

				$hiringMust   = $must;
				$hiringMust[] = ['terms' => ['company_id' => $pageIds]];

				$hiringParams = [
					'index' => ELASTIC_CLIENT_LIST,
					'body'  => [
						'size'  => $size,
						'query' => ['bool' => ['must' => $hiringMust]],
					],
				];

				$hiringResp = $this->elasticService->indexExists(ELASTIC_CLIENT_LIST)
					? $this->elasticService->getClient()->search($hiringParams)
					: ['hits' => ['hits' => []]];

				$hitsByCompanyId = [];
				foreach ($hiringResp['hits']['hits'] as $hit) {
					$hitsByCompanyId[$hit['_source']['company_id'] ?? ''] = $hit['_source'];
				}
				$data = [];
				foreach ($pageIds as $cid) {
					if (isset($hitsByCompanyId[$cid])) {
						$data[] = $hitsByCompanyId[$cid];
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
				]
			];
			if (!$this->elasticService->indexExists(ELASTIC_CLIENT_LIST)) {
				$total = 0;
				$data  = [];
			} else {
				$response = $this->elasticService->getClient()->search($params);
				$total    = $response['hits']['total']['value'] ?? 0;
				$data     = array_map(fn($hit) => $hit['_source'], $response['hits']['hits']);
			}
		}

		$pagination = [
			'total'        => $total,
			'current_page' => $page,
			'last_page'    => ceil($total / $size),
			'listType'     => 'allcandidate',
		];

		$userPermissions = $this->userPermissions;
		$responseData = [
			'html'       => view('service_company.ajax_pages.direct-client-ajax', compact('data','userPermissions','company_id'))->render(),
			'total'      => $total,
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		];

		cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_DIRECT_CLIENT_TIME));

		return response()->json($responseData);


	}
	public function custom_client($id)
	{
		$this->pageTitle = __('Custom Client');
		$this->clientDataCnt = $this->commonclient();
		// Countries feed the Location filter dropdown (Agencies-style design).
		$this->countries = DB::table('countries')->get();
		return view('service_company.client.custom_client', $this->data);
	}
	public function custom_client_data_ajax(Request $request)
	{

		abort_if(Gate::denies('view_clients'), 403);
		$searchTerms = request('cus_search');
		$custom_search = $searchTerms; //json_decode($searchTerms, true);

		$company_id = '';
		$company_id = auth()->user()->company_id;
		$department = GeneralMethod::industries();


		$searchQuery = $request->input('q');
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;
		$sort = [];
		$customSearch = trim($custom_search);
		$shouldSearch = [];
		$sort = [];
		$valCheck = request('sortingfilter');

		

		$cachePayload = [
			'page' => $page,
			'size' => $size,
			'q' => $searchQuery,
			'country' => request('country'),
			'company' => $company_id,
			'cus_search' => $custom_search,
			'user_id' => $this->user->id,
			'valCheck' => $valCheck
		];

		$hiringFilter = (int) $request->input('hiring', 0);
		$cachePayload['hiring'] = $hiringFilter;

		$versionKey = 'allcustomclient_version_user_'.$this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:allcustomclient:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
		/* ----------------------------
		⚡ Check DragonflyDB first
		----------------------------- */
		if (cache()->has($cacheKeySet)) {
			return response()->json(cache()->get($cacheKeySet));
		}

		if (!empty($valCheck)) {
			if ($valCheck == 1) {
				$sort[] = ['created_at' => ['order' => 'desc']];
			} elseif ($valCheck == 2) {
				$sort[] = ['created_at' => ['order' => 'asc']];
			}else{
			    $sort[] = ['id' => ['order' => 'desc']];
			}
		} else {
			$sort[] = ['id' => ['order' => 'desc']];
		}
		$must[] = [
			'bool' => [
				'should' => [
					// Case 1: self_company = 0 and from_company_id match
					[
						'bool' => [
							'must' => [
								['term' => ['company_id' => $company_id]],
								['term' => ['self_company' => 0]],
							]
						]
					],
				],
				'minimum_should_match' => 1,
			]
		];

		if (!empty($customSearch)) {
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
									['wildcard' => ['client_location.city_name' => $customSearchLower . '*']],
									['wildcard' => ['client_location.state_name' => $customSearchLower . '*']],
									['wildcard' => ['client_location.country_name' => $customSearchLower . '*']],
								]
							]
						]
					]
				]
			];
		
			$must[] = [
				'bool' => [
					'should' => $shouldSearch,
					'minimum_should_match' => 1,
				]
			];
		}
		if ($hiringFilter) {
			// Sort internal clients by active job count DESC
			$orderedClientIds = DB::table('jobs')
				->where('company_id', $company_id)
				->where('status', 'active')
				->whereNotNull('client_id')
				->select('client_id', DB::raw('COUNT(*) as active_count'))
				->groupBy('client_id')
				->orderByDesc('active_count')
				->pluck('client_id')
				->toArray();

			if (empty($orderedClientIds)) {
				$total = 0;
				$data = [];
			} else {
				$total = count($orderedClientIds);
				$pageIds = array_slice($orderedClientIds, $from, $size);
				$hiringMust = $must;
				$hiringMust[] = ['terms' => ['client_id' => $pageIds]];
				$hiringParams = [
					'index' => ELASTIC_CLIENT_LIST,
					'body' => [
						'size' => count($pageIds),
						'query' => ['bool' => ['must' => $hiringMust]],
					]
				];
				if (!$this->elasticService->indexExists(ELASTIC_CLIENT_LIST)) {
					$total = 0;
					$data = [];
				} else {
					$hiringResponse = $this->elasticService->getClient()->search($hiringParams);
					$hits = $hiringResponse['hits']['hits'] ?? [];
					$indexed = [];
					foreach ($hits as $hit) {
						$src = $hit['_source'];
						$indexed[$src['client_id'] ?? $src['id'] ?? null] = $src;
					}
					$data = [];
					foreach ($pageIds as $cid) {
						if (isset($indexed[$cid])) {
							$data[] = $indexed[$cid];
						}
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
				'query' => [
					'bool' => [
						'must' => $must
					],
				],
			]
		];
		if (!$this->elasticService->indexExists(ELASTIC_JOBS)) {
			$total = 0;
			$data = [];
		}else{
			$response = $this->elasticService->getClient()->search($params);
			$total = $response['hits']['total']['value'] ?? 0;
			$data = array_map(function ($hit) {
				return $hit['_source'];
			}, $response['hits']['hits']);
		}
		}
        // dd($total,$data);

		$pagination = [
			'total' => $total,
			'current_page' => $page,
			'last_page' =>ceil($total / $size),
			'listType' => 'allcandidate',
		];

		$userPermissions = $this->userPermissions;
		$responseData = [
			'html' => view('service_company.ajax_pages.custom-client-ajax', compact('data','userPermissions','company_id','department'))->render(),
			'total' => $total,
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		];

		cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_CUSTOM_CLIENT_TIME));

	    return response()->json($responseData);

		
	}
	public function client_request($id)
	{
		$this->pageTitle = __('Client Request');
		$this->clientDataCnt = $this->commonclient();
		return view('service_company.client.client_request', $this->data);
	}
	public function client_request_data(Request $request)
	{
    
		abort_if(Gate::denies('view_clients'), 403);

		$company_id = '';
		$company_id = auth()->user()->company_id;


		$searchQuery = $request->input('q');
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;
		$sort = [];
		$filters = [];

		$cachePayload = [
			'page' => $page,
			'size' => $size,
			'q' => $searchQuery,
			'user_id' => $this->user->id,
			'company' => $company_id
		];

		$versionKey = 'allclientrequest_version_user_'.$this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:allclientrequest:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
		/* ----------------------------
		⚡ Check DragonflyDB first
		----------------------------- */
		if (cache()->has($cacheKeySet)) {
			// return response()->json(cache()->get($cacheKeySet));
		}


		// Apply fallback sort by ID descending
		$sort[] = ['to_invitation_status' => ['order' => 'asc', 'unmapped_type' => 'long']];

  
		
		$boolQuery = [
			'bool' => [
				'must' => [
					// First condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['to_company_id' => $company_id]],
							])
						]
					],
				],
				'filter' => $filters, // We'll add all filters here
				'must_not' => [
					['terms' => ['to_invitation_status' => [-1, 1]]]
				]
			]
		];

		// Build the final query
		$params = [
			'index' => ELASTIC_CLIENT_REQUEST,
			'request_cache' => true,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => $sort,
				'query' => $boolQuery
			]
		];

		if (!$this->elasticService->indexExists(ELASTIC_CLIENT_REQUEST)) {
			$total = 0;
			$data = [];
		}else{
			$response = $this->elasticService->getClient()->search($params);
			$total = $response['hits']['total']['value'] ?? 0;
			$data = array_map(function ($hit) {
				return $hit['_source'];
			}, $response['hits']['hits']);
		}
		
		$pagination = [
			'total' => $total,
			'current_page' => $page,
			'last_page' =>ceil($total / $size),
			'listType' => 'allcandidate',
		];

		$userPermissions = $this->userPermissions;
		$responseData =  [
			'html' => view('service_company.ajax_pages.client_request_ajax', compact('data','userPermissions'))->render(),
			'total' => $total,
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		];


		
		cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_CLIENT_REQUEST_TIME));

	    return response()->json($responseData);


	}
	public function commonclient()
	{
		$company_id = auth()->user()->company_id;
		$clientCounter = array();

		//All Clients
		$clientCounter['clientAll'] = 0;
		$this->clientAll = DB::table('companies')->select('companies.id');
		$this->clientAll = $this->clientAll->leftJoin('staffing_company_relation', 'staffing_company_relation.from_company_id', '=', 'companies.id');
		$this->clientAll = $this->clientAll->join('clients', 'clients.company_id', '=', 'companies.id');
		$this->clientAll = $this->clientAll->where(function ($query) use ($company_id) {
			return $query->where('clients.company_id', '=', $company_id)
				->where('clients.self_company', '!=', 1);
		});
		$clientAll = $this->clientAll->orWhere(function ($query) use ($company_id) {
			return
				$query->where('clients.self_company', '=', 1)
					->where('staffing_company_relation.to_company_id', '=', $company_id)
					->where('staffing_company_relation.final_status', '=', 1);
		});
		$clientAll = $this->clientAll->count();
		$clientCounter['clientAll'] = $clientAll;

		//Direct Client
		$clientCounter['clientDirect'] = 0;
		$clientDirect = DB::table('staffing_company_relation')
			->select('staffing_company_relation.id')
			->where('staffing_company_relation.to_company_id', '=', $company_id)
			->where('staffing_company_relation.final_status', '=', 1)
			->distinct()
			->count(DB::raw('CONCAT(from_company_id,"-",to_company_id)'));
		$clientCounter['clientDirect'] = $clientDirect;

		//Custom Client
		$clientCounter['clientCustom'] = 0;
		$clientCustom = Client::select('clients.id')->where('clients.self_company', '!=', 1)->where('clients.company_id', '=', $company_id)->count();
		$clientCounter['clientCustom'] = $clientCustom;

		//Client requests
		// $boolQuery = [
		// 	'bool' => [
		// 		'must' => [
		// 			[
		// 				'bool' => [
		// 					'must' => [
		// 						['term' => ['to_company_id' => $company_id]],
		// 						['term' => ['to_invitation_status' => 0]],
		// 					]
		// 				]
		// 			],
		// 		],
		// 		'filter' => [],
		// 	]
		// ];


		$boolQuery = [
			'bool' => [
				'must' => [
					// First condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['to_company_id' => $company_id]],
							])
						]
					],
				],
				'filter' => [],
				'must_not' => [
					['terms' => ['to_invitation_status' => [-1, 1]]]
				]
			]
		];


		// Count query
		$params = [
			'index' => ELASTIC_CLIENT_REQUEST,
			'body' => [
				'query' => $boolQuery
			]
		];

		$response = $this->elasticService->getClient()->count($params);

		$count = $response['count'];


		// $clientCounter['clientRequest'] = 0;
		// $clientRequest = DB::table('staffing_company_invitation')
		// 	->select('staffing_company_invitation.id')
		// 	->where('staffing_company_invitation.to_company_id', '=', $company_id)
		// 	->where('staffing_company_invitation.to_invitation_status', '=', 0)
		// 	->count();

		$clientCounter['clientRequest'] = $count;
		$clientCounter['clientAll'] = $clientCustom + $clientDirect;
		return $clientCounter;

	}
	public function viewMore($id)
	{

		abort_if(Gate::denies('view_clients'), 403);


		$cachePayload = [
			'client_id' => $id,
			'user_id' => $this->user->id
		];

		$versionKey = 'clientsviewmore_version_user_'.$this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:clientsviewmore:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
		/* ----------------------------
		⚡ Check DragonflyDB first
		----------------------------- */
		if (cache()->has($cacheKeySet)) {
			return response()->json(cache()->get($cacheKeySet));
		}


		$this->countries = Country::all();
		$this->categories = JobCategory::all();
		$this->companyType = GeneralMethod::companyType();
		$this->department = GeneralMethod::industries();
		$this->countryArr = GeneralMethod::getAllCountryList();
		$this->statelist = GeneralMethod::statelist(101);

		// $this->client = Client::find($id);
		if($id){
			$params = [
				'index' => ELASTIC_CLIENT_LIST,
				'id'    => $id,
			];
			
			$response = $this->elasticService->getClient()->get($params);
			$this->client = (object) $response['_source'];
		}

		// dd($this->client);
		$this->client_file = DB::table('client_file')->select('*')->where('client_id', $id)->get();
		// $this->countries 		= Country::all();

		$this->client_location = DB::table('client_location')
			//->join('countries', 'countries.id', '=', 'client_location.country_id')
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

				// $startOfMonth = Carbon::now()->startOfMonth();
				$startOfMonth = Carbon::now()->startOfMonth()->subMonths(3);
				$endOfMonth =  Carbon::now()->endOfMonth();

				// dd($startOfMonth,$endOfMonth);
				
				$startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(6);
				$endOfMonthcomman =  Carbon::now()->endOfMonth();

				// dd($startOfMonth,$endOfMonth);

				$must = [];
				$should = [];
				$filter = [];
				$sort = [];
				$boolQuery = [
						'bool' => [
							'should' => [
								// First condition
								[
									'bool' => [
										'must' => array_merge([
											['term' => ['client_id' => $id]],
											['term' => ['ClientComID' => 0]],
										])
									]
								],
								// Second condition
								[
									'bool' => [
										'must' => array_merge([
											['term' => ['client_id' => $id]],
											['range' => ['ClientComID' => ['gt' => 0]]],
											['range' => ['jobId' => ['gt' => 0]]],
										], [
											['bool' => [
												'must_not' => [
													['terms' => ['job_master_status_id' => [8,9]]]
												]
											]]
										])
									]
								],
								// Third condition
								[
									'bool' => [
										'must' => array_merge([
											['term' => ['client_id' => $id]],
											['range' => ['jobId' => ['gt' => 0]]],
										])
									]
								],
							],
							'minimum_should_match' => 1,
							'filter' => [
								[
									'bool' => [
										'should' => [
											[
												'range' => [
													'updated_at' => [
														'gte' => $startOfMonth,
														'lte' => $endOfMonth,
													],
												],
											],
										],
										'minimum_should_match' => 1,
									],
								],
							],
						]
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
										'field' => 'job_master_status_id', // Use `.keyword` for exact match
										'size' => 10 // Increase if you have more unique statuses
									]
								]
							],
						],
				];

				$response = $this->elasticService->getClient()->search($params);
				$bucketData = $response['aggregations']['status_counts']['buckets'] ?? [];
				$this->result = collect($bucketData)->mapWithKeys(function ($item) {
					return [$item['key'] => $item['doc_count']];
				});

				$this->joined_count = $this->result->get('6',0);
				$this->sourcing_count = $this->result->get('1',0);
				$this->screening_count = $this->result->get('2',0);
				$this->evaluation_count = $this->result->get('3',0);
				$this->negotiation_count = $this->result->get('4',0);
				$this->offer_count = $this->result->get('5',0);
				$this->Rejected_count = $this->result->get('7',0);
				$this->inreview_count = $this->result->get('8',0);

				$this->total_candidates =$this->inreview_count+$this->sourcing_count+$this->screening_count+$this->evaluation_count+$this->negotiation_count+$this->offer_count+$this->joined_count+$this->Rejected_count;

				$this->startOfMonth = $startOfMonth;
				$this->endOfMonth = $endOfMonth;
				
				// dd($this->total_candidates,$this->inreview_count,$this->sourcing_count,$this->screening_count,$this->evaluation_count,$this->negotiation_count,$this->offer_count,$this->joined_count,$this->Rejected_count);



				/** Start Jobs Count Graph */
				$must = [];
				$filter = [];
				$must[] = [
						'bool' => [
							'should' => [
								['term' => ['client_id' => $id]],
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
						'query' => [
							'bool' => [
								'must' => $must,
								'filter' => $filter,
							],
						],
						'aggs' => [
							'status_counts' => [
								'terms' => [
									'field' => 'status', // Use `.keyword` for exact match
									'size' => 10 // Increase if you have more unique statuses
								]
							]
						],
					],
				];

				$response = $this->elasticService->getClient()->search($params);
				$bucketData = $response['aggregations']['status_counts']['buckets'] ?? [];
				$this->counts = collect($bucketData)->mapWithKeys(function ($item) {
					return [$item['key'] => $item['doc_count']];
				});

				/** End Jobs Count Graph */

				

				/** Start Candidate last 7 days */	
				
					$endDate = Carbon::today()->endOfDay();
					$startDate = Carbon::today()->subDays(6)->startOfDay();
					// dd($startDate,$endDate);
					$paramsCandidates = [
						'index' => ELASTIC_CANDIDATES,
						'request_cache' => true,
						'body' => [
							'track_total_hits' => true,
							'size' => 0,
							'query' => [
								'bool' => [
									'must' => [
											['term' => ['client_id' => $id]],
									],
									'filter' => [
										[
											'range' => [
												'created_at' => [
													'gte' => $startDate->toDateString(),
													'lte' => $endDate->toDateString()
												]
											]
										]
									]
								]
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
											'max' => $endDate->toDateString()
										]
									]
								]
							]
						]
					];

					$responseCandidates = $this->elasticService->getClient()->search($paramsCandidates);
					$candidateBuckets = $responseCandidates['aggregations']['uploads_over_time']['buckets'] ?? [];
					
					$labels = [];
					$candidateValues = [];

					for ($i = 0; $i < 7; $i++) {
						$date = $startDate->copy()->addDays($i);
						$formatted = $date->format('M d'); // e.g., 'Jul 16'
						$labels[] = $formatted;
						// Find matching doc_count from candidates
						$candidateBucket = collect($candidateBuckets)->firstWhere('key_as_string', $date->format('Y-m-d'));

						$candidateValues[] = $candidateBucket['doc_count'] ?? 0;
					}
					// dd($labels);
					$this->labels = $labels ; 
					$this->candidateValues = $candidateValues;
					$this->candidateValuesTotal = array_sum($candidateValues);

				/** End Candidate last 7 days */	

				/** Start Offer Count Graph */			
					$boolQuery = [
						'bool' => [
							'should' => [
								// First condition
								[
									'bool' => [
										'must' => array_merge([
											['term' => ['client_id' => $id]],
											['term' => ['ClientComID' => 0]],
										])
									]
								],
								// Second condition
								[
									'bool' => [
										'must' => array_merge([
											['term' => ['client_id' => $id]],
											['range' => ['ClientComID' => ['gt' => 0]]],
											['range' => ['job_id' => ['gt' => 0]]],
										], [
											['bool' => [
												'must_not' => [
													['terms' => ['job_master_status_id' => [8,9]]]
												]
											]]
										])
									]
								],
								// Third condition
								[
									'bool' => [
										'must' => array_merge([
											['term' => ['client_id' => $id]],
											['range' => ['job_id' => ['gt' => 0]]],
										])
									]
								],
							],
							'minimum_should_match' => 1,
							'filter' => [
								[
									'bool' => [
										'should' => [
											[
												'range' => [
													'of_created_date' => [
														'gte' => $startOfMonthcomman->toDateString(),
														'lte' => $endOfMonthcomman->toDateString(),
													],
												],
											],
										],
										'minimum_should_match' => 1,
									],
								],
							],
						]
					];

					$params = [
						'index' => ELASTIC_CANDIDATES,
						'request_cache' => true,
						'body' => [
							'track_total_hits' => true,
							'size' => 0,
							'query' => $boolQuery,
							'aggs' => [
								'of_offer_count' => [
									'date_histogram' => [
										'field' => 'of_created_date',
										'calendar_interval' => 'month',
										'format' => 'yyyy-MM',
										'min_doc_count' => 0,
										'extended_bounds' => [
											'min' => $startOfMonthcomman->format('Y-m'),
											'max' => $endOfMonthcomman->format('Y-m')
										]
									]
								]
							],
						],
					];
					$response = $this->elasticService->getClient()->search($params);
					$candidateBucketsoffer = $response['aggregations']['of_offer_count']['buckets'] ?? [];

					$candidateValuesCommoffer = [];
					$labelsCommoffer = [];
					$startDateComm = Carbon::now();
					for ($i = 5; $i >= 0; $i--) {
							$startDate = $startDateComm->copy()->startOfMonth()->subMonths($i);
							$endDate   = $startDate->copy()->endOfMonth();
							// dd($startDate,$endDate);
							$formattedComm = $startDate->format('M Y'); // e.g., 'Jul 16'
							$labelsCommoffer[] = $formattedComm;
							$candidateBucketoffer = collect($candidateBucketsoffer)->firstWhere('key_as_string', $startDate->format('Y-m'));
							$candidateValuesCommoffer[] = $candidateBucketoffer['doc_count'] ?? 0;
				

					}
					$this->labelsCommoffer = $labelsCommoffer ; 
					$this->candidateValuesCommoffer = $candidateValuesCommoffer;
					//  dd($this->candidateValuesCommoffer,$this->labelsCommoffer);
					$this->candidateValuesTotaloffer = array_sum($candidateValuesCommoffer);

				/** End Offer count Graph */

				/** Start Candidate list joined*/					
                    $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(6);
				    $endOfMonthcomman =  Carbon::now()->endOfMonth();
				
				 	$must = [];
					$should = [];
					$filter = [];
					$sort = [];					

					$boolQuery = [
						'bool' => [
							'should' => [
								// First condition
								[
									'bool' => [
										'must' => array_merge([
											['term' => ['client_id' => $id]],
											['term' => ['job_master_status_id' => 6]],
											['term' => ['ClientComID' => 0]],
										])
									]
								],
								// Second condition
								[
									'bool' => [
										'must' => array_merge([
											['term' => ['client_id' => $id]],
											['term' => ['job_master_status_id' => 6]],
											['range' => ['ClientComID' => ['gt' => 0]]],
											['range' => ['job_id' => ['gt' => 0]]],
										])
									]
								],
								// Third condition
								[
									'bool' => [
										'must' => array_merge([
											['term' => ['client_id' => $id]],
											['term' => ['job_master_status_id' => 6]],
											['range' => ['job_id' => ['gt' => 0]]],
										])
									]
								],
							],
							'minimum_should_match' => 1,
							'filter' => [
								[
									'bool' => [
										'should' => [
											[
												'range' => [
													'of_joined_date' => [
														'gte' => $startOfMonthcomman->toDateString(),
														'lte' => $endOfMonthcomman->toDateString(),
													],
												],
											],
										],
										'minimum_should_match' => 1,
									],
								],
							],
						]
					];
					$sort[] = ['of_joined_date' => ['order' => 'desc']];
					$params = [
						'index' => ELASTIC_CANDIDATES,
						'request_cache' => true,
						'body' => [
							'track_total_hits' => true,
							'size' => 6,
							'sort' => $sort,
							'query' => $boolQuery,
							'aggs' => [
								'of_joined_count' => [
									'date_histogram' => [
										'field' => 'of_joined_date',
										'calendar_interval' => 'month',
										'format' => 'yyyy-MM',
										'min_doc_count' => 0,
										'extended_bounds' => [
											'min' => $startOfMonthcomman->format('Y-m'),
											'max' => $endOfMonthcomman->format('Y-m')
										]
									]
								]
							]
						],
					];
					$response = $this->elasticService->getClient()->search($params);
					$this->total_candidates_join = $response['hits']['total']['value'] ?? 0;
					$data = array_map(function ($hit) {
						return $hit['_source'];
					}, $response['hits']['hits']);
                    $this->data_list = $data;
					$candidateBucketsjoin = $response['aggregations']['of_joined_count']['buckets'] ?? [];


					$candidateValuesCommjoin = [];
					$labelsCommjoin = [];
					 $startDateComm = Carbon::now();
				 for ($i = 5; $i >= 0; $i--) {
						$startDate = $startDateComm->copy()->startOfMonth()->subMonths($i);
                        $endDate   = $startDate->copy()->endOfMonth();
						// dd($startDate,$endDate);
						$formattedComm = $startDate->format('M Y'); // e.g., 'Jul 16'
					    $labelsCommjoin[] = $formattedComm;
						$candidateBucketjoin = collect($candidateBucketsjoin)->firstWhere('key_as_string', $startDate->format('Y-m'));
                        $candidateValuesCommjoin[] = $candidateBucketjoin['doc_count'] ?? 0;
			

				 }
				 $this->labelsCommjoin = $labelsCommjoin ; 
				 $this->candidateValuesCommjoin = $candidateValuesCommjoin;
				//  dd($this->candidateValuesCommjoin,$this->labelsCommjoin);
				 $this->candidateValuesTotaljoin = array_sum($candidateValuesCommjoin);

				/** End Candidate list joined*/


				/** Start Interview Section */
		
				$page = 1;
				$size = 5; // Default 20 results per page
				$from = ($page - 1) * $size;
				$must = [];
				$should = [];
				$filter = [];
				$nowMinus1Hour = now()->subHour()->toIso8601String();
				$must[] = [
					'bool' => [
						'should' => [
							['term' => ['client_id' => $id]],
						],
						'minimum_should_match' => 1,
					]
				];

				$upcomingParams = [
					'index' => ELASTIC_INERTVIEW_TRACKER,
					'request_cache' => true,
					'body' => [
						'from' => $from,
						'size' => $size,
						'sort' => [
							['schedule_date' => ['order' => 'asc']]
						],
						'query' => [
							'bool' => [
								'must' => $must,
								'filter' => [
									[
										'range' => [
											'schedule_date' => [
												'gte' => $nowMinus1Hour,
												'format' => 'strict_date_optional_time'
											]
										]
									]
								]
							]
						]
					]
				];

				$upcomingData = [];
				$upcomingTotal = 0;

				if ($this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
					$response = $this->elasticService->getClient()->search($upcomingParams);
					$upcomingTotal = $response['hits']['total']['value'] ?? 0;
					$upcomingData = array_map(fn($hit) => $hit['_source'], $response['hits']['hits']);
				}
				$this->upcomingData = $upcomingData;

			    //////////////////////////////////// last 7 days
                $endDate = Carbon::today()->endOfDay();
				$startDate = Carbon::today()->subDays(6)->startOfDay();
				$recentParams = [
					'index' => ELASTIC_INERTVIEW_TRACKER,
					'request_cache' => true,
					'body' => [
						'from' => $from,
						'size' => $size,
						'sort' => [
							['schedule_date' => ['order' => 'desc']]
						],
						'query' => [
							'bool' => [
								'must' => $must,
								'filter' => [
									[
										'range' => [
												'schedule_date' => [
													'gte' => $startDate->toDateString(),
													'lte' => $endDate->toDateString()
												]
											]
									]
								]
							]
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
											'max' => $endDate->toDateString()
										]
									]
								]
							]
					]
				];

				$recentData = [];
				$recentTotal = 0;

				if ($this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
					$response = $this->elasticService->getClient()->search($recentParams);
					$interviewBuckets = $response['aggregations']['uploads_over_time']['buckets'] ?? [];
					$recentTotal = $response['hits']['total']['value'] ?? 0;
					$recentData = array_map(fn($hit) => $hit['_source'], $response['hits']['hits']);
				}
				$this->recentData = $recentData;

				$interviewlabels = [];
				$interviewValues = [];

				for ($i = 0; $i < 7; $i++) {
					// dd($date->format('Y-m-d'));
					$date = $startDate->copy()->addDays($i);
					$formatted = $date->format('M d'); // e.g., 'Jul 16'
					$interviewlabels[] = $formatted;
					// Find matching doc_count from candidates
					$interviewBucket = collect($interviewBuckets)->firstWhere('key_as_string', $date->format('Y-m-d'));

					$interviewValues[] = $interviewBucket['doc_count'] ?? 0;
				}
				$this->interviewlabels = $interviewlabels ; 
				$this->interviewValues = $interviewValues;
				// dd($this->interviewlabels);
				$this->interviewValuesTotal = array_sum($interviewValues);



				//////////////////////////////////// last 6 months
				$startDateComm = Carbon::now();
				$startDate = $startDateComm->copy()->startOfMonth()->subMonths(5);
				$endDate   = $startDateComm;
					$recentParamsMonth = [
						'index' => ELASTIC_INERTVIEW_TRACKER,
						'request_cache' => true,
						'body' => [
							'from' => $from,
							'size' => $size,
							'sort' => [
								['schedule_date' => ['order' => 'desc']]
							],
							'query' => [
								'bool' => [
									'must' => $must,
									'filter' => [
										[
											'range' => [
													'schedule_date' => [
														'gte' => $startDate->toDateString(),
														'lte' => $endDate->toDateString()
													]
												]
										]
									]
								]
								],
								'aggs' => [
									'uploads_over_time' => [
										'date_histogram' => [
											'field' => 'schedule_date',
											'calendar_interval' => 'month',
											'format' => 'yyyy-MM-dd',
											'min_doc_count' => 0,
											'extended_bounds' => [
												'min' => $startDate->toDateString(),
												'max' => $endDate->toDateString()
											]
										]
									]
								]
						]
					];

				$recentDataMonth = [];
				$recentTotalMonth = 0;

				if ($this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
					$responseMonth = $this->elasticService->getClient()->search($recentParamsMonth);
					$interviewBucketsMonth = $responseMonth['aggregations']['uploads_over_time']['buckets'] ?? [];
					$recentTotalMonth = $responseMonth['hits']['total']['value'] ?? 0;
					$recentDataMonth = array_map(fn($hit) => $hit['_source'], $responseMonth['hits']['hits']);
				}
				$this->recentDataMonth = $recentDataMonth;

				// dd($this->recentDataMonth);

				$interviewlabelsMonth = [];
				$interviewValuesMonth = [];

				for ($i = 5; $i >= 0; $i--) {
					$startDate = $startDateComm->copy()->startOfMonth()->subMonths($i);	
					// dd($startDate->format('Y-m'),$this->recentDataMonth);			
					$formatted = $startDate->format('M Y'); // e.g., 'Jul 16'
					$interviewlabelsMonth[] = $formatted;
					$interviewBucket = collect($interviewBucketsMonth)->firstWhere('key_as_string', $startDate->format('Y-m-01'));
					// collect($interviewBucketsMonth)->firstWhere('key_as_string', $startDate->format('Y-m'));

					$interviewValuesMonth[] = $interviewBucket['doc_count'] ?? 0;
				}
				$this->interviewlabelsMonth = $interviewlabelsMonth ; 
				$this->interviewValuesMonth = $interviewValuesMonth;
				$this->interviewValuesTotalMonth = array_sum($interviewValuesMonth);
				
	           /** End Interview Section */


		

		$view = view('service_company.client.show', $this->data)->render();
		$responseData =  Reply::dataOnly(['status' => 'success', 'view' => $view]);
		
		cache()->put($cacheKeySet, $responseData, now()->addMinutes(CLIENTS_VIEWMORE_TIME));

	    return response()->json($responseData);



	}
	public function contactdata(Request $request)
	{

		$clientId = '';
		$clientId = $_REQUEST['clientId'];

		$available_status = '';
		$available_status = GeneralMethod::available_status();
		$feedback_status = '';
		$feedback_status = GeneralMethod::feedback_status();
		$interview_mode_arr = '';
		$interview_mode_arr = GeneralMethod::interview_mode();

		$shedule = DB::table('client_contact_info')
			->select('client_contact_info.id', 'client_contact_info.contact_person_name','client_contact_info.calling_code', 'client_contact_info.contact_person_email', 'client_contact_info.contact_person_number', 'client_contact_info.contact_person_designation')
			->where('client_contact_info.client_id', $request->clientId)
			->get();

		if ($request->jobs != 'all' && $request->jobs != '') {
			// $jobApplications = $jobApplications->where('job_applications.job_id', $request->jobs);
		}

		// Avatar colour palette (matches the Agencies-style contacts table).
		$avatarColors = ['#c084fc', '#60a5fa', '#fbbf24', '#34d399', '#f472b6', '#38bdf8', '#fb923c'];

		return DataTables::of($shedule)
			->editColumn('chkbox', function ($row) {
				return '<input type="checkbox" class="table-checkbox" />';
			})
			->editColumn('Contact', function ($row) use ($avatarColors) {
				$name = trim((string) @$row->contact_person_name);
				$initials = strtoupper(implode('', array_map(fn($w) => mb_substr($w, 0, 1),
					array_slice(array_filter(explode(' ', $name)), 0, 2))));
				$color = $avatarColors[(ord(strtolower(substr($name, 0, 1) ?: 'a'))) % count($avatarColors)];
				return '<div class="contact-cell">
							<div class="contact-avatar-table" style="background:' . $color . '">' . $initials . '</div>
							<div class="contact-info-table">
								<div class="contact-name-table">' . ucwords($name) . '</div>
							</div>
						</div>';
			})
			->editColumn('Designation', function ($row) {
				return ucwords((string) @$row->contact_person_designation);
			})
			->editColumn('Email', function ($row) {
				return '<span class="email-cell">' . @$row->contact_person_email . '</span>';
			})
			->editColumn('Phone', function ($row) {
				return '<div class="phone-cell">
							<div class="iti__flag-container" data-code="' . $row->calling_code . '" data-row="' . $row->id . '" id="phoneDisplay_' . $row->id . '" style="display:flex;position:relative;"></div>
							<span class="phone-digits">' . @$row->contact_person_number . '</span>
						</div>';
			})
			->editColumn('Status', function ($row) {
				return '<span class="status-badge-table active"><i class="fas fa-circle"></i>Active</span>';
			})
			->editColumn('Actions', function ($row) {
				$call = $row->calling_code . ' ' . $row->contact_person_number;
				return '<div class="action-btns">
							<a href="tel:' . trim($call) . '" class="action-btn-table" title="Call"><i class="fas fa-phone"></i></a>
							<a href="mailto:' . $row->contact_person_email . '" class="action-btn-table" title="Email"><i class="fas fa-envelope"></i></a>
						</div>';
			})
			->rawColumns(['chkbox', 'Contact', 'Email', 'Phone', 'Status', 'Actions'])
			->make(true);

	}
	public function dviewMore($id)
	{
		abort_if(Gate::denies('view_clients'), 403);
		$company_id = auth()->user()->company_id;

		$cachePayload = [
			'client_id' => $id,
			'user_id' => $this->user->id
		];

		$versionKey = 'clientsdviewmore_version_user_'.$this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:clientsdviewmore:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
		/* ----------------------------
		⚡ Check DragonflyDB first
		----------------------------- */
		if (cache()->has($cacheKeySet)) {
			return response()->json(cache()->get($cacheKeySet));
		}

		$this->countries = Country::all();
		$this->categories = JobCategory::all();
		$this->companyType = GeneralMethod::industries_type_arr();//GeneralMethod::accountCompanyType();
		$this->department = GeneralMethod::department();
		$this->industries=	GeneralMethod::industries();
		$this->countryArr = GeneralMethod::getAllCountryList();
		$this->statelist = GeneralMethod::statelist(101);


		$this->directClientDetail = DB::table('clients')
			->select(

				'clients.id as client_id',
				'clients.name',
				'clients.logo_logical_path',
				'clients.industry',
				'clients.tags',
				'clients.description',
				'companies.website',
				'companies.description as companies_description',
				'companies.id as companies_id',
				'companies.logo',
				'companies.company_type',
				'companies.industries',
				'companies.industries_type',
				'companies.company_name',
				'companies.company_size',
				'companies.founded',
				'staffing_company_relation.from_company_id',
				'staffing_company_relation.created_at as to_created_at'
			)
			->join('companies', 'companies.id', '=', 'clients.company_id')
			->join('staffing_company_relation', 'staffing_company_relation.from_company_id', '=', 'companies.id')
			->where('clients.id', '=', $id)
			->where('staffing_company_relation.to_company_id', '=', $company_id)
			->first();

		// dd($this->directClientDetail->from_company_id);
		$from_company_id = $this->directClientDetail->from_company_id;


		$this->client_file = DB::table('client_file')->select('*')->where('client_id', $id)->get();

		$this->client_location = DB::table('client_location')
			->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
			->where('client_location.client_id', $id)
			->orderBy('client_location.id', 'DESC')
			->first();
		$this->city_name = '';

		if (!empty($this->client_location->country_id) && ($this->client_location->country_id == 101)) {
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

		$this->client_contact_info = DB::table('client_contact_info')->select('*')->where('client_id', $id)->take(1)->get();
		$this->client_contact_count = DB::table('client_contact_info')->select('*')->where('client_id', $id)->count();
		$this->active_requisition = DB::table('jobs')->select('jobs.id')->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')->where('recommendation_relation.to_company_id', $company_id)->where('jobs.status', 'active')->count();

		if($id){
			$params = [
				'index' => ELASTIC_CLIENT_LIST,
				'id'    => $id,
			];
			
			$response = $this->elasticService->getClient()->get($params);
			$this->directClientDetail = (object) $response['_source'];

			// dd($this->directClientDetail);
		}

		
			

				// $startOfMonth = Carbon::now()->startOfMonth();
				$startOfMonth = Carbon::now()->startOfMonth()->subMonths(3);
				$endOfMonth =  Carbon::now()->endOfMonth();

				// dd($startOfMonth,$endOfMonth);
				
				$startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(6);
				$endOfMonthcomman =  Carbon::now()->endOfMonth();

				// dd($startOfMonth,$endOfMonth); $company_id
                // dd($from_company_id,$company_id);
				$must = [];
				$should = [];
				$filter = [];
				$sort = [];

				$boolQuery = [
					'bool' => [
						'must' => [
							// First required condition
							[
								'bool' => [
									'must' => [
										['term' => ['from_company_id' => $from_company_id]],
										['term' => ['to_company_id' => $company_id]],
										['term' => ['ClientComID' => $company_id]],
										['term' => ['final_status' => 1]],
									],
									'must_not' => [
										['terms' => ['job_master_status_id' => [8,9]]]
									]
								]
							],
							// Second required condition
							[
								'range' => [
									'updated_at' => [
										'gte' => $startOfMonth,
										'lte' => $endOfMonth,
									]
								]
							]

						]
					]
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
										'field' => 'job_master_status_id', // Use `.keyword` for exact match
										'size' => 10 // Increase if you have more unique statuses
									]
								]
							],
						],
				];

				$response = $this->elasticService->getClient()->search($params);
				$bucketData = $response['aggregations']['status_counts']['buckets'] ?? [];
				$this->result = collect($bucketData)->mapWithKeys(function ($item) {
					return [$item['key'] => $item['doc_count']];
				});

				$this->joined_count = $this->result->get('6',0);
				$this->sourcing_count = $this->result->get('1',0);
				$this->screening_count = $this->result->get('2',0);
				$this->evaluation_count = $this->result->get('3',0);
				$this->negotiation_count = $this->result->get('4',0);
				$this->offer_count = $this->result->get('5',0);
				$this->Rejected_count = $this->result->get('7',0);
				$this->inreview_count = $this->result->get('8',0);

				$this->total_candidates =$this->inreview_count+$this->sourcing_count+$this->screening_count+$this->evaluation_count+$this->negotiation_count+$this->offer_count+$this->joined_count+$this->Rejected_count;

				$this->startOfMonth = $startOfMonth;
				$this->endOfMonth = $endOfMonth;
				
				// dd($this->total_candidates,$this->inreview_count,$this->sourcing_count,$this->screening_count,$this->evaluation_count,$this->negotiation_count,$this->offer_count,$this->joined_count,$this->Rejected_count);


				/** Start Jobs Count Graph */
				$must = [];
				$filter = [];
				$must[] = [
					'nested' => [
						'path' => 'recommendation_relation',
						'query' => [
							'bool' => [
								'filter' => [
									[
										'term' => [
											'recommendation_relation.from_company_id' => (int)$from_company_id
										]
									],
									[
										'term' => [
											'recommendation_relation.to_company_id' => (int)$company_id
										]
									],
									[
										'terms' => [
											'recommendation_relation.final_status' => [1, 2, 3]
										]
									]
								]
							]
						]
					]
				];

				// $must[] = [
				// 		'bool' => [
				// 			'should' => [
				// 				['term' => ['client_id' => $id]],
				// 			],
				// 			'minimum_should_match' => 1,
				// 		],
				// ];

				$params = [
					'index' => ELASTIC_JOBS,
					'request_cache' => true,
					'body' => [
						'track_total_hits' => true,
						'size' => 0,
						'query' => [
							'bool' => [
								'must' => $must,
								'filter' => $filter,
							],
						],
						'aggs' => [
							'status_counts' => [
								'terms' => [
									'field' => 'status', // Use `.keyword` for exact match
									'size' => 10 // Increase if you have more unique statuses
								]
							]
						],
					],
				];

				$response = $this->elasticService->getClient()->search($params);
				$bucketData = $response['aggregations']['status_counts']['buckets'] ?? [];
				$this->counts = collect($bucketData)->mapWithKeys(function ($item) {
					return [$item['key'] => $item['doc_count']];
				});

				/** End Jobs Count Graph */

				

				/** Start Candidate last 7 days */	
				
					$endDate = Carbon::today()->endOfDay();
					$startDate = Carbon::today()->subDays(6)->startOfDay();
					// dd($startDate,$endDate);


					$paramsCandidates = [
						'index' => ELASTIC_CANDIDATES,
						'request_cache' => true,
						'body' => [
							'track_total_hits' => true,
							'size' => 0,
							'query' => [
								'bool' => [
									'must' => [
										
										// First required condition
										[
											'bool' => [
												'must' => [
													['term' => ['from_company_id' => $from_company_id]],
													['term' => ['to_company_id' => $company_id]],
													['term' => ['ClientComID' => $company_id]],
													['term' => ['final_status' => 1]],
												],
												'must_not' => [
													['terms' => ['job_master_status_id' => [8,9]]]
												]
											]
										],
										// Second required condition
										[
											'range' => [
												'updated_at' => [
													'gte' => $startDate->toDateString(),
													'lte' =>$endDate->toDateString(),
												]
											]
										]
									]
								]
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
											'max' => $endDate->toDateString()
										]
									]
								]
							]
						]
					];

					$responseCandidates = $this->elasticService->getClient()->search($paramsCandidates);
					$candidateBuckets = $responseCandidates['aggregations']['uploads_over_time']['buckets'] ?? [];
					
					$labels = [];
					$candidateValues = [];

					for ($i = 0; $i < 7; $i++) {
						$date = $startDate->copy()->addDays($i);
						$formatted = $date->format('M d'); // e.g., 'Jul 16'
						$labels[] = $formatted;
						// Find matching doc_count from candidates
						$candidateBucket = collect($candidateBuckets)->firstWhere('key_as_string', $date->format('Y-m-d'));

						$candidateValues[] = $candidateBucket['doc_count'] ?? 0;
					}
					// dd($labels);
					$this->labels = $labels ; 
					$this->candidateValues = $candidateValues;
					$this->candidateValuesTotal = array_sum($candidateValues);

				/** End Candidate last 7 days */	

				/** Start Offer Count Graph */			
					$boolQuery = [
						'bool' => [
							'must' => [
								[
									'term' => ['from_company_id' => $from_company_id]
								],
								[
									'term' => ['to_company_id' => $company_id]
								],
								[
									'term' => ['ClientComID' => $company_id]
								],
								[
									'term' => ['final_status' => 1]
								],
								[
									'range' => [
										'of_created_date' => [
											'gte' => $startOfMonthcomman->toDateString(),
											'lte' => $endOfMonthcomman->toDateString(),
										]
									]
								]
							]
						]
					];

					$params = [
						'index' => ELASTIC_CANDIDATES,
						'request_cache' => true,
						'body' => [
							'track_total_hits' => true,
							'size' => 0,
							'query' => $boolQuery,
							'aggs' => [
								'of_offer_count' => [
									'date_histogram' => [
										'field' => 'of_created_date',
										'calendar_interval' => 'month',
										'format' => 'yyyy-MM',
										'min_doc_count' => 0,
										'extended_bounds' => [
											'min' => $startOfMonthcomman->format('Y-m'),
											'max' => $endOfMonthcomman->format('Y-m')
										]
									]
								]
							],
						],
					];
					$response = $this->elasticService->getClient()->search($params);
					$candidateBucketsoffer = $response['aggregations']['of_offer_count']['buckets'] ?? [];

					$candidateValuesCommoffer = [];
					$labelsCommoffer = [];
					$startDateComm = Carbon::now();
					for ($i = 5; $i >= 0; $i--) {
							$startDate = $startDateComm->copy()->startOfMonth()->subMonths($i);
							$endDate   = $startDate->copy()->endOfMonth();
							// dd($startDate,$endDate);
							$formattedComm = $startDate->format('M Y'); // e.g., 'Jul 16'
							$labelsCommoffer[] = $formattedComm;
							$candidateBucketoffer = collect($candidateBucketsoffer)->firstWhere('key_as_string', $startDate->format('Y-m'));
							$candidateValuesCommoffer[] = $candidateBucketoffer['doc_count'] ?? 0;
				

					}
					$this->labelsCommoffer = $labelsCommoffer ; 
					$this->candidateValuesCommoffer = $candidateValuesCommoffer;
					//  dd($this->candidateValuesCommoffer,$this->labelsCommoffer);
					$this->candidateValuesTotaloffer = array_sum($candidateValuesCommoffer);

				/** End Offer count Graph */

				/** Start Candidate list joined*/					
                    $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(6);
				    $endOfMonthcomman =  Carbon::now()->endOfMonth();
				
				 	$must = [];
					$should = [];
					$filter = [];
					$sort = [];	
					
					$boolQuery = [
						'bool' => [
							'must' => [
								[
									'term' => ['from_company_id' => $from_company_id]
								],
								[
									'term' => ['to_company_id' => $company_id]
								],
								[
									'term' => ['ClientComID' => $company_id]
								],
								[
									'term' => ['final_status' => 1]
								],
								[
									'range' => [
										'of_joined_date' => [
											'gte' => $startOfMonthcomman->toDateString(),
											'lte' => $endOfMonthcomman->toDateString(),
										]
									]
								]
							]
						]
					];

					
					$sort[] = ['of_joined_date' => ['order' => 'desc']];
					$params = [
						'index' => ELASTIC_CANDIDATES,
						'request_cache' => true,
						'body' => [
							'track_total_hits' => true,
							'size' => 6,
							'sort' => $sort,
							'query' => $boolQuery,
							'aggs' => [
								'of_joined_count' => [
									'date_histogram' => [
										'field' => 'of_joined_date',
										'calendar_interval' => 'month',
										'format' => 'yyyy-MM',
										'min_doc_count' => 0,
										'extended_bounds' => [
											'min' => $startOfMonthcomman->format('Y-m'),
											'max' => $endOfMonthcomman->format('Y-m')
										]
									]
								]
							]
						],
					];
					$response = $this->elasticService->getClient()->search($params);
					$this->total_candidates_join = $response['hits']['total']['value'] ?? 0;
					$data = array_map(function ($hit) {
						return $hit['_source'];
					}, $response['hits']['hits']);
                    $this->data_list = $data;
					$candidateBucketsjoin = $response['aggregations']['of_joined_count']['buckets'] ?? [];


					$candidateValuesCommjoin = [];
					$labelsCommjoin = [];
					 $startDateComm = Carbon::now();
				 for ($i = 5; $i >= 0; $i--) {
						$startDate = $startDateComm->copy()->startOfMonth()->subMonths($i);
                        $endDate   = $startDate->copy()->endOfMonth();
						// dd($startDate,$endDate);
						$formattedComm = $startDate->format('M Y'); // e.g., 'Jul 16'
					    $labelsCommjoin[] = $formattedComm;
						$candidateBucketjoin = collect($candidateBucketsjoin)->firstWhere('key_as_string', $startDate->format('Y-m'));
                        $candidateValuesCommjoin[] = $candidateBucketjoin['doc_count'] ?? 0;
			

				 }
				 $this->labelsCommjoin = $labelsCommjoin ; 
				 $this->candidateValuesCommjoin = $candidateValuesCommjoin;
				//  dd($this->candidateValuesCommjoin,$this->labelsCommjoin);
				 $this->candidateValuesTotaljoin = array_sum($candidateValuesCommjoin);

				/** End Candidate list joined*/


				 /** Start Interview Section */
		
				$page = 1;
				$size = 5; // Default 20 results per page
				$from = ($page - 1) * $size;
				$must = [];
				$should = [];
				$filter = [];
				$nowMinus1Hour = now()->subHour()->toIso8601String();
				$must[] = [
					'bool' => [
						'must' => [
								[
									'term' => ['from_company_id' => $from_company_id]
								],
								[
									'term' => ['to_company_id' => $company_id]
								]
						],
					]
				];

				$upcomingParams = [
					'index' => ELASTIC_INERTVIEW_TRACKER,
					'request_cache' => true,
					'body' => [
						'from' => $from,
						'size' => $size,
						'sort' => [
							['schedule_date' => ['order' => 'asc']]
						],
						'query' => [
							'bool' => [
								'must' => $must,
								'filter' => [
									[
										'range' => [
											'schedule_date' => [
												'gte' => $nowMinus1Hour,
												'format' => 'strict_date_optional_time'
											]
										]
									]
								]
							]
						]
					]
				];

				$upcomingData = [];
				$upcomingTotal = 0;

				if ($this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
					$response = $this->elasticService->getClient()->search($upcomingParams);
					$upcomingTotal = $response['hits']['total']['value'] ?? 0;
					$upcomingData = array_map(fn($hit) => $hit['_source'], $response['hits']['hits']);
				}
				$this->upcomingData = $upcomingData;

			    //////////////////////////////////// last 7 days
                $endDate = Carbon::today()->endOfDay();
				$startDate = Carbon::today()->subDays(6)->startOfDay();
				$recentParams = [
					'index' => ELASTIC_INERTVIEW_TRACKER,
					'request_cache' => true,
					'body' => [
						'from' => $from,
						'size' => $size,
						'sort' => [
							['schedule_date' => ['order' => 'desc']]
						],
						'query' => [
							'bool' => [
								'must' => $must,
								'filter' => [
									[
										'range' => [
												'schedule_date' => [
													'gte' => $startDate->toDateString(),
													'lte' => $endDate->toDateString()
												]
											]
									]
								]
							]
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
											'max' => $endDate->toDateString()
										]
									]
								]
							]
					]
				];

				$recentData = [];
				$recentTotal = 0;

				if ($this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
					$response = $this->elasticService->getClient()->search($recentParams);
					$interviewBuckets = $response['aggregations']['uploads_over_time']['buckets'] ?? [];
					$recentTotal = $response['hits']['total']['value'] ?? 0;
					$recentData = array_map(fn($hit) => $hit['_source'], $response['hits']['hits']);
				}
				$this->recentData = $recentData;

				$interviewlabels = [];
				$interviewValues = [];

				for ($i = 0; $i < 7; $i++) {
					// dd($date->format('Y-m-d'));
					$date = $startDate->copy()->addDays($i);
					$formatted = $date->format('M d'); // e.g., 'Jul 16'
					$interviewlabels[] = $formatted;
					// Find matching doc_count from candidates
					$interviewBucket = collect($interviewBuckets)->firstWhere('key_as_string', $date->format('Y-m-d'));

					$interviewValues[] = $interviewBucket['doc_count'] ?? 0;
				}
				$this->interviewlabels = $interviewlabels ; 
				$this->interviewValues = $interviewValues;
				// dd($this->interviewlabels);
				$this->interviewValuesTotal = array_sum($interviewValues);



				//////////////////////////////////// last 6 months
				$startDateComm = Carbon::now();
				$startDate = $startDateComm->copy()->startOfMonth()->subMonths(5);
				$endDate   = $startDateComm;
					$recentParamsMonth = [
						'index' => ELASTIC_INERTVIEW_TRACKER,
						'request_cache' => true,
						'body' => [
							'from' => $from,
							'size' => $size,
							'sort' => [
								['schedule_date' => ['order' => 'desc']]
							],
							'query' => [
								'bool' => [
									'must' => $must,
									'filter' => [
										[
											'range' => [
													'schedule_date' => [
														'gte' => $startDate->toDateString(),
														'lte' => $endDate->toDateString()
													]
												]
										]
									]
								]
								],
								'aggs' => [
									'uploads_over_time' => [
										'date_histogram' => [
											'field' => 'schedule_date',
											'calendar_interval' => 'month',
											'format' => 'yyyy-MM-dd',
											'min_doc_count' => 0,
											'extended_bounds' => [
												'min' => $startDate->toDateString(),
												'max' => $endDate->toDateString()
											]
										]
									]
								]
						]
					];

				$recentDataMonth = [];
				$recentTotalMonth = 0;

				if ($this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
					$responseMonth = $this->elasticService->getClient()->search($recentParamsMonth);
					$interviewBucketsMonth = $responseMonth['aggregations']['uploads_over_time']['buckets'] ?? [];
					$recentTotalMonth = $responseMonth['hits']['total']['value'] ?? 0;
					$recentDataMonth = array_map(fn($hit) => $hit['_source'], $responseMonth['hits']['hits']);
				}
				$this->recentDataMonth = $recentDataMonth;

				// dd($this->recentDataMonth);

				$interviewlabelsMonth = [];
				$interviewValuesMonth = [];

				for ($i = 5; $i >= 0; $i--) {
					$startDate = $startDateComm->copy()->startOfMonth()->subMonths($i);	
					// dd($startDate->format('Y-m'),$this->recentDataMonth);			
					$formatted = $startDate->format('M Y'); // e.g., 'Jul 16'
					$interviewlabelsMonth[] = $formatted;
					$interviewBucket = collect($interviewBucketsMonth)->firstWhere('key_as_string', $startDate->format('Y-m-01'));
					// collect($interviewBucketsMonth)->firstWhere('key_as_string', $startDate->format('Y-m'));

					$interviewValuesMonth[] = $interviewBucket['doc_count'] ?? 0;
				}
				$this->interviewlabelsMonth = $interviewlabelsMonth ; 
				$this->interviewValuesMonth = $interviewValuesMonth;
				$this->interviewValuesTotalMonth = array_sum($interviewValuesMonth);
				
	           /** End Interview Section */



		$view = view('service_company.client.dviewMore', $this->data)->render();
		$responseData =  Reply::dataOnly(['status' => 'success', 'view' => $view]);

		cache()->put($cacheKeySet, $responseData, now()->addMinutes(CLIENTS_DVIEWMORE_TIME));
			return response()->json($responseData);

	}
	public function dcontactList()
	{

		abort_if(Gate::denies('view_clients'), 403);

		$parentCompanyType = GeneralMethod::parentCompanyType();
		$accountCompanyType = GeneralMethod::accountCompanyType();

		$company_id = auth()->user()->company_id;
		$department = GeneralMethod::industries();

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

		return DataTables::of($directContactList)

			->editColumn('chkbox', function ($row) {
				return '<input type="checkbox" class="table-checkbox" />';
			})
			->editColumn('Contact', function ($row) use ($avatarColors) {
				$name = trim((string) @$row->name);
				$initials = strtoupper(implode('', array_map(fn($w) => mb_substr($w, 0, 1),
					array_slice(array_filter(explode(' ', $name)), 0, 2))));
				$color = $avatarColors[(ord(strtolower(substr($name, 0, 1) ?: 'a')) ) % count($avatarColors)];
				// "Primary" marks the linked point-of-contact (the spoc tied to the relation).
				$primary = !empty($row->from_user_id)
					? '<div class="contact-primary-badge"><i class="fas fa-check"></i>Primary</div>' : '';
				return '<div class="contact-cell">
							<div class="contact-avatar-table" style="background:' . $color . '">' . $initials . '</div>
							<div class="contact-info-table">
								<div class="contact-name-table">' . ucwords($name) . '</div>' . $primary . '
							</div>
						</div>';
			})
			->editColumn('Designation', function ($row) {
				return ucwords((string) @$row->display_name);
			})
			->editColumn('Email', function ($row) {
				return '<span class="email-cell">' . @$row->email . '</span>';
			})
			->editColumn('Phone', function ($row) {
				return '<div class="phone-cell">
							<div class="iti__flag-container" data-code="' . $row->calling_code . '" data-row="' . $row->userId . '" id="phoneDisplay_' . $row->userId . '" style="display:flex;position:relative;"></div>
							<span class="phone-digits">' . @$row->mobile . '</span>
						</div>';
			})
			->editColumn('Status', function ($row) {
				$cls = ($row->status == 'active') ? 'active' : 'inactive';
				return '<span class="status-badge-table ' . $cls . '"><i class="fas fa-circle"></i>' . ucwords($row->status) . '</span>';
			})
			->editColumn('Actions', function ($row) {
				$call = $row->calling_code . ' ' . $row->mobile;
				return '<div class="action-btns">
							<a href="tel:' . trim($call) . '" class="action-btn-table" title="Call"><i class="fas fa-phone"></i></a>
							<a href="mailto:' . $row->email . '" class="action-btn-table" title="Email"><i class="fas fa-envelope"></i></a>
						</div>';
			})
			->rawColumns(['chkbox', 'Contact', 'Email', 'Phone', 'Status', 'Actions'])
			->addIndexColumn()->make(true);


	}
	public function edit($id)
	{

		abort_if(Gate::denies('edit_clients'), 403);
		$this->pageTitle = __('Edit Clients');
		$this->page_event = 'EDIT';
		$id = GeneralMethod::value_decrypt($id);

		// dd($id);

		// $this->client = Client::find($id);
		if($id){
			$params = [
				'index' => ELASTIC_CLIENT_LIST,
				'id'    => $id,
			];
			
			$response = $this->elasticService->getClient()->get($params);
			$this->client = (object) $response['_source'];
		}

		$this->client_file = DB::table('client_file')->select('*')->where('client_id', $id)->get();
		$this->countries = Country::all();
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
			->get();

		$this->client_contact_info = DB::table('client_contact_info')
			->select('*')
			->where('client_id', $id)
			->get();

		//  return view('service_company.client.edit', $this->data); 

		$this->countries = Country::all();
		$this->categories = JobCategory::all();
		$this->companyType = GeneralMethod::companyType();
		$this->department = DB::table('td_industries')
			->select('td_industries.ind_id', 'td_industries.ind_name')
			->orderBy('td_industries.ind_name', 'ASC')
			->get(); //GeneralMethod::department();
		$this->countryArr = GeneralMethod::getAllCountryList();
		$this->statelist = GeneralMethod::statelist(101);


		$statelist = DB::table('state_list')
			->select('state_list.id', 'state_list.state as name', DB::raw("101 as country_id"))
			->where('state_list.country_id', 101)
			->orderBy('state_list.state')
			->get()->toArray();
		// $this->indianStatelist       = json_decode(json_encode($statelist), true);

		$this->cities = GeneralMethod::allCities();

		$this->default_loc = GeneralMethod::allCitiesSelected(array());

		return view('service_company.client.create', $this->data);

	}
	public function update(Request $request, $id)
	{
		abort_if(Gate::denies('edit_clients'), 403);

		$company_id = $request->user()->company_id;
		$client = Client::find($id);
		// FIRST TAB-1       
		if (!empty($request->name)) {
			$client->name = $request->name;
		}
		if (!empty($request->website)) {
			$client->website = $request->website;
		}
		if (!empty($request->industry)) {
			$client->industry = $request->industry;
		}
		if (!empty($request->company_sizes)) {
			[$from_size, $to_size] = explode('-', $request->company_sizes);
			$client->company_size_from = $from_size;
			$client->company_sizes     = $to_size;
		}
		if (!empty($request->company_type)) {
			$client->company_type = $request->company_type;
		}
		if (!empty($request->founded)) {
			$client->founded = $request->founded;
		}
		// SECOND TAB-2
		if (!empty(@$request->description)) {
			$client->description = @$request->description;
		}
		if (!empty(@$request->tags)) {
			$tags = @$request->tags;
			$tags = json_decode($tags, true);
			$tags = array_column($tags, 'value');
			$tags = implode(',', $tags);
			$client->tags = $tags;
		}
		DB::beginTransaction();
		try {

		$client->save();

		$client_id = $id;

		// THIRD TAB-3
		$addressId = @$request->addressId;
		// dd($request->all());


		 
          $addIdGet = [];
		 for ($i = 0; $i <= $addressId; $i++) {
			$address = @$request->address[$i];
			$work_city = @$request->kt_tagify_work_location[$i];
			$work_city_all = json_decode($work_city, true);
			if (@$request->kt_tagify_work_location[$i] != "") {
				foreach ($work_city_all as $cityVal) {
                    $parts = explode('|', $cityVal['value']);
					$cityName = trim($parts[0]);
					$countrName = trim($parts[2]);

					$city_row = DB::table('city_list')
						->join('state_list', 'state_list.id', '=', 'city_list.state_id')
						->join('countries', 'countries.id', '=', 'state_list.country_id')
						->select('city_list.city_id', 'countries.id as country_id')
						->where('city_list.city', '!=', '')
						->where('city_list.city', '=', $cityName)
						->where('countries.country_name', '=', $countrName)
						->first();
					if ($city_row) {
							$cityID = (@$city_row) ? $city_row->city_id : '';
							$countryID = (@$city_row) ? $city_row->country_id : '';
							$stateId = GeneralMethod::stateId($cityID);
						if(DB::table('client_location')->where('city_id', '=',$cityID)->where('address', '=',$address)->where('client_id', $client_id)->exists()){
							 $client_location = DB::table('client_location')->where('city_id', '=',$cityID)->where('address', '=',$address)->where('client_id','=', $client_id)->first();
							 array_push($addIdGet,$client_location->id);
						}
					}
				}
			}
		 }
		 
		 DB::table('client_location')->whereNotIn('id', $addIdGet)->where('client_id', $client_id)->delete();

		for ($i = 0; $i <= $addressId; $i++) {

			$address = @$request->address[$i];
			$country = '';//@$request->country[$i];
			$state = '';//@$request->state[$i];
			$city = @$request->kt_tagify_work_location[$i];
			$postcode = '0';//@$request->postcode[$i];	


			$stateId = '';
			$cityID = '';
			$countryID = '';
			$city = '';
			$work_city = @$request->kt_tagify_work_location[$i];
			$work_city_all = json_decode($work_city, true);
			if (@$request->kt_tagify_work_location[$i] != "") {
				foreach ($work_city_all as $cityVal) {
					$parts = explode('|', $cityVal['value']);
					// Trim to remove any leading or trailing whitespace
					$cityName = trim($parts[0]);
					$countrName = trim($parts[2]);
					$city_row = DB::table('city_list')
						->join('state_list', 'state_list.id', '=', 'city_list.state_id')
						->join('countries', 'countries.id', '=', 'state_list.country_id')
						->select('city_list.city_id', 'countries.id as country_id')
						->where('city_list.city', '!=', '')
						->where('city_list.city', '=', $cityName)
						->where('countries.country_name', '=', $countrName)
						->first();

					// dd($city_row);

					if ($city_row) {
						$cityID = (@$city_row) ? $city_row->city_id : '';
						$countryID = (@$city_row) ? $city_row->country_id : '';
						$stateId = GeneralMethod::stateId($cityID);

						$values = array(
							'client_id' => $client_id,
							'country_id' => $countryID,
							'city' => $city,
							'city_id' => $cityID,
							'address' => $address,
							'postcode' => $postcode,
							'state' => $stateId
						);
                        if(DB::table('client_location')->where('city_id', '=',$cityID)->where('address', '=',$address)->where('client_id', $client_id)->exists()){
							 $client_location = DB::table('client_location')->where('city_id', '=',$cityID)->where('address', '=',$address)->where('client_id','=', $client_id)->first();
                             DB::table('client_location')->where('id', $client_location->id)->update($values);
						}else{
                             DB::table('client_location')->insert($values);
						}

						


					}

				}
			}

			// dd($values);
			// if(!empty($country) && !empty($address) && !empty($postcode) && !empty($state))
			// {
			// 		$city_id 			= $city = '';
			// 		// if($country == 101){
			// 			$city_id 			= $cityID; //@$request->city_id[$i];	
			// 		// }else{
			// 		// 	$city 				= @$request->city[$i];	
			// 		// }

			// 	$values 				= array(
			// 										'client_id' 	=> $client_id, 
			// 										'country_id' 	=> $countryID, 
			// 										'city' 			=> $city,
			// 										'city_id' 		=> $city_id, 
			// 										'address' 		=> $address, 
			// 										'postcode' 		=> $postcode, 
			// 										'state' 		=> $stateId
			// 									);
			// 	DB::table('client_location')->insert($values);	
			// }

		}

		// FORTH TAB-4
		DB::table('client_contact_info')->where('client_id', $client_id)->delete();
		$contactPersonId = @$request->contactPersonId;
		for ($j = 0; $j <= $contactPersonId; $j++) {
			$cp_name = @$request->cp_name[$j];
			$cp_email = @$request->cp_email[$j];
			$cp_number = @$request->cp_number[$j];
			$cp_desigination = @$request->cp_desigination[$j];
			$calling_code = @$request->calling_code[$j];

			if (!empty($cp_name) && !empty($cp_email) && !empty($cp_desigination)) {
				$values = array(
					'client_id' => $client_id,
					'contact_person_name' => $cp_name,
					'contact_person_email' => $cp_email,
					'contact_person_number' => $cp_number,
					'calling_code' => $calling_code,
					'contact_person_designation' => $cp_desigination
				);
				DB::table('client_contact_info')->insert($values);
			}

		}


		$maxsize = 5242880;
		$directoryPath = GeneralMethod::document_manage_directory($company_id);
		$timeStamp = time();

		if ($request->hasFile('logo')) {
			$name = $file_size = $file_type = $temp = $target_file = $logicalpath = $fileUrl = '';
			$file_size = @$_FILES['logo']['size'];
			$file_type = @$_FILES['logo']['type'];
			$temp = @$_FILES['logo']['tmp_name'];

			$path_parts = pathinfo($_FILES['logo']['name']);
			$extension = $path_parts['extension'];
			$filename = strtolower($path_parts['filename']);
			$altfilename = GeneralMethod::getValidUploadFileName($filename) . "-" . $timeStamp . "." . $extension;
			$dirArr = explode("/", $directoryPath);
			$target_file = $directoryPath . '/' . $altfilename;
			$allowed = array('png', 'jpeg', 'jpg', 'PNG', 'JPEG', 'JPG');
			$ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
			if (in_array($ext, $allowed)) {

				$logicalpath = substr($target_file, strpos($target_file, 'user-uploads'));
				$file = $request->file('logo');

	
						$companyNameR2 = DB::table('companies')
						->where('id',auth()->user()->company_id)
						->value('company_name');   // change column if needed
						$companyNameR2 = strtolower(trim($companyNameR2 ?? 'unknown'));
						$companyNameR2 = preg_replace('/[^a-z0-9_-]/i','_', $companyNameR2);
						$companyNameRTwo = $companyNameR2 . '_' . auth()->user()->company_id;
						
						$filename = uniqid('JERO_logo').'.'.$file->getClientOriginalExtension();

						$tempPath = $file->storeAs('Companies/'.$companyNameRTwo.'/Images', $filename,'r2'); // STORAGE/app/temp
						$fileUrl = "https://file.jero.works/Companies/{$companyNameRTwo}/Images/{$filename}";



				// $filename = time() . '.' . $file->getClientOriginalExtension();
				// $filePath = 'Images/' . $filename;

				// Storage::disk('s3')->put($filePath, file_get_contents($file));

				// $fileUrl = Storage::disk('s3')->url($filePath);

				if (move_uploaded_file($temp, $target_file)) {

					$client = Client::find($client_id);
					$client->logo = @$altfilename;
					$client->logo_physical_path = @$target_file;
					$client->logo_logical_path = @$fileUrl;
					$client->save();
				}
			}
		}

		if($client_id>0){
			GeneralMethod::clientStaffingPartner($client_id);
		}

		$cachekeys = [
			'allclients_version_user_'.$this->user->id,
			'allcustomclient_version_user_'.$this->user->id,
			'clientsviewmore_version_user_'.$this->user->id
		];
		foreach ($cachekeys as $key) {
			GeneralMethod::cacheClean($key);
		}

		DB::commit();

		return Reply::redirect(route('service_company.clients.index'), __('menu.clients') . ' ' . __('messages.updatedSuccessfully'));

		} catch (\Throwable $e) {
			DB::rollBack();
			\Log::error('clients update() transaction failed: ' . $e->getMessage(), ['exception' => $e]);
			return Reply::dataOnly(['status' => 'error', 'msgType' => 'danger', 'message' => 'Update failed. Please try again.']);
		}
	}
	public function create()
	{
		abort_if(Gate::denies('add_clients'), 403);

		$this->pageTitle = __('Add Clients');
		$this->page_event = 'ADD';

		$this->countries = Country::all();
		$this->categories = JobCategory::all();
		$this->companyType = GeneralMethod::companyType();
		$this->department = DB::table('td_industries')
			->select('td_industries.ind_id', 'td_industries.ind_name')
			->orderBy('td_industries.ind_name', 'ASC')
			->get(); //GeneralMethod::department();
		$this->countryArr = GeneralMethod::getAllCountryList();
		$this->statelist = GeneralMethod::statelist(101);

		$statelist = DB::table('state_list')
			->select('state_list.id', 'state_list.state as name', DB::raw("101 as country_id"))
			->orderBy('state_list.state')
			->get()->toArray();
		$this->statelist = json_decode(json_encode($statelist), true);
		$this->cities = GeneralMethod::allCities();

		$this->default_loc = GeneralMethod::allCitiesSelected(array());

		return view('service_company.client.create', $this->data);
	}
	public function citylist($state_id)
	{

		$Rawcitylist = DB::table('city_list')
			->select('city_list.city_id as id', 'city_list.city as name')
			->where('city_list.state_id', $state_id)
			->orderBy('city_list.city')
			->get()->toArray();
		$citylist = json_decode(json_encode($Rawcitylist), true);

		$custom = '<option>Select City</option>';
		foreach ($citylist as $sval) {
			$custom .= '<option value="' . $sval['id'] . '">' . $sval['name'] . '</option>';
		}
		return Reply::dataOnly(['status' => 'success', 'data' => $custom]);
	}
	public function store(Request $request)
	{

		abort_if(Gate::denies('add_clients'), 403);

		$company_id = '';
		$timeStamp = time();
		$names = $request->name;
		$company_id = $request->user()->company_id;
		if (trim($request->name) == '') {
			return Reply::error(__('errors.addClients'));
		}


		$client = new Client();
		$client->company_id = @$company_id;
		// FIRST TAB-1
		if (!empty(@$request->name)) {
			$client->name = @$request->name;
		}
		if (!empty(@$request->website)) {
			$client->website = @$request->website;
		}
		if (!empty(@$request->industry)) {
			$client->industry = @$request->industry;
		}
		if (!empty(@$request->company_sizes)) {
			[$from_size, $to_size] = explode('-', $request->company_sizes);
			$client->company_size_from = $from_size;
			$client->company_sizes     = $to_size;
		}
		if (!empty(@$request->company_type)) {
			$client->company_type = @$request->company_type;
		}
		if (!empty(@$request->founded)) {
			$client->founded = @$request->founded;
		}
		// SECOND TAB-2
		if (!empty(@$request->description)) {
			$client->description = @$request->description;
			//$client->detail = @$request->detail; 
		}
		if (!empty(@$request->tags)) {
			$tags = @$request->tags;
			$tags = json_decode($tags, true);
			$tags = array_column($tags, 'value');
			$tags = implode(',', $tags);
			$client->tags = $tags;
		}


		DB::beginTransaction();
		try {

		$client->save();
		$client_id = DB::getPdo()->lastInsertId();

		// THIRD TAB-3
		$Location = array();
		//if (!empty(@$request->addressId)) {
		$addressId = @$request->addressId;


		for ($i = 0; $i <= $addressId; $i++) {

			$address = @$request->address[$i];
			$country = '';//@$request->country[$i];
			$state = '';//@$request->state[$i];

			$postcode = "0";//@$request->postcode[$i];	

			$city_id = $city = '';
			// if($country == 101){
			$city_id = @$request->kt_tagify_work_location[$i];
			// }else{
			// 	$city 				= @$request->city[$i];	
			// }

			$stateId = '';
			$cityID = '';
			$countryID = '';
			$city = '';
			$work_city = @$request->kt_tagify_work_location[$i];
			$work_city_all = json_decode($work_city, true);
			if (@$request->kt_tagify_work_location[$i] != "") {
				foreach ($work_city_all as $cityVal) {
					$parts = explode('|', $cityVal['value']);
					// Trim to remove any leading or trailing whitespace
					$cityName = trim($parts[0]);
					$countrName = trim($parts[2]);
					$city_row = DB::table('city_list')
						->join('state_list', 'state_list.id', '=', 'city_list.state_id')
						->join('countries', 'countries.id', '=', 'state_list.country_id')
						->select('city_list.city_id', 'countries.id as country_id')
						->where('city_list.city', '!=', '')
						->where('city_list.city', '=', $cityName)
						->where('countries.country_name', '=', $countrName)
						->first();

					// dd($city_row);

					if ($city_row) {
						$cityID = (@$city_row) ? $city_row->city_id : '';
						$countryID = (@$city_row) ? $city_row->country_id : '';
						$stateId = GeneralMethod::stateId($cityID);

						$values = array(
							'client_id' => $client_id,
							'country_id' => $countryID,
							'city' => $city,
							'city_id' => $cityID,
							'address' => $address,
							'postcode' => $postcode,
							'state' => $stateId
						);


						DB::table('client_location')->insert($values);
						$Location[] = (object) $values;

					}

				}
			}


			// $values 			= array(
			// 								'client_id' 	=> $client_id, 
			// 								'country_id' 	=> $country, 
			// 								'city' 			=> $city,
			// 								'city_id' 		=> $city_id, 
			// 								'address' 		=> $address, 
			// 								'postcode' 		=> $postcode, 
			// 								'state' 		=> $state
			// 							);
			// DB::table('client_location')->insert($values);


		}
		//}
		// FORTH TAB-4
		$contacDetails = array();
		$contactPersonId = @$request->contactPersonId;
		for ($j = 0; $j <= $contactPersonId; $j++) {
			$cp_name = @$request->cp_name[$j];
			$cp_email = @$request->cp_email[$j];
			$cp_number = @$request->cp_number[$j];
			$cp_desigination = @$request->cp_desigination[$j];
			$calling_code = @$request->calling_code[$j];

			if (!empty($cp_email)) {
				$values = array(
					'client_id' => $client_id,
					'contact_person_name' => $cp_name,
					'contact_person_email' => $cp_email,
					'contact_person_number' => $cp_number,
					'calling_code' => $calling_code,
					'contact_person_designation' => $cp_desigination
				);
				DB::table('client_contact_info')->insert($values);
				$contacDetails[] = (object) $values;
			}
		}


		$maxsize = 5242880;
		$directoryPath = GeneralMethod::document_manage_directory($company_id);

		$comp = Company::find($company_id);
		$added_in_com_user = Company::where('companies.id', $company_id)->join('users', 'users.company_id', '=', 'companies.id')->first();

		$data = [
			"Status" => "Active | Hold | Closed",
			"ClientID" => $client_id,
			"CompanyName" => $comp->company_name,
			"ClientName" => @$request->name,
			"addedBy" => @$request->user()->name,
			"added_in_con_user" => @$added_in_com_user->name,
			"Location" => $Location,
			"contacDetails" => $contacDetails
		];

		$basecompany = GeneralMethod::baseCompany();
		$senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
		$fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
		$attachments = array();
		$cc = array();
		$subject = "New Client Added";

		QueueNotificationMailService::dispatch(
			auth()->user()->email,
			$subject,
			'email.client.add-client',
			(object) $data,
			$attachments,
			$fromAddress,
			$senderName,
			$cc,
			$company_id
		)->onConnection('emails');


		// $result = $this->notificationMailService->sendEmail(auth()->user()->email, $subject, 'email.client.add-client', (object) $data, $attachments, $fromAddress, $senderName, $cc);

		// Mail::to(auth()->user()->email)->send(new AddClient((object) $data));


		if ($request->hasFile('logo')) {



			$name = $file_size = $file_type = $temp = $target_file = $logicalpath = $fileUrl = '';
			$file_size = @$_FILES['logo']['size'];
			$file_type = @$_FILES['logo']['type'];
			$temp = @$_FILES['logo']['tmp_name'];

			$path_parts = pathinfo($_FILES['logo']['name']);
			$extension = $path_parts['extension'];
			$filename = strtolower($path_parts['filename']);
			$altfilename = GeneralMethod::getValidUploadFileName($filename) . "-" . $timeStamp . "." . $extension;
			$dirArr = explode("/", $directoryPath);
			$target_file = $directoryPath . '/' . $altfilename;
			$allowed = array('png', 'jpeg', 'jpg', 'PNG', 'JPEG', 'JPG');
			$ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
			if (in_array($ext, $allowed)) {
				$logicalpath = substr($target_file, strpos($target_file, 'user-uploads'));
				$file = $request->file('logo');

				

						$companyNameR2 = DB::table('companies')
						->where('id',auth()->user()->company_id)
						->value('company_name');   // change column if needed
						$companyNameR2 = strtolower(trim($companyNameR2 ?? 'unknown'));
						$companyNameR2 = preg_replace('/[^a-z0-9_-]/i','_', $companyNameR2);
						$companyNameRTwo = $companyNameR2 . '_' . auth()->user()->company_id;
						
						$filename = uniqid('JERO_logo').'.'.$file->getClientOriginalExtension();

						$tempPath = $file->storeAs('Companies/'.$companyNameRTwo.'/Images', $filename,'r2'); // STORAGE/app/temp
						$fileUrl = "https://file.jero.works/Companies/{$companyNameRTwo}/Images/{$filename}";

				// $filename = time() . '.' . $file->getClientOriginalExtension();
				// $filePath = 'Images/' . $filename;

				// Storage::disk('s3')->put($filePath, file_get_contents($file));

				// $fileUrl = Storage::disk('s3')->url($filePath);
				if (move_uploaded_file($temp, $target_file)) {

					$client = Client::find($client_id);
					$client->logo = @$altfilename;
					$client->logo_physical_path = @$target_file;
					$client->logo_logical_path = @$fileUrl;
					$client->save();

				}
			}


		}
		if($client_id>0){
			GeneralMethod::clientStaffingPartner($client_id);
		}

		$cachekeys = [
			'allclients_version_user_'.$this->user->id,
			'allcustomclient_version_user_'.$this->user->id,
			'clientsviewmore_version_user_'.$this->user->id
		];
		foreach ($cachekeys as $key) {
			GeneralMethod::cacheClean($key);
		}

		DB::commit();

		return Reply::redirect(route('service_company.clients.custom_client') . '/0', __('menu.clients') . ' ' . __('messages.createdSuccessfully'));

		} catch (\Throwable $e) {
			DB::rollBack();
			\Log::error('clients store() transaction failed: ' . $e->getMessage(), ['exception' => $e]);
			return Reply::dataOnly(['status' => 'error', 'msgType' => 'danger', 'message' => 'Submission failed. Please try again.']);
		}
	}
	public function addresslist($cid)
	{
		// dd("djdh");
		$this->countryArr = GeneralMethod::getAllCountryList();
		//$this->statelist 		=	GeneralMethod::statelist(101);
		$this->cid = $cid;
		$this->cities = GeneralMethod::allCities();

		$this->default_loc = GeneralMethod::allCitiesSelected(array());

		$view = view('service_company.client.addresslist', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'data' => $view]);
	}
	public function request_update(Request $request)
	{
		$company_id = auth()->user()->company_id;
		$user_id = $this->user->id;

		$invitation_id = $event_id = $from_company = '';
		$event_id = $_REQUEST['event_id'];
		$from_company = $_REQUEST['from_company'];
		$invitation_id = $_REQUEST['invitation_id'];


		if ($event_id == -1) {
			// CLIENT INVIDATION IGNORE
			$updateArr = array('to_invitation_status' => -1);
			DB::table('staffing_company_invitation')->where('id', $invitation_id)->limit(1)->update($updateArr);
			$exists = $this->elasticService->getClient()->exists([
				'index' =>ELASTIC_CLIENT_REQUEST,
				'id' => $invitation_id
			]);
		

			if ($exists) {
			
				// 2. Prepare the update timestamp
				$updateTime = now()->toIso8601String();

			 // 3. Execute update with debugging
				$updateParams = [
					'index' => ELASTIC_CLIENT_REQUEST,
					'id' =>$invitation_id,
					'body' => [
						'doc' => [
							'to_invitation_status' => -1,
							'updated_at' => $updateTime
						],
						'doc_as_upsert' => false // Changed to false since we verified existence
					],
					'refresh' => true // Force immediate refresh
				];
				$this->elasticService->getClient()->update($updateParams);
				
			}

			// CLIENT RELATION IGNORE
			$updateArr = array('final_status' => -1);
			DB::table('staffing_company_relation')
				->where('staffing_company_relation.from_company_id', $from_company)
				->where('staffing_company_relation.to_company_id', $company_id)
				->update($updateArr);
			// CLIENT JOB IGNORE
			$updateArr = array('final_status' => -1);
			DB::table('recommendation_relation')
				->where('recommendation_relation.from_company_id', $from_company)
				->where('recommendation_relation.to_company_id', $company_id)
				->update($updateArr);

			$clientDataCnt = $this->commonclient();
			
			$cachekeys = [
				'allclients_version_user_'.$this->user->id,
				'allclientrequest_version_user_'.$this->user->id,
				'alldirectclient_version_user_'.$this->user->id,
				'allconsultancy_version_user_'.$this->user->id
			];
			foreach ($cachekeys as $key) {
				GeneralMethod::cacheClean($key);
			}


			return Reply::dataOnly(['status' => 'success', 'clientDataCnt' => $clientDataCnt,'message' => 'Request has been Ignored']);
		} else if ($event_id == 1) {

			$updateArr = array('to_invitation_status' => $event_id);
			DB::table('staffing_company_invitation')->where('id', $invitation_id)->limit(1)->update($updateArr);

			$exists = $this->elasticService->getClient()->exists([
				'index' =>ELASTIC_CLIENT_REQUEST,
				'id' => $invitation_id
			]);
		

			if ($exists) {
			
				// 2. Prepare the update timestamp
				$updateTime = now()->toIso8601String();

			 // 3. Execute update with debugging
				$updateParams = [
					'index' => ELASTIC_CLIENT_REQUEST,
					'id' =>$invitation_id,
					'body' => [
						'doc' => [
							'to_invitation_status' => 1,
							'updated_at' => $updateTime
						],
						'doc_as_upsert' => false // Changed to false since we verified existence
					],
					'refresh' => true // Force immediate refresh
				];
				$this->elasticService->getClient()->update($updateParams);

				$cachekeys = [
					'allclientrequest_version_user_'.$this->user->id,
					'allclients_version_user_'.$this->user->id,
					'alldirectclient_version_user_'.$this->user->id
				];
				foreach ($cachekeys as $key) {
					GeneralMethod::cacheClean($key);
				}
				
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


			$values = array(
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
				'updated_at' => date('Y-m-d')
			);
			DB::table('staffing_company_relation')->insert($values);
			$relationId 		=	DB::getPdo()->lastInsertId();
			GeneralMethod::appendClientNestedData($relationId);

			$job = Job::find($invitationRow->job_id);
			if ($job) {

				$detailsVendorSide = GeneralMethod::companyAsVendor($from_company_id, $to_company_id);
				$basecompany = GeneralMethod::baseCompany();
				$senderName = (auth()->user()->name) ? auth()->user()->name : '';
				$fromAddress = (auth()->user()->email) ? auth()->user()->email : '';
				$attachments = array();

				$cc = [];
				$checkMailPermision = GeneralMethod::mailPermission(20);
				if ($checkMailPermision) {

					$mailTypeSubject = GeneralMethod::mailTypeSubject(20, 1);

					$subject = str_replace(["[{VENDOR_COMPANY_NAME}]", "[{JOB_TITLE}]", "[{JOB_ID}]"], [$detailsVendorSide->vendor_company, @$job->title, @$job->id], $mailTypeSubject->subject);

					$template_description = $mailTypeSubject->body_description;

					$template_description = str_replace(["[{CLIENT_COMPANY_NAME}]", "[{VENDOR_COMPANY_NAME}]", "[{VENDOR_NAME}]", "[{VENDOR_PHONE}]", "[{VENDOR_EMAIL}]", "[{TERMS}]"], [$detailsVendorSide->client_company, $detailsVendorSide->vendor_company, $detailsVendorSide->vendor_name, $detailsVendorSide->vendor_email, $detailsVendorSide->vendor_phone, ''], $template_description);

					$message = $template_description;

					$notificationemail = GeneralMethod::notification($from_company_id, 'Recuitment'); //Recuitment Sales
					$defailsEmail = DB::table('companies')->where('id', $from_company_id)->first()->company_email;
					$to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
					//    dd($to_Mail_Id);
					$data = [
						'message' => $message
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
				'allconsultancy_version_user_'.$this->user->id
			];
			foreach ($cachekeys as $key) {
				GeneralMethod::cacheClean($key);
			}

			return Reply::dataOnly(['status' => 'success', 'clientDataCnt' => $clientDataCnt,'message' => 'Request has been Accepted']);
		}



	}
	public function requestviewmore($id)
	{



		abort_if(Gate::denies('view_clients'), 403);
		$company_id = auth()->user()->company_id;

		$this->countries = Country::all();
		$this->categories = JobCategory::all();
		$this->companyType = GeneralMethod::accountCompanyType();
		$this->department = GeneralMethod::industries();
		$this->countryArr = GeneralMethod::getAllCountryList();
		$this->statelist = GeneralMethod::statelist(101);


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
			->where('clients.id', '=', $id)
			//->where('staffing_company_relation.to_company_id', '=', $company_id)
			->first();
		$this->client_file = DB::table('client_file')->select('*')->where('client_id', $id)->get();

		$this->client_location = DB::table('client_location')
			->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
			->where('client_location.client_id', $id)
			->orderBy('client_location.id', 'DESC')
			->first();

		$this->city_name = '';


		if (!empty($this->client_location->country_id) && ($this->client_location->country_id == 101)) {
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

}
