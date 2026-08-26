<?php

/***########################################################
Controller    	: 	AdminProfileController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/

namespace App\Http\Controllers\ServiceCompany;

use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\UpdateProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\JobCategory;
use App\Helper\GeneralMethod;
use Illuminate\Support\Carbon;
use App\Services\ElasticService;
use App\Services\ServiceCompanyProfileService;
use App\Helper\Google;


class ServiceCompanyProfileController extends ServiceCompanyBaseController
{
	protected $elasticService;
	protected $profileService;

	public function __construct(ElasticService $elasticService, ServiceCompanyProfileService $profileService)
	{
		parent::__construct();
		$this->pageTitle 	= 'menu.myProfile';
		$this->pageIcon 	= 'ti-user';
		$this->elasticService = $elasticService;
		$this->profileService = $profileService;
	}

	public function index()
	{
		$this->countryName	= '';
		$company_id			= $this->user->company_id;
		$this->user_id 		= $this->user->id;
		$loggedInUserId 	= $this->user_id;

		$rolesCustome 		= $this->user->getAllPermissions();
		$this->admin		= isset($rolesCustome[0]) ? $rolesCustome[0]->name : '';

		// 1. User profile details (Redis cached)
		$this->users = $this->profileService->getUserDetails($loggedInUserId);

		if ($this->users && $this->users->country > 0) {
			$countryArr = GeneralMethod::getCountryName($this->users->country);
			foreach ($countryArr as $key => $val) {
				$this->countryName = $val['name'];
			}
		}

		$this->countries = $this->profileService->getCountriesList();
		$this->calling_codes = $this->getCallingCodes();

		$this->city_name = $this->users->city_name ?? '';
		$this->state_name = $this->users->state_name ?? '';
		$this->country_name = $this->countryName;

		// 2. KPI Metrics & Pipeline status counts (Redis cached)
		$kpis = $this->profileService->getKpiMetrics($loggedInUserId, $company_id);
		$this->joined_count = $kpis['joined_count'];
		$this->sourcing_count = $kpis['sourcing_count'];
		$this->screening_count = $kpis['screening_count'];
		$this->evaluation_count = $kpis['evaluation_count'];
		$this->negotiation_count = $kpis['negotiation_count'];
		$this->offer_count = $kpis['offer_count'];
		$this->Rejected_count = $kpis['Rejected_count'];
		$this->inreview_count = $kpis['inreview_count'];
		$this->total_candidates = $kpis['total_candidates'];
		$this->counts = $kpis['counts'];
		$this->scpCandidatesTotal = $kpis['scpCandidatesTotal'];
		$this->scpJoiningsTotal = $kpis['scpJoiningsTotal'];
		$this->scpInterviewsTotal = $kpis['scpInterviewsTotal'];
		$this->scpOffersTotal = $kpis['scpOffersTotal'];

		// 3. Monthly ranks (Redis cached - eliminates 20-month loop & live 10k sorts)
		$this->rankings = $this->profileService->getMonthlyRanks($loggedInUserId, $company_id);

		// 4. Leaderboard (Redis cached)
		$this->leaderboard = $this->profileService->getLeaderboard($company_id, $loggedInUserId);

		// 5. Performance Graphs (Redis cached)
		$graphs = $this->profileService->getGraphData($loggedInUserId, $company_id);
		$this->labels = $graphs['labels'];
		$this->candidateValues = $graphs['candidateValues'];
		$this->candidateValuesTotal = $graphs['candidateValuesTotal'];
		$this->labelsCommoffer = $graphs['labelsCommoffer'];
		$this->candidateValuesCommoffer = $graphs['candidateValuesCommoffer'];
		$this->candidateValuesTotaloffer = $graphs['candidateValuesTotaloffer'];

		// Default interview chart placeholders
		$this->interviewlabelsMonth = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
		$this->interviewValuesMonth = [0, 0, 0, 0, 0, 0];
		$this->interviewValuesTotalMonth = 0;
		$this->interviewlabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
		$this->interviewValues = [0, 0, 0, 0, 0, 0, 0];
		$this->interviewValuesTotal = 0;

		// 6. List items (Joined candidates & upcoming interviews) (Redis cached)
		$listItems = $this->profileService->getListItemsData($loggedInUserId, $company_id);
		$this->data_list = $listItems['data_list'];
		$this->total_candidates_join = $listItems['total_candidates_join'];
		$this->upcomingData = $listItems['upcomingData'];
		$this->labelsCommjoin = $listItems['labelsCommjoin'] ?? [];
		$this->candidateValuesCommjoin = $listItems['candidateValuesCommjoin'] ?? [];
		$this->candidateValuesTotaljoin = $listItems['candidateValuesTotaljoin'] ?? 0;

		$this->startOfMonth = Carbon::now()->startOfMonth()->subMonths(3);
		$this->endOfMonth = Carbon::now()->endOfMonth();

		return view('service_company.profile.index', $this->data);
	}

	private function getRankData($company_id, $loggedInUserId, $startDate, $endDate, $isInternal = true)
	{
		// Get active users

		// dd($company_id, $loggedInUserId, $startDate, $endDate, $isInternal);
		$userQuery = [
			'index' => ELASTIC_USERS_LIST,
			'body' => [
				'query' => [
					'bool' => [
						'filter' => array_values(array_filter([
							$isInternal ? ['term' => ['company_id' => $company_id]] : null,
							// ['term' => ['status' => 'active']],
						])),
					],
				],
				'_source' => ['id', 'name', 'email', 'designation','status'],
				'size' => 10000,
				'sort'    => [
					['id' => ['order' => 'asc']],
				],
			],
		];

		$userResponse = $this->elasticService->getClient()->search($userQuery);
		$users = collect($userResponse['hits']['hits'])->pluck('_source');

		

		// Get ranking stats
		$rankQuery = [
			'index' => ELASTIC_TEAM_RANK,
			'body' => [
				'query' => [
					'bool' => [
						'filter' => array_values(array_filter([
							$isInternal ? ['term' => ['doc.company_id' => $company_id]] : null,
							['range' => [
								'doc.created_at' => [
									'gte' => $startDate,
									'lte' => $endDate,
								]
							]]
						])),
					],
				],
				'aggs' => [
					'user_scores' => [
						'terms' => [
							'field' => 'doc.user_id',
							'size' => 10000
						],
						'aggs' => [
							'profileUpload_sum' => ['sum' => ['field' => 'doc.profileUpload']],
							'interviewScheduled_sum' => ['sum' => ['field' => 'doc.interviewScheduled']],
							'interviewNoShow_sum' => ['sum' => ['field' => 'doc.interviewNoShow']],
							'offerReleased_sum' => ['sum' => ['field' => 'doc.offerReleased']],
							'candidateJoined_sum' => ['sum' => ['field' => 'doc.candidateJoined']],
							'interestedCandidates_sum' => ['sum' => ['field' => 'doc.interestedCandidates']],
							'notInterestedCandidates_sum' => ['sum' => ['field' => 'doc.notInterestedCandidates']],
							'teamCaptainInterviewScheduled_sum' => ['sum' => ['field' => 'doc.teamCaptainInterviewScheduled']],
							'teamCaptainInterviewNoShow_sum' => ['sum' => ['field' => 'doc.teamCaptainInterviewNoShow']],
							'teamCaptainOfferReleased_sum' => ['sum' => ['field' => 'doc.teamCaptainOfferReleased']],
							'teamCaptainCandidateJoined_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoined']],
							'attachments_sum' => ['sum' => ['field' => 'doc.attachmentCount']],
							'sourcing_sum' => ['sum' => ['field' => 'doc.sourcing']],
							'screening_sum' => ['sum' => ['field' => 'doc.screening']],
							'evaluation_sum' => ['sum' => ['field' => 'doc.evaluation']],
							'negotiation_sum' => ['sum' => ['field' => 'doc.negotiation']],
							'backout_sum' => ['sum' => ['field' => 'doc.backout']],
							'backoutTeam_sum' => ['sum' => ['field' => 'doc.backoutTeam']],
							'candidateJoinedContract_sum' => ['sum' => ['field' => 'doc.candidateJoinedContract']],
							'teamCaptainCandidateJoinedContract_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoinedContract']],
							'backoutContract_sum' => ['sum' => ['field' => 'doc.backoutContract']],
							'backoutTeamContract_sum' => ['sum' => ['field' => 'doc.backoutTeamContract']],
						]
					]
				]
			]
		];

		$response = $this->elasticService->getClient()->search($rankQuery);
		$buckets = $response['aggregations']['user_scores']['buckets'];

		

		$scoreMap = collect($buckets)->mapWithKeys(function ($item) {
			$score = ($item['profileUpload_sum']['value'] ?? 0)
				+ ($item['interviewScheduled_sum']['value'] ?? 0)
				+ ($item['interviewNoShow_sum']['value'] ?? 0)
				+ ($item['offerReleased_sum']['value'] ?? 0)
				+ ($item['candidateJoined_sum']['value'] ?? 0)
				+ ($item['teamCaptainInterviewScheduled_sum']['value'] ?? 0)
				+ ($item['teamCaptainOfferReleased_sum']['value'] ?? 0)
				+ ($item['teamCaptainCandidateJoined_sum']['value'] ?? 0)
				+ ($item['sourcing_sum']['value'] ?? 0)
				+ ($item['screening_sum']['value'] ?? 0)
				+ ($item['evaluation_sum']['value'] ?? 0)
				+ ($item['negotiation_sum']['value'] ?? 0)
				+ ($item['backout_sum']['value'] ?? 0)
				+ ($item['backoutTeam_sum']['value'] ?? 0) + ($item['candidateJoinedContract_sum']['value'] ?? 0) + ($item['teamCaptainCandidateJoinedContract_sum']['value'] ?? 0) + ($item['backoutContract_sum']['value'] ?? 0) + ($item['backoutTeamContract_sum']['value'] ?? 0);

			return [$item['key'] => [
				'user_id' => $item['key'],
				'profileUpload' => $item['profileUpload_sum']['value'] ?? 0,
				'interviewScheduled' => $item['interviewScheduled_sum']['value'] ?? 0,
				'interviewNoShow' => $item['interviewNoShow_sum']['value'] ?? 0,
				'offerReleased' => $item['offerReleased_sum']['value'] ?? 0,
				'candidateJoined' => $item['candidateJoined_sum']['value'] ?? 0,
				'teamCaptainInterviewScheduled' => $item['teamCaptainInterviewScheduled_sum']['value'] ?? 0,
				'teamCaptainOfferReleased' => $item['teamCaptainOfferReleased_sum']['value'] ?? 0,
				'teamCaptainCandidateJoined' => $item['teamCaptainCandidateJoined_sum']['value'] ?? 0,
				'sourcing' => ($item['sourcing_sum']['value'] ?? 0),
				'screening' => ($item['screening_sum']['value'] ?? 0),
				'evaluation' => ($item['evaluation_sum']['value'] ?? 0),
				'negotiation' => ($item['negotiation_sum']['value'] ?? 0),
				'backout' => ($item['backout_sum']['value'] ?? 0),
				'backoutTeam' => ($item['backoutTeam_sum']['value'] ?? 0),
				'candidateJoinedContract' => ($item['candidateJoinedContract_sum']['value'] ?? 0),
				'teamCaptainCandidateJoinedContract' => ($item['teamCaptainCandidateJoinedContract_sum']['value'] ?? 0),
				'backoutContract' => ($item['backoutContract_sum']['value'] ?? 0),
				'backoutTeamContract' => ($item['backoutTeamContract_sum']['value'] ?? 0),
				'score' => $score
			]];
		});

		$ranked = $users->map(function ($user) use ($scoreMap) {
			$id = $user['id'];
			return [
				'id' => $id,
				'status' => strtolower($user['status'] ?? 'inactive'),
				'score' => $scoreMap[$id]['score'] ?? 0,
			];
		})->sort(function ($a, $b) {

			// 1️⃣ Active users first
			if ($a['status'] !== $b['status']) {
				return ($a['status'] === 'active') ? -1 : 1;
			}

			// 2️⃣ Higher score first
			if ($a['score'] !== $b['score']) {
				return $b['score'] <=> $a['score'];
			}

			// 3️⃣ User ID ASC
			return $a['id'] <=> $b['id'];
		})
		->values();

		$ranked = $ranked->map(function ($user, $index) {
			$user['rank'] = $index + 1;
			return $user;
		});


		return $ranked->firstWhere('id', $loggedInUserId) ?? ['score' => 0, 'rank' => 'N/A'];
	}

	/**
	 * Returns the ranked leaderboard (top 10 + the logged-in user's own row)
	 * for a given period. Mirrors getRankData() scoring but keeps user names/avatars.
	 */
	private function getLeaderboard($company_id, $loggedInUserId, $startDate, $endDate, $isInternal = true)
	{
		$userResponse = $this->elasticService->getClient()->search([
			'index' => ELASTIC_USERS_LIST,
			'body' => [
				'query' => ['bool' => ['filter' => array_values(array_filter([
					$isInternal ? ['term' => ['company_id' => $company_id]] : null,
				]))]],
				'_source' => ['id', 'name', 'email', 'designation', 'status', 'image'],
				'size' => 10000,
				'sort' => [['id' => ['order' => 'asc']]],
			],
		]);
		$users = collect($userResponse['hits']['hits'])->pluck('_source');

		$rankResponse = $this->elasticService->getClient()->search([
			'index' => ELASTIC_TEAM_RANK,
			'body' => [
				'query' => ['bool' => ['filter' => array_values(array_filter([
					$isInternal ? ['term' => ['doc.company_id' => $company_id]] : null,
					['range' => ['doc.created_at' => ['gte' => $startDate, 'lte' => $endDate]]],
				]))]],
				'aggs' => ['user_scores' => [
					'terms' => ['field' => 'doc.user_id', 'size' => 10000],
					'aggs' => [
						'profileUpload_sum' => ['sum' => ['field' => 'doc.profileUpload']],
						'interviewScheduled_sum' => ['sum' => ['field' => 'doc.interviewScheduled']],
						'interviewNoShow_sum' => ['sum' => ['field' => 'doc.interviewNoShow']],
						'offerReleased_sum' => ['sum' => ['field' => 'doc.offerReleased']],
						'candidateJoined_sum' => ['sum' => ['field' => 'doc.candidateJoined']],
						'teamCaptainInterviewScheduled_sum' => ['sum' => ['field' => 'doc.teamCaptainInterviewScheduled']],
						'teamCaptainOfferReleased_sum' => ['sum' => ['field' => 'doc.teamCaptainOfferReleased']],
						'teamCaptainCandidateJoined_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoined']],
						'sourcing_sum' => ['sum' => ['field' => 'doc.sourcing']],
						'screening_sum' => ['sum' => ['field' => 'doc.screening']],
						'evaluation_sum' => ['sum' => ['field' => 'doc.evaluation']],
						'negotiation_sum' => ['sum' => ['field' => 'doc.negotiation']],
						'backout_sum' => ['sum' => ['field' => 'doc.backout']],
						'backoutTeam_sum' => ['sum' => ['field' => 'doc.backoutTeam']],
						'candidateJoinedContract_sum' => ['sum' => ['field' => 'doc.candidateJoinedContract']],
						'teamCaptainCandidateJoinedContract_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoinedContract']],
						'backoutContract_sum' => ['sum' => ['field' => 'doc.backoutContract']],
						'backoutTeamContract_sum' => ['sum' => ['field' => 'doc.backoutTeamContract']],
					],
				]],
				'size' => 0,
			],
		]);
		$buckets = $rankResponse['aggregations']['user_scores']['buckets'] ?? [];

		$scoreMap = collect($buckets)->mapWithKeys(function ($item) {
			$score = ($item['profileUpload_sum']['value'] ?? 0)
				+ ($item['interviewScheduled_sum']['value'] ?? 0)
				+ ($item['interviewNoShow_sum']['value'] ?? 0)
				+ ($item['offerReleased_sum']['value'] ?? 0)
				+ ($item['candidateJoined_sum']['value'] ?? 0)
				+ ($item['teamCaptainInterviewScheduled_sum']['value'] ?? 0)
				+ ($item['teamCaptainOfferReleased_sum']['value'] ?? 0)
				+ ($item['teamCaptainCandidateJoined_sum']['value'] ?? 0)
				+ ($item['sourcing_sum']['value'] ?? 0)
				+ ($item['screening_sum']['value'] ?? 0)
				+ ($item['evaluation_sum']['value'] ?? 0)
				+ ($item['negotiation_sum']['value'] ?? 0)
				+ ($item['backout_sum']['value'] ?? 0)
				+ ($item['backoutTeam_sum']['value'] ?? 0) + ($item['candidateJoinedContract_sum']['value'] ?? 0) + ($item['teamCaptainCandidateJoinedContract_sum']['value'] ?? 0) + ($item['backoutContract_sum']['value'] ?? 0) + ($item['backoutTeamContract_sum']['value'] ?? 0);
			return [$item['key'] => $score];
		});

		$ranked = $users->map(function ($user) use ($scoreMap) {
			$id = $user['id'];
			return [
				'id' => $id,
				'name' => $user['name'] ?? 'User',
				'designation' => $user['designation'] ?? '',
				'image' => $user['image'] ?? '',
				'status' => strtolower($user['status'] ?? 'inactive'),
				'score' => (int) round($scoreMap[$id] ?? 0),
			];
		})->sort(function ($a, $b) {
			if ($a['status'] !== $b['status']) {
				return ($a['status'] === 'active') ? -1 : 1;
			}
			if ($a['score'] !== $b['score']) {
				return $b['score'] <=> $a['score'];
			}
			return $a['id'] <=> $b['id'];
		})->values()->map(function ($user, $index) {
			$user['rank'] = $index + 1;
			return $user;
		});

		return [
			'top'   => $ranked->take(10)->values(),
			'me'    => $ranked->firstWhere('id', $loggedInUserId),
			'total' => $ranked->count(),
		];
	}

	private function getRankDataComman($company_id, $loggedInUserId, $startDate, $endDate, $isInternal = true)
	{
		// Get active users

		// dd($company_id, $loggedInUserId, $startDate, $endDate, $isInternal);
		$userQuery = [
			'index' => ELASTIC_USERS_LIST,
			'body' => [
				'query' => [
					'bool' => [
						'filter' => array_values(array_filter([
							$isInternal ? ['term' => ['company_id' => $company_id]] : null,
							// ['term' => ['status' => 'active']],
						])),
					],
				],
				'_source' => ['id', 'name', 'email', 'designation','status'],
				'size' => 10000,
				'sort'    => [
					['id' => ['order' => 'asc']],
				],
			],
		];

		$userResponse = $this->elasticService->getClient()->search($userQuery);
		$users = collect($userResponse['hits']['hits'])->pluck('_source');

		

		// Get ranking stats
		$rankQuery = [
			'index' => ELASTIC_TEAM_RANK,
			'body' => [
				'query' => [
					'bool' => [
						'filter' => array_values(array_filter([
							$isInternal ? ['term' => ['doc.company_id' => $company_id]] : null,
							['range' => [
								'doc.created_at' => [
									'gte' => $startDate,
									'lte' => $endDate,
								]
							]]
						])),
					],
				],
				'aggs' => [
					'user_scores' => [
						'terms' => [
							'field' => 'doc.user_id',
							'size' => 10000
						],
						'aggs' => [
							'interviewScheduled_sum' => ['sum' => ['field' => 'doc.interviewScheduled']],
						]
					]
				]
			]
		];

		$response = $this->elasticService->getClient()->search($rankQuery);
		$buckets = $response['aggregations']['user_scores']['buckets'];

		

		$scoreMap = collect($buckets)->mapWithKeys(function ($item) {
			$score = ($item['interviewScheduled_sum']['value'] ?? 0);

			return [$item['key'] => [
				'user_id' => $item['key'],
				'interviewScheduled' => $item['interviewScheduled_sum']['value'] ?? 0,
				'score' => $score
			]];
		});

		$ranked = $users->map(function ($user) use ($scoreMap) {
			$id = $user['id'];
			return [
				'id' => $id,
				'status' => strtolower($user['status'] ?? 'inactive'),
				'score' => $scoreMap[$id]['score'] ?? 0,
			];
		})->sort(function ($a, $b) {

			// 1️⃣ Active users first
			if ($a['status'] !== $b['status']) {
				return ($a['status'] === 'active') ? -1 : 1;
			}

			// 2️⃣ Higher score first
			if ($a['score'] !== $b['score']) {
				return $b['score'] <=> $a['score'];
			}

			// 3️⃣ User ID ASC
			return $a['id'] <=> $b['id'];
		})
		->values();

		$ranked = $ranked->map(function ($user, $index) {
			$user['rank'] = $index + 1;
			return $user;
		});


		return $ranked->firstWhere('id', $loggedInUserId) ?? ['score' => 0, 'rank' => 'N/A'];
	}

	public function edit()
	{
			
				$id = $this->user->id;
			
        		//abort_if(Gate::denies('edit_team'), 403);

        		/*if($id == $this->user->id){
            									abort(403);
        			}*/
       		 	//$this->roles = Role::all();
       			// $this->team = User::find($id);

				$params = [
						'index' => ELASTIC_USERS_LIST,
						'id'    => $id,
				];
				
				$response = $this->elasticService->getClient()->get($params);
				$team_details = (object) $response['_source'];
				// dd($team_details->city_name,$team_details->state_name,$team_details->country_name);
                $this->city_name = $team_details->city_name ?? '';
				$this->state_name = $team_details->state_name ?? '';
				$this->country_name = $team_details->country_name ?? '';
		
				
				$this->statelist 		=	array();
				$this->team 			=	User::join('companies', 'companies.id', '=', 'users.company_id')
											->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')						
											->select(
														'users.id', 'users.company_id', 'users.name', 'users.email', 'users.mobile', 'users.created_at','users.calling_code', 'users.image', 'users.status', 'users.image', 'users.whatsapp_number', 'users.linkedin', 
														'companies.company_name', 'companies.website',
														'users_profile.country', 'users_profile.state', 'users_profile.city', 'users_profile.city_id', 'users_profile.designation', 'users_profile.skills'  
													)
											->with('company')
											->where('users.id', '=', $id)
											->company()
											->first();
				
				$this->categories 		= JobCategory::all();
				$this->companyType		= GeneralMethod::companyType();
				$this->department		= GeneralMethod::department();
				$this->countryArr 		= GeneralMethod::getAllCountryList();	
				if($this->team->country > 0){
					$this->statelist 		= GeneralMethod::statelist($this->team->country);	
				}	 
				
				$this->cityListArr       			=	'';			
				if($this->team->country == 101) 
				{
					$statelist 					=	DB::table('state_list')
													->select('state_list.id', 'state_list.state as name', DB::raw("101 as country_id"))
													->orderBy('state_list.state')
													->get()->toArray();
					$this->statelist       		= 	json_decode(json_encode($statelist), true);	
								
					$citylist 					=	DB::table('city_list')
													->select('city_list.city_id as id', 'city_list.city as name')
													->where('city_list.state_id', $this->team->state)
													->orderBy('city_list.city')
													->get()->toArray();
					$this->cityListArr       		= json_decode(json_encode($citylist), true);
					
				}

				$cities = DB::table('city_list')
				->join('state_list', 'state_list.id', '=', 'city_list.state_id')
				->join('countries', 'countries.id', '=', 'state_list.country_id')
				->select('city_list.city as city_name', 'state_list.state as state_name', 'countries.country_name')
				->where('city_list.city_id','=', $this->team->city_id)
				->orderBy('city_list.city')
				->limit(10)
				->cursor()
				->map(function($city) {
					return $city->city_name . ' | ' . $city->state_name . ' | ' . $city->country_name;
				});
				if($cities){
		       		 $this->cities = ($cities) ? $cities: '';
				}else{
					$this->cities = GeneralMethod::allCities();
				}

				// 
						
    			return view('service_company.profile.edit', $this->data);
		
		
	}

    public function update_old(UpdateProfile $request)
	{
			//echo '<pre>'; print_r($_REQUEST); exit; 
			
        $user 						= User::withoutGlobalScopes()->find($this->user->id);
        $user->name 				= $request->name;
        $user->email 				= $request->email;
		
		
        if($request->password != ''){
            $user->password = Hash::make($request->password);
        }

        if ($request->has('mobile')) {
            if ($user->mobile !== $request->mobile || $user->calling_code !== $request->calling_code) {
                $user->mobile_verified = 0;
            }

            $user->mobile = $request->mobile;
            $user->calling_code = $request->calling_code;
        }

        if ($request->hasFile('image')) {
            $user->image = Files::upload($request->image,'profile');
        }
        $user->save();	
				
		
        return Reply::redirect(route('service_company.profile.index'), __('menu.myProfile').' '.__('messages.updatedSuccessfully'));
    }
	
	public function update(Request $request, $id)
	{
		
		
			$company_id      	=	auth()->user()->company_id;
			$previousUrl = url()->previous();
			$previousPath = parse_url($previousUrl, PHP_URL_PATH); // Just the path without domain
			$cleanedPath = str_replace('index.php', '', $previousPath);
			$segments = explode('/', trim($cleanedPath, '/')); 
            $currentSlug = end($segments);
			if($segments[0]=='MEECRUITUAT'){
				$currentSlug = isset($segments[3]) ? $segments[3] : null;
			}else{
				$currentSlug = isset($segments[1]) ? $segments[1] : null;
			}
			
			$team_page = false;
			if($currentSlug=='team'){
               $team_page = true;
			}

			
			$user 				= User::withoutGlobalScopes()->find($id);
        	$user->name 		= $request->name;
			$user->mobile       = $request->mobile;
			$user->whatsapp_number       = $request->whatsapp_number;
			$user->linkedin       = $request->linkedin;
			$user->calling_code       = $request->phone_country_code;
			if ($request->hasFile('logo')) {
				
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
	
				//  $fileUrl = Storage::disk('s3')->url($filePath);
				 $user->image = $fileUrl;
                 // 	$user->image = Files::upload($request->logo,'team_profile_img');
        	}

			$work_city = @$request->kt_tagify_work_location;
			$work_city_all = json_decode($work_city, true);
			$parts = array_map('trim', explode('|', $work_city_all[0]['value']));



			// $user->country_name = $parts[2] ?? '';
			// $user->state_name = $parts[1] ?? '';
			// $user->city_name = $parts[0] ?? '';

			// dd($user->city_name,$user->state_name,$user->country_name);

       		$user->save();
			
			$users_profile_count	= DB::table('users_profile')->select('*')->where('user_id', $id)->count();		
			
			$users_team_count	= DB::table('client_contact_info')->select('*')->where('contact_person_email', $request->email)->count();	
			// dd($request->email,$users_team_count);
			if($users_team_count > 0)
			{
			$insertArr = array('contact_person_name' =>$request->name, 'contact_person_designation'=>$request->designation);
			DB::table('client_contact_info')->where('contact_person_email', $request->email)->limit(1) ->update($insertArr); 
			}

			
			   $stateId = '';
				$cityID = '';
				$countryID = '';
				$city = '';
				
				// dd($work_city_all);
				$values = [];
				if (@$request->kt_tagify_work_location != "") {
					foreach ($work_city_all as $cityVal) {
						$parts = explode('|', $cityVal['value']);
						// Trim to remove any leading or trailing whitespace
						$cityName = trim($parts[0]);
						$countrName = trim($parts[2]);
						$city_row = DB::table('city_list')
						->join('state_list', 'state_list.id', '=', 'city_list.state_id')
						->join('countries', 'countries.id', '=', 'state_list.country_id')
						->select('city_list.city_id', 'countries.id as country_id')
						->where('city_list.city','!=','')
						->where('city_list.city', '=', $cityName)
						->where('countries.country_name', '=', $countrName)
						->first();
						if($city_row){
							$cityID = (@$city_row) ? $city_row->city_id : '';
							$countryID = (@$city_row) ? $city_row->country_id : '';
							$stateId = GeneralMethod::stateId($cityID);

							$values 				= array(
								'company_id' 	=> $company_id, 
								'user_id' 	=> $id, 
								'designation' 			=> $request->designation,
								'city_id' 		=> $cityID, 
								'country' 		=> $countryID, 
								'state' 		=> $stateId
							);

						}
						
					}
				}
								
			if($users_profile_count > 0)
			{			
				//UPDATE
				DB::table('users_profile')->where('company_id', @$company_id)->where('user_id', @$id)->limit(1) ->update($values);  
			}
			else
			{
				//INSERT				
				DB::table('users_profile')->insert($values);
			}
				
			$cachekeys = [
			'allteam_version_user_'.$this->user->id,				
			'teamviewmore_version_user_'.$this->user->id
			];
			foreach ($cachekeys as $key) {
				GeneralMethod::cacheClean($key);
			}

			GeneralMethod::userIndexingupload($id);
			$message		=	"update successfully";
    		return Reply::dataOnly(['status' => 'success', 'message'=> $message,'team_page'=>$team_page]);   
		
	}
	
	public function appdup()
	{
			$company_id			= 	$this->user->company_id;
			$rolesCustome = $this->user->getAllPermissions();
			$this->admin		=	$rolesCustome[0]->name;
						
        	//$this->calling_codes = $this->getCallingCodes();
        	return view('service_company.profile.appdup', $this->data);
			
			
	}
	
	public function appdupupdate(Request $request)
	{
		$company_id					= 	$this->user->company_id;
		
			$rolesCustome = $this->user->getAllPermissions();
			$this->admin		=	$rolesCustome[0]->name;
		//if($admin == 'admin')
		//{
			$app_duplicate 			= $request->app_duplicate;	
			$app_duplicate_days 	= $request->app_duplicate_days;	
						
			DB::table('companies')
						->where('id', $company_id)
						//->where('candidate_id', @$request->candidate)
						//->where('skill_id', @$request->schemaname)
						->limit(1) 
						->update(array('app_duplicate' => @$app_duplicate, 'app_duplicate_days' => @$app_duplicate_days));     
						
			
		//}		s
		
		return Reply::redirect(route('service_company.profile.appdup'), __('menu.myProfile').' '.__('messages.updatedSuccessfully'));
	
	}
	
	public function videoMeetingSettings()
	{
		
			//echo '<pre>'; print_r($this->user->company); exit; 
			$company_id					= 	$this->user->company_id;
			
			$rolesCustome = $this->user->getAllPermissions();
			$this->admin		=	$rolesCustome[0]->name;

        	$this->calling_codes 		= 	$this->getCallingCodes();
			
			$this->data['company']		=	$this->user->company;
			
			//echo '<pre>'; print_r($this->data['company']); exit;  
			
        	return view('service_company.profile.videoMeetingSettings', $this->data);
    }
		
	public function videoupdate(Request $request)
	{
	
		$zoom_status		=	$zoom_api_key		=	$zoom_api_secret		=	'';
		$google_status		=	$google_api_key		=	$google_api_secret		=	'';
		$microsoft_status	=	$microsoft_api_key	=	$microsoft_api_secret	=	'';
				
		$zoom_status 			= $request->zoom_status;	
		$zoom_api_key 			= $request->zoom_api_key;	
		$zoom_api_secret 		= $request->zoom_api_secret;
		
		$google_status 			= $request->google_status;
		$google_api_key 		= $request->google_api_key;
		$google_api_secret 		= $request->google_api_secret;
		
		$microsoft_status 		= $request->microsoft_status;
		$microsoft_api_key 		= $request->microsoft_api_key;
		$microsoft_api_secret 	= $request->microsoft_api_secret;
			
			
		
		$company_id					= 	$this->user->company->id;
		
			$rolesCustome = $this->user->getAllPermissions();
			$this->admin		=	$rolesCustome[0]->name;
		
		
		$update						=	array(
												'zoom_status' 			=> @$zoom_status, 
												'zoom_api_key' 			=> @$zoom_api_key,
												'zoom_api_secret' 		=> @$zoom_api_secret,
												
												'google_status' 		=> @$google_status,
												'google_api_key' 		=> @$google_api_key,
												'google_api_secret' 	=> @$google_api_secret,
												
											);	
					
		DB::table('companies')
						->where('id', $company_id)
						//->where('candidate_id', @$request->candidate)
						//->where('skill_id', @$request->schemaname)
						->limit(1) 
						->update($update);     
						
			
	
		
		return Reply::redirect(route('service_company.profile.appdup'), __('menu.myProfile').' '.__('messages.updatedSuccessfully'));
	
	}
	
	public function editProfile()
	{		
        	return view('service_company.profile.editProfile', $this->data);
    }	
	
	public function profileedit()
	{			
        	return view('service_company.profile.profileedit', $this->data);
    }	
		
	public function verifyGoogleRedirect(Request $request)
	{
			date_default_timezone_set('Asia/Calcutta'); 
			$strtotime 					= strtotime(date("Y-m-d H:i:s"));
		
		
		$code							=	$scope	=	$company	=	$company_id	=	'';
		$code 							=	$request->code;
		$scope 							=	$request->scope;		
		$company_id      				=	auth()->user()->company_id;
		//$CLIENT_REDIRECT_URL			=	route('service_company.profile.verify-google-redirect');
		//$CLIENT_REDIRECT_URL			=	route('service_company.profile.verify-google-redirect');
		
		$CLIENT_REDIRECT_URL			=	route('service_company.profile.verify-google-redirect').'/';  
		
		$company						=	$this->user->company;
		//Session::put('code', $code);
		//Session::put('scope', $scope);	
				
		$data 							= 	Google::GetAccessToken($company->google_client_id, $CLIENT_REDIRECT_URL, $company->google_client_secret, $code);
		
		 
		//echo '<pre>'; print_r($data); exit; 
		
		//if(array_key_exists('refresh_token', $data))
		//{
			//refresh_token 				= $data['refresh_token'];
			//Session::put('refresh_token', $refresh_token);
		//}
		//Session::put('access_token_expiry', $strtotime + $data['expires_in']);
		//Session::put('access_token', $data['access_token']);
		
		$access_token	=	$data['access_token'];
		$expires_in		=	$strtotime + $data['expires_in'];
		$refresh_token	=	$data['refresh_token'];
		$scope			=	$data['scope'];
		$token_type		=	$data['token_type'];
		
		$update			=	array(
									'google_access_token' 	=> @$access_token, 
									'google_expires_in' 	=> @$expires_in,
									'google_refresh_token' 	=> @$refresh_token,
									'google_scope' 			=> @$scope,
									'google_token_type' 	=> @$token_type,
									'google_verify' 		=> 1											
								);			
		DB::table('companies')->where('id', $company_id)->limit(1)->update($update);		
		
		return Reply::redirect(route('service_company.account'), __('menu.myProfile').' '.__('messages.updatedSuccessfully'));
		
		//return Redirect::to(route('service_company.profile.video-meetings-settings'));
		//return Reply::dataOnly([
									//'status' 		=> 	'success',
									//'message'		=>	$message,
									//'data'			=>	$data
							  //]);
		
		//service_company/account
		//return Reply::dataOnly(['status' => 'success', 'message'=> 'verify']);   
								
	}		
	
}
