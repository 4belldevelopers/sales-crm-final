<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Http\Requests\SuperAdmin\StorePackage;
use App\Helper\Reply;
use Illuminate\Support\Facades\DB;
use App\Helper\GeneralMethod;
use App\Models\Company;
use Yajra\DataTables\Facades\DataTables;

use App\Models\GlobalSetting;
use App\Models\PaypalInvoice;
use App\Models\RazorpayInvoice;
use App\Models\StripeInvoice;
use App\Traits\StripeSettings;
use Carbon\Carbon;


class DefaultPackageController extends SuperAdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle 	= 'menu.packages';
        $this->pageIcon 	= 'icon-settings';
    }
    public function index() {
		
		$this->defualt_package = DB::table('package_default')->select('package_default.*')->orderBy('package_default.id', 'ASC')->first();					
				
	    return view('super-admin.defaultpackages.index', $this->data);
    }
    public function create($company_id = null) 
	{
    	$this->company_id 		= $company_id;
	    //$this->paymentSetting = PaymentSetting::first();
        return view('super-admin.custompackages.create', $this->data);
    
	}
    public function createInvoice(Request $request) 
	{

			$company_id					=	'';
			$company_id					=	$request->company_id;
			
			$this->company 				=	DB::table('companies')->select('*')->where('companies.id', $company_id)->first();
			
			//echo '<pre>'; print_r($_REQUEST); 
			//exit; 
			
			$cnt						=	0;
			$service					=	array();			
			if($request->cvaccess_quantity > 0 && $request->cvaccess_price >0){
				$service[$cnt]['alt_item'] 		=	'cvaccess';	
				$service[$cnt]['item'] 			=	'CV Access / Received';	
				
				$service[$cnt]['startdate'] 	=	$request->cvaccess_startdate;
				$service[$cnt]['enddate'] 		=	$request->cvaccess_enddate;
				$service[$cnt]['status'] 		=	$request->cvaccess_status;
				$service[$cnt]['quantity'] 		=	$request->cvaccess_quantity;	
				$service[$cnt]['price'] 		=	$request->cvaccess_price;
				$service[$cnt]['finalprice'] 	=	$request->cvaccess_finalprice;
				$cnt	=	$cnt+1;
			}
			if($request->users_quantity > 0 && $request->users_price >0){
				$service[$cnt]['alt_item'] 		=	'users';	
				$service[$cnt]['item'] 			=	'Users';	
				
				$service[$cnt]['startdate'] 	=	$request->users_startdate;
				$service[$cnt]['enddate'] 		=	$request->users_enddate;
				$service[$cnt]['status'] 		=	$request->users_status;
				$service[$cnt]['quantity'] 		=	$request->users_quantity;	
				$service[$cnt]['price'] 		=	$request->users_price;
				$service[$cnt]['finalprice'] 	=	$request->users_finalprice;
				$cnt	=	$cnt+1;
			}
			if($request->clientrequest_quantity > 0 && $request->clientrequest_price >0){
				$service[$cnt]['alt_item'] 		=	'new_client_request';
				$service[$cnt]['item'] 			=	'New Client Request';
					
				$service[$cnt]['startdate'] 	=	$request->clientrequest_startdate;
				$service[$cnt]['enddate'] 		=	$request->clientrequest_enddate;
				$service[$cnt]['status'] 		=	$request->clientrequest_status;
				$service[$cnt]['quantity'] 		=	$request->clientrequest_quantity;	
				$service[$cnt]['price'] 		=	$request->clientrequest_price;
				$service[$cnt]['finalprice'] 	=	$request->clientrequest_finalprice;
				$cnt	=	$cnt+1;
			}
			if($request->jobrequest_quantity > 0 && $request->jobrequest_price >0){
				$service[$cnt]['alt_item'] 		=	'new_job_request';
				$service[$cnt]['item'] 			=	'New Job Request';	
				
				$service[$cnt]['startdate'] 	=	$request->jobrequest_startdate;
				$service[$cnt]['enddate'] 		=	$request->jobrequest_enddate;
				$service[$cnt]['status'] 		=	$request->jobrequest_status;
				$service[$cnt]['quantity'] 		=	$request->jobrequest_quantity;	
				$service[$cnt]['price'] 		=	$request->jobrequest_price;
				$service[$cnt]['finalprice'] 	=	$request->jobrequest_finalprice;
				$cnt	=	$cnt+1;
			}
			if($request->benchclient_quantity > 0 && $request->benchclient_price >0){
				$service[$cnt]['alt_item'] 		=	'new_bench_client_request';
				$service[$cnt]['item'] 			=	'New Bench Client Request';	
				
				$service[$cnt]['startdate'] 	=	$request->benchclient_startdate;
				$service[$cnt]['enddate'] 		=	$request->benchclient_enddate;
				$service[$cnt]['status'] 		=	$request->benchclient_status;
				$service[$cnt]['quantity'] 		=	$request->benchclient_quantity;	
				$service[$cnt]['price'] 		=	$request->benchclient_price;
				$service[$cnt]['finalprice'] 	=	$request->benchclient_finalprice;
				$cnt	=	$cnt+1;
			}
			if($request->emails_quantity > 0 && $request->emails_price >0){
				$service[$cnt]['alt_item'] 		=	'emails';
				$service[$cnt]['item'] 			=	'Emails';	
				
				$service[$cnt]['startdate'] 	=	$request->emails_startdate;
				$service[$cnt]['enddate'] 		=	$request->emails_enddate;
				$service[$cnt]['status'] 		=	$request->emails_status;
				$service[$cnt]['quantity'] 		=	$request->emails_quantity;	
				$service[$cnt]['price'] 		=	$request->emails_price;
				$service[$cnt]['finalprice'] 	=	$request->emails_finalprice;
				$cnt	=	$cnt+1;
			}
			if($request->oldcvaccess_quantity > 0 && $request->oldcvaccess_price >0){
				$service[$cnt]['alt_item'] 		=	'old_cv_access';
				$service[$cnt]['item'] 			=	'Old CV Access';
					
				$service[$cnt]['startdate'] 	=	$request->oldcvaccess_startdate;
				$service[$cnt]['enddate'] 		=	$request->oldcvaccess_enddate;
				$service[$cnt]['status'] 		=	$request->oldcvaccess_status;
				$service[$cnt]['quantity'] 		=	$request->oldcvaccess_quantity;	
				$service[$cnt]['price'] 		=	$request->oldcvaccess_price;
				$service[$cnt]['finalprice'] 	=	$request->oldcvaccess_finalprice;
				$cnt	=	$cnt+1;
			}
			if($request->discount >= 0){
				$service[$cnt]['alt_item'] 		=	'discount';
				$service[$cnt]['item'] 			=	'discount';	
				$service[$cnt]['discount'] 		=	$request->discount;
				$cnt	=	$cnt+1;
			}				
			if($request->label){
				$service[$cnt]['alt_item'] 		=	'TAX';
				$service[$cnt]['item'] 			=	'TAX';	
				$service[$cnt]['label'] 		=	$request->label;
				$service[$cnt]['per_text'] 		=	$request->per_text;
				$service[$cnt]['per_val'] 		=	$request->per_val;
				$cnt	=	$cnt+1;	
			}
			if($request->amount_total_paid){
				$service[$cnt]['alt_item'] 				=	'amount_total_paid';
				$service[$cnt]['item'] 					=	'amount_total_paid';	
				$service[$cnt]['amount_total_paid'] 	=	$request->amount_total_paid;
				$cnt	=	$cnt+1;	
			}
			if($request->payment_mode){
				$service[$cnt]['alt_item'] 				=	'payment_mode';
				$service[$cnt]['item'] 					=	'payment_mode';	
				$service[$cnt]['payment_mode'] 			=	$request->payment_mode;
				$cnt	=	$cnt+1;	
			}
									
			$this->service						= $service;	

			$this->client_location_count 	= DB::table('client_location')
											->join('clients', 'clients.id', '=', 'client_location.client_id')
											->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
											->where('clients.company_id', $company_id)
											->where('clients.self_company', 1)
											->orderBy('client_location.id', 'ASC')
											->count();
											
		    $this->client_location 	    	= DB::table('client_location')
											->join('clients', 'clients.id', '=', 'client_location.client_id')
											->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
											->where('clients.company_id', $company_id)
											->where('clients.self_company', 1)
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
			
			$view 		= view('super-admin.custompackages.createInvoice', $this->data)->render();
			return Reply::dataOnly(['status' => 'success', 'view' => $view]);
	}
	public function saveInvoice(Request $request)
	{
		//echo '<pre>'; print_r($_REQUEST); exit; 
		
		$company_id		=	'';
		$company_id		=	$request->company_id;
		
		$serviceArr		=	$invoice_id	=	'';
		$serviceArr		=	$request->serviceArr;
		
		$t				=	date('Y-m-d H:i:s');
		$invoice_id		=	strtotime($t);

		$companyRow 	=	DB::table('companies')->select('*')->where('companies.id', $company_id)->first();
		
		
		
		$order_val				=	array('company_id' => @$company_id, 'company_type' => @$companyRow->company_type);
		$invoice_val			=	array('company_id' => @$company_id, 'invoice_id' => @$invoice_id, 'company_type' => @$companyRow->company_type);
		$active_val  			=	array('company_type' => @$companyRow->company_type);
		
		//package_order
		
		$subtotal		=	0;
		if(!empty($serviceArr)){
			foreach($serviceArr as $sArr){
				if($sArr['alt_item'] == 'cvaccess') { 
						$order_val['cvaccess_startdate'] 	=	$invoice_val['cvaccess_startdate'] 	=  	@$sArr['startdate'];
						$order_val['cvaccess_enddate'] 		=	$invoice_val['cvaccess_enddate'] 	=  	@$sArr['enddate'];
						$order_val['cvaccess_status'] 		=	$invoice_val['cvaccess_status'] 	= 	@$sArr['status'];
						$order_val['cvaccess_quantity'] 	=	$invoice_val['cvaccess_quantity'] 	=  	@$sArr['quantity'];
						$order_val['cvaccess_price'] 		=	$invoice_val['cvaccess_price'] 		=  	@$sArr['price'];
						$order_val['cvaccess_totalprice'] 	=	$invoice_val['cvaccess_totalprice'] = 	@$sArr['finalprice'];

						$active_val['cvaccess_startdate']	=	@$sArr['startdate'];
						$active_val['cvaccess_enddate']		=	@$sArr['enddate'];
						$active_val['cvaccess_status']		=	@$sArr['status'];
						$active_val['cvaccess_quantity']	=	@$sArr['quantity'];
						$active_val['cvaccess_used']		= 	0;
						$active_val['cvaccess_balance']		=	@$sArr['quantity'];	
																			
						$subtotal							=  $subtotal + @$sArr['finalprice'];												 									
				}elseif($sArr['alt_item'] == 'users') {

						$order_val['users_startdate'] 		=	$invoice_val['users_startdate'] 	=  	@$sArr['startdate'];
						$order_val['users_enddate'] 		=	$invoice_val['users_enddate'] 		=  	@$sArr['enddate'];
						$order_val['users_status'] 			=	$invoice_val['users_status'] 		= 	@$sArr['status'];
						$order_val['users_quantity'] 		=	$invoice_val['users_quantity'] 		=  	@$sArr['quantity'];
						$order_val['users_price'] 			=	$invoice_val['users_price'] 		=  	@$sArr['price'];
						$order_val['users_totalprice'] 		=	$invoice_val['users_totalprice'] 	= 	@$sArr['finalprice'];

						$active_val['users_startdate']		=	@$sArr['startdate'];
						$active_val['users_enddate']		=	@$sArr['enddate'];
						$active_val['users_status']			=	@$sArr['status'];
						$active_val['users_quantity']		=	@$sArr['quantity'];
						$active_val['users_used']			= 	0;
						$active_val['users_balance']		=	@$sArr['quantity'];	
																			
						$subtotal							=  $subtotal + @$sArr['finalprice'];	

				}elseif($sArr['alt_item'] == 'new_client_request') {
					$order_val['newclientrequest_startdate'] 	=	$invoice_val['newclientrequest_startdate'] 	=  	@$sArr['startdate'];
					$order_val['newclientrequest_enddate'] 		=	$invoice_val['newclientrequest_enddate'] 	=  	@$sArr['enddate'];
					$order_val['newclientrequest_status'] 		=	$invoice_val['newclientrequest_status'] 	= 	@$sArr['status'];
					$order_val['newclientrequest_quantity'] 	=	$invoice_val['newclientrequest_quantity'] 	=  	@$sArr['quantity'];
					$order_val['newclientrequest_price'] 		=	$invoice_val['newclientrequest_price'] 		=  	@$sArr['price'];
					$order_val['newclientrequest_totalprice'] 	=	$invoice_val['newclientrequest_totalprice'] = 	@$sArr['finalprice'];

					$active_val['newclientrequest_startdate']	=	@$sArr['startdate'];
					$active_val['newclientrequest_enddate']		=	@$sArr['enddate'];
					$active_val['newclientrequest_status']		=	@$sArr['status'];
					$active_val['newclientrequest_quantity']	=	@$sArr['quantity'];
					$active_val['newclientrequest_used']		= 	0;
					$active_val['newclientrequest_balance']		=	@$sArr['quantity'];	
																		
					$subtotal									=  $subtotal + @$sArr['finalprice'];
																		
				}elseif($sArr['alt_item'] == 'new_job_request') {

					$order_val['newjobrequest_startdate'] 	=	$invoice_val['newjobrequest_startdate'] 	=  	@$sArr['startdate'];
					$order_val['newjobrequest_enddate'] 		=	$invoice_val['newjobrequest_enddate'] 	=  	@$sArr['enddate'];
					$order_val['newjobrequest_status'] 		=	$invoice_val['newjobrequest_status'] 		= 	@$sArr['status'];
					$order_val['newjobrequest_quantity'] 	=	$invoice_val['newjobrequest_quantity'] 		=  	@$sArr['quantity'];
					$order_val['newjobrequest_price'] 		=	$invoice_val['newjobrequest_price'] 		=  	@$sArr['price'];
					$order_val['newjobrequest_totalprice'] 	=	$invoice_val['newjobrequest_totalprice'] 	= 	@$sArr['finalprice'];

					$active_val['newjobrequest_startdate']	=	@$sArr['startdate'];
					$active_val['newjobrequest_enddate']	=	@$sArr['enddate'];
					$active_val['newjobrequest_status']		=	@$sArr['status'];
					$active_val['newjobrequest_quantity']	=	@$sArr['quantity'];
					$active_val['newjobrequest_used']		= 	0;
					$active_val['newjobrequest_balance']	=	@$sArr['quantity'];	
																		
					$subtotal								=	$subtotal + @$sArr['finalprice'];
		        }elseif($sArr['alt_item'] == 'new_bench_client_request') {
					$order_val['newbenchclientrequest_startdate'] 	=	$invoice_val['newbenchclientrequest_startdate'] 	=  	@$sArr['startdate'];
					$order_val['newbenchclientrequest_enddate'] 	=	$invoice_val['newbenchclientrequest_enddate'] 	=  	@$sArr['enddate'];
					$order_val['newbenchclientrequest_status'] 		=	$invoice_val['newbenchclientrequest_status'] 		= 	@$sArr['status'];
					$order_val['newbenchclientrequest_quantity'] 	=	$invoice_val['newbenchclientrequest_quantity'] 		=  	@$sArr['quantity'];
					$order_val['newbenchclientrequest_price'] 		=	$invoice_val['newbenchclientrequest_price'] 		=  	@$sArr['price'];
					$order_val['newbenchclientrequest_totalprice'] 	=	$invoice_val['newbenchclientrequest_totalprice'] 	= 	@$sArr['finalprice'];

					$active_val['newbenchclientrequest_startdate']	=	@$sArr['startdate'];
					$active_val['newbenchclientrequest_enddate']	=	@$sArr['enddate'];
					$active_val['newbenchclientrequest_status']		=	@$sArr['status'];
					$active_val['newbenchclientrequest_quantity']	=	@$sArr['quantity'];
					$active_val['newbenchclientrequest_used']		= 	0;
					$active_val['newbenchclientrequest_balance']	=	@$sArr['quantity'];	
																		
					$subtotal										=	$subtotal + @$sArr['finalprice'];

				}elseif($sArr['alt_item'] == 'emails') {

					$order_val['emails_startdate'] 	=	$invoice_val['emails_startdate'] 	=  	@$sArr['startdate'];
					$order_val['emails_enddate'] 	=	$invoice_val['emails_enddate'] 		=  	@$sArr['enddate'];
					$order_val['emails_status'] 	=	$invoice_val['emails_status'] 		= 	@$sArr['status'];
					$order_val['emails_quantity'] 	=	$invoice_val['emails_quantity'] 	=  	@$sArr['quantity'];
					$order_val['emails_price'] 		=	$invoice_val['emails_price'] 		=  	@$sArr['price'];
					$order_val['emails_totalprice'] =	$invoice_val['emails_totalprice'] 	= 	@$sArr['finalprice'];

					$active_val['emails_startdate']	=	@$sArr['startdate'];
					$active_val['emails_enddate']	=	@$sArr['enddate'];
					$active_val['emails_status']	=	@$sArr['status'];
					$active_val['emails_quantity']	=	@$sArr['quantity'];
					$active_val['emails_used']		= 	0;
					$active_val['emails_balance']	=	@$sArr['quantity'];	
																		
					$subtotal						=	$subtotal + @$sArr['finalprice'];
							
				}elseif($sArr['alt_item'] == 'old_cv_access') {
						$order_val['oldcvaccess_startdate'] 	=	$invoice_val['oldcvaccess_startdate'] 	=  	@$sArr['startdate'];
						$order_val['oldcvaccess_enddate'] 		=	$invoice_val['oldcvaccess_enddate'] 	=  	@$sArr['enddate'];
						$order_val['oldcvaccess_status'] 		=	$invoice_val['oldcvaccess_status'] 		= 	@$sArr['status'];
						$order_val['oldcvaccess_quantity'] 		=	$invoice_val['oldcvaccess_quantity'] 	=  	@$sArr['quantity'];
						$order_val['oldcvaccess_price'] 		=	$invoice_val['oldcvaccess_price'] 		=  	@$sArr['price'];
						$order_val['oldcvaccess_totalprice'] 	=	$invoice_val['oldcvaccess_totalprice'] 	= 	@$sArr['finalprice'];

						$active_val['oldcvaccess_startdate']	=	@$sArr['startdate'];
						$active_val['oldcvaccess_enddate']		=	@$sArr['enddate'];
						$active_val['oldcvaccess_status']		=	@$sArr['status'];
						$active_val['oldcvaccess_quantity']		=	@$sArr['quantity'];
						$active_val['oldcvaccess_used']			= 	0;
						$active_val['oldcvaccess_balance']		=	@$sArr['quantity'];	
																			
						$subtotal								=	$subtotal + @$sArr['finalprice'];

				}else if($sArr['alt_item'] == 'discount') {
							$invoice_val['discount'] =	@$sArr['discount'];
				}else if($sArr['alt_item'] == 'amount_total_paid') {
							$invoice_val['grand_total'] =	@$sArr['amount_total_paid'];
				}else if($sArr['alt_item'] == 'payment_mode') {
							$invoice_val['payment_mode'] =	@$sArr['payment_mode'];
				}					
								
			   
					   
		




														
			}
		}
		
		DB::table('package_order')->insert($order_val);  
		$package_order_id    				=	DB::getPdo()->lastInsertId();
		
		$invoice_val['package_order_id'] 	=	@$package_order_id;	
		$invoice_val['subtotal'] 			=	@$subtotal;				
		DB::table('package_invoice')->insert($invoice_val);  
		$package_invoice_id    				=	DB::getPdo()->lastInsertId();
		
		if(!empty($serviceArr)){
				foreach($serviceArr as $sArr){
						if($sArr['alt_item'] == 'TAX') {
						
								$childArr 		=	@$sArr['label'];
								$per_text 		=	@$sArr['per_text'];
								$per_val 		=	@$sArr['per_val'];
								
								$cnt		=	0;
							 	if(!empty($childArr)){
									foreach($childArr as $childVal){
											
												$values			 = array(
																			'invoice_id' 					=>	@$package_invoice_id, 
																			'tax_name'						=>	@$childVal,
																			'tax_percentage'				=>	@$per_text[$cnt],
																			'tax_amount'					=>	@$per_val[$cnt]
																		);
												DB::table('package_invoice_tax')->insert($values);  
												$cnt++;
					
									}
								}
						}		
				}
		}	
								
		DB::table('package_active')->where('company_id', $company_id)->limit(1)->update($active_val);
		    	
		if($package_invoice_id > 0){
			$url		=	url('super-admin/company/'.$company_id);
			return Reply::dataOnly(['status' => 'success', 'url' => $url]);
		}else{
			return Reply::dataOnly(['status' => 'fail']);	
		} 
	
	}
    public function store(Request $request) 
	{
	
        $package = new Package();
        $package->name = $request->name;
        $package->description = $request->description;
        $package->monthly_price = $request->monthly_price;
        $package->annual_price = $request->annual_price;
        $package->stripe_monthly_plan_id = $request->stripe_monthly_plan_id;
        $package->stripe_annual_plan_id = $request->stripe_annual_plan_id;
        $package->razorpay_annual_plan_id = $request->razorpay_annual_plan_id;
        $package->razorpay_monthly_plan_id = $request->razorpay_monthly_plan_id;

        if ($request->no_of_job_openings != '') {
            $package->no_of_job_openings = $request->no_of_job_openings;
        } else {
            $package->no_of_job_openings = null;
        }
        if($request->no_of_candidate_access != '') {
            $package->no_of_candidate_access = $request->no_of_candidate_access;
        }else {
            $package->no_of_candidate_access = null;
        }
        if($request->career_website) {
            $package->career_website = 1;
        }else {
            $package->career_website = 0;
        }
        if($request->multiple_roles) {
            $package->multiple_roles = 1;
        }else {
            $package->multiple_roles = 0;
        }
        if($request->recommended) {
            $package->recommended = 1;
        }else {
            $package->recommended = 0;
        }
        if($request->status) {
            $package->status = 1;
        }else {
            $package->status = 0;
        }
        $package->save();

        return Reply::redirect(route('superadmin.packages.index'), __('messages.createdSuccessfully'));
    }
    public function edit($id) 
	{
        $this->package = Package::findOrfail($id);
        $this->paymentSetting = PaymentSetting::first();
        return view('super-admin.packages.edit', $this->data);
    }
    public function destroy($id) 
	{
        Package::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }
	public function show(){
		// 
	}
	public function invoiceComment()
    {
		
        		//$categories 	= Company::with('package')->withoutGlobalScope('company')->get();	
				$myPackageHistory = DB::table('package_invoice_comment')->select('package_invoice_comment.*');			
				if (\request('invoice_id') != "")
				{
					$invoice_id		=	request('invoice_id');	
					$myPackageHistory->where('package_invoice_comment.invoice_id', '=', $invoice_id);
				}
				$myPackageHistory->groupBy('package_invoice_comment.id');
				$myPackageHistory->orderBy('package_invoice_comment.id', 'DESC');
							
				
				return DataTables::of($myPackageHistory)
				->editColumn('Name', function ($row) {
	
						$invoice_id				=	@$row->invoice_id;
						$payment_mode			=	@$row->payment_mode;
						$grand_total			=	number_format(@$row->grand_total);
								
						$cvaccess_startdate 					=	$cvaccess_enddate 	=	$cvaccess_quantity 	=	$cvaccess_price 	=	$cvaccess_totalprice 	= $cvaccess_status =	'';
						$cvaccess_startdate 					=	@$row->cvaccess_startdate ? date("d M, Y", strtotime(@$row->cvaccess_startdate)):'';
						$cvaccess_enddate 						=	@$row->cvaccess_enddate ? date("d M, Y", strtotime(@$row->cvaccess_enddate)):'';
						$cvaccess_quantity 						=	@$row->cvaccess_quantity ? number_format(@$row->cvaccess_quantity) :'';
						$cvaccess_price 						=	@$row->cvaccess_price ? number_format(@$row->cvaccess_price) :'';
						$cvaccess_totalprice 					=	@$row->cvaccess_totalprice ? number_format(@$row->cvaccess_totalprice) :'';
						$cvaccess_status						=	@$row->cvaccess_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';				
										
						$users_startdate 						=	$users_enddate 	=	$users_quantity 	=	$users_price 	=	$users_totalprice 	= $users_status =	'';
						$users_startdate 						=	@$row->users_startdate ? date("d M, Y", strtotime(@$row->users_startdate)):'';
						$users_enddate 							=	@$row->users_enddate ? date("d M, Y", strtotime(@$row->users_enddate)):'';
						$users_quantity 						=	@$row->users_quantity ? number_format(@$row->users_quantity) :'';
						$users_price 							=	@$row->users_price ? number_format(@$row->users_price) :'';
						$users_totalprice 						=	@$row->users_totalprice ? number_format(@$row->users_totalprice) :'';
						$users_status							=	@$row->users_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';				

						$newclientrequest_startdate 			=	$newclientrequest_enddate 	=	$newclientrequest_quantity 	=	$newclientrequest_price 	=	$newclientrequest_totalprice 	= $newclientrequest_status =	'';
						$newclientrequest_startdate 			=	@$row->newclientrequest_startdate ? date("d M, Y", strtotime(@$row->newclientrequest_startdate)):'';
						$newclientrequest_enddate 				=	@$row->newclientrequest_enddate ? date("d M, Y", strtotime(@$row->newclientrequest_enddate)):'';
						$newclientrequest_quantity 				=	@$row->newclientrequest_quantity ? number_format(@$row->newclientrequest_quantity) :'';
						$newclientrequest_price 				=	@$row->newclientrequest_price ? number_format(@$row->newclientrequest_price) :'';
						$newclientrequest_totalprice 			=	@$row->newclientrequest_totalprice ? number_format(@$row->newclientrequest_totalprice) :'';
						$newclientrequest_status				=	@$row->newclientrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';		

						$newjobrequest_startdate 				=	$newjobrequest_enddate 	=	$newjobrequest_quantity 	=	$newjobrequest_price 	=	$newjobrequest_totalprice 	= $newjobrequest_status =	'';
						$newjobrequest_startdate 				=	@$row->newjobrequest_startdate ? date("d M, Y", strtotime(@$row->newjobrequest_startdate)):'';
						$newjobrequest_enddate 					=	@$row->newjobrequest_enddate ? date("d M, Y", strtotime(@$row->newjobrequest_enddate)):'';
						$newjobrequest_quantity 				=	@$row->newjobrequest_quantity ? number_format(@$row->newjobrequest_quantity) :'';
						$newjobrequest_price 					=	@$row->newjobrequest_price ? number_format(@$row->newjobrequest_price) :'';
						$newjobrequest_totalprice 				=	@$row->newjobrequest_totalprice ? number_format(@$row->newjobrequest_totalprice) :'';
						$newjobrequest_status					=	@$row->newjobrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';

						//@$row->newjobrequest_totalprice

						$newbenchclientrequest_status 		=	$newbenchclientrequest_enddate 	=	$newbenchclientrequest_quantity 	=	$newbenchclientrequest_price 	=	$newbenchclientrequest_totalprice 	= $newbenchclientrequest_status =	'';
						$newbenchclientrequest_startdate 		=	@$row->newbenchclientrequest_startdate ? date("d M, Y", strtotime(@$row->newbenchclientrequest_startdate)):'';
						$newbenchclientrequest_enddate 			=	@$row->newbenchclientrequest_enddate ? date("d M, Y", strtotime(@$row->newbenchclientrequest_enddate)):'';
						$newbenchclientrequest_quantity 		=	@$row->newbenchclientrequest_quantity ? number_format(@$row->newbenchclientrequest_quantity) :'';
						$newbenchclientrequest_price 			=	@$row->newbenchclientrequest_price ? number_format(@$row->newbenchclientrequest_price) :'';
						$newbenchclientrequest_totalprice 		=	@$row->newbenchclientrequest_totalprice ? number_format(@$row->newbenchclientrequest_totalprice) :'';
						$newbenchclientrequest_status			=	@$row->newbenchclientrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';

						$emails_startdate 						=	$emails_enddate 	=	$emails_quantity 	=	$emails_price 	=	$emails_totalprice 	= $emails_status =	'';
						$emails_startdate 						=	@$row->emails_startdate ? date("d M, Y", strtotime(@$row->emails_startdate)):'';
						$emails_enddate 						=	@$row->emails_enddate ? date("d M, Y", strtotime(@$row->emails_enddate)):'';
						$emails_quantity 						=	@$row->emails_quantity ? number_format(@$row->emails_quantity) :'';
						$emails_price 							=	@$row->emails_price ? number_format(@$row->emails_price) :'';
						$emails_totalprice 						=	@$row->emails_totalprice ? number_format(@$row->emails_totalprice) :'';
						$emails_status							=	@$row->emails_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 

						$oldcvaccess_startdate 					=	$oldcvaccess_enddate 	=	$oldcvaccess_quantity 	=	$oldcvaccess_price 	=	$oldcvaccess_totalprice 	= $oldcvaccess_status =	'';
						$oldcvaccess_startdate 					=	@$row->oldcvaccess_startdate ? date("d M, Y", strtotime(@$row->oldcvaccess_startdate)):'';
						$oldcvaccess_enddate 					=	@$row->oldcvaccess_enddate ? date("d M, Y", strtotime(@$row->oldcvaccess_enddate)):'';
						$oldcvaccess_quantity 					=	@$row->oldcvaccess_quantity ? number_format(@$row->oldcvaccess_quantity) :'';
						$oldcvaccess_price 						=	@$row->oldcvaccess_price ? number_format(@$row->oldcvaccess_price) :'';
						$oldcvaccess_totalprice 				=	@$row->oldcvaccess_totalprice ? number_format(@$row->oldcvaccess_totalprice) :'';
						$oldcvaccess_status						=	@$row->oldcvaccess_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 

						$path					=	url('super-admin/custompackages/invoiceDownload/'.@$row->id);
						$packageCommentCount	=	DB::table('package_invoice_comment')->select('*')->where('invoice_id', '=', @$row->id)->count();
						$packageComment			=	DB::table('package_invoice_comment')->select('*')->where('invoice_id', '=', @$row->id)->orderBy('package_invoice_comment.id', 'DESC')->get();

						$commentHtml	=  '';
						if($packageCommentCount > 0)
						{
							foreach($packageComment as $pc)
							{
								$commentHtml	.=	'<tr>
											<td>Posted Comment</td>
											<td colspan="2">'.$pc->comment.'</td>
											<td>'. date("d M, Y h:i A", strtotime(@$pc->created_at)).'</td>
										</tr>';	
							}
						}
		
				
				
				
						$html = '<div class="card" style="margin-top:10px;">
							<div class="card-body">
								<div class="table-responsive">			  		
								<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="currentPackage">
									<thead class="">
									<tr class="fw-bolder text-muted">
										<th class="min-w-200px"><h4 class="card-title">Invoice ID</h4></th>
										<th class="min-w-150px"><h4 class="card-title">Payment Mode</h4></th>
										<th class="min-w-150px"><h4 class="card-title">Amount Paid</h4></th>
										<th class="min-w-150px"><h4 class="card-title">Invoice Download</h4></th>
									</tr>
									</thead>
									<tbody class="">
									<tr>
										<td>'.@$invoice_id.'</td>
										<td>'.@$payment_mode.'</td>
										<td>&#x20b9;'.@$grand_total.'</td>
										<td> <a href="'.@$path.'"><i class="bi bi-download" style="color:#006600; height:35px; width:35px;"></i></a></td>
									</tr>
									<tr>
										<td>Comment</td>
										<td colspan="2"><textarea name="" cols="100" rows="3" id="comment_'.@$row->id.'"></textarea></td>
										<td><button type="button" class="btn btn-success font-weight-bolder text-uppercase px-5 py-2 comment_submit" rel="'.@$row->id.'"  >Submit</button></td>
									</tr>
									'.$commentHtml.'
									
									</tbody>
								</table>
								</div>
								<div class="table-responsive">
								<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="currentPackage">  
									<thead class="">
									<tr class="fw-bolder text-muted">
										<th class="min-w-200px">Product</th>
										<th class="min-w-150px">Start Date</th>
										<th class="min-w-150px">End Date</th>
										<th class="min-w-150px">Quantity</th>
										<th class="min-w-150px">Price</th>
										<th class="min-w-150px">Final Price</th>
										<th class="min-w-150px">Status</th>
									</tr>
									</thead>
									<tbody class="">
									<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">CV Access / Received</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$cvaccess_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$cvaccess_enddate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$cvaccess_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$cvaccess_price.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$cvaccess_totalprice.'</a></td>
										<td class="">'.@$cvaccess_status.'</td>
									</tr>
									<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6" >Users</td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$users_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$users_enddate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$users_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$users_price.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$users_totalprice.'</a></td>
										<td class="">'.@$users_status.'</td>
									</tr>
									<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">New Client Request</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_enddate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_price.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_totalprice.'</a></td>
										<td class="">'.@$newclientrequest_status.'</td>
									</tr>
									<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">New Job Request</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newjobrequest_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newjobrequest_enddate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newjobrequest_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newjobrequest_price.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.$newjobrequest_totalprice.'</a></td>
										<td class="">'.@$newjobrequest_status.'</td>
									</tr>
									<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">New Bench Client Request</td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newbenchclientrequest_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newbenchclientrequest_enddate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newbenchclientrequest_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.$newbenchclientrequest_price.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newbenchclientrequest_totalprice.'</a></td>
										<td class="">'.@$newbenchclientrequest_status.'</td>
									</tr>
									<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Emails</td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$emails_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$emails_enddate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$emails_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$emails_price.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$emails_totalprice.'</a></td>
										<td class="">'.@$emails_status.'</td>
									</tr>
									<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Old CV Access</td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$oldcvaccess_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$oldcvaccess_enddate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$oldcvaccess_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$oldcvaccess_price.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$oldcvaccess_totalprice.'</a></td>
										<td class="">'.@$oldcvaccess_status.'</td>
									</tr>
									</tbody>
								</table>
								</div>
							</div>
							</div>';
						return $html;
				})
				->editColumn('Comment', function ($row) {
					return '<div class="form-check form-check-sm form-check-custom form-check-solid">
							'.@$row->comment.'
						  </div>';
				})
				->editColumn('Date', function ($row) {
					
					$created_at			=	$row->created_at;					
					$middle 			=	strtotime($created_at);             // returns bool(false)
					$new_date 			=	date('d M, Y H:i A', $middle);   // returns 1970-01-01 00:00:00

					return '<div class="form-check form-check-sm form-check-custom form-check-solid">
							'.$new_date.'
						  </div>';
				})
				->addIndexColumn()
				->rawColumns(['Comment', 'Date']) 
				->make(true);
    } 	
	public function myPackageHistory()
    {
		
        	//$categories 	= Company::with('package')->withoutGlobalScope('company')->get();	
			$myPackageHistory = DB::table('package_invoice')->select('package_invoice.*');			

				if (\request('company_id') != "")
				{
					$company_id		=	request('company_id');	
					$myPackageHistory->where('package_invoice.company_id', '=', $company_id);
				}
		
				$myPackageHistory->groupBy('package_invoice.id');
				$myPackageHistory->orderBy('package_invoice.id', 'DESC');
							
				
			    return DataTables::of($myPackageHistory)
				->editColumn('Name', function ($row) {
	
							$invoice_id				=	@$row->invoice_id;
							$payment_mode			=	@$row->payment_mode;
							$grand_total			=	number_format(@$row->grand_total);
									
							$cvaccess_startdate 					=	$cvaccess_enddate 	=	$cvaccess_quantity 	=	$cvaccess_price 	=	$cvaccess_totalprice 	= $cvaccess_status =	'';
							$cvaccess_startdate 					=	@$row->cvaccess_startdate ? date("d M, Y", strtotime(@$row->cvaccess_startdate)):'';
							$cvaccess_enddate 						=	@$row->cvaccess_enddate ? date("d M, Y", strtotime(@$row->cvaccess_enddate)):'';
							$cvaccess_quantity 						=	@$row->cvaccess_quantity ? number_format(@$row->cvaccess_quantity) :'';
							$cvaccess_price 						=	@$row->cvaccess_price ? number_format(@$row->cvaccess_price) :'';
							$cvaccess_totalprice 					=	@$row->cvaccess_totalprice ? number_format(@$row->cvaccess_totalprice) :'';
							$cvaccess_status						=	@$row->cvaccess_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';				
				
							$users_startdate 						=	$users_enddate 	=	$users_quantity 	=	$users_price 	=	$users_totalprice 	= $users_status =	'';
							$users_startdate 						=	@$row->users_startdate ? date("d M, Y", strtotime(@$row->users_startdate)):'';
							$users_enddate 							=	@$row->users_enddate ? date("d M, Y", strtotime(@$row->users_enddate)):'';
							$users_quantity 						=	@$row->users_quantity ? number_format(@$row->users_quantity) :'';
							$users_price 							=	@$row->users_price ? number_format(@$row->users_price) :'';
							$users_totalprice 						=	@$row->users_totalprice ? number_format(@$row->users_totalprice) :'';
							$users_status							=	@$row->users_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';				

							$newclientrequest_startdate 			=	$newclientrequest_enddate 	=	$newclientrequest_quantity 	=	$newclientrequest_price 	=	$newclientrequest_totalprice 	= $newclientrequest_status =	'';
							$newclientrequest_startdate 			=	@$row->newclientrequest_startdate ? date("d M, Y", strtotime(@$row->newclientrequest_startdate)):'';
							$newclientrequest_enddate 				=	@$row->newclientrequest_enddate ? date("d M, Y", strtotime(@$row->newclientrequest_enddate)):'';
							$newclientrequest_quantity 				=	@$row->newclientrequest_quantity ? number_format(@$row->newclientrequest_quantity) :'';
							$newclientrequest_price 				=	@$row->newclientrequest_price ? number_format(@$row->newclientrequest_price) :'';
							$newclientrequest_totalprice 			=	@$row->newclientrequest_totalprice ? number_format(@$row->newclientrequest_totalprice) :'';
							$newclientrequest_status				=	@$row->newclientrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';		

							$newjobrequest_startdate 				=	$newjobrequest_enddate 	=	$newjobrequest_quantity 	=	$newjobrequest_price 	=	$newjobrequest_totalprice 	= $newjobrequest_status =	'';
							$newjobrequest_startdate 				=	@$row->newjobrequest_startdate ? date("d M, Y", strtotime(@$row->newjobrequest_startdate)):'';
							$newjobrequest_enddate 					=	@$row->newjobrequest_enddate ? date("d M, Y", strtotime(@$row->newjobrequest_enddate)):'';
							$newjobrequest_quantity 				=	@$row->newjobrequest_quantity ? number_format(@$row->newjobrequest_quantity) :'';
							$newjobrequest_price 					=	@$row->newjobrequest_price ? number_format(@$row->newjobrequest_price) :'';
							$newjobrequest_totalprice 				=	@$row->newjobrequest_totalprice ? number_format(@$row->newjobrequest_totalprice) :'';
							$newjobrequest_status					=	@$row->newjobrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';

							//@$row->newjobrequest_totalprice

							$newbenchclientrequest_status 		=	$newbenchclientrequest_enddate 	=	$newbenchclientrequest_quantity 	=	$newbenchclientrequest_price 	=	$newbenchclientrequest_totalprice 	= $newbenchclientrequest_status =	'';
							$newbenchclientrequest_startdate 		=	@$row->newbenchclientrequest_startdate ? date("d M, Y", strtotime(@$row->newbenchclientrequest_startdate)):'';
							$newbenchclientrequest_enddate 			=	@$row->newbenchclientrequest_enddate ? date("d M, Y", strtotime(@$row->newbenchclientrequest_enddate)):'';
							$newbenchclientrequest_quantity 		=	@$row->newbenchclientrequest_quantity ? number_format(@$row->newbenchclientrequest_quantity) :'';
							$newbenchclientrequest_price 			=	@$row->newbenchclientrequest_price ? number_format(@$row->newbenchclientrequest_price) :'';
							$newbenchclientrequest_totalprice 		=	@$row->newbenchclientrequest_totalprice ? number_format(@$row->newbenchclientrequest_totalprice) :'';
							$newbenchclientrequest_status			=	@$row->newbenchclientrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';

							$emails_startdate 						=	$emails_enddate 	=	$emails_quantity 	=	$emails_price 	=	$emails_totalprice 	= $emails_status =	'';
							$emails_startdate 						=	@$row->emails_startdate ? date("d M, Y", strtotime(@$row->emails_startdate)):'';
							$emails_enddate 						=	@$row->emails_enddate ? date("d M, Y", strtotime(@$row->emails_enddate)):'';
							$emails_quantity 						=	@$row->emails_quantity ? number_format(@$row->emails_quantity) :'';
							$emails_price 							=	@$row->emails_price ? number_format(@$row->emails_price) :'';
							$emails_totalprice 						=	@$row->emails_totalprice ? number_format(@$row->emails_totalprice) :'';
							$emails_status							=	@$row->emails_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 

							$oldcvaccess_startdate 					=	$oldcvaccess_enddate 	=	$oldcvaccess_quantity 	=	$oldcvaccess_price 	=	$oldcvaccess_totalprice 	= $oldcvaccess_status =	'';
							$oldcvaccess_startdate 					=	@$row->oldcvaccess_startdate ? date("d M, Y", strtotime(@$row->oldcvaccess_startdate)):'';
							$oldcvaccess_enddate 					=	@$row->oldcvaccess_enddate ? date("d M, Y", strtotime(@$row->oldcvaccess_enddate)):'';
							$oldcvaccess_quantity 					=	@$row->oldcvaccess_quantity ? number_format(@$row->oldcvaccess_quantity) :'';
							$oldcvaccess_price 						=	@$row->oldcvaccess_price ? number_format(@$row->oldcvaccess_price) :'';
							$oldcvaccess_totalprice 				=	@$row->oldcvaccess_totalprice ? number_format(@$row->oldcvaccess_totalprice) :'';
							$oldcvaccess_status						=	@$row->oldcvaccess_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 

							$path					=	url('super-admin/custompackages/invoiceDownload/'.@$row->id);
							$packageCommentCount	=	DB::table('package_invoice_comment')->select('*')->where('invoice_id', '=', @$row->id)->count();
							$packageComment			=	DB::table('package_invoice_comment')->select('*')->where('invoice_id', '=', @$row->id)->orderBy('package_invoice_comment.id', 'DESC')->get();

							$commentHtml	=  '';
							if($packageCommentCount > 0)
							{
								foreach($packageComment as $pc)
								{
									$commentHtml	.=	'<tr>
												<td>Posted Comment</td>
												<td colspan="2">'.$pc->comment.'</td>
												<td>'. date("d M, Y h:i A", strtotime(@$pc->created_at)).'</td>
											</tr>';	
								}
							}
		
				
				
				
							$html = '<div class="card" style="margin-top:10px;">
							<div class="card-body">
								<div class="table-responsive">			  		
								<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="currentPackage">
									<thead class="">
									<tr class="fw-bolder text-muted">
										<th class="min-w-200px"><h4 class="card-title">Invoice ID</h4></th>
										<th class="min-w-150px"><h4 class="card-title">Payment Mode</h4></th>
										<th class="min-w-150px"><h4 class="card-title">Amount Paid</h4></th>
										<th class="min-w-150px"><h4 class="card-title">Invoice Download</h4></th>
									</tr>
									</thead>
									<tbody class="">
									<tr>
										<td>'.@$invoice_id.'</td>
										<td>'.@$payment_mode.'</td>
										<td>&#x20b9;'.@$grand_total.'</td>
										<td> <a href="'.@$path.'"><i class="bi bi-download" style="color:#006600; height:35px; width:35px;"></i></a></td>
									</tr>
									<tr>
										<td>Comment</td>
										<td colspan="2"><textarea name="" cols="100" rows="3" id="comment_'.@$row->id.'"></textarea></td>
										<td><button type="button" class="btn btn-success font-weight-bolder text-uppercase px-5 py-2 comment_submit" rel="'.@$row->id.'"  >Submit</button></td>
									</tr>
									'.$commentHtml.'
									
									</tbody>
								</table>
								</div>
								<div class="table-responsive">
								<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="currentPackage">  
									<thead class="">
									<tr class="fw-bolder text-muted">
										<th class="min-w-200px">Product</th>
										<th class="min-w-150px">Start Date</th>
										<th class="min-w-150px">End Date</th>
										<th class="min-w-150px">Quantity</th>
										<th class="min-w-150px">Price</th>
										<th class="min-w-150px">Final Price</th>
										<th class="min-w-150px">Status</th>
									</tr>
									</thead>
									<tbody class="">
									<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">CV Access / Received</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$cvaccess_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$cvaccess_enddate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$cvaccess_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$cvaccess_price.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$cvaccess_totalprice.'</a></td>
										<td class="">'.@$cvaccess_status.'</td>
									</tr>
									<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6" >Users</td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$users_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$users_enddate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$users_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$users_price.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$users_totalprice.'</a></td>
										<td class="">'.@$users_status.'</td>
									</tr>
									<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">New Client Request</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_enddate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_price.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_totalprice.'</a></td>
										<td class="">'.@$newclientrequest_status.'</td>
									</tr>
									<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">New Job Request</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newjobrequest_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newjobrequest_enddate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newjobrequest_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newjobrequest_price.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.$newjobrequest_totalprice.'</a></td>
										<td class="">'.@$newjobrequest_status.'</td>
									</tr>
									<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">New Bench Client Request</td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newbenchclientrequest_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newbenchclientrequest_enddate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newbenchclientrequest_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.$newbenchclientrequest_price.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newbenchclientrequest_totalprice.'</a></td>
										<td class="">'.@$newbenchclientrequest_status.'</td>
									</tr>
									<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Emails</td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$emails_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$emails_enddate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$emails_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$emails_price.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$emails_totalprice.'</a></td>
										<td class="">'.@$emails_status.'</td>
									</tr>
									<tr>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Old CV Access</td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$oldcvaccess_startdate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$oldcvaccess_enddate.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$oldcvaccess_quantity.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$oldcvaccess_price.'</a></td>
										<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$oldcvaccess_totalprice.'</a></td>
										<td class="">'.@$oldcvaccess_status.'</td>
									</tr>
									</tbody>
								</table>
								</div>
							</div>
							</div>';
							return $html;
				})
				->addIndexColumn()
				->rawColumns(['Name'])
				->make(true);
    }
	public function data()
    {
		
			//echo "call"; exit; 
        	//$categories 		=	Company::with('package')->withoutGlobalScope('company')->get();	
				$packageInvoice =	DB::table('package_invoice')
									->join('companies', 'companies.id', '=', 'package_invoice.company_id')
									->select('package_invoice.*', 'companies.company_name');	
		
				$packageInvoice->groupBy('package_invoice.id');
				$packageInvoice->orderBy('package_invoice.id', 'DESC');
							
				
			return DataTables::of($packageInvoice)
							
				->editColumn('chkbox', function ($row) {
					return '<div class="form-check form-check-sm form-check-custom form-check-solid">
							<input class="form-check-input" type="checkbox" value="1">
						  </div>';
				})	
				->editColumn('InvoiceNo', function ($row) {
					return '<div class="form-check form-check-sm form-check-custom form-check-solid">
								<a href="javascript:void(0);" data-row-id="'.@$row->id.'" class="fw-bold text-muted d-block fs-7 text-hover-primary show-detail">
									'.@$row->invoice_id.'
								</a> 							
						    </div>';
				})
				->editColumn('Company', function ($row) {
					return '<div class="form-check form-check-sm form-check-custom form-check-solid">
							'.@$row->company_name.'
						  </div>'; 
				})
				->editColumn('PaymentMode', function ($row) { 
					return '<div class="form-check form-check-sm form-check-custom form-check-solid">
							'.@$row->payment_mode.' 
						  </div>';
				})
				->editColumn('AmountPaid', function ($row) {
					return '<div class="form-check form-check-sm form-check-custom form-check-solid">
							'.@$row->grand_total.' 
						  </div>';
				})
				->editColumn('InvoiceDownload', function ($row) {
					return '<div class="form-check form-check-sm form-check-custom form-check-solid">
							<a href="'.url('super-admin/custompackages/invoiceDownload/'.$row->id).'"><i class="bi bi-download" style="color:#006600; height:35px; width:35px;"></i></a>
						  </div>';
				})
				->editColumn('Create', function ($row) {
					$create_at	=	'';
					$create_at	=	date("d M, Y h:i A", strtotime(@$row->create_at));
					
					return '<div class="form-check form-check-sm form-check-custom form-check-solid"> 
							'.@$create_at.' 
						  </div>';
				})
				->addIndexColumn()
				->rawColumns(['chkbox', 'InvoiceNo', 'Company', 'PaymentMode', 'AmountPaid', 'InvoiceDownload', 'Create']) 
				->make(true);
    }
	public function invoiceDownload($id)
    {
	 	
		$this->company      	=	auth()->user();	
		
		
		$this->invoiceRow 	=	DB::table('package_invoice')
								->select('package_invoice.*', 'companies.company_type', 'companies.logo', 'companies.company_email')
								->join('companies', 'companies.id', '=', 'package_invoice.company_id')
								->where('package_invoice.id', '=', $id)
								->first();
		
		$this->client_location_count 	= DB::table('client_location')
											->join('clients', 'clients.id', '=', 'client_location.client_id')
											->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
											->where('clients.company_id', $this->invoiceRow->company_id)
											->where('clients.self_company', 1)
											->orderBy('client_location.id', 'ASC')
											->count();
											
		 $this->client_location 				= DB::table('client_location')
											->join('clients', 'clients.id', '=', 'client_location.client_id')
											->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
											->where('clients.company_id', $this->invoiceRow->company_id)
											->where('clients.self_company', 1)
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
					
		return view('razorpay-invoice.custom_package_invoice', $this->data);
		
		
    }
	public function submitcomment(Request $request)
	{
			$id					=	'';
			$id 				=	$request->id;
			
			$comment			=	'';
			$comment 			=	$request->comment;
			
			$value  			=	array('invoice_id' => @$id, 'comment' => @$comment);
			
			DB::table('package_invoice_comment')->insert($value);
			$comment_id    		=	DB::getPdo()->lastInsertId();
			
			if($comment_id  > 0){
				return Reply::dataOnly(['status' => 'success']);
			}else{
				return Reply::dataOnly(['status' => 'fail']);	
			}
			
        	
	}
	public function viewMore($id)
	{
				
				$this->invoice_id		=	$id;
				
				$this->packageDetail	=	DB::table('package_invoice')
				->join('companies', 'companies.id', '=', 'package_invoice.company_id')
				->select('package_invoice.id', 'package_invoice.create_at','package_invoice.invoice_id', 'package_invoice.payment_mode', 'package_invoice.grand_total','package_invoice.cvaccess_startdate', 'package_invoice.cvaccess_enddate', 'package_invoice.cvaccess_status', 'package_invoice.cvaccess_quantity', 'package_invoice.cvaccess_price',  'package_invoice.cvaccess_totalprice','package_invoice.users_startdate', 'package_invoice.users_enddate', 'package_invoice.users_status', 'package_invoice.users_quantity', 'package_invoice.users_price',  'package_invoice.users_totalprice','package_invoice.newclientrequest_startdate', 'package_invoice.newclientrequest_enddate', 'package_invoice.newclientrequest_status', 'package_invoice.newclientrequest_quantity', 'package_invoice.newclientrequest_price',  'package_invoice.newclientrequest_totalprice','package_invoice.newjobrequest_startdate', 'package_invoice.newjobrequest_enddate', 'package_invoice.newjobrequest_status', 'package_invoice.newjobrequest_quantity', 'package_invoice.newjobrequest_price',  'package_invoice.newjobrequest_totalprice','package_invoice.newbenchclientrequest_startdate', 'package_invoice.newbenchclientrequest_enddate', 'package_invoice.newbenchclientrequest_status', 'package_invoice.newbenchclientrequest_quantity', 'package_invoice.newbenchclientrequest_price',  'package_invoice.newbenchclientrequest_totalprice','package_invoice.emails_startdate', 'package_invoice.emails_enddate', 'package_invoice.emails_status', 'package_invoice.emails_quantity', 'package_invoice.emails_price',  'package_invoice.emails_totalprice','package_invoice.oldcvaccess_startdate', 'package_invoice.oldcvaccess_enddate', 'package_invoice.oldcvaccess_status', 'package_invoice.oldcvaccess_quantity', 'package_invoice.oldcvaccess_price',  'package_invoice.oldcvaccess_totalprice','companies.company_name', 'companies.status', 'companies.company_email', 'companies.company_phone', 'companies.website', 'companies.company_type','companies.company_size', 'companies.founded'
				)
				->where('package_invoice.id', '=', $this->invoice_id)
				->first();
			
			

                $this->package_invoice_comment_count	=	DB:: table('package_invoice_comment')
										->select('package_invoice_comment.*')
										->where('package_invoice_comment.invoice_id', '=', $this->invoice_id) 
										->orderBy('package_invoice_comment.id', 'DESC')
										->count(); 
												
                $this->package_invoice_comment			=	DB:: table('package_invoice_comment')
										->select('package_invoice_comment.*')
										->where('package_invoice_comment.invoice_id', '=', $this->invoice_id) 
										->orderBy('package_invoice_comment.id', 'DESC')
										->get(); 			
			
			 	
									
			    $view 				=	view('super-admin.custompackages.viewMore', $this->data)->render();
        	    return Reply::dataOnly(['status' => 'success', 'view' => $view]);			

	}
	public function editDefaultPackage ()
	{		
			//$this->company_id		=	1;
			$this->defualt_package 	=	DB::table('package_default')->select('package_default.*')->orderBy('package_default.id', 'ASC')->first();							
			$view 					=	view('super-admin.defaultpackages.editDefaultPackage', $this->data)->render();
			
        	return Reply::dataOnly(['status' => 'success', 'view' => $view]);				
	}
	public function update(Request $request) 
	{
				
					$cvaccess_quantity				=	$cvaccess_validity	=	$cvaccess_status	=	'';
					$cvaccess_quantity				=	@$request->cvaccess_quantity;
					$cvaccess_validity				=	@$request->cvaccess_validity;
					$cvaccess_status				=	@$request->cvaccess_status;
					
					$users_quantity					=	$users_validity	=	$users_status	=	'';
					$users_quantity					=	@$request->users_quantity;
					$users_validity					=	@$request->users_validity;
					$users_status					=	@$request->users_status;
					
					$newclientrequest_quantity		=	$newclientrequest_validity	=	$newclientrequest_status	=	'';
					$newclientrequest_quantity		=	@$request->newclientrequest_quantity;
					$newclientrequest_validity		=	@$request->newclientrequest_validity;
					$newclientrequest_status		=	@$request->newclientrequest_status;
					
					$newbenchclientrequest_quantity	=	$newbenchclientrequest_validity	=	$newbenchclientrequest_status	=	'';
					$newbenchclientrequest_quantity	=	@$request->newbenchclientrequest_quantity;
					$newbenchclientrequest_validity	=	@$request->newbenchclientrequest_validity;
					$newbenchclientrequest_status	=	@$request->newbenchclientrequest_status;
					
					$newjobrequest_quantity			=	$newjobrequest_validity	=	$newjobrequest_status	=	'';
					$newjobrequest_quantity			=	@$request->newjobrequest_quantity;
					$newjobrequest_validity			=	@$request->newjobrequest_validity;
					$newjobrequest_status			=	@$request->newjobrequest_status;
					
					$emails_quantity				=	$emails_validity	=	$emails_status	=	'';
					$emails_quantity				=	@$request->emails_quantity;
					$emails_validity				=	@$request->emails_validity;
					$emails_status					=	@$request->emails_status;
					
					$oldcvaccess_quantity			=	$oldcvaccess_validity	=	$oldcvaccess_status	=	'';
					$oldcvaccess_quantity			=	@$request->oldcvaccess_quantity;
					$oldcvaccess_validity			=	@$request->oldcvaccess_validity;
					$oldcvaccess_status				=	@$request->oldcvaccess_status;
					
					
					$updateArr 						= array(
																'cvaccess_quantity' 			=> @$cvaccess_quantity,
																'cvaccess_validity' 			=> @$cvaccess_validity,
																'cvaccess_status' 				=> @$cvaccess_status,
																
																'users_quantity' 				=> @$users_quantity,
																'users_validity' 				=> @$users_validity,
																'users_status' 					=> @$users_status,
																
																'newclientrequest_quantity' 	=> @$newclientrequest_quantity,
																'newclientrequest_validity' 	=> @$newclientrequest_validity,
																'newclientrequest_status' 		=> @$newclientrequest_status,
																
																'newbenchclientrequest_quantity'=> @$newbenchclientrequest_quantity,
																'newbenchclientrequest_validity'=> @$newbenchclientrequest_validity,
																'newbenchclientrequest_status' 	=> @$newbenchclientrequest_status,
																
																'newjobrequest_quantity' 		=> @$newjobrequest_quantity,
																'newjobrequest_validity' 		=> @$newjobrequest_validity,
																'newjobrequest_status' 			=> @$newjobrequest_status,
																
																'emails_quantity' 				=> @$emails_quantity,
																'emails_validity' 				=> @$emails_validity,
																'emails_status' 				=> @$emails_status,
																
																'oldcvaccess_quantity' 			=> @$oldcvaccess_quantity,
																'oldcvaccess_validity' 			=> @$oldcvaccess_validity,
																'oldcvaccess_status' 			=> @$oldcvaccess_status,
																
																
															);	
					DB::table('package_default')->limit(1)->update($updateArr);
					
					$today 		= 	date('d M, Y');
					$datatable	=	'';
					
					//............................  CV Access / Received ........................................				
					$ncvaccess_quantity		=	$ncvaccess_validity	= $ncvaccess_endDate = $ncvaccess_status = '';    
					$ncvaccess_quantity		=	@$cvaccess_quantity 	? number_format(@$cvaccess_quantity): 0;
					$ncvaccess_validity		=	@$cvaccess_validity 	? number_format(@$cvaccess_validity): 0;
					if(@$cvaccess_validity > 0){
						$cvaccess_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + '.@$cvaccess_validity.' days'));
					}else{
						$cvaccess_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + 0 days'));
					}
					$ncvaccess_status		=   @$cvaccess_status == 1 	? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
					
					$datatable	.=	'<tr>
						<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">CV Access / Received</a></td>
						<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$ncvaccess_quantity.'</a></td>
						<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$ncvaccess_validity.'</a></td>
						<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$today.'</a></td>
						<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$cvaccess_endDate.'</a></td>
						<td class="">'.@$ncvaccess_status.'</td>
					</tr>';

					//................................ Users ...............................................			
					$nusers_quantity		=	$nusers_validity	= $nusers_endDate = $nusers_status = '';    
					$nusers_quantity		=	@$users_quantity 	? number_format(@$users_quantity): 0;
					$nusers_validity		=	@$users_validity 	? number_format(@$users_validity): 0;
					if(@$users_validity > 0){
						$nusers_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + '.@$users_validity.' days'));
					}else{
						$nusers_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + 0 days'));
					}
					$nusers_status		=   @$users_status == 1 	? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
										
					$datatable	.=	'<tr>
						<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Users</a></td>
						<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nusers_quantity.'</a></td>
						<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nusers_validity.'</a></td>
						<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$today.'</a></td>
						<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nusers_endDate.'</a></td>
						<td class="">'.@$nusers_status.'</td>
					</tr>';

					//................................  New Client Request  ...............................................			
					$nnewclientrequest_quantity		=	$nnewclientrequest_validity	= $nnewclientrequest_endDate = $nnewclientrequest_status = '';    
					$nnewclientrequest_quantity		=	@$newclientrequest_quantity 	? number_format(@$newclientrequest_quantity): 0;
					$nnewclientrequest_validity		=	@$newclientrequest_validity 	? number_format(@$newclientrequest_validity): 0;
					if(@$newclientrequest_validity > 0){
						$nnewclientrequest_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + '.@$newclientrequest_validity.' days'));
					}else{
						$nnewclientrequest_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + 0 days'));
					}
					$nnewclientrequest_status		=   @$newclientrequest_status == 1 	? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
					
					$datatable	.=	'<tr>
						<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">New Client Request</a></td>
						<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewclientrequest_quantity.'</a></td>
						<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewclientrequest_validity.'</a></td>
						<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$today.'</a></td>
						<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewclientrequest_endDate.'</a></td>
						<td class="">'.@$nnewclientrequest_status.'</td>
					    </tr>';

						//................................  New Bench Client Request  ...............................................			
						$nnewbenchclientrequest_quantity		=	$nnewbenchclientrequest_validity	= $nnewbenchclientrequest_endDate = $nnewbenchclientrequest_status = '';    
						$nnewbenchclientrequest_quantity		=	@$newbenchclientrequest_quantity 	? number_format(@$newbenchclientrequest_quantity): 0;
						$nnewbenchclientrequest_validity		=	@$newbenchclientrequest_validity 	? number_format(@$newbenchclientrequest_validity): 0;
						if(@$newbenchclientrequest_validity > 0){
							$nnewbenchclientrequest_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + '.@$newbenchclientrequest_validity.' days'));
						}else{
							$nnewbenchclientrequest_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + 0 days'));
						}
						$nnewbenchclientrequest_status		=   @$newbenchclientrequest_status == 1 	? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
											
						$datatable	.=	'<tr>
							<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">New Bench Client Request</a></td>
							<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewbenchclientrequest_quantity.'</a></td>
							<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewbenchclientrequest_validity.'</a></td>
							<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$today.'</a></td>
							<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewbenchclientrequest_endDate.'</a></td>
							<td class="">'.@$nnewbenchclientrequest_status.'</td>
						</tr>';


						//................................  New Job Request  ...............................................			
						$nnewjobrequest_quantity		=	$nnewjobrequest_validity	= $nnewjobrequest_endDate = $nnewjobrequest_status = '';    
						$nnewjobrequest_quantity		=	@$newjobrequest_quantity 	? number_format(@$newjobrequest_quantity): 0;
						$nnewjobrequest_validity		=	@$newjobrequest_validity 	? number_format(@$newjobrequest_validity): 0;
						if(@$newjobrequest_validity > 0){
							$nnewjobrequest_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + '.@$newjobrequest_validity.' days'));
						}else{
							$nnewjobrequest_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + 0 days'));
						}
						$nnewjobrequest_status		=   @$newjobrequest_status == 1 	? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
					
						$datatable	.=	'<tr>
							<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">New Job Request</a></td>
							<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewjobrequest_quantity.'</a></td>
							<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewjobrequest_validity.'</a></td>
							<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$today.'</a></td>
							<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nnewjobrequest_endDate.'</a></td>
							<td class="">'.@$nnewjobrequest_status.'</td>
						</tr>';

						//................................  Emails  ...............................................			
						$nemails_quantity		=	$nemails_validity	= $nemails_endDate = $nemails_status = '';    
						$nemails_quantity		=	@$emails_quantity 	? number_format(@$emails_quantity): 0;
						$nemails_validity		=	@$emails_validity 	? number_format(@$emails_validity): 0;
						if(@$emails_validity > 0){
							$nemails_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + '.@$emails_validity.' days'));
						}else{
							$nemails_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + 0 days'));
						}
						$nemails_status		=   @$emails_status == 1 	? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
											
						$datatable	.=	'<tr>
							<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Emails</a></td>
							<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nemails_quantity.'</a></td>
							<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nemails_validity.'</a></td>
							<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$today.'</a></td>
							<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$nemails_endDate.'</a></td>
							<td class="">'.@$nemails_status.'</td>
						    </tr>';
	

							//................................  Old CV Access  ...............................................			
							$noldcvaccess_quantity		=	$noldcvaccess_validity	= $noldcvaccess_endDate = $noldcvaccess_status = '';    
							$noldcvaccess_quantity		=	@$oldcvaccess_quantity 	? number_format(@$oldcvaccess_quantity): 0;
							$noldcvaccess_validity		=	@$oldcvaccess_validity 	? number_format(@$oldcvaccess_validity): 0;
							if(@$oldcvaccess_validity > 0){
								$noldcvaccess_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + '.@$oldcvaccess_validity.' days'));
							}else{
								$noldcvaccess_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + 0 days'));
							}
							$noldcvaccess_status		=   @$oldcvaccess_status == 1 	? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
												
							$datatable	.=	'<tr>
								<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Old CV Access</a></td>
								<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$noldcvaccess_quantity.'</a></td>
								<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$noldcvaccess_validity.'</a></td>
								<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$today.'</a></td>
								<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$noldcvaccess_endDate.'</a></td>
								<td class="">'.@$noldcvaccess_status.'</td>
							    </tr>';
							
                            // 				
			
					
					
					return Reply::dataOnly(['status' => 'success', 'datatable'=> $datatable]); 
					

					


        
    }
	
}
