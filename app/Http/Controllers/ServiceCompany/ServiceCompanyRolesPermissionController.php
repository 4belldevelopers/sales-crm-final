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
use App\Models\Module;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Gate;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceCompanyRolesPermissionController extends ServiceCompanyBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle 	= 'menu.rolesPermission';
        $this->pageIcon 	= 'ti-lock';
    }

	public function index()
    {
	
		   abort_if(Gate::denies('view_roles_permission'), 403);
			// $permissionIds = DB::table('permissions')->pluck('id')->toArray();
			// $roles = DB::table('roles')->pluck('id')->toArray();

			// foreach ($roles as $rid) {
			// 	foreach ($permissionIds as $pid) {
			// 		if(!DB::table('role_has_permissions')->where('permission_id', $pid)->where('role_id', $rid)->exists()){
			// 					DB::table('role_has_permissions')->insert([
			// 					'permission_id' => $pid,
			// 					'role_id' => $rid
			// 				]);
			// 			}
			// 		}
			// }


			// foreach ($roles as $rid) {
			// 	     $company = DB::table('roles')->where('id','=',$rid)->select('company_id')->first();
			// 		 if ($company) {
			// 			   $company_id = $company->company_id;
			// 			   $users = DB::table('users')->whereNotNull('company_id')->where('company_id','=',$company_id)->select('id')->first();
			// 			   if ($users) {
			// 				    $model_id = $users->id;
			// 						if(!DB::table('model_has_roles')->where('role_id', $rid)->where('model_id', $model_id)->exists()){
			// 						DB::table('model_has_roles')->insert([
			// 							'role_id' => $rid,
			// 							'model_type' => 'App\Models\User',
			// 							'model_id' => $model_id
			// 						]);
			// 			   }
			// 		} 
					
			// 	}
			// }

        	// foreach ($roles as $rid) {
			// 	foreach ($permissionIds as $pid) {

			// 		 $company = DB::table('roles')->where('id','=',$rid)->select('company_id')->first();
			// 		 if ($company) {
			// 			   $company_id = $company->company_id;
			// 			   $users = DB::table('users')->whereNotNull('company_id')->where('company_id','=',$company_id)->select('id')->first();
			// 			   if ($users) {
			// 				    $model_id = $users->id;
			// 						if(!DB::table('model_has_permissions')->where('permission_id', $pid)->where('model_id', $model_id)->exists()){
			// 						DB::table('model_has_permissions')->insert([
			// 							'permission_id' => $pid,
			// 							'model_type' => 'App\Models\User',
			// 							'model_id' => $model_id
			// 						]);
			// 			   }
			// 		} 
					
			// 	}
			// 	}
			// }





			$company_id					=	auth()->user()->company_id; 

			$roles = Role::company($company_id)
				->with(['permissions' => function ($q) {
					$q->select('id', 'name', 'group_name');
				}])
				->get();
				// dd($roles);

			$roles->each(function ($role) {
				$role->grouped_permissions = $role->permissions
					->groupBy('group_name')
					->map(fn($g) => $g->pluck('name'));
			});


			$this->roles =  $roles;
			$company_id					=	'';
			 				
			// $this->roles 				=	Role::where('name', '!=', 'admin')->get();
			$this->totalPermissions 	=	Permission::count();
			$this->modules 				=	Module::all();			
			$this->roleslist 			=	DB::table('roles')
											->select('roles.id', 'roles.display_name', 'roles.priority', 'roles.name','roles.manual_add')
											->where('roles.company_id', '=', $company_id)
											->orderBy('roles.priority', 'ASC')
											->orderBy('roles.name', 'ASC')
											->get();
			$this->company_id			=	$company_id;

			// dd($this->roles );
		
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
				abort_if(!$this->user->can('add_roles_permission'), 403);

				$company_id					=	auth()->user()->company_id;

				if($this->global_company_type == 3){

						$permissions = DB::table('permissions')
							->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
							->join('roles', 'roles.id', '=', 'role_has_permissions.role_id')
							->where('roles.company_id', $company_id)
							->where('permissions.status', 1)
							->where('permissions.product_based', 1)
							->whereNotIn('permissions.id', [58,59,14,15,16,17])
							->select('permissions.id', 'permissions.name', 'permissions.display_name','permissions.status', 'permissions.group_name')
							->orderBy('permissions.group_name')
							->orderBy('permissions.name')
							->distinct()
							->get()
							->groupBy('group_name')
							->map(function ($group) {
								return $group->map(function ($perm) {
									return [
										'id' => $perm->id,
										'name' => $perm->name,
										'display_name' => $perm->display_name,
										'status' => $perm->status,
									];
								});
							});
				}else{
					$permissions = DB::table('permissions')
					->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
					->join('roles', 'roles.id', '=', 'role_has_permissions.role_id')
					->where('roles.company_id', $company_id)
					->where('permissions.status', 1)
					->whereNotIn('permissions.id', [58,59,14,15,16,17])
					->select('permissions.id', 'permissions.name', 'permissions.display_name','permissions.status', 'permissions.group_name')
					->orderBy('permissions.group_name')
					->orderBy('permissions.name')
					->distinct()
					->get()
					->groupBy('group_name')
					->map(function ($group) {
						return $group->map(function ($perm) {
							return [
								'id' => $perm->id,
								'name' => $perm->name,
								'display_name' => $perm->display_name,
								'status' => $perm->status,
							];
						});
					});
				}

                $this->permissions = $permissions;
									
				$this->id					=	$id;
				$view = view('service_company.role-permission.rolelist', $this->data)->render();
				return Reply::dataOnly(['status' => 'success', 'view' => $view]);
	}	
	public function saveRole(Request $request)
    {
			// dd("djdjdjjd");
			abort_if(!$this->user->can('add_roles_permission'), 403);
			
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
					$values 		=	array('company_id' => $company_id, 'name' => strtolower($role_name), 'display_name' => $role_name, 'description' => $role_name, 'priority' => $role_priority,'manual_add' => 1  );

					DB::table('roles')->insert($values);
					$lastInsertId	=	DB::getPdo()->lastInsertId();

					if($lastInsertId > 0){
							if(!empty($roleId)){
								foreach($roleId  as $roleVal){
											$values 		=	array('permission_id' 	=>	$roleVal,	'role_id'	=>	$lastInsertId);
											DB::table('role_has_permissions')->insertOrIgnore($values); 
								}
							}	
					}	
					
					$all_requisition			=	0;
					$all_requisition 			=	$request->all_requisition;
					if($all_requisition == 1){
					
											$values 		=	array('permission_id' => 58, 'role_id' => $lastInsertId);
											DB::table('role_has_permissions')->insertOrIgnore($values); 
					
					}         
					$add_team_on_requisition	=	0;
					$add_team_on_requisition 	=	$request->add_team_on_requisition; 
					if($add_team_on_requisition == 1){
											$values 		= array('permission_id' => 59, 'role_id' => $lastInsertId);
											DB::table('role_has_permissions')->insertOrIgnore($values);	 
					}
					   					
			}			

        return Reply::dataOnly(['status' => 'success']);
    }
	public function loadEditRole ($id)
	{
			$company_id					=	auth()->user()->company_id;

			if($this->global_company_type == 3){

						$permissions = DB::table('permissions')
							->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
							->join('roles', 'roles.id', '=', 'role_has_permissions.role_id')
							->where('roles.company_id', $company_id)
							->where('permissions.status', 1)
							->where('permissions.product_based', 1)
							->whereNotIn('permissions.id', [58,59,14,15,16,17])
							->select('permissions.id', 'permissions.name', 'permissions.display_name','permissions.status', 'permissions.group_name')
							->orderBy('permissions.group_name')
							->orderBy('permissions.name')
							->distinct()
							->get()
							->groupBy('group_name')
							->map(function ($group) {
								return $group->map(function ($perm) {
									return [
										'id' => $perm->id,
										'name' => $perm->name,
										'display_name' => $perm->display_name,
										'status' => $perm->status,
									];
								});
							});
			}else{
					$permissions = DB::table('permissions')
					->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
					->join('roles', 'roles.id', '=', 'role_has_permissions.role_id')
					->where('roles.company_id', $company_id)
					->where('permissions.status', 1)
					->whereNotIn('permissions.id', [58,59,14,15,16,17])
					->select('permissions.id', 'permissions.name', 'permissions.display_name','permissions.status', 'permissions.group_name')
					->orderBy('permissions.group_name')
					->orderBy('permissions.name')
					->distinct()
					->get()
					->groupBy('group_name')
					->map(function ($group) {
						return $group->map(function ($perm) {
							return [
								'id' => $perm->id,
								'name' => $perm->name,
								'display_name' => $perm->display_name,
								'status' => $perm->status,
							];
						});
					});
			}

                	$this->permissions = $permissions;
		
					$this->role_row 				=	DB::table('roles')->select('roles.display_name')->where('roles.id', $id)->first();

					$Rawlist 						=	DB::table('role_has_permissions')->select('role_has_permissions.permission_id')->where('role_has_permissions.role_id', $id)->get()->toArray();

					$array 							=	array_column($Rawlist, 'permission_id');	

					$this->permission_role      	=	json_decode(json_encode($array), true);
															
					$this->id						=	$id;

					$view 							=	view('service_company.role-permission.loadEditRole', $this->data)->render();
					return Reply::dataOnly(['status' => 'success', 'view' => $view]);

	}
	public function manualDeleteRole($id){

		if(DB::table('role_user')->where('role_id','=',$id)->exists()){
			$roles =	Role::where('display_name', '=', 'Recruiter')->first();
			DB::table('role_user')
					->where('role_id', $id)
					->update(array('role_id' => strtolower($roles->id) ));  
			}
			Role::whereId($id)->delete();
		
		return Reply::dataOnly(['status' => 'success', 'view' => '']);

	}

}