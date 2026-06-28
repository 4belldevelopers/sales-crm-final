<?php
/*** ########################################################
Controller    	: 	AdminCompanyController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/

namespace App\Http\Controllers\StaffingCompany;

use App\Helper\Files;
use App\Helper\Reply;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\Company\StoreCompany;
use App\Models\Job;

class StaffingCompanyCompanyController extends StaffingCompanyBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'menu.companies';
        $this->pageIcon = 'icon-film';
    }
    public function index()
    {
        abort_if(!$this->user->cans('view_company'), 403);

        $this->totalCompanies = Company::count();
        $this->activeCompanies = Company::where('status', 'active')->count();
        $this->inactiveCompanies = Company::where('status', 'inactive')->count();
        return view('staffing_company.company.index', $this->data);
    }
    public function create()
    {
        abort_if(!$this->user->cans('add_company'), 403);
        return view('staffing_company.company.create', $this->data);
    }
    public function store(StoreCompany $request)
    {
        abort_if(!$this->user->cans('add_company'), 403);

        $setting = new Company();
        $setting->company_name        = $request->input('company_name');
        $setting->company_email       = $request->input('company_email');
        $setting->company_phone       = $request->input('company_phone');
        $setting->website             = $request->input('website');
        $setting->address             = $request->input('address');
        $setting->show_in_frontend    = $request->input('show_in_frontend');
        $setting->job_opening_text    = 'Welcome!';
        $setting->job_opening_title   = 'We want people to thrive. We believe you do your best work when you feel your best.';

        if ($request->hasFile('logo')) {
            $setting->logo = Files::upload($request->logo,'company-logo');
        }

        $setting->save();

        return Reply::redirect(route('staffing_company.company.index'), __('menu.companies') . ' ' . __('messages.createdSuccessfully'));
    }
    public function show($id)
    {
        //
    }
    public function edit($id)
    {
        abort_if(!$this->user->cans('edit_company'), 403);

        $this->company = Company::find($id);
        return view('staffing_company.company.edit', $this->data);
    }
    public function update(StoreCompany $request, $id)
    {
        abort_if(!$this->user->cans('edit_company'), 403);

        $setting = Company::findOrFail($id);
        $setting->company_name        = $request->input('company_name');
        $setting->company_email       = $request->input('company_email');
        $setting->company_phone       = $request->input('company_phone');
        $setting->website             = $request->input('website');
        $setting->address             = $request->input('address');
        $setting->show_in_frontend    = $request->input('show_in_frontend');

        //update company's jobs status
        if($setting->status != $request->input('status')) {
            Job::where('company_id', $id)->update(['status' => $request->status]);
        }

        $setting->status = $request->input('status');

        if ($request->hasFile('logo')) {
            $setting->logo = Files::upload($request->logo,'company-logo');
        }

        $setting->save();

        return Reply::redirect(route('staffing_company.company.index'), __('menu.companies') . ' ' . __('messages.updatedSuccessfully'));
    }
    public function destroy($id)
    {
        abort_if(!$this->user->cans('delete_company'), 403);

        Company::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }
    public function data()
    {
        abort_if(!$this->user->cans('view_company'), 403);

        $categories = Company::all();

        return DataTables::of($categories)
            ->addColumn('action', function ($row) {
                $action = '';

                if ($this->user->cans('edit_company')) {
                    $action .= '<a href="' . route('staffing_company.company.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                      data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                }

                if ($this->user->cans('delete_company')) {
                    $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
                }
                return $action;
            })
            ->editColumn('status', function ($row) {
                if($row->status == 'active'){
                    return '<label class="badge bg-success">'.__('app.active').'</label>';
                }
                if($row->status == 'inactive'){
                    return '<label class="badge bg-danger">'.__('app.inactive').'</label>';
                }
             })
            ->editColumn('logo', function ($row) {
                return '<img src="' . $row->logo_url . '" class="img-responsive" height="25" />';
            })
            ->editColumn('company_name', function ($row) {
                return ucfirst($row->company_name);
            })
            ->addIndexColumn()
            ->rawColumns(['logo', 'action', 'status'])
            ->make(true);
    }

}
