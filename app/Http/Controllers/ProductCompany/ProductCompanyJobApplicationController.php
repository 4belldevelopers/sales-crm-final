<?php

/***########################################################
Controller    	: 	AdminJobApplicationController 
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/

namespace App\Http\Controllers\ProductCompany;

use App\Models\ApplicationSetting;
use App\Models\ApplicationStatus;
use App\Models\Company;
use App\Exports\JobApplicationExport;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\InterviewSchedule\StoreRequest;
use App\Http\Requests\StoreJobApplication;
use App\Http\Requests\UpdateJobApplication;
use App\Models\InterviewSchedule;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobApplicationAnswer;
use App\Models\JobLocation;
use App\Models\AddJobWiseCondidate;
//use App\JobLocation;
use App\Notifications\CandidateScheduleInterview;
use App\Notifications\CandidateStatusChange;
use App\Notifications\ScheduleInterview;
use App\Models\Skill;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Models\NoticePeriod;
use App\Helper\GeneralMethod;
use App\Models\Country;

use App\Helper\Zoom;
use App\Helper\Google;

class ProductCompanyJobApplicationController extends ProductCompanyBaseController
{

	public function __construct()
	{
		parent::__construct();
		$this->pageTitle = 'menu.jobApplications';
		$this->pageIcon = 'icon-user';
	}
	public function create($jobId = null, $appId = null)
	{

		abort_if(!$this->user->cans('add_job_applications'), 403);

		$this->pageTitle = __('Add Candidate');
		$packageStatus = GeneralMethod::validatePackageRet('CVACCESS');
		if ($packageStatus != 1) {
			$url = url('product_company/apptracker/' . @$jobId . '/all');
			return redirect($url);
		}

		$company_id = '';
		$company_id = auth()->user()->company_id;
		$candidateCount = JobApplication::count();

		$this->jobs = Job::activeJobs();
		$this->gender = ['male' => __('modules.front.male'), 'female' => __('modules.front.female'), 'others' => __('modules.front.others')];
		$chkAdminRole = $this->user->hasRole('admin');
		// $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
		if ($chkAdminRole > 0) {
			$this->jobs = Job::select('jobs.id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city')->join('clients', 'clients.id', 'jobs.client_id')->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->where('jobs.status', '=', 'active')->where('jobs.company_id', '=', $this->user->company_id)->groupBy('jobs.id')->get();
		} else {
			$this->jobs = Job::select('jobs.id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city')->join('job_assign', 'job_assign.job_id', 'jobs.id')->join('clients', 'clients.id', 'jobs.client_id')->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->where('jobs.status', '=', 'active')->where('job_assign.user_id', '=', $this->user->id)->where('jobs.company_id', '=', $this->user->company_id)->groupBy('jobs.id')->get();
		}

		$this->qualification = DB::table('qualification_master')->select('*')->orderBy('qualification_master.display_order', 'ASC')->get();
		$this->jobId = $jobId;
		$this->appId = $appId;

		$this->jobDetail = Job::select('jobs.id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'jobs.min_exp', 'jobs.max_exp', 'jobs.status', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city')->join('clients', 'clients.id', 'jobs.client_id')->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->where('jobs.id', '=', $jobId)->where('jobs.company_id', '=', $this->user->company_id)->first();

		$this->page_event = 'ADD';
		$this->currency = GeneralMethod::currency();
		$this->salaryMode = GeneralMethod::salaryMode();
		$this->statelist = GeneralMethod::statelist(101);

		$this->job_additional_detail = "";
		$this->job_additional_detail = DB::table('job_additional_detail')->select('*')->where('job_additional_detail.company_id', '=', $company_id)->where('job_additional_detail.job_id', '=', $this->jobId)->orderBy('job_additional_detail.id', 'ASC')->get();

		$this->jobLocation = DB::table('client_location_jobs')->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')->select('client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name', 'state_list.state as state_name')->where('client_location_jobs.job_id', $this->jobId)->orderBy('client_location_jobs.id', 'DESC')->first();

		$this->countries = GeneralMethod::getAllCountryList();
		$this->statelist = '';
		$this->cityListArr = '';

		return view('product_company.job-applications.create', $this->data);

	}
	public function statelist($cid)
	{

		$statelist = GeneralMethod::statelist($cid);
		if ($cid == 101) {

			$Rawstatelist = DB::table('state_list')->select('state_list.id', 'state_list.state as name', DB::raw("101 as country_id"))->orderBy('state_list.state')->get()->toArray();
			$statelist = json_decode(json_encode($Rawstatelist), true);
		}
		$custom = '<option>Select State</option>';
		foreach ($statelist as $sval) {
			$custom .= '<option value="' . $sval['id'] . '">' . $sval['name'] . '</option>';
		}
		return Reply::dataOnly(['status' => 'success', 'data' => $custom]);
	}
	public function store(Request $request)
	{

		abort_if(!$this->user->cans('add_job_applications'), 403);

		$company_id = '';
		$company_id = auth()->user()->company_id;

		$packageStatus = GeneralMethod::validatePackageRet('CVACCESS');
		if ($packageStatus != 1) {
			$url = url('product_company/apptracker/' . @$request->job_id . '/all');
			return redirect($url);
		}

		$applicationStatus = ApplicationStatus::first();
		$jobApplication = new JobApplication();
		$jobApplication->full_name = $request->full_name;
		$jobApplication->job_id = $request->job_id;
		$jobApplication->status_id = 8;

		$jobApplication->email = $request->email;
		$jobApplication->phone = $request->phone;
		$jobApplication->column_priority = 0;
		$jobApplication->total_exp = $request->total_exp;
		$jobApplication->rel_exp = $request->rel_exp;

		$jobApplication->ctc = $request->ctc;
		$jobApplication->current_ctc_cur = $request->current_ctc_cur;
		$jobApplication->current_ctc_mode = $request->current_ctc_mode;
		$jobApplication->ectc = $request->ectc;
		$jobApplication->current_ectc_cur = $request->current_ectc_cur;

		$jobApplication->current_ectc_mode = $request->current_ectc_mode;
		$jobApplication->percentage_hike = $request->percentage_hike;
		$jobApplication->comment = $request->comment;
		$jobApplication->pincode = $request->pincode;
		$jobApplication->current_designation = $request->current_designation;

		$jobApplication->current_company = $request->current_company;

		$cnt = 0;
		$skillStr = '';
		$skillArr = json_decode($request->skills, true);
		if (!empty($skillArr)) {
			foreach ($skillArr as $sval) {
				if ($cnt == 0) {
					$skillStr .= $sval['value'];
				} else {
					$skillStr .= ' , ' . $sval['value'];
				}
				$cnt++;
			}
		}
		$jobApplication->skills = $skillStr;
		$jobApplication->master_status_id = 8;
		$jobApplication->secondrary_email = $request->secondrary_email;
		$jobApplication->secondary_mobile = $request->secondary_mobile;
		$submitted_by = '';
		$submitted_by = auth()->user()->id;

		if (!empty($submitted_by)) {
			$jobApplication->submitted_by = $submitted_by;
		}

		$serving_notice_period = '';
		$serving_notice_period = $request->serving_notice_period;
		if ($serving_notice_period == 1) {
			$jobApplication->np_id = 'SNP-LWD-' . $request->np_id;
		} else if ($serving_notice_period == 2) {
			$jobApplication->np_id = 'Immediate';
		} else {
			$jobApplication->np_id = $request->ddl_np_id;
		}

		if (!empty($request->qualification)) {
			$jobApplication->qualification = $request->qualification;
		}
		$jobApplication->company_id = $this->user->company_id;
		$jobApplication->serving_notice_period_mode = $request->serving_notice_period;
		if ($request->has('gender')) {
			$jobApplication->gender = $request->gender;
		}
		if ($request->has('dob')) {
			$jobApplication->dob = $request->dob;
		}

		$jobApplication->country = $request->country;
		$jobApplication->state = $request->state;

		if ($jobApplication->country == 101) {
			$jobApplication->city_id = $request->city_id;
		} else {
			$jobApplication->city = $request->city;
		}

		if ($request->hasFile('photo')) {
			$jobApplication->photo = Files::upload($request->photo, 'candidate-photos');
		}


		$returnCount = 0;
		$message = 'messages.createdSuccessfully';
		$company_id = $this->user->roles[0]->company_id;
		$app_duplicate = $this->user->company->app_duplicate;
		$app_duplicate_days = $this->user->company->app_duplicate_days;
		$applicationEmail = $request->email;
		if (($this->user->company->app_duplicate == 1) || ($this->user->company->app_duplicate == 2)) {
			$returnCount = GeneralMethod::checkDuplicateCandidate($company_id, $app_duplicate, $request->job_id, $applicationEmail, $app_duplicate_days);
			if ($returnCount > 0 && $app_duplicate == 1) {
				$message = 'Duplicate :: application already in this job';
			} else if ($returnCount > 0 && $app_duplicate == 2) {
				$message = 'Duplicate :: application already in system';
			}
		}
		$chkApplication = DB::table('job_applications')
			->select('job_applications.id')
			->where('company_id', $company_id)
			->where('email', $applicationEmail)
			->orWhere('phone', $request->phone)
			->count();
		if ($chkApplication > 0) {
			$jobApplication->duplicate = 1;
		}
		$jobApplication->save();

		$insertArr = array('job_id' => $request->job_id, 'job_application_id' =>$jobApplication->id,'status_id' =>$jobApplication->status_id,'master_status_id' => $jobApplication->master_status_id,'child_status_id' =>$jobApplication->child_status_id,'company_id' =>$company_id,'submitted_by' =>$submitted_by);
		 DB::table('add_job_wise_condidates')->insert($insertArr);

		// Check job Assign Or Not
		$chkJobAssign = DB::table('job_assign')->select('job_assign.id')->where('company_id', $company_id)->where('job_id', $request->job_id)->where('user_id', $submitted_by)->count();
		if ($chkJobAssign < 1) {
			$values = array('company_id' => @$company_id, 'user_id' => @$submitted_by, 'job_id' => @$request->job_id);
			DB::table('job_assign')->insert($values);
		}
		if ($request->hasFile('resume')) {
			$hashname = Files::upload($request->resume, 'documents/' . $jobApplication->id, null, null, false);
			$jobApplication->documents()->create(['name' => 'Resume', 'hashname' => $hashname]);
		}


		if (isset($request->jobskill) && !empty($request->jobskill)) {
			DB::table('candidate_skills_value')->where('job_id', $request->job_id)->where('candidate_id', $jobApplication->id)->delete();
			foreach ($request->jobskill as $key => $value) {
				if (!empty(@$value)) {
					$values = array('job_id' => @$request->job_id, 'skill_id' => @$key, 'candidate_id' => $jobApplication->id, 'skill_value' => @$value, );
					DB::table('candidate_skills_value')->insert($values);
				}
			}
		}
		$status_name = $note = '';
		$status_cnt = DB::table('candidate_status')->select('*')->where('company_id', user()->company_id)->where('parent', $applicationStatus->id)->count();
		if ($status_cnt > 0) {
			//echo '<pre>';print_r($applicationStatus->name); exit;
			$status_row = DB::table('candidate_status')->select('*')->where('company_id', user()->company_id)->where('parent', $applicationStatus->id)->orderBy('priority', 'ASC')->first();
			$status_name = $status_row->name;
		}
		$insert_type = 0;


		$timelineArr = array(
			'event_id' => 1,
			'event_by_company_id' => auth()->user()->company_id,
			'event_by_user_id' => auth()->user()->id,
			'master_process_name' => 'InReview',
			//'child_process_name'	=>	

			'application_id' => @$jobApplication->id,
			'application_email' => @$request->email,
			'job_id' => @$request->job_id,
			'note' => 'Successfully Added'
		);
		GeneralMethod::UpdateTimeline($timelineArr);



		// Job Application Answer save
		if (isset($request->answer) && !empty($request->answer)) {
			foreach ($request->answer as $key => $value) {
				$answer = new JobApplicationAnswer();
				$answer->job_application_id = $jobApplication->id;
				$answer->job_id = $request->job_id;
				$answer->question_id = $key;
				$answer->answer = $value;
				$answer->save();
			}
		}

		$job_additional_detail_count = DB::table('job_additional_detail')->select('*')->where('job_id', $request->job_id)->orderBy('job_additional_detail.id', 'ASC')->count();
		$job_additional_detail = DB::table('job_additional_detail')->select('*')->where('job_id', $request->job_id)->orderBy('job_additional_detail.id', 'ASC')->get();
		if ($job_additional_detail_count > 0) {
			$inc = 0;
			foreach ($job_additional_detail as $jd) {


				$values = array(
					'job_id' => @$request->job_id,
					'question_id' => @$jd->id,
					'application_id' => @$jobApplication->id,
					'job_detail_value' => @$jd->detail_value,
					'application_detail_value' => @$request->additional_detail[$inc]
				);
				DB::table('application_additional_detail')->insert($values);
				$inc++;

			}
		}

		if (@$jobApplication->id > 0) {
			$event_name = 'CVACCESS';
			$event_table_name = 'job_applications';
			$event_table_id = @$jobApplication->id;
			$packageStatus = GeneralMethod::packageRedeem($event_name, $event_table_name, $event_table_id);
		}

		if (!empty($request->job_id)) {
			return Reply::redirect(url('/product_company/apptracker/' . $request->job_id . '/all'), __('menu.jobApplications') . ' ' . __($message));
		} else {
			return Reply::redirect(route('product_company.job-applications.table'), __('menu.jobApplications') . ' ' . __($message));
		}
	}
	public function show($id)
	{
		$company_id = '';
		$company_id = auth()->user()->company_id;

		$this->currency = GeneralMethod::currency();
		$this->salaryMode = GeneralMethod::salaryMode();

		$this->intModeArr = GeneralMethod::interview_mode();
		$this->application = JobApplication::select(
			'job_applications.id',
			'add_job_wise_condidates.submitted_by',
			'add_job_wise_condidates.company_id',
			'job_applications.np_id',
			'job_applications.serving_notice_period_mode',
			'job_applications.full_name',
			'job_applications.email',
			'job_applications.phone',
			'job_applications.secondary_mobile',
			'job_applications.photo',
			'job_applications.resume',
			'job_applications.rating',
			'job_applications.skype_id',
			'job_applications.cover_letter',
			'add_job_wise_condidates.job_id',
			'job_applications.status_id',
			'job_applications.is_viewed',
			'job_applications.column_priority',
			'job_applications.created_at',
			'job_applications.updated_at',
			'job_applications.deleted_at',
			'job_applications.total_exp',
			'job_applications.rel_exp',
			'job_applications.passport',
			'job_applications.current_location',
			'job_applications.current_designation',
			'job_applications.current_company',
			'job_applications.process_id',
			'job_applications.qualification',
			'add_job_wise_condidates.master_status_id',
			'add_job_wise_condidates.child_status_id',
			'job_applications.secondrary_email',
			'job_applications.secondary_mobile',
			'job_applications.skills',
			'job_applications.city',
			'job_applications.country',
			'job_applications.pincode',
			'job_applications.state',
			'job_applications.current_ctc_cur',
			'job_applications.ctc',
			'job_applications.current_ctc_mode',
			'job_applications.current_ectc_cur',
			'job_applications.ectc',
			'job_applications.current_ectc_mode',
			'job_applications.percentage_hike',
			'job_applications.comment',
			'job_applications.dob',
			'job_applications.created_at',
			'clients.name as client_name',
			'jobs.title',
			'users.name as posted_by',
			'users.image as posted_by_image',
			'master_status.name as master_status_name',
			'offer_job_applications.proposed_ctc',
			'offer_job_applications.any_offer',
			'offer_job_applications.document',
			'offer_job_applications.sub_for_approval',
			'offer_job_applications.offer_approved',
			'offer_job_applications.offer_release',
			'offer_job_applications.offer_acceptance',
			'offer_job_applications.joined',
			'offer_job_applications.joined_date',
			'offer_job_applications.offer_amount',
			'qualification_master.name as qualification_name',
			'state_list.state as state_name',
			'companies.company_name',
			'city_list.city as city_name'
		)
		->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
		->join('master_status', function($join){
			$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
		})
		->leftJoin('jobs', function($join){
			$join->on('jobs.id', 'add_job_wise_condidates.job_id');
		})
		->leftJoin('companies', function($join){
			$join->on('companies.id', 'add_job_wise_condidates.company_id');
		})
		->leftJoin('offer_job_applications', function($join) use ($id, $company_id){
			$join->on('offer_job_applications.application_id', 'job_applications.id');
			$join->where('offer_job_applications.application_id', $id);
			$join->where('offer_job_applications.company_id', $company_id);
		})
			// ->leftJoin('jobs', 'jobs.id', '=', 'job_applications.job_id')
			->leftjoin('clients', 'clients.id', 'jobs.client_id', )
			->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
			->leftjoin('users', 'users.id', 'job_applications.submitted_by')
			->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
			// ->leftJoin('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
			->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
			->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id');
		// $this->application = $this->application->where('job_applications.id', $id);
		$this->application = $this->application->where('add_job_wise_condidates.job_application_id', $id);
		$this->application = $this->application->where('add_job_wise_condidates.company_id', $company_id);
		$this->application = $this->application->first();


		$this->client_contuser_arr = DB::table('client_contact_info_jobs')->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')->join('clients', 'clients.id', '=', 'client_contact_info.client_id')->select('client_contact_info.contact_person_name', 'client_contact_info.contact_person_email', 'client_contact_info.contact_person_number', 'client_contact_info.contact_person_designation', 'clients.name')->where('client_contact_info_jobs.job_id', $this->application->job_id)->get();

		$this->job_additional_detail = "";
		$this->job_additional_detail = DB::table('job_additional_detail')->select('*')->where('job_additional_detail.company_id', '=', $company_id)->where('job_additional_detail.job_id', '=', @$this->application->job_id)->orderBy('job_additional_detail.id', 'ASC')->get();

		$this->application_additional_detail = DB::table('application_additional_detail')->select('*')->where('application_additional_detail.application_id', '=', $this->application->id)->where('application_additional_detail.job_id', '=', @$this->application->job_id)->orderBy('application_additional_detail.id', 'ASC')->get();

		if (!empty($this->application->qualification) && $this->application->qualification > 0) {
			$this->qualification = DB::table('qualification_master')->select('name')->where('id', $this->application->qualification)->first();
		}
		$this->answers = JobApplicationAnswer::with(['question'])->where('job_id', $this->application->job_id)->where('job_application_id', $this->application->id)->get();

		$this->notesArr = DB::table('applicant_notes')->select('applicant_notes.id', 'applicant_notes.note_text', 'applicant_notes.created_at', 'applicant_notes.updated_at', 'users.name', 'users.image as posted_by_image')->join('users', 'users.id', '=', 'applicant_notes.user_id')->where('job_application_id', $this->application->id)->where('applicant_notes.user_id', auth()->user()->id)->orderBy('id', 'ASC')->get();

		$this->schedule = DB::table('interview_schedules')->select('interview_schedules.id', 'interview_schedules.company_id', 'interview_schedules.job_application_id', 'interview_schedules.schedule_date', 'interview_schedules.status', 'interview_schedules.user_accept_status', 'interview_schedules.created_at', 'interview_schedules.updated_at')->where('job_application_id', $this->application->id)->orderBy('id', 'DESC')->get();

		$application_email = $this->application->email;
		$this->application_email = $application_email;
		$application_id = $this->application->id;
		$this->application_id = $application_id;
		$job_id = $this->application->job_id;
		$this->job_id = $job_id;

		$this->timeline = DB::table('timeline');
		$this->timeline = $this->timeline->select('timeline.id', 'timeline.owner_id', 'timeline.application_id', 'timeline.application_email', 'timeline.job_id', 'timeline.process_id', 'timeline.status_name', 'timeline.process_child_name', 'timeline.note', 'timeline.visible', 'timeline.insert_type', 'timeline.create_at', 'timeline.interview_schedule_date', 'timeline.interview_from_time', 'timeline.interview_to_time', 'timeline.interview_mode', 'timeline.interview_mention_link', 'timeline.interviewers', 'timeline.interview_additional_email', 'timeline.interview_message', 'jobs.id as job_id', 'jobs.title', 'master_status.name as master_status_name', 'clients.name as client_name', 'timeline.timeline_type', 'timeline.prev_process_name', 'timeline.prev_process_child_name', 'timeline.interview_mode_child_level', 'timeline.average_star_rating', 'timeline.interview_schedules_id', 'master_status.css_style', 'users.name', 'users.image');
		$this->timeline = $this->timeline->join('job_applications', 'job_applications.id', '=', 'timeline.application_id');
		$this->timeline = $this->timeline->leftJoin('users', 'users.id', '=', 'timeline.owner_id');
		$this->timeline = $this->timeline->leftJoin('jobs', 'jobs.id', '=', 'timeline.job_id');
		$this->timeline = $this->timeline->leftJoin('clients', 'clients.id', '=', 'jobs.client_id');
		$this->timeline = $this->timeline->leftJoin('master_status', 'master_status.id', '=', 'timeline.process_id');
		$this->timeline = $this->timeline->where('timeline.application_id', '=', $application_id);
		$this->timeline = $this->timeline->where('timeline.job_id', '=', $job_id);
		$this->timeline = $this->timeline->orderBy('timeline.id', 'DESC');
		$this->timeline = $this->timeline->get();

		$this->created_at = GeneralMethod::ymdTodmy($this->application->created_at);

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

		// $this->updateTimeLine = $this->updateTimeLine->where('job_applications.id', '=', $application_id);
		// $this->updateTimeLine = $this->updateTimeLine->where('job_applications.company_id', '=', auth()->user()->company_id);

		$this->updateTimeLine = $this->updateTimeLine->where('updatetimeline.application_id', '=', $id);
		$this->updateTimeLine = $this->updateTimeLine->where('updatetimeline.event_by_company_id', '=', auth()->user()->company_id);
		$this->updateTimeLine = $this->updateTimeLine->where('updatetimeline.job_id', '=', @$this->application->job_id);


		$this->updateTimeLine = $this->updateTimeLine->orderBy('updatetimeline.id', 'DESC');
		$this->updateTimeLine = $this->updateTimeLine->get();

		$this->child_status_name = 'N/A';
		if ($this->application->child_status_id > 0) {
			$childStatus = DB::table('candidate_status')->select('*')->where('id', $this->application->child_status_id)->first();
			if (!empty($childStatus)) {
				$this->child_status_name = @$childStatus->name;
			}
		}
		$this->company_id = $this->user->company_id;
		$this->additional_skills_arr = DB::table('skills')->join('candidate_skills_value', 'skills.id', '=', 'candidate_skills_value.skill_id')->select('skills.name', 'candidate_skills_value.skill_value')->where('candidate_skills_value.job_id', $this->application->job_id)->where('candidate_skills_value.candidate_id', $this->application->id)->where('skills.company_id', $this->user->company_id)->get();

		$this->application_add_detail_cnt = DB::table('application_additional_detail')->select('*')->where('application_id', $this->application->id)->where('job_id', $this->application->job_id)->orderBy('application_additional_detail.id', 'ASC')->count();
		$this->application_add_detail = DB::table('application_additional_detail')->select('*')->where('application_id', $this->application->id)->where('job_id', $this->application->job_id)->orderBy('application_additional_detail.id', 'ASC')->get();

		$this->offer_job_applications_count = DB::table('offer_job_applications')->select('offer_job_applications.id')->join('jobs', 'jobs.id', '=', 'offer_job_applications.job_id')->join('clients', 'clients.id', '=', 'jobs.client_id')->where('offer_job_applications.email', '=', $this->application->email)->where('offer_job_applications.company_id', '=', auth()->user()->company_id)->orderBy('offer_job_applications.id', 'DESC')->count();

		$this->offer_job_applications = DB::table('offer_job_applications')->select('offer_job_applications.id', 'offer_job_applications.application_id', 'offer_job_applications.submitted_by', 'offer_job_applications.company_id', 'offer_job_applications.np_id', 'offer_job_applications.full_name', 'offer_job_applications.email', 'offer_job_applications.phone', 'offer_job_applications.gender', 'offer_job_applications.dob', 'offer_job_applications.country', 'offer_job_applications.state', 'offer_job_applications.city', 'offer_job_applications.secondrary_email', 'offer_job_applications.secondary_mobile', 'offer_job_applications.skills', 'offer_job_applications.photo', 'offer_job_applications.resume', 'offer_job_applications.rating', 'offer_job_applications.skype_id', 'offer_job_applications.cover_letter', 'offer_job_applications.job_id', 'offer_job_applications.status_id', 'offer_job_applications.is_viewed', 'offer_job_applications.column_priority', 'offer_job_applications.created_at', 'offer_job_applications.updated_at', 'offer_job_applications.total_exp', 'offer_job_applications.rel_exp', 'offer_job_applications.passport', 'offer_job_applications.ctc', 'offer_job_applications.ectc', 'offer_job_applications.current_location', 'offer_job_applications.current_designation', 'offer_job_applications.current_company', 'offer_job_applications.process_id', 'offer_job_applications.qualification', 'offer_job_applications.master_status_id', 'offer_job_applications.child_status_id', 'offer_job_applications.serving_notice_period_mode', 'offer_job_applications.proposed_ctc', 'offer_job_applications.any_offer', 'offer_job_applications.document', 'offer_job_applications.sub_for_approval', 'offer_job_applications.offer_approved', 'offer_job_applications.offer_release', 'offer_job_applications.offer_acceptance', 'offer_job_applications.joined', 'offer_job_applications.joined_date', 'offer_job_applications.duplicate', 'jobs.title', 'clients.name as client_name')->join('jobs', 'jobs.id', '=', 'offer_job_applications.job_id')->join('clients', 'clients.id', '=', 'jobs.client_id')->where('offer_job_applications.email', '=', $this->application->email)->where('offer_job_applications.company_id', '=', auth()->user()->company_id)->orderBy('offer_job_applications.id', 'DESC')->get();

		$resume = '';
		$resumeData = DB::table('documents')->select('*')->where('documentable_id', $this->application->id)->where('name', '=', 'Resume')->first();
		$this->resume = @$resumeData->hashname;



		$sel_app_id = $id;
		$sel_job_id = $job_id;
		$this->benchStatusCnt = DB::table('rec_candidate_assign')->where('source_application_id', '=', $sel_app_id)->where('source_jobid', '=', $sel_job_id)->where('source_from_type', '!=', 0)->count();
		if ($this->benchStatusCnt > 0) {
			$benchAssignRow = DB::table('rec_candidate_assign')->select('rec_candidate_assign.*')->where('source_application_id', '=', $sel_app_id)->first();

			$source_from_company_id = @$benchAssignRow->source_from_company_id;
			$source_from_user_id = @$benchAssignRow->source_from_user_id;
			$source_from_id = @$benchAssignRow->source_from_id;
			$source_from_type = @$benchAssignRow->source_from_type;
			$source_company_id = @$benchAssignRow->source_company_id;

			$source_user_id = @$benchAssignRow->source_user_id;
			$source_application_id = @$benchAssignRow->source_application_id;
			$source_jobid = @$benchAssignRow->source_jobid;

			if ($source_from_type == 2) {
				$this->application->phone = $this->application->secondary_mobile = 'xxx-xxx-xxxx';
				$this->application->email = $this->application->secondrary_email = 'anonymous@email.com';
			}

			$this->bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile')->join('users', 'users.id', '=', 'bench_assign.user_id')->where('bench_assign.bench_applications_id', '=', $benchAssignRow->source_from_id)->get();

			$this->bench_additional_detail = DB::table('bench_additional_detail')->select('bench_additional_detail.id', 'bench_additional_detail.custom_keys', 'bench_additional_detail.custom_keys_value')->where('bench_additional_detail.bench_id', '=', $benchAssignRow->source_from_id)->get();
		}

		$view = view('product_company.job-applications.showdetail', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'view' => $view]);
	}

	public function edit($id)
	{
		$this->pageTitle = __('Edit Candidate');
		abort_if(!$this->user->cans('edit_job_applications'), 403);

		$company_id = '';
		$company_id = auth()->user()->company_id;
		$this->statuses = ApplicationStatus::all();
		$this->application = JobApplication::find($id);
		$this->jobQuestion = $this->application->job->questions;
		$this->jobs = Job::select('id', 'title', 'location_id', 'status', 'start_date', 'end_date', 'section_visibility')->get();
		$this->noticeperiod = NoticePeriod::all();
		$this->qualification = DB::table('qualification_master')->select('*')->orderBy('qualification_master.display_order', 'ASC')->get();
		$this->interviewStatus = DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();
		$this->childArr = DB::table('candidate_status')->select('*')->where('parent', $this->application->master_status_id)->where('company_id', $this->user->company_id)->orderBy('priority', 'ASC')->get();

		$this->company_id = $this->user->company_id;
		$this->application_add_detail_cnt = DB::table('application_additional_detail')->select('*')->where('application_id', $this->application->id)->orderBy('application_additional_detail.id', 'ASC')->count();

		$this->application_add_detail = DB::table('application_additional_detail')->select('*')->where('application_id', $this->application->id)->orderBy('application_additional_detail.id', 'ASC')->get();

		$this->jobId = $this->application->job_id;
		$this->appId = $this->application->id;

		$this->jobDetail = Job::select('jobs.id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'jobs.min_exp', 'jobs.max_exp', 'jobs.status', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city')->join('clients', 'clients.id', 'jobs.client_id')->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->where('jobs.id', '=', $this->jobId)->where('jobs.company_id', '=', $this->user->company_id)->first();

		$this->page_event = 'EDIT';
		$this->currency = GeneralMethod::currency();
		$this->salaryMode = GeneralMethod::salaryMode();
		$this->countries = GeneralMethod::getAllCountryList();
		$this->statelist = GeneralMethod::statelist($this->application->country);
		$this->documents = DB::table('documents')->select('*')->where('documentable_id', $this->application->id)->where('name', 'Resume')->first();

		$this->job_additional_detail = "";
		$this->job_additional_detail = DB::table('job_additional_detail')->select('*')->where('job_additional_detail.company_id', '=', $company_id)->where('job_additional_detail.job_id', '=', $this->jobId)->orderBy('job_additional_detail.id', 'ASC')->get();

		$this->application_additional_detail = DB::table('application_additional_detail')->select('*')->where('application_additional_detail.application_id', '=', $this->application->id)->where('application_additional_detail.job_id', '=', $this->jobId)->orderBy('application_additional_detail.id', 'ASC')->get();

		$this->cityListArr = '';
		if ($this->application->country == 101) {
			$statelist = DB::table('state_list')->select('state_list.id', 'state_list.state as name', DB::raw("101 as country_id"))->orderBy('state_list.state')->get()->toArray();
			$this->statelist = json_decode(json_encode($statelist), true);

			$citylist = DB::table('city_list')->select('city_list.city_id as id', 'city_list.city as name')->where('city_list.state_id', $this->application->state)->orderBy('city_list.city')->get()->toArray();
			$this->cityListArr = json_decode(json_encode($citylist), true);

		}

		return view('product_company.job-applications.create', $this->data);
	}
	public function update(Request $request, $id)
	{


		abort_if(!$this->user->cans('edit_job_applications'), 403);

		$jobApplication = JobApplication::with(['documents'])->findOrFail($id);
		$jobApplication->full_name = $request->full_name;

		!empty($request->current_designation) ? $jobApplication->current_designation = @$request->current_designation : NULL;
		!empty($request->current_company) ? $jobApplication->current_company = @$request->current_company : NULL;
		!empty($request->phone) ? $jobApplication->phone = @$request->phone : NULL;
		!empty($request->secondary_mobile) ? $jobApplication->secondary_mobile = @$request->secondary_mobile : NULL;
		!empty($request->dob) ? $jobApplication->dob = @$request->dob : NULL;

		!empty($request->email) ? $jobApplication->email = @$request->email : NULL;
		!empty($request->secondrary_email) ? $jobApplication->secondrary_email = @$request->secondrary_email : NULL;
		!empty($request->qualification) ? $jobApplication->qualification = @$request->qualification : NULL;
		!empty($request->total_exp) ? $jobApplication->total_exp = @$request->total_exp : NULL;
		!empty($request->rel_exp) ? $jobApplication->rel_exp = @$request->rel_exp : NULL;


		$serving_notice_period = '';
		$serving_notice_period = $request->serving_notice_period;
		if ($serving_notice_period == 1) {
			$jobApplication->np_id = $request->np_id;
		} else if ($serving_notice_period == 2) {
			$jobApplication->np_id = 'Immediate';
		} else {
			$jobApplication->np_id = $request->ddl_np_id;
		}
		$jobApplication->serving_notice_period_mode = $request->serving_notice_period;
		$jobApplication->current_ctc_cur = $request->current_ctc_cur;
		if (!empty($request->ctc)) {
			$jobApplication->ctc = @$request->ctc;
		}
		$jobApplication->current_ctc_mode = $request->current_ctc_mode;
		$jobApplication->current_ectc_cur = $request->current_ectc_cur;
		if (!empty($request->ectc)) {
			$jobApplication->ectc = @$request->ectc;
		}
		$jobApplication->current_ectc_mode = @$request->current_ectc_mode;
		$jobApplication->percentage_hike = @$request->percentage_hike;
		$jobApplication->comment = @$request->comment;

		$jobApplication->country = @$request->country;
		$jobApplication->state = @$request->state;
		if ($jobApplication->country == 101) {
			$jobApplication->city_id = $request->city_id;
		} else {
			$jobApplication->city = $request->city;
		}
		$jobApplication->pincode = @$request->pincode;

		$cnt = 0;
		$skillStr = '';
		$skillArr = json_decode($request->skills, true);
		if (!empty($skillArr)) {
			foreach ($skillArr as $sval) {
				if ($cnt == 0) {
					$skillStr .= $sval['value'];
				} else {
					$skillStr .= ' , ' . $sval['value'];
				}
				$cnt++;
			}
		}
		$jobApplication->skills = $skillStr;

		if ($request->hasFile('photo')) {
			$jobApplication->photo = Files::upload($request->photo, 'candidate-photos');
		}
		$jobApplication->save();


		$mailSetting = ApplicationSetting::select('id', 'mail_setting')->first()->mail_setting;

		$status_name = $note = '';
		$status_cnt = DB::table('candidate_status')->select('*')->where('company_id', $this->user->company_id)->where('parent', $request->status_id)->count();
		if ($status_cnt > 0) {
			$status_row = DB::table('candidate_status')->select('*')->where('company_id', $this->user->company_id)->where('parent', $request->status_id)->where('id', @$request->childStatus)->first();
		}
		if ($request->hasFile('resume')) {
			$hashname = Files::upload($request->resume, 'documents/' . $jobApplication->id, null, null, false);
			$jobApplication->documents()->updateOrCreate(['documentable_type' => JobApplication::class, 'documentable_id' => $jobApplication->id, 'name' => 'Resume'], ['hashname' => $hashname]);
		}
		if (isset($request->answer) && count($request->answer) > 0) {
			foreach ($request->answer as $key => $value) {
				JobApplicationAnswer::updateOrCreate(['job_application_id' => $jobApplication->id, 'job_id' => $jobApplication->job_id, 'question_id' => $key], ['answer' => $value]);
			}
		}

		DB::table('application_additional_detail')->where('application_id', $jobApplication->id)->delete();

		$job_additional_detail_count = DB::table('job_additional_detail')->select('*')->where('job_id', $jobApplication->job_id)->orderBy('job_additional_detail.id', 'ASC')->count();
		$job_additional_detail = DB::table('job_additional_detail')->select('*')->where('job_id', $jobApplication->job_id)->orderBy('job_additional_detail.id', 'ASC')->get();
		if ($job_additional_detail_count > 0) {
			$inc = 0;
			foreach ($job_additional_detail as $jd) {


				$values = array(
					'job_id' => @$jobApplication->job_id,
					'question_id' => @$jd->id,
					'application_id' => @$jobApplication->id,
					'job_detail_value' => @$jd->detail_value,
					'application_detail_value' => @$request->additional_detail[$inc]
				);
				DB::table('application_additional_detail')->insert($values);
				$inc++;

			}
		}

		return Reply::redirect(url('/product_company/apptracker/' . $jobApplication->job_id . '/all'), __('menu.jobApplications') . ' ' . __('messages.createdSuccessfully'));
	}
	public function createSchedule(Request $request, $id)
	{
		abort_if(!$this->user->cans('add_schedule'), 403);

		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();
		$this->candidates = JobApplication::all();

		$this->users = User::with('company')->company()->where('users.type', '<>', 1)->get();
		$this->scheduleDate = $request->date;
		$this->currentApplicant = JobApplication::findOrFail($id);
		$this->interview_node = GeneralMethod::interview_mode();
		$this->application_id = $this->interview_id = $this->feedback_id = $this->interview_detail = '';
		$this->application_id = $id;
		$this->interview_detail = array();
		$this->application = JobApplication::select(
			'job_applications.*',
			'clients.name as client_name',
			'jobs.title as job_title',
			'client_location.city',
			'jobs.company_id',
			'master_status.name as master_status_name',
			'master_status.css_style as master_status_style',
			'users.email as submitted_by_email',
			'users.name as submitted_by_name',
			'users.company_id as submitted_by_company_id',
			'qualification_master.name as qualification_name',
			'state_list.state as state_name',
			'users.name as posted_by',
			'users.image as posted_by_image'
		)->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
		->join('master_status', function ($join) {
			$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
		})
		->join('jobs', function ($join) {
			$join->on('jobs.id', 'add_job_wise_condidates.job_id');
		})
			// ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
			->join('clients', 'clients.id', '=', 'jobs.client_id')
			// ->join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
			->join('users', 'users.id', '=', 'job_applications.submitted_by')
			->leftjoin('client_location_jobs', 'client_location_jobs.job_id', 'jobs.id')
			->leftjoin('client_location', 'client_location.id', 'client_location_jobs.client_location_id')
			->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
			->leftjoin('state_list', 'state_list.id', 'job_applications.state')
			->where('job_applications.id', $this->application_id)
			->groupBy('job_applications.id')->first();

		$country = $this->row_country_name = $this->row_state_name = $this->row_city_name = '';
		if (empty($this->application->country)) {
			$this->row_country_name = '----';
		} else {
			if (!empty($this->application->country)) {
				if (is_numeric($this->application->country)) {
					$country_name_Arr = GeneralMethod::getCountryName($this->application->country);
					foreach ($country_name_Arr as $cval) {
						$this->row_country_name = ucwords($cval['name']);
					}
				}
			}
		}

		if (empty($this->application->state)) {
			$this->row_state_name = '----';
		} else {
			if ($this->application->country == 101) {
				$this->row_state_name = $this->application->state_name;
			} else {
				$state = $this->application->state;
				if (is_numeric($this->application->country) && is_numeric($state)) {
					if ($state > 0) {
						$state_name_Arr = GeneralMethod::getStateName($this->application->country, $state);
						foreach ($state_name_Arr as $sval) {
							$this->row_state_name = ucwords($sval['name']);
						}
					}
				}
			}
		}

		if ($this->application->country == 101) {
			$row_city = DB::table('city_list')->select('*')->where('city_id', $this->application->city_id)->first();
			$this->row_city_name = @$row_city->city;
		} else {
			$this->row_city_name = $this->application->city;
		}

		$current_ctc_cur = $current_ectc_cur = $ctc = $ectc = $current_ctc_mode = $current_ectc_mode = $this->child_status_name = '';
		$current_ctc_cur = $currencySymbole[$this->application->current_ctc_cur];
		$current_ectc_cur = $currencySymbole[$this->application->current_ectc_cur];
		$ctc = GeneralMethod::thousandsCurrencyFormat($this->application->ctc);
		$ectc = GeneralMethod::thousandsCurrencyFormat($this->application->ectc);
		$current_ctc_mode = substr($salaryMode[$this->application->current_ctc_mode], 0, 1);
		$current_ectc_mode = substr($salaryMode[$this->application->current_ectc_mode], 0, 1);
		$this->current_ctc = @$current_ctc_cur . @$ctc . '/' . @$current_ctc_mode;
		$this->expected_ctc = @$current_ectc_cur . @$ectc . '/' . @$current_ectc_mode;

		$submitted_by_company_id = $this->application->submitted_by_company_id;
		$submitted_by_row = DB::table('companies')->select('*')->where('companies.id', $submitted_by_company_id)->first();
		$this->submitted_by_company = $submitted_by_row->company_name;

		if ($this->application->child_status_id) {
			$child_status_by_row = DB::table('candidate_status')->select('*')->where('candidate_status.id', $this->application->child_status_id)->first();
			$this->child_status_name = $child_status_by_row->name;
		}

		$this->interviewStatus = DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();
		$this->client_contact_info = DB::table('jobs')->join('client_contact_info_jobs', 'client_contact_info_jobs.job_id', '=', 'jobs.id')->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')->select('client_contact_info.id', 'client_contact_info.contact_person_name', 'client_contact_info.contact_person_email')->where('client_contact_info_jobs.job_id', '=', $this->application->job_id)->orderBy('client_contact_info.contact_person_name', 'ASC')->first();

		$this->job_id = $this->application->job_id;
		$this->interview_mode = DB::table('interview_mode')->select('*')->orderBy('interview_mode.id', 'ASC')->get();
		$this->email_template = DB::table('th_email_template')->select('*')->orderBy('th_email_template.e_temp_id', 'ASC')->get();
		$this->from_email = $this->user->email;
		$this->cc_email = $this->user->email;
		if ($this->application->submitted_by_email != $this->user->email) {
			$this->cc_email = $this->cc_email . ' , ' . $this->application->submitted_by_email;
		}

		$client_contact_arr = DB::table('client_contact_info')->join('client_contact_info_jobs', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')->select('client_contact_info.contact_person_name', 'client_contact_info.contact_person_email')->where('client_contact_info_jobs.job_id', $this->application->job_id)->get();
		if (!empty($client_contact_arr)) {
			foreach ($client_contact_arr as $ccData) {
				if (!empty($ccData->contact_person_email)) {
					$this->cc_email = $this->cc_email . ' , ' . $ccData->contact_person_email;
				}
			}
		}
		$this->reschedule = @$_REQUEST['reschedule'];
		$view = view('product_company.job-applications.interview-create', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'view' => $view]);

	}
	public function download($id)
	{
		$resume = '';
		$resumeData = DB::table('documents')->select('*')->where('documentable_id', $id)->where('name', '=', 'Resume')->first();
		$resume = $resumeData->hashname;
		$company = user()->company_id;
		$jobApplication = JobApplication::findOrFail($id);
		$path = public_path() . '/user-uploads/documents/' . $jobApplication->id . '/' . $resume;
		if (!file_exists($path)) {
			abort(404);
		}
		return response()->download($path);
	}
	public function getinterviewchild(Request $request)
	{
		$id = $job_id = '';
		$id = @$request->id;
		$job_id = @$request->job_id;
		$Data = DB::table('interview_mode_lavel')->select('*')->where('interview_mode_id', $id)->get();

		$companyData = $this->user->company;

		if (!empty($Data)) {
			if ($id == 1) {
				$html = '<select class="form-control" name="interview_mode_lavel" id="interview_mode_lavels" data-style="" onchange="getVideoMeetingUrl(this.value, ' . $id . ');">
													<option value="">Select Mode</option>';
				foreach ($Data as $cval) {
					$html .= '<option value= "' . $cval->id . '">' . ucfirst($cval->name) . '</option>';
					$flag = 1;
				}
				$html .= '</select>';
			} else if ($id == 2) {
				$html = '<select class="form-control" name="interview_mode_lavel" id="interview_mode_lavels" data-style="" onchange="getVideoMeetingUrl(this.value, ' . $id . ',' . $job_id . ');">
													<option value="">Select Mode</option>';
				foreach ($Data as $cval) {
					if ($cval->id == 2) {  // Google Meeting
						if ($companyData->google_status == 1) {
							$html .= '<option value= "' . $cval->id . '">' . ucfirst($cval->name) . '</option>';
						}
					} else if ($cval->id == 3) {  // Zoom Meeting
						if ($companyData->zoom_status == 1) {
							$html .= '<option value= "' . $cval->id . '">' . ucfirst($cval->name) . '</option>';
						}
					} else if ($cval->id == 5) {  // Microsoft Meeting
						if ($companyData->microsoft_status == 1) {
							$html .= '<option value= "' . $cval->id . '">' . ucfirst($cval->name) . '</option>';
						}
					} else {
						$html .= '<option value= "' . $cval->id . '">' . ucfirst($cval->name) . '</option>';
					}
					$flag = 1;
				}
				$html .= '</select>';
			} else if ($id == 3) {

				$Data = DB::table('client_location')
					->select('client_location.id', 'client_location.address', 'client_location.city', 'client_location.postcode')
					->join('client_location_jobs', 'client_location_jobs.client_location_id', '=', 'client_location.id')
					->where('client_location_jobs.job_id', $job_id)
					->orderBy('client_location.id', 'ASC')
					->get();

				$html = '<select class="form-control" name="interview_mode_lavel" id="interview_mode_lavels" data-style="" onchange="getVideoMeetingUrl(this.value, ' . $id . ');">
													<option value="">Select Address</option>';
				foreach ($Data as $cval) {
					$html .= '<option value= "' . @$cval->id . '">' . ucfirst(@$cval->address) . '  ' . ucfirst(@$cval->city) . '  ' . @$cval->postcode . '</option>';
					$flag = 1;
				}
				$html .= '</select>';
			}
		} else {
			$html = '&nbsp;-&nbsp;';
		}
		return Reply::dataOnly(['status' => 'success', 'data' => $html]);
	}

	public function recruiter($id)
	{
		$panellist = '';
		$panellist = $_REQUEST['panellist'];
		$this->panellistArr = explode(',', $panellist);

		$company_id = auth()->user()->company_id;
		$this->company = DB::table('companies')->select('*')->where('id', $company_id)->first();

		$company_id = auth()->user()->company_id;
		$this->team = DB::table('users')->leftjoin('role_user', 'role_user.user_id', 'users.id')->leftjoin('roles', 'roles.id', 'role_user.role_id')->select('users.id', 'users.name', 'users.email', 'roles.display_name', 'users.working_status')->where('type', 0)->where('users.company_id', $company_id)->where('status', 'active')->get();

		$view = view('product_company.job-applications.recruiter', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'view' => $view]);

	}
	public function storeSchedule(Request $request)
	{

		$application_id = $interview_type = $interviewDate = $master_status_id = $child_status_id = $interview_mode = $interview_mode_lavel = $interviewers = $panellist = '';
		$application_id = @$request->application_id;
		$interview_type = @$request->interview_type;
		$interviewDate = @$request->interviewDate;
		$interview_mode = @$request->interview_mode;
		$interview_mode_lavel = @$request->interview_mode_lavel;
		$master_status_id = @$request->master_status_id;
		$child_status_id = @$request->child_status_id;
		$interviewers = @$request->interviewers;
		$panellist = @$request->panellist;

		// INTERVIEW SCHEDULE
		$interviewSchedule = new InterviewSchedule();
		$interviewSchedule->job_application_id = $application_id;
		if (!empty($request->interview_type)) {
			$interviewSchedule->reschedule = $interview_type;
		}
		if (!empty($interview_mode)) {
			$interviewSchedule->interview_mode = $interview_mode;
		}
		if (!empty($interview_mode_lavel)) {
			$interviewSchedule->interview_mode_lavel = $interview_mode_lavel;
		}
		if (!empty($master_status_id)) {
			$interviewSchedule->stage = $master_status_id;
		}
		if (!empty($child_status_id)) {
			$interviewSchedule->substage = $child_status_id;
		}
		if (!empty($panellist)) {
			$interviewSchedule->interviewers = $panellist;   //implode(',', $panellist);
		}
		if (!empty($interview_created_by)) {
			$interviewSchedule->interview_created_by = $this->user->id;
		}
		$interviewSchedule->schedule_date = $interviewDate;
		$interviewSchedule->save();

		$interviewSchedule_id = '';
		$interviewSchedule_id = $interviewSchedule->id;

		$jobApplication = JobApplication::find($request->application_id);
		$date = date('Y-m-d H:i:s');
		$insert_type = $request->interview_type;
		$statusRow = ApplicationStatus::findOrFail($request->master_status_id);
		$interview_mode_lavel = '';
		$interview_mode_lavel = @$request->interview_mode_lavel;
		if ($interview_mode_lavel > 0) {
			$intModeLbl = DB::table('interview_mode_lavel')->select('interview_mode_lavel.name')->where('interview_mode_lavel.id', $interview_mode_lavel)->first();
			$interview_mode_lavel = $intModeLbl->name;
		}

		$date = date('Y-m-d H:i:s');
		$values = array(
			'event_id' => 3,
			'event_by_company_id' => auth()->user()->company_id,
			'event_by_user_id' => auth()->user()->id,

			'application_id' => @$request->application_id ? @$request->application_id : NULL,
			'application_email' => @$jobApplication->email ? @$jobApplication->email : NULL,
			'job_id' => @$jobApplication->job_id ? @$jobApplication->job_id : NULL,
			'note' => 'Interview Schedule',
			'create_at' => @$date ? @$date : NULL,

			'update_at' => @$date ? @$date : NULL,

			'interview_schedule_date' => @$request->interviewDate,
			'interview_mode' => $interview_mode,
			'interview_mode_child_level' => $interview_mode_lavel,
		);

		if ($jobApplication->master_status_id > 0) {
			$masterStatus = DB::table('master_status')->select('master_status.name')->where('id', $jobApplication->master_status_id)->first();
			$values['master_process_name'] = @$masterStatus->name;
		}
		if ($jobApplication->child_status_id > 0) {
			$masterStatus = DB::table('candidate_status')->select('candidate_status.name')->where('candidate_status.id', $jobApplication->child_status_id)->first();
			$values['child_process_name'] = @$masterStatus->name;
		}
		if (!empty(@$comment)) {
			$values['note'] = @$comment;
		}
		if (!empty($request->url) && $request->url != '') {
			$values['interview_mention_link'] = @$request->url;
		}
		if (!empty($request->panellist) && $request->panellist != '') {
			$values['interviewers'] = @$request->panellist;
		}
		$values['interview_schedules_id'] = $interviewSchedule_id;

		GeneralMethod::UpdateTimeline($values);
		return Reply::dataOnly(['status' => 'success', 'message' => 'Interview Reschedule successfully !']);

	}
	public function accessEmailTemplate(Request $request)
	{
		$template_id = $application_id = $template_subject = $job_title = $client_name = $job_id = '';
		$email = $template_description = $full_name = $cc_email = $submitted_by_email = $to_email = '';
		$template_id = @$request->template_id;
		$application_id = @$request->application_id;
		$email = $this->user->email;

		$applicationRow = DB::table('job_applications')->join('jobs', 'jobs.id', '=', 'job_applications.job_id')->join('clients', 'clients.id', '=', 'jobs.client_id')->join('users', 'users.id', '=', 'job_applications.submitted_by')->select('jobs.id as job_id', 'jobs.title', 'clients.name as client_name', 'job_applications.full_name', 'users.email as submitted_by_email', 'job_applications.email as to_email')->where('job_applications.id', $application_id)->first();

		$job_title = "(" . $applicationRow->title . ")";
		$client_name = "(" . $applicationRow->client_name . ")";
		$full_name = $applicationRow->full_name;
		$job_id = $applicationRow->job_id;

		$dataRow = DB::table('th_email_template')->select('*')->where('th_email_template.e_temp_id', $template_id)->first();
		$template_subject = $dataRow->template_subject;
		$template_subject = str_replace("[{JOB_TITLE}]", $job_title, $template_subject);
		$template_subject = str_replace("[{COMPANY_NAME}]", $client_name, $template_subject);
		$template_description = $dataRow->template_description;
		$template_description = str_replace("[{JOB_TITLE}]", $job_title, $template_description);
		$template_description = str_replace("[{COMPANY_NAME}]", $client_name, $template_description);
		$template_description = str_replace("[{CANDIDATE_NAME}]", $full_name, $template_description);

		$videoUrl = '';
		if (!empty(@$request->interview_mode)) {
			if (@$request->interview_mode == 1) {
				$videoUrl = 'Interview Mode Phone Call';
			}
			if (@$request->interview_mode == 2) {
				$videoUrl = @$request->videoUrl;
				$videoUrl = 'Zoom meeting  Url  :' . $videoUrl;
			}
			if (@$request->interview_mode == 3) {
				$videoUrl = @$request->videoUrl;
				$videoUrl = 'Interview Location :' . $videoUrl;
			}
		}

		$template_description = str_replace("[{VIDEOURL}]", $videoUrl, $template_description);

		$to_email = $applicationRow->to_email;
		$cc_email = $this->user->email;
		if ($applicationRow->submitted_by_email != $this->user->email) {
			$cc_email = $cc_email . ' , ' . $applicationRow->submitted_by_email;
		}
		$client_contact_arr = DB::table('client_contact_info')->join('client_contact_info_jobs', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')->select('client_contact_info.contact_person_name', 'client_contact_info.contact_person_email')->where('client_contact_info_jobs.job_id', $job_id)->get();
		if (!empty($client_contact_arr)) {
			foreach ($client_contact_arr as $ccData) {
				if (!empty($ccData->contact_person_email)) {
					$cc_email = $cc_email . ' , ' . $ccData->contact_person_email;
				}
			}
		}
		return Reply::dataOnly(['status' => 'success', 'to_email' => $to_email, 'cc_email' => $cc_email, 'from_email' => $email, 'template_subject' => $template_subject, 'template_description' => $template_description]);

	}

	public function emailSchedule(Request $request)
	{



		$application_id = $master_status_id = $child_status_id = $interview_type = $interviewDate = $interview_mode = $interview_mode_lavel = $panellist = $interviewers = '';
		$email_template = $from_email = $to_email = $cc_email = $subject = $editor = '';
		$application_id = @$request->application_id;
		$master_status_id = @$request->master_status_id;
		$child_status_id = @$request->child_status_id;
		$interview_type = @$request->interview_type;
		$interviewDate = @$request->interviewDate;
		$interview_mode = @$request->interview_mode;
		$interview_mode_lavel = @$request->interview_mode_lavel;
		$panellist = @$request->panellist;

		$email_template = @$request->email_template;
		$from_email = @$request->from_email;
		$to_email = @$request->to_email;
		$cc_email = @$request->cc_email;
		$subject = @$request->subject;
		$editor = @$request->editor;

		// INTERVIEW SCHEDULE
		$interviewSchedule = new InterviewSchedule();
		$interviewSchedule->job_application_id = $application_id;
		if (!empty($master_status_id)) {
			$interviewSchedule->stage = $master_status_id;
		}
		if (!empty($child_status_id)) {
			$interviewSchedule->substage = $child_status_id;
		}
		if (!empty($request->interview_type)) {
			$interviewSchedule->reschedule = $interview_type;
		}
		$interviewSchedule->schedule_date = $interviewDate;
		if (!empty($interview_mode)) {
			$interviewSchedule->interview_mode = $interview_mode;
		}
		if (!empty($interview_mode_lavel)) {
			$interviewSchedule->interview_mode_lavel = $interview_mode_lavel;
		}
		if (!empty($panellist)) {
			$interviewSchedule->interviewers = $panellist;   //implode(',', $panellist);
		}
		if (!empty($interview_created_by)) {
			$interviewSchedule->interview_created_by = $this->user->id;
		}
		$interviewSchedule->save();

		$interviewSchedule_id = '';
		$interviewSchedule_id = $interviewSchedule->id;

		$jobApplication = JobApplication::find($request->application_id);
		$date = date('Y-m-d H:i:s');
		$insert_type = $request->interview_type;
		$statusRow = ApplicationStatus::findOrFail($request->master_status_id);
		$values = array(
			'owner_id' => auth()->user()->id,
			'application_id' => @$request->application_id,
			'application_email' => @$jobApplication->email,
			'job_id' => @$jobApplication->job_id,
			'process_id' => @$request->master_status_id,
			'status_name' => @$statusRow->name,

			'note' => 'Interview Schedule',
			'insert_type' => @$insert_type,
			'create_at' => @$date,
			'update_at' => @$date,
			'interview_schedule_date' => @$interviewDate,
			'interview_mode' => $interview_mode,
			'timeline_type' => 1,

		);

		if (!empty($request->url) && $request->url != '') {
			$values['interview_mention_link'] = @$request->url;
		}
		if (!empty($request->panellist) && $request->panellist != '') {
			$values['interviewers'] = @$request->panellist;
		}
		if ($request->child_status_id > 0) {
			$childStatus = '';
			$childStatus = DB::table('candidate_status')->select('*')->where('company_id', '=', $this->user->company_id)->where('id', $request->child_status_id)->first();
			$values['process_child_name'] = @$childStatus->name;
		}
		$values['interview_schedules_id'] = $interviewSchedule_id;

		DB::table('timeline')->insert($values);

		$from_name = "Vibhanshu";
		$from_mail = "vibhanshu@4bel.com";
		$replyto = "support@4bell.com";
		$uid = md5(uniqid(time()));
		$message = $editor;
		$mailto = "abu@4bell.com";

		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		// More headers
		$headers .= 'From: $from_mail' . "\r\n";
		$headers .= 'Cc: $replyto' . "\r\n";
		mail($mailto, $subject, $message, $headers);
		// END SEND EMAIL		        
		return Reply::dataOnly(['status' => 'success', 'message' => 'Interview Reschedule successfully !']);

	}
	public function stage_body_content($id)
	{

		$current_company_id = auth()->user()->company_id;
		$this->qualification = $this->application = '';
		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();

		$this->intModeArr = GeneralMethod::interview_mode();
		$this->application = JobApplication::select(
			'job_applications.id',
			'job_applications.submitted_by',
			'job_applications.company_id',
			'job_applications.np_id',
			'job_applications.serving_notice_period_mode',
			'job_applications.full_name',
			'job_applications.email',
			'job_applications.phone',
			'job_applications.secondary_mobile',
			'job_applications.photo',
			'job_applications.resume',
			'job_applications.rating',
			'job_applications.skype_id',
			'job_applications.cover_letter',
			'job_applications.job_id',
			'job_applications.status_id',
			'job_applications.is_viewed',
			'job_applications.column_priority',
			'job_applications.created_at',
			'job_applications.updated_at',
			'job_applications.deleted_at',
			'job_applications.total_exp',
			'job_applications.rel_exp',
			'job_applications.passport',
			'job_applications.current_ctc_cur',
			'job_applications.current_ectc_cur',
			'job_applications.current_ctc_mode',
			'job_applications.current_ectc_mode',
			'job_applications.ctc',
			'job_applications.ectc',
			'job_applications.current_location',
			'job_applications.current_designation',
			'job_applications.current_company',
			'job_applications.process_id',
			'job_applications.qualification',
			'add_job_wise_condidates.master_status_id',
			'add_job_wise_condidates.child_status_id',
			'job_applications.secondrary_email',
			'job_applications.secondary_mobile',
			'job_applications.skills',
			'clients.name as client_name',
			'jobs.title',
			'users.name as posted_by',
			'master_status.name as master_status_name',
			'job_applications.proposed_ctc',
			'job_applications.any_offer',
			'job_applications.document',
			'job_applications.sub_for_approval',
			'job_applications.offer_approved',
			'job_applications.offer_release',
			'job_applications.offer_acceptance',
			'job_applications.joined',
			'job_applications.joined_date',
			'job_applications.city',
			'job_applications.state',
			'job_applications.country',
			'job_applications.city_id',
			'state_list.state as state_name',
			'users.name as submitted_by_name',
			'users.company_id as submitted_by_company_id',
			'add_job_wise_condidates.thirdparty_company_id',
			'add_job_wise_condidates.id as job_wise_condidates_id'

		)
			->leftJoin('jobs', 'jobs.id', '=', 'job_applications.job_id')
			->leftjoin('clients', 'clients.id', 'jobs.client_id')
			->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
			->leftjoin('users', 'users.id', 'job_applications.submitted_by')
			->leftjoin('state_list', 'state_list.id', 'job_applications.state')
			// ->leftJoin('master_status', 'master_status.id', '=', 'job_applications.status_id');
			->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
			->join('master_status', function($join){
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
			});
			// dd("l");
		$this->application = $this->application->where('add_job_wise_condidates.id', $id);
		$this->application = $this->application->first();

		if (!empty($this->application->qualification) && $this->application->qualification > 0) {
			$this->qualification = DB::table('qualification_master')->select('name')->where('id', $this->application->qualification)->first();
		}

		$submitted_by_company_id = $this->application->submitted_by_company_id;
		$this->company = DB::table('companies')->select('company_name')->where('id', $submitted_by_company_id)->first();

		$this->master_status = DB::table('master_status')->select('*')->where('master_status.custom_client', '=', 1)->orderBy('master_status.priority', 'ASC')->get();

		$master_status_id = $this->application->master_status_id;
		$this->candidate_status_Arr = DB::table('candidate_status')->select('*')->where('parent', $master_status_id)->where('company_id', $current_company_id)->orderBy('candidate_status.priority', 'ASC')->get();

		$this->schedule = DB::table('interview_schedules')->select('interview_schedules.id', 'interview_schedules.company_id', 'interview_schedules.job_application_id', 'interview_schedules.schedule_date', 'interview_schedules.status', 'interview_schedules.user_accept_status', 'interview_schedules.created_at', 'interview_schedules.updated_at')->where('job_application_id', $this->application->id)->orderBy('id', 'DESC')->get();

		$application_email = $this->application->email;
		$this->application_email = $application_email;
		$application_id = $this->application->id;
		$this->application_id = $application_id;
		$job_id = $this->application->job_id;
		$this->job_id = $job_id;

		$this->created_at = GeneralMethod::ymdTodmy($this->application->created_at);
		$this->child_status_name = 'N/A';

		if ($this->application->child_status_id > 0) {
			$childStatus = DB::table('candidate_status')->select('*')->where('id', $this->application->child_status_id)->first();
			if (!empty($childStatus)) {
				$this->child_status_name = @$childStatus->name;
			}
		}
		$this->company_id = $this->user->company_id;
		$current_ctc_cur = $current_ectc_cur = $ctc = $ectc = $current_ctc_mode = $current_ectc_mode = $country = $this->row_country_name = $this->row_city_name = '';
		$current_ctc_cur = $currencySymbole[$this->application->current_ctc_cur];
		$current_ectc_cur = $currencySymbole[$this->application->current_ectc_cur];
		$ctc = GeneralMethod::thousandsCurrencyFormat($this->application->ctc);
		$ectc = GeneralMethod::thousandsCurrencyFormat($this->application->ectc);
		$current_ctc_mode = substr($salaryMode[$this->application->current_ctc_mode], 0, 1);
		$current_ectc_mode = substr($salaryMode[$this->application->current_ectc_mode], 0, 1);
		$this->current_ctc = @$current_ctc_cur . @$ctc . '/' . @$current_ctc_mode;
		$this->expected_ctc = @$current_ectc_cur . @$ectc . '/' . @$current_ectc_mode;

		if (empty($this->application->country)) {
			$this->row_country_name = '----';
		} else {
			if (!empty($this->application->country)) {
				if (is_numeric($this->application->country)) {
					$country_name_Arr = GeneralMethod::getCountryName($this->application->country);
					foreach ($country_name_Arr as $cval) {
						$this->row_country_name = ucwords($cval['name']);
					}
				}
			}
		}

		$this->row_state_name = '';
		if (empty($this->application->state)) {
			$this->row_state_name = '----';
		} else {
			if ($this->application->country == 101) {
				$this->row_state_name = $this->application->state_name;
			} else {
				$state = $this->application->state;
				if (is_numeric($this->application->country) && is_numeric($state)) {
					if ($state > 0) {
						$state_name_Arr = GeneralMethod::getStateName($this->application->country, $state);
						foreach ($state_name_Arr as $sval) {
							$this->row_state_name = ucwords($sval['name']);
						}
					}
				}
			}
		}

		if ($this->application->country == 101) {
			$row_city = DB::table('city_list')->select('*')->where('city_id', $this->application->city_id)->first();
			$this->row_city_name = @$row_city->city;
		} else {
			$this->row_city_name = $this->application->city;
		}

		$view = view('product_company.job-applications.stage_body_content', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'view' => $view]);
	}

	public function child_stage($id)
	{
		$company_id = $html = '';
		$company_id = auth()->user()->company_id;

		$master_status_id = $id;
		$candidate_status_Arr = DB::table('candidate_status')->select('*')->where('parent', $master_status_id)->where('company_id', $company_id)->orderBy('candidate_status.priority', 'ASC')->get();

		if (!empty($candidate_status_Arr)) {
			$cnt = 1;
			foreach ($candidate_status_Arr as $cstatus) {

				$html .= '<div class="div' . $cnt . ' d-inline-block w-14 px-2">
                            <input type="radio" class="btn-check" name="sub_stages" value="' . @$cstatus->id . '" id="screening' . $cnt . '">
                            <label
                                class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                for="screening' . $cnt . '"> <span class="d-block fw-bold text-start"> <span
                                        class="text-dark-800 fw-bolder d-block fs-5">' . @$cstatus->name . '</span> </span>
                            </label>
                        </div>';
				$cnt++;
			}
		}
		return Reply::dataOnly(['status' => 'success', 'html' => $html]);
	}
	public function stage_submit(Request $request)
	{

		$company_id = "";
		$company_id = auth()->user()->company_id;

		$application_id = $master_status = $sub_stages = $comment = $addJobWiseCondidate_id = "";
		$prev_process_name = $prev_process_child_name = '';
		$application_id = @$request->application_id;
		$addJobWiseCondidate_id = @$request->job_wise_condidates_id;
		$master_status = @$request->master_status;
		$sub_stages = @$request->sub_stages;
		$comment = @$request->comment;

		$jobApplication = JobApplication::find($application_id);
		$addJobWiseCondidate = AddJobWiseCondidate::find($addJobWiseCondidate_id);
		if (!empty(@$master_status)) {
			if ($jobApplication->master_status_id >= 0 || $addJobWiseCondidate->master_status_id >= 0) {
				$masterStatus = DB::table('master_status')->select('master_status.name')->where('id', $addJobWiseCondidate->master_status_id)->first();
				$prev_process_name = @$masterStatus->name;
			}
			$jobApplication->master_status_id = @$master_status;
			$addJobWiseCondidate->master_status_id = @$master_status; //1
		}

		if ($jobApplication->child_status_id > 0 || $addJobWiseCondidate->child_status_id > 0) {
			$subProcess = DB::table('candidate_status')->select('candidate_status.name')->where('candidate_status.company_id', $company_id)->where('candidate_status.id', $addJobWiseCondidate->child_status_id)->first();
			$prev_process_child_name = @$subProcess->name;
		}


		if (!empty(@$sub_stages)) {
			$jobApplication->child_status_id = @$sub_stages;
			$addJobWiseCondidate->child_status_id = @$sub_stages; //2
		} else {
			$jobApplication->child_status_id = '';
			$addJobWiseCondidate->child_status_id = '';
		}


		$date = date('Y-m-d H:i:s');
		$values = array(
			'event_id' => 2,
			'event_by_company_id' => auth()->user()->company_id,
			'event_by_user_id' => auth()->user()->id,
			'application_id' => @$application_id ? @$application_id : NULL,
			'application_email' => @$jobApplication->email ? @$jobApplication->email : NULL,
			'job_id' => @$addJobWiseCondidate->job_id ? @$addJobWiseCondidate->job_id : NULL,
			'note' => '',
			'create_at' => @$date ? @$date : NULL,

			'update_at' => @$date ? @$date : NULL,
			'prev_process_name' => $prev_process_name ? $prev_process_name : NULL,
			'prev_process_child_name' => $prev_process_child_name ? $prev_process_child_name : NULL
		);


		if (@$master_status > 0) {
			$masterStatus = DB::table('master_status')->select('master_status.name')->where('id', $master_status)->first();
			$values['master_process_name'] = @$masterStatus->name;
		}
		if (@$sub_stages > 0) {
			$masterStatus = DB::table('candidate_status')->select('candidate_status.name')->where('candidate_status.company_id', $company_id)->where('candidate_status.id', $sub_stages)->first();
			$values['child_process_name'] = @$masterStatus->name;
		}
		if (!empty(@$comment)) {
			$values['note'] = @$comment;
		}

		$joined_date = '';
		if ((@$master_status > 0) && @$master_status == 6) {
			if (!empty($jobApplication->joined_date)) {
				$joined_date = date("d-M-Y", strtotime($jobApplication->joined_date));
				$values['note'] = 'Applicant joining date :: &nbsp;' . $joined_date;
			}
		}

		GeneralMethod::UpdateTimeline($values);

		// $jobApplication->save();
		$addJobWiseCondidate->save();

		if ($master_status == 5) {

			$rowData = DB::table('job_applications')->select('*')->where('id', $application_id)->first();
			$rowDataOffer = DB::table('offer_job_applications')->select('*')->where('application_id', $application_id)->where('job_id', $addJobWiseCondidate->job_id)->first();
			$values = array(
				'application_id' => $rowData->id,
				'submitted_by' => $rowData->submitted_by,
				'company_id' => $rowData->company_id,
				'np_id' => $rowData->np_id,
				'full_name' => $rowData->full_name,

				'email' => $rowData->email,
				'phone' => $rowData->phone,
				'gender' => $rowData->gender,
				'dob' => $rowData->dob,
				'country' => $rowData->country,

				'state' => $rowData->state,
				'city' => $rowData->city,
				'secondrary_email' => $rowData->secondrary_email,
				'secondary_mobile' => $rowData->secondary_mobile,
				'skills' => $rowData->skills,

				'photo' => $rowData->photo,
				'resume' => $rowData->resume,
				'rating' => $rowData->rating,
				'skype_id' => $rowData->skype_id,
				'cover_letter' => $rowData->cover_letter,
				
				'job_id' => $addJobWiseCondidate->job_id,
				'status_id' => $rowData->status_id,
				'is_viewed' => $rowData->is_viewed,
				'column_priority' => $rowData->column_priority,
				'total_exp' => $rowData->total_exp,

				'rel_exp' => $rowData->rel_exp,
				'passport' => $rowData->passport,
				'ctc' => $rowData->ctc,
				'ectc' => $rowData->ectc,
				'current_location' => $rowData->current_location,

				'current_designation' => $rowData->current_designation,
				'current_company' => $rowData->current_company,
				'process_id' => $rowData->process_id,
				'qualification' => $rowData->qualification,
				'master_status_id' => $rowData->master_status_id,

				'child_status_id' => $rowData->child_status_id,
				'serving_notice_period_mode' => $rowData->serving_notice_period_mode,
				'proposed_ctc' => $rowData->proposed_ctc,
				'any_offer' => $rowData->any_offer,
				'document' => $rowData->document,

				'sub_for_approval' => $rowData->sub_for_approval,
				'offer_approved' => $rowData->offer_approved,
				'offer_release' => $rowData->offer_release,
				'offer_acceptance' => $rowData->offer_acceptance,
				'joined' => $rowData->joined,

				'created_at' => date('Y-m-d'),
				'updated_at' => date('Y-m-d')

			);
			if(empty($rowDataOffer)){
				DB::table('offer_job_applications')->insert($values);
			}
			
		}

		$finalJobId = (int) ($addJobWiseCondidate->job_id ?? $jobApplication->job_id ?? 0);
		$finalCompanyId = (int) ($addJobWiseCondidate->company_id ?? $company_id ?? auth()->user()->company_id ?? 0);
		if ($finalJobId > 0 && $finalCompanyId > 0) {
			app(\App\Services\Apptracker\CounterService::class)->onStageMoved($finalJobId, (int) $application_id, (int) $master_status, $finalCompanyId);
		}

		return Reply::dataOnly(['status' => 'success', 'message' => 'Application stage update successfully !']);

	}






}
