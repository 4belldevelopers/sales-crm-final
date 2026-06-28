<?php

/***######################################################## 
Controller    	: 	AdminJobsController
Purpose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/

namespace App\Http\Controllers\StaffingCompany;

use App\Models\ApplicationStatus;
use App\Helper\Reply;
use App\Helper\GeneralMethod;
use App\Http\Requests\StoreJob;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobLocation;
use App\Models\Client;
use App\Http\Requests\UpdateJob;
use App\Models\JobSkill;
use App\Models\Question;
use App\Models\Skill;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Company;
use App\Models\JobApplication;
use App\Notifications\NewJobOpening;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use App\Models\Country;
use App\Exports\JobExport;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;

use App\User;

class StaffingCompanyJobsController extends StaffingCompanyBaseController
{
  public function __construct()
   {
     parent::__construct();
     $this->pageTitle ='menu.jobs';
     $this->pageIcon = 'icon-badge';
   }
   
	public function index()
	{
		
	
		// $this->companies = Company::all();
		$this->totalJobs = Job::where('id', '>', '0')->count();
		$this->activeJobs = Job::where('status', 'active')->count();
		$this->inactiveJobs = Job::where('status', 'inactive')->count();
		$chkAdminRole 				= DB::table('roles')
		->select('roles.id')
		->where('id', $this->user->role->role_id)
		->where('name', 'admin')
		->count(); 
		if($chkAdminRole > 0)
		{
			$this->totalJobs 		= Job::where('id', '>', '0')->count();
			$this->activeJobs 		= Job::where('status', 'active')->count();
			$this->inactiveJobs 	= Job::where('status', 'inactive')->count();
			$this->holdJobs 		= Job::where('status', 'hold')->count();
		}
		else
		{
			$this->totalJobs 		= Job:: join('job_assign', 'job_assign.job_id', 'jobs.id')->where('jobs.id', '>', '0')->where('job_assign.user_id', '=', $this->user->id)->count();
			$this->activeJobs 		= Job:: join('job_assign', 'job_assign.job_id', 'jobs.id')->where('status', 'active')->where('job_assign.user_id', '=', $this->user->id)->count();
			$this->inactiveJobs 	= Job:: join('job_assign', 'job_assign.job_id', 'jobs.id')->where('status' , 'inactive')->where('job_assign.user_id', '=', $this->user->id)->count();
			$this->holdJobs 		= Job:: join('job_assign', 'job_assign.job_id', 'jobs.id')->where('status' , 'hold')->where('job_assign.user_id', '=', $this->user->id)->count();
		}
		return view('staffing_company.jobs.index', $this->data);
	}

	public function create1()
	{

		abort_if(!$this->user->cans('add_jobs'), 403);
		$activeJobsCount = Job::where('status', 'active')->count();
		if($activeJobsCount > 0 && $this->activePackage->package->no_of_job_openings !== null &&$this->activePackage->package->no_of_job_openings <= $activeJobsCount) {
			$this->message = __('modules.subscription.jobPurchase');
			return view('staffing_company.subscription.ask_purchase', $this->data);
		}
		$this->categories 	= JobCategory::all();
		$this->locations 	= JobLocation::all();
		$this->questions 	= Question::all();
		$this->companies 	= Company::all();
		return view('staffing_company.jobs.create', $this->data);

	}
	
	
	public function create()
	{
		
			abort_if(!$this->user->cans('add_jobs'), 403);
			
			//echo $this->company_type; 
			//echo '<pre>'; print_r($this->user);
			//exit; 	 
		
		$company_id      		=	auth()->user()->company_id;
		$activeJobsCount 		=	Job::where('status', 'active')->count();
		if($activeJobsCount > 0 && $this->activePackage->package->no_of_job_openings !== null &&$this->activePackage->package->no_of_job_openings <= $activeJobsCount) {
			$this->message 		=	__('modules.subscription.jobPurchase');
			return view('staffing_company.subscription.ask_purchase', $this->data);
		}
		
		$this->categories  		=	JobCategory::all();
		$this->questions   		=	Question::all();
		$this->companies   		=	Company::all();
		$this->locations   		=	JobLocation::all();
		$this->client   		=	Client::select('*')

		->where('company_id', $company_id)
		->orderBy('clients.id', 'ASC')
		->get();

		$this->skills   		=	Skill::all();
		$this->consultancy  	=	DB::table('users')->select('id', 'name')->where('type', 1)->where('company_id', $company_id)->where('status', 'active')->get();
		$this->team  			=	DB::table('users')->select('id', 'name')->where('type', 0)->where('company_id', $company_id)->where('status', 'active')->get();

		$this->employmentType   =	GeneralMethod::employmentType();
		$this->priority   		=	GeneralMethod::priority();
		$this->qualification 	=	DB::table('qualification_master')
		->select('*')
		->orderBy('qualification_master.display_order', 'ASC')
		->get();		

		//echo $this->user->company_id;
		$this->other_consultancy 	=  DB::table('invitation_consultancy')
		->join('users', 'users.id', '=', 'invitation_consultancy.parent_id')
		->join('companies', 'companies.id', '=', 'users.company_id')
		->select('users.id', 'users.name', 'companies.company_name', 'companies.id as other_company_id')
		->where('invitation_consultancy.company_id', '=', $this->user->company_id)
		->orderBy('users.name', 'ASC')
		->get();			
		
		$this->page_event		=	'ADD';
		$this->currency 		=	GeneralMethod::currency();
		$this->salaryMode 		=	GeneralMethod::salaryMode();
											
		//echo '<pre>'; print_r($this->currencySymbole); exit; 	
		
		
		return view('staffing_company.jobs.create', $this->data);
	}

	public function store(Request $request)
	{
		
			
			abort_if(!$this->user->cans('add_jobs'), 403);
			
			$job 							= new Job();
			$job->slug 						= null;
			$job->company_id 				= $this->user->company_id;
			$job->status 					= 'active';
			
			$job->client_id 				= @$request->client_id;
			$job->title 					= @$request->title;
			$job->category_id 				= @$request->category_id;
			if(!empty(@$request->employment_type))
			{
				$job->employment_type 		= @$request->employment_type;
			}
			if(!empty(@$request->total_positions))
			{
				$job->total_positions 		= @$request->total_positions;
			}			
			if(!empty(@$request->min_exp))
			{
				$job->min_exp 				= @$request->min_exp;
			}
			if(!empty(@$request->max_exp))
			{
				$job->max_exp 				= @$request->max_exp;
			}
			if(!empty(@$request->qualification))
			{
				$job->qualification 		= @$request->qualification;
			}
			$job->start_date 				= date('Y-m-d');
			$job->end_date 					= @$request->end_date;
			$job->priority 					= 0;
			$job->location_id 				= 1;
			$job->meta_details 				= [
												'title' 		=> @$request->meta_title ?: @$request->title,
												'description' 	=> @$request->meta_description ?: strip_tags(Str::substr(html_entity_decode(@$request->job_description), 0, 150))
												];			
			$job->min_salary_currency 		= @$request->min_salary_currency;
			$job->min_salary 				= @$request->minimum_salary;
			$job->min_sal_mode 				= @$request->min_sal_mode;
			
			$job->max_salary_currency 		= @$request->max_salary_currency;
			$job->max_salary 				= @$request->maximum_salary;
			$job->max_sal_mode 				= @$request->max_sal_mode;
			if(!empty(@$request->detail_description))
			{
				$job->job_description 		= @$request->detail_description;
			}		
			$job->carrier_website 			=	@$request->carrier_website;									
			$job->save();
												
			// Insert Job Location
			if (!is_null($request->kt_select2_3)) 
			{
				foreach ($request->kt_select2_3 as $ld) 
				{
					$insertArr = array('company_id' => $this->user->company_id, 'client_location_id' => $ld, 'job_id' => $job->id);
					DB:: table('client_location_jobs')->insert($insertArr);
				}
			}
			// Insert Skills
			if (!empty(@$request->tags)) {
				$tags 			=	@$request->tags;
				$tags 			=	json_decode($tags, true);
				$tagsArr 		=	array_column($tags, 'value');
				foreach($tagsArr as $key=>$value)
				{
					$insertArr = array('company_id' => $this->user->company_id, 'job_id' => $job->id, 'skill_name'=> $value );
					DB:: table('job_metronics_skills')->insert($insertArr);					
					
				}
				
			}
			
			// Job additional detail
				if (!is_null($request->custom_title)) 
				{
					foreach ($request->custom_title as $adArr) 
					{
						if(!empty($adArr))
						{
								$values 	= array(
														'company_id' 	=> $this->user->company_id, 
														'job_id' 		=> $job->id, 
														'detail_value' 	=> $adArr
													);
								DB::table('job_additional_detail')->insert($values);
						}	
					}
				}
				
			//Assign to Team 												
			if (!is_null($request->recruiter)) 
			{
				$recruiterArr	=	'';					
				$recruiterArr	= explode(',', $request->recruiter);
				foreach ($recruiterArr as $recArr) 
				{
					$recRow		=	DB::table('users')->select('users.id')->where('company_id', $this->user->company_id)->where('name', trim($recArr))->first();						
					if(!empty(@$recRow->id) && @$recRow->id > 0)
					{
						$values = array('company_id' => $this->user->company_id, 'user_id' => $recRow->id, 'job_id' => $job->id);
						DB::table('job_assign')->insert($values);	
					}						
				}
			}
			
			// Insert Client Contact Person
			if (!is_null($request->hiring_namager)) 
			{
				$hiringManagerArr	=	'';
				$recruiterArr		=	explode(',', $request->hiring_namager);
				foreach ($recruiterArr as $cp) 
				{
					$recRow			=	DB::table('client_contact_info')->select('client_contact_info.id')->where('client_id', @$request->client_id)->where('contact_person_name', trim($cp))->first();	
					if(!empty(@$recRow->id) && @$recRow->id > 0)
					{				
						$insertArr 		=	array('company_id' => $this->user->company_id, 'client_contact_info_id' => $recRow->id, 'job_id' => $job->id);
						DB:: table('client_contact_info_jobs')->insert($insertArr);
					}
				}
			}
				
			//echo '<pre>'; print_r($_REQUEST);	
			// exit; 
			
			
			
			/*if (is_null($request->job_description)) {
				return Reply::error(__('messages.job_description'));
			}

			if (is_null($request->job_requirement))
			{
				return Reply::error(__('messages.job_requirement'));
			}

			$required_columns = 
								[
									'gender' 	=> false,
									'dob' 		=> false,
									'country' 	=> false
								];

			foreach ($required_columns as $key => $value )
			{
				if ($request->has($key))
				{
					$required_columns[$key] = true;
				}
			}

			$section_visibility = 
									[
										'profile_image' 		=> 'no',
										'resume' 				=> 'no',
										'cover_letter' 			=> 'no',
										'terms_and_conditions' 	=> 'no'
									];

			foreach ($section_visibility as $key => $value )
			{
				if ($request->has($key))
				{
					$section_visibility[$key] = 'yes';
				}
			}*/

			
			
			
			
			//$job->job_description 		= $request->job_description;
			//$job->job_requirement 		= $request->job_requirement;
			// $job->total_positions 		= $request->total_positions;
			
			
			// $job->location_id 			= $request->location_id;
			
			
			//$job->required_columns 		= $required_columns;
			//$job->section_visibility 	= $section_visibility;
			

					
			
			/*if(!empty($request->priority))
			{
				$job->priority 				= $request->priority;
			}*/
			
			
			/*if(!empty($request->min_salary))
			{
				$job->min_salary 			= $request->min_salary;
			}
			if(!empty($request->max_salary))
			{
				$job->max_salary 			= $request->max_salary;
			}*/					
			
			/*if (!is_null($request->skill_id))
			{
				JobSkill::where('job_id', $job->id)->delete();
		
				foreach ($request->skill_id as $skill)
				{
					$jobSkill 				= new JobSkill();
					$jobSkill->skill_id 	= $skill;
					$jobSkill->job_id 		= $job->id;
					$jobSkill->save();
				}
			}*/
			
			
			
			// Assign to consultancy 
			/*if (!is_null($request->consultancy)) 
			{
				foreach ($request->consultancy as $constArr) 
				{
					$values = array('company_id' => $this->user->company_id,	'user_id' => $constArr, 'job_id' => $job->id);
					DB::table('job_assign')->insert($values);
				}
			}*/
			
			
			
			
			
			
				
				
				
				
				
				
				
				// Save Question for job
				//$job->questions()->sync($request->question);
				//echo '<pre>'; print_r($_REQUEST); exit;
				return Reply::redirect(route('staffing_company.jobs.index'), __('menu.jobs') . ' ' . __('messages.createdSuccessfully'));
	}


	public function store_2(StoreJob $request)
	{
			
			abort_if(!$this->user->can('add_jobs'), 403);
			//echo '<pre>'; print_r($_REQUEST); 
			//exit; 
			$job 					= new Job();
			$job->slug 				= null;
			$job->company_id 		= $this->user->company_id;
			$job->status 			= $request->status;
			$job->title 			= $request->title;
			$job->job_description 	= $request->job_description;
			$job->job_requirement 	= $request->job_requirement;
			$job->category_id 		= $request->category_id;
			$job->start_date 		= $request->start_date;
			$job->end_date 			= $request->end_date;
			//$job->location_id 		= $request->location_id;
			$job->location_id 		= 5;
			$job->client_id 		= $request->client_id;
			if(!empty($request->total_positions)){
				$job->total_positions 	= $request->total_positions;
			}
			if(!empty($request->employment_type))
			{
				$job->employment_type 		= $request->employment_type;
			}
			if(!empty($request->priority))
			{
				$job->priority 				= $request->priority;
			}
			if(!empty($request->min_exp))
			{
				$job->min_exp 				= $request->min_exp;
			}
			if(!empty($request->max_exp))
			{
				$job->max_exp 				= $request->max_exp;
			}
			if(!empty($request->min_salary))
			{
				$job->min_salary 			= $request->min_salary;
			}
			if(!empty($request->max_salary))
			{
				$job->max_salary 			= $request->max_salary;
			}
			if(!empty($request->qualification))
			{
				$job->qualification 		= $request->qualification;
			}

			if(!empty($request->detail_description))
			{
				$job->detail_description 		= $request->detail_description;
			}
			$job->save();

			if (!is_null($request->skill_id))
			{
				JobSkill::where('job_id', $job->id)->delete();
				foreach ($request->skill_id as $skill)
				{
					$jobSkill 				= new JobSkill();
					$jobSkill->skill_id 	= $skill;
					$jobSkill->job_id 		= $job->id;
					$jobSkill->save();
				}
			}
			// Insert Job Address 
			if (!is_null($request->contact_person)) 
			{
				foreach ($request->contact_person as $cp) 
				{
					$insertArr = array(
					'company_id' 				=> $this->user->company_id,
					'client_contact_info_id'	=> $cp,
					'job_id' 					=> $job->id
				);
				DB:: table('client_contact_info_jobs')->insert($insertArr);					   
				}
		}
			// Insert reporting User
			if (!is_null($request->location_id)) 
			{
				foreach ($request->location_id as $ld) 
				{
					$insertArr = array(
					'company_id' 			=> $this->user->company_id,
					'client_location_id' 	=> $ld,
					'job_id' 				=> $job->id
				);
				DB:: table('client_location_jobs')->insert($insertArr);	
				}
			}
			// Save Question for job
			if (!is_null($request->question))
			{	
				JobQuestion::where('job_id', $job->id)->delete();
				foreach ($request->question as $question)
				{
				$jobQuestion 				= new JobQuestion();
				$jobQuestion->question_id 	= $question;
				$jobQuestion->job_id 		= $job->id;
				$jobQuestion->save();
				}
			}
			// Assign to consultancy 
				if (!is_null($request->consultancy)) 
				{
					foreach ($request->consultancy as $constArr) 
					{
						$values = array(
						'company_id' 		=> $this->user->company_id,
						'user_id' 		 	=> $constArr, 
						'job_id' 		 	=> $job->id
					);
					DB::table('job_assign')->insert($values);
				}
			}
			// Assign to Team 
			if (!is_null($request->team)) 
			{
				foreach ($request->team as $teamArr) 
				{
					$values = array(
					'company_id' 		=> $this->user->company_id,
					'user_id' 		 	=> $teamArr, 
					'job_id' 		 	=> $job->id
				);
				DB::table('job_assign')->insert($values);
				}
			}
			$id 	= $job->id; 
			//$id 	='';
				if(!empty($id) && $_SERVER['HTTP_HOST'] != '127.0.0.1')
				{
					$company 	= DB::table('companies')->select('*')->where('id', $this->user->company_id)->first();
					$job 		= Job::findOrFail($id);
					$userArr 	=  DB::table('users')
					->join('job_assign', 'job_assign.user_id', '=', 'users.id')
					->where('job_assign.job_id', '=', $id)
					->get()->toArray();
					
					if(!empty($userArr))
					{
						foreach($userArr as $uData)
						{
							$email 		= $name = $company_id = ''; 
							$name 		= $uData->name;
							$email 		= $uData->email;
							$user 		= array('name' => @$uData->name, 'email' => @$uData->email, 'company' => $company->company_name, 'job_title' => @$job->title ); 
							Mail::send('email.job-created', ['user' => $user], function ($message) use ($user)
							{
								$message->from('no-reply@vibhanshujaiswal.com', 'ATS');
								$message->to($user['email'], $user['name']);
								$message->subject('New job has been posted!');
							});
						}
					}

					$adminUserArr =  DB::table('roles')
					->select('users.*')
					->join('role_user', 'role_user.role_id', '=', 'roles.id')
					->join('users', 'users.id', '=', 'role_user.user_id')
					->where('roles.company_id', '=', $this->user->company_id)
					->where('roles.name', '=', 'admin')
					->where('roles.display_name', '=', 'Administrator')
					->where('users.type', '=', 0)
					->get();
					if(!empty($adminUserArr))
					{
						foreach($adminUserArr as $dArr)
						{
							$email 		= $name = $company_id = ''; 
							$name 		= $uData->name;
							$email 		= $uData->email;
							$user 		= array('name' => @$dArr->name, 'email' => @$dArr->email, 'company' => $company->company_name, 'job_title' => @$job->title ); 
							Mail::send('email.job-created', ['user' => $user], function ($message) use ($user)
							{
								$message->from('no-reply@vibhanshujaiswal.com', 'ATS');
								$message->to($user['email'], $user['name']);
								$message->subject('New job has been posted!');
							});
						}
					}
				} 
		
		return Reply::redirect(route('staffing_company.jobs.index'), __('menu.jobs') . ' ' . __('messages.createdSuccessfully'));
	}

	public function show($id)
	{
		$this->job = Job::find($id);
		return $this->job;
	}
	
	public function edit($id)
	{
		abort_if(!$this->user->cans('edit_jobs'), 403);
		
		$this->job 				=	Job::findOrFail($id);
		$this->categories 		=	JobCategory::all();
		$this->locations 		=	JobLocation::all();
		//$this->skills 			=	Skill::where('category_id', $this->job->category_id)->get();
		$this->skills   			=	Skill::all();
		$this->jobQuestion 		=	$this->job->questions->pluck('id')->toArray();
		$this->questions 		=	Question::all();
		$company_id				=	$this->user->company_id;
		
		$this->consultancy  	=	DB::table('users')->select('id', 'name')->where('type', 1)->where('company_id', $company_id)->where('status', 'active')->get();
		
		$this->team  			=	DB::table('users')->select('id', 'name')->where('type', 0)->where('company_id', $company_id)->where('status', 'active')->get();		 
			
		$this->lArr 		=	'';
		$this->uArr 		=	'';
		if($this->job->client_id > 0)
		{
			$this->lArr 	= DB::table('client_location')
			->join('countries', 'countries.id', '=', 'client_location.country_id')
			->join('clients', 'clients.id', '=', 'client_location.client_id')
			->select('client_location.id', 'client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
			->where('client_location.client_id', $this->job->client_id)
			->get();

			$this->uArr 	= DB::table('client_contact_info')
			//->join('countries', 'countries.id', '=', 'client_location.country_id')
			->join('clients', 'clients.id', '=', 'client_contact_info.client_id')
			->select('client_contact_info.id', 'client_contact_info.contact_person_name')
			->where('clients.company_id', $this->job->client_id)
			->get();
		}
		$this->client   		= Client::all();
		$this->employmentType   = GeneralMethod::employmentType();
		$this->priority   		= GeneralMethod::priority();
		$this->locations 		= JobLocation::all();
		$this->qualification 		=	DB::table('qualification_master')
		->select('*')
		->orderBy('qualification_master.display_order', 'ASC')
		->get();
				
		$this->jobAssign  				= DB::table('job_assign')
											->select('user_id')
											->where('company_id', $company_id)
											->where('job_id', $id)
											->get();			
		$this->additionalDetailCount  	= DB::table('job_additional_detail')->select('*')->where('company_id', $company_id)->where('job_id', $id)->count();	
					
		//echo '<pre>'; print_r($this->additionalDetail); exit;   									
		//return view('staffing_company.jobs.edit', $this->data);
		
		// CONTACT PERSON 
		$this->contactPersonList	= DB::table('client_contact_info_jobs')
									->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')
									->select('client_contact_info.contact_person_name')
									->where('client_contact_info_jobs.job_id', $id)
									->get();
		
			
			
		// RECRUITER LIST 
		$this->recruiterlist	= DB::table('job_assign')
									->join('users', 'users.id', '=', 'job_assign.user_id')
									->select('users.name')
									->where('job_assign.company_id', $company_id)
									->where('job_assign.job_id', $id)
									->where('users.type', '=', 0)
									->get();					
							
		$this->additionalDetail  		=	DB::table('job_additional_detail')->select('*')->where('company_id', $company_id)->where('job_id', $id)->get()->toArray();
		$this->job_metronics_skills  	=	DB::table('job_metronics_skills')->select('skill_name')->where('company_id', $company_id)->where('job_id', $id)->get();
		$this->page_event				=	'EDIT';
		$this->currency 				=	GeneralMethod::currency();
		$this->salaryMode 				=	GeneralMethod::salaryMode();
		
		//echo '<pre>'; print_r($this->client_location_jobs); exit; 								
		
		return view('staffing_company.jobs.create', $this->data);
		
	}

	public function update(StoreJob $request, $id)
	{

			abort_if(!$this->user->cans('edit_jobs'), 403);			
			
			$job 							=	Job::findOrFail($id);
			$job->client_id 				=	@$request->client_id;
			$job->title 					=	@$request->title;
			$job->category_id 				=	@$request->category_id;
			if(!empty(@$request->employment_type))
			{
				$job->employment_type 		=	@$request->employment_type;
			}
			if(!empty(@$request->total_positions))
			{
				$job->total_positions 		=	@$request->total_positions;
			}			
			if(!empty(@$request->min_exp))
			{
				$job->min_exp 				=	@$request->min_exp;
			}
			if(!empty(@$request->max_exp))
			{
				$job->max_exp 				=	@$request->max_exp;
			}
			if(!empty(@$request->qualification))
			{
				$job->qualification 		=	@$request->qualification;
			}
			//$job->start_date 				= date('Y-m-d');
			$job->end_date 					=	@$request->end_date;
			$job->priority 					=	0;
			$job->location_id 				=	1;
			$job->meta_details 				=	[
													'title' 		=> @$request->meta_title ?: @$request->title,
													'description' 	=> @$request->meta_description ?: strip_tags(Str::substr(html_entity_decode(@$request->job_description), 0, 150))
												];			
			$job->min_salary_currency 		=	@$request->min_salary_currency;
			$job->min_salary 				=	@$request->minimum_salary;
			$job->min_sal_mode 				=	@$request->min_sal_mode;
			
			$job->max_salary_currency 		=	@$request->max_salary_currency;
			$job->max_salary 				=	@$request->maximum_salary;
			$job->max_sal_mode 				=	@$request->max_sal_mode;
			$job->carrier_website 			=	@$request->carrier_website;
			
			if(!empty(@$request->detail_description))
			{
				$job->job_description 		=	@$request->detail_description;
			}											
			$job->save();
			
			// UPDATE JOB LOCATION
			DB::table('client_location_jobs')->where('job_id', $job->id)->delete();
			if (!is_null($request->kt_select2_3)) 
			{
				foreach ($request->kt_select2_3 as $ld) 
				{
					$insertArr = array('company_id' => $this->user->company_id, 'client_location_id' => $ld, 'job_id' => $job->id);
					DB:: table('client_location_jobs')->insert($insertArr);
				}
			}
			// UPDATE HIRING MANAGER
			DB::table('client_contact_info_jobs')->where('job_id', $job->id)->delete();
			if (!is_null($request->hiring_namager)) 
			{
				$hiringManagerArr	=	'';
				$recruiterArr		=	explode(',', $request->hiring_namager);
				foreach ($recruiterArr as $cp) 
				{
					$recRow			=	DB::table('client_contact_info')->select('client_contact_info.id')->where('client_id', @$request->client_id)->where('contact_person_name', trim($cp))->first();	
					if(!empty(@$recRow->id) && @$recRow->id > 0)
					{				
						$insertArr 		=	array('company_id' => $this->user->company_id, 'client_contact_info_id' => $recRow->id, 'job_id' => $job->id);
						DB:: table('client_contact_info_jobs')->insert($insertArr);
					}
				}
			}
			//UPDATE RECRUITER											
			DB::table('job_assign')->where('job_id', $job->id)->delete();
			if (!is_null($request->recruiter)) 
			{
				$recruiterArr	=	'';					
				$recruiterArr	= explode(',', $request->recruiter);
				foreach ($recruiterArr as $recArr) 
				{
					$recRow		=	DB::table('users')->select('users.id')->where('company_id', $this->user->company_id)->where('name', trim($recArr))->first();						
					if(!empty(@$recRow->id) && @$recRow->id > 0)
					{
						$values = array('company_id' => $this->user->company_id, 'user_id' => $recRow->id, 'job_id' => $job->id);
						DB::table('job_assign')->insert($values);	
					}						
				}
			}			
			// UPDATE  SKILLS
			DB::table('job_metronics_skills')->where('job_id', $job->id)->delete();
			JobSkill::where('job_id', $job->id)->delete();
			if (!empty(@$request->tags)) {				
				$tags 			=	@$request->tags;
				$tags 			=	json_decode($tags, true);
				$tagsArr 		=	array_column($tags, 'value');
				foreach($tagsArr as $key=>$value)
				{
					$insertArr = array('company_id' => $this->user->company_id, 'job_id' => $job->id, 'skill_name'=> $value );
					DB:: table('job_metronics_skills')->insert($insertArr);					
					
				}				
			}				
			// Job ADDITIONAL DETAIL
			DB::table('job_additional_detail')->where('job_id', $job->id)->delete();
			if (!is_null($request->custom_title)) 
			{
				foreach ($request->custom_title as $adArr) 
				{
					if(!empty($adArr))
					{
							$values 	= array(
													'company_id' 	=> $this->user->company_id, 
													'job_id' 		=> $job->id, 
													'detail_value' 	=> $adArr
												);
							DB::table('job_additional_detail')->insert($values);
					}	
				}
			}
			
			
			
			//echo '<pre>'; print_r($_REQUEST); exit; 						
			/*$required_columns =
								[
									'gender' 	=> false,
									'dob' 		=> false,
									'country' 	=> false
								];
			foreach ($required_columns as $key => $value )
				{
					if ($request->has($key))
					{
						$required_columns[$key] = true;
					}
				}*/
			/*$section_visibility =
									[
										'profile_image' 		=> 'no',
										'resume' 				=> 'no',
										'cover_letter' 			=> 'no',
										'terms_and_conditions' 	=> 'no'
									];

			foreach ($section_visibility as $key => $value )
			{
				if ($request->has($key))
				{
					$section_visibility[$key] = 'yes';
				}
			}*/			
			//$job->job_description 		= $request->job_description;
			//$job->job_requirement 		= $request->job_requirement;			
			// $job->location_id 			= $request->location_id;
			//$job->start_date 				= $request->start_date;
			//$job->end_date 				= $request->end_date;
			//$job->status 					= $request->status;
			//$job->required_columns 		= $required_columns;
			//$job->section_visibility 		= $section_visibility;
			/*$job->meta_details 			= 
											[
												'title' 		=> $request->meta_title ?: $job->title,
												'description' 	=> $request->meta_description ?: strip_tags(Str::substr(html_entity_decode($job->job_description), 0, 150))
											];*/
			/*if(!empty($request->priority))
			{
				$job->priority 			= $request->priority;
			}*/		
			/*if(!empty($request->min_salary))
			{
				$job->min_salary 		= $request->min_salary;
			}
			if(!empty($request->max_salary))
			{
				$job->max_salary 		= $request->max_salary;
			}*/
		
			

			/*if(!empty($request->detail_description))
			{
				$job->detail_description = $request->detail_description;
			}*/
			
			
			/*if (!is_null($request->skill_id))
			{
				JobSkill::where('job_id', $job->id)->delete();
				foreach ($request->skill_id as $skill)
				{
					$jobSkill = new JobSkill();
					$jobSkill->skill_id = $skill;
					$jobSkill->job_id = $job->id;
					$jobSkill->save();
				}
			}*/
			
			// Update Question for job
			/*$job->questions()->sync($request->question);
			DB::table('client_contact_info_jobs')->where('job_id', $job->id)->delete();
			// Insert Job Address 
			if (!is_null($request->contact_person)) 
			{
				foreach ($request->contact_person as $cp) 
				{
					$insertArr = array(
					'company_id' 				=> $this->user->company_id,
					'client_contact_info_id'	=> $cp,
					'job_id' 					=> $job->id
					);
					DB:: table('client_contact_info_jobs')->insert($insertArr);					   
				}
			}*/
			/*DB::table('client_location_jobs')->where('job_id', $job->id)->delete();
			// Insert reporting User
			if (!is_null($request->location_id)) 
			{
				foreach ($request->location_id as $ld) 
				{
						$insertArr = array(
					'company_id' 			=> $this->user->company_id,
					'client_location_id' 	=> $ld,
					'job_id' 				=> $job->id
					);
					DB:: table('client_location_jobs')->insert($insertArr);	
				}
			}*/
			
			
			/*if($request->status == 'active')
			{
				DB::table('job_assign')->where('job_id', $job->id)->delete();
				if (!is_null($request->consultancy)) 
				{
					foreach ($request->consultancy as $constArr) 
					{
						$values = array(
						'company_id' 		=> $this->user->company_id,
						'user_id' 		 	=> $constArr, 
						'job_id' 		 	=> $job->id
					);
					DB::table('job_assign')->insert($values);
					}
				}
				if (!is_null($request->team)) 
				{
					foreach ($request->team as $teamArr) 
					{
						$values = array(
						'company_id' 		=> $this->user->company_id,
						'user_id' 		 	=> $teamArr, 
						'job_id' 		 	=> $job->id
					);
					DB::table('job_assign')->insert($values);
					}
				}
			}*/	
					
				
		return Reply::redirect(route('staffing_company.jobs.index'), __('menu.jobs') . ' ' . __('messages.updatedSuccessfully'));

	}
	
	public function destroy($id)
	{
		abort_if(!$this->user->cans('delete_jobs'), 403);
		Job::destroy($id);
		return Reply::success(__('messages.recordDeleted'));
	}
	public function data_old()
	{
		abort_if(!$this->user->cans('view_jobs'), 403);
		$categories = Job::where('id', '>', '0');
		if (\request('filter_company') != "")
		{
			$categories->where('company_id', \request('filter_company'));
		}
		if (\request('filter_status') != "")
		{
			$categories->where('status', \request('filter_status'));
		}
		$categories->get();
		return DataTables::of($categories)
		->addColumn('action', function ($row)
		{
			$params = module_enabled('Subdomain') ? [$row->slug] : [$row->company->career_page_link, $row->slug];
			$action = '';
			if ($this->user->cans('edit_jobs'))
			{
				$action .= '<a href="' . route('staffing_company.jobs.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
				data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
			}
			$action .= ' <a href="javascript:;" class="btn btn-info btn-circle open-url"
			data-toggle="tooltip" data-row-open-url="'.route('jobs.jobDetail', $params).'" data-original-title="' . __('app.copyUrl') . '"><i class="fa fa-copy" aria-hidden="true"></i></a>';

				if ($this->user->cans('delete_jobs'))
				{
					$action .= ' <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
					data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
				}
				if ($row->status == 'expired')
				{
					$action .= ' <a href="javascript:;" class="btn btn-circle expire_modal" style="background: #FF8C00; color: white;"
					data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.refresh') . '"><i class="fa fa-refresh" aria-hidden="true"></i></a>';
				}
				return $action;
			})
			->editColumn('title', function ($row)
			{
				return ucfirst($row->title);
			})
			->editColumn('location_id', function ($row)
			{
				return ucfirst($row->location->location) . ' (' . $row->location->country->country_code . ')';

			})

			->editColumn('start_date', function ($row)
			{
				return $row->start_date->format('d M, Y');
			})

			->editColumn('end_date', function ($row)
			{
				return $row->end_date->format('d M, Y');
			})

			->editColumn('status', function ($row)
			{
				if ($row->status == 'active')
				{
					return '<label class="badge bg-success">' . __('app.active') . '</label>';
				}

				if ($row->status == 'inactive')
				{
					return '<label class="badge bg-danger">' . __('app.inactive') . '</label>';
				}
				if ($row->status == 'expired')
				{
					return '<label class="badge" style="background: #FF8C00;">' . __('app.expired') . '</label>';
				}
			})
			->rawColumns(['status', 'action'])
			->addIndexColumn()
			->make(true);
	}
	
	public function data(Request $request)
	{		
		abort_if(!$this->user->cans('view_jobs'), 403);
		
		$chkAdminRole 		=	DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count(); 		
		$job_status			=	GeneralMethod::job_status();
		$salaryMode 		=	GeneralMethod::salaryMode();
		$currencySymbole 	=	GeneralMethod::currencySymbole(); 
		
		if($chkAdminRole > 0)
		{	
			$categories = Job::select(
										'jobs.id', 'jobs.title', 'jobs.total_positions', 'jobs.priority', 'jobs.status', 'jobs.min_exp', 'jobs.max_exp', 'jobs.created_at', 'jobs.start_date', 'jobs.end_date', 
										'jobs.min_salary', 'jobs.max_salary', 'jobs.min_sal_mode', 'jobs.max_sal_mode',
										
										'jobs.min_salary_currency', 'jobs.max_salary_currency',
										
										'clients.name as clients_name', 'clients.logo_logical_path',
										DB::raw("COUNT(job_applications.id) as allCandidate"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS InReview")
									)
									->join('clients', 'clients.id', 'jobs.client_id')
									->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id')
									->where('jobs.id', '>', '0')
									->take(1);
		}
		else
		{
			$categories = Job::select(
										'jobs.id', 'jobs.title', 'jobs.total_positions', 'jobs.priority', 'jobs.status', 'jobs.min_exp', 'jobs.max_exp', 'jobs.created_at', 'jobs.start_date', 'jobs.end_date',
										'jobs.min_salary', 'jobs.max_salary', 'jobs.min_sal_mode', 'jobs.max_sal_mode',
										
										'jobs.min_salary_currency', 'jobs.max_salary_currency',
										
										'clients.name as clients_name', 'clients.logo_logical_path',
										DB::raw("count(job_applications.id) as allCandidate"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS InReview")
									)
								->join('job_assign', 'job_assign.job_id', 'jobs.id')
								->join('clients', 'clients.id', 'jobs.client_id')
								->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id')
								->where('jobs.id', '>', '0')
								->where('job_assign.user_id', '=', $this->user->id)
								->take(1);		
			$categories->where('job_assign.user_id', $this->user->id); 	
		}
				
		if (\request('filter_company') != "")
		{
			$categories->where('company_id', \request('filter_company'));
		}
		if (\request('filter_status') != "")
		{
			$categories->where('status', \request('filter_status'));
		}
		if (\request('search') != "")
		{
			$search	=	request('search');	
			$categories->where('title', 'LIKE', "%$search%");
		}
		if ($request->client_id != 'all' && $request->client_id != '' ) {
				$categories->where('jobs.client_id', $request->client_id );
		}
		
		$categories->groupBy('jobs.id');
		$categories->orderBy('jobs.id', 'DESC');
		return DataTables::of($categories)
			
			->editColumn('chkbox', function ($row) { 
					return '<div class="form-check form-check-sm form-check-custom form-check-solid">
					<input class="form-check-input" type="checkbox" value="1" />
					</div>';
			})  	
			->editColumn('Requisition', function ($row) { 
			
					$bgTextDecore			=	$strVal	=	'';
					$str					=	trim(@$row->title);
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
					
					$icon	= '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
					<a href="javascript:void(0);" data-row-id="'.$row->id.'" class="show-detail">
						<div class="symbol-label '.$bgTextDecore.' border-radius-5">'.ucwords($strVal).'</div>
					</a>
					</div>';
					
					if(!empty($row->logo_logical_path)){
						$icon	= '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5">
										<a href="javascript:void(0);">
											<div class="symbol-label border-radius-5">
											<img src="'.url('public/'.@$row->logo_logical_path).'" alt="Company Logo" class="border-radius-5 w-100">
											</div>
										</a>
									</div>';
					}	
					
					return $icon.'<div class="d-flex flex-column">
					<a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 show-detail"  data-toggle="tooltip1" title="'.ucwords(@$row->title).'" >'.ucwords(substr(@$row->title, 0, 15)).'</a> 
					<a href="javascript:void(0);" data-row-id="'.$row->id.'" class="fw-bold text-muted d-block fs-7 text-hover-primary show-detail">'.ucwords(substr(@$row->clients_name, 0, 15)).'</a>
					</div>
					'; 
			}) 
			->editColumn('Exp', function ($row) { 
					return '<div class="d-flex flex-column">
					<span class="text-dark fw-bolder fs-6">'.@$row->max_exp.'+</span>
					<span class="text-muted fw-bold text-muted d-block fs-7">'.@$row->min_exp.'+</span>
					</div>';
			}) 
			->editColumn('Salary', function ($row) use($salaryMode, $currencySymbole) {	
			
							
										
						$min_sal_mode	=	$max_sal_mode	=	$min_salary_currency	= $max_salary_currency	=	"";
						$min_sal_mode			=	strtoupper(substr($salaryMode[$row->min_sal_mode], 0, 1)); 
						$max_sal_mode			=	strtoupper(substr($salaryMode[$row->max_sal_mode], 0, 1));
						
						$min_salary_currency	=	$currencySymbole[@$row->min_salary_currency];
						$max_salary_currency	=	$currencySymbole[@$row->max_salary_currency];
						
						$max_salary 			=	"";
						$max_salary 			=	GeneralMethod::thousandsCurrencyFormat($row->max_salary); 
						$min_salary 			=	"";
						$min_salary 			=	GeneralMethod::thousandsCurrencyFormat($row->min_salary); 
						
						
					return '<div class="d-flex flex-column">
					<span class="text-dark fw-bolder fs-6">'.$min_salary_currency.$max_salary.'/'.$min_sal_mode.'</span>                    
					<span class="fw-bold d-block fs-7">'.$min_salary_currency.$min_salary.'/'.$max_sal_mode.'</span>
					</div>';
			}) 
			->editColumn('Location', function ($row) { 
			
						$jobid 		= $row->id;
						$client_loc 	= DB::table('client_location_jobs')
					->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
					->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
					->select(
	'client_location.address', 'client_location.city',  'client_location.postcode', 'client_location.country_id',
	'city_list.city as city_name'
	)
					->where('client_location_jobs.job_id', $jobid)
					->first();
					
					$country_id		=	$country_name = '';	
					$country_id		=	@$client_loc->country_id;
					if(@$country_id > 0){
						$country_name	= GeneralMethod::getCountryName($country_id);	
						foreach($country_name as $cval){
							$country_name 	=  ucwords($cval['sortname']);	
						}
					}
					
					$city				= substr(@$client_loc->city, 0,6); 
					if(@$client_loc->country_id == 101){
						$city			= substr(@$client_loc->city_name, 0, 6);		
					}
											
					return ' <div class="d-flex flex-column">
					<span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="'.ucwords(@$client_loc->address).'" >'.ucwords(substr(@$client_loc->address, 0, 10)).'</span>
					<div class="d-flex align-items-center">
							<span class="fw-bold text-muted d-block fs-7">'.ucwords(substr(@$city, 0, 10)).'</span>
							&nbsp;|&nbsp;
							<span class="fw-bold text-muted d-block fs-7">'.ucwords(substr(@$country_name, 0, 10)).'</span>
						<div>
						</div>
					</div>
					</div>';
			}) 
			
			->editColumn('Status', function ($row) use($job_status) { 
										
					$status 	= $active = $hold = $inactive =	$class	=	 '';
					$status		= ucwords($row->status);
					$class		=	$job_status[$row->status];
					if($status == 'Inactive')
					{
						$status		= 'Closed';
					}
					return '<div class="d-flex flex-column">
						<a href="javascript:void(0);" class="fs-6 '.@$class.' text-hover-success">'.$status.'</a>
						<span class="fw-bold text-muted d-block fs-7">R'.@$row->id.'</span>
					</div>';
			}) 
			->editColumn('Date', function ($row) { 
			
					$total_positions	=	'';
					$total_positions 	=	number_format($row->total_positions);
					return '<div class="d-flex flex-column">
						<span class="text-dark fw-bolder fs-6">'.$row->created_at->format('d M, Y').'</span>
						<span class="text-muted fw-bold d-block fs-7">'.$total_positions.'</span>
					</div>';
			}) 
			->editColumn('HM', function ($row) {
						
						$partnersCount 	=  DB::table('job_assign')
											->join('users', 'users.id', '=', 'job_assign.user_id')
											->select('users.id', 'users.name', 'users.image')
											->where('job_assign.job_id', '=', $row->id)
											->orderBy('users.name', 'ASC')
											->count();						
					$partnersData 	=  DB::table('job_assign')
											->join('users', 'users.id', '=', 'job_assign.user_id')
											->select('users.id', 'users.name', 'users.image')
											->where('job_assign.job_id', '=', $row->id)
											->where('users.type', '=', 0)
											->orderBy('users.name', 'ASC')
											->skip(0)->take(1)->get();
					
					$html			 =	'';
					$html			.=	'<div class="d-flex flex-column">
											<div class="symbol-group symbol-hover">';						
					foreach($partnersData as $pval){
							$imagePath		=	"";
							$imagePath		=	"https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg";
							if(!empty(@$atVal->image)){
								$imagePath	=	url('public/user-uploads/team_profile_img/'.@$pval->image);	
							}
		
							$html			.=	'<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="'.@$pval->name.'">
													<img alt="Pic" src="'.@$imagePath.'">
												</div>';		
					}						
						$html					.=	'<div class="symbol symbol-30 symbol-circle symbol-light" data-bs-placement="top" data-toggle="tooltip2" title="More users list..">
														<span class="symbol-label font-weight-bold">1+</span>
														</div>';					
					$html			.=		'</div>
										</div>';						 
					return $html;
			})
			->editColumn('Partners', function ($row) {
			
					$displayCounter		=	'';
					$partnersCount 		=  DB::table('job_assign')
											->join('users', 'users.id', '=', 'job_assign.user_id')
											->select('users.id', 'users.name', 'users.image')
											->where('users.type', '=', 1)
											->where('job_assign.job_id', '=', $row->id)
											->orderBy('users.name', 'ASC')
											->count();
					$displayCounter		=	$partnersCount.'+';		
					//if($partnersCount	==	0){
					//	$displayCounter		=	$partnersCount; 
					//}
																
					$partnersData 	=  DB::table('job_assign')
											->join('users', 'users.id', '=', 'job_assign.user_id')
											->select('users.id', 'users.name', 'users.image')
											->where('job_assign.job_id', '=', $row->id)
											->where('users.type', '=', 1)
											->orderBy('users.name', 'ASC')
											->skip(0)->take(3)->get();
					
					$html			 =	'';
					$html			.=	'<div class="d-flex flex-column">
											<div class="symbol-group symbol-hover">';						
					foreach($partnersData as $pval){
							$imagePath		=	"";
							$imagePath		=	"https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg";
							if(!empty(@$atVal->image)){
								$imagePath	=	url('public/user-uploads/team_profile_img/'.@$pval->image);	
							}
		
		
	$bgTextDecore			=	$strVal	=	'';
	$str					=	trim( @$pval->name );
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
						
	//$html			.=	'<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="'.@$pval->name.'"><img alt="Pic" src="'.@$imagePath.'"></div>';
	$html			.= '<div class="symbol symbol-30 symbol-circle"data-toggle="tooltip" title="'.@$pval->name.'"> <span class="symbol-label '.$bgTextDecore.'  fw-bolder">'.ucwords($strVal).'</span> </div>';	


						
					}						
						$html					.=	'<div data-row-id="'.$row->id.'"  class="symbol symbol-30 symbol-circle symbol-light spartner-listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list..">
														<span class="symbol-label font-weight-bold">'.$displayCounter.'</span>
														</div>';					
					$html			.=		'</div>
										</div>';						 
					return $html;
					
					
					
					
					
					
			})
			->editColumn('TOT', function ($row) { 
					$applicationCounter 	= number_format(@$row->allCandidate);
					return '<div class="d-flex flex-column">
						<a href="'.url('/staffing_company/apptracker/'.$row->id.'/all').'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$applicationCounter.'</a>
						<a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">'.number_format(@$row->InReview).'</a>
					</div>';
			})
			
			->editColumn('SOU', function ($row) { 
					$master_status_id	=	1; 
					$job_id  			=	'';
					$cnt     			=	0;
					$job_id  			=	$row->id;
					$cnt				=	number_format($row->Sourcing) ;
					if($cnt==0){
						return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
					}else{
						return '<div class="d-flex flex-column"><a href="'.url('/staffing_company/apptracker/'.$row->id.'/'.$master_status_id).'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
					}                   
			}) 
			->editColumn('SCR', function ($row) { 
					$master_status_id	= 2; 
					$job_id  			= '';
					$cnt     			= 0;
					$job_id  			= $row->id;
					$cnt				= number_format($row->Screening) ;
					if($cnt==0){
						$cnt = '<b>.</b>';
						return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
					}else{
						return '<div class="d-flex flex-column"><a href="'.url('/staffing_company/apptracker/'.$row->id.'/'.$master_status_id).'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
					}                   
			}) 
			->editColumn('EVA', function ($row) {
					$master_status_id	=	3;
					$job_id  	= '';
					$cnt     	= 0;
					$job_id  	= $row->id;
					$cnt		= number_format($row->Interview) ;
					if($cnt==0){
						$cnt = '<b>.</b>';
						return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
					}else{
						return '<div class="d-flex flex-column"><a href="'.url('/staffing_company/apptracker/'.$row->id.'/'.$master_status_id).'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';					
					}
			}) 
			->editColumn('NEO', function ($row) { 
					
					$master_status_id	=	4;
					$job_id  			=	'';
					$cnt     			=	0;
					$job_id  			=	$row->id;
					$cnt				=	number_format($row->Negotiation) ;
					if($cnt==0){
						$cnt = '<b>.</b>';
						return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
					}else{
						return '<div class="d-flex flex-column"><a href="'.url('/staffing_company/apptracker/'.$row->id.'/'.$master_status_id).'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
					}
			}) 
			->editColumn('OFF', function ($row) { 
					$master_status_id	=	5;
					$job_id  	= '';
					$cnt     	= 0;
					$job_id  	= $row->id;
					$cnt		= number_format($row->Offered) ;
					if($cnt==0){
						$cnt = '<b>.</b>';
						return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
					}else{
						return '<div class="d-flex flex-column"><a href="'.url('/staffing_company/apptracker/'.$row->id.'/'.$master_status_id).'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
					}
			}) 
			
			->editColumn('JOI', function ($row) { 
					$master_status_id	=	6;
					$job_id  	= '';
					$cnt     	= 0;
					$job_id  	= $row->id;
					$cnt		= number_format($row->Joined) ;
					if($cnt==0){
						$cnt = '<b>.</b>';
						return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
					}else{
						return '<div class="d-flex flex-column"><a href="'.url('/staffing_company/apptracker/'.$row->id.'/'.$master_status_id).'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
					}
			}) 
			->editColumn('REJ', function ($row) { 
					$master_status_id	= 7;
					$job_id  			= '';
					$cnt     			= 0;
					$job_id  			= $row->id;
					$cnt				= number_format($row->Rejected) ;
					if($cnt==0){
						$cnt = '<b>.</b>';
							return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
					}else{
						return '<div class="d-flex flex-column"><a href="'.url('/staffing_company/apptracker/'.$row->id.'/'.$master_status_id).'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
					}      
			}) 
			
			->rawColumns(['chkbox', 'Requisition', 'Exp', 'Salary', 'Location', 'Status', 'Date', 'HM', 'Partners', 'TOT', 'SOU', 'SCR', 'EVA', 'NEO', 'OFF', 'JOI', 'REJ'])
			
			->addIndexColumn()
			->make(true);
	}
	public function data_n(Request $request)
	{		
			
		//abort_if(!$this->user->cans('view_jobs'), 403);
		
		$chkAdminRole 		=	DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count(); 		
		$job_status			=	GeneralMethod::job_status();
		$salaryMode 		=	GeneralMethod::salaryMode();
		$currencySymbole 	=	GeneralMethod::currencySymbole(); 
		
		if($chkAdminRole > 0)
		{	
			$categories = Job::select(
										'jobs.id', 'jobs.title', 'jobs.total_positions', 'jobs.priority', 'jobs.status', 'jobs.min_exp', 'jobs.max_exp', 'jobs.created_at', 'jobs.start_date', 'jobs.end_date', 
										'jobs.min_salary', 'jobs.max_salary', 'jobs.min_sal_mode', 'jobs.max_sal_mode',
										
										'jobs.min_salary_currency', 'jobs.max_salary_currency',
										
										'clients.name as clients_name', 'clients.logo_logical_path',
										DB::raw("COUNT(job_applications.id) as allCandidate"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS InReview")
									)
									->join('clients', 'clients.id', 'jobs.client_id')
									->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id')
									->where('jobs.id', '>', '0')
									->take(1);
		}
		/*else
		{
			$categories = Job::select(
										'jobs.id', 'jobs.title', 'jobs.total_positions', 'jobs.priority', 'jobs.status', 'jobs.min_exp', 'jobs.max_exp', 'jobs.created_at', 'jobs.start_date', 'jobs.end_date',
										'jobs.min_salary', 'jobs.max_salary', 'jobs.min_sal_mode', 'jobs.max_sal_mode',
										
										'jobs.min_salary_currency', 'jobs.max_salary_currency',
										
										'clients.name as clients_name', 'clients.logo_logical_path',
										DB::raw("count(job_applications.id) as allCandidate"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS InReview")
									)
								->join('job_assign', 'job_assign.job_id', 'jobs.id')
								->join('clients', 'clients.id', 'jobs.client_id')
								->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id')
								->where('jobs.id', '>', '0')
								->where('job_assign.user_id', '=', $this->user->id)
								->take(1);		
			$categories->where('job_assign.user_id', $this->user->id); 	
		}*/
				
		if (\request('filter_company') != "")
		{
			$categories->where('company_id', \request('filter_company'));
		}
		if (\request('filter_status') != "")
		{
			$categories->where('status', \request('filter_status'));
		}
		if (\request('search') != "")
		{
			$search	=	request('search');	
			$categories->where('title', 'LIKE', "%$search%");
		}
		if ($request->client_id != 'all' && $request->client_id != '' ) {
				$categories->where('jobs.client_id', $request->client_id );
		}
		
		$categories->groupBy('jobs.id');
		$categories->orderBy('jobs.id', 'DESC');
		return DataTables::of($categories)
			
			->editColumn('chkbox', function ($row) { 
					return '<div class="form-check form-check-sm form-check-custom form-check-solid">
					<input class="form-check-input" type="checkbox" value="1" />
					</div>';
			})  	
			->editColumn('Requisition', function ($row) { 
			
						return '<div class="form-check form-check-sm form-check-custom form-check-solid">
					<input class="form-check-input" type="checkbox" value="1" />
					</div>';
			}) 
			->editColumn('Exp', function ($row) { 
						return '<div class="form-check form-check-sm form-check-custom form-check-solid">
					<input class="form-check-input" type="checkbox" value="1" />
					</div>';
			}) 
			->editColumn('Salary', function ($row) use($salaryMode, $currencySymbole) {	
			
							return '<div class="form-check form-check-sm form-check-custom form-check-solid">
					<input class="form-check-input" type="checkbox" value="1" />
					</div>';
			}) 
			->editColumn('Location', function ($row) { 
			
						return '<div class="form-check form-check-sm form-check-custom form-check-solid">
					<input class="form-check-input" type="checkbox" value="1" />
					</div>';
			}) 
			
			->editColumn('Status', function ($row) use($job_status) { 
										
						return '<div class="form-check form-check-sm form-check-custom form-check-solid">
					<input class="form-check-input" type="checkbox" value="1" />
					</div>';
			}) 
			
			->editColumn('Date', function ($row) use($job_status) { 
										
						return '<div class="form-check form-check-sm form-check-custom form-check-solid">
					<input class="form-check-input" type="checkbox" value="1" />
					</div>';
			}) 
			
			
			->editColumn('HM', function ($row) {
						
						return '<div class="form-check form-check-sm form-check-custom form-check-solid">
					<input class="form-check-input" type="checkbox" value="1" />
					</div>';
			})
			
			->editColumn('Partners', function ($row) {
			
						return '<div class="form-check form-check-sm form-check-custom form-check-solid">
					<input class="form-check-input" type="checkbox" value="1" />
					</div>';
					
					
					
			})
			
			->editColumn('TOT', function ($row) { 
						return '<div class="form-check form-check-sm form-check-custom form-check-solid">
					<input class="form-check-input" type="checkbox" value="1" />
					</div>';
			})
			->editColumn('SOU', function ($row) { 
						return '<div class="form-check form-check-sm form-check-custom form-check-solid">
					<input class="form-check-input" type="checkbox" value="1" />
					</div>';                
			}) 
			->editColumn('SCR', function ($row) { 
						return '<div class="form-check form-check-sm form-check-custom form-check-solid">
					<input class="form-check-input" type="checkbox" value="1" />
					</div>';                 
			}) 
			->editColumn('EVA', function ($row) {
						return '<div class="form-check form-check-sm form-check-custom form-check-solid">
					<input class="form-check-input" type="checkbox" value="1" />
					</div>';
			}) 
			
			->editColumn('NEO', function ($row) { 
					
						return '<div class="form-check form-check-sm form-check-custom form-check-solid">
					<input class="form-check-input" type="checkbox" value="1" />
					</div>';
			}) 
			
			->editColumn('OFF', function ($row) { 
						return '<div class="form-check form-check-sm form-check-custom form-check-solid">
					<input class="form-check-input" type="checkbox" value="1" />
					</div>';
			}) 
			->editColumn('JOI', function ($row) { 
						return '<div class="form-check form-check-sm form-check-custom form-check-solid">
					<input class="form-check-input" type="checkbox" value="1" />
					</div>';
			}) 
			->editColumn('REJ', function ($row) { 
						return '<div class="form-check form-check-sm form-check-custom form-check-solid">
					<input class="form-check-input" type="checkbox" value="1" />
					</div>'; 
			}) 
			
			->rawColumns(['chkbox', 'Requisition', 'Exp', 'Salary', 'Location', 'Status', 'Date', 'HM', 'Partners', 'TOT', 'SOU', 'SCR', 'EVA', 'NEO', 'OFF', 'JOI', 'REJ'])
			
			->addIndexColumn()
			->make(true);
	}
    public function sendEmail(Request $request)
   	{



        abort_if(!$this->user->cans('add_jobs'), 403);







        $this->boardColumns = ApplicationStatus::all();



        $this->locations = JobLocation::all();



        $this->jobs = Job::all();



        $this->skills = Skill::all();







        return view('staffing_company.jobs.send-email', $this->data);



    }
    public function applicationData(Request $request)
	{



        abort_if(!$this->user->cans('view_job_applications'), 403);







        return DataTables::of($this->filterJobApplications($request))



            ->editColumn('full_name', function ($row) {



                return '<a href="javascript:;" class="show-detail" data-widget="control-sidebar" data-slide="true" data-row-id="' . $row->id . '">' . ucwords($row->full_name) . '</a>';



            })



            ->editColumn('title', function ($row) {



                return ucfirst($row->title);



            })



            ->editColumn('location', function ($row) {



                return ucwords($row->location);



            })



            ->editColumn('status', function ($row) {



                return ucwords($row->status);



            })



            ->addColumn('mail_status', function ($row) use ($request){



                return $row->jobs()->where('job_id', $request->jobId)->count() == 0 ? '<label class="badge bg-danger">'.__('modules.newJobEmail.mailNotSent').'</label>' : '<label class="badge bg-success">'.__('modules.newJobEmail.mailSent').'</label>';



            })



            ->addColumn('checkbox', function ($row) {



                return '



                    <div class="checkbox form-check">



                        <input id="' . $row->id . '" type="checkbox" class="form-check-input" >



                        <label for="' . $row->id . '"></label>



                    </div>



                ';



            })



            ->rawColumns(['action', 'resume', 'full_name', 'checkbox', 'mail_status'])



            ->addIndexColumn()



            ->make(true);



    }
    public function sendEmails(Request $request)
	{



        if ($request->allSelected == 'false') {



            if (!$request->has('selectedIds')) {



                return Reply::error(__('messages.selectApplicantsForEmail'));



            }







            $jobApplications = JobApplication::whereIn('id', $request->selectedIds)->with('jobs');



        }



        else {



            $jobApplications = JobApplication::with('jobs');



        }







        // get jobApplication



        $job = Job::findOrFail($request->job_for_email);







        if ($request->excludeSent == 'true') {



            $jobApplicationsCopy = clone $jobApplications;







            $jobApplicationIds = $jobApplicationsCopy->whereHas('jobs', function($q) use ($request){



                $q->where('job_id', $request->job_for_email);



            })->get()->map(function($jobApplication) {



                return $jobApplication->id;



            })->toArray();







            $jobApplications = $jobApplications->whereNotIn('job_applications.id', $jobApplicationIds);



        }







        $jobApplications = $jobApplications->get();







        $jobApplicationIds = $jobApplications->map(function($jobApplication){



            return $jobApplication->id;



        })->toArray();







        $job->applications()->syncWithoutDetaching($jobApplicationIds);







        $uniqueEmailJobs = $jobApplications->unique(function ($job) {



            return $job['email'];



        });







        Notification::send($uniqueEmailJobs, new NewJobOpening($job));







        return Reply::success(__('messages.emailsSentSuccessfully'));







    }
    public function filterJobApplications($request)
	{



        $jobApplications = JobApplication::select('job_applications.id', 'job_applications.full_name', 'job_applications.email', 'jobs.title', 'job_locations.location', 'application_status.status')



            ->with('jobs')



            ->join('jobs', 'jobs.id', 'job_applications.job_id')



            ->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')



            ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')



            ->leftjoin('application_status', 'application_status.id', 'job_applications.status_id')->distinct();







        // Filter by status



        if ($request->status != 'all' && $request->status != '') {



            $jobApplications = $jobApplications->where('job_applications.status_id', $request->status);



        }







        // Filter By jobs



        if ($request->jobs != 'all' && $request->jobs != '') {



            $jobApplications = $jobApplications->where('job_applications.job_id', $request->jobs);



        }







        // Filter By skills



        if ($request->skill != 'all' && $request->skill != '') {



            $jobApplications = $jobApplications->whereIn('job_skills.skill_id',  gettype($request->skill) == 'array' ? $request->skill : explode(',', $request->skill));



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







        // Filter by MailStatus



        if ($request->mailStatus != null && $request->mailStatus != '') {



            if ($request->mailStatus !== 'all') {



                if ($request->mailStatus == 'sent') {



                    $jobApplications = $jobApplications->whereHas('jobs', function($q) use ($request){



                        $q->where('job_id', $request->jobId);



                    });



                }



                else {



                    $jobApplicationsCopy = clone $jobApplications;







                    $jobApplicationIds = $jobApplicationsCopy->whereHas('jobs', function($q) use ($request){



                        $q->where('job_id', $request->jobId);



                    })->get()->map(function($jobApplication) {



                        return $jobApplication->id;



                    })->toArray();







                    $jobApplications = $jobApplications->whereNotIn('job_applications.id', $jobApplicationIds);



                }



            }



        }







        $jobApplications = $jobApplications->groupBy('job_applications.id');







        return $jobApplications;



    }
    //Refresh Expire Date 
    public function refreshDate(UpdateJob $request)
	{



       $job = Job::find($request->id);



        $job->start_date = $request->start_date;



        $job->end_date = $request->end_date;



        $job->status = 'active';



        $job->save();



        return Reply::success(__('messages.updatedSuccessfully'));







    }
	public function getContactPerson($client_id)
	{

			$ccArr 	= '';
			$locArr = '';
			$client_arr 	= DB::table('client_contact_info')->select('*')->where('client_id', $client_id)->get();
			foreach($client_arr as $cArr){
				$ccArr .= '<option  value="'.$cArr->id.'">'.ucwords($cArr->contact_person_name ).'</option>';
			}


		    $locInfo 		= DB::table('client_location')
							//->join('countries', 'countries.id', '=', 'client_location.country_id')
							->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
							->select('client_location.id', 'client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id','city_list.city as city_name')
							->where('client_id', $client_id)
							->get();

			foreach($locInfo as $lArr){

				$city_name		= $lArr->city;
				if($lArr->country_id == 101){
					$city_name		= $lArr->city_name;
				}
			
            $locArr .= '<option   value="'.$lArr->id.'">'.ucwords($lArr->address).'&nbsp;-&nbsp;'.ucwords($city_name).'</option>';

        	}


			
			$job_id		=	@$_REQUEST['job_id'];
			if($_REQUEST['job_id'] > 0){
				$client_location_jobs 		=	DB::table('client_location_jobs')
												->select('client_location_jobs.id', 'client_location_jobs.client_location_id')
												->where('client_location_jobs.job_id', $job_id)
												->get()
												->toArray();	
			}



			$city 		= DB::table('client_location')
							->join('countries', 'countries.id', '=', 'client_location.country_id')
							->select('client_location.id', 'client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
							->where('client_id', $client_id)
							->groupBy('client_location.city')
							->get();
			$clientcity	=	"";
			foreach($city as $arr){		
					
					
				$sel		=	'';	
				if($job_id	 > 0)
				{
					foreach($client_location_jobs as $locVal){
						if($locVal->client_location_id == $arr->id){
							$sel		=	'selected="selected"';
						}
					}
                }
			
				$clientcity .= '<option   value="'.$arr->id.'"  '.@$sel.'>'.ucwords($arr->city).'</option>';
        	}
        	return Reply::dataOnly(['status' => 'success', 'ccArr' => $ccArr, 'locArr' => $locArr, 'clientcity' => $clientcity]);

    }
	public function lightbox($client_id)
	{



			$this->client 		= Client::find($client_id);



			return view('staffing_company.jobs.lightbox', $this->data)->render();



	}
	public function loc_lightbox($client_id)
	{



			$this->client 			= '';



			



			$this->countries 		= Country::all();



			if($client_id > 0)



			{



				$this->client 		= Client::find($client_id);



			}



			else



			{



				$data					=	array();



				$data['id'] 			= 0;



				$data['name'] 			= "Self";	



				$this->client 			= (object) $data;



			}



			return view('staffing_company.jobs.loc_lightbox', $this->data)->render();



	}
	public function editJobStatus($id)
	{



		$this->id = $id;



		if($this->id)



		{



			$this->job  = DB:: table('jobs')->select('*')->where('id', '=', $this->id)->first();



		}



		return view('staffing_company.jobs.status', $this->data)->render();



    }
	public function newUpdateJobstatus(Request $request)
	{



		$company_id			=	'';
		$company_id			= auth()->user()->company_id; 



		



		$job_id 			= $_REQUEST['job_id'];



		$status 			= $_REQUEST['status'];



		



		$data				=	array();



		//$data['job_id']		=	$job_id ;



		$data['status']		=	$status ;



			



		DB::table('jobs')->where('id', @$job_id)->limit(1) ->update($data);



		



		$this->activeJobCounter  	= DB:: table('jobs')->select('id')->where('status', '=', 'active')->where('company_id', '=', $company_id)->count();



		$this->inactiveJobCounter  	= DB:: table('jobs')->select('id')->where('status', '=', 'inactive')->where('company_id', '=', $company_id)->count();



		$this->holdJobCounter  		= DB:: table('jobs')->select('id')->where('status', '=', 'hold')->where('company_id', '=', $company_id)->count();



		



		return Reply::dataOnly(['status' => 'success', 'activeJobs' => $this->activeJobCounter, 'inactiveJobs' => $this->inactiveJobCounter, 'holdJobs'=> $this->holdJobCounter ]);



	



	}
	public function saveClass(Request $request)
	{



		$skillId				=	$company_id   = "";



		$jobid 					=	$request->job_id;



		$skill_name 			=	$request->class_name;



		$job 					=	Job::findOrFail($jobid);



			



		$company_id   			=	auth()->user()->company_id;



		



	



				



		$values 				= array('name' => $skill_name, 'company_id' => $company_id, 'category_id' => $job->category_id);



		DB::table('skills')->insert($values);



		$skillId 				= DB::getPdo()->lastInsertId();



		



		$values 				= array('company_id' => $company_id, 'skill_id' => $skillId, 'job_id' => $jobid);



		DB::table('job_skills')->insert($values);



		$skillId 				= DB::getPdo()->lastInsertId();	



		



		return Reply::dataOnly(['status' => 'success' ]);



        



    }
	public function export($start, $length)
    {
        $filters = [
						'start' 			=>	$start,
						'length' 			=>	$length,
						'loginUserId'		=>	$this->user->id,
						'loginUserRole'		=>	$this->user->role->role_id,
						'company_id'		=>	auth()->user()->company_id
					];

        $data = [
					'company' => $this->user->name 
				];

       	return Excel::download(new JobExport($filters, $data), 'JobExport.xlsx', ExcelExcel::XLSX); 

	}
	public function hiring_manager($id) 
	{
	
		$hiring_namager					=	'';
		$hiring_namager					=	$_REQUEST['hiring_namager'];
		$this->hiring_namagerArr 		=	explode(',', $hiring_namager);
		
		
		
        $this->client_id				=	$id;
		$contPersonArr					=	array();		
		
			
		$clientContactArr			=	DB:: table('client_contact_info')
											->select('client_contact_info.contact_person_designation')
											->where('client_contact_info.client_id', $this->client_id)
											->orderBy('client_contact_info.contact_person_designation', 'ASC')
											->groupBy('client_contact_info.contact_person_designation')
											->get();
																				
		$cnt	=	0;									
		foreach($clientContactArr as $cval)
		{
			//$contPersonArr[$cnt]			=	$cval->contact_person_designation;
			
			$personDetail					=	DB:: table('client_contact_info')
											->select('client_contact_info.id', 'client_contact_info.client_id', 'client_contact_info.contact_person_name', 'client_contact_info.contact_person_email',  'client_contact_info.contact_person_number', 'client_contact_info.contact_person_designation')
											->where('client_contact_info.client_id', $this->client_id)
											->where('client_contact_info.contact_person_designation',$cval->contact_person_designation)
											->orderBy('client_contact_info.contact_person_name', 'ASC')
											->get();
			
			$i=0;
			foreach($personDetail as $pDetail)
			{
				$contPersonArr[$cval->contact_person_designation][$i]['id']							=	$pDetail->id;
				$contPersonArr[$cval->contact_person_designation][$i]['client_id']					=	$pDetail->client_id;
				$contPersonArr[$cval->contact_person_designation][$i]['contact_person_name']		=	$pDetail->contact_person_name;
				$contPersonArr[$cval->contact_person_designation][$i]['contact_person_email']		=	$pDetail->contact_person_email;
				$contPersonArr[$cval->contact_person_designation][$i]['contact_person_number']		=	$pDetail->contact_person_number;
				$contPersonArr[$cval->contact_person_designation][$i]['contact_person_designation']	=	$pDetail->contact_person_designation;	
				$i++;		
			}							
			
			
			
			$cnt++;	
					
		}
		
		$this->contPersonArr		=	$contPersonArr;		
		
		
		$this->assigned					=	DB:: table('client_contact_info')
											->select('client_contact_info.id', 'client_contact_info.client_id', 'client_contact_info.contact_person_name', 'client_contact_info.contact_person_email',  'client_contact_info.contact_person_number', 'client_contact_info.contact_person_designation')
											->where('client_contact_info.client_id', $this->client_id)
											//->where('client_contact_info.contact_person_designation',$cval->contact_person_designation)
											->orderBy('client_contact_info.contact_person_name', 'ASC')
											->get()->toArray();										
							
        $view = view('staffing_company.jobs.hiring_manager', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    	
	}
	public function recruiter($id) 
	{
	
		$recruiter					=	'';
		$recruiter					=	$_REQUEST['recruiter'];
		$this->recruiter 			=	explode(',', $recruiter);
	
        $this->client_id				=	$id;
		$contPersonArr			=	array();		
		
			
		$clientContactArr			=	DB:: table('client_contact_info')
											->select('client_contact_info.contact_person_designation')
											->where('client_contact_info.client_id', $this->client_id)
											->orderBy('client_contact_info.contact_person_designation', 'ASC')
											->groupBy('client_contact_info.contact_person_designation')
											->get();
																				
		$cnt	=	0;									
		foreach($clientContactArr as $cval)
		{
			//$contPersonArr[$cnt]			=	$cval->contact_person_designation;
			
			$personDetail					=	DB:: table('client_contact_info')
											->select('client_contact_info.id', 'client_contact_info.client_id', 'client_contact_info.contact_person_name', 'client_contact_info.contact_person_email',  'client_contact_info.contact_person_number', 'client_contact_info.contact_person_designation')
											->where('client_contact_info.client_id', $this->client_id)
											->where('client_contact_info.contact_person_designation',$cval->contact_person_designation)
											->orderBy('client_contact_info.contact_person_name', 'ASC')
											->get();
			
			$i=0;
			foreach($personDetail as $pDetail)
			{
				$contPersonArr[$cval->contact_person_designation][$i]['id']							=	$pDetail->id;
				$contPersonArr[$cval->contact_person_designation][$i]['client_id']					=	$pDetail->client_id;
				$contPersonArr[$cval->contact_person_designation][$i]['contact_person_name']		=	$pDetail->contact_person_name;
				$contPersonArr[$cval->contact_person_designation][$i]['contact_person_email']		=	$pDetail->contact_person_email;
				$contPersonArr[$cval->contact_person_designation][$i]['contact_person_number']		=	$pDetail->contact_person_number;
				$contPersonArr[$cval->contact_person_designation][$i]['contact_person_designation']	=	$pDetail->contact_person_designation;	
				$i++;		
			}							
			
			
			
			$cnt++;	
					
		}
		
		$this->contPersonArr		=	$contPersonArr;		
		
		
		$this->assigned				=	DB:: table('client_contact_info')
											->select('client_contact_info.id', 'client_contact_info.client_id', 'client_contact_info.contact_person_name', 'client_contact_info.contact_person_email',  'client_contact_info.contact_person_number', 'client_contact_info.contact_person_designation')
											->where('client_contact_info.client_id', $this->client_id)
											//->where('client_contact_info.contact_person_designation',$cval->contact_person_designation)
											->orderBy('client_contact_info.contact_person_name', 'ASC')
											->get()->toArray();										
							
        
		$company_id      			=	auth()->user()->company_id;
		$this->team  				=	DB::table('users')
										->leftjoin('role_user', 'role_user.user_id', 'users.id')
										->leftjoin('roles', 'roles.id', 'role_user.role_id')
										->select('users.id', 'users.name', 'roles.display_name')
										->where('type', 0)
										->where('users.company_id', $company_id)
										->where('status', 'active')
										->get();		
		
		$view = view('staffing_company.jobs.recruiter', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    	
	
	
	}
	public function viewMore($id)
	{
				abort_if(! $this->user->cans('view_jobs'), 403);	
				
				$this->salaryMode 			=	GeneralMethod::salaryMode();
				$this->employmentType   	=	GeneralMethod::employmentType();
				
				$this->job 					=	Job::Select(
					'jobs.id', 'jobs.title', 'jobs.slug', 'jobs.company_id', 'jobs.job_description', 'jobs.job_requirement', 'jobs.total_positions', 'jobs.location_id', 'jobs.category_id',
					'jobs.start_date' , 'jobs.end_date' , 'jobs.status' , 'jobs.required_columns' , 'jobs.meta_details' , 'jobs.section_visibility' , 'jobs.created_at', 'jobs.updated_at',
					 'jobs.priority',  'jobs.client_id',  'jobs.min_exp',  'jobs.max_exp',  'jobs.min_salary',  'jobs.max_salary',  'jobs.qualification',  'jobs.employment_type',
					 'jobs.min_salary_currency', 'jobs.max_salary_currency', 'jobs.min_sal_mode', 'jobs.max_sal_mode', 'jobs.carrier_website',
					 'clients.name as client_name', 'clients.description as client_description', 'clients.logo_logical_path',
					 'job_categories.name as cat_name',
					 'qualification_master.name as qualification_name' 
				)
												->join('clients', 'clients.id', '=', 'jobs.client_id')
												->leftJoin('job_categories', 'job_categories.id', '=', 'jobs.category_id')
												->leftJoin('qualification_master', 'qualification_master.id', '=', 'jobs.qualification')
												->findOrFail($id);
												
				$this->skillArr				=	DB::table('job_metronics_skills')->select('*')->where('job_id', $this->job->id)->get();	
				
				// Company Data 
				$company_arr				=	DB::table('companies')->select('*')->where('id', $this->job->company_id)->first();
				$this->company_name			=	@$company_arr->company_name;
				$this->category_name		=	'N/A';

				$this->jobLocation			=	'';

				if($this->job->id > 0)
				{				
		
		
					$this->jobLocation	=	DB:: table('client_location_jobs')
											->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
											->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
											//->join('countries', 'countries.id', '=', 'client_location.country_id')
											->select('client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
											->where('client_location_jobs.job_id', $this->job->id)
											->orderBy('client_location_jobs.id', 'DESC')
											->first();
		
					$this->jobLocationCount	=	DB:: table('client_location_jobs')
											->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
											->join('countries', 'countries.id', '=', 'client_location.country_id')
											->select('client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
											->where('client_location_jobs.job_id', $this->job->id)
											->orderBy('client_location_jobs.id', 'DESC')
											->count();							
		
				}		

			/*$this->assignTeam				=	DB:: table('job_assign')
											->join('users', 'users.id', '=', 'job_assign.user_id')
											->select('users.id', 'users.name', 'users.email',  'users.image')
											->where('job_assign.job_id', $this->job->id)
											->orderBy('users.name', 'DESC')
											->get();*/
											
			$this->assignTeamCount			=	DB:: table('job_assign')
											->join('users', 'users.id', '=', 'job_assign.user_id')
											->select('users.id', 'users.name', 'users.email')
											->where('job_assign.job_id', $this->job->id)
											->orderBy('users.name', 'DESC')
											->count();								
			
							// RECRUITER LIST 
							$this->recruiterlist	= 	DB::table('job_assign')
										->join('users', 'users.id', '=', 'job_assign.user_id')
										->join('role_user', 'role_user.user_id', 'users.id')
										->join('roles', 'roles.id', 'role_user.role_id')

									//->leftJoin('job_applications', 'job_applications.job_id', '=', 'job_assign.job_id')
									->leftJoin("job_applications",function($join){
									$join->on("job_applications.job_id","=","job_assign.job_id")
									->on("job_applications.submitted_by","=","users.id");
									})
		
										->select('users.id', 'users.name', 'users.email',  'users.image', 'users.mobile','roles.display_name',
										DB::raw("count(job_applications.id) as allCandidate"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview"),
										DB::raw("SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined")
										)
										//->where('job_assign.company_id', $company_id)
										->where('job_assign.job_id', $this->job->id)
										->where('users.type', '=', 0)
										->groupBy('users.id')
										->orderBy('users.name', 'DESC')
										->get();	
									
			$view 				=	view('staffing_company.jobs.show', $this->data)->render();
        	return Reply::dataOnly(['status' => 'success', 'view' => $view]);			

	}
	public function teamdata() 
	{
       
		abort_if(! $this->user->cans('view_team'), 403);	
		
		$company_id			= 	auth()->user()->company_id;
		
		
        $users 		=	User::select('users.id', 'users.name', 'users.email', 'roles.display_name', 'users.created_at', 'users.status', 'users.image');
		$users		=	$users->join('role_user', 'role_user.user_id', 'users.id');
		$users		=	$users->join('roles', 'roles.id', 'role_user.role_id');
		//$users		=	$users->join('clients', 'clients.id', 'jobs.client_id')
		//$users		=	$users->join('master_status', 'master_status.id', 'job_applications.master_status_id')
		$users		=	$users->where('users.company_id', '=', $company_id);
		
		if (\request('search') != "")
		{
			$search		=	request('search');	
			//$users		=	$users->where('users.name', 'LIKE', "%$search%");
		}
				
										
		 return DataTables::of($users)
            
			
			
			
            ->editColumn('name', function ($row) {
                return '<img src='.$row->profile_image_url.' class="img-circle" width="25" /> '.ucwords($row->name);
            })
			->editColumn('email', function ($row) {
                return $row->email;
            })
           
		   ->addColumn('role_name', function ($row) {
                return $row->display_name;
            })
			
		   	->addColumn('action', function ($row) {
                $action = '';

               if($row->id == $this->user->id){
                    return $action;
                }
				
                if( $this->user->cans('edit_team')){
                    $action.= '
                    <a href="' . route('staffing_company.team.edit', [$row->id]) . '" class="btn" data-toggle="tooltip" data-original-title="'.__('app.edit').'">
                      <i class="fa fa-pencil" aria-hidden="true"></i>
                  </a>';
                }

                if( $this->user->cans('delete_team')){
                    $action.= '
                    <a href="javascript:;" class="btn sa-params" data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'">
                        <i class="fa fa-times text-danger" aria-hidden="true"></i>
                    </a>';
                }
                return $action;
            })
			
			
			
			->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid"><input class="form-check-input form-check-input1" type="checkbox" value="1"></div>';
            })
			->editColumn('Name', function ($row) {      
			
            $bgTextDecore 		= $strVal = '';
            $str = trim(@$row->name);
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
           
		    $icon	= '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left" >
                   				 <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="show-detail-team" id="kt_activities3_toggle" >
                      				<div class="symbol-label fs-3 ' . @$bgTextDecore . '  border-radius-5">' . ucwords(@$strVal) . '</div>
                    			</a>
                  		</div>';
				  
				  if(!empty($row->image)){
				  		$imagePath	=	'';
				  		$imagePath	=	url('public/user-uploads/team_profile_img/'.@$row->image);
				  		$icon	= '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5" style="float:left">
                                        <a href="javascript:void(0);">
                                            <div class="symbol-label border-radius-5">
                                            <img src="'.$imagePath.'" alt="User Image" class="border-radius-5 w-100">
                                            </div>
                                        </a>
                                    </div>';
				  }	
					
                   
					
					
					
			$departmentArr		=	'------';	
										  
            return $icon.'<div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="text-dark fw-bolder text-hover-primary fs-6 show-detail-team"  data-toggle="tooltip1" data-html="true" title="' . ucwords(@$row->name) . '">' . ucwords(substr(@$row->name, 0, 15)) . '</a>
                    <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-detail-team">'.@$departmentArr.'</a>
                  </div>
                  ';
        })
		
		->editColumn('Role', function ($row) {
                return '<div class="d-flex flex-column">
                                       <span class="text-dark fw-bolder fs-6">'.ucwords($row->display_name).'</span>
                                    </div>';
            })
			
		->editColumn('Status', function ($row) {
				
				$chk		=	'';
				$status		=	$row->status;
				if($status == 'active'){
					$chk		=	'checked="checked"';			
				}
				
                return ' <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                       <input class="form-check-input chkStatus" type="checkbox" value="active" name="staus" '.@$chk.' onclick="handleClick(this, '.$row->id.');">
                                    </div>';
            })
			
		->editColumn('AddedDate', function ($row) {
		
				$created_at		=	$row->created_at;
				//$currentDateTime = '08/04/2010 22:15:00';
				$strtotime		=	strtotime($created_at);
				$newDate 		=	date('d-M-Y',$strtotime);
				$newDateTime 	=	date('h:i A',$strtotime);

					
                return ' <div class="d-flex flex-column">
                                       <span class="text-dark fw-bolder fs-6">'.@$newDate.'</span>
                                       <span class="text-muted fw-bold d-block fs-7">'.@$newDateTime.'</span>
                                    </div>';
            })
		
		
		
            ->rawColumns(['chkbox', 'Name', 'Role', 'Status', 'AddedDate',  'role_name', 'action'])
            ->addIndexColumn()
            ->make(true);					
								
    }
	public function spartnerlisting($id)
	{
				
				abort_if(! $this->user->cans('view_staffing_partners'), 403);	
				
				$this->salaryMode 			=	GeneralMethod::salaryMode();
				$this->employmentType   	=	GeneralMethod::employmentType();
				
				$this->job 					=	Job::Select(
					'jobs.id', 'jobs.title', 'jobs.slug', 'jobs.company_id', 'jobs.job_description', 'jobs.job_requirement', 'jobs.total_positions', 'jobs.location_id', 'jobs.category_id',
					'jobs.start_date' , 'jobs.end_date' , 'jobs.status' , 'jobs.required_columns' , 'jobs.meta_details' , 'jobs.section_visibility' , 'jobs.created_at', 'jobs.updated_at',
					 'jobs.priority',  'jobs.client_id',  'jobs.min_exp',  'jobs.max_exp',  'jobs.min_salary',  'jobs.max_salary',  'jobs.qualification',  'jobs.employment_type',
					 'jobs.min_salary_currency', 'jobs.max_salary_currency', 'jobs.min_sal_mode', 'jobs.max_sal_mode', 'jobs.carrier_website',
					 'clients.name as client_name', 'clients.description as client_description', 'clients.logo_logical_path',
					 'job_categories.name as cat_name',
					 'qualification_master.name as qualification_name' 
				)
												->join('clients', 'clients.id', '=', 'jobs.client_id')
												->leftJoin('job_categories', 'job_categories.id', '=', 'jobs.category_id')
												->leftJoin('qualification_master', 'qualification_master.id', '=', 'jobs.qualification')
												->findOrFail($id);
												
				$this->skillArr				=	DB::table('job_metronics_skills')->select('*')->where('job_id', $this->job->id)->get();	
				
				// Company Data 
				$company_arr				=	DB::table('companies')->select('*')->where('id', $this->job->company_id)->first();
				$this->company_name			=	@$company_arr->company_name;
				$this->category_name		=	'N/A';

				$this->jobLocation			=	'';

				if($this->job->id > 0)
				{			
		
		
					$this->jobLocation	=	DB:: table('client_location_jobs')
											->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
											->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
											//->join('countries', 'countries.id', '=', 'client_location.country_id')
											->select('client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
											->where('client_location_jobs.job_id', $this->job->id)
											->orderBy('client_location_jobs.id', 'DESC')
											->first();
		
					$this->jobLocationCount	=	DB:: table('client_location_jobs')
											->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
											->join('countries', 'countries.id', '=', 'client_location.country_id')
											->select('client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
											->where('client_location_jobs.job_id', $this->job->id)
											->orderBy('client_location_jobs.id', 'DESC')
											->count();							
		
				}		

			$this->assignTeam				=	DB:: table('job_assign')
											->join('users', 'users.id', '=', 'job_assign.user_id')
											->select('users.id', 'users.name', 'users.email',  'users.image')
											->where('job_assign.job_id', $this->job->id)
											->orderBy('users.name', 'DESC')
											->get();
			$this->assignTeamCount			=	DB:: table('job_assign')
											->join('users', 'users.id', '=', 'job_assign.user_id')
											->select('users.id', 'users.name', 'users.email')
											->where('job_assign.job_id', $this->job->id)
											->orderBy('users.name', 'DESC')
											->count();	
											
											
			$company_id      				=	auth()->user()->company_id;								
			$this->spartnerArr				=	DB:: table('users')
												->leftjoin('job_assign', 'users.id', 'job_assign.user_id')
												//->join('users', 'users.id', '=', 'job_assign.user_id')
												->select('users.id', 'users.name', 'users.email',  'users.image', 'job_assign.job_id as job_assign_id')
												->where('users.company_id', $company_id)
												->where('users.type', 1)
												//->where('job_assign.job_id', $id)
												->orderBy('users.name', 'DESC')
												->get();								
																		
			$this->job_id		=	$id;
			$view 				=	view('staffing_company.jobs.spartnerlisting', $this->data)->render();
        	return Reply::dataOnly(['status' => 'success', 'view' => $view]);			

	}
	public function spartnerAssign(Request $request)
	{
		$company_id      		= auth()->user()->company_id;
		$id 					= $request->id;
		$assign 				= $request->assign;
		$jobid 					= $request->jobid;
		
		if($assign == 1)
		{
			DB::table('job_assign')->where('company_id', $company_id)->where('user_id', $id)->where('job_id', $jobid)->delete(); 
			// INSERT 
			$insertArr = array('company_id' => $company_id, 'user_id' => $id, 'job_id' => $jobid);
			DB:: table('job_assign')->insert($insertArr);
			
		}
		else
		{
			// DELETE
			DB::table('job_assign')->where('company_id', $company_id)->where('user_id', $id)->where('job_id', $jobid)->delete();
		}
		
		
		
	}
	public function all_jobs($id) {
	
        return view('staffing_company.jobs.all_jobs', $this->data);
    }

}
