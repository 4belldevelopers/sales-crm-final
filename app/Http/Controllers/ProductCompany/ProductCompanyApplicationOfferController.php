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

use App\Exports\ApplicationOfferExport;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use App\Helper\GeneralMethod;
use App\OfferJobApplication;
use Carbon\Carbon;

class ProductCompanyApplicationOfferController extends ProductCompanyBaseController
{
	public function __construct()
	{
		parent::__construct();
		$this->pageTitle = __('Offer Tracker');
		$this->pageIcon = 'icon-user';
	}

	public function index(Request $request)
	{

		abort_if(!$this->user->cans('view_offer'), 403);
		$company_id = auth()->user()->company_id;

		$this->appCounter = DB::table('job_applications');
		$this->appCounter = $this->appCounter->select('job_applications.id');
		$this->appCounter = $this->appCounter->where('job_applications.master_status_id', '=', 5);
		$this->appCounter = $this->appCounter->where('job_applications.company_id', '=', $company_id);
		$this->appCounter = $this->appCounter->count();

		return view('product_company.applicationoffer.index', $this->data);
	}

	public function data(Request $request)
	{
		abort_if(!$this->user->cans('view_offer'), 403);

		$company_id = auth()->user()->company_id;

		$currency = GeneralMethod::currency();
		$salaryMode = GeneralMethod::salaryMode();

		$chkAdminRole = $this->user->hasRole('admin');

		// $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
		if ($chkAdminRole > 0) {
			$applicationOffer = DB::table('job_applications');
			$applicationOffer = $applicationOffer->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id');
			$applicationOffer = $applicationOffer->join('master_status', function($join) use ($company_id){
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id')
				->where('add_job_wise_condidates.company_id', $company_id);
			});
			$applicationOffer = $applicationOffer->join('jobs', function($join) use ($company_id){
				$join->on('jobs.id', 'add_job_wise_condidates.job_id')
				->where('add_job_wise_condidates.company_id', $company_id);
			});
			// $applicationOffer = $applicationOffer->join('jobs', 'jobs.id', 'job_applications.job_id');
			$applicationOffer = $applicationOffer->join('clients', 'clients.id', 'jobs.client_id');
			// $applicationOffer = $applicationOffer->join('master_status', 'master_status.id', 'job_applications.master_status_id');
			$applicationOffer = $applicationOffer->join('offer_job_applications', 'offer_job_applications.application_id', 'job_applications.id');
			$applicationOffer = $applicationOffer->join('users', 'users.id', 'job_applications.submitted_by');
			$applicationOffer = $applicationOffer->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id');
			
			$applicationOffer = $applicationOffer->select(
				'job_applications.id',
				'job_applications.full_name',
				'job_applications.current_designation',
				'job_applications.ctc',
				'job_applications.ectc',
				'job_applications.proposed_ctc',
				'offer_job_applications.any_offer',
				'job_applications.document',
				'job_applications.sub_for_approval',
				'offer_job_applications.offer_approved',
				'offer_job_applications.offer_release',
				'offer_job_applications.offer_acceptance',
				'offer_job_applications.joined',
				'offer_job_applications.joined_date',
				'add_job_wise_condidates.job_id',
				'job_applications.phone',
				'job_applications.email',
				'offer_job_applications.offer_currency',
				'offer_job_applications.offer_mode',
				'offer_job_applications.offer_amount',
				'add_job_wise_condidates.master_status_id',
				'add_job_wise_condidates.child_status_id',
				'jobs.title',
				'clients.name as client_name',
				'users.name as posted_by',
				'users.image as posted_by_image',
				'master_status.name',
				'master_status.css_style',
				'uc.company_name as uc_company_name',
				'offer_job_applications.id as offer_id'
			);
		} else {

			$applicationOffer = DB::table('job_applications');
			$applicationOffer = $applicationOffer->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id');
			$applicationOffer = $applicationOffer->join('master_status', function($join) {
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
			});
			$applicationOffer = $applicationOffer->join('jobs', function($join){
				$join->on('jobs.id', 'add_job_wise_condidates.job_id');
			});
			// $applicationOffer = $applicationOffer->join('jobs', 'jobs.id', 'job_applications.job_id');
			$applicationOffer = $applicationOffer->join('job_assign', 'job_assign.job_id', 'jobs.id');
			$applicationOffer = $applicationOffer->join('clients', 'clients.id', 'jobs.client_id');
			// $applicationOffer = $applicationOffer->join('master_status', 'master_status.id', 'job_applications.master_status_id');
			$applicationOffer = $applicationOffer->join('users', 'users.id', 'job_applications.submitted_by');
			$applicationOffer = $applicationOffer->join('offer_job_applications', 'offer_job_applications.application_id', 'job_applications.id');
			$applicationOffer = $applicationOffer->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id');
			$applicationOffer = $applicationOffer->select(
				'job_applications.id',
				'job_applications.full_name',
				'job_applications.current_designation',
				'job_applications.ctc',
				'job_applications.ectc',
				'job_applications.proposed_ctc',
				'offer_job_applications.any_offer',
				'job_applications.document',
				'job_applications.sub_for_approval',
				'offer_job_applications.offer_approved',
				'offer_job_applications.offer_release',
				'offer_job_applications.offer_acceptance',
				'offer_job_applications.joined',
				'offer_job_applications.joined_date',
				'add_job_wise_condidates.job_id',
				'job_applications.phone',
				'job_applications.email',
				'offer_job_applications.offer_currency',
				'offer_job_applications.offer_mode',
				'offer_job_applications.offer_amount',
				'add_job_wise_condidates.master_status_id',
				'add_job_wise_condidates.child_status_id',
				'jobs.title',
				'clients.name as client_name',
				'users.name as posted_by',
				'users.image as posted_by_image',
				'master_status.name',
				'master_status.css_style',
				'uc.company_name as uc_company_name',
				'offer_job_applications.id as offer_id'
			);
			$applicationOffer = $applicationOffer->where('job_assign.user_id', '=', $this->user->id);
		}

		$applicationOffer = $applicationOffer->where('add_job_wise_condidates.master_status_id', 5);
		$applicationOffer = $applicationOffer->where('add_job_wise_condidates.company_id', $company_id);
		$applicationOffer = $applicationOffer->groupBy('add_job_wise_condidates.id');

		$applicationOffer = $applicationOffer->orderBy('offer_job_applications.id', 'DESC');

		if (\request('search') != "") {
			$search = request('search');
			$applicationOffer = $applicationOffer->where('job_applications.full_name', 'LIKE', "%$search%");
		}


		//echo $applicationOffer->tosql();
		// dd($applicationOffer->get());

		return DataTables::of($applicationOffer)

			->editColumn('chkbox', function ($row) {
				return '<div class="form-check form-check-sm form-check-custom form-check-solid">
							<input class="form-check-input" type="checkbox" value="1" />
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
				return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
						<a href="javascript:void(0);"   data-row-id="' . @$row->id . '" class="show-application-detail">
						<div class="symbol-label ' . @$bgTextDecore . ' border-radius-5">' . strtoupper(@$strVal) . '</div>
						</a>
					</div>                  
					<div class="d-flex flex-column">
						<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">' . ucwords(substr(@$row->full_name, 0, 10)) . '</a>
						<a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-application-detail" data-toggle="tooltip" title="' . ucwords(@$row->current_designation) . '">' . ucwords(substr(@$row->current_designation, 0, 10)) . '</a>
					</div>';
			})
			->editColumn('Contacts', function ($row) {


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
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' . @$row->phone . '">' . @$row->phone . '</a>
						<a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary" data-toggle="tooltip" title="' . @$row->email . '">' . substr(@$row->email, 0, 15) . $bench_cnt_html . '</a>
					</div>';
			})
			->editColumn('Requisitions', function ($row) {
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
								<img alt="Pic" src="' . @$posted_by_image . '">
							</div>
						</div>
						<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="' . @$row->uc_company_name . '">' . substr(@$row->uc_company_name, 0, 10) . '</span>
						';
			})

			->editColumn('Requisitions', function ($row) {
				return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . ucwords(substr($row->title, 0, 10)) . '</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7">' . ucwords(substr($row->client_name, 0, 10)) . '</span>
                  </div>';
			})
			->editColumn('OfferAmount', function ($row) use ($currency, $salaryMode) {

				$sel = '';
				$option_currency = '';
				if (!empty($currency)) {
					foreach ($currency as $key => $cval) {
						$sel = '';
						if ($row->offer_currency == $key) {
							$sel = 'selected="selected"';
						}
						$option_currency .= '<option value="' . $key . '" ' . $sel . '>' . $cval . '</option>';
					}
				}

				$option_salarymode = '';
				if (!empty($salaryMode)) {
					foreach ($salaryMode as $key => $sval) {
						$sel = '';
						if ($row->offer_mode == $key) {
							$sel = 'selected="selected"';
						}
						$option_salarymode .= '<option value="' . $key . '" ' . $sel . '>' . $sval . '</option>';
					}
				}

				$raw_html = '<div class="" style="">
						<select class="form-select form-select-solid option_currency" arial-label="Select Currency" id="option_currency_' . $row->id . '" data-id="' . $row->id . '" data-offerId="' . $row->offer_id . '" name="option_currency[]" style="width: 82px;display: initial;">
							<option value="">Select</option>
							' . @$option_currency . '
						</select>
						<input type="number" class="form-control form-control-solid offer_amount" minlength="3" maxlength="7" name="offer_amount[]" id="offer_amount_' . $row->id . '" data-offerId="' . $row->offer_id . '" data-id="' . $row->id . '" placeholder="Amount" value="' . $row->offer_amount . '" style="width: 100px;display: initial;">
						<select class="form-select form-select-solid option_salarymode" arial-label="Select Annual Salary" name="option_salarymode[]" id="option_salarymode' . $row->id . '" data-offerId="' . $row->offer_id . '" data-id="' . $row->id . '" style="width: 100px;display: initial;">
							<option value="">Select</option>
							' . @$option_salarymode . '
						</select>
					</div>';
				return @$raw_html;
			})
			->editColumn('JoiningDate', function ($row) {


				$joined_date = $reset_date = '';
				$joined_date = @$row->joined_date;

				$oneNineSevenZero = date("d-m-Y", strtotime($joined_date));
				if (!empty($joined_date) && ($oneNineSevenZero != '01-01-1970')) {
					$reset_date = date("Y-m-d", strtotime($joined_date));
				}


				$raw_html = '<div class="d-flex align-items-center justify-content-stretch">
								<input type="date" id="joining_date_' . $row->id . '" value="' . @$reset_date . '" data-id="' . $row->id . '" data-offerId="' . $row->offer_id . '" name="joining_date[]"  class="me-2 form-control form-control-solid cursor-pointer custom_calendar1 joining_date" placeholder="Joining date" style="width:185px;"  onclick="this.showPicker()" />
								
							</div>';
				return @$raw_html;
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
							<a href="javascript.void(0);" data-row-id="' . @$row->application_id . '" class="d-flex flex-column  " data-toggle="modal" >
								<span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1" data-toggle="tooltip" title="' . $row->name . '">' . ucwords(substr($row->name, 0, 8)) . '</span>
								<span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' . @$name . '">' . @$name. '</span>
							</a>
						</div>';
			})

			->rawColumns(['chkbox', 'Name', 'Contacts', 'Requisitions', 'Source', 'OfferAmount', 'JoiningDate', 'Stage'])
			->addIndexColumn()
			->make(true);
	}
	public function update_application(Request $request)
	{
		abort_if(!$this->user->cans('edit_offer'), 403);

		$candidate_id = $process = $cvalue = '';

		$candidate_id = $request->candidate_id;
		if ($candidate_id > 0) {
			$jobApplication = OfferJobApplication::find($candidate_id);

			if ($request->process == 1) {												//		proposed_ctc
				$jobApplication->proposed_ctc = $request->cvalue;
			}

			if ($request->process == 2) {      											//		any_offer
				$jobApplication->any_offer = $request->cvalue;
			}

			if ($request->process == 3) {      											//		document
				$jobApplication->document = $request->cvalue;
			}

			if ($request->process == 4) {      											//		sub_for_approval
				$jobApplication->sub_for_approval = $request->cvalue;
			}

			if ($request->process == 5) {      											//		offer_approved
				$jobApplication->offer_approved = $request->cvalue;
			}

			if ($request->process == 6) {      											//		offer_release
				$jobApplication->offer_release = $request->cvalue;
			}

			if ($request->process == 7) {      											//		offer_acceptance
				$jobApplication->offer_acceptance = $request->cvalue;
			}

			if ($request->process == 8) {      											//		joined
				$jobApplication->joined = $request->cvalue;
			}

			if ($request->process == 9) {

				$monthArr = array('Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4, 'May' => 5, 'Jun' => 6, 'Jul' => 7, 'Aug' => 8, 'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12);
				$dateTimeArr = explode(" ", $request->cvalue);
				$date = $dateTimeArr[0];
				$time = $dateTimeArr[1];

				$splitDate = explode("-", $date);
				$customdate = $splitDate[2] . '-' . $monthArr[$splitDate[1]] . '-' . $splitDate[0] . ' ' . $time;




				//$cvalue 								= strtotime($request->cvalue);

				//		joined_date
				$jobApplication->joined_date = $customdate;
			}
			$jobApplication->save();

		}


		//$view = view('product_company.job-applications.show', $this->data)->render();
		return Reply::dataOnly(['status' => 'success']);
	}
	public function loadVisibilityControl($id)
	{
		/*$job_id	=	$id	;
				 if($job_id > 0)
				 {
					  $this->jobSkill = DB::table('job_skills')
							 ->join('skills', 'skills.id', 'job_skills.skill_id')
							 ->select('job_skills.company_id', 'job_skills.skill_id', 'job_skills.job_id', 'skills.name')
							 ->where('job_skills.job_id' , '=', $job_id)
							 ->get()->toArray();
				 }*/

		return view('product_company.applicationoffer.load_visibility_control', $this->data)->render();
	}
	public function export($start, $length)
	{
		$filters = [
			'start' => $start,
			'length' => $length,
			'loginUserId' => $this->user->id,
			'loginUserRole' => $this->user->role->role_id,
			'company_id' => auth()->user()->company_id
		];


		$data = [
			'company' => $this->user->name
		];


		return Excel::download(new ApplicationOfferExport($filters, $data), 'ApplicationOffer.xlsx', ExcelExcel::XLSX);
	}

	public function updateSalary(Request $request)
	{
		abort_if(!$this->user->cans('edit_offer'), 403);

		$block = '';
		$company_id = auth()->user()->company_id;


		$option_case = @$request->option_case;
		$option_currency_id = @$request->option_currency_id;
		$option_currency_val = @$request->option_currency_val;
		$offerId = @$request->offerId;


		$jobApplication = JobApplication::find($option_currency_id);
		$OfferJobApplication = OfferJobApplication::find($offerId);
		// dd($option_currency_id,$offerId,$OfferJobApplication,$jobApplication);

		$date = date('Y-m-d H:i:s');
		$values = array(
			'event_id' => 4,
			'event_by_company_id' => auth()->user()->company_id,
			'event_by_user_id' => auth()->user()->id,
			//'master_process_name' 		=>	@$jobApplication->job_id, 
			//'child_process_name' 			=>	@$master_status, 

			'application_id' => @$jobApplication->id ? @$jobApplication->id : NULL,
			'application_email' => @$jobApplication->email ? @$jobApplication->email : NULL,
			'job_id' => @$OfferJobApplication->job_id ? @$OfferJobApplication->job_id : NULL,
			'note' => '',
			'create_at' => @$date ? @$date : NULL,

			'update_at' => @$date ? @$date : NULL,
			//'prev_process_name'				=>	$prev_process_name ? $prev_process_name: NULL,
			//'prev_process_child_name'		=>	$prev_process_child_name ? $prev_process_child_name : NULL	
		);


		if (@$jobApplication->master_status_id > 0) {
			$masterStatus = DB::table('master_status')->select('master_status.name')->where('id', $jobApplication->master_status_id)->first();
			$values['master_process_name'] = @$masterStatus->name;
		}
		if (@$jobApplication->child_status_id > 0) {
			$masterStatus = DB::table('candidate_status')->select('candidate_status.name')->where('candidate_status.id', $jobApplication->child_status_id)->first();
			$values['child_process_name'] = @$masterStatus->name;
		}


		if ($option_case == 'OFFER_CURRENCY') {
			//DB::table('offer_job_applications')->where('company_id', $company_id)->where('id', $option_currency_id)->limit(1) ->update(array('offer_currency' => $option_currency_val));
			DB::table('job_applications')->where('company_id', $company_id)->where('id', $option_currency_id)->limit(1)->update(array('offer_currency' => $option_currency_val));

			DB::table('offer_job_applications')->where('company_id', $company_id)->where('id', $offerId)->limit(1)->update(array('offer_currency' => $option_currency_val));


			$values['offer_currency'] = @$option_currency_val;
		}
		if ($option_case == 'OFFER_SALARY_MODE') {
			//DB::table('offer_job_applications')->where('company_id', $company_id)->where('id', $option_currency_id)->limit(1) ->update(array('offer_mode' => $option_currency_val));	
			DB::table('job_applications')->where('company_id', $company_id)->where('id', $option_currency_id)->limit(1)->update(array('offer_mode' => $option_currency_val));

			DB::table('offer_job_applications')->where('company_id', $company_id)->where('id', $offerId)->limit(1)->update(array('offer_mode' => $option_currency_val));

			$values['offer_mode'] = @$option_currency_val;
		}
		if ($option_case == 'OFFER_AMOUNT') {
			//DB::table('offer_job_applications')->where('company_id', $company_id)->where('id', $option_currency_id)->limit(1) ->update(array('offer_amount' => $option_currency_val));	
			DB::table('job_applications')->where('company_id', $company_id)->where('id', $option_currency_id)->limit(1)->update(array('offer_amount' => $option_currency_val));

			DB::table('offer_job_applications')->where('company_id', $company_id)->where('id', $offerId)->limit(1)->update(array('offer_amount' => $option_currency_val));

			$values['offer_amount'] = @$option_currency_val;
		}
		if ($option_case == 'OFFER_JOINING_DATE') {
			$joined_date = '';
			//$joined_date	=	@$row->joined_date;
			$joined_date = date("y-m-d H:i:s", strtotime($option_currency_val));

			//DB::table('offer_job_applications')->where('company_id', $company_id)->where('id', $option_currency_id)->limit(1) ->update(array('joined_date' => $joined_date));
			DB::table('job_applications')->where('company_id', $company_id)->where('id', $option_currency_id)->limit(1)->update(array('joined_date' => $joined_date));

			DB::table('offer_job_applications')->where('company_id', $company_id)->where('id', $offerId)->limit(1)->update(array('joined_date' => $joined_date));


			$values['joined_date'] = @$option_currency_val;

		}
		$Offertimelime = OfferJobApplication::find($offerId);
		// dd($offerId,date("Y-m-d H:i:s", strtotime(@$request->joining_date)),$Offertimelime);
		$Offertimelime->offer_currency=  @$request->currency ? @$request->currency : 'NULL'; 
		$Offertimelime->offer_mode=  @$request->salarymode ? @$request->salarymode : 'NULL';
		$Offertimelime->offer_amount=  @$request->offer_amount ? @$request->offer_amount : 'NULL' ;
		$Offertimelime->joined_date=  @$request->joining_date ? date("Y-m-d H:i:s", strtotime(@$request->joining_date)) : 'NULL'; 
		$Offertimelime->save();
		$values['offer_currency'] = @$request->currency ? @$request->currency : 'NULL';
		$values['offer_mode'] = @$request->salarymode ? @$request->salarymode : 'NULL';
		$values['offer_amount'] = @$request->offer_amount ? @$request->offer_amount : 'NULL' ;
		$values['joined_date'] = @$request->joining_date ? date("Y-m-d H:i:s", strtotime(@$request->joining_date)) : 'NULL';
		// dd($offerId,$request->all());

		GeneralMethod::UpdateTimeline($values);



		$message = '<div class="alert alert-success" id="success-alert"><button type="button" class="close bg-transparent border-0 fw-bold" data-dismiss="alert">x</button><strong>Success! </strong> New Process have added successfully.</div>';
		return Reply::dataOnly(['status' => 'success', 'message' => $message]);


	}

}
