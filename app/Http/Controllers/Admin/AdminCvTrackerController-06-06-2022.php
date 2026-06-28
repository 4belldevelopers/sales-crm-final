<?php 
/*** ########################################################
Controller    	: 	AdminCVTrackerController
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
use App\Models\InterviewSchedule;
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

//use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\NoticePeriod;
use App\InterviewProcess;

use Illuminate\Support\Facades\DB;
use Mail;


//use Maatwebsite\Excel\Facades\Excel;
//use App\Exports\CvTrackerExport;

use App\Exports\CvTrackerExport;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;



class AdminCvTrackerController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('CV / Resume Received');
        $this->pageIcon = 'icon-user';
		
    }

/*** ########################################################
Controller    	: 	AdminCVTrackerController
Method			:	index
Purpose			:	Load all application 
Date			:	05-April-2021	
########################################################***/
	public function index()
    {
		
        //abort_if(!$this->user->cans('view_job_applications'), 403);

		$chkAdminRole = $this->user->hasRole('admin');
		
		// $chkAdminRole = DB::table('roles')
		// 				->select('*')
		// 				->where('id', $this->user->role->role_id)
		// 				->where('name', 'admin')
		// 				->count(); 
						
        $this->boardColumns 	= ApplicationStatus::all();
		
		$this->candidateStatus 	= DB::table('master_status')
									->select('*')
									->orderBy('master_status.priority', 'ASC')
									->get(); 
		
		
        $this->locations 		= JobLocation::all();       
        $this->skills 			= Skill::all();
		$this->clients 			= Client::all();
		
		if($chkAdminRole > 0)
		{
			 $this->jobs 			= Job::where('jobs.company_id', $this->user->company_id)->get();	
		}
		else
		{
			$this->jobs 			= DB::table('jobs')
											->join('job_assign', 'job_assign.job_id', '=', 'jobs.id')
											->select('jobs.id', 'jobs.title')
											->where('jobs.status', 'active')
											->where('jobs.company_id', $this->user->company_id)
											->where('job_assign.user_id', $this->user->id)
											->get();
						
		}
		
		$this->users 			= User::select('users.*')->where('status', '=', 'active')->where('company_id', '=', $this->user->company_id)->get();
		
		$this->noticeperiod 	= NoticePeriod::all();
		
		
		//$country_name_Arr	= GeneralMethod::getCountryName(101);	
		//echo '<pre>'; print_r($country_name_Arr); exit; 
		
        return view('admin.cvtracker.index', $this->data);
    }
		
/*** ########################################################
Controller    	: 	AdminCVTrackerController
Method			:	loadVisibilityControl
Purpose			:	Display table schema
Date			:	05-April-2021	
########################################################***/	
	public function loadVisibilityControl($id)
    {
		

		return view('admin.cvtracker.load_visibility_control', $this->data)->render();
       // $view = view('admin.job-applications.load_visibility_control', $this->data)->render();
       // return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }
	
/*** ########################################################
Controller    	: 	AdminCVTrackerController
Method			:	
Purpose			:	Display table schema
Date			:	05-April-2021	
########################################################***/	
	 public function create_status(Request $request)
	 {
        abort_if(! $this->user->cans('add_schedule'), 403);
		
       	$data	=	$_REQUEST['date'];
        
		if($this->user->role->role_id != 10)
		{	
				  $this->candidates 	= JobApplication::where('job_applications.submitted_by', $this->user->id)->get();
				 //$this->schedules = $this->schedules->where('job_applications.submitted_by', $this->user->id);	
		}
		else
		{
			 $this->candidates 	= JobApplication::all();
		}
		
		$this->application 			= JobApplication::where('job_applications.id', $data)->get();
		$this->interviewStatus 		= DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();
		
		$this->users 		= User::with('company')->company()->get();
        $this->scheduleDate = $request->date;
        return view('admin.cvtracker.status', $this->data)->render();
    }

/*** ########################################################
Controller    	: 	AdminCVTrackerController
Method			:	getchild
Purpose			:	Return child status data
Date			:	05-April-2021	
########################################################***/	
	public function getchild(Request $request)
	{
		
		$id 			= $flag = '';
		$flag 			= 0;
		$id 			= $request->id;
		
		$candidateArr 		= DB::table('candidate_status')
								->select('*')
									->where('parent', $id)
									->where('company_id', '=', $this->user->company_id)
									->get(); 
		
		if(!empty($candidateArr))
		{
			$html               = '<select class="form-control" name="childStatus" id="childStatus" data-style="form-control">';
			foreach($candidateArr as $cval){
					$html      .= '<option value= "'.$cval->id.'">'.ucfirst($cval->name).'</option>';
					$flag 		=	1;
			} 
			$html 				.= '</select>';	
			
		}
		else
		{
			$html				=	'';
		}
	
		
		
		
		$countArr 				= DB::table('candidate_status')
									->select('*')
									->where('parent', $id)
									->where('company_id', '=', $this->user->company_id)
									->count(); 
		
		return Reply::dataOnly(['status' => 'success', 'data' => $html, 'counter' => $countArr, 'flag'=> $flag ]);
	}

/*** ########################################################
Controller    	: 	AdminCVTrackerController
Method			:	jobQuestion
Purpose			:	Return question by job id
Date			:	05-April-2021	
########################################################***/
    public function jobQuestion($jobID)
    {
        $this->jobQuestion 	= JobQuestion::with(['question'])->where('job_id', $jobID)->get();
        $view 				= view('admin.cvtracker.job-question', $this->data)->render();
        $count 				= count($this->jobQuestion);

        return Reply::dataOnly(['status' => 'success', 'view' => $view, 'count' => $count]);
    }

/*** ########################################################
Controller    	: 	AdminCVTrackerController
Method			:	edit
Purpose			:	Edit job application
Date			:	05-April-2021	
########################################################***/
    public function edit($id)
    {
        abort_if(!$this->user->cans('edit_job_applications'), 403);

        $this->statuses 		= ApplicationStatus::all();
        $this->application 		= JobApplication::find($id);
        $this->jobQuestion 		= JobQuestion::with(['question'])
            ->where('job_id', $this->application->job_id)->get();

        return view('admin.cvtracker.edit', $this->data);
    }



    public function createSchedule(Request $request, $id)
    	{
        abort_if(!$this->user->cans('add_schedule'), 403);
        $this->candidates = JobApplication::all();
        $this->users = User::company()->get();
        $this->scheduleDate = $request->date;
        $this->currentApplicant = JobApplication::findOrFail($id);
        return view('admin.cvtracker.interview-create', $this->data)->render();
    }

    public function storeSchedule(StoreRequest $request)
    	{
        abort_if(!$this->user->cans('add_schedule'), 403);

        $dateTime =  $request->scheduleDate . ' ' . $request->scheduleTime;
        $dateTime = Carbon::createFromFormat('Y-m-d H:i', $dateTime);

        $interviewSchedule = new InterviewSchedule();
        $interviewSchedule->job_application_id = $request->candidate;
        $interviewSchedule->schedule_date = $dateTime;
        $interviewSchedule->save();

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

        Notification::send($jobApplication, new CandidateScheduleInterview($jobApplication, $interviewSchedule));
        return Reply::redirect(route('admin.interview-schedule.index'), __('menu.interviewSchedule') . ' ' . __('messages.createdSuccessfully'));
    }

    public function store(StoreJobApplication $request)
    	{
        abort_if(!$this->user->cans('add_job_applications'), 403);
        $applicationStatus = ApplicationStatus::first();

        $jobApplication 					= new JobApplication();
        $jobApplication->full_name 			= $request->full_name;
        $jobApplication->job_id 			= $request->job_id;
        $jobApplication->status_id 			= $applicationStatus->id;
        $jobApplication->email 				= $request->email;
        $jobApplication->phone 				= $request->phone;
        $jobApplication->cover_letter 		= $request->cover_letter;
		
		
		$jobApplication->total_exp 			= $request->total_exp;
		
		$jobApplication->total_exp 				= $request->total_exp;
		$jobApplication->rel_exp 				= $request->rel_exp;
		$jobApplication->passport 				= $request->passport;
		$jobApplication->ctc 					= $request->ctc;
		$jobApplication->ectc 					= $request->ectc;
		$jobApplication->current_location 		= $request->current_location;
		$jobApplication->current_designation 	= $request->current_designation;
		$jobApplication->current_company 		= $request->current_company;
		
		
        $jobApplication->column_priority 	= 0;

        if ($request->hasFile('resume')) {
            $jobApplication->resume = Files::upload($request->resume,'resumes',null,null,false);
        }

        if ($request->hasFile('photo')) {
            $jobApplication->photo = Files::upload($request->photo,'candidate-photos');
        }
        $jobApplication->save();

        if (isset($request->answer) && !empty($request->answer)) {
                JobApplicationAnswer::where('job_application_id', $jobApplication->id)->delete();

                foreach ($request->answer as $key => $value) {
                    $answer = new JobApplicationAnswer();
                    $answer->job_application_id = $jobApplication->id;
                    $answer->job_id = $request->job_id;
                    $answer->question_id = $key;
                    $answer->answer = $value;
                    $answer->save();
                }
            }

        return Reply::redirect(route('admin.cvtracker.index'), __('menu.jobApplications') . ' ' . __('messages.createdSuccessfully'));
    }

    public function update(UpdateJobApplication $request, $id)
    	{
        abort_if(!$this->user->cans('edit_job_applications'), 403);

        $jobApplication = JobApplication::find($id);
        $jobApplication->full_name = $request->full_name;
        $jobApplication->status_id = $request->status_id;
        $jobApplication->email = $request->email;
        $jobApplication->phone = $request->phone;
        $jobApplication->cover_letter = $request->cover_letter;

        if ($request->hasFile('resume')) {

            $jobApplication->resume = Files::upload($request->resume,'resumes',null,null,false);
        }

        if ($request->hasFile('photo')) {
            $jobApplication->photo = Files::upload($request->photo,'candidate-photos');
        }

        $jobApplication->save();

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

    public function destroy($id)
    	{
        abort_if(!$this->user->cans('delete_job_applications'), 403);

        JobApplication::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }

    public function updateIndex(Request $request)
    	{
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

    public function table()
    	{
        abort_if(!$this->user->cans('view_job_applications'), 403);
        $this->boardColumns = ApplicationStatus::all();
        $this->locations = JobLocation::all();
        $this->jobs = Job::all();
        $this->skills = Skill::all();
        return view('admin.cvtracker.index', $this->data);
    }

    public function ratingSave(Request $request, $id)
    	{
        abort_if(!$this->user->cans('edit_job_applications'), 403);

        $application = JobApplication::findOrFail($id);
        $application->rating = $request->rating;
        $application->save();

        return Reply::success(__('messages.updatedSuccessfully'));
    }
   
    public function viewDetails(Request $request) 
		{
        $candidateCount = JobApplication::count();
        if($candidateCount > 0 && !is_null($this->activePackage->package->no_of_candidate_access) && $this->activePackage->package->no_of_candidate_access <= $candidateCount) {
            $this->message = __('modules.subscription.candidatePurchase');
            return Reply::error(__('modules.subscription.candidatePurchase'));
        }

        $application = JobApplication::findOrFail($request->id);
        $application->is_viewed = 1;
        $application->save();

        return Reply::dataOnly([
            'status' => 'success',
            'email' => $application->email,
            'phone' => $application->phone,
            'resume' => route('admin.cvtracker.download', $application->id)
        ]);
    }

    public function download($id)
    	{
        $jobApplication = JobApplication::findOrFail($id);
        if(!file_exists('user-uploads/resumes/'.$jobApplication->resume))
        {
            abort(404);
        }
        return response()->download('user-uploads/resumes/'.$jobApplication->resume);
    }
	
	 public function get_client_job(Request $request)
	 	{
       
	    $clientId 			= $html = '';
		
		$clientId 			= $request->clientId;

		
		$chkAdminRole = $this->user->hasRole('admin');

		// $chkAdminRole = DB::table('roles')
		// 				->select('*')
		// 				->where('id', $this->user->role->role_id)
		// 				->where('name', 'admin')
		// 				->count(); 
		
		if($chkAdminRole > 0)
		{
			if($clientId == "all" || $clientId ==''  ){
					$jobArr = DB::table('jobs')
									->select('id', 'title')
									->where('jobs.company_id', $this->user->company_id)		
									->where('status', 'active')
									->get();
			}else {
        			$jobArr = DB::table('jobs')
									->select('id', 'title')
									->where('client_id', $clientId)
									->where('jobs.company_id', $this->user->company_id)		
									->where('status', 'active')
									->get();
			}
		}
		else
		{
			if($clientId == "all" || $clientId ==''  ){
					$jobArr = DB::table('jobs')
						->join('job_assign', 'job_assign.job_id', '=', 'jobs.id')
						->select('jobs.id', 'jobs.title')
						->where('jobs.status', 'active')
						->where('job_assign.user_id', $this->user->id)
						->where('jobs.company_id', $this->user->company_id)		
						->get();
			}else {
        			$jobArr = DB::table('jobs')
						->join('job_assign', 'job_assign.job_id', '=', 'jobs.id')
						->select('jobs.id', 'jobs.title')
						->where('jobs.client_id', $clientId)
						->where('jobs.status', 'active')
						->where('job_assign.user_id', $this->user->id)
						->where('jobs.company_id', $this->user->company_id)		
						->get();
			}
			
			
		}	
		
		
		$html 				= '<option value="all">All Jobs</option>';
		foreach($jobArr as $jobvalue){
			$html          .= '<option value= "'.$jobvalue->id.'">'.ucfirst($jobvalue->title).'</option>';
		} 

        return Reply::dataOnly(['status' => 'success', 'data' => $html]);
    }

  	public function show($id)
    	{
        $this->application = JobApplication::with(['schedule', 'status', 'schedule.employee', 'schedule.comments.user'])->find($id);

		 $this->processArr 				= InterviewProcess::select('interview_processes.id', 'interview_processes.name')->where('status', "Y")->get();
           
		$this->statusArr  				= ApplicationStatus::all();
		
		$this->application_track 		=  DB::table('application_track')
										->join('job_applications', 'job_applications.id', '=', 'application_track.applicationid')
										->join('users', 'users.id', '=', 'application_track.userid')
										->leftJoin('interview_processes', 'interview_processes.id', '=', 'application_track.process_id')
										->leftJoin('application_status', 'application_status.id', '=', 'application_track.status_id')
										
										->select('users.name as account_holder_name', 'application_track.note', 'application_track.create_at', 'interview_processes.name', 'application_status.status', 'application_track.visible' )
										->where('job_applications.id', '=', $id)
										->orderBy('application_track.id', 'DESC')
										->get();
										
        $view = view('admin.cvtracker.interviewprocess', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

	public function hrprocess_store(Request $request)
		{
			$id = $note = $process = $status_id = $visible = $job_id =  '';
			
			$id  		= $request->id;
			$note  		= $request->note;
			$process  	= $request->process;
			$status_id  = $request->status_id;
			$visible  	= $request->visible;
			$job_id  	= $request->job_id;
			
			DB::table('job_applications')
					->where('id', @$id)
					->limit(1) 
					->update(array('status_id' => @$status_id, 'process_id' => @$process));
			
			
			$values = array(
								'job_id' 		 	=> $job_id ,
								'applicationid' 	=> $id, 
								'userid' 		 	=> auth()->user()->id,
								'process_id' 		=> $process,
								'status_id'  		=> $status_id,
								'note'			 	=> $note,
								'visible'			=> $visible,
								
							);
			DB::table('application_track')->insert($values);
					
			return Reply::redirect(route('admin.cvtracker.index'), __('menu.jobApplications') . ' ' . __('messages.updatedSuccessfully'));
	
	}

	 public function sehedulecreate(Request $request, $id, $process_id)
	 	{
	 	
		$this->process_id	= $process_id;	
	 	$this->candidate 	= JobApplication::find($id);
		
        $this->users 		= User::company()->get();
        $this->scheduleDate = $request->date;
        return view('admin.cvtracker.sehedulecreate', $this->data)->render();
    }
	
	public function schedulestore(Request $request)
		{

		$application_id  	= $request->application_id;
		$process_id   		= $request->process_id;
		$scheduleDate  		= $request->scheduleDate;
		$scheduleTime  		= $request->scheduleTime;
		$employee  			= $request->employee;
		$comment  			= $request->comment;
		
		$schedule_date 		=  $request->scheduleDate.' '.$request->scheduleTime;
        $schedule_date 		= Carbon::createFromFormat('Y-m-d H:i', $schedule_date);

		$user_id            =	$this->user->id;
		$company_id			=	$this->user->company_id;
		
		
		
		$values = array(
							'company_id' 		 => $company_id,
							'user_id' 		 	 => $user_id, 
							'process_id' 		 => $process_id,
							'application_id' 	 => $application_id,
							'schedule_date'  	 => $schedule_date,
							'comment'			 => $comment
						);
		DB::table('interview_hiring_process')->insert($values);
		$interview_hiring_process_id 	= DB::getPdo()->lastInsertId();
		
		if(!empty($request->employee)){
            
            foreach($request->employee as $employee)
            {
					$values = array(
										'interview_hiring_process_id'	=> $interview_hiring_process_id,
										'user_id' 		 	 			=> $employee, 
										'company_id' 					=> $company_id ,
								
									);
					DB::table('employer_hiring_process')->insert($values);
		
					
			}
		}				        
		echo 1; exit; 
    }	
	
	public function candidate_process_update1(Request $request)
    	{
        abort_if(!$this->user->cans('edit_job_applications'), 403);

        $jobApplication = JobApplication::find($id);
        $jobApplication->full_name = $request->full_name;
        $jobApplication->status_id = $request->status_id;
        $jobApplication->email = $request->email;
        $jobApplication->phone = $request->phone;
        $jobApplication->cover_letter = $request->cover_letter;

        if ($request->hasFile('resume')) {

            $jobApplication->resume = Files::upload($request->resume,'resumes',null,null,false);
        }

        if ($request->hasFile('photo')) {
            $jobApplication->photo = Files::upload($request->photo,'candidate-photos');
        }

        $jobApplication->save();

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
	
	public function candidate_process_update(Request $request)
		{
			$applicationId 		= $processId = '';
			
			$applicationId 		= $request->applicationId; 
			$processId 			= $request->processId; 
			
			if($processId  > 0)
			{
				DB::table('job_applications')
					->where('id', @$applicationId)
					->limit(1) 
					->update(array('process_id' => @$processId)); 	
			}			
						
	}
	
	public function statusUpdate(Request $request)
		{
	
			$application_id = $masterStatus = $childStatus =  $scheduleDate = $scheduleTime = $comment =  $team = '';
	
			
			$application_id	=	@$_REQUEST['application_id'];
			
			$masterStatus	=	@$_REQUEST['masterStatus'];
			$childStatus	=	@$_REQUEST['childStatus'];
			
			$scheduleDate	=	@$_REQUEST['scheduleDate'];
			$scheduleTime	=	@$_REQUEST['scheduleTime'];
			
			$comment		=	@$_REQUEST['comment'];
			$team			=	@$_REQUEST['team'];
			
			if(!empty(@$application_id))
			{
			
					$job_applications	=	'';
					$job_applications	=	DB::table('job_applications')->select('*')->where('id', $application_id)->first();
					
					// UPDATE APPLICATION					
					$application_update_arr	=	array();
					if(!empty(@$masterStatus))
					{
						$application_update_arr['master_status_id']	= $masterStatus; 	
					}
					if(!empty(@$childStatus))
					{
						$application_update_arr['child_status_id']	= $childStatus; 	
					}else{
						$application_update_arr['child_status_id']	= ''; 		
					}
					DB::table('job_applications')->where('id', @$application_id)->limit(1) ->update($application_update_arr);
					
					
					// INSERT STATUS TRACKER
					$application_tracker_arr							=	array();
					
					$application_tracker_arr['job_id']					=	@$job_applications->job_id;
					$application_tracker_arr['application_id']			=	@$application_id;
					
					if(!empty(@$scheduleDate)){
						$application_tracker_arr['schedule_date']		=	@$scheduleDate;
					}
					if(!empty(@$scheduleTime)){
						$application_tracker_arr['schedule_time']		=	@$scheduleTime;
					}
					if(!empty(@$masterStatus)){
						$application_tracker_arr['status_master_id']	=	@$masterStatus;
					}
					if(!empty(@$childStatus)){
						$application_tracker_arr['child_status_id']		=	@$childStatus;
					}
					if(!empty(@$comment)){
						$application_tracker_arr['note']				=	@$comment;
					}
					if(!empty(@$team)){
						$team 											= implode(',', $team);
						$application_tracker_arr['team']				=	@$team;
					}
					$application_tracker_arr['update_by']				=	$this->user->id;
					DB::table('application_tracker')->insert($application_tracker_arr);
					
					
					$statusHtml 		=	'';
					$masterStatusName	=	$masterStatusRow = '';
					$childStatusName 	=	$childStatusRow = '';
					
					// GET MASTER STATUS NAME 
					if($masterStatus > 0)
					{
						
						$masterStatusRow	=	DB:: table('master_status')->select
						('master_status.name')->where('id', '=', $masterStatus)->first(); 
						$masterStatusName	=	$masterStatusRow->name;
						$statusHtml 		=   @$masterStatusName;
					}
					// GET CHILD STATUS NAME 
					if(@$childStatus > 0 && $masterStatus > 0)
					{
						
						$childStatusRow  =  DB::table('candidate_status')
											->select('candidate_status.name')
											->where('candidate_status.company_id', '=', 
											$this->user->company_id)
											->where('candidate_status.parent', '=', $masterStatus)
											->where('candidate_status.id', '=', $childStatus)
											->first();
						$childStatusName = $childStatusRow->name;
						$statusHtml 		= @$statusHtml.'  -  '.@$childStatusName ;					
					}
						
					$dateTime 		=	$request->scheduleDate . ' ' . $request->scheduleTime;
        			$dateTime 		=	Carbon::createFromFormat('Y-m-d H:i', $dateTime);
					
					$statusHtml		=	$statusHtml."   Schedule:  ".$dateTime;
					GeneralMethod :: manageTimeline(auth()->user()->id, $application_id, @$job_applications->job_id, $masterStatus, $masterStatusName, $statusHtml);	
					
					
					
					return Reply::dataOnly(['status' => 'success']);
			}
			
			
			//return Reply::redirect(route('admin.interview-schedule.index'), __('menu.interviewSchedule').' '.__('messages.createdSuccessfully'));
	}
		
	public function resume($id)
    	{
        $this->application = JobApplication::findOrFail($id);
		
		$view = view('admin.cvtracker.resume', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
		
    }
		 
    public function export($clients, $jobs, $source, $search_passport, $search_np_id, $status, $start, $length)
    	{
        $filters = [
						'clients' 			=> $clients,
						'jobs' 				=> $jobs,
						'source' 			=> $source,
						'search_passport' 	=> $search_passport,
						'search_np_id' 		=> $search_np_id,
						'status' 			=> $status,
						'start' 			=> $start,
						'length' 			=> $length,
						'loginUserId'		=> $this->user->id,
						'loginUserRole'		=> $this->user->role->role_id
					];

        $data = [
					'company' => $this->companyName
				];

        return Excel::download(new CvTrackerExport($filters, $data), 'cvtracker.xlsx', ExcelExcel::XLSX);
    }
		
	public function create()
    	{
        abort_if(!$this->user->cans('add_job_applications'), 403);

        $candidateCount = JobApplication::count();
            if($candidateCount > 0 && $this->activePackage->package->no_of_candidate_access !== null && $this->activePackage->package->no_of_candidate_access <= $candidateCount) {
            $this->message = __('modules.subscription.candidatePurchase');
            return view('admin.subscription.ask_purchase', $this->data);
        }
        $this->jobs = Job::activeJobs();
        return view('admin.cvtracker.create', $this->data);
    }


/*** ########################################################
Controller    	: 	AdminCVTrackerController
Method			:	data
Purpose			:	Return json data for cv tracker
Date			:	05-April-2021	
########################################################***/

    public function data_old(Request $request)
    	{
		
        	abort_if(!$this->user->cans('view_job_applications'), 403);

			$chkAdminRole = $this->user->hasRole('admin');

			// $chkAdminRole = DB::table('roles')
			// 				->select('*')
			// 				->where('id', $this->user->role->role_id)
			// 				->where('name', 'admin')
			// 				->count(); 
		
		
        $jobApplications 	= JobApplication::select('job_applications.*', 'jobs.title', 'job_locations.location', 'application_status.status', 'clients.name as client_name', 'users.name as posted_by', 'master_status.name', 'master_status.css_style', 'qualification_master.name as qualification_name')
            ->join('jobs', 'jobs.id', 'job_applications.job_id')
            ->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')
			
			->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
			
			
            ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
			
			->leftjoin('clients', 'clients.id', 'jobs.client_id')
			
			->leftjoin('users', 'users.id', 'job_applications.submitted_by')
			->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
			
            ->leftjoin('application_status', 'application_status.id', 'job_applications.status_id');
			
		
		 if($chkAdminRole == 0)
		 {	
		 	$jobApplications 	= $jobApplications->join('job_assign', 'job_assign.job_id', 'jobs.id');
			$jobApplications 	= $jobApplications->where('job_assign.user_id', $this->user->id);
		 }
		 	

		if ($request->status > 0 ) {
			$jobApplications = $jobApplications->where('job_applications.master_status_id', $request->status);
		}		
		
        // Filter By jobs
        if ($request->jobs != 'all' && $request->jobs != '') {
            $jobApplications = $jobApplications->where('job_applications.job_id', $request->jobs);
        }

        // Filter By skills
        if ($request->skill != 'all' && $request->skill != '') {
            $jobApplications = $jobApplications->whereIn('job_skills.skill_id',  explode(',', $request->skill));
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
            $jobApplications = $jobApplications->where('job_applications.full_name', 'like',  '%'.$request->applicant_search .'%');
        }
		
		if ($request->clients >0) {
            $jobApplications = $jobApplications->where('jobs.client_id', $request->clients);
        }
		
		
		//Email 
		if ($request->email_search != 'all' && $request->email_search != '' ) {
            $jobApplications = $jobApplications->where('job_applications.email', 'like',  '%'.$request->email_search .'%');
        }
		// SUBMITTED
		if ($request->source != 'all' && $request->source != '') {
            $jobApplications = $jobApplications->where('job_applications.submitted_by', $request->source);
        }
		// PHONE
		if ($request->phone_search != '') {
			
             $jobApplications = $jobApplications->where('job_applications.phone', 'like',  '%'.$request->phone_search .'%'); 
        }
				
		// NOTICE PERIOD
		if ($request->search_passport != 'all' && $request->search_passport != '' && $request->search_passport !='undefined' ) {
             $jobApplications = $jobApplications->where('job_applications.passport',  $request->search_passport); 
        }
				
		// NOTICE PERIOD
		if ( ($request->search_np_id != 'all') &&  ($request->search_np_id != '') && ($request->search_np_id > 0) ) 
		{
			if($request->search_np_id == 1){  			// IMMEDIATE 
				$jobApplications = $jobApplications->where('job_applications.serving_notice_period_mode',  2);   	
			}else if($request->search_np_id == 2){  	// SERVING 
				$jobApplications = $jobApplications->where('job_applications.serving_notice_period_mode',  1);   	
			}else if($request->search_np_id == 3){  	// 0-15 Days 				
				$jobApplications = $jobApplications->whereBetween('job_applications.np_id', [1, 15]);	
			}else if($request->search_np_id == 4){  	// 16-30 Days 				
				$jobApplications = $jobApplications->whereBetween('job_applications.np_id', [16, 30]);	
			}else if($request->search_np_id == 5){  	// 16-30 Days 				
				$jobApplications = $jobApplications->whereBetween('job_applications.np_id', [31, 45]);	
			}else if($request->search_np_id == 6){  	// 16-30 Days 				
				$jobApplications = $jobApplications->whereBetween('job_applications.np_id', [46, 60]);	
			}else if($request->search_np_id == 7){  	// 16-30 Days 				
				$jobApplications = $jobApplications->whereBetween('job_applications.np_id', [61, 75]);	
			}else if($request->search_np_id == 8){  	// 16-30 Days 				
				$jobApplications = $jobApplications->whereBetween('job_applications.np_id', [76, 90]);	
			}else if($request->search_np_id == 9){  	// 16-30 Days 				
				$jobApplications = $jobApplications->where('job_applications.np_id','>', 90);	
			}
        }		
		
		
		// CURRENT DESIGNATION
		if ($request->sea_cur_designation != '') {
             $jobApplications = $jobApplications->where('job_applications.current_designation', 'like',  '%'.$request->sea_cur_designation .'%'); 
        }
		// CURRENT COMPANY
		if ($request->sea_cur_company != '') {
             $jobApplications = $jobApplications->where('job_applications.current_company', 'like',  '%'.$request->sea_cur_company .'%'); 
        }
		// CURRENT LOCATION
		if ($request->sea_cur_location != '') {
             $jobApplications = $jobApplications->where('job_applications.current_location', 'like',  '%'.$request->sea_cur_location .'%'); 
        }
		
		 if (\request('search') != "")
		{
			$search					=	request('search');	
			$jobApplications		=	$jobApplications->where('job_applications.full_name', 'LIKE', "%$search%");
		}
		
		
		
		
		
		
        $jobApplications 		= $jobApplications->groupBy('job_applications.id');
		
		$jobApplications 		= $jobApplications->orderBy('job_applications.id', 'DESC');
		
        return DataTables::of($jobApplications)
           
			->editColumn('created_at', function ($row) {
				$created_at = '';
				$created_at = GeneralMethod::ymdTodmy($row->created_at);   
				return '
				<div class="text-center">
					<a href="#" data-toggle="tooltip" data-placement="right" title="'.$created_at.'&nbsp;'.'">
						<i class="fa fa-calendar text-dark"></i>
					</a>
				</div>';	
            })

            
			/*->editColumn('full_name', function ($row) use($chkAdminRole) 
			{
				$Html = '';
				if($chkAdminRole > 0){
					$Html =  '<a href="javascript:;" class="show-detail" data-widget="control-sidebar" data-slide="true" data-row-id="' . $row->id . '">' . ucwords($row->full_name) . '</a>';
				}else{
					if($row->submitted_by == $this->user->id){
						$Html = '<a href="javascript:;" class="show-detail" data-widget="control-sidebar" data-slide="true" data-row-id="' . $row->id . '">' . ucwords($row->full_name) . '</a>';
					}else{
						$Html =  ucwords($row->full_name);
					}
				}
				return $Html;
			})*/
							
			->editColumn('full_name', function ($row) use($chkAdminRole) 
			{
				
					$duplicate				=	0;
					$duplicate				=	$row->duplicate;
					
					$mstatus_text			=	'';					
					$company_id				= 	$this->user->roles[0]->company_id;
					$app_duplicate			=	$this->user->company->app_duplicate;
					$app_duplicate_days 	=	$this->user->company->app_duplicate_days;
					
					if($duplicate	==1)											// CHECK RECORD IS DUPLICATE
					{
							if(($app_duplicate == 1)||($app_duplicate == 2)) 		// CHECK SYSTEM MODE SET IN DUPLICATE
							{
							
									if($app_duplicate == 1)							// CHECK DUPLICATE JOBWISE WITH TIME INTERVAL IN DAYS FOR- 1
									{
										$application_cnt		= DB::table('job_applications')->select('*')
																->where('company_id', $company_id )
																->where('job_id', $row->job_id )
																->where('email', $row->email )
																->where('job_applications.created_at', '>', DB::raw('DATE_SUB(CURDATE(), INTERVAL '.@$app_duplicate_days.' DAY)')) 
																->where('job_applications.duplicate', '=', 1 )
																->count();
										if($application_cnt > 0){
												$mstatus_text	=	'
												<div class="d-inline-grid">
													<div class="duplicate-style m-t-1" style="">
														<div class="tooltip font-weight-500">
															<span class="d-flex font-weight-700 duplicate-child" style="">.</span>
															<span class="tooltiptext font-weight-500">Duplicate Appilcant</span>
														</div>
													</div>
													<div class="duplicate-style bg-warning m-t-1" style="">
														<div class="tooltip font-weight-500">
															<span class="d-flex font-weight-700 duplicate-child" style="">.</span>
															<span class="tooltiptext font-weight-500">
																Assessment Sub Stage - Rejected | Not Intrested
															</span>
														</div>
													</div>
												</div>';
										}				
											
									}
									else if($app_duplicate == 2)					
									// CHECK DUPLICATE OVERALL SYSTEM WITH TIME INTERVAL IN DAYS FOR- 2
									{
										$application_cnt		= DB::table('job_applications')->select('job_applications.id')
																->where('company_id', $company_id)
																//->where('job_id', $job_id)
																->where('job_applications.email', $row->email )
																->where('job_applications.created_at', '>', DB::raw('DATE_SUB(CURDATE(), INTERVAL '.@$app_duplicate_days.' DAY)')) 
																->where('job_applications.duplicate', '=', 1 )
																->count();	
										if($application_cnt > 0)
										{
											$mstatus_text	=	'
												<div class="d-inline-grid">
													<div class="duplicate-style m-t-1" style="">
														<div class="tooltip font-weight-500">
															<span class="d-flex font-weight-700 duplicate-child" style="">D</span>
															<span class="tooltiptext font-weight-500">Duplicate Appilcant</span>
														</div>
													</div>
													<div class="duplicate-style bg-warning m-t-1" style="">
														<div class="tooltip font-weight-500">
															<span class="d-flex font-weight-700 duplicate-child" style="">.</span>
															<span class="tooltiptext font-weight-500">
																Assessment Sub Stage - Rejected | Not Intrested
															</span>
														</div>
													</div>
												</div>';
										}						
																
									}
									
									
									
							}
					}		
					
					$full_name				=	'';
					if(strlen($row->full_name)> 8){ 		
						$full_name			=	'<div class="tooltip">'.ucwords(substr($row->full_name, 0, 8)).'...<span class="tooltiptext"><span>'.ucwords($row->full_name).'<br></span></div>';				
					}else{
						$full_name			=	'<div class="tooltip">'.ucwords(substr($row->full_name, 0, 8)).'<span class="tooltiptext"><span>'.ucwords($row->full_name).'<br></span></div>';				
					}
								
					if($chkAdminRole > 0)
					{
									$Html =  '<a class="align-items-center d-flex show-detail" href="javascript:void(0);" class="show-detail" data-widget="control-sidebar" data-slide="true" data-row-id="' . $row->id . '">' . $mstatus_text.$full_name . '</a>';
					}
					else
					{
							if($row->submitted_by == $this->user->id)
							{
									$Html = '<a class="align-items-center d-flex show-detail" href="javascript:void(0);" class="show-detail" data-widget="control-sidebar" data-slide="true" data-row-id="' . $row->id . '">' .$mstatus_text. $full_name . '</a>';
							}
							else
							{
									$Html =  $mstatus_text.$full_name;
							}
					}				
					return $Html;
					
						
			})


			 ->editColumn('client_name', function ($row) {
			 
				$client_name				=	'';
				if(strlen($row->full_name)> 8){ 		
					$client_name			=	'<div class="tooltip">'.ucwords(substr($row->client_name, 0, 8)).'...<span class="tooltiptext"><span>'.ucwords($row->client_name).'<br></span></div>';				
				}else{
					$client_name			=	'<div class="tooltip">'.ucwords(substr($row->client_name, 0, 8)).'<span class="tooltiptext"><span>'.ucwords($row->client_name).'<br></span></div>';				
				}
		
                return ucfirst($client_name);
            })
		/*	->editColumn('title', function ($row) {
               // $out = strlen($row->title) > 30 ? substr($row->title,0,30)."..." : $row->title;
               // return ucwords($out);
			   
			   if(strlen($row->title)> 15)
				{
					$html			=	'<div class="tooltip">'.ucwords(substr($row->title, 0, 15)).'...<span class="tooltiptext"><span>'.ucwords($row->title).'<br></span></div>';				
				}
				else
				{
					$html			=	'<div class="tooltip">'.ucwords(substr($row->title, 0, 15)).'<span class="tooltiptext"><span>'.ucwords($row->title).'<br></span></div>';
				}
				return $html;
				
            })*/
			 ->editColumn('posted_by', function ($row) {
               //return ucfirst($row->posted_by);
			   		$posted_by				=	'';
					if(strlen($row->posted_by)> 8){ 		
						$posted_by			=	'<div class="tooltip">'.(substr($row->posted_by, 0, 8)).'...<span class="tooltiptext"><span>'.($row->posted_by).'<br></span></div>';				
					}else{
						$posted_by			=	'<div class="tooltip">'.(substr($row->posted_by, 0, 8)).'<span class="tooltiptext"><span>'.($row->posted_by).'<br></span></div>';				
					}
					return $posted_by;
					
            })			
			
			->editColumn('email', function ($row) {
                //return $row->email;
				
					$email				=	'';
					if(strlen($row->email)> 8){ 		
						$email			=	'<div class="tooltip">'.(substr($row->email, 0, 8)).'...<span class="tooltiptext"><span>'.($row->email).'<br></span></div>';				
					}else{
						$email			=	'<div class="tooltip">'.(substr($row->email, 0, 8)).'<span class="tooltiptext"><span>'.($row->email).'<br></span></div>';				
					}
					return $email;
            })
			->editColumn('phone', function ($row) {
                return $row->phone;
            })
			->editColumn('ctc', function ($row) {
                //return $row->ctc;
				
					$ctc				=	'';
					if(strlen($row->ctc)> 8){ 		
						$ctc			=	'<div class="tooltip">'.ucwords(substr($row->ctc, 0, 8)).'...<span class="tooltiptext"><span>'.ucwords($row->ctc).'<br></span></div>';				
					}else{
						$ctc			=	'<div class="tooltip">'.ucwords(substr($row->ctc, 0, 8)).'<span class="tooltiptext"><span>'.ucwords($row->ctc).'<br></span></div>';				
					}
					return $ctc;
	
            })
			->editColumn('ectc', function ($row) {
                //return $row->ectc;
					$ectc				=	'';
					if(strlen($row->ectc)> 8){ 		
						$ectc			=	'<div class="tooltip">'.ucwords(substr($row->ectc, 0, 8)).'...<span class="tooltiptext"><span>'.ucwords($row->ectc).'<br></span></div>';				
					}else{
						$ectc			=	'<div class="tooltip">'.ucwords(substr($row->ectc, 0, 8)).'<span class="tooltiptext"><span>'.ucwords($row->ectc).'<br></span></div>';				
					}
					return $ectc;
	
            })
			->editColumn('total_exp', function ($row) {
                return $row->total_exp;
            })
			
			->editColumn('rel_exp', function ($row) {
                return $row->rel_exp;
            })
			->editColumn('passport ', function ($row) {
                return $row->passport;
            })
			->editColumn('np_id', function ($row) {
				$np = $row->np_id;
				if(empty($np)){
					$np = 'N/A';	
				}


				if($row->serving_notice_period_mode==0)
					{
						if(!empty($row->np_id)){
							$np		=	$row->np_id. '  Days';
						}
					}else{
							$np		=	$row->np_id;
					}
			
				
				if(strlen($np)> 10)
				{
					$html			=	'<div class="tooltip">'.ucwords(substr($np, 0, 10)).'...<span class="tooltiptext"><span>'.ucwords($np).'<br></span></div>';				
				}
				else
				{
					$html			=	'<div class="tooltip">'.ucwords(substr($np, 0, 10)).'<span class="tooltiptext"><span>'.ucwords($np).'<br></span></div>';
				}
				return $html;
				
            })
						
			->editColumn('current_designation ', function ($row) {
				if(strlen($row->current_designation)> 10)
				{
					$html			=	'<div class="tooltip">'.ucwords(substr($row->current_designation, 0, 10)).'...<span class="tooltiptext"><span>'.ucwords($row->current_designation).'<br></span></div>';				
				}
				else
				{
					$html			=	'<div class="tooltip">'.ucwords(substr($row->current_designation, 0, 10)).'<span class="tooltiptext"><span>'.ucwords($row->current_designation).'<br></span></div>';
				}
				return $html;
				
               //return ucfirst($row->current_designation);
            })
			->editColumn('current_company', function ($row) {
                //return ucfirst($row->current_company);
				if(strlen($row->current_company)> 10)
				{
					$html			=	'<div class="tooltip">'.ucwords(substr($row->current_company, 0, 10)).'...<span class="tooltiptext"><span>'.ucwords($row->current_company).'<br></span></div>';				
				}
				else
				{
					$html			=	'<div class="tooltip">'.ucwords(substr($row->current_company, 0, 10)).'<span class="tooltiptext"><span>'.ucwords($row->current_company).'<br></span></div>';
				}
				return $html;
				
            })	
			->editColumn('current_location', function ($row) {
                //return ucfirst($row->current_location );
				if(strlen($row->current_location)> 10)
				{
					$html			=	'<div class="tooltip">'.ucwords(substr($row->current_location, 0, 10)).'...<span class="tooltiptext"><span>'.ucwords($row->current_location).'<br></span></div>';				
				}
				else
				{
					$html			=	'<div class="tooltip">'.ucwords(substr($row->current_location, 0, 10)).'<span class="tooltiptext"><span>'.ucwords($row->current_location).'<br></span></div>';
				}
				return $html;
            })
			->editColumn('qualification_name', function ($row) {
                //return ucfirst($row->title);
				if(strlen($row->qualification_name)> 10)
				{
					$html			=	'<div class="tooltip">'.ucwords(substr($row->qualification_name, 0, 10)).'...<span class="tooltiptext"><span>'.ucwords($row->qualification_name).'<br></span></div>';				
				}
				else
				{
					$html			=	'<div class="tooltip">'.ucwords(substr($row->qualification_name, 0, 10)).'<span class="tooltiptext"><span>'.ucwords($row->qualification_name).'<br></span></div>';
				}
				return $html;
            })
			
			->editColumn('status', function ($row) use($chkAdminRole)
			{
				$name		=	'';
				if(!empty($row->name)){
					$name		=	@$row->name;	 
				}
				
				$action 	= $name.'&nbsp;';
				$action 	.= '<a href="Javascript:void(0);" onclick="updateCandidateStatus('.$row->id.');">Update</a>';
				return $action;
			})

			->editColumn('interview_status_level', function ($row) use($chkAdminRole)
			{
				$name		=	'N/A';
				if(!empty($row->child_status_id)&& $row->child_status_id > 0){
					$childStatus = '';
					$childStatus = DB::table('candidate_status')->select('*')->where('id', $row->child_status_id)->first(); 
						if(!empty($childStatus)){
							$name 	= @$childStatus->name;
						}
				}
				return $name;
			})
			
		    
            ->editColumn('title', function ($row) {
				//return ucfirst($row->title);
				
				if(strlen($row->title)> 15)
				{
					$html			=	'<div class="tooltip">'.ucwords(substr($row->title, 0, 15)).'...<span class="tooltiptext"><span>'.ucwords($row->title).'<br></span></div>';				
				}
				else
				{
					$html			=	'<div class="tooltip">'.ucwords(substr($row->title, 0, 15)).'<span class="tooltiptext"><span>'.ucwords($row->title).'<br></span></div>';
				}
				return $html;
				
            })
			->editColumn('interview_process', function ($row) use($chkAdminRole)
			{
			   return ucfirst($row->title);
			})
			
			
->editColumn('resume', function ($row) 
{
$html 			= '';
if(!empty($row->resume))
{
	$html 		=	'<p class="" id="resume-'.$row->id.'">
								<a  href="'. route('admin.job-applications.download', $row->id) .'" class="btn">
									<i class="fa fa-file text-primary" aria-hidden="true"></i>
								</a>
							</p>'; 
							
}
else
{
	$html 		= 'N/A';
}
return $html; 	
	
	
})

			 ->addColumn('action', function ($row) use($chkAdminRole) {
			
		
                $action = '';
				
				if($chkAdminRole > 0)
				{
					if ($this->user->cans('edit_job_applications')) {
                    $action .= '<a href="' . route('admin.job-applications.edit', [$row->id]) . '" class="btn"
                      data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil text-primary" aria-hidden="true"></i></a>';
                	}

					if ($this->user->cans('delete_job_applications')) {
						$action .= ' <a href="javascript:;" class="btn sa-params"
						  data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times text-danger" aria-hidden="true"></i></a>';
					}
				} 
				else
				{
					if($row->submitted_by == $this->user->id)
					{
						if ($this->user->cans('edit_job_applications')) {
							$action .= '<a href="' . route('admin.job-applications.edit', [$row->id]) . '" class="btn"
							data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil text-warning" aria-hidden="true"></i></a>';
						}
						
						if ($this->user->cans('delete_job_applications')) {
							$action .= ' <a href="javascript:;" class="btn sa-params"
							data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times text-danger" aria-hidden="true"></i></a>';
						}
					}
					else
					{
						$action = 'N/A';	
					}
						
				}
				return $action;				
				
            })

			->editColumn('chkbox', function ($row)
			{
                return '
				<div class="form-check form-check-sm form-check-custom form-check-solid">
                  <input class="form-check-input" type="checkbox" value="1" />
                </div>';
            })
			
			->editColumn('Name', function ($row) {
					
					$bgTextDecore			=	$strVal	=	'';
					$str					=	trim(@$row->full_name);
					$str	= $strVal		=	strtolower($str[0]);
					$str					=	ord($str);
					if($str >=97 && $str <=101){
						$bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
					}else if($str >=102 && $str <=106){
						$bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
					}else if($str >=107 && $str <=111){
						$bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
					}else if($str >=112 && $str <=116){
						$bgTextDecore		=	'fs-3 bg-light-success text-success';	
					}else if($str >=117 && $str <=121){
						$bgTextDecore		=	'fs-3 bg-dark text-secondary';	
					}else {
						$bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
					}	
					
                    return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);"  data-row-id="'.$row->id.'" class="show-application-detail">
                      <div class="symbol-label '.@$bgTextDecore.' border-radius-5">'.strtoupper(@$strVal).'</div>
                    </a>
                  </div>                  
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'"  class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">'.ucwords(substr(@$row->full_name, 0, 10)).'</a>
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'"  class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-application-detail">'.ucwords(substr(@$row->current_designation, 0, 10)).'</a>
                  </div>'; 
            })
			
			->editColumn('Contacts', function ($row) {
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.@$row->phone.'</a>
                    <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">'.substr(@$row->email, 0, 15).'</a>
                  </div>';
            })
			->editColumn('Exp', function ($row) {
					
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.@$row->total_exp.'+</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.@$row->rel_exp.'+</span>
                  </div>';
            })
			->editColumn('Salary', function ($row) {
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">18 LPA</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7">14 LPA</span>
                  </div>';
            })
			->editColumn('NoticePeriod', function ($row) {
					
					$np = $row->np_id;
					if(empty($np)){
						$np = 'N/A';	
					}
					
					
					if($row->serving_notice_period_mode==0)
					{
						if(!empty($row->np_id)){
								$np		=	$row->np_id. '  Days';
						}
					}
					else{
							$np		=	$row->np_id;
					}
					
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.@$np.'</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.ucwords(substr(@$row->current_company, 0, 10)).'</span>
                  </div>';
            })
			->editColumn('Qualifaction', function ($row) {
                    return ' <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.ucwords(substr($row->qualification_name, 0, 10)).'</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.strtoupper(substr($row->skills, 0, 10)).'</span> 
                  </div>';
            })
			->editColumn('Location', function ($row) {
					if(empty($row->city)){
						$city	=	'------';	
					}else{
						$city	=	@$row->city;	 	
					}
					if(empty($row->country)){
						$country	=	'---';	
					}else{
						$country	=	@$row->country;	 	
					}
					if(empty($row->state)){
						$state	=	'---';	
					}else{
						$state	=	@$row->state;	 	
					}					
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.ucwords(substr($city, 0, 10)).'</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.strtoupper(substr($state, 0, 3)).' | '.strtoupper(substr($country, 0, 10)).'</span>
                  </div>';
            })
			->editColumn('Source', function ($row) {
                    return '<div class="d-flex flex-column">
                    <div class="symbol-group symbol-hover">
                      <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Sonali kaushik" data-original-title="">
                        <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_19.jpg">
                      </div>
                      <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Abu Baker" data-original-title="">
                        <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_22.jpg">
                      </div>
                    </div>
                    <span class="text-muted fw-bold text-muted d-block fs-7">TCS</span>
                  </div>';
            })
			->editColumn('UploadDate', function ($row) {
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.Carbon::parse($row->created_at)->format('d M, Y').'</a>
                  </div>';
            })
			->editColumn('Requisitions', function ($row) {
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.ucwords(substr($row->title, 0, 10)).'</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.ucwords(substr($row->client_name, 0, 10)).'</span>
                  </div>';
            })
			->editColumn('Stage', function ($row) {
					
					$name		=	'--------';
					if(!empty($row->child_status_id)&& $row->child_status_id > 0){
						$childStatus = '';
						$childStatus = DB::table('candidate_status')->select('*')->where('id', $row->child_status_id)->first(); 
						if(!empty($childStatus)){
							$name 	= @$childStatus->name;
						}
					}
				
                    return '<div class="d-flex flex-column">
                        <span class="badge fw-bolder fs-6 '.@$row->css_style.' px-2 py-1">'.ucwords(substr($row->name, 0, 10)).'</span>
                        <span class="text-muted fw-bold text-muted d-block fs-7">'.@$name.'</span>
                    </div>';
            })
			


            ->rawColumns(['chkbox', 'Name', 'Contacts', 'Exp', 'Salary', 'NoticePeriod', 'Qualifaction', 'Location', 'Source', 'UploadDate', 'Requisitions', 'Stage'])
            ->addIndexColumn()
            ->make(true);
   
   
   
   
   
   
   
   
    }
	

	 public function data(Request $request)
    	{
		
        	abort_if(!$this->user->cans('view_job_applications'), 403);

			$salaryMode 		=	GeneralMethod::salaryMode();
			$currencySymbole 	=	GeneralMethod::currencySymbole();
			 
			$chkAdminRole 		= DB::table('roles')
								->select('*')
								->where('id', $this->user->role->role_id)
								->where('name', 'admin')
								->count(); 
		
		
        $jobApplications 	= JobApplication::select('job_applications.*', 'jobs.title', 'job_locations.location', 'application_status.status', 
		'clients.name as client_name', 'users.name as posted_by', 
		
		
		'users.image as posted_by_image', 
		
		'master_status.name', 'master_status.css_style', 'qualification_master.name as qualification_name',
		
		'state_list.state as state_name'
		)
            ->join('jobs', 'jobs.id', 'job_applications.job_id')
            ->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')		
			->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
            ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
			->leftjoin('clients', 'clients.id', 'jobs.client_id')
			->leftjoin('users', 'users.id', 'job_applications.submitted_by')
			->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
            ->leftjoin('application_status', 'application_status.id', 'job_applications.status_id')
			->leftjoin('state_list', 'state_list.id', 'job_applications.state');
			
		 if($chkAdminRole == 0)
		 {	
		 	$jobApplications 	= $jobApplications->join('job_assign', 'job_assign.job_id', 'jobs.id');
			$jobApplications 	= $jobApplications->where('job_assign.user_id', $this->user->id);
		 }
		 	
       
		//FULLNAME		
		
		/*if ($request->applicant_search != 'all' && $request->applicant_search != '') {
            $jobApplications = $jobApplications->where('job_applications.full_name', 'like',  '%'.$request->applicant_search .'%');
        }
		*/
		
		 if (\request('search') != "")
		{
			$search					=	request('search');	
			$jobApplications		=	$jobApplications->where('job_applications.full_name', 'LIKE', "%$search%");
		}
		
				
        $jobApplications 		= $jobApplications->groupBy('job_applications.id');		
		$jobApplications 		= $jobApplications->orderBy('job_applications.id', 'DESC');
		
        return DataTables::of($jobApplications)
           
			
			->editColumn('chkbox', function ($row) {
                    return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
            })
			->editColumn('Name', function ($row) {
				$bgTextDecore 	= $strVal = '';
				$str 			= trim(@$row->full_name);
				if(!empty($str))
				{
						
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
				
				}
				
				

		return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
		<a href="javascript:void(0);"   data-row-id="' . @$row->id . '" class="show-application-detail">
		  <div class="symbol-label ' . @$bgTextDecore . ' border-radius-5">' . strtoupper(@$strVal) . '</div>
		</a>
	  </div>                  
	  <div class="d-flex flex-column">
		<div class="">
			<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="'.ucwords(@$row->full_name) .'">' .ucwords(substr(@$row->full_name, 0, 7)). '</a>
		</div>
		<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-application-detail" data-toggle="tooltip" title="' .ucwords(@$row->current_designation). '">' .ucwords(substr(@$row->current_designation, 0, 7)). '</a>
	</div>';
	
					
					
})
			
			
			
			->editColumn('Contacts', function ($row) {
			
				
				 return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.@$row->phone.'</a>
                    <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary" data-toggle="tooltip" title="'.@$row->email.'">'.substr(@$row->email, 0, 10).'</a>
                  </div>';
				



            })			
			->editColumn('Exp', function ($row) {
            return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$row->total_exp . '+</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7">' . @$row->rel_exp . '+</span>
                  </div>';
        })
			->editColumn('Salary1', function ($row) use($salaryMode) {				
            return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.@$row->ctc.'</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.@$row->ectc.'</span>
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
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.$current_ectc_cur.@$ectc.'/'.$current_ectc_mode.'</span>
                  </div>';
        })
		
			->editColumn('NoticePeriod', function ($row) {
					
					$np = $row->np_id;
					if(empty($np)){
						$np = 'N/A';	
					}
					
					
					if($row->serving_notice_period_mode==0)
					{
						if(!empty($row->np_id)){
								$np		=	$row->np_id. '  Days';
						}
					}
					else{
							$np		=	$row->np_id;
					}
					
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.@$np.'</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="'.ucwords(@$row->current_company).'">'.ucwords(substr(@$row->current_company, 0, 10)).'</span>
                  </div>';
            })
			->editColumn('Qualifaction', function ($row) {
                    return ' <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="'.ucwords($row->qualification_name).'">'.ucwords(substr($row->qualification_name, 0, 10)).'</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="'.strtoupper($row->skills).'">'.strtoupper(substr($row->skills, 0, 15)).'</span> 
                  </div>';
            })
				
			->editColumn('Location123', function ($row) {
			
			
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
											$row_country_name	= ucwords($cval['sortname']);	
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
            if (empty($row->city)) {
                $row_city_name 	= '--------';
            } 
			else 
			{
				if(@$row->country == 101)
				{
					$row_city 				=	DB::table('city_list')->select('*')->where('city_id', @$row->city_id)->first();
					$row_city_name 			=	substr(@$row_city->city, 0, 6);
				}
				else
				{
					$row_city_name = substr(@$row->city, 0, 6); 
				}
                
            }
			
						
            return '<div class="d-flex flex-column">
                    	<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . ucwords(substr($row_city_name, 0, 10)) .@$row->city_id. '</a>
                    	<span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="'.ucfirst($row_state_name). ' | '.strtoupper($row_country_name).'">' . strtoupper(substr($row_state_name, 0, 5)) . ' | ' . strtoupper(substr($row_country_name, 0, 2)) . '</span>
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
                    	<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.ucfirst($row_city_name).'">' . ucwords(substr($row_city_name, 0, 10)) . '</a>
                    	<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucfirst($row_state_name).' | '.strtoupper($row_country_name).'">' . strtoupper(substr($row_state_name, 0, 7)) . ' | ' . strtoupper(substr($row_country_name, 0, 2)) . '</span>
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
			->editColumn('UploadDate', function ($row) {
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.Carbon::parse($row->created_at)->format('d M, Y').'</a>
                  </div>';
            })
			->editColumn('Requisitions', function ($row) {
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.ucwords($row->title).'">'.ucwords(substr($row->title, 0, 8)).'</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="'.ucwords($row->client_name).'">'.ucwords(substr($row->client_name, 0, 8)).'</span>
                  </div>';
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
							<a href="javascript.void(0);" data-row-id="'.@$row->id.'" class="d-flex flex-column stage_sub_stage stageupdate" data-toggle="modal" data-target="#stage_sub_stage" onClick="updateApplicantStage('.@$row->id.')">
								<span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1" data-toggle="tooltip" title="' . $row->name . '">' . ucwords(substr($row->name, 0, 8)) . '</span>
								<span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' . @$name . '">' . substr(@$name, 0, 8) . '</span>
							</a>
						</div>';
			})
            ->rawColumns(['chkbox', 'Name', 'Contacts', 'Exp', 'Salary', 'NoticePeriod', 'Qualifaction', 'Location', 'Source', 'UploadDate', 'Requisitions', 'Stage'])
            ->addIndexColumn()
            ->make(true);   
    }
}