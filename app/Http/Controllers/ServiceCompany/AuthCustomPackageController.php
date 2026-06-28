<?php

/***########################################################
Controller    	: 	ManageSubscriptionController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/

namespace App\Http\Controllers\ServiceCompany;

use App\Helper\Reply;
use App\Traits\StripeSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helper\GeneralMethod;

class AuthCustomPackageController extends ServiceCompanyBaseController
{
    use StripeSettings;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle 	= 'menu.authCustomPackage';
        $this->pageIcon 	= 'ti-settings';
    }
    
    public function index()
    {
       
					$user 							=	auth()->user();
					$company_id       				=	$user->company_id;
					
					$this->package_active			=	DB:: table('package_active')->select('package_active.*')->where('package_active.company_id', '=', $company_id)->first();
							
					$activePackageRow 				=	DB::table('package_active')->select('package_active.*')->where('package_active.company_id', $company_id)->first(); 
					
					//USERS
					$users_status					=	@$activePackageRow->users_status;
					//$users_startdate 				=	@$activePackageRow->users_startdate;		
					$users_startdate				=	strtotime($activePackageRow->users_startdate);
					$users_enddate					=	strtotime($activePackageRow->users_enddate);
					$current			 			= 	strtotime(date("Y-m-d"));
					
					
					
					$this->message					=	"";
					if(($user->root_user == 0) && ($user->working_status == 0)){
						$this->message				=	'Working <span style="color:#FF0000"> STATUS NOT ENABLED</span> in your account, please conact to administrator for explore prodct functionality.';	
					}else if($users_status != 1){
						$this->message				=	'Users package <span style="color:#FF0000"> STATUS INACTIVE </span>, please conact to administrator for explore prodct functionality.';		
					}else if(!(  ($users_startdate <= $current) && ($users_enddate >= $current)  ) ){
						$this->message				=	'Users package <span style="color:#FF0000">VALIDITY HAS EXPIRED</span>, please conact to administrator for explore prodct functionality.';	
					}else{
						return redirect()->route('service_company.dashboard'); 
					}
															
					return view('service_company.auth-custom-package.index', $this->data);
	}
	
	public function validatePackage(Request $request)
	{
				$event		=	'';
				$event		=	$request->event;
				$company_id 			=	auth()->user()->company_id; 
				$eventId = $request->eventId;
				if($event=='KEYWORD_ADD'){
					return Reply::dataOnly(['status' => 'success']);
				}
				// $coinsDeductions =  GeneralMethod::coinsValidations((int) $company_id,$eventId,auth()->user()->id,'','',$company_id);
				$coinsRequ = 1;
				$companyId = (int)$company_id;
				$coinsDeductions = GeneralMethod::coinsValidationsAllcompany($companyId,$eventId,$coinsRequ);

				// dd($coinsDeductions->status);

				if($coinsDeductions->status){
					return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message,'company_coins'=> $coinsDeductions->company_coins]);
				}else{
					return Reply::dataOnly(['status' => 'success']);
				}	
		
	}   		
	public function validateClientPackage(Request $request)
	{
		
				//echo '<pre>'; print_r($_REQUEST); exit; 
				 
				$event			=	$app_id	=	$company_id	=	'';
				$event			=	$request->event;
				$app_id			=	$request->app_id;

				// dd($app_id,$request->all());
				
				$appRow			=	DB::table('job_applications')
				->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')->select('add_job_wise_condidates.company_id','add_job_wise_condidates.thirdparty_company_id')->where('add_job_wise_condidates.id', '=', $app_id)->first(); 
				$company_id		=	$appRow->company_id;
				$thirdparty_company_id = $appRow->thirdparty_company_id;

				// dd($appRow	);
				// dd($company_id, $thirdparty_company_id);
				$eventId = $request->eventId;
				// $coinsDeductions =  GeneralMethod::coinsValidations((int) $company_id,$eventId,auth()->user()->id,'','',$company_id);
				$coinsRequ = 1;
				$companyId = (int)$company_id;
				$coinsDeductions = GeneralMethod::coinsValidationsAllcompany($companyId,$eventId,$coinsRequ);

				if($coinsDeductions->status){
					return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message,'company_coins'=> $coinsDeductions->company_coins]);
				}else{
					return Reply::dataOnly(['status' => 'success']);
				}			
				
		
	}   
		
		
}
