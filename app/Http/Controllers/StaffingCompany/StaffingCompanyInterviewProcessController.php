<?php
/*** ########################################################
Controller    	: 	AdminInterviewProcessController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/

namespace App\Http\Controllers\StaffingCompany;

use App\Helper\Reply;
use App\Http\Requests\StoreSkill;
use App\Http\Requests\StoreProcess;


use App\Models\JobCategory;
use App\Models\Skill;
use App\Models\InterviewProcess;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class StaffingCompanyInterviewProcessController extends StaffingCompanyBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('Interview Process');
        $this->pageIcon = 'icon-grid';
    }   
    public function index()
    {
	
		//echo '<pre>'; print_r($this->user); exit; 
		
        abort_if(! $this->user->can('view_skills'), 403);

        return view('staffing_company.interview-process.index', $this->data);
    }
    public function create()
   	{
        abort_if(! $this->user->can('add_skills'), 403);

        //$this->categories = JobCategory::all();
        return view('staffing_company.interview-process.create', $this->data);
    }
    public function store(StoreProcess $request)
    {
        abort_if(! $this->user->can('add_skills'), 403);

        $names = $request->name;
        //$categoryId = $request->category_id;

        if (trim($names[0]) == '') {
            return Reply::error(__('Interview Process can not be blank'));
        }

        foreach ($names as $name) {
            if (is_null($name)) {
                return Reply::error(__('Interview process can not be blank'));
            }
        }

        foreach ($names as $key => $name):
            if(!is_null($name)){
                InterviewProcess::create(['name' => $name, 'company_id' => auth()->user()->company_id, 'user_id' => auth()->user()->id ]);
            }
        endforeach;
		
		//echo '<pre>'; print_r(auth()->user()); exit;  company_id  id

        return Reply::redirect(route('staffing_company.interview-process.index'), __('menu.skills').' '.__('messages.createdSuccessfully'));
    }
    public function show($id)
    {
        //
    }
    public function edit($id)
    {
        abort_if(! $this->user->can('edit_skills'), 403);

       // $this->categories = JobCategory::all();
        $this->InterviewProcess 	= InterviewProcess::find($id);
        return view('staffing_company.interview-process.edit', $this->data);
    }
    public function update(Request $request, $id)
    {
       // abort_if(! $this->user->can('edit_skills'), 403);

        $row 				= InterviewProcess::find($id);
        $row->name 			= $request->name;
		//$row->status 		= $request->status;
        //$skill->category_id = $request->category_id;
        $row->save();

        return Reply::redirect(route('staffing_company.interview-process.index'), __('menu.skills').' '.__('messages.updatedSuccessfully'));
    }
    public function destroy($id)
    {
        abort_if(! $this->user->can('delete_skills'), 403);

        InterviewProcess::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }
    public function data() 
	{
        abort_if(! $this->user->can('view_skills'), 403);

        $categories = InterviewProcess::all();
		//$categories = $categories->orderBy('interview_processes.name', 'ASC');

        return DataTables::of($categories)
            ->addColumn('action', function ($row) {
                $action = '';

                if( $this->user->can('edit_skills')){
                    $action.= '<a href="' . route('staffing_company.interview-process.edit', [$row->id]) . '" class="btn btn-primary btn-circle" data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="flaticon2-pen text-primary" aria-hidden="true"></i></a>';
                }

                if( $this->user->can('delete_skills')){
                    $action.= ' <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times fa-sm" aria-hidden="true"></i></a>';
                }
                return $action;
            })
           
		    ->editColumn('name', function ($row) {
                return ucfirst($row->name);
            })
			
			/* ->editColumn('status', function ($row) {
				if ($row->status == 'Y') {
                    return '<label class="badge bg-success">' . __('app.active') . '</label>';
                }
				else
               {
                    return '<label class="badge bg-danger">' . __('app.inactive') . '</label>';
                }
            })*/
			
			 
				
           /* ->editColumn('category_id', function ($row) {
                return ucfirst($row->category->name);
            })*/
			->rawColumns(['title',  'action'])
            ->addIndexColumn()
            ->make(true);
    }


}
