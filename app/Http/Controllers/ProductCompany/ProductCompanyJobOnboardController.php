<?php

namespace App\Http\Controllers\ProductCompany;

use App\Models\Department;
use App\Models\Designation;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\Onboard\StoreRequest;
use App\Models\JobApplication;
use App\Notifications\JobOffer;
use App\Models\Onboard;
use App\Models\OnboardFiles;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Support\Facades\DB;

use App\Exports\JobOnBoardExport;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use App\Helper\GeneralMethod;

class ProductCompanyJobOnboardController extends ProductCompanyBaseController
{
	public function __construct()
	{
		parent::__construct();
		$this->pageTitle = __('Joining Tracker');
		$this->pageIcon = 'icon-user';
	}

	public function index(Request $request)
	{

		//echo '<pre>'; print_r($this->userPermissions); exit; 

		//abort_if(!$this->user->cans('view_job_applications'), 403);

		$company_id = auth()->user()->company_id;
		$this->appCounter = DB::table('job_applications');
		$this->appCounter = $this->appCounter->select('job_applications.id');
		$this->appCounter = $this->appCounter->join('jobs', 'jobs.id', 'job_applications.job_id');
		$this->appCounter = $this->appCounter->where('job_applications.master_status_id', '=', 6);
		$this->appCounter = $this->appCounter->where('jobs.company_id', '=', $company_id);
		$this->appCounter = $this->appCounter->where('job_applications.company_id', '=', $company_id);
		$this->appCounter = $this->appCounter->count();

		return view('product_company.job-onboard.index', $this->data);
	}

	public function data(Request $request)
	{


		abort_if(!$this->user->cans('view_joining'), 403);

		$company_id = auth()->user()->company_id;
		$currency = GeneralMethod::currency();
		$salaryMode = GeneralMethod::salaryMode();


		$chkAdminRole = $this->user->hasRole('admin');

		// $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();

		if ($chkAdminRole > 0) {
			$joiningApp = DB::table('job_applications');
			$joiningApp = $joiningApp->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
			->join('master_status', function($join){
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
			})
			->join('jobs', function($join){
				$join->on('jobs.id', 'add_job_wise_condidates.job_id');
			})
			->leftJoin('companies as uc', function($join){
				$join->on('uc.id', 'add_job_wise_condidates.company_id');
			});
			// $joiningApp = $joiningApp->join('jobs', 'jobs.id', 'job_applications.job_id');
			$joiningApp = $joiningApp->join('clients', 'clients.id', 'jobs.client_id');
			// $joiningApp = $joiningApp->join('master_status', 'master_status.id', 'job_applications.master_status_id');
			$joiningApp = $joiningApp->join('users', 'users.id', 'job_applications.submitted_by');
			// $joiningApp = $joiningApp->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id');

			$joiningApp = $joiningApp->select(
				'job_applications.id',
				'job_applications.full_name',
				'jobs.title',
				'job_applications.current_designation',
				'job_applications.ctc',
				'job_applications.ectc',
				'job_applications.phone',
				'job_applications.email',
				'add_job_wise_condidates.master_status_id',
				'add_job_wise_condidates.child_status_id',
				'offer_job_applications.offer_currency',
				'offer_job_applications.offer_amount',
				'offer_job_applications.offer_mode',
				'offer_job_applications.joined_date',
				'jobs.title',
				'clients.name as client_name',
				'users.name as posted_by',
				'users.image as posted_by_image',
				'master_status.name',
				'master_status.css_style',
				'uc.company_name as uc_company_name'
			);
		} else {
			$joiningApp = DB::table('job_applications');
			$joiningApp = $joiningApp->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
			->join('master_status', function($join){
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
			})
			->join('jobs', function($join){
				$join->on('jobs.id', 'add_job_wise_condidates.job_id');
			})
			->leftJoin('companies as uc', function($join){
				$join->on('uc.id', 'add_job_wise_condidates.company_id');
			});
			// $joiningApp = $joiningApp->join('jobs', 'jobs.id', 'job_applications.job_id');
			$joiningApp = $joiningApp->join('job_assign', 'job_assign.job_id', 'jobs.id');
			$joiningApp = $joiningApp->join('clients', 'clients.id', 'jobs.client_id');
			// $joiningApp = $joiningApp->join('master_status', 'master_status.id', 'job_applications.master_status_id');
			$joiningApp = $joiningApp->join('users', 'users.id', 'job_applications.submitted_by');
			// $joiningApp = $joiningApp->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id');
			$joiningApp = $joiningApp->select(
				'job_applications.id',
				'job_applications.full_name',
				'jobs.title',
				'job_applications.current_designation',
				'job_applications.ctc',
				'job_applications.ectc',
				'job_applications.phone',
				'job_applications.email',
				'add_job_wise_condidates.master_status_id',
				'add_job_wise_condidates.child_status_id',
				'offer_job_applications.offer_currency',
				'offer_job_applications.offer_amount',
				'offer_job_applications.offer_mode',
				'offer_job_applications.joined_date',
				'jobs.title',
				'clients.name as client_name',
				'users.name as posted_by',
				'users.image as posted_by_image',
				'master_status.name',
				'master_status.css_style',
				'uc.company_name as uc_company_name'
			);

			$joiningApp = $joiningApp->where('job_assign.user_id', $this->user->id);
		}
		$joiningApp = $joiningApp->join('offer_job_applications', 'offer_job_applications.application_id', 'job_applications.id');
		$joiningApp = $joiningApp->where('offer_job_applications.company_id', '=', $company_id);

		$joiningApp = $joiningApp->where('add_job_wise_condidates.master_status_id', 6);
		$joiningApp = $joiningApp->where('add_job_wise_condidates.company_id', $company_id);


		if (\request('search') != "") {
			$search = request('search');
			$joiningApp = $joiningApp->where('job_applications.full_name', 'LIKE', "%$search%");
		}

		return DataTables::of($joiningApp)
			->editColumn('chkbox', function ($row) {
				return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1">
                  </div>';
			})
			->editColumn('Name', function ($row) {

				$bgTextDecore = $strVal = '';
				$str = trim(@$row->full_name);
				$str = $strVal = strtolower($str[0]);
				$str = ord($str);
				if ($str >= 97 && $str <= 101) {
					$bgTextDecore = 'fs-3 bg-light-danger text-danger';
				} else if ($str >= 102 && $str <= 106) {
					$bgTextDecore = 'fs-3 bg-light-warning text-warning';
				} else if ($str >= 107 && $str <= 111) {
					$bgTextDecore = 'fs-3 bg-light-primary text-primary';
				} else if ($str >= 112 && $str <= 116) {
					$bgTextDecore = 'fs-3 bg-light-success text-success';
				} else if ($str >= 117 && $str <= 121) {
					$bgTextDecore = 'fs-3 bg-dark text-secondary';
				} else {
					$bgTextDecore = 'fs-3 bg-light-secondary text-dark';
				}

				$flag = 0;
				$duplicate = 0;
				//$duplicate				=	$row->duplicate;
				$company_id = $this->user->roles[0]->company_id;
				$app_duplicate = $this->user->company->app_duplicate;
				$app_duplicate_days = $this->user->company->app_duplicate_days;


				$flag = 0;

				$profile_sidebar_css = 'show-application-detail';



				return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
<a href="javascript:void(0);"   data-row-id="' . @$row->id . '" class="' . $profile_sidebar_css . '">
<div class="symbol-label ' . @$bgTextDecore . ' border-radius-5">' . strtoupper(@$strVal) . '</div>
</a>
</div>                  
<div class="d-flex flex-column">
<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-dark fw-bolder text-hover-primary fs-6 ' . $profile_sidebar_css . ' " data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">' . ucwords(substr(@$row->full_name, 0, 10)) . '</a>
<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-muted fw-bold d-block fs-7 text-hover-primary ' . $profile_sidebar_css . ' " data-toggle="tooltip" title="' . ucwords(@$row->current_designation) . '">' . ucwords(substr(@$row->current_designation, 0, 10)) . '</a>
</div>';
			})
			->editColumn('Contact', function ($row) {

				$bench_count = DB::table('rec_candidate_assign')
					->select('rec_candidate_assign.id')
					->join('bench_applications', 'bench_applications.id', '=', 'rec_candidate_assign.source_from_id')
					->where('rec_candidate_assign.source_application_id', '=', $row->id)
					//->where('rec_candidate_assign.source_company_id', '=', auth()->user()->company_id)
					->where('rec_candidate_assign.source_from_type', '=', 2)
					->count();

				$bench_cnt_html = '';
				if ($bench_count > 0) {

					$bench_row = DB::table('rec_candidate_assign')
						->select('rec_candidate_assign.id', 'bench_applications.id as bench_applications_id')
						->join('bench_applications', 'bench_applications.id', '=', 'rec_candidate_assign.source_from_id')
						->where('rec_candidate_assign.source_application_id', '=', $row->id)
						->where('rec_candidate_assign.source_company_id', '=', auth()->user()->company_id)
						->where('rec_candidate_assign.source_from_type', '=', 2)
						->first();

					$bench_cnt = DB::table('bench_assign')->select('users.id')
						->join('users', 'users.id', '=', 'bench_assign.user_id')->where('bench_assign.bench_applications_id', '=', $bench_row->bench_applications_id)->count();

					$bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile')
						->join('users', 'users.id', '=', 'bench_assign.user_id')
						->where('bench_assign.bench_applications_id', '=', $bench_row->bench_applications_id)->first();

					$row->phone = @$bench_assign_row->mobile;
					if (empty($row->phone)) {
						$row->phone = 'xxx-xxx-xxxx';
					}
					$row->email = @$bench_assign_row->email;
					if (empty($row->email)) {
						$row->email = 'anonymous@email.com';
					}

					if ($bench_cnt > 1) {
						$bench_cnt_html = '<div data-row-id="' . $bench_row->bench_applications_id . '" class="symbol symbol-30 symbol-circle symbol-light bench_assign_listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list.."> 
		<span class="symbol-label font-weight-bold">' . $bench_cnt . '+</span> 
	</div>';
					}


				}

				return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' . $row->phone . '">' . @$row->phone . '</a>   
                    <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary" data-toggle="tooltip" title="' . $row->email . '">' . @$row->email . $bench_cnt_html . '</a>
                  </div>';
			})
			->editColumn('Requisitions', function ($row) use ($company_id) {
				return '<div class="d-flex flex-column">
								<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . ucwords(substr($row->title, 0, 10)) . '</a>
								<span class="text-muted fw-bold text-muted d-block fs-7">' . ucwords(substr($row->client_name, 0, 10)) . '</span>
							</div>';
			})
			->editColumn('Source', function ($row) {

				$posted_by_image = '';
				$posted_by_image = url('public/avatar.png');
				if (!empty($row->posted_by_image)) {
					$posted_by_image = url('public/user-uploads/team_profile_img/' . @$row->posted_by_image);
				}

				return '<div class="d-flex flex-column">
                    <div class="symbol-group symbol-hover">
                      <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="' . @$row->posted_by . '">
                        <img alt="image" src="' . @$posted_by_image . '">
                      </div>
                  </div>
				  <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="' . @$row->uc_company_name . '">' . substr(@$row->uc_company_name, 0, 10) . '</span>
				  ';
			})
			->editColumn('Offer', function ($row) use ($currency, $salaryMode) {

				$offer_currency = '';
				if (!empty($row->offer_currency)) {
					$offer_currency = $currency[$row->offer_currency];
				}

				$offer_mode = '';
				if (!empty($row->offer_mode)) {
					$offer_mode = $salaryMode[$row->offer_mode];
				}

				return '<div class="d-flex flex-column">
                         <span class="text-dark fw-bolder fs-6">' . $offer_currency . ' ' . $row->offer_amount . ' ' . $offer_mode . '</span>
                     </div>';
			})
			->editColumn('Joining', function ($row) {

				$joined_date = $joined_time = $date = '';
				$date = @$row->joined_date;
				if (!empty($date)) {
					$joined_date = date("d-M-y", strtotime($date));
					$joined_time = date("H:i a", strtotime($date));
				}
				return '<div class="d-flex flex-column">
                         <span class="text-dark fw-bolder fs-6">' . $joined_date . '</span>
                         <span class="text-muted fw-bold d-block fs-7">' . $joined_time . '</span>
                     </div>';
			})
			->editColumn('Stage1', function ($row) {
				return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1">
                  </div>';
			})
			->editColumn('Stage', function ($row) {
				$name = '--------';
				if ($row->master_status_id == 8) {
					$name = '';
				}
				if (!empty(@$row->child_status_id) && @$row->child_status_id > 0) {
					$childStatus = '';
					$childStatus = DB::table('candidate_status')->select('*')->where('id', $row->child_status_id)->first();
					if (!empty($childStatus)) {
						$name = @$childStatus->name;
					}
				}
				return '<div class="d-flex flex-column">
							<a href="javascript:void(0);" data-row-id="' . @$row->id . '" class="d-flex flex-column" data-toggle="modal" ">
								<span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1">' . ucwords(substr($row->name, 0, 10)) . '</span>
								<span class="text-muted fw-bold text-muted d-block fs-7">' . @$name . '</span>
							</a>
						</div>';
			})
			->rawColumns(['chkbox', 'Name', 'Contact', 'Requisitions', 'Source', 'Offer', 'Joining', 'Stage'])
			->addIndexColumn()
			->make(true);
	}

	public function create(Request $request)
	{
		abort_if(!$this->user->cans('add_job_applications'), 403);

		$this->application = JobApplication::with(['job', 'status'])->findOrFail($request->id);

		abort_if($this->application->status->status != 'hired', 403);

		//$this->users        = User::all();
		$this->users = User::where('company_id', '=', $this->application->company_id)->where('type', '!=', 1)->get();
		$this->departments = Department::all();
		$this->designations = Designation::all();

		return view('product_company.job-onboard.create', $this->data);
	}

	public function store(StoreRequest $request)
	{
		// Save On Board Details
		$onBoard = new Onboard();
		$onBoard->job_application_id = $request->candidate_id;
		$onBoard->department_id = $request->department;
		$onBoard->designation_id = $request->designation;
		$onBoard->salary_offered = $request->salary;
		$onBoard->joining_date = $request->join_date;
		$onBoard->reports_to_id = $request->report_to;
		$onBoard->employee_status = $request->employee_status;
		$onBoard->accept_last_date = $request->last_date;
		$onBoard->offer_code = Str::random(18);
		$onBoard->save();

		$names = $request->name;
		$files = $request->file;

		foreach ($names as $key => $name):
			// Files store in directory
			$fileName = Files::upload($files[$key], 'onboard-files', null, null, false);

			// Files save
			OnboardFiles::create(
				[
					'on_board_detail_id' => $onBoard->id,
					'name' => $name,
					'file' => $files[$key]->getClientOriginalName(),
					'hash_name' => $fileName
				]
			);
		endforeach;

		// Send Offer email
		if ($request->has('send_email')) {
			$this->sendOffer($request->candidate_id);
		}

		return Reply::redirect(route('product_company.job-onboard.index'), __('app.onBoard') . ' ' . __('messages.createdSuccessfully'));
	}

	public function edit($id)
	{
		abort_if(!$this->user->cans('add_job_applications'), 403);

		// images format with icon
		$this->imageExt = [
			'png' => 'fa-file-image-o',
			'jpe' => 'fa-file-image-o',
			'jpeg' => 'fa-file-image-o',
			'jpg' => 'fa-file-image-o',
			'gif' => 'fa-file-image-o',
			'bmp' => 'fa-file-image-o',
			'ico' => 'fa-file-image-o',
			'tiff' => 'fa-file-image-o',
			'tif' => 'fa-file-image-o',
			'svg' => 'fa-file-image-o',
			'svgz' => 'fa-file-image-o',
		];

		// adobe and ms office files format with icon
		$this->fileExt = [
			// adobe
			'pdf' => 'fa-file-pdf-o',
			'psd' => 'fa-file-image-o',
			'ai' => 'fa-file-o',
			'eps' => 'fa-file-o',
			'ps' => 'fa-file-o',
			// ms office
			'doc' => 'fa-file-text',
			'rtf' => 'fa-file-text',
			'xls' => 'fa-file-excel-o',
			'ppt' => 'fa-file-powerpoint-o',
			'docx' => 'fa-file-text',
			'xlsx' => 'fa-file-excel-o',
			'pptx' => 'fa-file-powerpoint-o',
			// open office
			'odt' => 'fa-file-text',
			'ods' => 'fa-file-text',
		];

		$this->onboard = Onboard::with(['reportto', 'files'])->findOrFail($id);
		$this->application = JobApplication::with(['job', 'status'])->findOrFail($this->onboard->job_application_id);
		$this->users = User::all();
		$this->departments = Department::all();
		$this->designations = Designation::all();

		return view('product_company.job-onboard.edit', $this->data);
	}

	public function update(StoreRequest $request, $id)
	{
		// update On Board Details
		$onBoard = Onboard::findOrFail($id);
		$onBoard->job_application_id = $request->candidate_id;
		$onBoard->department_id = $request->department;
		$onBoard->designation_id = $request->designation;
		$onBoard->salary_offered = $request->salary;
		$onBoard->joining_date = $request->join_date;
		$onBoard->reports_to_id = $request->report_to;
		$onBoard->employee_status = $request->employee_status;
		$onBoard->accept_last_date = $request->last_date;
		$onBoard->hired_status = $request->status;

		if ($onBoard->hired_status == 'rejected' || $onBoard->hired_status == 'canceled') {
			$onBoard->hired_status = $request->reason;
		}
		$onBoard->save();

		$names = $request->name;
		$files = $request->file;


		if (!empty($names)) {
			foreach ($names as $key => $name):
				if (!empty($files[$key])) {
					$fileName = Files::upload($files[$key], 'onboard-files', null, null, false);
					OnboardFiles::create(
						[
							'on_board_detail_id' => $onBoard->id,
							'name' => $name,
							'file' => $files[$key]->getClientOriginalName(),
							'hash_name' => $fileName
						]
					);
				}

			endforeach;
		}
		return Reply::redirect(route('product_company.job-onboard.index'), __('app.onBoard') . ' ' . __('messages.updatedSuccessfully'));

	}

	public function destroy($id)
	{
		$onBoardFiles = OnboardFiles::findOrFail($id);
		File::delete('user-uploads/onboard-files/' . $onBoardFiles->hashName);

		OnboardFiles::destroy($id);
		return Reply::success('messages.recordDeleted');
	}

	public function show($id)
	{
		// images format with icon
		$this->imageExt = [
			'png' => 'fa-file-image-o',
			'jpe' => 'fa-file-image-o',
			'jpeg' => 'fa-file-image-o',
			'jpg' => 'fa-file-image-o',
			'gif' => 'fa-file-image-o',
			'bmp' => 'fa-file-image-o',
			'ico' => 'fa-file-image-o',
			'tiff' => 'fa-file-image-o',
			'tif' => 'fa-file-image-o',
			'svg' => 'fa-file-image-o',
			'svgz' => 'fa-file-image-o',
		];

		// adobe and ms office files format with icon
		$this->fileExt = [
			// adobe
			'pdf' => 'fa-file-pdf-o',
			'psd' => 'fa-file-image-o',
			'ai' => 'fa-file-o',
			'eps' => 'fa-file-o',
			'ps' => 'fa-file-o',
			// ms office
			'doc' => 'fa-file-text',
			'rtf' => 'fa-file-text',
			'xls' => 'fa-file-excel-o',
			'ppt' => 'fa-file-powerpoint-o',
			'docx' => 'fa-file-text',
			'xlsx' => 'fa-file-excel-o',
			'pptx' => 'fa-file-powerpoint-o',
			// open office
			'odt' => 'fa-file-text',
			'ods' => 'fa-file-text',
		];
		$this->onboard = Onboard::with(['reportto', 'files'])->findOrFail($id);
		$this->application = JobApplication::with(['job', 'status'])->findOrFail($this->onboard->job_application_id);
		return view('product_company.job-onboard.show', $this->data);
	}

	public function sendOffer($userID)
	{

		$jobApplication = JobApplication::with(['onboard', 'job'])->findOrFail($userID);
		// Send Email Or Sms to applicant.
		Notification::send($jobApplication, new JobOffer($jobApplication));
	}

	public function updateStatus($id)
	{

		$onboard = Onboard::findOrFail($id);
		$onboard->hired_status = 'canceled';
		$onboard->save();

		return Reply::success(__('messages.offer.updatedSuccessfully'));
	}

	public function loadVisibilityControl($id)
	{
		return view('product_company.job-onboard.load_visibility_control', $this->data)->render();
	}

	public function export($start, $length)
	{
		$filters = [
			'start' => $start,
			'length' => $length,
			'loginUserId' => $this->user->id,
			'loginUserRole' => $this->user->role->role_id
		];

		$data = [
			'company' => $this->companyName
		];

		return Excel::download(new JobOnBoardExport($filters, $data), 'jobOnBoard.xlsx', ExcelExcel::XLSX);
	}
}
