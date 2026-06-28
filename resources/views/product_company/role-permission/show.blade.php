@extends('layouts.product_company_layout')
@push('head-script')
<script></script>
<style>
body
{
    overflow-x:hidden;
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
      <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">View Role Details</h1>
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
        <!--end::Svg Icon-->
        Filter</a>
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
                <span class="form-check-label">Author</span> </label>
                <!--end::Options-->
                <!--begin::Options-->
                <label class="form-check form-check-sm form-check-custom form-check-solid">
                <input class="form-check-input" type="checkbox" value="2" checked="checked" />
                <span class="form-check-label">Customer</span> </label>
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
      <a href="javascript:void(0);" class="btn btn-sm btn-primary d-none" data-bs-toggle="modal" data-bs-target="#kt_modal_create_app" id="kt_toolbar_primary_button">Create</a>
      <!--end::Create-->
      <!--begin::Add New Role-->
      <a href="javascript:void(0);" class="btn btn-sm btn-primary">Add New Role</a>
      <!--end::Add New Role-->
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
    <div id="kt_content_container" class="container-fluid px-3">
      <!--begin::Layout-->
      <div class="d-flex flex-column flex-xl-row">
        <!--begin::Sidebar-->
        <div class="flex-column flex-lg-row-auto w-100 w-lg-300px mb-10">
          <!--begin::Card-->
          <div class="card card-flush">
            <!--begin::Card header-->
            <div class="card-header">
              <!--begin::Card title-->
              <div class="card-title">
                <h1 class="mb-0"><?php echo ucwords($roleslist->display_name); ?></h1>
              </div>
              <!--end::Card title-->
            </div>
            <!--end::Card header-->
            <?php 

//echo '<pre>'; print_r($permission_model); 
?>
            <!--begin::Card body-->
            <div class="card-body pt-0">
              <!--begin::Permissions-->
              <div class="d-flex flex-column text-gray-600 fs-6">
                <?php 
	$chk_cnt	=	count($permission_model_all)-5;
	
	if(!empty($permission_model)){
		foreach($permission_model as $pval){
			$html	=	'';
			$html	=	'<div class="d-flex align-items-center py-2 fs-6">
			<span class="bullet bg-primary me-3 "></span>'.ucwords($pval->master_display_name).'
		</div>';
		echo $html;
			
		}
	}	

if($chk_cnt > 5){

	echo '<div class="d-flex align-items-center py-2 fs-6">
	<span class="bullet bg-primary me-3"></span>
	<em>and '.$chk_cnt.' more...</em>
	</div>';	
} 

?>
              </div>
              <!--end::Permissions-->
            </div>
            <!--end::Card body-->
            <!--begin::Card footer-->
            <?php 
		if(($roleslist->name != 'admin')&& ($roleslist->priority != 0) )
		{
?>
            @if(in_array("edit_roles_permission", $userPermissions))
            <div class="card-footer pt-0">
              <button type="button" class="btn btn-light btn-active-primary editRole" data-toggle="modal" data-target="#kt_modal_edit_role" data-id="<?php echo @$id; ?>"> Edit Role </button>
            </div>
            @endif
            <?php 
}
?>
            <!--end::Card footer-->
          </div>
          <!--end::Card-->
        </div>
        <input type="hidden" name="teamcount" id="teamcount" value="<?php echo @$teamcount; ?>" />
        <!--end::Sidebar-->
        <!--begin::Content-->
        <div class="flex-lg-row-fluid ms-lg-8">
          <!--begin::Card-->
          <div class="card card-flush mb-6 mb-xl-9">
            <!--begin::Card header-->
            <div class="card-header pt-5">
              <!--begin::Card title-->
              <div class="card-title">
                <h1 class="d-flex align-items-center">Users Assigned <span class="text-gray-600 fs-6 ms-1 teamcount">(<?php echo @$teamcount; ?>)</span></h1>
              </div>
              <!--end::Card title-->
              <!--begin::Card toolbar-->
              <div class="card-toolbar">
                <!--begin::Search-->
                <div class="d-flex align-items-center position-relative my-1" data-kt-view-roles-table-toolbar="base">
                  <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                  <span class="svg-icon svg-icon-1 position-absolute ms-6">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                  </svg>
                  </span>
                  <!--end::Svg Icon-->
                  <input type="text" data-kt-roles-table-filter="search" name="inputSearch" id="inputSearch" class="form-control form-control-solid w-250px ps-15" placeholder="Search Users" />
                </div>
                <!--end::Search-->
                <!--begin::Group actions-->
                <div class="d-flex justify-content-end align-items-center d-none" data-kt-view-roles-table-toolbar="selected">
                  <div class="fw-bolder me-5"> <span class="me-2" data-kt-view-roles-table-select="selected_count"></span>Selected</div>
                  <button type="button" class="btn btn-danger" data-kt-view-roles-table-select="delete_selected">Delete Selected</button>
                </div>
                <!--end::Group actions-->
              </div>
              <!--end::Card toolbar-->
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
              <!--begin::Table-->
              <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0" id="myTable">
                <!--begin::Table head-->
                <thead>
                  <!--begin::Table row-->
                  <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="w-10px pe-2"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                        <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_roles_view_table .form-check-input-view" value="1" />
                      </div></th>
                    <th class="min-w-50px fs-6">ID</th>
                    <th class="min-w-150px fs-6">User</th>
                    <th class="min-w-125px fs-6">Joined Date</th>
                    <th class="text-end min-w-100px fs-6">Actions</th>
                  </tr>
                  <!--end::Table row-->
                </thead>
              </table>
              <!--end::Table-->
            </div>
            <!--end::Card body-->
          </div>
          <!--end::Card-->
        </div>
        <!--end::Content-->
      </div>
      <!--end::Layout-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
  <!--begin::Modal - Edit role-->
  <div class="modal fade" id="kt_modal_edit_role" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="fw-bolder">Edit Role</h2>
          <div class="btn btn-icon btn-sm btn-active-icon-primary"  data-dismiss="modal"> <span class="svg-icon svg-icon-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
              <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
            </svg>
            </span> </div>
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
<div id="kt_activitiesClient_toggle"></div>
<div id="kt_activitiesClient" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesClient_toggle" data-kt-drawer-close="#kt_activitiesClient_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">User Profile</h3>
        <!--begin::seprator-->
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
        <!--end::seprator-->
      </div>
      <!--end::Header-->
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesClient_close">
        <!--begin::Svg Icon | path: icons/duotone/Navigation/Close.svg-->
        <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
          </g>
        </svg>
        </span>
        <!--end::Svg Icon-->
        </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesClient_body"> &nbsp; </div>
  </div>
</div>
@endsection
@push('footer-script')
<script>
   $('.delete-category').click(function ()
   {
        var roleId = $(this).data('role-id');
        var url = "{{ route('product_company.role-permission.deleteRole') }}";
        var token = "{{ csrf_token() }}";
        $.easyAjax({
            type: 'POST',
            url: url,
            data: {'_token': token, 'roleId': roleId},
            success: function (response) {
            if (response.status == "success") {
                $.unblockUI();
                // swal("Deleted!", response.message, "success");
                window.location.reload();
            }
            }
        });
    });
    $('#save-category').click(function ()
    {
        $.easyAjax({
            url: '{{route('product_company.role-permission.storeRole')}}',
            container: '#createProjectCategory',
            type: "POST",
            data: $('#createProjectCategory').serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
    });
   
    $('.edit-category').click(function ()
    {
        var id = $(this).data('role-id');
        var url = '{{ route("product_company.role-permission.edit", ":id")}}';
        url = url.replace(':id', id);
   
        $('#modelHeading').html('Role Members');
        $.ajaxModal('#managePermissionModal', url);
    })
</script>
<script src=""></script>
<script src="assets/js/custom/apps/user-management/roles/view/view.js"></script>
<script src="assets/js/custom/apps/user-management/roles/view/update-role.js"></script>
<script>

$('.editRole').click(function(){
				
				
				var id 			= $(this).data('id');		   
				var url    		= "{{ route('product_company.settings.loadEditRole',':id') }}";
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
	
</script>
<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>"type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>"type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/calendar/dist/fullcalendar.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/jquery.fullcalendar.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/locale-all.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/schedule-calendar.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script>

$(document).ready(function() { 
      $('#inputSearch').keyup(function() {        
          tableLoad('filter');   
      });
});
   
var table;
tableLoad('load');
		   
function tableLoad(type) 
{
	
           
		var role_id          	= {{$id}};
		var inputSearch       	= $('#inputSearch').val();
        
     	table 					= $('#myTable').dataTable({
      
	   			responsive     	: true, 
      			processing      : true,
				ordering		: false, //disable ordering      			
      			destroy       	: true,   
      			stateSave     	: true,      
      			"bLengthChange" : false,   
				sDom			: 'lrtip',       
				          
          		//ajax			: '{!! route('product_company.apptracker.data') !!}?jobid='+jobid+'&mstatus_id='+mstatus_id+'&search='+inputSearch,
				
				ajax			: '{!! route('product_company.settings.teamdata') !!}?role_id='+role_id+'&search='+inputSearch,
                          
                language		: {
                       					"url": "<?php echo __("app.datatable") ?>"
                   				  },           
               "fnDrawCallback" : function (oSettings) {
                       										$("body").tooltip({
                           															selector: '[data-toggle="tooltip"]'
                       														  });
                   					},
                    
          "initComplete":function( settings, json){ 
          
            //$('.clientStatusLoader').html('');
            //$('.jobLoader').html('');
            //$('.sourceLoader').html('');
            
            //$('.search_np_idLoader').html('');
            //$('.search_passportLoader').html('');
            //$('.statusLoader').html(''); 
            
            
          }, 		  
           columns: [             
						{data: 'chkbox', name: 'chkbox', orderable: false, searchable: false},            
						{data: 'ID', name: 'ID',  orderable: false, searchable: false, width: '15%'},						
						{data: 'USER', name: 'USER', searchable : false, orderable : false, width: '15%'},						
						{data: 'JOINED_DATE', name: 'JOINED_DATE', searchable : false, orderable : false,  width: '5%'},
						{data: 'ACTIONS', name: 'ACTIONS',  searchable : false, orderable : false,  width: '10%'}
						
						/*,   

						{data: 'NoticePeriod', name: 'NoticePeriod', searchable : false, orderable : false},
						{data: 'Qualifaction', name: 'Qualifaction',  searchable : false, orderable : false},
												
						{data: 'Interview', name: 'Interview', orderable: false},
						{data: 'Location', name: 'Location', orderable : false},   
						{data: 'Source', name: 'Source', orderable: false},
						{data: 'Stage', name: 'Stage', orderable: false}*/
					]         
      
             }); 
           new $.fn.dataTable.FixedHeader(table);
		   
} 

table.on('click', '.show-detail-team', function (){

	
	
			$('#kt_activitiesClient_toggle').trigger('click');
		
			
		
		var id    			= $(this).data('row-id');
		var url   			= "{{ route('product_company.team.viewmore',':id') }}";
		url     			= url.replace(':id', id);
		var baseurl    		= "{{ asset('assets/images/Loading2.gif') }}"; 
		$.ajax({
						type  : 'GET',
						url   : url,
						 beforeSend: function() {
                                  $('#kt_activitiesClient_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                    	},
						success : function (response) 
						{
								$('#kt_activitiesClient_body').html(response.view);
								/*setTimeout(
								()=>
								{
										if (response.status == "success")
										{
											$('#kt_activitiesClient_body').html(response.view);
										}
								}, 500);*/
  		  					
						}
	  			});
				
				
				
	

}); 


table.on('click', '.delete-role', function (){

		
		//alert("call me"); return false; 
		
		
		
Swal.fire({
			  title					: 'Are you sure?',
			  text					: "You won't be able to revert this!",
			  icon					: 'warning',
			  showCancelButton		: true,
			  confirmButtonColor	: '#3085d6',
			  cancelButtonColor		: '#d33',
			  confirmButtonText		: 'Yes, delete it!'
}).then((result) => {
  if (result.isConfirmed) {
  
			var user_id    			= $(this).data('row-id');
			var role_id          	= {{$id}};
			var url 				= "{{ route('product_company.settings.removerole', ':user_id') }}";
			url 					= url.replace(':user_id', user_id);
			var token 				= '{{ csrf_token() }}';
			$.ajax({
   							type: 'POST',
   							url: url,
   							data: {'_token':token, '_method': 'DELETE', 'user_id':user_id, 'role_id':role_id},
   							success: function (response) {
   								if(response.status == 'success') {
   																	tableLoad('load');
																	Swal.fire(
																					'Deleted!',
																					'Your role has been deleted.',
																					'success'
																			)
																			
   	
   								}
   							}
   						});  
  }
})
	

});
 

	
	
 $('body').on('click', '.actiondiv', function(){
 
 		$('.clight_').hide();
 		var id = $(this).data('row-id');
		$('#clight_'+id).show();
		
 		//alert("call="+id); 
 });

$(document).click(function(event) {
    if ( !$(event.target).hasClass('clight_')  && !$(event.target).hasClass('actiondiv') ) {
         $(".clight_").hide();
    }
});

</script>
@endpush