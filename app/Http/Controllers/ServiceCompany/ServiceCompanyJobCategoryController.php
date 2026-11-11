<?php

/***########################################################
Controller    	: 	AdminJobCategoryController
Purmose			:
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021
########################################################***/

namespace App\Http\Controllers\ServiceCompany;

use App\Exports\CategoryExport;
use App\Helper\Reply;
use App\Http\Requests\StoreJobCategory;
use App\Models\JobCategory;
use App\Models\Skill;
use App\Services\ElasticService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ServiceCompanyJobCategoryController extends ServiceCompanyBaseController
{
    protected $mailService;

    public function __construct(ElasticService $elasticService)
    {
        parent::__construct();
        $this->pageTitle = 'menu.jobCategories';
        $this->pageIcon = 'icon-grid';
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

        foreach ($names as $key => $name) {
            if (! is_null($name)) {
                $category = JobCategory::create(['name' => $name]);
                $insertedId = $category->id;

                $response = $this->elasticService->getClient()->update([
                    'index' => ELASTIC_JOB_CATEGORIES,
                    'id' => $insertedId,
                    'retry_on_conflict' => 5,
                    'body' => [
                        'doc' => [
                            'id' => $insertedId,
                            'company_id' => (int) auth()->user()->company_id,
                            'name' => $name,
                            'created_at' => now()->toIso8601String(),
                            'updated_at' => now()->toIso8601String(),
                        ],
                        'doc_as_upsert' => true,
                        'detect_noop' => false,
                    ],
                ]);

            }
        }

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
            'id' => $id,
            'retry_on_conflict' => 5,
            'body' => [
                'doc' => [
                    'id' => $id,
                    'name' => $request->name,
                    'updated_at' => now()->toIso8601String(),
                ],
                'doc_as_upsert' => true,
                'detect_noop' => false,
            ],
        ]);

        return Reply::redirect(route('service_company.job-categories.index'), __('menu.jobCategories').' '.__('messages.updatedSuccessfully'));
    }

    public function destroy($id)
    {
        // abort_if(Gate::denies('delete_category'), 403);
        $companyId = (int) (company()->id ?? (auth()->user()?->company_id ?? 0));

        // Enterprise Data Integrity: Prevent deleting department if active jobs are assigned to it
        $jobsAssignedCount = DB::table('jobs')
            ->where('company_id', $companyId)
            ->where('category_id', $id)
            ->count();

        if ($jobsAssignedCount > 0) {
            return Reply::dataOnly([
                'status' => 'error',
                'message' => "Cannot delete department: {$jobsAssignedCount} job(s) in your organization are currently assigned to it. Please reassign the jobs first.",
            ]);
        }

        $category = JobCategory::withoutGlobalScopes()->where('company_id', $companyId)->find($id);
        if (! $category) {
            return Reply::dataOnly([
                'status' => 'error',
                'message' => 'Department not found or belongs to standard global library.',
            ]);
        }

        $category->delete();

        $this->elasticService->getClient()->deleteByQuery([
            'index' => ELASTIC_JOB_CATEGORIES,
            'conflicts' => 'proceed',   // ⭐ THIS LINE FIXES YOUR ERROR
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['id' => (int) $id]],
                        ],
                    ],
                ],
            ],
        ]);

        $company_id = auth()->user()->company_id;
        $departmentArr = DB::table('job_categories')->select('*')->where('company_id', $company_id)->orderBy('job_categories.name', 'ASC')->get();
        $html = view('service_company.account.components.departments-list', ['departmentArr' => $departmentArr])->render();

        return Reply::dataOnly(['status' => 'success', 'html' => $html, 'message' => '']);

        // return Reply::success(__('messages.recordDeleted'));
    }

    public function data()
    {

        abort_if(Gate::denies('view_category'), 403);

        // $categories = JobCategory::all();

        $categories = JobCategory::select('job_categories.id', 'job_categories.name');
        $categories = $categories->where('job_categories.company_id', $this->user->company_id);
        if (\request('search') != '') {
            $search = request('search');
            $categories = $categories->where('job_categories.name', 'LIKE', "%$search%");
        }

        $categories = $categories->groupBy('job_categories.id');
        $categories = $categories->orderBy('job_categories.id', 'DESC');

        return DataTables::of($categories)
            ->addColumn('action', function ($row) {
                $action = '';

                if ($this->user->cans('edit_category')) {
                    $action .= '
                    <a href="'.route('service_company.job-categories.edit', [$row->id]).'" class="btn px-2 py-0 mr-3"
                      data-toggle="tooltip" data-original-title="'.__('app.edit').'">
                        <i class="fa fa-pencil text-primary" aria-hidden="true"></i>
                    </a>';
                }

                if ($this->user->cans('delete_category')) {
                    $action .= '
                    <a href="javascript:;" class="btn sa-params px-2 py-0"
                      data-toggle="tooltip" data-row-id="'.$row->id.'" data-original-title="'.__('app.delete').'">
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

        foreach ($skills as $skill) {
            $jobSkills .= '<option selected value="'.$skill->id.'">'.ucwords($skill->name).'</option>';
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $jobSkills]);
    }

    public function export($start, $length)
    {

        $filters = [
            'start' => $start,
            'length' => $length,
            'loginUserId' => $this->user->id,
            'loginUserRole' => $this->user->role->role_id,
            'company_id' => auth()->user()->company_id,
        ];
        $data = ['company' => $this->user->name];

        return Excel::download(new CategoryExport($filters, $data), 'CategoriesExport.xlsx', ExcelExcel::XLSX);

    }
}
