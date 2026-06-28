<?php

/***########################################################
Controller    	: 	AdminProfileController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################** */

namespace App\Http\Controllers\Admin;

use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\UpdateProfile;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

use App\Models\JobCategory;
use App\Helper\GeneralMethod;

use App\Helper\Google;


class AdminProfileController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'menu.myProfile';
        $this->pageIcon = 'ti-user';
    }

    public function index()
		{
		
			$this->countryName	=	'';
			$company_id			= 	$this->user->roles[0]->company_id;
			$this->admin		=	$this->user->roles[0]->name;
			$this->user_id 		=	$this->user->id;
			
			$this->users 		= User::join('companies', 'companies.id', '=', 'users.company_id')
								->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')	
								->leftJoin('city_list', 'city_list.city_id', '=', 'users_profile.city_id')	
								->leftJoin('state_list', 'state_list.id', '=', 'users_profile.state')				
								->select(
											'users.id', 'users.company_id', 'users.name', 'users.email', 'users.mobile', 'users.created_at', 'users.image', 'users.status', 
											'companies.company_name', 'companies.website', 'companies.zoom_status', 'companies.google_status', 'companies.microsoft_status', 'companies.company_size',
											'users_profile.country', 'users_profile.state', 'users_profile.city', 'users_profile.designation', 'users_profile.skills',
											
											'city_list.city as city_name',
											'state_list.state as state_name'
										)
								->with('company')
								->where('users.id', '=', $this->user_id)
								->company()
								->first();
			
				
			if($this->users->country > 0)
			{
				$countryArr 		= GeneralMethod::getCountryName($this->users->country);	
				foreach($countryArr as $key=>$val)
				{
					$this->countryName	= $val['name']; 
				}
			}
					
        	$this->calling_codes = $this->getCallingCodes();
        	return view('admin.profile.index', $this->data);
    	}

	  public function edit()
		{
			
				$id = $this->user->id;
			
        		//abort_if(! $this->user->cans('edit_team'), 403);

        		/*if($id == $this->user->id){
            									abort(403);
        			}*/
       		 	//$this->roles = Role::all();
       			// $this->team = User::find($id);        
		
				
				$this->statelist 		=	array();
				$this->team 			=	User::join('companies', 'companies.id', '=', 'users.company_id')
											->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')						
											->select(
														'users.id', 'users.company_id', 'users.name', 'users.email', 'users.mobile', 'users.created_at', 'users.image', 'users.status', 'users.image', 
														'companies.company_name', 'companies.website',
														'users_profile.country', 'users_profile.state', 'users_profile.city', 'users_profile.city_id', 'users_profile.designation', 'users_profile.skills'  
													)
											->with('company')
											->where('users.id', '=', $id)
											->company()
											->first();
				
				$this->categories 		= JobCategory::all();
				$this->companyType		= GeneralMethod::companyType();
				$this->department		= GeneralMethod::department();
				$this->countryArr 		= GeneralMethod::getAllCountryList();	
				if($this->team->country > 0){
					$this->statelist 		= GeneralMethod::statelist($this->team->country);	
				}	 
				
				$this->cityListArr       			=	'';			
				if($this->team->country == 101) 
				{
					$statelist 					=	DB::table('state_list')
													->select('state_list.id', 'state_list.state as name', DB::raw("101 as country_id"))
													->orderBy('state_list.state')
													->get()->toArray();
					$this->statelist       		= 	json_decode(json_encode($statelist), true);	
								
					$citylist 					=	DB::table('city_list')
													->select('city_list.city_id as id', 'city_list.city as name')
													->where('city_list.state_id', $this->team->state)
													->orderBy('city_list.city')
													->get()->toArray();
					$this->cityListArr       		= json_decode(json_encode($citylist), true);
					
				}
		
						
    			return view('admin.profile.edit', $this->data);
		
		
		}
	





    public function update_old(UpdateProfile $request)
		{
			//echo '<pre>'; print_r($_REQUEST); exit; 
			
        $user 						= User::find($this->user->id);
        $user->name 				= $request->name;
        $user->email 				= $request->email;
		
		
        if($request->password != ''){
            $user->password = Hash::make($request->password);
        }

        if ($request->has('mobile')) {
            if ($user->mobile !== $request->mobile || $user->calling_code !== $request->calling_code) {
                $user->mobile_verified = 0;
            }

            $user->mobile = $request->mobile;
            $user->calling_code = $request->calling_code;
        }

        if ($request->hasFile('image')) {
            $user->image = Files::upload($request->image,'profile');
        }
        $user->save();



		/*$company_id					= 	$this->user->roles[0]->company_id;
		$admin						=	$this->user->roles[0]->name;
		if($admin == 'admin')
		{
			$app_duplicate 			= $request->app_duplicate;	
			$app_duplicate_days 	= $request->app_duplicate_days;	
			
			
			DB::table('companies')
						->where('id', $company_id)
						//->where('candidate_id', @$request->candidate)
						//->where('skill_id', @$request->schemaname)
						->limit(1) 
						->update(array('app_duplicate' => @$app_duplicate, 'app_duplicate_days' => @$app_duplicate_days));     
						
			
		}	*/
			
				
		
		
		
        return Reply::redirect(route('admin.profile.index'), __('menu.myProfile').' '.__('messages.updatedSuccessfully'));
    }
	
	 public function update(Request $request, $id)
		{
			$company_id      	=	auth()->user()->company_id;
			
			$user 				= User::find($id);
        	$user->name 		= $request->name;
			$user->mobile       = $request->mobile;
			if ($request->hasFile('logo')) {
            	$user->image = Files::upload($request->logo,'team_profile_img');
        	}
       		$user->save();
			
			$users_profile_count	= DB::table('users_profile')->select('*')->where('user_id', $id)->count();				
			
			
			
			$values 		= array(
										'company_id' 		=> $company_id,
										'user_id' 			=> $id, 
										'designation' 		=> $request->designation,
										'country' 			=> $request->country,
										'state'  			=> $request->state
										//'city'			=> $request->city
										//'skills'			=> $request->designation
									);
				
			$countryId 					=	$request->country;
			$stateId 					=	$request->state;
			if($countryId  == 101){
					$values['city_id']	=	$request->city_id;	
			}else{
					$values['city']		=	$request->city;	
			}
								
			if($users_profile_count > 0)
			{			
				//UPDATE
				DB::table('users_profile')->where('company_id', @$company_id)->where('user_id', @$id)->limit(1) ->update($values);  
			}
			else
			{
				//INSERT				
				DB::table('users_profile')->insert($values);
			}
						
			//echo '<pre>'; print_r($_FILES);
			//echo '<pre>'; print_r($_REQUEST); exit; 
			//abort_if(! $this->user->cans('edit_team'), 403);
			/*
			if($id == $this->user->id){
			abort(403);
			}
			*/
			//$user->email = $request->email;
			/*if($request->password != ''){
			$user->password = Hash::make($request->password);
			}
			if (($request->mobile != $user->mobile || $request->calling_code != $user->calling_code) && $user->mobile_verified == 1) {
			$user->mobile_verified = 0;
			}
			$user->calling_code = $request->calling_code;
			//attach role
			RoleUser::where('user_id', $id)->delete();
			$user->roles()->attach($request->role_id);*/

        	//return Reply::redirect(route('admin.team.index'), __('menu.team').' '.__('messages.updatedSuccessfully'));
			$message		=	"update successfully";
    		return Reply::dataOnly(['status' => 'success', 'message'=> $message]);   
		
		}
		
	
	public function appdup()
	{
			$company_id			= 	$this->user->roles[0]->company_id;
			$this->admin		=	$this->user->roles[0]->name;
						
        	//$this->calling_codes = $this->getCallingCodes();
        	return view('admin.profile.appdup', $this->data);
			
			
	}
	
	public function appdupupdate(Request $request)
	{
		$company_id					= 	$this->user->roles[0]->company_id;
		$admin						=	$this->user->roles[0]->name;
		//if($admin == 'admin')
		//{
			$app_duplicate 			= $request->app_duplicate;	
			$app_duplicate_days 	= $request->app_duplicate_days;	
						
			DB::table('companies')
						->where('id', $company_id)
						//->where('candidate_id', @$request->candidate)
						//->where('skill_id', @$request->schemaname)
						->limit(1) 
						->update(array('app_duplicate' => @$app_duplicate, 'app_duplicate_days' => @$app_duplicate_days));     
						
			
		//}		s
		
		return Reply::redirect(route('admin.profile.appdup'), __('menu.myProfile').' '.__('messages.updatedSuccessfully'));
	
	}
	
	public function videoMeetingSettings()
		{
		
			//echo '<pre>'; print_r($this->user->company); exit; 
			$company_id					= 	$this->user->roles[0]->company_id;
			$this->admin				=	$this->user->roles[0]->name;						
        	$this->calling_codes 		= 	$this->getCallingCodes();
			
			$this->data['company']		=	$this->user->company;
			
			//echo '<pre>'; print_r($this->data['company']); exit;  
			
        	return view('admin.profile.videoMeetingSettings', $this->data);
    	}
		
	public function videoupdate(Request $request)
	{
	
		$zoom_status		=	$zoom_api_key		=	$zoom_api_secret		=	'';
		$google_status		=	$google_api_key		=	$google_api_secret		=	'';
		$microsoft_status	=	$microsoft_api_key	=	$microsoft_api_secret	=	'';
				
		$zoom_status 			= $request->zoom_status;	
		$zoom_api_key 			= $request->zoom_api_key;	
		$zoom_api_secret 		= $request->zoom_api_secret;
		
		$google_status 			= $request->google_status;
		$google_api_key 		= $request->google_api_key;
		$google_api_secret 		= $request->google_api_secret;
		
		$microsoft_status 		= $request->microsoft_status;
		$microsoft_api_key 		= $request->microsoft_api_key;
		$microsoft_api_secret 	= $request->microsoft_api_secret;
			
			
		
		$company_id					= 	$this->user->company->id;
		$admin						=	$this->user->roles[0]->name;
		
		
		$update						=	array(
												'zoom_status' 			=> @$zoom_status, 
												'zoom_api_key' 			=> @$zoom_api_key,
												'zoom_api_secret' 		=> @$zoom_api_secret,
												
												'google_status' 		=> @$google_status,
												'google_api_key' 		=> @$google_api_key,
												'google_api_secret' 	=> @$google_api_secret,
												
											);	
					
		DB::table('companies')
						->where('id', $company_id)
						//->where('candidate_id', @$request->candidate)
						//->where('skill_id', @$request->schemaname)
						->limit(1) 
						->update($update);     
						
			
	
		
		return Reply::redirect(route('admin.profile.appdup'), __('menu.myProfile').' '.__('messages.updatedSuccessfully'));
	
	}
	
	public function editProfile()
		{		
        	return view('admin.profile.editProfile', $this->data);
    	}	
	
	public function profileedit()
		{			
        	return view('admin.profile.profileedit', $this->data);
    	}	
		
		
	public function verifyGoogleRedirect(Request $request)
		{
			date_default_timezone_set('Asia/Calcutta'); 
			$strtotime 					= strtotime(date("Y-m-d H:i:s"));
		
		
		$code							=	$scope	=	$company	=	$company_id	=	'';
		$code 							=	$request->code;
		$scope 							=	$request->scope;		
		$company_id      				=	auth()->user()->company_id;
		//$CLIENT_REDIRECT_URL			=	route('admin.profile.verify-google-redirect');
		//$CLIENT_REDIRECT_URL			=	route('admin.profile.verify-google-redirect');
		
		$CLIENT_REDIRECT_URL			=	route('admin.profile.verify-google-redirect').'/';  
		
		$company						=	$this->user->company;
		//Session::put('code', $code);
		//Session::put('scope', $scope);	
				
		$data 							= 	Google::GetAccessToken($company->google_client_id, $CLIENT_REDIRECT_URL, $company->google_client_secret, $code);
		
		 
		//echo '<pre>'; print_r($data); exit; 
		
		//if(array_key_exists('refresh_token', $data))
		//{
			//refresh_token 				= $data['refresh_token'];
			//Session::put('refresh_token', $refresh_token);
		//}
		//Session::put('access_token_expiry', $strtotime + $data['expires_in']);
		//Session::put('access_token', $data['access_token']);
		
		$access_token	=	$data['access_token'];
		$expires_in		=	$strtotime + $data['expires_in'];
		$refresh_token	=	$data['refresh_token'];
		$scope			=	$data['scope'];
		$token_type		=	$data['token_type'];
		
		$update			=	array(
									'google_access_token' 	=> @$access_token, 
									'google_expires_in' 	=> @$expires_in,
									'google_refresh_token' 	=> @$refresh_token,
									'google_scope' 			=> @$scope,
									'google_token_type' 	=> @$token_type,
									'google_verify' 		=> 1											
								);			
		DB::table('companies')->where('id', $company_id)->limit(1)->update($update);		
		
		return Reply::redirect(route('admin.account'), __('menu.myProfile').' '.__('messages.updatedSuccessfully'));
		
		//return Redirect::to(route('admin.profile.video-meetings-settings'));
		//return Reply::dataOnly([
									//'status' 		=> 	'success',
									//'message'		=>	$message,
									//'data'			=>	$data
							  //]);
		
		//admin/account
		//return Reply::dataOnly(['status' => 'success', 'message'=> 'verify']);   
								
		
	}	
	
	
	
	//https://rm.vibhanshujaiswal.com/admin/verify-google-redirect?code=4/0AX4XfWh-Q1lyg9DsqLRq2G_E1atIN9NHGCNLeMlBUpZKM70HgIzALIrIdba0sJefeg6C8A&scope=https://www.googleapis.com/auth/calendar
	//https://rm.vibhanshujaiswal.com/admin/verify-google-redirect
	//GET https://rm.vibhanshujaiswal.com/admin/cvtracker/data?search=&_=1638256986564

	
	
}
