@extends('layouts.superadmin_layout')

@push('head-script')
<!-- Responsive CSS -->
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>
<!-- Responsive CSS End -->
<style>
  /* .customedivclass{ 
    padding: 1rem !important;
   } */
@media only screen and (min-width: 900px) and (max-width: 2560px){
  .table td, .table th, .table tr
  {width: max-content!important;}
  .table > :not(caption) > * > *
  { padding: 0rem; }
  td:nth-child(n+2),
  th:nth-child(n+2)
  { width: revert!important;}
}
@media only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesJob {width: 1025px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesJob {width: 1200px;}
}
@media only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesClientjob_toggle {width: 1025px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesClientjob_toggle {width: 1200px;}
}

.table-custom-class tbody tr {
    height: 82px; /* Adjust the height as per your requirement */
    position: relative;
}

</style>
@endpush

@section('content')

@php	
     use App\Helper\GeneralMethod;
     
      $job_status = GeneralMethod::job_status();
      $salaryMode = GeneralMethod::salaryMode();
      $currencySymbole = GeneralMethod::currencySymbole();
      $productAllowCompany = GeneralMethod::companiesType();

      $userPermissions = array();
     
@endphp     
     




<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      
      
      
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      
      
      <!--begin::All Requisition-->
      <div class="d-flex flex-column">
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="javascript:void(0);" class="text-primary-800">All Jobs</a> </h5>
        <span class="text-gray-600" id="allRequisition"> <?php echo @$dataCnt['allRequisition']; ?> </span> </div>
      <!--end::All Requisition-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <!--end::Separator-->
    
    </div>
    
    
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
        
        <div class="d-flex align-items-center flex-wrap">
       <div class="col-lg-4 d-flex align-items-center px-2" style="width:55%">
          <input type="text" class="form-control form-control-solid ps-3 inputJobSearch" name="inputJobSearch" value="" placeholder="Search..." id="kt_tagify_2" />
       </div>
       <div class="col-lg-2 d-flex align-items-center px-2" style="width: 20%;">
      <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="status" data-control="select2" data-dropdown-css-class="w-150px" data-placeholder="Select an option" data-kt-table-widget-3="filter_status" style="border: 1px solid #ffffff;" onchange="filtersdata()">
         <option value="all">All</option>
         <option value="active" selected="selected">Active</option>   
         <option value="hold">Hold</option>     
         <option value="inactive">Closed</option>  
      </select>
   </div>
   <div class="col-lg-2 d-flex align-items-center px-2" style="width: 25%;">
      <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="hiringtype" data-control="select2" data-dropdown-css-class="w-150px" data-placeholder="Select an option" style="border: 1px solid #ffffff;" onchange="filtersdata()">
         <option value="All">Sort</option>
         <option value="1" selected="selected">Newest to Oldest (Date)</option>
         <option value="2">Oldest to Newest (Date)</option>
         <option value="3">Low to High (Salary)</option>
         <option value="4">High to Low (Salary)</option>
         <option value="5">A to Z (Company Wise)</option>
         <option value="6">Z to A (Company Wise)</option>
         <option value="7">Full-Time Jobs Only</option>
         <option value="8">Contract Jobs Only</option>
      </select>
   </div>
   {{-- <div class="col-lg-2 d-flex align-items-center px-2" style="width: 10%;">
      <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder w-100 py-4" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
         <span class="svg-icon svg-icon-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
               <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
            </svg>
         </span>
         Filter
      </button>
   </div> --}}
   
</div>
        
          <div class="loadingMessage loadingMessagejobListAjax" style="display: none; position: absolute;">Processing...</div>
          <table id="myTable" class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class">
            <thead>
              <tr class="fw-bolder text-muted">
                <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                  </div></th>
                <th class="customedivclass">Jobs</th>
                <th class="customedivclass">Exp.</th>
                <th class="customedivclass">Salary</th>
                <th class="">Location</th>
                <th class="">Status</th>
                <th class="">Date</th>
                <th class=""><i class="fa fa-users" aria-hidden="true" style="font-size:17px"></i></th>
                <th class=""><i class="fa fa-handshake" aria-hidden="true" style="font-size:17px"></i></th>
                <th class="">TOT</th>
                <th class="" >SOU</th>
                <th class="" >SCR</th>
                <th class="" >EVA</th>
                <th class="" >NEO</th>
                <th class="" >OFF</th>
                <th class="">JOI</th>
                <th class="" >REJ</th>
              </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold" id="jobListAjax">
            </tbody>
          </table>
          <div id="paginationJobs"></div>
         
          
        
       
        <!--New Design-->
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
<div id="kt_activitiesStaffing_toggle"></div>
<div id="kt_activitiesStaffing" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesStaffing_toggle" data-kt-drawer-close="#kt_activitiesStaffing_close">
  <div class="card shadow-none rounded-0 w-100">
    <!--begin::Header-->
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Partners</h3>
        <!--begin::seprator-->
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
        <!--end::seprator-->
      </div>
      <!--end::Header-->
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesStaffing_close">
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
    <!--end::Header-->
    <!--begin::Body-->
    <div class="card-body position-relative px-0 py-0" id="kt_activities_body"> </div>
    <!--end::Body-->
  </div>
</div>
<div id="kt_activitiesJob_toggle"></div>
<div id="kt_activitiesJob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesJob_toggle" data-kt-drawer-close="#kt_activitiesJob_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Job Details</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesJob_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesJob_body"></div>
  </div>
</div>
<div id="kt_activitiesClientjob_toggle"></div>
<div id="kt_activitiesClientjob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesClientjob_toggle" data-kt-drawer-close="#kt_activitiesClientjob_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Direct Client Requisition</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesClientjob_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesclientjob_body"> </div>
  </div>
</div>
<div id="kt_activitiesRecruiter_toggle"></div>
<div id="kt_activitiesRecruiter" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesRecruiter_toggle" data-kt-drawer-close="#kt_activitiesRecruiter_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <div class=" d-flex align-items-center">
          <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);" class="l">
            <div class="symbol-label border-radius-5" id="light_recruiter_caption">&nbsp;</div>
            </a> </div>
          <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" id="light_recruiter_name">&nbsp;</a> </div>
        </div>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesRecruiter_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesRecruiter_body"> &nbsp; </div>
  </div>
</div>
<div id="kt_activitiesTEAM_toggle"></div>
<div id="kt_activitiesTEAM" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesTEAM_toggle" data-kt-drawer-close="#kt_activitiesTEAM_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Team Members</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesTEAM_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesTEAM_body"> </div>
  </div>
</div>
<div id="kt_activitiesHM_toggle"></div>
<div id="kt_activitiesHM" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesHM_toggle" data-kt-drawer-close="#kt_activitiesHM_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Team Members</h3>
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
<div id="kt_activitiesCHM_toggle"></div>
<div id="kt_activitiesCHM" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesCHM_toggle" data-kt-drawer-close="#kt_activitiesCHM_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark" id="company_or_client">Direct Client Contact Person</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesCHM_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesCHM_body"> </div>
  </div>
</div>
<!--end::Staffing Partners sidebar-->
<div class="modal fade" id="status_stage" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header flex-stack">
        <h2>Job Status</h2>
        <div class="btn btn-sm btn-icon btn-active-color-primary close" data-dismiss="modal"  id="status_sub_stage_close"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
          </svg>
          </span> </div>
      </div>
      <div class="status_body_content" id="status_body_content"></div>
    </div>
  </div>
</div>
@endsection
@push('footer-script')

<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>

<!--<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.responsive.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script>-->


<link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>


<script>

@if(session('packageStatus'))
 Swal.fire("Failed", "{{ session('packageStatus') }}" , "warning");	
@endif


      $(document).ready(function() {	


        $('body').on('click', '#AddjobSection', function(){
          
          var url 				=	"{{ route('service_company.auth-custom-package.validatePackage') }}";
          var token 				=	"{{ csrf_token() }}";							
          var baseurl    			=	"{{ asset('assets/images/Loading2.gif') }}"; 
          var eventId 			=	2; 
          $.ajax({
                type		: 'POST',
                url			: url,
                headers     : { 'X-CSRF-TOKEN': token },
                beforeSend	: function(){ },
                data		: {'_token': token, 'event':'CVACCESS','eventId':eventId},
                success		: function (response){
                                      if (response.status == "success"){  																					  																												                                                                                            	var url   			=	"{{url('service_company/jobs/create')}}";
                                            window.location 	=	url;
                                        }else if (response.status == "info"){   
                                          sweetAlert("Fail!", response.message, "error");	
                                        }										 
                                }
              });
      
        });

        $('#inputSearch').keyup(function() {
          table._fnDraw();
        });

      });

</script>



        <!--<link href="https://preview.keenthemes.com/html/keen/docs/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />-->
        <!--<script src="https://preview.keenthemes.com/html/keen/docs/assets/plugins/global/plugins.bundle.js"></script>-->
        <script>
            var input1 = document.querySelector("#kt_tagify_1");
            // var input2 = document.querySelector("#kt_tagify_2");
            
            new Tagify(input1);
            // new Tagify(input2);
        </script>

<script>
    // var table = $('#myTable').dataTable({
   
   	// 		responsive  	: true,
   	// 		processing  	: true,
   	// 		serverSide  	: true,
   	// 		destroy   		: true,
   	// 		ordering		: false, //disable ordering	
   	// 		"bLengthChange" : false, //thought this line could hide the LengthMenu   			
   	// 		sDom			: 'lrtip',   			
   	// 		oLanguage 		: { sLengthMenu: "_MENU_", },
		// 	ajax			: {'url' : '{!! route('superadmin.company.all_jobs_data') !!}', "data": function ( d ){d.search = $('#inputSearch').val(),d.custom_search = $('#kt_tagify_2').val(),d.custom_status = $('#status').val(),d.custom_hiringtype = $('#hiringtype').val()}},
   	// 	  "drawCallback"	: function( settings ) {
		// 							$('[data-toggle="tooltip1"]').tooltip();
		// 							$('[data-toggle="tooltip2"]').tooltip();
		// 							$('[data-toggle="tooltip3"]').tooltip();
		// 							$('[data-toggle="tooltip4"]').tooltip();		
		// 					},
   	// 	  "columnDefs": [
    //                 { "width": "50px", "targets": 0, "width": "4%", "class": "" },
    //                 { "width": "200px", "targets": 1, "class": "d-flex align-items-center" },
    //                 { "width": "50px", "targets": 2 },
    //                 { "width": "100px", "targets": 3 },
    //                 { "width": "200px", "targets": 4 },
    //                 { "width": "50px", "targets": 5 },
    //                 { "width": "200px", "targets": 6 },
    //                 { "width": "150px", "targets": 7 },
    //                 { "width": "150px", "targets": 8 },
    //                 { "width": "20px", "targets": 9 },
    //                 { "width": "20px", "targets": 10 },
    //                 { "width": "20px", "targets": 11 },
    //                 { "width": "20px", "targets": 12 },
    //                 { "width": "20px", "targets": 13 },
    //                 { "width": "20px", "targets": 14 },
    //                 { "width": "20px", "targets": 15 },
    //                 { "width": "20px", "targets": 16 },
                    
                   
    //             ],
   	// 			columns:
   	// 			[
   	// 			  	{ data: 'chkbox', name: 'chkbox',  orderable: false, searchable: false},  
   	// 				{ data: 'Requisition', name: 'Requisition' , orderable: false, searchable: false},
   	// 				{ data: 'Exp', name: 'Exp' , orderable: false, searchable: false},
   	// 				{ data: 'Salary', name: 'Salary' , orderable: false, searchable: false},
   	// 				{ data: 'Location', name: 'Location' , orderable: false, searchable: false},
   					
   	// 				{ data: 'Status', name: 'Status' , orderable: false, searchable: false},
   	// 				{ data: 'Date', name: 'Date' , orderable: false, searchable: false},
   	// 				{ data: 'HM', name: 'HM' , orderable: false, searchable: false},
   	// 				{ data: 'Partners', name: 'Partners' , orderable: false, searchable: false},
   	// 				{ data: 'TOT', name: 'TOT' , orderable: false, searchable: false},
					
   	// 				{ data: 'SOU', name: 'SOU' , orderable: false, searchable: false},					
   	// 				{ data: 'SCR', name: 'SCR' , orderable: false, searchable: false},
   	// 				{ data: 'EVA', name: 'EVA' , orderable: false, searchable: false},
   	// 				{ data: 'NEO', name: 'NEO' , orderable: false, searchable: false},
   	// 				{ data: 'OFF', name: 'OFF' , orderable: false, searchable: false},
					
   	// 				{ data: 'JOI', name: 'JOI' , orderable: false, searchable: false},
   	// 				{ data: 'REJ', name: 'REJ' , orderable: false, searchable: false} 	   
   	// 			],				
		// 		//timeout: 500000,
    //        		async: false
   				
   	// }); 
   
   	// new $.fn.dataTable.FixedHeader( table );

   	$(document).on('keyup','.inputJobSearch',function(){
      // $('#myTable').DataTable().draw();
      fetchallJobsAjax();
   	  //  table._fnDraw();
   	});
   	$(document).on('change','#status,#hiringtype',function(){
      // $('#myTable').DataTable().draw();
      fetchallJobsAjax();
   	   //  table._fnDraw();
   	});
   
   
    $(document).on('click', '.show-detail', function ()
      {
      return false;
   		$('#kt_activitiesJob_toggle').trigger('click');		
   		var id    		=	$(this).data('row-id');
   		var url   		=	"{{ route('service_company.jobs.viewmore',':id') }}?viewmore=viewmore";
   		url     		=	url.replace(':id', id);
   		var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  		: 'GET',
   						url   		: url,
   						 beforeSend	: function() {
                                     $('#kt_activitiesJob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success 	: function (response) {
									$('#kt_activitiesJob_body').html(response.view);	
   						}
   	  			});
   	}); 
   	
		
    $(document).on('click', '.status_stage', function () { 
      return false;
        $('#status_body_content').html('');
        var id        	=	$(this).data('row-id');	
        var url       	=	"{{ route('service_company.jobs.editJobStatus',':id') }}";
        url           	=	url.replace(':id', id);
        $.ajax({
              type  	: 'GET',
              url   	: url,
              success : function (response) {
                if (response.status == "success") {
                  $('#status_body_content').html(response.view);      
                }
              }
        }); 			
    });
	
    $(document).on('click', '.client-show-detail', function ()
    {
      return false;
      $('#kt_activitiesJob_toggle').trigger('click');		
          var id    		=	$(this).data('row-id');
          var url   		=	"{{ route('service_company.jobs.viewmore',':id') }}?viewmore=clientviewmore";
          url     		=	url.replace(':id', id);
          var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
          $.ajax({
                  type  		: 'GET',
                  url   		: url,
                  beforeSend	: function() {
                                        $('#kt_activitiesJob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                            },
                  success 	: function (response) {
                      $('#kt_activitiesJob_body').html(response.view);	
                  }
                });

                    // $('#kt_activitiesClientjob_toggle').trigger('click');	
                    // var id    		=	$(this).data('row-id');
                    // var url   		=	"{{ route('service_company.jobs.clientviewmore',':id') }}"; //clientviewmore
                    // url     		=	url.replace(':id', id);
                    // var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
                    // $.ajax({
                    // 				type  		: 'GET',
                    // 				url   		: url,
                    // 				 beforeSend	: function() {
                    // 							 $('#kt_activitiesclientjob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');			
                    // 				},
                    // 				success 	: function (response) {
                    // 						$('#kt_activitiesclientjob_body').html(response.view);		
                    // 				}
                    // 		});
    }); 
	
    $(document).on('click', '.spartner-listing', function ()
    {
      return false;
      $('#kt_activitiesStaffing_toggle').trigger('click');		
      var id    		= $(this).data('row-id');
      var url   		= "{{ route('service_company.jobs.spartnerlisting',':id') }}";
      url     		= url.replace(':id', id);
      var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
      $.ajax({
              type  : 'GET',
              url   : url,
              beforeSend: function() {
                    $('#kt_activities_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
              },
              success : function (response) {
                  $('#kt_activities_body').html(response.view);		
              }
          });
    }); 
	
    function updateJobStatus(id, statusName)
    {
      var id        	= id;
      var statusName  = statusName;
      var url       	= "{{ route('service_company.jobs.updatestatus',':id') }}";
      url         	= url.replace(':id', id);
      var token       = "{{ csrf_token() }}";
      $.easyAjax({
              type		: 'POST',
              url		: url,
              data		: {'_token': token, '_method': 'POST', 'id':id, 'statusName':statusName},
              success	: function (response){
                table._fnDraw();
            }
          });
    }
     
</script>
<script>

$(document).ready(function(){

		table.on('click', '.hmlisting', function(){
      return false;
			
														$('#kt_activitiesRecruiter_toggle').trigger('click');  
														var id    		= $(this).data('row-id');
														var token 		=	"{{ csrf_token() }}";
										
											
														var url   		=	"{{ route('service_company.jobs.hmlisting',':id') }}"; 
														url     		=	url.replace(':id', id);  		
														var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}";    		
														$.ajax({
																	type  		: 'GET',
																	url   		: url,
																	data		: {'_token': token, 'id' : id},
																	 beforeSend	: function() {
																				 $('#kt_activitiesRecruiter_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
																	},
																	success 	: function (response) {
																				$('#kt_activitiesRecruiter_body').html(response.view);
																	}
															}); 
												
		
		});
    
});
</script>
<script>

    $(document).on('click', '.client-hm-listing', function ()
    {	
          return false;
          $('#kt_activitiesCHM_toggle').trigger('click');		
          var id    		= $(this).data('row-id');
          var url   		= "{{ route('service_company.jobs.clienthmlisting',':id') }}";
          url     		= url.replace(':id', id);
          var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
          $.ajax({
                  type  		: 'GET',
                  url   		: url,
                  beforeSend	: function() {
                                        $('#kt_activitiesCHM_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                            },
                  success : function (response) {
                    $('#kt_activitiesCHM_body').html(response.view);  	
                    $('#company_or_client').text(response.company_or_client);							
                  }
                });
    });
	
    $(document).on('click', '.team-listing', function ()
    {
          return false;
          $('#kt_activitiesTEAM_toggle').trigger('click');		
          var id    		= $(this).data('row-id');
          var url   		= "{{ route('service_company.jobs.currentteamlisting',':id') }}";
          url     		= url.replace(':id', id);
          var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
          $.ajax({
                  type  : 'GET',
                  url   : url,
                  beforeSend: function() {
                                        $('#kt_activitiesTEAM_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                            },
                  success : function (response) {
                    $('#kt_activitiesTEAM_body').html(response.view);		
                  }
                });
    });
	
</script>
<script>

    $(document).on('click', '.hm-listing', function ()
    {
          return false;
          $('#kt_activitiesHM_toggle').trigger('click');		
          var id    		= $(this).data('row-id');
          var url   		= "{{ route('service_company.jobs.hmlisting',':id') }}";
          url     		= url.replace(':id', id);
          var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
          $.ajax({
                  type  : 'GET',
                  url   : url,
                  beforeSend: function() {
                                        $('#kt_activitiesHM_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                            },
                  success : function (response) {		
                    $('#kt_activitiesHM_body').html(response.view);		
                  }
                });
    });
      
   	$('body').on('click', '.team_event_not', function(){
					    	Swal.fire({
									text				: "You are not authorized to change the status. Please contact the administrator for assistance.!",
									icon				: "warning",
									buttonsStyling		: false,
									confirmButtonText	: "Ok, got it!",
									customClass			: {
																confirmButton: "btn btn-primary"
														  }
                    });
	  });



    $(document).ready(function() {
            // $.noConflict();

            fetchallJobsAjax();
            
            $(document).on('click', '#paginationJobs .page-link', function(e) {
                e.preventDefault();

                const page = $(this).data('page');
                if (!page || $(this).hasClass('disabled')) return;

                // Disable the clicked pagination link
                $(this).addClass('disabled');

                // Fetch data
                fetchallJobsAjax(page, $('#search').val()).then(() => {
                    // Re-enable the links after successful data fetch
                    $('#paginationJobs .page-link').removeClass('disabled');
                }).catch(() => {
                    // Re-enable the links even if there's an error
                    $('#paginationJobs .page-link').removeClass('disabled');
                });
            });


    });

 

    function fetchallJobsAjax(page = 1, search = '') {
        var clientid = '1';
        $('.loadingMessagejobListAjax').css({ display: 'block' });
        $.ajax({
            url: `{!! route('superadmin.company.all_jobs_data_ajax') !!}?page=${page}`,
            type: 'GET',
            data: {
              search:$('#inputSearch').val(),
              custom_search:$('#kt_tagify_2').val(),
              custom_status:$('#status').val(),
              custom_hiringtype:$('#hiringtype').val()
            },
            success: function(response) {
                  
                $('.loadingMessagejobListAjax').css({ display: 'none' });
                $('#jobListAjax').html(response.html);
                $('#allRequisition').html(response.total);
                $('#paginationJobs').html(response.pagination);
            }
        });
    }


</script>
@endpush 