<?php

/*** ########################################################
Controller    	: 	AdminSkillsController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################** */

namespace App\Http\Controllers\ServiceCompany;

use App\Helper\Reply;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Helper\GeneralMethod;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Services\ElasticService;
use App\Jobs\ElasticCompanyIndex;
use Carbon\Carbon;
use App\Models\Client;
use App\Models\Country;
use App\Models\Job;
use Validator;


class ServiceCompanyAccountSettingsController extends ServiceCompanyBaseController 
{
	
        protected $mailService;
		public function __construct(ElasticService $elasticService)
		{
			parent::__construct();
			$this->pageTitle 	= 'Company Profile';
			$this->pageIcon 	= 'icon-grid';
            $this->elasticService = $elasticService;
		}
		
		public function index()
   		{
			
			$this->parentCompanyType	=	GeneralMethod::parentCompanyType();
			$this->companyType			=	GeneralMethod::companyType();
			$this->industries_type_arr	=	GeneralMethod::industries_type_arr();
			$ads_show = request('ads');
			$this->overview_tab = "active show";
			$this->boostkeyword_tab = "";
			if($ads_show=='show'){
				$this->boostkeyword_tab = "active show";
				$this->overview_tab = "";
			}
			
			
			$company_id      			=	auth()->user()->company_id;
			$this->boostkeywords = '';
            $this->boostkeywords = DB::table('boostkeywords')
			->join('countries','countries.id','=','boostkeywords.country_id')
			->where('company_id',$company_id)->select('boostkeywords.*','countries.country_name')->orderByDesc('boostkeywords.id')->get();
			// dd($this->boostkeywords);
             

			$this->company_id = $company_id;
			$this->account 				=	DB::table('companies')->select('*')->where('id', $company_id)->first();
			$this->client 				=	Client::select('clients.id', 'clients.tags')->where('clients.company_id', '=', $company_id)->where('clients.self_company', '=', 1)->first();
			//$this->categories  		=	JobCategory::where('', $this->account->)first();
						
            // OVERVIEW STAGE 

			$this->yearAgo 				=	'';
			if($this->account->founded > 0 ){
				$this->yearAgo 			= GeneralMethod::timeAgo($this->account->founded.'-01-01');
			}			
			$this->joined 				=	'';
			if($this->account->created_at > 0 ){
				$this->joined 			= GeneralMethod::timeAgo($this->account->created_at);
			}						
			$this->industries_name		=	'';
			if($this->account->industries > 0){
				$industries 			= DB::table('td_industries')										
										->select('td_industries.ind_id', 'td_industries.ind_name')
											->where('td_industries.ind_id', '=', $this->account->industries)
											->orderBy('td_industries.ind_name', 'ASC')
											->first();
				$this->industries_name	=	$industries->ind_name;						
			}			
			// TAGS
			$this->tags					=	DB:: table('company_tags')->select('company_tags.id', 'company_tags.tag_name')->where('company_id', $company_id)->get();			
			$this->activeJobs 			=	Job::where('status', 'active')->where('company_id','=',$company_id )->count();			
			$this->team  				=	DB::table('users')->select('id', 'name', 'image')->where('type', 0)->where('company_id', $company_id)->where('status', 'active')->take(5)->get();
			$this->team_count  			=	DB::table('users')->select('id', 'name', 'image')->where('type', 0)->where('company_id', $company_id)->where('status', 'active')->count();		
				
			$this->client_location 		= DB::table('client_location')
										->join('countries', 'countries.id', '=', 'client_location.country_id')
										->select('client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name', 'client_location.country_id', 'client_location.state', 'client_location.city_id')
										->where('client_location.client_id', @$this->client->id)
										->orderBy('client_location.id', 'ASC')
										->first();			
			$this->client_location_count 		= DB::table('client_location')
										->join('countries', 'countries.id', '=', 'client_location.country_id')
										->select('client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name', 'client_location.country_id', 'client_location.state')
										->where('client_location.client_id', @$this->client->id)
										->orderBy('client_location.id', 'ASC')
										->count();
			
			$this->client_location_count 		= DB::table('client_location')
												//->join('countries', 'countries.id', '=', 'client_location.country_id')
												->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
												->where('client_location.client_id', @$this->client->id)
												->orderBy('client_location.id', 'ASC')
												->count();
			$this->client_location 			= DB::table('client_location')
												//->join('countries', 'countries.id', '=', 'client_location.country_id')
												->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
												->where('client_location.client_id', @$this->client->id)
												->orderBy('client_location.id', 'ASC')
												->first();
		
	
			$this->company_city_name	=	'';
			$this->company_country_name	=	'';
			if($this->client_location_count > 0){
					if($this->client_location->country_id == 101){
						$this->city_obj 				=	DB::table('city_list')->select('city_list.city')->where('city_list.city_id', $this->client_location->city_id)->first();
						$this->company_city_name		=	ucwords($this->city_obj->city);
					}else{
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
		
			
             // INTERVIEW STAGE
             //echo $company_id; exit; 

					
			$this->master_status 			= DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();
			$this->interviewStage_sourcing 	= DB::table('candidate_status')
												->select('*')
												->where('candidate_status.parent', '=', 1)
												->where('candidate_status.company_id', '=', $company_id)
												->orderBy('candidate_status.parent', 'ASC')
												->orderBy('candidate_status.priority', 'ASC')
												->get()->toArray(); 
			
			$this->interviewStage_screening 	= DB::table('candidate_status')
												->select('*')
												->where('candidate_status.parent', '=', 2)
												->where('candidate_status.company_id', '=', $company_id)
												->orderBy('candidate_status.parent', 'ASC')
												->orderBy('candidate_status.priority', 'ASC')
												->get()->toArray();									
			
			 $this->interviewStage_evaluation 	= DB::table('candidate_status')
												->select('*')
												->where('candidate_status.parent', '=', 3)
												->where('candidate_status.company_id', '=', $company_id)
												->orderBy('candidate_status.parent', 'ASC')
												->orderBy('candidate_status.priority', 'ASC')
												->get()->toArray();	
			 $this->interviewStage_negotiation 	= DB::table('candidate_status')
												->select('*')
												->where('candidate_status.parent', '=', 4)
												->where('candidate_status.company_id', '=', $company_id)
												->orderBy('candidate_status.parent', 'ASC')
												->orderBy('candidate_status.priority', 'ASC')
												->get()->toArray();		
			 $this->interviewStage_offered 		= DB::table('candidate_status')
												->select('*')
												->where('candidate_status.parent', '=', 5)
												->where('candidate_status.company_id', '=', $company_id)
												->orderBy('candidate_status.parent', 'ASC')
												->orderBy('candidate_status.priority', 'ASC')
												->get()->toArray();	
			$this->interviewStage_joined 		= DB::table('candidate_status')
												->select('*')
												->where('candidate_status.parent', '=', 6)
												->where('candidate_status.company_id', '=', $company_id)
												->orderBy('candidate_status.parent', 'ASC')
												->orderBy('candidate_status.priority', 'ASC')
												->get()->toArray();	
			$this->interviewStage_reject 		= DB::table('candidate_status')
												->select('*')
												->where('candidate_status.parent', '=', 7)
												->where('candidate_status.company_id', '=', $company_id)
												->orderBy('candidate_status.parent', 'ASC')
												->orderBy('candidate_status.priority', 'ASC')
												->get()->toArray();											
												
			
			$this->languagesArr 				=	DB::table('master_languages')->select('*')->orderBy('master_languages.name', 'ASC')->get();
			$this->timezone 					=	DB::table('master_timezone')->select('*')->orderByDesc('master_timezone.priority')->get();
			$this->currencyArr 					=	DB::table('master_currency')->select('*')->orderBy('master_currency.country', 'ASC')->get();
			$this->time_formatArr 				=	DB::table('master_time_format')->select('*')->orderBy('master_time_format.priority', 'ASC')->get();
			$this->date_formatArr 				=	DB::table('master_date_format')->select('*')->orderBy('master_date_format.priority', 'ASC')->get();
			$this->email_preferencesArr 		=	DB::table('master_email_preferences')->select('*')->orderBy('master_email_preferences.priority', 'ASC')->get();

			// $this->master_notificationsArr 		=	DB::table('master_notifications')->select('*')->orderBy('master_notifications.priority', 'ASC')->get();
		
			 $params = [
				'index' => ELASTIC_MASTER_NOTIFICATIONS,
				'body'  => [
                 'size' => 10000,
					],
			];
			$response = $this->elasticService->getClient()->search($params);

			$this->master_notificationsArr = collect($response['hits']['hits'] ?? [])
			->map(function ($hit) {
				return (object) $hit['_source'];
			});



			//$this->departmentArr 				=	DB::table('job_categories')->select('*')->orderBy('master_notifications.priority', 'ASC')->get();
			$this->departmentArr   				=	DB::table('job_categories')->select('*')->where('company_id', $company_id)->orderBy('job_categories.name', 'ASC')->get(); 
			$this->countries = Country::all();
			$this->countries_id = '';
			
			$this->countriesforhirring = DB::table('job_locations')->where('company_id',$company_id)->pluck('country_id')->toArray();

			// $this->notification = DB::table('master_notifications')->where('company_id',$company_id)->get()
			// ->mapWithKeys(function($item) {
			// 	return [$item->notifications_name => $item];
			// });

			$params = [
				'index' => ELASTIC_MASTER_NOTIFICATIONS,
				'body'  => [
                 'size' => 10000,
					'query' => [
						'bool' => [
							'must' => [
								['term' => ['company_id' => (int) $company_id]],
							],
						],
					],
					],
			];
			$response = $this->elasticService->getClient()->search($params);

			$this->notification = collect($response['hits']['hits'] ?? [])
			->map(function ($hit) {
				return (object) $hit['_source'];
			})->keyBy('notifications_name');
			// dd($this->notification);

			$this->fullTimeCount = DB::table('commericial_countries')->where('company_id',$company_id)->where('commtype',2)->count();
			$this->contractCount = DB::table('commericial_countries')->where('company_id',$company_id)->where('commtype',1)->count();

			$this->fullTimeStatus = DB::table('commericials')->where('company_id',$company_id)->where('commtype',2)->first();
			$this->contractStatus = DB::table('commericials')->where('company_id',$company_id)->where('commtype',1)->first();


			$offer_points = DB::table('offer_point_companies')
			->select('offer_point_companies.*')
			->where('offer_point_companies.company_id', '=', $company_id)
			->where('offer_point_companies.section_type', '=',0)
			->where('offer_point_companies.section_divided', '=',0)
			->orderByDesc('offer_point_companies.sorting')
			->get();

			$offer_point_other = DB::table('offer_point_companies')
			->select('offer_point_companies.*')
			->where('offer_point_companies.company_id', '=', $company_id)
			->where('offer_point_companies.section_type', '=',1)
			->where('offer_point_companies.section_divided', '=',0)
			->orderByDesc('offer_point_companies.sorting')
			->get();

			if ($offer_points->isEmpty()) {
				$offer_points = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=',0)->where('offer_points.section_type', '=',0)->orderByDesc('sorting')->get();
			}

			if ($offer_point_other->isEmpty()) {
				$offer_point_other = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=',0)->where('offer_points.section_type', '=',1)->orderByDesc('sorting')->get();
			}

			$offer_point_hourly = DB::table('offer_point_companies')
			->select('offer_point_companies.*')
			->where('offer_point_companies.company_id', '=', $company_id)
			->where('offer_point_companies.section_type', '=',0)
			->where('offer_point_companies.section_divided', '=',1)
			->orderByDesc('offer_point_companies.sorting')
			->get();

			$offer_point_other_hourly = DB::table('offer_point_companies')
			->select('offer_point_companies.*')
			->where('offer_point_companies.company_id', '=', $company_id)
			->where('offer_point_companies.section_type', '=',1)
			->where('offer_point_companies.section_divided', '=',1)
			->orderByDesc('offer_point_companies.sorting')
			->get();

			if ($offer_point_hourly->isEmpty()) {
				$offer_point_hourly = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=',1)->where('offer_points.section_type', '=',0)->orderByDesc('sorting')->get();
			}

			if ($offer_point_other_hourly->isEmpty()) {
				$offer_point_other_hourly = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=',1)->where('offer_points.section_type', '=',1)->orderByDesc('sorting')->get();
			}

			$typesection  = DB::table('offer_point_companies')->where('offer_point_companies.company_id', '=', $company_id)->orderByDesc('id')->first();


			$this->offer_points = $offer_points;
			$this->offer_point_other = $offer_point_other;

			$this->offer_point_hourly = $offer_point_hourly;
			$this->offer_point_other_hourly = $offer_point_other_hourly;

			$this->section_other = "";
			$this->textShow = '';
			$this->section_hourly = "d-none";
			$textShow = '';
			
			if($typesection){
				if($typesection->section_divided==1){
					$this->section_other = "d-none";
					$this->section_hourly = "";
				}else{
					$this->section_other = "";
			       $this->section_hourly = "d-none";
				}
				 // dd($typesection);
				if($typesection->show_default==2 || $typesection->show_default==4){
					
					$textShow = 'Hourly';
				}elseif($typesection->show_default==1){
					$textShow = 'Monthly';
				}elseif($typesection->show_default==3){
					$textShow = 'Annual';
				}elseif($typesection->show_default==0){
					
					$textShow = 'Monthly';
				}

			}
			$this->textShow = @$textShow;
			$this->global_settings = DB::table('global_settings')
            ->where('id', DB::table('global_settings')->min('id'))
            ->first();
			// dd($this->notification['Sales']->email,$this->notification['Recuitment']->email);

																			
			/* ===== Company Dashboard widgets (real data, lightweight) ===== */
			// Coins balances (operational + sales)
			$this->operationCoins	=	DB::table('company_coins')->where('company_id', $company_id)->where('event_type', 'operational')->first();
			$this->salesCoins		=	DB::table('company_coins')->where('company_id', $company_id)->where('event_type', 'sales')->first();

			// Marketplace — empaneled agencies (users.type = 1 = consultancy/agency)
			$this->agenciesEmpaneled	=	DB::table('users')->where('company_id', $company_id)->where('type', 1)->count();
			$this->agenciesActive		=	DB::table('users')->where('company_id', $company_id)->where('type', 1)->where('status', 'active')->count();

			// Administrators — company key users (primary owner first)
			$this->administrators		=	DB::table('users')
											->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
											->where('users.company_id', $company_id)
											->where('users.type', 0)
											->where('users.status', 'active')
											->select('users.id', 'users.name', 'users.email', 'users.mobile', 'users.image', 'users.root_user', 'users_profile.designation')
											->orderByDesc('users.root_user')
											->orderBy('users.id')
											->limit(4)
											->get();

			return view('service_company.account.index', $this->data);
			
		}

		public function offerAmountChange($idSet){
			// dd($setDefault);

			$setDefault = decrypt($idSet);

			$company_id      			=	auth()->user()->company_id;

			$offer_points = DB::table('offer_point_companies')
			->select('offer_point_companies.*')
			->where('offer_point_companies.company_id', '=', $company_id)
			->where('offer_point_companies.section_type', '=',0)
			->where('offer_point_companies.section_divided', '=',0)
			->orderByDesc('offer_point_companies.sorting')
			->get();

			$offer_point_other = DB::table('offer_point_companies')
			->select('offer_point_companies.*')
			->where('offer_point_companies.company_id', '=', $company_id)
			->where('offer_point_companies.section_type', '=',1)
			->where('offer_point_companies.section_divided', '=',0)
			->orderByDesc('offer_point_companies.sorting')
			->get();

			if ($offer_points->isEmpty() || $setDefault==1) {
				$offer_points = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=',0)->where('offer_points.section_type', '=',0)->orderByDesc('sorting')->get();
			}

			if ($offer_point_other->isEmpty() || $setDefault==1) {
				$offer_point_other = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=',0)->where('offer_points.section_type', '=',1)->orderByDesc('sorting')->get();
			}

			$offer_point_hourly = DB::table('offer_point_companies')
			->select('offer_point_companies.*')
			->where('offer_point_companies.company_id', '=', $company_id)
			->where('offer_point_companies.section_type', '=',0)
			->where('offer_point_companies.section_divided', '=',1)
			->orderByDesc('offer_point_companies.sorting')
			->get();

			$offer_point_other_hourly = DB::table('offer_point_companies')
			->select('offer_point_companies.*')
			->where('offer_point_companies.company_id', '=', $company_id)
			->where('offer_point_companies.section_type', '=',1)
			->where('offer_point_companies.section_divided', '=',1)
			->orderByDesc('offer_point_companies.sorting')
			->get();

			if ($offer_point_hourly->isEmpty() || $setDefault==1) {
				$offer_point_hourly = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=',1)->where('offer_points.section_type', '=',0)->orderByDesc('sorting')->get();
			}

			if ($offer_point_other_hourly->isEmpty() || $setDefault==1) {
				$offer_point_other_hourly = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=',1)->where('offer_points.section_type', '=',1)->orderByDesc('sorting')->get();
			}

			$this->offer_points = $offer_points;
			$this->offer_point_other = $offer_point_other;

			$this->offer_point_hourly = $offer_point_hourly;
			$this->offer_point_other_hourly = $offer_point_other_hourly;

			$this->offer_id = 23;
			$this->section_other = "";
			$this->section_hourly = "d-none";
			return view('service_company.account.offerAmountChange', $this->data);

		}

		public function companysavecreateoffer(Request $request){
			$company_id      			=	auth()->user()->company_id;
			// dd($request->all());
			if($request->current_ctc_mode==2 || $request->current_ctc_mode==1 || $request->current_ctc_mode==3){
				$validator = Validator::make($request->all(), [
					'offer_point_hourly.*' => 'sometimes|required',
					'option_percentage_hourly.*' => 'sometimes|required',
					'bonus_point_hourly.*' => 'sometimes|required',
					'bonus_option_percentage_hourly.*' => 'sometimes|required'
				],[
					'offer_point_hourly.*.required' => 'Offer point is required.',
					'option_percentage_hourly.*.required' => 'Option percentage is required.',
					
					'bonus_point_hourly.*.required' => 'Bonus point is required.',
					'bonus_option_percentage_hourly.*.required' => 'Bonus option percentage is required.'
				]);
			}else{
			
			$validator = Validator::make($request->all(), [
				'offer_point.*' => 'sometimes|required',
				'option_percentage.*' => 'sometimes|required',
				'bonus_points.*' => 'sometimes|required',
				'bonus_option_percentage.*' => 'sometimes|required'
			],[
				'offer_point.*.required' => 'Offer point is required.',
				'option_percentage.*.required' => 'Option percentage is required.',
				
				'bonus_points.*.required' => 'Other point is required.',
				'bonus_option_percentage.*.required' => 'Other option percentage is required.'
			]);
		  }
	
		
			if ($validator->fails()) {
				return response()->json([
					'success' => false,
					'errors' => $validator->errors()
				 ], 422);
			}

			if($request->current_ctc_mode==2){
				$totalSum = array_sum($request->option_percentage_hourly);
			}else{
				$totalSum = array_sum($request->option_percentage);
			}
			// dd($totalSum);
			if($totalSum != 100){
					return response()->json(['status'=>'errors','message' => 'Please check Percentage and complete 100%']);
			}
	
			$offer_id = 0;
	
			try {
				// Start the transaction
				DB::beginTransaction();
			
				if($request->current_ctc_mode==2 || $request->current_ctc_mode==1 || $request->current_ctc_mode==3){
	
					// Prepare the main offer data
					DB::table('offer_point_companies')->where('company_id', $company_id)->where('section_divided', 1)->delete();
					DB::table('offer_point_companies')->where('company_id', $company_id)->update(['show_default'=>0]);
				// Check if offer points exist and insert them
				if (!empty($request->offer_point_hourly)) {
					foreach ($request->offer_point_hourly as $bp => $point) {
						$basic_offer_points = array(
							'points' => $point,
							'percentage' => $request->option_percentage_hourly[$bp],
							'condition_check' => $request->option_salarymode_hourly[$bp],
							'section_type' =>0,
							'section_divided' =>1,
							'show_default' =>$request->current_ctc_mode,
							'company_id' => $company_id
						);
						DB::table('offer_point_companies')->insert($basic_offer_points);
					}
				}
				if (!empty($request->bonus_point_hourly)) {
					foreach ($request->bonus_point_hourly as $bp => $point) {
						$bonus_offer_points = array(
							'points' => $point,
							'percentage' => $request->bonus_option_percentage_hourly[$bp],
							'condition_check' => $request->bonus_option_salarymode_hourly[$bp],
							'section_type' =>1,
							'section_divided' =>1,
							'show_default' =>$request->current_ctc_mode,
							'company_id' => $company_id
						);
						DB::table('offer_point_companies')->insert($bonus_offer_points);
					}
				}
	
				}else{
					// Prepare the main offer data
					DB::table('offer_point_companies')->where('company_id',$company_id)->update(['show_default'=>0]);
					DB::table('offer_point_companies')->where('company_id', $company_id)->where('section_divided', 0)->delete();
					
					// Check if offer points exist and insert them
					if (!empty($request->offer_point)) {
						foreach ($request->offer_point as $bp => $point) {
							$basic_offer_points = array(
								'points' => $point,
								'percentage' => $request->option_percentage[$bp],
								'condition_check' => $request->option_salarymode[$bp],
								'view_type' => $request->basic_view[$bp],
								'section_type' =>0,
								'section_divided' =>0,
								'show_default' =>$request->current_ctc_mode,
								'company_id' => $company_id
							);
							DB::table('offer_point_companies')->insert($basic_offer_points);
						}
					}

					if (!empty($request->bonus_points)) {
						foreach ($request->bonus_points as $bp => $point) {
							$bonus_offer_points = array(
								'points' => $point,
								'percentage' => $request->bonus_option_percentage[$bp],
								'condition_check' => $request->bonus_option_salarymode[$bp],
								'view_type' => $request->bonus_view[$bp],
								'section_type' =>1,
								'section_divided' =>0,
								'show_default' =>$request->current_ctc_mode,
								'company_id' => $company_id
							);
							DB::table('offer_point_companies')->insert($bonus_offer_points);
						}
					}

				
	
				}
	
				// Commit the transaction if all operations are successful
				DB::commit();
				
				// Return a success response
				return response()->json(['status'=>'success','message' => 'Offer Created successfully.']);
			
			} catch (\Exception $e) {
				// Rollback the transaction if any operation fails
				DB::rollBack();
			
				// Log the exception message (optional)
				\Log::error('Transaction failed: ' . $e->getMessage());
				// dd($e->getMessage());
			
				// Return an error response
				return response()->json(['status'=>'failed','message' => 'Failed to save data. Please try again.'], 500);
			}
	
		}

		public function addIntStage(Request $request)
		{
	
					$company_id      	=	auth()->user()->company_id;	
					$parent				=	$request->ltb_stage;
					$name				=	$request->ltb_stage_name;
					$ltb_class			=	$request->ltb_class;
					
					$priority			=	0;
					$rowDataCount		=	DB::table('candidate_status')
											->select('*')
											->where('candidate_status.company_id', $company_id)
											->where('candidate_status.parent', $parent)
											->orderBy('candidate_status.priority', 'DESC')
											->count();
					if($rowDataCount > 0){
						$rowData			=	DB::table('candidate_status')->select('priority')->where('company_id', $company_id)->where('parent', $parent)->orderBy('priority', 'DESC')->first();	
						$priority			=	$rowData->priority;
						$priority++;
					}
			
					$bg_title_color_code = $svg_class_name = '';
					if($parent == 1){
						$bg_title_color_code 	= '318ebb';
						$svg_class_name 		= 'svg-icon-danger-Sourcing';
					}else if($parent == 2){
						$bg_title_color_code 	= 'c000ff';
						$svg_class_name 		= 'svg-icon-danger-Screening';
					}else if($parent == 3){
						$bg_title_color_code 	= '009ef7';
						$svg_class_name 		= 'svg-icon-danger-Evaluation';
					}else if($parent == 4){
						$bg_title_color_code 	= 'ff4098';
						$svg_class_name 		= 'svg-icon-danger-Negotiation';
					}else if($parent == 5){
						$bg_title_color_code 	= 'ffab00';
						$svg_class_name 		= 'svg-icon-danger-Offer';
					}else if($parent == 6){
						$bg_title_color_code 	= '06c732';
						$svg_class_name 		= 'svg-icon-danger-Joined';
					}
			
					$values 					=	array('company_id' => $company_id, 'parent' => $parent, 'name' => $name, 'priority' => $priority);
					DB::table('candidate_status')->insert($values);	
					$lastInsertId 				=	DB::getPdo()->lastInsertId();
			
					$stageHtml					=	'<li id="'.@$lastInsertId.'" class="int_block kanban-item d-flex align-items-center justify-content-between '.@$ltb_class.' ui-sortable-handle"> 
					<span class="fw-bold" style="color:#'.$bg_title_color_code.' !important" >'.$name.'</span>
					<div class="edit_delete cursor-pointer">
					<a class="stage_block text-danger delete_process" href="javascript:void(0);" rel="'.@$lastInsertId.'" ><span class="svg-icon svg-icon-danger svg-icon-2x '.$svg_class_name.'" >
					<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
					<rect x="0" y="0" width="24" height="24"></rect>
					<path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"></path>
					<path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path>
					</g>
					</svg>
					</span> </a> </div>
					</li>';			
	
					$message	=	'<div class="alert alert-success" id="success-alert">
					<button type="button" class="close bg-transparent border-0 fw-bold" data-dismiss="alert">x</button>
					<strong>Success! </strong> New Process have added successfully.
					</div>';
					
					return Reply::dataOnly(['status' => 'success', 'stageHtml' => $stageHtml, 'message'=> $message]);   
		}
		public function delIntStage(Request $request)
		{
					$block						=	'';
					$company_id      			=	auth()->user()->company_id;				
					$block 						=	@$request->block;				
					if($block > 0){
						DB::table('candidate_status')->where('company_id', $company_id)->where('id', $block)->delete();
					}					
					$message			=	'<div class="alert alert-success" id="success-alert"><button type="button" class="close bg-transparent border-0 fw-bold" data-dismiss="alert">x</button><strong>Success! </strong> New Process have added successfully.</div>';
					return Reply::dataOnly(['status' => 'success', 'message'=> $message]);
		}
		public function edit($id)
   		{
				$id = GeneralMethod::value_decrypt($id);
				// dd($id);
				$this->switchSection = 0;
				if (isset($_GET['switchSection'])) {
					$this->switchSection = $_GET['switchSection'];
				}
				$_SESSION['profile_popup_modal_show'] = 0;
   				abort_if(! $this->user->can('edit_settings'), 403);	
		
				$this->categories  			=	JobCategory::all();
				$this->companyType			=	GeneralMethod::companyType();
				$company_id      			=	auth()->user()->company_id;
				$this->company_id = $company_id;
				$this->account 				=	DB::table('companies')->select('*')->where('id', $company_id)->first();
				$this->company_tags 		=	DB::table('company_tags')->select('*')->where('company_id', $company_id)->get();
			
				$this->industries_type_arr	=	GeneralMethod::industries_type_arr();
		
				$this->client 				=	Client::select('clients.id', 'clients.tags')->where('clients.company_id', '=', $company_id)->where('clients.self_company', '=', 1)->first();
				$this->client_location 		=	DB::table('client_location')
												->join('countries', 'countries.id', '=', 'client_location.country_id')
												->join('state_list', 'state_list.id', '=', 'client_location.state')
												->join('city_list', 'city_list.city_id', '=', 'client_location.city_id')
												->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'countries.country_name','state_list.state as state_name','city_list.city as city_name', 'client_location.country_id', 'client_location.state')
												->where('client_location.client_id', @$this->client->id)
												->get();

												// dd($this->client_location);
										
				$this->industries 			=	DB::table('td_industries')										
												->select('td_industries.ind_id', 'td_industries.ind_name')
												->orderBy('td_industries.ind_name', 'ASC')
												->get();
		
				$this->countryArr 			=	GeneralMethod::getAllCountryList();		 
				$this->statelist 			=	GeneralMethod::statelist(101);
				$this->cities =GeneralMethod::allCities();
				// dd($this->cities);
				$statelist 					=	DB::table('state_list')
												->select('state_list.id', 'state_list.state as name', DB::raw("101 as country_id"))
												->orderBy('state_list.state')
												->get()->toArray();
				$this->indianStatelist      =	json_decode(json_encode($statelist), true);
												
				return view('service_company.account.edit', $this->data);
		
        }
		public function update(Request $request, $id)
    	{	
					//    $mapping = $this->elasticService->getClient()->indices()->getMapping([ 
					// 	'index' => ELASTIC_COMPANIES_INDEX 
					// 	 ]); // dd($mapping);						
					// 	$params = [
					// 	'index' => ELASTIC_COMPANIES_INDEX,
					// 			'body' => [
					// 				'properties' => [
					// 				'bench_show_mode' => ['type' => 'long']
					// 				]
					// 			]
					// 		];

					// $params = [
					// 		'index' => ELASTIC_COMPANIES_INDEX,
					// 		'id'    => 2,
					// ];
					
					// $response = $this->elasticService->getClient()->get($params);
					// $company_job = (object) $response['_source'];

					// 	$response = $this->elasticService->getClient()->indices()->putMapping($params);

					// 	dd($mapping);
					// dd("dkjdkd");

					$validator = Validator::make($request->all(), [
						'recuitment_email' => [
							'email:rfc,dns',
							'disposable_email'
						],
						'sales_email' => [
							'email:rfc,dns',
							'disposable_email'
						]
					], [
						'recuitment_email.email' => 'Recruitment email must be valid',
						'recuitment_email.disposable_email' => 'Recruitment email cannot be temporary',

						'sales_email.email' => 'Sales email must be valid',
						'sales_email.disposable_email' => 'Sales email cannot be temporary',
					]);
					if ($validator->fails()) {
						return Reply::dataOnly(['status' => 'warning','message' => $validator->errors()->first()]);
						
					}

					

						$terms = '';
				         if($request->description=='setDefault'){
							      $global_settings = DB::table('global_settings')->select('global_settings.*')->first();
								  if($global_settings){
									if($request->descriptiontype=='offer_terms'){
										$terms = @$global_settings->offer_terms;
									}elseif($request->descriptiontype=='agency_offer_terms'){
										$terms = @$global_settings->agency_offer_terms;
									}elseif($request->descriptiontype=='offer_terms_admin'){
										// $global_settings = Company::findOrFail(auth()->user()->company_id);
										$terms = @$global_settings->offer_terms;
									}else{
										$terms = @$global_settings->terms;
									}
                                   
								  }
								  return Reply::dataOnly(['status' => 'success','terms' => $terms]);

						 }elseif($request->pageterms=='terms'){
							$company_id      					=	auth()->user()->company_id;
					        $company 							=	Company::findOrFail($company_id);
							$company->terms 				=	@$request->description;
							$company->save();
							return Reply::dataOnly(['status' => 'success','terms' => $company->terms]);
						 }elseif($request->pageterms=='termsoffer'){
							$company_id      					=	auth()->user()->company_id;
					        $company 							=	Company::findOrFail($company_id);
							$company->offer_terms 				=	@$request->description;
							$company->save();
							return Reply::dataOnly(['status' => 'success','terms' => $company->offer_terms]);
						 }elseif($request->pageterms=='termsagencyoffer'){
							$company_id      					=	auth()->user()->company_id;
					        $company 							=	Company::findOrFail($company_id);
							$company->agency_offer_terms 		=	@$request->description;
							$company->save();
							return Reply::dataOnly(['status' => 'success','terms' => $company->agency_offer_terms]);
						 }
			
					$company_logo_path					=	'';
					$company_id      					=	auth()->user()->company_id;
					$company 							=	Company::findOrFail($company_id);
					if(!empty(@$request->company_name)){
						$company->company_name 			=	@$request->company_name;
					}
					if(!empty(@$request->website)){
						$company->website 				=	@$request->website;
					}
					if(!empty(@$request->industries)){
						$company->industries 			=	@$request->industries;
					}
					if(!empty(@$request->company_size)){
						$company_size = $request->company_size;
						[$from_size, $to_size] = explode('-', $company_size);
						$company->company_size_from 			=	$from_size;
						$company->company_size 			=	$to_size;
					}
					if(!empty(@$request->industries_type)){
						$company->industries_type 		=	@$request->industries_type;
					}
					if(!empty(@$request->founded)){
						$company->founded 				=	@$request->founded;
					}
					if(!empty(@$request->success_per)){
						$company->success_per 				=	@$request->success_per;
					}
				// 	dd(@$request->description);
					if(!empty(@$request->description)){
						$company->description 			=	@$request->description; //htmlentities(@$request->kt_docs_tinymce_hidden, ENT_QUOTES, "UTF-8");
					}
					
					if ($request->hasFile('logo')) {

					    $file = $request->file('logo');

						$companyNameR2 = DB::table('companies')
						->where('id',auth()->user()->company_id)
						->value('company_name');   // change column if needed
						$companyNameR2 = strtolower(trim($companyNameR2 ?? 'unknown'));
						$companyNameR2 = preg_replace('/[^a-z0-9_-]/i','_', $companyNameR2);
						$companyNameRTwo = $companyNameR2 . '_' . auth()->user()->company_id;
						
						$filename = uniqid('JERO_logo').'.'.$file->getClientOriginalExtension();

						$tempPath = $file->storeAs('Companies/'.$companyNameRTwo.'/Images', $filename,'r2'); // STORAGE/app/temp
						$fileUrl = "https://file.jero.works/Companies/{$companyNameRTwo}/Images/{$filename}";



				        // $filename = time() . '.' . $file->getClientOriginalExtension();
				        // $filePath = 'Images/' . $filename;

				        // Storage::disk('s3')->put($filePath, file_get_contents($file));
	
				        // $fileUrl = Storage::disk('s3')->url($filePath);
						
				        $company->logo = $fileUrl;
					    
				        // 	$company_logo_path				=	Files::upload($request->logo,'company-logo');
				        // 	$company->logo 					=	$company_logo_path;
					}
					if(!empty(@$request->language)){
						$company->language 				=	@$request->language;
					}			
					if(!empty(@$request->timezone)){
						$company->timezone 				=	@$request->timezone;
					}
					if(!empty(@$request->currency)){
						$company->currency 				=	@$request->currency;
					}
					if(!empty(@$request->time_format)){
						$company->time_format 			=	@$request->time_format;
					}
					if(!empty(@$request->date_format)){
						$company->date_format 			=	@$request->date_format;
					}

					if(!empty(@$request->date_format)){
						if(@$request->app_duplicate==1){
							$company->app_duplicate 			=	@$request->app_duplicate;
							$company->app_duplicate_days 			=	@$request->app_duplicate_requisition_wise;
						}elseif(@$request->app_duplicate==2){
							$company->app_duplicate 			=	@$request->app_duplicate;
							$company->app_duplicate_days 			=	@$request->app_duplicate_overroll;
						}else{
							$company->app_duplicate 			=	@$request->app_duplicate;
							$company->app_duplicate_days 			=	'';
						}
					}
					// Opt-in for the cross-agency, job-wide duplicate pool. This
					// update() method is posted to by more than one settings tab, so
					// (like every other field here) it must only touch this column
					// when ITS OWN tab was actually submitted — a plain checkbox
					// posts nothing when unchecked, so its own presence can't be that
					// signal. The view pairs the checkbox with a hidden
					// enable_cross_agency_duplicate_submitted marker unique to this
					// tab, mirroring how app_duplicate above (mis)uses date_format's
					// presence for the same purpose, without inheriting that coupling.
					if ($request->has('enable_cross_agency_duplicate_submitted')) {
						$company->enable_cross_agency_duplicate = $request->has('enable_cross_agency_duplicate') ? 1 : 0;
					}
					if(!empty(@$request->bench_show_mode)){
						$company->bench_show_mode 			=	(int) @$request->bench_show_mode;

						
					}
								
					$company->save();
					// ElasticCompanyIndex::dispatch($company_id)->onConnection('emails');

					 $company_coins_value_elastic = [
						'description'=>$company->description,
						'logo'=>$company->logo,
						'company_size_from'=>$company->company_size_from,
						'company_size'=>$company->company_size,
						'industries_type'=>$company->industries_type,
						'founded'=>$company->founded,
						'industries'=>$company->industries,
						'updated_at'=>now()->toIso8601String()	
					];

					$params = [
						'index' => ELASTIC_COMPANIES_INDEX,
						'conflicts' => 'proceed', // avoid version conflict failures
						'body' => [
							'script' => [
								'lang'   => 'painless',
								'source' => "
									ctx._source.description    = params.description;
									ctx._source.logo    = params.logo;
									ctx._source.company_size_from    = params.company_size_from;
									ctx._source.company_size    = params.company_size;
									ctx._source.industries_type    = params.industries_type;
									ctx._source.founded    = params.founded;
									ctx._source.industries    = params.industries;
									ctx._source.updated_at       = params.updated_at;
								",
								'params' => $company_coins_value_elastic
							],
							'query' => [
								'bool' => [
									'must' => [
										['term' => ['id' => $company_id]]
									]
								]
							]
						]
					];
					// 🚀 Execute update
					$response = $this->elasticService->getClient()->updateByQuery($params);

					$client_data = GeneralMethod::getClientIdCompany($company_id);
					// dd($client_data,$company_id);
					$client	=	Client::findOrFail($client_data->client_id);
					if(!empty(@$request->name)){
						$client->name 			=	@$request->company_name;
					}
					if(!empty(@$request->website)){
						$client->website 				=	@$request->website;
					}
					if(!empty(@$request->industries)){
						$client->industry 			=	@$request->industries;
					}
					if(!empty(@$request->company_size)){
						
						$company_size = $request->company_size;
						[$from_size, $to_size] = explode('-', $company_size);
						$client->company_size_from 			=	$from_size;
						$client->company_sizes 			=	$to_size;
						// $client->company_sizes 			=	@$request->company_size;
					}
					if(!empty(@$request->industries_type)){
						$client->company_type 		=	@$request->industries_type;
					}
					if(!empty(@$request->founded)){
						$client->founded 				=	@$request->founded;
					}
					$client->logo = $company->logo;
					if(!empty(@$request->description)){
						$client->description 			=	@$request->description; //htmlentities(@$request->kt_docs_tinymce_hidden, ENT_QUOTES, "UTF-8");
					}
					$client->save();
					
					if($client_data){
						GeneralMethod::clientStaffingPartner($client_data->client_id);
					}
                    


                     
					
					$default_rates = DB::table('default_rates')->first();
                    if(!DB::table('commericials')->where('rateType',1)->where('company_id',$company_id)->exists()){

						$status = 0;
						if($company->company_type==1){
							$status = 1;
						}elseif($company->company_type==2){
							$status = 1; // IT Service Company
						}else{
							$status = 0; // Product Company
						}
						
						$value = array('company_id'=>$company_id,'rateType'=>1,'commtype'=>1,'sal_mode'=>0,'salary_currency'=>4,'status'=>$status);
						 
						$commericialId = DB::table('commericials')->insertGetId($value);
						 $rate = @$default_rates->rates_fixed;
						for($i=0; $i<10; $i++){
							$value = array('company_id'=>$company_id,'commericial_id'=>$commericialId,'commtype'=>1,'exp'=>$i,'rate'=>$rate);
							DB::table('commericial_details')->insertGetId($value);
						}

						$addressId 		=	@$request->addressId; 
						for($i=0; $i<=$addressId; $i++)
						{
						
							$countryID = '';
							$work_city = @$request->kt_tagify_work_location[$i];
							$work_city_all = json_decode($work_city, true);
							if (@$request->kt_tagify_work_location[$i] != "") {
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


						$addressId 		=	@$request->addressId; 
						for($i=0; $i<=$addressId; $i++)
						{
						
							$countryID = '';
							$work_city = @$request->kt_tagify_work_location[$i];
							$work_city_all = json_decode($work_city, true);
							if (@$request->kt_tagify_work_location[$i] != "") {
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

											
						}

					}


					if(!empty(@$request->sales_email)){
						
						if(!DB::table('master_notifications')->where('notifications_name','=','Sales')->where('company_id','=',$company_id)->exists()){
							$values 				= array(
								'notifications_name' => 'Sales', 
								'notifications_email' =>1, 
								'notifications_phone' => 0,
								'priority' 		=> 0, 
								'company_id' 		=> $company_id, 
								'email' 		=> $request->sales_email
							);

                            $rowId = DB::table('master_notifications')->insertGetId($values);	
							$response = $this->elasticService->getClient()->update([
								'index' => ELASTIC_MASTER_NOTIFICATIONS,
								'id'    => $rowId ,
								'retry_on_conflict' => 5,
								'body'  => [
									'doc' => [ 
										'id' =>  $rowId ,
										'notifications_name' => 'Sales',
										'notifications_email' => 1,
										'notifications_phone' => 0,
										'priority' => 0,
										'status' => 1,
										'email' => $request->sales_email,
										'mobile' => null,
										'company_id' => $company_id
									],
									'doc_as_upsert' => true,
									'detect_noop' => false
								],
							]);


						}else{
												
                            DB::table('master_notifications')->where('notifications_name','=','Sales')->where('company_id','=',$company_id)->update(['email'=> $request->sales_email]);	

							$params = [
								'index' => ELASTIC_MASTER_NOTIFICATIONS,
								'body'  => [
									'query' => [
										'bool' => [
											'must' => [
												['term' => ['company_id' => (int) $company_id]],
												['match' => ['notifications_name' => 'Sales']], // exact match
											]
										]
									],
									'script' => [
										'lang' => 'painless',
										'source' => 'ctx._source.email = params.email',
										'params' => [
											'email' => $request->sales_email,
										]
									]
								]
							];

							$response = $this->elasticService->getClient()->updateByQuery($params);

						}

					}
					if(!empty(@$request->recuitment_email)){
						if(!DB::table('master_notifications')->where('notifications_name','=','Recuitment')->where('company_id','=',$company_id)->exists()){
							$values 				= array(
								'notifications_name' => 'Recuitment', 
								'notifications_email' =>1, 
								'notifications_phone' => 0,
								'priority' 		=> 0, 
								'company_id' 		=> $company_id, 
								'email' 		=> $request->recuitment_email
							);

                            $rowId =  DB::table('master_notifications')->insertGetId($values);	
								
							$response = $this->elasticService->getClient()->update([
								'index' => ELASTIC_MASTER_NOTIFICATIONS,
								'id'    => $rowId,
								'retry_on_conflict' => 5,
								'body'  => [
									'doc' => [ 
										'id' =>  $rowId,
										'notifications_name' => 'Recuitment',
										'notifications_email' => 1,
										'notifications_phone' => 0,
										'priority' => 0,
										'status' => 1,
										'email' => $request->recuitment_email,
										'mobile' => null,
										'company_id' => $company_id
									],
									'doc_as_upsert' => true,
									'detect_noop' => false
								],
							]);

						}else{
							
                            DB::table('master_notifications')->where('notifications_name','=','Recuitment')->where('company_id','=',$company_id)->update(['email'=> $request->recuitment_email]);	

							$params = [
								'index' => ELASTIC_MASTER_NOTIFICATIONS,
								'body'  => [
									'query' => [
										'bool' => [
											'must' => [
												['term' => ['company_id' => (int) $company_id]],
												['match' => ['notifications_name' => 'Recuitment']], // exact match
											]
										]
									],
									'script' => [
										'lang' => 'painless',
										'source' => 'ctx._source.email = params.email',
										'params' => [
											'email' => $request->recuitment_email,
										]
									]
								]
							];

							$response = $this->elasticService->getClient()->updateByQuery($params);

						}

					}
			
					// Insert Skills
					 $strdata	=	$tagsArr =	"";
					 // Insert Skills
					if (!empty(@$request->tags)) {
						$tags 			=	@$request->tags;
						$tags 			=	json_decode($tags, true);
						$tagsArr 		=	array_column($tags, 'value');
						$strdata 		=	implode(',', $tagsArr);
						
							 DB::table('clients')
							 ->where('company_id', @$company_id)
							 ->where('self_company', 1)
							 ->limit(1) 
							 ->update(array('tags' => @$strdata));					
					}
			
					if($request->pagename != 'settings'){					
							// THIRD TAB-3
							$client_id		=	'';
							$client_obj		=	DB::table('clients')->select('id')->where('company_id', $company_id)->where('self_company', 1)->first(); 
							$client_id		=	@$client_obj->id;	
							$addressId 		=	@$request->addressId;
							DB::table('client_location')->where('client_id', $client_id)->delete();  
							for($i=0; $i<=$addressId; $i++)
								{
								
									$address 			= @$request->address[$i];
									$country 			= '';//@$request->country[$i];
									$state 				= '';//@$request->state[$i];
									$city 				= @$request->kt_tagify_work_location[$i];	
									$postcode 			= '0';//@$request->postcode[$i];	


									$stateId = '';
									$cityID = '';
									$countryID = '';
									$city = '';
									$work_city = @$request->kt_tagify_work_location[$i];
									$work_city_all = json_decode($work_city, true);
									if (@$request->kt_tagify_work_location[$i] != "") {
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

											// dd($city_row);

											if($city_row){
												$cityID = (@$city_row) ? $city_row->city_id : '';
												$countryID = (@$city_row) ? $city_row->country_id : '';
												$stateId = GeneralMethod::stateId($cityID);

												$values 				= array(
													'client_id' 	=> $client_id, 
													'country_id' 	=> $countryID, 
													'city' 			=> $city,
													'city_id' 		=> $cityID, 
													'address' 		=> $address, 
													'postcode' 		=> $postcode, 
													'state' 		=> $stateId
												);

												
												DB::table('client_location')->insert($values);	

											}
											
										}
									}

													
								}
								
							// for($i=0; $i<=$addressId; $i++){
								
							// 	$address 			= @$request->address[$i];
							// 	$country 			= @$request->country[$i];
							// 	$state 				= @$request->state[$i];
							// 	$city 				= @$request->city[$i];	
							// 	$postcode 			= @$request->postcode[$i];		
							// 	if(!empty($country) && !empty($address) && !empty($postcode) && !empty($state)){
							// 			$city_id 			= $city = '';
							// 			if($country == 101){
							// 				$city_id 			= @$request->city_id[$i];	
							// 			}else{
							// 				$city 				= @$request->city[$i];	
							// 			}			
							// 			$values 				= array(
							// 												'client_id' 	=> $client_id, 
							// 												'country_id' 	=> $country, 
							// 												'city' 			=> $city,
							// 												'city_id' 		=> $city_id, 
							// 												'address' 		=> $address, 
							// 												'postcode' 		=> $postcode, 
							// 												'state' 		=> $state
							// 											);
							// 		DB::table('client_location')->insert($values);	
							// 	}	
							// }
								
							// dd(count($request->countries));
							// UPDATE CLIENT IMAGE 
							if($this->global_company_type == 3){
									$client_id		=	$client_obj->id;	
									if($client_id > 0){			
											$maxsize 			=	5242880;
											$directoryPath 		=	GeneralMethod::document_manage_directory($company_id);	
											$timeStamp 			=	time();
									
											if ($request->hasFile('logo')) {
											
												$name = $file_size = $file_type = $temp = $target_file = $logicalpath = '';
												$file_size 		=	@$_FILES['logo']['size'];
												$file_type 		=	@$_FILES['logo']['type'];
												$temp 			=	@$_FILES['logo']['tmp_name'];
												
												$path_parts 	=	pathinfo($_FILES['logo']['name']);
												$extension 		=	$path_parts['extension'];
												$filename 		=	strtolower($path_parts['filename']);
												$altfilename 	=	GeneralMethod::getValidUploadFileName($filename) . "-" . $timeStamp . "." . $extension;
												$dirArr 		=	explode("/", $directoryPath);
												$target_file 	=	$directoryPath . '/' . $altfilename;
												$allowed 		=	array('doc', 'docx', 'pdf', 'png', 'jpeg', 'jpg', 'DOC', 'DOCX', 'PDF', 'PNG', 'JPEG', 'JPG');
												$ext 			=	pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);          
												
												if (in_array($ext, $allowed)) {
												
														$logicalpath 				=	substr($target_file, strpos($target_file, 'user-uploads'));
														$src 						=	$_SERVER['DOCUMENT_ROOT']."/public/user-uploads/company-logo/".$company_logo_path;      //"/home/www/example.com/source/folders/123456";  
														$dest 						=	$target_file;   																			// destination folder or file        
														copy($src,$dest);							
														$client 					=	Client::find($client_id);
														$client->logo				=	@$altfilename;	
														$client->logo_physical_path	=	@$target_file;	
														$client->logo_logical_path	=	@$logicalpath;	
														$client->save(); 		
																
												}						 
											}		
									}
							}



					}
					//    dd($request->all);
					//    if(count($request->country) > 0){
					// 	DB::table('job_locations')->where('company_id',$company_id)->delete();
					// 	 for($i=0; $i< count($request->country); $i++){

							
					// 			DB::table('job_locations')->insert(['company_id'=>$company_id,'country_id'=>$request->country[$i],'location'=>'.']);
							
							

					// 	}
					//    }

				$cachekeys = [
					'candidateDetails_version_user_'.$this->user->id,
					'clientsviewmore_version_user_'.$this->user->id
				];
				foreach ($cachekeys as $key) {
					GeneralMethod::cacheClean($key);
				}
           

			return Reply::dataOnly(['status' => 'success']);   
		
		}

		public function addressupdate(Request $request){
			// dd($request->clientId());
			$stateId = '';
			$cityID = '';
			$countryID = '';
			$city = '';
			$postcode = '';
			$address 			= @$request->address;
			$client_id = (int) $request->clientId; 
			$address = $address[0];
			$work_city = @$request->kt_tagify_work_location_one;
			$work_city_all = json_decode($work_city[0], true);
			// dd($work_city);
			if (@$request->kt_tagify_work_location_one != "") {
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

					// dd($city_row);

					if($city_row){
						$cityID = (@$city_row) ? $city_row->city_id : '';
						$countryID = (@$city_row) ? $city_row->country_id : '';
						$stateId = GeneralMethod::stateId($cityID);

						$values 				= array(
							'client_id' 	=> $client_id, 
							'country_id' 	=> $countryID, 
							'city' 			=> $city,
							'city_id' 		=> $cityID, 
							'address' 		=> $address, 
							'postcode' 		=> $postcode, 
							'state' 		=> $stateId
						);

						DB::table('client_location')->insert($values);	
						return Reply::dataOnly(['status' => 'success']);

					}
					
				}
			}
			
		}
			
		/* Career Page Settings */
		public function careerPageSettings()
		{
			$company_id       = auth()->user()->company_id;
			$this->company    = DB::table('companies')->where('id', $company_id)->first();
			$this->global     = DB::table('global_settings')->first();
			try {
				$this->careerSettings = DB::table('company_career_settings')->where('company_id', $company_id)->first();
			} catch (\Exception $e) {
				$this->careerSettings = null;
			}
			/* Ensure table + all Template Design columns exist */
			DB::statement("CREATE TABLE IF NOT EXISTS `company_career_settings` (
				`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`company_id` bigint(20) UNSIGNED NOT NULL,
				`primary_color` varchar(20) DEFAULT '#2563EB',
				`secondary_color` varchar(20) DEFAULT '#10B981',
				`font_family` varchar(50) DEFAULT 'Poppins',
				`default_language` varchar(10) DEFAULT 'en',
				`currency` varchar(10) DEFAULT 'INR',
				`page_layout` varchar(20) DEFAULT 'modern',
				`jobs_display` varchar(20) DEFAULT 'card',
				`career_page_status` tinyint(1) DEFAULT 1,
				`td_template` varchar(30) DEFAULT 'modern',
				`td_color_primary` varchar(20) DEFAULT '#2563EB',
				`td_color_secondary` varchar(20) DEFAULT '#1F2937',
				`td_color_accent` varchar(20) DEFAULT '#10B981',
				`td_color_bg` varchar(20) DEFAULT '#FFFFFF',
				`td_theme_mode` varchar(10) DEFAULT 'light',
				`td_layout` varchar(20) DEFAULT 'wide',
				`td_logo_path` varchar(500) DEFAULT NULL,
				`td_favicon_path` varchar(500) DEFAULT NULL,
				`td_opt_life` tinyint(1) DEFAULT 1,
				`td_opt_testi` tinyint(1) DEFAULT 1,
				`td_opt_animation` tinyint(1) DEFAULT 1,
				`td_custom_css` text DEFAULT NULL,
				`stat_departments` int DEFAULT 15,
				`stat_countries` int DEFAULT 5,
				`stat_employees` varchar(20) DEFAULT '220+',
				`stat_retention` varchar(20) DEFAULT '95%',
				`created_at` timestamp NULL DEFAULT NULL,
				`updated_at` timestamp NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `company_career_settings_company_id_unique` (`company_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
			/* Add columns if table existed before Template Design was introduced */
			$tdColumns = [
				'td_template'       => "ALTER TABLE `company_career_settings` ADD COLUMN `td_template` varchar(30) DEFAULT 'modern'",
				'td_color_primary'  => "ALTER TABLE `company_career_settings` ADD COLUMN `td_color_primary` varchar(20) DEFAULT '#2563EB'",
				'td_color_secondary'=> "ALTER TABLE `company_career_settings` ADD COLUMN `td_color_secondary` varchar(20) DEFAULT '#1F2937'",
				'td_color_accent'   => "ALTER TABLE `company_career_settings` ADD COLUMN `td_color_accent` varchar(20) DEFAULT '#10B981'",
				'td_color_bg'       => "ALTER TABLE `company_career_settings` ADD COLUMN `td_color_bg` varchar(20) DEFAULT '#FFFFFF'",
				'td_theme_mode'     => "ALTER TABLE `company_career_settings` ADD COLUMN `td_theme_mode` varchar(10) DEFAULT 'light'",
				'td_layout'         => "ALTER TABLE `company_career_settings` ADD COLUMN `td_layout` varchar(20) DEFAULT 'wide'",
				'td_logo_path'      => "ALTER TABLE `company_career_settings` ADD COLUMN `td_logo_path` varchar(500) DEFAULT NULL",
				'td_favicon_path'   => "ALTER TABLE `company_career_settings` ADD COLUMN `td_favicon_path` varchar(500) DEFAULT NULL",
				'td_opt_life'       => "ALTER TABLE `company_career_settings` ADD COLUMN `td_opt_life` tinyint(1) DEFAULT 1",
				'td_opt_testi'      => "ALTER TABLE `company_career_settings` ADD COLUMN `td_opt_testi` tinyint(1) DEFAULT 1",
				'td_opt_animation'  => "ALTER TABLE `company_career_settings` ADD COLUMN `td_opt_animation` tinyint(1) DEFAULT 1",
				'td_custom_css'     => "ALTER TABLE `company_career_settings` ADD COLUMN `td_custom_css` text DEFAULT NULL",
				'stat_departments'  => "ALTER TABLE `company_career_settings` ADD COLUMN `stat_departments` int DEFAULT 15",
				'stat_countries'    => "ALTER TABLE `company_career_settings` ADD COLUMN `stat_countries` int DEFAULT 5",
				'stat_employees'    => "ALTER TABLE `company_career_settings` ADD COLUMN `stat_employees` varchar(20) DEFAULT '220+'",
				'stat_retention'    => "ALTER TABLE `company_career_settings` ADD COLUMN `stat_retention` varchar(20) DEFAULT '95%'",
			];
			$existing = DB::select("SHOW COLUMNS FROM `company_career_settings`");
			$existingCols = array_column($existing, 'Field');
			foreach ($tdColumns as $col => $sql) {
				if (!in_array($col, $existingCols)) {
					try { DB::statement($sql); } catch (\Exception $ex) {}
				}
			}
			if (!$this->careerSettings) {
				$this->careerSettings = DB::table('company_career_settings')->where('company_id', $company_id)->first();
			}
			return view('service_company.career-page.settings', $this->data);
		}

		public function careerPageSettingsSave(Request $request)
		{
			$company_id = auth()->user()->company_id;

			$data = [
				'primary_color'      => $request->primary_color,
				'secondary_color'    => $request->secondary_color,
				'font_family'        => $request->font_family,
				'default_language'   => $request->default_language,
				'currency'           => $request->currency,
				'page_layout'        => $request->page_layout,
				'jobs_display'       => $request->jobs_display,
				'career_page_status' => $request->has('career_page_status') ? 1 : 0,
				/* Template Design fields */
				'td_template'        => $request->input('td_template', 'modern'),
				'td_color_primary'   => $request->input('td_color_primary', '#2563EB'),
				'td_color_secondary' => $request->input('td_color_secondary', '#1F2937'),
				'td_color_accent'    => $request->input('td_color_accent', '#10B981'),
				'td_color_bg'        => $request->input('td_color_bg', '#FFFFFF'),
				'td_theme_mode'      => $request->input('td_theme_mode', 'light'),
				'td_layout'          => $request->input('td_layout', 'wide'),
				'td_opt_life'        => $request->has('td_opt_life') ? 1 : 0,
				'td_opt_testi'       => $request->has('td_opt_testi') ? 1 : 0,
				'td_opt_animation'   => $request->has('td_opt_animation') ? 1 : 0,
				'td_custom_css'      => $request->input('td_custom_css', ''),
				'updated_at'         => now(),
			];

			/* Handle logo upload */
			if ($request->hasFile('td_logo') && $request->file('td_logo')->isValid()) {
				$logo = $request->file('td_logo');
				$logoName = 'career_logo_' . $company_id . '_' . time() . '.' . $logo->getClientOriginalExtension();
				$logo->move(public_path('uploads/career'), $logoName);
				$data['td_logo_path'] = 'uploads/career/' . $logoName;
			}

			/* Handle favicon upload */
			if ($request->hasFile('td_favicon') && $request->file('td_favicon')->isValid()) {
				$fav = $request->file('td_favicon');
				$favName = 'career_favicon_' . $company_id . '_' . time() . '.' . $fav->getClientOriginalExtension();
				$fav->move(public_path('uploads/career'), $favName);
				$data['td_favicon_path'] = 'uploads/career/' . $favName;
			}

			DB::table('company_career_settings')->updateOrInsert(
				['company_id' => $company_id],
				array_merge($data, ['company_id' => $company_id, 'created_at' => now()])
			);

			/* Ensure upload directory exists */
			if (!is_dir(public_path('uploads/career'))) {
				mkdir(public_path('uploads/career'), 0755, true);
			}

			return Reply::success('Career page settings saved successfully.');
		}

		public function google($id)
    	{			
			$company_id      			=	auth()->user()->company_id;
			$this->company  			=	DB::table('companies')->select('*')->where('id', $company_id)->first();
			$view 						=	view('service_company.account.google', $this->data)->render();
        	return Reply::dataOnly(['status' => 'success', 'view' => $view]);								
    	}	
		public function zoom($id)
    	{			
			$company_id      			=	auth()->user()->company_id;
			$this->company  			=	DB::table('companies')->select('*')->where('id', $company_id)->first();			
			$view 						=	view('service_company.account.zoom', $this->data)->render();
        	return Reply::dataOnly(['status' => 'success', 'view' => $view]);								
    	}
  		public function videoMeetingSeetingUpdate(Request $request)
		{
					$company_id   			=	$SER_NAME	=	"";
					$company_id      		=	auth()->user()->company_id;						
					$SER_NAME 				=	$request->SER_NAME;
								
					if($SER_NAME == "GOOGLE"){
						$google_status	= $google_client_id	= $google_client_secret	=	"";				
						$google_client_id 			=	$request->google_client_id;
						$google_client_secret 		=	$request->google_client_secret;				
						$update						=	array('google_client_id' => @$google_client_id, 'google_client_secret' => @$google_client_secret, 'google_verify' => 0);							
						DB::table('companies')->where('id', $company_id)->limit(1) ->update($update);   
						return Reply::dataOnly(['status' => 'success']);
					}				
					if($SER_NAME == "ZOOM"){
						$zoom_email	= $zoom_api_key	= $zoom_api_secret	=	"";				
						$zoom_email 				=	$request->zoom_email;
						$zoom_api_key 				=	$request->zoom_api_key;	
						$zoom_api_secret 			=	$request->zoom_api_secret;				
						$update						=	array('zoom_email' => @$zoom_email, 'zoom_api_key' => @$zoom_api_key, 'zoom_api_secret' => @$zoom_api_secret );							
						DB::table('companies')->where('id', $company_id)->limit(1) ->update($update);   
						return Reply::dataOnly(['status' => 'success']);
					}			
		}	
  		public function storeDept(Request $request)
		{
		      
					$company_id      	=	auth()->user()->company_id;	
					$dept_name			=	$request->dept_name;
					$values 			=	array('company_id' => $company_id, 'name' => $dept_name);
					$insertGetId = DB::table('job_categories')->insertGetId($values);
					// dd($insertGetId,auth()->user()->company_id,ELASTIC_JOB_CATEGORIES);	
					 $response = $this->elasticService->getClient()->update([
                        'index' => ELASTIC_JOB_CATEGORIES,
                        'id'    => $insertGetId,
						'retry_on_conflict' => 5,
                        'body'  => [
                            'doc' => [ 
								'id' =>  $insertGetId,
								'company_id' =>(int) auth()->user()->company_id,
								'name' => $request->dept_name,
                                'created_at' => now()->toIso8601String(),
                                'updated_at' => now()->toIso8601String()
							],
							'doc_as_upsert' => true,
							'detect_noop' => false
						],
                    ]);
				
					$message			=	'<div class="alert alert-success" id="success-alert">
									  <button type="button" class="close bg-transparent border-0 fw-bold" data-dismiss="alert">x</button>
									  <strong>Success! </strong> New Process have added successfully.
									</div>';
			
			
					$departmentArr   	=	DB::table('job_categories')->select('*')->where('company_id', $company_id)->orderBy('job_categories.name', 'ASC')->get(); 
					$html				=	'';		
					$cnt				=	0;
					if(!empty($departmentArr)){
					foreach($departmentArr as $dval){
														if($cnt %3==0)
														{
															$html	.=	'<div class="form-group fv-plugins-icon-container has-success">
																			<div class="row">';					
														}
												$cnt++;
							$html	.=	'<div class="col-lg-4">
																						   <div class="badge badge-secondary d-flex align-items-center justify-content-between py-4 px-5" data-class="secondary">
																							  <div class="fs-4">'.$dval->name.'</div>
																							  <div class="edit_delete cursor-pointer">													  
										 <a class="text-primary me-2 d-none" href="javascript:void(0);">
																									<span class="svg-icon svg-icon-primary">                           
																									   <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																										  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																											 <rect x="0" y="0" width="24" height="24"></rect>
																											 <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "></path>
																											 <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"></rect>
																										  </g>
																									   </svg>
																									</span>Edit
																								 </a>                        
																								
																								 
																								 <!--begin::Delete Icon-->
																								 <a class="text-danger dept_delete" href="javascript:void(0);" rel="'.$dval->id.'">
																									<span class="svg-icon svg-icon-danger svg-icon-2x">
																									   <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																										  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																											 <rect x="0" y="0" width="24" height="24"></rect>
																											 <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"></path>
																											 <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path>
																										  </g>
																									   </svg>
																									</span>
																									<!-- Delete -->
																								 </a>          
																								 <!--end::Delete Icon-->
																							  </div>
																						   </div>
																						</div>';
												if($cnt %3==0){										
													$html	.=	'	</div>
																	<div class="form-check"></div>
																	<div class="form-text text-muted"></div>
																 </div>';
												}			 
				}
			}			 
												
				return Reply::dataOnly(['status' => 'success', 'html'=> $html , 'message'=> $message]);   
			
		}
		
		public function add_team_member_form($id)
    	{	

			$company_id      			=	auth()->user()->company_id;

			
			$eventId = 1;
			$coinsRequ = 1;
			$companyId = (int) $company_id;
			$coinsDeductions = GeneralMethod::coinsValidationsAllcompany($companyId,$eventId,$coinsRequ);
			if($coinsDeductions->status){
				return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message,'company_coins'=> $coinsDeductions->company_coins]);
			}


			$this->company  			=	DB::table('companies')->select('*')->where('id', $company_id)->first();
			$this->roles   				=	DB::table('roles')->select('*')->where('company_id', $company_id)->orderBy('roles.display_name', 'ASC')->get();		
			$view 						=	view('service_company.account.add_team_member_form', $this->data)->render();
        	return Reply::dataOnly(['status' => 'success', 'view' => $view]);								
    	}	
		
		public function boostkeywordadd(Request $request) 
		{
			$work_city = request('kt_tagify_work_location');
			$keyWord = request('title');
			$company_id = request('keyword_compant_id');
			$employment_type = request('employment_type');
			$campaign_name = request('campaign_name');
			$countryID = null;
			$work_city_all = json_decode($work_city, true);
			if (\request('kt_tagify_work_location') != "") {
				foreach ($work_city_all as $cityVal) {
					$parts = explode('|', $cityVal['value']);
					// Trim to remove any leading or trailing whitespace
					$countrName = trim($parts[0]);
					$city_row = DB::table('city_list')
					->join('state_list', 'state_list.id', '=', 'city_list.state_id')
					->join('countries', 'countries.id', '=', 'state_list.country_id')
					->select('city_list.city_id', 'countries.id as country_id')
					->where('countries.country_name', '=', $countrName)
					->first();

					if($city_row){
						
						$countryID = (@$city_row) ? $city_row->country_id : '';
					}
					
				}
			}
				
				if(!DB::table('boostkeywords')->where('company_id','=',$company_id)->where('country_id','=',$countryID)->where('skill_name','=',$keyWord)->where('campaign_type','=',$employment_type)->exists()){
					$insertArr = array('company_id' =>$company_id,'campaign_name' =>$campaign_name,'country_id' =>$countryID, 'skill_name' => $keyWord,'campaign_type' =>$employment_type, 'status' =>1);
					DB::table('boostkeywords')->insert($insertArr);
					return response()->json([
						'message' => 'Keyword has been added successfully!',
						'status' => 'success'
					]); 
				}else{
					return response()->json([
						'message' => 'Keyword already added!',
						'status' => 'faild'
					]); 
				}
		
					
				

					 


		}
		public function keyaddcreate(Request $request){
			$this->pageTitle = 'Campaign';
			abort_if(Gate::denies('add_jobs'), 403);
	
	
			$company_id = auth()->user()->company_id;
			$this->company_id =$company_id;
			
			$this->consultancy = DB::table('users')->select('id', 'name')->where('type', 1)->where('company_id', $company_id)->where('status', 'active')->get();
			$this->team = DB::table('users')->select('id', 'name')->where('type', 0)->where('company_id', $company_id)->where('status', 'active')->get();
	
			$this->employmentType = GeneralMethod::employmentType();
			
			
	
			
			$this->cities = GeneralMethod::allCities();
	
			$this->default_loc = '';
			$this->default_qualification ='';
	
			$this->countries = DB::table('countries')->get();

			$cities = DB::table('countries')
					->select('countries.country_name')
					->where('countries.country_name','!=','')
					->orderBy('countries.country_name')
					->get()
					->map(function($city) {
						return $city->country_name;
					});
				$this->cities =  $cities;
				
	
			$this->valSelected = array();
			// dd(auth()->user()->email);
			$this->recruiterlist = DB::table('users')
				->select('users.name')
				->where('users.email',auth()->user()->email)
				->where('users.type', '=', 0)
				->get();
				// dd($this->recruiterlist);
			$this->page_event = 'ADD';
			$this->currency = GeneralMethod::currency();
			$this->salaryMode = GeneralMethod::salaryMode();
			$this->direct_add = true;
			return view('service_company.account.add_edit_key', $this->data);
		}

		public function boostStatusUpdate(Request $request)
		{
			$company_id		=	auth()->user()->company_id;
			$chkStatus		=	$request->chkStatus;
			$userId			=	$request->userId;
			$status_text = '';
			
			if($chkStatus == 'true'){
							DB::table('boostkeywords')->where('company_id', @$company_id)->where('id', @$userId)->limit(1) ->update(array('status' => 1));  
							$status_text = 'Active'; 		 
			}else{
				
							DB::table('boostkeywords')->where('company_id', @$company_id)->where('id', @$userId)->limit(1) ->update(array('status' => 0));  
							$status_text = 'Inactive';  			 
			}			 
			return Reply::dataOnly(['status' => 'success','status_text'=>$status_text]);
		}

  
}
