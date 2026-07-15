<?php

/***#######################################################
Controller    	: 	AdminAppTrackerController
Purpose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/

namespace App\Http\Controllers\ServiceCompany;

use App\Models\ApplicationStatus;
use App\Helper\Files;
use App\Helper\Reply;
use App\Helper\GeneralMethod;
use App\Http\Requests\InterviewSchedule\StoreRequest;
use App\Http\Requests\StoreJobApplication;
use App\Http\Requests\UpdateJobApplication;
use App\InterviewSchedule;
use App\InterviewScheduleEmployee;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobApplicationAnswer;
use App\Models\JobLocation;
use App\Models\JobQuestion;
use Illuminate\Support\Facades\Gate;
use App\Notifications\CandidateScheduleInterview;
use App\Notifications\ScheduleInterview;
use App\Models\Skill;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;
use App\Jobs\SendEmailJob;
use App\Jobs\QueueMailService;
use App\Jobs\QueueMassMailService;
use App\Jobs\QueueNotificationMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\NoticePeriod;
use App\Models\JobSkill;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\Comment;
use Illuminate\Support\Facades\Mail;
use App\Mail\BenchCandidatLeadMail;
use App\Exports\AppTrackerExport;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\MailService;
use App\Services\MassMailService;
use App\Services\NotificationMailService;
use App\Jobs\ElasticCompanyIndex;
use App\Services\ElasticService;
use App\Jobs\ElasticCandidateIndex;

class ServiceCompanyAppTrackerController extends ServiceCompanyBaseController
{
	protected $mailService;
	protected $massMailService;
	protected $notificationMailService;
	protected $elasticService;
	public function __construct(MailService $mailService, MassMailService $massMailService, NotificationMailService $notificationMailService,ElasticService $elasticService)
	{
		parent::__construct();
		$this->pageTitle = __('Applied Candidates');
		$this->pageIcon = 'icon-user';
		$this->mailService = $mailService;
		$this->massMailService = $massMailService;
		$this->notificationMailService = $notificationMailService;
		$this->elasticService = $elasticService;
	}
	public function index($id, $mstatus_id)
	{
		//abort_if(Gate::denies('view_job_applications'), 403);			
		$this->skillData = array();
		$this->statusArr = array();
		$this->candStatusClass = array();
		$id = GeneralMethod::value_decrypt($id);

		// $params = [
		// 	'index' => ELASTIC_CANDIDATES,
		// 	'id'    => 63801,
		// ];
		
		// $response = $this->elasticService->getClient()->get($params);
		// $data = (object) $response['_source'];
		// dd($data);


		if ($id > 0) {
			$this->boardColumns = ApplicationStatus::all();
			$this->locations = JobLocation::all();
			//$this->jobs 					=	Job::all();
			$this->skills = Skill::all();
			$this->clients = Client::all();
			//$this->users 					=	User::company()->get();
			$this->noticeperiod = NoticePeriod::all();
			$this->job = Job::findOrFail($id);
			//echo '<pre>'; print_r($this->job); exit;
			if ($mstatus_id != 'all') {
				$this->statusArr = ApplicationStatus::findOrFail($mstatus_id);
				$this->candStatusClass = GeneralMethod::candidateStatusColorCode($this->statusArr->name);
			} else {
				$this->candStatusClass = GeneralMethod::candidateStatusColorCode('all');
			}
			if ($this->job->client_id > 0) {
				$this->jobclient = Client::findOrFail($this->job->client_id);
				$contactHtml = '';
				$this->client_loc_arr = DB::table('client_location_jobs')->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->join('countries', 'countries.id', '=', 'client_location.country_id')->select('client_location.address', 'client_location.city', 'client_location.postcode', 'countries.country_name')
					->where('client_location_jobs.job_id', $id)->get();
			}
			$this->skillData = DB::table('job_skills')->join('skills', 'job_skills.skill_id', '=', 'skills.id')->select('skills.id', 'skills.name')->where('job_skills.job_id', '=', $id)->orderBy('skills.name', 'ASC')->get();
			$this->mstatus_id = $mstatus_id;
			$this->job_id = $id;

		} else {
			$this->boardColumns = ApplicationStatus::all();
			$this->locations = JobLocation::all();
			$this->jobs = Job::all();
			$this->skills = Skill::all();
			$this->clients = Client::all();
			//$this->users 					=	User::company()->get();
			$this->noticeperiod = NoticePeriod::all();

		}


		$this->pageTitle = __('Candidates Recommended');
		$company_id = auth()->user()->company_id;
		$this->job = Job::findOrFail($id);

		$this->custome_count = $this->custome_count($id);
		
		$return_data = GeneralMethod::custome_count_all($id);
		$this->pool_counter = $return_data->pool_counter;
		$this->existing_counter = $return_data->existing_counter;
		$this->internal_bench_counter = $return_data->internal_bench_counter;		
		$this->public_bench_counter = $return_data->public_bench_counter;	
		$this->recomnded_counter = $return_data->recomnded_counter;	
		$this->career_counter = $return_data->career_counter;
		//dd($return_data->existing_counter);
		// dd($this->custome_count);
		session()->put('job_id', $id);

		$this->jobclient = Client::findOrFail($this->job->client_id);



		return view('service_company.apptracker.index', $this->data);
	}
	public function loadcounter(Request $request)
	{
		// dd("hi");
		$company_id = auth()->user()->company_id;

		$jobid = $request->jobid;
		$mstatus_id = $request->mstatus_id;
		$inputSearch = $request->inputSearch;

		$params = [
			'index' => ELASTIC_CANDIDATES,
			'body' => [
				'size' => 0,
				'query' => [
					'bool' => [
				'should' => [
					// First condition:
					[
						'bool' => [
							'must' => [
								['term' => ['jobId' => $jobid]],
								['term' => ['company_id' => $company_id]],
							],
							'must_not' => [
								['range' => ['ClientComID' => ['gt' => 0]]]
							]
						],
					],
					
					// Second condition:
					[
						'bool' => [
							'must' => [
								['term' => ['jobId' => $jobid]],
								['term' => ['company_id' => $company_id]],
								['range' => ['ClientComID' => ['gt' => 0]]],
							],
							'must_not' => [
								['terms' => ['job_master_status_id' => [8,9]]]
							]
						],
					],
				],
				'minimum_should_match' => 1,
				'filter' => [
					// ['term' => ['job_id' => $jobID]]
				],
			]
				],
				'aggs' => [
					'allCandidate' => [
						'value_count' => [
							'field' => 'id'
						]
					],

					'Sourcing' => [
						'filter' => [
							'term' => ['job_master_status_id' => 1]
						]
					],

					'Screening' => [
						'filter' => [
							'term' => ['job_master_status_id' => 2]
						]
					],

					'Interview' => [
						'filter' => [
							'term' => ['job_master_status_id' => 3]
						]
					],

					'Negotiation' => [
						'filter' => [
							'term' => ['job_master_status_id' => 4]
						]
					],

					'Offered' => [
						'filter' => [
							'term' => ['job_master_status_id' => 5]
						]
					],

					'Joined' => [
						'filter' => [
							'term' => ['job_master_status_id' => 6]
						]
					],

					'Rejected' => [
						'filter' => [
							'term' => ['job_master_status_id' => 7]
						]
					],
				]
			]
		];

		$response = $this->elasticService->getClient()->search($params);

		$applicationCounter = [
			'allCandidate' => $response['aggregations']['allCandidate']['value'] ?? 0,
			'Sourcing'     => $response['aggregations']['Sourcing']['doc_count'] ?? 0,
			'Screening'    => $response['aggregations']['Screening']['doc_count'] ?? 0,
			'Interview'    => $response['aggregations']['Interview']['doc_count'] ?? 0,
			'Negotiation'  => $response['aggregations']['Negotiation']['doc_count'] ?? 0,
			'Offered'      => $response['aggregations']['Offered']['doc_count'] ?? 0,
			'Joined'       => $response['aggregations']['Joined']['doc_count'] ?? 0,
			'Rejected'     => $response['aggregations']['Rejected']['doc_count'] ?? 0,
		];
		$allCounter = $response['aggregations']['allCandidate']['value'] ?? 0;

		// dd($applicationCounter, $allCounter);

		return Reply::dataOnly(['status' => 'success', 'data' => $applicationCounter, 'allCounter' => $allCounter]);

	}
	public function allDataAjax(Request $request)
	{
		// dd("hi");
		// abort_if(Gate::denies('view_job_applications'), 403);		
		$company_id = $mstatus_id = $jobid = $feedback_status = '';

		// GeneralMethod::JobApplicationsAddEditUpload(1440);

		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();
		$company_id = auth()->user()->company_id;
		$mstatus_id = $request->mstatus_id;
		$jobid = $request->jobid;

		$interviewStatus = array();
		$interviewStatus = DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();

		$chkAdminRole = $this->user->hasRole('admin');

		// $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();

		$feedback_status = GeneralMethod::feedback_status();

		$searchTerms = request('cus_search');
		$custom_search = $searchTerms; //json_decode($searchTerms, true);

		$searchQuery = $request->input('q');
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;
		$valCheck = $request->input('sortingfilter');
		$mstatus_id = $request->input('mstatus_id');
		$sort = [];
		$country = '';
		$filters = [];
		$customOrder = [8, 1, 2, 3, 4, 5, 6, 7];

		// Job's own required skills (job_metronics_skills — same source
		// skill_percentage() reads for the row Match % badge), used by the
		// "Higher Match" sort option below to rank candidates against this
		// job specifically. Fetched once here, not per-row.
		$jobSkillsForSort = DB::table('job_metronics_skills')->where('job_id', $jobid)->pluck('skill_name')->map(fn($s) => strtolower(trim($s)))->values()->toArray();

		$cachePayload = [
			'company' => $company_id,
			'page' => $page,
			'size' => $size,
			'jobid' => $jobid,
			'mstatus_id' => $mstatus_id,
			'sortingfilter' => $valCheck,
			'cus_search' => $custom_search,
			'user_id' => $this->user->id,
			'q' => $searchQuery
		];

		// dd($cachePayload );
		
		$versionKey = 'allapptracker_version_user_'.$this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:allapptracker:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
		/* ----------------------------
		⚡ Check DragonflyDB first
		----------------------------- */
		if (cache()->has($cacheKeySet)) {
			return response()->json(cache()->get($cacheKeySet));
		}
		
		

		switch ($valCheck) {
			case 1:
				$sort[] = ['created_at' => ['order' => 'desc']];
				break;
			case 2:
				$sort[] = ['created_at' => ['order' => 'asc']];
				break;
			case 3:
				$sort[] = ['ectc' => ['order' => 'asc', 'unmapped_type' => 'float']];
				break;
			case 4:
				$sort[] = ['ectc' => ['order' => 'desc', 'unmapped_type' => 'float']];
				break;
			case 5: // Notice period ASC (ignore Immediate)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return 999999;
								}
								try {
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {}
								return 999999;
							"
						],
						'order' => 'asc'
					]
				];
				break;
			case 6: // Notice period DESC (ignore Immediate)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return -1;
								}
								try {
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {}
								return -1;
							"
						],
						'order' => 'desc'
					]
				];
				break;
			case 21: // Notice period Date ASC
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return 999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return 999999999; }
							def v = raw.trim();

							// Immediate should come first
							if (v.equals('Immediate')) { return -999999998; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return 999999999; // not a date -> last
									}
								}
							}
						"
					],
					'order' => 'asc'
				]
			];
			break;

			case 22: // Notice period Date DESC
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return -999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return -999999999; }
							def v = raw.trim();

							// Immediate should come after valid dates (but before null/non-date)
							if (v.equals('Immediate')) { return -888888888; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return -999999999; // not a date -> last
									}
								}
							}
						"
					],
					'order' => 'asc'
				]
			];
			break;

			case 9: // Immediate joiners first
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "params['_source']['immediate_join'] == 'Immediate' ? 0 : 1"
						],
						'order' => 'asc'
					]
				];
				break;
			case 7:
				$sort[] = ['total_exp' => ['order' => 'desc', 'unmapped_type' => 'float']];
				break;
			case 8:
				$sort[] = ['total_exp' => ['order' => 'asc', 'unmapped_type' => 'float']];
				break;
			case 10: // Higher Match — stage grouping (In-Review → Sourcing →
				// Screening → ... → Rejected) STAYS the primary sort, same
				// as the default view; this only replaces the secondary
				// "id desc" tiebreak within each stage with match ranking,
				// so a Screening candidate can never outrank an In-Review
				// one just for matching better — it only reorders who's
				// shown first WITHIN In-Review, WITHIN Sourcing, etc.
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								def orderMap = ['8':0, '1':1, '2':2, '3':3, '4':4, '5':5, '6':6, '7':7];
								if (doc.containsKey('job_master_status_id') && !doc['job_master_status_id'].empty) {
									String val = doc['job_master_status_id'].value.toString();
									if (orderMap.containsKey(val)) {
										return orderMap[val];
									}
								}
								return 999;
							"
						],
						'order' => 'asc'
					]
				];
				// Ranks by how many of the job's own required skills
				// (job_metronics_skills) this candidate's skill list
				// satisfies. doc['...keyword'].contains(s) checks EXACT list
				// membership, not substring — so a required "java" never
				// matched a candidate skill of "Java Developer" (case
				// mismatch AND no phrase-prefix), leaving the ranking
				// uncorrelated with the Match % badge shown on the row
				// (which uses skill_percentage()'s phrase-prefix, case-
				// insensitive comparison). Reading params._source directly
				// and doing a lowercase substring check per candidate skill
				// mirrors that same logic closely enough to sort in the
				// order the badge implies.
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								def candidateSkills = [];
								if (params._source.containsKey('job_application_skills')) {
									for (item in params._source.job_application_skills) {
										if (item.containsKey('skills') && item.skills != null) {
											candidateSkills.add(item.skills.toString().toLowerCase());
										}
									}
								}
								int matchCount = 0;
								for (req in params.skills) {
									boolean found = false;
									for (cand in candidateSkills) {
										if (cand.contains(req)) { found = true; break; }
									}
									if (found) { matchCount += 1; }
								}
								return matchCount;
							",
							'params' => [
								'skills' => array_values($jobSkillsForSort)
							]
						],
						'order' => 'desc'
					]
				];
				break;
			default:
				// Custom job_master_status_id order
				$orderMapScript = "
					def orderMap = ['8':0, '1':1, '2':2, '3':3, '4':4, '5':5, '6':6, '7':7];
					if (doc.containsKey('job_master_status_id') && !doc['job_master_status_id'].empty) {
						String val = doc['job_master_status_id'].value.toString();
						if (orderMap.containsKey(val)) {
							return orderMap[val];
						}
					}
					return 999;
				";

				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => $orderMapScript
						],
						'order' => 'asc'
					]
				];
				break;
		}

		// Apply fallback sort by ID descending
		$sort[] = ['id' => ['order' => 'desc', 'unmapped_type' => 'long']];


		
		if ($mstatus_id != 'all') {
			$filters[] = ['term' => ['job_master_status_id' => (int) $mstatus_id]];
		}
		
		$boolQuery = [
			'bool' => [
				'should' => [
					// First condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['company_id' => $company_id]],
								['term' => ['jobId' => $jobid]],
								['term' => ['ClientComID' => 0]],
							], $this->getSearchConditions($custom_search, $country))
						]
					],
					// Second condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['company_id' => $company_id]],
								['term' => ['jobId' => $jobid]],
								['range' => ['ClientComID' => ['gt' => 0]]],
							], [
								['bool' => [
									'must_not' => [
										['terms' => ['job_master_status_id' => [8,9]]]
									]
								]]
							], $this->getSearchConditions($custom_search, $country))
						]
					],
					// Third condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['ClientComID' => $company_id]],
								['term' => ['jobId' => $jobid]],
								['range' => ['job_id' => ['gt' => 0]]],
							], $this->getSearchConditions($custom_search, $country))
						]
					],
				],
				'minimum_should_match' => 1,
				'filter' => $filters // We'll add all filters here
			]
		];

		if ($valCheck==21 || $valCheck==22) {
			$boolQuery['bool']['filter'][] = [
				'term' => [
					'job_master_status_id' => (int) 8
				]
			];
		}

		if ($valCheck==22) {
			$today = date('Y-m-d');  
			$next30 = date('Y-m-d', strtotime('+30 days'));

			// Serving candidates filter
			$servingCandidateFilter = [
				'script' => [
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) return false;

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) return false;

							def v = raw.trim();

							if (v.equals('Immediate')) return false; // exclude 'Immediate'

							int comma = v.indexOf(',');
							if (comma > -1) v = v.substring(0, comma).trim();

							int t = v.indexOf('T');
							if (t > -1) v = v.substring(0, t);

							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								long epoch = d.toEpochDay();
								long today = java.time.LocalDate.parse(params.today).toEpochDay();
								long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

								return epoch >= today && epoch <= next30;
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									long epoch = d.toEpochDay();
									long today = java.time.LocalDate.parse(params.today).toEpochDay();
									long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

									return epoch >= today && epoch <= next30;
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										long epoch = d.toEpochDay();
										long today = java.time.LocalDate.parse(params.today).toEpochDay();
										long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

										return epoch >= today && epoch <= next30;
									} catch (Exception e3) {
										return false; // not a valid date
									}
								}
							}
						",
						'params' => [
							'today'  => $today,
							'next30' => $next30
						]
					]
				]
			];
		    // Attach serving candidate condition along with others
            $boolQuery['bool']['filter'][] = $servingCandidateFilter;
		}



		// Build the final query
		$params = [
			'index' => ELASTIC_CANDIDATES,
			'request_cache' => true,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => $sort,
				'query' => $boolQuery
			]
		];

		if (!$this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
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
		// dd($pagination);
		$userPermissions = $this->userPermissions;
		$responseData = [
			'html' => view('service_company.ajax_pages.all_apptracker', compact('data', 'userPermissions'))->render(),
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		];

		cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_APPTRACKER_TIME));

	return response()->json($responseData);

	
	}
	public function similar_candidates($id)
	{
		$id = GeneralMethod::value_decrypt($id);

		$lock_unlock = GeneralMethod::checksubscription();
		if ($lock_unlock == false) {
			return redirect()->route('service_company.dashboard')->with('message_paid', 'This is a paid featute');
			;
		}

		$this->pageTitle = __('Candidates Recommended');
		$company_id = auth()->user()->company_id;
		$this->job = Job::findOrFail($id);

		$this->custome_count = $this->custome_count($id);
		$return_data = GeneralMethod::custome_count_all($id);
		$this->pool_counter = $return_data->pool_counter;
		$this->existing_counter = $return_data->existing_counter;
		$this->internal_bench_counter = $return_data->internal_bench_counter;		
		$this->public_bench_counter = $return_data->public_bench_counter;
		$this->recomnded_counter = $return_data->recomnded_counter;		
		$this->career_counter = $return_data->career_counter;;
		// dd($this->custome_count);
		session()->put('job_id', $id);



		$searchTerms = request('cus_search');
		$custom_search = json_decode($searchTerms, true);

		$job = $this->job;
		$jobid = $job->id;

		// Single shared source (GeneralMethod::jobDefaultsFromEs(), ES only)
		// for a job's default skills/location/experience/salary — used here
		// AND by custome_count_all() (the header counter on every other
		// AppTracker tab), so the two can't drift out of sync the way
		// gdFilterData()/JobcitiesTags() (MySQL) vs custome_count_all() (ES)
		// did: a job edited in MySQL with the ES reindex lagging even
		// briefly used to make this page and the header counter compute
		// against genuinely different data, disagreeing on a fresh load
		// before any filter was touched.
		$jobDefaults = GeneralMethod::jobDefaultsFromEs($id);

		$this->default_loc = $jobDefaults->default_loc;
		// All configured locations for this job, not just the first — the
		// Similar Candidates filter drawer uses this array to pre-select
		// every configured city.
		$this->default_locations = $jobDefaults->default_locations;
		$this->qualification = '';//GeneralMethod::allQualificationSelected($jobid);
		$this->min_exp = ($jobDefaults->min_exp >= 1) ? $jobDefaults->min_exp - 1 : $jobDefaults->min_exp;
		$this->max_exp = $jobDefaults->max_exp + 1;
		$this->min_salary_currency = $jobDefaults->min_salary_currency;
		// Every AppTracker filter drawer defaults Min Salary to 0, not the
		// job's own min_salary (see custome_count_all()'s salary-floor
		// comments) — the header counter and this page have to agree on
		// the same floor or their candidate counts disagree on load.
		$this->min_salary = 0;
		$this->max_salary = $jobDefaults->max_salary;
		$this->min_sal_mode = $jobDefaults->min_sal_mode;
		$this->default_skill = $jobDefaults->default_skill;

		// dd($jobid,$title,$min_exp,$max_exp,$min_salary,$max_salary);

		// $this->loc_data = DB::table('client_location_jobs')
		// 	->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
		// 	->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
		// 	->where('client_location_jobs.job_id', $jobid)
		// 	->pluck('city_list.city as city_name')
		// 	->toArray();

		// 	$this->default_loc = implode(",",$this->loc_data);


		$skill_arr = array();
		$skill_data = DB::table('job_metronics_skills')->select('*')->where('job_id', $jobid)->get()->toArray();
		foreach ($skill_data as $sval) {
			$skill_arr[] = $sval->skill_name;
		}
		// $this->default_skill = implode(",",$skill_arr);

		$this->skill_data = $skill_data;
		$this->js_array = 'whitelist: ["' . implode('", "', $skill_arr) . '"]';

		$this->cities = GeneralMethod::allCities();
		$this->qualificationTag = DB::table('qualification_master')
			->select('*')
			->whereNotIn('id',[60])
			->orderBy('qualification_master.display_order', 'ASC')
			->get()
			->map(function ($row) {
				return $row->name;
			});


		//  dd( $js_array);
		// dd($skill_arr ,$jobid,$title,$min_exp,$max_exp,$min_salary,$max_salary);

		return view('service_company.apptracker.similar_candidates', $this->data);
	}
	public function similar_existing_data_ajax(Request $request)
	{



		// $requiredSkills = ['.Net', 'Angular.js', 'Angular', '.net core', 'Web API', 'MVC', 'C#'];
		// $requiredSkills = ['laravel', 'php', 'html'];
		$requiredSkills = [];
		$jobid = $request->jobid;
		$company_id = auth()->user()->company_id;
		$job_status = GeneralMethod::job_status();
		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();

		$searchTerms = request('cus_search');
		$custom_search = json_decode($searchTerms, true);
		if (\request('cus_search') != "") {
			$skillsAddElastic = array_column($custom_search, 'value');
			GeneralMethod::skillsAddElastic($skillsAddElastic);
		}

		// $mandatorySkills tracks only the starred/checked skills (used to
		// build a strict "must match" clause below). $requiredSkills is
		// EVERY entered skill, checked or not — it previously got
		// overwritten to contain ONLY the checked skills the moment any
		// skill was starred, which silently dropped every other typed
		// skill from filtering AND from the relevance-sort script AND
		// from the "Match %" badge (all three read $requiredSkills). A
		// recruiter starring one skill out of four was unknowingly
		// discarding the other three everywhere, not just marking one as
		// required. $requiredSkills now always reflects everything typed;
		// $mandatorySkills is the separate, additive "must have" set.
		$mandatorySkills = [];
		if (\request('cus_search') != "") {

			$checkedSkills = array_filter($custom_search, function ($skill) {
				return $skill['checked'] == true;
			});

			$uncheckedSkills = array_filter($custom_search, function ($skill) {
				return $skill['checked'] == false;
			});

			$requiredSkills = array_column($custom_search, 'value');
			$mandatorySkills = array_column($checkedSkills, 'value');

		}

		// dd($requiredSkills,$mandatorySkills,$custom_search,$checkedSkills,$uncheckedSkills);

		$location = request('kt_tagify_location');
		$custom_location = json_decode($location, true);

		$education = request('kt_tagify_education');
		$custom_education = json_decode($education, true);


		$job = Job::findOrFail($jobid);
		$title = $job->title;

		$current_date = date('Y-m-d H:i:s');
		$current_timestamp = strtotime($current_date);
		$current_previous_30_days = date('Y-m-d H:i:s', strtotime('-30 days', $current_timestamp));
		$current_previous_60_days = date('Y-m-d H:i:s', strtotime('-60 days', $current_timestamp));
		$current_previous_90_days = date('Y-m-d H:i:s', strtotime('-90 days', $current_timestamp));
		$current_previous_180_days = date('Y-m-d H:i:s', strtotime('-180 days', $current_timestamp));
		$current_previous_1_year = date('Y-m-d H:i:s', strtotime('-365 days', $current_timestamp));
		$app_int_activity = request('app_int_activity');
		$app_int_status = request('app_int_status');
		$app_int_notice = request('app_int_notice');
		$sal_mode = request('sal_mode') ?? $job->max_sal_mode;
		$min_salary_currency = request('min_salary_currency') ?? $job->max_salary_currency;

		
		$symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
		$symbolsValue = !empty($symbolsValue) ? $symbolsValue : [
			0	=>	"INR",
			1	=>	"EUR",
			2	=>	"JPY",
			3	=>	"GBP",
			4	=>	"USD",
			5	=>	"AED"
		];

		$modeFilter = $sal_mode ?? $job->max_sal_mode;
		$currencyFilter = $min_salary_currency ?? $job->max_salary_currency;
		$symbolsFilter = $symbolsValue[$currencyFilter] ?? "INR";


		if (\request('min_exp') != "" || \request('max_exp') != "") {
			$min_exp = request('min_exp');
			$max_exp = request('max_exp');
		} else {
			$min_exp = '';//$job->min_exp;
			$max_exp = '';//$job->max_exp;
		}

		$currency = GeneralMethod::currency();  // Currency code
		$currencyCheck = $currency[$min_salary_currency];

		if (\request('min_sal') != "" || \request('max_sal') != "") {
			$AmountMin = request('min_sal');
			$AmountMax = request('max_sal');
			$min_salary = $AmountMin;
			$max_salary = $AmountMax;
			if ($min_salary_currency > 0) {
				$min_salary = GeneralMethod::convertToINR($AmountMin, $currencyCheck);
				$max_salary = GeneralMethod::convertToINR($AmountMax, $currencyCheck);
			}
			// $ctcdividedMin = GeneralMethod::salaryDividedByMode($sal_mode, $AmountMin);
			// $ctcdividedMax = GeneralMethod::salaryDividedByMode($sal_mode, $AmountMax);
			// if ($sal_mode == 0) {
			// 	$min_salary = $ctcdividedMin->yearly;
			// 	$max_salary = $ctcdividedMax->yearly;

			// } elseif ($sal_mode == 1) {
			// 	$min_salary = $ctcdividedMin->monthly;
			// 	$max_salary = $ctcdividedMax->monthly;

			// } elseif ($sal_mode == 2) {
			// 	$min_salary = $ctcdividedMin->hourly;
			// 	$max_salary = $ctcdividedMax->hourly;

			// }

		} else {
             
			$convertedAmount = request('max_sal');//$job->max_salary;
			$max_salary  = $convertedAmount;
			$min_salary = 0;//$job->min_salary;
			if ($min_salary_currency > 0) {
				$max_salary = GeneralMethod::convertToINR($convertedAmount, $currencyCheck);
			}

			// $ectcdivided = GeneralMethod::salaryDividedByMode($sal_mode, $convertedAmount);
			// // $max_salary = $job->max_salary;	
			// if ($sal_mode == 0) {
			// 	$max_salary = $ectcdivided->yearly;

			// } elseif ($sal_mode == 1) {
			// 	$max_salary = $ectcdivided->monthly;

			// } elseif ($sal_mode == 2) {
			// 	$max_salary = $ectcdivided->hourly;

			// }

			// dd("dkd");

		}
		$location = array();
		$locationCityNames = array();
		if (\request('kt_tagify_location') != "") {
			foreach ($custom_location as $sval) {
				$parts = explode('|', $sval['value']);
				// Trim to remove any leading or trailing whitespace
				$cityName = trim($parts[0]);
				$locationCityNames[] = $cityName;
				$loc_data = DB::table('city_list')->where('city_list.city', '=', $cityName)->first();
				if ($loc_data) {
					array_push($location, $loc_data->city_id);
				}

			}
		}

		// Delhi NCR-style metro clustering: widen $location to include every
		// city in the same cluster as any selected city (see
		// resolveLocationCluster() / config/location_clusters.php). The
		// existing "if (!empty($location))" filter block below is untouched
		// — it now just filters on the expanded set automatically. Exact
		// selections still rank first via the priority sort added further
		// down, and $exactLocationIds is threaded through to the response
		// mapping so the UI can show a "Nearby" badge on cluster-only rows.
		$exactLocationIds = array_map('intval', $location);
		$locationCluster = GeneralMethod::resolveLocationCluster($exactLocationIds, $locationCityNames);
		$location = $locationCluster['expanded'];
		$locationClusterExpanded = count($locationCluster['expanded']) > count($locationCluster['exact']);
		$education = array();
		if (\request('kt_tagify_education') != "") {
			foreach ($custom_education as $sval) {
				$education_data = DB::table('qualification_master')->where('qualification_master.name', '=', $sval['value'])->first();
				if ($education_data) {
					$educationfiletr = GeneralMethod::qualificationFilterCondition($education_data->id);
					$education = array_merge($education, $educationfiletr);
				}
			}
		}

		// (A $relevanceCase block used to sit here, building a MySQL-style
		// "CASE WHEN ... LIKE" string from the same checked/unchecked skill
		// split computed above — it was never read anywhere after being
		// built, since this method queries Elasticsearch, not MySQL. Removed
		// as dead code rather than left to confuse future readers into
		// thinking skill relevance is scored via a SQL CASE expression.)


		$valCheck = $request->input('sortingfilter');
		$country = '';
		$searchQuery = $request->input('q');
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;
        // dd(array_values($requiredSkills));
		$requiredSkills = array_map('strtolower', $requiredSkills);
		$mandatorySkills = array_map('strtolower', $mandatorySkills);


		// Computed here (not down where the search filter clause is built)
		// specifically so it can be included in $cachePayload below — the
		// cache key is derived from this payload, and a parameter left out
		// of it means two different searches with identical other filters
		// would collide on the same cache key and one could silently serve
		// the other's stale results.
		$freeTextSearch = trim((string) $request->input('search', ''));

		$cachePayload = [
			'company' => $company_id,
			'page' => $page,
			'size' => $size,
			'q' => $searchQuery,
			'search' => $freeTextSearch,
			'jobid' => $jobid,
			'cus_search' => $requiredSkills ?? [],
			'sortingfilter' => $valCheck,
			'min_exp' => $min_exp,
			'max_exp' => $max_exp,
			'min_salary_currency' => $min_salary_currency,
			'education' => $education ?? [],
			'location' => $location ?? [],
			'min_sal' => $min_salary,
			'max_sal' => $max_salary,
			'app_int_status'=>$app_int_status,
			'app_int_activity'=>$app_int_activity,
			'app_int_notice'=>$app_int_notice,
			'user_id' => $this->user->id,
			'sal_mode' =>$sal_mode
		];

		// dd($cachePayload );
		
		$versionKey = 'allsimilarcandidates_version_user_'.$this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:allsimilarcandidates:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
		/* ----------------------------
		⚡ Check DragonflyDB first
		----------------------------- */
		if (cache()->has($cacheKeySet)) {
			// return response()->json(cache()->get($cacheKeySet));
		}



		// dd($requiredSkills);

		if($valCheck !=21 && $valCheck !=22) {
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => '
							int matchCount = 0;
							if (doc.containsKey("job_application_skills.skills.keyword")) {
								for (s in params.skills) {
									if (doc["job_application_skills.skills.keyword"].contains(s)) {
										matchCount += 1;
									}
								}
							}
							return matchCount;
						',
						'params' => [
							'skills' => array_values($requiredSkills)
						]
					],
					'order' => 'desc'
				]
			];
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
			case 5: // Notice period ASC (ignore Immediate)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return 999999; // Put missing/Immediate at the end
								}
								try {
									// Extract first number from comma-separated values
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {
									// If parsing fails
								}
								return 999999; // Invalid values at the end
							"
						],
						'order' => 'asc'
					]
				];
				break;
			case 6: // Notice period DESC (ignore Immediate)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return -1; // Put missing/Immediate at the end
								}
								try {
									// Extract first number from comma-separated values
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {
									// If parsing fails
								}
								return -1; // Invalid values at the end
							"
						],
						'order' => 'desc'
					]
				];
				break;
			case 21: // Notice period Date ASC
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return 999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return 999999999; }
							def v = raw.trim();

							// Immediate should come first
							if (v.equals('Immediate')) { return -999999998; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return 999999999; // not a date -> last
									}
								}
							}
						"
					],
					'order' => 'asc'
				]
			];
			break;

			case 22: // Notice period Date DESC
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return -999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return -999999999; }
							def v = raw.trim();

							// Immediate should come after valid dates (but before null/non-date)
							if (v.equals('Immediate')) { return -888888888; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return -999999999; // not a date -> last
									}
								}
							}
						"
					],
					'order' => 'asc'
				]
			];
			break;
			case 7:
				$sort[] = ['total_exp' => ['order' => 'desc', 'unmapped_type' => 'float']];
				break;
			case 8:
				$sort[] = ['total_exp' => ['order' => 'asc', 'unmapped_type' => 'float']];
				break;
			case 9: // Immediate joiners first (original functionality)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "params['_source']['immediate_join'] == 'Immediate' ? 0 : 1"
						],
						'order' => 'asc'
					]
				];
				break;
			default:
				$sort[] = ['id' => ['order' => 'desc', 'unmapped_type' => 'long']];
				break;
		}

		// Metro-cluster expansion (see resolveLocationCluster() above) widens
		// the location filter to include nearby cities, but the recruiter's
		// exact pick should still come first. Prepending this makes it the
		// PRIMARY sort key — every criterion the switch above just built
		// still applies, but only as the tie-breaker within "exact" and
		// within "nearby" separately, not across the two groups. Only added
		// when expansion actually happened (empty city name, or a city with
		// no configured cluster, both leave $locationClusterExpanded false),
		// so there's zero extra query cost for jobs/searches that never
		// touch a clustered city.
		if (!empty($locationClusterExpanded)) {
			array_unshift($sort, [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => 'return params.exactCityIds.contains((int) doc["city_id"].value) ? 0 : 1;',
						'params' => [
							'exactCityIds' => array_values($exactLocationIds)
						]
					],
					'order' => 'asc'
				]
			]);
		}

		$mustSubmittedBy = [];

		$custom_search = "";
    
		// dd($custom_search);

		// $skillsList = array_filter($requiredSkills, fn($s) => $s !== 'skill');

		$boolQuery = [
			'bool' => [
				'should' => [
					// First condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['company_id' => $company_id]],
								['term' => ['condidate_type' => 'other']],
								['term' => ['ClientComID' => 0]],
							], $this->getSearchConditions($custom_search, $country))
						]
					],
					// Second condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['company_id' => $company_id]],
								['term' => ['condidate_type' => 'other']],
								['range' => ['ClientComID' => ['gt' => 0]]],
								['range' => ['job_id' => ['gt' => 0]]],
							], [
								['bool' => [
									'must_not' => [
										['terms' => ['job_master_status_id' => [8,9]]]
									]
								]]
							], $this->getSearchConditions($custom_search, $country))
						]
					],
					// Third condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['ClientComID' => $company_id]],
								['term' => ['condidate_type' => 'other']],
								['range' => ['job_id' => ['gt' => 0]]],
							], $this->getSearchConditions($custom_search, $country))
						]
					],
				],
				'minimum_should_match' => 1,
				'filter' => [], // We'll add all filters here
				'must_not' => [
					['term' => ['job_master_status_id' => 5]],
					['term' => ['job_master_status_id' => 6]]
				]
			]
		];

		// Starred/mandatory skills are a hard requirement (must — every one of
		// them has to match). Skills typed but left unstarred are additive
		// once at least one skill is starred: they still boost relevance
		// (the sort script and the Match % badge both read $requiredSkills,
		// which includes them) but are never required on their own. This
		// matches how "mandatory keyword" reads in Naukri-style search —
		// required PLUS preferred, not required INSTEAD OF preferred, which
		// is what the previous must/should EITHER-OR branch actually did:
		// starring one skill silently discarded every other typed skill
		// from filtering entirely.
		if (!empty($mandatorySkills)) {
			$mustSkills = [];
			foreach ($mandatorySkills as $skill) {
				$mustSkills[] = GeneralMethod::buildSkillMatchClause($skill);
			}

			$skillBool = ['must' => $mustSkills];

			$optionalSkills = array_values(array_diff($requiredSkills, $mandatorySkills));
			if (!empty($optionalSkills)) {
				$shouldSkills = [];
				foreach ($optionalSkills as $skill) {
					$shouldSkills[] = GeneralMethod::buildSkillMatchClause($skill);
				}
				// No minimum_should_match here on purpose — these are
				// preferred, not required; adding a minimum would turn them
				// back into a second hard requirement layered on the must.
				$skillBool['should'] = $shouldSkills;
			}

			$boolQuery['bool']['filter'][] = [
				'bool' => $skillBool
			];
		} elseif (!empty($requiredSkills)) {
			// Nothing starred: unchanged original behaviour — match at
			// least one of the entered skills.
			$should = [];

			foreach ($requiredSkills as $skill) {
				$should[] = GeneralMethod::buildSkillMatchClause($skill);
			}

			$boolQuery['bool']['filter'][] = [
							'bool' => [
								'should' => $should,
								'minimum_should_match' => 1
							]
			];
		}


			$range = [];

			// Minimum experience
			if ($min_exp !== '' && $min_exp !== null) {
				$range['gte'] = (float) $min_exp;
			}

			// Maximum experience
			if ($max_exp !== '' && $max_exp !== null) {
				$range['lte'] = (float) $max_exp;
			}

			// Add filter query
		if (!empty($range)) {
			$boolQuery['bool']['filter'][] = [
				'range' => [
					'total_exp' => $range
				]
			];
		}
		
		
		// Salary filter
		// dd($min_salary,$max_salary);
		$salaryField = match($sal_mode) {
				0 => 'ectc_yearly',
				1 => 'ectc_monthly',
				2 => 'ectc_hourly',
				default => 'ectc_yearly'
			};

			if (($min_salary !== null && $min_salary !== '' && $min_salary != 0.00) || ($max_salary !== null && $max_salary !== '' && $max_salary != 0.00)) {

				$salaryRange = [];

				// Minimum salary
				if ($min_salary !== null && $min_salary !== '' && $min_salary != 0.00) {
					$salaryRange['gte'] = (float) $min_salary;
				}

				// Maximum salary
				if ($max_salary !== null && $max_salary !== '' && $max_salary != 0.00) {
					$salaryRange['lte'] = (float) $max_salary;
				}

				// Inclusive of candidates with no salary recorded: a plain
				// `range` filter only matches documents that HAVE the field,
				// so a candidate missing salary data would silently vanish
				// from the results the instant any salary filter was
				// applied, with no indication why. Matching either "salary
				// is in range" OR "salary field is missing entirely" keeps
				// those candidates visible instead of penalizing them for a
				// data gap that isn't their fault.
				$boolQuery['bool']['filter'][] = [
					'bool' => [
						'should' => [
							['range' => [$salaryField => $salaryRange]],
							['bool' => ['must_not' => [['exists' => ['field' => $salaryField]]]]],
						],
						'minimum_should_match' => 1,
					]
				];
			}

		// Education filter
		if (!empty($education)) {
			$boolQuery['bool']['filter'][] = [
				'terms' => [
					'qualification' => $education
				]
			];
		}
		
		// Status filter
		if (!empty($app_int_status) && (int)$app_int_status > 0) {
			$boolQuery['bool']['filter'][] = [
				'term' => [
					'job_master_status_id' => (int)$app_int_status
				]
			];
		}
		
		// Notice period filter
		if (!empty($app_int_notice) && (int)$app_int_notice > 1) {
			$noticeFilter = $this->getNoticePeriodFilter($app_int_notice, $current_date);
			if ($noticeFilter) {
				$boolQuery['bool']['filter'][] = $noticeFilter;
			}
		}
		
		// Activity filter
		if (!empty($app_int_activity)) {
			$activityFilter = $this->getActivityFilter(
				$app_int_activity,
				$current_previous_30_days,
				$current_previous_60_days,
				$current_previous_90_days,
				$current_previous_180_days,
				$current_previous_1_year,
				$current_date
			);
			if ($activityFilter) {
				$boolQuery['bool']['filter'][] = $activityFilter;
			}
		}
		
		// Skills filter

		

		$existingCandidatesQuery = DB::table('add_job_wise_condidates')
			->where('job_id', '=', $jobid)->pluck('job_application_id')->toArray();

			if (!empty($existingCandidatesQuery)) {
					$boolQuery['bool']['must_not'][] = [
						'terms' => [
							'candidate_id' => $existingCandidatesQuery
						]
					];
			}

			$existingMap = array_flip($existingCandidatesQuery);
			

		$boolQuery['bool']['must_not'][] = [
			'terms' => [
				'job_master_status_id' => [5, 6]
			]
		];
         
		if (!empty($location)) {
			
			$boolQuery['bool']['filter'][] = [
				'terms' => [
					 'city_id' => array_map('intval', $location)
				]
			];
			$boolQuery['bool']['filter'][] = [
				'exists' => [
					'field' => 'city_id'
				]
			];
			$boolQuery['bool']['filter'][] = [
				'range' => [
					'city_id' => [
						'gt' => 0
					]
				]
			];

		}

		// Free-text search box (candidate name, recruiter/ownership, client
		// or company name, job title/position, location, skills, and Job ID).
		// Appended to the SAME $boolQuery['bool']['filter'] array every other
		// filter above uses — that array is AND-ed by Elasticsearch, so this
		// narrows whatever the other active filters already matched instead
		// of searching the full unfiltered candidate pool. ($freeTextSearch
		// itself is computed earlier, alongside $cachePayload, so it's part
		// of the cache key.)
		if ($freeTextSearch !== '') {
			$searchShould = [
				[
					'multi_match' => [
						'query' => $freeTextSearch,
						'type' => 'best_fields',
						'fuzziness' => 'AUTO',
						'fields' => [
							'full_name^3',
							'posted_by^2',
							'client_name',
							'company_name',
							'title^2',
							'city',
							'job_application_skills.skills^2',
						],
					],
				],
			];

			// job_id is a numeric field — a term/match query against it
			// errors on non-numeric input rather than just finding zero
			// matches, so only add this clause when the typed text is
			// purely digits.
			if (ctype_digit($freeTextSearch)) {
				$searchShould[] = [
					'term' => ['job_id' => (int) $freeTextSearch],
				];
			}

			$boolQuery['bool']['filter'][] = [
				'bool' => [
					'should' => $searchShould,
					'minimum_should_match' => 1,
				],
			];
		}

		if ($valCheck==22) {
			$today = date('Y-m-d');  
			$next30 = date('Y-m-d', strtotime('+30 days'));

			// Serving candidates filter
			$servingCandidateFilter = [
				'script' => [
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) return false;

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) return false;

							def v = raw.trim();

							if (v.equals('Immediate')) return false; // exclude 'Immediate'

							int comma = v.indexOf(',');
							if (comma > -1) v = v.substring(0, comma).trim();

							int t = v.indexOf('T');
							if (t > -1) v = v.substring(0, t);

							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								long epoch = d.toEpochDay();
								long today = java.time.LocalDate.parse(params.today).toEpochDay();
								long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

								return epoch >= today && epoch <= next30;
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									long epoch = d.toEpochDay();
									long today = java.time.LocalDate.parse(params.today).toEpochDay();
									long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

									return epoch >= today && epoch <= next30;
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										long epoch = d.toEpochDay();
										long today = java.time.LocalDate.parse(params.today).toEpochDay();
										long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

										return epoch >= today && epoch <= next30;
									} catch (Exception e3) {
										return false; // not a valid date
									}
								}
							}
						",
						'params' => [
							'today'  => $today,
							'next30' => $next30
						]
					]
				]
			];
		    // Attach serving candidate condition along with others
            $boolQuery['bool']['filter'][] = $servingCandidateFilter;
		}

		// dd($boolQuery);

			// dd($existingCandidatesQuery);


			// Build the final query
			$params = [
				'index' => ELASTIC_CANDIDATES,
				'request_cache' => true,
				'body' => [
					'from' => $from,
					'size' => $size,
					'sort' => $sort,
					'query' => $boolQuery,
					'collapse' => [
						'field' => 'email'
					]
				]
			];

			if (!$this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
				$total = 0;
				$data = [];
			}else{
				$response = $this->elasticService->getClient()->search($params);
				// This total reflects ALL matching docs BEFORE collapse
				$totalBeforeCollapse = $response['hits']['total']['value'] ?? 0;

				// Count of current page records (after pagination & collapse)
				$records = $response['hits']['hits'] ?? [];
				$returnedCount = count($records);

				// Optional: if you want real total of unique candidate_id (after collapse), use an aggregation
				$aggParams = [
					'index' => ELASTIC_CANDIDATES,
					'body' => [
						'size' => 0,
						'query' => $boolQuery,
						'aggs' => [
							'unique_candidates' => [
								'cardinality' => [
									'field' => 'email'
								]
							]
						]
					]
				];
				$aggResponse = $this->elasticService->getClient()->search($aggParams);
				$total = $aggResponse['aggregations']['unique_candidates']['value'] ?? $returnedCount;
		
				$data = array_map(function ($hit) use ($requiredSkills, $mandatorySkills, $exactLocationIds) {

				$source = $hit['_source'];
				$jobSkills = collect(data_get($source, 'job_application_skills', []))
				->pluck('skills')
				->filter()
				->unique()
				->toArray();

					$matchedSkills = GeneralMethod::matchedSkillsList($jobSkills, $requiredSkills);
				    $matchCount = count($matchedSkills);
					// Split out of matched_skills specifically: which matches
					// were against a STARRED/mandatory requirement vs just an
					// entered-but-not-starred one. A recruiter reading the
					// row should be able to tell "this candidate has the
					// skill I marked mandatory" apart from "this candidate
					// happens to have one of my other entered skills too" —
					// both currently rendered with the identical highlight.
					$matchedMandatorySkills = GeneralMethod::matchedSkillsList($jobSkills, $mandatorySkills);
					// True when this candidate is only in the results because
					// of metro-cluster expansion (e.g. a Noida candidate
					// surfaced by a Gurgaon search) — lets the UI show a
					// "Nearby" badge instead of silently mixing them in with
					// the exact-location matches.
					$candidateCityId = isset($source['city_id']) ? (int) $source['city_id'] : null;
					$isNearbyLocation = !empty($exactLocationIds)
						&& $candidateCityId !== null
						&& !in_array($candidateCityId, $exactLocationIds, true);
					return array_merge($source, [
						'matched_skill_count' => $matchCount,
						'matched_skills' => $matchedSkills,
						'matched_mandatory_skills' => $matchedMandatorySkills,
						// The candidate's FULL skill list (already computed above
						// for the intersect, just not previously exposed) — lets
						// the results row show every skill the candidate has,
						// highlighting the ones that match the job instead of
						// only showing the matched subset and hiding the rest.
						'all_skills' => $jobSkills,
						'is_nearby_location' => $isNearbyLocation
					]);
				}, $response['hits']['hits']);
				
			}
			
			$pagination = [
				'total' => $total,
				'current_page' => $page,
				'last_page' =>ceil($total / $size),
				'listType' => 'allcandidate',
			];

			$userPermissions = $this->userPermissions;
			$app_int_activity = request('app_int_activity');
			$app_int_status = request('app_int_status');
			$app_int_notice = request('app_int_notice');
			$sal_mode = request('sal_mode');
			$min_salary_currency = request('min_salary_currency');
			$app_int_outsource = request('app_int_outsource');
			// Lets the empty-state message in the results partial distinguish
			// "no candidates at all for this job" from "no matches for your
			// current filters/search" — a plain "No data available" reads as
			// a possible bug once search is AND-ed onto every other filter,
			// since an over-filtered zero-result state is now more common.
			$hasActiveFilters = !empty($requiredSkills) || !empty($location) || !empty($education)
				|| $min_exp !== '' || $max_exp !== ''
				|| (!empty($app_int_status) && (int) $app_int_status > 0)
				|| (!empty($app_int_notice) && (int) $app_int_notice > 1)
				|| !empty($app_int_activity)
				|| ($min_salary !== null && $min_salary !== '' && $min_salary != 0.00)
				|| ($max_salary !== null && $max_salary !== '' && $max_salary != 0.00);
			$search = $freeTextSearch;
			// dd($data);
			$responseData  =[
				'html' => view('service_company.ajax_pages.similar_existing_ajax', compact('data','userPermissions','app_int_status','app_int_activity','app_int_notice','sal_mode','min_salary_currency','app_int_outsource','jobid','requiredSkills','modeFilter','currencyFilter','symbolsFilter','hasActiveFilters','search'))->render(),
				'total'=>$total,
				'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
			];


		cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_SIMILAR_CANDIDATES_TIME));

		return response()->json($responseData);


	}



	// Helper method for search conditions
	protected function getSearchConditions($custom_search, $country) {
		$conditions = [];

		// Apply custom search if it's not empty
		if (!empty($custom_search)) {
			$conditions[] = [
				'multi_match' => [
					'query' => $custom_search,
					'type' => 'best_fields', // or 'most_fields' depending on your needs
					'fields' => [
						'full_name^3',  // Boost full_name matches higher
						'email^2',
						'phone',
						'immediate_join',
						'title',
						'country_name',
						'qualification_name',
						'state_name',
						'city_name',
						'posted_by',
						'client_or_company_name',
						'skills'
					],
					'operator' => 'or',
					'fuzziness' => 'AUTO' // Optional: enables fuzzy matching
				]
			];
		}
	
		
	
		return $conditions;

	}

	// Helper method for notice period filter
	protected function getNoticePeriodFilter($app_int_notice, $current_date)
	{
		switch ($app_int_notice) {
			case 8: // Immediate
				return [
					'bool' => [
						'must' => [
							['term' => ['np_id.keyword' => 'Immediate']],
							['term' => ['serving_notice_period_mode' => 2]]
						]
					]
				];
			case 2: // 0-15 days
				return [
					'bool' => [
						'must' => [
							['range' => ['np_id' => ['gte' => 0, 'lte' => 15]]],
							['term' => ['serving_notice_period_mode' => 0]]
						]
					]
				];
			case 3: // 0-30 days
				return [
					'bool' => [
						'must' => [
							['range' => ['np_id' => ['gte' => 0, 'lte' => 30]]],
							['term' => ['serving_notice_period_mode' => 0]]
						]
					]
				];
			case 4: // 0-60 days
				return [
					'bool' => [
						'must' => [
							['range' => ['np_id' => ['gte' => 0, 'lte' => 60]]],
							['term' => ['serving_notice_period_mode' => 0]]
						]
					]
				];
			case 5: // 0-90 days
				return [
					'bool' => [
						'must' => [
							['range' => ['np_id' => ['gte' => 0, 'lte' => 90]]],
							['term' => ['serving_notice_period_mode' => 0]]
						]
					]
				];
			case 6: // Any notice period
				return [
					'bool' => [
						'must' => [
							['range' => ['np_id' => ['gt' => 0]]],
							['term' => ['serving_notice_period_mode' => 0]]
						]
					]
				];
			case 7: // After current date
				return [
					'bool' => [
						'must' => [
							['range' => ['np_id' => ['gt' => $current_date]]],
							['term' => ['serving_notice_period_mode' => 1]]
						]
					]
				];
			case 9: // Before current date
				return [
					'bool' => [
						'must' => [
							['range' => ['np_id' => ['lt' => $current_date]]],
							['term' => ['serving_notice_period_mode' => 1]]
						]
					]
				];
			default:
				return null;
		}
	}
	protected function getActivityFilter($app_int_activity, $days30, $days60, $days90, $days180, $days365, $current_date)
	{
		switch ($app_int_activity) {
			case 2: // Last 30 days
				return ['range' => ['updated_at' => ['gte' => $days30, 'lte' => $current_date]]];
			case 3: // Last 60 days
				return ['range' => ['updated_at' => ['gte' => $days60, 'lte' => $current_date]]];
			case 4: // Last 90 days
				return ['range' => ['updated_at' => ['gte' => $days90, 'lte' => $current_date]]];
			case 5: // Last 180 days
				return ['range' => ['updated_at' => ['gte' => $days180, 'lte' => $current_date]]];
			case 6: // Last 365 days
				return ['range' => ['updated_at' => ['gte' => $days365, 'lte' => $current_date]]];
			default:
				return null;
		}
	}
	
	public function clientapp($id, $mstatus_id)
	{

		$id = GeneralMethod::value_decrypt($id);
		$company_id = auth()->user()->company_id;

		$this->skillData = array();
		$this->statusArr = array();
		$this->candStatusClass = array();

		if ($mstatus_id != 'all') {
			$this->statusArr = ApplicationStatus::findOrFail($mstatus_id);
			$this->candStatusClass = GeneralMethod::candidateStatusColorCode($this->statusArr->name);
		} else {
			$this->candStatusClass = GeneralMethod::candidateStatusColorCode('all');
		}


		$this->job = DB::table('recommendation_relation')
			->select('jobs.id', 'jobs.title', 'jobs.client_id', 'jobs.status', 'recommendation_relation.id as recommendation_relation_id', 'recommendation_relation.final_status')
			->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
			->where('jobs.id', '=', $id)
			->where('recommendation_relation.to_company_id', '=', $company_id)
			//->where('recommendation_relation.final_status', '=', 1)
			->where(function ($query) {
				$query->where('recommendation_relation.final_status', '=', 1)
					->orWhere('recommendation_relation.final_status', '=', 2);
			})
			->first();
		$this->final_status_job = $this->job->final_status;
		// dd($this->final_status_job);


		$this->skillData = DB::table('job_skills')
			->join('skills', 'job_skills.skill_id', '=', 'skills.id')
			->select('skills.id', 'skills.name')->where('job_skills.job_id', '=', $id)
			->orderBy('skills.name', 'ASC')
			->get();

		$this->mstatus_id = $mstatus_id;
		$this->job_id = $id;
		


		// $this->applicationCounter = DB::table('job_applications')
		// 	->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')

		// 	->where('add_job_wise_condidates.job_id', '=', $id)
		// 	->where('add_job_wise_condidates.thirdparty_company_id', auth()->user()->company_id)
		// 	->select(
		// 		DB::raw("count(add_job_wise_condidates.id) as allCandidate"),
		// 		DB::raw("SUM(CASE WHEN add_job_wise_condidates.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing"),
		// 		DB::raw("SUM(CASE WHEN add_job_wise_condidates.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening"),
		// 		DB::raw("SUM(CASE WHEN add_job_wise_condidates.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview"),
		// 		DB::raw("SUM(CASE WHEN add_job_wise_condidates.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation"),
		// 		DB::raw("SUM(CASE WHEN add_job_wise_condidates.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered"),
		// 		DB::raw("SUM(CASE WHEN add_job_wise_condidates.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined"),
		// 		DB::raw(
		// 			"SUM(CASE WHEN add_job_wise_condidates.master_status_id = 7 THEN 1 WHEN add_job_wise_condidates.master_status_id = 9 THEN 1 ELSE 0 END) AS Rejected"
		// 		)
		// 	)
		// 	->groupBy('add_job_wise_condidates.job_id')
		// 	->first();

			

			$params = [
			'index' => ELASTIC_CANDIDATES,
			'body' => [
				'size' => 0,
				'query' => [
					'bool' => [
					'must' => [
						// First condition:
						[
							'bool' => [
								'must' => [
									['term' => ['jobId' => $id]],
									['term' => ['ClientComID' => auth()->user()->company_id]],
								]
							],
						]
				],
				'filter' => [
					// ['term' => ['job_id' => $jobID]]
				],
			]
				],
				'aggs' => [
					'allCandidate' => [
						'value_count' => [
							'field' => 'id'
						]
					],

					'Sourcing' => [
						'filter' => [
							'term' => ['job_master_status_id' => 1]
						]
					],

					'Screening' => [
						'filter' => [
							'term' => ['job_master_status_id' => 2]
						]
					],

					'Interview' => [
						'filter' => [
							'term' => ['job_master_status_id' => 3]
						]
					],

					'Negotiation' => [
						'filter' => [
							'term' => ['job_master_status_id' => 4]
						]
					],

					'Offered' => [
						'filter' => [
							'term' => ['job_master_status_id' => 5]
						]
					],

					'Joined' => [
						'filter' => [
							'term' => ['job_master_status_id' => 6]
						]
					],

					'Rejected' => [
						'filter' => [
							'term' => ['job_master_status_id' => 7]
						]
					],
				]
			]
		];

		$response = $this->elasticService->getClient()->search($params);

		$this->applicationCounter = (object) [
			'allCandidate' => $response['aggregations']['allCandidate']['value'] ?? 0,
			'Sourcing'     => $response['aggregations']['Sourcing']['doc_count'] ?? 0,
			'Screening'    => $response['aggregations']['Screening']['doc_count'] ?? 0,
			'Interview'    => $response['aggregations']['Interview']['doc_count'] ?? 0,
			'Negotiation'  => $response['aggregations']['Negotiation']['doc_count'] ?? 0,
			'Offered'      => $response['aggregations']['Offered']['doc_count'] ?? 0,
			'Joined'       => $response['aggregations']['Joined']['doc_count'] ?? 0,
			'Rejected'     => $response['aggregations']['Rejected']['doc_count'] ?? 0,
		];
		// dd($this->applicationCounter);




		$this->job = DB::table('jobs')
			->select('jobs.id', 'jobs.title', 'jobs.min_exp', 'jobs.max_exp', 'jobs.min_salary', 'jobs.max_salary', 'jobs.min_sal_mode', 'jobs.max_sal_mode', 'jobs.min_salary_currency', 'jobs.max_salary_currency', 'jobs.total_positions', 'jobs.status', 'recommendation_relation.final_status', 'jobs.client_id', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city')
			->leftJoin('recommendation_relation', function ($join) use ($company_id) {
				$join->on('recommendation_relation.job_id', '=', 'jobs.id');
				$join->on('recommendation_relation.to_company_id', '=', DB::raw("'" . $company_id . "'"));
				//$join->on('recommendation_relation.final_status', '=', DB::raw("'1'"));
			})
			->join('clients', 'clients.id', 'jobs.client_id')
			->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
			->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
			->where(function ($query) use ($company_id, $id) {
				return
					$query->where('recommendation_relation.final_status', '=', 1)
						->where('recommendation_relation.to_company_id', '=', $company_id)
						->where('jobs.id', '=', $id);
			})
			->orWhere(function ($query) use ($company_id, $id) {
				return
					$query->where('recommendation_relation.final_status', '=', 2)
						->where('recommendation_relation.to_company_id', '=', $company_id)
						->where('jobs.id', '=', $id);
			})
			->first();


		$this->dinternal_count = $this->dinternal_count($id, $company_id);
		$return_data = GeneralMethod::custome_count_all($id);
		$this->pool_counter = $return_data->pool_client_counter;
		$this->existing_counter = $return_data->existing_counter;
		$this->internal_bench_counter = $return_data->internal_bench_counter;		
		$this->career_counter = $return_data->career_counter;;
		// $this->jobclient = Client::findOrFail($this->job->client_id);
		return view('service_company.apptracker.clientapp', $this->data);

	}
	public function clientappdata_ajax(Request $request)
	{

		$company_id = auth()->user()->company_id;

		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();
		$searchTerms = request('cus_search');
		$custom_search = $searchTerms; //json_decode($searchTerms, true);
		$mstatus_id = '';
		$mstatus_id = $request->mstatus_id;
		$interviewStatus = array();
		$interviewStatus = DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();
		$chkAdminRole = $this->user->hasRole('admin');
		// $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
		$feedback_status = '';
		$feedback_status = GeneralMethod::feedback_status();
		$jobid = @$request->jobid;


		$searchQuery = $request->input('q');
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;
		$valCheck = $request->input('sortingfilter');
		$mstatus_id = $request->input('mstatus_id');
		$sort = [];
		$country = '';
		$filters = [];
		$customOrder = [8, 1, 2, 3, 4, 5, 6, 7];

		$cachePayload = [
			'page' => $page,
			'size' => $size,
			'q' => $searchQuery,
			'company' => $company_id,
			'mstatus_id' => $mstatus_id,
			'valCheck' => $valCheck,
			'user_id' => $this->user->id,
			'custom_search' => $custom_search
		];
		
		
		$versionKey = 'allclientapp_version_user_'.$this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:allclientapp:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
		/* ----------------------------
		⚡ Check DragonflyDB first
		----------------------------- */
		if (cache()->has($cacheKeySet)) {
			return response()->json(cache()->get($cacheKeySet));
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
			case 5: // Notice period ASC (ignore Immediate)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return 999999;
								}
								try {
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {}
								return 999999;
							"
						],
						'order' => 'asc'
					]
				];
				break;
			case 6: // Notice period DESC (ignore Immediate)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return -1;
								}
								try {
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {}
								return -1;
							"
						],
						'order' => 'desc'
					]
				];
				break;
			case 21: // Notice period Date ASC
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return 999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return 999999999; }
							def v = raw.trim();

							// Immediate should come first
							if (v.equals('Immediate')) { return -999999998; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return 999999999; // not a date -> last
									}
								}
							}
						"
					],
					'order' => 'asc'
				]
			];
			break;

			case 22: // Notice period Date DESC
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return -999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return -999999999; }
							def v = raw.trim();

							// Immediate should come after valid dates (but before null/non-date)
							if (v.equals('Immediate')) { return -888888888; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return -999999999; // not a date -> last
									}
								}
							}
						"
					],
					'order' => 'desc'
				]
			];
			break;
			case 7:
				$sort[] = ['total_exp' => ['order' => 'desc', 'unmapped_type' => 'float']];
				break;
			case 8:
				$sort[] = ['total_exp' => ['order' => 'asc', 'unmapped_type' => 'float']];
				break;
			case 9: // Immediate joiners first
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "params['_source']['immediate_join'] == 'Immediate' ? 0 : 1"
						],
						'order' => 'asc'
					]
				];
				break;
			default:
				// ✅ Custom job_master_status_id order
				$orderMapScript = "
					def orderMap = ['8':0, '1':1, '2':2, '3':3, '4':4, '5':5, '6':6, '7':7];
					if (doc.containsKey('job_master_status_id') && !doc['job_master_status_id'].empty) {
						String val = doc['job_master_status_id'].value.toString();
						if (orderMap.containsKey(val)) {
							return orderMap[val];
						}
					}
					return 999;
				";

				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => $orderMapScript
						],
						'order' => 'asc'
					]
				];
				break;
		}
		$sort[] = ['id' => ['order' => 'desc', 'unmapped_type' => 'long']];
		

		if ($mstatus_id == 7) {
				$status = [7, 9];   // array required
			} else {
				$status = [(int) $mstatus_id];
			}

		if ($mstatus_id != 'all') {
			$filters[] = ['terms' => ['job_master_status_id' => $status]];
		}

		
		$boolQuery = [
			'bool' => [
				'should' => [
					// First condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['ClientComID' => $company_id]],
								['term' => ['jobId' => $jobid]],
							], $this->getSearchConditions($custom_search, $country))
						]
					]
				],
				'minimum_should_match' => 1,
				'filter' => $filters // We'll add all filters here
			]
		];

		if ($valCheck==22) {
			$today = date('Y-m-d');  
			$next30 = date('Y-m-d', strtotime('+30 days'));

			// Serving candidates filter
			$servingCandidateFilter = [
				'script' => [
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) return false;

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) return false;

							def v = raw.trim();

							if (v.equals('Immediate')) return false; // exclude 'Immediate'

							int comma = v.indexOf(',');
							if (comma > -1) v = v.substring(0, comma).trim();

							int t = v.indexOf('T');
							if (t > -1) v = v.substring(0, t);

							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								long epoch = d.toEpochDay();
								long today = java.time.LocalDate.parse(params.today).toEpochDay();
								long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

								return epoch >= today && epoch <= next30;
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									long epoch = d.toEpochDay();
									long today = java.time.LocalDate.parse(params.today).toEpochDay();
									long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

									return epoch >= today && epoch <= next30;
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										long epoch = d.toEpochDay();
										long today = java.time.LocalDate.parse(params.today).toEpochDay();
										long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

										return epoch >= today && epoch <= next30;
									} catch (Exception e3) {
										return false; // not a valid date
									}
								}
							}
						",
						'params' => [
							'today'  => $today,
							'next30' => $next30
						]
					]
				]
			];
		    // Attach serving candidate condition along with others
            $boolQuery['bool']['filter'][] = $servingCandidateFilter;
		}


		// Build the final query
		$params = [
			'index' => ELASTIC_CANDIDATES,
			'request_cache' => true,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => $sort,
				'query' => $boolQuery,
				'aggs' => [
					'status_counts' => [
						'terms' => [
							'field' => 'job_master_status_id'
						]
					]
				]
			]
		];

		if (!$this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
			$total = 0;
			$data = [];
		}else{
			$response = $this->elasticService->getClient()->search($params);
			$total = $response['hits']['total']['value'] ?? 0;
			$data = array_map(function ($hit) {
				return $hit['_source'];
			}, $response['hits']['hits']);

			// Extract status-wise count
			$statusCounts = [];
			if (isset($response['aggregations']['status_counts']['buckets'])) {
				foreach ($response['aggregations']['status_counts']['buckets'] as $bucket) {
					$statusCounts[$bucket['key']] = $bucket['doc_count'];
				}
			}

		}
	
		$pagination = [
			'total' => $total,
			'current_page' => $page,
			'last_page' =>ceil($total / $size),
			'listType' => 'allcandidate',
		];
		// dd($pagination);
		$userPermissions = $this->userPermissions;
		$app_int_activity = request('app_int_activity');
		$app_int_status = request('app_int_status');
		$app_int_notice = request('app_int_notice');
		$sal_mode = request('sal_mode');
		$min_salary_currency = request('min_salary_currency');
		$app_int_outsource = request('app_int_outsource');
		$responseData = [
			'html' => view('service_company.ajax_pages.clientappdata_ajax', compact('data','userPermissions','app_int_status','app_int_activity','app_int_notice','sal_mode','min_salary_currency','app_int_outsource','jobid'))->render(),
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		];

		
		
		cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_CLIENT_APP_TIME));

		return response()->json($responseData);





		$jobApplications = DB::table('job_applications')
			->select(
				'job_applications.*',
				'jobs.title',
				'jobs.max_salary_currency',
				'jobs.max_sal_mode',
				'job_locations.location',
				'application_status.status as a_status',
				'clients.name as client_name',
				'users.name as posted_by',
				'users.image as posted_by_image',
				'qualification_master.name as qualification_name',
				'master_status.name',
				'master_status.css_style',
				'state_list.state as state_name',
				'city_list.city as indian_city_name',
				'master_status.id as available_status',
				'companies.company_name',
				'add_job_wise_condidates.master_status_id as masterstatusid',
				'add_job_wise_condidates.child_status_id as childstatusid',
				'add_job_wise_condidates.id as condidate_id'
			)->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
			->join('master_status', function ($join) {
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
			})
			->join('jobs', function ($join) {
				$join->on('jobs.id', 'add_job_wise_condidates.job_id');
			})
			// ->join('jobs', 'jobs.id', 'job_applications.job_id')
			->join('recommendation_relation', 'recommendation_relation.job_id', 'jobs.id')
			// ->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
			->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')
			->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
			->leftjoin('clients', 'clients.id', 'jobs.client_id')
			->leftjoin('users', 'users.id', 'add_job_wise_condidates.submitted_by')
			->leftjoin('companies', 'companies.id', 'users.company_id')
			->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
			->leftjoin('application_status', 'application_status.id', 'job_applications.status_id')
			->leftjoin('state_list', 'state_list.id', 'job_applications.state')
			->leftjoin('city_list', 'city_list.city_id', 'job_applications.city_id');
		if (\request('search') != "") {
			$search = request('search');
			// $jobApplications = $jobApplications->where('job_applications.full_name', 'LIKE', "%$search%");
		}
		if (\request('cus_search') != "") {

			$jobApplications = $jobApplications->where(function ($query) use ($custom_search, $company_id) {
				$query->where('job_applications.full_name', 'like', "%{$custom_search}%");
				$query->orWhere('job_applications.email', 'like', "%{$custom_search}%");
				$query->orWhere('job_applications.phone', 'like', "%{$custom_search}%");
				$query->orWhere('users.name', 'like', "%{$custom_search}%");
				$query->orWhere('companies.company_name', 'like', "%{$custom_search}%");
			});
		}
		if ($request->mstatus_id != 'all') {
			if ($request->mstatus_id == 7) {
				$jobApplications = $jobApplications->whereIn('add_job_wise_condidates.master_status_id', array(7, 9));
			} else {
				$jobApplications = $jobApplications->where('add_job_wise_condidates.master_status_id', $request->mstatus_id);
			}
		}
		if ($request->jobid != '') {
			$jobApplications = $jobApplications->where('add_job_wise_condidates.job_id', $request->jobid);
		}
		$jobApplications = $jobApplications->where('add_job_wise_condidates.thirdparty_company_id', auth()->user()->company_id);
		$jobApplications = $jobApplications->where('recommendation_relation.to_company_id', auth()->user()->company_id);

		$customOrder = [8, 1, 2, 3, 4, 5, 6, 7];
		if (\request('sortingfilter') != "") {
			$valCheck = request('sortingfilter');
			if ($valCheck == 1) {
				$jobApplications = $jobApplications->orderBy('add_job_wise_condidates.created_at', 'DESC');
			} elseif ($valCheck == 2) {
				$jobApplications = $jobApplications->orderBy('add_job_wise_condidates.created_at', 'ASC');
			} elseif ($valCheck == 3) {
				$jobApplications = $jobApplications->orderBy('job_applications.ctc', 'ASC');
			} elseif ($valCheck == 4) {
				$jobApplications = $jobApplications->orderBy('job_applications.ctc', 'DESC');
			} elseif ($valCheck == 5) {
				$jobApplications = $jobApplications->orderBy('job_applications.np_id', 'ASC');
			} elseif ($valCheck == 6) {
				$jobApplications = $jobApplications->orderBy('job_applications.np_id', 'DESC');
			} elseif ($valCheck == 7) {
				$jobApplications = $jobApplications->orderBy('job_applications.total_exp', 'DESC');
			} elseif ($valCheck == 8) {
				$jobApplications = $jobApplications->orderBy('job_applications.total_exp', 'ASC');
			} elseif ($valCheck == 9) {
				$jobApplications = $jobApplications->orderByRaw("FIELD(job_applications.np_id, 'Immediate') DESC")
					->orderBy('np_id', 'DESC');
			} else {
				$jobApplications = $jobApplications->orderByRaw("FIELD(add_job_wise_condidates.master_status_id, " . implode(',', $customOrder) . ")");
				$jobApplications = $jobApplications->orderby('add_job_wise_condidates.id', 'DESC');
			}

		} else {
			$jobApplications = $jobApplications->orderByRaw("FIELD(add_job_wise_condidates.master_status_id, " . implode(',', $customOrder) . ")");
			$jobApplications = $jobApplications->orderby('add_job_wise_condidates.id', 'DESC');
		}

		$jobApplications = $jobApplications->groupBy('add_job_wise_condidates.id');
		
		$perPage = 10;
		$page = $request->get('page', 1);
		// dd($page);
		$total = count($jobApplications->get());
		// dd($total);
		$data = $jobApplications->skip(($page - 1) * $perPage)->take($perPage)->get();

		// dd($total,$data);
		$jobid = @$request->jobid;

		$pagination = [
			'total' => $total,
			'current_page' => $page,
			'last_page' => ceil($total / $perPage),
			'listType' => 'allcandidate',
		];
		// dd($pagination);
		$userPermissions = $this->userPermissions;
		$app_int_activity = request('app_int_activity');
		$app_int_status = request('app_int_status');
		$app_int_notice = request('app_int_notice');
		$sal_mode = request('sal_mode');
		$min_salary_currency = request('min_salary_currency');
		$app_int_outsource = request('app_int_outsource');

		$applicationCounter = DB::table('job_applications')
			->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')

			->where('add_job_wise_condidates.job_id', '=',$jobid)
			->where('add_job_wise_condidates.thirdparty_company_id', auth()->user()->company_id)
			->select(
				DB::raw("count(add_job_wise_condidates.id) as allCandidate"),
				DB::raw("SUM(CASE WHEN add_job_wise_condidates.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing"),
				DB::raw("SUM(CASE WHEN add_job_wise_condidates.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening"),
				DB::raw("SUM(CASE WHEN add_job_wise_condidates.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview"),
				DB::raw("SUM(CASE WHEN add_job_wise_condidates.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation"),
				DB::raw("SUM(CASE WHEN add_job_wise_condidates.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered"),
				DB::raw("SUM(CASE WHEN add_job_wise_condidates.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined"),
				DB::raw(
					"SUM(CASE WHEN add_job_wise_condidates.master_status_id = 7 THEN 1 WHEN add_job_wise_condidates.master_status_id = 9 THEN 1 ELSE 0 END) AS Rejected"
				)
			)
			->groupBy('add_job_wise_condidates.job_id')
			->first();

			$allCandidateCounter	=	@$applicationCounter->allCandidate > 0 ? @$applicationCounter->allCandidate : 0; 	
			$SourcingCounter		=	@$applicationCounter->Sourcing > 0 ? @$applicationCounter->Sourcing : 0;
			$ScreeningCounter		=	@$applicationCounter->Screening > 0 ? @$applicationCounter->Screening : 0 ;		
			$InterviewCounter		=	@$applicationCounter->Interview > 0 ? @$applicationCounter->Interview : 0;	
			$NegotiationCounter		=	@$applicationCounter->Negotiation > 0 ? @$applicationCounter->Negotiation : 0;	
			$OfferedCounter			=	@$applicationCounter->Offered > 0 ? @$applicationCounter->Offered : 0;	
			$JoinedCounter			=	@$applicationCounter->Joined > 0 ? @$applicationCounter->Joined : 0; 	
			$RejectedCounter		=	@$applicationCounter->Rejected > 0 ? @$applicationCounter->Rejected : 0;


		return response()->json([
			'html' => view('service_company.ajax_pages.clientappdata_ajax', compact('data','userPermissions','app_int_status','app_int_activity','app_int_notice','sal_mode','min_salary_currency','app_int_outsource','jobid'))->render(),
			'allCandidateCounter'=>$allCandidateCounter,
			'SourcingCounter'=>$SourcingCounter,
			'ScreeningCounter'=>$ScreeningCounter,
			'InterviewCounter'=>$InterviewCounter,
			'NegotiationCounter'=>$NegotiationCounter,
			'OfferedCounter'=>$OfferedCounter,
			'JoinedCounter'=>$JoinedCounter,
			'RejectedCounter'=>$RejectedCounter,
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		]);


	}
	public function rec_candidate_assign(Request $request)
	{

		$id = $source_jobid = $source_from_type = $company_id = $source_jobid = "";
		$source_from_company_id = $source_from_user_id = $source_from_id = $source_tab = "";
		$source_company_id = $source_user_id = "";

		$source_company_id = auth()->user()->company_id;
		$source_user_id = auth()->user()->id;

		$applicationsInsertedId = 0;

		$id = @$request->id;
		$source_jobid = @$request->source_jobid;
		$source_from_type = (int) @$request->source_from_type;
		$source_tab = @$request->source_tab;
		$wiseId =  (int) @$request->wiseId;

		// dd($wiseId,$source_from_type);

		$row_data = DB::table('job_applications')->select('*')->where('id', @$id)->first();
		$source_from_company_id = @$row_data->company_id;
		// dd($source_from_company_id);
		// $detailsVendorSide = GeneralMethod::companyAsVendorAndClient($source_company_id,2);
		// dd($request->all(),$detailsVendorSide);

		//######### FOR JOB APPLICANT
		$customClient = 0;
		$jobRow = DB::table('jobs')->select('*')->where('id', $source_jobid)->first();

		if ($source_company_id != $jobRow->company_id) { 								// CHECK DIRECT CLIENT APPLICATION 
			$customClient = 1;
		}

		$submitted_by = '';
		$thirdparty_company_id = '';
		if ($customClient == 1) {														//	DIRECT CLIENT JOB  
			$thirdparty_company_id = auth()->user()->company_id;
		} else {																		// 	CUSTOM CLIENT JOB 
			$thirdparty_company_id = 0;

		}

		$coin_company_id = auth()->user()->company_id;
		//######### FOR EXISTING APPLICANT	
		if ($source_from_type == 0) {

			$eventId = 9;
			$coinsDeductions = GeneralMethod::coinsDeductions((int) $coin_company_id, $eventId, auth()->user()->id, $coin_company_id, '', '');
			if ($coinsDeductions->status) {
				return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
			}



			$row_data = DB::table('job_applications')->select('*')->where('id', $id)->first();
			$source_from_company_id = @$row_data->company_id;
			$source_from_user_id = @$row_data->submitted_by;
			$source_from_id = @$row_data->id;


			$job_application_values = array(
				'np_id' => @$row_data->np_id,
				'full_name' => @$row_data->full_name,

				'email' => @$row_data->email,
				'phone' => @$row_data->phone,
				'gender' => @$row_data->gender,
				'dob' => @$row_data->dob,
				'country' => @$row_data->country,

				'state' => @$row_data->state,
				'city' => @$row_data->city,
				'city_id' => @$row_data->city_id,
				'secondrary_email' => @$row_data->secondrary_email,
				'secondary_mobile' => @$row_data->secondary_mobile,

				'skills' => @$row_data->skills,
				'photo' => @$row_data->photo,
				'resume' => @$row_data->resume,
				'rating' => @$row_data->rating,
				'skype_id' => @$row_data->skype_id,

				'cover_letter' => @$row_data->cover_letter,
				'job_id' => @$source_jobid,
				'status_id' => @$row_data->status_id,
				'is_viewed' => @$row_data->is_viewed,
				'column_priority' => @$row_data->column_priority,

				'total_exp' => @$row_data->total_exp,
				'rel_exp' => @$row_data->rel_exp,
				'passport' => @$row_data->passport,
				'ctc' => @$row_data->ctc,
				'current_ctc_cur' => @$row_data->current_ctc_cur,

				'current_ctc_mode' => @$row_data->current_ctc_mode,
				'ectc' => @$row_data->ectc,
				'current_ectc_cur' => @$row_data->current_ectc_cur,
				'current_ectc_mode' => @$row_data->current_ectc_mode,
				'current_location' => @$row_data->current_location,

				'current_designation' => @$row_data->current_designation,
				'current_company' => @$row_data->current_company,
				'process_id' => @$row_data->process_id,
				'qualification' => @$row_data->qualification,
				'master_status_id' => 8,

				// 'child_status_id' => '',
				'serving_notice_period_mode' => @$row_data->serving_notice_period_mode,
				'proposed_ctc' => @$row_data->proposed_ctc,

				'joined_date' => @$row_data->joined_date,
				'duplicate' => @$row_data->duplicate,
				'percentage_hike' => @$row_data->percentage_hike,
				'comment' => @$row_data->comment,
				'pincode' => @$row_data->pincode,
			);
			// DB::table('job_applications')->insert($job_application_values);

			$application_id = "";
			$application_id = $id; //DB::getPdo()->lastInsertId();



			$timelineArr = array(
				'event_id' => 1,
				'event_by_company_id' => auth()->user()->company_id,
				'event_by_user_id' => auth()->user()->id,
				'master_process_name' => 'InReview',
				'application_id' => @$application_id,
				'application_email' => @$row_data->email,
				'job_id' => @$source_jobid,
				'note' => 'Similar Candidate => Existing'
			);
			GeneralMethod::UpdateTimeline($timelineArr);

			// FOR SAME REQUISITION 
			$parent_id = 0;
			$bCnt = DB::table('rec_candidate_assign')
				->select('id')
				->where('source_company_id', '=', auth()->user()->company_id)
				->where('source_from_type', '=', 1)
				->where('source_application_id', '=', $row_data->id)
				->count();
			if ($bCnt > 0) {
				$bData = DB::table('rec_candidate_assign')
					->select('rec_candidate_assign.id', 'rec_candidate_assign.source_from_id', 'rec_candidate_assign.source_application_id')
					->where('source_company_id', '=', auth()->user()->company_id)
					->where('source_from_type', '=', 1)
					->where('source_application_id', '=', $row_data->id)
					->first();
				$source_from_type = 1;
				$source_from_id = $bData->source_from_id;
				$parent_id = $bData->source_application_id;

			}

			$values = array(
				'parent_id' => @$parent_id,

				'source_from_company_id' => @$source_from_company_id,
				'source_from_user_id' => @$source_from_user_id,
				'source_from_id' => @$source_from_id,
				'source_from_type' => @$source_from_type,

				'source_company_id' => @$source_company_id,
				'source_user_id' => @$source_user_id,
				'source_application_id' => @$application_id,
				'ac_status' => 1,
				'source_jobid' => @$source_jobid
			);
			// dd($values);
			DB::table('rec_candidate_assign')->insert($values);
			$recCandidateAssignId = DB::getPdo()->lastInsertId();

			$skillArr = explode(',', @$row_data->skills);
		    $percentage =  GeneralMethod::skill_match_percentage(@$source_jobid,$skillArr);

			$now = now();
			$insertArr = array('job_id' => @$source_jobid, 'rec_candidate_assign_id' => $recCandidateAssignId, 'job_application_id' => @$application_id, 'status_id' => @$row_data->status_id, 'master_status_id' => 8, 'company_id' => @$jobRow->company_id, 'submitted_by' => @$this->user->id, 'thirdparty_company_id' => @$thirdparty_company_id, 'existing' => 1,'skill_match' => $percentage,'created_at' => $now,'updated_at' => $now);

			DB::table('add_job_wise_condidates')->insert($insertArr);
			$lastWiseIs = DB::getPdo()->lastInsertId();
			
			$cachekeys = [
				'allcvtracker_version_user_'.$this->user->id,				
				'allapptracker_version_user_'.$this->user->id,				
				'allsimilarcandidates_version_user_'.$this->user->id,				
				'allclientapp_version_user_'.$this->user->id,				
				'allinternalbench_version_user_'.$this->user->id,				
				'allpublicbench_version_user_'.$this->user->id,				
				'alldsimilarcandidates_version_user_'.$this->user->id,				
				'alldinternalbench_version_user_'.$this->user->id,				
				'allfindtalents_version_user_'.$this->user->id,				
				'allcandidatesrequest_version_user_'.$this->user->id,				
				'allcareerapplydata_version_user_'.$this->user->id,	
				'alljobslist_version_user_'.$this->user->id
			];
			foreach ($cachekeys as $key) {
				GeneralMethod::cacheClean($key);
			}

			$teamRankElastic = [
								'company_id'=>$jobRow->company_id,
								'user_id'=>$this->user->id,
								'candidate_id'=>$lastWiseIs,
								'profileUpload'=>1,
								'interviewScheduled'=>0,
								'interviewNoShow'=>0,
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
		
			// ElasticCandidateIndex::dispatch($lastWiseIs)->onConnection('emails');
			
			


			DB::table('add_job_wise_condidates')->where('id', '=', $wiseId)->update(['existing' => 1]);

			// if (@$application_id > 0) {
			// 	if(101==101){
			// 		$event_name = 'internaldatabaseaccess';
			// 	}else{
			// 		$event_name = 'internaldatabaseaccess';
			// 	}

			// 	$event_table_name = 'job_applications';
			// 	$event_table_id = @$application_id;
			// 	$packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
			// }

			

		}

		//######### FOR INTERNAL BENCH APPLICANT
		if ($source_from_type == 1) {



			$eventId = 11;
			$coinsDeductions = GeneralMethod::coinsDeductions((int) $coin_company_id, $eventId, auth()->user()->id, $coin_company_id, '', '');
			if ($coinsDeductions->status) {
				return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
			}





			$parent_id = 0;
			if ($source_tab == 'existing') {

				$rec_row_data = DB::table('rec_candidate_assign')
					->select('rec_candidate_assign.source_from_id')
					->where('rec_candidate_assign.source_from_type', '=', 1)
					->where('rec_candidate_assign.source_application_id', '=', $id)
					->where('rec_candidate_assign.source_company_id', '=', $source_company_id)
					->first();
				$row_data = DB::table('job_applications')->select('*')->where('id', @$rec_row_data->source_from_id)->first();
				$parent_id = $id;

			} else {
				$row_data = DB::table('job_applications')->select('*')->where('id', @$id)->first();
			}

			//$row_data 					=	DB::table('bench_applications')->select('*')->where('id', $id)->first();
			$source_from_company_id = @$row_data->company_id;
			$source_from_user_id = @$row_data->submitted_by;
			$source_from_id = $id; //@$row_data->id;
			

			$job_application_values = array(
				//'submitted_by' 			=>	$source_user_id, 
				//'company_id' 				=>	$source_company_id,
				//'thirdparty_company_id'	=>	0,

				'np_id' => @$row_data->np_id ? @$row_data->np_id : 0,
				'full_name' => @$row_data->full_name,

				'email' => @$row_data->email,
				'phone' => @$row_data->phone,
				//'gender' 					=>	@$row_data->gender,
				'dob' => @$row_data->dob,
				'country' => @$row_data->country,

				'state' => @$row_data->state,
				'city' => @$row_data->city,
				//'city_id' 				=>	@$row_data->city_id,
				'secondrary_email' => @$row_data->secondrary_email,
				'secondary_mobile' => @$row_data->secondary_mobile,

				'skills' => @$row_data->skills,
				'photo' => @$row_data->photo,
				'resume' => @$row_data->resume,
				'rating' => @$row_data->rating,
				//'skype_id' 				=>	@$row_data->skype_id,

				//'cover_letter' 			=>	@$row_data->cover_letter,
				'job_id' => @$source_jobid,
				//'status_id' 				=>	@$row_data->status_id,
				'is_viewed' => @$row_data->is_viewed,
				//'column_priority' 		=>	@$row_data->column_priority,

				'total_exp' => @$row_data->total_exp,
				'rel_exp' => @$row_data->rel_exp,
				//'passport' 				=>	@$row_data->passport,
				'ctc' => @$row_data->pay_rate_amount,
				'current_ctc_cur' => @$row_data->pay_rate_currency,

				'current_ctc_mode' => @$row_data->pay_rate_mode,

				'ectc' => @$row_data->bill_rate_amount,
				'current_ectc_cur' => @$row_data->bill_rate_currency,
				'current_ectc_mode' => @$row_data->bill_rate_mode,
				//'current_location' 		=> 	@$row_data->current_location,

				'current_designation' => @$row_data->current_designation,
				'current_company' => @$row_data->current_company,
				'process_id' => @$row_data->process_id,
				'qualification' => @$row_data->qualification,
				'master_status_id' => 8,

				//'child_status_id' 		=> 	@$row_data->child_status_id,
				'serving_notice_period_mode' => @$row_data->serving_notice_period_mode ? @$row_data->serving_notice_period_mode : 0,
				'proposed_ctc' => @$row_data->proposed_ctc,
				'any_offer' => @$row_data->any_offer ? @$row_data->any_offer : 0,
				//'document' 				=> 	@$row_data->document,

				//'sub_for_approval' 		=> 	@$row_data->sub_for_approval,
				'offer_approved' => @$row_data->offer_approved ? @$row_data->offer_approved : 0,
				'offer_release' => @$row_data->offer_release ? @$row_data->offer_release : 0,
				'offer_acceptance' => @$row_data->offer_acceptance ? @$row_data->offer_acceptance : 0,
				'joined' => @$row_data->joined ? @$row_data->joined : 0,

				'joined_date' => @$row_data->joined_date,
				//'duplicate' 				=>	@$row_data->duplicate,
				//'percentage_hike' 		=>	@$row_data->percentage_hike,
				//'comment' 				=>	@$row_data->comment,
				'pincode' => @$row_data->pincode,

				'offer_currency' => @$row_data->offer_currency,
				'offer_mode' => @$row_data->offer_mode,
				'offer_amount' => @$row_data->offer_amount

			);
			// DB::table('job_applications')->insert($job_application_values);

			$note = 'Similar Candidate => Internal Bench';

			if ($source_from_company_id != auth()->user()->company_id) {
				$note = 'Similar Candidate => Public Bench';
			}

			$application_id = "";
			$application_id = $id; //DB::getPdo()->lastInsertId();

			// $insertArr = array('job_id' => @$source_jobid, 'job_application_id' =>@$application_id, 'status_id' => 8, 'master_status_id' => 8, 'company_id' =>  @$row_data->country, 'submitted_by' => @$this->user->id,'thirdparty_company_id'=>@$thirdparty_company_id);

			// DB::table('add_job_wise_condidates')->insert($insertArr);

			$timelineArr = array(
				'event_id' => 1,
				'event_by_company_id' => auth()->user()->company_id,
				'event_by_user_id' => auth()->user()->id,
				'master_process_name' => 'InReview',
				//'child_process_name'	=>	
				'application_id' => @$application_id,
				'application_email' => @$row_data->email,
				'job_id' => @$source_jobid,
				'note' => $note
			);
			GeneralMethod::UpdateTimeline($timelineArr);

			$values = array(
				'source_from_company_id' => @$source_from_company_id,
				'source_from_user_id' => @$source_from_user_id,
				'source_from_id' => @$source_from_id,
				'source_from_type' => @$source_from_type,

				'parent_id' => $parent_id,
				'source_company_id' => @$source_company_id,
				'source_user_id' => @$source_user_id,
				'source_application_id' => @$application_id,
				'ac_status' => 1,
				'source_jobid' => @$source_jobid
			);
			DB::table('rec_candidate_assign')->insert($values);
			$recCandidateAssignId = DB::getPdo()->lastInsertId();

			$skillArr = explode(',', @$row_data->skills);
		    $percentage =  GeneralMethod::skill_match_percentage(@$source_jobid,$skillArr);
			
			$now = now();
			$insertArr = array('job_id' => @$source_jobid, 'rec_candidate_assign_id' => $recCandidateAssignId, 'job_application_id' => @$application_id, 'master_status_id' => 8, 'company_id' => @$jobRow->company_id, 'submitted_by' => @$this->user->id, 'thirdparty_company_id' => @$thirdparty_company_id, 'existing' => 1,'skill_match' => $percentage,'created_at' => $now,'updated_at' => $now);

			DB::table('add_job_wise_condidates')->insert($insertArr);
			$lastWiseIs = DB::getPdo()->lastInsertId();
			
			$cachekeys = [
				'allcvtracker_version_user_'.$this->user->id,				
				'allapptracker_version_user_'.$this->user->id,				
				'allsimilarcandidates_version_user_'.$this->user->id,				
				'allclientapp_version_user_'.$this->user->id,				
				'allinternalbench_version_user_'.$this->user->id,				
				'allpublicbench_version_user_'.$this->user->id,				
				'alldsimilarcandidates_version_user_'.$this->user->id,				
				'alldinternalbench_version_user_'.$this->user->id,				
				'allfindtalents_version_user_'.$this->user->id,				
				'allcandidatesrequest_version_user_'.$this->user->id,				
				'allcareerapplydata_version_user_'.$this->user->id,
				'alljobslist_version_user_'.$this->user->id
			];
			foreach ($cachekeys as $key) {
				GeneralMethod::cacheClean($key);
			}

			$teamRankElastic = [
								'company_id'=>$jobRow->company_id,
								'user_id'=>$this->user->id,
								'candidate_id'=>$lastWiseIs,
								'profileUpload'=>1,
								'interviewScheduled'=>0,
								'interviewNoShow'=>0,
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
			
			// ElasticCandidateIndex::dispatch($lastWiseIs)->onConnection('emails');
			GeneralMethod::elasticBenchCandidateAddEditIndex($source_from_id);			


		}

		//######### FOR PUBLIC BENCH APPLICANT
		if ($source_from_type == 2) {

			$eventId = 10;
			$coinsDeductions = GeneralMethod::coinsDeductions((int) $coin_company_id, $eventId, auth()->user()->id, $coin_company_id, '', '');
			if ($coinsDeductions->status) {
				return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
			}


			$jobcountry = GeneralMethod::JobCountries($jobRow->id);
			$country = @$row_data->country;

			// $packageStatus = GeneralMethod::validatePackageRet('CVACCESS');
			// if ($packageStatus != 1) {
			// 	return Reply::dataOnly(['status' => 'info', "message" => "CV Access / Received Opps something worng , please contact to Administrator."]);
			// }

			//     $packageStatusIndia = GeneralMethod::validatePackageRet('BenchSalesIndia');
			// 	$packageStatusInternational = GeneralMethod::validatePackageRet('BenchSalesInternational');
			// if (in_array($country, $jobcountry)) {
			//     if ($packageStatusIndia != 1) {
			// 		return Reply::dataOnly(['status' => 'info', "message" => "Bench Sales / Received Opps something worng , please contact to Administrator."]);
			// 	}
			// }else{
			// 	if ($packageStatusInternational != 1) {
			// 		return Reply::dataOnly(['status' => 'info', "message" => "Bench Sales / Received Opps something worng , please contact to Administrator."]);
			// 	}
			// }

			$parent_id = 0;
			if ($source_tab == 'existing') {

				$rec_row_data = DB::table('rec_candidate_assign')
					->select('rec_candidate_assign.source_from_id')
					->where('rec_candidate_assign.source_from_type', '=', 2)
					->where('rec_candidate_assign.source_application_id', '=', $id)
					->where('rec_candidate_assign.source_company_id', '=', $source_company_id)
					->first();
				$row_data = DB::table('job_applications')->select('*')->where('id', @$rec_row_data->source_from_id)->first();
				$parent_id = $id;

			} else {
				$row_data = DB::table('job_applications')->select('*')->where('id', @$id)->first();
			}

			$source_from_company_id = @$row_data->company_id;
			$source_from_user_id = @$row_data->submitted_by;
			$source_from_id = @$row_data->id;

			$job_application_values = array(
				//'submitted_by' 			=>	$source_user_id, 
				//'company_id' 				=>	$source_company_id,
				//'thirdparty_company_id'	=>	0,

				'np_id' => @$row_data->np_id ? @$row_data->np_id : null,
				'full_name' => @$row_data->full_name,

				'email' => @$row_data->email,
				'phone' => @$row_data->phone,
				//'gender' 					=>	@$row_data->gender,
				'dob' => @$row_data->dob,
				'country' => @$row_data->country,

				'state' => @$row_data->state,
				'city' => @$row_data->city,
				//'city_id' 				=>	@$row_data->city_id,
				'secondrary_email' => @$row_data->secondrary_email,
				'secondary_mobile' => @$row_data->secondary_mobile,

				'skills' => @$row_data->skills,
				'photo' => @$row_data->photo,
				'resume' => @$row_data->resume,
				'rating' => @$row_data->rating,
				//'skype_id' 				=>	@$row_data->skype_id,

				//'cover_letter' 			=>	@$row_data->cover_letter,
				'job_id' => @$source_jobid,
				//'status_id' 				=>	@$row_data->status_id,
				'is_viewed' => @$row_data->is_viewed,
				//'column_priority' 		=>	@$row_data->column_priority,

				'total_exp' => @$row_data->total_exp,
				'rel_exp' => @$row_data->rel_exp,
				//'passport' 				=>	@$row_data->passport,
				'ctc' => @$row_data->pay_rate_amount,
				'current_ctc_cur' => @$row_data->pay_rate_currency,

				'current_ctc_mode' => @$row_data->pay_rate_mode,

				'ectc' => @$row_data->bill_rate_amount,
				'current_ectc_cur' => @$row_data->bill_rate_currency,
				'current_ectc_mode' => @$row_data->bill_rate_mode,
				//'current_location' 		=> 	@$row_data->current_location,

				'current_designation' => @$row_data->current_designation,
				'current_company' => @$row_data->current_company,
				'process_id' => @$row_data->process_id,
				'qualification' => @$row_data->qualification,
				'master_status_id' => 8,

				//'child_status_id' 		=> 	@$row_data->child_status_id,
				'serving_notice_period_mode' => @$row_data->serving_notice_period_mode ? @$row_data->serving_notice_period_mode : 0,
				'proposed_ctc' => @$row_data->proposed_ctc,
				'any_offer' => @$row_data->any_offer ? @$row_data->any_offer : 0,
				//'document' 				=> 	@$row_data->document,

				//'sub_for_approval' 		=> 	@$row_data->sub_for_approval,
				'offer_approved' => @$row_data->offer_approved ? @$row_data->offer_approved : 0,
				'offer_release' => @$row_data->offer_release ? @$row_data->offer_release : 0,
				'offer_acceptance' => @$row_data->offer_acceptance ? @$row_data->offer_acceptance : 0,
				'joined' => @$row_data->joined ? @$row_data->joined : 0,

				'joined_date' => @$row_data->joined_date,
				//'duplicate' 				=>	@$row_data->duplicate,
				//'percentage_hike' 		=>	@$row_data->percentage_hike,
				//'comment' 				=>	@$row_data->comment,
				'pincode' => @$row_data->pincode,

				'offer_currency' => @$row_data->offer_currency,
				'offer_mode' => @$row_data->offer_mode,
				'offer_amount' => @$row_data->offer_amount

			);
			// DB::table('job_applications')->insert($job_application_values);

			$note = 'Similar Candidate => Agency Bench';

			$application_id = "";
			$application_id = $id; //DB::getPdo()->lastInsertId();

			// $insertArr = array('job_id' => @$source_jobid, 'job_application_id' =>@$application_id, 'status_id' => 8, 'master_status_id' => 8, 'company_id' =>  @$row_data->country, 'submitted_by' => @$this->user->id,'thirdparty_company_id'=>@$thirdparty_company_id);

			// DB::table('add_job_wise_condidates')->insert($insertArr);

			$timelineArr = array(
				'event_id' => 1,
				'event_by_company_id' => auth()->user()->company_id,
				'event_by_user_id' => auth()->user()->id,
				'master_process_name' => 'InReview',
				//'child_process_name'	=>	
				'application_id' => @$application_id,
				'application_email' => @$row_data->email,
				'job_id' => @$source_jobid,
				'note' => $note
			);
			GeneralMethod::UpdateTimeline($timelineArr);


			$values = array(
				'source_from_company_id' => @$source_from_company_id,
				'source_from_user_id' => @$source_from_user_id,
				'source_from_id' => @$source_from_id,
				'source_from_type' => @$source_from_type,

				'parent_id' => $parent_id,
				'source_company_id' => @$source_company_id,
				'source_user_id' => @$source_user_id,
				'source_application_id' => @$application_id,
				'ac_status' => 1,
				'source_jobid' => @$source_jobid
			);
			DB::table('rec_candidate_assign')->insert($values);
			$recCandidateAssignId = DB::getPdo()->lastInsertId();

			$skillArr = explode(',', @$row_data->skills);
		    $percentage =  GeneralMethod::skill_match_percentage(@$source_jobid,$skillArr);
			$now = now();
			$insertArr = array('job_id' => @$source_jobid, 'rec_candidate_assign_id' => $recCandidateAssignId, 'job_application_id' => @$application_id, 'master_status_id' => 8, 'company_id' => @$jobRow->company_id, 'submitted_by' => @$this->user->id, 'thirdparty_company_id' => @$thirdparty_company_id, 'existing' => 1,'skill_match' => $percentage,'created_at' => $now,'updated_at' => $now);

			DB::table('add_job_wise_condidates')->insert($insertArr);
			$lastWiseIs = DB::getPdo()->lastInsertId();
			
			$cachekeys = [
				'allcvtracker_version_user_'.$this->user->id,				
				'allapptracker_version_user_'.$this->user->id,				
				'allsimilarcandidates_version_user_'.$this->user->id,				
				'allclientapp_version_user_'.$this->user->id,				
				'allinternalbench_version_user_'.$this->user->id,				
				'allpublicbench_version_user_'.$this->user->id,				
				'alldsimilarcandidates_version_user_'.$this->user->id,				
				'alldinternalbench_version_user_'.$this->user->id,				
				'allfindtalents_version_user_'.$this->user->id,				
				'allcandidatesrequest_version_user_'.$this->user->id,				
				'allcareerapplydata_version_user_'.$this->user->id,
				'alljobslist_version_user_'.$this->user->id
			];
			foreach ($cachekeys as $key) {
				GeneralMethod::cacheClean($key);
			}

			$teamRankElastic = [
								'company_id'=>$jobRow->company_id,
								'user_id'=>$this->user->id,
								'candidate_id'=>$lastWiseIs,
								'profileUpload'=>1,
								'interviewScheduled'=>0,
								'interviewNoShow'=>0,
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
			
			// ElasticCandidateIndex::dispatch($lastWiseIs)->onConnection('emails');
			// GeneralMethod::JobApplicationsAddEditUpload($lastWiseIs);
			


			$countforadd = $row_data->countforadd + 1;
			$dataupdate['countforadd'] = $countforadd;
			if ($countforadd >= 3) {
				$dataupdate['status'] = 1;
			}
			DB::table('job_applications')->where('id', '=', $row_data->id)->update($dataupdate);

			

			$jobRowupdate = DB::table('job_applications')->select('*')->where('id', $row_data->id)->first();

			if ($jobRowupdate) {
				// dd($jobRowupdate->countforadd);
				if ($jobRowupdate->countforadd >= 3) {
					$detailsVendorSide = GeneralMethod::companyAsVendorAndClient($source_company_id, $source_from_company_id);
					$basecompany = GeneralMethod::baseCompany();

					$senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
					$fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
					$attachments = array();

					$cc = [];
					$checkMailPermision = GeneralMethod::mailPermission(26);
					if ($checkMailPermision) {

						$mailTypeSubject = GeneralMethod::mailTypeSubject(26, 1);

						$subject = str_replace(["[{BENCH_CANDIDATE_NAME}]", "[{BENCH_ID}]"], [$jobRowupdate->full_name, $jobRowupdate->id], $mailTypeSubject->subject);
						// dd($subject);
						$current_status = '';
						if ($jobRowupdate->status == 1) {
							$current_status = 'Pool';
						} elseif ($jobRowupdate->status == 2) {
							$current_status = 'Bench';
						} elseif ($jobRowupdate->status == 3) {
							$current_status = 'Placed';
						} elseif ($jobRowupdate->status == 4) {
							$current_status = 'Hold';
						}
						$template_description = $mailTypeSubject->body_description;
						$template_description = str_replace(["[{VENDOR_COMPANY_NAME}]", "[{BENCH_CANDIDATE_NAME}]", "[{BENCH_ID}]", "[{DESIGNATION}]", "[{STATUS}]"], [$detailsVendorSide->vendor_company, $jobRowupdate->full_name, $jobRowupdate->id, $jobRowupdate->current_designation, $current_status], $template_description);

						$message = $template_description;

						// dd($message);

						$notificationemail = GeneralMethod::notification($source_from_company_id, 'Recuitment'); //Recuitment Sales
						$defailsEmail = DB::table('companies')->where('id', $source_from_company_id)->first()->company_email;
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
							$source_from_company_id
						)->onConnection('emails');

						// $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject, 'email.common.index', (object) $data, $attachments, $fromAddress, $senderName, $cc);
					}

				}

			}

			$eventId = 23;
			$coinsDeductions = GeneralMethod::coinsDeductions((int) $source_from_company_id, $eventId, auth()->user()->id, $coin_company_id, '', '');


			// REDEEM CVACCESS   
			if (@$application_id > 0) {
				$event_name = 'CVACCESS';
				$event_table_name = 'job_applications';
				$event_table_id = @$application_id;
				// $packageStatus = GeneralMethod::packageRedeem($event_name, $event_table_name, $event_table_id);

			}
			// CLIENT REDEEM NEW_BENCH_CLIENT_REQUEST 
			if (@$application_id > 0) {

				if (in_array($country, $jobcountry)) {
					$event_name = 'BenchSalesIndia';
				} else {
					$event_name = 'BenchSalesInternational';
				}



				$event_table_name = 'job_applications';
				$event_table_id = @$application_id;
				// $packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
			}

			$packageStatus = GeneralMethod::validateClientPackage('emails', $source_from_company_id);
			if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) {
				$companies = DB::table('companies')->select('company_email')->where('id', '=', @$row_data->company_id)->first();
				if (@$source_jobid > 0) {
					$source_from_company_id = @$row_data->company_id;
					$event_name = 'emails';
					$event_table_name = 'jobs';
					$event_table_id = @$source_jobid;
					// GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);

					$data = [
						'username' => auth()->user()->name,
					];
					$senderName = auth()->user()->name;

					$detailsVendorSide = GeneralMethod::companyAsVendorAndClient($source_company_id, $source_from_company_id);

					$basecompany = GeneralMethod::baseCompany();
					$senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
					$fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
					$attachments = array();

					$cc = [];
					$checkMailPermision = GeneralMethod::mailPermission(23);
					if ($checkMailPermision) {

						$mailTypeSubject = GeneralMethod::mailTypeSubject(23, 2);
						// $subject = $mailTypeSubject->subject;
						// $message = $mailTypeSubject->body_description;

						$subject = str_replace(["[{CANDIDATE_NAME}]", "[{CLIENT_COMPANY_NAME}]"], [$row_data->full_name, $detailsVendorSide->client_company], $mailTypeSubject->subject);
						// dd($subject);

						$template_description = $mailTypeSubject->body_description;
						$template_description = str_replace(["[{VENDOR_COMPANY_NAME}]", "[{CLIENT_COMPANY_NAME}]", "[{CLIENT_NAME}]", "[{CLIENT_DESIGNATION}]", "[{CLIENT_EMAIL}]", "[{CLIENT_PHONE}]", "[{CANDIDATET_NAME}]", "[{CANDIDATET_EMAIL}]", "[{CANDIDATET_PHONE}]"], [$detailsVendorSide->vendor_company, $detailsVendorSide->client_company, $detailsVendorSide->client_name, $detailsVendorSide->client_designation, $detailsVendorSide->client_email, $detailsVendorSide->client_phone, $row_data->full_name, $row_data->id, $row_data->current_designation], $template_description);

						$message = $template_description;

						// dd($message);

						$notificationemail = GeneralMethod::notification($source_from_company_id, 'Recuitment'); //Recuitment Sales
						$defailsEmail = DB::table('companies')->where('id', $source_from_company_id)->first()->company_email;
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
							$source_from_company_id
						)->onConnection('emails');


						// $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject, 'email.common.index', (object) $data, $attachments, $fromAddress, $senderName, $cc);

					}

					$checkMailPermision = GeneralMethod::mailPermission(24);
					if ($checkMailPermision) {

						$mailTypeSubject = GeneralMethod::mailTypeSubject(24, 1);


						$subject = str_replace(["[{JOB_TITLE}]"], [$jobRow->title], $mailTypeSubject->subject);

						$template_description = $mailTypeSubject->body_description;
						$template_description = str_replace(["[{COMPANY_NAME}]", "[{HR_NAME}]", "[{JOB_TITLE}]", "[{JOB_ID}]", "[{CANDIDATET_NAME}]", "[{CANDIDATET_DESINATION}]", "[{VENDOR_COMPANY_NAME}]", "[{VENDOR_NAME}]", "[{VENDOR_EMAIL}]", "[{VENDOR_PHONE}]"], [$detailsVendorSide->client_company, $detailsVendorSide->client_name, $jobRow->title, $jobRow->id, $row_data->full_name, $row_data->current_designation, $detailsVendorSide->vendor_company, $detailsVendorSide->vendor_name, $detailsVendorSide->vendor_phone, $detailsVendorSide->vendor_email], $template_description);
						$message = $template_description;

						$notificationemail = GeneralMethod::notification($source_company_id, 'Sales'); //Recuitment Sales
						$defailsEmail = DB::table('companies')->where('id', $source_company_id)->first()->company_email;
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
							$source_company_id
						)->onConnection('emails');

						// $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject, 'email.common.index', (object) $data, $attachments, $fromAddress, $senderName, $cc);
					}

					// Mail::to(@$companies->company_email)->send(new BenchCandidatLeadMail((object)$data, $senderName));
				}
			}


			GeneralMethod::elasticBenchCandidateAddEditIndex($application_id);

		}

		//######### WEBSITE CAREER APPLY APPLICANT
		if ($source_from_type == 3) {



			$eventId = 11;
			$coinsDeductions = GeneralMethod::coinsDeductions((int) $coin_company_id, $eventId, auth()->user()->id, $coin_company_id, '', '');
			if ($coinsDeductions->status) {
				return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
			}


			$parent_id = 0;
			$row_data = DB::table('job_applications')->select('*')->where('id', @$id)->first();

			//$row_data 					=	DB::table('bench_applications')->select('*')->where('id', $id)->first();
			$source_from_company_id = @$row_data->company_id;
			$source_from_user_id = @$row_data->submitted_by;
			$source_from_id = $id; //@$row_data->id;
			

			$job_application_values = array(
				'np_id' => @$row_data->np_id ? @$row_data->np_id : 0,
				'full_name' => @$row_data->full_name,

				'email' => @$row_data->email,
				'phone' => @$row_data->phone,
				//'gender' 					=>	@$row_data->gender,
				'dob' => @$row_data->dob,
				'country' => @$row_data->country,

				'state' => @$row_data->state,
				'city' => @$row_data->city,
				//'city_id' 				=>	@$row_data->city_id,
				'secondrary_email' => @$row_data->secondrary_email,
				'secondary_mobile' => @$row_data->secondary_mobile,

				'skills' => @$row_data->skills,
				'photo' => @$row_data->photo,
				'resume' => @$row_data->resume,
				'rating' => @$row_data->rating,
				//'skype_id' 				=>	@$row_data->skype_id,

				//'cover_letter' 			=>	@$row_data->cover_letter,
				'job_id' => @$source_jobid,
				//'status_id' 				=>	@$row_data->status_id,
				'is_viewed' => @$row_data->is_viewed,
				//'column_priority' 		=>	@$row_data->column_priority,

				'total_exp' => @$row_data->total_exp,
				'rel_exp' => @$row_data->rel_exp,
				//'passport' 				=>	@$row_data->passport,
				'ctc' => @$row_data->pay_rate_amount,
				'current_ctc_cur' => @$row_data->pay_rate_currency,

				'current_ctc_mode' => @$row_data->pay_rate_mode,

				'ectc' => @$row_data->bill_rate_amount,
				'current_ectc_cur' => @$row_data->bill_rate_currency,
				'current_ectc_mode' => @$row_data->bill_rate_mode,
				//'current_location' 		=> 	@$row_data->current_location,

				'current_designation' => @$row_data->current_designation,
				'current_company' => @$row_data->current_company,
				'process_id' => @$row_data->process_id,
				'qualification' => @$row_data->qualification,
				'master_status_id' => 8,

				//'child_status_id' 		=> 	@$row_data->child_status_id,
				'serving_notice_period_mode' => @$row_data->serving_notice_period_mode ? @$row_data->serving_notice_period_mode : 0,
				'proposed_ctc' => @$row_data->proposed_ctc,
				'any_offer' => @$row_data->any_offer ? @$row_data->any_offer : 0,
				//'document' 				=> 	@$row_data->document,

				//'sub_for_approval' 		=> 	@$row_data->sub_for_approval,
				'offer_approved' => @$row_data->offer_approved ? @$row_data->offer_approved : 0,
				'offer_release' => @$row_data->offer_release ? @$row_data->offer_release : 0,
				'offer_acceptance' => @$row_data->offer_acceptance ? @$row_data->offer_acceptance : 0,
				'joined' => @$row_data->joined ? @$row_data->joined : 0,

				'joined_date' => @$row_data->joined_date,
				//'duplicate' 				=>	@$row_data->duplicate,
				//'percentage_hike' 		=>	@$row_data->percentage_hike,
				//'comment' 				=>	@$row_data->comment,
				'pincode' => @$row_data->pincode,

				'offer_currency' => @$row_data->offer_currency,
				'offer_mode' => @$row_data->offer_mode,
				'offer_amount' => @$row_data->offer_amount

			);
			 // DB::table('job_applications')->insert($job_application_values);

			 $note = 'Website => Pool';


			$application_id = "";
			$application_id = $id; //DB::getPdo()->lastInsertId();


			$timelineArr = array(
				'event_id' => 1,
				'event_by_company_id' => auth()->user()->company_id,
				'event_by_user_id' => auth()->user()->id,
				'master_process_name' => 'InReview',
				//'child_process_name'	=>	
				'application_id' => @$application_id,
				'application_email' => @$row_data->email,
				'job_id' => @$source_jobid,
				'note' => $note
			);
			GeneralMethod::UpdateTimeline($timelineArr);


			$skillArr = explode(',', @$row_data->skills);
		    $percentage =  GeneralMethod::skill_match_percentage(@$source_jobid,$skillArr);
			
			$now = now();
			$insertArr = array('job_id' => @$source_jobid, 'rec_candidate_assign_id' => 0, 'job_application_id' => @$application_id, 'master_status_id' => 8, 'company_id' => @$jobRow->company_id, 'submitted_by' => @$this->user->id, 'thirdparty_company_id' => @$thirdparty_company_id, 'existing' => 1,'skill_match' => $percentage,'created_at' => $now,'updated_at' => $now);

			DB::table('add_job_wise_condidates')->insert($insertArr);
			$lastWiseIs = DB::getPdo()->lastInsertId();			
		
			$cachekeys = [
				'allcvtracker_version_user_'.$this->user->id,				
				'allapptracker_version_user_'.$this->user->id,				
				'allsimilarcandidates_version_user_'.$this->user->id,				
				'allclientapp_version_user_'.$this->user->id,				
				'allinternalbench_version_user_'.$this->user->id,				
				'allpublicbench_version_user_'.$this->user->id,				
				'alldsimilarcandidates_version_user_'.$this->user->id,				
				'alldinternalbench_version_user_'.$this->user->id,				
				'allfindtalents_version_user_'.$this->user->id,				
				'allcandidatesrequest_version_user_'.$this->user->id,
				'alljobslist_version_user_'.$this->user->id,
				'allcareerapplydata_version_user_'.$this->user->id,
			];
			foreach ($cachekeys as $key) {
				GeneralMethod::cacheClean($key);
			}

			$teamRankElastic = [
								'company_id'=>$jobRow->company_id,
								'user_id'=>$this->user->id,
								'candidate_id'=>$lastWiseIs,
								'profileUpload'=>1,
								'interviewScheduled'=>0,
								'interviewNoShow'=>0,
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
			
			// ElasticCandidateIndex::dispatch($lastWiseIs)->onConnection('emails');
			// GeneralMethod::elasticBenchCandidateAddEditIndex($source_from_id);	
			
			
		$updateParams = [
					'index' => ELASTIC_CAREER_APPLY,
					'id' => $application_id,
					'body' => [
						'doc' => [
							'status' => 1,
							'submitted_by' => auth()->user()->id,
							'posted_by' => auth()->user()->name
						],
						'doc_as_upsert' => false // Changed to false since we verified existence
					],
					'refresh' => true // Force immediate refresh
				];
	
				$this->elasticService->getClient()->update($updateParams);

		}

		GeneralMethod::JobApplicationsAddEditUpload($lastWiseIs);


		return Reply::dataOnly(['status' => 'success']);

	}
	public function candidates_request()
	{
		$this->pageTitle = __('Candidates Request');
		$id = session()->get('job_id');

		$this->custome_count = $this->custome_count($id);
		$return_data = GeneralMethod::custome_count_all($id);
		$this->pool_counter = $return_data->pool_counter;
		$this->existing_counter = $return_data->existing_counter;
		$this->internal_bench_counter = $return_data->internal_bench_counter;		
		$this->public_bench_counter = $return_data->public_bench_counter;
		$this->recomnded_counter = $return_data->recomnded_counter;		
		$this->career_counter = $return_data->career_counter;;


		return view('service_company.apptracker.candidates_request', $this->data);
	}
	public function talentPoolRequest($id)
	{
		$id = GeneralMethod::value_decrypt($id);
		$this->pageTitle = __('Candidates Request');

		$this->custome_count = $this->custome_count($id);
		$return_data = GeneralMethod::custome_count_all($id);
		$this->pool_counter = $return_data->pool_counter;
		$this->existing_counter = $return_data->existing_counter;
		$this->internal_bench_counter = $return_data->internal_bench_counter;		
		$this->public_bench_counter = $return_data->public_bench_counter;	
		$this->recomnded_counter = $return_data->recomnded_counter;	
		$this->career_counter = $return_data->career_counter;;
		return view('service_company.apptracker.candidates_request', $this->data);

	}
	public function candidates_request_data_ajax(Request $request)
	{
		// abort_if(Gate::denies('view_job_applications'), 403);		
		$company_id = $mstatus_id = $jobid = $feedback_status = '';
        
		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();
		$company_id = auth()->user()->company_id;
		$mstatus_id = $request->mstatus_id;
		$jobid = (int) session()->get('job_id');

		$job = Job::findOrFail($jobid);

		$interviewStatus = array();
		$interviewStatus = DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();
		$chkAdminRole = $this->user->hasRole('admin');
		// $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
		$feedback_status = GeneralMethod::feedback_status();


		$searchQuery = $request->input('q');
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;
		$valCheck = $request->input('sortingfilter');
		$mstatus_id = $request->input('mstatus_id');
		$cus_search = $request->input('cus_search');

		$symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
		$symbolsValue = !empty($symbolsValue) ? $symbolsValue : [
			0	=>	"INR",
			1	=>	"EUR",
			2	=>	"JPY",
			3	=>	"GBP",
			4	=>	"USD",
			5	=>	"AED"
		];

		$modeFilter = $job->max_sal_mode;
		$currencyFilter = $job->max_salary_currency;
		$symbolsFilter = $symbolsValue[$currencyFilter] ?? "INR";



		$sort = [];
		$country = '';
		$filters = [];
		$customOrder = [8, 1, 2, 3, 4, 5, 6, 7];

		$cachePayload = [
			'page' => $page,
			'size' => $size,
			'q' => $searchQuery,
			'company' => $company_id,
			'mstatus_id' => $mstatus_id,
			'sortingfilter' => $valCheck,
			'user_id' => $this->user->id,
			'country' => $country ?? [],
			'cus_search' =>$cus_search
		];


		$versionKey = 'allcandidatesrequest_version_user_'.$this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:allcandidatesrequest:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
		/* ----------------------------
		⚡ Check DragonflyDB first
		----------------------------- */
		if (cache()->has($cacheKeySet)) {
			// return response()->json(cache()->get($cacheKeySet));
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
			case 5: // Notice period ASC (ignore Immediate)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return 999999;
								}
								try {
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {}
								return 999999;
							"
						],
						'order' => 'asc'
					]
				];
				break;
			case 6: // Notice period DESC (ignore Immediate)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return -1;
								}
								try {
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {}
								return -1;
							"
						],
						'order' => 'desc'
					]
				];
				break;
			case 21: // Notice period Date ASC
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return 999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return 999999999; }
							def v = raw.trim();

							// Immediate should come first
							if (v.equals('Immediate')) { return -999999998; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return 999999999; // not a date -> last
									}
								}
							}
						"
					],
					'order' => 'asc'
				]
			];
			break;

			case 22: // Notice period Date DESC
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return -999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return -999999999; }
							def v = raw.trim();

							// Immediate should come after valid dates (but before null/non-date)
							if (v.equals('Immediate')) { return -888888888; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return -999999999; // not a date -> last
									}
								}
							}
						"
					],
					'order' => 'desc'
				]
			];
			break;
			case 7:
				$sort[] = ['total_exp' => ['order' => 'desc', 'unmapped_type' => 'float']];
				break;
			case 8:
				$sort[] = ['total_exp' => ['order' => 'asc', 'unmapped_type' => 'float']];
				break;
			case 9: // Immediate joiners first
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "params['_source']['immediate_join'] == 'Immediate' ? 0 : 1"
						],
						'order' => 'asc'
					]
				];
				break;
			default:
				// Custom job_master_status_id order
				$sort[] = ['ac_status' => ['order' => 'asc']];
				break;
		}

		// Apply fallback sort by ID descending
		$sort[] = ['id' => ['order' => 'desc', 'unmapped_type' => 'long']];


		
		$boolQuery = [
			'bool' => [
				'should' => [
					// First condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['source_company_id' => $company_id]],
								['term' => ['source_jobid' => $jobid]],
							])
						]
					],
				],
				'minimum_should_match' => 1,
				'filter' => $filters // We'll add all filters here
			]
		];

		if ($valCheck==22) {
			$today = date('Y-m-d');  
			$next30 = date('Y-m-d', strtotime('+30 days'));

			// Serving candidates filter
			$servingCandidateFilter = [
				'script' => [
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) return false;

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) return false;

							def v = raw.trim();

							if (v.equals('Immediate')) return false; // exclude 'Immediate'

							int comma = v.indexOf(',');
							if (comma > -1) v = v.substring(0, comma).trim();

							int t = v.indexOf('T');
							if (t > -1) v = v.substring(0, t);

							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								long epoch = d.toEpochDay();
								long today = java.time.LocalDate.parse(params.today).toEpochDay();
								long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

								return epoch >= today && epoch <= next30;
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									long epoch = d.toEpochDay();
									long today = java.time.LocalDate.parse(params.today).toEpochDay();
									long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

									return epoch >= today && epoch <= next30;
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										long epoch = d.toEpochDay();
										long today = java.time.LocalDate.parse(params.today).toEpochDay();
										long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

										return epoch >= today && epoch <= next30;
									} catch (Exception e3) {
										return false; // not a valid date
									}
								}
							}
						",
						'params' => [
							'today'  => $today,
							'next30' => $next30
						]
					]
				]
			];
		    // Attach serving candidate condition along with others
            $boolQuery['bool']['filter'][] = $servingCandidateFilter;
		}


		// Build the final query
		$params = [
			'index' => ELASTIC_CANDIDATES_REQUEST,
			'request_cache' => true,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => $sort,
				'query' => $boolQuery
			]
		];

		if (!$this->elasticService->indexExists(ELASTIC_CANDIDATES_REQUEST)) {
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
				$app_int_activity = request('app_int_activity');
				$app_int_status = request('app_int_status');
				$app_int_notice = request('app_int_notice');
				$sal_mode = request('sal_mode');
				$min_salary_currency = request('min_salary_currency');
				$app_int_outsource = request('app_int_outsource');

				$responseData =  [
					'html' => view('service_company.ajax_pages.candidates_request_ajax', compact('data','userPermissions','app_int_status','app_int_activity','app_int_notice','sal_mode','min_salary_currency','app_int_outsource','total','jobid','modeFilter','currencyFilter','symbolsFilter'))->render(),
					'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
				];

				cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_CANDIDATES_REQUEST_TIME));

	            return response()->json($responseData);	

		
	}
	public function custome_count($id)
	{


		$company_id = auth()->user()->company_id;
		$this->job = Job::findOrFail($id);
		$this->jobclient = Client::find($this->job->client_id);
		//$this->jobclient = Client::findOrFail($this->job->client_id);

	}
	public function dinternal_count($id, $company_id)
	{

		$jobid = $id;
		$job = $this->job;
		$title = $job->title;
		$min_exp = $job->min_exp;
		$max_exp = $job->max_exp;
		$min_salary = $job->min_salary;
		$max_salary = $job->max_salary;
		$loc_data = DB::table('client_location_jobs')->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
			->select('client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')->where('client_location_jobs.job_id', $jobid)->first();
		$postcode = @$loc_data->postcode;
		$skill_arr = array();
		$skill_data = DB::table('job_metronics_skills')->select('*')->where('job_id', $jobid)->get()->toArray();
		foreach ($skill_data as $sval) {
			$skill_arr[] = $sval->skill_name;
		}
		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();
		$chkAdminRole = $this->user->hasRole('admin');
		// $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
		$this->job = Job::findOrFail($id);
		$gdFilterData = GeneralMethod::gdFilterData($id);
		$this->default_loc = GeneralMethod::JobcitiesTags($id);
		$this->qualification = GeneralMethod::allQualificationSelected($id);

		$min_exp = ($gdFilterData->min_exp >= 1) ? $gdFilterData->min_exp - 1 : $gdFilterData->min_exp;
		$max_exp = $gdFilterData->max_exp + 1;
		$min_salary_currency = $gdFilterData->min_salary_currency;
		$min_salary = 0;
		$max_salary = $gdFilterData->max_salary;
		$sal_mode = $gdFilterData->min_sal_mode;
		$default_skill = $gdFilterData->default_skill;

		$currency = GeneralMethod::currency();  // Currency code
		$currencyCheck = $currency[$min_salary_currency];
		$convertedAmount = $gdFilterData->max_salary;
		if ($min_salary_currency > 0) {
			$convertedAmount = GeneralMethod::convertToINR($convertedAmount, $currencyCheck);
		}
		$ectcdivided = GeneralMethod::salaryDividedByMode($sal_mode, $convertedAmount);
		$min_salary = 0;//$job->min_salary;
		// $max_salary = $job->max_salary;	

		if ($sal_mode == 0) {
			$max_salary = $ectcdivided->yearly;

		} elseif ($sal_mode == 1) {
			$max_salary = $ectcdivided->monthly;

		} elseif ($sal_mode == 2) {
			$max_salary = $ectcdivided->hourly;

		}
		$min_exp =($job->min_exp >= 1) ? $job->min_exp - 1 : $job->min_exp;
		$max_exp = $job->max_exp + 1;

		$jobid = $id;
		$job = $this->job;
		$title = $job->title;

	}
	public function internal_bench($id)
	{

		$id = GeneralMethod::value_decrypt($id);
		$lock_unlock = GeneralMethod::checksubscription();
		if ($lock_unlock == false) {
			return redirect()->route('service_company.dashboard')->with('message_paid', 'This is a paid featute');
			;
		}


		$this->pageTitle = __('Candidates Recommended');
		$company_id = auth()->user()->company_id;
		$this->job = Job::findOrFail($id);

		// ES-only shared source (see GeneralMethod::jobDefaultsFromEs()) —
		// same one similar_candidates() and custome_count_all() (the header
		// counter on every tab) use, so this page's defaults can't drift
		// out of sync with the header count the way gdFilterData()/
		// JobcitiesTags() (MySQL) vs custome_count_all() (ES) used to.
		$jobDefaults = GeneralMethod::jobDefaultsFromEs($id);

		$this->default_loc = $jobDefaults->default_loc;
		$this->qualification = '';//GeneralMethod::allQualificationSelected($id);
		$this->min_exp = ($jobDefaults->min_exp >= 1) ? $jobDefaults->min_exp - 1 : $jobDefaults->min_exp;
		$this->max_exp = $jobDefaults->max_exp + 1;
		$this->min_salary_currency = $jobDefaults->min_salary_currency;
		// Every AppTracker filter drawer defaults Min Salary to 0, not the
		// job's own min_salary (see custome_count_all()'s salary-floor
		// comments) — the header counter and this page have to agree on
		// the same floor or their candidate counts disagree on load.
		$this->min_salary = 0;
		$this->max_salary = $jobDefaults->max_salary;
		$this->min_sal_mode = $jobDefaults->min_sal_mode;
		$this->default_skill = $jobDefaults->default_skill;

		$this->custome_count = $this->custome_count($id);
		$return_data = GeneralMethod::custome_count_all($id);
		$this->pool_counter = $return_data->pool_counter;
		$this->existing_counter = $return_data->existing_counter;
		$this->internal_bench_counter = $return_data->internal_bench_counter;
		$this->public_bench_counter = $return_data->public_bench_counter;
		$this->recomnded_counter = $return_data->recomnded_counter;
		$this->career_counter = $return_data->career_counter;;

		$this->cities = GeneralMethod::allCities();
		$this->qualificationTag = DB::table('qualification_master')
			->select('*')
			->whereNotIn('id',[60])
			->orderBy('qualification_master.display_order', 'ASC')
			->get()
			->map(function ($row) {
				return $row->name;
			});

		session()->put('job_id', $id);

		return view('service_company.apptracker.internal_bench', $this->data);
	}
	public function internal_bench_data_ajax(Request $request)
	{

		$company_id = auth()->user()->company_id;

		$searchTerms = request('cus_search');
		$custom_search = json_decode($searchTerms, true);
		if (\request('cus_search') != "") {
			$skillsAddElastic = array_column($custom_search, 'value');
			GeneralMethod::skillsAddElastic($skillsAddElastic);
		}
		
		$jobid = $request->jobid;
		$job = Job::findOrFail($jobid);

		$title = $job->title;

		$location = request('kt_tagify_location');
		$custom_location = json_decode($location, true);

		$education = request('kt_tagify_education');
		$custom_education = json_decode($education, true);

		$company = request('kt_tagify_company');
		$custom_company = json_decode($company, true);
		$current_date = date('Y-m-d H:i:s');
		$current_timestamp = strtotime($current_date);
		$current_previous_30_days = date('Y-m-d H:i:s', strtotime('-30 days', $current_timestamp));
		$current_previous_60_days = date('Y-m-d H:i:s', strtotime('-60 days', $current_timestamp));
		$current_previous_90_days = date('Y-m-d H:i:s', strtotime('-90 days', $current_timestamp));
		$current_previous_180_days = date('Y-m-d H:i:s', strtotime('-180 days', $current_timestamp));
		$current_previous_1_year = date('Y-m-d H:i:s', strtotime('-365 days', $current_timestamp));
		$app_int_activity = request('app_int_activity');
		$app_int_status = request('app_int_status');
		$app_int_notice = request('app_int_notice');
		$sal_mode = request('sal_mode') ?? $job->max_sal_mode;
		$min_salary_currency = request('min_salary_currency') ?? $job->max_salary_currency;
		$app_int_outsource = request('app_int_outsource');

		$symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
		$symbolsValue = !empty($symbolsValue) ? $symbolsValue : [
			0	=>	"INR",
			1	=>	"EUR",
			2	=>	"JPY",
			3	=>	"GBP",
			4	=>	"USD",
			5	=>	"AED"
		];

		$modeFilter = $sal_mode ?? $job->max_sal_mode;
		$currencyFilter = $min_salary_currency ?? $job->max_salary_currency;
		$symbolsFilter = $symbolsValue[$currencyFilter] ?? "INR";


		if (\request('max_exp') != "" || \request('min_exp') != "") {
			$min_exp = request('min_exp');
			$max_exp = request('max_exp');
		} else {
			$min_exp = '';//$job->min_exp;
			$max_exp = '';//$job->max_exp;
		}

		$currency = GeneralMethod::currency();  // Currency code
		$currencyCheck = $currency[$currencyFilter];

		if (\request('min_sal') != "" || \request('max_sal') != "") {
			$AmountMin = request('min_sal');
			$AmountMax = request('max_sal');
			$min_salary = $AmountMin;
			$max_salary = $AmountMax;
			if ($currencyFilter > 0) {
				$min_salary = GeneralMethod::convertToINR($AmountMin, $currencyCheck);
				$max_salary = GeneralMethod::convertToINR($AmountMax, $currencyCheck);
			}

			// $ctcdividedMin = GeneralMethod::salaryDividedByMode($sal_mode, $AmountMin);
			// $ctcdividedMax = GeneralMethod::salaryDividedByMode($sal_mode, $AmountMax);
			// if ($sal_mode == 0) {
			// 	$min_salary = $ctcdividedMin->yearly;
			// 	$max_salary = $ctcdividedMax->yearly;
			// } elseif ($sal_mode == 1) {
			// 	$min_salary = $ctcdividedMin->monthly;
			// 	$max_salary = $ctcdividedMax->monthly;

			// } elseif ($sal_mode == 2) {
			// 	$min_salary = $ctcdividedMin->hourly;
			// 	$max_salary = $ctcdividedMax->hourly;
			// }




		} else {

			$convertedAmount = request('max_sal'); //$job->max_salary;
			$max_salary = $convertedAmount;
			$min_salary = 0;//$job->min_salary;
			if ($currencyFilter > 0) {
				$max_salary = GeneralMethod::convertToINR($convertedAmount, $currencyCheck);
			}

			// $ectcdivided = GeneralMethod::salaryDividedByMode($sal_mode, $convertedAmount);

			// if ($sal_mode == 0) {
			// 	$max_salary = $ectcdivided->yearly;

			// } elseif ($sal_mode == 1) {
			// 	$max_salary = $ectcdivided->monthly;

			// } elseif ($sal_mode == 2) {
			// 	$max_salary = $ectcdivided->hourly;

			// }

		}

		// dd($min_salary,$max_salary);

		$location = array();
		if (\request('kt_tagify_location') != "") {
			foreach ($custom_location as $sval) {
				$parts = explode('|', $sval['value']);
				// Trim to remove any leading or trailing whitespace
				$cityName = trim($parts[0]);
				$loc_data = DB::table('city_list')->where('city_list.city', '=', $cityName)->first();
				if ($loc_data) {
					array_push($location, $loc_data->city_id);
				}

			}
		}

		$education = array();
		if (\request('kt_tagify_education') != "") {
			foreach ($custom_education as $sval) {
				$education_data = DB::table('qualification_master')->where('qualification_master.name', '=', $sval['value'])->first();
				if ($education_data) {
					$educationfiletr = GeneralMethod::qualificationFilterCondition($education_data->id);
					$education = array_merge($education, $educationfiletr);
				}
			}
		}

		$loc_data = DB::table('client_location_jobs')
			->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
			->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
			->select(
				'client_location.address',
				'client_location.city',
				'client_location.postcode',
				'client_location.country_id',
				'city_list.city as city_name'
			)
			->where('client_location_jobs.job_id', $jobid)
			->first();

		$postcode = @$loc_data->postcode;


		$skill_arr = array();
		$skill_data = DB::table('job_metronics_skills')->select('*')->where('job_id', $jobid)->get()->toArray();
		foreach ($skill_data as $sval) {
			$skill_arr[] = $sval->skill_name;
		}
		$company_arr = array($company_id);
		//echo '<pre>'; print_r($skill_arr); exit; 


		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();

		$chkAdminRole = $this->user->hasRole('admin');

		// $chkAdminRole = DB::table('roles')
		// 	->select('*')
		// 	->where('id', $this->user->role->role_id)
		// 	->where('name', 'admin')
		// 	->count();

		// $mandatorySkills tracks only the starred/checked skills (used to build
		// a strict "must match" clause below). $requiredSkills is EVERY entered
		// skill, checked or not — it used to get overwritten to ONLY the checked
		// skills the instant any skill was starred, silently dropping every other
		// typed skill from filtering, the sort script, and the Match % badge.
		// Mirrors similar_existing_data_ajax()'s must(mandatory)+should(optional)
		// hybrid fix.
		$requiredSkills = [];
		$mandatorySkills = [];
		$checkedSkills = [];
		if (\request('cus_search') != "") {
			$checkedSkills = array_filter($custom_search, function ($skill) {
				return $skill['checked'] == true;
			});
			$requiredSkills = array_column($custom_search, 'value');
			$mandatorySkills = array_column($checkedSkills, 'value');
		}


		$valCheck = $request->input('sortingfilter');
		$country = '';
		$searchQuery = $request->input('q');
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;
		$requiredSkills = array_map('strtolower', $requiredSkills);
		$mandatorySkills = array_map('strtolower', $mandatorySkills);


		$cachePayload = [
			'company' => $company_id,
			'page' => $page,
			'size' => $size,
			'q' => $searchQuery,
			'jobid' => $jobid,
			'cus_search' => $requiredSkills ?? [],
			'sortingfilter' => $valCheck,
			'min_exp' => $min_exp,
			'max_exp' => $max_exp,
			'min_salary_currency' => $min_salary_currency,
			'education' => $education ?? [],
			'location' => $location ?? [],
			'min_sal' => $min_salary,
			'max_sal' => $max_salary,
			'app_int_status'=>$app_int_status,
			'app_int_activity'=>$app_int_activity,
			'app_int_notice'=>$app_int_notice,
			'app_int_outsource' =>$app_int_outsource,
			'user_id' => $this->user->id,
			'sal_mode' =>$sal_mode
		];

		// dd($cachePayload );
		
		$versionKey = 'allinternalbench_version_user_'.$this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:allinternalbench:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
		/* ----------------------------
		⚡ Check DragonflyDB first
		----------------------------- */
		if (cache()->has($cacheKeySet)) {
			return response()->json(cache()->get($cacheKeySet));
		}

         
		$sort[] = [
			'_script' => [
				'type' => 'number',
				'script' => [
					'lang' => 'painless',
					'source' => '
						int matchCount = 0;
						if (doc.containsKey("job_application_skills.skills.keyword")) {
							for (s in params.skills) {
								if (doc["job_application_skills.skills.keyword"].contains(s)) {
									matchCount += 1;
								}
							}
						}
						return matchCount;
					',
					'params' => [
						'skills' => array_values($requiredSkills)
					]
				],
				'order' => 'desc'
			]
		];
		switch ($valCheck) {
			case 1:
				$sort[] = ['updated_at' => ['order' => 'desc']];
				break;
			case 2:
				$sort[] = ['updated_at' => ['order' => 'asc']];
				break;
			case 3:
				$sort[] = ['ctc' => ['order' => 'asc', 'unmapped_type' => 'float']];
				break;
			case 4:
				$sort[] = ['ctc' => ['order' => 'desc', 'unmapped_type' => 'float']];
				break;
			case 5: // Notice period ASC (ignore Immediate)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return 999999; // Put missing/Immediate at the end
								}
								try {
									// Extract first number from comma-separated values
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {
									// If parsing fails
								}
								return 999999; // Invalid values at the end
							"
						],
						'order' => 'asc'
					]
				];
				break;
			case 6: // Notice period DESC (ignore Immediate)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return -1; // Put missing/Immediate at the end
								}
								try {
									// Extract first number from comma-separated values
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {
									// If parsing fails
								}
								return -1; // Invalid values at the end
							"
						],
						'order' => 'desc'
					]
				];
				break;
			case 21: // Notice period Date ASC
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return 999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return 999999999; }
							def v = raw.trim();

							// Immediate should come first
							if (v.equals('Immediate')) { return -999999998; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return 999999999; // not a date -> last
									}
								}
							}
						"
					],
					'order' => 'asc'
				]
			];
			break;

			case 22: // Notice period Date DESC
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return -999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return -999999999; }
							def v = raw.trim();

							// Immediate should come after valid dates (but before null/non-date)
							if (v.equals('Immediate')) { return -888888888; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return -999999999; // not a date -> last
									}
								}
							}
						"
					],
					'order' => 'desc'
				]
			];
			break;
			case 7:
				$sort[] = ['total_exp' => ['order' => 'desc', 'unmapped_type' => 'float']];
				break;
			case 8:
				$sort[] = ['total_exp' => ['order' => 'asc', 'unmapped_type' => 'float']];
				break;
			case 9: // Immediate joiners first (original functionality)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "params['_source']['immediate_join'] == 'Immediate' ? 0 : 1"
						],
						'order' => 'asc'
					]
				];
				break;
			default:
				$sort[] = ['id' => ['order' => 'desc', 'unmapped_type' => 'long']];
				break;
		}
		

		$boolQuery = [
			'bool' => [
				'must' => [
					[
						'bool' => [
							'must' => [
								['term' => ['company_id' => $company_id]],
								['term' => ['condidate_type' => 'bench_condidate']],
								['term' => ['status' => 2]]
							]
						]
					]
				],
				'filter' => [],
				'must_not' => []
			]
		];

		


		// Skills filter — must(mandatory)+should(optional) hybrid, mirroring
		// similar_existing_data_ajax(): starred skills are a hard requirement
		// (must — every one of them has to match), skills typed but left
		// unstarred stay additive once at least one skill is starred (they
		// still boost relevance/Match % via $requiredSkills, but are never
		// required on their own). If nothing is starred, behaviour is
		// unchanged: match at least one of the entered skills.
		if (!empty($mandatorySkills)) {
			$mustSkills = [];
			foreach ($mandatorySkills as $skill) {
				$mustSkills[] = GeneralMethod::buildSkillMatchClause($skill);
			}

			$skillBool = ['must' => $mustSkills];

			$optionalSkills = array_values(array_diff($requiredSkills, $mandatorySkills));
			if (!empty($optionalSkills)) {
				$shouldSkills = [];
				foreach ($optionalSkills as $skill) {
					$shouldSkills[] = GeneralMethod::buildSkillMatchClause($skill);
				}
				// No minimum_should_match here on purpose — these are
				// preferred, not required.
				$skillBool['should'] = $shouldSkills;
			}

			$boolQuery['bool']['filter'][] = [
				'bool' => $skillBool
			];
		} elseif (!empty($requiredSkills)) {
			$skillShould = [];

			foreach ($requiredSkills as $skill) {
				$skillShould[] = GeneralMethod::buildSkillMatchClause($skill);
			}
			// append skill filter
			$boolQuery['bool']['filter'][] = [
				'bool' => [
					'should' => $skillShould,
					'minimum_should_match' => 1
				]
			];
		}

		// Experience filter
	

			$range = [];

			// Minimum experience
			if ($min_exp !== '' && $min_exp !== null) {
				$range['gte'] = (float) $min_exp;
			}

			// Maximum experience
			if ($max_exp !== '' && $max_exp !== null) {
				$range['lte'] = (float) $max_exp;
			}

			// Add filter query
		if (!empty($range)) {
			$boolQuery['bool']['filter'][] = [
				'range' => [
					'total_exp' => $range
				]
			];
		}
		// if (!empty($max_exp)) {
		// 	$boolQuery['bool']['filter'][] = [
		// 		'range' => [
		// 			'total_exp' => [
		// 				'gte' => (float)$min_exp,
		// 				'lte' => (float)$max_exp
		// 			]
		// 		]
		// 	];
		// }
		
		// Salary filter

		$salaryField = match($sal_mode) {
				0 => 'ectc_yearly',
				1 => 'ectc_monthly',
				2 => 'ectc_hourly',
				default => 'ectc_yearly'
		};

		if (($min_salary !== null && $min_salary !== '' && $min_salary != 0.00) || ($max_salary !== null && $max_salary !== '' && $max_salary != 0.00)) {

			$salaryRange = [];

			// Minimum salary
			if ($min_salary !== null && $min_salary !== '' && $min_salary != 0.00) {
				$salaryRange['gte'] = (float) $min_salary;
			}

			// Maximum salary
			if ($max_salary !== null && $max_salary !== '' && $max_salary != 0.00) {
				$salaryRange['lte'] = (float) $max_salary;
			}

			$boolQuery['bool']['filter'][] = [
				'range' => [
					$salaryField => $salaryRange
				]
			];
		}

		// if (!empty($max_salary)) {
		// 	// Normalize inputs

        //     $min = is_numeric($min_salary) ? (float) $min_salary : 0.0;
        //     $max = is_numeric($max_salary) ? (float) $max_salary : null;
            
        //     if($min >0 && $max > 0){
                
                
    	// 		$salaryField = 'ectc_yearly'; // default
    	// 		switch ($sal_mode) {
    	// 			case 2: $salaryField = 'ectc_hourly'; break;
    	// 			case 1: $salaryField = 'ectc_monthly'; break;
    	// 		}
    			
    	// 		$boolQuery['bool']['filter'][] = [
    	// 			'range' => [
    	// 				$salaryField => [
    	// 					'gte' => (float)$min_salary,
    	// 					'lte' => (float)$max_salary
    	// 				]
    	// 			]
    	// 		];
			
                
        //     }else{
            
        //         // Decide salary field
        //         $salaryField = 'ectc_yearly'; // default
        //         switch ($sal_mode) {
        //             case 2:
        //                 $salaryField = 'ectc_hourly';
        //                 break;
        //             case 1:
        //                 $salaryField = 'ectc_monthly';
        //                 break;
        //         }
                
        //         // Build salary filter
        //         $salaryFilter = [
        //             'bool' => [
        //                 'should' => [
        //                     // 1️⃣ Salary >= min (covers numeric values)
        //                     [
        //                         'range' => [
        //                             $salaryField => [
        //                                 'gte' => $min
        //                             ]
        //                         ]
        //                     ],
                
        //                     // 2️⃣ Salary IS NULL (field missing)
        //                     [
        //                         'bool' => [
        //                             'must_not' => [
        //                                 ['exists' => ['field' => $salaryField]]
        //                             ]
        //                         ]
        //                     ]
        //                 ],
        //                 'minimum_should_match' => 1
        //             ]
        //         ];
                
        //         // Add max salary only if provided and > 0
        //         if ($max !== null && $max > 0) {
        //             $salaryFilter['bool']['should'][0]['range'][$salaryField]['lte'] = $max;
        //         }
                
        //         // Attach filter to main query
        //         $boolQuery['bool']['filter'][] = $salaryFilter;
        //     }
		// }
		
		// Location filter
		if (!empty($location)) {
			$boolQuery['bool']['filter'][] = [
				'terms' => [
					'city_id' => $location
				]
			];
		}

		if ((int) $app_int_outsource > 0) {
			$boolQuery['bool']['filter'][] = [
				'term' => [
					'outsourcing_type' => $app_int_outsource
				]
			];
		}

		// Education filter
		if (!empty($education)) {
			$boolQuery['bool']['filter'][] = [
				'terms' => [
					'qualification' => $education
				]
			];
		}
		
		// Status filter
		if (!empty($app_int_status) && (int)$app_int_status > 0) {
			$boolQuery['bool']['filter'][] = [
				'term' => [
					'job_master_status_id' => (int)$app_int_status
				]
			];
		}
		
		// Notice period filter
		if (!empty($app_int_notice) && (int)$app_int_notice > 1) {
			$noticeFilter = $this->getNoticePeriodFilter($app_int_notice, $current_date);
			if ($noticeFilter) {
				$boolQuery['bool']['filter'][] = $noticeFilter;
			}
		}
		
		// Activity filter
		if (!empty($app_int_activity)) {
			$activityFilter = $this->getActivityFilter(
				$app_int_activity,
				$current_previous_30_days,
				$current_previous_60_days,
				$current_previous_90_days,
				$current_previous_180_days,
				$current_previous_1_year,
				$current_date
			);
			if ($activityFilter) {
				$boolQuery['bool']['filter'][] = $activityFilter;
			}
		}
		
		

		$existingCandidatesQuery = DB::table('add_job_wise_condidates')
			->where('job_id', '=', $jobid)->pluck('job_application_id')->toArray();

			// if (!empty($existingCandidatesQuery)) {
			// 		$boolQuery['bool']['must_not'][] = [
			// 			'terms' => [
			// 				'id' => $existingCandidatesQuery
			// 			]
			// 		];
			// }

		if ($valCheck==22) {
			$today = date('Y-m-d');  
			$next30 = date('Y-m-d', strtotime('+30 days'));

			// Serving candidates filter
			$servingCandidateFilter = [
				'script' => [
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) return false;

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) return false;

							def v = raw.trim();

							if (v.equals('Immediate')) return false; // exclude 'Immediate'

							int comma = v.indexOf(',');
							if (comma > -1) v = v.substring(0, comma).trim();

							int t = v.indexOf('T');
							if (t > -1) v = v.substring(0, t);

							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								long epoch = d.toEpochDay();
								long today = java.time.LocalDate.parse(params.today).toEpochDay();
								long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

								return epoch >= today && epoch <= next30;
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									long epoch = d.toEpochDay();
									long today = java.time.LocalDate.parse(params.today).toEpochDay();
									long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

									return epoch >= today && epoch <= next30;
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										long epoch = d.toEpochDay();
										long today = java.time.LocalDate.parse(params.today).toEpochDay();
										long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

										return epoch >= today && epoch <= next30;
									} catch (Exception e3) {
										return false; // not a valid date
									}
								}
							}
						",
						'params' => [
							'today'  => $today,
							'next30' => $next30
						]
					]
				]
			];
		    // Attach serving candidate condition along with others
            $boolQuery['bool']['filter'][] = $servingCandidateFilter;
		}

        //  dd($boolQuery);

		// Build the final query
		$params = [
			'index' => ELASTIC_CANDIDATES_BENCH,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => $sort,
				'query' => $boolQuery,
				'collapse' => [
					'field' => 'email'
				]
			]
		];

		if(!$this->elasticService->indexExists(ELASTIC_CANDIDATES_BENCH)) {
				$total = 0;
				$data = [];
		}else{
				$response = $this->elasticService->getClient()->search($params);
				// This total reflects ALL matching docs BEFORE collapse
				$totalBeforeCollapse = $response['hits']['total']['value'] ?? 0;

				// Count of current page records (after pagination & collapse)
				$records = $response['hits']['hits'] ?? [];
				$returnedCount = count($records);

				// Optional: if you want real total of unique candidate_id (after collapse), use an aggregation
				$aggParams = [
					'index' => ELASTIC_CANDIDATES_BENCH,
					'body' => [
						'size' => 0,
						'query' => $boolQuery,
						'aggs' => [
							'unique_candidates' => [
								'cardinality' => [
									'field' => 'email'
								]
							]
						]
					]
				];
				$aggResponse = $this->elasticService->getClient()->search($aggParams);
				$total = $aggResponse['aggregations']['unique_candidates']['value'] ?? $returnedCount;
		
				$data = array_map(function ($hit) use ($requiredSkills, $mandatorySkills) {

			$source = $hit['_source'];
			$jobSkills = collect(data_get($source, 'job_application_skills', []))
			->pluck('skills')
			->filter()
			->unique()
			->toArray();

				$matchedSkills = GeneralMethod::matchedSkillsList($jobSkills, $requiredSkills);
				$matchCount = count($matchedSkills);
				// Which matches were against a STARRED/mandatory requirement
				// vs just an entered-but-not-starred one — feeds the 3-tier
				// skill chip rendering in internal_bench_ajax.blade.php.
				$matchedMandatorySkills = GeneralMethod::matchedSkillsList($jobSkills, $mandatorySkills);
				return array_merge($source, [
					'matched_skill_count' => $matchCount,
					'matched_skills' => $matchedSkills,
					'matched_mandatory_skills' => $matchedMandatorySkills
				]);
			}, $response['hits']['hits']);
		}
			
			$pagination = [
				'total' => $total,
				'current_page' => $page,
				'last_page' =>ceil($total / $size),
				'listType' => 'allcandidate',
			];

			$userPermissions = $this->userPermissions;
			$app_int_activity = request('app_int_activity');
			$app_int_status = request('app_int_status');
			$app_int_notice = request('app_int_notice');
			$sal_mode = request('sal_mode');
			$min_salary_currency = request('min_salary_currency');
			$app_int_outsource = request('app_int_outsource');
			$responseData =  [
				'html' => view('service_company.ajax_pages.internal_bench_ajax', compact('data', 'userPermissions', 'app_int_status', 'app_int_activity', 'app_int_notice', 'sal_mode', 'min_salary_currency', 'app_int_outsource','jobid','requiredSkills','modeFilter','currencyFilter','symbolsFilter'))->render(),
				'total'=>$total,
				'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
			];

			cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_INTERNAL_BENCH_TIME));


	return response()->json($responseData);

	}

	public function career_apply($id)
	{
        // dd(auth()->user()->name);
		$id = GeneralMethod::value_decrypt($id);
		$lock_unlock = GeneralMethod::checksubscription();
		if ($lock_unlock == false) {
			return redirect()->route('service_company.dashboard')->with('message_paid', 'This is a paid featute');
			;
		}


		$this->pageTitle = __('Candidates Recommended');
		$company_id = auth()->user()->company_id;
		$this->job = Job::findOrFail($id);

		// ES-only shared source (see GeneralMethod::jobDefaultsFromEs()) —
		// same one similar_candidates() and custome_count_all() (the header
		// counter on every tab) use, so this page's defaults can't drift
		// out of sync with the header count the way gdFilterData()/
		// JobcitiesTags() (MySQL) vs custome_count_all() (ES) used to.
		$jobDefaults = GeneralMethod::jobDefaultsFromEs($id);

		$this->default_loc = $jobDefaults->default_loc;
		$this->qualification = GeneralMethod::allQualificationSelected($id);
		$this->min_exp = ($jobDefaults->min_exp >= 1) ? $jobDefaults->min_exp - 1 : $jobDefaults->min_exp;
		$this->max_exp = $jobDefaults->max_exp + 1;
		$this->min_salary_currency = $jobDefaults->min_salary_currency;
		// Every AppTracker filter drawer defaults Min Salary to 0, not the
		// job's own min_salary (see custome_count_all()'s salary-floor
		// comments) — the header counter and this page have to agree on
		// the same floor or their candidate counts disagree on load.
		$this->min_salary = 0;
		$this->max_salary = $jobDefaults->max_salary;
		$this->min_sal_mode = $jobDefaults->min_sal_mode;
		$this->default_skill = $jobDefaults->default_skill;

		$this->custome_count = $this->custome_count($id);
		$return_data = GeneralMethod::custome_count_all($id);
		$this->pool_counter = $return_data->pool_counter;
		$this->existing_counter = $return_data->existing_counter;
		$this->internal_bench_counter = $return_data->internal_bench_counter;		
		$this->public_bench_counter = $return_data->public_bench_counter;	
		$this->recomnded_counter = $return_data->recomnded_counter;	
		$this->career_counter = $return_data->career_counter;;
          // dd($gdFilterData);
		$this->cities = GeneralMethod::allCities();
		$this->qualificationTag = DB::table('qualification_master')
			->select('*')
			->whereNotIn('id',[60])
			->orderBy('qualification_master.display_order', 'ASC')
			->get()
			->map(function ($row) {
				return $row->name;
			});

		session()->put('job_id', $id);

		return view('service_company.apptracker.career_apply', $this->data);
	}

	public function career_apply_data_ajax(Request $request)
	{

		$company_id = auth()->user()->company_id;

		$searchTerms = request('cus_search');
		$custom_search = json_decode($searchTerms, true);
		if (\request('cus_search') != "") {
			$skillsAddElastic = array_column($custom_search, 'value');
			GeneralMethod::skillsAddElastic($skillsAddElastic);
		}

		$jobid = $request->jobid;
		$job = Job::findOrFail($jobid);

		$location = request('kt_tagify_location');
		$custom_location = json_decode($location, true);

		$education = request('kt_tagify_education');
		$custom_education = json_decode($education, true);

		$company = request('kt_tagify_company');
		$custom_company = json_decode($company, true);
		$current_date = date('Y-m-d H:i:s');
		$current_timestamp = strtotime($current_date);
		$current_previous_30_days = date('Y-m-d H:i:s', strtotime('-30 days', $current_timestamp));
		$current_previous_60_days = date('Y-m-d H:i:s', strtotime('-60 days', $current_timestamp));
		$current_previous_90_days = date('Y-m-d H:i:s', strtotime('-90 days', $current_timestamp));
		$current_previous_180_days = date('Y-m-d H:i:s', strtotime('-180 days', $current_timestamp));
		$current_previous_1_year = date('Y-m-d H:i:s', strtotime('-365 days', $current_timestamp));
		$app_int_activity = request('app_int_activity');
		$app_int_status = request('app_int_status');
		$app_int_notice = request('app_int_notice');
		$sal_mode = request('sal_mode') ?? $job->max_sal_mode;
		$min_salary_currency = request('min_salary_currency') ?? $job->max_salary_currency;
		$app_int_outsource = request('app_int_outsource');

		
		$symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
		$symbolsValue = !empty($symbolsValue) ? $symbolsValue : [
			0	=>	"INR",
			1	=>	"EUR",
			2	=>	"JPY",
			3	=>	"GBP",
			4	=>	"USD",
			5	=>	"AED"
		];
		$modeFilter = $sal_mode ?? $job->max_sal_mode;
		$currencyFilter = $min_salary_currency ?? $job->max_salary_currency;
		$symbolsFilter = $symbolsValue[$currencyFilter] ?? "INR";


		

		$title = $job->title;
		if (\request('min_exp') != "" && \request('max_exp') != "") {
			$min_exp = request('min_exp');
			$max_exp = request('max_exp');
		} else {
			$min_exp = 0;//$job->min_exp;
			$max_exp = $job->max_exp;
		}

		$currency = GeneralMethod::currency();  // Currency code
		$currencyCheck = $currency[$request->min_salary_currency];

		if (\request('min_sal') != "" && \request('max_sal') != "") {
			$AmountMin = request('min_sal');
			$AmountMax = request('max_sal');
			if ($request->min_salary_currency > 0) {
				$AmountMin = GeneralMethod::convertToINR($AmountMin, $currencyCheck);
				$AmountMax = GeneralMethod::convertToINR($AmountMax, $currencyCheck);
			}
			$ctcdividedMin = GeneralMethod::salaryDividedByMode($sal_mode, $AmountMin);
			$ctcdividedMax = GeneralMethod::salaryDividedByMode($sal_mode, $AmountMax);
			if ($sal_mode == 0) {
				$min_salary = $ctcdividedMin->yearly;
				$max_salary = $ctcdividedMax->yearly;

			} elseif ($sal_mode == 1) {
				$min_salary = $ctcdividedMin->monthly;
				$max_salary = $ctcdividedMax->monthly;

			} elseif ($sal_mode == 2) {
				$min_salary = $ctcdividedMin->hourly;
				$max_salary = $ctcdividedMax->hourly;

			}




		} else {

			$convertedAmount = request('max_sal'); //$job->max_salary;
			if ($request->min_salary_currency > 0) {
				$convertedAmount = GeneralMethod::convertToINR($convertedAmount, $currencyCheck);
			}
			$ectcdivided = GeneralMethod::salaryDividedByMode($sal_mode, $convertedAmount);
			$min_salary = 0;//$job->min_salary;
			// $max_salary = $job->max_salary;	

			if ($sal_mode == 0) {
				$max_salary = $ectcdivided->yearly;

			} elseif ($sal_mode == 1) {
				$max_salary = $ectcdivided->monthly;

			} elseif ($sal_mode == 2) {
				$max_salary = $ectcdivided->hourly;

			}

		}

		// dd($min_salary,$max_salary);

		$location = array();
		if (\request('kt_tagify_location') != "") {
			foreach ($custom_location as $sval) {
				$parts = explode('|', $sval['value']);
				// Trim to remove any leading or trailing whitespace
				$cityName = trim($parts[0]);
				$loc_data = DB::table('city_list')->where('city_list.city', '=', $cityName)->first();
				if ($loc_data) {
					array_push($location, $loc_data->city_id);
				}

			}
		}

		$education = array();
		if (\request('kt_tagify_education') != "") {
			foreach ($custom_education as $sval) {
				$education_data = DB::table('qualification_master')->where('qualification_master.name', '=', $sval['value'])->first();
				if ($education_data) {
					$educationfiletr = GeneralMethod::qualificationFilterCondition($education_data->id);
					$education = array_merge($education, $educationfiletr);
				}
			}
		}

		$loc_data = DB::table('client_location_jobs')
			->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
			->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
			->select(
				'client_location.address',
				'client_location.city',
				'client_location.postcode',
				'client_location.country_id',
				'city_list.city as city_name'
			)
			->where('client_location_jobs.job_id', $jobid)
			->first();

		$postcode = @$loc_data->postcode;


		$skill_arr = array();
		$skill_data = DB::table('job_metronics_skills')->select('*')->where('job_id', $jobid)->get()->toArray();
		foreach ($skill_data as $sval) {
			$skill_arr[] = $sval->skill_name;
		}
		$company_arr = array($company_id);
		//echo '<pre>'; print_r($skill_arr); exit; 


		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();

		$chkAdminRole = $this->user->hasRole('admin');

		// $chkAdminRole = DB::table('roles')
		// 	->select('*')
		// 	->where('id', $this->user->role->role_id)
		// 	->where('name', 'admin')
		// 	->count();

		$requiredSkills = [];
		if (\request('cus_search') != "") {
			$checkedSkills = array_filter($custom_search, function ($skill) {
				return $skill['checked'] == true;
			});
			$uncheckedSkills = array_filter($custom_search, function ($skill) {
				return $skill['checked'] == false;
			});
			if (!empty($checkedSkills)) {
				foreach ($checkedSkills as $skill) {
					$requiredSkills = array_column($checkedSkills, 'value');
				}
			} else {
				foreach ($uncheckedSkills as $skill) {
					$requiredSkills = array_column($uncheckedSkills, 'value');
				}
			}
		}



		$valCheck = $request->input('sortingfilter');
		$country = '';
		$searchQuery = $request->input('q');
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;
		$requiredSkills = array_map('strtolower', $requiredSkills);



		$cachePayload = [
			'company' => $company_id,
			'page' => $page,
			'size' => $size,
			'q' => $searchQuery,
			'jobid' => $jobid,
			'cus_search' => $requiredSkills ?? [],
			'sortingfilter' => $valCheck,
			'min_exp' => $min_exp,
			'max_exp' => $max_exp,
			'min_salary_currency' => $min_salary_currency,
			'education' => $education ?? [],
			'location' => $location ?? [],
			'min_sal' => $min_salary,
			'max_sal' => $max_salary,
			'app_int_status'=>$app_int_status,
			'app_int_activity'=>$app_int_activity,
			'app_int_notice'=>$app_int_notice,
			'app_int_outsource'=>$app_int_outsource,
			'user_id' => $this->user->id,
			'sal_mode' =>$sal_mode
		];

		// dd($cachePayload );
		
		$versionKey = 'allcareerapplydata_version_user_'.$this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:allcareerapplydata:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
		/* ----------------------------
		⚡ Check DragonflyDB first
		----------------------------- */
		if (cache()->has($cacheKeySet)) {
			return response()->json(cache()->get($cacheKeySet));
		}
         
		// $sort[] = ['status' => ['order' => 'asc', 'unmapped_type' => 'long']];
		switch ($valCheck) {
			case 1:
				$sort[] = ['updated_at' => ['order' => 'desc']];
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "doc['status'].size() == 0 ? 0 : doc['status'].value"
						],
						'order' => 'asc'
					]
				];
				break;
			case 2:
				$sort[] = ['updated_at' => ['order' => 'asc']];
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "doc['status'].size() == 0 ? 0 : doc['status'].value"
						],
						'order' => 'asc'
					]
				];
				break;
			case 3:
				$sort[] = ['ctc' => ['order' => 'asc', 'unmapped_type' => 'float']];
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "doc['status'].size() == 0 ? 0 : doc['status'].value"
						],
						'order' => 'asc'
					]
				];
				break;
			case 4:
				$sort[] = ['ctc' => ['order' => 'desc', 'unmapped_type' => 'float']];
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "doc['status'].size() == 0 ? 0 : doc['status'].value"
						],
						'order' => 'asc'
					]
				];
				break;
			case 5: // Notice period ASC (ignore Immediate)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return 999999; // Put missing/Immediate at the end
								}
								try {
									// Extract first number from comma-separated values
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {
									// If parsing fails
								}
								return 999999; // Invalid values at the end
							"
						],
						'order' => 'asc'
					]
				];
				break;
			case 6: // Notice period DESC (ignore Immediate)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return -1; // Put missing/Immediate at the end
								}
								try {
									// Extract first number from comma-separated values
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {
									// If parsing fails
								}
								return -1; // Invalid values at the end
							"
						],
						'order' => 'desc'
					]
				];
				break;
			case 21: // Notice period Date ASC
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return 999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return 999999999; }
							def v = raw.trim();

							// Immediate should come first
							if (v.equals('Immediate')) { return -999999998; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return 999999999; // not a date -> last
									}
								}
							}
						"
					],
					'order' => 'asc'
				]
			];
			break;

			case 22: // Notice period Date DESC
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return -999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return -999999999; }
							def v = raw.trim();

							// Immediate should come after valid dates (but before null/non-date)
							if (v.equals('Immediate')) { return -888888888; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return -999999999; // not a date -> last
									}
								}
							}
						"
					],
					'order' => 'desc'
				]
			];
			break;
			case 7:
				$sort[] = ['total_exp' => ['order' => 'desc', 'unmapped_type' => 'float']];
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "doc['status'].size() == 0 ? 0 : doc['status'].value"
						],
						'order' => 'asc'
					]
				];
				break;
			case 8:
				$sort[] = ['total_exp' => ['order' => 'asc', 'unmapped_type' => 'float']];
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "doc['status'].size() == 0 ? 0 : doc['status'].value"
						],
						'order' => 'asc'
					]
				];
				break;
			case 9: // Immediate joiners first (original functionality)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "params['_source']['immediate_join'] == 'Immediate' ? 0 : 1"
						],
						'order' => 'asc'
					]
				];
				break;
			default:
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "doc['status'].size() == 0 ? 0 : doc['status'].value"
						],
						'order' => 'asc'
					]
				];
				break;
		}

		$boolQuery = [
			'bool' => [
				'should' => [
					// First condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['company_id' => $company_id]],
								['term' => ['job_id' => $jobid]],
								['term' => ['status_type' => 'DIRECT']]
							])
						]
					]
				],
				'minimum_should_match' => 1,
				'filter' => [], // We'll add all filters here
			]
		];

	


		if ($valCheck==22) {
			$today = date('Y-m-d');  
			$next30 = date('Y-m-d', strtotime('+30 days'));

			// Serving candidates filter
			$servingCandidateFilter = [
				'script' => [
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) return false;

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) return false;

							def v = raw.trim();

							if (v.equals('Immediate')) return false; // exclude 'Immediate'

							int comma = v.indexOf(',');
							if (comma > -1) v = v.substring(0, comma).trim();

							int t = v.indexOf('T');
							if (t > -1) v = v.substring(0, t);

							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								long epoch = d.toEpochDay();
								long today = java.time.LocalDate.parse(params.today).toEpochDay();
								long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

								return epoch >= today && epoch <= next30;
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									long epoch = d.toEpochDay();
									long today = java.time.LocalDate.parse(params.today).toEpochDay();
									long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

									return epoch >= today && epoch <= next30;
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										long epoch = d.toEpochDay();
										long today = java.time.LocalDate.parse(params.today).toEpochDay();
										long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

										return epoch >= today && epoch <= next30;
									} catch (Exception e3) {
										return false; // not a valid date
									}
								}
							}
						",
						'params' => [
							'today'  => $today,
							'next30' => $next30
						]
					]
				]
			];
		    // Attach serving candidate condition along with others
            $boolQuery['bool']['filter'][] = $servingCandidateFilter;
		}



		// Build the final query
		$params = [
			'index' => ELASTIC_CAREER_APPLY,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => $sort,
				'query' => $boolQuery,
				'collapse' => [
					'field' => 'email'
				]
			]
		];

		if(!$this->elasticService->indexExists(ELASTIC_CAREER_APPLY)) {
				$total = 0;
				$data = [];
		}else{
				$response = $this->elasticService->getClient()->search($params);
				// This total reflects ALL matching docs BEFORE collapse
				$totalBeforeCollapse = $response['hits']['total']['value'] ?? 0;

				// Count of current page records (after pagination & collapse)
				$records = $response['hits']['hits'] ?? [];
				$returnedCount = count($records);

				// Optional: if you want real total of unique candidate_id (after collapse), use an aggregation
				$aggParams = [
					'index' => ELASTIC_CAREER_APPLY,
					'body' => [
						'size' => 0,
						'query' => $boolQuery,
						'aggs' => [
							'unique_candidates' => [
								'cardinality' => [
									'field' => 'email'
								]
							]
						]
					]
				];
				$aggResponse = $this->elasticService->getClient()->search($aggParams);
				$total = $aggResponse['aggregations']['unique_candidates']['value'] ?? $returnedCount;
		
				$data = array_map(function ($hit) use ($requiredSkills) {
					
				$source = $hit['_source'];
				$jobSkills = collect(data_get($source, 'job_application_skills', []))
				->pluck('skills')
				->filter()
				->unique()
				->toArray();

					$matchedSkills = GeneralMethod::matchedSkillsList($jobSkills, $requiredSkills);
					$matchCount = count($matchedSkills);
					return array_merge($source, [
						'matched_skill_count' => $matchCount,
						'matched_skills' => $matchedSkills
					]);
				}, $response['hits']['hits']);
		}
			
			$pagination = [
				'total' => $total,
				'current_page' => $page,
				'last_page' =>ceil($total / $size),
				'listType' => 'allcandidate',
			];
			// dd($pagination);

			$userPermissions = $this->userPermissions;
			$app_int_activity = request('app_int_activity');
			$app_int_status = request('app_int_status');
			$app_int_notice = request('app_int_notice');
			$sal_mode = request('sal_mode');
			$min_salary_currency = request('min_salary_currency');
			$app_int_outsource = request('app_int_outsource');
			$responseData =  [
				'html' => view('service_company.ajax_pages.career_apply_ajax', compact('data', 'userPermissions', 'app_int_status', 'app_int_activity', 'app_int_notice', 'sal_mode', 'min_salary_currency', 'app_int_outsource','jobid','requiredSkills','modeFilter','currencyFilter','symbolsFilter'))->render(),
				'total'=>$total,
				'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
			];

			cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_CAREER_APPLY_DATA_TIME));


	return response()->json($responseData);

	}

	public function public_bench($id)
	{

		$id = GeneralMethod::value_decrypt($id);
		$this->pageTitle = __('Candidates Recommended');
		$company_id = auth()->user()->company_id;
		$this->job = Job::findOrFail($id);

		$this->custome_count = $this->custome_count($id);
		$return_data = GeneralMethod::custome_count_all($id);
		$this->pool_counter = $return_data->pool_counter;
		$this->existing_counter = $return_data->existing_counter;
		$this->internal_bench_counter = $return_data->internal_bench_counter;		
		$this->public_bench_counter = $return_data->public_bench_counter;
		$this->recomnded_counter = $return_data->recomnded_counter;		
		$this->career_counter = $return_data->career_counter;;
		session()->put('job_id', $id);


		// ES-only shared source (see GeneralMethod::jobDefaultsFromEs()) —
		// same one similar_candidates() and custome_count_all() (the header
		// counter on every tab) use, so this page's defaults can't drift
		// out of sync with the header count the way gdFilterData()/
		// JobcitiesTags() (MySQL) vs custome_count_all() (ES) used to.
		$jobDefaults = GeneralMethod::jobDefaultsFromEs($id);

		$this->default_loc = $jobDefaults->default_loc;
		$this->qualification = '';// GeneralMethod::allQualificationSelected($id);
		$this->min_exp = ($jobDefaults->min_exp >= 1) ? $jobDefaults->min_exp - 1 : $jobDefaults->min_exp;
		$this->max_exp = $jobDefaults->max_exp + 1;
		$this->min_salary_currency = $jobDefaults->min_salary_currency;
		// Every AppTracker filter drawer defaults Min Salary to 0, not the
		// job's own min_salary (see custome_count_all()'s salary-floor
		// comments) — the header counter and this page have to agree on
		// the same floor or their candidate counts disagree on load.
		$this->min_salary = 0;
		$this->max_salary = $jobDefaults->max_salary;
		$this->min_sal_mode = $jobDefaults->min_sal_mode;
		$this->default_skill = $jobDefaults->default_skill;

		$this->cities = GeneralMethod::allCities();
		$this->qualificationTag = DB::table('qualification_master')
			->select('*')
			->whereNotIn('id',[60])
			->orderBy('qualification_master.display_order', 'ASC')
			->get()
			->map(function ($row) {
				return $row->name;
			});



		return view('service_company.apptracker.public_bench', $this->data);
	}

	public function public_bench_data_ajax(Request $request)
	{

		$company_id = auth()->user()->company_id;


		$searchTerms = request('cus_search');
		$custom_search = json_decode($searchTerms, true);
		if (\request('cus_search') != "") {
			$skillsAddElastic = array_column($custom_search, 'value');
			GeneralMethod::skillsAddElastic($skillsAddElastic);
		}

		$jobid = $request->jobid;
		$job = Job::findOrFail($jobid);

		$title = $job->title;

		$location = request('kt_tagify_location');
		$custom_location = json_decode($location, true);

		$education = request('kt_tagify_education');
		$custom_education = json_decode($education, true);

		$company = request('kt_tagify_company');
		$custom_company = json_decode($company, true);
		$current_date = date('Y-m-d H:i:s');
		$current_timestamp = strtotime($current_date);
		$current_previous_30_days = date('Y-m-d H:i:s', strtotime('-30 days', $current_timestamp));
		$current_previous_60_days = date('Y-m-d H:i:s', strtotime('-60 days', $current_timestamp));
		$current_previous_90_days = date('Y-m-d H:i:s', strtotime('-90 days', $current_timestamp));
		$current_previous_180_days = date('Y-m-d H:i:s', strtotime('-180 days', $current_timestamp));
		$current_previous_1_year = date('Y-m-d H:i:s', strtotime('-365 days', $current_timestamp));
		$app_int_activity = request('app_int_activity');
		$app_int_status = request('app_int_status');
		$app_int_notice = request('app_int_notice');
		$sal_mode = request('sal_mode') ?? $job->max_sal_mode;
		$min_salary_currency = request('min_salary_currency') ?? $job->max_salary_currency;
		$app_int_outsource = request('app_int_outsource');

		
		$symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
		$symbolsValue = !empty($symbolsValue) ? $symbolsValue : [
			0	=>	"INR",
			1	=>	"EUR",
			2	=>	"JPY",
			3	=>	"GBP",
			4	=>	"USD",
			5	=>	"AED"
		];
		

		$modeFilter = $sal_mode ?? $job->max_sal_mode;
		$currencyFilter = $min_salary_currency ?? $job->max_salary_currency;
		$symbolsFilter = $symbolsValue[$currencyFilter] ?? "INR";


	

		if (\request('min_exp') != "" || \request('max_exp') != "") {
			$min_exp = request('min_exp');
			$max_exp = request('max_exp');
		} else {
			$min_exp = '';//$job->min_exp;
			$max_exp = '';//$job->max_exp;
		}
		

		$currency = GeneralMethod::currency();  // Currency code
		$currencyCheck = $currency[$currencyFilter];

		if (\request('min_sal') != "" || \request('max_sal') != "") {
			$AmountMin = request('min_sal');
			$AmountMax = request('max_sal');
			$min_salary = $AmountMin;
			$max_salary = $AmountMax;
			if ($currencyFilter > 0) {
				$min_salary = GeneralMethod::convertToINR($AmountMin, $currencyCheck);
				$max_salary = GeneralMethod::convertToINR($AmountMax, $currencyCheck);
			}
			// $ctcdividedMin = GeneralMethod::salaryDividedByMode($sal_mode, $AmountMin);
			// $ctcdividedMax = GeneralMethod::salaryDividedByMode($sal_mode, $AmountMax);
			// if ($sal_mode == 0) {
			// 	$min_salary = $ctcdividedMin->yearly;
			// 	$max_salary = $ctcdividedMax->yearly;

			// } elseif ($sal_mode == 1) {
			// 	$min_salary = $ctcdividedMin->monthly;
			// 	$max_salary = $ctcdividedMax->monthly;

			// } elseif ($sal_mode == 2) {
			// 	$min_salary = $ctcdividedMin->hourly;
			// 	$max_salary = $ctcdividedMax->hourly;

			// }

		} else {

			$convertedAmount = request('max_sal');//$job->max_salary;
			$max_salary = $convertedAmount;
			$min_salary = '';//$job->min_salary;
			if ($request->min_salary_currency > 0) {
				$max_salary = GeneralMethod::convertToINR($convertedAmount, $currencyCheck);
			}
			// $ectcdivided = GeneralMethod::salaryDividedByMode($sal_mode, $convertedAmount);
			// // $max_salary = $job->max_salary;	
			// if ($sal_mode == 0) {
			// 	$max_salary = $ectcdivided->yearly;

			// } elseif ($sal_mode == 1) {
			// 	$max_salary = $ectcdivided->monthly;

			// } elseif ($sal_mode == 2) {
			// 	$max_salary = $ectcdivided->hourly;

			// }

			// dd("dkd");

		}

		$location = array();
		if (\request('kt_tagify_location') != "") {
			foreach ($custom_location as $sval) {
				$parts = explode('|', $sval['value']);
				// Trim to remove any leading or trailing whitespace
				$cityName = trim($parts[0]);
				$loc_data = DB::table('city_list')->where('city_list.city', '=', $cityName)->first();
				if ($loc_data) {
					array_push($location, $loc_data->city_id);
				}

			}
		}

		$education = array();
		if (\request('kt_tagify_education') != "") {
			foreach ($custom_education as $sval) {
				$education_data = DB::table('qualification_master')->where('qualification_master.name', '=', $sval['value'])->first();
				if ($education_data) {
					$educationfiletr = GeneralMethod::qualificationFilterCondition($education_data->id);
					$education = array_merge($education, $educationfiletr);
				}
			}
		}




		$loc_data = DB::table('client_location_jobs')
			->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
			->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
			->select(
				'client_location.address',
				'client_location.city',
				'client_location.postcode',
				'client_location.country_id',
				'city_list.city as city_name'
			)
			->where('client_location_jobs.job_id', $jobid)
			->first();

		$postcode = @$loc_data->postcode;


		$skill_arr = array();
		$skill_data = DB::table('job_metronics_skills')->select('*')->where('job_id', $jobid)->get()->toArray();
		foreach ($skill_data as $sval) {
			$skill_arr[] = $sval->skill_name;
		}
		$company_arr = array($company_id);


		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();

		$chkAdminRole = $this->user->hasRole('admin');

		// $chkAdminRole = DB::table('roles')
		// 	->select('*')
		// 	->where('id', $this->user->role->role_id)
		// 	->where('name', 'admin')
		// 	->count();

		// $mandatorySkills tracks only the starred/checked skills (used to build
		// a strict "must match" clause below). $requiredSkills is EVERY entered
		// skill, checked or not — it used to get overwritten to ONLY the checked
		// skills the instant any skill was starred, silently dropping every other
		// typed skill from filtering, the sort script, and the Match % badge.
		// Mirrors similar_existing_data_ajax()'s must(mandatory)+should(optional)
		// hybrid fix.
		$requiredSkills = [];
		$mandatorySkills = [];
		if (\request('cus_search') != "") {
			$checkedSkills = array_filter($custom_search, function ($skill) {
				return $skill['checked'] == true;
			});
			$requiredSkills = array_column($custom_search, 'value');
			$mandatorySkills = array_column($checkedSkills, 'value');
		}
		$requiredSkills = array_map('strtolower', $requiredSkills);
		$mandatorySkills = array_map('strtolower', $mandatorySkills);


		$valCheck = $request->input('sortingfilter');
		$country = '';
		$searchQuery = $request->input('q');
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;



		$cachePayload = [
			'company' => $company_id,
			'page' => $page,
			'size' => $size,
			'q' => $searchQuery,
			'jobid' => $jobid,
			'cus_search' => $requiredSkills ?? [],
			'sortingfilter' => $valCheck,
			'min_exp' => $min_exp,
			'max_exp' => $max_exp,
			'min_salary_currency' => $min_salary_currency,
			'education' => $education ?? [],
			'location' => $location ?? [],
			'min_sal' => $min_salary,
			'max_sal' => $max_salary,
			'app_int_status'=>$app_int_status,
			'app_int_activity'=>$app_int_activity,
			'app_int_notice'=>$app_int_notice,
			'app_int_outsource'=>$app_int_outsource,
			'user_id' => $this->user->id,
			'sal_mode' =>$sal_mode
		];

		// dd($cachePayload );		
		
		$versionKey = 'allpublicbench_version_user_'.$this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:allpublicbench:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
		/* ----------------------------
		⚡ Check DragonflyDB first
		----------------------------- */
		if (cache()->has($cacheKeySet)) {
			return response()->json(cache()->get($cacheKeySet));
		}

         
		$sort[] = [
			'_script' => [
				'type' => 'number',
				'script' => [
					'lang' => 'painless',
					'source' => '
						int matchCount = 0;
						if (doc.containsKey("job_application_skills.skills.keyword")) {
							for (s in params.skills) {
								if (doc["job_application_skills.skills.keyword"].contains(s)) {
									matchCount += 1;
								}
							}
						}
						return matchCount;
					',
					'params' => [
						'skills' => array_values($requiredSkills)
					]
				],
				'order' => 'desc'
			]
		];
		switch ($valCheck) {
			case 1:
				$sort[] = ['updated_at' => ['order' => 'desc']];
				break;
			case 2:
				$sort[] = ['updated_at' => ['order' => 'asc']];
				break;
			case 3:
				$sort[] = ['ctc' => ['order' => 'asc', 'unmapped_type' => 'float']];
				break;
			case 4:
				$sort[] = ['ctc' => ['order' => 'desc', 'unmapped_type' => 'float']];
				break;
			case 5: // Notice period ASC (ignore Immediate)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return 999999; // Put missing/Immediate at the end
								}
								try {
									// Extract first number from comma-separated values
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {
									// If parsing fails
								}
								return 999999; // Invalid values at the end
							"
						],
						'order' => 'asc'
					]
				];
				break;
			case 6: // Notice period DESC (ignore Immediate)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return -1; // Put missing/Immediate at the end
								}
								try {
									// Extract first number from comma-separated values
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {
									// If parsing fails
								}
								return -1; // Invalid values at the end
							"
						],
						'order' => 'desc'
					]
				];
				break;
			case 21: // Notice period Date ASC
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return 999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return 999999999; }
							def v = raw.trim();

							// Immediate should come first
							if (v.equals('Immediate')) { return -999999998; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return 999999999; // not a date -> last
									}
								}
							}
						"
					],
					'order' => 'asc'
				]
			];
			break;

			case 22: // Notice period Date DESC
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return -999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return -999999999; }
							def v = raw.trim();

							// Immediate should come after valid dates (but before null/non-date)
							if (v.equals('Immediate')) { return -888888888; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return -999999999; // not a date -> last
									}
								}
							}
						"
					],
					'order' => 'desc'
				]
			];
			break;
			case 7:
				$sort[] = ['total_exp' => ['order' => 'desc', 'unmapped_type' => 'float']];
				break;
			case 8:
				$sort[] = ['total_exp' => ['order' => 'asc', 'unmapped_type' => 'float']];
				break;
			case 9: // Immediate joiners first (original functionality)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "params['_source']['immediate_join'] == 'Immediate' ? 0 : 1"
						],
						'order' => 'asc'
					]
				];
				break;
			default:
				$sort[] = ['id' => ['order' => 'desc', 'unmapped_type' => 'long']];
				break;
		}

		$boolQuery = [
			'bool' => [
				'must' => [
					// First condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['condidate_type' => 'bench_condidate']],
								['term' => ['status' => 2]]
							])
						]
					]
				],
				'filter' => [], // We'll add all filters here
				'must_not' => [
					['term' => ['company_id' => $company_id]]
				]
			]
		];

		// Experience filter

			$range = [];

			// Minimum experience
			if ($min_exp !== '' && $min_exp !== null) {
				$range['gte'] = (float) $min_exp;
			}

			// Maximum experience
			if ($max_exp !== '' && $max_exp !== null) {
				$range['lte'] = (float) $max_exp;
			}

			// Add filter query
		if (!empty($range)) {
			$boolQuery['bool']['filter'][] = [
				'range' => [
					'total_exp' => $range
				]
			];
		}
		// if (!empty($max_exp)) {
		// 	$boolQuery['bool']['filter'][] = [
		// 		'range' => [
		// 			'total_exp' => [
		// 				'gte' => (float)$min_exp,
		// 				'lte' => (float)$max_exp
		// 			]
		// 		]
		// 	];
		// }
		
		// Salary filter

		$salaryField = match($sal_mode) {
				0 => 'ectc_yearly',
				1 => 'ectc_monthly',
				2 => 'ectc_hourly',
				default => 'ectc_yearly'
		};

		if (($min_salary !== null && $min_salary !== '' && $min_salary != 0.00) || ($max_salary !== null && $max_salary !== '' && $max_salary != 0.00)) {

			$salaryRange = [];

			// Minimum salary
			if ($min_salary !== null && $min_salary !== '' && $min_salary != 0.00) {
				$salaryRange['gte'] = (float) $min_salary;
			}

			// Maximum salary
			if ($max_salary !== null && $max_salary !== '' && $max_salary != 0.00) {
				$salaryRange['lte'] = (float) $max_salary;
			}

			$boolQuery['bool']['filter'][] = [
				'range' => [
					$salaryField => $salaryRange
				]
			];
		}

		// if (!empty($max_salary)) {
		// 	// Normalize inputs
        //     $min = is_numeric($min_salary) ? (float) $min_salary : 0.0;
        //     $max = is_numeric($max_salary) ? (float) $max_salary : null;
            
        //     if($min >0 && $max > 0){
                
                
    	// 		$salaryField = 'ectc_yearly'; // default
    	// 		switch ($sal_mode) {
    	// 			case 2: $salaryField = 'ectc_hourly'; break;
    	// 			case 1: $salaryField = 'ectc_monthly'; break;
    	// 		}
    			
    	// 		$boolQuery['bool']['filter'][] = [
    	// 			'range' => [
    	// 				$salaryField => [
    	// 					'gte' => (float)$min_salary,
    	// 					'lte' => (float)$max_salary
    	// 				]
    	// 			]
    	// 		];
			
                
        //     }else{
            
        //         // Decide salary field
        //         $salaryField = 'ectc_yearly'; // default
        //         switch ($sal_mode) {
        //             case 2:
        //                 $salaryField = 'ectc_hourly';
        //                 break;
        //             case 1:
        //                 $salaryField = 'ectc_monthly';
        //                 break;
        //         }
                
        //         // Build salary filter
        //         $salaryFilter = [
        //             'bool' => [
        //                 'should' => [
        //                     // 1️⃣ Salary >= min (covers numeric values)
        //                     [
        //                         'range' => [
        //                             $salaryField => [
        //                                 'gte' => $min
        //                             ]
        //                         ]
        //                     ],
                
        //                     // 2️⃣ Salary IS NULL (field missing)
        //                     [
        //                         'bool' => [
        //                             'must_not' => [
        //                                 ['exists' => ['field' => $salaryField]]
        //                             ]
        //                         ]
        //                     ]
        //                 ],
        //                 'minimum_should_match' => 1
        //             ]
        //         ];
                
        //         // Add max salary only if provided and > 0
        //         if ($max !== null && $max > 0) {
        //             $salaryFilter['bool']['should'][0]['range'][$salaryField]['lte'] = $max;
        //         }
                
        //         // Attach filter to main query
        //         $boolQuery['bool']['filter'][] = $salaryFilter;
        //     }
		// }
		
		// Location filter
		if (!empty($location)) {
			$boolQuery['bool']['filter'][] = [
				'terms' => [
					'city_id' => $location
				]
			];
		}

		if ((int) $app_int_outsource > 0) {
			$boolQuery['bool']['filter'][] = [
				'term' => [
					'outsourcing_type' => $app_int_outsource
				]
			];
		}

		// Education filter
		if (!empty($education)) {
			$boolQuery['bool']['filter'][] = [
				'terms' => [
					'qualification' => $education
				]
			];
		}
		
		// Status filter
		if (!empty($app_int_status) && (int)$app_int_status > 0) {
			$boolQuery['bool']['filter'][] = [
				'term' => [
					'job_master_status_id' => (int)$app_int_status
				]
			];
		}
		
		// Notice period filter
		if (!empty($app_int_notice) && (int)$app_int_notice > 1) {
			$noticeFilter = $this->getNoticePeriodFilter($app_int_notice, $current_date);
			if ($noticeFilter) {
				$boolQuery['bool']['filter'][] = $noticeFilter;
			}
		}
		
		// Activity filter
		if (!empty($app_int_activity)) {
			$activityFilter = $this->getActivityFilter(
				$app_int_activity,
				$current_previous_30_days,
				$current_previous_60_days,
				$current_previous_90_days,
				$current_previous_180_days,
				$current_previous_1_year,
				$current_date
			);
			if ($activityFilter) {
				$boolQuery['bool']['filter'][] = $activityFilter;
			}
		}
		
		// Skills filter — must(mandatory)+should(optional) hybrid, mirroring
		// similar_existing_data_ajax()/internal_bench_data_ajax(): starred
		// skills are a hard requirement (must), unstarred entered skills stay
		// additive. APPENDED to $boolQuery['bool']['filter'][] — the previous
		// version of this block REASSIGNED $boolQuery entirely in the
		// mandatory-skill branch, silently discarding the must_not company_id
		// (this tab excludes the requesting company's own candidates) and
		// every other filter (status/education/location/range) already built
		// above the instant any skill was starred.
		if (!empty($mandatorySkills)) {
			$mustSkills = [];
			foreach ($mandatorySkills as $skill) {
				$mustSkills[] = GeneralMethod::buildSkillMatchClause($skill);
			}

			$skillBool = ['must' => $mustSkills];

			$optionalSkills = array_values(array_diff($requiredSkills, $mandatorySkills));
			if (!empty($optionalSkills)) {
				$shouldSkills = [];
				foreach ($optionalSkills as $skill) {
					$shouldSkills[] = GeneralMethod::buildSkillMatchClause($skill);
				}
				// No minimum_should_match here on purpose — these are
				// preferred, not required.
				$skillBool['should'] = $shouldSkills;
			}

			$boolQuery['bool']['filter'][] = [
				'bool' => $skillBool
			];
		} elseif (!empty($requiredSkills)) {
			$should = [];

			foreach ($requiredSkills as $skill) {
				$should[] = GeneralMethod::buildSkillMatchClause($skill);
			}
			$boolQuery['bool']['filter'][] = [
							'bool' => [
								'should' => $should,
								'minimum_should_match' => 1
							]
			];
		}

		if ($valCheck==22) {
			$today = date('Y-m-d');  
			$next30 = date('Y-m-d', strtotime('+30 days'));

			// Serving candidates filter
			$servingCandidateFilter = [
				'script' => [
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) return false;

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) return false;

							def v = raw.trim();

							if (v.equals('Immediate')) return false; // exclude 'Immediate'

							int comma = v.indexOf(',');
							if (comma > -1) v = v.substring(0, comma).trim();

							int t = v.indexOf('T');
							if (t > -1) v = v.substring(0, t);

							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								long epoch = d.toEpochDay();
								long today = java.time.LocalDate.parse(params.today).toEpochDay();
								long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

								return epoch >= today && epoch <= next30;
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									long epoch = d.toEpochDay();
									long today = java.time.LocalDate.parse(params.today).toEpochDay();
									long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

									return epoch >= today && epoch <= next30;
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										long epoch = d.toEpochDay();
										long today = java.time.LocalDate.parse(params.today).toEpochDay();
										long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

										return epoch >= today && epoch <= next30;
									} catch (Exception e3) {
										return false; // not a valid date
									}
								}
							}
						",
						'params' => [
							'today'  => $today,
							'next30' => $next30
						]
					]
				]
			];
		    // Attach serving candidate condition along with others
            $boolQuery['bool']['filter'][] = $servingCandidateFilter;
		}


		// $existingCandidatesQuery = DB::table('add_job_wise_condidates')
		// 	->where('job_id', '=', $jobid)->pluck('job_application_id')->toArray();
			
		// 	if (!empty($existingCandidatesQuery)) {
		// 		$boolQuery['bool']['must_not'][] = [
		// 			'terms' => [
		// 				'id' => $existingCandidatesQuery
		// 			]
		// 		];
		// }


		// Build the final query
		$params = [
			'index' => ELASTIC_CANDIDATES_BENCH,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => $sort,
				'query' => $boolQuery,
				'collapse' => [
					'field' => 'email'
				]
			]
		];

		if(!$this->elasticService->indexExists(ELASTIC_CANDIDATES_BENCH)) {
				$total = 0;
				$data = [];
		}else{
				$response = $this->elasticService->getClient()->search($params);
				// This total reflects ALL matching docs BEFORE collapse
				$totalBeforeCollapse = $response['hits']['total']['value'] ?? 0;

				// Count of current page records (after pagination & collapse)
				$records = $response['hits']['hits'] ?? [];
				$returnedCount = count($records);

				// Optional: if you want real total of unique candidate_id (after collapse), use an aggregation
				$aggParams = [
					'index' => ELASTIC_CANDIDATES_BENCH,
					'body' => [
						'size' => 0,
						'query' => $boolQuery,
						'aggs' => [
							'unique_candidates' => [
								'cardinality' => [
									'field' => 'email'
								]
							]
						]
					]
				];
				$aggResponse = $this->elasticService->getClient()->search($aggParams);
				$total = $aggResponse['aggregations']['unique_candidates']['value'] ?? $returnedCount;
		
				$data = array_map(function ($hit) use ($requiredSkills, $mandatorySkills) {

				$source = $hit['_source'];
				$jobSkills = collect(data_get($source, 'job_application_skills', []))
				->pluck('skills')
				->filter()
				->unique()
				->toArray();

					$matchedSkills = GeneralMethod::matchedSkillsList($jobSkills, $requiredSkills);
					$matchCount = count($matchedSkills);
					// Which matches were against a STARRED/mandatory requirement
					// vs just an entered-but-not-starred one — feeds the 3-tier
					// skill chip rendering in public_bench_ajax.blade.php.
					$matchedMandatorySkills = GeneralMethod::matchedSkillsList($jobSkills, $mandatorySkills);
					return array_merge($source, [
						'matched_skill_count' => $matchCount,
						'matched_skills' => $matchedSkills,
						'matched_mandatory_skills' => $matchedMandatorySkills
					]);
				}, $response['hits']['hits']);
		}
			
		$pagination = [
			'total' => $total,
			'current_page' => $page,
			'last_page' =>ceil($total / $size),
			'listType' => 'allcandidate',
		];
		$boostbenchApplications = [];
		$userPermissions = $this->userPermissions;
		$app_int_activity = request('app_int_activity');
		$app_int_status = request('app_int_status');
		$app_int_notice = request('app_int_notice');
		$sal_mode = request('sal_mode');
		$min_salary_currency = request('min_salary_currency');
		$app_int_outsource = request('app_int_outsource');
		$responseData = [
			'html' => view('service_company.ajax_pages.public_bench_ajax', compact('data', 'userPermissions', 'app_int_status', 'app_int_activity', 'app_int_notice', 'sal_mode', 'min_salary_currency', 'app_int_outsource','jobid','boostbenchApplications','requiredSkills','modeFilter','currencyFilter','symbolsFilter'))->render(),
			'total'=>$total,
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		];



		cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_PUBLIC_BENCH_TIME));

	    return response()->json($responseData);


		//echo $benchApplications =	$benchApplications->toSql();



	}

	public function dsimilar_candidates($id)
	{
		$id = GeneralMethod::value_decrypt($id);
		$lock_unlock = GeneralMethod::checksubscription();
		if ($lock_unlock == false) {
			return redirect()->route('service_company.dashboard')->with('message_paid', 'This is a paid featute');
			;
		}

		$company_id = auth()->user()->company_id;
		$this->pageTitle = __('Candidates Recommended');
		$company_id = auth()->user()->company_id;

		$this->job = DB::table('jobs')
			->select('jobs.id', 'jobs.title', 'jobs.min_exp', 'jobs.max_exp', 'jobs.min_salary', 'jobs.max_salary', 'jobs.min_sal_mode', 'jobs.max_sal_mode', 'jobs.min_salary_currency', 'jobs.max_salary_currency', 'jobs.total_positions', 'jobs.status', 'recommendation_relation.final_status', 'jobs.client_id', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city')
			->leftJoin('recommendation_relation', function ($join) use ($company_id) {
				$join->on('recommendation_relation.job_id', '=', 'jobs.id');
				$join->on('recommendation_relation.to_company_id', '=', DB::raw("'" . $company_id . "'"));
				//$join->on('recommendation_relation.final_status', '=', DB::raw("'1'"));
			})->join('clients', 'clients.id', 'jobs.client_id')
			->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
			->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
			->where(function ($query) use ($company_id, $id) {
				return
					$query->where('recommendation_relation.final_status', '=', 1)
						->where('recommendation_relation.to_company_id', '=', $company_id)
						->where('jobs.id', '=', $id);
			})
			->orWhere(function ($query) use ($company_id, $id) {
				return
					$query->where('recommendation_relation.final_status', '=', 2)
						->where('recommendation_relation.to_company_id', '=', $company_id)
						->where('jobs.id', '=', $id);
			})
			->first();

		$this->dinternal_count = $this->dinternal_count($id, $company_id);
		$return_data = GeneralMethod::custome_count_all($id);
		$this->pool_counter = $return_data->pool_client_counter;
		$this->existing_counter = $return_data->existing_counter;
		$this->internal_bench_counter = $return_data->internal_bench_counter;		
		$this->career_counter = $return_data->career_counter;;

		$jobid = $id;
		// ES-only shared source (see GeneralMethod::jobDefaultsFromEs()) —
		// same one similar_candidates()/internal_bench()/etc. and
		// custome_count_all() (the header counter on every tab) use, so
		// this page's defaults can't drift out of sync with the header
		// count the way gdFilterData()/JobcitiesTags() (MySQL) vs
		// custome_count_all() (ES) used to.
		$jobDefaults = GeneralMethod::jobDefaultsFromEs($id);

		$this->default_loc = $jobDefaults->default_loc;
		$this->qualification = '';//GeneralMethod::allQualificationSelected($jobid);
		$this->min_exp = ($jobDefaults->min_exp >= 1) ? $jobDefaults->min_exp - 1 : $jobDefaults->min_exp;
		$this->max_exp = $jobDefaults->max_exp + 1;
		$this->min_salary_currency = $jobDefaults->min_salary_currency;
		// Every AppTracker filter drawer defaults Min Salary to 0, not the
		// job's own min_salary (see custome_count_all()'s salary-floor
		// comments) — the header counter and this page have to agree on
		// the same floor or their candidate counts disagree on load.
		$this->min_salary = 0;
		$this->max_salary = $jobDefaults->max_salary;
		$this->min_sal_mode = $jobDefaults->min_sal_mode;
		$this->default_skill = $jobDefaults->default_skill;


		$skill_arr = array();
		$skill_data = DB::table('job_metronics_skills')->select('*')->where('job_id', $jobid)->get()->toArray();
		foreach ($skill_data as $sval) {
			$skill_arr[] = $sval->skill_name;
		}
		// $this->default_skill = implode(",",$skill_arr);

		$this->skill_data = $skill_data;
		$this->js_array = 'whitelist: ["' . implode('", "', $skill_arr) . '"]';

		$this->cities = GeneralMethod::allCities();
		$this->qualificationTag = DB::table('qualification_master')
			->select('*')
			->whereNotIn('id',[60])
			->orderBy('qualification_master.display_order', 'ASC')
			->get()
			->map(function ($row) {
				return $row->name;
			});



		// $this->jobclient = Client::findOrFail($this->job->client_id);



		return view('service_company.apptracker.dsimilar_candidates', $this->data);
	}

	public function dsimilar_existing_data_ajax(Request $request)
	{

		abort_if(Gate::denies('view_job_applications'), 403);
		$company_id = auth()->user()->company_id;
		$job_status = GeneralMethod::job_status();
		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();
		$requiredSkills = [];
		$searchTerms = request('cus_search');
		$custom_search = json_decode($searchTerms, true);

		$jobid = $request->jobid;
		$job = DB::table('jobs')
			->select('jobs.id', 'jobs.title', 'jobs.min_exp', 'jobs.max_exp', 'jobs.min_salary', 'jobs.max_salary','jobs.max_sal_mode','jobs.max_salary_currency')
			->where('id', $jobid)
			->first();


		if (\request('cus_search') != "") {
			$skillsAddElastic = array_column($custom_search, 'value');
			GeneralMethod::skillsAddElastic($skillsAddElastic);
		}

		if (\request('cus_search') != "") {

			$checkedSkills = array_filter($custom_search, function ($skill) {
				return $skill['checked'] == true;
			});

			$uncheckedSkills = array_filter($custom_search, function ($skill) {
				return $skill['checked'] == false;
			});

			if (!empty($checkedSkills)) {
				$requiredSkills = array_column($checkedSkills, 'value');
			} else {
				$requiredSkills = array_column($uncheckedSkills, 'value');
			}

		}

		$location = request('kt_tagify_location');
		$custom_location = json_decode($location, true);

		$education = request('kt_tagify_education');
		$custom_education = json_decode($education, true);


		$current_date = date('Y-m-d H:i:s');
		$current_timestamp = strtotime($current_date);
		$current_previous_30_days = date('Y-m-d H:i:s', strtotime('-30 days', $current_timestamp));
		$current_previous_60_days = date('Y-m-d H:i:s', strtotime('-60 days', $current_timestamp));
		$current_previous_90_days = date('Y-m-d H:i:s', strtotime('-90 days', $current_timestamp));
		$current_previous_180_days = date('Y-m-d H:i:s', strtotime('-180 days', $current_timestamp));
		$current_previous_1_year = date('Y-m-d H:i:s', strtotime('-365 days', $current_timestamp));
		$app_int_activity = request('app_int_activity');
		$app_int_status = request('app_int_status');
		$app_int_notice = request('app_int_notice');
		$sal_mode = request('sal_mode') ?? $job->max_sal_mode;
		$min_salary_currency = request('min_salary_currency') ?? $job->max_salary_currency;

         


		

		$title = $job->title;

		
		$symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
		$symbolsValue = !empty($symbolsValue) ? $symbolsValue : [
			0	=>	"INR",
			1	=>	"EUR",
			2	=>	"JPY",
			3	=>	"GBP",
			4	=>	"USD",
			5	=>	"AED"
		];


		$modeFilter = $sal_mode ?? $job->max_sal_mode;
		$currencyFilter = $min_salary_currency ?? $job->max_salary_currency;
		$symbolsFilter = $symbolsValue[$currencyFilter] ?? "INR";


		if (\request('min_exp') != "" || \request('max_exp') != "") {
			$min_exp = request('min_exp');
			$max_exp = request('max_exp');
		} else {
			$min_exp = '';//$job->min_exp;
			$max_exp = '';//$job->max_exp;
		}

		$currency = GeneralMethod::currency();  // Currency code
		$currencyCheck = $currency[$currencyFilter];

		if (\request('min_sal') != "" || \request('max_sal') != "") {
			$AmountMin = request('min_sal');
			$AmountMax = request('max_sal');
			$min_salary = $AmountMin;
			$max_salary = $AmountMax;
			if ($currencyFilter > 0) {
				$min_salary = GeneralMethod::convertToINR($AmountMin, $currencyCheck);
				$max_salary = GeneralMethod::convertToINR($AmountMax, $currencyCheck);
			}
			// $ctcdividedMin = GeneralMethod::salaryDividedByMode($sal_mode, $AmountMin);
			// $ctcdividedMax = GeneralMethod::salaryDividedByMode($sal_mode, $AmountMax);
			// if ($sal_mode == 0) {
			// 	$min_salary = $ctcdividedMin->yearly;
			// 	$max_salary = $ctcdividedMax->yearly;

			// } elseif ($sal_mode == 1) {
			// 	$min_salary = $ctcdividedMin->monthly;
			// 	$max_salary = $ctcdividedMax->monthly;

			// } elseif ($sal_mode == 2) {
			// 	$min_salary = $ctcdividedMin->hourly;
			// 	$max_salary = $ctcdividedMax->hourly;

			// }

		} else {

			$convertedAmount = request('max_sal');//$job->max_salary;
			$max_salary = $convertedAmount;
			$min_salary = 0;//$job->min_salary;
			if ($request->min_salary_currency > 0) {
				$max_salary = GeneralMethod::convertToINR($convertedAmount, $currencyCheck);
			}
			// $ectcdivided = GeneralMethod::salaryDividedByMode($sal_mode, $convertedAmount);
			// // $max_salary = $job->max_salary;	

			// if ($sal_mode == 0) {
			// 	$max_salary = $ectcdivided->yearly;

			// } elseif ($sal_mode == 1) {
			// 	$max_salary = $ectcdivided->monthly;

			// } elseif ($sal_mode == 2) {
			// 	$max_salary = $ectcdivided->hourly;

			// }

			// dd("dkd");

		}

		$location = array();
		if (\request('kt_tagify_location') != "") {
			foreach ($custom_location as $sval) {
				$parts = explode('|', $sval['value']);
				// Trim to remove any leading or trailing whitespace
				$cityName = trim($parts[0]);
				$loc_data = DB::table('city_list')->where('city_list.city', '=', $cityName)->first();
				if ($loc_data) {
					array_push($location, $loc_data->city_id);
				}

			}
		}
		$education = array();
		if (\request('kt_tagify_education') != "") {
			foreach ($custom_education as $sval) {
				$education_data = DB::table('qualification_master')->where('qualification_master.name', '=', $sval['value'])->first();
				if ($education_data) {
					$educationfiletr = GeneralMethod::qualificationFilterCondition($education_data->id);
					$education = array_merge($education, $educationfiletr);

				}
			}
		}


		$valCheck = $request->input('sortingfilter');
		$country = '';
		$searchQuery = $request->input('q');
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;
        // dd(array_values($requiredSkills));
		$requiredSkills = array_map('strtolower', $requiredSkills);
		// dd($requiredSkills);

		$cachePayload = [
			'company' => $company_id,
			'page' => $page,
			'size' => $size,
			'q' => $searchQuery,
			'jobid' => $jobid,
			'cus_search' => $requiredSkills ?? [],
			'sortingfilter' => $valCheck,
			'min_exp' => $min_exp,
			'max_exp' => $max_exp,
			'min_salary_currency' => $min_salary_currency,
			'education' => $education ?? [],
			'location' => $location ?? [],
			'min_sal' => $min_salary,
			'max_sal' => $max_salary,
			'app_int_status'=>$app_int_status,
			'app_int_activity'=>$app_int_activity,
			'app_int_notice'=>$app_int_notice,
			'user_id' => $this->user->id,
			'sal_mode' =>$sal_mode
		];

		// dd($cachePayload );	
		
		$versionKey = 'alldsimilarcandidates_version_user_'.$this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:alldsimilarcandidates:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
		/* ----------------------------
		⚡ Check DragonflyDB first
		----------------------------- */
		if (cache()->has($cacheKeySet)) {
			return response()->json(cache()->get($cacheKeySet));
		}




		$sort[] = [
			'_script' => [
				'type' => 'number',
				'script' => [
					'lang' => 'painless',
					'source' => '
						int matchCount = 0;
						if (doc.containsKey("job_application_skills.skills.keyword")) {
							for (s in params.skills) {
								if (doc["job_application_skills.skills.keyword"].contains(s)) {
									matchCount += 1;
								}
							}
						}
						return matchCount;
					',
					'params' => [
						'skills' => array_values($requiredSkills)
					]
				],
				'order' => 'desc'
			]
		];
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
			case 5: // Notice period ASC (ignore Immediate)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return 999999; // Put missing/Immediate at the end
								}
								try {
									// Extract first number from comma-separated values
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {
									// If parsing fails
								}
								return 999999; // Invalid values at the end
							"
						],
						'order' => 'asc'
					]
				];
				break;
			case 6: // Notice period DESC (ignore Immediate)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return -1; // Put missing/Immediate at the end
								}
								try {
									// Extract first number from comma-separated values
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {
									// If parsing fails
								}
								return -1; // Invalid values at the end
							"
						],
						'order' => 'desc'
					]
				];
				break;
			case 21: // Notice period Date ASC
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return 999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return 999999999; }
							def v = raw.trim();

							// Immediate should come first
							if (v.equals('Immediate')) { return -999999998; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return 999999999; // not a date -> last
									}
								}
							}
						"
					],
					'order' => 'asc'
				]
			];
			break;

			case 22: // Notice period Date DESC
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return -999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return -999999999; }
							def v = raw.trim();

							// Immediate should come after valid dates (but before null/non-date)
							if (v.equals('Immediate')) { return -888888888; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return -999999999; // not a date -> last
									}
								}
							}
						"
					],
					'order' => 'desc'
				]
			];
			break;
			case 7:
				$sort[] = ['total_exp' => ['order' => 'desc', 'unmapped_type' => 'float']];
				break;
			case 8:
				$sort[] = ['total_exp' => ['order' => 'asc', 'unmapped_type' => 'float']];
				break;
			case 9: // Immediate joiners first (original functionality)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "params['_source']['immediate_join'] == 'Immediate' ? 0 : 1"
						],
						'order' => 'asc'
					]
				];
				break;
			default:
				$sort[] = ['id' => ['order' => 'desc', 'unmapped_type' => 'long']];
				break;
		}

		if ($valCheck==22) {
			$today = date('Y-m-d');  
			$next30 = date('Y-m-d', strtotime('+30 days'));

			// Serving candidates filter
			$servingCandidateFilter = [
				'script' => [
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) return false;

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) return false;

							def v = raw.trim();

							if (v.equals('Immediate')) return false; // exclude 'Immediate'

							int comma = v.indexOf(',');
							if (comma > -1) v = v.substring(0, comma).trim();

							int t = v.indexOf('T');
							if (t > -1) v = v.substring(0, t);

							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								long epoch = d.toEpochDay();
								long today = java.time.LocalDate.parse(params.today).toEpochDay();
								long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

								return epoch >= today && epoch <= next30;
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									long epoch = d.toEpochDay();
									long today = java.time.LocalDate.parse(params.today).toEpochDay();
									long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

									return epoch >= today && epoch <= next30;
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										long epoch = d.toEpochDay();
										long today = java.time.LocalDate.parse(params.today).toEpochDay();
										long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

										return epoch >= today && epoch <= next30;
									} catch (Exception e3) {
										return false; // not a valid date
									}
								}
							}
						",
						'params' => [
							'today'  => $today,
							'next30' => $next30
						]
					]
				]
			];
		    // Attach serving candidate condition along with others
            $boolQuery['bool']['filter'][] = $servingCandidateFilter;
		}

		$mustSubmittedBy = [];

		$custom_search = "";
    
		// dd($custom_search);

		// $skillsList = array_filter($requiredSkills, fn($s) => $s !== 'skill');

		$boolQuery = [
			'bool' => [
				'should' => [
					// First condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['company_id' => $company_id]],
								['term' => ['condidate_type' => 'other']],
								['term' => ['ClientComID' => 0]],
							], $this->getSearchConditions($custom_search, $country))
						]
					],
					// Second condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['company_id' => $company_id]],
								['term' => ['condidate_type' => 'other']],
								['range' => ['ClientComID' => ['gt' => 0]]],
								['range' => ['job_id' => ['gt' => 0]]],
							], [
								['bool' => [
									'must_not' => [
										['terms' => ['job_master_status_id' => [8,9]]]
									]
								]]
							], $this->getSearchConditions($custom_search, $country))
						]
					],
					// Third condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['ClientComID' => $company_id]],
								['term' => ['condidate_type' => 'other']],
								['range' => ['job_id' => ['gt' => 0]]],
							], $this->getSearchConditions($custom_search, $country))
						]
					],
				],
				'minimum_should_match' => 1,
				'filter' => [], // We'll add all filters here
				'must_not' => [
					['term' => ['job_master_status_id' => 5]],
					['term' => ['job_master_status_id' => 6]]
				]
			]
		];

		// Experience filter

			$range = [];

			// Minimum experience
			if ($min_exp !== '' && $min_exp !== null) {
				$range['gte'] = (float) $min_exp;
			}

			// Maximum experience
			if ($max_exp !== '' && $max_exp !== null) {
				$range['lte'] = (float) $max_exp;
			}

			// Add filter query
		if (!empty($range)) {
			$boolQuery['bool']['filter'][] = [
				'range' => [
					'total_exp' => $range
				]
			];
		}
		// if (!empty($min_exp) && !empty($max_exp)) {
		// 	$boolQuery['bool']['filter'][] = [
		// 		'range' => [
		// 			'total_exp' => [
		// 				'gte' => (float)$min_exp,
		// 				'lte' => (float)$max_exp
		// 			]
		// 		]
		// 	];
		// }
		
		// Salary filter

		$salaryField = match($sal_mode) {
				0 => 'ectc_yearly',
				1 => 'ectc_monthly',
				2 => 'ectc_hourly',
				default => 'ectc_yearly'
		};

		if (($min_salary !== null && $min_salary !== '' && $min_salary != 0.00) || ($max_salary !== null && $max_salary !== '' && $max_salary != 0.00)) {

			$salaryRange = [];

			// Minimum salary
			if ($min_salary !== null && $min_salary !== '' && $min_salary != 0.00) {
				$salaryRange['gte'] = (float) $min_salary;
			}

			// Maximum salary
			if ($max_salary !== null && $max_salary !== '' && $max_salary != 0.00) {
				$salaryRange['lte'] = (float) $max_salary;
			}

			$boolQuery['bool']['filter'][] = [
				'range' => [
					$salaryField => $salaryRange
				]
			];
		}
		// if (!empty($max_salary)) {
		// 	// Normalize inputs
        //     $min = is_numeric($min_salary) ? (float) $min_salary : 0.0;
        //     $max = is_numeric($max_salary) ? (float) $max_salary : null;
            
        //     if($min >0 && $max > 0){
                
                
    	// 		$salaryField = 'ectc_yearly'; // default
    	// 		switch ($sal_mode) {
    	// 			case 2: $salaryField = 'ectc_hourly'; break;
    	// 			case 1: $salaryField = 'ectc_monthly'; break;
    	// 		}
    			
    	// 		$boolQuery['bool']['filter'][] = [
    	// 			'range' => [
    	// 				$salaryField => [
    	// 					'gte' => (float)$min_salary,
    	// 					'lte' => (float)$max_salary
    	// 				]
    	// 			]
    	// 		];
			
                
        //     }else{
            
        //         // Decide salary field
        //         $salaryField = 'ectc_yearly'; // default
        //         switch ($sal_mode) {
        //             case 2:
        //                 $salaryField = 'ectc_hourly';
        //                 break;
        //             case 1:
        //                 $salaryField = 'ectc_monthly';
        //                 break;
        //         }
                
        //         // Build salary filter
        //         $salaryFilter = [
        //             'bool' => [
        //                 'should' => [
        //                     // 1️⃣ Salary >= min (covers numeric values)
        //                     [
        //                         'range' => [
        //                             $salaryField => [
        //                                 'gte' => $min
        //                             ]
        //                         ]
        //                     ],
                
        //                     // 2️⃣ Salary IS NULL (field missing)
        //                     [
        //                         'bool' => [
        //                             'must_not' => [
        //                                 ['exists' => ['field' => $salaryField]]
        //                             ]
        //                         ]
        //                     ]
        //                 ],
        //                 'minimum_should_match' => 1
        //             ]
        //         ];
                
        //         // Add max salary only if provided and > 0
        //         if ($max !== null && $max > 0) {
        //             $salaryFilter['bool']['should'][0]['range'][$salaryField]['lte'] = $max;
        //         }
                
        //         // Attach filter to main query
        //         $boolQuery['bool']['filter'][] = $salaryFilter;
        //     }
		// }

		// dd($min_exp,$max_exp,$min_salary,$max_salary,$sal_mode,$location,$education);
		
		// Location filter
		if (!empty($location)) {
			$boolQuery['bool']['filter'][] = [
				'terms' => [
					'city_id' => $location
				]
			];
		}
		
		// Education filter
		if (!empty($education)) {
			$boolQuery['bool']['filter'][] = [
				'terms' => [
					'qualification' => $education
				]
			];
		}
		
		// Status filter
		if (!empty($app_int_status) && (int)$app_int_status > 0) {
			$boolQuery['bool']['filter'][] = [
				'term' => [
					'job_master_status_id' => (int)$app_int_status
				]
			];
		}
		
		// Notice period filter
		if (!empty($app_int_notice) && (int)$app_int_notice > 1) {
			$noticeFilter = $this->getNoticePeriodFilter($app_int_notice, $current_date);
			if ($noticeFilter) {
				$boolQuery['bool']['filter'][] = $noticeFilter;
			}
		}
		
		// Activity filter
		if (!empty($app_int_activity)) {
			$activityFilter = $this->getActivityFilter(
				$app_int_activity,
				$current_previous_30_days,
				$current_previous_60_days,
				$current_previous_90_days,
				$current_previous_180_days,
				$current_previous_1_year,
				$current_date
			);
			if ($activityFilter) {
				$boolQuery['bool']['filter'][] = $activityFilter;
			}
		}
		
		// Skills filter

		if (!empty($checkedSkills)) {
			if (!empty($requiredSkills)) {
				$must = [];

				foreach ($requiredSkills as $skill) {
					$must[] = [
						'match_phrase' => [
							'job_application_skills.skills' => $skill
						]
					];
				}

				$boolQuery['bool']['filter'][] = [
								'bool' => [
									'must' => $must
								]
				];
			}
		}else{
			if (!empty($requiredSkills)) {
				$should = [];

				foreach ($requiredSkills as $skill) {
					$should[] = [
						'match_phrase' => [
							'job_application_skills.skills' => $skill
						]
					];
				}
				$boolQuery['bool']['filter'][] = [
								'bool' => [
									'should' => $should,
									'minimum_should_match' => 1
								]
				];
			} 
		}

		$existingCandidatesQuery = DB::table('add_job_wise_condidates')
			->where('job_id', '=', $jobid)->pluck('job_application_id')->toArray();

			if (!empty($existingCandidatesQuery)) {
				$boolQuery['bool']['must_not'][] = [
					'terms' => [
						'candidate_id' => $existingCandidatesQuery
					]
				];
		}
		$boolQuery['bool']['must_not'][] = [
			'terms' => [
				'job_master_status_id' => [5, 6]
			]
		];



		// Build the final query
		$params = [
			'index' => ELASTIC_CANDIDATES,
			'request_cache' => true,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => $sort,
				'query' => $boolQuery,
				'collapse' => [
					'field' => 'email'
				]
			]
		];

		// dd($params);

			if (!$this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
				$total = 0;
				$data = [];
			}else{
				$response = $this->elasticService->getClient()->search($params);
				// This total reflects ALL matching docs BEFORE collapse
				$totalBeforeCollapse = $response['hits']['total']['value'] ?? 0;

				// Count of current page records (after pagination & collapse)
				$records = $response['hits']['hits'] ?? [];
				$returnedCount = count($records);

				// Optional: if you want real total of unique candidate_id (after collapse), use an aggregation
				$aggParams = [
					'index' => ELASTIC_CANDIDATES,
					'body' => [
						'size' => 0,
						'query' => $boolQuery,
						'aggs' => [
							'unique_candidates' => [
								'cardinality' => [
									'field' => 'email'
								]
							]
						]
					]
				];
				$aggResponse = $this->elasticService->getClient()->search($aggParams);
				$total = $aggResponse['aggregations']['unique_candidates']['value'] ?? $returnedCount;
		
				$data = array_map(function ($hit) use ($requiredSkills) {
					
				$source = $hit['_source'];
				$jobSkills = collect(data_get($source, 'job_application_skills', []))
				->pluck('skills')
				->filter()
				->unique()
				->toArray();

					$matchedSkills = GeneralMethod::matchedSkillsList($jobSkills, $requiredSkills);
				    $matchCount = count($matchedSkills);
					return array_merge($source, [
						'matched_skill_count' => $matchCount,
						'matched_skills' => $matchedSkills
					]);
				}, $response['hits']['hits']);
			}
			
			$pagination = [
				'total' => $total,
				'current_page' => $page,
				'last_page' =>ceil($total / $size),
				'listType' => 'allcandidate',
			];

			$userPermissions = $this->userPermissions;
			$app_int_activity = request('app_int_activity');
			$app_int_status = request('app_int_status');
			$app_int_notice = request('app_int_notice');
			$sal_mode = request('sal_mode');
			$min_salary_currency = request('min_salary_currency');
			$app_int_outsource = request('app_int_outsource');
			$responseData =  [
				'html' => view('service_company.ajax_pages.dsimilar_existing_ajax', compact('data','userPermissions','app_int_status','app_int_activity','app_int_notice','sal_mode','min_salary_currency','app_int_outsource','jobid','modeFilter','currencyFilter','symbolsFilter'))->render(),
				'total'=>$total,
				'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
			];


			cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_D_SIMILAR_CANDIDATES_TIME));


	    return response()->json($responseData);



	}
	public function dinternal_bench($id)
	{
		$id = GeneralMethod::value_decrypt($id);
		$lock_unlock = GeneralMethod::checksubscription();
		if ($lock_unlock == false) {
			return redirect()->route('service_company.dashboard')->with('message_paid', 'This is a paid featute');
			;
		}

		$this->pageTitle = __('Candidates Recommended');
		$company_id = auth()->user()->company_id;

		$this->job = DB::table('jobs')
			->select('jobs.id', 'jobs.title', 'jobs.min_exp', 'jobs.max_exp', 'jobs.min_salary', 'jobs.max_salary', 'jobs.min_sal_mode', 'jobs.max_sal_mode', 'jobs.min_salary_currency', 'jobs.max_salary_currency', 'jobs.total_positions', 'jobs.status', 'recommendation_relation.final_status', 'jobs.client_id', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city')
			->leftJoin('recommendation_relation', function ($join) use ($company_id) {
				$join->on('recommendation_relation.job_id', '=', 'jobs.id');
				$join->on('recommendation_relation.to_company_id', '=', DB::raw("'" . $company_id . "'"));
				//$join->on('recommendation_relation.final_status', '=', DB::raw("'1'"));
			})->join('clients', 'clients.id', 'jobs.client_id')
			->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
			->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
			->where(function ($query) use ($company_id, $id) {
				return
					$query->where('recommendation_relation.final_status', '=', 1)
						->where('recommendation_relation.to_company_id', '=', $company_id)
						->where('jobs.id', '=', $id);
			})
			->orWhere(function ($query) use ($company_id, $id) {
				return
					$query->where('recommendation_relation.final_status', '=', 2)
						->where('recommendation_relation.to_company_id', '=', $company_id)
						->where('jobs.id', '=', $id);
			})
			->first();

		$this->dinternal_count = $this->dinternal_count($id, $company_id);
		$return_data = GeneralMethod::custome_count_all($id);
		$this->pool_counter = $return_data->pool_client_counter;
		$this->existing_counter = $return_data->existing_counter;
		$this->internal_bench_counter = $return_data->internal_bench_counter;		
		$this->career_counter = $return_data->career_counter;;

		// ES-only shared source (see GeneralMethod::jobDefaultsFromEs()) —
		// same one similar_candidates()/internal_bench()/etc. and
		// custome_count_all() (the header counter on every tab) use, so
		// this page's defaults can't drift out of sync with the header
		// count the way gdFilterData()/JobcitiesTags() (MySQL) vs
		// custome_count_all() (ES) used to.
		$jobDefaults = GeneralMethod::jobDefaultsFromEs($id);
		$jobid = $id;

		$this->default_loc = $jobDefaults->default_loc;
		$this->qualification = '';//GeneralMethod::allQualificationSelected($jobid);
		$this->min_exp = ($jobDefaults->min_exp >= 1) ? $jobDefaults->min_exp - 1 : $jobDefaults->min_exp;
		$this->max_exp = $jobDefaults->max_exp + 1;
		$this->min_salary_currency = $jobDefaults->min_salary_currency;
		// Every AppTracker filter drawer defaults Min Salary to 0, not the
		// job's own min_salary (see custome_count_all()'s salary-floor
		// comments) — the header counter and this page have to agree on
		// the same floor or their candidate counts disagree on load.
		$this->min_salary = 0;
		$this->max_salary = $jobDefaults->max_salary;
		$this->min_sal_mode = $jobDefaults->min_sal_mode;
		$this->default_skill = $jobDefaults->default_skill;
		// $this->jobclient = Client::findOrFail($this->job->client_id);

		$skill_arr = array();
		$skill_data = DB::table('job_metronics_skills')->select('*')->where('job_id', $jobid)->get()->toArray();
		foreach ($skill_data as $sval) {
			$skill_arr[] = $sval->skill_name;
		}
		// $this->default_skill = implode(",",$skill_arr);

		$this->skill_data = $skill_data;
		$this->js_array = 'whitelist: ["' . implode('", "', $skill_arr) . '"]';

		$this->cities = GeneralMethod::allCities();
		$this->qualificationTag = DB::table('qualification_master')
			->select('*')
			->whereNotIn('id',[60])
			->orderBy('qualification_master.display_order', 'ASC')
			->get()
			->map(function ($row) {
				return $row->name;
			});


		return view('service_company.apptracker.dinternal_bench', $this->data);
	}
	public function dinternal_bench_data_ajax(Request $request)
	{

		$company_id = auth()->user()->company_id;
		$jobid = $request->jobid;
		$job = DB::table('jobs')
			->select('jobs.id', 'jobs.title', 'jobs.min_exp', 'jobs.max_exp', 'jobs.min_salary', 'jobs.max_salary','jobs.max_sal_mode','jobs.max_salary_currency')
			->where('jobs.id', $jobid)
			->first();

		$title = $job->title;
		if (\request('min_exp') != "" || \request('max_exp') != "") {
			$min_exp = request('min_exp');
			$max_exp = request('max_exp');
		} else {
			$min_exp = '';//$job->min_exp;
			$max_exp = '';//$job->max_exp;
		}
		$location = request('kt_tagify_location');
		$custom_location = json_decode($location, true);

		$education = request('kt_tagify_education');
		$custom_education = json_decode($education, true);

		$current_date = date('Y-m-d H:i:s');
		$current_timestamp = strtotime($current_date);
		$current_previous_30_days = date('Y-m-d H:i:s', strtotime('-30 days', $current_timestamp));
		$current_previous_60_days = date('Y-m-d H:i:s', strtotime('-60 days', $current_timestamp));
		$current_previous_90_days = date('Y-m-d H:i:s', strtotime('-90 days', $current_timestamp));
		$current_previous_180_days = date('Y-m-d H:i:s', strtotime('-180 days', $current_timestamp));
		$current_previous_1_year = date('Y-m-d H:i:s', strtotime('-365 days', $current_timestamp));
		$app_int_activity = request('app_int_activity');
		$app_int_status = request('app_int_status');
		$app_int_notice = request('app_int_notice');
		$sal_mode = request('sal_mode') ?? $job->max_sal_mode;
		$min_salary_currency = request('min_salary_currency') ?? $job->max_salary_currency;
		$app_int_outsource = request('app_int_outsource');

		
		$symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
		$symbolsValue = !empty($symbolsValue) ? $symbolsValue : [
			0	=>	"INR",
			1	=>	"EUR",
			2	=>	"JPY",
			3	=>	"GBP",
			4	=>	"USD",
			5	=>	"AED"
		];

		$modeFilter = $sal_mode ?? $job->max_sal_mode;
		$currencyFilter = $min_salary_currency ?? $job->max_salary_currency;
		$symbolsFilter = $symbolsValue[$currencyFilter] ?? "INR";


		$currency = GeneralMethod::currency();  // Currency code
		$currencyCheck = $currency[$currencyFilter];

		if (\request('min_sal') != "" || \request('max_sal') != "") {
			$AmountMin = request('min_sal');
			$AmountMax = request('max_sal');
			$min_salary = $AmountMin;
			$max_salary = $AmountMax;
			if ($request->min_salary_currency > 0) {
				$min_salary = GeneralMethod::convertToINR($AmountMin, $currencyCheck);
				$max_salary = GeneralMethod::convertToINR($AmountMax, $currencyCheck);
			}
			// $ctcdividedMin = GeneralMethod::salaryDividedByMode($sal_mode, $AmountMin);
			// $ctcdividedMax = GeneralMethod::salaryDividedByMode($sal_mode, $AmountMax);
			// if ($sal_mode == 0) {
			// 	$min_salary = $ctcdividedMin->yearly;
			// 	$max_salary = $ctcdividedMax->yearly;

			// } elseif ($sal_mode == 1) {
			// 	$min_salary = $ctcdividedMin->monthly;
			// 	$max_salary = $ctcdividedMax->monthly;

			// } elseif ($sal_mode == 2) {
			// 	$min_salary = $ctcdividedMin->hourly;
			// 	$max_salary = $ctcdividedMax->hourly;

			// }

		} else {

			$convertedAmount = request('max_sal');//$job->max_salary;
			$max_salary = $convertedAmount;
			$min_salary = 0;//$job->min_salary;
			if ($request->min_salary_currency > 0) {
				$max_salary = GeneralMethod::convertToINR($convertedAmount, $currencyCheck);
			}
			// $ectcdivided = GeneralMethod::salaryDividedByMode($sal_mode, $convertedAmount);
			// // $max_salary = $job->max_salary;	

			// if ($sal_mode == 0) {
			// 	$max_salary = $ectcdivided->yearly;

			// } elseif ($sal_mode == 1) {
			// 	$max_salary = $ectcdivided->monthly;

			// } elseif ($sal_mode == 2) {
			// 	$max_salary = $ectcdivided->hourly;

			// }

			// dd("dkd");

		}

		$loc_data = DB::table('client_location_jobs')
			->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
			->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
			->select(
				'client_location.address',
				'client_location.city',
				'client_location.postcode',
				'client_location.country_id',
				'city_list.city as city_name'
			)
			->where('client_location_jobs.job_id', $jobid)
			->first();

		$postcode = @$loc_data->postcode;


		$skill_arr = array();
		$skill_data = DB::table('job_metronics_skills')->select('*')->where('job_id', $jobid)->get()->toArray();
		foreach ($skill_data as $sval) {
			$skill_arr[] = $sval->skill_name;
		}
		$company_arr = array($company_id);

		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();
        $requiredSkills = [];
		$searchTerms = request('cus_search');
		$custom_search = json_decode($searchTerms, true);
		if (\request('cus_search') != "") {
			$skillsAddElastic = array_column($custom_search, 'value');
			GeneralMethod::skillsAddElastic($skillsAddElastic);
		}

		if (\request('cus_search') != "") {

			$checkedSkills = array_filter($custom_search, function ($skill) {
				return $skill['checked'] == true;
			});

			$uncheckedSkills = array_filter($custom_search, function ($skill) {
				return $skill['checked'] == false;
			});

			if (!empty($checkedSkills)) {
				$requiredSkills = array_column($checkedSkills, 'value');
			} else {
				$requiredSkills = array_column($uncheckedSkills, 'value');
			}

		}


		$chkAdminRole = $this->user->hasRole('admin');

		// $chkAdminRole = DB::table('roles')
		// 	->select('*')
		// 	->where('id', $this->user->role->role_id)
		// 	->where('name', 'admin')
		// 	->count();


		$location = array();
		if (\request('kt_tagify_location') != "") {
			foreach ($custom_location as $sval) {
				$parts = explode('|', $sval['value']);
				// Trim to remove any leading or trailing whitespace
				$cityName = trim($parts[0]);
				$loc_data = DB::table('city_list')->where('city_list.city', '=', $cityName)->first();
				if ($loc_data) {
					array_push($location, $loc_data->city_id);
				}

			}
		}
		$education = array();
		if (\request('kt_tagify_education') != "") {
			foreach ($custom_education as $sval) {
				$education_data = DB::table('qualification_master')->where('qualification_master.name', '=', $sval['value'])->first();
				if ($education_data) {
					$educationfiletr = GeneralMethod::qualificationFilterCondition($education_data->id);
					$education = array_merge($education, $educationfiletr);

				}
			}
		}

		
		$valCheck = $request->input('sortingfilter');
		$country = '';
		$searchQuery = $request->input('q');
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;
		$requiredSkills = array_map('strtolower', $requiredSkills);

		$cachePayload = [
			'company' => $company_id,
			'page' => $page,
			'size' => $size,
			'q' => $searchQuery,
			'jobid' => $jobid,
			'cus_search' => $requiredSkills ?? [],
			'sortingfilter' => $valCheck,
			'min_exp' => $min_exp,
			'max_exp' => $max_exp,
			'min_salary_currency' => $min_salary_currency,
			'education' => $education ?? [],
			'location' => $location ?? [],
			'min_sal' => $min_salary,
			'max_sal' => $max_salary,
			'app_int_status'=>$app_int_status,
			'app_int_activity'=>$app_int_activity,
			'app_int_notice'=>$app_int_notice,
			'app_int_outsource'=>$app_int_outsource,
			'user_id' => $this->user->id,
			'sal_mode' =>$sal_mode
		];

		// dd($cachePayload );	
		
		$versionKey = 'alldinternalbench_version_user_'.$this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:alldinternalbench:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
		/* ----------------------------
		⚡ Check DragonflyDB first
		----------------------------- */
		if (cache()->has($cacheKeySet)) {
			return response()->json(cache()->get($cacheKeySet));
		}
         
		$sort[] = [
			'_script' => [
				'type' => 'number',
				'script' => [
					'lang' => 'painless',
					'source' => '
						int matchCount = 0;
						if (doc.containsKey("job_application_skills.skills.keyword")) {
							for (s in params.skills) {
								if (doc["job_application_skills.skills.keyword"].contains(s)) {
									matchCount += 1;
								}
							}
						}
						return matchCount;
					',
					'params' => [
						'skills' => array_values($requiredSkills)
					]
				],
				'order' => 'desc'
			]
		];
		switch ($valCheck) {
			case 1:
				$sort[] = ['updated_at' => ['order' => 'desc']];
				break;
			case 2:
				$sort[] = ['updated_at' => ['order' => 'asc']];
				break;
			case 3:
				$sort[] = ['ctc' => ['order' => 'asc', 'unmapped_type' => 'float']];
				break;
			case 4:
				$sort[] = ['ctc' => ['order' => 'desc', 'unmapped_type' => 'float']];
				break;
			case 5: // Notice period ASC (ignore Immediate)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return 999999; // Put missing/Immediate at the end
								}
								try {
									// Extract first number from comma-separated values
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {
									// If parsing fails
								}
								return 999999; // Invalid values at the end
							"
						],
						'order' => 'asc'
					]
				];
				break;
			case 6: // Notice period DESC (ignore Immediate)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return -1; // Put missing/Immediate at the end
								}
								try {
									// Extract first number from comma-separated values
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {
									// If parsing fails
								}
								return -1; // Invalid values at the end
							"
						],
						'order' => 'desc'
					]
				];
				break;
			case 21: // Notice period Date ASC
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return 999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return 999999999; }
							def v = raw.trim();

							// Immediate should come first
							if (v.equals('Immediate')) { return -999999998; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return 999999999; // not a date -> last
									}
								}
							}
						"
					],
					'order' => 'asc'
				]
			];
			break;

			case 22: // Notice period Date DESC
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return -999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return -999999999; }
							def v = raw.trim();

							// Immediate should come after valid dates (but before null/non-date)
							if (v.equals('Immediate')) { return -888888888; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return -999999999; // not a date -> last
									}
								}
							}
						"
					],
					'order' => 'desc'
				]
			];
			break;
			case 7:
				$sort[] = ['total_exp' => ['order' => 'desc', 'unmapped_type' => 'float']];
				break;
			case 8:
				$sort[] = ['total_exp' => ['order' => 'asc', 'unmapped_type' => 'float']];
				break;
			case 9: // Immediate joiners first (original functionality)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "params['_source']['immediate_join'] == 'Immediate' ? 0 : 1"
						],
						'order' => 'asc'
					]
				];
				break;
			default:
				$sort[] = ['id' => ['order' => 'desc', 'unmapped_type' => 'long']];
				break;
		}

		if ($valCheck==22) {
			$today = date('Y-m-d');  
			$next30 = date('Y-m-d', strtotime('+30 days'));

			// Serving candidates filter
			$servingCandidateFilter = [
				'script' => [
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) return false;

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) return false;

							def v = raw.trim();

							if (v.equals('Immediate')) return false; // exclude 'Immediate'

							int comma = v.indexOf(',');
							if (comma > -1) v = v.substring(0, comma).trim();

							int t = v.indexOf('T');
							if (t > -1) v = v.substring(0, t);

							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								long epoch = d.toEpochDay();
								long today = java.time.LocalDate.parse(params.today).toEpochDay();
								long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

								return epoch >= today && epoch <= next30;
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									long epoch = d.toEpochDay();
									long today = java.time.LocalDate.parse(params.today).toEpochDay();
									long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

									return epoch >= today && epoch <= next30;
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										long epoch = d.toEpochDay();
										long today = java.time.LocalDate.parse(params.today).toEpochDay();
										long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

										return epoch >= today && epoch <= next30;
									} catch (Exception e3) {
										return false; // not a valid date
									}
								}
							}
						",
						'params' => [
							'today'  => $today,
							'next30' => $next30
						]
					]
				]
			];
		    // Attach serving candidate condition along with others
            $boolQuery['bool']['filter'][] = $servingCandidateFilter;
		}

		$boolQuery = [
			'bool' => [
				'must' => [
					// First condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['company_id' => $company_id]],
								['term' => ['condidate_type' => 'bench_condidate']],
								['term' => ['status' => 2]]
							])
						]
					]
				],
				'filter' => [], // We'll add all filters here
				'must_not' => []
			]
		];
       
		// Experience filter
	

			$range = [];

			// Minimum experience
			if ($min_exp !== '' && $min_exp !== null) {
				$range['gte'] = (float) $min_exp;
			}

			// Maximum experience
			if ($max_exp !== '' && $max_exp !== null) {
				$range['lte'] = (float) $max_exp;
			}

			// Add filter query
		if (!empty($range)) {
			$boolQuery['bool']['filter'][] = [
				'range' => [
					'total_exp' => $range
				]
			];
		}
		// if (!empty($max_exp)) {
		// 	$boolQuery['bool']['filter'][] = [
		// 		'range' => [
		// 			'total_exp' => [
		// 				'gte' => (float)$min_exp,
		// 				'lte' => (float)$max_exp
		// 			]
		// 		]
		// 	];
		// }
		
		// Salary filter

		$salaryField = match($sal_mode) {
				0 => 'ectc_yearly',
				1 => 'ectc_monthly',
				2 => 'ectc_hourly',
				default => 'ectc_yearly'
		};

		if (($min_salary !== null && $min_salary !== '' && $min_salary != 0.00) || ($max_salary !== null && $max_salary !== '' && $max_salary != 0.00)) {

			$salaryRange = [];

			// Minimum salary
			if ($min_salary !== null && $min_salary !== '' && $min_salary != 0.00) {
				$salaryRange['gte'] = (float) $min_salary;
			}

			// Maximum salary
			if ($max_salary !== null && $max_salary !== '' && $max_salary != 0.00) {
				$salaryRange['lte'] = (float) $max_salary;
			}

			$boolQuery['bool']['filter'][] = [
				'range' => [
					$salaryField => $salaryRange
				]
			];
		}
		// if (!empty($max_salary)) {
		// 	// Normalize inputs
        //     $min = is_numeric($min_salary) ? (float) $min_salary : 0.0;
        //     $max = is_numeric($max_salary) ? (float) $max_salary : null;
            
        //     if($min >0 && $max > 0){
                
                
    	// 		$salaryField = 'ectc_yearly'; // default
    	// 		switch ($sal_mode) {
    	// 			case 2: $salaryField = 'ectc_hourly'; break;
    	// 			case 1: $salaryField = 'ectc_monthly'; break;
    	// 		}
    			
    	// 		$boolQuery['bool']['filter'][] = [
    	// 			'range' => [
    	// 				$salaryField => [
    	// 					'gte' => (float)$min_salary,
    	// 					'lte' => (float)$max_salary
    	// 				]
    	// 			]
    	// 		];
			
                
        //     }else{
            
        //         // Decide salary field
        //         $salaryField = 'ectc_yearly'; // default
        //         switch ($sal_mode) {
        //             case 2:
        //                 $salaryField = 'ectc_hourly';
        //                 break;
        //             case 1:
        //                 $salaryField = 'ectc_monthly';
        //                 break;
        //         }
                
        //         // Build salary filter
        //         $salaryFilter = [
        //             'bool' => [
        //                 'should' => [
        //                     // 1️⃣ Salary >= min (covers numeric values)
        //                     [
        //                         'range' => [
        //                             $salaryField => [
        //                                 'gte' => $min
        //                             ]
        //                         ]
        //                     ],
                
        //                     // 2️⃣ Salary IS NULL (field missing)
        //                     [
        //                         'bool' => [
        //                             'must_not' => [
        //                                 ['exists' => ['field' => $salaryField]]
        //                             ]
        //                         ]
        //                     ]
        //                 ],
        //                 'minimum_should_match' => 1
        //             ]
        //         ];
                
        //         // Add max salary only if provided and > 0
        //         if ($max !== null && $max > 0) {
        //             $salaryFilter['bool']['should'][0]['range'][$salaryField]['lte'] = $max;
        //         }
                
        //         // Attach filter to main query
        //         $boolQuery['bool']['filter'][] = $salaryFilter;
        //     }
		// }
		
		// Location filter
		if (!empty($location)) {
			$boolQuery['bool']['filter'][] = [
				'terms' => [
					'city_id' => $location
				]
			];
		}


		if ((int) $app_int_outsource > 0) {
			$boolQuery['bool']['filter'][] = [
				'term' => [
					'outsourcing_type' => $app_int_outsource
				]
			];
		}

		
		// Education filter
		if (!empty($education)) {
			$boolQuery['bool']['filter'][] = [
				'terms' => [
					'qualification' => $education
				]
			];
		}
		
		// Status filter
		if (!empty($app_int_status) && (int)$app_int_status > 0) {
			$boolQuery['bool']['filter'][] = [
				'term' => [
					'job_master_status_id' => (int)$app_int_status
				]
			];
		}
		
		// Notice period filter
		if (!empty($app_int_notice) && (int)$app_int_notice > 1) {
			$noticeFilter = $this->getNoticePeriodFilter($app_int_notice, $current_date);
			if ($noticeFilter) {
				$boolQuery['bool']['filter'][] = $noticeFilter;
			}
		}
		
		// Activity filter
		if (!empty($app_int_activity)) {
			$activityFilter = $this->getActivityFilter(
				$app_int_activity,
				$current_previous_30_days,
				$current_previous_60_days,
				$current_previous_90_days,
				$current_previous_180_days,
				$current_previous_1_year,
				$current_date
			);
			if ($activityFilter) {
				$boolQuery['bool']['filter'][] = $activityFilter;
			}
		}
		
		// Skills filter

		if (!empty($checkedSkills)) {
			if (!empty($requiredSkills)) {
				$must = [];

				foreach ($requiredSkills as $skill) {
					$must[] = [
						'match_phrase' => [
							'job_application_skills.skills' => $skill
						]
					];
				}

				$boolQuery['bool']['filter'][] = [
								'bool' => [
									'must' => $must
								]
				];
			}
		}else{
			if (!empty($requiredSkills)) {

				$should = [];

				foreach ($requiredSkills as $skill) {
					$should[] = [
						'match_phrase' => [
							'job_application_skills.skills' => $skill
						]
					];
				}
				$boolQuery['bool']['filter'][] = [
								'bool' => [
									'should' => $should,
									'minimum_should_match' => 1
								]
				];
			
			} 
		}

		

		// $existingCandidatesQuery = DB::table('add_job_wise_condidates')
		// 	->where('job_id', '=', $jobid)->pluck('job_application_id')->toArray();

		// 	if (!empty($existingCandidatesQuery)) {
		// 		$boolQuery['bool']['must_not'][] = [
		// 			'terms' => [
		// 				'id' => $existingCandidatesQuery
		// 			]
		// 		];
		// }



		// Build the final query
		$params = [
			'index' => ELASTIC_CANDIDATES_BENCH,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => $sort,
				'query' => $boolQuery,
				'collapse' => [
					'field' => 'email'
				]
			]
		];

		if(!$this->elasticService->indexExists(ELASTIC_CANDIDATES_BENCH)) {
				$total = 0;
				$data = [];
		}else{
				$response = $this->elasticService->getClient()->search($params);
				// This total reflects ALL matching docs BEFORE collapse
				
				$totalBeforeCollapse = $response['hits']['total']['value'] ?? 0;

				// Count of current page records (after pagination & collapse)
				$records = $response['hits']['hits'] ?? [];
				$returnedCount = count($records);

				// Optional: if you want real total of unique candidate_id (after collapse), use an aggregation
				$aggParams = [
					'index' => ELASTIC_CANDIDATES_BENCH,
					'body' => [
						'size' => 0,
						'query' => $boolQuery,
						'aggs' => [
							'unique_candidates' => [
								'cardinality' => [
									'field' => 'email'
								]
							]
						]
					]
				];
				$aggResponse = $this->elasticService->getClient()->search($aggParams);
				$total = $aggResponse['aggregations']['unique_candidates']['value'] ?? $returnedCount;
		
				$data = array_map(function ($hit) use ($requiredSkills) {
					
				$source = $hit['_source'];
				$jobSkills = collect(data_get($source, 'job_application_skills', []))
				->pluck('skills')
				->filter()
				->unique()
				->toArray();

					$matchedSkills = GeneralMethod::matchedSkillsList($jobSkills, $requiredSkills);
					$matchCount = count($matchedSkills);
					return array_merge($source, [
						'matched_skill_count' => $matchCount,
						'matched_skills' => $matchedSkills
					]);
				}, $response['hits']['hits']);
		}
			
			$pagination = [
				'total' => $total,
				'current_page' => $page,
				'last_page' =>ceil($total / $size),
				'listType' => 'allcandidate',
			];

            $userPermissions = $this->userPermissions;
			$app_int_activity = request('app_int_activity');
			$app_int_status = request('app_int_status');
			$app_int_notice = request('app_int_notice');
			$sal_mode = request('sal_mode');
			$min_salary_currency = request('min_salary_currency');
			$app_int_outsource = request('app_int_outsource');
			
			$responseData = [
				'html' => view('service_company.ajax_pages.dinternal_bench_ajax', compact('data','userPermissions','app_int_status','app_int_activity','app_int_notice','sal_mode','min_salary_currency','app_int_outsource','jobid','modeFilter','currencyFilter','symbolsFilter'))->render(),
				'total'=>$total,
				'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
			];


			
		cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_D_INTERNAL_BENCH_TIME));

		return response()->json($responseData);

	}
	public function benchReqViewMore($id, $job_applications_id)
	{
		abort_if(Gate::denies('view_jobs'), 403);
		// dd($id, $job_applications_id);
		$this->salaryMode = GeneralMethod::salaryMode();
		$this->employmentType = GeneralMethod::employmentType();
		$this->job = DB::table('jobs')->Select(
			'jobs.id',
			'jobs.title',
			'jobs.slug',
			'jobs.company_id',
			'jobs.job_description',
			'jobs.job_requirement',
			'jobs.total_positions',
			'jobs.location_id',
			'jobs.category_id',
			'jobs.start_date',
			'jobs.end_date',
			'jobs.status',
			'jobs.required_columns',
			'jobs.meta_details',
			'jobs.section_visibility',
			'jobs.created_at',
			'jobs.updated_at',
			'jobs.priority',
			'jobs.client_id',
			'jobs.min_exp',
			'jobs.max_exp',
			'jobs.min_salary',
			'jobs.max_salary',
			'jobs.qualification',
			'jobs.employment_type',
			'jobs.min_salary_currency',
			'jobs.max_salary_currency',
			'jobs.min_sal_mode',
			'jobs.max_sal_mode',
			'jobs.carrier_website',
			'clients.name as client_name',
			'clients.description as client_description',
			'clients.logo_logical_path',
			'job_categories.name as cat_name',
			'qualification_master.name as qualification_name'
		)
			->join('clients', 'clients.id', '=', 'jobs.client_id')
			->leftJoin('job_categories', 'job_categories.id', '=', 'jobs.category_id')
			->leftJoin('qualification_master', 'qualification_master.id', '=', 'jobs.qualification')
			->where('jobs.id', $id)
			->first();
		$this->skillArr = DB::table('job_metronics_skills')->select('*')->where('job_id', $this->job->id)->get();
		$company_arr = DB::table('companies')->select('*')->where('id', $this->job->company_id)->first();
		$this->company_name = @$company_arr->company_name;
		$this->category_name = 'N/A';
		$this->jobLocation = '';
		if ($this->job->id > 0) {

		}
		// UPDATE TIMELINE
		$this->updateTimeLine = DB::table('updatetimeline');
		$this->updateTimeLine = $this->updateTimeLine->select(
			'updatetimeline.id',
			'updatetimeline.event_id',
			'updatetimeline.event_by_company_id',
			'updatetimeline.event_by_user_id',
			'updatetimeline.master_process_name',
			'updatetimeline.child_process_name',
			'updatetimeline.application_id',
			'updatetimeline.application_email',
			'updatetimeline.job_id',
			'updatetimeline.note',
			'updatetimeline.create_at',
			'updatetimeline.update_at',
			'updatetimeline.prev_process_name',
			'updatetimeline.note',
			'updatetimeline.prev_process_child_name',
			'updatetimeline.interview_schedule_date',
			'updatetimeline.interview_mode',
			'updatetimeline.interview_mode_child_level',
			'updatetimeline.interviewers',
			'updatetimeline.average_star_rating',
			'updatetimeline.interview_message',
			'updatetimeline.interview_schedules_id',
			'updatetimeline.offer_currency',
			'updatetimeline.offer_mode',
			'updatetimeline.offer_amount',
			'updatetimeline.joined_date',
			'application_event.event_name',
			'jobs.id as job_id',
			'jobs.title',
			'users.name',
			'users.image',
			'clients.name as client_name',
			'interview_schedules.available_status',
			'interview_schedules.reschedule',
			'interview_schedules.feedback'
		);
		$this->updateTimeLine = $this->updateTimeLine->join('application_event', 'application_event.id', '=', 'updatetimeline.event_id');
		$this->updateTimeLine = $this->updateTimeLine->join('job_applications', 'job_applications.id', '=', 'updatetimeline.application_id');
		$this->updateTimeLine = $this->updateTimeLine->leftjoin('interview_schedules', 'interview_schedules.id', 'updatetimeline.interview_schedules_id');
		$this->updateTimeLine = $this->updateTimeLine->join('users', 'users.id', '=', 'updatetimeline.event_by_user_id');
		$this->updateTimeLine = $this->updateTimeLine->join('jobs', 'jobs.id', '=', 'updatetimeline.job_id');
		$this->updateTimeLine = $this->updateTimeLine->join('clients', 'clients.id', '=', 'jobs.client_id');
		$this->updateTimeLine = $this->updateTimeLine->where('updatetimeline.application_id', '=', $job_applications_id);
		$this->updateTimeLine = $this->updateTimeLine->where('updatetimeline.job_id', '=', $id);
		$this->updateTimeLine = $this->updateTimeLine->orderBy('updatetimeline.id', 'DESC');
		$this->updateTimeLine = $this->updateTimeLine->get();

		$view = view('service_company.apptracker.benchReqViewMore', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'view' => $view]);
	}
	public function ticketList(Request $request)
	{
		$search = $request->input('search');
		$this->pageTitle = __('Applied Candidates');
		$this->pageIcon = 'icon-user';
		$this->tickets = Ticket::with('ticketstatus')
			->where('tickets.company_id', '=', auth()->user()->company_id)
			->when($search, function ($query, $search) {
				return $query->where('title', 'LIKE', "%{$search}%")
					->orWhere('ticket_no', 'LIKE', "%{$search}%");
			})
			->orderBy('status_id', 'ASC') // Order by status_id
			->paginate(21);


		return view('service_company.supports.index', $this->data);

		// return view('service_company.apptracker.index', $this->data);
	}
	public function ticketCreate()
	{

		$number = Ticket::latest('id')->value('id');
		$this->number_token = ($number > 0) ? $number + 1 : 1;
		$this->formattedNumber = str_pad($this->number_token, 5, '0', STR_PAD_LEFT);

		$this->pageTitle = __('Applied Candidates');
		$this->pageIcon = 'icon-user';
		return view('service_company.supports.create', $this->data);

		// return view('service_company.apptracker.index', $this->data);
	}
	public function storeticketCreate(Request $request)
	{
		$validated = $request->validate([
			'title' => 'required|string|max:255',
			'description' => 'required|string',
			'ticketId' => 'required|string',
			'ticketIdCheck' => 'required|string',
		]);

		$number_token = decrypt($validated['ticketId']);

		// dd($number);
		// $formattedNumber = sprintf("%05d", $number_token);
		$formattedNumber = str_pad($number_token, 5, '0', STR_PAD_LEFT);

		if ($formattedNumber === $validated['ticketIdCheck']) {
			$ticket = Ticket::create([
				'user_id' => auth()->user()->id,
				'title' => $validated['title'],
				'description' => $validated['description'],
				'ticket_no' => $formattedNumber,
				'company_id' => auth()->user()->company_id,
				'status_id' => 1, // Default status as 'Pending' or 'Open'
			]);
			return redirect()->route('service_company.tickets.ticketList')->with('success', 'Ticket created successfully.');
		}
		// $number
		// dd($formattedNumber);

		return redirect()->back()
			->withInput() // retains the input data
			->with('error', 'Ticket creation failed. Please try again.');


	}
	public function ticketshow($ticket)
	{

		$id = decrypt($ticket);
		$this->ticket = Ticket::where('id', $id)
			->with(['comments.user', 'ticketstatus', 'user'])
			->firstOrFail();

		// dd($ticket);


		// dd($ticket->all());
		$this->pageTitle = __('Applied Candidates');
		$this->pageIcon = 'icon-user';
		$this->ticket_status = TicketStatus::all();
		// $this->ticket = $ticket->load(['comments.user', 'status', 'user']);

		// dd($ticket);
		return view('service_company.supports.show', $this->data);
	}
	public function updateStatus(Request $request, Ticket $ticket)
	{
		$ticket->update(['status_id' => $request->status_id]);
		return redirect()->route('service_company.tickets.ticketList')->with('success', 'Ticket status updated.');
	}
	public function storetComment(Request $request)
	{
		// dd($request->all());
		$validated = $request->validate([
			'ticket_id' => 'required|exists:tickets,id',
			'content' => 'required|string|max:1000',
		]);
		$id = $validated['ticket_id'];
		Comment::create([
			'ticket_id' => $validated['ticket_id'],
			'user_id' => auth()->user()->id,
			'content' => $validated['content'],
		]);

		Ticket::where('id', $id)->update(['status_id' => $request->status_is]);


		return redirect()->route('service_company.tickets.show', encrypt($validated['ticket_id']))->with('success', 'Comment added successfully.');
	}
	public function findTalent()
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
		$this->min_exp = '0';
		$this->max_exp = '0';
		$this->min_salary_currency = '0';
		$this->min_salary = '0';
		$this->max_salary = '0';
		$this->min_sal_mode = '0';
		$this->default_skill = '';

		$this->cities = GeneralMethod::allCities();
		$this->qualificationTag = DB::table('qualification_master')
			->select('*')
			->whereNotIn('id',[60])
			->orderBy('qualification_master.display_order', 'ASC')
			->get()
			->map(function ($row) {
				return $row->name;
			});
		
		return view('service_company.apptracker.find-talent', $this->data);
	}
	public function findTalentData(Request $request)
	{

		$company_id = auth()->user()->company_id;
		$searchTerms = request('cus_search');
		$custom_search = json_decode($searchTerms, true);

		$location = request('kt_tagify_location');
		$custom_location = json_decode($location, true);

		$education = request('kt_tagify_education');
		$custom_education = json_decode($education, true);

		$company = request('kt_tagify_company');
		$custom_company = json_decode($company, true);
		$current_date = date('Y-m-d H:i:s');
		$current_timestamp = strtotime($current_date);
		$current_previous_30_days = date('Y-m-d H:i:s', strtotime('-30 days', $current_timestamp));
		$current_previous_60_days = date('Y-m-d H:i:s', strtotime('-60 days', $current_timestamp));
		$current_previous_90_days = date('Y-m-d H:i:s', strtotime('-90 days', $current_timestamp));
		$current_previous_180_days = date('Y-m-d H:i:s', strtotime('-180 days', $current_timestamp));
		$current_previous_1_year = date('Y-m-d H:i:s', strtotime('-365 days', $current_timestamp));
		$app_int_activity = request('app_int_activity');
		$app_int_status = request('app_int_status');
		$app_int_notice = request('app_int_notice');
		$sal_mode = request('sal_mode');
		$min_salary_currency = request('min_salary_currency');
		$app_int_outsource = request('app_int_outsource');
		$jobid = '';

		$title = "jobs";
		$min_exp ='';
		$max_exp = '';
		if (\request('min_exp') != "" || \request('max_exp') != "") {
			$min_exp = request('min_exp');
			$max_exp = request('max_exp');
		}
        
		$currency = GeneralMethod::currency();  // Currency code
		$currencyCheck = $currency[$request->min_salary_currency];
		$min_salary = '';
		$max_salary = '';
		if (\request('min_sal') != "" || \request('max_sal') != "") {
			$AmountMin = request('min_sal');
			$AmountMax = request('max_sal');
			if ($request->min_salary_currency > 0) {
				$AmountMin = GeneralMethod::convertToINR($AmountMin, $currencyCheck);
				$AmountMax = GeneralMethod::convertToINR($AmountMax, $currencyCheck);
			}
			$ctcdividedMin = GeneralMethod::salaryDividedByMode($sal_mode, $AmountMin);
			$ctcdividedMax = GeneralMethod::salaryDividedByMode($sal_mode, $AmountMax);
			if ($sal_mode == 0) {
				$min_salary = $ctcdividedMin->yearly;
				$max_salary = $ctcdividedMax->yearly;

			} elseif ($sal_mode == 1) {
				$min_salary = $ctcdividedMin->monthly;
				$max_salary = $ctcdividedMax->monthly;

			} elseif ($sal_mode == 2) {
				$min_salary = $ctcdividedMin->hourly;
				$max_salary = $ctcdividedMax->hourly;

			}

		} 

		$location = array();
		if (\request('kt_tagify_location') != "") {
			foreach ($custom_location as $sval) {
				$parts = explode('|', $sval['value']);
				// Trim to remove any leading or trailing whitespace
				$cityName = trim($parts[0]);
				$loc_data = DB::table('city_list')->where('city_list.city', '=', $cityName)->first();
				if ($loc_data) {
					array_push($location, $loc_data->city_id);
				}

			}
		}

		$education = array();
		if (\request('kt_tagify_education') != "") {
			foreach ($custom_education as $sval) {
				$education_data = DB::table('qualification_master')->where('qualification_master.name', '=', $sval['value'])->first();
				if ($education_data) {
					$educationfiletr = GeneralMethod::qualificationFilterCondition($education_data->id);
					$education = array_merge($education, $educationfiletr);
				}
			}
		}
        // dd("ji");
		

		$company_arr = array($company_id);
		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();

		$chkAdminRole = $this->user->hasRole('admin');

		// $chkAdminRole = DB::table('roles')
		// 	->select('*')
		// 	->where('id', $this->user->role->role_id)
		// 	->where('name', 'admin')
		// 	->count();

		$requiredSkills = [];
		if (\request('cus_search') != "") {
			$checkedSkills = array_filter($custom_search, function ($skill) {
				return $skill['checked'] == true;
			});
			$uncheckedSkills = array_filter($custom_search, function ($skill) {
				return $skill['checked'] == false;
			});
			if (!empty($checkedSkills)) {
				foreach ($checkedSkills as $skill) {
					$requiredSkills = array_column($checkedSkills, 'value');
				}
			} else {
				foreach ($uncheckedSkills as $skill) {
					$requiredSkills = array_column($uncheckedSkills, 'value');
				}
			}
		}

		$valCheck = $request->input('sortingfilter');
		$country = '';
		$searchQuery = $request->input('q');
		$page = max((int) $request->input('page', 1), 1);
		$size = (int) $request->input('size', 10); // Default 20 results per page
		$from = ($page - 1) * $size;
		$requiredSkills = array_map('strtolower', $requiredSkills);


		$cachePayload = [
			'page' => $page,
			'size' => $size,
			'q' => $searchQuery,
			'company' => $company_id,
			'requiredSkills' => $requiredSkills ?? [],
			'app_int_outsource' => $app_int_outsource,
			'valCheck' => $valCheck,
			'min_exp' => $min_exp,
			'max_exp' => $max_exp,
			'kt_tagify_location' => $location ?? [],
			'kt_tagify_education' => $education ?? [],
			'min_sal' =>$min_salary,
			'max_sal' => $max_salary,
			'min_salary_currency' => $min_salary_currency,
			'app_int_activity' => $app_int_activity,
			'sal_mode' => $sal_mode,
			'app_int_notice' => $app_int_notice,
			'user_id' => $this->user->id,
			'app_int_status' => $app_int_status
		];
		
		$versionKey = 'allfindtalents_version_user_'.$this->user->id;
		$version = cache()->get($versionKey, 1);
		$cacheKeySet = 'ats:allfindtalents:' . $this->user->id . ':' . $version . ':' . md5(json_encode($cachePayload));
		/* ----------------------------
		⚡ Check DragonflyDB first
		----------------------------- */
		if (cache()->has($cacheKeySet)) {
			return response()->json(cache()->get($cacheKeySet));
		}

		if($valCheck=='Show All'){
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('ads_switch') || doc['ads_switch'].size() == 0) {
								return 1;
							}
							return doc['ads_switch'].value.toLowerCase() == 'yes' ? 0 : 1;
						"
					],
					'order' => 'asc'
				]
			];

			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => '
							int matchCount = 0;
							if (doc.containsKey("job_application_skills.skills.keyword")) {
								for (s in params.skills) {
									if (doc["job_application_skills.skills.keyword"].contains(s)) {
										matchCount += 1;
									}
								}
							}
							return matchCount;
						',
						'params' => [
							'skills' => array_values($requiredSkills)
						]
					],
					'order' => 'desc'
				]
			];
		}
		switch ($valCheck) {
			case 1:
				$sort[] = ['updated_at' => ['order' => 'desc']];
				break;
			case 2:
				$sort[] = ['updated_at' => ['order' => 'asc']];
				break;
			case 3:
				$sort[] = ['bill_rate_amount' => ['order' => 'asc', 'unmapped_type' => 'float']];
				break;
			case 4:
				$sort[] = ['bill_rate_amount' => ['order' => 'desc', 'unmapped_type' => 'float']];
				break;
			case 5: // Notice period ASC (ignore Immediate)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return 999999; // Put missing/Immediate at the end
								}
								try {
									// Extract first number from comma-separated values
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {
									// If parsing fails
								}
								return 999999; // Invalid values at the end
							"
						],
						'order' => 'asc'
					]
				];
				break;
			case 6: // Notice period DESC (ignore Immediate)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return -1; // Put missing/Immediate at the end
								}
								try {
									// Extract first number from comma-separated values
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {
									// If parsing fails
								}
								return -1; // Invalid values at the end
							"
						],
						'order' => 'desc'
					]
				];
				break;
			case 21: // Notice period Date ASC
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return 999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return 999999999; }
							def v = raw.trim();

							// Immediate should come first
							if (v.equals('Immediate')) { return -999999998; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return 999999999; // not a date -> last
									}
								}
							}
						"
					],
					'order' => 'asc'
				]
			];
			break;

			case 22: // Notice period Date DESC
			$sort[] = [
				'_script' => [
					'type' => 'number',
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return -999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return -999999999; }
							def v = raw.trim();

							// Immediate should come after valid dates (but before null/non-date)
							if (v.equals('Immediate')) { return -888888888; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return -999999999; // not a date -> last
									}
								}
							}
						"
					],
					'order' => 'desc'
				]
			];
			break;
			case 7:
				$sort[] = ['total_exp' => ['order' => 'desc', 'unmapped_type' => 'float']];
				break;
			case 8:
				$sort[] = ['total_exp' => ['order' => 'asc', 'unmapped_type' => 'float']];
				break;
			case 9: // Immediate joiners first (original functionality)
				$sort[] = [
					'_script' => [
						'type' => 'number',
						'script' => [
							'lang' => 'painless',
							'source' => "params['_source']['immediate_join'] == 'Immediate' ? 0 : 1"
						],
						'order' => 'asc'
					]
				];
				break;
			default:
				$sort[] = ['id' => ['order' => 'desc', 'unmapped_type' => 'long']];
				break;
		}

		$boolQuery = [
			'bool' => [
				'must' => [
					// First condition
					[
						'bool' => [
							'must' => array_merge([
								['term' => ['condidate_type' => 'bench_condidate']],
								['term' => ['status' => 2]]
							])
						]
					]
				],
				'filter' => [], // We'll add all filters here
				'must_not' => [
					['term' => ['company_id' => $company_id]]
				]
			]
		];

		// Experience filter
		if (!empty($max_exp)) {
			$boolQuery['bool']['filter'][] = [
				'range' => [
					'total_exp' => [
						'gte' => (float)$min_exp,
						'lte' => (float)$max_exp
					]
				]
			];
		}
		
		// Salary filter
		if (!empty($max_salary)) {
			// Normalize inputs
            $min = is_numeric($min_salary) ? (float) $min_salary : 0.0;
            $max = is_numeric($max_salary) ? (float) $max_salary : null;
            
            if($min >0 && $max > 0){
                
                
    			$salaryField = 'ectc_yearly'; // default
    			switch ($sal_mode) {
    				case 2: $salaryField = 'ectc_hourly'; break;
    				case 1: $salaryField = 'ectc_monthly'; break;
    			}
    			
    			$boolQuery['bool']['filter'][] = [
    				'range' => [
    					$salaryField => [
    						'gte' => (float)$min_salary,
    						'lte' => (float)$max_salary
    					]
    				]
    			];
			
                
            }else{
            
                // Decide salary field
                $salaryField = 'ectc_yearly'; // default
                switch ($sal_mode) {
                    case 2:
                        $salaryField = 'ectc_hourly';
                        break;
                    case 1:
                        $salaryField = 'ectc_monthly';
                        break;
                }
                
                // Build salary filter
                $salaryFilter = [
                    'bool' => [
                        'should' => [
                            // 1️⃣ Salary >= min (covers numeric values)
                            [
                                'range' => [
                                    $salaryField => [
                                        'gte' => $min
                                    ]
                                ]
                            ],
                
                            // 2️⃣ Salary IS NULL (field missing)
                            [
                                'bool' => [
                                    'must_not' => [
                                        ['exists' => ['field' => $salaryField]]
                                    ]
                                ]
                            ]
                        ],
                        'minimum_should_match' => 1
                    ]
                ];
                
                // Add max salary only if provided and > 0
                if ($max !== null && $max > 0) {
                    $salaryFilter['bool']['should'][0]['range'][$salaryField]['lte'] = $max;
                }
                
                // Attach filter to main query
                $boolQuery['bool']['filter'][] = $salaryFilter;
            }
		}
		
		// Location filter
		if (!empty($location)) {
			$boolQuery['bool']['filter'][] = [
				'terms' => [
					'city_id' => $location
				]
			];
		}

		if ((int) $app_int_outsource > 0) {
			$boolQuery['bool']['filter'][] = [
				'term' => [
					'outsourcing_type' => $app_int_outsource
				]
			];
		}

		// Education filter
		if (!empty($education)) {
			$boolQuery['bool']['filter'][] = [
				'terms' => [
					'qualification' => $education
				]
			];
		}
		
		// Status filter
		if (!empty($app_int_status) && (int)$app_int_status > 0) {
			$boolQuery['bool']['filter'][] = [
				'term' => [
					'job_master_status_id' => (int)$app_int_status
				]
			];
		}
		
		// Notice period filter
		if (!empty($app_int_notice) && (int)$app_int_notice > 1) {
			$noticeFilter = $this->getNoticePeriodFilter($app_int_notice, $current_date);
			if ($noticeFilter) {
				$boolQuery['bool']['filter'][] = $noticeFilter;
			}
		}
		
		// Activity filter
		if (!empty($app_int_activity)) {
			$activityFilter = $this->getActivityFilter(
				$app_int_activity,
				$current_previous_30_days,
				$current_previous_60_days,
				$current_previous_90_days,
				$current_previous_180_days,
				$current_previous_1_year,
				$current_date
			);
			if ($activityFilter) {
				$boolQuery['bool']['filter'][] = $activityFilter;
			}
		}
		
		// Skills filter
		if (!empty($requiredSkills)) {
			$boolQuery['bool']['filter'][] = [
				'bool' => [
					'should' => [
						['terms' => ['job_application_skills.skills' => $requiredSkills]]
					],
					'minimum_should_match' => 1
				]
			];
		}

		if ($valCheck==22) {
			$today = date('Y-m-d');  
			$next30 = date('Y-m-d', strtotime('+30 days'));

			// Serving candidates filter
			$servingCandidateFilter = [
				'script' => [
					'script' => [
						'lang' => 'painless',
						'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) return false;

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) return false;

							def v = raw.trim();

							if (v.equals('Immediate')) return false; // exclude 'Immediate'

							int comma = v.indexOf(',');
							if (comma > -1) v = v.substring(0, comma).trim();

							int t = v.indexOf('T');
							if (t > -1) v = v.substring(0, t);

							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								long epoch = d.toEpochDay();
								long today = java.time.LocalDate.parse(params.today).toEpochDay();
								long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

								return epoch >= today && epoch <= next30;
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									long epoch = d.toEpochDay();
									long today = java.time.LocalDate.parse(params.today).toEpochDay();
									long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

									return epoch >= today && epoch <= next30;
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										long epoch = d.toEpochDay();
										long today = java.time.LocalDate.parse(params.today).toEpochDay();
										long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

										return epoch >= today && epoch <= next30;
									} catch (Exception e3) {
										return false; // not a valid date
									}
								}
							}
						",
						'params' => [
							'today'  => $today,
							'next30' => $next30
						]
					]
				]
			];
		    // Attach serving candidate condition along with others
            $boolQuery['bool']['filter'][] = $servingCandidateFilter;
		}

		// Build the final query
		$params = [
			'index' => ELASTIC_CANDIDATES_BENCH,
			'body' => [
				'from' => $from,
				'size' => $size,
				'sort' => $sort,
				'track_total_hits' => true,
				'query' => $boolQuery,
				'collapse' => [
					'field' => 'email',
					'inner_hits' => [
						'name' => 'latest',
						'size' => 1,
						'sort' => $sort
					]
				]
			]
		];

		if(!$this->elasticService->indexExists(ELASTIC_CANDIDATES_BENCH)) {
				$total = 0;
				$data = [];
		}else{
				$response = $this->elasticService->getClient()->search($params);
				// This total reflects ALL matching docs BEFORE collapse
				$totalBeforeCollapse = $response['hits']['total']['value'] ?? 0;

				// Count of current page records (after pagination & collapse)
				$records = $response['hits']['hits'] ?? [];
				$returnedCount = count($records);

				// Optional: if you want real total of unique candidate_id (after collapse), use an aggregation
				$aggParams = [
					'index' => ELASTIC_CANDIDATES_BENCH,
					'body' => [
						'size' => 0,
						'query' => $boolQuery,
						'aggs' => [
							'unique_candidates' => [
								'cardinality' => [
									'field' => 'email'
								]
							]
						]
					]
				];
				$aggResponse = $this->elasticService->getClient()->search($aggParams);
				$total = $aggResponse['aggregations']['unique_candidates']['value'] ?? $returnedCount;
		
				$data = array_map(function ($hit) use ($requiredSkills) {
					
				$source = $hit['_source'];
				$jobSkills = collect(data_get($source, 'job_application_skills', []))
				->pluck('skills')
				->filter()
				->unique()
				->toArray();

					$matchedSkills = GeneralMethod::matchedSkillsList($jobSkills, $requiredSkills);
					$matchCount = count($matchedSkills);
					return array_merge($source, [
						'matched_skill_count' => $matchCount,
						'matched_skills' => $matchedSkills
					]);
				}, $response['hits']['hits']);
		}
			
		$pagination = [
			'total' => $total,
			'current_page' => $page,
			'last_page' =>ceil($total / $size),
			'listType' => 'allcandidate',
		];

		$userPermissions = $this->userPermissions;
		$app_int_activity = request('app_int_activity');
		$app_int_status = request('app_int_status');
		$app_int_notice = request('app_int_notice');
		$sal_mode = request('sal_mode');
		$min_salary_currency = request('min_salary_currency');
		$app_int_outsource = request('app_int_outsource');
		
		$responseData = [
			'html' => view('service_company.ajax_pages.find_talent_ajax', compact('data', 'userPermissions', 'app_int_status', 'app_int_activity', 'app_int_notice', 'sal_mode', 'min_salary_currency', 'app_int_outsource','jobid'))->render(),
			'total'=>$total,
			'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
		];

		
		cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_FIND_TALENTS_TIME));

	    return response()->json($responseData);

		//echo $benchApplications =	$benchApplications->toSql();

	}

}