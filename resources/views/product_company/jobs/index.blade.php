@extends('layouts.product_company_layout')

@push('head-script')
<!-- Responsive CSS -->
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>
<!-- Responsive CSS End -->

<style>

@media only screen and (min-width: 900px) and (max-width: 2560px)
{
  .table td, .table th, .table tr { width: max-content!important; }
  .table > :not(caption) > * > *{ padding: 0rem; }
  td:nth-child(n+2),
  th:nth-child(n+2){ width: revert!important; }
}

@media only screen and (min-width: 1024px) and (max-width: 1440px)
{
  #kt_activitiesJob {width: 1025px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px)
{
  #kt_activitiesJob { width: 1200px; }
}

</style>

@endpush

@if(in_array("add_jobs", $userPermissions))
@section('create-button')
<div class="row align-items-center">
  <div class="col-md-7 text-left d-flex">
    <div class="all-positions pl-3 padding-left-browser">
      <h3 class="card-label mb-0" style="font-size: 14px;"> <span class="d-block text-dark font-size-sm mt-custom">
        <div class="d-flex align-items-center"> <a href="Javascript:void(0);" class="text-secondary mr-1">All Position&nbsp; <span class="badge badge-secondary badge-sm">{{ number_format($totalJobs) }}</span> </a> <a href="Javascript:void(0);" class="text-primary mr-1">&nbsp;Active <span class="badge badge-primary badge-sm">{{ number_format($activeJobs) }}</span> </a> <a href="Javascript:void(0);" class="text-warning mr-1">&nbsp;Hold <span class="badge badge-warning badge-sm">{{ number_format($holdJobs) }}</span> </a> <a href="Javascript:void(0);" class="text-danger mr-1">&nbsp;Closed <span class="badge badge-danger badge-sm">{{ number_format($inactiveJobs) }}</span> </a> </div>
        </span> </h3>
    </div>
  </div>
  <div class="col-md-5 text-right searchbar-parent">
    <div class="input-group mr-1 searchbar-child" style="">
      <input type="text" name="inputSearch" id="inputSearch" class="form-control" placeholder="Search..." style="padding: 0.3em .5em;">
    </div>
    <a href="{{ route('product_company.jobs.create') }}" class="d-flex align-items-center btn-grad-req btn-sm mr-1 px-2 py-1"> <span><i class="fa fa-plus-square fa-sm"></i>&nbsp;Add Requisition</span> </a> <a href="{{ route('product_company.job-applications.create') }}" class="d-flex align-items-center btn-grad-app btn-sm mr-1 px-2 py-1"> <span> <i class="fa fa-user-plus"></i>&nbsp;Add Applicant</span> </a> <a onclick="exportExcel()">
    <button class="btn btn-light-primary btn-sm mr-1 px-2 py-1" type="button"> <i class="fa fa-download fa-sm"></i> </button>
    </a> </div>
</div>
</div>
@endsection
@endif

@section('content')
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <!--begin::All Requisition-->
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="javascript:void(0);" class="text-primary-800">All Requisition</a> </h5>
        <span class="text-gray-600" id="">{{$totalJobs}}</span> </div>
      <!--end::All Requisition-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4"></span>
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
        <input type="text" id="inputSearch" name="inputSearch" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search..." />
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
          <!--end::Filter-->
          @if(in_array("add_jobs", $userPermissions))
          <!--begin::Add Requisition-->
          <a  href="<?php echo url('product_company/jobs/create'); ?>" type="button" class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3" id="AddCandidate"> <span class="svg-icon svg-icon-primary svg-icon-1x">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <polygon points="0 0 24 0 24 24 0 24"/>
              <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
              <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
            </g>
          </svg>
          </span>Add Requisition</a> @endif </div>
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
        <div class="card-body pt-3 px-5"> @if(in_array("view_jobs", $userPermissions))
          <!--begin::Table-->
          <table id="myTable" class="table align-middle table-row-dashed fs-6 gy-5">
            <!--begin::Table head-->
            <thead>
              <!--begin::Table row-->
              <tr class="fw-bolder text-muted">
                <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                  </div></th>
                <th class="">Requisition</th>
                <th class="">Exp.</th>
                <th class="">Salary</th>
                <th class="">Location</th>
                <th class="">Status</th>
                <th class="">Date | OPN</th>
                <th class="">HM | CP</th>
                <th class="">S.Partners</th>
                <th class="">TOT</th>
                <th class="" >SOU</th>
                <th class="" >SCR</th>
                <th class="" >EVA</th>
                <th class="" >NEO</th>
                <th class="" >OFF</th>
                <th class="">JOI</th>
                <th class="" >REJ</th>
              </tr>
              <!--end::Table row-->
            </thead>
            <!--end::Table head-->
          </table>
          <!--end::Table-->
          @endif </div>
        <!--end::Card body-->
      </div>
      <!--end::Card-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->
<div class="modal fade bs-modal-sm in" id="scheduleDetailModalStatus" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeadingStatus"></span> </div>
      <div class="modal-body"> Loading... </div>
      <div class="modal-footer">
        <button type="button" class="btn default" data-dismiss="modal">Close</button>
        <button type="button" class="btn blue">Save changes</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal start -->
<div class="modal fade" id="exampleModalSizeLg" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeLg" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document"> ... </div>
</div>
<!-- Modal End -->

<div class="modal fade right" id="sidebar-right" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeLg">
  <div class="modal-dialog modal-lg" role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Right Sidebar</h4>
      </div>
      <div class="modal-body">
        <ul class="nav nav-pills nav-stacked text-center lead">
          <li role="presentation" class="active"> <a href="#home">Home</a> </li>
          <li role="presentation" class="dropdown"> <a href="#" class="dropdown-toggle" id="myTabDrop1" data-toggle="dropdown" aria-controls="myTabDrop1-contents" aria-expanded="false">Services <span class="fa fa-fw fa-chevron-down"></span> </a>
            <ul class="dropdown-menu btn-block">
              <li> <a href="#design" role="tab">Design</a> </li>
              <li> <a href="#develop" role="tab">Develop</a> </li>
              <li> <a href="#support" role="tab">Support</a> </li>
            </ul>
          </li>
          <li role="presentation"> <a href="#about">About</a> </li>
        </ul>
        <hr>
        <div class="form-group hide">
          <div class="input-group">
            <input class="form-control" placeholder="Search" name="customSearch" id="customSearch" class="customSearch">
            <span class="input-group-btn">
            <button class="btn btn-default" type="button"> <i class="fa fa-fw fa-search"></i> </button>
            </span> </div>
        </div>
        <div class="form-group has-feedback">
          <input type="text" class="form-control" id="search-right" name="search-right" class="search-right" placeholder="Search">
          <span class="glyphicon glyphicon-search form-control-feedback" aria-hidden="true"></span> </div>
        <hr>
        <div class="btn-group-vertical center-block"> <a class="btn btn-instagram btn-lg" href="https://www.instagram.com/jorenerene/" target="_blank"> <i class="fa fa-fw fa-instagram"></i> </a> <a class="btn btn-twitter btn-lg" href="https://www.twitter.com/jorenerene/" target="_blank"> <i class="fa fa-fw fa-twitter"></i> </a> <a class="btn btn-github btn-lg" href="https://www.github.com/jorenerene/" target="_blank"> <i class="fa fa-fw fa-github"></i> </a> </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade bs-modal-lg in" id="JobDetailModalStatus" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" id="modal-data-application" style="margin:.5rem auto;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="JobDetailModalStatusData"></span> </div>
    </div>
  </div>
</div>

<div class="modal fade bs-modal-md in" id="scheduleDetailModal_vis" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" id="modal-data-application" style="margin:.5rem auto;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading_vis"></span> </div>
    </div>
  </div>
</div>

<div id="kt_activitiesJob_toggle"></div>
<div id="kt_activitiesJob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesJob_toggle" data-kt-drawer-close="#kt_activitiesJob_close">
  <div class="card shadow-none rounded-0 w-100">
    <!--begin::Header-->
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Requisition</h3>
        <!--begin::seprator-->
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
        <!--end::seprator-->
      </div>
      <!--end::Header-->
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesJob_close">
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
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesJob_body"></div>
  </div>
</div>


<div id="kt_activitiesStaffing_toggle"></div>
<div id="kt_activitiesStaffing" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesStaffing_toggle" data-kt-drawer-close="#kt_activitiesStaffing_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Staffing Partners</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesStaffing_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activities_body"> </div>
  </div>
</div>
<div id="kt_activitiesHM_toggle"></div>
<div id="kt_activitiesHM" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesHM_toggle" data-kt-drawer-close="#kt_activitiesHM_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">HM | CP</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesHM_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesHM_body"> </div>
  </div>
</div>


<div class="modal fade" id="status_stage" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header flex-stack">
        <h2>Requisition Status</h2>
        <div class="btn btn-sm btn-icon btn-active-color-primary close" data-dismiss="modal"  id="status_sub_stage_close">
          <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
          </svg>
          </span>
        </div>
      </div>
      <div class="status_body_content" id="status_body_content"></div>
    </div>
  </div>
</div>

@endsection
@push('footer-script')
<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.responsive.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script>
	
   $(document).ready(function() {	
	   $('#inputSearch').keyup(function() {
			table._fnDraw();
	   });
   });
   
   $(document).ready(function(){	
   
   $('body').on('click', '.right-side-toggle', function () {
   
       $("body").removeClass("control-sidebar-slide-open");
   
   })
   
   $(document).on('click', '.priority', function ()
     {
       var job_id        	= $(this).attr('data-id');
       if(typeof scheduleDate === "undefined") {
       scheduleDate 		= '';
     }
     var url       		= "{{ route('product_company.jobs.priority',':id') }}";
     url         		= url.replace(':id', job_id);
     $('#modelHeading_priority').html('Schedule');
     $.ajaxModal('#scheduleDetailModal_priority', url);
     });
	 
   });  
   
  
   
</script>
<script>

   var table = $('#myTable').dataTable({
   
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
   								
     
   
      				  ajax: {'url' : '{!! route('product_company.jobs.data') !!}',
   					  "data": function ( d )
   					  {
   						d.search = $('#inputSearch').val()
   					  }
   					},
   
   					language:
   					{
   					  "url": "<?php echo __("app.datatable") ?>"
   					},   
   
   				"drawCallback": function( settings ) {
   				
   						$('[data-toggle="tooltip1"]').tooltip();
   						$('[data-toggle="tooltip2"]').tooltip();
   						$('[data-toggle="tooltip3"]').tooltip();
   						$('[data-toggle="tooltip4"]').tooltip();
   						
   				},
   				
   				
   
   				"columnDefs":
   						  [ 
   									{ "width": "50px", "targets": 0, "width": "4%", "class":""},
   									{  "width": "200px", "targets": 1, "class":"d-flex align-items-center" },																				
   									{  "width": "50px"  , "targets": 2},		
   									{  "width": "100px", "targets": 3 },		
   									{  "width": "200px", "targets": 4 }, 
									
   									{  "width": "50px" ,"targets": 5 },
   									{  "width": "200px" , "targets": 6 },              
   									{  "width": "150px" , "targets": 7 },	
   									{  "width": "150px", "targets": 8 },	
   									{ "width": "20px", "targets": 9 },
										
   									{ "width": "20px", "targets": 10 }, 
   									{ "width": "20px", "targets": 11 },    
   									{ "width": "20px", "targets": 12 },             
   									{ "width": "20px", "targets": 13 },
   									{ "width": "20px", "targets": 14 }, 
									  
   									{ "width": "20px", "targets": 15 },
									{ "width": "20px", "targets": 16 }  
   									
   							  ], 
   				columns:
   				[
   				  	{ data: 'chkbox', name: 'chkbox',  orderable: false, searchable: false},  
   					{ data: 'Requisition', name: 'Requisition' , orderable: false, searchable: false},
   					{ data: 'Exp', name: 'Exp' , orderable: false, searchable: false},
   					{ data: 'Salary', name: 'Salary' , orderable: false, searchable: false},
   					{ data: 'Location', name: 'Location' , orderable: false, searchable: false},
   					
   					{ data: 'Status', name: 'Status' , orderable: false, searchable: false},
   					{ data: 'Date', name: 'Date' , orderable: false, searchable: false},
   					{ data: 'HM', name: 'HM' , orderable: false, searchable: false},
   					{ data: 'Partners', name: 'Partners' , orderable: false, searchable: false},
   					{ data: 'TOT', name: 'TOT' , orderable: false, searchable: false},
					
   					{ data: 'SOU', name: 'SOU' , orderable: false, searchable: false},					
   					{ data: 'SCR', name: 'SCR' , orderable: false, searchable: false},
   					{ data: 'EVA', name: 'EVA' , orderable: false, searchable: false},
   					{ data: 'NEO', name: 'NEO' , orderable: false, searchable: false},
   					{ data: 'OFF', name: 'OFF' , orderable: false, searchable: false},
					
   					{ data: 'JOI', name: 'JOI' , orderable: false, searchable: false},
   					{ data: 'REJ', name: 'REJ' , orderable: false, searchable: false}
   					        
   				  	   
   				],
				
				timeout		: 500000,
           		async		: false
   				
   				}); 
   
   				new $.fn.dataTable.FixedHeader( table );
   
   table.on('click', '.show-detail', function ()
   	{
   	
   		$('#kt_activitiesJob_toggle').trigger('click');		
   		var id    		= $(this).data('row-id');
   		var url   		= "{{ route('product_company.jobs.viewmore',':id') }}";
   		url     		= url.replace(':id', id);
   		 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
   		
   		$.ajax({
   						type  : 'GET',
   						url   : url,
   						 beforeSend: function() {
                                     $('#kt_activitiesJob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success : function (response) 
   						{
   								setTimeout(
   								()=>
   								{
   										if (response.status == "success")
   										{
   											$('#kt_activitiesJob_body').html(response.view);
   										}
   								}, 500);
     		  					
   						}
   	  			});
   	}); 
   	
	table.on('click', '.spartner-listing', function ()
   	{
   	
   		$('#kt_activitiesStaffing_toggle').trigger('click');		
   		var id    		= $(this).data('row-id');
   		var url   		= "{{ route('product_company.jobs.spartnerlisting',':id') }}";
   		url     		= url.replace(':id', id);
   		 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  : 'GET',
   						url   : url,
   						 beforeSend: function() {
                                     $('#kt_activities_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success : function (response) 
   						{
   								setTimeout(
   								()=>
   								{
   										if (response.status == "success")
   										{
   											$('#kt_activities_body').html(response.view);
   										}
   								}, 500);
     		  					
   						}
   	  			});
   	}); 
	
	
	
	
	
	table.on('click', '.status_stage', function () {
	
			$('#status_body_content').html('');
			var id        	=	$(this).data('row-id');	
			var url       	=	"{{ route('product_company.jobs.editJobStatus',':id') }}";
			url           	=	url.replace(':id', id);
			$.ajax({
						type  	: 'GET',
						url   	: url,
						success : function (response) {
							if (response.status == "success") 
							{
								$('#status_body_content').html(response.view);      
							}
						}
			});
			
	});
	
	
	
	
	
	table.on('click', '.hm-listing', function ()
   	{
   	
   		$('#kt_activitiesHM_toggle').trigger('click');		
   		var id    		= $(this).data('row-id');
   		var url   		= "{{ route('product_company.jobs.hmlisting',':id') }}";
   		url     		= url.replace(':id', id);
   		 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  		: 'GET',
   						url   		: url,
   						 beforeSend	: function() {
                                     $('#kt_activitiesHM_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success 	: function (response) {
   								setTimeout(
   								()=>{
   										if (response.status == "success"){
   											$('#kt_activitiesHM_body').html(response.view);
   										}
   								}, 500);
     		  					
   						}
   	  			});
   	});
	
	  
	
	
	
	
   $('#filter-company, #filter-status').change(function ()
   {
     	table._fnDraw();
   })
   
 
   
   $('body').on('click', '.sa-params', function()
   {
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
		   },
   
   function(isConfirm)
   {
    if (isConfirm)
     {
       var url = "{{ route('product_company.jobs.destroy',':id') }}";
       url = url.replace(':id', id);
       var token = "{{ csrf_token() }}";
   
       $.easyAjax({
       type: 'POST',
       url: url,
       data: {'_token': token, '_method': 'DELETE'},
       success: function (response)
       {
         if (response.status == "success")
         {
           $.unblockUI();
           //swal("Deleted!", response.message, "success");
           table._fnDraw();
         }
       }
     });
   }
   });
   });
   
   function updateJobStatus(id, statusName)
   {
		var id        	= id;
		var statusName  = statusName;
		var url       	= "{{ route('product_company.jobs.updatestatus',':id') }}";
		url         	= url.replace(':id', id);
		var token       = "{{ csrf_token() }}";
		$.easyAjax({
						type		: 'POST',
						url			: url,
						data		: {'_token': token, '_method': 'POST', 'id':id, 'statusName':statusName},
						success		: function (response){
															table._fnDraw();
						}
					});
   }
   
   function exportExcel()
   {
		var table 		        =	$('#myTable').dataTable();
		var length 		        =	table.fnGetData().length; 	
		var oSettings         	=	table.fnSettings();
		var start            	=	oSettings._iDisplayStart;					
		var url 			    =	'{{ route('product_company.jobs.export', [':start' , ':length']) }}';
		url 					=	url.replace(':start', start);
		url 					=	url.replace(':length', length);
		window.location.href  	=	url;
   }
   
   
   
   
</script>
<script>


function editJobStatus(job_id)
{   
   	
     var id        		=	job_id;
     if(typeof scheduleDate === "undefined")
     {
        scheduleDate  	=	'';
     }
     var url       		=	"{{ route('product_company.jobs.editJobStatus',':id') }}";
     url         		=	url.replace(':id', id);
     $('#modelHeadingStatus').html('Schedule');
     $.ajaxModal('#scheduleDetailModalStatus', url);
   
}
    				
</script>
@endpush
