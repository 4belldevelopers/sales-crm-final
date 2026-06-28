<?php

/***########################################################
Controller    	: 	AdminJobCategoryController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/ 

namespace App\Http\Controllers\ProductCompany;

use App\Helper\Reply;
use App\Http\Requests\StoreJobCategory;
use App\Models\JobCategory;
use App\Models\Skill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;


use App\Exports\CategoryExport;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;


class ProductCompanyJobCategoryController extends ProductCompanyBaseController
{

    public function __construct()
    {
	
        parent::__construct();
        $this->pageTitle 	= 'menu.jobCategories';
        $this->pageIcon 	= 'icon-grid';
    
	}

   
    public function index()
	{
	
		abort_if(! $this->user->cans('view_category'), 403);		
		return view('product_company.job-category.index', $this->data);
	
	}
    
    public function create()
    	{
        abort_if(! $this->user->cans('add_category'), 403);

        return view('product_company.job-category.create', $this->data);
    }
  
    public function store(Request $request)
    	{
        abort_if(! $this->user->cans('add_category'), 403);

        $names = $request->name;

        if (trim($names[0]) == '') {
            return Reply::error(__('errors.addCategory'));
        }

        foreach ($names as $name) {
            if (is_null($name)) {
                return Reply::error(__('errors.addCategory'));
            }
        }

        foreach ($names as $key => $name):
            if(!is_null($name)){
                JobCategory::create(['name' => $name]);
            }
        endforeach;

        return Reply::redirect(route('product_company.job-categories.index'), __('menu.jobCategories').' '.__('messages.createdSuccessfully'));
    }
    
    public function show($id)
    	{
        //
    }
    
    public function edit($id)
    	{
        abort_if(! $this->user->cans('edit_category'), 403);

        $this->category = JobCategory::findOrFail($id);
        return view('product_company.job-category.edit', $this->data);
    }
   
    public function update(StoreJobCategory $request, $id)
    	{
        abort_if(! $this->user->cans('edit_category'), 403);

        $category = JobCategory::find($id);
        $category->name = $request->name;
        $category->save();

        return Reply::redirect(route('product_company.job-categories.index'), __('menu.jobCategories').' '.__('messages.updatedSuccessfully'));
    }
    
    public function destroy($id)
    	{
        abort_if(! $this->user->cans('delete_category'), 403);

        JobCategory::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }

    public function data() 
		{
		
        	abort_if(! $this->user->cans('view_category'), 403);

        	//$categories = JobCategory::all();
			
			$categories 	= JobCategory::select('job_categories.id', 'job_categories.name');
			$categories 	= $categories->where('job_categories.company_id', $this->user->company_id);
			if (\request('search') != "")
			{
				$search			=	request('search');	
				$categories		=	$categories->where('job_categories.name', 'LIKE', "%$search%");  
			}
		 
		 	$categories 		= $categories->groupBy('job_categories.id');		
			$categories 		= $categories->orderBy('job_categories.id', 'DESC');
		 

        return DataTables::of($categories)
            ->addColumn('action', function ($row) {
                $action = '';

                if( $this->user->cans('edit_category')){
                    $action.= '
                    <a href="' . route('product_company.job-categories.edit', [$row->id]) . '" class="btn px-2 py-0 mr-3"
                      data-toggle="tooltip" data-original-title="'.__('app.edit').'">
                        <i class="fa fa-pencil text-primary" aria-hidden="true"></i>
                    </a>';
                }

                if( $this->user->cans('delete_category')){
                    $action.= '
                    <a href="javascript:;" class="btn sa-params px-2 py-0"
                      data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'">
                      <i class="fa fa-times text-danger" aria-hidden="true"></i>
                    </a>';
                }
                return $action;
            })
            ->editColumn('name', function ($row) {
                return ucfirst($row->name);
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function getSkills($categoryId)
		{
        $jobSkills = '';

        $skills = Skill::where('category_id', $categoryId)->get();

        foreach($skills as $skill){
            $jobSkills.= '<option selected value="'.$skill->id.'">'.ucwords($skill->name).'</option>';
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $jobSkills]);
    }
	
	public function export($start, $length)
    {

        $filters 	= [
						'start' 			=>	$start,
						'length' 			=>	$length,
						'loginUserId'		=>	$this->user->id,
						'loginUserRole'		=>	$this->user->role->role_id,
						'company_id'		=>	auth()->user()->company_id
					 ];
        $data 		= ['company' => $this->user->name ];

       	return Excel::download(new CategoryExport($filters, $data), 'CategoriesExport.xlsx', ExcelExcel::XLSX); 

	}




}
