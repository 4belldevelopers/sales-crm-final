<?php

namespace App\Exports;

use App\Helper\GeneralMethod;   
use App\JobApplication;
use App\Client;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeExport;

use App\InterviewSchedule;
use App\InterviewScheduleEmployee;
use App\MasterStatus;
use App\Notifications\CandidateNotify;
use App\Notifications\CandidateReminder;
use App\Notifications\CandidateScheduleInterview;
use App\Notifications\EmployeeResponse;
use App\Notifications\ScheduleInterview;
use App\Notifications\ScheduleInterviewStatus;
use App\Notifications\ScheduleStatusCandidate;
use App\ScheduleComments;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;
use App\Notifications\CandidateRejected;

class InterviewScheduleExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
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
	   
		$available_statusArr		=	'';
		$available_statusArr		=	GeneralMethod::available_status();
		
		$feedback_status			=	'';
		$feedback_status			=	GeneralMethod::feedback_status();
		
		$interview_mode_arr			=	'';
		$interview_mode_arr			=	GeneralMethod::interview_mode();
		
		
					
        $shedule = InterviewSchedule::select(
												'interview_schedules.id','job_applications.full_name',
												'companies.company_name',
												'jobs.title as job_title',
												'interview_schedules.schedule_date', 'interview_schedules.schedule_from_time', 'interview_schedules.schedule_to_time',
												'interview_schedules.url_link', 'interview_schedules.other_email',  'interview_schedules.interview_mode',  
												'interview_schedules.message', 'interview_schedules.available_status',
												
												'interview_schedules.feedback',
												'interview_schedules.interview_type',
												'interview_schedules.created_at'
											)
            ->leftjoin('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
			->leftjoin('users', 'users.id', 'job_applications.submitted_by')
			->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
			->leftjoin('companies', 'companies.id', 'jobs.company_id')
			->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')
			->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage');
			
			
		$shedule 		= $shedule->skip($this->filters['start'])->take($this->filters['length'])->get();	
		
		
		foreach($shedule as $list)
		{
				//echo '<pre>'; print_r($list->available_status);
				
				$interview_mode	=	'N/A';
				if($list->interview_mode >= 0){
					$interview_mode	=	$interview_mode_arr[$list->interview_mode];			
				}
				$list->interview_mode	= $interview_mode;
				
				$list->available_status	= $available_statusArr[$list->available_status]['name'];
				
				
				$interview_type		=	'';
				$interview_type		=	$list->interview_type;
				if($interview_type	 ==2 ){
					$list->interview_type  = 'RE-Schedule';
				}else{
					$list->interview_type  = 'Fresh Interview Schelude';	
				}
				
				
							
				$list->feedback	= $feedback_status[$list->feedback]['name'];
				
		}
		
		
					
						
						
		return $shedule;
			
			    
    }
	
	//$contactInfo 	= DB::table('client_contact_info')->select('*')->where('client_id', $row->id)->get();
	

    public function headings(): array
    {
        return [
					'ID', 'Candidate Name', 'Company Name', 'Job Title', 'Schedule Date', 'Schedule From Time', 
					'Schedule To Time', 'Url Link', 'Other Email', 'Interview Mode', 'Message', 
					'Available Status', 'Feedback', 'Interview Type', 'Created at'
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
