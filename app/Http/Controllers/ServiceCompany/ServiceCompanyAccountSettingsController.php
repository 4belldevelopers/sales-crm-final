<?php

/*** ########################################################
Controller    	: 	AdminSkillsController
Purmose			:
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021
########################################################** */

namespace App\Http\Controllers\ServiceCompany;

use App\Helper\GeneralMethod;
use App\Helper\Reply;
use App\Jobs\ElasticCompanyIndex;
use App\Models\Client;
use App\Models\Company;
use App\Models\CompanyCareerSetting;
use App\Models\Country;
use App\Models\JobCategory;
use App\Services\AccountSettingsService;
use App\Services\AgencyCommercialTermsService;
use App\Services\ElasticService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Validator;

class ServiceCompanyAccountSettingsController extends ServiceCompanyBaseController
{
    protected $mailService;

    protected AccountSettingsService $accountSettingsService;

    public function __construct(ElasticService $elasticService, AccountSettingsService $accountSettingsService)
    {
        parent::__construct();
        $this->pageTitle = 'Company Profile';
        $this->pageIcon = 'icon-grid';
        $this->elasticService = $elasticService;
        $this->accountSettingsService = $accountSettingsService;
    }

    /**
     * Account Settings V2 Phase 2 Service & Cache Index Method
     *
     * Intent: Delegates tenant settings & master dropdown retrieval to AccountSettingsService and
     * ReferenceDataCache with Redis caching, preserving 100% backward compatibility for all view variables.
     *
     * Tenancy: All data queries are strictly scoped by auth()->user()->company_id.
     *
     * Author: Antigravity AI
     * Date: August 12, 2026
     */
    public function index()
    {
        $this->parentCompanyType = GeneralMethod::parentCompanyType();
        $this->companyType = GeneralMethod::companyType();
        $this->industries_type_arr = GeneralMethod::industries_type_arr();
        $ads_show = request('ads');
        $requestedTab = request('tab');
        $isStandaloneAccountTab = $requestedTab === 'services' || $requestedTab === 'ads' || $ads_show == 'show';
        $this->overview_tab = 'active show';
        $this->services_tab = '';
        $this->boostkeyword_tab = '';
        // Intent: route the Company Profile left-submenu directly to its existing account content without changing data flows.
        if ($requestedTab === 'services') {
            $this->overview_tab = '';
            $this->services_tab = 'active show';
        }
        if ($requestedTab === 'ads' || $ads_show == 'show') {
            $this->boostkeyword_tab = 'active show';
            $this->overview_tab = '';
        }

        $company_id = auth()->user()->company_id;
        $this->company_id = $company_id;
        $this->hide_account_tabs_header = $isStandaloneAccountTab;

        // Retrieve cached page data from AccountSettingsService; standalone Commercials/Ads loads skip hidden notification payloads.
        $pageData = $this->accountSettingsService->getAccountPageData($company_id, [
            'include_notifications' => ! $isStandaloneAccountTab,
        ]);

        foreach ($pageData as $key => $value) {
            $this->{$key} = $value;
        }

        return view('service_company.account.index', $this->data);
    }

    /**
     * Account Settings V2 Phase 3 Lazy Tab Content Loader
     *
     * Intent: Asynchronously renders and returns HTML for requested tab partial view.
     * Tenancy: Scoped strictly by auth()->user()->company_id.
     */
    public function getTabContent(string $tabName)
    {
        $company_id = auth()->user()->company_id;
        $pageData = $this->accountSettingsService->getAccountPageData($company_id);

        foreach ($pageData as $key => $value) {
            $this->{$key} = $value;
        }

        $allowedTabs = [
            'overview' => 'service_company.account.v2.tabs.overview',
            'interview_Stage' => 'service_company.account.v2.tabs.interview_stage',
            'terms' => 'service_company.account.v2.tabs.terms',
            'offerterms' => 'service_company.account.v2.tabs.offerterms',
            'agencyofferterms' => 'service_company.account.v2.tabs.agencyofferterms',
            'offercomponent' => 'service_company.account.v2.tabs.offercomponent',
            'services' => 'service_company.account.v2.tabs.services',
            'boostkeyword' => 'service_company.account.v2.tabs.boostkeyword',
            'settings' => 'service_company.account.v2.tabs.settings',
        ];

        if (! isset($allowedTabs[$tabName])) {
            return response()->json(['status' => 'error', 'message' => 'Invalid tab requested.'], 400);
        }

        $viewName = $allowedTabs[$tabName];
        if (view()->exists($viewName)) {
            $html = view($viewName, $this->data)->render();

            return response()->json([
                'status' => 'success',
                'tab' => $tabName,
                'html' => $html,
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Tab view not found.'], 404);
    }

    public function offerAmountChange($idSet)
    {
        // dd($setDefault);

        $setDefault = decrypt($idSet);

        $company_id = auth()->user()->company_id;

        $offer_points = DB::table('offer_point_companies')
            ->select('offer_point_companies.*')
            ->where('offer_point_companies.company_id', '=', $company_id)
            ->where('offer_point_companies.section_type', '=', 0)
            ->where('offer_point_companies.section_divided', '=', 0)
            ->orderByDesc('offer_point_companies.sorting')
            ->get();

        $offer_point_other = DB::table('offer_point_companies')
            ->select('offer_point_companies.*')
            ->where('offer_point_companies.company_id', '=', $company_id)
            ->where('offer_point_companies.section_type', '=', 1)
            ->where('offer_point_companies.section_divided', '=', 0)
            ->orderByDesc('offer_point_companies.sorting')
            ->get();

        if ($offer_points->isEmpty() || $setDefault == 1) {
            $offer_points = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=', 0)->where('offer_points.section_type', '=', 0)->orderByDesc('sorting')->get();
        }

        if ($offer_point_other->isEmpty() || $setDefault == 1) {
            $offer_point_other = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=', 0)->where('offer_points.section_type', '=', 1)->orderByDesc('sorting')->get();
        }

        $offer_point_hourly = DB::table('offer_point_companies')
            ->select('offer_point_companies.*')
            ->where('offer_point_companies.company_id', '=', $company_id)
            ->where('offer_point_companies.section_type', '=', 0)
            ->where('offer_point_companies.section_divided', '=', 1)
            ->orderByDesc('offer_point_companies.sorting')
            ->get();

        $offer_point_other_hourly = DB::table('offer_point_companies')
            ->select('offer_point_companies.*')
            ->where('offer_point_companies.company_id', '=', $company_id)
            ->where('offer_point_companies.section_type', '=', 1)
            ->where('offer_point_companies.section_divided', '=', 1)
            ->orderByDesc('offer_point_companies.sorting')
            ->get();

        if ($offer_point_hourly->isEmpty() || $setDefault == 1) {
            $offer_point_hourly = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=', 1)->where('offer_points.section_type', '=', 0)->orderByDesc('sorting')->get();
        }

        if ($offer_point_other_hourly->isEmpty() || $setDefault == 1) {
            $offer_point_other_hourly = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=', 1)->where('offer_points.section_type', '=', 1)->orderByDesc('sorting')->get();
        }

        $this->offer_points = $offer_points;
        $this->offer_point_other = $offer_point_other;

        $this->offer_point_hourly = $offer_point_hourly;
        $this->offer_point_other_hourly = $offer_point_other_hourly;

        $this->offer_id = 23;
        $this->section_other = '';
        $this->section_hourly = 'd-none';

        return view('service_company.account.offerAmountChange', $this->data);

    }

    public function companysavecreateoffer(Request $request)
    {
        $company_id = auth()->user()->company_id;
        // dd($request->all());
        if ($request->current_ctc_mode == 2 || $request->current_ctc_mode == 1 || $request->current_ctc_mode == 3) {
            $validator = Validator::make($request->all(), [
                'offer_point_hourly.*' => 'sometimes|required',
                'option_percentage_hourly.*' => 'sometimes|required',
                'bonus_point_hourly.*' => 'sometimes|required',
                'bonus_option_percentage_hourly.*' => 'sometimes|required',
            ], [
                'offer_point_hourly.*.required' => 'Offer point is required.',
                'option_percentage_hourly.*.required' => 'Option percentage is required.',

                'bonus_point_hourly.*.required' => 'Bonus point is required.',
                'bonus_option_percentage_hourly.*.required' => 'Bonus option percentage is required.',
            ]);
        } else {

            $validator = Validator::make($request->all(), [
                'offer_point.*' => 'sometimes|required',
                'option_percentage.*' => 'sometimes|required',
                'bonus_points.*' => 'sometimes|required',
                'bonus_option_percentage.*' => 'sometimes|required',
            ], [
                'offer_point.*.required' => 'Offer point is required.',
                'option_percentage.*.required' => 'Option percentage is required.',

                'bonus_points.*.required' => 'Other point is required.',
                'bonus_option_percentage.*.required' => 'Other option percentage is required.',
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($request->current_ctc_mode == 2) {
            $totalSum = array_sum($request->option_percentage_hourly);
        } else {
            $totalSum = array_sum($request->option_percentage);
        }
        // dd($totalSum);
        if ($totalSum != 100) {
            return response()->json(['status' => 'errors', 'message' => 'Please check Percentage and complete 100%']);
        }

        $offer_id = 0;

        try {
            // Start the transaction
            DB::beginTransaction();

            if ($request->current_ctc_mode == 2 || $request->current_ctc_mode == 1 || $request->current_ctc_mode == 3) {

                // Prepare the main offer data
                DB::table('offer_point_companies')->where('company_id', $company_id)->where('section_divided', 1)->delete();
                DB::table('offer_point_companies')->where('company_id', $company_id)->update(['show_default' => 0]);
                // Check if offer points exist and insert them
                if (! empty($request->offer_point_hourly)) {
                    foreach ($request->offer_point_hourly as $bp => $point) {
                        $basic_offer_points = [
                            'points' => $point,
                            'percentage' => $request->option_percentage_hourly[$bp],
                            'condition_check' => $request->option_salarymode_hourly[$bp],
                            'section_type' => 0,
                            'section_divided' => 1,
                            'show_default' => $request->current_ctc_mode,
                            'company_id' => $company_id,
                        ];
                        DB::table('offer_point_companies')->insert($basic_offer_points);
                    }
                }
                if (! empty($request->bonus_point_hourly)) {
                    foreach ($request->bonus_point_hourly as $bp => $point) {
                        $bonus_offer_points = [
                            'points' => $point,
                            'percentage' => $request->bonus_option_percentage_hourly[$bp],
                            'condition_check' => $request->bonus_option_salarymode_hourly[$bp],
                            'section_type' => 1,
                            'section_divided' => 1,
                            'show_default' => $request->current_ctc_mode,
                            'company_id' => $company_id,
                        ];
                        DB::table('offer_point_companies')->insert($bonus_offer_points);
                    }
                }

            } else {
                // Prepare the main offer data
                DB::table('offer_point_companies')->where('company_id', $company_id)->update(['show_default' => 0]);
                DB::table('offer_point_companies')->where('company_id', $company_id)->where('section_divided', 0)->delete();

                // Check if offer points exist and insert them
                if (! empty($request->offer_point)) {
                    foreach ($request->offer_point as $bp => $point) {
                        $basic_offer_points = [
                            'points' => $point,
                            'percentage' => $request->option_percentage[$bp],
                            'condition_check' => $request->option_salarymode[$bp],
                            'view_type' => $request->basic_view[$bp],
                            'section_type' => 0,
                            'section_divided' => 0,
                            'show_default' => $request->current_ctc_mode,
                            'company_id' => $company_id,
                        ];
                        DB::table('offer_point_companies')->insert($basic_offer_points);
                    }
                }

                if (! empty($request->bonus_points)) {
                    foreach ($request->bonus_points as $bp => $point) {
                        $bonus_offer_points = [
                            'points' => $point,
                            'percentage' => $request->bonus_option_percentage[$bp],
                            'condition_check' => $request->bonus_option_salarymode[$bp],
                            'view_type' => $request->bonus_view[$bp],
                            'section_type' => 1,
                            'section_divided' => 0,
                            'show_default' => $request->current_ctc_mode,
                            'company_id' => $company_id,
                        ];
                        DB::table('offer_point_companies')->insert($bonus_offer_points);
                    }
                }

            }

            // Commit the transaction if all operations are successful
            DB::commit();

            // Return a success response
            return response()->json(['status' => 'success', 'message' => 'Offer Created successfully.']);

        } catch (\Exception $e) {
            // Rollback the transaction if any operation fails
            DB::rollBack();

            // Log the exception message (optional)
            \Log::error('Transaction failed: '.$e->getMessage());
            // dd($e->getMessage());

            // Return an error response
            return response()->json(['status' => 'failed', 'message' => 'Failed to save data. Please try again.'], 500);
        }

    }

    public function addIntStage(Request $request)
    {

        $company_id = auth()->user()->company_id;
        $parent = $request->ltb_stage;
        $name = $request->ltb_stage_name;
        $ltb_class = $request->ltb_class;

        $priority = 0;
        $rowDataCount = DB::table('candidate_status')
            ->select('*')
            ->where('candidate_status.company_id', $company_id)
            ->where('candidate_status.parent', $parent)
            ->orderBy('candidate_status.priority', 'DESC')
            ->count();
        if ($rowDataCount > 0) {
            $rowData = DB::table('candidate_status')->select('priority')->where('company_id', $company_id)->where('parent', $parent)->orderBy('priority', 'DESC')->first();
            $priority = $rowData->priority;
            $priority++;
        }

        $bg_title_color_code = $svg_class_name = '';
        if ($parent == 1) {
            $bg_title_color_code = '318ebb';
            $svg_class_name = 'svg-icon-danger-Sourcing';
        } elseif ($parent == 2) {
            $bg_title_color_code = 'c000ff';
            $svg_class_name = 'svg-icon-danger-Screening';
        } elseif ($parent == 3) {
            $bg_title_color_code = '009ef7';
            $svg_class_name = 'svg-icon-danger-Evaluation';
        } elseif ($parent == 4) {
            $bg_title_color_code = 'ff4098';
            $svg_class_name = 'svg-icon-danger-Negotiation';
        } elseif ($parent == 5) {
            $bg_title_color_code = 'ffab00';
            $svg_class_name = 'svg-icon-danger-Offer';
        } elseif ($parent == 6) {
            $bg_title_color_code = '06c732';
            $svg_class_name = 'svg-icon-danger-Joined';
        }

        $values = ['company_id' => $company_id, 'parent' => $parent, 'name' => $name, 'priority' => $priority];
        DB::table('candidate_status')->insert($values);
        $lastInsertId = DB::getPdo()->lastInsertId();

        $this->accountSettingsService->flushAccountCache($company_id);

        $stageHtml = '<li id="'.@$lastInsertId.'" class="int_block kanban-item d-flex align-items-center justify-content-between '.@$ltb_class.' ui-sortable-handle"> 
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

        $message = '<div class="alert alert-success" id="success-alert">
					<button type="button" class="close bg-transparent border-0 fw-bold" data-dismiss="alert">x</button>
					<strong>Success! </strong> New Process have added successfully.
					</div>';

        return Reply::dataOnly(['status' => 'success', 'stageHtml' => $stageHtml, 'message' => $message]);
    }

    public function delIntStage(Request $request)
    {
        $block = '';
        $company_id = auth()->user()->company_id;
        $block = @$request->block;
        if ($block > 0) {
            DB::table('candidate_status')->where('company_id', $company_id)->where('id', $block)->delete();
        }
        $message = '<div class="alert alert-success" id="success-alert"><button type="button" class="close bg-transparent border-0 fw-bold" data-dismiss="alert">x</button><strong>Success! </strong> New Process have added successfully.</div>';

        return Reply::dataOnly(['status' => 'success', 'message' => $message]);
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

        $this->categories = JobCategory::all();
        $this->companyType = GeneralMethod::companyType();
        $company_id = auth()->user()->company_id;
        $this->company_id = $company_id;
        $this->account = DB::table('companies')->select('*')->where('id', $company_id)->first();
        $this->company_tags = DB::table('company_tags')->select('*')->where('company_id', $company_id)->get();

        $this->industries_type_arr = GeneralMethod::industries_type_arr();

        $this->client = Client::select('clients.id', 'clients.tags')->where('clients.company_id', '=', $company_id)->where('clients.self_company', '=', 1)->first();
        $this->client_location = DB::table('client_location')
            ->join('countries', 'countries.id', '=', 'client_location.country_id')
            ->join('state_list', 'state_list.id', '=', 'client_location.state')
            ->join('city_list', 'city_list.city_id', '=', 'client_location.city_id')
            ->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'countries.country_name', 'state_list.state as state_name', 'city_list.city as city_name', 'client_location.country_id', 'client_location.state')
            ->where('client_location.client_id', @$this->client->id)
            ->get();

        // dd($this->client_location);

        $this->industries = DB::table('td_industries')
            ->select('td_industries.ind_id', 'td_industries.ind_name')
            ->orderBy('td_industries.ind_name', 'ASC')
            ->get();

        $this->countryArr = GeneralMethod::getAllCountryList();
        $this->statelist = GeneralMethod::statelist(101);
        $this->cities = GeneralMethod::allCities();
        // dd($this->cities);
        $statelist = DB::table('state_list')
            ->select('state_list.id', 'state_list.state as name', DB::raw('101 as country_id'))
            ->orderBy('state_list.state')
            ->get()->toArray();
        $this->indianStatelist = json_decode(json_encode($statelist), true);

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
                'disposable_email',
            ],
            'sales_email' => [
                'email:rfc,dns',
                'disposable_email',
            ],
        ], [
            'recuitment_email.email' => 'Recruitment email must be valid',
            'recuitment_email.disposable_email' => 'Recruitment email cannot be temporary',

            'sales_email.email' => 'Sales email must be valid',
            'sales_email.disposable_email' => 'Sales email cannot be temporary',
        ]);
        if ($validator->fails()) {
            return Reply::dataOnly(['status' => 'warning', 'message' => $validator->errors()->first()]);

        }

        $terms = '';
        if ($request->description == 'setDefault') {
            $global_settings = DB::table('global_settings')->select('global_settings.*')->first();
            if ($global_settings) {
                // Intent: reset only the selected offer-template field while preserving the legacy terms endpoints.
                $offerTemplateFields = ['offer_terms', 'internship_offer_terms', 'contract_offer_terms', 'agency_offer_terms', 'offer_terms_admin'];
                $requestedTemplateField = in_array($request->descriptiontype, $offerTemplateFields, true) ? $request->descriptiontype : null;
                if ($requestedTemplateField === 'offer_terms_admin') {
                    $requestedTemplateField = 'offer_terms';
                }
                if ($requestedTemplateField) {
                    $terms = @$global_settings->{$requestedTemplateField};
                } else {
                    $terms = @$global_settings->terms;
                }

            }

            return Reply::dataOnly(['status' => 'success', 'terms' => $terms]);

        } elseif ($request->pageterms == 'terms') {
            $company_id = auth()->user()->company_id;
            $company = Company::withoutGlobalScopes()->findOrFail($company_id);
            $company->terms = @$request->description;
            $company->save();
            $this->accountSettingsService->flushAccountCache($company_id);
            // Intent: refresh cached job drawer HTML after Agency Agreement content changes.
            Cache::forever('jobDetails_version_company_'.$company_id, ((int) Cache::get('jobDetails_version_company_'.$company_id, 1)) + 1);

            return Reply::dataOnly(['status' => 'success', 'terms' => $company->terms]);
        } elseif ($request->pageterms == 'termsoffer') {
            $company_id = auth()->user()->company_id;
            $company = Company::withoutGlobalScopes()->findOrFail($company_id);
            // Intent: save the currently selected candidate offer template without affecting other templates.
            $offerTemplateFields = ['offer_terms', 'internship_offer_terms', 'contract_offer_terms', 'agency_offer_terms'];
            $templateField = in_array($request->descriptiontype, $offerTemplateFields, true) ? $request->descriptiontype : 'offer_terms';
            if (! Schema::hasColumn('companies', $templateField)) {
                return Reply::dataOnly([
                    'status' => 'warning',
                    'message' => 'This offer template cannot be saved until the database column is added.',
                ]);
            }
            $company->{$templateField} = @$request->description;
            $company->save();
            $this->accountSettingsService->flushAccountCache($company_id);

            return Reply::dataOnly(['status' => 'success', 'terms' => $company->{$templateField}, 'template_field' => $templateField]);
        } elseif ($request->pageterms == 'termsagencyoffer') {
            $company_id = auth()->user()->company_id;
            $company = Company::withoutGlobalScopes()->findOrFail($company_id);
            if (! Schema::hasColumn('companies', 'agency_offer_terms')) {
                return Reply::dataOnly([
                    'status' => 'warning',
                    'message' => 'This offer template cannot be saved until the database column is added.',
                ]);
            }
            $company->agency_offer_terms = @$request->description;
            $company->save();
            $this->accountSettingsService->flushAccountCache($company_id);

            return Reply::dataOnly(['status' => 'success', 'terms' => $company->agency_offer_terms]);
        } elseif ($request->pageterms == 'agency_commercial_terms') {
            $company_id = auth()->user()->company_id;
            if (! Schema::hasTable('agency_commercial_terms')) {
                return Reply::dataOnly([
                    'status' => 'warning',
                    'message' => 'Agency commercial terms table is not available yet.',
                ]);
            }

            // Intent: save agency-facing commercial terms separately from client Services/Commercials.
            // Tenancy: Upsert is strictly scoped by the authenticated service company's company_id.
            $agencyTermsPayload = [
                'status' => $request->input('status', 'active'),
                'version' => $request->input('version', '1.0'),
                'fulltime_fee_model' => $request->input('fulltime_fee_model', 'percentage'),
                'fulltime_success_fee_percentage' => (float) $request->input('fulltime_success_fee_percentage', 8.33),
                'fulltime_payment_terms_days' => (int) $request->input('fulltime_payment_terms_days', 30),
                'fulltime_replacement_period_days' => (int) $request->input('fulltime_replacement_period_days', 90),
                'contract_billing_model' => $request->input('contract_billing_model', 'monthly'),
                'contract_rate_amount' => (float) $request->input('contract_rate_amount', 80000),
                'contract_rate_currency' => (int) $request->input('contract_rate_currency', 4),
                'contract_payment_terms_days' => (int) $request->input('contract_payment_terms_days', 30),
                'contract_min_duration_months' => (int) $request->input('contract_min_duration_months', 3),
                'contract_replacement_policy' => $request->input('contract_replacement_policy', 'As per Contract Terms'),
                'applies_to' => $request->input('applies_to', 'All Agencies'),
                'acceptance_required' => $request->boolean('acceptance_required', true) ? 1 : 0,
                'acceptance_trigger' => $request->input('acceptance_trigger', 'Before Candidate Submission'),
                'submission_before_acceptance' => $request->boolean('submission_before_acceptance') ? 1 : 0,
                'effective_from' => $request->input('effective_from', 'Agency Acceptance Date'),
                'agreement_duration_months' => (int) $request->input('agreement_duration_months', 12),
                'auto_renewal' => $request->boolean('auto_renewal', true) ? 1 : 0,
                'agency_can_reject' => $request->boolean('agency_can_reject', true) ? 1 : 0,
                'updated_by' => auth()->id(),
                'updated_by_name' => auth()->user()->name ?? null,
            ];
            // Intent: persist agency experience pricing as a compact JSON payload so account page loads do not need extra commercial master queries.
            $agencyExperienceRows = function (array $labels, array $rates): array {
                $rows = [];
                foreach ($labels as $index => $label) {
                    $label = trim((string) $label);
                    if ($label === '') {
                        continue;
                    }
                    $rows[] = [
                        'label' => $label,
                        'rate' => $rates[$index] ?? '',
                    ];
                }

                return $rows;
            };

            $agencyTermsPayload['settings_json'] = json_encode([
                'fulltime_fixed_fee_currency' => (int) $request->input('fulltime_fixed_fee_currency', 4),
                'fulltime_fixed_fee_amount' => $request->filled('fulltime_fixed_fee_amount') ? (float) $request->input('fulltime_fixed_fee_amount') : null,
                'fulltime_fixed_fee_unit' => $request->input('fulltime_fixed_fee_unit', 'Per Hire'),
                'fulltime_experience_pricing' => $agencyExperienceRows((array) $request->input('fulltime_experience_label', []), (array) $request->input('fulltime_experience_rate', [])),
                'fulltime_fee_cap_currency' => (int) $request->input('fulltime_fee_cap_currency', 4),
                'fulltime_fee_cap_amount' => $request->filled('fulltime_fee_cap_amount') ? (float) $request->input('fulltime_fee_cap_amount') : null,
                'fulltime_payment_timeline_from' => $request->input('fulltime_payment_timeline_from', 'From invoice date'),
                'fulltime_replacement_from' => $request->input('fulltime_replacement_from', 'From candidate joining date'),
                'fulltime_notice_period' => $request->input('fulltime_notice_period', 'As per client notice period'),
                'fulltime_basis' => $request->input('fulltime_basis', 'Annual Fixed CTC'),
                'fulltime_invoice_raising' => $request->input('fulltime_invoice_raising', 'After candidate joins'),
                'contract_agency_rate_type' => $request->input('contract_agency_rate_type', 'Fixed Rate'),
                'contract_billing_cycle' => $request->input('contract_billing_cycle', 'Monthly in Advance'),
                'contract_overtime_policy' => $request->input('contract_overtime_policy', 'As per client terms'),
                'contract_early_termination' => $request->input('contract_early_termination', 'As per contract terms'),
                'contract_invoice_raising' => $request->input('contract_invoice_raising', 'Monthly in Advance'),
                'contract_escalation_policy' => $request->input('contract_escalation_policy', 'As per contract terms'),
                'contract_rate_display' => $request->input('contract_rate_display', 'Depend upon job requirements'),
                'contract_experience_pricing' => $agencyExperienceRows((array) $request->input('contract_experience_label', []), (array) $request->input('contract_experience_rate', [])),
                'payment_modes_allowed' => array_values(array_unique((array) $request->input('payment_modes_allowed', ['bank_transfer', 'neft', 'rtgs']))),
                'general_currency' => (int) $request->input('general_currency', 4),
                'tax_handling' => $request->input('tax_handling', 'GST Extra'),
                'tds_deduction' => $request->input('tds_deduction', 'As per applicable law'),
                'round_off' => $request->input('round_off', 'To Nearest Rupee'),
            ]);

            app(AgencyCommercialTermsService::class)->upsertForCompany($company_id, $agencyTermsPayload);
            $this->accountSettingsService->flushAccountCache($company_id);

            return Reply::dataOnly(['status' => 'success', 'message' => 'Agency commercial terms saved successfully.']);
        }

        $company_logo_path = '';
        $company_id = auth()->user()->company_id;
        $company = Company::withoutGlobalScopes()->findOrFail($company_id);
        if (! empty(@$request->company_name)) {
            $company->company_name = @$request->company_name;
        }
        if (! empty(@$request->website)) {
            $company->website = @$request->website;
        }
        if (! empty(@$request->industries)) {
            $company->industries = @$request->industries;
        }
        if (! empty(@$request->company_size)) {
            $company_size = $request->company_size;
            [$from_size, $to_size] = explode('-', $company_size);
            $company->company_size_from = $from_size;
            $company->company_size = $to_size;
        }
        if (! empty(@$request->industries_type)) {
            $company->industries_type = @$request->industries_type;
        }
        if (! empty(@$request->founded)) {
            $company->founded = @$request->founded;
        }
        if (! empty(@$request->success_per)) {
            $company->success_per = @$request->success_per;
        }
        // 	dd(@$request->description);
        if (! empty(@$request->description)) {
            $company->description = @$request->description; // htmlentities(@$request->kt_docs_tinymce_hidden, ENT_QUOTES, "UTF-8");
        }

        if ($request->hasFile('logo')) {

            $file = $request->file('logo');

            $companyNameR2 = DB::table('companies')
                ->where('id', auth()->user()->company_id)
                ->value('company_name');   // change column if needed
            $companyNameR2 = strtolower(trim($companyNameR2 ?? 'unknown'));
            $companyNameR2 = preg_replace('/[^a-z0-9_-]/i', '_', $companyNameR2);
            $companyNameRTwo = $companyNameR2.'_'.auth()->user()->company_id;

            $filename = uniqid('JERO_logo').'.'.$file->getClientOriginalExtension();

            $tempPath = $file->storeAs('Companies/'.$companyNameRTwo.'/Images', $filename, 'r2'); // STORAGE/app/temp
            $fileUrl = "https://file.jero.works/Companies/{$companyNameRTwo}/Images/{$filename}";

            // $filename = time() . '.' . $file->getClientOriginalExtension();
            // $filePath = 'Images/' . $filename;

            // Storage::disk('s3')->put($filePath, file_get_contents($file));

            // $fileUrl = Storage::disk('s3')->url($filePath);

            $company->logo = $fileUrl;

            // 	$company_logo_path				=	Files::upload($request->logo,'company-logo');
            // 	$company->logo 					=	$company_logo_path;
        }
        if (! empty(@$request->language)) {
            $company->language = @$request->language;
        }
        if (! empty(@$request->timezone)) {
            $company->timezone = @$request->timezone;
        }
        if (! empty(@$request->currency)) {
            $company->currency = @$request->currency;
        }
        if (! empty(@$request->time_format)) {
            $company->time_format = @$request->time_format;
        }
        if (! empty(@$request->date_format)) {
            $company->date_format = @$request->date_format;
        }

        if (! empty(@$request->date_format)) {
            if (@$request->app_duplicate == 1) {
                $company->app_duplicate = @$request->app_duplicate;
                $company->app_duplicate_days = @$request->app_duplicate_requisition_wise;
            } elseif (@$request->app_duplicate == 2) {
                $company->app_duplicate = @$request->app_duplicate;
                $company->app_duplicate_days = @$request->app_duplicate_overroll;
            } else {
                $company->app_duplicate = @$request->app_duplicate;
                $company->app_duplicate_days = '';
            }
        }
        if (! empty(@$request->bench_show_mode)) {
            $company->bench_show_mode = (int) @$request->bench_show_mode;

        }

        $company->save();
        // ElasticCompanyIndex::dispatch($company_id)->onConnection('emails');

        $company_coins_value_elastic = [
            'description' => $company->description,
            'logo' => $company->logo,
            'company_size_from' => $company->company_size_from,
            'company_size' => $company->company_size,
            'industries_type' => $company->industries_type,
            'founded' => $company->founded,
            'industries' => $company->industries,
            'updated_at' => now()->toIso8601String(),
        ];

        $params = [
            'index' => ELASTIC_COMPANIES_INDEX,
            'conflicts' => 'proceed', // avoid version conflict failures
            'body' => [
                'script' => [
                    'lang' => 'painless',
                    'source' => '
									ctx._source.description    = params.description;
									ctx._source.logo    = params.logo;
									ctx._source.company_size_from    = params.company_size_from;
									ctx._source.company_size    = params.company_size;
									ctx._source.industries_type    = params.industries_type;
									ctx._source.founded    = params.founded;
									ctx._source.industries    = params.industries;
									ctx._source.updated_at       = params.updated_at;
								',
                    'params' => $company_coins_value_elastic,
                ],
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['id' => $company_id]],
                        ],
                    ],
                ],
            ],
        ];
        // 🚀 Execute update
        $response = $this->elasticService->getClient()->updateByQuery($params);

        $client_data = GeneralMethod::getClientIdCompany($company_id);
        // dd($client_data,$company_id);
        $client = Client::withoutGlobalScopes()->findOrFail($client_data->client_id);
        if (! empty(@$request->name)) {
            $client->name = @$request->company_name;
        }
        if (! empty(@$request->website)) {
            $client->website = @$request->website;
        }
        if (! empty(@$request->industries)) {
            $client->industry = @$request->industries;
        }
        if (! empty(@$request->company_size)) {

            $company_size = $request->company_size;
            [$from_size, $to_size] = explode('-', $company_size);
            $client->company_size_from = $from_size;
            $client->company_sizes = $to_size;
            // $client->company_sizes 			=	@$request->company_size;
        }
        if (! empty(@$request->industries_type)) {
            $client->company_type = @$request->industries_type;
        }
        if (! empty(@$request->founded)) {
            $client->founded = @$request->founded;
        }
        $client->logo = $company->logo;
        if (! empty(@$request->description)) {
            $client->description = @$request->description; // htmlentities(@$request->kt_docs_tinymce_hidden, ENT_QUOTES, "UTF-8");
        }
        $client->save();

        if ($client_data) {
            GeneralMethod::clientStaffingPartner($client_data->client_id);
        }

        $default_rates = DB::table('default_rates')->first();
        if (! DB::table('commericials')->where('rateType', 1)->where('company_id', $company_id)->exists()) {

            $status = 0;
            if ($company->company_type == 1) {
                $status = 1;
            } elseif ($company->company_type == 2) {
                $status = 1; // IT Service Company
            } else {
                $status = 0; // Product Company
            }

            $value = ['company_id' => $company_id, 'rateType' => 1, 'commtype' => 1, 'sal_mode' => 0, 'salary_currency' => 4, 'status' => $status];

            $commericialId = DB::table('commericials')->insertGetId($value);
            $rate = @$default_rates->rates_fixed;
            for ($i = 0; $i < 10; $i++) {
                $value = ['company_id' => $company_id, 'commericial_id' => $commericialId, 'commtype' => 1, 'exp' => $i, 'rate' => $rate];
                DB::table('commericial_details')->insertGetId($value);
            }

            $addressId = @$request->addressId;
            for ($i = 0; $i <= $addressId; $i++) {

                $countryID = '';
                $work_city = @$request->kt_tagify_work_location[$i];
                $work_city_all = json_decode($work_city, true);
                if (@$request->kt_tagify_work_location[$i] != '') {
                    foreach ($work_city_all as $cityVal) {
                        $parts = explode('|', $cityVal['value']);
                        // Trim to remove any leading or trailing whitespace
                        $cityName = trim($parts[0]);
                        $countrName = trim($parts[2]);
                        $city_row = DB::table('city_list')
                            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
                            ->join('countries', 'countries.id', '=', 'state_list.country_id')
                            ->select('city_list.city_id', 'countries.id as country_id')
                            ->where('city_list.city', '!=', '')
                            ->where('city_list.city', '=', $cityName)
                            ->where('countries.country_name', '=', $countrName)
                            ->first();

                        if ($city_row) {
                            $countryID = (@$city_row) ? $city_row->country_id : '';

                            $valueCountry = ['company_id' => $company_id, 'commericial_id' => $commericialId, 'commtype' => 1, 'country_id' => $countryID];

                            DB::table('commericial_countries')->insert($valueCountry);

                        }

                    }
                }

            }

        }

        if (! DB::table('commericials')->where('rateType', 2)->where('company_id', $company_id)->exists()) {
            $status = 0;
            if ($company->company_type == 1) {
                $status = 1;   // Staffing Company
            } elseif ($company->company_type == 2) {
                $status = 1; // IT Service Company
            } else {
                $status = 0; // Product Company
            }
            $value = ['company_id' => $company_id, 'rateType' => 2, 'commtype' => 2, 'status' => $status];
            $commericialId = DB::table('commericials')->insertGetId($value);
            $rate = @$default_rates->rates_perecentage;
            for ($i = 0; $i < 10; $i++) {
                $value = ['company_id' => $company_id, 'commericial_id' => $commericialId, 'commtype' => 2, 'exp' => $i, 'rate' => $rate];
                DB::table('commericial_details')->insertGetId($value);
            }

            $addressId = @$request->addressId;
            for ($i = 0; $i <= $addressId; $i++) {

                $countryID = '';
                $work_city = @$request->kt_tagify_work_location[$i];
                $work_city_all = json_decode($work_city, true);
                if (@$request->kt_tagify_work_location[$i] != '') {
                    foreach ($work_city_all as $cityVal) {
                        $parts = explode('|', $cityVal['value']);
                        // Trim to remove any leading or trailing whitespace
                        $cityName = trim($parts[0]);
                        $countrName = trim($parts[2]);
                        $city_row = DB::table('city_list')
                            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
                            ->join('countries', 'countries.id', '=', 'state_list.country_id')
                            ->select('city_list.city_id', 'countries.id as country_id')
                            ->where('city_list.city', '!=', '')
                            ->where('city_list.city', '=', $cityName)
                            ->where('countries.country_name', '=', $countrName)
                            ->first();

                        if ($city_row) {
                            $countryID = (@$city_row) ? $city_row->country_id : '';

                            $valueCountry = ['company_id' => $company_id, 'commericial_id' => $commericialId, 'commtype' => 2, 'country_id' => $countryID];

                            DB::table('commericial_countries')->insert($valueCountry);

                        }

                    }
                }

            }

        }

        if (! empty(@$request->sales_email)) {

            if (! DB::table('master_notifications')->where('notifications_name', '=', 'Sales')->where('company_id', '=', $company_id)->exists()) {
                $values = [
                    'notifications_name' => 'Sales',
                    'notifications_email' => 1,
                    'notifications_phone' => 0,
                    'priority' => 0,
                    'company_id' => $company_id,
                    'email' => $request->sales_email,
                ];

                $rowId = DB::table('master_notifications')->insertGetId($values);
                $response = $this->elasticService->getClient()->update([
                    'index' => ELASTIC_MASTER_NOTIFICATIONS,
                    'id' => $rowId,
                    'retry_on_conflict' => 5,
                    'body' => [
                        'doc' => [
                            'id' => $rowId,
                            'notifications_name' => 'Sales',
                            'notifications_email' => 1,
                            'notifications_phone' => 0,
                            'priority' => 0,
                            'status' => 1,
                            'email' => $request->sales_email,
                            'mobile' => null,
                            'company_id' => $company_id,
                        ],
                        'doc_as_upsert' => true,
                        'detect_noop' => false,
                    ],
                ]);

            } else {

                DB::table('master_notifications')->where('notifications_name', '=', 'Sales')->where('company_id', '=', $company_id)->update(['email' => $request->sales_email]);

                $params = [
                    'index' => ELASTIC_MASTER_NOTIFICATIONS,
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['company_id' => (int) $company_id]],
                                    ['match' => ['notifications_name' => 'Sales']], // exact match
                                ],
                            ],
                        ],
                        'script' => [
                            'lang' => 'painless',
                            'source' => 'ctx._source.email = params.email',
                            'params' => [
                                'email' => $request->sales_email,
                            ],
                        ],
                    ],
                ];

                $response = $this->elasticService->getClient()->updateByQuery($params);

            }

        }
        if (! empty(@$request->recuitment_email)) {
            if (! DB::table('master_notifications')->where('notifications_name', '=', 'Recuitment')->where('company_id', '=', $company_id)->exists()) {
                $values = [
                    'notifications_name' => 'Recuitment',
                    'notifications_email' => 1,
                    'notifications_phone' => 0,
                    'priority' => 0,
                    'company_id' => $company_id,
                    'email' => $request->recuitment_email,
                ];

                $rowId = DB::table('master_notifications')->insertGetId($values);

                $response = $this->elasticService->getClient()->update([
                    'index' => ELASTIC_MASTER_NOTIFICATIONS,
                    'id' => $rowId,
                    'retry_on_conflict' => 5,
                    'body' => [
                        'doc' => [
                            'id' => $rowId,
                            'notifications_name' => 'Recuitment',
                            'notifications_email' => 1,
                            'notifications_phone' => 0,
                            'priority' => 0,
                            'status' => 1,
                            'email' => $request->recuitment_email,
                            'mobile' => null,
                            'company_id' => $company_id,
                        ],
                        'doc_as_upsert' => true,
                        'detect_noop' => false,
                    ],
                ]);

            } else {

                DB::table('master_notifications')->where('notifications_name', '=', 'Recuitment')->where('company_id', '=', $company_id)->update(['email' => $request->recuitment_email]);

                $params = [
                    'index' => ELASTIC_MASTER_NOTIFICATIONS,
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['company_id' => (int) $company_id]],
                                    ['match' => ['notifications_name' => 'Recuitment']], // exact match
                                ],
                            ],
                        ],
                        'script' => [
                            'lang' => 'painless',
                            'source' => 'ctx._source.email = params.email',
                            'params' => [
                                'email' => $request->recuitment_email,
                            ],
                        ],
                    ],
                ];

                $response = $this->elasticService->getClient()->updateByQuery($params);

            }

        }

        // Insert Skills
        $strdata = $tagsArr = '';
        // Insert Skills
        if (! empty(@$request->tags)) {
            $tags = @$request->tags;
            $tags = json_decode($tags, true);
            $tagsArr = array_column($tags, 'value');
            $strdata = implode(',', $tagsArr);

            DB::table('clients')
                ->where('company_id', @$company_id)
                ->where('self_company', 1)
                ->limit(1)
                ->update(['tags' => @$strdata]);
        }

        if ($request->pagename != 'settings') {
            // THIRD TAB-3
            $client_id = '';
            $client_obj = DB::table('clients')->select('id')->where('company_id', $company_id)->where('self_company', 1)->first();
            $client_id = @$client_obj->id;
            $addressId = @$request->addressId;
            DB::table('client_location')->where('client_id', $client_id)->delete();
            for ($i = 0; $i <= $addressId; $i++) {

                $address = @$request->address[$i];
                $country = ''; // @$request->country[$i];
                $state = ''; // @$request->state[$i];
                $city = @$request->kt_tagify_work_location[$i];
                $postcode = '0'; // @$request->postcode[$i];

                $stateId = '';
                $cityID = '';
                $countryID = '';
                $city = '';
                $work_city = @$request->kt_tagify_work_location[$i];
                $work_city_all = json_decode($work_city, true);
                if (@$request->kt_tagify_work_location[$i] != '') {
                    foreach ($work_city_all as $cityVal) {
                        $parts = explode('|', $cityVal['value']);
                        // Trim to remove any leading or trailing whitespace
                        $cityName = trim($parts[0]);
                        $countrName = trim($parts[2]);
                        $city_row = DB::table('city_list')
                            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
                            ->join('countries', 'countries.id', '=', 'state_list.country_id')
                            ->select('city_list.city_id', 'countries.id as country_id')
                            ->where('city_list.city', '!=', '')
                            ->where('city_list.city', '=', $cityName)
                            ->where('countries.country_name', '=', $countrName)
                            ->first();

                        // dd($city_row);

                        if ($city_row) {
                            $cityID = (@$city_row) ? $city_row->city_id : '';
                            $countryID = (@$city_row) ? $city_row->country_id : '';
                            $stateId = GeneralMethod::stateId($cityID);

                            $values = [
                                'client_id' => $client_id,
                                'country_id' => $countryID,
                                'city' => $city,
                                'city_id' => $cityID,
                                'address' => $address,
                                'postcode' => $postcode,
                                'state' => $stateId,
                            ];

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
            if ($this->global_company_type == 3) {
                $client_id = $client_obj->id;
                if ($client_id > 0) {
                    $maxsize = 5242880;
                    $directoryPath = GeneralMethod::document_manage_directory($company_id);
                    $timeStamp = time();

                    if ($request->hasFile('logo')) {

                        $name = $file_size = $file_type = $temp = $target_file = $logicalpath = '';
                        $file_size = @$_FILES['logo']['size'];
                        $file_type = @$_FILES['logo']['type'];
                        $temp = @$_FILES['logo']['tmp_name'];

                        $path_parts = pathinfo($_FILES['logo']['name']);
                        $extension = $path_parts['extension'];
                        $filename = strtolower($path_parts['filename']);
                        $altfilename = GeneralMethod::getValidUploadFileName($filename).'-'.$timeStamp.'.'.$extension;
                        $dirArr = explode('/', $directoryPath);
                        $target_file = $directoryPath.'/'.$altfilename;
                        $allowed = ['doc', 'docx', 'pdf', 'png', 'jpeg', 'jpg', 'DOC', 'DOCX', 'PDF', 'PNG', 'JPEG', 'JPG'];
                        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);

                        if (in_array($ext, $allowed)) {

                            $logicalpath = substr($target_file, strpos($target_file, 'user-uploads'));
                            $src = $_SERVER['DOCUMENT_ROOT'].'/public/user-uploads/company-logo/'.$company_logo_path;      // "/home/www/example.com/source/folders/123456";
                            $dest = $target_file;   																			// destination folder or file
                            copy($src, $dest);
                            $client = Client::withoutGlobalScopes()->find($client_id);
                            $client->logo = @$altfilename;
                            $client->logo_physical_path = @$target_file;
                            $client->logo_logical_path = @$logicalpath;
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
            'clientsviewmore_version_user_'.$this->user->id,
        ];
        foreach ($cachekeys as $key) {
            GeneralMethod::cacheClean($key);
        }

        \Illuminate\Support\Facades\Cache::forget("tenant_default_currency:{$company_id}");
        \Illuminate\Support\Facades\Cache::forget("tenant_default_currency_key:{$company_id}");
        \Illuminate\Support\Facades\Cache::forget("tenant_default_currency_symbol:{$company_id}");
        \Illuminate\Support\Facades\Cache::forget("tenant_currency_code:{$company_id}");
        \Illuminate\Support\Facades\Cache::forget("tenant_country_code:{$company_id}");
        \Illuminate\Support\Facades\Cache::forget("tenant_default_country_id:{$company_id}");
        \Illuminate\Support\Facades\Cache::forget("tenant_default_calling_code:{$company_id}");
        \Illuminate\Support\Facades\Cache::forget("tenant_default_timezone:{$company_id}");
        \Illuminate\Support\Facades\Cache::forget("tenant_defaults_payload:{$company_id}");
        \Illuminate\Support\Facades\Cache::forever("tenant_defaults_version:{$company_id}", time());
        $this->accountSettingsService->flushAccountCache($company_id);

        return Reply::dataOnly([
            'status' => 'success',
            'tenant_defaults' => GeneralMethod::getTenantDefaultsPayload($company_id),
        ]);

    }

    public function addressupdate(Request $request)
    {
        // dd($request->clientId());
        $stateId = '';
        $cityID = '';
        $countryID = '';
        $city = '';
        $postcode = '';
        $address = @$request->address;
        $client_id = (int) $request->clientId;
        $address = $address[0];
        $work_city = @$request->kt_tagify_work_location_one;
        $work_city_all = json_decode($work_city[0], true);
        // dd($work_city);
        if (@$request->kt_tagify_work_location_one != '') {
            foreach ($work_city_all as $cityVal) {
                $parts = explode('|', $cityVal['value']);
                // Trim to remove any leading or trailing whitespace
                $cityName = trim($parts[0]);
                $countrName = trim($parts[2]);
                $city_row = DB::table('city_list')
                    ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
                    ->join('countries', 'countries.id', '=', 'state_list.country_id')
                    ->select('city_list.city_id', 'countries.id as country_id')
                    ->where('city_list.city', '!=', '')
                    ->where('city_list.city', '=', $cityName)
                    ->where('countries.country_name', '=', $countrName)
                    ->first();

                // dd($city_row);

                if ($city_row) {
                    $cityID = (@$city_row) ? $city_row->city_id : '';
                    $countryID = (@$city_row) ? $city_row->country_id : '';
                    $stateId = GeneralMethod::stateId($cityID);

                    $values = [
                        'client_id' => $client_id,
                        'country_id' => $countryID,
                        'city' => $city,
                        'city_id' => $cityID,
                        'address' => $address,
                        'postcode' => $postcode,
                        'state' => $stateId,
                    ];

                    DB::table('client_location')->insert($values);

                    $company_id = auth()->user()->company_id;
                    \Illuminate\Support\Facades\Cache::forget("tenant_country_code:{$company_id}");
                    \Illuminate\Support\Facades\Cache::forget("tenant_default_country_id:{$company_id}");
                    \Illuminate\Support\Facades\Cache::forget("tenant_default_calling_code:{$company_id}");
                    \Illuminate\Support\Facades\Cache::forget("tenant_defaults_payload:{$company_id}");
                    \Illuminate\Support\Facades\Cache::forever("tenant_defaults_version:{$company_id}", time());
                    $this->accountSettingsService->flushAccountCache($company_id);

                    return Reply::dataOnly([
                        'status' => 'success',
                        'tenant_defaults' => GeneralMethod::getTenantDefaultsPayload($company_id),
                    ]);

                }

            }
        }

    }

    /* Career Page Settings */
    /**
     * Career Page Settings V2
     *
     * Intent: Delegates career page settings retrieval to AccountSettingsService with 24-hour Redis caching.
     * Removed all runtime DDL operations (CREATE TABLE / ALTER TABLE / SHOW COLUMNS) for 10K+ concurrency.
     *
     * Tenancy: Scoped strictly by auth()->user()->company_id.
     *
     * Author: Antigravity AI
     * Date: August 17, 2026
     */
    public function careerPageSettings()
    {
        $company_id = auth()->user()->company_id;
        $careerData = $this->accountSettingsService->getCareerPageData($company_id);

        foreach ($careerData as $key => $value) {
            $this->{$key} = $value;
        }

        return view('service_company.career-page.settings', $this->data);
    }

    /**
     * Save Career Page Settings V2
     *
     * Intent: Updates tenant career page settings and invalidates Redis cache via AccountSettingsService.
     *
     * Tenancy: Scoped strictly by auth()->user()->company_id.
     *
     * Author: Antigravity AI
     * Date: August 17, 2026
     */
    public function careerPageSettingsSave(Request $request)
    {
        $company_id = auth()->user()->company_id;

        $primaryColor = $request->input('td_color_primary') ?: ($request->input('primary_color') ?: '#2563EB');
        $secondaryColor = $request->input('td_color_secondary') ?: ($request->input('secondary_color') ?: '#1F2937');

        $data = [
            'primary_color' => $primaryColor,
            'secondary_color' => $secondaryColor,
            'font_family' => $request->input('font_family', 'Poppins'),
            'default_language' => $request->input('default_language', 'en'),
            'currency' => $request->input('currency', 'INR'),
            'page_layout' => $request->input('page_layout', 'modern'),
            'jobs_display' => $request->input('jobs_display', 'card'),
            'career_page_status' => $request->has('career_page_status') ? 1 : 0,
            /* Template Design fields */
            'td_template' => $request->input('td_template', 'modern'),
            'td_color_primary' => $primaryColor,
            'td_color_secondary' => $secondaryColor,
            'td_color_accent' => $request->input('td_color_accent', '#10B981'),
            'td_color_bg' => $request->input('td_color_bg', '#FFFFFF'),
            'td_theme_mode' => $request->input('td_theme_mode', 'light'),
            'td_layout' => $request->input('td_layout', 'wide'),
            'td_opt_life' => $request->has('td_opt_life') ? 1 : 0,
            'td_opt_testi' => $request->has('td_opt_testi') ? 1 : 0,
            'td_opt_animation' => $request->has('td_opt_animation') ? 1 : 0,
            'td_custom_css' => $request->input('td_custom_css', ''),
            /* Hero Section fields */
            'hero_layout' => $request->input('hero_layout', 'left-right'),
            'hero_badge' => $request->input('hero_badge'),
            'hero_heading' => $request->input('hero_heading'),
            'hero_description' => $request->input('hero_description'),
            'hero_cta1_text' => $request->input('hero_cta1_text'),
            'hero_cta1_link' => $request->input('hero_cta1_link'),
            'hero_cta1_newtab' => $request->has('hero_cta1_newtab') ? 1 : 0,
            'hero_cta2_text' => $request->input('hero_cta2_text'),
            'hero_cta2_link' => $request->input('hero_cta2_link'),
            'hero_cta2_newtab' => $request->has('hero_cta2_newtab') ? 1 : 0,
            'hero_bg_style' => $request->input('hero_bg_style', 'gradient'),
            'hero_bg_gradient' => $request->input('hero_bg_gradient'),
            /* Company Info fields */
            'ci_industry' => $request->input('ci_industry'),
            'ci_company_name' => $request->input('ci_company_name'),
            'ci_headquarters' => $request->input('ci_headquarters'),
            'ci_tagline' => $request->input('ci_tagline'),
            'ci_website' => $request->input('ci_website'),
            'ci_linkedin' => $request->input('ci_linkedin'),
            'ci_founded_year' => $request->input('ci_founded_year'),
            'ci_company_size' => $request->input('ci_company_size'),
            'ci_twitter' => $request->input('ci_twitter'),
            'ci_about' => $request->input('ci_about'),
            'ci_mission' => $request->input('ci_mission'),
            'ci_vision' => $request->input('ci_vision'),
            /* Benefits Section fields */
            'benefits_enabled' => $request->has('benefits_enabled') ? 1 : 0,
            'benefits_title' => $request->input('benefits_title', 'Why Work With Us'),
            'benefits_description' => $request->input('benefits_description'),
            'benefits_display_style' => $request->input('benefits_display_style', 'grid'),
            'benefits_count' => $request->input('benefits_count', 8),
            /* Job Settings fields */
            'js_display_style' => $request->input('js_display_style', 'card'),
            'js_per_page' => $request->input('js_per_page', 12),
            'js_sort_by' => $request->input('js_sort_by', 'newest'),
            'js_show_posted_date' => $request->has('js_show_posted_date') ? 1 : 0,
            'js_show_experience' => $request->has('js_show_experience') ? 1 : 0,
            'js_show_salary' => $request->has('js_show_salary') ? 1 : 0,
            'js_show_tags' => $request->has('js_show_tags') ? 1 : 0,
            'js_apply_option' => $request->input('js_apply_option', 'resume'),
            /* SEO & Social fields */
            'seo_meta_title' => $request->input('seo_meta_title'),
            'seo_meta_desc' => $request->input('seo_meta_desc'),
            'seo_keywords' => $request->input('seo_keywords'),
            'seo_robots' => $request->input('seo_robots', 'index,follow'),
            'seo_canonical' => $request->input('seo_canonical'),
            'seo_linkedin' => $request->input('seo_linkedin'),
            'seo_twitter' => $request->input('seo_twitter'),
            'seo_facebook' => $request->input('seo_facebook'),
            'seo_instagram' => $request->input('seo_instagram'),
            'seo_youtube' => $request->input('seo_youtube'),
            /* Advanced / Tracking fields */
            'adv_caching' => $request->has('adv_caching') ? 1 : 0,
            'adv_cache_ttl' => $request->input('adv_cache_ttl', 86400),
            'adv_cdn' => $request->has('adv_cdn') ? 1 : 0,
            'adv_img_opt' => $request->has('adv_img_opt') ? 1 : 0,
            'adv_maintenance' => $request->has('adv_maintenance') ? 1 : 0,
            'adv_maintenance_msg' => $request->input('adv_maintenance_msg'),
            'adv_ga4_id' => $request->input('adv_ga4_id'),
            'adv_fb_pixel' => $request->input('adv_fb_pixel'),
            'adv_linkedin_tag' => $request->input('adv_linkedin_tag'),
            'adv_hotjar_id' => $request->input('adv_hotjar_id'),
            'updated_at' => now(),
        ];

        /* Ensure upload directory exists */
        if (! is_dir(public_path('uploads/career'))) {
            mkdir(public_path('uploads/career'), 0755, true);
        }

        /* Handle logo upload (supports td_logo, career_logo, ci_logo) */
        $logoFile = $request->file('td_logo') ?? $request->file('career_logo') ?? $request->file('ci_logo');
        if ($logoFile && $logoFile->isValid()) {
            $logoName = 'career_logo_'.$company_id.'_'.time().'.'.$logoFile->getClientOriginalExtension();
            $logoFile->move(public_path('uploads/career'), $logoName);
            $data['td_logo_path'] = 'uploads/career/'.$logoName;
        }

        /* Handle favicon upload */
        if ($request->hasFile('td_favicon') && $request->file('td_favicon')->isValid()) {
            $fav = $request->file('td_favicon');
            $favName = 'career_favicon_'.$company_id.'_'.time().'.'.$fav->getClientOriginalExtension();
            $fav->move(public_path('uploads/career'), $favName);
            $data['td_favicon_path'] = 'uploads/career/'.$favName;
        }

        /* Handle hero image 1 upload (supports td_hero_img1 or hero_illustration) */
        $hero1File = $request->file('td_hero_img1') ?? $request->file('hero_illustration');
        if ($hero1File && $hero1File->isValid()) {
            $img1Name = 'hero1_'.$company_id.'_'.time().'.'.$hero1File->getClientOriginalExtension();
            $hero1File->move(public_path('uploads/career'), $img1Name);
            $data['td_hero_img1_path'] = 'uploads/career/'.$img1Name;
        }

        /* Handle hero image 2 upload */
        if ($request->hasFile('td_hero_img2') && $request->file('td_hero_img2')->isValid()) {
            $img2 = $request->file('td_hero_img2');
            $img2Name = 'hero2_'.$company_id.'_'.time().'.'.$img2->getClientOriginalExtension();
            $img2->move(public_path('uploads/career'), $img2Name);
            $data['td_hero_img2_path'] = 'uploads/career/'.$img2Name;
        }

        CompanyCareerSetting::updateOrCreate(
            ['company_id' => $company_id],
            $data
        );

        /* Intent: Invalidate tenant career metadata and settings Redis caches when updated */
        \Illuminate\Support\Facades\Cache::forget("tenant_default_currency:{$company_id}");
        $this->accountSettingsService->flushCareerCache($company_id);

        return Reply::success('Career page settings saved successfully.');
    }

    public function google($id)
    {
        $company_id = auth()->user()->company_id;
        $this->company = DB::table('companies')->select('*')->where('id', $company_id)->first();
        $view = view('service_company.account.google', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function zoom($id)
    {
        $company_id = auth()->user()->company_id;
        $this->company = DB::table('companies')->select('*')->where('id', $company_id)->first();
        $view = view('service_company.account.zoom', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function videoMeetingSeetingUpdate(Request $request)
    {
        $company_id = $SER_NAME = '';
        $company_id = auth()->user()->company_id;
        $SER_NAME = $request->SER_NAME;

        if ($SER_NAME == 'GOOGLE') {
            $google_status = $google_client_id = $google_client_secret = '';
            $google_client_id = $request->google_client_id;
            $google_client_secret = $request->google_client_secret;
            $update = ['google_client_id' => @$google_client_id, 'google_client_secret' => @$google_client_secret, 'google_verify' => 0];
            DB::table('companies')->where('id', $company_id)->limit(1)->update($update);

            return Reply::dataOnly(['status' => 'success']);
        }
        if ($SER_NAME == 'ZOOM') {
            $zoom_email = $zoom_api_key = $zoom_api_secret = '';
            $zoom_email = $request->zoom_email;
            $zoom_api_key = $request->zoom_api_key;
            $zoom_api_secret = $request->zoom_api_secret;
            $update = ['zoom_email' => @$zoom_email, 'zoom_api_key' => @$zoom_api_key, 'zoom_api_secret' => @$zoom_api_secret];
            DB::table('companies')->where('id', $company_id)->limit(1)->update($update);

            return Reply::dataOnly(['status' => 'success']);
        }
    }

    public function storeDept(Request $request)
    {

        $company_id = (int) auth()->user()->company_id;
        $dept_name = trim($request->dept_name ?? ($request->name ?? ''));
        if ($dept_name === '') {
            return Reply::error(__('errors.addCategory'));
        }

        // 1. Check if department already exists in this organization (case-insensitive)
        $existing = \App\Models\JobCategory::where('company_id', $company_id)
            ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower($dept_name)])
            ->first();

        // 2. Check if department already exists in standard global taxonomy
        $standard = \App\Models\JobCategory::whereNull('company_id')
            ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower($dept_name)])
            ->first();

        if ($existing || $standard) {
            $matched = $existing ?: $standard;
            $departmentArr = DB::table('job_categories')->select('*')->where('company_id', $company_id)->orderBy('job_categories.name', 'ASC')->get();
            $html = '';
            $cnt = 0;
            if (! empty($departmentArr)) {
                foreach ($departmentArr as $dval) {
                    if ($cnt % 3 == 0) {
                        $html .= '<div class="form-group fv-plugins-icon-container has-success"><div class="row">';
                    }
                    $cnt++;
                    $html .= '<div class="col-lg-4">
                                <div class="badge badge-secondary d-flex align-items-center justify-content-between py-4 px-5" data-class="secondary">
                                  <div class="fs-4">'.$dval->name.'</div>
                                  <div class="edit_delete cursor-pointer">
                                     <a class="text-danger dept_delete" href="javascript:void(0);" rel="'.$dval->id.'">
                                        <span class="svg-icon svg-icon-danger svg-icon-2x">
                                           <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"></path><path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"></path></g></svg>
                                        </span>
                                     </a>
                                  </div>
                               </div>
                            </div>';
                    if ($cnt % 3 == 0) {
                        $html .= '</div><div class="form-check"></div><div class="form-text text-muted"></div></div>';
                    }
                }
            }

            $alertMsg = $standard
                ? 'Department "'.$matched->name.'" is already available in the standard global catalog.'
                : 'Department "'.$matched->name.'" already exists in your organization.';

            $message = '<div class="alert alert-info" id="success-alert">
                          <button type="button" class="close bg-transparent border-0 fw-bold" data-dismiss="alert">x</button>
                          <strong>Notice: </strong> '.$alertMsg.'
                        </div>';

            return Reply::dataOnly([
                'status' => 'success',
                'html' => $html,
                'message' => $message,
                'category_id' => $matched->id,
                'category_name' => $matched->name,
                'is_standard' => empty($matched->company_id),
                'already_exists' => true,
            ]);
        }

        $category = \App\Models\JobCategory::create([
            'company_id' => $company_id,
            'name' => $dept_name,
        ]);
        $insertGetId = $category->id;
        $response = $this->elasticService->getClient()->update([
            'index' => ELASTIC_JOB_CATEGORIES,
            'id' => $insertGetId,
            'retry_on_conflict' => 5,
            'body' => [
                'doc' => [
                    'id' => $insertGetId,
                    'company_id' => (int) auth()->user()->company_id,
                    'name' => $request->dept_name,
                    'created_at' => now()->toIso8601String(),
                    'updated_at' => now()->toIso8601String(),
                ],
                'doc_as_upsert' => true,
                'detect_noop' => false,
            ],
        ]);

        $message = '<div class="alert alert-success" id="success-alert">
					  <button type="button" class="close bg-transparent border-0 fw-bold" data-dismiss="alert">x</button>
					  <strong>Success! </strong> New Process have added successfully.
					</div>';

        $departmentArr = DB::table('job_categories')->select('*')->where('company_id', $company_id)->orderBy('job_categories.name', 'ASC')->get();
        $html = '';
        $cnt = 0;
        if (! empty($departmentArr)) {
            foreach ($departmentArr as $dval) {
                if ($cnt % 3 == 0) {
                    $html .= '<div class="form-group fv-plugins-icon-container has-success">
																			<div class="row">';
                }
                $cnt++;
                $html .= '<div class="col-lg-4">
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
                if ($cnt % 3 == 0) {
                    $html .= '	</div>
																	<div class="form-check"></div>
																	<div class="form-text text-muted"></div>
																 </div>';
                }
            }
        }

        return Reply::dataOnly([
            'status' => 'success',
            'html' => $html,
            'message' => $message,
            'category_id' => $insertGetId,
            'category_name' => $category->name,
            'is_standard' => false,
            'already_exists' => false,
        ]);

    }

    public function add_team_member_form($id)
    {
        $company_id = auth()->user()->company_id;
        $companyId = (int) $company_id;

        $eventId = 1;
        $coinsRequ = 1;
        $coinsDeductions = GeneralMethod::coinsValidationsAllcompany($companyId, $eventId, $coinsRequ);
        if ($coinsDeductions->status) {
            return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
        }

        $this->company = DB::table('companies')->select('*')->where('id', $companyId)->first();

        // Phase 1 Optimization: Cache roles in Redis for 24 hours to prevent repeated DB hits on drawer open
        $roles = cache()->remember('ats:roles:company_'.$companyId, 86400, function () use ($companyId) {
            return DB::table('roles')
                ->select('id', 'display_name', 'name', 'description')
                ->where('company_id', $companyId)
                ->orderBy('display_name', 'ASC')
                ->get();
        });

        $this->roles = $roles;
        $view = view('service_company.account.add_team_member_form', $this->data)->render();

        return Reply::dataOnly([
            'status' => 'success',
            'roles' => $roles,
            'view' => $view,
        ]);
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
        if (\request('kt_tagify_work_location') != '') {
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

                if ($city_row) {

                    $countryID = (@$city_row) ? $city_row->country_id : '';
                }

            }
        }

        if (! DB::table('boostkeywords')->where('company_id', '=', $company_id)->where('country_id', '=', $countryID)->where('skill_name', '=', $keyWord)->where('campaign_type', '=', $employment_type)->exists()) {
            $insertArr = ['company_id' => $company_id, 'campaign_name' => $campaign_name, 'country_id' => $countryID, 'skill_name' => $keyWord, 'campaign_type' => $employment_type, 'status' => 1];
            DB::table('boostkeywords')->insert($insertArr);

            return response()->json([
                'message' => 'Keyword has been added successfully!',
                'status' => 'success',
            ]);
        } else {
            return response()->json([
                'message' => 'Keyword already added!',
                'status' => 'faild',
            ]);
        }

    }

    public function keyaddcreate(Request $request)
    {
        $this->pageTitle = 'Campaign';
        abort_if(Gate::denies('add_jobs'), 403);

        $company_id = auth()->user()->company_id;
        $this->company_id = $company_id;

        $this->consultancy = DB::table('users')->select('id', 'name')->where('type', 1)->where('company_id', $company_id)->where('status', 'active')->get();
        $this->team = DB::table('users')->select('id', 'name')->where('type', 0)->where('company_id', $company_id)->where('status', 'active')->get();

        $this->employmentType = GeneralMethod::employmentType();

        $this->cities = GeneralMethod::allCities();

        $this->default_loc = '';
        $this->default_qualification = '';

        $this->countries = DB::table('countries')->get();

        $cities = DB::table('countries')
            ->select('countries.country_name')
            ->where('countries.country_name', '!=', '')
            ->orderBy('countries.country_name')
            ->get()
            ->map(function ($city) {
                return $city->country_name;
            });
        $this->cities = $cities;

        $this->valSelected = [];
        // dd(auth()->user()->email);
        $this->recruiterlist = DB::table('users')
            ->select('users.name')
            ->where('users.email', auth()->user()->email)
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
        $company_id = auth()->user()->company_id;
        $chkStatus = $request->chkStatus;
        $userId = $request->userId;
        $status_text = '';

        if ($chkStatus == 'true') {
            DB::table('boostkeywords')->where('company_id', @$company_id)->where('id', @$userId)->limit(1)->update(['status' => 1]);
            $status_text = 'Active';
        } else {

            DB::table('boostkeywords')->where('company_id', @$company_id)->where('id', @$userId)->limit(1)->update(['status' => 0]);
            $status_text = 'Inactive';
        }

        return Reply::dataOnly(['status' => 'success', 'status_text' => $status_text]);
    }
}
