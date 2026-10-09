<?php

/*** ########################################################
Controller    	: 	AdminCompanyController
Purmose			:
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021
########################################################** */

namespace App\Http\Controllers\ServiceCompany;

use App\Helper\Files;
use App\Helper\GeneralMethod;
use App\Helper\Reply;
use App\Http\Requests\Company\StoreCompany;
use App\Models\Company;
use App\Models\Job;
use App\Services\ElasticService;
use App\Services\FindAgencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class ServiceCompanyCompanyController extends ServiceCompanyBaseController
{
    protected $elasticService;

    public function __construct(ElasticService $elasticService)
    {
        parent::__construct();
        $this->pageTitle = 'menu.companies';
        $this->pageIcon = 'icon-film';
        $this->elasticService = $elasticService;
    }

    public function index()
    {
        abort_if(Gate::denies('view_company'), 403);

        $this->totalCompanies = Company::count();
        $this->activeCompanies = Company::where('status', 'active')->count();
        $this->inactiveCompanies = Company::where('status', 'inactive')->count();

        return view('service_company.company.index', $this->data);
    }

    public function create()
    {
        abort_if(Gate::denies('add_company'), 403);

        return view('service_company.company.create', $this->data);
    }

    public function store(StoreCompany $request)
    {
        abort_if(Gate::denies('add_company'), 403);

        $setting = new Company;
        $setting->company_name = $request->input('company_name');
        $setting->company_email = $request->input('company_email');
        $setting->company_phone = $request->input('company_phone');
        $setting->website = $request->input('website');
        $setting->address = $request->input('address');
        $setting->show_in_frontend = $request->input('show_in_frontend');
        $setting->job_opening_text = 'Welcome!';
        $setting->job_opening_title = 'We want people to thrive. We believe you do your best work when you feel your best.';

        if ($request->hasFile('logo')) {
            $setting->logo = Files::upload($request->logo, 'company-logo');
        }

        $setting->save();

        return Reply::redirect(route('service_company.company.index'), __('menu.companies').' '.__('messages.createdSuccessfully'));
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        abort_if(Gate::denies('edit_company'), 403);

        $this->company = Company::withoutGlobalScopes()->find($id);

        return view('service_company.company.edit', $this->data);
    }

    public function update(StoreCompany $request, $id)
    {
        abort_if(Gate::denies('edit_company'), 403);

        $setting = Company::withoutGlobalScopes()->findOrFail($id);
        $setting->company_name = $request->input('company_name');
        $setting->company_email = $request->input('company_email');
        $setting->company_phone = $request->input('company_phone');
        $setting->website = $request->input('website');
        $setting->address = $request->input('address');
        $setting->show_in_frontend = $request->input('show_in_frontend');

        // update company's jobs status
        if ($setting->status != $request->input('status')) {
            Job::where('company_id', $id)->update(['status' => $request->status]);
        }

        $setting->status = $request->input('status');

        if ($request->hasFile('logo')) {
            $setting->logo = Files::upload($request->logo, 'company-logo');
        }

        $setting->save();

        return Reply::redirect(route('service_company.company.index'), __('menu.companies').' '.__('messages.updatedSuccessfully'));
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('delete_company'), 403);

        Company::destroy($id);

        return Reply::success(__('messages.recordDeleted'));
    }

    public function data()
    {
        abort_if(Gate::denies('view_company'), 403);

        $categories = Company::all();

        return DataTables::of($categories)
            ->addColumn('action', function ($row) {
                $action = '';

                if ($this->user->cans('edit_company')) {
                    $action .= '<a href="'.route('service_company.company.edit', [$row->id]).'" class="btn btn-primary btn-circle"
                      data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                }

                if ($this->user->cans('delete_company')) {
                    $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-row-id="'.$row->id.'" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
                }

                return $action;
            })
            ->editColumn('status', function ($row) {
                if ($row->status == 'active') {
                    return '<label class="badge bg-success">'.__('app.active').'</label>';
                }
                if ($row->status == 'inactive') {
                    return '<label class="badge bg-danger">'.__('app.inactive').'</label>';
                }
            })
            ->editColumn('logo', function ($row) {
                return '<img src="'.$row->logo_url.'" class="img-responsive" height="25" />';
            })
            ->editColumn('company_name', function ($row) {
                return ucfirst($row->company_name);
            })
            ->addIndexColumn()
            ->rawColumns(['logo', 'action', 'status'])
            ->make(true);
    }

    /**
     * Intent: Renders the Find Agency discovery page for tenant service companies.
     * Tenancy: Scoped to authenticated user's company_id. Job lookup uses soft fallback to prevent 404 ModelNotFoundException.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function findAgency(Request $request)
    {

        $this->pageTitle = __('Find Agency');
        $company_id = auth()->user()->company_id;

        // Resolve job_id dynamically from request or fallback to company's latest job, using find() instead of findOrFail(48) to avoid 404
        $id = $request->get('id') ?? $request->get('job_id') ?? Job::where('company_id', $company_id)->latest()->value('id');
        $this->job = $id ? Job::withoutGlobalScopes()->find($id) : null;
        $this->custome_count = 0;

        if ($id) {
            session()->put('job_id', $id);
            $gdFilterData = GeneralMethod::gdFilterData($id);
        }

        $this->default_loc = '';
        $this->qualification = '';
        $this->min_exp = '';
        $this->max_exp = '';
        $this->min_salary_currency = '';
        $this->min_salary = '';
        $this->max_salary = '';
        $this->min_sal_mode = '';
        $this->default_skill = '';

        $this->cities = Cache::remember('ref:countries_ordered_list', 86400, function () {
            return DB::table('countries')
                ->select('countries.country_name')
                ->where('countries.country_name', '!=', '')
                ->orderBy('countries.country_name')
                ->pluck('countries.country_name');
        });

        $this->qualificationTag = Cache::remember('ref:qualification_master_list', 86400, function () {
            return DB::table('qualification_master')
                ->orderBy('qualification_master.display_order', 'ASC')
                ->pluck('name');
        });

        $this->td_industries = Cache::remember('ref:td_industries_list', 86400, function () {
            return DB::table('td_industries')->select('ind_id as id', 'ind_name as name')->get();
        });

        $allCompanies = 0;
        $activeCompanies = 0;
        $superAdminCompanyIds = \App\Helper\GeneralMethod::superAdminCompanyIds();
        try {
            $mustNotTerms = array_merge(
                [['term' => ['id' => $company_id]]],
                array_map(fn ($saId) => ['term' => ['id' => $saId]], $superAdminCompanyIds)
            );

            $params = [
                'index' => ELASTIC_COMPANIES_INDEX,
                'body' => [
                    'size' => 0,
                    'query' => [
                        'bool' => [
                            'must_not' => $mustNotTerms,
                        ],
                    ],
                    'aggs' => [
                        'all_companies' => [
                            'value_count' => [
                                'field' => 'id',
                            ],
                        ],
                        'active_companies' => [
                            'filter' => [
                                'term' => [
                                    'status' => 'active',
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            $response = $this->elasticService->getClient()->search($params);
            $allCompanies = $response['aggregations']['all_companies']['value'] ?? 0;
            $activeCompanies = $response['aggregations']['active_companies']['doc_count'] ?? 0;
        } catch (\Throwable $e) {
            // Intent: Gracefully fallback to database counts if ElasticSearch cluster or index is unavailable
            $allCompanies = DB::table('companies')->where('id', '!=', $company_id)->whereNotIn('id', $superAdminCompanyIds)->count();
            $activeCompanies = DB::table('companies')->where('id', '!=', $company_id)->whereNotIn('id', $superAdminCompanyIds)->where('status', 'active')->count();
        }

        $this->allCompanies = $allCompanies;
        $this->activeCompanies = $activeCompanies;

        return view('service_company.common.find-agency', $this->data);
    }

    /**
     * Retrieve paginated agency discovery results.
     * Delegates to FindAgencyService for CQRS execution, batch enrichment, and L3 multi-tenant caching.
     * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
     */
    public function findAgencyData(Request $request, FindAgencyService $findAgencyService)
    {
        return $findAgencyService->getAgencyData($request, $this->user);
    }

    /**
     * Authoritative mutation workflow for partner agency requests/invitations.
     * Delegates to FindAgencyService for ACID transactions, atomic idempotency, and transactional outbox publication.
     * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
     */
    public function findAgencyRequest(Request $request, $id, FindAgencyService $findAgencyService)
    {
        return $findAgencyService->handleAgencyRequest($request, $id, $this->user);
    }
}
