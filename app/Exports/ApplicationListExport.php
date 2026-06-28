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

class ApplicationListExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
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
			
						
         $jobApplications = JobApplication::select(
		 		
				'clients.name as client_name',
				'jobs.title as job_title',
				DB::raw('count(job_applications.id) as total_application'),
				DB::raw("SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS sourcing"),
				DB::raw("SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS screening"),
				DB::raw("SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS interview"),
				DB::raw("SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS negotiation"),
				DB::raw("SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS offered"),
				DB::raw("SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS joined"),
				DB::raw("SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS rejected")
			
			)
			
            ->join('jobs', 'jobs.id', 'job_applications.job_id')
            ->join('clients', 'clients.id', '=', 'jobs.client_id');
			
			/*->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')
			->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
            ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
			->leftjoin('clients', 'clients.id', 'jobs.client_id')
			->leftjoin('users', 'users.id', 'job_applications.submitted_by')			
			->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')          
		    ->leftjoin('application_status', 'application_status.id', 'job_applications.status_id'); 
					*/
		
		
			//$application_data 		=	$application_data->join('jobs', 'jobs.client_id', '=', 'clients.id');
			//$application_data 		=	$application_data->join('job_applications', 'job_applications.job_id', '=', 'jobs.id');
			//$application_data 		=	$application_data->join('users', 'job_applications.submitted_by', '=', 'users.id');
			//$application_data 		=	$application_data->where('clients.company_id', $this->user->company_id);	
			
			
			 	
		 // Filter By CLIENT 
		if ( ($clients !='all') && ($clients >0) ) 
		{
            $jobApplications = $jobApplications->where('clients.id', $clients);
        }
		// Filter By JOBS
		if ( ($jobs != 'all' && $request->jobs != '')&& ($jobs > 0) ) 
		{
            $jobApplications = $jobApplications->where('jobs.id', $jobs);
        }
		// Filter By APPLICATION STATUS
		if ( ($master_status_id != 'all' && $request->master_status_id != '')&& ($master_status_id > 0) ) 
		{
            $jobApplications = $jobApplications->where('job_applications.master_status_id', $master_status_id);
        }
		// Filter By SUBMITTED 
		if (    ($source > 0) && ($source != 'all' && $source != '')   ) 
		{
            $jobApplications = $jobApplications->where('job_applications.submitted_by', $source);
        }
		
		
				
		/* // Filter By JOBS
       	
		 
		
		 // Filter By PASSPORT 
		if ($search_passport != 'all' && $search_passport != '' && $search_passport !='undefined' ) {
             $jobApplications = $jobApplications->where('job_applications.passport',  $search_passport); 
        }*/
			
						
		$jobApplications 		=	$jobApplications->groupBy('jobs.id');
		$jobApplications 		=	$jobApplications->orderBy('clients.name', 'ASC');
		
		$jobApplications 		=	$jobApplications->skip($this->filters['start'])->take($this->filters['length'])->get();	
		
		return $jobApplications;
			
    }

    public function headings(): array
    {
        return [
					'Client Name', 'Requisitions', 'Total Application', 'Sourcing', 'Screening', 
					'Interview', 'Negotiation', 'Offered', 'Joined', 'Rejected'
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
