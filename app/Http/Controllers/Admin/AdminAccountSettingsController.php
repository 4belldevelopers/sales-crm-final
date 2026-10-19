<?php

/*** ########################################################
Controller    	: 	AdminSkillsController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################** */

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Requests\StoreSkill;
use App\Models\JobCategory;
use App\Models\Skill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

use App\Helper\GeneralMethod;

use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Helper\Files;
use App\Models\Client;
use App\Models\Country;
use App\Models\Job;
use App\Models\CandidateStatus;

use App\Models\User;
use Carbon\Carbon;

//use App\Helper\GeneralMethod;


class AdminAccountSettingsController extends AdminBaseController 
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle 	= 'Account Management';
        $this->pageIcon 	= 'icon-grid';
    }


    public function index()
   		 {
		 	//echo '<pre>'; print_r($this->user); exit; 
			
		 	//echo "call"; exit; 
			
			//abort_if(! $this->user->cans('view_skills'), 403);
			
			$this->parentCompanyType	=	GeneralMethod::parentCompanyType();
			$this->companyType			=	GeneralMethod::companyType();
			
			$company_id      			=	auth()->user()->company_id;
			$this->account 				=	DB::table('companies')->select('*')->where('id', $company_id)->first();
			$this->client 				=	Client::select('clients.id', 'clients.tags')->where('clients.company_id', '=', $company_id)->where('clients.self_company', '=', 1)->first();
			//$this->categories  		=	JobCategory::where('', $this->account->)first();
						
// OVERVIEW STAGE 
			
			$this->yearAgo 				=	'';
			if($this->account->founded > 0 )
			{
				$this->yearAgo 			= GeneralMethod::timeAgo($this->account->founded.'-01-01');
			}			
			$this->joined 				=	'';
			if($this->account->created_at > 0 )
			{
				$this->joined 			= GeneralMethod::timeAgo($this->account->created_at);
			}						
			$this->industries_name		=	'';
			if($this->account->industries > 0)
			{
				$industries 			= DB::table('td_industries')										
										->select('td_industries.ind_id', 'td_industries.ind_name')
											->where('td_industries.ind_id', '=', $this->account->industries)
											->orderBy('td_industries.ind_name', 'ASC')
											->first();
				$this->industries_name	=	$industries->ind_name;						
			}			
			// TAGS
			$this->tags					=	DB:: table('company_tags')->select('company_tags.id', 'company_tags.tag_name')->where('company_id', $company_id)->get();			
			$this->activeJobs 			=	Job::where('status', 'active')->count();			
			$this->team  				=	DB::table('users')->select('id', 'name', 'image')->where('type', 0)->where('company_id', $company_id)->where('status', 'active')->take(5)->get();
			$this->team_count  			=	DB::table('users')->select('id', 'name', 'image')->where('type', 0)->where('company_id', $company_id)->where('status', 'active')->count();			
			$this->client_location 		= DB::table('client_location')
										->join('countries', 'countries.id', '=', 'client_location.country_id')
										->select('client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name', 'client_location.country_id', 'client_location.state', 'client_location.city_id')
										->where('client_location.client_id', $this->client->id)
										->orderBy('client_location.id', 'ASC')
										->first();			
			$this->client_location_count 		= DB::table('client_location')
										->join('countries', 'countries.id', '=', 'client_location.country_id')
										->select('client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name', 'client_location.country_id', 'client_location.state')
										->where('client_location.client_id', $this->client->id)
										->orderBy('client_location.id', 'ASC')
										->count();
			
			
		
		
		$this->client_location_count 		= DB::table('client_location')
											//->join('countries', 'countries.id', '=', 'client_location.country_id')
											->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
											->where('client_location.client_id', $this->client->id)
											->orderBy('client_location.id', 'ASC')
											->count();
		$this->client_location 			= DB::table('client_location')
											//->join('countries', 'countries.id', '=', 'client_location.country_id')
											->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
											->where('client_location.client_id', $this->client->id)
											->orderBy('client_location.id', 'ASC')
											->first();
		
	
		$this->company_city_name	=	'';
		$this->company_country_name	=	'';
		if($this->client_location_count > 0)
		{
				if($this->client_location->country_id == 101)
				{
					$this->city_obj 				=	DB::table('city_list')->select('city_list.city')->where('city_list.city_id', $this->client_location->city_id)->first();
					$this->company_city_name		=	ucwords($this->city_obj->city);
				}
				else
				{
					$city							=	$this->client_location->city;
					$this->company_city_name		=	ucwords($city);	
				}
				
				if($this->client_location->country_id > 0){
					$country_name	= GeneralMethod::getCountryName($this->client_location->country_id);	
					foreach($country_name as $cval){
						$this->company_country_name	= ucwords($cval['name']);	
					}
				}
		
		}		
		
		
		
			//echo $this->country_name;
			//echo '<pre>'; print_r($this->client_location); exit; 	
			
// INTERVIEW STAGE
//echo $company_id; exit; 

					
			$this->master_status 			= DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();
			$this->interviewStage_sourcing 	= DB::table('candidate_status')
												->select('*')
												->where('candidate_status.parent', '=', 1)
												->where('candidate_status.company_id', '=', $company_id)
												->orderBy('candidate_status.parent', 'ASC')
												->orderBy('candidate_status.priority', 'ASC')
												->get()->toArray(); 
			
			$this->interviewStage_screening 	= DB::table('candidate_status')
												->select('*')
												->where('candidate_status.parent', '=', 2)
												->where('candidate_status.company_id', '=', $company_id)
												->orderBy('candidate_status.parent', 'ASC')
												->orderBy('candidate_status.priority', 'ASC')
												->get()->toArray();									
			
			 $this->interviewStage_evaluation 	= DB::table('candidate_status')
												->select('*')
												->where('candidate_status.parent', '=', 3)
												->where('candidate_status.company_id', '=', $company_id)
												->orderBy('candidate_status.parent', 'ASC')
												->orderBy('candidate_status.priority', 'ASC')
												->get()->toArray();	
			 $this->interviewStage_negotiation 	= DB::table('candidate_status')
												->select('*')
												->where('candidate_status.parent', '=', 4)
												->where('candidate_status.company_id', '=', $company_id)
												->orderBy('candidate_status.parent', 'ASC')
												->orderBy('candidate_status.priority', 'ASC')
												->get()->toArray();		
			 $this->interviewStage_offered 		= DB::table('candidate_status')
												->select('*')
												->where('candidate_status.parent', '=', 5)
												->where('candidate_status.company_id', '=', $company_id)
												->orderBy('candidate_status.parent', 'ASC')
												->orderBy('candidate_status.priority', 'ASC')
												->get()->toArray();	
			$this->interviewStage_joined 		= DB::table('candidate_status')
												->select('*')
												->where('candidate_status.parent', '=', 6)
												->where('candidate_status.company_id', '=', $company_id)
												->orderBy('candidate_status.parent', 'ASC')
												->orderBy('candidate_status.priority', 'ASC')
												->get()->toArray();																												
			//echo '<pre>'; print_r($this->interviewStage_sourcing); exit; 
			
			
			$this->languagesArr 				=	DB::table('master_languages')->select('*')->orderBy('master_languages.name', 'ASC')->get();
			$this->timezone 					=	DB::table('master_timezone')->select('*')->orderBy('master_timezone.country_name', 'ASC')->get();
			$this->currencyArr 					=	\App\Services\ReferenceDataCache::masterCurrencies();
			$this->time_formatArr 				=	DB::table('master_time_format')->select('*')->orderBy('master_time_format.priority', 'ASC')->get();
			$this->date_formatArr 				=	DB::table('master_date_format')->select('*')->orderBy('master_date_format.priority', 'ASC')->get();
			$this->email_preferencesArr 		=	DB::table('master_email_preferences')->select('*')->orderBy('master_email_preferences.priority', 'ASC')->get();
			$this->master_notificationsArr 		=	DB::table('master_notifications')->select('*')->orderBy('master_notifications.priority', 'ASC')->get();
			//$this->departmentArr 				=	DB::table('job_categories')->select('*')->orderBy('master_notifications.priority', 'ASC')->get();
			$this->departmentArr   				=	DB::table('job_categories')->select('*')->where('company_id', $company_id)->orderBy('job_categories.name', 'ASC')->get(); 
			
												
			
			
										
			return view('admin.account.index', $this->data);
		}
		
   public function edit($id)
   {
   		abort_if(! $this->user->cans('edit_settings'), 403);	
		
		
		//echo $this->global_company_type; 
		//exit; 
		
   		$this->categories  			=	JobCategory::all();
		$this->companyType			=	GeneralMethod::companyType();
   		$company_id      			=	auth()->user()->company_id;
		$this->account 				=	DB::table('companies')->select('*')->where('id', $company_id)->first();
		$this->company_tags 		=	DB::table('company_tags')->select('*')->where('company_id', $company_id)->get();
	
		
		
		$this->client 				= Client::select('clients.id', 'clients.tags')->where('clients.company_id', '=', $company_id)->where('clients.self_company', '=', 1)->first();
		$this->client_location 		= DB::table('client_location')
										->join('countries', 'countries.id', '=', 'client_location.country_id')
										->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'countries.country_name', 'client_location.country_id', 'client_location.state')
										->where('client_location.client_id', $this->client->id)
										->get();
										
		$this->industries 			= DB::table('td_industries')										
										->select('td_industries.ind_id', 'td_industries.ind_name')
										->orderBy('td_industries.ind_name', 'ASC')
										->get();
																		
		
		
		$this->countryArr 			= GeneralMethod::getAllCountryList();		 
	    $this->statelist 			= GeneralMethod::statelist(101);
		
		//echo '<pre>'; print_r($this->client_location); exit; 
		
		
		
		
		$statelist 					=	DB::table('state_list')
										->select('state_list.id', 'state_list.state as name', DB::raw("101 as country_id"))
										->orderBy('state_list.state')
										->get()->toArray();
		$this->indianStatelist       = json_decode(json_encode($statelist), true);
		
		
										
		return view('admin.account.edit', $this->data);
		//return view('admin.profile.editProfile', $this->data);
   }
   
    public function update(Request $request, $id)
    	{	
			
			
        	//abort_if(! $this->user->cans('edit_skills'), 403);
			
			$company_logo_path					=	'';
			$company_id      					=	auth()->user()->company_id;
			$company 							=	Company::findOrFail($company_id);
			if(!empty(@$request->company_name)){
				$company->company_name 			=	@$request->company_name;
			}
			if(!empty(@$request->website)){
				$company->website 				=	@$request->website;
			}
			if(!empty(@$request->industries)){
				$company->industries 			=	@$request->industries;
			}
			if(!empty(@$request->company_size)){
				$company->company_size 			=	@$request->company_size;
			}
			if(!empty(@$request->company_type)){
				$company->company_type 			=	@$request->company_type;
			}
			if(!empty(@$request->founded)){
				$company->founded 				=	@$request->founded;
			}
			if(!empty(@$request->kt_docs_tinymce_hidden)){
				$company->description 			=	htmlentities(@$request->kt_docs_tinymce_hidden, ENT_QUOTES, "UTF-8");
			}
			
			if ($request->hasFile('logo')) {
				$company_logo_path				=	Files::upload($request->logo,'company-logo');
            	$company->logo 					= $company_logo_path;
				//$company_logo_path			=	$company->logo;
        	}
			
						
			
			/*
			if(!empty(@$request->timezone))
			{
				$company->timezone 			=	@$request->timezone;
			}
			if(!empty(@$request->currency))
			{
				$company->currency 			=	@$request->currency;
			}
			if(!empty(@$request->time_format))
			{
				$company->time_format 		=	@$request->time_format;
			}
			if(!empty(@$request->date_format))
			{
				$company->date_format 		=	@$request->date_format;
			}
			
			if(!empty(@$request->google_status) && @$request->google_status == 1){
				$company->google_status 	=	@$request->google_status;
			}else{
				$company->google_status 	=	0;
			}
			if(!empty(@$request->zoom_status) && @$request->zoom_status == 1){
				$company->zoom_status 	=	@$request->zoom_status;
			}else{
				$company->zoom_status 	=	0;
			}
			if(!empty(@$request->microsoft_status) && @$request->microsoft_status == 1){
				$company->microsoft_status 	=	@$request->microsoft_status;
			}else{
				$company->microsoft_status 	=	0;
			}
			
			$company->app_duplicate 				=	@$request->app_duplicate;
			if($company->app_duplicate == 2){
				$company->app_duplicate_days 		=	@$request->app_duplicate_overroll;	
			}
			if($company->app_duplicate == 1){
				$company->app_duplicate_days 		=	@$request->app_duplicate_requisition_wise;	
			}
			if($company->app_duplicate == 0){
				$company->app_duplicate_days 		=	NULL;	
			}
			
			$company->company_type 					=	@$request->company_type;
			$company->acount_type 					=	@$request->acount_type;	
			if(@$request->status == 'inactive'){
				$company->status 					=	@$request->status;
			}else{
				$company->status 					=	'active';
			}
			*/
								
			$company->save();
			
			// Insert Skills
			 $strdata	=	$tagsArr =	"";
			 // Insert Skills
			if (!empty(@$request->tags)) {
            	$tags 			=	@$request->tags;
				$tags 			=	json_decode($tags, true);
				$tagsArr 		=	array_column($tags, 'value');
				$strdata 		=	implode(',', $tagsArr);
				
					 DB::table('clients')
				     ->where('company_id', @$company_id)
				   	 ->where('self_company', 1)
				   	 ->limit(1) 
				  	 ->update(array('tags' => @$strdata));					
        	}
			
			
// THIRD TAB-3
$client_id		=	'';
$client_obj		=	DB::table('clients')->select('id')->where('company_id', $company_id)->where('self_company', 1)->first(); 
$client_id		=	$client_obj->id;	
$addressId 		=	@$request->addressId;
DB::table('client_location')->where('client_id', $client_id)->delete();  
for($i=0; $i<=$addressId; $i++)
{
	$address 			= @$request->address[$i];
	$country 			= @$request->country[$i];
	$state 				= @$request->state[$i];
	$city 				= @$request->city[$i];	
	$postcode 			= @$request->postcode[$i];		
	if(!empty($country) && !empty($address) && !empty($postcode) && !empty($state))
	{
			$city_id 			= $city = '';
			if($country == 101){
				$city_id 			= @$request->city_id[$i];	
			}else{
				$city 				= @$request->city[$i];	
			}			
			$values 				= array(
												'client_id' 	=> $client_id, 
												'country_id' 	=> $country, 
												'city' 			=> $city,
												'city_id' 		=> $city_id, 
												'address' 		=> $address, 
												'postcode' 		=> $postcode, 
												'state' 		=> $state
											);
		DB::table('client_location')->insert($values);	
	}	
}




// UPDATE CLIENT IMAGE 
if($this->global_company_type == 3)
{
		$client_id		=	$client_obj->id;	
		if($client_id > 0)
		{			
				$maxsize 			= 5242880;
				$directoryPath 		= GeneralMethod::document_manage_directory($company_id);	
				$timeStamp 			= time();
		
				if ($request->hasFile('logo'))  
				{
				
				
				
				
					$name = $file_size = $file_type = $temp = $target_file = $logicalpath = '';
					$file_size 		= @$_FILES['logo']['size'];
					$file_type 		= @$_FILES['logo']['type'];
					$temp 			= @$_FILES['logo']['tmp_name'];
					
					$path_parts 	= pathinfo($_FILES['logo']['name']);
					$extension 		= $path_parts['extension'];
					$filename 		= strtolower($path_parts['filename']);
					$altfilename 	= GeneralMethod::getValidUploadFileName($filename) . "-" . $timeStamp . "." . $extension;
					$dirArr 		= explode("/", $directoryPath);
					$target_file 	= $directoryPath . '/' . $altfilename;
					$allowed 		= array('doc', 'docx', 'pdf', 'png', 'jpeg', 'jpg', 'DOC', 'DOCX', 'PDF', 'PNG', 'JPEG', 'JPG');
					$ext 			= pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);          
					
					 
					if (in_array($ext, $allowed)) 
					{
					
					
					   $logicalpath = substr($target_file, strpos($target_file, 'user-uploads'));
					   
					$src = $_SERVER['DOCUMENT_ROOT']."/public/user-uploads/company-logo/".$company_logo_path;      //"/home/www/example.com/source/folders/123456";  // source folder or file
					$dest = $target_file;   																			// destination folder or file        
					copy($src,$dest);
		  
					  //if (move_uploaded_file($temp, $target_file)) {
																
									$client 					= Client::find($client_id);
									$client->logo				= @$altfilename;	
									$client->logo_physical_path	= @$target_file;	
									$client->logo_logical_path	= @$logicalpath;	
									$client->save(); 		
						//}
						
						//echo $target_file;	
						//echo '<pre>'; print_r($_FILES); exit;					
					}						 
				}		
		}
}





					
			//echo $strdata;		
			//echo '<pre>'; print_r($_REQUEST); exit; 						
        	//return Reply::redirect(route('admin.account.index'), __('menu.skills').' '.__('messages.updatedSuccessfully'));
			return Reply::dataOnly(['status' => 'success']);   

}
	
   
   
    public function addIntStage(Request $request)
	{
	
			$company_id      	=	auth()->user()->company_id;	
			$parent				=	$request->ltb_stage;
			$name				=	$request->ltb_stage_name;
			$ltb_class			=	$request->ltb_class;
			
			$priority			=	0;
			$rowDataCount		=	DB::table('candidate_status')
									->select('*')
									->where('candidate_status.company_id', $company_id)
									->where('candidate_status.parent', $parent)
									->orderBy('candidate_status.priority', 'DESC')
									->count();
			
			//DB::table('client_file')->select('*')->where('id', $id)->first(); 
			if($rowDataCount > 0)
			{
				$rowData			=	DB::table('candidate_status')->select('priority')->where('company_id', $company_id)->where('parent', $parent)->orderBy('priority', 'DESC')->first();	
				$priority			=	$rowData->priority;
				$priority++;
			}
			//echo '<pre>'; print_r($rowDataCount); exit; 
			//$lastInsertId 		=	0;
			$values 			=	array('company_id' => $company_id, 'parent' => $parent, 'name' => $name, 'priority' => $priority);
			DB::table('candidate_status')->insert($values);	
			$lastInsertId 		=	DB::getPdo()->lastInsertId();
			
			$stageHtml			=	'<li id="'.@$lastInsertId.'" class="int_block kanban-item d-flex align-items-center justify-content-between '.@$ltb_class.' ui-sortable-handle"> <span class="fw-bold">'.$name.'</span>
  <div class="edit_delete cursor-pointer">
  <a class="stage_block text-danger delete_process" href="javascript:void(0);" rel="'.@$lastInsertId.'" ><span class="svg-icon svg-icon-danger svg-icon-2x" >
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <rect x="0" y="0" width="24" height="24"></rect>
        <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"></path>
        <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path>
      </g>
    </svg>
    </span> </a> </div>
</li>';			
	
		$message	=	'<div class="alert alert-success" id="success-alert">
			<button type="button" class="close bg-transparent border-0 fw-bold" data-dismiss="alert">x</button>
			<strong>Success! </strong> New Process have added successfully.
			</div>';
			return Reply::dataOnly(['status' => 'success', 'stageHtml' => $stageHtml, 'message'=> $message]);   
		}
   
   
   
   
    public function delIntStage(Request $request)
	{
	
			$block						=	'';
			$company_id      			=	auth()->user()->company_id;				
			$block 						=	@$request->block;
			
			if($block > 0)
			{
				DB::table('candidate_status')->where('company_id', $company_id)->where('id', $block)->delete();
			}
			
			
			
				
			$message			=	'<div class="alert alert-success" id="success-alert"><button type="button" class="close bg-transparent border-0 fw-bold" data-dismiss="alert">x</button><strong>Success! </strong> New Process have added successfully.</div>';
		return Reply::dataOnly(['status' => 'success', 'message'=> $message]);
	}
   
   
   public function updatePriority(Request $request)
	{
	
			$block						=	'';
			$company_id      			=	auth()->user()->company_id;				
			$data 						=	@$request->data;
			
			if(!empty($data))
			{
				$i=0;
				foreach($data as $val)
				{
					if($val > 0)
					{
							DB::table('candidate_status')
							->where('company_id', $company_id)
							->where('id', $val)
							->limit(1) 
							->update(array('priority' => $i));
							$i++;  
					 
					}	
				}
			}
			
					
			
			
				
			$message			=	'<div class="alert alert-success" id="success-alert"><button type="button" class="close bg-transparent border-0 fw-bold" data-dismiss="alert">x</button><strong>Success! </strong> New Process have added successfully.</div>';
		return Reply::dataOnly(['status' => 'success', 'message'=> $message]);
	}
	
   
   
   
   
   public function data() 
	{
       // abort_if(! $this->user->cans('view_team'), 403);
		
		$company_id			= 	auth()->user()->company_id;		
		
		
        $users 				=	User::select('users.id', 'users.name', 'users.email', 'roles.display_name');
		$users				=	$users->join('role_user', 'role_user.user_id', 'users.id');
		$users				=	$users->join('roles', 'roles.id', 'role_user.role_id');
		$users				=	$users->where('users.company_id', '=', $company_id);
		
		/*if (\request('search') != "")
		{
			$search		=	request('search');	
			$users		=	$users->where('users.name', 'LIKE', "%$search%");
		}*/
														
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
                    <a href="' . route('admin.team.edit', [$row->id]) . '" class="btn" data-toggle="tooltip" data-original-title="'.__('app.edit').'">
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
			
            ->rawColumns(['name', 'email', 'role_name', 'action'])
            ->addIndexColumn()
            ->make(true);					
								
    }
	
	
   public function storeDept(Request $request)
	{
		
			$company_id      	=	auth()->user()->company_id;	
			$dept_name			=	$request->dept_name;
			$values 			=	array('company_id' => $company_id, 'name' => $dept_name);
			DB::table('job_categories')->insert($values);	
		
			$message	=	'<div class="alert alert-success" id="success-alert">
							  <button type="button" class="close bg-transparent border-0 fw-bold" data-dismiss="alert">x</button>
							  <strong>Success! </strong> New Process have added successfully.
							</div>';
			
			
			$departmentArr   	=	DB::table('job_categories')->select('*')->where('company_id', $company_id)->orderBy('job_categories.name', 'ASC')->get(); 
			$html	=	'';		
		    $cnt	=	0;
		    if(!empty($departmentArr)){
			foreach($departmentArr as $dval){
												if($cnt %3==0)
												{
													$html	.=	'<div class="form-group fv-plugins-icon-container has-success">
																	<div class="row">';					
												}
												$cnt++;
												$html	.=	'<div class="col-lg-4">
																						   <div class="badge badge-secondary d-flex align-items-center justify-content-between py-4 px-5" data-class="secondary">
																							  <div class="fs-4">'.$dval->name.'</div>
																							  <div class="edit_delete cursor-pointer">													  
										 <a class="text-primary me-2 d-none" href="javascript:void(0);">
																									<span class="svg-icon svg-icon-primary">                           
																									   <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																										  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																											 <rect x="0" y="0" width="24" height="24"></rect>
																											 <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "></path>
																											 <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"></rect>
																										  </g>
																									   </svg>
																									</span>Edit
																								 </a>                        
																								
																								 
																								 <!--begin::Delete Icon-->
																								 <a class="text-danger dept_delete" href="javascript:void(0);" rel="'.$dval->id.'">
																									<span class="svg-icon svg-icon-danger svg-icon-2x">
																									   <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																										  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																											 <rect x="0" y="0" width="24" height="24"></rect>
																											 <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"></path>
																											 <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path>
																										  </g>
																									   </svg>
																									</span>
																									<!-- Delete -->
																								 </a>          
																								 <!--end::Delete Icon-->
																							  </div>
																						   </div>
																						</div>';
												if($cnt %3==0)
												{										
													$html	.=	'	</div>
																	<div class="form-check"></div>
																	<div class="form-text text-muted"></div>
																 </div>';
												}			 
			}
		}			 
					 						
			return Reply::dataOnly(['status' => 'success', 'html'=> $html , 'message'=> $message]);   
			
	}
	

	public function destroy($id)
    	{
			$company_id      	=	auth()->user()->company_id;	
			//abort_if(! $this->user->cans('delete_category'), 403);
	
			JobCategory::destroy($id);
			
			
			$departmentArr   	=	DB::table('job_categories')->select('*')->where('company_id', $company_id)->orderBy('job_categories.name', 'ASC')->get(); 
			$html	=	'';		
		    $cnt	=	0;
		    if(!empty($departmentArr)){
			foreach($departmentArr as $dval){
												if($cnt %3==0)
												{
													$html	.=	'<div class="form-group fv-plugins-icon-container has-success">
																	<div class="row">';					
												}
												$cnt++;
												$html	.=	'<div class="col-lg-4">
																						   <div class="badge badge-secondary d-flex align-items-center justify-content-between py-4 px-5" data-class="secondary">
																							  <div class="fs-4">'.$dval->name.'</div>
																							  <div class="edit_delete cursor-pointer">													  
										 <a class="text-primary me-2 d-none" href="javascript:void(0);">
																									<span class="svg-icon svg-icon-primary">                           
																									   <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																										  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																											 <rect x="0" y="0" width="24" height="24"></rect>
																											 <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "></path>
																											 <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"></rect>
																										  </g>
																									   </svg>
																									</span>Edit
																								 </a>                        
																								
																								 
																								 <!--begin::Delete Icon-->
																								 <a class="text-danger dept_delete" href="javascript:void(0);" rel="'.$dval->id.'">
																									<span class="svg-icon svg-icon-danger svg-icon-2x">
																									   <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																										  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																											 <rect x="0" y="0" width="24" height="24"></rect>
																											 <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"></path>
																											 <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path>
																										  </g>
																									   </svg>
																									</span>
																									<!-- Delete -->
																								 </a>          
																								 <!--end::Delete Icon-->
																							  </div>
																						   </div>
																						</div>';
												if($cnt %3==0)
												{										
													$html	.=	'	</div>
																	<div class="form-check"></div>
																	<div class="form-text text-muted"></div>
																 </div>';
												}			 
			}
		}		
		
			//return Reply::success(__('messages.recordDeleted'));
			return Reply::dataOnly(['status' => 'success', 'html'=> $html]);
						
    	}
	
	
	
	
	public function google($id)
    	{			
			$company_id      			=	auth()->user()->company_id;
			$this->company  			=	DB::table('companies')->select('*')->where('id', $company_id)->first();
			
			$view 						=	view('admin.account.google', $this->data)->render();
        	return Reply::dataOnly(['status' => 'success', 'view' => $view]);								
    	}
		
		
	
	public function zoom($id)
    	{			
			$company_id      			=	auth()->user()->company_id;
			$this->company  			=	DB::table('companies')->select('*')->where('id', $company_id)->first();
			
			$view 						=	view('admin.account.zoom', $this->data)->render();
        	return Reply::dataOnly(['status' => 'success', 'view' => $view]);								
    	}
	
	
	public function videoMeetingSeetingUpdate(Request $request)
		{
				$company_id   			=	$SER_NAME	=	"";
				$company_id      		=	auth()->user()->company_id;						
				$SER_NAME 				=	$request->SER_NAME;
							
				if($SER_NAME == "GOOGLE")
				{
					$google_status	= $google_client_id	= $google_client_secret	=	"";				
					$google_client_id 			=	$request->google_client_id;
					$google_client_secret 		=	$request->google_client_secret;				
					$update						=	array(
															'google_client_id' 		=>  @$google_client_id,
															'google_client_secret' 	=>  @$google_client_secret,
															'google_verify'			=>	0
														 );							
					DB::table('companies')->where('id', $company_id)->limit(1) ->update($update);   
					return Reply::dataOnly(['status' => 'success']);
				}				
				
				if($SER_NAME == "ZOOM")
				{
					$zoom_email	= $zoom_api_key	= $zoom_api_secret	=	"";				
					$zoom_email 				=	$request->zoom_email;
					$zoom_api_key 				=	$request->zoom_api_key;	
					$zoom_api_secret 			=	$request->zoom_api_secret;				
					$update						=	array(
															'zoom_email' 		=> @$zoom_email,
															'zoom_api_key' 		=> @$zoom_api_key,
															'zoom_api_secret' 	=> @$zoom_api_secret
														 );							
					DB::table('companies')->where('id', $company_id)->limit(1) ->update($update);   
					return Reply::dataOnly(['status' => 'success']);
				}	
							
				
		}	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
    public function create()
    	{
        abort_if(! $this->user->cans('add_skills'), 403);

        $this->categories = JobCategory::all();
        return view('admin.skills.create', $this->data);
    }
  
    public function store(Request $request)
    	{
        abort_if(! $this->user->cans('add_skills'), 403);

        $names = $request->name;
        //$categoryId = $request->category_id;

        if (trim($names[0]) == '') {
            return Reply::error(__('errors.addSkills'));
        }

        foreach ($names as $name) {
            if (is_null($name)) {
                return Reply::error(__('errors.addSkills'));
            }
        }

        foreach ($names as $key => $name):
            if(!is_null($name)){
                Skill::create(['name' => $name ]);
            }
        endforeach;

        return Reply::redirect(route('admin.skills.index'), __('menu.skills').' '.__('messages.createdSuccessfully'));
    }
  
    public function show($id)
    {
        //
    }
   
   /* public function edit($id)
    	{
			echo "call"; 
			
       /* abort_if(! $this->user->cans('edit_skills'), 403);

        $this->categories = JobCategory::all();
        $this->skill = Skill::find($id);
        return view('admin.skills.edit', $this->data);*/
   /* }*/
   
   
   
   /* public function destroy($id)
		{
        abort_if(! $this->user->cans('delete_skills'), 403);

        Skill::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }*/

    public function data1() 
		{
		
			abort_if(! $this->user->cans('view_skills'), 403);
	
			//$categories 	= Skill::all();
			
			$categories 				= Skill::select('skills.id', 'skills.name', 'skills.category_id', 'job_categories.name as cat_name' );
			$categories 				= $categories->leftjoin('job_categories', 'job_categories.id', 'skills.category_id');
			
			if (\request('search') != "")
			{
				$search					=	request('search');	
				$categories				=	$categories->where('skills.name', 'LIKE', "%$search%");
			}  
			  
			$categories 				= $categories->groupBy('skills.id');		
			$categories 				= $categories->orderBy('skills.id', 'DESC');



        return DataTables::of($categories)
            ->addColumn('action', function ($row) {
                $action = '';

                if( $this->user->cans('edit_skills'))
                {
                    $action.= '
                    <a href="'. route('admin.skills.edit', [$row->id]) . '"class="btn"
                      data-toggle="tooltip" data-original-title="'.__('app.edit').'">
                        <i class="fa fa-pencil text-primary"></i>
                    </a>';
                }   

                if( $this->user->cans('delete_skills'))
                {
                    $action.= '
                    <a href="javascript:void();" class="btn sa-params" data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'">
                        <i class="fa fa-times text-danger"></i>
                    </a>';
                }
                return $action;
            })
            ->editColumn('name', function ($row) {
                return ucfirst($row->name);
            })
			
			 ->editColumn('category_id', function ($row) {
                return ucfirst($row->cat_name);
            })
			
           /* ->editColumn('category_id', function ($row) {
                return ucfirst($row->category->name);
            })*/
			
            ->addIndexColumn()
            ->make(true);
    }	
	
	public function addMoreSkill($id)
		{
			//echo $id; exit; 
			//$this->client 		= Client::find($client_id);
			return view('admin.skills.lightbox', $this->data)->render();
	}
	
	public function saveMoreSkills(Request $request)
		{

		$name 			= '';
		$name 			= $request->name;
		
		if($name !='' )
		{
				
				$skillCounter 		= DB::table('skills')->select('*')->where('company_id', $this->user->company_id)->where('name', $name)->count();
				
				if($skillCounter == 0)
				{
						$insertArr 		= array('company_id' => $this->user->company_id, 'name' => $name);
						
						DB::table('skills')->insert($insertArr); 
						$insertedId	= DB::getPdo()->lastInsertId();
						if($insertedId > 0)
						{
							$option 	= array('id' => $insertedId , 'name' => ucwords($name));
							return Reply::dataOnly(['status' => 'success', 'message' => "Skills added successfully.", 'option'=> $option ]);		
						}
						else
						{
							return Reply::dataOnly(['status' => 'error', 'message' => "Please enter skills name."]);
						}
				}
				else
				{
					return Reply::dataOnly(['status' => 'error', 'message' => "Already exists job skills."]);	
				}			
		}
		else
		{
			return Reply::dataOnly(['status' => 'error', 'message' => "Please enter skills name."]);
		}
		
		
	}
	
	

	
	/*public function add_team_member_form()
    	{			
			$company_id      			=	auth()->user()->company_id;
			$this->company  			=	DB::table('companies')->select('*')->where('id', $company_id)->first();			
			//$this->roles   			=	DB::table('roles')->select('*')->where('company_id', $company_id)->orderBy('roles.display_name', 'ASC')->get();		
			
			$view 						=	view('admin.account.add_team_member_form', $this->data)->render();
        	return Reply::dataOnly(['status' => 'success', 'view' => $view]);								
    	}*/
		
	public function add_team_member_form($id)
    	{			
			$company_id      			=	auth()->user()->company_id;
			$this->company  			=	DB::table('companies')->select('*')->where('id', $company_id)->first();
			$this->roles   				=	DB::table('roles')->select('*')->where('company_id', $company_id)->orderBy('roles.display_name', 'ASC')->get();		
			//$view 						=	view('admin.account.zoom', $this->data)->render();
			$view 						=	view('admin.account.add_team_member_form', $this->data)->render();
        	return Reply::dataOnly(['status' => 'success', 'view' => $view]);								
    	}	
	
	

}
