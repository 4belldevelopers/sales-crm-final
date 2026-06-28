<?php

/***########################################################
Controller    	: 	ManageRolePermissionController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/

namespace App\Http\Controllers\StaffingCompany;

use App\Helper\Reply;
use App\Http\Requests\StoreRole;
use App\Http\Requests\StoreUserRole;
use App\Models\Module;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Helper\GeneralMethod;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ManageRolePermissionController extends StaffingCompanyBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'menu.rolesPermission';
        $this->pageIcon = 'ti-lock';
    }
    public function index()
    {
			$company_id			=	'';
			$company_id			=	auth()->user()->company_id; 
				
			if(!$this->activePackage->package->multiple_roles) {
				$this->message = __('modules.subscription.multipleRoles');
				return view('staffing_company.subscription.ask_purchase', $this->data);
			}

			$this->roles 				=	Role::where('name', '!=', 'admin')->get();
			$this->totalPermissions 	=	Permission::count();
			$this->modules 				=	Module::all();
			
			
			$this->roleslist 			=	DB::table('roles')
											->select('roles.id', 'roles.display_name', 'roles.priority', 'roles.name')
											->where('roles.company_id', '=', $company_id)
											->orderBy('roles.priority', 'ASC')
											->orderBy('roles.name', 'ASC')
											->get();
			
			//echo '<pre>'; print_r($this->roleslist); exit; 			
			
			$this->company_id			=	$company_id;
			
			return view('staffing_company.role-permission.index', $this->data);
    }
    public function store(Request $request)
    	{
        $roleId = $request->roleId;
        $permissionId = $request->permissionId;

        if ($request->assignPermission == 'yes') {
            PermissionRole::firstOrCreate([
                'permission_id' => $permissionId,
                'role_id' => $roleId
            ]);
        } else {
            PermissionRole::where('role_id', $roleId)->where('permission_id', $permissionId)->delete();
        }

        return Reply::dataOnly(['status' => 'success']);
    }
    public function assignAllPermission(Request $request)
   	 	{
        $roleId = $request->roleId;
        $permissions = Permission::all();

        $role = Role::findOrFail($roleId);
        $role->perms()->sync([]);
        $role->attachPermissions($permissions);
        return Reply::dataOnly(['status' => 'success']);
    }
    public function removeAllPermission(Request $request)
    	{
        $roleId = $request->roleId;

        $role = Role::findOrFail($roleId);
        $role->perms()->sync([]);

        return Reply::dataOnly(['status' => 'success']);
    }
    public function showMembers($id)
    {
        $this->role = Role::find($id);
        $this->employees = User::doesntHave('role', 'and', function ($query) use ($id) {
            $query->where('role_user.role_id', $id);
        })
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at')
            ->distinct('users.id')
            ->get();

        return view('staffing_company.role-permission.members', $this->data);
    }
    public function storeRole(StoreRole $request)
    {
        $roleUser = new Role();
        $roleUser->name = strtolower($request->name);
        $roleUser->display_name = ucwords($request->name);
        $roleUser->save();
        return Reply::success(__('messages.roleCreated'));
    }
    public function update(StoreRole $request, $id)
    {
        $roleUser = Role::findOrFail($id);
        $roleUser->name = $request->name;
        $roleUser->display_name = ucwords($request->name);
        $roleUser->save();
        return Reply::success(__('messages.roleCreated'));
    }
    public function assignRole(StoreUserRole $request)
    {
        foreach ($request->user_id as $user) {
            $roleUser = new RoleUser();
            $roleUser->user_id = $user;
            $roleUser->role_id = $request->role_id;
            $roleUser->save();
        }
        return Reply::success(__('messages.roleAssigned'));
    }
    public function detachRole(Request $request)
    {
        $user = User::find($request->userId);
        $user->detachRole($request->roleId);
        return Reply::dataOnly(['status' => 'success']);
    }
    public function deleteRole(Request $request)
    {
        // Role::destroy($request->roleId);
        Role::whereId($request->roleId)->delete();
        return Reply::dataOnly(['status' => 'success']);
    }
    public function create()
    {
			$this->roles = Role::all();
			return view('staffing_company.role-permission.create', $this->data);
	}
    public function edit($id)
    {
        $this->role = Role::findOrFail($id);
        return view('staffing_company.role-permission.edit', $this->data);
    }	
	public function loadRole ($id)
	{
			abort_if(!$this->user->cans('add_roles_permission'), 403);
			
			if($this->global_company_type == 3)
			{
					 $this->permissions_master 	= DB::table('permissions_master')
		 								->select('permissions_master.display_name as master_display_name', 'permissions_master.module_id as master_module_id')
										->where('permissions_master.status', 1)
										->where('permissions_master.product_based', 1)
										->orderBy('permissions_master.priority', 'ASC')
										->get();	
			
			}
			else
			{
					 $this->permissions_master 	= DB::table('permissions_master')
		 								->select('permissions_master.display_name as master_display_name', 'permissions_master.module_id as master_module_id')
										->where('permissions_master.status', 1)
										->orderBy('permissions_master.priority', 'ASC')
										->get();	
			}
		
		 
		 /*$this->permissions 		=  DB::table('permissions_master')
										->join('permissions', 'permissions.module_id', '=', 'permissions_master.module_id')
										->select(
'permissions_master.display_name as master_display_name', 'permissions_master.module_id as master_module_id', 
'permissions.display_name', 'permissions.status'
)
										->where('permissions_master.status', '=', 1)
										->orderBy('permissions_master.priority', 'ASC')
										->get();*/	
											
		$this->id					=	$id;
		$view = view('staffing_company.role-permission.rolelist', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
	}
	public function loadEditRole ($id)
	{
		
		if($this->global_company_type == 3)
		{
				$this->permissions_master 	= DB::table('permissions_master')
		 								->select('permissions_master.display_name as master_display_name', 'permissions_master.module_id as master_module_id')
										->where('permissions_master.status', 1)
										->where('permissions_master.product_based', 1)
										->orderBy('permissions_master.priority', 'ASC')
										->get();	
		}
		else
		{
				 $this->permissions_master 	= DB::table('permissions_master')
		 								->select('permissions_master.display_name as master_display_name', 'permissions_master.module_id as master_module_id')
										->where('permissions_master.status', 1)
										->orderBy('permissions_master.priority', 'ASC')
										->get();	
		}
		
		
		 $this->role_row 			= DB::table('roles')
		 								->select('roles.display_name')
										->where('roles.id', $id)										
										->first();
																		
		
		$Rawlist 					=	DB::table('permission_role')
											->select('permission_role.permission_id')
											->where('permission_role.role_id', $id)
											//->orderBy('state_list.state')
											->get()->toArray();
		$array 						= array_column($Rawlist, 'permission_id');								
		$this->permission_role      = json_decode(json_encode($array), true);
					
		
		 /*$this->permissions 		=  DB::table('permissions_master')
										->join('permissions', 'permissions.module_id', '=', 'permissions_master.module_id')
										->select(
'permissions_master.display_name as master_display_name', 'permissions_master.module_id as master_module_id', 
'permissions.display_name', 'permissions.status'
)
										->where('permissions_master.status', '=', 1)
										->orderBy('permissions_master.priority', 'ASC')
										->get();*/
										
		
		
		$this->id					=	$id;
		$view = view('staffing_company.role-permission.loadEditRole', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
	}
	public function saveRole(Request $request)
    {
			
			abort_if(!$this->user->cans('add_roles_permission'), 403);
			
			$role_name			=	'';
			
			$company_id			=	'';
			$company_id			=	auth()->user()->company_id; 
			
			$user_id 			=	'';
        	$user_id 			=	auth()->user()->id;
		
        	$roleId 			=	$request->role;
			$role_name 			=	$request->role_name;
        	
			if(!empty($role_name))
			{
					$old_role_display_name	=	'';
					$role_priority			=	1;
					$role_row 				=	DB::table('roles')->select('*')->where('company_id', $company_id)->orderBy('roles.id', 'DESC')->first();
					if($role_row->priority >=0){
						$role_priority	=	$role_row->priority +1;	
					} 
					
					$old_role_display_name		=	$role_row->display_name;	
					if($old_role_display_name == $role_name)
					{
						return Reply::dataOnly(['status' => 'warning', 'message'=> 'Role name already register. please choose another name.']);	
					}				
						
					$lastInsertId	=	'';
					$values 		= array(
												'company_id' 		=>	$company_id,
												'name' 		 		=>	strtolower($role_name),
												'display_name' 		=>  $role_name, 
												'description' 		=>  $role_name,
												'priority' 			=>  $role_priority 
											);
					DB::table('roles')->insert($values);
					$lastInsertId	=	DB::getPdo()->lastInsertId();
					if($lastInsertId > 0)
					{
							if(!empty($roleId)){
								foreach($roleId  as $roleVal){
											$values 		= array(
																		'permission_id' 		=> $roleVal,
																		'role_id' 		 		=> $lastInsertId
																	);
											DB::table('permission_role')->insert($values);
								}
							}	
					}	
					
			}			

			//echo '<pre>'; print_r($_REQUEST); exit; 
		   /* if ($request->assignPermission == 'yes') {
				PermissionRole::firstOrCreate([
					'permission_id' => $permissionId,
					'role_id' => $roleId
				]);
			} else {
				PermissionRole::where('role_id', $roleId)->where('permission_id', $permissionId)->delete();
			}*/

        return Reply::dataOnly(['status' => 'success']);
    }
	public function updateRole(Request $request)
    {
			
			$role_name			=	'';
			
			$company_id			=	'';
			$company_id			=	auth()->user()->company_id; 
			
			$user_id 			=	'';
        	$user_id 			=	auth()->user()->id;
		
        	
			$role_name 			=	$request->role_name;
        	
			$role_id			=	'';
			$role_id 			=	$request->role_id;
			
			$permission_id 		=	$request->role;
			
			if(!empty($role_name))
			{
			
					$old_role_display_name	=	'';
					$role_priority			=	1;
					$role_row 				=	DB::table('roles')->select('*')->where('company_id', $company_id)->where('id', '!=', $role_id)->orderBy('roles.id', 'DESC')->first();
					if($role_row->priority >=0){
						$role_priority	=	$role_row->priority +1;	
					} 
					
					$old_role_display_name	=	$role_row->display_name;	
					if($old_role_display_name == $role_name)
					{
						return Reply::dataOnly(['status' => 'warning', 'message'=> 'Role name already register. please choose another name.']);	
					}	
					
					
					
					 DB::table('roles')
				     ->where('id', @$role_id)
				   	 ->limit(1) 
				  	 ->update(array('name' => strtolower($role_name), 'display_name' => $role_name ));     
					
					DB::table('permission_role')->where('role_id', $role_id)->delete();  	
					if($role_id > 0)
					{
							
							if(!empty($permission_id)){
								foreach($permission_id  as $pval){
											$values 		= 		array(
																				'permission_id' 		=> $pval,
																				'role_id' 		 		=> $role_id
																		  );
											DB::table('permission_role')->insert($values);
								}
							}	
					}
					
			}			

			//echo '<pre>'; print_r($_REQUEST); exit; 
		   /* if ($request->assignPermission == 'yes') {
				PermissionRole::firstOrCreate([
					'permission_id' => $permissionId,
					'role_id' => $roleId
				]);
			} else {
				PermissionRole::where('role_id', $roleId)->where('permission_id', $permissionId)->delete();
			}*/

        return Reply::dataOnly(['status' => 'success']);
    }
	public function showRole($id)
	{
		
		
			$company_id					=	'';
			$company_id					=	auth()->user()->company_id; 
			$this->roleslist 			=	DB::table('roles')
											->select('roles.id', 'roles.display_name', 'roles.name', 'roles.priority')
											->where('roles.company_id', '=', $company_id)
											->where('roles.id', '=', $id)
											->orderBy('roles.name', 'ASC')
											->first();
											
		
            $this->permission_model 				= DB::table('roles')
								  ->join('permission_role', 'permission_role.role_id', '=', 'roles.id')
								  ->join('permissions', 'permissions.id', '=', 'permission_role.permission_id')
								  ->join('permissions_master', 'permissions_master.module_id', '=', 'permissions.module_id')
								  ->select('roles.id', 'roles.display_name', 'permissions_master.display_name as master_display_name')
								  ->where('roles.id', $id)
								  ->groupBy('permissions.module_id')
								  ->orderBy('roles.id', 'DESC')
								  ->skip(0)->take(5)->get(); 
			
            $this->permission_model_all 	= DB::table('roles')
								  ->join('permission_role', 'permission_role.role_id', '=', 'roles.id')
								  ->join('permissions', 'permissions.id', '=', 'permission_role.permission_id')
								  ->join('permissions_master', 'permissions_master.module_id', '=', 'permissions.module_id')
								  ->select('roles.id', 'roles.display_name', 'permissions_master.display_name as master_display_name')
								  ->where('roles.id', $id)
								  ->groupBy('permissions.module_id')
								  ->orderBy('roles.id', 'DESC')
								  ->get(); 			
											
		
										
		
		
			$this->id					=	$id;
			
			
			$teamArr 			=	User::select('*');
			$teamArr 			=	$teamArr->join('role_user', 'role_user.user_id', 'users.id');
			$teamArr 			=	$teamArr->where('users.type', 0);
			$teamArr 			=	$teamArr->where('users.company_id', $company_id);
			//$teamArr 			=	$teamArr->groupBy('users.id');
			$teamArr 			=	$teamArr->where('role_user.role_id', $id);
			//$teamArr 			=	$teamArr->groupBy('users.id');
			//$teamArr 			=	$teamArr->orderBy('users.id', 'DESC');
			$teamArr 			=	$teamArr->count();
			
			$this->teamcount	=	$teamArr;	
			
			
		return view('staffing_company.role-permission.show', $this->data);
	   
	}	
	public function teamdata(Request $request) 
	{
		
		$company_id			=	'';
		$company_id			=	auth()->user()->company_id; 
			
        $role_id 			= '';
        $role_id 			= $request->role_id;
		
		$teamArr 			= User::select(
			'users.id', 'users.name', 'users.email', 'users.mobile', 'users.status', 'users.created_at','users.name as posted_by', 'users.image as posted_by_image'
		);
		$teamArr 			= $teamArr->join('role_user', 'role_user.user_id', 'users.id');
        $teamArr 			= $teamArr->where('users.type', 0);
        $teamArr 			= $teamArr->where('users.company_id', $company_id);
        $teamArr 			= $teamArr->groupBy('users.id');
        if (\request('search') != "") {
            $search = request('search');
            $teamArr = $teamArr->where('users.name', 'LIKE', "%$search%");
        }
		if (\request('role_id') != "") {
            $role_id = request('role_id');
            //$teamArr = $teamArr->where('users.name', 'LIKE', "%$role_id%");
        }
		$teamArr 	= $teamArr->where('role_user.role_id', $role_id);
		$teamArr 	= $teamArr->groupBy('users.id');
		$teamArr 	= $teamArr->orderBy('users.id', 'DESC');
			
        return DataTables::of($teamArr)
			
		->editColumn('chkbox', function ($row) {
            return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
        })
		->editColumn('ID', function ($row) {
            return 'ID'.@$row->id;
        })
		->editColumn('USER', function ($row) {
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
					<a href="Javascript:void(0);">
						'.ucwords(@$row->name).'
					</a>
					<span>'.@$row->email.'</span>
				   </div>';
				  
        })
		
		->editColumn('JOINED_DATE', function ($row) {
		
			$created_at		=	$timeAgo	=	'';
			$created_at		=	@$row->created_at;
			$timeAgo 		=	GeneralMethod::timeAgo($created_at);
			
            return '<div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">'.Carbon::parse(@$created_at)->format('d M, Y').'</span>
                        <span class="text-muted fw-bold d-block fs-7">'.@$timeAgo.'</span>
                    </div>';
        })
		
		->editColumn('ACTIONS', function ($row) 
		{
		
$html	=	'<a href="Javascript:void(0);" data-row-id="' . $row->id . '" class="btn btn-sm btn-light btn-active-light-primary actiondiv" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
Actions 
<span class="svg-icon svg-icon-5 m-0">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
<path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
</svg>
</span> 
</a> 
<span class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4 clight_" id="clight_'.$row->id.'" data-kt-menu="true" data-row-id="' . $row->id . '">  
<div class="menu-item px-3"> <a href="Javascript:void(0);" class="menu-link px-3 show-detail-team" data-row-id="' . $row->id . '">View</a> </div>   
<div class="menu-item px-3"> <a href="Javascript:void(0);" class="menu-link px-3 delete-role" data-kt-roles-table-filter="delete_row" data-row-id="' . $row->id . '">Delete</a> </div>  
</span>';

$html	=	'<div class="menu-item px-3"> <a href="Javascript:void(0);" class="menu-link px-3 show-detail-team" data-row-id="' . $row->id . '">View</a> </div>';

            return $html;       
		
		})		
			
		->rawColumns(['chkbox', 'ID', 'USER', 'JOINED_DATE',  'ACTIONS', 'Requisitions',  'ContactPerson'])
		->addIndexColumn()
		->make(true);
    
	}	
	public function removerole(Request $request)
    {						
			$company_id			=	'';
			$company_id			=	auth()->user()->company_id; 
					
			$user_id 			=	'';
			$role_id			=	'';
        	$user_id 			=	$request->user_id;	
			$role_id 			=	$request->role_id;
        	
			if(!empty($user_id) && !empty($role_id) )
			{
				DB::table('role_user')->where('user_id', $user_id)->where('role_id', $role_id)->delete();
			}
        	return Reply::dataOnly(['status' => 'success']);
    }
	
}
