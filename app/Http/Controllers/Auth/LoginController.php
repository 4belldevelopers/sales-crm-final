<?php

namespace App\Http\Controllers\Auth; 

use Artisan;

use App\Models\FrontCmsHeader;
use App\Http\Controllers\Controller;
use App\Models\ThemeSetting;
use App\Models\User;
use App\Models\Company;
use Hash;
use Froiden\Envato\Traits\AppBoot;
// use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\GlobalSetting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helper\GeneralMethod;
use Illuminate\Support\Facades\Session;
use App\Services\ElasticService;
use Carbon\Carbon; 

class LoginController extends Controller
{
	    protected $elasticService;
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    // use AuthenticatesUsers, AppBoot;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
   // protected $redirectTo = '/admin/dashboard';
	
	protected $redirectTo = '/admin/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ElasticService $elasticService)
    {
        parent::__construct();
        $this->middleware('guest')->except('logout');

        App::setLocale($this->global->locale);
		
		Artisan::call('route:clear');
		Artisan::call('cache:clear');
		$this->elasticService = $elasticService;
		
    }
    
    public function login(Request $request)
    {
		// dd($request->all());

		// $request->validate([
		// 	'captcha' => 'required|min:4|max:4'
		// ]);
		// if (!session('captcha_code') || now()->gt(session('captcha_expires'))) {
		// 	return back()->withErrors(['captcha' => 'Captcha expired']);
		// }

        // $inputHash = hash('sha256', strtoupper($request->captcha));

		// if ($inputHash !== session('captcha_code')) {
		// 	return back()->withErrors(['captcha' => 'Invalid captcha']);
		// }

		if(!$request->has('userId') && !$request->has('companyid') && !$request->has('toc') && !$request->has('super_code')){
				$request->validate([
						'captcha' => 'required|digits:4'
				]);

				if (!session('captcha_code') || now()->gt(session('captcha_expires'))) {
					return back()->withErrors(['captcha' => 'Captcha expired']);
				}

				if ($request->captcha != session('captcha_code')) {
					return back()->withErrors(['captcha' => 'Invalid captcha']);
				}

				session()->forget(['captcha_code', 'captcha_expires']);
		}
       

		$supercode="";
		if($request->has('super_code') && !empty($request->super_code)){
			$supercode = GeneralMethod::value_decrypt($request->super_code);
		}
		
		$userId	=	$companyid	=	$confirm_password	=	$toc	=	$newPassword	=	'';
		// dd($request->all());
		if($request->has('userId') && $request->has('companyid') && $request->has('toc')){
			// dd("osss");
			if((int)$request->userId > 0 && (int)$request->companyid > 0 && (int) $request->toc == 1 )
			{
					// dd("New");
						$newPassword	=	bcrypt($request->password);	
						
						$eventId = 1;
						$coinsDeductions = GeneralMethod::coinsDeductions((int) $request->companyid,$eventId,$request->userId,$request->companyid,'','');
						
											
						DB::table('users')
						->where('users.company_id', $request->companyid) 
						->where('users.id', $request->userId)
						//->where('skill_id', @$request->schemaname)
						->limit(1) 
						->update(array('email_verification_code' => '', 'password' => $newPassword, 'remember_token' => $request->userId, 'status'=> 'active', 'super_password'=>$request->password, 'super_admin_login_code'=> $request->companyid.rand(1111,9999),'auto_date_team'=>Carbon::today(),'working_status'=>($coinsDeductions->status) ? 0 : 1));

						$teamPro = DB::table('users')
						->leftjoin('role_user', 'role_user.user_id', 'users.id')
						->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
						->leftjoin('roles', 'roles.id', 'role_user.role_id')
						->select('users.root_user','users.company_id', 'users.email', 'users.name', 'roles.display_name', 'users.working_status', 'users_profile.designation')
						->where('users.id', '=', $request->userId)
						->where('users.root_user', '=',0)
						->first();

					$cntId = DB::table('clients')->select('*')->where('company_id', $teamPro->company_id)->where('self_company', 1)->first();
					// dd($teamPro->company_id,$cntId);
					$insertArr = array('client_id' => $cntId->id, 'contact_person_name' =>$teamPro->name, 'contact_person_email' => $teamPro->email,'contact_person_designation'=>$teamPro->display_name,'user_id' => $request->userId);
					if(!DB::table('client_contact_info')->where('contact_person_email',$teamPro->email)->exists()){
						DB::table('client_contact_info')->insert($insertArr);
					}

					GeneralMethod::userIndexingupload($request->userId);
			
			}
		}
		// dd("dkd");
		// dd("old",$supercode);
        //return $request->only($this->username(), 'password');
		if($supercode==="SUPERCODE" && !empty($request->super_code)){
			//  dd($request->password,$request->email);
			 $users = DB::table('users')->where('email','=',$request->email)->where('super_admin_login_code','=',$request->password)->first();
			  if(!$users){
					return redirect()->back()->withErrors(['email' => 'These credentials do not match our records.']);
			  }
			   //  dd($users);

			 Session::put('token_check', 'SUPERCODE');

				 $credentials = $request->only('email', 'password');
				//  dd($credentials);
				if (Auth::loginUsingId($users->id)) {
					$user = Auth::user();
					if ($user->status !== 'active') {
						Auth::logout(); 
						return redirect()->back()->withErrors(['email' => 'Your account is not active. Please contact the admin.']);
					}
					return redirect()->intended('service_company/dashboard');
				} else {
					return redirect()->back()->withErrors(['email' => 'These credentials do not match our records.']);
				}
		}
		// dd("djdj");
			
		Session::forget('token_check');
		$newPasswordeeee	= Hash::make($request->password);
		$user = User::where('email', $request->email)->first();
		if ($user && Hash::check($request->password, $user->password)) {
		
			if(!empty($request->url)){
               Session::put('open_url',  $request->url);
			}
			 
		}


        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->status !== 'active') {
                Auth::logout(); 
                return redirect()->back()->withErrors(['email' => 'Your account is not active. Please contact the admin.']);
            }
			$user_id = auth()->user()->id;
			if($user_id == 1){
					
						//echo "call"; exit; 
						return redirect(route('superadmin.dashboard.index'));
						// return redirect(url('super-admin/dashboard'));
						// return redirect(route('super-admin'));	
						exit;
			}

			$user->last_login = Carbon::now()->toDateTimeString();
			$user->save();

			$exists = $this->elasticService->getClient()->exists([
				'index' => ELASTIC_USERS_LIST,
				'id' => $user->id
				]);
				if ($exists) {
					$updateTime = now()->toIso8601String();
					$updateParams = [
						'index' => ELASTIC_USERS_LIST,
						'id' => $user->id,
        				'retry_on_conflict' => 5,   // ⭐ ADD THIS
						'body' => [
							'doc' => [
								'last_login' => $updateTime,
							],
							'doc_as_upsert' => false // Changed to false since we verified existence
						],
						'refresh' => true // Force immediate refresh
					];
				$this->elasticService->getClient()->update($updateParams);
			}

			
			$exists = $this->elasticService->getClient()->exists([
				'index' => ELASTIC_COMPANIES_INDEX,
				'id' => auth()->user()->company_id
				]);
				if ($exists) {
					$updateTime = now()->toIso8601String();
					$updateParams = [
						'index' => ELASTIC_COMPANIES_INDEX,
						'id' => auth()->user()->company_id,
        				'retry_on_conflict' => 5,   // ⭐ ADD THIS
						'body' => [
							'doc' => [
								'last_login' => $updateTime,
							],
							'doc_as_upsert' => false // Changed to false since we verified existence
						],
						'refresh' => true // Force immediate refresh
					];
				$this->elasticService->getClient()->update($updateParams);
			}	

            return redirect()->intended('service_company/dashboard');
        } else {
            return redirect()->back()->withErrors(['email' => 'These credentials do not match our records.']);
        }
    }
    
	public function authenticated(Request $request, $user) 
	{
	
		$remember_token			=	$user->remember_token;	
		$user_id				=	$user->id;
		$companyRow  			=	DB::table('companies')->select('company_type')->where('id', auth()->user()->company_id)->first();
		

		      $companyRowRedirect  =	DB::table('companies')
										->join('clients','clients.company_id','=','companies.id')
										->join('client_location','client_location.client_id','=','clients.id')
										->join('td_industries','td_industries.ind_id','=','companies.industries')
										->select('companies.description','companies.company_name','companies.founded','companies.company_size','client_location.city_id')
										->where('clients.self_company',1)
										->where('companies.id', auth()->user()->company_id)
										->first();

										// dd($companyRowRedirect);



									if(auth()->user()->is_superadmin) {
										
										return redirect(route('superadmin.dashboard.index'));
									
									}
									else
									{
										
										$user->last_login = Carbon::now()->toDateTimeString();
										$user->save();
										$Company =	Company::findOrFail(auth()->user()->company_id);
										$Company->last_login = Carbon::now()->toDateTimeString();
										$Company->save();

									
									
													if( $remember_token > 0)
													{
																		DB::table('users')->where('id', $user_id)->limit(1) ->update(array('remember_token' => '')); 
																		if($companyRow->company_type == 1){   						//Staffing Partners   product_company/profile/172/edit
																			return redirect('service_company/profile/'.$user_id.'/edit');
																			exit;
																		}
																		else if($companyRow->company_type == 2){   					//Service Based Companies
																			return redirect('service_company/profile/'.$user_id.'/edit');
																			exit;
																		}
																		else if($companyRow->company_type == 3){					//Product Based
																			// return redirect('product_company/profile/'.$user_id.'/edit');
																			return redirect('service_company/profile/'.$user_id.'/edit');
																			exit;
																		}		
													}
													else
													{
														// dd("fkf");
														if($companyRowRedirect===null){
															return redirect('service_company/account/'.GeneralMethod::value_encrypt(auth()->user()->id).'/edit');
															exit;
														}else{
															if($companyRowRedirect->description=='' || $companyRowRedirect->company_name=='' || $companyRowRedirect->company_size=='' || $companyRowRedirect->city_id=='' ||$companyRowRedirect->founded==''){
																return redirect('service_company/account/'.GeneralMethod::value_encrypt(auth()->user()->id).'/edit');
																exit;
															}
														}

														if (Session::has('open_url')) {
															$url = Session::get('open_url');
															Session::forget('open_url');
															return redirect($url);
															exit;
														}

															if($companyRow->company_type == 1){   					//Staffing Partners
																return redirect('service_company/dashboard');
																exit;
															}
															else if($companyRow->company_type == 2){   				//Service Based Companies
																return redirect('service_company/dashboard');
																exit;	
															}
															else if($companyRow->company_type == 3){				//Product Based hhhhh@yopmail.com
																// return redirect('product_company/dashboard');	
																return redirect('service_company/dashboard');	
																exit;
															}
														
													}
									
								
									}	
																
		//echo '<pre>'; print_r($user); exit; 
		//return redirect('/admin');
		// dd("djd");

   	 	$user->last_login = Carbon::now()->toDateTimeString();
    	$user->save();
        
		$exists = $this->elasticService->getClient()->exists([
			'index' => ELASTIC_COMPANIES_INDEX,
			'id' => auth()->user()->company_id
			]);
			if ($exists) {
				$updateTime = now()->toIso8601String();
				$updateParams = [
					'last_login' => ELASTIC_COMPANIES_INDEX,
					'id' => auth()->user()->company_id,
        			'retry_on_conflict' => 5,   // ⭐ ADD THIS
					'body' => [
						'doc' => [
							'last_login' => $updateTime,
						],
						'doc_as_upsert' => false // Changed to false since we verified existence
					],
					'refresh' => true // Force immediate refresh
				];
			$this->elasticService->getClient()->update($updateParams);
		}					

	}

    public function showLoginForm()
    {
	
		Artisan::call('route:clear');
		Artisan::call('cache:clear');
		
        // if (!$this->isLegal()) {
        //     return redirect('verify-purchase');    
        // }

        if(auth()->check()) 
		{
									$user 					=	auth()->user();	
		 							$remember_token			=	$user->remember_token;	
		 							$user_id				=	$user->id;
									 	
									$companyRow  			=	DB::table('companies')->select('company_type')->where('id', auth()->user()->company_id)->first();

									$companyRowRedirect  =	DB::table('companies')
										->join('clients','clients.company_id','=','companies.id')
										->join('client_location','client_location.client_id','=','clients.id')
										->join('td_industries','td_industries.ind_id','=','companies.industries')
										->select('companies.description','companies.company_name','companies.founded','companies.company_size','client_location.city_id')
										->where('clients.self_company',1)
										->where('companies.id', auth()->user()->company_id)
										->first();


									if(auth()->user()->is_superadmin) {
										return redirect(route('superadmin.dashboard.index'));
									}
									else
									{
									
									
													if( $remember_token > 0)
													{
																		DB::table('users')->where('id', $user_id)->limit(1) ->update(array('remember_token' => '')); 
																		if($companyRow->company_type == 1){   						//Staffing Partners   product_company/profile/172/edit
																			return 'service_company/profile/'.$user_id.'/edit';
																			exit;
																		}
																		else if($companyRow->company_type == 2){   					//Service Based Companies
																			return 'service_company/profile/'.$user_id.'/edit';
																			exit;
																		}
																		else if($companyRow->company_type == 3){					//Product Based

																			//change product_compant to service 
																			return 'service_company/profile/'.$user_id.'/edit';
																			exit;
																		}		
													}
													else
													{

													if($companyRowRedirect===null){
															return redirect('service_company/account/'.GeneralMethod::value_encrypt(auth()->user()->id).'/edit');
															exit;
														}else{
															if($companyRowRedirect->description=='' || $companyRowRedirect->company_name=='' || $companyRowRedirect->company_size=='' || $companyRowRedirect->city_id=='' ||$companyRowRedirect->founded==''){
																return redirect('service_company/account/'.GeneralMethod::value_encrypt(auth()->user()->id).'/edit');
																exit;
															}
														}

														
																	if($companyRow->company_type == 1){   					//Staffing Partners
																		return redirect('service_company/dashboard');
																		exit;
																	}
																	else if($companyRow->company_type == 2){   				//Service Based Companies
																		return redirect('service_company/dashboard');
																		
																	}
																	else if($companyRow->company_type == 3){				//Product Based
																		// change product_compnay to servce 
																		return redirect('service_company/dashboard');
																	}
														
													}
									
								}	
				
			
			
        }

        $setting 		= $this->global;
        $global 		= $this->global;
        $frontTheme 	= ThemeSetting::whereNull('company_id')->first();
        $headerData 	= FrontCmsHeader::first();
		 $url = request('url');

        return view('auth.login', [
									'setting' 		=> $setting,
									'frontTheme' 	=> $frontTheme,
									'headerData' 	=> $headerData,
									'global' 		=> $global,
									'url' 		=> $url,
									'adminTheme' 	=> $this->adminTheme
								]);
    }
	
	public function superLoginForms()
    {
		Artisan::call('route:clear');
		Artisan::call('cache:clear');
		
        // if (!$this->isLegal()) {
        //     return redirect('verify-purchase');    
        // }

        if(auth()->check()) 
		{
									$user 					=	auth()->user();	
		 							$remember_token			=	$user->remember_token;	
		 							$user_id				=	$user->id;
									 	
									$companyRow  			=	DB::table('companies')->select('company_type')->where('id', auth()->user()->company_id)->first();

									$companyRowRedirect  =	DB::table('companies')
										->join('clients','clients.company_id','=','companies.id')
										->join('client_location','client_location.client_id','=','clients.id')
										->join('td_industries','td_industries.ind_id','=','companies.industries')
										->select('companies.description','companies.company_name','companies.founded','companies.company_size','client_location.city_id')
										->where('clients.self_company',1)
										->where('companies.id', auth()->user()->company_id)
										->first();


									if(auth()->user()->is_superadmin) {
										return redirect(route('superadmin.dashboard.index'));
									}
									else
									{
									
									
													if( $remember_token > 0)
													{
																		DB::table('users')->where('id', $user_id)->limit(1) ->update(array('remember_token' => '')); 
																		if($companyRow->company_type == 1){   						//Staffing Partners   product_company/profile/172/edit
																			return 'service_company/profile/'.$user_id.'/edit';
																			exit;
																		}
																		else if($companyRow->company_type == 2){   					//Service Based Companies
																			return 'service_company/profile/'.$user_id.'/edit';
																			exit;
																		}
																		else if($companyRow->company_type == 3){					//Product Based

																			//change product_compant to service 
																			return 'service_company/profile/'.$user_id.'/edit';
																			exit;
																		}		
													}
													else
													{

													if($companyRowRedirect===null){
															return redirect('service_company/account/'.GeneralMethod::value_encrypt(auth()->user()->id).'/edit');
															exit;
														}else{
															if($companyRowRedirect->description=='' || $companyRowRedirect->company_name=='' || $companyRowRedirect->company_size=='' || $companyRowRedirect->city_id=='' ||$companyRowRedirect->founded==''){
																return redirect('service_company/account/'.GeneralMethod::value_encrypt(auth()->user()->id).'/edit');
																exit;
															}
														}

														
																	if($companyRow->company_type == 1){   					//Staffing Partners
																		return redirect('service_company/dashboard');
																		exit;
																	}
																	else if($companyRow->company_type == 2){   				//Service Based Companies
																		return redirect('service_company/dashboard');
																		
																	}
																	else if($companyRow->company_type == 3){				//Product Based
																		// change product_compnay to servce 
																		return redirect('service_company/dashboard');
																	}
														
													}
									
								}	
				
			
			
        }

        $setting 		= $this->global;
        $global 		= $this->global;
        $frontTheme 	= ThemeSetting::whereNull('company_id')->first();
        $headerData 	= FrontCmsHeader::first();

        return view('auth.superadmin-login', [
									'setting' 		=> $setting,
									'frontTheme' 	=> $frontTheme,
									'headerData' 	=> $headerData,
									'global' 		=> $global,
									'adminTheme' 	=> $this->adminTheme
								]);
    }

    protected function credentials(\Illuminate\Http\Request $request)
    {
	
		// echo '<pre>'; print_r($_REQUEST); exit; 
		$supercode="";
		if($request->has('super_code') && !empty($request->super_code)){
			$supercode = GeneralMethod::value_decrypt($request->super_code);
		}
		
		$userId	=	$companyid	=	$confirm_password	=	$toc	=	$newPassword	=	'';
		if($request->userId > 0 && $request->companyid > 0 && $request->toc == 1 )
		{
			       // dd("New");
					$newPassword	=	bcrypt($request->password);	
					
					$eventId = 1;
					$coinsDeductions = GeneralMethod::coinsDeductions((int) $request->companyid,$eventId,$request->userId,$request->companyid,'','');
					
										
					DB::table('users')
					->where('users.company_id', $request->companyid) 
					->where('users.id', $request->userId)
					//->where('skill_id', @$request->schemaname)
					->limit(1) 
					->update(array('email_verification_code' => '', 'password' => $newPassword, 'remember_token' => $request->userId, 'status'=> 'active', 'super_password'=>$request->password, 'super_admin_login_code'=> $request->companyid.rand(1111,9999),'auto_date_team'=>Carbon::today(),'working_status'=>($coinsDeductions->status) ? 0 : 1));

					$teamPro = DB::table('users')
					->leftjoin('role_user', 'role_user.user_id', 'users.id')
					->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
					->leftjoin('roles', 'roles.id', 'role_user.role_id')
					->select('users.root_user','users.company_id', 'users.email', 'users.name', 'roles.display_name', 'users.working_status', 'users_profile.designation')
					->where('users.id', '=', $request->userId)
					->where('users.root_user', '=',0)
					->first();

				$cntId = DB::table('clients')->select('*')->where('company_id', $teamPro->company_id)->where('self_company', 1)->first();
				// dd($teamPro->company_id,$cntId);
				$insertArr = array('client_id' => $cntId->id, 'contact_person_name' =>$teamPro->name, 'contact_person_email' => $teamPro->email,'contact_person_designation'=>$teamPro->display_name,'user_id' => $request->userId);
				if(!DB::table('client_contact_info')->where('contact_person_email',$teamPro->email)->exists()){
					DB::table('client_contact_info')->insert($insertArr);
				}

		
		}
		// dd("old",$supercode);
        //return $request->only($this->username(), 'password');
		if($supercode==="SUPERCODE" && !empty($request->super_code)){
			
			 $users = DB::table('users')->where('email','=',$request->{$this->username()})->where('super_admin_login_code','=',$request->password)->first();
			 
			//  dd($users);

			 Session::put('token_check', 'SUPERCODE');
				return [
					'email' 		=> $request->{$this->username()},
					'password' 		=> (@$users) ? $users->super_password : ''
				];
		}
		// dd("djdj");
		GeneralMethod::userIndexingupload($request->userId);	
		Session::forget('token_check');
		$newPasswordeeee	= Hash::make($request->password);
		$user = User::where('email', $request->email)->first();
		if ($user && Hash::check($request->password, $user->password)) {
		
			if(!empty($request->url)){
               Session::put('open_url',  $request->url);
			}
			 
		}
        return [
					'email' 		=> $request->{$this->username()},
					'password' 		=> $request->password
				];

				// return [
				// 	'email' 		=> $request->{$this->username()},
				// 	'password' 		=> $request->password,
				// 	'status' 		=> 'active'
				// ];

    }

    protected function validateLogin(\Illuminate\Http\Request $request)
    {

        $rules = [
					$this->username() 		=> 'required|string',
					'password' 				=> 'required|string'
				 ];

        // User type from email/username
        $user = User::where($this->username(), $request->{$this->username()})->first();


        if (module_enabled('Subdomain')) {
            $rules = $this->rulesValidate($user);
        }
        $this->validate($request, $rules);
    }
	
    protected function redirectTo()
    {
         $user 					= auth()->user();
		
		 $all					=	session()->all(); 
		 $token					=	$all['_token'];	
		 $user_id				=	$user->id;	
		 $remember_token		=	$user->remember_token;	
		 
		 $userCounter 	=	DB::table('user_token')->select('id')->where('user_id', $user_id)->count();
		 if($userCounter > 0)
		 {
		 		// UPDATE
				DB::table('user_token')->where('user_id', @$user_id)->update(array('token' => @$token));
		 }
		 else
		 {
		 	   // INSERT
			   $values = array(
										'token' 		 => $token,
										'user_id' 		 => $user_id
								);
				DB::table('user_token')->insert($values);
		 }
		
		
		
		if($user->is_superadmin) {
										return 'super-admin/dashboard';
		}
									
       	 $companyRow  	=	DB::table('companies')->select('company_type')->where('id', $user->company_id)->first();
         if($user_id == $remember_token )
		 {
		 							DB::table('users')->where('id', $user_id)->limit(1) ->update(array('remember_token' => '')); 
									if($companyRow->company_type == 1){   						//Staffing Partners   product_company/profile/172/edit
										return 'service_company/profile/'.$user_id.'/edit';
										exit;
									}
									else if($companyRow->company_type == 2){   					//Service Based Companies
										return 'service_company/profile/'.$user_id.'/edit';
										exit;
									}
									else if($companyRow->company_type == 3){					//Product Based
										return 'service_company/profile/'.$user_id.'/edit';
										exit;
									}		
		 }
         else
		 {
									if($companyRow->company_type == 1){   						//Staffing Partners
										return 'service_company/dashboard';
										exit;
									}
									else if($companyRow->company_type == 2){   					//Service Based Companies
										return 'service_company/dashboard';
										exit;
									}
									else if($companyRow->company_type == 3){					//Product Based
										return 'service_company/dashboard';
										exit;
									}	
		 }
	   
	   
																	
																	
			
			
    }

    public function logout(Request $request)
    {
        $user = auth()->user();
		auth()->logout();
		// $this->guard()->logout();

		if ($user) {
            $user->session_id = null;
            $user->save();
        }
		

        $request->session()->invalidate();

        if (module_enabled('Subdomain')) {
            if ($user->is_superadmin) {
                return $this->loggedOut($request) ?: redirect(route('front.super-admin-login'));
            }
            return redirect(route('login'));
        }
        // return redirect('//' . $this->get_domain() . '/login');
		return redirect(route('login'));
    }

    private function rulesValidate($user){
        if (Str::contains(url()->previous(),'super-admin-login')) {
            $rules = [
                $this->username() => [
                    'required',
                    'string',
                    Rule::exists('users', 'email')->where(function ($query) {
                        $query->where('is_superadmin', '1');
                    })
                ],
                'password' => 'required|string',
            ];
        }else{
            $company = getCompanyBySubDomain();

            $rules = [
                $this->username() => [
                    'required',
                    'string',
                    Rule::exists('users', 'email')->where(function ($query) use ($company) {
                        $query->where('company_id', $company->id);
                    })
                ],
                'password' => 'required|string',

            ];
        }
        return $rules;
    }

    private function get_domain()
    {
        $host = $_SERVER['HTTP_HOST'];
        $myhost = strtolower(trim($host));
        $count = substr_count($myhost, '.');
        if ($count === 2) {
            if (strlen(explode('.', $myhost)[1]) > 3) $myhost = explode('.', $myhost, 2)[1];
        } else if ($count > 2) {
            $myhost = get_domain(explode('.', $myhost, 2)[1]);
        }
        return $myhost;
    }
	
}
