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

class AppTrackerExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
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
		/*$clients		=	'all';
		if($this->filters['clients'] != 'all'){
			$clients		=	$this->filters['clients'];
		}*/
		
												// Jobs 
												$jobs		=	'all';
												if($this->filters['jobs'] != 'all'){
													$jobs			=	$this->filters['jobs'];
												}
												// mstatus_id 
												$master_status_id		=	'all';
												if($this->filters['master_status_id'] != 'all'){
													$master_status_id			=	$this->filters['master_status_id'];
												}
		
		
		
		// Source 
		/*$source		=	'all';
		if($this->filters['source'] != 'all'){
			$source			=	$this->filters['source'];
		}*/
		
		// Passport 
		/*$search_passport		=	'all';
		if($this->filters['search_passport'] != 'all'){
			$search_passport	=	$this->filters['search_passport'];
		}*/
		
		// Passport 
		/*$search_np_id		=	'all';
		if($this->filters['search_np_id'] != 'all'){
			$search_np_id	=	$this->filters['search_np_id'];
		}*/
		
		// Status 
		/*$status		=	'all';
		if($this->filters['status'] != 'all'){
			$status	=	$this->filters['status'];
		}*/
		
		
		
		$chkAdminRole = DB::table('roles')
						->select('*')
						->where('id', $loginUserRole)
						->where('name', 'admin')
						->count(); 
						
         $jobApplications = DB::table('job_applications')->select(		 
																	'job_applications.id',  
																	'master_status.name',
																	//DB::raw('1 as SUB_STAGE'),
																	'job_applications.child_status_id',
																	'job_applications.np_id',
																	'job_applications.full_name',
																	'job_applications.total_exp', 'job_applications.rel_exp',
																	'job_applications.current_location',
																	'job_applications.phone',
																	'job_applications.email',
																	'job_applications.ctc',  'job_applications.ectc','job_applications.current_designation',  'job_applications.current_company',
																	'qualification_master.name as qualification_name', 
																	'clients.name as client_name',
																	'users.name as posted_by',			
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
		 				
		// Filter By JOBS
		if ( ($jobs != 'all' && $this->filters['jobs'] != '')&& ($jobs > 0) ) 
		{
			$jobApplications = $jobApplications->where('job_applications.job_id', $jobs);
		}
		// Filter By master_status_id
		if ( ($master_status_id != 'all' && $this->filters['master_status_id'] != '')&& ($master_status_id > 0) ) 
		{
			$jobApplications = $jobApplications->where('job_applications.master_status_id', $master_status_id);
		}	
			
		 $jobApplications 		= $jobApplications->groupBy('job_applications.id');
		 $jobApplications 		= $jobApplications->orderBy('job_applications.id', 'DESC');			
		 $jobApplications 		= $jobApplications->where('job_applications.company_id', '=', $this->filters['company_id']);		
		 $jobApplications 		= $jobApplications->skip($this->filters['start'])->take($this->filters['length'])->get();	
		
		
		$cnt	=	0;
		if($this->filters['start'] == 0){
			$cnt	=	$this->filters['start']+1;	
		}else{
			$cnt	=	$this->filters['start'];		
		}
					
		foreach($jobApplications as $jval)
		{		
				
				$application_id					=	$jval->id;							
				$jval->id						=	$cnt;
				
				if($jval->child_status_id > 0){
						
						$job_applications		=	DB::table('job_applications')->select('*')->where('job_applications.id', $application_id)->first();	
						
						$ja 					=	DB::table('candidate_status')
													->select('candidate_status.name')
													->where('candidate_status.company_id', $job_applications->company_id)
													->where('candidate_status.id', $job_applications->child_status_id)
													->first();											
						$jval->child_status_id	= $ja->name;
						
						
				}else{
					$jval->child_status_id		= 'None';
				}
		
				if(empty($jval->np_id)){
					$jval->np_id				=	'None';
				} 
				
				
				$cnt							=	$cnt+1; 		
		}		
		
		
		return $jobApplications;
			
    }

    public function headings(): array
    {
        return [
					'SNO.', 'Stage', 'S.Stage', 'NP', 'Applicant', 'EXP', 'R.Exp', 'Loc', 'Phone',
					'Email', 'C.CTC', 'E.CTC', 'C.DESIG', 'C.Company', 'Qualification',
					'Client','Submitted By',					 
					'Date'
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
