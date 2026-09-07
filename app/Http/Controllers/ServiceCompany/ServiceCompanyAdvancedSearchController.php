<?php  
/*** ########################################################
Controller    	: 	AdminCVTrackerController
Purpose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/


namespace App\Http\Controllers\ServiceCompany;

use App\ApplicationStatus;
use App\Helper\Files;
use App\Helper\Reply;
use App\Helper\GeneralMethod;
use Illuminate\Support\Facades\Gate;
use App\Models\InterviewSchedule;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\InterviewProcess;
use Illuminate\Support\Facades\DB;
use App\Exports\CvTrackerExport;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;

class ServiceCompanyAdvancedSearchController extends ServiceCompanyBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('Advance Search');
        $this->pageIcon = 'icon-user';
		
    }

/*** ########################################################
Controller    	: 	AdminAdvancedSearchController
Method			:	index
Purpose			:	Load all application 
Date			:	12-April-2021	
########################################################***/
	public function index()
    {
		
			$routeArray 					=	app('request')->route()->getAction();
			$controllerAction 				=	class_basename($routeArray['controller']);
			list($controller, $action) 		=	explode('@', $controllerAction);
			$this->currentControllerName  	=	$controller;
			
			$common_search					=	@$_REQUEST['common_search'];			
			return view('service_company.advsearch.index', $this->data);    
	
	}


/*** ########################################################
Controller    	: 	AdminAdvancedSearchController
Method			:	data
Purpose			:	Return json data for cv tracker
Date			:	12-April-2021	
########################################################***/

    public function data(Request $request)
    {
		
        	abort_if(Gate::denies('view_job_applications'), 403);

			$chkAdminRole = $this->user->hasRole('admin'); 
		
		
        $jobApplications = JobApplication::select('job_applications.*', 'jobs.title', 'job_locations.location', 'application_status.status', 'clients.name as client_name', 'users.name as posted_by', 'master_status.name')
            ->join('jobs', 'jobs.id', 'job_applications.job_id')
            ->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')
			
			->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')		
            ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')			
			->leftjoin('clients', 'clients.id', 'jobs.client_id')			
			->leftjoin('users', 'users.id', 'job_applications.submitted_by')
			//->leftjoin('notice_periods', 'notice_periods.np_id', 'job_applications.np_id')			
            ->leftjoin('application_status', 'application_status.id', 'job_applications.status_id');
			
		
		if(!empty($request->common_search))
		{
			$jobApplications 	= $jobApplications
									->where('job_applications.full_name', 'like',  '%'.$request->common_search .'%')
									->orWhere('job_applications.email', 'like',  '%'.$request->common_search .'%')
									->orWhere('job_applications.secondrary_email', 'like',  '%'.$request->common_search .'%')
									->orWhere('job_applications.phone', 'like',  '%'.$request->common_search .'%')
									->orWhere('job_applications.secondary_mobile', 'like',  '%'.$request->common_search .'%');
			
			//$jobApplications 	= $jobApplications->where('job_applications.full_name', $request->common_search)->orWhere('job_applications.submitted_by', $this->user->id);		
		}
		else
		{
			$jobApplications 	= $jobApplications->where('job_applications.submitted_by', '=', $this->user->id);
		}
		
		
		
		
		
        $jobApplications 		= $jobApplications->groupBy('job_applications.id');
		$jobApplications 		= $jobApplications->orderBy('job_applications.id', 'DESC');
		
		
		
		
        return DataTables::of($jobApplications)
           
			->editColumn('created_at', function ($row) {
					$created_at = '';
					$created_at = GeneralMethod::ymdTodmy($row->created_at);
					return '<div class="text-center"><a href="#" data-toggle="tooltip" data-placement="right" title="'.$created_at.'&nbsp;'.'"><i class="fa fa-calendar text-custom fa-sm"></i></a></div>';	
            })

            
			->editColumn('full_name', function ($row) use($chkAdminRole) 
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
			})

			 ->editColumn('client_name', function ($row) {
                return ucfirst($row->client_name);
            })
			->editColumn('title', function ($row) {
                $out = strlen($row->title) > 30 ? substr($row->title,0,30)."..." : $row->title;
                return ucwords($out);
            })
			 ->editColumn('posted_by', function ($row) {
               return ucfirst($row->posted_by);
            })			
			
			->editColumn('email', function ($row) {
                return $row->email;
            })
			->editColumn('phone', function ($row) {
                return $row->phone;
            })
			->editColumn('ctc', function ($row) {
                return $row->ctc;
            })
			->editColumn('ectc', function ($row) {
                return $row->ectc;
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
		
		$np 			= $row->np_id;
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
		return ucfirst($np);
	})
						
	->editColumn('current_designation ', function ($row) {
	   		return ucfirst($row->current_designation);
	})
	
	->editColumn('current_company', function ($row) {
			return ucfirst($row->current_company);
	})
		
	->editColumn('current_location', function ($row) {
			return ucfirst($row->current_location );
	})
	
	->editColumn('qualification_name', function ($row) {
			return ucfirst($row->title);
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
				    
	->editColumn('title', function ($row) 
	{
			return ucfirst($row->title);
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
				$html 	=	'<p class="" id="resume-'.$row->id.'"><a  href="'. route('service_company.job-applications.download', $row->id) .'" class="btn"><i class="fa fa-file text-primary" aria-hidden="true"></i></a></p>'; 							
			}
			else
			{
				$html 	= 'N/A';
			}
			return $html; 		
	})

			 ->addColumn('action', function ($row) use($chkAdminRole) {
			
		
                $action = '';
				
				if($chkAdminRole > 0)
				{
					if ($this->user->cans('edit_job_applications')) {
                    $action .= '<a href="' . route('service_company.job-applications.edit', [$row->id]) . '" class="btn"
                      data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-edit text-primary" aria-hidden="true"></i></a>';
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
							$action .= '<a href="' . route('service_company.job-applications.edit', [$row->id]) . '" class="btn"
							data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-edit text-warning" aria-hidden="true"></i></a>';
						}
						
						if ($this->user->cans('delete_job_applications')) {
							$action .= ' <a href="javascript:;" class="btn sa-params"
							data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times text-danger" aria-hidden="true"></i></a>';
						}
					}
					else
					{
						$action = '---';	
					}
						
				}
				return $action;				
				
            })

            ->rawColumns(['action', 'created_at', 'resume', 'full_name', 'status', 'client_name', 'email', 'interview_process', 'resume', 'qualification_name', 'interview_status_level'])
            ->addIndexColumn()
            ->make(true);
	
	
	}
	


		
/*** ########################################################
Controller    	: 	AdminCVTrackerController
Method			:	loadVisibilityControl
Purpose			:	Display table schema
Date			:	05-April-2021	
########################################################***/	
	public function loadVisibilityControl($id)
    {
		

		return view('service_company.advsearch.load_visibility_control', $this->data)->render();
    }
	
/*** ########################################################
Controller    	: 	AdminCVTrackerController
Method			:	
Purpose			:	Display table schema
Date			:	05-April-2021	
########################################################***/	
	public function create_status(Request $request)
	 {
        abort_if(! $this->user->can('add_schedule'), 403);
		
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
        return view('service_company.advsearch.status', $this->data)->render();
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
        $view 				= view('service_company.cvtracker.job-question', $this->data)->render();
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
        abort_if(Gate::denies('edit_job_applications'), 403);

        $this->statuses 		= ApplicationStatus::all();
        $this->application 		= JobApplication::withoutGlobalScopes()->find($id);
        $this->jobQuestion 		= JobQuestion::with(['question'])
            ->where('job_id', $this->application->job_id)->get();

        return view('service_company.advsearch.edit', $this->data);
    }



    public function createSchedule(Request $request, $id)
    {
        abort_if(Gate::denies('add_schedule'), 403);
        $this->candidates = JobApplication::all();
        $this->users = User::company()->get();
        $this->scheduleDate = $request->date;
        $this->currentApplicant = JobApplication::withoutGlobalScopes()->findOrFail($id);
        return view('service_company.advsearch.interview-create', $this->data)->render();
    }

    public function storeSchedule(StoreRequest $request)
    {
        abort_if(Gate::denies('add_schedule'), 403);

        $dateTime =  $request->scheduleDate . ' ' . $request->scheduleTime;
        $dateTime = Carbon::createFromFormat('Y-m-d H:i', $dateTime);

        $interviewSchedule = new InterviewSchedule();
        $interviewSchedule->job_application_id = $request->candidate;
        $interviewSchedule->schedule_date = $dateTime;
        $interviewSchedule->save();

        $interviweColumn = ApplicationStatus::where('status', 'interview')->first();
        $jobApplication = JobApplication::withoutGlobalScopes()->find($request->candidate);
        $jobApplication->status_id = $interviweColumn->id;
        $jobApplication->save();

        if (!empty($request->employee)) {
            InterviewScheduleEmployee::where('interview_schedule_id', $interviewSchedule->id)->delete();
            foreach ($request->employee as $employee) {
                    $scheduleEmployee = new InterviewScheduleEmployee();
                    $scheduleEmployee->user_id = $employee;
                    $scheduleEmployee->interview_schedule_id = $interviewSchedule->id;
                    $scheduleEmployee->save();

                    $user = User::withoutGlobalScopes()->find($employee);
                    // Mail to employee for inform interview schedule
                    Notification::send($user, new ScheduleInterview($jobApplication));
                }
        }

        Notification::send($jobApplication, new CandidateScheduleInterview($jobApplication, $interviewSchedule));
        return Reply::redirect(route('service_company.interview-schedule.index'), __('menu.interviewSchedule') . ' ' . __('messages.createdSuccessfully'));
    }

    public function store(StoreJobApplication $request)
    {
        abort_if(Gate::denies('add_job_applications'), 403);
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

        return Reply::redirect(route('service_company.advsearch.index'), __('menu.jobApplications') . ' ' . __('messages.createdSuccessfully'));
    }

    public function update(UpdateJobApplication $request, $id)
    {
        abort_if(Gate::denies('edit_job_applications'), 403);

        $jobApplication = JobApplication::withoutGlobalScopes()->find($id);
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

        return Reply::redirect(route('service_company.advsearch.index'), __('menu.jobApplications') . ' ' . __('messages.updatedSuccessfully'));
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('delete_job_applications'), 403);

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

                    $task = JobApplication::withoutGlobalScopes()->find($taskId);
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
        abort_if(Gate::denies('view_job_applications'), 403);
        $this->boardColumns = ApplicationStatus::all();
        $this->locations = JobLocation::all();
        $this->jobs = Job::all();
        $this->skills = Skill::all();
        return view('service_company.advsearch.index', $this->data);
    }

    public function ratingSave(Request $request, $id)
    {
        abort_if(Gate::deniess('edit_job_applications'), 403);

        $application = JobApplication::withoutGlobalScopes()->findOrFail($id);
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

        $application = JobApplication::withoutGlobalScopes()->findOrFail($request->id);
        $application->is_viewed = 1;
        $application->save();

        return Reply::dataOnly([
            'status' => 'success',
            'email' => $application->email,
            'phone' => $application->phone,
            'resume' => route('service_company.advsearch.download', $application->id)
        ]);
    }

    public function download($id)
    {
        $jobApplication = JobApplication::withoutGlobalScopes()->findOrFail($id);
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
        $this->application = JobApplication::with(['schedule', 'status', 'schedule.employee', 'schedule.comments.user'])->withoutGlobalScopes()->find($id);

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
										
        $view = view('service_company.cvtracker.interviewprocess', $this->data)->render();
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
					
			return Reply::redirect(route('service_company.advsearch.index'), __('menu.jobApplications') . ' ' . __('messages.updatedSuccessfully'));
	
	}

	public function sehedulecreate(Request $request, $id, $process_id)
	{
	 	
		$this->process_id	= $process_id;	
	 	$this->candidate 	= JobApplication::withoutGlobalScopes()->find($id);
		
        $this->users 		= User::company()->get();
        $this->scheduleDate = $request->date;
        return view('service_company.advsearch.sehedulecreate', $this->data)->render();
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
        abort_if(Gate::denies('edit_job_applications'), 403);

        $jobApplication = JobApplication::withoutGlobalScopes()->find($id);
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

        return Reply::redirect(route('service_company.advsearch.index'), __('menu.jobApplications') . ' ' . __('messages.updatedSuccessfully'));
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
						
						$masterStatusRow	=	DB:: table('master_status')->select('master_status.name')->where('id', '=', $masterStatus)->first(); 
						$masterStatusName	=	$masterStatusRow->name;
						$statusHtml 		=   @$masterStatusName;
					}
					// GET CHILD STATUS NAME 
					if(@$childStatus > 0 && $masterStatus > 0)
					{
						
						$childStatusRow  =  DB::table('candidate_status')
											->select('candidate_status.name')
											->where('candidate_status.company_id', '=', $this->user->company_id)
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
			
			
			//return Reply::redirect(route('service_company.interview-schedule.index'), __('menu.interviewSchedule').' '.__('messages.createdSuccessfully'));
	}
		
	public function resume($id)
    {
        $this->application = JobApplication::findOrFail($id);
		
		$view = view('service_company.advsearch.resume', $this->data)->render();
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

        return Excel::download(new CvTrackerExport($filters, $data), 'advsearch.xlsx', ExcelExcel::XLSX);
    }
		
	public function create()
    {
        abort_if(Gate::denies('add_job_applications'), 403);

        $candidateCount = JobApplication::count();
            if($candidateCount > 0 && $this->activePackage->package->no_of_candidate_access !== null && $this->activePackage->package->no_of_candidate_access <= $candidateCount) {
            $this->message = __('modules.subscription.candidatePurchase');
            return view('service_company.subscription.ask_purchase', $this->data);
        }
        $this->jobs = Job::activeJobs();
        return view('service_company.advsearch.create', $this->data);
    }

}
