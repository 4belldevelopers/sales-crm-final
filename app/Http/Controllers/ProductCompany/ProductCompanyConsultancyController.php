<?php 
 
/***########################################################

Controller    	: 	AdminConsultancyController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	

########################################################***/ 

namespace App\Http\Controllers\ProductCompany;
use App\Helper\Files;
use App\Helper\GeneralMethod;
use App\Helper\Reply;
use App\Http\Requests\StoreSkill;
use App\Models\JobCategory;
use App\Models\Client;
use App\Models\User;
//use App\Consultancy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; 
use App\Models\Role;
use Session;
use App\Exports\ConsultancyExport;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
//use Illuminate\Support\Facades\DB;
use Mail;
use Carbon\Carbon;
use App\Models\Country;

class ProductCompanyConsultancyController extends ProductCompanyBaseController {

    public function __construct() {
        parent::__construct();
        $this->pageTitle 	=	__('Staffing Partners');
        $this->pageIcon 	=	'icon-grid';
        view()->share('calling_codes', $this->getCallingCodes());
		
		// $this->pageTitle = 'menu.jobApplications';
        // $this->pageIcon = 'icon-user';
		
    }
	
    public function index(Request $request)
    {
		$company_id      			=	auth()->user()->company_id;	 
		$this->spartnerDataCnt		=	$this->spartnerDataCnt();
        return view('product_company.consultancy.index', $this->data);
    }




	public function spartnerDataCnt()
	{	
		
		$company_id      					=	auth()->user()->company_id;
		$spartnerCounter					=	array();
		
		//All Clients
		$spartnerCounter['clientAll']		=	0;	
		$this->clientAll 					= 	DB::table('companies')->select('companies.id');														
		$this->clientAll 					=	$this->clientAll->leftJoin('staffing_company_relation', 'staffing_company_relation.to_company_id', '=', 'companies.id');		
		$this->clientAll 					=	$this->clientAll->where('staffing_company_relation.final_status', '=', 1);
		$this->clientAll 					=	$this->clientAll->where('staffing_company_relation.from_company_id', '=', $company_id);													
		$clientAll 							= 	$this->clientAll->count();	
		$spartnerCounter['spartnerAll']		=	$clientAll;		
		
		
		//SENT REQUEST INVITATION
		$spartnerCounter['sentRequest']		=	0;	
	    $sentRequest						=	DB::table('staffing_company_invitation')
												->select('staffing_company_invitation.id')
												->where('staffing_company_invitation.from_company_id', '=', $company_id)
												->where('staffing_company_invitation.to_invitation_status', '!=', 1)
												->count();  								
		$spartnerCounter['sentRequest']		=	$sentRequest;  
		
			
			
			
		 //Direct Client
		 $spartnerCounter['clientDirect']	=	0;				
		 $clientDirect						=	DB::table('staffing_company_relation')
												->select('staffing_company_relation.id')
												->where('staffing_company_relation.to_company_id', '=', $company_id)
												->where('staffing_company_relation.final_status', '=', 1)
												->count();
		$spartnerCounter['clientDirect']	=	$clientDirect;
											
		//Custom Client
		$spartnerCounter['clientCustom']	=	0;
	 	$clientCustom 						= 	Client::select('clients.id')->where('clients.self_company', '!=', 1)->where('clients.company_id', '=', $company_id)->count();	
		$spartnerCounter['clientCustom']	=	$clientCustom;		
		
		
		
		
		 	 
		return $spartnerCounter;								
											
	
	}
	
	
	public function requestPartners($id) {
	
		$company_id      			=	auth()->user()->company_id;	 
		$this->spartnerDataCnt		=	$this->spartnerDataCnt();		
        return view('product_company.consultancy.requestPartners', $this->data);
    
	}
	
	public function suggestedPartners($id) {
		$company_id      			=	auth()->user()->company_id;	 
		$this->spartnerDataCnt		=	$this->spartnerDataCnt();		
        return view('product_company.consultancy.suggestedPartners', $this->data);
    }
	

	
	public function uncleared_request_data() 
	{
		
		$company_id				=	'';
		$company_id				=	auth()->user()->company_id; 
			
		/*$clientDirectDataList 	=	DB::table('companies')
									->select(
													'companies.id', 'companies.company_name', 'companies.logo', 'companies.industries', 'companies.logo', 'companies.company_type',
													'clients.id as client_id', 'clients.tags',
													'td_industries.ind_name', 
													'staffing_company_relation.id as staffing_company_relation_id', 'staffing_company_relation.from_company_id', 'staffing_company_relation.to_created_at',
													
													DB::raw("SUM(CASE WHEN job_applications.master_status_id = 6  THEN 1 ELSE 0 END) AS join_application"),
													DB::raw("SUM(CASE WHEN job_applications.id >0  THEN 1 ELSE 0 END) AS all_application")		
											)														
									->join('staffing_company_relation', 'staffing_company_relation.to_company_id', '=', 'companies.id')													
									->join('clients', 'clients.company_id', '=', 'companies.id')
									->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries')
									->leftJoin('job_applications', 'job_applications.thirdparty_company_id', '=', 'companies.id');
		if (\request('search') != "") {
            $search 				=	request('search');
            $clientDirectDataList 	=	$clientDirectDataList->where('companies.company_name', 'LIKE', "%$search%");
        }							
		$clientDirectDataList 	= $clientDirectDataList->where('clients.self_company', '=', 1);
		$clientDirectDataList 	= $clientDirectDataList->where('staffing_company_relation.from_company_id', '=', $company_id);
		$clientDirectDataList 	= $clientDirectDataList->where('staffing_company_relation.final_status', '=', 1);*/
		
		
		$unclearedDataList 	=	DB::table('staffing_company_invitation')
									->select(

'staffing_company_invitation.to_email', 'staffing_company_invitation.to_company_id', 'staffing_company_invitation.to_company_id', 'staffing_company_invitation.to_invitation_status',
'companies.company_name','companies.industries'	,
'td_industries.ind_name'	
											)													
									->leftJoin('companies', 'companies.id', '=', 'staffing_company_invitation.to_company_id')	
									->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries');		

/*->leftJoin('clients', function($join) use ($jobId)  
    {
        $join->on('clients.company_id', '=', 'companies.id');
        $join->on('recommendation_relation.job_id', '=', DB::raw("'".$jobId."'") );

    })*/
																				
									//->join('clients', 'clients.company_id', '=', 'companies.id')
									//->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries')
									//->leftJoin('job_applications', 'job_applications.thirdparty_company_id', '=', 'companies.id');
									
		if (\request('search') != "") {
            $search 			=	request('search');
            //$unclearedDataList 	=	$unclearedDataList->where('staffing_company_invitation.to_email', 'LIKE', "%$search%");
			//$unclearedDataList 	=	$unclearedDataList->where('staffing_company_invitation.to_email', 'LIKE', "%$search%");
			
			$unclearedDataList 		= $unclearedDataList->where(function ($query) use($search) {
					$query->where('staffing_company_invitation.to_email', 'LIKE', "%$search%")
					->orWhere('companies.company_name', 'LIKE', "%$search%");
			});
		
        }
		
		
		
									
		$unclearedDataList 	= $unclearedDataList->where('staffing_company_invitation.from_company_id', '=', $company_id);
		//$unclearedDataList 	= $unclearedDataList->where('staffing_company_relation.from_company_id', '=', $company_id);
		
		
		$unclearedDataList 	= $unclearedDataList->where('staffing_company_invitation.to_invitation_status', '!=', 1);
		
		
        return DataTables::of($unclearedDataList)
		
		->editColumn('chkbox', function ($row) {
			
			$cname	=	'';
			$cname	=	$row->company_name;
			if($cname == ''){
				$cname	=	$row->to_email;	
			}
			$cname	=	ucwords($cname);
			
			$bgTextDecore			=	$strVal	=	'';
			$str					=	trim(@$cname);
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
			
			
			$btn					=	'';
			$to_company_id			= $to_invitation_status =	'';
			
			if($row->to_invitation_status == -1){
				$btn	=	'<a href="javascript:void(0);" type="button" class="btn btn-sm btn-light fw-bolder me-3 fs-6" style="color:#FF0000">Ignore Request</a>';
			}
			else{
						if($row->to_company_id > 0){
														$btn	=	'<a href="javascript:void(0);" type="button" class="btn btn-sm btn-light fw-bolder me-3 fs-6">Pending Request</a>';	
						}else{
														$btn	=	'<a href="javascript:void(0);" type="button" class="btn btn-sm btn-light fw-bolder me-3 fs-6">Waiting For Registration</a>';
						}
			
			}
						
            
			
			
			
			$industries				=	$ind_name	=	'&nbsp;&nbsp;';
			$industries				=	$row->industries;
			if($industries > 0){
				$ind_name				=	$row->ind_name;	
			}
			
			$to_company_id				= $tags_html	=	'';
			if($row->to_company_id > 0)
			{
				$company_tags 		=	DB::table('company_tags')->select('*')->where('company_id', $row->to_company_id)->get();
				
				foreach($company_tags as $val){
					//if(!empty($val->tag_name)){
						$tags_html	.= '<span class="badge badge-light my-1">'.$val->tag_name.'</span> ';	
					//}
				}
			}
			
			
			
			$tags_html				=	'';
			//echo $tags					=	$clientArr->tags; exit; 
			if($row->to_company_id > 0)
			{
				$clientArr 				=	DB::table('clients')->select('clients.id', 'clients.tags')->where('clients.company_id', '=', $row->to_company_id)->where('clients.self_company', '=', 1)->first();
				$tags					=	$clientArr->tags;	
				if(!empty($tags))
				{
					$tagsArr = explode(',', $tags);
					foreach($tagsArr as $key=>$val){
						$tags_html			.=	'<span class="badge badge-light my-1 me-2">'.$val.'</span>';	
					}
				}
			}
			
			
			
			return '<div class="card mb-5 mb-xl-5 mx-2">
  <div class="card-body px-5">
    <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
      <div class="me-7 mb-4">
        <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative"> <a href="javascript:void(0);">
          <div class="symbol-label bg-light-primary text-primary border-radius-5 fs-40 '.$bgTextDecore.'">'.ucwords($strVal).'</div>
          </a>
          <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
        </div>
      </div>
	  
      <div class="flex-grow-1">
        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
          <div class="d-flex flex-column">
            <div class="d-flex align-items-center mb-2"> 
				<span class="text-gray-900 fs-2 fw-bolder me-2"> '.$cname.' </span> 
<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="Verified"> <span class="svg-icon svg-icon-1 svg-icon-success">
<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
<path d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z" fill="#00A3FF"></path>
<path class="permanent" d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z" fill="white"></path>
</svg>
</span> 
</a> 
			  </div>
			  
            <div class="d-flex flex-wrap fw-bold fs-6 pe-2"> 
			
			<a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2"> <span class="svg-icon svg-icon-4 me-1">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <rect x="0" y="0" width="24" height="24"/>
                  <path d="M13.5,21 L13.5,18 C13.5,17.4477153 13.0522847,17 12.5,17 L11.5,17 C10.9477153,17 10.5,17.4477153 10.5,18 L10.5,21 L5,21 L5,4 C5,2.8954305 5.8954305,2 7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,21 L13.5,21 Z M9,4 C8.44771525,4 8,4.44771525 8,5 L8,6 C8,6.55228475 8.44771525,7 9,7 L10,7 C10.5522847,7 11,6.55228475 11,6 L11,5 C11,4.44771525 10.5522847,4 10,4 L9,4 Z M14,4 C13.4477153,4 13,4.44771525 13,5 L13,6 C13,6.55228475 13.4477153,7 14,7 L15,7 C15.5522847,7 16,6.55228475 16,6 L16,5 C16,4.44771525 15.5522847,4 15,4 L14,4 Z M9,8 C8.44771525,8 8,8.44771525 8,9 L8,10 C8,10.5522847 8.44771525,11 9,11 L10,11 C10.5522847,11 11,10.5522847 11,10 L11,9 C11,8.44771525 10.5522847,8 10,8 L9,8 Z M9,12 C8.44771525,12 8,12.4477153 8,13 L8,14 C8,14.5522847 8.44771525,15 9,15 L10,15 C10.5522847,15 11,14.5522847 11,14 L11,13 C11,12.4477153 10.5522847,12 10,12 L9,12 Z M14,12 C13.4477153,12 13,12.4477153 13,13 L13,14 C13,14.5522847 13.4477153,15 14,15 L15,15 C15.5522847,15 16,14.5522847 16,14 L16,13 C16,12.4477153 15.5522847,12 15,12 L14,12 Z" fill="#000000"/>
                  <rect fill="#FFFFFF" x="13" y="8" width="3" height="3" rx="1"/>
                  <path d="M4,21 L20,21 C20.5522847,21 21,21.4477153 21,22 L21,22.4 C21,22.7313708 20.7313708,23 20.4,23 L3.6,23 C3.26862915,23 3,22.7313708 3,22.4 L3,22 C3,21.4477153 3.44771525,21 4,21 Z" fill="#000000" opacity="0.3"/>
                </g>
              </svg>
              </span> '.$ind_name.' </a> 
			  
			 
			  
			  <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2"> <span class="svg-icon svg-icon-4 me-1">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <polygon points="0 0 24 0 24 24 0 24"/>
                  <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                  <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                </g>
              </svg>
              </span> Badge </a> </div>
          </div>
          <div class="d-flex mt-4">'.$btn.'</div>
        </div>
        
		<div class="d-flex flex-wrap flex-stack">
          <div class="d-flex flex-column flex-grow-1 pe-8">
            <div class="row w-75"> 
				<span class=""> 
					'.$tags_html.'
				</span> 
			</div>
          </div>
        </div>
		
		
		
      </div>
    </div>
    <div class="separator separator-solid my-7 d-none"></div>
  </div>
</div>';
        	
		})
		
		->rawColumns(['chkbox'])
		->addIndexColumn()
		->make(true);
    
	}
	
	
    public function create()
    {
        // abort_if(! $this->user->can('add_skills'), 403);
        return view('product_company.consultancy.create', $this->data);
    }
	
    public function store(Request $request) {
        //abort_if(! $this->user->can('add_skills'), 403);
        //echo '<pre>'; print_r($_REQUEST); exit;
        $timeStamp = time();
        $company_id = '';
        $names = $request->name;
        $company_id = $request->user()->company_id;
        if (trim($request->name) == '') {
            return Reply::error(__('Consultancy name can not be null'));
        }
        if (trim($request->password) == '') {
            return Reply::error(__('Password can not be null'));
        }
        if (trim($request->email) == '') {
            return Reply::error(__('Email can not be null'));
        }
        if (trim($request->email) != '') {
            if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                return Reply::error(__('errors.emailAddressInvalid'));
            }
            $cnt = 0;
            $cnt = DB::table('users')->select('id')->where('email', $request->email)->count();
            if ($cnt > 0) {
                return Reply::error(__('The email address you have entered is already registered. please choose another email.'));
                exit;
            }
        }
        $user = new User();
        $user->company_id = @$company_id;
        if (!empty(@$request->name)) {
            $user->name = @$request->name;
        }
        if (!empty(@$request->email)) {
            $user->email = @$request->email;
        }
        if (!empty(@$request->password)) {
            $user->password = Hash::make($request->password);
        }
        if (!empty(@$request->mobile)) {
            $user->mobile = @$request->mobile;
        }
        if (!empty(@$request->calling_code)) {
            $user->calling_code = @$request->calling_code;
        }
        if (!empty(@$request->status)) {
            $user->status = @$request->status;
        }
        $user->type = 1;
        $user->save();
        $user_id = DB::getPdo()->lastInsertId();
        $role = Role::where('company_id', $company_id)->first();
        DB::table('role_user')->insert(array('user_id' => $user_id, 'role_id' => $role->id));
        $values = array();
        if (!empty(@$company_id)) {
            $values['company_id'] = @$company_id;
        }
        if (!empty(@$user_id)) {
            $values['consultancy_id'] = @$user_id;
        }
        if (!empty(@$request->contact_person_name)) {
            $values['contact_person_name'] = @$request->contact_person_name;
        }
        if (!empty(@$request->website)) {
            $values['website'] = @$request->website;
        }
        if (!empty(@$request->commission)) {
            $values['commission'] = @$request->commission;
        }
        if (!empty(@$request->payout)) {
            $values['payout'] = @$request->payout;
        }
        if (!empty(@$request->detail)) {
            $values['detail'] = @$request->detail;
        }
        if (!empty(@$request->feedback)) {
            $values['feedback'] = @$request->feedback;
        }
        DB::table('consultancy')->insert($values);
        $maxsize = 5242880;
        $directoryPath = GeneralMethod::document_manage_directory($company_id);      
        if ($request->hasFile('image')) {
            for ($i = 0;$i < count($_FILES['image']);$i++) {
                //echo '<pre>'; print_r($_FILES['image']);exit;
                $name = $file_size = $file_type = $temp = $target_file = $logicalpath = '';
                $file_size = @$_FILES['image']['size'][$i];
                $file_type = @$_FILES['image']['type'][$i];
                $temp = @$_FILES['image']['tmp_name'][$i];
                if (@$_FILES['image']['size'][$i] > 0) {
                    $path_parts = pathinfo($_FILES['image']['name'][$i]);
                    $extension = $path_parts['extension'];
                    $filename = strtolower($path_parts['filename']);
                    $altfilename = GeneralMethod::getValidUploadFileName($filename) . "-" . $timeStamp . "." . $extension;
                    $dirArr = explode("/", $directoryPath);
                    $target_file = $directoryPath . '/' . $altfilename;
                    $allowed = array('doc', 'docx', 'pdf', 'png', 'jpeg', 'jpg', 'DOC', 'DOCX', 'PDF', 'PNG', 'JPEG', 'JPG');
                    $ext = pathinfo($_FILES['image']['name'][$i], PATHINFO_EXTENSION);
                }
                if (in_array($ext, $allowed) && ($file_size < $maxsize)) {
                    $logicalpath = substr($target_file, strpos($target_file, 'user-uploads'));
                    if (!empty($company_id) && !empty($user_id) && !empty($altfilename) && !empty($target_file) && !empty($logicalpath) && !empty($file_type) && !empty($file_size)) {
                        if (move_uploaded_file($temp, $target_file)) {
                            $values = array('company_id' => $company_id, 'consultancy_id' => $user_id, 'file_name' => $altfilename, 'file_physical_path' => $target_file, 'file_logical_path' => $logicalpath, 'file_type' => $file_type, 'file_size' => $file_size);
                            DB::table('consultancy_file')->insert($values);
                        }
                    }
                }
            }
        }
        $adminArr = array();
        $adminUserArr = DB::table('roles')->select('users.*')->join('role_user', 'role_user.role_id', '=', 'roles.id')->join('users', 'users.id', '=', 'role_user.user_id')->where('roles.company_id', '=', $this->user->company_id)->where('roles.name', '=', 'admin')->where('roles.display_name', '=', 'Administrator')->where('users.type', '=', 0)->get();
        if (!empty($adminUserArr)) {
            foreach ($adminUserArr as $dArr) {
                $adminArr[] = @$dArr->email;
            }
        }
        // Email Sending
        $company = DB::table('companies')->select('*')->where('id', $this->user->company_id)->first();
        $user = array('name' => @$request->name, 'email' => @$request->email, 'password' => $request->password, 'adminemail' => $adminArr);
        /*Mail::send('email.team-created', ['user' => $user], function ($message) use ($user) {
        
        $message->from('no-reply@vibhanshujaiswal.com', 'ATS');
        $message->to($user['email'], $user['name']);
        if(!empty($user['adminemail'])){
        $message->bcc($user['adminemail']);
        }
        $message->subject('Congratulations! Your account has been created.');
        
        
        });*/
        return Reply::redirect(route('product_company.consultancy.index'), __('menu.consultancy') . ' ' . __('messages.createdSuccessfully'));
    }
	
    public function update(Request $request, $id) {
        //abort_if(! $this->user->can('edit_skills'), 403);
        $company_id = $request->user()->company_id;
        $user = DB::table('users')->select('*')->where('company_id', $company_id)->where('id', $id)->get();
        $userArr = array();
        if (!empty(@$request->name)) {
            $userArr['name'] = @$request->name;
        }
        if (!empty(@$request->email)) {
            $userArr['email'] = @$request->email;
        }
        if (!empty(@$request->password)) {
            $userArr['password'] = Hash::make($request->password);
        }
        if (!empty(@$request->mobile)) {
            $userArr['mobile'] = @$request->mobile;
        }
        if (!empty(@$request->calling_code)) {
            $userArr['calling_code'] = @$request->calling_code;
        }
        if (!empty(@$request->status)) {
            $userArr['status'] = @$request->status;
        }
        DB::table('users')->where('id', @$id)->where('company_id', @$company_id)->limit(1)->update($userArr);
        $updateArr = array();
        if (!empty(@$request->contact_person_name)) {
            $updateArr['contact_person_name'] = @$request->contact_person_name;
        }
        if (!empty(@$request->website)) {
            $updateArr['website'] = @$request->website;
        }
        if (!empty(@$request->commission)) {
            $updateArr['commission'] = @$request->commission;
        }
        if (!empty(@$request->payout)) {
            $updateArr['payout'] = @$request->payout;
        }
        if (!empty(@$request->detail)) {
            $updateArr['detail'] = @$request->detail;
        }
        if (!empty(@$request->feedback)) {
            $updateArr['feedback'] = @$request->feedback;
        }
        DB::table('consultancy')->where('consultancy_id', @$id)->where('company_id', @$company_id)->limit(1)->update($updateArr);
        //echo '<pre>'; print_r($_FILES['image']); exit;
        $timeStamp = time();
        $company_id = $request->user()->company_id;
        $maxsize = 5242880;
        $directoryPath = GeneralMethod::document_manage_directory($company_id);
        if ($request->hasFile('image')) {
            for ($i = 0;$i <= count($_FILES['image']);$i++) {
                $name = $file_size = $file_type = $temp = $target_file = $logicalpath = '';
                $file_size = @$_FILES['image']['size'][$i];
                $file_type = @$_FILES['image']['type'][$i];
                $temp = @$_FILES['image']['tmp_name'][$i];
                if (@$_FILES['image']['size'][$i] > 0) {
                    $path_parts = pathinfo($_FILES['image']['name'][$i]);
                    $extension = $path_parts['extension'];
                    $filename = strtolower($path_parts['filename']);
                    $altfilename = GeneralMethod::getValidUploadFileName($filename) . "-" . $timeStamp . "." . $extension;
                    $dirArr = explode("/", $directoryPath);
                    $target_file = $directoryPath . '/' . $altfilename;
                    $allowed = array('doc', 'docx', 'pdf', 'png', 'jpeg', 'jpg', 'DOC', 'DOCX', 'PDF', 'PNG', 'JPEG', 'JPG');
                    $ext = pathinfo($_FILES['image']['name'][$i], PATHINFO_EXTENSION);
                }
                if (in_array($ext, $allowed) && ($file_size < $maxsize)) {
                    $logicalpath = substr($target_file, strpos($target_file, 'user-uploads'));
                    if (!empty($company_id) && !empty($id) && !empty($altfilename) && !empty($target_file) && !empty($logicalpath) && !empty($file_type) && !empty($file_size)) {
                        if (move_uploaded_file($temp, $target_file)) {
                            $values = array('company_id' => $company_id, 'consultancy_id' => $id, 'file_name' => $altfilename, 'file_physical_path' => $target_file, 'file_logical_path' => $logicalpath, 'file_type' => $file_type, 'file_size' => $file_size);
                            DB::table('consultancy_file')->insert($values);
                            //echo '<pre>'; print_r($values); exit;
                            
                        }
                    }
                }
            }
        }
        return Reply::redirect(route('product_company.consultancy.index'), __('menu.clients') . ' ' . __('messages.updatedSuccessfully'));
    }
	
    public function destroy($id) 
		{
        //abort_if(! $this->user->can('delete_skills'), 403);
        User::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }	
    public function deleterecord($id) 
		{
        $client_file = DB::table('client_file')->select('*')->where('id', $id)->first();
        if (file_exists($client_file->file_logical_path)) {
            unlink(@$client_file->file_logical_path);
        }
        DB::table('client_file')->where('id', $id)->delete();
        return Reply::success(__('messages.recordDeleted'));
    }    
	public function data_old() 
		{
		$company_id			=	'';
		$company_id			=	auth()->user()->company_id; 
			
        //abort_if(! $this->user->can('view_skills'), 403);
        // $client = Client::all();
        //$consultancyArr  = User::select('users.*');
		
        $consultancyArr = User::select(
			'users.id', 'users.name', 'users.email', 'users.mobile', 'users.status', 'users.created_at',
			'consultancy.contact_person_name', 'consultancy.website', 'consultancy.commission', 'consultancy.payout',
		
			
			
			DB::raw("SUM(CASE WHEN job_applications.id > 0 THEN 1 ELSE 0 END) AS TOTAL_APPLICATION"),
			DB::raw("SUM(CASE WHEN job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS APPLICATION_JOIN"),
			
			DB::raw("SUM(CASE WHEN jobs.id > 0 THEN 1 ELSE 0 END) AS JOBS_ALL"),
			DB::raw("SUM(CASE WHEN jobs.status = 'active' THEN 1 ELSE 0 END) AS JOBS_ACTIVE"),
			DB::raw("SUM(CASE WHEN jobs.status = 'hold' THEN 1 ELSE 0 END) AS JOBS_HOLD"),
			DB::raw("SUM(CASE WHEN jobs.status = 'inactive' THEN 1 ELSE 0 END) AS JOBS_INACTIVE"),
			
			'users.name as posted_by', 	
			'users.image as posted_by_image'

			
		);
        $consultancyArr = $consultancyArr->join('consultancy', 'consultancy.consultancy_id', 'users.id');
		$consultancyArr = $consultancyArr->leftJoin('job_applications', 'job_applications.submitted_by', 'users.id');
		$consultancyArr = $consultancyArr->leftJoin('job_assign', 'job_assign.user_id', 'users.id');
		$consultancyArr = $consultancyArr->leftJoin('jobs', 'jobs.id', 'job_assign.job_id');
		
        // Filter by status 
        /*if ($request->status != 'all' && $request->status != '') {
        $jobApplications = $jobApplications->where('job_applications.status_id', $request->status);
        }*/
        // Filter By jobs
        /*if ($request->jobs != 'all' && $request->jobs != '') {
        $jobApplications = $jobApplications->where('job_applications.job_id', $request->jobs);
        }*/
		
        $consultancyArr = $consultancyArr->where('users.type', 1);
        $consultancyArr = $consultancyArr->where('users.company_id', $company_id);
        $consultancyArr = $consultancyArr->groupBy('users.id');
        if (\request('search') != "") {
            $search = request('search');
            $consultancyArr = $consultancyArr->where('users.name', 'LIKE', "%$search%");
        }
		
		$consultancyArr 	= $consultancyArr->groupBy('users.id');
		$consultancyArr 	= $consultancyArr->orderBy('users.id', 'DESC');
		
		
        return DataTables::of($consultancyArr)
		
		->addColumn('action', function ($row) {
            $action = '';
            // if( $this->user->cans('edit_client')){
            $action.= '
                    <a href="' . route('product_company.consultancy.edit', [$row->id]) . '" class="btn" data-toggle="tooltip" data-original-title="' . __('app.edit') . '">
                    	<i class="fa fa-pencil text-primary" aria-hidden="true"></i>
                    </a>';
            // }
            // if( $this->user->cans('delete_client')){
            $action.= '
                    <a href="javascript:;" class="btn sa-params" data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '">
                      <i class="fa fa-times text-danger" aria-hidden="true"></i>
                    </a>';
            //}
            return $action;
        })
		->editColumn('name', function ($row) {
            $bgTextDecore = $strVal = '';
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
            return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="show-detail">
                      <div class="symbol-label fs-3 ' . $bgTextDecore . ' text-warning border-radius-5">' . ucwords($strVal) . '</div>
                    </a>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="text-dark fw-bolder text-hover-primary fs-6 show-detail">' . ucwords(substr(@$row->name, 0, 15)) . '</a> 
                    <a href="javascript:void(0);" data-row-id="' . $row->id . '"  class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-detail">' . ucwords(substr(@$row->name, 0, 15)) . '</a>
                  </div>';
            //echo '<pre>'; print_r($row);
            //return ucfirst($row->name);
            
        })
		->editColumn('contact_person_name', function ($row) {
            return ucfirst($row->contact_person_name);
        })
		->editColumn('mobile', function ($row) {
            return ucfirst($row->mobile);
        })
		->editColumn('email', function ($row) {
            return $row->email;
        })
		->editColumn('commission  ', function ($row) {
            return ucfirst($row->commission);
        })
		->editColumn('payout   ', function ($row) {
            return ucfirst($row->payout);
        })
		->editColumn('status', function ($row) {
            if ($row->status == 'active') {
                return '<span class="badge btn btn-light-primary d-block">' . __('app.active') . '</span>';
            } else {
                return '<span class="badge btn btn-light-danger d-block">' . __('app.inactive') . '</span>';
            }
        })
		
		
		->editColumn('chkbox', function ($row) {
            return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
        })
		
		->editColumn('Name', function ($row) {
            $bgTextDecore = $strVal = '';
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
          
            return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left" >
                   				 <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="show-detail" id="kt_activities3_toggle" >
                      				<div class="symbol-label fs-3 ' . @$bgTextDecore . ' text-dark border-radius-5">' . ucwords(@$strVal) . '</div>
                    			</a>
                  		</div>                 
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="text-dark fw-bolder text-hover-primary fs-6 show-detail"  data-toggle="tooltip" title="'.ucwords(@$row->name).'">'. ucwords(substr(@$row->name, 0, 15)).'</a>
                    <a href="javascript:void(0);" data-row-id="' . $row->id . '" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-detail" data-toggle="tooltip" title="IT Services">IT Services</a>
                  </div>
                  ';
        })
		
		->editColumn('Location', function ($row) {
            return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Pune</a>
                    <span class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary cursor-pointer" data-toggle="tooltip" title="Pune | INDIA">Pune&nbsp;|&nbsp;INDIA</span>
                  </div>';
				  
				  
        })
		
		->editColumn('Joining', function ($row) {
		
			$TOTAL_APPLICATION		=	$APPLICATION_JOIN	=	'';
			$TOTAL_APPLICATION		=	@$row->TOTAL_APPLICATION;
			$APPLICATION_JOIN		=	@$row->APPLICATION_JOIN;		
			
            return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.@$APPLICATION_JOIN.'</a>                    
                    <span class="fw-bold d-block fs-7">'.@$TOTAL_APPLICATION.'</span>
                  </div>';
				  
        })
		
		->editColumn('Requisitions', function ($row) {
			
			$JOBS_ACTIVE		=	$JOBS_HOLD	=	$JOBS_INACTIVE	=	'';
			$JOBS_ACTIVE		=	@$row->JOBS_ACTIVE;
			$JOBS_HOLD			=	@$row->JOBS_HOLD;
			$JOBS_INACTIVE		=	@$row->JOBS_INACTIVE;
			
            return '<div class="d-flex flex-column">
                      <span class="fw-bolder text-success fs-6">'.@$JOBS_ACTIVE.' Active</span>
                      <div class="d-flex align-items-center">
                        <span class="fw-bold d-block fs-7 text-warning">'.@$JOBS_HOLD.' Hold</span>
                          &nbsp;|&nbsp;
                        <span class="fw-bold d-block fs-7 text-danger">'.@$JOBS_INACTIVE.' Closed</span>
                      <div>
                    </div>';
        })
		
		->editColumn('ConnectedOn', function ($row) {
		
			$created_at		=	$timeAgo	=	'';
			$created_at		=	@$row->created_at;
			
			$timeAgo 		=	GeneralMethod::timeAgo($created_at);
			
            return '<div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">'.Carbon::parse(@$created_at)->format('d M, Y').'</span>
                        <span class="text-muted fw-bold d-block fs-7">'.@$timeAgo.'</span>
                    </div>';
        })
		
		->editColumn('ContactPerson', function ($row) {
			
			$posted_by_image			=		'';
			if(!empty($row->posted_by_image)){	
				$posted_by_image		=	url('public/user-uploads/team_profile_img/'.@$row->posted_by_image); 
			}	

            return '<div class="d-flex flex-column">
                    <div class="symbol-group symbol-hover">
                      <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="'.@$row->posted_by.'">
                        <img alt="image" src="'.@$posted_by_image.'">
                      </div>
                  </div>';				  
           
        })
		
		->rawColumns(['chkbox', 'Name', 'Location', 'Joining', 'Requisitions', 'ConnectedOn', 'ContactPerson'])
		->addIndexColumn()
		->make(true);
    }
	public function data() 
		{
		$company_id			=	'';
		$company_id			=	auth()->user()->company_id; 
			
       
		/*$clientDirectDataList 	= DB::table('companies')
									->select(
													'companies.id',
											)														
									->join('staffing_company_relation', 'staffing_company_relation.to_company_id', '=', 'companies.id')									
									->join('clients', 'clients.company_id', '=', 'staffing_company_relation.to_company_id');
									//->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries')
									//->leftJoin('job_applications', 'job_applications.thirdparty_company_id', '=', 'companies.id');
		
		
		if (\request('search') != "") {
            $search 				=	request('search');
            //$clientDirectDataList 	=	$clientDirectDataList->where('companies.company_name', 'LIKE', "%$search%"); 
			//$clientDirectDataList 	=	$clientDirectDataList->where('companies.company_name', 'LIKE', "%$search%");
        }
		
		
									
		//$clientDirectDataList 	= $clientDirectDataList->where('clients.self_company', '=', 1);
		$clientDirectDataList 		= $clientDirectDataList->where('staffing_company_relation.from_company_id', '=', $company_id);
		$clientDirectDataList 		= $clientDirectDataList->where('staffing_company_relation.final_status', '=', 1);*/
		
		
		$staffing_partner_query		=	DB::table('staffing_company_relation');
		$staffing_partner_query		=	$staffing_partner_query->select(
			'companies.id', 'companies.company_name', 'companies.logo', 'companies.industries', 'companies.company_type',
			'clients.id as client_id', 'clients.tags',
			'td_industries.ind_name',
			'staffing_company_relation.id as staffing_company_relation_id', 'staffing_company_relation.from_company_id', 'staffing_company_relation.to_created_at',
			DB::raw("SUM(CASE WHEN job_applications.master_status_id = 6  THEN 1 ELSE 0 END) AS join_application"),
			DB::raw("SUM(CASE WHEN job_applications.id >0  THEN 1 ELSE 0 END) AS all_application")
			
			
		
		);
		$staffing_partner_query		=	$staffing_partner_query->join('companies', 'companies.id', '=', 'staffing_company_relation.to_company_id');
		$staffing_partner_query		=	$staffing_partner_query->join('clients', 'clients.company_id', '=', 'staffing_company_relation.to_company_id');
		$staffing_partner_query		=	$staffing_partner_query->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries');
		$staffing_partner_query		=	$staffing_partner_query->leftJoin('job_applications', 'job_applications.thirdparty_company_id', '=', 'staffing_company_relation.to_company_id');
		
		
		
		$staffing_partner_query		=	$staffing_partner_query->where('clients.self_company', '=', 1);
		$staffing_partner_query		=	$staffing_partner_query->where('staffing_company_relation.from_company_id', '=', $company_id);
		$staffing_partner_query		=	$staffing_partner_query->where('staffing_company_relation.final_status', '=', 1);
		
		$staffing_partner_query		=	$staffing_partner_query->groupBy('staffing_company_relation.to_company_id'); 
		
		
		
		
		
        return DataTables::of($staffing_partner_query)
		
		
		
		->editColumn('chkbox', function ($row) {
			if($row->id > 0)
			{
				
            return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
        
			}
		})
		
		->editColumn('Name', function ($row) {if($row->id > 0){
				
				$company_name			=	'';
				$company_name			=	ucwords($row->company_name);
			
				$logo_path				=	url('img/client-default.png');
				if(!empty(@$row->logo) && strlen(@$row->logo)>10){
					$logo_path			=  url('public/user-uploads/company-logo/'.@$row->logo); 	
				}
			
				$ind_name				=	'';
				$ind_name				=	$row->ind_name;
			



				$client_type_css		=	'show-detail-direct';
				$logo_path				=	url('img/client-default.png');
				if(!empty(@$row->logo) && strlen(@$row->logo)>10){
					$logo_path			=  url('public/user-uploads/company-logo/'.@$row->logo); 	
				}
				
				$image					= '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 '.$client_type_css.'" style="float:left"> 
												<img src="'.$logo_path.'" height="100px" width="100px" class="'.$client_type_css.' border-radius-5"> 
										   </div>';
										   
				 return $image.'		              
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="' . $row->client_id . '" class="text-dark fw-bolder text-hover-primary fs-6 '.$client_type_css.'"  data-toggle="tooltip1" data-html="true" title="' . ucwords(@$row->company_name) . '">' . ucwords(substr(@$row->company_name, 0, 20)) . '</a>
                    <a href="javascript:void(0);" data-row-id="' . $row->client_id . '" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary '.$client_type_css.'">'.@$ind_name.'</a>
                  </div>
                  ';
				  						   
										   

        
			}})
		
		->editColumn('Location', function ($row) { if($row->id > 0){
								
			
			$client_id		=	$row->client_id;
			
			$locationHtml 	= '';
            $locInfo 		= DB::table('client_location')
								->select('client_location.id', 'client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
								->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
								->where('client_id', $client_id)
								->first();
            $locationHtml	= '';
           
			$country_name	=	"";
			if(@$locInfo->country_id > 0){
								 $country_name	= GeneralMethod::getCountryName( $locInfo->country_id );	
								 foreach($country_name as $cval){
									$country_name	= ucwords($cval['name']);	
								 }
								 
							}
			
			$city			= @$locInfo->city;
			if(@$locInfo->country_id == 101){
				$city			= @$locInfo->city_name;		
			}			
			$locationHtml = ucwords(substr(@$city, 0, 10)) .'&nbsp;'. @$country_name ;
			$address			=	'';
			$address			=	@$locInfo->address;
			
            return '<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.$address.'</a>
                  	<div class="d-flex align-items-center">
                  		<span class="fw-bold d-block fs-7">'.$city.'</span> &nbsp;
						|&nbsp; <span class="fw-bold d-block fs-7">'.$country_name.'</span>
                  	<div>';
        
			} })
		
		->editColumn('Joining', function ($row) {
            return '<div class="d-flex flex-column"> 
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.$row->join_application.'</a> 
						<span class="fw-bold d-block fs-7">'.$row->all_application.'</span> 
					</div>';
        })
		
		->editColumn('Requisitions', function ($row) use($company_id) {
		
			if($row->id > 0)
			{
			
								$requisitionRow 	=	'';
								$requisitionRow 	= DB:: table('recommendation_relation')
														->join('jobs', 'jobs.id','=', 'recommendation_relation.job_id')
														->select(
																	DB::raw("SUM(CASE WHEN jobs.status = 'active'  THEN 1 ELSE 0 END) AS active"),
																	DB::raw("SUM(CASE WHEN jobs.status = 'hold'  THEN 1 ELSE 0 END) AS hold"),
																	DB::raw("SUM(CASE WHEN jobs.status = 'inactive'  THEN 1 ELSE 0 END) AS inactive")
														)
														->where('recommendation_relation.from_company_id', '=', $company_id)
														->where('recommendation_relation.to_company_id', '=', $row->id)
														->where('recommendation_relation.final_status', '=', 1)
														->first();
										
							$Active = $Hold = $Closed	=	0;
							if($requisitionRow->active > 0){
								$Active = $requisitionRow->active;
							}
							if($requisitionRow->hold > 0){
								$Hold = $requisitionRow->hold;
							}
							if($requisitionRow->inactive > 0){
								$Closed = $requisitionRow->inactive;
							}
							
							return '<div class="d-flex flex-column">
										<span class="fw-bolder text-success fs-6">('.$Active.') Active</span>
										<div class="d-flex align-items-center">
											<span class="fw-bold d-block fs-7 text-warning">('.$Hold.') Hold</span> &nbsp;|&nbsp; 
											<span class="fw-bold d-block fs-7 text-danger">('.$Closed.') Closed</span>
										<div> 
									</div>'; 
					
			}		
        })
		
		->editColumn('ConnectedOn', function ($row) {
			if($row->id > 0)
			{
				
			$newDate		=	'';
			$to_created_at	=	$row->to_created_at;
			$newDate 		=	date("d M, Y", strtotime($to_created_at));
			
			$timeAgo		=	'';
			$timeAgo		=	GeneralMethod::timeAgo($to_created_at);
            
			return '<div class="d-flex flex-column"> 
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.$newDate.'</a> 
						<a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">'.$timeAgo.'</a> 
					</div>';
        
			}
		})
		
		->editColumn('ContactPerson', function ($row) {
		
			$company_id		=	$row->id;
			$contactHtml 	=	'<div class="d-flex flex-column">
                    					<div class="symbol-group symbol-hover">';
										
			$adminUserArr =  DB::table('roles')
									->select('users.*')
									->join('role_user', 'role_user.role_id', '=', 'roles.id')
									->join('users', 'users.id', '=', 'role_user.user_id')
									->where('roles.company_id', '=', $company_id)
									->where('roles.name', '=', 'admin')
									->where('roles.display_name', '=', 'Administrator')
									->get();
			if(!empty($adminUserArr))
			{
				foreach($adminUserArr as $dArr)
				{
					$image_path			=	'';
					$image_path			=	url('public/avatar.png');
					if($dArr->image !=''){
						$image_path			=	url('public/user-uploads/team_profile_img/'.$dArr->image); 	
					}
                	$contactHtml		.= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="' . ucwords(@$dArr->name) . '" >
                            		<img alt="Pic" src="'.$image_path.'">
                          		</div>';
				}
			}
		
			$contactHtml.= '</div>
                    			</div>';
            return $contactHtml;
			
            /* return '<div class="d-flex flex-column">
                    	<div class="symbol-group symbol-hover">
                      		<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="" data-bs-original-title="Manvi Sharma"> 
								<img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg"> 
							</div>
                    	</div>
                  </div>';*/
				  
				  
				  
        })
		
		
		
		->rawColumns(['chkbox', 'Name', 'Location', 'Joining', 'Requisitions', 'ConnectedOn', 'ContactPerson'])
		->addIndexColumn()
		->make(true);
    }
	public function staffingContactList($id) 
		{
	
		
		$parentCompanyType			=	GeneralMethod::parentCompanyType();
		$accountCompanyType			=	GeneralMethod::accountCompanyType();
		
		$company_id      			=	auth()->user()->company_id;
		$department					=	GeneralMethod::department();
		
		$directContactList 			=	DB::table('companies')
										->select(
													'companies.id', 'companies.company_name', 'companies.logo', 'companies.industries', 'companies.company_type',
													'users.name', 'users.email', 'users.mobile', 'users.image', 'users.status',
													'roles.display_name' 	
											);														
		$directContactList 			=	$directContactList->join('roles', 'roles.company_id', '=', 'companies.id');
		$directContactList 			=	$directContactList->join('role_user', 'role_user.role_id', '=', 'roles.id');
		$directContactList 			=	$directContactList->join('users', 'users.id', '=', 'role_user.user_id');
		$directContactList 			=	$directContactList->where('companies.id', '=', $id);
		$directContactList 			=	$directContactList->where('roles.name', '=', 'admin');
		//$directContactList 			=	$directContactList->where('roles.display_name', '=', 'Administrator');
		
		
        return DataTables::of($directContactList)  					
			
		->editColumn('chkbox', function ($row) {
            return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input form-check-input2" type="checkbox" value="1" />
                      </div>';
        })	
		->editColumn('Name', function ($row) {
			
			$bgTextDecore			=	$strVal	=	'';
					$str					=	trim(@$row->name);
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
					
					$icon	= '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);"  class="show-detail">
                      <div class="symbol-label '.$bgTextDecore.' border-radius-5">'.ucwords($strVal).'</div>
                    </a>
                  </div>';
				  
            return ' <div class="d-flex align-items-center border-0">
							  '.$icon.'                   
							  <div class="d-flex flex-column"> 
									<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.ucwords($row->name).'</a> 
									<a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">'.ucwords($row->company_name).'</a> 
							  </div>
                      </div>';
        })	
		->editColumn('Role', function ($row) {
            return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.ucwords($row->display_name).'</span> </div>';
        })
		->editColumn('Contacts', function ($row) {
            return '<div class="d-flex flex-column"> 
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.$row->mobile.'</a> 
						<a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">'.$row->email.'</a> 
					</div>';
        })
		->editColumn('Status', function ($row) {
			$classname	=	'';
			if($row->status=='active'){
				$classname	=	'bg-light-success text-success';
			}else{
				$classname	=	'bg-light-danger text-danger';
			}
			
            return '<span class=" '.$classname.' fw-bolder fs-6 px-1">'.ucwords($row->status).'</span>';
        })
		->rawColumns(['chkbox', 'Name', 'Role', 'Contacts', 'Status'])
   		->addIndexColumn()->make(true);
    
	
	
	
	}	
	public function allreq(Request $request)
		{		
		//abort_if(!$this->user->cans('view_jobs'), 403);
		
		$toCompanyId		=	request('id');	
		
		$company_id      	=	auth()->user()->company_id;
		
		$chkAdminRole 		=	DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count(); 		
		$job_status			=	GeneralMethod::job_status();
		$salaryMode 		=	GeneralMethod::salaryMode();
		$currencySymbole 	=	GeneralMethod::currencySymbole(); 
		
		
$alldatalist = DB::table('jobs')
->select(
'jobs.id', 'jobs.title', 'jobs.total_positions', 'jobs.priority', 'jobs.status', 'jobs.min_exp', 'jobs.max_exp', 'jobs.created_at', 'jobs.start_date', 'jobs.end_date', 
'jobs.min_salary', 'jobs.max_salary', 'jobs.min_sal_mode', 'jobs.max_sal_mode',
'jobs.min_salary_currency', 'jobs.max_salary_currency',
'clients.name as clients_name', 'clients.logo_logical_path',
DB::raw("COUNT(job_applications.id) as allCandidate"),

										
DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = ".$toCompanyId." THEN 1 ELSE 0 END) AS thirdparty_counter"),
DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = ".$toCompanyId." AND job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS thirdparty_InReview"),			 
DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = ".$toCompanyId." AND job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS thirdparty_Sourcing"),
DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = ".$toCompanyId." AND job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS thirdparty_Screening"),
DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = ".$toCompanyId." AND job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS thirdparty_Interview"),  
DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = ".$toCompanyId." AND job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS thirdparty_Negotiation"),
DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = ".$toCompanyId." AND job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS thirdparty_Offered"),
DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = ".$toCompanyId." AND job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS thirdparty_Joined"),
DB::raw("SUM(CASE WHEN recommendation_relation.id > 0 AND job_applications.thirdparty_company_id = ".$toCompanyId." AND job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS thirdparty_Rejected"),																			 'recommendation_relation.id as recommendation_id'

)								
->join('clients', 'clients.id', 'jobs.client_id')
->leftJoin('job_applications', 'job_applications.job_id', '=', 'jobs.id')
->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
->where('jobs.id', '>', '0')
->take(1);
				
		$alldatalist 		=	$alldatalist->where(function ($query) use ($company_id, $toCompanyId) {
									return 
												$query->where('recommendation_relation.final_status', '=', 1)
												->where('recommendation_relation.from_company_id', '=', $company_id)
												->where('recommendation_relation.to_company_id', '=', $toCompanyId);
								 });
		$alldatalist	=	$alldatalist->where('jobs.company_id', '=', $company_id);						 		
		$alldatalist	=	$alldatalist->groupBy('jobs.id');
		$alldatalist	=	$alldatalist->orderBy('jobs.id', 'DESC');
		
		
        return DataTables::of($alldatalist)
			
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
					
					$profile_detail_class	=	'show-detail';
					if($row->recommendation_id > 0){
						$profile_detail_class	=	'client-show-detail';
					}
					
					$icon	= '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="'.$profile_detail_class.'">
                      <div class="symbol-label '.$bgTextDecore.' border-radius-5">'.ucwords($strVal).'</div>
                    </a>
                  </div>';
				  
				  if(!empty($row->logo_logical_path)){
				  		$icon	= '<div data-row-id="'.$row->id.'"  class="symbol symbol-circle symbol-40px overflow-hidden me-2  '.$profile_detail_class.' border-radius-5">
                                        <a href="javascript:void(0);">
                                            <div class="symbol-label border-radius-5">
                                            	<img src="'.url('public/'.@$row->logo_logical_path).'" alt="Company Logo" class="border-radius-5 w-100">
                                            </div>
                                        </a>
                                    </div>';
				  }	
				  
                    return $icon.'<div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 '.$profile_detail_class.'"  data-toggle="tooltip1" title="'.ucwords(@$row->title).'" >'.ucwords(substr(@$row->title, 0, 15)).'</a> 
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="fw-bold text-muted d-block fs-7 text-hover-primary '.$profile_detail_class.'">'.ucwords(substr(@$row->clients_name, 0, 15)).'</a>
                  </div>
                  '; 
            }) 
			
			->editColumn('Exp', function ($row) { 
                    return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">'.@$row->max_exp.'+</span>
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.@$row->min_exp.'+</span>
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
                    <span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="'.ucwords(@$client_loc->address).'" >'.ucwords(substr(@$client_loc->address, 0, 10)).'</span>
                    <div class="d-flex align-items-center">
                          <span class="fw-bold text-muted d-block fs-7">'.ucwords(substr(@$city, 0, 10)).'</span>
                            &nbsp;|&nbsp;
                          <span class="fw-bold text-muted d-block fs-7">'.ucwords(substr(@$country_name, 0, 10)).'</span>
                        <div>
                      </div>
                    </div>
                  </div>';
            })
			 
			->editColumn('Status', function ($row) use($job_status) { 
										
					$status 	= $active = $hold = $inactive =	$class	=	$recommendation_id	=	 '';
					$status				=	ucwords($row->status);
					$class				=	$job_status[$row->status];
					$recommendation_id	=	$row->recommendation_id;
					
					$recommendation_text	=	'Custom';
					if($recommendation_id > 0){
						$recommendation_text	=	'Client';
					}
					
					if($status == 'Inactive')
					{
						$status		= 'Closed';
					}
                    return '<div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="fs-6 '.@$class.' text-hover-success">'.$status.'</a>
                      <span class="fw-bold text-muted d-block fs-7">R'.@$row->id.' -'.$recommendation_text.'</span>
                    </div>';
            }) 
			
			->editColumn('Date', function ($row) { 
			
					$total_positions	=	'';
					$total_positions 	=	number_format($row->total_positions);
					
					$created_at			=	'';
					$created_at			=	$row->created_at;
					$newDate 			= date("d M, Y", strtotime($created_at));
                    return '<div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">'.$newDate.'</span>
                        <span class="text-muted fw-bold d-block fs-7">'.$total_positions.'</span>
                    </div>';
            }) 
			
			->editColumn('HM', function ($row) use($company_id) {
				
					 
					 $partnersCount 	=  DB::table('job_assign')
											->join('users', 'users.id', '=', 'job_assign.user_id')
											->select('users.id', 'users.name', 'users.image')
											->where('job_assign.job_id', '=', $row->id)
											->where('job_assign.company_id', '=', $company_id)
											->orderBy('users.name', 'ASC')
											->count();						
					$partnersData 	=  DB::table('job_assign')
											->join('users', 'users.id', '=', 'job_assign.user_id')
											->select('users.id', 'users.name', 'users.image')
											->where('job_assign.job_id', '=', $row->id)
											->where('job_assign.company_id', '=', $company_id)
											->where('users.type', '=', 0)
											->orderBy('users.name', 'ASC')
											->skip(0)->take(3)->get();
					
					$html			 =	'';
					$html			.=	'<div class="d-flex flex-column">
                    						<div class="symbol-group symbol-hover">';						
					foreach($partnersData as $pval){
							$imagePath		=	"";
							$imagePath  = url('public/avatar.png');
							if(!empty(@$atVal->image)){
								$imagePath	=	url('public/user-uploads/team_profile_img/'.@$pval->image);	
							}
		
							$html			.=	'<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="'.@$pval->name.'">

                            						<img alt="Pic" src="'.@$imagePath.'">
                          						</div>';		
					}						

$html	.=	'<div class="symbol symbol-30 symbol-circle symbol-light hmlisting" data-row-id="'.$row->id.'" data-bs-placement="top" data-toggle="tooltip2" title="More users list..">
				<span class="symbol-label font-weight-bold">'.$partnersCount.'+</span>
			</div>';					
$html	.=		'</div>
            </div>';						 
return $html;
            
			})
 
			
			->editColumn('Partners', function ($row) {
			
					$displayCounter		=	'';
					$partnersCount 		=  DB::table('job_assign')
											->join('users', 'users.id', '=', 'job_assign.user_id')
											->select('users.id', 'users.name', 'users.image')
											->where('users.type', '=', 1)
											->where('job_assign.job_id', '=', $row->id)
											->orderBy('users.name', 'ASC')
											->count();
					$displayCounter		=	$partnersCount.'+';		
					//if($partnersCount	==	0){
					//	$displayCounter		=	$partnersCount; 
					//}
																
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
							$imagePath  	=	url('public/avatar.png');
							if(!empty(@$atVal->image)){
								$imagePath	=	url('public/user-uploads/team_profile_img/'.@$pval->image);	
							}
		
		
$bgTextDecore			=	$strVal	=	'';
$str					=	trim( @$pval->name );
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
				  		
//$html			.=	'<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="'.@$pval->name.'"><img alt="Pic" src="'.@$imagePath.'"></div>';
$html			.= '<div class="symbol symbol-30 symbol-circle"data-toggle="tooltip" title="'.@$pval->name.'"> <span class="symbol-label '.$bgTextDecore.'  fw-bolder">'.ucwords($strVal).'</span> </div>';	


						
					}						
					 $html					.=	'<div data-row-id="'.$row->id.'"  class="symbol symbol-30 symbol-circle symbol-light spartner-listing" data-bs-placement="top" data-toggle="tooltip2" title="More users list..">
														<span class="symbol-label font-weight-bold">'.$displayCounter.'</span>
													  </div>';					
					$html			.=		'</div>
                    					</div>';						 
                    return $html;
					
					
					
					
					
					
            })
			
			->editColumn('TOT', function ($row) {
					
					$url_path					=	$total_application = $inreview_counter = '';
					$url_path					=	url('/product_company/apptracker/'.$row->id.'/all');
					
					//$applicationCounter 		=	@$row->allCandidate;
					//$inreview_counter 			=	@$row->InReview;	
					
					
					$thirdparty_counter 		=	$row->thirdparty_counter;
					//$inreview_counter 			=	@$row->thirdparty_counter;
						
					$thirdparty_counter			=	number_format($thirdparty_counter);
					//$inreview_counter			=	number_format($inreview_counter);
						
                    return '<div class="d-flex flex-column">
                        <a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$thirdparty_counter.'</a>
                        <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">&nbsp;</a>
                    </div>';
            })
			 
			->editColumn('SOU', function ($row) { 
					
					$job_id  				=	'';
					$master_status_id		=	1; 
					$cnt     				=	0;
					
					$job_id  				=	$row->id;
					$cnt					=	$row->thirdparty_Sourcing;
					$url_path				=	$total_application = $inreview_counter = '';
					$url_path				=	url('/product_company/apptracker/'.$job_id.'/'.$master_status_id);
					$cnt					= 	number_format($cnt);	
					
					
					return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
					               
            }) 
			
			->editColumn('SCR', function ($row) { 
					
					$master_status_id		=	2; 
					$job_id  				=	'';
					$url_path				=	'';
					$cnt     				=	0;
					
					
					$job_id  				=	$row->id;
					$cnt					=	$row->thirdparty_Screening;
					$url_path				=	url('/product_company/apptracker/'.$job_id.'/'.$master_status_id);
					$cnt					= 	number_format($cnt);
										 
					
					return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
            		                
            }) 
			
			->editColumn('EVA', function ($row) {
					
					//thirdparty_Interview
					
					$master_status_id		=	3;
					$cnt     				=	0;	
					$job_id  				=	'';
					$url_path				=	'';
					
					$job_id  				=	$row->id;			
					$cnt					=	$row->thirdparty_Interview;
					$url_path				=	url('/product_company/apptracker/'.$job_id.'/'.$master_status_id);
					$cnt					=	number_format($cnt);
					
					return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';					
            		
            }) 
			
			->editColumn('NEO', function ($row) { 
					
					$job_id  				=	'';
					$url_path				=	'';
					$cnt     				=	0;
					$master_status_id		=	4;
					
					$job_id  				=	$row->id;
					$cnt					=	$row->thirdparty_Negotiation;
					$url_path				=	url('/product_company/apptracker/'.$job_id.'/'.$master_status_id);
					$cnt					=	number_format($cnt);
					
					return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
					
            }) 
			
			->editColumn('OFF', function ($row) { 
										
					$job_id  				=	'';
					$url_path				=	'';
					$cnt     				=	0;
					
					
					$master_status_id		=	5;
					$job_id  				=	$row->id;
					$cnt					=	$row->thirdparty_Offered;
					$url_path				=	url('/product_company/apptracker/'.$job_id.'/'.$master_status_id);
					$cnt					=	number_format($cnt);	
										
					return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
					
            }) 
			
			->editColumn('JOI', function ($row) { 
					
					
					$job_id  				=	'';
					$url_path				=	'';
					
					$cnt     				=	0;
					$master_status_id		=	6;
					$job_id  				=	$row->id;
					$cnt					=	$row->thirdparty_Joined ;
					
					$url_path				=	url('/product_company/apptracker/'.$row->id.'/'.$master_status_id);
					$cnt					=	number_format($cnt);	
					
					
					return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
					
            }) 
			
			
			->editColumn('REJ', function ($row) { 
					
					$master_status_id	= 7;
					$job_id  			= '';
					$cnt     			= 0;
					$job_id  			= $row->id;
					$cnt				= number_format($row->thirdparty_Rejected) ;
					
					return '<div class="d-flex flex-column"><a href="'.url('/service_company/apptracker/'.$row->id.'/'.$master_status_id).'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
					      
            }) 
			
			
			
			
			->rawColumns(['chkbox', 'Requisition', 'Exp', 'Salary', 'Location', 'Status', 'Date', 'HM', 'Partners', 'TOT', 'SOU', 'SCR', 'EVA', 'NEO', 'OFF', 'JOI', 'REJ'])
			
            ->addIndexColumn()
            ->make(true);
    }
	
	
	
	
	
	
	
    public function download($id) 
		{
        $client_file = DB::table('client_file')->select('*')->where('id', $id)->first();
        //$client_file->file_logical_path
        // $jobApplication = JobApplication::findOrFail($id);
        if (!file_exists($client_file->file_logical_path)) {
            abort(404);
        }
        return response()->download($client_file->file_logical_path);
    }
    public function recrive() 
		{
        //abort_if(! $this->user->can('view_skills'), 403);
        $consultancyArr = User::select('users.id', 'users.name', 'users.email', 'users.mobile', 'invitation.status', 'invitation.id as invitation_id');
        $consultancyArr = $consultancyArr->join('invitation', 'invitation.from_id', 'users.id');
        $consultancyArr = $consultancyArr->where('invitation.to_id', $this->user->id);
        $consultancyArr = $consultancyArr->where('invitation.status', 0);
        return DataTables::of($consultancyArr)->addColumn('action', function ($row) {
            $action = '';
            if ($row->status == 0) {
                if ($this->user->can('edit_client')) {
                    $action.= '<a href="javascript:void(0)" class="btn" data-toggle="tooltip" data-original-title="' . __('Action') . '"						  onclick="openCanadidateLightbox(' . @$row->invitation_id . ');">
						  	<i class="fa fa-pencil text-primary" aria-hidden="true"></i></a>';
                }
                if ($this->user->can('delete_client')) {
                    $action.= '<a href="javascript:;" class="btn sa-params"	data-toggle="tooltip" data-row-id="' . $row->invitation_id . '" data-original-title="' . __('Rejected') . '"><i class="fa fa-times text-danger" aria-hidden="true"></i></a>';
                }
            }
            return $action;
        })->editColumn('name', function ($row) {
            //echo '<pre>'; print_r($row);
            return ucfirst($row->name);
        })->editColumn('mobile', function ($row) {
            return ucfirst($row->mobile);
        })->editColumn('email', function ($row) {
            return $row->email;
        })->editColumn('status', function ($row) {
            if ($row->status == 1) {
                return '<label class="badge bg-success">' . __('Approved') . '</label>';
            } else if ($row->status == 2) {
                return '<label class="badge bg-success1" style="background-color:#FF6600">' . __('Rejected') . '</label>';
            } else {
                return '<label class="badge bg-danger">' . __('Pending') . '</label>';
            }
        })->rawColumns(['title', 'status', 'action'])->addIndexColumn()->make(true);
    }
    public function loadVisibilityControl($id) 
		{
        return view('product_company.consultancy.load_visibility_control', $this->data)->render();
    }
    public function export($start, $length) 
		{
        $filters = ['start' => $start, 'length' => $length, 'loginUserId' => $this->user->id, 'loginUserRole' => $this->user->role->role_id, 'company_id' => auth()->user()->company_id];
        $data = ['company' => $this->user->name];
        return Excel::download(new ConsultancyExport($filters, $data), 'ConsultancyExport.xlsx', ExcelExcel::XLSX);
    }
    
	
	
	public function edit($id) 
		{
        // abort_if(! $this->user->can('edit_skills'), 403);
        $this->user = User::find($id);
        $this->consultancy = DB::table('consultancy')->select('*')->where('consultancy_id', $id)->first();
        $this->client_file = DB::table('client_file')->select('*')->where('client_id', $id)->get();
        //echo '<pre>'; print_r($this->consultancy); exit;
        return view('product_company.consultancy.edit', $this->data);
    }
    public function show($id) 
		{
	
        $this->user 			=	User::find($id);
        $this->consultancy 		=	DB::table('consultancy')->select('*')->where('consultancy_id', $id)->first();
        $this->client_file 		=	DB::table('client_file')->select('*')->where('client_id', $id)->get();
        $view 					=	view('product_company.consultancy.show', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    
	}
	public function staffing_invitation($id) 
		{
        
		$staffing_partners_email	=	$userId	=	'';
		$company_id      			=	auth()->user()->company_id;
		$staffing_partners_email	=	$_REQUEST['staffing_partners_email'];
		$userId						=	$this->user->id;
		
		$clientRow					=	DB::table('clients')->select('*')->where('company_id', $company_id)->where('self_company', 1)->first();
		
		$message					=	'';
		
		// DELETE ALL OLD INVITATION 
		
		DB::table('staffing_company_invitation')
			->where('staffing_company_invitation.from_company_id', $company_id)
			->where('staffing_company_invitation.to_email', $staffing_partners_email)
			->where('staffing_company_invitation.to_invitation_status', '!=', 1)
			->delete();
				
		
		// CASE 1. NOT REGISTER 						=> SEND REGISTRATION REQUEST
		// CASE 2. DO NOT SEND INTERNAL TEAM MEMBER
		// CASE 3. ALREADY REGISTER 					=> SEND STAFFING PARTNER REQUEST
		// CASE 4. 
				
		$userRowCount 				=	User::where('email',$staffing_partners_email)->count();
		$userRow 					=	User::where('email',$staffing_partners_email)->first();
		
		
		if(empty($staffing_partners_email) || $staffing_partners_email == '')
		{
			$message				=	'Email can not be empty, please enter email';
			return Reply::dataOnly(['status' => 'warning', 'message' => $message]);		
		}
		
		if($userRowCount > 0)             							
		{
		
				if($userRow->company_id == $company_id)												// CASE 2. DO NOT SEND INTERNAL TEAM MEMBER
				{
					$message						=	'Can not send internal team member';
					return Reply::dataOnly(['status' => 'warning', 'message' => $message]);		
				}
				else
				{
																									// CHECK ALREADY LINKED 
					$staffingRowCount 				=	DB::table('staffing_company_relation')
														->where('from_company_id',$company_id)
														->where('to_company_id', $userRow->company_id)
														->where('final_status' , 1)
														->count();
														
					if($staffingRowCount > 0)														// CASE 3. ALREADY REGISTER 	=> SEND STAFFING PARTNER REQUEST
					{
						$message					=	'Staffing company already added';
						return Reply::dataOnly(['status' => 'warning', 'message' => $message]);		
					}
					else
					{
									$from_company_id	=	$company_id;
									$to_company_id		=	$userRow->company_id;
									
									// CHECK CLIENT INVITATION SENT OR NOT 
									$clientInvitationCounter 			=	DB::table('staffing_company_invitation')->select('*')
																			->where('from_company_id', $from_company_id)
																			->where('to_company_id', $to_company_id)
																			->where('to_invitation_status', '!=', -1)
																			->count();	
									if($clientInvitationCounter == 0)		// INVITATION SENT :- IN CASE NO INVITATION AVAIL
									{
										// INVITATION 
										$staffing_company_invitation	=	array(
																					'from_client_id'	=>	$clientRow->id,	
																					'from_company_id'	=>	$company_id,
																					'from_user_id'		=>	$userId,
																					'from_status'		=>	1,
																					'from_created_at'	=>	date('Y-m-d'),
																					'to_company_id'		=>	$userRow->company_id,
																					'to_email'			=>	$staffing_partners_email,
																					'to_status'			=>	0
																					//'job_id'			=>
																				  ); 
										DB::table('staffing_company_invitation')->insert($staffing_company_invitation);	
										$message						=	'Staffing company already register in system. But invitation request has';									  
									}						
		
		
						
				
			 			
						$spartnerDataCnt		=	$this->spartnerDataCnt();
						return Reply::dataOnly(['status' => 'success', 'message' => $message, 'spartnerDataCnt' => $spartnerDataCnt ]);			
					}                
				}			
		
		
		}
		else 																						//  CASE 1. NOT REGISTER => SEND REGISTRATION REQUEST												
		{
			
			
				$staffing_company_invitation	=	array(
															'from_client_id'	=>	$clientRow->id,	
															'from_company_id'	=>	$company_id,
															'from_user_id'		=>	$userId,
															'from_status'		=>	1,
															'from_created_at'	=>	date('Y-m-d'),
															//'to_company_id'		=>,
															'to_email'			=>	$staffing_partners_email,
															'to_status'			=>	0
															//'job_id'			=>
														  );
				
			 	DB::table('staffing_company_invitation')->insert($staffing_company_invitation);
			    $invitationId 		=	DB::getPdo()->lastInsertId();
				$encode				=	base64_encode($invitationId);
				
$Url 		=	url('/invitation-company-register/'.$encode);				
$to 		=	$staffing_partners_email;
$cc 		=	"abu@4bell.com";
$subject 	=	"Invitation";
$message = '
<html>
	<head>
		<title>HTML email</title>
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


$from					=	'vibhanshu@4bell.com';	
$sendEmail				=	GeneralMethod::sentEmailUsingSMTP($to, $cc, $subject, $message, $from);
$message				=	'Invitation request has been sent on email !';

$spartnerDataCnt		=	$this->spartnerDataCnt();

return Reply::dataOnly(['status' => 'success', 'message' => $message, 'spartnerDataCnt' => $spartnerDataCnt ]);	
						
				
    
		}												
		
	
	
	
	}
	public function request() 
		{
        return view('product_company.consultancy.request', $this->data);
    }
	
	public function dviewMore($id) 
		{
	
			//abort_if(! $this->user->cans('view_clients'), 403);	
			 $company_id      					=	auth()->user()->company_id;
			
			$this->countries 					=	Country::all();
			$this->categories 					=	JobCategory::all();
			$this->companyType					=	GeneralMethod::accountCompanyType();
			$this->department					=	GeneralMethod::department();
			$this->countryArr 					=	GeneralMethod::getAllCountryList();		 
			$this->statelist 					=	GeneralMethod::statelist(101);
			
		
		
			$this->directClientDetail			=	DB:: table('clients')
													->select(
																'companies.id as current_company_id',
																'clients.id as client_id', 'clients.name', 'clients.logo_logical_path', 'clients.industry', 'clients.tags', 'clients.description', 'clients.website',
																'companies.id as companies_id', 'companies.logo', 'companies.company_type', 'companies.company_size', 'companies.founded',
																'staffing_company_relation.to_created_at', 'staffing_company_relation.from_company_id', 'staffing_company_relation.to_company_id'
													)
													->join('companies', 'companies.id', '=', 'clients.company_id')
													->join('staffing_company_relation', 'staffing_company_relation.to_company_id', '=', 'companies.id')
													->where('clients.id', '=', $id)
													//->where('staffing_company_relation.from_company_id', '=', $company_id)
													->first();	
														
		
			
			 
			//echo '<pre>'; print_r($this->directClientDetail); exit; 
			
			
			
			$this->client_file 				= DB::table('client_file')->select('*')->where('client_id', $id)->get();
		   
			$this->client_location 			= DB::table('client_location')
												->select('client_location.city', 'client_location.city_id', 'client_location.address', 'client_location.postcode', 'client_location.country_id')
												->where('client_location.client_id', $id)
												->orderBy('client_location.id', 'DESC')
												->first();
			
			$this->city_name				=	'';
		
		
			if(!empty($this->client_location->country_id) && ($this->client_location->country_id == 101))
			{
				$this->city_obj 				=	DB::table('city_list')->select('city_list.city')->where('city_list.city_id', $this->client_location->city_id)->first();
				$this->city_name				=	ucwords($this->city_obj->city);
			}
			else
			{
				$city							=	@$this->client_location->city;
				$this->city_name				=	ucwords($city);	
			}
					
			$this->country_name	=	'';
			if(@$this->client_location->country_id > 0){
				$country_name					= GeneralMethod::getCountryName($this->client_location->country_id);	
				foreach($country_name as $cval){
					$this->country_name			= ucwords($cval['name']);	
				}
			}
				
			
			$this->active_requisition 		=	DB::table('jobs')->select('jobs.id')->where('jobs.client_id', $id)->where('jobs.status', 'active')->count();
			
			$this->client_contact_info 		=	DB::table('roles')
													->select('users.*')
													->join('role_user', 'role_user.role_id', '=', 'roles.id')
													->join('users', 'users.id', '=', 'role_user.user_id')
													->where('roles.company_id', '=', $this->directClientDetail->current_company_id)
													->where('roles.name', '=', 'admin')
													->where('roles.display_name', '=', 'Administrator')
													->get();
		
							
			$view = view('product_company.consultancy.dviewMore', $this->data)->render();
			return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    
	
	}
	
	 
	

}

