<?php

/*** ########################################################
Controller    	: 	AdminSkillsController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################** */

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Requests\StoreSkill;
use App\Models\JobCategory;
use App\Models\Skill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Support\Facades\DB;

class AdminSkillsController extends AdminBaseController 
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'menu.skills';
        $this->pageIcon = 'icon-grid';
    }


    public function index()
   		 {
        abort_if(! $this->user->cans('view_skills'), 403);

        return view('admin.skills.index', $this->data);
    }
   
    public function create()
    	{
        abort_if(! $this->user->cans('add_skills'), 403);

        $this->categories = JobCategory::all();
        return view('admin.skills.create', $this->data);
    }
  
    public function store(Request $request)
    	{
        abort_if(! $this->user->cans('add_skills'), 403);

        $names = $request->name;
        //$categoryId = $request->category_id;

        if (trim($names[0]) == '') {
            return Reply::error(__('errors.addSkills'));
        }

        foreach ($names as $name) {
            if (is_null($name)) {
                return Reply::error(__('errors.addSkills'));
            }
        }

        foreach ($names as $key => $name):
            if(!is_null($name)){
                Skill::create(['name' => $name ]);
            }
        endforeach;

        return Reply::redirect(route('admin.skills.index'), __('menu.skills').' '.__('messages.createdSuccessfully'));
    }
  
    public function show($id)
    {
        //
    }
   
    public function edit($id)
    	{
        abort_if(! $this->user->cans('edit_skills'), 403);

        $this->categories = JobCategory::all();
        $this->skill = Skill::find($id);
        return view('admin.skills.edit', $this->data);
    }
   
    public function update(Request $request, $id)
    	{
        abort_if(! $this->user->cans('edit_skills'), 403);

        $skill = Skill::find($id);
        $skill->name = $request->name;
        //$skill->category_id = $request->category_id;
        $skill->save();

        return Reply::redirect(route('admin.skills.index'), __('menu.skills').' '.__('messages.updatedSuccessfully'));
    }
   
    public function destroy($id)
		{
        abort_if(! $this->user->cans('delete_skills'), 403);

        Skill::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }

    public function data() 
		{
		
			abort_if(! $this->user->cans('view_skills'), 403);
	
			//$categories 	= Skill::all();
			
			$categories 				= Skill::select('skills.id', 'skills.name', 'skills.category_id', 'job_categories.name as cat_name' );
			$categories 				= $categories->leftjoin('job_categories', 'job_categories.id', 'skills.category_id');
			
			if (\request('search') != "")
			{
				$search					=	request('search');	
				$categories				=	$categories->where('skills.name', 'LIKE', "%$search%");
			}  
			  
			$categories 				= $categories->groupBy('skills.id');		
			$categories 				= $categories->orderBy('skills.id', 'DESC');



        return DataTables::of($categories)
            ->addColumn('action', function ($row) {
                $action = '';

                if( $this->user->cans('edit_skills'))
                {
                    $action.= '
                    <a href="'. route('admin.skills.edit', [$row->id]) . '"class="btn"
                      data-toggle="tooltip" data-original-title="'.__('app.edit').'">
                        <i class="fa fa-pencil text-primary"></i>
                    </a>';
                }   

                if( $this->user->cans('delete_skills'))
                {
                    $action.= '
                    <a href="javascript:void();" class="btn sa-params" data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'">
                        <i class="fa fa-times text-danger"></i>
                    </a>';
                }
                return $action;
            })
            ->editColumn('name', function ($row) {
                return ucfirst($row->name);
            })
			
			 ->editColumn('category_id', function ($row) {
                return ucfirst($row->cat_name);
            })
			
           /* ->editColumn('category_id', function ($row) {
                return ucfirst($row->category->name);
            })*/
			
            ->addIndexColumn()
            ->make(true);
    }	
	
	public function addMoreSkill($id)
		{
			//echo $id; exit; 
			//$this->client 		= Client::find($client_id);
			return view('admin.skills.lightbox', $this->data)->render();
	}
	
	public function saveMoreSkills(Request $request)
		{

		$name 			= '';
		$name 			= $request->name;
		
		if($name !='' )
		{
				
				$skillCounter 		= DB::table('skills')->select('*')->where('company_id', $this->user->company_id)->where('name', $name)->count();
				
				if($skillCounter == 0)
				{
						$insertArr 		= array('company_id' => $this->user->company_id, 'name' => $name);
						
						DB::table('skills')->insert($insertArr); 
						$insertedId	= DB::getPdo()->lastInsertId();
						if($insertedId > 0)
						{
							$option 	= array('id' => $insertedId , 'name' => ucwords($name));
							return Reply::dataOnly(['status' => 'success', 'message' => "Skills added successfully.", 'option'=> $option ]);		
						}
						else
						{
							return Reply::dataOnly(['status' => 'error', 'message' => "Please enter skills name."]);
						}
				}
				else
				{
					return Reply::dataOnly(['status' => 'error', 'message' => "Already exists job skills."]);	
				}			
		}
		else
		{
			return Reply::dataOnly(['status' => 'error', 'message' => "Please enter skills name."]);
		}
		
		
	}
	
	

}
