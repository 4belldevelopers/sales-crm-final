<?php
/*** ########################################################
Controller    	: 	AdminCompanyController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################** */

namespace App\Http\Controllers\ServiceCompany;

use App\Helper\Files;
use App\Helper\Reply;
use App\Helper\GeneralMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Company;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Gate;
use App\Jobs\QueueNotificationMailService;
use App\Http\Requests\Company\StoreCompany;
use App\Models\Job;
use App\Services\ElasticService;

class ServiceCompanyCompanyController extends ServiceCompanyBaseController
{

	protected $elasticService;
    public function __construct(ElasticService $elasticService)
    {
        parent::__construct();
        $this->pageTitle = 'menu.companies';
        $this->pageIcon = 'icon-film';
		$this->elasticService = $elasticService;
    }

    public function index()
    {
        abort_if(Gate::denies('view_company'), 403);

        $this->totalCompanies = Company::count();
        $this->activeCompanies = Company::where('status', 'active')->count();
        $this->inactiveCompanies = Company::where('status', 'inactive')->count();
        return view('service_company.company.index', $this->data);
    }
    
    public function create()
    	{
        abort_if(Gate::denies('add_company'), 403);
        return view('service_company.company.create', $this->data);
    }
    
    public function store(StoreCompany $request)
    	{
        abort_if(Gate::denies('add_company'), 403);

        $setting = new Company();
        $setting->company_name        = $request->input('company_name');
        $setting->company_email       = $request->input('company_email');
        $setting->company_phone       = $request->input('company_phone');
        $setting->website             = $request->input('website');
        $setting->address             = $request->input('address');
        $setting->show_in_frontend    = $request->input('show_in_frontend');
        $setting->job_opening_text    = 'Welcome!';
        $setting->job_opening_title   = 'We want people to thrive. We believe you do your best work when you feel your best.';

        if ($request->hasFile('logo')) {
            $setting->logo = Files::upload($request->logo,'company-logo');
        }

        $setting->save();

        return Reply::redirect(route('service_company.company.index'), __('menu.companies') . ' ' . __('messages.createdSuccessfully'));
    }
   
    public function show($id)
    	{
        //
    }
   
    public function edit($id)
    	{
        abort_if(Gate::denies('edit_company'), 403);

        $this->company = Company::find($id);
        return view('service_company.company.edit', $this->data);
    }
    
    public function update(StoreCompany $request, $id)
    {
        abort_if(Gate::denies('edit_company'), 403);

        $setting = Company::findOrFail($id);
        $setting->company_name        = $request->input('company_name');
        $setting->company_email       = $request->input('company_email');
        $setting->company_phone       = $request->input('company_phone');
        $setting->website             = $request->input('website');
        $setting->address             = $request->input('address');
        $setting->show_in_frontend    = $request->input('show_in_frontend');

        //update company's jobs status
        if($setting->status != $request->input('status')) {
            Job::where('company_id', $id)->update(['status' => $request->status]);
        }

        $setting->status = $request->input('status');

        if ($request->hasFile('logo')) {
            $setting->logo = Files::upload($request->logo,'company-logo');
        }

        $setting->save();

        return Reply::redirect(route('service_company.company.index'), __('menu.companies') . ' ' . __('messages.updatedSuccessfully'));
    }
    
    public function destroy($id)
    	{
        abort_if(Gate::denies('delete_company'), 403);

        Company::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }

    public function data()
    	{
        abort_if(Gate::denies('view_company'), 403);

        $categories = Company::all();

        return DataTables::of($categories)
            ->addColumn('action', function ($row) {
                $action = '';

                if ($this->user->cans('edit_company')) {
                    $action .= '<a href="' . route('service_company.company.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                      data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                }

                if ($this->user->cans('delete_company')) {
                    $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
                }
                return $action;
            })
            ->editColumn('status', function ($row) {
                if($row->status == 'active'){
                    return '<label class="badge bg-success">'.__('app.active').'</label>';
                }
                if($row->status == 'inactive'){
                    return '<label class="badge bg-danger">'.__('app.inactive').'</label>';
                }
             })
            ->editColumn('logo', function ($row) {
                return '<img src="' . $row->logo_url . '" class="img-responsive" height="25" />';
            })
            ->editColumn('company_name', function ($row) {
                return ucfirst($row->company_name);
            })
            ->addIndexColumn()
            ->rawColumns(['logo', 'action', 'status'])
            ->make(true);
    }

    public function findAgency(Request $request)
	{

		
		$id = 48;
		$this->pageTitle = __('Candidates Recommended');
		$company_id = auth()->user()->company_id;
		$this->job = Job::findOrFail($id);
		$this->custome_count = 0;
       			
		session()->put('job_id', $id);


		$gdFilterData = GeneralMethod::gdFilterData($id);

		$this->default_loc = '';
		$this->qualification ='';
		$this->min_exp = '';
		$this->max_exp = '';
		$this->min_salary_currency = '';
		$this->min_salary = '';
		$this->max_salary = '';
		$this->min_sal_mode = '';
		$this->default_skill = '';

        $cities = DB::table('countries')
        ->select('countries.country_name')
        ->where('countries.country_name','!=','')
        ->orderBy('countries.country_name')
        ->get()
        ->map(function($city) {
            return $city->country_name;
        });

		$this->cities = $cities;
		// dd($this->cities);
		$this->qualificationTag = DB::table('qualification_master')
			->select('*')
			->orderBy('qualification_master.display_order', 'ASC')
			->get()
			->map(function ($row) {
				return $row->name;
			});
            $this->td_industries = DB::table('td_industries')->select('ind_id as id','ind_name as name')->get();

            // dd($this->td_industries);

             $params = [
					'index' => ELASTIC_COMPANIES_INDEX,
					'body'  => [
						'size' => 0,
						'query' => [
							'bool' => [
								'must_not' => [
									['term' => ['id' => $company_id]],
								]
							]
						],
						'aggs' => [
							'all_companies' => [
								'value_count' => [
									'field' => 'id'
								]
							],
							'active_companies' => [
								'filter' => [
									'term' => [
										'status' => 'active'
									]
								]
							]
						]
					]
				];

				$response = $this->elasticService->getClient()->search($params);
				$allCompanies = $response['aggregations']['all_companies']['value'] ?? 0;
				$activeCompanies       = $response['aggregations']['active_companies']['doc_count'] ?? 0;
                
                $this->allCompanies= $allCompanies;
                $this->activeCompanies = $activeCompanies;

		
		return view('service_company.common.find-agency', $this->data);
	}

	public function findAgencyData(Request $request)
	{

		$company_id = auth()->user()->company_id;
          
        $searchTerms = request('cus_search');
        // dd($searchTerms);
        $custom_search = json_decode($searchTerms, true);
        // dd($custom_search);

        $searchTermsLocation = request('kt_tagify_location');
        // dd($searchTerms);
        $custom_search_location = json_decode($searchTermsLocation, true);

        if (\request('app_int_outsource') != 0) {
             $parent_employment_type = request('app_int_outsource');
        }else{
            $parent_employment_type = 'all';
        }
		$parent_employment_type = request('app_int_outsource');
		$commtype = request('app_int_outsource');

		// dd($parent_employment_type);

        $currentJobSkill = ['skill'];
    
    	if (is_array($custom_search) && !empty($searchTerms)) {
    		 $currentJobSkill = array_column($custom_search, 'value');
    		//  dd($currentJobSkill);
    	}
		
       $perPage = 10;
       $page = $request->get('page', 1);
       $skillsList = array_filter($currentJobSkill, fn($s) => $s !== 'skill');
	   $skillsList = array_map('strtolower', $skillsList);
		// dd($skillsList);
		$searchQuery = $request->input('q');
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;

		$cachePayload = [
			'page' => $page,
			'size' => $size,
			'q' => $searchQuery,
			'company' => $company_id,
			'kt_tagify_location' => $searchTermsLocation,
			'app_int_outsource' => $parent_employment_type,
			'user_id' => $this->user->id,
			'skillsList' => $skillsList ?? []
		];
		// dd($cachePayload);

		$versionKey = 'allfindagencyversion_user_'.$this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:allfindagency:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
		/* ----------------------------
		⚡ Check DragonflyDB first
		----------------------------- */
		if (cache()->has($cacheKeySet)) {
			// return response()->json(cache()->get($cacheKeySet));
		}

		// dd( $searchTermsLocation);


        // dd($currentJobSkill);
        if (is_array($custom_search_location) && !empty($searchTermsLocation)) {
    		$country_id_job = 101;
    		$currentJobLocation = array_column($custom_search_location, 'value');
    		$job_country_id = DB::table('client_city_jobs')
    					->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
    					->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
    					->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
    					->select('countries.id')
    					->where('countries.country_name','=', $currentJobLocation[0])
    					->where('client_city_jobs.job_id','=', $request->jobid)
    					->first();
    		if($job_country_id){
    			$country_id_job = $job_country_id->id;
    		}else{
    			$job_country_id = DB::table('countries')
    					->select('countries.id')
    					->where('countries.country_name','=', $currentJobLocation[0])
    					->first();
    			$country_id_job = $job_country_id->id;
    		}			
    		   //    dd($country_id_job);
       }else{
    	$country_id_job = 101;
    	$job_country_id = DB::table('client_city_jobs')
    					->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
    					->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
    					->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
    					->select('countries.id')
    					->where('countries.country_name','=', $searchTermsLocation)
    					->first();
    		if($job_country_id){
    			$country_id_job = $job_country_id->id;
    		}else{
    			if(!empty($searchTermsLocation)){
    			$job_country_id = DB::table('countries')
    					->select('countries.id')
    					->where('countries.country_name','=', $searchTermsLocation)
    					->first();
    			$country_id_job = @$job_country_id->id;
    		}
    		}		
       }
       $vendorIds = DB::table('companies')
    		->where('company_type', '!=', 3)
    		->where('package_id', '>', 0)
    		->where('id', '!=', $company_id)
    		->pluck('id');
    		
     // 		dd( $vendorIds);
    // dd($skillsList);
    // $skillsList = implode(",",$currentJobSkill);
    $forceRefresh = $request->get('force_refresh', false);
    
    
    $cacheKey = "final_vendors_{$company_id}_".md5(json_encode($skillsList))."_{$parent_employment_type}_{$country_id_job}_{$request->get('page', 1)}";

	// 1. Try to get from cache
	 if (!$forceRefresh && Cache::has($cacheKey)) {
        return response()->json(Cache::get($cacheKey));
    }
	// dd("fdl");
    
    
    // 1. Cached vendor IDs
    $vendorIds = Cache::remember("vendor_ids_for_company_{$company_id}", 300, function () use ($company_id) {
        return DB::table('companies')
            ->where('company_type', '!=', 3)
            ->where('package_id', '>', 0)
            ->where('id', '!=', $company_id)
            ->pluck('id');
    });
    // 2. Precompute: SimilarReq jobs count
    $similarReqSub = DB::table('jobs as j')
        ->select(DB::raw('COUNT(DISTINCT j.id) as SimilarReq'), DB::raw('COALESCE(j.company_id, rr.to_company_id) as vendor_id'))
        ->leftJoin('job_metronics_skills as jms', 'jms.job_id', '=', 'j.id')
        ->leftJoin('recommendation_relation as rr', 'rr.job_id', '=', 'j.id')
        ->where('j.status', 'active');
        if(count($skillsList)>0){
			$similarReqSub = $similarReqSub->whereIn('jms.skill_name', $skillsList);
		}
        $similarReqSub = $similarReqSub->when($parent_employment_type === '1' || $parent_employment_type === '0', function ($q) use ($parent_employment_type) {
            $q->where('j.employment_type', $parent_employment_type);
        })
        ->groupBy('vendor_id');
    
    // 3. Precompute: Bench profiles count
    $benchProfilesSub = DB::table('job_applications')
        ->select(DB::raw('COUNT(DISTINCT job_applications.id) as benchProfilesCount'), 'job_applications.company_id as vendor_id')
        ->join('job_application_skills', 'job_application_skills.job_application_id', '=', 'job_applications.id')
        ->where('job_applications.status', 2)
        ->where('job_applications.condidate_type', 'bench_condidate');
		if(count($skillsList)>0){
			$benchProfilesSub = $benchProfilesSub->whereIn('job_application_skills.skills', $skillsList);
		}
        $benchProfilesSub = $benchProfilesSub->groupBy('job_applications.company_id');
    
    // 4. Precompute: Other profiles count
    $otherProfilesSub = DB::table('job_applications')
        ->select(DB::raw('COUNT(DISTINCT job_applications.id) as otherProfilesCount'), DB::raw('COALESCE(add_job_wise_condidates.company_id, add_job_wise_condidates.thirdparty_company_id) as vendor_id'))
        ->join('job_application_skills', 'job_application_skills.job_application_id', '=', 'job_applications.id')
        ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', '=', 'job_applications.id')
        ->where('job_applications.condidate_type', 'other');
		if(count($skillsList)>0){
          $otherProfilesSub = $otherProfilesSub->whereIn('job_application_skills.skills', $skillsList);
		}
        $otherProfilesSub = $otherProfilesSub->where(function ($q) {
            $q->whereNotNull('add_job_wise_condidates.company_id')
              ->orWhere(function ($q2) {
                  $q2->whereNotNull('add_job_wise_condidates.thirdparty_company_id')
                      ->where('add_job_wise_condidates.master_status_id', '!=', 8);
              });
        })
        ->groupBy('vendor_id');

		if(empty($country_id_job)){
			$country_id_job = 101;
		}

		
		// ['id' => ['order' => 'desc']]


		
		/*
        |--------------------------------------------------------------------------
        | Commercial Filter
        |--------------------------------------------------------------------------
        |
        | NOTE:
        | commericials is NOT nested.
        | So we use script query for exact same-object matching.
        |
        */

        $page = request()->get('page', 1);
        $size = request()->get('size', 10);
        $from = ($page - 1) * $size;

        $companyId = request()->get('company_id');
        $searchTerms = request('cus_search');
        // dd($searchTerms);
        $custom_search = json_decode($searchTerms, true);
        $currentJobSkill = ['skill'];

        if (is_array($custom_search) && !empty($searchTerms)) {
                $currentJobSkill = array_column($custom_search, 'value');
            //  dd($currentJobSkill);
        }



        /*
        |--------------------------------------------------------------------------
        | SEARCH SKILLS
        |--------------------------------------------------------------------------
        */

        $searchSkills = array_filter($currentJobSkill, fn($s) => $s !== 'skill');
        $searchSkills = array_map('strtolower', $skillsList);
        $globalSearch = trim(request('search'));

        /*
        |--------------------------------------------------------------------------
        | CLEAN SKILLS
        |--------------------------------------------------------------------------
        */

        $skills = array_map(function ($skill) {
            return strtolower(trim($skill));
        }, $searchSkills);
        $skills = array_values(array_unique(array_filter($skills)));

        /*
        |--------------------------------------------------------------------------
        | STEP 1
        |--------------------------------------------------------------------------
        | Find other companies having same skills
        |--------------------------------------------------------------------------
        */

        $companySkillMatchCount = [];
        $companyMatchedSkills = [];

        if (!empty($skills)) {

            /*
            |--------------------------------------------------------------------------
            | SKILL MATCH QUERY
            |--------------------------------------------------------------------------
            */

            $skillQueries = [];

            foreach ($skills as $skill) {

                $skill = strtolower(trim($skill));

                if (!empty($skill)) {

                    $skillQueries[] = [

                        'wildcard' => [

                            'skill_name' => [
                                'value' => '*' . $skill . '*'
                            ]
                        ]
                    ];
                }
            }

            /*
            |--------------------------------------------------------------------------
            | ELASTIC SEARCH
            |--------------------------------------------------------------------------
            */

            $skillSearchParams = [

                'index' => ELASTIC_COMPANY_SKILLS,

                'body'  => [

                    'size' => 10000,

                    '_source' => [
                        'company_id',
                        'skill_name'
                    ],

                    'query' => [

                        'bool' => [

                            'must' => [

                                [
                                    'bool' => [

                                        'should' => $skillQueries,

                                        'minimum_should_match' => 1
                                    ]
                                ]
                            ],

                            'must_not' => [

                                [
                                    'term' => [
                                        'company_id' => $company_id
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            $skillResponse = $this->elasticService->getClient()->search($skillSearchParams);

            $hits = $skillResponse['hits']['hits'] ?? [];

            // dd($hits);
            $SkillArrCheck = [];

            foreach ($hits as $hit) {

                $source = $hit['_source'];

                $matchedCompanyId = $source['company_id'];

                $skillName = strtolower(trim($source['skill_name']));
                if (!in_array($skillName, $SkillArrCheck)) {
                          $SkillArrCheck[] = $skillName;

                /*
                |--------------------------------------------------------------------------
                | MATCH COUNT
                |--------------------------------------------------------------------------
                */

                if (!isset($companySkillMatchCount[$matchedCompanyId])) {

                    $companySkillMatchCount[$matchedCompanyId] = 0;
                }

                $companySkillMatchCount[$matchedCompanyId]++;

                /*
                |--------------------------------------------------------------------------
                | STORE MATCHED SKILLS
                |--------------------------------------------------------------------------
                */

                $companyMatchedSkills[$matchedCompanyId][] = $skillName;
                
                }


            }

            /*
            |--------------------------------------------------------------------------
            | REMOVE DUPLICATE SKILLS
            |--------------------------------------------------------------------------
            */

            foreach ($companyMatchedSkills as $companyId => $skillList) {

                $companyMatchedSkills[$companyId] = array_values(array_unique($skillList));
            }
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 2
        |--------------------------------------------------------------------------
        | Dynamic score boost
        |--------------------------------------------------------------------------
        */

        // dd($companySkillMatchCount);

        $functions = [];

        foreach ($companySkillMatchCount as $matchedCompanyId => $matchedSkillCount) {
            //   dd($matchedCompanyId);
            $functions[] = [

                'filter' => [

                    'term' => [
                        'id' => $matchedCompanyId
                    ]
                ],

                'weight' => ($matchedSkillCount * 100)
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 3
        |--------------------------------------------------------------------------
        | Fetch companies
        |--------------------------------------------------------------------------
        */

        $experience = request('experience');
        $industry = request('industry');

        $experienceFilter = [];
        $industryFilter = [];

        $currentYear = date('Y');

        if (!empty($experience)) {

            /*
            |--------------------------------------------------------------------------
            | Calculate experience from founded year
            |--------------------------------------------------------------------------
            */

            if ($experience == '0–2 years') {

                $minFoundedYear = $currentYear - 2;

                $experienceFilter[] = [
                    'range' => [
                        'founded' => [
                            'gte' => $minFoundedYear
                        ]
                    ]
                ];
            }

            elseif ($experience == '3–5 years') {

                $maxFoundedYear = $currentYear - 3;
                $minFoundedYear = $currentYear - 5;

                $experienceFilter[] = [
                    'range' => [
                        'founded' => [
                            'gte' => $minFoundedYear,
                            'lte' => $maxFoundedYear
                        ]
                    ]
                ];
            }

            elseif ($experience == '5–10 years') {

                $maxFoundedYear = $currentYear - 5;
                $minFoundedYear = $currentYear - 10;

                $experienceFilter[] = [
                    'range' => [
                        'founded' => [
                            'gte' => $minFoundedYear,
                            'lte' => $maxFoundedYear
                        ]
                    ]
                ];
            }

            elseif ($experience == '10+ years') {

                $maxFoundedYear = $currentYear - 10;

                $experienceFilter[] = [
                    'range' => [
                        'founded' => [
                            'lte' => $maxFoundedYear
                        ]
                    ]
                ];
            }
        }
        if (!empty($industry)) {

                $industryFilter[] = [

                    'terms' => [
                        'industry.ind_id' => (array) $industry
                    ]
                ];
        }
        
        $commtype = request('app_int_outsource');
        $commercialFilter = [];

        if (!empty($commtype)) {
            $commercialFilter[] = [

                'nested' => [

                    'path' => 'commercial_nested',

                    'query' => [

                        'bool' => [

                            'must' => [

                                [
                                    'term' => [
                                        'commercial_nested.commtype' => (int)$commtype
                                    ]
                                ],

                                [
                                    'term' => [
                                        'commercial_nested.status' => 1
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }


        $globalSearchFilter = [];

        if (!empty($globalSearch)) {

            // $globalSearchFilter[] = [

            //     'bool' => [

            //         'should' => [
            //             /*
            //             |--------------------------------------------------------------------------
            //             | COMPANY NAME
            //             |--------------------------------------------------------------------------
            //             */
            //             ['wildcard' => ['company_name.keyword' => ['value' => '*' . strtolower($globalSearch) . '*','case_insensitive' => true]]],
            //             ['match' => [ 'company_name' => ['query' => $globalSearch, 'operator' => 'and']]],
            //             /*
            //             |--------------------------------------------------------------------------
            //             | CITY
            //             |--------------------------------------------------------------------------
            //             */
            //             ['wildcard' => ['locations_address.locations_address.city_name.keyword' => ['value' => '*' . strtolower($globalSearch) . '*','case_insensitive' => true]]],
            //             /*
            //             |--------------------------------------------------------------------------
            //             | STATE
            //             |--------------------------------------------------------------------------
            //             */
            //             ['wildcard' => ['locations_address.locations_address.state_name.keyword' => ['value' => '*' . strtolower($globalSearch) . '*','case_insensitive' => true]]],
            //             /*
            //             |--------------------------------------------------------------------------
            //             | COUNTRY
            //             |--------------------------------------------------------------------------
            //             */
            //             ['wildcard' => ['locations_address.locations_address.country_name.keyword' => ['value' => '*' . strtolower($globalSearch) . '*', 'case_insensitive' => true]]]

            //         ],

            //         'minimum_should_match' => 1
            //     ]
            // ];
        }

        $companySearchParams = [

            'index' => ELASTIC_COMPANIES_INDEX,

            'body'  => [

                'from' => $from,
                'size' => $size,

                'track_total_hits' => true,

                'query' => [

                    'function_score' => [

                        'query' => [

                            'bool' => [
                                'must' => array_merge(

                                    [

                                        /*
                                        |--------------------------------------------------------------------------
                                        | ACTIVE COMPANY
                                        |--------------------------------------------------------------------------
                                        */

                                        [
                                            'term' => [
                                                'status' => 'active'
                                            ]
                                        ],
                                        [
                                            'terms' => [
                                                'company_type' => [1, 2]
                                            ]
                                        ],

                                        [
                                            'term' => [
                                                'status' => 'active'
                                            ]
                                        ],

                                        [
                                            'term' => [
                                                'commericial_countries.country_id' => $country_id_job
                                            ]
                                        ],

                                        /*
                                        |--------------------------------------------------------------------------
                                        | COMPANY COINS
                                        |--------------------------------------------------------------------------
                                        */

                                        [
                                            'nested' => [

                                                'path' => 'company_coins',

                                                'query' => [

                                                    'bool' => [

                                                        'must' => [

                                                            [
                                                                'term' => [
                                                                    'company_coins.event_type' => 'sales'
                                                                ]
                                                            ],

                                                            [
                                                                'range' => [
                                                                    'company_coins.coins' => [
                                                                        'gt' => 0
                                                                    ]
                                                                ]
                                                            ]

                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]

                                    ],

                                    $commercialFilter,                                    
                                    $experienceFilter,
                                    $industryFilter,
                                    $globalSearchFilter
                                ),

                                // EXCLUDE CURRENT COMPANY
                                'must_not' => [

                                    [
                                        'term' => [
                                            'id' => $company_id
                                        ]
                                    ]
                                ]
                            ]
                        ],

                        'functions' => $functions,

                        'score_mode' => 'sum',

                        'boost_mode' => 'sum'
                    ]
                ],

                'sort' => [

                    [
                        '_score' => [
                            'order' => 'desc'
                        ]
                    ],

                ]
            ]
        ];

        /*
        |--------------------------------------------------------------------------
        | EXECUTE SEARCH
        |--------------------------------------------------------------------------
        */

        $companyResponse = $this->elasticService->getClient()->search($companySearchParams);

        /*
        |--------------------------------------------------------------------------
        | RESPONSE DATA
        |--------------------------------------------------------------------------
        */

        $total = $companyResponse['hits']['total']['value'] ?? 0;

        $data = [];
        
        foreach ($companyResponse['hits']['hits'] as $hit) {

            $source = $hit['_source'];



            // $matchedSkills = $companyMatchedSkills[$matchedCompanyId] ?? [];

            // $finalMatchedSkills = [];

            // foreach ($searchSkills as $searchSkill) {

            //     $searchSkill = strtolower(trim($searchSkill));

            //     foreach ($matchedSkills as $matchedSkill) {

            //         $matchedSkill = strtolower(trim($matchedSkill));

            //         /*
            //         |--------------------------------------------------------------------------
            //         | PARTIAL MATCH
            //         |--------------------------------------------------------------------------
            //         */

            //         if (str_contains($matchedSkill, $searchSkill)) {

            //             // STORE SEARCH SKILL
            //             $finalMatchedSkills[] = $searchSkill;

            //             break;
            //         }
            //     }
            // }

            // $finalMatchedSkills = array_values(array_unique($finalMatchedSkills));

            // $source['matched_skills'] = $finalMatchedSkills;

            // $source['search_skills'] = $searchSkills;

            $matchedCompanyId = $source['id'];

            $matchedSkills = $companyMatchedSkills[$matchedCompanyId] ?? [];
            $commonSkills = array_values(array_intersect(

                array_map('strtolower', $searchSkills),
                array_map('strtolower', $matchedSkills)

            ));

            $source['matched_skills'] = $commonSkills;
            // $source['matched_skills'] = $companyMatchedSkills[$matchedCompanyId] ?? [];
            $source['search_skills'] = $searchSkills;//$companySkillMatchCount[$matchedCompanyId] ?? 0;

            $source['match_score'] = $hit['_score'];
            $source['matched_skill_count'] = $hit['_score'];

            $data[] = $source;
        }
        // dd($data);

		$pagination = [
			'total' => $total,
			'current_page' => $page,
			'last_page' =>ceil($total / $size),
			'listType' => 'allcandidate',
		];

		$page_view = 'find_agency_ajax';

		$userPermissions = $this->userPermissions;
		$app_int_outsource = request('app_int_outsource');
        $hiringtype = $app_int_outsource ;
        $jobid = '';
		
		$responseData = [
			'html' => view('service_company.ajax_pages.'.$page_view.'', compact('data','userPermissions','app_int_outsource', 'jobid', 'currentJobSkill', 'hiringtype', 'parent_employment_type', 'company_id'))->render(),
			'total' => $total,
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		];
		
		cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_FIND_AGENCY_TIME));

	    return response()->json($responseData);

	}

	public function findAgencyRequest(Request $request,$id)
	{

		$from_company_id = auth()->user()->company_id;
		$from_user_id = $this->user->id;
		$to_company_id = $id;


		 $jobidGet = $_REQUEST['current_jobid'];
		 $event_id = $_REQUEST['event_id'];
		 $company_row = DB::table('companies')->select('companies.company_email','companies.id')->where('companies.id', '=', $to_company_id)->first();

		 if ($event_id == -1) {
			// CLIENT INVIDATION IGNORE
			DB::table('staffing_company_invitation')->where('from_company_id', $from_company_id)->where('to_company_id', $to_company_id)->where('to_status','=', 0)->where('to_invitation_status','=', 0)->delete();

			return Reply::dataOnly(['status' => 'success', 'clientDataCnt' => '']);
		}else{
		   $boostId = '';
			if(@$request->boostercom==1){
				$boostIdArr = explode(',',$request->boostids);
				$boostId = $boostIdArr[0];
				$boostkeywords = DB::table('boostkeywords')->where('id',$boostId)->first();
				$clickCount = $boostkeywords->clickCount+1;
				$boostkeywords = DB::table('boostkeywords')->where('id',$boostId)->update(['clickCount'=>$clickCount]);
			}
			
			
			$clientRow = DB::table('clients')->select('clients.id')->where('clients.company_id', '=', $from_company_id)->first();
			
			//###################################################         START CLIENT INVITATION

			// CHECK CLIENT INVITATION SENT OR NOT 
			$clientInvitationCounter = DB::table('staffing_company_invitation')->select('*')
				->where('from_company_id', $from_company_id)
				->where('to_company_id', $to_company_id)
				->where('to_invitation_status', '!=', -1)
				->count();
				// dd($clientInvitationCounter);
			if ($clientInvitationCounter == 0)		// INVITATION SENT :- IN CASE NO INVITATION AVAIL
			{
				// GET COMPANY DETAILS
				
				// dd($company_row);
				// INVITATION 
				// staffing_company_invitation
				$insertArr = array(
					'from_company_id' => $from_company_id,
					'from_client_id' => $clientRow->id,
					'from_user_id' => $from_user_id,
					'from_status' => 1,
					'from_created_at' => date('Y-m-d'),

					'to_company_id' => $to_company_id,
					'to_email' => $company_row->company_email,
					'to_status' => 0,
					'to_invitation_status' => 0
				);
				// dd($insertArr);

				DB::table('staffing_company_invitation')->insert($insertArr);
				$invitationId 		=	DB::getPdo()->lastInsertId();
				GeneralMethod::clientRequestIndexElastic($invitationId);

				$Companies = Company::find(auth()->user()->company_id);
											
					$encode				=	base64_encode($invitationId);
					$reqdate = date('d M Y');
					$Url 				=	url('/invitation-company-register/'.$encode);				
					$to 				=	$company_row->company_email;
					$cc 				=	"abu@4bell.com";
					$subject 			=	"Invitation to Become a Staffing Partner | $Companies->company_name | $reqdate ";
					$message 			= '<html><head><title>HTML email</title></head><body><p>Hi</p><table><tr><th><a href="'.$Url.'" target="_blank">Click here </a></th></tr><tr><th>Or Copy link</th></tr><tr><th>'.$Url.'</th></tr></table></body></html>';
				
					$attachments = array();
					$cc = array();
					$basecompany = GeneralMethod::baseCompany();
					$senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
					$fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
					$checkMailPermision = GeneralMethod::mailPermission(40);
					if ($checkMailPermision) {
						$mailTypeSubject = GeneralMethod::mailTypeSubject(40, 1);
						$subject = str_replace(["[COMPANY_NAME]", "[REQUEST_DATE]"], [$Companies->company_name, $reqdate], $mailTypeSubject->subject);
						$template_description = $mailTypeSubject->body_description;

						$AcceptInvite ='<a href="'.$Url.'" rel="noopener"
																style="text-decoration:none;display:inline-block;text-align:center;padding:0.75575rem 1.3rem;font-size:0.925rem;line-height:1.5;border-radius:0.35rem;color:#ffffff;background-color:#009EF7;border:0px;margin-right:0.75rem!important;font-weight:600!important;outline:none!important;vertical-align:middle">Accept
																Invite
															</a>';
						$urlLink = '<a href="'.$Url.'" rel="noopener" style="text-decoration:none;color: #009EF7">'.$Url.'</a>';
															

						$template_description = str_replace(["[COMPANY_NAME]", "[ACCEPT_INVITE]", "[URL_LINK]"], [$Companies->company_name, $AcceptInvite, $urlLink], $template_description);

						$message = $template_description;
						$data = [
							'message' => $message
						];


					QueueNotificationMailService::dispatch(
						$company_row->company_email,
						$subject,
						'email.common.index',
						(object) $data,
						$attachments,
						$fromAddress,
						$senderName,
						$cc,
						$company_row->id,
					)->onConnection('emails');
					}

			}else{
				$clientInvitationCounter = DB::table('staffing_company_invitation')->select('*')
				->where('from_company_id', $from_company_id)
				->where('to_company_id', $to_company_id)
				->where('to_invitation_status', '=', 0)
				->first();
				if($clientInvitationCounter){
				   GeneralMethod::clientRequestIndexElastic($clientInvitationCounter->id);
				}
			}
	    }
		
          $cachekeys = [
				'allfindagencyversion_user_'.$this->user->id
			];
			foreach ($cachekeys as $key) {
				GeneralMethod::cacheClean($key);
			}

		//################################################             END CLIENT INVITATION 



		return Reply::dataOnly(['status' => 'success']);

	}


}
