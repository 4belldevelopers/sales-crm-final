<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Files;
use App\Helper\Reply;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\Company\StoreCompany;
use App\Models\Job;
use App\Models\Package;
use App\Models\CompanyPackage;
use Carbon\Carbon;
use App\Http\Requests\Company\UpdatePakage;
use App\Models\User;
use App\Models\Role;
use App\Models\Client;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Storage;
use App\Models\Companies;
use App\Models\JobCategory;
use App\Models\Country;
use App\Http\Requests\Company\UpdateCompany;
use Illuminate\Support\Facades\Auth;
use Crypt;
use App\Helper\GeneralMethod;
use Illuminate\Support\Facades\DB;


class SuperAdminTeamController extends SuperAdminBaseController
{
    public function __construct()
   	{
				parent::__construct();
				$this->pageTitle = 'menu.team';
				$this->pageIcon = 'icon-people';
				view()->share('calling_codes', $this->getCallingCodes());
    }

   	public function index()
	{
				$this->usersCount 	= User::with('company')->company()->count();
				$this->users 		= User::with('company')->company()->get();
				return view('super-admin.team.index', $this->data);
    }
	public function show() 
	{ 
		
				// abort_if(! $this->user->cans('view_team'), 403);

				$company_id		= 	auth()->user()->company_id; // $this->user->roles[0]->company_id;	
				// dd($company_id	);
				$users 			=	User::select('users.id', 'users.name', 'users.email', 'roles.display_name', 'users.created_at', 'users.status', 'users.image', 'users.working_status', 'users.root_user',
												'roles.name as roles_name','users_profile.designation');
				$users			=	$users->join('role_user', 'role_user.user_id', 'users.id');
				$users			=	$users->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id');
				$users			=	$users->join('roles', 'roles.id', 'role_user.role_id');				
				$users			=	$users->where('users.company_id', '=', $company_id);		
				if (\request('search') != ""){
												$search		=	request('search');	
												$users		=	$users->where('users.name', 'LIKE', "%$search%");
				}	
											// dd($users->get());					
		 		return DataTables::of($users)						
						->editColumn('chkbox', function ($row) {
							return '<div class="form-check form-check-sm form-check-custom form-check-solid"><input class="form-check-input form-check-input1" type="checkbox" value="1"></div>';
						})
						->editColumn('Name', function ($row) {      
								
								$bgTextDecore 		= $strVal = '';
								$str = trim(@$row->name);
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
								$icon	= '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 show-detail-team" data-row-id="' . $row->id . '" style="float:left" >
													 <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="show-detail-team" id="kt_activities3_toggle" >
														<div class="symbol-label fs-3 ' . @$bgTextDecore . '  border-radius-5">' . ucwords(@$strVal) . '</div>
													</a>
											</div>';
								  if(!empty($row->image)){
										$imagePath	=	'';
										$imagePath	=	url('public/user-uploads/team_profile_img/'.@$row->image);
										$icon	= '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5 show-detail-team" data-row-id="' . $row->id . '" style="float:left">
														<a href="javascript:void(0);">
															<div class="symbol-label border-radius-5">
															<img src="'.$imagePath.'" alt="User Image" class="border-radius-5 w-100">
															</div>
														</a>
													</div>';
								  }	
								$departmentArr		=	'------';	
								return $icon.'<div class="d-flex flex-column show-detail-team" data-row-id="' . $row->id . '">
										<a href="javascript:void(0);" data-row-id="' . $row->id . '" class="text-dark fw-bolder text-hover-primary fs-6 show-detail-team"  data-toggle="tooltip1" data-html="true" title="' . ucwords(@$row->name) . '">' . ucwords(substr(@$row->name, 0, 15)) . '</a>
										<a href="javascript:void(0);" data-row-id="' . $row->id . '" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-detail-team">'.@$departmentArr.'</a>
									  </div>
									  ';
						})
						->editColumn('designation', function ($row) {      
								
								
								$departmentArr		=	'------';	
								return '<div class="d-flex flex-column">
										<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-html="true" title="' . ucwords(@$row->designation) . '">' . ucwords(@$row->designation) . '</a>
									  </div>
									  ';
						})
						->editColumn('Role', function ($row){
							return '<div class="d-flex flex-column update-user-role" data-row-id="' . $row->id . '">
										<span class="text-dark fw-bolder fs-6">'.ucwords($row->display_name).'
											<span class="svg-icon svg-icon-primary cursor-pointer update-role">
												<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
												<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
													<rect x="0" y="0" width="24" height="24"/>
													<path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
													<rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
												</g>
												</svg>
											</span>
										</span>
									</div>';
						})
						->editColumn('Status', function ($row){
										$chk		=	$disabled		=	'';
										$status		=	$row->status;
										if($status == 'active'){
											$chk		=	'checked="checked"';			
										}
										$disabled		=	$row->root_user == 1 ? 'disabled="disabled"':'';
										return '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
													<input class="form-check-input chkStatus" type="checkbox" value="active" name="staus" '.@$chk.' onclick="handleClick(this, '.$row->id.');" '.$disabled.'>
												</div>';
						})
						->editColumn('WorkingStatus', function ($row){ 
			
									$chk		=	$disabled		=	'';
									$working_status		=	$row->working_status;
									if($working_status == 1){
										$chk		=	'checked="checked"'; 			
									}
									$disabled		=	$row->root_user == 1 ? 'disabled="disabled"':'';
									return '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
												<input id="working_status_'.$row->id.'" class="form-check-input chkStatus" type="checkbox" value="1" name="working_status" '.@$chk.' onclick="WorkingStatus(this, '.$row->id.');"   '.@$disabled.'  ></div>';
					
						})			
						->editColumn('AddedDate', function ($row) {
							$created_at		=	$row->created_at;
							$strtotime		=	strtotime($created_at);
							$newDate 		=	date('d-M-Y',$strtotime);
							$newDateTime 	=	date('h:i A',$strtotime);
							$temp			=	"";	
							return ' <div class="d-flex flex-column">
												   <span class="text-dark fw-bolder fs-6">'.@$newDate.'</span>
												   <span class="text-muted fw-bold d-block fs-7">'.@$newDateTime.'</span>
												</div>';					
						})
						->rawColumns(['chkbox', 'Name', 'Role', 'Status', 'WorkingStatus', 'AddedDate',  'role_name', 'action','designation'])
						->addIndexColumn()
						->make(true);	
										
								
    }
	public function viewMore($id)  
	{			
				$this->countryName	=	'';
				$this->stateName	=	'';
				$this->userId		=	$id;	
				$this->users 		=	User::join('companies', 'companies.id', '=', 'users.company_id')
										->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')						
										->select('users.id', 'users.company_id', 'users.name', 'users.email', 'users.mobile', 'users.created_at', 'users.image', 'users.status', 'companies.company_name', 'companies.website', 'companies.zoom_status', 'companies.google_status', 'companies.microsoft_status', 'users_profile.country', 'users_profile.state', 'users_profile.city', 'users_profile.designation', 'users_profile.skills')
										->with('company')
										->where('users.id', '=', $id)
										->company()
										->first();		
				if($this->users->country > 0){
					$countryArr 		= GeneralMethod::getCountryName($this->users->country);	
					foreach($countryArr as $key=>$val){
						$this->countryName	= $val['name']; 
					}
				}
				if($this->users->state > 0){
					$stateArr 		= GeneralMethod::getStateName($this->users->country, $this->users->state);	
					foreach($stateArr as $key=>$val){
						$this->stateName	= $val['name']; 
					}
				}		
	    		$view = view('super-admin.team.show', $this->data)->render();
        		return Reply::dataOnly(['status' => 'success', 'view' => $view]);
	}
	public function jobdata(Request $request)
	{
		$type_check = $request->input('team', 'default');
		// abort_if(!$this->user->cans('view_jobs'), 403);

		$company_id = auth()->user()->company_id;
		$userId						=	$request->userId;	
		$chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
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
				->leftJoin('job_assign', 'job_assign.job_id', 'jobs.id')
				->join('companies', 'companies.id', 'jobs.company_id')
				->leftJoin('add_job_wise_condidates as job_applications', 'job_applications.job_id', '=', 'jobs.id')
				->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
				->where('jobs.id', '>', '0');
			  
			  // $alldatalist = $alldatalist->where('job_assign.user_id', '=', @$userId);



           if($type_check=='default' || auth()->user()->id==@$userId){
			$alldatalist = $alldatalist->where('jobs.company_id', '=', $company_id);
		   
			$search = '';
			$search = request('search');
			if (\request('search') != "") {
				$search = request('search');
				$alldatalist = $alldatalist->where('jobs.title', 'LIKE', "%$search%");
			}

			if (\request('search') != "") {
				$alldatalist = $alldatalist->orWhere(function ($query) use ($company_id, $search) {
					return $query->where('recommendation_relation.to_company_id', '=', $company_id)->where('recommendation_relation.final_status', '=', 1)->where('jobs.title', 'LIKE', "%$search%");
				});
				$alldatalist = $alldatalist->orWhere(function ($query) use ($company_id, $search) {
					return $query->where('recommendation_relation.to_company_id', '=', $company_id)->where('recommendation_relation.final_status', '=', 2)->where('jobs.title', 'LIKE', "%$search%");
				});
			} else {
				$alldatalist = $alldatalist->orWhere(function ($query) use ($company_id, $search) {
					return $query->where('recommendation_relation.to_company_id', '=', $company_id)->where('recommendation_relation.final_status', '=', 1);
				});
				$alldatalist = $alldatalist->orWhere(function ($query) use ($company_id, $search) {
					return $query->where('recommendation_relation.to_company_id', '=', $company_id)->where('recommendation_relation.final_status', '=', 2);
				});
			}
		  }else{
			$alldatalist->where('job_assign.user_id', '=', @$userId);
			$alldatalist->groupBy('jobs.id');
			$alldatalist->orderBy('jobs.id', 'DESC');
		  }

		} else {  																	// END IF MEANS [ ADMINISTRATOR ROLE ]	 


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
					DB::raw("SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview"),
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
					'recommendation_relation.id as recommendation_id',
					'recommendation_relation.from_company_id',
					'recommendation_relation.final_status',
					'recommendation_relation.to_company_id'
				)
				->join('clients', 'clients.id', 'jobs.client_id')
				->join('companies', 'companies.id', 'jobs.company_id')
				->join('job_assign', function ($join) {
					$join->on('job_assign.job_id', '=', 'jobs.id');
					$join->on('job_assign.company_id', '=', DB::raw("'" . auth()->user()->company_id . "'"));
					$join->on('job_assign.user_id', '=', DB::raw("'" . $this->user->id . "'"));

				})
				->leftJoin('add_job_wise_condidates as job_applications', 'job_applications.job_id', '=', 'jobs.id')
				->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
				->where('jobs.id', '>', '0');

			$alldatalist = $alldatalist->where('jobs.company_id', '=', $company_id);
			$search = '';
			$search = request('search');
			if (\request('search') != "") {
				$search = request('search');
				$alldatalist = $alldatalist->where('jobs.title', 'LIKE', "%$search%");
			}

			if (\request('search') != "") {
				$alldatalist = $alldatalist->orWhere(function ($query) use ($company_id, $search) {
					return $query->where('recommendation_relation.to_company_id', '=', $company_id)->where('recommendation_relation.final_status', '=', 1)->where('jobs.title', 'LIKE', "%$search%");
				});
				$alldatalist = $alldatalist->orWhere(function ($query) use ($company_id, $search) {
					return $query->where('recommendation_relation.to_company_id', '=', $company_id)->where('recommendation_relation.final_status', '=', 2)->where('jobs.title', 'LIKE', "%$search%");
				});
			} else {
				$alldatalist = $alldatalist->orWhere(function ($query) use ($company_id, $search) {
					return $query->where('recommendation_relation.to_company_id', '=', $company_id)->where('recommendation_relation.final_status', '=', 1);
				});
				$alldatalist = $alldatalist->orWhere(function ($query) use ($company_id, $search) {
					return $query->where('recommendation_relation.to_company_id', '=', $company_id)->where('recommendation_relation.final_status', '=', 2);
				});
			}

		}

		if (\request('client_id') != "") {
			$client_id = request('client_id');
			$alldatalist = $alldatalist->where('jobs.client_id', $client_id);
		}

		$alldatalist = $alldatalist->distinct('job_applications.id');
		$alldatalist = $alldatalist->groupBy('jobs.id');
		$alldatalist = $alldatalist->orderBy('jobs.id', 'DESC');

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
			->editColumn('Partners', function ($row) use ($company_id) {


				$html = '';
				if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id)							// Direct Client Job  
				{


					$partnersCount = DB::table('job_assign')
						->join('users', 'users.id', '=', 'job_assign.user_id')
						->select('users.id', 'users.name', 'users.image')
						->where('job_assign.job_id', '=', $row->id)
						->where('job_assign.company_id', '=', $company_id)
						->where('users.company_id', '=', $company_id)
						->count();

					$partnersData = DB::table('job_assign')
						->join('users', 'users.id', '=', 'job_assign.user_id')
						->select('users.id', 'users.name', 'users.image')
						->where('job_assign.job_id', '=', $row->id)
						->where('job_assign.company_id', '=', $company_id)
						->where('users.company_id', '=', $company_id)
						->orderBy('users.name', 'ASC')
						->skip(0)->take(3)->get();

					$jobId = $row->id;



					$html .= '<div class="d-flex flex-column">
                    						<div class="symbol-group symbol-hover">';
					$temp = '';
					foreach ($partnersData as $pval) {

						$logo_path = url('public/avatar.png');
						if (!empty(@$pval->image) && strlen(@$pval->image) > 10) {
							$logo_path = url('public/user-uploads/team_profile_img/' . @$pval->image);
						}

						$html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' . @$pval->name . '">
                            						<img alt="Pic" src="' . @$logo_path . '">
                          						</div>';

					}
					$html .= '<div data-row-id="' . $row->id . '"   class="symbol symbol-30 symbol-circle symbol-light team-listing" data-bs-placement="top" data-toggle="tooltip2" title="More team list..">
														<span class="symbol-label font-weight-bold">' . $partnersCount . '+</span>
													  </div>';
					$html .= '</div>
                    					</div>';



				} else													//	Custom Client Job  
				{




					$jobId = "";
					$jobId = $row->id;

					$recommendedDataListCnt = DB::table('recommendation_relation');
					$recommendedDataListCnt = $recommendedDataListCnt->select('companies.id', 'companies.company_name', 'companies.logo');
					$recommendedDataListCnt = $recommendedDataListCnt->join('companies', 'companies.id', 'recommendation_relation.to_company_id');
					$recommendedDataListCnt = $recommendedDataListCnt->join('jobs', 'jobs.id', 'recommendation_relation.job_id');
					//$recommendedDataListCnt 	=	$recommendedDataListCnt->leftJoin('td_industries', 'td_industries.ind_id', 'companies.industries');
					/*$recommendedDataListCnt 	=	$recommendedDataListCnt->leftJoin('job_applications', function($join) use ($jobId)
								{
										$join->on('job_applications.thirdparty_company_id', '=', 'companies.id');
										$join->on('job_applications.job_id', '=', 'jobs.id' );
								});*/
					$recommendedDataListCnt = $recommendedDataListCnt->where('companies.id', '!=', $company_id);
					$recommendedDataListCnt = $recommendedDataListCnt->where('recommendation_relation.final_status', 1);
					$recommendedDataListCnt = $recommendedDataListCnt->where('recommendation_relation.job_id', '=', $jobId);
					$recommendedDataListCnt = $recommendedDataListCnt->groupBy('companies.id');
					$recommendedDataListCnt = $recommendedDataListCnt->count();


					$recommendedDataList = DB::table('recommendation_relation');
					$recommendedDataList = $recommendedDataList->select('companies.id', 'companies.company_name', 'companies.logo');
					$recommendedDataList = $recommendedDataList->join('companies', 'companies.id', 'recommendation_relation.to_company_id');
					$recommendedDataList = $recommendedDataList->join('jobs', 'jobs.id', 'recommendation_relation.job_id');
					//$recommendedDataList 	=	$recommendedDataList->leftJoin('td_industries', 'td_industries.ind_id', 'companies.industries');
					$recommendedDataList = $recommendedDataList->leftJoin('job_applications', function ($join) use ($jobId) {
						$join->on('job_applications.thirdparty_company_id', '=', 'companies.id');
						$join->on('job_applications.job_id', '=', 'jobs.id');
					});
					$recommendedDataList = $recommendedDataList->where('companies.id', '!=', $company_id);
					$recommendedDataList = $recommendedDataList->where('recommendation_relation.final_status', 1);
					$recommendedDataList = $recommendedDataList->where('recommendation_relation.job_id', '=', $jobId);
					$recommendedDataList = $recommendedDataList->groupBy('companies.id');
					$recommendedDataList = $recommendedDataList->skip(0)->take(3)->get();


					$html = '<div class="d-flex flex-column">
			<div class="symbol-group symbol-hover">';

					foreach ($recommendedDataList as $pval) {

						$image_path = url('img/client-default.png');
						if (!empty(@$pval->logo) && strlen(@$pval->logo) > 10) {
							$image_path = url('public/user-uploads/company-logo/' . @$pval->logo);
						}



						$html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="' . @$pval->company_name . '"><img alt="Pic" src="' . @$image_path . '"></div>';
					}


					$html .= '
			<div data-row-id="' . $row->id . '" class="symbol symbol-30 symbol-circle symbol-light spartner-listing" data-bs-placement="top" data-toggle="tooltip2" title="" data-bs-original-title="More users list..">
				<span class="symbol-label font-weight-bold" >' . @$recommendedDataListCnt . '+</span>
			</div>
			</div>
		</div>';






				}

				return $html;

			})
			->editColumn('TOT', function ($row) {

				$url_path = $total_application = $inreview_counter = '';
				$url_path = url('/service_company/apptracker/' . $row->id . '/all');


				$job_id = $row->id;
				$applicationCounter = DB::table('add_job_wise_condidates as job_applications')
					->select('job_applications.id')
					->where('job_applications.job_id', '=', $row->id)
					->where('job_applications.thirdparty_company_id', '=', 0)
					->orWhere(function ($query) use ($job_id) {
						return
							$query->where('job_applications.thirdparty_company_id', '>', 0)
								->where('job_applications.job_id', '=', $job_id)
								->where('job_applications.master_status_id', '!=', 8)
								->where('job_applications.master_status_id', '!=', 9);
					})
					->count();

				$inreview_counter = DB::table('add_job_wise_condidates as job_applications')
					->select('job_applications.id')
					->where('job_applications.job_id', '=', $row->id)
					->where('job_applications.thirdparty_company_id', '=', 0)
					->where('job_applications.master_status_id', '=', 8)
					->count();

				if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
					$url_path = url('/service_company/apptracker/clientapp/' . $row->id . '/all');

					$applicationCounter = DB::table('add_job_wise_condidates as job_applications')
						->select('job_applications.id')
						->where('job_applications.job_id', '=', $row->id)
						->where('job_applications.thirdparty_company_id', '=', $row->to_company_id)
						->count();
					$inreview_counter = DB::table('job_applications')
						->select('job_applications.id')
						->where('job_applications.job_id', '=', $row->id)
						->where('job_applications.thirdparty_company_id', '=', $row->to_company_id)
						->where('job_applications.master_status_id', '=', 8)
						->count();



				}
				$applicationCounter = number_format($applicationCounter);
				$inreview_counter = number_format($inreview_counter);


				return '<div class="d-flex flex-column">
                        <a href="' . $url_path . '" class="text-dark fw-bolder fs-6 text-hover-primary">' . @$applicationCounter . '</a>
                        <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">' . $inreview_counter . '</a>
                    </div>';


			})
			->editColumn('SOU', function ($row) {
				$job_id = '';
				$master_status_id = 1;
				$cnt = 0;
				$job_id = $row->id;
				$cnt = $row->Sourcing;
				$url_path = $total_application = $inreview_counter = '';
				$url_path = url('/service_company/apptracker/' . $job_id . '/' . $master_status_id);
				if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
					$url_path = url('/service_company/apptracker/clientapp/' . $job_id . '/' . $master_status_id);
					$cnt = $row->thirdparty_Sourcing;
				}
				$cnt = number_format($cnt);
				if ($cnt == 0) {
					return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
				} else {
					return '<div class="d-flex flex-column"><a href="' . $url_path . '" class="text-dark fw-bolder fs-6 text-hover-primary">' . @$cnt . '</a></div>';
				}
			})
			->editColumn('SCR', function ($row) {
				$master_status_id = 2;
				$job_id = '';
				$job_id = $row->id;
				$cnt = 0;
				$cnt = $row->Screening;
				$url_path = '';
				$url_path = url('/service_company/apptracker/' . $job_id . '/' . $master_status_id);
				if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
					$url_path = url('/service_company/apptracker/clientapp/' . $job_id . '/' . $master_status_id);
					$cnt = $row->thirdparty_Screening;
				}
				$cnt = number_format($cnt);
				if ($cnt == 0) {
					$cnt = '<b>.</b>';
					return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
				} else {
					return '<div class="d-flex flex-column"><a href="' . $url_path . '" class="text-dark fw-bolder fs-6 text-hover-primary">' . @$cnt . '</a></div>';
				}
			})
			->editColumn('EVA', function ($row) {
				$master_status_id = 3;
				$job_id = '';
				$job_id = $row->id;
				$cnt = 0;
				$cnt = $row->Interview;
				$url_path = '';
				$url_path = url('/service_company/apptracker/' . $job_id . '/' . $master_status_id);
				if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
					$url_path = url('/service_company/apptracker/clientapp/' . $job_id . '/' . $master_status_id);
					$cnt = $row->thirdparty_Interview;
				}
				$cnt = number_format($cnt);
				if ($cnt == 0) {
					$cnt = '<b>.</b>';
					return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
				} else {
					return '<div class="d-flex flex-column"><a href="' . $url_path . '" class="text-dark fw-bolder fs-6 text-hover-primary">' . @$cnt . '</a></div>';
				}
			})
			->editColumn('NEO', function ($row) {
				$master_status_id = 4;
				$job_id = '';
				$job_id = $row->id;
				$cnt = 0;
				$cnt = $row->Negotiation;
				$url_path = '';
				$url_path = url('/service_company/apptracker/' . $job_id . '/' . $master_status_id);
				if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
					$url_path = url('/service_company/apptracker/clientapp/' . $job_id . '/' . $master_status_id);
					$cnt = number_format($row->thirdparty_Negotiation);
				}
				$cnt = number_format($cnt);
				if ($cnt == 0) {
					$cnt = '<b>.</b>';
					return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
				} else {
					return '<div class="d-flex flex-column"><a href="' . $url_path . '" class="text-dark fw-bolder fs-6 text-hover-primary">' . @$cnt . '</a></div>';
				}
			})
			->editColumn('OFF', function ($row) {
				$master_status_id = 5;
				$cnt = 0;
				$job_id = '';
				$job_id = $row->id;
				$cnt = $row->Offered;
				$url_path = '';
				$url_path = url('/service_company/apptracker/' . $job_id . '/' . $master_status_id);
				if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
					$url_path = url('/service_company/apptracker/clientapp/' . $job_id . '/' . $master_status_id);
					$cnt = $row->thirdparty_Offered;
				}
				$cnt = number_format($cnt);
				if ($cnt == 0) {
					$cnt = '<b>.</b>';
					return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
				} else {
					return '<div class="d-flex flex-column"><a href="' . $url_path . '" class="text-dark fw-bolder fs-6 text-hover-primary">' . @$cnt . '</a></div>';
				}
			})
			->editColumn('JOI', function ($row) {

				$master_status_id = 6;
				$job_id = '';
				$job_id = $row->id;
				$cnt = 0;
				$cnt = $row->Joined;
				$url_path = '';
				$url_path = url('/service_company/apptracker/' . $row->id . '/' . $master_status_id);

				if ($row->recommendation_id > 0 && @$row->to_company_id === auth()->user()->company_id) {
					$url_path = url('/service_company/apptracker/clientapp/' . $job_id . '/' . $master_status_id);
					$cnt = $row->thirdparty_Joined;
				}
				$cnt = number_format($cnt);
				if ($cnt == 0) {
					$cnt = '<b>.</b>';
					return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
				} else {
					return '<div class="d-flex flex-column"><a href="' . $url_path . '" class="text-dark fw-bolder fs-6 text-hover-primary">' . @$cnt . '</a></div>';
				}
			})
			->editColumn('REJ', function ($row) {
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
				$cnt = number_format($cnt);
				if ($cnt == 0) {
					$cnt = '<b>.</b>';
					return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
				} else {
					return '<div class="d-flex flex-column"><a href="' . $url_path . '" class="text-dark fw-bolder fs-6 text-hover-primary">' . @$cnt . '</a></div>';
				}
			})
			->rawColumns(['chkbox', 'Requisition', 'Exp', 'Salary', 'Location', 'Status', 'Date', 'HM', 'Partners', 'TOT', 'SOU', 'SCR', 'EVA', 'NEO', 'OFF', 'JOI', 'REJ'])
			->addIndexColumn()
			->make(true);
	}
	public function edit($id)
	{      
	
			
			$this->statelist 		=	array();
			$this->team 			=	User::join('companies', 'companies.id', '=', 'users.company_id')
											->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')						
											->select(
														'users.id', 'users.company_id', 'users.name', 'users.email', 'users.mobile', 'users.created_at', 'users.image', 'users.status', 'users.image', 
														'companies.company_name', 'companies.website',
														'users_profile.country', 'users_profile.state', 'users_profile.city', 'users_profile.city_id', 'users_profile.designation', 'users_profile.skills'  
													)
											->where('users.id', '=', $id)
											->first();
				
				$this->categories 		= JobCategory::all();
				$this->companyType		= GeneralMethod::companyType();
				$this->department		= GeneralMethod::department();
				$this->countryArr 		= GeneralMethod::getAllCountryList();	
				if($this->team->country > 0){
					$this->statelist 		= GeneralMethod::statelist($this->team->country);	
				}	 
				
				$this->cityListArr       			=	'';			
				if($this->team->country == 101) 
				{
					$statelist 					=	DB::table('state_list')
													->select('state_list.id', 'state_list.state as name', DB::raw("101 as country_id"))
													->orderBy('state_list.state')
													->get()->toArray();
					$this->statelist       		= 	json_decode(json_encode($statelist), true);	
								
					$citylist 					=	DB::table('city_list')
													->select('city_list.city_id as id', 'city_list.city as name')
													->where('city_list.state_id', $this->team->state)
													->orderBy('city_list.city')
													->get()->toArray();
					$this->cityListArr       		= json_decode(json_encode($citylist), true);
					
				}	
					
				
				$this->cities = GeneralMethod::allCities(); 
					// dd($this->cities);
				
			return view('super-admin.team.edit', $this->data);
	
	}
	public function WorkingStatus(Request $request)
	{				
								
				$packageStatus  =	GeneralMethod::validatePackage($this->customActivePackage, 'USERS');				 
				
				$company_id		=	auth()->user()->company_id;
				$chkStatus		=	$request->chkStatus;
				$userId			=	$request->userId;
						
				if($chkStatus == 'true'){						
							
							if($packageStatus['STATUS']==1){
								if($packageStatus['EXPIRE']== 'false'){
									if($packageStatus['BALANCE'] > 0 ){

										DB::table('users')->where('company_id', @$company_id)->where('id', @$userId)->limit(1) ->update(array('working_status' => 1));
										$activeUserCount	=	DB::table('users')->select('users.id')->where('users.company_id', @$company_id)->where('users.root_user', '!=', 1)->where('users.working_status', '=', 1)->count();
										$users_balance		=	$this->customActivePackage['users_quantity']-$activeUserCount;										
										DB::table('package_active')->where('package_active.company_id', @$company_id)->limit(1)
										->update(array('users_used' 	=> $activeUserCount, 'users_balance' => $users_balance) ); 
										return Reply::dataOnly(['status' => 'success']);	

									}else{
										return Reply::dataOnly(['status' => 'info', 'message'=> 'Team user working status can not update because your package balance max limit reached , please contact to Administrator.']);	
									}									
								}else{
									return Reply::dataOnly(['status' => 'info', 'message'=> 'Team user working status can not update because your package expired , please contact to Administrator.']);		
								}								  
							}else{							
								return Reply::dataOnly(['status' => 'info', 'message'=> 'Team user working status can not update because your package status is Inactive , please contact to Administrator.']);	
							}										 
				}else{				
								
					DB::table('users')->where('company_id', @$company_id)->where('id', @$userId)->limit(1) ->update(array('working_status' => 0));  							
					$activeUserCount	=	DB::table('users')->select('users.id')->where('users.company_id', @$company_id)->where('users.root_user', '!=', 1)->where('users.working_status', '=', 1)->count();
					$users_balance		=	$this->customActivePackage['users_quantity']-$activeUserCount;
					if($users_balance < 0){
						$users_balance	=	0;
					}										
					DB::table('package_active')->where('package_active.company_id', @$company_id)->limit(1)
					->update( array('users_used' 	=> $activeUserCount, 'users_balance' => $users_balance ) ); 							
					return Reply::dataOnly(['status' => 'success']); 		
									
				}
		 	 
				
	}	
	public function statusUpdate(Request $request)
	{
			$company_id		=	auth()->user()->company_id;
			$chkStatus		=	$request->chkStatus;
			$userId			=	$request->userId;
			
			if($chkStatus == 'true'){
							DB::table('users')->where('company_id', @$company_id)->where('id', @$userId)->limit(1) ->update(array('status' => 'active'));   		 
			}else{
				
							DB::table('users')->where('company_id', @$company_id)->where('id', @$userId)->limit(1) ->update(array('status' => 'inactive'));   			 
			}			 
			return Reply::dataOnly(['status' => 'success']);
	}
	public function store(Request $request)
	{
	
				$company_id		=	auth()->user()->company_id;
				// dd($company_id);
				$count			=	0;
				$count			=	DB::table('users')->select('users.id')->where('email', '=', $request->email)->count();

				if($count == 0){
							if(!empty($request->name) && !empty($request->name)){
										
										$user 								=	new User();
										$user->name 						=	$request->name;
										$user->email 						=	$request->email;
										$user->email_verification_code 		=	str_random(40);
										
										$user->status 						=	'inactive';
										$user->working_status 				=	0;
										
										if ($request->hasFile('logo')) {
											$user->image = Files::upload($request->logo,'team_profile_img');
										}
					
										$user->save();
								
										//attach role
										$user->roles()->attach($request->role_id);
								
										if($user->id > 0){
													
													if($request->invitation_roles > 0){
														$values = array('user_id' => $user->id, 'role_id' => $request->invitation_roles);
															DB::table('role_user')->insert($values);	
														}	
													$users_profile = array('company_id' => $company_id, 'user_id' => $user->id, 'country' => 101, 'created_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d') );
													DB::table('users_profile')->insert($users_profile);				
											
													$Url 			=	url('/teamReg/'.base64_encode($company_id).'/'.base64_encode($user->id));				
													$to 			=	$request->email;
													$subject 		=	"Account Credential";
													$message 		= '
																		<html>
																			<head>
																				<title>Password Reset</title>
																			</head>
																			<body>
																					<p>Hi</p>
																					<table>
																						<tr><th><a href="'.$Url.'" target="_blank">Click here </a></th></tr>
																						<tr><th>Or Copy link</th></tr>
																						<tr><th>'.$Url.'</th></tr>
																					</table>
																			</body>
																		</html>
																		';
																		// dd($message);
																							
													$to				=	$request->email;
													$cc				=	'abu@4bell.com';
													$from			=	'vibhanshu@4bell.com';
													$subject		=	'Account Credential';
													$sendEmail		=	GeneralMethod::sentEmailUsingSMTP($to, $cc, $subject, $message, $from);	
													$message		=	'Team member created successfully. set password link has been sent to team member email';	
													$type			=	'success';			

														
								}
					}else{
							$message				=	'User detail can not be null !';	
							$type					=	'warning';
					}								
						
				}else{
						$count					=	0;
						$count					=	DB::table('users')->select('users.id')->where('email', '=', $request->email)->where('status', '=', 'inactive')->count();
						if($count == 1){
								$message				=	'Already user register & account not activated';	
								$type					=	'warning';	
						}else{
								$message				=	'Already user register';	
								$type					=	'warning';		
						}	
				}
				
				return Reply::dataOnly(['status' => 'success', 'msgType'=> $type, 'message' => $message]);	


	} 
	public function update(Request $request, $id)
	{
		
		    // dd($request->all());
		    $user 				= User::find($id);
        	$user->name 		= $request->name;
			$user->mobile       = $request->mobile;
			$user->email       = $request->email;
			$user->calling_code       = $request->phone_country_code;
			if ($request->hasFile('logo')) {
				
				$file = $request->file('logo');
				$companyNameR2 = DB::table('companies')
				->where('id',auth()->user()->company_id)
				->value('company_name');   // change column if needed
				$companyNameR2 = strtolower(trim($companyNameR2 ?? 'unknown'));
				$companyNameR2 = preg_replace('/[^a-z0-9_-]/i','_', $companyNameR2);
				$companyNameRTwo = $companyNameR2 . '_' . auth()->user()->company_id;
				
				$filename = uniqid('JERO_logo').'.'.$file->getClientOriginalExtension();

                $tempPath = $file->storeAs('Companies/'.$companyNameRTwo.'/Images', $filename,'r2'); // STORAGE/app/temp
                $fileUrl = "https://file.jero.works/Companies/{$companyNameRTwo}/Images/{$filename}";
			    
				// $filename = time() . '.' . $file->getClientOriginalExtension();
				// $filePath = 'Images/' . $filename;

				// Storage::disk('s3')->put($filePath, file_get_contents($file));
	
				//  $fileUrl = Storage::disk('s3')->url($filePath);
				 $user->image = $fileUrl;
                 // 	$user->image = Files::upload($request->logo,'team_profile_img');
        	}
       		$user->save();

		    $company_id      	=	$user->company_id;

			$users_profile_count	= DB::table('users_profile')->select('*')->where('user_id', $id)->count();
			$users_team_count	= DB::table('client_contact_info')->select('*')->where('contact_person_email', $request->email)->count();
		
		if($users_team_count > 0)
			{
			$insertArr = array('contact_person_name' =>$request->name, 'contact_person_designation'=>$request->designation);
			DB::table('client_contact_info')->where('contact_person_email', $request->email)->limit(1) ->update($insertArr); 
			}

			
			   $stateId = '';
				$cityID = '';
				$countryID = '';
				$city = '';
				$work_city = @$request->kt_tagify_work_location;
				$work_city_all = json_decode($work_city, true);
				// dd($work_city_all);
				if (@$request->kt_tagify_work_location != "") {
					foreach ($work_city_all as $cityVal) {
						$parts = explode('|', $cityVal['value']);
						// Trim to remove any leading or trailing whitespace
						$cityName = trim($parts[0]);
						$countrName = trim($parts[2]);
						$city_row = DB::table('city_list')
						->join('state_list', 'state_list.id', '=', 'city_list.state_id')
						->join('countries', 'countries.id', '=', 'state_list.country_id')
						->select('city_list.city_id', 'countries.id as country_id')
						->where('city_list.city','!=','')
						->where('city_list.city', '=', $cityName)
						->where('countries.country_name', '=', $countrName)
						->first();
						if($city_row){
							$cityID = (@$city_row) ? $city_row->city_id : '';
							$countryID = (@$city_row) ? $city_row->country_id : '';
							$stateId = GeneralMethod::stateId($cityID);

							$values 				= array(
								'company_id' 	=> $company_id, 
								'user_id' 	=> $id, 
								'designation' 			=> $request->designation,
								'city_id' 		=> $cityID, 
								'country' 		=> $countryID, 
								'state' 		=> $stateId
							);

						}
						
					}
				}
								
			if($users_profile_count > 0)
			{			
				//UPDATE
				DB::table('users_profile')->where('company_id', @$company_id)->where('user_id', @$id)->limit(1) ->update($values);  
			}
			else
			{
				//INSERT				
				DB::table('users_profile')->insert($values);
			}

			GeneralMethod::userIndexingupload($id);

		$message		=	"update successfully";
		return Reply::dataOnly(['status' => 'success', 'message'=> $message]);   
	
	}
	public function editrole($id)  
	{
		    // dd($id);
			
			$company_id					=	'';
			$company_id					=	auth()->user()->company_id; 
			$this->roleslist 			=	DB::table('roles')
											->select('roles.id', 'roles.display_name', 'roles.name', 'roles.priority', 'roles.description')
											->where('roles.company_id', '=', $company_id)
											//->where('roles.id', '=', $id)
											->orderBy('roles.name', 'ASC')
											->get();
			
			$this->selrole 				=	DB::table('role_user')
											->select('role_user.role_id')
											->where('role_user.user_id', '=', $id)
											//->where('roles.id', '=', $id)
											//->orderBy('roles.name', 'ASC')
											->first();
			
			$this->id					=	$id;	
			// dd($this->data);
														
			$view = view('super-admin.team.editrole', $this->data)->render();
			// dd($view);
			return Reply::dataOnly(['status' => 'success', 'view' => $view]);

	}
	public function roleupdate(Request $request)
	{
		$company_id		=	auth()->user()->company_id;
		$cuser_id		=	$request->cuser_id;
		$user_role		=	$request->user_role;
		
		if(!empty($cuser_id) && !empty($user_role))
		{
			DB::table('role_user')
					->where('user_id', @$cuser_id)
						->limit(1) 
					->update(array('role_id' => $user_role));
					
			return Reply::dataOnly(['status' => 'success']);		  		 
		}
		else
		{
			return Reply::dataOnly(['status' => 'warning', 'message' => 'something worng, please try again one more time ']);	
		}		 
							
	}
	public function create()
	{
		//abort_if(! $this->user->cans('add_team'), 403);

		$this->roles = Role::all();
		return view('super-admin.team.create', $this->data);
	}
	//----------appdata function in un-use code 
	public function appdata(Request $request) 
	{
				
				$userId 			=	$mstatus_id 		=	$feedback_status 	=	'';
				$userId 			=	$request->userId;				
				
		 		$salaryMode 		=	GeneralMethod::salaryMode();
				$currencySymbole 	=	GeneralMethod::currencySymbole(); 
		 
				$mstatus_id 		=	$request->mstatus_id;
				$interviewStatus 	=	array();
				$interviewStatus 	=	DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();
       		   
				$chkAdminRole 		=	DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
				$feedback_status 	=	GeneralMethod::feedback_status();
				$company_id			=	auth()->user()->company_id;
						
				// 'application_status.status',
				$jobApplications 	= DB::table('job_applications')
				->select('job_applications.*', 'jobs.title', 'job_locations.location', 'clients.name as client_name', 'users.name as posted_by','qualification_master.name as qualification_name', 'master_status.name', 'master_status.css_style','add_job_wise_condidates.id as wiseId'
				)
				->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
				->leftjoin('master_status', function($join){
					$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
				})
				->join('jobs', function($join){
					$join->on('jobs.id', 'add_job_wise_condidates.job_id');
				})
				->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')
				->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
				->leftjoin('clients', 'clients.id', 'jobs.client_id')
				->leftjoin('users', 'users.id', 'job_applications.submitted_by')
				->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
				->leftjoin('application_status', 'application_status.id', 'job_applications.status_id')
				->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
				->leftjoin('state_list', 'state_list.id', 'job_applications.state');

					if($chkAdminRole == 0)
					{	
						$jobApplications 		=	$jobApplications->join('job_assign', 'job_assign.job_id', 'jobs.id');
						$jobApplications 		=	$jobApplications->where('job_assign.user_id', $userId);
					}
		 	
					if(\request('search') != "")
					{
							$search					=	request('search');	
							$jobApplications		=	$jobApplications->where('job_applications.full_name', 'LIKE', "%$search%");
					}
		

					$jobApplications			=	$jobApplications->where('add_job_wise_condidates.submitted_by', '=', $userId);
															
					$jobApplications 			=	$jobApplications->groupBy('add_job_wise_condidates.id');		
					$jobApplications 			=	$jobApplications->orderBy('add_job_wise_condidates.id', 'DESC');		
					$dataTable 					=	DataTables::of($jobApplications)
					->editColumn('chkbox', function ($row) {
						return '<div class="form-check form-check-sm form-check-custom form-check-solid">
							<input class="form-check-input" type="checkbox" value="1" />
						  </div>';
					})
					->editColumn('Name', function ($row) {
								$bgTextDecore = $strVal = '';
								if(!empty(@$row->full_name))
								{
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
									$profile_sidebar_css		=	'show-application-detail';
									if($row->thirdparty_company_id > 0)
									{
										$profile_sidebar_css	=	'client-show-application-detail';	
									}
			
									return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
											<a href="javascript:void(0);"   data-row-id="' . @$row->wiseId . '" class="'.$profile_sidebar_css.'">
											<div class="symbol-label ' . @$bgTextDecore . ' border-radius-5">' . strtoupper(@$strVal) . '</div>
											</a>
										</div>                  
										<div class="d-flex flex-column">
											<a href="javascript:void(0);"  data-row-id="' . @$row->wiseId . '"  class="text-dark fw-bolder text-hover-primary fs-6 '.$profile_sidebar_css.'" data-toggle="tooltip" title="'.ucwords(@$row->full_name).'">' . ucwords(substr(@$row->full_name, 0, 5)) . '</a>
											<a href="javascript:void(0);"  data-row-id="' . @$row->wiseId . '"  class="text-muted fw-bold d-block fs-7 text-hover-primary '.$profile_sidebar_css.'" data-toggle="tooltip" title="'.ucwords(@$row->current_designation).'">' . ucwords(substr(@$row->current_designation, 0, 7)) . '</a>
										</div>';
									
								}else{
										return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">&nbsp;</div>';				  				
								}	  
                    })
					->editColumn('Contacts', function ($row) {
						return '<div class="d-flex flex-column">
								<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . @$row->phone . '</a>
								<a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="'.@$row->email.'">' . substr(@$row->email, 0, 7) . '</a>
							</div>';
       				})
					->editColumn('Exp', function ($row) {
							return '<div class="d-flex flex-column">
									<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$row->total_exp . '+</a>
									<span class="text-muted fw-bold d-block fs-7">' . @$row->rel_exp . '+</span>
								</div>';
                    })
					->editColumn('Salary', function ($row) use($salaryMode, $currencySymbole) {
		
								$current_ctc_cur	=	"";
								$current_ctc_cur	=	$currencySymbole[@$row->current_ctc_cur];
								
								$current_ectc_cur		=	"";
								$current_ectc_cur		=	$currencySymbole[@$row->current_ectc_cur];
								
								$ctc 					=	"";
								$ctc 					=	GeneralMethod::thousandsCurrencyFormat($row->ctc); 
								$ectc 					=	"";
								$ectc 					=	GeneralMethod::thousandsCurrencyFormat($row->ectc); 
								
								$current_ctc_mode		=	"";
								$current_ctc_mode		= substr($salaryMode[@$row->current_ctc_mode], 0, 1);
								
								$current_ectc_mode		=	"";
								$current_ectc_mode		= substr($salaryMode[@$row->current_ectc_mode], 0, 1);
											
							return '<div class="d-flex flex-column">
									<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.@$current_ctc_cur.@$ctc.'/'.$current_ctc_mode.'</a>
									<span class="text-muted fw-bold d-block fs-7">'.$current_ectc_cur.@$ectc.'/'.$current_ectc_mode.'</span>
								</div>';
                    })
					->editColumn('NoticePeriod', function ($row) {
								$np = $row->np_id;
								if (empty($np)) {
									$np = 'N/A';
								}
								if ($row->serving_notice_period_mode == 0) {
									if (!empty($row->np_id)) {
										$np = $row->np_id . '  Days';
									}
								} else {
									$np = $row->np_id;
								}
								return '<div class="d-flex flex-column">
										<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . @$np . '</a>
										<span class="text-muted fw-bold d-block fs-7">' . ucwords(substr(@$row->current_company, 0, 10)) . '</span>  
									</div>';
                    })
					->editColumn('Qualifaction', function ($row) {
						return ' <div class="d-flex flex-column">
								<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . ucwords(substr(@$row->qualification_name, 0, 10)) . '</a>
								<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' . strtoupper(@$row->skills) . '">' . strtoupper(substr(@$row->skills, 0, 15)) . '</span> 
							</div>';
					})
					->editColumn('Interview', function ($row) {
								return '<div class="form-check form-check-sm form-check-custom form-check-solid">
										<input class="form-check-input" type="checkbox" value="1" />
									</div>';
					})
					->editColumn('Location', function ($row) {
						
						$country			= 	'';  
						$row_country_name 	=	'';
								
						if (empty($row->country)) {
							$row_country_name = '----';
						} 
						else
						{
									if(!empty(@$row->country))
									{
											if (is_numeric(@$row->country)) {
													$country_name_Arr	= GeneralMethod::getCountryName(@$row->country);	
													foreach($country_name_Arr as $cval){
														$row_country_name	= ucwords($cval['name']);	
													}
											}              
									}
						}
						
						$state				=	'';
						$row_state_name 	=	'';
						if (empty($row->state)) {
							$row_state_name = '----';
						} 
						else 
						{
								if(@$row->country == 101)
								{
									$row_state_name 	=	@$row->state_name;
								}
								else
								{
										$state 		= @$row->state;
										if(is_numeric(@$row->country) && is_numeric($state))
										{
											if( $state > 0){
												$state_name_Arr	= GeneralMethod::getStateName(@$row->country, $state);	
												foreach($state_name_Arr as $sval){
													$row_state_name		= ucwords($sval['name']);	
												}	
											}
										}	
									
								}
											
						}
			
									
						$city				=	'';
						$row_city_name 		=	'';
						
						if(@$row->country == 101)
						{
							$row_city 				= DB::table('city_list')->select('*')->where('city_id', @$row->city_id)->first();
							$row_city_name 			= @$row_city->city;
						}
						else
						{
							$row_city_name = @$row->city;	
						}
                
           
			
									
						return '<div class="d-flex flex-column">
									<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' . ucfirst($row_city_name) . '">' . ucfirst(substr($row_city_name, 0, 6)) . '</a>
									<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' . ucwords($row_state_name) . ' | ' . strtoupper($row_country_name) . '">' . ucwords(substr($row_state_name, 0, 5)) . ' | ' . strtoupper(substr($row_country_name, 0, 2)) . '</span>
								</div>';
					})
					->editColumn('Source', function ($row) {
								$bgTextDecore			=	$strVal	=	'';
								$str					=	trim(@$row->posted_by);
								$str	= $strVal		=	strtolower($str[0]);
								$str					=	ord($str);
								if($str >=97 && $str <=101){
									$bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
								}else if($str >=102 && $str <=106){
									$bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
								}else if($str >=107 && $str <=111){
									$bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
								}else if($str >=112 && $str <=116){
									$bgTextDecore		=	'fs-3 bg-light-success text-success';	
								}else if($str >=117 && $str <=121){
									$bgTextDecore		=	'fs-3 bg-dark text-secondary';	
								}else {
									$bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
								}		
								
							return ' <div class="d-flex flex-column">
											<div class="symbol-group symbol-hover">
												<div class="symbol symbol-circle symbol-25px overflow-hidden me-2" data-toggle="tooltip" data-placement="top" title="'.@$row->posted_by.'" style="float:left">
													<a href="javascript:void(0);" data-row-id="'.$row->id.'" class="show-application-detail">
													<div data-row-id="'.$row->id.'"  class="symbol-label '.$bgTextDecore.' border-radius-5 show-application-detail fw-bolder" style="font-size: 13px!important;">'.ucwords($strVal).'</div>
													</a>
												</div>
											</div>
										</div>
										<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'. @$row->posted_by .'">'.ucwords(substr(@$row->posted_by, 0, 5)).'</span>';			
					})->editColumn('UploadDate', function ($row) {
						$UploadDate = $className = '';
						$UploadDate 				= @$row->created_at;
						$UploadDate 				= date("d M, Y", strtotime($UploadDate));
						return '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.@$UploadDate.'</a> </div>';
					})
					->editColumn('Requisitions', function ($row) {
						$jobtitle 				= $client_name = '';
						$jobtitle 				= ucwords(substr(@$row->title, 0, 10)); 
						$client_name 			= ucwords(substr(@$row->client_name, 0, 10));  
						
						return '<div class="d-flex flex-column"> 
									<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'. ucwords($jobtitle) .'">'. ucwords(substr($jobtitle, 0, 6)) .'</a> 
									<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'. ucwords($client_name) .'">'. ucwords(substr($client_name, 0, 6)) .'</span>  
								</div>';
					})
					->editColumn('Stage', function ($row) {
		
							$stage_default_css			=	'stage_sub_stage';
							if($row->thirdparty_company_id >0)
							{
								$stage_default_css		=	'client_stage_sub_stage';	
							}
							$name = '--------';
							if($row->master_status_id == 8){
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
											<a href="javascript.void(0);" data-row-id="' . @$row->wiseId . '" class="d-flex flex-column '.$stage_default_css.'" data-toggle="modal" data-target="#'.$stage_default_css.'">
												<span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1">' . ucwords(substr($row->name, 0, 10)) . '</span>
												<span class="text-muted fw-bold d-block fs-7">' . @$name . '</span>
											</a>
										</div>';
					});
				
					$rawColumn 		=	array('chkbox', 'Name', 'Contacts', 'Exp', 'Salary', 'NoticePeriod', 'Qualifaction', 'Interview', 'Location', 'Source', 'Stage', 'UploadDate', 'Requisitions');
					$dataTable 		=	$dataTable->rawColumns($rawColumn);
					$dataTable 		=	$dataTable->addIndexColumn()->make(true);
					return $dataTable;
	}
	public function intdata(Request $request)
	{	
			
			$userId 				=	$available_status		=	$feedback_status		=	$interview_mode_arr		=	'';
			$userId 				=	$request->userId;			
			
			$available_status		=	GeneralMethod::available_status();		
			$feedback_status		=	GeneralMethod::feedback_status();
			$interview_mode_arr		=	GeneralMethod::interview_mode();		


			$shedule 				=	DB::table('interview_schedules')->select('interview_schedules.id', 'interview_schedules.status', 'interview_schedules.schedule_date', 'interview_schedules.available_status', 'interview_schedules.reschedule','interview_schedules.feedback', 'interview_schedules.interview_mode', 'interview_schedules.schedule_from_time', 'interview_schedules.interviewers', 'interview_schedules.interview_type','interview_schedules.job_application_id', 'interview_mode.name as mode_name', 'interview_mode_lavel.name as mode_lavel_name', 'interview_schedules.average_star_rating', 
			'job_applications.current_designation', 'job_applications.company_id as current_company_id', 'job_applications.full_name', 'job_applications.thirdparty_company_id', 
			'job_applications.master_status_id', 'job_applications.child_status_id', 
			'users.name as source',
			'master_status.name as master_status_name',
			'candidate_status.name as child_status_name',
			'jobs.title as job_title', 'jobs.id as job_id',
			'companies.company_name',
			'master_status.css_style'				
)
		->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')  
		->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
				->join('jobs', function ($join){
					$join->on('jobs.id', 'add_job_wise_condidates.job_id');
				})
		->leftjoin('users', 'users.id', 'job_applications.submitted_by')
		// ->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
		->leftjoin('companies', 'companies.id', 'jobs.company_id')		
		->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
		->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
		->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
		->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage');			
		$shedule 			= $shedule->where('job_applications.submitted_by', '=', @$userId);
		$shedule->orderBy('interview_schedules.schedule_date', 'DESC');                             
		$shedule->get();          

        return DataTables::of($shedule)
			
			->editColumn('chkbox', function ($row) {
                    return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
            })
			->editColumn('Name', function ($row) {
								
					$bgTextDecore			=	$strVal	=	'';
					$str					=	trim(@$row->full_name);
					$str	= $strVal		=	strtolower($str[0]);
					$str					=	ord($str);
					if($str >=97 && $str <=101){
						$bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
					}else if($str >=102 && $str <=106){
						$bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
					}else if($str >=107 && $str <=111){
						$bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
					}else if($str >=112 && $str <=116){
						$bgTextDecore		=	'fs-3 bg-light-success text-success';	
					}else if($str >=117 && $str <=121){
						$bgTextDecore		=	'fs-3 bg-dark text-secondary';	
					}else {
						$bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
					}			
						
                    return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 custom-show-application-detail" style="float:left" data-row-id="'.$row->job_application_id.'" >
                    <a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'" class="custom-show-application-detail">
                      <div data-row-id="'.$row->job_application_id.'"  class="symbol-label '.$bgTextDecore.' border-radius-5 custom-show-application-detail">'.ucwords($strVal).'</div>
                    </a>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'" class="text-dark fw-bolder text-hover-primary fs-6 custom-show-application-detail" data-toggle="tooltip" title="'.ucwords(@$row->full_name).'">'.ucwords(substr(@$row->full_name, 0, 8)).'</a>
                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords(@$row->current_designation).'">'.ucwords(substr(@$row->current_designation, 0, 8)).'</span>
                  </div>';
            })
			->editColumn('Requisitions', function ($row) { 
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.ucwords($row->job_title).'">'.ucwords(substr($row->job_title, 0, 8)).'</a>
                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords($row->company_name).'">'.ucwords(substr($row->company_name, 0, 8)).'</span>
                  </div>';
            })
			->editColumn('InterviewMode', function ($row) {
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.ucwords($row->mode_name).'">'.ucwords(substr($row->mode_name, 0, 8)).'</a> 
                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords($row->mode_lavel_name).'">'.ucwords(substr($row->mode_lavel_name, 0, 8)).'</span> 
                  </div>';
            })
			->editColumn('Interviewers', function ($row) {
					
					$html			=	'';
					$interviewers	= $row->interviewers;
                    if(!empty($interviewers))
					{
						$interviewersArr 	=	explode(',', $interviewers);
						$interviewersData	=	DB::table('users')
												->select('users.id', 'users.name', 'users.image')
												->where('users.company_id', '=', $row->current_company_id)
												->whereIn('users.name', $interviewersArr)
												->get();
						if(!empty($interviewersData)){
							foreach($interviewersData as $intVal){
								$name	=	$image	=	'';
								$name	=	$intVal->name;
								$image	=	$intVal->image;
								
								$imagePath	=	'';
				  				$imagePath	=	url('public/user-uploads/team_profile_img/'.@$image);
								$html		.=	'<div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="'.@$name.'">
													<img alt="image" src="'.@$imagePath.'">
												</div>';
							}
						}							
					}
										
					return ' <div class="d-flex flex-column"><div class="symbol-group symbol-hover">'.$html.'</div>';
            })
			->editColumn('Source', function ($row) {
					
					$bgTextDecore			=	$strVal	=	'';
					$str					=	trim(@$row->source);
					$str	= $strVal		=	strtolower($str[0]);
					$str					=	ord($str);
					if($str >=97 && $str <=101){
						$bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
					}else if($str >=102 && $str <=106){
						$bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
					}else if($str >=107 && $str <=111){
						$bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
					}else if($str >=112 && $str <=116){
						$bgTextDecore		=	'fs-3 bg-light-success text-success';	
					}else if($str >=117 && $str <=121){
						$bgTextDecore		=	'fs-3 bg-dark text-secondary';	
					}else {
						$bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
					}		
					
                  return ' <div class="d-flex flex-column">
                  				<div class="symbol-group symbol-hover">
									<div class="symbol symbol-circle symbol-30px overflow-hidden me-2" style="float:left" data-toggle="tooltip" data-placement="top" title="Sonika Sharma">
										<a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'" class="show-application-detail">
											<div data-row-id="'.$row->job_application_id.'"  class="symbol-label '.$bgTextDecore.' border-radius-5 show-application-detail fw-bolder" style="font-size: 13px!important;">'.ucwords($strVal).'</div>
										</a>
									</div>
								</div>
                  			</div>
                    		<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords(@$row->source).'">'.ucwords(substr(@$row->source, 0, 5)).'</span>';
            })
			->editColumn('InterviewDate', function ($row) {
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.Carbon::parse($row->schedule_date)->format('d M, Y').'</a>
                    <span class="text-muted fw-bold d-block fs-7">'.Carbon::parse($row->schedule_date)->format('H:i a').'</span>
                  </div>';
            })
			->editColumn('Feedback', function ($row) use ($feedback_status, $available_status) {
					
					$available_status_name	=	'';
					if($row->available_status > 0){
						$available_status_name	=	@$available_status[@$row->available_status]['name'];   	
					}else{
						$available_status_name	=	@$available_status[6]['name']; 
					}
                    return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 show-feedback" data-toggle="tooltip" title="'.ucwords(@$feedback_status[@$row->feedback]['name']).'">'.ucwords(substr(@$feedback_status[@$row->feedback]['name'], 0, 12)).'</a>
                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords(@$available_status_name).'">'.ucwords(substr(@$available_status_name, 0, 12)).'</span>
                  </div>';
            })
			->editColumn('Rating', function ($row) {
					$imagePath				=	$text = 	'';
					$average_star_rating	=	'';
					$average_star_rating	=	$row->average_star_rating; 
					if($average_star_rating == 1){
						$imagePath	=	url('public/img/star/1.png');
						$text		=	'Bad';
					}else if($average_star_rating == 2){
						$imagePath	=	url('public/img/star/2.png');
						$text		=	'Poor';
					}else if($average_star_rating == 3){
						$imagePath	=	url('public/img/star/3.png');
						$text		=	'Average';
					}else if($average_star_rating == 4){
						$imagePath	=	url('public/img/star/4.png');
						$text		=	'Best';
					}else if($average_star_rating == 5){
						$imagePath	=	url('public/img/star/5.png');	
						$text		=	'Very Best';
					}else{
						$imagePath	=	url('public/img/star/5-blank.png');
						$text		=	'Blank';
					}
										
                    return ' <img src="'.@$imagePath.'" alt="image" style="width: 5.5rem">
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.@$text.'</span>';
            })	
			->editColumn('Stage', function ($row) {

					$stage_default_css			=	'interview_stage_sub_stage';
					if($row->thirdparty_company_id >0)
					{
						$stage_default_css		=	'interview_client_stage_sub_stage';	
					}
					$name 						=	'--------';
					if($row->master_status_id == 8){
						$name 					=	'';
					}
					if (!empty(@$row->child_status_id) && @$row->child_status_id > 0) {
						$childStatus 			=	'';
						$childStatus 			=	DB::table('candidate_status')->select('*')->where('id', $row->child_status_id)->first();
						if (!empty($childStatus)) {
							$name 				=	@$childStatus->name;
						}
					}
					return '<div class="d-flex flex-column">
								<a href="javascript.void(0);" data-row-id="' . @$row->job_application_id . '" class="d-flex flex-column '.$stage_default_css.'" data-toggle="modal">
									<span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1">' . substr(@$row->master_status_name, 0, 10) . '</span>
									<span class="text-muted fw-bold d-block fs-7">' . @$name . '</span>
								</a>
							</div>';
			})
			->rawColumns(['chkbox', 'Name', 'Requisitions', 'InterviewMode', 'Interviewers', 'Source', 'InterviewDate', 'Feedback', 'Rating', 'Stage'])
            ->make(true);
    }
	public function intdata_old(Request $request)
	{
		// abort_if(!$this->user->cans('view_schedule'), 403);

		$company_id = auth()->user()->company_id;
		$available_status = $feedback_status = $interview_mode_arr = '';

		$available_status = GeneralMethod::available_status();
		$feedback_status = GeneralMethod::feedback_status();
		$interview_mode_arr = GeneralMethod::interview_mode();
		$job_status_arr = GeneralMethod::job_status();

		$chkAdminRole = $this->user->hasRole('admin');
		// $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
		if (($chkAdminRole > 0) || (in_array("all_requisition", $this->userPermissions) && in_array("view_jobs", $this->userPermissions))) {

			$interviewShedule = DB::table('interview_schedules')
				->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
				->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
				->join('jobs', function ($join) use ($company_id) {
					$join->on('jobs.id', 'add_job_wise_condidates.job_id');
				})
				->leftjoin('users', 'users.id', 'job_applications.submitted_by')
				->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id')
				// ->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
				->leftjoin('companies', 'companies.id', 'jobs.company_id')
				->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')
				->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
				->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
				->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage')
				->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')                             // Append new code 
				->select('interview_schedules.id', 'interview_schedules.status', 'interview_schedules.schedule_date', 'interview_schedules.available_status', 'interview_schedules.reschedule','interview_schedules.feedback', 'interview_schedules.interview_mode', 'interview_schedules.schedule_from_time', 'interview_schedules.interviewers', 'interview_schedules.interview_type','interview_schedules.job_application_id', 'interview_mode.name as mode_name', 'interview_mode_lavel.name as mode_lavel_name', 'interview_schedules.average_star_rating', 
				'job_applications.current_designation', 'job_applications.company_id as current_company_id', 'job_applications.full_name', 'job_applications.thirdparty_company_id', 
				'job_applications.master_status_id', 'job_applications.child_status_id', 
				'users.name as source',
				'master_status.name as master_status_name',
				'candidate_status.name as child_status_name',
				'jobs.title as job_title', 'jobs.id as job_id',
				'companies.company_name',
				'master_status.css_style'					
				);

			$interviewShedule = $interviewShedule->where('jobs.company_id', '=', $company_id);

			

			if (\request('search') != "") {
				$search = request('search');
				$interviewShedule = $interviewShedule->orWhere(function ($query) use ($company_id, $search) {
					return
						$query->where('recommendation_relation.to_company_id', '=', $company_id)
							->where('job_applications.thirdparty_company_id', '=', $company_id);

				});
			} else {

				$interviewShedule = $interviewShedule->orWhere(function ($query) use ($company_id) {
					return
						$query->where('recommendation_relation.to_company_id', '=', $company_id)
							->where('job_applications.thirdparty_company_id', '=', $company_id);

				});

			}



		} else {


			$interviewShedule = DB::table('interview_schedules')
				->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
				->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
				->join('jobs', function ($join) use ($company_id) {
					$join->on('jobs.id', 'add_job_wise_condidates.job_id');
				})
				->join('users', 'users.id', 'job_applications.submitted_by')
				->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id')
				// ->join('jobs', 'jobs.id', 'job_applications.job_id')
				->join('job_assign', 'job_assign.job_id', 'jobs.id')
				->leftjoin('companies', 'companies.id', 'jobs.company_id')
				->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')
				->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
				->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
				->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage')
				->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')                             // Append new code 
				->select('interview_schedules.id', 'interview_schedules.status', 'interview_schedules.schedule_date', 'interview_schedules.available_status', 'interview_schedules.reschedule','interview_schedules.feedback', 'interview_schedules.interview_mode', 'interview_schedules.schedule_from_time', 'interview_schedules.interviewers', 'interview_schedules.interview_type','interview_schedules.job_application_id', 'interview_mode.name as mode_name', 'interview_mode_lavel.name as mode_lavel_name', 'interview_schedules.average_star_rating', 
				'job_applications.current_designation', 'job_applications.company_id as current_company_id', 'job_applications.full_name', 'job_applications.thirdparty_company_id', 
				'job_applications.master_status_id', 'job_applications.child_status_id', 
				'users.name as source',
				'master_status.name as master_status_name',
				'candidate_status.name as child_status_name',
				'jobs.title as job_title', 'jobs.id as job_id',
				'companies.company_name',
				'master_status.css_style'					
				);

			$interviewShedule = $interviewShedule->where('jobs.company_id', '=', $company_id)->where('job_assign.user_id', $this->user->id);

			if (\request('search') != "") {
				$search = request('search');
				$interviewShedule = $interviewShedule->orWhere(function ($query) use ($company_id, $search) {

					return
						$query->where('recommendation_relation.to_company_id', '=', $company_id)
							->where('job_applications.thirdparty_company_id', '=', $company_id)
							->where('job_assign.user_id', $this->user->id);
				});

			} else {
				$interviewShedule = $interviewShedule->orWhere(function ($query) use ($company_id) {
					return
						$query->where('recommendation_relation.to_company_id', '=', $company_id)
							->where('job_assign.user_id', $this->user->id)
							->where('job_applications.thirdparty_company_id', '=', $company_id);

				});
			}

		}

		$interviewShedule->orderBy('interview_schedules.schedule_date', 'DESC');
		$interviewShedule->get();
		// dd($interviewShedule->get());
		return DataTables::of($interviewShedule)

		->editColumn('chkbox', function ($row) {
			return '<div class="form-check form-check-sm form-check-custom form-check-solid">
			<input class="form-check-input" type="checkbox" value="1" />
		  </div>';
	    })
		->editColumn('Name', function ($row) {
						
			$bgTextDecore			=	$strVal	=	'';
			$str					=	trim(@$row->full_name);
			$str	= $strVal		=	strtolower($str[0]);
			$str					=	ord($str);
			if($str >=97 && $str <=101){
				$bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
			}else if($str >=102 && $str <=106){
				$bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
			}else if($str >=107 && $str <=111){
				$bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
			}else if($str >=112 && $str <=116){
				$bgTextDecore		=	'fs-3 bg-light-success text-success';	
			}else if($str >=117 && $str <=121){
				$bgTextDecore		=	'fs-3 bg-dark text-secondary';	
			}else {
				$bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
			}			
				
			return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 custom-show-application-detail" style="float:left" data-row-id="'.$row->job_application_id.'" >
			<a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'" class="custom-show-application-detail">
			  <div data-row-id="'.$row->job_application_id.'"  class="symbol-label '.$bgTextDecore.' border-radius-5 custom-show-application-detail">'.ucwords($strVal).'</div>
			</a>
		  </div>
		  <div class="d-flex flex-column">
			<a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'" class="text-dark fw-bolder text-hover-primary fs-6 custom-show-application-detail" data-toggle="tooltip" title="'.ucwords(@$row->full_name).'">'.ucwords(substr(@$row->full_name, 0, 8)).'</a>
			<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords(@$row->current_designation).'">'.ucwords(substr(@$row->current_designation, 0, 8)).'</span>
		  </div>';
	    })
	   ->editColumn('Requisitions', function ($row) { 
			return '<div class="d-flex flex-column">
			<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.ucwords($row->job_title).'">'.ucwords(substr($row->job_title, 0, 8)).'</a>
			<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords($row->company_name).'">'.ucwords(substr($row->company_name, 0, 8)).'</span>
		  </div>';
	    })
		->editColumn('InterviewMode', function ($row) {
				return '<div class="d-flex flex-column">
				<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.ucwords($row->mode_name).'">'.ucwords(substr($row->mode_name, 0, 8)).'</a> 
				<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords($row->mode_lavel_name).'">'.ucwords(substr($row->mode_lavel_name, 0, 8)).'</span> 
			</div>';
		})
		->editColumn('Interviewers', function ($row) {
				
				$html			=	'';
				$interviewers	= $row->interviewers;
				if(!empty($interviewers))
				{
					$interviewersArr 	=	explode(',', $interviewers);
					$interviewersData	=	DB::table('users')
											->select('users.id', 'users.name', 'users.image')
											->where('users.company_id', '=', $row->current_company_id)
											->whereIn('users.name', $interviewersArr)
											->get();
					if(!empty($interviewersData)){
						foreach($interviewersData as $intVal){
							$name	=	$image	=	'';
							$name	=	$intVal->name;
							$image	=	$intVal->image;
							
							$imagePath	=	'';
							$imagePath	=	url('public/user-uploads/team_profile_img/'.@$image);
							$html		.=	'<div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="'.@$name.'">
												<img alt="image" src="'.@$imagePath.'">
											</div>';
						}
					}							
				}
									
				return ' <div class="d-flex flex-column"><div class="symbol-group symbol-hover">'.$html.'</div>';
		})
		->editColumn('Source', function ($row) {
				
				$bgTextDecore			=	$strVal	=	'';
				$str					=	trim(@$row->source);
				$str	= $strVal		=	strtolower($str[0]);
				$str					=	ord($str);
				if($str >=97 && $str <=101){
					$bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
				}else if($str >=102 && $str <=106){
					$bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
				}else if($str >=107 && $str <=111){
					$bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
				}else if($str >=112 && $str <=116){
					$bgTextDecore		=	'fs-3 bg-light-success text-success';	
				}else if($str >=117 && $str <=121){
					$bgTextDecore		=	'fs-3 bg-dark text-secondary';	
				}else {
					$bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
				}		
				
			return ' <div class="d-flex flex-column">
							<div class="symbol-group symbol-hover">
								<div class="symbol symbol-circle symbol-30px overflow-hidden me-2" style="float:left" data-toggle="tooltip" data-placement="top" title="Sonika Sharma">
									<a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'" class="show-application-detail">
										<div data-row-id="'.$row->job_application_id.'"  class="symbol-label '.$bgTextDecore.' border-radius-5 show-application-detail fw-bolder" style="font-size: 13px!important;">'.ucwords($strVal).'</div>
									</a>
								</div>
							</div>
						</div>
						<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords(@$row->source).'">'.ucwords(substr(@$row->source, 0, 5)).'</span>';
		})
		->editColumn('InterviewDate', function ($row) {
				return '<div class="d-flex flex-column">
				<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.Carbon::parse($row->schedule_date)->format('d M, Y').'</a>
				<span class="text-muted fw-bold d-block fs-7">'.Carbon::parse($row->schedule_date)->format('H:i a').'</span>
			</div>';
		})
		->editColumn('Feedback', function ($row) use ($feedback_status, $available_status) {
				
				$available_status_name	=	'';
				if($row->available_status > 0){
					$available_status_name	=	@$available_status[@$row->available_status]['name'];   	
				}else{
					$available_status_name	=	@$available_status[6]['name']; 
				}
				return '<div class="d-flex flex-column">
				<a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 show-feedback" data-toggle="tooltip" title="'.ucwords(@$feedback_status[@$row->feedback]['name']).'">'.ucwords(substr(@$feedback_status[@$row->feedback]['name'], 0, 12)).'</a>
				<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords(@$available_status_name).'">'.ucwords(substr(@$available_status_name, 0, 12)).'</span>
			</div>';
		})
		->editColumn('Rating', function ($row) {
				$imagePath				=	$text = 	'';
				$average_star_rating	=	'';
				$average_star_rating	=	$row->average_star_rating; 
				if($average_star_rating == 1){
					$imagePath	=	url('public/img/star/1.png');
					$text		=	'Bad';
				}else if($average_star_rating == 2){
					$imagePath	=	url('public/img/star/2.png');
					$text		=	'Poor';
				}else if($average_star_rating == 3){
					$imagePath	=	url('public/img/star/3.png');
					$text		=	'Average';
				}else if($average_star_rating == 4){
					$imagePath	=	url('public/img/star/4.png');
					$text		=	'Best';
				}else if($average_star_rating == 5){
					$imagePath	=	url('public/img/star/5.png');	
					$text		=	'Very Best';
				}else{
					$imagePath	=	url('public/img/star/5-blank.png');
					$text		=	'Blank';
				}
									
				return ' <img src="'.@$imagePath.'" alt="image" style="width: 5.5rem">
				<span class="text-muted fw-bold text-muted d-block fs-7">'.@$text.'</span>';
		})	
		->editColumn('Stage', function ($row) {

	$stage_default_css			=	'interview_stage_sub_stage';
	if($row->thirdparty_company_id >0)
	{
		$stage_default_css		=	'interview_client_stage_sub_stage';	
	}
	$name 						=	'--------';
	if($row->master_status_id == 8){
		$name 					=	'';
	}
	if (!empty(@$row->child_status_id) && @$row->child_status_id > 0) {
		$childStatus 			=	'';
		$childStatus 			=	DB::table('candidate_status')->select('*')->where('id', $row->child_status_id)->first();
		if (!empty($childStatus)) {
			$name 				=	@$childStatus->name;
		}
	}
	return '<div class="d-flex flex-column">
				<a href="javascript.void(0);" data-row-id="' . @$row->job_application_id . '" class="d-flex flex-column '.$stage_default_css.'" data-toggle="modal">
					<span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1">' . substr(@$row->master_status_name, 0, 10) . '</span>
					<span class="text-muted fw-bold d-block fs-7">' . @$name . '</span>
				</a>
			</div>';
		})
		->rawColumns(['chkbox', 'Name', 'Requisitions', 'InterviewMode', 'Interviewers', 'Source', 'InterviewDate', 'Feedback', 'Rating', 'Stage'])
		->make(true);
	}
	public function joiningdata(Request $request)
    {
			$currency 			=	GeneralMethod::currency();
			$salaryMode 		=	GeneralMethod::salaryMode();
			$userId 			=	'';
			$userId 			=	$request->userId;				
				
			$jobApplications 	=	JobApplication::select('job_applications.id', 'job_applications.full_name', 'jobs.title', 'job_applications.current_designation', 'job_applications.ctc','job_applications.ectc', 'job_applications.joined_date', 'job_applications.offer_currency', 'job_applications.offer_mode', 'job_applications.offer_amount','master_status.name as master_status_name', 'add_job_wise_condidates.id as wiseId','job_applications.phone', 'job_applications.email', 'users.name as source', 'master_status.css_style','job_applications.thirdparty_company_id', 'job_applications.master_status_id', 'job_applications.child_status_id')->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
			->leftjoin('master_status', function($join){
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
			})
			->join('jobs', function($join){
				$join->on('jobs.id', 'add_job_wise_condidates.job_id');
			})->join('users', 'users.id', 'add_job_wise_condidates.submitted_by')
			->where('add_job_wise_condidates.master_status_id', 6)
			->where('add_job_wise_condidates.submitted_by', $userId);				
												
           return DataTables::of($jobApplications)
		   ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1">
                  </div>';
            })
			->editColumn('Name', function ($row) {
			
					$bgTextDecore			=	$strVal	=	'';
					$str					=	trim(@$row->full_name);
					$str	= $strVal		=	strtolower($str[0]);
					$str					=	ord($str);
					if($str >=97 && $str <=101){
						$bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
					}else if($str >=102 && $str <=106){
						$bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
					}else if($str >=107 && $str <=111){
						$bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
					}else if($str >=112 && $str <=116){
						$bgTextDecore		=	'fs-3 bg-light-success text-success';	
					}else if($str >=117 && $str <=121){
						$bgTextDecore		=	'fs-3 bg-dark text-secondary';	
					}else {
						$bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
					}
                return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="custom-show-application-detail">
                      <div class="symbol-label fs-3 '.$bgTextDecore.' text-warning border-radius-5">'.ucwords($strVal).'</div>
                    </a>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 custom-show-application-detail" data-toggle="tooltip" title="'.ucwords(@$row->full_name).'">'.ucwords(substr(@$row->full_name, 0, 7)).'</a> 
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'"  class="text-muted fw-bold d-block fs-7 text-hover-primary custom-show-application-detail" data-toggle="tooltip" title="'.ucwords(@$row->current_designation).'">'.ucwords(substr(@$row->current_designation, 0, 7)).'</a>
                  </div>';
            })
			->editColumn('Contact', function ($row) { 
                return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.@$row->phone.'</a>   
                    <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="'.@$row->email.'">'.substr(@$row->email, 0, 12).'</a>
                  </div>';
            })
			->editColumn('Requisitions', function ($row) { 
				
				$jobtitle		=	$row->title;
				$jobtitle		=	substr($jobtitle, 0, 15);
				$client_name	=	$row->client_name;
				$client_name	=	substr($client_name, 0, 15);	
				
                return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.$row->title.'">'.$jobtitle.'</a> 
                    <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="'.$row->client_name.'">'.$client_name.'</a>
                  </div>';
            })
			->editColumn('Source', function ($row) {
					
					$bgTextDecore			=	$strVal	=	'';
					$str					=	trim(@$row->source);
					$str	= $strVal		=	strtolower($str[0]);
 					$str					=	ord($str);
					if($str >=97 && $str <=101){
						$bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
					}else if($str >=102 && $str <=106){
						$bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
					}else if($str >=107 && $str <=111){
						$bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
					}else if($str >=112 && $str <=116){
						$bgTextDecore		=	'fs-3 bg-light-success text-success';	
					}else if($str >=117 && $str <=121){
						$bgTextDecore		=	'fs-3 bg-dark text-secondary';	
					}else {
						$bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
					}		
					
                return '<div class="d-flex flex-column">
							<div class="symbol-group symbol-hover">
								<div class="symbol symbol-circle symbol-30px overflow-hidden me-2" style="float:left" data-toggle="tooltip" title="'.$row->source.'">
									<a href="javascript:void(0);" data-row-id="'.$row->id.'" class="show-application-detail">
										<div data-row-id="'.$row->id.'"  class="symbol-label '.$bgTextDecore.' border-radius-5 show-application-detail" style="font-size: 13px!important;">'.ucwords($strVal).'</div>
									</a>
								</div>
							</div>
						</div>
						<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords(@$row->source).'">'.ucwords(substr(@$row->source, 0, 5)).'</span>';
            })
			->editColumn('Offer', function ($row)  use($currency, $salaryMode) {
		
			 		$offer_currency		=	$offer_mode			=	'';
					if(!empty($row->offer_currency)){
						$offer_currency	=	$currency[$row->offer_currency];
					}
					
					if(!empty($row->offer_mode)){
						$offer_mode	=	$salaryMode[$row->offer_mode]; 
					}					
                 	return '<div class="d-flex flex-column">
                         <span class="text-dark fw-bolder fs-6">'.$offer_currency.' '.$row->offer_amount.' '.$offer_mode.'</span>
                     </div>';  		
		    })
			->editColumn('Joining', function ($row) {
			
               		$joined_date		=	$joined_time	=	$date	=	'';
					$date				=	@$row->joined_date;
					if(!empty($date)){
						$joined_date 	= date("d-M-y", strtotime($date));
						$joined_time 	= date("H:i a", strtotime($date));
					}
					return '<div class="d-flex flex-column">
							 <span class="text-dark fw-bolder fs-6">'.$joined_date.'</span>
							 <span class="text-muted fw-bold d-block fs-7">'.$joined_time.'</span>
						 </div>';
            })
			->editColumn('Stage', function ($row) {

						$stage_default_css			=	'joining_stage_sub_stage';
						if($row->thirdparty_company_id >0)
						{
							$stage_default_css		=	'joining_client_stage_sub_stage';	
						}
						$name 						=	'--------';
						if($row->master_status_id == 8){
							$name 					=	'';
						}
						if (!empty(@$row->child_status_id) && @$row->child_status_id > 0) {
							$childStatus 			=	'';
							$childStatus 			=	DB::table('candidate_status')->select('*')->where('id', $row->child_status_id)->first();
							if (!empty($childStatus)) {
								$name 				=	@$childStatus->name;
							}
						}
		
					return '<div class="d-flex flex-column">
								<a href="javascript.void(0);" data-row-id="' . @$row->id . '" class="d-flex flex-column '.$stage_default_css.'" data-toggle="modal">
									<span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1">' . substr(@$row->master_status_name, 0, 10) . '</span>
									<span class="text-muted fw-bold d-block fs-7">' . @$name . '</span>
								</a>
							</div>';
            })
			->rawColumns(['chkbox', 'Name', 'Contact', 'Requisitions', 'Source',  'Offer', 'Joining', 'Stage'])	
            ->addIndexColumn()
            ->make(true);
    }
public function joiningdata_old(Request $request)
	{
		// abort_if(!$this->user->cans('view_joining'), 403);

		$company_id = auth()->user()->company_id;
		$currency = GeneralMethod::currency();
		$salaryMode = GeneralMethod::salaryMode();

		$job_status_arr = GeneralMethod::job_status();

		$chkAdminRole = $this->user->hasRole('admin');
		// $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
		if (($chkAdminRole > 0) || (in_array("all_requisition", $this->userPermissions) && in_array("view_jobs", $this->userPermissions))) {

			$joiningData = DB::table('job_applications');
			$joiningData = $joiningData->select(
				'job_applications.id',
				'add_job_wise_condidates.job_id',
				'job_applications.full_name',
				'job_applications.current_designation',
				'job_applications.proposed_ctc',
				'job_applications.any_offer',
				'job_applications.document',
				'job_applications.sub_for_approval',
				'offer_job_applications.offer_approved',
				'offer_job_applications.offer_release',
				'offer_job_applications.offer_acceptance',
				'offer_job_applications.joined',
				'offer_job_applications.joined_date',
				'offer_job_applications.offer_currency',
				'offer_job_applications.offer_mode',
				'job_applications.phone',
				'job_applications.email',
				'offer_job_applications.offer_amount',
				'add_job_wise_condidates.master_status_id',
				'add_job_wise_condidates.child_status_id',
				'add_job_wise_condidates.thirdparty_company_id',
				'jobs.title',
				'clients.name as client_name',
				'users.name as posted_by',
				'users.image as posted_by_image',
				'master_status.name',
				'master_status.css_style',
				'jobs.status as job_status',
				'recommendation_relation.id as recommendation_id',
				'recommendation_relation.from_company_id',
				'recommendation_relation.final_status',
				'recommendation_relation.to_company_id',
				'uc.company_name as uc_company_name',
				'add_job_wise_condidates.id as wiseId'
			)
			->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
			->join('master_status', function($join){
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
			})
			->join('jobs', function($join){
				$join->on('jobs.id', 'add_job_wise_condidates.job_id');
			})
			->leftJoin('companies as uc', function($join){
				$join->on('uc.id', 'add_job_wise_condidates.company_id');
			});
			// $joiningData = $joiningData->join('jobs', 'jobs.id', 'job_applications.job_id');
			$joiningData = $joiningData->join('clients', 'clients.id', 'jobs.client_id');
			$joiningData = $joiningData->join('users', 'users.id', 'job_applications.submitted_by');
			// $joiningData = $joiningData->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id');
			// $joiningData = $joiningData->join('master_status', 'master_status.id', 'job_applications.master_status_id');
			$joiningData = $joiningData->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id');
			$joiningData = $joiningData->join('offer_job_applications', 'offer_job_applications.application_id', 'job_applications.id');
			$joiningData = $joiningData->where('offer_job_applications.company_id', '=', $company_id);

			if (\request('search') != "") {

				$search = request('search');

				$joiningData = $joiningData->where(function ($query) use ($company_id, $search) {
					return
						$query->where('jobs.company_id', '=', $company_id)
							->where('add_job_wise_condidates.master_status_id', '=', 6);
				});

				$joiningData = $joiningData->orWhere(function ($query) use ($company_id, $search) {
					return
						$query->where('recommendation_relation.to_company_id', '=', $company_id)
							->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)
							->where('add_job_wise_condidates.master_status_id', '=', 6);
				});

			} else {

				$joiningData = $joiningData->where(function ($query) use ($company_id) {
					return
						$query->where('jobs.company_id', '=', $company_id)
							->where('add_job_wise_condidates.master_status_id', '=', 6);
				});


				$joiningData = $joiningData->orWhere(function ($query) use ($company_id) {
					return
						$query->where('recommendation_relation.final_status', '=', 1)
							->where('recommendation_relation.to_company_id', '=', $company_id)
							->where('add_job_wise_condidates.master_status_id', '=', 6)
							->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id);
				});

			}


		} else {


			$joiningData = DB::table('job_applications');
			$joiningData = $joiningData->select(
				'job_applications.id',
				'add_job_wise_condidates.job_id',
				'job_applications.full_name',
				'job_applications.current_designation',
				'job_applications.proposed_ctc',
				'job_applications.any_offer',
				'job_applications.document',
				'offer_job_applications.sub_for_approval',
				'offer_job_applications.offer_approved',
				'offer_job_applications.offer_release',
				'offer_job_applications.offer_acceptance',
				'offer_job_applications.joined',
				'offer_job_applications.joined_date',
				'offer_job_applications.offer_currency',
				'offer_job_applications.offer_mode',
				'job_applications.phone',
				'job_applications.email',
				'offer_job_applications.offer_amount',
				'add_job_wise_condidates.master_status_id',
				'add_job_wise_condidates.child_status_id',
				'add_job_wise_condidates.thirdparty_company_id',
				'jobs.title',
				'clients.name as client_name',
				'users.name as posted_by',
				'users.image as posted_by_image',
				'master_status.name',
				'master_status.css_style',
				'jobs.status as job_status',
				'recommendation_relation.id as recommendation_id',
				'recommendation_relation.from_company_id',
				'recommendation_relation.final_status',
				'recommendation_relation.to_company_id',
				'uc.company_name as uc_company_name',
				'add_job_wise_condidates.id as wiseId'
			)
			->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
			->join('master_status', function($join){
				$join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
			})
			->join('jobs', function($join){
				$join->on('jobs.id', 'add_job_wise_condidates.job_id');
			})
			->leftJoin('companies as uc', function($join){
				$join->on('uc.id', 'add_job_wise_condidates.company_id');
			});
			// $joiningData = $joiningData->join('jobs', 'jobs.id', 'job_applications.job_id');
			$joiningData = $joiningData->join('job_assign', 'job_assign.job_id', 'jobs.id');
			$joiningData = $joiningData->join('clients', 'clients.id', 'jobs.client_id');
			$joiningData = $joiningData->join('users', 'users.id', 'job_applications.submitted_by');
			$joiningData = $joiningData->leftjoin('companies as uc', 'uc.id', '=', 'users.company_id');
			// $joiningData = $joiningData->join('master_status', 'master_status.id', 'job_applications.master_status_id');
			$joiningData = $joiningData->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id');
			$joiningData = $joiningData->join('offer_job_applications', 'offer_job_applications.application_id', 'job_applications.id');
			$joiningData = $joiningData->where('offer_job_applications.company_id', '=', $company_id);

			if (\request('search') != "") {

				$search = request('search');

				$joiningData = $joiningData->where(function ($query) use ($company_id, $search) {
					return
						$query->where('jobs.company_id', '=', $company_id)
							->where('add_job_wise_condidates.master_status_id', '=', 6)
							->where('job_assign.user_id', $this->user->id)
							->where('job_applications.full_name', 'LIKE', "%$search%");
				});

				$joiningData = $joiningData->orWhere(function ($query) use ($company_id, $search) {
					return
						$query->where('recommendation_relation.to_company_id', '=', $company_id)
							->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id)
							->where('job_assign.user_id', $this->user->id)
							->where('job_applications.master_status_id', '=', 6)
							->where('job_applications.full_name', 'LIKE', "%$search%");
				});

			} else {

				$joiningData = $joiningData->where(function ($query) use ($company_id) {
					return
						$query->where('jobs.company_id', '=', $company_id)
							->where('job_assign.user_id', $this->user->id)
							->where('add_job_wise_condidates.master_status_id', '=', 6);
				});


				$joiningData = $joiningData->orWhere(function ($query) use ($company_id) {
					return
						$query->where('recommendation_relation.final_status', '=', 1)
							->where('recommendation_relation.to_company_id', '=', $company_id)
							->where('job_assign.user_id', $this->user->id)
							->where('add_job_wise_condidates.master_status_id', '=', 6)
							->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id);
				});

			}




		}




		$joiningData = $joiningData->orderBy('job_applications.id', 'DESC');

		 // dd($joiningData->get());

		return DataTables::of($joiningData)

			->editColumn('chkbox', function ($row) {
				return '<div class="form-check form-check-sm form-check-custom form-check-solid">
					<input class="form-check-input" type="checkbox" value="1">
					</div>
					';
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
				if ($row->thirdparty_company_id > 0) {
					$profile_sidebar_css = 'client-show-application-detail';
				}



				return '
<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);"   data-row-id="' . @$row->wiseId . '" class="' . $profile_sidebar_css . '">
  <div class="symbol-label ' . @$bgTextDecore . ' border-radius-5">' . strtoupper(@$strVal) . '</div>
  </a> </div>
<div class="d-flex flex-column"> <a href="javascript:void(0);"  data-row-id="' . @$row->wiseId . '"  class="text-dark fw-bolder text-hover-primary fs-6 ' . $profile_sidebar_css . ' " data-toggle="tooltip" title="' . ucwords(@$row->full_name) . '">' . ucwords(substr(@$row->full_name, 0, 10)) . '</a> <a href="javascript:void(0);"  data-row-id="' . @$row->wiseId . '"  class="text-muted fw-bold d-block fs-7 text-hover-primary ' . $profile_sidebar_css . ' " data-toggle="tooltip" title="' . ucwords(@$row->current_designation) . '">' . ucwords(substr(@$row->current_designation, 0, 10)) . '</a> </div>
';

			})
			->editColumn('Contact', function ($row) {



				$bench_count = DB::table('rec_candidate_assign')
					->select('rec_candidate_assign.id')
					->join('bench_applications', 'bench_applications.id', '=', 'rec_candidate_assign.source_from_id')
					->where('rec_candidate_assign.source_application_id', '=', $row->id)
					->where('rec_candidate_assign.source_company_id', '=', auth()->user()->company_id)
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
						$bench_cnt_html = '
<div data-row-id="' . $bench_row->bench_applications_id . '" class="symbol symbol-30 symbol-circle symbol-light bench_assign_listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list.."> <span class="symbol-label font-weight-bold">' . $bench_cnt . '+</span> </div>
';
					}


				}
				return '
<div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' . $row->phone . '">' . @$row->phone . '</a> <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary" data-toggle="tooltip" title="' . $row->email . '">' . @$row->email . $bench_cnt_html . '</a> </div>
';

			})
			->editColumn('Requisitions', function ($row) use ($company_id) {
				$icon = '&nbsp;';
				if ($row->thirdparty_company_id == $company_id) {
					$icon = '&nbsp;';
				}
				return '
<div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . ucwords(substr($row->title, 0, 10)) . '</a> <span class="text-muted fw-bold text-muted d-block fs-7">' . ucwords(substr($row->client_name, 0, 10)) . $icon . '</span> </div>
';
			})
			->editColumn('Source', function ($row) {

				$posted_by_image = '';
				$posted_by_image = url('public/avatar.png');
				if (!empty($row->posted_by_image)) {
					$posted_by_image = url('public/user-uploads/team_profile_img/' . @$row->posted_by_image);
				}
				return '
				<div class="d-flex flex-column">
				<div class="symbol-group symbol-hover">
				<div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="' . @$row->posted_by . '"> <img alt="image" src="' . @$posted_by_image . '"> </div>
				</div>
				<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="' . @$row->uc_company_name . '">' . substr(@$row->uc_company_name, 0, 10) . '</span> ';
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

				return '
				<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">' . $offer_currency . ' ' . $row->offer_amount . ' ' . $offer_mode . '</span> </div>
				';
			})
			->editColumn('Joining', function ($row) {

				$joined_date = $joined_time = $date = '';
				$date = @$row->joined_date;
				if (!empty($date)) {
					$joined_date = date("d-M-y", strtotime($date));
					$joined_time = date("H:i a", strtotime($date));
				}
				return '
				<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">' . $joined_date . '</span> <span class="text-muted fw-bold d-block fs-7">' . $joined_time . '</span> </div>
				';
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
				return '
<div class="d-flex flex-column"> <a href="javascript:void(0);" data-row-id="' . @$row->id . '" class="d-flex flex-column" data-toggle="modal" "> <span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1">' . ucwords(substr($row->name, 0, 10)) . '</span> <span class="text-muted fw-bold text-muted d-block fs-7">' . @$name . '</span> </a> </div>
';
			})
			->editColumn('Status', function ($row) use ($job_status_arr) {

				$status = $active = $hold = $inactive = $class = $recommendation_id = '';

				$job_status = $row->job_status;

				$status = ucwords($job_status);
				$class = '';
				//$class			=	$job_status_arr['active']; 
				$class = $job_status_arr[$job_status];
				$recommendation_id = $row->recommendation_id;

				if ($status == 'Inactive') {
					$status = 'Closed';
				}

				$final_status = "";
				if ($row->final_status == 2 && @$row->to_company_id == auth()->user()->company_id) {
					$status = 'REMOVED';
					$class = "closed-bg";
				}

				$ulink = '<a href="javascript.void(0);" class="d-flex flex-column ' . @$class . '  text-hover-success" >' . $status . '</a>';

				$recommendation_text = 'Custom';
				if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
					$recommendation_text = 'Client';
					$ulink = '<a href="javascript:void(0);" class="fs-6 ' . @$class . ' text-hover-success">' . $status . '</a>';
				}

				return '
<div class="d-flex flex-column"> ' . $ulink . ' <span class="fw-bold text-muted d-block fs-7">R' . @$row->job_id . ' -' . $recommendation_text . '</span> </div>
';
			})
			->rawColumns(['chkbox', 'Name', 'Contact', 'Requisitions', 'Status', 'Source', 'Offer', 'Joining', 'Stage'])

			->addIndexColumn()
			->make(true);
	}

	
}
