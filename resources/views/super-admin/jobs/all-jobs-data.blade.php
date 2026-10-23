@extends('layouts.superadmin_layout')

@push('head-script')
<!-- Responsive CSS -->
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>
<!-- Responsive CSS End -->
<style>
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
  /* Intent: size the Total Jobs drawer at 80% of the content canvas */
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
    height: 82px;
    position: relative;
}
.modal-backdrop.show {
    opacity: 0 !important;
    display: none;
}

.hidden-button {
  display: none;
  position: absolute;
  bottom: 10px;
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
  display: block;
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

/* Job Status Badge & Color Combinations (Metronic Palette) */
.badge-hiring {
  display: inline-flex !important;
  align-items: center !important;
  gap: 5px !important;
  font-size: 11.5px !important;
  font-weight: 600 !important;
  line-height: 1 !important;
  cursor: pointer !important;
  width: fit-content !important;
  padding: 0 !important;
  background: none !important;
  border-radius: 0 !important;
  text-decoration: none !important;
  transition: opacity 0.15s ease-in-out;
}
.badge-hiring:hover {
  opacity: 0.85;
}
.badge-hiring i {
  width: 13px !important;
  height: 13px !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  flex-shrink: 0 !important;
  font-size: 11px !important;
  line-height: 13px !important;
}
.badge-hiring.active-bg,
.badge-hiring.active-bg i {
  color: #17c653 !important;
}
.badge-hiring.hold-bg,
.badge-hiring.hold-bg i {
  color: #ffa800 !important;
}
.badge-hiring.closed-bg,
.badge-hiring.closed-bg i {
  color: #f1416c !important;
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
    'allRequisition' => @$dataCnt['allRequisition'] ?? 0,
    'customRequisition' => @$dataCnt['customRequisition'] ?? 0,
    'clientRequisition' => @$dataCnt['clientRequisition'] ?? 0,
    'jobRequest' => @$dataCnt['jobRequest'] ?? 0,
    'productAllowCompany' => @$productAllowCompany,
    'userPermissions' => [],
    'hiringCount'    => @$dataCnt['hiringCount'] ?? 0,
    'holdCount'      => @$dataCnt['holdCount'] ?? 0,
    'closedCount'    => @$dataCnt['closedCount'] ?? 0,
    'allTabCount'    => @$dataCnt['allTabCount'] ?? @$dataCnt['allRequisition'] ?? 0,
    'removedCount'   => @$dataCnt['removedCount'] ?? 0,
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
        <!-- FILTER BAR (Exact replica of sections/service_company/jobFilterBar) -->
        <div class="jh-filter-bar">
          <div class="jh-search-wrap">
            <svg class="jh-search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" class="jh-search-input inputJobSearch" name="inputJobSearch"
                   placeholder="Search by job title, skills, company..." id="kt_tagify_2"
                   autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"/>
            <button type="button" id="jobSearchBtn" class="jh-search-btn" onclick="executeJobSearch(1)">
              Search
            </button>
          </div>

          <div class="jh-filter-group">
            {{-- Hidden status input so status tabs continue updating table --}}
            <input type="hidden" id="status" value="all">

            {{-- JOB TYPE Dropdown --}}
            <div class="jh-select-wrap jh-custom-select">
              <span class="jh-control-icon">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24">
                  <rect x="3" y="7" width="18" height="13" rx="2" stroke="currentColor" stroke-width="1.8"/>
                  <path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="1.8"/>
                </svg>
              </span>
              <div class="jh-control-copy">
                <div class="jh-select-label">JOB TYPE</div>
                <button type="button" class="jh-select-trigger" data-select-target="employment_type" aria-expanded="false">
                  <span class="jh-select-value">All Types</span>
                </button>
                <select class="jh-select-native" id="employment_type" onchange="fetchCandidates(1)" tabindex="-1" aria-hidden="true">
                  <option value="all" selected>All Types</option>
                  <option value="0">Full-time</option>
                  <option value="1">Contract</option>
                  <option value="2">Freelance</option>
                  <option value="3">Part Time</option>
                </select>
              </div>
              <svg class="jh-select-caret" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
              <div class="jh-select-menu" data-select-menu="employment_type">
                <button type="button" class="jh-select-option is-selected" data-value="all">All Types</button>
                <button type="button" class="jh-select-option" data-value="0">Full-time</button>
                <button type="button" class="jh-select-option" data-value="1">Contract</button>
                <button type="button" class="jh-select-option" data-value="2">Freelance</button>
                <button type="button" class="jh-select-option" data-value="3">Part Time</button>
              </div>
            </div>

            {{-- EXPERIENCE Dropdown --}}
            <div class="jh-select-wrap jh-custom-select">
              <span class="jh-control-icon">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24">
                  <path d="M22 10v6M2 10l10-5 10 5-10 5z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M6 12v5c0 2 3 3 6 3s6-1 6-3v-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
              <div class="jh-control-copy">
                <div class="jh-select-label">EXPERIENCE</div>
                <button type="button" class="jh-select-trigger" data-select-target="experience_filter" aria-expanded="false">
                  <span class="jh-select-value">All Exp.</span>
                </button>
                <select class="jh-select-native" id="experience_filter" onchange="fetchCandidates(1)" tabindex="-1" aria-hidden="true">
                  <option value="all" selected>All Exp.</option>
                  <option value="0-1">0-1 yr</option>
                  <option value="1-3">1-3 yrs</option>
                  <option value="3-5">3-5 yrs</option>
                  <option value="5+">5+ yrs</option>
                </select>
              </div>
              <svg class="jh-select-caret" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
              <div class="jh-select-menu" data-select-menu="experience_filter">
                <button type="button" class="jh-select-option is-selected" data-value="all">All Exp.</button>
                <button type="button" class="jh-select-option" data-value="0-1">0-1 yr</button>
                <button type="button" class="jh-select-option" data-value="1-3">1-3 yrs</button>
                <button type="button" class="jh-select-option" data-value="3-5">3-5 yrs</button>
                <button type="button" class="jh-select-option" data-value="5+">5+ yrs</button>
              </div>
            </div>

            {{-- SORT BY Dropdown --}}
            <div class="jh-select-wrap jh-custom-select">
              <span class="jh-control-icon">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24">
                  <path d="M4 7h12M4 12h9M4 17h5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                  <path d="M18 6v12m0 0 3-3m-3 3-3-3" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
              <div class="jh-control-copy">
                <div class="jh-select-label">SORT BY</div>
                <button type="button" class="jh-select-trigger" data-select-target="hiringtype" aria-expanded="false">
                  <span class="jh-select-value">Newest First</span>
                </button>
                <select class="jh-select-native" id="hiringtype" onchange="fetchCandidates(1)" tabindex="-1" aria-hidden="true">
                  <option value="1" selected>Newest First</option>
                  <option value="2">Oldest First</option>
                  <option value="3">Low-High (Salary)</option>
                  <option value="4">High-Low (Salary)</option>
                  <option value="5">A-Z (Company)</option>
                  <option value="6">Z-A (Company)</option>
                </select>
              </div>
              <svg class="jh-select-caret" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
              <div class="jh-select-menu" data-select-menu="hiringtype">
                <button type="button" class="jh-select-option is-selected" data-value="1">Newest First</button>
                <button type="button" class="jh-select-option" data-value="2">Oldest First</button>
                <button type="button" class="jh-select-option" data-value="3">Low-High (Salary)</button>
                <button type="button" class="jh-select-option" data-value="4">High-Low (Salary)</button>
                <button type="button" class="jh-select-option" data-value="5">A-Z (Company)</button>
                <button type="button" class="jh-select-option" data-value="6">Z-A (Company)</button>
              </div>
            </div>
          </div>
        </div>

        <!-- STATUS TABS (Exact replica of sections/service_company status tabs) -->
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

        </div>

        <!-- TABLE CARD SHELL (Exact replica of sections/service_company/jobListTable) -->
        <div class="table-card">
          <!-- Standard Header -->
          <div class="table-header table-header-standard">
            <div class="th">Job Details</div>
            <div class="th">
              Experience
              <svg width="13" height="13" fill="none" viewBox="0 0 24 24">
                <rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/>
                <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" stroke="currentColor" stroke-width="1.8"/>
              </svg>
            </div>
            <div class="th">Location</div>
            <div class="th">Hiring Manager</div>
            <div class="th">Team</div>
            <div class="th">Agency</div>
            <div class="th">
              Hiring Pipeline
              <svg width="13" height="13" fill="none" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8"/>
                <path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
              </svg>
            </div>
          </div>

          <!-- Job Requests Header -->
          <div class="table-header table-header-request" style="display: none;">
            <div class="th">Job Details</div>
            <div class="th">
              Experience
              <svg width="13" height="13" fill="none" viewBox="0 0 24 24">
                <rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/>
                <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" stroke="currentColor" stroke-width="1.8"/>
              </svg>
            </div>
            <div class="th">Hiring & Location</div>
            <div class="th">Hiring Manager</div>
            <div class="th">Date</div>
            <div class="th">Terms</div>
            <div class="th">
              Action
              <svg width="13" height="13" fill="none" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8"/>
                <path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
              </svg>
            </div>
          </div>

          <div class="loadingMessage" style="display: none; position: absolute;">Processing...</div>
          <div class="" id="allJobList"></div>

          <!-- PAGINATION -->
          <div id="pagination"></div>

          <!-- LEGEND -->
          <div class="pipeline-legend-card">
            <div class="pipeline-legend">
              <span class="legend-label">Pipeline Stages:</span>
              <div class="legend-item"><div class="legend-dot" style="background: #bdbd26"></div>INW – In Review</div>
              <div class="legend-item"><div class="legend-dot" style="background: #7e8299"></div>SOU – Sourcing</div>
              <div class="legend-item"><div class="legend-dot" style="background: #c000ff"></div>SCR – Screening</div>
              <div class="legend-item"><div class="legend-dot" style="background: #2896ff"></div>EVA – Evaluation</div>
              <div class="legend-item"><div class="legend-dot" style="background: #ff4098"></div>NEG – Negotiation</div>
              <div class="legend-item"><div class="legend-dot" style="background: #ffab00"></div>OFF – Offer</div>
              <div class="legend-item"><div class="legend-dot" style="background: #06c732"></div>JOI – Joining</div>
              <div class="legend-item"><div class="legend-dot" style="background: #ff0202"></div>REJ – Rejected</div>
            </div>
          </div>
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
  <div class="card shadow-none bg-custom-bar rounded-0 w-100">
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
        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
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
        <div class="d-flex align-items-center">
          <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
            <a href="javascript:void(0);">
              <div class="symbol-label border-radius-5" id="light_recruiter_caption">&nbsp;</div>
            </a>
          </div>
          <div class="d-flex flex-column">
            <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" id="light_recruiter_name">&nbsp;</a>
          </div>
        </div>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
      </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesRecruiter_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
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
        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
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
        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
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
        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
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

<!-- Modern Sliding Job Status Drawer (Exact replica of service_company 80% drawer) -->
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
<link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

<script>
@if(session('packageStatus'))
 Swal.fire("Failed", "{{ session('packageStatus') }}" , "warning");	
@endif
</script>

<script>
    /**
     * Phase 5: Client Caching & IndexedDB Integration (Rule 6 Multi-Tenant & Context Isolation)
     */
    const SuperAdminJobsLocalStore = (function() {
        const DB_NAME = 'jero_superadmin_jobs_db';
        const DB_VERSION = 2;
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
                        if (db.objectStoreNames.contains('jobs_cache')) {
                            db.deleteObjectStore('jobs_cache');
                        }
                        if (db.objectStoreNames.contains('meta')) {
                            db.deleteObjectStore('meta');
                        }
                        db.createObjectStore('jobs_cache', { keyPath: 'cache_key' });
                        db.createObjectStore('meta', { keyPath: 'id' });
                    };
                    req.onsuccess = function(e) {
                        const db = e.target.result;
                        try {
                            const tx = db.transaction(['meta'], 'readonly');
                            const metaStore = tx.objectStore('meta');
                            const metaReq = metaStore.get('current_context');
                            metaReq.onsuccess = function() {
                                const meta = metaReq.result;
                                if (meta && meta.user_id !== CURRENT_USER_ID) {
                                    const wipeTx = db.transaction(['jobs_cache', 'meta'], 'readwrite');
                                    wipeTx.objectStore('jobs_cache').clear();
                                    wipeTx.objectStore('meta').put({ id: 'current_context', user_id: CURRENT_USER_ID, is_superadmin: 1, updated_at: Date.now() });
                                } else if (!meta) {
                                    const setTx = db.transaction(['meta'], 'readwrite');
                                    setTx.objectStore('meta').put({ id: 'current_context', user_id: CURRENT_USER_ID, is_superadmin: 1, updated_at: Date.now() });
                                }
                            };
                        } catch (err) {
                            console.warn('SuperAdminJobsLocalStore context error:', err);
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
                } catch (e) {}
            },
            purge: async function() {
                const db = await getDB();
                if (!db) return;
                try {
                    const tx = db.transaction(['jobs_cache'], 'readwrite');
                    tx.objectStore('jobs_cache').clear();
                } catch (e) {}
            }
        };
    })();
    window.SuperAdminJobsLocalStore = SuperAdminJobsLocalStore;

    // In-memory cache for opened drawer views (zero repeat network roundtrips)
    window.jobDrawerCache = window.jobDrawerCache || {};
    window.lastAuthoritativeSearch = '';

    var page_no = localStorage.getItem('currentPageJobs');
    if (!page_no) {
        page_no = 1;
    }
    var pageSize = 10;

    /**
     * Phase 4: Client-Side Jobs Store & Rendering Engine
     * Enables instant 0ms searching, filtering, sorting, and UI updates in browser RAM.
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
            var displayClientName = (job.is_direct_client && job.parent_company_name) ? formatCompanyName(job.parent_company_name) : (job.clients_name ? formatCompanyName(job.clients_name) : formatCompanyName(job.company_name));
            var displayAgencyName = formatCompanyName(job.agency_company_name);
            var displayCity = formatJobPositionName(job.city);
            var displayHiringManager = formatJobPositionName(job.hiring_manager);
            var displaySalaryMode = formatSalaryMode(job.min_sal_mode);
            var salaryModeText = displaySalaryMode ? ' /' + displaySalaryMode : '';
            var employmentMeta = getEmploymentTypeMeta(job.employment_type, job.employment_type_raw);
            var rawSt = String(job.status_raw || job.status || 'active').toLowerCase().trim();
            var displayStatus = 'Hiring';
            var statusIcon = 'fa-user-group';
            var statusClass = 'active-bg';

            if (rawSt === 'hold' || rawSt === 'on hold') {
                displayStatus = 'Hold';
                statusIcon = 'fa-pause';
                statusClass = 'hold-bg';
            } else if (rawSt === 'inactive' || rawSt === 'closed') {
                displayStatus = 'Closed';
                statusIcon = 'fa-box-archive';
                statusClass = 'closed-bg';
            } else if (rawSt === 'removed') {
                displayStatus = 'Removed';
                statusIcon = 'fa-box-archive';
                statusClass = 'closed-bg';
            } else {
                displayStatus = 'Hiring';
                statusIcon = 'fa-user-group';
                statusClass = 'active-bg';
            }

            var ulink = (displayStatus === 'Removed')
                ? '<span class="badge-hiring ' + statusClass + '"><i class="fas ' + statusIcon + '"></i> ' + displayStatus + '</span>'
                : '<span data-row-id="' + job.id + '" data-direct-client="' + directClientAttr + '" class="badge-hiring ' + statusClass + ' status_stage" title="Click to update status"><i class="fas ' + statusIcon + '"></i> ' + displayStatus + '</span>';

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
            pipelineHtml += '<div class="stage show-detail" data-row-id="' + job.id + '" title="INW - In Review" style="cursor:pointer;"><div class="stage-label">INW</div><div class="stage-badge s-inw"><a href="javascript:void(0);" class="s-inw fw-bolder fs-6">' + (job.inreview_counter || 0) + '</a></div></div>';

            stageLabels.forEach(function(stg) {
                var count = (job.stage_counts && job.stage_counts[stg.id]) ? job.stage_counts[stg.id] : 0;
                var countClass = count > 0 ? (stg.id === 1 ? 'text-dark fw-bolder fs-6' : stg.key + ' fs-6') : stg.key + ' fs-6';
                pipelineHtml += '<div class="stage show-detail" data-row-id="' + job.id + '" title="' + stg.label + ' - ' + stg.title + '" style="cursor:pointer;"><div class="stage-label">' + stg.label + '</div><div class="stage-badge ' + stg.key + '"><a href="javascript:void(0);" class="' + countClass + '">' + count + '</a></div></div>';
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
                  '<div class="loc-row"><svg fill="none" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="1.8"/></svg>' + (displayCity ? displayCity.substring(0, 10) : '') + '</div>' +
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

        var searchTerm = ($('.inputJobSearch').val() || '').toLowerCase().trim();
        var status = $('#status').val() || 'all';
        var employmentType = $('#employment_type').val() || 'all';
        var expFilter = $('#experience_filter').val() || 'all';
        var sortVal = $('#hiringtype').val() || '1';

        var filtered = window.jobsState.all.filter(function(job) {
            if (searchTerm !== '') {
                var matchTitle = (job.title || '').toLowerCase().includes(searchTerm);
                var matchDept = (job.department || '').toLowerCase().includes(searchTerm);
                var matchClient = (job.clients_name || '').toLowerCase().includes(searchTerm);
                var matchCompany = (job.company_name || '').toLowerCase().includes(searchTerm);
                var matchCity = (job.city || '').toLowerCase().includes(searchTerm);
                var matchHM = (job.hiring_manager || '').toLowerCase().includes(searchTerm);
                var matchSkills = (job.skills || []).some(function(s) { return s.toLowerCase().includes(searchTerm); });
                var matchId = String(job.id) === searchTerm;
                if (!matchTitle && !matchDept && !matchClient && !matchCompany && !matchCity && !matchHM && !matchSkills && !matchId) {
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
            if (sortVal === '5') return (a.clients_name || a.company_name || '').localeCompare(b.clients_name || b.company_name || '');
            if (sortVal === '6') return (b.clients_name || b.company_name || '').localeCompare(a.clients_name || a.company_name || '');
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
        localStorage.setItem('currentPageJobs', page);

        updateKpiViewToggle(window.currentKpiFilter);

        if (response.jobs && response.jobs.length > 0) {
            window.jobsState = {
                all: response.jobs,
                filtered: response.jobs,
                total: response.total
            };
            $('#allJobList').html(renderJobsClientSide(response.jobs));
        } else if (response.html && response.html.trim() !== '') {
            $('#allJobList').html(response.html);
        } else {
            $('#allJobList').html(
                '<div style="text-align:center;padding:40px 20px;font-family:Poppins,sans-serif;">' +
                '<p style="font-size:13px;color:#5e6278;margin:0;">No jobs found.</p>' +
                '</div>'
            );
        }

        $('#all_jobs_count, #allRequisition').html(response.total !== undefined ? response.total : 0);
        $('#pagination').html(response.pagination || '');

        if (response.status_counts) {
            $('.jh-tab[data-status="all"] .jh-badge').text(response.status_counts.all !== undefined ? response.status_counts.all : 0);
            $('.jh-tab[data-status="active"] .jh-badge').text(response.status_counts.active !== undefined ? response.status_counts.active : 0);
            $('.jh-tab[data-status="hold"] .jh-badge').text(response.status_counts.hold !== undefined ? response.status_counts.hold : 0);
            $('.jh-tab[data-status="inactive"] .jh-badge').text(response.status_counts.inactive !== undefined ? response.status_counts.inactive : 0);
        }
    }

    async function fetchCandidates(page = page_no, search = '', size = pageSize) {
        window.latestSearchRequestId = (window.latestSearchRequestId || 0) + 1;
        const thisRequestId = window.latestSearchRequestId;

        const searchVal = ($('.inputJobSearch').val() || search || '').trim();
        const cacheKey = [
            window.currentKpiFilter || 'all',
            $('#status').val() || 'all',
            $('#hiringtype').val() || '',
            $('#employment_type').val() || '',
            $('#experience_filter').val() || '',
            searchVal,
            page,
            size
        ].join('_');

        // Phase 5: Instant Cache Hydration from IndexedDB (<20ms render)
        let cached = null;
        try {
            cached = await SuperAdminJobsLocalStore.get(cacheKey);
        } catch (err) {}

        if (cached && thisRequestId === window.latestSearchRequestId) {
            applyJobsResponse(cached, page);
        } else {
            $('#jobSearchBtn').prop('disabled', true).addClass('disabled');
            $('.loadingMessage').css({ display: 'block' });
        }

        if (window.currentJobsAjax && typeof window.currentJobsAjax.abort === 'function') {
            window.currentJobsAjax.abort();
        }

        window.currentJobsAjax = $.ajax({
            url: `{!! route('superadmin.company.all_jobs_data_ajax') !!}?page=${page}`,
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
                    return;
                }
                if (window.SuperAdminJobsLocalStore) {
                    SuperAdminJobsLocalStore.set(cacheKey, response);
                }
                applyJobsResponse(response, page);
            },
            error: function(xhr, status, error) {
                if (thisRequestId !== window.latestSearchRequestId || status === 'abort') return;
                window.currentJobsAjax = null;
                $('#jobSearchBtn').prop('disabled', false).removeClass('disabled');
                $('.loadingMessage').css({ display: 'none' });

                if (cached) return;

                var msg = (xhr.status === 0 || !navigator.onLine)
                    ? 'You are currently offline or the server is unreachable. Please check your connection and try again.'
                    : 'Could not load jobs (HTTP ' + xhr.status + '). Please try again.';

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

    function executeJobSearch(page = 1) {
        localStorage.setItem('currentPageJobs', page);
        var currentSearch = ($('.inputJobSearch').val() || '').trim();
        window.lastAuthoritativeSearch = currentSearch;
        fetchCandidates(page);
    }

    // Debounced search input handler: filters in RAM at 350ms, authoritative call when needed
    var jobFilterDebounceTimer = null;
    $(document).on('input', '.inputJobSearch', function() {
        var val = $(this).val();
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

    $(document).on('keydown', '.inputJobSearch', function(e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
            clearTimeout(jobFilterDebounceTimer);
            executeJobSearch(1);
        }
    });

    $(document).on('click', '#jobSearchBtn', function(e) {
        e.preventDefault();
        clearTimeout(jobFilterDebounceTimer);
        executeJobSearch(1);
    });

    $(document).on('change', '#status,#hiringtype,#employment_type,#experience_filter', function() {
        if (this.id === 'status') {
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

        try {
            var currentUrl = new URL(window.location.href);
            if (kpiType && kpiType !== 'all') {
                currentUrl.searchParams.set('kpi', kpiType);
            } else {
                currentUrl.searchParams.delete('kpi');
            }
            window.history.replaceState({}, '', currentUrl.pathname + currentUrl.search + currentUrl.hash);
        } catch (e) {}

        updateKpiViewToggle(kpiType);
        localStorage.setItem('currentPageJobs', 1);
        fetchCandidates(1);
    }

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

    /**
     * Phase 6: Drawer Replication & Interaction Wiring
     */
    $(document).on('click', '#status_sub_stage_close', function () { $('#status_drawer_close').trigger('click'); });

    $(document).on('click', '.status_stage', function () {
        $('#status_body_content').html('<div class="text-center py-20 text-muted"><i class="fa fa-spinner fa-spin"></i> Loading…</div>');
        $('#status_confirm_btn').prop('disabled', true).css('background', '');
        $('.changetextupdate').text('Update Status');
        $('#status_drawer_toggle').trigger('click');

        var id = $(this).data('row-id');
        var url = "{{ route('superadmin.company.jobs.editJobStatus', ':id') }}".replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            success: function (response) {
                if (response.status == "success") {
                    $('#status_body_content').html(response.view);
                } else {
                    $('#status_body_content').html('<div class="text-center py-20 text-danger">Could not load the job status details. Please close and try again.</div>');
                }
            },
            error: function () {
                $('#status_body_content').html('<div class="text-center py-20 text-danger">Something went wrong loading this job\'s status. Please close and try again.</div>');
            }
        });
    });

    $(document).on('click', '.show-detail, .client-show-detail', function () {
        $('#kt_activitiesJob_toggle').trigger('click');
        var id = $(this).data('row-id');
        var cacheKey = 'job_' + id;
        if (window.jobDrawerCache && window.jobDrawerCache[cacheKey]) {
            $('#kt_activitiesJob_body').html(window.jobDrawerCache[cacheKey]);
            return;
        }

        var url = "{{ route('superadmin.company.jobs.job_viewmore', ':id') }}".replace(':id', id);
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        $.ajax({
            type: 'GET',
            url: url,
            beforeSend: function() {
                $('#kt_activitiesJob_body').html('<div style="text-align:center;padding:40px;"><img src="'+baseurl+'" height="35px"></div>');
            },
            success: function (response) {
                if (response.view) {
                    window.jobDrawerCache[cacheKey] = response.view;
                    $('#kt_activitiesJob_body').html(response.view);
                }
            },
            error: function (xhr) {
                var msg = (xhr.status === 0 || !navigator.onLine)
                    ? 'You are offline. Please check your connection.'
                    : 'Could not load job details (HTTP ' + xhr.status + '). Please try again.';
                $('#kt_activitiesJob_body').html('<div style="padding:24px;text-align:center;font-family:Poppins,sans-serif;font-size:13px;color:#f64e60;">' + msg + '</div>');
            }
        });
    });

    $(document).on('click', '.hm-listing, .team-listing', function () {
        $('#kt_activitiesRecruiter_toggle').trigger('click');
        var id = $(this).data('row-id');
        var cacheKey = 'hm_' + id;
        if (window.jobDrawerCache && window.jobDrawerCache[cacheKey]) {
            $('#kt_activitiesRecruiter_body').html(window.jobDrawerCache[cacheKey]);
            return;
        }

        var url = "{{ route('superadmin.company.hmlisting', ':id') }}".replace(':id', id);
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        $.ajax({
            type: 'GET',
            url: url,
            beforeSend: function() {
                $('#kt_activitiesRecruiter_body').html('<div style="text-align:center;padding:40px;"><img src="'+baseurl+'" height="35px"></div>');
            },
            success: function (response) {
                if (response.view) {
                    window.jobDrawerCache[cacheKey] = response.view;
                    $('#kt_activitiesRecruiter_body').html(response.view);
                }
            }
        });
    });

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

    /**
     * Phase 7: Real-Time Reverb Delta Synchronization & Multi-Tab Broadcast
     */
    const superAdminJobsChannel = (typeof BroadcastChannel !== 'undefined') ? new BroadcastChannel('jero_superadmin_jobs') : null;
    if (superAdminJobsChannel) {
        superAdminJobsChannel.onmessage = function(e) {
            if (e.data && e.data.type === 'job_status_updated') {
                if (window.SuperAdminJobsLocalStore) { window.SuperAdminJobsLocalStore.purge(); }
                window.jobDrawerCache = {};
                fetchCandidates(parseInt(localStorage.getItem('currentPageJobs')) || 1);
            }
        };
    }

    function initSuperAdminReverbSync() {
        if (!window.Echo) return;

        try {
            window.Echo.channel('superadmin-jobs')
                .listen('.JobStatusChanged', function(event) {
                    if (!event || !event.jobId) return;
                    const jobId = parseInt(event.jobId, 10);
                    const newStatus = (event.newStatus || '').toLowerCase();

                    if (window.SuperAdminJobsLocalStore) {
                        window.SuperAdminJobsLocalStore.purge();
                    }
                    window.jobDrawerCache = {};

                    if (window.jobsState && window.jobsState.all) {
                        let targetJob = window.jobsState.all.find(j => parseInt(j.id, 10) === jobId);
                        if (targetJob) {
                            targetJob.status_raw = newStatus;
                            targetJob.status = (newStatus === 'active') ? 'Hiring' : ((newStatus === 'hold') ? 'Hold' : 'Closed');
                            $('#allJobList').html(renderJobsClientSide(window.jobsState.filtered));
                        }
                    }

                    if (event.status_counts) {
                        $('.jh-tab[data-status="all"] .jh-badge').text(event.status_counts.all || 0);
                        $('.jh-tab[data-status="active"] .jh-badge').text(event.status_counts.active || 0);
                        $('.jh-tab[data-status="hold"] .jh-badge').text(event.status_counts.hold || 0);
                        $('.jh-tab[data-status="inactive"] .jh-badge').text(event.status_counts.inactive || 0);
                    }

                    if (typeof showToastr === 'function' && event.jobTitle) {
                        showToastr('info', 'Job "' + event.jobTitle + '" status updated to ' + event.newStatus, 'Job Status Updated');
                    }
                });
        } catch (err) {
            console.warn('[Reverb SuperAdmin Jobs] sync error:', err);
        }
    }

    $(document).ready(function() {
        if (window.currentKpiFilter && window.currentKpiFilter !== 'all') {
            $('.jh-card').removeClass('jh-card-active');
            $(`.jh-card[data-kpi="${window.currentKpiFilter}"]`).addClass('jh-card-active');
            $('.jh-radio-input').prop('checked', false);
            $(`.jh-card[data-kpi="${window.currentKpiFilter}"] .jh-radio-input`).prop('checked', true);
        }

        updateKpiViewToggle(window.currentKpiFilter);
        fetchCandidates();
        initSuperAdminReverbSync();

        // Pagination handler supporting both .page-link and .pagi-page
        $(document).on('click', '#pagination .page-link:not(.disabled):not(.active), #pagination .pagi-page:not(.pagi-active-disabled), #pagination .pagi-arrow:not(.pagi-disabled)', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (!page) return;
            fetchCandidates(page);
        });
    });
</script>
@endpush