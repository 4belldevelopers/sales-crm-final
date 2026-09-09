<?php
/*** ########################################################
Controller    	: 	AdminCVTrackerController
Purpose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/

namespace App\Http\Controllers\ProductCompany;

use App\Models\ApplicationStatus;
use App\Helper\Files;
use App\Helper\Reply;
use App\Helper\GeneralMethod;
use App\Http\Requests\InterviewSchedule\StoreRequest;
use App\Http\Requests\StoreJobApplication;
use App\Http\Requests\UpdateJobApplication;
use App\Models\InterviewSchedule;
use App\Models\InterviewScheduleEmployee;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobApplicationAnswer;
use App\Models\JobLocation;
use App\Models\JobQuestion;
use App\Notifications\CandidateScheduleInterview;
use App\Notifications\ScheduleInterview;
use App\Models\Skill;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;
use App\Models\NoticePeriod;
use App\InterviewProcess;
use Illuminate\Support\Facades\DB;
use Mail;
use App\Exports\CvTrackerExport;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;



class ProductCompanyCvTrackerController extends ProductCompanyBaseController
{
	public function __construct()
	{
		parent::__construct();
		$this->pageTitle = __('Candidates');
		$this->pageIcon = 'icon-user';

	}


	/***########################################################
	   Controller    	: 	AdminCVTrackerController
	   Method			:	index
	   Purpose			:	Load all application 
	   Date			:	05-April-2021	
	   ########################################################***/
	public function index()
	{


		$company_id = auth()->user()->company_id;
		// $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
		$chkAdminRole = $this->user->hasRole('admin');

		$this->boardColumns = ApplicationStatus::all();
		$this->candidateStatus = DB::table('master_status')->select('*')->orderBy('master_status.priority', 'ASC')->get();
		$this->locations = JobLocation::all();
		$this->skills = Skill::all();
		$this->clients = Client::all();

		if ($chkAdminRole > 0) {
			$this->jobs = Job::where('jobs.company_id', $this->user->company_id)->get();
		} else {
			$this->jobs = DB::table('jobs')->join('job_assign', 'job_assign.job_id', '=', 'jobs.id')->select('jobs.id', 'jobs.title')->where('jobs.status', 'active')->where('jobs.company_id', $this->user->company_id)->where('job_assign.user_id', $this->user->id)->get();

		}

		$this->users = User::select('users.*')->where('status', '=', 'active')->where('company_id', '=', $this->user->company_id)->get();
		$this->noticeperiod = NoticePeriod::all();


		$this->applicationCounter = DB::table('job_applications');
		$this->applicationCounter = $this->applicationCounter->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id');
		$this->applicationCounter = $this->applicationCounter->join('master_status', function ($join) use ($company_id) {
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
					->where('add_job_wise_condidates.master_status_id', '!=', 8)
					->where('add_job_wise_condidates.master_status_id', '!=', 9);
		});
		$this->applicationCounter = $this->applicationCounter->orWhere(function ($query) use ($company_id) {
			return $query->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id);
		});
		$this->applicationCounter = $this->applicationCounter->count();

		return view('product_company.cvtracker.index', $this->data);

	}

	public function data(Request $request)
	{

		abort_if(!$this->user->cans('view_job_applications'), 403);

		$company_id = auth()->user()->company_id;
		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();
		$chkAdminRole = $this->user->hasRole('admin');
		// $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
		// dd($company_id);
		// 'application_status.status',
		$allCandidate = JobApplication::select('job_applications.*', 'jobs.title', 'job_locations.location', 'clients.name as client_name', 'users.name as posted_by', 'users.image as posted_by_image', 'master_status.name', 'master_status.css_style', 'qualification_master.name as qualification_name', 'state_list.state as state_name', 'companies.logo', 'uc.company_name as uc_company_name');
		$allCandidate = $allCandidate->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id');
		$allCandidate = $allCandidate->join('master_status', function ($join) use ($company_id) {
			$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
		});
		$allCandidate = $allCandidate->join('jobs', function ($join) use ($company_id) {
			$join->on('jobs.id', 'add_job_wise_condidates.job_id')
			->where('add_job_wise_condidates.company_id', '=', $company_id);
		});
		$allCandidate = $allCandidate->join('companies', function ($join) use ($company_id) {
			$join->on('companies.id', 'add_job_wise_condidates.company_id')
			->where('add_job_wise_condidates.company_id', '=', $company_id);
		});

		// $allCandidate 		=	$allCandidate->join('jobs', 'jobs.id', 'job_applications.job_id');
		// $allCandidate 		=	$allCandidate->join('companies', 'companies.id', 'job_applications.company_id');
		$allCandidate = $allCandidate->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id');
		// $allCandidate		= 	$allCandidate->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id');
		$allCandidate = $allCandidate->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id');
		$allCandidate = $allCandidate->leftjoin('clients', 'clients.id', 'jobs.client_id');
		$allCandidate = $allCandidate->leftjoin('users', 'users.id', 'add_job_wise_condidates.submitted_by');
		$allCandidate = $allCandidate->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id');
		$allCandidate = $allCandidate->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification');
		$allCandidate = $allCandidate->leftjoin('application_status', 'application_status.id', 'add_job_wise_condidates.status_id');
		$allCandidate = $allCandidate->leftjoin('state_list', 'state_list.id', 'job_applications.state');


		if ($chkAdminRole == 0) {
			$allCandidate = $allCandidate->join('job_assign', 'job_assign.job_id', 'jobs.id');
			$allCandidate = $allCandidate->where('job_assign.user_id', $this->user->id);
		}
		$allCandidate = $allCandidate->where('add_job_wise_condidates.thirdparty_company_id', '=', 0);
		$allCandidate 			=	$allCandidate->orWhere(function ($query) use ($company_id) {
									return $query->where('add_job_wise_condidates.thirdparty_company_id', '>', 0)
											->where('add_job_wise_condidates.company_id', '=', $company_id)
											->where('add_job_wise_condidates.master_status_id', '!=', 8)
											->where('add_job_wise_condidates.master_status_id', '!=', 9);
									});
		if (\request('search') != "") {
			$search = request('search');
			$allCandidate = $allCandidate->where('job_applications.full_name', 'LIKE', "%$search%");
		}
		$allCandidate = $allCandidate->groupBy('add_job_wise_condidates.id');
		$allCandidate = $allCandidate->orderBy('add_job_wise_condidates.id', 'DESC');

		// dd($allCandidate );

		return DataTables::of($allCandidate)

			->editColumn('chkbox', function ($row) {
				return '<div class="form-check form-check-sm form-check-custom form-check-solid">
									<input class="form-check-input chk_list_item " name="chk_list_item[]" type="checkbox" value="' . $row->id . '" />
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

				$duplicate = $row->duplicate ?? 0;
				$flag = 0;
				if ($duplicate == 1) {
					$flag = $duplicate;
				}

				$profile_sidebar_css = 'show-application-detail';

				if ($flag == 1) {
					return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
											<a href="javascript:void(0);"  data-row-id="' . @$row->id . '" class=" ' . $profile_sidebar_css . ' btn btn-icon btn-light-danger pulse pulse-danger mr-5" data-toggle="tooltip" title="Duplicate">
											<span class="fs-3">' . strtoupper(@$strVal) . '</span>
											<span class="pulse-ring"></span>
											</a>
											</div>                  
											<div class="d-flex flex-column">
											<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-danger fw-bolder text-hover-primary fs-6  ' . $profile_sidebar_css . ' " data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">' . ucwords(substr(@$row->full_name, 0, 7)) . '</a>
											<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-danger fw-bold d-block fs-7 text-hover-primary ' . $profile_sidebar_css . ' " data-toggle="tooltip" title="' . ucwords(@$row->current_designation) . '">' . ucwords(substr(@$row->current_designation, 0, 7)) . '</a>
											</div>';
				} else {
					return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
											<a href="javascript:void(0);"   data-row-id="' . @$row->id . '" class="' . $profile_sidebar_css . '">
											<div class="symbol-label ' . @$bgTextDecore . ' border-radius-5">' . strtoupper(@$strVal) . '</div>
											</a>
											</div>                  
											<div class="d-flex flex-column">
											<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-dark fw-bolder text-hover-primary fs-6 ' . $profile_sidebar_css . ' " data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">' . ucwords(substr(@$row->full_name, 0, 10)) . '</a>
											<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-muted fw-bold d-block fs-7 text-hover-primary ' . $profile_sidebar_css . ' " data-toggle="tooltip" title="' . ucwords(@$row->current_designation) . '">' . ucwords(substr(@$row->current_designation, 0, 10)) . '</a>
											</div>';
				}
			})
			->editColumn('Contacts', function ($row) {

				$duplicate = $row->duplicate ?? 0;
				$flag = 0;
				if ($duplicate == 1) {
					$flag = $duplicate;
				}

				$bench_count = DB::table('rec_candidate_assign');
				$bench_count = $bench_count->select('rec_candidate_assign.id');
				$bench_count = $bench_count->join('bench_applications', 'bench_applications.id', '=', 'rec_candidate_assign.source_from_id');
				$bench_count = $bench_count->where('rec_candidate_assign.source_application_id', '=', $row->id);
				$bench_count = $bench_count->where('rec_candidate_assign.source_jobid', '=', $row->job_id);
				$bench_count = $bench_count->where('rec_candidate_assign.source_from_type', '=', 2);
				$bench_count = $bench_count->count();

				$bench_cnt_html = '';
				if ($bench_count > 0) {
					$bench_row = DB::table('rec_candidate_assign');
					$bench_row = $bench_row->select('rec_candidate_assign.id', 'bench_applications.id as bench_applications_id');
					$bench_row = $bench_row->join('bench_applications', 'bench_applications.id', '=', 'rec_candidate_assign.source_from_id');
					$bench_row = $bench_row->where('rec_candidate_assign.source_application_id', '=', $row->id);
					$bench_row = $bench_row->where('rec_candidate_assign.source_jobid', '=', $row->job_id);
					$bench_row = $bench_row->where('rec_candidate_assign.source_from_type', '=', 2);
					$bench_row = $bench_row->first();

					$bench_cnt = DB::table('bench_assign')->select('users.id')->join('users', 'users.id', '=', 'bench_assign.user_id')->where('bench_assign.bench_applications_id', '=', $bench_row->bench_applications_id)->count();

					$bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile')->join('users', 'users.id', '=', 'bench_assign.user_id')->where('bench_assign.bench_applications_id', '=', $bench_row->bench_applications_id)->first();

					$row->phone = @$bench_assign_row->mobile;
					if (empty($row->phone)) {
						$row->phone = 'xxx-xxx-xxxx';
					}
					$row->email = @$bench_assign_row->email;
					if (empty($row->email)) {
						$row->email = 'anonymous@email.com';
					}
					if ($bench_cnt > 1) {
						$bench_cnt_html = '<div data-row-id="' . $bench_row->bench_applications_id . '" class="symbol symbol-30 symbol-circle symbol-light bench_assign_listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list.."> 
									<span class="symbol-label font-weight-bold">' . $bench_cnt . '+</span> 
								</div>';
					}


				}

				if (@$flag == 1) {
					return '<div class="d-flex flex-column">
											<a href="javascript:void(0);" class="text-danger fw-bolder text-hover-primary fs-6">' . @$row->phone . '</a>
											<a href="javascript:void(0);" class="text-danger fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="' . @$row->email . '">' . substr(@$row->email, 0, 10) . $bench_cnt_html . '</a>
											</div>';
				} else {
					return '<div class="d-flex flex-column">
											<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . @$row->phone . '</a>
											<a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary" data-toggle="tooltip" title="' . @$row->email . '">' . substr(@$row->email, 0, 10) . $bench_cnt_html . '</a>
											</div>';
				}

			})
			->editColumn('Exp', function ($row) {
				return '<div class="d-flex flex-column">
									<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$row->total_exp . '+</a>
									<span class="text-muted fw-bold d-block fs-7">' . @$row->rel_exp . '+</span>
									</div>';
			})
			->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {

				$current_ctc_cur = $current_ectc_cur = $ctc = $ectc = $current_ctc_mode = $current_ectc_mode = "";
				$current_ctc_cur = $currencySymbole[@$row->current_ctc_cur];
				$current_ectc_cur = $currencySymbole[@$row->current_ectc_cur];
				$ctc = GeneralMethod::thousandsCurrencyFormat($row->ctc);
				$ectc = GeneralMethod::thousandsCurrencyFormat($row->ectc);

				return '<div class="d-flex flex-column">
										<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$current_ctc_cur . @$ctc . '/' . $current_ctc_mode . '</a>
										<span class="text-muted fw-bold d-block fs-7">' . $current_ectc_cur . @$ectc . '/' . $current_ectc_mode . '</span>
								  </div>';
			})

			->editColumn('NoticePeriod', function ($row) {

				$np = $row->np_id;
				if (empty($np)) {
					$np = 'N/A';
				}
				if ($row->serving_notice_period_mode == 1) {
					$npDate = $row->np_id;
					$originalDate = $row->np_id;
					$np = 'SNP LWD ' . date("d-M-y", strtotime($originalDate));
				} else if ($row->serving_notice_period_mode == 0) {
					if (!empty($row->np_id)) {
						$np = $row->np_id . '  Days';
					}
				} else {
					$np = $row->np_id;
				}

				return '<div class="d-flex flex-column">
									<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$np . '</a>
									<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' . ucwords(@$row->current_company) . '">' . ucwords(substr(@$row->current_company, 0, 7)) . '</span>
								  </div>';
			})
			->editColumn('Qualifaction', function ($row) {
				return ' <div class="d-flex flex-column">
									<a href="javascript:void(0);" class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="' . @$row->qualification_name . '">' . ucwords(substr(@$row->qualification_name, 0, 10)) . '</a>
									<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' . strtoupper(@$row->skills) . '">' . strtoupper(substr(@$row->skills, 0, 15)) . '</span> 
								  </div>';
			})
			->editColumn('Location', function ($row) {

				$country = $row_country_name = $state = $row_state_name = $city = $row_city_name = '';
				if (empty($row->country)) {
					$row_country_name = '----';
				} else {
					if (!empty(@$row->country)) {
						if (is_numeric(@$row->country)) {
							$country_name_Arr = GeneralMethod::getCountryName(@$row->country);
							foreach ($country_name_Arr as $cval) {
								$row_country_name = ucwords($cval['name']);
							}
						}
					}
				}

				if (empty($row->state)) {
					$row_state_name = '----';
				} else {

					if (@$row->country == 101) {
						$row_state_name = @$row->state_name;
					} else {
						$state = @$row->state;
						if (is_numeric(@$row->country) && is_numeric($state)) {
							if ($state > 0) {
								$state_name_Arr = GeneralMethod::getStateName(@$row->country, $state);
								foreach ($state_name_Arr as $sval) {
									$row_state_name = ucwords($sval['name']);
								}
							}
						}
					}
				}

				if (@$row->country == 101) {
					$row_city = DB::table('city_list')->select('*')->where('city_id', @$row->city_id)->first();
					$row_city_name = @$row_city->city;
				} else {
					$row_city_name = @$row->city;
				}

				return '<div class="d-flex flex-column">
											<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' . ucwords($row_city_name) . '">' . ucwords(substr($row_city_name, 0, 10)) . '</a>
											<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' . ucwords($row_state_name) . ' | ' . strtoupper($row_country_name) . '">' . ucfirst(substr($row_state_name, 0, 5)) . ' | ' . strtoupper(substr($row_country_name, 0, 2)) . '</span>
										</div>';


			})
			->editColumn('Source', function ($row) {

				$posted_by_image = '';
				$posted_by_image = url('public/avatar.png');
				if (!empty($row->posted_by_image)) {
					$posted_by_image = url('public/user-uploads/team_profile_img/' . @$row->posted_by_image);
				}
				return '<div class="d-flex flex-column">
									<div class="symbol-group symbol-hover">
									  <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="' . @$row->posted_by . '">
										<img alt="Pic" src="' . @$posted_by_image . '">
									  </div>
								  </div>
								  <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="' . @$row->uc_company_name . '">' . substr(@$row->uc_company_name, 0, 10) . '</span>
								  ';
			})
			->editColumn('UploadDate', function ($row) {

				$created_at = '';
				$created_at = $newDate = date("d M , Y", strtotime($row->created_at));
				return '<div class="d-flex flex-column">
										<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . @$created_at . '</a> 
									</div>';
			})

			->editColumn('Requisitions', function ($row) {

				$job_title = $client_name = '';
				$job_title = $row->title;
				$job_title = ucwords(substr($job_title, 0, 15));
				$client_name = $row->client_name;
				$client_name = ucwords(substr($client_name, 0, 15));

				return '<div class="d-flex flex-column">
									<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="" data-bs-original-title="' . $row->title . '">' . $job_title . '</a>
									<span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="' . $row->client_name . '">' . $client_name . '</span>
								  </div>';
			})
			->editColumn('Stage', function ($row) {

				$stage_default_css = 'stage_sub_stage';
				$name = '--------';
				if ($row->master_status_id == 8) {
					$name = '';
				}
				if (!empty(@$row->child_status_id) && @$row->child_status_id > 0) {
					$childStatus = '';
					$childStatus = DB::table('candidate_status')->select('*')->where('id', $row->child_status_id)->first();
					if (!empty($childStatus)) {
						$name = @$childStatus->name;
					}
				}
				return '<div class="d-flex flex-column">
										<a href="javascript.void(0);" data-row-id="' . @$row->id . '" class="d-flex flex-column ' . $stage_default_css . '" data-toggle="modal" data-target="#' . $stage_default_css . '">
											<span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1">' . ucwords(substr($row->name, 0, 10)) . '</span>
											<span class="text-muted fw-bold d-block fs-7">' . @$name . '</span>
										</a>
									</div>';
			})

			->rawColumns(['chkbox', 'Name', 'Contacts', 'Exp', 'Salary', 'NoticePeriod', 'Qualifaction', 'Location', 'Source', 'UploadDate', 'Requisitions', 'Stage'])
			->addIndexColumn()
			->make(true);
	}

	public function application_copy(Request $request)
	{
		$arr = '';
		$arr = $request->arr;
		$this->applicationDataCount = DB::table('job_applications')->select('*')->whereIn('id', $arr)->count();
		$this->applicationData = DB::table('job_applications')->select('job_applications.id')->whereIn('id', $arr)->get();

		$this->job = Job::select('jobs.id', 'jobs.title', 'jobs.total_positions', 'jobs.priority', 'jobs.status', 'jobs.min_exp', 'jobs.max_exp', 'jobs.created_at', 'jobs.start_date', 'jobs.end_date', 'jobs.min_salary', 'jobs.max_salary', 'jobs.min_sal_mode', 'jobs.max_sal_mode', 'jobs.min_salary_currency', 'jobs.max_salary_currency', 'clients.name as clients_name', 'clients.logo_logical_path');
		$this->job = $this->job->join('clients', 'clients.id', 'jobs.client_id');
		$this->job = $this->job->leftJoin('add_job_wise_condidates as job_applications', 'job_applications.job_id', '=', 'jobs.id');
		$this->job = $this->job->where('jobs.id', '>', '0');
		$this->job = $this->job->groupBy('jobs.id');
		$this->job = $this->job->orderBy('jobs.id', 'DESC');
		$this->job = $this->job->get();

		$view = view('product_company.cvtracker.application_copy', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'view' => $view]);

	}

	public function copy_current_selected_applicant(Request $request)
	{

		$selectedJobListing = $selectedApplicantListing = '';
		$selectedJobListing = $request->selectedJobListing;
		$selectedApplicantListing = $request->selectedApplicantListing;

		if (!empty($selectedJobListing)) {
			foreach ($selectedJobListing as $jval) {

				if (!empty($selectedApplicantListing)) {
					foreach ($selectedApplicantListing as $app_id) {
						$applicationRow = DB::table('job_applications')->select('*')->where('id', $app_id)->first();

						$default_master_status = 8;
						if ($applicationRow->thirdparty_company_id > 0) {    		// Application upload by staffing partner.
							$default_master_status = 8;
						} else {
							$default_master_status = 8;
						}

						$values = array(
							'submitted_by' => $this->user->id,
							'company_id' => auth()->user()->company_id,
							'thirdparty_company_id' => 0,

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
							'any_offer' => @$applicationRow->any_offer,
							'document' => @$applicationRow->document,

							'sub_for_approval' => @$applicationRow->sub_for_approval,
							'offer_approved' => @$applicationRow->offer_approved,
							'offer_release' => @$applicationRow->offer_release,
							'offer_acceptance' => @$applicationRow->offer_acceptance,
							'joined' => @$applicationRow->joined,

							'joined_date' => @$applicationRow->joined_date,
							'duplicate' => @$applicationRow->duplicate,
							'percentage_hike' => @$applicationRow->percentage_hike,
							'comment' => @$applicationRow->comment,
							'pincode' => @$applicationRow->pincode,

							'offer_currency' => @$applicationRow->offer_currency,
							'offer_mode' => @$applicationRow->offer_mode,
							'offer_amount' => @$applicationRow->offer_amount
						);
						// DB::table('job_applications')->insert($values);
						$insertArr = array('job_id' => @$jval, 'job_application_id' =>@$app_id, 'status_id' => @$default_master_status, 'master_status_id' => @$default_master_status, 'child_status_id'=>@$applicationRow->child_status_id, 'company_id' => auth()->user()->company_id, 'submitted_by' => @$this->user->id,'thirdparty_company_id'=>0,'existing'=>1);

			            DB::table('add_job_wise_condidates')->insert($insertArr);
					}
				}
			}
		}
		return Reply::dataOnly(['status' => 'success']);
	}

	public function fwd_resume(Request $request)
	{


		$arr = '';
		$this->arr = $request->arr;
		$this->applicationDataCount = DB::table('job_applications')->select('*')->whereIn('id', $this->arr)->count();

		$this->applicationData = DB::table('job_applications')->select(
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
		);
		$this->applicationData = $this->applicationData->join('jobs', 'jobs.id', 'job_applications.job_id');
		$this->applicationData = $this->applicationData->leftjoin('clients', 'clients.id', 'jobs.client_id');
		$this->applicationData = $this->applicationData->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification');
		$this->applicationData = $this->applicationData->whereIn('job_applications.id', $this->arr);
		$this->applicationData = $this->applicationData->get();

		$this->templateData = DB::table('th_email_template')->select('*')->where('th_email_template.status', 1)->get();

		$this->userdata = $this->user;
		$this->to_email_from_count = $this->cc_email_count = 0;

		$this->to_email_from_data = $this->cc_email_data = array();
		$commonFromArr = $commonCcArr = array();

		if ($request->id > 1) {

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
			$assignCount = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.email')->where('job_assign.job_id', $request->id)->count();
			$assignData = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.email')->where('job_assign.job_id', $request->id)->get();
			if ($assignCount > 0) {
				$i = 0;
				foreach ($assignData as $aval) {
					$commonCcArr[$i] = $aval->email;
					$i++;
				}
			}
			$this->cc_email_count = $assignCount;
			$this->cc_email_data = $commonCcArr;


		}

		$view = view('product_company.cvtracker.fwd_resume', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'view' => $view]);

	}

	public function email_template(Request $request)
	{

		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();
		$selectedApplicantListing = $applicationDataCount = $applicationData = $template = $tracker_html = '';

		$selectedApplicantListing = $request->selectedApplicantListing;
		$applicationDataCount = DB::table('job_applications')->select('*')->whereIn('id', $selectedApplicantListing)->count();
		$applicationData = DB::table('job_applications');
		$applicationData = $applicationData->select(
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
		);
		$applicationData = $applicationData->join('jobs', 'jobs.id', 'job_applications.job_id');
		$applicationData = $applicationData->leftjoin('clients', 'clients.id', 'jobs.client_id');
		$applicationData = $applicationData->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification');
		$applicationData = $applicationData->whereIn('job_applications.id', $selectedApplicantListing);
		$applicationData = $applicationData->get();

		$template = $request->template;
		$templateData = DB::table('th_email_template')->select('*')->where('th_email_template.status', 1)->where('th_email_template.e_temp_id', '=', $template)->first();

		$this->job = Job::select('jobs.id', 'jobs.title', 'jobs.total_positions', 'jobs.priority', 'jobs.status', 'jobs.min_exp', 'jobs.max_exp', 'jobs.created_at', 'jobs.start_date', 'jobs.end_date', 'jobs.min_salary', 'jobs.max_salary', 'jobs.min_sal_mode', 'jobs.max_sal_mode', 'jobs.min_salary_currency', 'jobs.max_salary_currency', 'clients.name as clients_name', 'clients.logo_logical_path');
		$this->job = $this->job->join('clients', 'clients.id', 'jobs.client_id');
		$this->job = $this->job->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id');
		$this->job = $this->job->where('jobs.id', '>', '0');
		$this->job = $this->job->groupBy('jobs.id');
		$this->job = $this->job->orderBy('jobs.id', 'DESC');
		$this->job = $this->job->get();

		$tracker_html = '<table class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer dtr-inline" id="myTable" role="grid" aria-describedby="myTable_info" style="width: 1272px;">
            <thead>
              <tr class="fw-bolder text-muted" role="row">
					<th class="sorting_disabled align-items-center" rowspan="1" colspan="1" style="width: 151.5px;">S.No.</th>
					<th class="sorting_disabled align-items-center" rowspan="1" colspan="1" style="width: 151.5px;">Name</th>
					<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 112.5px;">Contacts</th>
					<th class="sorting_disabled"  rowspan="1" colspan="1" style="width: 40.5px;">Exp.</th>
					<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 58.5px;">Salary</th>
					<th class="sorting_disabled" data-toggle="tooltip" title="Notice Period" rowspan="1" colspan="1" style="width: 62.5px;">NP</th>
					<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 109.5px;">Qualifaction</th>
					<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 108.5px;">Requisitions</th>
            </thead>';

		if ($applicationDataCount > 0) {
			$cnt = 1;
			foreach ($applicationData as $appData) {

				$bgTextDecore = $strVal = $current_ctc_cur = $current_ectc_cur = $ctc = $ectc = "";
				$str = trim(@$appData->full_name);
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

				$current_ctc_cur = $currencySymbole[@$appData->current_ctc_cur];
				$current_ectc_cur = $currencySymbole[@$appData->current_ectc_cur];
				$ctc = GeneralMethod::thousandsCurrencyFormat($appData->ctc);
				$ectc = GeneralMethod::thousandsCurrencyFormat($appData->ectc);
				$np = $appData->np_id;
				if (empty($np)) {
					$np = 'N/A';
				}
				if ($appData->serving_notice_period_mode == 0) {
					if (!empty($appData->np_id)) {
						$np = $appData->np_id . '  Days';
					}
				} else {
					$np = $appData->np_id;
				}

				$tracker_html .= '<tr role="row" class="odd">
<td tabindex="0">
		<div class="form-check form-check-sm form-check-custom form-check-solid">' . $cnt . '.</div>
</td>
<td class=" align-items-center">
		<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> 
			<a href="javascript:void(0);" data-row-id="1418" class="show-application-detail">
				<div class="symbol-label fs-3 bg-light-danger text-danger border-radius-5">' . strtoupper(@$strVal) . '</div>
			</a> 
		</div>
		<div class="d-flex flex-column"> 
		<a href="javascript:void(0);" data-row-id="1418" class="text-dark fw-bolder text-hover-primary fs-6">' . ucwords(@$appData->full_name) . '</a><br>  
		<a href="javascript:void(0);" data-row-id="1418" class="text-muted fw-bold d-block fs-7 text-hover-primary" >' . ucwords(@$appData->current_designation) . '</a> </div>
</td>
<td>
		<div class="d-flex flex-column"> 
			<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . @$appData->phone . '</a><br>
			<a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">' . @$appData->email . '</a> 
		</div>
</td>
<td>
		<div class="d-flex flex-column"> 
			<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$appData->total_exp . '+</a><br>  
			<span class="text-muted fw-bold d-block fs-7">' . @$appData->rel_exp . '+</span> 
		</div>
</td>
<td>
  		<div class="d-flex flex-column"> 
			<a href="javascript:void(0);" class="text-dark fw-bolder fs-6"><i class="fa fa-inr" aria-hidden="true"></i>' . @$current_ctc_cur . @$ctc . '</a><br> 
			<span class="text-muted fw-bold d-block fs-7"><i class="fa fa-inr" aria-hidden="true"></i>' . $current_ectc_cur . @$ectc . '</span> 
		</div>
</td>
<td>
  		<div class="d-flex flex-column"> 
			<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . $np . '</a><br> 
			<span class="text-muted fw-bold d-block fs-7">' . @$appData->current_company . '</span>  
		</div>
</td>
<td>
  		<div class="d-flex flex-column"> 
			<a href="javascript:void(0);" class="text-dark fw-bolder fs-6" >' . ucwords(substr(@$appData->qualification_name, 0, 10)) . '</a><br> 
			<span class="text-muted fw-bold d-block fs-7">' . strtoupper(substr(@$appData->skills, 0, 15)) . '</span> 
		</div>
	</td>
<td>
  		<div class="d-flex flex-column"> 
			<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" >' . @$appData->job_title . '</a><br> 
			<span class="text-muted fw-bold text-muted d-block fs-7" >' . @$appData->client_name . '</span> 
		</div>
	</td>
											</tr>';
				$cnt = $cnt + 1;
			}
		}

		$tracker_html .= '<tbody class="text-gray-600 fw-bold">';
		$tracker_html .= '</tbody></table>';
		$template_description = $templateData->template_description;
		$template_description = str_replace("[{EMAIL_TRACKER}]", $tracker_html, $template_description);
		return Reply::dataOnly(['status' => 'success', 'template_description' => $template_description]);

	}

	public function fwd_resume_send(Request $request)
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

		$from = json_decode($from, true);
		$from = array_column($from, 'value');
		$from = implode(',', $from);

		$to = json_decode($to, true);
		$to = array_column($to, 'value');
		$to = implode(',', $to);

		if (!empty($cc)) {
			$cc = json_decode($cc, true);
			$cc = array_column($cc, 'value');
			$cc = implode(',', $cc);
		}

		if ($attached_cv == 1) {

			$resumeRowCount = DB::table('documents')->select('*')->whereIn('documents.documentable_id', $arr)->count();
			$resumeRow = DB::table('documents')->select('*')->whereIn('documents.documentable_id', $arr)->get();
			$filename = array();
			$raw_filename = array();
			if ($resumeRowCount) {
				foreach ($resumeRow as $rval) {
					$hashname = $documentable_id = '';
					$hashname = $rval->hashname;
					$documentable_id = $rval->documentable_id;

					$raw_filename[] = $hashname;
					$filename[] = base_path() . '/public/user-uploads/documents/' . $documentable_id . '/' . $hashname;
				}
			}

			$path = base_path() . '/public/user-uploads/documents/';
			$mailto = $to;
			$from_mail = $from;
			$from_name = "testing";
			$replyto = $from;
			$bcc = $cc;

			$this->status = GeneralMethod::mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $bcc, $subject, $message);

		} else {
			$this->status = GeneralMethod::sentEmailUsingSMTP($to, $cc, $subject, $message, $from);
		}


		$view = view('product_company.cvtracker.fwd_resume_message', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'view' => $view]);

	}



}
