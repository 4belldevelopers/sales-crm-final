<?php

/***#######################################################
Controller    	: 	AdminAppTrackerController
Purpose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/

namespace App\Http\Controllers\Admin;
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
use App\Notifications\CandidateScheduleInterview;
use App\Notifications\ScheduleInterview;
use App\Models\Skill;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
//use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Models\NoticePeriod;
use App\Models\JobSkill;
use Mail;
use App\Exports\AppTrackerExport;
use App\Services\ReferenceDataCache;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
class AdminAppTrackerController extends AdminBaseController {
    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('CV / Resume Received');
        $this->pageIcon = 'icon-user';
    }
    public function index($id, $mstatus_id) {
	
        //abort_if(!$this->user->cans('view_job_applications'), 403);
    
	    $this->skillData = array();
        $this->statusArr = array();
        $this->candStatusClass = array();
        if ($id > 0) {
            $this->boardColumns = ReferenceDataCache::applicationStatuses();
            $this->locations = JobLocation::all();
            //$this->jobs 			= Job::all();
            $this->skills = Skill::all();
            $this->clients = Client::all();
            //$this->users 			= User::company()->get();
            $this->noticeperiod = NoticePeriod::all();
            $this->job = Job::findOrFail($id);
            //echo '<pre>'; print_r($this->job); exit;
            if ($mstatus_id != 'all') {
                $this->statusArr 			= ApplicationStatus::findOrFail($mstatus_id);
                $this->candStatusClass 		= GeneralMethod::candidateStatusColorCode($this->statusArr->name);
            } else {
                $this->candStatusClass 		= GeneralMethod::candidateStatusColorCode('all');
            }
            if ($this->job->client_id > 0) {
                $this->jobclient = Client::findOrFail($this->job->client_id);
                $contactHtml = '';
                $this->client_loc_arr = DB::table('client_location_jobs')->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->join('countries', 'countries.id', '=', 'client_location.country_id')->select('client_location.address', 'client_location.city', 'client_location.postcode', 'countries.country_name')
                //->where('jobs.status', 'active')
                ->where('client_location_jobs.job_id', $id)->get();
            }
            $this->skillData = DB::table('job_skills')->join('skills', 'job_skills.skill_id', '=', 'skills.id')->select('skills.id', 'skills.name')->where('job_skills.job_id', '=', $id)->orderBy('skills.name', 'ASC')->get();
            $this->mstatus_id = $mstatus_id;
            $this->job_id = $id;
			
            $this->applicationCounter = DB::table('job_applications')
										->select(
													DB::raw("count(job_applications.id) as allCandidate"), 
													DB::raw("SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing"), 
													DB::raw("SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening"), 
													DB::raw("SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview"), 
													DB::raw("SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation"), 
													DB::raw("SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered"), 
													DB::raw("SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined"), 
													DB::raw("SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected")
												)
										->where('job_applications.job_id', '=', $id)->groupBy('job_applications.job_id')
										->first();
        } else {
            $this->boardColumns = ReferenceDataCache::applicationStatuses();
            $this->locations = JobLocation::all();
            $this->jobs = Job::all();
            $this->skills = Skill::all();
            $this->clients = Client::all();
            //$this->users 			= User::company()->get();
            $this->noticeperiod = NoticePeriod::all();
            //echo '<pre>'; print_r($this->users ); exit;
            
        }
        return view('admin.apptracker.index', $this->data);
    }
	
	
    public function index2(Request $request) {
        abort_if(!$this->user->cans('view_job_applications'), 403);
        $date = Carbon::now();
        $startDate = $date->subDays(30)->format('Y-m-d');
        $endDate = Carbon::now()->format('Y-m-d');
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
        }, 'applications.schedule'])->get();
        $boardStracture = [];
        foreach ($this->boardColumns as $key => $column) {
            $boardStracture[$column->id] = [];
            foreach ($column->applications as $application) {
                $boardStracture[$column->id][] = $application->id;
            }
        }
        $this->boardStracture = json_encode($boardStracture);
        $this->currentDate = Carbon::now()->timestamp;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        if ($request->ajax()) {
            $view = view('admin.cvtracker.board-data', $this->data)->render();
            return Reply::dataOnly(['view' => $view]);
        }
        return view('admin.apptracker.board', $this->data);
    }
    public function create() {
        abort_if(!$this->user->cans('add_job_applications'), 403);
        $candidateCount = JobApplication::count();
        if ($candidateCount > 0 && $this->activePackage->package->no_of_candidate_access !== null && $this->activePackage->package->no_of_candidate_access <= $candidateCount) {
            $this->message = __('modules.subscription.candidatePurchase');
            return view('admin.subscription.ask_purchase', $this->data);
        }
        $this->jobs = Job::activeJobs();
        return view('admin.apptracker.create', $this->data);
    }
    public function jobQuestion($jobID) {
        $this->jobQuestion = JobQuestion::with(['question'])->where('job_id', $jobID)->get();
        $view = view('admin.cvtracker.job-question', $this->data)->render();
        $count = count($this->jobQuestion);
        return Reply::dataOnly(['status' => 'success', 'view' => $view, 'count' => $count]);
    }
    public function edit($id) {
        abort_if(!$this->user->cans('edit_job_applications'), 403);
        $this->statuses = ApplicationStatus::all();
        $this->application = JobApplication::find($id);
        $this->jobQuestion = JobQuestion::with(['question'])->where('job_id', $this->application->job_id)->get();
        return view('admin.cvtracker.edit', $this->data);
    }
	
    public function data(Request $request) {
	
	
		// abort_if(!$this->user->cans('view_job_applications'), 403);		
		 
		$salaryMode 		=	GeneralMethod::salaryMode();
		$currencySymbole 	=	GeneralMethod::currencySymbole(); 
		
		
        $mstatus_id 		= '';
        $mstatus_id 		= $request->mstatus_id;
        $interviewStatus 	= array();
        $interviewStatus 	= DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();
       
	   
       
	    $chkAdminRole 		= DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        $feedback_status 	= '';
        $feedback_status = ReferenceDataCache::feedbackStatus();
		
        $jobApplications 	= JobApplication::select('job_applications.*', 'jobs.title', 'job_locations.location', 'application_status.status', 'clients.name as client_name', 
		'users.name as posted_by', 'users.image as posted_by_image', 
        //'notice_periods.np_title',
        'qualification_master.name as qualification_name', 'master_status.name', 'master_status.css_style',
		'state_list.state as state_name',
		'city_list.city as indian_city_name'
		
		)
		->join('jobs', 'jobs.id', 'job_applications.job_id')
		->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
		->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')
		->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
		->leftjoin('clients', 'clients.id', 'jobs.client_id')
		->leftjoin('users', 'users.id', 'job_applications.submitted_by')
        //->leftjoin('notice_periods', 'notice_periods.np_id', 'job_applications.np_id')
        ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
		->leftjoin('application_status', 'application_status.id', 'job_applications.status_id')
		
		->leftjoin('state_list', 'state_list.id', 'job_applications.state')
		->leftjoin('city_list', 'city_list.city_id', 'job_applications.city_id');
        
		
		// Filter by status
		
       /* if ($request->status != 'all' && $request->status != '') {
            $jobApplications = $jobApplications->where('job_applications.master_status_id', $request->status);
        }*/
		
        // Filter By jobs
       /* if ($request->jobs != 'all' && $request->jobs != '') {
            $jobApplications = $jobApplications->where('job_applications.job_id', $request->jobs);
        }*/
		
       /* // Filter By skills
        if ($request->skill != 'all' && $request->skill != '') {
            $jobApplications = $jobApplications->whereIn('job_skills.skill_id', explode(',', $request->skill));
        }
        // Filter by location
        if ($request->location != 'all' && $request->location != '') {
            $jobApplications = $jobApplications->where('jobs.location_id', $request->location);
        }
        // Filter by StartDate
        if ($request->startDate != null && $request->startDate != '') {
            $jobApplications = $jobApplications->where(DB::raw('DATE(job_applications.`created_at`)'), '>=', "$request->startDate");
        }
        // Filter by EndDate
        if ($request->endDate != null && $request->endDate != '') {
            $jobApplications = $jobApplications->where(DB::raw('DATE(job_applications.`created_at`)'), '<=', "$request->endDate");
        }
        //CUSTOM FILTER
        //FULLNAME
        if ($request->applicant_search != 'all' && $request->applicant_search != '') {
            $jobApplications = $jobApplications->where('job_applications.full_name', 'like', '%' . $request->applicant_search . '%');
        }
        //Client
        if ($request->clients != 'all' && $request->clients != '') {
            $jobApplications = $jobApplications->where('jobs.client_id', $request->clients);
        }
        //Email
        if ($request->email_search != 'all' && $request->email_search != '') {
            $jobApplications = $jobApplications->where('job_applications.email', 'like', '%' . $request->email_search . '%');
        }
        // SUBMITTED
        if ($request->source != 'all' && $request->source != '') {
            $jobApplications = $jobApplications->where('job_applications.submitted_by', $request->source);
        }
        // PHONE
        if ($request->phone_search != '') {
            $jobApplications = $jobApplications->where('job_applications.phone', 'like', '%' . $request->phone_search . '%');
        }
        // NOTICE PERIOD
        if ($request->search_passport != 'all' && $request->search_passport != '') {
            $jobApplications = $jobApplications->where('job_applications.passport', $request->search_passport);
        }
        // NOTICE PERIOD
        if ($request->search_np_id != '' && $request->search_np_id > 0) {
            $jobApplications = $jobApplications->where('job_applications.np_id', $request->search_np_id);
        }
        // CURRENT DESIGNATION
        if ($request->sea_cur_designation != '') {
            $jobApplications = $jobApplications->where('job_applications.current_designation', 'like', '%' . $request->sea_cur_designation . '%');
        }
        // CURRENT COMPANY
        if ($request->sea_cur_company != '') {
            $jobApplications = $jobApplications->where('job_applications.current_company', 'like', '%' . $request->sea_cur_company . '%');
        }
        // CURRENT LOCATION
        if ($request->sea_cur_location != '') {
            $jobApplications = $jobApplications->where('job_applications.current_location', 'like', '%' . $request->sea_cur_location . '%');
        }
        if ($request->jobid != '') {
            $jobApplications = $jobApplications->where('job_applications.job_id', $request->jobid);
        }       
		*/
		
		
		 if (\request('search') != "") {
            $search = request('search');
            $jobApplications = $jobApplications->where('job_applications.full_name', 'LIKE', "%$search%");
        }
		
		 if ($request->mstatus_id != 'all') {
            $jobApplications = $jobApplications->where('job_applications.master_status_id', $request->mstatus_id);
        }
		
		if ($request->jobid != '') {
            $jobApplications = $jobApplications->where('job_applications.job_id', $request->jobid);
        }
		
		//$jobApplications = $jobApplications->where('job_applications.job_id', 77);
        $jobApplications = $jobApplications->groupBy('job_applications.id');
		
		//echo $jobApplications->toSql(); exit; 		
		
        $dataTable 		= DataTables::of($jobApplications)		
				
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
			
$duplicate = $row->duplicate ?? 0;
$flag = 0;
if ($duplicate == 1) {
    $flag = $duplicate;
}			
						
			
if($flag ==1)
{
return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
<a href="javascript:void(0);"  data-row-id="'. @$row->id .'" class="show-application-detail btn btn-icon btn-light-danger pulse pulse-danger mr-5" data-toggle="tooltip" title="Duplicate">
<span class="fs-3">'. strtoupper(@$strVal) .'</span>
<span class="pulse-ring"></span>
</a>
</div>                  
<div class="d-flex flex-column">
<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-danger fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="'.ucwords(@$row->full_name) .'">' .ucwords(substr(@$row->full_name, 0, 7)).$covid. '</a>
<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-danger fw-bold d-block fs-7 text-hover-primary show-application-detail" data-toggle="tooltip" title="' .ucwords(@$row->current_designation). '">' .ucwords(substr(@$row->current_designation, 0, 7)). '</a>
</div>';
}
else
{
return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
<a href="javascript:void(0);"   data-row-id="' . @$row->id . '" class="show-application-detail">
<div class="symbol-label ' . @$bgTextDecore . ' border-radius-5">' . strtoupper(@$strVal) . '</div>
</a>
</div>                  
<div class="d-flex flex-column">
<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="'.ucwords(@$row->full_name).'">' . ucwords(substr(@$row->full_name, 0, 10)) .$covid. '</a>
<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-muted fw-bold d-block fs-7 text-hover-primary show-application-detail" data-toggle="tooltip" title="'.ucwords(@$row->current_designation).'">' . ucwords(substr(@$row->current_designation, 0, 10)) . '</a>
</div>';
}
		
		
})	
		
		
	
		
		
->editColumn('Contacts', function ($row) {


$duplicate = $row->duplicate ?? 0;
$flag = 0;
if ($duplicate == 1) {
    $flag = $duplicate;
}		


if( @$flag == 1)
{
return '<div class="d-flex flex-column">
<a href="javascript:void(0);" class="text-danger fw-bolder text-hover-primary fs-6">'.@$row->phone.'</a>
<a href="javascript:void(0);" class="text-danger fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="'.@$row->email.'">'.substr(@$row->email, 0, 10).'</a>
</div>';
}
else
{
return '<div class="d-flex flex-column">
<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.@$row->phone.'</a>
<a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary" data-toggle="tooltip" title="'.@$row->email.'">'.substr(@$row->email, 0, 10).'</a>
</div>';
}
	/*return '<div class="d-flex flex-column">
			<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.@$row->phone.'">' . substr(@$row->phone, 0, 10) . '</a>
			<a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="'.@$row->email.'">' . substr(@$row->email, 0, 10) . '</a>
		  </div>';*/
})
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
		
		->editColumn('Exp', function ($row) {
            return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$row->total_exp . '+</a>
                    <span class="text-muted fw-bold d-block fs-7">' . @$row->rel_exp . '+</span>
                  </div>';
        })

		->editColumn('Salary', function ($row) use($salaryMode, $currencySymbole) {
		
				$current_ctc_cur	=	"";
				$current_ctc_cur	=	$currencySymbole[@$row->current_ctc_cur];
				
				$current_ectc_cur		=	"";
				$current_ectc_cur		=	$currencySymbole[@$row->current_ectc_cur];
				
				$ctc 					=	"";
				$ctc 					=	GeneralMethod::thousandsCurrencyFormat($row->ctc); 
				$ectc 					=	"";
				$ectc 					=	GeneralMethod::thousandsCurrencyFormat($row->ectc); 
				
				$current_ctc_mode		=	"";
				$current_ctc_mode		= substr($salaryMode[@$row->current_ctc_mode], 0, 1);
				
				$current_ectc_mode		=	"";
				$current_ectc_mode		= substr($salaryMode[@$row->current_ectc_mode], 0, 1);
					 		
            return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.@$current_ctc_cur.@$ctc.'/'.$current_ctc_mode.'</a>
                    <span class="text-muted fw-bold d-block fs-7">'.$current_ectc_cur.@$ectc.'/'.$current_ectc_mode.'</span>
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
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.@$np.'</a>
                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords(@$row->current_company).'">'.ucwords(substr(@$row->current_company, 0, 7)).'</span>
                  </div>';
        })
		
		->editColumn('Qualifaction', function ($row) {
            return ' <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="'.@$row->qualification_name.'">'.ucwords(substr(@$row->qualification_name, 0, 10)).'</a>
                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.strtoupper(@$row->skills).'">'.strtoupper(substr(@$row->skills, 0, 15)).'</span> 
                  </div>';
        })
		
		->editColumn('Interview', function ($row) use ($feedback_status) {
            $schedule_date = $className = '';
            $intRowCount 	= DB::table('interview_schedules')->select('*')->where('job_application_id', $row->id)->orderBy('interview_schedules.id', 'DESC')->count();
            $intRow 		= DB::table('interview_schedules')->select('*')->where('job_application_id', $row->id)->orderBy('interview_schedules.id', 'DESC')->first();
            $html 			= '';
            if ($intRowCount > 0) {
                $schedule_date 				= @$intRow->schedule_date;
                $schedule_date 				= date("d M, Y", strtotime($schedule_date));
            } else {
                $html 						= '<div style="width:150px" id="interview_date">&nbsp;</div>';
            }
            if ($intRowCount > 0) {
                $feedback_status_name = '';
                if ($row->available_status > 0) {
                    $feedback_status_name 	= @$feedback_status[@$intRow->feedback]['name'];
                    $className 				= @$feedback_status[@$intRow->feedback]['classname'];
                } else {
                    $feedback_status_name 	= 'Pending';
                    $className 				= 'pending-bg';
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
			
			
			$country			= 	'';  
			$row_country_name 	=	'';
			        
		    if (empty($row->country)) {
                $row_country_name = '----';
            } 
			else
			{
						if(!empty(@$row->country))
						{
								if (is_numeric(@$row->country)) {
										$country_name_Arr	= GeneralMethod::getCountryName(@$row->country);	
										foreach($country_name_Arr as $cval){
											$row_country_name	= ucwords($cval['name']);	
										}
								}              
						}
            }
			
			$state				=	'';
			$row_state_name 	=	'';
			if (empty($row->state)) {
                $row_state_name = '----';
            } 
			else 
			{
					if(@$row->country == 101)
					{
						$row_state_name 	=	@$row->state_name;
					}
					else
					{
							$state 		= @$row->state;
							if(is_numeric(@$row->country) && is_numeric($state))
							{
								if( $state > 0){
									$state_name_Arr	= GeneralMethod::getStateName(@$row->country, $state);	
									foreach($state_name_Arr as $sval){
										$row_state_name		= ucwords($sval['name']);	
									}	
								}
							}	
						
					}
                				
            }
			
						
			$city				=	'';
			$row_city_name 		=	'';
            
			if(@$row->country == 101)
			{
				$row_city 				= DB::table('city_list')->select('*')->where('city_id', @$row->city_id)->first();
				$row_city_name 			= @$row_city->city;
			}
			else
			{
				$row_city_name = @$row->city;	
			}
                
           
			
						
            return '<div class="d-flex flex-column">
                    	<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.ucwords($row_city_name).'">'. ucwords(substr($row_city_name, 0, 10)).'</a>
                    	<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords($row_state_name) .' | '. strtoupper($row_country_name).'">'.ucfirst(substr($row_state_name, 0, 5)) .' | '. strtoupper(substr($row_country_name, 0, 2)).'</span>
                    </div>';
					
					
        })
		
		->editColumn('Source', function ($row) {
			
			$posted_by_image			=		'';
			if(!empty($row->posted_by_image)){	
				$posted_by_image		=	url('public/user-uploads/team_profile_img/'.@$row->posted_by_image); 
			}	

            return '<div class="d-flex flex-column">
                    <div class="symbol-group symbol-hover">
                      <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="'.@$row->posted_by.'">
                        <img alt="Pic" src="'.@$posted_by_image.'">
                      </div>
                  </div>
				  <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="'.@$row->posted_by.'">'.substr(@$row->posted_by, 0, 10).'</span>
				  ';
        })
		
		->editColumn('Stage', function ($row) {
		
            $name = '--------';
            if($row->master_status_id == 8){
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
	
	
    public function createSchedule(Request $request, $id) {
        abort_if(!$this->user->cans('add_schedule'), 403);
        $this->candidates = JobApplication::all();
        $this->users = User::company()->get();
        $this->scheduleDate = $request->date;
        $this->currentApplicant = JobApplication::findOrFail($id);
        return view('admin.apptracker.interview-create', $this->data)->render();
    }
    public function storeSchedule(StoreRequest $request) {
        abort_if(!$this->user->cans('add_schedule'), 403);
        $dateTime = $request->scheduleDate . ' ' . $request->scheduleTime;
        $dateTime = Carbon::createFromFormat('Y-m-d H:i', $dateTime);
        // store Schedule
        $interviewSchedule = new InterviewSchedule();
        $interviewSchedule->job_application_id = $request->candidate;
        $interviewSchedule->schedule_date = $dateTime;
        $interviewSchedule->save();
        // Update Schedule Status
        $interviweColumn = ApplicationStatus::where('status', 'interview')->first();
        $jobApplication = JobApplication::find($request->candidate);
        $jobApplication->status_id = $interviweColumn->id;
        $jobApplication->save();
        if (!empty($request->employee)) {
            InterviewScheduleEmployee::where('interview_schedule_id', $interviewSchedule->id)->delete();
            foreach ($request->employee as $employee) {
                $scheduleEmployee = new InterviewScheduleEmployee();
                $scheduleEmployee->user_id = $employee;
                $scheduleEmployee->interview_schedule_id = $interviewSchedule->id;
                $scheduleEmployee->save();
                $user = User::find($employee);
                // Mail to employee for inform interview schedule
                Notification::send($user, new ScheduleInterview($jobApplication));
            }
        }
        // mail to candidate for inform interview schedule
        Notification::send($jobApplication, new CandidateScheduleInterview($jobApplication, $interviewSchedule));
        return Reply::redirect(route('admin.interview-schedule.index'), __('menu.interviewSchedule') . ' ' . __('messages.createdSuccessfully'));
    }
    public function update(UpdateJobApplication $request, $id) {
        abort_if(!$this->user->cans('edit_job_applications'), 403);
        $jobApplication = JobApplication::find($id);
        $jobApplication->full_name = $request->full_name;
        $jobApplication->status_id = $request->status_id;
        $jobApplication->email = $request->email;
        $jobApplication->phone = $request->phone;
        $jobApplication->cover_letter = $request->cover_letter;
        if ($request->hasFile('resume')) {
            $jobApplication->resume = Files::upload($request->resume, 'resumes', null, null, false);
        }
        if ($request->hasFile('photo')) {
            $jobApplication->photo = Files::upload($request->photo, 'candidate-photos');
        }
        $jobApplication->save();
        // Job Application Answer save
        if (isset($request->answer) && count($request->answer) > 0) {
            JobApplicationAnswer::where('job_application_id', $jobApplication->id)->delete();
            foreach ($request->answer as $key => $value) {
                $answer = new JobApplicationAnswer();
                $answer->job_application_id = $jobApplication->id;
                $answer->job_id = $jobApplication->job_id;
                $answer->question_id = $key;
                $answer->answer = $value;
                $answer->save();
            }
        }
        return Reply::redirect(route('admin.cvtracker.index'), __('menu.jobApplications') . ' ' . __('messages.updatedSuccessfully'));
    }
    public function destroy($id) {
        abort_if(!$this->user->cans('delete_job_applications'), 403);
        JobApplication::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }
    public function show($id) {
        $this->application = JobApplication::with(['schedule', 'status', 'schedule.employee', 'schedule.comments.user'])->find($id);
        $this->answers = JobApplicationAnswer::with(['question'])->where('job_id', $this->application->job_id)->where('job_application_id', $this->application->id)->get();
        $view = view('admin.apptracker.show', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }
    public function updateIndex(Request $request) {
        $taskIds = $request->applicationIds;
        $boardColumnIds = $request->boardColumnIds;
        $priorities = $request->prioritys;
        if (!is_null($taskIds)) {
            foreach ($taskIds as $key => $taskId) {
                if (!is_null($taskId)) {
                    $task = JobApplication::find($taskId);
                    $task->column_priority = $priorities[$key];
                    $task->status_id = $boardColumnIds[$key];
                    $task->save();
                }
            }
        }
        return Reply::dataOnly(['status' => 'success']);
    }
    public function table() {
        abort_if(!$this->user->cans('view_job_applications'), 403);
        $this->boardColumns = ReferenceDataCache::applicationStatuses();
        $this->locations = JobLocation::all();
        $this->jobs = Job::all();
        $this->skills = Skill::all();
        return view('admin.apptracker.index', $this->data);
    }
    public function ratingSave(Request $request, $id) {
        abort_if(!$this->user->cans('edit_job_applications'), 403);
        $application = JobApplication::findOrFail($id);
        $application->rating = $request->rating;
        $application->save();
        return Reply::success(__('messages.updatedSuccessfully'));
    }
    public function viewDetails(Request $request) {
        $candidateCount = JobApplication::count();
        if ($candidateCount > 0 && !is_null($this->activePackage->package->no_of_candidate_access) && $this->activePackage->package->no_of_candidate_access <= $candidateCount) {
            $this->message = __('modules.subscription.candidatePurchase');
            return Reply::error(__('modules.subscription.candidatePurchase'));
        }
        $application = JobApplication::findOrFail($request->id);
        $application->is_viewed = 1;
        $application->save();
        return Reply::dataOnly(['status' => 'success', 'email' => $application->email, 'phone' => $application->phone, 'resume' => route('admin.apptracker.download', $application->id) ]);
    }
    public function download($id) {
        $jobApplication = JobApplication::findOrFail($id);
        if (!file_exists('user-uploads/resumes/' . $jobApplication->resume)) {
            abort(404);
        }
        return response()->download('user-uploads/resumes/' . $jobApplication->resume);
    }
    public function get_client_job1(Request $request) {
        $clientId = '';
        $clientId = $request->clientId;
        $html = '<select class="select2" name="jobs" id="jobs" data-style="form-control">';
        $jobArr = DB::table('jobs')->select('id', 'title')->where('client_id', $clientId)->where('status', 'active')->get();
        foreach ($jobArr as $jobvalue) {
            $html.= '<option value= "' . $jobvalue->id . '">' . ucfirst($jobvalue->title) . '</option>';
        }
        $html.= '</select>';
        echo $html;
        exit;
    }
    public function get_client_job(Request $request) {
        $clientId = $html = '';
        $clientId = $request->clientId;
        if ($clientId == "all" || $clientId == '') {
            $jobArr = DB::table('jobs')->select('id', 'title')->where('status', 'active')->get();
        } else {
            $jobArr = DB::table('jobs')->select('id', 'title')->where('client_id', $clientId)->where('status', 'active')->get();
        }
        $html = '<option value="all">All Jobs</option>';
        foreach ($jobArr as $jobvalue) {
            $html.= '<option value= "' . $jobvalue->id . '">' . ucfirst($jobvalue->title) . '</option>';
        }
        return Reply::dataOnly(['status' => 'success', 'data' => $html]);
    }
    public function store(Request $request) {
        $skillId = $company_id = "";
        $jobid = $request->jobid;
        $skill_name = $request->skill_name;
        $job = Job::findOrFail($jobid);
        $company_id = auth()->user()->company_id;
        $values = array('name' => $skill_name, 'company_id' => $company_id, 'category_id' => $job->category_id);
        DB::table('skills')->insert($values);
        $skillId = DB::getPdo()->lastInsertId();
        $values = array('company_id' => $company_id, 'skill_id' => $skillId, 'job_id' => $jobid);
        DB::table('job_skills')->insert($values);
        $skillId = DB::getPdo()->lastInsertId();
        echo 1;
        exit;
        //return redirect('admin/jobtracker/'.$jobid)->with('status', 'Profile updated!');
        //return Reply::redirect(route('admin.jobtracker.'.$jobid ), __('menu.jobApplications') . ' ' . __('messages.createdSuccessfully'));
        
    }
    public function updateinformation(Request $request) {
        $jobid = $candidate = $schematype = '';
        $jobid = $request->jobid;
        $candidate = $request->candidate;
        $schematype = $request->schematype;
        $schemaname = $request->schemaname;
        $schemavalue = $request->schemavalue;
        if ($schematype == 'noskill') {
            $application = JobApplication::findOrFail($candidate);
            if ($schemaname == 'full_name') {
                $application->full_name = $request->schemavalue;
            }
            if ($schemaname == 'email') {
                $application->email = $request->schemavalue;
            }
            if ($schemaname == 'phone') {
                $application->phone = $request->schemavalue;
            }
            if ($schemaname == 'ctc') {
                $application->ctc = $request->schemavalue;
            }
            if ($schemaname == 'ectc') {
                $application->ectc = $request->schemavalue;
            }
            if ($schemaname == 'total_exp') {
                $application->total_exp = $request->schemavalue;
            }
            if ($schemaname == 'rel_exp') {
                $application->rel_exp = $request->schemavalue;
            }
            if ($schemaname == 'current_designation') {
                $application->current_designation = $request->schemavalue;
            }
            if ($schemaname == 'current_company') {
                $application->current_company = $request->schemavalue;
            }
            if ($schemaname == 'current_location') {
                $application->current_location = $request->schemavalue;
            }
            if ($schemaname == 'passport') {
                $application->passport = $request->schemavalue;
            }
            if ($schemaname == 'np_id') {
                $application->np_id = $request->schemavalue;
            }
            if ($schemaname == 'status_id') {
                $application->status_id = $request->schemavalue;
                //Sender Detail
                $submitted_by = DB::table('users')->select('email')->where('id', $application->submitted_by)->first();
                $candidateSenderEmail = $submitted_by->email;
                // Candidate Detail
                $candidateName = $application->full_name;
                $candidateEmail = $application->email;
                // Status name
                $statusArr = DB::table('application_status')->select('status')->where('id', $application->status_id)->where('company_id', $this->user->company_id)->first();
                $status_name = $statusArr->status;
                $user = array('name' => $candidateName, 'email' => $candidateEmail, 'cc_email' => $candidateSenderEmail, 'status_name' => $status_name,);
                Mail::send('email.candidate_status_update', ['user' => $user], function ($message) use ($user) {
                    $message->from('no-reply@vibhanshujaiswal.com', 'ATS');
                    $message->to($user['email'], $user['name']);
                    $message->bcc($user['cc_email']);
                    $message->subject('Your Job Application update Successfully.');
                });
            }
            if ($schemaname == 'education') {
                $application->qualification = $request->schemavalue;
            }
            $application->save();
        }
        if ($schematype == 'skill') {
            $skillcounter = DB::table('candidate_skills_value')->select('*')->where('job_id', @$request->jobid)->where('candidate_id', @$request->candidate)->where('skill_id', @$request->schemaname)->count();
            if ($skillcounter == 0 || $skillcounter == '') {
                $values = array('job_id' => @$request->jobid, 'skill_id' => @$request->schemaname, 'candidate_id' => @$request->candidate, 'skill_value' => @$request->schemavalue,);
                DB::table('candidate_skills_value')->insert($values);
            } else {
                DB::table('candidate_skills_value')->where('job_id', @$request->jobid)->where('candidate_id', @$request->candidate)->where('skill_id', @$request->schemaname)->limit(1)->update(array('skill_value' => @$request->schemavalue));
            }
            //echo '<pre>'; print_r($_REQUEST); exit;
            
        }
        //echo '<pre>'; print_r($_REQUEST); exit;
        
    }
    public function export1($status, $location, $startDate, $endDate, $jobs) {
        // Fetching All Job Applications
        $jobApplications = JobApplication::select('job_applications.id', 'jobs.title', 'job_applications.full_name', 'job_applications.email', 'job_applications.phone', 'job_applications.cover_letter', 'application_status.status', 'job_applications.created_at')->leftJoin('jobs', 'jobs.id', '=', 'job_applications.job_id')->leftJoin('application_status', 'application_status.id', '=', 'job_applications.status_id');
        if ($status != 'all' && $status != '') {
            $jobApplications = $jobApplications->where('job_applications.status_id', $status);
        }
        // Filter  By Location
        if ($location != 'all' && $location != '') {
            $jobApplications = $jobApplications->where('jobs.location_id', $location);
        }
        // Filter  By Job
        if ($jobs != 'all' && $jobs != '') {
            $jobApplications = $jobApplications->where('job_applications.job_id', $jobs);
        } // Filter  By Job
        if ($jobs != 'all' && $jobs != '') {
            $jobApplications = $jobApplications->where('job_applications.job_id', $jobs);
        }
        // Filter  By StartDate of job
        if ($startDate != null && $startDate != '' && $startDate != 0) {
            $jobApplications = $jobApplications->where(DB::raw('DATE(jobs.`start_date`)'), '>=', "$startDate");
        }
        // Filter  By EndDate of job
        if ($endDate != null && $endDate != '' && $endDate != 0) {
            $jobApplications = $jobApplications->where(DB::raw('DATE(jobs.`end_date`)'), '<=', "$endDate");
        }
        $jobApplications = $jobApplications->get();
        // Initialize the array which will be passed into the Excel
        // generator.
        $exportArray = [];
        // Define the Excel spreadsheet headers
        $exportArray[] = ['ID', 'Job Title', 'Name', 'Email', 'Mobile', 'Cover Letter', 'Status', 'Created at'];
        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        foreach ($jobApplications as $row) {
            $exportArray[] = $row->toArray();
        }
        // Generate and return the spreadsheet
        Excel::create('job-applications', function ($excel) use ($exportArray) {
            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Job Applications');
            $excel->setCreator('Recruit')->setCompany($this->companyName);
            $excel->setDescription('job-applications file');
            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function ($sheet) use ($exportArray) {
                $sheet->fromArray($exportArray, null, 'A1', false, false);
                $sheet->row(1, function ($row) {
                    // call row manipulation methods
                    $row->setFont(array('bold' => true));
                });
            });
        })->download('xlsx');
    }
    public function loadVisibilityControl($id) {
        $job_id = $id;
        if ($job_id > 0) {
            $this->jobSkill = DB::table('job_skills')->join('skills', 'skills.id', 'job_skills.skill_id')->select('job_skills.company_id', 'job_skills.skill_id', 'job_skills.job_id', 'skills.name')->where('job_skills.job_id', '=', $job_id)->get()->toArray();
        }
        return view('admin.apptracker.load_visibility_control', $this->data)->render();
    }
    public function getchild(Request $request) {
        $id = $flag = '';
        $current_application_id = $current_status = $job_id = '';
        $flag = 0;
        $id = $request->id;
        $current_application_id = $request->current_application_id;
        $current_status = $request->current_status;
        $job_id = $request->job_id;
        $rowData = DB::table('job_applications')->select('*')->where('id', $current_application_id)->first();
        //echo '<pre>'; print_r($rowData); exit;
        $candidateArr = DB::table('candidate_status')->select('*')->where('parent', $current_status)->where('company_id', '=', $this->user->company_id)->get();
        $candChildCounter = DB::table('candidate_status')->select('*')->where('parent', $current_status)->where('company_id', '=', $this->user->company_id)->count();
        if ($current_application_id > 0 && $current_status > 0 && $job_id > 0 && $candChildCounter == 0) {
            DB::table('job_applications')->where('id', @$current_application_id)->where('job_id', @$job_id)
            //->where('skill_id', @$request->schemaname)
            ->limit(1)->update(array('master_status_id' => $current_status, 'child_status_id' => NULL));
        }
        if (!empty($candidateArr)) {
            $html = '<select class="" name="childStatus" id="childStatus_' . $current_application_id . '" data-style="" onchange="getOpenRejectionResionFrm(this.value, ' . $current_application_id . ', ' . $job_id . ', ' . $current_status . ');"><option value="">Select Sub Stage</option>';
            foreach ($candidateArr as $cval) {
                $html.= '<option value= "' . $cval->id . '">' . ucfirst($cval->name) . '</option>';
                $flag = 1;
            }
            $html.= '</select>';
        } else {
            $html = '&nbsp;-&nbsp;';
        }
        if ($current_status > 0) {
            $insert_type = 0;
            $applicationStatus = ApplicationStatus::find($current_status);
            GeneralMethod::manageTimeline(auth()->user()->id, $current_application_id, $job_id, $current_status, $applicationStatus->name, 'Update process stage', '', $insert_type);
        }
        $activeAppCounter = DB::table('job_applications')->select(DB::raw("count(job_applications.id) as allCandidate"), DB::raw("SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing"), DB::raw("SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening"), DB::raw("SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview"), DB::raw("SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation"), DB::raw("SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered"), DB::raw("SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined"), DB::raw("SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected"))->where('job_applications.job_id', '=', $job_id)->groupBy('job_applications.job_id')->first();
        $countArr = DB::table('candidate_status')->select('*')->where('parent', $current_status)->where('company_id', '=', $this->user->company_id)->count();
        if ($current_status == 5) {
            $values = array('application_id' => $rowData->id, 'submitted_by' => $rowData->submitted_by, 'company_id' => $rowData->company_id, 'np_id' => $rowData->np_id, 'full_name' => $rowData->full_name, 'email' => $rowData->email, 'phone' => $rowData->phone, 'gender' => $rowData->gender, 'dob' => $rowData->dob, 'country' => $rowData->country, 'state' => $rowData->state, 'city' => $rowData->city, 'secondrary_email' => $rowData->secondrary_email, 'secondary_mobile' => $rowData->secondary_mobile, 'skills' => $rowData->skills, 'photo' => $rowData->photo, 'resume' => $rowData->resume, 'rating' => $rowData->rating, 'skype_id' => $rowData->skype_id, 'cover_letter' => $rowData->cover_letter, 'job_id' => $rowData->job_id, 'status_id' => $rowData->status_id, 'is_viewed' => $rowData->is_viewed, 'column_priority' => $rowData->column_priority, 'total_exp' => $rowData->total_exp, 'rel_exp' => $rowData->rel_exp, 'passport' => $rowData->passport, 'ctc' => $rowData->ctc, 'ectc' => $rowData->ectc, 'current_location' => $rowData->current_location, 'current_designation' => $rowData->current_designation, 'current_company' => $rowData->current_company, 'process_id' => $rowData->process_id, 'qualification' => $rowData->qualification, 'master_status_id' => $rowData->master_status_id, 'child_status_id' => $rowData->child_status_id, 'serving_notice_period_mode' => $rowData->serving_notice_period_mode, 'proposed_ctc' => $rowData->proposed_ctc, 'any_offer' => $rowData->any_offer, 'document' => $rowData->document, 'sub_for_approval' => $rowData->sub_for_approval, 'offer_approved' => $rowData->offer_approved, 'offer_release' => $rowData->offer_release, 'offer_acceptance' => $rowData->offer_acceptance, 'joined' => $rowData->joined, 'created_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d'));
            DB::table('offer_job_applications')->insert($values);
        }
        return Reply::dataOnly(['status' => 'success', 'data' => $html, 'counter' => $countArr, 'flag' => $flag, 'activeAppCounter' => $activeAppCounter]);
    }
    public function openRejFrm(Request $request) {
        abort_if(!$this->user->cans('add_schedule'), 403);
        $application_id = $current_child_id = $job_id = $parent_status_id = '';
        $this->application_id = $_REQUEST['application_id'];
        $this->current_child_id = $_REQUEST['current_child_id'];
        $this->job_id = $_REQUEST['job_id'];
        $this->parent_status_id = $_REQUEST['parent_status_id'];
        $this->interview_node = GeneralMethod::interview_mode();
        $this->application = JobApplication::select('job_applications.*', 'clients.name as client_name', 'jobs.title as job_title', 'client_location.city', 'jobs.company_id', 'master_status.name as master_status_name')->join('jobs', 'jobs.id', '=', 'job_applications.job_id')->join('clients', 'clients.id', '=', 'jobs.client_id')->leftjoin('client_location_jobs', 'client_location_jobs.job_id', 'jobs.id')->leftjoin('client_location', 'client_location.id', 'client_location_jobs.client_location_id')->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')->where('job_applications.id', $this->application_id)->groupBy('job_applications.id')->first();
        $this->childStatus = DB::table('candidate_status')->select('*')->where('parent', '=', $this->parent_status_id)->where('id', '=', $this->current_child_id)
        //->orderBy('priority', 'ASC')
        ->first();
        return view('admin.apptracker.rejectionFrm', $this->data)->render();
    }
    public function save_rejected_resion(Request $request) {
        $application_id = $email_everyone = $rejected_resion_message = $rejected_resion_name = $process_id = $master_status_name = $process_child_name = $rejected_resion_message = $child_status_id = '';
        $application_id = $_REQUEST['application_id'];
        $job_id = $_REQUEST['job_id'];
        $process_id = $_REQUEST['process_id'];
        $master_status_name = $_REQUEST['master_status_name'];
        $process_child_name = $_REQUEST['process_child_name'];
        $rejected_resion_message = $_REQUEST['rejected_resion_message'];
        $child_status_id = $_REQUEST['child_status_id'];
        if (!empty($application_id) && !empty($job_id) && !empty($process_id) && !empty($master_status_name) && !empty($process_child_name) && !empty($rejected_resion_message)) {
            DB::table('job_applications')->where('id', @$application_id)->where('job_id', @$job_id)->limit(1)->update(array('child_status_id' => $child_status_id, 'master_status_id' => $process_id));
            //manageTimeline($owner_id, 		$application_id,   $job_id ='', $process_id='', $status_name='', $note='', $process_child_name = '')
            //GeneralMethod :: manageTimeline(auth()->user()->id, $application_id, $job_id, $process_id, $master_status_name, 'Rejected Resion:'.$rejected_resion_message, $process_child_name);
            // WORKING WITH TIMELINE .............................................................................................................................................
            $application = JobApplication::find($application_id);
            $date = date('Y-m-d H:i:s');
            $insert_type = 6;
            $statusRow = ApplicationStatus::findOrFail($application->master_status_id);
            $values = array('owner_id' => auth()->user()->id, 'application_id' => $application->id, 'application_email' => $application->email, 'job_id' => $application->job_id, 'process_id' => $application->master_status_id, 'status_name' => $statusRow->name,
            //	'process_child_name'	=> $status_name,
            'note' => 'Rejected reason', 'insert_type' => $insert_type, 'create_at' => $date, 'update_at' => $date,
            //'interview_schedule_date'		=> $dateTime,
            //'interview_from_time'			=> $request->schedule_from_time,
            //'interview_to_time'			=> $request->schedule_to_time,
            'interview_mode' => $rejected_resion_name,
            //'interview_mention_link'		=> $request->url_link,
            //'interviewers'				=>	$date,
            //'interview_additional_email'	=>	$request->other_email,
            'interview_message' => $rejected_resion_message);
            if ($application->child_status_id > 0) {
                $childStatus = '';
                $childStatus = DB::table('candidate_status')->select('*')->where('company_id', '=', $this->user->company_id)->where('id', $application->child_status_id)->first();
                $values['process_child_name'] = @$childStatus->name;
            }
            /*if(!empty($request->interviewers))
            {
            $html = '';
            foreach($request->interviewers as $intVal)
            {
            $userArr  = DB::table('users')->select('name')->whereIn('id', $request->interviewers)->get();
            foreach($userArr as $uval){
            $html .= ' '.$uval->name;
            }
            }
            $values['interviewers'] 	=	@$html;
            }*/
            DB::table('timeline')->insert($values);
            // END WORKING WITH TIMELINE ...............................................................................................................................................
            return Reply::dataOnly(['status' => 'success',
            //'message'	=>	'Rejected resion added successfully !'
            //'email' => $application->email,
            //'phone' => $application->phone,
            //'resume' => route('admin.job-applications.download', $application->id)
            ]);
        } else {
            return Reply::dataOnly(['status' => 'error', 'message' => 'Please enter Rejection Reason'
            //'email' => $application->email,
            //'phone' => $application->phone,
            //'resume' => route('admin.job-applications.download', $application->id)
            ]);
        }
    }
    public function update_sub_stage($status, $application_id, $job_id) {
        echo $status;
        echo '<pre>';
        print_r($_REQUEST);
        exit;
    }
    public function sub_stage(Request $request) {
        //GET http://talentmily.vibhanshujaiswal.com/admin/apptracker/sub_stage/566?application_id=566&status=112&job_id=14
        //$id 			= $flag = '';
        $flag = 0;
        //$id 			= $request->id;
        $application_id = $current_status = $job_id = $parent_status = '';
        $application_id = $request->application_id;
        $job_id = $request->job_id;
        $status = $request->status;
        $parent_status = $request->parent_status;
        if (empty($request->parent_status) || $request->parent_status == '') {
            $parentRow = DB::table('candidate_status')->select('*')->where('id', $request->status)->first();
            $parent_status = $parentRow->parent;
        }
        $candidateRow = DB::table('candidate_status')->select('*')->where('parent', $status)->where('company_id', '=', $this->user->company_id)->first();
        //echo '<pre>'; print_r($_REQUEST); exit;
        if ($application_id > 0 && $status > 0 && $job_id > 0) {
            DB::table('job_applications')->where('id', @$application_id)->where('job_id', @$job_id)
            //->where('skill_id', @$request->schemaname)
            ->limit(1)->update(array('child_status_id' => $status, 'master_status_id' => $parent_status));
            $candidateApplicationRow = DB::table('job_applications')->join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')->join('candidate_status', 'candidate_status.id', '=', 'job_applications.child_status_id')->select('job_applications.master_status_id', 'master_status.name as master_status_name', 'master_status.name as child_status_id', 'candidate_status.name as candidate_status_name')->where('job_applications.id', '=', $application_id)->where('job_applications.company_id', '=', $this->user->company_id)->where('job_applications.job_id', '=', $job_id)->first();
            $process_id = $candidateApplicationRow->master_status_id;
            $master_status_name = $candidateApplicationRow->master_status_name;
            $process_child_name = $candidateApplicationRow->candidate_status_name;
            //$insert_type				=	0;
            //GeneralMethod :: manageTimeline(auth()->user()->id, $application_id, $job_id, $process_id, $master_status_name, 'Update process sub stage', $process_child_name, $insert_type);
            // WORKING WITH TIMELINE .............................................................................................................................................
            $application = JobApplication::find($application_id);
            $date = date('Y-m-d H:i:s');
            $insert_type = 0;
            $statusRow = ApplicationStatus::findOrFail($application->master_status_id);
            $values = array('owner_id' => auth()->user()->id, 'application_id' => $application->id, 'application_email' => $application->email, 'job_id' => $application->job_id, 'process_id' => $application->master_status_id, 'status_name' => $statusRow->name,
            //'process_child_name'		=>	$status_name,
            //'note'			 			=>	'Rejected reason',
            'insert_type' => $insert_type, 'create_at' => $date, 'update_at' => $date,
            //'interview_schedule_date'	=>	$dateTime,
            //'interview_from_time'		=>	$request->schedule_from_time,
            //'interview_to_time'		=>	$request->schedule_to_time,
            //'interview_mode'			=>	$rejected_resion_name,
            //'interview_mention_link'	=>	$request->url_link,
            //'interviewers'			=>	$date,
            //'interview_additional_email'=>	$request->other_email,
            'interview_message' => $process_child_name);
            if ($application->child_status_id > 0) {
                $childStatus = '';
                $childStatus = DB::table('candidate_status')->select('*')->where('company_id', '=', $this->user->company_id)->where('id', $application->child_status_id)->first();
                $values['process_child_name'] = @$childStatus->name;
            }
            /*if(!empty($request->interviewers))
            {
            $html = '';
            foreach($request->interviewers as $intVal)
            {
            $userArr  = DB::table('users')->select('name')->whereIn('id', $request->interviewers)->get();
            foreach($userArr as $uval){
            $html .= ' '.$uval->name;
            }
            }
            $values['interviewers'] 	=	@$html;
            }*/
            DB::table('timeline')->insert($values);
            // END WORKING WITH TIMELINE ...............................................................................................................................................
            $flag = 1;
            $activeAppCounter = DB::table('job_applications')->select(DB::raw("count(job_applications.id) as allCandidate"), DB::raw("SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing"), DB::raw("SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening"), DB::raw("SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview"), DB::raw("SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation"), DB::raw("SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered"), DB::raw("SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined"), DB::raw("SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected"))->where('job_applications.job_id', '=', $job_id)->groupBy('job_applications.job_id')->first();
            return Reply::dataOnly(['status' => 'success', 'flag' => $flag, 'activeAppCounter' => $activeAppCounter]);
        } else {
            return Reply::dataOnly(['status' => 'error', 'message' => 'Something worng please try agarin!']);
        }
    }
    public function bk_export($jobs, $start, $length) {
        //echo $jobs; exit;
        $jobApplications = JobApplication::select('job_applications.id', 'job_applications.created_at', 'job_applications.full_name', 'clients.name', 'jobs.title', 'users.name as submitted_by', 'job_applications.email', 'job_applications.phone', 'job_applications.ctc', 'job_applications.ectc', 'job_applications.total_exp', 'job_applications.rel_exp', 'job_applications.passport', 'notice_periods.np_title', 'job_applications.current_designation', 'job_applications.current_company', 'job_applications.current_location', 'application_status.status');
        $jobApplications = $jobApplications->join('jobs', 'jobs.id', '=', 'job_applications.job_id');
        $jobApplications = $jobApplications->join('clients', 'clients.id', '=', 'jobs.client_id');
        $jobApplications = $jobApplications->join('users', 'users.id', '=', 'job_applications.submitted_by');
        $jobApplications = $jobApplications->join('notice_periods', 'notice_periods.np_id', 'job_applications.np_id');
        $jobApplications = $jobApplications->leftJoin('application_status', 'application_status.id', '=', 'job_applications.status_id');
        $jobApplications = $jobApplications->where('job_applications.job_id', '=', $jobs);
        // $jobApplications  = $jobApplications->get();
        $jobApplications = $jobApplications->skip($start)->take($length)->get();
        //echo '<pre>'; print_r($jobApplications); exit;
        $jobSkill = DB::table('job_skills')->join('skills', 'skills.id', 'job_skills.skill_id')->select('job_skills.company_id', 'job_skills.skill_id', 'job_skills.job_id', 'skills.name')->where('job_skills.job_id', '=', $jobs)->get()->toArray();
        $exportArray = array();
        $exportArray[0]['SNo'] = 'SNo';
        $exportArray[0]['Date'] = 'Date';
        $exportArray[0]['Applicant'] = 'Applicant';
        $exportArray[0]['Client'] = 'Client';
        $exportArray[0]['Jobs'] = 'Jobs';
        $exportArray[0]['Source'] = 'Source';
        //echo '<pre>'; print_r( $jobSkill); exit;
        $inc = 0;
        foreach ($jobSkill as $skillValue) {
            $exportArray[0][$inc] = 'Skill# ' . $skillValue->name;
            $inc = $inc + 1;
        }
        $exportArray[0]['Email'] = 'Email';
        $exportArray[0]['Phone'] = 'Phone';
        $exportArray[0]['CTC'] = 'CTC';
        $exportArray[0]['ECTC'] = 'ECTC';
        $exportArray[0]['Experience'] = 'Experience';
        $exportArray[0]['Relevant Experience'] = 'Relevant Experience';
        $exportArray[0]['Passport'] = 'Passport';
        $exportArray[0]['Notice Period'] = 'Notice Period';
        $exportArray[0]['Current Designation'] = 'Current Designation';
        $exportArray[0]['Current Company'] = 'Current Company';
        $exportArray[0]['Current Location'] = 'Current Location';
        $exportArray[0]['Status'] = 'Status';
        $loopCounter = 1;
        foreach ($jobApplications as $row) {
            $exportArray[$loopCounter]['SNo'] = $loopCounter;
            $exportArray[$loopCounter]['Date'] = $row->created_at;
            $exportArray[$loopCounter]['Applicant'] = $row->full_name;
            $exportArray[$loopCounter]['Client'] = $row->name;
            $exportArray[$loopCounter]['Jobs'] = $row->title;
            $exportArray[$loopCounter]['Source'] = $row->submitted_by;
            $inc = 0;
            foreach ($jobSkill as $skillValue) {
                $skillvalue = DB::table('candidate_skills_value')->select('candidate_skills_value.skill_value')->where('candidate_skills_value.job_id', '=', $jobs)->where('candidate_skills_value.candidate_id', '=', $row->id)->where('candidate_skills_value.skill_id', '=', $skillValue->skill_id)->first();
                if (!empty(@$skillvalue->skill_value)) {
                    $exportArray[$loopCounter][$inc] = @$skillvalue->skill_value;
                } else {
                    $exportArray[$loopCounter][$inc] = "N/A";
                }
                $inc = $inc + 1;
            }
            $exportArray[$loopCounter]['Email'] = $row->email;
            $exportArray[$loopCounter]['Phone'] = $row->phone;
            $exportArray[$loopCounter]['CTC'] = $row->ctc;
            $exportArray[$loopCounter]['ECTC'] = $row->ectc;
            $exportArray[$loopCounter]['Experience'] = $row->total_exp;
            $exportArray[$loopCounter]['Relevant Experience'] = $row->rel_exp;
            $exportArray[$loopCounter]['Passport'] = $row->passport;
            $exportArray[$loopCounter]['Notice Period'] = $row->np_title;
            $exportArray[$loopCounter]['Current Designation'] = $row->current_designation;
            $exportArray[$loopCounter]['Current Company'] = $row->current_company;
            $exportArray[$loopCounter]['Current Location'] = $row->current_location;
            $exportArray[$loopCounter]['Status'] = $row->status;
            $loopCounter = $loopCounter + 1;
        }
        $date = date('d-m-Y');
        // Generate and return the spreadsheet
        Excel::create('JobTracker-' . $date, function ($excel) use ($exportArray) {
            $excel->setTitle('Job Applications');
            $excel->setCreator('Recruit')->setCompany($this->companyName);
            $excel->setDescription('Job_tracker_file');
            $excel->sheet('sheet1', function ($sheet) use ($exportArray) {
                $sheet->fromArray($exportArray, null, 'A1', false, false);
                $sheet->row(1, function ($row) {
                    $row->setFont(array('bold' => true));
                });
            });
        })->download('xlsx');
    }
    public function export_bk($jobs, $mstatus_id, $start, $length) {
        //echo $jobs; exit;
        $jobApplications = JobApplication::select('job_applications.id', 'job_applications.created_at', 'job_applications.full_name', 'clients.name', 'jobs.title', 'users.name as submitted_by', 'job_applications.email', 'job_applications.phone', 'job_applications.ctc', 'job_applications.ectc', 'job_applications.total_exp', 'job_applications.rel_exp', 'job_applications.passport', 'notice_periods.np_title', 'job_applications.current_designation', 'job_applications.current_company', 'job_applications.current_location', 'application_status.status');
        $jobApplications = $jobApplications->join('jobs', 'jobs.id', '=', 'job_applications.job_id');
        $jobApplications = $jobApplications->join('clients', 'clients.id', '=', 'jobs.client_id');
        $jobApplications = $jobApplications->join('users', 'users.id', '=', 'job_applications.submitted_by');
        $jobApplications = $jobApplications->join('notice_periods', 'notice_periods.np_id', 'job_applications.np_id');
        $jobApplications = $jobApplications->leftJoin('application_status', 'application_status.id', '=', 'job_applications.status_id');
        $jobApplications = $jobApplications->where('job_applications.job_id', '=', $jobs);
        // $jobApplications  = $jobApplications->get();
        $jobApplications = $jobApplications->skip($start)->take($length)->get();
        //echo '<pre>'; print_r($jobApplications); exit;
        $jobSkill = DB::table('job_skills')->join('skills', 'skills.id', 'job_skills.skill_id')->select('job_skills.company_id', 'job_skills.skill_id', 'job_skills.job_id', 'skills.name')->where('job_skills.job_id', '=', $jobs)->get()->toArray();
        $exportArray = array();
        $exportArray[0]['SNo'] = 'SNo';
        $exportArray[0]['Date'] = 'Date';
        $exportArray[0]['Applicant'] = 'Applicant';
        $exportArray[0]['Client'] = 'Client';
        $exportArray[0]['Jobs'] = 'Jobs';
        $exportArray[0]['Source'] = 'Source';
        //echo '<pre>'; print_r( $jobSkill); exit;
        $inc = 0;
        foreach ($jobSkill as $skillValue) {
            $exportArray[0][$inc] = 'Skill# ' . $skillValue->name;
            $inc = $inc + 1;
        }
        $exportArray[0]['Email'] = 'Email';
        $exportArray[0]['Phone'] = 'Phone';
        $exportArray[0]['CTC'] = 'CTC';
        $exportArray[0]['ECTC'] = 'ECTC';
        $exportArray[0]['Experience'] = 'Experience';
        $exportArray[0]['Relevant Experience'] = 'Relevant Experience';
        $exportArray[0]['Passport'] = 'Passport';
        $exportArray[0]['Notice Period'] = 'Notice Period';
        $exportArray[0]['Current Designation'] = 'Current Designation';
        $exportArray[0]['Current Company'] = 'Current Company';
        $exportArray[0]['Current Location'] = 'Current Location';
        $exportArray[0]['Status'] = 'Status';
        $loopCounter = 1;
        foreach ($jobApplications as $row) {
            $exportArray[$loopCounter]['SNo'] = $loopCounter;
            $exportArray[$loopCounter]['Date'] = $row->created_at;
            $exportArray[$loopCounter]['Applicant'] = $row->full_name;
            $exportArray[$loopCounter]['Client'] = $row->name;
            $exportArray[$loopCounter]['Jobs'] = $row->title;
            $exportArray[$loopCounter]['Source'] = $row->submitted_by;
            $inc = 0;
            foreach ($jobSkill as $skillValue) {
                $skillvalue = DB::table('candidate_skills_value')->select('candidate_skills_value.skill_value')->where('candidate_skills_value.job_id', '=', $jobs)->where('candidate_skills_value.candidate_id', '=', $row->id)->where('candidate_skills_value.skill_id', '=', $skillValue->skill_id)->first();
                if (!empty(@$skillvalue->skill_value)) {
                    $exportArray[$loopCounter][$inc] = @$skillvalue->skill_value;
                } else {
                    $exportArray[$loopCounter][$inc] = "N/A";
                }
                $inc = $inc + 1;
            }
            $exportArray[$loopCounter]['Email'] = $row->email;
            $exportArray[$loopCounter]['Phone'] = $row->phone;
            $exportArray[$loopCounter]['CTC'] = $row->ctc;
            $exportArray[$loopCounter]['ECTC'] = $row->ectc;
            $exportArray[$loopCounter]['Experience'] = $row->total_exp;
            $exportArray[$loopCounter]['Relevant Experience'] = $row->rel_exp;
            $exportArray[$loopCounter]['Passport'] = $row->passport;
            $exportArray[$loopCounter]['Notice Period'] = $row->np_title;
            $exportArray[$loopCounter]['Current Designation'] = $row->current_designation;
            $exportArray[$loopCounter]['Current Company'] = $row->current_company;
            $exportArray[$loopCounter]['Current Location'] = $row->current_location;
            $exportArray[$loopCounter]['Status'] = $row->status;
            $loopCounter = $loopCounter + 1;
        }
        $date = date('d-m-Y');
        // Generate and return the spreadsheet
        Excel::create('JobTracker-' . $date, function ($excel) use ($exportArray) {
            $excel->setTitle('Job Applications');
            $excel->setCreator('Recruit')->setCompany($this->companyName);
            $excel->setDescription('Job_tracker_file');
            $excel->sheet('sheet1', function ($sheet) use ($exportArray) {
                $sheet->fromArray($exportArray, null, 'A1', false, false);
                $sheet->row(1, function ($row) {
                    $row->setFont(array('bold' => true));
                });
            });
        })->download('xlsx');
    }
    public function export($jobs, $mstatus_id, $start, $length) {
        $filters = ['jobs' => $jobs, 'master_status_id' => $mstatus_id, 'start' => $start, 'length' => $length, 'loginUserId' => $this->user->id, 'loginUserRole' => $this->user->role->role_id, 'company_id' => auth()->user()->company_id];
        $data = ['company' => $this->user->name];
        return Excel::download(new AppTrackerExport($filters, $data), 'AppTrackerExport.xlsx', ExcelExcel::XLSX);
    }
    public function showInterviews($id) {
        $this->currentApplicant = JobApplication::findOrFail($id);
        echo "call";
        exit;
        // return view('admin.job-applications.move-create', $this->data)->render();
        return view('admin.apptracker.showInterviews', $this->data)->render();
    }
}
