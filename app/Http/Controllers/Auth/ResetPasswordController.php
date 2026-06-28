<?php

namespace App\Http\Controllers\Auth; 

use App\Models\FrontCmsHeader;
use App\Models\GlobalSetting;
use App\Http\Controllers\Controller;
use App\Models\ThemeSetting;
// use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Hash;
use App\Models\User;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Helper\GeneralMethod;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Session;
use Illuminate\Support\Str;
use App\Services\MailService;
use App\Services\MassMailService;
use App\Services\NotificationMailService;
use App\Jobs\QueueMailService;
use App\Jobs\QueueMassMailService;
use App\Jobs\QueueNotificationMailService;

class ResetPasswordController extends Controller
{
     protected $mailService;
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    // use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MailService $mailService)
    {
        parent::__construct();
        $this->middleware('guest');
        $this->mailService = $mailService;
    }
    
    public function sendResetLinkEmail(Request $request){
         $request->validate(['email' => 'required|email']);
         
         if(User::where('email', $request->email)->exists()){
             if(User::where('email', $request->email)->where('status','=','active')->exists()){
            //   session()->flash();
                $email = $request->email;
                DB::table('password_resets')->where(['email'=> $email])->delete();
                $basecompany = GeneralMethod::baseCompany();
                $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
                $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
                $attachments = array();
                $cc = array();
                $subject = "Reset Password Notification";
                $user = User::where('email', $request->email)->first();
                $token = $this->generateToken();
                $Url 			=	url('/password/reset/'.$token.'/')."?email=".$email;		
                $data = [
                    'name' => $user->name,
                    'link' => $Url
                    ];

                    QueueMailService::dispatch(
                        $email,
                        $subject,
                        'email.others.reset-password',
                        (object) $data,
                        $attachments,
                        $fromAddress,
                        $senderName,
                        $cc,
                        $user->company_id
                    )->onConnection('emails');

                // $result = $this->mailService->sendEmail($email, $subject,'email.others.reset-password', (object) $data, $attachments, $fromAddress, $senderName,$cc);

                DB::table('password_resets')->insert(['email'=> $email,'token'=>$token,'created_at'=>date('Y-m-d H:i:s')]);
                    return back()->with('status', 'We have e-mailed your password reset link!');
                }else{
                    return back()->with('accountnotverify', 'Your account has been suspended. To resolve this issue, please contact the administrator or send an email to support@jeroapp.com for further assistance.');  
                }
         }else{
             return back()->with('accountnotverify', 'This account does not exist.');
         }
         
         
        
    }
    
     protected function generateToken()
    {
        // Custom token generation logic
        return hash('sha256', Str::random(60));
    }
    

    public function showResetForm(Request $request, $token = null)
    {
        $setting 		=	GlobalSetting::first();
        $frontTheme 	=	ThemeSetting::whereNull('company_id')->first();
        $headerData 	=	FrontCmsHeader::first();
        $setting 		=	$this->global;
        $global 		=	$this->global;
		
		$email   		=	$request->email;
		
        return view('auth.passwords.reset')->with(
            [
                'token' 		=>	$token,
                'setting' 		=>	$setting,
                'frontTheme' 	=>	$frontTheme,
                'headerData' 	=>	$headerData,
                'global'		=>	$global,
				'email'			=>	$email
            ]
        );
    }


	
	public function ResetPasswordStore(Request $request) 
	{			
			
			$encEmail	=	base64_decode($request->encEmail); 
			
			$request->validate([
									//'email' 				=> 'required|email',
									'password' 				=> 'required|string|min:8',
									'password_confirmation' => 'required'
								]);
	
			//$update 		=	DB::table('password_resets')->where('email' ,'=', $encEmail)->where('token', '=', $request->token)->first();
			$update 		=	DB::table('password_resets')->where('email' ,'=', $encEmail)->first(); 
				

			if(!$update){
				return back()->withErrors(['errors' => 'Invalid token!']);
				//return Redirect::back()->withErrors(['errors' => 'Invalid token!']);
				//return back()->withInput()->with('errors', 'Invalid token!');
			}
	
			$user 	= User::where('email', $encEmail)->update(['password' => Hash::make($request->password),'super_password'=>$request->password]);
	
			// Delete password_resets record
			DB::table('password_resets')->where(['email'=> $encEmail])->delete();
			
			$basecompany = GeneralMethod::baseCompany();
                $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
                $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
                $attachments = array();
                $cc = array();
                $subject = "Reset Password Notification"; 
                $user = User::where('email', $encEmail)->first(); 
                $token = $this->generateToken();
                $Url 			=	url('/password/reset/'.$token.'/')."?email=".$encEmail;		
                $data = [
                    'name' => $user->name,
                    'link' => $Url
                    ];
                $result = $this->mailService->sendEmail($encEmail, $subject,'email.others.changed-password', (object) $data, $attachments, $fromAddress, $senderName,$cc);
                
	
			return redirect('/password-confirmation')->with('set_email', $encEmail);
	}
	
	public function passwordConfirmation()
	{
			$this->data	=	array();
			//Session::get('message');
			
			return view('auth.passwords.passwordConfirmation', $this->data);
	
	}
	

    protected function redirectTo()
    {
	
		
        $user = auth()->user();
        if ($user->is_superadmin) {
            return 'super-admin/dashboard';
        }



        if ($user->hasRole('admin')) {
            return 'admin/dashboard';
        } elseif ($user->hasRole('employee')) {
            return 'member/dashboard';
        }
		
		
		
									/*$user 					=	auth()->user();	
		 							$remember_token			=	$user->remember_token;	
		 							$user_id				=	$user->id;
									 	
									$companyRow  			=	DB::table('companies')->select('company_type')->where('id', auth()->user()->company_id)->first();
									if(auth()->user()->is_superadmin) {
										return redirect(route('superadmin.dashboard.index'));
									}
									else
									{
									
									
													if( $remember_token > 0)
													{
																		DB::table('users')->where('id', $user_id)->limit(1) ->update(array('remember_token' => '')); 
																		if($companyRow->company_type == 1){   						//Staffing Partners   product_company/profile/172/edit
																			return 'staffing_company/profile/'.$user_id.'/edit';
																			exit;
																		}
																		else if($companyRow->company_type == 2){   					//Service Based Companies
																			return 'service_company/profile/'.$user_id.'/edit';
																			exit;
																		}
																		else if($companyRow->company_type == 3){					//Product Based
																			return 'product_company/profile/'.$user_id.'/edit';
																			exit;
																		}		
													}
													else
													{
																	if($companyRow->company_type == 1){   					//Staffing Partners
																		return redirect('staffing_company/dashboard');
																		exit;
																	}
																	else if($companyRow->company_type == 2){   				//Service Based Companies
																		return redirect('service_company/dashboard');
																		
																	}
																	else if($companyRow->company_type == 3){				//Product Based
																		return redirect('product_company/dashboard');
																	}
														
													}
									
								}	*/
								
								
								
		
		
		
		

    }
}
