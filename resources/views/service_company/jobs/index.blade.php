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
@media only screen and (min-width: 992px){
  /* Intent: size the Total Jobs drawer at 80% of the content canvas, not the left sidebar. */
  #kt_activitiesJob {
    --jero-service-aside-width: 225px;
    width: min(80vw, calc((100vw - var(--jero-service-aside-width)) * .8)) !important;
    max-width: calc(100vw - var(--jero-service-aside-width)) !important;
  }
  body.aside-minimize #kt_activitiesJob { --jero-service-aside-width: 75px; }
}
@media only screen and (max-width: 991.98px){
  #kt_activitiesJob {
    width: 100vw !important;
    max-width: 100vw !important;
  }
}
@media only screen and (min-width: 992px){
  #kt_activitiesRecruiter {
    width: min(1320px, 96vw) !important;
    max-width: 96vw !important;
  }
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
#kt_content.flex-column-fluid {
  flex: 0 0 auto !important;
  padding-bottom: 0 !important;
}

/* Multi-City Work Location Badge & Enterprise Popover (0% Server Load) */
.loc-row {
  display: flex !important;
  align-items: center !important;
  gap: 4px !important;
  font-size: 11.5px !important;
  color: var(--text-mid, #5e6278) !important;
  white-space: nowrap !important;
}
.loc-row .city-name-text {
  max-width: 68px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  display: inline-block;
  vertical-align: middle;
}
.loc-more-badge-wrap {
  position: relative;
  display: inline-flex;
  align-items: center;
}
.loc-more-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: #f1f5f9;
  color: #009ef7;
  border: 1px solid #c2e7ff;
  border-radius: 9px;
  font-size: 9.5px;
  font-weight: 700;
  padding: 0 4px;
  line-height: 13px;
  cursor: pointer;
  vertical-align: middle;
  transition: all 0.15s ease-in-out;
  flex-shrink: 0;
}
.loc-more-badge:hover,
.loc-more-badge-wrap:hover .loc-more-badge {
  background: #009ef7;
  color: #ffffff;
  border-color: #009ef7;
  box-shadow: 0 1px 3px rgba(0, 158, 247, 0.35);
}
.loc-popover-card {
  position: absolute;
  bottom: calc(100% + 7px);
  left: 50%;
  transform: translateX(-50%);
  background: #1e1e2d;
  color: #ffffff;
  border-radius: 6px;
  padding: 7px 10px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.35);
  z-index: 1050;
  min-width: 120px;
  max-width: 210px;
  pointer-events: none;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.15s ease-in-out, visibility 0.15s ease-in-out;
}
.loc-more-badge-wrap:hover .loc-popover-card {
  opacity: 1;
  visibility: visible;
}
.loc-popover-card::after {
  content: "";
  position: absolute;
  top: 100%;
  left: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: #1e1e2d transparent transparent transparent;
}
.loc-popover-header {
  font-size: 9.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.4px;
  color: #009ef7;
  border-bottom: 1px solid rgba(255, 255, 255, 0.12);
  padding-bottom: 3px;
  margin-bottom: 4px;
  white-space: nowrap;
}
.loc-popover-body {
  display: flex;
  flex-direction: column;
  gap: 3px;
  max-height: 140px;
  overflow-y: auto;
}
.loc-popover-item {
  font-size: 11px;
  font-weight: 500;
  color: #f1f1f2;
  display: flex;
  align-items: center;
  gap: 5px;
  white-space: nowrap;
}
.loc-popover-item i {
  font-size: 8.5px;
  color: #009ef7;
  flex-shrink: 0;
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
      
        $currentKpi = request('kpi', 'all');
        $tabactive = [
         'allRequisition_active'=> ($currentKpi === 'all' || empty($currentKpi)) ? 'tabactive' : '',
         'allRequisition_url'=> 'javascript:void(0);',
         'allRequisition_id'=>'top_card_all_jobs_count',
         'customRequisition_active'=> ($currentKpi === 'internal') ? 'tabactive' : '',
         'customRequisition_url'=> 'javascript:void(0);',
         'customRequisition_id'=>'top_card_custom_requisition_count',
         'clientRequisition_active'=> ($currentKpi === 'direct') ? 'tabactive' : '',
         'clientRequisition_url'=> 'javascript:void(0);',
         'clientRequisition_id'=>'top_card_client_requisition_count',
         'jobRequest_active'=> ($currentKpi === 'job_request' || $currentKpi === 'request') ? 'tabactive' : '',
         'jobRequest_url'=> 'javascript:void(0);',
         'jobRequest_id'=>'top_card_job_request_count',
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
<div class="content d-flex flex-column flex-column-fluid pt-4 kt_content_job" id="kt_content" style="margin-top: -90px;">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">

      <!-- CONTENT -->
        <div class="content">        

          <!-- HERO -->
           @include('sections.service_company.jobHeader',['tabactive'=>array_merge($tabactive, ['showTabs' => false])])

          <!-- FILTER BAR -->
          @include('sections.service_company.jobFilterBar',['searchbar'=>$searchbar])

          <!-- STATUS TABS -->
          <div class="jh-tabs-card">
            <a href="#" class="jh-tab jh-tab-active" data-status="all" onclick="filterJobsByStatus('all'); return false;">
                <span class="jh-tab-icon jh-tab-icon-blue">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                    </svg>
                </span>
                All Statuses
                <span class="jh-badge jh-badge-blue">{{ $tabactive['allTabCount'] ?? $tabactive['allRequisition'] }}</span>
            </a>
            <div class="jh-tab-divider"></div>
            <a href="#" class="jh-tab" data-status="active" onclick="filterJobsByStatus('active'); return false;">
                <span class="jh-tab-icon jh-tab-icon-green">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </span>
                Hiring
                <span class="jh-badge jh-badge-green">{{ $tabactive['hiringCount'] ?? 0 }}</span>
            </a>
            <div class="jh-tab-divider"></div>
            <a href="#" class="jh-tab" data-status="hold" onclick="filterJobsByStatus('hold'); return false;">
                <span class="jh-tab-icon jh-tab-icon-orange">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="10" y1="15" x2="10" y2="9"/><line x1="14" y1="15" x2="14" y2="9"/>
                    </svg>
                </span>
                On Hold
                <span class="jh-badge jh-badge-orange">{{ $tabactive['holdCount'] ?? 0 }}</span>
            </a>
            <div class="jh-tab-divider"></div>
            <a href="#" class="jh-tab" data-status="inactive" onclick="filterJobsByStatus('inactive'); return false;">
                <span class="jh-tab-icon jh-tab-icon-red">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </span>
                Closed
                <span class="jh-badge jh-badge-red">{{ $tabactive['closedCount'] ?? 0 }}</span>
            </a>
            <div class="jh-tab-divider"></div>
            <a href="#" class="jh-tab" data-status="removed" onclick="filterJobsByStatus('removed'); return false;">
                <span class="jh-tab-icon" style="background:#f1f1f4;color:#7e8299;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                    </svg>
                </span>
                Removed
                <span class="jh-badge" style="background:#f1f1f4;color:#7e8299;">{{ $tabactive['removedCount'] ?? 0 }}</span>
            </a>
          </div>

          <!-- TABLE -->
          @include('sections.service_company.jobListTable', ['listId' => 'allJobList'])
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
  <div class="card shadow-none bg-custom-bar rounded-0 w-100">
   {{-- header removed --}}
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
<div id="kt_activitiesRecruiter" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1320px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesRecruiter_toggle" data-kt-drawer-close="#kt_activitiesRecruiter_close">
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
       // Phase 2: Client Caching & IndexedDB Integration (Rule 6 Multi-Tenant Isolation)
    const JeroJobsLocalStore = (function() {
        const DB_NAME = 'jero_service_jobs_db';
        const DB_VERSION = 1;
        const CURRENT_TENANT_ID = {{ (int) (auth()->user()->company_id ?? 0) }};
        const CURRENT_USER_ID = {{ (int) (auth()->user()->id ?? 0) }};
        const JOBS_CACHE_TTL_MS = 15 * 60 * 1000; // 15 minutes TTL

        let dbPromise = null;

        function getDB() {
            if (!window.indexedDB) return Promise.resolve(null);
            if (dbPromise) return dbPromise;

            dbPromise = new Promise((resolve) => {
                try {
                    const req = window.indexedDB.open(DB_NAME, DB_VERSION);
                    req.onupgradeneeded = function(e) {
                        const db = e.target.result;
                        if (!db.objectStoreNames.contains('jobs_cache')) {
                            db.createObjectStore('jobs_cache', { keyPath: 'cache_key' });
                        }
                        if (!db.objectStoreNames.contains('meta')) {
                            db.createObjectStore('meta', { keyPath: 'id' });
                        }
                    };
                    req.onsuccess = function(e) {
                        const db = e.target.result;
                        try {
                            const tx = db.transaction(['meta'], 'readonly');
                            const metaStore = tx.objectStore('meta');
                            const metaReq = metaStore.get('current_context');
                            metaReq.onsuccess = function() {
                                const meta = metaReq.result;
                                if (meta && (meta.tenant_id !== CURRENT_TENANT_ID || meta.user_id !== CURRENT_USER_ID)) {
                                    // Tenant / User mismatch: purge immediately to prevent cross-tenant data leaks
                                    const wipeTx = db.transaction(['jobs_cache', 'meta'], 'readwrite');
                                    wipeTx.objectStore('jobs_cache').clear();
                                    wipeTx.objectStore('meta').put({ id: 'current_context', tenant_id: CURRENT_TENANT_ID, user_id: CURRENT_USER_ID, updated_at: Date.now() });
                                } else if (!meta) {
                                    const setTx = db.transaction(['meta'], 'readwrite');
                                    setTx.objectStore('meta').put({ id: 'current_context', tenant_id: CURRENT_TENANT_ID, user_id: CURRENT_USER_ID, updated_at: Date.now() });
                                }
                            };
                        } catch (err) {
                            console.warn('JeroJobsLocalStore tenancy check error:', err);
                        }
                        resolve(db);
                    };
                    req.onerror = function() { resolve(null); };
                } catch (err) {
                    resolve(null);
                }
            });

            return dbPromise;
        }

        return {
            get: async function(key) {
                const db = await getDB();
                if (!db || !key) return null;
                return new Promise((resolve) => {
                    try {
                        const tx = db.transaction(['jobs_cache'], 'readonly');
                        const store = tx.objectStore('jobs_cache');
                        const req = store.get(key);
                        req.onsuccess = function() {
                            const res = req.result;
                            if (res && (Date.now() - res.timestamp < JOBS_CACHE_TTL_MS)) {
                                resolve(res.payload);
                            } else {
                                resolve(null);
                            }
                        };
                        req.onerror = function() { resolve(null); };
                    } catch (e) {
                        resolve(null);
                    }
                });
            },
            set: async function(key, payload) {
                const db = await getDB();
                if (!db || !key || !payload) return;
                try {
                    const tx = db.transaction(['jobs_cache'], 'readwrite');
                    const store = tx.objectStore('jobs_cache');
                    store.put({
                        cache_key: key,
                        payload: payload,
                        timestamp: Date.now()
                    });
                } catch (e) {
                    // Non-fatal if storage quota exceeded
                }
            },
            purge: async function() {
                const db = await getDB();
                if (!db) return;
                try {
                    const tx = db.transaction(['jobs_cache'], 'readwrite');
                    tx.objectStore('jobs_cache').clear();
                } catch (e) {
                    // Non-fatal
                }
            }
        };
    })();
    window.JeroJobsLocalStore = JeroJobsLocalStore;
// In-memory cache for loaded job drawer HTML to eliminate duplicate network fetches
    window.jobDrawerCache = window.jobDrawerCache || {};
    window.lastAuthoritativeSearch = '';

    function executeJobSearch(page = 1) {
        localStorage.setItem('currentPageJobs', page);
        var currentSearch = ($('.inputJobSearch').val() || $('#inputSearch').val() || '').trim();
        window.lastAuthoritativeSearch = currentSearch;
        fetchCandidates(page);
    }

    // Debounced search input handler: filters loaded jobs in browser RAM at 250ms with 0 server requests
    var jobFilterDebounceTimer = null;
    $(document).on('input', '.inputJobSearch, #inputSearch', function() {
        var val = $(this).val();
        $('.inputJobSearch, #inputSearch').not(this).val(val);
        // Instant visual feedback for currently visible items in RAM
        if (window.jobsState && window.jobsState.all && window.jobsState.all.length > 0) {
            filtersdata();
        }

        clearTimeout(jobFilterDebounceTimer);
        jobFilterDebounceTimer = setTimeout(function() {
            var trimmed = val.trim();
            if (trimmed !== window.lastAuthoritativeSearch) {
                executeJobSearch(1);
            }
        }, 350);
    });

    // Enter key handler for authoritative search across full dataset
    $(document).on('keydown', '.inputJobSearch, #inputSearch', function(e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
            clearTimeout(jobFilterDebounceTimer);
            executeJobSearch(1);
        }
    });

    // Single click handler for Search button: authoritative search
    $(document).on('click', '#jobSearchBtn', function(e) {
        e.preventDefault();
        clearTimeout(jobFilterDebounceTimer);
        executeJobSearch(1);
    });

    $(document).on('change', '#status,#hiringtype,#employment_type,#experience_filter', function() {
        if (this.id === 'status') {
            // Sync the tab row to match the selected status
            var val = $(this).val();
            var tabStatus = (val === 'inactive') ? 'inactive' : val;
            $('.jh-tab').removeClass('jh-tab-active');
            $('.jh-tab[data-status="' + tabStatus + '"]').addClass('jh-tab-active');
        }
        syncJobCustomSelect(this.id);
        localStorage.setItem('currentPageJobs', 1);
        fetchCandidates(1);
    });

    window.currentKpiFilter = (new URLSearchParams(window.location.search)).get('kpi') || 'all';

    function updateKpiViewToggle(kpiType) {
        if (kpiType === 'job_request' || kpiType === 'request') {
            $('.jh-filter-bar').hide();
            $('.jh-tabs-card').hide();
            $('.pipeline-legend-card').hide();
            $('.table-header-standard').hide();
            $('.table-header-request').show();
        } else {
            $('.jh-filter-bar').show();
            $('.jh-tabs-card').show();
            $('.pipeline-legend-card').show();
            $('.table-header-standard').show();
            $('.table-header-request').hide();
        }
    }

    function filterJobsByKpi(kpiType) {
        window.currentKpiFilter = kpiType;
        $('.jh-card').removeClass('jh-card-active');
        $(`.jh-card[data-kpi="${kpiType}"]`).addClass('jh-card-active');

        $('.jh-radio-input').prop('checked', false);
        $(`.jh-card[data-kpi="${kpiType}"] .jh-radio-input`).prop('checked', true);

        // Reflect current KPI in address bar without full page reload
        try {
            var currentUrl = new URL(window.location.href);
            if (kpiType && kpiType !== 'all') {
                currentUrl.searchParams.set('kpi', kpiType);
            } else {
                currentUrl.searchParams.delete('kpi');
            }
            window.history.replaceState({}, '', currentUrl.pathname + currentUrl.search + currentUrl.hash);
        } catch (e) {}

        // Sync sidebar active state between CRM -> Job Request and Hiring -> Job Tracker
        if (kpiType === 'job_request' || kpiType === 'request') {
            $('a[href*="kpi=job_request"], a[href*="job_request"]').addClass('active');
            $('a[href="{{ route('service_company.jobs.index') }}"]').not('[href*="kpi="]').removeClass('active');
        } else {
            $('a[href*="kpi=job_request"], a[href*="job_request"]').removeClass('active');
            $('a[href="{{ route('service_company.jobs.index') }}"]').not('[href*="kpi="]').addClass('active');
        }

        updateKpiViewToggle(kpiType);

        localStorage.setItem('currentPageJobs', 1);
        fetchCandidates(1);
    }



    // Called from the tab row in jobHeader — filters table and marks tab active
    function filterJobsByStatus(status) {
        $('#status').val(status);
        syncJobCustomSelect('status');
        $('.jh-tab').removeClass('jh-tab-active');
        $('.jh-tab[data-status="' + status + '"]').addClass('jh-tab-active');
        localStorage.setItem('currentPageJobs', 1);
        fetchCandidates(1);
    }

    function syncJobCustomSelect(selectId) {
        var $select = $('#' + selectId);
        var value = $select.val();
        var label = $select.find('option:selected').text();
        var $wrap = $('.jh-select-trigger[data-select-target="' + selectId + '"]').closest('.jh-custom-select');
        $wrap.find('.jh-select-value').text(label);
        $wrap.find('.jh-select-option').removeClass('is-selected');
        $wrap.find('.jh-select-option[data-value="' + value + '"]').addClass('is-selected');
    }

    $(document).on('click', '.jh-select-trigger', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $wrap = $(this).closest('.jh-custom-select');
        $('.jh-custom-select').not($wrap).removeClass('is-open').find('.jh-select-trigger').attr('aria-expanded', 'false');
        $wrap.toggleClass('is-open');
        $(this).attr('aria-expanded', $wrap.hasClass('is-open') ? 'true' : 'false');
    });

    $(document).on('click', '.jh-select-option', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $option = $(this);
        var $wrap = $option.closest('.jh-custom-select');
        var selectId = $wrap.find('.jh-select-trigger').data('select-target');
        $('#' + selectId).val($option.data('value'));
        syncJobCustomSelect(selectId);
        $wrap.removeClass('is-open').find('.jh-select-trigger').attr('aria-expanded', 'false');
        if (selectId === 'status') {
            var val = $option.data('value');
            $('.jh-tab').removeClass('jh-tab-active');
            $('.jh-tab[data-status="' + val + '"]').addClass('jh-tab-active');
        }
        localStorage.setItem('currentPageJobs', 1);
        filtersdata();
    });

    $(document).on('click', function() {
        $('.jh-custom-select').removeClass('is-open').find('.jh-select-trigger').attr('aria-expanded', 'false');
    });

    $(document).on('change', '.jh-select-native', function() {
        syncJobCustomSelect(this.id);
    });

    syncJobCustomSelect('status');
    syncJobCustomSelect('employment_type');
    syncJobCustomSelect('experience_filter');
    syncJobCustomSelect('hiringtype');
    
   
   
    $(document).on('click', '.show-detail', function ()
   	{
      // alert("kkss");
   		$('#kt_activitiesJob_toggle').trigger('click');		
      
      // localStorage is already kept current by fetchCandidates; no DOM read needed
      var currentPageJobs = parseInt(localStorage.getItem('currentPageJobs')) || 1;
      localStorage.setItem('currentPageJobs', currentPageJobs);

   		var id = $(this).data('row-id');
        var cacheKey = 'job_' + id + '_regular';
        if (window.jobDrawerCache && window.jobDrawerCache[cacheKey]) {
            $('#kt_activitiesJob_body').html(window.jobDrawerCache[cacheKey]);
            return;
        }
   		var url = "{{ route('service_company.jobs.viewmore',':id') }}?viewmore=viewmore";
   		url = url.replace(':id', id);   		var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  		: 'GET',
   						url   		: url,
   						 beforeSend	: function() {
                                     $('#kt_activitiesJob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success 	: function (response) {
									window.jobDrawerCache[cacheKey] = response.view;
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
   		var id = $(this).data('row-id');
        var cacheKey = 'job_' + id + '_client';
        if (window.jobDrawerCache && window.jobDrawerCache[cacheKey]) {
            $('#kt_activitiesJob_body').html(window.jobDrawerCache[cacheKey]);
            return;
        }
   		var url = "{{ route('service_company.jobs.viewmore',':id') }}?viewmore=clientviewmore";
   		url = url.replace(':id', id);   		var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  		: 'GET',
   						url   		: url,
   						 beforeSend	: function() {
                                     $('#kt_activitiesJob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success 	: function (response) {
									window.jobDrawerCache[cacheKey] = response.view;
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
	
	

	
   
      var _isUpdatingStatusLock = false;
   function updateJobStatus(id, statusName)
   {
     if (_isUpdatingStatusLock) return false;
     _isUpdatingStatusLock = true;

     var url = "{{ route('service_company.jobs.updatestatus',':id') }}";
     url = url.replace(':id', id);
     var token = "{{ csrf_token() }}";
     var mutationId = 'mut_st_' + id + '_' + Date.now();

     $.easyAjax({
         type: 'POST',
         url: url,
         data: {
             '_token': token, 
             '_method': 'POST', 
             'id': id, 
             'statusName': statusName,
             'client_mutation_id': mutationId
         },
         success: function (response){
             _isUpdatingStatusLock = false;
             window.jobDrawerCache = {};
             if (window.JeroJobsLocalStore) { JeroJobsLocalStore.purge(); }
             fetchCandidates();
         },
         error: function (xhr) {
             _isUpdatingStatusLock = false;
             if (typeof showToastr === 'function') {
                 showToastr('error', 'Status update could not be completed. Please try again.', 'Action Failed');
             }
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

  // Intent: load the existing agencies drawer for dynamically-rendered Add Agency controls.
  $(document).off('click.spartnerListing', '.spartner-listing').on('click.spartnerListing', '.spartner-listing', function(e){
        e.preventDefault();
        e.stopPropagation();
        if (e.stopImmediatePropagation) {
            e.stopImmediatePropagation();
        }

        var $btn = $(this);
        if ($btn.data('in-flight')) return false;

        $('#kt_activitiesRecruiter_toggle').trigger('click');
        var id = $btn.data('row-id');

        if (window.currentActiveDrawerJobId !== id) {
            window.currentActiveDrawerJobId = id;
            if (window.selectedAgencyIds) {
                window.selectedAgencyIds.clear();
            }
        }

        var url = "{{ route('service_company.jobs.spartnerlisting',':id') }}".replace(':id', id);
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        var loaderHtml = '<div style="padding:40px;text-align:center;font-family:Poppins,sans-serif;"><img src="'+baseurl+'" height="35px" style="margin-bottom:10px;"><div style="font-size:13px;color:#5e6278;font-weight:500;">Loading agencies...</div></div>';
        var errorHtml = '<div style="padding:24px;text-align:center;font-family:Poppins,sans-serif;font-size:13px;color:#f64e60;">Could not load agencies. Please try again.</div>';

        window.jobDrawerCache = window.jobDrawerCache || {};
        var cacheKey = 'spartner_' + id;
        var hasCachedDrawer = !!window.jobDrawerCache[cacheKey];

        if (hasCachedDrawer) {
            // Instant 0ms paint from user RAM
            $('#kt_activitiesRecruiter_body').html(window.jobDrawerCache[cacheKey]);
        } else {
            $('#kt_activitiesRecruiter_body').html(loaderHtml);
        }

        $btn.data('in-flight', true);

        // Cancel previous pending drawer AJAX if active
        if (window.pendingSpartnerXhr && typeof window.pendingSpartnerXhr.abort === 'function') {
            try { window.pendingSpartnerXhr.abort(); } catch(err) {}
        }

        window.pendingSpartnerXhr = $.ajax({
            type: 'GET',
            url: url,
            cache: false,
            headers: {
                'X-Has-Representation': hasCachedDrawer ? '1' : '0'
            },
            success: function (response) {
                if (response && response.view) {
                    window.jobDrawerCache[cacheKey] = response.view;
                    $('#kt_activitiesRecruiter_body').html(response.view);
                } else if (hasCachedDrawer) {
                    // HTTP 304 Not Modified: representation already rendered from user RAM
                } else {
                    // Safe fallback if server sent empty response unexpectedly
                    $.getJSON(url + '?_force=1', function(fresh) {
                        if (fresh && fresh.view) {
                            window.jobDrawerCache[cacheKey] = fresh.view;
                            $('#kt_activitiesRecruiter_body').html(fresh.view);
                        } else {
                            $('#kt_activitiesRecruiter_body').html(errorHtml);
                        }
                    }).fail(function() {
                        $('#kt_activitiesRecruiter_body').html(errorHtml);
                    });
                }
            },
            error: function (xhr, status) {
                if (status !== 'abort' && !hasCachedDrawer) {
                    $('#kt_activitiesRecruiter_body').html(errorHtml);
                }
            },
            complete: function() {
                $btn.data('in-flight', false);
                window.pendingSpartnerXhr = null;
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

    // Accept Job Request handler
    $(document).on('click', '.request_approval', function (e) {
        e.preventDefault();
        var $btn = $(this);
        if ($btn.prop('disabled')) return;

        var jobId = $btn.attr('data-row-id') || $btn.data('row-id');
        var recommendation_relation_id = $btn.attr('data-row-recommendation_relation_id') || $btn.data('row-recommendation_relation_id');
        var status = $btn.attr('data-row-status') || $btn.data('row-status') || 'accept';
        var originalHtml = $btn.html();

        $btn.prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Please wait...');

        $.ajax({
            type: 'GET',
            url: "{{ route('service_company.jobs.requestapproval') }}",
            data: {
                jobId: jobId,
                rec_relation_id: recommendation_relation_id,
                status: status
            },
            success: function (response) {
                if (response.status === 'success' || response.status === 'accept') {
                    if (window.JeroJobsLocalStore) {
                        window.JeroJobsLocalStore.purge();
                    }
                    if (response.dataCnt) {
                        $('#top_card_all_jobs_count').text(response.dataCnt.allRequisition ?? response.dataCnt.allTabCount ?? 0);
                        $('#top_card_client_requisition_count').text(response.dataCnt.clientRequisition ?? 0);
                        $('#top_card_custom_requisition_count').text(response.dataCnt.customRequisition ?? 0);
                        $('#top_card_job_request_count').text(response.dataCnt.jobRequest ?? 0);
                    }
                    Swal.fire({
                        text: 'Job request accepted successfully.',
                        icon: 'success',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok, got it!',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                    fetchCandidates(1);
                } else if (response.status === 'ignoreClient') {
                    $btn.prop('disabled', false).html(originalHtml);
                    Swal.fire({
                        text: 'Client details could not be verified for this company.',
                        icon: 'warning',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                } else {
                    $btn.prop('disabled', false).html(originalHtml);
                    Swal.fire({
                        text: response.message || 'Unable to accept job request.',
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                }
            },
            error: function () {
                $btn.prop('disabled', false).html(originalHtml);
                Swal.fire({
                    text: 'Something went wrong while accepting the job request. Please try again.',
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'Ok',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
            }
        });
    });

    // Reject Job Request handler
    $(document).on('click', '.request_reject', function (e) {
        e.preventDefault();
        var $btn = $(this);
        if ($btn.prop('disabled')) return;

        var jobId = $btn.attr('data-row-id') || $btn.data('row-id');
        var recommendation_relation_id = $btn.attr('data-row-recommendation_relation_id') || $btn.data('row-recommendation_relation_id');
        var status = $btn.attr('data-row-status') || $btn.data('row-status') || 'ignore';

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-light'
            },
            buttonsStyling: false
        });

        swalWithBootstrapButtons.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Reject',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                var originalHtml = $btn.html();
                $btn.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Please wait...');

                $.ajax({
                    type: 'GET',
                    url: "{{ route('service_company.jobs.requestapproval') }}",
                    data: {
                        jobId: jobId,
                        rec_relation_id: recommendation_relation_id,
                        status: status
                    },
                    success: function (response) {
                        if (window.JeroJobsLocalStore) {
                            window.JeroJobsLocalStore.purge();
                        }
                        if (response.dataCnt) {
                            $('#top_card_all_jobs_count').text(response.dataCnt.allRequisition ?? response.dataCnt.allTabCount ?? 0);
                            $('#top_card_client_requisition_count').text(response.dataCnt.clientRequisition ?? 0);
                            $('#top_card_custom_requisition_count').text(response.dataCnt.customRequisition ?? 0);
                            $('#top_card_job_request_count').text(response.dataCnt.jobRequest ?? 0);
                        }
                        swalWithBootstrapButtons.fire('Rejected!', 'Selected request has been rejected.', 'success');
                        fetchCandidates(1);
                    },
                    error: function () {
                        $btn.prop('disabled', false).html(originalHtml);
                        swalWithBootstrapButtons.fire('Error', 'Something went wrong while rejecting the request.', 'error');
                    }
                });
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

            if (window.currentKpiFilter && window.currentKpiFilter !== 'all') {
                $('.jh-card').removeClass('jh-card-active');
                $(`.jh-card[data-kpi="${window.currentKpiFilter}"]`).addClass('jh-card-active');
                $('.jh-radio-input').prop('checked', false);
                $(`.jh-card[data-kpi="${window.currentKpiFilter}"] .jh-radio-input`).prop('checked', true);
            }

            updateKpiViewToggle(window.currentKpiFilter);
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

                fetchCandidates(page);
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

        function formatCompanyName(value) {
            return value ? String(value).toUpperCase() : '';
        }

        function formatJobPositionName(value) {
            if (!value) return '';
            var str = String(value).trim();
            if (str === '--------' || /^[-–—\s]+$/.test(str)) return '--------';
            return str.replace(/\b[a-z]/g, function(letter) {
                return letter.toUpperCase();
            });
        }

        function formatSalaryMode(value) {
            if (!value) return '';
            var text = String(value).replace('/', '').trim();
            var normalized = text.toLowerCase();
            if (normalized.indexOf('ann') !== -1 || normalized === 'yearly' || normalized === 'annual') return 'Annum';
            if (normalized.indexOf('month') !== -1) return 'Monthly';
            if (normalized.indexOf('hour') !== -1) return 'Hourly';
            return text;
        }

        function getEmploymentTypeMeta(value, rawValue) {
            var text = String(value || '').toLowerCase();
            var raw = String(rawValue || '');
            if (raw === '1' || text.indexOf('contract') !== -1) {
                return { icon: 'fa-file-signature', tone: 'contract' };
            }
            if (raw === '2' || text.indexOf('freelance') !== -1) {
                return { icon: 'fa-user-clock', tone: 'freelance' };
            }
            if (raw === '3' || text.indexOf('part') !== -1) {
                return { icon: 'fa-clock', tone: 'parttime' };
            }
            return { icon: 'fa-briefcase', tone: 'fulltime' };
        }

        function renderJobsClientSide(jobs) {
            if (!jobs || jobs.length === 0) {
                return '<div style="text-align:center;padding:40px 20px;font-family:Poppins,sans-serif;color:#7e8299;">' +
                       '<i class="fa fa-briefcase" style="font-size:32px;margin-bottom:12px;display:block;"></i>' +
                       '<p style="font-size:13px;margin:0;">No jobs found matching your criteria.</p></div>';
            }

            var html = '';
            jobs.forEach(function(job) {
                var profileDetailClass = job.is_direct_client ? 'client-show-detail' : 'show-detail';
                var directClientAttr = job.is_direct_client ? '1' : '0';
                var displayTitle = formatJobPositionName(job.title);
                var displayClientName = (job.is_direct_client && job.parent_company_name) ? formatCompanyName(job.parent_company_name) : formatCompanyName(job.clients_name);
                var displayAgencyName = formatCompanyName(job.agency_company_name);
                var displayCity = formatJobPositionName(job.city);
                var displayHiringManager = formatJobPositionName(job.hiring_manager);
                var displaySalaryMode = formatSalaryMode(job.min_sal_mode);
                var salaryModeText = displaySalaryMode ? ' /' + displaySalaryMode : '';
                var employmentMeta = getEmploymentTypeMeta(job.employment_type, job.employment_type_raw);
                var statusIcon = job.status === 'Hiring' ? 'fa-user-group' : (job.status === 'Hold' ? 'fa-pause' : 'fa-box-archive');
                var statusClass = job.status_raw === 'active' ? 'active-bg' : (job.status_raw === 'hold' ? 'hold-bg' : 'closed-bg');
                if (job.status === 'Removed') statusClass = 'closed-bg';

                var ulink = job.status === 'Removed'
                    ? '<span class="badge-hiring ' + statusClass + '">' + job.status + '</span>'
                    : '<span data-row-id="' + job.id + '" data-direct-client="' + directClientAttr + '" class="badge-hiring ' + statusClass + ' status_stage"><i class="fas ' + statusIcon + '"></i> ' + job.status + '</span>';

                var logoHtml = job.logo_path
                    ? '<img src="' + job.logo_path + '" alt="Company Logo" class="border-radius-5 w-100">'
                    : job.initial_letter;

                var skillsHtml = '';
                if (job.skills && job.skills.length > 0) {
                    skillsHtml = job.skills.map(function(s) {
                        return '<span class="skill-tag">' + s + '</span>';
                    }).join(' ');
                }
                var addedDateHtml = job.added_date
                    ? '<span class="job-date-tag"><svg fill="none" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8"/></svg>' + job.added_date + '</span>'
                    : '';

                var teamHtml = '';
                if (job.is_direct_client) {
                    if (job.partners_first_name) {
                        teamHtml += '<div class="hm-name team-listing" data-row-id="' + job.id + '">' + job.partners_first_name.substring(0, 10) + '</div>';
                    }
                    var pCount = job.partners_count > 1 ? '+' + (job.partners_count - 1) : job.partners_count;
                    teamHtml += '<div class="action-count">' + pCount + ' Members</div>';
                    teamHtml += '<div class="action-more team-listing" data-row-id="' + job.id + '">+ Add More</div>';
                } else {
                    if (job.partners_first_name) {
                        teamHtml += '<div class="hm-name hm-listing" data-row-id="' + job.id + '">' + job.partners_first_name.substring(0, 10) + '</div>';
                    }
                    var pCount = job.partners_count > 1 ? '+' + (job.partners_count - 1) : job.partners_count;
                    teamHtml += '<div class="action-count">' + pCount + ' Members</div>';
                    teamHtml += '<div class="action-more hm-listing" data-row-id="' + job.id + '">+ Add More</div>';
                }

                var agencyHtml = '';
                if (job.is_direct_client) {
                    agencyHtml += '<div class="hm-name">' + (displayAgencyName ? displayAgencyName.substring(0, 30) : '') + '</div>';
                } else {
                    if (job.agency_count > 0) {
                        agencyHtml += '<div class="hm-name">' + job.agency_count + ' Agency</div>';
                        agencyHtml += '<div class="action-count">15 Matching</div>';
                        agencyHtml += '<div data-row-id="' + job.id + '" class="action-more spartner-listing">+ Add More</div>';
                    } else {
                        agencyHtml += '<button class="btn-action spartner-listing" data-row-id="' + job.id + '"><svg fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>Add Agency</button>';
                        agencyHtml += '<div class="action-count">15 Matching</div>';
                    }
                }

                var apptrackerBase = job.is_direct_client
                    ? "{{ url('/service_company/apptracker/clientapp') }}/" + job.encrypted_id + "/"
                    : "{{ url('/service_company/apptracker') }}/" + job.encrypted_id + "/";

                var allAppUrl = job.is_direct_client
                    ? "{{ url('/service_company/apptracker/clientapp') }}/" + job.encrypted_id + "/all"
                    : "{{ url('/service_company/apptracker') }}/" + job.encrypted_id + "/all";

                var stageLabels = [
                    { id: 1, key: 's-sou', label: 'SOU', title: 'Sourcing', compact: 'Source' },
                    { id: 2, key: 's-scr', label: 'SCR', title: 'Screening', compact: 'Screen' },
                    { id: 3, key: 's-eva', label: 'EVA', title: 'Evaluation', compact: 'Eval' },
                    { id: 4, key: 's-neg', label: 'NEG', title: 'Negotiation', compact: 'Neg' },
                    { id: 5, key: 's-off', label: 'OFF', title: 'Offer', compact: 'Offer' },
                    { id: 6, key: 's-joi', label: 'JOI', title: 'Joining', compact: 'Join' },
                    { id: 7, key: 's-rej', label: 'REJ', title: 'Rejected', compact: 'Reject' }
                ];

                var pipelineHtml = '<div class="pipeline-stages">';
                pipelineHtml += '<div class="stage" title="INW - In Review"><div class="stage-label">INW</div><div class="stage-badge s-inw"><a href="' + allAppUrl + '" class="s-inw fw-bolder fs-6">' + (job.inreview_counter || 0) + '</a></div></div>';

                stageLabels.forEach(function(stg) {
                    var count = (job.stage_counts && job.stage_counts[stg.id]) ? job.stage_counts[stg.id] : 0;
                    var stgUrl = apptrackerBase + stg.id;
                    var countClass = count > 0 ? (stg.id === 1 ? 'text-dark fw-bolder fs-6' : stg.key + ' fs-6') : stg.key + ' fs-6';
                    pipelineHtml += '<div class="stage" title="' + stg.label + ' - ' + stg.title + '"><div class="stage-label">' + stg.label + '</div><div class="stage-badge ' + stg.key + '"><a href="' + stgUrl + '" class="' + countClass + '">' + count + '</a></div></div>';
                });
                pipelineHtml += '</div>';

                var directClientLabelText = job.recommendation_text || (job.is_direct_client ? 'Direct Client' : ((job.self_company == 1) ? 'Internal' : 'Internal Client'));

                var isSalaryConfidential = Boolean(
                    job.is_salary_confidential ||
                    job.salary_visibility === 'confidential' ||
                    job.salary_visibility === 'fully_confidential' ||
                    job.salary_display === 'Confidential' ||
                    ((parseFloat(job.min_salary) === 0 || !job.min_salary || job.min_salary === '0') &&
                     (parseFloat(job.max_salary) === 0 || !job.max_salary || job.max_salary === '0'))
                );

                var salaryHtml = isSalaryConfidential
                    ? '<div class="sal-row"><span class="sal-amount sal-amount-confidential" style="font-weight: 500; color: #dc2626; display: inline-flex; align-items: center; gap: 4px;"><i class="fas fa-lock" style="font-size: 10px; color: #dc2626;"></i> Confidential</span></div>'
                    : '<div class="sal-row"><span class="sal-amount">' + job.min_salary_currency + ' ' + job.min_salary + ' - ' + job.max_salary + salaryModeText + '</span></div>';

                var locations = Array.isArray(job.work_locations) ? job.work_locations : [];
                var allCitiesList = (Array.isArray(job.cities) && job.cities.length > 0)
                    ? job.cities.filter(Boolean)
                    : (locations.length > 0 ? locations.map(function(l) { return l.city_name; }).filter(Boolean) : (job.primary_city || job.city ? [job.primary_city || job.city] : []));
                var primaryCityName = job.primary_city ? formatJobPositionName(job.primary_city) : (allCitiesList.length > 0 ? formatJobPositionName(allCitiesList[0]) : (job.city ? formatJobPositionName(job.city) : ''));
                var extraCitiesCount = allCitiesList.length > 1 ? (allCitiesList.length - 1) : 0;

                var distinctCountries = locations.map(function(l) { return (l.country_name || '').trim(); }).filter(Boolean);
                var hasMultipleCountries = (new Set(distinctCountries.map(function(c) { return c.toLowerCase(); }))).size > 1;

                var locContentHtml = '';
                if (primaryCityName) {
                    locContentHtml = '<span class="city-name-text" title="' + primaryCityName + '">' + primaryCityName.substring(0, 10) + '</span>';
                    if (extraCitiesCount > 0) {
                        var popoverItems = allCitiesList.map(function(c) {
                            var cleanC = formatJobPositionName(c);
                            var matchLoc = locations.find(function(l) { return (l.city_name || '').toLowerCase() === String(c).toLowerCase(); });
                            var countrySuffix = (hasMultipleCountries && matchLoc && matchLoc.country_name) ? ' <span style="opacity: 0.65; font-size: 9.5px;">(' + formatJobPositionName(matchLoc.country_name) + ')</span>' : '';
                            return '<div class="loc-popover-item"><i class="fas fa-map-marker-alt"></i> ' + cleanC + countrySuffix + '</div>';
                        }).join('');

                        locContentHtml += '<div class="loc-more-badge-wrap">' +
                            '<span class="loc-more-badge">+' + extraCitiesCount + '</span>' +
                            '<div class="loc-popover-card">' +
                                '<div class="loc-popover-header">Work Locations (' + allCitiesList.length + ')</div>' +
                                '<div class="loc-popover-body">' + popoverItems + '</div>' +
                            '</div>' +
                        '</div>';
                    }
                } else {
                    locContentHtml = '<span class="city-name-text text-muted" style="color: #a1a5b7;" title="Location not specified">Not specified</span>';
                }

                var locRowHtml = '<div class="loc-row">' +
                    '<svg fill="none" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="1.8"/></svg>' +
                    locContentHtml +
                '</div>';

                html += '<div class="job-row" data-job-id="' + job.id + '" style="position: relative; min-height: 96px; cursor: pointer;">' +
                    '<div class="col-job ' + profileDetailClass + '" data-row-id="' + job.id + '">' +
                        '<div class="job-logo ' + job.bg_text_decore + '" style="min-height: 48px; align-self: flex-start;">' + logoHtml + '</div>' +
                        '<div class="job-info">' +
                          '<div class="job-title">' + (displayTitle ? displayTitle.substring(0, 30) : '') + '</div>' +
                          '<div class="job-dept">' + (job.department || '') + '</div>' +
                          '<div class="job-client">' + (displayClientName ? displayClientName.substring(0, 30) : '') + '</div>' +
                        '</div>' +
                    '</div>' +

                    '<div class="col-exp">' +
                      '<div class="exp-row"><svg fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8"/><polyline points="12 6 12 12 16 14" stroke="currentColor" stroke-width="1.8"/></svg>' + job.min_exp + ' - ' + job.max_exp + ' Years</div>' +
                      salaryHtml +
                      '<span class="badge-type employment-badge employment-badge-' + employmentMeta.tone + '"><i class="fas ' + employmentMeta.icon + '"></i><span class="it-job-type">' + job.employment_type + '</span></span>' +
                    '</div>' +

                    '<div class="col-hl">' + ulink +
                      locRowHtml +
                      '<div class="mode-row"><svg fill="none" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke="currentColor" stroke-width="1.8"/><polyline points="9,22 9,12 15,12 15,22" stroke="currentColor" stroke-width="1.8"/></svg>' + job.work_type + '</div>' +
                    '</div>' +

                    '<div class="col-hm">' +
                      '<div class="hm-name" title="' + (displayHiringManager !== '--------' ? displayHiringManager : '') + '">' + (displayHiringManager ? displayHiringManager.substring(0, 40) : '') + '</div>' +
                      ((job.hiring_manager_designation && job.hiring_manager_designation !== '--------') ? '<div class="hm-title" title="' + job.hiring_manager_designation + '">' + formatJobPositionName(job.hiring_manager_designation) + '</div>' : '') +
                      '<div class="hm-company hm-client-type"><span class="it-job-type ' + job.badge + '">' + directClientLabelText + '</span><span class="it-dot-separator"></span><a href="javascript:void(0);" class="it-job-id">' + job.id + '</a></div>' +
                    '</div>' +

                    '<div class="col-action">' + teamHtml + '</div>' +
                    '<div class="col-action">' + agencyHtml + '</div>' +

                    '<div class="col-pipeline">' + pipelineHtml +
                      '<div class="pipeline-total">Total: <strong>' + (job.application_counter || 0) + ' Applications</strong> &nbsp;|&nbsp; Interview: <strong>' + (job.interview_counter || 0) + '</strong></div>' +
                    '</div>' +

                    '<div class="job-meta-strip">' +
                      '<div class="job-meta-line">' + addedDateHtml + skillsHtml + '</div>' +
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
                    var matchCity = (job.city || '').toLowerCase().includes(searchTerm) ||
                        (Array.isArray(job.cities) && job.cities.some(function(c) { return String(c).toLowerCase().includes(searchTerm); })) ||
                        (Array.isArray(job.work_locations) && job.work_locations.some(function(wl) { return (wl.city_name || '').toLowerCase().includes(searchTerm) || (wl.country_name || '').toLowerCase().includes(searchTerm); }));
                    var matchHM = (job.hiring_manager || '').toLowerCase().includes(searchTerm);
                    var matchSkills = (job.skills || []).some(function(s) { return s.toLowerCase().includes(searchTerm); });
                    var matchId = String(job.id) === searchTerm;
                    if (!matchTitle && !matchDept && !matchClient && !matchCity && !matchHM && !matchSkills && !matchId) {
                        return false;
                    }
                }

                if (status !== 'all' && status !== '') {
                    var s = (job.status || '').toLowerCase();
                    var sr = (job.status_raw || '').toLowerCase();
                    if (status === 'active' && s !== 'hiring' && sr !== 'active') return false;
                    if (status === 'hold' && s !== 'hold' && sr !== 'hold') return false;
                    if (status === 'inactive' && s !== 'closed' && sr !== 'inactive') return false;
                    if (status === 'removed' && s !== 'removed') return false;
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
        }

                function applyJobsResponse(response, page) {
            window.currentJobsAjax = null;
            $('#jobSearchBtn').prop('disabled', false).removeClass('disabled');
            $('.loadingMessage').css({ display: 'none' });
            $('.loader_search').css({ display: 'none' });
            $('.page-loader').css('display', 'none');
            localStorage.setItem('currentPageJobs', page);

            updateKpiViewToggle(window.currentKpiFilter);

            if (window.currentKpiFilter === 'job_request' || window.currentKpiFilter === 'request') {
                if (response.html && response.html.trim() !== '') {
                    $('#allJobList').html(response.html);
                } else if (response.jobs && response.jobs.length > 0) {
                    $('#allJobList').html(renderJobsClientSide(response.jobs));
                } else {
                    $('#allJobList').html(
                        '<div class="card border-0 shadow-sm rounded-4 text-center py-16 px-6 my-4" style="background: #ffffff; border: 1px solid #eef0f4; border-radius: 12px;">' +
                          '<div class="mb-4 d-flex justify-content-center">' +
                            '<div style="width: 64px; height: 64px; border-radius: 16px; background-color: #f1f3f7; display: flex; align-items: center; justify-content: center; color: #a1a5b7;">' +
                              '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' +
                                '<rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>' +
                                '<path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>' +
                                '<line x1="12" y1="11" x2="12" y2="17"/>' +
                                '<line x1="9" y1="14" x2="15" y2="14"/>' +
                              '</svg>' +
                            '</div>' +
                          '</div>' +
                          '<h4 class="fw-bolder text-dark mb-2" style="font-family: \'Poppins\', sans-serif; font-size: 16px; color: #181c32;">No Job Requests Found</h4>' +
                          '<p class="text-muted fs-7 mb-0" style="font-family: \'Poppins\', sans-serif; font-size: 13px; color: #7e8299;">There are no pending job requests at the moment.</p>' +
                        '</div>'
                    );
                }
            } else {
                if (response.jobs && response.jobs.length > 0) {
                    window.jobsState = {
                        all: response.jobs,
                        filtered: response.jobs,
                        total: response.total
                    };
                    $('#allJobList').html(renderJobsClientSide(response.jobs));
                } else if (response.html) {
                    $('#allJobList').html(response.html);
                } else {
                    $('#allJobList').html(
                        '<div style="text-align:center;padding:40px 20px;font-family:Poppins,sans-serif;">' +
                        '<p style="font-size:13px;color:#5e6278;margin:0;">No jobs found.</p>' +
                        '</div>'
                    );
                }
            }
            $('#all_jobs_count').html(response.total !== undefined ? response.total : 0);
            $('#pagination').html(response.pagination || '');

            if (response.status_counts) {
                $('.jh-tab[data-status="all"] .jh-badge').text(response.status_counts.all !== undefined ? response.status_counts.all : 0);
                $('.jh-tab[data-status="active"] .jh-badge').text(response.status_counts.active !== undefined ? response.status_counts.active : 0);
                $('.jh-tab[data-status="hold"] .jh-badge').text(response.status_counts.hold !== undefined ? response.status_counts.hold : 0);
                $('.jh-tab[data-status="inactive"] .jh-badge').text(response.status_counts.inactive !== undefined ? response.status_counts.inactive : 0);
                $('.jh-tab[data-status="removed"] .jh-badge').text(response.status_counts.removed !== undefined ? response.status_counts.removed : 0);
            }
        }

        async function fetchCandidates(page = page_no, search = '', size = pageSize) {
            window.latestSearchRequestId = (window.latestSearchRequestId || 0) + 1;
            const thisRequestId = window.latestSearchRequestId;

            const searchVal = ($('.inputJobSearch').val() || $('#inputSearch').val() || search || '').trim();
            const cacheKey = [
                'v2',
                window.currentKpiFilter || 'all',
                $('#status').val() || 'all',
                $('#hiringtype').val() || '',
                $('#employment_type').val() || '',
                $('#experience_filter').val() || '',
                searchVal,
                page,
                size
            ].join('_');

            // Phase 2: Instant Cache Hydration from IndexedDB (<20ms render)
            let cached = null;
            try {
                cached = await JeroJobsLocalStore.get(cacheKey);
            } catch (err) {}

            if (cached && thisRequestId === window.latestSearchRequestId) {
                applyJobsResponse(cached, page);
            } else {
                $('#jobSearchBtn').prop('disabled', true).addClass('disabled');
                if (searchVal !== '') {
                    $('.loader_search').css({ display: 'block' });
                    $('.page-loader').css('display', 'flex');
                } else {
                    $('.page-loader').css('display', 'flex');
                }
            }

            if (window.currentJobsAjax && typeof window.currentJobsAjax.abort === 'function') {
                window.currentJobsAjax.abort();
            }

            window.currentJobsAjax = $.ajax({
                url: `{!! route('service_company.jobs.all_data_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                  requisition_type: window.currentKpiFilter || 'all',
                  custom_status : $('#status').val(),
                  search : searchVal,
                  custom_hiringtype : $('#hiringtype').val(),
                  employment_type : $('#employment_type').val(),
                  experience_filter : $('#experience_filter').val(),
                  custom_search : searchVal,
                  size : size
                },
                success: function(response) {
                    if (thisRequestId !== window.latestSearchRequestId) {
                        return; // Ignore superseded / stale responses
                    }
                    // Persist response into IndexedDB for instant repeat views
                    if (window.JeroJobsLocalStore) {
                        JeroJobsLocalStore.set(cacheKey, response);
                    }
                    applyJobsResponse(response, page);
                },
                error: function(xhr, status, error) {
                    if (thisRequestId !== window.latestSearchRequestId || status === 'abort') return;
                    window.currentJobsAjax = null;
                    $('#jobSearchBtn').prop('disabled', false).removeClass('disabled');
                    $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');

                    // If we already rendered from IndexedDB cache, do not overwrite with error screen
                    if (cached) return;

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
                }
            });

            return window.currentJobsAjax;
        }

    



    var UrlMailJobId = "{{$UrlMailJobId}}";
    if(UrlMailJobId){

      setTimeout(() => {
         $('#kt_activitiesJob_toggle').trigger('click');		
      

        var id    		=	UrlMailJobId;
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


    // Phase 4: Real-time Reverb Delta Synchronization
    function initJobsReverbSync() {
        if (!window.Echo || typeof window.Echo.private !== 'function') {
            return;
        }

        const companyId = {{ (int) (auth()->user()->company_id ?? 0) }};
        if (!companyId) return;

        try {
            window.Echo.private('company.' + companyId)
                .listen('.JobStatusChanged', function(event) {
                    if (!event || !event.jobId) return;

                    const jobId = parseInt(event.jobId, 10);
                    const newStatus = (event.newStatus || '').toLowerCase();

                    // 1. Purge local IndexedDB and drawer caches so fresh data is guaranteed
                    if (window.JeroJobsLocalStore) {
                        window.JeroJobsLocalStore.purge();
                    }
                    window.jobDrawerCache = {};

                    // 2. Instant Delta Patch in Browser RAM & DOM
                    if (window.jobsState && window.jobsState.all) {
                        let targetJob = window.jobsState.all.find(j => parseInt(j.id, 10) === jobId);
                        if (targetJob) {
                            targetJob.status_raw = newStatus;
                            targetJob.status = (newStatus === 'active') ? 'Hiring' : ((newStatus === 'hold') ? 'Hold' : 'Closed');
                            
                            // Re-render currently visible filtered list in RAM (<2ms)
                            $('#allJobList').html(renderJobsClientSide(window.jobsState.filtered));
                        }
                    }

                    // 3. Update status counters if supplied
                    if (event.status_counts) {
                        $('.jh-tab[data-status="all"] .jh-badge').text(event.status_counts.all || 0);
                        $('.jh-tab[data-status="active"] .jh-badge').text(event.status_counts.active || 0);
                        $('.jh-tab[data-status="hold"] .jh-badge').text(event.status_counts.hold || 0);
                        $('.jh-tab[data-status="inactive"] .jh-badge').text(event.status_counts.inactive || 0);
                        $('.jh-tab[data-status="removed"] .jh-badge').text(event.status_counts.removed || 0);
                    }

                    // 4. Show non-intrusive notification if toastr is loaded
                    if (typeof showToastr === 'function' && event.jobTitle) {
                        showToastr('info', 'Job "' + event.jobTitle + '" status changed to ' + event.newStatus, 'Job Status Updated');
                    }
                })
                .listen('.candidate.assigned', function(event) {
                    if (!event || !event.jobId) return;
                    const jobId = parseInt(event.jobId, 10);

                    if (window.JeroJobsLocalStore) {
                        window.JeroJobsLocalStore.purge();
                    }

                    // Direct DOM patch of pipeline badge on the job card (0ms latency)
                    const $jobRow = $('.job-row[data-job-id="' + jobId + '"]');
                    if ($jobRow.length > 0) {
                        const stageId = parseInt(event.masterStatusId, 10);
                        if (stageId) {
                            const $badge = $jobRow.find('.stage-badge a[href*="/' + stageId + '"]');
                            if ($badge.length > 0) {
                                const currentCount = parseInt($badge.text().trim(), 10) || 0;
                                $badge.text(currentCount + 1);
                            }
                        }
                    }
                })
                .listen('.CandidateDrawerUpdated', function(event) {
                    if (window.JeroJobsLocalStore) {
                        window.JeroJobsLocalStore.purge();
                    }
                    window.jobDrawerCache = {};
                })
                .listen('.JobSkillsUpdated', function(event) {
                    if (window.JeroJobsLocalStore) {
                        window.JeroJobsLocalStore.purge();
                    }
                    window.jobDrawerCache = {};
                });

            // Reconnection recovery: purge cache to ensure re-sync with server authoritative state
            if (window.Echo.connector && window.Echo.connector.pusher && window.Echo.connector.pusher.connection) {
                window.Echo.connector.pusher.connection.bind('connected', function() {
                    if (window.JeroJobsLocalStore) {
                        window.JeroJobsLocalStore.purge();
                    }
                });
            }
        } catch (err) {
            console.warn('[Reverb Jobs] sync error:', err);
        }
    }

    $(document).ready(function() {
        initJobsReverbSync();
    });

</script>

@endpush
