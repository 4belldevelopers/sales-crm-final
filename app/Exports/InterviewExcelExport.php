<?php

namespace App\Exports;

//use App\JobApplication;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeExport;

use App\ApplicationStatus;
use App\Helper\Files;
use App\Helper\Reply;
use App\Helper\GeneralMethod;
use App\Http\Requests\InterviewSchedule\StoreRequest;
use App\Http\Requests\StoreJobApplication;
use App\Http\Requests\UpdateJobApplication;
use App\InterviewSchedule;
use App\InterviewScheduleEmployee;
use App\Job;
use App\JobApplication;
use App\JobApplicationAnswer;
use App\JobLocation;
use App\JobQuestion;
use App\Notifications\CandidateScheduleInterview;
use App\Notifications\ScheduleInterview;
use App\Skill;
use App\Client;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\InterviewProcess;

class InterviewExcelExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
{
    use Exportable;

    protected $filters;
    protected $data;

    public function __construct(array $filters, array $data)
    {
        $this->filters 	= $filters;
        $this->data 	= $data;
    }

    public function collection()
    {
		$loginUserId	= $this->filters['loginUserId'];
		$loginUserRole	= $this->filters['loginUserRole'];
		
		
		// Clients 
		$clients		=	'all';
		if($this->filters['clients'] != 'all'){
			$clients			=	$this->filters['clients'];
		}
		//jobs
		$jobs		=	'all';
		if($this->filters['jobs'] != 'all'){
			$jobs				=	$this->filters['jobs'];
		}
		//master_status_id
		$master_status_id		=	'all';
		if($this->filters['master_status_id'] != 'all'){
			$master_status_id	=	$this->filters['master_status_id'];
		}
		//source
		$source		=	'all';
		if($this->filters['source'] != 'all'){
			$source	=	$this->filters['source'];
		}
		//custom_text
		$custom_text		=	'none';
		if($this->filters['custom_text'] != 'none'){
			$custom_text	=	$this->filters['custom_text'];
		}
		//start_date
		if(!empty($this->filters['start_date'])){
			$start_date		=	$this->filters['start_date'];	
		}
		//end_date
		if(!empty($this->filters['end_date'])){
			$end_date		=	$this->filters['end_date'];	
		}
			
						
        $interviewScheduleList		=	DB::table('interview_schedules')
											->select(
														'interview_schedules.schedule_date',  
														'job_applications.full_name',
														'clients.name as client_name', 
														'jobs.title as requisitions_title',
														'users.name as source_user',
														'interview_schedules.status as interview_status_name',
														DB::raw(
																	'(CASE 
																			WHEN interview_schedules.interview_mode = 1 THEN "TELECON"     
																			WHEN interview_schedules.interview_mode = 2 THEN "VIDEO" 
																			WHEN interview_schedules.interview_mode = 3 THEN "F2F"
																			WHEN interview_schedules.interview_mode = 4 THEN "OTHER"
																	  END) AS interview_mode'
																),
														'master_status.name as status_name',
																																											
														DB::raw(
																	'(CASE 
																			WHEN interview_schedules.feedback = 0 THEN "PENDING" 
																			WHEN interview_schedules.feedback = 1 THEN "SHORTLISTED"      
																			WHEN interview_schedules.feedback = 2 THEN "REJECTED" 
																			WHEN interview_schedules.feedback = 3 THEN "FEEDBACK"    
																			WHEN interview_schedules.feedback = 4 THEN "RESCHEDULE"
																	  END) AS feedback'
																)
													)
													
													
											->join('job_applications', 'job_applications.id', '=', 'interview_schedules.job_application_id' )
											->join('jobs', 'jobs.id', '=', 'job_applications.job_id' )	
											->join('clients', 'clients.id', '=', 'jobs.client_id' )		
											->join('users', 'users.id', '=', 'job_applications.submitted_by' )
											->join('master_status', 'master_status.id', '=', 'job_applications.master_status_id' )		
											->join('candidate_status', 'candidate_status.id', '=', 'job_applications.child_status_id' )	;	
											
													
			
			 	
		 // Filter By CLIENT 
		if ( ($clients !='all') && ($clients >0) ) 
		{
            $interviewScheduleList = $interviewScheduleList->where('clients.id', $clients);
        }
		// Filter By JOBS
		if ( ($jobs != 'all' && $request->jobs != '')&& ($jobs > 0) ) 
		{
            $interviewScheduleList = $interviewScheduleList->where('jobs.id', $jobs);
        }
		// Filter By APPLICATION STATUS
		if ( ($master_status_id != 'all' && $request->master_status_id != '')&& ($master_status_id > 0) ) 
		{
            $interviewScheduleList = $interviewScheduleList->where('job_applications.master_status_id', $master_status_id);
        }
		// Filter By SUBMITTED 
		if (    ($source > 0) && ($source != 'all' && $source != '')   ) 
		{
            $interviewScheduleList = $interviewScheduleList->where('job_applications.submitted_by', $source);
        }
									
		// CUSTOM TEXT  
		if(!empty($custom_text) && $custom_text !='none' ){
				$interviewScheduleList 	=	$interviewScheduleList->where('clients.name', 'like', '%' . $custom_text . '%')
											->orWhere('jobs.title', 'like', '%' . $custom_text . '%')                       
											->orWhere('users.name', 'like', '%' . $custom_text . '%');
		} 
			
		
		$interviewScheduleList 		=	$interviewScheduleList->skip($this->filters['start'])->take($this->filters['length'])->get();	
		
		return $interviewScheduleList;
			
    }

    public function headings(): array
    {
        return [
					'Schedule Date', 'Application', 'Client', 'Requisitions', 'Source', 
					'Status', 'Mode', 'Stage', 'Feedback'
				]; 
    }

    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function(BeforeExport $event) {
                $event->writer->getProperties()->setTitle('Job Applications')->setDescription('job-applications file')->setCreator('Recruit')->setCompany($this->data['company']);
            },
            AfterSheet::class => function(AfterSheet $event) {
                $styleArray = [
                    'font' => [
                        'bold' => true,
                    ],
                ];
                $event->sheet->getDelegate()->getStyle('A1:H1')->applyFromArray($styleArray);
            },
        ];
    }
}
