<?php

/***########################################################
Controller    	: 	AdminJobCategoryController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/

namespace App\Http\Controllers\ServiceCompany;

use App\Helper\Reply;
use App\Http\Requests\StoreJobCategory;
use App\Models\JobCategory;
use App\Models\Skill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use App\Exports\CategoryExport;
use App\Services\ElasticService;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;


class ServiceCompanyJobCategoryController extends ServiceCompanyBaseController
{

	protected $mailService;
    public function __construct(ElasticService $elasticService)
    {
        parent::__construct();
        $this->pageTitle 	= 'menu.jobCategories';
        $this->pageIcon 	= 'icon-grid';
        $this->elasticService = $elasticService;
    
	}

   
    public function index()
	{
	
		abort_if(Gate::denies('view_category'), 403);		
		return view('service_company.job-category.index', $this->data);
	
	}
    
    public function create()
    {
        abort_if(Gate::denies('add_category'), 403);

        return view('service_company.job-category.create', $this->data);
    }
  
    public function store(Request $request)
    	{
        abort_if(Gate::denies('add_category'), 403);

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
                $category = JobCategory::create(['name' => $name]);
				$insertedId = $category->id;

				$response = $this->elasticService->getClient()->update([
                        'index' => ELASTIC_JOB_CATEGORIES,
                        'id'    => $insertedId,
						'retry_on_conflict' => 5,
                        'body'  => [
                            'doc' => [ 
								'id' =>  $insertedId,
								'company_id' =>(int) auth()->user()->company_id,
								'name' => $name,
                                'created_at' => now()->toIso8601String(),
                                'updated_at' => now()->toIso8601String()
							],
							'doc_as_upsert' => true,
							'detect_noop' => false
						],
                    ]);

            }
        endforeach;

        return Reply::redirect(route('service_company.job-categories.index'), __('menu.jobCategories').' '.__('messages.createdSuccessfully'));
    }
    
    public function show($id)
    	{
        //
    }
    
    public function edit($id)
    	{
        abort_if(Gate::denies('edit_category'), 403);

        $this->category = JobCategory::withoutGlobalScopes()->findOrFail($id);
        return view('service_company.job-category.edit', $this->data);
    }
   
    public function update(StoreJobCategory $request, $id)
    	{
        abort_if(Gate::denies('edit_category'), 403);

        $category = JobCategory::withoutGlobalScopes()->find($id);
        $category->name = $request->name;
        $category->save();

		$response = $this->elasticService->getClient()->update([
					'index' => ELASTIC_JOB_CATEGORIES,
					'id'    => $id,
					'retry_on_conflict' => 5,
					'body'  => [
						'doc' => [ 
							'id' =>  $id,
							'name' => $request->name,
							'updated_at' => now()->toIso8601String()
						],
						'doc_as_upsert' => true,
						'detect_noop' => false
					],
				]);






        return Reply::redirect(route('service_company.job-categories.index'), __('menu.jobCategories').' '.__('messages.updatedSuccessfully'));
    }
    
    public function destroy($id)
    {
			// abort_if(Gate::denies('delete_category'), 403);
	
			JobCategory::destroy($id);

			$this->elasticService->getClient()->deleteByQuery([
					'index' => ELASTIC_JOB_CATEGORIES,
					'conflicts' => 'proceed',   // ⭐ THIS LINE FIXES YOUR ERROR
					'body'  => [
						'query' => [
							'bool' => [
								'must' => [
									['term' => ['id' => (int) $id]]
								],
							],
						],
					],
			]);





            $company_id      	=	auth()->user()->company_id;	
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
					 						
			return Reply::dataOnly(['status' => 'success', 'html'=> $html , 'message'=>'']);  

			// return Reply::success(__('messages.recordDeleted'));
	}

    public function data() 
	{
		
        	abort_if(Gate::denies('view_category'), 403);

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
                    <a href="' . route('service_company.job-categories.edit', [$row->id]) . '" class="btn px-2 py-0 mr-3"
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
