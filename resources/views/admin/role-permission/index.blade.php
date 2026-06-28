@extends('layouts.demo_admin_app')

@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/icheck/skins/all.css') }}<?php echo TM_VERSION; ?>">
<link href="{{ asset('assets/node_modules/select2/dist/css/select2.min.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}<?php echo TM_VERSION; ?>">
<style>

body
{
    overflow-x:hidden;
}

.pll-1
{
    padding-left: 1.5em!important;
}

</style>
@endpush

@section('content')
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
    <!--begin::Container-->
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <!--begin::Page title-->
        <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
            <!--begin::Title-->
            <h5 class="d-flex align-items-center text-dark fw-bolder my-1">Roles & Permissions</h5>
            <!--end::Title-->
            <!--begin::Separator-->
            <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
            <!--end::Separator-->
        </div>
        <!--end::Page title-->
        <!--begin::Actions-->
        <div class="d-flex align-items-center py-1 d-none">
            <!--begin::Wrapper-->
            <div class="me-4">
                <!--begin::Menu-->
                <a href="javascript:void(0);" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
                <span class="svg-icon svg-icon-5 svg-icon-gray-500 me-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black" />
                    </svg>
                </span>
                <!--end::Svg Icon-->Filter</a>
                <!--begin::Menu 1-->
                <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_61233af9d5c20">
                    <!--begin::Header-->
                    <div class="px-7 py-5">
                        <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Menu separator-->
                    <div class="separator border-gray-200"></div>
                    <!--end::Menu separator-->
                    <!--begin::Form-->
                    <div class="px-7 py-5">
                        <!--begin::Input group-->
                        <div class="mb-10">
                            <!--begin::Label-->
                            <label class="form-label fw-bold">Status:</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <div>
                                <select class="form-select form-select-solid" data-kt-select2="true" data-placeholder="Select option" data-dropdown-parent="#kt_menu_61233af9d5c20" data-allow-clear="true">
                                    <option></option>
                                    <option value="1">Approved</option>
                                    <option value="2">Pending</option>
                                    <option value="2">In Process</option>
                                    <option value="2">Rejected</option>
                                </select>
                            </div>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="mb-10">
                            <!--begin::Label-->
                            <label class="form-label fw-bold">Member Type:</label>
                            <!--end::Label-->
                            <!--begin::Options-->
                            <div class="d-flex">
                                <!--begin::Options-->
                                <label class="form-check form-check-sm form-check-custom form-check-solid me-5">
                                    <input class="form-check-input" type="checkbox" value="1" />
                                    <span class="form-check-label">Author</span>
                                </label>
                                <!--end::Options-->
                                <!--begin::Options-->
                                <label class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="2" checked="checked" />
                                    <span class="form-check-label">Customer</span>
                                </label>
                                <!--end::Options-->
                            </div>
                            <!--end::Options-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="mb-10">
                            <!--begin::Label-->
                            <label class="form-label fw-bold">Notifications:</label>
                            <!--end::Label-->
                            <!--begin::Switch-->
                            <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="" name="notifications" checked="checked" />
                                <label class="form-check-label">Enabled</label>
                            </div>
                            <!--end::Switch-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Actions-->
                        <div class="d-flex justify-content-end">
                            <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true">Reset</button>
                            <button type="submit" class="btn btn-sm btn-primary" data-kt-menu-dismiss="true">Apply</button>
                        </div>
                        <!--end::Actions-->
                    </div>
                    <!--end::Form-->
                </div>
                <!--end::Menu 1-->
                <!--end::Menu-->
            </div>
            <!--end::Wrapper-->
            <!--begin::Create-->
           
            <!--end::Create-->
           
		   
@if(in_array("add_roles_permission", $userPermissions))		   
		   
		    <!--begin::Add New Role-->
            <a href="javascript:void(0);" class="btn btn-sm btn-primary">Add New Role</a>
            <!--end::Add New Role-->
@endif				
			
			
			
        </div>
        <!--end::Actions-->
    </div>
    <!--end::Container-->
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container-fluid container-fluid pl-1 pe-2">
            <!--begin::Row-->
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-5 g-xl-9">	
					
@if(in_array("add_roles_permission", $userPermissions))					
<?php 

	if(!empty($roleslist))
	{
		foreach($roleslist as $rlist)  
		{
			$role_id	=	'';
			$role_id	=	$rlist->id;	
			
			$permission_model_count 		=	0;
			$permission_model_count 		=	DB::table('role_user')
												->join('users', 'users.id', '=', 'role_user.user_id')
												->select('*')
												->where('role_user.role_id', $role_id)
												->where('users.type', 0)
												->count();		
											  
			$permission_model 				= DB::table('roles')
											  ->join('permission_role', 'permission_role.role_id', '=', 'roles.id')
											  ->join('permissions', 'permissions.id', '=', 'permission_role.permission_id')
											  ->join('permissions_master', 'permissions_master.module_id', '=', 'permissions.module_id')
											  ->select('roles.id', 'roles.display_name', 'permissions_master.display_name as master_display_name')
											  ->where('roles.id', $role_id)
											  ->groupBy('permissions.module_id')
											  ->orderBy('roles.id', 'DESC')
											  ->skip(0)->take(5)->get(); 
			
			$permission_model_all 			= DB::table('roles')
											  ->join('permission_role', 'permission_role.role_id', '=', 'roles.id')
											  ->join('permissions', 'permissions.id', '=', 'permission_role.permission_id')
											  ->join('permissions_master', 'permissions_master.module_id', '=', 'permissions.module_id')
											  ->select('roles.id')
											  ->where('roles.id', $role_id)
											  ->groupBy('permissions.module_id')
											  ->orderBy('roles.id', 'DESC')
											  ->get(); 								  		
			
?>				
                <div class="col-md-4">
                    <div class="card card-flush h-md-100">
                        <div class="card-header">
                            <div class="card-title">
                                <h1><?php echo ucwords($rlist->display_name); ?></h1>
                            </div>
                        </div>
                        <div class="card-body pt-1">
                            <div class="fw-bolder text-gray-600 mb-5 fs-6">Total users with this role: <?php echo $permission_model_count; ?></div>
                            <div class="d-flex flex-column text-gray-600">
<?php 

			
			if(!empty($permission_model))
			{
				foreach($permission_model as $pval)
				{
					echo ' <div class="d-flex align-items-center py-2 fs-6">
                                    <span class="bullet bg-primary me-3"></span>'.ucwords($pval->master_display_name).'
                                </div>';
				}
			}
			
			if(count($permission_model_all ) > 5)
			{
				$rest_cnt	=	0;
				$rest_cnt	=	count($permission_model_all ) - 5;
				
				echo '<div class="d-flex align-items-center py-2 fs-6">
                                    <span class="bullet bg-primary me-3"></span>
                                    <em>and '.$rest_cnt.' more...</em>
                                </div>';  
			}
			
																				
?>							   
                            </div>
                        </div>
						
<div class="card-footer flex-wrap pt-0"> 
	<a href="<?php echo url('/admin/settings/show-role/'.@$role_id);?>" class="btn btn-light btn-active-primary my-1 me-2">View Role</a>
<?php 
		if(($rlist->priority != 0) && ($rlist->name != 'admin') ){
?>
	
	@if(in_array("edit_roles_permission", $userPermissions))	
		<button type="button" class="btn btn-light btn-active-light-primary my-1 editRole" data-toggle="modal" data-target="#kt_modal_edit_role" data-id="<?php echo @$role_id; ?>">
			Edit Role
		</button>
	@endif
	
<?php 
	}
?>
</div>


                    </div>
                </div>
<?php 
		}
	}	
?>				
@endif				
				
				
				
				
				
				
                <!--begin::Analyst-->
                <div class="col-md-4 d-none">
                    <!--begin::Card-->
                    <div class="card card-flush h-md-100">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <!--begin::Card title-->
                            <div class="card-title">
                                <h1>Analyst</h1>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-1">
                            <!--begin::Users-->
                            <div class="fw-bolder text-gray-600 mb-5 fs-6">Total users with this role: 4</div>
                            <!--end::Users-->
                            <!--begin::Permissions-->
                            <div class="d-flex flex-column text-gray-600">
                                <div class="d-flex align-items-center py-2 fs-6">
                                    <span class="bullet bg-primary me-3"></span>No Admin Controls
                                </div>
                                <div class="d-flex align-items-center py-2 fs-6">
                                    <span class="bullet bg-primary me-3"></span>View and Edit Financial Summaries
                                </div>
                                <div class="d-flex align-items-center py-2 fs-6">
                                    <span class="bullet bg-primary me-3"></span>Enabled Bulk Reports
                                </div>
                                <div class="d-flex align-items-center py-2 fs-6">
                                    <span class="bullet bg-primary me-3"></span>View Payouts only
                                </div>
                                <div class="d-flex align-items-center py-2 fs-6">
                                    <span class="bullet bg-primary me-3"></span>View Disputes only
                                </div>
                                <div class="d-flex align-items-center py-2 fs-6">
                                    <span class="bullet bg-primary me-3"></span>
                                    <em>and 2 more...</em>
                                </div>
                            </div>
                            <!--end::Permissions-->
                        </div>
                        <!--end::Card body-->
                        <!--begin::Card footer-->
                        <div class="card-footer flex-wrap pt-0">
                            <a href="{{route('admin.role-permission.create')}}" class="btn btn-light btn-active-primary my-1 me-2">View Role</a>
                            <button type="button" class="btn btn-light btn-active-light-primary my-1" data-toggle="modal" data-target="#kt_modal_edit_role">Edit Role</button>
                        </div>
                        <!--end::Card footer-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Analyst-->
                <!--begin::Support-->
                <div class="col-md-4 d-none">
                    <!--begin::Card-->
                    <div class="card card-flush h-md-100">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <!--begin::Card title-->
                            <div class="card-title">
                                <h1>Support</h1>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-1">
                            <!--begin::Users-->
                            <div class="fw-bolder text-gray-600 mb-5 fs-6">Total users with this role: 23</div>
                            <!--end::Users-->
                            <!--begin::Permissions-->
                            <div class="d-flex flex-column text-gray-600">
                                <div class="d-flex align-items-center py-2 fs-6">
                                    <span class="bullet bg-primary me-3"></span>No Admin Controls
                                </div>
                                <div class="d-flex align-items-center py-2 fs-6">
                                    <span class="bullet bg-primary me-3"></span>View Financial Summaries only
                                </div>
                                <div class="d-flex align-items-center py-2 fs-6">
                                    <span class="bullet bg-primary me-3"></span>View Payouts only
                                </div>
                                <div class="d-flex align-items-center py-2 fs-6">
                                    <span class="bullet bg-primary me-3"></span>View and Edit Disputes
                                </div>
                                <div class="d-flex align-items-center py-2 fs-6">
                                    <span class="bullet bg-primary me-3"></span>Response to Customer Feedback
                                </div>
                            </div>
                            <!--end::Permissions-->
                        </div>
                        <!--end::Card body-->
                        <!--begin::Card footer-->
                        <div class="card-footer flex-wrap pt-0">
                            <a href="{{route('admin.role-permission.create')}}" class="btn btn-light btn-active-primary my-1 me-2">View Role</a>
                            <button type="button" class="btn btn-light btn-active-light-primary my-1" data-toggle="modal" data-target="#kt_modal_edit_role">Edit Role</button>
                        </div>
                        <!--end::Card footer-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Support-->
                <!--begin::Trial-->
                <div class="col-md-4 px-0 d-none">
                    <!--begin::Card-->
                    <div class="card card-flush h-md-100">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <!--begin::Card title-->
                            <div class="card-title">
                                <h1>Trial</h1>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-1">
                            <!--begin::Users-->
                            <div class="fw-bolder text-gray-600 mb-5 fs-6">Total users with this role: 546</div>
                            <!--end::Users-->
                            <!--begin::Permissions-->
                            <div class="d-flex flex-column text-gray-600">
                                <div class="d-flex align-items-center py-2 fs-6">
                                    <span class="bullet bg-primary me-3"></span>No Admin Controls
                                </div>
                                <div class="d-flex align-items-center py-2 fs-6">
                                    <span class="bullet bg-primary me-3"></span>View Financial Summaries only
                                </div>
                                <div class="d-flex align-items-center py-2 fs-6">
                                    <span class="bullet bg-primary me-3"></span>View Bulk Reports only
                                </div>
                                <div class="d-flex align-items-center py-2 fs-6">
                                    <span class="bullet bg-primary me-3"></span>View Payouts only
                                </div>
                                <div class="d-flex align-items-center py-2 fs-6">
                                    <span class="bullet bg-primary me-3"></span>View Disputes only
                                </div>
                            </div>
                            <!--end::Permissions-->
                        </div>
                        <!--end::Card body-->
                        <!--begin::Card footer-->
                        <div class="card-footer flex-wrap pt-0">
                            <a href="{{route('admin.role-permission.create')}}" class="btn btn-light btn-active-primary my-1 me-2">View Role</a>
                            <button type="button" class="btn btn-light btn-active-light-primary my-1" data-toggle="modal" data-target="#kt_modal_edit_role">Edit Role</button>
                        </div>
                        <!--end::Card footer-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Trial-->

@if(in_array("add_roles_permission", $userPermissions))		   
		   
		     <!--begin::Add new role-->
                <div class="ol-md-4">
                    <!--begin::Card-->
                    <div class="card h-md-100">
                        <!--begin::Card body-->
                        <div class="card-body d-flex flex-center" id="addNewRole">
                            <!--begin::Button-->
                            <button type="button" class="btn btn-clear d-flex flex-column flex-center" data-toggle="modal" data-target="#kt_modal_add_role">
                                <!--begin::Illustration-->
                                <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/illustrations/sketchy-1/4.png" alt="" class="mw-100 mh-150px mb-7" />
                                <!--end::Illustration-->
                                <!--begin::Label-->
                                <div class="fw-bolder fs-3 text-gray-600 text-hover-primary">Add New Role</div>
                                <!--end::Label-->
                            </button>
                            <!--begin::Button-->
                        </div>
                        <!--begin::Card body-->
                    </div>
                    <!--begin::Card-->
                </div>
             <!--begin::Add new role-->
@endif				
			              
			   
				
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->
    
    
	
	
	
	
<div class="modal fade" id="kt_modal_add_role" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-750px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bolder">Add Role</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary"  data-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 my-7">
                 	<div class="stage_body_content" id="stage_body_content"></div> 
                </div>
            </div>
        </div>
</div>





    <!--begin::Modal - Edit role-->
    <div class="modal fade" id="kt_modal_edit_role" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-750px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bolder">Edit Role</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary"  data-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 my-7">
					<div class="edit_body_content" id="edit_body_content"></div> 
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - Edit role-->
	
	
	
	
</div>
<!--end::Content-->










<div class="row px-3 d-none">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="col-md-6 d-flex justify-content-between align-items-center">
                    <!-- Page Title -->
                    <span class="card-title pt-2 pb-2"><i class="fa fa-gear"></i>&nbsp;Roles & Permissions 2</span>
                    <!-- Page Title End -->
                    <a href="javascript:void();" id="addRole" class="btn btn-success btn-sm btn-outline"><i class="fa fa-gear"></i>&nbsp;@lang("modules.roles.addRole")</a>
                </div>

                @foreach($roles as $role)
                    <div class="col-md-6 b-all mt-2">
                        <div class="row">
                            <div class="col-md-4 text-center p-2 bg-secondary ">
                                <h5 class="text-white mt-2 mb-2"><strong>{{ ucwords($role->display_name) }}</strong></h5>
                            </div>
                            <div class="col-md-4 text-center bg-secondary role-members">
                                {{--<button class="btn btn-xs btn-danger btn-rounded show-members" data-role-id="{{ $role->id }}"><i class="fa fa-users"></i> {{ count($role->roleuser)  }} Member(s)</button>--}}
                            </div>
                            <div class="col-md-4 p-2 bg-secondary" style="padding-bottom: 11px !important;">
                                <button class="btn btn-default btn-sm btn-rounded pull-right toggle-permission" data-role-id="{{ $role->id }}"><i class="fa fa-key"></i> Permissions</button>
                            </div>


                            <div class="col-md-12 b-t permission-section" style="display: none;" id="role-permission-{{ $role->id }}" >
                                <table class="table ">
                                    <thead>
                                    <tr class="bg-white">
                                        <th>
                                            <div class="form-group">
                                                <input type="checkbox" value="{{ $role->id }}" class=" select_all_permission" id="select_all_permission_{{ $role->id }}"
                                                        @if(count($role->permissions) == $totalPermissions) checked @endif
                                                >
                                                <label for="select_all_permission_{{ $role->id }}">@lang('modules.permission.selectAll')</label>
                                            </div>
                                        </th>
                                        <th>@lang('app.add')</th>
                                        <th>@lang('app.view')</th>
                                        <th>@lang('app.update')</th>
                                        <th>@lang('app.delete')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($modules as $module)
                                        <tr>
                                            <td>{{ ucwords($module->module_name) }}

                                                @if($module->description != '')
                                                    <a class="mytooltip" href="javascript:void(0)"> <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span class="tooltip-inner2">{{ $module->description  }}</span></span></span></a>
                                                @endif
                                            </td>

                                            @foreach($module->permissions as $permission)
                                                <td>
                                                    <div class="switchery-demo">
                                                        <input type="checkbox"
                                                                @if($role->hasPermission([$permission->name]))
                                                                checked
                                                                @endif
                                                                class="js-switch assign-role-permission permission_{{ $role->id }}" data-size="small" data-color="#00c292" data-permission-id="{{ $permission->id }}" data-role-id="{{ $role->id }}" />
                                                    </div>
                                                </td>
                                            @endforeach

                                            @if(count($module->permissions) < 4)
                                                @for($i=1; $i<=(4-count($module->permissions)); $i++)
                                                    <td>&nbsp;</td>
                                                @endfor
                                            @endif

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
{{--Ajax Modal Start for--}}
<div class="modal fade bs-modal-md in" id="managePermissionModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
            </div>
            <div class="modal-body">
                Loading...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn blue">Save changes</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
{{--Ajax Modal Ends--}}
@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script>
    $(function () {
        $('.assign-role-permission').on('change', assignRollPermission);
    });

    $('.toggle-permission').click(function () {
        var roleId = $(this).data('role-id');
        $('#role-permission-'+roleId).toggle();
    })

    // Switchery
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    $('.js-switch').each(function() {
        new Switchery($(this)[0], $(this).data());
    });

    // Initialize multiple switches
    var animating = false;
    var masteranimate = false;

    var assignRollPermission = function () {

        var roleId = $(this).data('role-id');
        var permissionId = $(this).data('permission-id');

        if($(this).is(':checked'))
            var assignPermission = 'yes';
        else
            var assignPermission = 'no';

        var url = '{{route('admin.role-permission.store')}}';

        $.easyAjax({
            url: url,
            type: "POST",
            data: { 'roleId': roleId, 'permissionId': permissionId, 'assignPermission': assignPermission, '_token': '{{ csrf_token() }}' }
        })
    };

    $('.assign-role-permission').change(assignRollPermission());

    $('.select_all_permission').change(function () {
        if($(this).is(':checked')){
            var roleId = $(this).val();
            var url = '{{ route('admin.role-permission.assignAllPermission') }}';

            $.easyAjax({
                url: url,
                type: "POST",
                data: { 'roleId': roleId, '_token': '{{ csrf_token() }}' },
                success: function () {
                    masteranimate = true;
                    if (!animating){
                        var masterStatus = true;
                        $('.assign-role-permission').off('change');
                        $('input.permission_'+roleId).each(function(index){
                            var switchStatus = $('input.permission_'+roleId)[index].checked;
                            if(switchStatus != masterStatus){
                                $(this).trigger('click');
                            }
                        });
                        $('.assign-role-permission').on('change', assignRollPermission);
                    }
                    masteranimate = false;
                }
            })
        }
        else{
            var roleId = $(this).val();
            var url = '{{ route('admin.role-permission.removeAllPermission') }}';

            $.easyAjax({
                url: url,
                type: "POST",
                data: { 'roleId': roleId, '_token': '{{ csrf_token() }}' },
                success: function () {
                    masteranimate = true;
                    if (!animating){
                        var masterStatus = false;
                        $('.assign-role-permission').off('change');
                        $('input.permission_'+roleId).each(function(index){
                            var switchStatus = $('input.permission_'+roleId)[index].checked;
                            if(switchStatus != masterStatus){
                                $(this).trigger('click');
                            }
                        });
                        $('.assign-role-permission').on('change', assignRollPermission);
                    }
                    masteranimate = false;
                }
            })
        }
    })

    $('.show-members').click(function () {
        var id = $(this).data('role-id');
        var url = '{{ route('admin.role-permission.showMembers', ':id')}}';
        url = url.replace(':id', id);

        $('#modelHeading').html('Role Members');
        $.ajaxModal('#managePermissionModal', url);
    })

    $('#addRole').click(function () {
        var url = '{{ route('admin.role-permission.create')}}';

        $('#modelHeading').html('Role Members');
        $.ajaxModal('#managePermissionModal', url);
    })
	




</script>



<script>
$(document).ready(function(){

	$('#addNewRole').click(function(){
				
				var id       	= 0;  		   
				var url    		= "{{ route('admin.settings.loadRole',':id') }}";
				url      		= url.replace(':id', id);
				$.ajax({
								type  			:   'GET',
								url   			:	url,
								success 		:	function (response) {
																			if (response.status == "success") 
																			{
																				$('#stage_body_content').html(response.view);
																			}
												    }
			          });
	
	});	
	
	$('.editRole').click(function(){
				
				
				var id 			= $(this).data('id');		   
				var url    		= "{{ route('admin.settings.loadEditRole',':id') }}";
				url      		= url.replace(':id', id);
				$.ajax({
								type  			:   'GET',
								url   			:	url,
								success 		:	function (response) {
																			if (response.status == "success") 
																			{
																				$('#edit_body_content').html(response.view);
																			}
												    }
			          });
	
	});		
		
});	
</script>

@endpush