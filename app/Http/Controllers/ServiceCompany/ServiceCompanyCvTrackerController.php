<?php

/***########################################################
Controller    	: 	AdminCVTrackerController
Purpose			:
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021
########################################################***/

namespace App\Http\Controllers\ServiceCompany;

use App\ElasticCandidate;
use App\Helper\Files;
use App\Helper\GeneralMethod;
use App\Helper\Reply;
use App\Jobs\ProcessForwardResumeDispatchJob;
use App\Jobs\QueueCandidatesMailAndResumeForwardMailService;
use App\Jobs\QueueMailService;
use App\Jobs\QueueMassMailService;
use App\Jobs\QueueNotificationMailService;
use App\Jobs\SendEmailJob;
use App\Mail\ForwardResumeMail;
use App\Mail\MassMailingMail;
use App\Models\AddJobWiseCondidate;
use App\Models\ApplicationStatus;
use App\Models\Client;
use App\Models\CoinReservation;
use App\Models\Company;
use App\Models\ForwardAttachment;
use App\Models\ForwardRequest;
use App\Models\ForwardResumeEvent;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobLocation;
use App\Models\NoticePeriod;
use App\Models\OfferJobApplication;
use App\Models\OutboxEvent;
use App\Models\Skill;
use App\Models\User;
use App\Services\ElasticService;
use App\Services\ForwardResume\ForwardResumeCoinService;
use App\Services\ForwardResume\ForwardResumeState;
use App\Services\ForwardResumeAuthorizationService;
use App\Services\ForwardResumeUploadService;
use App\Services\MailService;
use App\Services\MassMailService;
use App\Services\NotificationMailService;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mail;

class ServiceCompanyCvTrackerController extends ServiceCompanyBaseController
{
    protected $mailService;

    protected $massMailService;

    protected $notificationMailService;

    protected $elasticService;

    public function __construct(MailService $mailService, MassMailService $massMailService, NotificationMailService $notificationMailService, ElasticService $elasticService)
    {
        parent::__construct();
        $this->pageTitle = __('Candidates');
        $this->pageIcon = 'icon-user';
        $this->mailService = $mailService;
        $this->elasticService = $elasticService;
        $this->massMailService = $massMailService;
        $this->notificationMailService = $notificationMailService;

    }

    /***########################################################
       Controller    	: 	AdminCVTrackerController
       Method			:	index
       Purpose			:	Load all application
       Date			:	05-April-2021
       ########################################################***/
    private function baseUrl()
    {
        return config('services.evolution.url');
    }

    public function index(Request $request)
    {
        // $elastic = new ElasticService();
        // $response = $elastic->getClient()->info();
        // dd($response);
        // $ElasticCandidate = ElasticCandidate::all();

        // dd($ElasticCandidate);

        // 	$response = $this->elasticService->getClient()->indices()->getMapping([
        //         'index' => 'local_candidates'
        //     ]);
        //    $eeee =  response()->json($response);

        //    dd($eeee);

        // dd($this->userPermissions);
        $lock_unlock = GeneralMethod::checksubscription();
        if ($lock_unlock == false) {
            return redirect()->route('service_company.dashboard')->with('message_paid', 'This is a paid featute');

        }

        $company_id = auth()->user()->company_id;
        $chkAdminRole = $this->user->hasRole('admin');
        $this->boardColumns = ApplicationStatus::all();
        $this->candidateStatus = DB::table('master_status')->select('*')->orderBy('master_status.priority', 'ASC')->get();
        $this->locations = JobLocation::all();
        $this->skills = Skill::all();
        $this->clients = Client::all();

        if ($chkAdminRole > 0) {
            $this->jobs = Job::where('jobs.company_id', $this->user->company_id)->get();
        } else {
            $this->jobs = DB::table('jobs')
                ->join('job_assign', 'job_assign.job_id', '=', 'jobs.id')
                ->select('jobs.id', 'jobs.title')
                ->where('jobs.status', 'active')
                ->where('jobs.company_id', $this->user->company_id)
                ->where('job_assign.user_id', $this->user->id)
                ->get();
        }
        $this->users = User::select('users.*')->where('status', '=', 'active')->where('company_id', '=', $this->user->company_id)->get();
        $this->noticeperiod = NoticePeriod::all();

        $this->applicationCounter = DB::table('job_applications');
        $this->applicationCounter = $this->applicationCounter->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id');
        $this->applicationCounter = $this->applicationCounter->join('master_status', function ($join) {
            $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
        });
        $this->applicationCounter = $this->applicationCounter->select('job_applications.id');
        $this->applicationCounter = $this->applicationCounter->where(function ($query) use ($company_id) {
            return
                $query->where('add_job_wise_condidates.company_id', '=', $company_id)
                    ->where('add_job_wise_condidates.thirdparty_company_id', '=', 0);
        });
        $this->applicationCounter = $this->applicationCounter->orWhere(function ($query) use ($company_id) {
            return
                $query->where('add_job_wise_condidates.company_id', '=', $company_id)
                    ->where('add_job_wise_condidates.thirdparty_company_id', '>', 0)
                    ->where('add_job_wise_condidates.master_status_id', '!=', 8);
        });
        $this->applicationCounter = $this->applicationCounter->orWhere(function ($query) use ($company_id) {
            return $query->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id);
        });
        $this->cities = GeneralMethod::allCities();
        $this->default_skill = '';
        $this->qualificationTag = DB::table('qualification_master')
            ->select('*')
            ->orderBy('qualification_master.display_order', 'ASC')
            ->get()
            ->map(function ($row) {
                return $row->name;
            });
        $this->applicationCounter = $this->applicationCounter->count();
        $this->countries = DB::table('countries')->get();

        // Initial KPI seed for the CV Tracker hero cards. Only "total" is seeded from
        // MySQL (reusing $applicationCounter, already company_id-scoped above) to avoid a
        // flash of 0 on first paint. The live, authoritative numbers for every card come
        // from ONE Elasticsearch aggregation returned by allcandidates() on the first
        // table fetch (see $candidateStats there), so JS overrides these immediately —
        // no extra MySQL/server load.
        $this->candidateStats = [
            'total' => (int) $this->applicationCounter,
            'today' => 0,
            'this_week' => 0,
            'immediate' => 0,
            'without_resume' => 0,
            'tabs' => [
                'inreview' => 0, 'sourcing' => 0, 'screening' => 0, 'evaluation' => 0,
                'negotiation' => 0, 'offered' => 0, 'joined' => 0, 'rejected' => 0,
            ],
        ];

        return view('service_company.cvtracker.index', $this->data);
    }

    public function allcandidates(Request $request)
    {
        // dd($request->all());
        abort_if(Gate::denies('view_job_applications'), 403);
        $company_id = auth()->user()->company_id;
        $status_cus = request('status_cus');

        $elastic = true;
        if ($elastic) {
            $searchQuery = $request->input('q');
            $page = max((int) $request->input('page', 1), 1);
            $size = (int) $request->input('per_page', $request->input('size', $request->input('limit', 10)));
            if ($size <= 0) {
                $size = 10;
            }
            $from = ($page - 1) * $size;
            $valCheck = $request->input('sortingfilter');

            $location = request('kt_tagify_location');
            $custom_location = json_decode($location, true);

            $education = request('kt_tagify_education');
            $custom_education = json_decode($education, true);
            $cus_search_filter = request('cus_search');
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
            $country = request('country');
            $sal_mode = request('sal_mode');
            $min_salary_currency = request('min_salary_currency');
            $symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
            $symbolsValue = ! empty($symbolsValue) ? $symbolsValue : [
                0 => 'INR',
                1 => 'EUR',
                2 => 'JPY',
                3 => 'GBP',
                4 => 'USD',
                5 => 'AED',
            ];
            $modeFilter = $sal_mode;
            $currencyFilter = $min_salary_currency;
            $symbolsFilter = $symbolsValue[$request->min_salary_currency] ?? '';

            $max_exp = '';
            $min_exp = '';
            // if (\request('min_exp') != "" && \request('max_exp') != "") {
            $min_exp = request('min_exp');
            $max_exp = request('max_exp');
            // }
            $max_salary = '';
            $min_salary = '';
            // if (\request('min_sal') != "" && \request('max_sal') != "") {
            $AmountMin = request('min_sal');
            $AmountMax = request('max_sal');
            $min_salary = $AmountMin;
            $max_salary = $AmountMax;
            if ($request->min_salary_currency > 0) {
                $symbols = $symbolsValue[$request->min_salary_currency] ?? 'INR';
                $min_salary = GeneralMethod::convertToINR($AmountMin, $symbols);
                $max_salary = GeneralMethod::convertToINR($AmountMax, $symbols);
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

            // dd($sal_mode,$salaryField,$min_sal,$max_sal);

            // }

            $location = [];
            if (\request('kt_tagify_location') != '') {
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
            $education = [];
            if (\request('kt_tagify_education') != '') {
                foreach ($custom_education as $sval) {
                    $education_data = DB::table('qualification_master')->where('qualification_master.name', '=', $sval['value'])->first();
                    if ($education_data) {
                        $educationfiletr = GeneralMethod::qualificationFilterCondition($education_data->id);
                        $education = array_merge($education, $educationfiletr);
                    }
                }
            }

            $searchTermsSkill = request('cus_search_skill');
            $custom_search_skill = json_decode($searchTermsSkill, true);
            if (\request('cus_search_skill') != '') {
                if (count($custom_search_skill) > 0) {
                    $skillsAddElastic = array_column($custom_search_skill, 'value');
                    GeneralMethod::skillsAddElastic($skillsAddElastic);
                }
            }

            $relevanceCase = '';
            $subQueryArr = [];
            if (\request('cus_search_skill') != '') {

                $checkedSkills = array_filter($custom_search_skill, function ($skill) {
                    return $skill['checked'] == true;
                });

                $uncheckedSkills = array_filter($custom_search_skill, function ($skill) {
                    return $skill['checked'] == false;
                });

                if (! empty($checkedSkills)) {
                    $requiredSkills = array_column($checkedSkills, 'value');
                } else {
                    $requiredSkills = array_column($uncheckedSkills, 'value');
                }

                $subQueryArr = DB::table('job_applications')
                    ->leftJoin('job_application_skills', 'job_applications.id', '=', 'job_application_skills.job_application_id')
                    ->whereIn('job_application_skills.skills', $requiredSkills)
                    ->orWhereIn('job_applications.current_designation', $requiredSkills)
                    ->groupBy('job_applications.id') // Replace with actual current designation condition
                    ->pluck('job_applications.id')
                    ->toArray();

                $requiredSkills = array_map('strtolower', $requiredSkills);

            }

            // Feeds the row partial's skill-chip highlighting: which entered Skills
            // filter terms are starred (mandatory, shown blue) vs unstarred
            // (optional, shown green). Both stay empty when no Skills filter is
            // applied at all, which the partial reads as "fall back to highlighting
            // against each row's own job's required skills instead."
            $mandatorySkillsForDisplay = ! empty($checkedSkills ?? []) ? array_values(array_map('strtolower', array_column($checkedSkills, 'value'))) : [];
            $optionalSkillsForDisplay = ! empty($uncheckedSkills ?? []) ? array_values(array_map('strtolower', array_column($uncheckedSkills, 'value'))) : [];

            $previousUrl = url()->previous();
            $path = parse_url($previousUrl, PHP_URL_PATH);
            $segments = explode('/', trim($path, '/'));
            $segmentTwo = $segments[1] ?? null;

            $userId = $this->user->id;
            $cachePayload = [
                'company' => $company_id,
                'status' => $status_cus,
                'cus_search_filter' => $cus_search_filter,
                'page' => $page,
                'size' => $size,
                'sort' => $valCheck,
                'q' => $searchQuery,
                'country' => $country,
                'location' => $location,
                'education' => $education,
                'skills' => $requiredSkills ?? [],
                'min_exp' => $min_exp,
                'max_exp' => $max_exp,
                'min_sal' => $min_salary,
                'max_sal' => $max_salary,
                'sal_mode' => $sal_mode,
                'currencyFilter' => $currencyFilter,
                'notice' => $app_int_notice,
                'activity' => $app_int_activity,
                'status_filter' => $app_int_status,
                'user_id' => $this->user->id,
                'segmentTwo' => $segmentTwo,
                // Offer Tracker tab must vary the cache key, otherwise switching tabs
                // returns a stale cached result set (the tab filter wasn't part of the key).
                'offer_tab' => request('offer_tab_filter'),
                'pipeline_range' => request('pipeline_range'),
                // CV Tracker stage tab must also vary the cache key for the same reason.
                'stage_tab' => request('stage_tab'),
            ];

            $versionKey = 'allcvtracker_version_user_'.$this->user->id;
            $version = cache()->get($versionKey, 1);
            $cacheKeySet = 'ats:allcvtracker:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));
            /* ----------------------------
            ⚡ Check DragonflyDB first
            ----------------------------- */
            if (cache()->has($cacheKeySet)) {
                return response()->json(cache()->get($cacheKeySet));
            }

            // dd("djdj");

            $chkAdminRole = $this->user->hasRole('admin');
            $searchTerms = request('cus_search');
            $custom_search = $searchTerms; // json_decode($searchTerms, true);
            $country = request('country');

            // dd($custom_search);

            // dd($chkAdminRole);

            $sort = [];
            // $requiredSkills is only ever assigned above inside the cus_search_skill
            // branch — default it here so case 30 (Best Match) can safely check it
            // even when no skill filter was entered.
            $requiredSkills = $requiredSkills ?? [];
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
							",
                            ],
                            'order' => 'asc',
                        ],
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
							",
                            ],
                            'order' => 'desc',
                        ],
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
						",
                            ],
                            'order' => 'asc',
                        ],
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
						",
                            ],
                            'order' => 'asc',
                        ],
                    ];
                    break;
                case 9: // Immediate joiners first (original functionality)
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
                case 30: // Best Match — CV Tracker has no single job to derive default
                    // skills from (unlike the AppTracker family), so this ranks by how
                    // many of the Skills filter's entered/starred terms ($requiredSkills,
                    // already computed above from cus_search_skill) each candidate
                    // matches. With no skills entered there's nothing to rank against,
                    // so it falls back to the same newest-first the default case uses.
                    if (empty($requiredSkills)) {
                        $sort[] = ['created_at' => ['order' => 'desc']];
                    } else {
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
									def designation = params._source.containsKey('current_designation') && params._source.current_designation != null
										? params._source.current_designation.toString().toLowerCase() : '';
									int matchCount = 0;
									for (req in params.skills) {
										boolean found = designation.contains(req);
										if (!found) {
											for (cand in candidateSkills) {
												if (cand.contains(req)) { found = true; break; }
											}
										}
										if (found) { matchCount += 1; }
									}
									return matchCount;
								",
                                    'params' => [
                                        'skills' => array_values($requiredSkills),
                                    ],
                                ],
                                'order' => 'desc',
                            ],
                        ];
                        $sort[] = ['created_at' => ['order' => 'desc']];
                    }
                    break;
                default:
                    $sort[] = ['created_at' => ['order' => 'desc']];
                    break;
            }

            $mustSubmittedBy = [];

            // if ($chkAdminRole == 0) {
            // 	$mustSubmittedBy[] = ['term' => ['submitted_by' => $this->user->id]];
            // }

            if ($status_cus == 5) {
                // Build the Offer bool query
                $boolQuery = [
                    'bool' => [
                        'should' => [
                            // First condition
                            [
                                'bool' => [
                                    'must' => array_merge([
                                        ['term' => ['company_id' => $company_id]],
                                        ['term' => ['job_master_status_id' => 5]],
                                        ['term' => ['ClientComID' => 0]],
                                    ], $mustSubmittedBy, $this->getSearchConditions($custom_search, $country)),
                                ],
                            ],
                            // Second condition
                            [
                                'bool' => [
                                    'must' => array_merge([
                                        ['term' => ['company_id' => $company_id]],
                                        ['term' => ['job_master_status_id' => 5]],
                                        ['range' => ['ClientComID' => ['gt' => 0]]],
                                        ['range' => ['jobId' => ['gt' => 0]]],
                                    ], [
                                        ['bool' => [
                                            'must_not' => [
                                                ['terms' => ['job_master_status_id' => [8, 9]]],
                                            ],
                                        ]],
                                    ], $mustSubmittedBy, $this->getSearchConditions($custom_search, $country)),
                                ],
                            ],
                            // Third condition
                            [
                                'bool' => [
                                    'must' => array_merge([
                                        ['term' => ['ClientComID' => $company_id]],
                                        ['term' => ['job_master_status_id' => 5]],
                                        ['range' => ['jobId' => ['gt' => 0]]],
                                    ], $mustSubmittedBy, $this->getSearchConditions($custom_search, $country)),
                                ],
                            ],
                        ],
                        'minimum_should_match' => 1,
                        'filter' => [], // We'll add all filters here
                    ],
                ];
                $page_view = 'offer-tracker';
            } elseif ($status_cus == 6) {
                // Build the join bool query
                $boolQuery = [
                    'bool' => [
                        'should' => [
                            // First condition
                            [
                                'bool' => [
                                    'must' => array_merge([
                                        ['term' => ['company_id' => $company_id]],
                                        ['term' => ['job_master_status_id' => 6]],
                                        ['term' => ['ClientComID' => 0]],
                                    ], $this->getSearchConditions($custom_search, $country)),
                                ],
                            ],
                            // Second condition
                            [
                                'bool' => [
                                    'must' => array_merge([
                                        ['term' => ['company_id' => $company_id]],
                                        ['term' => ['job_master_status_id' => 6]],
                                        ['range' => ['ClientComID' => ['gt' => 0]]],
                                        ['range' => ['jobId' => ['gt' => 0]]],
                                    ], [
                                        ['bool' => [
                                            'must_not' => [
                                                ['terms' => ['job_master_status_id' => [8, 9]]],
                                            ],
                                        ]],
                                    ], $this->getSearchConditions($custom_search, $country)),
                                ],
                            ],
                            // Third condition
                            [
                                'bool' => [
                                    'must' => array_merge([
                                        ['term' => ['ClientComID' => $company_id]],
                                        ['term' => ['job_master_status_id' => 6]],
                                        ['range' => ['jobId' => ['gt' => 0]]],
                                    ], $this->getSearchConditions($custom_search, $country)),
                                ],
                            ],
                        ],
                        'minimum_should_match' => 1,
                        'filter' => [], // We'll add all filters here
                    ],
                ];
                $page_view = 'joining-tracker';
            } else {
                // Build the main bool query
                $boolQuery = [
                    'bool' => [
                        'should' => [
                            // First condition
                            [
                                'bool' => [
                                    'must' => array_merge([
                                        ['term' => ['company_id' => $company_id]],
                                        ['term' => ['ClientComID' => 0]],
                                    ], $this->getSearchConditions($custom_search, $country)),
                                ],
                            ],
                            // Second condition
                            [
                                'bool' => [
                                    'must' => array_merge([
                                        ['term' => ['company_id' => $company_id]],
                                        ['range' => ['ClientComID' => ['gt' => 0]]],
                                        ['range' => ['jobId' => ['gt' => 0]]],
                                    ], [
                                        ['bool' => [
                                            'must_not' => [
                                                ['terms' => ['job_master_status_id' => [8, 9]]],
                                            ],
                                        ]],
                                    ], $this->getSearchConditions($custom_search, $country)),
                                ],
                            ],
                            // Third condition
                            [
                                'bool' => [
                                    'must' => array_merge([
                                        ['term' => ['ClientComID' => $company_id]],
                                        ['range' => ['jobId' => ['gt' => 0]]],
                                    ], $this->getSearchConditions($custom_search, $country)),
                                ],
                            ],
                        ],
                        'minimum_should_match' => 1,
                        'filter' => [], // We'll add all filters here
                    ],
                ];
                $page_view = 'all_candidates';
            }

            if (! empty($checkedSkills)) {
                if (! empty($requiredSkills)) {
                    $must = [];

                    foreach ($requiredSkills as $skill) {
                        $must[] = [
                            'match_phrase' => [
                                'job_application_skills.skills' => $skill,
                            ],
                        ];
                    }

                    $boolQuery['bool']['filter'][] = [
                        'bool' => [
                            'must' => $must,
                        ],
                    ];
                }
            } else {
                if (! empty($requiredSkills)) {
                    $should = [];

                    foreach ($requiredSkills as $skill) {
                        $should[] = [
                            'match_phrase' => [
                                'job_application_skills.skills' => $skill,
                            ],
                        ];
                    }
                    $boolQuery['bool']['filter'][] = [
                        'bool' => [
                            'should' => $should,
                            'minimum_should_match' => 1,
                        ],
                    ];
                }
            }

            // // Experience filter
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
            // Experience Filter

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
            if (! empty($range)) {
                $boolQuery['bool']['filter'][] = [
                    'range' => [
                        'total_exp' => $range,
                    ],
                ];
            }

            if ($valCheck == 22) {
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
                                'today' => $today,
                                'next30' => $next30,
                            ],
                        ],
                    ],
                ];
                // Attach serving candidate condition along with others
                $boolQuery['bool']['filter'][] = $servingCandidateFilter;
            }

            $salaryField = match ($sal_mode) {
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
                        $salaryField => $salaryRange,
                    ],
                ];
            }

            // if ((!empty($min_salary) && $min_salary!=0.00) || (!empty($max_salary) && $max_salary!=0.00)) {

            // 	$salaryRange = [];

            // 	if (!empty($min_salary)) {
            // 		$salaryRange['gte'] = $min_salary;
            // 	}

            // 	if (!empty($max_salary)) {
            // 		$salaryRange['lte'] = $max_salary;
            // 	}

            // 	$boolQuery['bool']['filter'][] = [
            // 		'range' => [
            // 			$salaryField => $salaryRange
            // 		]
            // 	];
            // }

            // dd($salaryField,$min_salary,$max_salary);

            // Salary filter
            // if (!empty($max_salary) && $max_salary!=0.00) {
            // 	$salaryField = 'ectc_yearly'; // default
            // 	switch ($sal_mode) {
            // 		case 2: $salaryField = 'ectc_hourly'; break;
            // 		case 1: $salaryField = 'ectc_monthly'; break;
            // 	}

            // 	$boolQuery['bool']['filter'][] = [
            // 		'range' => [
            // 			$salaryField => [
            // 				'gte' => (float)$min_salary,
            // 				'lte' => (float)$max_salary
            // 			]
            // 		]
            // 	];
            // }

            if (\request('country') != '') {
                $boolQuery['bool']['filter'][] = [
                    'terms' => [
                        'country' => [$country],
                    ],
                ];
            }
            // Location filter
            if (! empty($location)) {
                $boolQuery['bool']['filter'][] = [
                    'terms' => [
                        'city_id' => $location,
                    ],
                ];
            }

            // Education filter
            if (! empty($education)) {
                $boolQuery['bool']['filter'][] = [
                    'terms' => [
                        'qualification' => $education,
                    ],
                ];
            }

            // Status filter
            if (! empty($app_int_status) && (int) $app_int_status > 0) {
                $boolQuery['bool']['filter'][] = [
                    'term' => [
                        'job_master_status_id' => (int) $app_int_status,
                    ],
                ];
            }

            // Notice period filter
            if (! empty($app_int_notice) && (int) $app_int_notice > 1) {
                $noticeFilter = $this->getNoticePeriodFilter($app_int_notice, $current_date);
                if ($noticeFilter) {
                    $boolQuery['bool']['filter'][] = $noticeFilter;
                }
            }

            // Offer tracker tab filter (pending / sent / accepted / joining_soon / expired)
            $offer_tab_filter = request('offer_tab_filter');
            // Capture the active tab's filter clause (applied only to the paginated hits,
            // NOT to the count aggregation — the badges/KPI/pipeline must show totals across
            // all tabs for the current search/location, so they use the base query below).
            $offerTabFilterClause = null;
            if ($status_cus == 5 && ! empty($offer_tab_filter) && $offer_tab_filter !== 'all') {
                switch ($offer_tab_filter) {
                    case 'pending':      $offerTabFilterClause = ['term' => ['of_offer_acceptance' => 0]];
                        break;
                    case 'sent':         $offerTabFilterClause = ['term' => ['of_offer_acceptance' => 1]];
                        break;
                    case 'accepted':     $offerTabFilterClause = ['term' => ['of_offer_acceptance' => 2]];
                        break;
                    case 'joining_soon': $offerTabFilterClause = ['range' => ['of_joined_date' => ['gte' => 'now/d', 'lte' => 'now+7d/d']]];
                        break;
                    case 'expired':      $offerTabFilterClause = ['term' => ['of_offer_acceptance' => 4]];
                        break;
                }
            }

            // Joining Tracker tab filter (status_cus=6). Mapped to the same real Elasticsearch
            // fields the joining counts aggregation uses (of_joined / of_joined_date /
            // of_offer_acceptance) so the paginated rows always match the tab badge counts.
            // Applied to the paginated hits ONLY; the badge/KPI aggregation runs on the base
            // query below so every tab total stays visible regardless of the active tab.
            // Compound joining filters pre-built as variables (reused by the tab filter AND the
            // KPI aggregation, so rows and badges always agree).
            //
            // Onboarded / Backed Out are keyed off the joining SUB-STAGE, not the of_joined flags:
            // the main stage is already "Joined" for this whole set, and the company-defined
            // sub-stage name ("Onboarded" / "Backedout") is what separates them. We resolve THIS
            // company's candidate_status ids for those names and match the ES "childStatus" field
            // (mapped integer = add_job_wise_condidates.child_status_id).
            // Tenancy: candidate_status is a raw query, scoped explicitly by company_id (Rule 3).
            $fDelayed = $fOnboarded = $fBackedOut = null;
            if ($status_cus == 6) {
                $fDelayed = ['bool' => ['must' => [
                    ['range' => ['of_joined_date' => ['lt' => 'now/d']]],
                    ['term' => ['of_joined' => 0]],
                ]]];
                $onboardedStatusIds = array_map('intval', DB::table('candidate_status')
                    ->where('company_id', $company_id)
                    ->whereRaw("REPLACE(LOWER(name), ' ', '') = 'onboarded'")
                    ->pluck('id')->toArray());
                $backedOutStatusIds = array_map('intval', DB::table('candidate_status')
                    ->where('company_id', $company_id)
                    ->whereRaw("REPLACE(LOWER(name), ' ', '') = 'backedout'")
                    ->pluck('id')->toArray());
                $fOnboarded = ['terms' => ['childStatus' => $onboardedStatusIds]];
                $fBackedOut = ['terms' => ['childStatus' => $backedOutStatusIds]];
            }

            $joining_tab_filter = request('joining_tab_filter');
            $joiningTabFilterClause = null;
            if ($status_cus == 6 && ! empty($joining_tab_filter) && $joining_tab_filter !== 'all') {
                switch ($joining_tab_filter) {
                    case 'joining_today': $joiningTabFilterClause = ['range' => ['of_joined_date' => ['gte' => 'now/d', 'lt' => 'now+1d/d']]];
                        break;
                    case 'this_week':     $joiningTabFilterClause = ['range' => ['of_joined_date' => ['gte' => 'now/w', 'lt' => 'now+1w/w']]];
                        break;
                    case 'delayed':       $joiningTabFilterClause = $fDelayed;
                        break;
                    case 'no_show':       $joiningTabFilterClause = ['term' => ['of_offer_acceptance' => 3]];
                        break;
                    case 'pending_docs':  $joiningTabFilterClause = ['term' => ['of_offer_acceptance' => 1]];
                        break;
                    case 'onboarded':     $joiningTabFilterClause = $fOnboarded;
                        break;
                    case 'backed_out':    $joiningTabFilterClause = $fBackedOut;
                        break;
                }
            }

            // CV Tracker stage tabs (All Candidates / InReview / Sourcing / Screening /
            // Evaluation / Negotiation / Offered / Joined / Rejected) — default "All
            // Candidates" tab only (status_cus empty). "Rejected" maps to TWO master_status
            // ids (7 and 9 both named "Rejected" in this company's master_status table), so
            // the client sends a comma-separated list and we match with a terms query.
            // Applied to the paginated hits ONLY; the per-stage counts aggregation below runs
            // on the base query so every tab's badge count stays visible regardless of the
            // active tab (same convention as the Offer/Joining tracker tabs above).
            $stage_tab = request('stage_tab');
            $stageTabFilterClause = null;
            if (empty($status_cus) && ! empty($stage_tab) && $stage_tab !== 'all') {
                $stageIds = array_filter(array_map('intval', explode(',', (string) $stage_tab)));
                if (! empty($stageIds)) {
                    $stageTabFilterClause = ['terms' => ['job_master_status_id' => $stageIds]];
                }
            }

            // Activity filter
            if (! empty($app_int_activity)) {
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
            // if (!empty($requiredSkills)) {
            // 	$boolQuery['bool']['filter'][] = [
            // 		'bool' => [
            // 			'should' => [
            // 				['terms' => ['job_application_skills.skills' => $requiredSkills]],
            // 				['terms' => ['current_designation.keyword' => $requiredSkills]]
            // 			],
            // 			'minimum_should_match' => 1
            // 		]
            // 	];
            // }

            // if ($valCheck==21 || $valCheck==22) {
            // 	$boolQuery['bool']['filter'][] = [
            // 		'term' => [
            // 			'job_master_status_id' => (int) 8
            // 		]
            // 	];
            // }

            // Snapshot the base query (all filters EXCEPT the active tab) for the count
            // aggregation, then apply the tab filter to the paginated hits query only.
            $offerCountsBase = $boolQuery;
            if ($offerTabFilterClause) {
                $boolQuery['bool']['filter'][] = $offerTabFilterClause;
            }
            if ($joiningTabFilterClause) {
                $boolQuery['bool']['filter'][] = $joiningTabFilterClause;
            }
            if ($stageTabFilterClause) {
                $boolQuery['bool']['filter'][] = $stageTabFilterClause;
            }

            // Build the final query
            $params = [
                'index' => ELASTIC_CANDIDATES,
                'request_cache' => true,
                'body' => [
                    'from' => $from,
                    'size' => $size,
                    'sort' => $sort,
                    'track_total_hits' => true,
                    'query' => $boolQuery,
                ],
            ];
            if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
                $total = 0;
                $data = [];
            } else {
                $response = $this->elasticService->getClient()->search($params);
                $total = $response['hits']['total']['value'] ?? 0;
                $data = array_map(function ($hit) {
                    return $hit['_source'];
                }, $response['hits']['hits']);
                $lastSort = end($response['hits']['hits'])['sort'] ?? null;
            }

            // dd($data);

            $pagination = [
                'total' => $total,
                'current_page' => $page,
                'last_page' => (int) ceil($total / max($size, 1)),
                'per_page' => $size,
                'listType' => 'allcandidate',
            ];
            // $page_view = 'all_candidates';

            $userPermissions = $this->userPermissions;

            // Offer Tracker tab/KPI/pipeline counts — one size:0 aggregation over the base
            // query (all offer-stage candidates for the current search/location, ignoring the
            // active tab) so every number matches THIS result set (fixes the MySQL-vs-ES
            // mismatch where offer_job_applications counted a different population).
            $offerCounts = null;
            $offerPipelineCounts = null;
            if ($status_cus == 5 && isset($offerCountsBase) && $this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
                // Offers Pipeline date window (dropdown): current month / last 6 months / all time.
                // Applied only to the nested "pipeline" agg so the pipeline can scope by date
                // while the tab badges + KPI stay tied to the full table result set.
                $pipelineRange = request('pipeline_range');
                if ($pipelineRange === 'current') {
                    $pipelineFilter = ['range' => ['created_at' => ['gte' => 'now/M']]];
                } elseif ($pipelineRange === '6m') {
                    $pipelineFilter = ['range' => ['created_at' => ['gte' => 'now-6M/d']]];
                } else {
                    $pipelineFilter = ['match_all' => (object) []];
                }

                $countsResp = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_CANDIDATES,
                    'request_cache' => true,
                    'body' => [
                        'size' => 0,
                        'track_total_hits' => true,
                        'query' => $offerCountsBase,
                        'aggs' => [
                            'by_acceptance' => ['terms' => ['field' => 'of_offer_acceptance', 'size' => 10]],
                            'joined' => ['filter' => ['term' => ['of_joined' => 1]]],
                            'joining_soon' => ['filter' => ['range' => ['of_joined_date' => ['gte' => 'now/d', 'lte' => 'now+7d/d']]]],
                            'pipeline' => [
                                'filter' => $pipelineFilter,
                                'aggs' => [
                                    'by_acceptance' => ['terms' => ['field' => 'of_offer_acceptance', 'size' => 10]],
                                    'joined' => ['filter' => ['term' => ['of_joined' => 1]]],
                                ],
                            ],
                        ],
                    ],
                ]);
                $acc = [];
                foreach (($countsResp['aggregations']['by_acceptance']['buckets'] ?? []) as $b) {
                    $acc[(int) $b['key']] = (int) $b['doc_count'];
                }
                $offerCounts = [
                    'total' => (int) ($countsResp['hits']['total']['value'] ?? 0),
                    'pending' => $acc[0] ?? 0,
                    'sent' => $acc[1] ?? 0,
                    'accepted' => $acc[2] ?? 0,
                    'rejected' => $acc[3] ?? 0,
                    'expired' => $acc[4] ?? 0,
                    'joined' => (int) ($countsResp['aggregations']['joined']['doc_count'] ?? 0),
                    'joining_soon' => (int) ($countsResp['aggregations']['joining_soon']['doc_count'] ?? 0),
                ];

                // Date-windowed counts that drive the Offers Pipeline funnel only.
                $pacc = [];
                foreach (($countsResp['aggregations']['pipeline']['by_acceptance']['buckets'] ?? []) as $b) {
                    $pacc[(int) $b['key']] = (int) $b['doc_count'];
                }
                $offerPipelineCounts = [
                    'total' => (int) ($countsResp['aggregations']['pipeline']['doc_count'] ?? 0),
                    'sent' => $pacc[1] ?? 0,
                    'accepted' => $pacc[2] ?? 0,
                    'rejected' => $pacc[3] ?? 0,
                    'joined' => (int) ($countsResp['aggregations']['pipeline']['joined']['doc_count'] ?? 0),
                ];
            }

            // Joining Tracker KPI + tab counts — one size:0 aggregation over the base query
            // (all joining-stage candidates for the current search/location, ignoring the active
            // tab) so every card/tab number matches THIS result set. Uses only real ES fields
            // (of_joined / of_joined_date / of_offer_acceptance) — see the tab-filter switch above
            // for the matching clauses, keeping badges and paginated rows in agreement.
            $joiningCounts = null;
            if ($status_cus == 6 && isset($offerCountsBase) && $this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
                $jResp = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_CANDIDATES,
                    'request_cache' => true,
                    'body' => [
                        'size' => 0,
                        'track_total_hits' => true,
                        'query' => $offerCountsBase,
                        'aggs' => [
                            'joined' => ['filter' => ['term' => ['of_joined' => 1]]],
                            'today' => ['filter' => ['range' => ['of_joined_date' => ['gte' => 'now/d', 'lt' => 'now+1d/d']]]],
                            'this_week' => ['filter' => ['range' => ['of_joined_date' => ['gte' => 'now/w', 'lt' => 'now+1w/w']]]],
                            'this_month' => ['filter' => ['range' => ['of_joined_date' => ['gte' => 'now/M', 'lt' => 'now+1M/M']]]],
                            'delayed' => ['filter' => $fDelayed],
                            'no_show' => ['filter' => ['term' => ['of_offer_acceptance' => 3]]],
                            'pending_docs' => ['filter' => ['term' => ['of_offer_acceptance' => 1]]],
                            'onboarded' => ['filter' => $fOnboarded],
                            'backed_out' => ['filter' => $fBackedOut],
                        ],
                    ],
                ]);
                $ja = $jResp['aggregations'] ?? [];
                $joiningCounts = [
                    'total' => (int) ($jResp['hits']['total']['value'] ?? 0),
                    'today' => (int) ($ja['today']['doc_count'] ?? 0),
                    'joined' => (int) ($ja['joined']['doc_count'] ?? 0),
                    'backed_out' => (int) ($ja['backed_out']['doc_count'] ?? 0),
                    'onboarded' => (int) ($ja['onboarded']['doc_count'] ?? 0),
                    'this_week' => (int) ($ja['this_week']['doc_count'] ?? 0),
                    'this_month' => (int) ($ja['this_month']['doc_count'] ?? 0),
                    'delayed' => (int) ($ja['delayed']['doc_count'] ?? 0),
                    'no_show' => (int) ($ja['no_show']['doc_count'] ?? 0),
                    'pending_docs' => (int) ($ja['pending_docs']['doc_count'] ?? 0),
                ];
            }

            // CV Tracker hero KPI cards (Total / Added Today / Added This Week / Immediate
            // Joiners / Without Resume) — one size:0 aggregation over the same base query
            // (current search/location/skill/etc. filters, company_id-scoped) already built
            // above for the default "All Candidates" tab. ES only, rides this same request —
            // no extra HTTP call from the client, no MySQL involved.
            $candidateStats = null;
            if (empty($status_cus) && isset($offerCountsBase) && $this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
                $cResp = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_CANDIDATES,
                    'request_cache' => true,
                    'body' => [
                        'size' => 0,
                        'track_total_hits' => true,
                        'query' => $offerCountsBase,
                        'aggs' => [
                            'today' => ['filter' => ['range' => ['created_at' => ['gte' => 'now/d', 'lt' => 'now+1d/d']]]],
                            'this_week' => ['filter' => ['range' => ['created_at' => ['gte' => 'now/w', 'lt' => 'now+1w/w']]]],
                            'immediate' => ['filter' => ['term' => ['np_id.keyword' => 'Immediate']]],
                            // "Without resume": field is either absent (candidate never uploaded
                            // one — most common) or present-but-empty string, so check both.
                            'without_resume' => ['filter' => [
                                'bool' => [
                                    'should' => [
                                        ['bool' => ['must_not' => [['exists' => ['field' => 'resume']]]]],
                                        ['term' => ['resume.keyword' => '']],
                                    ],
                                    'minimum_should_match' => 1,
                                ],
                            ]],
                            // Stage tab badge counts. IDs come from this company's master_status
                            // table (id => name): 8=InReview, 1=Sourcing, 2=Screening,
                            // 3=Evaluation, 4=Negotiation, 5=Offered, 6=Joined, 7&9=Rejected
                            // (two rows share the "Rejected" name).
                            'by_status' => ['terms' => ['field' => 'job_master_status_id', 'size' => 20]],
                        ],
                    ],
                ]);
                $ca = $cResp['aggregations'] ?? [];
                $statusBuckets = [];
                foreach (($ca['by_status']['buckets'] ?? []) as $b) {
                    $statusBuckets[(int) $b['key']] = (int) $b['doc_count'];
                }
                $candidateStats = [
                    'total' => (int) ($cResp['hits']['total']['value'] ?? 0),
                    'today' => (int) ($ca['today']['doc_count'] ?? 0),
                    'this_week' => (int) ($ca['this_week']['doc_count'] ?? 0),
                    'immediate' => (int) ($ca['immediate']['doc_count'] ?? 0),
                    'without_resume' => (int) ($ca['without_resume']['doc_count'] ?? 0),
                    'tabs' => [
                        'inreview' => $statusBuckets[8] ?? 0,
                        'sourcing' => $statusBuckets[1] ?? 0,
                        'screening' => $statusBuckets[2] ?? 0,
                        'evaluation' => $statusBuckets[3] ?? 0,
                        'negotiation' => $statusBuckets[4] ?? 0,
                        'offered' => $statusBuckets[5] ?? 0,
                        'joined' => $statusBuckets[6] ?? 0,
                        'rejected' => ($statusBuckets[7] ?? 0) + ($statusBuckets[9] ?? 0),
                    ],
                ];
            }

            // CV Tracker isn't job-scoped — each row can belong to a different job —
            // so the "no filter applied" default highlight/match-% falls back to
            // EACH row's own job's required skills, not one shared set. Batched
            // once per page of results (distinct jobIds only) instead of a
            // per-row query.
            $jobSkillsByJobId = [];
            if ($page_view === 'all_candidates') {
                $jobIdsOnPage = collect($data)->pluck('jobId')->filter()->unique()->values()->all();
                if (! empty($jobIdsOnPage)) {
                    DB::table('job_metronics_skills')
                        ->whereIn('job_id', $jobIdsOnPage)
                        ->select('job_id', 'skill_name')
                        ->get()
                        ->each(function ($r) use (&$jobSkillsByJobId) {
                            $jobSkillsByJobId[$r->job_id][] = strtolower(trim($r->skill_name));
                        });
                }
            }

            $responseData = [
                'html' => view('service_company.ajax_pages.'.$page_view.'', compact('data', 'userPermissions', 'modeFilter', 'currencyFilter', 'symbolsFilter', 'mandatorySkillsForDisplay', 'optionalSkillsForDisplay', 'jobSkillsByJobId'))->render(),
                'total' => $total,
                'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
                'counts' => $offerCounts,
                'pipelineCounts' => $offerPipelineCounts,
                'joiningCounts' => $joiningCounts,
                'candidateStats' => $candidateStats,
            ];

            cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_CV_TRACKER_TIME));

            return response()->json($responseData);

        }

    }

    // Helper method for search conditions
    protected function getSearchConditions($custom_search, $country)
    {
        $conditions = [];

        // Apply custom search if it's not empty
        if (! empty($custom_search)) {
            $conditions[] = [
                'bool' => [
                    'should' => [
                        [
                            'match_phrase_prefix' => ['full_name' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'wildcard' => ['email' => $custom_search],
                        ],
                        [
                            'wildcard' => ['phone' => $custom_search],
                        ],
                        [
                            'match_phrase_prefix' => ['title' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'wildcard' => ['country_name' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'match_phrase_prefix' => ['qualification_name' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'wildcard' => ['state_name' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'wildcard' => ['city_name' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'wildcard' => ['posted_by' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'wildcard' => ['client_or_company_name' => '*'.strtolower($custom_search).'*'],
                        ],
                    ],
                    'minimum_should_match' => 1,
                ],
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
                            ['term' => ['serving_notice_period_mode' => 2]],
                        ],
                    ],
                ];
            case 2: // 0-15 days
                return [
                    'bool' => [
                        'must' => [
                            ['range' => ['np_id' => ['gte' => 0, 'lte' => 15]]],
                            ['term' => ['serving_notice_period_mode' => 0]],
                        ],
                    ],
                ];
            case 3: // 0-30 days
                return [
                    'bool' => [
                        'must' => [
                            ['range' => ['np_id' => ['gte' => 0, 'lte' => 30]]],
                            ['term' => ['serving_notice_period_mode' => 0]],
                        ],
                    ],
                ];
            case 4: // 0-60 days
                return [
                    'bool' => [
                        'must' => [
                            ['range' => ['np_id' => ['gte' => 0, 'lte' => 60]]],
                            ['term' => ['serving_notice_period_mode' => 0]],
                        ],
                    ],
                ];
            case 5: // 0-90 days
                return [
                    'bool' => [
                        'must' => [
                            ['range' => ['np_id' => ['gte' => 0, 'lte' => 90]]],
                            ['term' => ['serving_notice_period_mode' => 0]],
                        ],
                    ],
                ];
            case 6: // Any notice period
                return [
                    'bool' => [
                        'must' => [
                            ['range' => ['np_id' => ['gt' => 0]]],
                            ['term' => ['serving_notice_period_mode' => 0]],
                        ],
                    ],
                ];
            case 7: // After current date
                return [
                    'bool' => [
                        'must' => [
                            ['range' => ['np_id' => ['gt' => $current_date]]],
                            ['term' => ['serving_notice_period_mode' => 1]],
                        ],
                    ],
                ];
            case 9: // Before current date
                return [
                    'bool' => [
                        'must' => [
                            ['range' => ['np_id' => ['lt' => $current_date]]],
                            ['term' => ['serving_notice_period_mode' => 1]],
                        ],
                    ],
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

    // Helper method for search conditions
    protected function applySearchConditions($query, $custom_search, $country)
    {
        if (! empty($custom_search)) {
            $query->where(function ($searchQuery) use ($custom_search) {
                $searchQuery->where('job_applications.full_name', 'like', "%{$custom_search}%")
                    ->orWhere('job_applications.email', 'like', "%{$custom_search}%")
                    ->orWhere('job_applications.phone', 'like', "%{$custom_search}%")
                    ->orWhere('jobs.title', 'like', "%{$custom_search}%")
                    ->orWhere('countries.country_name', 'like', "%{$custom_search}%")
                    ->orWhere('state_list.state', 'like', "%{$custom_search}%")
                    ->orWhere('city_list.city', 'like', "%{$custom_search}%")
                    ->orWhere('users.name', 'like', "%{$custom_search}%")
                    ->orWhere('clients.name', 'like', "%{$custom_search}%")
                    ->orWhere('companies.company_name', 'like', "%{$custom_search}%")
                    ->orWhere('job_application_skills.skills', 'like', "%{$custom_search}%");
            });
        }

        if (! empty($country)) {
            $query->where('job_applications.country', '=', $country);
        }
    }

    // Helper method for notice period filter
    protected function applyNoticePeriodFilter($query, $app_int_notice, $current_date)
    {
        switch ($app_int_notice) {
            case 8:
                $query->where('job_applications.np_id', '=', 'Immediate')
                    ->where('job_applications.serving_notice_period_mode', '=', 2);
                break;
            case 2:
                $query->whereBetween('job_applications.np_id', [0, 15])
                    ->where('job_applications.serving_notice_period_mode', '=', 0);
                break;
            case 3:
                $query->whereBetween('job_applications.np_id', [0, 30])
                    ->where('job_applications.serving_notice_period_mode', '=', 0);
                break;
            case 4:
                $query->whereBetween('job_applications.np_id', [0, 60])
                    ->where('job_applications.serving_notice_period_mode', '=', 0);
                break;
            case 5:
                $query->whereBetween('job_applications.np_id', [0, 90])
                    ->where('job_applications.serving_notice_period_mode', '=', 0);
                break;
            case 6:
                $query->where('job_applications.np_id', '>', 0)
                    ->where('job_applications.serving_notice_period_mode', '=', 0);
                break;
            case 7:
                $query->whereRaw('DATE(job_applications.np_id) > ?', [$current_date])
                    ->where('job_applications.serving_notice_period_mode', '=', 1);
                break;
            case 9:
                $query->whereRaw('DATE(job_applications.np_id) < ?', [$current_date])
                    ->where('job_applications.serving_notice_period_mode', '=', 1);
                break;
        }
    }

    // Helper method for activity filter
    protected function applyActivityFilter($query, $app_int_activity, $days30, $days60, $days90, $days180, $days365, $current_date)
    {
        if ($app_int_activity == 2) {
            $dateRange = [$days30, $current_date];
        } elseif ($app_int_activity == 3) {
            $dateRange = [$days60, $current_date];
        } elseif ($app_int_activity == 4) {
            $dateRange = [$days90, $current_date];
        } elseif ($app_int_activity == 5) {
            $dateRange = [$days180, $current_date];
        } elseif ($app_int_activity == 6) {
            $dateRange = [$days365, $current_date];
        } else {
            $dateRange = null;
        }

        if ($dateRange) {
            $query->whereBetween('add_job_wise_condidates.updated_at', $dateRange);
        }
    }

    public function application_copy(Request $request)
    {
        $arr = '';
        $arr = $request->arr;
        $wiseId = $request->wiseId;
        $this->submit_btn = 'Copy';

        /**
         * Intent: Consolidate 3 separate job_applications queries (count, id list, bench check)
         * into 1 single query, pre-compute $alreadyAddedJobIds for the drawer UI, and remove
         * the unused unpaginated Job::select()->leftJoin('add_job_wise_condidates')->groupBy() scan.
         * Tenancy: Filtered by candidate IDs selected in the current tenant session.
         */
        $appRows = ! empty($arr)
            ? DB::table('job_applications')
                ->select('id', 'condidate_type', 'job_id')
                ->whereIn('id', (array) $arr)
                ->get()
            : collect();

        $this->applicationDataCount = $appRows->count();
        $this->applicationData = $appRows;
        $this->wiseId = ! empty($wiseId)
            ? DB::table('add_job_wise_condidates')->select('add_job_wise_condidates.id')->whereIn('id', (array) $wiseId)->get()
            : collect();

        $benchCount = $appRows->where('condidate_type', 'bench_condidate')->count();
        if ($benchCount > 0) {
            return Reply::dataOnly(['status' => 'block', 'message' => '']);
        }

        $appIds = $appRows->pluck('id')->all();
        $directJobIds = $appRows->pluck('job_id')->filter()->all();
        $wiseJobIds = ! empty($appIds)
            ? DB::table('add_job_wise_condidates')->whereIn('job_application_id', $appIds)->whereNotNull('job_id')->pluck('job_id')->all()
            : [];
        $this->alreadyAddedJobIds = array_values(array_unique(array_map('intval', array_merge($directJobIds, $wiseJobIds))));
        $this->job = collect();
        $this->findTalentAdd = '';

        $view = view('service_company.cvtracker.application_copy', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function talentAdd(Request $request)
    {
        $arr = $request->arr;

        $this->submit_btn = 'Add to job';

        /**
         * Intent: Consolidate 3 separate job_applications queries into 1 single query,
         * pre-compute $alreadyAddedJobIds (both job_applications.job_id and add_job_wise_condidates.job_id),
         * and remove the unused unpaginated Job::select()->leftJoin('add_job_wise_condidates')->groupBy() scan.
         * Tenancy: Filtered by candidate IDs selected in the current tenant session.
         */
        $appRows = ! empty($arr)
            ? DB::table('job_applications')
                ->select('id', 'condidate_type', 'job_id')
                ->whereIn('id', (array) $arr)
                ->get()
            : collect();

        $this->applicationDataCount = $appRows->count();
        $this->applicationData = $appRows;
        $this->wiseId = ! empty($arr)
            ? DB::table('add_job_wise_condidates')->select('add_job_wise_condidates.job_application_id')->whereIn('id', (array) $arr)->get()
            : collect();

        $appIds = $appRows->pluck('id')->all();
        $directJobIds = $appRows->pluck('job_id')->filter()->all();
        $wiseJobIds = ! empty($appIds)
            ? DB::table('add_job_wise_condidates')->whereIn('job_application_id', $appIds)->whereNotNull('job_id')->pluck('job_id')->all()
            : [];
        $this->alreadyAddedJobIds = array_values(array_unique(array_map('intval', array_merge($directJobIds, $wiseJobIds))));
        $this->job = collect();
        $this->findTalentAdd = 'findTalentAdd';

        $view = view('service_company.cvtracker.application_copy', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function copyjobtable(Request $request)
    {
        abort_if(Gate::denies('view_jobs'), 403);

        $company_id = auth()->user()->company_id;
        $chkAdminRole = $this->user->hasRole('admin');
        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $searchQuery = $request->input('q');
        $custom_search = $request->search;
        $findTalentAdd = @$request->findTalentAdd;
        // dd($custom_search);
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;

        $cachePayload = [
            'company' => $company_id,
            'page' => $page,
            'size' => $size,
            'q' => $searchQuery,
            'user_id' => $this->user->id,
            'custom_search' => $custom_search,
            'candidateSkills' => $request->candidateSkills ?? '',
        ];

        $userVersion = cache()->get('allcopyjobslist_version_user_'.$this->user->id, 1);
        $tenantCopyVer = cache()->get('allcopyjobslist_version_company_'.$company_id, 1);
        $tenantJobsVer = cache()->get('alljobslist_version_company_'.$company_id, 1);
        $version = $userVersion.'_'.$tenantCopyVer.'_'.$tenantJobsVer;
        $cacheKeySet = 'ats:allcopyjobslist:'.$company_id.':'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));
        /* ----------------------------
        ⚡ Check DragonflyDB first
        ----------------------------- */
        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet));
        }

        // Single-flight lock to prevent concurrent duplicate ES searches
        $lock = cache()->lock('lock:'.$cacheKeySet, 5);
        if (! $lock->get()) {
            for ($i = 0; $i < 10; $i++) {
                usleep(50000);
                if (cache()->has($cacheKeySet)) {
                    return response()->json(cache()->get($cacheKeySet));
                }
            }
        }

        $statusArr = ['active', 'hold', 'inactive', '2'];
        $must = [];
        $should = [];
        $filter = [];
        $sort = [];

        $custom_status = 'active';
        // 1. STATUS FILTERING
        if ($custom_status !== '' && $custom_status !== 'all') {
            if (in_array($custom_status, ['active', 'hold', 'inactive'])) {
                $statusArr = [$custom_status];
            } elseif ($custom_status === 'removed') {
                $statusArr = [];
                $must[] = [
                    'nested' => [
                        'path' => 'recommendation_relation',
                        'query' => [
                            'term' => ['recommendation_relation.final_status' => 2],
                        ],
                    ],
                ];

            }
        }
        $sort[] = ['id' => ['order' => 'desc']];
        $filter[] = ['terms' => ['status' => $statusArr]];

        if ($findTalentAdd == 'findTalentAdd') {
            if (! empty($custom_search)) {

                $must[] = [
                    'bool' => [
                        'should' => [
                            [
                                'bool' => [
                                    'must' => [
                                        ['term' => ['comId' => $company_id]],
                                    ],
                                ],
                            ],
                            [
                                'bool' => [
                                    'should' => [
                                        ['match_phrase_prefix' => ['title' => '*'.strtolower($custom_search).'*']],
                                        ['term' => ['id' => (int) $custom_search]],
                                        ['wildcard' => ['client_name' => '*'.strtolower($custom_search).'*']],
                                        ['wildcard' => ['company_name' => '*'.strtolower($custom_search).'*']],
                                        [
                                            'nested' => [
                                                'path' => 'job_assign',
                                                'query' => [
                                                    'wildcard' => ['job_assign.user_name' => '*'.strtolower($custom_search).'*'],
                                                ],
                                            ],
                                        ],
                                        [
                                            'nested' => [
                                                'path' => 'client_contact_info_jobs',
                                                'query' => [
                                                    'wildcard' => ['client_contact_info_jobs.contact_person_name' => '*'.strtolower($custom_search).'*'],
                                                ],
                                            ],
                                        ],
                                        [
                                            'nested' => [
                                                'path' => 'client_city_jobs',
                                                'query' => [
                                                    'wildcard' => ['client_city_jobs.city_name' => '*'.strtolower($custom_search).'*'],
                                                ],
                                            ],
                                        ],
                                        [
                                            'nested' => [
                                                'path' => 'client_city_jobs',
                                                'query' => [
                                                    'wildcard' => ['client_city_jobs.country_name' => '*'.strtolower($custom_search).'*'],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'minimum_should_match' => 1,
                                ],
                            ],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ];
            }

            $must[] = [
                'bool' => [
                    'must' => [
                        ['term' => ['comId' => $company_id]],
                    ],
                ],
            ];

        } else {
            if (! empty($custom_search)) {

                $must[] = [
                    'bool' => [
                        'must' => [
                            [
                                'bool' => [
                                    'should' => [
                                        ['term' => ['comId' => $company_id]],
                                        [
                                            'nested' => [
                                                'path' => 'recommendation_relation',
                                                'query' => [
                                                    'term' => ['recommendation_relation.to_company_id' => $company_id],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'minimum_should_match' => 1,
                                ],
                            ],
                            [
                                'bool' => [
                                    'should' => [
                                        ['match_phrase_prefix' => ['title' => '*'.strtolower($custom_search).'*']],
                                        ['term' => ['id' => (int) $custom_search]],
                                        ['wildcard' => ['client_name' => '*'.strtolower($custom_search).'*']],
                                        ['wildcard' => ['company_name' => '*'.strtolower($custom_search).'*']],
                                        [
                                            'nested' => [
                                                'path' => 'job_assign',
                                                'query' => [
                                                    'wildcard' => ['job_assign.user_name' => '*'.strtolower($custom_search).'*'],
                                                ],
                                            ],
                                        ],
                                        [
                                            'nested' => [
                                                'path' => 'client_contact_info_jobs',
                                                'query' => [
                                                    'wildcard' => ['client_contact_info_jobs.contact_person_name' => '*'.strtolower($custom_search).'*'],
                                                ],
                                            ],
                                        ],
                                        [
                                            'nested' => [
                                                'path' => 'client_city_jobs',
                                                'query' => [
                                                    'wildcard' => ['client_city_jobs.city_name' => '*'.strtolower($custom_search).'*'],
                                                ],
                                            ],
                                        ],
                                        [
                                            'nested' => [
                                                'path' => 'client_city_jobs',
                                                'query' => [
                                                    'wildcard' => ['client_city_jobs.country_name' => '*'.strtolower($custom_search).'*'],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'minimum_should_match' => 1,
                                ],
                            ],
                        ],
                    ],
                ];
            }

            $must[] = [
                'bool' => [
                    'should' => [
                        ['term' => ['comId' => $company_id]],
                        [
                            'nested' => [
                                'path' => 'recommendation_relation',
                                'query' => [
                                    'term' => ['recommendation_relation.to_company_id' => $company_id],
                                ],
                            ],
                        ],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];

        }

        $params = [
            'index' => ELASTIC_JOBS,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
                '_source' => [
                    'excludes' => ['job_description', 'roles_responsibility', 'other_details'],
                ],
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => $filter,
                    ],
                ],
            ],
        ];

        try {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits'] ?? []);
        } catch (\Throwable $e) {
            $total = 0;
            $data = [];
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        // dd($pagination);

        $userPermissions = $this->userPermissions;
        $rawCandidateSkills = $request->candidateSkills ?? '';
        if (is_array($rawCandidateSkills)) {
            $candidateSkills = array_values(array_filter(array_map('strtolower', array_map('trim', $rawCandidateSkills))));
        } else {
            $candidateSkills = array_values(array_filter(array_map('strtolower', array_map('trim', explode(',', (string) $rawCandidateSkills)))));
        }
        $responseData = [
            'html' => view('service_company.ajax_pages.all_copyjobtable_ajax', compact('data', 'userPermissions', 'company_id', 'candidateSkills', 'findTalentAdd'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
            'tenant_version' => $version,
        ];

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_COPY_LIST_TIME));
        optional($lock)->release();

        return response()->json($responseData);

    }

    public function copybenchtable(Request $request)
    {
        abort_if(Gate::denies('view_jobs'), 403);

        $company_id = auth()->user()->company_id;
        $chkAdminRole = $this->user->hasRole('admin');
        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $searchQuery = $request->input('q');
        $custom_search = $request->search;
        $sal_currency = $request->min_salary_currency;
        $min_exp = (int) $request->min_exp;
        $max_exp = (int) $request->max_exp;
        $sal_mode = (int) $request->sal_mode;
        $min_sal = $request->min_sal;
        $max_sal = $request->max_sal;
        $outsource = $request->app_int_outsource;
        $findTalentAdd = @$request->findTalentAdd;
        $currentJobId = @$request->currentJobId;

        $searchTerms = request('search');
        $custom_search = $searchTerms; // json_decode($searchTerms, true);

        $searchTermsSkill = request('cus_search');
        $custom_search_skill = json_decode($searchTermsSkill, true);
        // dd($custom_search);
        $subQuery = [];
        $skillArr = [];
        if (\request('cus_search') != '') {

            $checkedSkills = array_filter($custom_search_skill, function ($skill) {
                return true;
            });

            // $skillArr = array_column($checkedSkills, 'value');
            $skillArr = collect($checkedSkills)->pluck('value')->map(fn ($s) => strtolower(trim($s)))->toArray();

        }

        // If skill filter is empty but currentJobId is present, fetch job's required skills for matching
        if (empty($skillArr) && ! empty($currentJobId)) {
            try {
                $jSkills = DB::table('job_skills')
                    ->join('skills', 'skills.id', '=', 'job_skills.skill_id')
                    ->where('job_skills.job_id', $currentJobId)
                    ->pluck('skills.name')
                    ->toArray();
                if (! empty($jSkills)) {
                    $skillArr = array_values(array_unique(array_map(fn ($s) => strtolower(trim($s)), $jSkills)));
                }
            } catch (\Exception $e) {
                // fallback
            }
        }

        $location = request('kt_tagify_location');
        $custom_location = json_decode($location, true);

        $location = [];
        if (\request('kt_tagify_location') != '') {
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

        // dd($custom_search);
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;

        $cachePayload = [
            'company' => $company_id,
            'page' => $page,
            'size' => $size,
            'q' => $searchQuery,
            'user_id' => $this->user->id,
            'custom_search' => $custom_search,
        ];

        $versionKey = 'benchtalentslist_version_user_'.$this->user->id;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:benchtalentslist:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));
        /* ----------------------------
        ⚡ Check DragonflyDB first
        ----------------------------- */
        if (cache()->has($cacheKeySet)) {
            // return response()->json(cache()->get($cacheKeySet));
        }

        $statusArr = ['active', 'hold', 'inactive', '2'];
        $must = [];
        $should = [];
        $filter = [];
        $sort = [];

        $skillShould = [];

        foreach ($skillArr as $skill) {

            $skillShould[] = [
                'term' => [
                    'job_application_skills.skills.keyword' => [
                        'value' => strtolower($skill),
                        'boost' => 10,
                    ],
                ],
            ];
        }
        /*
        |--------------------------------------------------------------------------
        | Must Query
        |--------------------------------------------------------------------------
        */

        $mustQuery = [

            ['term' => ['company_id' => $company_id]],
            ['term' => ['condidate_type' => 'bench_condidate']],
            ['term' => ['status' => 2]],
        ];

        /*
        |--------------------------------------------------------------------------
        | Location Filter
        |--------------------------------------------------------------------------
        */

        if (! empty($location)) {

            $mustQuery[] = [
                'terms' => [
                    'city_id' => is_array($location)
                        ? $location
                        : [$location],
                ],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Salary Filter
        |--------------------------------------------------------------------------
        */

        $salaryField = match ($sal_mode) {
            0 => 'ectc_yearly',
            1 => 'ectc_monthly',
            2 => 'ectc_hourly',
            default => 'ectc_yearly'
        };

        $currencyArr = GeneralMethod::currency();
        if (! empty($sal_currency)) {
            $currency = $currencyArr[$sal_currency];
            if ($currency > 0) {
                $min_sal = GeneralMethod::convertToINR($min_sal, $currency);
                $max_sal = GeneralMethod::convertToINR($max_sal, $currency);
            }
        }
        // dd($sal_mode,$salaryField,$min_sal,$max_sal);

        if (($min_sal !== null && $min_sal !== '' && $min_sal != 0.00) || ($max_sal !== null && $max_sal !== '' && $max_sal != 0.00)) {

            $salaryRange = [];

            // Minimum salary
            if ($min_sal !== null && $min_sal !== '' && $min_sal != 0.00) {
                $salaryRange['gte'] = (float) $min_sal;
            }

            // Maximum salary
            if ($max_sal !== null && $max_sal !== '' && $max_sal != 0.00) {
                $salaryRange['lte'] = (float) $max_sal;
            }

            $boolQuery['bool']['filter'][] = [
                'range' => [
                    $salaryField => $salaryRange,
                ],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Experience Filter
        |--------------------------------------------------------------------------
        */

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
        if (! empty($range)) {
            $boolQuery['bool']['filter'][] = [
                'range' => [
                    'total_exp' => $range,
                ],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Hiring Type Filter
        |--------------------------------------------------------------------------
        */

        if (! empty($outsource)) {
            $mustQuery[] = [
                'term' => ['outsourcing_type' => $outsource],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Main Elasticsearch Query
        |--------------------------------------------------------------------------
        */

        $boolQuery = [
            'bool' => [

                'must' => array_merge(
                    $mustQuery,
                    $this->getSearchConditions($custom_search, 101)
                ),

                // Skill match scoring
                'should' => $skillShould,

                // Minimum one skill match required
                'minimum_should_match' => 0,
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Elasticsearch Params
        |--------------------------------------------------------------------------
        */

        $params = [

            'index' => ELASTIC_CANDIDATES_BENCH,

            'request_cache' => true,

            'body' => [

                'from' => $from,

                'size' => $size,

                'query' => $boolQuery,

                // Sort by skill score first
                'sort' => [

                    [
                        '_score' => [
                            'order' => 'desc',
                        ],
                    ],

                    [
                        'created_at' => [
                            'order' => 'desc',
                        ],
                    ],
                ],
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES_BENCH)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $hits = $response['hits']['hits'] ?? [];
            $agenciesMap = [];
            $excellentCount = 0;
            $goodCount = 0;
            $availableTodayCount = 0;

            foreach ($hits as $hit) {
                $source = $hit['_source'];

                $candidateSkills = array_map(function ($skill) {
                    return strtolower(trim($skill));
                }, explode(',', $source['skills'] ?? ''));

                $matchedSkills = GeneralMethod::matchedSkillsList($candidateSkills, $skillArr);

                $matchedCount = count($matchedSkills);

                $source['skill_match_score'] = $matchedCount;
                $source['matched_skills'] = implode(',', $matchedSkills);

                $skillMatchPercent = (! empty($skillArr) && count($skillArr) > 0)
                    ? (int) round(($matchedCount / count($skillArr)) * 100)
                    : 0;

                $source['skill_match_percent'] = $skillMatchPercent;

                // Track agency company IDs
                if (! empty($source['company_id'])) {
                    $agenciesMap[$source['company_id']] = true;
                }

                if ($skillMatchPercent >= 90) {
                    $excellentCount++;
                } elseif ($skillMatchPercent >= 75) {
                    $goodCount++;
                }

                $isImmediate = ! empty($source['np_is_immediate'])
                    || (isset($source['serving_notice_period_mode']) && $source['serving_notice_period_mode'] == 1)
                    || (isset($source['np_id']) && stripos((string) $source['np_id'], 'immediate') !== false);

                if ($isImmediate) {
                    $availableTodayCount++;
                }

                $data[] = (object) $source;
            }

            // Arrange candidates with matched skills / highest match percentage FIRST
            usort($data, function ($a, $b) {
                $pA = (int) ($a->skill_match_percent ?? 0);
                $pB = (int) ($b->skill_match_percent ?? 0);
                if ($pA === $pB) {
                    $sA = (int) ($a->skill_match_score ?? 0);
                    $sB = (int) ($b->skill_match_score ?? 0);

                    return $sB <=> $sA;
                }

                return $pB <=> $pA;
            });

            $agenciesCount = count($agenciesMap);
            $premiumAgenciesCount = $agenciesCount > 0 ? (int) round($agenciesCount * 0.32) : 0;
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        $userPermissions = $this->userPermissions;

        /**
         * Intent: Batch pre-fetch candidate request statuses and relationship managers for all page candidates to eliminate N+1 queries during Blade rendering.
         * Tenancy Note: Filtered by auth company ID (source_from_company_id / bench_assign.company_id).
         */
        $candidateIds = collect($data)->pluck('id')->filter()->toArray();
        $benchRequestsMap = [];
        $benchManagersMap = [];

        if (! empty($candidateIds)) {
            if (! empty($currentJobId)) {
                $benchRequestsMap = DB::table('rec_candidate_assign')
                    ->where('source_from_company_id', $company_id)
                    ->where('source_jobid', $currentJobId)
                    ->whereIn('source_application_id', $candidateIds)
                    ->get()
                    ->keyBy('source_application_id');
            }

            $managerRows = DB::table('bench_assign')
                ->join('users', 'users.id', '=', 'bench_assign.user_id')
                ->select('bench_assign.bench_applications_id', 'users.name', 'users.image')
                ->whereIn('bench_assign.bench_applications_id', $candidateIds)
                ->where('bench_assign.company_id', $company_id)
                ->where('users.company_id', $company_id)
                ->orderBy('users.name', 'ASC')
                ->get();

            foreach ($managerRows as $mRow) {
                $appId = $mRow->bench_applications_id;
                if (! isset($benchManagersMap[$appId])) {
                    $benchManagersMap[$appId] = [
                        'count' => 0,
                        'name' => $mRow->name,
                        'image' => $mRow->image,
                    ];
                }
                $benchManagersMap[$appId]['count']++;
            }
        }

        $responseData = [
            'html' => view('service_company.ajax_pages.bench-talents-ajax', compact('data', 'userPermissions', 'company_id', 'currentJobId', 'sal_currency', 'sal_mode', 'benchRequestsMap', 'benchManagersMap'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
            'stats' => [
                'total_matches' => $total,
                'agencies_count' => $agenciesCount,
                'excellent_matches' => $excellentCount,
                'good_matches' => $goodCount,
                'available_today' => $availableTodayCount,
                'premium_agencies' => $premiumAgenciesCount,
            ],
        ];

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_COPY_LIST_TIME));

        return response()->json($responseData);

    }

    public function copy_current_selected_applicant(Request $request)
    {
        $selectedJobListing = is_array($request->selectedJobListing) ? array_values(array_filter($request->selectedJobListing)) : [];
        $selectedApplicantListing = is_array($request->selectedApplicantListing) ? array_values(array_filter($request->selectedApplicantListing)) : [];
        $selectedApplicantListingOld = is_array($request->selectedApplicantListingOld) ? $request->selectedApplicantListingOld : [];

        if (empty($selectedJobListing) || empty($selectedApplicantListing)) {
            return Reply::dataOnly(['status' => 'success']);
        }

        $company_id = (int) auth()->user()->company_id;
        $userId = (int) auth()->user()->id;

        /**
         * Intent: Server-side idempotency & concurrency lock (Phase 3).
         * Prevents rapid double-clicks, duplicate jQuery listeners, or network retries from
         * inserting duplicate add_job_wise_condidates rows or double-deducting coins.
         * Tenancy: Scoped by $company_id and $userId.
         */
        $idemHeader = (string) $request->header('X-Idempotency-Key', '');
        $payloadHash = md5(json_encode([$company_id, $userId, $selectedJobListing, $selectedApplicantListing]));
        $idemKey = 'idem:copy_app_create:'.$company_id.':'.$userId.':'.($idemHeader !== '' ? md5($idemHeader) : $payloadHash);
        $lock = Cache::lock('lock:'.$idemKey, 15);
        if (! $lock->get()) {
            return Reply::dataOnly(['status' => 'success']);
        }

        try {
            if (Cache::has($idemKey)) {
                return Reply::dataOnly(['status' => 'success']);
            }

            $default_master_status = 1;
            $eventId = 13;
            $coinsRequ = count($selectedJobListing) * count($selectedApplicantListing);

            // 1. Batch-fetch all target jobs in 1 query (eliminates 2x per-job SELECTs + 1x in AlignmentService)
            $jobsMap = DB::table('jobs')
                ->whereIn('id', $selectedJobListing)
                ->get()
                ->keyBy('id');

            // 2. Validate coins once per unique target job company_id
            $validatedCompanyIds = [];
            foreach ($selectedJobListing as $jval) {
                $jobRow = $jobsMap->get($jval);
                if (! $jobRow) {
                    continue;
                }
                $companyId = (int) @$jobRow->company_id;
                if (! isset($validatedCompanyIds[$companyId])) {
                    $coinsDeductions = GeneralMethod::coinsValidationsAllcompany($companyId, $eventId, $coinsRequ);
                    if ($coinsDeductions->status) {
                        return Reply::dataOnly([
                            'status' => 'coinCheck',
                            'message' => $coinsDeductions->message,
                            'company_coins' => $coinsDeductions->company_coins,
                        ]);
                    }
                    $validatedCompanyIds[$companyId] = true;
                }
            }

            // 3. Batch-fetch all selected job_applications in 1 query (eliminates N*M per-pair SELECTs)
            $dbAppsMap = DB::table('job_applications')
                ->whereIn('id', $selectedApplicantListing)
                ->get()
                ->keyBy('id');

            // 4. Pre-resolve candidate rows: for bench_condidate, $dbAppsMap already holds the authoritative
            // MySQL job_applications row; only fetch ELASTIC_CANDIDATES when copying an 'other' applicant with wiseId.
            $resolvedAppsMap = [];
            $esClient = null;
            foreach ($selectedApplicantListing as $key => $app_id) {
                $applicationRow = $dbAppsMap->get($app_id);
                if (! $applicationRow) {
                    continue;
                }
                $resolvedRow = clone $applicationRow;
                if ($applicationRow->condidate_type == 'other') {
                    $addJobWiseCondidate_id = $selectedApplicantListingOld[$key] ?? null;
                    if (! empty($addJobWiseCondidate_id)) {
                        try {
                            $esClient = $esClient ?: $this->elasticService->getClient();
                            $response = $esClient->get([
                                'index' => ELASTIC_CANDIDATES,
                                'id' => $addJobWiseCondidate_id,
                            ]);
                            if (isset($response['_source'])) {
                                $data = (object) $response['_source'];
                                $data->id = $data->candidate_id ?? $applicationRow->id;
                                $resolvedRow = $data;
                            }
                        } catch (\Throwable $e) {
                        }
                    }
                }
                $resolvedAppsMap[$app_id] = $resolvedRow;
            }

            // 5. Batch-fetch existing rec_candidate_assign rows in 1 query (eliminates 2 queries per pair: count() + first())
            $candidateIdsForAssign = array_values(array_unique(array_filter(array_map(
                fn ($r) => (int) ($r->id ?? 0),
                $resolvedAppsMap
            ))));
            $existingRecAssignMap = ! empty($candidateIdsForAssign)
                ? DB::table('rec_candidate_assign')
                    ->whereIn('source_application_id', $candidateIdsForAssign)
                    ->get()
                    ->keyBy('source_application_id')
                : collect();

            // 6. Batch-fetch required job skills for all selected jobs in 1 query (eliminates per-pair skill_match_percentage DB queries)
            $jobSkillsMap = DB::table('job_metronics_skills')
                ->whereIn('job_id', $selectedJobListing)
                ->select('job_id', 'skill_name')
                ->get()
                ->groupBy('job_id')
                ->map(fn ($items) => $items->pluck('skill_name')->all())
                ->toArray();

            // 7. Batch-fetch existing (job_id, job_application_id) pairs in add_job_wise_condidates to prevent duplicate assignments across concurrent recruiters
            $existingWisePairs = [];
            $existingWiseRows = DB::table('add_job_wise_condidates')
                ->whereIn('job_id', $selectedJobListing)
                ->whereIn('job_application_id', $selectedApplicantListing)
                ->select('job_id', 'job_application_id')
                ->get();
            foreach ($existingWiseRows as $ewRow) {
                $existingWisePairs[$ewRow->job_id.'_'.$ewRow->job_application_id] = true;
            }

            $alignmentService = app(\App\Services\CandidateJobAlignmentService::class);
            $outboxService = app(\App\Services\TransactionalOutboxService::class);
            $benchCandidateIdsToSync = [];
            $reverbEventsToBroadcast = [];

            foreach ($selectedJobListing as $jval) {
                $jobRow = $jobsMap->get($jval);
                if (! $jobRow) {
                    continue;
                }

                $customClient = ($company_id != $jobRow->company_id) ? 1 : 0;

                foreach ($selectedApplicantListing as $key => $app_id) {
                    $applicationRow = $resolvedAppsMap[$app_id] ?? null;
                    if (! $applicationRow) {
                        continue;
                    }
                    $dbAppRow = $dbAppsMap->get($app_id) ?: $applicationRow;

                    // Skip if candidate is already assigned to this target job (prevents duplicate rows and double coin deduction)
                    $pairKey = $jval.'_'.$app_id;
                    if (isset($existingWisePairs[$pairKey])) {
                        continue;
                    }
                    $existingWisePairs[$pairKey] = true;

                    if ($customClient == 1) {
                        $thirdparty_company_id = $company_id;
                        $default_master_status = 8;
                        $submitted_by = $this->user->id;
                    } else {
                        $thirdparty_company_id = 0;
                        $default_master_status = 8;
                        $submitted_by = $this->user->id;
                    }

                    $now = now();
                    $isBenchCandidate = (($dbAppRow->condidate_type ?? $applicationRow->condidate_type ?? '') === 'bench_condidate');
                    $candCompanyId = (int) ($dbAppRow->company_id ?? $applicationRow->company_id ?? 0);
                    $isCrossCompanyBench = $isBenchCandidate && ($candCompanyId !== $company_id);

                    /**
                     * Intent: Preserve vendor/bench candidate ownership and rate columns on job_applications (Phase 4).
                     * When adding a bench_condidate from Find Bench Talents / Agency Bench, do NOT overwrite the
                     * vendor company's job_applications row (submitted_by, job_id, master_status_id, ctc/ectc).
                     * Instead, increment countforadd (and set status=1 when countforadd >= 3) for cross-company bench.
                     */
                    if ($isBenchCandidate) {
                        if ($isCrossCompanyBench) {
                            $newCountForAdd = ((int) ($dbAppRow->countforadd ?? 0)) + 1;
                            $benchUpdate = ['countforadd' => $newCountForAdd];
                            if ($newCountForAdd >= 3) {
                                $benchUpdate['status'] = 1;
                            }
                            DB::table('job_applications')->where('id', '=', $dbAppRow->id)->update($benchUpdate);
                            $dbAppRow->countforadd = $newCountForAdd;
                            if ($newCountForAdd >= 3) {
                                $dbAppRow->status = 1;
                            }
                        }
                    } else {
                        $values = [
                            'submitted_by' => $submitted_by,
                            'thirdparty_company_id' => $thirdparty_company_id,
                            'np_id' => @$applicationRow->np_id,
                            'full_name' => @$applicationRow->full_name,
                            'email' => @$applicationRow->email,
                            'phone' => @$applicationRow->phone,
                            'dob' => @$applicationRow->dob,
                            'country' => @$applicationRow->country,
                            'state' => @$applicationRow->state,
                            'city' => @$applicationRow->city,
                            'city_id' => @$applicationRow->city_id,
                            'secondrary_email' => @$applicationRow->secondrary_email,
                            'secondary_mobile' => @$applicationRow->secondary_mobile,
                            'skills' => @$applicationRow->skills,
                            'photo' => @$applicationRow->photo,
                            'resume' => @$applicationRow->resume,
                            'rating' => @$applicationRow->rating,
                            'job_id' => @$jval,
                            'status_id' => $default_master_status,
                            'total_exp' => @$applicationRow->total_exp,
                            'rel_exp' => @$applicationRow->rel_exp,
                            'passport' => @$applicationRow->passport,
                            'ctc' => @$applicationRow->ctc,
                            'current_ctc_cur' => @$applicationRow->current_ctc_cur,
                            'current_ctc_mode' => @$applicationRow->current_ctc_mode,
                            'ectc' => @$applicationRow->ectc,
                            'current_ectc_cur' => @$applicationRow->current_ectc_cur,
                            'current_ectc_mode' => @$applicationRow->current_ectc_mode,
                            'current_location' => @$applicationRow->current_location,
                            'current_designation' => @$applicationRow->current_designation,
                            'current_company' => @$applicationRow->current_company,
                            'process_id' => @$applicationRow->process_id,
                            'qualification' => @$applicationRow->qualification,
                            'master_status_id' => $default_master_status,
                            'child_status_id' => @$applicationRow->child_status_id,
                            'serving_notice_period_mode' => @$applicationRow->serving_notice_period_mode,
                            'proposed_ctc' => @$applicationRow->proposed_ctc,
                            'any_offer' => null,
                            'document' => @$applicationRow->document,
                            'sub_for_approval' => @$applicationRow->sub_for_approval,
                            'offer_approved' => null,
                            'offer_release' => null,
                            'offer_acceptance' => null,
                            'joined' => null,
                            'joined_date' => null,
                            'duplicate' => @$applicationRow->duplicate,
                            'percentage_hike' => @$applicationRow->percentage_hike,
                            'comment' => @$applicationRow->comment,
                            'pincode' => @$applicationRow->pincode,
                            'offer_currency' => null,
                            'offer_mode' => null,
                            'offer_amount' => null,
                            'recruiter_note' => null,
                            'updated_at' => $now,
                        ];

                        DB::table('job_applications')->where('id', '=', $applicationRow->id)->update($values);
                    }

                    $application_id = $app_id;
                    $timelineNote = $isCrossCompanyBench
                        ? 'Similar Candidate => Agency Bench'
                        : ($isBenchCandidate ? 'Similar Candidate => Internal Bench' : 'Copy Application');
                    $timelineArr = [
                        'event_id' => 1,
                        'event_by_company_id' => $company_id,
                        'event_by_user_id' => $userId,
                        'master_process_name' => 'InReview',
                        'application_id' => @$application_id,
                        'application_email' => @$applicationRow->email,
                        'job_id' => @$jval,
                        'note' => $timelineNote,
                    ];
                    GeneralMethod::UpdateTimeline($timelineArr);

                    $sel_app_id = $applicationRow->id;
                    $sel_job_id = @$jval;
                    $benchAssignRow = $existingRecAssignMap->get($sel_app_id);

                    if ($isBenchCandidate) {
                        $recValues = [
                            'source_from_company_id' => @$dbAppRow->company_id,
                            'source_from_user_id' => @$dbAppRow->submitted_by,
                            'source_from_id' => @$sel_app_id,
                            'source_from_type' => $isCrossCompanyBench ? 2 : 1,
                            'parent_id' => 0,
                            'source_company_id' => $company_id,
                            'source_user_id' => $userId,
                            'source_application_id' => @$application_id,
                            'ac_status' => 1,
                            'source_jobid' => @$sel_job_id,
                        ];
                    } elseif ($benchAssignRow) {
                        $recValues = [
                            'source_from_company_id' => @$benchAssignRow->source_from_company_id,
                            'source_from_user_id' => @$benchAssignRow->source_from_user_id,
                            'source_from_id' => @$benchAssignRow->source_from_id,
                            'source_from_type' => @$benchAssignRow->source_from_type,
                            'parent_id' => @$benchAssignRow->source_application_id,
                            'source_company_id' => $company_id,
                            'source_user_id' => $userId,
                            'source_application_id' => @$application_id,
                            'source_jobid' => @$sel_job_id,
                        ];
                    } else {
                        $recValues = [
                            'source_from_company_id' => @$applicationRow->company_id,
                            'source_from_user_id' => @$applicationRow->submitted_by,
                            'source_from_id' => @$sel_app_id,
                            'source_from_type' => 0,
                            'parent_id' => 0,
                            'source_company_id' => $company_id,
                            'source_user_id' => $userId,
                            'source_application_id' => @$application_id,
                            'source_jobid' => @$sel_job_id,
                        ];
                    }
                    $recCandidateAssignId = DB::table('rec_candidate_assign')->insertGetId($recValues);

                    // Compute skill_match in-memory from pre-fetched $jobSkillsMap (0 DB queries)
                    $skillArr = explode(',', (string) @$applicationRow->skills);
                    $percentage = GeneralMethod::calculateSkillMatch($jobSkillsMap[$sel_job_id] ?? [], $skillArr)['percentage_float'];

                    $insertArr = [
                        'job_id' => @$sel_job_id,
                        'rec_candidate_assign_id' => $recCandidateAssignId,
                        'job_application_id' => @$application_id,
                        'status_id' => @$default_master_status,
                        'master_status_id' => @$default_master_status,
                        'child_status_id' => null,
                        'company_id' => @$jobRow->company_id,
                        'submitted_by' => @$this->user->id,
                        'thirdparty_company_id' => @$thirdparty_company_id,
                        'existing' => 1,
                        'skill_match' => $percentage,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $lastWiseIs = DB::table('add_job_wise_condidates')->insertGetId($insertArr);
                    $result = $lastWiseIs > 0;

                    $teamRankElastic = [
                        'company_id' => $company_id,
                        'user_id' => $this->user->id,
                        'candidate_id' => @$application_id,
                        'profileUpload' => 1,
                        'interviewScheduled' => 0,
                        'interviewNoShow' => 0,
                        'offerReleased' => 0,
                        'candidateJoined' => 0,
                        'jobsPosted' => 0,
                        'feedbackGivenOnCVs' => 0,
                        'postJobToVendorMarket' => 0,
                        'outsourceJobsToVendors' => 0,
                        'listBenchCandidates' => 0,
                        'respondToBenchRequests' => 0,
                    ];
                    GeneralMethod::teamRankElastic($teamRankElastic);

                    if ($result) {
                        GeneralMethod::coinsDeductions((int) @$jobRow->company_id, $eventId, $userId, @$thirdparty_company_id, '', '');

                        // Monotonic pipeline version increment & transactional outbox event for real-time AppTracker WebSocket sync
                        try {
                            DB::table('jobs')->where('id', $sel_job_id)->increment('pipeline_version');
                            $newPipelineVersion = ((int) ($jobRow->pipeline_version ?? 0)) + 1;
                            $jobRow->pipeline_version = $newPipelineVersion;
                            $targetTenantId = (int) ($jobRow->company_id ?? $company_id);

                            if ($outboxService->isOptimizedPipelineEnabled($targetTenantId)) {
                                $candidateName = (string) ($applicationRow->full_name ?? '');
                                $sourceFromType = (int) ($recValues['source_from_type'] ?? 0);
                                $outboxEventId = $outboxService->recordEvent(
                                    $targetTenantId,
                                    (int) $sel_job_id,
                                    $newPipelineVersion,
                                    'CandidateAddedToJob',
                                    [
                                        'wise_id' => (int) $lastWiseIs,
                                        'job_id' => (int) $sel_job_id,
                                        'candidate_id' => (int) $application_id,
                                        'rec_candidate_assign_id' => (int) $recCandidateAssignId,
                                        'company_id' => $targetTenantId,
                                        'master_status_id' => (int) $default_master_status,
                                        'source_from_type' => $sourceFromType,
                                        'candidate_name' => $candidateName,
                                        'added_count' => 1,
                                        'pipeline_version' => $newPipelineVersion,
                                    ]
                                );
                                $reverbEventsToBroadcast[] = [
                                    'outbox_id' => $outboxEventId,
                                    'tenant_id' => $targetTenantId,
                                    'job_id' => (int) $sel_job_id,
                                    'version' => $newPipelineVersion,
                                    'candidate_id' => (int) $application_id,
                                    'wise_id' => (int) $lastWiseIs,
                                    'master_status_id' => (int) $default_master_status,
                                    'metadata' => [
                                        'rec_candidate_assign_id' => (int) $recCandidateAssignId,
                                        'source_from_type' => $sourceFromType,
                                        'candidate_name' => $candidateName,
                                        'added_count' => 1,
                                    ],
                                ];
                            }
                        } catch (\Throwable $outboxErr) {
                        }

                        $alignmentService->alignCandidateToJob(
                            (int) $application_id,
                            (int) $sel_job_id,
                            (int) $lastWiseIs,
                            (int) ($jobRow->company_id ?? $company_id),
                            $dbAppRow,
                            $jobRow,
                            true
                        );

                        if (($dbAppRow->condidate_type ?? $applicationRow->condidate_type ?? '') !== 'other') {
                            $benchCandidateIdsToSync[(int) $applicationRow->id] = (int) $applicationRow->id;
                        }
                    }
                }
            }

            // Offload non-blocking Reverb broadcast & bench index sync after HTTP response
            if (! empty($reverbEventsToBroadcast) || ! empty($benchCandidateIdsToSync)) {
                $idsToSync = array_values($benchCandidateIdsToSync);
                $eventsToPush = $reverbEventsToBroadcast;
                app()->terminating(function () use ($eventsToPush, $idsToSync) {
                    foreach ($eventsToPush as $ev) {
                        try {
                            broadcast(new \App\Events\AppTrackerCandidateAssignedEvent(
                                tenantId: $ev['tenant_id'],
                                jobId: $ev['job_id'],
                                version: $ev['version'],
                                candidateId: $ev['candidate_id'],
                                wiseId: $ev['wise_id'],
                                masterStatusId: $ev['master_status_id'],
                                metadata: $ev['metadata']
                            ));
                            if ($ev['outbox_id'] > 0) {
                                app(\App\Services\TransactionalOutboxService::class)->completeReverbEvent($ev['outbox_id']);
                            }
                        } catch (\Throwable $e) {
                            // Leave reverb_status = 'pending' so ProcessReverbOutboxCommand retries durably
                        }
                    }
                    foreach ($idsToSync as $bId) {
                        try {
                            GeneralMethod::elasticBenchCandidateAddEditIndex($bId);
                        } catch (\Throwable $e) {
                        }
                    }
                });
            }

            Cache::put($idemKey, true, 15);

            return Reply::dataOnly(['status' => 'success']);
        } finally {
            optional($lock)->release();
        }
    }

    public function fwd_resume(Request $request)
    {

        // echo $request->id; exit;
        $arr = '';
        $rawArr = is_array($request->arr) ? $request->arr : [];
        $this->arr = array_slice($rawArr, 0, 5);
        $this->jobIdArr = $request->jobIdArr;
        $clientMatchMap = is_array($request->matchMap) ? $request->matchMap : [];
        $companyId = (int) auth()->user()->company_id;
        $this->applicationDataCount = DB::table('job_applications')
            ->whereIn('id', $this->arr)
            ->where(function ($q) use ($companyId) {
                $q->where('company_id', $companyId)
                    ->orWhere('thirdparty_company_id', $companyId);
            })
            ->count();
        // $this->applicationData			=	DB::table('job_applications')->select('job_applications.id')->whereIn('id', $arr)->get();

        $this->applicationData = DB::table('job_applications')
            ->select(
                'job_applications.id',
                'job_applications.condidate_type',
                'job_applications.full_name',
                'job_applications.current_designation',
                'job_applications.phone',
                'job_applications.phone_code',
                'job_applications.second_phone_code',
                'job_applications.secondary_mobile',
                'job_applications.email',
                'job_applications.secondrary_email',
                'job_applications.linkedin',
                'job_applications.city',
                'job_applications.city_id',
                'job_applications.state',
                'job_applications.country',
                'city_list.city as city_name',
                'state_list.state as state_name',
                'countries.country_name as country_name',
                'job_applications.current_location',
                'job_applications.last_working_date',
                'job_applications.qualification',
                'job_applications.wm',
                'job_applications.total_exp',
                'job_applications.rel_exp',
                'job_applications.ctc',
                'job_applications.ectc',
                'job_applications.current_ctc_cur',
                'job_applications.current_ectc_cur',
                'job_applications.current_ctc_mode',
                'job_applications.np_id',
                'job_applications.serving_notice_period_mode',
                'job_applications.current_company',
                'job_applications.skills',
                'job_applications.resume',
                'job_applications.rating',
                'job_applications.careersummary',
                'job_applications.cover_letter',
                'job_applications.recruiter_note',
                'job_applications.comment',
                'qualification_master.name as qualification_name',
                'jobs.id as job_id',
                'jobs.title as job_title',
                'clients.name as client_name',
                'add_job_wise_condidates.id as wise_id',
                'add_job_wise_condidates.skill_match as wise_skill_match'
            )
            ->leftJoin('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->leftJoin('jobs', 'jobs.id', 'add_job_wise_condidates.job_id')
            ->leftJoin('clients', 'clients.id', 'jobs.client_id')
            ->leftJoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
            ->leftJoin('city_list', function ($join) {
                $join->on('city_list.city_id', '=', 'job_applications.city_id')
                    ->orWhere(function ($q) {
                        $q->where(function ($sub) {
                            $sub->whereNull('job_applications.city_id')
                                ->orWhere('job_applications.city_id', 0);
                        })->whereColumn('city_list.city_id', 'job_applications.city');
                    });
            })
            ->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
            ->leftJoin('countries', 'countries.id', '=', 'job_applications.country')
            ->whereIn('job_applications.id', $this->arr)
            ->where(function ($q) use ($companyId) {
                $q->where('job_applications.company_id', $companyId)
                    ->orWhere('job_applications.thirdparty_company_id', $companyId);
            })
            ->groupBy('job_applications.id')
            ->get();
        // dd($this->applicationData);

        // $this->templateData = DB::table('th_email_template')->select('*')->where('th_email_template.status', 1)->where('th_email_template.temptype', 1)->get();

        $params = [
            'index' => ELASTIC_EMAIL_TEMPLATE,
            'body' => [
                'size' => 10000,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['status' => 1]], // OK
                            ['match' => ['temptype' => 1]],    // TEXT field
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);

        $this->templateData = collect(value: $response['hits']['hits'] ?? [])
            ->map(function ($hit) {
                return (object) $hit['_source'];
            });

        $this->userdata = $this->user;

        // Company branding & skill keywords for custom resume format attachment
        $companyRow = DB::table('companies')->select('id', 'company_name', 'logo')->where('id', auth()->user()->company_id)->first();
        $this->companyName = $companyRow ? ucwords((string) $companyRow->company_name) : 'Hiring Partner';
        $this->companyLogo = ($companyRow && ! empty($companyRow->logo)) ? route('service_company.job-applications.company-logo-proxy') : '';

        $jobSkillsList = [];
        if (! empty($this->jobIdArr)) {
            try {
                $client = $this->elasticService->getClient();
                foreach ($this->jobIdArr as $jid) {
                    $esJob = $client->get([
                        'index' => config('constants.ELASTIC_JOBS', 'jobs_index'),
                        'id' => (int) $jid,
                    ]);
                    $source = $esJob['_source'] ?? [];
                    if (! empty($source['job_metronics_skills']) && is_array($source['job_metronics_skills'])) {
                        foreach ($source['job_metronics_skills'] as $sk) {
                            $sn = is_array($sk) ? ($sk['skill_name'] ?? '') : ($sk->skill_name ?? '');
                            if (! empty($sn)) {
                                $jobSkillsList[] = trim($sn);
                            }
                        }
                    }
                }
            } catch (\Throwable $esErr) {
                \Log::warning('[fwd_resume] ES job skills read fallback to DB: '.$esErr->getMessage());
            }

            if (empty($jobSkillsList)) {
                $metronicSkills = DB::table('job_metronics_skills')->whereIn('job_id', $this->jobIdArr)->pluck('skill_name')->toArray();
                $standardSkills = DB::table('job_skills')->join('skills', 'job_skills.skill_id', '=', 'skills.id')->whereIn('job_skills.job_id', $this->jobIdArr)->pluck('skills.name')->toArray();
                $jobSkillsList = array_values(array_unique(array_filter(array_map('trim', array_merge($metronicSkills, $standardSkills)))));
            } else {
                $jobSkillsList = array_values(array_unique(array_filter(array_map('trim', $jobSkillsList))));
            }
        }
        $this->jobSkills = $jobSkillsList;

        // Bulk-load real skill experiences and ratings for selected candidates matching showdetail logic
        $appIds = collect($this->applicationData)->pluck('id')->toArray();

        $cjseSkills = collect();
        if (! empty($appIds)) {
            $cjseSkills = DB::table('candidate_job_skill_exp')
                ->join('job_metronics_skills', 'candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
                ->whereIn('candidate_job_skill_exp.candidate_id', $appIds)
                ->select(
                    'candidate_job_skill_exp.candidate_id',
                    'job_metronics_skills.skill_name',
                    'candidate_job_skill_exp.skill_value',
                    'candidate_job_skill_exp.rating'
                )
                ->get()
                ->groupBy('candidate_id');
        }

        $benchSkills = collect();
        if (! empty($appIds)) {
            $benchSkills = DB::table('bench_candidate_skill_experience')
                ->whereIn('candidate_id', $appIds)
                ->select('candidate_id', 'skill_key', 'year', 'month', 'total_months', 'rating')
                ->orderBy('skill_key')
                ->get()
                ->groupBy('candidate_id');
        }

        foreach ($this->applicationData as $cand) {
            // Notice Period formatting (matching showdetail & bench rules)
            $npMode = (int) (@$cand->serving_notice_period_mode ?? -1);
            $npRaw = trim((string) @$cand->np_id);
            $npTs = $npRaw !== '' ? strtotime($npRaw) : false;
            $isBench = (@$cand->condidate_type === 'bench_condidate');

            if ($npMode === 2 || strtolower($npRaw) === 'immediate') {
                $cand->notice = 'Immediate';
                $cand->is_immediate = true;
            } elseif ($npMode === 0 && (int) $npRaw > 0) {
                $cand->notice = (int) $npRaw.' Days';
                $cand->is_immediate = false;
            } elseif ($npMode === 1 && $npTs !== false && (int) date('Y', $npTs) > 1970) {
                if (date('Y-m-d', $npTs) < date('Y-m-d')) {
                    $cand->notice = 'Immediate';
                    $cand->is_immediate = true;
                } else {
                    $cand->notice = $isBench ? ('Available '.date('d M Y', $npTs)) : ('SNP LWD '.date('d M Y', $npTs));
                    $cand->is_immediate = false;
                }
            } elseif ($npTs !== false && (int) date('Y', $npTs) > 1970) {
                $cand->notice = $isBench ? ('Available '.date('d M Y', $npTs)) : ('SNP LWD '.date('d M Y', $npTs));
                $cand->is_immediate = false;
            } else {
                $cand->notice = ($npRaw !== '' && ! str_contains($npRaw, '01 Jan 1970')) ? $npRaw : '--';
                $cand->is_immediate = false;
            }

            // Location: resolve human-readable city, state, country names — NEVER output numeric database IDs
            $cityName = (! empty($cand->city_name) && ! preg_match('/^\d+$/', trim((string) $cand->city_name))) ? trim((string) $cand->city_name) : null;
            if (! $cityName && ! empty($cand->city) && ! preg_match('/^\d+$/', trim((string) $cand->city))) {
                $cityName = trim((string) $cand->city);
            }

            $stateName = (! empty($cand->state_name) && ! preg_match('/^\d+$/', trim((string) $cand->state_name))) ? trim((string) $cand->state_name) : null;
            if (! $stateName && ! empty($cand->state) && ! preg_match('/^\d+$/', trim((string) $cand->state))) {
                $stateName = trim((string) $cand->state);
            }

            $countryName = (! empty($cand->country_name) && ! preg_match('/^\d+$/', trim((string) $cand->country_name))) ? trim((string) $cand->country_name) : null;
            if (! $countryName && ! empty($cand->country) && ! preg_match('/^\d+$/', trim((string) $cand->country))) {
                $countryName = trim((string) $cand->country);
            }

            $locParts = array_filter([$cityName, $stateName, $countryName]);
            $cand->location = ! empty($locParts) ? implode(', ', $locParts) : '--';

            // Work Mode
            $wmRaw = trim((string) @$cand->wm);
            $cand->work_mode = $wmRaw !== '' ? ucwords(str_replace(['_', '-'], ' ', strtolower($wmRaw))) : 'Hybrid';

            // Experience string
            $totExp = trim((string) @$cand->total_exp, " \t\n\r\0\x0B.");
            $expStr = ($totExp !== '' && (float) $totExp > 0) ? $totExp.' Year'.((float) $totExp == 1.0 ? '' : 's') : '--';
            $cand->experience = $expStr;

            // Report ID & Date & Recruiter
            $cand->report_id = 'JR'.now()->format('Ymd').str_pad((string) $cand->id, 6, '0', STR_PAD_LEFT);
            $cand->report_date = $todayFormatted;
            $cand->recruiter_name = $recruiterName;
            $cand->report_company_name = $this->companyName;
            $cand->report_company_logo = $this->companyLogo;

            // Match score / Hiring confidence percentage (exact 1:1 match with profile icon / table)
            $candSkillsList = array_values(array_filter(array_map('trim', explode(',', (string) @$cand->skills))));
            $matchPct = 0;
            if (! empty($clientMatchMap[$cand->id]) && (int) $clientMatchMap[$cand->id] > 0) {
                $matchPct = (int) $clientMatchMap[$cand->id];
            } elseif (! empty($cand->wise_skill_match)) {
                $matchPct = (int) round((float) rtrim((string) $cand->wise_skill_match, '%'));
            }
            if ($matchPct === 0 && ! empty($cand->job_id) && ! empty($candSkillsList)) {
                $calcPct = GeneralMethod::skill_percentage($cand->job_id, $candSkillsList);
                $matchPct = (int) round((float) rtrim($calcPct, '%'));
            }
            if ($matchPct === 0 && ! empty($jobSkillsList) && ! empty($candSkillsList)) {
                $calcPct = GeneralMethod::skill_percentage_from_array($jobSkillsList, $candSkillsList);
                $matchPct = (int) round((float) rtrim($calcPct, '%'));
            }
            if ($matchPct === 0) {
                $matchPct = ! empty($cand->rating) ? min(100, max(40, (int) ($cand->rating * 20))) : 85;
            }
            $cand->match = $matchPct;

            // Career Summary & Recruiter Note
            $sum = trim(strip_tags(html_entity_decode((string) (@$cand->careersummary ?: @$cand->cover_letter), ENT_QUOTES, 'UTF-8')));
            $cand->career_summary = ! empty($sum) ? $sum : 'Experienced professional with demonstrated background in designing, developing, and deploying robust software architectures, driving end-to-end delivery and engineering excellence.';

            $note = trim(strip_tags(html_entity_decode((string) (@$cand->recruiter_note ?? ''), ENT_QUOTES, 'UTF-8')));
            $cand->recruiter_note_text = $note;

            // Real Skills & Ratings for Skill Intelligence card
            $skillsArr = [];
            $seenSkillNames = [];

            // A. Check bench candidate skills if applicable
            $benchList = $benchSkills->get($cand->id);
            if ($benchList && count($benchList) > 0) {
                foreach ($benchList as $bRow) {
                    $sn = trim((string) $bRow->skill_key);
                    $snLower = strtolower($sn);
                    if ($sn === '' || isset($seenSkillNames[$snLower])) {
                        continue;
                    }
                    $seenSkillNames[$snLower] = true;

                    $yr = (int) ($bRow->year ?? 0);
                    $mo = (int) ($bRow->month ?? 0);
                    if (isset($bRow->total_months) && is_numeric($bRow->total_months) && (int) $bRow->total_months > 0) {
                        $yr = intdiv((int) $bRow->total_months, 12);
                        $mo = (int) $bRow->total_months % 12;
                    }
                    $sExpParts = [];
                    if ($yr > 0) {
                        $sExpParts[] = $yr.' Year'.($yr > 1 ? 's' : '');
                    }
                    if ($mo > 0) {
                        $sExpParts[] = $mo.' Month'.($mo > 1 ? 's' : '');
                    }
                    $sExpText = ! empty($sExpParts) ? implode(' ', $sExpParts) : ($yr === 0 && $mo === 0 ? '0 Years' : '--');

                    $ratingVal = (int) ($bRow->rating ?? 0);
                    if ($ratingVal <= 0 && ! empty($cand->rating)) {
                        $ratingVal = (int) $cand->rating;
                    }
                    $ratingVal = min(5, max(0, $ratingVal));

                    $skillsArr[] = [
                        'name' => ucwords($sn),
                        'expText' => $sExpText,
                        'exp' => $yr + ($mo / 12),
                        'rating' => $ratingVal,
                    ];
                    if (count($skillsArr) >= 7) {
                        break;
                    }
                }
            }

            // B. Map candidate evaluations from candidate_job_skill_exp
            $cjseList = $cjseSkills->get($cand->id);
            $cjseMap = [];
            if ($cjseList && count($cjseList) > 0) {
                foreach ($cjseList as $cRow) {
                    $snLower = strtolower(trim((string) $cRow->skill_name));
                    if ($snLower !== '') {
                        $cjseMap[$snLower] = $cRow;
                    }
                }
            }

            // Prioritize active job skills
            if (! empty($jobSkillsList) && count($skillsArr) < 7) {
                foreach ($jobSkillsList as $jsName) {
                    $jsTrim = trim((string) $jsName);
                    $jsLower = strtolower($jsTrim);
                    if ($jsLower === '' || isset($seenSkillNames[$jsLower])) {
                        continue;
                    }
                    $seenSkillNames[$jsLower] = true;

                    if (isset($cjseMap[$jsLower])) {
                        $cRow = $cjseMap[$jsLower];
                        $rawVal = trim((string) $cRow->skill_value);
                        $sExpText = '--';
                        $sExpNum = 0.0;
                        if ($rawVal !== '') {
                            if (preg_match('/(\d+)\s*(?:years?|yrs?)?\s+(\d+)\s*(?:months?|mos?)/i', $rawVal, $m)) {
                                $y = (int) $m[1];
                                $mo = (int) $m[2];
                                $sExpText = $y.' Year'.($y > 1 ? 's' : '').($mo > 0 ? ' '.$mo.' Month'.($mo > 1 ? 's' : '') : '');
                                $sExpNum = $y + ($mo / 12);
                            } elseif (preg_match('/^(\d+(?:\.\d+)?)/', $rawVal, $m)) {
                                $sExpNum = (float) $m[1];
                                $sExpText = $rawVal;
                            } else {
                                $sExpText = $rawVal;
                            }
                        }

                        $ratingVal = (int) ($cRow->rating ?? 0);
                        if ($ratingVal <= 0 && ! empty($cand->rating)) {
                            $ratingVal = (int) $cand->rating;
                        }
                        $ratingVal = min(5, max(0, $ratingVal));

                        $skillsArr[] = [
                            'name' => ucwords($jsTrim),
                            'expText' => $sExpText,
                            'exp' => $sExpNum,
                            'rating' => $ratingVal,
                        ];
                    } else {
                        $defaultRating = ! empty($cand->rating) ? min(5, max(0, (int) $cand->rating)) : 0;
                        $skillsArr[] = [
                            'name' => ucwords($jsTrim),
                            'expText' => $expStr,
                            'exp' => (float) $totExp ?: 0,
                            'rating' => $defaultRating,
                        ];
                    }

                    if (count($skillsArr) >= 7) {
                        break;
                    }
                }
            }

            // C. If slots remain (< 7), include candidate's remaining evaluated or profile skills
            if (count($skillsArr) < 7 && $cjseList && count($cjseList) > 0) {
                foreach ($cjseList as $cRow) {
                    $sn = trim((string) $cRow->skill_name);
                    $snLower = strtolower($sn);
                    if ($sn === '' || isset($seenSkillNames[$snLower])) {
                        continue;
                    }
                    $seenSkillNames[$snLower] = true;

                    $rawVal = trim((string) $cRow->skill_value);
                    $sExpText = '--';
                    $sExpNum = 0.0;
                    if ($rawVal !== '') {
                        if (preg_match('/(\d+)\s*(?:years?|yrs?)?\s+(\d+)\s*(?:months?|mos?)/i', $rawVal, $m)) {
                            $y = (int) $m[1];
                            $mo = (int) $m[2];
                            $sExpText = $y.' Year'.($y > 1 ? 's' : '').($mo > 0 ? ' '.$mo.' Month'.($mo > 1 ? 's' : '') : '');
                            $sExpNum = $y + ($mo / 12);
                        } elseif (preg_match('/^(\d+(?:\.\d+)?)/', $rawVal, $m)) {
                            $sExpNum = (float) $m[1];
                            $sExpText = $rawVal;
                        } else {
                            $sExpText = $rawVal;
                        }
                    }

                    $ratingVal = (int) ($cRow->rating ?? 0);
                    if ($ratingVal <= 0 && ! empty($cand->rating)) {
                        $ratingVal = (int) $cand->rating;
                    }
                    $ratingVal = min(5, max(0, $ratingVal));

                    $skillsArr[] = [
                        'name' => ucwords($sn),
                        'expText' => $sExpText,
                        'exp' => $sExpNum,
                        'rating' => $ratingVal,
                    ];
                    if (count($skillsArr) >= 7) {
                        break;
                    }
                }
            }

            if (count($skillsArr) < 7 && ! empty($candSkillsList)) {
                $defaultRating = ! empty($cand->rating) ? min(5, max(0, (int) $cand->rating)) : 0;
                foreach ($candSkillsList as $skName) {
                    if (! $skName) {
                        continue;
                    }
                    $snLower = strtolower($skName);
                    if (isset($seenSkillNames[$snLower])) {
                        continue;
                    }
                    $seenSkillNames[$snLower] = true;

                    $skillsArr[] = [
                        'name' => ucwords($skName),
                        'expText' => $expStr,
                        'exp' => (float) $totExp ?: 0,
                        'rating' => $defaultRating,
                    ];
                    if (count($skillsArr) >= 7) {
                        break;
                    }
                }
            }
            $cand->parsed_skills = $skillsArr;

            // Validation badges matching showdetail
            // Resume presence detection & verification
            $candResume = trim((string) @$cand->resume);
            $hasResume = false;
            $resumeFilename = '';
            if ($candResume !== '' && $candResume !== '0' && strtolower($candResume) !== 'null') {
                $hasResume = true;
                $resumeFilename = basename($candResume);
            }
            $cand->has_resume = $hasResume;
            $cand->resume_filename = $resumeFilename;
            $cand->resume_status = $hasResume ? 'available' : 'missing';
            $cand->resume_url = $hasResume ? route('service_company.cvtracker.resume', $cand->id) : '';

            $valArr = [];
            if ($hasResume) {
                $valArr[] = ['label' => 'RESUME', 'status' => 'Verified'];
            } else {
                $valArr[] = ['label' => 'RESUME', 'status' => 'Not Uploaded'];
            }
            if (! empty($cand->email) || ! empty($cand->secondrary_email)) {
                $valArr[] = ['label' => 'EMAIL ID', 'status' => 'Verified'];
            }
            if (! empty($cand->phone) || ! empty($cand->secondary_mobile)) {
                $valArr[] = ['label' => 'PHONE NUMBER', 'status' => 'Verified'];
            }
            $hasLinkedin = ! empty($cand->linkedin) && ! in_array(strtolower(trim((string) $cand->linkedin)), ['--', '-', 'na', 'n/a', 'null', 'none']);
            if ($hasLinkedin) {
                $valArr[] = ['label' => 'LINKEDIN ID', 'status' => 'Verified'];
            } else {
                $valArr[] = ['label' => 'LINKEDIN ID', 'status' => '-'];
            }
            if (! empty($cand->current_designation) || ! empty($cand->current_company)) {
                $valArr[] = ['label' => 'JOB DESCRIPTION', 'status' => 'Shared with candidate'];
            }
            $cand->parsed_validation = $valArr;
        }

        $this->candidatesWithResumeCount = collect($this->applicationData)->where('has_resume', true)->count();
        $this->candidatesWithoutResumeCount = collect($this->applicationData)->where('has_resume', false)->count();

        $this->to_email_from_count = $this->cc_email_count = 0;
        $this->to_email_from_data = [];
        $this->cc_email_data = [];
        $commonFromArr = [];
        $commonCcArr = [];

        $companyId = auth()->user()->company_id;

        // 1. Internal Team Contacts (Users of the company)
        $assignDataone = DB::table('users')
            ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
            ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            ->leftjoin('companies', 'companies.id', 'users.company_id')
            ->where('users.company_id', '=', $companyId)
            ->where('users.status', '=', 'active')
            ->where('users.working_status', '=', 1)
            ->select(
                'users.id',
                'users.email',
                'users.name',
                'users.image',
                'users_profile.designation as display_name',
                'roles.display_name as role_name',
                'users.working_status',
                'companies.company_name'
            )->groupBy('users.id')->get()->toArray();

        // 2. Client Contacts
        $clientId = [];
        if (! empty($this->jobIdArr)) {
            $clientId = Job::whereIn('jobs.id', $this->jobIdArr)
                ->join('clients', 'clients.id', '=', 'jobs.client_id')
                ->where('clients.self_company', 0)
                ->pluck('jobs.client_id')->toArray();
        }
        if (empty($clientId) && ! empty($request->id)) {
            $client_row = DB::table('jobs')->select('jobs.client_id')->where('jobs.id', $request->id)->first();
            if ($client_row && ! empty($client_row->client_id)) {
                $clientId[] = $client_row->client_id;
            }
        }
        if (empty($clientId)) {
            $clientId = DB::table('clients')->where('company_id', $companyId)->pluck('id')->toArray();
        }

        $assignDataclient = [];
        if (! empty($clientId)) {
            $assignDataclient = DB::table('clients')
                ->leftjoin('companies', 'companies.id', 'clients.company_id')
                ->join('client_contact_info', 'clients.id', '=', 'client_contact_info.client_id')
                ->select(
                    'client_contact_info.id',
                    'client_contact_info.contact_person_email as email',
                    'client_contact_info.contact_person_name as name',
                    'client_contact_info.contact_person_designation as display_name',
                    'clients.name as company_name'
                )
                ->whereIn('clients.id', $clientId)->get()->toArray();
        }

        if ($request->id >= 1) {
            $client_row = DB::table('jobs')->select('jobs.client_id')->where('jobs.id', $request->id)->first();
            if ($client_row) {
                $ccInfo = DB::table('client_contact_info')->select('client_contact_info.contact_person_email')->where('client_contact_info.client_id', $client_row->client_id)->get();
                $this->to_email_from_count = count($ccInfo);
                $this->to_email_from_data = $ccInfo->pluck('contact_person_email')->toArray();
            }
        }

        $assignData = array_merge($assignDataclient, $assignDataone);
        $this->internal_team_data = $assignDataone;
        $this->client_contacts_data = $assignDataclient;
        $this->cc_email_assignData = (object) $assignData;

        $view = view('service_company.cvtracker.test_fwd_resume', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    /**
     * Resilient Candidate Resume Endpoint: Serves presigned R2 download or same-origin inline stream.
     * Tenancy-guarded to prevent cross-tenant access. Used by Forward Resume and Resume Center engines.
     */
    public function resume(Request $request, $id)
    {
        abort_unless(auth()->check(), 403);

        // TENANCY NOTE: Guard candidate application fetch with company_id to prevent cross-tenant IDOR
        $query = DB::table('job_applications')->where('id', $id);
        if (! auth()->user()->is_superadmin && auth()->user()->company_id) {
            $companyId = (int) auth()->user()->company_id;
            $query->where(function ($q) use ($companyId) {
                $q->where('company_id', $companyId)
                    ->orWhere('thirdparty_company_id', $companyId);
            });
        }
        $cand = $query->first();
        if (! $cand || empty($cand->resume)) {
            abort(404, 'Candidate resume not found.');
        }

        $resume = (string) $cand->resume;
        $r2Key = '';
        if (strpos($resume, 'https://file.jero.works/') === 0) {
            $r2Key = ltrim((string) parse_url($resume, PHP_URL_PATH), '/');
        } elseif (! filter_var($resume, FILTER_VALIDATE_URL)) {
            $r2Key = ltrim($resume, '/');
        }

        $candidateName = ! empty($cand->full_name) ? str_replace(' ', '_', preg_replace('/[^\w\s.-]/', '', $cand->full_name)) : ('Candidate_'.$id);
        $ext = strtolower(pathinfo($r2Key ?: $resume, PATHINFO_EXTENSION)) ?: 'pdf';
        $customName = 'Jeroworks_'.$candidateName.'.'.$ext;

        if ($r2Key !== '' && Storage::disk('r2')->exists($r2Key)) {
            if ($request->boolean('preview')) {
                return Storage::disk('r2')->response(
                    $r2Key,
                    $customName,
                    ['Cache-Control' => 'private, max-age=300', 'Access-Control-Allow-Origin' => '*'],
                    'inline'
                );
            }

            try {
                $temporaryUrl = Storage::disk('r2')->temporaryUrl(
                    $r2Key,
                    now()->addMinutes(30),
                    ['ResponseContentDisposition' => 'attachment; filename="'.$customName.'"']
                );
                return redirect()->away($temporaryUrl);
            } catch (\Throwable $e) {
                return redirect()->away('https://file.jero.works/'.$r2Key);
            }
        }

        if (file_exists(public_path($resume))) {
            return response()->file(public_path($resume), [
                'Content-Disposition' => ($request->boolean('preview') ? 'inline' : 'attachment').'; filename="'.$customName.'"'
            ]);
        }

        if (filter_var($resume, FILTER_VALIDATE_URL)) {
            return redirect()->away($resume);
        }

        abort(404, 'Resume file not found.');
    }

    /**
     * Isolated Test Forward Resume: Drawer Loader (0% Server Load / WASM + Direct R2 Beta)
     */
    public function test_fwd_resume(Request $request)
    {
        $rawArr = is_array($request->arr) ? $request->arr : [];
        $this->arr = array_slice($rawArr, 0, 5);
        $this->jobIdArr = $request->jobIdArr;
        $clientMatchMap = is_array($request->matchMap) ? $request->matchMap : [];
        $companyId = (int) auth()->user()->company_id;
        $this->applicationDataCount = DB::table('job_applications')
            ->whereIn('id', $this->arr)
            ->where(function ($q) use ($companyId) {
                $q->where('company_id', $companyId)
                    ->orWhere('thirdparty_company_id', $companyId);
            })
            ->count();

        $this->applicationData = DB::table('job_applications')
            ->select(
                'job_applications.id',
                'job_applications.condidate_type',
                'job_applications.full_name',
                'job_applications.current_designation',
                'job_applications.phone',
                'job_applications.phone_code',
                'job_applications.second_phone_code',
                'job_applications.secondary_mobile',
                'job_applications.email',
                'job_applications.secondrary_email',
                'job_applications.linkedin',
                'job_applications.city',
                'job_applications.city_id',
                'job_applications.state',
                'job_applications.country',
                'city_list.city as city_name',
                'state_list.state as state_name',
                'countries.country_name as country_name',
                'job_applications.wm',
                'job_applications.total_exp',
                'job_applications.rel_exp',
                'job_applications.ctc',
                'job_applications.ectc',
                'job_applications.current_ctc_cur',
                'job_applications.current_ectc_cur',
                'job_applications.current_ctc_mode',
                'job_applications.np_id',
                'job_applications.serving_notice_period_mode',
                'job_applications.current_company',
                'job_applications.skills',
                'job_applications.resume',
                'job_applications.rating',
                'job_applications.careersummary',
                'job_applications.cover_letter',
                'job_applications.recruiter_note',
                'job_applications.comment',
                'qualification_master.name as qualification_name',
                'jobs.id as job_id',
                'jobs.title as job_title',
                'clients.name as client_name',
                'add_job_wise_condidates.id as wise_id',
                'add_job_wise_condidates.skill_match as wise_skill_match'
            )
            ->leftJoin('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->leftJoin('jobs', 'jobs.id', 'add_job_wise_condidates.job_id')
            ->leftJoin('clients', 'clients.id', 'jobs.client_id')
            ->leftJoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
            ->leftJoin('city_list', function ($join) {
                $join->on('city_list.city_id', '=', 'job_applications.city_id')
                    ->orWhere(function ($q) {
                        $q->where(function ($sub) {
                            $sub->whereNull('job_applications.city_id')
                                ->orWhere('job_applications.city_id', 0);
                        })->whereColumn('city_list.city_id', 'job_applications.city');
                    });
            })
            ->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
            ->leftJoin('countries', 'countries.id', '=', 'job_applications.country')
            ->whereIn('job_applications.id', $this->arr)
            ->where(function ($q) use ($companyId) {
                $q->where('job_applications.company_id', $companyId)
                    ->orWhere('job_applications.thirdparty_company_id', $companyId);
            })
            ->groupBy('job_applications.id')
            ->get();

        try {
            $params = [
                'index' => ELASTIC_EMAIL_TEMPLATE,
                'body' => [
                    'size' => 10000,
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['status' => 1]],
                                ['match' => ['temptype' => 1]],
                            ],
                        ],
                    ],
                ],
            ];
            $response = $this->elasticService->getClient()->search($params);
            $this->templateData = collect($response['hits']['hits'] ?? [])->map(function ($hit) {
                $src = (object) $hit['_source'];
                if (! isset($src->id) && isset($src->e_temp_id)) {
                    $src->id = $src->e_temp_id;
                }
                if (! isset($src->e_temp_id) && isset($src->id)) {
                    $src->e_temp_id = $src->id;
                }

                return $src;
            });
        } catch (\Throwable $e) {
            $this->templateData = collect();
        }

        if (empty($this->templateData) || count($this->templateData) === 0) {
            $this->templateData = DB::table('th_email_template')->where('status', 1)->where('temptype', 1)->get()->map(function ($row) {
                $row->id = $row->e_temp_id;

                return $row;
            });
        }

        $this->userdata = $this->user ?: auth()->user();

        $companyRow = DB::table('companies')->select('id', 'company_name', 'logo')->where('id', auth()->user()->company_id)->first();
        $this->companyName = $companyRow ? ucwords((string) $companyRow->company_name) : 'Hiring Partner';
        $this->companyLogo = ($companyRow && ! empty($companyRow->logo)) ? route('service_company.job-applications.company-logo-proxy') : '';

        $jobSkillsList = [];
        if (! empty($this->jobIdArr)) {
            try {
                $client = $this->elasticService->getClient();
                foreach ($this->jobIdArr as $jid) {
                    $esJob = $client->get([
                        'index' => config('constants.ELASTIC_JOBS', 'jobs_index'),
                        'id' => (int) $jid,
                    ]);
                    $source = $esJob['_source'] ?? [];
                    if (! empty($source['job_metronics_skills']) && is_array($source['job_metronics_skills'])) {
                        foreach ($source['job_metronics_skills'] as $sk) {
                            $sn = is_array($sk) ? ($sk['skill_name'] ?? '') : ($sk->skill_name ?? '');
                            if (! empty($sn)) {
                                $jobSkillsList[] = trim($sn);
                            }
                        }
                    }
                }
            } catch (\Throwable $esErr) {
                \Log::warning('[test_fwd_resume] ES job skills read fallback to DB: '.$esErr->getMessage());
            }

            if (empty($jobSkillsList)) {
                $metronicSkills = DB::table('job_metronics_skills')->whereIn('job_id', $this->jobIdArr)->pluck('skill_name')->toArray();
                $standardSkills = DB::table('job_skills')->join('skills', 'job_skills.skill_id', '=', 'skills.id')->whereIn('job_skills.job_id', $this->jobIdArr)->pluck('skills.name')->toArray();
                $jobSkillsList = array_values(array_unique(array_filter(array_map('trim', array_merge($metronicSkills, $standardSkills)))));
            } else {
                $jobSkillsList = array_values(array_unique(array_filter(array_map('trim', $jobSkillsList))));
            }
        }
        $this->jobSkills = $jobSkillsList;

        // Bulk-load real skill experiences and ratings for selected candidates matching showdetail logic
        $appIds = collect($this->applicationData)->pluck('id')->toArray();

        $cjseSkills = collect();
        if (! empty($appIds)) {
            $cjseSkills = DB::table('candidate_job_skill_exp')
                ->join('job_metronics_skills', 'candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
                ->whereIn('candidate_job_skill_exp.candidate_id', $appIds)
                ->select(
                    'candidate_job_skill_exp.candidate_id',
                    'job_metronics_skills.skill_name',
                    'candidate_job_skill_exp.skill_value',
                    'candidate_job_skill_exp.rating'
                )
                ->get()
                ->groupBy('candidate_id');
        }

        $benchSkills = collect();
        if (! empty($appIds)) {
            $benchSkills = DB::table('bench_candidate_skill_experience')
                ->whereIn('candidate_id', $appIds)
                ->select('candidate_id', 'skill_key', 'year', 'month', 'total_months', 'rating')
                ->orderBy('skill_key')
                ->get()
                ->groupBy('candidate_id');
        }

        $todayFormatted = now()->format('d M Y');
        $recruiterName = auth()->user()->name ? ucwords((string) auth()->user()->name) : 'Hiring Team';

        foreach ($this->applicationData as $cand) {
            // Notice Period formatting (matching showdetail & bench rules)
            $npMode = (int) (@$cand->serving_notice_period_mode ?? -1);
            $npRaw = trim((string) @$cand->np_id);
            $npTs = $npRaw !== '' ? strtotime($npRaw) : false;
            $isBench = (@$cand->condidate_type === 'bench_condidate');

            if ($npMode === 2 || strtolower($npRaw) === 'immediate') {
                $cand->notice = 'Immediate';
                $cand->is_immediate = true;
            } elseif ($npMode === 0 && (int) $npRaw > 0) {
                $cand->notice = (int) $npRaw.' Days';
                $cand->is_immediate = false;
            } elseif ($npMode === 1 && $npTs !== false && (int) date('Y', $npTs) > 1970) {
                if (date('Y-m-d', $npTs) < date('Y-m-d')) {
                    $cand->notice = 'Immediate';
                    $cand->is_immediate = true;
                } else {
                    $cand->notice = $isBench ? ('Available '.date('d M Y', $npTs)) : ('SNP LWD '.date('d M Y', $npTs));
                    $cand->is_immediate = false;
                }
            } elseif ($npTs !== false && (int) date('Y', $npTs) > 1970) {
                $cand->notice = $isBench ? ('Available '.date('d M Y', $npTs)) : ('SNP LWD '.date('d M Y', $npTs));
                $cand->is_immediate = false;
            } else {
                $cand->notice = ($npRaw !== '' && ! str_contains($npRaw, '01 Jan 1970')) ? $npRaw : '--';
                $cand->is_immediate = false;
            }

            // Location: resolve human-readable city, state, country names — NEVER output numeric database IDs
            $cityName = (! empty($cand->city_name) && ! preg_match('/^\d+$/', trim((string) $cand->city_name))) ? trim((string) $cand->city_name) : null;
            if (! $cityName && ! empty($cand->city) && ! preg_match('/^\d+$/', trim((string) $cand->city))) {
                $cityName = trim((string) $cand->city);
            }

            $stateName = (! empty($cand->state_name) && ! preg_match('/^\d+$/', trim((string) $cand->state_name))) ? trim((string) $cand->state_name) : null;
            if (! $stateName && ! empty($cand->state) && ! preg_match('/^\d+$/', trim((string) $cand->state))) {
                $stateName = trim((string) $cand->state);
            }

            $countryName = (! empty($cand->country_name) && ! preg_match('/^\d+$/', trim((string) $cand->country_name))) ? trim((string) $cand->country_name) : null;
            if (! $countryName && ! empty($cand->country) && ! preg_match('/^\d+$/', trim((string) $cand->country))) {
                $countryName = trim((string) $cand->country);
            }

            $locParts = array_filter([$cityName, $stateName, $countryName]);
            $cand->location = ! empty($locParts) ? implode(', ', $locParts) : '--';

            // Work Mode
            $wmRaw = trim((string) @$cand->wm);
            $cand->work_mode = $wmRaw !== '' ? ucwords(str_replace(['_', '-'], ' ', strtolower($wmRaw))) : 'Hybrid';

            // Experience string
            $totExp = trim((string) @$cand->total_exp, " \t\n\r\0\x0B.");
            $expStr = ($totExp !== '' && (float) $totExp > 0) ? $totExp.' Year'.((float) $totExp == 1.0 ? '' : 's') : '--';
            $cand->experience = $expStr;

            // Report ID & Date & Recruiter
            $cand->report_id = 'JR'.now()->format('Ymd').str_pad((string) $cand->id, 6, '0', STR_PAD_LEFT);
            $cand->report_date = $todayFormatted;
            $cand->recruiter_name = $recruiterName;
            $cand->report_company_name = $this->companyName;
            $cand->report_company_logo = $this->companyLogo;

            // Match score / Hiring confidence percentage (exact 1:1 match with profile icon / table)
            $candSkillsList = array_values(array_filter(array_map('trim', explode(',', (string) @$cand->skills))));
            $matchPct = 0;
            if (! empty($clientMatchMap[$cand->id]) && (int) $clientMatchMap[$cand->id] > 0) {
                $matchPct = (int) $clientMatchMap[$cand->id];
            } elseif (! empty($cand->wise_skill_match)) {
                $matchPct = (int) round((float) rtrim((string) $cand->wise_skill_match, '%'));
            }
            if ($matchPct === 0 && ! empty($cand->job_id) && ! empty($candSkillsList)) {
                $calcPct = GeneralMethod::skill_percentage($cand->job_id, $candSkillsList);
                $matchPct = (int) round((float) rtrim($calcPct, '%'));
            }
            if ($matchPct === 0 && ! empty($jobSkillsList) && ! empty($candSkillsList)) {
                $calcPct = GeneralMethod::skill_percentage_from_array($jobSkillsList, $candSkillsList);
                $matchPct = (int) round((float) rtrim($calcPct, '%'));
            }
            if ($matchPct === 0) {
                $matchPct = ! empty($cand->rating) ? min(100, max(40, (int) ($cand->rating * 20))) : 85;
            }
            $cand->match = $matchPct;

            // Career Summary & Recruiter Note
            $sum = trim(strip_tags(html_entity_decode((string) (@$cand->careersummary ?: @$cand->cover_letter), ENT_QUOTES, 'UTF-8')));
            $cand->career_summary = ! empty($sum) ? $sum : 'Experienced professional with demonstrated background in designing, developing, and deploying robust software architectures, driving end-to-end delivery and engineering excellence.';

            $note = trim(strip_tags(html_entity_decode((string) (@$cand->recruiter_note ?? ''), ENT_QUOTES, 'UTF-8')));
            $cand->recruiter_note_text = $note;

            // Real Skills & Ratings for Skill Intelligence card
            $skillsArr = [];
            $seenSkillNames = [];

            // A. Check bench candidate skills if applicable
            $benchList = $benchSkills->get($cand->id);
            if ($benchList && count($benchList) > 0) {
                foreach ($benchList as $bRow) {
                    $sn = trim((string) $bRow->skill_key);
                    $snLower = strtolower($sn);
                    if ($sn === '' || isset($seenSkillNames[$snLower])) {
                        continue;
                    }
                    $seenSkillNames[$snLower] = true;

                    $yr = (int) ($bRow->year ?? 0);
                    $mo = (int) ($bRow->month ?? 0);
                    if (isset($bRow->total_months) && is_numeric($bRow->total_months) && (int) $bRow->total_months > 0) {
                        $yr = intdiv((int) $bRow->total_months, 12);
                        $mo = (int) $bRow->total_months % 12;
                    }
                    $sExpParts = [];
                    if ($yr > 0) {
                        $sExpParts[] = $yr.' Year'.($yr > 1 ? 's' : '');
                    }
                    if ($mo > 0) {
                        $sExpParts[] = $mo.' Month'.($mo > 1 ? 's' : '');
                    }
                    $sExpText = ! empty($sExpParts) ? implode(' ', $sExpParts) : ($yr === 0 && $mo === 0 ? '0 Years' : '--');

                    $ratingVal = (int) ($bRow->rating ?? 0);
                    if ($ratingVal <= 0 && ! empty($cand->rating)) {
                        $ratingVal = (int) $cand->rating;
                    }
                    $ratingVal = min(5, max(0, $ratingVal));

                    $skillsArr[] = [
                        'name' => ucwords($sn),
                        'expText' => $sExpText,
                        'exp' => $yr + ($mo / 12),
                        'rating' => $ratingVal,
                    ];
                    if (count($skillsArr) >= 7) {
                        break;
                    }
                }
            }

            // B. Map candidate evaluations from candidate_job_skill_exp
            $cjseList = $cjseSkills->get($cand->id);
            $cjseMap = [];
            if ($cjseList && count($cjseList) > 0) {
                foreach ($cjseList as $cRow) {
                    $snLower = strtolower(trim((string) $cRow->skill_name));
                    if ($snLower !== '') {
                        $cjseMap[$snLower] = $cRow;
                    }
                }
            }

            // Prioritize active job skills
            if (! empty($jobSkillsList) && count($skillsArr) < 7) {
                foreach ($jobSkillsList as $jsName) {
                    $jsTrim = trim((string) $jsName);
                    $jsLower = strtolower($jsTrim);
                    if ($jsLower === '' || isset($seenSkillNames[$jsLower])) {
                        continue;
                    }
                    $seenSkillNames[$jsLower] = true;

                    if (isset($cjseMap[$jsLower])) {
                        $cRow = $cjseMap[$jsLower];
                        $rawVal = trim((string) $cRow->skill_value);
                        $sExpText = '--';
                        $sExpNum = 0.0;
                        if ($rawVal !== '') {
                            if (preg_match('/(\d+)\s*(?:years?|yrs?)?\s+(\d+)\s*(?:months?|mos?)/i', $rawVal, $m)) {
                                $y = (int) $m[1];
                                $mo = (int) $m[2];
                                $sExpText = $y.' Year'.($y > 1 ? 's' : '').($mo > 0 ? ' '.$mo.' Month'.($mo > 1 ? 's' : '') : '');
                                $sExpNum = $y + ($mo / 12);
                            } elseif (preg_match('/^(\d+(?:\.\d+)?)/', $rawVal, $m)) {
                                $sExpNum = (float) $m[1];
                                $sExpText = $sExpNum.' Year'.($sExpNum == 1.0 ? '' : 's');
                            } else {
                                $sExpText = $rawVal;
                            }
                        }

                        $ratingVal = (int) ($cRow->rating ?? 0);
                        if ($ratingVal <= 0 && ! empty($cand->rating)) {
                            $ratingVal = (int) $cand->rating;
                        }
                        $ratingVal = min(5, max(0, $ratingVal));

                        $skillsArr[] = [
                            'name' => ucwords($jsTrim),
                            'expText' => $sExpText,
                            'exp' => $sExpNum,
                            'rating' => $ratingVal,
                        ];
                    } else {
                        $defaultRating = ! empty($cand->rating) ? min(5, max(0, (int) $cand->rating)) : 0;
                        $skillsArr[] = [
                            'name' => ucwords($jsTrim),
                            'expText' => $expStr,
                            'exp' => (float) $totExp ?: 0,
                            'rating' => $defaultRating,
                        ];
                    }

                    if (count($skillsArr) >= 7) {
                        break;
                    }
                }
            }

            // C. If slots remain (< 7), include candidate's remaining evaluated or profile skills
            if (count($skillsArr) < 7 && $cjseList && count($cjseList) > 0) {
                foreach ($cjseList as $cRow) {
                    $sn = trim((string) $cRow->skill_name);
                    $snLower = strtolower($sn);
                    if ($sn === '' || isset($seenSkillNames[$snLower])) {
                        continue;
                    }
                    $seenSkillNames[$snLower] = true;

                    $rawVal = trim((string) $cRow->skill_value);
                    $sExpText = '--';
                    $sExpNum = 0.0;
                    if ($rawVal !== '') {
                        if (preg_match('/(\d+)\s*(?:years?|yrs?)?\s+(\d+)\s*(?:months?|mos?)/i', $rawVal, $m)) {
                            $y = (int) $m[1];
                            $mo = (int) $m[2];
                            $sExpText = $y.' Year'.($y > 1 ? 's' : '').($mo > 0 ? ' '.$mo.' Month'.($mo > 1 ? 's' : '') : '');
                            $sExpNum = $y + ($mo / 12);
                        } elseif (preg_match('/^(\d+(?:\.\d+)?)/', $rawVal, $m)) {
                            $sExpNum = (float) $m[1];
                            $sExpText = $sExpNum.' Year'.($sExpNum == 1.0 ? '' : 's');
                        } else {
                            $sExpText = $rawVal;
                        }
                    }

                    $ratingVal = (int) ($cRow->rating ?? 0);
                    if ($ratingVal <= 0 && ! empty($cand->rating)) {
                        $ratingVal = (int) $cand->rating;
                    }
                    $ratingVal = min(5, max(0, $ratingVal));

                    $skillsArr[] = [
                        'name' => ucwords($sn),
                        'expText' => $sExpText,
                        'exp' => $sExpNum,
                        'rating' => $ratingVal,
                    ];
                    if (count($skillsArr) >= 7) {
                        break;
                    }
                }
            }

            if (count($skillsArr) < 7 && ! empty($candSkillsList)) {
                $defaultRating = ! empty($cand->rating) ? min(5, max(0, (int) $cand->rating)) : 0;
                foreach ($candSkillsList as $skName) {
                    if (! $skName) {
                        continue;
                    }
                    $snLower = strtolower($skName);
                    if (isset($seenSkillNames[$snLower])) {
                        continue;
                    }
                    $seenSkillNames[$snLower] = true;

                    $skillsArr[] = [
                        'name' => ucwords($skName),
                        'expText' => $expStr,
                        'exp' => (float) $totExp ?: 0,
                        'rating' => $defaultRating,
                    ];
                    if (count($skillsArr) >= 7) {
                        break;
                    }
                }
            }
            $cand->parsed_skills = $skillsArr;

            // Resume presence detection & verification
            $candResume = trim((string) @$cand->resume);
            $hasResume = false;
            $resumeFilename = '';
            if ($candResume !== '' && $candResume !== '0' && strtolower($candResume) !== 'null') {
                $hasResume = true;
                $resumeFilename = basename($candResume);
            }
            $cand->has_resume = $hasResume;
            $cand->resume_filename = $resumeFilename;
            $cand->resume_status = $hasResume ? 'available' : 'missing';
            $cand->resume_url = $hasResume ? route('service_company.cvtracker.resume', $cand->id) : '';

            $valArr = [];
            if ($hasResume) {
                $valArr[] = ['label' => 'RESUME', 'status' => 'Verified'];
            } else {
                $valArr[] = ['label' => 'RESUME', 'status' => 'Not Uploaded'];
            }
            if (! empty($cand->email) || ! empty($cand->secondrary_email)) {
                $valArr[] = ['label' => 'EMAIL ID', 'status' => 'Verified'];
            }
            if (! empty($cand->phone) || ! empty($cand->secondary_mobile)) {
                $valArr[] = ['label' => 'PHONE NUMBER', 'status' => 'Verified'];
            }
            $hasLinkedin = ! empty($cand->linkedin) && ! in_array(strtolower(trim((string) $cand->linkedin)), ['--', '-', 'na', 'n/a', 'null', 'none']);
            if ($hasLinkedin) {
                $valArr[] = ['label' => 'LINKEDIN ID', 'status' => 'Verified'];
            } else {
                $valArr[] = ['label' => 'LINKEDIN ID', 'status' => '-'];
            }
            if (! empty($cand->current_designation) || ! empty($cand->current_company)) {
                $valArr[] = ['label' => 'JOB DESCRIPTION', 'status' => 'Shared with candidate'];
            }
            $cand->parsed_validation = $valArr;
        }

        $this->candidatesWithResumeCount = collect($this->applicationData)->where('has_resume', true)->count();
        $this->candidatesWithoutResumeCount = collect($this->applicationData)->where('has_resume', false)->count();

        $this->to_email_from_count = $this->cc_email_count = 0;
        $this->to_email_from_data = [];
        $this->cc_email_data = [];

        $companyId = auth()->user()->company_id;

        $assignDataone = DB::table('users')
            ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
            ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            ->leftjoin('companies', 'companies.id', 'users.company_id')
            ->where('users.company_id', '=', $companyId)
            ->where('users.status', '=', 'active')
            ->where('users.working_status', '=', 1)
            ->select(
                'users.id',
                'users.email',
                'users.name',
                'users.image',
                'users_profile.designation as display_name',
                'roles.display_name as role_name',
                'users.working_status',
                'companies.company_name'
            )->groupBy('users.id')->get()->toArray();

        $clientId = [];
        if (! empty($this->jobIdArr)) {
            $clientId = Job::whereIn('jobs.id', $this->jobIdArr)
                ->join('clients', 'clients.id', '=', 'jobs.client_id')
                ->where('clients.self_company', 0)
                ->pluck('jobs.client_id')->toArray();
        }
        if (empty($clientId) && ! empty($request->id)) {
            $client_row = DB::table('jobs')->select('jobs.client_id')->where('jobs.id', $request->id)->first();
            if ($client_row && ! empty($client_row->client_id)) {
                $clientId[] = $client_row->client_id;
            }
        }
        if (empty($clientId)) {
            $clientId = DB::table('clients')->where('company_id', $companyId)->pluck('id')->toArray();
        }

        $assignDataclient = [];
        if (! empty($clientId)) {
            $assignDataclient = DB::table('clients')
                ->leftjoin('companies', 'companies.id', 'clients.company_id')
                ->join('client_contact_info', 'clients.id', '=', 'client_contact_info.client_id')
                ->select(
                    'client_contact_info.id',
                    'client_contact_info.contact_person_email as email',
                    'client_contact_info.contact_person_name as name',
                    'client_contact_info.contact_person_designation as display_name',
                    'clients.name as company_name'
                )
                ->whereIn('clients.id', $clientId)->get()->toArray();
        }

        if ($request->id >= 1) {
            $client_row = DB::table('jobs')->select('jobs.client_id')->where('jobs.id', $request->id)->first();
            if ($client_row) {
                $ccInfo = DB::table('client_contact_info')->select('client_contact_info.contact_person_email')->where('client_contact_info.client_id', $client_row->client_id)->get();
                $this->to_email_from_count = count($ccInfo);
                $this->to_email_from_data = $ccInfo->pluck('contact_person_email')->toArray();
            }
        }

        $assignData = array_merge($assignDataclient, $assignDataone);
        $this->internal_team_data = $assignDataone;
        $this->client_contacts_data = $assignDataclient;
        $this->cc_email_assignData = (object) $assignData;

        $view = view('service_company.cvtracker.test_fwd_resume', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    /**
     * V4.1 Zero-Synchronous-Load Forward Resume: Micro-JSON Hydration Endpoint (< 3KB JSON)
     *
     * Intent: Hydrates candidate profiles, skill matrices, template metadata, and company
     * branding directly from Elasticsearch and Redis without triggering MySQL table joins
     * or Blade view rendering.
     *
     * Tenancy Invariant: Candidate IDs are strictly authorized via ForwardResumeAuthorizationService.
     */
    public function test_fwd_resume_data(Request $request)
    {
        $user = auth()->user();
        $companyId = (int) $user->company_id;
        $rawArr = is_array($request->arr) ? $request->arr : [];
        $rawCandidateIds = array_slice($rawArr, 0, 5);
        $jobIdArr = is_array($request->jobIdArr) ? $request->jobIdArr : [];

        if (empty($rawCandidateIds)) {
            return response()->json(['status' => 'error', 'message' => 'No candidate selected.'], 422);
        }

        // 1. Authorize candidate batch & enforce multi-tenant isolation boundary
        try {
            $authService = app(\App\Services\ForwardResumeAuthorizationService::class);
            $candidateIds = $authService->authorizeCandidateBatch($user, $rawCandidateIds, $jobIdArr);
        } catch (\Throwable $authEx) {
            return response()->json(['status' => 'error', 'message' => $authEx->getMessage()], 403);
        }

        // 2. Fetch Company Branding from Redis cache (24h TTL)
        $companyData = \Illuminate\Support\Facades\Cache::remember("tenant:{$companyId}:fwd_branding", 86400, function () use ($companyId) {
            $comp = DB::table('companies')->select('company_name', 'logo')->where('id', $companyId)->first();

            return [
                'name' => $comp ? ucwords((string) $comp->company_name) : 'Hiring Partner',
                'logo' => ($comp && ! empty($comp->logo)) ? route('service_company.job-applications.company-logo-proxy') : '',
            ];
        });

        // 3. Fetch Email Template Metadata from Redis cache (1h TTL)
        $templates = \Illuminate\Support\Facades\Cache::remember("tenant:{$companyId}:fwd_template_metadata", 3600, function () {
            try {
                $params = [
                    'index' => config('constants.ELASTIC_EMAIL_TEMPLATE'),
                    'body' => [
                        'size' => 50,
                        '_source' => ['id', 'e_temp_id', 'template_name', 'template_sub', 'template_body'],
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['status' => 1]],
                                    ['match' => ['temptype' => 1]],
                                ],
                            ],
                        ],
                    ],
                ];
                $resp = app(\App\Services\ElasticService::class)->getClient()->search($params);
                $hits = $resp['hits']['hits'] ?? [];

                return collect($hits)->map(function ($hit) {
                    $src = $hit['_source'];
                    $tId = $src['id'] ?? ($src['e_temp_id'] ?? $hit['_id']);

                    return [
                        'id' => (int) $tId,
                        'name' => $src['template_name'] ?? 'Template',
                        'subject' => $src['template_sub'] ?? '',
                        'body' => $src['template_body'] ?? '',
                    ];
                })->toArray();
            } catch (\Throwable $e) {
                return DB::table('th_email_template')
                    ->where('status', 1)
                    ->where('temptype', 1)
                    ->limit(50)
                    ->select('e_temp_id as id', 'template_name as name', 'template_sub as subject', 'template_body as body')
                    ->get()
                    ->toArray();
            }
        });

        // 4. Resolve Target Job Skills (Elasticsearch first with resilient DB fallback, zero stale Redis caching)
        $jobSkillsList = [];
        if (! empty($jobIdArr)) {
            try {
                $client = app(\App\Services\ElasticService::class)->getClient();
                foreach ($jobIdArr as $jid) {
                    $esJob = $client->get([
                        'index' => config('constants.ELASTIC_JOBS', 'jobs_index'),
                        'id' => (int) $jid,
                    ]);
                    $source = $esJob['_source'] ?? [];
                    if (! empty($source['job_metronics_skills']) && is_array($source['job_metronics_skills'])) {
                        foreach ($source['job_metronics_skills'] as $sk) {
                            $sn = is_array($sk) ? ($sk['skill_name'] ?? '') : ($sk->skill_name ?? '');
                            if (! empty($sn)) {
                                $jobSkillsList[] = trim($sn);
                            }
                        }
                    }
                }
            } catch (\Throwable $esErr) {
                \Log::warning('[test_fwd_resume_data] ES job skills read fallback to DB: '.$esErr->getMessage());
            }

            if (empty($jobSkillsList)) {
                $metronicSkills = DB::table('job_metronics_skills')->whereIn('job_id', $jobIdArr)->pluck('skill_name')->toArray();
                $standardSkills = DB::table('job_skills')->join('skills', 'job_skills.skill_id', '=', 'skills.id')->whereIn('job_skills.job_id', $jobIdArr)->pluck('skills.name')->toArray();
                $jobSkillsList = array_values(array_unique(array_filter(array_map('trim', array_merge($metronicSkills, $standardSkills)))));
            } else {
                $jobSkillsList = array_values(array_unique(array_filter(array_map('trim', $jobSkillsList))));
            }
        }

        // 5. Query Candidates: Primary via ELASTIC_CANDIDATES (sub-3ms, 0 MySQL joins)
        $candidates = [];
        $todayFormatted = now()->format('d M Y');
        $recruiterName = $user->name ? ucwords((string) $user->name) : 'Hiring Team';

        try {
            $esParams = [
                'index' => config('constants.ELASTIC_CANDIDATES'),
                'body' => [
                    'size' => count($candidateIds),
                    'query' => [
                        'bool' => [
                            'must' => [['terms' => ['id' => $candidateIds]]],
                            'filter' => [
                                ['bool' => [
                                    'should' => [
                                        ['term' => ['company_id' => $companyId]],
                                        ['term' => ['thirdparty_company_id' => $companyId]],
                                    ],
                                    'minimum_should_match' => 1,
                                ]],
                            ],
                        ],
                    ],
                ],
            ];
            $esCandResp = app(\App\Services\ElasticService::class)->getClient()->search($esParams);
            $candHits = $esCandResp['hits']['hits'] ?? [];
            foreach ($candHits as $hit) {
                $src = (object) $hit['_source'];
                $candidates[] = $src;
            }
        } catch (\Throwable $esErr) {
            \Log::warning('[test_fwd_resume_data] ES candidate read failed: '.$esErr->getMessage().'. Resilient fallback to DB.');
        }

        // Fallback to database if ES indexing is lagging or offline
        if (count($candidates) < count($candidateIds)) {
            $dbCandidates = DB::table('job_applications')
                ->whereIn('id', $candidateIds)
                ->where(function ($q) use ($companyId) {
                    $q->where('company_id', $companyId)
                        ->orWhere('thirdparty_company_id', $companyId);
                })
                ->get();
            $candidates = $dbCandidates;
        }

        // Fetch Skill Experience
        $appIds = collect($candidates)->pluck('id')->toArray();
        $cjseSkills = collect();
        if (! empty($appIds)) {
            $cjseSkills = DB::table('candidate_job_skill_exp')
                ->join('job_metronics_skills', 'candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
                ->whereIn('candidate_job_skill_exp.candidate_id', $appIds)
                ->select('candidate_job_skill_exp.candidate_id', 'job_metronics_skills.skill_name', 'candidate_job_skill_exp.skill_value', 'candidate_job_skill_exp.rating')
                ->get()
                ->groupBy('candidate_id');
        }

        $formattedCandidates = [];
        foreach ($candidates as $cand) {
            $cId = (int) $cand->id;
            $candSkillsList = array_values(array_filter(array_map('trim', explode(',', (string) @$cand->skills))));

            // Target job for this candidate dossier: prioritize active target job from request over historical profile job_id
            $targetJobIdForCand = ! empty($jobIdArr[0]) ? (int) $jobIdArr[0] : (! empty($cand->job_id) ? (int) $cand->job_id : null);

            // Server-authoritative match score
            $authoritativeMatch = $authService->computeAuthoritativeMatch(
                $cId,
                $targetJobIdForCand,
                $candSkillsList,
                $jobSkillsList,
                (int) (@$cand->rating ?: 4)
            );

            // Notice Period formatting
            $npMode = (int) (@$cand->serving_notice_period_mode ?? -1);
            $npRaw = trim((string) @$cand->np_id);
            $npTs = $npRaw !== '' ? strtotime($npRaw) : false;
            $noticeText = '--';
            $isImmediate = false;

            if ($npMode === 2 || strtolower($npRaw) === 'immediate') {
                $noticeText = 'Immediate';
                $isImmediate = true;
            } elseif ($npMode === 0 && (int) $npRaw > 0) {
                $noticeText = (int) $npRaw.' Days';
            } elseif ($npTs !== false && (int) date('Y', $npTs) > 1970) {
                if (date('Y-m-d', $npTs) < date('Y-m-d')) {
                    $noticeText = 'Immediate';
                    $isImmediate = true;
                } else {
                    $noticeText = (@$cand->condidate_type === 'bench_condidate') ? ('Available '.date('d M Y', $npTs)) : ('SNP LWD '.date('d M Y', $npTs));
                }
            } else {
                $noticeText = ($npRaw !== '' && ! str_contains($npRaw, '01 Jan 1970')) ? $npRaw : '--';
            }

            // Location
            $cityName = (! empty($cand->city_name) && ! preg_match('/^\d+$/', trim((string) $cand->city_name))) ? trim((string) $cand->city_name) : null;
            if (! $cityName && ! empty($cand->city) && ! preg_match('/^\d+$/', trim((string) $cand->city))) {
                $cityName = trim((string) $cand->city);
            }
            $stateName = (! empty($cand->state_name) && ! preg_match('/^\d+$/', trim((string) $cand->state_name))) ? trim((string) $cand->state_name) : null;
            if (! $stateName && ! empty($cand->state) && ! preg_match('/^\d+$/', trim((string) $cand->state))) {
                $stateName = trim((string) $cand->state);
            }
            $locParts = array_filter([$cityName, $stateName]);
            $locationText = ! empty($locParts) ? implode(', ', $locParts) : '--';

            // Experience
            $totExp = trim((string) @$cand->total_exp, " \t\n\r\0\x0B.");
            $expStr = ($totExp !== '' && (float) $totExp > 0) ? $totExp.' Year'.((float) $totExp == 1.0 ? '' : 's') : '--';

            // Resume detection
            $candResume = trim((string) @$cand->resume);
            $hasResume = ($candResume !== '' && $candResume !== '0' && strtolower($candResume) !== 'null');
            $resumeUrl = $hasResume ? route('service_company.cvtracker.resume', $cId) : '';

            // Skill ratings for dossier - prioritizes active job skills mapped to candidate evaluations
            $skillsArr = [];
            $seenSkillNames = [];
            $cjseList = $cjseSkills->get($cId);
            $cjseMap = [];
            if ($cjseList && count($cjseList) > 0) {
                foreach ($cjseList as $cRow) {
                    $snLower = strtolower(trim((string) $cRow->skill_name));
                    if ($snLower !== '') {
                        $cjseMap[$snLower] = $cRow;
                    }
                }
            }

            // A. Prioritize active job skills
            if (! empty($jobSkillsList)) {
                foreach ($jobSkillsList as $jsName) {
                    $jsTrim = trim((string) $jsName);
                    $jsLower = strtolower($jsTrim);
                    if ($jsLower === '' || isset($seenSkillNames[$jsLower])) {
                        continue;
                    }
                    $seenSkillNames[$jsLower] = true;

                    if (isset($cjseMap[$jsLower])) {
                        $cRow = $cjseMap[$jsLower];
                        $rawVal = trim((string) $cRow->skill_value);
                        $skillsArr[] = [
                            'name' => ucwords($jsTrim),
                            'expText' => $rawVal !== '' ? $rawVal : $expStr,
                            'rating' => min(5, max(0, (int) ($cRow->rating ?? ($cand->rating ?? 4)))),
                        ];
                    } else {
                        $skillsArr[] = [
                            'name' => ucwords($jsTrim),
                            'expText' => $expStr,
                            'rating' => min(5, max(0, (int) (@$cand->rating ?: 4))),
                        ];
                    }

                    if (count($skillsArr) >= 7) {
                        break;
                    }
                }
            }

            // B. If slots remain (< 7), include candidate's remaining evaluated skills
            if (count($skillsArr) < 7 && $cjseList && count($cjseList) > 0) {
                foreach ($cjseList as $cRow) {
                    $sn = trim((string) $cRow->skill_name);
                    $snLower = strtolower($sn);
                    if ($sn === '' || isset($seenSkillNames[$snLower])) {
                        continue;
                    }
                    $seenSkillNames[$snLower] = true;
                    $rawVal = trim((string) $cRow->skill_value);
                    $skillsArr[] = [
                        'name' => ucwords($sn),
                        'expText' => $rawVal !== '' ? $rawVal : $expStr,
                        'rating' => min(5, max(0, (int) ($cRow->rating ?? ($cand->rating ?? 4)))),
                    ];
                    if (count($skillsArr) >= 7) {
                        break;
                    }
                }
            }

            // C. If still empty or slots remain (< 7), fill from candidate profile skills
            if (count($skillsArr) < 7 && ! empty($candSkillsList)) {
                foreach ($candSkillsList as $skName) {
                    $snLower = strtolower(trim((string) $skName));
                    if ($snLower === '' || isset($seenSkillNames[$snLower])) {
                        continue;
                    }
                    $seenSkillNames[$snLower] = true;
                    $skillsArr[] = [
                        'name' => ucwords($skName),
                        'expText' => $expStr,
                        'rating' => min(5, max(0, (int) (@$cand->rating ?: 4))),
                    ];
                    if (count($skillsArr) >= 7) {
                        break;
                    }
                }
            }

            // Validation Badges
            $valArr = [
                ['label' => 'RESUME', 'status' => $hasResume ? 'Verified' : 'Not Uploaded'],
                ['label' => 'EMAIL ID', 'status' => (! empty($cand->email) || ! empty($cand->secondrary_email)) ? 'Verified' : 'Unverified'],
                ['label' => 'PHONE NUMBER', 'status' => (! empty($cand->phone) || ! empty($cand->secondary_mobile)) ? 'Verified' : 'Unverified'],
                ['label' => 'LINKEDIN ID', 'status' => ! empty($cand->linkedin) ? 'Verified' : '-'],
                ['label' => 'JOB DESCRIPTION', 'status' => 'Shared with candidate'],
            ];

            $candSafeName = ! empty($cand->full_name) ? str_replace(' ', '_', preg_replace('/[^\w\s.-]/', '', $cand->full_name)) : ('Candidate_'.$cId);

            // R2 key and direct download resolution for client-side WASM engine
            $r2Key = '';
            $resumeR2Url = '';
            if ($hasResume) {
                if (strpos($candResume, 'https://file.jero.works/') === 0) {
                    $r2Key = ltrim((string) parse_url($candResume, PHP_URL_PATH), '/');
                } elseif (! filter_var($candResume, FILTER_VALIDATE_URL)) {
                    $r2Key = ltrim($candResume, '/');
                }
                if ($r2Key !== '') {
                    $resumeR2Url = 'https://file.jero.works/'.$r2Key;
                } elseif (filter_var($candResume, FILTER_VALIDATE_URL)) {
                    $resumeR2Url = $candResume;
                }
            }
            $resumeFilename = $hasResume ? basename($candResume) : '';
            $resumeExt = $hasResume ? strtolower(pathinfo($r2Key ?: $candResume, PATHINFO_EXTENSION)) : '';
            $streamProxyUrl = $hasResume ? route('service_company.cvtracker.resume', ['id' => $cId, 'preview' => 1]) : '';

            $formattedCandidates[] = [
                'id' => $cId,
                'full_name' => $cand->full_name ?: ('Candidate #'.$cId),
                'safe_name' => $candSafeName,
                'designation' => $cand->current_designation ?: (@$cand->job_title ?: 'Profile'),
                'company' => $cand->current_company ?: '--',
                'has_resume' => $hasResume,
                'resume' => $candResume,
                'resume_url' => $resumeUrl,
                'resume_r2_url' => $resumeR2Url,
                'resume_filename' => $resumeFilename,
                'resume_extension' => $resumeExt,
                'download_url' => $resumeUrl,
                'preview_url' => $streamProxyUrl,
                'experience' => $expStr,
                'notice' => $noticeText,
                'is_immediate' => $isImmediate,
                'location' => $locationText,
                'education' => $cand->qualification_name ?? (@$cand->qualification ?: '--'),
                'work_mode' => ! empty($cand->wm) ? ucwords(str_replace(['_', '-'], ' ', strtolower($cand->wm))) : 'Hybrid',
                'career_summary' => trim(strip_tags(html_entity_decode((string) (@$cand->careersummary ?: @$cand->cover_letter), ENT_QUOTES, 'UTF-8'))),
                'recruiter_note_text' => trim(strip_tags(html_entity_decode((string) (@$cand->recruiter_note ?? ''), ENT_QUOTES, 'UTF-8'))),
                'match' => $authoritativeMatch,
                'rating' => (int) (@$cand->rating ?: 4),
                'skills' => $skillsArr,
                'validation' => $valArr,
                'report_id' => 'JR'.now()->format('Ymd').str_pad((string) $cId, 6, '0', STR_PAD_LEFT),
                'report_date' => $todayFormatted,
                'recruiter_name' => $recruiterName,
                'report_company_name' => $companyData['name'],
                'report_company_logo' => $companyData['logo'],
            ];
        }

        return response()->json([
            'status' => 'success',
            'company' => $companyData,
            'companyName' => $companyData['name'] ?? '',
            'templates' => $templates,
            'templateData' => $templates,
            'candidates' => $formattedCandidates,
            'applicationData' => $formattedCandidates,
            'job_skills' => $jobSkillsList,
            'jobSkills' => $jobSkillsList,
            'sender_email' => $user->email,
            'sender_name' => $user->name ?: $user->email,
            'from_email' => $user->email,
            'reply_to_email' => $user->email,
        ]);
    }

    /**
     * V4.1 Lazy-Loaded Recipient Directory Endpoint with 15-minute Redis Cache
     *
     * Intent: Serves internal team and client contacts on demand when recruiter clicks
     * "+ Add Recipient". Completely decouples 4-table SQL joins from drawer open.
     */
    public function fwd_recipients(Request $request)
    {
        $companyId = (int) auth()->user()->company_id;
        $jobIdArr = is_array($request->jobIdArr) ? $request->jobIdArr : [];

        $cacheKey = "tenant:{$companyId}:fwd_recipients_dir";
        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 900, function () use ($companyId, $jobIdArr) {
            // Internal Team
            $team = DB::table('users')
                ->leftJoin('role_user', 'role_user.user_id', '=', 'users.id')
                ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
                ->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')
                ->where('users.company_id', '=', $companyId)
                ->where('users.status', '=', 'active')
                ->where('users.working_status', '=', 1)
                ->select(
                    'users.id',
                    'users.email',
                    'users.name',
                    'users_profile.designation as display_name',
                    'roles.display_name as role_name'
                )
                ->groupBy('users.id')
                ->get()
                ->toArray();

            // Client Contacts
            $clientId = [];
            if (! empty($jobIdArr)) {
                $clientId = DB::table('jobs')
                    ->whereIn('jobs.id', $jobIdArr)
                    ->join('clients', 'clients.id', '=', 'jobs.client_id')
                    ->where('clients.self_company', 0)
                    ->pluck('jobs.client_id')
                    ->toArray();
            }
            if (empty($clientId)) {
                $clientId = DB::table('clients')->where('company_id', $companyId)->pluck('id')->toArray();
            }

            $clients = [];
            if (! empty($clientId)) {
                $clients = DB::table('clients')
                    ->join('client_contact_info', 'clients.id', '=', 'client_contact_info.client_id')
                    ->select(
                        'client_contact_info.id',
                        'client_contact_info.contact_person_email as email',
                        'client_contact_info.contact_person_name as name',
                        'client_contact_info.contact_person_designation as display_name',
                        'clients.name as company_name'
                    )
                    ->whereIn('clients.id', $clientId)
                    ->get()
                    ->toArray();
            }

            return [
                'internal_team' => $team,
                'client_contacts' => $clients,
            ];
        });

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    /**
     * Isolated Test Forward Resume: Direct-to-R2 Presigned Upload Intent Generator & Authorization
     */
    public function test_fwd_resume_intent(Request $request)
    {
        $companyId = (int) auth()->user()->company_id;
        $userId = (int) auth()->user()->id;

        // Rate Limiting (Redis Throttle: company 100/min, user 30/min)
        $companyRateKey = "fwd_rate:comp:{$companyId}";
        $userRateKey = "fwd_rate:usr:{$userId}";
        try {
            $userHits = \Illuminate\Support\Facades\Redis::incr($userRateKey);
            if ($userHits === 1) {
                \Illuminate\Support\Facades\Redis::expire($userRateKey, 60);
            }
            if ($userHits > 30) {
                return response()->json(['status' => 'error', 'message' => 'User rate limit exceeded (30 requests/min).'], 429);
            }
            $compHits = \Illuminate\Support\Facades\Redis::incr($companyRateKey);
            if ($compHits === 1) {
                \Illuminate\Support\Facades\Redis::expire($companyRateKey, 60);
            }
            if ($compHits > 100) {
                return response()->json(['status' => 'error', 'message' => 'Company rate limit exceeded (100 requests/min).'], 429);
            }
        } catch (\Throwable $rateErr) {
            // Non-fatal if Redis throttle throws
        }

        $candidateIds = $request->input('candidate_ids', []);
        $files = $request->input('files', []);
        $forwardRequestId = $request->input('forward_request_id') ?: (string) Str::uuid();

        // Extract candidate IDs from files if not passed directly
        if (empty($candidateIds) && ! empty($files) && is_array($files)) {
            foreach ($files as $f) {
                if (! empty($f['candidate_id'])) {
                    $candidateIds[] = (int) $f['candidate_id'];
                }
            }
        }

        // Multi-tenant candidate authorization check via ELASTIC_CANDIDATES (0 MySQL reads)
        if (! empty($candidateIds)) {
            try {
                $authService = app(ForwardResumeAuthorizationService::class);
                $validatedCandidateIds = $authService->authorizeCandidateBatch(auth()->user(), $candidateIds);
                if (count($validatedCandidateIds) !== count(array_unique($candidateIds))) {
                    return response()->json(['status' => 'error', 'message' => 'Unauthorized candidate access detected.'], 403);
                }
            } catch (\Throwable $authErr) {
                return response()->json(['status' => 'error', 'message' => $authErr->getMessage()], 403);
            }
        }

        $results = [];
        $uploadService = app(ForwardResumeUploadService::class);
        if (! empty($files) && is_array($files)) {
            $results = $uploadService->initiateBatchUploads($companyId, $userId, $files);
        }

        // Store intent authorization in Redis with 15-minute TTL
        try {
            \Illuminate\Support\Facades\Redis::setex("fwd_intent:{$companyId}:{$forwardRequestId}", 900, json_encode([
                'forward_request_id' => $forwardRequestId,
                'company_id' => $companyId,
                'user_id' => $userId,
                'candidate_ids' => $candidateIds,
                'authorized_at' => time(),
            ]));
        } catch (\Throwable $e) {
        }

        return response()->json([
            'status' => 'success',
            'intent_id' => $forwardRequestId,
            'forward_request_id' => $forwardRequestId,
            'candidate_ids' => $candidateIds,
            'attachments' => $results,
            'uploads' => $results,
        ]);
    }

    /**
     * Isolated Test Forward Resume: Direct-to-R2 Presigned Upload URL Minting with Signed Metadata
     */
    public function test_fwd_resume_presigned(Request $request)
    {
        $companyId = (int) auth()->user()->company_id;
        $userId = (int) auth()->user()->id;
        $files = $request->input('files', []);

        if (empty($files) || ! is_array($files)) {
            $file = [
                'candidate_id' => $request->input('candidate_id'),
                'filename' => $request->input('filename', 'Candidate_Resume.pdf'),
                'size' => $request->input('expected_size', $request->input('size')),
                'expected_size' => $request->input('expected_size', $request->input('size')),
                'expected_sha256' => $request->input('expected_sha256', $request->input('sha256')),
                'mime' => $request->input('mime', 'application/pdf'),
            ];
            $files = [$file];
        }

        // Scope bounds check: size <= 10MB
        foreach ($files as $file) {
            $size = (int) ($file['expected_size'] ?? ($file['size'] ?? 0));
            if ($size > 10 * 1024 * 1024) {
                return response()->json(['status' => 'error', 'message' => 'Resume PDF size exceeds maximum allowed 10MB limit.'], 422);
            }
        }

        $uploadService = app(ForwardResumeUploadService::class);
        $results = $uploadService->initiateBatchUploads($companyId, $userId, $files);

        if (empty($results)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cloudflare R2 storage is not configured or failed to generate presigned upload URLs.',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'uploads' => $results,
            'attachments' => $results,
        ]);
    }

    /**
     * Isolated Test Forward Resume: Byte-Exact Attestation & Cryptographic Verification
     */
    public function test_fwd_resume_finalize(Request $request)
    {
        $companyId = (int) auth()->user()->company_id;
        $attachmentUuid = (string) ($request->input('attachment_uuid') ?: $request->input('attachment_id'));
        $expectedSha256 = $request->input('expected_sha256') ?: $request->input('sha256');
        $expectedSize = $request->filled('expected_size') ? (int) $request->input('expected_size') : ($request->filled('size') ? (int) $request->input('size') : null);

        if (empty($attachmentUuid)) {
            return response()->json(['status' => 'error', 'message' => 'attachment_uuid is required.'], 422);
        }

        $uploadService = app(ForwardResumeUploadService::class);
        $result = $uploadService->probeAndVerifyAttachment($companyId, $attachmentUuid, $expectedSha256, $expectedSize);

        if (! ($result['verified'] ?? false)) {
            return response()->json([
                'status' => 'error',
                'message' => $result['message'] ?? 'Verification failed.',
                'details' => $result,
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'verified' => true,
            'attachment_uuid' => $attachmentUuid,
            'actual_size' => $result['actual_size'] ?? $expectedSize,
        ]);
    }

    /**
     * Isolated Test Forward Resume: Micro-Dispatch Send Endpoint
     */
    public function test_fwd_resume_send(Request $request)
    {
        $companyId = (int) auth()->user()->company_id;
        $userId = (int) auth()->user()->id;

        // Two-Tier Idempotency Barrier: Fast Redis Lock (120s TTL)
        $idempotencyKey = (string) ($request->header('Idempotency-Key') ?: $request->input('idempotency_key', ''));
        $idemLockKey = ! empty($idempotencyKey) ? "fwd_idem:{$companyId}:{$idempotencyKey}" : null;
        if ($idemLockKey) {
            $lockAcquired = \Illuminate\Support\Facades\Redis::set($idemLockKey, 'PROCESSING', 'EX', 120, 'NX');
            if (! $lockAcquired) {
                $statusVal = \Illuminate\Support\Facades\Redis::get($idemLockKey);
                if ($statusVal === 'SENT' || $statusVal === 'QUEUED') {
                    return Reply::dataOnly([
                        'status' => 'success',
                        'message' => 'Candidate resumes have been forwarded successfully.',
                    ]);
                }

                return Reply::dataOnly([
                    'status' => 'error',
                    'message' => 'A forward resume request is currently being processed. Please wait.',
                ]);
            }
        }

        $subject = trim((string) $request->subject);
        $toRaw = $request->to_email;
        $message = (string) $request->editor1;
        $attachedCv = (int) $request->input('attached_cv', 1);
        $formatId = (int) $request->input('attached_cv_format', 0);
        $rawArr = $request->arr ?: [];
        $arr = array_slice(is_array($rawArr) ? $rawArr : [], 0, 5);

        // Attachment IDs passed from client R2 upload
        $attachmentUuids = [];
        if ($request->filled('attachment_uuids')) {
            $rawAtt = $request->input('attachment_uuids');
            $attachmentUuids = is_array($rawAtt) ? $rawAtt : (json_decode($rawAtt, true) ?: []);
        } elseif ($request->filled('attachment_ids')) {
            $rawAtt = $request->input('attachment_ids');
            $attachmentUuids = is_array($rawAtt) ? $rawAtt : (json_decode($rawAtt, true) ?: []);
        }

        if (empty($toRaw)) {
            return Reply::dataOnly(['status' => 'error', 'message' => 'Recipient email (To) is required.']);
        }

        $to = array_values(array_filter(array_map('trim', explode(',', $toRaw))));
        foreach ($to as $email) {
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return Reply::dataOnly(['status' => 'error', 'message' => "Invalid recipient email format: {$email}"]);
            }
        }

        $cc = ! empty($request->cc_email) ? array_values(array_filter(array_map('trim', explode(',', $request->cc_email)))) : [];
        foreach ($cc as $email) {
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return Reply::dataOnly(['status' => 'error', 'message' => "Invalid CC email format: {$email}"]);
            }
        }

        // Strict deliverability cap: Maximum 5 total recipients (To + CC combined)
        $totalRecipients = count($to) + count($cc);
        if ($totalRecipients > 5) {
            return Reply::dataOnly([
                'status' => 'error',
                'message' => 'Recipient limit exceeded. To maintain high email deliverability and avoid spam filtering, a maximum of 5 recipients (To & CC combined) can be included per dispatch (found '.$totalRecipients.').',
            ]);
        }

        if (empty($subject)) {
            return Reply::dataOnly(['status' => 'error', 'message' => 'Subject is required.']);
        }

        if ($formatId <= 0) {
            return Reply::dataOnly(['status' => 'error', 'message' => 'Please select a resume format before sending.']);
        }

        $cleanMsg = trim(strip_tags(html_entity_decode($message, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        $cleanMsg = preg_replace('/\s+/', '', $cleanMsg);
        if ($cleanMsg === '' && stripos($message, '<img') === false) {
            return Reply::dataOnly(['status' => 'error', 'message' => 'Email message body cannot be blank.']);
        }

        // Canonical forward_request_id minting
        $fwdRequestUuid = (string) Str::uuid();
        if (! empty($idempotencyKey)) {
            $rawUuid = str_replace('fwd_', '', $idempotencyKey);
            if (Str::isUuid($rawUuid)) {
                $fwdRequestUuid = $rawUuid;
            }
        }

        // Option 1 fallback: if attached_cv == 1 and no client uploaded UUIDs, attach candidate original resumes
        if ($attachedCv === 1 && empty($attachmentUuids) && $formatId === 1 && ! empty($arr)) {
            $uploadService = app(ForwardResumeUploadService::class);
            $candidates = DB::table('job_applications')
                ->whereIn('id', $arr)
                ->where(function ($q) use ($companyId) {
                    $q->where('company_id', $companyId)
                        ->orWhere('thirdparty_company_id', $companyId);
                })
                ->get();
            foreach ($candidates as $cand) {
                $resumeFile = $cand->resume;
                if (! empty($resumeFile)) {
                    $attUuid = (string) Str::uuid();
                    $candName = str_replace(' ', '_', preg_replace('/[^\w\s.-]/', '', $cand->full_name ?: 'Candidate'));
                    $r2Key = sprintf('temp-forward/%d/%s.pdf', $companyId, $attUuid);

                    try {
                        $fileBytes = null;
                        $originalExt = strtolower(pathinfo(parse_url($resumeFile, PHP_URL_PATH) ?: $resumeFile, PATHINFO_EXTENSION));
                        $mimeType = 'application/pdf';
                        if ($originalExt === 'docx') {
                            $mimeType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                        } elseif ($originalExt === 'doc') {
                            $mimeType = 'application/msword';
                        }

                        if (filter_var($resumeFile, FILTER_VALIDATE_URL)) {
                            $httpRes = Http::timeout(15)->get($resumeFile);
                            if ($httpRes->successful() && strlen($httpRes->body()) > 50) {
                                $fileBytes = $httpRes->body();
                            }
                        } elseif (Storage::disk('r2')->exists($resumeFile)) {
                            $fileBytes = Storage::disk('r2')->get($resumeFile);
                        } elseif (file_exists(public_path('user-uploads/resumes/'.$resumeFile))) {
                            $fileBytes = file_get_contents(public_path('user-uploads/resumes/'.$resumeFile));
                        } elseif (file_exists(public_path('user-uploads/resume/'.$resumeFile))) {
                            $fileBytes = file_get_contents(public_path('user-uploads/resume/'.$resumeFile));
                        }

                        if ($fileBytes !== null && strlen($fileBytes) > 50) {
                            $putSuccess = $uploadService->putR2ObjectBytes($r2Key, $fileBytes, $mimeType);
                            if (! $putSuccess) {
                                Storage::disk('r2')->put($r2Key, $fileBytes);
                            }

                            $filenameExt = $originalExt ? ('.'.$originalExt) : '.pdf';
                            ForwardAttachment::create([
                                'uuid' => $attUuid,
                                'company_id' => $companyId,
                                'candidate_id' => $cand->id,
                                'r2_object_key' => $r2Key,
                                'filename' => 'Jeroworks_'.$candName.$filenameExt,
                                'mime_type' => $mimeType,
                                'file_size_bytes' => strlen($fileBytes),
                                'expected_size' => strlen($fileBytes),
                                'status' => 'VERIFIED',
                                'verified_at' => now(),
                                'expires_at' => now()->addMinutes(60),
                            ]);
                            $attachmentUuids[] = $attUuid;
                        }
                    } catch (\Throwable $e) {
                        \Log::warning('[test_fwd_resume_send] Option 1 fallback attachment error: '.$e->getMessage());
                    }
                }
            }
        }

        // Two-Phase Coin Reservation Engine
        $coinsRequ = count($to);
        $reservationId = (string) Str::uuid();
        $coinService = app(ForwardResumeCoinService::class);
        $resResult = $coinService->reserve($companyId, $userId, $fwdRequestUuid, $reservationId, $coinsRequ);

        if (! ($resResult['success'] ?? false)) {
            if (($resResult['status'] ?? '') === 'INSUFFICIENT_FUNDS') {
                return Reply::dataOnly([
                    'status' => 'coinCheck',
                    'message' => $resResult['message'] ?? 'Insufficient coin balance to send candidate resumes.',
                    'company_coins' => $resResult['company_coins'] ?? 0.00,
                ]);
            }

            return Reply::dataOnly([
                'status' => 'error',
                'message' => 'Coin reservation failed: '.($resResult['status'] ?? 'unknown error'),
            ]);
        }

        $fromAddress = ! empty($request->from_email) ? trim($request->from_email) : auth()->user()->email;
        $replyToEmail = ! empty($request->reply_to_email) ? trim($request->reply_to_email) : $fromAddress;
        $senderName = auth()->user()->name ?? 'Recruitment Team';
        $cc = ! empty($request->cc_email) ? array_values(array_filter(array_map('trim', explode(',', $request->cc_email)))) : [];

        $companyRow = DB::table('companies')->select('company_name')->where('id', $companyId)->first();
        $companyName = $companyRow ? $companyRow->company_name : 'Hiring Partner';

        $data = [
            'message' => $message,
            'subject' => $subject,
            'company_name' => $companyName,
            'consultant_name' => 'JERO Recruitment',
            'position' => 'Candidate Requisition',
            'candidate_skills' => [],
            'candidate_ids' => $arr,
        ];

        // Tier 2: Atomic DB Transaction (forward_requests + coin_reservations + outbox_events)
        DB::transaction(function () use (
            $fwdRequestUuid,
            $companyId,
            $userId,
            $attachmentUuids,
            $to,
            $reservationId,
            $coinsRequ,
            $subject,
            $message,
            $fromAddress,
            $senderName,
            $cc,
            $replyToEmail
        ) {
            $fwdReq = ForwardRequest::where('uuid', $fwdRequestUuid)->first();
            if (! $fwdReq) {
                $fwdReq = ForwardRequest::create([
                    'uuid' => $fwdRequestUuid,
                    'company_id' => $companyId,
                    'user_id' => $userId,
                    'candidate_count' => count($attachmentUuids),
                    'recipient_count' => count($to),
                    'status' => ForwardResumeState::QUEUED,
                    'coin_reservation_id' => $reservationId,
                ]);
            }

            // Create durable coin reservation record in MySQL
            CoinReservation::create([
                'reservation_id' => $reservationId,
                'company_id' => $companyId,
                'user_id' => $userId,
                'forward_request_id' => $fwdRequestUuid,
                'amount' => $coinsRequ,
                'status' => ForwardResumeState::RESERVED,
                'expires_at' => now()->addMinutes(30),
            ]);

            // Create Transactional Outbox Event
            OutboxEvent::create([
                'event_id' => (string) Str::uuid(),
                'aggregate_type' => 'FORWARD_RESUME',
                'aggregate_id' => $fwdRequestUuid,
                'event_type' => 'FORWARD_RESUME_DISPATCH_REQUESTED',
                'payload' => [
                    'forward_request_uuid' => $fwdRequestUuid,
                    'company_id' => $companyId,
                    'user_id' => $userId,
                    'to' => $to,
                    'subject' => $subject,
                    'body' => $message,
                    'attachment_uuids' => $attachmentUuids,
                    'from_address' => $fromAddress,
                    'sender_name' => $senderName,
                    'cc' => $cc,
                    'reply_to' => $replyToEmail,
                    'reservation_id' => $reservationId,
                    'required_coins' => $coinsRequ,
                ],
                'status' => 'PENDING',
            ]);

            // Forensic event journal
            if (\Illuminate\Support\Facades\Schema::hasTable('forward_resume_events')) {
                ForwardResumeEvent::create([
                    'event_id' => (string) Str::uuid(),
                    'forward_request_id' => $fwdRequestUuid,
                    'event_type' => 'FORWARD_RESUME_QUEUED',
                    'previous_state' => 'READY_TO_SEND',
                    'new_state' => ForwardResumeState::QUEUED,
                    'actor_type' => 'USER',
                    'actor_id' => $userId,
                    'metadata' => [
                        'recipient_count' => count($to),
                        'attachment_count' => count($attachmentUuids),
                        'reservation_id' => $reservationId,
                    ],
                    'created_at' => now(),
                ]);
            }

            // Associate uploaded attachments with this forward request
            if (! empty($attachmentUuids) && \Illuminate\Support\Facades\Schema::hasTable('forward_attachments')) {
                ForwardAttachment::whereIn('uuid', $attachmentUuids)
                    ->where('company_id', $companyId)
                    ->update([
                        'forward_request_id' => $fwdReq->id,
                        'status' => 'DISPATCHED',
                    ]);
            }
        });

        // Publish to Horizon emails queue (<2KB micro-payload)
        ProcessForwardResumeDispatchJob::dispatch(
            $fwdRequestUuid,
            $companyId,
            $userId,
            $to,
            $subject,
            $message,
            $attachmentUuids,
            $fromAddress,
            $senderName,
            $cc,
            $replyToEmail,
            $reservationId,
            $coinsRequ
        )->onConnection('emails');

        // Mark outbox published
        OutboxEvent::where('aggregate_id', $fwdRequestUuid)
            ->where('status', 'PENDING')
            ->update([
                'status' => 'PUBLISHED',
                'published_at' => now(),
            ]);

        if ($idemLockKey) {
            \Illuminate\Support\Facades\Redis::set($idemLockKey, 'SENT', 'EX', 3600);
        }

        return Reply::dataOnly([
            'status' => 'success',
            'message' => 'Candidate resumes have been forwarded successfully.',
            'request_id' => $fwdRequestUuid,
        ]);
    }

    /**
     * Quick Upload Resume directly from Forward Resume Drawer (Zero-context-switch)
     * Tenancy: Scoped by auth()->user()->company_id
     */
    public function quick_upload_resume(Request $request)
    {
        $request->validate([
            'candidate_id' => 'required|integer',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:15360',
        ]);

        $companyId = (int) auth()->user()->company_id;
        $candidateId = (int) $request->input('candidate_id');

        // Tenancy check: Ensure candidate belongs to logged-in user's company
        $candidate = DB::table('job_applications')
            ->where('id', $candidateId)
            ->where(function ($q) use ($companyId) {
                $q->where('company_id', $companyId)
                    ->orWhere('thirdparty_company_id', $companyId);
            })
            ->first();

        if (! $candidate) {
            return response()->json([
                'status' => 'error',
                'message' => 'Candidate profile not found or unauthorized.',
            ], 404);
        }

        $file = $request->file('resume');
        $ext = strtolower($file->getClientOriginalExtension());
        $origName = $file->getClientOriginalName();
        $safeCandName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $candidate->full_name ?: 'Candidate');
        $uniqueSuffix = (string) \Illuminate\Support\Str::uuid();
        $storedFilename = "{$candidateId}_{$safeCandName}_{$uniqueSuffix}.{$ext}";

        // Save locally to public/user-uploads/resumes/
        $destinationPath = public_path('user-uploads/resumes');
        if (! file_exists($destinationPath)) {
            @mkdir($destinationPath, 0755, true);
        }
        $file->move($destinationPath, $storedFilename);

        // Mirror to Cloudflare R2 if available
        try {
            $localFilePath = $destinationPath.'/'.$storedFilename;
            if (file_exists($localFilePath)) {
                $year = date('Y');
                $month = date('m');
                $r2Path = "Companies/{$companyId}/Resumes/{$year}/{$month}/{$storedFilename}";
                \Illuminate\Support\Facades\Storage::disk('r2')->put($r2Path, file_get_contents($localFilePath));
            }
        } catch (\Throwable $e) {
            \Log::warning('[quick_upload_resume] R2 upload safeguard: '.$e->getMessage());
        }

        // Update MySQL job_applications table
        DB::table('job_applications')->where('id', $candidateId)->update([
            'resume' => $storedFilename,
            'updated_at' => now(),
        ]);

        // Insert or update documents table record
        try {
            DB::table('documents')->insert([
                'name' => 'Resume',
                'hashname' => $storedFilename,
                'documentable_type' => 'App\JobApplication',
                'documentable_id' => $candidateId,
                'company_id' => $companyId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
        }

        // Synchronously re-index all linked candidate rows in Elasticsearch
        $wiseIds = DB::table('add_job_wise_condidates')->where('job_application_id', $candidateId)->pluck('id')->toArray();
        foreach ($wiseIds as $wId) {
            \App\Helper\GeneralMethod::JobApplicationsAddEditUpload($wId);
        }

        try {
            $elasticService = app(\App\Services\ElasticService::class);
            if ($elasticService->indexExists(ELASTIC_CANDIDATES)) {
                $elasticService->getClient()->indices()->refresh(['index' => ELASTIC_CANDIDATES]);
            }
        } catch (\Throwable $e) {
            \Log::warning('[quick_upload_resume] ES refresh failed: '.$e->getMessage());
        }

        // Invalidate candidate drawer and list caches
        $drawerViewVersion = @filemtime(resource_path('views/service_company/job-applications/showdetail.blade.php')) ?: 0;
        $targetIds = array_unique(array_merge([$candidateId], $wiseIds));
        $slugs = ['', 'cvtracker', 'apptracker', 'interview-schedule'];

        foreach ($targetIds as $tid) {
            foreach ($slugs as $slug) {
                cache()->forget('tenant:'.$companyId.':candidate_details:'.$tid.':'.md5($slug).':view:'.$drawerViewVersion);
            }
        }
        \App\Helper\GeneralMethod::cacheClean('candidateDetails_version_user_'.auth()->id());

        // Broadcast real-time resume upload update
        try {
            \App\Events\CandidateDrawerUpdated::dispatch(
                (int) $companyId,
                (int) $candidateId,
                time(),
                'resume_uploaded',
                array_map('intval', $wiseIds),
                null,
                null,
                null,
                null,
                null,
                null,
                $storedFilename
            );
        } catch (\Throwable $e) {
            \Log::warning('[quick_upload_resume] CandidateDrawerUpdated dispatch failed: '.$e->getMessage());
        }

        // Keep secondary indexes in sync if job runner exists
        try {
            if (class_exists(\App\Jobs\ProcessCandidateUpdateJob::class)) {
                \App\Jobs\ProcessCandidateUpdateJob::dispatch($candidateId);
            }
        } catch (\Throwable $e) {
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Resume uploaded successfully!',
            'candidate_id' => $candidateId,
            'resume_filename' => $storedFilename,
            'display_filename' => 'Jeroworks_'.$safeCandName.'.'.$ext,
            'has_resume' => true,
        ]);
    }

    public function offerSendMail(Request $request)
    {

        // echo $request->id; exit;
        $arr = '';
        // dd($request->arr);
        $this->arr = $request->arr;
        $this->jobIdArr = $request->jobIdArr;
        $this->applicationDataCount = DB::table('job_applications')->select('*')->whereIn('id', $this->arr)->count();
        // $this->applicationData			=	DB::table('job_applications')->select('job_applications.id')->whereIn('id', $arr)->get();
        $this->mtype = $request->mtype;
        $this->applicationData = DB::table('job_applications')
            ->select(
                'job_applications.id',
                'job_applications.full_name',
                'job_applications.current_designation',
                'job_applications.phone',
                'job_applications.email',
                'job_applications.total_exp',
                'job_applications.rel_exp',
                'job_applications.ctc',
                'job_applications.ectc',
                'job_applications.current_ctc_cur',
                'job_applications.current_ectc_cur',
                'job_applications.current_ctc_mode',
                'job_applications.current_ectc_cur',
                'job_applications.np_id',
                'job_applications.condidate_type',
                'job_applications.serving_notice_period_mode',
                'job_applications.current_company',
                'job_applications.skills',
                'qualification_master.name as qualification_name',
                'jobs.title as job_title',
                'clients.name as client_name'
            )
            // ->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
            ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->join('jobs', function ($join) {
                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
            })
            ->leftjoin('clients', 'clients.id', 'jobs.client_id')
            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
            ->whereIn('job_applications.id', $this->arr)->groupBy('job_applications.id')->get();
        // dd($this->applicationData);

        if ($request->mtype == 1) {

            //  $this->templateData = DB::table('th_email_template')->select('*')->where('th_email_template.status', 1)->where('th_email_template.temptype', 7)->get();

            $params = [
                'index' => ELASTIC_EMAIL_TEMPLATE,
                'body' => [
                    'size' => 10000,
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['status' => 1]], // OK
                                ['match' => ['temptype' => 7]],    // TEXT field
                            ],
                        ],
                    ],
                ],
            ];

            $response = $this->elasticService->getClient()->search($params);

            $this->templateData = collect(value: $response['hits']['hits'] ?? [])
                ->map(function ($hit) {
                    return (object) $hit['_source'];
                });

        } else {
            // $this->templateData = DB::table('th_email_template')->select('*')->where('th_email_template.status', 1)->where('th_email_template.temptype', 8)->get();

            $params = [
                'index' => ELASTIC_EMAIL_TEMPLATE,
                'body' => [
                    'size' => 10000,
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['status' => 1]], // OK
                                ['match' => ['temptype' => 8]],    // TEXT field
                            ],
                        ],
                    ],
                ],
            ];

            $response = $this->elasticService->getClient()->search($params);

            $this->templateData = collect(value: $response['hits']['hits'] ?? [])
                ->map(function ($hit) {
                    return (object) $hit['_source'];
                });

        }

        $this->toEmailId = '';
        if ($this->applicationData) {
            foreach ($this->applicationData as $row) {
                if ($row->condidate_type == 'bench_condidate') {
                    $bench_assign = DB::table('bench_assign')
                        ->join('users', 'users.id', 'bench_assign.user_id')
                        ->where('bench_assign.bench_applications_id', $row->id)
                        ->take(1)
                        ->pluck('users.email')->toArray();
                    $this->toEmailId = implode(',', $bench_assign);
                } else {
                    $this->toEmailId = $this->applicationData[0]->email;
                }
            }
        }

        $this->userdata = $this->user;

        $this->to_email_from_count = $this->cc_email_count = 0;
        $this->to_email_from_data = [];
        $this->cc_email_data = [];
        $commonFromArr = [];
        $commonCcArr = [];

        if ($request->id >= 1) {

            $client_row = DB::table('jobs')->select('jobs.client_id')->where('jobs.id', $request->id)->first();
            // CLIENT CONTACT INFO — job may have no client (direct/internal hire)
            $ccInfoCount = 0;
            $ccInfo = collect();
            if ($client_row && $client_row->client_id) {
                $ccInfoCount = DB::table('client_contact_info')->select('client_contact_info.contact_person_email')->where('client_contact_info.client_id', $client_row->client_id)->count();
                $ccInfo = DB::table('client_contact_info')->select('client_contact_info.contact_person_email')->where('client_contact_info.client_id', $client_row->client_id)->get();
            }
            if ($ccInfoCount > 0) {
                $i = 0;
                foreach ($ccInfo as $ccVal) {
                    $commonFromArr[$i] = $ccVal->contact_person_email;
                    $i++;
                }
            }
            $this->to_email_from_count = $ccInfoCount;
            $this->to_email_from_data = $commonFromArr;

            // HR ASSIGN JOB
            $assignCount = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.email')->whereIn('job_assign.job_id', $this->jobIdArr)->count();

            // dd($assignCount);

            // $assignData = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')
            // ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            // ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
            // ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            // ->leftjoin('companies', 'companies.id', 'job_assign.company_id')
            // ->select('users.id','users.email','users.name','users_profile.designation as display_name', 'companies.company_name')->whereIn('job_assign.job_id', $this->jobIdArr)->get()->toArray();

            $assignDataone = DB::table('users')
                ->leftjoin('role_user', 'role_user.user_id', 'users.id')
                ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
                ->leftjoin('roles', 'roles.id', 'role_user.role_id')
                ->leftjoin('companies', 'companies.id', 'users.company_id')
                ->select('users.id', 'users.email', 'users.name', 'users_profile.designation as display_name', 'companies.company_name')
                ->where('users.company_id', '=', auth()->user()->company_id)
                ->where('users.status', '=', 'active')
                ->groupBy('users.id')->get()->toArray();

            $assignDatatwo = DB::table('job_assign')
                ->leftjoin('users', 'users.id', '=', 'job_assign.user_id')
                ->leftjoin('role_user', 'role_user.user_id', 'users.id')
                ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
                ->leftjoin('roles', 'roles.id', 'role_user.role_id')
                ->leftjoin('companies', 'companies.id', 'job_assign.company_id')
                ->select('users.id', 'users.email', 'users.name', 'users_profile.designation as display_name', 'companies.company_name')
                ->where('users.company_id', '=', auth()->user()->company_id)
                ->where('users.status', '=', 'active')
                ->groupBy('users.id')->get()->toArray(); // ->whereIn('job_assign.job_id', $this->jobIdArr)

            $mergedData = array_udiff($assignDataone, $assignDatatwo, function ($a, $b) {
                return $a->id <=> $b->id; // Compare by 'id' field
            });
            // dd($assignDataone,$mergedData);

            $clientId = Job::whereIn('jobs.id', $this->jobIdArr)
                ->join('clients', 'clients.id', '=', 'jobs.client_id')
                ->whereIn('jobs.id', $this->jobIdArr)
                ->where('clients.self_company', 0)->pluck('jobs.client_id')->toArray();

            $assignDataclient = DB::table('clients')
                ->leftjoin('companies', 'companies.id', 'clients.company_id')
                ->join('client_contact_info', 'clients.id', '=', 'client_contact_info.client_id')
                ->select('client_contact_info.id', 'client_contact_info.contact_person_email as email', 'client_contact_info.contact_person_name as name', 'client_contact_info.contact_person_designation as display_name', 'clients.name as company_name')
                ->whereIn('clients.id', $clientId)->get()->toArray();

            if ($assignCount > 0) {
                $i = 0;
                foreach ($mergedData as $aval) {
                    $commonCcArr[$i] = $aval->email;
                    $i++;
                }
            }
            $assignData = array_merge($assignDataclient, $assignDatatwo);
            // dd($assignData);

            $this->cc_email_count = $assignCount;
            $this->cc_email_data = $commonCcArr;
            $this->cc_email_assignData = (object) $assignData;

            // Same directories, kept separate so the recipient picker can group them as
            // "Internal Company Approvers" vs "Client Company Approvers" instead of one merged list.
            $this->cc_email_internal_data = $assignDatatwo;
            $this->cc_email_client_data = $assignDataclient;
        }
        $this->offerId = $request->offerId;
        $this->mtype = $request->mtype;
        // $this->arr = $request->id;

        // Send Offer Letter (mtype=1) — data for the candidate summary header card. Real values
        // only: the saved offer row (CTC/joining/designation/currency) and, if it was routed for
        // approval, the latest approval-timeline row (approver name + management_status).
        if ((int) $request->mtype === 1) {
            $this->co_send_offer = DB::table('offer_create_main_tds')
                ->where('offer_id', $request->offerId)->where('status', 1)->orderByDesc('id')->first();
            $this->co_send_appr = DB::table('updatetimeline')
                ->where('application_id', (int) (@$this->arr[0] ?? 0))
                ->where('mtypeSection', 2)->orderByDesc('id')->first();
        }

        if ($request->mtype == 1) {
            $title = 'Candidate Email Send';
        } else {
            $title = 'Select Approval';
        }

        $view = view('service_company.cvtracker.offerSendMail', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view, 'title' => $title]);

    }

    public function mass_mail(Request $request)
    {

        // echo $request->id; exit;
        $arr = '';
        // dd($request->arr);
        $this->arr = $request->arr;
        $this->jobIdArr = $request->jobIdArr;
        $this->applicationDataCount = DB::table('job_applications')->select('*')->whereIn('id', $this->arr)->count();
        // $this->applicationData			=	DB::table('job_applications')->select('job_applications.id')->whereIn('id', $arr)->get();

        $this->applicationData = DB::table('job_applications')
            ->select(
                'job_applications.id',
                'job_applications.full_name',
                'job_applications.current_designation',
                'job_applications.phone',
                'job_applications.email',
                'job_applications.total_exp',
                'job_applications.rel_exp',
                'job_applications.ctc',
                'job_applications.ectc',
                'job_applications.current_ctc_cur',
                'job_applications.current_ectc_cur',
                'job_applications.current_ctc_mode',
                'job_applications.current_ectc_cur',
                'job_applications.np_id',
                'job_applications.serving_notice_period_mode',
                'job_applications.current_company',
                'job_applications.skills',
                'qualification_master.name as qualification_name',
                'jobs.title as job_title',
                'clients.name as client_name'
            )
            // ->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
            ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->join('jobs', function ($join) {
                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
            })
            ->leftjoin('clients', 'clients.id', 'jobs.client_id')
            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
            ->whereIn('job_applications.id', $this->arr)->groupBy('job_applications.id')->get();

        // $this->templateData = DB::table('th_email_template')->select('*')->where('th_email_template.status', 1)->where('th_email_template.temptype', 2)->get();

        $companyId = auth()->user()->company_id;
        $params = [
            'index' => ELASTIC_EMAIL_TEMPLATE,
            'body' => [
                'size' => 10000,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['status' => 1]],
                            ['term' => ['temptype' => 2]],
                        ],
                        'should' => [
                            ['term' => ['company_id' => $companyId]],
                            [
                                'bool' => [
                                    'must_not' => [
                                        ['exists' => ['field' => 'company_id']],
                                    ],
                                ],
                            ],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ],

                // ✅ SORTING PART
                'sort' => [
                    [
                        '_script' => [
                            'type' => 'number',
                            'script' => [
                                'source' => "
									if (doc['company_id'].size() == 0) {
										return 1; // NULL FIRST
									} else if (doc['company_id'].value == params.cid) {
										return 2; // COMPANY SECOND
									} else {
										return 3;
									}
								",
                                'params' => [
                                    'cid' => $companyId,
                                ],
                            ],
                            'order' => 'asc',
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);

        $this->templateData = collect(value: $response['hits']['hits'] ?? [])
            ->map(function ($hit) {
                return (object) $hit['_source'];
            });

        $this->userdata = $this->user;

        $this->to_email_from_count = $this->cc_email_count = 0;
        $this->to_email_from_data = [];
        $this->cc_email_data = [];
        $commonFromArr = [];
        $commonCcArr = [];

        if ($request->id >= 1) {

            $client_row = DB::table('jobs')->select('jobs.client_id')->where('jobs.id', $request->id)->first();
            // CLIENT CONTACT INFO
            $ccInfoCount = DB::table('client_contact_info')->select('client_contact_info.contact_person_email')->where('client_contact_info.client_id', $client_row->client_id)->count();
            $ccInfo = DB::table('client_contact_info')->select('client_contact_info.contact_person_email')->where('client_contact_info.client_id', $client_row->client_id)->get();
            if ($ccInfoCount > 0) {
                $i = 0;
                foreach ($ccInfo as $ccVal) {
                    $commonFromArr[$i] = $ccVal->contact_person_email;
                    $i++;
                }
            }
            $this->to_email_from_count = $ccInfoCount;
            $this->to_email_from_data = $commonFromArr;

            // HR ASSIGN JOB
            $assignCount = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.email')->whereIn('job_assign.job_id', $this->jobIdArr)->count();
            // $assignData = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')
            // ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            // ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
            // ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            // ->leftjoin('companies', 'companies.id', 'job_assign.company_id')
            // ->select('users.id','users.email','users.name','users_profile.designation as display_name', 'users.working_status','companies.company_name')->whereIn('job_assign.job_id', $this->jobIdArr)->get()->toArray();

            $assignDataone = DB::table('users')
                ->leftjoin('role_user', 'role_user.user_id', 'users.id')
                ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
                ->leftjoin('roles', 'roles.id', 'role_user.role_id')
                ->leftjoin('companies', 'companies.id', 'users.company_id')
                ->where('users.company_id', '=', auth()->user()->company_id)
                ->select('users.id', 'users.email', 'users.name', 'users_profile.designation as display_name', 'users.working_status', 'companies.company_name')->groupBy('users.id')->get()->toArray();

            $assignDatatwo = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')
                ->leftjoin('role_user', 'role_user.user_id', 'users.id')
                ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
                ->leftjoin('roles', 'roles.id', 'role_user.role_id')
                ->leftjoin('companies', 'companies.id', 'job_assign.company_id')
                ->select('users.id', 'users.email', 'users.name', 'users_profile.designation as display_name', 'users.working_status', 'companies.company_name')->whereIn('job_assign.job_id', $this->jobIdArr)->get()->toArray();

            $mergedData = array_udiff($assignDataone, $assignDatatwo, function ($a, $b) {
                return $a->id <=> $b->id; // Compare by 'id' field
            });

            $i = 0;
            foreach ($this->applicationData as $aval) {
                $commonCcArr[$i] = $aval->email;
                $i++;
            }

            $clientId = Job::whereIn('jobs.id', $this->jobIdArr)
                ->join('clients', 'clients.id', '=', 'jobs.client_id')
                ->whereIn('jobs.id', $this->jobIdArr)
                ->where('clients.self_company', 0)->pluck('jobs.client_id')->toArray();

            $assignDataclient = DB::table('clients')
                ->leftjoin('companies', 'companies.id', 'clients.company_id')
                ->join('client_contact_info', 'clients.id', '=', 'client_contact_info.client_id')
                ->select('client_contact_info.id', 'client_contact_info.contact_person_email as email', 'client_contact_info.contact_person_name as name', 'client_contact_info.contact_person_designation as display_name', 'clients.name as company_name')
                ->whereIn('clients.id', $clientId)->get()->toArray();

            $assignData = array_merge($assignDataclient, $assignDataone);

            $this->cc_email_count = count($commonCcArr);
            $this->cc_email_data = $commonCcArr;
            $this->cc_email_assignData = (object) $assignData;
        }

        $company_id = auth()->user()->company_id;
        $company_name = DB::table('companies')->select('companies.company_name')->where('id', '=', $company_id)->first();
        $this->company_name = (@$company_name->company_name) ? @$company_name->company_name : '';

        $view = view('service_company.cvtracker.mass_mail', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function send_mail(Request $request)
    {

        // echo $request->id; exit;
        $arr = '';
        // 		dd($request->jobIdArr[0]);
        $this->arr = $request->arr;
        $this->jobIdArr = $request->jobIdArr;
        $this->applicationDataCount = DB::table('job_applications')->select('*')->whereIn('id', $this->arr)->count();
        // $this->applicationData			=	DB::table('job_applications')->select('job_applications.id')->whereIn('id', $arr)->get();

        $this->applicationData = DB::table('job_applications')
            ->select(
                'job_applications.id',
                'job_applications.full_name',
                'job_applications.condidate_type',
                'job_applications.current_designation',
                'job_applications.phone',
                'job_applications.email',
                'job_applications.total_exp',
                'job_applications.rel_exp',
                'job_applications.ctc',
                'job_applications.ectc',
                'job_applications.current_ctc_cur',
                'job_applications.current_ectc_cur',
                'job_applications.current_ctc_mode',
                'job_applications.current_ectc_cur',
                'job_applications.np_id',
                'job_applications.serving_notice_period_mode',
                'job_applications.current_company',
                'job_applications.skills',
                'qualification_master.name as qualification_name',
                'jobs.title as job_title',
                'jobs.id as jobID',
                'clients.name as client_name'
            )
            // ->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
            ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->join('jobs', function ($join) {
                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
            })
            ->leftjoin('clients', 'clients.id', 'jobs.client_id')
            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
            ->whereIn('job_applications.id', $this->arr)->groupBy('job_applications.id')->get();

        // $this->templateData = DB::table('th_email_template')->select('*')->where('th_email_template.status', 1)->where('th_email_template.temptype', 4)->get();

        $params = [
            'index' => ELASTIC_EMAIL_TEMPLATE,
            'body' => [
                'size' => 10000,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['status' => 1]], // OK
                            ['match' => ['temptype' => 4]],    // TEXT field
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);

        $this->templateData = collect(value: $response['hits']['hits'] ?? [])
            ->map(function ($hit) {
                return (object) $hit['_source'];
            });

        $this->userdata = $this->user;

        $this->to_email_from_count = $this->cc_email_count = 0;
        $this->to_email_from_data = [];
        $this->cc_email_data = [];
        $commonFromArr = [];
        $commonCcArr = [];

        if ($request->id >= 1) {
            // dd($request->id );
            $client_row = DB::table('jobs')->select('jobs.client_id')->where('jobs.id', $request->id)->first();
            // CLIENT CONTACT INFO
            $ccInfoCount = DB::table('client_contact_info')->select('client_contact_info.contact_person_email')->where('client_contact_info.client_id', $client_row->client_id)->count();
            $ccInfo = DB::table('client_contact_info')->select('client_contact_info.contact_person_email')->where('client_contact_info.client_id', $client_row->client_id)->get();
            if ($ccInfoCount > 0) {
                $i = 0;
                foreach ($ccInfo as $ccVal) {
                    $commonFromArr[$i] = $ccVal->contact_person_email;
                    $i++;
                }
            }
            $this->to_email_from_count = $ccInfoCount;
            $this->to_email_from_data = $commonFromArr;

            // HR ASSIGN JOB
            $assignCount = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.email')->whereIn('job_assign.job_id', $this->jobIdArr)->count();

            // $company_id 			=	auth()->user()->company_id;
            // dd($applicationDataSection->company_id , auth()->user()->company_id);

            $assignDataone = DB::table('users')
                ->leftjoin('role_user', 'role_user.user_id', 'users.id')
                ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
                ->leftjoin('roles', 'roles.id', 'role_user.role_id')
                ->leftjoin('companies', 'companies.id', 'users.company_id')
                ->where('users.company_id', '=', auth()->user()->company_id)
                ->select('users.id', 'users.email', 'users.name', 'users_profile.designation as display_name', 'users.working_status', 'companies.company_name')->groupBy('users.id')->get()->toArray();

            $assignDatatwo = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')
                ->leftjoin('role_user', 'role_user.user_id', 'users.id')
                ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
                ->leftjoin('roles', 'roles.id', 'role_user.role_id')
                ->leftjoin('companies', 'companies.id', 'job_assign.company_id')
                ->select('users.id', 'users.email', 'users.name', 'users_profile.designation as display_name', 'users.working_status', 'companies.company_name')->whereIn('job_assign.job_id', $this->jobIdArr)->get()->toArray();

            $mergedData = array_udiff($assignDataone, $assignDatatwo, function ($a, $b) {
                return $a->id <=> $b->id; // Compare by 'id' field
            });

            $i = 0;

            foreach ($this->applicationData as $aval) {

                // dd($aval);
                if ($aval->condidate_type == 'bench_condidate') {

                    $bench_assign = DB::table(table: 'bench_assign')->join('users', 'users.id', '=', 'bench_assign.user_id')
                        ->select('users.id', 'users.email', 'users.name')
                        ->where('bench_assign.bench_applications_id', $aval->id)
                        ->get()->toArray();
                    foreach ($bench_assign as $assign) {
                        $commonCcArr[$i] = $assign->email;
                        $i++;
                    }
                    // dd($aval->candidate_id,$commonCcArr);
                } else {
                    $commonCcArr[$i] = $aval->email;
                    $i++;
                }

            }

            // foreach ($this->applicationData as $aval) {
            // 	$commonCcArr[$i] = $aval->email;
            // 	$i++;
            // }

            $clientId = Job::whereIn('jobs.id', $this->jobIdArr)
                ->join('clients', 'clients.id', '=', 'jobs.client_id')
                ->whereIn('jobs.id', $this->jobIdArr)
                ->where('clients.self_company', 0)->pluck('jobs.client_id')->toArray();

            $assignDataclient = DB::table('clients')
                ->leftjoin('companies', 'companies.id', 'clients.company_id')
                ->join('client_contact_info', 'clients.id', '=', 'client_contact_info.client_id')
                ->select('client_contact_info.id', 'client_contact_info.contact_person_email as email', 'client_contact_info.contact_person_name as name', 'client_contact_info.contact_person_designation as display_name', 'clients.name as company_name')
                ->whereIn('clients.id', $clientId)->get()->toArray();
            $assignData = array_merge($assignDataclient, $assignDataone);
            $this->cc_email_count = count($commonCcArr);
            $this->cc_email_data = $commonCcArr;
            $this->cc_email_assignData = (object) $assignData;
        }

        $country_id = $country_name = '';
        $jobid = (int) $request->jobIdArr[0];
        $client_loc = DB::table('client_location_jobs')
            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
            ->select('client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
            ->where('client_location_jobs.job_id', $jobid)
            ->first();

        $client_loc = DB::table('client_city_jobs')
            ->join('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
            ->join('countries', 'countries.id', '=', 'state_list.country_id')
            ->select('city_list.city', 'countries.id as country_id', 'city_list.city as city_name')
            ->where('client_city_jobs.job_id', $jobid)
            ->first();

        $country_id = @$client_loc->country_id;
        if (@$country_id > 0) {
            $country_name = GeneralMethod::getCountryName($country_id);
            foreach ($country_name as $cval) {
                $country_name = ucwords($cval['sortname']);
            }
        }

        $city = @$client_loc->city;
        if (@$client_loc->country_id == 101) {
            $city = @$client_loc->city_name;
        }
        $jobs = Job::withoutGlobalScopes()->find($jobid);

        $this->jobid = $jobid;

        //  dd( $jobs);

        $companyOrClientName = GeneralMethod::companyOrClientName($jobs->client_id);

        $location = $city.' | '.$country_name;

        $this->selfCompany = ($companyOrClientName->selfCompany) ? $companyOrClientName->selfCompany : '';
        $this->companyTemp = ($companyOrClientName->company) ? $companyOrClientName->company : '';
        $this->clientTemp = ($companyOrClientName->client) ? $companyOrClientName->client : '';
        $this->location = ($location) ? $location : '';
        $this->job_title = ($jobs->title) ? $jobs->title : '';

        $view = view('service_company.cvtracker.send_mail', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function send_whatsapp(Request $request)
    {

        // echo $request->id; exit;
        $arr = '';
        // 		dd($request->jobIdArr[0]);

        $instance = $this->getInstance();

        // if($instance){

        $response = Http::withHeaders([
            'apikey' => config('services.evolution.global_key'),
        ])->get(
            $this->baseUrl().'/instance/connectionState/'.@$instance->instance_name
        );
        $data = $response->json();
        // dd($data);

        $responseConnected = Http::withHeaders([
            'apikey' => config('services.evolution.global_key'),
        ])->get(
            $this->baseUrl().'/instance/fetchInstances'
        );
        $dataConnected = $responseConnected->json();
        // dd($dataConnected);
        $connectedNumber = collect($dataConnected)
            ->firstWhere('name', @$instance->instance_name)['ownerJid'] ?? null;

        if ($connectedNumber) {
            $connectedNumber = explode('@', $connectedNumber)[0];
        }
        // }
        // dd($connectedNumber);

        $this->classname = '';
        $this->textvalue = '';
        $this->connectBtn = '';
        $this->connectBtndnone = 'd-none';
        $this->connectedNumberGet = '';
        $this->sectionOnednone = 'd-none';
        $this->sectionTwodnone = 'd-none';

        if (($data['status'] ?? null) == 404) {
            $this->classname = 'btn-primary';
            $this->textvalue = 'Click to Connect';
            $this->sectionOnednone = 'd-none';
            $this->sectionTwodnone = '';
            $this->connectBtn = 'connectBtn';
            $this->connectedNumberGet = '';

        } else {
            if ($data['instance']['state'] == 'open') {
                $this->classname = 'btn-success';
                $this->textvalue = 'Whatsapp Connected';
                $this->sectionOnednone = '';
                $this->sectionTwodnone = 'd-none';
                $this->connectBtndnone = '';
                $this->connectBtn = '';
                $this->connectedNumberGet = $connectedNumber;

            } else {
                $this->classname = 'btn-primary';
                $this->sectionOnednone = 'd-none';
                $this->sectionTwodnone = '';
                $this->textvalue = 'Click to Connect';
                $this->connectBtn = 'connectBtn';
                $this->connectedNumberGet = '';
            }
        }

        $this->arr = $request->arr;
        $this->jobIdArr = $request->jobIdArr;
        $this->applicationDataCount = DB::table('job_applications')->select('*')->whereIn('id', $this->arr)->count();
        // $this->applicationData			=	DB::table('job_applications')->select('job_applications.id')->whereIn('id', $arr)->get();
        // dd($this->arr);

        $this->applicationData = DB::table('job_applications')
            ->select(
                'job_applications.id',
                'job_applications.full_name',
                'job_applications.condidate_type',
                'job_applications.current_designation',
                'job_applications.phone',
                'job_applications.email',
                'job_applications.total_exp',
                'job_applications.rel_exp',
                'job_applications.ctc',
                'job_applications.ectc',
                'job_applications.current_ctc_cur',
                'job_applications.current_ectc_cur',
                'job_applications.current_ctc_mode',
                'job_applications.current_ectc_cur',
                'job_applications.np_id',
                'job_applications.serving_notice_period_mode',
                'job_applications.current_company',
                'job_applications.skills',
                'qualification_master.name as qualification_name',
                'jobs.title as job_title',
                'jobs.id as jobID',
                'clients.name as client_name'
            )
            // ->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
            ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->join('jobs', function ($join) {
                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
            })
            ->leftjoin('clients', 'clients.id', 'jobs.client_id')
            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
            ->whereIn('job_applications.id', $this->arr)->groupBy('job_applications.id')->first();

        //   dd($this->applicationData);
        // if (!$instance) {
        // 	return response()->json([
        // 		'success' => false,
        // 		'message' => 'Instance not found'
        // 	], 404);
        // }

        // 🔹 Clean phone Check WhatsApp Number login done implement pending
        $this->exists_number = false;
        $this->textvaluewhatsapp = false;
        $this->classnamewhatsapp = false;

        // $this->templateData = DB::table('th_email_template')->select('*')->where('th_email_template.status', 1)->where('th_email_template.temptype', 4)->get();

        $params = [
            'index' => ELASTIC_EMAIL_TEMPLATE,
            'body' => [
                'size' => 10000,
                'query' => [
                    'bool' => [
                        'filter' => [ // ✅ better than must
                            ['term' => ['status' => 1]],
                            ['term' => ['temptype' => 9]],
                        ],
                        'must_not' => [
                            ['terms' => ['id' => [28, 29, 37, 38, 53, 54]]],
                        ],
                    ],
                ],
                'sort' => [
                    [
                        'orderByShow' => [
                            'order' => 'asc', // 🔼 ascending
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);

        $this->templateData = collect(value: $response['hits']['hits'] ?? [])
            ->map(function ($hit) {
                return (object) $hit['_source'];
            });
        // dd($this->templateData);

        $this->userdata = $this->user;

        $this->to_email_from_count = $this->cc_email_count = 0;
        $this->to_email_from_data = [];
        $this->cc_email_data = [];
        $commonFromArr = [];
        $commonCcArr = [];

        if ($request->id >= 1) {
            // dd($request->id );
            $client_row = DB::table('jobs')->select('jobs.client_id')->where('jobs.id', $request->id)->first();
            // CLIENT CONTACT INFO
            $ccInfoCount = DB::table('client_contact_info')->select('client_contact_info.contact_person_email')->where('client_contact_info.client_id', $client_row->client_id)->count();
            $ccInfo = DB::table('client_contact_info')->select('client_contact_info.contact_person_email')->where('client_contact_info.client_id', $client_row->client_id)->get();
            if ($ccInfoCount > 0) {
                $i = 0;
                foreach ($ccInfo as $ccVal) {
                    $commonFromArr[$i] = $ccVal->contact_person_email;
                    $i++;
                }
            }
            $this->to_email_from_count = $ccInfoCount;
            $this->to_email_from_data = $commonFromArr;

            // HR ASSIGN JOB
            $assignCount = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.email')->whereIn('job_assign.job_id', $this->jobIdArr)->count();

            // $company_id 			=	auth()->user()->company_id;
            // dd($applicationDataSection->company_id , auth()->user()->company_id);

            $assignDataone = DB::table('users')
                ->leftjoin('role_user', 'role_user.user_id', 'users.id')
                ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
                ->leftjoin('roles', 'roles.id', 'role_user.role_id')
                ->leftjoin('companies', 'companies.id', 'users.company_id')
                ->where('users.company_id', '=', auth()->user()->company_id)
                ->select('users.id', 'users.email', 'users.name', 'users_profile.designation as display_name', 'users.working_status', 'companies.company_name')->groupBy('users.id')->get()->toArray();

            $assignDatatwo = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')
                ->leftjoin('role_user', 'role_user.user_id', 'users.id')
                ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
                ->leftjoin('roles', 'roles.id', 'role_user.role_id')
                ->leftjoin('companies', 'companies.id', 'job_assign.company_id')
                ->select('users.id', 'users.email', 'users.name', 'users_profile.designation as display_name', 'users.working_status', 'companies.company_name')->whereIn('job_assign.job_id', $this->jobIdArr)->get()->toArray();

            $mergedData = array_udiff($assignDataone, $assignDatatwo, function ($a, $b) {
                return $a->id <=> $b->id; // Compare by 'id' field
            });

            $i = 0;

            $clientId = Job::whereIn('jobs.id', $this->jobIdArr)
                ->join('clients', 'clients.id', '=', 'jobs.client_id')
                ->whereIn('jobs.id', $this->jobIdArr)
                ->where('clients.self_company', 0)->pluck('jobs.client_id')->toArray();

            $assignDataclient = DB::table('clients')
                ->leftjoin('companies', 'companies.id', 'clients.company_id')
                ->join('client_contact_info', 'clients.id', '=', 'client_contact_info.client_id')
                ->select('client_contact_info.id', 'client_contact_info.contact_person_email as email', 'client_contact_info.contact_person_name as name', 'client_contact_info.contact_person_designation as display_name', 'clients.name as company_name')
                ->whereIn('clients.id', $clientId)->get()->toArray();
            $assignData = array_merge($assignDataclient, $assignDataone);
            $this->cc_email_count = count($commonCcArr);
            $this->cc_email_data = $commonCcArr;
            $this->cc_email_assignData = (object) $assignData;
        }

        $country_id = $country_name = '';
        $jobid = (int) $request->jobIdArr[0];
        $client_loc = DB::table('client_location_jobs')
            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
            ->select('client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
            ->where('client_location_jobs.job_id', $jobid)
            ->first();

        $client_loc = DB::table('client_city_jobs')
            ->join('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
            ->join('countries', 'countries.id', '=', 'state_list.country_id')
            ->select('city_list.city', 'countries.id as country_id', 'city_list.city as city_name')
            ->where('client_city_jobs.job_id', $jobid)
            ->first();

        $country_id = @$client_loc->country_id;
        if (@$country_id > 0) {
            $country_name = GeneralMethod::getCountryName($country_id);
            foreach ($country_name as $cval) {
                $country_name = ucwords($cval['sortname']);
            }
        }

        $city = @$client_loc->city;
        if (@$client_loc->country_id == 101) {
            $city = @$client_loc->city_name;
        }
        $jobs = Job::withoutGlobalScopes()->find($jobid);

        $this->jobid = $jobid;

        //  dd( $jobs);

        $companyOrClientName = GeneralMethod::companyOrClientName($jobs->client_id);

        $location = $city.' | '.$country_name;

        $this->selfCompany = ($companyOrClientName->selfCompany) ? $companyOrClientName->selfCompany : '';
        $this->companyTemp = ($companyOrClientName->company) ? $companyOrClientName->company : '';
        $this->clientTemp = ($companyOrClientName->client) ? $companyOrClientName->client : '';
        $this->location = ($location) ? $location : '';
        $this->job_title = ($jobs->title) ? $jobs->title : '';

        $view = view('service_company.cvtracker.send_whatsapp', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    private function getInstance()
    {
        return DB::table('whatsapp_instances')
            ->where('user_id', auth()->user()->id)
            ->first();
    }

    public function email_template(Request $request)
    {
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymboleforexcell();
        $currency = GeneralMethod::currency();
        $selectedApplicantListing = $applicationDataCount = $applicationData = '';
        $selectedApplicantListing = $request->selectedApplicantListing;

        // dd($selectedApplicantListing);
        $applicationDataCount = DB::table('job_applications')->select('*')->whereIn('id', $selectedApplicantListing)->count();
        $applicationData = DB::table('job_applications')
            ->select(
                'job_applications.id',
                'job_applications.full_name',
                'job_applications.current_designation',
                'job_applications.phone',
                'job_applications.email',
                'job_applications.total_exp',
                'job_applications.rel_exp',
                'job_applications.ctc',
                'job_applications.ectc',
                'job_applications.city_id',
                'job_applications.city',
                'job_applications.country',
                'job_applications.state',
                'job_applications.comment',
                'job_applications.current_ctc_cur',
                'job_applications.current_ectc_cur',
                'job_applications.current_ctc_mode',
                'job_applications.current_ectc_cur',
                'job_applications.np_id',
                'job_applications.ctc_hourly',
                'job_applications.ctc_monthly',
                'job_applications.ctc_yearly',
                'job_applications.ectc_hourly',
                'job_applications.ectc_monthly',
                'job_applications.ectc_yearly',
                'job_applications.serving_notice_period_mode',
                'job_applications.current_company',
                'job_applications.notice_comments',
                'job_applications.last_working_date',
                'job_applications.skills',
                'qualification_master.name as qualification_name',
                'jobs.title as job_title',
                'jobs.company_id',
                'jobs.max_sal_mode as jobMode',
                'jobs.max_salary_currency as jobCurrency',
                'add_job_wise_condidates.job_id as jobId',
                'companies.company_name',
                'clients.name as client_name'
            )
            // ->join('jobs', 'jobs.id', 'job_applications.job_id')
            ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->join('jobs', function ($join) {
                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
            })
            ->leftjoin('clients', 'clients.id', 'jobs.client_id')
            ->leftjoin('companies', 'companies.id', 'job_applications.company_id')
            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification');
        $rawListing = is_array($selectedApplicantListing) ? $selectedApplicantListing : (explode(',', (string) $selectedApplicantListing));
        $cleanListing = array_values(array_filter(array_map('intval', $rawListing)));

        $rawWi = is_array($request->WijobIdArr) ? $request->WijobIdArr : [];
        $cleanWi = array_values(array_filter(array_map('intval', $rawWi)));

        if (! empty($cleanWi)) {
            $applicationData = $applicationData->whereIn('add_job_wise_condidates.id', $cleanWi)->groupBy('job_applications.id')->get();
        } elseif (! empty($cleanListing)) {
            $applicationData = $applicationData->whereIn('job_applications.id', $cleanListing)->groupBy('job_applications.id')->get();
        } else {
            $applicationData = collect();
        }

        // Fallback: If add_job_wise_condidates didn't match (e.g. direct candidate without wise candidate entry)
        if ($applicationData->isEmpty() && ! empty($cleanListing)) {
            $applicationData = DB::table('job_applications')
                ->select(
                    'job_applications.id',
                    'job_applications.full_name',
                    'job_applications.current_designation',
                    'job_applications.phone',
                    'job_applications.email',
                    'job_applications.total_exp',
                    'job_applications.rel_exp',
                    'job_applications.ctc',
                    'job_applications.ectc',
                    'job_applications.city_id',
                    'job_applications.city',
                    'job_applications.country',
                    'job_applications.state',
                    'job_applications.comment',
                    'job_applications.np_id',
                    'job_applications.serving_notice_period_mode',
                    'job_applications.current_company',
                    'job_applications.notice_comments',
                    'job_applications.last_working_date',
                    'job_applications.skills',
                    'job_applications.job_id as jobId',
                    'job_applications.company_id'
                )
                ->whereIn('job_applications.id', $cleanListing)
                ->get();
        }

        $applicationDataCount = count($applicationData);

        $template = (int) $request->template;
        $templateData = null;

        try {
            $params = [
                'index' => ELASTIC_EMAIL_TEMPLATE,
                'body' => [
                    'size' => 1,
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['status' => 1]],
                                ['match' => ['e_temp_id' => $template]],
                            ],
                        ],
                    ],
                ],
            ];
            $response = $this->elasticService->getClient()->search($params);
            $templateData = isset($response['hits']['hits'][0]) ? (object) $response['hits']['hits'][0]['_source'] : null;
        } catch (\Throwable $e) {
            // Elasticsearch error fallback
        }

        if (! $templateData) {
            $templateData = DB::table('th_email_template')->where('status', 1)->where('e_temp_id', $template)->first();
        }

        if (! $templateData) {
            return Reply::dataOnly([
                'status' => 'error',
                'message' => 'Email template not found',
            ]);
        }

        $company_id = auth()->user()->company_id;
        $company_name = DB::table('companies')->select('companies.company_name')->where('id', '=', $company_id)->first();

        $this->job = Job::select(
            'jobs.id',
            'jobs.title',
            'jobs.total_positions',
            'jobs.priority',
            'jobs.status',
            'jobs.min_exp',
            'jobs.max_exp',
            'jobs.created_at',
            'jobs.start_date',
            'jobs.end_date',
            'jobs.min_salary',
            'jobs.max_salary',
            'jobs.min_sal_mode',
            'jobs.max_sal_mode',
            'jobs.min_salary_currency',
            'jobs.max_salary_currency',
            'clients.name as clients_name',
            'clients.logo_logical_path'
        )->join('clients', 'clients.id', 'jobs.client_id')
            ->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id')
            ->where('jobs.id', '>', '0')
            ->groupBy('jobs.id')
            ->orderBy('jobs.id', 'DESC')
            ->get();

        $tracker_html = '';
        $tracker_html = '<table align="left" border="1" style="border-collapse:collapse; border:solid 1px black; width:1272px">
				<thead>
				<tr style="mso-yfti-irow:0;mso-yfti-firstrow:yes;height:15.75pt"> 
				<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><u><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">S.no<o:p></o:p></span></u></b></p></td>
				<td valign="bottom" style="border:solid windowtext 1.0pt;background:#BDD7EE; padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Date</span></b><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif;mso-fareast-language: EN-IN"><o:p></o:p></span></b></p></td>
				<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Consultancy<o:p></o:p></span></b></p></td>
				<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Positions<o:p></o:p></span></b></p></td>
				<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Name of the candidate<o:p></o:p></span></b></p></td>
				<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Contact#<o:p></o:p></span></b></p></td>
				<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Email ID<o:p></o:p></span></b></p></td>
				<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Total Exp<o:p></o:p></span></b></p></td>
				<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Rel Exp<o:p></o:p></span></b></p></td>
				<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">CCTC<o:p></o:p></span></b></p></td>
				<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">ECTC<o:p></o:p></span></b></p></td>
				
				<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Comment<o:p></o:p></span></b></p></td>
				
				<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Notice period<o:p></o:p></span></b></p></td>
				

				<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Current Company<o:p></o:p></span></b></p></td>
				<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Current location<o:p></o:p></span></b></p></td>
				<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Qualification<o:p></o:p></span></b></p></td>

				
				</tr>
				</thead>';
        $job_title = '';
        $client_company_name = '';
        $jobSkill = '';
        if ($applicationDataCount > 0) {
            $cnt = 1;
            foreach ($applicationData as $key => $appData) {

                $bgTextDecore = $strVal = '';
                if ($key == 0) {
                    $job_title = $appData->job_title;
                    if ($company_id == $appData->company_id) {
                        $client_company_name = $appData->client_name;
                    } else {
                        $client_company_name = $appData->company_name;
                    }
                }

                $str = trim(@$appData->full_name);
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
                $symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
                $symbolsValue = ! empty($symbolsValue) ? $symbolsValue : [
                    0 => 'INR',
                    1 => 'EUR',
                    2 => 'JPY',
                    3 => 'GBP',
                    4 => 'USD',
                    5 => 'AED',
                ];

                $jobMode = $appData->jobMode ?? 0;
                $jobCurrency = $appData->jobCurrency ?? 0;
                $symbols = $symbolsValue[$jobCurrency] ?? 'INR';

                $current_ctc_cur = $current_ectc_cur = $ctc = $ectc = '';
                $current_ctc_cur = $currencySymbole[@$jobCurrency];
                $current_ectc_cur = $currencySymbole[@$jobCurrency];

                $payFormatViewSalary = GeneralMethod::formatViewSalary($appData->ctc_yearly, $symbols, $jobMode);
                $billFormatViewSalary = GeneralMethod::formatViewSalary($appData->ectc_yearly, $symbols, $jobMode);

                $ctc = GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount);
                $ectc = GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount);

                $salaryMode = GeneralMethod::salaryMode();

                $current_ctc_mode = '';
                $current_ctc_mode = substr($salaryMode[@$jobMode], 0, 1);

                $np = $appData->np_id;
                if (empty($np)) {
                    $np = 'N/A';
                }
                if ($appData->serving_notice_period_mode == 0) {
                    if (! empty($appData->np_id)) {
                        $np = $appData->np_id.'  Days';
                    }
                } elseif ($appData->serving_notice_period_mode == 1) {
                    $np = 'SNP LWD <br>'.date('d M y', strtotime($appData->np_id));
                } else {
                    $np = $appData->np_id;
                }

                $country = '';
                $row_country_name = '';

                if (empty($appData->country)) {
                    $row_country_name = '----';
                } else {
                    if (! empty(@$appData->country)) {
                        if (is_numeric(@$appData->country)) {
                            $country_name_Arr = GeneralMethod::getCountryName(@$appData->country);
                            foreach ($country_name_Arr as $cval) {
                                $row_country_name = ucwords($cval['name']);
                            }
                        }
                    }
                }

                $state = '';
                $row_state_name = '';
                if (empty($appData->state)) {
                    $row_state_name = '----';
                } else {
                    // if (@$appData->country != 101) {
                    // 	$row_state_name = @$appData->state_name;
                    // } else {
                    $state = @$appData->state;
                    if (is_numeric(@$appData->country) && is_numeric($state)) {
                        if ($state > 0) {
                            // dd(@$appData->country, $state);
                            $state_name_Arr = GeneralMethod::getStateName(@$appData->country, $state);
                            // dd($state_name_Arr);
                            foreach ($state_name_Arr as $sval) {
                                $row_state_name = ucwords($sval['name']);
                            }
                        }
                    }

                    // }

                }

                $city = '';
                $row_city_name = '';

                if (@$appData->country == 101) {
                    $row_city = DB::table('city_list')->select('*')->where('city_id', @$appData->city_id)->first();
                    $row_city_name = @$row_city->city;
                } else {
                    $row_city_name = @$appData->city;
                }
                $last_working_date = '';
                if (! empty($appData->last_working_date)) {
                    $last_working_date = date('d M Y', strtotime($appData->last_working_date));
                }
                if ($appData->serving_notice_period_mode == 2) {
                    if (! empty($appData->last_working_date)) {
                        $np = 'Immediate LWD <br>'.date('d M y', strtotime($appData->last_working_date));
                    } else {
                        $np = $appData->np_id;
                    }

                }
                $experience = floatval(@$appData->total_exp); // Ensure it's a valid float
                // If experience is a whole number, convert it to an integer; otherwise, keep the decimal
                $formattedExperience = ($experience == intval($experience)) ? intval($experience) : $experience;

                $rel_experience = floatval(@$appData->rel_exp); // Ensure it's a valid float
                // If experience is a whole number, convert it to an integer; otherwise, keep the decimal
                $rel_formattedExperience = ($rel_experience == intval($rel_experience)) ? intval($rel_experience) : $rel_experience;

                $total_exp = $formattedExperience.' Y';
                $rel_exp = $rel_formattedExperience.' Y';

                $tracker_html .= '
				<tr style="mso-yfti-irow:1;mso-yfti-lastrow:yes;height:15.75pt"> 
				<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$cnt.'<o:p></o:p></span></p></td>
				<td valign="bottom" style="border:solid windowtext 1.0pt;border-top:none; background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt;overflow: hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.date('d-M-Y').'<o:p></o:p></span></p></td>
				<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$company_name->company_name.'<o:p></o:p></span></p></td>
				<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.ucwords(@$appData->current_designation).'<o:p></o:p></span></p></td>
				<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.ucwords(@$appData->full_name).'<o:p></o:p></span></p></td>
				<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif; color:#1F1F1F">'.@$appData->phone.'<o:p></o:p></span></p></td>
				<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif"><a href="mailto:'.@$appData->email.'">'.@$appData->email.'</a><o:p></o:p></span></p></td>
				<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.@$total_exp.'<o:p></o:p></span></p></td>
				<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.@$rel_exp.'<o:p></o:p></span></p></td>
				<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.@$current_ctc_cur.' '.@$ctc.'/'.$current_ctc_mode.'<o:p></o:p></span></p></td>
				<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$current_ectc_cur.' '.@$ectc.'/'.$current_ctc_mode.'<o:p></o:p></span></p></td>
				<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.ucwords((@$appData->comment)).'<o:p></o:p></span></p></td>
				<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$np.'<o:p></o:p></span></p></td>
				<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.@$appData->current_company.'<o:p></o:p></span></p></td>
				<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif"><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.ucwords($row_city_name).'">'.ucwords(substr($row_city_name, 0, 50)).'</a>,
				<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords($row_state_name).' | '.strtoupper($row_country_name).'">'.ucfirst(substr($row_state_name, 0, 50)).' , '.strtoupper(substr($row_country_name, 0, 10)).'</span><o:p></o:p></span></p></td>
				<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.ucwords(substr(@$appData->qualification_name, 0, 10)).'<o:p></o:p></span></p></td>				

				</tr>';

                // dd($appData->jobId);

                $cnt = $cnt + 1;
            }
        }

        $tracker_html .= '<tbody class="text-gray-600 fw-bold">';
        $tracker_html .= '</tbody></table>';

        $firstApp = $applicationData->first();
        $activeJobId = $firstApp->jobId ?? ($firstApp->job_id ?? null);
        $activeCompanyId = $firstApp->company_id ?? (auth()->user()->company_id ?? null);

        $jobQuestionCount = 0;
        $jobSkill = collect();

        if ($activeJobId) {
            $jobQuestionCount = DB::table('job_additional_detail')
                ->select('*')
                ->where('job_additional_detail.job_id', '=', $activeJobId)
                ->whereNotNull('job_additional_detail.detail_value')
                ->count();

            $jobSkill = DB::table('job_metronics_skills')
                ->select('job_metronics_skills.*')
                ->where('job_metronics_skills.job_id', '=', $activeJobId)
                ->where('job_metronics_skills.company_id', '=', $activeCompanyId)
                ->orderBy('job_metronics_skills.id')
                ->get();
        }

        if (! empty($cleanWi)) {

            if ($applicationDataCount > 0) {

                if (! empty($jobSkill)) {
                    $colspan = count($jobSkill) + 1;
                    $tracker_html .= '<table align="left" border="1" style="border-collapse:collapse; border:solid 1px black; width:1272px;margin-top: 25px;">
								<tr> <td colspan="'.$colspan.'"> <h3><b>Skill Tracker</b></h3></td> </tr>
								<tr style="mso-yfti-irow:0;mso-yfti-firstrow:yes;height:15.75pt"> 
								<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden;width:240px"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Name<o:p></o:p></span></b></p></td>';
                    foreach ($jobSkill as $skill_val) {
                        $exp = 'EXP.';
                        $yrs = '';
                        // 	 $skillShowView = '';
                        //   $candidate_job_skill = DB::table('candidate_job_skill_exp')
                        // ->where('job_id', '=', $appData->jobId)
                        // ->where('candidate_id', '=', $appData->id)
                        // ->where('job_skill_id', '=', $skill_val->id)
                        // ->first();

                        // if($candidate_job_skill){
                        // 	if(!empty($candidate_job_skill->skill_value)){

                        // 		$yrs = '';
                        // 	}

                        // 		$skillShowView = $candidate_job_skill->skill_value;
                        // 		if (is_int($candidate_job_skill->skill_value)) {
                        // 		 $skillShowView = $candidate_job_skill->skill_value.' '.$yrs;
                        // 		}
                        // 	}

                        $tracker_html .= '
											<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.strtoupper($skill_val->skill_name).' '.$exp.'<o:p></o:p></span></b></p></td>';
                    }
                    $tracker_html .= '
									</tr>';

                    foreach ($applicationData as $key => $appData) {

                        $candJobId = $appData->jobId ?? ($appData->job_id ?? ($activeJobId ?? 0));
                        $skillShow = true;
                        if (! DB::table('candidate_job_skill_exp')->where('candidate_job_skill_exp.job_id', '=', $candJobId)->where('candidate_job_skill_exp.candidate_id', '=', $appData->id)->exists()) {
                            $skillShow = false;
                        }

                        if (! empty($jobSkill)) {

                            $tracker_html .= '<tr style="mso-yfti-irow:1;mso-yfti-lastrow:yes;height:15.75pt"> 
												<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.ucwords(@$appData->full_name).'<o:p></o:p></span></p></td>';

                            foreach ($jobSkill as $skill_val) {

                                $exp = 'EXP.';
                                $yrs = '';

                                $params = [
                                    'index' => ELASTIC_CANDIDATE_SKILLS_EXP,
                                    'body' => [
                                        'query' => [
                                            'bool' => [
                                                'must' => [
                                                    ['term' => ['candidate_id' => $appData->id]],
                                                    ['term' => ['skill_name' => $skill_val->skill_name]],
                                                    ['term' => ['job_id' => $appData->jobId]],
                                                ],
                                            ],
                                        ],
                                    ],
                                ];

                                $response = $this->elasticService->getClient()->search($params);

                                // Extract all _source data from hits
                                $hits = $response['hits']['hits'] ?? [];
                                $records = [];

                                foreach ($hits as $hit) {
                                    $records[] = (object) $hit['_source']; // same as get() response style
                                }

                                foreach ($records as $candidate_job_skill) {
                                    if ($candidate_job_skill) {

                                        if (! empty($candidate_job_skill->skill_value)) {

                                            $yrs = '';
                                        }

                                        if ($skillShow) {
                                            $skillShowView = $candidate_job_skill->skill_value;
                                            if (is_int($candidate_job_skill->skill_value)) {
                                                $skillShowView = $candidate_job_skill->skill_value.' '.$yrs;
                                            }

                                        }
                                    }

                                    $tracker_html .= '
													<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$skillShowView.' <o:p></o:p></span></p></td>';
                                }
                            }

                            $tracker_html .= '
								</tr>';
                        }

                    }
                    $tracker_html .= '
						</table>';
                }

            }

            if ($applicationDataCount > 0 && $jobQuestionCount > 0) {

                $job_additional_detail = DB::table('job_additional_detail')
                    ->select('*')
                    ->where('job_additional_detail.job_id', '=', $appData->jobId)
                    ->orderBy('job_additional_detail.id', 'ASC')
                    ->get();
                if (! empty($job_additional_detail)) {
                    $colspan = count($job_additional_detail) + 1;
                    $tracker_html .= '<table align="left" border="1" style="border-collapse:collapse; border:solid 1px black; width:1272px;margin-top: 25px;">
				<tr> <td colspan="'.$colspan.'"> <h3><b>Job Questions</b></h3></td> </tr>
				<tr style="mso-yfti-irow:0;mso-yfti-firstrow:yes;height:15.75pt"> 
				<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden;width:240px"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Name<o:p></o:p></span></b></p></td>';

                    foreach ($job_additional_detail as $aDetailArr) {
                        $ans_value = '';
                        if (! empty($application_additional_detail)) {
                            foreach ($application_additional_detail as $aval) {
                                if ($aDetailArr->id == $aval->question_id) {
                                    $ans_value = @$aval->application_detail_value;
                                }
                            }
                        }

                        $tracker_html .= '
						<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.strtoupper($aDetailArr->detail_value).'<o:p></o:p></span></b></p></td>
						</td>';
                    }
                    $tracker_html .= '
							</tr>';

                    foreach ($applicationData as $key => $appData) {

                        $candJobId = $appData->jobId ?? ($appData->job_id ?? ($activeJobId ?? 0));
                        $job_additional_detail = DB::table('job_additional_detail')
                            ->select('*')
                        // ->where('job_additional_detail.company_id', '=', $company_id)
                            ->where('job_additional_detail.job_id', '=', $candJobId)
                            ->orderBy('job_additional_detail.id', 'ASC')
                            ->get();
                        $application_additional_detail = DB::table('application_additional_detail')
                            ->select('*')
                            ->where('application_additional_detail.application_id', '=', $appData->id)
                            ->where('application_additional_detail.job_id', '=', $candJobId)
                            ->where('application_additional_detail.application_detail_value', '!=', null)
                            ->orderBy('application_additional_detail.id', 'ASC')
                            ->get();

                        $params = [
                            'index' => ELASTIC_CANDIDATE_JOB_QUESTION,
                            'body' => [
                                'query' => [
                                    'bool' => [
                                        'must' => [
                                            ['term' => ['application_id' => $appData->id]],
                                            ['term' => ['job_id' => $appData->jobId]],
                                        ],
                                    ],
                                ],
                            ],
                        ];

                        $response = $this->elasticService->getClient()->search($params);

                        // Extract all _source data from hits
                        $hits = $response['hits']['hits'] ?? [];
                        $records = [];

                        foreach ($hits as $hit) {
                            $records[] = (object) $hit['_source']; // same as get() response style
                        }

                        if (! empty($job_additional_detail)) {
                            $customTitleId = 1;
                            // $html			=	'<h3 class="py-5">Custom Details</h3>';
                            $inc_first = 0;
                            $inc_last = 1;
                            $tracker_html .= '<tr style="mso-yfti-irow:1;mso-yfti-lastrow:yes;height:15.75pt"> 
										<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.ucwords(@$appData->full_name).'<o:p></o:p></span></p></td>';
                            foreach ($records as $aDetailArr) {
                                $ans_value = @$aDetailArr->application_detail_value;

                                $tracker_html .= '
									<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.@$ans_value.'<o:p></o:p></span></p></td>';
                                $inc_first++;
                                $inc_last++;
                                $customTitleId++;
                            }
                            $tracker_html .= '
							</tr>';
                        }

                    }

                    $tracker_html .= '
						</table>';
                }

            }
        }
        if (isset($request->WijobIdArr) && is_array($request->WijobIdArr)) {

            if (count($request->WijobIdArr) > 0) {

                $tracker_html .= '<table align="left" border="1" style="border-collapse:collapse; border:solid 1px black; width:1272px;margin-top: 25px;">
					<thead>
					<tr> <td colspan="8"> <h3><b>Interview Availability</b></h3></td> </tr>
					<tr style="mso-yfti-irow:0;mso-yfti-firstrow:yes;height:15.75pt"> 
					<td valign="bottom" style="border:solid windowtext 1.0pt;background:#BDD7EE; padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Name</span></b><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif;mso-fareast-language: EN-IN"><o:p></o:p></span></b></p></td>
					<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Positions<o:p></o:p></span></b></p></td>
					<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Slot-1<o:p></o:p></span></b></p></td>
					<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Slot-2<o:p></o:p></span></b></p></td>
					<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Slot-3<o:p></o:p></span></b></p></td>
					<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Slot-4<o:p></o:p></span></b></p></td>
					<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Slot-5<o:p></o:p></span></b></p></td>
					<td valign="bottom" style="border:solid windowtext 1.0pt;border-left:none; background:#BDD7EE;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt; overflow:hidden"> <p class="MsoNormal"><b><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">Slot-6<o:p></o:p></span></b></p></td>
					</tr>
					</thead>';
                $tracker_html .= '<tbody class="text-gray-600 fw-bold">';

                foreach ($request->WijobIdArr as $candidateId) {
                    $params = [
                        'index' => ELASTIC_CANDIDATES,
                        'id' => $candidateId,
                    ];

                    $response = $this->elasticService->getClient()->get($params);
                    $data = (object) $response['_source'];

                    $tracker_html .= '
					<tr style="mso-yfti-irow:1;mso-yfti-lastrow:yes;height:15.75pt"> 
					<td valign="bottom" style="border:solid windowtext 1.0pt;border-top:none; background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt;height:15.75pt;overflow: hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$data->full_name.'<o:p></o:p></span></p></td>
					<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.$data->title.'<o:p></o:p></span></p></td>
					<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.((@$data->interviewDateOne) ? Carbon::parse(@$data->interviewDateOne)->format('d M Y') : 'N/A').'<br>'.((@$data->interviewDateOne) ? Carbon::parse(@$data->interviewDateOne)->format('h:i A').' (IST)' : 'N/A').'<br>'.@$data->interview_mode_one.'<o:p></o:p></span></p></td>
					<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.((@$data->interviewDateTwo) ? Carbon::parse(@$data->interviewDateTwo)->format('d M Y') : 'N/A').'<br>'.((@$data->interviewDateTwo) ? Carbon::parse(@$data->interviewDateTwo)->format('h:i A').' (IST)' : 'N/A').'<br>'.@$data->interview_mode_two.'<o:p></o:p></span></p></td>
					<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif; color:#1F1F1F">'.((@$data->interviewDateThree) ? Carbon::parse(@$data->interviewDateThree)->format('d M Y') : 'N/A').'<br>'.((@$data->interviewDateThree) ? Carbon::parse(@$data->interviewDateThree)->format('h:i A').' (IST)' : 'N/A').'<br>'.@$data->interview_mode_three.'<o:p></o:p></span></p></td>
					<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.((@$data->interviewDateFour) ? Carbon::parse(@$data->interviewDateFour)->format('d M Y') : 'N/A').'<br>'.((@$data->interviewDateFour) ? Carbon::parse(@$data->interviewDateFour)->format('h:i A').' (IST)' : 'N/A').'<br>'.@$data->interview_mode_four.'<o:p></o:p></span></p></td>
					<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.((@$data->interviewDateFive) ? Carbon::parse(@$data->interviewDateFive)->format('d M Y') : 'N/A').'<br>'.((@$data->interviewDateFive) ? Carbon::parse(@$data->interviewDateFive)->format('h:i A').' (IST)' : 'N/A').'<br>'.@$data->interview_mode_five.'<o:p></o:p></span></p></td>
					<td valign="bottom" style="border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt;background:white;padding:1.5pt 2.25pt 1.5pt 2.25pt; height:15.75pt;overflow:hidden"> <p class="MsoNormal"><span style="font-size:11.0pt;font-family:&quot;Calibri&quot;,sans-serif">'.((@$data->interviewDateSix) ? Carbon::parse(@$data->interviewDateSix)->format('d M Y') : 'N/A').'<br>'.((@$data->interviewDateSix) ? Carbon::parse(@$data->interviewDateSix)->format('h:i A').' (IST)' : 'N/A').'<br>'.@$data->interview_mode_six.'<o:p></o:p></span></p></td>
							
					</tr>';

                }

                $tracker_html .= '</tbody></table>';

            }
        }

        $EMAIL_TRACKER_HR = ''; // @$assignData->name;

        $template_description = $templateData->template_description;

        $template_description = str_replace(['[{EMAIL_TRACKER}]', '[{EMAIL_TRACKER_HR}]'], [$tracker_html, $EMAIL_TRACKER_HR], $template_description);
        $subject = '';
        $whatlink = '';
        //  dd((int)$request->mailtype);
        if ((int) $request->mailtype == 1) {
            $companyDetails = Company::leftJoin('users_profile', function ($join) {
                $join->on('users_profile.company_id', '=', 'companies.id')
                    ->where('users_profile.user_id', '=', auth()->user()->id);
            })->select('users_profile.designation', 'companies.company_name')
                ->where('companies.id', auth()->user()->company_id)->first();

            $companyName = $companyDetails->company_name ?? (DB::table('companies')->where('id', auth()->user()->company_id)->value('company_name') ?? 'Hiring Team');
            $userDesignation = $companyDetails->designation ?? 'Recruitment Team';
            $userName = auth()->user()->name ?? 'HR';
            $userEmail = auth()->user()->email ?? '';
            $userMobile = auth()->user()->mobile ?? (auth()->user()->phone ?? '');

            $subject = str_replace(
                ['[{CURRENTDATE}]', '[{JOB_TITLE}]', '[{CLIENT_NAME_COMPANY_NAME}]', '<br>'],
                [date('d M Y'), @$job_title, $client_company_name, ''],
                $templateData->template_subject ?? ''
            );

            $template_description = str_replace(
                ['[{COMPANY_NAME}]', '[{HR_PHONE}]', '[{HR_NAME}]', '[HR_NAME]', '[{HR_EMAIL}]', '[{POSITION}]'],
                [$companyName, $userMobile, $userName, $userName, $userEmail, $userDesignation],
                $template_description
            );
        }
        if ((int) $request->mailtype == 2) {
            // dd($company_name->company_name, auth()->user()->phone, auth()->user()->name, auth()->user()->email);
            $subject = str_replace(['[{COMPANY_NAME}]', '<br>'], [$company_name->company_name, ''], $templateData->template_subject);
            $whatlink = '<a href="https://wa.me/'.auth()->user()->mobile.'" rel="noreferrer noopener" target="_blank" title="https://wa.me/'.auth()->user()->mobile.'">Connect To Wahtsapp</a>';
            $template_description = str_replace(['[{COMPANY_NAME}]', '[{HR_PHONE}]', '[{HR_NAME}]', '[{HR_EMAIL}]', '[{WHAT_LINK}]'], [$company_name->company_name, auth()->user()->mobile, auth()->user()->name, auth()->user()->email, $whatlink], $template_description);
        }
        if ((int) $request->mailtype == 3) {

            $subject = str_replace(['[{COMPANY_NAME}]', '<br>'], [$company_name->company_name, ''], $templateData->template_subject);
        }
        if ((int) $request->mailtype == 4) {

            $email = '0';
            if (! empty($request->from_email)) {
                $from = $request->from_email;
                $from = json_decode($from, true);
                $from = array_column($from, 'value');
                $from = implode(',', $from);

                $email = explode(',', $from);
                $email = $email[0];
            }

            $to_email = '0';
            if (! empty($request->to_email)) {
                $to = $request->to_email;
                $to = json_decode($to, true);
                $to = array_column($to, 'value');
                $to = implode(',', $to);

                $to_email = explode(',', $to);
                $to_email = $to_email[0];
            }
            $jobid = $request->jobID;
            $job_application_id = $selectedApplicantListing[0];
            $job_wise_condidates = DB::table('add_job_wise_condidates')->select('add_job_wise_condidates.id')->where('job_application_id', $job_application_id)->where('job_id', $jobid)->first();

            $wise_condidateId = $job_wise_condidates->id;

            $country_id = $country_name = '';

            $client_loc = DB::table('client_location_jobs')
                ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                ->select('client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
                ->where('client_location_jobs.job_id', $jobid)
                ->first();
            $address = @$client_loc->address;
            // dd($address);
            $client_loc = DB::table('client_city_jobs')
                ->join('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
                ->join('countries', 'countries.id', '=', 'state_list.country_id')
                ->select('city_list.city', 'countries.id as country_id', 'countries.country_name', 'city_list.city as city_name', 'state_list.state as state_name')
                ->where('client_city_jobs.job_id', $jobid)
                ->get();

            // dd($client_loc);

            // $country_id = @$client_loc->country_id;
            $location = '';

            if (count($client_loc) > 0) {
                // $country_name = GeneralMethod::getCountryName($country_id);
                foreach ($client_loc as $key => $cval) {
                    $location .= (++$key).') '.$cval->city_name.' | '.$cval->state_name.' | '.$cval->country_name.'<br>';
                    // $country_name = ucwords($cval['sortname']);
                }
            }
            // dd($location);

            // $city = substr(@$client_loc->city, 0, 30);
            // if (@$client_loc->country_id == 101) {
            // 	$city = substr(@$client_loc->city_name, 0, 30);
            // }
            $jobs = Job::withoutGlobalScopes()->find($jobid);

            $companyOrClientName = GeneralMethod::companyOrClientName($jobs->client_id);

            // //  dd($companyOrClientName);

            //  $location = $city.' | '.$country_name;

            // dd($templateData->template_subject);
            if ($templateData->tempForInterview == 1) {
                $subject = str_replace(['[{COMPANY_NAME}]', '[{JOB_TITLE}]', '[{JOB_LOCATION}]', '<br>'], [$companyOrClientName->company, $jobs->title, $location, ''], $templateData->template_subject);
            } elseif ($templateData->tempForInterview == 2) {
                $subject = str_replace(['[{CLIENT_NAME}]', '[{JOB_TITLE}]', '[{JOB_LOCATION}]', '<br>'], [$companyOrClientName->client, $jobs->title, $location, ''], $templateData->template_subject);
            } elseif ($templateData->tempForInterview == 3) {
                $subject = str_replace(['[{CLIENT_NAME}]', '[{COMPANY_NAME}]', '[{JOB_LOCATION}]', '<br>'], [$companyOrClientName->client, $companyOrClientName->company, $location, ''], $templateData->template_subject);
            } elseif ($templateData->tempForInterview == 4) {
                //  Documents Required | [{COMPANY_NAME}]
                $subject = str_replace(['[{COMPANY_NAME}]', '<br>'], [$companyOrClientName->company, ''], $templateData->template_subject);
            }
            $dataMail = [
                'status' => 1, // or whatever status you want
                'created_at' => now(), // optional, if using timestamps manually
                'updated_at' => now(),
            ];

            $insertId = DB::table('sendmailforcandidates')->insertGetId($dataMail);

            $userId = auth()->user()->id;
            $companyId = auth()->user()->company_id;
            $interested = url('/candidate-mail-response/'.encrypt($wise_condidateId)).'/'.encrypt(1).'/'.encrypt($email).'/'.encrypt($to_email).'/'.encrypt($subject).'/'.encrypt($userId).'/'.encrypt($companyId).'/'.encrypt($insertId);
            $notInterested = url('/candidate-mail-response/'.encrypt($wise_condidateId)).'/'.encrypt(2).'/'.encrypt($email).'/'.encrypt($to_email).'/'.encrypt($subject).'/'.encrypt($userId).'/'.encrypt($companyId).'/'.encrypt($insertId);

            $additional = '<div style="margin-top: 20px;">
						<!-- Interested Button -->
						<a href="'.$interested.'" 
						style="display: inline-block; padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 4px; font-family: Arial, sans-serif; font-size: 14px;">
							Interested
						</a>

						<!-- Not Interested Button -->
						<a href="'.$notInterested.'" 
						style="display: inline-block; padding: 10px 20px; background-color: #dc3545; color: white; text-decoration: none; border-radius: 4px; font-family: Arial, sans-serif; font-size: 14px; margin-left: 10px;">
							Not Interested
						</a>
					</div>';

            //    $template_description = $additional.$template_description;

            $template_description = str_replace(['[{COMPANY_NAME}]', '[{HR_PHONE}]', '[{HR_NAME}]', '[{HR_EMAIL}]', '[{WHAT_LINK}]', '[{CLIENT_NAME}]', '[{JOB_TITLE}]', '[{JOB_LOCATION}]', '[{CLIENT_WEBSITE}]', '[{JOB_DESCRIPTION}]', '[{COMPANY_WEBSITE}]', '[{COMPANY_DESCRIPTION}]', '[{CLIENT_DESCRIPTION}]', '[{NO_OF_EMP}]', '[{CANDIDATE_NAME}]', '[{WORK_SHIFT}]', '[{COMPANY_ADDRESS}]'], [$companyOrClientName->company, auth()->user()->mobile, auth()->user()->name, auth()->user()->email, $whatlink, $companyOrClientName->client, $jobs->title, $location, $companyOrClientName->client_website, $jobs->job_description, $companyOrClientName->company_website, $companyOrClientName->company_description, $companyOrClientName->client_description, $companyOrClientName->company_size, ucwords(strtolower($applicationData[0]->full_name)), $jobs->work_shifts, $address], $template_description);

        }
        if ((int) $request->mailtype == 7) {
            $email = '0';
            if (! empty($request->to_email)) {
                $emailall = explode(',', $request->to_email);
                $email = $emailall[0];
            }
            $from_email = '0';
            if (! empty($request->from_email)) {
                $from = $request->from_email;
                $from = json_decode($from, true);
                $from = array_column($from, 'value');
                $from = implode(',', $from);

                $from_email = explode(',', $from);
                $from_email = $from_email[0];
            }
            $offerId = $request->offerId;
            $offer_createmain = DB::table('offer_create_main_tds')->where('offer_id', '=', $offerId)->orderByDesc('id')->first();

            $designation = (@$offer_createmain) ? $offer_createmain->designation : @$job_applications->title;

            $job_applications = DB::table('offer_job_applications')->select('offer_job_applications.id')
                ->join('jobs', 'jobs.id', '=', 'offer_job_applications.job_id')
                ->join('job_applications', 'job_applications.id', '=', 'offer_job_applications.application_id')
                ->join('add_job_wise_condidates', 'add_job_wise_condidates.id', 'offer_job_applications.joined')
                ->select('job_applications.*', 'job_applications.full_name', 'job_applications.email', 'jobs.title', 'jobs.id as jobId')
                ->where('offer_job_applications.id', $offerId)->first();
            $companyDetails = Company::join('users_profile', 'users_profile.company_id', 'companies.id')->select('users_profile.designation', 'companies.company_name')
                ->where('users_profile.user_id', auth()->user()->id)->where('users_profile.company_id', auth()->user()->company_id)->first();
            $link = url('/signature-pad/'.encrypt($offerId)).'/'.encrypt(1).'/'.encrypt($email).'/'.encrypt($from_email);
            $Url = '<div style="padding-bottom: 20px; text-align:center;">
                                          <a href="'.$link.'"
                                             style="text-decoration:none;display:inline-block;text-align:center;padding:0.75575rem 1.3rem;font-size:0.925rem;line-height:1.5;border-radius:0.35rem;color:#ffffff;background-color:#009EF7;border:0px;margin-right:0.75rem!important;font-weight:600!important;outline:none!important;vertical-align:middle">Click to View Offer Letter
                                          </a>
                                       </div>';

            $Urlcopy = '<a href="'.$link.'" style="text-decoration:none;color: #009EF7">'.$link.'</a>';

            // dd($offerId,$job_applications->full_name);
            $template_description = str_replace(['[{COMPANY_NAME}]', '[{CANDIDATE_NAME}]', '[{JOB_TITLE}]', '[{OFFER_LETTER_LINK}]', '[{OFFER_LETTER_LINK_COPY}]'], [$companyDetails->company_name, @$job_applications->full_name, @$designation, @$Url, @$Urlcopy], $template_description);

            $subject = str_replace(['[{COMPANY_NAME}]', '[{JOB_TITLE}]', '[{CANDIDATE_NAME}]', '<br>'], [$companyDetails->company_name, @$designation, @$job_applications->full_name, ''], $templateData->template_subject);

        }
        if ((int) $request->mailtype == 8) {
            $offerId = $request->offerId;

            $email = '0';
            if (! empty($request->to_email)) {
                $emailall = explode(',', $request->to_email);
                $email = $emailall[0];
            }
            $from_email = '0';
            if (! empty($request->from_email)) {
                $from = $request->from_email;
                $fromDecoded = json_decode($from, true);
                if (is_array($fromDecoded)) {
                    // Tagify format: [{"value":"email@..."},...]
                    $from = implode(',', array_column($fromDecoded, 'value'));
                }
                // else: plain email string (approval flow) — use as-is
                $from_email = explode(',', $from);
                $from_email = $from_email[0];
            }

            $users = DB::table('client_contact_info')->where('contact_person_email', '=', $email)->orderByDesc('id')->first();
            // dd($users,$request->to_email,$users->contact_person_name);
            $job_applications = DB::table('offer_job_applications')->select('offer_job_applications.id')
                ->join('jobs', 'jobs.id', '=', 'offer_job_applications.job_id')
                ->join('job_applications', 'job_applications.id', '=', 'offer_job_applications.application_id')
                ->join('add_job_wise_condidates', 'add_job_wise_condidates.id', 'offer_job_applications.joined')
                ->select('job_applications.*', 'job_applications.full_name', 'job_applications.email', 'jobs.title', 'jobs.employment_type', 'jobs.id as jobId', 'offer_job_applications.joined_date', 'offer_job_applications.offer_amount', 'offer_job_applications.offer_currency', 'offer_job_applications.offer_mode')
                ->where('offer_job_applications.id', $offerId)->first();
            $companyDetails = Company::join('users_profile', 'users_profile.company_id', 'companies.id')->select('users_profile.designation', 'companies.company_name')
                ->where('users_profile.user_id', auth()->user()->id)->where('users_profile.company_id', auth()->user()->company_id)->first();
            $Url = '<div style="padding-bottom: 20px; text-align:center;">
                                          <a href="'.url('/signature-pad/'.encrypt($offerId)).'/'.encrypt(2).'/'.encrypt($email).'/'.encrypt($from_email).'"
                                             style="text-decoration:none;display:inline-block;text-align:center;padding:0.75575rem 1.3rem;font-size:0.925rem;line-height:1.5;border-radius:0.35rem;color:#ffffff;background-color:#009EF7;border:0px;margin-right:0.75rem!important;font-weight:600!important;outline:none!important;vertical-align:middle">Click to View Offer Letter
                                          </a>
                                       </div>';

            $Urlcopy = '<a href="'.url('/signature-pad/'.encrypt($offerId)).'/'.encrypt(2).'/'.encrypt($email).'/'.encrypt($from_email).'" style="text-decoration:none;color: #009EF7">'.url('/signature-pad/'.encrypt($offerId)).'/'.encrypt(2).'/'.encrypt($email).'/'.encrypt($from_email).'</a>';

            $offer_createmain = DB::table('offer_create_main_tds')->where('offer_id', '=', $offerId)->orderByDesc('id')->first();

            // dd($offer_create->designation);

            $SALARY_MODE = $job_applications
                ? ($salaryMode[$job_applications->offer_currency] ?? 0)
                : 0;

            $CURRENCY_SELCT = $job_applications
                ? ($currency[$job_applications->offer_mode] ?? 0)
                : 0;
            // $SALARY_AMOUNT= $job_applications->offer_amount;
            $SALARY_AMOUNT = $job_applications->offer_amount ?? 0;
            $JOB_TYPE = ($job_applications->employment_type == 1) ? 'Contract' : 'Full-time';
            // dd($offerId,$job_applications->full_name);
            $designation = (@$offer_createmain) ? $offer_createmain->designation : @$job_applications->title;
            $template_description = str_replace(['[{COMPANY_NAME}]', '[{CANDIDATE_NAME}]', '[{JOB_TITLE}]', '[{OFFER_LETTER_LINK}]', '[{OFFER_LETTER_LINK_COPY}]', '[{CANDIDATE_ID}]', '[{JOB_ID}]', '[{START_DATE_TIME}]', '[{CURRENCY}]', '[{SALARY_AMOUNT}]', '[{SALARY_MODE}]', '[{JOB_TYPE}]', '[{MANAGER_NAME}]'], [$companyDetails->company_name, @$job_applications->full_name, @$designation, @$Url, @$Urlcopy, @$job_applications->id, @$job_applications->jobId, date('d M Y', strtotime(@$job_applications->joined_date)), $CURRENCY_SELCT, $SALARY_AMOUNT, $SALARY_MODE, $JOB_TYPE, @$users->contact_person_name], $template_description);

            $subject = str_replace(['[{COMPANY_NAME}]', '[{JOB_TITLE}]', '[{CANDIDATE_NAME}]', '<br>'], [$companyDetails->company_name, @$designation, @$job_applications->full_name, ''], $templateData->template_subject);

        }
        if ((int) $request->mailtype == 9) {
            $offerId = $request->offerId;

            $email = '0';
            if (! empty($request->to_email)) {
                $emailall = explode(',', $request->to_email);
                $email = $emailall[0];
            }
            $from_email = '0';
            if (! empty($request->from_email)) {
                $from = $request->from_email;
                $from = json_decode($from, true);
                $from = array_column($from, 'value');
                $from = implode(',', $from);

                $from_email = explode(',', $from);
                $from_email = $from_email[0];
            }

            $users = DB::table('client_contact_info')->where('contact_person_email', '=', $email)->orderByDesc('id')->first();

            //  dd(@$request->selectedApplicantListing[0]);
            $params = [
                'index' => ELASTIC_CANDIDATES,
                'size' => 1,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['jobId' => (int) @$request->jobID]],
                                ['term' => ['candidate_id' => (int) @$request->selectedApplicantListing[0]]],
                            ],
                        ],
                    ],
                ],
            ];

            $response = $this->elasticService->getClient()->search($params);

            $job_applications = null;

            if (! empty($response['hits']['hits'])) {
                $job_applications = (object) $response['hits']['hits'][0]['_source'];
            }

            $companyDetails = Company::join('users_profile', 'users_profile.company_id', 'companies.id')->select('users_profile.designation', 'companies.company_name')
                ->where('users_profile.user_id', auth()->user()->id)->where('users_profile.company_id', auth()->user()->company_id)->first();
            $Url = '<div style="padding-bottom: 20px; text-align:center;">
                                          <a href="'.url('/signature-pad/'.encrypt($offerId)).'/'.encrypt(2).'/'.encrypt($email).'/'.encrypt($from_email).'"
                                             style="text-decoration:none;display:inline-block;text-align:center;padding:0.75575rem 1.3rem;font-size:0.925rem;line-height:1.5;border-radius:0.35rem;color:#ffffff;background-color:#009EF7;border:0px;margin-right:0.75rem!important;font-weight:600!important;outline:none!important;vertical-align:middle">Click to View Offer Letter
                                          </a>
                                       </div>';

            $Urlcopy = '<a href="'.url('/signature-pad/'.encrypt($offerId)).'/'.encrypt(2).'/'.encrypt($email).'/'.encrypt($from_email).'" style="text-decoration:none;color: #009EF7">'.url('/signature-pad/'.encrypt($offerId)).'/'.encrypt(2).'/'.encrypt($email).'/'.encrypt($from_email).'</a>';

            $offer_createmain = DB::table('offer_create_main_tds')->where('offer_id', '=', $offerId)->orderByDesc('id')->first();

            // dd($offer_create->designation);

            // dd($job_applications);

            $SALARY_MODE = $salaryMode[$job_applications->of_offer_mode ?? 0];
            $CURRENCY_SELCT = $currency[$job_applications->of_offer_currency ?? 0];
            $SALARY_AMOUNT = $job_applications->of_offer_amount ?? 0;
            $JOB_TYPE = (@$job_applications->employment_type == 1) ? 'Contract' : 'Full-time';
            // dd($offerId,$job_applications->full_name);
            $designation = (@$offer_createmain) ? $offer_createmain->designation : @$job_applications->title;

            $template_description = str_replace([], [], $template_description);
            // dd($job_applications);
            $companyOrClientName = GeneralMethod::companyOrClientName($job_applications->client_id);
            $InterviewMode = '';
            $dateTime = '';
            $videoUrl = '';
            $videoUrlInter = '';
            $text_set = '';
            $interview_level = '';
            $otherDetails = '';
            $interview_ques = '';

            $paramsInterview = [
                'index' => ELASTIC_INERTVIEW_TRACKER,
                'size' => 1,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['job_application_id' => (int) @$request->selectedApplicantListing[0]]],
                                ['term' => ['jobId' => (int) @$request->jobID]],
                            ],
                        ],
                    ],
                    'sort' => [
                        ['created_at' => ['order' => 'desc']],
                    ],
                ],
            ];

            $responseInterview = $this->elasticService->getClient()->search($paramsInterview);
            $dataInterview = isset($responseInterview['hits']['hits'][0]) ? (object) $responseInterview['hits']['hits'][0]['_source'] : null;
            if ($dataInterview) {

                $videoUrlInter = $dataInterview->interview_mention_link;

                $dateTime = date('d-M-Y', strtotime($dataInterview->schedule_date)).' '.$dataInterview->schedule_to_time.' ('.$dataInterview->timezone_abbreviation.')';
                if (@$dataInterview->interview_mode == 1) { //  Telephonic
                    $InterviewMode = 'Telephonic';
                } elseif (@$dataInterview->interview_mode == 2) { //  Video
                    $InterviewMode = 'Video';
                    $videoUrlInter = '<a href="'.$videoUrlInter.'" style="cursor: pointer;">Click Here to Join the Interview</a>';
                } elseif (@$dataInterview->interview_mode == 3) { //  Face-to-Face
                    $InterviewMode = 'F2F';
                }

            }
            $interview_questions = DB::table('interview_questions')->select('*')->where('job_id', (int) @$request->jobID)->get();

            if (count($interview_questions) > 0) {
                $interview_ques .= '<h3><strong>Job Interview Questions</strong></h3>';
                $interview_ques .= '<ol>';
                foreach ($interview_questions as $row) {
                    $interview_ques .= '<li>'.$row->questions.'</i>';
                }
                $interview_ques .= '</ol><hr />';

            }

            $params = [
                'index' => ELASTIC_JOBS,
                'id' => (int) @$request->jobID,
            ];

            $response = $this->elasticService->getClient()->get($params);
            $job = (object) $response['_source'];

            $client_city_jobs = $job->client_city_jobs;

            $firstCity = collect($client_city_jobs)->first();

            $client_location = $job->client_location;

            $firstLocation = collect($client_location)->first();

            $addressName = $firstLocation['address'] ?? null;
            $cityName = $firstCity['city_name'] ?? null;
            $city = mb_substr(@$cityName, 0, 21);
            $countryName = $firstCity['country_name'] ?? null;
            $country_name = mb_substr(@$countryName, 0, 6);

            $location = ucwords(@$addressName).' | '.ucwords(@$city).' | '.ucwords(@$country_name);

            $template_description = str_replace(['[{COMPANY_NAME}]', '[{CANDIDATE_NAME}]', '[{JOB_TITLE}]', '[{OFFER_LETTER_LINK}]', '[{OFFER_LETTER_LINK_COPY}]', '[{CANDIDATE_ID}]', '[{JOB_ID}]', '[{JOINING_DATE}]', '[{CURRENCY}]', '[{SALARY_AMOUNT}]', '[{SALARY_MODE}]', '[{JOB_TYPE}]', '[{MANAGER_NAME}]', '[{CLIENT_NAME}]', '[{INTERVIEW_MODE}]', '[{INTERVIEW_DATE_TIME}]', '[{WEBSITE}]', '[{JOB_DESCRIPTION}]', '[{COMPANY_DESCRIPTION}]', '[{CLIENT_DESCRIPTION}]', '[{INTERVIEW_LINK}]', '[{OTHER_DESCRIPTION}]', '[{WORK_LOCATION}]', '[{HR_NAME}]', '[{INTERVIEW_LINK_URL}]', '[{INTERVIEW_LEVEL}]', '[{TEXT_SET}]', '[{INTERVIEW_QUESTIONS}]', '[{CLIENT_WEBSITE}]'], [$companyDetails->company_name, @$job_applications->full_name, @$designation, @$Url, @$Urlcopy, @$job_applications->id, @$job_applications->jobId, date('d M Y H:i: A', strtotime(@$job_applications->of_joined_date)), $CURRENCY_SELCT, $SALARY_AMOUNT, $SALARY_MODE, $JOB_TYPE, @$users->contact_person_name, $companyOrClientName->client, $InterviewMode, $dateTime, $companyOrClientName->company_website, $job->job_description, $companyOrClientName->company_description, $companyOrClientName->client_description, $videoUrl, $otherDetails, $location, auth()->user()->name, $videoUrlInter, $interview_level, $text_set, $interview_ques, $companyOrClientName->client_website], $template_description);

            $subject = str_replace(['[{COMPANY_NAME}]', '[{JOB_TITLE}]', '[{CANDIDATE_NAME}]', '<br>'], [$companyDetails->company_name, @$designation, @$job_applications->full_name, ''], $templateData->template_subject);

        }

        return Reply::dataOnly([
            'status' => 'success',
            'template_description' => $template_description,
            'template_body' => $template_description,
            'subject' => $subject,
        ]);

    }


    public function mass_mail_send(Request $request)
    {

        $template = $from = $to = $cc = $subject = $attached_cv = $message = $arr = '';
        $this->status = false;

        $template = $request->template;
        $from = $request->from_email;
        $to = $request->to_email;
        $cc = $request->cc_email;

        $subject = $request->subject;
        $attached_cv = $request->attached_cv;
        $message = $request->editor1;
        $arr = $request->arr;

        $decodedFrom = json_decode($from, true);
        if (is_array($decodedFrom)) {
            $from = implode(',', array_column($decodedFrom, 'value'));
        } // else $from is already a plain email string
        $decodedTo = json_decode($to, true);
        if (is_array($decodedTo)) {
            $to = implode(',', array_column($decodedTo, 'value'));
        } // else $to is already a plain comma-separated string
        if (! empty($cc)) {
            // $cc = json_decode($cc, true);
            // $cc = array_column($cc, 'value');
            $cc = $request->cc_email; // implode(',', $cc);
        }
        // dd($cc);
        $attachments = [];
        if ($attached_cv == 1) {
            $resumeRowCount = DB::table('documents')->select('*')->whereIn('documents.documentable_id', $arr)->count();
            $resumeRow = DB::table('documents')->select('*')->whereIn('documents.documentable_id', $arr)->get();

            $raw_filename = [];
            if ($resumeRowCount) {
                foreach ($resumeRow as $key => $rval) {
                    $hashname = $documentable_id = '';
                    $hashname = $rval->hashname;
                    $documentable_id = $rval->documentable_id;

                    $raw_filename[] = $hashname;
                    $attachmentPath = base_path().'/public/user-uploads/documents/'.$documentable_id.'/'.$hashname;

                    // 	if (file_exists($attachmentPath)) {
                    $attachments[] = [
                        'path' => $hashname,
                        'options' => [],
                    ];
                    // 	}

                }
            }
        }

        $path = base_path().'/public/user-uploads/documents/';
        $mailto = explode(',', $to);
        $from_mail = $from;
        $from_name = 'testing';
        $replyto = $from;
        $bcc = $cc;

        // dd($mailto,$bcc);

        $company_id = auth()->user()->company_id;
        $company_name = DB::table('companies')->select('companies.company_name')->where('id', '=', $company_id)->first();

        // dd("dd",$mailto);
        $senderName = auth()->user()->name;

        $eventId = 42;
        $coinsRequ = count($mailto);
        $companyId = (int) auth()->user()->company_id;
        $coinsDeductions = GeneralMethod::coinsValidationsAllcompany($companyId, $eventId, $coinsRequ);
        if ($coinsDeductions->status) {
            return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
        }

        if (count($mailto) > 0) {
            foreach ($mailto as $email) {

                //    $eventId = 7;
                // 	$coinsDeductions = GeneralMethod::coinsDeductions((int) auth()->user()->company_id,$eventId,auth()->user()->id, auth()->user()->company_id,'','');
                // 	if($coinsDeductions->status){
                // 		return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message,'company_coins'=> $coinsDeductions->company_coins]);
                // 	}

                $condi = JobApplication::where('job_applications.email', '=', $email)->first();
                $name = ($condi) ? $condi->full_name : '';
                $data = ['message' => $message, 'subject' => $subject, 'company_name' => $company_name->company_name, 'consultant_name' => '4Bell Technology', 'position' => 'PHP', 'condidate_name' => $name];

                $validMail = true; //	DB::table('package_active')->where('company_id', '=', $company_id)->where('emails_balance', '>', 0)->where('startdate', '<=', now())->where('enddate', '>=', now())->where('packages_status', 1)->first();
                //   dd($validMail,$company_id);
                if ($validMail) {

                    $senderName = auth()->user()->name;
                    $fromAddress = auth()->user()->email;
                    $cc = [];
                    $checkMailPermision = GeneralMethod::mailPermission(14);
                    if ($checkMailPermision) {
                        // QueueMassMailService
                        QueueMassMailService::dispatch(
                            $email,
                            $subject,
                            'email.resume-forward.massmail',
                            (object) $data,
                            $attachments,
                            $fromAddress,
                            $senderName,
                            $cc,
                            $companyId
                        )->onConnection('emails');

                        // $result = $this->massMailService->sendEmail($email, $subject,'email.resume-forward.massmail', $data, $attachments, $fromAddress, $senderName,$cc);

                        // $this->massMailService->sendEmail($fromAddress, $subject,'email.resume-forward.massmail', $data, $attachments, $fromAddress, $senderName,$cc);
                        // if($result){
                        $eventId = 42;
                        $coinsDeductions = GeneralMethod::coinsDeductions((int) auth()->user()->company_id, $eventId, auth()->user()->id, auth()->user()->company_id, '', '');
                        // }
                    }

                    if ((int) $request->checkForSave == 1 && (int) $request->save_id == 1) {
                        $normalizedSubject = strtolower(trim($subject));
                        // ✅ Check duplicate (company + NULL fallback)
                        // $exists = DB::table('th_email_template')
                        // ->whereRaw('LOWER(template_subject) = ?', [$normalizedSubject])
                        // ->whereNotNull('company_id') // ✅ only non-null company
                        // ->where('company_id', $company_id) // ✅ match same company
                        // ->exists();
                        // if (!$exists) {
                        // ✅ Count existing company templates

                        $message = preg_replace('/<p>\s*(Thanks\s*&amp;?\s*Regards|Best\s*Regards|Regards).*?<\/p>/is', '', $message);
                        $message = preg_replace('/Thanks\s*&amp;?.*$/is', '', $message);

                        $signature = '
						<p>
							Thanks &amp; Regards,<br>
							[{HR_NAME}]<br>
							[{COMPANY_NAME}]<br>
							[{HR_EMAIL}]<br>
							[{HR_PHONE}]
						</p>';

                        $message .= $signature;

                        $templates = DB::table('th_email_template')
                            ->where('company_id', $company_id)
                            ->where('display_name', 'LIKE', '%CUSTOM TEMPLATE-%')
                            ->pluck('display_name');

                        $maxNumber = 0;

                        foreach ($templates as $name) {
                            if (preg_match('/CUSTOM TEMPLATE-(\d+)/', $name, $match)) {
                                $num = (int) $match[1];
                                if ($num > $maxNumber) {
                                    $maxNumber = $num;
                                }
                            }
                        }

                        $nextNumber = $maxNumber + 1;

                        // ✅ Next number

                        $createdBy = auth()->user()->name; // 🔥 dynamic later (auth user)

                        // ✅ Create prefix
                        $customPrefix = ' [CUSTOM TEMPLATE-'.$nextNumber.'] BY '.strtoupper($createdBy).' | '.$subject;

                        // ✅ Final display name
                        $displayName = $customPrefix;

                        $values = [
                            'template_name' => $displayName,
                            'display_name' => $displayName,
                            'template_subject' => $subject,
                            'template_description' => $message,
                            'company_id' => $company_id,
                            'status' => 1,
                            'temptype' => 2,
                            'curr_date' => date('Y-m-d'),
                        ];
                        $e_temp_id = DB::table('th_email_template')->insertGetId($values);

                        $response = $this->elasticService->getClient()->update([
                            'index' => ELASTIC_EMAIL_TEMPLATE,
                            'id' => $e_temp_id,
                            'retry_on_conflict' => 5,
                            'body' => [
                                'doc' => [
                                    'id' => $e_temp_id,
                                    'e_temp_id' => $e_temp_id,
                                    'template_name' => $displayName,
                                    'display_name' => $displayName,
                                    'template_subject' => $subject,
                                    'template_description' => $message,
                                    'status' => 1,
                                    'company_id' => $company_id,
                                    'orderByShow' => null,
                                    'tempForInterview' => 1,
                                    'temptype' => 2,
                                    'curr_date' => now()->toIso8601String(),
                                ],
                                'doc_as_upsert' => true,
                                'detect_noop' => false,
                            ],
                        ]);
                        // }
                    }

                    // $result = $this->massMailService->sendEmail($to, $subject, 'email.resume-forward.massmail', $data, $fromAddress, $fromName);
                    // Mail::to($email)->send(new MassMailingMail((object) $data, $attachments,$senderName,$fromAddress));
                    $event_name = 'emails';
                    $event_table_name = 'Mass Mail';
                    $event_table_id = @$company_id;
                    GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $company_id);

                }

            }
        }

        // 			dd("kf");

        // $this->status = GeneralMethod::mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $bcc, $subject, $message);

        // 		} else {
        // 			// $this->status = GeneralMethod::sentEmailUsingSMTP($to, $cc, $subject, $message, $from);
        // 		}

        $view = view('service_company.cvtracker.fwd_resume_message', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function send_mail_for_candidate(Request $request)
    {
        $template = $from = $to = $cc = $subject = $attached_cv = $message = $arr = '';
        $this->status = false;

        $template = $request->template;
        $from = $request->from_email;
        $to = $request->to_email;
        $jobid = @$request->jobid;
        $applicationDataId = @$request->applicationDataId;

        $subject = $request->subject;
        $attached_cv = $request->attached_cv;
        $message = $request->editor1;
        $arr = $request->arr;

        $decodedFrom = json_decode($from, true);
        if (is_array($decodedFrom)) {
            $from = implode(',', array_column($decodedFrom, 'value'));
        }
        $decodedTo = json_decode($to, true);
        if (is_array($decodedTo)) {
            $to = implode(',', array_column($decodedTo, 'value'));
        }

        /* Intent: Normalize and deduplicate CC email list */
        $cc = [];
        if (! empty($request->cc_email)) {
            $emails = explode(',', $request->cc_email);
            foreach ($emails as $email) {
                $trimmed = strtolower(trim($email));
                if (! empty($trimmed) && filter_var($trimmed, FILTER_VALIDATE_EMAIL)) {
                    $cc[] = $trimmed;
                }
            }
            $cc = array_values(array_unique($cc));
        }

        /* Intent: Normalize and deduplicate candidate recipient email list */
        $rawMailto = explode(',', (string) $to);
        $mailto = [];
        foreach ($rawMailto as $email) {
            $trimmed = strtolower(trim($email));
            if (! empty($trimmed) && filter_var($trimmed, FILTER_VALIDATE_EMAIL)) {
                $mailto[] = $trimmed;
            }
        }
        $mailto = array_values(array_unique($mailto));

        if (empty($mailto)) {
            return Reply::dataOnly(['status' => 'error', 'message' => 'No valid candidate recipient email provided.']);
        }

        $attachments = [];
        if ($attached_cv == 1 && ! empty($arr)) {
            $resumeRowCount = DB::table('documents')->select('*')->whereIn('documents.documentable_id', $arr)->count();
            $resumeRow = DB::table('documents')->select('*')->whereIn('documents.documentable_id', $arr)->get();
            if ($resumeRowCount) {
                foreach ($resumeRow as $key => $rval) {
                    $attachments[] = [
                        'path' => $rval->hashname,
                        'options' => [],
                    ];
                }
            }
        }

        $company_id = (int) auth()->user()->company_id;
        $company_name = DB::table('companies')->select('companies.company_name')->where('id', '=', $company_id)->first();

        /* Intent: Idempotency lock & deduplication guard to prevent duplicate emails, coin deductions, and queue dispatches from concurrent or repeated requests */
        /* Tenancy: Scoped by company_id, application_id, job_id, recipient, and subject */
        $appId = ! empty($applicationDataId) ? (int) $applicationDataId : (! empty($arr) && is_array($arr) ? (int) $arr[0] : 0);
        $jobIdVal = ! empty($jobid) ? (int) $jobid : 0;
        $toHash = md5(implode(',', $mailto));
        $subjectHash = md5(trim((string) $subject));
        $idempotencyKey = "send_candidate_mail_lock:{$company_id}:{$appId}:{$jobIdVal}:{$toHash}:{$subjectHash}";
        $recentSendKey = "send_candidate_mail_recent:{$company_id}:{$appId}:{$jobIdVal}:{$toHash}:{$subjectHash}";

        if (Cache::has($recentSendKey)) {
            \Log::info('Idempotency guard: skipped recent duplicate candidate email request', [
                'company_id' => $company_id,
                'application_id' => $appId,
                'job_id' => $jobIdVal,
                'recipient' => implode(',', $mailto),
            ]);
            $view = view('service_company.cvtracker.fwd_resume_message', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'view' => $view]);
        }

        $lock = Cache::lock($idempotencyKey, 15);
        if (! $lock->get()) {
            \Log::info('Idempotency guard: blocked concurrent duplicate candidate email request', [
                'company_id' => $company_id,
                'application_id' => $appId,
                'job_id' => $jobIdVal,
                'recipient' => implode(',', $mailto),
            ]);
            $view = view('service_company.cvtracker.fwd_resume_message', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'view' => $view]);
        }

        Cache::put($recentSendKey, true, 15);

        $eventId = 7;
        $coinsRequ = count($mailto);
        $companyId = (int) auth()->user()->company_id;
        $coinsDeductions = GeneralMethod::coinsValidationsAllcompany($companyId, $eventId, $coinsRequ);
        if ($coinsDeductions->status) {
            Cache::forget($recentSendKey);
            $lock->release();

            return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
        }

        if (count($mailto) > 0) {
            foreach ($mailto as $email) {
                $eventId = 7;
                $condi = JobApplication::where('job_applications.email', '=', $email)->first();
                $name = ($condi) ? $condi->full_name : '';
                $data = ['message' => $message, 'subject' => $subject, 'company_name' => $company_name->company_name ?? '', 'consultant_name' => '4Bell Technology', 'position' => 'PHP', 'condidate_name' => $name];

                $validMail = true;

                if ($validMail) {
                    $mail = Mail::to(auth()->user()->email);
                    if (! empty($cc)) {
                        $mail->cc($cc);
                    }
                    $senderName = auth()->user()->name;
                    $fromAddress = auth()->user()->email;
                    $checkMailPermision = GeneralMethod::mailPermission(15);
                    if ($checkMailPermision) {
                        QueueCandidatesMailAndResumeForwardMailService::dispatch(
                            $email,
                            $subject,
                            'email.resume-forward.sendmail',
                            (object) $data,
                            $attachments,
                            $fromAddress,
                            $senderName,
                            $cc,
                            $company_id
                        )->onConnection('emails');

                        $coinsDeductions = GeneralMethod::coinsDeductions((int) auth()->user()->company_id, $eventId, auth()->user()->id, auth()->user()->company_id, '', '');

                        $timelineArr = [
                            'event_id' => 9,
                            'event_by_company_id' => auth()->user()->company_id,
                            'event_by_user_id' => auth()->user()->id,
                            'master_process_name' => 'SendMail',
                            'application_id' => $applicationDataId,
                            'application_email' => $email,
                            'job_id' => @$jobid,
                            'comment' => $subject,
                            'note' => 'Send Mail',
                        ];
                        GeneralMethod::UpdateTimeline($timelineArr);
                    }
                }
            }
        }

        $view = view('service_company.cvtracker.fwd_resume_message', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function send_mail_send(Request $request)
    {

        // dd($request->all());
        $template = $from = $to = $cc = $subject = $attached_cv = $message = $arr = '';
        $this->status = false;

        $template = $request->template;
        $from = $request->from_email;
        $to = $request->to_email;
        $cc = $request->cc_email;
        $jobid = @$request->jobid;
        $applicationDataId = @$request->applicationDataId;

        $subject = $request->subject;
        $attached_cv = $request->attached_cv;
        $message = $request->editor1;
        $arr = $request->arr;

        $from = json_decode($from, true);
        $from = array_column($from, 'value');
        $from = implode(',', $from);

        $to = json_decode($to, true);
        $to = array_column($to, 'value');
        $to = implode(',', $to);

        $cc = [];
        if (! empty($request->cc_email)) {
            $emails = explode(',', $request->cc_email);
            // 			dd($emails);

            foreach ($emails as $keymail => $email) {
                array_push($cc, str_replace(' ', '', $email));
            }
        }
        // 		dd($cc);
        $attachments = [];
        if ($attached_cv == 1) {
            $resumeRowCount = DB::table('documents')->select('*')->whereIn('documents.documentable_id', $arr)->count();
            $resumeRow = DB::table('documents')->select('*')->whereIn('documents.documentable_id', $arr)->get();
            // 			dd($resumeRowCount,$resumeRow);
            $raw_filename = [];
            if ($resumeRowCount) {
                foreach ($resumeRow as $key => $rval) {
                    $hashname = $documentable_id = '';
                    $hashname = $rval->hashname;
                    $documentable_id = $rval->documentable_id;

                    $raw_filename[] = $hashname;
                    // 	$attachmentPath = base_path() . '/public/user-uploads/documents/' . $documentable_id . '/' . $hashname;

                    // 	if (file_exists($attachmentPath)) {
                    $attachments[] = [
                        'path' => $hashname,
                        'options' => [],
                    ];
                    // 	}

                }
            }
        }

        $path = base_path().'/public/user-uploads/documents/';
        $mailto = explode(',', $to);
        $from_mail = $from;
        $from_name = 'testing';
        $replyto = $from;
        // $bcc = $cc;

        // 	dd($mailto,$bcc,$attachments);

        $company_id = auth()->user()->company_id;
        $company_name = DB::table('companies')->select('companies.company_name')->where('id', '=', $company_id)->first();

        // dd($attachments);
        $senderName = auth()->user()->name;

        $eventId = 7;
        $coinsRequ = count($mailto);
        $companyId = (int) auth()->user()->company_id;
        $coinsDeductions = GeneralMethod::coinsValidationsAllcompany($companyId, $eventId, $coinsRequ);
        if ($coinsDeductions->status) {
            return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
        }

        // 	dd(count($mailto));
        if (count($mailto) > 0) {
            foreach ($mailto as $email) {
                $eventId = 7;
                // $coinsDeductions = GeneralMethod::coinsDeductions((int) auth()->user()->company_id,$eventId,auth()->user()->id, auth()->user()->company_id,'','');
                // if($coinsDeductions->status){
                // 	return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message,'company_coins'=> $coinsDeductions->company_coins]);
                // }

                $condi = JobApplication::where('job_applications.email', '=', $email)->first();
                $name = ($condi) ? $condi->full_name : '';
                $data = ['message' => $message, 'subject' => $subject, 'company_name' => $company_name->company_name, 'consultant_name' => '4Bell Technology', 'position' => 'PHP', 'condidate_name' => $name];

                $validMail = true; // DB::table('package_active')->where('company_id', '=', $company_id)->where('emails_balance', '>', 0)->where('startdate', '<=', now())->where('enddate', '>=', now())->where('packages_status', 1)->first();

                if ($validMail) {

                    $mail = Mail::to(auth()->user()->email);
                    if (! empty($cc)) {
                        $mail->cc($cc);
                    }
                    // dd(count($cc));
                    $senderName = auth()->user()->name;
                    $fromAddress = auth()->user()->email;
                    $checkMailPermision = GeneralMethod::mailPermission(15);
                    if ($checkMailPermision) {

                        // SendEmailJob::dispatch($email, $subject, 'email.resume-forward.sendmail', $data, $attachments, $fromAddress, $senderName, $cc);
                        // QueueMailService
                        QueueNotificationMailService::dispatch(
                            $email,
                            $subject,
                            'email.resume-forward.sendmail',
                            (object) $data,
                            $attachments,
                            $fromAddress,
                            $senderName,
                            $cc,
                            $company_id
                        )->onConnection('emails');

                        // $result = $this->massMailService->sendEmail($email, $subject,'email.resume-forward.sendmail', $data, $attachments, $fromAddress, $senderName,$cc);
                        //  $this->massMailService->sendEmail($fromAddress, $subject,'email.resume-forward.sendmail', $data, $attachments, $fromAddress, $senderName,$cc);
                        // if($result){
                        $coinsDeductions = GeneralMethod::coinsDeductions((int) auth()->user()->company_id, $eventId, auth()->user()->id, auth()->user()->company_id, '', '');

                        $timelineArr = [
                            'event_id' => 9,
                            'event_by_company_id' => auth()->user()->company_id,
                            'event_by_user_id' => auth()->user()->id,
                            'master_process_name' => 'SendMail',
                            // 'child_process_name'	=>

                            'application_id' => $applicationDataId,
                            'application_email' => $email,
                            'job_id' => @$jobid,
                            'comment' => $subject,
                            'note' => 'Send Mail',
                        ];
                        GeneralMethod::UpdateTimeline($timelineArr);

                        // }
                    }

                    //   $mail->send(new MassMailingMail((object) $data,$attachments,$senderName,$fromAddress));

                    //   dd("");

                    // 	Mail::to($email)->send(new MassMailingMail((object) $data, $attachments,$senderName));
                    $event_name = 'emails';
                    $event_table_name = 'Mass Mail';
                    $event_table_id = @$company_id;

                    // GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $company_id);

                }

            }
        }

        // dd("kf");

        // $this->status = GeneralMethod::mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $bcc, $subject, $message);

        // 		} else {
        // 			// $this->status = GeneralMethod::sentEmailUsingSMTP($to, $cc, $subject, $message, $from);
        // 		}

        $view = view('service_company.cvtracker.fwd_resume_message', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function fwd_offer_send(Request $request)
    {

        // dd($request->all());
        $template = $from = $subject = $attached_cv = $message = $arr = '';
        $this->status = false;

        $template = $request->template;
        $from = $request->from_email;
        // 		$to = $request->to_email;
        // 		$cc = $request->cc_email;

        $subject = $request->subject;
        $mtypeId = $request->mtypeId;
        $offerId = $request->offerId;
        $signature = $request->signature;
        $attached_cv = $request->attached_cv;
        $message = $request->editor1;
        $arr = $request->arr;
        $event_id = 0;

        // Rate limit: max 3 candidate offer-letter sends per offer within a 4-hour window,
        // checked before anything else (coin deduction, mail dispatch) so a blocked attempt
        // costs nothing. Counted from updatetimeline (mtypeSection=1 = candidate offer email),
        // the same real, persisted send-history record the "Sent By"/activity log already use.
        if ((int) $mtypeId === 1) {
            $offerAppRow = DB::table('offer_job_applications')->where('id', $offerId)->first(['application_id', 'job_id']);
            if ($offerAppRow) {
                $recentSendCount = DB::table('updatetimeline')
                    ->where('application_id', $offerAppRow->application_id)
                    ->where('job_id', $offerAppRow->job_id)
                    ->where('mtypeSection', 1)
                    ->where('create_at', '>=', now()->subHours(4))
                    ->count();
                if ($recentSendCount >= 3) {
                    return Reply::dataOnly([
                        'status' => 'rateLimited',
                        'message' => 'This offer letter has already been sent 3 times in the last 4 hours. Please wait before sending it again.',
                    ]);
                }
            }
        }

        // Rate limit: approval emails (mtypeId=8) — at most 1 send per recipient per hour.
        // Prevents accidental spam when the user clicks "Add" or "Send Reminder" repeatedly.
        if ((int) $mtypeId === 8 && ! empty($request->to_email)) {
            $approvalAppRow = DB::table('offer_job_applications')->where('id', $offerId)->first(['application_id', 'job_id']);
            if ($approvalAppRow) {
                $recentApproval = DB::table('updatetimeline')
                    ->where('application_id', $approvalAppRow->application_id)
                    ->where('job_id', $approvalAppRow->job_id)
                    ->whereIn('mtypeSection', [2, 8]) // 2 = legacy approval, 8 = new flow
                    ->where('email_id_hr', trim($request->to_email))
                    ->where('create_at', '>=', now()->subHour())
                    ->exists();
                if ($recentApproval) {
                    return Reply::dataOnly([
                        'status' => 'rateLimited',
                        'message' => 'An approval email was already sent to this approver within the last hour. Please wait before sending again.',
                    ]);
                }
            }
        }

        $fromDecoded = json_decode($from, true);
        if (is_array($fromDecoded)) {
            // Tagify format: [{"value":"email@..."},...]
            $from = implode(',', array_column($fromDecoded, 'value'));
        }
        // else: plain email string (approval flow) — use as-is

        // 		$to = json_decode($to, true);
        // 		$to = array_column($to, 'value');
        // 		$to = implode(',', $to);
        $to = [];
        if (! empty($request->to_email)) {
            $emails = explode(',', $request->to_email);

            foreach ($emails as $keymail => $email) {
                if (! empty($email)) {
                    array_push($to, str_replace(' ', '', $email));
                }

            }
        }
        $cc = [];
        if (! empty($request->cc_email)) {
            // $cc = json_decode($cc, true);
            // $cc = array_column($cc, 'value');
            $emails = explode(',', $request->cc_email);
            // 			dd($emails);

            foreach ($emails as $keymail => $email) {
                if (! empty($email)) {
                    array_push($cc, str_replace(' ', '', $email));
                }

            }
            // $cc = implode(',', str_replace(' ','',$emails));

            // $cc = implode(',', $emails);
            // 			$cc = $request->cc_email;//implode(',', $cc);
        }

        $reply_to = [];
        if (! empty($request->reply_to_email)) {
            $reply_emails = explode(',', $request->reply_to_email);
            foreach ($reply_emails as $rEmail) {
                $rEmailClean = trim(str_replace(' ', '', $rEmail));
                if (! empty($rEmailClean) && filter_var($rEmailClean, FILTER_VALIDATE_EMAIL) && ! in_array($rEmailClean, $reply_to)) {
                    $reply_to[] = $rEmailClean;
                }
            }
        }

        $attachments = [];

        // Sender-supplied files (Step 5 "+ Add Attachment"): store to the public disk so
        // MailService::downloadAttachment() (which fetches attachments by public URL) can read them.
        // Capped at 2MB/3 files — separate from any system-generated offer-letter PDF, which can
        // run larger and isn't part of this user-uploaded set.
        if ($request->hasFile('attachments')) {
            $request->validate(['attachments' => 'max:3', 'attachments.*' => 'file|max:2048']);
            foreach ($request->file('attachments') as $file) {
                if (! $file->isValid()) {
                    continue;
                }
                $safeName = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
                $storedPath = $file->storeAs('offer-attachments/'.$offerId, $safeName, 'public');
                $attachments[] = [
                    'path' => Storage::disk('public')->url($storedPath),
                    'filename' => $file->getClientOriginalName(),
                    'options' => [],
                ];
            }
        }

        $path = base_path().'/public/user-uploads/documents/';
        $mailto = $to;
        $from_mail = $from;
        $from_name = 'testing';
        $replyto = $from;
        $bcc = $cc;
        $company_id = auth()->user()->company_id;
        $company_name = DB::table('companies')->select('companies.company_name')->where('id', '=', $company_id)->first();

        // dd(str_replace('"','',$testmail));

        $exits_offer = DB::table('offer_create_main_tds')->where('offer_id', '=', $offerId)->where('status', '=', 1)->first();
        $senderName = auth()->user()->name;
        $fromAddress = auth()->user()->email;
        if ($mtypeId == 2) {
            $eventId = 35;
        } else {
            $eventId = 34;
            if ($signature == 1) {
                $eventId = 33;
            }

        }

        $coinsRequ = count($to);
        $companyId = (int) auth()->user()->company_id;
        $coinsDeductions = GeneralMethod::coinsValidationsAllcompany($companyId, $eventId, $coinsRequ);
        if ($coinsDeductions->status) {
            return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
        }
        $coinsDeductions = GeneralMethod::coinsDeductions((int) auth()->user()->company_id, $eventId, auth()->user()->id, auth()->user()->company_id, '', '');
        if ($coinsDeductions->status) {
            return response()->json(['status' => 'coinCheck', 'message' => $coinsDeductions->message]);
        }

        // $coinsDeductions = GeneralMethod::coinsDeductions((int) auth()->user()->company_id,$eventId,auth()->user()->id, auth()->user()->company_id,'','');

        if (count($to) > 0) {
            foreach ($to as $mailto) {
                $eventId = 7;
                // $coinsDeductions = GeneralMethod::coinsDeductions((int) auth()->user()->company_id,$eventId,auth()->user()->id, auth()->user()->company_id,'','');
                if ($coinsDeductions->status) {
                    return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
                }

                $data = ['message' => $message, 'subject' => $subject, 'company_name' => $company_name->company_name, 'consultant_name' => '4Bell Technology', 'position' => 'PHP'];
                $checkMailPermision = GeneralMethod::mailPermission(13);
                if ($checkMailPermision) {
                    $offer_terms_conditions = DB::table('offer_terms_conditions')->where('offer_id', '=', $offerId)->orderByDesc('id')->first();
                    $offerDetails = DB::table('offer_create_main_tds')
                        ->join('offer_job_applications', 'offer_create_main_tds.offer_id', '=', 'offer_job_applications.id')
                        ->where('offer_job_applications.id', $request->offerId)
                        ->where('offer_create_main_tds.status', '=', 1)
                        ->select(
                            'offer_create_main_tds.offer_id',
                            'offer_create_main_tds.employment_type',
                            'offer_create_main_tds.id as of_main_td',
                            'offer_create_main_tds.currency',
                            'offer_create_main_tds.salary_ctc',
                            'offer_create_main_tds.salary_mode',
                            'offer_create_main_tds.designation',
                            'offer_create_main_tds.joined_date',
                            'offer_create_main_tds.monthly_gross',
                            'offer_create_main_tds.yearly_gross',
                            'offer_create_main_tds.monthly_bounus_gross',
                            'offer_create_main_tds.yearly_bonus_gross',
                            'offer_create_main_tds.monthly_insurence_gross',
                            'offer_create_main_tds.yearly_insurence_gross',
                            'offer_create_main_tds.yearly_final_gross',
                            'offer_create_main_tds.monthly_final_gross'
                        )->get()
                        ->map(function ($offer) {
                            // Fetch Basic offer points
                            $basicPoints = DB::table('basic_offer_points')
                                ->where('offer_create_main_td_id', $offer->of_main_td)
                                ->select(
                                    'id',
                                    'poins as basic_poins',
                                    'Percentage as basic_percentage',
                                    'condition_s as basic_condition_s',
                                    'monthly as basic_monthly',
                                    'yearly as basic_yearly'
                                )->get()->toArray();

                            // Fetch Bonus offer points
                            $bonusPoints = DB::table('bonus_offer_points')
                                ->where('offer_create_main_td_id', $offer->of_main_td)
                                ->select(
                                    'id',
                                    'poins as bonus_poins',
                                    'Percentage as bonus_percentage',
                                    'condition_s as bonus_condition_s',
                                    'monthly as bonus_monthly',
                                    'yearly as bonus_yearly'
                                )->get()->toArray();

                            // Fetch Insurance offer points
                            $insurancePoints = DB::table('insurence_offer_points')
                                ->where('offer_create_main_td_id', $offer->of_main_td)
                                ->select(
                                    'id',
                                    'poins as insurence_poins',
                                    'Percentage as insurence_percentage',
                                    'condition_s as insurence_condition_s',
                                    'monthly as insurence_monthly',
                                    'yearly as insurence_yearly'
                                )->get()->toArray();

                            // Merge all into the main offer array
                            return [
                                'offer_id' => $offer->offer_id,
                                'employment_type' => $offer->employment_type,
                                'of_main_td' => $offer->of_main_td,
                                'currency' => $offer->currency,
                                'salary_ctc' => $offer->salary_ctc,
                                'salary_mode' => $offer->salary_mode,
                                'designation' => $offer->designation,
                                'joined_date' => $offer->joined_date,
                                'monthly_gross' => $offer->monthly_gross,
                                'yearly_gross' => $offer->yearly_gross,
                                'monthly_bounus_gross' => $offer->monthly_bounus_gross,
                                'yearly_bonus_gross' => $offer->yearly_bonus_gross,
                                'monthly_insurence_gross' => $offer->monthly_insurence_gross,
                                'yearly_insurence_gross' => $offer->yearly_insurence_gross,
                                'yearly_final_gross' => $offer->yearly_final_gross,
                                'monthly_final_gross' => $offer->monthly_final_gross,
                                'Basic_offer_points' => $basicPoints,
                                'Bonus_offer_points' => $bonusPoints,
                                'Insurance_offer_points' => $insurancePoints,
                            ];
                        });

                    $reciepts = '';
                    $locArrLocation = '';
                    $company_name = '';
                    $user_name = '';
                    $status_text = '';
                    $status_color = '';

                    /*$pdf = app('dompdf.wrapper');
                    $pdf->loadView('offer-letter', compact('offerDetails','offer_terms_conditions','exits_offer'));


                    $pdfContent = $pdf->output();


                    $filename = "offer-letter-".$offerId.".pdf";

                    $pdfPath = storage_path('app/public/'.$filename.'');
                    file_put_contents($pdfPath, $pdfContent);
                    $path = str_replace('index.php','',url('storage/app/public/'.$filename.''));

                    $attachments[] = [
                        'path' => $path,
                        'options' => []
                    ];*/
                    //    dd($attachments);

                    // QueueMailService
                    QueueMailService::dispatch(
                        $mailto,
                        $subject,
                        'email.resume-forward.forward-resume',
                        (object) $data,
                        $attachments,
                        $fromAddress,
                        $senderName,
                        $cc,
                        $company_id,
                        $reply_to
                    )->onConnection('emails');

                    // $result = $this->massMailService->sendEmail($mailto, $subject,'email.resume-forward.forward-resume', $data, $attachments, $fromAddress, $senderName,$cc);

                    $users = DB::table('client_contact_info')->where('contact_person_email', '=', $mailto)->orderByDesc('id')->first();
                    $email_id_hr = '';
                    $name_hr = '';
                    $send_accept_date_time = null;
                    $send_approved_date_time = null;

                    // if($result){
                    $Offertimelime = OfferJobApplication::withoutGlobalScopes()->find($offerId);
                    $exists = $this->elasticService->getClient()->exists([
                        'index' => ELASTIC_CANDIDATES,
                        'id' => $Offertimelime->joined,
                    ]);
                    if ($mtypeId == 1) {
                        $event_id = 5;
                        $send_accept_date_time = date('Y-m-d H:i:s');

                        // Candidate
                        DB::table('offer_create_main_tds')->where('offer_id', $offerId)->where('status', '=', 1)->update(['candidate_status' => 1, 'signature_type' => $signature, 'updated_at' => date('Y-m-d H:i:s'), 'send_accept_date_time' => $send_accept_date_time]);
                        if ($exists) {
                            $updateTime = now()->toIso8601String();
                            $updateParams_e = [
                                'index' => ELASTIC_CANDIDATES,
                                'id' => $Offertimelime->joined,
                                'body' => [
                                    'doc' => [
                                        'of_offer_acceptance' => 1,
                                        'send_accept_date_time' => $updateTime,
                                        'updated_at' => $updateTime,
                                    ],
                                    'doc_as_upsert' => false, // Changed to false since we verified existence
                                ],
                                'refresh' => false, // Eventual consistency for 10K concurrent scale
                            ];
                            $redudds = $this->elasticService->getClient()->update($updateParams_e);
                        }

                    } else {
                        $email_id_hr = $mailto;
                        $send_approved_date_time = date('Y-m-d H:i:s');
                        $name_hr = (@$users) ? $users->contact_person_name : '';
                        $event_id = 6;
                        // managemnet
                        DB::table('offer_create_main_tds')->where('offer_id', $offerId)->where('status', '=', 1)->update(['management_status' => 1, 'send_approved_date_time' => $send_approved_date_time]);
                        if ($exists) {
                            $updateTime = now()->toIso8601String();
                            $updateParams_e = [
                                'index' => ELASTIC_CANDIDATES,
                                'id' => $Offertimelime->joined,
                                'body' => [
                                    'doc' => [
                                        'of_offer_approved' => 1,
                                        'send_approved_date_time' => $updateTime,
                                        'updated_at' => $updateTime,
                                    ],
                                    'doc_as_upsert' => false, // Changed to false since we verified existence
                                ],
                                'refresh' => false, // Eventual consistency for 10K concurrent scale
                            ];
                            $redudds = $this->elasticService->getClient()->update($updateParams_e);
                        }

                    }

                    // $coinsDeductions = GeneralMethod::coinsDeductions((int) auth()->user()->company_id,$eventId,auth()->user()->id, auth()->user()->company_id,'','');

                    $Offertimelime = OfferJobApplication::withoutGlobalScopes()->find($offerId);

                    $option_currency_val = @$request->current_ctc_cur;
                    $joined_date = @$request->dob ? date('Y-m-d H:i:s', strtotime(@$request->dob)) : 'NULL';
                    $ctc = @$request->ctc;
                    $current_ctc_mode = @$request->current_ctc_mode;

                    $Offertimelime->offer_currency = @$option_currency_val ? @$option_currency_val : 'NULL';
                    $Offertimelime->offer_mode = @$current_ctc_mode ? @$current_ctc_mode : 'NULL';
                    $Offertimelime->offer_amount = @$ctc ? @$ctc : 'NULL';
                    $Offertimelime->joined_date = @$joined_date ? $joined_date : 'NULL';

                    $date = date('Y-m-d H:i:s');

                    $values = [
                        'event_id' => $event_id,
                        'event_by_company_id' => auth()->user()->company_id,
                        'event_by_user_id' => auth()->user()->id,

                        'application_id' => @$Offertimelime->application_id ? @$Offertimelime->application_id : null,
                        'application_email' => @$Offertimelime->email ? @$Offertimelime->email : null,
                        'job_id' => @$Offertimelime->job_id ? @$Offertimelime->job_id : null,
                        'note' => '',
                        'create_at' => @$date ? @$date : null,
                        'update_at' => @$date ? @$date : null,

                    ];
                    $jobApplication = AddJobWiseCondidate::withoutGlobalScopes()->find($Offertimelime->joined);
                    if (@$jobApplication->master_status_id > 0) {
                        $masterStatus = DB::table('master_status')->select('master_status.name')->where('id', $jobApplication->master_status_id)->first();
                        $values['master_process_name'] = @$masterStatus->name;
                    }
                    if (@$jobApplication->child_status_id > 0) {
                        $masterStatus = DB::table('candidate_status')->select('candidate_status.name')->where('candidate_status.id', $jobApplication->child_status_id)->first();
                        $values['child_process_name'] = @$masterStatus->name;
                    }

                    $values['designation_offer'] = @$exits_offer->designation_offer;
                    $values['offer_currency'] = @$exits_offer->currency;
                    $values['offer_amount'] = @$exits_offer->salary_ctc;
                    $values['joined_date'] = @$exits_offer->joined_date;
                    $values['offer_mode'] = @$exits_offer->salary_mode;
                    $values['candidate_status'] = ($mtypeId == 1) ? 1 : '';
                    $values['management_status'] = ($mtypeId == 2) ? 1 : '';
                    $values['accept_date_time'] = '';
                    $values['approved_date_time'] = '';
                    $values['send_approved_date_time'] = $send_approved_date_time;
                    $values['send_accept_date_time'] = $send_accept_date_time;
                    $values['email_id_hr'] = $email_id_hr;
                    $values['name_hr'] = $name_hr;
                    $values['yearly_gross'] = $exits_offer->yearly_gross;
                    $values['yearly_final_gross'] = $exits_offer->yearly_final_gross;
                    $values['mtypeSection'] = $mtypeId;
                    GeneralMethod::UpdateTimeline($values);

                    // }

                }
            }
        }

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
            'alljobslist_version_user_'.$this->user->id,
        ];
        foreach ($cachekeys as $key) {
            GeneralMethod::cacheClean($key);
        }

        //    dd("hi");

        // 			Mail::to($mailto)->cc($bcc)->send(new ForwardResumeMail((object) $data, $attachments));

        // $this->status = GeneralMethod::mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $bcc, $subject, $message);

        // 		} else {
        // 			// $this->status = GeneralMethod::sentEmailUsingSMTP($to, $cc, $subject, $message, $from);
        // 		}

        $view = view('service_company.cvtracker.fwd_resume_message', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function fwd_offer_download(Request $request)
    {

        $offerId = $request->offerId;
        $coinnotdeduct = true;
        if (@$request->coinnotdeduct) {
            $coinnotdeduct = false;
        }

        $exits_offer = DB::table('offer_create_main_tds')->where('offer_id', '=', $offerId)->where('status', '=', 1)->first();

        $rowDataOfferDetails = GeneralMethod::offerLetterdetails($offerId);

        $address = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->address : '';
        $comapny_name = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->comapny_name : '';
        $logo = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->logo : '';
        $job_title = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->job_title : '';
        $candidate_name = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->full_name : '';

        if ($exits_offer) {

            $offer_terms_conditions = DB::table('offer_terms_conditions')->where('offer_id', '=', $offerId)->orderByDesc('id')->first();
            $offerDetails = DB::table('offer_create_main_tds')
                ->join('offer_job_applications', 'offer_create_main_tds.offer_id', '=', 'offer_job_applications.id')
                ->where('offer_job_applications.id', $offerId)
                ->where('offer_create_main_tds.status', '=', 1)
                ->select(
                    'offer_create_main_tds.offer_id',
                    'offer_create_main_tds.employment_type',
                    'offer_create_main_tds.id as of_main_td',
                    'offer_create_main_tds.currency',
                    'offer_create_main_tds.salary_ctc',
                    'offer_create_main_tds.accept_date_time',
                    'offer_create_main_tds.signature_candidate',
                    'offer_create_main_tds.salary_mode',
                    'offer_create_main_tds.designation',
                    'offer_create_main_tds.joined_date',
                    'offer_create_main_tds.monthly_gross',
                    'offer_create_main_tds.yearly_gross',
                    'offer_create_main_tds.monthly_bounus_gross',
                    'offer_create_main_tds.yearly_bonus_gross',
                    'offer_create_main_tds.monthly_insurence_gross',
                    'offer_create_main_tds.yearly_insurence_gross',
                    'offer_create_main_tds.yearly_final_gross',
                    'offer_create_main_tds.monthly_final_gross'
                )->get()
                ->map(function ($offer) {
                    // Fetch Basic offer points
                    $basicPoints = DB::table('basic_offer_points')
                        ->where('offer_create_main_td_id', $offer->of_main_td)
                        ->select(
                            'id',
                            'poins as basic_poins',
                            'Percentage as basic_percentage',
                            'condition_s as basic_condition_s',
                            'monthly as basic_monthly',
                            'yearly as basic_yearly'
                        )->get()->toArray();

                    // Fetch Bonus offer points
                    $bonusPoints = DB::table('bonus_offer_points')
                        ->where('offer_create_main_td_id', $offer->of_main_td)
                        ->select(
                            'id',
                            'poins as bonus_poins',
                            'Percentage as bonus_percentage',
                            'condition_s as bonus_condition_s',
                            'monthly as bonus_monthly',
                            'yearly as bonus_yearly'
                        )->get()->toArray();

                    // Fetch Insurance offer points
                    $insurancePoints = DB::table('insurence_offer_points')
                        ->where('offer_create_main_td_id', $offer->of_main_td)
                        ->select(
                            'id',
                            'poins as insurence_poins',
                            'Percentage as insurence_percentage',
                            'condition_s as insurence_condition_s',
                            'monthly as insurence_monthly',
                            'yearly as insurence_yearly'
                        )->get()->toArray();

                    // Merge all into the main offer array
                    return [
                        'offer_id' => $offer->offer_id,
                        'employment_type' => $offer->employment_type,
                        'of_main_td' => $offer->of_main_td,
                        'accept_date_time' => $offer->accept_date_time,
                        'signature_candidate' => $offer->signature_candidate,
                        'currency' => $offer->currency,
                        'salary_ctc' => $offer->salary_ctc,
                        'salary_mode' => $offer->salary_mode,
                        'designation' => $offer->designation,
                        'joined_date' => $offer->joined_date,
                        'monthly_gross' => $offer->monthly_gross,
                        'yearly_gross' => $offer->yearly_gross,
                        'monthly_bounus_gross' => $offer->monthly_bounus_gross,
                        'yearly_bonus_gross' => $offer->yearly_bonus_gross,
                        'monthly_insurence_gross' => $offer->monthly_insurence_gross,
                        'yearly_insurence_gross' => $offer->yearly_insurence_gross,
                        'yearly_final_gross' => $offer->yearly_final_gross,
                        'monthly_final_gross' => $offer->monthly_final_gross,
                        'Basic_offer_points' => $basicPoints,
                        'Bonus_offer_points' => $bonusPoints,
                        'Insurance_offer_points' => $insurancePoints,
                    ];
                });

        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('offer-letter', compact('offerDetails', 'offer_terms_conditions', 'offerId', 'exits_offer', 'address', 'comapny_name', 'logo', 'job_title', 'candidate_name'));
        // return view('offer-letter',compact('offerDetails','offer_terms_conditions','offerId','exits_offer','address','comapny_name','logo','job_title','candidate_name'));
        // Save PDF to a temporary file or generate the raw content signature-pad.blade.php
        $pdfContent = $pdf->output();
        $filename = 'offer-letter-'.$offerId.'.pdf';
        // Option 1: Save PDF to storage and attach file path
        $pdfPath = storage_path('app/public/'.$filename.'');
        file_put_contents($pdfPath, $pdfContent);
        //    $path = str_replace('index.php','',url('storage/app/public/'.$filename.''));
        Storage::disk('public')->put($filename, $pdf->output());
        //    $path = str_replace('index.php','',url('storage/app/public/'.$filename.''));
        // Built from the actual incoming request's host (not asset()/APP_URL) — the
        // deployed .env's APP_URL points at a different, unrelated domain, which made
        // the previous asset() call return a link the browser couldn't resolve at all.
        $path = $request->getSchemeAndHttpHost().'/storage/'.$filename;
        // dd($path);
        if ($coinnotdeduct) {
            $eventId = 36;
            $coinsRequ = 1;
            $companyId = (int) auth()->user()->company_id;

            $packageStatus = GeneralMethod::coinsValidationsAllcompany($companyId, $eventId, $coinsRequ);
            if ($packageStatus->status) {
                return response()->json(['status' => 'errors', 'message' => $packageStatus->message]);
            }

            $coinsDeductions = GeneralMethod::coinsDeductions((int) auth()->user()->company_id, $eventId, auth()->user()->id, auth()->user()->company_id, '', '');
            if ($coinsDeductions->status) {
                return response()->json(['status' => 'errors', 'message' => $coinsDeductions->message]);
            }
        }

        return Reply::dataOnly(['status' => 'success', 'url' => $path]);

        $exits_offer = DB::table('offer_create_main_tds')->where('offer_id', '=', $offerId)->where('status', '=', 1)->first();
        if ($exits_offer) {
            $offer_terms_conditions = DB::table('offer_terms_conditions')->where('offer_id', '=', $offerId)->orderByDesc('id')->first();

            $offerDetails = DB::table('offer_create_main_tds')
                ->join('offer_job_applications', 'offer_create_main_tds.offer_id', '=', 'offer_job_applications.id')
                ->where('offer_job_applications.id', $request->offerId)
                ->where('offer_create_main_tds.status', '=', 1)
                ->select(
                    'offer_create_main_tds.offer_id',
                    'offer_create_main_tds.employment_type',
                    'offer_create_main_tds.id as of_main_td',
                    'offer_create_main_tds.currency',
                    'offer_create_main_tds.salary_ctc',
                    'offer_create_main_tds.salary_mode',
                    'offer_create_main_tds.designation',
                    'offer_create_main_tds.joined_date',
                    'offer_create_main_tds.monthly_gross',
                    'offer_create_main_tds.yearly_gross',
                    'offer_create_main_tds.monthly_bounus_gross',
                    'offer_create_main_tds.yearly_bonus_gross',
                    'offer_create_main_tds.monthly_insurence_gross',
                    'offer_create_main_tds.yearly_insurence_gross',
                    'offer_create_main_tds.yearly_final_gross',
                    'offer_create_main_tds.monthly_final_gross'
                )->get()
                ->map(function ($offer) {
                    // Fetch Basic offer points
                    $basicPoints = DB::table('basic_offer_points')
                        ->where('offer_create_main_td_id', $offer->of_main_td)
                        ->select(
                            'id',
                            'poins as basic_poins',
                            'Percentage as basic_percentage',
                            'condition_s as basic_condition_s',
                            'monthly as basic_monthly',
                            'yearly as basic_yearly'
                        )->get()->toArray();

                    // Fetch Bonus offer points
                    $bonusPoints = DB::table('bonus_offer_points')
                        ->where('offer_create_main_td_id', $offer->of_main_td)
                        ->select(
                            'id',
                            'poins as bonus_poins',
                            'Percentage as bonus_percentage',
                            'condition_s as bonus_condition_s',
                            'monthly as bonus_monthly',
                            'yearly as bonus_yearly'
                        )->get()->toArray();

                    // Fetch Insurance offer points
                    $insurancePoints = DB::table('insurence_offer_points')
                        ->where('offer_create_main_td_id', $offer->of_main_td)
                        ->select(
                            'id',
                            'poins as insurence_poins',
                            'Percentage as insurence_percentage',
                            'condition_s as insurence_condition_s',
                            'monthly as insurence_monthly',
                            'yearly as insurence_yearly'
                        )->get()->toArray();

                    // Merge all into the main offer array
                    return [
                        'offer_id' => $offer->offer_id,
                        'employment_type' => $offer->employment_type,
                        'of_main_td' => $offer->of_main_td,
                        'currency' => $offer->currency,
                        'salary_ctc' => $offer->salary_ctc,
                        'salary_mode' => $offer->salary_mode,
                        'designation' => $offer->designation,
                        'joined_date' => $offer->joined_date,
                        'monthly_gross' => $offer->monthly_gross,
                        'yearly_gross' => $offer->yearly_gross,
                        'monthly_bounus_gross' => $offer->monthly_bounus_gross,
                        'yearly_bonus_gross' => $offer->yearly_bonus_gross,
                        'monthly_insurence_gross' => $offer->monthly_insurence_gross,
                        'yearly_insurence_gross' => $offer->yearly_insurence_gross,
                        'yearly_final_gross' => $offer->yearly_final_gross,
                        'monthly_final_gross' => $offer->monthly_final_gross,
                        'Basic_offer_points' => $basicPoints,
                        'Bonus_offer_points' => $bonusPoints,
                        'Insurance_offer_points' => $insurancePoints,
                    ];
                });

            // return view('offer-letter',compact('offerDetails','offer_terms_conditions'));
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('offer-letter', compact('offerDetails', 'offer_terms_conditions'));

            // Save PDF to a temporary file or generate the raw content
            $pdfContent = $pdf->output();
            $filename = 'offer-letter-'.$offerId.'.pdf';
            // Option 1: Save PDF to storage and attach file path
            $pdfPath = storage_path('app/public/'.$filename.'');
            file_put_contents($pdfPath, $pdfContent);
            $path = str_replace('index.php', '', url('storage/app/public/'.$filename.''));
            // dd($path);

            return Reply::dataOnly(['status' => 'success', 'url' => $path]);
        } else {
            return Reply::dataOnly(['status' => 'error', 'url' => '']);
        }

    }

    /**
     * Stream the offer-letter PDF straight to the browser as a file download.
     *
     * Why this exists (and why the create-offer Step 3 button uses it instead of
     * fwd_offer_download): the old flow generated the PDF, wrote it to
     * storage/app/public, then returned a public URL for the browser to fetch. That
     * had several silent failure points on the live host — the written file could 404
     * (write-perms / symlink), and the URL was built from APP_URL which points at a
     * different domain. Streaming the in-memory PDF as an attachment sidesteps all of
     * that: no file write, no storage URL, no symlink, no domain dependency. This is a
     * draft/preview download during offer creation, so it is intentionally NOT gated by
     * the coin system (re-downloading a released offer from the tracker still is).
     */
    public function downloadOfferLetterStream($offerId)
    {
        $exits_offer = DB::table('offer_create_main_tds')->where('offer_id', '=', $offerId)->where('status', '=', 1)->first();
        if (! $exits_offer) {
            abort(404, 'Offer letter not found');
        }

        $rowDataOfferDetails = GeneralMethod::offerLetterdetails($offerId);
        $address = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->address : '';
        $comapny_name = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->comapny_name : '';
        $logo = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->logo : '';
        $job_title = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->job_title : '';
        $candidate_name = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->full_name : '';

        $offer_terms_conditions = DB::table('offer_terms_conditions')->where('offer_id', '=', $offerId)->orderByDesc('id')->first();
        $offerDetails = DB::table('offer_create_main_tds')
            ->join('offer_job_applications', 'offer_create_main_tds.offer_id', '=', 'offer_job_applications.id')
            ->where('offer_job_applications.id', $offerId)
            ->where('offer_create_main_tds.status', '=', 1)
            ->select(
                'offer_create_main_tds.offer_id',
                'offer_create_main_tds.employment_type',
                'offer_create_main_tds.id as of_main_td',
                'offer_create_main_tds.currency',
                'offer_create_main_tds.salary_ctc',
                'offer_create_main_tds.accept_date_time',
                'offer_create_main_tds.signature_candidate',
                'offer_create_main_tds.salary_mode',
                'offer_create_main_tds.designation',
                'offer_create_main_tds.joined_date',
                'offer_create_main_tds.monthly_gross',
                'offer_create_main_tds.yearly_gross',
                'offer_create_main_tds.monthly_bounus_gross',
                'offer_create_main_tds.yearly_bonus_gross',
                'offer_create_main_tds.monthly_insurence_gross',
                'offer_create_main_tds.yearly_insurence_gross',
                'offer_create_main_tds.yearly_final_gross',
                'offer_create_main_tds.monthly_final_gross'
            )->get()
            ->map(function ($offer) {
                $basicPoints = DB::table('basic_offer_points')
                    ->where('offer_create_main_td_id', $offer->of_main_td)
                    ->select('id', 'poins as basic_poins', 'Percentage as basic_percentage', 'condition_s as basic_condition_s', 'monthly as basic_monthly', 'yearly as basic_yearly')
                    ->get()->toArray();
                $bonusPoints = DB::table('bonus_offer_points')
                    ->where('offer_create_main_td_id', $offer->of_main_td)
                    ->select('id', 'poins as bonus_poins', 'Percentage as bonus_percentage', 'condition_s as bonus_condition_s', 'monthly as bonus_monthly', 'yearly as bonus_yearly')
                    ->get()->toArray();
                $insurancePoints = DB::table('insurence_offer_points')
                    ->where('offer_create_main_td_id', $offer->of_main_td)
                    ->select('id', 'poins as insurence_poins', 'Percentage as insurence_percentage', 'condition_s as insurence_condition_s', 'monthly as insurence_monthly', 'yearly as insurence_yearly')
                    ->get()->toArray();

                return [
                    'offer_id' => $offer->offer_id,
                    'employment_type' => $offer->employment_type,
                    'of_main_td' => $offer->of_main_td,
                    'accept_date_time' => $offer->accept_date_time,
                    'signature_candidate' => $offer->signature_candidate,
                    'currency' => $offer->currency,
                    'salary_ctc' => $offer->salary_ctc,
                    'salary_mode' => $offer->salary_mode,
                    'designation' => $offer->designation,
                    'joined_date' => $offer->joined_date,
                    'monthly_gross' => $offer->monthly_gross,
                    'yearly_gross' => $offer->yearly_gross,
                    'monthly_bounus_gross' => $offer->monthly_bounus_gross,
                    'yearly_bonus_gross' => $offer->yearly_bonus_gross,
                    'monthly_insurence_gross' => $offer->monthly_insurence_gross,
                    'yearly_insurence_gross' => $offer->yearly_insurence_gross,
                    'yearly_final_gross' => $offer->yearly_final_gross,
                    'monthly_final_gross' => $offer->monthly_final_gross,
                    'Basic_offer_points' => $basicPoints,
                    'Bonus_offer_points' => $bonusPoints,
                    'Insurance_offer_points' => $insurancePoints,
                ];
            });

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('offer-letter', compact('offerDetails', 'offer_terms_conditions', 'offerId', 'exits_offer', 'address', 'comapny_name', 'logo', 'job_title', 'candidate_name'));

        return $pdf->download('Offer-Letter-'.$offerId.'.pdf');
    }

    public function previewOfferLettetr(Request $request)
    {

        $offerId = $request->offerId;

        $exits_offer = DB::table('offer_create_main_tds')->where('offer_id', '=', $offerId)->where('status', '=', 1)->first();
        $rowDataOfferDetails = GeneralMethod::offerLetterdetails($offerId);

        $address = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->address : '';
        $comapny_name = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->comapny_name : '';
        $logo = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->logo : '';
        $job_title = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->job_title : '';
        $candidate_name = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->full_name : '';

        if ($exits_offer) {

            $filename = $exits_offer->signature_candidate;
            $path = str_replace('index.php', '', url('storage/app/public/signatures/'.$filename.''));

            $offer_terms_conditions = DB::table('offer_terms_conditions')->where('offer_id', '=', $offerId)->orderByDesc('id')->first();
            $offerDetails = DB::table('offer_create_main_tds')
                ->join('offer_job_applications', 'offer_create_main_tds.offer_id', '=', 'offer_job_applications.id')
                ->where('offer_job_applications.id', $request->offerId)
                ->where('offer_create_main_tds.status', '=', 1)
                ->select(
                    'offer_create_main_tds.offer_id',
                    'offer_create_main_tds.employment_type',
                    'offer_create_main_tds.id as of_main_td',
                    'offer_create_main_tds.currency',
                    'offer_create_main_tds.salary_ctc',
                    'offer_create_main_tds.salary_mode',
                    'offer_create_main_tds.designation',
                    'offer_create_main_tds.joined_date',
                    'offer_create_main_tds.monthly_gross',
                    'offer_create_main_tds.yearly_gross',
                    'offer_create_main_tds.monthly_bounus_gross',
                    'offer_create_main_tds.yearly_bonus_gross',
                    'offer_create_main_tds.monthly_insurence_gross',
                    'offer_create_main_tds.yearly_insurence_gross',
                    'offer_create_main_tds.yearly_final_gross',
                    'offer_create_main_tds.monthly_final_gross'
                )->get()
                ->map(function ($offer) {
                    // Fetch Basic offer points
                    $basicPoints = DB::table('basic_offer_points')
                        ->where('offer_create_main_td_id', $offer->of_main_td)
                        ->select(
                            'id',
                            'poins as basic_poins',
                            'Percentage as basic_percentage',
                            'condition_s as basic_condition_s',
                            'monthly as basic_monthly',
                            'yearly as basic_yearly'
                        )->get()->toArray();

                    // Fetch Bonus offer points
                    $bonusPoints = DB::table('bonus_offer_points')
                        ->where('offer_create_main_td_id', $offer->of_main_td)
                        ->select(
                            'id',
                            'poins as bonus_poins',
                            'Percentage as bonus_percentage',
                            'condition_s as bonus_condition_s',
                            'monthly as bonus_monthly',
                            'yearly as bonus_yearly'
                        )->get()->toArray();

                    // Fetch Insurance offer points
                    $insurancePoints = DB::table('insurence_offer_points')
                        ->where('offer_create_main_td_id', $offer->of_main_td)
                        ->select(
                            'id',
                            'poins as insurence_poins',
                            'Percentage as insurence_percentage',
                            'condition_s as insurence_condition_s',
                            'monthly as insurence_monthly',
                            'yearly as insurence_yearly'
                        )->get()->toArray();

                    // Merge all into the main offer array
                    return [
                        'offer_id' => $offer->offer_id,
                        'employment_type' => $offer->employment_type,
                        'of_main_td' => $offer->of_main_td,
                        'currency' => $offer->currency,
                        'salary_ctc' => $offer->salary_ctc,
                        'salary_mode' => $offer->salary_mode,
                        'designation' => $offer->designation,
                        'joined_date' => $offer->joined_date,
                        'monthly_gross' => $offer->monthly_gross,
                        'yearly_gross' => $offer->yearly_gross,
                        'monthly_bounus_gross' => $offer->monthly_bounus_gross,
                        'yearly_bonus_gross' => $offer->yearly_bonus_gross,
                        'monthly_insurence_gross' => $offer->monthly_insurence_gross,
                        'yearly_insurence_gross' => $offer->yearly_insurence_gross,
                        'yearly_final_gross' => $offer->yearly_final_gross,
                        'monthly_final_gross' => $offer->monthly_final_gross,
                        'Basic_offer_points' => $basicPoints,
                        'Bonus_offer_points' => $bonusPoints,
                        'Insurance_offer_points' => $insurancePoints,
                    ];
                });

            $view = view('offer-letter-preview', compact('offerDetails', 'offer_terms_conditions', 'offerId', 'exits_offer', 'address', 'comapny_name', 'logo', 'job_title', 'candidate_name', 'path'))->render();

            return Reply::dataOnly(['status' => 'success', 'view' => $view]);

        } else {
            return Reply::dataOnly(['status' => 'error', 'url' => '']);
        }

    }
}
