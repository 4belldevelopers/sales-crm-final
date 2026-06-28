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

class CvTrackerExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
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
		
		// Client 
		$clients		=	'all';
		if($this->filters['clients'] != 'all'){
			$clients		=	$this->filters['clients'];
		}
		
		// Jobs 
		$jobs		=	'all';
		if($this->filters['jobs'] != 'all'){
			$jobs			=	$this->filters['jobs'];
		}
		
		// Source 
		$source		=	'all';
		if($this->filters['source'] != 'all'){
			$source			=	$this->filters['source'];
		}
		
		// Passport 
		$search_passport		=	'all';
		if($this->filters['search_passport'] != 'all'){
			$search_passport	=	$this->filters['search_passport'];
		}
		
		// Passport 
		$search_np_id		=	'all';
		if($this->filters['search_np_id'] != 'all'){
			$search_np_id	=	$this->filters['search_np_id'];
		}
		
		// Status 
		$status		=	'all';
		if($this->filters['status'] != 'all'){
			$status	=	$this->filters['status'];
		}
		
		
		
		$chkAdminRole = DB::table('roles')
						->select('*')
						->where('id', $loginUserRole)
						->where('name', 'admin')
						->count(); 
						
         $jobApplications = JobApplication::select(
		 
				'job_applications.id',  'job_applications.full_name', 'job_applications.email' ,  'job_applications.secondrary_email',   'job_applications.phone', 
				'job_applications.secondary_mobile', 'job_applications.skills', 
				'jobs.title', 
				'job_applications.total_exp', 'job_applications.rel_exp',  'job_applications.passport',  'job_applications.ctc',  'job_applications.ectc',
				'job_applications.current_location',  'job_applications.current_designation',  'job_applications.current_company',
				'qualification_master.name as qualification_name',
				'master_status.name',
				'users.name as posted_by',
				'clients.name as client_name',
				'job_applications.created_at'
			
			)
			
            ->join('jobs', 'jobs.id', 'job_applications.job_id')
            ->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')
			->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
            ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
			->leftjoin('clients', 'clients.id', 'jobs.client_id')
			->leftjoin('users', 'users.id', 'job_applications.submitted_by')
			
			->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
           
		    ->leftjoin('application_status', 'application_status.id', 'job_applications.status_id');
			
			
		 if($loginUserRole == 0)
		 {	
		 	$jobApplications 	= $jobApplications->join('job_assign', 'job_assign.job_id', 'jobs.id');
			$jobApplications 	= $jobApplications->where('job_assign.user_id', $loginUserId);
			//$jobApplications 	= $jobApplications->where('job_assign.job_id', '=', $this->user->id);
		 }
		 	
		 // Filter By CLIENT 
		if ( ($clients !='all') && ($clients >0) ) 
		{
            $jobApplications = $jobApplications->where('jobs.client_id', $clients);
        }
		 // Filter By JOBS
        if ( ($jobs != 'all' && $request->jobs != '')&& ($jobs > 0) ) 
		{
            $jobApplications = $jobApplications->where('job_applications.job_id', $jobs);
        }	
		 // Filter By SUBMITTED 
		if (    ($source > 0) && ($source != 'all' && $source != '')   ) 
		{
            $jobApplications = $jobApplications->where('job_applications.submitted_by', $source);
        }
		
		 // Filter By PASSPORT 
		if ($search_passport != 'all' && $search_passport != '' && $search_passport !='undefined' ) {
             $jobApplications = $jobApplications->where('job_applications.passport',  $search_passport); 
        }
		
		// NOTICE PERIOD
		if ( ($search_np_id != 'all') &&  ($search_np_id != '') && ($search_np_id > 0) ) 
		{
			if($search_np_id == 1){  			// IMMEDIATE 
				$jobApplications = $jobApplications->where('job_applications.serving_notice_period_mode',  2);   	
			}else if($search_np_id == 2){  	// SERVING 
				$jobApplications = $jobApplications->where('job_applications.serving_notice_period_mode',  1);   	
			}else if($search_np_id == 3){  	// 0-15 Days 				
				$jobApplications = $jobApplications->whereBetween('job_applications.np_id', [1, 15]);	
			}else if($search_np_id == 4){  	// 16-30 Days 				
				$jobApplications = $jobApplications->whereBetween('job_applications.np_id', [16, 30]);	
			}else if($search_np_id == 5){  	// 16-30 Days 				
				$jobApplications = $jobApplications->whereBetween('job_applications.np_id', [31, 45]);	
			}else if($search_np_id == 6){  	// 16-30 Days 				
				$jobApplications = $jobApplications->whereBetween('job_applications.np_id', [46, 60]);	
			}else if($search_np_id == 7){  	// 16-30 Days 				
				$jobApplications = $jobApplications->whereBetween('job_applications.np_id', [61, 75]);	
			}else if($search_np_id == 8){  	// 16-30 Days 				
				$jobApplications = $jobApplications->whereBetween('job_applications.np_id', [76, 90]);	
			}else if($search_np_id == 9){  	// 16-30 Days 				
				$jobApplications = $jobApplications->where('job_applications.np_id','>', 90);	
			}
             //$jobApplications = $jobApplications->where('job_applications.np_id',  $request->search_np_id); 
        }
		
		 if ($status > 0 && $status != 'all') {
            	$jobApplications = $jobApplications->where('job_applications.master_status_id', $status);
       	 	}
		
			
		 $jobApplications 		= $jobApplications->groupBy('job_applications.id');
		 $jobApplications 		= $jobApplications->orderBy('job_applications.id', 'DESC');	
		
		$jobApplications 		= $jobApplications->skip($this->filters['start'])->take($this->filters['length'])->get();	
		
		return $jobApplications;
			
    }

    public function headings(): array
    {
        return [
					'ID', 'Fullname', 'Email', 'Secondrary Email ', 'Mobile', 
					'Secondary Mobile', 'Skills', 'Job Title', 'Total Exp', 'Rel Exp', 
					'Passport', 'Current CTC', 'Expected CTC', 'Current Location', 'Current Designation',
					'Current Company', 'Qualification', 'Status', 'Submitted By', 'Client', 'Created at'
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
