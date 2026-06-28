<?php

/*** ########################################################
Controller    	: 	AdminTeamcontroller
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/

namespace App\Http\Controllers\Admin;

use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\StoreTeam;
use App\Http\Requests\UpdateTeam;
use App\Notifications\NewUser;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Tests\HttpCache\StoreTest;
use Yajra\DataTables\Facades\DataTables;

use App\Exports\TeamExport;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\DB;
use App\Helper\GeneralMethod;

use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobLocation;

use App\Models\InterviewSchedule;
use Carbon\Carbon;

use App\Models\JobApplication;

//use App\Helper\GeneralMethod;

//use Illuminate\Support\Facades\Hash;
//use App\User;

class AdminTeamController extends AdminBaseController
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
        	//abort_if(! $this->user->cans('view_team'), 403);

        $this->users = User::with('company')->company()->get();
        return view('admin.team.index', $this->data);
    }
	
	
	public function viewMore($id)  
	{	
		
		
		$this->countryName	=	'';
		$this->stateName	=	'';
		$this->userId		=	$id;	
		$this->users = User::join('companies', 'companies.id', '=', 'users.company_id')
								->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')						
								->select(
											'users.id', 'users.company_id', 'users.name', 'users.email', 'users.mobile', 'users.created_at', 'users.image', 'users.status', 
											'companies.company_name', 'companies.website', 'companies.zoom_status', 'companies.google_status', 'companies.microsoft_status',
											'users_profile.country', 'users_profile.state', 'users_profile.city', 'users_profile.designation', 'users_profile.skills'  
										)
								->with('company')
								->where('users.id', '=', $id)
								->company()
								->first();
		
		//$this->countryArr 		= GeneralMethod::getAllCountryList(101);		 
	    //$this->statelist 		= GeneralMethod::statelist(101);
		
		
		if($this->users->country > 0)
		{
			$countryArr 		= GeneralMethod::getCountryName($this->users->country);	
			foreach($countryArr as $key=>$val)
			{
				$this->countryName	= $val['name']; 
			}
		}
		if($this->users->state > 0)
		{
			$stateArr 		= GeneralMethod::getStateName($this->users->country, $this->users->state);	
			foreach($stateArr as $key=>$val)
			{
				$this->stateName	= $val['name']; 
			}
		}
		//echo '<pre>'; print_r($this->stateName); exit; 
		
	    $view = view('admin.team.show', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
	}
	
	
	public function editrole($id)  
		{
				
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
															
				$view = view('admin.team.editrole', $this->data)->render();
				return Reply::dataOnly(['status' => 'success', 'view' => $view]);
	
		}
		
	
	public function jobdata(Request $request)
	{
			
	        //abort_if(!$this->user->cans('view_jobs'), 403);
	       
		    $company_id					=	'';
			$company_id					=	auth()->user()->company_id; 
		
		   	$salaryMode 				=	GeneralMethod::salaryMode();
			$currencySymbole 			=	GeneralMethod::currencySymbole(); 
		
		    $userId				=	$request->userId;		   
		    $chkAdminRole 		=	DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count(); 		
			$job_status			=	GeneralMethod::job_status();		
		 	$loadJob 			=	Job::select(
											'jobs.id', 'jobs.title', 'jobs.total_positions', 'jobs.priority', 'jobs.status', 'jobs.min_exp', 'jobs.max_exp', 'jobs.created_at', 'jobs.start_date', 'jobs.end_date',
											'jobs.min_sal_mode', 'jobs.max_sal_mode', 'jobs.min_salary_currency', 'jobs.max_salary_currency', 'jobs.max_salary', 'jobs.min_salary', 
											'clients.name as clients_name',  'clients.logo_logical_path',
											DB::raw("count(job_applications.id) as allCandidate"),
											DB::raw("SUM(CASE WHEN job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing"),
											DB::raw("SUM(CASE WHEN job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening"),
											DB::raw("SUM(CASE WHEN job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview"),
											DB::raw("SUM(CASE WHEN job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation"),
											DB::raw("SUM(CASE WHEN job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered"),
											DB::raw("SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined"),
											DB::raw("SUM(CASE WHEN job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected"),
											DB::raw("SUM(CASE WHEN job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS InReview")
										)
										->join('clients', 'clients.id', 'jobs.client_id')
										->join('job_assign', 'job_assign.job_id', 'jobs.id')		
										->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id')
										->where('jobs.id', '>', '0')
										->where('jobs.company_id', '=', $company_id)
										->take(1);			  
										$loadJob->where('job_assign.user_id', '=', @$userId);
										$loadJob->groupBy('jobs.id');
										$loadJob->orderBy('jobs.id', 'DESC');
										return DataTables::of($loadJob)
											
			->editColumn('chkbox', function ($row) { 
					return '<div class="form-check form-check-sm form-check-custom form-check-solid">
					<input class="form-check-input" type="checkbox" value="1" />
				</div>';
			})
			
				
			->editColumn('Requisition', function ($row) { 
			
					$bgTextDecore			=	$strVal	=	'';
					$str					=	trim(@$row->title);
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
					$icon	= '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="show-detail">
                      <div class="symbol-label '.$bgTextDecore.' border-radius-5">'.ucwords($strVal).'</div>
                    </a>
                  </div>';
				  
				  if(!empty($row->logo_logical_path)){
				  		$icon	= '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5">
                                        <a href="javascript:void(0);">
                                            <div class="symbol-label border-radius-5">
                                            <img src="'.url('public/'.@$row->logo_logical_path).'" alt="Company Logo" class="border-radius-5 w-100">
                                            </div>
                                        </a>
                                    </div>';
				  }	
					
				return $icon.'<div class="d-flex flex-column">
				<a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 show-detail"  data-toggle="tooltip" title="'.ucwords(@$row->title).'" >'.ucwords(substr(@$row->title, 0, 8)).'</a> 
				<a href="javascript:void(0);" data-row-id="'.$row->id.'" class="fw-bold text-muted d-block fs-7 text-hover-primary show-detail" data-toggle="tooltip" title="'.@$row->client_name.'">'.ucwords(substr(@$row->clients_name, 0, 8)).'</a>
				</div>'; 
            })

			->editColumn('Exp', function ($row) { 
                    return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">'.@$row->max_exp.'+</span>
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.@$row->min_exp.'+</span>
                  </div>';
            }) 
			
			->editColumn('Salary1', function ($row) { 
                    return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">18 LPA</span>                    
                    <span class="fw-bold d-block fs-7">12 LPA</span>
                  </div>';
            }) 
			
			->editColumn('Salary_old', function ($row) use($salaryMode, $currencySymbole) {										 				
					 $min_sal_mode	=	$max_sal_mode	=	$min_salary_currency	= $max_salary_currency	=	"";
					 $min_sal_mode			=	strtoupper(substr($salaryMode[$row->min_sal_mode], 0, 1)); 
					 $max_sal_mode			=	strtoupper(substr($salaryMode[$row->max_sal_mode], 0, 1));
					 $min_salary_currency	=	$currencySymbole[@$row->min_salary_currency];
					 $max_salary_currency	=	$currencySymbole[@$row->max_salary_currency];
					 $max_salary 			=	"";
					 $max_salary 			=	GeneralMethod::thousandsCurrencyFormat($row->max_salary); 
					 $min_salary 			=	"";
					 $min_salary 			=	GeneralMethod::thousandsCurrencyFormat($row->min_salary); 					 
                    return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">'.$min_salary_currency.$max_salary.'/'.$min_sal_mode.'</span>                    
                    <span class="fw-bold d-block fs-7">'.$min_salary_currency.$min_salary.'/'.$max_sal_mode.'</span>
                  </div>';
            }) 
			
			->editColumn('Salary', function ($row) use($salaryMode, $currencySymbole) {						 				
					 $min_sal_mode	=	$max_sal_mode	=	$min_salary_currency	= $max_salary_currency	=	"";
					 $min_sal_mode			=	strtoupper(substr($salaryMode[$row->min_sal_mode], 0, 1)); 
					 $max_sal_mode			=	strtoupper(substr($salaryMode[$row->max_sal_mode], 0, 1)); 
					 $min_salary_currency	=	$currencySymbole[@$row->min_salary_currency];
					 $max_salary_currency	=	$currencySymbole[@$row->max_salary_currency];
					 $max_salary 			=	"";
					 $max_salary 			=	GeneralMethod::thousandsCurrencyFormat($row->max_salary); 
					 $min_salary 			=	"";
					 $min_salary 			=	GeneralMethod::thousandsCurrencyFormat($row->min_salary); 
                    return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">'.$min_salary_currency.$max_salary.'/'.$min_sal_mode.'</span>                    
                    <span class="fw-bold d-block fs-7">'.$min_salary_currency.$min_salary.'/'.$max_sal_mode.'</span>
                  </div>';
            })
			
			->editColumn('Location2', function ($row) { 			
					
					$jobid 		= $row->id;
					$client_loc = DB::table('client_location_jobs')
					->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
					->join('countries', 'countries.id', '=', 'client_location.country_id')
					->select('client_location.address', 'client_location.city',  'client_location.postcode', 'countries.country_name')
					->where('client_location_jobs.job_id', $jobid)
					->first();			
					
					
								
                    return ' <div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="'.ucwords(@$client_loc->address).'" >'.ucwords(substr(@$client_loc->address, 0, 10)).'</span>
                    <div class="d-flex align-items-center">
                          <span class="fw-bold text-muted d-block fs-7">'.ucwords(substr(@$client_loc->city, 0, 10)).'</span>
                            &nbsp;|&nbsp;
                          <span class="fw-bold text-muted d-block fs-7">'.ucwords(substr(@$client_loc->country_name, 0, 10)).'</span>
                        <div>
                      </div>
                    </div>
                  </div>';
            }) 
					
			->editColumn('Location', function ($row) { 
			
					 $jobid 		= $row->id;
					 $client_loc 	= DB::table('client_location_jobs')
					->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
					->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
					->select(
					'client_location.address', 'client_location.city',  'client_location.postcode', 'client_location.country_id',
					'city_list.city as city_name'
					)
					->where('client_location_jobs.job_id', $jobid)
					->first();
					
					$country_id		=	$country_name = '';	
					$country_id		=	@$client_loc->country_id;
					if(@$country_id > 0){
						$country_name	= GeneralMethod::getCountryName($country_id);	
						foreach($country_name as $cval){
							$country_name 	=  ucwords($cval['sortname']);	
						}
					}
					
					$city				= substr(@$client_loc->city, 0,6); 
					if(@$client_loc->country_id == 101){
						$city			= substr(@$client_loc->city_name, 0, 6);		
					}
										 	
                    return ' <div class="d-flex flex-column">
                   	<span class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="'.ucwords(@$client_loc->address).'" >'.ucwords(substr(@$client_loc->address, 0, 7)).'</span>
                    	<div class="d-flex align-items-center">
                          <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="'.ucwords(@$city).' | '.ucwords(@$country_name).'">'.ucwords(substr(@$city, 0, 5)).' | '.ucwords(substr(@$country_name, 0, 2)).'</span>
                        <div>
                      </div>
                    </div>
                  </div>';
            }) 
				
			->editColumn('Status', function ($row) use($job_status) { 										
					$status 	= $active = $hold = $inactive =	$class	=	 '';
					$status		=	ucwords($row->status);					
					$class		=	$job_status[$row->status];					
					if($status == 'Inactive')
					{
						$status		= 'Closed';
					}
                    return '<div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="fs-6 '.@$class.' text-hover-success">'.$status.'</a>
                      <span class="fw-bold text-muted d-block fs-7">R'.@$row->id.'</span>
                    </div>';
            })

			->editColumn('Date', function ($row) { 
					$total_positions	=	'';
					$total_positions 	=	number_format($row->total_positions);
					
                    return '<div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">'.$row->created_at->format('d M, Y').'</span>
                        <span class="text-muted fw-bold d-block fs-7">'.$total_positions.'</span>
                    </div>';
            }) 
						
			->editColumn('HM', function ($row) {
					 
					 $partnersCount 	=  DB::table('job_assign')
											->join('users', 'users.id', '=', 'job_assign.user_id')
											->select('users.id', 'users.name', 'users.image')
											->where('job_assign.job_id', '=', $row->id)
											->orderBy('users.name', 'ASC')
											->count();						
					$partnersData 	=  DB::table('job_assign')
											->join('users', 'users.id', '=', 'job_assign.user_id')
											->select('users.id', 'users.name', 'users.image')
											->where('job_assign.job_id', '=', $row->id)
											->where('users.type', '=', 0)
											->orderBy('users.name', 'ASC')
											->skip(0)->take(1)->get();
					
					$html			 =	'';
					$html			.=	'<div class="d-flex flex-column">
                    						<div class="symbol-group symbol-hover">';						
					foreach($partnersData as $pval){
							$imagePath		=	"";
							$imagePath		=	"https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg";
							if(!empty(@$atVal->image)){
								$imagePath	=	url('public/user-uploads/team_profile_img/'.@$pval->image);	
							}
		
							$html			.=	'<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="'.@$pval->name.'">
                            						<img alt="Pic" src="'.@$imagePath.'">
                          						</div>';		
					}						
					 $html					.=	'<div class="symbol symbol-30 symbol-circle symbol-light" data-bs-placement="top" data-toggle="tooltip2" title="More users list..">
														<span class="symbol-label font-weight-bold">1+</span>
													  </div>';					
					$html			.=		'</div>
                    					</div>';						 
                    return $html;
            })
						
			->editColumn('Partners1', function ($row) { 
                    return '<div class="d-flex flex-column">
                    <div class="symbol-group symbol-hover">
                          <div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="One">
                            <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_21.jpg">
                          </div>
                          <div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="Two">
                            <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_22.jpg">
                          </div>
                          <div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="Three">
                            <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_23.jpg">
                          </div>
                          <div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="Four">
                            <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_24.jpg">
                          </div>
                          <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-placement="top" data-toggle="tooltip3" title="More users list..">
                            <span class="symbol-label font-weight-bold">4+</span>
                          </div>
                      </div>
                    </div>';
            })
			
			->editColumn('Partners', function ($row) {
			
					$partnersCount 	=  DB::table('job_assign')
											->join('users', 'users.id', '=', 'job_assign.user_id')
											->select('users.id', 'users.name', 'users.image')
											->where('job_assign.job_id', '=', $row->id)
											->orderBy('users.name', 'ASC')
											->count();						
					$partnersData 	=  DB::table('job_assign')
											->join('users', 'users.id', '=', 'job_assign.user_id')
											->select('users.id', 'users.name', 'users.image')
											->where('job_assign.job_id', '=', $row->id)
											->where('users.type', '=', 1)
											->orderBy('users.name', 'ASC')
											->skip(0)->take(3)->get();
					
					$html			 =	'';
					$html			.=	'<div class="d-flex flex-column">
                    						<div class="symbol-group symbol-hover">';						
					foreach($partnersData as $pval){
							$imagePath		=	"";
							$imagePath		=	"https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg";
							if(!empty(@$atVal->image)){
								$imagePath	=	url('public/user-uploads/team_profile_img/'.@$pval->image);	
							}
		
							$html			.=	'<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="'.@$pval->name.'">
                            						<img alt="Pic" src="'.@$imagePath.'">
                          						</div>';		
					}						
					 $html					.=	'<div class="symbol symbol-30 symbol-circle symbol-light" data-bs-placement="top" data-toggle="tooltip2" title="More users list..">
														<span class="symbol-label font-weight-bold">1+</span>
													  </div>';					
					$html			.=		'</div>
                    					</div>';						 
                    return $html;
					
            })
			
			->editColumn('TOT', function ($row) { 
					$applicationCounter 	= number_format(@$row->allCandidate);
                    return '<div class="d-flex flex-column">
                        <a href="'.url('/admin/apptracker/'.$row->id.'/all').'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$applicationCounter.'</a>
                        <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">'.number_format(@$row->InReview).'</a>
                    </div>';
            })

			->editColumn('SOU', function ($row) { 
					$master_status_id	= 1; 
					$job_id  	= '';
					$cnt     	= 0;
					$job_id  	= $row->id;
					$cnt		= number_format($row->Sourcing) ;
					if($cnt==0){
						return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
					}else{
						return '<div class="d-flex flex-column"><a href="'.url('/admin/apptracker/'.$row->id.'/'.$master_status_id).'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
					}                   
            }) 		

			->editColumn('SCR', function ($row) { 
					$master_status_id	= 2; 
					$job_id  			= '';
					$cnt     			= 0;
					$job_id  			= $row->id;
					$cnt				= number_format($row->Screening);
					if($cnt==0){
						$cnt = '<b>.</b>';
						return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
					}else{
						return '<div class="d-flex flex-column"><a href="'.url('/admin/apptracker/'.$row->id.'/'.$master_status_id).'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
            		}                    
            }) 

			->editColumn('EVA', function ($row) {
					$master_status_id	=	3;
					$job_id  			=	'';
					$cnt     			=	0;
					$job_id  			=	$row->id;
					$cnt				=	number_format($row->Interview) ;
					if($cnt==0){
						$cnt = '<b>.</b>';
						return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
					}else{
						return '<div class="d-flex flex-column"><a href="'.url('/admin/apptracker/'.$row->id.'/'.$master_status_id).'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';					
            		}                   
            }) 
					
			->editColumn('NEO', function ($row) { 
					
					$master_status_id	=	4;
					$job_id  			=	'';
					$cnt     			=	0;
					$job_id  			=	$row->id;
					$cnt				=	number_format($row->Negotiation) ;
					if($cnt==0){
						$cnt = '<b>.</b>';
						return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
					}else{
						return '<div class="d-flex flex-column"><a href="'.url('/admin/apptracker/'.$row->id.'/'.$master_status_id).'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
					}
            })
			
			->editColumn('OFF', function ($row) { 
					$master_status_id	=	5;
					$job_id  			=	'';
					$cnt     			=	0;
					$job_id  			=	$row->id;
					$cnt				=	number_format($row->Offered) ;
					if($cnt==0){
						$cnt = '<b>.</b>';
						return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
					}else{
						return '<div class="d-flex flex-column"><a href="'.url('/admin/apptracker/'.$row->id.'/'.$master_status_id).'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
					}                    
            }) 

			->editColumn('JOI', function ($row) { 
					$master_status_id	=	6;
					$job_id  			=	'';
					$cnt     			=	0;
					$job_id  			=	$row->id;
					$cnt				=	number_format($row->Joined) ;
					if($cnt==0){
						$cnt = '<b>.</b>';
						return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
					}else{
						return '<div class="d-flex flex-column"><a href="'.url('/admin/apptracker/'.$row->id.'/'.$master_status_id).'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
					}                   
            }) 

			->editColumn('REJ', function ($row) { 
					$master_status_id	=	7;
					$job_id  			=	'';
					$cnt     			=	0;
					$job_id  			=	$row->id;
					$cnt				=	number_format($row->Rejected) ;
					if($cnt==0){
						$cnt = '<b>.</b>';
						 return '<div class="d-flex flex-column"><a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
					}else{
						return '<div class="d-flex flex-column"><a href="'.url('/admin/apptracker/'.$row->id.'/'.$master_status_id).'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
					}      
            }) 
			->rawColumns(['chkbox', 'Requisition', 'Exp', 'Salary', 'Location', 'Status', 'Date', 'HM', 'Partners', 'TOT', 'SOU', 'SCR', 'EVA', 'NEO', 'OFF', 'JOI', 'REJ'])			
            ->addIndexColumn()
            ->make(true);
    }
	
	public function appdata(Request $request) 
	{
				
				//echo '<pre>'; print_r($request); exit; 
				
				$userId 			=	'';
				$userId 			=	$request->userId;				
				
		 		$salaryMode 		=	GeneralMethod::salaryMode();
				$currencySymbole 	=	GeneralMethod::currencySymbole(); 
		 
				$mstatus_id 		= '';
				$mstatus_id 		= $request->mstatus_id;
				$interviewStatus 	= array();
				$interviewStatus 	= DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();
       		   
				$chkAdminRole 		= DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
				$feedback_status 	= '';
				$feedback_status 	= GeneralMethod::feedback_status();
				
				$jobApplications 	= JobApplication::select('job_applications.*', 'jobs.title', 'job_locations.location', 'application_status.status', 'clients.name as client_name', 'users.name as posted_by',
				'qualification_master.name as qualification_name', 'master_status.name', 'master_status.css_style')
				->join('jobs', 'jobs.id', 'job_applications.job_id')
				->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
				->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')
				->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
				->leftjoin('clients', 'clients.id', 'jobs.client_id')
				->leftjoin('users', 'users.id', 'job_applications.submitted_by')
				->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')->leftjoin('application_status', 'application_status.id', 'job_applications.status_id');
						
			   /* if (\request('search') != "") {
					$search = request('search');
					$jobApplications = $jobApplications->where('job_applications.full_name', 'LIKE', "%$search%");
				}*/
				$jobApplications 	= $jobApplications->where('job_applications.submitted_by', '=', @$userId);
				$jobApplications 	= $jobApplications->groupBy('job_applications.id');	
				$jobApplications 	= $jobApplications->orderBy('job_applications.id', 'DESC');		
				//echo $jobApplications->toSql(); exit; 
		
        		$dataTable = DataTables::of($jobApplications)		
		
		
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
            return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);"   data-row-id="' . @$row->id . '" class="show-application-detail">
                      <div class="symbol-label ' . @$bgTextDecore . ' border-radius-5">' . strtoupper(@$strVal) . '</div>
                    </a>
                  </div>                  
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="'.ucwords(@$row->full_name).'">' . ucwords(substr(@$row->full_name, 0, 5)) . '</a>
                    <a href="javascript:void(0);"  data-row-id="' . @$row->id . '"  class="text-muted fw-bold d-block fs-7 text-hover-primary show-application-detail" data-toggle="tooltip" title="'.ucwords(@$row->current_designation).'">' . ucwords(substr(@$row->current_designation, 0, 7)) . '</a>
                  </div>';
				  
			}
			else
			{
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
				
				$current_ctc_cur		=	"";
				if(!empty(@$row->current_ctc_cur)){
					$current_ctc_cur		=	$currencySymbole[@$row->current_ctc_cur];
				}
				$current_ectc_cur		=	"";
				if(!empty(@$row->current_ectc_cur)){
					$current_ectc_cur		=	$currencySymbole[@$row->current_ectc_cur];
				}
				
				$ctc 					=	"";
				if(!empty(@$row->ctc)){
					$ctc 					=	GeneralMethod::thousandsCurrencyFormat($row->ctc); 
				}
				$ectc 					=	"";
				if(!empty(@$row->ectc)){
					$ectc 					=	GeneralMethod::thousandsCurrencyFormat($row->ectc); 
				}
				
				$current_ctc_mode		=	"";
				if(!empty(@$row->current_ctc_mode)){
					$current_ctc_mode		= substr($salaryMode[@$row->current_ctc_mode], 0, 1);
				}
				$current_ectc_mode		=	"";
				if(!empty(@$row->current_ectc_mode)){
					$current_ectc_mode		= substr($salaryMode[@$row->current_ectc_mode], 0, 1);
				}	 		
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
		
		->editColumn('Location1', function ($row) {
            if (empty($row->city)) {
                $city = '--------';
            } else {
                $city = @$row->city;
            }
            if (empty($row->country)) {
                $country = '----';
            } else {
                $country = @$row->country;
            }
            if (empty($row->state)) {
                $state = '----';
            } else {
                $state = @$row->state;
            }
            return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' . ucwords(substr($city, 0, 10)) . '</a>
                    <span class="text-muted fw-bold d-block fs-7">' . strtoupper(substr($state, 0, 3)) . ' | ' . strtoupper(substr($country, 0, 10)) . '</span>
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
									<div class="symbol symbol-circle symbol-25px overflow-hidden me-2" data-toggle="tooltip" data-placement="top" title="Sonika Sharma" style="float:left">
										<a href="javascript:void(0);" data-row-id="'.$row->id.'" class="show-application-detail">
										<div data-row-id="'.$row->id.'"  class="symbol-label '.$bgTextDecore.' border-radius-5 show-application-detail fw-bolder" style="font-size: 13px!important;">'.ucwords($strVal).'</div>
										</a>
									</div>
								</div>
                  			</div>
							<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'. @$row->posted_by .'">'.ucwords(substr(@$row->posted_by, 0, 5)).'</span>';			
		})
		
		->editColumn('UploadDate', function ($row) {
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
            $name = '--------';
            if (!empty(@$row->child_status_id) && @$row->child_status_id > 0) {
                $childStatus = '';
                $childStatus = DB::table('candidate_status')->select('*')->where('id', $row->child_status_id)->first();
                if (!empty($childStatus)) {
                    $name = @$childStatus->name;
                }
            }
            return '<div class="d-flex flex-column">
                        <span class="badge fw-bolder fs-6 ' . @$row->css_style . ' px-2 py-1">' . ucwords(substr($row->name, 0, 10)) . '</span>
                        <span class="text-muted fw-bold d-block fs-7">' . @$name . '</span>
                    </div>';
        });
				
        $rawColumn = array('chkbox', 'Name', 'Contacts', 'Exp', 'Salary', 'NoticePeriod', 'Qualifaction', 'Interview', 'Location', 'Source', 'Stage', 'UploadDate', 'Requisitions');
        $dataTable = $dataTable->rawColumns($rawColumn);
        $dataTable = $dataTable->addIndexColumn()->make(true);
        return $dataTable;
	}

	public function intdata(Request $request)
	{		
			$userId 			= '';
			$userId 			= $request->userId;			
			
			$available_status		=	'';
			$available_status		=	GeneralMethod::available_status();		
			
			$feedback_status		=	'';
			$feedback_status		=	GeneralMethod::feedback_status();
			
			$interview_mode_arr		=	'';
			$interview_mode_arr		=	GeneralMethod::interview_mode();		

        	$shedule = InterviewSchedule::select(
			
			'interview_schedules.id','job_applications.full_name','interview_schedules.status', 'interview_schedules.schedule_date','interview_schedules.available_status',
			'interview_schedules.reschedule', 'interview_schedules.feedback', 'interview_schedules.interview_mode', 'interview_schedules.interviewers', 
			 'interview_schedules.interview_type', 'interview_schedules.schedule_from_time',
			'users.name as source',
			'interview_schedules.job_application_id',
			'master_status.name as master_status_name', 'candidate_status.name as child_status_name', 
			'jobs.title as job_title',
			'jobs.id as job_id',
			'companies.company_name',
			
			'job_applications.current_designation', 
			'interview_mode.name as mode_name',
			'interview_mode_lavel.name as mode_lavel_name',
			'master_status.css_style',
			'interview_schedules.average_star_rating'	
		 )
		
		->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')  
		->leftjoin('users', 'users.id', 'job_applications.submitted_by')
		->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
		->leftjoin('companies', 'companies.id', 'jobs.company_id')
		->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')
		->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
		->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
		->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage');
			
		if (\request('search') != "")
		{
			//$search				=	request('search');	
			//$shedule			=	$shedule->where('job_applications.full_name', 'LIKE', "%$search%");
		}	

		$shedule 			=	$shedule->where('interview_schedules.company_id', $this->user->company_id);
		
        // Filter by StartDate
        if($request->startDate !== null && $request->startDate != 'null'){
            $shedule 		= $shedule->where(DB::raw('DATE(interview_schedules.`schedule_date`)'), '>=', "$request->startDate");
        }
        // Filter by EndDate
        if($request->endDate !== null && $request->endDate != 'null'){

            $shedule 		= $shedule->where(DB::raw('DATE(interview_schedules.`schedule_date`)'), '<=', "$request->endDate");

        }
		$shedule 		= $shedule->where('job_applications.submitted_by', '=', @$userId);
		$shedule->orderBy('interview_schedules.schedule_date', 'DESC');                             
		$shedule->get();          

		//echo $shedule->toSql(); exit; 

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
						
                    return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'" class="show-application-detail">
                      <div data-row-id="'.$row->job_application_id.'"  class="symbol-label '.$bgTextDecore.' border-radius-5 show-application-detail">'.ucwords($strVal).'</div>
                    </a>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="'.ucwords(@$row->full_name).'">'.ucwords(substr(@$row->full_name, 0, 8)).'</a>
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
                    return '<div class="d-flex flex-column"> 
                        <span class="badge fw-bolder fs-6 px-2 py-1 '.@$row->css_style.'">'.substr(@$row->master_status_name, 0, 10).'</span>
                        <span class="text-muted fw-bold d-block fs-7">'.substr(@$row->child_status_name, 0, 10).'</span>
                    </div>';
            })
							
            ->rawColumns(['chkbox', 'Name', 'Requisitions', 'InterviewMode', 'Interviewers', 'Source', 'InterviewDate', 'Feedback', 'Rating', 'Stage'])
            ->make(true);
    }
	
	 public function joiningdata(Request $request)
    {
		
			$userId 			= '';
			$userId 			= $request->userId;	

        	$jobApplications 	= JobApplication::select('job_applications.id', 'job_applications.full_name', 'jobs.title', 'job_applications.current_designation', 'job_applications.ctc', 'job_applications.ectc', 'master_status.name', 'job_applications.phone', 'job_applications.email', 'users.name as source',)
								->join('jobs', 'jobs.id', 'job_applications.job_id')
								->join('users', 'users.id', 'job_applications.submitted_by')
								->join('master_status', 'master_status.id', 'job_applications.master_status_id')
								->where('job_applications.master_status_id', 6)
								->where('job_applications.submitted_by', $userId);
			
        return DataTables::of($jobApplications)
           
			->addColumn('action', function ($row) {
						$action = '';
						
						//if ($this->user->cans('edit_job_applications')) {
							$action .= '<a href="' . route('admin.job-applications.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
							data-toggle="tooltip" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
						//}

						/*if ($this->user->cans('delete_job_applications')) {
							$action .= ' <a href="javascript:;" class="btn sa-params"
							data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times text-danger" aria-hidden="true"></i></a>';
						}*/			
					return $action;
			})
			
			->editColumn('full_name', function ($row) {
				return '<a href="javascript:void(0);" class="show-detail" data-widget="control-sidebar" data-slide="true" data-row-id="' .  $row->id . '">' . ucwords($row->full_name) . '</a>';
			})         
			->editColumn('title', function ($row) {
				return (!is_null($row->title)) ? $row->title : '&nbsp;';
			})			
			->editColumn('current_designation', function ($row) {
				return (!is_null($row->current_designation)) ? $row->current_designation : '&nbsp;';
			})			
			->editColumn('ctc', function ($row) {
				return (!is_null($row->ctc)) ? $row->ctc : '&nbsp;';
			})			
			->editColumn('ectc', function ($row) {
				return (!is_null($row->ectc)) ? $row->ectc : '&nbsp;';
			})			
			->editColumn('name', function ($row) {
				return (!is_null($row->name)) ? $row->name : '&nbsp;';
			})	
			
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
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="show-application-detail">
                      <div class="symbol-label fs-3 '.$bgTextDecore.' text-warning border-radius-5">'.ucwords($strVal).'</div>
                    </a>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail" data-toggle="tooltip" title="'.ucwords(@$row->full_name).'">'.ucwords(substr(@$row->full_name, 0, 7)).'</a> 
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'"  class="text-muted fw-bold d-block fs-7 text-hover-primary show-application-detail" data-toggle="tooltip" title="'.ucwords(@$row->current_designation).'">'.ucwords(substr(@$row->current_designation, 0, 7)).'</a>
                  </div>';
            })

			->editColumn('Contact', function ($row) { 
                return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.@$row->phone.'</a>   
                    <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="'.@$row->email.'">'.substr(@$row->email, 0, 12).'</a>
                  </div>';
            })

			->editColumn('Requisitions', function ($row) {
                return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="Wordpress Developer">Wordpress</a> 
                    <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="4Bell Technology">4Bell</a>
                  </div>';
            })

			->editColumn('Source1', function ($row) {
                return '<div class="d-flex flex-column">
                        <div class="symbol-group symbol-hover">
                        <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Abu Baker" data-original-title="">
                            <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_1.jpg">
                        </div>
                        <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Sonali kaushik" data-original-title="">
                            <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_2.jpg">
                        </div>
                        </div>
                        <span class="text-muted fw-bold d-block fs-7">Wipro</span>
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
								<div class="symbol symbol-circle symbol-30px overflow-hidden me-2" style="float:left" data-toggle="tooltip" title="Sonika Sharma">
									<a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'" class="show-application-detail">
										<div data-row-id="'.$row->job_application_id.'"  class="symbol-label '.$bgTextDecore.' border-radius-5 show-application-detail" style="font-size: 13px!important;">'.ucwords($strVal).'</div>
									</a>
								</div>
							</div>
						</div>
						<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords(@$row->source).'">'.ucwords(substr(@$row->source, 0, 5)).'</span>';
            })
			
			
			// ->editColumn('Offer', function ($row) {
            //     return '<div class="d-flex flex-column">
            //             <span class="text-dark fw-bolder fs-6">65 LPA</span>
            //         </div>';
            // })

			->editColumn('Offer', function ($row) {
                    $raw_html	=	'<div class="" style="width:300px!important">
						<select class="form-select form-select-solid" arial-label="Select Currency" id="" name="" style="width: 82px;display: initial;">
							<option value="0" selected="selected">INR</option>
							<option value="1">EUR</option>
							<option value="2">JPY</option>
							<option value="3">GBP</option>
							<option value="4">USD</option>
						</select>
						<input type="number" class="form-control form-control-solid" minlength="3" maxlength="7" name="" id="" placeholder="Amount" value="7654321" style="width: 100px;display: initial;">
						<select class="form-select form-select-solid" arial-label="Select Annual Salary" name="" id="" style="width: 100px;display: initial;">
							<option value="0" selected="selected">Annual</option>
							<option value="1">Monthly</option>
							<option value="2">Hourly</option>
						</select>
					</div>';
					return @$raw_html;
            	})

			// ->editColumn('Joining', function ($row) {
            //     return '<div class="d-flex flex-column">
            //             <span class="text-dark fw-bolder fs-6">05-July-2021</span>
            //             <span class="text-muted fw-bold d-block fs-7">2:05 pm</span>
            //         </div>';
            // })
			
			->editColumn('Joining', function ($row)
			{
				$raw_html	=	'<input type="datetime-local" class="form-control form-control-solid" placeholder="Joining date" id="" style="width:240px"/>';
                    return @$raw_html;
            })

			->editColumn('Stage', function ($row) {
                return '<div class="d-flex flex-column">
                        <span class="fw-bolder joined-bg fs-6 px-2">Joined</span>
                    </div>';
            })

			->rawColumns(['chkbox', 'Name', 'Contact', 'Requisitions', 'Source',  'Offer', 'Joining', 'Stage'])			
            //->rawColumns(['full_name', 'title', 'current_designation', 'ctc', 'ectc', 'name', 'action'])
            ->addIndexColumn()
            ->make(true);
    }
	
	public function info($id)
	{
			//echo '<pre>'; print_r($id); exit; 

			$this->roles = Role::all();
			$team = User::find($id);
			//echo '<pre>'; print_r($team ); exit; 
			return Reply::dataOnly(['status' => 'success', 'team' => $team ]);
	}
	
	public function resetpassword(Request $request)
	{
	
		
		
		$mteam_id 	=	$mteampassword	=	$mteamcpassword	=	'';		
		$company_id      				=	auth()->user()->company_id;
		if(!empty(@$request->mteam_id))
		{
			$mteam_id 		=	@$request->mteam_id;
		}
		if(!empty(@$request->mteampassword))
		{
			$mteampassword 		=	@$request->mteampassword;
			$mteampassword 		= Hash::make($mteampassword);
		}
		if(!empty(@$request->mteamcpassword))
		{
			$mteamcpassword 		=	@$request->mteamcpassword;
		}
		
		
		$user 						= User::find($this->user->id);			
		$user 						= User::find($mteam_id);
		if($request->mteampassword != ''){
			$user->password = Hash::make($request->mteampassword);
		}       
		$user->save();
		
		
		return Reply::dataOnly(['status' => 'success', 'message' => 'update' ]);
		
	
	
	}
	
	public function edit($id)
	{
	
		
			//abort_if(! $this->user->cans('edit_team'), 403);

			/*if($id == $this->user->id){
											abort(403);
				}*/
			//$this->roles = Role::all();
			// $this->team = User::find($id);        
	
			
			$this->statelist 		=	array();
			$this->team = User::join('companies', 'companies.id', '=', 'users.company_id')
							->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')						
							->select(
										'users.id', 'users.company_id', 'users.name', 'users.email', 'users.mobile', 'users.created_at', 'users.image', 'users.status', 'users.image', 
										'companies.company_name', 'companies.website',
										'users_profile.country', 'users_profile.state', 'users_profile.city', 'users_profile.designation', 'users_profile.skills'  
									)
							->with('company')
							->where('users.id', '=', $id)
							->company()
							->first();
			
			$this->categories 		= JobCategory::all();
			$this->companyType		= GeneralMethod::companyType();
			$this->department		= GeneralMethod::department();
			$this->countryArr 		= GeneralMethod::getAllCountryList();	
			if($this->team->country > 0){
				$this->statelist 		= GeneralMethod::statelist($this->team->country);	
			}	 
			
							
			return view('admin.team.edit', $this->data);
	
	}
	
	public function update(Request $request, $id)
	{
		$company_id      	=	auth()->user()->company_id;
		
		$user 				= User::find($id);
		$user->name 		= $request->name;
		$user->mobile       = $request->mobile;
		if ($request->hasFile('logo')) {
			$user->image = Files::upload($request->logo,'team_profile_img');
		}
		$user->save();
		
		$users_profile_count	= DB::table('users_profile')->select('*')->where('user_id', $id)->count();				
		
		$values 		= array(
									'company_id' 		=> $company_id,
									'user_id' 			=> $id, 
									'designation' 		=> $request->designation,
									'country' 			=> $request->country,
									'state'  			=> $request->state,
									'city'			 	=> $request->city
									//'skills'			=> $request->designation
								);
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
					
		//echo '<pre>'; print_r($_FILES);
		//echo '<pre>'; print_r($_REQUEST); exit; 
		//abort_if(! $this->user->cans('edit_team'), 403);
		/*
		if($id == $this->user->id){
		abort(403);
		}
		*/
		//$user->email = $request->email;
		/*if($request->password != ''){
		$user->password = Hash::make($request->password);
		}
		if (($request->mobile != $user->mobile || $request->calling_code != $user->calling_code) && $user->mobile_verified == 1) {
		$user->mobile_verified = 0;
		}
		$user->calling_code = $request->calling_code;
		//attach role
		RoleUser::where('user_id', $id)->delete();
		$user->roles()->attach($request->role_id);*/

		//return Reply::redirect(route('admin.team.index'), __('menu.team').' '.__('messages.updatedSuccessfully'));
		$message		=	"update successfully";
		return Reply::dataOnly(['status' => 'success', 'message'=> $message]);   
	
	}
	
    public function create()
	{
		//abort_if(! $this->user->cans('add_team'), 403);

		$this->roles = Role::all();
		return view('admin.team.create', $this->data);
	}

    public function store(StoreTeam $request)
	{
		//abort_if(! $this->user->cans('add_team'), 403);

		$user 				= new User();
		$user->name 		= $request->name;
		$user->email 		= $request->email;
		$user->password 	= Hash::make($request->password);
		$user->calling_code = $request->calling_code;
		$user->mobile 		= $request->mobile;

		if ($request->hasFile('image'))
		{
			$user->image 			= Files::upload($request->image,'profile');
		}
		$user->save();


		//attach role
		$user->roles()->attach($request->role_id);

		//send notification
		//$user->notify(new NewUser($request->password));

		return Reply::redirect(route('admin.team.index'), __('menu.team').' '.__('messages.createdSuccessfully'));
	}

    public function data1() 
	{
        //abort_if(! $this->user->cans('view_team'), 403);

        $usersData = User::with('company','role')->company()->where('users.type' , '<>',  1)->get();

        $roles = Role::all();

        return DataTables::of($usersData)
            ->addColumn('action', function ($row) {
                $action = '';

                //do not allow user to change own details
                if($row->id == $this->user->id){
                    return $action;
                }

                if( $this->user->cans('edit_team')){
                    $action.= '
                    <a href="' . route('admin.team.edit', [$row->id]) . '" class="btn"
                      data-toggle="tooltip" data-original-title="'.__('app.edit').'">
                      <i class="fa fa-pencil" aria-hidden="true"></i>
                    </a>';
                }

                if( $this->user->cans('delete_team')){
                    $action.= '
                    <a href="javascript:;" class="btn sa-params"
                      data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'">
                      <i class="fa fa-times text-danger" aria-hidden="true"></i>
                    </a>';
                }
                return $action;
            })
            ->addColumn('role_name', function ($row) use ($roles) {

                //do not allow user to change own role
                if($row->id == $this->user->id){
                    return $row->role->role->display_name;
                }

                if(!$this->user->cans('edit_team')){
                    return $row->role->role->display_name;
                }

                $roleOption = '<select name="role_id" class="form-control role_id" data-row-id="'.$row->id.'">';
                foreach ($roles as $role){
                    $roleOption.= '<option ';

                    if($row->role->role->id == $role->id){
                        $roleOption.= ' selected ';
                    }

                    $roleOption.= 'value="'.$role->id.'">'.ucwords($role->display_name).'</option>';
                }
                $roleOption.= '</select>';
                return $roleOption;
            })
            ->editColumn('name', function ($row) {
                return '<img src='.$row->profile_image_url.' class="img-circle" width="25" /> '.ucwords($row->name);
            })
            ->editColumn('email', function ($row) {
                return $row->email;
            })
            ->rawColumns(['name', 'action', 'role_name'])
            ->addIndexColumn()
            ->make(true);
    }

	public function data() 
	{
        abort_if(! $this->user->cans('view_team'), 403);
		
		$company_id			= 	$this->user->roles[0]->company_id;
		
		
        $users 		=	User::select('users.id', 'users.name', 'users.email', 'roles.display_name', 'users.created_at', 'users.status', 'users.image');
		$users		=	$users->join('role_user', 'role_user.user_id', 'users.id');
		$users		=	$users->join('roles', 'roles.id', 'role_user.role_id');
		//$users		=	$users->join('clients', 'clients.id', 'jobs.client_id')
		//$users		=	$users->join('master_status', 'master_status.id', 'job_applications.master_status_id')
		$users		=	$users->where('users.company_id', '=', $company_id);
		
		if (\request('search') != "")
		{
			$search		=	request('search');	
			$users		=	$users->where('users.name', 'LIKE', "%$search%");
		}
				
										
		 return DataTables::of($users)
            
			
			
			
            ->editColumn('name', function ($row) {
                return '<img src='.$row->profile_image_url.' class="img-circle" width="25" /> '.ucwords($row->name);
            })
			->editColumn('email', function ($row) {
                return $row->email;
            })
           
		   ->addColumn('role_name', function ($row) {
                return $row->display_name;
            })
			
		   	->addColumn('action', function ($row) {
                $action = '';

               if($row->id == $this->user->id){
                    return $action;
                }
				
                if( $this->user->cans('edit_team')){
                    $action.= '
                    <a href="' . route('admin.team.edit', [$row->id]) . '" class="btn" data-toggle="tooltip" data-original-title="'.__('app.edit').'">
                      <i class="fa fa-pencil" aria-hidden="true"></i>
                  </a>';
                }

                if( $this->user->cans('delete_team')){
                    $action.= '
                    <a href="javascript:;" class="btn sa-params" data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'">
                        <i class="fa fa-times text-danger" aria-hidden="true"></i>
                    </a>';
                }
                return $action;
            })
			
			
			
			->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid"><input class="form-check-input form-check-input1" type="checkbox" value="1"></div>';
            })
			->editColumn('Name', function ($row) {      
			//function ($row) use($department) {
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
		
		->editColumn('Role', function ($row)
		{
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
			
		->editColumn('Status', function ($row)
		{
			$chk		=	'';
			$status		=	$row->status;
			if($status == 'active'){
				$chk		=	'checked="checked"';			
			}
			
			return '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
						<input class="form-check-input chkStatus" type="checkbox" value="active" name="staus" '.@$chk.' onclick="handleClick(this, '.$row->id.');">
					</div>';
        })
			
		->editColumn('AddedDate', function ($row) {
		
				$created_at		=	$row->created_at;
				//$currentDateTime = '08/04/2010 22:15:00';
				$strtotime		=	strtotime($created_at);
				$newDate 		=	date('d-M-Y',$strtotime);
				$newDateTime 	=	date('h:i A',$strtotime);

					
                return ' <div class="d-flex flex-column">
                                       <span class="text-dark fw-bolder fs-6">'.@$newDate.'</span>
                                       <span class="text-muted fw-bold d-block fs-7">'.@$newDateTime.'</span>
                                    </div>';
            })
		
		
		
            ->rawColumns(['chkbox', 'Name', 'Role', 'Status', 'AddedDate',  'role_name', 'action'])
            ->addIndexColumn()
            ->make(true);					
								
    }
	
	public function statusUpdate(Request $request)
	{
		$company_id		=	auth()->user()->company_id;
		$chkStatus		=	$request->chkStatus;
		$userId			=	$request->userId;
		
		//echo '<pre>'; print_r($_REQUEST); 
		
		if($chkStatus == 'true')
		{
			DB::table('users')
				     ->where('company_id', @$company_id)
				   	 ->where('id', @$userId)
				   	 ->limit(1) 
				  	 ->update(array('status' => 'active'));   
					 
		}else{
			
			DB::table('users')
				     ->where('company_id', @$company_id)
				   	 ->where('id', @$userId)
				   	 ->limit(1) 
				  	 ->update(array('status' => 'inactive'));   
					 
		}
		 
					 
		return Reply::dataOnly(['status' => 'success']);
	}
	
    public function destroy($id)
    {
        //abort_if(! $this->user->cans('delete_team'), 403);

        User::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }

    public function changeRole(Request $request)
	{
        //attach role
        $user = User::find($request->teamId);

        RoleUser::where('user_id', $request->teamId)->delete();
        $user->roles()->attach($request->roleId);

        return Reply::dataOnly(['status' => 'success']);
    }
	
	public function export($start, $length)
    {
        $filters = [
						'start' 			=>	$start,
						'length' 			=>	$length,
						'loginUserId'		=>	$this->user->id,
						'loginUserRole'		=>	$this->user->role->role_id,
						'company_id'		=>	auth()->user()->company_id
					];

        $data = [
					'company' => $this->user->name 
				];

		//echo '<pre>'; print_r( $filters); exit; 
		
       	return Excel::download(new TeamExport($filters, $data), 'TeamExport.xlsx', ExcelExcel::XLSX); 

	}

	public function assigndata(Request $request) 
	{
       
		$company_id	= 	auth()->user()->company_id;
		
        $users 		=	User::select(
										'users.id', 'users.name', 'users.email', 'users.created_at', 'users.status', 'users.image', 'users.mobile',
										'roles.display_name', 
										'job_assign.job_id'
									);
		$users		=	$users->join('role_user', 'role_user.user_id', 'users.id');
		$users		=	$users->join('roles', 'roles.id', 'role_user.role_id');
		$users		=	$users->join('job_assign', 'job_assign.user_id', 'users.id');
		
		$users		=	$users->leftJoin('job_applications', 'job_applications.submitted_by', '=', 'users.id');
		
		//$users		=	$users->join('master_status', 'master_status.id', 'job_applications.master_status_id')
		$users		=	$users->where('job_assign.job_id', '=', $request->job_id );
		//$users		=	$users->where('job_assign.user_id', '=', 'job_applications.submitted_by' );
		$users		=	$users->where('users.company_id', '=', $company_id);
		$users		=	$users->groupBy('users.id'); 
																		
		 return DataTables::of($users)
            
			->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid"><input class="form-check-input form-check-input1" type="checkbox" value="1"></div>';
            })
			->editColumn('Name', function ($row) {      
			//function ($row) use($department) {
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
           
		    $icon	= '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left" >
                   				 <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="show-detail-team" id="kt_activities3_toggle" >
                      				<div class="symbol-label fs-3 ' . @$bgTextDecore . '  border-radius-5">' . ucwords(@$strVal) . '</div>
                    			</a>
                  		</div>';
				  
				  if(!empty($row->image)){
				  		$imagePath	=	'';
				  		$imagePath	=	url('public/user-uploads/team_profile_img/'.@$row->image);
				  		$icon	= '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5" style="float:left">
                                        <a href="javascript:void(0);">
                                            <div class="symbol-label border-radius-5">
                                            <img src="'.$imagePath.'" alt="User Image" class="border-radius-5 w-100">
                                            </div>
                                        </a>
                                    </div>';
				  }	
					
                   
					
					
					
			$departmentArr		=	'------';	
										  
            return $icon.'<div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="text-dark fw-bolder text-hover-primary fs-6 show-detail-team"  data-toggle="tooltip1" data-html="true" title="' . ucwords(@$row->name) . '">' . ucwords(substr(@$row->name, 0, 15)) . '</a>
                    <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-detail-team">'.ucwords($row->display_name).'</a>
                  </div>
                  ';
        })
		
		->editColumn('Role', function ($row) {
                return '<div class="d-flex flex-column">
                                       <span class="text-dark fw-bolder fs-6">'.ucwords($row->display_name).'</span>
                                    </div>';
            })
			
		->editColumn('Status', function ($row) {
				
				$chk		=	'';
				$status		=	$row->status;
				if($status == 'active'){
					$chk		=	'checked="checked"';			
				}
				
                return ' <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                       <input class="form-check-input chkStatus" type="checkbox" value="active" name="staus" '.@$chk.' onclick="handleClick(this, '.$row->id.');">
                                    </div>';
            })
			
		->editColumn('AddedDate', function ($row) {
		
				$created_at		=	$row->created_at;
				//$currentDateTime = '08/04/2010 22:15:00';
				$strtotime		=	strtotime($created_at);
				$newDate 		=	date('d-M-Y',$strtotime);
				$newDateTime 	=	date('h:i A',$strtotime);

					
                return ' <div class="d-flex flex-column">
                                       <span class="text-dark fw-bolder fs-6">'.@$newDate.'</span>
                                       <span class="text-muted fw-bold d-block fs-7">'.@$newDateTime.'</span>
                                    </div>';
            })
		
		
		->editColumn('ContactPerson', function ($row) { 
				return '<div class="d-flex flex-column">
				<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6 show-detail-team"  data-toggle="tooltip1" data-html="true">'.substr(@$row->email, 0, 20).'</a>
				<a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-detail-team">'.@$row->mobile.'</a>
				</div>';
            })
		->editColumn('UploadProfile', function ($row) {
				
				$userId					=	$jobId	=	$UploadProfile	=	"";
				$userId					=	$row->id; 
				$jobId					=	$row->job_id; 
				$UploadProfile 			=	DB::table('job_applications')->select('id')->where('job_applications.job_id', $jobId)->where('job_applications.submitted_by', $userId)->count();	
				 
                return '<div class="d-flex flex-column"><span class="text-dark fw-bolder fs-6">'.@$UploadProfile.'</span></div>';
            })
		->editColumn('Interview', function ($row) {
				
				$userId					=	$jobId	=	$Interview	=	"";
				$userId					=	$row->id; 
				$jobId					=	$row->job_id; 
				$Interview 				=  DB::table('job_applications')
											->join('interview_schedules', 'interview_schedules.job_application_id', '=', 'job_applications.id')
											->select('job_applications.id')
											->where('job_applications.job_id', '=', $jobId)
											->where('job_applications.submitted_by', '=', $userId)
											->count();							
                return '<div class="d-flex flex-column"><span class="text-dark fw-bolder fs-6">'.@$Interview.'</span></div>';
            })
		->editColumn('Joinings', function ($row) {
               $userId					=	$jobId	=	$Interview	=	"";
				$userId					=	$row->id; 
				$jobId					=	$row->job_id; 
				$Interview 				=  DB::table('job_applications')
											//->join('interview_schedules', 'interview_schedules.job_application_id', '=', 'job_applications.id')
											->select('job_applications.id')
											->where('job_applications.job_id', '=', $jobId)
											->where('job_applications.submitted_by', '=', $userId) 
											->where('job_applications.master_status_id', '=', 6) 
											->count();							
                return '<div class="d-flex flex-column"><span class="text-dark fw-bolder fs-6">'.@$Interview.'</span></div>';
            }) 
			
		
            ->rawColumns(['chkbox', 'Name', 'Role', 'Status', 'AddedDate',  'role_name', 'action', 'ContactPerson', 'UploadProfile', 'Interview', 'Joinings'])
            ->addIndexColumn()
            ->make(true);					
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
				   	// ->where('id', @$userId)
				   	 ->limit(1) 
				  	 ->update(array('role_id' => $user_role));  
			return Reply::dataOnly(['status' => 'success']);		  
					 
		}
		else
		{
			return Reply::dataOnly(['status' => 'warning', 'message' => 'something worng, please try again one more time ']);	
		}		 
					 
		
	}	
	
	
}
