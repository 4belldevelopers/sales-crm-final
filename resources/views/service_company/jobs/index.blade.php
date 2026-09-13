@extends('layouts.service_company_layout')
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
  #kt_activitiesTerms {width: 1025px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesTerms {width: 1200px;}
}
/* @media only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesStaffing {width: 1100px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesStaffing {width: 1200px;}
} */
@media only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesHM {width: 1025px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesHM {width: 1200px;}
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
.modal-backdrop.show {
    opacity: 0 !important;
    display: none;
}

.hidden-button {
  display: none; /* Initially hidden */
  position: absolute;
  bottom: 10px; /* Adjust position as needed */
  left: 50%;
  transform: translateX(-50%);
  padding: 10px 20px;
  background-color: #007BFF;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}
.show_hidden:hover .hidden-button {
  display: block; /* Show the button on hover */
}

/* Prevent filter bar and table from compressing when the job-detail drawer opens */
#kt_content_container {
  min-width: 760px;
  overflow-x: auto;
}
.filter-bar {
  flex-wrap: nowrap !important;
}
.filter-bar .search-input-wrap {
  min-width: 140px;
  flex-shrink: 1;
}
.filter-bar .search-input,
.filter-bar .filter-select,
.filter-bar .sortingfilterselect,
.filter-bar .more-filters-btn {
  font-size: 12.5px !important;
  white-space: nowrap;
}

</style>

    
<link href="{{ asset('demo/css/joblist.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/topCard.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />





@endpush

@section('content')

@php	
     use App\Helper\GeneralMethod;
     
      $job_status = GeneralMethod::job_status();
      $salaryMode = GeneralMethod::salaryMode();
      $currencySymbole = GeneralMethod::currencySymbole();
      $productAllowCompany = GeneralMethod::companiesType();
      
        $tabactive = [
         'allRequisition_active'=>'tabactive',
         'allRequisition_url'=> 'javascript:void(0);',
         'allRequisition_id'=>'all_jobs_count',
         'customRequisition_active'=>'',
         'customRequisition_url'=> url('service_company/jobs/custom_requisition/0'),
         'customRequisition_id'=>'',
         'clientRequisition_active'=>'',
         'clientRequisition_url'=> url('service_company/jobs/client_requisition/0'),
         'clientRequisition_id'=>'',
         'jobRequest_active'=>'',
         'jobRequest_url'=> url('service_company/jobs/job_request/0'),
         'jobRequest_id'=>'',
         'allRequisition' =>@$dataCnt['allRequisition'],
         'customRequisition' =>@$dataCnt['customRequisition'],
         'clientRequisition' =>@$dataCnt['clientRequisition'],
         'jobRequest' =>@$dataCnt['jobRequest'],
         'productAllowCompany' =>@$productAllowCompany,
         'userPermissions' =>$userPermissions,
         'hiringCount'    =>@$dataCnt['hiringCount'],
         'holdCount'      =>@$dataCnt['holdCount'],
         'closedCount'    =>@$dataCnt['closedCount'],
         'allTabCount'    =>@$dataCnt['allTabCount'],
        ];
        $searchbar = [
         'userPermissions'=>$userPermissions
        ];
     
@endphp     
     
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4 kt_content_job" id="kt_content">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">

      <!-- CONTENT -->
        <div class="content">        

          <!-- HERO -->
           @include('sections.service_company.jobHeader',['tabactive'=>$tabactive])

          <!-- FILTER BAR -->
          @include('sections.service_company.jobFilterBar',['searchbar'=>$searchbar])

          <!-- TABLE -->
          <div class="table-card">
            <!-- Header -->
            <div class="table-header">
              <div class="th">Job Details</div>
              <div class="th">
                Experience
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24">
                  <rect
                    x="2"
                    y="7"
                    width="20"
                    height="14"
                    rx="2"
                    stroke="currentColor"
                    stroke-width="1.8"
                  />
                  <path
                    d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"
                    stroke="currentColor"
                    stroke-width="1.8"
                  />
                </svg>
              </div>
              <div class="th">Hiring &amp; Location</div>
              <div class="th">Hiring Manager</div>
              <div class="th">Team</div>
              <div class="th">Agency</div>
              <div class="th">
                Hiring Pipeline
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24">
                  <circle
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="1.8"
                  />
                  <path
                    d="M12 8v4M12 16h.01"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                  />
                </svg>
              </div>
            </div>

             <div class="loadingMessage" style="display: none; position: absolute;">Processing...</div>
             <div class="" id="allJobList"></div>

            <!-- PAGINATION -->
             <div id="pagination"></div>

            <!-- LEGEND -->
             @include('sections.service_company.pipeline')
            
          </div>
          <!-- /table-card -->
        </div>
        <!-- /content -->
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

<div id="kt_activitiesJob_toggle"></div>
<div id="kt_activitiesJob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end"
data-kt-drawer-toggle="#kt_activitiesJob_toggle" data-kt-drawer-close="#kt_activitiesJob_close">
  <div class="card shadow-none bg-custom-bar rounded-0 w-100 h-100">
    <div class="jobs-drawer-header">
      <div class="jobs-drawer-title-wrap">
        <span class="jobs-drawer-kicker" id="jobsDrawerKicker">Job Details</span>
        <h3 class="jobs-drawer-title" id="jobsDrawerTitle">Job Details</h3>
        <div class="jobs-drawer-meta" id="jobsDrawerMeta">Loading selected requisition...</div>
      </div>
      <button type="button" class="jobs-drawer-close jd-drawer-close-btn" id="kt_activitiesJob_close" aria-label="Close job details">
        <i class="fa fa-xmark"></i>
      </button>
    </div>
    <div class="card-body bg-custom-bar position-relative px-0 py-0" id="kt_activitiesJob_body"></div>
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
        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
            style="float:left"> 
            <a href="javascript:void(0);" class="l">
                <div class="symbol-label border-radius-5" id="light_logo_company">&nbsp;</div>
            </a> 
        </div>
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

<div id="kt_activitiesCHM_toggle"></div>
<div id="kt_activitiesCHM" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesCHM_toggle" data-kt-drawer-close="#kt_activitiesCHM_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
            style="float:left"> 
            <a href="javascript:void(0);" class="l">
                <div class="symbol-label border-radius-5" id="light_client_caption">&nbsp;</div>
            </a> 
        </div>

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
<!-- Job Status drawer — replaces the old #status_stage modal. Slides in from the right,
     capped to 80% of the viewport width on laptop/desktop (per design spec) so the top
     menu bar stays visible/reachable beside it; expands to full width on small screens
     where 80% would leave too little room for the two-column layout. Same trigger class
     (.status_stage) and same AJAX endpoints as before — only the chrome changed. -->
<style>
  #status_drawer .sd-topbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 18px 28px; border-bottom: 1px solid #eef0f4; flex-wrap: wrap; }
  #status_drawer .sd-topbar-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
  #status_drawer .sd-topbar-ico { width: 42px; height: 42px; border-radius: 10px; background: #eef4ff; color: #3e97ff; display: flex; align-items: center; justify-content: center; font-size: 17px; flex-shrink: 0; }
  #status_drawer .sd-topbar-title { font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; color: #181c32; line-height: 1.3; }
  #status_drawer .sd-topbar-sub { font-family: 'Poppins', sans-serif; font-size: 11.5px; font-weight: 400; color: #7e8299; margin-top: 1px; }
  #status_drawer .sd-topbar-right { display: flex; align-items: center; gap: 10px; }
  #status_drawer .sd-btn-cancel { border: 1px solid #e4e6ef; background: #fff; color: #3f4254; border-radius: 8px; padding: 9px 16px; font-family: 'Poppins', sans-serif; font-size: 12.5px; font-weight: 500; cursor: pointer; }
  #status_drawer .sd-btn-cancel:hover { background: #f8f9fb; }
  #status_drawer .sd-btn-confirm { border: none; background: #3e97ff; color: #fff; border-radius: 8px; padding: 9px 18px; font-family: 'Poppins', sans-serif; font-size: 12.5px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: background .15s; }
  #status_drawer .sd-btn-confirm:hover { background: #2f7fe0; }
  #status_drawer .sd-btn-confirm:disabled { background: #c2c9d6; cursor: not-allowed; }
  #status_drawer .sd-close { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #7e8299; cursor: pointer; flex-shrink: 0; }
  #status_drawer .sd-close:hover { background: #f8f9fb; color: #f64e60; }
</style>
<div id="status_drawer_toggle" style="display:none;"></div>
<div id="status_drawer" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="job-status"
    data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
    data-kt-drawer-width="{default:'100%', 'sm': '92%', 'lg': '80%'}" data-kt-drawer-direction="end"
    data-kt-drawer-toggle="#status_drawer_toggle" data-kt-drawer-close="#status_drawer_close">
  <div class="d-flex flex-column h-100 w-100">
    <div class="sd-topbar">
      <div class="sd-topbar-left">
        <span class="sd-topbar-ico"><i class="fas fa-briefcase"></i></span>
        <div>
          <div class="sd-topbar-title">Job Status</div>
          <div class="sd-topbar-sub">Update the job status and manage its visibility across the platform.</div>
        </div>
      </div>
      <div class="sd-topbar-right">
        <button type="button" class="sd-btn-cancel" id="status_sub_stage_close">Cancel</button>
        <button type="button" class="sd-btn-confirm" id="status_confirm_btn" disabled onclick="newUpdateJobstatus()">
          <span class="indicator-label changetextupdate">Update Status</span> <i class="fas fa-arrow-right"></i>
        </button>
        <span class="sd-close" id="status_drawer_close"><i class="fa fa-xmark"></i></span>
      </div>
    </div>
    <div class="status_body_content flex-grow-1" id="status_body_content" style="overflow-y:auto;"></div>
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
			// table._fnDraw();
      fetchCandidates();
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
   var table = $('#myTablew').dataTable({
   
   			responsive  	: true,
   			processing  	: false,
   			serverSide  	: true,
   			destroy   		: true,
   			ordering		: false, //disable ordering	
   			"bLengthChange" : false, //thought this line could hide the LengthMenu   			
   			sDom			: 'lrtip',   			
   			oLanguage 		: { sLengthMenu: "_MENU_", },
			ajax			: {'url' : '{!! route('service_company.jobs.all_data') !!}', "data": function ( d ){d.search = $('#inputSearch').val(),d.custom_search = $('#kt_tagify_2').val(),d.custom_status = $('#status').val(),d.custom_hiringtype = $('#hiringtype').val(),d.employment_type = $('#employment_type').val()},
           beforeSend: function () {
            if($('#kt_tagify_2').val()!=''){
                // $('.loadingMessage').css({ display: 'block' });
                $('.loader_search').css({ display: 'block' });
            }else{
                $('.page-loader').css('display', 'flex');
            } // Show loader before request
            },
            complete: function () {
              $('.page-loader').css('display', 'none'); // Hide loader after request
              $('.loader_search').css({ display: 'none' });
            }
      },
   		  "drawCallback"	: function( settings ) {
									$('[data-toggle="tooltip1"]').tooltip();
									$('[data-toggle="tooltip2"]').tooltip();
									$('[data-toggle="tooltip3"]').tooltip();
									$('[data-toggle="tooltip4"]').tooltip();		
							},
   		  "columnDefs": [
                    { "width": "50px", "targets": 0, "width": "4%", "class": "" },
                    { "width": "200px", "targets": 1, "class": "d-flex align-items-center" },
                    { "width": "50px", "targets": 2 },
                    { "width": "100px", "targets": 3 },
                    { "width": "200px", "targets": 4 },
                    { "width": "50px", "targets": 5 },
                    { "width": "200px", "targets": 6 },
                    { "width": "150px", "targets": 7 },
                    { "width": "150px", "targets": 8 },
                    { "width": "20px", "targets": 9 },
                    { "width": "20px", "targets": 10 },
                    { "width": "20px", "targets": 11 },
                    { "width": "20px", "targets": 12 },
                    { "width": "20px", "targets": 13 },
                    { "width": "20px", "targets": 14 },
                    { "width": "20px", "targets": 15 },
                    { "width": "20px", "targets": 16 },
                    
                   
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
				//timeout: 500000,
           		async: false
   				
   				}); 
   
   	// new $.fn.dataTable.FixedHeader( table );
   	$(document).on('keyup','.inputJobSearch',function(e){
      // $('#myTable').DataTable().draw();
          if (e.key === 'Enter' || e.keyCode === 13) {
            fetchCandidates(); // your custom function
          }
            // fetchCandidates();
   	  //  table._fnDraw();

   	});
   	$(document).on('change','#status,#hiringtype,#employment_type',function(){
        if (this.id === 'status') {
            // Sync the tab row to match the selected status
            var val = $(this).val();
            var tabStatus = (val === 'inactive') ? 'inactive' : val; // closed → inactive
            $('.jh-tab').removeClass('jh-tab-active');
            $('.jh-tab[data-status="' + tabStatus + '"]').addClass('jh-tab-active');
        }
        localStorage.setItem('currentPageJobs', 1);
        fetchCandidates(1);
   	});

    // Called from the tab row in jobHeader — filters table and marks tab active
    function filterJobsByStatus(status) {
        $('#status').val(status);
        $('.jh-tab').removeClass('jh-tab-active');
        $('.jh-tab[data-status="' + status + '"]').addClass('jh-tab-active');
        localStorage.setItem('currentPageJobs', 1);
        fetchCandidates(1);
    }
    
   
   
    $(document).on('click', '.show-detail', function ()
   	{
      // alert("kkss");
   		$('#kt_activitiesJob_toggle').trigger('click');		
      
      // localStorage is already kept current by fetchCandidates; no DOM read needed
      var currentPageJobs = parseInt(localStorage.getItem('currentPageJobs')) || 1;
      localStorage.setItem('currentPageJobs', currentPageJobs);

   		var id    		=	$(this).data('row-id');
      var selectedJob = findJobById(id);
   		var url   		=	"{{ route('service_company.jobs.viewmore',':id') }}?viewmore=viewmore";
   		url     		=	url.replace(':id', id);
   		$.ajax({
   						type  		: 'GET',
   						url   		: url,
   						 beforeSend	: function() {
                                     setJobsDrawerLoading(selectedJob);
                       	},
   						success 	: function (response) {
									$('#kt_activitiesJob_body').html(response.view);
   						},
   						error		: function (xhr) {
   							var msg = (xhr.status === 0 || !navigator.onLine)
   								? 'You are offline. Please check your connection.'
   								: 'Could not load job details (HTTP ' + xhr.status + '). Please try again.';
   							$('#kt_activitiesJob_body').html('<div style="padding:24px;text-align:center;font-family:Poppins,sans-serif;font-size:13px;color:#f64e60;">' + msg + '</div>');
   						}
   	  			});
   	});


  $(document).on('click', '#status_sub_stage_close', function () { $('#status_drawer_close').trigger('click'); });

  $(document).on('click', '.status_stage', function () {
		// Direct Client jobs (recommended to this company, not owned by it)
		// stay clickable, but only the owning company may actually change
		// the status — enforced server-side too, in
		// ServiceCompanyJobsController::newUpdateJobstatus(). Notify instead
		// of opening a drawer whose submit would just get rejected.
		if ($(this).data('direct-client') == 1) {
			Swal.fire({
				icon: 'warning',
				title: 'Restricted',
				text: 'Direct Client Job Status cannot be changed by Agencies'
			});
			return false;
		}
		$('#status_body_content').html('<div class="text-center py-20 text-muted"><i class="fa fa-spinner fa-spin"></i> Loading…</div>');
		$('#status_confirm_btn').prop('disabled', true).css('background', '');
		$('.changetextupdate').text('Update Status');
		$('#status_drawer_toggle').trigger('click');
		var id        	=	$(this).data('row-id');
		var url       	=	"{{ route('service_company.jobs.editJobStatus',':id') }}";
		url           	=	url.replace(':id', id);
		$.ajax({
					type  	: 'GET',
					url   	: url,
					success : function (response) {
						if (response.status == "success") {
							$('#status_body_content').html(response.view);
						} else {
							$('#status_body_content').html('<div class="text-center py-20 text-danger">Could not load the job status details. Please close and try again.</div>');
						}
					},
					error : function () {
						$('#status_body_content').html('<div class="text-center py-20 text-danger">Something went wrong loading this job\'s status. Please close and try again.</div>');
					}
		});
});
	
$(document).on('click', '.client-show-detail', function ()
{
  $('#kt_activitiesJob_toggle').trigger('click');		
   		var id    		=	$(this).data('row-id');
        var selectedJob = findJobById(id);
   		var url   		=	"{{ route('service_company.jobs.viewmore',':id') }}?viewmore=clientviewmore";
   		url     		=	url.replace(':id', id);
   		$.ajax({
   						type  		: 'GET',
   						url   		: url,
   						 beforeSend	: function() {
                                     setJobsDrawerLoading(selectedJob);
                       	},
   						success 	: function (response) {
									$('#kt_activitiesJob_body').html(response.view);
   						},
   						error		: function (xhr) {
   							var msg = (xhr.status === 0 || !navigator.onLine)
   								? 'You are offline. Please check your connection.'
   								: 'Could not load job details (HTTP ' + xhr.status + '). Please try again.';
   							$('#kt_activitiesJob_body').html('<div style="padding:24px;text-align:center;font-family:Poppins,sans-serif;font-size:13px;color:#f64e60;">' + msg + '</div>');
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
						  // table._fnDraw();
              fetchCandidates();
					 }
				 });
   }
     
</script>
<script>

$(document).ready(function(){

  $(document).on('click', '.hmlisting', function(){
			
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
                if(response.logo){
                    $('#light_client_caption').removeClass('d-none');
                    $('#light_client_caption').html('<img src="' + response.logo +
                      '" height="35px" width="100%">');
                  }else{
                    $('#light_client_caption').addClass('d-none');
                  }
                $('#company_or_client').text(response.company_or_client);							
   						}
   	  			});
   	});
	
     $(document).on('click', '.team-listing', function ()
   	{
   	
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
                if(response.logo){
                    $('#light_logo_company').removeClass('d-none');
                    $('#light_logo_company').html('<img src="' + response.logo +
                      '" height="35px" width="100%">');
                  }else{
                    $('#light_logo_company').addClass('d-none');
                  }
   						}
   	  			});
   	});
	
</script>
<script>


   	
   	
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

            fetchCandidates();

            // Pagination bar markup (service_company.ajax_pages.pagination) uses .pagi-page /
            // .pagi-arrow buttons and a #rowsPerPageSelect dropdown, not the old .page-link
            // links this handler used to target — that mismatch is why Next/page-numbers and
            // the per-page selector did nothing. Disabled states (.pagi-disabled /
            // .pagi-active-disabled) already carry pointer-events:none in CSS, so clicks on
            // them never reach here; the :not() below is just a defensive second check.
            $(document).on('click', '#pagination .pagi-page:not(.pagi-active-disabled), #pagination .pagi-arrow:not(.pagi-disabled)', function(e) {
                e.preventDefault();

                const page = $(this).data('page');
                if (!page) return;

                fetchCandidates(page, $('#search').val());
            });

        });

        var page_no = localStorage.getItem('currentPageJobs');
        if (!page_no) {
            page_no = 1;
        }
        localStorage.removeItem('jobsPageSize');
        var pageSize = 10;

        /**
         * Client-Side Jobs Store & Rendering Engine
         * Enables instant 0ms searching, filtering, sorting, and UI updates on the client side.
         */
        window.jobsState = {
            all: [],
            filtered: [],
            total: 0
        };

        function escHtml(value) {
            return String(value == null ? '' : value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function escAttr(value) {
            return escHtml(value).replace(/`/g, '&#096;');
        }

        function cssClassList(value) {
            return String(value == null ? '' : value).replace(/[^a-zA-Z0-9_\-\s]/g, '').trim();
        }

        function numText(value, fallback) {
            var parsed = parseInt(value, 10);
            return isNaN(parsed) ? (fallback || 0) : parsed;
        }

        function countLabel(count, singular, plural) {
            var safeCount = numText(count, 0);
            return safeCount + ' ' + (safeCount === 1 ? singular : plural);
        }

        function syncJobsResultSummary(visibleCount, totalCount) {
            var visible = numText(visibleCount, 0);
            var total = numText(totalCount, visible);
            var text = total > visible ? ('Showing ' + visible + ' of ' + total + ' jobs') : ('Showing ' + visible + ' jobs');
            $('#jobsResultCount').text(text);
        }

        function selectedOptionText(selector) {
            var $option = $(selector).find('option:selected');
            return ($option.text() || '').trim();
        }

        function renderAppliedFiltersCard() {
            var chips = [];
            var searchTerm = ($('.inputJobSearch').val() || '').trim();
            var status = $('#status').val() || 'all';
            var employmentType = $('#employment_type').val() || 'all';
            var expFilter = $('#experience_filter').val() || 'all';
            var sortVal = $('#hiringtype').val() || '1';

            if (searchTerm !== '') chips.push({ label: 'Search', value: searchTerm, target: 'search' });
            if (status !== 'all' && status !== '') chips.push({ label: 'Status', value: selectedOptionText('#status'), target: 'status' });
            if (employmentType !== 'all' && employmentType !== '') chips.push({ label: 'Type', value: selectedOptionText('#employment_type'), target: 'employment_type' });
            if (expFilter !== 'all' && expFilter !== '') chips.push({ label: 'Experience', value: selectedOptionText('#experience_filter'), target: 'experience_filter' });
            if (sortVal !== '1') chips.push({ label: 'Sort', value: selectedOptionText('#hiringtype'), target: 'hiringtype' });

            var visibleCount = window.jobsState && window.jobsState.filtered ? window.jobsState.filtered.length : 0;
            var totalCount = window.jobsState ? window.jobsState.total : visibleCount;
            syncJobsResultSummary(visibleCount, totalCount);

            if (chips.length === 0) {
                $('#jobsAppliedChips').html('<span class="jh-applied-muted">No filters applied</span>');
            } else {
                $('#jobsAppliedChips').html(chips.map(function(chip) {
                    return '<button type="button" class="jh-applied-chip" data-filter-target="' + escAttr(chip.target) + '">' +
                        '<span>' + escHtml(chip.label) + ':</span> ' + escHtml(chip.value) +
                        '<i class="fa fa-xmark"></i>' +
                    '</button>';
                }).join(''));
            }

            $('#jobsAppliedFilters').removeClass('d-none');
        }

        function resetJobsFilters() {
            $('.inputJobSearch').val('');
            $('#status').val('all');
            $('#employment_type').val('all');
            $('#experience_filter').val('all');
            $('#hiringtype').val('1');
            $('.jh-tab').removeClass('jh-tab-active');
            $('.jh-tab[data-status="all"]').addClass('jh-tab-active');
            localStorage.setItem('currentPageJobs', 1);
            fetchCandidates(1);
        }

        $(document).on('click', '.jh-applied-chip', function() {
            var target = $(this).data('filter-target');
            if (target === 'search') {
                $('.inputJobSearch').val('');
                fetchCandidates(1);
                return;
            }

            if (target === 'status') {
                $('#status').val('all');
                $('.jh-tab').removeClass('jh-tab-active');
                $('.jh-tab[data-status="all"]').addClass('jh-tab-active');
                fetchCandidates(1);
                return;
            }

            if (target === 'employment_type') {
                $('#employment_type').val('all');
                fetchCandidates(1);
                return;
            }

            if (target === 'experience_filter') {
                $('#experience_filter').val('all');
                filtersdata();
                return;
            }

            if (target === 'hiringtype') {
                $('#hiringtype').val('1');
                fetchCandidates(1);
            }
        });

        function setJobsDrawerContext(job) {
            $('#jobsDrawerKicker').text(job && job.is_direct_client ? 'Direct Client Job' : 'Job Details');
            $('#jobsDrawerTitle').text((job && job.title) ? job.title : 'Job Details');

            var meta = [];
            if (job && job.id) meta.push('Job ID ' + job.id);
            if (job && job.clients_name) meta.push(job.clients_name);
            if (job && job.status) meta.push(job.status);
            if (job && job.city) meta.push(job.city);
            $('#jobsDrawerMeta').text(meta.length ? meta.join(' | ') : 'Loading selected requisition...');
        }

        function setJobsDrawerLoading(job) {
            setJobsDrawerContext(job || null);
            $('#kt_activitiesJob_body').html(
                '<div class="jobs-drawer-skeleton">' +
                    '<div class="jd-skel-line jd-skel-lg"></div>' +
                    '<div class="jd-skel-line"></div>' +
                    '<div class="jd-skel-grid">' +
                        '<span></span><span></span><span></span><span></span>' +
                    '</div>' +
                '</div>'
            );
        }

        function findJobById(id) {
            var allJobs = (window.jobsState && window.jobsState.all) ? window.jobsState.all : [];
            return allJobs.find(function(job) { return String(job.id) === String(id); }) || null;
        }

        function renderJobsClientSide(jobs) {
            if (!jobs || jobs.length === 0) {
                return '<div style="text-align:center;padding:40px 20px;font-family:Poppins,sans-serif;color:#7e8299;">' +
                       '<i class="fa fa-briefcase" style="font-size:32px;margin-bottom:12px;display:block;"></i>' +
                       '<p style="font-size:13px;margin:0 0 14px;">No jobs found matching your criteria.</p>' +
                       '<button type="button" onclick="resetJobsFilters()" class="btn btn-sm btn-primary" style="font-size:12px;">Reset Filters</button></div>';
            }

            var html = '';
            jobs.forEach(function(job) {
                var profileDetailClass = job.is_direct_client ? 'client-show-detail' : 'show-detail';
                var directClientAttr = job.is_direct_client ? '1' : '0';
                var statusIcon = job.status === 'Hiring' ? 'fa-user-group' : (job.status === 'Hold' ? 'fa-pause' : 'fa-box-archive');
                var statusClass = job.status_raw === 'active' ? 'active-bg' : (job.status_raw === 'hold' ? 'hold-bg' : 'closed-bg');
                if (job.status === 'Removed') statusClass = 'closed-bg';

                var ulink = job.status === 'Removed'
                    ? '<span class="badge-hiring ' + statusClass + '">' + escHtml(job.status) + '</span>'
                    : '<span data-row-id="' + escAttr(job.id) + '" data-direct-client="' + directClientAttr + '" class="badge-hiring ' + statusClass + ' status_stage" title="Change job status"><i class="fas ' + statusIcon + '"></i> ' + escHtml(job.status) + '</span>';

                var logoHtml = job.logo_path
                    ? '<img src="' + escAttr(job.logo_path) + '" alt="Company Logo" class="border-radius-5 w-100">'
                    : escHtml(job.initial_letter);

                var typeIcon = job.is_direct_client
                    ? '<i class="fas fa-handshake" style="color:#09b760;"></i>'
                    : '<i class="fas fa-building" style="color:#3e97ff;"></i>';

                var skillsHtml = '';
                if (job.skills && job.skills.length > 0) {
                    var visibleSkills = job.skills.slice(0, 4);
                    skillsHtml = visibleSkills.map(function(s) {
                        return '<span class="badge badge-light my-1 job-skill-chip" title="' + escAttr(s) + '">' + escHtml(s) + '</span>';
                    }).join(' ');
                    if (job.skills.length > visibleSkills.length) {
                        skillsHtml += ' <span class="badge badge-light-primary my-1 job-skill-chip" title="' + escAttr(job.skills.join(', ')) + '">+' + (job.skills.length - visibleSkills.length) + '</span>';
                    }
                }

                var teamHtml = '';
                var partnersCount = numText(job.partners_count, 0);
                if (job.is_direct_client) {
                    if (job.partners_first_name) {
                        teamHtml += '<div class="hm-name team-listing text-truncate" title="' + escAttr(job.partners_first_name) + '" data-row-id="' + escAttr(job.id) + '">' + escHtml(job.partners_first_name) + '</div>';
                    }
                    teamHtml += '<div class="action-count">' + countLabel(partnersCount, 'Member', 'Members') + '</div>';
                    teamHtml += '<button type="button" class="action-more team-listing" data-row-id="' + escAttr(job.id) + '">Manage Team</button>';
                } else {
                    if (job.partners_first_name) {
                        teamHtml += '<div class="hm-name hm-listing text-truncate" title="' + escAttr(job.partners_first_name) + '" data-row-id="' + escAttr(job.id) + '">' + escHtml(job.partners_first_name) + '</div>';
                    }
                    teamHtml += '<div class="action-count">' + countLabel(partnersCount, 'Member', 'Members') + '</div>';
                    teamHtml += '<button type="button" class="action-more hm-listing" data-row-id="' + escAttr(job.id) + '">Manage Team</button>';
                }

                var agencyHtml = '';
                if (job.is_direct_client) {
                    agencyHtml += '<div class="hm-name text-truncate" title="' + escAttr(job.agency_company_name || '') + '">' + escHtml(job.agency_company_name || '') + '</div>';
                } else {
                    if (job.agency_count > 0) {
                        agencyHtml += '<div class="hm-name">' + countLabel(job.agency_count, 'Agency', 'Agencies') + '</div>';
                        if (typeof job.matching_agency_count !== 'undefined') {
                            agencyHtml += '<div class="action-count">' + countLabel(job.matching_agency_count, 'Match', 'Matches') + '</div>';
                        }
                        agencyHtml += '<button type="button" data-row-id="' + escAttr(job.id) + '" class="action-more spartner-listing">Manage Agency</button>';
                    } else {
                        agencyHtml += '<button type="button" class="btn-action spartner-listing" data-row-id="' + escAttr(job.id) + '"><svg fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>Add Agency</button>';
                        if (typeof job.matching_agency_count !== 'undefined') {
                            agencyHtml += '<div class="action-count">' + countLabel(job.matching_agency_count, 'Match', 'Matches') + '</div>';
                        }
                    }
                }

                var apptrackerBase = job.is_direct_client
                    ? "{{ url('/service_company/apptracker/clientapp') }}/" + String(job.encrypted_id || '') + "/"
                    : "{{ url('/service_company/apptracker') }}/" + String(job.encrypted_id || '') + "/";

                var allAppUrl = job.is_direct_client
                    ? "{{ url('/service_company/apptracker/clientapp') }}/" + String(job.encrypted_id || '') + "/all"
                    : "{{ url('/service_company/apptracker') }}/" + String(job.encrypted_id || '') + "/all";

                var stageLabels = [
                    { id: 1, key: 's-sou', label: 'SOU', title: 'Sourcing' },
                    { id: 2, key: 's-scr', label: 'SCR', title: 'Screening' },
                    { id: 3, key: 's-eva', label: 'EVA', title: 'Evaluation' },
                    { id: 4, key: 's-neg', label: 'NEG', title: 'Negotiation' },
                    { id: 5, key: 's-off', label: 'OFF', title: 'Offer' },
                    { id: 6, key: 's-joi', label: 'JOI', title: 'Joining' },
                    { id: 7, key: 's-rej', label: 'REJ', title: 'Rejected' }
                ];

                var pipelineHtml = '<div class="pipeline-stages">';
                pipelineHtml += '<div class="stage" title="In Review"><div class="stage-label">INW</div><div class="stage-badge s-inw"><a href="' + escAttr(allAppUrl) + '" class="s-inw fw-bolder fs-6" aria-label="In Review applications">' + numText(job.inreview_counter, 0) + '</a></div></div>';

                stageLabels.forEach(function(stg) {
                    var count = (job.stage_counts && job.stage_counts[stg.id]) ? job.stage_counts[stg.id] : 0;
                    var stgUrl = apptrackerBase + stg.id;
                    var countClass = count > 0 ? (stg.id === 1 ? 'text-dark fw-bolder fs-6' : stg.key + ' fs-6') : stg.key + ' fs-6';
                    pipelineHtml += '<div class="stage" title="' + escAttr(stg.title) + '"><div class="stage-label">' + stg.label + '</div><div class="stage-badge ' + stg.key + '"><a href="' + escAttr(stgUrl) + '" class="' + countClass + '" aria-label="' + escAttr(stg.title) + ' applications">' + numText(count, 0) + '</a></div></div>';
                });
                pipelineHtml += '</div>';

                html += '<div class="job-row" data-job-id="' + escAttr(job.id) + '" data-job-title="' + escAttr(job.title || '') + '" data-job-client="' + escAttr(job.clients_name || '') + '" data-job-status="' + escAttr(job.status || '') + '" data-job-city="' + escAttr(job.city || '') + '" data-direct-client="' + directClientAttr + '" style="position: relative; min-height: 111px;">' +
                    '<div style="display: flex; gap: 8px; align-items: flex-start">' +
                      '<div class="drag-col"><div class="drag-handle"><svg width="12" height="12" fill="none" viewBox="0 0 24 24"><circle cx="9" cy="5" r="1.5" fill="currentColor"/><circle cx="15" cy="5" r="1.5" fill="currentColor"/><circle cx="9" cy="12" r="1.5" fill="currentColor"/><circle cx="15" cy="12" r="1.5" fill="currentColor"/><circle cx="9" cy="19" r="1.5" fill="currentColor"/><circle cx="15" cy="19" r="1.5" fill="currentColor"/></svg></div></div>' +
                      '<div class="col-job ' + profileDetailClass + '" data-row-id="' + escAttr(job.id) + '">' +
                        '<div class="job-logo ' + cssClassList(job.bg_text_decore) + '" style="min-height: 48px; align-self: flex-start;">' + logoHtml + '</div>' +
                        '<div class="job-info">' +
                          '<div class="job-title text-truncate" title="' + escAttr(job.title || '') + '">' + escHtml(job.title || '') + '</div>' +
                          '<div class="job-dept text-truncate" title="' + escAttr(job.department || '') + '">' + escHtml(job.department || '') + '</div>' +
                          '<div class="job-client text-truncate" title="' + escAttr(job.clients_name || '') + '">' + escHtml(job.clients_name || '') + '</div>' +
                          '<div class="job-meta-line"><span class="skills-inline"><b>Skills:</b> ' + (skillsHtml || '<span class="text-muted">Not specified</span>') + '</span></div>' +
                        '</div>' +
                      '</div>' +
                    '</div>' +

                    '<div class="col-exp">' +
                      '<div class="exp-row"><svg fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8"/><polyline points="12 6 12 12 16 14" stroke="currentColor" stroke-width="1.8"/></svg>' + escHtml(job.min_exp) + ' - ' + escHtml(job.max_exp) + ' Years</div>' +
                      '<div class="sal-row">' + escHtml(job.min_salary_currency) + ' ' + escHtml(job.min_salary) + ' - ' + escHtml(job.max_salary) + ' <span class="sal-mode">' + escHtml(job.min_sal_mode) + '</span></div>' +
                      '<span class="badge-type">' + typeIcon + ' <span class="it-job-type ' + cssClassList(job.badge) + '">' + escHtml(job.recommendation_text) + '</span> <span class="it-dot-separator"></span> <a href="javascript:void(0);" class="it-job-id">' + escHtml(job.id) + '</a></span>' +
                    '</div>' +

                    '<div class="col-hl">' + ulink +
                      '<div class="loc-row text-truncate" title="' + escAttr(job.city || '') + '"><svg fill="none" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="1.8"/></svg>' + escHtml(job.city || '') + '</div>' +
                      '<div class="mode-row text-truncate" title="' + escAttr(job.work_type || '') + '"><svg fill="none" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke="currentColor" stroke-width="1.8"/><polyline points="9,22 9,12 15,12 15,22" stroke="currentColor" stroke-width="1.8"/></svg>' + escHtml(job.work_type || '') + '</div>' +
                    '</div>' +

                    '<div class="col-hm">' +
                      '<div class="hm-name text-truncate" title="' + escAttr(job.hiring_manager || '') + '">' + escHtml(job.hiring_manager || '') + '</div>' +
                      '<div class="hm-title text-truncate" title="' + escAttr(job.hiring_manager_designation || '') + '">' + escHtml(job.hiring_manager_designation || '') + '</div>' +
                      '<div class="hm-company text-truncate" title="' + escAttr(job.employment_type || '') + '">' + escHtml(job.employment_type || '') + '</div>' +
                    '</div>' +

                    '<div class="col-action">' + teamHtml + '</div>' +
                    '<div class="col-action">' + agencyHtml + '</div>' +

                    '<div class="col-pipeline">' + pipelineHtml +
                      '<div class="pipeline-total">Total: <strong>' + countLabel(job.application_counter, 'Application', 'Applications') + '</strong> <span>|</span> Interview: <strong>' + numText(job.interview_counter, 0) + '</strong></div>' +
                      '<div class="add-date"><svg fill="none" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8"/></svg><span>' + escHtml(job.added_date || '') + '</span></div>' +
                    '</div>' +
                  '</div>';
            });

            return html;
        }

        function filtersdata() {
            if (!window.jobsState || !window.jobsState.all || window.jobsState.all.length === 0) {
                return fetchCandidates(1);
            }

            var searchTerm = ($('.inputJobSearch').val() || $('#inputSearch').val() || '').toLowerCase().trim();
            var status = $('#status').val() || 'all';
            var employmentType = $('#employment_type').val() || 'all';
            var expFilter = $('#experience_filter').val() || 'all';
            var sortVal = $('#hiringtype').val() || '1';

            var filtered = window.jobsState.all.filter(function(job) {
                if (searchTerm !== '') {
                    var matchTitle = (job.title || '').toLowerCase().includes(searchTerm);
                    var matchDept = (job.department || '').toLowerCase().includes(searchTerm);
                    var matchClient = (job.clients_name || '').toLowerCase().includes(searchTerm);
                    var matchCity = (job.city || '').toLowerCase().includes(searchTerm);
                    var matchHM = (job.hiring_manager || '').toLowerCase().includes(searchTerm);
                    var matchSkills = (job.skills || []).some(function(s) { return s.toLowerCase().includes(searchTerm); });
                    var matchId = String(job.id) === searchTerm;
                    if (!matchTitle && !matchDept && !matchClient && !matchCity && !matchHM && !matchSkills && !matchId) {
                        return false;
                    }
                }

                if (status !== 'all' && status !== '') {
                    if (status === 'active' && job.status !== 'Hiring') return false;
                    if (status === 'hold' && job.status !== 'Hold') return false;
                    if (status === 'inactive' && job.status !== 'Closed') return false;
                    if (status === 'removed' && job.status !== 'Removed') return false;
                }

                if (employmentType !== 'all' && employmentType !== '') {
                    if (String(job.employment_type_raw) !== String(employmentType)) return false;
                }

                if (expFilter !== 'all' && expFilter !== '') {
                    var minE = parseInt(job.min_exp) || 0;
                    var maxE = parseInt(job.max_exp) || 0;
                    if (expFilter === '0-1' && !(minE <= 1 && maxE >= 0)) return false;
                    if (expFilter === '1-3' && !(minE <= 3 && maxE >= 1)) return false;
                    if (expFilter === '3-5' && !(minE <= 5 && maxE >= 3)) return false;
                    if (expFilter === '5+' && maxE < 5) return false;
                }

                return true;
            });

            filtered.sort(function(a, b) {
                if (sortVal === '1') return b.id - a.id;
                if (sortVal === '2') return a.id - b.id;
                if (sortVal === '3') return (parseFloat(a.min_salary) || 0) - (parseFloat(b.min_salary) || 0);
                if (sortVal === '4') return (parseFloat(b.max_salary) || 0) - (parseFloat(a.max_salary) || 0);
                if (sortVal === '5') return (a.clients_name || '').localeCompare(b.clients_name || '');
                if (sortVal === '6') return (b.clients_name || '').localeCompare(a.clients_name || '');
                return b.id - a.id;
            });

            window.jobsState.filtered = filtered;
            $('#allJobList').html(renderJobsClientSide(filtered));
            $('#all_jobs_count').html(filtered.length);
            renderAppliedFiltersCard();
        }

        function fetchCandidates(page = page_no, search = '', size = pageSize) {
            var clientid = '1';
            if($('.inputJobSearch').val()!=''){
                $('.loader_search').css({ display: 'block' });
                $('.page-loader').css('display', 'flex');
            }else{
                $('.page-loader').css('display', 'flex');
            }
            return $.ajax({
                url: `{!! route('service_company.jobs.all_data_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                  custom_status : $('#status').val(),
                  search : $('#inputSearch').val(),
                  custom_hiringtype : $('#hiringtype').val(),
                  employment_type : $('#employment_type').val(),
                  custom_search : $('.inputJobSearch').val(),
                  size : size
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    localStorage.setItem('currentPageJobs', page);

                    if (response.jobs && response.jobs.length > 0) {
                        window.jobsState = {
                            all: response.jobs,
                            filtered: response.jobs,
                            total: response.total
                        };
                        $('#allJobList').html(renderJobsClientSide(response.jobs));
                    } else if (response.html) {
                        window.jobsState = {
                            all: [],
                            filtered: [],
                            total: response.total || 0
                        };
                        $('#allJobList').html(response.html);
                    } else {
                        window.jobsState = {
                            all: [],
                            filtered: [],
                            total: response.total || 0
                        };
                        $('#allJobList').html(renderJobsClientSide([]));
                    }
                    $('#all_jobs_count').html(response.total);
                    $('#pagination').html(response.pagination);
                    renderAppliedFiltersCard();
                },
                error: function(xhr) {
                    $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');

                    var msg, icon;
                    if (xhr.status === 0 || !navigator.onLine) {
                        msg  = 'You are currently offline or the server is unreachable. Please check your connection and try again.';
                        icon = 'warning';
                    } else if (xhr.status === 503 || xhr.status === 500) {
                        msg  = 'The search service is temporarily unavailable. Please try again in a moment.';
                        icon = 'error';
                    } else {
                        msg  = 'Could not load jobs (HTTP ' + xhr.status + '). Please refresh the page.';
                        icon = 'error';
                    }

                    $('#allJobList').html(
                        '<div style="text-align:center;padding:40px 20px;font-family:Poppins,sans-serif;">' +
                        '<i class="fa fa-exclamation-triangle" style="font-size:32px;color:#f64e60;margin-bottom:12px;display:block;"></i>' +
                        '<p style="font-size:13px;color:#5e6278;margin:0;">' + msg + '</p>' +
                        '<button onclick="fetchCandidates()" class="btn btn-sm btn-primary mt-4" style="font-size:12px;">Retry</button>' +
                        '</div>'
                    );
                    $('#pagination').html('');
                    syncJobsResultSummary(0, 0);
                }
            });
        }

    



    var UrlMailJobId = "{{$UrlMailJobId}}";
    if(UrlMailJobId){

      setTimeout(() => {
         $('#kt_activitiesJob_toggle').trigger('click');		
        setJobsDrawerLoading(null);
      

        var id    		=	UrlMailJobId;
        var url   		=	"{{ route('service_company.jobs.viewmore',':id') }}?viewmore=viewmore";
        url     		=	url.replace(':id', id);
        $.ajax({
                type  		: 'GET',
                url   		: url,
                beforeSend	: function() {
                                      setJobsDrawerLoading(null);
                          },
                success 	: function (response) {
                    $('#kt_activitiesJob_body').html(response.view);	
                }
              });
      }, 200);
       
    }

    $(document).on('click', '.jd-drawer-close-btn, #kt_activitiesJob_close', function(e) {
        e.preventDefault();
        var drawerEl = this.closest('[data-kt-drawer="true"]') || document.getElementById('kt_activitiesJob');
        if (drawerEl) {
            if (typeof KTDrawer !== 'undefined') {
                var inst = KTDrawer.getInstance(drawerEl);
                if (!inst && typeof KTDrawer === 'function') {
                    inst = new KTDrawer(drawerEl);
                }
                if (inst && typeof inst.hide === 'function') {
                    inst.hide();
                    return;
                }
            }
            drawerEl.classList.remove('drawer-on');
            document.querySelectorAll('.drawer-overlay').forEach(function(el) { el.remove(); });
            document.body.removeAttribute('data-kt-drawer-activities');
            document.body.style.overflow = '';
        }
    });

</script>


@endpush
