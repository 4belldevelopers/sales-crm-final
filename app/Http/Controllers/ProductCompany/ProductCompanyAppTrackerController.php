<?php

/***#######################################################
Controller    	: 	AdminAppTrackerController
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
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\NoticePeriod;
use App\Models\JobSkill;
use Mail;
use App\Exports\AppTrackerExport;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class ProductCompanyAppTrackerController extends ProductCompanyBaseController
{

	public function __construct()
	{
		parent::__construct();
		$this->pageTitle = __('Applied Candidates');
		$this->pageIcon = 'icon-user';
	}
	public function index($id, $mstatus_id)
	{

		$this->skillData = array();
		$this->statusArr = array();
		$this->candStatusClass = array();
		if ($id > 0) {

			$this->boardColumns = ApplicationStatus::all();
			$this->locations = JobLocation::all();
			$this->skills = Skill::all();
			$this->clients = Client::all();
			$this->noticeperiod = NoticePeriod::all();
			$this->job = Job::findOrFail($id);

			if ($mstatus_id != 'all') {
				$this->statusArr = ApplicationStatus::findOrFail($mstatus_id);
				$this->candStatusClass = GeneralMethod::candidateStatusColorCode($this->statusArr->name);
			} else {
				$this->candStatusClass = GeneralMethod::candidateStatusColorCode('all');
			}

			if ($this->job->client_id > 0) {
				$this->jobclient = Client::findOrFail($this->job->client_id);
				$contactHtml = '';
				$this->client_loc_arr = DB::table('client_location_jobs')->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->join('countries', 'countries.id', '=', 'client_location.country_id')->select('client_location.address', 'client_location.city', 'client_location.postcode', 'countries.country_name')->where('client_location_jobs.job_id', $id)->get();
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
			$this->noticeperiod = NoticePeriod::all();
		}
		$this->custome_count = $this->custome_count($id);
		session()->put('job_id', $id);


		return view('product_company.apptracker.index', $this->data);
	}
	public function loadcounter(Request $request)
	{
		$company_id  = auth()->user()->company_id;
		$jobid       = (int) $request->jobid;
		$inputSearch = $request->inputSearch;

		// Filtered search bypasses cache — counts must reflect the search term live.
		if (!empty($inputSearch)) {
			$data       = $this->runCounterMySQL($jobid, $inputSearch);
			$allCounter = $data ? 1 : 0;
			return Reply::dataOnly(['status' => 'success', 'data' => $data, 'allCounter' => $allCounter]);
		}

		// Stale-while-revalidate: serve cached value up to 120s; refresh in background after 30s.
		// Uses the same pipeline:{jobId} key as ServiceCompany so counters:reconcile (Step 9) feeds both.
		$cacheKey = "pipeline:{$jobid}";
		$payload  = Cache::flexible($cacheKey, [30, 120], function () use ($jobid, $company_id) {
			try {
				return Cache::lock("lock:pipeline:{$jobid}", 5)->block(6, function () use ($jobid, $company_id) {
					// Prefer pipeline_counters DB snapshot (written by counters:reconcile).
					$row = DB::table('pipeline_counters')
						->where('job_id', $jobid)
						->where('company_id', $company_id)
						->whereNotNull('computed_at')
						->first();
					if ($row) {
						return [
							'allCandidate' => (int) $row->all_candidate,
							'Sourcing'     => (int) $row->sourcing,
							'Screening'    => (int) $row->screening,
							'Interview'    => (int) $row->interview,
							'Negotiation'  => (int) $row->negotiation,
							'Offered'      => (int) $row->offered,
							'Joined'       => (int) $row->joined,
							'Rejected'     => (int) $row->rejected,
						];
					}
					// Cold start: fall back to MySQL aggregation.
					return $this->runCounterMySQL($jobid, null);
				});
			} catch (\Throwable) {
				return $this->runCounterMySQL($jobid, null);
			}
		});

		$allCounter = $payload ? 1 : 0;
		return Reply::dataOnly(['status' => 'success', 'data' => $payload, 'allCounter' => $allCounter]);
	}

	/**
	 * MySQL aggregation query for per-stage counts on a job.
	 * Used on cold-start or for live filtered requests.
	 */
	private function runCounterMySQL(int $jobid, ?string $inputSearch): ?object
	{
		return DB::table('job_applications')
			->select(
				DB::raw('count(add_job_wise_condidates.id) as allCandidate'),
				DB::raw('SUM(CASE WHEN add_job_wise_condidates.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing'),
				DB::raw('SUM(CASE WHEN add_job_wise_condidates.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening'),
				DB::raw('SUM(CASE WHEN add_job_wise_condidates.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview'),
				DB::raw('SUM(CASE WHEN add_job_wise_condidates.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation'),
				DB::raw('SUM(CASE WHEN add_job_wise_condidates.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered'),
				DB::raw('SUM(CASE WHEN add_job_wise_condidates.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined'),
				DB::raw('SUM(CASE WHEN add_job_wise_condidates.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected')
			)
			->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
			->join('master_status', 'master_status.id', 'add_job_wise_condidates.master_status_id')
			->where(function ($q) use ($jobid, $inputSearch) {
				$q->where('add_job_wise_condidates.thirdparty_company_id', '=', 0)
				  ->where('add_job_wise_condidates.job_id', '=', $jobid);
				if (!empty($inputSearch)) {
					$q->where('job_applications.full_name', 'LIKE', "%{$inputSearch}%");
				}
			})
			->orWhere(function ($q) use ($jobid, $inputSearch) {
				$q->where('add_job_wise_condidates.thirdparty_company_id', '>', 0)
				  ->where('add_job_wise_condidates.job_id', '=', $jobid)
				  ->whereNotIn('add_job_wise_condidates.master_status_id', [8, 9]);
				if (!empty($inputSearch)) {
					$q->where('job_applications.full_name', 'LIKE', "%{$inputSearch}%");
				}
			})
			->groupBy('add_job_wise_condidates.job_id')
			->first();
	}
	public function data_old(Request $request)
	{

		$mstatus_id = $job_id = $search = $feedback_status = '';

		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();
		$company_id = auth()->user()->company_id;
		$mstatus_id = $request->mstatus_id;
		$interviewStatus = array();
		$interviewStatus = DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();

		$job_id = $request->jobid;
		$search = $request->search;

		$chkAdminRole = $this->user->hasRole('admin');
		// $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
		$feedback_status = GeneralMethod::feedback_status();
		// 'application_status.status', 
		$jobApplications = JobApplication::select(
			'job_applications.*',
			'jobs.title',
			'job_locations.location',
			'clients.name as client_name',
			'users.name as posted_by',
			'users.image as posted_by_image',
			'qualification_master.name as qualification_name',
			'master_status.name',
			'master_status.css_style',
			'state_list.state as state_name',
			'city_list.city as indian_city_name',
			'bench_applications.id as bench_applications_id',
			'uc.company_name as uc_company_name'
		)
			->join('jobs', 'jobs.id', 'job_applications.job_id')
			->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
			->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')
			->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
			->leftjoin('clients', 'clients.id', 'jobs.client_id')
			->leftjoin('users', 'users.id', 'job_applications.submitted_by')
			->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id')
			->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
			->leftjoin('application_status', 'application_status.id', 'job_applications.status_id')
			->leftjoin('state_list', 'state_list.id', 'job_applications.state')
			->leftjoin('city_list', 'city_list.city_id', 'job_applications.city_id')
			->leftJoin('rec_candidate_assign', function ($join) use ($job_id, $company_id) {
				$join->on('rec_candidate_assign.source_application_id', '=', 'job_applications.id')
					->where('rec_candidate_assign.source_jobid', '=', $job_id)
					->whereIn('rec_candidate_assign.source_from_type', [1, 2, 3])
					->where('rec_candidate_assign.source_from_company_id', '!=', $company_id);
			})
			->leftjoin('bench_applications', 'bench_applications.id', 'rec_candidate_assign.source_from_id');

		if ($mstatus_id > 0) {

			$jobApplications = $jobApplications->where(function ($query) use ($company_id, $job_id, $search, $mstatus_id) {
				if (!empty($search)) {
					return $query->where('job_applications.thirdparty_company_id', '=', 0)->where('job_applications.company_id', '=', $company_id)->where('job_applications.master_status_id', '=', $mstatus_id)->where('job_applications.job_id', '=', $job_id)->where('job_applications.full_name', 'LIKE', "%$search%");
				} else {
					return $query->where('job_applications.thirdparty_company_id', '=', 0)->where('job_applications.master_status_id', '=', $mstatus_id)->where('job_applications.company_id', '=', $company_id)->where('job_applications.job_id', '=', $job_id);
				}
			});
			$jobApplications = $jobApplications->orWhere(function ($query) use ($company_id, $job_id, $search, $mstatus_id) {
				if (!empty($search)) {
					return $query->where('job_applications.thirdparty_company_id', '>', 0)->where('job_applications.company_id', '=', $company_id)->where('job_applications.job_id', '=', $job_id)
						->where('job_applications.master_status_id', '=', $mstatus_id)->where('job_applications.master_status_id', '!=', 8)->where('job_applications.master_status_id', '!=', 9)->where('job_applications.full_name', 'LIKE', "%$search%");
				} else {
					return $query->where('job_applications.thirdparty_company_id', '>', 0)->where('job_applications.company_id', '=', $company_id)->where('job_applications.job_id', '=', $job_id)
						->where('job_applications.master_status_id', '=', $mstatus_id)->where('job_applications.master_status_id', '!=', 8)->where('job_applications.master_status_id', '!=', 9);
				}
			});


		} else {

			$jobApplications = $jobApplications->where(function ($query) use ($company_id, $job_id, $search, $mstatus_id) {
				if (!empty($search)) {
					return $query->where('job_applications.thirdparty_company_id', '=', 0)->where('job_applications.company_id', '=', $company_id)->where('job_applications.job_id', '=', $job_id)
						->where('job_applications.full_name', 'LIKE', "%$search%");
				} else {
					return $query->where('job_applications.thirdparty_company_id', '=', 0)->where('job_applications.company_id', '=', $company_id)->where('job_applications.job_id', '=', $job_id);
				}
			});
			$jobApplications = $jobApplications->orWhere(function ($query) use ($company_id, $job_id, $search, $mstatus_id) {
				if (!empty($search)) {
					return $query->where('job_applications.thirdparty_company_id', '>', 0)->where('job_applications.company_id', '=', $company_id)->where('job_applications.job_id', '=', $job_id)->where('job_applications.master_status_id', '!=', 8)->where('job_applications.master_status_id', '!=', 9)->where('job_applications.full_name', 'LIKE', "%$search%");
				} else {
					return $query->where('job_applications.thirdparty_company_id', '>', 0)->where('job_applications.company_id', '=', $company_id)->where('job_applications.job_id', '=', $job_id)->where('job_applications.master_status_id', '!=', 8)->where('job_applications.master_status_id', '!=', 9);
				}
			});
		}

		$jobApplications = $jobApplications->groupBy('job_applications.id');
		$dataTable = DataTables::of($jobApplications)

			->editColumn('chkbox', function ($row) {
				return '<div class="form-check form-check-sm form-check-custom form-check-solid">
						<input class="form-check-input chk_list_item" type="checkbox" value="' . @$row->id . '" />
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

				$flag = 0;
				$duplicate = 0;
				$duplicate = $row->duplicate;
				$company_id = $this->user->roles[0]->company_id;
				$app_duplicate = $this->user->company->app_duplicate;
				$app_duplicate_days = $this->user->company->app_duplicate_days;

				$covid = '';
				if ($duplicate == 1)											// CHECK RECORD IS DUPLICATE
				{

					if (($app_duplicate == 1) || ($app_duplicate == 2)) 		// CHECK SYSTEM MODE SET IN DUPLICATE
					{

						if ($app_duplicate == 1)								// CHECK DUPLICATE JOBWISE WITH TIME INTERVAL IN DAYS FOR- 1
						{

							$application_cnt = DB::table('job_applications')->select('*')->where('company_id', $company_id)->where('job_id', $row->job_id)->where('email', $row->email)->orWhere('phone', $row->phone)->where('job_applications.created_at', '>', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . @$app_duplicate_days . ' DAY)'))->orderby('job_applications.id', 'DESC')->count();
							if ($application_cnt > 1) {
								$flag = 1;
							} else {
								$flag = 0;
							}
						} else if ($app_duplicate == 2)						// CHECK DUPLICATE OVERALL SYSTEM WITH TIME INTERVAL IN DAYS FOR- 2		
						{

							$application_cnt = DB::table('job_applications')->select('job_applications.id')->where('company_id', $company_id)->where('job_applications.email', $row->email)->orWhere('phone', $row->phone)->where('job_applications.created_at', '>', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . @$app_duplicate_days . ' DAY)'))->orderby('job_applications.id', 'DESC')->count();
							if ($application_cnt > 1) {
								$flag = 1;
							} else {
								$flag = 0;
							}
						} else {
							$flag = 0;
						}
					} else {
						$flag = 0;
					}
				} else {
					$flag = 0;
				}


				if ($flag == 1) {

					return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
						<a href="javascript:void(0);"  data-row-id="' . @$row->id . '" class="show-application-detail btn btn-icon btn-light-danger pulse pulse-danger mr-5" data-toggle="tooltip" title="Duplicate">
							<span class="fs-3">' . strtoupper(@$strVal) . '</span>
							<span class="pulse-ring"></span>
						</a>
            		</div>                  
            <div class="d-flex flex-column">
				<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-danger fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">' . ucwords(substr(@$row->full_name, 0, 7)) . $covid . '</a>
				<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-danger fw-bold d-block fs-7 text-hover-primary show-application-detail" data-toggle="tooltip" title="' . ucwords(@$row->current_designation) . '">' . ucwords(substr(@$row->current_designation, 0, 7)) . '</a>
			</div>';

				} else {

					return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
						<a href="javascript:void(0);"   data-row-id="' . @$row->id . '" class="show-application-detail">
							<div class="symbol-label ' . @$bgTextDecore . ' border-radius-5">' . strtoupper(@$strVal) . '</div>
						</a>
					</div>                  
					<div class="d-flex flex-column">
					<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">' . ucwords(substr(@$row->full_name, 0, 10)) . $covid . '</a>
					<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-muted fw-bold d-block fs-7 text-hover-primary show-application-detail" data-toggle="tooltip" title="' . ucwords(@$row->current_designation) . '">' . ucwords(substr(@$row->current_designation, 0, 10)) . '</a>
					</div>';
				}

			})
			->editColumn('Contacts', function ($row) {

				$flag = $duplicate = 0;
				$duplicate = $row->duplicate;
				$company_id = $this->user->roles[0]->company_id;
				$app_duplicate = $this->user->company->app_duplicate;
				$app_duplicate_days = $this->user->company->app_duplicate_days;

				if ($duplicate == 1)										// CHECK RECORD IS DUPLICATE
				{
					if (($app_duplicate == 1) || ($app_duplicate == 2)) 		// CHECK SYSTEM MODE SET IN DUPLICATE
					{
						if ($app_duplicate == 1)								// CHECK DUPLICATE JOBWISE WITH TIME INTERVAL IN DAYS FOR- 1
						{
							$application_cnt = DB::table('job_applications')
								->select('*')
								->where('company_id', $company_id)
								->where('job_id', $row->job_id)
								->where('email', $row->email)
								->orWhere('phone', $row->phone)
								->where('job_applications.created_at', '>', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . @$app_duplicate_days . ' DAY)'))
								->orderby('job_applications.id', 'DESC')
								->count();
							if ($application_cnt > 1) {
								$flag = 1;
							} else {
								$flag = 0;
							}
						} else if ($app_duplicate == 2)						// CHECK DUPLICATE OVERALL SYSTEM WITH TIME INTERVAL IN DAYS FOR- 2		
						{
							$application_cnt = DB::table('job_applications')
								->select('job_applications.id')
								->where('company_id', $company_id)
								->where('job_applications.email', $row->email)
								->orWhere('phone', $row->phone)
								->where('job_applications.created_at', '>', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . @$app_duplicate_days . ' DAY)'))
								->orderby('job_applications.id', 'DESC')
								->count();
							if ($application_cnt > 1) {
								$flag = 1;
							} else {
								$flag = 0;
							}
						} else {
							$flag = 0;
						}
					} else {
						$flag = 0;
					}
				} else {
					$flag = 0;
				}



				$bench_cnt_html = '';
				if ($row->bench_applications_id > 0) {
					$bench_cnt = DB::table('bench_assign')->select('users.id')->join('users', 'users.id', '=', 'bench_assign.user_id')->where('bench_assign.bench_applications_id', '=', $row->bench_applications_id)->count();

					$bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile')->join('users', 'users.id', '=', 'bench_assign.user_id')->where('bench_assign.bench_applications_id', '=', $row->bench_applications_id)->first();

					$row->phone = @$bench_assign_row->mobile;
					if (empty($row->phone)) {
						$row->phone = 'xxx-xxx-xxxx';
					}
					$row->email = @$bench_assign_row->email;
					if (empty($row->email)) {
						$row->email = 'anonymous@email.com';
					}

					if ($bench_cnt > 1) {
						$bench_cnt_html = '<div data-row-id="' . $row->bench_applications_id . '" class="symbol symbol-30 symbol-circle symbol-light bench_assign_listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list.."> 
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
				$current_ctc_mode = substr($salaryMode[@$row->current_ctc_mode], 0, 1);
				$current_ectc_mode = substr($salaryMode[@$row->current_ectc_mode], 0, 1);

				return '<div class="d-flex flex-column">
							<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$current_ctc_cur . @$ctc . '/' . $current_ctc_mode . '</a>
							<span class="text-muted fw-bold d-block fs-7">' . $current_ectc_cur . @$ectc . '/' . $current_ectc_mode . '</span>
					  </div>';
			})

			->editColumn('NoticePeriod111', function ($row) {

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
			->editColumn('NoticePeriod', function ($row) {
				$np = $row->np_id;
				if (empty($np)) {
					$np = 'N/A';
				}
				if ($row->serving_notice_period_mode == 0) {
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
			->editColumn('Interview', function ($row) use ($feedback_status) {
				$schedule_date = $className = '';
				$intRowCount = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->id)->orderBy('interview_schedules.id', 'DESC')->count();
				$intRow = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->id)->orderBy('interview_schedules.id', 'DESC')->first();
				$html = '';
				if ($intRowCount > 0) {
					$schedule_date = @$intRow->schedule_date;
					$schedule_date = date("d M, Y", strtotime($schedule_date));
				} else {
					$html = '<div style="width:150px" id="interview_date">&nbsp;</div>';
				}
				if ($intRowCount > 0) {
					$feedback_status_name = '';
					if ($row->feedback >= 0) {
						$feedback_status_name = @$feedback_status[@$intRow->feedback]['name'];
						$className = @$feedback_status[@$intRow->feedback]['classname'];
					} else {
						$feedback_status_name = 'Pending';
						$className = 'pending-bg';
					}
				} else {
					$feedback_status_name = '--------';
				}
				return '<div class="d-flex flex-column">
										<span class="text-dark fw-bolder fs-6">' . @$schedule_date . '</span>
										<span class="fw-bold d-block fs-7 ' . @$className . ' px-1">' . @$feedback_status_name . '</span>
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
			->editColumn('Stage', function ($row) {

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
						<a href="javascript.void(0);" data-row-id="' . @$row->id . '" class="d-flex flex-column stage_sub_stage" data-toggle="modal" data-target="#stage_sub_stage">
                        	<span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1">' . ucwords(substr($row->name, 0, 10)) . '</span>
                        	<span class="text-muted fw-bold d-block fs-7">' . @$name . '</span>
						</a>
                    </div>';
			});

		$rawColumn = array('chkbox', 'Name', 'Contacts', 'Exp', 'Salary', 'NoticePeriod', 'Qualifaction', 'Interview', 'Location', 'Source', 'Stage');
		$dataTable = $dataTable->rawColumns($rawColumn);
		$dataTable = $dataTable->addIndexColumn()->make(true);
		return $dataTable;
	}

	public function data(Request $request)
	{
     
		//  dd("hfhfh");
		$mstatus_id = $job_id = $search = $feedback_status = '';

		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();
		$company_id = auth()->user()->company_id;
		$mstatus_id = $request->mstatus_id;
		$interviewStatus = array();
		$interviewStatus = DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();

		$job_id = $request->jobid;
		$search = $request->search;

		$chkAdminRole = $this->user->hasRole('admin');

		// $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
		$feedback_status = GeneralMethod::feedback_status();
		// 'application_status.status', 
		$jobApplications = JobApplication::select(
			'job_applications.*',
			'jobs.title',
			'job_locations.location',
			'clients.name as client_name',
			'users.name as posted_by',
			'users.image as posted_by_image',
			'qualification_master.name as qualification_name',
			'master_status.name',
			'master_status.css_style',
			'state_list.state as state_name',
			'city_list.city as indian_city_name',
			'bench_applications.id as bench_applications_id',
			'uc.company_name as uc_company_name',
			'add_job_wise_condidates.id as add_job_wise_condidates_id',
			'add_job_wise_condidates.child_status_id as child_status'
		)
		   ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
			->join('master_status', function($join) use ($job_id){
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id')
				->where('add_job_wise_condidates.job_id', '=', $job_id);
			})
			->join('jobs', function($join) use ($job_id){
				$join->on('jobs.id', 'add_job_wise_condidates.job_id')
				->where('add_job_wise_condidates.job_id', '=', $job_id);
			})
			// ->join('jobs', 'jobs.id', 'job_applications.job_id')
			// ->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
			->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')
			->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
			->leftjoin('clients', 'clients.id', 'jobs.client_id')
			->leftjoin('users', 'users.id', 'job_applications.submitted_by')
			->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id')
			->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
			->leftjoin('application_status', 'application_status.id', 'job_applications.status_id')
			->leftjoin('state_list', 'state_list.id', 'job_applications.state')
			->leftjoin('city_list', 'city_list.city_id', 'job_applications.city_id')
			->leftJoin('rec_candidate_assign', function ($join) use ($job_id, $company_id) {
				$join->on('rec_candidate_assign.source_application_id', '=', 'job_applications.id')
					->where('rec_candidate_assign.source_jobid', '=', $job_id)
					->whereIn('rec_candidate_assign.source_from_type', [1, 2, 3])
					->where('rec_candidate_assign.source_from_company_id', '!=', $company_id);
			})
			
			->leftjoin('bench_applications', 'bench_applications.id', 'rec_candidate_assign.source_from_id');

		$jobApplications = $jobApplications->where(function ($outer) use ($company_id, $job_id, $search, $mstatus_id) {

			if ($mstatus_id > 0) {

				$outer->where(function ($query) use ($company_id, $job_id, $search, $mstatus_id) {
					if (!empty($search)) {
						return $query->where('add_job_wise_condidates.master_status_id', '=', $mstatus_id)->where('add_job_wise_condidates.job_id', '=', $job_id)->where('job_applications.full_name', 'LIKE', "%$search%");
					} else {
						return $query->where('add_job_wise_condidates.master_status_id', '=', $mstatus_id)->where('add_job_wise_condidates.job_id', '=', $job_id);
					}
				});
				$outer->orWhere(function ($query) use ($company_id, $job_id, $search, $mstatus_id) {
					if (!empty($search)) {
						return $query->where('add_job_wise_condidates.job_id', '=', $job_id)
							->where('add_job_wise_condidates.master_status_id', '=', $mstatus_id)->where('job_applications.full_name', 'LIKE', "%$search%");
					} else {
						return $query->where('add_job_wise_condidates.job_id', '=', $job_id)
							->where('add_job_wise_condidates.master_status_id', '=', $mstatus_id);
					}
				});

			} else {

				$outer->where(function ($query) use ($company_id, $job_id, $search, $mstatus_id) {
					if (!empty($search)) {
						return $query->where('add_job_wise_condidates.job_id', '=', $job_id)
							->where('job_applications.full_name', 'LIKE', "%$search%");
					} else {
						return $query->where('add_job_wise_condidates.job_id', '=', $job_id);
					}
				});
				$outer->orWhere(function ($query) use ($company_id, $job_id, $search, $mstatus_id) {
					if (!empty($search)) {
						return $query->where('add_job_wise_condidates.job_id', '=', $job_id)->where('job_applications.full_name', 'LIKE', "%$search%");
					} else {
						return $query->where('add_job_wise_condidates.job_id', '=', $job_id);
					}
				});
			}
		});

		// InReview(8) and agency-Rejected(9) are the submitting agency's
		// private, pre-sourcing stages — the client company must never see
		// these candidates until the agency sources them. index()'s own
		// queries already exclude both; this endpoint (the one that actually
		// renders the candidate table) was missing the exclusion entirely,
		// so the "All" tab leaked every InReview/agency-Rejected candidate.
		$jobApplications = $jobApplications->whereNotIn('add_job_wise_condidates.master_status_id', [8, 9]);

		$jobApplications = $jobApplications->groupBy('job_applications.id');
		$dataTable = DataTables::of($jobApplications)

			->editColumn('chkbox', function ($row) {
				return '<div class="form-check form-check-sm form-check-custom form-check-solid">
						<input class="form-check-input chk_list_item" type="checkbox" value="' . @$row->id . '" />
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

				$flag = 0;
				$duplicate = 0;
				$duplicate = $row->duplicate;
				$company_id = $this->user->roles[0]->company_id;
				$app_duplicate = $this->user->company->app_duplicate;
				$app_duplicate_days = $this->user->company->app_duplicate_days;

				$covid = '';
				if ($duplicate == 1)											// CHECK RECORD IS DUPLICATE
				{

					if (($app_duplicate == 1) || ($app_duplicate == 2)) 		// CHECK SYSTEM MODE SET IN DUPLICATE
					{

						if ($app_duplicate == 1)								// CHECK DUPLICATE JOBWISE WITH TIME INTERVAL IN DAYS FOR- 1
						{

							$application_cnt = DB::table('job_applications')->select('*')->where('company_id', $company_id)->where('job_id', $row->job_id)->where('email', $row->email)->orWhere('phone', $row->phone)->where('job_applications.created_at', '>', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . @$app_duplicate_days . ' DAY)'))->orderby('job_applications.id', 'DESC')->count();
							if ($application_cnt > 1) {
								$flag = 1;
							} else {
								$flag = 0;
							}
						} else if ($app_duplicate == 2)						// CHECK DUPLICATE OVERALL SYSTEM WITH TIME INTERVAL IN DAYS FOR- 2		
						{

							$application_cnt = DB::table('job_applications')->select('job_applications.id')->where('company_id', $company_id)->where('job_applications.email', $row->email)->orWhere('phone', $row->phone)->where('job_applications.created_at', '>', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . @$app_duplicate_days . ' DAY)'))->orderby('job_applications.id', 'DESC')->count();
							if ($application_cnt > 1) {
								$flag = 1;
							} else {
								$flag = 0;
							}
						} else {
							$flag = 0;
						}
					} else {
						$flag = 0;
					}
				} else {
					$flag = 0;
				}


				if ($flag == 1) {

					return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
						<a href="javascript:void(0);"  data-row-id="' . @$row->id . '" class="show-application-detail btn btn-icon btn-light-danger pulse pulse-danger mr-5" data-toggle="tooltip" title="Duplicate">
							<span class="fs-3">' . strtoupper(@$strVal) . '</span>
							<span class="pulse-ring"></span>
						</a>
            		</div>                  
            <div class="d-flex flex-column">
				<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-danger fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">' . ucwords(substr(@$row->full_name, 0, 7)) . $covid . '</a>
				<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-danger fw-bold d-block fs-7 text-hover-primary show-application-detail" data-toggle="tooltip" title="' . ucwords(@$row->current_designation) . '">' . ucwords(substr(@$row->current_designation, 0, 7)) . '</a>
			</div>';

				} else {

					return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
						<a href="javascript:void(0);"   data-row-id="' . @$row->id . '" class="show-application-detail">
							<div class="symbol-label ' . @$bgTextDecore . ' border-radius-5">' . strtoupper(@$strVal) . '</div>
						</a>
					</div>                  
					<div class="d-flex flex-column">
					<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">' . ucwords(substr(@$row->full_name, 0, 10)) . $covid . '</a>
					<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-muted fw-bold d-block fs-7 text-hover-primary show-application-detail" data-toggle="tooltip" title="' . ucwords(@$row->current_designation) . '">' . ucwords(substr(@$row->current_designation, 0, 10)) . '</a>
					</div>';
				}

			})
			->editColumn('Contacts', function ($row) {

				$flag = $duplicate = 0;
				$duplicate = $row->duplicate;
				$company_id = $this->user->roles[0]->company_id;
				$app_duplicate = $this->user->company->app_duplicate;
				$app_duplicate_days = $this->user->company->app_duplicate_days;

				if ($duplicate == 1)										// CHECK RECORD IS DUPLICATE
				{
					if (($app_duplicate == 1) || ($app_duplicate == 2)) 		// CHECK SYSTEM MODE SET IN DUPLICATE
					{
						if ($app_duplicate == 1)								// CHECK DUPLICATE JOBWISE WITH TIME INTERVAL IN DAYS FOR- 1
						{
							$application_cnt = DB::table('job_applications')
								->select('*')
								->where('company_id', $company_id)
								->where('job_id', $row->job_id)
								->where('email', $row->email)
								->orWhere('phone', $row->phone)
								->where('job_applications.created_at', '>', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . @$app_duplicate_days . ' DAY)'))
								->orderby('job_applications.id', 'DESC')
								->count();
							if ($application_cnt > 1) {
								$flag = 1;
							} else {
								$flag = 0;
							}
						} else if ($app_duplicate == 2)						// CHECK DUPLICATE OVERALL SYSTEM WITH TIME INTERVAL IN DAYS FOR- 2		
						{
							$application_cnt = DB::table('job_applications')
								->select('job_applications.id')
								->where('company_id', $company_id)
								->where('job_applications.email', $row->email)
								->orWhere('phone', $row->phone)
								->where('job_applications.created_at', '>', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . @$app_duplicate_days . ' DAY)'))
								->orderby('job_applications.id', 'DESC')
								->count();
							if ($application_cnt > 1) {
								$flag = 1;
							} else {
								$flag = 0;
							}
						} else {
							$flag = 0;
						}
					} else {
						$flag = 0;
					}
				} else {
					$flag = 0;
				}



				$bench_cnt_html = '';
				if ($row->bench_applications_id > 0) {
					$bench_cnt = DB::table('bench_assign')->select('users.id')->join('users', 'users.id', '=', 'bench_assign.user_id')->where('bench_assign.bench_applications_id', '=', $row->bench_applications_id)->count();

					$bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile')->join('users', 'users.id', '=', 'bench_assign.user_id')->where('bench_assign.bench_applications_id', '=', $row->bench_applications_id)->first();

					$row->phone = @$bench_assign_row->mobile;
					if (empty($row->phone)) {
						$row->phone = 'xxx-xxx-xxxx';
					}
					$row->email = @$bench_assign_row->email;
					if (empty($row->email)) {
						$row->email = 'anonymous@email.com';
					}

					if ($bench_cnt > 1) {
						$bench_cnt_html = '<div data-row-id="' . $row->bench_applications_id . '" class="symbol symbol-30 symbol-circle symbol-light bench_assign_listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list.."> 
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
				$current_ctc_mode = substr($salaryMode[@$row->current_ctc_mode], 0, 1);
				$current_ectc_mode = substr($salaryMode[@$row->current_ectc_mode], 0, 1);

				return '<div class="d-flex flex-column">
							<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$current_ctc_cur . @$ctc . '/' . $current_ctc_mode . '</a>
							<span class="text-muted fw-bold d-block fs-7">' . $current_ectc_cur . @$ectc . '/' . $current_ectc_mode . '</span>
					  </div>';
			})

			->editColumn('NoticePeriod111', function ($row) {

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
			->editColumn('NoticePeriod', function ($row) {
				$np = $row->np_id;
				if (empty($np)) {
					$np = 'N/A';
				}
				if ($row->serving_notice_period_mode == 0) {
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
			->editColumn('Interview', function ($row) use ($feedback_status) {
				$schedule_date = $className = '';
				$intRowCount = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->id)->orderBy('interview_schedules.id', 'DESC')->count();
				$intRow = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->id)->orderBy('interview_schedules.id', 'DESC')->first();
				$html = '';
				if ($intRowCount > 0) {
					$schedule_date = @$intRow->schedule_date;
					$schedule_date = date("d M, Y", strtotime($schedule_date));
				} else {
					$html = '<div style="width:150px" id="interview_date">&nbsp;</div>';
				}
				if ($intRowCount > 0) {
					$feedback_status_name = '';
					if ($row->feedback >= 0) {
						$feedback_status_name = @$feedback_status[@$intRow->feedback]['name'];
						$className = @$feedback_status[@$intRow->feedback]['classname'];
					} else {
						$feedback_status_name = 'Pending';
						$className = 'pending-bg';
					}
				} else {
					$feedback_status_name = '--------';
				}
				return '<div class="d-flex flex-column">
										<span class="text-dark fw-bolder fs-6">' . @$schedule_date . '</span>
										<span class="fw-bold d-block fs-7 ' . @$className . ' px-1">' . @$feedback_status_name . '</span>
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
			->editColumn('Stage', function ($row) {

				$name = '--------';
				if ($row->master_status_id == 8) {
					$name = '';
				}
				if (!empty(@$row->child_status) && @$row->child_status > 0) {
					$childStatus = '';
					$childStatus = DB::table('candidate_status')->select('*')->where('id', $row->child_status)->first();
					if (!empty($childStatus)) {
						$name = @$childStatus->name;
					}
				}
				return '<div class="d-flex flex-column">
						<a href="javascript.void(0);" data-row-id="' . @$row->add_job_wise_condidates_id . '" class="d-flex flex-column stage_sub_stage" data-toggle="modal" data-target="#stage_sub_stage">
                        	<span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1">' . ucwords(substr($row->name, 0, 10)) . '</span>
                        	<span class="text-muted fw-bold d-block fs-7">' . @$name . '</span>
						</a>
                    </div>';
			});

		$rawColumn = array('chkbox', 'Name', 'Contacts', 'Exp', 'Salary', 'NoticePeriod', 'Qualifaction', 'Interview', 'Location', 'Source', 'Stage');
		$dataTable = $dataTable->rawColumns($rawColumn);
		$dataTable = $dataTable->addIndexColumn()->make(true);
		return $dataTable;
	}


	public function similar_candidates($id)
	{
		$this->pageTitle = __('Candidates Recommended');
		$company_id = auth()->user()->company_id;
		$this->job = Job::findOrFail($id);
		$this->jobclient = Client::findOrFail($this->job->client_id);
		$this->custome_count = $this->custome_count($id);

		return view('product_company.apptracker.similar_candidates', $this->data);

	}
	public function similar_existing_data(Request $request)
	{

		abort_if(!$this->user->cans('view_job_applications'), 403);
		$company_id = auth()->user()->company_id;

		$jobid = $request->jobid;
		$job = Job::findOrFail($jobid);
		$title = $job->title;
		$min_exp = $job->min_exp;
		$max_exp = $job->max_exp;
		$min_salary = $job->min_salary;
		$max_salary = $job->max_salary;

		$loc_data = DB::table('client_location_jobs')
			->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
			->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
			->select('client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
			->where('client_location_jobs.job_id', $jobid)
			->first();
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
		// 'application_status.status',
		$existingApplications = DB::table('job_applications')
			->select(
				'job_applications.*',
				'jobs.title',
				'job_locations.location',
				'clients.name as client_name',
				'users.name as posted_by',
				'users.image as posted_by_image',
				'master_status.name',
				'master_status.css_style',
				'qualification_master.name as qualification_name',
				'state_list.state as state_name',
				'recommendation_relation.final_status',
				'bench_applications.id as bench_applications_id',
				'uc.company_name as uc_company_name'
			)
			->join('jobs', 'jobs.id', 'job_applications.job_id')
			->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')
			->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
			->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
			->leftjoin('clients', 'clients.id', 'jobs.client_id')
			->leftjoin('users', 'users.id', 'job_applications.submitted_by')
			->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id')
			->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
			->leftjoin('application_status', 'application_status.id', 'job_applications.status_id')
			->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
			->leftjoin('state_list', 'state_list.id', 'job_applications.state');
		$existingApplications = $existingApplications->leftJoin('rec_candidate_assign', function ($join) {
			$join->on('rec_candidate_assign.source_application_id', '=', 'job_applications.id')
				->where('rec_candidate_assign.source_from_type', '=', '2');
		});
		$existingApplications = $existingApplications->leftjoin('bench_applications', 'bench_applications.id', 'rec_candidate_assign.source_from_id');

		if (\request('search') != "") {
			$search = request('search');
			$existingApplications = $existingApplications->where('job_applications.full_name', 'LIKE', "%$search%");
		}
		$existingApplications = $existingApplications->where(function ($query) use ($company_id, $title, $min_exp, $max_exp, $postcode, $jobid) {
			return $query->where('job_applications.company_id', '=', $company_id)
				->where('job_applications.master_status_id', '!=', 6)
				->where('job_applications.thirdparty_company_id', '=', 0)
				->where('job_applications.job_id', '!=', $jobid)
				->where('job_applications.current_designation', 'LIKE', "%$title%")
				->where('job_applications.pincode', '=', $postcode)
				->whereBetween('job_applications.total_exp', [$min_exp, $max_exp]);
		});
		$existingApplications = $existingApplications->orWhere(function ($query) use ($company_id, $title, $jobid) {
			return $query->where('job_applications.company_id', '=', $company_id)
				->where('job_applications.master_status_id', '!=', 6)
				->where('job_applications.thirdparty_company_id', '=', 0)
				->where('job_applications.job_id', '!=', $jobid)
				->where('job_applications.current_designation', 'LIKE', "%$title%");
		});
		foreach ($skill_arr as $val) {
			$existingApplications = $existingApplications->orWhere(function ($query) use ($company_id, $val, $jobid) {
				return $query->where('job_applications.company_id', '=', $company_id)
					->where('job_applications.master_status_id', '!=', 6)
					->where('job_applications.thirdparty_company_id', '=', 0)
					->where('job_applications.job_id', '!=', $jobid)
					->where('job_applications.skills', 'like', '%' . $val . '%');
			});
		}
		$existingApplications = $existingApplications->orWhere(function ($query) use ($company_id, $title, $min_exp, $max_exp, $postcode, $jobid) {
			return $query->where('job_applications.company_id', '=', $company_id)
				->where('job_applications.master_status_id', '!=', 6)
				->where('job_applications.thirdparty_company_id', '>', 0)
				->where('job_applications.master_status_id', '!=', 8)
				->where('job_applications.job_id', '!=', $jobid)
				->where('job_applications.current_designation', 'LIKE', "%$title%")
				->where('job_applications.pincode', '=', $postcode)
				->whereBetween('job_applications.total_exp', [$min_exp, $max_exp]);
		});
		$existingApplications = $existingApplications->orWhere(function ($query) use ($company_id, $title, $jobid) {
			return $query->where('job_applications.company_id', '=', $company_id)
				->where('job_applications.master_status_id', '!=', 6)
				->where('job_applications.thirdparty_company_id', '>', 0)
				->where('job_applications.master_status_id', '!=', 8)
				->where('job_applications.job_id', '!=', $jobid)
				->where('job_applications.current_designation', 'LIKE', "%$title%");
		});
		foreach ($skill_arr as $val) {
			$existingApplications = $existingApplications->orWhere(function ($query) use ($company_id, $val, $jobid) {
				return $query->where('job_applications.company_id', '=', $company_id)
					->where('job_applications.master_status_id', '!=', 6)
					->where('job_applications.thirdparty_company_id', '>', 0)
					->where('job_applications.master_status_id', '!=', 8)
					->where('job_applications.job_id', '!=', $jobid)
					->where('job_applications.skills', 'like', '%' . $val . '%');
			});
		}
		$existingApplications = $existingApplications->orWhere(function ($query) use ($company_id, $title, $min_exp, $max_exp, $postcode, $jobid) {
			return $query->where('job_applications.thirdparty_company_id', '=', $company_id)
				->where('job_applications.master_status_id', '!=', 6)
				->where('recommendation_relation.job_id', '>', 0)
				->where('job_applications.job_id', '!=', $jobid)
				->where('job_applications.current_designation', 'LIKE', "%$title%")
				->where('job_applications.pincode', '=', $postcode)
				->whereBetween('job_applications.total_exp', [$min_exp, $max_exp]);
		});
		$existingApplications = $existingApplications->orWhere(function ($query) use ($company_id, $title, $jobid) {
			return $query->where('job_applications.thirdparty_company_id', '=', $company_id)
				->where('job_applications.master_status_id', '!=', 6)
				->where('recommendation_relation.job_id', '>', 0)
				->where('job_applications.job_id', '!=', $jobid)
				->where('job_applications.current_designation', 'LIKE', "%$title%");
		});
		foreach ($skill_arr as $val) {
			$existingApplications = $existingApplications->orWhere(function ($query) use ($company_id, $val, $jobid) {
				return $query->where('job_applications.thirdparty_company_id', '=', $company_id)
					->where('job_applications.master_status_id', '!=', 6)
					->where('recommendation_relation.job_id', '>', 0)
					->where('job_applications.job_id', '!=', $jobid)
					->where('job_applications.skills', 'like', '%' . $val . '%');
			});
		}
		$existingApplications = $existingApplications->groupBy('job_applications.id');
		$existingApplications = $existingApplications->orderBy('job_applications.id', 'DESC');

		return DataTables::of($existingApplications)

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

				$flag = 0;
				$duplicate = 0;
				$duplicate = $row->duplicate;
				$company_id = $this->user->roles[0]->company_id;
				$app_duplicate = $this->user->company->app_duplicate;
				$app_duplicate_days = $this->user->company->app_duplicate_days;

				$covid = '';
				if ($duplicate == 1)											// CHECK RECORD IS DUPLICATE
				{

					if (($app_duplicate == 1) || ($app_duplicate == 2)) 		// CHECK SYSTEM MODE SET IN DUPLICATE
					{

						if ($app_duplicate == 1)								// CHECK DUPLICATE JOBWISE WITH TIME INTERVAL IN DAYS FOR- 1
						{

							$application_cnt = DB::table('job_applications')->select('*')
								->where('company_id', $company_id)
								->where('job_id', $row->job_id)
								->where('email', $row->email)
								->orWhere('phone', $row->phone)
								->where('job_applications.created_at', '>', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . @$app_duplicate_days . ' DAY)'))
								->orderby('job_applications.id', 'DESC')
								->count();
							if ($application_cnt > 1) {
								$flag = 1;
							} else {
								$flag = 0;
							}
						} else if ($app_duplicate == 2)						// CHECK DUPLICATE OVERALL SYSTEM WITH TIME INTERVAL IN DAYS FOR- 2		
						{


							$application_cnt = DB::table('job_applications')
								->select('job_applications.id')
								->where('company_id', $company_id)
								->where('job_applications.email', $row->email)
								->orWhere('phone', $row->phone)
								->where('job_applications.created_at', '>', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . @$app_duplicate_days . ' DAY)'))
								//->where('job_applications.duplicate', '=', 1 )
								->orderby('job_applications.id', 'DESC')
								->count();

							if ($application_cnt > 1) {
								$flag = 1;
							} else {
								$flag = 0;
							}
						} else {
							$flag = 0;
						}
					} else {
						$flag = 0;
					}
				} else {
					$flag = 0;
				}


				$company_id = auth()->user()->company_id;
				$profile_sidebar_css = 'show-application-detail';
				if ($row->thirdparty_company_id > 0 && $row->thirdparty_company_id == $company_id) {
					$profile_sidebar_css = 'client-show-application-detail';
				}


				if ($flag == 1) {
					return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
<a href="javascript:void(0);"  data-row-id="' . @$row->id . '" class=" ' . $profile_sidebar_css . ' btn btn-icon btn-light-danger pulse pulse-danger mr-5" data-toggle="tooltip" title="Duplicate">
<span class="fs-3">' . strtoupper(@$strVal) . '</span>
<span class="pulse-ring"></span>
</a>
</div>                  
<div class="d-flex flex-column">
<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-danger fw-bolder text-hover-primary fs-6  ' . $profile_sidebar_css . ' " data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">' . ucwords(substr(@$row->full_name, 0, 7)) . $covid . '</a>
<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-danger fw-bold d-block fs-7 text-hover-primary ' . $profile_sidebar_css . ' " data-toggle="tooltip" title="' . ucwords(@$row->current_designation) . '">' . ucwords(substr(@$row->current_designation, 0, 7)) . '</a>
</div>';
				} else {
					return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
<a href="javascript:void(0);"   data-row-id="' . @$row->id . '" class="' . $profile_sidebar_css . '">
<div class="symbol-label ' . @$bgTextDecore . ' border-radius-5">' . strtoupper(@$strVal) . '</div>
</a>
</div>                  
<div class="d-flex flex-column">
<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-dark fw-bolder text-hover-primary fs-6 ' . $profile_sidebar_css . ' " data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">' . ucwords(substr(@$row->full_name, 0, 10)) . $covid . '</a>
<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-muted fw-bold d-block fs-7 text-hover-primary ' . $profile_sidebar_css . ' " data-toggle="tooltip" title="' . ucwords(@$row->current_designation) . '">' . ucwords(substr(@$row->current_designation, 0, 10)) . '</a>
</div>';
				}
			})
			->editColumn('Contacts', function ($row) {


				$flag = 0;
				$duplicate = 0;
				$duplicate = $row->duplicate;
				$company_id = $this->user->roles[0]->company_id;
				$app_duplicate = $this->user->company->app_duplicate;
				$app_duplicate_days = $this->user->company->app_duplicate_days;

				if ($duplicate == 1)										// CHECK RECORD IS DUPLICATE
				{
					if (($app_duplicate == 1) || ($app_duplicate == 2)) 		// CHECK SYSTEM MODE SET IN DUPLICATE
					{
						if ($app_duplicate == 1)								// CHECK DUPLICATE JOBWISE WITH TIME INTERVAL IN DAYS FOR- 1
						{
							$application_cnt = DB::table('job_applications')->select('*')->where('company_id', $company_id)->where('job_id', $row->job_id)->where('email', $row->email)->orWhere('phone', $row->phone)->where('job_applications.created_at', '>', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . @$app_duplicate_days . ' DAY)'))->orderby('job_applications.id', 'DESC')->count();
							if ($application_cnt > 1) {
								$flag = 1;
							} else {
								$flag = 0;
							}
						} else if ($app_duplicate == 2)						// CHECK DUPLICATE OVERALL SYSTEM WITH TIME INTERVAL IN DAYS FOR- 2		
						{
							$application_cnt = DB::table('job_applications')->select('job_applications.id')->where('company_id', $company_id)->where('job_applications.email', $row->email)->orWhere('phone', $row->phone)->where('job_applications.created_at', '>', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . @$app_duplicate_days . ' DAY)'))->orderby('job_applications.id', 'DESC')->count();
							if ($application_cnt > 1) {
								$flag = 1;
							} else {
								$flag = 0;
							}
						} else {
							$flag = 0;
						}
					} else {
						$flag = 0;
					}
				} else {
					$flag = 0;
				}


				$bench_cnt_html = '';
				if ($row->bench_applications_id > 0) {

					$bench_cnt = DB::table('bench_assign')->select('users.id')
						->join('users', 'users.id', '=', 'bench_assign.user_id')->where('bench_assign.bench_applications_id', '=', $row->bench_applications_id)->count();

					$bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile')
						->join('users', 'users.id', '=', 'bench_assign.user_id')
						->where('bench_assign.bench_applications_id', '=', $row->bench_applications_id)->first();

					$row->phone = @$bench_assign_row->mobile;
					if (empty($row->phone)) {
						$row->phone = 'xxx-xxx-xxxx';
					}
					$row->email = @$bench_assign_row->email;
					if (empty($row->email)) {
						$row->email = 'anonymous@email.com';
					}

					if ($bench_cnt > 1) {
						$bench_cnt_html = '<div data-row-id="' . $row->bench_applications_id . '" class="symbol symbol-30 symbol-circle symbol-light bench_assign_listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list.."> 
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

			->editColumn('NP', function ($row) {

				$np = $row->np_id;
				if (empty($np)) {
					$np = 'N/A';
				}
				if ($row->serving_notice_period_mode == 1) {
					$npDate = $row->np_id;
					if ($row->serving_notice_period_mode == 0 || $row->serving_notice_period_mode == 1) {
						$originalDate = $row->np_id;
						$np = 'SNP LWD ' . date("d-M-y", strtotime($originalDate));
					}
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
			->editColumn('Qualification', function ($row) {
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
							<img alt="image" src="' . @$posted_by_image . '">
						  </div>
					  </div>
					  <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="' . @$row->uc_company_name . '">' . substr(@$row->uc_company_name, 0, 10) . '</span>';
			})
			->editColumn('Date', function ($row) {
				$created_at = '';
				$created_at = $newDate = date("d M , Y", strtotime($row->created_at));
				return '<div class="d-flex flex-column">
                    			<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . @$created_at . '</a> 
                  			</div>';
			})

			->editColumn('Requisitions', function ($row) use ($company_id) {

				$removed_status = $icon = $job_title = $client_name = '';
				if ($row->final_status == 2) {
					$removed_status = '<div class=" fw-bolder fs-6 closed-bg px-2 py-1">REMOVED</div>';
				}

				//$icon				=	'&nbsp;<i class="bi bi-house" style="color:#00CC00"></i>';
				if ($row->thirdparty_company_id == $company_id) {
					//$icon			=	'&nbsp;<i style="color: #FF0000; font-weight:bold" class="fa fa-users" aria-hidden="true"></i>';	
	
				}
				$job_title = $row->title;
				$job_title = ucwords(substr($job_title, 0, 15));
				$client_name = $row->client_name;
				$client_name = ucwords(substr($client_name, 0, 15));

				return '<div class="d-flex flex-column">
                    	<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="" data-bs-original-title="' . $row->title . '">' . $job_title . '</a>
                    	<span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="' . $row->client_name . '">' . $client_name . $icon . $removed_status . '</span>
                  	</div>';
			})
			->editColumn('Stage', function ($row) use ($company_id) {

				$stage_default_css = 'stage_sub_stage';
				if (($row->thirdparty_company_id > 0 && $row->thirdparty_company_id == $company_id)) {
					$stage_default_css = 'client_stage_sub_stage';
				}
				if (($row->thirdparty_company_id > 0 && $row->thirdparty_company_id == $company_id) && $row->master_status_id != 8) {
					$stage_default_css = '';
				}

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
			->editColumn('Action', function ($row) use ($jobid, $company_id) {

				$source_from_id = @$row->id;
				$recCounter = 0;
				$recCounter = DB::table('rec_candidate_assign')->select('id')->where('source_from_id', @$row->id)->where('source_jobid', @$jobid)->count();

				$source_from_type = 0;
				$public_bench_conter = DB::table('rec_candidate_assign')->select('id')->where('source_from_type', '=', 2)->where('source_company_id', '=', $company_id)->where('source_application_id', '=', @$row->id)->count();

				if ($public_bench_conter > 0) {
					$source_from_type = 2;
				}

				$internal_bench_conter = DB::table('rec_candidate_assign')->select('id')->where('source_from_type', '=', 1)->where('source_company_id', '=', $company_id)->where('source_application_id', '=', @$row->id)->count();

				if ($internal_bench_conter > 0) {
					$source_from_type = 1;
				}

				if ($recCounter > 0) {
					$html = '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">Added</a> </div>';
				} else {

					$public_bench_conter = DB::table('rec_candidate_assign')->select('id')->where('source_from_type', '=', 2)->where('source_company_id', '=', $company_id)->where('source_jobid', '=', $jobid)->where('parent_id', '=', @$row->id)->count();
					$internal_bench_conter = DB::table('rec_candidate_assign')->select('id')->where('source_from_type', '=', 1)->where('source_company_id', '=', $company_id)->where('source_jobid', '=', $jobid)->where('parent_id', '=', @$row->id)->count();

					if ($public_bench_conter > 0) {
						$html = '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">Added</a> </div>';
					} elseif ($internal_bench_conter > 0) {
						$html = '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">Added</a> </div>';
					} else {
						$html = '<div class="d-flex flex-column"> 
									<a href="javascript:void(0);" class="btn btn-sm btn-primary add_to_inreview" data-row-from="existing" data-row-source_from_type="' . @$source_from_type . '"  data-row-id="' . @$row->id . '" data-row-jobid="' . @$jobid . '">
										Add InReview
									</a> 
								</div>';
					}

				}
				return $html;
			})

			->rawColumns(['chkbox', 'Name', 'Contacts', 'Exp', 'Salary', 'NP', 'Qualification', 'Location', 'Source', 'Date', 'Requisitions', 'Stage', 'Action'])
			->addIndexColumn()
			->make(true);
	}
	public function rec_candidate_assign(Request $request)
	{

		$id = $source_jobid = $source_from_type = $company_id = $source_jobid = $source_company_id = $source_user_id = "";
		$source_from_company_id = $source_from_user_id = $source_from_id = $source_tab = $submitted_by = $thirdparty_company_id = '';

		$source_company_id = auth()->user()->company_id;
		$source_user_id = auth()->user()->id;

		$applicationsInsertedId = $customClient = 0;

		$id = @$request->id;
		$source_jobid = @$request->source_jobid;
		$source_from_type = @$request->source_from_type;
		$source_tab = @$request->source_tab;

		//######### FOR JOB APPLICANT
		$jobRow = DB::table('jobs')->select('*')->where('id', $source_jobid)->first();
		if ($source_company_id != $jobRow->company_id) { 								// CHECK DIRECT CLIENT APPLICATION 
			$customClient = 1;
		}
		if ($customClient == 1) {														//	DIRECT CLIENT JOB  
			$thirdparty_company_id = auth()->user()->company_id;
		} else {																		// 	CUSTOM CLIENT JOB 
			$thirdparty_company_id = 0;

		}
		//######### FOR EXISTING APPLICANT	
		if ($source_from_type == 0) {
			$row_data = DB::table('job_applications')->select('*')->where('id', $id)->first();
			$source_from_company_id = @$row_data->company_id;
			$source_from_user_id = @$row_data->submitted_by;
			$source_from_id = @$row_data->id;

			$job_application_values = array(
				'submitted_by' => $this->user->id,
				'company_id' => @$jobRow->company_id,
				'thirdparty_company_id' => @$thirdparty_company_id,

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

				'child_status_id' => @$row_data->child_status_id,
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
			$bCnt = DB::table('rec_candidate_assign')->select('id')->where('source_company_id', '=', auth()->user()->company_id)->where('source_from_type', '=', 1)->where('source_application_id', '=', $row_data->id)->count();
			if ($bCnt > 0) {
				$bData = DB::table('rec_candidate_assign')->select('rec_candidate_assign.id', 'rec_candidate_assign.source_from_id', 'rec_candidate_assign.source_application_id')->where('source_company_id', '=', auth()->user()->company_id)->where('source_from_type', '=', 1)->where('source_application_id', '=', $row_data->id)->first();
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
				'source_jobid' => @$source_jobid
			);
			DB::table('rec_candidate_assign')->insert($values);
			$recCandidateAssignId = DB::getPdo()->lastInsertId();

			$insertArr = array('job_id' => @$source_jobid, 'rec_candidate_assign_id'=>$recCandidateAssignId, 'job_application_id' =>@$application_id, 'status_id' => @$row_data->status_id, 'master_status_id' => 8, 'child_status_id'=>@$row_data->child_status_id, 'company_id' => @$jobRow->company_id, 'submitted_by' => @$this->user->id,'thirdparty_company_id'=>@$thirdparty_company_id,'existing'=>1);

			DB::table('add_job_wise_condidates')->insert($insertArr);

		}

		//######### FOR INTERNAL BENCH APPLICANT
		if ($source_from_type == 1) {
			$parent_id = 0;
			if ($source_tab == 'existing') {
				$rec_row_data = DB::table('rec_candidate_assign')->select('rec_candidate_assign.source_from_id')->where('rec_candidate_assign.source_from_type', '=', 1)->where('rec_candidate_assign.source_application_id', '=', $id)->where('rec_candidate_assign.source_company_id', '=', $source_company_id)->first();
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

				'submitted_by' => @$this->user->id,
				'company_id' => @$jobRow->company_id,
				'thirdparty_company_id' => @$thirdparty_company_id,

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
			DB::table('job_applications')->insert($job_application_values);

			$note = 'Similar Candidate => Internal Bench';

			if ($source_from_company_id != auth()->user()->company_id) {
				$note = 'Similar Candidate => Public Bench';
			}

			$application_id = "";
			$application_id = DB::getPdo()->lastInsertId();
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
				'source_jobid' => @$source_jobid
			);
			DB::table('rec_candidate_assign')->insert($values);
			$recCandidateAssignId = DB::getPdo()->lastInsertId();

			$insertArr = array('job_id' => @$source_jobid, 'rec_candidate_assign_id'=>$recCandidateAssignId, 'job_application_id' =>@$application_id, 'master_status_id' => 8, 'company_id' => @$jobRow->company_id, 'submitted_by' => @$this->user->id,'thirdparty_company_id'=>@$thirdparty_company_id,'existing'=>1);

			DB::table('add_job_wise_condidates')->insert($insertArr);


		}

		//######### FOR PUBLIC BENCH APPLICANT
		if ($source_from_type == 2) {

			$parent_id = 0;
			if ($source_tab == 'existing') {

				$rec_row_data = DB::table('rec_candidate_assign')
					->select('rec_candidate_assign.source_from_id')
					->where('rec_candidate_assign.source_from_type', '=', 2)
					->where('rec_candidate_assign.source_application_id', '=', $id)
					->where('rec_candidate_assign.source_company_id', '=', $source_company_id)
					->first();
				$row_data = DB::table('bench_applications')->select('*')->where('id', @$rec_row_data->source_from_id)->first();
				$parent_id = $id;

			} else {
				$row_data = DB::table('bench_applications')->select('*')->where('id', @$id)->first();
			}

			$source_from_company_id = @$row_data->company_id;
			$source_from_user_id = @$row_data->submitted_by;
			$source_from_id = @$row_data->id;

			$job_application_values = array(
				'submitted_by' => @$this->user->id,
				'company_id' => @$jobRow->company_id,
				'thirdparty_company_id' => @$thirdparty_company_id,

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
			DB::table('job_applications')->insert($job_application_values);

			$note = 'Similar Candidate => Public Bench';

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
			$values = array(
				'source_from_company_id' => @$source_from_company_id,
				'source_from_user_id' => @$source_from_user_id,
				'source_from_id' => @$source_from_id,
				'source_from_type' => @$source_from_type,

				'parent_id' => $parent_id,
				'source_company_id' => @$source_company_id,
				'source_user_id' => @$source_user_id,
				'source_application_id' => @$application_id,
				'source_jobid' => @$source_jobid
			);
			DB::table('rec_candidate_assign')->insert($values);
			$recCandidateAssignId = DB::getPdo()->lastInsertId();

			$insertArr = array('job_id' => @$source_jobid, 'rec_candidate_assign_id'=>$recCandidateAssignId, 'job_application_id' =>@$application_id, 'master_status_id' => 8, 'company_id' => @$jobRow->company_id, 'submitted_by' => @$this->user->id,'thirdparty_company_id'=>@$thirdparty_company_id,'existing'=>1);

			DB::table('add_job_wise_condidates')->insert($insertArr);



			// REDEEM CVACCESS   
			if (@$application_id > 0) {
				$event_name = 'CVACCESS';
				$event_table_name = 'job_applications';
				$event_table_id = @$application_id;
				$packageStatus = GeneralMethod::packageRedeem($event_name, $event_table_name, $event_table_id);
			}
			// CLIENT REDEEM NEW_BENCH_CLIENT_REQUEST 
			if (@$application_id > 0) {
				$event_name = 'NEW_BENCH_CLIENT_REQUEST';
				$event_table_name = 'job_applications';
				$event_table_id = @$application_id;
				$packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
			}
		}

		return Reply::dataOnly(['status' => 'success']);


	}
	public function candidates_request()
	{
		$this->pageTitle = __('Candidates Request');
		$id = session()->get('job_id');
		$this->job = Job::findOrFail($id);
		$this->jobclient = Client::findOrFail($this->job->client_id);
		$this->custome_count = $this->custome_count($id);
		return view('product_company.apptracker.candidates_request', $this->data);
	}

	public function candidates_request_data(Request $request)
	{

		// abort_if(!$this->user->cans('view_job_applications'), 403);		
		$company_id = $mstatus_id = $jobid = $feedback_status = '';

		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();
		$company_id = auth()->user()->company_id;
		$mstatus_id = $request->mstatus_id;
		$jobid = session()->get('job_id');

		// dd($company_id,$jobid);

		$interviewStatus = array();
		$interviewStatus = DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();
		$chkAdminRole = $this->user->hasRole('admin');
		// $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
		$feedback_status = GeneralMethod::feedback_status();

		$jobApplications = JobApplication::select(
			'job_applications.*',
			'jobs.title',
			'job_locations.location',
			'application_status.status as a_status',
			'clients.name as client_name',
			'users.name as posted_by',
			'users.image as posted_by_image',
			//'notice_periods.np_title',
			'qualification_master.name as qualification_name',
			'master_status.name',
			'master_status.css_style',
			'state_list.state as state_name',
			'city_list.city as indian_city_name',
			'companies.company_name',
			'rec_candidate_assign.source_application_id',
			'rec_candidate_assign.source_jobid',
			'rec_candidate_assign.id as rec_candidate_assign_id'
		)
			->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
			->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
			->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')
			->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
			->leftjoin('clients', 'clients.id', 'jobs.client_id')
			->leftjoin('users', 'users.id', 'job_applications.submitted_by')
			->leftjoin('companies', 'companies.id', 'users.company_id')
			->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
			->leftjoin('application_status', 'application_status.id', 'job_applications.status_id')
			->leftjoin('state_list', 'state_list.id', 'job_applications.state')
			->leftjoin('city_list', 'city_list.city_id', 'job_applications.city_id')
			->join('rec_candidate_assign', function ($join) use ($jobid, $company_id) {
				$join->on('rec_candidate_assign.source_application_id', '=', 'job_applications.id')
					->where('rec_candidate_assign.source_jobid', '=', $jobid)
					->where('rec_candidate_assign.source_from_type', '=', 1)
					->where('rec_candidate_assign.source_company_id', '=', $company_id);
				// ->where('rec_candidate_assign.source_from_company_id', '!=', $company_id);
			});

		$search = '';
		if (\request('search') != "") {
			$search = request('search');
			$jobApplications = $jobApplications->where('job_applications.full_name', 'LIKE', "%$search%");
		}
		$mstatus_id = $request->mstatus_id;
		// $jobApplications = $jobApplications->where(function ($query) use ($jobid, $mstatus_id, $search) {
		// 	if ($mstatus_id != 'all') {
		// 		if (!empty($search)) {
		// 			return $query->where('job_applications.thirdparty_company_id', '=', 0)
		// 				->where('job_applications.job_id', $jobid)
		// 				->where('job_applications.master_status_id', $mstatus_id)
		// 				->where('job_applications.full_name', 'LIKE', "%$search%");
		// 		} else {
		// 			return $query->where('job_applications.thirdparty_company_id', '=', 0)
		// 				->where('job_applications.job_id', $jobid)
		// 				->where('job_applications.master_status_id', $mstatus_id);
		// 		}
		// 	} else {
		// 		if (!empty($search)) {
		// 			return $query->where('job_applications.thirdparty_company_id', '=', 0)
		// 				->where('job_applications.job_id', $jobid)
		// 				->where('job_applications.full_name', 'LIKE', "%$search%");
		// 		} else {
		// 			return $query->where('job_applications.thirdparty_company_id', '=', 0)
		// 				->where('job_applications.job_id', $jobid);
		// 		}

		// 	}
		// });

		// $jobApplications = $jobApplications->orWhere(function ($query) use ($jobid, $mstatus_id, $search) {
		// 	if ($mstatus_id != 'all') {

		// 		if (!empty($search)) {
		// 			return $query->where('job_applications.thirdparty_company_id', '>', 0)
		// 				->where('job_applications.job_id', $jobid)
		// 				->where('job_applications.master_status_id', '!=', 8)
		// 				->where('job_applications.master_status_id', '!=', 9)
		// 				->where('job_applications.master_status_id', $mstatus_id)
		// 				->where('job_applications.full_name', 'LIKE', "%$search%");
		// 		} else {
		// 			return $query->where('job_applications.thirdparty_company_id', '>', 0)
		// 				->where('job_applications.job_id', $jobid)
		// 				->where('job_applications.master_status_id', '!=', 8)
		// 				->where('job_applications.master_status_id', '!=', 9)
		// 				->where('job_applications.master_status_id', $mstatus_id);
		// 		}

		// 	} else {

		// 		if (!empty($search)) {
		// 			return $query->where('job_applications.thirdparty_company_id', '>', 0)
		// 				->where('job_applications.job_id', $jobid)
		// 				->where('job_applications.master_status_id', '!=', 8)
		// 				->where('job_applications.master_status_id', '!=', 9)
		// 				->where('job_applications.full_name', 'LIKE', "%$search%");
		// 		} else {
		// 			return $query->where('job_applications.thirdparty_company_id', '>', 0)
		// 				->where('job_applications.job_id', $jobid)
		// 				->where('job_applications.master_status_id', '!=', 8)
		// 				->where('job_applications.master_status_id', '!=', 9);
		// 		}

		// 	}
		// });

		$jobApplications = $jobApplications->where('job_applications.condidate_type', '=', 'bench_condidate');
		$jobApplications = $jobApplications->groupBy('job_applications.id');

		// dd($jobApplications->toSql(),$jobApplications->get());

		//echo $jobApplications->toSql(); exit; 		

		$dataTable = DataTables::of($jobApplications)

			->editColumn('chkbox', function ($row) {
				return '<div class="form-check form-check-sm form-check-custom form-check-solid">
							<input class="form-check-input chk_list_item" type="checkbox" value="' . @$row->id . '" />
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

				$flag = 0;
				$duplicate = 0;
				$duplicate = $row->duplicate;
				$company_id = $this->user->roles[0]->company_id;
				$app_duplicate = $this->user->company->app_duplicate;
				$app_duplicate_days = $this->user->company->app_duplicate_days;

				$covid = '';
				if ($duplicate == 1)											// CHECK RECORD IS DUPLICATE
				{

					if (($app_duplicate == 1) || ($app_duplicate == 2)) 		// CHECK SYSTEM MODE SET IN DUPLICATE
					{

						if ($app_duplicate == 1)								// CHECK DUPLICATE JOBWISE WITH TIME INTERVAL IN DAYS FOR- 1
						{

							$application_cnt = DB::table('job_applications')->select('*')
								->where('company_id', $company_id)
								->where('job_id', $row->job_id)
								->where('email', $row->email)
								->orWhere('phone', $row->phone)
								->where('job_applications.created_at', '>', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . @$app_duplicate_days . ' DAY)'))
								->orderby('job_applications.id', 'DESC')
								->count();
							if ($application_cnt > 1) {
								$flag = 1;
							} else {
								$flag = 0;
							}
						} else if ($app_duplicate == 2)						// CHECK DUPLICATE OVERALL SYSTEM WITH TIME INTERVAL IN DAYS FOR- 2		
						{



							$application_cnt = DB::table('job_applications')
								->select('job_applications.id')
								->where('company_id', $company_id)
								->where('job_applications.email', $row->email)
								->orWhere('phone', $row->phone)
								->where('job_applications.created_at', '>', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . @$app_duplicate_days . ' DAY)'))
								//->where('job_applications.duplicate', '=', 1 )
								->orderby('job_applications.id', 'DESC')
								->count();

							if ($application_cnt > 1) {
								$flag = 1;
							} else {
								$flag = 0;
							}
						} else {
							$flag = 0;
						}
					} else {
						$flag = 0;
					}
				} else {
					$flag = 0;
				}


				if ($flag == 1) {
					return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
		<a href="javascript:void(0);"  data-row-id="' . @$row->id . '" class="show-application-detail btn btn-icon btn-light-danger pulse pulse-danger mr-5" data-toggle="tooltip" title="Duplicate">
		<span class="fs-3">' . strtoupper(@$strVal) . '</span>
		<span class="pulse-ring"></span>
		</a>
		</div>                  
		<div class="d-flex flex-column">
		<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-danger fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">' . ucwords(substr(@$row->full_name, 0, 7)) . $covid . '</a>
		<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-danger fw-bold d-block fs-7 text-hover-primary show-application-detail" data-toggle="tooltip" title="' . ucwords(@$row->current_designation) . '">' . ucwords(substr(@$row->current_designation, 0, 7)) . '</a>
		</div>';
				} else {
					return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
		<a href="javascript:void(0);"   data-row-id="' . @$row->id . '" class="show-application-detail">
		<div class="symbol-label ' . @$bgTextDecore . ' border-radius-5">' . strtoupper(@$strVal) . '</div>
		</a>
		</div>                  
		<div class="d-flex flex-column">
		<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">' . ucwords(substr(@$row->full_name, 0, 10)) . $covid . '</a>
		<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-muted fw-bold d-block fs-7 text-hover-primary show-application-detail" data-toggle="tooltip" title="' . ucwords(@$row->current_designation) . '">' . ucwords(substr(@$row->current_designation, 0, 10)) . '</a>
		</div>';
				}

			})
			->editColumn('Contacts', function ($row) {
				if ($row->outsourcing_type == 1) {
					$type = 'Fulltime';
				} elseif ($row->outsourcing_type == 2) {
					$type = 'Contract';
				} elseif ($row->outsourcing_type == 3) {
					$type = 'Fulltime/Contract';
				}
				return '<div class="d-flex flex-column">
								   <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6"><span class="badge fw-bolder fs-6 joined-bg px-2 py-1">' . $type . '</span></a>
							</div>';
			})
			->editColumn('Exp', function ($row) {
				return '<div class="d-flex flex-column">
							<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$row->total_exp . '+</a>
							<span class="text-muted fw-bold d-block fs-7">' . @$row->rel_exp . '+</span>
						  </div>';
			})
			->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {

				$current_ctc_cur = "";
				$current_ctc_cur = $currencySymbole[@$row->current_ctc_cur];

				$current_ectc_cur = "";
				$current_ectc_cur = $currencySymbole[@$row->current_ectc_cur];

				$ctc = "";
				$ctc = GeneralMethod::thousandsCurrencyFormat($row->ctc);
				$ectc = "";
				$ectc = GeneralMethod::thousandsCurrencyFormat($row->ectc);

				$current_ctc_mode = "";
				$current_ctc_mode = substr($salaryMode[@$row->current_ctc_mode], 0, 1);

				$current_ectc_mode = "";
				$current_ectc_mode = substr($salaryMode[@$row->current_ectc_mode], 0, 1);

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
				if ($row->serving_notice_period_mode == 0) {
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
			->editColumn('Interview', function ($row) use ($feedback_status) {

				$schedule_date = $className = '';
				$intRowCount = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->id)->orderBy('interview_schedules.id', 'DESC')->count();
				$intRow = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->id)->orderBy('interview_schedules.id', 'DESC')->first();
				$html = '';
				if ($intRowCount > 0) {
					$schedule_date = @$intRow->schedule_date;
					$schedule_date = date("d M, Y", strtotime($schedule_date));
				} else {
					$html = '<div style="width:150px" id="interview_date">&nbsp;</div>';
				}

				if ($intRowCount > 0) {
					$feedback_status_name = '';
					if ($intRow->feedback >= 0) {
						$feedback_status_name = @$feedback_status[@$intRow->feedback]['name'];
						$className = @$feedback_status[@$intRow->feedback]['classname'];
					} else {
						$feedback_status_name = 'Pending';
						$className = 'pending-bg';
					}
				} else {
					$feedback_status_name = '--------';
				}

				return '<div class="d-flex flex-column">
										<span class="text-dark fw-bolder fs-6">' . @$schedule_date . '</span>
										<span class="fw-bold d-block fs-7 ' . @$className . ' px-1">' . @$feedback_status_name . '</span>
									</div>';
			})
			->editColumn('Location', function ($row) {


				$country = $row_country_name = '';

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

				$state = $row_state_name = '';

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


				$city = $row_city_name = '';

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
						  <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="">' . date("d-m-Y H:i:s", strtotime(@$row->updated_at)) . '</span></div>
						  ';
			})

			->editColumn('Action', function ($row) {

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
				$addStatus = DB::table('add_job_wise_condidates')->select('*')->where('job_id', $row->source_jobid)->where('job_application_id', $row->source_application_id)->first();
				if(empty($addStatus)){
					return ' <div class="d-flex flex-column">
						<a href="javascript:void(0);" data-row-id="' . @$row->source_jobid . '" data-recomnded-id="' . @$row->rec_candidate_assign_id . '" data-condidate-id="' . @$row->id . '" class="btn btn-sm btn-primary add_to_list">Add to list</a>
					  </div>';
				} else {
					return ' <div class="d-flex flex-column">
							  <a href="javascript:void(0);" class="btn btn-sm btn-primary">Added</a>
					  </div> ';
				}
			});


		$rawColumn = array('chkbox', 'Name', 'Contacts', 'Exp', 'Salary', 'NoticePeriod', 'Qualifaction', 'Interview', 'Location', 'Source', 'Action');
		$dataTable = $dataTable->rawColumns($rawColumn);
		$dataTable = $dataTable->addIndexColumn()->make(true);
		return $dataTable;
	}

	public function internal_bench($id)
	{

		$this->pageTitle = __('Candidates Recommended');
		$company_id = auth()->user()->company_id;
		$this->job = Job::findOrFail($id);
		$this->jobclient = Client::findOrFail($this->job->client_id);
		$this->custome_count = $this->custome_count($id);

		return view('product_company.apptracker.internal_bench', $this->data);
	}
	public function internal_bench_data(Request $request)
	{

		$company_id = auth()->user()->company_id;

		$jobid = $request->jobid;
		$job = Job::findOrFail($jobid);
		$title = $job->title;
		$min_exp = $job->min_exp;
		$max_exp = $job->max_exp;
		$min_salary = $job->min_salary;
		$max_salary = $job->max_salary;

		$loc_data = DB::table('client_location_jobs')
			->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
			->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
			->select('client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
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

		// $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();

		$benchApplications = DB::table('job_applications')
			->select('job_applications.*', 'qualification_master.name as qualification_name', 'bench_master_status.name as master_status_name', 'bench_master_status.css_style');
		$benchApplications = $benchApplications->join('bench_master_status', 'bench_master_status.id', 'job_applications.status');
		$benchApplications = $benchApplications->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification');

		if (\request('search') != "") {
			$search = request('search');
			$benchApplications = $benchApplications->where('job_applications.status', 2)->where('job_applications.company_id', '=', $company_id)->where('job_applications.full_name', 'LIKE', "%$search%");
		}

		$benchApplications = $benchApplications->where(function ($query) use ($company_id, $title, $min_exp, $max_exp, $postcode) {
			return $query->where('job_applications.status', 2)
				->where('job_applications.company_id', '=', $company_id)
				->where('job_applications.current_designation', 'LIKE', "%$title%")
				->where('job_applications.pincode', '=', $postcode)
				->whereBetween('job_applications.total_exp', [$min_exp, $max_exp]);

		});
		$benchApplications = $benchApplications->orWhere(function ($query) use ($company_id, $title) {
			return $query->where('job_applications.company_id', $company_id)->where('job_applications.status', 2)->where('job_applications.current_designation', 'LIKE', "%$title%");
		});
		foreach ($skill_arr as $val) {
			$benchApplications = $benchApplications->orWhere(function ($query) use ($company_id, $val) {
				return $query->where('job_applications.company_id', $company_id)->where('job_applications.status', 2)->where('job_applications.skills', 'like', '%' . $val . '%');

			});
		}
		$benchApplications = $benchApplications->where('job_applications.condidate_type', 'bench_condidate');
		$benchApplications = $benchApplications->groupBy('job_applications.id');
		$benchApplications = $benchApplications->orderBy('job_applications.id', 'DESC');


		return DataTables::of($benchApplications)


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
				$profile_sidebar_css = 'bench_show_application_detail';

				return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
<a href="javascript:void(0);"   data-row-id="' . @$row->id . '" class="' . $profile_sidebar_css . '">
<div class="symbol-label ' . @$bgTextDecore . ' border-radius-5">' . strtoupper(@$strVal) . '</div>
</a>
</div>                  
<div class="d-flex flex-column">
<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-dark fw-bolder text-hover-primary fs-6 ' . $profile_sidebar_css . ' " data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">' . ucwords(substr(@$row->full_name, 0, 10)) . '</a>
<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-muted fw-bold d-block fs-7 text-hover-primary ' . $profile_sidebar_css . ' " data-toggle="tooltip" title="' . ucwords(@$row->current_designation) . '">' . ucwords(substr(@$row->current_designation, 0, 10)) . '</a>
</div>';

			})
			->editColumn('Exp', function ($row) {
				return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$row->total_exp . '+</a>
                    <span class="text-muted fw-bold d-block fs-7">' . @$row->rel_exp . '+</span>
                  </div>';
			})
			->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {

				$pay_rate_currency = $bill_rate_currency = $pay_rate_amount = $bill_rate_amount = $bill_rate_mode = $pay_rate_mode = "";
				$pay_rate_currency = $currencySymbole[@$row->pay_rate_currency];
				$bill_rate_currency = $currencySymbole[@$row->bill_rate_currency];
				$pay_rate_amount = GeneralMethod::thousandsCurrencyFormat($row->pay_rate_amount);
				$bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($row->bill_rate_amount);

				return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$pay_rate_currency . @$ctc . '/' . $pay_rate_mode . '</a>
                    <span class="text-muted fw-bold d-block fs-7">' . $bill_rate_currency . @$bill_rate_amount . '/' . $bill_rate_mode . '</span>
                  </div>';
			})

			->editColumn('NP', function ($row) {
				$npArr = array('Notice period', 'Available from', 'Immediate');

				$npDate = $row->np_id;
				if ($row->serving_notice_period_mode == 0 || $row->serving_notice_period_mode == 1) {
					$originalDate = $row->np_id;
					$npDate = 'SNP LWD ' . date("d-M-y", strtotime($originalDate));
				}

				return '<div class="d-flex flex-column">
                      <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . $npArr[$row->serving_notice_period_mode] . '</a> 
                      <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' . $row->np_id . '">' . $npDate . '</span>
                    </div>';
			})
			->editColumn('Qualification', function ($row) {
				return ' <div class="d-flex flex-column">
						  <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="' . @$row->qualification_name . '">' . @$row->qualification_name . '</a>
						  <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' . $row->skills . '">' . substr($row->skills, 0, 12) . '</span> 
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
						$row_state = DB::table('state_list')->select('*')->where('id', @$row->state)->first();
						$row_state_name = @$row_state->state;

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
					$row_city = DB::table('city_list')->select('*')->where('city_id', @$row->city)->first();
					$row_city_name = @$row_city->city;
				} else {
					$row_city_name = @$row->city;
				}

				return '<div class="d-flex flex-column">
                    	<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' . ucwords($row_city_name) . '">' . ucwords(substr($row_city_name, 0, 10)) . '</a>
                    	<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' . ucwords($row_state_name) . ' | ' . strtoupper($row_country_name) . '">' . ucfirst(substr($row_state_name, 0, 5)) . ' | ' . strtoupper(substr($row_country_name, 0, 2)) . '</span>
                    </div>';
			})
			->editColumn('Type', function ($row) {
				$type_name = '';
				$comission_type = $row->comission_type;
				if ($comission_type == 1) {
					$type_name = 'Free';
				} else if ($comission_type == 2) {
					$type_name = 'Paid';
				}
				return '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . @$type_name . '</a> </div>';
			})
			->editColumn('OfferBy', function ($row) use ($company_id) {
				return '<div class="d-flex flex-column"> 
							<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">........</a> 
						</div>';
			})
			->editColumn('LastActive', function ($row) {
				$updated_at = '';
				$updated_at = $newDate = date("d M , Y", strtotime($row->updated_at));
				return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">' . @$updated_at . '</span> </div>';
			})
			->editColumn('Action', function ($row) use ($jobid) {

				$recCounter = 0;
				$recCounter = DB::table('rec_candidate_assign')->select('id')->where('source_from_id', @$row->id)->where('source_from_type', 1)->where('source_jobid', @$jobid)->count();

				if ($recCounter > 0) {
					$html = '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">Added</a> </div>';
				} else {
					$html = '<div class="d-flex flex-column"> 
							<a href="javascript:void(0);" class="btn btn-sm btn-primary add_to_inreview" data-row-source_from_type="1"  data-row-id="' . @$row->id . '" data-row-jobid="' . @$jobid . '">
							Add InReview
							</a> 
							</div>';
				}
				return $html;



			})

			->rawColumns(['chkbox', 'Name', 'Contacts', 'Exp', 'Salary', 'NP', 'Qualification', 'Location', 'Type', 'OfferBy', 'LastActive', 'Action'])
			->addIndexColumn()
			->make(true);

	}
	public function public_bench($id)
	{

		$this->pageTitle = __('Candidates Recommended');

		$company_id = auth()->user()->company_id;

		$this->job = Job::findOrFail($id);
		$this->jobclient = Client::findOrFail($this->job->client_id);
		$this->custome_count = $this->custome_count($id);

		return view('product_company.apptracker.public_bench', $this->data);
	}

	public function public_bench_data(Request $request)
	{

		$company_id = auth()->user()->company_id;
		$jobid = $request->jobid;
		$job = Job::findOrFail($jobid);
		$title = $job->title;
		$min_exp = $job->min_exp;
		$max_exp = $job->max_exp;
		$min_salary = $job->min_salary;
		$max_salary = $job->max_salary;

		$loc_data = DB::table('client_location_jobs')->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')->select('client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')->where('client_location_jobs.job_id', $jobid)->first();
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

		// $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();

		$benchApplications = DB::table('job_applications')
			->select('job_applications.*', 'qualification_master.name as qualification_name', 'bench_master_status.name as master_status_name', 'bench_master_status.css_style');
		$benchApplications = $benchApplications->join('bench_master_status', 'bench_master_status.id', 'job_applications.status');
		$benchApplications = $benchApplications->join('package_active', 'package_active.company_id', 'job_applications.company_id');
		$benchApplications = $benchApplications->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification');
		$benchApplications = $benchApplications->join('jobs', 'jobs.id', 'job_applications.job_id');
		$benchApplications = $benchApplications->where('package_active.newbenchclientrequest_balance', '>', 0);
		if (\request('search') != "") {
			$search = request('search');
			$benchApplications = $benchApplications->where('job_applications.status', 2)->where('job_applications.company_id', '!=', $company_id)->where('job_applications.full_name', 'LIKE', "%$search%");
		}
		$benchApplications = $benchApplications->where(function ($query) use ($company_id, $title, $min_exp, $max_exp, $postcode) {
			return $query->where('job_applications.status', 2)->where('job_applications.company_id', '!=', $company_id)->where('job_applications.current_designation', 'LIKE', "%$title%")->where('job_applications.pincode', '=', $postcode)->whereBetween('job_applications.total_exp', [$min_exp, $max_exp]);
		});
		$benchApplications = $benchApplications->orWhere(function ($query) use ($company_id, $title) {
			return $query->where('job_applications.company_id', '!=', $company_id)->where('job_applications.status', 2)->where('job_applications.current_designation', 'LIKE', "%$title%");
		});
		foreach ($skill_arr as $val) {
			$benchApplications = $benchApplications->orWhere(function ($query) use ($company_id, $val) {
				return $query->where('job_applications.company_id', '!=', $company_id)->where('job_applications.status', 2)->where('job_applications.skills', 'like', '%' . $val . '%');

			});
		}
		// dd($job->id);							 				
		$benchApplications = $benchApplications->groupBy('job_applications.id');
		$benchApplications = $benchApplications->orderBy('job_applications.id', 'DESC');

		return DataTables::of($benchApplications)

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

				$profile_sidebar_css = 'pbench_show_application_detail';

				return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
<a href="javascript:void(0);"   data-row-id="' . @$row->id . '" class="' . $profile_sidebar_css . '">
<div class="symbol-label ' . @$bgTextDecore . ' border-radius-5">' . strtoupper(@$strVal) . '</div>
</a>
</div>                  
<div class="d-flex flex-column">
<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-dark fw-bolder text-hover-primary fs-6 ' . $profile_sidebar_css . ' " data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">' . ucwords(substr(@$row->full_name, 0, 10)) . '</a>
<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-muted fw-bold d-block fs-7 text-hover-primary ' . $profile_sidebar_css . ' " data-toggle="tooltip" title="' . ucwords(@$row->current_designation) . '">' . ucwords(substr(@$row->current_designation, 0, 10)) . '</a>
</div>';

			})
			->editColumn('Exp', function ($row) {
				return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$row->total_exp . '+</a>
                    <span class="text-muted fw-bold d-block fs-7">' . @$row->rel_exp . '+</span>
                  </div>';
			})
			->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {

				$pay_rate_currency = $bill_rate_currency = $pay_rate_amount = $bill_rate_amount = $pay_rate_mode = $bill_rate_mode = "";
				$pay_rate_currency = $currencySymbole[@$row->pay_rate_currency];
				$bill_rate_currency = $currencySymbole[@$row->bill_rate_currency];
				$pay_rate_amount = GeneralMethod::thousandsCurrencyFormat($row->pay_rate_amount);
				$bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($row->bill_rate_amount);
				return '<div class="d-flex flex-column">
						<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$pay_rate_currency . @$ctc . '/' . $pay_rate_mode . '</a>
						<span class="text-muted fw-bold d-block fs-7">' . $bill_rate_currency . @$bill_rate_amount . '/' . $bill_rate_mode . '</span>
					  </div>';
			})

			->editColumn('NP', function ($row) {
				$npArr = array('Notice period', 'Available from', 'Immediate');
				$npDate = $row->np_id;
				if ($row->serving_notice_period_mode == 0 || $row->serving_notice_period_mode == 1) {
					$originalDate = $row->np_id;
					$npDate = 'SNP LWD ' . date("d-M-y", strtotime($originalDate));
				}
				return '<div class="d-flex flex-column">
					  <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . $npArr[$row->serving_notice_period_mode] . '</a> 
					  <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' . $row->np_id . '">' . $npDate . '</span>
					</div>';
			})
			->editColumn('Qualification', function ($row) {
				return ' <div class="d-flex flex-column">
						  <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="' . @$row->qualification_name . '">' . @$row->qualification_name . '</a>
						  <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' . $row->skills . '">' . substr($row->skills, 0, 12) . '</span> 
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
						$row_state = DB::table('state_list')->select('*')->where('id', @$row->state)->first();
						$row_state_name = @$row_state->state;

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
					$row_city = DB::table('city_list')->select('*')->where('city_id', @$row->city)->first();
					$row_city_name = @$row_city->city;
				} else {
					$row_city_name = @$row->city;
				}

				return '<div class="d-flex flex-column">
                    	<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' . ucwords($row_city_name) . '">' . ucwords(substr($row_city_name, 0, 10)) . '</a>
                    	<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' . ucwords($row_state_name) . ' | ' . strtoupper($row_country_name) . '">' . ucfirst(substr($row_state_name, 0, 5)) . ' | ' . strtoupper(substr($row_country_name, 0, 2)) . '</span>
                    </div>';
			})
			->editColumn('Type', function ($row) {
				$type_name = '';
				$comission_type = $row->comission_type;
				if ($comission_type == 1) {
					$type_name = 'Free';
				} else if ($comission_type == 2) {
					$type_name = 'Paid';
				}
				return '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . @$type_name . '</a> </div>';
			})
			->editColumn('OfferBy', function ($row) use ($company_id) {
				return '<div class="d-flex flex-column"> 
							<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">........</a> 
						</div>';
			})

			->editColumn('LastActive', function ($row) {
				$updated_at = '';
				$updated_at = $newDate = date("d M , Y", strtotime($row->updated_at));
				return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">' . @$updated_at . '</span> </div>';
			})
			->editColumn('Action', function ($row) use ($jobid) {

				$recCounter = 0;
				$recCounter = DB::table('rec_candidate_assign')->select('id')->where('source_from_id', @$row->id)->where('source_from_type', 2)->where('source_jobid', @$jobid)->count();

				if ($recCounter > 0) {
					$html = '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">Added</a> </div>';
				} else {
					$html = '<div class="d-flex flex-column"> 
							<a href="javascript:void(0);" class="btn btn-sm btn-primary add_to_inreview" data-row-source_from_type="2"  data-row-id="' . @$row->id . '" data-row-jobid="' . @$jobid . '">
							Add InReview
							</a> 
							</div>';
				}
				return $html;
			})
			->rawColumns(['chkbox', 'Name', 'Contacts', 'Exp', 'Salary', 'NP', 'Qualification', 'Location', 'Type', 'OfferBy', 'LastActive', 'Action'])
			->addIndexColumn()
			->make(true);

	}



	public function custome_count($id)
	{

		$company_id = auth()->user()->company_id;


		$jobid = $id;
		$job = $this->job;

		$title = @$job->title;
		$min_exp = @$job->min_exp;
		$max_exp = @$job->max_exp;
		$min_salary = @$job->min_salary;
		$max_salary = @$job->max_salary;

		$loc_data = DB::table('client_location_jobs')->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')->select('client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')->where('client_location_jobs.job_id', $jobid)->first();
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


		//#############################   START  EXISTING CANDIDATE 
		$existingApplications = DB::table('job_applications')->select('job_applications.id')
		->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
			->join('master_status', function($join){
			   $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
		   })
		   ->join('jobs', function($join){
			   $join->on('jobs.id', 'add_job_wise_condidates.job_id');
		   })
			// ->join('jobs', 'jobs.id', 'job_applications.job_id')
			->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')
			// ->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
			->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
			->leftjoin('clients', 'clients.id', 'jobs.client_id')
			->leftjoin('users', 'users.id', 'job_applications.submitted_by')
			->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
			->leftjoin('application_status', 'application_status.id', 'job_applications.status_id')
			->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
			->leftjoin('state_list', 'state_list.id', 'job_applications.state');
		$existingApplications = $existingApplications->where(function ($query) use ($company_id, $title, $min_exp, $max_exp, $postcode, $jobid) {
			return $query->where('add_job_wise_condidates.company_id', '=', $company_id)->where('add_job_wise_condidates.master_status_id', '!=', 6)->where('add_job_wise_condidates.thirdparty_company_id', '=', 0)->where('add_job_wise_condidates.job_id', '!=', $jobid)->where('add_job_wise_condidates.existing','=',0)->where('job_applications.current_designation', 'LIKE', "%$title%")->where('job_applications.pincode', '=', $postcode)->whereBetween('job_applications.total_exp', [$min_exp, $max_exp]);
		});

		$existingApplications = $existingApplications->orWhere(function ($query) use ($company_id, $title, $jobid) {
			return $query->where('add_job_wise_condidates.company_id', '=', $company_id)->where('add_job_wise_condidates.master_status_id', '!=', 6)->where('add_job_wise_condidates.thirdparty_company_id', '=', 0)->where('add_job_wise_condidates.job_id', '!=', $jobid)->where('add_job_wise_condidates.existing','=',0)->where('job_applications.current_designation', 'LIKE', "%$title%");
		});
		foreach ($skill_arr as $val) {
			$existingApplications = $existingApplications->orWhere(function ($query) use ($company_id, $val, $jobid) {
				return $query->where('add_job_wise_condidates.company_id', '=', $company_id)->where('add_job_wise_condidates.master_status_id', '!=', 6)->where('add_job_wise_condidates.thirdparty_company_id', '=', 0)->where('add_job_wise_condidates.job_id', '!=', $jobid)->where('add_job_wise_condidates.existing','=',0)->where('job_applications.skills', 'like', '%' . $val . '%');
			});
		}
		$existingApplications = $existingApplications->orWhere(function ($query) use ($company_id, $title, $min_exp, $max_exp, $postcode, $jobid) {
			return $query->where('add_job_wise_condidates.company_id', '=', $company_id)->where('add_job_wise_condidates.master_status_id', '!=', 6)->where('add_job_wise_condidates.thirdparty_company_id', '>', 0)->where('add_job_wise_condidates.master_status_id', '!=', 8)->where('add_job_wise_condidates.job_id', '!=', $jobid)->where('add_job_wise_condidates.existing','=',0)->where('job_applications.current_designation', 'LIKE', "%$title%")->where('job_applications.pincode', '=', $postcode)->whereBetween('job_applications.total_exp', [$min_exp, $max_exp]);
		});

		$existingApplications = $existingApplications->orWhere(function ($query) use ($company_id, $title, $jobid) {
			return $query->where('add_job_wise_condidates.company_id', '=', $company_id)->where('add_job_wise_condidates.master_status_id', '!=', 6)->where('add_job_wise_condidates.thirdparty_company_id', '>', 0)->where('add_job_wise_condidates.master_status_id', '!=', 8)->where('add_job_wise_condidates.job_id', '!=', $jobid)->where('add_job_wise_condidates.existing','=',0)->where('job_applications.current_designation', 'LIKE', "%$title%");
		});

		foreach ($skill_arr as $val) {
			$existingApplications = $existingApplications->orWhere(function ($query) use ($company_id, $val, $jobid) {
				return $query->where('add_job_wise_condidates.company_id', '=', $company_id)->where('add_job_wise_condidates.master_status_id', '!=', 6)->where('add_job_wise_condidates.thirdparty_company_id', '>', 0)->where('add_job_wise_condidates.master_status_id', '!=', 8)->where('add_job_wise_condidates.job_id', '!=', $jobid)->where('add_job_wise_condidates.existing','=',0)->where('job_applications.skills', 'like', '%' . $val . '%');
			});
		}
		$existingApplications = $existingApplications->orWhere(function ($query) use ($company_id, $title, $min_exp, $max_exp, $postcode, $jobid) {
			return $query->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)->where('add_job_wise_condidates.master_status_id', '!=', 6)->where('recommendation_relation.job_id', '>', 0)->where('add_job_wise_condidates.job_id', '!=', $jobid)->where('add_job_wise_condidates.existing','=',0)->where('job_applications.current_designation', 'LIKE', "%$title%")->where('job_applications.pincode', '=', $postcode)->whereBetween('job_applications.total_exp', [$min_exp, $max_exp]);
		});

		$existingApplications = $existingApplications->orWhere(function ($query) use ($company_id, $title, $jobid) {
			return $query->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)->where('add_job_wise_condidates.master_status_id', '!=', 6)->where('recommendation_relation.job_id', '>', 0)->where('add_job_wise_condidates.existing','=',0)->where('add_job_wise_condidates.job_id', '!=', $jobid)->where('job_applications.current_designation', 'LIKE', "%$title%");
		});
		foreach ($skill_arr as $val) {
			$existingApplications = $existingApplications->orWhere(function ($query) use ($company_id, $val, $jobid) {
				return $query->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)->where('add_job_wise_condidates.master_status_id', '!=', 6)->where('recommendation_relation.job_id', '>', 0)->where('add_job_wise_condidates.existing','=',0)->where('add_job_wise_condidates.job_id', '!=', $jobid)->where('job_applications.skills', 'like', '%' . $val . '%');
			});
		}
		$existingApplications = $existingApplications->distinct('add_job_wise_condidates.id');
		$existingApplications = $existingApplications->orderBy('add_job_wise_condidates.id', 'DESC');

		//#############################   END  EXISTING CANDIDATE 

		//#############################		START INTERNAL BENCH CANDIDATE			
		$internalBenchApplications = DB::table('job_applications')->select('job_applications.id');
		$internalBenchApplications = $internalBenchApplications->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
			->join('bench_master_status', function($join){
			   $join->on('bench_master_status.id', 'add_job_wise_condidates.master_status_id');
		   });
		// $internalBenchApplications = $internalBenchApplications->join('bench_master_status', 'bench_master_status.id', 'job_applications.status');
		$internalBenchApplications = $internalBenchApplications->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification');
		$internalBenchApplications = $internalBenchApplications->where(function ($query) use ($company_id, $title, $min_exp, $max_exp, $postcode) {
			return $query->where('job_applications.status', 2)->where('add_job_wise_condidates.company_id', '=', $company_id)->where('job_applications.current_designation', 'LIKE', "%$title%")->where('job_applications.pincode', '=', $postcode)->whereBetween('job_applications.total_exp', [$min_exp, $max_exp]);
		});
		$internalBenchApplications = $internalBenchApplications->orWhere(function ($query) use ($company_id, $title) {
			return $query->where('add_job_wise_condidates.company_id', $company_id)->where('job_applications.status', 2)->where('job_applications.current_designation', 'LIKE', "%$title%");
		});
		foreach ($skill_arr as $val) {
			$internalBenchApplications = $internalBenchApplications->orWhere(function ($query) use ($company_id, $val) {
				return $query->where('add_job_wise_condidates.company_id', $company_id)->where('job_applications.status', 2)->where('job_applications.skills', 'like', '%' . $val . '%');
			});
		}
		$internalBenchApplications = $internalBenchApplications->distinct('add_job_wise_condidates.id');
		$internalBenchApplications = $internalBenchApplications->orderBy('add_job_wise_condidates.id', 'DESC');
		//#############################		END INTERNAL BENCH CANDIDATE


		//#############################		START PUBLIC BENCH 
		$publicBenchApplications = DB::table('job_applications')->select('job_applications.*', 'qualification_master.name as qualification_name', 'bench_master_status.name as master_status_name', 'bench_master_status.css_style');
		$publicBenchApplications = $publicBenchApplications->join('bench_master_status', 'bench_master_status.id', 'job_applications.status');
		$publicBenchApplications = $publicBenchApplications->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification');

		$publicBenchApplications = $publicBenchApplications->where(function ($query) use ($company_id, $title, $min_exp, $max_exp, $postcode) {
			return $query->where('job_applications.status', 2)->where('job_applications.company_id', '!=', $company_id)->where('job_applications.current_designation', 'LIKE', "%$title%")->where('job_applications.pincode', '=', $postcode)->whereBetween('job_applications.total_exp', [$min_exp, $max_exp]);
		});
		$publicBenchApplications = $publicBenchApplications->orWhere(function ($query) use ($company_id, $title) {
			return $query->where('job_applications.company_id', '!=', $company_id)->where('job_applications.status', 2)->where('job_applications.current_designation', 'LIKE', "%$title%");
		});
		foreach ($skill_arr as $val) {
			$publicBenchApplications = $publicBenchApplications->orWhere(function ($query) use ($company_id, $val) {
				return $query->where('job_applications.company_id', '!=', $company_id)->where('job_applications.status', 2)->where('job_applications.skills', 'like', '%' . $val . '%');
			});
		}
		$publicBenchApplications = $publicBenchApplications->distinct('job_applications.id');
		$publicBenchApplications = $publicBenchApplications->orderBy('job_applications.id', 'DESC');
		//#############################		END PUBLIC BENCH 		

		////////////////

		$jobApplications = JobApplication::select(
			'job_applications.*',
			'jobs.title',
			'job_locations.location',
			'application_status.status as a_status',
			'clients.name as client_name',
			'users.name as posted_by',
			'users.image as posted_by_image',
			//'notice_periods.np_title',
			'qualification_master.name as qualification_name',
			'master_status.name',
			'master_status.css_style',
			'state_list.state as state_name',
			'city_list.city as indian_city_name',
			'companies.company_name',
			'rec_candidate_assign.source_jobid',
			'rec_candidate_assign.id as rec_candidate_assign_id'
		)->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
		->join('master_status', function($join){
		   $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
	   })
	   ->join('jobs', function($join){
		   $join->on('jobs.id', 'add_job_wise_condidates.job_id');
	   })
			// ->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
			// ->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
			->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')
			->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
			->leftjoin('clients', 'clients.id', 'jobs.client_id')
			->leftjoin('users', 'users.id', 'job_applications.submitted_by')
			->leftjoin('companies', 'companies.id', 'users.company_id')
			->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
			->leftjoin('application_status', 'application_status.id', 'job_applications.status_id')
			->leftjoin('state_list', 'state_list.id', 'job_applications.state')
			->leftjoin('city_list', 'city_list.city_id', 'job_applications.city_id')
			->join('rec_candidate_assign', function ($join) use ($jobid, $company_id) {
				$join->on('rec_candidate_assign.source_application_id', '=', 'job_applications.id')
					->where('rec_candidate_assign.source_jobid', '=', $jobid)
					->where('rec_candidate_assign.source_from_type', '=', 1)
					->where('rec_candidate_assign.source_company_id', '=', $company_id);
				// ->where('rec_candidate_assign.source_from_company_id', '!=', $company_id);
			})->where('job_applications.condidate_type', '=', 'bench_condidate');

		/////////////////////

		$this->public_bench_counter = $publicBenchApplications->count();
		$this->internal_bench_counter = $internalBenchApplications->count();
		$this->existing_counter = $existingApplications->count();
		$this->recomnded_counter = $jobApplications->count();

	}



	public function benchReqViewMore($id, $job_applications_id)
	{
		abort_if(!$this->user->cans('view_jobs'), 403);

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
			'clients.name as client_name'
		);
		$this->updateTimeLine = $this->updateTimeLine->join('application_event', 'application_event.id', '=', 'updatetimeline.event_id');
		$this->updateTimeLine = $this->updateTimeLine->join('job_applications', 'job_applications.id', '=', 'updatetimeline.application_id');
		$this->updateTimeLine = $this->updateTimeLine->join('users', 'users.id', '=', 'updatetimeline.event_by_user_id');
		$this->updateTimeLine = $this->updateTimeLine->join('jobs', 'jobs.id', '=', 'updatetimeline.job_id');
		$this->updateTimeLine = $this->updateTimeLine->join('clients', 'clients.id', '=', 'jobs.client_id');
		$this->updateTimeLine = $this->updateTimeLine->where('job_applications.id', '=', $job_applications_id);
		$this->updateTimeLine = $this->updateTimeLine->where('job_applications.job_id', '=', $id);
		$this->updateTimeLine = $this->updateTimeLine->orderBy('updatetimeline.id', 'DESC');
		$this->updateTimeLine = $this->updateTimeLine->get();

		$view = view('product_company.apptracker.benchReqViewMore', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'view' => $view]);
	}

	/* ── Route stubs — prevent 500 on routes that target this controller ── */
	// These features are not yet implemented for the Product Company persona.
	// Each returns 404 rather than throwing a fatal PHP error.

	public function dsimilar_candidates($id = null) { abort(404); }
	public function dinternal_bench($id = null) { abort(404); }
	public function update_sub_stage($status = null, $application_id = null, $job_id = null, $parent_status = null) { abort(404); }
	public function sub_stage(Request $request) { abort(404); }
	public function save_rejected_resion(Request $request) { abort(404); }
	public function download($id4 = null) { abort(404); }
	public function ratingSave(Request $request) { abort(404); }
	public function viewDetails(Request $request) { abort(404); }
	public function createSchedule($id2 = null) { abort(404); }
	public function storeSchedule(Request $request) { abort(404); }
	public function storeScheduleWithEmailTempate(Request $request) { abort(404); }
	public function jobQuestion($jobID = null) { abort(404); }
	public function clientappdata() { abort(404); }
	public function dsimilar_existing_data(Request $request) { abort(404); }
	public function dinternal_bench_data(Request $request) { abort(404); }
	public function table() { abort(404); }
	public function updateIndex(Request $request) { abort(404); }
	public function statusupdate(Request $request) { abort(404); }
	public function get_client_job(Request $request) { abort(404); }
	public function showInterviews($id = null) { abort(404); }

}