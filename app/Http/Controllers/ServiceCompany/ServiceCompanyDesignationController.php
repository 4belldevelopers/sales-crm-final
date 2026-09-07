<?php
/*** ########################################################
Controller    	: 	AdminDesignationController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################** */

namespace App\Http\Controllers\ServiceCompany;

use App\Models\Designation;
use App\Helper\Reply;
use App\Http\Requests\Designation\StoreRequest;
use Illuminate\Support\Facades\Gate;


class ServiceCompanyDesignationController extends ServiceCompanyBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.designation';
        $this->pageIcon = 'icon-user';
    }
   
    public function index()
    {
        //
    }

    public function create()
    {
        abort_if(Gate::denies('add_job_applications'), 403);

        $this->designations = Designation::all();
        return view('service_company.designation.create', $this->data);
    }

    public function edit($id)
    {
       //
    }

    public function store(StoreRequest $request)
    {
        $designation = new Designation;
        $designation->name = $request->name;
        $designation->save();

        $designationData = Designation::all();
        return Reply::successWithData(__('messages.designationAdded'),['data' => $designationData]);
    }

    public function update(StoreRequest $request, $id)
    	{
        $designation = Designation::withoutGlobalScopes()->findOrFail($id);
        $designation->name = $request->name;
        $designation->save();

        $designationData = Designation::all();
        return Reply::successWithData(__('messages.designationUpdated'),['data' => $designationData]);
    }

    public function destroy($id)
    	{
        Designation::destroy($id);

        $designationData = Designation::all();
        return Reply::successWithData(__('messages.designationDeleted'),['data' => $designationData]);
    }

    public function show($id)
    {
        //
    }
    
}
