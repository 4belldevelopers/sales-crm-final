@extends('layouts.demo_admin_app')
<style>
.border-radius-5
{
  border-radius: 5px!important;
}
.ml-2
{
   margin-left: 2em;
}

/*Media query*/
@media only screen and (min-width: 900px) and (max-width: 2560px)
{
  .table td, .table th, .table tr
  {
    width: max-content!important;
  }
  .table > :not(caption) > * > *
  {
    padding: 0.75em 0rem!important;
  }
  td:nth-child(n+2),
  th:nth-child(n+2)
  {
    width: revert!important;
  }
}

@media only screen and (min-width: 1024px) and (max-width: 1440px)
{
  #kt_activitiesClient
  {
    width: 1025px;
  }
}

@media only screen and (min-width: 1440px) and (max-width: 2560px)
{
  #kt_activitiesClient
  {
    width: 1200px;
  }
}
/*Media query end*/
</style>
@section('content')
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <!--begin::All Client-->
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
          <a href="javascript:void(0);" class="text-primary">All Client</a>
        </h5>
        <span class="text-gray-600">120</span>
      </div>
      <!--end::All Client-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <!--end::Separator-->
      <!--begin::Jobs-->
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
          <a href="javascript:void(0);" class="text-gray-800 text-hover-primary">Client requests</a>
        </h5>
        <span class="text-gray-600">40</span> </div>
      <!--end::Jobs-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <!--end::Separator-->
      <!--begin::Request-->
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
          <a href="{{ asset('admin/clients/client_request/0') }}" class="text-gray-800 text-hover-primary">Job requests</a>
        </h5>
        <span class="text-gray-600">40</span>
      </div>
      <!--end::Request-->
    </div>
    <!--end::Page title-->
    <!--begin::Actions-->
    <div class="d-flex align-items-center py-1">
      <!--begin::Search-->
      <div class="d-flex align-items-center position-relative my-1 me-3">
        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
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
      <div class="card-toolbar">
        <!--begin::Toolbar-->
        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
          <!--begin::Filter-->
          <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
          <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
          <span class="svg-icon svg-icon-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black" />
          </svg>
          </span>
          <!--end::Svg Icon-->
          Filter</button>
          <!--begin::Menu 1-->
          <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
            <!--begin::Header-->
            <div class="px-7 py-5">
              <div class="fs-5 text-dark fw-bolder">Filter Options</div>
            </div>
            <!--end::Header-->
            <!--begin::Separator-->
            <div class="separator border-gray-200"></div>
            <!--end::Separator-->
            <!--begin::Content-->
            <div class="px-7 py-5" data-kt-user-table-filter="form">
              <!--begin::Input group-->
              <div class="mb-10">
                <label class="form-label fs-6 fw-bold">Role:</label>
                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" data-kt-user-table-filter="role" data-hide-search="true">
                  <option></option>
                  <option value="Administrator">Administrator</option>
                  <option value="Analyst">Analyst</option>
                  <option value="Developer">Developer</option>
                  <option value="Support">Support</option>
                  <option value="Trial">Trial</option>
                </select>
              </div>
              <!--end::Input group-->
              <!--begin::Input group-->
              <div class="mb-10">
                <label class="form-label fs-6 fw-bold">Two Step Verification:</label>
                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" data-kt-user-table-filter="two-step" data-hide-search="true">
                  <option></option>
                  <option value="Enabled">Enabled</option>
                </select>
              </div>
              <!--end::Input group-->
              <!--begin::Actions-->
              <div class="d-flex justify-content-end">
                <button type="reset" class="btn btn-light btn-active-light-primary fw-bold me-2 px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="reset">Reset</button>
                <button type="submit" class="btn btn-primary fw-bold px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="filter">Apply</button>
              </div>
              <!--end::Actions-->
            </div>
            <!--end::Content-->
          </div>
          <!--end::Menu 1-->
          <!--end::Filter-->
          
@if(in_array("add_clients", $userPermissions))
            <!--begin::Add Candidate-->
<a href="<?php echo url('admin/clients/create'); ?>">
	<button type="button" class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3">
	<!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
	<span class="svg-icon svg-icon-primary svg-icon-1x">
	<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
	<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
	<polygon points="0 0 24 0 24 24 0 24"/>
	<path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
	<path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
	</g>
	</svg>
	</span> Add Client</button>
</a> 
@endif		  

		  
		  
		  
		  </div>
        <!--end::Toolbar-->
        <!--begin::Group actions-->
        <div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
          <div class="fw-bolder me-5"> <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected</div>
          <button type="button" class="btn btn-danger" data-kt-user-table-select="delete_selected">Delete Selected</button>
        </div>
      </div>
    </div>
    <!--end::Actions-->
  </div>
  <!--end::Container-->
</div>
<!--end::Toolbar-->
<!--begin::Content-->

<?php 
		//abort_if(! $user->cans('view_clients'), 403);
		//echo '<pre>'; print_r(user->cans('view_clients')); exit; 
	
?>

<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container-fluid px-3">
          <!--begin::Card-->
          <div class="card">
            <!--begin::Card body-->
            <div class="card-body pt-3 px-5">
@if(in_array("view_clients", $userPermissions))                
				<!--begin::Table-->
                <table id="myTable" class="table align-middle table-row-dashed fs-6 gy-5" >
                  <!--begin::Table head-->
                  <thead>
                    <!--begin::Table row-->
                    <tr class="fw-bolder text-muted">
                      <th class="w-10px pe-0">
                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                          <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                        </div>
                      </th>
                      <th class="">Name</th>
                      <th class="">Location</th>
                      <th class="">Joining</th>
                      <th class="">Requisitions</th>
                      <th class="">Connected On</th>
                      <th class="">Client</th>
                      <th class="">Contact Person</th>
                    </tr>
                    <!--end::Table row-->
                  </thead>
                </table>
@endif					
            </div>
            <!--end::Card body-->
          </div>
          <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->
</div>
<!--end::Content-->

<div id="kt_activitiesClient_toggle"></div>
<div id="kt_activitiesClient" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesClient_toggle" data-kt-drawer-close="#kt_activitiesClient_close">
  <div class="card shadow-none rounded-0 w-100">
    <!--begin::Header-->
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Client</h3>
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
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
          </g>
        </svg>
        </span>
        <!--end::Svg Icon-->
        </button>
      </div>
    </div>
    <!--end::Header-->
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesClient_body"> &nbsp; </div>
    <!--begin::Body-->
  </div>
</div>
@endsection

@push('footer-script')
<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
<script src="//cdn.datatables.net/colreorder/1.5.2/js/dataTables.colReorder.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/buttons.colVis.min.js"></script>
<script>

$(document).ready(function() {	
	$('#inputSearch').keyup(function() {
		//alert("call");
		table._fnDraw(); 
	});
});

    var table = $('#myTable').dataTable({
       
	   		responsive  	: true,
			processing  	: true,
			serverSide  	: true,
			destroy   		: true,
			"bLengthChange" : true,
			"lengthMenu"    : [[10, 25, 50, 100], [10, 25, 50, 100]],
			sDom			: 'lrtip',
			oLanguage 		: {
									sLengthMenu: "_MENU_",
								},
								
	   /* responsive      : true,
        processing      : true,
        serverSide      : true,
        "bFilter"       : false,
        stateSave       : true,
        "bInfo"         : true,
        "bLengthChange" : true,
		ordering		: false, //disable ordering
		
        oLanguage:
        {
          sLengthMenu: "_MENU_",
        },
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]], 
        "pageLength": 25,*/
	
		ajax: {'url' : '{!! route('admin.clients.data') !!}',
		"data": function ( d )
			{
				d.search = $('#inputSearch').val()
			}
		},
			
        language: {
            "url": "<?php echo __("app.datatable") ?>"
        },
       
	   "drawCallback": function( settings ) {
				
						$('[data-toggle="tooltip1"]').tooltip();
						$('[data-toggle="tooltip2"]').tooltip();
						//$('[data-toggle="tooltip3"]').tooltip();
						//$('[data-toggle="tooltip4"]').tooltip();						
				},
				
		
		"columnDefs":
						  [ 
									{ "width": "50px", "targets": 0, "width": "4%", "class":""},
									{  "width": "200px", "targets": 1, "class":"d-flex align-items-center" }																			
									
						 ], 
							  
        columns: [
					{ data: 'chkbox', name: 'chkbox', searchable : false, orderable : false},
					{ data: 'Name', name: 'Name', searchable : false, orderable : false},
					{ data: 'Location', name: 'Location', searchable : false, orderable : false},
					{ data: 'Joining', name: 'Joining', searchable : false, orderable : false},
					{ data: 'Requisitions', name: 'Requisitions', Requisitions : false, orderable : false},
					
					{ data: 'Connected', name: 'Connected', Requisitions : false, orderable : false},
					{ data: 'Client', name: 'Client', Requisitions : false, orderable : false},
					{ data: 'Contact', name: 'Contact', Requisitions : false, orderable : false}        
					/*{ data: 'name', name: 'name'},
					{ data: 'contact_person_name', name: 'contact_person_name' , searchable : false, orderable : false},
					{ data: 'location', name: 'location', searchable : false, orderable : false },
					
					{ data: 'total_jobs', name: 'total_jobs', searchable : false, orderable : false },
					{ data: 'open_jobs', name: 'open_jobs', searchable : false, orderable : false },
					{ data: 'closed_jobs', name: 'closed_jobs', searchable : false, orderable : false },
					{ data: 'hold_jobs', name: 'hold_jobs', searchable : false, orderable : false },
					
					//{ data: 'email', name: 'email' },
					{ data: 'commission', name: 'commission' },
					{ data: 'payout', name: 'payout' },
			
            		{ data: 'action', name: 'action', searchable : false, orderable : false }*/
        ],
		
			timeout: 500000,
           async: false
    });
    new $.fn.dataTable.FixedHeader( table );




	table.on('click', '.show-detail', function ()
	{
	
		$('#kt_activitiesClient_toggle').trigger('click');
		
		var id    		= $(this).data('row-id');
		var url   		= "{{ route('admin.clients.viewmore',':id') }}";
		url     		= url.replace(':id', id);
		
		 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
		//alert(url); 
		//$.easyAjax({
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


	


    $('body').on('click', '.sa-params', function(){
        var id = $(this).data('row-id');
        swal({
            title				: "@lang('errors.areYouSure')",
            text				: "@lang('errors.deleteWarning')",
            type				: "warning",
            showCancelButton	: true,
            confirmButtonColor	: "#DD6B55",
            confirmButtonText	: "@lang('app.delete')",
            cancelButtonText	: "@lang('app.cancel')",
            closeOnConfirm		: true,
            closeOnCancel		: true
        }, function(isConfirm){
            if (isConfirm) {

                var url = "{{ route('admin.clients.destroy',':id') }}";
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
	$('body').on('click', '.client-user-delete', function(){
        var id = $(this).data('row-id');
		
        swal({
            title				: "@lang('errors.areYouSure')",
            text				: "@lang('errors.deleteWarning')",
            type				: "warning",
            showCancelButton	: true,
            confirmButtonColor	: "#DD6B55",
            confirmButtonText	: "@lang('app.delete')",
            cancelButtonText	: "@lang('app.cancel')",
            closeOnConfirm		: true,
            closeOnCancel		: true
        }, function(isConfirm){
            if (isConfirm) {

                var url = "{{ route('admin.clients.deleteUser') }}";
              //  url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, 'id':id},
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
	$('body').on('click', '.client-location-delete', function(){
        var id = $(this).data('row-id');
		
        swal({
            title				: "@lang('errors.areYouSure')",
            text				: "@lang('errors.deleteWarning')",
            type				: "warning",
            showCancelButton	: true,
            confirmButtonColor	: "#DD6B55",
            confirmButtonText	: "@lang('app.delete')",
            cancelButtonText	: "@lang('app.cancel')",
            closeOnConfirm		: true,
            closeOnCancel		: true
        }, function(isConfirm){
            if (isConfirm) {

                var url = "{{ route('admin.clients.deleteAddress') }}";
              //  url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, 'id':id},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
                             table._fnDraw();
                        }
                    }
                });
            }
        });
    });
	

	 function exportJobApplication()
			{			

				var table 				= $('#myTable').dataTable();
				var length 				= table.fnGetData().length;
						
				var oSettings 			= table.fnSettings();
				var start     			= oSettings._iDisplayStart;
							
				var url 				= '{{ route('admin.clients.export', [':start' , ':length']) }}';
					
				url 					= url.replace(':start', start);
				url 					= url.replace(':length', length);
			
				window.location.href = url;
						
        }
		
</script>
@endpush 