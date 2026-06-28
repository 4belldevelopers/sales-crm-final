<?php

/***########################################################
Controller    	: 	ManageRolePermissionController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/

namespace App\Http\Controllers\ServiceCompany;

use App\Helper\Reply;
use App\Http\Requests\StoreRole;
use App\Http\Requests\StoreUserRole;
use App\Module;
use App\Permission;
use App\PermissionRole;
use App\Role;
use App\RoleUser;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Helper\GeneralMethod;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ManageRolePermissionController extends ServiceCompanyBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle 	= 'menu.rolesPermission';
        $this->pageIcon 	= 'ti-lock';
    }

	public function index()
    	{
	
			$company_id					=	'';
			$company_id					=	auth()->user()->company_id; 				
			$this->roles 				=	Role::where('name', '!=', 'admin')->get();
			$this->totalPermissions 	=	Permission::count();
			$this->modules 				=	Module::all();			
			$this->roleslist 			=	DB::table('roles')
											->select('roles.id', 'roles.display_name', 'roles.priority', 'roles.name')
											->where('roles.company_id', '=', $company_id)
											->orderBy('roles.priority', 'ASC')
											->orderBy('roles.name', 'ASC')
											->get();
			$this->company_id			=	$company_id;
			return view('service_company.role-permission.index', $this->data);
    }
	public function create()
    	{
			
				if($this->global_company_type == 3){
						 $this->permissions_master 	= DB::table('permissions_master')
											->select('permissions_master.display_name as master_display_name', 'permissions_master.module_id as master_module_id')
											->where('permissions_master.status', 1)
											->where('permissions_master.product_based', 1)
											->orderBy('permissions_master.priority', 'ASC')
											->get();	
				}else{
						 $this->permissions_master 	= DB::table('permissions_master')
											->select('permissions_master.display_name as master_display_name', 'permissions_master.module_id as master_module_id')
											->where('permissions_master.status', 1)
											->orderBy('permissions_master.priority', 'ASC')
											->get();	
				}									
				$this->id					=	$id;
				$view 						=	view('service_company.role-permission.create', $this->data)->render();
				return Reply::dataOnly(['status' => 'success', 'view' => $view]);
		
		}
		
		
	public function loadRole ($id)
		{
				abort_if(!$this->user->cans('add_roles_permission'), 403);
				if($this->global_company_type == 3){
						 $this->permissions_master 	= DB::table('permissions_master')
											->select('permissions_master.display_name as master_display_name', 'permissions_master.module_id as master_module_id')
											->where('permissions_master.status', 1)
											->where('permissions_master.product_based', 1)
											->orderBy('permissions_master.priority', 'ASC')
											->get();	
				}else{
						 $this->permissions_master 	= DB::table('permissions_master')
											->select('permissions_master.display_name as master_display_name', 'permissions_master.module_id as master_module_id')
											->where('permissions_master.status', 1)
											->orderBy('permissions_master.priority', 'ASC')
											->get();	
				}									
				$this->id					=	$id;
				$view = view('service_company.role-permission.rolelist', $this->data)->render();
				return Reply::dataOnly(['status' => 'success', 'view' => $view]);
		}	
	public function saveRole(Request $request)
    	{
			
			abort_if(!$this->user->cans('add_roles_permission'), 403);
			
			$role_name			=	$user_id 			=	$company_id			=	'';
			$company_id			=	auth()->user()->company_id; 
        	$user_id 			=	auth()->user()->id;
        	$roleId 			=	$request->role;
			$role_name 			=	$request->role_name;
        	
			if(!empty($role_name)){
					
					$old_role_display_name	=	'';
					$role_priority			=	1;
					$role_row 				=	DB::table('roles')->select('*')->where('company_id', $company_id)->orderBy('roles.id', 'DESC')->first();
					if($role_row->priority >=0){
						$role_priority	=	$role_row->priority +1;	
					} 
					
					$old_role_display_name		=	$role_row->display_name;	
					if($old_role_display_name == $role_name){
						return Reply::dataOnly(['status' => 'warning', 'message'=> 'Role name already register. please choose another name.']);	
					}				
						
					$lastInsertId	=	'';
					$values 		=	array('company_id' => $company_id, 'name' => strtolower($role_name), 'display_name' => $role_name, 'description' => $role_name, 'priority' => $role_priority );
					DB::table('roles')->insert($values);
					$lastInsertId	=	DB::getPdo()->lastInsertId();
					if($lastInsertId > 0){
							if(!empty($roleId)){
								foreach($roleId  as $roleVal){
											$values 		=	array('permission_id' 	=>	$roleVal,	'role_id'	=>	$lastInsertId);
											DB::table('permission_role')->insert($values); 
								}
							}	
					}	
					
					$all_requisition			=	0;
					$all_requisition 			=	$request->all_requisition;
					if($all_requisition == 1){
					
											$values 		=	array('permission_id' => 58, 'role_id' => $lastInsertId);
											DB::table('permission_role')->insert($values); 
					
					}         
					$add_team_on_requisition	=	0;
					$add_team_on_requisition 	=	$request->add_team_on_requisition; 
					if($add_team_on_requisition == 1){
											$values 		= array('permission_id' => 59, 'role_id' => $lastInsertId);
											DB::table('permission_role')->insert($values);	 
					}
					   					
			}			

        return Reply::dataOnly(['status' => 'success']);
    }
	
	public function loadEditRole ($id)
		{
					if($this->global_company_type == 3){
							$this->permissions_master 	= DB::table('permissions_master')
													->select('permissions_master.display_name as master_display_name', 'permissions_master.module_id as master_module_id')
													->where('permissions_master.status', 1)
													->where('permissions_master.product_based', 1)
													->orderBy('permissions_master.priority', 'ASC')
													->get();	
					}else{
							 $this->permissions_master 	= DB::table('permissions_master')
													->select('permissions_master.display_name as master_display_name', 'permissions_master.module_id as master_module_id')
													->where('permissions_master.status', 1)
													->orderBy('permissions_master.priority', 'ASC')
													->get();	
					}
		
					$this->role_row 				=	DB::table('roles')->select('roles.display_name')->where('roles.id', $id)->first();
					$Rawlist 						=	DB::table('permission_role')->select('permission_role.permission_id')->where('permission_role.role_id', $id)->get()->toArray();
					$array 							=	array_column($Rawlist, 'permission_id');								
					$this->permission_role      	=	json_decode(json_encode($array), true);
															
					$this->id						=	$id;
					$view 							=	view('service_company.role-permission.loadEditRole', $this->data)->render();
					return Reply::dataOnly(['status' => 'success', 'view' => $view]);
	}
	




}