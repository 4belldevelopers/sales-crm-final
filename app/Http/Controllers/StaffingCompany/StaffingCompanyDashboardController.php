<?php
/*** ########################################################
Controller    	: 	adminDashboardController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################** */

namespace App\Http\Controllers\StaffingCompany;

use App\Models\InterviewSchedule;
use App\Models\Job;
use App\Models\JobApplication;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Company;
use App\Models\User;

use App\Helper\GeneralMethod;

//use App\InterviewSchedule;

class StaffingCompanyDashboardController extends StaffingCompanyBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageIcon 	= 'icon-speedometer';
        $this->pageTitle 	= 'menu.dashboard';
    }
    public function index()
   	{
				
			
				 				
				$this->salaryMode 			=	GeneralMethod::salaryMode();
				$this->currencySymbole 		=	GeneralMethod::currencySymbole(); 
		
				$company_id			= 	$this->user->roles[0]->company_id;
				$this->admin		=	$this->user->roles[0]->name;
				
				$company_id			=	'';
				$company_id			= auth()->user()->company_id; 
		
				$lastSevenDate		=	date('Y-m-d', strtotime('-7 days'));
				$todayDate			=	date('Y-m-d H:i:s');
				$nextSevenDate		=	date('Y-m-d', strtotime('+7 days'));
				//exit;  
				//$start_date 		=	date("Y-m-d", strtotime($start_date) );
				//$end_date 			=	date("Y-m-d", strtotime($end_date) );
				//$application_data 	=	$application_data->whereBetween('job_applications.created_at', [$start_date, $end_date] );				
				//echo $this->admin; exit;    
				
				
				if($this->admin == 'admin')
				{
				
					// Top Active Tiles 	
										$this->totalOpenings 		=	Job::whereBetween('jobs.created_at', [$lastSevenDate, $todayDate])->where('jobs.status', '=', 'active')->count(); 	
					// Top Total Applicant 			
										$allApplications 			=	JobApplication::join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
																		->whereBetween('job_applications.created_at', [$lastSevenDate, $todayDate])
																		->get();		
										$this->totalApplications 	=	count($allApplications);
							
					//Offered 5
										$this->totalOffered  		=	JobApplication::join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
																		->where('job_applications.master_status_id', '=', 5)
																		->whereBetween('job_applications.updated_at', [$lastSevenDate, $todayDate])
																		->count();	
					//Joined 6 		
										$this->totalJoined  		=	JobApplication::join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
																		 ->where('job_applications.master_status_id', '=', 6)
																		 ->whereBetween('job_applications.updated_at', [$lastSevenDate, $todayDate])
																		 ->count();																				
					//All Requisitions
										$this->jobListing			=	Job::select( 
											'clients.name as clients_name', 'clients.logo_logical_path',
											'jobs.title' , 'jobs.total_positions', DB::raw("count(job_applications.id) as allCandidate"),
											'jobs.status', 'jobs.id', 'jobs.min_exp', 'jobs.max_exp' ,
											
											'jobs.min_salary', 'jobs.max_salary', 'jobs.min_sal_mode', 'jobs.max_sal_mode',
											'jobs.min_salary_currency', 'jobs.max_salary_currency',
										
										);
										$this->jobListing			=	$this->jobListing->join('clients', 'clients.id', 'jobs.client_id');
										$this->jobListing			=	$this->jobListing->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id');
										$this->jobListing			=	$this->jobListing->where('status', '=', 'active');
										//$this->jobListing			=	$this->jobListing->whereBetween('jobs.created_at', [$lastSevenDate, $todayDate]);																	
										$this->jobListing			=	$this->jobListing->take(4);
										$this->jobListing			=	$this->jobListing->groupBy('jobs.id');						
										$this->jobListing			=	$this->jobListing->orderBy('jobs.id', 'DESC');
										$this->jobListing			=	$this->jobListing->get();																	   
							 
							
					//New Joining listing
								$this->newJoining_count 			=	JobApplication::select('job_applications.id', 'job_applications.full_name', 'jobs.title',  'clients.name as clients_name')
																		 ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
																		 ->join('clients', 'clients.id', 'jobs.client_id')
																		 ->where('job_applications.master_status_id', '=', 6)
																		 ->take(4)
																		 ->count();
																		 
								$this->newJoining 					=	JobApplication::select('job_applications.id', 'job_applications.full_name', 'jobs.title',  'clients.name as clients_name', 'job_applications.current_designation', 'users.name as submitted_by_name', 'companies.company_name','job_applications.created_at')
																		 ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
																		 ->join('clients', 'clients.id', 'jobs.client_id')
																		 ->join('users', 'users.id', 'job_applications.submitted_by')
																		 ->join('companies', 'companies.id', 'users.company_id')
																		 ->where('job_applications.master_status_id', '=', 6)
																		 ->take(4)
																		 ->get();																														
					// Interview Schedule     
								$this->intSchedules_count 			= InterviewSchedule::select('interview_schedules.schedule_date', 'job_applications.full_name',  'jobs.title',   'clients.name as clients_name' )
																		//->with(['employee','employee.user'])
																		->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
																		->join('jobs', 'jobs.id', 'job_applications.job_id')
																		->join('clients', 'clients.id', 'jobs.client_id')
																		->whereBetween('interview_schedules.schedule_date', [$todayDate, $nextSevenDate])
																		->take(4)
																		->orderBy('interview_schedules.id', 'DESC')
																		->count();
																		
								 $this->intSchedules 				= InterviewSchedule::select('interview_schedules.schedule_date', 'job_applications.full_name',  'jobs.title',   'clients.name as clients_name', 'job_applications.current_designation', 'interview_mode.name as mode', 'interview_mode_lavel.name as interview_mode_name' )
																		//->with(['employee','employee.user'])
																		->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
																		->join('jobs', 'jobs.id', 'job_applications.job_id')
																		->join('clients', 'clients.id', 'jobs.client_id')
																		->join('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
																		->leftJoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
																		//->whereBetween('interview_schedules.schedule_date', [$todayDate, $nextSevenDate])
																		->take(4)
																		->orderBy('interview_schedules.id', 'DESC')
																		->get();
																		
					//Follow up candidate listing OFFered 
								$this->followupApp_count 			=	JobApplication::select('job_applications.id', 'job_applications.created_at', 'job_applications.full_name', 'jobs.title',  'clients.name as clients_name')
																		 ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
																		 ->join('clients', 'clients.id', 'jobs.client_id')
																		 ->where('job_applications.master_status_id', '=', 5)
																		 //->whereBetween('job_applications.updated_at', [$todayDate, $nextSevenDate])
																		 ->take(4)
																		 ->orderBy('job_applications.id', 'DESC')
																		 ->count();	
																		 
								$this->followupApp 				   =	JobApplication::select('job_applications.id', 'job_applications.created_at', 'job_applications.full_name', 'jobs.title',  'clients.name as clients_name')
																		 ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
																		 ->join('clients', 'clients.id', 'jobs.client_id')
																		 ->where('job_applications.master_status_id', '=', 5)
																		 //->whereBetween('job_applications.updated_at', [$todayDate, $nextSevenDate])
																		 ->take(4)
																		 ->orderBy('job_applications.id', 'DESC')
																		 ->get();	
																		 
																		 
											
											
																		 
									$this->offer 				   =	JobApplication::select('job_applications.id', 'job_applications.full_name', 'jobs.title', 'job_applications.current_designation', 'job_applications.ctc', 'job_applications.ectc', 'master_status.name as master_status_name', 
'clients.name as client_name',
'job_applications.proposed_ctc' , 'job_applications.any_offer' , 'job_applications.document', 'job_applications.sub_for_approval', 'job_applications.offer_approved', 'job_applications.offer_release', 'job_applications.offer_acceptance', 'job_applications.joined', 'job_applications.joined_date', 'job_applications.job_id', 'users.name as submitted_by', 'candidate_status.name as child_status_name', 'companies.company_name'
									
									)
																		
									 ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
									 ->join('clients', 'clients.id', 'jobs.client_id')
									 ->join('master_status', 'master_status.id', 'job_applications.master_status_id')
									 ->leftJoin('candidate_status', 'candidate_status.id', 'job_applications.child_status_id')
									  ->join('users', 'users.id', 'job_applications.submitted_by')
									   ->join('companies', 'companies.id', 'users.company_id')
									 ->where('job_applications.master_status_id', '=', 5)
									 //->whereBetween('job_applications.updated_at', [$todayDate, $nextSevenDate])
									 ->take(4)
									 ->orderBy('job_applications.updated_at', 'DESC')
									 ->get();									 
								
							
							
						
							
							//echo '<pre>'; print_r($this->offer);  exit; 
																				
				}
				else
				{
					
					
						
							// WORKING JOBS	
							$this->totalOpenings 				=   Job:: join('job_assign', 'job_assign.job_id', 'jobs.id')->where('jobs.id', '>', '0')
																		->whereBetween('jobs.created_at', [$lastSevenDate, $todayDate])
																		->where('job_assign.user_id', '=', $this->user->id)
																		->where('jobs.status', '=', 'active')
																		->count();
																		
							
							// ALL ASSIGN ASSIGNMENT																					
							$allApplications 					=	JobApplication::join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
																	->where('job_applications.submitted_by', '=', $this->user->id)
																	->whereBetween('job_applications.created_at', [$lastSevenDate, $todayDate])
																	->get();		
							$this->totalApplications 			=	count($allApplications);	
							
							//OFFERED 5
							$this->totalOffered  				=	JobApplication::join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
																		->where('job_applications.master_status_id', '=', 5)
																		->where('job_applications.submitted_by', '=', $this->user->id)
																		->whereBetween('job_applications.updated_at', [$lastSevenDate, $todayDate])
																		->count();   		
								
							//Joined 6 		
							$this->totalJoined  				=	JobApplication::join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')															 																	 ->whereBetween('job_applications.updated_at', [$lastSevenDate, $todayDate])
																	 ->where('job_applications.submitted_by', '=', $this->user->id)
																	 ->where('job_applications.master_status_id', '=', 6)
																	 ->count();	
																		 										
									
							// Interview Schedule
							$this->intSchedules_count 			= InterviewSchedule::select('interview_schedules.schedule_date', 'job_applications.full_name',  'jobs.title',   'clients.name as clients_name' )
																		//->with(['employee','employee.user'])
																		->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
																		->join('jobs', 'jobs.id', 'job_applications.job_id')
																		->join('clients', 'clients.id', 'jobs.client_id')
																		->whereBetween('interview_schedules.schedule_date', [$todayDate, $nextSevenDate])
																		->where('job_applications.submitted_by', '=', $this->user->id)
																		->take(4)
																		->orderBy('interview_schedules.id', 'DESC')
																		->count();
																		
							$this->intSchedules 				= InterviewSchedule::select('interview_schedules.schedule_date', 'job_applications.full_name',  'jobs.title',   'clients.name as clients_name' )
																		//->with(['employee','employee.user'])
																		->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
																		->join('jobs', 'jobs.id', 'job_applications.job_id')
																		->join('clients', 'clients.id', 'jobs.client_id')
																		->whereBetween('interview_schedules.schedule_date', [$todayDate, $nextSevenDate])
																		->where('job_applications.submitted_by', '=', $this->user->id)
																		->take(4)
																		->orderBy('interview_schedules.id', 'DESC')
																		->get();
																		
																		
							//All Requisitions
							
							$this->jobListing					=	Job::select( 'clients.name as clients_name',  'jobs.title' , 'jobs.total_positions', 										
																		DB::raw("count(job_applications.id) as allCandidate") 										
																	);
										$this->jobListing		=	$this->jobListing->join('clients', 'clients.id', 'jobs.client_id');
										$this->jobListing		=	$this->jobListing->join('job_assign', 'job_assign.job_id', 'jobs.id');
										$this->jobListing		=	$this->jobListing->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id');
										$this->jobListing		=	$this->jobListing->where('status', '=', 'active');
										$this->jobListing		=	$this->jobListing->where('job_assign.user_id', '=', $this->user->id);
										$this->jobListing		=	$this->jobListing->take(4);
										$this->jobListing		=	$this->jobListing->groupBy('jobs.id');						
										$this->jobListing		=	$this->jobListing->orderBy('jobs.id', 'DESC');
										$this->jobListing		=	$this->jobListing->get();																	   
																				
									
							//New Joining listing
								$this->newJoining_count 			=	JobApplication::select('job_applications.id', 'job_applications.full_name', 'jobs.title',  'clients.name as clients_name')
																		 ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
																		 ->join('clients', 'clients.id', 'jobs.client_id')
																		 ->where('job_applications.master_status_id', '=', 6)
																		 ->where('job_applications.submitted_by', '=', $this->user->id)
																		 ->take(4)
																		 ->count();
																		 
								$this->newJoining 					=	JobApplication::select('job_applications.id', 'job_applications.full_name', 'jobs.title',  'clients.name as clients_name')
																		 ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
																		 ->join('clients', 'clients.id', 'jobs.client_id')
																		 ->where('job_applications.master_status_id', '=', 6)
																		 ->where('job_applications.submitted_by', '=', $this->user->id)
																		 ->take(4)
																		 ->get();			
									
														  
						  		$this->followupApp_count 		=	JobApplication::select('job_applications.id', 'job_applications.created_at', 'job_applications.full_name', 'jobs.title',  'clients.name as clients_name')
																		 ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
																		 ->join('clients', 'clients.id', 'jobs.client_id')
																		 ->where('job_applications.master_status_id', '=', 5)
																		 ->where('job_applications.submitted_by', '=', $this->user->id)
																		 ->take(4)
																		 ->orderBy('job_applications.id', 'DESC')
																		 ->count();
																		 
								$this->followupApp 				=	JobApplication::select('job_applications.id', 'job_applications.created_at', 'job_applications.full_name', 'jobs.title',  'clients.name as clients_name')
																		 ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
																		 ->join('clients', 'clients.id', 'jobs.client_id')
																		 ->where('job_applications.master_status_id', '=', 5)
																		 ->where('job_applications.submitted_by', '=', $this->user->id)
																		 ->take(4)
																		 ->orderBy('job_applications.id', 'DESC')
																		 ->get();						
																					
					    
							$this->offer 				   =	JobApplication::select('job_applications.id', 'job_applications.full_name', 'jobs.title', 'job_applications.current_designation', 'job_applications.ctc', 'job_applications.ectc', 'master_status.name', 
'clients.name as client_name',
'job_applications.proposed_ctc' , 'job_applications.any_offer' , 'job_applications.document', 'job_applications.sub_for_approval', 'job_applications.offer_approved', 'job_applications.offer_release', 'job_applications.offer_acceptance', 'job_applications.joined', 'job_applications.joined_date', 'job_applications.job_id'
									
									)
																		
																		 ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
																		 ->join('clients', 'clients.id', 'jobs.client_id')
																		 ->join('master_status', 'master_status.id', 'job_applications.master_status_id')
																		 ->where('job_applications.master_status_id', '=', 6)
																		 ->where('job_applications.submitted_by', '=', $this->user->id)
																		 //->whereBetween('job_applications.updated_at', [$todayDate, $nextSevenDate])
																		 ->take(4)
																		 ->orderBy('job_applications.joined_date', 'DESC')
																		 ->get();	
																		 
																		 
																		 													
						
				}
				
				
					
				$this->totalHired 			= $allApplications->filter(function ($value, $key) {
					return $value->slug  == 'hired';
				})->count();
		
				$this->totalRejected = $allApplications->filter(function ($value, $key) {
					return $value->slug  == 'rejected';
				})->count();
		
				$this->newApplications = $allApplications->filter(function ($value, $key) {
					return $value->slug  == 'applied';
				})->count();
		
				$this->shortlisted = $allApplications->filter(function ($value, $key) {
					return $value->slug  == 'phone screen' || $value->status == 'interview';
				})->count();
				
				$currentDate = Carbon::now(company()->timezone)->format('Y-m-d');
		
				$this->totalTodayInterview = InterviewSchedule::where(DB::raw('DATE(`schedule_date`)'),  "$currentDate")
					->count();
				$this->todoItemsView = $this->generateTodoView();							

				
				return view('staffing_company.dashboard.index', $this->data);
    }
	public function home()
   	{
				
				//echo 	$this->user->last_login; exit; 
			
				//echo '<pre>'; print_r(session()->all());  exit;   
				
		
				$company_id			= 	$this->user->roles[0]->company_id;
				$this->admin		=	$this->user->roles[0]->name;
				
				$lastSevenDate		=	date('Y-m-d', strtotime('-7 days'));
				$todayDate			=	date('Y-m-d H:i:s');
				$nextSevenDate		=	date('Y-m-d', strtotime('+7 days'));
				//exit;  
				//$start_date 		=	date("Y-m-d", strtotime($start_date) );
				//$end_date 			=	date("Y-m-d", strtotime($end_date) );
				//$application_data 	=	$application_data->whereBetween('job_applications.created_at', [$start_date, $end_date] );
				
				//echo $this->admin; exit;    
				
				
				if($this->admin == 'admin')
				{
				
					// Top Active Tiles 	
										$this->totalOpenings 		=	Job::whereBetween('jobs.created_at', [$lastSevenDate, $todayDate])->where('jobs.status', '=', 'active')->count(); 	
					// Top Total Applicant 			
										$allApplications 			=	JobApplication::join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
																		->whereBetween('job_applications.created_at', [$lastSevenDate, $todayDate])
																		->get();		
										$this->totalApplications 	=	count($allApplications);
							
					//Offered 5
										$this->totalOffered  		=	JobApplication::join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
																		->where('job_applications.master_status_id', '=', 5)
																		->whereBetween('job_applications.updated_at', [$lastSevenDate, $todayDate])
																		->count();	
					//Joined 6 		
										$this->totalJoined  		=	JobApplication::join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
																		 ->where('job_applications.master_status_id', '=', 6)
																		 ->whereBetween('job_applications.updated_at', [$lastSevenDate, $todayDate])
																		 ->count();																				
					//All Requisitions
										$this->jobListing			=	Job::select( 'clients.name as clients_name',  'jobs.title' , 'jobs.total_positions', DB::raw("count(job_applications.id) as allCandidate") );
										$this->jobListing			=	$this->jobListing->join('clients', 'clients.id', 'jobs.client_id');
										$this->jobListing			=	$this->jobListing->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id');
										$this->jobListing			=	$this->jobListing->where('status', '=', 'active');
										$this->jobListing			=	$this->jobListing->whereBetween('jobs.created_at', [$lastSevenDate, $todayDate]);																	
										$this->jobListing			=	$this->jobListing->take(4);
										$this->jobListing			=	$this->jobListing->groupBy('jobs.id');						
										$this->jobListing			=	$this->jobListing->orderBy('jobs.id', 'DESC');
										$this->jobListing			=	$this->jobListing->get();																	   
							 
							
					//New Joining listing
								$this->newJoining_count 			=	JobApplication::select('job_applications.id', 'job_applications.full_name', 'jobs.title',  'clients.name as clients_name')
																		 ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
																		 ->join('clients', 'clients.id', 'jobs.client_id')
																		 ->where('job_applications.master_status_id', '=', 6)
																		 ->take(4)
																		 ->count();
																		 
								$this->newJoining 					=	JobApplication::select('job_applications.id', 'job_applications.full_name', 'jobs.title',  'clients.name as clients_name')
																		 ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
																		 ->join('clients', 'clients.id', 'jobs.client_id')
																		 ->where('job_applications.master_status_id', '=', 6)
																		 ->take(4)
																		 ->get();						
																								
					// Interview Schedule
								$this->intSchedules_count 			= InterviewSchedule::select('interview_schedules.schedule_date', 'job_applications.full_name',  'jobs.title',   'clients.name as clients_name' )
																		//->with(['employee','employee.user'])
																		->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
																		->join('jobs', 'jobs.id', 'job_applications.job_id')
																		->join('clients', 'clients.id', 'jobs.client_id')
																		->whereBetween('interview_schedules.schedule_date', [$todayDate, $nextSevenDate])
																		->take(4)
																		->orderBy('interview_schedules.id', 'DESC')
																		->count();
																		
								 $this->intSchedules 				= InterviewSchedule::select('interview_schedules.schedule_date', 'job_applications.full_name',  'jobs.title',   'clients.name as clients_name' )
																		//->with(['employee','employee.user'])
																		->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
																		->join('jobs', 'jobs.id', 'job_applications.job_id')
																		->join('clients', 'clients.id', 'jobs.client_id')
																		->whereBetween('interview_schedules.schedule_date', [$todayDate, $nextSevenDate])
																		->take(4)
																		->orderBy('interview_schedules.id', 'DESC')
																		->get();
																		
					//Follow up candidate listing OFFered 
								$this->followupApp_count 			=	JobApplication::select('job_applications.id', 'job_applications.created_at', 'job_applications.full_name', 'jobs.title',  'clients.name as clients_name')
																		 ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
																		 ->join('clients', 'clients.id', 'jobs.client_id')
																		 ->where('job_applications.master_status_id', '=', 5)
																		 //->whereBetween('job_applications.updated_at', [$todayDate, $nextSevenDate])
																		 ->take(4)
																		 ->orderBy('job_applications.id', 'DESC')
																		 ->count();	
																		 
								$this->followupApp 				   =	JobApplication::select('job_applications.id', 'job_applications.created_at', 'job_applications.full_name', 'jobs.title',  'clients.name as clients_name')
																		 ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
																		 ->join('clients', 'clients.id', 'jobs.client_id')
																		 ->where('job_applications.master_status_id', '=', 5)
																		 //->whereBetween('job_applications.updated_at', [$todayDate, $nextSevenDate])
																		 ->take(4)
																		 ->orderBy('job_applications.id', 'DESC')
																		 ->get();	
																		 
																		 
																		 
									$this->offer 				   =	JobApplication::select('job_applications.id', 'job_applications.full_name', 'jobs.title', 'job_applications.current_designation', 'job_applications.ctc', 'job_applications.ectc', 'master_status.name', 
'clients.name as client_name',
'job_applications.proposed_ctc' , 'job_applications.any_offer' , 'job_applications.document', 'job_applications.sub_for_approval', 'job_applications.offer_approved', 'job_applications.offer_release', 'job_applications.offer_acceptance', 'job_applications.joined', 'job_applications.joined_date', 'job_applications.job_id'
									
									)
																		
																		 ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
																		 ->join('clients', 'clients.id', 'jobs.client_id')
																		 ->join('master_status', 'master_status.id', 'job_applications.master_status_id')
																		 ->where('job_applications.master_status_id', '=', 6)
																		 //->whereBetween('job_applications.updated_at', [$todayDate, $nextSevenDate])
																		 ->take(4)
																		 ->orderBy('job_applications.joined_date', 'DESC')
																		 ->get();									 
								
							
							//echo '<pre>'; print_r($this->offer); 
																				
				}
				else
				{
					
					
						
							// WORKING JOBS	
							$this->totalOpenings 				=   Job:: join('job_assign', 'job_assign.job_id', 'jobs.id')->where('jobs.id', '>', '0')
																		->whereBetween('jobs.created_at', [$lastSevenDate, $todayDate])
																		->where('job_assign.user_id', '=', $this->user->id)
																		->where('jobs.status', '=', 'active')
																		->count();
																		
							
							// ALL ASSIGN ASSIGNMENT																					
							$allApplications 					=	JobApplication::join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
																	->where('job_applications.submitted_by', '=', $this->user->id)
																	->whereBetween('job_applications.created_at', [$lastSevenDate, $todayDate])
																	->get();		
							$this->totalApplications 			=	count($allApplications);	
							
							//OFFERED 5
							$this->totalOffered  				=	JobApplication::join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
																		->where('job_applications.master_status_id', '=', 5)
																		->where('job_applications.submitted_by', '=', $this->user->id)
																		->whereBetween('job_applications.updated_at', [$lastSevenDate, $todayDate])
																		->count();   		
								
							//Joined 6 		
							$this->totalJoined  				=	JobApplication::join('master_status', 'master_status.id', '=', 'job_applications.master_status_id')															 																	 ->whereBetween('job_applications.updated_at', [$lastSevenDate, $todayDate])
																	 ->where('job_applications.submitted_by', '=', $this->user->id)
																	 ->where('job_applications.master_status_id', '=', 6)
																	 ->count();	
																		 										
									
							// Interview Schedule
							$this->intSchedules_count 			= InterviewSchedule::select('interview_schedules.schedule_date', 'job_applications.full_name',  'jobs.title',   'clients.name as clients_name' )
																		//->with(['employee','employee.user'])
																		->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
																		->join('jobs', 'jobs.id', 'job_applications.job_id')
																		->join('clients', 'clients.id', 'jobs.client_id')
																		->whereBetween('interview_schedules.schedule_date', [$todayDate, $nextSevenDate])
																		->where('job_applications.submitted_by', '=', $this->user->id)
																		->take(4)
																		->orderBy('interview_schedules.id', 'DESC')
																		->count();
																		
							$this->intSchedules 				= InterviewSchedule::select('interview_schedules.schedule_date', 'job_applications.full_name',  'jobs.title',   'clients.name as clients_name' )
																		//->with(['employee','employee.user'])
																		->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
																		->join('jobs', 'jobs.id', 'job_applications.job_id')
																		->join('clients', 'clients.id', 'jobs.client_id')
																		->whereBetween('interview_schedules.schedule_date', [$todayDate, $nextSevenDate])
																		->where('job_applications.submitted_by', '=', $this->user->id)
																		->take(4)
																		->orderBy('interview_schedules.id', 'DESC')
																		->get();
																		
																		
							//All Requisitions
							
							$this->jobListing					=	Job::select( 'clients.name as clients_name',  'jobs.title' , 'jobs.total_positions', 										
																		DB::raw("count(job_applications.id) as allCandidate") 										
																	);
										$this->jobListing		=	$this->jobListing->join('clients', 'clients.id', 'jobs.client_id');
										$this->jobListing		=	$this->jobListing->join('job_assign', 'job_assign.job_id', 'jobs.id');
										$this->jobListing		=	$this->jobListing->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id');

										$this->jobListing		=	$this->jobListing->where('status', '=', 'active');
										$this->jobListing		=	$this->jobListing->where('job_assign.user_id', '=', $this->user->id);
										$this->jobListing		=	$this->jobListing->take(4);
										$this->jobListing		=	$this->jobListing->groupBy('jobs.id');						
										$this->jobListing		=	$this->jobListing->orderBy('jobs.id', 'DESC');
										$this->jobListing		=	$this->jobListing->get();																	   
																				
									
							//New Joining listing
								$this->newJoining_count 			=	JobApplication::select('job_applications.id', 'job_applications.full_name', 'jobs.title',  'clients.name as clients_name')
																		 ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
																		 ->join('clients', 'clients.id', 'jobs.client_id')
																		 ->where('job_applications.master_status_id', '=', 6)
																		 ->where('job_applications.submitted_by', '=', $this->user->id)
																		 ->take(4)
																		 ->count();
																		 
								$this->newJoining 					=	JobApplication::select('job_applications.id', 'job_applications.full_name', 'jobs.title',  'clients.name as clients_name')
																		 ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
																		 ->join('clients', 'clients.id', 'jobs.client_id')
																		 ->where('job_applications.master_status_id', '=', 6)
																		 ->where('job_applications.submitted_by', '=', $this->user->id)
																		 ->take(4)
																		 ->get();			
									
														  
						  		$this->followupApp_count 		=	JobApplication::select('job_applications.id', 'job_applications.created_at', 'job_applications.full_name', 'jobs.title',  'clients.name as clients_name')
																		 ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
																		 ->join('clients', 'clients.id', 'jobs.client_id')
																		 ->where('job_applications.master_status_id', '=', 5)
																		 ->where('job_applications.submitted_by', '=', $this->user->id)
																		 ->take(4)
																		 ->orderBy('job_applications.id', 'DESC')
																		 ->count();
																		 
								$this->followupApp 				=	JobApplication::select('job_applications.id', 'job_applications.created_at', 'job_applications.full_name', 'jobs.title',  'clients.name as clients_name')
																		 ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
																		 ->join('clients', 'clients.id', 'jobs.client_id')
																		 ->where('job_applications.master_status_id', '=', 5)
																		 ->where('job_applications.submitted_by', '=', $this->user->id)
																		 ->take(4)
																		 ->orderBy('job_applications.id', 'DESC')
																		 ->get();						
																					
					    
							$this->offer 				   		=	JobApplication::select('job_applications.id', 'job_applications.full_name', 'jobs.title', 'job_applications.current_designation', 'job_applications.ctc', 'job_applications.ectc', 'master_status.name', 
'clients.name as client_name',
'job_applications.proposed_ctc' , 'job_applications.any_offer' , 'job_applications.document', 'job_applications.sub_for_approval', 'job_applications.offer_approved', 'job_applications.offer_release', 'job_applications.offer_acceptance', 'job_applications.joined', 'job_applications.joined_date', 'job_applications.job_id'
									
									)
																		
																		 ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
																		 ->join('clients', 'clients.id', 'jobs.client_id')
																		 ->join('master_status', 'master_status.id', 'job_applications.master_status_id')
																		 ->where('job_applications.master_status_id', '=', 6)
																		 ->where('job_applications.submitted_by', '=', $this->user->id)
																		 //->whereBetween('job_applications.updated_at', [$todayDate, $nextSevenDate])
																		 ->take(4)
																		 ->orderBy('job_applications.joined_date', 'DESC')
																		 ->get();	
																		 
																		 
																		 													
						
				}
				
				
					
				$this->totalHired 			= $allApplications->filter(function ($value, $key) {
					return $value->slug  == 'hired';
				})->count();
		
				$this->totalRejected = $allApplications->filter(function ($value, $key) {
					return $value->slug  == 'rejected';
				})->count();
		
				$this->newApplications = $allApplications->filter(function ($value, $key) {
					return $value->slug  == 'applied';
				})->count();
		
				$this->shortlisted = $allApplications->filter(function ($value, $key) {
					return $value->slug  == 'phone screen' || $value->status == 'interview';
				})->count();
				
				$currentDate = Carbon::now(company()->timezone)->format('Y-m-d');
		
				$this->totalTodayInterview = InterviewSchedule::where(DB::raw('DATE(`schedule_date`)'),  "$currentDate")
					->count();
				$this->todoItemsView = $this->generateTodoView();				
				
				
				return view('staffing_company.dashboard.home', $this->data);
    }

}
