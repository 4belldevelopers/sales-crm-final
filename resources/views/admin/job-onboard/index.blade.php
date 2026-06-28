@extends('layouts.demo_admin_app')
<style>
.border-radius-5
{
	border-radius:5px!important;
}
/*begin::Media query*/
@media only screen and (min-width: 900px) and (max-width: 2560px)
{
  .table td, .table th, .table tr
  {
    width: max-content!important;
  }
  .table > :not(caption) > * > *
  {
    padding: 0rem;
  }
  td:nth-child(n+2),
  th:nth-child(n+2)
  {
    width: revert!important;
  }
}
/*end::Media query*/
</style>
<!-- Responsive CSS -->
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<!-- Responsive CSS End -->
@section('content')

<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <!--begin::Staffing Partners-->
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
          <a href="javascript:void(0);" class="text-gray-800 text-hover-primary">Joining Tracker</a>
        </h5>
        <span class="text-gray-600">115</span> </div>
      <!--end::Staffing Partners-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
      <!--end::Separator-->
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
        <input type="text" data-kt-user-table-filter="search" name="inputSearch" id="inputSearch" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search..." />
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
          Filter </button>
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
        </div>
        <!--end::Toolbar-->
        <!--begin::Group actions-->
        <div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
          <div class="fw-bolder me-5"> <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected </div>
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
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">
      <!--begin::Card-->
      <div class="card">
        <!--begin::Card body-->
        <div class="card-body pt-3 px-5">
          
@if(in_array("view_joining", $userPermissions))			  
		  <!--begin::Table-->
          <table id="myTable" class="table align-middle table-row-dashed fs-6 gy-5" >
          <thead>
            <tr class="fw-bolder text-muted">
              <th class="w-10px pe-0">
                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                  <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                </div>
              </th>
              <th class="">Name</th>
              <th class="">Contact</th>
              <th class="">Requisitions</th>
              <th class="">Source</th>
              <th class="">Offer Amount</th>
              <th class="">Joining Date</th>
              <th class="">Stage</th>
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
</div>
<!--end::Content-->


<!--begin::Modal - Stage and sub stage-->
<div class="modal fade" id="stage_sub_stage" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header flex-stack"> 
        <h2>Stage and sub stage</h2>
        <div class="btn btn-sm btn-icon btn-active-color-primary close" data-dismiss="modal"  id="stage_sub_stage_close">  
          <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
          </svg>
          </span>
        </div>
      </div>
	 <div class="stage_body_content" id="stage_body_content"></div> 	  
    </div>
  </div>
</div>
<!--end::Modal - Stage and sub stage-->


@section('create-button')
<div class="row align-items-center">
  <div class="col-md-6">
    <div class="dropdown dropdown-inline">
      <button class="dropbtn bg-light dropdown-toggle dropbtn-border">10</button>
      <div class="dropdown-content">
        <ul class="navi">
          <li class="navi-item"> <a href="Javascript:void(0);" class="navi-link custom-width"> <span class="navi-text">20</span> </a> </li>
          <li class="navi-item"> <a href="Javascript:void(0);" class="navi-link custom-width"> <span class="navi-text">30</span> </a> </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="col-md-6 text-right">
    <div class="input-group mr-1" style="width: fit-content;display: inline-flex;">
      <input type="text" name="inputSearch" id="inputSearch" class="form-control" placeholder="Search..." style="padding: 0.3em .5em;">
    </div>
    <a onclick="exportExcel()">
    <button class="btn btn-light-primary btn-sm px-2 py-1 mr-1" type="button"> <i class="fa fa-download fa-sm"></i> </button>
    </a> <a href="javascript:void(0);" id="column_visibility_control_btn" class="btn btn-light-dark btn-sm px-2 py-1"> <i class="fa fa-filter fa-sm"></i> </a> </div>
</div>
@endsection



@endsection
@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
<script src="//cdn.datatables.net/colreorder/1.5.2/js/dataTables.colReorder.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/buttons.colVis.min.js"></script>
<script type="text/javascript">
   $( document ).ready(function() {
      
       $('body').on('click', '.ti-close', function () {
   			$("body").removeClass("control-sidebar-slide-open");
   		})	
   });	
</script>
<script>

	$(document).ready(function() {
		$('#inputSearch').keyup(function() {
			tableLoad('filter');     
		}); 
	});

   var table;
   tableLoad('load');
   
   function tableLoad()
   {
       table = $('#myTable').dataTable(
       {
	   
							
							responsive  	:	true,
							processing  	:	true,
							serverSide  	:	true,
							destroy   		:	true,
							ordering		:	false, //disable ordering
							"bLengthChange" :	false, //thought this line could hide the LengthMenu
							sDom			:	'lrtip',
							oLanguage 		:	{sLengthMenu: "_MENU_",},
				   			
						   	//ajax			:	'{!! route('admin.job-onboard.data') !!}',
							
							ajax			: {'url' : '{!! route('admin.job-onboard.data') !!}',
												"data": function ( d )
													{
														d.search = $('#inputSearch').val()
													}
												},
												
												
						   language			:	{"url": "<?php echo __("app.datatable") ?>"},
						   "fnDrawCallback": function (oSettings)
						   {
							   $("body").tooltip(	{selector: '[data-toggle="tooltip"]'});
						   },        
						   columns:
						   [
									{data: 'chkbox', name:'chkbox', width: '5%', orderable: false, searchable: false},
									{data: 'Name', name:'Name', width: '20%', orderable: false, searchable: false},
									{data: 'Contact', name:'Contact', width: '15%', orderable: false, searchable: false},
									{data: 'Requisitions', name:'Requisitions', width: '15%', orderable: false, searchable: false},
									{data: 'Source', name:'Source', width: '10%', orderable: false, searchable: false},
									{data: 'Offer', name:'Offer', width: '10%', orderable: false, searchable: false},
									{data: 'Joining', name:'Joining', width: '10%', orderable: false, searchable: false},
									{data: 'Stage', name:'Stage', width: '10%', orderable: false, searchable: false}
				   
						   ]
					   
	   
	   });
       new $.fn.dataTable.FixedHeader(table);
   }
  
  
  
  
  
  
  
  
  
   
   $('#column_visibility_control_btn').click(function()
   {
   var id 				= 12;
   
   if(typeof scheduleDate === "undefined")
       {
   scheduleDate 	= '';
   }
   var url 			= "{{ route('admin.job-onboard.load_visibility_control',':id') }}";
   url 				= url.replace(':id', id);
   $('#modelHeading_vis').html('Schedule');
   $.ajaxModal('#scheduleDetailModal_vis', url);
   });		
   
   $('body').on('click', '.send-offer', function()
   {
       var id = $(this).data('row-id');
       swal(
       {
           title: "@lang('errors.areYouSure')",
           text: "@lang('errors.deleteWarning')",
           type: "warning",
           showCancelButton: true,
           confirmButtonColor: "#DD6B55",
           confirmButtonText: "@lang('app.send') @lang('app.offer')",
           cancelButtonText: "@lang('app.cancel')",
           closeOnConfirm: true,
           closeOnCancel: true
       },
       function(isConfirm)
       {
           if (isConfirm)
           {
               var url = "{{ route('admin.job-onboard.send-offer',':id') }}";
               url = url.replace(':id', id);
               var token = "{{ csrf_token() }}";
               $.easyAjax({
                   type: 'GET',
                   url: url,
                   success: function (response)
                   {
                       if (response.status == "success")
                       {
                           $.unblockUI();
                           table._fnDraw();
                       }
                   }
               });
           }
       });
   });
   
   // Change status in cancel
   $('body').on('click', '.sa-params', function(){
       var id = $(this).data('row-id');
       swal(
       {
           title: "@lang('messages.offer.areYouSureCanceled')",
           type: "warning",
           showCancelButton: true,
           confirmButtonColor: "#DD6B55",
           confirmButtonText: "@lang('messages.offer.yesDoIt') ",
           cancelButtonText: "@lang('app.cancel')",
           closeOnConfirm: true,
           closeOnCancel: true
       },
       function(isConfirm)
       {
           if (isConfirm)
           {
               var url = "{{ route('admin.job-onboard.update-status',':id') }}";
               url = url.replace(':id', id);
               var token = "{{ csrf_token() }}";
   
               $.easyAjax(
               {
                   type: 'GET',
                   url: url,
                   success: function (response)
                   {
                       if (response.status == "success")
                       {
                           $.unblockUI();
                           table._fnDraw();
                       }
                   }
               });
           }
       });
   });
   
   $('body').on('click', '.right-side-toggle', function ()
   {
       $("body").removeClass("control-sidebar-slide-open");
   })
   
   table.on('click', '.show-detail', function ()
    {
       $(".right-sidebar").slideDown(50).addClass("shw-rside");
       var id = $(this).data('row-id');
       var url = "{{ route('admin.job-applications.show',':id') }}";
       url = url.replace(':id', id);
       $.easyAjax(
       {
           type: 'GET',
           url: url,
           success: function (response)
           {
               if (response.status == "success")
               {
                   $('#right-sidebar-content').html(response.view);
               }
           }
       });
   });		
   function exportExcel()
   {
   var table 			= $('#myTable').dataTable();
   var length 			= table.fnGetData().length;
   var oSettings 		= table.fnSettings();
   var start     		= oSettings._iDisplayStart;
   //alert(start);
   //alert(oSettings._iDisplayStart); return false; $('#myTable').dataTable().fnGetData()
   var url 				= '{{ route('admin.job-onboard.export', [':start' , ':length']) }}';	
   url 					= url.replace(':start', start);
   url 					= url.replace(':length', length);
   //alert(url); 
   //return false;
       window.location.href = url;
   }


		function updateApplicantStage(id)
		{
				//var id       	= $(this).data('row-id');			   
				var url    		= "{{ route('admin.job-applications.stage_body_content',':id') }}";
				url      		= url.replace(':id', id);
				$.ajax({
								type  			:	'GET',
								url   			:	url,
								success 		:	function (response) {
																	if (response.status == "success") 
																	{
																		$('#stage_body_content').html(response.view);
																	}
															  }
						}); 
		
		}	

</script>
@endpush