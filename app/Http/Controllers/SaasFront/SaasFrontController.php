<?php
namespace App\Http\Controllers\SaasFront;

use Artisan;

use App\Helper\GeneralMethod;
use App\Models\ClientFeedback;
use App\Models\Company;
use App\Models\FooterMenu;
use App\Models\FooterSetting;
use App\Models\FrontCmsHeader;
use App\Models\FrontIconFeature;
use App\Models\FrontImageFeature;
use App\Models\GlobalSetting;
use App\Helper\Reply;
use App\Http\Requests\ContactForm;
use App\Jobs\SendEmailJob;
use App\Jobs\QueueMailService;
use App\Jobs\QueueMassMailService;
use App\Jobs\QueueNotificationMailService;
use App\Http\Requests\RegisterForm;
use App\Models\LanguageSetting;
use App\Mail\ContactMail;
use App\Notifications\EmailVerification;
use App\Notifications\EmailVerificationSuccess;
use App\Notifications\NewCompanyRegister;
use App\Models\Package;
use App\Models\PackageOrderItem;
use App\Models\Role as CustomRole;
use App\Models\ThemeSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Mail\CompanyProfileEmailConfirmation;
use App\Mail\CompanyAccountConfirmationTeam;
use App\Services\MailService;
use App\Services\ElasticService;
use App\Jobs\ElasticCompanyIndex;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;


class SaasFrontController extends SaasFrontBaseController
{
     protected $mailService;
	public function __construct(MailService $mailService,ElasticService $elasticService)
	{
		parent::__construct();
		$this->defaultFrontDetail = FooterSetting::first();
		$this->pageTitle = 'app.register';
		$this->mailService = $mailService;
        $this->elasticService = $elasticService;
	}

	public function index()
	{
		$imageFeaturesCount = FrontImageFeature::select('id', 'language_settings_id')->where('language_settings_id', $this->localeLanguage->id)->count();
		$iconFeaturesCount = FrontIconFeature::select('id', 'language_settings_id')->where('language_settings_id', $this->localeLanguage->id)->count();
		$feedbackCount = ClientFeedback::select('id', 'language_settings_id')->where('language_settings_id', $this->localeLanguage->id)->count();
		$footerMenuCount = FooterMenu::select('id', 'language_settings_id')->where('language_settings_id', $this->localeLanguage->id)->count();
		$footerSettingsCount = FooterSetting::select('id', 'language_settings_id')->where('language_settings_id', $this->localeLanguage->id)->count();

		$this->imageFeatures = FrontImageFeature::where('language_settings_id', $imageFeaturesCount > 0 ? $this->localeLanguage->id : $this->englishLangId)->get();
		$this->iconFeatures = FrontIconFeature::where('language_settings_id', $iconFeaturesCount > 0 ? $this->localeLanguage->id : $this->englishLangId)->get();
		$this->feedbacks = ClientFeedback::where('language_settings_id', $feedbackCount > 0 ? $this->localeLanguage->id : $this->englishLangId)->get();
		$this->packages = Package::where('is_trial', 0)->where('status', 1)->get();
		$this->frontDetail = FooterSetting::where('language_settings_id', $footerSettingsCount > 0 ? $this->localeLanguage->id : $this->englishLangId)->first();
		$this->footerSettings = FooterMenu::where('language_settings_id', $footerMenuCount > 0 ? $this->localeLanguage->id : $this->englishLangId)->get();

		$currentDate = Carbon::now()->format('Y-m-d');
		$this->featuredCompanies = Company::where('status', 'active')
			->where(function ($query) use ($currentDate) {
				$query->whereNull('featured_start_date')
					->orWhere(DB::raw('DATE(`featured_start_date`)'), '<=', $currentDate);
			})
			->where(function ($query) use ($currentDate) {
				$query->whereNull('featured_end_date')
					->orWhere(DB::raw('DATE(`featured_end_date`)'), '>=', $currentDate);
			})
			->where(function ($query) use ($currentDate) {
				$query->whereNull('licence_expire_on')
					->orWhere(DB::raw('DATE(`licence_expire_on`)'), '>=', $currentDate);
			})
			->where('featured', 1)->get();
		if (auth()->check()) {
			$companyRow  			=	DB::table('companies')->select('company_type')->where('id', auth()->user()->company_id)->first();
			if(auth()->user()->is_superadmin) {
										
				return redirect(route('superadmin.dashboard.index'));
			
			}
			
			if(@$companyRow->company_type == 1){   					//Staffing Partners
				return redirect('service_company/dashboard');
				exit;
			}
			else if(@$companyRow->company_type == 2){   				//Service Based Companies
				return redirect('service_company/dashboard');
				exit;	
			}
			else if(@$companyRow->company_type == 3){				//Product Based hhhhh@yopmail.com
				// return redirect('product_company/dashboard');	
				return redirect('service_company/dashboard');	
				exit;
			}
		}
		
		return view('auth.login', $this->data);
		// return view('saas-front.index', $this->data);
	}

	public function teamReg($companyId, $invitationId)
	{
		Artisan::call('route:clear');
		Artisan::call('cache:clear');

		$currentCompany = $currentUserId = '';
		$currentCompany = base64_decode($companyId);
		$currentUserId = base64_decode($invitationId);

		// dd($currentCompany,$currentUserId);

		$count = 0;
		$count = DB::table('users')
			->select('users.id', 'users.name', 'name.email', 'name.email_verification_code')
			->where('users.company_id', '=', $currentCompany)
			->where('users.id', '=', $currentUserId)
			->where('users.status', '=', 'inactive')
			->count();

		if ($count == 0) {
			abort(404);
		} else {
			$this->companyid = $currentCompany;
			$this->userData = DB::table('users')
				->select('users.id', 'users.name', 'users.email', 'users.email_verification_code')
				->where('users.company_id', '=', $currentCompany)
				->where('users.id', '=', $currentUserId)
				->first();
			if (!empty($this->userData->email_verification_code)) {

				$user_id = $this->userData->id;

				DB::table('users')->where('id', @$user_id)->limit(1)->update(array('remember_token' => @$user_id));

				return view('saas-front.teamReg', $this->data);
			} else {
				abort(404);
			}
		}
	}

	public function termsAndConditions()
	{
		$this->global_settings = DB::table('global_settings')->select('terms_and_conditions')->first();
		return view('saas-front.terms-and-condition', $this->data);
	}

	public function paymenttermsAndConditions()
	{
		$this->global_settings = DB::table('global_settings')->select('payment_policy')->first();
		return view('saas-front.payment-terms-and-condition', $this->data);
	}
	public function privacyPolicy()
	{
		return view('saas-front.privacy-policy', $this->data);
	}
	public function refundPolicy()
	{
		return view('saas-front.refund-policy', $this->data);
	}
	public function help_page()
	{
		return view('saas-front.help_page', $this->data);
	}
	public function invitationCompanyRegisterDirectAccess($code='')
	{
       
		if (empty($code)) {
			return redirect('register');
		} else {
			$decode = base64_decode($code);

			$userRowData=DB::table('users')->select('*')->where('root_user', '=',1)->where('company_id', $decode)->first();

			
			$userRowCount=DB::table('users')->select('*')->where('root_user', '=',1)->where('company_id', $decode)->count();
			if ($userRowCount > 0) {
					$this->direct_open_link= false;
					$this->direct_user= base64_encode($userRowData->id);
				return view('saas-front.invitationCompanyRegister', $this->data);
			} else {
				$this->direct_open_link= false;
				$this->company_id='';
				return view('saas-front.invitationCompanyRegister', $this->data);
			}
		}

	}
	public function invitationCompanyRegister($code)
	{
		
		if (empty($code)) {
			abort(404);
		} else {
			$decode = base64_decode($code);

			$this->userRowData = DB::table('staffing_company_invitation')
					->select('*')
					->where('id', $decode)
					->where('to_status', 0)
					->first();

			if($this->userRowData){		

				$user = DB::table('users')
				->join('companies', 'companies.id', 'users.company_id')
				->select('users.id', 'users.name', 'users.email', 'users.email_verification_code', 'companies.company_name')
				->where('users.email', '=',$this->userRowData->to_email)
				->where('users.status', '=', 'active')
				->first();

				// dd($user);
				
				if($user){
					$userRowCount = DB::table('staffing_company_invitation')
					->select('*')
					->where('id', $decode)
					// ->where('to_invitation_status', 0)
					// ->where('to_status', 0)
					->count();
                    // dd($userRowCount);
					if($userRowCount){
						

						$company_id = $this->userRowData->to_company_id;
						$event_id = 1;
						$from_company = $this->userRowData->from_company_id;
						$invitation_id = $this->userRowData->id;

										

						$user_id = $user->id;
						$updateArr = array('to_invitation_status' => $event_id);
						DB::table('staffing_company_invitation')->where('id', $invitation_id)->limit(1)->update($updateArr);
						$invitationRow = DB::table('staffing_company_invitation')->select('*')->where('id', $invitation_id)->first();
						$clientRow = DB::table('clients')->select('*')->where('company_id', $company_id)->where('self_company', 1)->first();
						$from_company_id = $invitationRow->from_company_id;
						$from_client_id = $invitationRow->from_client_id;
						$from_user_id = $invitationRow->from_user_id;
						$from_status = $invitationRow->from_status;
						$from_created_at = $invitationRow->from_created_at;
						$to_company_id = $invitationRow->to_company_id;
						$to_email = $invitationRow->to_email;
						$to_status = $invitationRow->to_status;
						$job_id = $invitationRow->job_id;
						$to_invitation_status = $invitationRow->to_invitation_status;

						$values = array(
							'from_company_id' => $from_company_id,
							'from_client_id' => $from_client_id,
							'from_user_id' => $from_user_id,
							'from_status' => 1,
							'from_created_at' => date('Y-m-d'),
			
							'to_company_id' => $company_id,
							'to_client_id' => @$clientRow->id,
							'to_user_id' => @$user_id,
							'to_status' => 1,
							'to_created_at' => date('Y-m-d'),
			
							'final_status' => 1,
							'created_at' => date('Y-m-d'),
							'updated_at' => date('Y-m-d')
						);
						DB::table('staffing_company_relation')->insert($values);
						$relationId 		=	DB::getPdo()->lastInsertId();
						GeneralMethod::appendClientNestedData($relationId);

						$updateTime = now()->toIso8601String();

						// 3. Execute update with debugging
						$updateParams = [
							'index' => ELASTIC_CLIENT_REQUEST,
							'id' =>$invitation_id,
							'body' => [
								'doc' => [
									'to_invitation_status' => 1,
									'to_user_id' => @$user_id,
									'to_company_id' => $company_id,
									'to_client_id' => @$clientRow->id,
									'from_status' => 1,
									'to_status' => 1,
									'final_status' => 1,
									'updated_at' => $updateTime
								],
								'doc_as_upsert' => false // Changed to false since we verified existence
							],
							'refresh' => true // Force immediate refresh
						];
						$this->elasticService->getClient()->update($updateParams);

						$cachekeys = [
							'allclients_version_user_'.$user_id,
							'alldirectclient_version_user_'.$user_id,
							'allconsultancy_version_user_'.$from_user_id
						];
						foreach ($cachekeys as $key) {
							GeneralMethod::cacheClean($key);
						}
					}


					return redirect('service_company/clients/direct_client/0');
				    exit;


				}
			}

			
			$userRowCount = DB::table('staffing_company_invitation')
				->select('*')
				->where('id', $decode)
				->where('to_status', 0)
				->count();
			if ($userRowCount > 0) {
				$this->userRowData = DB::table('staffing_company_invitation')
					->select('*')
					->where('id', $decode)
					->where('to_status', 0)
					->first();
					$this->direct_open_link= true;
					$this->direct_user='';
				return view('saas-front.invitationCompanyRegister', $this->data);
			} else {
				
				$this->direct_open_link= true;
				$this->direct_user='';

				return view('saas-front.invitationCompanyRegister', $this->data);
			}
		}

	}
	public function submitContact(ContactForm $request)
	{
		Mail::send(new ContactMail($request));
		return Reply::dataOnly(['status' => 'success']);
	}
	public function companyRegister(Request $request)
	{
		
		// $package_default = DB::table('packages')->select('packages.*')->where('is_trial','=',1)->first();

         // 			dd($package_default);
         
       
        //  dd($request->all());

			  
		$validator = Validator::make($request->all(), [
				'name' => 'required|string|max:255',
				'email' => 'required|email|unique:users',
				'password_confirmation' => 'required|same:password',
				'password' => 'required|confirmed',
				'toc' => 'required',
				'company_name' => 'required',
				'company_type' => 'required',
				'website' => 'required',
				'contact_number' => 'required',
				'contact_code' => 'required',
        ]);
        // dd($validator->errors());

        if ($validator->fails()) {
            
            	return Reply::dataOnly(['status' => 'error', 'validation' => $validator->errors()]);
            	
        }

           $company = new Company();
		try {
			$company->company_name = $request->company_name;
			$company->company_email = $request->email;
			$company->website = $request->website;
			// $company->company_size = $request->company_size;
			$company->company_phone = $request->contact_number;
			$company->calling_code = @$request->contact_code;
			$company->company_type = $request->company_type;
			$company->app_duplicate = 2;
			$company->app_duplicate_days = 60;
			$company->bench_show_mode	= 2;
			
			//$company->career_page_link 	= str_slug($request->career_page_link, '-'); 
			$company->job_opening_text = 'Welcome!';
			$company->job_opening_title = 'We want people to thrive. We believe you do your best work when you feel your best.';
			$company->timezone = 'Asia/Kolkata';
            if($request->company_type==1){
				$company->industries = 131;
			}
			

			if (module_enabled('Subdomain')) {
				$company->sub_domain = $request->sub_domain;
			}
			$company->save();
			// dd("dld");
			// ACCESS DEFAULT PACKAGE
			$today = date('Y-m-d');
			// $package_default = DB::table('package_default')->select('package_default.*')->first();
			$package_default = Package::select('packages.*')->first();
			$package_default_details = Package::select('package_features.id','package_details.quantity','package_features.field_name','package_details.price','package_details.sub_total as finalprice','package_details.status','package_features.id as features_id')->join('package_details','package_details.package_id','=','packages.id')->join('package_features','package_details.package_feature_id','=','package_features.id')->where('package_details.package_id','=',$package_default->id)->where('is_trial','=',1)->get()->toArray();

			
			$duration_endDate = '';
			if (@$package_default->trial_duration > 0) {
				// dd(@$package_default->trial_duration);
				$duration_endDate = date('Y-m-d', strtotime(date('Y-m-d') . ' + ' . @$package_default->trial_duration . ' days'));
			} else {
				$duration_endDate = date('Y-m-d', strtotime(date('Y-m-d') . ' + 0 days'));
			}
			$t						=	date('Y-m-d H:i:s');
			$invoice_id				=	strtotime($t);
			// $company =
			$order_val				=	array('company_id' => @$company->id, 'company_type' => @$company->company_type);
			$invoice_val			=	array('company_id' => @$company->id, 'invoice_id' => @$invoice_id, 'company_type' => @$company->company_type);
			$active_val  			=	array('company_id' => @$company->id,'company_type' => @$company->company_type);
			$subtotal= 0;

			if(!empty($package_default_details)){
				foreach($package_default_details as $sArr){
					if($sArr['field_name'] == 'cvaccess') { 

						$order_val['cvaccess_startdate'] 	=	$invoice_val['cvaccess_startdate'] 	=  	date('Y-m-d');
						$order_val['cvaccess_enddate'] 		=	$invoice_val['cvaccess_enddate'] 	=  	@$duration_endDate;
						$order_val['cvaccess_status'] 		=	$invoice_val['cvaccess_status'] 	= 	@$sArr['status'];
						$order_val['cvaccess_quantity'] 	=	$invoice_val['cvaccess_quantity'] 	=  	@$sArr['quantity'];
						$order_val['cvaccess_price'] 		=	$invoice_val['cvaccess_price'] 		=  	@$sArr['price'];
						$order_val['cvaccess_totalprice'] 	=	$invoice_val['cvaccess_totalprice'] = 	@$sArr['finalprice'];
						
						$active_val['cvaccess_startdate']	=	date('Y-m-d');
						$active_val['cvaccess_enddate']		=	@$duration_endDate;
						$active_val['cvaccess_status']		=	@$sArr['status'];
						$active_val['cvaccess_quantity']	=	@$sArr['quantity'];
						$active_val['cvaccess_used']		= 	0;
						$active_val['cvaccess_balance']		=	@$sArr['quantity'];	
																			
						$subtotal							=  $subtotal + @$sArr['finalprice'];												 
																			
					}else if($sArr['field_name'] == 'users') {
						
						$order_val['users_startdate'] 		=	$invoice_val['users_startdate'] 	=  	date('Y-m-d');
						$order_val['users_enddate'] 		=	$invoice_val['users_enddate'] 		=  	@$duration_endDate;
						$order_val['users_status'] 			=	$invoice_val['users_status'] 		= 	@$sArr['status'];
						$order_val['users_quantity'] 		=	$invoice_val['users_quantity'] 		=  	@$sArr['quantity'];
						$order_val['users_price'] 			=	$invoice_val['users_price'] 		=  	@$sArr['price'];
						$order_val['users_totalprice'] 		=	$invoice_val['users_totalprice'] 	= 	@$sArr['finalprice'];
						
						$active_val['users_startdate']		=	date('Y-m-d');
						$active_val['users_enddate']		=	@$duration_endDate;
						$active_val['users_status']			=	@$sArr['status'];
						$active_val['users_quantity']		=	@$sArr['quantity'];
						$active_val['users_used']			= 	0;
						$active_val['users_balance']		=	@$sArr['quantity'];	
																			
						$subtotal							=  $subtotal + @$sArr['finalprice'];	
						
					}else if($sArr['field_name'] == 'new_bench_client_request') {
						
						$order_val['newbenchclientrequest_startdate'] 	=	$invoice_val['newbenchclientrequest_startdate'] 	=  date('Y-m-d');
						$order_val['newbenchclientrequest_enddate'] 	=	$invoice_val['newbenchclientrequest_enddate'] 	=  	@$duration_endDate;
						$order_val['newbenchclientrequest_status'] 		=	$invoice_val['newbenchclientrequest_status'] 		= 	@$sArr['status'];
						$order_val['newbenchclientrequest_quantity'] 	=	$invoice_val['newbenchclientrequest_quantity'] 		=  	@$sArr['quantity'];
						$order_val['newbenchclientrequest_price'] 		=	$invoice_val['newbenchclientrequest_price'] 		=  	@$sArr['price'];
						$order_val['newbenchclientrequest_totalprice'] 	=	$invoice_val['newbenchclientrequest_totalprice'] 	= 	@$sArr['finalprice'];
						
						$active_val['newbenchclientrequest_startdate']	=	date('Y-m-d');
						$active_val['newbenchclientrequest_enddate']	=	@$duration_endDate;
						$active_val['newbenchclientrequest_status']		=	@$sArr['status'];
						$active_val['newbenchclientrequest_quantity']	=	@$sArr['quantity'];
						$active_val['newbenchclientrequest_used']		= 	0;
						$active_val['newbenchclientrequest_balance']	=	@$sArr['quantity'];	
																			
						$subtotal										=	$subtotal + @$sArr['finalprice'];
					}else if($sArr['field_name'] == 'emails') {
						
						$order_val['emails_startdate'] 	=	$invoice_val['emails_startdate'] 	=  	date('Y-m-d');
						$order_val['emails_enddate'] 	=	$invoice_val['emails_enddate'] 		=  	@$duration_endDate;
						$order_val['emails_status'] 	=	$invoice_val['emails_status'] 		= 	@$sArr['status'];
						$order_val['emails_quantity'] 	=	$invoice_val['emails_quantity'] 	=  	@$sArr['quantity'];
						$order_val['emails_price'] 		=	$invoice_val['emails_price'] 		=  	@$sArr['price'];
						$order_val['emails_totalprice'] =	$invoice_val['emails_totalprice'] 	= 	@$sArr['finalprice'];
						
						$active_val['emails_startdate']	=	date('Y-m-d');
						$active_val['emails_enddate']	=	@$duration_endDate;
						$active_val['emails_status']	=	@$sArr['status'];
						$active_val['emails_quantity']	=	@$sArr['quantity'];
						$active_val['emails_used']		= 	0;
						$active_val['emails_balance']	=	@$sArr['quantity'];	
																			
						$subtotal						=	$subtotal + @$sArr['finalprice'];
						
					}else if($sArr['field_name'] == 'oldcvaccess') {
						
						$order_val['oldcvaccess_startdate'] 	=	$invoice_val['oldcvaccess_startdate'] 	=  	date('Y-m-d');
						$order_val['oldcvaccess_enddate'] 		=	$invoice_val['oldcvaccess_enddate'] 	=  	@$duration_endDate;
						$order_val['oldcvaccess_status'] 		=	$invoice_val['oldcvaccess_status'] 		= 	@$sArr['status'];
						$order_val['oldcvaccess_quantity'] 		=	$invoice_val['oldcvaccess_quantity'] 	=  	@$sArr['quantity'];
						$order_val['oldcvaccess_price'] 		=	$invoice_val['oldcvaccess_price'] 		=  	@$sArr['price'];
						$order_val['oldcvaccess_totalprice'] 	=	$invoice_val['oldcvaccess_totalprice'] 	= 	@$sArr['finalprice'];
						
						$active_val['oldcvaccess_startdate']	=	date('Y-m-d');
						$active_val['oldcvaccess_enddate']		=	@$duration_endDate;
						$active_val['oldcvaccess_status']		=	@$sArr['status'];
						$active_val['oldcvaccess_quantity']		=	@$sArr['quantity'];
						$active_val['oldcvaccess_used']			= 	0;
						$active_val['oldcvaccess_balance']		=	@$sArr['quantity'];	
																			
						$subtotal								=	$subtotal + @$sArr['finalprice'];
						
					}else if($sArr['field_name'] == 'new_client_request') {
						
						$order_val['newclientrequest_startdate'] 	=	$invoice_val['newclientrequest_startdate'] 	=  	date('Y-m-d');
						$order_val['newclientrequest_enddate'] 		=	$invoice_val['newclientrequest_enddate'] 	=  	@$duration_endDate;
						$order_val['newclientrequest_status'] 		=	$invoice_val['newclientrequest_status'] 	= 	@$sArr['status'];
						$order_val['newclientrequest_quantity'] 	=	$invoice_val['newclientrequest_quantity'] 	=  	@$sArr['quantity'];
						$order_val['newclientrequest_price'] 		=	$invoice_val['newclientrequest_price'] 		=  	@$sArr['price'];
						$order_val['newclientrequest_totalprice'] 	=	$invoice_val['newclientrequest_totalprice'] = 	@$sArr['finalprice'];
						
						$active_val['newclientrequest_startdate']	=	date('Y-m-d');
						$active_val['newclientrequest_enddate']		=	@$duration_endDate;
						$active_val['newclientrequest_status']		=	@$sArr['status'];
						$active_val['newclientrequest_quantity']	=	@$sArr['quantity'];
						$active_val['newclientrequest_used']		= 	0;
						$active_val['newclientrequest_balance']		=	@$sArr['quantity'];	
																			
						$subtotal									=  $subtotal + @$sArr['finalprice'];
																		
					}else if($sArr['field_name'] == 'new_job_request') {
						
						$order_val['newjobrequest_startdate'] 	=	$invoice_val['newjobrequest_startdate'] 	=  	date('Y-m-d');
						$order_val['newjobrequest_enddate'] 		=	$invoice_val['newjobrequest_enddate'] 	=  	@$duration_endDate;
						$order_val['newjobrequest_status'] 		=	$invoice_val['newjobrequest_status'] 		= 	@$sArr['status'];
						$order_val['newjobrequest_quantity'] 	=	$invoice_val['newjobrequest_quantity'] 		=  	@$sArr['quantity'];
						$order_val['newjobrequest_price'] 		=	$invoice_val['newjobrequest_price'] 		=  	@$sArr['price'];
						$order_val['newjobrequest_totalprice'] 	=	$invoice_val['newjobrequest_totalprice'] 	= 	@$sArr['finalprice'];
						
						$active_val['newjobrequest_startdate']	=	date('Y-m-d');
						$active_val['newjobrequest_enddate']	=	@$duration_endDate;
						$active_val['newjobrequest_status']		=	@$sArr['status'];
						$active_val['newjobrequest_quantity']	=	@$sArr['quantity'];
						$active_val['newjobrequest_used']		= 	0;
						$active_val['newjobrequest_balance']	=	@$sArr['quantity'];	
																			
						$subtotal								=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'boostkeywords') {
						
						$order_val['boostkeywords_startdate'] 		=	$invoice_val['boostkeywords_startdate'] 	=  	date('Y-m-d');
						$order_val['boostkeywords_enddate'] 		=	$invoice_val['boostkeywords_enddate'] 		=  @$duration_endDate;
						$order_val['boostkeywords_status'] 			=	$invoice_val['boostkeywords_status'] 		= 	@$sArr['status'];
						$order_val['boostkeywords_quantity'] 		=	$invoice_val['boostkeywords_quantity'] 		=  	@$sArr['quantity'];
						
						$order_val['boostkeywords_price'] 			=	$invoice_val['boostkeywords_price'] 		=  	@$sArr['price'];
						$order_val['boostkeywords_totalprice'] 		=	$invoice_val['boostkeywords_totalprice'] 	= 	@$sArr['finalprice'];
						$string_version 							=	implode(' , ', @$sArr['keywords']);
						$order_val['boostkeywords_text'] 			=	$invoice_val['boostkeywords_text'] 			=  	@$string_version;
						
						$active_val['boostkeywords_startdate']		=	date('Y-m-d');
						$active_val['boostkeywords_enddate']		=	@$duration_endDate;
						$active_val['boostkeywords_status']			=	@$sArr['status'];
						$active_val['boostkeywords_text']			=	@$string_version;
						
						$active_val['boostkeywords_quantity']		=	@$sArr['quantity'];
						$active_val['boostkeywords_used']			= 	0;
						$active_val['boostkeywords_balance']		=	@$sArr['quantity'];	
																			
						$subtotal									=	$subtotal + @$sArr['finalprice'];
						
						
					}else if($sArr['field_name'] == 'InternalDatabaseaccess') {
						
							$order_val['internaldatabaseaccess_status']=$invoice_val['internaldatabaseaccess_status']=	@$sArr['status'];
							$order_val['internaldatabaseaccess_quantity']=$invoice_val['internaldatabaseaccess_quantity']=@$sArr['quantity'];
							$order_val['internaldatabaseaccess_price']=	$invoice_val['internaldatabaseaccess_price']= @$sArr['price'];
							$order_val['internaldatabaseaccess_totalprice']=$invoice_val['internaldatabaseaccess_totalprice']=@$sArr['finalprice'];
								
							$active_val['internaldatabaseaccess_status']		=	@$sArr['status'];	
							$active_val['internaldatabaseaccess_quantity']		=	@$sArr['quantity'];
							$active_val['internaldatabaseaccess_used']			= 	0;
							$active_val['internaldatabaseaccess_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'Interviewtracker') {
						
							$order_val['interviewtracker_status']=$invoice_val['interviewtracker_status']=	@$sArr['status'];
							$order_val['interviewtracker_quantity']=$invoice_val['interviewtracker_quantity']=@$sArr['quantity'];
							$order_val['interviewtracker_price']=	$invoice_val['interviewtracker_price']= @$sArr['price'];
							$order_val['interviewtracker_totalprice']=$invoice_val['interviewtracker_totalprice']=@$sArr['finalprice'];
								
							$active_val['interviewtracker_status']		=	@$sArr['status'];	
							$active_val['interviewtracker_quantity']		=	@$sArr['quantity'];
							$active_val['interviewtracker_used']			= 	0;
							$active_val['interviewtracker_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'Offertracker') {
						
							$order_val['offertracker_status']=$invoice_val['offertracker_status']=	@$sArr['status'];
							$order_val['offertracker_quantity']=$invoice_val['offertracker_quantity']=@$sArr['quantity'];
							$order_val['offertracker_price']=	$invoice_val['offertracker_price']= @$sArr['price'];
							$order_val['offertracker_totalprice']=$invoice_val['offertracker_totalprice']=@$sArr['finalprice'];
								
							$active_val['offertracker_status']		=	@$sArr['status'];	
							$active_val['offertracker_quantity']		=	@$sArr['quantity'];
							$active_val['offertracker_used']			= 	0;
							$active_val['offertracker_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'Joiningtracker') {
						
							$order_val['joiningtracker_status']=$invoice_val['joiningtracker_status']=	@$sArr['status'];
							$order_val['joiningtracker_quantity']=$invoice_val['joiningtracker_quantity']=@$sArr['quantity'];
							$order_val['joiningtracker_price']=	$invoice_val['joiningtracker_price']= @$sArr['price'];
							$order_val['joiningtracker_totalprice']=$invoice_val['joiningtracker_totalprice']=@$sArr['finalprice'];
								
							$active_val['joiningtracker_status']		=	@$sArr['status'];	
							$active_val['joiningtracker_quantity']		=	@$sArr['quantity'];
							$active_val['joiningtracker_used']			= 	0;
							$active_val['joiningtracker_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'PermissionforSHaringCVtoclienys') {
						
							$order_val['PermissionforSHaringCVtoclienys_status']=$invoice_val['PermissionforSHaringCVtoclienys_status']=	@$sArr['status'];
							$order_val['PermissionforSHaringCVtoclienys_quantity']=$invoice_val['PermissionforSHaringCVtoclienys_quantity']=@$sArr['quantity'];
							$order_val['PermissionforSHaringCVtoclienys_price']=	$invoice_val['PermissionforSHaringCVtoclienys_price']= @$sArr['price'];
							$order_val['PermissionforSHaringCVtoclienys_totalprice']=$invoice_val['PermissionforSHaringCVtoclienys_totalprice']=@$sArr['finalprice'];
								
							$active_val['PermissionforSHaringCVtoclienys_status']		=	@$sArr['status'];	
							$active_val['PermissionforSHaringCVtoclienys_quantity']		=	@$sArr['quantity'];
							$active_val['PermissionforSHaringCVtoclienys_used']			= 	0;
							$active_val['PermissionforSHaringCVtoclienys_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'PermamentRecruitmentLeadIndia') {
						
							$order_val['PermamentRecruitmentLeadIndia_status']=$invoice_val['PermamentRecruitmentLeadIndia_status']=	@$sArr['status'];
							$order_val['PermamentRecruitmentLeadIndia_quantity']=$invoice_val['PermamentRecruitmentLeadIndia_quantity']=@$sArr['quantity'];
							$order_val['PermamentRecruitmentLeadIndia_price']=	$invoice_val['PermamentRecruitmentLeadIndia_price']= @$sArr['price'];
							$order_val['PermamentRecruitmentLeadIndia_totalprice']=$invoice_val['PermamentRecruitmentLeadIndia_totalprice']=@$sArr['finalprice'];
								
							$active_val['PermamentRecruitmentLeadIndia_status']		=	@$sArr['status'];	
							$active_val['PermamentRecruitmentLeadIndia_quantity']		=	@$sArr['quantity'];
							$active_val['PermamentRecruitmentLeadIndia_used']			= 	0;
							$active_val['PermamentRecruitmentLeadIndia_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'PermamentRecruitmentLeadInternational') {
						
							$order_val['PermamentRecruitmentLeadInternational_status']=$invoice_val['PermamentRecruitmentLeadInternational_status']=	@$sArr['status'];
							$order_val['PermamentRecruitmentLeadInternational_quantity']=$invoice_val['PermamentRecruitmentLeadInternational_quantity']=@$sArr['quantity'];
							$order_val['PermamentRecruitmentLeadInternational_price']=	$invoice_val['PermamentRecruitmentLeadInternational_price']= @$sArr['price'];
							$order_val['PermamentRecruitmentLeadInternational_totalprice']=$invoice_val['PermamentRecruitmentLeadInternational_totalprice']=@$sArr['finalprice'];
								
							$active_val['PermamentRecruitmentLeadInternational_status']		=	@$sArr['status'];	
							$active_val['PermamentRecruitmentLeadInternational_quantity']		=	@$sArr['quantity'];
							$active_val['PermamentRecruitmentLeadInternational_used']			= 	0;
							$active_val['PermamentRecruitmentLeadInternational_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'ContractRecruitmentLeadIndia') {
						
							$order_val['ContractRecruitmentLeadIndia_status']=$invoice_val['ContractRecruitmentLeadIndia_status']=	@$sArr['status'];
							$order_val['ContractRecruitmentLeadIndia_quantity']=$invoice_val['ContractRecruitmentLeadIndia_quantity']=@$sArr['quantity'];
							$order_val['ContractRecruitmentLeadIndia_price']=	$invoice_val['ContractRecruitmentLeadIndia_price']= @$sArr['price'];
							$order_val['ContractRecruitmentLeadIndia_totalprice']=$invoice_val['ContractRecruitmentLeadIndia_totalprice']=@$sArr['finalprice'];
								
							$active_val['ContractRecruitmentLeadIndia_status']		=	@$sArr['status'];	
							$active_val['ContractRecruitmentLeadIndia_quantity']		=	@$sArr['quantity'];
							$active_val['ContractRecruitmentLeadIndia_used']			= 	0;
							$active_val['ContractRecruitmentLeadIndia_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'ContractRecruitmentLeadInternational') {
						
							$order_val['ContractRecruitmentLeadInternational_status']=$invoice_val['ContractRecruitmentLeadInternational_status']=	@$sArr['status'];
							$order_val['ContractRecruitmentLeadInternational_quantity']=$invoice_val['ContractRecruitmentLeadInternational_quantity']=@$sArr['quantity'];
							$order_val['ContractRecruitmentLeadInternational_price']=	$invoice_val['ContractRecruitmentLeadInternational_price']= @$sArr['price'];
							$order_val['ContractRecruitmentLeadInternational_totalprice']=$invoice_val['ContractRecruitmentLeadInternational_totalprice']=@$sArr['finalprice'];
								
							$active_val['ContractRecruitmentLeadInternational_status']		=	@$sArr['status'];	
							$active_val['ContractRecruitmentLeadInternational_quantity']		=	@$sArr['quantity'];
							$active_val['ContractRecruitmentLeadInternational_used']			= 	0;
							$active_val['ContractRecruitmentLeadInternational_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'BenchSalesIndia') {
						
							$order_val['BenchSalesIndia_status']=$invoice_val['BenchSalesIndia_status']=	@$sArr['status'];
							$order_val['BenchSalesIndia_quantity']=$invoice_val['BenchSalesIndia_quantity']=@$sArr['quantity'];
							$order_val['BenchSalesIndia_price']=	$invoice_val['BenchSalesIndia_price']= @$sArr['price'];
							$order_val['BenchSalesIndia_totalprice']=$invoice_val['BenchSalesIndia_totalprice']=@$sArr['finalprice'];
								
							$active_val['BenchSalesIndia_status']		=	@$sArr['status'];	
							$active_val['BenchSalesIndia_quantity']		=	@$sArr['quantity'];
							$active_val['BenchSalesIndia_used']			= 	0;
							$active_val['BenchSalesIndia_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'BenchSalesInternational') {
						
							$order_val['BenchSalesInternational_status']=$invoice_val['BenchSalesInternational_status']=	@$sArr['status'];
							$order_val['BenchSalesInternational_quantity']=$invoice_val['BenchSalesInternational_quantity']=@$sArr['quantity'];
							$order_val['BenchSalesInternational_price']=	$invoice_val['BenchSalesInternational_price']= @$sArr['price'];
							$order_val['BenchSalesInternational_totalprice']=$invoice_val['BenchSalesInternational_totalprice']=@$sArr['finalprice'];
								
							$active_val['BenchSalesInternational_status']		=	@$sArr['status'];	
							$active_val['BenchSalesInternational_quantity']		=	@$sArr['quantity'];
							$active_val['BenchSalesInternational_used']			= 	0;
							$active_val['BenchSalesInternational_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'BenchClientAcccessIndia') {
						
							$order_val['BenchClientAcccessIndia_status']=$invoice_val['BenchClientAcccessIndia_status']=	@$sArr['status'];
							$order_val['BenchClientAcccessIndia_quantity']=$invoice_val['BenchClientAcccessIndia_quantity']=@$sArr['quantity'];
							$order_val['BenchClientAcccessIndia_price']=	$invoice_val['BenchClientAcccessIndia_price']= @$sArr['price'];
							$order_val['BenchClientAcccessIndia_totalprice']=$invoice_val['BenchClientAcccessIndia_totalprice']=@$sArr['finalprice'];
								
							$active_val['BenchClientAcccessIndia_status']		=	@$sArr['status'];	
							$active_val['BenchClientAcccessIndia_quantity']		=	@$sArr['quantity'];
							$active_val['BenchClientAcccessIndia_used']			= 	0;
							$active_val['BenchClientAcccessIndia_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'BenchClientAccessInternational') {
						
							$order_val['BenchClientAccessInternational_status']=$invoice_val['BenchClientAccessInternational_status']=	@$sArr['status'];
							$order_val['BenchClientAccessInternational_quantity']=$invoice_val['BenchClientAccessInternational_quantity']=@$sArr['quantity'];
							$order_val['BenchClientAccessInternational_price']=	$invoice_val['BenchClientAccessInternational_price']= @$sArr['price'];
							$order_val['BenchClientAccessInternational_totalprice']=$invoice_val['BenchClientAccessInternational_totalprice']=@$sArr['finalprice'];
								
							$active_val['BenchClientAccessInternational_status']		=	@$sArr['status'];	
							$active_val['BenchClientAccessInternational_quantity']		=	@$sArr['quantity'];
							$active_val['BenchClientAccessInternational_used']			= 	0;
							$active_val['BenchClientAccessInternational_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'BoostKeywordsIndia') {
						
							$order_val['BoostKeywordsIndia_status']=$invoice_val['BoostKeywordsIndia_status']=	@$sArr['status'];
							$order_val['BoostKeywordsIndia_quantity']=$invoice_val['BoostKeywordsIndia_quantity']=@$sArr['quantity'];
							$order_val['BoostKeywordsIndia_price']=	$invoice_val['BoostKeywordsIndia_price']= @$sArr['price'];
							$order_val['BoostKeywordsIndia_totalprice']=$invoice_val['BoostKeywordsIndia_totalprice']=@$sArr['finalprice'];
							$string_version 							=	"";//implode(' , ', @$sArr['keywords']);
							$order_val['boostkeywords_text'] 			=	$invoice_val['boostkeywords_text'] 			=  	@$string_version;
								
							$active_val['BoostKeywordsIndia_status']		=	@$sArr['status'];	
							$active_val['BoostKeywordsIndia_quantity']		=	@$sArr['quantity'];
							$active_val['BoostKeywordsIndia_used']			= 	0;
							$active_val['BoostKeywordsIndia_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
							$active_val['boostkeywords_text']			=	@$string_version;
								
					}else if($sArr['field_name'] == 'BoostKeywordsInternational') {
						
							$order_val['BoostKeywordsInternational_status']=$invoice_val['BoostKeywordsInternational_status']=	@$sArr['status'];
							$order_val['BoostKeywordsInternational_quantity']=$invoice_val['BoostKeywordsInternational_quantity']=@$sArr['quantity'];
							$order_val['BoostKeywordsInternational_price']=	$invoice_val['BoostKeywordsInternational_price']= @$sArr['price'];
							$order_val['BoostKeywordsInternational_totalprice']=$invoice_val['BoostKeywordsInternational_totalprice']=@$sArr['finalprice'];
							$string_version 							=	"";//implode(' , ', @$sArr['keywords']);
							$order_val['boostkeywords_text'] 			=	$invoice_val['boostkeywords_text'] 			=  	@$string_version;
								
							$active_val['BoostKeywordsInternational_status']		=	@$sArr['status'];	
							$active_val['BoostKeywordsInternational_quantity']		=	@$sArr['quantity'];
							$active_val['BoostKeywordsInternational_used']			= 	0;
							$active_val['BoostKeywordsInternational_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
							$active_val['boostkeywords_text']			=	@$string_version;
								
					}else if($sArr['field_name'] == 'Limitperdayforleadsconsumptions') {
						
							$order_val['Limitperdayforleadsconsumptions_status']=$invoice_val['Limitperdayforleadsconsumptions_status']=	@$sArr['status'];
							$order_val['Limitperdayforleadsconsumptions_quantity']=$invoice_val['Limitperdayforleadsconsumptions_quantity']=@$sArr['quantity'];
							$order_val['Limitperdayforleadsconsumptions_price']=	$invoice_val['Limitperdayforleadsconsumptions_price']= @$sArr['price'];
							$order_val['Limitperdayforleadsconsumptions_totalprice']=$invoice_val['Limitperdayforleadsconsumptions_totalprice']=@$sArr['finalprice'];
								
							$active_val['Limitperdayforleadsconsumptions_status']		=	@$sArr['status'];	
							$active_val['Limitperdayforleadsconsumptions_quantity']		=	@$sArr['quantity'];
							$active_val['Limitperdayforleadsconsumptions_used']			= 	0;
							$active_val['Limitperdayforleadsconsumptions_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}
				}
			}

			$active_val['package_id'] = $package_default->id;
			$invoice_val['package_id'] = $package_default->id;
			$order_val['package_id'] = $package_default->id;

			$active_val['startdate'] =date('Y-m-d');
			$active_val['enddate'] = $duration_endDate;
			$active_val['packages_status'] = 1;
			$active_val['payment_status'] = 0;

			$invoice_val['startdate'] = date('Y-m-d');
			$invoice_val['enddate'] = $duration_endDate;
			$invoice_val['packages_status'] = 1;
			$invoice_val['payment_status'] = 0;

			$order_val['startdate'] =date('Y-m-d');
			$order_val['enddate'] = $duration_endDate;
			$order_val['packages_status'] = 1;
			$order_val['payment_status'] = 0;
		
			DB::table('package_order')->insert($order_val);  
			$package_order_id    				=	DB::getPdo()->lastInsertId();
		
			$invoice_val['package_order_id'] 	=	@$package_order_id;	
			$invoice_val['subtotal'] 			=	@$subtotal;				
			DB::table('package_invoice')->insert($invoice_val);  
			$package_invoice_id    				=	DB::getPdo()->lastInsertId();


			if(!empty($package_default_details)){
				foreach($package_default_details as $sArr){
					if((int) @$sArr['features_id'] > 0){
					$PackageOrderItem = new PackageOrderItem();
					$PackageOrderItem->package_id = $package_default->id;
					$PackageOrderItem->package_order_id = $package_order_id;
					$PackageOrderItem->invoice_id = $package_invoice_id;
					$PackageOrderItem->	feature_id = (int) @$sArr['features_id'];
					$PackageOrderItem->qty = @$sArr['quantity'];
					$PackageOrderItem->price = @$sArr['price'];
					$PackageOrderItem->sub_total = @$sArr['finalprice'];
					$PackageOrderItem->status = @$sArr['status'];
					$PackageOrderItem->save();
					}

				}
			}
		
			DB::table('package_active')->insert($active_val);

			$currency = GeneralMethod::convertINRToUSD();
			$company_id = @$company->id;
			$company_coins_free = DB::table('coin_packages')
			->where('coin_packages.custome_pck',2)
			->select('coin_packages.*')
			->get()
			->map(function ($coinOrder) {
				$coinOrder->coin_order_events = DB::table('coin_events')
					->where('event_type', $coinOrder->event_type)
					->select('coin_events.*')
					->where('status','=',1)
					->get()
					->toArray();
				return (array) $coinOrder;
			})
			->toArray();

			if(@$company_coins_free){

				foreach($company_coins_free as $package_sales){

					$newDate = date("Y-m-d");
					$currentDate = date("Y-m-d"); 
					$newDate = date("Y-m-d", strtotime($currentDate . " +".$package_sales['days']." days")); 
					// dd($currentDate,$newDate);

						$coin_orders = [
							'coin_package_id'=>$package_sales['id'],
							"currency" =>$currency,
							'price'=>($currency=='INR') ? $package_sales['price'] : $package_sales['price_usd'],
							'coins'=>$package_sales['coins'],
							'days'=>$package_sales['days'],
							'event_type'=>$package_sales['event_type'],
							'company_id'=>$company_id,
							'discount'=>$package_sales['discount'],
							'status'=>1,
							'created_at'=>date("Y-m-d H:i:s"),
							'updated_at'=>date("Y-m-d H:i:s")	
						];
				
						$coin_order_id = DB::table('coin_orders')->insertGetId($coin_orders);
						$response = $this->elasticService->getClient()->update([
							'index' => ELASTIC_COIN_ORDERS,
							'id'    => $coin_order_id,
							'retry_on_conflict' => 5,
							'body'  => [
								'doc' => [ 
									'id' =>  $coin_order_id,
									'coin_package_id'=>$package_sales['id'],
									"currency" =>$currency,
									'price'=>($currency=='INR') ? $package_sales['price'] : $package_sales['price_usd'],
									'coins'=>$package_sales['coins'],
									'days'=> $package_sales['days'],
									'event_type'=>$package_sales['event_type'],
									'company_id'=>$company_id,
									'discount'=>$package_sales['discount'],
									'status'=>1,
									'updated_at' => now()->toIso8601String(),
									'created_at' => now()->toIso8601String()
								],
								'doc_as_upsert' => true,
								'detect_noop' => false
							],
						]);

						foreach($package_sales['coin_order_events'] as $package_value){
							$coin_order_events = [
								'company_id'=>$company_id,
								'coin_event_id'=>$package_value->id,
								'deductions'=>$package_value->default_rate,
								'deduction_type'=>$package_value->deduction_type,
								'coin_order_id'=>$coin_order_id,
								'required_coin'=>$package_value->minimum_required,
								'event_type'=>$package_value->event_type
							];
							// dd($coin_order_events);
							$eventsId = DB::table('coin_order_events')->insertGetId($coin_order_events);

							$response = $this->elasticService->getClient()->update([
								'index' => ELASTIC_COIN_ORDER_EVENTS,
								'id'    => $eventsId,
								'retry_on_conflict' => 5,
								'body'  => [
									'doc' => [ 
										'id' =>  $eventsId,
										'company_id'=>$company_id,
										'coin_event_id'=>$package_value->id,
										'deductions'=>$package_value->default_rate,
										'deduction_type'=>$package_value->deduction_type,
										'coin_order_id'=>$coin_order_id,
										'required_coin'=>$package_value->minimum_required,
										'event_type'=>$package_value->event_type
									],
									'doc_as_upsert' => true,
									'detect_noop' => false
								],
							]);
						}

						$company_coins = [
								'coin_order_id'=>$coin_order_id,
								'company_id'=>$company_id,
								'event_type'=>$package_sales['event_type'],
								"currency" =>$currency,
								'amount'=>($currency=='INR') ? $package_sales['price'] : $package_sales['price_usd'],
								'discount'=>$package_sales['discount'],
								'coins'=>$package_sales['coins'],
								'actual_coins'=>$package_sales['coins'],
								'balance_coin'=>0,
								'status'=>'valid',
								'free_pack_status'=>'active',
								'start_date'=>$currentDate,
								'end_date'=>$newDate,
								'free_update_date'=>$currentDate,
								'created_at'=>date("Y-m-d H:i:s"),
								'updated_at'=>date("Y-m-d H:i:s")				
						];
						$insertGetId = DB::table('company_coins')->insertGetId($company_coins);

						$response = $this->elasticService->getClient()->update([
							'index' => ELASTIC_COMPANY_COINS,
							'id'    => $insertGetId,
							'retry_on_conflict' => 5,
							'body'  => [
								'doc' => [ 
									'id' =>  $insertGetId,
									'coin_order_id' => $coin_order_id,
									'company_id' => $company_id,
									'event_type' => $package_sales['event_type'],
									'currency' => $currency,
									'amount' => ($currency=='INR') ? $package_sales['price'] : $package_sales['price_usd'],
									'discount' => $package_sales['discount'],
									'coins' => $package_sales['coins'],
									'actual_coins' => $package_sales['coins'],
									'balance_coin' => 0,
									'status' => 'valid',
									'free_pack_status' => 'active',
									'start_date' => ($currentDate) ? Carbon::parse($currentDate)->toIso8601String() : null,
									'end_date' => ($newDate) ? Carbon::parse($newDate)->toIso8601String() : null,
									'free_update_date' => ($currentDate) ? Carbon::parse($currentDate)->toIso8601String() : null,
									'auto_team_coins' => now()->toIso8601String(),
									'auto_job_coins' => now()->toIso8601String(),
									'updated_at' => now()->toIso8601String(),
									'created_at' => now()->toIso8601String(),
								],
								'doc_as_upsert' => true,
								'detect_noop' => false
							],
						]);



						$coinEvents = DB::table('coin_orders')
						->join('coin_packages','coin_packages.id','=','coin_orders.coin_package_id')
						->join('coin_package_types','coin_package_types.id','=','coin_packages.coin_package_type_id')
						->where('coin_orders.id',$coin_order_id)
						->select('coin_package_types.title')->first();
						$coinEventspackage = ($coinEvents) ? $coinEvents->title : '';
						
						$coin_transection_logs = [
								'coin_order_id'=>$coin_order_id,
								'from_company_id'=>$company_id,
								'company_id'=>$company_id,
								'event_by'=>1,
								'event'=> $coinEventspackage. " ",
								"currency" =>$currency,
								'amount'=>($currency=='INR') ? $package_sales['price'] : $package_sales['price_usd'],
								'discount'=>$package_sales['discount'],
								'coins'=>$package_sales['coins'],
								'transection_type'=>'credit',
								'settlement_coins'=>0,
								'event_type'=>$package_sales['event_type'],
								'start_date'=>$currentDate,
								'end_date'=>$newDate,
								'created_at'=>date("Y-m-d H:i:s"),
								'updated_at'=>date("Y-m-d H:i:s")	
						];

						$transection_logsId = DB::table('coin_transection_logs')->insertGetId($coin_transection_logs);

						$response = $this->elasticService->getClient()->update([
                                'index' => ELASTIC_COIN_TRANSECTION_LOGS,
                                'id'    => $transection_logsId,
                                'retry_on_conflict' => 5,
                                'body'  => [
                                    'doc' => [ 
										'coin_order_id'=>$coin_order_id,
										'from_company_id'=>$company_id,
										'company_id'=>$company_id,
										'event_by'=>1,
										'event'=> $coinEventspackage. " ",
										"currency" =>$currency,
										'amount'=>($currency=='INR') ? $package_sales['price'] : $package_sales['price_usd'],
										'discount'=>$package_sales['discount'],
										'coins'=>$package_sales['coins'],
										'transection_type'=>'credit',
										'settlement_coins'=>0,
										'event_type'=>$package_sales['event_type'],
                                        'start_date' => ($currentDate) ? Carbon::parse($currentDate)->toIso8601String() : null,
                                        'end_date' => ($newDate) ? Carbon::parse($newDate)->toIso8601String()  : null,
                                        'created_at' => now()->toIso8601String(),
                                        'updated_at' => now()->toIso8601String()
                                    ],
                                    'doc_as_upsert' => true,
                                    'detect_noop' => false
                                ],
                        ]);






						
					
				}
			}

			$user = new User();
			$user->company_id = $company->id;
			$user->name = $request->name;
			$user->mobile =  $request->contact_number;
			$user->calling_code = @$request->contact_code;
			$user->email = $request->email;
			$user->password = bcrypt($request->password);
			$user->super_password 			=   $request->password;
			$user->super_admin_login_code 	=  $company->id.rand(1111,9999);
			$user->email_verification_code = str_random(40);
			$user->status = 'inactive';
			$user->working_status = 1;
			$user->root_user = 1;
			$user->save();
			if ($admin = User::find($user->id)) {
				$admin->assignRole('admin');
			}

			//assign admin role to default user
			$role = CustomRole::where('company_id', $company->id)->first();
			if ($role) {
			   $user->roles()->attach(@$role->id);
			}


			$users_profile = array(
				'company_id' => $company->id,
				'user_id' => $user->id,
				'country' => 101,
				'created_at' => date('Y-m-d'),
				'updated_at' => date('Y-m-d')
			);
			DB::table('users_profile')->insert($users_profile);



			if ($role) {
				if (@$role->id) {
					if ($request->company_type == 3) {

						DB::table('permission_role')
							->leftJoin('permissions', 'permissions.id', '=', 'permission_role.permission_id')
							->where('permissions.product_based', 0)
							->where('permission_role.role_id', $role->id)
							->delete();

										/*DELETE permission_role FROM permission_role
										INNER JOIN permissions ON permissions.id = permission_role.permission_id
										WHERE permissions.product_based = 0 and  permission_role.role_id = 114*/
					}

				}
			}

			if ($company->id > 0) {
				// Working with Categories
				$designationArr = '';
				$designationArr = GeneralMethod::department();
				if (!empty($designationArr)) {
					foreach ($designationArr as $dval) {
						if(!empty($dval)){
						$values = array(
							'company_id' => $company->id,
							'name' => $dval,
							'created_at' => date('Y-m-d'),
							'updated_at' => date('Y-m-d')
						);
						$insertGetId = DB::table('job_categories')->insertGetId($values);

						 $response = $this->elasticService->getClient()->update([
							'index' => ELASTIC_JOB_CATEGORIES,
							'id'    => $insertGetId,
							'retry_on_conflict' => 5,
							'body'  => [
								'doc' => [ 
									'id' =>  $insertGetId,
									'company_id' =>(int) $company->id,
									'name' => $dval,
									'created_at' => now()->toIso8601String(),
									'updated_at' => now()->toIso8601String()
								],
								'doc_as_upsert' => true,
								'detect_noop' => false
							],
						]);

					}
					}
				}

				//Working with Client
				$values = array(
					'company_id' => $company->id,
					'name' => $request->company_name,
					'website' => $request->website, 
					'self_company' => 1,
					'created_at' => date('Y-m-d'),
					'updated_at' => date('Y-m-d'),
					'email' => $request->email,
					'contact_person_name' => $request->company_name
				);
				DB::table('clients')->insert($values);
				$client_id = DB::getPdo()->lastInsertId();
				

				// Working with Client Contact Info 
				$values = array(
					'client_id' => $client_id,
					'contact_person_name' => $request->name,
					'contact_person_email' => $request->email,
					//'contact_person_number' 	=> $number, 
					'contact_person_designation' => 'Account Owner'
				);
				DB::table('client_contact_info')->insert($values);


				$cpriority = 90;
				$childStatusArr = array('Duplicate', 'Not Interested', 'Not Relevant', 'Profile Mismatch', 'Stability Issues','High Notice Period','High Salary Expectations','Interview Rejected','HR Rejected','Proxy/Fake','Other Reasons');
				foreach ($childStatusArr as $csVal) {
					$values = array(
						'company_id' => $company->id,
						'parent' => 7,
						'name' => $csVal,
						'priority' => $cpriority,
						'delete_status' => 0
					);
					DB::table('candidate_status')->insert($values);
					$cpriority++;
				}

				$childStatusArr		=	array('Onboarded', 'Backed Out');
				foreach($childStatusArr as $csVal)
				{
					
					$values 		=	array(
												'company_id' 	=> $company->id, 
												'parent' 		=> 6, 
												'name' 			=> $csVal, 
												'priority' 		=> $cpriority, 
												'delete_status'	=> 0
											);
					DB::table('candidate_status')->insert($values);
					$cpriority++;
				}
				
			}

			$custom_role_arr = array('Admin','Sub Administrator', 'Team Lead', 'Recruiter', 'Interviewer', 'Sales','Default');
			$role_company_id = '';
			$role_company_id = $company->id;
			foreach ($custom_role_arr as $rolename) {
				$role_priority = 1;
				$role_row = DB::table('roles')->select('*')->where('company_id', $role_company_id)->orderBy('roles.id', 'DESC')->first();
				if($role_row){
				if (@$role_row->priority >0) {
						$role_priority = @$role_row->priority + 1;
					}
				}

				$lastInsertId = '';
				$values = array(
					'company_id' => $role_company_id,
					'name' => strtolower($rolename),
					'display_name' => ($rolename=="Admin") ? 'Administrator' : $rolename,
					'guard_name' => 'web',
					'description' => ($rolename=="Admin") ? 'Admin is allowed to manage everything of the app.' : $rolename,
					'priority' => $role_priority
				);
				DB::table('roles')->insert($values);
				$lastInsertId = DB::getPdo()->lastInsertId();
                if($rolename=="Admin"){
					$valuesrole = array('user_id' => $user->id, 'role_id' => $lastInsertId);
					DB::table('role_user')->insertOrIgnore($valuesrole);
				}

				

				if ($lastInsertId > 0) {
					if ($request->company_type == 3) {
						$roleId = DB::table('permissions')
							->select('*')
							->where('permissions.status', 1)
							->where('permissions.product_based', 1)
							->orderBy('permissions.priority', 'ASC')
							->get();
					} else {
						$roleId = DB::table('permissions')
							->select('*')
							->where('permissions.status', 1)
							->orderBy('permissions.priority', 'ASC')
							->get();
					}

					if (!empty($roleId)) {
						foreach ($roleId as $roleVal) {
							$values = array(
								'permission_id' => $roleVal->id,
								'role_id' => $lastInsertId
							);
							DB::table('role_has_permissions')->insertOrIgnore($values);
						}



					}

				}

			}





			// STAFFING COMPANY INVITATION
			$staffing_company_invitation = $staffing_company_invitation_id = '';
			$staffing_company_invitation = $request->staffing_company_invitation;
			$staffing_company_invitation_id = $request->staffing_company_invitation_id;
			if (($staffing_company_invitation_id > 0) && ($staffing_company_invitation == 'staffing_company_invitation')) {

				$rowData = DB::table('staffing_company_invitation')
					->select('*')
					->where('id', @$staffing_company_invitation_id)
					->first();

				$from_company_id = $rowData->from_company_id;
				$from_user_id = $rowData->from_user_id;
				$from_client_id = $rowData->from_client_id;

				// UPDATE STAFFING COMPANY INVITATION
				DB::table('staffing_company_invitation')
					->where('id', @$staffing_company_invitation_id)
					->limit(1)
					->update(array('to_status' => 1, 'to_company_id' => $role_company_id));


				// INSERT COMPANY RELATION 
				$values = array(
					'from_company_id' => $from_company_id,
					'from_client_id' => $from_client_id,
					'from_user_id' => $from_user_id,
					'from_status' => 1,

					'from_created_at' => date('Y-m-d H:i:s'),
					'to_company_id' => $role_company_id,
					'to_client_id' => $client_id,
					'to_user_id' => $user->id,
					'to_status' => 1,
					'to_created_at' => date('Y-m-d H:i:s'),
					'final_status' => 1,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')

				);
				DB::table('staffing_company_relation')->insert($values);
				$relationId 		=	DB::getPdo()->lastInsertId();
				GeneralMethod::appendClientNestedData($relationId);

			}


			$to = $request->email;   							//	$user->email;
			$cc = 'abu@4bell.com';
			$from = 'vibhanshu@4bell.com';
			$subject = __('modules.saasFront.emailVerification') . ' ' . config('app.name') . '!';
			$urlLink = getDomainSpecificUrl(route('get-email-verification', $user->email_verification_code), $request->company_name);
			$message = '<a href="' . $urlLink . '" target="_blank">Account Verification link</a>';

				$data = [
					'name' => $request->name,
					'to' => $to,
					"cc" => $cc,
					'from' => $from,
					'subject' => $subject,
					'urlLink'=> $urlLink,
					'message'=> $message
				];
				
				
					$basecompany = GeneralMethod::baseCompany();
                    $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
                    $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
                    $attachments = array();
                    $cc = array();
                    $subject = "Company Profile Confirmation";		

					
					QueueMailService::dispatch(
						$to,
						$subject,
						'email.company-profile.email-confirmation',
						$data,
						$attachments,
						$fromAddress,
						$senderName,
						$cc,
						$company_id
					)->onConnection('emails');

                                        

			        $work_city = @$request->kt_tagify_work_location;
					$work_city_all = json_decode($work_city, true);
					$company_id = $company->id;
					$company 							=	Company::findOrFail($company_id);
					$stateId = '';
					$cityID = '';
					$countryID = '';
					$city = '';
					$default_rates = DB::table('default_rates')->first();
					if(count($work_city_all)>0){
						if(!DB::table('commericials')->where('rateType',1)->where('company_id',$company_id)->exists()){

							$status = 0;
							if($company->company_type==1){
								$status = 1;
							}elseif($company->company_type==2){
								$status = 1; // IT Service Company
							}else{
								$status = 0; // Product Company
							}
							
							$value = array('company_id'=>$company_id,'rateType'=>1,'commtype'=>1,'sal_mode'=>2,'salary_currency'=>4,'status'=>$status);
							
							$commericialId = DB::table('commericials')->insertGetId($value);
							$rate = @$default_rates->rates_fixed;
							for($i=0; $i<10; $i++){
								$value = array('company_id'=>$company_id,'commericial_id'=>$commericialId,'commtype'=>1,'exp'=>$i,'rate'=>$rate);
								DB::table('commericial_details')->insertGetId($value);
							}

								
									foreach ($work_city_all as $cityVal) {
										
										$parts = explode('|', $cityVal['value']);
										// Trim to remove any leading or trailing whitespace
										$cityName = trim($parts[0]);
										$countrName = trim($parts[2]);
										$city_row = DB::table('city_list')
										->join('state_list', 'state_list.id', '=', 'city_list.state_id')
										->join('countries', 'countries.id', '=', 'state_list.country_id')
										->select('city_list.city_id', 'countries.id as country_id')
										->where('city_list.city','!=','')
										->where('city_list.city', '=', $cityName)
										->where('countries.country_name', '=', $countrName)
										->first();

										if($city_row){
											$countryID = (@$city_row) ? $city_row->country_id : '';

											$valueCountry = array('company_id'=>$company_id,'commericial_id'=>$commericialId, 'commtype'=>1,'country_id'=>$countryID);

											
											DB::table('commericial_countries')->insert($valueCountry);	

										}
										
									}
								

						}
						
						if(!DB::table('commericials')->where('rateType',2)->where('company_id',$company_id)->exists()){
							$status = 0;
							if($company->company_type==1){
								$status = 1;   // Staffing Company
							}elseif($company->company_type==2){
								$status = 1; // IT Service Company
							}else{
								$status = 0; //Product Company
							}
							$value = array('company_id'=>$company_id,'rateType'=>2,'commtype'=>2,'status'=>$status);
							$commericialId = DB::table('commericials')->insertGetId($value);
							$rate = @$default_rates->rates_perecentage;
							for($i=0; $i<10; $i++){
								$value = array('company_id'=>$company_id,'commericial_id'=>$commericialId,'commtype'=>2,'exp'=>$i,'rate'=>$rate);
								DB::table('commericial_details')->insertGetId($value);
							}

									foreach ($work_city_all as $cityVal) {
										$parts = explode('|', $cityVal['value']);
										// Trim to remove any leading or trailing whitespace
										$cityName = trim($parts[0]);
										$countrName = trim($parts[2]);
										$city_row = DB::table('city_list')
										->join('state_list', 'state_list.id', '=', 'city_list.state_id')
										->join('countries', 'countries.id', '=', 'state_list.country_id')
										->select('city_list.city_id', 'countries.id as country_id')
										->where('city_list.city','!=','')
										->where('city_list.city', '=', $cityName)
										->where('countries.country_name', '=', $countrName)
										->first();

										if($city_row){
											$countryID = (@$city_row) ? $city_row->country_id : '';

											$valueCountry = array('company_id'=>$company_id,'commericial_id'=>$commericialId, 'commtype'=>2,'country_id'=>$countryID);										
											DB::table('commericial_countries')->insert($valueCountry);	

										}
										
									}
								

						}

						
						
						foreach($work_city_all as $cityVal) {
							$parts = explode('|', $cityVal['value']);
							// Trim to remove any leading or trailing whitespace
							$cityName = trim($parts[0]);
							$countrName = trim($parts[2]);
							$city_row = DB::table('city_list')
							->join('state_list', 'state_list.id', '=', 'city_list.state_id')
							->join('countries', 'countries.id', '=', 'state_list.country_id')
							->select('city_list.city_id', 'countries.id as country_id','state_list.id as stateID')
							->where('city_list.city','!=','')
							->where('city_list.city', '=', $cityName)
							->where('countries.country_name', '=', $countrName)
							->first();

							

							if($city_row){
								$cityID = (@$city_row) ? $city_row->city_id : '';
								$countryID = (@$city_row) ? $city_row->country_id : '';
								$stateId = (@$city_row) ? $city_row->stateID : '';
							
								$values 				= array(
									'company_id' 	=> 0, 
									'client_id' 	=> $client_id, 
									'country_id' 	=> $countryID, 
									'city' 			=> $city,
									'city_id' 		=> $cityID, 
									'address' 		=> ($cityName) ? $cityName :'A', 
									'postcode' 		=> 123456, 
									'state' 		=> $stateId
								);
								// dd($values);
								DB::table('client_location')->insert($values);	

							}
							
						}
					}

					if($client_id>0){
						GeneralMethod::clientStaffingPartner($client_id);
					}

					$this->user = $user;
					
					GeneralMethod::userIndexingupload($user->id);
					
					$view = view('saas-front.registration_success', $this->data)->render();

			        return Reply::dataOnly(['status' => 'success', 'view' => $view]);


		} catch (\Swift_TransportException $e) {
			DB::rollback();
			return Reply::error('Please contact administrator to set SMTP details to add company', 'smtp_error');
		}



		return Reply::dataOnly(['status' => 'fail']);



	}
	public function companyRegisterChk(Request $request)
	{
      
	    $status = 'fail';

		$validator = Validator::make($request->all(), [
			'email' => [
				'required',
				'email:rfc,dns',
				'disposable_email'
			]
		], [
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email',
            'email.disposable_email' => 'Temporary emails are not allowed'
        ]);

		if ($validator->fails()) {
			return Reply::dataOnly(['status' => 'emailchecker','message' => $validator->errors()->first()]);
			
		}
		
		if ($request->email != '') {
			$email = $request->email;
			$userCount = DB::table('users')
				->select('users.id')
				->where('users.email', $request->email)
				->count();
			if ($userCount == 0) {
				$status = 'success';
			}
		}
		return Reply::dataOnly(['status' => $status]);
	}
	public function contactCompanyRegisterChk(Request $request)
	{

		$status = 'fail';
		if ($request->contact_number != '') {
			$contact_number = $request->contact_number;
			$userCount = DB::table('users')
				->select('users.id')
				->where('users.mobile', $request->contact_number)
				->count();
			if ($userCount == 0) {
				$status = 'success';
			}
		}
		return Reply::dataOnly(['status' => $status]);
	}
	public function domainCompanyRegisterChk(Request $request)
	{

		$status = 'fail';
		$company_domain = '';
		$company_email = '';
		$company_name = '';
		if ($request->website != '') {

			$client_id = $request->check_client_id;
			$parsedUrl = parse_url($request->website, PHP_URL_HOST) ?? $request->website;

			// Remove 'www.'
			$parsedUrl = preg_replace('/^www\./', '', $parsedUrl);

			// Remove TLD (.com, .org, etc.)
			$domainParts = explode('.', $parsedUrl);
			$mainDomain = $parsedUrl;//$domainParts[0] ?? '';

			$website = $request->website;
			$userCount = DB::table('companies')
				->select('companies.id')
				->where('companies.website', 'LIKE', '%' . $mainDomain . '%')
				->count();
			if ($userCount == 0) {
				$status = 'success';
			}else{
				$companies = DB::table('companies')
				->select('companies.*')
				->where('companies.website', 'LIKE', '%' . $mainDomain . '%')
				->first();

				$visibleLength = 10; $maskChar = 'X';
				$email = $companies->company_email;
				list($username, $domain) = explode('@', $email);
				$length = strlen($username);
				if($length <= 2){
                    $maskedUsername = $username;
				} elseif ($length <= 6) {
					// Show first 2, mask the rest
					$maskedUsername = substr($username, 0, 2) . str_repeat('X', $length - 2);
				} elseif ($length <= 10) {
					// Show first 3, mask the rest
					$maskedUsername = substr($username, 0, 3) . str_repeat('X', $length - 3);
				} else {
					// Show first 10, mask the rest
					$maskedUsername = substr($username, 0, 10) . str_repeat('X', $length - 10);
				}
				
				$company_email = $email;//$maskedUsername . '@' . $domain;
				$company_domain = $companies->website;
				$company_name = $companies->company_name;
			}

		}
		return Reply::dataOnly(['status' => $status, 'company_domain' => $company_domain,'company_email' => $company_email,'company_name' => $company_name]);
	}
	public function domainClientRegisterChk(Request $request)
	{

		$status = 'fail';
		$logo_url = '';
		$message = '';
		if ($request->website != '') {

			$company_id = auth()->user()->company_id;
			// dd($company_id);

			$client_id = $request->check_client_id;
			$parsedUrl = parse_url($request->website, PHP_URL_HOST) ?? $request->website;

			// Remove 'www.'
			$parsedUrl = preg_replace('/^www\./', '', $parsedUrl);
            // dd($parsedUrl);
			// Remove TLD (.com, .org, etc.)
			$domainParts = explode('.', $parsedUrl);
			$mainDomain = $parsedUrl;//$domainParts[0] ?? '';
			$clientName = null;
			try {
				$response = Http::timeout(5)->get(
					'https://autocomplete.clearbit.com/v1/companies/suggest',
					['query' => $mainDomain]
				);

				// ✅ Check response success
				if (!$response->successful()) {
					$clientName =  null;
				}

				$data = $response->json();

				// ✅ Check empty response
				if (empty($data) || !is_array($data)) {
					$clientName =  null;
				}
                $company = collect($data)->firstWhere('domain', $mainDomain);
				// ✅ Safely get first result
				$clientName =  $company['name'] ?? null;;

			} catch (\Exception $e) {
				// optional: log error
				\Log::error('Clearbit API Error: ' . $e->getMessage());
				$clientName =  null;
			}

			// dd($client_id);
			$userCount = DB::table('clients')
				->select('clients.id')
				->where('clients.website', 'LIKE', '%' . $mainDomain . '%')
				->where('clients.company_id', '=',$company_id)
				->count();
				
				$clients = DB::table('clients')
				->select('clients.id')
				->where('clients.website', 'LIKE', '%' . $mainDomain . '%')
				->where('clients.company_id', '!=',$company_id)
				->first();

				$clients_ckeck = DB::table('clients')
				->select('clients.id')
				->where('clients.website', 'LIKE', '%' . $mainDomain . '%')
				->where('clients.id', '=',$client_id)
				->first();

				

				if($clients){
					$params = [
						'index' => ELASTIC_CLIENT_LIST,
						'id'    => $clients->id,
					];

					$response = $this->elasticService->getClient()->get($params);
					$client = (object) $response['_source'];
					$logo_url = (@$client->logo_logical_path) ? @$client->logo_logical_path : @$client->logo;
					
				}
				if($clients_ckeck){
					$status = 'success';
				}

				$message = "This Client is already exists.";

			if ($userCount == 0) {
				$status = 'success';
			}
		}
		return response()->json(['status' => $status,'message' => $message,'logo_url' => $logo_url,'clientName'=>$clientName]);
	}
	public function resend(Request $request)
	{
		$userId = $request->id;
		$userCount = DB::table('users')
			->select('users.id')
			->where('users.id', $userId)
			->where('users.status', '!=', 'active')
			->where('users.email_verification_code', '!=', '')
			->count();
			
		if ($userCount > 0) {
			// SELECT USER DB QUERY
			$user = $name = $company_id = $email = '';

			$user = DB::table('users')
				->join('companies', 'companies.id', 'users.company_id')
				->select('users.id', 'users.name', 'users.email','users.company_id', 'users.email_verification_code', 'companies.company_name')
				->where('users.id', $userId)
				->where('users.status', '!=', 'active')
				->where('users.email_verification_code', '!=', '')
				->first();
				

			$name = $user->name;
			$company_name = $user->company_name;
			$company_id = $user->company_id;


			$to = $user->email;
			$cc = '';
			$from = 'vibhanshu@4bell.com';
			$subject = __('modules.saasFront.emailVerification') . ' ' . config('app.name') . '!';

			$urlLink = getDomainSpecificUrl(route('get-email-verification', $user->email_verification_code), $company_name);
			$message = '<a href="' . $urlLink . '" target="_blank">Account Verification link</a>';
			$data = [
				'name' => $request->name,
				'to' => $to,
				"cc" => $cc,
				'from' => $from,
				'subject' => $subject,
				'urlLink'=> $urlLink,
				'message'=> $message
			];



			        $basecompany = GeneralMethod::baseCompany();
                    $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
                    $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
                    $attachments = array();
                    $cc = array();
                    $subject = "Company Profile Confirmation";	
					
					
					QueueMailService::dispatch(
						$to,
						$subject,
						'email.company-profile.email-confirmation',
						$data,
						$attachments,
						$fromAddress,
						$senderName,
						$cc,
						$company_id
					)->onConnection('emails');

                                            
                //  $result = $this->mailService->sendEmail($to, $subject,'email.company-profile.email-confirmation', $data, $attachments, $fromAddress, $senderName,$cc);


			// dd($urlLink);
			// $message = '<a href="' . $urlLink . '" target="_blank">Account Verification link</a>';


			// $sendEmail = GeneralMethod::sentEmailUsingSMTP($to, $cc, $subject, $message, $from);

			return Reply::dataOnly(['status' => 'success']);
		} else {
			return Reply::dataOnly(['status' => 'fail']);
		}


	}
	public function invitationCompanyStore_old(Request $request)
	{
		// dd("dkd");

		$company = new Company();

		try {
			$company->company_name = $request->company_name;
			$company->company_email = $request->email;
			$company->website = $request->website;
			// $company->company_size = $request->company_size;
			$company->company_phone = $request->contact_number;
			$company->company_type = $request->company_type;
			$company->app_duplicate = 2;
			$company->app_duplicate_days = 60;
			$company->bench_show_mode	= 2;
			//$company->career_page_link 	= str_slug($request->career_page_link, '-'); 
			$company->job_opening_text = 'Welcome!';
			$company->job_opening_title = 'We want people to thrive. We believe you do your best work when you feel your best.';
			$company->timezone = 'Asia/Kolkata';

			if (module_enabled('Subdomain')) {
				$company->sub_domain = $request->sub_domain;
			}
			$company->save();

			$user = new User();
			$user->company_id = $company->id;
			$user->name = $request->company_name;
			$user->mobile = $request->contact_number;

			$user->email = $request->email;
			$user->password = bcrypt($request->password);
			$user->super_password 			=   $request->password;
			$user->super_admin_login_code 	=  $company->id.rand(1111,9999);

			//$user->email_verification_code 	=	str_random(40);
			//$user->status 					=	'inactive';

			$user->email_verification_code = '';
			$user->status = 'active';

			$user->save();
			if ($admin = User::find($user->id)) {
				$admin->assignRole('admin');
			}


			if ($company->id > 0) {


				// Working with Categories
				$designationArr = '';
				$designationArr = GeneralMethod::department();
				if (!empty($designationArr)) {
					foreach ($designationArr as $dval) {
						if(!empty($dval)){
						$values = array(
							'company_id' => $company->id,
							'name' => $dval,
							'created_at' => date('Y-m-d'),
							'updated_at' => date('Y-m-d')
						);
						$insertGetId =  DB::table('job_categories')->insertGetId($values);
						$response = $this->elasticService->getClient()->update([
							'index' => ELASTIC_JOB_CATEGORIES,
							'id'    => $insertGetId,
							'retry_on_conflict' => 5,
							'body'  => [
								'doc' => [ 
									'id' =>  $insertGetId,
									'company_id' =>(int) $company->id,
									'name' => $dval,
									'created_at' => now()->toIso8601String(),
									'updated_at' => now()->toIso8601String()
								],
								'doc_as_upsert' => true,
								'detect_noop' => false
							],
						]);
					}
					}
				}

				//Working with Client
				$values = array(
					'company_id' => $company->id,
					'name' => $request->company_name,
					'website' => $request->website, 
					'self_company' => 1,
					'created_at' => date('Y-m-d'),
					'updated_at' => date('Y-m-d'),
					'email' => $request->email,
					'contact_person_name' => $request->company_name
				);
				DB::table('clients')->insert($values);
				$client_id = DB::getPdo()->lastInsertId();

				// Working with Client Contact Info 
				$values = array(
					'client_id' => $client_id,
					'contact_person_name' => $request->company_name,
					'contact_person_email' => $request->email,
					//'contact_person_number' 	=> $number, 
					'contact_person_designation' => 'Account Owner'
				);
				DB::table('client_contact_info')->insert($values);


				$cpriority = 90;
				$childStatusArr = array('Duplicate', 'Not Interested', 'Not Relevant', 'Profile Mismatch', 'Stability Issues','High Notice Period','High Salary Expectations','Interview Rejected','HR Rejected','Proxy/Fake','Other Reasons');
				foreach ($childStatusArr as $csVal) {

					$values = array(
						'company_id' => $company->id,
						'parent' => 7,
						'name' => $csVal,
						'priority' => $cpriority,
						'delete_status' => 0
					);
					DB::table('candidate_status')->insert($values);
					$cpriority++;
				}
				$childStatusArr		=	array('Onboarded', 'Backed Out');
				foreach($childStatusArr as $csVal)
				{
					
					$values 		=	array(
												'company_id' 	=> $company->id, 
												'parent' 		=> 6, 
												'name' 			=> $csVal, 
												'priority' 		=> $cpriority, 
												'delete_status'	=> 0
											);
					DB::table('candidate_status')->insert($values);
					$cpriority++;
				}





			}



			// STAFFING COMPANY INVITATION
			$s_company_invitation = $s_company_invitation_id = '';
			$s_company_invitation = $request->s_company_invitation;
			$s_company_invitation_id = $request->s_company_invitation_id;
			if (($s_company_invitation_id > 0) && ($s_company_invitation == 's_company_invitation')) {

				$rowData = DB::table('staffing_company_invitation')->select('*')->where('id', @$s_company_invitation_id)->first();

				$from_company_id = $rowData->from_company_id;
				$from_user_id = $rowData->from_user_id;
				$from_client_id = $rowData->from_client_id;

				// UPDATE STAFFING COMPANY INVITATION

				/*DB::table('staffing_company_invitation')
							  ->where('id', @$s_company_invitation_id)
							  ->limit(1) 
							  ->update(array('to_status' => 1, 'to_company_id' => $company->id, 'to_invitation_status' => 1));*/

				DB::table('staffing_company_invitation')
					->where('id', @$s_company_invitation_id)
					->limit(1)
					->update(array('to_company_id' => $company->id));

				// INSERT COMPANY RELATION 
				$values = array(
					'from_company_id' => $from_company_id,
					'from_client_id' => $from_client_id,
					'from_user_id' => $from_user_id,
					'from_status' => 1,

					'from_created_at' => date('Y-m-d'),
					'to_company_id' => $company->id,
					'to_client_id' => $client_id,
					'to_user_id' => $user->id,
					'to_status' => 1,
					'to_created_at' => date('Y-m-d'),
					'final_status' => 1,
					'created_at' => date('Y-m-d'),
					'updated_at' => date('Y-m-d'),
					'to_created_at' => date('Y-m-d')

				);
				//DB::table('staffing_company_relation')->insert($values); old

			}


			//assign admin role to default user
			$role = CustomRole::where('company_id', $company->id)->first();
			if ($role) {
				$user->roles()->attach($role->id);
				
				if ($role->id) {
					if ($request->company_type == 3) {

						DB::table('permission_role')
							->leftJoin('permissions', 'permissions.id', '=', 'permission_role.permission_id')
							->where('permissions.product_based', 0)
							->where('permission_role.role_id', $role->id)
							->delete();

						/*DELETE permission_role FROM permission_role
										INNER JOIN permissions ON permissions.id = permission_role.permission_id
										WHERE permissions.product_based = 0 and  permission_role.role_id = 114*/
					}

				}
		   }







			$custom_role_arr = array('Admin','Sub Administrator','Team Lead', 'Recruiter', 'Interviewer','Sales','Default');
			$role_company_id = '';
			$role_company_id = $company->id;
			foreach ($custom_role_arr as $rolename) {
				$role_priority = 1;
				$role_row = DB::table('roles')->select('*')->where('company_id', $role_company_id)->orderBy('roles.id', 'DESC')->first();
				if($role_row){
					if ($role_row->priority >= 0) {
						$role_priority = $role_row->priority + 1;
					}
				}



				$lastInsertId = '';
				$values = array(
					'company_id' => $role_company_id,
					'name' => strtolower($rolename),
					'display_name' => ($rolename=="Admin") ? 'Administrator' : $rolename,
					'guard_name' => 'web',
					'description' => ($rolename=="Admin") ? 'Admin is allowed to manage everything of the app.' : $rolename,
					'priority' => $role_priority
				);
				DB::table('roles')->insert($values);
				$lastInsertId = DB::getPdo()->lastInsertId();
                if($rolename=="Admin"){
					$valuesrole = array('user_id' => $user->id, 'role_id' => $lastInsertId);
					DB::table('role_user')->insertOrIgnore($valuesrole);
				}
				

				if ($lastInsertId > 0) {
					if ($request->company_type == 3) {
						$roleId = DB::table('permissions')
							->select('*')
							->where('permissions.status', 1)
							->where('permissions.product_based', 1)
							->orderBy('permissions.priority', 'ASC')
							->get();
					} else {
						$roleId = DB::table('permissions')
							->select('*')
							->where('permissions.status', 1)
							->orderBy('permissions.priority', 'ASC')
							->get();
					}

					if (!empty($roleId)) {
						foreach ($roleId as $roleVal) {
							$values = array(
								'permission_id' => $roleVal->id,
								'role_id' => $lastInsertId
							);
							// DB::table('permission_role')->insert($values);
							DB::table('role_has_permissions')->insertOrIgnore($values);
						}
					}
				}

			}


			
			if($client_id>0){
				GeneralMethod::clientStaffingPartner($client_id);
			}
			$this->user = $user;
			$view = view('saas-front.invited_registration_success', $this->data)->render();

			return Reply::dataOnly(['status' => 'success', 'view' => $view]);



		} catch (\Swift_TransportException $e) {
			DB::rollback();
			return Reply::error('Please contact administrator to set SMTP details to add company', 'smtp_error');
		} catch (\Exception $e) {
			DB::rollback();
			return Reply::error('Some error occurred when inserting the data. Please try again or contact support');
		}
		return Reply::dataOnly(['status' => 'success']);



	}
	public function invitationCompanyStore(Request $request) 
		{
			// dd("jdjdjd",$request->all());
	
        $company = new Company();
		
        try {
			
			if(!empty($request->direct_user)){

				$decode = base64_decode($request->direct_user);
				$usersGet					=	DB::table('users')->select('*')->where('id','=',$decode)->first();
				// dd($usersGet);

				$clientRow					=	DB::table('clients')->select('*')->where('company_id', $usersGet->company_id)->where('self_company', 1)->first();
				$company_id      			=	$usersGet->company_id;
				$userId						=	$usersGet->id;
				$staffing_partners_email	=	$_REQUEST['email'];


				if(DB::table('users')->select('*')->where('root_user', '=',1)->where('email', $staffing_partners_email)->exists()){
					$user_to = DB::table('users')->select('*')->where('root_user', '=',1)->where('email', $staffing_partners_email)->first();

					$clientInvitationCounter =	DB::table('staffing_company_invitation')->select('*')
					->where('from_company_id', $usersGet->company_id)
					->where('to_company_id',$user_to->company_id)
					->count();
				if($clientInvitationCounter == 0){	
					$clientRow_to = DB::table('clients')->select('*')->where('company_id', $user_to->company_id)->where('self_company', 1)->first();
					$staffing_company_invitation	=	array(
						'from_client_id'	=>	$clientRow->id,	
						'from_company_id'	=>	$company_id,
						'from_user_id'		=>	$usersGet->id,
						'from_status'		=>	1,
						'from_created_at'	=>	date('Y-m-d'),
						'to_company_id'		=>	$user_to->company_id,
						'to_email'			=>	$staffing_partners_email,
						'to_status'			=>	0
						//'job_id'			=>
						); 
						DB::table('staffing_company_invitation')->insert($staffing_company_invitation);	
						$invitationId 		=	DB::getPdo()->lastInsertId();
					
					$values = array(
						'from_company_id' => $company_id,
						'from_client_id' => $clientRow->id,
						'from_user_id' => $usersGet->id,
						'from_status' => 1,
						'from_created_at' => date('Y-m-d'),
		
						'to_company_id' => $user_to->company_id,
						'to_client_id' => @$clientRow_to->id,
						'to_user_id' => @$user_to->id,
						'to_status' => 1,
						'to_created_at' => date('Y-m-d'),
		
						'final_status' => 1,
						'created_at' => date('Y-m-d'),
						'updated_at' => date('Y-m-d')
					);
					DB::table('staffing_company_relation')->insert($values);
					$relationId 		=	DB::getPdo()->lastInsertId();
					GeneralMethod::appendClientNestedData($relationId);


						
				}else{
					$user_to = DB::table('users')->select('*')->where('root_user', '=',1)->where('email', $staffing_partners_email)->first();
					$clientRow_to = DB::table('clients')->select('*')->where('company_id', $user_to->company_id)->where('self_company', 1)->first();

					$from_company_id = $company_id;
					$from_client_id = $clientRow->id;
					$from_user_id = $usersGet->id;

					$values = array(
						'from_company_id' => $from_company_id,
						'from_client_id' => $from_client_id,
						'from_user_id' => $from_user_id,
						'from_status' => 1,
						'from_created_at' => date('Y-m-d'),
		
						'to_company_id' => $user_to->company_id,
						'to_client_id' =>@$clientRow_to->id,
						'to_user_id' => @$user_to->id,
						'to_status' => 1,
						'to_created_at' => date('Y-m-d'),
		
						'final_status' => 1,
						'created_at' => date('Y-m-d'),
						'updated_at' => date('Y-m-d')
					);
					if(!DB::table('staffing_company_relation')->select('*')->where('from_company_id', '=',$company_id)->where('to_company_id', '=',$user_to->company_id)->exists()){
						DB::table('staffing_company_relation')->insert($values);
						$relationId 		=	DB::getPdo()->lastInsertId();
						GeneralMethod::appendClientNestedData($relationId);
					}

				}														

				$this->user		=	$clientRow;
				$emailenabled 				=	false;
				$view 				=	view('saas-front.invited_registration_success', $this->data)->render();
				return Reply::dataOnly(['status' => 'success', 'view' => $view,'emailenabled' => $emailenabled]);	

									
				}else{
					$exists = DB::table('users')->select('*')->where('root_user', '=',0)->where('email', $staffing_partners_email)->first();
					if($exists){
						$emailenabled 				=	false;
						return Reply::dataOnly(['status' => 'failed', 'view' => '','emailenabled' => $emailenabled]);
					}
				}
					
			
				// dd($company_id,$userId,$staffing_partners_email);
				$clientRow					=	DB::table('clients')->select('*')->where('company_id', $company_id)->where('self_company', 1)->first();
				$staffing_company_invitation	=	array(
					'from_client_id'	=>	$clientRow->id,	
					'from_company_id'	=>	$company_id,
					'from_user_id'		=>	$userId,
					'from_status'		=>	1,
					'from_created_at'	=>	date('Y-m-d'),
					//'to_company_id'		=>,
					'to_email'			=>	$staffing_partners_email,
					'to_status'			=>	0
					//'job_id'			=>
				  );
				// $Companies = Company::find($company_id);

				DB::table('staffing_company_invitation')->insert($staffing_company_invitation);
				$invitationId	=	DB::getPdo()->lastInsertId();
				$request->s_company_invitation_id= $invitationId;

			}

            //  dd($request->s_company_invitation_id,$request->all());


				$company->company_name 			= $request->company_name;
				$company->company_email 		= $request->email;
				$company->website 				= $request->website;
				// $company->company_size 			= $request->company_size;
				$company->company_phone 		= $request->contact_number;
				$company->calling_code = @$request->contact_code;
				$company->company_type 			= $request->company_type;
				$company->industries = 131;
			    $company->bench_show_mode	= 2;
				$company->app_duplicate = 2;
				$company->app_duplicate_days = 60;
				// dd($request->company_type);
				//$company->career_page_link 	= str_slug($request->career_page_link, '-'); 
				$company->job_opening_text 		= 'Welcome!';
				$company->job_opening_title 	= 'We want people to thrive. We believe you do your best work when you feel your best.';
				$company->timezone 				= 'Asia/Kolkata';
	
				if (module_enabled('Subdomain')){
					$company->sub_domain = $request->sub_domain;
				}
            	$company->save();

				$package_default = Package::select('packages.*')->first();
				$package_default_details = Package::select('package_features.id','package_details.quantity','package_features.field_name','package_details.price','package_details.sub_total as finalprice','package_details.status','package_features.id as features_id')->join('package_details','package_details.package_id','=','packages.id')->join('package_features','package_details.package_feature_id','=','package_features.id')->where('package_details.package_id','=',$package_default->id)->where('is_trial','=',1)->get()->toArray();

			
			$duration_endDate = '';
			if (@$package_default->trial_duration > 0) {
				// dd(@$package_default->trial_duration);
				$duration_endDate = date('Y-m-d', strtotime(date('Y-m-d') . ' + ' . @$package_default->trial_duration . ' days'));
			} else {
				$duration_endDate = date('Y-m-d', strtotime(date('Y-m-d') . ' + 0 days'));
			}
		$t						=	date('Y-m-d H:i:s');
		$invoice_id				=	strtotime($t);
		// $company =
		$order_val				=	array('company_id' => @$company->id, 'company_type' => @$company->company_type);
		$invoice_val			=	array('company_id' => @$company->id, 'invoice_id' => @$invoice_id, 'company_type' => @$company->company_type);
		$active_val  			=	array('company_id' => @$company->id,'company_type' => @$company->company_type);
		$subtotal= 0;

			if(!empty($package_default_details)){
				foreach($package_default_details as $sArr){
					if($sArr['field_name'] == 'cvaccess') { 

						$order_val['cvaccess_startdate'] 	=	$invoice_val['cvaccess_startdate'] 	=  	date('Y-m-d');
						$order_val['cvaccess_enddate'] 		=	$invoice_val['cvaccess_enddate'] 	=  	@$duration_endDate;
						$order_val['cvaccess_status'] 		=	$invoice_val['cvaccess_status'] 	= 	@$sArr['status'];
						$order_val['cvaccess_quantity'] 	=	$invoice_val['cvaccess_quantity'] 	=  	@$sArr['quantity'];
						$order_val['cvaccess_price'] 		=	$invoice_val['cvaccess_price'] 		=  	@$sArr['price'];
						$order_val['cvaccess_totalprice'] 	=	$invoice_val['cvaccess_totalprice'] = 	@$sArr['finalprice'];
						
						$active_val['cvaccess_startdate']	=	date('Y-m-d');
						$active_val['cvaccess_enddate']		=	@$duration_endDate;
						$active_val['cvaccess_status']		=	@$sArr['status'];
						$active_val['cvaccess_quantity']	=	@$sArr['quantity'];
						$active_val['cvaccess_used']		= 	0;
						$active_val['cvaccess_balance']		=	@$sArr['quantity'];	
																			
						$subtotal							=  $subtotal + @$sArr['finalprice'];												 
																			
					}else if($sArr['field_name'] == 'users') {
						
						$order_val['users_startdate'] 		=	$invoice_val['users_startdate'] 	=  	date('Y-m-d');
						$order_val['users_enddate'] 		=	$invoice_val['users_enddate'] 		=  	@$duration_endDate;
						$order_val['users_status'] 			=	$invoice_val['users_status'] 		= 	@$sArr['status'];
						$order_val['users_quantity'] 		=	$invoice_val['users_quantity'] 		=  	@$sArr['quantity'];
						$order_val['users_price'] 			=	$invoice_val['users_price'] 		=  	@$sArr['price'];
						$order_val['users_totalprice'] 		=	$invoice_val['users_totalprice'] 	= 	@$sArr['finalprice'];
						
						$active_val['users_startdate']		=	date('Y-m-d');
						$active_val['users_enddate']		=	@$duration_endDate;
						$active_val['users_status']			=	@$sArr['status'];
						$active_val['users_quantity']		=	@$sArr['quantity'];
						$active_val['users_used']			= 	0;
						$active_val['users_balance']		=	@$sArr['quantity'];	
																			
						$subtotal							=  $subtotal + @$sArr['finalprice'];	
						
					}else if($sArr['field_name'] == 'new_bench_client_request') {
						
						$order_val['newbenchclientrequest_startdate'] 	=	$invoice_val['newbenchclientrequest_startdate'] 	=  date('Y-m-d');
						$order_val['newbenchclientrequest_enddate'] 	=	$invoice_val['newbenchclientrequest_enddate'] 	=  	@$duration_endDate;
						$order_val['newbenchclientrequest_status'] 		=	$invoice_val['newbenchclientrequest_status'] 		= 	@$sArr['status'];
						$order_val['newbenchclientrequest_quantity'] 	=	$invoice_val['newbenchclientrequest_quantity'] 		=  	@$sArr['quantity'];
						$order_val['newbenchclientrequest_price'] 		=	$invoice_val['newbenchclientrequest_price'] 		=  	@$sArr['price'];
						$order_val['newbenchclientrequest_totalprice'] 	=	$invoice_val['newbenchclientrequest_totalprice'] 	= 	@$sArr['finalprice'];
						
						$active_val['newbenchclientrequest_startdate']	=	date('Y-m-d');
						$active_val['newbenchclientrequest_enddate']	=	@$duration_endDate;
						$active_val['newbenchclientrequest_status']		=	@$sArr['status'];
						$active_val['newbenchclientrequest_quantity']	=	@$sArr['quantity'];
						$active_val['newbenchclientrequest_used']		= 	0;
						$active_val['newbenchclientrequest_balance']	=	@$sArr['quantity'];	
																			
						$subtotal										=	$subtotal + @$sArr['finalprice'];
					}else if($sArr['field_name'] == 'emails') {
						
						$order_val['emails_startdate'] 	=	$invoice_val['emails_startdate'] 	=  	date('Y-m-d');
						$order_val['emails_enddate'] 	=	$invoice_val['emails_enddate'] 		=  	@$duration_endDate;
						$order_val['emails_status'] 	=	$invoice_val['emails_status'] 		= 	@$sArr['status'];
						$order_val['emails_quantity'] 	=	$invoice_val['emails_quantity'] 	=  	@$sArr['quantity'];
						$order_val['emails_price'] 		=	$invoice_val['emails_price'] 		=  	@$sArr['price'];
						$order_val['emails_totalprice'] =	$invoice_val['emails_totalprice'] 	= 	@$sArr['finalprice'];
						
						$active_val['emails_startdate']	=	date('Y-m-d');
						$active_val['emails_enddate']	=	@$duration_endDate;
						$active_val['emails_status']	=	@$sArr['status'];
						$active_val['emails_quantity']	=	@$sArr['quantity'];
						$active_val['emails_used']		= 	0;
						$active_val['emails_balance']	=	@$sArr['quantity'];	
																			
						$subtotal						=	$subtotal + @$sArr['finalprice'];
						
					}else if($sArr['field_name'] == 'oldcvaccess') {
						
						$order_val['oldcvaccess_startdate'] 	=	$invoice_val['oldcvaccess_startdate'] 	=  	date('Y-m-d');
						$order_val['oldcvaccess_enddate'] 		=	$invoice_val['oldcvaccess_enddate'] 	=  	@$duration_endDate;
						$order_val['oldcvaccess_status'] 		=	$invoice_val['oldcvaccess_status'] 		= 	@$sArr['status'];
						$order_val['oldcvaccess_quantity'] 		=	$invoice_val['oldcvaccess_quantity'] 	=  	@$sArr['quantity'];
						$order_val['oldcvaccess_price'] 		=	$invoice_val['oldcvaccess_price'] 		=  	@$sArr['price'];
						$order_val['oldcvaccess_totalprice'] 	=	$invoice_val['oldcvaccess_totalprice'] 	= 	@$sArr['finalprice'];
						
						$active_val['oldcvaccess_startdate']	=	date('Y-m-d');
						$active_val['oldcvaccess_enddate']		=	@$duration_endDate;
						$active_val['oldcvaccess_status']		=	@$sArr['status'];
						$active_val['oldcvaccess_quantity']		=	@$sArr['quantity'];
						$active_val['oldcvaccess_used']			= 	0;
						$active_val['oldcvaccess_balance']		=	@$sArr['quantity'];	
																			
						$subtotal								=	$subtotal + @$sArr['finalprice'];
						
					}else if($sArr['field_name'] == 'new_client_request') {
						
						$order_val['newclientrequest_startdate'] 	=	$invoice_val['newclientrequest_startdate'] 	=  	date('Y-m-d');
						$order_val['newclientrequest_enddate'] 		=	$invoice_val['newclientrequest_enddate'] 	=  	@$duration_endDate;
						$order_val['newclientrequest_status'] 		=	$invoice_val['newclientrequest_status'] 	= 	@$sArr['status'];
						$order_val['newclientrequest_quantity'] 	=	$invoice_val['newclientrequest_quantity'] 	=  	@$sArr['quantity'];
						$order_val['newclientrequest_price'] 		=	$invoice_val['newclientrequest_price'] 		=  	@$sArr['price'];
						$order_val['newclientrequest_totalprice'] 	=	$invoice_val['newclientrequest_totalprice'] = 	@$sArr['finalprice'];
						
						$active_val['newclientrequest_startdate']	=	date('Y-m-d');
						$active_val['newclientrequest_enddate']		=	@$duration_endDate;
						$active_val['newclientrequest_status']		=	@$sArr['status'];
						$active_val['newclientrequest_quantity']	=	@$sArr['quantity'];
						$active_val['newclientrequest_used']		= 	0;
						$active_val['newclientrequest_balance']		=	@$sArr['quantity'];	
																			
						$subtotal									=  $subtotal + @$sArr['finalprice'];
																		
					}else if($sArr['field_name'] == 'new_job_request') {
						
						$order_val['newjobrequest_startdate'] 	=	$invoice_val['newjobrequest_startdate'] 	=  	date('Y-m-d');
						$order_val['newjobrequest_enddate'] 		=	$invoice_val['newjobrequest_enddate'] 	=  	@$duration_endDate;
						$order_val['newjobrequest_status'] 		=	$invoice_val['newjobrequest_status'] 		= 	@$sArr['status'];
						$order_val['newjobrequest_quantity'] 	=	$invoice_val['newjobrequest_quantity'] 		=  	@$sArr['quantity'];
						$order_val['newjobrequest_price'] 		=	$invoice_val['newjobrequest_price'] 		=  	@$sArr['price'];
						$order_val['newjobrequest_totalprice'] 	=	$invoice_val['newjobrequest_totalprice'] 	= 	@$sArr['finalprice'];
						
						$active_val['newjobrequest_startdate']	=	date('Y-m-d');
						$active_val['newjobrequest_enddate']	=	@$duration_endDate;
						$active_val['newjobrequest_status']		=	@$sArr['status'];
						$active_val['newjobrequest_quantity']	=	@$sArr['quantity'];
						$active_val['newjobrequest_used']		= 	0;
						$active_val['newjobrequest_balance']	=	@$sArr['quantity'];	
																			
						$subtotal								=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'boostkeywords') {
						
						$order_val['boostkeywords_startdate'] 		=	$invoice_val['boostkeywords_startdate'] 	=  	date('Y-m-d');
						$order_val['boostkeywords_enddate'] 		=	$invoice_val['boostkeywords_enddate'] 		=  @$duration_endDate;
						$order_val['boostkeywords_status'] 			=	$invoice_val['boostkeywords_status'] 		= 	@$sArr['status'];
						$order_val['boostkeywords_quantity'] 		=	$invoice_val['boostkeywords_quantity'] 		=  	@$sArr['quantity'];
						
						$order_val['boostkeywords_price'] 			=	$invoice_val['boostkeywords_price'] 		=  	@$sArr['price'];
						$order_val['boostkeywords_totalprice'] 		=	$invoice_val['boostkeywords_totalprice'] 	= 	@$sArr['finalprice'];
						$string_version 							=	implode(' , ', @$sArr['keywords']);
						$order_val['boostkeywords_text'] 			=	$invoice_val['boostkeywords_text'] 			=  	@$string_version;
						
						$active_val['boostkeywords_startdate']		=	date('Y-m-d');
						$active_val['boostkeywords_enddate']		=	@$duration_endDate;
						$active_val['boostkeywords_status']			=	@$sArr['status'];
						$active_val['boostkeywords_text']			=	@$string_version;
						
						$active_val['boostkeywords_quantity']		=	@$sArr['quantity'];
						$active_val['boostkeywords_used']			= 	0;
						$active_val['boostkeywords_balance']		=	@$sArr['quantity'];	
																			
						$subtotal									=	$subtotal + @$sArr['finalprice'];
						
						
					}else if($sArr['field_name'] == 'InternalDatabaseaccess') {
						
							$order_val['internaldatabaseaccess_status']=$invoice_val['internaldatabaseaccess_status']=	@$sArr['status'];
							$order_val['internaldatabaseaccess_quantity']=$invoice_val['internaldatabaseaccess_quantity']=@$sArr['quantity'];
							$order_val['internaldatabaseaccess_price']=	$invoice_val['internaldatabaseaccess_price']= @$sArr['price'];
							$order_val['internaldatabaseaccess_totalprice']=$invoice_val['internaldatabaseaccess_totalprice']=@$sArr['finalprice'];
								
							$active_val['internaldatabaseaccess_status']		=	@$sArr['status'];	
							$active_val['internaldatabaseaccess_quantity']		=	@$sArr['quantity'];
							$active_val['internaldatabaseaccess_used']			= 	0;
							$active_val['internaldatabaseaccess_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'Interviewtracker') {
						
							$order_val['interviewtracker_status']=$invoice_val['interviewtracker_status']=	@$sArr['status'];
							$order_val['interviewtracker_quantity']=$invoice_val['interviewtracker_quantity']=@$sArr['quantity'];
							$order_val['interviewtracker_price']=	$invoice_val['interviewtracker_price']= @$sArr['price'];
							$order_val['interviewtracker_totalprice']=$invoice_val['interviewtracker_totalprice']=@$sArr['finalprice'];
								
							$active_val['interviewtracker_status']		=	@$sArr['status'];	
							$active_val['interviewtracker_quantity']		=	@$sArr['quantity'];
							$active_val['interviewtracker_used']			= 	0;
							$active_val['interviewtracker_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'Offertracker') {
						
							$order_val['offertracker_status']=$invoice_val['offertracker_status']=	@$sArr['status'];
							$order_val['offertracker_quantity']=$invoice_val['offertracker_quantity']=@$sArr['quantity'];
							$order_val['offertracker_price']=	$invoice_val['offertracker_price']= @$sArr['price'];
							$order_val['offertracker_totalprice']=$invoice_val['offertracker_totalprice']=@$sArr['finalprice'];
								
							$active_val['offertracker_status']		=	@$sArr['status'];	
							$active_val['offertracker_quantity']		=	@$sArr['quantity'];
							$active_val['offertracker_used']			= 	0;
							$active_val['offertracker_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'Joiningtracker') {
						
							$order_val['joiningtracker_status']=$invoice_val['joiningtracker_status']=	@$sArr['status'];
							$order_val['joiningtracker_quantity']=$invoice_val['joiningtracker_quantity']=@$sArr['quantity'];
							$order_val['joiningtracker_price']=	$invoice_val['joiningtracker_price']= @$sArr['price'];
							$order_val['joiningtracker_totalprice']=$invoice_val['joiningtracker_totalprice']=@$sArr['finalprice'];
								
							$active_val['joiningtracker_status']		=	@$sArr['status'];	
							$active_val['joiningtracker_quantity']		=	@$sArr['quantity'];
							$active_val['joiningtracker_used']			= 	0;
							$active_val['joiningtracker_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'PermissionforSHaringCVtoclienys') {
						
							$order_val['PermissionforSHaringCVtoclienys_status']=$invoice_val['PermissionforSHaringCVtoclienys_status']=	@$sArr['status'];
							$order_val['PermissionforSHaringCVtoclienys_quantity']=$invoice_val['PermissionforSHaringCVtoclienys_quantity']=@$sArr['quantity'];
							$order_val['PermissionforSHaringCVtoclienys_price']=	$invoice_val['PermissionforSHaringCVtoclienys_price']= @$sArr['price'];
							$order_val['PermissionforSHaringCVtoclienys_totalprice']=$invoice_val['PermissionforSHaringCVtoclienys_totalprice']=@$sArr['finalprice'];
								
							$active_val['PermissionforSHaringCVtoclienys_status']		=	@$sArr['status'];	
							$active_val['PermissionforSHaringCVtoclienys_quantity']		=	@$sArr['quantity'];
							$active_val['PermissionforSHaringCVtoclienys_used']			= 	0;
							$active_val['PermissionforSHaringCVtoclienys_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'PermamentRecruitmentLeadIndia') {
						
							$order_val['PermamentRecruitmentLeadIndia_status']=$invoice_val['PermamentRecruitmentLeadIndia_status']=	@$sArr['status'];
							$order_val['PermamentRecruitmentLeadIndia_quantity']=$invoice_val['PermamentRecruitmentLeadIndia_quantity']=@$sArr['quantity'];
							$order_val['PermamentRecruitmentLeadIndia_price']=	$invoice_val['PermamentRecruitmentLeadIndia_price']= @$sArr['price'];
							$order_val['PermamentRecruitmentLeadIndia_totalprice']=$invoice_val['PermamentRecruitmentLeadIndia_totalprice']=@$sArr['finalprice'];
								
							$active_val['PermamentRecruitmentLeadIndia_status']		=	@$sArr['status'];	
							$active_val['PermamentRecruitmentLeadIndia_quantity']		=	@$sArr['quantity'];
							$active_val['PermamentRecruitmentLeadIndia_used']			= 	0;
							$active_val['PermamentRecruitmentLeadIndia_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'PermamentRecruitmentLeadInternational') {
						
							$order_val['PermamentRecruitmentLeadInternational_status']=$invoice_val['PermamentRecruitmentLeadInternational_status']=	@$sArr['status'];
							$order_val['PermamentRecruitmentLeadInternational_quantity']=$invoice_val['PermamentRecruitmentLeadInternational_quantity']=@$sArr['quantity'];
							$order_val['PermamentRecruitmentLeadInternational_price']=	$invoice_val['PermamentRecruitmentLeadInternational_price']= @$sArr['price'];
							$order_val['PermamentRecruitmentLeadInternational_totalprice']=$invoice_val['PermamentRecruitmentLeadInternational_totalprice']=@$sArr['finalprice'];
								
							$active_val['PermamentRecruitmentLeadInternational_status']		=	@$sArr['status'];	
							$active_val['PermamentRecruitmentLeadInternational_quantity']		=	@$sArr['quantity'];
							$active_val['PermamentRecruitmentLeadInternational_used']			= 	0;
							$active_val['PermamentRecruitmentLeadInternational_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'ContractRecruitmentLeadIndia') {
						
							$order_val['ContractRecruitmentLeadIndia_status']=$invoice_val['ContractRecruitmentLeadIndia_status']=	@$sArr['status'];
							$order_val['ContractRecruitmentLeadIndia_quantity']=$invoice_val['ContractRecruitmentLeadIndia_quantity']=@$sArr['quantity'];
							$order_val['ContractRecruitmentLeadIndia_price']=	$invoice_val['ContractRecruitmentLeadIndia_price']= @$sArr['price'];
							$order_val['ContractRecruitmentLeadIndia_totalprice']=$invoice_val['ContractRecruitmentLeadIndia_totalprice']=@$sArr['finalprice'];
								
							$active_val['ContractRecruitmentLeadIndia_status']		=	@$sArr['status'];	
							$active_val['ContractRecruitmentLeadIndia_quantity']		=	@$sArr['quantity'];
							$active_val['ContractRecruitmentLeadIndia_used']			= 	0;
							$active_val['ContractRecruitmentLeadIndia_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'ContractRecruitmentLeadInternational') {
						
							$order_val['ContractRecruitmentLeadInternational_status']=$invoice_val['ContractRecruitmentLeadInternational_status']=	@$sArr['status'];
							$order_val['ContractRecruitmentLeadInternational_quantity']=$invoice_val['ContractRecruitmentLeadInternational_quantity']=@$sArr['quantity'];
							$order_val['ContractRecruitmentLeadInternational_price']=	$invoice_val['ContractRecruitmentLeadInternational_price']= @$sArr['price'];
							$order_val['ContractRecruitmentLeadInternational_totalprice']=$invoice_val['ContractRecruitmentLeadInternational_totalprice']=@$sArr['finalprice'];
								
							$active_val['ContractRecruitmentLeadInternational_status']		=	@$sArr['status'];	
							$active_val['ContractRecruitmentLeadInternational_quantity']		=	@$sArr['quantity'];
							$active_val['ContractRecruitmentLeadInternational_used']			= 	0;
							$active_val['ContractRecruitmentLeadInternational_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'BenchSalesIndia') {
						
							$order_val['BenchSalesIndia_status']=$invoice_val['BenchSalesIndia_status']=	@$sArr['status'];
							$order_val['BenchSalesIndia_quantity']=$invoice_val['BenchSalesIndia_quantity']=@$sArr['quantity'];
							$order_val['BenchSalesIndia_price']=	$invoice_val['BenchSalesIndia_price']= @$sArr['price'];
							$order_val['BenchSalesIndia_totalprice']=$invoice_val['BenchSalesIndia_totalprice']=@$sArr['finalprice'];
								
							$active_val['BenchSalesIndia_status']		=	@$sArr['status'];	
							$active_val['BenchSalesIndia_quantity']		=	@$sArr['quantity'];
							$active_val['BenchSalesIndia_used']			= 	0;
							$active_val['BenchSalesIndia_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'BenchSalesInternational') {
						
							$order_val['BenchSalesInternational_status']=$invoice_val['BenchSalesInternational_status']=	@$sArr['status'];
							$order_val['BenchSalesInternational_quantity']=$invoice_val['BenchSalesInternational_quantity']=@$sArr['quantity'];
							$order_val['BenchSalesInternational_price']=	$invoice_val['BenchSalesInternational_price']= @$sArr['price'];
							$order_val['BenchSalesInternational_totalprice']=$invoice_val['BenchSalesInternational_totalprice']=@$sArr['finalprice'];
								
							$active_val['BenchSalesInternational_status']		=	@$sArr['status'];	
							$active_val['BenchSalesInternational_quantity']		=	@$sArr['quantity'];
							$active_val['BenchSalesInternational_used']			= 	0;
							$active_val['BenchSalesInternational_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'BenchClientAcccessIndia') {
						
							$order_val['BenchClientAcccessIndia_status']=$invoice_val['BenchClientAcccessIndia_status']=	@$sArr['status'];
							$order_val['BenchClientAcccessIndia_quantity']=$invoice_val['BenchClientAcccessIndia_quantity']=@$sArr['quantity'];
							$order_val['BenchClientAcccessIndia_price']=	$invoice_val['BenchClientAcccessIndia_price']= @$sArr['price'];
							$order_val['BenchClientAcccessIndia_totalprice']=$invoice_val['BenchClientAcccessIndia_totalprice']=@$sArr['finalprice'];
								
							$active_val['BenchClientAcccessIndia_status']		=	@$sArr['status'];	
							$active_val['BenchClientAcccessIndia_quantity']		=	@$sArr['quantity'];
							$active_val['BenchClientAcccessIndia_used']			= 	0;
							$active_val['BenchClientAcccessIndia_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'BenchClientAccessInternational') {
						
							$order_val['BenchClientAccessInternational_status']=$invoice_val['BenchClientAccessInternational_status']=	@$sArr['status'];
							$order_val['BenchClientAccessInternational_quantity']=$invoice_val['BenchClientAccessInternational_quantity']=@$sArr['quantity'];
							$order_val['BenchClientAccessInternational_price']=	$invoice_val['BenchClientAccessInternational_price']= @$sArr['price'];
							$order_val['BenchClientAccessInternational_totalprice']=$invoice_val['BenchClientAccessInternational_totalprice']=@$sArr['finalprice'];
								
							$active_val['BenchClientAccessInternational_status']		=	@$sArr['status'];	
							$active_val['BenchClientAccessInternational_quantity']		=	@$sArr['quantity'];
							$active_val['BenchClientAccessInternational_used']			= 	0;
							$active_val['BenchClientAccessInternational_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}else if($sArr['field_name'] == 'BoostKeywordsIndia') {
						
							$order_val['BoostKeywordsIndia_status']=$invoice_val['BoostKeywordsIndia_status']=	@$sArr['status'];
							$order_val['BoostKeywordsIndia_quantity']=$invoice_val['BoostKeywordsIndia_quantity']=@$sArr['quantity'];
							$order_val['BoostKeywordsIndia_price']=	$invoice_val['BoostKeywordsIndia_price']= @$sArr['price'];
							$order_val['BoostKeywordsIndia_totalprice']=$invoice_val['BoostKeywordsIndia_totalprice']=@$sArr['finalprice'];
							$string_version 							=	"";//implode(' , ', @$sArr['keywords']);
							$order_val['boostkeywords_text'] 			=	$invoice_val['boostkeywords_text'] 			=  	@$string_version;
								
							$active_val['BoostKeywordsIndia_status']		=	@$sArr['status'];	
							$active_val['BoostKeywordsIndia_quantity']		=	@$sArr['quantity'];
							$active_val['BoostKeywordsIndia_used']			= 	0;
							$active_val['BoostKeywordsIndia_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
							$active_val['boostkeywords_text']			=	@$string_version;
								
					}else if($sArr['field_name'] == 'BoostKeywordsInternational') {
						
							$order_val['BoostKeywordsInternational_status']=$invoice_val['BoostKeywordsInternational_status']=	@$sArr['status'];
							$order_val['BoostKeywordsInternational_quantity']=$invoice_val['BoostKeywordsInternational_quantity']=@$sArr['quantity'];
							$order_val['BoostKeywordsInternational_price']=	$invoice_val['BoostKeywordsInternational_price']= @$sArr['price'];
							$order_val['BoostKeywordsInternational_totalprice']=$invoice_val['BoostKeywordsInternational_totalprice']=@$sArr['finalprice'];
							$string_version 							=	"";//implode(' , ', @$sArr['keywords']);
							$order_val['boostkeywords_text'] 			=	$invoice_val['boostkeywords_text'] 			=  	@$string_version;
								
							$active_val['BoostKeywordsInternational_status']		=	@$sArr['status'];	
							$active_val['BoostKeywordsInternational_quantity']		=	@$sArr['quantity'];
							$active_val['BoostKeywordsInternational_used']			= 	0;
							$active_val['BoostKeywordsInternational_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
							$active_val['boostkeywords_text']			=	@$string_version;
								
					}else if($sArr['field_name'] == 'Limitperdayforleadsconsumptions') {
						
							$order_val['Limitperdayforleadsconsumptions_status']=$invoice_val['Limitperdayforleadsconsumptions_status']=	@$sArr['status'];
							$order_val['Limitperdayforleadsconsumptions_quantity']=$invoice_val['Limitperdayforleadsconsumptions_quantity']=@$sArr['quantity'];
							$order_val['Limitperdayforleadsconsumptions_price']=	$invoice_val['Limitperdayforleadsconsumptions_price']= @$sArr['price'];
							$order_val['Limitperdayforleadsconsumptions_totalprice']=$invoice_val['Limitperdayforleadsconsumptions_totalprice']=@$sArr['finalprice'];
								
							$active_val['Limitperdayforleadsconsumptions_status']		=	@$sArr['status'];	
							$active_val['Limitperdayforleadsconsumptions_quantity']		=	@$sArr['quantity'];
							$active_val['Limitperdayforleadsconsumptions_used']			= 	0;
							$active_val['Limitperdayforleadsconsumptions_balance']		=	@$sArr['quantity'];	
							$subtotal									=	$subtotal + @$sArr['finalprice'];
								
					}
				}
			}

		$active_val['package_id'] = $package_default->id;
		$invoice_val['package_id'] = $package_default->id;
		$order_val['package_id'] = $package_default->id;

		$active_val['startdate'] =date('Y-m-d');
		$active_val['enddate'] = $duration_endDate;
		$active_val['packages_status'] = 1;
		$active_val['payment_status'] = 0;

		$invoice_val['startdate'] = date('Y-m-d');
		$invoice_val['enddate'] = $duration_endDate;
		$invoice_val['packages_status'] = 1;
		$invoice_val['payment_status'] = 0;

		$order_val['startdate'] =date('Y-m-d');
		$order_val['enddate'] = $duration_endDate;
		$order_val['packages_status'] = 1;
		$order_val['payment_status'] = 0;
		
		DB::table('package_order')->insert($order_val);  
		$package_order_id    				=	DB::getPdo()->lastInsertId();
		
		$invoice_val['package_order_id'] 	=	@$package_order_id;	
		$invoice_val['subtotal'] 			=	@$subtotal;				
		DB::table('package_invoice')->insert($invoice_val);  
		$package_invoice_id    				=	DB::getPdo()->lastInsertId();


		if(!empty($package_default_details)){
			foreach($package_default_details as $sArr){
				if((int) @$sArr['features_id'] > 0){
				$PackageOrderItem = new PackageOrderItem();
				$PackageOrderItem->package_id = $package_default->id;
				$PackageOrderItem->package_order_id = $package_order_id;
				$PackageOrderItem->invoice_id = $package_invoice_id;
				$PackageOrderItem->	feature_id = (int) @$sArr['features_id'];
				$PackageOrderItem->qty = @$sArr['quantity'];
				$PackageOrderItem->price = @$sArr['price'];
				$PackageOrderItem->sub_total = @$sArr['finalprice'];
				$PackageOrderItem->status = @$sArr['status'];
				$PackageOrderItem->save();
				}

			}
		}
		
		DB::table('package_active')->insert($active_val);

		$currency = GeneralMethod::convertINRToUSD();
		$company_id = @$company->id;
		$company_coins_free = DB::table('coin_packages')
        ->where('coin_packages.custome_pck',2)
        ->select('coin_packages.*')
        ->get()
        ->map(function ($coinOrder) {
            $coinOrder->coin_order_events = DB::table('coin_events')
                ->where('event_type', $coinOrder->event_type)
                ->select('coin_events.*')
				->where('status','=',1)
                ->get()
                ->toArray();
            return (array) $coinOrder;
        })
        ->toArray();

        if(@$company_coins_free){

            foreach($company_coins_free as $package_sales){

                $newDate = date("Y-m-d");
                $currentDate = date("Y-m-d"); 
                $newDate = date("Y-m-d", strtotime($currentDate . " +".$package_sales['days']." days")); 
                // dd($currentDate,$newDate);

                    $coin_orders = [
                        'coin_package_id'=>$package_sales['id'],
						"currency" =>$currency,
                        'price'=>($currency=='INR') ? $package_sales['price'] : $package_sales['price_usd'],
                        'coins'=>$package_sales['coins'],
                        'days'=>$package_sales['days'],
                        'event_type'=>$package_sales['event_type'],
                        'company_id'=>$company_id,
                        'discount'=>$package_sales['discount'],
                        'status'=>1,
                        'created_at'=>date("Y-m-d H:i:s"),
                        'updated_at'=>date("Y-m-d H:i:s")	
                    ];
               
                    $coin_order_id = DB::table('coin_orders')->insertGetId($coin_orders);
					
						$response = $this->elasticService->getClient()->update([
							'index' => ELASTIC_COIN_ORDERS,
							'id'    => $coin_order_id,
							'retry_on_conflict' => 5,
							'body'  => [
								'doc' => [ 
									'id' =>  $coin_order_id,
									'coin_package_id'=>$package_sales['id'],
									"currency" =>$currency,
									'price'=>($currency=='INR') ? $package_sales['price'] : $package_sales['price_usd'],
									'coins'=>$package_sales['coins'],
									'days'=> $package_sales['days'],
									'event_type'=>$package_sales['event_type'],
									'company_id'=>$company_id,
									'discount'=>$package_sales['discount'],
									'status'=>1,
									'updated_at' => now()->toIso8601String(),
									'created_at' => now()->toIso8601String()
								],
								'doc_as_upsert' => true,
								'detect_noop' => false
							],
						]);
						
                    foreach($package_sales['coin_order_events'] as $package_value){
                        $coin_order_events = [
                            'company_id'=>$company_id,
                            'coin_event_id'=>$package_value->id,
                            'deductions'=>$package_value->default_rate,
                            'deduction_type'=>$package_value->deduction_type,
                            'coin_order_id'=>$coin_order_id,
                            'required_coin'=>$package_value->minimum_required,
                            'event_type'=>$package_value->event_type
                        ];
                        // dd($coin_order_events);
                        $eventsId = DB::table('coin_order_events')->insertGetId($coin_order_events);

						$response = $this->elasticService->getClient()->update([
								'index' => ELASTIC_COIN_ORDER_EVENTS,
								'id'    => $eventsId,
								'retry_on_conflict' => 5,
								'body'  => [
									'doc' => [ 
										'id' =>  $eventsId,
										'company_id'=>$company_id,
										'coin_event_id'=>$package_value->id,
										'deductions'=>$package_value->default_rate,
										'deduction_type'=>$package_value->deduction_type,
										'coin_order_id'=>$coin_order_id,
										'required_coin'=>$package_value->minimum_required,
										'event_type'=>$package_value->event_type
									],
									'doc_as_upsert' => true,
									'detect_noop' => false
								],
							]);

                    }

                    $company_coins = [
                            'coin_order_id'=>$coin_order_id,
                            'company_id'=>$company_id,
                            'event_type'=>$package_sales['event_type'],
							"currency" =>$currency,
                            'amount'=>($currency=='INR') ? $package_sales['price'] : $package_sales['price_usd'],
                            'discount'=>$package_sales['discount'],
                            'coins'=>$package_sales['coins'],
							'actual_coins'=>$package_sales['coins'],
                            'balance_coin'=>0,
                            'status'=>'valid',
                            'free_pack_status'=>'active',
                            'start_date'=>$currentDate,
                            'end_date'=>$newDate,
							'free_update_date'=>$currentDate,
                            'created_at'=>date("Y-m-d H:i:s"),
                            'updated_at'=>date("Y-m-d H:i:s")				
                    ];
                    $insertGetId = DB::table('company_coins')->insertGetId($company_coins);
					$response = $this->elasticService->getClient()->update([
							'index' => ELASTIC_COMPANY_COINS,
							'id'    => $insertGetId,
							'retry_on_conflict' => 5,
							'body'  => [
								'doc' => [ 
									'id' =>  $insertGetId,
									'coin_order_id' => $coin_order_id,
									'company_id' => $company_id,
									'event_type' => $package_sales['event_type'],
									'currency' => $currency,
									'amount' => ($currency=='INR') ? $package_sales['price'] : $package_sales['price_usd'],
									'discount' => $package_sales['discount'],
									'coins' => $package_sales['coins'],
									'actual_coins' => $package_sales['coins'],
									'balance_coin' => 0,
									'status' => 'valid',
									'free_pack_status' => 'active',
									'start_date' => ($currentDate) ? Carbon::parse($currentDate)->toIso8601String() : null,
									'end_date' => ($newDate) ? Carbon::parse($newDate)->toIso8601String() : null,
									'free_update_date' => ($currentDate) ? Carbon::parse($currentDate)->toIso8601String() : null,
									'auto_team_coins' => now()->toIso8601String(),
									'auto_job_coins' => now()->toIso8601String(),
									'updated_at' => now()->toIso8601String(),
									'created_at' => now()->toIso8601String(),
								],
								'doc_as_upsert' => true,
								'detect_noop' => false
							],
						]);


                    $coinEvents = DB::table('coin_orders')
                    ->join('coin_packages','coin_packages.id','=','coin_orders.coin_package_id')
                    ->join('coin_package_types','coin_package_types.id','=','coin_packages.coin_package_type_id')
                    ->where('coin_orders.id',$coin_order_id)
                    ->select('coin_package_types.title')->first();
                    $coinEventspackage = ($coinEvents) ? $coinEvents->title : '';
                    
                    $coin_transection_logs = [
                            'coin_order_id'=>$coin_order_id,
                            'from_company_id'=>$company_id,
                            'company_id'=>$company_id,
                            'event_by'=>1,
                            'event'=> $coinEventspackage. " ",
							"currency" =>$currency,
                            'amount'=>($currency=='INR') ? $package_sales['price'] : $package_sales['price_usd'],
                            'discount'=>$package_sales['discount'],
                            'coins'=>$package_sales['coins'],
							'remain_coins'=>$package_sales['coins'],
							'actual_coins'=>$package_sales['coins'],
                            'transection_type'=>'credit',
                            'settlement_coins'=>0,
                            'event_type'=>$package_sales['event_type'],
                            'start_date'=>$currentDate,
                            'end_date'=>$newDate,
                            'created_at'=>date("Y-m-d H:i:s"),
                            'updated_at'=>date("Y-m-d H:i:s")	
                    ];

                    $transection_logsId = DB::table('coin_transection_logs')->insertGetId($coin_transection_logs);

					$response = $this->elasticService->getClient()->update([
                                'index' => ELASTIC_COIN_TRANSECTION_LOGS,
                                'id'    => $transection_logsId,
                                'retry_on_conflict' => 5,
                                'body'  => [
                                    'doc' => [ 
										'coin_order_id'=>$coin_order_id,
										'from_company_id'=>$company_id,
										'company_id'=>$company_id,
										'event_by'=>1,
										'event'=> $coinEventspackage. " ",
										"currency" =>$currency,
										'amount'=>($currency=='INR') ? $package_sales['price'] : $package_sales['price_usd'],
										'discount'=>$package_sales['discount'],
										'coins'=>$package_sales['coins'],
										'transection_type'=>'credit',
										'settlement_coins'=>0,
										'event_type'=>$package_sales['event_type'],
                                        'start_date' => ($currentDate) ? Carbon::parse($currentDate)->toIso8601String() : null,
                                        'end_date' => ($newDate) ? Carbon::parse($newDate)->toIso8601String()  : null,
                                        'created_at' => now()->toIso8601String(),
                                        'updated_at' => now()->toIso8601String()
                                    ],
                                    'doc_as_upsert' => true,
                                    'detect_noop' => false
                                ],
                        ]);

					
                
            }
        }

		

				$user 							=	new User();
				$user->company_id 				=	$company->id;
				$user->name 					=	$request->company_name;
				$user->mobile 					=	$request->contact_number;
				$user->calling_code = @$request->contact_code;
				$user->root_user 					=	1;
				$user->email 					=	$request->email;
				$user->password 				=	bcrypt($request->password);
				$user->super_password 			=   $request->password;
				$user->super_admin_login_code 			=  $company->id.rand(1111,9999);

				if(!empty($request->direct_user)){
					$user->email_verification_code 	=	str_random(40);
					$user->status 					=	'inactive';
					$user->working_status = 1;
				}else{
					$user->email_verification_code 	=	'';
					$user->status 					=	'active';
				}
				
				$user->save();
				if ($admin = User::find($user->id)) {
					$admin->assignRole('admin');
				}

				if(!empty($request->direct_user)){
						$to = $request->email;  
						$subject = __('modules.saasFront.emailVerification') . ' ' . config('app.name') . '!';
						$urlLink = getDomainSpecificUrl(route('get-email-verification', $user->email_verification_code), $request->company_name);
						$message = '<a href="' . $urlLink . '" target="_blank">Account Verification link</a>';

						$data = [
							'name' => $request->name,
							'to' => $to,
							'urlLink'=> $urlLink,
							'message'=> $message
						];
						
						
							$basecompany = GeneralMethod::baseCompany();
							$senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
							$fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
							$attachments = array();
							$cc = array();
							$subject = "Company Profile Confirmation";		

							
							QueueMailService::dispatch(
								$to,
								$subject,
								'email.company-profile.email-confirmation',
								$data,
								$attachments,
								$fromAddress,
								$senderName,
								$cc,
								$company_id
							)->onConnection('emails');
				}


				if($company->id > 0)
				{

						
											// Working with Categories
											$designationArr  = '';
											$designationArr  = GeneralMethod::department();
											if(!empty($designationArr))
											{
												foreach($designationArr as $dval)
												{
													if(!empty($dval)){
													$values = array(
																		'company_id' 		=> $company->id,
																		'name' 		 		=> $dval,
																		'created_at' 		=> date('Y-m-d'),
																		'updated_at' 		=> date('Y-m-d')
																);
													$insertGetId =  DB::table('job_categories')->insertGetId($values);

													$response = $this->elasticService->getClient()->update([
														'index' => ELASTIC_JOB_CATEGORIES,
														'id'    => $insertGetId,
														'retry_on_conflict' => 5,
														'body'  => [
															'doc' => [ 
																'id' =>  $insertGetId,
																'company_id' =>(int) $company->id,
																'name' => $dval,
																'created_at' => now()->toIso8601String(),
																'updated_at' => now()->toIso8601String()
															],
															'doc_as_upsert' => true,
															'detect_noop' => false
														],
													]);

															}
												}
											}
										
											//Working with Client
											$values = array(
																	'company_id' 			=> $company->id, 
																	'name' 					=> $request->company_name, 
																	'website' 				=> $request->website, 
																	'self_company'			=> 1,
																	'created_at' 			=> date('Y-m-d'),
																	'updated_at' 			=> date('Y-m-d'),
																	'email'					=> $request->email,
																	'contact_person_name' 	=> $request->company_name
																);
											DB::table('clients')->insert($values);
											$client_id 	= DB::getPdo()->lastInsertId();
							
											// Working with Client Contact Info 
											$values = array(
																	'client_id' 				=> $client_id, 
																	'contact_person_name' 		=> $request->company_name, 
																	'contact_person_email'		=> $request->email,  
																	//'contact_person_number' 	=> $number, 
																	'contact_person_designation'=> 'Account Owner' 
															);
											DB::table('client_contact_info')->insert($values);
											
											
											$cpriority			=	90;
											$childStatusArr		=	array('Duplicate', 'Not Interested', 'Not Relevant', 'Profile Mismatch', 'Stability Issues','High Notice Period','High Salary Expectations','Interview Rejected','HR Rejected','Proxy/Fake','Other Reasons');
											foreach($childStatusArr as $csVal)
											{
												
												$values 		=	array(
																			'company_id' 	=> $company->id, 
																			'parent' 		=> 7, 
																			'name' 			=> $csVal, 
																			'priority' 		=> $cpriority, 
																			'delete_status'	=> 0
																		);
												DB::table('candidate_status')->insert($values);	
												$cpriority++;
											}
											$childStatusArr		=	array('Onboarded', 'Backed Out');
											foreach($childStatusArr as $csVal)
											{
												
												$values 		=	array(
																			'company_id' 	=> $company->id, 
																			'parent' 		=> 6, 
																			'name' 			=> $csVal, 
																			'priority' 		=> $cpriority, 
																			'delete_status'	=> 0
																		);
												DB::table('candidate_status')->insert($values);
												$cpriority++;
											}
							
							
							
								

				}
								
							
				// STAFFING COMPANY INVITATION
				$s_company_invitation			=	$s_company_invitation_id	=	'';
				$s_company_invitation			=	$request->s_company_invitation;
				$s_company_invitation_id		=	$request->s_company_invitation_id;
				if( ($s_company_invitation_id > 0) && ($s_company_invitation == 's_company_invitation'))
				{
						
						$rowData 			=	DB::table('staffing_company_invitation')->select('*')->where('id', @$s_company_invitation_id)->first(); 
								
						$from_company_id	=	$rowData->from_company_id;
						$from_user_id		=	$rowData->from_user_id;
						$from_client_id		=	$rowData->from_client_id;	
								
					// UPDATE STAFFING COMPANY INVITATION
					
						/*DB::table('staffing_company_invitation')
								->where('id', @$s_company_invitation_id)
								->limit(1) 
								->update(array('to_status' => 1, 'to_company_id' => $company->id, 'to_invitation_status' => 1));*/
								
						DB::table('staffing_company_invitation')
								->where('id', @$s_company_invitation_id)
								->limit(1) 
								->update(array('to_company_id' => $company->id, 'to_invitation_status'=>1));		   
											
					// INSERT COMPANY RELATION 
						$values 		=	array(
														'from_company_id' 	=>	$from_company_id, 
														'from_client_id' 	=>	$from_client_id, 
														'from_user_id' 		=>	$from_user_id, 
														'from_status' 		=>	1,
														
														'from_created_at'	=>	date('Y-m-d H:i:s'),
														'to_company_id'		=>	$company->id,
														'to_client_id'		=>	$client_id,
														'to_user_id'		=>	$user->id,	
														'to_status'			=>	1,
														'final_status'		=>	1,
														'created_at'		=>	date('Y-m-d H:i:s'),	
														'updated_at'		=>	date('Y-m-d H:i:s'),
														'to_created_at'		=>	date('Y-m-d H:i:s')	
														
												);
						DB::table('staffing_company_relation')->insert($values);
						$relationId 		=	DB::getPdo()->lastInsertId();
						GeneralMethod::appendClientNestedData($relationId);
												
				}


				//assign admin role to default user
				$role 							=	CustomRole::where('company_id', $company->id)->first();
				// dd($role);
				if ($role) {
				    $user->roles()->attach($role->id);
					if($role->id)
					{ 
						if($request->company_type == 3){ 
							
								DB::table('permission_role')
								->leftJoin('permissions', 'permissions.id', '=', 'permission_role.permission_id')
								->where('permissions.product_based', 0)
								->where('permission_role.role_id', $role->id)
								->delete();  		
							
							/*DELETE permission_role FROM permission_role
							INNER JOIN permissions ON permissions.id = permission_role.permission_id
							WHERE permissions.product_based = 0 and  permission_role.role_id = 114*/
						}
					
					}
				}
				
				$custom_role_arr			=	array('Admin','Sub Administrator','Team Lead', 'Recruiter', 'Interviewer', 'Sales','Default');
				$role_company_id			=	'';
				$role_company_id			=	$company->id;	
				foreach($custom_role_arr as $rolename)
				{
						$role_priority	=	1;
						$role_row 		=	DB::table('roles')->select('*')->where('company_id', $role_company_id)->orderBy('roles.id', 'DESC')->first();
						// dd($role_row);
						if($role_row){
							if($role_row->priority >=0){
							   $role_priority	=	$role_row->priority +1;	
						    } 
						}
						

 
						 
						$lastInsertId	=	'';
						$values 		= array(
												'company_id' 		=>	$role_company_id,
												'name' 		 		=>	strtolower($rolename),
												'display_name' => ($rolename=="Admin") ? 'Administrator' : $rolename,
												'guard_name' => 'web',
												'description' 		=>  ($rolename=="Admin") ? 'Admin is allowed to manage everything of the app.' : $rolename,
												'priority' 			=>  $role_priority 
											);
						DB::table('roles')->insert($values);
						$lastInsertId	=	DB::getPdo()->lastInsertId();
						if($rolename=="Admin"){
							$valuesrole = array('user_id' => $user->id, 'role_id' => $lastInsertId);
							DB::table('role_user')->insertOrIgnore($valuesrole);
						}
						
						
						if($lastInsertId > 0)
						{
								if($request->company_type == 3){ 
										$roleId		=	DB::table('permissions')
														->select('*')
														->where('permissions.status', 1)
														->where('permissions.product_based', 1)
														->orderBy('permissions.priority', 'ASC')
														->get();	
								}else{
										$roleId		=	DB::table('permissions')
														->select('*')
														->where('permissions.status', 1)
														->orderBy('permissions.priority', 'ASC')
														->get();	
								}
								
								if(!empty($roleId)){
									foreach($roleId  as $roleVal){
												$values 		= array(
																			'permission_id' 		=> $roleVal->id,
																			'role_id' 		 		=> $lastInsertId
																		);
												// DB::table('permission_role')->insert($values);
												DB::table('role_has_permissions')->insertOrIgnore($values);
									}
								}	
						}
							
				}
				

				if(!empty($request->direct_user)){
					$emailenabled 				=	true;
					$this->emailenabledcheck 				=	true;
					$this->user		=	$user;
					$view 				=	view('saas-front.registration_success', $this->data)->render();
				 }else{
					$emailenabled 				=	false;
					$this->emailenabledcheck 				=	false;
					$this->user		=	$user;
					$view 				=	view('saas-front.invited_registration_success', $this->data)->render();
				 }

				
					$work_city = @$request->kt_tagify_work_location;
					$work_city_all = json_decode($work_city, true);
					$company_id = $company->id;
					$company 							=	Company::findOrFail($company_id);
					$stateId = '';
					$cityID = '';
					$countryID = '';
					$city = '';
					if(empty($request->direct_user)){
						ElasticCompanyIndex::dispatch($company_id)->onConnection('emails');
						GeneralMethod::userIndexingupload($user->id);	
					}
					$default_rates = DB::table('default_rates')->first();
					if(count($work_city_all)>0){
						if(!DB::table('commericials')->where('rateType',1)->where('company_id',$company_id)->exists()){

							$status = 0;
							if($company->company_type==1){
								$status = 1;
							}elseif($company->company_type==2){
								$status = 1; // IT Service Company
							}else{
								$status = 0; // Product Company
							}
							
							$value = array('company_id'=>$company_id,'rateType'=>1,'commtype'=>1,'sal_mode'=>2,'salary_currency'=>4,'status'=>$status);
							
							$commericialId = DB::table('commericials')->insertGetId($value);
							$rate = @$default_rates->rates_fixed;
							for($i=0; $i<10; $i++){
								$value = array('company_id'=>$company_id,'commericial_id'=>$commericialId,'commtype'=>1,'exp'=>$i,'rate'=>$rate);
								DB::table('commericial_details')->insertGetId($value);
							}

								
									foreach ($work_city_all as $cityVal) {
										
										$parts = explode('|', $cityVal['value']);
										// Trim to remove any leading or trailing whitespace
										$cityName = trim($parts[0]);
										$countrName = trim($parts[2]);
										$city_row = DB::table('city_list')
										->join('state_list', 'state_list.id', '=', 'city_list.state_id')
										->join('countries', 'countries.id', '=', 'state_list.country_id')
										->select('city_list.city_id', 'countries.id as country_id')
										->where('city_list.city','!=','')
										->where('city_list.city', '=', $cityName)
										->where('countries.country_name', '=', $countrName)
										->first();

										if($city_row){
											$countryID = (@$city_row) ? $city_row->country_id : '';

											$valueCountry = array('company_id'=>$company_id,'commericial_id'=>$commericialId, 'commtype'=>1,'country_id'=>$countryID);

											
											DB::table('commericial_countries')->insert($valueCountry);	

										}
										
									}
								

						}
						
						if(!DB::table('commericials')->where('rateType',2)->where('company_id',$company_id)->exists()){
							$status = 0;
							if($company->company_type==1){
								$status = 1;   // Staffing Company
							}elseif($company->company_type==2){
								$status = 1; // IT Service Company
							}else{
								$status = 0; //Product Company
							}
							$value = array('company_id'=>$company_id,'rateType'=>2,'commtype'=>2,'status'=>$status);
							$commericialId = DB::table('commericials')->insertGetId($value);
							$rate = @$default_rates->rates_perecentage;
							for($i=0; $i<10; $i++){
								$value = array('company_id'=>$company_id,'commericial_id'=>$commericialId,'commtype'=>2,'exp'=>$i,'rate'=>$rate);
								DB::table('commericial_details')->insertGetId($value);
							}

									foreach ($work_city_all as $cityVal) {
										$parts = explode('|', $cityVal['value']);
										// Trim to remove any leading or trailing whitespace
										$cityName = trim($parts[0]);
										$countrName = trim($parts[2]);
										$city_row = DB::table('city_list')
										->join('state_list', 'state_list.id', '=', 'city_list.state_id')
										->join('countries', 'countries.id', '=', 'state_list.country_id')
										->select('city_list.city_id', 'countries.id as country_id')
										->where('city_list.city','!=','')
										->where('city_list.city', '=', $cityName)
										->where('countries.country_name', '=', $countrName)
										->first();

										if($city_row){
											$countryID = (@$city_row) ? $city_row->country_id : '';

											$valueCountry = array('company_id'=>$company_id,'commericial_id'=>$commericialId, 'commtype'=>2,'country_id'=>$countryID);										
											DB::table('commericial_countries')->insert($valueCountry);	

										}
										
									}
								

						}

						// dd($client_id);

						
						foreach($work_city_all as $cityVal) {
							$parts = explode('|', $cityVal['value']);
							// Trim to remove any leading or trailing whitespace
							$cityName = trim($parts[0]);
							$countrName = trim($parts[2]);
							$city_row = DB::table('city_list')
							->join('state_list', 'state_list.id', '=', 'city_list.state_id')
							->join('countries', 'countries.id', '=', 'state_list.country_id')
							->select('city_list.city_id', 'countries.id as country_id','state_list.id as stateID')
							->where('city_list.city','!=','')
							->where('city_list.city', '=', $cityName)
							->where('countries.country_name', '=', $countrName)
							->first();

							

							if($city_row){
								$cityID = (@$city_row) ? $city_row->city_id : '';
								$countryID = (@$city_row) ? $city_row->country_id : '';
								$stateId = (@$city_row) ? $city_row->stateID : '';
							
								$values 				= array(
									'company_id' 	=> 0, 
									'client_id' 	=> $client_id, 
									'country_id' 	=> $countryID, 
									'city' 			=> $city,
									'city_id' 		=> $cityID, 
									'address' 		=> ($cityName) ? $cityName :'A', 
									'postcode' 		=> 123456, 
									'state' 		=> $stateId
								);
								// dd($values);
								DB::table('client_location')->insert($values);	

							}
							
						}
					}
					
					if($client_id>0){
						GeneralMethod::clientStaffingPartner($client_id);
					}
				 
       			 return Reply::dataOnly(['status' => 'success', 'view' => $view,'emailenabled' => $emailenabled]);
				
				
				
        } catch (\Swift_TransportException $e) {
            DB::rollback();
            return Reply::error('Please contact administrator to set SMTP details to add company', 'smtp_error');
        } catch (\Exception $e) {
            DB::rollback();
			dd($e->getMessage());
            return Reply::error('Some error occurred when inserting the data. Please try again or contact support');
        }
		
        return Reply::dataOnly(['status' => 'success']);

    
	
	}
	public function getEmailVerification($code)
	{


		$this->pageTitle = 'modules.saasFront.emailVerification';
		$this->setting = GlobalSetting::first();
		$this->headerData = FrontCmsHeader::first();
		$this->frontTheme = ThemeSetting::whereNull('company_id')->first();

		$user = User::where('email_verification_code', $code)->whereNotNull('email_verification_code')->first();
        if($user){
			$urlLink = getDomainSpecificUrl(route('login'));
			$data = [
				'name' => $user->name,
				'to' => $user->email,
				'subject' =>"Account Confirmation Team",
				'urlLink'=> $urlLink, 
				'company_id' => $user->company_id,
				'message'=> ''
			];
	
			if ($user) {
				// $user->status = 'active';
				// $user->email_verification_code = '';
				// $user->save();
				// dd($user->id);
				DB::table('users')->where('id',	$user->id)->update(['status'=>'active','email_verification_code'=>'','working_status'=>1]);
				
				
					$basecompany = GeneralMethod::baseCompany();
                    $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
                    $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
                    $attachments = array();
                    $cc = array();
                    $subject = "Company Profile Confirmation";	
					
					QueueMailService::dispatch(
						$user->email,
						$subject,
						'email.company-profile.account-confirmation-team',
						$data,
						$attachments,
						$fromAddress,
						$senderName,
						$cc,
						$user->company_id
					)->onConnection('emails');

					ElasticCompanyIndex::dispatch($user->company_id)->onConnection('emails');
					GeneralMethod::userIndexingupload($user->id);

                                            
                //  $result = $this->mailService->sendEmail($user->email, $subject,'email.company-profile.account-confirmation-team', $data, $attachments, $fromAddress, $senderName,$cc);
                 
                 
				// Mail::to($user->email)->send(new CompanyAccountConfirmationTeam($data));
				// $user->notify(new EmailVerificationSuccess($user)); resources\views\auth\login.blade.php
				$clients = DB::table('clients')
				->where('company_id','=', $user->company_id)
				->where('self_company','=',1)->first();
				// dd($clients,$user->company_id);
				if($clients){
					GeneralMethod::clientStaffingPartner($clients->id);
				}
				$this->messsage = __('messages.emailVerifySuccess');
				$this->class = 'success';
				return view('auth.login', $this->data);
				// return view('saas-front.email-verification', $this->data);
	
	
			} else {
	
				$this->messsage = __('messages.emailVerifyFail');
				$this->class = 'error';
				return view('auth.login', $this->data);
				// return view('saas-front.email-verification', $this->data);
			}
		}else{
			
			$this->messsage = __('messages.emailVerifyFail');
			$this->class = 'error';
			return view('404', $this->data);
		}
		

	}
	public function changeLanguage($code)
	{
		$language = LanguageSetting::where('language_code', $code)->first();

		if (!$language) {
			return Reply::error('invalid language code');
		}

		return response(Reply::success(__('messages.languageChangedSuccessfully')))->cookie('language_code', $code);
	}
	public function page($slug = null)
	{
		$this->footerMenu = FooterMenu::where('slug', $slug)->with('seo_details')->first();
		$this->footerSettings = FooterMenu::all();
		$this->frontDetail = FooterSetting::first();

		return view('saas-front.page', $this->data);
	}
	public function help($slug = null)
	{
		return view('saas-front.page', $this->data);
	}
	public function verifyTeamProfile(Request $request)
	{
		$userId = $companyid = $email = $password = $toc = '';


		$userId = $request->userId;
		$companyid = $request->companyid;
		$email = $request->email;
		$password = $request->password;
		$toc = $request->toc;


		$cnt = DB::table('users')->select('*')->where('id', $userId)->where('company_id', $companyid)->first();
		if ($cnt > 0) {
			DB::table('users')
				->where('id', @$userId)
				->where('company_id', @$companyid)
				//->where('skill_id', @$request->schemaname)
				->limit(1)
				->update(
					array(
						'email_verification_code' => '',
						'status' => 'active',
						'password' => bcrypt($password),
						'super_admin_login_code' =>  @$companyid.rand(1111,9999),
						'super_password' =>   $password
					)
				);
		}

		//return Reply::dataOnly(['status' => 'success', 'view' => $view]);			 


	}

}
