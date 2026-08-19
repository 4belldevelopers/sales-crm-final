@extends('layouts.demo_admin_app')
<style>
body
{
    overflow-x:hidden;
}
</style>

@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
@endpush

@section('content')
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack px-5">
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <!--begin::Team-->
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
          <span class="text-gray-800 mb-1">Team</span>
        </h5>
        <span class="text-gray-600" id="">15</span>
      </div>
      <!--end::Team-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
      <!--end::Separator-->
    </div>
    <!--end::Page title-->
    <!--begin::Actions-->
    <div class="d-flex align-items-center py-1">
      <!--begin::Search-->
      <div class="d-flex align-items-center position-relative my-1 me-3">
        <!--begin::Svg Icon-->
        <span class="svg-icon svg-icon-1 position-absolute ms-6">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
          <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
          <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
        </svg>
        </span>
        <!--end::Svg Icon-->
        <input type="text" id="inputSearch" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search..." />
      </div>
      <!--end::Search-->
      
@if(in_array("add_team", $userPermissions))		  
	  <!--begin::Add member-->
      <a href="javascript:void(0);" class="btn btn-primary align-self-center add_team_member me-3" data-toggle="modal" data-target="#add_team_member">
        <span class="svg-icon svg-icon-primary svg-icon-1x">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <polygon points="0 0 24 0 24 24 0 24"></polygon>
                <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                </g>
            </svg>
        </span>Add Member
      </a>
      <!--end::Add member-->
@endif	  
	  
	  
      <!--begin::Update role-->
      <!--<a href="javascript:void(0);" class="btn btn-primary align-self-center update_role" data-toggle="modal" data-target="#update_role">Update role</a>-->
      <!--end::Update role-->
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
            <!--begin::Card-->
            <div class="card">
                <!--begin::Card body-->
                <div class="card-body pt-3 px-5">
                   
@if(in_array("view_team", $userPermissions))					   
				    <!--begin::Table-->                    
                     <table class="table align-middle table-row-dashed fs-6 gy-5" id="myTable">
                        <thead>
                           <tr class="fw-bolder text-muted">
                              <th class="w-10px pe-0">
                                 <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input1" value="1">
                                 </div>
                              </th>
                              <th class="min-w-150px">Name</th>
                              <th class="min-w-150px">Role</th>
                              <th class="min-w-150px">Status</th>
                              <th class="min-w-15px">Added Date</th>
                           </tr>
                        </thead>
                     </table>
                    <!--end::Table-->
 @endif               
				
				</div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->
<!--end::Content-->

<!--begin::Add Team Member Modal-->
<div class="modal fade" id="add_team_member" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <div class="modal-content">
      <div class="modal-header" id="add_team_member_header">
        <h2 class="fw-bolder">Add Team Member</h2>
        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-dismiss="modal">
          <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
          </svg>
          </span>
        </div>
      </div>     
      <div class="modal-body scroll-y mx-5">
        <form id="add_team_member_form" name="add_team_member_form" class="form" action="#" method="post">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        @csrf  
        @method('POST')
          <div id="load_add_team_invitation_content"></div>
        </form>
      </div>	 
    </div>
  </div>
</div>
<!--end::Add team member popup-->


<div class="btn btn-primary align-self-center update_role" data-toggle="modal" data-target="#update_role" ></div>
<!--begin::Update role-->
<div class="modal fade show" id="update_role" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header" id="update_role_header" data-dismiss="modal">
                <h2 class="fw-bolder">Update role</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5" id="update_role_body">
                
            </div>
        </div>
    </div>
</div>
<!--end::Update role-->



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
<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>

<!--begin::Add member-->
<script>
    $('body').on('click', '.add_team_member', function()
    {		
        var id    					=	123;
        // var url   					=	"{{ route('admin.account.add_team_member_form',':id') }}";
        var url   					=	"{{ route('admin.team.add_team_member_form',':id') }}";
        url     					=	url.replace(':id', id);
        var token 					=	"{{ csrf_token() }}";								
        var baseurl    				=	"{{ asset('assets/images/Loading2.gif') }}"; 			
        $.ajax(
        {
            type		:	'GET',
            url			:	url,
            beforeSend: function()
            {		
                $('#load_add_team_invitation_content').html('<img src="'+baseurl+'" height="60%" width="100%">');
            },
            success		: function (response)
            {
                if (response.status == "success")
                {
                    $('#load_add_team_invitation_content').html(response.view);	 	
                }
                
            }
        });		
    });
</script>
<!--end::Add member-->

<!--begin::Team Data Table-->
<script>
   $('#inputSearch').keyup(function() {
   		
   		//alert("call"+$('#inputSearch').val() );
   		//e.preventDefault();     
   		//table.fnDestroy();      
   		table._fnDraw();      
   });       
   
    var table = $('#myTable').dataTable({
   
   		responsive      : true,
   		processing      : true,
   		serverSide      : true,
   		"bFilter"       : false,
   		stateSave       : true,
   		"bInfo"         : true, // Showing table records
   		"bLengthChange" : false,
   		oLanguage:
   		{
   		  sLengthMenu: "_MENU_",
   		},
   
   		"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]], 
   		"pageLength": 10,
   						      
             /*responsive: true,
             // processing: true, 
             serverSide: true,*/  
   	
   	
            // ajax: '{!! route('admin.job-categories.data') !!}',
   	
   	ajax: {'url' : '{!! route('admin.team.data') !!}',
   		"data": function ( d )
   			{
   				d.search = $('#inputSearch').val()
   			}
   		},
   	
   	language: {
                 "url": "<?php echo __("app.datatable") ?>"
             },
             "fnDrawCallback": function( oSettings ) {
                 $("body").tooltip({
                     selector: '[data-toggle="tooltip"]'
                 });
             },
   	
   	
         "columnDefs": [
   		{ "width": "3%", "targets": 0 },
   		{ "width": "27%", "targets": 1 },
   		{ "width": "40%", "targets": 2 },
   		{ "width": "22%", "targets": 3 },
   		{ "width": "8%", "targets": 4 }
      ],
             columns: [
   		  	/*{ data: 'DT_Row_Index', name:'id'},  
   		   	{ data: 'name', name: 'name' },
   		   	{ data: 'action', name: 'action', width: '40%' }*/
   			{ data: 'DT_Row_Index', orderable : false, searchable : true},
   			{ data: 'Name', name: 'Name', orderable : false, searchable : true },
   			{ data: 'Role', name: 'Role', orderable : false, searchable : true },
   			{ data: 'Status', name: 'Status', orderable : false, searchable : false },
   			{ data: 'AddedDate', name: 'AddedDate', orderable : false, searchable : false}	
      ]
         });
   
      new $.fn.dataTable.FixedHeader( table );
   
      $('body').on('click', '.sa-params', function(){
             var id = $(this).data('row-id');
             swal({
                 title: "@lang('errors.areYouSure')",
                 text: "@lang('errors.deleteWarning')",
                 type: "warning",
                 showCancelButton: true,
                 confirmButtonColor: "#DD6B55",
                 confirmButtonText: "@lang('app.delete')",
                 cancelButtonText: "@lang('app.cancel')",
                 closeOnConfirm: true,
                 closeOnCancel: true
             }, function(isConfirm){
                 if (isConfirm) {
   
                     var url = "{{ route('admin.job-categories.destroy',':id') }}";
                     url = url.replace(':id', id);
   
                     var token = "{{ csrf_token() }}";
   
                     $.easyAjax({
                         type: 'POST',
                         url: url,
                         data: {'_token': token, '_method': 'DELETE'},
                         success: function (response) {
                             if (response.status == "success") {
                                 $.unblockUI();
   //                                    swal("Deleted!", response.message, "success");
                                 table._fnDraw();
                             }
                         }
                     });
                 }
             });
         }); 
   
   table.on('click', '.show-detail-team', function (){

	
	
			$('#kt_activitiesClient_toggle').trigger('click');
		
			
		
		var id    			= $(this).data('row-id');
		var url   			= "{{ route('admin.team.viewmore',':id') }}";
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

	table.on('click', '.update-user-role', function (){

		$('.update_role').trigger('click');			
		
		var id    			= $(this).data('row-id');
		var url   			= "{{ route('admin.team.editrole',':id') }}";
		url     			= url.replace(':id', id);
		var baseurl    		= "{{ asset('assets/images/Loading2.gif') }}"; 
		$.ajax({
						type  		: 'GET',
						url   		: url,
						beforeSend	: function() {
                                  					$('#update_role_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                    	},
						success : function (response) 
						{
								$('#update_role_body').html(response.view);
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



   function exportExcel(){
   var table 		        		= $('#myTable').dataTable();
   var length 		        		= table.fnGetData().length; 	
   var oSettings         			= table.fnSettings();
   var start             			= oSettings._iDisplayStart;					
   var url 			        	= '{{ route('admin.job-categories.export', [':start' , ':length']) }}';
   url 					        = url.replace(':start', start);
   url 					        = url.replace(':length', length);
   window.location.href  			= url;
   }
        
</script>

<script>
   $(document).ready(function()
   {
   
   var table = $('#table_users111').dataTable({	
    
   		/*responsive      : true,
           processing      : true,
           serverSide      : true,
           "bFilter"       : false,// to disabled search bar
           stateSave       : true,
           "bInfo"         : true,
           "bLengthChange" : false,// to disabled table indexing
           oLanguage:
           {
             sLengthMenu: "_MENU_",
           },
   
           "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]], 
           "pageLength": 10,*/
   		
   			responsive  	: true,
   			processing  	: true,
   			serverSide  	: true,
   			destroy   		: true,
   			ordering		: false, //disable ordering			
   			"bLengthChange" : false, //thought this line could hide the LengthMenu
   			sDom			: 'lrtip',
   			oLanguage 		: {
   									sLengthMenu: "_MENU_",
   								},
   		
   		
   		
   		
   		
   		/*ajax: {'url' : '{!! route('admin.team.data') !!}',
   		"data": function ( d )
   			{
   				d.search = $('#inputSearch').val()
   			}
   		},*/
   		
   		
          ajax: '{!! route('admin.team.data') !!}',
   		
   		
   		
           language: languageOptions(),
           "fnDrawCallback": function( oSettings ) {
               $("body").tooltip({
                   selector: '[data-toggle="tooltip"]'
               });
           },
   
           "columnDefs": [
   						{ "width": "3%", "targets": 0 },
   						{ "width": "27%", "targets": 1 },
   						{ "width": "40%", "targets": 2 },
   						{ "width": "22%", "targets": 3 },
   						{ "width": "8%", "targets": 4 }						
   					  ], 
   					  
           columns: [
   					{ data: 'chkbox', 		name: 'chkbox', 	searchable : false, orderable : false},
   					{ data: 'Name', 		name: 'Name', 		orderable : false, 	searchable : true },
   					{ data: 'Role', 		name: 'Role', 		orderable : false, 	searchable : true },
   					{ data: 'Status', 		name: 'Status', 	orderable : false, 	searchable : false },
   					{ data: 'AddedDate', 	name: 'AddedDate', 	orderable : false, 	searchable : false}
           		]
       });
   new $.fn.dataTable.FixedHeader( table );
   	
   table.on('click', '.show-detail-team', function (){
   	
   			$('#kt_activitiesClient_toggle').trigger('click');
   		
   			
   		
   		var id    			= $(this).data('row-id');
   		var url   			= "{{ route('admin.team.viewmore',':id') }}";
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
   		
   	
   	
   });
   	function handleClick(currentValue, currentId)
    {
        var chkStatus	=	currentValue.checked;
        var userId		=	currentId;   	
        var url 				= "{{ route('admin.team.statusUpdate') }}";
        var token 				= "{{ csrf_token() }}";							
        var baseurl    			= "{{ asset('assets/images/Loading2.gif') }}"; 
        $.ajax(
        {
            type		: 'POST',
            url			: url,
            beforeSend: function()
            {                                            
            },
            data		: {'_token': token, 'chkStatus':chkStatus, 'userId': userId },
            success		: function (response)
            {
                if (response.status == "success")
                {                                                                    
                    $('#'+block).remove();																											
                }
            }
        });
   	}
</script>
<!--end::Team Data Table-->

<script>

    $(document).ready(function() {

        
        $('#inputSearch').keyup(function() {
            //alert("call");
            table._fnDraw(); 
        });
    });

    var table = $('#myTable').dataTable({	
		responsive      : true,
        processing      : true,
        serverSide      : true,
        "bFilter"       : false,// to disabled search bar
        stateSave       : true,
        "bInfo"         : true,
        "bLengthChange" : false,// to disabled table indexing
        oLanguage:
        {
          sLengthMenu: "_MENU_",
        },

        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]], 
        "pageLength": 10,
		
		ajax: {'url' : '{!! route('admin.team.data') !!}',
		"data": function ( d )
			{
				d.search = $('#inputSearch').val()
			}
		},
		
       // ajax: '{!! route('admin.team.data') !!}',
		
        language: languageOptions(),
        "fnDrawCallback": function( oSettings ) {
            $("body").tooltip({
                selector: '[data-toggle="tooltip"]'
            });
        },

        "columnDefs": [
						{ "width": "3%", "targets": 0 },
						{ "width": "27%", "targets": 1 },
						{ "width": "40%", "targets": 2 },
						{ "width": "22%", "targets": 3 },
						{ "width": "8%", "targets": 4 }						
					  ], 
					  
        columns: [
					{ data: 'DT_Row_Index', orderable : false, searchable : true},
					{ data: 'name', name: 'name', orderable : false, searchable : true },
					{ data: 'email', name: 'email', orderable : false, searchable : true },
					 { data: 'role_name', name: 'role_name', orderable : false, searchable : false },
					{ data: 'action', name: 'action', orderable : false, searchable : false}
        		]
    });
    new $.fn.dataTable.FixedHeader( table );

    $('body').on('click', '.sa-params', function(){
        var id = $(this).data('row-id');
        swal({
            title: "@lang('errors.areYouSure')",
            text: "@lang('errors.deleteWarning')",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "@lang('app.delete')",
            cancelButtonText: "@lang('app.cancel')",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {

                var url = "{{ route('admin.team.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
    //                                    swal("Deleted!", response.message, "success");
                            table._fnDraw();
                        }
                    }
                });
            }
        });
    });

    $('body').on('change', '.role_id', function () {
        var id = $(this).val();
        var teamId = $(this).data('row-id');
        var url = '{{route('admin.team.changeRole')}}';

        $.easyAjax({
            url: url,
            type: 'POST',
            data: { roleId: id, teamId: teamId, _token: '{{ csrf_token() }}'},
            success: function (response) {
                if (response.status == "success") {
                    $.unblockUI();
    //                                    swal("Deleted!", response.message, "success");
                    table._fnDraw();
                }
            }
        })
    });
	
	

    function exportExcel()
    {
        var table 		        = $('#myTable').dataTable();
        var length 		        = table.fnGetData().length; 	
        var oSettings         	= table.fnSettings();
        var start             	= oSettings._iDisplayStart;					
        var url 			    = '{{ route('admin.team.export', [':start' , ':length']) }}';
        url 					= url.replace(':start', start);
        url 					= url.replace(':length', length);
        window.location.href    = url;
    }
	
</script>
@endpush