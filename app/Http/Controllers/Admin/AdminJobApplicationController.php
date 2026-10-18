<?php

/***########################################################

Controller    	: 	AdminJobApplicationController
Purmose			:
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021

########################################################***/

namespace App\Http\Controllers\Admin;

use App\Exports\JobApplicationExport;
use App\Helper\Files;
use App\Helper\GeneralMethod;
use App\Helper\Google;
use App\Helper\Reply;
use App\Helper\Zoom;
use App\Http\Requests\InterviewSchedule\StoreRequest;
use App\Http\Requests\StoreJobApplication;
use App\Models\ApplicationSetting;
use App\Models\ApplicationStatus;
use App\Models\Company;
use App\Models\Country;
use App\Models\InterviewSchedule;
// use App\Models\JobLocation;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobApplicationAnswer;
use App\Models\JobLocation;
use App\Models\NoticePeriod;
use App\Models\Skill;
use App\Models\User;
use App\Notifications\CandidateScheduleInterview;
use App\Notifications\CandidateStatusChange;
use App\Notifications\ScheduleInterview;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class AdminJobApplicationController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'menu.jobApplications';
        $this->pageIcon = 'icon-user';
    }

    public function index(Request $request)
    {

        abort_if(! $this->user->cans('view_job_applications'), 403);
        $date = Carbon::now(company()->timezone);
        $startDate = $date->subDays(30)->format('Y-m-d');
        $endDate = Carbon::now(company()->timezone)->format('Y-m-d');
        $this->locations = JobLocation::all();
        $this->jobs = Job::all();
        $this->boardColumns = ApplicationStatus::with(['applications' => function ($q) use ($startDate, $endDate, $request) {
            if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
                $q = $q->where(DB::raw('DATE(job_applications.`created_at`)'), '>=', $request->startDate);
            } else {
                $q = $q->where(DB::raw('DATE(job_applications.`created_at`)'), '>=', $startDate);
            }
            if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
                $q = $q->where(DB::raw('DATE(job_applications.`created_at`)'), '<=', $request->endDate);
            } else {
                $q = $q->where(DB::raw('DATE(job_applications.`created_at`)'), '<=', $endDate);
            }
            // Filter By jobs
            if ($request->jobs != 'all' && $request->jobs != '') {
                $q = $q->where('job_applications.job_id', $request->jobs);
            }
            // Filter By jobs
            if ($request->search != null && $request->search != '') {
                $q = $q->where('full_name', 'LIKE', '%'.$request->search.'%');
            }
        }, 'applications.schedule'])->get();
        $boardStracture = [];
        foreach ($this->boardColumns as $key => $column) {
            $boardStracture[$column->id] = [];
            foreach ($column->applications as $application) {
                $boardStracture[$column->id][] = $application->id;
            }
        }
        $this->boardStracture = json_encode($boardStracture);
        $this->currentDate = Carbon::now(company()->timezone)->timestamp;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        if ($request->ajax()) {
            $view = view('admin.job-applications.board-data', $this->data)->render();

            return Reply::dataOnly(['view' => $view]);
        }
        $this->mailSetting = ApplicationSetting::select('id', 'mail_setting')->first();

        return view('admin.job-applications.board', $this->data);
    }

    public function create($jobId = null, $appId = null)
    {

        abort_if(! $this->user->cans('add_job_applications'), 403);

        $company_id = '';
        $company_id = auth()->user()->company_id;

        abort_if(! $this->user->cans('add_job_applications'), 403);
        $candidateCount = JobApplication::count();
        if ($candidateCount > 0 && $this->activePackage->package->no_of_candidate_access !== null && $this->activePackage->package->no_of_candidate_access <= $candidateCount) {
            $this->message = __('modules.subscription.candidatePurchase');

            return view('admin.subscription.ask_purchase', $this->data);
        }

        $this->jobs = Job::activeJobs();
        $this->gender = ['male' => __('modules.front.male'), 'female' => __('modules.front.female'), 'others' => __('modules.front.others')];
        $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        if ($chkAdminRole > 0) {
            $this->jobs = Job::select('jobs.id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city')->join('clients', 'clients.id', 'jobs.client_id')->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->where('jobs.status', '=', 'active')->where('jobs.company_id', '=', $this->user->company_id)->groupBy('jobs.id')->get();
        } else {
            $this->jobs = Job::select('jobs.id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city')->join('job_assign', 'job_assign.job_id', 'jobs.id')->join('clients', 'clients.id', 'jobs.client_id')->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->where('jobs.status', '=', 'active')->where('job_assign.user_id', '=', $this->user->id)->where('jobs.company_id', '=', $this->user->company_id)->groupBy('jobs.id')->get();
        }

        $this->qualification = DB::table('qualification_master')->select('*')->orderBy('qualification_master.display_order', 'ASC')->get();
        $this->jobId = $jobId;
        $this->appId = $appId;

        $this->jobDetail = Job::select('jobs.id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'jobs.min_exp', 'jobs.max_exp', 'jobs.status', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city')
        // ->join('job_assign', 'job_assign.job_id', 'jobs.id')
            ->join('clients', 'clients.id', 'jobs.client_id')
            ->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
            ->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->where('jobs.id', '=', $jobId)->where('jobs.company_id', '=', $this->user->company_id)
            ->first();

        $this->page_event = 'ADD';
        $this->currency = GeneralMethod::currency();
        $this->salaryMode = GeneralMethod::salaryMode();
        // $this->countries = Country::all();

        $this->statelist = GeneralMethod::statelist(101);
        // echo '<pre>'; print_r($this->statelist); exit;

        $this->job_additional_detail = '';
        $this->job_additional_detail = DB::table('job_additional_detail')
            ->select('*')
            ->where('job_additional_detail.company_id', '=', $company_id)
            ->where('job_additional_detail.job_id', '=', $this->jobId)
            ->orderBy('job_additional_detail.id', 'ASC')
            ->get();

        $this->jobLocation = DB::table('client_location_jobs')
            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
            ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
            ->select('client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name', 'state_list.state as state_name')
            ->where('client_location_jobs.job_id', $this->jobId)
            ->orderBy('client_location_jobs.id', 'DESC')
            ->first();

        $this->countries = GeneralMethod::getAllCountryList();
        $this->statelist = '';
        $this->cityListArr = '';

        return view('admin.job-applications.create', $this->data);
    }

    public function edit($id)
    {
        abort_if(! $this->user->cans('edit_job_applications'), 403);

        $company_id = '';
        $company_id = auth()->user()->company_id;

        $this->statuses = ApplicationStatus::all();
        $this->application = JobApplication::find($id);
        $this->jobQuestion = $this->application->job->questions;
        $this->jobs = Job::select('id', 'title', 'location_id', 'status', 'start_date', 'end_date', 'section_visibility')->get();
        $this->noticeperiod = NoticePeriod::all();
        $this->qualification = DB::table('qualification_master')->select('*')->orderBy('qualification_master.display_order', 'ASC')->get();
        $this->interviewStatus = DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();
        $this->childArr = DB::table('candidate_status')
            ->select('*')->where('parent', $this->application->master_status_id)
            ->where('company_id', $this->user->company_id)
            ->orderBy('priority', 'ASC')
            ->get();

        $this->company_id = $this->user->company_id;
        $this->application_add_detail_cnt = DB::table('application_additional_detail')
            ->select('*')
            ->where('application_id', $this->application->id)
            ->orderBy('application_additional_detail.id', 'ASC')
            ->count();

        $this->application_add_detail = DB::table('application_additional_detail')
            ->select('*')
            ->where('application_id', $this->application->id)
            ->orderBy('application_additional_detail.id', 'ASC')
            ->get();

        // $jobId 							=	$this->application->job_id;
        $this->jobId = $this->application->job_id;
        $this->appId = $this->application->id;

        $this->jobDetail = Job::select('jobs.id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'jobs.min_exp', 'jobs.max_exp', 'jobs.status', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city')
        // ->join('job_assign', 'job_assign.job_id', 'jobs.id')
            ->join('clients', 'clients.id', 'jobs.client_id')
            ->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
            ->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->where('jobs.id', '=', $this->jobId)->where('jobs.company_id', '=', $this->user->company_id)
            ->first();

        $this->page_event = 'EDIT';
        $this->currency = GeneralMethod::currency();
        $this->salaryMode = GeneralMethod::salaryMode();
        $this->countries = GeneralMethod::getAllCountryList();
        $this->statelist = GeneralMethod::statelist($this->application->country);
        $this->documents = DB::table('documents')->select('*')->where('documentable_id', $this->application->id)->where('name', 'Resume')->first();

        $this->job_additional_detail = '';
        $this->job_additional_detail = DB::table('job_additional_detail')
            ->select('*')
            ->where('job_additional_detail.company_id', '=', $company_id)
            ->where('job_additional_detail.job_id', '=', $this->jobId)
            ->orderBy('job_additional_detail.id', 'ASC')
            ->get();

        $this->application_additional_detail = DB::table('application_additional_detail')
            ->select('*')
            ->where('application_additional_detail.application_id', '=', $this->application->id)
            ->where('application_additional_detail.job_id', '=', $this->jobId)
            ->orderBy('application_additional_detail.id', 'ASC')
            ->get();

        $this->cityListArr = '';
        if ($this->application->country == 101) {
            $statelist = DB::table('state_list')
                ->select('state_list.id', 'state_list.state as name', DB::raw('101 as country_id'))
                ->orderBy('state_list.state')
                ->get()->toArray();
            $this->statelist = json_decode(json_encode($statelist), true);

            $citylist = DB::table('city_list')
                ->select('city_list.city_id as id', 'city_list.city as name')
                ->where('city_list.state_id', $this->application->state)
                ->orderBy('city_list.city')
                ->get()->toArray();
            $this->cityListArr = json_decode(json_encode($citylist), true);

        }

        // echo '<pre>'; print_r($this->application->country); exit;

        return view('admin.job-applications.create', $this->data);
    }

    public function data(Request $request)
    {
        abort_if(! $this->user->cans('view_job_applications'), 403);
        $jobApplications = JobApplication::select('id', 'job_id', 'status_id', 'full_name', 'skills')->with(['job:id,location_id,title', 'job.skills', 'job.location:id,location', 'status:id,status,color']);
        // Filter by status
        if ($request->status != 'all' && $request->status != '') {
            $jobApplications = $jobApplications->where('status_id', $request->status);
        }
        // Filter By jobs
        if ($request->jobs != 'all' && $request->jobs != '') {
            $jobApplications = $jobApplications->where('job_id', $request->jobs);
        }
        // Filter By skills
        if ($request->skill != 'all' && $request->skill != '') {
            foreach (explode(',', $request->skill) as $key => $skill) {
                if ($key == 0) {
                    $jobApplications = $jobApplications->whereJsonContains('skills', $skill);
                } else {
                    $jobApplications = $jobApplications->orWhereJsonContains('skills', $skill);
                }
            }
        }
        // Filter by location
        if ($request->location != 'all' && $request->location != '') {
            $jobApplications = $jobApplications->whereHas('job.location', function ($query) use ($request) {
                $query->where('id', $request->location);
            });
        }
        // Filter by StartDate
        if ($request->startDate != null && $request->startDate != '') {
            $jobApplications = $jobApplications->whereDate('created_at', '>=', $request->startDate);
        }
        // Filter by EndDate
        if ($request->endDate != null && $request->endDate != '') {
            $jobApplications = $jobApplications->whereDate('created_at', '<=', $request->endDate);
        }
        $jobApplications = $jobApplications->get();

        return DataTables::of($jobApplications)->addColumn('action', function ($row) {
            $action = '';
            if ($this->user->cans('view_job_applications')) {
                $action .= '<a href="javascript:;" class="btn btn-success btn-circle show-document"

                      data-toggle="tooltip" data-row-id="'.$row->id.'" data-modal-name="'.'\\'.get_class($row).'" data-original-title="'.__('modules.jobApplication.viewDocuments').'"><i class="fa fa-search" aria-hidden="true"></i></a>';
            }
            if ($this->user->cans('edit_job_applications')) {
                $action .= ' <a href="'.route('admin.job-applications.edit', [$row->id]).'" class="btn btn-primary btn-circle"

                      data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            }
            if ($this->user->cans('delete_job_applications')) {
                $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle sa-params"

                      data-toggle="tooltip" data-row-id="'.$row->id.'" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
            }

            return $action;
        })->editColumn('full_name', function ($row) {
            return '<a href="javascript:;" class="show-detail" data-widget="control-sidebar" data-slide="true" data-row-id="'.$row->id.'">'.ucwords($row->full_name).'</a>';
        })->editColumn('title', function ($row) {
            return ucfirst($row->job->title);
        })->editColumn('location', function ($row) {
            return ucwords($row->job->location->location);
        })->editColumn('status', function ($row) {
            return '<span>'.ucwords($row->status->status).'</span> <span class="badge badge-pill badge-primary text-white" style= "margin-bottom: -3px; height: 15px; background:'.$row->status->color.'"> </span>';
            // return ucwords($row->status->status);

        })->rawColumns(['action', 'full_name', 'status'])->addIndexColumn()->make(true);
    }

    // public function store(StoreJobApplication $request)
    public function store(Request $request)
    {

        abort_if(! $this->user->cans('add_job_applications'), 403);

        $company_id = '';
        $company_id = auth()->user()->company_id;

        $applicationStatus = ApplicationStatus::first();
        $jobApplication = new JobApplication;
        $jobApplication->full_name = $request->full_name;
        $jobApplication->job_id = $request->job_id;
        // $jobApplication->status_id 			= $applicationStatus->id;
        $jobApplication->status_id = 8;
        $jobApplication->email = $request->email;
        $jobApplication->phone = $request->phone;
        // $jobApplication->cover_letter = $request->cover_letter;
        $jobApplication->column_priority = 0;
        $jobApplication->total_exp = $request->total_exp;
        // $jobApplication->total_exp = $request->total_exp;
        $jobApplication->rel_exp = $request->rel_exp;
        // $jobApplication->passport= $request->passport;
        $jobApplication->ctc = $request->ctc;
        $jobApplication->current_ctc_cur = $request->current_ctc_cur;
        $jobApplication->current_ctc_mode = $request->current_ctc_mode;

        $jobApplication->ectc = $request->ectc;
        $jobApplication->current_ectc_cur = $request->current_ectc_cur;
        $jobApplication->current_ectc_mode = $request->current_ectc_mode;

        $jobApplication->percentage_hike = $request->percentage_hike;
        $jobApplication->comment = $request->comment;
        $jobApplication->pincode = $request->pincode;

        // $jobApplication->current_location = $request->current_location;
        $jobApplication->current_designation = $request->current_designation;
        $jobApplication->current_company = $request->current_company;

        $cnt = 0;
        $skillStr = '';
        $skillArr = json_decode($request->skills, true);
        if (! empty($skillArr)) {
            foreach ($skillArr as $sval) {
                if ($cnt == 0) {
                    $skillStr .= $sval['value'];
                } else {
                    $skillStr .= ' , '.$sval['value'];
                }
                $cnt++;
            }
        }
        $jobApplication->skills = $skillStr;

        // $jobApplication->master_status_id 	= $applicationStatus->id;
        $jobApplication->master_status_id = 8;
        $jobApplication->secondrary_email = $request->secondrary_email;
        $jobApplication->secondary_mobile = $request->secondary_mobile;
        $submitted_by = '';
        $submitted_by = auth()->user()->id;
        if (! empty($submitted_by)) {
            $jobApplication->submitted_by = $submitted_by;
        }
        $serving_notice_period = '';
        $serving_notice_period = $request->serving_notice_period;
        if ($serving_notice_period == 1) {
            $jobApplication->np_id = 'SNP-LWD-'.$request->calender;
        } elseif ($serving_notice_period == 2) {
            $jobApplication->np_id = 'Immediate';
        } else {
            $jobApplication->np_id = $request->ddl_np_id;
        }
        if (! empty($request->qualification)) {
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

        /* if ($request->has('country')) {
             $countriesArray 			=	json_decode(file_get_contents(public_path('country-state-city/countries.json')), true) ['countries'];
             $statesArray 				=	json_decode(file_get_contents(public_path('country-state-city/states.json')), true) ['states'];
             $jobApplication->country 	=	$this->getName($countriesArray, $request->country);
             $jobApplication->state 		=	$this->getName($statesArray, $request->state);
             $jobApplication->city 		=	$request->city;
         }*/

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
            } elseif ($returnCount > 0 && $app_duplicate == 2) {
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
        // Check job Assign Or Not
        $chkJobAssign = DB::table('job_assign')->select('job_assign.id')->where('company_id', $company_id)->where('job_id', $request->job_id)->where('user_id', $submitted_by)->count();
        if ($chkJobAssign < 1) {
            $values = ['company_id' => @$company_id, 'user_id' => @$submitted_by, 'job_id' => @$request->job_id];
            DB::table('job_assign')->insert($values);
        }
        if ($request->hasFile('resume')) {
            $hashname = Files::upload($request->resume, 'documents/'.$jobApplication->id, null, null, false);
            $jobApplication->documents()->create(['name' => 'Resume', 'hashname' => $hashname]);
        }

        /*if ($request->hasFile('resume')) {
        	//$hashname 			= Files::upload($request->resume,'resumes',null,null,false);
             $hashname 			= Files::upload($request->resume, 'documents/'.$jobApplication->id, null, null, false);
             $jobApplication->documents()->create([
        					'name' 		=> 'Resume',
        					'hashname' 	=> $hashname
        				]);
        }*/

        if (isset($request->jobskill) && ! empty($request->jobskill)) {
            DB::table('candidate_skills_value')->where('job_id', $request->job_id)->where('candidate_id', $jobApplication->id)->delete();
            foreach ($request->jobskill as $key => $value) {
                if (! empty(@$value)) {
                    $values = ['job_id' => @$request->job_id, 'skill_id' => @$key, 'candidate_id' => $jobApplication->id, 'skill_value' => @$value];
                    DB::table('candidate_skills_value')->insert($values);
                }
            }
        }
        $status_name = $note = '';
        $status_cnt = DB::table('candidate_status')->select('*')->where('company_id', user()->company_id)->where('parent', $applicationStatus->id)
        // ->orderBy('priority', 'ASC')
            ->count();
        if ($status_cnt > 0) {
            // echo '<pre>';print_r($applicationStatus->name); exit;
            $status_row = DB::table('candidate_status')->select('*')->where('company_id', user()->company_id)->where('parent', $applicationStatus->id)->orderBy('priority', 'ASC')->first();
            $status_name = $status_row->name;
        }
        $insert_type = 0;
        GeneralMethod::manageTimeline(auth()->user()->id, $jobApplication->id, @$request->job_id, $applicationStatus->id, $applicationStatus->name, 'Successfully Application  Added ', $status_name, $insert_type);

        // Job Application Answer save
        if (isset($request->answer) && ! empty($request->answer)) {
            foreach ($request->answer as $key => $value) {
                $answer = new JobApplicationAnswer;
                $answer->job_application_id = $jobApplication->id;
                $answer->job_id = $request->job_id;
                $answer->question_id = $key;
                $answer->answer = $value;
                $answer->save();
            }
        }
        // return Reply::redirect(route('admin.job-applications.index'), __('menu.jobApplications') . ' ' . __('messages.createdSuccessfully')); 54+43=97
        // exit;
        $job_additional_detail_count = DB::table('job_additional_detail')->select('*')->where('job_id', $request->job_id)->orderBy('job_additional_detail.id', 'ASC')->count();
        $job_additional_detail = DB::table('job_additional_detail')->select('*')->where('job_id', $request->job_id)->orderBy('job_additional_detail.id', 'ASC')->get();
        if ($job_additional_detail_count > 0) {
            $inc = 0;
            foreach ($job_additional_detail as $jd) {

                $values = [
                    'job_id' => @$request->job_id,
                    'question_id' => @$jd->id,
                    'application_id' => @$jobApplication->id,
                    'job_detail_value' => @$jd->detail_value,
                    'application_detail_value' => @$request->additional_detail[$inc],
                ];
                DB::table('application_additional_detail')->insert($values);
                $inc++;

            }
        }

        // echo '<pre>'; print_r($_REQUEST); exit;

        if (! empty($request->job_id)) {
            return Reply::redirect(url('/admin/apptracker/'.$request->job_id.'/all'), __('menu.jobApplications').' '.__($message));
        } else {
            return Reply::redirect(route('admin.job-applications.table'), __('menu.jobApplications').' '.__($message));
        }
    }

    public function update(Request $request, $id)
    {
        // echo '<pre>'; print_r($_FILES); exit;

        abort_if(! $this->user->cans('edit_job_applications'), 403);

        $jobApplication = JobApplication::with(['documents'])->findOrFail($id);
        $jobApplication->full_name = $request->full_name;
        if (! empty($request->current_designation)) {
            $jobApplication->current_designation = @$request->current_designation;
        }
        if (! empty($request->current_company)) {
            $jobApplication->current_company = @$request->current_company;
        }
        $jobApplication->phone = $request->phone;
        $jobApplication->secondary_mobile = $request->secondary_mobile;
        if ($request->has('dob')) {
            $jobApplication->dob = $request->dob;
        }
        $jobApplication->email = $request->email;
        $jobApplication->secondrary_email = $request->secondrary_email;

        if (! empty($request->qualification)) {
            $jobApplication->qualification = @$request->qualification;
        }

        if (! empty($request->total_exp)) {
            $jobApplication->total_exp = @$request->total_exp;
        }
        if (! empty($request->rel_exp)) {
            $jobApplication->rel_exp = @$request->rel_exp;
        }

        $serving_notice_period = '';
        $serving_notice_period = $request->serving_notice_period;
        if ($serving_notice_period == 1) {
            // if (!empty($request->calender)) {
            //    $jobApplication->np_id 				= 'SNP-LWD-' . $request->calender;
            // } else {
            $jobApplication->np_id = $request->np_id;
            // }
        } elseif ($serving_notice_period == 2) {
            $jobApplication->np_id = 'Immediate';
        } else {
            $jobApplication->np_id = $request->ddl_np_id;
        }
        $jobApplication->serving_notice_period_mode = $request->serving_notice_period;
        $jobApplication->current_ctc_cur = $request->current_ctc_cur;
        if (! empty($request->ctc)) {
            $jobApplication->ctc = @$request->ctc;
        }
        $jobApplication->current_ctc_mode = $request->current_ctc_mode;
        $jobApplication->current_ectc_cur = $request->current_ectc_cur;
        if (! empty($request->ectc)) {
            $jobApplication->ectc = @$request->ectc;
        }
        $jobApplication->current_ectc_mode = @$request->current_ectc_mode;
        $jobApplication->percentage_hike = @$request->percentage_hike;
        $jobApplication->comment = @$request->comment;

        // $jobApplication->country 					= @$request->country;
        // $jobApplication->state 						= @$request->state;
        // $jobApplication->city 						= $request->city;

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
        if (! empty($skillArr)) {
            foreach ($skillArr as $sval) {
                if ($cnt == 0) {
                    $skillStr .= $sval['value'];
                } else {
                    $skillStr .= ' , '.$sval['value'];
                }
                $cnt++;
            }
        }
        $jobApplication->skills = $skillStr;

        if ($request->hasFile('photo')) {
            $jobApplication->photo = Files::upload($request->photo, 'candidate-photos');
        }

        $jobApplication->save();

        // echo '<pre>'; print_r($_REQUEST); exit;

        $mailSetting = ApplicationSetting::select('id', 'mail_setting')->first()->mail_setting;

        /* $jobApplication->job_id = $request->job_id;
         $jobApplication->status_id = $request->status_id;
         $jobApplication->master_status_id = $request->status_id;
         $jobApplication->cover_letter = $request->cover_letter;
         $jobApplication->cover_letter = $request->childStatus;
         if (!empty($request->status_id)) {
             $jobApplication->master_status_id = @$request->status_id;
         }
         if (!empty($request->childStatus)) {
             $jobApplication->child_status_id = @$request->childStatus;
         }
         if (!empty($request->passport)) {
             $jobApplication->passport = @$request->passport;
         }

         if (!empty($request->np_id)) {
             //$jobApplication->np_id 				= @$request->np_id;
         }

         if (!empty($request->current_location)) {
             $jobApplication->current_location = $request->current_location;
         }
         if ($request->has('gender')) {
             $jobApplication->gender = $request->gender;
         }

         if ($request->hasFile('photo')) {
             $jobApplication->photo = Files::upload($request->photo, 'candidate-photos');
         }
         $isStatusDirty = $jobApplication->isDirty('status_id');*/

        $status_name = $note = '';
        $status_cnt = DB::table('candidate_status')->select('*')->where('company_id', $this->user->company_id)->where('parent', $request->status_id)
        // ->orderBy('priority', 'ASC')
            ->count();
        if ($status_cnt > 0) {
            $status_row = DB::table('candidate_status')->select('*')->where('company_id', $this->user->company_id)->where('parent', $request->status_id)->where('id', @$request->childStatus)->first();
            // $status_name			=	$status_row->name;

        }
        // GeneralMethod :: manageTimeline(auth()->user()->id, $jobApplication->id, $jobApplication->job_id, $request->status_id, $status_name, 'Successfully Application  Update ');
        if ($request->hasFile('resume')) {
            $hashname = Files::upload($request->resume, 'documents/'.$jobApplication->id, null, null, false);
            $jobApplication->documents()->updateOrCreate(['documentable_type' => JobApplication::class, 'documentable_id' => $jobApplication->id, 'name' => 'Resume'], ['hashname' => $hashname]);
        }
        // Job Application Answer save
        if (isset($request->answer) && count($request->answer) > 0) {
            foreach ($request->answer as $key => $value) {
                JobApplicationAnswer::updateOrCreate(['job_application_id' => $jobApplication->id, 'job_id' => $jobApplication->job_id, 'question_id' => $key], ['answer' => $value]);
            }
        }
        /*if ($mailSetting[$request->status_id]['status'] && $isStatusDirty) {

            Notification::send($jobApplication, new CandidateStatusChange($jobApplication));

        }*/
        // return Reply::redirect(route('admin.apptracker.table'), __('menu.jobApplications') . ' ' . __('messages.updatedSuccessfully'));
        // admin/apptracker/14/all
        DB::table('application_additional_detail')->where('application_id', $jobApplication->id)->delete();

        $job_additional_detail_count = DB::table('job_additional_detail')->select('*')->where('job_id', $jobApplication->job_id)->orderBy('job_additional_detail.id', 'ASC')->count();
        $job_additional_detail = DB::table('job_additional_detail')->select('*')->where('job_id', $jobApplication->job_id)->orderBy('job_additional_detail.id', 'ASC')->get();
        if ($job_additional_detail_count > 0) {
            $inc = 0;
            foreach ($job_additional_detail as $jd) {

                $values = [
                    'job_id' => @$jobApplication->job_id,
                    'question_id' => @$jd->id,
                    'application_id' => @$jobApplication->id,
                    'job_detail_value' => @$jd->detail_value,
                    'application_detail_value' => @$request->additional_detail[$inc],
                ];
                DB::table('application_additional_detail')->insert($values);
                $inc++;

            }
        }

        /*$job_additional_detail_count = DB::table('job_additional_detail')->select('*')->where('job_id', $request->job_id)->orderBy('job_additional_detail.id', 'ASC')->count();
        $job_additional_detail = DB::table('job_additional_detail')->select('*')->where('job_id', $request->job_id)->orderBy('job_additional_detail.id', 'ASC')->get();
        if ($job_additional_detail_count > 0) {
            $inc = 0;
            foreach ($job_additional_detail as $jd) {
                if (!empty($jd)) {
                    $values = array('job_id' => $request->job_id, 'application_id' => $jobApplication->id, 'job_detail_value' => $jd->detail_value, 'application_detail_value' => $request->job_additional_detail[$inc]);
                    DB::table('application_additional_detail')->insert($values);
                    $inc++;
                }
            }
        }*/

        return Reply::redirect(url('/admin/apptracker/'.$jobApplication->job_id.'/all'), __('menu.jobApplications').' '.__('messages.createdSuccessfully'));
    }

    public function destroy($id)
    {
        abort_if(! $this->user->cans('delete_job_applications'), 403);
        $jobApplication = JobApplication::findOrFail($id);
        if ($jobApplication->photo) {
            Storage::delete('candidate-photos/'.$jobApplication->photo);
        }
        $jobApplication->forceDelete();

        return Reply::success(__('messages.recordDeleted'));
    }

    public function show_original($id)
    {
        $this->application = JobApplication::with(['schedule', 'status', 'schedule.employee', 'schedule.comments.user'])->find($id);
        $this->timezone = Company::select('id', 'timezone')->first()->timezone;
        $this->skills = Skill::select('id', 'name')->get();
        $this->answers = JobApplicationAnswer::with(['question'])->where('job_id', $this->application->job_id)->where('job_application_id', $this->application->id)->get();
        $view = view('admin.job-applications.show', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function show($id)
    {
        $company_id = '';
        $company_id = auth()->user()->company_id;

        $this->currency = GeneralMethod::currency();
        $this->salaryMode = GeneralMethod::salaryMode();

        $this->intModeArr = GeneralMethod::interview_mode();
        $this->application = JobApplication::select('job_applications.id', 'job_applications.submitted_by', 'job_applications.company_id', 'job_applications.np_id', 'job_applications.serving_notice_period_mode', 'job_applications.full_name', 'job_applications.email', 'job_applications.phone', 'job_applications.secondary_mobile', 'job_applications.photo', 'job_applications.resume', 'job_applications.rating', 'job_applications.skype_id', 'job_applications.cover_letter', 'job_applications.job_id', 'job_applications.status_id', 'job_applications.is_viewed', 'job_applications.column_priority', 'job_applications.created_at', 'job_applications.updated_at', 'job_applications.deleted_at', 'job_applications.total_exp', 'job_applications.rel_exp', 'job_applications.passport', 'job_applications.current_location', 'job_applications.current_designation', 'job_applications.current_company', 'job_applications.process_id', 'job_applications.qualification', 'job_applications.master_status_id', 'job_applications.child_status_id', 'job_applications.secondrary_email', 'job_applications.secondary_mobile', 'job_applications.skills',
            'job_applications.city', 'job_applications.country', 'job_applications.pincode', 'job_applications.state',

            'job_applications.current_ctc_cur', 'job_applications.ctc', 'job_applications.current_ctc_mode',
            'job_applications.current_ectc_cur', 'job_applications.ectc', 'job_applications.current_ectc_mode',
            'job_applications.percentage_hike', 'job_applications.comment', 'job_applications.dob', 'job_applications.created_at',

            'clients.name as client_name', 'jobs.title',
            'users.name as posted_by', 'users.image as posted_by_image',
            // 'notice_periods.np_title',
            'master_status.name as master_status_name', 'job_applications.proposed_ctc', 'job_applications.any_offer', 'job_applications.document', 'job_applications.sub_for_approval', 'job_applications.offer_approved', 'job_applications.offer_release', 'job_applications.offer_acceptance', 'job_applications.joined', 'job_applications.joined_date',
            'qualification_master.name as qualification_name',

            'state_list.state as state_name',
            'city_list.city as city_name'
        )
            ->leftJoin('jobs', 'jobs.id', '=', 'job_applications.job_id')
            ->leftjoin('clients', 'clients.id', 'jobs.client_id')
        // ->leftjoin('notice_periods', 'notice_periods.np_id', 'job_applications.np_id')
            ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
            ->leftjoin('users', 'users.id', 'job_applications.submitted_by')
            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
            ->leftJoin('master_status', 'master_status.id', '=', 'job_applications.status_id')
            ->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id');
        $this->application = $this->application->where('job_applications.id', $id);
        $this->application = $this->application->first();

        $this->client_contuser_arr = DB::table('client_contact_info_jobs')->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')->join('clients', 'clients.id', '=', 'client_contact_info.client_id')->select('client_contact_info.contact_person_name', 'client_contact_info.contact_person_email', 'client_contact_info.contact_person_number', 'client_contact_info.contact_person_designation', 'clients.name')->where('client_contact_info_jobs.job_id', $this->application->job_id)->get();

        $this->job_additional_detail = '';
        $this->job_additional_detail = DB::table('job_additional_detail')
            ->select('*')
            ->where('job_additional_detail.company_id', '=', $company_id)
            ->where('job_additional_detail.job_id', '=', @$this->application->job_id)
            ->orderBy('job_additional_detail.id', 'ASC')
            ->get();

        $this->application_additional_detail = DB::table('application_additional_detail')
            ->select('*')
            ->where('application_additional_detail.application_id', '=', $this->application->id)
            ->where('application_additional_detail.job_id', '=', @$this->application->job_id)
            ->orderBy('application_additional_detail.id', 'ASC')
            ->get();

        // echo '<pre>'; print_r($this->application); exit;
        if (! empty($this->application->qualification) && $this->application->qualification > 0) {
            $this->qualification = DB::table('qualification_master')->select('name')->where('id', $this->application->qualification)->first();
        }
        $this->answers = JobApplicationAnswer::with(['question'])->where('job_id', $this->application->job_id)->where('job_application_id', $this->application->id)->get();

        $this->notesArr = DB::table('applicant_notes')
            ->select(
                'applicant_notes.id', 'applicant_notes.note_text', 'applicant_notes.created_at', 'applicant_notes.updated_at',
                'users.name', 'users.image as posted_by_image'
            )
        // ->where('company_id', user()->company_id)
            ->join('users', 'users.id', '=', 'applicant_notes.user_id')
            ->where('job_application_id', $this->application->id)
            ->orderBy('id', 'ASC')
            ->get();

        $this->schedule = DB::table('interview_schedules')->select('interview_schedules.id', 'interview_schedules.company_id', 'interview_schedules.job_application_id', 'interview_schedules.schedule_date', 'interview_schedules.status', 'interview_schedules.user_accept_status', 'interview_schedules.created_at', 'interview_schedules.updated_at')
        // ->where('company_id', user()->company_id)
        // ->join('users', 'users.id', '=', 'applicant_notes.user_id')
            ->where('job_application_id', $this->application->id)
            ->orderBy('id', 'DESC')
            ->get();

        $application_email = $this->application->email;
        $this->application_email = $application_email;
        $application_id = $this->application->id;
        $this->application_id = $application_id;
        $job_id = $this->application->job_id;
        $this->job_id = $job_id;

        $this->timeline = DB::table('timeline');
        $this->timeline = $this->timeline->select('timeline.id', 'timeline.owner_id', 'timeline.application_id', 'timeline.application_email', 'timeline.job_id', 'timeline.process_id', 'timeline.status_name', 'timeline.process_child_name', 'timeline.note', 'timeline.visible', 'timeline.insert_type', 'timeline.create_at', 'timeline.interview_schedule_date', 'timeline.interview_from_time', 'timeline.interview_to_time', 'timeline.interview_mode', 'timeline.interview_mention_link', 'timeline.interviewers', 'timeline.interview_additional_email', 'timeline.interview_message', 'jobs.id as job_id', 'jobs.title', 'master_status.name as master_status_name', 'clients.name as client_name',

            'timeline.timeline_type', 'timeline.prev_process_name', 'timeline.prev_process_child_name', 'timeline.interview_mode_child_level', 'timeline.average_star_rating', 'timeline.interview_schedules_id',
            'master_status.css_style',
            'users.name', 'users.image'

        );
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

        $this->child_status_name = 'N/A';
        if ($this->application->child_status_id > 0) {
            $childStatus = DB::table('candidate_status')->select('*')->where('id', $this->application->child_status_id)->first();
            if (! empty($childStatus)) {
                $this->child_status_name = @$childStatus->name;
            }
        }
        $this->company_id = $this->user->company_id;
        $this->additional_skills_arr = DB::table('skills')->join('candidate_skills_value', 'skills.id', '=', 'candidate_skills_value.skill_id')
        // ->join('clients', 'clients.id', '=', 'client_contact_info.client_id')
            ->select('skills.name', 'candidate_skills_value.skill_value')->where('candidate_skills_value.job_id', $this->application->job_id)->where('candidate_skills_value.candidate_id', $this->application->id)->where('skills.company_id', $this->user->company_id)->get();
        $this->application_add_detail_cnt = DB::table('application_additional_detail')->select('*')->where('application_id', $this->application->id)->where('job_id', $this->application->job_id)->orderBy('application_additional_detail.id', 'ASC')->count();
        $this->application_add_detail = DB::table('application_additional_detail')->select('*')->where('application_id', $this->application->id)->where('job_id', $this->application->job_id)->orderBy('application_additional_detail.id', 'ASC')->get();
        $this->offer_job_applications_count = DB::table('offer_job_applications')->select('offer_job_applications.id')->join('jobs', 'jobs.id', '=', 'offer_job_applications.job_id')->join('clients', 'clients.id', '=', 'jobs.client_id')
        // ->leftjoin('countries', 'countries.id', 'job_applications.master_status_id')
            ->where('offer_job_applications.email', '=', $this->application->email)->where('offer_job_applications.company_id', '=', auth()->user()->company_id)->orderBy('offer_job_applications.id', 'DESC')->count();
        $this->offer_job_applications = DB::table('offer_job_applications')->select('offer_job_applications.id', 'offer_job_applications.application_id', 'offer_job_applications.submitted_by', 'offer_job_applications.company_id', 'offer_job_applications.np_id', 'offer_job_applications.full_name', 'offer_job_applications.email', 'offer_job_applications.phone', 'offer_job_applications.gender', 'offer_job_applications.dob', 'offer_job_applications.country', 'offer_job_applications.state', 'offer_job_applications.city', 'offer_job_applications.secondrary_email', 'offer_job_applications.secondary_mobile', 'offer_job_applications.skills', 'offer_job_applications.photo', 'offer_job_applications.resume', 'offer_job_applications.rating', 'offer_job_applications.skype_id', 'offer_job_applications.cover_letter', 'offer_job_applications.job_id', 'offer_job_applications.status_id', 'offer_job_applications.is_viewed', 'offer_job_applications.column_priority', 'offer_job_applications.created_at', 'offer_job_applications.updated_at', 'offer_job_applications.total_exp', 'offer_job_applications.rel_exp', 'offer_job_applications.passport', 'offer_job_applications.ctc', 'offer_job_applications.ectc', 'offer_job_applications.current_location', 'offer_job_applications.current_designation', 'offer_job_applications.current_company', 'offer_job_applications.process_id', 'offer_job_applications.qualification', 'offer_job_applications.master_status_id', 'offer_job_applications.child_status_id', 'offer_job_applications.serving_notice_period_mode', 'offer_job_applications.proposed_ctc', 'offer_job_applications.any_offer', 'offer_job_applications.document', 'offer_job_applications.sub_for_approval', 'offer_job_applications.offer_approved', 'offer_job_applications.offer_release', 'offer_job_applications.offer_acceptance', 'offer_job_applications.joined', 'offer_job_applications.joined_date', 'offer_job_applications.duplicate', 'jobs.title', 'clients.name as client_name')->join('jobs', 'jobs.id', '=', 'offer_job_applications.job_id')->join('clients', 'clients.id', '=', 'jobs.client_id')
        // ->leftjoin('countries', 'countries.id', 'job_applications.master_status_id')
            ->where('offer_job_applications.email', '=', $this->application->email)->where('offer_job_applications.company_id', '=', auth()->user()->company_id)
        // ->where('job_id', $this->application->job_id)
            ->orderBy('offer_job_applications.id', 'DESC')->get();
        // echo '<pre>'; print_r($this->offer_job_applications); exit;

        $resume = '';
        $resumeData = DB::table('documents')->select('*')->where('documentable_id', $this->application->id)->where('name', '=', 'Resume')->first();
        $this->resume = @$resumeData->hashname;
        // $view = view('admin.job-applications.show', $this->data)->render();
        $view = view('admin.job-applications.showdetail', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function updateIndex(Request $request)
    {
        $taskIds = $request->applicationIds;
        $boardColumnIds = $request->boardColumnIds;
        $priorities = $request->prioritys;
        $mailSetting = ApplicationSetting::select('id', 'mail_setting')->first()->mail_setting;
        $date = Carbon::now(company()->timezone);
        $startDate = $request->startDate ?: $date->subDays(30)->format('Y-m-d');
        $endDate = $request->endDate ?: $date->format('Y-m-d');
        if ($request->has('applicationIds')) {
            foreach ($taskIds as $key => $taskId) {
                if (! is_null($taskId)) {
                    $task = JobApplication::find($taskId);
                    $task->column_priority = $priorities[$key];
                    $task->status_id = $boardColumnIds[$key];
                    $task->save();
                }
            }
            // Send notification to candidate on update status
            if (isset($mailSetting[$boardColumnIds[0]]['status'])) {
                if ($mailSetting[$boardColumnIds[0]]['status'] && $request->draggedTaskId != 0) {
                    $jobApplication = JobApplication::findOrFail($request->draggedTaskId);
                    Notification::send($jobApplication, new CandidateStatusChange($jobApplication));
                }
            }
        }
        $columnCountByIds = ApplicationStatus::select('id', 'color')->withCount(['applications as status_count' => function ($query) use ($startDate, $endDate, $request) {
            $query->withoutTrashed()->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate);
            if ($request->jobs != 'all' && $request->jobs != '') {
                $query->where('job_id', $request->jobs);
            }
            if ($request->search != '') {
                $query->where('full_name', 'LIKE', '%'.$request->search.'%');
            }
        },
        ])->get()->toArray();

        return Reply::dataOnly(['status' => 'success', 'columnCountByIds' => $columnCountByIds]);
    }

    public function table()
    {
        abort_if(! $this->user->cans('view_job_applications'), 403);
        $this->boardColumns = ApplicationStatus::all();
        $this->locations = JobLocation::all();
        $this->jobs = Job::all();
        $this->skills = Skill::all();

        return view('admin.job-applications.index', $this->data);
    }

    public function ratingSave(Request $request, $id)
    {
        abort_if(! $this->user->cans('edit_job_applications'), 403);
        $application = JobApplication::withTrashed()->findOrFail($id);
        $application->rating = $request->rating;
        $application->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }

    // Job Applications data Export

    public function export($status, $location, $startDate, $endDate, $jobs)
    {
        $filters = ['status' => $status, 'location' => $location, 'startDate' => $startDate, 'endDate' => $endDate, 'jobs' => $jobs];
        $data = ['company' => $this->companyName];

        return Excel::download(new JobApplicationExport($filters, $data), 'job-applications.xlsx', ExcelExcel::XLSX);
    }

    public function viewDetails(Request $request)
    {
        $candidateCount = JobApplication::count();
        if ($candidateCount > 0 && ! is_null($this->activePackage->package->no_of_candidate_access) && $this->activePackage->package->no_of_candidate_access <= $candidateCount) {
            $this->message = __('modules.subscription.candidatePurchase');

            return Reply::error(__('modules.subscription.candidatePurchase'));
        }
        $application = JobApplication::withTrashed()->findOrFail($request->id);
        $application->is_viewed = 1;
        $application->save();

        return Reply::dataOnly(['status' => 'success', 'email' => $application->email, 'phone' => $application->phone, 'gender' => ucfirst($application->gender), 'dob' => $application->dob ? $application->dob->format('jS F, Y') : '', 'country' => $application->country, 'state' => $application->state, 'city' => $application->city, 'resume' => $application->resume_url ?: '']);
    }

    public function getName($arr, $id)
    {
        $result = array_filter($arr, function ($value) use ($id) {
            return $value['id'] == $id;
        });

        return current($result)['name'];
    }

    public function archiveJobApplication(Request $request, JobApplication $application)
    {
        abort_if(! $this->user->cans('delete_job_applications'), 403);
        $application->delete();

        return Reply::success(__('messages.applicationArchivedSuccessfully'));
    }

    public function unarchiveJobApplication(Request $request, $application_id)
    {
        abort_if(! $this->user->cans('delete_job_applications'), 403);
        $application = JobApplication::select('id', 'deleted_at')->withTrashed()->where('id', $application_id)->first();
        $application->restore();

        return Reply::success(__('messages.applicationUnarchivedSuccessfully'));
    }

    public function addSkills(Request $request, $applicationId)
    {
        abort_if(! $this->user->cans('edit_job_applications'), 403);
        $application = JobApplication::withTrashed()->findOrFail($applicationId);
        $application->skills = $request->skills;
        $application->save();

        return Reply::success(__('messages.skillsSavedSuccessfully'));
    }

    public function download($id)
    {
        $resume = '';
        $resumeData = DB::table('documents')->select('*')->where('documentable_id', $id)->where('name', '=', 'Resume')->first();
        $resume = $resumeData->hashname;
        // echo '<pre>'; print_r( public_path() );  exit;
        $company = user()->company_id;
        $jobApplication = JobApplication::findOrFail($id);
        $path = public_path().'/user-uploads/documents/'.$jobApplication->id.'/'.$resume;
        if (! file_exists($path)) {
            abort(404);
        }

        return response()->download($path);
    }

    public function storeSchedule_original(StoreRequest $request)
    {
        abort_if(! $this->user->cans('add_schedule'), 403);
        $dateTime = $request->scheduleDate.' '.$request->scheduleTime;
        $dateTime = Carbon::createFromFormat('Y-m-d H:i', $dateTime);
        // store Schedule
        $interviewSchedule = new InterviewSchedule;
        $interviewSchedule->job_application_id = $request->candidates[0];
        $interviewSchedule->schedule_date = $dateTime;
        $interviewSchedule->save();
        // Update Schedule Status
        $status = ApplicationStatus::where('status', 'interview')->first();
        $jobApplication = $interviewSchedule->jobApplication;
        $jobApplication->status_id = $status->id;
        $jobApplication->save();
        if ($request->comment) {
            $scheduleComment = ['interview_schedule_id' => $interviewSchedule->id, 'user_id' => $this->user->id, 'comment' => $request->comment];
            $interviewSchedule->comments()->create($scheduleComment);
        }
        if (! empty($request->employees)) {
            $interviewSchedule->employees()->attach($request->employees, ['company_id' => $this->user->company_id]);
            // Mail to employee for inform interview schedule
            Notification::send($interviewSchedule->employees, new ScheduleInterview($jobApplication));
        }
        // mail to candidate for inform interview schedule
        Notification::send($jobApplication, new CandidateScheduleInterview($jobApplication, $interviewSchedule));

        return Reply::redirect(route('admin.interview-schedule.index'), __('menu.interviewSchedule').' '.__('messages.createdSuccessfully'));
    }

    public function createSchedule_original(Request $request, $id)
    {
        abort_if(! $this->user->cans('add_schedule'), 403);
        $this->candidates = JobApplication::all();
        $this->users = User::with('company')->company()->get();
        $this->scheduleDate = $request->date;
        $this->currentApplicant = JobApplication::findOrFail($id);

        return view('admin.job-applications.interview-create', $this->data)->render();
    }

    public function createSchedule(Request $request, $id)
    {
        abort_if(! $this->user->cans('add_schedule'), 403);

        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $this->candidates = JobApplication::all();
        // $this->users 				=	User::with('company')->company()->get();
        $this->users = User::with('company')->company()->where('users.type', '<>', 1)->get();
        $this->scheduleDate = $request->date;
        $this->currentApplicant = JobApplication::findOrFail($id);
        $this->interview_node = GeneralMethod::interview_mode();
        $this->application_id = $this->interview_id = $this->feedback_id = $this->interview_detail = '';
        $this->application_id = $id;
        $this->interview_detail = [];
        $this->application = JobApplication::select('job_applications.*', 'clients.name as client_name', 'jobs.title as job_title', 'client_location.city', 'jobs.company_id',
            'master_status.name as master_status_name', 'master_status.css_style as master_status_style',
            'users.email as submitted_by_email', 'users.name as submitted_by_name', 'users.company_id as submitted_by_company_id',
            'qualification_master.name as qualification_name',
            'state_list.state as state_name'
        )
            ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
            ->join('clients', 'clients.id', '=', 'jobs.client_id')
            ->join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
            ->join('users', 'users.id', '=', 'job_applications.submitted_by')
            ->leftjoin('client_location_jobs', 'client_location_jobs.job_id', 'jobs.id')
            ->leftjoin('client_location', 'client_location.id', 'client_location_jobs.client_location_id')
            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
            ->leftjoin('state_list', 'state_list.id', 'job_applications.state')
            ->where('job_applications.id', $this->application_id)
            ->groupBy('job_applications.id')->first();

        $country = '';
        $this->row_country_name = '';

        if (empty($this->application->country)) {
            $this->row_country_name = '----';
        } else {
            if (! empty($this->application->country)) {
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

        $this->row_city_name = '';
        if ($this->application->country == 101) {
            $row_city = DB::table('city_list')->select('*')->where('city_id', $this->application->city_id)->first();
            $this->row_city_name = @$row_city->city;
        } else {
            $this->row_city_name = $this->application->city;
        }

        $current_ctc_cur = '';
        $current_ctc_cur = $currencySymbole[$this->application->current_ctc_cur];

        $current_ectc_cur = '';
        $current_ectc_cur = $currencySymbole[$this->application->current_ectc_cur];

        $ctc = '';
        $ctc = GeneralMethod::thousandsCurrencyFormat($this->application->ctc);
        $ectc = '';
        $ectc = GeneralMethod::thousandsCurrencyFormat($this->application->ectc);

        $current_ctc_mode = '';
        $current_ctc_mode = substr($salaryMode[$this->application->current_ctc_mode], 0, 1);

        $current_ectc_mode = '';
        $current_ectc_mode = substr($salaryMode[$this->application->current_ectc_mode], 0, 1);

        $this->current_ctc = @$current_ctc_cur.@$ctc.'/'.@$current_ctc_mode;
        $this->expected_ctc = @$current_ectc_cur.@$ectc.'/'.@$current_ectc_mode;

        $this->child_status_name = '';
        $submitted_by_company_id = $this->application->submitted_by_company_id;
        $submitted_by_row = DB::table('companies')->select('*')->where('companies.id', $submitted_by_company_id)->first();
        $this->submitted_by_company = $submitted_by_row->company_name;

        if ($this->application->child_status_id) {
            $child_status_by_row = DB::table('candidate_status')->select('*')->where('candidate_status.id', $this->application->child_status_id)->first();
            $this->child_status_name = $child_status_by_row->name;
        }

        $this->interviewStatus = DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();
        $this->client_contact_info = DB::table('jobs')->join('client_contact_info_jobs', 'client_contact_info_jobs.job_id', '=', 'jobs.id')->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')->select('client_contact_info.id', 'client_contact_info.contact_person_name', 'client_contact_info.contact_person_email')->where('client_contact_info_jobs.job_id', '=', $this->application->job_id)->orderBy('client_contact_info.contact_person_name', 'ASC')->first();

        // echo '<pre>'; print_r($this->client_contact_info); exit;
        $this->job_id = $this->application->job_id;
        $this->interview_mode = DB::table('interview_mode')->select('*')->orderBy('interview_mode.id', 'ASC')->get();
        // interview_mode_lavel
        $this->email_template = DB::table('th_email_template')->select('*')->orderBy('th_email_template.e_temp_id', 'ASC')->get();
        $this->from_email = $this->user->email;
        $this->cc_email = $this->user->email;
        if ($this->application->submitted_by_email != $this->user->email) {
            $this->cc_email = $this->cc_email.' , '.$this->application->submitted_by_email;
        }

        $client_contact_arr = DB::table('client_contact_info')->join('client_contact_info_jobs', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')->select('client_contact_info.contact_person_name', 'client_contact_info.contact_person_email')->where('client_contact_info_jobs.job_id', $this->application->job_id)->get();
        if (! empty($client_contact_arr)) {
            foreach ($client_contact_arr as $ccData) {
                if (! empty($ccData->contact_person_email)) {
                    $this->cc_email = $this->cc_email.' , '.$ccData->contact_person_email;
                }
            }
        }

        $this->reschedule = @$_REQUEST['reschedule'];

        $view = view('admin.job-applications.interview-create', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function createCopy(Request $request, $id)
    {
        abort_if(! $this->user->cans('add_schedule'), 403);
        $this->candidates = JobApplication::all();
        // $this->users 				= User::company()->get();
        $this->users = User::with('company')->company()->get();
        $this->scheduleDate = $request->date;
        $this->currentApplicant = JobApplication::findOrFail($id);
        $this->interview_node = GeneralMethod::interview_mode();
        $this->application_id = $this->interview_id = $this->feedback_id = $this->interview_detail = '';
        $this->application_id = $id;
        $this->interview_detail = [];
        $this->application = JobApplication::select('job_applications.*', 'clients.name as client_name', 'jobs.title as job_title', 'client_location.city', 'jobs.company_id', 'master_status.name as master_status_name')->join('jobs', 'jobs.id', '=', 'job_applications.job_id')->join('clients', 'clients.id', '=', 'jobs.client_id')->join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')->leftjoin('client_location_jobs', 'client_location_jobs.job_id', 'jobs.id')->leftjoin('client_location', 'client_location.id', 'client_location_jobs.client_location_id')->where('job_applications.id', $this->application_id)->groupBy('job_applications.id')->first();
        $this->interviewStatus = DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();
        $this->client_contact_info = DB::table('jobs')->join('client_contact_info', 'client_contact_info.client_id', '=', 'jobs.client_id')->select('client_contact_info.id', 'client_contact_info.contact_person_name')->where('client_contact_info.id', '=', $this->application->job_id)->orderBy('client_contact_info.contact_person_name', 'ASC')->get();
        $chkAdminRole = $this->user->hasRole('admin');
        // $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        if ($chkAdminRole > 0) {
            $this->jobs = Job::select('jobs.id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city')->join('clients', 'clients.id', 'jobs.client_id')->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->where('jobs.status', '=', 'active')->where('jobs.company_id', '=', $this->user->company_id)->where('jobs.id', '!=', $this->application->job_id)->groupBy('jobs.id')->get();
        } else {
            $this->jobs = Job::select('jobs.id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city')->join('job_assign', 'job_assign.job_id', 'jobs.id')->join('clients', 'clients.id', 'jobs.client_id')->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->where('jobs.status', '=', 'active')->where('job_assign.user_id', '=', $this->user->id)->where('jobs.company_id', '=', $this->user->company_id)->where('jobs.id', '!=', $this->application->job_id)->groupBy('jobs.id')->get();
        }

        return view('admin.job-applications.copy-create', $this->data)->render();
    }

    public function storeCopy(Request $request)
    {
        $application_id = $job_id = $masterStatus = $child_status = $masterStatus_id = '';
        $application_id = $request->application_id;
        $job_id = $request->job_id;
        $masterStatus_id = $masterStatus = $request->masterStatus;
        $child_status = $request->child_status;
        $jobApplication = JobApplication::find($request->application_id);
        $jobApplicationObject = new JobApplication;
        $jobApplicationObject->full_name = $jobApplication->full_name;
        $jobApplicationObject->job_id = $job_id;
        $jobApplicationObject->status_id = 1;
        $jobApplicationObject->email = $jobApplication->email;
        $jobApplicationObject->phone = $jobApplication->phone;
        $jobApplicationObject->cover_letter = $jobApplication->cover_letter;
        $jobApplicationObject->total_exp = $jobApplication->total_exp;
        $jobApplicationObject->total_exp = $jobApplication->total_exp;
        $jobApplicationObject->rel_exp = $jobApplication->rel_exp;
        $jobApplicationObject->passport = $jobApplication->passport;
        $jobApplicationObject->ctc = $jobApplication->ctc;
        $jobApplicationObject->ectc = $jobApplication->ectc;
        $jobApplicationObject->current_location = $jobApplication->current_location;
        $jobApplicationObject->current_designation = $jobApplication->current_designation;
        $jobApplicationObject->current_company = $jobApplication->current_company;
        $jobApplicationObject->skills = $jobApplication->skills;
        $jobApplicationObject->master_status_id = $masterStatus_id;
        $jobApplicationObject->column_priority = 0;
        $submitted_by = '';
        $submitted_by = auth()->user()->id;
        if (! empty($submitted_by)) {
            $jobApplicationObject->submitted_by = $submitted_by;
        }
        $jobApplicationObject->resume = $jobApplication->resume;
        $jobApplicationObject->np_id = $jobApplication->np_id;
        $jobApplicationObject->qualification = $jobApplication->qualification;
        $jobApplicationObject->photo = $jobApplication->photo;
        $jobApplicationObject->company_id = $jobApplication->company_id;
        $jobApplicationObject->serving_notice_period_mode = $jobApplication->serving_notice_period_mode;
        if ($jobApplication->child_status > 0) {
            $jobApplicationObject->child_status_id = $jobApplication->child_status;
        }
        $jobApplicationObject->duplicate = 1;
        $jobApplicationObject->save();
        $applicationAnsArr = DB::table('job_application_answers')->select('*')->where('job_application_id', $application_id)
        // ->where('parent', $applicationStatus->id)
        // ->orderBy('priority', 'ASC')
            ->get();
        foreach ($applicationAnsArr as $ansValue) {
            $answer = new JobApplicationAnswer;
            $answer->job_application_id = $ansValue->job_application_id;
            $answer->job_id = $ansValue->job_id;
            $answer->question_id = $ansValue->question_id;
            $answer->answer = $ansValue->answer;
            $answer->save();
        }
        $candidate_skills_valueArr = DB::table('candidate_skills_value')->select('*')->where('job_id', $job_id)->where('candidate_id', $application_id)->get();
        foreach ($candidate_skills_valueArr as $csvalue) {
            if (! empty(@$value)) {
                $values = ['job_id' => @$csvalue->job_id, 'skill_id' => @$csvalue->skill_id, 'candidate_id' => @$csvalue->candidate_id, 'skill_value' => @$csvalue->skill_value];
                DB::table('candidate_skills_value')->insert($values);
            }
        }
        $status_name = $note = '';
        $status_cnt = DB::table('candidate_status')->select('*')->where('company_id', user()->company_id)->where('parent', $masterStatus_id)->where('id', $child_status)
        // ->orderBy('priority', 'ASC')
            ->count();
        if ($status_cnt > 0 && $child_status > 0) {
            $status_row = DB::table('candidate_status')->select('*')->where('company_id', user()->company_id)->where('parent', $masterStatus_id)->where('id', $child_status)->first();
            $status_name = $status_row->name;
        }
        $masterStatusName = '';
        $masterStatusNameRow = DB::table('master_status')->select('name')->where('id', $request->masterStatus)
        // ->where('parent', $request->masterStatus)
        // ->orderBy('priority', 'ASC')
            ->first();
        $masterStatusName = $masterStatusNameRow->name;
        $insert_type = 0;
        GeneralMethod::manageTimeline(auth()->user()->id, $jobApplication->id, @$request->job_id, $masterStatus_id, $masterStatusName, 'Application Copy Successfully ', $status_name, $insert_type);

        // manageTimeline($owner_id, $application_id, $job_id ='',  $process_id='', $status_name='', $note='', $process_child_name = '', $insert_type = '')
        return Reply::dataOnly(['status' => 'success', 'message' => 'Job application copy successfully !',
            // 'email' => $application->email,
            // 'phone' => $application->phone,
            // 'resume' => route('admin.job-applications.download', $application->id)
        ]);
    }

    public function createMove(Request $request, $id)
    {
        abort_if(! $this->user->cans('add_schedule'), 403);
        $this->candidates = JobApplication::all();
        // $this->users 				= User::company()->get();
        $this->users = User::with('company')->company()->get();
        $this->scheduleDate = $request->date;
        $this->currentApplicant = JobApplication::findOrFail($id);
        $this->interview_node = GeneralMethod::interview_mode();
        $this->application_id = $this->interview_id = $this->feedback_id = $this->interview_detail = '';
        $this->application_id = $id;
        $this->interview_detail = [];
        $this->application = JobApplication::select('job_applications.*', 'clients.name as client_name', 'jobs.title as job_title', 'client_location.city', 'jobs.company_id', 'master_status.name as master_status_name')->join('jobs', 'jobs.id', '=', 'job_applications.job_id')->join('clients', 'clients.id', '=', 'jobs.client_id')->join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')->leftjoin('client_location_jobs', 'client_location_jobs.job_id', 'jobs.id')->leftjoin('client_location', 'client_location.id', 'client_location_jobs.client_location_id')->where('job_applications.id', $this->application_id)->groupBy('job_applications.id')->first();
        $this->interviewStatus = DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();
        $this->client_contact_info = DB::table('jobs')->join('client_contact_info', 'client_contact_info.client_id', '=', 'jobs.client_id')->select('client_contact_info.id', 'client_contact_info.contact_person_name')->where('client_contact_info.id', '=', $this->application->job_id)->orderBy('client_contact_info.contact_person_name', 'ASC')->get();
        // echo '<pre>'; print_r($this->application->job_id); exit;
        $chkAdminRole = $this->user->hasRole('admin');
        // $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        if ($chkAdminRole > 0) {
            $this->jobs = Job::select('jobs.id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city')->join('clients', 'clients.id', 'jobs.client_id')->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->where('jobs.status', '=', 'active')->where('jobs.company_id', '=', $this->user->company_id)->where('jobs.id', '!=', $this->application->job_id)->groupBy('jobs.id')->get();
        } else {
            $this->jobs = Job::select('jobs.id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city')->join('job_assign', 'job_assign.job_id', 'jobs.id')->join('clients', 'clients.id', 'jobs.client_id')->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->where('jobs.status', '=', 'active')->where('job_assign.user_id', '=', $this->user->id)->where('jobs.company_id', '=', $this->user->company_id)->where('jobs.id', '!=', $this->application->job_id)->groupBy('jobs.id')->get();
        }

        return view('admin.job-applications.move-create', $this->data)->render();
    }

    public function storeMove(Request $request)
    {
        $application_id = $job_id = $masterStatus = $child_status = $masterStatus_id = '';
        $application_id = $request->application_id;
        $job_id = $request->job_id;
        $masterStatus_id = $masterStatus = $request->masterStatus;
        $child_status = $request->child_status;
        $jobApplication = JobApplication::find($request->application_id);
        if ($request->masterStatus > 0) {
            $jobApplication->status_id = $request->masterStatus;
            $jobApplication->master_status_id = $request->masterStatus;
        }
        if ($request->child_status > 0) {
            $jobApplication->child_status_id = $request->child_status;
        }
        if ($request->job_id > 0) {
            $jobApplication->job_id = $request->job_id;
        }
        $submitted_by = '';
        $submitted_by = auth()->user()->id;
        if (! empty($submitted_by)) {
            $jobApplication->submitted_by = $submitted_by;
        }
        $jobApplication->save();
        $status_name = $note = '';
        $status_cnt = DB::table('candidate_status')->select('*')->where('company_id', user()->company_id)->where('parent', $masterStatus_id)->where('id', $child_status)
        // ->orderBy('priority', 'ASC')
            ->count();
        if ($status_cnt > 0 && $child_status > 0) {
            $status_row = DB::table('candidate_status')->select('*')->where('company_id', user()->company_id)->where('parent', $masterStatus_id)->where('id', $child_status)->first();
            $status_name = $status_row->name;
        }
        $masterStatusName = '';
        $masterStatusNameRow = DB::table('master_status')->select('name')->where('id', $request->masterStatus)
        // ->where('parent', $request->masterStatus)
        // ->orderBy('priority', 'ASC')
            ->first();
        $masterStatusName = $masterStatusNameRow->name;
        $insert_type = 0;
        GeneralMethod::manageTimeline(auth()->user()->id, $jobApplication->id, @$request->job_id, $masterStatus_id, $masterStatusName, 'Application Move Successfully ', $status_name, $insert_type);

        // manageTimeline($owner_id, $application_id, $job_id ='',  $process_id='', $status_name='', $note='', $process_child_name = '', $insert_type = '')
        return Reply::dataOnly(['status' => 'success', 'message' => 'Job application moved successfully !',
            // 'email' => $application->email,
            // 'phone' => $application->phone,
            // 'resume' => route('admin.job-applications.download', $application->id)
        ]);
    }

    public function updateSocurce($id)
    {
        $application_id = $id;
        $this->jobApplication = JobApplication::find($application_id);
        // $this->users 			=	User::company()->get();
        $this->users = User::with('company')->company()->get();

        return view('admin.job-applications.updateSocurce', $this->data)->render();
    }

    public function source_update(Request $request)
    {
        $application_id = '';
        $application_id = $request->application_id;
        $jobApplication = JobApplication::find($application_id);
        $jobApplication->submitted_by = $request->candidate_source;
        $jobApplication->save();
        $uRow = User::find($request->candidate_source);
        $data = '';
        $customMsg = 'success';
        $message = 'Source update successfully !';

        return Reply::dataOnly(['status' => $customMsg, 'message' => $message, 'data' => $uRow]);
    }

    public function resume_change($id)
    {
        $application_id = $id;
        $this->jobApplication = JobApplication::find($application_id);

        return view('admin.job-applications.resume_change', $this->data)->render();
    }

    public function resume_update(Request $request)
    {
        $customMsg = 'fail';
        $message = 'Please upload valid files!';
        $application_id = '';
        $application_id = $request->application_id;
        $jobApplication = JobApplication::with(['documents'])->findOrFail($application_id);
        $allowedExts = ['pdf', 'doc', 'docx'];
        $temp = explode('.', $_FILES['resume']['name']);
        $extension = end($temp);
        if (in_array($extension, $allowedExts)) {
            $customMsg = 'success';
            $message = 'Resume update successfully !';
            // $jobApplication->resume =	Files::upload($request->resume,'resumes',null,null,false);
            if ($request->hasFile('resume')) {
                $hashname = Files::upload($request->resume, 'documents/'.$jobApplication->id, null, null, false);
                $jobApplication->documents()->updateOrCreate(['documentable_type' => JobApplication::class, 'documentable_id' => $jobApplication->id, 'name' => 'Resume'], ['hashname' => $hashname]);
            }
        }
        /* if ($request->hasFile('resume')) {

            $hashname = Files::upload($request->resume, 'documents/'.$jobApplication->id, null, null, false);

            $jobApplication->documents()->updateOrCreate([
        							'documentable_type' => JobApplication::class,
        							'documentable_id' 	=> $jobApplication->id,
        							'name' 				=> 'Resume'
        						],
        						[
        							'hashname' => $hashname
        						]);

        }*/
        $jobApplication->save();

        return Reply::dataOnly(['status' => $customMsg, 'message' => $message]);
    }

    public function jobQuestion($jobID, $applicationId = null)
    {
        $this->job = Job::findOrFail($jobID);
        $this->jobQuestion = $this->job->questions()->with(['answers' => function ($query) use ($jobID, $applicationId) {
            $query->where(['job_application_id' => $applicationId, 'job_id' => $jobID]);
        },
        ])->get();
        $this->gender = ['male' => __('modules.front.male'), 'female' => __('modules.front.female'), 'others' => __('modules.front.others')];
        $view = view('admin.job-applications.job-question', $this->data)->render();
        // echo $jobID; exit;
        $options = ['job' => $this->job, 'gender' => $this->gender];
        $sections = ['section_visibility' => $this->job->section_visibility];
        if ($applicationId) {
            $application = JobApplication::select('id', 'gender', 'dob', 'country', 'state', 'city')->where('id', $applicationId)->first();
            $options = Arr::add($options, 'application', $application);
            $sections = Arr::add($sections, 'application', $application);
        }
        $requiredColumnsView = view('admin.job-applications.required-columns', $options)->render();
        $requiredSectionsView = view('admin.job-applications.required-sections', $sections)->render();
        $count = count($this->jobQuestion);
        $data = ['status' => 'success', 'view' => $view, 'requiredColumnsView' => $requiredColumnsView, 'requiredSectionsView' => $requiredSectionsView, 'count' => $count];
        if ($applicationId) {
            $data = Arr::add($data, 'application', $application);
        }

        return Reply::dataOnly($data);
    }

    public function getAdditionalDetail($job_id)
    {
        $this->job = Job::findOrFail($job_id);
        $this->job_additional_detail_count = DB::table('job_additional_detail')->select('*')->where('job_id', $job_id)->orderBy('job_additional_detail.id', 'ASC')->count();
        $this->job_additional_detail = DB::table('job_additional_detail')->select('*')->where('job_id', $job_id)->orderBy('job_additional_detail.id', 'ASC')->get();
        $view = view('admin.job-applications.job_additional_detail', $this->data)->render();
        $data = ['status' => 'success', 'view' => $view];

        return Reply::dataOnly($data);
    }

    public function resume_delete()
    {
        $application_id = $_REQUEST['application_id'];
        DB::table('documents')->where('documentable_id', $application_id)->delete();

        return Reply::dataOnly(['status' => 'success']);
        // echo '<pre>'; print_r($_REQUEST); exit;

    }

    public function interviewShow(Request $request, $id)
    {
        abort_if(! $this->user->cans('add_schedule'), 403);
        $this->candidates = JobApplication::all();
        // $this->users 				= User::company()->get();
        $this->users = User::with('company')->company()->get();
        $this->scheduleDate = $request->date;
        $this->currentApplicant = JobApplication::findOrFail($id);
        $this->interview_node = GeneralMethod::interview_mode();
        $this->application_id = $this->interview_id = $this->feedback_id = $this->interview_detail = '';
        $this->application_id = $id;
        $this->interview_detail = [];
        $this->application = JobApplication::select('job_applications.*', 'clients.name as client_name', 'jobs.title as job_title', 'client_location.city', 'jobs.company_id', 'master_status.name as master_status_name')->join('jobs', 'jobs.id', '=', 'job_applications.job_id')->join('clients', 'clients.id', '=', 'jobs.client_id')->join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')->leftjoin('client_location_jobs', 'client_location_jobs.job_id', 'jobs.id')->leftjoin('client_location', 'client_location.id', 'client_location_jobs.client_location_id')->where('job_applications.id', $this->application_id)->groupBy('job_applications.id')->first();
        $this->interviewStatus = DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();
        $this->client_contact_info = DB::table('jobs')->join('client_contact_info', 'client_contact_info.client_id', '=', 'jobs.client_id')->select('client_contact_info.id', 'client_contact_info.contact_person_name')->where('client_contact_info.id', '=', $this->application->job_id)->orderBy('client_contact_info.contact_person_name', 'ASC')->get();
        // echo '<pre>'; print_r($this->application->job_id); exit;
        $chkAdminRole = $this->user->hasRole('admin');
        // $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        if ($chkAdminRole > 0) {
            $this->jobs = Job::select('jobs.id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city')->join('clients', 'clients.id', 'jobs.client_id')->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->where('jobs.status', '=', 'active')->where('jobs.company_id', '=', $this->user->company_id)->where('jobs.id', '!=', $this->application->job_id)->groupBy('jobs.id')->get();
        } else {
            $this->jobs = Job::select('jobs.id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city')->join('job_assign', 'job_assign.job_id', 'jobs.id')->join('clients', 'clients.id', 'jobs.client_id')->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->where('jobs.status', '=', 'active')->where('job_assign.user_id', '=', $this->user->id)->where('jobs.company_id', '=', $this->user->company_id)->where('jobs.id', '!=', $this->application->job_id)->groupBy('jobs.id')->get();
        }

        // return view('admin.job-applications.move-create', $this->data)->render();
        return view('admin.job-applications.interview-show', $this->data)->render();
    }

    public function accessEmailTemplate(Request $request)
    {
        $template_id = $application_id = $template_subject = $job_title = $client_name = $job_id = '';
        $email = $template_description = $full_name = $cc_email = $submitted_by_email = $to_email = '';
        $template_id = @$request->template_id;
        $application_id = @$request->application_id;
        $email = $this->user->email;
        // echo '<pre>'; print_r($this->user->email); exit;
        $applicationRow = DB::table('job_applications')
            ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
            ->leftJoin('clients', 'clients.id', '=', 'jobs.client_id')
            ->leftJoin('companies', 'companies.id', '=', 'jobs.company_id')
            ->join('users', 'users.id', '=', 'job_applications.submitted_by')
            ->select(
                'jobs.id as job_id',
                'jobs.title',
                'jobs.job_description',
                'clients.name as client_name',
                'clients.website as client_website',
                'clients.description as client_description',
                'companies.company_name',
                'companies.website as company_website',
                'companies.description as company_description',
                'job_applications.full_name',
                'job_applications.phone',
                'users.email as submitted_by_email',
                'job_applications.email as to_email'
            )
            ->where('job_applications.id', $application_id)
            ->first();

        $compName = $applicationRow->company_name ?: ($this->user->company->company_name ?? 'Our Company');
        $cliName = $applicationRow->client_name ?: $compName;
        $jobTitleClean = $applicationRow->title ?? '';
        $full_name = $applicationRow->full_name ?? '';
        $job_id = $applicationRow->job_id ?? '';

        $dataRow = DB::table('th_email_template')->select('*')->where('th_email_template.e_temp_id', $template_id)->first();

        $videoUrl = '';
        $interviewLevel = '';
        if (! empty(@$request->interview_mode)) {
            if (@$request->interview_mode == 1) {
                $videoUrl = 'Interview Mode Phone Call';
                $interviewLevel = 'Interview Telephonic';
            }
            if (@$request->interview_mode == 2) {
                $videoUrl = @$request->videoUrl;
                $interviewLevel = 'Zoom Meeting Url: '.$videoUrl;
            }
            if (@$request->interview_mode == 3) {
                $videoUrl = @$request->videoUrl;
                $interviewLevel = 'Interview Location: '.$videoUrl;
            }
        }

        $templateContext = [
            'company_name' => $compName,
            'client_name' => $cliName,
            'job_title' => $jobTitleClean,
            'candidate_name' => $full_name,
            'candidate_email' => $applicationRow->to_email ?? '',
            'candidate_phone' => $applicationRow->phone ?? '',
            'job_description' => $applicationRow->job_description ?? '',
            'company_website' => $applicationRow->company_website ?? '',
            'client_website' => $applicationRow->client_website ?? '',
            'company_description' => $applicationRow->company_description ?? '',
            'client_description' => $applicationRow->client_description ?? '',
            'hr_name' => $this->user->name ?? '',
            'hr_email' => $this->user->email ?? '',
            'hr_phone' => $this->user->mobile ?? ($this->user->phone ?? ''),
            'interview_link' => $videoUrl,
            'interview_level' => $interviewLevel,
            'current_date' => date('d M Y'),
        ];

        $template_subject = GeneralMethod::replaceTemplateVariables($dataRow->template_subject ?? '', $templateContext);
        $template_description = GeneralMethod::replaceTemplateVariables($dataRow->template_description ?? '', $templateContext);
        $template_description = str_replace('[{VIDEOURL}]', $videoUrl, $template_description);

        $to_email = $applicationRow->to_email;
        $cc_email = $this->user->email;
        if ($applicationRow->submitted_by_email != $this->user->email) {
            $cc_email = $cc_email.' , '.$applicationRow->submitted_by_email;
        }
        $client_contact_arr = DB::table('client_contact_info')->join('client_contact_info_jobs', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')->select('client_contact_info.contact_person_name', 'client_contact_info.contact_person_email')->where('client_contact_info_jobs.job_id', $job_id)->get();
        if (! empty($client_contact_arr)) {
            foreach ($client_contact_arr as $ccData) {
                if (! empty($ccData->contact_person_email)) {
                    $cc_email = $cc_email.' , '.$ccData->contact_person_email;
                }
            }
        }

        // echo '<pre>'; print_r($client_contact_arr ); exit;
        return Reply::dataOnly(['status' => 'success', 'to_email' => $to_email, 'cc_email' => $cc_email, 'from_email' => $email, 'template_subject' => $template_subject, 'template_description' => $template_description]);
    }

    public function processFormData(Request $request)
    {
        // echo '<pre>'; print_r($_REQUEST); exit;
        $interviewers = '';
        $interviewers = @$request->interviewers;
        $this->cc_email = $this->user->email;
        // if($this->application->submitted_by_email != $this->user->email){
        //	$this->cc_email				= $this->cc_email.' , '.$this->application->submitted_by_email;
        // }
        // $client_contact_arr 		=	DB::table('client_contact_info')
        // ->join('client_contact_info_jobs', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')
        // ->select('client_contact_info.contact_person_name', 'client_contact_info.contact_person_email')
        // ->where('client_contact_info_jobs.job_id', $this->application->job_id)
        // ->get();
        if (! empty($interviewers)) {
            foreach ($interviewers as $ccData) {
                if (! empty($ccData)) {
                    $this->cc_email = $this->cc_email.' , '.$ccData;
                }
            }
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $this->cc_email]);
    }

    public function storeScheduleWithEmailTempate(Request $request)
    {
        // echo '<pre>';
        // print_r($_REQUEST);
        // exit;
        $from_email = $to_email = $cc_email = $subject = $editor = '';
        $from_email = @$request->from_email;
        $to_email = @$request->to_email;
        $cc_email = @$request->cc_email;
        $subject = @$request->subject;
        $editor = @$request->editor;
        $application_id = $interview_type = $interviewDate = $masterStatus = $child_status = $interview_mode = $interview_mode_lavel = $interviewers = '';
        $application_id = @$request->application_id;
        $interview_type = @$request->interview_type;
        $interviewDate = @$request->interviewDate;
        $masterStatus = @$request->masterStatus;
        $child_status = @$request->child_status;
        $interview_mode = @$request->interview_mode;
        $interview_mode_lavel = @$request->interview_mode_lavel;
        $interviewers = @$request->interviewers;
        // INTERVIEW SCHEDULE
        $interviewSchedule = new InterviewSchedule;
        $interviewSchedule->job_application_id = $application_id;
        if (! empty($request->interview_type)) {
            $interviewSchedule->reschedule = $interview_type;
        }
        $interviewSchedule->schedule_date = $interviewDate;
        if (! empty($masterStatus)) {
            $interviewSchedule->stage = $masterStatus;
        }
        if (! empty($child_status)) {
            $interviewSchedule->substage = $child_status;
        }
        if (! empty($interview_mode)) {
            $interviewSchedule->interview_mode = $interview_mode;
        }
        if (! empty($interview_mode_lavel)) {
            $interviewSchedule->interview_mode_lavel = $interview_mode_lavel;
        }
        if (! empty($interviewers)) {
            $interviewSchedule->interviewers = implode(',', $interviewers);
        }
        $interviewSchedule->save();
        // UPDATE JOB APPLICATION
        $intScheduleId = '';
        $intScheduleId = $interviewSchedule->id;
        $jobApplication = JobApplication::find($request->application_id);
        if ($request->masterStatus > 0) {
            $jobApplication->status_id = $request->masterStatus;
            $jobApplication->master_status_id = $request->masterStatus;
        }
        if ($request->child_status > 0) {
            $jobApplication->child_status_id = $request->child_status;
        }
        $jobApplication->save();
        // mail to candidate for inform interview schedule
        if ($_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
            // Notification::send($jobApplication, new CandidateScheduleInterview($jobApplication, $interviewSchedule));

        }
        $date = date('Y-m-d H:i:s');
        $insert_type = $request->interview_type;
        $statusRow = ApplicationStatus::findOrFail($request->masterStatus);
        $values = [
            'owner_id' => auth()->user()->id,
            'application_id' => @$request->application_id,
            'application_email' => @$jobApplication->email,
            'job_id' => @$jobApplication->job_id,
            'process_id' => @$request->masterStatus,
            'status_name' => @$statusRow->name,
            //	'process_child_name'	=> $status_name,
            'note' => 'Interview Schedule',
            'insert_type' => @$insert_type,
            'create_at' => @$date,
            'update_at' => @$date,
            'interview_schedule_date' => @$interviewDate,
            // 'interview_from_time'					=>  $request->schedule_from_time,
            // 'interview_to_time'					=>  $request->schedule_to_time,
            'interview_mode' => $request->interview_mode,
            // 'interview_mention_link'				=>  $request->url_link,
            // 'interviewers'						=>	$date,
            // 'interview_additional_email'			=>	$request->other_email,
            // 'interview_message'					=>	$request->message
        ];
        if ($request->child_status > 0) {
            $childStatus = '';
            $childStatus = DB::table('candidate_status')->select('*')->where('company_id', '=', $this->user->company_id)->where('id', $request->child_status)->first();
            $values['process_child_name'] = @$childStatus->name;
        }
        if (! empty($request->interviewers)) {
            $html = '';
            foreach ($request->interviewers as $intVal) {
                $userArr = DB::table('users')->select('name')->whereIn('id', $request->interviewers)->get();
                foreach ($userArr as $uval) {
                    $html .= ' '.$uval->name;
                }
            }
            $values['interviewers'] = @$html;
        }
        DB::table('timeline')->insert($values);

        // echo '<pre>'; print_r($_REQUEST); exit;
        return Reply::dataOnly(['status' => 'success', 'message' => 'Interview Reschedule successfully !',
            // 'email' => $application->email,
            // 'phone' => $application->phone,
            // 'resume' => route('admin.job-applications.download', $application->id)
        ]);
    }

    public function statelist($cid)
    {
        $statelist = GeneralMethod::statelist($cid);

        if ($cid == 101) {

            $Rawstatelist = DB::table('state_list')
                ->select('state_list.id', 'state_list.state as name', DB::raw('101 as country_id'))
                ->orderBy('state_list.state')
                ->get()->toArray();
            $statelist = json_decode(json_encode($Rawstatelist), true);
        }

        $custom = '<option>Select State</option>';
        foreach ($statelist as $sval) {
            $custom .= '<option value="'.$sval['id'].'">'.$sval['name'].'</option>';
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $custom]);
        // echo '<pre>'; print_r($statelist);
    }

    public function recruiter($id)
    {

        $panellist = '';
        $panellist = $_REQUEST['panellist'];
        $this->panellistArr = explode(',', $panellist);

        // echo '<pre>'; print_r($panellistArr);

        $company_id = auth()->user()->company_id;
        $this->company = DB::table('companies')->select('*')->where('id', $company_id)->first();

        $company_id = auth()->user()->company_id;
        $this->team = DB::table('users')
            ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'roles.display_name')
            ->where('type', 0)
            ->where('users.company_id', $company_id)
            ->where('status', 'active')
            ->get();

        $view = view('admin.job-applications.recruiter', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function getinterviewchild_old(Request $request)
    {

        $id = $job_id = '';
        $id = @$request->id;
        $job_id = @$request->job_id;
        $DataCount = DB::table('interview_mode_lavel')->select('*')->where('interview_mode_id', $id)->count();
        $Data = DB::table('interview_mode_lavel')->select('*')->where('interview_mode_id', $id)->get();
        if (! empty($DataCount) && $DataCount > 0) {

            if (($id == 1) || ($id == 2)) {
                $html = '<select class="form-select form-select-solid" name="interview_mode_lavel" id="interview_mode_lavels" data-style="">
													<option value="">Select mode</option>';
                foreach ($Data as $cval) {
                    $html .= '<option value= "'.$cval->id.'">'.ucfirst($cval->name).'</option>';
                    $flag = 1;
                }
                $html .= '</select>';
            } elseif ($id == 3) {
                $Data = DB::table('client_location')->select('client_location.id', 'client_location.address', 'client_location.city', 'client_location.postcode')->join('client_location_jobs', 'client_location_jobs.client_location_id', '=', 'client_location.id')->where('client_location_jobs.job_id', $job_id)->orderBy('client_location.id', 'ASC')->get();
                $html = '<select class="form-control" name="interview_mode_lavel" id="interview_mode_lavels" data-style="">
													<option value="">Select Interview Address</option>';
                foreach ($Data as $cval) {
                    $html .= '<option value= "'.@$cval->id.'">'.ucfirst(@$cval->address).'  '.ucfirst(@$cval->city).'  '.@$cval->postcode.'</option>';
                    $flag = 1;
                }
                $html .= '</select>';
            }

        } else {
            $html = '<select class="form-select form-select-solid" name="interview_mode_lavel" id="interview_mode_lavels" data-style="">
													<option value="">Select mode</option>';
            $html .= '</select>';
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $html]);
    }

    public function getinterviewchild(Request $request)
    {
        $id = $job_id = '';
        $id = @$request->id;
        $job_id = @$request->job_id;
        $Data = DB::table('interview_mode_lavel')->select('*')->where('interview_mode_id', $id)->get();

        $companyData = $this->user->company;

        if (! empty($Data)) {
            if ($id == 1) {
                $html = '<select class="form-control" name="interview_mode_lavel" id="interview_mode_lavels" data-style="" onchange="getVideoMeetingUrl(this.value, '.$id.');">
													<option value="">Select Mode</option>';
                foreach ($Data as $cval) {

                    $html .= '<option value= "'.$cval->id.'">'.ucfirst($cval->name).'</option>';

                    $flag = 1;
                }
                $html .= '</select>';
            } elseif ($id == 2) {
                $html = '<select class="form-control" name="interview_mode_lavel" id="interview_mode_lavels" data-style="" onchange="getVideoMeetingUrl(this.value, '.$id.','.$job_id.');">
													<option value="">Select Mode</option>';
                foreach ($Data as $cval) {

                    if ($cval->id == 2) {  // Google Meeting
                        if ($companyData->google_status == 1) {
                            $html .= '<option value= "'.$cval->id.'">'.ucfirst($cval->name).'</option>';
                        }
                    } elseif ($cval->id == 3) {  // Zoom Meeting
                        if ($companyData->zoom_status == 1) {
                            $html .= '<option value= "'.$cval->id.'">'.ucfirst($cval->name).'</option>';
                        }
                    } elseif ($cval->id == 5) {  // Microsoft Meeting
                        if ($companyData->microsoft_status == 1) {
                            $html .= '<option value= "'.$cval->id.'">'.ucfirst($cval->name).'</option>';
                        }
                    } else {
                        $html .= '<option value= "'.$cval->id.'">'.ucfirst($cval->name).'</option>';
                    }

                    $flag = 1;
                }
                $html .= '</select>';
            } elseif ($id == 3) {

                $Data = DB::table('client_location')
                    ->select('client_location.id', 'client_location.address', 'client_location.city', 'client_location.postcode')
                    ->join('client_location_jobs', 'client_location_jobs.client_location_id', '=', 'client_location.id')
                    ->where('client_location_jobs.job_id', $job_id)
                    ->orderBy('client_location.id', 'ASC')
                    ->get();

                $html = '<select class="form-control" name="interview_mode_lavel" id="interview_mode_lavels" data-style="" onchange="getVideoMeetingUrl(this.value, '.$id.');">
													<option value="">Select Address</option>';
                foreach ($Data as $cval) {
                    $html .= '<option value= "'.@$cval->id.'">'.ucfirst(@$cval->address).'  '.ucfirst(@$cval->city).'  '.@$cval->postcode.'</option>';
                    $flag = 1;
                }
                $html .= '</select>';
            }
        } else {
            $html = '&nbsp;-&nbsp;';
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $html]);
    }

    public function getVideoUrl(Request $request)
    {

        // echo '<pre>'; print_r($_SERVER); exit;
        // Google Meeting  	= 	2
        // Zoom Meeting 		=	3
        // Microsoft Meeting =	5
        $currentId = $ParentId = $mode = $companyData = $zoom_email = $job_id = $response = $interviewDate = '';
        $currentId = @$request->currentId;
        $ParentId = @$request->ParentId;
        $companyData = $this->user->company;

        $message = '';
        $interviewDate = @$request->interviewDate;

        $jobs = Job::select('jobs.id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'clients.name as client_name')
            ->join('clients', 'clients.id', 'jobs.client_id')
                                            // ->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
                                            // ->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                                            // ->where('jobs.status', '=', 'active')
                                            // ->where('jobs.company_id', '=', $this->user->company_id)
            ->where('jobs.id', '!=', $job_id)
            ->groupBy('jobs.id')
            ->first();

        $jobtitle = $jobs->client_name.'-'.$jobs->title;

        if ($currentId == 3) { // Zoom Meeting

            $data = [];
            $data['topic'] = 'Interview Schedule For ('.$jobtitle.') and time - '.$interviewDate;
            $data['start_date'] = date('Y-m-d h:i:s', strtotime($interviewDate));
            $data['duration'] = 30;
            $data['type'] = 2;
            $data['password'] = '123456';
            $zoom_api_key = $companyData->zoom_api_key;
            $zoom_api_secret = $companyData->zoom_api_secret;
            $zoom_email = $companyData->zoom_email;
            Zoom::setting($zoom_api_key, $zoom_api_secret);

            $response = Zoom::createMeeting($data, $zoom_email);

            $message = 'Zoom Meeting Url generated successfully !';
            $mode = 'zoom';

            // echo '<pre>'; print_r($response); exit;
        } else {
            $response = '';
            $message = 'Underdevelopment !';
            $mode = 'other';
        }

        return Reply::dataOnly([
            'status' => 'success',
            'message' => $message,
            'output' => $response,
            'mode' => $mode,
            // 'email' => $application->email,
            // 'phone' => $application->phone,
            // 'resume' => route('admin.job-applications.download', $application->id)
        ]);

    }

    public function stage_body_content($id)
    {
        $current_company_id = auth()->user()->company_id;
        $this->qualification = '';
        $this->application = '';

        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $this->intModeArr = GeneralMethod::interview_mode();
        $this->application = JobApplication::select('job_applications.id', 'job_applications.submitted_by', 'job_applications.company_id', 'job_applications.np_id', 'job_applications.serving_notice_period_mode', 'job_applications.full_name', 'job_applications.email', 'job_applications.phone', 'job_applications.secondary_mobile', 'job_applications.photo', 'job_applications.resume', 'job_applications.rating', 'job_applications.skype_id', 'job_applications.cover_letter', 'job_applications.job_id', 'job_applications.status_id', 'job_applications.is_viewed', 'job_applications.column_priority', 'job_applications.created_at', 'job_applications.updated_at', 'job_applications.deleted_at', 'job_applications.total_exp', 'job_applications.rel_exp', 'job_applications.passport',
            'job_applications.current_ctc_cur', 'job_applications.current_ectc_cur', 'job_applications.current_ctc_mode', 'job_applications.current_ectc_mode',
            'job_applications.ctc', 'job_applications.ectc',

            'job_applications.current_location', 'job_applications.current_designation', 'job_applications.current_company', 'job_applications.process_id', 'job_applications.qualification', 'job_applications.master_status_id', 'job_applications.child_status_id', 'job_applications.secondrary_email', 'job_applications.secondary_mobile', 'job_applications.skills', 'clients.name as client_name', 'jobs.title', 'users.name as posted_by',
            'master_status.name as master_status_name', 'job_applications.proposed_ctc', 'job_applications.any_offer', 'job_applications.document', 'job_applications.sub_for_approval', 'job_applications.offer_approved', 'job_applications.offer_release', 'job_applications.offer_acceptance', 'job_applications.joined', 'job_applications.joined_date',

            'job_applications.city', 'job_applications.state', 'job_applications.country', 'job_applications.city_id',
            'state_list.state as state_name',

            'users.name as submitted_by_name', 'users.company_id as submitted_by_company_id'
        )
            ->leftJoin('jobs', 'jobs.id', '=', 'job_applications.job_id')
            ->leftjoin('clients', 'clients.id', 'jobs.client_id')
            ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
            ->leftjoin('users', 'users.id', 'job_applications.submitted_by')
            ->leftjoin('state_list', 'state_list.id', 'job_applications.state')
            ->leftJoin('master_status', 'master_status.id', '=', 'job_applications.status_id');
        $this->application = $this->application->where('job_applications.id', $id);
        $this->application = $this->application->first();

        if (! empty($this->application->qualification) && $this->application->qualification > 0) {
            $this->qualification = DB::table('qualification_master')->select('name')->where('id', $this->application->qualification)->first();
        }

        $submitted_by_company_id = $this->application->submitted_by_company_id;
        $this->company = DB::table('companies')->select('company_name')->where('id', $submitted_by_company_id)->first();

        $this->master_status = DB::table('master_status')->select('*')->orderBy('master_status.priority', 'ASC')->get();

        $master_status_id = $this->application->master_status_id;
        $this->candidate_status_Arr = DB::table('candidate_status')
            ->select('*')
            ->where('parent', $master_status_id)
            ->where('company_id', $current_company_id)
            ->orderBy('candidate_status.priority', 'ASC')
            ->get();

        $this->schedule = DB::table('interview_schedules')->select('interview_schedules.id', 'interview_schedules.company_id', 'interview_schedules.job_application_id', 'interview_schedules.schedule_date', 'interview_schedules.status', 'interview_schedules.user_accept_status', 'interview_schedules.created_at', 'interview_schedules.updated_at')
            ->where('job_application_id', $this->application->id)
            ->orderBy('id', 'DESC')
            ->get();

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
            if (! empty($childStatus)) {
                $this->child_status_name = @$childStatus->name;
            }
        }
        $this->company_id = $this->user->company_id;

        $current_ctc_cur = '';
        $current_ctc_cur = $currencySymbole[$this->application->current_ctc_cur];

        $current_ectc_cur = '';
        $current_ectc_cur = $currencySymbole[$this->application->current_ectc_cur];

        $ctc = '';
        $ctc = GeneralMethod::thousandsCurrencyFormat($this->application->ctc);
        $ectc = '';
        $ectc = GeneralMethod::thousandsCurrencyFormat($this->application->ectc);

        $current_ctc_mode = '';
        $current_ctc_mode = substr($salaryMode[$this->application->current_ctc_mode], 0, 1);

        $current_ectc_mode = '';
        $current_ectc_mode = substr($salaryMode[$this->application->current_ectc_mode], 0, 1);

        $this->current_ctc = @$current_ctc_cur.@$ctc.'/'.@$current_ctc_mode;
        $this->expected_ctc = @$current_ectc_cur.@$ectc.'/'.@$current_ectc_mode;

        $country = '';
        $this->row_country_name = '';

        if (empty($this->application->country)) {
            $this->row_country_name = '----';
        } else {
            if (! empty($this->application->country)) {
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

        $this->row_city_name = '';
        if ($this->application->country == 101) {
            $row_city = DB::table('city_list')->select('*')->where('city_id', $this->application->city_id)->first();
            $this->row_city_name = @$row_city->city;
        } else {
            $this->row_city_name = $this->application->city;
        }

        $view = view('admin.job-applications.stage_body_content', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function child_stage($id)
    {
        $company_id = '';
        $company_id = auth()->user()->company_id;

        $master_status_id = $id;
        $candidate_status_Arr = DB::table('candidate_status')
            ->select('*')
            ->where('parent', $master_status_id)
            ->where('company_id', $company_id)
            ->orderBy('candidate_status.priority', 'ASC')
            ->get();

        $html = '';
        if (! empty($candidate_status_Arr)) {
            $cnt = 1;
            foreach ($candidate_status_Arr as $cstatus) {
                /*$html				.=	'<div class="div'.$cnt.' d-inline-block w-14 px-2">
                                            <input type="radio" class="btn-check" name="sub_stages" value="'.@$cstatus->id.'" id="screening'.$cnt.'">
                                            <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="screening1">
                                                <span class="d-block fw-bold text-start"><span class="text-dark-800 fw-bolder d-block fs-5">'.@$cstatus->name.'</span>
                                            </label>
                                        </div>';*/

                $html .= '<div class="div'.$cnt.' d-inline-block w-14 px-2">
								<input type="radio" class="btn-check" name="sub_stages" value="'.@$cstatus->id.'" id="screening'.$cnt.'">
								<label
									class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
									for="screening'.$cnt.'"> <span class="d-block fw-bold text-start"> <span
											class="text-dark-800 fw-bolder d-block fs-5">'.@$cstatus->name.'</span> </span>
								</label>
							</div>';

                $cnt++;
            }
        }

        // $view = view('admin.job-applications.stage_body_content', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'html' => $html]);

    }

    public function stage_submit(Request $request)
    {

        $company_id = '';
        $company_id = auth()->user()->company_id;

        $application_id = $master_status = $sub_stages = $comment = '';
        $prev_process_name = $prev_process_child_name = '';
        $application_id = @$request->application_id;
        $master_status = @$request->master_status;
        $sub_stages = @$request->sub_stages;
        $comment = @$request->comment;

        $jobApplication = JobApplication::find($application_id);
        if (! empty(@$master_status)) {
            if ($jobApplication->master_status_id >= 0) {
                $masterStatus = DB::table('master_status')->select('master_status.name')->where('id', $jobApplication->master_status_id)->first();
                $prev_process_name = @$masterStatus->name;
            }
            $jobApplication->master_status_id = @$master_status;
        }

        if ($jobApplication->child_status_id > 0) {
            $subProcess = DB::table('candidate_status')
                ->select('candidate_status.name')
                ->where('candidate_status.company_id', $company_id)
                ->where('candidate_status.id', $jobApplication->child_status_id)
                ->first();
            $prev_process_child_name = @$subProcess->name;
        }

        if (! empty(@$sub_stages)) {
            $jobApplication->child_status_id = @$sub_stages;
        } else {
            $jobApplication->child_status_id = '';
        }

        $date = date('Y-m-d H:i:s');
        $values = [
            'owner_id' => auth()->user()->id,
            'application_id' => @$application_id,
            'application_email' => @$jobApplication->email,
            'job_id' => @$jobApplication->job_id,
            'process_id' => @$master_status,
            'note' => 'Process stage and sub stage update',
            'create_at' => @$date,
            'update_at' => @$date,

            'timeline_type' => 0,
            'prev_process_name' => $prev_process_name,
            'prev_process_child_name' => $prev_process_child_name,

            // 'interview_mode_child_level'=>  $interview_mode_lavel

        ];

        if (@$master_status > 0) {
            $masterStatus = DB::table('master_status')->select('master_status.name')->where('id', $master_status)->first();
            $values['status_name'] = @$masterStatus->name;
        }
        if (@$sub_stages > 0) {
            $masterStatus = DB::table('candidate_status')->select('candidate_status.name')->where('candidate_status.company_id', $company_id)->where('candidate_status.id', $sub_stages)->first();
            $values['process_child_name'] = @$masterStatus->name;
        }
        if (! empty(@$comment)) {
            $values['comment'] = @$comment;
        }
        DB::table('timeline')->insert($values);
        $jobApplication->save();

        if ($master_status == 5) {

            $rowData = DB::table('job_applications')->select('*')->where('id', $application_id)->first();
            $values = [
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

                'job_id' => $rowData->job_id,
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
                'updated_at' => date('Y-m-d'),

            ];
            DB::table('offer_job_applications')->insert($values);

        }

        return Reply::dataOnly(['status' => 'success', 'message' => 'Application stage update successfully !']);

    }

    public function storeSchedule(Request $request)
    {

        // echo '<pre>'; print_r($_REQUEST); exit;

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
        $interviewSchedule = new InterviewSchedule;
        $interviewSchedule->job_application_id = $application_id;
        if (! empty($request->interview_type)) {
            $interviewSchedule->reschedule = $interview_type;
        }
        if (! empty($interview_mode)) {
            $interviewSchedule->interview_mode = $interview_mode;
        }
        if (! empty($interview_mode_lavel)) {
            $interviewSchedule->interview_mode_lavel = $interview_mode_lavel;
        }
        if (! empty($master_status_id)) {
            $interviewSchedule->stage = $master_status_id;
        }
        if (! empty($child_status_id)) {
            $interviewSchedule->substage = $child_status_id;
        }
        if (! empty($panellist)) {
            $interviewSchedule->interviewers = $panellist;   // implode(',', $panellist);
        }
        if (! empty($interview_created_by)) {
            $interviewSchedule->interview_created_by = $this->user->id;
        }
        $interviewSchedule->schedule_date = $interviewDate;
        $interviewSchedule->save();

        $interviewSchedule_id = '';
        $interviewSchedule_id = $interviewSchedule->id;

        // mail to candidate for inform interview schedule
        if ($_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
            // Notification::send($jobApplication, new CandidateScheduleInterview($jobApplication, $interviewSchedule));
        }

        $jobApplication = JobApplication::find($request->application_id);
        $date = date('Y-m-d H:i:s');
        $insert_type = $request->interview_type;

        $statusRow = ApplicationStatus::findOrFail($request->master_status_id);

        $interview_mode_lavel = '';
        $interview_mode_lavel = @$request->interview_mode_lavel;
        if ($interview_mode_lavel > 0) {
            $intModeLbl = DB::table('interview_mode_lavel')
                ->select('interview_mode_lavel.name')
                ->where('interview_mode_lavel.id', $interview_mode_lavel)
                ->first();
            $interview_mode_lavel = $intModeLbl->name;
        }

        $values = [
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
            'interview_mode_child_level' => $interview_mode_lavel,

        ];

        if (! empty($request->url) && $request->url != '') {
            $values['interview_mention_link'] = @$request->url;
        }
        if (! empty($request->panellist) && $request->panellist != '') {
            $values['interviewers'] = @$request->panellist;
        }

        if ($request->child_status_id > 0) {
            $childStatus = '';
            $childStatus = DB::table('candidate_status')->select('*')->where('company_id', '=', $this->user->company_id)->where('id', $request->child_status_id)->first();
            $values['process_child_name'] = @$childStatus->name;
        }

        /* if(!empty($request->panellist))
         {
             $html = '';
             foreach ($request->panellist as $intVal) {
                 $userArr = DB::table('users')->select('name')->whereIn('id', $request->panellist)->get();
                 foreach ($userArr as $uval) {
                     $html.= ' ' . $uval->name;
                 }
             }
             $values['interviewers'] = @$html;
         }*/

        if (! empty($panellist)) {
            $values['interviewers'] = $panellist;
        }
        $values['interview_schedules_id'] = $interviewSchedule_id;
        DB::table('timeline')->insert($values);

        return Reply::dataOnly(['status' => 'success', 'message' => 'Interview Reschedule successfully !']);

    }

    public function emailSchedule(Request $request)
    {

        // echo '<pre>'; print_r($_REQUEST); exit;

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
        $interviewSchedule = new InterviewSchedule;
        $interviewSchedule->job_application_id = $application_id;
        if (! empty($master_status_id)) {
            $interviewSchedule->stage = $master_status_id;
        }
        if (! empty($child_status_id)) {
            $interviewSchedule->substage = $child_status_id;
        }
        if (! empty($request->interview_type)) {
            $interviewSchedule->reschedule = $interview_type;
        }
        $interviewSchedule->schedule_date = $interviewDate;
        if (! empty($interview_mode)) {
            $interviewSchedule->interview_mode = $interview_mode;
        }
        if (! empty($interview_mode_lavel)) {
            $interviewSchedule->interview_mode_lavel = $interview_mode_lavel;
        }
        if (! empty($panellist)) {
            $interviewSchedule->interviewers = $panellist;   // implode(',', $panellist);
        }
        if (! empty($interview_created_by)) {
            $interviewSchedule->interview_created_by = $this->user->id;
        }
        $interviewSchedule->save();

        $interviewSchedule_id = '';
        $interviewSchedule_id = $interviewSchedule->id;

        // UPDATE JOB APPLICATION
        /*$intScheduleId 		= '';
        $intScheduleId 		= $interviewSchedule->id;
        $jobApplication 	= JobApplication::find($request->application_id);
        if ($request->masterStatus > 0) {
            $jobApplication->status_id = $request->masterStatus;
            $jobApplication->master_status_id = $request->masterStatus;
        }
        if ($request->child_status > 0) {
            $jobApplication->child_status_id = $request->child_status;
        }
        $jobApplication->save();*/

        // mail to candidate for inform interview schedule
        if ($_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
            // Notification::send($jobApplication, new CandidateScheduleInterview($jobApplication, $interviewSchedule));

        }
        $jobApplication = JobApplication::find($request->application_id);
        $date = date('Y-m-d H:i:s');
        $insert_type = $request->interview_type;
        $statusRow = ApplicationStatus::findOrFail($request->master_status_id);
        $values = [
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

        ];

        /*if ($request->child_status_id > 0)
        {
            $childStatus = '';
            $childStatus = DB::table('candidate_status')->select('*')->where('company_id', '=', $this->user->company_id)->where('id', $request->child_status_id)->first();
            $values['process_child_name'] = @$childStatus->name;
        }
        if (!empty($panellist)) {
            $values['interviewers'] = $panellist;
        }*/

        if (! empty($request->url) && $request->url != '') {
            $values['interview_mention_link'] = @$request->url;
        }
        if (! empty($request->panellist) && $request->panellist != '') {
            $values['interviewers'] = @$request->panellist;
        }
        if ($request->child_status_id > 0) {
            $childStatus = '';
            $childStatus = DB::table('candidate_status')->select('*')->where('company_id', '=', $this->user->company_id)->where('id', $request->child_status_id)->first();
            $values['process_child_name'] = @$childStatus->name;
        }

        /*if (!empty($panellist)) {
            $values['interviewers'] 		= $panellist;
        }*/
        $values['interview_schedules_id'] = $interviewSchedule_id;

        DB::table('timeline')->insert($values);

        // START SEND EMAIL
        // $file 			= $path.$filename;
        // $content 		= file_get_contents( $file);
        // $content 		= chunk_split(base64_encode($content));

        // $file_name 		= basename($file);

        $from_name = 'Vibhanshu';
        $from_mail = 'vibhanshu@4bel.com';
        $replyto = 'support@4bell.com';
        $uid = md5(uniqid(time()));
        $message = $editor;
        $mailto = 'abu@4bell.com';

        // Always set content-type when sending HTML email
        $headers = 'MIME-Version: 1.0'."\r\n";
        $headers .= 'Content-type:text/html;charset=UTF-8'."\r\n";

        // More headers
        $headers .= 'From: $from_mail'."\r\n";
        $headers .= 'Cc: $replyto'."\r\n";

        mail($mailto, $subject, $message, $headers);

        // END SEND EMAIL

        return Reply::dataOnly(['status' => 'success', 'message' => 'Interview Reschedule successfully !']);

    }

    public function getGoogleVideoUrl(Request $request)
    {

        date_default_timezone_set('Asia/Calcutta');
        $strtotime = strtotime(date('Y-m-d H:i:s'));

        $currentId = $ParentId = $mode = $companyData = $zoom_email = $job_id = $response = $interviewDate = '';
        $currentId = @$request->currentId;
        $ParentId = @$request->ParentId;
        $companyData = $this->user->company;

        $message = '';
        $interviewDate = @$request->interviewDate;
        $start_date = date("Y-m-d\TH:i:s", strtotime($interviewDate));

        $newtimestamp = strtotime($interviewDate.'+ 60 minute');
        $end_date = date("Y-m-d\TH:i:s", $newtimestamp);

        $jobs = Job::select('jobs.id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'clients.name as client_name')
            ->join('clients', 'clients.id', 'jobs.client_id')
            ->where('jobs.id', '!=', $job_id)
            ->groupBy('jobs.id')
            ->first();

        $jobtitle = $jobs->client_name.'-'.$jobs->title;
        if ($currentId == 2) { // Google Meeting

            $data = [];
            $data['topic'] = 'Interview Schedule For ('.$jobtitle.') and time - '.$interviewDate;

            $data['duration'] = 30;
            $data['type'] = 2;
            $data['password'] = '123456';

            $company_id = $this->user->company->id;
            $company = $this->user->company;
            $google_client_id = $company->google_client_id;
            $google_client_secret = $company->google_client_secret;
            $google_access_token = $company->google_access_token;
            $google_expires_in = $company->google_expires_in;
            $google_refresh_token = $company->google_refresh_token;

            if ($strtotime > $google_expires_in) {

                $token = Google::GetRefreshedAccessToken($google_client_id, $google_refresh_token, $google_client_secret);
                $google_expires_in = $strtotime + $token['expires_in'];
                if ($token['expires_in'] > 0) {
                    $google_access_token = $token['access_token'];
                    $update = [
                        'google_expires_in' => @$google_expires_in,
                        'google_access_token' => @$google_access_token,
                    ];
                    DB::table('companies')->where('id', $company_id)->limit(1)->update($update);
                }
            }

            $company = $this->user->company;

            $status_row = DB::table('companies')
                ->select('*')
                ->where('id', $company_id)
                ->first();
            $google_access_token = $status_row->google_access_token;

            $user_timezone = Google::GetUserCalendarTimezone(@$google_access_token);

            $all_day = 0;
            $event_time = ['start_time' => $start_date, 'end_time' => $end_date];
            $eventDetail = Google::CreateCalendarEvent('primary', $data['topic'], $all_day, $event_time, $user_timezone, @$company->google_access_token);

            $message = 'Google Meeting Url generated successfully !';
            $mode = 'google';
        } else {
            $response = '';
            $message = 'Underdevelopment !';
            $mode = 'other';
        }

        return Reply::dataOnly([
            'status' => 'success',
            'message' => $message,
            'output' => $eventDetail,
            'mode' => $mode,
            // 'email' => $application->email,
            // 'phone' => $application->phone,
            // 'resume' => route('admin.job-applications.download', $application->id)
        ]);

    }
}
