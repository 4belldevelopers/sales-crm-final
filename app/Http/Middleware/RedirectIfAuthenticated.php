<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
        public function handle($request, Closure $next, $guard = null)
    {
	
        if (Auth::guard($guard)->check()) { 

            $user 					=	auth()->user();
			$remember_token			=	$user->remember_token;	
			$user_id				=	$user->id;
									
			$companyRow  			=	DB::table('companies')->select('company_type')->where('id', $user->company_id)->first();
			
			
			//echo '<pre>'; print_r($user);  exit; 
			
			if($user_id == $remember_token)
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
					if($user_id == 1){
					
						//echo "call"; exit; 
						return redirect(route('superadmin.dashboard.index'));
						// return redirect(url('super-admin/dashboard'));
						// return redirect(route('super-admin'));	
						exit;
					}
					
					if($companyRow->company_type == 1){   							//Staffing Partners
						return redirect(route('service_company.dashboard'));
					}
					else if($companyRow->company_type == 2){   						//Service Based Companies
						return redirect(route('service_company.dashboard'));
					}
					else if($companyRow->company_type == 3){						//Product Based
						return redirect(route('service_company.dashboard'));
					}
					
			}
			
			
			if($user->hasRole('employee')){
                return redirect(route('member.dashboard'));
            }
        }

        return $next($request);
    }
}
