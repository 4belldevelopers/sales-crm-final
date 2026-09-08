<?php
/*** ########################################################
Controller    	: 	AdminlocationsController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################** */

namespace App\Http\Controllers\ServiceCompany;

use App\Models\Country;
use App\Helper\Reply;
use App\Models\JobLocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Admin\Location\StoreLocation;
use App\Http\Requests\Admin\Location\UpdateLocation;
use Yajra\DataTables\Facades\DataTables;

class ServiceCompanyLocationsController extends ServiceCompanyBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'menu.locations';
        $this->pageIcon = 'icon-location-pin';
    }
    
    public function index()
    {
        abort_if(Gate::denies('view_locations'), 403);

        return view('service_company.locations.index', $this->data);
    }
   
    public function create()
    {
        abort_if(Gate::denies('add_locations'), 403);

        $this->countries = Country::all();
        return view('service_company.locations.create', $this->data);
    }
    
    public function store(StoreLocation $request)
    {
        abort_if(Gate::denies('add_locations'), 403);

        foreach ($request->locations as $location) {
            if (!is_null($location)) {
                JobLocation::create(['country_id' => $request->country_id, 'location' => $location]);
            }
        }
        
        return Reply::redirect(route('service_company.locations.index'), __('menu.locations').' '.__('messages.createdSuccessfully'));
    }

    public function data() 
	{
        abort_if(Gate::denies('view_locations'), 403);

        $categories = JobLocation::all();

        return DataTables::of($categories)
            ->addColumn('action', function ($row) {
                $action = '';

                if( $this->user->cans('edit_locations')){
                    $action.= '<a href="' . route('service_company.locations.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                      data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                }

                if( $this->user->cans('delete_locations')){
                    $action.= ' <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
                }
                return $action;
            })
            ->editColumn('location', function ($row) {
                return ucwords($row->location);
            })
            ->editColumn('country_id', function ($row) {
                return ucwords($row->country->country_name);
            })
            ->addIndexColumn()
            ->make(true);
    }
  
    public function edit($id)
    {
        abort_if(Gate::denies('edit_locations'), 403);

        $this->countries = Country::all();
        $this->location = JobLocation::withoutGlobalScopes()->find($id);
        return view('service_company.locations.edit', $this->data);
    }
   
    public function update(UpdateLocation $request, $id)
    {
        abort_if(Gate::denies('edit_locations'), 403);

        $location = JobLocation::withoutGlobalScopes()->find($id);
        $location->location = $request->location;
        $location->country_id = $request->country_id;
        $location->save();

        return Reply::redirect(route('service_company.locations.index'), __('menu.locations').' '.__('messages.updatedSuccessfully'));
    }
   
    public function destroy($id)
    {
        abort_if(Gate::denies('delete_locations'), 403);

        JobLocation::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }

}
