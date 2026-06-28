<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\CompanyPackage;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CustomPackage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
        public function handle($request, Closure $next)
    {
	
		
		 $user 	= auth()->user();
		 	
						 
		 if(($user->root_user == 0)){
		 		
				
				
				$company_id		=	$user->company_id;
				$companyRow		=	DB::table('companies')->select('companies.company_type')->where('companies.id', '=', $company_id)->first();

				if($companyRow	){
		 
		 		//echo $companyRow->company_type; exit; 
		 
		 		if($user->working_status == 0){
						
							if($companyRow->company_type == 1){   						// Staffing Partners   product_company/profile/172/edit
								return redirect()->route('service_company.auth-custom-package.index'); 
							}
							else if($companyRow->company_type == 2){   					// Service Based Companies
								return redirect()->route('service_company.auth-custom-package.index'); 
							}
							else if($companyRow->company_type == 3){					// Product Based
								return redirect()->route('product_company.auth-custom-package.index'); 
							}						
							
				}else if($user->working_status == 1){
						
						$activePackageRow 	=	DB::table('package_active')->select('package_active.*')->where('package_active.company_id', $company_id)->first();

						// dd($activePackageRow);
					
						
						//USERS
						$users_startdate 				=	@$activePackageRow->users_startdate;
						$users_enddate					=	@$activePackageRow->users_enddate;
						$users_status					=	@$activePackageRow->users_status;
						
						$users_startdate				=	strtotime(@$activePackageRow->users_startdate);
						$users_enddate					=	strtotime(@$activePackageRow->users_enddate);
						$current			 			= 	strtotime(date("Y-m-d"));
						
											
					
					
				
				}
			}
				
			
				
					 	 
		 
		 }
		
		 
		
		 
		 
		 return $next($request);
		 
    }
}
