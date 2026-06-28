<?php

namespace App\Http\Controllers\Auth; 

use App\Models\FrontCmsHeader;
use App\Http\Controllers\Controller;
use App\Models\ThemeSetting;
use App\Models\User;
use Froiden\Envato\Traits\AppBoot;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\GlobalSetting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\DB;

use Carbon\Carbon; 

class CustomloginController extends Controller
{
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

    use AuthenticatesUsers, AppBoot;

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
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest')->except('logout');

        App::setLocale($this->global->locale);
		
		//echo '<pre>'; print_r($this->global->locale);
		//echo '<pre>'; print_r($_REQUEST);
		
		
    }

	public function authenticated(Request $request, $user) 
	{
	
		

   	 	$user->last_login = Carbon::now()->toDateTimeString();
    	$user->save();
	}

    public function showLoginForm()
    {
        if (!$this->isLegal()) {
            return redirect('verify-purchase');
        }

        if(auth()->check()) 
		{
																		if(auth()->user()->is_superadmin) {
																			return redirect(route('superadmin.dashboard.index'));
																		}
																		//return redirect('admin/dashboard');
																		$companyRow  	=	DB::table('companies')->select('company_type')->where('id', auth()->user()->company_id)->first();
																		if($companyRow->company_type == 1){   					//Staffing Partners
																			return redirect('staffing_company/dashboard');
																		}
																		else if($companyRow->company_type == 2){   				//Service Based Companies
																			return redirect('service_company/dashboard');
																		}
																		else if($companyRow->company_type == 3){				//Product Based
																			return redirect('product_company/dashboard');
																		}
				
			
			
        }

        $setting 		= $this->global;
        $global 		= $this->global;
        $frontTheme 	= ThemeSetting::whereNull('company_id')->first();
        $headerData 	= FrontCmsHeader::first();

        return view('auth.login', [
									'setting' 		=> $setting,
									'frontTheme' 	=> $frontTheme,
									'headerData' 	=> $headerData,
									'global' 		=> $global,
									'adminTheme' 	=> $this->adminTheme
								]);
    }

    protected function credentials(\Illuminate\Http\Request $request)
    {
	
		$userId	=	$companyid	=	$confirm_password	=	$toc	=	$newPassword	=	'';
		if($request->userId > 0 && $request->companyid > 0 && $request->toc == 1 )
		{
					$newPassword	=	bcrypt($request->password);	
					
					DB::table('users')
					->where('users.company_id', $request->companyid) 
					->where('users.id', $request->userId)
					//->where('skill_id', @$request->schemaname)
					->limit(1) 
					->update(array('email_verification_code' => '', 'password' => $newPassword)); 	
		
		}
	
	//echo '<pre>'; print_r($_REQUEST); exit; 
	
		
/*	Array
(
    [_token] => YurbVhppwXGyOtqVmLs45VDQ85sE56Au2pkAbJYF
    [email] => 19pro@test.com
    [password] => 12345678
)*/


        //return $request->only($this->username(), 'password');
        return [
					'email' 		=> $request->{$this->username()},
					'password' 		=> $request->password,
					'status' 		=> 'active'
				];
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
        $user 			= auth()->user();
		
		 $all			=	session()->all(); 
		 $token			=	$all['_token'];	
		 $user_id		=	$user->id;	
		 $userCounter 	=	DB::table('user_token')->select('id')->where('user_id', $user_id)->count();
		 if($userCounter > 0)
		 {
		 		// UPDATE
				DB::table('user_token')
				     ->where('user_id', @$user_id)
				  	 ->update(array('token' => @$token));
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
		
		
        //echo '<pre>'; print_r(session()->all());
         
       
	   
	   
																/*if($user->is_superadmin) {
																	return 'super-admin/dashboard';
																}
																return 'admin/dashboard';*/
																	if($user->is_superadmin) {
																		return 'super-admin/dashboard';
																	}
																	$companyRow  	=	DB::table('companies')->select('company_type')->where('id', $user->company_id)->first();
																	
													 
																	
																	
																	if($companyRow->company_type == 1){   					//Staffing Partners
																		return 'staffing_company/dashboard';
																		exit;
																	}
																	else if($companyRow->company_type == 2){   				//Service Based Companies
																		return 'service_company/dashboard';
																		exit;
																	}
																	else if($companyRow->company_type == 3){				//Product Based
																		return 'product_company/dashboard';
																		exit;
																	}
																	/*else{
																		return 'admin/dashboard';	
																	}*/
			
			
    }

    public function logout(Request $request)
    {
        $user = auth()->user();
        $this->guard()->logout();

        // Clear session ID when the user logs out
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
        return redirect('//' . $this->get_domain() . '/login');
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
