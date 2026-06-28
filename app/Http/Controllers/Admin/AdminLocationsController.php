<?php
/*** ########################################################
Controller    	: 	AdminlocationsController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################** */

namespace App\Http\Controllers\Admin;

use App\Models\Country;
use App\Helper\Reply;
use App\Models\JobLocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Location\StoreLocation;
use App\Http\Requests\Admin\Location\UpdateLocation;
use Yajra\DataTables\Facades\DataTables;

class AdminLocationsController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'menu.locations';
        $this->pageIcon = 'icon-location-pin';
    }
    
    public function index()
    	{
        abort_if(! $this->user->cans('view_locations'), 403);

        return view('admin.locations.index', $this->data);
    }
   
    public function create()
    	{
        abort_if(! $this->user->cans('add_locations'), 403);

        $this->countries = Country::all();
        return view('admin.locations.create', $this->data);
    }
    
    public function store(StoreLocation $request)
    	{
        abort_if(! $this->user->cans('add_locations'), 403);

        foreach ($request->locations as $location) {
            if (!is_null($location)) {
                JobLocation::create(['country_id' => $request->country_id, 'location' => $location]);
            }
        }
        
        return Reply::redirect(route('admin.locations.index'), __('menu.locations').' '.__('messages.createdSuccessfully'));
    }

    public function data() 
		{
        abort_if(! $this->user->cans('view_locations'), 403);

        $categories = JobLocation::all();

        return DataTables::of($categories)
            ->addColumn('action', function ($row) {
                $action = '';

                if( $this->user->cans('edit_locations')){
                    $action.= '<a href="' . route('admin.locations.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
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
        abort_if(! $this->user->cans('edit_locations'), 403);

        $this->countries = Country::all();
        $this->location = JobLocation::find($id);
        return view('admin.locations.edit', $this->data);
    }
   
    public function update(UpdateLocation $request, $id)
    	{
        abort_if(! $this->user->cans('edit_locations'), 403);

        $location = JobLocation::find($id);
        $location->location = $request->location;
        $location->country_id = $request->country_id;
        $location->save();

        return Reply::redirect(route('admin.locations.index'), __('menu.locations').' '.__('messages.updatedSuccessfully'));
    }
   
    public function destroy($id)
    	{
        abort_if(! $this->user->cans('delete_locations'), 403);

        JobLocation::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }

}
