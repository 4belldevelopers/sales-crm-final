<?php

/***########################################################
Controller    	: 	interviewScheduleController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/

namespace App\Http\Controllers\ServiceCompany;

use App\Helper\Reply;
use App\Helper\GeneralMethod;
use App\Models\InterviewSchedule;
use App\Models\OpenworkspaceInterviewSchedule;
use App\Models\JobApplication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use App\Models\AddJobWiseCondidate;
use App\Jobs\QueueNotificationMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Services\MailService;
use App\Services\ElasticService;

class InterviewScheduleController extends ServiceCompanyBaseController
{
    protected $mailService;
	protected $elasticService;
	public function __construct(MailService $mailService,ElasticService $elasticService)
	{
		parent::__construct();
		$this->pageTitle = 'Interview Tracker';
		$this->pageIcon = 'icon-calender';
		$this->mailService = $mailService;
		$this->elasticService = $elasticService;
	}
	public function table(Request $request)
	{
		$lock_unlock = GeneralMethod::checksubscription();
		if($lock_unlock==false){
			return redirect()->route('service_company.dashboard')->with('message_paid', 'This is a paid featute');;
		}

		$query = JobApplication::query();

		if (!$this->user->hasRole('admin')) {
			$query->where('submitted_by', $this->user->id);
		}

		$this->candidates = $query->limit(2)->get();

		return view('service_company.interview-schedule.table', $this->data);
	}
	public function data_ajax(Request $request)
	{

		abort_if(Gate::denies('view_schedule'), 403);

		$company_id = auth()->user()->company_id;
		// dd($company_id);
		$available_status = $feedback_status = $interview_mode_arr = '';

		$available_status = GeneralMethod::available_status();
		$feedback_status = GeneralMethod::feedback_status();
		$interview_mode_arr = GeneralMethod::interview_mode();
		$job_status_arr = GeneralMethod::job_status();

		$startOfDay = Carbon::now()->startOfDay()->toDateTimeString();
		$endOfDay = Carbon::now()->endOfDay()->toDateTimeString();
		$valCheck_date = request('custom_hiringtype');
		// dd($valCheck_date );
		$chkAdminRole = $this->user->hasRole('admin');
		// dd($startOfDay,$endOfDay);


		$searchTerm = trim(request('custom_search') ?? $request->input('q'));
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;
		$must = [];
		$should = [];
		$filter = [];

		$cachePayload = [
			'page' => $page,
			'size' => $size,
			'company' => $company_id,
			'searchTerm' => $searchTerm,
			'user_id' => $this->user->id,
			'sort' => $valCheck_date
		];

		$versionKey = 'allinterviewschedule_version_user_'.$this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:allinterviewschedule:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
		/* ----------------------------
		⚡ Check DragonflyDB first
		----------------------------- */
		if (cache()->has($cacheKeySet)) {
			return response()->json(cache()->get($cacheKeySet));
		}


        $valCheck = request('custom_hiringtype');
		switch ($valCheck) {
			case 1:
				$sort[] = ['schedule_date' => ['order' => 'asc']];		
				// $sort[] = ['id' => ['order' => 'desc']];		
				break;
			case 2:
				$sort[] = ['created_at' => ['order' => 'asc']];
				break;
			case 3:
				$sort[] = ['schedule_date' => ['order' => 'desc']];
				break;
			case 4:
				$sort[] = ['schedule_date' => ['order' => 'asc']];
				break;
			default:
				$sort[] = ['schedule_date' => ['order' => 'desc']];
				$sort[] = ['id' => ['order' => 'desc']];
		}

		// 2. Feedback pending first (nulls first)
		
		// if($valCheck=='Feedback Pending'){
		// 	       // 1. Sort by feedback pending (feedback is null)
		// 	array_unshift($sort, [
		// 		'_script' => [
		// 			'type' => 'number',
		// 			'script' => [
		// 				'source' => "doc['feedback'].size() == 0 ? 0 : 1",
		// 				'lang' => 'painless'
		// 			],
		// 			'order' => 'asc'
		// 		]
		// 	]);
		// 	$filter[] = [
		// 		'range' => [
		// 			'schedule_date' => [
		// 				'lte' => now()->subHour()->toIso8601String(),
		// 				'format' => 'strict_date_optional_time'
		// 			]
		// 		]
		// 	];
		// }
		// if($valCheck=='Interview Pending'){
		// 	       // 1. Sort by feedback pending (feedback is null)
		// 	array_unshift($sort, [
		// 		'_script' => [
		// 			'type' => 'number',
		// 			'script' => [
		// 				'source' => "doc['feedback'].size() == 0 ? 0 : 1",
		// 				'lang' => 'painless'
		// 			],
		// 			'order' => 'asc'
		// 		]
		// 	]);
		// 	$filter[] = [
		// 		'range' => [
		// 			'schedule_date' => [
		// 				'gte' => now()->subHour()->toIso8601String(),
		// 				'format' => 'strict_date_optional_time'
		// 			]
		// 		]
		// 	];
		// }

		if ($valCheck == 'Feedback Pending') {
			$sort = [];
			$sort[] = ['schedule_date' => ['order' => 'desc']];
			// Sort by feedback (null first), after schedule_date
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'source' => "doc['feedback'].size() == 0 ? 0 : 1",
						'lang' => 'painless'
					],
					'order' => 'asc'
				]
			];

			$filter[] = [
				'range' => [
					'schedule_date' => [
						'lte' => now()->subHour()->toIso8601String(),
						'format' => 'strict_date_optional_time'
					]
				]
			];
			$filter[] = [
				'bool' => [
					'must_not' => [
						'exists' => [
							'field' => 'feedback'
						]
					]
				]
			];
		}

		if ($valCheck == 'Interview Pending') {
			$sort = [];
			$sort[] = ['schedule_date' => ['order' => 'asc']];
			// Sort by feedback (null first), after schedule_date
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'source' => "doc['feedback'].size() == 0 ? 0 : 1",
						'lang' => 'painless'
					],
					'order' => 'asc'
				]
			];

			$filter[] = [
				'range' => [
					'schedule_date' => [
						'gte' => now()->subHour()->toIso8601String(),
						'format' => 'strict_date_optional_time'
					]
				]
			];
			$filter[] = [
				'bool' => [
					'must_not' => [
						'exists' => [
							'field' => 'feedback'
						]
					]
				]
			];
			$filter[] = [
				'bool' => [
					'must_not' => [
						'term' => [
							'available_status' => 2
						]
					]
				]
			];
		}


		// Custom search logic
		if (!empty($searchTerm)) {
			$must[] = [
				'bool' => [
					'must' => [
						[
							'bool' => [
								'should' => [
									['term' => ['phone' => $searchTerm]],
									['term' => ['email' => $searchTerm]],
									['match_phrase_prefix' => ['job_title' => $searchTerm]],
									['match_phrase_prefix' => ['client_name' => $searchTerm]],
									['match_phrase_prefix' => ['full_name' => $searchTerm]],
									['wildcard' => ['city_name' => strtolower($searchTerm) . '*']],
									['wildcard' => ['country_name' => strtolower($searchTerm) . '*']],
									['wildcard' => ['state_name' => strtolower($searchTerm) . '*']],
									['wildcard' => ['posted_by' => strtolower($searchTerm) . '*']],
									['match_phrase_prefix' => ['company_name' => $searchTerm]],
								],
								'minimum_should_match' => 1,
							]
						]
					]
				]
			];
		}

		$must[] = [
			'bool' => [
				'should' => [
					['term' => ['job_company_id' => $company_id]],
					[
						'bool' => [
							'must' => [
								['term' => ['ClientComID' => $company_id]],
								['term' => ['candidate_type' => 'other']],
							]
						]
					],
					// If you still need the nested recommendation_relation condition, include it here
					// [
					//     'nested' => [
					//         'path' => 'recommendation_relation',
					//         'query' => [
					//             'bool' => [
					//                 'should' => [
					//                     ['term' => ['recommendation_relation.to_company_id' => $company_id]],
					//                 ],
					//                 'minimum_should_match' => 1
					//             ]
					//         ],
					//     ],
					// ],
				],
				'minimum_should_match' => 1,
			]
		];
		
		
		// 5. RESTRICT TO ASSIGNED USERS (if not admin)
		if (!$chkAdminRole > 0) {
			$must[] = [
				'nested' => [
					'path' => 'job_assign',
					'query' => [
						'term' => ['job_assign.user_id' => auth()->user()->id],
					],
				],
			];
		}

		if ($valCheck_date == 1) {
			$filter[] = [
				'range' => [
					'schedule_date' => [
						'gte' =>  Carbon::parse($startOfDay)->startOfDay()->toIso8601String(),
						'lte' =>  Carbon::parse($endOfDay)->endOfDay()->toIso8601String(),
						'format' => 'strict_date_optional_time' // optional based on your index mapping
					]
				]
			];
		}

		// FINAL ELASTIC PARAMS
		$params = [
			'index' => ELASTIC_INERTVIEW_TRACKER,
			'request_cache' => true,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => $sort,
				'query' => [
					'bool' => [
						'must' => $must,
						'filter' => $filter,
					],
				],
			],
		];

		$response = $this->elasticService->getClient()->search($params);
		if (!$this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
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
			'html' => view('service_company.ajax_pages.interview-tracker-ajax', compact('data','userPermissions','feedback_status','available_status','company_id','chkAdminRole'))->render(),
			'total' => $total,
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		];


		cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_INTERVIEW_SCHEDULE_TIME));

	    return response()->json($responseData);
		



		
		if (($chkAdminRole > 0) && (in_array("all_requisition", $this->userPermissions) && in_array("view_jobs", $this->userPermissions))) {
       		 // dd("ki");
			$interviewShedule = DB::table('interview_schedules')
				->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
				->join('add_job_wise_condidates', 'interview_schedules.interview_job_wise_con_id', 'add_job_wise_condidates.id')
				// ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
				->join('jobs', function ($join) use ($company_id) {
					$join->on('jobs.id', 'add_job_wise_condidates.job_id');
				})
				->join('companies', function ($join) use ($company_id) {
					$join->on('companies.id', 'jobs.company_id');
				})
				->leftjoin('clients', 'clients.id', 'jobs.client_id')
				->leftjoin('users', 'users.id', 'add_job_wise_condidates.submitted_by')
				->leftjoin('countries', 'countries.id', 'job_applications.country')
				->leftjoin('state_list', 'state_list.id', 'job_applications.state')
				->leftjoin('city_list', 'city_list.city_id', 'job_applications.city_id')
				->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id')
				->leftjoin('interview_feedback', 'interview_feedback.interview_id', 'interview_schedules.id')
				// 'interview_schedules.stage'
				// ->leftjoin('companies', 'companies.id', 'jobs.company_id')
				->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')
				->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
				->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
				->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage')
				->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')    
				->leftJoin('updatetimeline','updatetimeline.interview_schedules_id','interview_schedules.id') 
				->select(
					'interview_schedules.id',
					'job_applications.full_name',					
                    'job_applications.email',
                    'job_applications.phone',
					'interview_schedules.status',
					'interview_schedules.schedule_date',
					'interview_schedules.stage as master_status_id',
					'interview_schedules.substage as child_status_id',
					'clients.name as client_name',
					'interview_schedules.available_status',
					'interview_schedules.reschedule',
					'interview_schedules.feedback',
					'interview_schedules.interview_mode',
					'interview_schedules.interviewers',
					'interview_schedules.interview_type',
					'interview_schedules.schedule_from_time',
					'interview_schedules.company_id as real_company_id',
					'interview_schedules.mailstatus',
					'interview_schedules.timezone_abbreviation',
					'interview_schedules.schedule_to_time',
					'users.name as source',
					'users.name as posted_by',
					'users.image as posted_by_image',
					'interview_schedules.job_application_id',
					'master_status.name as master_status_name',
					'candidate_status.name as child_status_name',
					'jobs.title as job_title',
					'interview_schedules.interview_job_id as job_id',
					'companies.company_name',
					'job_applications.current_designation',					
                    'job_applications.condidate_type',
					'job_applications.thirdparty_company_id',
					'interview_mode.name as mode_name',
					'interview_mode_lavel.name as mode_lavel_name',
					'master_status.css_style',
					'interview_schedules.average_star_rating',
					'jobs.status as job_status',
					'recommendation_relation.id as recommendation_id',
					'recommendation_relation.from_company_id',
					'recommendation_relation.final_status',
					'recommendation_relation.to_company_id',
					'add_job_wise_condidates.id as wiseID',
					'add_job_wise_condidates.thirdparty_company_id as ClientComID',
					'add_job_wise_condidates.job_id as jobId',
					'uc.company_name as uc_company_name',
					'updatetimeline.interview_mention_link'
				);

			
			//  $interviewShedule->where('interview_schedules.feedback','=','');

			if (\request('profile') != "") {
				// dd("jfj");
				$interviewShedule = $interviewShedule->where('job_applications.company_id', $company_id);
			    $interviewShedule = $interviewShedule->where('add_job_wise_condidates.submitted_by', $this->user->id);
		    }else{
		      //  ->orWhere('job_applications.submitted_by', '=', auth()->user()->id)
			   if(\request('custom_hiringtype') != "") {
					$feedback = request('custom_hiringtype');
					if($feedback=='Feedback Pending'){
						$interviewShedule = $interviewShedule->where(function ($query) {
							$query->whereNull('interview_feedback.id')  // Allow interviews without feedback
							->orWhere('interview_schedules.feedback', '=', null); 
						});
						$interviewShedule = $interviewShedule->where('interview_schedules.schedule_date', '<', now()->subHour());
						}
				}

				$interviewShedule = $interviewShedule->where(function($query) use($company_id){
					$query->where('jobs.company_id', '=', $company_id);
					$query->orWhere('add_job_wise_condidates.thirdparty_company_id', '=', $company_id);
				});
			

				// Check if the today filter is applied
				if ($valCheck_date == 1) {
					$interviewShedule->whereBetween('interview_schedules.schedule_date', [$startOfDay, $endOfDay]);
				}

			$searchTerms = request('custom_search');
	        $custom_search = $searchTerms; //json_decode($searchTerms, true);
    		// if (\request('custom_search') != "") {
    		    
    			    
			// 		    $interviewShedule = $interviewShedule->where(function($query) use ($custom_search,$company_id){
			// 		        $query->where('job_applications.full_name','LIKE',"%{$custom_search}%");
			// 		        $query->orWhere('job_applications.phone','LIKE',"%{$custom_search}%");
			// 		        $query->orWhere('job_applications.email','LIKE',"%{$custom_search}%");
			// 				$query->orWhere('jobs.title','like', "%{$custom_search}%");
			// 				$query->orWhere('countries.country_name','like', "%{$custom_search}%");
			// 				$query->orWhere('state_list.state','like', "%{$custom_search}%");
			// 				$query->orWhere('city_list.city','like', "%{$custom_search}%");
			// 				$query->orWhere('users.name','like', "%{$custom_search}%");
			// 				$query->orWhere('clients.name','like', "%{$custom_search}%");
			// 				$query->orWhere('companies.company_name','like', "%{$custom_search}%");
			// 		    });
			// 	}

			if (\request('custom_search') != "") {
				$search = request('custom_search');
				$interviewShedule = $interviewShedule->where(function ($query) use ($company_id, $search,$custom_search) {
					
						$query->where('recommendation_relation.to_company_id', '=', $company_id)
							->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)
							->orWhere('job_applications.full_name','LIKE',"%{$custom_search}%")
    			            ->orWhere('job_applications.phone','LIKE',"%{$custom_search}%")
    			            ->orWhere('job_applications.email','LIKE',"%{$custom_search}%")
							->orWhere('jobs.title','like', "%{$custom_search}%")
							->orWhere('countries.country_name','like', "%{$custom_search}%")
							->orWhere('state_list.state','like', "%{$custom_search}%")
							->orWhere('city_list.city','like', "%{$custom_search}%")
							->orWhere('users.name','like', "%{$custom_search}%")
							->orWhere('clients.name','like', "%{$custom_search}%")
							->orWhere('companies.company_name','like', "%{$custom_search}%");

				});
				if ($valCheck_date == 1) {
				$interviewShedule = $interviewShedule->where(function ($subQuery) use ($startOfDay, $endOfDay) {
						$subQuery->whereBetween('interview_schedules.schedule_date', [$startOfDay, $endOfDay]);
					});
				}
			} else {
				
				$interviewShedule = $interviewShedule->orWhere(function ($query) use ($company_id,$valCheck_date,$startOfDay,$endOfDay) {
					if ($valCheck_date == 1) {
					 return
						$query->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)
						->where(function ($subQuery) use ($startOfDay, $endOfDay) {
								$subQuery->whereBetween('interview_schedules.schedule_date', [$startOfDay, $endOfDay]);
							});
					}else{
							return
						    $query->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id);
					}						

				});

			}
		}



		} else {
            // dd("djdj");
			$interviewShedule = DB::table('interview_schedules')
				->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
				->join('add_job_wise_condidates', 'interview_schedules.interview_job_wise_con_id', 'add_job_wise_condidates.id')
				// ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
				->join('jobs', function ($join) use ($company_id) {
					$join->on('jobs.id', 'add_job_wise_condidates.job_id');
				})
				->join('companies', function ($join) use ($company_id) {
					$join->on('companies.id', 'jobs.company_id');
				})
				->join('users', 'users.id', 'add_job_wise_condidates.submitted_by')
				->leftjoin('countries', 'countries.id', 'job_applications.country')
				->leftjoin('state_list', 'state_list.id', 'job_applications.state')
				->leftjoin('city_list', 'city_list.city_id', 'job_applications.city_id')
				->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id')
				// ->join('jobs', 'jobs.id', 'job_applications.job_id')
				->join('job_assign', 'job_assign.job_id', 'jobs.id')
				->leftjoin('clients', 'clients.id', 'jobs.client_id')
				 // ->leftjoin('companies', 'companies.id', 'jobs.company_id')
				 ->leftjoin('interview_feedback', 'interview_feedback.interview_id', 'interview_schedules.id')
				->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')
				->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
				->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
				->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage')
				->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
				->leftJoin('updatetimeline','updatetimeline.interview_schedules_id','interview_schedules.id')                             // Append new code 
				->select(
					'interview_schedules.id',
					'job_applications.full_name',
					'interview_schedules.status',
					'clients.name as client_name',
					'interview_schedules.schedule_date',
					'interview_schedules.stage as master_status_id',
					'interview_schedules.substage as child_status_id',
					'interview_schedules.available_status',
					'interview_schedules.reschedule',
					'interview_schedules.feedback',
					'interview_schedules.interview_mode',
					'interview_schedules.interviewers',
					'interview_schedules.interview_type',
					'interview_schedules.schedule_from_time',
					'interview_schedules.mailstatus',
					'interview_schedules.company_id as real_company_id',
					'users.name as source',
					'users.name as posted_by',
					'users.image as posted_by_image',
					'interview_schedules.job_application_id',
					'master_status.name as master_status_name',
					'candidate_status.name as child_status_name',
					'jobs.title as job_title',
					'interview_schedules.interview_job_id as job_id',
					'companies.company_name',
					'job_applications.current_designation',
					'job_applications.thirdparty_company_id',
					'interview_mode.name as mode_name',
					'interview_mode_lavel.name as mode_lavel_name',
					'master_status.css_style',
					'interview_schedules.average_star_rating',
					'jobs.status as job_status',
					'recommendation_relation.id as recommendation_id',
					'recommendation_relation.from_company_id',
					'recommendation_relation.final_status',
					'recommendation_relation.to_company_id',
					'add_job_wise_condidates.thirdparty_company_id as ClientComID',
					'add_job_wise_condidates.job_id as jobId',
					'interview_schedules.schedule_to_time',
					'interview_schedules.timezone_abbreviation',
					'add_job_wise_condidates.id as wiseID',
					'uc.company_name as uc_company_name',
					'updatetimeline.interview_mention_link'
				);

			if(\request('custom_hiringtype') != "") {
				$feedback = request('custom_hiringtype');
				if($feedback=='Feedback Pending'){
					$interviewShedule = $interviewShedule->where(function ($query) {
						$query->whereNull('interview_feedback.id')  // Allow interviews without feedback
						->orWhere('interview_schedules.feedback', '=', null); 
					});
					$interviewShedule = $interviewShedule->where('interview_schedules.schedule_date', '<', now()->subHour());
					}
			}
			
			$interviewShedule = $interviewShedule->where('jobs.company_id', '=', $company_id)->where('job_assign.user_id', $this->user->id);
			if (\request('profile') != "") {
				$interviewShedule = $interviewShedule->where('job_applications.submitted_by', $this->user->id);
				$interviewShedule = $interviewShedule->where('add_job_wise_condidates.submitted_by', $this->user->id);
			  }

			if ($valCheck_date == 1) {
				$interviewShedule->whereBetween('interview_schedules.schedule_date', [$startOfDay, $endOfDay]);
			}

			$searchTerms = request('custom_search');
	        $custom_search =$searchTerms;//json_decode($searchTerms, true);
    		if (\request('custom_search') != "") {
    		    
    			    
    			    $interviewShedule = $interviewShedule->where(function($query) use ($custom_search,$company_id){
    			        $query->orWhere('job_applications.full_name','LIKE',"%{$custom_search}%");
    			        $query->orWhere('job_applications.phone','LIKE',"%{$custom_search}%");
    			        $query->orWhere('job_applications.email','LIKE',"%{$custom_search}%");
						$query->orWhere('jobs.title','like', "%{$custom_search}%");
						$query->orWhere('countries.country_name','like', "%{$custom_search}%");
						$query->orWhere('state_list.state','like', "%{$custom_search}%");
						$query->orWhere('city_list.city','like', "%{$custom_search}%");
						$query->orWhere('users.name','like', "%{$custom_search}%");
						$query->orWhere('clients.name','like', "%{$custom_search}%");
						$query->orWhere('companies.company_name','like', "%{$custom_search}%");
    			    });

				if ($valCheck_date == 1) {
					$interviewShedule = $interviewShedule->where(function ($subQuery) use ($startOfDay, $endOfDay) {
							$subQuery->whereBetween('interview_schedules.schedule_date', [$startOfDay, $endOfDay]);
						});
					}
    	    }

			if (\request('custom_search') != "") {
				$search = request('custom_search');
				$interviewShedule = $interviewShedule->orWhere(function ($query) use ($company_id,$custom_search, $search) {

					return
						$query->where('recommendation_relation.to_company_id', '=', $company_id)
							->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)
							->where('job_assign.user_id', $this->user->id);
				});

			} else {
				$interviewShedule = $interviewShedule->orWhere(function ($query) use ($company_id,$valCheck_date,$startOfDay, $endOfDay) {

					if ($valCheck_date == 1) {
						return
						   $query->where('job_assign.user_id', $this->user->id)
						   ->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)
						   ->where(function ($subQuery) use ($startOfDay, $endOfDay) {
								   $subQuery->whereBetween('interview_schedules.schedule_date', [$startOfDay, $endOfDay]);
							   });
					   }else{
							   return
								$query->where('job_assign.user_id', $this->user->id)
									->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id);
					   }
				});
			}

		}
		
		if (\request('limitget') != "") {
			$limit = (int) request('limitget');
			$interviewShedule->limit($limit);
		}
		
		if (\request('custom_hiringtype') != "") {
		    $valCheck = request('custom_hiringtype');
		    if($valCheck==2){
		        $interviewShedule = $interviewShedule->orderBy('interview_schedules.created_at', 'ASC');
		    }elseif($valCheck==3){
		        $interviewShedule = $interviewShedule->orderBy('interview_schedules.schedule_date', 'DESC');
		    }elseif($valCheck==4){
		        $interviewShedule = $interviewShedule->orderBy('interview_schedules.schedule_date', 'ASC');
		    }elseif($valCheck==5){
		        $interviewShedule = $interviewShedule->orderBy('job_applications.np_id', 'ASC');
		    }elseif($valCheck==6){
		        $interviewShedule = $interviewShedule->orderBy('job_applications.np_id', 'DESC');
		    }elseif($valCheck==9){
		        $interviewShedule = $interviewShedule->orderByRaw("FIELD(job_applications.np_id, 'Immediate') DESC")
                    ->orderBy('job_applications.np_id', 'DESC')
                    ->get();
		    }elseif($valCheck==7){
		        $interviewShedule = $interviewShedule->orderBy('job_applications.total_exp', 'DESC');
		    }elseif($valCheck==8){
		        $interviewShedule = $interviewShedule->orderBy('job_applications.total_exp', 'ASC');
		    }else{
				$interviewShedule->orderBy('interview_schedules.schedule_date', 'DESC');
			}
			   
		 }else{
		    $interviewShedule->orderBy('interview_schedules.schedule_date', 'DESC');
    	}
    	
		
		// $interviewShedule->orderBy('interview_schedules.schedule_date', 'DESC');
		
		$interviewShedule->groupBy('interview_schedules.id');


		$perPage = 10;
		$page = $request->get('page', 1);
		// dd($page);
		$total = count($interviewShedule->get());
		// dd($total);
		$data = $interviewShedule->skip(($page - 1) * $perPage)->take($perPage)->get();

		$pagination = [
			'total' => $total,
			'current_page' => $page,
			'last_page' => ceil($total / $perPage),
			'listType' => 'allcandidate',
		];
		// dd($pagination);
		$userPermissions = $this->userPermissions;
		return response()->json([
			'html' => view('service_company.ajax_pages.interview-tracker-ajax', compact('data','userPermissions','feedback_status','available_status','company_id'))->render(),
			'total' => $total,
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		]);
	}

	public function benchinterviewtable(Request $request)
	{
		$chkAdminRole = $this->user->hasRole('admin');
		if (!$chkAdminRole) {
			$this->candidates = JobApplication::where('submitted_by', '=', $this->user->id)->get();
		} else {
			$this->candidates = JobApplication::all();
		}
		return view('service_company.bench-candidates.interview-schedule', $this->data);
	}

	public function benchinterviewdata_ajax(Request $request)
	{
        // dd("hidid");
		abort_if(Gate::denies('view_schedule'), 403);

		$company_id = auth()->user()->company_id;
		$available_status = $feedback_status = $interview_mode_arr = '';

		$available_status = GeneralMethod::available_status();
		$feedback_status = GeneralMethod::feedback_status();
		$interview_mode_arr = GeneralMethod::interview_mode();
		$job_status_arr = GeneralMethod::job_status();

		$startOfDay = Carbon::now()->startOfDay()->toDateTimeString();
		$endOfDay = Carbon::now()->endOfDay()->toDateTimeString();
		$valCheck_date = request('sortingfilter');

		$chkAdminRole = $this->user->hasRole('admin');


		$searchTerm = trim(request('custom_search') ?? $request->input('q'));
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;

		$cachePayload = [
			'page' => $page,
			'size' => $size,
			'searchTerm' => $searchTerm,
			'company' => $company_id,
			'user_id' => $this->user->id,
			'valCheck_date' => $valCheck_date,
			'country' =>request('country')
		];

		$versionKey = 'allinterviewbenchcandidates_version_user_'.$this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:allinterviewbenchcandidates:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
		/* ----------------------------
		⚡ Check DragonflyDB first
		----------------------------- */
		if (cache()->has($cacheKeySet)) {
			return response()->json(cache()->get($cacheKeySet));
		}



		$must = [];
		$should = [];
		$filter = [];

        $valCheck = request('custom_hiringtype');
		switch ($valCheck) {
			case 1:
				$sort[] = ['id' => ['order' => 'desc']];
				break;
			case 2:
				$sort[] = ['created_at' => ['order' => 'asc']];
				break;
			case 3:
				$sort[] = ['schedule_date' => ['order' => 'desc']];
				break;
			case 4:
				$sort[] = ['schedule_date' => ['order' => 'asc']];
				break;
			default:
				$sort[] = ['id' => ['order' => 'desc']];
		}

		// 2. Feedback pending first (nulls first)
		
		if($valCheck=='Feedback Pending'){
			       // 1. Sort by feedback pending (feedback is null)
			array_unshift($sort, [
				'_script' => [
					'type' => 'number',
					'script' => [
						'source' => "doc['feedback'].size() == 0 ? 0 : 1",
						'lang' => 'painless'
					],
					'order' => 'asc'
				]
			]);
			$filter[] = [
				'range' => [
					'schedule_date' => [
						'lt' => now()->subHour()->toIso8601String(),
						'format' => 'strict_date_optional_time'
					]
				]
			];
		}

		// Custom search logic
		if (!empty($searchTerm)) {
			$must[] = [
				'bool' => [
					'must' => [
						[
							'bool' => [
								'should' => [
									['term' => ['phone' => $searchTerm]],
									['term' => ['email' => $searchTerm]],
									['match_phrase_prefix' => ['job_title' => $searchTerm]],
									['match_phrase_prefix' => ['client_name' => $searchTerm]],
									['match_phrase_prefix' => ['full_name' => $searchTerm]],
									['wildcard' => ['city_name' => strtolower($searchTerm) . '*']],
									['wildcard' => ['country_name' => strtolower($searchTerm) . '*']],
									['wildcard' => ['state_name' => strtolower($searchTerm) . '*']],
									['wildcard' => ['posted_by' => strtolower($searchTerm) . '*']],
									['match_phrase_prefix' => ['company_name' => $searchTerm]],
								],
								'minimum_should_match' => 1,
							]
						]
					]
				]
			];
		}

		$must[] = [
			'bool' => [
				'should' => [
					// Case 1: Direct access with bench_condidate
					[
						'bool' => [
							'must' => [
								['term' => ['company_id' => $company_id]],
								['term' => ['candidate_type' => 'bench_condidate']],
							],
						]
					],
		
					// Case 2: recommendation_relation.from_company_id + ClientComID match
					[
						'bool' => [
							'must' => [
								['term' => ['recommendation_relation.from_company_id' => $company_id]],
								['term' => ['ClientComID' => $company_id]],
							],
						]
					],
				],
				'minimum_should_match' => 1,
			]
		];

		
		
		// 5. RESTRICT TO ASSIGNED USERS (if not admin)
		if (!$chkAdminRole > 0) {
			$must[] = [
				'nested' => [
					'path' => 'job_assign',
					'query' => [
						'term' => ['job_assign.user_id' => auth()->user()->id],
					],
				],
			];
		}

		if ($valCheck_date == 1) {
			$filter[] = [
				'range' => [
					'schedule_date' => [
						'gte' =>  Carbon::parse($startOfDay)->startOfDay()->toIso8601String(),
						'lte' =>  Carbon::parse($endOfDay)->endOfDay()->toIso8601String(),
						'format' => 'strict_date_optional_time' // optional based on your index mapping
					]
				]
			];
		}

		// FINAL ELASTIC PARAMS
		$params = [
			'index' => ELASTIC_INERTVIEW_TRACKER,
			'request_cache' => true,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => $sort,
				'query' => [
					'bool' => [
						'must' => $must,
						'filter' => $filter,
					],
				],
			],
		];

		$response = $this->elasticService->getClient()->search($params);
		if (!$this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
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
			'html' => view('service_company.ajax_pages.bench-candidate-interview-schedule-ajax', compact('data','userPermissions','company_id','available_status'))->render(),
			'total' => $total,
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		];

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_INTERVIEW_BENCH_CANDIDATES_TIME));

	      return response()->json($responseData);

	



		if (($chkAdminRole > 0) && (in_array("all_requisition", $this->userPermissions) && in_array("view_jobs", $this->userPermissions))) {
       		 // dd("ki");
			$interviewShedule = DB::table('interview_schedules')
				->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
				->join('add_job_wise_condidates', 'interview_schedules.interview_job_wise_con_id', 'add_job_wise_condidates.id')
				->join('jobs', function ($join) use ($company_id) {
					$join->on('jobs.id', 'add_job_wise_condidates.job_id');
				})
				->join('companies', function ($join) use ($company_id) {
					$join->on('companies.id', 'jobs.company_id');
				})
				->leftjoin('users', 'users.id', 'job_applications.submitted_by')
				->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id')
				->leftjoin('countries', 'countries.id', 'job_applications.country')
				->leftjoin('state_list', 'state_list.id', 'job_applications.state')
				->leftjoin('city_list', 'city_list.city_id', 'job_applications.city_id')
				// ->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
				// ->leftjoin('companies', 'companies.id', 'jobs.company_id')
				->leftjoin('clients', 'clients.id', 'jobs.client_id')
				->leftjoin('interview_feedback', 'interview_feedback.interview_id', 'interview_schedules.id')
				->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')
				->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
				->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
				->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage')
				->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
				->leftJoin('updatetimeline','updatetimeline.interview_schedules_id','interview_schedules.id') // Append new code 
				->select(
					'interview_schedules.id',
					'job_applications.full_name',
					'interview_schedules.status',
					'interview_schedules.schedule_date',
					'interview_schedules.available_status',
					'interview_schedules.reschedule',
					'interview_schedules.stage as master_status_id',
					'interview_schedules.substage as child_status_id',
					'interview_schedules.feedback',
					'interview_schedules.interview_mode',
					'interview_schedules.timezone_abbreviation',
					'interview_schedules.schedule_to_time',
					'interview_schedules.interviewers',
					'interview_schedules.interview_type',
					'interview_schedules.schedule_from_time',
					'interview_schedules.company_id as real_company_id',
					'users.name as source',
					'users.name as posted_by',
					'users.image as posted_by_image',
					'interview_schedules.job_application_id',
					'master_status.name as master_status_name',
					'candidate_status.name as child_status_name',
					'jobs.title as job_title',
					'interview_schedules.interview_job_id as job_id',
					'companies.company_name',
					'add_job_wise_condidates.thirdparty_company_id as ClientComID',
					'add_job_wise_condidates.job_id as jobId',
					'interview_schedules.schedule_to_time',
					'interview_schedules.timezone_abbreviation',
					'job_applications.current_designation',
					'job_applications.thirdparty_company_id',
					'interview_mode.name as mode_name',
					'interview_mode_lavel.name as mode_lavel_name',
					'master_status.css_style',
					'interview_schedules.average_star_rating',
					'jobs.status as job_status',
					'recommendation_relation.id as recommendation_id',
					'recommendation_relation.from_company_id',
					'recommendation_relation.final_status',
					'recommendation_relation.to_company_id',
					'add_job_wise_condidates.id as wiseID',
					'uc.company_name as uc_company_name',
					'updatetimeline.interview_mention_link'
				);

			if(\request('sortingfilter') != "") {
				$feedback = request('sortingfilter');
				if($feedback=='Feedback Pending'){
					$interviewShedule = $interviewShedule->where(function ($query) {
						$query->whereNull('interview_feedback.id')  // Allow interviews without feedback
						->orWhere('interview_schedules.feedback', '=', null); 
					});
					$interviewShedule = $interviewShedule->where('interview_schedules.schedule_date', '<', now()->subHour());
					}
			}

			$interviewShedule = $interviewShedule->where('job_applications.company_id', '=', $company_id);
			$interviewShedule = $interviewShedule->where('job_applications.condidate_type', '=', 'bench_condidate');
			if ($valCheck_date == 1) {
				$interviewShedule->whereBetween('interview_schedules.schedule_date', [$startOfDay, $endOfDay]);
			}
			
			$searchTerms = request('cus_search');
	        $custom_search = $searchTerms; //json_decode($searchTerms, true);
    		if (\request('cus_search') != "") {
    		    
    			    
    			    $interviewShedule = $interviewShedule->where(function($query) use ($custom_search,$company_id){
    			        $query->orWhere('job_applications.full_name', 'LIKE',"%{$custom_search}%");
    			        $query->orWhere('job_applications.phone', 'LIKE',"%{$custom_search}%");
    			        $query->orWhere('job_applications.email', 'LIKE',"%{$custom_search}%");
						$query->orWhere('jobs.title','like', "%{$custom_search}%");
						$query->orWhere('countries.country_name','like', "%{$custom_search}%");
						$query->orWhere('state_list.state','like', "%{$custom_search}%");
						$query->orWhere('city_list.city','like', "%{$custom_search}%");
						$query->orWhere('users.name','like', "%{$custom_search}%");
						$query->orWhere('clients.name','like', "%{$custom_search}%");
						$query->orWhere('companies.company_name','like', "%{$custom_search}%");
    			    });
    	    }

			if (\request('cus_search') != "") {
				$interviewShedule = $interviewShedule->orWhere(function ($query) use ($company_id,$custom_search) {

					return
						$query->where('recommendation_relation.from_company_id', '=', $company_id)
							->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id);
				});
				if ($valCheck_date == 1) {
					$interviewShedule = $interviewShedule->where(function ($subQuery) use ($startOfDay, $endOfDay) {
							$subQuery->whereBetween('interview_schedules.schedule_date', [$startOfDay, $endOfDay]);
						});
					}

			}
			

// 			if (\request('search') != "") {
// 				$search = request('search');
// 				$interviewShedule = $interviewShedule->where('job_applications.full_name', 'LIKE', "%$search%");
// 			}

// 			if (\request('search') != "") {
// 				$search = request('search');
// 				$interviewShedule = $interviewShedule->orWhere(function ($query) use ($company_id, $search) {
// 					return
// 						$query->where('recommendation_relation.from_company_id', '=', $company_id)
// 							->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)
// 							->where('job_applications.full_name', 'LIKE', "%$search%");

// 				});
// 			}

		} else {

			$interviewShedule = DB::table('interview_schedules')
				->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
				->join('add_job_wise_condidates', 'interview_schedules.interview_job_wise_con_id', 'add_job_wise_condidates.id')
				// ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
				->join('jobs', function ($join) use ($company_id) {
					$join->on('jobs.id', 'add_job_wise_condidates.job_id');
				})
				->join('companies', function ($join) use ($company_id) {
					$join->on('companies.id', 'jobs.company_id');
				})
				->join('users', 'users.id', 'job_applications.submitted_by')
				->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id')
				// ->join('jobs', 'jobs.id', 'job_applications.job_id')
				->join('job_assign', 'job_assign.job_id', 'jobs.id')
				->leftjoin('countries', 'countries.id', 'job_applications.country')
				->leftjoin('state_list', 'state_list.id', 'job_applications.state')
				->leftjoin('city_list', 'city_list.city_id', 'job_applications.city_id')
				// ->leftjoin('companies', 'companies.id', 'jobs.company_id')
				->leftjoin('clients', 'clients.id', 'jobs.client_id')
				->leftjoin('interview_feedback', 'interview_feedback.interview_id', 'interview_schedules.id')
				->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')
				->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
				->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
				->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage')
				->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')    
				->leftJoin('updatetimeline','updatetimeline.interview_schedules_id','interview_schedules.id') // Append new code 
				->select(
					'interview_schedules.id',
					'job_applications.full_name',
					'interview_schedules.status',
					'interview_schedules.schedule_date',
					'interview_schedules.available_status',
					'interview_schedules.reschedule',
					'interview_schedules.feedback',
					'interview_schedules.interview_mode',
					'interview_schedules.interviewers',
					'interview_schedules.interview_type',
					'interview_schedules.schedule_from_time',
					'interview_schedules.company_id as real_company_id',
					'users.name as source',
					'users.name as posted_by',
					'users.image as posted_by_image',
					'interview_schedules.job_application_id',
					'master_status.name as master_status_name',
					'candidate_status.name as child_status_name',
					'jobs.title as job_title',
					'jobs.company_id as companyIdCheck',
					'interview_schedules.interview_job_id as job_id',
					'companies.company_name',
					'add_job_wise_condidates.thirdparty_company_id as ClientComID',
					'add_job_wise_condidates.job_id as jobId',
					'interview_schedules.schedule_to_time',
					'interview_schedules.timezone_abbreviation',
					'job_applications.current_designation',
					'job_applications.thirdparty_company_id',
					'interview_mode.name as mode_name',
					'interview_mode_lavel.name as mode_lavel_name',
					'master_status.css_style',
					'interview_schedules.average_star_rating',
					'jobs.status as job_status',
					'recommendation_relation.id as recommendation_id',
					'recommendation_relation.from_company_id',
					'recommendation_relation.final_status',
					'recommendation_relation.to_company_id',
					'add_job_wise_condidates.id as wiseID',
					'uc.company_name as uc_company_name',
					'updatetimeline.interview_mention_link'
				);

			if(\request('sortingfilter') != "") {
				$feedback = request('sortingfilter');
				if($feedback=='Feedback Pending'){
					$interviewShedule = $interviewShedule->where(function ($query) {
						$query->whereNull('interview_feedback.id')  // Allow interviews without feedback
						->orWhere('interview_schedules.feedback', '=', null); 
					});
					$interviewShedule = $interviewShedule->where('interview_schedules.schedule_date', '<', now()->subHour());
					}
			}

			// $interviewShedule = $interviewShedule->where('jobs.company_id', '=', $company_id)->where('job_assign.user_id', $this->user->id);
			$interviewShedule = $interviewShedule->where('job_applications.condidate_type', '=', 'bench_condidate');
            // 			if (\request('search') != "") {
            // 				$search = request('search');
            // 				$interviewShedule = $interviewShedule->where('job_applications.full_name', 'LIKE', "%$search%")->where('job_assign.user_id', $this->user->id);
            // 			}
			if ($valCheck_date == 1) {
				$interviewShedule->whereBetween('interview_schedules.schedule_date', [$startOfDay, $endOfDay]);
			}
           $searchTerms = request('cus_search');
		   $custom_search =  $searchTerms;//json_decode($searchTerms, true);
    		if (\request('cus_search') != "") {
    			    
    			    $interviewShedule = $interviewShedule->where(function($query) use ($custom_search,$company_id){
    			        $query->where('job_assign.user_id', $this->user->id);
    			        $query->orWhere('job_applications.full_name','LIKE',"%{$custom_search}%");
    			        $query->orWhere('job_applications.phone', 'LIKE',"%{$custom_search}%");
    			        $query->orWhere('job_applications.email', 'LIKE',"%{$custom_search}%");
						$query->orWhere('jobs.title','like', "%{$custom_search}%");
						$query->orWhere('countries.country_name','like', "%{$custom_search}%");
						$query->orWhere('state_list.state','like', "%{$custom_search}%");
						$query->orWhere('city_list.city','like', "%{$custom_search}%");
						$query->orWhere('users.name','like', "%{$custom_search}%");
						$query->orWhere('clients.name','like', "%{$custom_search}%");
						$query->orWhere('companies.company_name','like', "%{$custom_search}%");
    			    });
    	    }

			if (\request('cus_search') != "") {
				$interviewShedule = $interviewShedule->orWhere(function ($query) use ($company_id,$custom_search) {

					return
						$query->where('recommendation_relation.from_company_id', '=', $company_id)
							->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)
							->where('job_assign.user_id', $this->user->id);
				});
				if ($valCheck_date == 1) {
					$interviewShedule = $interviewShedule->where(function ($subQuery) use ($startOfDay, $endOfDay) {
							$subQuery->whereBetween('interview_schedules.schedule_date', [$startOfDay, $endOfDay]);
						});
					}

			} 

		}
		// $interviewShedule = $interviewShedule->where('job_applications.submitted_by', '=', auth()->user()->id);
		$interviewShedule = $interviewShedule->where('job_applications.company_id', '=', auth()->user()->company_id);
		$interviewShedule->groupBy('interview_schedules.id');
		
		
		if (\request('sortingfilter') != "") {
		    $valCheck = request('sortingfilter');
		    if($valCheck==1){
		        $interviewShedule = $interviewShedule->orderBy('interview_schedules.created_at', 'DESC');
		    }elseif($valCheck==2){
		        $interviewShedule = $interviewShedule->orderBy('interview_schedules.created_at', 'ASC');
		    }elseif($valCheck==3){
		        $interviewShedule = $interviewShedule->orderBy('interview_schedules.schedule_date', 'ASC');
		    }elseif($valCheck==4){
		        $interviewShedule = $interviewShedule->orderBy('interview_schedules.schedule_date', 'DESC');
		    }elseif($valCheck==5){
		        $interviewShedule = $interviewShedule->orderBy('job_applications.np_id', 'ASC');
		    }elseif($valCheck==6){
		        $interviewShedule = $interviewShedule->orderBy('job_applications.np_id', 'DESC');
		    }elseif($valCheck==9){
		        $interviewShedule = $interviewShedule->orderByRaw("FIELD(job_applications.np_id, 'Immediate') DESC")
                    ->orderBy('job_applications.np_id', 'DESC')
                    ->get();
		    }elseif($valCheck==7){
		        $interviewShedule = $interviewShedule->orderBy('job_applications.total_exp', 'DESC');
		    }elseif($valCheck==8){
		        $interviewShedule = $interviewShedule->orderBy('job_applications.total_exp', 'ASC');
		    }else{
				$interviewShedule->orderBy('interview_schedules.schedule_date', 'DESC');
			}
			   
		 }else{
		    $interviewShedule->orderBy('interview_schedules.schedule_date', 'DESC');
    	}
    	
    	
		$perPage = 10;
		$page = $request->get('page', 1);
		// dd($page);
		$total = count($interviewShedule->get());
		// dd($total);
		$data = $interviewShedule->skip(($page - 1) * $perPage)->take($perPage)->get();

		$pagination = [
			'total' => $total,
			'current_page' => $page,
			'last_page' => ceil($total / $perPage),
			'listType' => 'allcandidate',
		];
		// dd($pagination);
		$userPermissions = $this->userPermissions;
		return response()->json([
			'html' => view('service_company.ajax_pages.bench-candidate-interview-schedule-ajax', compact('data','userPermissions','company_id','available_status'))->render(),
			'total' => $total,
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		]);

	}
	public function benchinterviewdata(Request $request)
	{
       // dd("hidid");
		abort_if(Gate::denies('view_schedule'), 403);

		$company_id = auth()->user()->company_id;
		$available_status = $feedback_status = $interview_mode_arr = '';

		$available_status = GeneralMethod::available_status();
		$feedback_status = GeneralMethod::feedback_status();
		$interview_mode_arr = GeneralMethod::interview_mode();
		$job_status_arr = GeneralMethod::job_status();

		$chkAdminRole = $this->user->hasRole('admin');

		if (($chkAdminRole > 0) && (in_array("all_requisition", $this->userPermissions) && in_array("view_jobs", $this->userPermissions))) {
       		 // dd("ki");
			$interviewShedule = DB::table('interview_schedules')
				->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
				->join('add_job_wise_condidates', 'interview_schedules.interview_job_wise_con_id', 'add_job_wise_condidates.id')
				->join('jobs', function ($join) use ($company_id) {
					$join->on('jobs.id', 'add_job_wise_condidates.job_id');
				})
				->join('companies', function ($join) use ($company_id) {
					$join->on('companies.id', 'jobs.company_id');
				})
				->leftjoin('users', 'users.id', 'job_applications.submitted_by')
				->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id')
				// ->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
				// ->leftjoin('companies', 'companies.id', 'jobs.company_id')
				->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')
				->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
				->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
				->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage')
				->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
				->leftJoin('updatetimeline','updatetimeline.interview_schedules_id','interview_schedules.id') // Append new code 
				->select(
					'interview_schedules.id',
					'job_applications.full_name',
					'interview_schedules.status',
					'interview_schedules.schedule_date',
					'interview_schedules.available_status',
					'interview_schedules.reschedule',
					'interview_schedules.feedback',
					'interview_schedules.interview_mode',
					'interview_schedules.interviewers',
					'interview_schedules.interview_type',
					'interview_schedules.schedule_from_time',
					'interview_schedules.company_id as real_company_id',
					'users.name as source',
					'users.name as posted_by',
					'users.image as posted_by_image',
					'interview_schedules.job_application_id',
					'master_status.name as master_status_name',
					'candidate_status.name as child_status_name',
					'jobs.title as job_title',
					'interview_schedules.interview_job_id as job_id',
					'companies.company_name',
					'job_applications.current_designation',
					'job_applications.thirdparty_company_id',
					'interview_mode.name as mode_name',
					'interview_mode_lavel.name as mode_lavel_name',
					'master_status.css_style',
					'interview_schedules.average_star_rating',
					'jobs.status as job_status',
					'recommendation_relation.id as recommendation_id',
					'recommendation_relation.from_company_id',
					'recommendation_relation.final_status',
					'recommendation_relation.to_company_id',
					'add_job_wise_condidates.id as wiseID',
					'uc.company_name as uc_company_name',
					'updatetimeline.interview_mention_link'
				);

			$interviewShedule = $interviewShedule->where('job_applications.company_id', '=', $company_id);
			$interviewShedule = $interviewShedule->where('job_applications.condidate_type', '=', 'bench_condidate');
			
			$searchTerms = request('cus_search');
	        $custom_search = json_decode($searchTerms, true);
    		if (\request('cus_search') != "") {
    		    
    			    
    			    $interviewShedule = $interviewShedule->where(function($query) use ($custom_search,$company_id){
    			        $query->whereIn('job_applications.full_name', $custom_search);
    			        $query->orWhereIn('job_applications.phone', $custom_search);
    			        $query->orWhereIn('job_applications.email', $custom_search);
    			    });
    	    }

			if (\request('cus_search') != "") {
				$interviewShedule = $interviewShedule->orWhere(function ($query) use ($company_id,$custom_search) {

					return
						$query->where('recommendation_relation.from_company_id', '=', $company_id)
							->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)
							->whereIn('job_applications.full_name', $custom_search)
    			            ->orWhereIn('job_applications.phone', $custom_search)
    			            ->orWhereIn('job_applications.email', $custom_search);
				});

			}
			

// 			if (\request('search') != "") {
// 				$search = request('search');
// 				$interviewShedule = $interviewShedule->where('job_applications.full_name', 'LIKE', "%$search%");
// 			}

// 			if (\request('search') != "") {
// 				$search = request('search');
// 				$interviewShedule = $interviewShedule->orWhere(function ($query) use ($company_id, $search) {
// 					return
// 						$query->where('recommendation_relation.from_company_id', '=', $company_id)
// 							->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)
// 							->where('job_applications.full_name', 'LIKE', "%$search%");

// 				});
// 			}

		} else {

			$interviewShedule = DB::table('interview_schedules')
				->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
				->join('add_job_wise_condidates', 'interview_schedules.interview_job_wise_con_id', 'add_job_wise_condidates.id')
				// ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
				->join('jobs', function ($join) use ($company_id) {
					$join->on('jobs.id', 'add_job_wise_condidates.job_id');
				})
				->join('companies', function ($join) use ($company_id) {
					$join->on('companies.id', 'jobs.company_id');
				})
				->join('users', 'users.id', 'job_applications.submitted_by')
				->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id')
				// ->join('jobs', 'jobs.id', 'job_applications.job_id')
				->join('job_assign', 'job_assign.job_id', 'jobs.id')
				// ->leftjoin('companies', 'companies.id', 'jobs.company_id')
				->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')
				->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
				->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
				->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage')
				->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')    
				->leftJoin('updatetimeline','updatetimeline.interview_schedules_id','interview_schedules.id') // Append new code 
				->select(
					'interview_schedules.id',
					'job_applications.full_name',
					'interview_schedules.status',
					'interview_schedules.schedule_date',
					'interview_schedules.available_status',
					'interview_schedules.reschedule',
					'interview_schedules.feedback',
					'interview_schedules.interview_mode',
					'interview_schedules.interviewers',
					'interview_schedules.interview_type',
					'interview_schedules.schedule_from_time',
					'interview_schedules.company_id as real_company_id',
					'users.name as source',
					'users.name as posted_by',
					'users.image as posted_by_image',
					'interview_schedules.job_application_id',
					'master_status.name as master_status_name',
					'candidate_status.name as child_status_name',
					'jobs.title as job_title',
					'jobs.company_id as companyIdCheck',
					'interview_schedules.interview_job_id as job_id',
					'companies.company_name',
					'job_applications.current_designation',
					'job_applications.thirdparty_company_id',
					'interview_mode.name as mode_name',
					'interview_mode_lavel.name as mode_lavel_name',
					'master_status.css_style',
					'interview_schedules.average_star_rating',
					'jobs.status as job_status',
					'recommendation_relation.id as recommendation_id',
					'recommendation_relation.from_company_id',
					'recommendation_relation.final_status',
					'recommendation_relation.to_company_id',
					'add_job_wise_condidates.id as wiseID',
					'uc.company_name as uc_company_name',
					'updatetimeline.interview_mention_link'
				);

			// $interviewShedule = $interviewShedule->where('jobs.company_id', '=', $company_id)->where('job_assign.user_id', $this->user->id);
			$interviewShedule = $interviewShedule->where('job_applications.condidate_type', '=', 'bench_condidate');
            // 			if (\request('search') != "") {
            // 				$search = request('search');
            // 				$interviewShedule = $interviewShedule->where('job_applications.full_name', 'LIKE', "%$search%")->where('job_assign.user_id', $this->user->id);
            // 			}
           $searchTerms = request('cus_search');
	      $custom_search = json_decode($searchTerms, true);
    		if (\request('cus_search') != "") {
    			    
    			    $interviewShedule = $interviewShedule->where(function($query) use ($custom_search,$company_id){
    			        $query->whereIn('job_applications.full_name', $custom_search);
    			        $query->orWhereIn('job_applications.phone', $custom_search);
    			        $query->orWhereIn('job_applications.email', $custom_search);
    			        $query->where('job_assign.user_id', $this->user->id);
    			    });
    	    }

			if (\request('cus_search') != "") {
				$interviewShedule = $interviewShedule->orWhere(function ($query) use ($company_id,$custom_search) {

					return
						$query->where('recommendation_relation.from_company_id', '=', $company_id)
							->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)
							->where('job_assign.user_id', $this->user->id)
							->whereIn('job_applications.full_name', $custom_search)
    			            ->orWhereIn('job_applications.phone', $custom_search)
    			            ->orWhereIn('job_applications.email', $custom_search);
				});

			} 

		}
		// $interviewShedule = $interviewShedule->where('job_applications.submitted_by', '=', auth()->user()->id);
		$interviewShedule = $interviewShedule->where('job_applications.company_id', '=', auth()->user()->company_id);
		$interviewShedule->groupBy('interview_schedules.id');
		
		
		if (\request('sortingfilter') != "") {
		    $valCheck = request('sortingfilter');
		    if($valCheck==1){
		        $interviewShedule = $interviewShedule->orderBy('interview_schedules.created_at', 'DESC');
		    }elseif($valCheck==2){
		        $interviewShedule = $interviewShedule->orderBy('interview_schedules.created_at', 'ASC');
		    }elseif($valCheck==3){
		        $interviewShedule = $interviewShedule->orderBy('interview_schedules.schedule_date', 'ASC');
		    }elseif($valCheck==4){
		        $interviewShedule = $interviewShedule->orderBy('interview_schedules.schedule_date', 'DESC');
		    }elseif($valCheck==5){
		        $interviewShedule = $interviewShedule->orderBy('job_applications.np_id', 'ASC');
		    }elseif($valCheck==6){
		        $interviewShedule = $interviewShedule->orderBy('job_applications.np_id', 'DESC');
		    }elseif($valCheck==9){
		        $interviewShedule = $interviewShedule->orderByRaw("FIELD(job_applications.np_id, 'Immediate') DESC")
                    ->orderBy('job_applications.np_id', 'DESC')
                    ->get();
		    }elseif($valCheck==7){
		        $interviewShedule = $interviewShedule->orderBy('job_applications.total_exp', 'DESC');
		    }elseif($valCheck==8){
		        $interviewShedule = $interviewShedule->orderBy('job_applications.total_exp', 'ASC');
		    }
			   
		 }else{
		    $interviewShedule->orderBy('interview_schedules.schedule_date', 'DESC');
    	}
    	
    	
		
		$interviewShedule->get();
		return DataTables::of($interviewShedule)

			->editColumn('chkbox', function ($row) {
				return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
			})
			->editColumn('Name', function ($row) use ($company_id) {

				$bgTextDecore = $strVal = '';
				$str = trim(@$row->full_name);
				$str = $strVal = strtolower($str[0]);
				$str = ord($str);
				if ($str >= 97 && $str <= 101) {
					$bgTextDecore = 'fs-3 bg-light-danger text-danger';
				} else if ($str >= 102 && $str <= 106) {
					$bgTextDecore = 'fs-3 bg-light-warning text-warning';
				} else if ($str >= 107 && $str <= 111) {
					$bgTextDecore = 'fs-3 bg-light-primary text-primary';
				} else if ($str >= 112 && $str <= 116) {
					$bgTextDecore = 'fs-3 bg-light-success text-success';
				} else if ($str >= 117 && $str <= 121) {
					$bgTextDecore = 'fs-3 bg-dark text-secondary';
				} else {
					$bgTextDecore = 'fs-3 bg-light-secondary text-dark';
				}

				$profile_detail = 'show-application-detail';
				if ($row->thirdparty_company_id == $company_id) {
					$profile_detail = 'client-show-application-detail';
				}

				return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="' . $row->wiseID . '" class="' . $profile_detail . '">
                      <div data-row-id="' . $row->wiseID . '"  class="symbol-label ' . $bgTextDecore . ' border-radius-5 ' . $profile_detail . '">' . ucwords($strVal) . '</div>
                    </a>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="' . $row->wiseID . '" class="text-dark fw-bolder text-hover-primary fs-6 ' . $profile_detail . '" data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">' . ucwords(substr(@$row->full_name, 0, 20)) . '</a>
                    <a href="javascript:void(0);" data-row-id="' . $row->wiseID . '" class="fw-bold text-muted d-block fs-7 ' . $profile_detail . ' text-hover-primary" data-toggle="tooltip" title="' . ucwords(@$row->current_designation) . '">' . ucwords(substr(@$row->current_designation, 0, 20)) . '</a>
                  </div>';
			})
			->editColumn('Requisitions', function ($row) use ($company_id) {

				$icon = '&nbsp;<i class="fas fa-hand-scissors" style="color:#00CC00"></i>';
				if ($row->thirdparty_company_id == $company_id) {
					$icon = '&nbsp;<i class="fas fa-link" style="color: #0000CC"></i></a>';
				}

				$client_name = '';
				$client_title_name = '';

				

				if (@$row->companyIdCheck == auth()->user()->company_id) {
					$client_name_com = GeneralMethod::companyDetailsByClient($row->job_id);
					if(!empty($client_name_com)){
						$client_title_name = $client_name_com;
						$client_name = ucwords(substr($client_title_name, 0, 15));
					}else{
						$client_title_name = $row->client_name;
						$client_name = ucwords(substr($client_title_name, 0, 15)); 
					}
				}else{
					$client_title_name = $row->company_name;
					$client_name = ucwords(substr($client_title_name, 0, 15)); 
				}



				return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' . ucwords($row->job_title) . '">' . ucwords(substr($row->job_title, 0, 10)) . '</a>
                    <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' . ucwords($client_title_name) . '">' . ucwords(substr($client_name, 0, 10)) . '
                  </div>';
			})
			->editColumn('Status', function ($row) use ($job_status_arr) {

				$status = $active = $hold = $inactive = $class = $recommendation_id = '';

				$job_status = $row->job_status;

				$status = ucwords($job_status);
				$class = '';
				//$class			=	$job_status_arr['active']; 
				$class = $job_status_arr[$job_status];
				$recommendation_id = $row->recommendation_id;

				if ($status == 'Inactive') {
					$status = 'Closed';
				}

				$final_status = "";
				if ($row->final_status == 2 && @$row->to_company_id == auth()->user()->company_id) {
					$status = 'REMOVED';
					$class = "closed-bg";
				}

				$ulink = '<a href="javascript:void(0);" class="d-flex flex-column ' . @$class . '  text-hover-success" >' . $status . '</a>';

				$recommendation_text = 'Client';
				if (@$row->companyIdCheck == auth()->user()->company_id) {
					$recommendation_text = 'Custom';
					$ulink = '<a href="javascript:void(0);" class="fs-6 ' . @$class . ' text-hover-success">' . $status . '</a>';
				}

				return '<div class="d-flex flex-column">
                       ' . $ulink . '
                      <span class="fw-bold text-muted d-block fs-7">R' . @$row->job_id . ' -' . $recommendation_text . '</span>
                    </div>';
			})
			->editColumn('InterviewMode', function ($row) {
			    $interLink ='';
			    if($row->interview_mode==2){
			        
			        $schedule_date  = $row->schedule_date;
			       $current_date  = date('Y-m-d H:i:s');
			       $schedule_timestamp = strtotime($schedule_date);
                   $current_timestamp = strtotime($current_date);
			        
			         $url = $row->interview_mention_link;
			         $urlComponents = parse_url($url);
			         
			     if ($schedule_timestamp >= $current_timestamp) { 
			         if ($urlComponents === false) {
			              $interLink = '<a href="javascript:;" class="d-flex flex-column interview_mention_link">Interview Link</a>' ;
			         }
			         
			         $scheme = $urlComponents['scheme'] ?? '';
			         
			         if ($scheme === 'http' || $scheme === 'https') {
			               $interLink = '<a href="'.$row->interview_mention_link.'" class="d-flex flex-column" target="_blank">Interview Link</a>' ;
			             } else {
			                $interLink = '<a href="javascript:;" class="d-flex flex-column interview_mention_link">Interview Link</a>' ;
			             }
			    }
                
			        
			        	
			    }else{
			       	$interLink = '' ; 
			    }
				return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . ucwords(substr($row->mode_name, 0, 10)) . '</a> 
                    <!--<span class="text-muted fw-bold d-block fs-7">' . ucwords(substr($row->mode_lavel_name, 0, 10)) . '</span>--> 
                  </div>'.$interLink;
			})
			->editColumn('Interviewers', function ($row) {

				$contactHtml = $real_company_id = $interviewers = '';
				$interviewers = $row->interviewers;
				$real_company_id = $row->real_company_id;

				$contactHtml = '<div class="d-flex flex-column">
                    							<div class="symbol-group symbol-hover">';
				if (!empty($interviewers)) {
					$interviewersArr = explode(' , ', trim($interviewers));
					$interviewersData = DB::table('users')
						->select('users.id', 'users.name', 'users.image')
						->whereIn('users.name', $interviewersArr)
						->where('users.company_id', '=', $real_company_id)
						->get();
					if (!empty($interviewersData)) {
						foreach ($interviewersData as $intVal) {
							$bgTextDecore				=	GeneralMethod::icoinText(@$intVal->name);
							$image_path = '';
							if ($intVal->image != '') {
								$image_path = $intVal->image;
							}
							if(!empty($image_path)){
							$contactHtml .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip1" data-html="true" title="' . ucwords(@$intVal->name) . '" >
								<img alt="Pic" src="' . $image_path . '">
								</div>';
							}else{
								$contactHtml .=
								'<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip" data-html="true" title="'.ucwords(@$intVal->name).'" >
								<div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100" style="float:left">
								<a href="javascript:void(0);" data-row-id="11" class="show-detail border-radius-100" id="kt_activities3_toggle">
									<div class="symbol-label fs-8 fs-8 '.$bgTextDecore.' border-radius-100">'.substr($intVal->name, 0, 1).'</div>
								</a>		 
								</div>
								</div>';
							}

						}
					}
				}
				$contactHtml .= '</div>
                    			</div>';
				return $contactHtml;

			})
			->editColumn('Source', function ($row) {
				$bgTextDecore				=	GeneralMethod::icoinText(@$row->posted_by);	
				$posted_by_image = '';
				if (!empty($row->posted_by_image)) {
					$posted_by_image =@$row->posted_by_image;
				}
				// if(!empty($posted_by_image)){
				// return '<div class="d-flex flex-column">
				// 			<div class="symbol-group symbol-hover">
				// 			  <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="' . @$row->posted_by . '">
				// 				<img alt="Pic" src="' . @$posted_by_image . '">
				// 			  </div>
				// 		  </div>
				// 		  <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="' . @$row->uc_company_name . '">' . substr(@$row->uc_company_name, 0, 10) . '</span>';
				// 		}else{
							return '<div class="d-flex flex-column">
									<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="" data-bs-original-title="' . @$row->posted_by . '">'.substr($row->posted_by, 0, 10).'</a>
								  </div>
								  <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="' . @$row->company_name . '">' . substr(@$row->company_name, 0, 10) . '</span>';
		
									
						// }
			})
			->editColumn('InterviewDate', function ($row) {
				return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . Carbon::parse($row->schedule_date)->format('d M, Y') . '</a>
                    <span class="text-muted fw-bold d-block fs-7">' . Carbon::parse($row->schedule_date)->format('H:i a') . '</span>
                  </div>';
			})
			->editColumn('Feedback', function ($row) use ($feedback_status, $available_status, $company_id) {




				$available_status_name = '';
				if ($row->available_status > 0) {
					$available_status_name = @$available_status[@$row->available_status]['name'];
				} else {
					//$available_status_name	=	@$available_status[6]['name']; 
				}

				$feedback = ucwords(substr(@$feedback_status[@$row->feedback]['name'], 0, 20));
				$customHtml = 'show-feedback_no';

				if ($row->thirdparty_company_id == $company_id) {
					$customHtml = '';
					if (empty($feedback)) {
						$feedback = '<span class=""> Pending Feedback</span>';
					}
				} else {
					if (empty($feedback)) {
						$feedback = '<span class="">-----</span>';
						$available_status_name = '';
					}
				}

				return '<div class="d-flex flex-column">
				<a href="javascript:void(0);" data-row-id="' . $row->id . '" class="text-dark fw-bolder text-hover-primary fs-6 ' . $customHtml . '">' . @$feedback . '</a>
				<span class="text-muted fw-bold text-muted d-block fs-7">' . ucwords(substr(@$available_status_name, 0, 20)) . '</span>
				</div>';


			})
			->editColumn('Rating', function ($row) {
				$imagePath = $text = '';
				$average_star_rating = '';
				$average_star_rating = $row->average_star_rating;
				if ($average_star_rating == 1) {
					$imagePath = url('public/img/star/1.png');
					$text = 'Bad';
				} else if ($average_star_rating == 2) {
					$imagePath = url('public/img/star/2.png');
					$text = 'Poor';
				} else if ($average_star_rating == 3) {
					$imagePath = url('public/img/star/3.png');
					$text = 'Average';
				} else if ($average_star_rating == 4) {
					$imagePath = url('public/img/star/4.png');
					$text = 'Best';
				} else if ($average_star_rating == 5) {
					$imagePath = url('public/img/star/5.png');
					$text = 'Very Best';
				} else {
					$imagePath = url('public/img/star/5-blank.png');
					$text = 'Blank';
				}

				return ' <img src="' . @$imagePath . '" alt="image" style="width: 5.5rem">
                    <span class="text-muted fw-bold text-muted d-block fs-7">' . @$text . '</span>';
			})
			->editColumn('Stage', function ($row) {
				return '<div class="d-flex flex-column"> 
                        <span class="badge fw-bolder fs-6 px-2 py-1 ' . @$row->css_style . '">' . substr(@$row->master_status_name, 0, 10) . '</span>
                        <span class="text-muted fw-bold text-muted d-block fs-7">' . substr(@$row->child_status_name, 0, 10) . '</span>
                    </div>';
			})

			->rawColumns(['chkbox', 'Name', 'Requisitions', 'Status', 'InterviewMode', 'Interviewers', 'Source', 'InterviewDate', 'Feedback', 'Rating', 'Stage'])

			->make(true);
	}
	public function sub_status_tab(Request $request){
		$interview_status_id = (int) $request->id;
		$feedback = (int) $request->feedback;
		$interview_sub_status 	= DB::table('interview_sub_status')
		->where('interview_sub_status.interview_status_id', '=',$interview_status_id)	
		->where('interview_sub_status.status', 1)		
		->get();	
		$html_sub_status = '';
		if(count($interview_sub_status)> 0){
			$html_sub_status .='<label class="text-dark fw-bolder fs-5">Interview status</label>
            <div class="row py-5 mb-3">';
			foreach($interview_sub_status as $val_status){
				          $sel	=	'';
                          if($feedback == $val_status->id ){
                            $sel	=	'checked="checked"';	
                          }
						  $charCount = strlen($val_status->name);
						  $divsection = 2;
						  if($charCount>18){
							$divsection = 3;
						  }

						//   dd($charCount);
						//   dd($val_status->name);
						  $html_sub_status .='<div class="col-lg-'.$divsection.'">
						<input type="radio" class="btn-check" name="app_int_feedback_status" id="app_int_feedback_status_'.$val_status->id.'" value="'.$val_status->id.'" '.@$sel.' >
						<label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_feedback_status_'.$val_status->id.'"> 
						<span class="d-block fw-bold text-start"> <span class="text-dark-800 fw-bolder d-block fs-6">'. ucwords($val_status->name) .' </span> </span> 
						</label>
				</div>';

			}
			$html_sub_status .='</div>';

		}else{
			$html_sub_status ='<input type="radio" class="btn-check" name="app_int_feedback_status" id="app_int_feedback_status_0" value="0" checked="checked">';
		}
		return Reply::dataOnly(['status' => 'success', 'view' => $html_sub_status]);
	}
	public function showfeedback($id)
	{

		$this->interviewSchedule = $available_status = $feedback_status = '';
        //  dd($id);
		$this->interviewSchedule = DB::table('interview_schedules')
			->select(
				'interview_schedules.id as interview_schedules_id',
				'interview_schedules.schedule_date',
				'interview_schedules.interview_job_id',
				'interview_schedules.job_application_id',
				'job_applications.id',
				'job_applications.full_name',
				'job_applications.current_designation',
				'jobs.title as job_title',
				'clients.name as client_name',
				'interview_mode.name as mode_name',
				'interview_mode_lavel.name as mode_lavel_name',

				'master_status.name as master_status_name',
				'master_status.css_style',
				'candidate_status.name as child_status_name',
				'interview_schedules.available_status',
				'interview_schedules.feedback',
				'interview_schedules.interviewers',
                'interview_schedules.company_id as real_company_id',
				'users.name as upload_by_name',
				'companies.company_name',
				'users.image'
				
			)
			->join('job_applications', 'job_applications.id', '=', 'interview_schedules.job_application_id')
			->join('add_job_wise_condidates', 'interview_schedules.interview_job_wise_con_id', 'add_job_wise_condidates.id')
			// ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', '=', 'job_applications.id')
			->leftjoin('master_status', function ($join){
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
			})
			->leftjoin('jobs', function ($join){
				$join->on('jobs.id', 'add_job_wise_condidates.job_id');
			})
			->join('users', 'users.id', '=', 'job_applications.submitted_by')
			//->leftjoin('users', 'users.id', 'job_applications.submitted_by')
			// ->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
			->leftjoin('clients', 'clients.id', '=', 'jobs.client_id')
			->leftjoin('companies', 'companies.id', 'users.company_id')
			// ->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
			->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
			->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
			->leftjoin('candidate_status', 'candidate_status.id', 'job_applications.child_status_id')
			->where('interview_schedules.id', $id)
			->first();
			
			$job_application_id= $this->interviewSchedule->job_application_id;

			$this->jobSkill = DB::table('job_metronics_skills')
			->leftJoin('candidate_job_skill_exp', function($join)use($job_application_id) {
			  $join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
				->where('candidate_job_skill_exp.candidate_id', '=',$job_application_id); // Add the candidate condition
			})
			->select('job_metronics_skills.*', 'candidate_job_skill_exp.skill_value')
			->where('job_metronics_skills.job_id', '=', $this->interviewSchedule->interview_job_id)
			->get();

			

		$this->available_status = GeneralMethod::available_status();
		$this->feedback_status = GeneralMethod::feedback_status();
		$this->interview_status = GeneralMethod::interview_status();


		$feedbackdata = DB::table('interview_skills_feedback as isf')
		->leftJoin('interview_feedback as ifb', function ($join) {
			$join->on('isf.feedback_id', '=', 'ifb.id');
		})
		->leftJoin('users', function ($join) {
			$join->on('isf.update_by', '=', 'users.id');
		})
		->leftJoin('users_profile', function ($join) {
			$join->on('users_profile.user_id', '=', 'users.id');
		})
		->where('isf.interview_id', $id)
		->orderByDesc('isf.feedback_id')
		->select(
			'isf.feedback_id',
			'isf.skills',
			'users.name as by_name',
			'users_profile.designation',
			'isf.skills_exp',
			'isf.skills_ratings',
			'isf.skills_remarks',
			'ifb.available_status as candidateStatus',
			'ifb.feedback_id as interviewStatus',
			'ifb.feedback_name',
			'ifb.message',
			'ifb.created_at',
			'ifb.star_ratings'
		)
		->get()
		->groupBy('feedback_id'); // Group records by feedback_id

		$this->feedbackMapped = $feedbackdata->map(function ($feedbacks) {
			return [
				'feedback_id' => $feedbacks->first()->feedback_id,
				'feedback_name' => $feedbacks->first()->feedback_name,
				'by_name' => $feedbacks->first()->by_name,
				'designation' => $feedbacks->first()->designation,
				'created_at' => $feedbacks->first()->created_at,
				'message' => $feedbacks->first()->message,
				'star_ratings' => $feedbacks->first()->star_ratings,
				'candidateStatus' => $feedbacks->first()->candidateStatus,
				'interviewStatus' => $feedbacks->first()->interviewStatus,
				'feedbackdata' => $feedbacks->map(function ($feedback) {
					return [
						'skills' => $feedback->skills,
						'experience' => $feedback->skills_exp,
						'ratings' => $feedback->skills_ratings,
						'remarks' => $feedback->skills_remarks,
					];
				})->values()->all(),
			];
		});

		$download_show = (count($this->feedbackMapped));

		

		$view = view('service_company.interview-schedule.showfeedback', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'view' => $view,'download_show'=> $download_show]);
	}
	public function skillshowfeedback($id)
	{

		$feedbackdata = DB::table('interview_skills_feedback as isf')
		->leftJoin('interview_feedback as ifb', function ($join) {
			$join->on('isf.feedback_id', '=', 'ifb.id');
		})
		->leftJoin('users', function ($join) {
			$join->on('isf.update_by', '=', 'users.id');
		})
		->leftJoin('users_profile', function ($join) {
			$join->on('users_profile.user_id', '=', 'users.id');
		})
		->where('isf.interview_id', $id)
		->orderByDesc('isf.feedback_id')
		->select(
			'isf.feedback_id',
			'isf.skills',
			'users.name as by_name',
			'users_profile.designation',
			'isf.skills_exp',
			'isf.skills_ratings',
			'isf.skills_remarks',
			'ifb.feedback_name',
			'ifb.available_status as candidateStatus',
			'ifb.feedback_id as interviewStatus',
			'ifb.message',
			'ifb.created_at',
			'ifb.star_ratings'
		)
		->get()
		->groupBy('feedback_id'); // Group records by feedback_id

		$this->feedbackMapped = $feedbackdata->map(function ($feedbacks) {
			return [
				'feedback_id' => $feedbacks->first()->feedback_id,
				'feedback_name' => $feedbacks->first()->feedback_name,
				'by_name' => $feedbacks->first()->by_name,
				'designation' => $feedbacks->first()->designation,
				'created_at' => $feedbacks->first()->created_at,
				'message' => $feedbacks->first()->message,
				'star_ratings' => $feedbacks->first()->star_ratings,
				'candidateStatus' => $feedbacks->first()->candidateStatus,
				'interviewStatus' => $feedbacks->first()->interviewStatus,
				'feedbackdata' => $feedbacks->map(function ($feedback) {
					return [
						'skills' => $feedback->skills,
						'experience' => $feedback->skills_exp,
						'ratings' => $feedback->skills_ratings,
						'remarks' => $feedback->skills_remarks,
					];
				})->values()->all(),
			];
		});

		// dd($this->feedbackMapped);




			// $feedbackdata = DB::table('interview_skills_feedback')
			// ->where('interview_id', $id)->orderByDesc('feedback_id')->get()
			// ->groupBy('feedback_id'); // Group records by feedback_id

			// $this->feedbackMapped = $feedbackdata->map(function ($feedbacks) {
			// 	return $feedbacks->map(function ($feedback) {
			// 		return [
			// 			'skills' => $feedback->skills,
			// 			'experience' => $feedback->skills_exp,
			// 			'ratings' => $feedback->skills_ratings,
			// 			'remarks' => $feedback->skills_remarks,
			// 		];
			// 	});
			// });

            // dd($this->feedbackMapped);


		$this->interviewSchedule = $available_status = $feedback_status = '';
        //  dd($id);
		$this->interviewSchedule = DB::table('interview_schedules')
			->select(
				'interview_schedules.id as interview_schedules_id',
				'interview_schedules.schedule_date',
				'interview_schedules.interview_job_id',
				'interview_schedules.job_application_id',
				'job_applications.id',
				'job_applications.full_name',
				'job_applications.current_designation',
				'jobs.title as job_title',
				'clients.name as client_name',
				'interview_mode.name as mode_name',
				'interview_mode_lavel.name as mode_lavel_name',

				'master_status.name as master_status_name',
				'master_status.css_style',
				'candidate_status.name as child_status_name',
				'interview_schedules.available_status',
				'interview_schedules.feedback',
				'interview_schedules.interviewers',
                'interview_schedules.company_id as real_company_id',
				'users.name as upload_by_name',
				'companies.company_name',
				'users.image'
				
			)
			->join('job_applications', 'job_applications.id', '=', 'interview_schedules.job_application_id')
			->join('add_job_wise_condidates', 'interview_schedules.interview_job_wise_con_id', 'add_job_wise_condidates.id')
			// ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', '=', 'job_applications.id')
			->leftjoin('master_status', function ($join){
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
			})
			->leftjoin('jobs', function ($join){
				$join->on('jobs.id', 'add_job_wise_condidates.job_id');
			})
			->join('users', 'users.id', '=', 'job_applications.submitted_by')
			//->leftjoin('users', 'users.id', 'job_applications.submitted_by')
			// ->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
			->leftjoin('clients', 'clients.id', '=', 'jobs.client_id')
			->leftjoin('companies', 'companies.id', 'users.company_id')
			// ->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
			->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
			->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
			->leftjoin('candidate_status', 'candidate_status.id', 'job_applications.child_status_id')
			->where('interview_schedules.id', $id)
			->first();
			
			$job_application_id= $this->interviewSchedule->job_application_id;

			$this->jobSkill = DB::table('job_metronics_skills')
			->leftJoin('candidate_job_skill_exp', function($join)use($job_application_id) {
			  $join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
				->where('candidate_job_skill_exp.candidate_id', '=',$job_application_id); // Add the candidate condition
			})
			->select('job_metronics_skills.*', 'candidate_job_skill_exp.skill_value')
			->where('job_metronics_skills.job_id', '=', $this->interviewSchedule->interview_job_id)
			->get();

			

		$this->available_status = GeneralMethod::available_status();
		$this->feedback_status = GeneralMethod::feedback_status();

		$view = view('service_company.interview-schedule.skillfeedback', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'view' => $view]);
	}
	public function skillshowfeedbackDownload($id,$feed='')
	{
		
		$feedbackdata = DB::table('interview_skills_feedback as isf')
		->leftJoin('interview_feedback as ifb', function ($join) {
			$join->on('isf.feedback_id', '=', 'ifb.id');
		})
		->leftJoin('users', function ($join) {
			$join->on('isf.update_by', '=', 'users.id');
		})
		->leftJoin('users_profile', function ($join) {
			$join->on('users_profile.user_id', '=', 'users.id');
		});
        if($feed){
           $feedbackdata = $feedbackdata->where('isf.feedback_id', $feed);
		}else{
          $feedbackdata = $feedbackdata->where('isf.interview_id', $id);
		}
		

		$feedbackdata = $feedbackdata->orderByDesc('isf.feedback_id')
		->select(
			'isf.feedback_id',
			'isf.skills',
			'users.name as by_name',
			'users_profile.designation',
			'isf.skills_exp',
			'isf.skills_ratings',
			'isf.skills_remarks',
			'ifb.feedback_name',
			'ifb.available_status as candidateStatus',
			'ifb.feedback_id as interviewStatus',
			'ifb.message',
			'ifb.created_at',
			'ifb.star_ratings'
		)
		->get()
		->groupBy('feedback_id'); // Group records by feedback_id
        // dd($feedbackdata);
		$this->feedbackMapped = $feedbackdata->map(function ($feedbacks) {
			return [
				'feedback_id' => $feedbacks->first()->feedback_id,
				'feedback_name' => $feedbacks->first()->feedback_name,
				'by_name' => $feedbacks->first()->by_name,
				'designation' => $feedbacks->first()->designation,
				'created_at' => $feedbacks->first()->created_at,
				'message' => $feedbacks->first()->message,
				'star_ratings' => $feedbacks->first()->star_ratings,
				'candidateStatus' => $feedbacks->first()->candidateStatus,
				'interviewStatus' => $feedbacks->first()->interviewStatus,
				'feedbackdata' => $feedbacks->map(function ($feedback) {
					return [
						'skills' => $feedback->skills,
						'experience' => $feedback->skills_exp,
						'ratings' => $feedback->skills_ratings,
						'remarks' => $feedback->skills_remarks,
					];
				})->values()->all(),
			];
		});
        // dd($this->feedbackMapped);

		$this->interviewSchedule = $available_status = $feedback_status = '';
        //  dd($id);
		$this->interviewSchedule = DB::table('interview_schedules')
			->select(
				'interview_schedules.id as interview_schedules_id',
				'interview_schedules.schedule_date',
				'interview_schedules.interview_job_id',
				'interview_schedules.job_application_id',
				'job_applications.id',
				'job_applications.full_name',
				'job_applications.current_designation',
				'jobs.title as job_title',
				'clients.name as client_name',
				'interview_mode.name as mode_name',
				'interview_mode_lavel.name as mode_lavel_name',

				'master_status.name as master_status_name',
				'master_status.css_style',
				'candidate_status.name as child_status_name',
				'interview_schedules.available_status',
				'interview_schedules.feedback',
				'interview_schedules.interviewers',
                'interview_schedules.company_id as real_company_id',
				'users.name as upload_by_name',
				'companies.company_name',
				'users.image'
				
			)
			->join('job_applications', 'job_applications.id', '=', 'interview_schedules.job_application_id')
			->join('add_job_wise_condidates', 'interview_schedules.interview_job_wise_con_id', 'add_job_wise_condidates.id')
			// ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', '=', 'job_applications.id')
			->leftjoin('master_status', function ($join){
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
			})
			->leftjoin('jobs', function ($join){
				$join->on('jobs.id', 'add_job_wise_condidates.job_id');
			})
			->join('users', 'users.id', '=', 'job_applications.submitted_by')
			//->leftjoin('users', 'users.id', 'job_applications.submitted_by')
			// ->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
			->leftjoin('clients', 'clients.id', '=', 'jobs.client_id')
			->leftjoin('companies', 'companies.id', 'users.company_id')
			// ->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
			->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
			->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
			->leftjoin('candidate_status', 'candidate_status.id', 'job_applications.child_status_id')
			->where('interview_schedules.id', $id)
			->first();
			// dd($this->interviewSchedule);
			
			$job_application_id= $this->interviewSchedule->job_application_id;

			$this->jobSkill = DB::table('job_metronics_skills')
			->leftJoin('candidate_job_skill_exp', function($join)use($job_application_id) {
			  $join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
				->where('candidate_job_skill_exp.candidate_id', '=',$job_application_id); // Add the candidate condition
			})
			->select('job_metronics_skills.*', 'candidate_job_skill_exp.skill_value')
			->where('job_metronics_skills.job_id', '=', $this->interviewSchedule->interview_job_id)
			->get();

			

		$this->available_status = GeneralMethod::available_status();
		$this->feedback_status = GeneralMethod::feedback_status();
		

		$view = view('service_company.interview-schedule.download-feedback', $this->data)->render();


		$pdf = app('dompdf.wrapper');
        $pdf->loadView('service_company.interview-schedule.download-feedback',$this->data);
		// return view('service_company.interview-schedule.download-feedback', $this->data);
		// return $pdf->stream('feedback-preview.pdf');

            $fileName = 'feedback-download'.'.pdf';
			// dd($fileName);
            if(1){
                return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="'.$fileName.'"');   //attachment inline
            }


		return Reply::dataOnly(['status' => 'success', 'view' => $view]);


	}
	public function updateInterview(Request $request)
	{

		// dd($request->all());
		$interview_id = $app_int_status = $app_int_feedback_status = $feedback_comment = $application_id = $rating = '';

		$interview_id = @$request->interview_id;
		$app_int_status = @$request->app_int_status;
		$app_int_feedback_status = @$request->app_int_feedback_status;
		$feedback_comment = @$request->feedback_comment;
		$application_id = @$request->application_id;
		$rating = @$request->rating;

		 $chkAdminRole = $this->user->hasRole('admin');
		 
		// if(auth()->user()->id==18){
		// 	dd(auth()->user()->id);
		//        $teamRankElastic = [
		// 			'company_id'=>2,
		// 			'user_id'=>16,
		// 			'candidate_id'=>26925,
		// 			'profileUpload'=>0,
		// 			'interviewScheduled'=>10,
		// 			'interviewNoShow'=>10,
		// 			'offerReleased'=>0,
		// 			'candidateJoined'=>0,
		// 			'jobsPosted'=>0,
		// 			'feedbackGivenOnCVs'=>0,
		// 			'postJobToVendorMarket'=>0,
		// 			'outsourceJobsToVendors'=>0,
		// 			'listBenchCandidates'=>0,
		// 			'respondToBenchRequests'=>0
		// 		];
		// 		GeneralMethod::teamRankElastic($teamRankElastic);
		// }

		
		 $cachekeys = [
            'allinterviewschedule_version_user_'.$this->user->id,
        ];
        foreach ($cachekeys as $key) {
            GeneralMethod::cacheClean($key);
        }

		$interviewCount_rank = DB::table('interview_feedback')
			->select('interview_feedback.id')
			->where('interview_id', @$interview_id)
			->where('application_id', @$application_id)
			->whereNotNull('feedback_id')
			->count();

			$interviewCount_1 = DB::table('interview_feedback')
			->where('interview_id', $interview_id)
			->where('application_id', $application_id)
			->where('available_status',1)
			->count();
			$interviewCount_2 = DB::table('interview_feedback')
			->where('interview_id', $interview_id)
			->where('application_id', $application_id)
			->where('available_status',2)
			->count();
			$interviewCount_3 = DB::table('interview_feedback')
			->where('interview_id', $interview_id)
			->where('application_id', $application_id)
			->where('available_status',3)
			->count();


			$interviewSum_rank = DB::table('interview_schedules')
				->select('interview_job_wise_con_id as wiseId','interview_job_id','schedule_date')
				->where('id', @$interview_id)
				->first();

			 
			  $interview_update_date_check = date("Y-m-d H:i:s", strtotime($interviewSum_rank->schedule_date));
			  $interview_update_date_check = Carbon::parse($interview_update_date_check);
			  $today = Carbon::today();
			  $diffInDays = abs($today->diffInDays($interview_update_date_check));
            
            // dd($interviewCount_rank); schedule_date
				

			if(($interviewCount_1==0 && $interviewCount_2==0 && $interviewCount_3==0) && ($app_int_status==1 || $app_int_status==2 || $app_int_status==3) ){
				$interviewSum_rank = DB::table('interview_schedules')
				->select('interview_job_wise_con_id as wiseId','interview_job_id')
				->where('id', @$interview_id)
				->first();
				$interviewScheduled = 0;
				$interviewNoShow = 0;
				$teamInterviewScheduled = 0;
				$teamInterviewNoShow = 0;
				if($diffInDays<=30){
					if($app_int_status==1 && $app_int_feedback_status>0){
						$interviewScheduled = 10;
						$teamInterviewScheduled = 2;
					}elseif($app_int_status==2){
						$interviewNoShow = -5;
					}elseif($app_int_status==3 && $app_int_feedback_status==13){
						$interviewNoShow = -5;
					}
				}

			   $AddJobWiseCondidate = AddJobWiseCondidate::find($interviewSum_rank->wiseId);

			   $spok_user_id_rows = DB::table('job_assign')
				->where('job_id', $interviewSum_rank->interview_job_id)
				->get(['spok_user_id', 'company_id'])->toArray();

				// Step 2: Build result as [company_id => spok_user_id]
				$teamCaptainId = [];

				foreach ($spok_user_id_rows as $row) {
					if (
						!is_null($row->spok_user_id) &&
						$row->spok_user_id != $AddJobWiseCondidate->submitted_by
					) {
						$teamCaptainId[$row->company_id][] = $row->spok_user_id;
					}
				}

				// Step 3: Remove duplicates and reindex inner arrays
				foreach ($teamCaptainId as $companyId => $spokIds) {
					$teamCaptainId[$companyId] = array_values(array_unique($spokIds));
				}

				
				 if(count($teamCaptainId) > 0){

					foreach($teamCaptainId as $comId => $captainId){
						$teamRankElastic = [
							'company_id'=>$comId,
							'user_id'=>$captainId[0],
							'candidate_id'=>$AddJobWiseCondidate->id,
							'profileUpload'=>0,
							'interviewScheduled'=>0,
							'interviewNoShow'=>0,
							'offerReleased'=>0,
							'candidateJoined'=>0,
							'jobsPosted'=>0,
							'feedbackGivenOnCVs'=>0,
							'postJobToVendorMarket'=>0,
							'outsourceJobsToVendors'=>0,
							'listBenchCandidates'=>0,
							'respondToBenchRequests'=>0,
							'interestedCandidates'=>0,
							'notInterestedCandidates'=>0,
							'teamCaptainInterviewScheduled'=>$teamInterviewScheduled,
							'teamCaptainInterviewNoShow'=>$teamInterviewNoShow,
							'teamCaptainOfferReleased'=>0,
							'teamCaptainCandidateJoined'=>0
						];
						GeneralMethod::teamRankElastic($teamRankElastic);
					}

				 }

					$teamRankElastic = [
					'company_id'=>$AddJobWiseCondidate->company_id,
					'user_id'=>$AddJobWiseCondidate->submitted_by,
					'candidate_id'=>$AddJobWiseCondidate->id,
					'profileUpload'=>0,
					'interviewScheduled'=>$interviewScheduled,
					'interviewNoShow'=>$interviewNoShow,
					'offerReleased'=>0,
					'candidateJoined'=>0,
					'jobsPosted'=>0,
					'feedbackGivenOnCVs'=>0,
					'postJobToVendorMarket'=>0,
					'outsourceJobsToVendors'=>0,
					'listBenchCandidates'=>0,
					'respondToBenchRequests'=>0
				];
				GeneralMethod::teamRankElastic($teamRankElastic);
			}else{

			$interviewCount_rank = DB::table('interview_feedback')
			->select('interview_feedback.id')
			->where('interview_id', @$interview_id)
			->where('available_status', '=',1)
			->count();

			$interviewCount_1 = DB::table('interview_feedback')
			->select('interview_feedback.id')
			->where('interview_id', @$interview_id)
			->where('available_status', '=',2)
			->count();
			$interviewCount_2 = DB::table('interview_feedback')
			->select('interview_feedback.id')
			->where('interview_id', @$interview_id)
			->where('available_status', '=',3)
			->count();
			
			// dd($interviewCount_rank);
			if((($interviewCount_rank==1) && ($interviewCount_1==0 && $interviewCount_2==0)) && ($app_int_status==2 ||$app_int_status==3)){

              $interviewSum_rank = DB::table('interview_schedules')
				->select('interview_job_wise_con_id as wiseId','interview_job_id')
				->where('id', @$interview_id)
				->first();

				$interviewScheduled = 0;
				$teamInterviewScheduled = 0;
				$interviewNoShow = 0;
				$teamInterviewNoShow = 0;
				if($diffInDays<=30){
					if($app_int_status==2){
						$interviewNoShow = -5;
						$interviewScheduled = -10;
					}elseif($app_int_status==3 && $app_int_feedback_status==13){
						$interviewNoShow = -5;
						$interviewScheduled = -10;
					}elseif($app_int_status==3 && $app_int_feedback_status==14){
						$interviewNoShow = 0;
						$interviewScheduled = -10;
					}elseif($app_int_status==3 && $app_int_feedback_status==15){
						$interviewNoShow = 0;
						$interviewScheduled = -10;
					}elseif($app_int_status==3 && $app_int_feedback_status==17){
						$interviewNoShow = 0;
						$interviewScheduled = -10;
					}
				}
				

				$interviewSum_no_show = DB::table('interview_feedback')
				->select('interview_feedback.id')
				->where('interview_id', @$interview_id)
				->where('available_status', '=',2)
				->count();

				$interviewSum_resch_show = DB::table('interview_feedback')
				->select('interview_feedback.id')
				->where('interview_id', @$interview_id)
				->where('available_status', '=',3)
				->count();

				// dd($interviewSum_no_show);
				$interviewSum_no_show   = (int) $interviewSum_no_show;
                $interviewSum_resch_show = (int) $interviewSum_resch_show;
				if($interviewSum_no_show >=1 || $interviewSum_resch_show >=1){
					$interviewScheduled = 0;
					$interviewNoShow = 0;
				}


			   $AddJobWiseCondidate = AddJobWiseCondidate::find($interviewSum_rank->wiseId);

			   $spok_user_id_rows = DB::table('job_assign')
				->where('job_id', $interviewSum_rank->interview_job_id)
				->get(['spok_user_id', 'company_id'])->toArray();

				// Step 2: Build result as [company_id => spok_user_id]
				$teamCaptainId = [];

				foreach ($spok_user_id_rows as $row) {
					if (
						!is_null($row->spok_user_id) &&
						$row->spok_user_id != $AddJobWiseCondidate->submitted_by
					) {
						$teamCaptainId[$row->company_id][] = $row->spok_user_id;
					}
				}

				// Step 3: Remove duplicates and reindex inner arrays
				foreach ($teamCaptainId as $companyId => $spokIds) {
					$teamCaptainId[$companyId] = array_values(array_unique($spokIds));
				}

				
				 if(count($teamCaptainId) > 0){

					foreach($teamCaptainId as $comId => $captainId){
						$teamRankElastic = [
							'company_id'=>$comId,
							'user_id'=>$captainId[0],
							'candidate_id'=>$AddJobWiseCondidate->id,
							'profileUpload'=>0,
							'interviewScheduled'=>0,
							'interviewNoShow'=>0,
							'offerReleased'=>0,
							'candidateJoined'=>0,
							'jobsPosted'=>0,
							'feedbackGivenOnCVs'=>0,
							'postJobToVendorMarket'=>0,
							'outsourceJobsToVendors'=>0,
							'listBenchCandidates'=>0,
							'respondToBenchRequests'=>0,
							'interestedCandidates'=>0,
							'notInterestedCandidates'=>0,
							'teamCaptainInterviewScheduled'=>$teamInterviewScheduled,
							'teamCaptainInterviewNoShow'=>$teamInterviewNoShow,
							'teamCaptainOfferReleased'=>0,
							'teamCaptainCandidateJoined'=>0
						];
						GeneralMethod::teamRankElastic($teamRankElastic);
					}

				 }

					$teamRankElastic = [
					'company_id'=>$AddJobWiseCondidate->company_id,
					'user_id'=>$AddJobWiseCondidate->submitted_by,
					'candidate_id'=>$AddJobWiseCondidate->id,
					'profileUpload'=>0,
					'interviewScheduled'=>$interviewScheduled,
					'interviewNoShow'=>$interviewNoShow,
					'offerReleased'=>0,
					'candidateJoined'=>0,
					'jobsPosted'=>0,
					'feedbackGivenOnCVs'=>0,
					'postJobToVendorMarket'=>0,
					'outsourceJobsToVendors'=>0,
					'listBenchCandidates'=>0,
					'respondToBenchRequests'=>0
				];
				GeneralMethod::teamRankElastic($teamRankElastic);

				
			}

			}
		
        		// dd($interview_id,$request->all());

		$data = [];
		

		$values = array(
			'interview_id' => @$interview_id,
			'application_id' => @$application_id,
			'available_status' => @$app_int_status,
			'feedback_id' => @$app_int_feedback_status,
			'star_ratings' => @$rating,
			//'feedback_name' => $feedback_name,
			'message' => @$feedback_comment,
			'update_by' => $this->user->id,
			'created_at' => date('Y-m-d:H:i:s')
		);
				
		$update = DB::table('interview_feedback')->insert($values);
		$lastId = DB::getPdo()->lastInsertId();

		if($app_int_feedback_status==5 || $app_int_status !=1){
			
		}else{
			foreach ($request->all() as $key => $value) {
				if (preg_match('/skillsId_(\d+)/', $key, $matches)) {
					$skillId = $matches[1];

					// Extract values using the skill ID
					$skillName = $request->input("skills_{$skillId}");
					$experience = $request->input("experience_{$skillId}");
					$skillRating = $request->input("rating_{$skillId}");
					$skillRemark = $request->input("remark_{$skillId}");

					// Store data in an array for batch insert
					$data[] = [
						'interview_id' => $interview_id,
						'application_id' => @$application_id,
						'update_by' => $this->user->id,
						'candidate_status' => @$app_int_status,
						'feedback_status' => @$app_int_feedback_status,
						'feedback_id' => $lastId,
						'star_ratings' => @$rating,
						'skills' => $skillName,
						'skills_exp' => $experience,
						'skills_ratings' => $skillRating,
						'skills_remarks' => $skillRemark,
						'created_at' => now(),
						'updated_at' => now(),
					];
				}
			}
			
			$data[] = [
				'interview_id' => $interview_id,
				'application_id' => @$application_id,
				'update_by' => $this->user->id,
				'candidate_status' => @$app_int_status,
				'feedback_status' => @$app_int_feedback_status,
				'feedback_id' => $lastId,
				'star_ratings' => @$rating,
				'skills' => @$request->skills_com,
				'skills_exp' => @$request->experience_com,
				'skills_ratings' =>@$request->rating_com,
				'skills_remarks' => @$request->remark_com,
				'created_at' => now(),
				'updated_at' => now(),
			];
			$data[] = [
				'interview_id' => $interview_id,
				'application_id' => @$application_id,
				'update_by' => $this->user->id,
				'candidate_status' => @$app_int_status,
				'feedback_status' => @$app_int_feedback_status,
				'feedback_id' => $lastId,
				'star_ratings' => @$rating,
				'skills' => @$request->skills_conl,
				'skills_exp' => @$request->experience_conl,
				'skills_ratings' =>@$request->rating_conl,
				'skills_remarks' => @$request->remark_conl,
				'created_at' => now(),
				'updated_at' => now(),
			];
			$data[] = [
				'interview_id' => $interview_id,
				'application_id' => @$application_id,
				'update_by' => $this->user->id,
				'candidate_status' => @$app_int_status,
				'feedback_status' => @$app_int_feedback_status,
				'feedback_id' => $lastId,
				'star_ratings' => @$rating,
				'skills' => @$request->skills_culf,
				'skills_exp' => @$request->experience_culf,
				'skills_ratings' =>@$request->rating_culf,
				'skills_remarks' => @$request->remark_culf,
				'created_at' => now(),
				'updated_at' => now(),
			];
			if (!empty($data)) {
				DB::table('interview_skills_feedback')->insert($data);
			}
		}

		$interviewCount = DB::table('interview_feedback')
			->select('interview_feedback.id')
			->where('interview_id', @$interview_id)
			->where('application_id', @$application_id)
			->count();

		$interviewSum = DB::table('interview_feedback')
			->select(DB::raw('SUM(star_ratings) AS star_ratings_sum'))
			->where('interview_id', @$interview_id)
			->where('application_id', @$application_id)
			->first();

		$averageRatimg = @$rating; //$interviewSum->star_ratings_sum / $interviewCount;

		$returnValue = DB::table('interview_schedules')->where('id', $interview_id)->limit(1)
			->update(array('available_status' => @$app_int_status, 'feedback' => @$app_int_feedback_status, 'average_star_rating' => ceil($averageRatimg)));


		$updatetimelineArr = array(
			'average_star_rating' => ceil($averageRatimg),
			'interview_message' => @$feedback_comment
		);
		$returnValue = DB::table('updatetimeline')->where('updatetimeline.interview_schedules_id', $interview_id)->limit(1)->update($updatetimelineArr);


		$InterviewSchedule = InterviewSchedule::join('add_job_wise_condidates','add_job_wise_condidates.id','=','interview_schedules.interview_job_wise_con_id')
		->join('companies','companies.id','=','interview_schedules.company_id')
		->join('clients','clients.company_id','=','companies.id')
		->join('client_location','client_location.client_id','=','clients.id')
		->join('jobs','jobs.id','=','interview_schedules.interview_job_id')
		->join('job_applications','job_applications.id','=','interview_schedules.job_application_id')
		->join('updatetimeline','updatetimeline.interview_schedules_id','=','interview_schedules.id')
		->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')
		->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage')
		->select('interview_schedules.*','updatetimeline.interview_mention_link','jobs.client_id','master_status.name as master_status_name', 'candidate_status.name as child_status_name', 'updatetimeline.interview_mode_child_level as mode_child','companies.company_name','companies.description','companies.website','client_location.address as c_address','job_applications.full_name','job_applications.email as cond_email','job_applications.current_designation','jobs.employment_type','jobs.title','jobs.job_description','jobs.id as jobID','jobs.total_positions')->where('interview_schedules.id','=',$interview_id)->first();

		// dd($interview_id,$InterviewSchedule );

		$jobId = $InterviewSchedule->interview_job_id;
		$company_or_client = GeneralMethod::companyDetailsByClient($InterviewSchedule->interview_job_id);
		$interview_mode	= "";
		if($InterviewSchedule->interview_mode > 0){
			$interview_mode_Arr		= 	GeneralMethod::interview_mode();
			$interview_mode			= 	$interview_mode_Arr[@$InterviewSchedule->interview_mode];				
		}
		$interview_mode_child_level	=	$InterviewSchedule->interview_mode_child_level;	
		if($InterviewSchedule->interview_mode==3){
		$interview_mode_child_level	=	@$InterviewSchedule->interview_mention_link;	
		}

		$partnersData = User::join('job_assign', 'users.id', '=', 'job_assign.user_id')
        ->join('package_active', function ($join) {
            $join->on('users.company_id', '=', 'package_active.company_id')
                ->where('package_active.emails_balance', '>', 0)
				->where('package_active.startdate', '<=', now()) 
                ->where('package_active.enddate', '>=', now()) 
                ->where('package_active.packages_status', 1); 
        })
		->where('job_assign.job_id', '=', $jobId)
        ->pluck('users.email')
        ->toArray();



		
		$recommendedDataList = DB::table('recommendation_relation');
		$recommendedDataList = $recommendedDataList->join('companies', 'companies.id', 'recommendation_relation.to_company_id');
		$recommendedDataList = $recommendedDataList->join('jobs', 'jobs.id', 'recommendation_relation.job_id');
		$recommendedDataList = $recommendedDataList->leftJoin('job_applications', function ($join) use ($jobId) {
			$join->on('job_applications.thirdparty_company_id', '=', 'companies.id');
			$join->on('job_applications.job_id', '=', 'jobs.id');
		});
		$recommendedDataList = $recommendedDataList->join('package_active', function ($join) {
            $join->on('companies.id', '=', 'package_active.company_id')
                ->where('package_active.emails_balance', '>', 0)
				->where('package_active.startdate', '<=', now()) 
                ->where('package_active.enddate', '>=', now()) 
                ->where('package_active.packages_status', 1);
        });
		$recommendedDataList = $recommendedDataList->where('recommendation_relation.final_status', 1);
		$recommendedDataList = $recommendedDataList->where('recommendation_relation.job_id', '=', $jobId);
		$recommendedDataList = $recommendedDataList->groupBy('companies.id');
		$recommendedDataList = $recommendedDataList->pluck('companies.company_email');
		$recommendedDataList = $recommendedDataList->toArray();


         $feedback_master= GeneralMethod::feedback_status();
		 $feedback_status ='';
		 if(@$app_int_feedback_status){
			$feedback_status  = $feedback_master[(@$app_int_feedback_status) ? @$app_int_feedback_status : 0]['name'];
		 }
         

		 $feedbackdata = DB::table('interview_skills_feedback as isf')
		->leftJoin('interview_feedback as ifb', function ($join) {
			$join->on('isf.feedback_id', '=', 'ifb.id');
		})
		->leftJoin('users', function ($join) {
			$join->on('isf.update_by', '=', 'users.id');
		})
		->leftJoin('users_profile', function ($join) {
			$join->on('users_profile.user_id', '=', 'users.id');
		})
		->where('ifb.id', $lastId)
		->orderByDesc('isf.feedback_id')
		->select(
			'isf.feedback_id',
			'isf.skills',
			'users.name as by_name',
			'users_profile.designation',
			'isf.skills_exp',
			'isf.skills_ratings',
			'isf.skills_remarks',
			'ifb.feedback_name',
			'ifb.message',
			'ifb.created_at',
			'ifb.star_ratings'
		)
		->get()
		->groupBy('feedback_id'); // Group records by feedback_id

		$feedbackMapped = $feedbackdata->map(function ($feedbacks) {
			return [
				'feedback_id' => $feedbacks->first()->feedback_id,
				'feedback_name' => $feedbacks->first()->feedback_name,
				'by_name' => $feedbacks->first()->by_name,
				'designation' => $feedbacks->first()->designation,
				'created_at' => $feedbacks->first()->created_at,
				'message' => $feedbacks->first()->message,
				'star_ratings' => $feedbacks->first()->star_ratings,
				'feedbackdata' => $feedbacks->map(function ($feedback) {
					return [
						'skills' => $feedback->skills,
						'experience' => $feedback->skills_exp,
						'ratings' => $feedback->skills_ratings,
						'remarks' => $feedback->skills_remarks,
					];
				})->values()->all(),
			];
		});

		$data = [
			'InterviewDate' => date("d-m-Y h:i:s", strtotime($InterviewSchedule->schedule_date)),
			'InterviewMode' => $interview_mode.'('.$InterviewSchedule->mode_child.')',
			'InterviewAddress' => $InterviewSchedule->interview_mention_link,
			'CompanyName' => $InterviewSchedule->company_name,
			'CompanyLocation' => $InterviewSchedule->c_address,
			'Position' => $InterviewSchedule->current_designation,
			'EmploymentType' => ($InterviewSchedule->employment_type==1) ? 'Contract' : 'Full-time',			
			'ReqID' => "R" . @$InterviewSchedule->jobID,
			'Reqname' => @$InterviewSchedule->title,
			'NoofEmployees' => $InterviewSchedule->total_positions,
			'OfficeTiming' => "...",
			'NoofWorkingDays' => "...",
			'feedback_comment' => @$feedback_comment,
			'WebsiteURL' => $InterviewSchedule->website,
			'Jobdescription' => $InterviewSchedule->job_description,
			'AboutOrganization' => $InterviewSchedule->description,
			'VendorTeam' => @$InterviewSchedule->company_name,
			'CompanyNameClientname' => $company_or_client,	
			'feedback_status' => (@$feedback_status) ? @$feedback_status : '',
			'CandidateName' => $InterviewSchedule->full_name,
			'Stage' => ($InterviewSchedule->master_status_name) ? $InterviewSchedule->master_status_name : '.....',
			'Substage' => ($InterviewSchedule->child_status_name) ? $InterviewSchedule->child_status_name : '....',
			'username' => auth()->user()->name,
			'mobile' => auth()->user()->mobile,
			'feedbackMapped' => $feedbackMapped
		];
		// 		dd($data);
		// 		config(['mail.from.name' => auth()->user()->name]);


		$cc_cond = array($InterviewSchedule->cond_email);
		
		 
    			$notification = DB::table('clients')->where('id','=', @$InterviewSchedule->client_id)->where('self_company','=', 1)->pluck('email')->toArray();
                $email_to_remove = auth()->user()->email;
                $index = array_search($email_to_remove, $notification);
                if ($index !== false) {
                    unset($notification[$index]);
                    $notification = array_values($notification);
                }
				$cc = []; //$notification; //array_merge($partnersData, $recommendedDataList);
				
         // $cc = array_merge($partnersData, $recommendedDataList);
        $basecompany = GeneralMethod::baseCompany();
        $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
        $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
             
		
		$attachments = [];
		
		$subject = "Interview Feedback | $InterviewSchedule->full_name | $InterviewSchedule->title | R$InterviewSchedule->jobID ";
		$checkMailPermision = GeneralMethod::mailPermission(12);
		if($checkMailPermision){
			if($app_int_feedback_status !=5){
			    $company_id = auth()->user()->company_id;
				$mailTypeSubject = GeneralMethod::mailTypeSubject(12,1);
		        $notificationemail = GeneralMethod::notification($company_id,'Recuitment');
				$defailsEmail = DB::table('companies')->where('id',$company_id)->first()->company_email;
				$to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';  
                //  dd($to_Mail_Id);
				QueueNotificationMailService::dispatch(
					$to_Mail_Id,
					$subject,
					'email.candidate.interview-feedback',
					(object) $data,
					$attachments,
					$fromAddress,
					$senderName,
					$cc,
					$company_id
				)->onConnection('emails');
			}

		    //   $result = $this->mailService->sendEmail($to_Mail_Id, $subject,'email.candidate.interview-feedback', (object) $data, $attachments, $fromAddress, $senderName,$cc);

		}
     		// 		Mail::to(auth()->user()->email)->cc($cc)->send(new InterviewFeedbackMail((object) $data));

		if(count($cc) > 0){
			foreach($cc as $email){
				$client_company_id = 0;
				$useremail = DB::table('users')->select('*')->where('email',$email)->first();
				if (!empty($useremail)) {
					$client_company_id = @$useremail->company_id;
				}
				// dd($client_company_id);
				if (@$client_company_id > 0) {
					$event_name = 'emails';
					$event_table_name = 'job applications stage change';
					$event_table_id = @$client_company_id;
					GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $client_company_id);
				}
			}
		}
		GeneralMethod::interviewTrackerAddEditUpload($interview_id);
		$cachekeys = [
            'allinterviewschedule_version_user_'.$this->user->id,
        ];
        foreach ($cachekeys as $key) {
            GeneralMethod::cacheClean($key);
        }
        //   dd("hi");

		return Reply::dataOnly(['status' => 'success', 'msg' => 'Interview feedback update successfully.']);


	}
	public function interviewDirectClientData(Request $request)
	{
		$available_status = '';
		$feedback_status = '';
		$interview_mode_arr = '';

		$company_id = auth()->user()->company_id;
		$available_status = GeneralMethod::available_status();
		$feedback_status = GeneralMethod::feedback_status();
		$interview_mode_arr = GeneralMethod::interview_mode();



		$shedule = OpenworkspaceInterviewSchedule::
			join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
			->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
			->join('jobs', function($join){
				$join->on('jobs.id', 'add_job_wise_condidates.job_id');
			})
			->leftjoin('users', 'users.id', 'job_applications.submitted_by')
			// ->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
			->leftjoin('companies', 'companies.id', 'jobs.company_id')
			->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')
			->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
			->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
			->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage')
			->select(
				'interview_schedules.id',
				'job_applications.full_name',
				'interview_schedules.status',
				'interview_schedules.schedule_date',
				'interview_schedules.available_status',
				'interview_schedules.reschedule',
				'interview_schedules.feedback',
				'interview_schedules.interview_mode',
				'interview_schedules.interviewers',
				'interview_schedules.interview_type',
				'interview_schedules.schedule_from_time',
				'users.name as source',
				'interview_schedules.job_application_id',
				'master_status.name as master_status_name',
				'candidate_status.name as child_status_name',
				'jobs.title as job_title',
				'jobs.id as job_id',
				'companies.company_name',

				'job_applications.current_designation',
				'interview_mode.name as mode_name',
				'interview_mode_lavel.name as mode_lavel_name',
				'master_status.css_style',

				'jobs.company_id'
			);
		$shedule = $shedule->where('job_applications.thirdparty_company_id', $this->user->company_id)
		->where('recommendation_relation.job_id', '>', 0)
		->where('interview_schedules.stage', '!=', 8);;
		$shedule = $shedule->where('add_job_wise_condidates.job_id', $request->hid_job_id);

		if ($request->startDate !== null && $request->startDate != 'null') {
			$shedule = $shedule->where(DB::raw('DATE(interview_schedules.`schedule_date`)'), '>=', "$request->startDate");
		}

		if ($request->endDate !== null && $request->endDate != 'null') {
			$shedule = $shedule->where(DB::raw('DATE(interview_schedules.`schedule_date`)'), '<=', "$request->endDate");
		}
		$shedule->groupBy('job_applications.id');
		$shedule->orderBy('interview_schedules.schedule_date', 'DESC');
		$shedule->get();

		return DataTables::of($shedule)

			->editColumn('chkbox', function ($row) {

				return '<div class="form-check form-check-sm form-check-custom form-check-solid">
								<input class="form-check-input" type="checkbox" value="1" />
							  </div>';

			})
			->editColumn('Name', function ($row) {


				$bgTextDecore = $strVal = '';
				$str = trim(@$row->full_name);
				$str = $strVal = strtolower($str[0]);
				$str = ord($str);
				if ($str >= 97 && $str <= 101) {
					$bgTextDecore = 'fs-3 bg-light-danger text-danger';
				} else if ($str >= 102 && $str <= 106) {
					$bgTextDecore = 'fs-3 bg-light-warning text-warning';
				} else if ($str >= 107 && $str <= 111) {
					$bgTextDecore = 'fs-3 bg-light-primary text-primary';
				} else if ($str >= 112 && $str <= 116) {
					$bgTextDecore = 'fs-3 bg-light-success text-success';
				} else if ($str >= 117 && $str <= 121) {
					$bgTextDecore = 'fs-3 bg-dark text-secondary';
				} else {
					$bgTextDecore = 'fs-3 bg-light-secondary text-dark';
				}

				return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="' . $row->job_application_id . '" class="show-application-detail">
                      <div data-row-id="' . $row->job_application_id . '"  class="symbol-label ' . $bgTextDecore . ' border-radius-5 show-application-detail">' . ucwords($strVal) . '</div>
                    </a>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="' . $row->job_application_id . '" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">' . ucwords(substr(@$row->full_name, 0, 20)) . '</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' . ucwords(@$row->current_designation) . '">' . ucwords(substr(@$row->current_designation, 0, 20)) . '</span>
                  </div>';

			})
			->editColumn('Requisitions', function ($row) {

				return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' . ucwords($row->job_title) . '">' . ucwords(substr($row->job_title, 0, 7)) . '</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' . ucwords($row->company_name) . '">' . ucwords(substr($row->company_name, 0, 7)) . '</span>
                  </div>';

			})
			->editColumn('InterviewMode', function ($row) {

				return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . ucwords(substr($row->mode_name, 0, 15)) . '</a> 
                    <span class="text-muted fw-bold text-muted d-block fs-7">' . ucwords(substr($row->mode_lavel_name, 0, 15)) . '</span> 
                  </div>';

			})
			->editColumn('Interviewers', function ($row) {


				$interviewers = $html = $company_id = "";
				$interviewers = $row->interviewers;

				$company_id = $row->company_id;

				$html = '<div class="d-flex flex-column"><div class="symbol-group symbol-hover">------</div></div>';

				if (!empty($interviewers)) {
					$trimmed_array = explode(',', $interviewers);
					$trimmed_array = array_map('trim', $trimmed_array);

					$html = '<div class="d-flex flex-column"> 
                  									<div class="symbol-group symbol-hover"> ';

					$InterviewArr = DB::table('users')
						->select('users.name', 'users.email', 'users.mobile', 'users.image')
						->where('users.company_id', '=', $company_id)
						->whereIn('users.name', $trimmed_array)
						->get();

					foreach ($InterviewArr as $idata) {

						$imagePath = "";
						$imagePath = url('public/avatar.png');
						if (!empty(@$idata->image)) {
							$imagePath = url('public/user-uploads/team_profile_img/' . @$idata->image);
						}

						$html .= '<div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="' . @$idata->name . '" data-original-title="One">
														<img alt="Pic" src="' . @$imagePath . '">
													</div>';
					}


					$html .= '</div>
                  							  	</div>';
				}

				return $html;

			})
			->editColumn('Source', function ($row) {


				$bgTextDecore = $strVal = '';
				$str = trim(@$row->source);
				$str = $strVal = strtolower($str[0]);
				$str = ord($str);
				if ($str >= 97 && $str <= 101) {
					$bgTextDecore = 'fs-3 bg-light-danger text-danger';
				} else if ($str >= 102 && $str <= 106) {
					$bgTextDecore = 'fs-3 bg-light-warning text-warning';
				} else if ($str >= 107 && $str <= 111) {
					$bgTextDecore = 'fs-3 bg-light-primary text-primary';
				} else if ($str >= 112 && $str <= 116) {
					$bgTextDecore = 'fs-3 bg-light-success text-success';
				} else if ($str >= 117 && $str <= 121) {
					$bgTextDecore = 'fs-3 bg-dark text-secondary';
				} else {
					$bgTextDecore = 'fs-3 bg-light-secondary text-dark';
				}

				return ' <div class="d-flex flex-column">
								<div class="symbol-group symbol-hover">
									<div class="symbol symbol-30 symbol-circle symbol-light border-radius-5" style="float:left">
										<a href="javascript:void(0);" data-row-id="' . $row->job_application_id . '">
											<div data-row-id="' . $row->job_application_id . '"  class="symbol-label ' . $bgTextDecore . ' border-radius-5 show-application-detail" style="border-radius: 50%!important;font-size: 13px!important;font-weight: 600;">' . ucwords($strVal) . '</div>
										</a>
									</div>
								</div>
                  			</div>

                    		<span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' . ucwords(@$row->source) . '">' . ucwords(substr(@$row->source, 0, 10)) . '</span>';


			})
			->editColumn('InterviewDate', function ($row) {

				return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . Carbon::parse($row->schedule_date)->format('d M, Y') . '</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7">' . Carbon::parse($row->schedule_date)->format('h:i a') . '</span>
                  </div>';

			})
			->editColumn('Feedback', function ($row) use ($feedback_status, $available_status) {


				$available_status_name = '';
				if ($row->available_status > 0) {
					$available_status_name = @$available_status[@$row->available_status]['name'];
				} else {
					$available_status_name = @$available_status[6]['name'];
				}


				return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="text-dark fw-bolder text-hover-primary fs-6 show-feedback" data-bs-toggle="modal" data-bs-target="#interview_feedback">' . ucwords(substr(@$feedback_status[@$row->feedback]['name'], 0, 20)) . '</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7">' . ucwords(substr(@$available_status_name, 0, 20)) . '</span>
                  </div>';

			})
			->editColumn('Rating', function ($row) {

				$interviewId = $row->id;
				$imagePath = "";
				$imagePath = url('public/stars.png');
				return ' <img src="' . $imagePath . '" alt="image" style="width: 5.5rem">
                    <span class="text-muted fw-bold text-muted d-block fs-7">Above Avarage</span>';

			})
			->editColumn('Stage', function ($row) {

				return '<div class="d-flex flex-column"> 
                        <span class="badge fw-bolder fs-6 px-2 py-1 ' . @$row->css_style . '">' . substr(@$row->master_status_name, 0, 10) . '</span>
                        <span class="text-muted fw-bold text-muted d-block fs-7">' . substr(@$row->child_status_name, 0, 10) . '</span>
                    </div>';

			})

			->rawColumns(['chkbox', 'Name', 'Requisitions', 'InterviewMode', 'Interviewers', 'Source', 'InterviewDate', 'Feedback', 'Rating', 'Stage'])
			->make(true);
	}


}