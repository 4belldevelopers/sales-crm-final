<?php
 
/***########################################################
Controller    	: 	ManageSubscriptionController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	08-SEPT-2022	
########################################################***/

namespace App\Http\Controllers\ProductCompany;

use App\Models\Company;
use App\Models\CompanyPackage;
use App\Models\GlobalSetting;
use App\Helper\Reply;
use App\Http\Requests\Payment\PaymentRequest;
use App\Models\PaymentSetting;
use App\Models\PaypalInvoice;
use App\Models\RazorpayInvoice;
use App\Models\RazorpaySubscription;
use App\Traits\StripeSettings;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Package;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use PayPal\Api\Agreement;
use PayPal\Api\AgreementStateDescriptor;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Razorpay\Api\Api;
use Stripe\Subscription;
use Yajra\DataTables\DataTables;

use App\Helper\GeneralMethod;

 
class AuthCustomPackageController extends ProductCompanyBaseController
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
		$this->message					=	"";				
		$activePackageRow 				=	DB::table('package_active')->select('package_active.*')->where('package_active.company_id', $company_id)->first();
		
		//USERS
		$users_status					=	@$activePackageRow->users_status;
		//$users_startdate 				=	@$activePackageRow->users_startdate;		
		$users_startdate				=	strtotime($activePackageRow->users_startdate);
		$users_enddate					=	strtotime($activePackageRow->users_enddate);
		$current			 			= 	strtotime(date("Y-m-d"));
		
		if(($user->root_user == 0) && ($user->working_status == 0)){
		 	$this->message				=	'Working <span style="color:#FF0000"> STATUS NOT ENABLED</span> in your account, please conact to administrator for explore prodct functionality.';	
		}else if($users_status != 1){
			$this->message				=	'Users package <span style="color:#FF0000"> STATUS INACTIVE </span>, please conact to administrator for explore prodct functionality.';		
		}else if(!(  ($users_startdate <= $current) && ($users_enddate >= $current)  ) ){
			$this->message				=	'Users package <span style="color:#FF0000">VALIDITY HAS EXPIRED</span>, please conact to administrator for explore prodct functionality.';	
		}else{
			return redirect()->route('product_company.dashboard'); 
		}										
        return view('product_company.auth-custom-package.index', $this->data);
    }
	
	
	public function validatePackage(Request $request)
		{
				$event		=	'';
				$event		=	$request->event;
				
if($event == 'CVACCESS')
	{
					$packageStatus  	=	GeneralMethod::validatePackage('CVACCESS');	
					
					if($packageStatus['STATUS']==1){
						if($packageStatus['EXPIRE']== 'false'){
							if($packageStatus['BALANCE'] > 0 ){
								return Reply::dataOnly(['status' => 'success']);	
							}else{
								return Reply::dataOnly(["status" => "info", "message" => "CV Access / Received can't access because your package balance max limit reached , please contact to Administrator."]);	
							}	
						}else{
							return Reply::dataOnly(["status" => "info", "message"=> "CV Access / Received can't access because your package expired , please contact to Administrator."]);		
						}  
					}else{
						return Reply::dataOnly(["status" => "info", "message"=> "CV Access / Received can't access because your package status is Inactive , please contact to Administrator."]);	
					}
				}
								
else if($event == 'NEW_BENCH_CLIENT_REQUEST')
	{
					$app_id				=	$client_company_id	=	'';
					$app_id				=	$request->app_id;
					
					$rowData			=	DB:: table('bench_applications')->select('bench_applications.company_id')->where('bench_applications.id', '=', $app_id)->first();
					$client_company_id	=	$rowData->company_id;
					
					$packageStatus  	=	GeneralMethod::validatePackage('CVACCESS');	
					if($packageStatus['STATUS']==1){
						if($packageStatus['EXPIRE']== 'false'){
							if($packageStatus['BALANCE'] > 0 ){
								
							
								
								
$clientPackageStatus  	=	GeneralMethod::validateClientPackage('NEW_BENCH_CLIENT_REQUEST', $client_company_id);	
if($clientPackageStatus['STATUS']==1){
	if($clientPackageStatus['EXPIRE']== 'false'){
		if($clientPackageStatus['BALANCE'] > 0 ){
			return Reply::dataOnly(['status' => 'success']);	
		}else{
			return Reply::dataOnly(["status" => "info", "message" => "New Bench Client Request can't access because Client package balance max limit reached , please contact to Administrator."]);	
		}	
	}else{
		return Reply::dataOnly(["status" => "info", "message"=> "New Bench Client Request can't access because Client package expired , please contact to Administrator."]);		
	}  
}else{
	return Reply::dataOnly(["status" => "info", "message"=> "New Bench Client Request can't access because Client package status is Inactive , please contact to Administrator."]);	
}
							
								
								
							}else{
								return Reply::dataOnly(["status" => "info", "message" => "CV Access / Received can't access because your package balance max limit reached , please contact to Administrator."]);	
							}	
						}else{
							return Reply::dataOnly(["status" => "info", "message"=> "CV Access / Received can't access because your package expired , please contact to Administrator."]);		
						}  
					}else{
						return Reply::dataOnly(["status" => "info", "message"=> "CV Access / Received can't access because your package status is Inactive , please contact to Administrator."]);	
					}
					
					
					
					
					
					
					
					
					
					
					}
				
				
				
							
		
		}
		
		 
	
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 

    

}
