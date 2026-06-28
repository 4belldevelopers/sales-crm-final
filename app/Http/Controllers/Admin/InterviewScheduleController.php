<?php
/***########################################################
Controller    	: 	interviewScheduleController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################** */

namespace App\Http\Controllers\Admin;
use App\Models\ApplicationStatus;
use App\Helper\Reply;
use App\Helper\GeneralMethod;
use App\Http\Requests\InterviewSchedule\StoreRequest;
use App\Http\Requests\InterviewSchedule\UpdateRequest;
use App\Models\InterviewSchedule;
use App\Models\InterviewScheduleEmployee;
use App\Models\JobApplication;
use App\Notifications\CandidateNotify;
use App\Notifications\CandidateReminder;
use App\Notifications\CandidateScheduleInterview;
use App\Notifications\EmployeeResponse;
use App\Notifications\ScheduleInterview;
use App\Notifications\ScheduleInterviewStatus;
use App\Notifications\ScheduleStatusCandidate;
use App\Models\ScheduleComments;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;
use App\Notifications\CandidateRejected;
use Illuminate\Support\Arr;
use App\Exports\InterviewScheduleExport;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;

class InterviewScheduleController extends AdminBaseController
{

    public function __construct()
    	{

        parent::__construct();

        $this->pageTitle = 'menu.interviewSchedule';

        $this->pageIcon = 'icon-calender';

    }
	 
	 public function index(Request $request)
    	{

        abort_if(! $this->user->cans('view_schedule'), 403);

        $currentDate = Carbon::now()->format('Y-m-d'); // Current Date

        // Get All schedules

        $this->schedules = InterviewSchedule::select('interview_schedules.*', 'jobs.title', 'job_applications.full_name' )
            ->with(['employee','employee.user'])
            ->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
            ->join('jobs', 'jobs.id', 'job_applications.job_id');
       		$this->schedules	=   $this->schedules->where('interview_schedules.status', 'pending');

			if($this->user->role->role_id != 10)
			{	
				 $this->schedules = $this->schedules->where('job_applications.submitted_by', $this->user->id);	
			}

           $this->schedules= $this->schedules->get();

        // Filter upcoming schedule
        $upComingSchedules = $this->schedules->filter(function ($value, $key)use($currentDate) {
            return $value->schedule_date >= $currentDate;
        });

        $upcomingData = [];



        // Set array for upcoming schedule

        foreach($upComingSchedules as $upComingSchedule){

            $dt = $upComingSchedule->schedule_date->format('Y-m-d');

            $upcomingData[$dt][] = $upComingSchedule;

        }



        $this->upComingSchedules = $upcomingData;



        if($request->ajax()){

            $viewData = view('admin.interview-schedule.upcoming-schedule', $this->data)->render();

            return Reply::dataOnly(['data' => $viewData, 'scheduleData' => $this->schedules]);

        }



        return view('admin.interview-schedule.index', $this->data);

    }

    public function index_original(Request $request)
    	{

        abort_if(! $this->user->cans('view_schedule'), 403);



        $currentDate = Carbon::now($this->global->timezone)->format('Y-m-d'); // Current Date



        // Get All schedules

        $this->schedules = InterviewSchedule::

            select('id', 'job_application_id', 'schedule_date', 'status')

            ->with(['employees', 'jobApplication:id,job_id,full_name', 'jobApplication.job:id,title'])

            ->where('status', 'pending')

            ->orderBy('schedule_date')

            ->get();



        // Filter upcoming schedule

        $upComingSchedules = $this->schedules->filter(function ($value, $key)use($currentDate) {

            return $value->schedule_date >= $currentDate;

        });



        $upcomingData = [];



        // Set array for upcoming schedule

        foreach($upComingSchedules as $upComingSchedule){

            $dt = $upComingSchedule->schedule_date->format('Y-m-d');

            $upcomingData[$dt][] = $upComingSchedule;

        }



        $this->upComingSchedules = $upcomingData;



        if($request->ajax()){

            $viewData = view('admin.interview-schedule.upcoming-schedule', $this->data)->render();

            return Reply::dataOnly(['data' => $viewData, 'scheduleData' => $this->schedules]);

        }



        return view('admin.interview-schedule.index', $this->data);

    }

    public function create(Request $request)
		{

        abort_if(! $this->user->cans('add_schedule'), 403);

        $this->candidates = JobApplication::all();

        $this->users = User::with('company')->company()->get();

        $this->scheduleDate = $request->date;

        return view('admin.interview-schedule.create', $this->data)->render();

    }   

	public function table(Request $request)
		{
		
			//echo "call"; exit; 
			/* abort_if(! $this->user->cans('add_schedule'), 403);
			$this->candidates = JobApplication::all();
			$this->users = User::with('company')->company()->get();
			return view('admin.interview-schedule.table', $this->data);*/		

		 if($this->user->role->role_id != 10)
		 {	

		 	$this->candidates 	= JobApplication::where('submitted_by', '=', $this->user->id)->get();

			//$categories = Job::where('id', '>', '0')->take(1);;	

		 }

		 else

		 {

			$this->candidates 	= JobApplication::all();

		 }

		 

       //$this->users 		= User::company()->get();

		

        return view('admin.interview-schedule.table', $this->data);

    }	 	

	public function data_original(Request $request)
		{

	  

	  	

        abort_if(! $this->user->cans('view_schedule'), 403);



		$available_status		=	'';

		$available_status		=	GeneralMethod::available_status();

		

		$feedback_status		=	'';

		$feedback_status		=	GeneralMethod::feedback_status();

		

		$interview_mode_arr		=	'';

		$interview_mode_arr		=	GeneralMethod::interview_mode();

		

		

        $shedule = InterviewSchedule::select(

			'interview_schedules.id','job_applications.full_name','interview_schedules.status', 'interview_schedules.schedule_date','interview_schedules.available_status',

			'interview_schedules.reschedule', 'interview_schedules.feedback', 'interview_schedules.interview_mode', 'interview_schedules.interviewers', 

			 'interview_schedules.interview_type', 'interview_schedules.schedule_from_time',

			'users.name as source',

			'interview_schedules.job_application_id',

			'master_status.name as master_status_name', 'candidate_status.name as child_status_name', 

			'jobs.title as job_title',

			'jobs.id as job_id',

			'companies.company_name'

		 )

            ->leftjoin('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')

			->leftjoin('users', 'users.id', 'job_applications.submitted_by')

			->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')

			->leftjoin('companies', 'companies.id', 'jobs.company_id')

			->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')

			->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage');

			

        // Filter by status

        if($request->status != 'all' && $request->status != ''){

            $shedule = $shedule->where('interview_schedules.status', $request->status);

        }



        // Filter By candidate

        if($request->applicationID != 'all' && $request->applicationID != ''){

            $shedule = $shedule->where('job_applications.id', $request->applicationID);

        }



        // Filter by StartDate

        if($request->startDate !== null && $request->startDate != 'null'){

            $shedule = $shedule->where(DB::raw('DATE(interview_schedules.`schedule_date`)'), '>=', "$request->startDate");

        }



        // Filter by EndDate

        if($request->endDate !== null && $request->endDate != 'null'){

            $shedule = $shedule->where(DB::raw('DATE(interview_schedules.`schedule_date`)'), '<=', "$request->endDate");

        }

		

		

		//$shedule->orderBy("interview_schedules.id", "DESC");

		$shedule->orderBy('interview_schedules.id', 'DESC');

		$shedule->get();



        return DataTables::of($shedule)

            ->addColumn('action', function ($row) {

                $action = '';





                // if( $this->user->can('view_schedule')){

                //     $action.= '<a href="javascript:;" data-row-id="' . $row->id . '" class="btn view-data"

                //       data-toggle="tooltip" data-original-title="'.__('app.view').'"><i class="fa fa-search text-warning" aria-hidden="true"></i></a>';

                // }

               /* if( $this->user->can('edit_schedule')){

                    $action.= '<a href="javascript:;" style="margin-left:4px" data-row-id="' . $row->id . '" class="btn edit-data"

                      data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa flaticon2-pen text-primary" aria-hidden="true"></i></a>';

                }*/



                if( $this->user->cans('delete_schedule')){

                    $action.= ' <a href="javascript:;" class="btn sa-params"

                      data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times text-danger" aria-hidden="true"></i></a>';

                }

                return $action;

            })

			

            ->editColumn('full_name', function ($row) {				

				$name	=	ucwords(substr($row->full_name, 0, 10));	

				$fname	=	'<a href="javascript:void(0);" class="show-detail" data-widget="control-sidebar" data-slide="true" data-row-id="'.@$row->job_application_id.'">'.$name.'</a>'; 

                return $fname;

            })

			

            ->editColumn('schedule_date', function ($row) {

                return Carbon::parse($row->schedule_date)->format('d M, Y H:i a');

            })

			

			->editColumn('job_title', function ($row) {

                return substr(ucwords($row->job_title), 0, 10);

            })

			->editColumn('company_name', function ($row) {

                return ucwords($row->company_name);

            })

			->editColumn('interview_mode', function ($row)  use ($interview_mode_arr) {

				$interview_mode	=	'N/A';

				if($row->interview_mode > 0){

					$interview_mode	=	$interview_mode_arr[1];			

				}

                return $interview_mode;

            })

			

			

			

			->editColumn('master_status_name', function ($row) {

                return substr($row->master_status_name, 0, 10);

            })

			->editColumn('child_status_name', function ($row) {

			

				$child_status	=	'N/A';

				if(!empty($row->child_status_name)){

					$child_status	= substr($row->child_status_name, 0, 10);

				}

                return $child_status;

            })

			

			

			->editColumn('source', function ($row) {

                return substr(ucwords($row->source), 0, 10);

            })

			

			->editColumn('available_status', function ($row)  use ($available_status) {

               

			    $selClassname	=	'';

				$option			=	'';

				$i				=	0;

				if(!empty($available_status))

				{

					foreach($available_status as $key=>$val)

					{

						$sel	=	'';

						if($row->available_status > 0)

						{

							if($row->available_status == $i ){

								$sel			=	'selected="selected"';	

								$selClassname	=	$val['classname'];

							}	

						}

						$option 		.= '<option style="background-color:#FFFFFF; color:#000000" value="'.$i.'"   '.$sel.'>'.$val['name'].'</option>'; 

						$i++;

					}

				}

				

				$action 		 = '';

				$action 		.= '<select  name="available_status" data-application="'.$row->job_application_id.'", data-interview_id="'.$row->id.'" class="updateAvailableStatus '.$selClassname.'" >';

				$action 		.= $option;

				$action 		.= '</select>'; 

				return $action;





            })

			

			->editColumn('feedback_status', function ($row)  use ($feedback_status) {

               

				/*$selClassname	=	'';

				$option			=	'';

				if(!empty($feedback_status))

				{

					foreach($feedback_status as $key=>$val)

					{

						$sel	=	'';

						if($row->feedback == $key ){

							$sel			=	'selected="selected"';	

							$selClassname	=	$val['classname'];

						}

						$option 		.= '<option value="'.$key.'"   '.$sel.'>'.$val.'</option>';

					}

				}

				

				$action 		 = '';

				$action 		.= '<select  name="feedback_status" onchange="updateFeedbackStatus(this.value, '.$row->id.', '.$row->job_application_id.');" >';

				$action 		.= $option;

				$action 		.= '</select>'; 

				return $action;*/



				$selClassname	=	'';

				$option			=	'';

				$i				=	0;

				if(!empty($feedback_status))

				{

					foreach($feedback_status as $key=>$val)

					{

						$sel	=	'';

						if($row->feedback > 0)

						{

							if($row->feedback == $i ){

								$sel			=	'selected="selected"';	

								$selClassname	=	$val['classname'];

							}	

						}

						$interview_type		=	$row->interview_type;

						if($interview_type ==2 && $i == 4){

									$redate = Carbon::parse($row->reschedule)->format('d M, Y').'-'.$row->schedule_from_time;

									$option 		.= '<option style="background-color:#FFFFFF; color:#000000" value="'.$i.'"   '.$sel.'>RE-'.$redate.'</option>'; 	

						}

						else{

							$option 		.= '<option style="background-color:#FFFFFF; color:#000000" value="'.$i.'"   '.$sel.'>'.$val['name'].'</option>'; 	

						}

						

						$i++;

					}

				}				

				$action 		 = '';

				$action 		.= '<select style="width:-webkit-fill-available;width: -moz-available;"  name="available_status" data-application="'.$row->job_application_id.'", data-interview_id="'.$row->id.'" class="updateFeedbackStatus '.$selClassname.'" >';

				//$action 		.= '<select  name="feedback_status" onchange="updateFeedbackStatus(this.value, '.$row->id.', '.$row->job_application_id.');" class="updateAvailableStatus '.$selClassname.'"  >';

				$action 		.= $option;

				$action 		.= '</select>'; 

				return $action;













            })

			

			

->editColumn('interviewer', function ($row) {

               

			 /*   $html 		 		= '<div class="interviewers text-center"> 

              <span class="total-like-user-main" dir="ltl"> ';

			   	$interviewers		=	$row->interviewers;

				if(!empty($interviewers))

				{

					 $record 	= DB::table('users')->select('name')->whereIn('id', explode(",", $interviewers))->get();

					 $i=0;

					 foreach($record as $rec){

					 	

						if($i==0){

						$html .= '<a data-toggle="tooltip" data-placement="top" title="" href="Javascript:void(0);" data-original-title="'.@$rec->name.'"><span class="rounded-pill inside-img1">'.substr(strtoupper(@$rec->name), 0, 2).'<i class="fa fa-plus" aria-hidden="true"></i></span></a>'; 

						}

                        else{

						$html .= '<a data-toggle="tooltip" data-placement="top" title="" href="Javascript:void(0);" data-original-title="'.@$rec->name.'"><img alt="Generic placeholder image" src="http://bootdey.com/img/Content/avatar/avatar4.png" class="total-like-user rounded-pill"></a>'; 

						}

						$i++;

					 }

				}

				else

				{

					

					$html .= '<a data-toggle="tooltip" data-placement="top" title="" href="Javascript:void(0);" data-original-title="No Record Found" class="total-like-user rounded-pill"><span class="rounded-pill inside-img">N/A</span></a>'; 

			  	

				}								

				$html .= '</span></div>'; 

				return $html;*/

				$cnt 			=	1; 

				$interviewers	=	$row->interviewers;

				$record 		=	DB::table('users')->select('name')->whereIn('id', explode(",", $interviewers))->get();

				$totalrecord 	=	DB::table('users')->select('name')->whereIn('id', explode(",", $interviewers))->count();

				

				$contactHtml	='';

				foreach($record as $rec)

				{

					$fname		= strtoupper(substr($rec->name, 0, 2));

					if($totalrecord <=1 && $cnt==1){

						$contactHtml	.=	'<div class="text-center tooltip">'.$fname.'&nbsp;<span class="tooltiptext">';	

					}

					else if($cnt ==1){

						$contactHtml	.=	'<div class="text-center tooltip">'.$fname.'&nbsp;<i class="fa fa-plus fa-1x text-dark" aria-hidden="true"></i><span class="tooltiptext">';			

					}

					

					

					

					

					

						$contactHtml 			.= '

														Name : '.$rec->name.'<br>

														

													';

					

					

					

					$cnt++;								

			

				}

				$contactHtml	.=	'</span></div><br>';

				return $contactHtml;

								





})

			

			

            ->editColumn('status', function ($row) {

                if($row->status == 'pending'){

                    return '<label class="badge bg-warning text-white">'.__('app.pending').'</label>';

                }

                if($row->status == 'hired'){

                    return '<label class="badge bg-success text-white">'.__('app.hired').'</label>';

                }

                if($row->status == 'canceled'){

                    return '<label class="badge bg-danger text-white">'.__('app.canceled').'</label>';

                }

                if($row->status == 'rejected'){

                    return '<label class="badge bg-danger text-white">'.__('app.rejected').'</label>';

                }

            })

			

            ->rawColumns(['action', 'status', 'full_name', 'available_status', 'feedback_status', 'interviewer'])

            ->make(true);

    

	

	}	
  
    public function edit($id)
		{

        abort_if(! $this->user->cans('edit_schedule'), 403);

        

        $this->candidates = JobApplication::all();

        $this->users = User::with('company')->company()->get();

        $this->schedule = InterviewSchedule::with(['jobApplication', 'user'])->find($id);

        $this->comment = ScheduleComments::where('interview_schedule_id', $this->schedule->id)

                                            ->where('user_id', $this->user->id)->first();

        $this->employeeList = json_encode($this->schedule->employee->pluck('user_id')->toArray());



        return view('admin.interview-schedule.edit', $this->data)->render();

    }

    public function store(StoreRequest $request)
		{

        abort_if(! $this->user->cans('add_schedule'), 403);



        $dateTime =  $request->scheduleDate.' '.$request->scheduleTime;

        $dateTime = Carbon::createFromFormat('Y-m-d H:i', $dateTime);



        foreach ($request->candidates as $candidateId) {

            // store Schedule

            $interviewSchedule = new InterviewSchedule();

            $interviewSchedule->job_application_id = $candidateId;

            $interviewSchedule->schedule_date = $dateTime;

            $interviewSchedule->save();



            $jobApplicationStatus = ApplicationStatus::where('status','interview')->first();

            // Update Schedule Status

            $jobApplication = $interviewSchedule->jobApplication;

            $jobApplication->status_id = $jobApplicationStatus->id;

            $jobApplication->save();



            if($request->comment){

                $scheduleComment = [

                    'interview_schedule_id' => $interviewSchedule->id,

                    'user_id' => $this->user->id,

                    'comment' => $request->comment

                ];



                $interviewSchedule->comments()->create($scheduleComment);

            }



            if(!empty($request->employees)){

                $interviewSchedule->employees()->attach($request->employees, ['company_id' => $this->user->company_id]);



                // Mail to employee for inform interview schedule

                Notification::send($interviewSchedule->employees, new ScheduleInterview($jobApplication));

            }



            // mail to candidate for inform interview schedule

            Notification::send($jobApplication, new CandidateScheduleInterview($jobApplication, $interviewSchedule));

        }



        return Reply::redirect(route('admin.interview-schedule.index'), __('menu.interviewSchedule').' '.__('messages.createdSuccessfully'));

    }
	
    public function changeStatus(Request $request)
		{

        abort_if(! $this->user->cans('add_schedule'), 403);



        $this->commonChangeStatusFunction($request->id, $request);



        return Reply::success(__('messages.interviewScheduleStatus'));

    }

    public function update(UpdateRequest $request, $id)
		{

        abort_if(! $this->user->cans('add_schedule'), 403);



        $dateTime =  $request->scheduleDate.' '.$request->scheduleTime;

        $dateTime = Carbon::createFromFormat('Y-m-d H:i', $dateTime);



        // Update interview Schedule

        $interviewSchedule = InterviewSchedule::select('id', 'job_application_id', 'schedule_date', 'status')

                            ->with([

                                'jobApplication:id,full_name,email,job_id,status_id',

                                'employees',

                                'comments'

                            ])

                            ->where('id', $id)->first();

        $interviewSchedule->schedule_date = $dateTime;

        $interviewSchedule->save();



        if($request->comment){

            $scheduleComment = [

                'comment' => $request->comment

            ];



            $interviewSchedule->comments()->updateOrCreate([

                'interview_schedule_id' => $interviewSchedule->id,

                'user_id' => $this->user->id

            ], $scheduleComment);

        }



        $jobApplication = $interviewSchedule->jobApplication;



        if(!empty($request->employee)){

            $employees = [];



            foreach ($request->employee as $employee) {

                $employees = Arr::add($employees, $employee, ['company_id' => $this->user->company_id]);

            }

            

            $interviewSchedule->employees()->sync($employees);



            // Mail to employee for inform interview schedule

            Notification::send($interviewSchedule->employees, new ScheduleInterview($jobApplication));

        }



        return Reply::redirect(route('admin.interview-schedule.index'), __('menu.interviewSchedule').' '.__('messages.updatedSuccessfully'));

    }

    public function destroy($id)
    	{

        abort_if(! $this->user->cans('delete_schedule'), 403);



        InterviewSchedule::destroy($id);

        return Reply::success(__('messages.recordDeleted'));

    }

    public function show(Request $request, $id)
    	{

        abort_if(! $this->user->cans('view_schedule'), 403);

        $this->schedule = InterviewSchedule::with(['jobApplication', 'user'])->find($id);

        $this->currentDateTimestamp = Carbon::now(company()->timezone)->timestamp;

        $this->tableData = null;



        if($request->has('table')){

            $this->tableData = 'yes';

        }



        return view('admin.interview-schedule.show', $this->data)->render();

    }

    // notify and reminder to candidate on interview schedule
    public function notify($id, $type)
    	{



        $jobApplication = JobApplication::find($id);



        if ($type == 'notify') {

            if ($jobApplication->status->status == 'hired') {

                // mail to candidate for hiring notify

                Notification::send($jobApplication, new CandidateNotify());

                return Reply::success(__('messages.notificationForHire'));

            }

            

            if ($jobApplication->status->status == 'rejected') {

                // mail to candidate for hiring notify

                Notification::send($jobApplication, new CandidateRejected());

                return Reply::success(__('messages.notificationForReject'));

            }

            

        } else {

            // mail to candidate for interview reminder

            Notification::send($jobApplication, new CandidateReminder( $jobApplication->schedule));

            return Reply::success(__('messages.notificationForReminder'));

        }



    }

    // Employee response on interview schedule
    public function employeeResponse($id, $res)
		{



        $scheduleEmployee = InterviewScheduleEmployee::find($id);

        $users = User::allAdmins(); // Get All admins for mail

        $type = 'refused';



        if($res == 'accept'){  $type = 'accepted'; }



        $scheduleEmployee->user_accept_status = $res;



        // mail to admin for employee response on refuse or accept

        Notification::send($users, new EmployeeResponse($scheduleEmployee->schedule, $type, $this->user));



        $scheduleEmployee->save();



        return Reply::success(__('messages.responseAppliedSuccess'));



    }

    public function changeStatusMultiple(Request $request)
		{

        abort_if(! $this->user->cans('edit_schedule'), 403);

        foreach($request->id as $ids)

        {

            $this->commonChangeStatusFunction($ids, $request);

        }



        return Reply::success(__('messages.interviewScheduleStatus'));

    }

    public function commonChangeStatusFunction($id, $request)
    	{

        // store Schedule

        $interviewSchedule = InterviewSchedule::select('id', 'job_application_id', 'status')

                            ->with([

                                'jobApplication:id,full_name,email,job_id,status_id',

                                'employees'

                            ])

                            ->where('id', $id)->first();

        $interviewSchedule->status = $request->status;

        $interviewSchedule->save();



        $application = $interviewSchedule->jobApplication;

        $status = ApplicationStatus::select('id', 'status');



        if(in_array($request->status, ['rejected', 'canceled'])){

            $applicationStatus = $status->status('rejected');

        }

        if($request->status === 'hired'){

            $applicationStatus = $status->status('hired');

        }

        if ($request->status === 'pending') {

            $applicationStatus = $status->status('interview');

        }



        $application->status_id = $applicationStatus->id;

        

        $application->save();



        $employees = $interviewSchedule->employees;

        $admins = User::allAdmins();



        $users = $employees->merge($admins);



        if($users){

            // Mail to employee for inform interview schedule

            Notification::send($users, new ScheduleInterviewStatus($application));

        }



        if($request->mailToCandidate ==  'yes'){

            // mail to candidate for inform interview schedule status

            Notification::send($application, new ScheduleStatusCandidate($application, $interviewSchedule));

        }



        return;

    }
	
	public	function updateAvailableStatus(Request $request)
		{

		

		$interview_id =	$job_application_id =	$status_id	=  '';

		

		$interview_id 				= $request->interview_id;

		$job_application_id 		= $request->job_application_id;

		$status_id 					= $request->status_id;

		

		$returnValue = DB::table('interview_schedules')

		->where('id', $interview_id)

		->where('job_application_id', @$job_application_id)

		//->where('skill_id', @$request->schemaname)

		->limit(1) 

		->update(array('available_status' => @$status_id)); 

		

		//$returnValue = 0;

		

		

// WORKING WITH TIMELINE .............................................................................................................................................

		

		$application 		= 	JobApplication::find($job_application_id);

		$date				= 	date('Y-m-d H:i:s');

		$insert_type		= 	3;

		$statusRow 			=	ApplicationStatus::findOrFail($application->master_status_id);

		$values 			=   array(

												'owner_id' 		 			=> auth()->user()->id,

												'application_id' 			=> $application->id, 

												'application_email' 		=> $application->email,

												'job_id' 					=> $application->job_id,

												'process_id'  				=> $application->master_status_id,

												

												'status_name'  				=> $statusRow->name,

											//	'process_child_name'	=> $status_name,

												'note'			 			=> 'Interview Status Update',

												'insert_type'				=> $insert_type,

												'create_at'					=> $date, 

												

												'update_at'					=> $date,

												//'interview_schedule_date'				=> $dateTime,

												//'interview_from_time'					=> $request->schedule_from_time,

												//'interview_to_time'						=> $request->schedule_to_time,

												//'interview_mode'						=> $request->interview_mode,

												

												//'interview_mention_link'				=> $request->url_link,

												//'interviewers'					=>	$date,

												//'interview_additional_email'			=>	$request->other_email,

												//'interview_message'						=>	$request->message

												

												

												

								  );

		if($application->child_status_id > 0)

		{

			$childStatus 					=	'';

			$childStatus 					=	DB::table('candidate_status')->select('*')->where('company_id', '=', $this->user->company_id)->where('id', $application->child_status_id)->first(); 

			$values['process_child_name'] 	=	@$childStatus->name; 	

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

		

		

		

		

		if($returnValue)

		{

			return Reply::success(__('Interview schedule status update successfully.'));	

		}

		else

		{

			return 0;	

		}

				

		

	

	}

	function updateFeedbackStatus(Request $request)
		{

		$feedback_status		=	'';

		$feedback_status		=	GeneralMethod::feedback_status();

		

		$interview_id =	$job_application_id =	$status_id	=  '';

		

		$interview_id 				= $request->interview_id;

		$job_application_id 		= $request->job_application_id;

		$status_id 					= $request->status_id;

		

		$returnValue = DB::table('interview_schedules')

		->where('id', $interview_id)

		->where('job_application_id', @$job_application_id)

		//->where('skill_id', @$request->schemaname)

		->limit(1) 

		->update(array('feedback' => @$status_id)); 

		

		//$returnValue = 0;

		

		

		

// WORKING WITH TIMELINE .............................................................................................................................................

		$feedback_status_name	=	'';

		if($status_id > 0)

		{

			$feedbackArr 			= $feedback_status[$status_id];

			$feedback_status_name	= $feedbackArr['name'];	

		}

		

		$application 		= 	JobApplication::find($job_application_id);

		$date				= 	date('Y-m-d H:i:s');

		$insert_type		= 	4;

		$statusRow 			=	ApplicationStatus::findOrFail($application->master_status_id);

		$values 			=   array(

												'owner_id' 		 			=> auth()->user()->id,

												'application_id' 			=> $application->id, 

												'application_email' 		=> $application->email,

												'job_id' 					=> $application->job_id,

												'process_id'  				=> $application->master_status_id,

												

												'status_name'  				=> $statusRow->name,

											//	'process_child_name'	=> $status_name,

												'note'			 			=> 'Feedback status update',

												'insert_type'				=> $insert_type,

												'create_at'					=> $date, 

												

												'update_at'					=> $date,

												//'interview_schedule_date'				=> $dateTime,

												//'interview_from_time'					=> $request->schedule_from_time,

												//'interview_to_time'						=> $request->schedule_to_time,

												//'interview_mode'						=> $request->interview_mode,

												

												//'interview_mention_link'				=> $request->url_link,

												//'interviewers'					=>	$date,

												//'interview_additional_email'			=>	$request->other_email,

												'interview_message'			=>	$feedback_status_name

												

												

												

								  );

		if($application->child_status_id > 0)

		{

			$childStatus 					=	'';

			$childStatus 					=	DB::table('candidate_status')->select('*')->where('company_id', '=', $this->user->company_id)->where('id', $application->child_status_id)->first(); 

			$values['process_child_name'] 	=	@$childStatus->name; 	

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





		if($returnValue)

		{

			return Reply::success(__('Feedback status update successfully.'));	

		}

		else

		{

			return 0;	

		}

				

		

	}
	
	 public function create_feedbackfrm(Request $request)
	 	{

         //abort_if(! $this->user->can('add_schedule'), 403);

		

	   // $this->feedback_status_arr	=	GeneralMethod::feedback_status();



		$this->application_id		=	$this->interview_id		= $this->feedback_id	=	'';

		$this->application_id		=	$_REQUEST['application_id'];

		$this->interview_id			=	$_REQUEST['interview_id'];

		$this->feedback_id			=	$_REQUEST['feedback_id'];

        

		$this->application 				= JobApplication::select('job_applications.*', 'clients.name as client_name', 'jobs.title as job_title', 'client_location.city', 'jobs.company_id')

											->join('jobs', 'jobs.id', '=', 'job_applications.job_id')

											->join('clients', 'clients.id', '=', 'jobs.client_id')

											->leftjoin('client_location_jobs', 'client_location_jobs.job_id', 'jobs.id')

											->leftjoin('client_location', 'client_location.id', 'client_location_jobs.client_location_id')

											->where('job_applications.id', $this->application_id)

											->groupBy('job_applications.id')

											->first();

		

		$this->interview_detail 	   =	DB::table('interview_schedules')

												->select('interview_schedules.*', 'master_status.name as master_status_name'

															

												)

												->join('master_status', 'master_status.id', '=', 'interview_schedules.stage')

												->where('interview_schedules.id', '=', $this->interview_id)

												->first();

												

																					

		$this->interviewStatus 			= DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();

		

		$this->interviewers					=	'';

		if(!empty($this->interview_detail->interviewers))

		{

			$customArr		=	array();

			if(!empty($this->interview_detail->interviewers)){

				$customArr	=	explode(" ",$this->interview_detail->interviewers);

			}

			

			$this->interviewers				=  DB::table('users')

										//->join('skills', 'job_skills.skill_id', '=', 'skills.id')

										->select('users.id', 'users.name')

										->whereIn('users.id', $customArr)

										->orderBy('users.name', 'ASC')

										->get();

		}

		

		//echo '<pre>'; print_r($this->interviewers); 

        return view('admin.interview-schedule.feedbackfrm', $this->data)->render();

    }
	
	public function store_feedbackfrm(Request $request)
    	{ 

		

		$application_id		=	$feedback_name		= $interview_id	= $message = 	'';

		

		$application_id		=	$request->application_id;

		$feedback_name		=	$request->feedback_name;

		$interview_id		=	$request->interview_id;

		$message			=	$request->message;

		 



if(!empty($application_id) && !empty($feedback_name) && !empty($interview_id) && !empty($message))

{

	// WORKING WITH TIMELINE .............................................................................................................................................

		

		$application 		= 	JobApplication::find($application_id);

		$date				= 	date('Y-m-d H:i:s');

		$insert_type		= 	5;

		$statusRow 			=	ApplicationStatus::findOrFail($application->master_status_id);

		$values 			=   array(

												'owner_id' 		 			=> auth()->user()->id,

												'application_id' 			=> $application->id, 

												'application_email' 		=> $application->email,

												'job_id' 					=> $application->job_id,

												'process_id'  				=> $application->master_status_id,

												

												'status_name'  				=> $statusRow->name,

											//	'process_child_name'	=> $status_name,

												'note'			 			=> 'Feedback update',

												'insert_type'				=> $insert_type,

												'create_at'					=> $date, 

												

												'update_at'					=> $date,

												//'interview_schedule_date'				=> $dateTime,

												//'interview_from_time'					=> $request->schedule_from_time,

												//'interview_to_time'						=> $request->schedule_to_time,

												'interview_mode'						=> $feedback_name,

												

												//'interview_mention_link'				=> $request->url_link,

												//'interviewers'					=>	$date,

												//'interview_additional_email'			=>	$request->other_email,

												'interview_message'						=>	$message

												

												

												

								  );

		if($application->child_status_id > 0)

		{

			$childStatus 					=	'';

			$childStatus 					=	DB::table('candidate_status')->select('*')->where('company_id', '=', $this->user->company_id)->where('id', $application->child_status_id)->first(); 

			$values['process_child_name'] 	=	@$childStatus->name; 	

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

		 if(!empty($application_id) && !empty($feedback_name) && !empty($interview_id))

		 {

				$values = array(

											'interview_id' 		 		=> $interview_id,

											'application_id' 		 	=> $application_id, 

											'feedback_name' 		 	=> $feedback_name,

											'message' 					=> $message,

											'update_by'  				=> $this->user->id,

											'created_at'			 	=> date('Y-m-d')

										);

				$update = DB::table('interview_feedback')->insert($values);

					

				if($update)

				{		

					return Reply::dataOnly([

										'status' 	=> 'success',

										'message'	=>	'Interview Feedback added successfully !'									

									]);

			 

				}

				else

				{

					return Reply::dataOnly([

										'status' 	=> 'fail',

										'message'	=>	'Something worng, please try again !'

									]);	

				

				}

		 }

		 else

		 {

				return Reply::dataOnly([

										'status' 	=> 'fail', 

										'message'	=>	'Something worng, please try again !'

									]);	

		 }

		 



}

else

{

		return Reply::dataOnly([

										'status' 	=> 'fail', 

										'message'	=>	'Something worng, please try again !'

								]);		

}	 

		 



		 

		 

		 

		 

		

		 	

	}
	
	public function export($start, $length)
    	{

        $filters = [

						'start' 			=> $start,

						'length' 			=> $length,

						'loginUserId'		=> $this->user->id,

						'loginUserRole'		=> $this->user->role->role_id

					];



        $data = [

					'company' => $this->companyName

				];

				

       	return Excel::download(new InterviewScheduleExport($filters, $data), 'InterviewSchedule.xlsx', ExcelExcel::XLSX);

    

	}
	
	 public function create_status(Request $request)
	 	{

        //abort_if(! $this->user->can('add_schedule'), 403);
	    $this->interview_node		=	GeneralMethod::interview_mode();
		$this->application_id		=	$this->interview_id		= $this->feedback_id	=	'';
		$this->application_id		=	$_REQUEST['application_id'];
		$this->interview_id			=	$_REQUEST['interview_id'];
		$this->feedback_id			=	$_REQUEST['feedback_id'];

		if($this->user->role->role_id != 10)
		{	
				  $this->candidates 	= JobApplication::where('job_applications.submitted_by', $this->user->id)->get();
				 //$this->schedules = $this->schedules->where('job_applications.submitted_by', $this->user->id);	
		}
		else
		{
			 $this->candidates 	= JobApplication::all();
		}

		

		

		

		

		$this->application 				= JobApplication::select('job_applications.*', 'clients.name as client_name', 'jobs.title as job_title', 'client_location.city', 'jobs.company_id')

											->join('jobs', 'jobs.id', '=', 'job_applications.job_id')

											->join('clients', 'clients.id', '=', 'jobs.client_id')

											->leftjoin('client_location_jobs', 'client_location_jobs.job_id', 'jobs.id')

											->leftjoin('client_location', 'client_location.id', 'client_location_jobs.client_location_id')

											->where('job_applications.id', $this->application_id)

											->groupBy('job_applications.id')

											->first();

		$this->interviewStatus 			= DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();

			

		

		$this->client_contact_info 		=	array();

		$this->interview_detail			=	array();	

		if($this->feedback_id ==4) 

		{

			$this->interview_detail 	= 	DB::table('interview_schedules')

												->select('interview_schedules.*', 'master_status.name as master_status_name'

															

												)

												->join('master_status', 'master_status.id', '=', 'interview_schedules.stage')

												

												->where('interview_schedules.id', '=', $this->interview_id)

												->first();

			$this->client_contact_info 	=  DB::table('jobs')

										//->join('clients', 'clients.id', '=', 'jobs.client_id')

										->join('client_contact_info', 'client_contact_info.client_id', '=', 'jobs.client_id')

										->select('client_contact_info.id', 'client_contact_info.contact_person_name')

										->where('jobs.id', '=', $this->application->job_id)

										->orderBy('client_contact_info.contact_person_name', 'ASC')

										->get();	

		} 

		

		//$this->users 		= User::company()->get();

        $this->scheduleDate = $request->date;

        return view('admin.interview-schedule.status', $this->data)->render();

    }
    
	
	public function data(Request $request)
		{
			abort_if(! $this->user->cans('view_schedule'), 403);
			//echo $this->user->company_id;			
			$available_status		=	'';
			$available_status		=	GeneralMethod::available_status();		
			
			$feedback_status		=	'';
			$feedback_status		=	GeneralMethod::feedback_status();
			
			$interview_mode_arr		=	'';
			$interview_mode_arr		=	GeneralMethod::interview_mode();		

        $shedule = InterviewSchedule::select(
			'interview_schedules.id','job_applications.full_name','interview_schedules.status', 'interview_schedules.schedule_date','interview_schedules.available_status',
			'interview_schedules.reschedule', 'interview_schedules.feedback', 'interview_schedules.interview_mode', 'interview_schedules.interviewers', 
			 'interview_schedules.interview_type', 'interview_schedules.schedule_from_time',
			'users.name as source',
			'interview_schedules.job_application_id',
			'master_status.name as master_status_name', 'candidate_status.name as child_status_name', 
			'jobs.title as job_title',
			'jobs.id as job_id',
			'companies.company_name',
			
			'job_applications.current_designation', 
			'interview_mode.name as mode_name',
			'interview_mode_lavel.name as mode_lavel_name',
			'master_status.css_style',
			'interview_schedules.average_star_rating'
			
		 )
            ->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')  
			->leftjoin('users', 'users.id', 'job_applications.submitted_by')
			->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
			->leftjoin('companies', 'companies.id', 'jobs.company_id')
			->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')
			->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
			->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
			->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage');

		if (\request('search') != "")
		{
			//$search				=	request('search');	
			//$shedule			=	$shedule->where('job_applications.full_name', 'LIKE', "%$search%");
		}	
        // Filter by status
       /* if($request->status != 'all' && $request->status != ''){
            $shedule = $shedule->where('interview_schedules.status', $request->status);
        }*/
        // Filter By candidate
       /* if($request->applicationID != 'all' && $request->applicationID != ''){
            $shedule = $shedule->where('job_applications.id', $request->applicationID);
        }*/
		$shedule 			=	$shedule->where('interview_schedules.company_id', $this->user->company_id);
		// $shedule 			= 	$shedule->where('interview_schedules.stage', '=', 3);
		 
        // Filter by StartDate
        if($request->startDate !== null && $request->startDate != 'null'){
            $shedule 		= $shedule->where(DB::raw('DATE(interview_schedules.`schedule_date`)'), '>=', "$request->startDate");
        }



        // Filter by EndDate

        if($request->endDate !== null && $request->endDate != 'null'){

            $shedule 		= $shedule->where(DB::raw('DATE(interview_schedules.`schedule_date`)'), '<=', "$request->endDate");

        }

		/*if (\request('search') != "")
		{
			$search			=	request('search');	           
			$shedule 		=	$shedule->where('job_applications.full_name', 'LIKE', "%$search%");	         		
		}   */

		   
		//$client->where('clients.name', 'LIKE', "%$search%");        
		//$shedule->orderBy("interview_schedules.id", "DESC");       

		$shedule->orderBy('interview_schedules.schedule_date', 'DESC');                            
        
		$shedule->get();          

		//echo $shedule->toSql(); exit; 

        return DataTables::of($shedule)

            ->addColumn('action', function ($row) {

                $action = '';





               /*  if( $this->user->cans('view_schedule')){

                     $action.= '<a href="javascript:;" data-row-id="' . $row->id . '" class="btn view-data"

                       data-toggle="tooltip" data-original-title="'.__('app.view').'"><i class="fa fa-search text-warning" aria-hidden="true"></i></a>';

                 }

                if( $this->user->cans('edit_schedule')){

                    $action.= '<a href="javascript:;" style="margin-left:4px" data-row-id="' . $row->id . '" class="btn edit-data"

                      data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa flaticon2-pen text-primary" aria-hidden="true"></i></a>';

                }*/



                if( $this->user->cans('delete_schedule')){

                    $action.= ' <a href="javascript:;" class="btn sa-params"

                      data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times text-danger" aria-hidden="true"></i></a>';

                }

                return $action;

            })
            ->editColumn('full_name', function ($row) {				

				/*$name	=	ucwords(substr($row->full_name, 0, 10));	

				$fname	=	'<a href="javascript:void(0);" class="show-detail" data-widget="control-sidebar" data-slide="true" data-row-id="'.@$row->job_application_id.'">'.$name.'</a>'; 

                return $fname;*/

				

				if(strlen($row->full_name)> 15){ 		

					$html			=	'<div class="tooltip">'.ucwords(substr($row->full_name, 0, 15)).'...<span class="tooltiptext"><span>'.ucwords($row->full_name).'<br></span></div>';				

				}else{

					$html			=	'<div class="tooltip">'.ucwords(substr($row->full_name, 0, 15)).'<span class="tooltiptext"><span>'.ucwords($row->full_name).'<br></span></div>';				

				}

				$fname	=	'<a href="javascript:void(0);" class="show-detail" data-widget="control-sidebar" data-slide="true" data-row-id="'.@$row->job_application_id.'">'.$html.'</a>'; 

                return $fname;

				

				

            })
            ->editColumn('schedule_date', function ($row) {

                return Carbon::parse($row->schedule_date)->format('d M, Y H:i a');

            })
			->editColumn('job_title', function ($row) {

               // return substr(ucwords($row->job_title), 0, 10);

				

				if(strlen($row->job_title)> 15){ 		

					$html			=	'<div class="tooltip">'.ucwords(substr($row->job_title, 0, 15)).'...<span class="tooltiptext"><span>'.ucwords($row->job_title).'<br></span></div>';				

				}else{

					$html			=	'<div class="tooltip">'.ucwords(substr($row->job_title, 0, 15)).'<span class="tooltiptext"><span>'.ucwords($row->job_title).'<br></span></div>';				

				}

				

                return $html;

				

            })
			->editColumn('company_name', function ($row) {

                return ucwords($row->company_name);

            })
			->editColumn('interview_mode', function ($row)  use ($interview_mode_arr) {

				$interview_mode	=	'N/A';

				if($row->interview_mode > 0){

					$interview_mode	=	$interview_mode_arr[1];			

				}

                return $interview_mode;

            })
			->editColumn('master_status_name', function ($row) {

                return substr($row->master_status_name, 0, 10);

            })
			->editColumn('child_status_name', function ($row) {

			

				$child_status	=	'N/A';

				if(!empty($row->child_status_name)){

					$child_status	= substr($row->child_status_name, 0, 10);

				}

                return $child_status;

            })
			->editColumn('source', function ($row) {

                return substr(ucwords($row->source), 0, 10);

            })
			->editColumn('available_status', function ($row)  use ($available_status) {

               

			    $selClassname	=	'';

				$option			=	'';

				$i				=	0;

				if(!empty($available_status))

				{

					foreach($available_status as $key=>$val)

					{

						$sel	=	'';

						if($row->available_status > 0)

						{

							if($row->available_status == $i ){

								$sel			=	'selected="selected"';	

								$selClassname	=	$val['classname'];

							}	

						}

						$option 		.= '<option style="background-color:#FFFFFF; color:#000000" value="'.$i.'"   '.$sel.'>'.$val['name'].'</option>'; 

						$i++;

					}

				}

				

				$action 		 = '';

				$action 		.= '<select  name="available_status" data-application="'.$row->job_application_id.'", data-interview_id="'.$row->id.'" class="updateAvailableStatus '.$selClassname.'" >';

				$action 		.= $option;

				$action 		.= '</select>'; 

				return $action;





            })
			->editColumn('feedback_status', function ($row)  use ($feedback_status) { 

				/*$selClassname	=	'';
				$option			=	'';
				if(!empty($feedback_status))
				{
					foreach($feedback_status as $key=>$val)
					{
						$sel	=	'';
						if($row->feedback == $key ){
							$sel			=	'selected="selected"';	
							$selClassname	=	$val['classname'];
						}
						$option 		.= '<option value="'.$key.'"   '.$sel.'>'.$val.'</option>';
					}
				}
				$action 		 = '';
				$action 		.= '<select  name="feedback_status" onchange="updateFeedbackStatus(this.value, '.$row->id.', '.$row->job_application_id.');" >';
				$action 		.= $option;
				$action 		.= '</select>'; 
				return $action;*/

				$selClassname	=	'';
				$option			=	'';
				$i				=	0;
				if(!empty($feedback_status))
				{
					foreach($feedback_status as $key=>$val)
					{
						$sel	=	'';
						if($row->feedback > 0)
						{
							if($row->feedback == $i ){
								$sel			=	'selected="selected"';	
								$selClassname	=	$val['classname'];
							}	
						}

						$interview_type		=	$row->interview_type;
						if($interview_type ==2 && $i == 4){
									$redate = Carbon::parse($row->reschedule)->format('d M, Y').'-'.$row->schedule_from_time;
									$option 		.= '<option style="background-color:#FFFFFF; color:#000000" value="'.$i.'"   '.$sel.'>RE-'.$redate.'</option>'; 	
						}
						else{
							$option 		.= '<option style="background-color:#FFFFFF; color:#000000" value="'.$i.'"   '.$sel.'>'.$val['name'].'</option>'; 	
						}
						$i++;
					}
				}				
				$action 		 = '';
				$action 		.= '<select style="width:-webkit-fill-available;width: -moz-available;"  name="available_status" data-application="'.$row->job_application_id.'", data-interview_id="'.$row->id.'" class="updateFeedbackStatus '.$selClassname.'" >';
				//$action 		.= '<select  name="feedback_status" onchange="updateFeedbackStatus(this.value, '.$row->id.', '.$row->job_application_id.');" class="updateAvailableStatus '.$selClassname.'"  >';
				$action 		.= $option;
				$action 		.= '</select>'; 
				return $action;
				
            })
			->editColumn('interviewer', function ($row) {

               

			 /*   $html 		 		= '<div class="interviewers text-center"> 

              <span class="total-like-user-main" dir="ltl"> ';

			   	$interviewers		=	$row->interviewers;

				if(!empty($interviewers))

				{

					 $record 	= DB::table('users')->select('name')->whereIn('id', explode(",", $interviewers))->get();

					 $i=0;

					 foreach($record as $rec){

					 	

						if($i==0){

						$html .= '<a data-toggle="tooltip" data-placement="top" title="" href="Javascript:void(0);" data-original-title="'.@$rec->name.'"><span class="rounded-pill inside-img1">'.substr(strtoupper(@$rec->name), 0, 2).'<i class="fa fa-plus" aria-hidden="true"></i></span></a>'; 

						}

                        else{

						$html .= '<a data-toggle="tooltip" data-placement="top" title="" href="Javascript:void(0);" data-original-title="'.@$rec->name.'"><img alt="Generic placeholder image" src="http://bootdey.com/img/Content/avatar/avatar4.png" class="total-like-user rounded-pill"></a>'; 

						}

						$i++;

					 }

				}

				else

				{

					

					$html .= '<a data-toggle="tooltip" data-placement="top" title="" href="Javascript:void(0);" data-original-title="No Record Found" class="total-like-user rounded-pill"><span class="rounded-pill inside-img">N/A</span></a>'; 

			  	

				}								

				$html .= '</span></div>'; 

				return $html;*/

				$cnt 			=	1; 

				$interviewers	=	$row->interviewers;

				$record 		=	DB::table('users')->select('name')->whereIn('id', explode(",", $interviewers))->get();

				$totalrecord 	=	DB::table('users')->select('name')->whereIn('id', explode(",", $interviewers))->count();

				

				$contactHtml	='';

				foreach($record as $rec)

				{

					$fname		= strtoupper(substr($rec->name, 0, 2));

					if($totalrecord <=1 && $cnt==1){

						$contactHtml	.=	'<div class="text-center tooltip">'.$fname.'&nbsp;<span class="tooltiptext">';	

					}

					else if($cnt ==1){

						$contactHtml	.=	'<div class="text-center tooltip">'.$fname.'&nbsp;<i class="fa fa-plus fa-1x text-dark" aria-hidden="true"></i><span class="tooltiptext">';			

					}

					

					

					

					

					

						$contactHtml 			.= '

														Name : '.$rec->name.'<br>

														

													';

					

					

					

					$cnt++;								

			

				}

				$contactHtml	.=	'</span></div><br>';

				return $contactHtml;

								



})
            ->editColumn('status', function ($row) {

                if($row->status == 'pending'){

                    return '<label class="badge bg-warning text-white">'.__('app.pending').'</label>';

                }

                if($row->status == 'hired'){

                    return '<label class="badge bg-success text-white">'.__('app.hired').'</label>';

                }

                if($row->status == 'canceled'){

                    return '<label class="badge bg-danger text-white">'.__('app.canceled').'</label>';

                }

                if($row->status == 'rejected'){

                    return '<label class="badge bg-danger text-white">'.__('app.rejected').'</label>';

                }

            })
			
			
		    ->editColumn('chkbox', function ($row) {
                    return '<div class="form-check form-check-sm form-check-custom form-check-solid">
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
                    <a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'" class="show-application-detail">
                      <div data-row-id="'.$row->job_application_id.'"  class="symbol-label '.$bgTextDecore.' border-radius-5 show-application-detail">'.ucwords($strVal).'</div>
                    </a>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="'.ucwords(@$row->full_name).'">'.ucwords(substr(@$row->full_name, 0, 10)).'</a>
                    <a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'" class="fw-bold text-muted d-block fs-7 show-application-detail text-hover-primary" data-toggle="tooltip" title="'.ucwords(@$row->current_designation).'">'.ucwords(substr(@$row->current_designation, 0, 10)).'</a>
                  </div>';
            })
			->editColumn('Requisitions', function ($row) { 
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.ucwords($row->job_title).'">'.ucwords(substr($row->job_title, 0, 10)).'</a>
                    <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords($row->company_name).'">'.ucwords(substr($row->company_name, 0, 10)).'</a>
                  </div>';
            })
			->editColumn('InterviewMode', function ($row) {
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.ucwords(substr($row->mode_name, 0, 10)).'</a> 
                    <span class="text-muted fw-bold d-block fs-7">'.ucwords(substr($row->mode_lavel_name, 0, 10)).'</span> 
                  </div>';
            })
			->editColumn('Interviewers', function ($row) {
					
					$html			=	'';
					$interviewers	= $row->interviewers;
                    if(!empty($interviewers))
					{
						$interviewersArr 	=	explode(',', $interviewers);
						$interviewersData	=	DB::table('users')
												->select('users.id', 'users.name', 'users.image')
												->whereIn('users.name', $interviewersArr)
												->get();
						if(!empty($interviewersData)){
							foreach($interviewersData as $intVal){
								$name		=	$image	=	'';
								$name		=	$intVal->name;
								$image		=	$intVal->image;								
								$imagePath	=	'';
				  				$imagePath	=	url('public/user-uploads/team_profile_img/'.@$image);
								$html		.=	'<div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="'.@$name.'">
													<img alt="Pic" src="'.@$imagePath.'">
												</div>';
							}
						}							
					}
										
					return ' <div class="d-flex flex-column"><div class="symbol-group symbol-hover">'.$html.'</div>';
            })
			
			->editColumn('Source', function ($row) {
					
					$bgTextDecore			=	$strVal	=	'';
					$str					=	trim(@$row->source);
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
					
                  return ' <div class="d-flex flex-column">
								<div class="symbol-group symbol-hover">
									<div class="symbol symbol-circle symbol-30px overflow-hidden me-2" style="float:left">
										<a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'" class="show-application-detail">
										<div data-row-id="'.$row->job_application_id.'"  class="symbol-label '.$bgTextDecore.' border-radius-5 show-application-detail" style="border-radius: 50%!important;font-size: 13px!important;font-weight: 600;">'.ucwords($strVal).'</div>
										</a>
									</div>
								</div>
                  			</div>
                    		<a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="'.ucwords(@$row->source).'">'.ucwords(substr(@$row->source, 0, 10)).'</a>';
					
					
						
						
                 /*return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'" class="show-application-detail">
                      <div data-row-id="'.$row->job_application_id.'"  class="symbol-label '.$bgTextDecore.' border-radius-5 show-application-detail">'.ucwords($strVal).'</div>
                    </a>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">'.ucwords(substr(@$row->full_name, 0, 15)).'</a>
                    <span class="text-muted fw-bold d-block fs-7">'.ucwords(substr(@$row->current_designation, 0, 15)).'</span>
                  </div>';*/
				  
				  
            })
			->editColumn('InterviewDate', function ($row) {
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.Carbon::parse($row->schedule_date)->format('d M, Y').'</a>
                    <span class="text-muted fw-bold d-block fs-7">'.Carbon::parse($row->schedule_date)->format('H:i a').'</span>
                  </div>';
            })
			->editColumn('Feedback', function ($row) use ($feedback_status, $available_status) {
					
					$available_status_name	=	'';
					if($row->available_status > 0){
						$available_status_name	=	@$available_status[@$row->available_status]['name'];   	
					}else{
						$available_status_name	=	@$available_status[6]['name']; 
					}
					
					
					
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 show-feedback">'.ucwords(substr(@$feedback_status[@$row->feedback]['name'], 0, 12)).'</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.ucwords(substr(@$available_status_name, 0, 12)).'</span>
                  </div>';
            })			 
			->editColumn('Rating', function ($row) {
					$imagePath				=	$text = 	'';
					$average_star_rating	=	'';
					$average_star_rating	=	$row->average_star_rating; 
					if($average_star_rating == 1){
						$imagePath	=	url('public/img/star/1.png');
						$text		=	'Bad';
					}else if($average_star_rating == 2){
						$imagePath	=	url('public/img/star/2.png');
						$text		=	'Poor';
					}else if($average_star_rating == 3){
						$imagePath	=	url('public/img/star/3.png');
						$text		=	'Average';
					}else if($average_star_rating == 4){
						$imagePath	=	url('public/img/star/4.png');
						$text		=	'Best';
					}else if($average_star_rating == 5){
						$imagePath	=	url('public/img/star/5.png');	
						$text		=	'Very Best';
					}else{
						$imagePath	=	url('public/img/star/5-blank.png');
						$text		=	'Blank';
					}
										
                    return ' <img src="'.@$imagePath.'" alt="image" style="width: 5.5rem">
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.@$text.'</span>';
            })
			->editColumn('Stage', function ($row) {
                    return '<div class="d-flex flex-column"> 
                        <span class="badge fw-bolder fs-6 px-2 py-1 '.@$row->css_style.'">'.substr(@$row->master_status_name, 0, 10).'</span>
                        <span class="text-muted fw-bold text-muted d-block fs-7">'.substr(@$row->child_status_name, 0, 10).'</span>
                    </div>';
            })
			
			
            ->rawColumns(['chkbox', 'Name', 'Requisitions', 'InterviewMode', 'Interviewers', 'Source', 'InterviewDate', 'Feedback', 'Rating', 'Stage'])

            ->make(true);

    }

	public function interviewData(Request $request)
		{
			
			$company_id				= 	auth()->user()->company_id;
			
			$available_status		=	'';
			$available_status		=	GeneralMethod::available_status();		
			
			$feedback_status		=	'';
			$feedback_status		=	GeneralMethod::feedback_status();
			
			$interview_mode_arr		=	'';
			$interview_mode_arr		=	GeneralMethod::interview_mode();		

        	$shedule 				= InterviewSchedule::select(
												'interview_schedules.id','job_applications.full_name','interview_schedules.status', 'interview_schedules.schedule_date','interview_schedules.available_status',
												'interview_schedules.reschedule', 'interview_schedules.feedback', 'interview_schedules.interview_mode', 'interview_schedules.interviewers', 
												 'interview_schedules.interview_type', 'interview_schedules.schedule_from_time',
												'users.name as source',
												'interview_schedules.job_application_id',
												'master_status.name as master_status_name', 'candidate_status.name as child_status_name', 
												'jobs.title as job_title',
												'jobs.id as job_id',
												'companies.company_name',
												
												'job_applications.current_designation', 
												'interview_mode.name as mode_name',
												'interview_mode_lavel.name as mode_lavel_name',												
												'master_status.css_style'
											
										 )
									->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')  
									->leftjoin('users', 'users.id', 'job_applications.submitted_by')
									->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
									->leftjoin('companies', 'companies.id', 'jobs.company_id')
									->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')
									->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
									->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
									->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage');

		
      
		$shedule 			=	$shedule->where('interview_schedules.company_id', $this->user->company_id);
		$shedule 			=	$shedule->where('job_applications.job_id', $request->hid_job_id);
		
		
		
        if($request->startDate !== null && $request->startDate != 'null'){
            $shedule 		= $shedule->where(DB::raw('DATE(interview_schedules.`schedule_date`)'), '>=', "$request->startDate");
        }

        if($request->endDate !== null && $request->endDate != 'null'){

            $shedule 		= $shedule->where(DB::raw('DATE(interview_schedules.`schedule_date`)'), '<=', "$request->endDate");

        }	       

		$shedule->orderBy('interview_schedules.schedule_date', 'DESC');                            
        
		$shedule->get();          

        return DataTables::of($shedule)

           
			
			->editColumn('chkbox', function ($row) {
                    return '<div class="form-check form-check-sm form-check-custom form-check-solid">
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
                    <a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'" class="show-application-detail">
                      <div data-row-id="'.$row->job_application_id.'"  class="symbol-label '.$bgTextDecore.' border-radius-5 show-application-detail">'.ucwords($strVal).'</div>
                    </a>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="'.ucwords(@$row->full_name).'">'.ucwords(substr(@$row->full_name, 0, 7)).'</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="'.ucwords(@$row->current_designation).'">'.ucwords(substr(@$row->current_designation, 0, 7)).'</span>
                  </div>';
            })
			->editColumn('Requisitions', function ($row) { 
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.ucwords($row->job_title).'">'.ucwords(substr($row->job_title, 0, 7)).'</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="'.ucwords($row->company_name).'">'.ucwords(substr($row->company_name, 0, 7)).'</span>
                  </div>';
            }) 
			->editColumn('InterviewMode', function ($row) {
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.ucwords(substr($row->mode_name, 0, 15)).'</a> 
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.ucwords(substr($row->mode_lavel_name, 0, 15)).'</span> 
                  </div>';
            })
			->editColumn('Interviewers', function ($row) use($company_id) {
			
					$interviewers	=	$html =	"";
					$interviewers	=	$row->interviewers;
					
					$html 			=	'<div class="d-flex flex-column"><div class="symbol-group symbol-hover">------</div></div>';
					
					if(!empty($interviewers))
					{
						$trimmed_array	=	explode(',', $interviewers);
						$trimmed_array 	= 	array_map('trim', $trimmed_array);
						
						$html 				=	'<div class="d-flex flex-column"> 
                  									<div class="symbol-group symbol-hover"> ';
				  							
						$InterviewArr 		=  DB::table('users')
										 		->select('users.name', 'users.email', 'users.mobile', 'users.image')
										 		->where('users.company_id', '=', $company_id)
										 		->whereIn('users.name', $trimmed_array)
										 		->get();												
											
						foreach($InterviewArr as $idata){ 
						
							$imagePath		=	"";
							$imagePath		=	"https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg";
							if(!empty(@$idata->image)){
								$imagePath	=	url('public/user-uploads/team_profile_img/'.@$idata->image);	
							}
				  			
							$html				.=	'<div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="'.@$idata->name.'" data-original-title="One">
														<img alt="Pic" src="'.@$imagePath.'">
													</div>';										
						}
						
						/*$html				.=	'<div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="One">
														<img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg">
													</div>
													<div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Two">
														<img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_19.jpg">
													</div>
													<div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Three">
														<img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_22.jpg">
													</div>';*/
													
													
													
						$html				.=  	'</div>
                  							  	</div>';
					}
					
                    return $html;
            })
			->editColumn('Source', function ($row) {
					
					$bgTextDecore			=	$strVal	=	'';
					$str					=	trim(@$row->source);
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
					
                  return ' <div class="d-flex flex-column">
								<div class="symbol-group symbol-hover">
									<div class="symbol symbol-30 symbol-circle symbol-light border-radius-5" style="float:left">
										<a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'">
											<div data-row-id="'.$row->job_application_id.'"  class="symbol-label '.$bgTextDecore.' border-radius-5 show-application-detail" style="border-radius: 50%!important;font-size: 13px!important;font-weight: 600;">'.ucwords($strVal).'</div>
										</a>
									</div>
								</div>
                  			</div>

                    		<span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="'.ucwords(@$row->source).'">'.ucwords(substr(@$row->source, 0, 7)).'</span>';
					
					
						
						
                 /*return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'" class="show-application-detail">
                      <div data-row-id="'.$row->job_application_id.'"  class="symbol-label '.$bgTextDecore.' border-radius-5 show-application-detail">'.ucwords($strVal).'</div>
                    </a>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">'.ucwords(substr(@$row->full_name, 0, 15)).'</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.ucwords(substr(@$row->current_designation, 0, 15)).'</span>
                  </div>';*/
				  
				  
            })
			->editColumn('InterviewDate', function ($row) {
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.Carbon::parse($row->schedule_date)->format('d M, Y').'</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.Carbon::parse($row->schedule_date)->format('H:i a').'</span>
                  </div>';
            })
			->editColumn('Feedback', function ($row) use ($feedback_status, $available_status) {
					
					$available_status_name	=	'';
					if($row->available_status > 0){
						$available_status_name	=	@$available_status[@$row->available_status]['name'];   	
					}else{
						$available_status_name	=	@$available_status[6]['name']; 
					}
					
					
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 show-feedback" data-bs-toggle="modal" data-bs-target="#interview_feedback">'.ucwords(substr(@$feedback_status[@$row->feedback]['name'], 0, 12)).'</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.ucwords(substr(@$available_status_name, 0, 12)).'</span>
                  </div>';
            })		 
			->editColumn('Rating', function ($row) {
					$interviewId	=	$row->id; 
			
                    return ' <img src="https://preview.keenthemes.com/metronic/theme/html/demo1/dist/assets/media/logos/stars.png" alt="image" style="width: 5.5rem">
                    <span class="text-muted fw-bold text-muted d-block fs-7">Above Avarage</span>';
            })
			->editColumn('Stage', function ($row) {
                    return '<div class="d-flex flex-column"> 
                        <span class="badge fw-bolder fs-6 px-2 py-1 '.@$row->css_style.'">'.substr(@$row->master_status_name, 0, 10).'</span>
                        <span class="text-muted fw-bold text-muted d-block fs-7">'.substr(@$row->child_status_name, 0, 10).'</span>
                    </div>';
            })
			
            ->rawColumns(['chkbox', 'Name', 'Requisitions', 'InterviewMode', 'Interviewers', 'Source', 'InterviewDate', 'Feedback', 'Rating', 'Stage'])

            ->make(true);

    }
	
	
	public function showfeedback($id)
		{
			
			 
			$this->interviewSchedule 			=	"";
			$this->interviewSchedule 			= DB::table('interview_schedules')  
													->select(
																'interview_schedules.id as interview_schedules_id' , 'interview_schedules.schedule_date',
																'job_applications.id', 'job_applications.full_name',  'job_applications.current_designation',
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
																
																'users.name as upload_by_name', 'users.image'
															)
													->join('job_applications', 'job_applications.id', '=', 'interview_schedules.job_application_id')
													->join('users', 'users.id', '=', 'job_applications.submitted_by')		 
													//->leftjoin('users', 'users.id', 'job_applications.submitted_by')
													->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
													->join('clients', 'clients.id', '=', 'jobs.client_id')
													//->leftjoin('companies', 'companies.id', 'jobs.company_id')
													->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
													->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
													->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
													->leftjoin('candidate_status', 'candidate_status.id', 'job_applications.child_status_id')
													->where('interview_schedules.id', $id)
													->first();
					
		
			$available_status				=	'';
			$this->available_status			=	GeneralMethod::available_status();

			$feedback_status				=	'';
			$this->feedback_status			=	GeneralMethod::feedback_status();
			
			$view 						= view('admin.interview-schedule.showfeedback', $this->data)->render();
       		return Reply::dataOnly(['status' => 'success', 'view' => $view]);		
		}
		
		
		public	function updateInterview(Request $request)
		{
								
						$interview_id =	$app_int_status =	$app_int_feedback_status	= $feedback_comment	= $application_id	= $rating	=	'';
							
						$interview_id 				= @$request->id;
						$app_int_status 			= @$request->app_int_status;
						$app_int_feedback_status 	= @$request->app_int_feedback_status;
						$feedback_comment 			= @$request->feedback_comment;
						$application_id 			= @$request->application_id;	
						$rating 					= @$request->rating;				
						
						
							
						$values = array(
											'interview_id' 		 		=> @$interview_id,
											'application_id' 		 	=> @$application_id, 
											'available_status' 			=> @$app_int_status, 
											'feedback_id' 				=> @$app_int_feedback_status,
											'star_ratings' 				=> @$rating,
											//'feedback_name' 		 	=> $feedback_name,
											'message' 					=> @$feedback_comment,
											'update_by'  				=> $this->user->id,
											'created_at'			 	=> date('Y-m-d:H:i:s')
										);
						$update = DB::table('interview_feedback')->insert($values);		
						
						
						
						$interviewCount 	= DB::table('interview_feedback')
												->select('interview_feedback.id')
												->where('interview_id', @$interview_id)
												->where('application_id', @$application_id)
												->count();
												
						$interviewSum 		= DB::table('interview_feedback')
												->select(DB::raw('SUM(star_ratings) AS star_ratings_sum'))
												->where('interview_id', @$interview_id)
												->where('application_id', @$application_id)
												->first();
												
						$averageRatimg		= $interviewSum->star_ratings_sum/$interviewCount;
							
						$returnValue = DB::table('interview_schedules')
						->where('id', $interview_id)
						->limit(1)  
						->update(array('available_status' => @$app_int_status, 'feedback' => @$app_int_feedback_status, 'average_star_rating' => ceil($averageRatimg) )); 
						
						
						$returnValue = DB::table('timeline')
						->where('interview_schedules_id', $interview_id)
						->limit(1)  
						->update(array('average_star_rating' => ceil($averageRatimg) )); 
							
								
						return Reply::dataOnly(['status' => 'success', 'msg' => 'Interview feedback update successfully.']); 
		
		
		}


	

} 