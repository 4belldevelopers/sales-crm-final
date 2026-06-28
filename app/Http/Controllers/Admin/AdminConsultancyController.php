<?php

/*** ########################################################
Controller    	: 	AdminConsultancyController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/

namespace App\Http\Controllers\Admin;
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


class AdminConsultancyController extends AdminBaseController {
    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('Staffing Partners');
        $this->pageIcon = 'icon-grid';
        view()->share('calling_codes', $this->getCallingCodes());
		
		// $this->pageTitle = 'menu.jobApplications';
        // $this->pageIcon = 'icon-user';
		
    }
    public function index(Request $request)
    {
        return view('admin.consultancy.index', $this->data);
    }

    public function create()
    {
        // abort_if(! $this->user->can('add_skills'), 403);
        return view('admin.consultancy.create', $this->data);
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
        return Reply::redirect(route('admin.consultancy.index'), __('menu.consultancy') . ' ' . __('messages.createdSuccessfully'));
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
        return Reply::redirect(route('admin.consultancy.index'), __('menu.clients') . ' ' . __('messages.updatedSuccessfully'));
    }
    public function destroy($id) {
        //abort_if(! $this->user->can('delete_skills'), 403);
        User::destroy($id);
        return Reply::success(__('messages.recordDeleted'));
    }
    public function deleterecord($id) {
        $client_file = DB::table('client_file')->select('*')->where('id', $id)->first();
        if (file_exists($client_file->file_logical_path)) {
            unlink(@$client_file->file_logical_path);
        }
        DB::table('client_file')->where('id', $id)->delete();
        return Reply::success(__('messages.recordDeleted'));
    }
    
	
	public function data() 
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
                    <a href="' . route('admin.consultancy.edit', [$row->id]) . '" class="btn" data-toggle="tooltip" data-original-title="' . __('app.edit') . '">
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
	
	
    public function download($id) {
        $client_file = DB::table('client_file')->select('*')->where('id', $id)->first();
        //$client_file->file_logical_path
        // $jobApplication = JobApplication::findOrFail($id);
        if (!file_exists($client_file->file_logical_path)) {
            abort(404);
        }
        return response()->download($client_file->file_logical_path);
    }
    public function recrive() {
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
    public function loadVisibilityControl($id) {
        return view('admin.consultancy.load_visibility_control', $this->data)->render();
    }
    public function export($start, $length) {
        $filters = ['start' => $start, 'length' => $length, 'loginUserId' => $this->user->id, 'loginUserRole' => $this->user->role->role_id, 'company_id' => auth()->user()->company_id];
        $data = ['company' => $this->user->name];
        return Excel::download(new ConsultancyExport($filters, $data), 'ConsultancyExport.xlsx', ExcelExcel::XLSX);
    }
    public function edit($id) {
        // abort_if(! $this->user->can('edit_skills'), 403);
        $this->user = User::find($id);
        $this->consultancy = DB::table('consultancy')->select('*')->where('consultancy_id', $id)->first();
        $this->client_file = DB::table('client_file')->select('*')->where('client_id', $id)->get();
        //echo '<pre>'; print_r($this->consultancy); exit;
        return view('admin.consultancy.edit', $this->data);
    }
	
	
	
	
	
    public function show($id) {
        $this->user = User::find($id);
        $this->consultancy = DB::table('consultancy')->select('*')->where('consultancy_id', $id)->first();
        $this->client_file = DB::table('client_file')->select('*')->where('client_id', $id)->get();
        $view = view('admin.consultancy.show', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }
	
	
	public function suggestedPartners($id) {
	
			//echo "call"; exit; 
			
        return view('admin.consultancy.suggestedPartners', $this->data);
    }
	
	public function requestPartners($id) {
        return view('admin.consultancy.requestPartners', $this->data);
    }
	
	
	
}
