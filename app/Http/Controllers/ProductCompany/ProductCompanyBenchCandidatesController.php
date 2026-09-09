<?php

/***#######################################################
Controller    	: 	AdminAppTrackerController
Purpose			: 
Author			:	Abu
Company			:	4Bell
Date			:	28-JUNE-2022	
########################################################***/

namespace App\Http\Controllers\ProductCompany;

use App\Helper\Files;
use App\Helper\Reply;
use App\Helper\GeneralMethod;

use App\Models\BenchApplication;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\JobApplication;
use App\Models\Job;

/*use App\ApplicationStatus;

use App\Http\Requests\InterviewSchedule\StoreRequest;
use App\Http\Requests\StoreJobApplication;
use App\Http\Requests\UpdateJobApplication;
use App\InterviewSchedule;
use App\InterviewScheduleEmployee;

use App\JobApplicationAnswer;
use App\JobLocation;
use App\JobQuestion;
use App\Notifications\CandidateScheduleInterview;
use App\Notifications\ScheduleInterview;
use App\Skill;
use App\Client;
use App\User;
use Carbon\Carbon;

use Illuminate\Support\Facades\Notification;

use App\NoticePeriod;
use App\JobSkill;
use Mail;*/

class ProductCompanyBenchCandidatesController extends ProductCompanyBaseController
{

	public function __construct()
	{
		parent::__construct();
		$this->pageTitle = 'Bench list';
		$this->pageIcon = 'icon-badge';
	}
	public function index()
	{
		$this->pageTitle = __('Bench Listing');
		$this->allBenchApplication = BenchApplication::count();
		return view('product_company.bench-candidates.index', $this->data);
	}
	public function data(Request $request)
	{


		abort_if(!$this->user->cans('view_job_applications'), 403);
		$company_id = auth()->user()->company_id;


		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();

		$chkAdminRole = $this->user->hasRole('admin');

		// $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
		$benchApplications = DB::table('job_applications')
			->select(
				'job_applications.id',
				'job_applications.full_name',
				'job_applications.current_designation',
				'job_applications.outsourcing_type',
				'job_applications.phone',
				'job_applications.secondary_mobile',
				'job_applications.dob',
				'job_applications.email',
				'job_applications.secondrary_email',
				'job_applications.qualification',
				'job_applications.total_exp',
				'job_applications.rel_exp',
				'job_applications.serving_notice_period_mode',
				'job_applications.np_id',
				'job_applications.pay_rate_currency',
				'job_applications.pay_rate_amount',
				'job_applications.pay_rate_mode',
				'job_applications.bill_rate_currency',
				'job_applications.bill_rate_amount',
				'job_applications.bill_rate_mode',
				'job_applications.comission_type',
				'job_applications.country',
				'job_applications.state',
				'job_applications.city',
				'job_applications.pincode',
				'job_applications.skills',
				'job_applications.photo',
				'job_applications.resume',
				'job_applications.status',
				'job_applications.employee_number',
				'job_applications.current_company',
				'job_applications.created_at',
				'qualification_master.name as qualification_name',
				'bench_master_status.name as master_status_name',
				'bench_master_status.css_style',
				DB::raw("COUNT(job_applications.id) as allCandidate"),
				DB::raw("SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing"),
				DB::raw("SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening"),
				DB::raw("SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview"),
				DB::raw("SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation"),
				DB::raw("SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered"),
				DB::raw("SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined"),
				DB::raw("SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected"),
				DB::raw("SUM(CASE WHEN job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS InReview")
			);

		$benchApplications = $benchApplications->join('bench_master_status', 'bench_master_status.id', 'job_applications.status');
		$benchApplications = $benchApplications->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification');

		$benchApplications = $benchApplications->leftJoin('rec_candidate_assign', function ($q) use ($company_id) {
			$q->on('rec_candidate_assign.source_from_id', '=', 'job_applications.id');
			$q->where('rec_candidate_assign.source_from_company_id', '=', $company_id, 'and');
		});
		// $benchApplications = $benchApplications->leftJoin('job_applications', function ($q) use ($company_id) {
		// 	$q->on('job_applications.id', '=', 'rec_candidate_assign.source_application_id');
		// 	$q->where('rec_candidate_assign.source_from_company_id', '=', $company_id, 'and');
		// });
		$benchApplications = $benchApplications->where('job_applications.status', '=', 2);
		$benchApplications = $benchApplications->where('job_applications.company_id', '=', $company_id);
		if (\request('search') != "") {
			$search = request('search');
			$benchApplications = $benchApplications->where('job_applications.full_name', 'LIKE', "%$search%");
		}
		$benchApplications = $benchApplications->groupBy('job_applications.id');
		$benchApplications = $benchApplications->orderBy('job_applications.id', 'DESC');

		return DataTables::of($benchApplications)

			->editColumn('chkbox', function ($row) {
				return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input chk_list_item " name="chk_list_item[]" type="checkbox" value="' . $row->id . '" />
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
				$profile_sidebar_css = 'bench_show_application_detail';

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
			->editColumn('Contacts', function ($row) {
				return '<div class="d-flex flex-column">
<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . @$row->phone . '</a>
<a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary" data-toggle="tooltip" title="' . @$row->email . '">' . substr(@$row->email, 0, 10) . '</a>
</div>';
			})
			->editColumn('Exp', function ($row) {
				return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$row->total_exp . '+</a>
                    <span class="text-muted fw-bold d-block fs-7">' . @$row->rel_exp . '+</span>
                  </div>';
			})
			->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {

				$pay_rate_currency = $bill_rate_currency = $pay_rate_amount = $bill_rate_amount = $pay_rate_mode = $bill_rate_mode = "";
				$pay_rate_currency = $currencySymbole[@$row->pay_rate_currency];
				$bill_rate_currency = $currencySymbole[@$row->bill_rate_currency];
				$pay_rate_amount = GeneralMethod::thousandsCurrencyFormat($row->pay_rate_amount);
				$bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($row->bill_rate_amount);

				return '<div class="d-flex flex-column">
							<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$pay_rate_currency . @$ctc . '/' . $pay_rate_mode . '</a>
							<span class="text-muted fw-bold d-block fs-7">' . $bill_rate_currency . @$bill_rate_amount . '/' . $bill_rate_mode . '</span>
						  </div>';
			})

			->editColumn('Availability', function ($row) {
				$npArr = array('Notice period', 'Available from', 'Immediate');
				return '<div class="d-flex flex-column">
                      <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . $npArr[$row->serving_notice_period_mode] . '</a> 
                      <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' . $row->np_id . '">' . $row->np_id . '</span>
                    </div>';
			})
			->editColumn('Qualifaction', function ($row) {
				return ' <div class="d-flex flex-column">
						  <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="' . @$row->qualification_name . '">' . @$row->qualification_name . '</a>
						  <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' . $row->skills . '">' . substr($row->skills, 0, 12) . '</span> 
						</div>';
			})
			->editColumn('Location', function ($row) {

				$country = $row_country_name = $state = $row_state_name = $city = $row_city_name = '';
				if (empty($row->country)) {
					$row_country_name = '----';
				} else {
					if (!empty(@$row->country)) {
						if (is_numeric(@$row->country)) {
							$country_name_Arr = GeneralMethod::getCountryName(@$row->country);
							foreach ($country_name_Arr as $cval) {
								$row_country_name = ucwords($cval['name']);
							}
						}
					}
				}

				if (empty($row->state)) {
					$row_state_name = '----';
				} else {
					if (@$row->country == 101) {
						$row_state = DB::table('state_list')->select('*')->where('id', @$row->state)->first();
						$row_state_name = @$row_state->state;

					} else {
						$state = @$row->state;
						if (is_numeric(@$row->country) && is_numeric($state)) {
							if ($state > 0) {
								$state_name_Arr = GeneralMethod::getStateName(@$row->country, $state);
								foreach ($state_name_Arr as $sval) {
									$row_state_name = ucwords($sval['name']);
								}
							}
						}
					}
				}

				if (@$row->country == 101) {
					$row_city = DB::table('city_list')->select('*')->where('city_id', @$row->city)->first();
					$row_city_name = @$row_city->city;
				} else {
					$row_city_name = @$row->city;
				}

				return '<div class="d-flex flex-column">
									<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' . ucwords($row_city_name) . '">' . ucwords(substr($row_city_name, 0, 10)) . '</a>
									<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' . ucwords($row_state_name) . ' | ' . strtoupper($row_country_name) . '">' . ucfirst(substr($row_state_name, 0, 5)) . ' | ' . strtoupper(substr($row_country_name, 0, 2)) . '</span>
								</div>';
			})
			->editColumn('Assigned', function ($row) use ($company_id) {

				$hmCount = DB::table('bench_assign')->join('users', 'users.id', '=', 'bench_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('bench_assign.bench_applications_id', '=', $row->id)->where('bench_assign.company_id', '=', $company_id)->where('users.company_id', '=', $company_id)->count();

				$hmData = DB::table('bench_assign')->join('users', 'users.id', '=', 'bench_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('bench_assign.bench_applications_id', '=', $row->id)->where('bench_assign.company_id', '=', $company_id)->where('users.company_id', '=', $company_id)->orderBy('users.name', 'ASC')->skip(0)->take(3)->get();

				$html = '';
				$html .= '<div class="d-flex flex-column">
                    						<div class="symbol-group symbol-hover">';
				$temp = '';
				foreach ($hmData as $pval) {

					$logo_path = url('public/avatar.png');
					if (!empty(@$pval->image) && strlen(@$pval->image) > 10) {
						$logo_path = url('public/user-uploads/team_profile_img/' . @$pval->image);
					}

					$html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' . @$pval->name . '">
                            						<img alt="Pic" src="' . @$logo_path . '">
                          						</div>';

				}
				$html .= '<div data-row-id="' . $row->id . '"   class="symbol symbol-30 symbol-circle symbol-light hm-listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list..">
														<span class="symbol-label font-weight-bold">' . $hmCount . '+</span>
													  </div>';
				$html .= '</div>
                    					</div>';
				return $html;
			})
			->editColumn('Date', function ($row) {

				$created_at = '';
				$created_at = $newDate = date("d M , Y", strtotime($row->created_at));
				return '<div class="d-flex flex-column">
							  <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . $created_at . '</a>
							 
							</div>';
			})


			->editColumn('TOT', function ($row) use ($company_id) {
				$stage = 'all';
				return '<div class="d-flex flex-column">
                        <a href="' . url('/product_company/bench-candidates/bench_explore/' . $row->id . '/' . $stage) . '">' . @$row->allCandidate . '</a>
                        <a href="' . url('/product_company/bench-candidates/bench_explore/' . $row->id . '/' . $stage) . '" class="text-muted fw-bold d-block fs-7 text-hover-primary">' . @$row->InReview . '</a>
                      </div>';
			})
			->editColumn('Status', function ($row) use ($company_id) {
				return '<div class="d-flex flex-column">
                      <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="fw-bold fs-6  stage_sub_stage ' . $row->css_style . '" data-toggle="modal" data-target="#stage_sub_stage" >
					  	' . $row->master_status_name . '</a>
                      <span class="fw-bold text-muted d-block fs-7" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="10 Days to Expire">Expire</span>
                    </div>';
			})
			->editColumn('SOU', function ($row) {
				$stage = 1;
				return '<div class="d-flex flex-column">
                        <a href="' . url('/product_company/bench-candidates/bench_explore/' . $row->id . '/' . $stage) . '" class="text-dark fw-bolder fs-6 text-hover-primary">' . @$row->Sourcing . '</a> 
                      </div>';
			})
			->editColumn('SCR', function ($row) {
				$stage = 2;
				return '<div class="d-flex flex-column">
                        <a href="' . url('/product_company/bench-candidates/bench_explore/' . $row->id . '/' . $stage) . '" class="text-dark fw-bolder fs-6 text-hover-primary">' . @$row->Screening . '</a>
                      </div>';
			})
			->editColumn('EVA', function ($row) {
				$stage = 3;
				return '<div class="d-flex flex-column">
                        <a href="' . url('/product_company/bench-candidates/bench_explore/' . $row->id . '/' . $stage) . '" class="text-dark fw-bolder fs-6 text-hover-primary">' . @$row->Interview . '</a>
                      </div>';
			})

			->editColumn('NEO', function ($row) {
				$stage = 4;
				return '<div class="d-flex flex-column">
                        <a href="' . url('/product_company/bench-candidates/bench_explore/' . $row->id . '/' . $stage) . '" class="text-dark fw-bolder fs-6 text-hover-primary">' . @$row->Negotiation . '</a>
                      </div>';
			})
			->editColumn('OFF', function ($row) {
				$stage = 5;
				return '<div class="d-flex flex-column">
                        <a href="' . url('/product_company/bench-candidates/bench_explore/' . $row->id . '/' . $stage) . '" class="text-dark fw-bolder fs-6 text-hover-primary">' . @$row->Offered . '</a>
                      </div>';
			})
			->editColumn('JOI', function ($row) {
				$stage = 6;
				return '<div class="d-flex flex-column">
                        <a href="' . url('/product_company/bench-candidates/bench_explore/' . $row->id . '/' . $stage) . '" class="text-dark fw-bolder fs-6 text-hover-primary">' . @$row->Joined . '</a>
                      </div>';
			})
			->editColumn('REJ', function ($row) {
				$stage = 7;
				return '<div class="d-flex flex-column">
                        <a href="' . url('/product_company/bench-candidates/bench_explore/' . $row->id . '/' . $stage) . '" class="text-dark fw-bolder fs-6 text-hover-primary">' . @$row->Rejected . '</a>
                      </div>';
			})

			->rawColumns(['chkbox', 'Name', 'Exp', 'Salary', 'Availability', 'Qualifaction', 'Location', 'Status', 'Date', 'Assigned', 'TOT', 'SOU', 'SCR', 'EVA', 'NEO', 'OFF', 'JOI', 'REJ'])
			->addIndexColumn()
			->make(true);
	}

	public function talent_pool()
	{
		$this->pageTitle = __('Talent Pool');
		$this->allBenchApplication = BenchApplication::count();
		return view('product_company.bench-candidates.talent_pool', $this->data);
	}
	public function benchListingPoolData(Request $request)
	{
		abort_if(!$this->user->cans('view_job_applications'), 403);
		$company_id = auth()->user()->company_id;

		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();

		$chkAdminRole = $this->user->hasRole('admin');

		// $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
		$benchApplications = DB::table('job_applications')->select('job_applications.*', 'qualification_master.name as qualification_name', 'bench_master_status.name as master_status_name', 'bench_master_status.css_style');
		$benchApplications = $benchApplications->join('bench_master_status', 'bench_master_status.id', 'job_applications.status');
		$benchApplications = $benchApplications->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification');
		$benchApplications = $benchApplications->where('job_applications.company_id', '=', $company_id);
		if (\request('search') != "") {
			$search = request('search');
			$benchApplications = $benchApplications->where('job_applications.full_name', 'LIKE', "%$search%");
		}
		$benchApplications = $benchApplications->whereNotIn('job_applications.status', [5,3]);
		$benchApplications = $benchApplications->groupBy('job_applications.id');
		$benchApplications = $benchApplications->orderBy('job_applications.id', 'DESC');
		$benchApplications = $benchApplications->where('job_applications.condidate_type', '=', 'bench_condidate');

		return DataTables::of($benchApplications)

			->editColumn('chkbox', function ($row) {
				return '<div class="form-check form-check-sm form-check-custom form-check-solid">
							<input class="form-check-input chk_list_item " name="chk_list_item[]" type="checkbox" value="' . $row->id . '" />
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
				$profile_sidebar_css = 'bench_show_application_detail';

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
			->editColumn('Contacts', function ($row) {
				return '<div class="d-flex flex-column">
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . @$row->phone . '</a>
						<a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary" data-toggle="tooltip" title="' . @$row->email . '">' . substr(@$row->email, 0, 10) . '</a>
						</div>';
			})
			->editColumn('Exp', function ($row) {
				return '<div class="d-flex flex-column">
								<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$row->total_exp . '+</a>
								<span class="text-muted fw-bold d-block fs-7">' . @$row->rel_exp . '+</span>
							  </div>';
			})
			->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {

				$pay_rate_currency = $bill_rate_currency = $pay_rate_amount = $bill_rate_amount = $pay_rate_mode = $bill_rate_mode = "";

				$pay_rate_currency = $currencySymbole[@$row->pay_rate_currency];
				$bill_rate_currency = $currencySymbole[@$row->bill_rate_currency];
				$pay_rate_amount = GeneralMethod::thousandsCurrencyFormat($row->pay_rate_amount);
				$bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($row->bill_rate_amount);
				return '<div class="d-flex flex-column">
							<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$pay_rate_currency . @$ctc . '/' . $pay_rate_mode . '</a>
							<span class="text-muted fw-bold d-block fs-7">' . $bill_rate_currency . @$bill_rate_amount . '/' . $bill_rate_mode . '</span>
						  </div>';
			})

			->editColumn('Availability-old', function ($row) {
				$npArr = array('Notice period', 'Available from', 'Immediate');
				return '<div class="d-flex flex-column">
							  <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . $npArr[$row->serving_notice_period_mode] . '</a> 
							  <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' . $row->np_id . '">' . $row->np_id . '</span>
							</div>';
			})
			->editColumn('Availability', function ($row) {
				$npArr = array('Notice period', 'Available from', 'Immediate');
				$npDate = $row->np_id;
				if ($row->serving_notice_period_mode == 0 || $row->serving_notice_period_mode == 1) {
					$originalDate = $row->np_id;
					$npDate = 'SNP LWD ' . date("d-M-y", strtotime($originalDate));
				}
				return '<div class="d-flex flex-column">
							  <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . $npArr[$row->serving_notice_period_mode] . '</a> 
							  <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' . $row->np_id . '">' . $npDate . '</span>
							</div>';
			})
			->editColumn('Qualifaction', function ($row) {
				return '<div class="d-flex flex-column">
								  <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="' . @$row->qualification_name . '">' . @$row->qualification_name . '</a>
								  <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' . $row->skills . '">' . substr($row->skills, 0, 12) . '</span> 
								</div>';
			})
			->editColumn('Location', function ($row) {

				$country = $row_country_name = $state = $row_state_name = $city = $row_city_name = '';
				if (empty($row->country)) {
					$row_country_name = '----';
				} else {
					if (!empty(@$row->country)) {
						if (is_numeric(@$row->country)) {
							$country_name_Arr = GeneralMethod::getCountryName(@$row->country);
							foreach ($country_name_Arr as $cval) {
								$row_country_name = ucwords($cval['name']);
							}
						}
					}
				}

				if (empty($row->state)) {
					$row_state_name = '----';
				} else {
					if (@$row->country == 101) {
						$row_state = DB::table('state_list')->select('*')->where('id', @$row->state)->first();
						$row_state_name = @$row_state->state;
					} else {
						$state = @$row->state;
						if (is_numeric(@$row->country) && is_numeric($state)) {
							if ($state > 0) {
								$state_name_Arr = GeneralMethod::getStateName(@$row->country, $state);
								foreach ($state_name_Arr as $sval) {
									$row_state_name = ucwords($sval['name']);
								}
							}
						}
					}
				}


				if (@$row->country == 101) {
					$row_city = DB::table('city_list')->select('*')->where('city_id', @$row->city)->first();
					$row_city_name = @$row_city->city;
				} else {
					$row_city_name = @$row->city;
				}

				return '<div class="d-flex flex-column">
										<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' . ucwords($row_city_name) . '">' . ucwords(substr($row_city_name, 0, 10)) . '</a>
										<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' . ucwords($row_state_name) . ' | ' . strtoupper($row_country_name) . '">' . ucfirst(substr($row_state_name, 0, 5)) . ' | ' . strtoupper(substr($row_country_name, 0, 2)) . '</span>
									</div>';

			})
			->editColumn('Assigned', function ($row) use ($company_id) {

				$hmCount = DB::table('bench_assign')->join('users', 'users.id', '=', 'bench_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('bench_assign.bench_applications_id', '=', $row->id)->where('bench_assign.company_id', '=', $company_id)->where('users.company_id', '=', $company_id)->count();

				$hmData = DB::table('bench_assign')->join('users', 'users.id', '=', 'bench_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('bench_assign.bench_applications_id', '=', $row->id)->where('bench_assign.company_id', '=', $company_id)->where('users.company_id', '=', $company_id)->orderBy('users.name', 'ASC')->skip(0)->take(3)->get();


				$html = '';
				$html .= '<div class="d-flex flex-column">
													<div class="symbol-group symbol-hover">';
				$temp = '';
				foreach ($hmData as $pval) {

					$logo_path = url('public/avatar.png');
					if (!empty(@$pval->image) && strlen(@$pval->image) > 10) {
						$logo_path = url('public/user-uploads/team_profile_img/' . @$pval->image);
					}
					$html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' . @$pval->name . '">
															<img alt="Pic" src="' . @$logo_path . '">
														</div>';
				}
				$html .= '<div data-row-id="' . $row->id . '"   class="symbol symbol-30 symbol-circle symbol-light hm-listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list..">
																<span class="symbol-label font-weight-bold">' . $hmCount . '+</span>
															  </div>';
				$html .= '</div>
												</div>';
				return $html;
			})

			->editColumn('Date', function ($row) {

				$created_at = '';
				$created_at = $newDate = date("d M , Y", strtotime($row->created_at));
				return '<div class="d-flex flex-column">
							  <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . $created_at . '</a>
							  <span class="text-muted fw-bold text-muted d-block fs-7">.......</span>
							</div>';
			})
			->editColumn('Requisitions', function ($row) use ($company_id) {

				return '<div class="d-flex flex-column">
							  <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="Custom Job">........</a>
							  <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="Veebrij-u">.......</span>
							</div>';
			})
			->editColumn('Status', function ($row) use ($company_id) {

				return '<div class="d-flex flex-column">
							  <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="fw-bold fs-6  stage_sub_stage ' . $row->css_style . '" data-toggle="modal" data-target="#stage_sub_stage" >
								' . $row->master_status_name . '</a>
							  <span class="fw-bold text-muted d-block fs-7" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="10 Days to Expire">Expire</span>
							</div>';
			})

			->rawColumns(['chkbox', 'Name', 'Contacts', 'Exp', 'Salary', 'Availability', 'Qualifaction', 'Location', 'Assigned', 'Date', 'Requisitions', 'Status'])
			->addIndexColumn()
			->make(true);
	}
	public function show($id)
	{
		$company_id = $this->job_additional_detail = $this->bench_assign = "";
		$company_id = auth()->user()->company_id;
		$this->currency = GeneralMethod::currency();
		$this->salaryMode = GeneralMethod::salaryMode();

		$this->benchApplication = JobApplication::select(
			'job_applications.id',
			'job_applications.company_id',
			'job_applications.submitted_by',
			'job_applications.full_name',
			'job_applications.current_designation',
			'job_applications.outsourcing_type',
			'job_applications.phone',
			'job_applications.secondary_mobile',
			'job_applications.dob',
			'job_applications.email',
			'job_applications.secondrary_email',
			'job_applications.qualification',
			'job_applications.total_exp',
			'job_applications.rel_exp',
			'job_applications.serving_notice_period_mode',
			'job_applications.np_id',
			'job_applications.pay_rate_currency',
			'job_applications.pay_rate_amount',
			'job_applications.pay_rate_mode',
			'job_applications.bill_rate_currency',
			'job_applications.bill_rate_amount',
			'job_applications.bill_rate_mode',
			'job_applications.comission_type',
			'job_applications.country',
			'job_applications.state',
			'job_applications.city',
			'job_applications.pincode',
			'job_applications.skills',
			'job_applications.photo',
			'job_applications.resume',
			'job_applications.status',
			'job_applications.offer_amount',
			'job_applications.joined_date',
			'job_applications.created_at',
			'job_applications.updated_at',
			'job_applications.employee_number',
			'qualification_master.name as qualification_name',
			'bench_master_status.name as master_status_name',
			'state_list.state as state_name',
			'city_list.city as city_name',
			'users.name as posted_by',
			'users.image as posted_by_image'
		)
			->leftjoin('users', 'users.id', 'job_applications.submitted_by')
			->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
			->leftJoin('bench_master_status', 'bench_master_status.id', '=', 'job_applications.status')
			->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
			->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city');

		$this->benchApplication = $this->benchApplication->where('job_applications.id', $id);
		$this->benchApplication = $this->benchApplication->first();

		$this->job_additional_detail = DB::table('bench_additional_detail')->select('*')->where('bench_additional_detail.bench_id', '=', @$this->benchApplication->id)->orderBy('bench_additional_detail.id', 'ASC')->get();
		$this->bench_assign = DB::table('bench_assign')->select('users.id', 'users.name')->leftjoin('users', 'users.id', 'bench_assign.user_id')->where('bench_assign.bench_applications_id', '=', @$this->benchApplication->id)->orderBy('bench_assign.id', 'ASC')->get();

		$view = view('product_company.bench-candidates.showdetail', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'view' => $view]);

	}
	public function hmlisting($id)
	{
		$this->currentBenchId = $id;
		$this->recruiter = array();
		$company_id = auth()->user()->company_id;
		$this->team = DB::table('users')
			->leftJoin('bench_assign', function ($join) use ($id) {
				$join->on('bench_assign.user_id', '=', 'users.id');
				$join->on('bench_assign.bench_applications_id', '=', DB::raw("'" . $id . "'"));
			})
			->leftjoin('role_user', 'role_user.user_id', 'users.id')
			->leftjoin('roles', 'roles.id', 'role_user.role_id')
			->select('users.id', 'users.name', 'users.email', 'users.working_status', 'bench_assign.id as bench_assign_id', 'bench_assign.bench_applications_id', 'roles.display_name')
			->where('users.company_id', $company_id)
			->where('users.status', '=', 'active')
			->groupBy('users.id')
			->get();
		$view = view('product_company.bench-candidates.hmlisting', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'view' => $view]);
	}

	public function hmlistingupdate(Request $request)
	{
		$company_id = auth()->user()->company_id;
		$assignStatus = $request->assignStatus;
		$currentBenchId = $request->currentBenchId;
		$currentUserId = $request->currentUserId;

		if ($assignStatus == 1) {
			DB::table('bench_assign')->where('bench_applications_id', $currentBenchId)->where('user_id', $currentUserId)->where('company_id', $company_id)->delete();
			return Reply::dataOnly(['status' => 'success', 'msg' => "Remove Record successfully."]);
		} else {
			DB::table('bench_assign')->where('bench_applications_id', $currentBenchId)->where('user_id', $currentUserId)->where('company_id', $company_id)->delete();
			$values = array('company_id' => $company_id, 'user_id' => $currentUserId, 'bench_applications_id' => $currentBenchId);
			DB::table('bench_assign')->insert($values);
			return Reply::dataOnly(['status' => 'success', 'msg' => "Added Record successfully."]);
		}
	}
	public function bench_application_status($id)
	{
		$current_company_id = auth()->user()->company_id;
		$this->qualification = $this->application = '';

		$this->currentBenchId = $id;

		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();

		$this->intModeArr = GeneralMethod::interview_mode();
		$this->benchApplication = JobApplication::select(
			'Job_applications.id',
			'Job_applications.company_id',
			'Job_applications.submitted_by',
			'Job_applications.full_name',
			'Job_applications.current_designation',
			'Job_applications.outsourcing_type',
			'Job_applications.phone',
			'Job_applications.secondary_mobile',
			'Job_applications.dob',
			'Job_applications.email',
			'Job_applications.secondrary_email',
			'Job_applications.qualification',
			'Job_applications.total_exp',
			'Job_applications.rel_exp',
			'Job_applications.serving_notice_period_mode',
			'Job_applications.np_id',
			'Job_applications.pay_rate_currency',
			'Job_applications.pay_rate_amount',
			'Job_applications.pay_rate_mode',
			'Job_applications.bill_rate_currency',
			'Job_applications.bill_rate_amount',
			'Job_applications.bill_rate_mode',
			'Job_applications.comission_type',
			'Job_applications.country',
			'Job_applications.state',
			'Job_applications.city',
			'Job_applications.pincode',
			'Job_applications.skills',
			'Job_applications.photo',
			'Job_applications.resume',
			'Job_applications.status',
			'Job_applications.offer_amount',
			'Job_applications.joined_date',
			'Job_applications.rating',
			'users.name as posted_by',
			'bench_master_status.name as master_status_name',
			'state_list.state as state_name',
			'users.name as submitted_by_name',
			'users.company_id as submitted_by_company_id'
		)
			->leftjoin('users', 'users.id', 'Job_applications.submitted_by')
			->leftjoin('state_list', 'state_list.id', 'Job_applications.status')
			->leftJoin('bench_master_status', 'bench_master_status.id', '=', 'Job_applications.status');
		$this->benchApplication = $this->benchApplication->where('Job_applications.id', $id);
		$this->benchApplication = $this->benchApplication->first();

		if (!empty($this->benchApplication->qualification) && $this->benchApplication->qualification > 0) {
			$this->qualification = DB::table('qualification_master')->select('name')->where('id', $this->benchApplication->qualification)->first();
		}
		$submitted_by_company_id = $this->benchApplication->submitted_by_company_id;
		$this->company = DB::table('companies')->select('company_name')->where('id', $submitted_by_company_id)->first();
		$this->master_status = DB::table('bench_master_status')->select('*')->orderBy('bench_master_status.priority', 'ASC')->get();

		$master_status_id = $this->benchApplication->status;

		$this->created_at = GeneralMethod::ymdTodmy($this->benchApplication->created_at);

		$this->company_id = $this->user->company_id;


		$pay_rate_currency = $pay_rate_amount = $pay_rate_mode = $bill_rate_amount = $bill_rate_currency = $bill_rate_mode = $country = '';
		$pay_rate_currency = $currencySymbole[$this->benchApplication->pay_rate_currency];


		$pay_rate_amount = GeneralMethod::thousandsCurrencyFormat($this->benchApplication->pay_rate_amount);
		$pay_rate_mode = substr($salaryMode[$this->benchApplication->pay_rate_mode], 0, 1);
		$bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($this->benchApplication->bill_rate_amount);
		$bill_rate_currency = $currencySymbole[$this->benchApplication->bill_rate_currency];
		$bill_rate_mode = substr($salaryMode[$this->benchApplication->bill_rate_mode], 0, 1);
		$this->pay_rate_amount = @$pay_rate_currency . @$pay_rate_amount . '/' . @$pay_rate_mode;
		$this->bill_rate_amount = @$bill_rate_currency . @$bill_rate_amount . '/' . @$bill_rate_mode;
		$this->row_country_name = '';

		if (empty($this->benchApplication->country)) {
			$this->row_country_name = '----';
		} else {
			if (!empty($this->benchApplication->country)) {
				if (is_numeric($this->benchApplication->country)) {
					$country_name_Arr = GeneralMethod::getCountryName($this->benchApplication->country);
					foreach ($country_name_Arr as $cval) {
						$this->row_country_name = ucwords($cval['name']);
					}
				}
			}
		}

		$this->row_state_name = '';
		if (empty($this->benchApplication->state)) {
			$this->row_state_name = '----';
		} else {
			if ($this->benchApplication->country == 101) {
				$this->row_state_name = $this->benchApplication->state_name;
			} else {
				$state = $this->benchApplication->state;
				if (is_numeric($this->benchApplication->country) && is_numeric($state)) {
					if ($state > 0) {
						$state_name_Arr = GeneralMethod::getStateName($this->benchApplication->country, $state);
						foreach ($state_name_Arr as $sval) {
							$this->row_state_name = ucwords($sval['name']);
						}
					}
				}
			}
		}

		$this->row_city_name = '';
		if ($this->benchApplication->country == 101) {
			$row_city = DB::table('city_list')->select('*')->where('city_id', $this->benchApplication->city)->first();
			$this->row_city_name = @$row_city->city;
		} else {
			$this->row_city_name = $this->application->city;
		}

		$view = view('product_company.bench-candidates.bench_application_status', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'view' => $view]);
	}
	public function bench_assign_to_job_listing(Request $request)
	{

		abort_if(!$this->user->cans('view_job_applications'), 403);
		$bench_id = $stage = $feedback_status = '';

		$company_id = auth()->user()->company_id;
		$bench_id = $request->bench_id;
		$stage = $request->stage;
		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();
		$job_status = GeneralMethod::job_status();
		$feedback_status = GeneralMethod::feedback_status();

		$chkAdminRole = $this->user->hasRole('admin');

		// $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
		// dd("kd");

		$benchAppOnJob = DB::table('rec_candidate_assign')
			->select(
				'bench_applications.id',
				'bench_applications.full_name',
				'bench_applications.phone',
				'bench_applications.email',
				'add_job_wise_condidates.master_status_id',
				'add_job_wise_condidates.child_status_id',
				'job_applications.created_at',
				'job_applications.id as job_applications_id',
				'master_status.name',
				'master_status.css_style',
				'jobs.total_positions',
				'jobs.status',
				'jobs.id as jobId',
				'jobs.min_sal_mode',
				'jobs.max_sal_mode',
				'jobs.min_salary_currency',
				'jobs.max_salary_currency',
				'jobs.max_salary',
				'jobs.min_salary',
				'jobs.max_exp',
				'jobs.min_exp',
				'jobs.title',
				'jobs.company_id',
				'clients.name as clients_name',
				'clients.logo_logical_path',
				'clients.self_company',
				'clients.company_id as client_company_id',
				'companies.company_name',
				'companies.logo',
				'add_job_wise_condidates.job_id',
				'add_job_wise_condidates.job_application_id'
			);

		$benchAppOnJob = $benchAppOnJob->join('job_applications as bench_applications', 'bench_applications.id', 'rec_candidate_assign.source_from_id');
		$benchAppOnJob = $benchAppOnJob->join('job_applications', 'job_applications.id', 'rec_candidate_assign.source_application_id');
		$benchAppOnJob = $benchAppOnJob->leftjoin('add_job_wise_condidates', 'add_job_wise_condidates.rec_candidate_assign_id', 'rec_candidate_assign.id')
			->leftjoin('master_status', function($join){
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
			});
		$benchAppOnJob = $benchAppOnJob->join('jobs', 'jobs.id', 'rec_candidate_assign.source_jobid');
		$benchAppOnJob = $benchAppOnJob->join('companies', 'companies.id', 'jobs.company_id');
		$benchAppOnJob = $benchAppOnJob->join('clients', 'clients.id', 'jobs.client_id');

		// $benchAppOnJob = $benchAppOnJob->leftjoin("master_status", function($qry){
		// 	$qry->on('master_status.id', 'job_applications.master_status_id');
		// });


		// $bench_applied_detail = $bench_applied_detail->join("rec_candidate_assign", function ($join) {
		// 	$join->on("rec_candidate_assign.source_from_id", "=", "job_applications.id")
		// 		->on("rec_candidate_assign.source_from_type", "=", DB::raw('2'));
		// });


		// $benchAppOnJob = $benchAppOnJob->join('offer_job_applications', 'offer_job_applications.job_id', 'job_applications.id');
		//$benchApplications 	=	$benchApplications->join('bench_master_status', 'bench_master_status.id', 'bench_applications.status');						
		$benchAppOnJob = $benchAppOnJob->leftjoin('qualification_master', 'qualification_master.id', 'bench_applications.qualification');
		$benchAppOnJob = $benchAppOnJob->where('bench_applications.company_id', '=', $company_id);
		// $benchAppOnJob = $benchAppOnJob->where(function ($query) {
		// 	$query->where('job_applications.master_status_id', '=', 5)->orWhere('job_applications.master_status_id', '=', 6);
		// });

		if (\request('search') != "") {
			$search = request('search');
			$benchAppOnJob = $benchAppOnJob->where('jobs.title', 'LIKE', "%$search%");
		}
		// dd( $bench_id);
		$benchAppOnJob = $benchAppOnJob->where('rec_candidate_assign.source_from_id', '=', $bench_id);
		if ($stage > 0) {
			// dd($stage);
			$benchAppOnJob = $benchAppOnJob->where('job_applications.master_status_id', '=', $stage);
		}

		$benchAppOnJob = $benchAppOnJob->groupBy('rec_candidate_assign.id');
		$benchAppOnJob = $benchAppOnJob->orderBy('rec_candidate_assign.id', 'DESC');


		return DataTables::of($benchAppOnJob)

			->editColumn('chkbox', function ($row) {
				return '<div class="form-check form-check-sm form-check-custom form-check-solid">
					<input type="radio" class="form-check-input chk_list_item " name="chk_list_item" value="' . $row->jobId . '"  />
                  </div>';
			})
			->editColumn('Requisition', function ($row) {

				$clients_name = "";
				$bgTextDecore = $strVal = '';
				$str = trim(@$row->title);
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

				$profile_detail_class = 'show-detail';
				$clients_name = ucwords(substr(@$row->clients_name, 0, 15));

				$logo_path = '';
				//$logo_path				=	url('img/client-default.png'); 	
				if (!empty($row->logo_logical_path)) {
					$logo_path = url('public/' . @$row->logo_logical_path);
				}

				if (@$row->self_company == 1) {
					if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
						$logo_path = url('public/user-uploads/company-logo/' . @$row->logo);
					}
				}

				$icon = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="' . $row->jobId . '" class="' . $profile_detail_class . '">
                      <div class="symbol-label ' . $bgTextDecore . ' border-radius-5">' . ucwords($strVal) . '</div>
                    </a>
                  </div>';

				if (!empty($logo_path)) {
					$icon = '<div data-row-id="' . $row->jobId . '"  class="symbol symbol-circle symbol-40px overflow-hidden me-2  ' . $profile_detail_class . ' border-radius-5">
                                        <a href="javascript:void(0);">
                                            <div class="symbol-label border-radius-5">
                                            	<img src="' . @$logo_path . '" alt="Company Logo" class="border-radius-5 w-100">
                                            </div>
                                        </a>
                                    </div>';
				}

				return $icon . '<div class="d-flex flex-column">
<a href="javascript:void(0);"  data-row-id="' . $row->jobId . '"   data-row-job_applications_id="' . $row->job_applications_id . '" class="text-dark fw-bolder text-hover-primary fs-6 ' . $profile_detail_class . '"  data-toggle="tooltip1" title="' . ucwords(@$row->title) . '" >' . ucwords(substr(@$row->title, 0, 15)) . '</a> 
<a href="javascript:void(0);" data-row-id="' . $row->jobId . '"   data-row-job_applications_id="' . $row->job_applications_id . '"   class="fw-bold text-muted d-block fs-7 text-hover-primary ' . $profile_detail_class . '">' . $clients_name . '</a>
                  </div>';

			})
			->editColumn('Exp', function ($row) {
				return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">' . @$row->max_exp . '+</span>
                    <span class="text-muted fw-bold text-muted d-block fs-7">' . @$row->min_exp . '+</span>
                  </div>';
			})
			->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {
				$min_sal_mode = $max_sal_mode = $min_salary_currency = $max_salary_currency = $max_salary = $min_salary = "";
				$min_sal_mode = strtoupper(substr($salaryMode[$row->min_sal_mode], 0, 1));
				$max_sal_mode = strtoupper(substr($salaryMode[$row->max_sal_mode], 0, 1));
				$min_salary_currency = $currencySymbole[@$row->min_salary_currency];
				$max_salary_currency = $currencySymbole[@$row->max_salary_currency];
				$max_salary = GeneralMethod::thousandsCurrencyFormat($row->max_salary);
				$min_salary = GeneralMethod::thousandsCurrencyFormat($row->min_salary);
				return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">' . $min_salary_currency . $max_salary . '/' . $min_sal_mode . '</span>                    
                    <span class="fw-bold d-block fs-7">' . $min_salary_currency . $min_salary . '/' . $max_sal_mode . '</span>
                  </div>';
			})
			->editColumn('Location', function ($row) {

				$country_id = $country_name = '';
				$jobid = $row->jobId;
				$client_loc = DB::table('client_location_jobs')
					->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
					->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
					->select('client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
					->where('client_location_jobs.job_id', $jobid)
					->first();

				$country_id = @$client_loc->country_id;
				if (@$country_id > 0) {
					$country_name = GeneralMethod::getCountryName($country_id);
					foreach ($country_name as $cval) {
						$country_name = ucwords($cval['sortname']);
					}
				}

				$city = substr(@$client_loc->city, 0, 6);
				if (@$client_loc->country_id == 101) {
					$city = substr(@$client_loc->city_name, 0, 6);
				}

				return ' <div class="d-flex flex-column">
                    				<span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="' . ucwords(@$client_loc->address) . '" >' . ucwords(substr(@$client_loc->address, 0, 10)) . '</span>
                    				<div class="d-flex align-items-center">
                          				<span class="fw-bold text-muted d-block fs-7">' . ucwords(substr(@$city, 0, 10)) . '</span>
                            				&nbsp;|&nbsp;
                          				<span class="fw-bold text-muted d-block fs-7">' . ucwords(substr(@$country_name, 0, 10)) . '</span>
                    				</div>
                  			</div>';
			})

			->editColumn('Status', function ($row) use ($job_status) {

				$status = $active = $hold = $inactive = $class = $recommendation_id = '';
				$status = ucwords($row->status);
				$class = $job_status[$row->status];

				if ($status == 'Inactive') {
					$status = 'Closed';
				}
				$ulink = '<a href="javascript.void(0);" data-row-id="' . @$row->id . '" class="d-flex flex-column ' . @$class . ' status_stage text-hover-success" data-toggle="modal" data-target="#status_stage">' . $status . '</a>';
				return '<div class="d-flex flex-column">
                       ' . $ulink . '
                      <span class="fw-bold text-muted d-block fs-7">R-' . @$row->jobId . '</span>
                    </div>';
			})
			->editColumn('AddedDate', function ($row) {

				$total_positions = $created_at = '';
				$total_positions = number_format($row->total_positions);
				$created_at = $row->created_at;
				$newDate = date("d M, Y", strtotime($created_at));
				return '<div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">' . $newDate . '</span>
                        <span class="text-muted fw-bold d-block fs-7">' . $total_positions . '</span>
                    </div>';

			})
			->editColumn('ContactPerson', function ($row) {

				$partnersCount = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('job_assign.job_id', '=', $row->jobId)->where('job_assign.company_id', '=', $row->company_id)->where('users.company_id', '=', $row->company_id)->count();

				$partnersData = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('job_assign.job_id', '=', $row->jobId)->where('job_assign.company_id', '=', $row->company_id)->where('users.company_id', '=', $row->company_id)->orderBy('users.name', 'ASC')->skip(0)->take(3)->get();

				$jobId = $row->jobId;
				$html = $temp = '';
				$html .= '<div class="d-flex flex-column"><div class="symbol-group symbol-hover">';
				foreach ($partnersData as $pval) {
					$image_path = url('public/avatar.png');
					if (!empty(@$pval->image) && strlen(@$pval->image) > 10)
						$image_path = url('public/user-uploads/team_profile_img/' . @$pval->image);
					$html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' . @$pval->name . '"><img alt="Pic" src="' . @$image_path . '"></div>';
				}
				$html .= '<div data-row-id="' . $row->jobId . '"   class="symbol symbol-30 symbol-circle symbol-light hm-listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list.."><span class="symbol-label font-weight-bold">' . $partnersCount . '+</span></div>';
				$html .= '</div></div>';

				return $html;

			})
			->editColumn('Interview', function ($row) use ($feedback_status) {
				$schedule_date = $className = '';
				$intRowCount = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->job_applications_id)->orderBy('interview_schedules.id', 'DESC')->count();
				$intRow = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->job_applications_id)->orderBy('interview_schedules.id', 'DESC')->first();
				$html = '';
				if ($intRowCount > 0) {
					$schedule_date = @$intRow->schedule_date;
					$schedule_date = date("d M, Y", strtotime($schedule_date));
				} else {
					$html = '<div style="width:150px" id="interview_date">&nbsp;</div>';
				}
				if ($intRowCount > 0) {
					$feedback_status_name = '';
					if ($intRow->feedback >= 0) {
						$feedback_status_name = @$feedback_status[@$intRow->feedback]['name'];
						$className = @$feedback_status[@$intRow->feedback]['classname'];
					} else {
						$feedback_status_name = 'Pending';
						$className = 'pending-bg';
					}
				} else {
					$feedback_status_name = '--------';
				}
				return '<div class="d-flex flex-column">
											<span class="text-dark fw-bolder fs-6">' . @$schedule_date . '</span>
											<span class="fw-bold d-block fs-7 ' . @$className . ' px-1">' . @$feedback_status_name . '</span>
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
				$stage_default_css = 'stage_sub_stage';
				$html = '';
				if ($row->master_status_id == 8) {
					$html = '<div class="d-flex flex-column">
								<a href="javascript.void(0);" data-row-id="' . @$row->id . '" class="d-flex flex-column client_stage_sub_stage" data-toggle="modal" data-target="#client_stage_sub_stage">
								<span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1">' . ucwords(substr($row->name, 0, 10)) . '</span>
								<span class="text-muted fw-bold d-block fs-7">' . @$name . '</span>
								</a>
								</div>';
				} else {
					$html = '<div class="d-flex flex-column">
								<span href="javascript.void(0);" data-row-id="' . @$row->id . '" class="d-flex flex-column ">
								<span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1">' . ucwords(substr($row->name, 0, 10)) . '</span>
								<span class="text-muted fw-bold d-block fs-7">' . @$name . '</span>
								</span>
								</div>';
				}
				return $html;
			})
			->editColumn('offeramount', function ($row) {
				$rowset = DB::table('offer_job_applications')->where('application_id',$row->job_application_id)->where('job_id',$row->job_id)->first();
				$offeramount = '--------';
				if($row->master_status_id==5){
					$offeramount = @$rowset->offer_amount;
				}
				return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">' . @$offeramount . '</span>
                  </div>';
			})
			->editColumn('joindate', function ($row) {
				// dd($row->job_id,$row->job_application_id);
				$rowset = DB::table('offer_job_applications')->where('application_id',$row->job_application_id)->where('job_id',$row->job_id)->first();
				$joindate = '--------';
				if($row->master_status_id==5){
					$joindate = @$rowset->joined_date ? date("d-m-Y",strtotime(@$rowset->joined_date)) : '';
				}
				return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">' . @$joindate . '</span>
                  </div>';
			})

			->rawColumns(['chkbox', 'Requisition', 'Exp', 'Salary', 'Location', 'Status', 'AddedDate', 'ContactPerson','offeramount','joindate', 'Interview', 'Stage'])
			->addIndexColumn()
			->make(true);

	}
	public function status_submit(Request $request)
	{

    //   dd($request->all());

		$company_id = "";
		$company_id = auth()->user()->company_id;
		$bench_master_status = @$request->bench_master_status;

		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();

		if ($bench_master_status == 2) {

			$packageStatus = GeneralMethod::validatePackage('NEW_BENCH_CLIENT_REQUEST');
			if ($packageStatus['STATUS'] == 1) {
				if ($packageStatus['EXPIRE'] == 'false') {
					if ($packageStatus['BALANCE'] > 0) {
						$bench_application_id = $bench_master_status = "";
						$bench_application_id = @$request->bench_application_id;
						$bench_master_status = @$request->bench_master_status;
						$BenchApplication = JobApplication::find($bench_application_id);
						$BenchApplication->status = @$bench_master_status;
						$BenchApplication->save();

						return Reply::dataOnly(['status' => 'success']);
					} else {
						return Reply::dataOnly(["status" => "info", "message" => "New Bench Client Request can't access because your package balance MAX LIMIT REACHED , please contact to Administrator."]);
					}
				} else {
					return Reply::dataOnly(["status" => "info", "message" => "New Bench Client Request can't access because your PACKAGE EXPIRED , please contact to Administrator."]);
				}
			} else {
				return Reply::dataOnly(["status" => "info", "message" => "New Bench Client Request can't access because your package STATUS IS INACTIVE , please contact to Administrator."]);
			}

		} else {
			$bench_application_id = $bench_master_status = "";
			$bench_application_id = @$request->bench_application_id;
			$bench_master_status = @$request->bench_master_status;
			$BenchApplication = JobApplication::find($bench_application_id);
			$BenchApplication->status = @$bench_master_status;
			$BenchApplication->save();

			// dd($BenchApplication);

			if (@$request->bench_master_status == 3) {

				if ($request->chk_list_item > 0) {

					$bench_applied_detail = DB::table('job_applications');
					$bench_applied_detail = $bench_applied_detail->select(
						'job_applications.id',
						'job_applications.full_name',
						'job_applications.current_designation',
						'job_applications.email',
						'job_applications.phone',
						'job_applications.total_exp',
						'job_applications.rel_exp',
						'job_applications.pay_rate_currency',
						'job_applications.pay_rate_amount',
						'job_applications.pay_rate_mode',
						'job_applications.bill_rate_currency',
						'job_applications.bill_rate_amount',
						'job_applications.bill_rate_mode',
						'job_applications.pay_rate_currency',
						'job_applications.pay_rate_amount',
						'job_applications.pay_rate_mode',
						'job_applications.bill_rate_currency',
						'job_applications.bill_rate_amount',
						'job_applications.bill_rate_mode',
						'jobs.title',
						'jobs.company_id as client_company_id',
						'companies.company_name',
						'qualification_master.name as qualification_master_name',
						'job_applications.skills',
						'job_applications.country',
						'job_applications.state',
						'job_applications.city',
						'job_applications.offer_amount',
						'master_status.name as master_status_name',
						'candidate_status.name as candidate_status_name',
						'bench_master_status.name as bench_master_status_name',
						'job_applications.joined_date',
						'job_applications.id as client_application_id'
					);

					$bench_applied_detail = $bench_applied_detail->join("rec_candidate_assign", function ($join) {
						$join->on("rec_candidate_assign.source_from_id", "=", "job_applications.id")
							->on("rec_candidate_assign.source_from_type", "=", DB::raw('1'));
					});
					$bench_applied_detail = $bench_applied_detail->join('jobs', 'jobs.id', 'rec_candidate_assign.source_jobid');
					$bench_applied_detail = $bench_applied_detail->join('companies', 'companies.id', 'jobs.company_id');
					// $bench_applied_detail = $bench_applied_detail->join('job_applications', 'job_applications.id', 'rec_candidate_assign.source_application_id');

					$bench_applied_detail = $bench_applied_detail->leftJoin('qualification_master', 'qualification_master.id', 'job_applications.qualification');
					$bench_applied_detail = $bench_applied_detail->leftJoin('master_status', 'master_status.id', 'job_applications.master_status_id');
					$bench_applied_detail = $bench_applied_detail->leftJoin('candidate_status', 'candidate_status.id', 'job_applications.child_status_id');
					$bench_applied_detail = $bench_applied_detail->leftJoin('bench_master_status', 'bench_master_status.id', 'job_applications.status');

					$bench_applied_detail = $bench_applied_detail->where('job_applications.id', '=', @$request->bench_application_id);
					$bench_applied_detail = $bench_applied_detail->where('jobs.id', '=', @$request->chk_list_item);
					$bench_applied_detail = $bench_applied_detail->first();

                    // dd($bench_applied_detail);
					$row_country_name = '';
					if (!empty(@$bench_applied_detail->country)) {
						if (is_numeric(@$bench_applied_detail->country)) {
							$country_name_Arr = GeneralMethod::getCountryName(@$bench_applied_detail->country);
							foreach ($country_name_Arr as $cval) {
								$row_country_name = ucwords($cval['name']);
							}
						}
					}

					$state = $row_state_name = '';

					if (empty($bench_applied_detail->state)) {
						$row_state_name = '----';
					} else {
						if (@$bench_applied_detail->country == 101) {
							$row_state = DB::table('state_list')->select('*')->where('id', @$bench_applied_detail->state)->first();
							$row_state_name = @$row_state->state;

						} else {
							$state = @$bench_applied_detail->state;
							if (is_numeric(@$bench_applied_detail->country) && is_numeric($state)) {
								if ($state > 0) {
									$state_name_Arr = GeneralMethod::getStateName(@$bench_applied_detail->country, $state);
									foreach ($state_name_Arr as $sval) {
										$row_state_name = ucwords($sval['name']);
									}
								}
							}
						}
					}


					$city = $row_city_name = '';

					if (@$bench_applied_detail->country == 101) {
						$row_city = DB::table('city_list')->select('*')->where('city_id', @$bench_applied_detail->city)->first();
						$row_city_name = @$row_city->city;
					} else {
						$row_city_name = @$bench_applied_detail->city;
					}

					$bench_assign_string = '';
					// $bench_assign_list = DB::table('bench_assign')->select('users.name')
					// 	->join('users', 'users.id', 'bench_assign.user_id')
					// 	->where('bench_assign.bench_applications_id', '=', @$request->bench_application_id)
					// 	->get();

					$cnt = 0;
					if (!empty($bench_assign_list)) {
						foreach ($bench_assign_list as $assignVal) {
							if ($cnt == 0) {
								$bench_assign_string = $assignVal->name;
							} else {
								$bench_assign_string = $bench_assign_string . ' , ' . $assignVal->name;
							}
							$cnt++;
						}
					}
					
					$values = array(
						'source_comapny_id' => auth()->user()->company_id,
						'bench_id' => @$request->bench_application_id,
						'client_company_id' => @$bench_applied_detail->client_company_id,
						'client_job_id' => @$request->chk_list_item,
						'client_application_id' => @$bench_applied_detail->client_application_id,

						'full_name' => @$bench_applied_detail->full_name,
						'current_designation' => @$bench_applied_detail->current_designation,
						'email' => @$bench_applied_detail->email,
						'phone' => @$bench_applied_detail->phone,
						'total_exp' => @$bench_applied_detail->total_exp,

						'rel_exp' => @$bench_applied_detail->rel_exp,
						'pay_rate_currency' => @$bench_applied_detail->pay_rate_currency,
						'pay_rate_amount' => @$bench_applied_detail->pay_rate_amount,
						'pay_rate_mode' => @$bench_applied_detail->pay_rate_mode,

						'bill_rate_currency' => @$bench_applied_detail->bill_rate_currency,
						'bill_rate_amount' => @$bench_applied_detail->bill_rate_amount,
						'bill_rate_mode' => @$bench_applied_detail->bill_rate_mode,

						'job_title' => @$bench_applied_detail->title,
						'company_name' => @$bench_applied_detail->company_name,
						'qualification' => @$bench_applied_detail->qualification_master_name,
						'skills' => @$bench_applied_detail->skills,
						'country' => @$row_country_name,

						'state' => @$row_state_name,
						'city' => @$row_city_name,
						'assign_to' => @$bench_assign_string,

						'offer_amount' => @$bench_applied_detail->offer_amount,
						'application_stage' => @$bench_applied_detail->master_status_name,
						'application_child_stage' => @$bench_applied_detail->candidate_status_name,
						'bench_status' => @$bench_applied_detail->bench_master_status_name,

						'joined_date' => @$bench_applied_detail->joined_date,
					);

					$detail = DB::table('add_job_wise_condidates')->where('job_id', '=', @$request->chk_list_item)->where('job_application_id', '=', @$request->bench_application_id)->first();
					DB::table('offer_job_applications')->where('job_id', @$request->chk_list_item)->where('application_id', '=', @$request->bench_application_id)->limit(1)->update(['joined'=>$detail->id]);

					// DB::table('bench_place')->insert($values);
				}
			}
			return Reply::dataOnly(['status' => 'success', 'message' => 'Bench Application status update successfully !']);
		}


	}

	public function create()
	{
		$this->pageTitle = __('Add Candidate');
		abort_if(!$this->user->cans('add_job_applications'), 403);

		$packageStatus = GeneralMethod::validatePackageRet('CVACCESS');
		if ($packageStatus != 1) {
			$url = url('product_company/bench-candidates/talent_pool');
			return redirect($url);
		}
		$this->qualification = DB::table('qualification_master')->select('*')->orderBy('qualification_master.display_order', 'ASC')->get();
		$this->currency = GeneralMethod::currency();
		$this->salaryMode = GeneralMethod::salaryMode();

		$this->countries = GeneralMethod::getAllCountryList();
		$this->statelist = $this->cityListArr = '';

		return view('product_company.bench-candidates.create', $this->data);
	}
	public function add_custom_detail($counter)
	{
		$this->countryArr = GeneralMethod::getAllCountryList();
		$this->counter = $counter;
		$view = view('product_company.bench-candidates.add_custom_detail', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'data' => $view]);

	}
	public function store(Request $request)
	{

		$packageStatus = GeneralMethod::validatePackageRet('CVACCESS');
		if ($packageStatus != 1) {
			$url = url('product_company/bench-candidates/talent_pool');
			return redirect($url);
		}

		$company_id = '';
		$company_id = auth()->user()->company_id;

		$benchApplication = new JobApplication(); // new BenchApplication();

		$benchApplication->company_id = $company_id;
		$benchApplication->submitted_by = auth()->user()->id;
		$benchApplication->full_name = @$request->full_name;
		$benchApplication->current_designation = @$request->current_designation;
		$benchApplication->outsourcing_type = @$request->outsourcing_type;

		$benchApplication->phone = @$request->phone;
		$benchApplication->secondary_mobile = @$request->secondary_mobile;
		$benchApplication->dob = @$request->dob;
		$benchApplication->email = @$request->email;
		$benchApplication->secondrary_email = @$request->secondrary_email;

		$benchApplication->qualification = @$request->qualification;
		$benchApplication->total_exp = @$request->total_exp;
		$benchApplication->rel_exp = @$request->rel_exp;
		$benchApplication->serving_notice_period_mode = @$request->serving_notice_period;
		$serving_notice_period = $request->serving_notice_period;
		if (($serving_notice_period == 0) || ($serving_notice_period == 1)) {
			$benchApplication->np_id = $request->calender;
		} else if ($serving_notice_period == 2) {
			$benchApplication->np_id = 'Immediate';
		}

		$benchApplication->pay_rate_currency = @$request->pay_rate_currency;
		$benchApplication->pay_rate_amount = @$request->pay_rate_amount;
		$benchApplication->pay_rate_mode = @$request->pay_rate_mode;
		$benchApplication->bill_rate_currency = @$request->bill_rate_currency;
		$benchApplication->bill_rate_amount = @$request->bill_rate_amount;

		$benchApplication->bill_rate_mode = @$request->bill_rate_mode;
		$benchApplication->comission_type = @$request->comission_type;
		$benchApplication->country = @$request->country;
		$benchApplication->state = @$request->state;
		$benchApplication->city = @$request->city;
		if (!empty($request->city)) {
			$benchApplication->city = @$request->city_id;
		}
		$benchApplication->employee_number = @$request->employee_number;
		$benchApplication->pincode = @$request->pincode;
		$cnt = 0;
		$skillStr = '';
		$skillArr = json_decode($request->skills, true);
		if (!empty($skillArr)) {
			foreach ($skillArr as $sval) {
				if ($cnt == 0) {
					$skillStr .= $sval['value'];
				} else {
					$skillStr .= ' , ' . $sval['value'];
				}
				$cnt++;
			}
		}
		$benchApplication->skills = $skillStr;
		if ($request->hasFile('photo')) {
			$benchApplication->photo = Files::upload($request->photo, 'bench_photos');
		}
		if ($request->hasFile('resume')) {
			$benchApplication->resume = Files::upload($request->resume, 'bench_resume', null, null, false);
		}
		$benchApplication->condidate_type = 'bench_condidate';
		$benchApplication->status = 1;
		$benchApplication->save();

		$customDetailsQuestion = @$request->customDetailsQuestion;
		$customDetailsAnswer = @$request->customDetailsAnswer;
		if ($customDetailsQuestion > 0) {
			$inc = 0;
			foreach ($customDetailsQuestion as $question) {
				$values = array('bench_id' => @$benchApplication->id, 'custom_keys' => @$question, 'custom_keys_value' => @$customDetailsAnswer[$inc]);
				DB::table('bench_additional_detail')->insert($values);
				$inc++;
			}
		}
		if (@$benchApplication->id > 0) {
			$event_name = 'CVACCESS';
			$event_table_name = 'job_applications';
			$event_table_id = @$benchApplication->id;
			$packageStatus = GeneralMethod::packageRedeem($event_name, $event_table_name, $event_table_id);
		}
		return Reply::redirect(url('product_company/bench-candidates/talent_pool'), __('menu.jobApplications') . ' ' . __('messages.createdSuccessfully'));

	}
	public function bench_explore($id, $stage)
	{
		$this->pageTitle = __('Bench Requisition');

		$company_id = auth()->user()->company_id;
		$this->stage = $stage;
		$this->bench_id = $id;
		$this->bench_detail = JobApplication::select('job_applications.id', 'job_applications.full_name', 'job_applications.current_designation', 'bench_master_status.name')
			->join('bench_master_status', 'bench_master_status.id', '=', 'job_applications.status')
			->findOrFail($this->bench_id);
		return view('product_company.bench-candidates.bench_explore', $this->data);
	}
	public function bench_explore_listing_counter(Request $request)
	{
		$bench_id = $request->bench_id;
		$company_id = auth()->user()->company_id;

		/**
		 * Intent: Use Elasticsearch GeneralMethod::bench_explore_listing_counter for candidate explore pipeline counts.
		 * Tenancy: Scoped strictly to authenticated tenant company_id.
		 */
		$bench_explore_listing_counter = GeneralMethod::bench_explore_listing_counter($bench_id, $company_id);

		$allCounter = $bench_explore_listing_counter->total;
		$applicationCounter = $bench_explore_listing_counter->applicationCounter;

		return Reply::dataOnly(['status' => 'success', 'data' => $applicationCounter, 'allCounter' => $allCounter]);
	}

	public function bench_explore_listing(Request $request)
	{

		abort_if(!$this->user->cans('view_job_applications'), 403);
		$company_id = auth()->user()->company_id;

		$bench_id = $stage = $feedback_status = '';
		$bench_id = $request->bench_id;
		// dd($bench_id);
		$stage = $request->stage;
		// dd($stage);
		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();
		$job_status = GeneralMethod::job_status();
		$feedback_status = GeneralMethod::feedback_status();

		$chkAdminRole = $this->user->hasRole('admin');

		// $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();

		$benchAppOnJob = DB::table('rec_candidate_assign')
			->select(
				'job_applications.id',
				'job_applications.full_name',
				'job_applications.phone',
				'job_applications.email',
				'job_applications.master_status_id',
				'job_applications.child_status_id',
				'job_applications.created_at',
				'job_applications.id as job_applications_id',
				'master_status.name',
				'master_status.css_style',
				'jobs.total_positions',
				'jobs.status',
				'jobs.id as jobId',
				'jobs.min_sal_mode',
				'jobs.max_sal_mode',
				'jobs.min_salary_currency',
				'jobs.max_salary_currency',
				'jobs.max_salary',
				'jobs.min_salary',
				'jobs.max_exp',
				'jobs.min_exp',
				'jobs.title',
				'jobs.company_id',
				'clients.name as clients_name',
				'clients.logo_logical_path',
				'clients.self_company',
				'clients.company_id as client_company_id',
				'companies.company_name',
				'companies.logo'
			);
		// $benchAppOnJob = $benchAppOnJob->leftjoin('bench_applications', 'bench_applications.id', 'rec_candidate_assign.source_from_id');
		$benchAppOnJob = $benchAppOnJob->join('job_applications', 'job_applications.id', 'rec_candidate_assign.source_application_id');
		$benchAppOnJob = $benchAppOnJob->join('jobs', 'jobs.id', 'rec_candidate_assign.source_jobid');
		$benchAppOnJob = $benchAppOnJob->join('companies', 'companies.id', 'jobs.company_id');
		$benchAppOnJob = $benchAppOnJob->join('clients', 'clients.id', 'jobs.client_id');
		$benchAppOnJob = $benchAppOnJob->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id');
		$benchAppOnJob = $benchAppOnJob->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification');
		$benchAppOnJob = $benchAppOnJob->where('job_applications.company_id', '=', $company_id);
		if (\request('search') != "") {
			$search = request('search');
			$benchAppOnJob = $benchAppOnJob->where('jobs.title', 'LIKE', "%$search%");
		}
		$benchAppOnJob = $benchAppOnJob->where('rec_candidate_assign.source_from_id', '=', $bench_id);
		if ($stage > 0) {
			$benchAppOnJob = $benchAppOnJob->where('job_applications.master_status_id', '=', $stage);
		}
		$benchAppOnJob = $benchAppOnJob->groupBy('rec_candidate_assign.id');
		$benchAppOnJob = $benchAppOnJob->orderBy('rec_candidate_assign.id', 'DESC');

		return DataTables::of($benchAppOnJob)

			->editColumn('chkbox', function ($row) {
				return '<div class="form-check form-check-sm form-check-custom form-check-solid">
								<input class="form-check-input chk_list_item " name="chk_list_item[]" type="checkbox" value="' . $row->id . '" />
							  </div>';
			})
			->editColumn('Requisition', function ($row) {

				$clients_name = $bgTextDecore = $strVal = $logo_path = '';
				$str = trim(@$row->title);
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
				$profile_detail_class = 'show-detail';
				$clients_name = ucwords(substr(@$row->clients_name, 0, 15));
				if (!empty($row->logo_logical_path)) {
					$logo_path = url('public/' . @$row->logo_logical_path);
				}
				if (@$row->self_company == 1) {
					if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
						$logo_path = url('public/user-uploads/company-logo/' . @$row->logo);
					}
				}
				$icon = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
						<a href="javascript:void(0);" data-row-id="' . $row->jobId . '" class="' . $profile_detail_class . '">
						  <div class="symbol-label ' . $bgTextDecore . ' border-radius-5">' . ucwords($strVal) . '</div>
						</a>
					  </div>';

				if (!empty($logo_path)) {
					$icon = '<div data-row-id="' . $row->jobId . '"  class="symbol symbol-circle symbol-40px overflow-hidden me-2  ' . $profile_detail_class . ' border-radius-5">
											<a href="javascript:void(0);">
												<div class="symbol-label border-radius-5">
													<img src="' . @$logo_path . '" alt="Company Logo" class="border-radius-5 w-100">
												</div>
											</a>
										</div>';
				}

				return $icon . '<div class="d-flex flex-column">
<a href="javascript:void(0);"  data-row-id="' . $row->jobId . '"   data-row-job_applications_id="' . $row->job_applications_id . '" class="text-dark fw-bolder text-hover-primary fs-6 ' . $profile_detail_class . '"  data-toggle="tooltip1" title="' . ucwords(@$row->title) . '" >' . ucwords(substr(@$row->title, 0, 15)) . '</a> 
<a href="javascript:void(0);" data-row-id="' . $row->jobId . '"   data-row-job_applications_id="' . $row->job_applications_id . '"   class="fw-bold text-muted d-block fs-7 text-hover-primary ' . $profile_detail_class . '">' . $clients_name . '</a>
                  </div>';

			})
			->editColumn('Exp', function ($row) {
				return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">' . @$row->max_exp . '+</span>
                    <span class="text-muted fw-bold text-muted d-block fs-7">' . @$row->min_exp . '+</span>
                  </div>';
			})
			->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {
				$min_sal_mode = $max_sal_mode = $min_salary_currency = $max_salary_currency = $max_salary = $min_salary = "";
				$min_sal_mode = strtoupper(substr($salaryMode[$row->min_sal_mode], 0, 1));
				$max_sal_mode = strtoupper(substr($salaryMode[$row->max_sal_mode], 0, 1));
				$min_salary_currency = $currencySymbole[@$row->min_salary_currency];
				$max_salary_currency = $currencySymbole[@$row->max_salary_currency];
				$max_salary = GeneralMethod::thousandsCurrencyFormat($row->max_salary);
				$min_salary = GeneralMethod::thousandsCurrencyFormat($row->min_salary);
				return '<div class="d-flex flex-column">
								<span class="text-dark fw-bolder fs-6">' . $min_salary_currency . $max_salary . '/' . $min_sal_mode . '</span>                    
								<span class="fw-bold d-block fs-7">' . $min_salary_currency . $min_salary . '/' . $max_sal_mode . '</span>
							  </div>';
			})


			->editColumn('Location', function ($row) {

				$country_id = $country_name = '';
				$jobid = $row->jobId;
				$client_loc = DB::table('client_location_jobs')->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')->select('client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')->where('client_location_jobs.job_id', $jobid)->first();

				$country_id = @$client_loc->country_id;
				if (@$country_id > 0) {
					$country_name = GeneralMethod::getCountryName($country_id);
					foreach ($country_name as $cval) {
						$country_name = ucwords($cval['sortname']);
					}
				}
				$city = substr(@$client_loc->city, 0, 6);
				if (@$client_loc->country_id == 101) {
					$city = substr(@$client_loc->city_name, 0, 6);
				}
				return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="' . ucwords(@$client_loc->address) . '" >' . ucwords(substr(@$client_loc->address, 0, 10)) . '</span>
                    <div class="d-flex align-items-center">
                          <span class="fw-bold text-muted d-block fs-7">' . ucwords(substr(@$city, 0, 10)) . '</span>
                            &nbsp;|&nbsp;
                          <span class="fw-bold text-muted d-block fs-7">' . ucwords(substr(@$country_name, 0, 10)) . '</span>
                        <div>
                      </div>
                    </div>
                  </div>';
			})
			->editColumn('Status', function ($row) use ($job_status) {

				$status = $active = $hold = $inactive = $class = $recommendation_id = '';
				$status = ucwords($row->status);
				$class = $job_status[$row->status];

				if ($status == 'Inactive') {
					$status = 'Closed';
				}
				$ulink = '<a href="javascript.void(0);" data-row-id="' . @$row->id . '" class="d-flex flex-column ' . @$class . ' status_stage text-hover-success" data-toggle="modal" data-target="#status_stage">' . $status . '</a>';
				return '<div class="d-flex flex-column">
                       ' . $ulink . '
                      <span class="fw-bold text-muted d-block fs-7">R-' . @$row->jobId . '</span>
                    </div>';
			})
			->editColumn('AddedDate', function ($row) {

				$total_positions = $created_at = '';
				$total_positions = number_format($row->total_positions);
				$created_at = $row->created_at;
				$newDate = date("d M, Y", strtotime($created_at));
				return '<div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">' . $newDate . '</span>
                        <span class="text-muted fw-bold d-block fs-7">' . $total_positions . '</span>
                    </div>';

			})
			->editColumn('ContactPerson', function ($row) {


				$partnersCount = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('job_assign.job_id', '=', $row->jobId)->where('job_assign.company_id', '=', $row->company_id)->where('users.company_id', '=', $row->company_id)->count();

				$partnersData = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('job_assign.job_id', '=', $row->jobId)->where('job_assign.company_id', '=', $row->company_id)->where('users.company_id', '=', $row->company_id)->orderBy('users.name', 'ASC')->skip(0)->take(3)->get();

				$jobId = $row->jobId;
				$html = $temp = '';
				$html .= '<div class="d-flex flex-column"><div class="symbol-group symbol-hover">';
				foreach ($partnersData as $pval) {
					$image_path = url('public/avatar.png');
					if (!empty(@$pval->image) && strlen(@$pval->image) > 10)
						$image_path = url('public/user-uploads/team_profile_img/' . @$pval->image);
					$html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' . @$pval->name . '"><img alt="Pic" src="' . @$image_path . '"></div>';
				}
				$html .= '<div data-row-id="' . $row->jobId . '"   class="symbol symbol-30 symbol-circle symbol-light hm-listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list.."><span class="symbol-label font-weight-bold">' . $partnersCount . '+</span></div>';
				$html .= '</div></div>';

				return $html;




			})

			->editColumn('Interview', function ($row) use ($feedback_status) {
				$schedule_date = $className = '';
				$intRowCount = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->job_applications_id)->orderBy('interview_schedules.id', 'DESC')->count();
				$intRow = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->job_applications_id)->orderBy('interview_schedules.id', 'DESC')->first();
				$html = '';
				if ($intRowCount > 0) {
					$schedule_date = @$intRow->schedule_date;
					$schedule_date = date("d M, Y", strtotime($schedule_date));
				} else {
					$html = '<div style="width:150px" id="interview_date">&nbsp;</div>';
				}
				if ($intRowCount > 0) {
					$feedback_status_name = '';
					if ($intRow->feedback >= 0) {
						$feedback_status_name = @$feedback_status[@$intRow->feedback]['name'];
						$className = @$feedback_status[@$intRow->feedback]['classname'];
					} else {
						$feedback_status_name = 'Pending';
						$className = 'pending-bg';
					}
				} else {
					$feedback_status_name = '--------';
				}
				return '<div class="d-flex flex-column">
											<span class="text-dark fw-bolder fs-6">' . @$schedule_date . '</span>
											<span class="fw-bold d-block fs-7 ' . @$className . ' px-1">' . @$feedback_status_name . '</span>
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

				$stage_default_css = 'stage_sub_stage';

				$html = '';
				if ($row->master_status_id == 8) {
					$html = '<div class="d-flex flex-column">
								<a href="javascript.void(0);" data-row-id="' . @$row->id . '" class="d-flex flex-column client_stage_sub_stage" data-toggle="modal" data-target="#client_stage_sub_stage">
								<span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1">' . ucwords(substr($row->name, 0, 10)) . '</span>
								<span class="text-muted fw-bold d-block fs-7">' . @$name . '</span>
								</a>
								</div>';
				} else {
					$html = '<div class="d-flex flex-column">
								<span href="javascript.void(0);" data-row-id="' . @$row->id . '" class="d-flex flex-column ">
								<span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1">' . ucwords(substr($row->name, 0, 10)) . '</span>
								<span class="text-muted fw-bold d-block fs-7">' . @$name . '</span>
								</span>
								</div>';
				}

				return $html;


			})

			->rawColumns(['chkbox', 'Requisition', 'Exp', 'Salary', 'Location', 'Status', 'AddedDate', 'ContactPerson', 'Interview', 'Stage'])
			->addIndexColumn()
			->make(true);

	}

	public function offerListing()
	{
		$this->pageTitle = __('Offer Listing');
		$this->allBenchApplication = BenchApplication::count();
		return view('product_company.bench-candidates.offerListing', $this->data);
	}
	public function offerListingData(Request $request)
	{

		abort_if(!$this->user->cans('view_job_applications'), 403);

		$company_id = auth()->user()->company_id;
		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();

		$chkAdminRole = $this->user->hasRole('admin');

		// $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();

		$benchApplications = DB::table('job_applications as bench_applications')
			->select(
				'bench_applications.*',
				'qualification_master.name as qualification_name',
				'bench_master_status.name as master_status_name',
				'bench_master_status.css_style',
				'jobs.title',
				'companies.company_name',
				'offer_job_applications.offer_amount as applications_offer_amount',
				'offer_job_applications.joined_date as applications_joined_date',
				'offer_job_applications.offer_currency as applications_offer_currency',
				'add_job_wise_condidates.master_status_id as candidate_master_status_id',
				'add_job_wise_condidates.child_status_id as candidate_child_status_id',
				DB::raw("SUM(CASE WHEN job_applications.id > 0 THEN 1 ELSE 0 END) AS ApplicantCount"),
				'master_status.name as candidate_master_status_name',
				'master_status.css_style as candidate_master_css_style'
			);
		$benchApplications = $benchApplications->join("rec_candidate_assign", function ($join) {
			$join->on("rec_candidate_assign.source_from_id", "=", "bench_applications.id")
				->on("rec_candidate_assign.source_from_type", "=", DB::raw('1'));
		});
		$benchApplications = $benchApplications->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'bench_applications.id');
		$benchApplications = $benchApplications->join('master_status', function ($join){
			$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
		});
		$benchApplications = $benchApplications->join('jobs', function ($join){
			$join->on('jobs.id', 'add_job_wise_condidates.job_id');
		});
		$benchApplications = $benchApplications->join('companies', function ($join){
			$join->on('companies.id', 'add_job_wise_condidates.company_id');
		});

		// $benchApplications = $benchApplications->join('jobs', 'jobs.id', 'rec_candidate_assign.source_jobid');
		// $benchApplications = $benchApplications->join('companies', 'companies.id', 'jobs.company_id');
		$benchApplications = $benchApplications->join('job_applications', 'job_applications.id', 'rec_candidate_assign.source_application_id');
		$benchApplications = $benchApplications->join('bench_master_status', 'bench_master_status.id', 'bench_applications.status');
		$benchApplications = $benchApplications->join('offer_job_applications', 'offer_job_applications.application_id', 'job_applications.id');
		// $benchApplications = $benchApplications->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id');
		$benchApplications = $benchApplications->leftjoin('qualification_master', 'qualification_master.id', 'bench_applications.qualification');
		$benchApplications = $benchApplications->where('bench_applications.company_id', '=', $company_id);

		if (\request('search') != "") {
			$search = request('search');
			$benchApplications = $benchApplications->where('bench_applications.full_name', 'LIKE', "%$search%");
		}
		$benchApplications = $benchApplications->where(function ($query) {
			$query->where('add_job_wise_condidates.master_status_id', '=', 5)->orWhere('add_job_wise_condidates.master_status_id', '=', 6);
		});
		$benchApplications = $benchApplications->where('bench_applications.status', '!=', 3);
		$benchApplications = $benchApplications->groupBy('bench_applications.id');
		$benchApplications = $benchApplications->orderBy('bench_applications.id', 'DESC');

		// dd($benchApplications->get());

		return DataTables::of($benchApplications)

			->editColumn('chkbox', function ($row) {
				return '<div class="form-check form-check-sm form-check-custom form-check-solid">
							<input class="form-check-input chk_list_item " name="chk_list_item[]" type="checkbox" value="' . $row->id . '" />
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
				$profile_sidebar_css = 'bench_show_application_detail';

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
			->editColumn('Contacts', function ($row) {
				return '<div class="d-flex flex-column">
<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . @$row->phone . '</a>
<a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary" data-toggle="tooltip" title="' . @$row->email . '">' . substr(@$row->email, 0, 10) . '</a>
</div>';
			})
			->editColumn('Exp', function ($row) {
				return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$row->total_exp . '+</a>
                    <span class="text-muted fw-bold d-block fs-7">' . @$row->rel_exp . '+</span>
                  </div>';
			})
			->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {

				$pay_rate_currency = $bill_rate_currency = $pay_rate_amount = $pay_rate_mode = $bill_rate_mode = "";
				$pay_rate_currency = $currencySymbole[@$row->pay_rate_currency];
				$bill_rate_currency = $currencySymbole[@$row->bill_rate_currency];
				$pay_rate_amount = GeneralMethod::thousandsCurrencyFormat($row->pay_rate_amount);
				$bill_rate_amount = "";
				$bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($row->bill_rate_amount);

				return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$pay_rate_currency . @$ctc . '/' . $pay_rate_mode . '</a>
                    <span class="text-muted fw-bold d-block fs-7">' . $bill_rate_currency . @$bill_rate_amount . '/' . $bill_rate_mode . '</span>
                  </div>';
			})

			->editColumn('Availability', function ($row) {
				$npArr = array('Notice period', 'Available from', 'Immediate');
				return '<div class="d-flex flex-column">
                      <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . $npArr[$row->serving_notice_period_mode] . '</a> 
                      <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' . $row->np_id . '">' . $row->np_id . '</span>
                    </div>';
			})
			->editColumn('Qualifaction', function ($row) {
				return ' <div class="d-flex flex-column">
						  <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="' . @$row->qualification_name . '">' . @$row->qualification_name . '</a>
						  <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' . $row->skills . '">' . substr($row->skills, 0, 12) . '</span> 
						</div>';
			})
			->editColumn('Location', function ($row) {

				$country = $row_country_name = $state = $row_state_name = $city = $row_city_name = '';

				if (empty($row->country)) {
					$row_country_name = '----';
				} else {
					if (!empty(@$row->country)) {
						if (is_numeric(@$row->country)) {
							$country_name_Arr = GeneralMethod::getCountryName(@$row->country);
							foreach ($country_name_Arr as $cval) {
								$row_country_name = ucwords($cval['name']);
							}
						}
					}
				}

				if (empty($row->state)) {
					$row_state_name = '----';
				} else {
					if (@$row->country == 101) {
						$row_state = DB::table('state_list')->select('*')->where('id', @$row->state)->first();
						$row_state_name = @$row_state->state;

					} else {
						$state = @$row->state;
						if (is_numeric(@$row->country) && is_numeric($state)) {
							if ($state > 0) {
								$state_name_Arr = GeneralMethod::getStateName(@$row->country, $state);
								foreach ($state_name_Arr as $sval) {
									$row_state_name = ucwords($sval['name']);
								}
							}
						}
					}
				}

				if (@$row->country == 101) {
					$row_city = DB::table('city_list')->select('*')->where('city_id', @$row->city)->first();
					$row_city_name = @$row_city->city;
				} else {
					$row_city_name = @$row->city;
				}

				return '<div class="d-flex flex-column">
                    	<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' . ucwords($row_city_name) . '">' . ucwords(substr($row_city_name, 0, 10)) . '</a>
                    	<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' . ucwords($row_state_name) . ' | ' . strtoupper($row_country_name) . '">' . ucfirst(substr($row_state_name, 0, 5)) . ' | ' . strtoupper(substr($row_country_name, 0, 2)) . '</span>
                    </div>';

			})
			->editColumn('Assigned', function ($row) use ($company_id) {

				$hmCount = DB::table('bench_assign')->join('users', 'users.id', '=', 'bench_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('bench_assign.bench_applications_id', '=', $row->id)->where('bench_assign.company_id', '=', $company_id)->where('users.company_id', '=', $company_id)->count();

				$hmData = DB::table('bench_assign')->join('users', 'users.id', '=', 'bench_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('bench_assign.bench_applications_id', '=', $row->id)->where('bench_assign.company_id', '=', $company_id)->where('users.company_id', '=', $company_id)->orderBy('users.name', 'ASC')->skip(0)->take(3)->get();

				$html = $temp = '';

				$html .= '<div class="d-flex flex-column">
                    						<div class="symbol-group symbol-hover">';
				foreach ($hmData as $pval) {

					$logo_path = url('public/avatar.png');
					if (!empty(@$pval->image) && strlen(@$pval->image) > 10) {
						$logo_path = url('public/user-uploads/team_profile_img/' . @$pval->image);
					}

					$html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' . @$pval->name . '">
                            						<img alt="Pic" src="' . @$logo_path . '">
                          						</div>';
				}
				$html .= '<div data-row-id="' . $row->id . '"   class="symbol symbol-30 symbol-circle symbol-light hm-listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list..">
														<span class="symbol-label font-weight-bold">' . $hmCount . '+</span>
													  </div>';
				$html .= '</div>
                    					</div>';
				return $html;

			})
			->editColumn('Date', function ($row) {

				$joined_date = '';
				$joined_date = date("d M , Y", strtotime($row->applications_joined_date));
				return '<div class="d-flex flex-column">
                      <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . $joined_date . '</a>
                      <span class="text-muted fw-bold text-muted d-block fs-7">.......</span>
                    </div>';
			})

			->editColumn('OfferAmount', function ($row) use ($currencySymbole, $salaryMode) {

				$applications_offer_amount = $applications_offer_currency = '';
				$applications_offer_amount = @$row->applications_offer_amount ? number_format(@$row->applications_offer_amount) : null;

				$applications_offer_currency = $row->applications_offer_currency ? $row->applications_offer_currency : 0;
				$salarytype = $salaryMode[$applications_offer_currency];
				return ' <div class="d-flex flex-column">
						  	<a href="javascript:void(0);" class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="' . @$applications_offer_amount . '">' . @$applications_offer_amount . '</a>
							<span class="text-muted fw-bold text-muted d-block fs-7">' . $salarytype . '</span>
						</div>';
			})
			->editColumn('Requisitions', function ($row) use ($company_id) {

				$html = '';
				$html .= '<div class="d-flex flex-column">
                    						<div class="symbol-group symbol-hover">';
				$html .= '<div data-row-id="' . $row->id . '"   class="symbol symbol-30 symbol-circle symbol-light bench_assign_to_job" data-bs-placement="top" data-toggle="tooltip2" title="More users list..">
														<span class="symbol-label font-weight-bold">' . $row->ApplicantCount . '+</span>
													  </div>';
				$html .= '</div>
                    					</div>';
				return $html;
			})
			->editColumn('Shortlisted', function ($row) {

				$name = '--------';
				if ($row->candidate_master_status_id == 8) {
					$name = '';
				}
				if (!empty(@$row->candidate_child_status_id) && @$row->candidate_child_status_id > 0) {
					$childStatus = '';
					$childStatus = DB::table('candidate_status')->select('*')->where('id', $row->candidate_child_status_id)->first();
					if (!empty($childStatus)) {
						$name = @$childStatus->name;
					}
				}
				return '<div class="d-flex flex-column">
								<a href="javascript.void(0);" data-row-id="' . @$row->id . '" class="d-flex flex-column offer_stage_sub_stage11" >
									<span class="badge fw-bolder fs-6 ' . @$row->candidate_master_css_style . ' px-2 py-1">' . ucwords(substr($row->candidate_master_status_name, 0, 10)) . '</span>
									<span class="text-muted fw-bold d-block fs-7">' . @$name . '</span>
								</a>
							</div>';
			})
			->editColumn('Status', function ($row) use ($company_id) {
				return '<div class="d-flex flex-column">
                      <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="fw-bold fs-6  stage_sub_stage ' . $row->css_style . '" data-toggle="modal" data-target="#stage_sub_stage" >
					  	' . $row->master_status_name . '</a>
                      <span class="fw-bold text-muted d-block fs-7 d-none" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="10 Days to Expire">Expire</span>
                    </div>';
			})

			->rawColumns(['chkbox', 'Name', 'Contacts', 'Exp', 'Salary', 'Availability', 'Qualifaction', 'Location', 'Assigned', 'Date', 'OfferAmount', 'Requisitions', 'Shortlisted', 'Status'])
			->addIndexColumn()
			->make(true);
	}
	public function bench_assign_to_job($id)
	{
		$this->currentBenchId = $id;
		$view = view('product_company.bench-candidates.bench_assign_to_job', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'view' => $view]);
	}
	public function recommend_req(Request $request)
	{
		$this->bench_id =$request->id;
		$this->bench_detail = JobApplication::select('job_applications.id', 'job_applications.full_name', 'job_applications.current_designation', 'bench_master_status.name')
			->join('bench_master_status', 'bench_master_status.id', '=', 'job_applications.status')
			->findOrFail($this->bench_id);
		$this->segment = ($request->segment(4)) ? $request->segment(4) : '';
		$this->pageTitle = __('Recommended Requisition');
		return view('product_company.bench-candidates.recommend_req', $this->data);
	}

	public function public_show($id)
	{

		$company_id = '';
		$company_id = auth()->user()->company_id;

		$this->currency = GeneralMethod::currency();
		$this->salaryMode = GeneralMethod::salaryMode();

		$this->benchApplication = DB::table('job_applications')
			->select(
				'job_applications.id',
				'job_applications.company_id',
				'job_applications.submitted_by',
				'job_applications.full_name',
				'job_applications.current_designation',
				'job_applications.outsourcing_type',
				'job_applications.phone',
				'job_applications.secondary_mobile',
				'job_applications.dob',
				'job_applications.email',
				'job_applications.secondrary_email',
				'job_applications.qualification',
				'job_applications.total_exp',
				'job_applications.rel_exp',
				'job_applications.serving_notice_period_mode',
				'job_applications.np_id',
				'job_applications.pay_rate_currency',
				'job_applications.pay_rate_amount',
				'job_applications.pay_rate_mode',
				'job_applications.bill_rate_currency',
				'job_applications.bill_rate_amount',
				'job_applications.bill_rate_mode',
				'job_applications.comission_type',
				'job_applications.country',
				'job_applications.state',
				'job_applications.city',
				'job_applications.pincode',
				'job_applications.skills',
				'job_applications.photo',
				'job_applications.resume',
				'job_applications.status',
				'job_applications.offer_amount',
				'job_applications.joined_date',
				'job_applications.created_at',
				'job_applications.updated_at',
				'job_applications.employee_number',
				'qualification_master.name as qualification_name',
				'bench_master_status.name as master_status_name',
				'state_list.state as state_name',
				'city_list.city as city_name',
				'users.name as posted_by',
				'users.image as posted_by_image'
			)
			->leftjoin('users', 'users.id', 'job_applications.submitted_by')
			->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
			->leftJoin('bench_master_status', 'bench_master_status.id', '=', 'job_applications.status')
			->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
			->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city');

		$this->benchApplication = $this->benchApplication->where('job_applications.id', $id);
		$this->benchApplication = $this->benchApplication->first();
		$this->benchApplication->phone = 'xxx-xxx-xxxx';
		$this->benchApplication->email = 'anonymous@email.com';

		$this->job_additional_detail = $this->bench_assign = "";
		$this->job_additional_detail = DB::table('bench_additional_detail')->select('*')->where('bench_additional_detail.bench_id', '=', @$this->benchApplication->id)->orderBy('bench_additional_detail.id', 'ASC')->get();

		$this->bench_assign = DB::table('bench_assign')->select('users.id', 'users.name')->leftjoin('users', 'users.id', 'bench_assign.user_id')->where('bench_assign.bench_applications_id', '=', @$this->benchApplication->id)->orderBy('bench_assign.id', 'ASC')->get();

		$this->bench_assign_count = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile')->join('users', 'users.id', '=', 'bench_assign.user_id')->where('bench_assign.bench_applications_id', '=', @$this->benchApplication->id)->count();

		$this->bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile')->join('users', 'users.id', '=', 'bench_assign.user_id')->where('bench_assign.bench_applications_id', '=', @$this->benchApplication->id)->get();

		$view = view('product_company.bench-candidates.public_show', $this->data)->render();
		return Reply::dataOnly(['status' => 'success', 'view' => $view]);

	}
	public function placedListing()
	{
		abort_if(!$this->user->cans('view_job_applications'), 403);
		$company_id = auth()->user()->company_id;

		$this->pageTitle = __('Offer Listing');

		$allBenchApplication = DB::table('job_applications')
		->select('job_applications.*', 
		'qualification_master.name as qualification_name',
		 'bench_master_status.name as master_status_name',
		'bench_master_status.css_style',
		'offer_job_applications.offer_amount as offeramount',
		'offer_job_applications.joined_date as joineddate'
	    );
		$allBenchApplication = $allBenchApplication->leftjoin('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
			->join('master_status', function($join){
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
			});
		$allBenchApplication = $allBenchApplication->join('bench_master_status', 'bench_master_status.id', 'job_applications.status');
		$allBenchApplication = $allBenchApplication->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification');
		$allBenchApplication = $allBenchApplication->join('offer_job_applications', 'offer_job_applications.application_id', 'job_applications.id');
		// $allBenchApplication = $allBenchApplication->join('offer_job_applications', 'offer_job_applications.job_id', 'job_applications.id');
		$allBenchApplication = $allBenchApplication->where('job_applications.company_id', '=', $company_id);
		if (\request('search') != "") {
			$search = request('search');
			$allBenchApplication = $allBenchApplication->where('job_applications.full_name', 'LIKE', "%$search%");
		}
		$allBenchApplication = $allBenchApplication->groupBy('job_applications.id');
		$allBenchApplication = $allBenchApplication->orderBy('job_applications.id', 'DESC');
		$allBenchApplication = $allBenchApplication->where('job_applications.status', '=', 3);
		$allBenchApplication = $allBenchApplication->where('job_applications.condidate_type', '=', 'bench_condidate');

		$this->allBenchApplication = $allBenchApplication->count();
		return view('product_company.bench-candidates.placedListing', $this->data);
	}
	public function placedListingData(Request $request)
	{
		abort_if(!$this->user->cans('view_job_applications'), 403);
		$company_id = auth()->user()->company_id;

		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();
		$currency = GeneralMethod::currency();

		$chkAdminRole = $this->user->hasRole('admin');

		// $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();

		// $placedApplications = DB::table('bench_place')->select('bench_place.*');
		// $placedApplications = $placedApplications->where('bench_place.source_comapny_id', '=', $company_id);
		// $placedApplications = $placedApplications->orderBy('bench_place.id', 'DESC');

		
		$placedApplications = DB::table('job_applications')
		->select('job_applications.*', 
		'qualification_master.name as qualification_name',
		'bench_master_status.name as master_status_name',
		'bench_master_status.css_style',
		'offer_job_applications.offer_amount as offeramount',
		'offer_job_applications.joined_date as joineddate',
		'jobs.title as job_title',
		'companies.company_name',
		'add_job_wise_condidates.id as placedID',
		'add_job_wise_condidates.placed_end_date',
		'offer_job_applications.offer_currency as offercurrency',
		'offer_job_applications.offer_mode as offermode'
	    );
		$placedApplications = $placedApplications->leftjoin('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
			->join('master_status', function($join){
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
			});
			$placedApplications = $placedApplications->join('jobs', function ($join) use ($company_id) {
				$join->on('jobs.id', 'add_job_wise_condidates.job_id');
			});
			$placedApplications = $placedApplications->join('companies', function ($join) use ($company_id) {
				$join->on('companies.id', 'add_job_wise_condidates.company_id');
			})
			->join('offer_job_applications', function($join){
				$join->on('offer_job_applications.joined', 'add_job_wise_condidates.id');
			});
		$placedApplications = $placedApplications->join('bench_master_status', 'bench_master_status.id', 'job_applications.status');
		$placedApplications = $placedApplications->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification');
		// $placedApplications = $placedApplications->join('offer_job_applications', 'offer_job_applications.application_id', 'job_applications.id');
		// $placedApplications = $placedApplications->join('offer_job_applications', 'offer_job_applications.job_id', 'job_applications.id');
		$placedApplications = $placedApplications->where('job_applications.company_id', '=', $company_id);
		if (\request('search') != "") {
			$search = request('search');
			$placedApplications = $placedApplications->where('job_applications.full_name', 'LIKE', "%$search%");
		}
		$placedApplications = $placedApplications->groupBy('job_applications.id');
		$placedApplications = $placedApplications->orderBy('job_applications.id', 'DESC');
		$placedApplications = $placedApplications->where('job_applications.status', '=', 3);
		$placedApplications = $placedApplications->where('job_applications.condidate_type', '=', 'bench_condidate');

		// dd($placedApplications->get());


		return DataTables::of($placedApplications)

			->editColumn('chkbox', function ($row) {
				return '<div class="form-check form-check-sm form-check-custom form-check-solid">
	<input class="form-check-input chk_list_item " name="chk_list_item[]" type="checkbox" value="' . $row->id . '" />
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

				$profile_sidebar_css = '';

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
			->editColumn('Contacts', function ($row) {
				return '<div class="d-flex flex-column">
	<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . @$row->phone . '</a>
	<a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary" data-toggle="tooltip" title="' . @$row->email . '">' . substr(@$row->email, 0, 10) . '</a>
	</div>';
			})
			->editColumn('Exp', function ($row) {
				return '<div class="d-flex flex-column">
	<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$row->total_exp . '+</a>
	<span class="text-muted fw-bold d-block fs-7">' . @$row->rel_exp . '+</span>
	</div>';
			})
			->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {

				$pay_rate_currency = $bill_rate_currency = $pay_rate_amount = $bill_rate_amount = $pay_rate_mode = $bill_rate_mode = "";
				$pay_rate_currency = $currencySymbole[@$row->pay_rate_currency];
				$bill_rate_currency = $currencySymbole[@$row->bill_rate_currency];
				$pay_rate_amount = GeneralMethod::thousandsCurrencyFormat($row->pay_rate_amount);
				$bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($row->bill_rate_amount);
				return '<div class="d-flex flex-column">
						<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$pay_rate_amount . @$ctc . '/' . $pay_rate_mode . '</a>
						<span class="text-muted fw-bold d-block fs-7">' . $bill_rate_currency . @$bill_rate_amount . '/' . $bill_rate_mode . '</span>
					  </div>';
			})

			->editColumn('Qualifaction', function ($row) {
				return '<div class="d-flex flex-column">
								  <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="' . @$row->qualification_name . '">' . @$row->qualification_name . '</a>
								  <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' . $row->skills . '">' . substr($row->skills, 0, 12) . '</span> 
								</div>';
			})
			->editColumn('Location', function ($row) {

				$country = $row_country_name = $state = $row_state_name = $city = $row_city_name = '';
				if (empty($row->country)) {
					$row_country_name = '----';
				} else {
					if (!empty(@$row->country)) {
						if (is_numeric(@$row->country)) {
							$country_name_Arr = GeneralMethod::getCountryName(@$row->country);
							foreach ($country_name_Arr as $cval) {
								$row_country_name = ucwords($cval['name']);
							}
						}
					}
				}

				if (empty($row->state)) {
					$row_state_name = '----';
				} else {
					if (@$row->country == 101) {
						$row_state = DB::table('state_list')->select('*')->where('id', @$row->state)->first();
						$row_state_name = @$row_state->state;
					} else {
						$state = @$row->state;
						if (is_numeric(@$row->country) && is_numeric($state)) {
							if ($state > 0) {
								$state_name_Arr = GeneralMethod::getStateName(@$row->country, $state);
								foreach ($state_name_Arr as $sval) {
									$row_state_name = ucwords($sval['name']);
								}
							}
						}
					}
				}


				if (@$row->country == 101) {
					$row_city = DB::table('city_list')->select('*')->where('city_id', @$row->city)->first();
					$row_city_name = @$row_city->city;
				} else {
					$row_city_name = @$row->city;
				}

				return '<div class="d-flex flex-column">
										<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' . ucwords($row_city_name) . '">' . ucwords(substr($row_city_name, 0, 10)) . '</a>
										<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' . ucwords($row_state_name) . ' | ' . strtoupper($row_country_name) . '">' . ucfirst(substr($row_state_name, 0, 5)) . ' | ' . strtoupper(substr($row_country_name, 0, 2)) . '</span>
									</div>';

			})
			->editColumn('Placed_End_Date', function ($row) use ($company_id) {

				$disabled = $custom_style = $placed_end_date = $reset_date = '';
				$placed_end_date = @$row->placed_end_date;
				$oneNineSevenZero = date("d-m-Y", strtotime($placed_end_date));
				if (!empty($placed_end_date) && ($oneNineSevenZero != '01-01-1970')) {
					$reset_date = date("Y-m-d", strtotime($placed_end_date));
				}
				$raw_html = '<div class="d-flex align-items-center justify-content-stretch">
							<input ' . $disabled . ' type="date" id="placed_end_date_' . $row->placedID . '" value="' . @$reset_date . '" data-id="' . $row->placedID . '" name="placed_end_date[]" onfocus="this.showPicker()"  class="me-2 form-control form-control-solid cursor-pointer custom_calendar placed_end_date" placeholder="End Date" style="width:185px; ' . $custom_style . '"/>
							</div>';
				return @$raw_html;

			})

			->editColumn('Assigned', function ($row) use ($company_id) {

				$hmCount = DB::table('bench_assign')->join('users', 'users.id', '=', 'bench_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('bench_assign.bench_applications_id', '=', $row->id)->where('bench_assign.company_id', '=', $company_id)->where('users.company_id', '=', $company_id)->count();

				$hmData = DB::table('bench_assign')->join('users', 'users.id', '=', 'bench_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('bench_assign.bench_applications_id', '=', $row->id)->where('bench_assign.company_id', '=', $company_id)->where('users.company_id', '=', $company_id)->orderBy('users.name', 'ASC')->skip(0)->take(3)->get();


				$html = '';
				$html .= '<div class="d-flex flex-column">
													<div class="symbol-group symbol-hover">';
				$temp = '';
				foreach ($hmData as $pval) {

					$logo_path = url('public/avatar.png');
					if (!empty(@$pval->image) && strlen(@$pval->image) > 10) {
						$logo_path = url('public/user-uploads/team_profile_img/' . @$pval->image);
					}
					$html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' . @$pval->name . '">
															<img alt="Pic" src="' . @$logo_path . '">
														</div>';
				}
				$html .= '<div data-row-id="' . $row->id . '"   class="symbol symbol-30 symbol-circle symbol-light hm-listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list..">
																<span class="symbol-label font-weight-bold">' . $hmCount . '+</span>
															  </div>';
				$html .= '</div>
												</div>';
				return $html;
			})
			
			// ->editColumn('Assigned', function ($row) use ($company_id) {

			// 	$hmCount = DB::table('bench_assign')->join('users', 'users.id', '=', 'bench_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('bench_assign.bench_applications_id', '=', $row->bench_id)->where('bench_assign.company_id', '=', $company_id)->where('users.company_id', '=', $company_id)->count();

			// 	$hmData = DB::table('bench_assign')->join('users', 'users.id', '=', 'bench_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('bench_assign.bench_applications_id', '=', $row->bench_id)->where('bench_assign.company_id', '=', $company_id)->where('users.company_id', '=', $company_id)->orderBy('users.name', 'ASC')->skip(0)->take(3)->get();

			// 	$html = $temp = '';
			// 	$html .= '<div class="d-flex flex-column">
            //         						<div class="symbol-group symbol-hover">';
			// 	if (!empty($hmData)) {
			// 		foreach ($hmData as $pval) {
			// 			$logo_path = url('public/avatar.png');
			// 			if (!empty(@$pval->image) && strlen(@$pval->image) > 10) {
			// 				$logo_path = url('public/user-uploads/team_profile_img/' . @$pval->image);
			// 			}
			// 			$html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' . @$pval->name . '">
			// 																<img alt="Pic" src="' . @$logo_path . '">
			// 															</div>';
			// 		}
			// 	} else {
			// 		$html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3">...........</div>';
			// 	}
			// 	$html .= '</div>
            //         					</div>';
			// 	return $html;

			// })
			->editColumn('JoiningDate', function ($row) {

				$joined_date = '';
				$joined_date = $newDate = date("d M , Y", strtotime($row->joineddate));

				return '<div class="d-flex flex-column">
                      <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . $joined_date . '</a>
                    </div>';
			})
			->editColumn('OfferAmount', function ($row) use ($currency,$salaryMode,$currencySymbole) {
				$offercurrency = $salarymode = $mode = $offeramount = "";
				$offercurrency = $currencySymbole[@$row->offercurrency];
				$salarymode = $salaryMode[@$row->offermode];
				$offeramount = GeneralMethod::thousandsCurrencyFormat($row->offeramount);

				if("Annual"==$salarymode){
					$mode = "Yr";
				}elseif("Monthly"==$salarymode){
					$mode = "Mon";
				}elseif("Hourly"==$salarymode){
					$mode = "Hr";
				}
				
				return '<div class="d-flex flex-column">
                      <a href="javascript:void(0);"  class="fw-bold fs-6" >
					  	' .$offercurrency.' '.@$offeramount .'/'.$mode.  '</a>
                    </div>';
			})
			->editColumn('Requisitions', function ($row) use ($company_id) {

				return '<div class="d-flex flex-column">
							  <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="Custom Job">'.$row->company_name.'</a>
							  <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="Veebrij-u">'.$row->job_title.'</span>
							</div>';
			})
			->editColumn('Status', function ($row) use ($company_id) {

				return '<div class="d-flex flex-column">
							  <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="fw-bold fs-6  stage_sub_stage ' . $row->css_style . '" data-toggle="modal" data-target="#stage_sub_stage" >
								' . $row->master_status_name . '</a>
							  <span class="fw-bold text-muted d-block fs-7" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="10 Days to Expire">Expire</span>
							</div>';
			})

			->rawColumns(['chkbox', 'Name', 'Contacts', 'Exp', 'Salary', 'Qualifaction', 'Placed_End_Date', 'Location', 'Assigned', 'JoiningDate', 'OfferAmount', 'Requisitions', 'Status'])
			->addIndexColumn()
			->make(true);

	}
	public function update_placed_end_date(Request $request)
	{
		$block = '';
		$company_id = auth()->user()->company_id;

		$bench_app_id = @$request->bench_app_id;
		$bench_placed_end_date = @$request->bench_placed_end_date;
		if (!empty($bench_app_id) && !empty($bench_placed_end_date)) {

			DB::table('add_job_wise_condidates')->where('id', @$bench_app_id)->limit(1)->update(array('placed_end_date' => @$bench_placed_end_date));
			$message = 'Update Bench Placed End Date';
			$status = 'success';
		} else {
			$message = 'Something wrong please try again.';
			$status = 'fail';
		}
		return Reply::dataOnly(['status' => $status, 'message' => $message]);
	}


	public function all_data(Request $request)
	{
		// dd($request->condidate_id);
		// dd($request->condidate_id); skills city_id city pay_rate_amount
		$currentRowJob = JobApplication::find($request->condidate_id);
		$currentJobSkill = explode(",", $currentRowJob->skills);
		$condidate_id  = (int) $request->condidate_id;
		$set_skill = array();
		if ($currentJobSkill > 0) {
			foreach ($currentJobSkill as $val) {
				array_push($set_skill, trim($val));
			}
		}
		$skill = DB::table('job_metronics_skills')->whereIn('skill_name', $set_skill)->pluck('job_id')->toArray();

		// ksort($skill);  // Sort by values

		// foreach ($skill as $value => $count) {
		// 	echo "<br>";
		// 	echo "Value: $value, Count: $count" . PHP_EOL;
		// }

		// dd(explode(",",$JobApplication->skills),array_unique($skill),$set_skill);
		abort_if(!$this->user->cans('view_jobs'), 403);

		$company_id = auth()->user()->company_id;

		$chkAdminRole = $this->user->hasRole('admin');

		// $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
		$job_status = GeneralMethod::job_status();
		$salaryMode = GeneralMethod::salaryMode();
		$currencySymbole = GeneralMethod::currencySymbole();

		if (($chkAdminRole > 0) || (in_array("all_requisition", $this->userPermissions) && in_array("view_jobs", $this->userPermissions))) {

			$alldatalist = DB::table('jobs')
				->select(
					'jobs.id',
					'jobs.title',
					'jobs.work_type',
					'jobs.show_vendor',
					'jobs.total_positions',
					'jobs.priority',
					'jobs.status',
					'jobs.min_exp',
					'jobs.max_exp',
					'jobs.created_at',
					'jobs.start_date',
					'jobs.end_date',
					'jobs.min_salary',
					'jobs.max_salary',
					'jobs.min_sal_mode',
					'jobs.max_sal_mode',
					'jobs.min_salary_currency',
					'jobs.max_salary_currency',
					'clients.name as clients_name',
					'clients.logo_logical_path',
					'clients.self_company',
					'clients.company_id as client_company_id',
					'companies.company_name',
					'companies.logo',
					DB::raw("COUNT(job_applications.id) as allCandidate"),

					DB::raw("SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing"),
					DB::raw("SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening"),
					DB::raw("SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview"),       // MEANS COMPANY OWN UPLOAD APP
					DB::raw("SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation"),
					DB::raw("SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered"),
					DB::raw("SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined"),
					DB::raw("SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected"),
					DB::raw("SUM(CASE WHEN job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS InReview"),

					DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = " . $company_id . " THEN 1 ELSE 0 END) AS thirdparty_counter"),
					DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = " . $company_id . " AND job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS thirdparty_InReview"),
					DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = " . $company_id . " AND job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS thirdparty_Sourcing"),
					DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = " . $company_id . " AND job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS thirdparty_Screening"),
					DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = " . $company_id . " AND job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS thirdparty_Interview"),
					DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = " . $company_id . " AND job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS thirdparty_Negotiation"),
					DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = " . $company_id . " AND job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS thirdparty_Offered"),
					DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = " . $company_id . " AND job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS thirdparty_Joined"),

					DB::raw("SUM(
							CASE 
									WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = " . $company_id . " AND job_applications.master_status_id = 7 THEN 1 
									WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = " . $company_id . " AND job_applications.master_status_id = 9 THEN 1
									ELSE 0 END
						) AS thirdparty_Rejected"
					),
					//DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = ".$company_id." AND job_applications.master_status_id = 9 THEN 1 ELSE 0 END) AS thirdparty_InReview_Rejected"),

					'recommendation_relation.id as recommendation_id',
					'recommendation_relation.from_company_id',
					'recommendation_relation.final_status',
					'recommendation_relation.to_company_id'
				)
				->join('clients', 'clients.id', 'jobs.client_id')
				->join('companies', 'companies.id', 'jobs.company_id')
				->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id')
				->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
				->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id')
				->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
				->leftJoin('rec_candidate_assign', 'rec_candidate_assign.source_jobid', '=', 'jobs.id')
				->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id');

			// $alldatalist = $alldatalist->where('jobs.company_id', '=', $company_id);

			$search = '';
			$search = request('search');
			if (\request('search') != "") {
				$search = request('search');
				$alldatalist = $alldatalist->where('jobs.title', 'LIKE', "%$search%");
			}

			// ->where('recommendation_relation.to_company_id', '=', $company_id)

			if (\request('search') != "") {
				$alldatalist = $alldatalist->orWhere(function ($query) use ($company_id, $search) {
					return $query->where('recommendation_relation.final_status', '=', 1)->where('jobs.title', 'LIKE', "%$search%");
				});
				$alldatalist = $alldatalist->orWhere(function ($query) use ($company_id, $search) {
					return $query->where('recommendation_relation.final_status', '=', 2)->where('jobs.title', 'LIKE', "%$search%");
				});
			} else {
				// $alldatalist 		=	$alldatalist->orWhere(function ($query) use ($company_id, $search) {
				// return $query->where('recommendation_relation.to_company_id', '=', $company_id)->where('recommendation_relation.final_status', '=', 1);
				// });		
				// $alldatalist 		=	$alldatalist->orWhere(function ($query) use ($company_id, $search) {
				// return $query->where('recommendation_relation.to_company_id', '=', $company_id)->where('recommendation_relation.final_status', '=', 2);
				// });		
			}

			//################################################################################# ------------- TITLE MATCH 
			// $findstring = explode(' ', $currentRowJob->title);
			// foreach ($findstring as $value) {
			// 	$alldatalist = $alldatalist->where('jobs.title', 'like', "%{$value}%")->whereIn('companies.company_type', array(1, 2));
			// }
			// if (!empty($currentJobSkill)) {
			// 	foreach ($currentJobSkill as $jskill) {
			// 		$skill_name = trim($jskill);
			// 		// $alldatalist = $alldatalist->where(function ($qry) use ($skill_name) {
			// 		// 	$qry->where('jobs.title', 'like', "%{$skill_name}%");
			// 		// 	$qry->orWhere('job_metronics_skills.skill_name', 'like', "%{$skill_name}%");
			// 		// });

			// 		$alldatalist = $alldatalist->orWhere('jobs.title', 'like', "%{$skill_name}%");
			// 		$alldatalist = $alldatalist->orWhere('job_metronics_skills.skill_name', 'like', "%{$skill_name}%");
			// 		$alldatalist = $alldatalist->orWhere('client_location.city_id', $currentRowJob->city_id);
			// 		$alldatalist = $alldatalist->orWhere('client_location.city', 'like', "%{$currentRowJob->city}%");
			// 		$alldatalist = $alldatalist->orWhere('jobs.employment_type', $currentRowJob->outsourcing_type);
			// 		$alldatalist = $alldatalist->where('jobs.min_exp','>=',$currentRowJob->total_exp);
			// 		$alldatalist = $alldatalist->where('jobs.max_exp','<=',$currentRowJob->total_exp);
			// 	}
			// }

			//################################################################################# ------------- EXPERIENCE MATCH
			// if ($currentRowJob->min_exp >= 0 && $currentRowJob->max_exp >= 0) {
			// 	$alldatalist = $alldatalist->orWhereBetween('jobs.min_exp', [$currentRowJob->min_exp, $currentRowJob->max_exp])->whereIn('companies.company_type', array(1, 2));
			// 	$alldatalist = $alldatalist->orWhereBetween('jobs.max_exp', [$currentRowJob->min_exp, $currentRowJob->max_exp])->whereIn('companies.company_type', array(1, 2));
			// }

			//################################################################################# ------------ JOB TYPE
			// if ($currentRowJob->employment_type) {
			// 	$alldatalist = $alldatalist->orWhere('jobs.employment_type', $currentRowJob->employment_type)->whereIn('companies.company_type', array(1, 2));
			// }

			//################################################################################# ------------  LOCATION 
			// if (!empty($currentJobLocation)) {
			// 	foreach ($currentJobLocation as $cloc) {
			// 		if ($cloc->country_id == 101) {
			// 			$alldatalist = $alldatalist->orWhere('client_location.city_id', $cloc->city_id)->whereIn('companies.company_type', array(1, 2));
			// 		} else {
			// 			$alldatalist = $alldatalist->orWhere('client_location.city', 'like', "%{$cloc->city}%")->whereIn('companies.company_type', array(1, 2));
			// 		}
			// 	}
			// }



		}


		if (\request('client_id') != "") {
			$client_id = request('client_id');
			$alldatalist = $alldatalist->where('jobs.client_id', $client_id);
		}


		// $alldatalist = $alldatalist->orWhere('jobs.title', 'like', "%{$skill_name}%");
		// $alldatalist = $alldatalist->orWhere('job_metronics_skills.skill_name', 'like', "%{$skill_name}%");
		// $alldatalist = $alldatalist->orWhere('client_location.city_id', $currentRowJob->city_id);
		// $alldatalist = $alldatalist->orWhere('client_location.city', 'like', "%{$currentRowJob->city}%");
		// $alldatalist = $alldatalist->orWhere('jobs.employment_type', $currentRowJob->outsourcing_type);
		// $alldatalist = $alldatalist->where('jobs.min_exp','>=',$currentRowJob->total_exp);
		// $alldatalist = $alldatalist->where('jobs.max_exp','<=',$currentRowJob->total_exp);

		// min_salary_currency max_salary_currency

		$alldatalist = $alldatalist->where(function ($qry) use ($skill, $set_skill, $currentRowJob) {

			$qry->where(function ($query) use ($set_skill) {
				foreach ($set_skill as $skill_title) {
					$query->orWhere('jobs.title', 'LIKE', '%' . $skill_title . '%');
				}
			});
			$qry->where(function ($query) use ($skill) {
				foreach ($skill as $job_id) {
					$query->orWhere('jobs.id', '=', $job_id);
				}
			});
			$qry->orWhere('client_location.city_id', $currentRowJob->city_id);
			$qry->orWhere('client_location.city', 'like', "%{$currentRowJob->city}%");
			$qry->where('jobs.employment_type', $currentRowJob->outsourcing_type);
			$qry->where('jobs.min_exp', '>=', $currentRowJob->total_exp);
			$qry->where('jobs.max_exp', '<=', $currentRowJob->total_exp);
		});

		$alldatalist = $alldatalist->distinct('job_applications.id');
		$alldatalist = $alldatalist->groupBy('jobs.id');
		$alldatalist = $alldatalist->orderBy('jobs.id', 'DESC');
		// dd($alldatalist->toSql(), $alldatalist->getBindings());
		// dd($alldatalist->get());

		return DataTables::of($alldatalist)

			->editColumn('chkbox', function ($row) {
				return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
			})
			->editColumn('Requisition', function ($row) {

				$clients_name = "";
				$bgTextDecore = $strVal = '';
				$str = trim(@$row->title);
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

				$profile_detail_class = 'show-detail';
				$clients_name = ucwords(substr(@$row->clients_name, 0, 15));

				$logo_path = '';
				//$logo_path				=	url('img/client-default.png'); 	
				if (!empty($row->logo_logical_path)) {
					$logo_path = url('public/' . @$row->logo_logical_path);
				}

				$recommendation_id = '';
				$recommendation_id = $row->recommendation_id;
				$recommendation_text = '&nbsp;<i class="bi bi-house" style="color:#00CC00"></i>';

				if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
					$recommendation_text = '&nbsp;<i style="color: #FF0000; font-weight:bold" class="fa fa-users" aria-hidden="true"></i>';
					$profile_detail_class = 'client-show-detail';
					$clients_name = ucwords(substr(@$row->company_name, 0, 15));

					if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
						$logo_path = url('public/user-uploads/company-logo/' . @$row->logo);
					}
				}

				if (@$row->self_company == 1) {
					if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
						$logo_path = url('public/user-uploads/company-logo/' . @$row->logo);
					}
				}

				$icon = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="' . $profile_detail_class . '">
                      <div class="symbol-label ' . $bgTextDecore . ' border-radius-5">' . ucwords($strVal) . '</div>
                    </a>
                  </div>';

				if (!empty($logo_path)) {
					$icon = '<div data-row-id="' . $row->id . '"  class="symbol symbol-circle symbol-40px overflow-hidden me-2  ' . $profile_detail_class . ' border-radius-5">
                                        <a href="javascript:void(0);">
                                            <div class="symbol-label border-radius-5">
                                            	<img src="' . @$logo_path . '" alt="Company Logo" class="border-radius-5 w-100">
                                            </div>
                                        </a>
                                    </div>';
				}


				return $icon . '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="text-dark fw-bolder text-hover-primary fs-6 ' . $profile_detail_class . '"  data-toggle="tooltip1" title="' . ucwords(@$row->title) . '" >' . ucwords(substr(@$row->title, 0, 15)) . '</a> 
                    <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="fw-bold text-muted d-block fs-7 text-hover-primary ' . $profile_detail_class . '">' . $clients_name . '</a>
                  </div>';
			})
			->editColumn('Exp', function ($row) {
				return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">' . @$row->max_exp . '+</span>
                    <span class="text-muted fw-bold text-muted d-block fs-7">' . @$row->min_exp . '+</span>
                  </div>';
			})
			->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {
				$min_sal_mode = $max_sal_mode = $min_salary_currency = $max_salary_currency = $max_salary = $min_salary = "";
				$min_sal_mode = strtoupper(substr($salaryMode[$row->min_sal_mode], 0, 1));
				$max_sal_mode = strtoupper(substr($salaryMode[$row->max_sal_mode], 0, 1));
				$min_salary_currency = $currencySymbole[@$row->min_salary_currency];
				$max_salary_currency = $currencySymbole[@$row->max_salary_currency];
				$max_salary = GeneralMethod::thousandsCurrencyFormat($row->max_salary);
				$min_salary = GeneralMethod::thousandsCurrencyFormat($row->min_salary);
				return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">' . $min_salary_currency . $max_salary . '/' . $min_sal_mode . '</span>                    
                    <span class="fw-bold d-block fs-7">' . $min_salary_currency . $min_salary . '/' . $max_sal_mode . '</span>
                  </div>';
			})
			->editColumn('Location', function ($row) {

				// dd($row);
	
				$country_id = $country_name = '';
				$jobid = $row->id;
				$client_loc = DB::table('client_location_jobs')
					->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
					->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
					->select('client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
					->where('client_location_jobs.job_id', $jobid)
					->first();

				$country_id = @$client_loc->country_id;
				if (@$country_id > 0) {
					$country_name = GeneralMethod::getCountryName($country_id);
					foreach ($country_name as $cval) {
						$country_name = ucwords($cval['sortname']);
					}
				}

				$city = substr(@$client_loc->city, 0, 6);
				if (@$client_loc->country_id == 101) {
					$city = substr(@$client_loc->city_name, 0, 6);
				}



				$jobLocation = DB::table('client_location_jobs')
					->where('client_location_jobs.job_id', $jobid)->count();

				$jobCitySelected = DB::table('client_city_jobs')
					->where('client_city_jobs.job_id', $jobid)->count();

				if ($row->work_type == 'yes') {
					return ' <div class="d-flex flex-column">
						<span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="' . ucwords(@$client_loc->address) . '" >' . ucwords(substr(@$client_loc->address, 0, 10)) . '</span>
						<div class="d-flex align-items-center">
						  <span class="fw-bold text-muted d-block fs-7">Remote</span>
						  </div>
						</div>
					  </div>';
				} elseif ($row->work_type == 'no') {

					if ($jobCitySelected == 1 || $jobCitySelected == 0) {
						return ' <div class="d-flex flex-column">
							<span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="' . ucwords(@$client_loc->address) . '" >' . ucwords(substr(@$client_loc->address, 0, 10)) . '</span>
							<div class="d-flex align-items-center">
								  <span class="fw-bold text-muted d-block fs-7">' . ucwords(substr(@$city, 0, 10)) . '</span>
									&nbsp;|&nbsp;
								  <span class="fw-bold text-muted d-block fs-7">' . ucwords(substr(@$country_name, 0, 10)) . '</span>
								<div>
							  </div>
							</div>
						  </div>';
					} else {
						return ' <div class="d-flex flex-column">
							<span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="' . ucwords(@$client_loc->address) . '" >' . ucwords(substr(@$client_loc->address, 0, 10)) . '</span>
							<div class="d-flex align-items-center">
								  <span class="fw-bold text-muted d-block fs-7">Multiple</span>
								<div>
						  </div>
						</div>
					  </div>';
					}

				} elseif ($row->work_type == 'any') {

					if ($jobCitySelected == 1 || $jobCitySelected == 0) {
						return ' <div class="d-flex flex-column">
							<span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="' . ucwords(@$client_loc->address) . '" >' . ucwords(substr(@$client_loc->address, 0, 10)) . '</span>
							<div class="d-flex align-items-center">
								  <span class="fw-bold text-muted d-block fs-7">' . ucwords(substr(@$city, 0, 10)) . '</span>
									&nbsp;|&nbsp;
								  <span class="fw-bold text-muted d-block fs-7">' . ucwords(substr(@$country_name, 0, 10)) . '</span>
								<div>
							  </div>
							</div>
						  </div>';
					} else {
						return ' <div class="d-flex flex-column">
							<span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="' . ucwords(@$client_loc->address) . '" >' . ucwords(substr(@$client_loc->address, 0, 10)) . '</span>
						<div class="d-flex align-items-center">
						  <span class="fw-bold text-muted d-block fs-7">Remote</span>
						  &nbsp;|&nbsp;
								  <span class="fw-bold text-muted d-block fs-7">Multiple</span>
								<div>

						  </div>
						</div>
					  </div>';
					}

				}


			})
			->editColumn('Status', function ($row) use ($job_status) {

				$status = $active = $hold = $inactive = $class = $recommendation_id = '';
				$status = ucwords($row->status);
				$class = $job_status[$row->status];
				$recommendation_id = $row->recommendation_id;
				if ($status == 'Inactive') {
					$status = 'Closed';
				}
				$final_status = "";
				if ($row->final_status == 2 && @$row->to_company_id == auth()->user()->company_id) {
					$status = 'REMOVED <i class="fa fa-users" aria-hidden="true"></i>';
					$class = "closed-bg";
				}

				$ulink = '<a href="javascript.void(0);" data-row-id="' . @$row->id . '" class="d-flex flex-column ' . @$class . ' status_stage text-hover-success" data-toggle="modal" data-target="#status_stage">' . $status . '</a>';

				$recommendation_text = 'Custom';
				if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
					$recommendation_text = 'Client';
					$ulink = '<a href="javascript:void(0);" class="fs-6 ' . @$class . ' text-hover-success">' . $status . '</a>';
				}


				return '<div class="d-flex flex-column">
                       ' . $ulink . '
                      <span class="fw-bold text-muted d-block fs-7">R' . @$row->id . ' -' . $recommendation_text . '</span>
                    </div>';
			})
			->editColumn('Date', function ($row) {

				$total_positions = '';
				$total_positions = number_format($row->total_positions);

				$created_at = '';
				$created_at = $row->created_at;
				$newDate = date("d M, Y", strtotime($created_at));
				return '<div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">' . $newDate . '</span>
                        <span class="text-muted fw-bold d-block fs-7">' . $total_positions . '</span>
                    </div>';
			})
			->editColumn('HM', function ($row) use ($company_id) {

				$html = '';
				if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id)							// Direct Client Job  
				{

					$from_company_id = $row->from_company_id;

					$partnersCount = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('job_assign.job_id', '=', $row->id)->where('job_assign.company_id', '=', $from_company_id)->where('users.company_id', '=', $from_company_id)->count();
					$partnersData = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('job_assign.job_id', '=', $row->id)->where('job_assign.company_id', '=', $from_company_id)->where('users.company_id', '=', $from_company_id)->orderBy('users.name', 'ASC')->skip(0)->take(3)->get();

					$jobId = $row->id;
					$html = $temp = '';
					$html .= '<div class="d-flex flex-column"><div class="symbol-group symbol-hover">';
					foreach ($partnersData as $pval) {
						$image_path = url('public/avatar.png');
						if (!empty(@$pval->image) && strlen(@$pval->image) > 10)
							$image_path = url('public/user-uploads/team_profile_img/' . @$pval->image);
						$html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' . @$pval->name . '"><img alt="Pic" src="' . @$image_path . '"></div>';
					}
					if (in_array("add_team_on_requisition", $this->userPermissions)) {
						$html .= '<div data-row-id="' . $row->id . '"   class="symbol symbol-30 symbol-circle symbol-light client-hm-listing" data-bs-placement="top" data-toggle="tooltip2" title="More Comtact list.."><span class="symbol-label font-weight-bold">' . $partnersCount . '+</span></div>';
					}
					$html .= '</div></div>';

				} else {	//	Custom Client Job	
					$partnersCount = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('job_assign.job_id', '=', $row->id)->where('job_assign.company_id', '=', $company_id)->where('users.company_id', '=', $company_id)->count();
					$partnersData = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('job_assign.job_id', '=', $row->id)->where('job_assign.company_id', '=', $company_id)->where('users.company_id', '=', $company_id)->orderBy('users.name', 'ASC')->skip(0)->take(3)->get();

					$jobId = $row->id;
					$html = $temp = '';
					$html .= '<div class="d-flex flex-column"><div class="symbol-group symbol-hover">';
					foreach ($partnersData as $pval) {
						$image_path = url('public/avatar.png');
						if (!empty(@$pval->image) && strlen(@$pval->image) > 10)
							$image_path = url('public/user-uploads/team_profile_img/' . @$pval->image);
						$html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' . @$pval->name . '"><img alt="Pic" src="' . @$image_path . '"></div>';
					}
					$html .= '<div data-row-id="' . $row->id . '"   class="symbol symbol-30 symbol-circle symbol-light hm-listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list.."><span class="symbol-label font-weight-bold" >' . $partnersCount . '+</span></div>';
					$html .= '</div></div>';
				}
				return $html;
			})
			->editColumn('action', function ($row) use ($currentRowJob,$condidate_id) {
				$master_status_id = 8;
				$job_id = '';
				$job_id = $row->id;
				$cnt = 0;
				$cnt = $row->Rejected;
				$url_path = '';
				$url_path = url('/service_company/apptracker/' . $row->id . '/' . $master_status_id);
				if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
					$url_path = url('/service_company/apptracker/clientapp/' . $job_id . '/' . $master_status_id);
					$cnt = $row->thirdparty_Rejected;
				}
				// dd($condidate_id);
				$count = DB::table('rec_candidate_assign')->where('rec_candidate_assign.source_jobid', $row->id)->where('rec_candidate_assign.source_application_id',$condidate_id)->count();
				$cnt = number_format($cnt);
				if ($count == 0) {
					$cnt = '<b>.</b>';
					return ' <div class="d-flex my-4">
						<a href="javascript:void(0);" data-jobId="' . $row->id . '" data-conId="' . $currentRowJob->id . '"  class="btn btn-sm btn-primary request-send">Send request</a>
					</div> ';
				} else {
					return ' <div class="d-flex my-4">
						<a href="javascript:void(0);" class="text-dark">Request sent</a>
					</div> ';
				}
			})

			->rawColumns(['chkbox', 'Requisition', 'Exp', 'Salary', 'Location', 'Status', 'Date', 'HM', 'action'])
			->addIndexColumn()
			->make(true);
	}



	public function recommandedrequestcondidate(Request $request)
	{

		$company_id = auth()->user()->company_id;

		$job = Job::findOrFail($request->jobId);



		$source_from_company_id = $company_id;
		$source_from_user_id = auth()->user()->id;
		//   $source_from_id	 = '';
		$source_from_type = 1;
		$source_company_id = $job->company_id;
		//   $source_user_id	 = '';
		$source_application_id = (int) $request->conId;
		$source_jobid = (int) $request->jobId;

		$values = array('source_from_company_id' => $source_from_company_id, 'source_from_user_id' => $source_from_user_id, 'source_from_type' => $source_from_type, 'source_company_id' => $source_company_id, 'source_application_id' => $source_application_id, 'source_from_id' => $source_application_id, 'source_jobid' => $source_jobid);
		// dd($values);

		$query = DB::table('rec_candidate_assign')->insert($values);
		// $query = true;
		if ($query) {
			$res = [
				'status' => true,
			];
		} else {
			$res = [
				'status' => false,
			];
		}

		return $res;


	}




}
